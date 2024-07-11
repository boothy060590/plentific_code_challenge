<?php

namespace Tests\DataObjects;

use App\Plentific\DataObjects\User\UserDto;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class UserDtoTest extends TestCase
{
    #[Test]
    public function it_can_build_a_valid_user_dto_with_all_properties()
    {
        $properties = [
            'id' => 1,
            'email' => 'goku.shenron@kame-house.org',
            'name' => 'Goku Shenron',
            'avatar' => 'https://reqres.in/img/faces/2-image.jpg',
            'job' => 'Super Saiyan'
        ];

        $userDto = new UserDto($properties);

        $this->assertInstanceOf(UserDto::class, $userDto);
        $this->assertEquals(1, $userDto->id);
        $this->assertEquals('goku.shenron@kame-house.org', $userDto->email);
        $this->assertEquals('Goku Shenron', $userDto->name);
        $this->assertEquals('https://reqres.in/img/faces/2-image.jpg', $userDto->avatar);
        $this->assertEquals('Super Saiyan', $userDto->job);
    }

    #[Test]
    public function it_can_correctly_serialize_dto()
    {
        $properties = [
            'id' => 1,
            'email' => 'goku.shenron@kame-house.org',
            'name' => 'Goku Shenron',
            'avatar' => 'https://reqres.in/img/faces/2-image.jpg',
            'job' => 'Super Saiyan'
        ];

        $userDto = new UserDto($properties);

        $this->assertEquals($properties, $userDto->toArray());
        $this->assertJsonStringEqualsJsonString(json_encode($properties), json_encode($userDto));
    }

    #[Test]
    public function it_can_build_a_user_dto_missing_non_required_properties()
    {
        $properties = [
            'id' => 1,
            'name' => 'Goku Shenron',
        ];

        $userDto = new UserDto($properties);

        $this->assertInstanceOf(UserDto::class, $userDto);
        $this->assertEquals(1, $userDto->id);
        $this->assertEquals('Goku Shenron', $userDto->name);
        $this->assertNull($userDto->avatar);
        $this->assertNull($userDto->email);
        $this->assertNull($userDto->job);
    }

    #[Test] #[DataProvider('invalidPropertyTypesProvider')]
    public function it_throws_exception_for_invalid_property_types(array $properties)
    {
        $this->expectException(InvalidOptionsException::class);
        new UserDto($properties);
    }

    #[Test] #[DataProvider('invalidPropertyValuesProvider')]
    public function it_throws_exception_for_invalid_property_values(array $properties)
    {
        $this->expectException(InvalidOptionsException::class);
        new UserDto($properties);
    }

    public static function invalidPropertyTypesProvider(): array
    {
        return [
            [[
                'id' => 'invalidString',
                'email' => 'goku.shenron@kame-house.org',
                'name' => 'Goku Shenron',
                'avatar' => 'https://image.com/goku.jpg',
                'job' => 'Super Saiyan'
            ]],
            [[
                'id' => 1,
                'email' => 5,
                'name' => 'Goku Shenron',
                'avatar' => 'https://image.com/goku.jpg',
                'job' => 'Super Saiyan'
            ]],
            [[
                'id' => 1,
                'email' => 'goku.shenron@kame-house.org',
                'name' => [],
                'avatar' => 'https://image.com/goku.jpg',
                'job' => 'Super Saiyan'
            ]],
            [[
                'id' => 1,
                'email' => 'goku.shenron@kame-house.org',
                'name' => 'Goku Shenron',
                'avatar' => new \DateTime(),
                'job' => 'Super Saiyan'
            ]],
            [[
                'id' => 1,
                'email' => 'goku.shenron@kame-house.org',
                'name' => 'Goku Shenron',
                'avatar' => 'https://image.com/goku.jpg',
                'job' => 123
            ]]
        ];
    }

    public static function invalidPropertyValuesProvider(): array
    {
        return [
            [[
                'id' => 0,
                'email' => 'goku.shenron@kame-house.org',
                'name' => 'Goku Shenron',
                'avatar' => null,
                'job' => 'Super Saiyan'
            ]],
            [[
                'id' => 1,
                'email' => 'nonemailstring',
                'name' => 'Goku Shenron',
                'avatar' => null,
                'job' => 'Super Saiyan'
            ]],
            [[
                'id' => 1,
                'email' => 'goku.shenron@kame-house.org',
                'name' => hash('sha256', uniqid()),
                'avatar' => null,
                'job' => 'Super Saiyan'
            ]],
            [[
                'id' => 1,
                'email' => 'goku.shenron@kame-house.org',
                'name' => 'A',
                'avatar' => null,
                'job' => 'Super Saiyan'
            ]],
            [[
                'id' => 1,
                'email' => 'goku.shenron@kame-house.org',
                'name' => 'Goku Shenron',
                'avatar' => null,
                'job' => hash('sha256', uniqid())
            ]],
            [[
                'id' => 1,
                'email' => 'goku.shenron@kame-house.org',
                'name' => 'Goku Shenron',
                'avatar' => null,
                'job' => 'A'
            ]]
        ];
    }
}
