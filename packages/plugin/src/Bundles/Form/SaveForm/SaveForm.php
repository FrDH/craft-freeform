<?php

namespace Solspace\Freeform\Bundles\Form\SaveForm;

use Carbon\Carbon;
use craft\db\Query;
use craft\helpers\UrlHelper;
use Solspace\Commons\Helpers\CryptoHelper;
use Solspace\Freeform\Bundles\Form\Context\Session\Bag\SessionBag;
use Solspace\Freeform\Bundles\Form\SaveForm\Actions\SaveFormAction;
use Solspace\Freeform\Bundles\Form\SaveForm\Events\SaveFormEvent;
use Solspace\Freeform\Events\Forms\HandleRequestEvent;
use Solspace\Freeform\Fields\Pro\SaveField;
use Solspace\Freeform\Freeform;
use Solspace\Freeform\Library\Bundles\FeatureBundle;
use Solspace\Freeform\Library\Composer\Components\Form;
use Solspace\Freeform\Models\Settings;
use Solspace\Freeform\Records\SavedFormRecord;
use yii\base\Event;

class SaveForm extends FeatureBundle
{
    const SAVE_ACTION = 'save';

    const BAG_KEY = 'savedSession';
    const BAG_REDIRECT = 'savedFormRedirect';

    const PROPERTY_KEY = 'key';
    const PROPERTY_TOKEN = 'token';
    const PROPERTY_URL = 'url';

    const EVENT_SAVE_FORM = 'save-form';

    const CLEANUP_CACHE_KEY = 'save-and-continue-cleanup';
    const CLEANUP_CACHE_TTL = 60 * 60; // 1 hour

    public function __construct()
    {
        Event::on(Form::class, Form::EVENT_AFTER_HANDLE_REQUEST, [$this, 'handleSave']);

        $this->cleanup();
    }

    public static function getPriority(): int
    {
        return 900;
    }

    public function handleSave(HandleRequestEvent $event)
    {
        $isSavingForm = self::SAVE_ACTION === $event->getRequest()->post(Form::ACTION_KEY);
        if (!$isSavingForm) {
            return;
        }

        $form = $event->getForm();
        if (\count($form->getErrors()) || $form->isMarkedAsSpam()) {
            return;
        }

        $token = CryptoHelper::getUniqueToken();
        $key = CryptoHelper::getUniqueToken(25);

        $form
            ->getPropertyBag()
            ->remove(self::BAG_KEY)
            ->remove(LoadSavedForm::BAG_KEY_LOADED)
        ;

        Event::trigger(self::class, self::EVENT_SAVE_FORM, new SaveFormEvent($form));

        $bag = new SessionBag($form->getId(), $form->getPropertyBag()->toArray(), $form->getAttributeBag()->toArray());
        $serialized = json_encode($bag);

        $encryptionKey = $this->getEncryptionKey($key);

        $payload = base64_encode(\Craft::$app->security->encryptByKey($serialized, $encryptionKey));

        $sessionId = \Craft::$app->getSession()->getId();

        $record = new SavedFormRecord();
        $record->sessionId = $sessionId;
        $record->formId = $form->getId();
        $record->token = $token;
        $record->payload = $payload;
        $record->save();

        $this->cleanupForSession($sessionId);

        $returnUrl = $form->getPropertyBag()->get(self::BAG_REDIRECT, '');
        if (empty($returnUrl)) {
            /** @var SaveField[] $saveButtons */
            $saveButtons = $form->getCurrentPage()->getFields(SaveField::class);
            foreach ($saveButtons as $button) {
                $returnUrl = $button->getUrl();
                if (!empty($returnUrl)) {
                    break;
                }
            }

            if (empty($returnUrl)) {
                $returnUrl = UrlHelper::url('', ['session-token' => '{token}', 'key' => '{key}']);
            }
        }

        $variables = [
            'form' => $form,
            'token' => $token,
            'key' => $key,
        ];

        $returnUrl = \Craft::$app->view->renderObjectTemplate($returnUrl, $variables, $variables);

        if ($event->getRequest()->getIsAjax()) {
            $form->addAction(
                new SaveFormAction([
                    self::PROPERTY_TOKEN => $token,
                    self::PROPERTY_KEY => $key,
                    self::PROPERTY_URL => $returnUrl,
                ])
            );
        } else {
            \Craft::$app->response->redirect($returnUrl)->send();
        }
    }

    public static function getEncryptionKey(string $key): string
    {
        return $key.\Craft::$app->getConfig()->getGeneral()->securityKey;
    }

    private function cleanupForSession($sessionId)
    {
        if (!$sessionId) {
            return;
        }

        $limit = (int) Freeform::getInstance()->settings->getSettingsModel()->saveFormSessionLimit;
        if ($limit <= 0) {
            return;
        }

        $ids = (new Query())
            ->select(['id'])
            ->from(SavedFormRecord::TABLE)
            ->where(['sessionId' => $sessionId])
            ->orderBy(['dateCreated' => \SORT_DESC])
            ->column()
        ;

        if ($ids <= $limit) {
            return;
        }

        $deletableIds = \array_slice($ids, $limit);
        if ($deletableIds) {
            \Craft::$app->db->createCommand()
                ->delete(SavedFormRecord::TABLE, ['id' => $deletableIds])
                ->execute()
            ;
        }
    }

    private function cleanup()
    {
        if (Freeform::isLocked(self::CLEANUP_CACHE_KEY, self::CLEANUP_CACHE_TTL)) {
            return;
        }

        $ttl = (int) Freeform::getInstance()->settings->getSettingsModel()->saveFormTtl;
        if ($ttl <= 0) {
            $ttl = Settings::SAVE_FORM_TTL;
        }

        $expirationTime = new Carbon("now -{$ttl} day");

        \Craft::$app->db->createCommand()
            ->delete(SavedFormRecord::TABLE, ['<', 'dateCreated', $expirationTime])
            ->execute()
        ;
    }
}