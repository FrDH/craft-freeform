<?php

namespace Solspace\Freeform\Library\DataObjects\FieldType;

use Solspace\Freeform\Attributes\Property\PropertyValidatorInterface;
use Solspace\Freeform\Attributes\Property\TransformerInterface;

class Property implements \JsonSerializable
{
    public string $type;
    public string $handle;
    public ?string $label;
    public ?string $instructions;
    public int $order;
    public mixed $value;
    public ?bool $required;
    public ?string $placeholder;
    public ?string $section;
    public ?array $options;
    public ?array $flags;
    public ?array $visibilityFilters;
    public ?array $middleware;
    public ?TransformerInterface $transformer;

    private array $validators = [];

    public function setValidators(array $validators): self
    {
        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }

        return $this;
    }

    public function addValidator(PropertyValidatorInterface $validator): self
    {
        $this->validators[] = $validator;

        return $this;
    }

    /**
     * @return PropertyValidatorInterface[]
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    public function hasFlag(string $name): bool
    {
        if (null === $this->flags) {
            return false;
        }

        return \in_array($name, $this->flags, true);
    }

    public function jsonSerialize(): array
    {
        $reflection = new \ReflectionClass($this);

        $array = [];
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $array[$property->getName()] = $property->getValue($this);
        }

        return $array;
    }
}