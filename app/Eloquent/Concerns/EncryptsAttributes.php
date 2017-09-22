<?php

namespace App\Eloquent\Concerns;

use Illuminate\Support\Str;

/**
 * Helps Eloquent Models to encrypts its attributes
 *
 * Use the attribute `encrypts` to name the fields that should be encrypted
 *
 * @Issues: It makes creation of eloquent models using this trait very slow on ModelFactory.
 *          Thats because encrypting the attributesis for every model creation is very cost
 *
 * @see https://laravel.com/docs/eloquent-mutators
 */
trait EncryptsAttributes
{
    /**
     * Set a given attribute on the model encrypted
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        parent::setAttribute($key, $value);

        /*
        | If this attribute does contains a JSON ->, we encrypt the result
        | attribute's value in the attributes property. Otherwise, the
        | fillJsonAttribute handles the encryption
        */
        if (in_array($key, $this->encrypts) && !Str::contains($key, '->')) {
            $this->attributes[$key] = bcrypt($value);
        }

        return $this;
    }

    /**
     * Set a given JSON attribute on the model encrypted
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function fillJsonAttribute($key, $value)
    {
        [$key, $path] = explode('->', $key, 2);

        $this->attributes[$key] = $this->asJson($this->getArrayAttributeWithValue(
            $path,
            $key,
            with(in_array($key, $this->encrypts) ? bcrypt($value) : $value)
        ));

        return $this;
    }
}
