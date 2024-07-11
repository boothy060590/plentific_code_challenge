<?php

namespace Tests\Api;

use App\Plentific\Api\User\UserApi;
use App\Plentific\DataObjects\User\UserCollectionDto;
use App\Plentific\DataObjects\User\UserDto;
use App\Plentific\Exceptions\ApiException;
use App\Plentific\Exceptions\InvalidApiResponseException;
use App\Plentific\Exceptions\UserNotFoundException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class UserApiTest extends TestCase
{
    private Client $mockClient;
    private UserApi $userApi;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up mocking the Api Responses so we don't rely on the API being live
        $this->mockClient = Mockery::mock(Client::class);
        $this->userApi = new UserApi();

        $reflectionClass = new ReflectionClass(UserApi::class);
        $clientPropertyToMock = $reflectionClass->getProperty('client');
        $clientPropertyToMock->setAccessible(true);
        $clientPropertyToMock->setValue($this->userApi, $this->mockClient);
    }

    #[Test]
    public function test_it_can_create_user_from_valid_dto()
    {
        $name = 'Goku Shenron';
        $job = 'Super Saiyan';

        $response = ['createdAt' => '2024-07-11', 'id' => '222', 'name' => $name, 'job' => $job];
        $expected = ['id' => 222, 'name' => $name, 'job' => $job, 'email' => null, 'avatar' => null];

        $this->setMockResponse('post', ['users', ['json' => ['name' => $name, 'job' => $job]]], 201, $response);
        $result = $this->userApi->createUser($name, $job);

        $this->assertInstanceOf(UserDto::class, $result);
        $this->assertEquals($expected, $result->toArray());
        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($result));
    }

    #[Test]
    public function test_it_can_get_a_user_by_valid_id()
    {
        $response = [
            'data' => [
                'id' => 1,
                'email' => 'goku.shenron@kame-house.org',
                'first_name' => 'Goku',
                'last_name' => 'Shenron',
                'avatar' => null
            ]
        ];

        $expectedProperties = [
            'id' => 1,
            'email' => 'goku.shenron@kame-house.org',
            'name' => 'Goku Shenron',
            'avatar' => null,
            'job' => null
        ];

        $this->setMockResponse('get', ['users/1'], 200, $response);

        $result = $this->userApi->getUserById(1);
        $this->assertInstanceOf(UserDto::class, $result);
        $this->assertEquals($expectedProperties, $result->toArray());
        $this->assertJsonStringEqualsJsonString(json_encode($expectedProperties), json_encode($result));
    }

    public function test_it_can_get_list_of_users_by_page()
    {
        $response = [
            'page' => 2,
            'per_page' => 6,
            'total' => 8,
            'total_pages' => 2,
            'data' => [
                [
                    'id' => 7,
                    'email' => 'goku.shenron@kame-house.org',
                    'first_name' => 'Goku',
                    'last_name' => 'Shenron',
                    'avatar' => null,
                ],
                [
                    'id' => 8,
                    'email' => 'vegeta.saiyan@capsule-corp.com',
                    'first_name' => 'Vegeta',
                    'last_name' => 'Saiyan',
                    'avatar' => null,
                ],
            ]
        ];

        $expectedUserProperties = [
            [
                'id' => 7,
                'email' => 'goku.shenron@kame-house.org',
                'name' => 'Goku Shenron',
                'avatar' => null,
                'job' => null,
            ],
            [
                'id' => 8,
                'email' => 'vegeta.saiyan@capsule-corp.com',
                'name' => 'Vegeta Saiyan',
                'avatar' => null,
                'job' => null,
            ],
        ];

        $this->setMockResponse('get', ['users', ['query' => ['page' => 2]]], 200, $response);

        $result = $this->userApi->getUsers(2);
        $this->assertInstanceOf(UserCollectionDto::class, $result);
        $this->assertEquals($expectedUserProperties, $result->toArray());
        $this->assertJsonStringEqualsJsonString(json_encode($expectedUserProperties), json_encode($result));
    }

    #[Test]
    public function it_throws_user_not_found_exception_when_api_responds_with_404()
    {
        $this->setMockClientErrorResponse('get', ['users/22'], 404, 'Not Found');

        $this->expectException(UserNotFoundException::class);
        $this->userApi->getUserById(22);
    }

    #[Test] #[DataProvider('errorConditions')]
    public function it_throws_generic_api_exception_when_api_responds_with_error_code_other_than_404(string $endpoint, ?array $endpointArgs, string $method, array $args, int $code, string $message)
    {
        $this->setMockClientErrorResponse($method, $args, $code, $message);
        $this->expectException(ApiException::class);

        $endpointArgs ? $this->userApi->$endpoint(...$endpointArgs) : $this->userApi->$endpoint();
    }

    #[Test] #[DataProvider('invalidApiResponseData')]
    public function it_throws_invalid_api_response_exception_when_response_data_doesnt_allow_us_to_create_valid_dto(string $endpoint, ?array $endpointArgs, string $method, array $args, int $code, array $body)
    {
        $this->setMockResponse($method, $args, $code, $body);
        $this->expectException(InvalidApiResponseException::class);

        $endpointArgs ? $this->userApi->$endpoint(...$endpointArgs) : $this->userApi->$endpoint();
    }

    public static function errorConditions(): array
    {
        return [
            ['getUserById', [22], 'get', ['users/22'], 503, 'Service Unavailable'],
            ['getUsers', null, 'get', ['users', ['query' => ['page' => 'string']]], 400, 'Bad Request'],
            ['createUser', ['admin', 'admin user'], 'post', ['users', ['job' => 'admin', 'name' => 'admin user']], 403, 'Forbidden'],
        ];
    }

    public static function invalidApiResponseData(): array
    {
        return [
            ['getUserById', [1], 'get', ['users/1'], 200, ['data' => ['email' => 'missing-required-props@test.com', 'first_name' => 'Goku', 'last_name' => 'Shenron']]],
            ['getUsers', [2], 'get', ['users', ['query' => ['page' => 2]]], 200, ['data' => [['first_name' => 'Goku', 'last_name' => 'Shenron'], ['first_name' => 'Vegeta', 'last_name' => 'Saiyan']]]],
            ['createUser', ['Goku Shenron', 'admin'], 'post', ['users', ['json' => ['name' => 'Goku Shenron', 'job' => 'admin']]], 201, ['createdAt' => '2024-07-11', 'id' => 0]],
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    private function setMockResponse(string $method, array $args, int $responseCode, array $responseBody): void
    {
        $this->mockClient
            ->shouldReceive($method)
            ->with(...$args)
            ->andReturn(new Response($responseCode, [], json_encode($responseBody)));
    }

    private function setMockClientErrorResponse(string $method, array $args, int $responseCode, string $message): void
    {
        $this->mockClient
            ->shouldReceive($method)
            ->with(...$args)
            ->andThrow(new RequestException($message, Mockery::mock(Request::class), new Response($responseCode, [], $message)));
    }
}
