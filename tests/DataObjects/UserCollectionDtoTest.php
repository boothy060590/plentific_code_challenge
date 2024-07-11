<?php

namespace Tests\DataObjects;

use App\Plentific\DataObjects\User\UserCollectionDto;
use App\Plentific\DataObjects\User\UserDto;
use App\Plentific\Exceptions\InvalidDtoCollectionException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserCollectionDtoTest extends TestCase
{
    #[Test]
    public function it_can_build_a_dto_collection_of_valid_user_dtos()
    {
        $users = [
            new UserDto(['id' => 1, 'name' => 'Goku Shenron', 'job' => 'Super Saiyan']),
            new UserDto(['id' => 2, 'name' => 'Vegeta Saiyan', 'job' => 'Saiyan Prince', 'email' => 'vegeta@capsule-corp.com']),
        ];

        $userCollection = new UserCollectionDto($users);

        array_walk($userCollection->users, fn ($user) => $this->assertInstanceOf(UserDto::class, $user));
    }

    #[Test]
    public function it_can_serialize_user_dtos_to_array()
    {
        $users = [
            new UserDto(['id' => 1, 'name' => 'Goku Shenron']),
            new UserDto(['id' => 2, 'name' => 'Vegeta Saiyan']),
        ];

        $userCollection = new UserCollectionDto($users);

        $expected = [
            ['id' => 1, 'name' => 'Goku Shenron', 'email' => null, 'avatar' => null, 'job' => null],
            ['id' => 2, 'name' => 'Vegeta Saiyan', 'email' => null, 'avatar' => null, 'job' => null]
        ];

        $this->assertEquals($expected, $userCollection->toArray());
        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($userCollection));
    }


    /**
     * @throws InvalidDtoCollectionException
     */
    #[Test]
    public function it_throws_invalid_dto_collection_exception_for_invalid_dto_arrays()
    {
        $users = [
            new UserDto(['id' => 1, 'name' => 'Goku Shenron']), // valid
            [1, 2, 3, 4, 'string', null] // invalid
        ];

        $this->expectExceptionObject(new InvalidDtoCollectionException('Property `users` must be an array of UserDto instances.'));

        new UserCollectionDto($users);
    }
}
