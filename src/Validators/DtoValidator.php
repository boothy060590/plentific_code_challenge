<?php

namespace App\Plentific\Validators;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface DtoValidator
{
    public function setDefaults(OptionsResolver $resolver): self;

    public function setRules(OptionsResolver $resolver): self;

    public function validated(OptionsResolver $resolver, array $properties): array;
}
