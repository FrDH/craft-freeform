<?php

namespace Solspace\Freeform\Bundles\Form\Tracking;

use Solspace\Freeform\Events\Forms\SubmitEvent;
use Solspace\Freeform\Library\Bundles\FeatureBundle;
use Solspace\Freeform\Library\Composer\Components\Form;
use yii\base\Event;

class Cookies extends FeatureBundle
{
    public function __construct()
    {
        Event::on(Form::class, Form::EVENT_AFTER_SUBMIT, [$this, 'setPostedCookie']);
    }

    public static function getCookieName(Form $form): string
    {
        return 'form_posted_'.$form->getId();
    }

    public function setPostedCookie(SubmitEvent $event)
    {
        if (\Craft::$app->request->isConsoleRequest) {
            return;
        }

        $form = $event->getForm();
        $name = self::getCookieName($form);
        $value = time();

        if (version_compare(\PHP_VERSION, '7.3', '<')) {
            setcookie(
                $name,
                $value,
                (int) strtotime('+1 year')
            );
        } else {
            setcookie(
                $name,
                $value,
                [
                    'expires' => (int) strtotime('+1 year'),
                    'path' => '/',
                    'domain' => \Craft::$app->getConfig()->getGeneral()->defaultCookieDomain,
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => \Craft::$app->getConfig()->getGeneral()->sameSiteCookieValue ?? 'Lax',
                ]
            );
        }

        $_COOKIE[$name] = $value;
    }
}
