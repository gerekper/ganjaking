<?php

namespace ElementPack\Wincher\Controller;

use ElementPack\Wincher\WincherOAuthClient;

/**
 * The REST Controller.
 */
abstract class RestController
{
    /**
     * @var WincherOAuthClient
     */
    protected $client;

    /**
     * RestController constructor.
     *
     * @param WincherOAuthClient $client The client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Returns whether the user has permission.
     *
     * @return bool true when the user has permission
     */
    public function hasPermission()
    {
        return current_user_can($this->getPermission());
    }

    /**
     * Returns the required permission to access this controller.
     *
     * @return string the required permission
     */
    protected function getPermission()
    {
        return 'manage_options';
    }
}
