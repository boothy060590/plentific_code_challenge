<?php

namespace App\Plentific\Validators;

use App\Plentific\DataObjects\BaseDtoCollection;

interface DtoCollectionValidator
{
    public function validateDTOs(BaseDtoCollection $dtoCollection): void;
}
