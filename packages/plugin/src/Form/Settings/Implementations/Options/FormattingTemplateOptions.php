<?php

namespace Solspace\Freeform\Form\Settings\Implementations\Options;

use Solspace\Freeform\Attributes\Property\Property;
use Solspace\Freeform\Attributes\Property\PropertyTypes\OptionCollection;
use Solspace\Freeform\Attributes\Property\PropertyTypes\OptionFetcherInterface;

class FormattingTemplateOptions implements OptionFetcherInterface
{
    public function fetchOptions(Property $property): OptionCollection
    {
        return new OptionCollection();
    }
}