<?php

namespace App\Auth;

use App\Salon\Employee;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

/**
 * User Provider for Salon Employee
 * @see App\Salon\Employee
 *
 * The provider search employees according to its scope
 *  - admin
 *  - professional
 * @see self::__construct
 *
 *  If use the admin scope for EmployeeProvider to filter Employees using admin scope
 *  If use the professional scope for EmployeeProvider to filter Employees using professional scope
 *  @see App\Eloquent\Concerns\SalonWorkerScopes
 *
 * @see EloquentUserProvider
 */
class EmployeeProvider extends EloquentUserProvider
{
    /**
     * The scope to apply to Employee queries
     *
     * @var string
     */
    protected $scope;

    /**
     * The scopes that can be set through this class.
     *
     * @var array
     */
    protected $allowedScopes = [
        'admin', 'professional',
    ];

    /**
     * Create a new employee provider.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param  string  $scope The scope to be used on employee queries
     * @return void
     */
    public function __construct(HasherContract $hasher, string $scope)
    {
        parent::__construct($hasher, Employee::class);

        if (! in_array($scope, $this->allowedScopes)) {
            throw new InvalidArgumentException("Employee scope [$scope] is not allowed.");
        }

        $this->scope = $scope;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->createModel()->newQuery()->{$this->scope}()->find($identifier);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent Employee "model" that will be utilized by the Guard instances.
        $query = $this->createModel()->newQuery()->{$this->scope}();

        foreach ($credentials as $key => $value) {
            if (! Str::contains($key, 'password')) {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }
}
