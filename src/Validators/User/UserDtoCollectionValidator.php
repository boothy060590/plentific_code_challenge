<?php

namespace App\Plentific\Validators\User;

use App\Plentific\DataObjects\BaseDtoCollection;
use App\Plentific\DataObjects\User\UserDto;
use App\Plentific\Exceptions\InvalidDtoCollectionException;
use App\Plentific\Validators\DtoCollectionValidator;

class UserDtoCollectionValidator implements DtoCollectionValidator
{

    public function validateDTOs(BaseDtoCollection $dtoCollection): void
    {
        $invalidDTOs = array_filter($dtoCollection->getDTOs(), fn ($userDto) => !($userDto instanceof UserDto));

        if (count($invalidDTOs)) {
            throw new InvalidDtoCollectionException('Property `users` must be an array of UserDto instances.');
        }
    }
}
