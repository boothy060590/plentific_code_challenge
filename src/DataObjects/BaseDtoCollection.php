<?php

namespace App\Plentific\DataObjects;

use App\Plentific\Validators\DtoCollectionValidator;
use JsonSerializable;

abstract class BaseDtoCollection implements JsonSerializable
{
    protected DtoCollectionValidator $validator;

    public function toArray(): array
    {
        return array_map(fn (BaseDto $dto) => $dto->toArray(), $this->getDTOs());
    }

    public abstract function getDTOs(): array;
}
