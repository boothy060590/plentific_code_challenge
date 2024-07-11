<?php

namespace App\Plentific\DataObjects\User;

use App\Plentific\DataObjects\BaseDtoCollection;
use App\Plentific\Exceptions\InvalidDtoCollectionException;
use App\Plentific\Validators\User\UserDtoCollectionValidator;

class UserCollectionDto extends BaseDtoCollection
{
    /** @var UserDto[] */
    public array $users;

    /**
     * @throws InvalidDtoCollectionException
     */
    public function __construct(array $users)
    {
        $this->validator = new UserDtoCollectionValidator();
        $this->users = $users;
        $this->validator->validateDTOs($this);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getDTOs(): array
    {
        return $this->users;
    }
}
