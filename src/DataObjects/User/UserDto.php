<?php
namespace App\Plentific\DataObjects\User;

use App\Plentific\DataObjects\BaseDto;
use App\Plentific\Validators\User\UserDtoValidator;

class UserDto extends BaseDto
{
    public int $id;
    public string $name;
    public ?string $avatar;
    public ?string $job;
    public ?string $email;

    public function __construct(array $properties)
    {
        parent::__construct($properties);
        $this->validator = (new UserDtoValidator())->setDefaults($this->resolver)->setRules($this->resolver);
        $validatedProperties = $this->validator->validated($this->resolver, $properties);

        // Assign the validated properties
        array_walk($validatedProperties, fn ($value, $property) => $this->$property = $value);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
