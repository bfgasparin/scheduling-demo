<?php

namespace App\Auth;

use App\User;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

/**
 * User Provider for system users
 * @see App\User
 *
 * The provider search users filtering by its credentials with or condition
 */
class UserProvider extends EloquentUserProvider
{
    /**
     * The scopes to remove from the User queries
     *
     * @var array
     */
    protected $scopesToRemove;

    /**
     * Create a new user provider.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param array $scopesToRemove   array of scopes to be removed from queries of the user
     * @return void
     */
    public function __construct(HasherContract $hasher, array $scopesToRemove = [])
    {
        parent::__construct($hasher, User::class);

        $this->scopesToRemove = $scopesToRemove;
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

        // First we will add each credential element to the query as a orWhere clause
        // and removes any passed scopes. Then we can execute the query and, if we found
        // a user, return it in a Eloquent User "model" that will be utilized by the Guard
        // instances.
        $query = $this->createModel()->newQuery()->withoutGlobalScopes($this->scopesToRemove);

        $query->where(function ($query) use ($credentials) {
            foreach ($credentials as $key => $value) {
                if (! Str::contains($key, 'password')) {
                    $query->orWhere($key, $value);
                }
            }
        });

        return $query->first();
    }

    /**
     * Remove passed scopes from User queries.
     *
     * @param  array  $scopes
     * @return $this
     */
    public function withoutScopes(array $scopes) : self
    {
        $this->scopesToRemove = array_merge($this->scopesToRemove, $scopes);

        return $this;
    }
}
