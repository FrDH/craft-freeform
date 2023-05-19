<?php

namespace Solspace\Freeform\Attributes\Property\Input;

use Solspace\Freeform\Attributes\Property\Property;

/**
 * @extends Property<int>
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Integer extends Property
{
    public function __construct(
        ?string $label = null,
        ?string $instructions = null,
        ?int $order = null,
        mixed $value = null,
        ?string $placeholder = null,
        public ?int $min = null,
        public ?int $max = null,
    ) {
        parent::__construct($label, $instructions, $order, $value, $placeholder);
    }
}