<?php

namespace Authifly\Provider;

use Authifly\Adapter\OAuth2;
use Authifly\Exception\HttpClientFailureException;
use Authifly\Exception\HttpRequestFailedException;
use Authifly\Exception\InvalidArgumentException;
use Authifly\Data;

/**
 * VerticalResponse OAuth2 provider adapter.
 */
class Jilt extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://api.jilt.com/v2/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://app.jilt.com/oauth/authorize';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://app.jilt.com/oauth/token';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://docs.google.com/document/d/1tfeU0kskV15o6TjcPqD_hdQVRtS6ct_hNA8na3SjAxM/edit#';

    /**
     * {@inheritdoc}
     */
    protected $scope = 'read_shops read_lists write_customers';

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        /** Jilt explicitly require access token to be set as Bearer.  */

        // if access token is found in storage, utilize else
        $storage_access_token = $this->getStoredData('access_token');
        if ( ! empty($storage_access_token)) {
            $this->apiRequestHeaders = [
                'Authorization' => 'Bearer ' . $storage_access_token
            ];
        }

        // use the one supplied in config.
        $config_access_token = $this->config->get('access_token');
        if ( ! empty($config_access_token)) {
            $this->apiRequestHeaders = [
                'Authorization' => 'Bearer ' . $config_access_token
            ];
        }
    }


    /**
     * @return array
     */
    public function getStoreList()
    {
        $shops = $this->apiRequest("shops");

        if ( ! is_array($shops)) return [];

        $filtered = [];

        foreach ($shops as $shop) {
            $filtered[$shop->id] = $shop->name;
        }

        return $filtered;
    }

    /**
     * @param $shop_id
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function getShopLists($shop_id)
    {
        if (empty($shop_id)) {
            throw new InvalidArgumentException('Shop ID is missing');
        }

        $lists = $this->apiRequest("shops/$shop_id/lists");

        if ( ! is_array($lists)) return [];

        $filtered = [];

        foreach ($lists as $list) {
            $filtered[$list->id] = $list->name;
        }

        return $filtered;
    }


    /**
     * Add customers/subscribers to an shop list.
     *
     * @param string $list_id
     * @param array $payload
     *
     * @return object
     * @throws InvalidArgumentException
     */
    public function addSubscriber($shop_id, $email, $payload = [])
    {
        if (empty($shop_id)) {
            throw new InvalidArgumentException('Shop ID is missing');
        }

        if (empty($email)) {
            throw new InvalidArgumentException('Email address is missing');
        }

        $headers = ['Content-Type' => 'application/json'];

        return $this->apiRequest("shops/$shop_id/customers/$email", 'PUT', $payload, $headers);
    }
}