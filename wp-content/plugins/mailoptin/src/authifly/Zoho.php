<?php

namespace Authifly\Provider;

use Authifly\Adapter\OAuth2;
use Authifly\Data;
use Authifly\Exception\InvalidAccessTokenException;

/**
 * Class Zoho
 * @see https://www.zoho.com/accounts/protocol/oauth/web-apps/authorization.html
 * @package Authifly\Provider
 */
class Zoho extends OAuth2
{
    /**
     * {@inheritdoc
     *
     * This is public we are overriding this for ZohoCRM
     */
    public $apiBaseUrl = 'https://campaigns.zoho.com/api/v1.1/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://accounts.zoho.com/oauth/v2/auth';

    /**
     * {@inheritdoc}
     *
     *
     * This is public we are overriding this for refreshing token
     */
    public $accessTokenUrl = 'https://accounts.zoho.com/oauth/v2/token';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://www.zoho.com/campaigns/help/developers/index.html';

    /**
     * {@inheritdoc}
     */
    protected $supportRequestState = false;

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->AuthorizeUrlParameters = [
            'response_type' => 'code',
            'access_type'   => 'offline',
            'prompt'        => 'consent',
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->callback,
            'scope'         => $this->scope,
        ];

        $access_token = $this->getStoredData('access_token');

        $config_access_token = $this->config->get('access_token');

        if ( ! empty($config_access_token)) {
            $access_token = $config_access_token;
        }

        if ( ! empty($access_token)) {
            $this->apiRequestHeaders = [
                'Authorization' => 'Bearer ' . $access_token
            ];
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see https://prezoho.zohocorp.com/accounts/protocol/oauth/multi-dc.html
     * @see https://www.zoho.com/accounts/protocol/oauth/web-apps/authorization.html
     */
    protected function exchangeCodeForAccessToken($code)
    {
        /**
         * @see https://www.zoho.com/accounts/protocol/oauth/multi-dc/client-authorization.html
         * @see https://www.zoho.com/accounts/protocol/oauth/web-apps/access-token.html
         */
        $this->accessTokenUrl                  = $_GET['accounts-server'] . '/oauth/v2/token';
        $this->tokenExchangeParameters['code'] = $code;

        if (isset($_GET['location']) && $_GET['location'] != 'us') {
            $this->tokenExchangeParameters['client_secret'] = $this->config->filter('keys')->get($_GET['location'] . '_secret');
        }

        $response = $this->httpClient->request(
            $this->accessTokenUrl,
            $this->tokenExchangeMethod,
            $this->tokenExchangeParameters,
            $this->tokenExchangeHeaders
        );

        $this->validateApiResponse('Unable to exchange code for API access token');

        return $response;
    }

    /**
     * {@inheritdoc}
     *
     * @see https://prezoho.zohocorp.com/accounts/protocol/oauth/multi-dc.html
     * @see https://www.zoho.com/accounts/protocol/oauth/web-apps/authorization.html
     */
    protected function validateAccessTokenExchange($response)
    {
        $data = (new Data\Parser())->parse($response);

        $collection = new Data\Collection($data);

        if ( ! $collection->exists('access_token')) {
            throw new InvalidAccessTokenException(
                'Provider returned an invalid access_token: ' . htmlentities($response)
            );
        }

        $this->storeData('access_token', $collection->get('access_token'));
        $this->storeData('token_type', $collection->get('token_type'));

        if ($collection->get('refresh_token')) {
            $this->storeData('refresh_token', $collection->get('refresh_token'));
        }

        // calculate when the access token expire

        // for zoho, expires_in is in milliseconds. Instead we use expires_in_sec
        if ($collection->exists('expires_in_sec')) {
            $expires_at = time() + (int)$collection->get('expires_in_sec');

            $this->storeData('expires_in', $collection->get('expires_in_sec'));
            $this->storeData('expires_at', $expires_at);
        }

        // custom feature starts
        if (isset($_GET['accounts-server'])) {
            $this->storeData('accounts_server', $_GET['accounts-server']);
        }

        if (isset($_GET['location'])) {
            $this->storeData('location', $_GET['location']);
        }

        if ($collection->exists('api_domain')) {
            $this->storeData('api_domain', $collection->get('api_domain'));
        }

        // custom feature ends.

        $this->deleteStoredData('authorization_state');

        $this->initialize();

        return $collection;
    }

    public function getAccessToken()
    {
        $tokenNames = [
            'access_token',
            'access_token_secret',
            'token_type',
            'refresh_token',
            'expires_in',
            'expires_at',
            'api_domain',
            'location',
            'accounts_server'
        ];

        $tokens = [];

        foreach ($tokenNames as $name) {
            if ($this->getStoredData($name)) {
                $tokens[$name] = $this->getStoredData($name);
            }
        }

        return $tokens;
    }
}