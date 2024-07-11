<?php

namespace App\Plentific\DataObjects;

use App\Plentific\Validators\DtoValidator;
use JsonSerializable;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class BaseDto implements JsonSerializable
{
    protected OptionsResolver $resolver;
    protected DtoValidator $validator;

    public function __construct(array $properties)
    {
        $this->resolver = new OptionsResolver();
    }

    public function toArray(): array
    {
        $properties = get_object_vars($this);
        unset($properties['resolver']);
        unset($properties['validator']);

        return $properties;
    }
}
