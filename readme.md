# User Service Package

A framework-agnostic PHP package to interact with the Reqres API for retrieving and creating users. This package handles API interactions and returns data as well-defined DTO models.

## Introduction

This package provides services to:
- Retrieve a single user by ID.
- Retrieve a paginated list of users.
- Create a new user with a name and job, returning a User ID.

All users returned by the service are converted into well-defined DTO models implementing `JsonSerializable` and supporting conversion to a standard array structure.

## Installation

### Install the package via Composer:

composer require lee/plentific_challenge  (FYI this obviously won't work, but just demonstrating what I'd put in the read me file).

## Usage

### Retrieving a Single User by ID
`(new UserApi())->getUserById(1); // where 1 is the id of the user.`
If the request succeeds this will return a single `UserDto` object.


### Retrieving a Paginated List of Users
`(new UserApi())->getUsers(2); // where 2 is page number.`
If the request succeeds this will return a UserCollectionDto, which has property `$users` (an array of `UserDto` objects).

### Creating a new User
`(new UserApi())->createUser('FirstName LastName', 'Administrator'); // where first argument is the users name, and second their job.`
If the request succeeds this will return a single UserDto object.

## Exceptions

### UserNotFoundException
Thrown when a user is not found by the API for the given id.

### InvalidDtoCollectionException
Thrown when the UserCollectionDto `$users` property contains an entry that is not an instance of `UserDto`.

### InvalidApiResponseException
Thrown when the API responds with data that doesn't allow us to build valid `UserDto(s)` for some reason.

### ApiException
Thrown for all other error responses from the API. A catch-all generic exception class.
