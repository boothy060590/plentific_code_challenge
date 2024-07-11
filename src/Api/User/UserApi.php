<?php

namespace App\Plentific\Api\User;

use App\Plentific\Api\BaseApi;
use App\Plentific\DataObjects\User\UserCollectionDto;
use App\Plentific\DataObjects\User\UserDto;
use App\Plentific\Exceptions\ApiException;
use App\Plentific\Exceptions\InvalidApiResponseException;
use App\Plentific\Exceptions\UserNotFoundException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class UserApi extends BaseApi
{
    /**
     * @throws InvalidApiResponseException
     * @throws GuzzleException
     * @throws ApiException
     * @throws UserNotFoundException
     */
    public function getUserById(int $id): UserDto
    {
        try {
            $response = $this->client->get("users/$id");
            $data = json_decode($response->getBody()->getContents(), true)['data'];
            $this->formatUserResponseData($data);

            return new UserDTO($data);
        } catch (RequestException $ex) {
            $this->handleErrorResponse($ex->getCode());
        } catch (InvalidOptionsException) {
            // The response doesn't allow us to build valid DTOs.
            throw new InvalidApiResponseException();
        } catch (\Exception) {
            // Fallback to other un-foreseen errors
            throw new ApiException();
        }
    }

    /**
     * @param int $page
     * @return UserCollectionDto
     * @throws GuzzleException
     * @throws ApiException
     */
    public function getUsers(int $page = 1): UserCollectionDto
    {
        try {
            $response = $this->client->get("users", ['query' => ['page' => $page]]);
            $data = json_decode($response->getBody()->getContents(), true)['data'];

            return new UserCollectionDto(array_map(function ($userData) {
                $this->formatUserResponseData($userData);
                return new UserDto($userData);
            }, $data));
        } catch (RequestException $ex) {
           $this->handleErrorResponse($ex->getCode());
        } catch (InvalidOptionsException) {
            // The response doesn't allow us to build valid DTOs.
            throw new InvalidApiResponseException();
        } catch (\Exception) {
            // Fallback to other un-foreseen errors
            throw new ApiException();
        }
    }

    /**
     * @throws InvalidApiResponseException
     * @throws GuzzleException
     * @throws ApiException
     * @throws UserNotFoundException
     */
    public function createUser(string $name, string $job): UserDto
    {
        try {
            // Get the response, remove the createdAt property, and ensure id is an integer before building DTO
            $response = $this->client->post("users", ['json' => ['name' => $name, 'job' => $job]]);
            $data = json_decode($response->getBody()->getContents(), true);
            unset($data['createdAt']);
            $data['id'] = (int)$data['id'];

            return new UserDTO($data);
        } catch (RequestException $ex) {
            $this->handleErrorResponse($ex->getCode());
        } catch (InvalidOptionsException) {
            // The response doesn't allow us to build valid DTOs.
            throw new InvalidApiResponseException();
        } catch (\Exception) {
            // Fallback to other un-foreseen errors
            throw new ApiException();
        }
    }

    private function formatUserResponseData(array &$data): void
    {
        $data['name'] = "{$data['first_name']} {$data['last_name']}";
        unset($data['first_name']);
        unset($data['last_name']);
    }

    /**
     * @throws UserNotFoundException
     * @throws ApiException
     */
    protected function handleErrorResponse(int $code): void
    {
        match ($code) {
            404 => throw new UserNotFoundException(),
            default => throw new ApiException()
        };
    }
}
