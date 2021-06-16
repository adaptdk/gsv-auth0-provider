<?php

namespace Adaptdk\GsvAuth0Provider;

use Illuminate\Support\Facades\Http;

class UserService
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected string $token;

    public function __construct(?string $baseUrl = null)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Set the base URL
     *
     * @param string $baseUrl
     * @return self
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Get the base URL
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Set the access token
     *
     * @param string $token
     * @return self
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the access token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Get the user data
     *
     * @return mixed
     */
    public function fetch()
    {
        if (empty($this->token)) {
            throw new \Exception('Token not set');
        }

        if (empty($this->baseUrl)) {
            throw new \Exception('Base URL not set');
        }

        return Http::withToken($this->token)
            ->withoutVerifying()
            ->get($this->baseUrl . '/api/users/auth0')
            ->json();
    }
}
