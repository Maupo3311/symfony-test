<?php

namespace AppBundle\Services;

use OK\Ipstack\Client;
use OK\Ipstack\Exceptions\InvalidApiException;
use Symfony\Component\HttpFoundation\Session\Session;

class IpstackService
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Client
     */
    protected $client;

    /**
     * IpstackService constructor.
     * @param Session $session
     * @param string  $apiKey
     * @throws InvalidApiException
     */
    public function __construct(Session $session, string $apiKey)
    {
        $this->session = $session;
        $this->client = new Client($apiKey);
    }

    /**
     * @param string $ip
     * @return mixed
     * @throws InvalidApiException
     */
    public function getLocation(string $ip)
    {
        return $this->client->get($ip);
    }

    /**
     * @param string $ip
     * @return $this
     * @throws InvalidApiException
     */
    public function addInSession(string $ip)
    {
        $this->session->set('userLocation', $this->getLocation($ip));

        return $this;
    }
}