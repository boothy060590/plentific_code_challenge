<?php

namespace App\Plentific\Validators\User;

use App\Plentific\Validators\DtoValidator;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Validation;

class UserDtoValidator implements DtoValidator
{
    public function setDefaults(OptionsResolver $resolver): self
    {
        $resolver->setDefaults([
            'id' => null,
            'email' => null,
            'name' => null,
            'avatar' => null,
            'job' => null,
        ]);

        return $this;
    }

    public function setRules(OptionsResolver $resolver): self
    {
        // Set allowed types for each property.
        $resolver->setRequired(['id', 'name']);
        $resolver->setAllowedTypes('id', 'int');
        $resolver->setAllowedTypes('name', 'string');
        $resolver->setAllowedTypes('email', ['string', 'null']);
        $resolver->setAllowedTypes('avatar', ['string', 'null']);
        $resolver->setAllowedTypes('job', ['string', 'null']);

        // Now set validation rules for the property values. E.g. ensure valid e-mail address and not just any old string.
        $resolver->setAllowedValues('id', Validation::createIsValidCallable(new GreaterThan(0)));
        $resolver->setAllowedValues('email', Validation::createIsValidCallable(new Email()));
        $resolver->setAllowedValues('name', Validation::createIsValidCallable(new Length(['min' => 2, 'max' => 50])));
        $resolver->setAllowedValues('job', [Validation::createIsValidCallable(new Length(['min' => 2, 'max' => 50])), 'null']);

        return $this;
    }

    public function validated(OptionsResolver $resolver, array $properties): array
    {
        return $resolver->resolve($properties);
    }
}
