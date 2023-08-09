<?php

namespace Solspace\Freeform\Form\Settings\Implementations\Options;

use Solspace\Freeform\Attributes\Property\Implementations\Options\OptionCollection;
use Solspace\Freeform\Attributes\Property\Implementations\Options\OptionsGeneratorInterface;
use Solspace\Freeform\Attributes\Property\Property;
use Solspace\Freeform\Services\SettingsService;

class FormattingTemplateOptions implements OptionsGeneratorInterface
{
    public function __construct(private SettingsService $settingsService)
    {
    }

    public function fetchOptions(Property $property): OptionCollection
    {
        $options = new OptionCollection();

        $base = $this->settingsService->getSolspaceFormTemplates();
        if ($base) {
            $solspaceTemplates = new OptionCollection();
            foreach ($base as $template) {
                $solspaceTemplates->add($template->getFileName(), $template->getName());
            }

            $options->addCollection('Solspace', $solspaceTemplates);
        }

        $custom = $this->settingsService->getCustomFormTemplates();
        if ($custom) {
            $customTemplates = new OptionCollection();
            foreach ($custom as $template) {
                $customTemplates->add($template->getFileName(), $template->getName());
            }

            $options->addCollection('Custom', $customTemplates);
        }

        return $options;
    }
}