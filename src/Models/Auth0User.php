<?php

namespace Adaptdk\GsvAuth0Provider\Models;

use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Access\Gate;

class Auth0User extends GenericUser
{
    /**
     * Return the Navision account number
     *
     * @return string|null
     */
    public function getAccountNo()
    {
        return $this->company['navision_account_no'] ?? null;
    }

    /**
     * Return the Can book with booking numbers permission
     *
     * @return boolean
     */
    public function canBookWithBookingNumber(): ?bool
    {
        return $this->company['can_book_with_booking_numbers'] ?? false;
    }

    /**
     * Set multiple properties
     *
     * @param array $attributes
     * @return self
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }

    /**
     * Determine if the entity has the given abilities.
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function can($abilities, $arguments = [])
    {
        return app(Gate::class)->forUser($this)->check($abilities, $arguments);
    }

    /**
     * Determine if the entity has any of the given abilities.
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function canAny($abilities, $arguments = [])
    {
        return app(Gate::class)->forUser($this)->any($abilities, $arguments);
    }

    /**
     * Determine if the entity does not have the given abilities.
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function cant($abilities, $arguments = [])
    {
        return ! $this->can($abilities, $arguments);
    }

    /**
     * Determine if the entity does not have the given abilities.
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function cannot($abilities, $arguments = [])
    {
        return $this->cant($abilities, $arguments);
    }

    /**
     * Dynamically access the user's attributes.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }
}
