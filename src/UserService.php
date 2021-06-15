<?php

namespace Adaptdk\GsvAuth0Provider;

use Illuminate\Support\Facades\Http;

class UserService
{
    /**
     * @var string
     */
    protected string $baseUrl;

    /**
     * @var string
     */
    protected string $token;

    public function __construct(?string $baseUrl)
    {
        if ($baseUrl === null) {
            throw new \Exception('User service base URL not set');
        }

        $this->baseUrl = $baseUrl;
    }

    /**
     * Set the access token
     *
     * @param string $token
     * @return self
     */
    public function setToken(string $token) : self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the user data
     *
     * @return mixed
     */
    public function getUser()
    {
        if (empty($this->token)) {
            return false;
        }

        return Http::withToken($this->token)
            ->withoutVerifying()
            ->get($this->baseUrl . '/api/users/auth0')
            ->json();
    }
}
