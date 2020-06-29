<?php

namespace MailOptin\MailerliteConnect\APIClass;

use MailOptin\MailerliteConnect\APIClass\Common\ApiConstants;
use MailOptin\MailerliteConnect\APIClass\Common\RestClient;
use MailOptin\MailerliteConnect\APIClass\Exceptions\MailerLiteSdkException;

class MailerLite {

    /**
     * @var null | string
     */
    protected $apiKey;

    /**
     * @var RestClient
     */
    protected $restClient;

    /**
     * @param string|null $apiKey
     * @param mixed $client
     */
    public function __construct(
        $apiKey = null,
        $httpClient = null
    ) {
        if (is_null($apiKey)) {
            throw new MailerLiteSdkException("API key is not provided");
        }

        $this->apiKey = $apiKey;

        $this->restClient = new RestClient(
            $this->getBaseUrl(),
            $apiKey,
            $httpClient
        );
    }

    /**
     * @return \MailOptin\MailerliteConnect\APIClass\Api\Groups
     */
    public function groups()
    {
        return new \MailOptin\MailerliteConnect\APIClass\Api\Groups($this->restClient);
    }

    /**
     * @return \MailOptin\MailerliteConnect\APIClass\Api\Fields
     */
    public function fields()
    {
        return new \MailOptin\MailerliteConnect\APIClass\Api\Fields($this->restClient);
    }

    /**
     * @return \MailOptin\MailerliteConnect\APIClass\Api\Subscribers
     */
    public function subscribers()
    {
        return new \MailOptin\MailerliteConnect\APIClass\Api\Subscribers($this->restClient);
    }

    /**
     * @return \MailOptin\MailerliteConnect\APIClass\Api\Campaigns
     */
    public function campaigns()
    {
        return new \MailOptin\MailerliteConnect\APIClass\Api\Campaigns($this->restClient);
    }

    /**
     * @return \MailOptin\MailerliteConnect\APIClass\Api\Stats
     */
    public function stats()
    {
        return new \MailOptin\MailerliteConnect\APIClass\Api\Stats($this->restClient);
    }

    /**
     * @return \MailOptin\MailerliteConnect\APIClass\Api\Settings
     */
    public function settings()
    {
        return new \MailOptin\MailerliteConnect\APIClass\Api\Settings($this->restClient);
    }

    /**
     * @param  string $version
     * @return string
     */
    public function getBaseUrl($version = ApiConstants::VERSION)
    {
        return ApiConstants::BASE_URL . $version . '/';
    }

}