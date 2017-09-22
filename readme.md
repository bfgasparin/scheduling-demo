# Scheduling App API Demo

An Scheduling demo API to test Laravel 5.4 features and some architecture concepts.

## Instalation

Install dependencie packages:

```sh
# composer install
```

The application is configured using envirotment variables. See [**Configuration**](#configuration)
section for more details. To configure the application, change the values of the `.env` files.


The Application contains protected routes signed using a JWT Token.
If your `.env` file does not contains a `JWT_SECRET` variable, you will need to generate it.
This variable is a secret key for the application to be able to generate secured JWT tokens. Just run:

```sh
$ php artinsa jwt:secret
```

See [**Authentication**](#authentication) section for more details.

## Structure Patterns

@todo (explain each directory)

Every important feature (sometimes referenced as Use Cases) of the system has its own responsable class to handled it.
These classes are divided and organized into `app/Jobs` directory.

Any simple CRUD operation in the system are made directly into the controllers. This helps to keep the code
simpler and avoid to overwhelm the `app\Jobs` diretory.

There's no a `models` directory. Here, a **model** represents anything that is not a Controller or a View in
the MVC pattern. Or, you can see it as anything responsable for the business logic of the application.
In fact, you can considering to be a model everything that it's inside the `app` directory, except the
`Http` and `Console` directories.

### Controllers<a name="controllers"></a>

The structure of the controller is divided according to the logged user that uses the controller.
But, this is not a rule. Sometimes, the same controller can be used for more than once type of user.

- `App/Controllers/API`: Unprotected controllers, used by guests
- `App/Controllers/API/User`:    -- Protected controllers, used by Users
- `App/Controllers/API/Salon/Admin`:    -- Protected controllers, mainly used by Salon Admins
- `App/Controllers/API/Salon/Professional`:   -- Protected controllers, mainly used by Salon Professionais

The name of the controllers follows the route to achieve the resource CRUD that the controller responds. Examples:

- `API\User\BarController` responses for the routes for
    - GET `api/bars`
    - POST `api/bars`
    - GET `api/bars/{bar}`
    - PUT `api/bars/{bar}`
    - DELETE `api/bars/{bar}`

    authenticated with a System User

- `API\Salon\Professional\BarController` responses for the routes for
    - GET `api/bars`
    - POST `api/bars`
    - GET `api/bars/{bar}`
    - PUT `api/bars/{bar}`
    - DELETE `api/bars/{bar}`

    authenticated with a Salon Professional User

- `API\User\FooBarController` responses for the routes for
    - GET `api/foos/{foo}/bars`
    - POST `api/foos/{foo}/bars`
    - GET `api/foos/{foo}/bars/{bar}`
    - PUT `api/foos/{foo}/bars/{bar}`
    - DELETE `api/foos/{foo}/bars/{bar}`

    authenticated with a User

- `API\Foo\Admin\FooBarController` responses for the routes for
    - GET `api/foos/{foo}/bars`
    - POST `api/foos/{foo}/bars`
    - GET `api/foos/{foo}/bars/{bar}`
    - PUT `api/foos/{foo}/bars/{bar}`
    - DELETE `api/foos/{foo}/bars/{bar}`

    authenticated with a Salon Admin User

### Tests

The tests are divided into **Unit** tests and **Feature** tests. You will notice that the
majority of them are not fully isolated tests. The main responsability of each one is
testing a piece of the application, or a piece of a feature, or a especific use case.

- `tests\Unit`: Specific class tests, like: model tests, service tests, job tests, ...
- `tests\Feature`: More like functional tests. These tests hits the application endpoints
and asserts if everything happens as expected

As **Feature tests** hits the application endpoints, the name and structure of the test files
follows name and file structure of the controllers that the routes points:

- `tests/Features/API`: Tests endpoints authenticated with no one
- `tests/Features/API/User`:    -- Tests endpoints authenticated with a User
- `tests/Features/API/Salon/Admin`:    -- Tests endpoints authenticated with a Salon Admins
- `tests/Features/API/Salon/Professional`:   -- Tests endpoints authenticated with a Salon Professionais

See [**Controllers**][#controllers],

For more details of the tests, see the [**Tests**](#tests) section.

## Code Patterns

All codebase follows [PSR-2 Coding Style][2].

All class, function and trait codebase are written with simplicity and readability in mind.

A declarative programming approach is preferreable and used at all source code, including tests.

You should apply declarative statements and functional programming as much as possible. This
helps to keep the code readable and clear.

All classes and functions are well documented. You can go foward the classes and read its
documentations to learn more about each one.

### `with` function

When you need to pass a value to a function parameter, or return it in a function, but
also wants assign this value to a variable to reuse it somewhere else in the codebase, use
the laravel `with` function to get a more clear and readable code.

Example:

```php
$call->function(with($parameter = $this->getParameterValue()));
```

Don't use it if the code is already readable without it.
## Configuration<a name="configuration"></a>

All sensitive data are configured with `.env` file. Any other configurable data of the
system can be found into `config` directory.

## Routing<a name="routing"></a>

All API routes can be found at `routes/api.php` file.

The routes must as can as possible use the `Route::resource` helper
method. This helps keep the routes file more clear and easy to maintain.

## Database

For migrations, run

```sh
$ php artisan migrate
```

To seed the database, run

```sh
$ php artisan db:seed
```

### Type of users

There are three types of users in the application:

* User  (see `App\User` class)
* Salon Admin  (see `App\Salon\Employee` class)
* Salon Professional (see `App\Salon\Employee` class)

## Authentication<a name="authentication"></a>

All protected endpoints are guarded by a JWTGuard, requiring a JWT token.

To generate secured JWT tokens, the application uses a secret key, that can
be configured into the the `.env` file.

There are three endpoints for authenticate users, one for each type of user:

* `/api/users/login` for Users
* `/api/professional/login` for Salon Professional
* `/api/admin/login` for Salon Admin

This endpoints returns a JWT token which must be injected at the `Authorization` header
of the protected endpoints, as a bearer token, as following example:

```
Authorization: Bearer <here-goes-the-token>
```

## Naming Convention

This project has some specific naming conventions, described below.

### Controllers

1. Controllers with protected endpoints to be accessed by **Users** (`App\Users`) must be at
`Http/Controllers/API/User`
2. Controllers with protected endpoints to be accessed by Salon Professionals, must be at
`Http/Controllers/Salon/Professional` directory.
3. Controllers with protected endpoints to be accssed by Salon Admins, must be at
`Http/Controllers/Salon/Admin` directory.

The name of controllers must match as much as possible the urls that routes to them,
despite the fact that in the urls, the resource name is in its plural form, Example:

| Url                      | Controller                     | Protected |
| ------------------------ | :-------------:                | -----:    |
| api/salons               | SalonController@index          | false     |
| api/salons/1/employees   | SalonEmployeeController@index  | false     |
| api/salons/1/services    | SalonServiceController@index   | false     |
| api/employees            | Salon/EmployeeController@index | true      |
| api/salonServices        | Salon/ServiceController        | true      |

Sometimes the controller won't represent a resource. For these cases, the name
of the Controller must be as declarative as possible to describe what it does.

Controllers must be responsible for a unique piece of feature on the system
and its actions of the must follow the five resourcefully methods as can as possible
(index, store, show, update, destroy). See [**Routing**](#routing) section.


### Jobs<a name="jobs"></a>

Jobs (`App/Jobs`) represents the main `features`/`use cases` of the application. Their name must
be as declarative as possible, describing what the jobs does (must describes the feature/use case).

## Tests<a name="testing"></a>

You can run all application tests with:

```sh
$ phpunit
```

There are Feature (`test/Feature` directory) and Unit ('test/Unit' directory) tests.


[1]: https://laravel.com/docs/structure
[2]: http://www.php-fig.org/psr/psr-2
