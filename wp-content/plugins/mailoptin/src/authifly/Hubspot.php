<?php

namespace Authifly\Provider;

use Authifly\Adapter\OAuth2;
use Authifly\Exception\InvalidArgumentException;
use Authifly\Data;

/**
 * Hubspot OAuth2 provider adapter.
 */
class Hubspot extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://api.hubapi.com/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://app.hubspot.com/oauth/authorize';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://api.hubapi.com/oauth/v1/token';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://developers.hubspot.com/docs/';

    /**
     * {@inheritdoc}
     */
    protected $scope = 'contacts';

    protected $supportRequestState = false;

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->tokenRefreshParameters = [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $this->getStoredData('refresh_token'),
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->callback,
        ];

        /** Hubspot explicitly require access token to be set as Bearer.  */

        // if access token is found in storage, utilize else
        $storage_access_token = $this->getStoredData('access_token');
        if ( ! empty($storage_access_token)) {
            $this->apiRequestHeaders = [
                'Authorization' => 'Bearer ' . $storage_access_token,
                'Content-Type'  => 'application/json'
            ];
        }

        // use the one supplied in config.
        $config_access_token = $this->config->get('access_token');
        if ( ! empty($config_access_token)) {
            $this->apiRequestHeaders = [
                'Authorization' => 'Bearer ' . $config_access_token,
                'Content-Type'  => 'application/json'
            ];
        }
    }

    /**
     * Return the contact lists for a portal.
     *
     * @param integer $count max number of results to fetch
     *
     * @return array
     */
    public function getEmailList($count = 250)
    {
        $response = $this->apiRequest("contacts/v1/lists/static", "GET", ['count' => $count]);
        $data     = new Data\Collection($response);
        $lists    = $data->filter('lists')->toArray();

        if ( ! is_array($lists)) {
            return array();
        }

        $filtered = [];
        foreach ($lists as $list) {
            $filtered[$list->listId] = $list->name;
        }

        return $filtered;
    }

    /**
     * Get custom fields.
     *
     *
     * @return array
     */
    public function getListCustomFields()
    {
        $fields = $this->apiRequest("properties/v1/contacts/properties");

        if (empty($fields)) {
            return array();
        }

        $filtered = array();
        foreach ($fields as $field) {

            //Ensure the field is not automatically set by Hubspot
            if ($field->readOnlyDefinition) {
                continue;
            }

            $filtered[$field->name] = $field->label;
        }

        return $filtered;
    }

    /**
     * Add subscriber to an email list.
     *
     * @param string $list_id
     * @param array $payload
     *
     * @return object
     * @throws InvalidArgumentException
     */
    public function addSubscriber($list_id, $email, $contact_data = [])
    {
        if (empty($list_id)) {
            throw new InvalidArgumentException('List ID is missing');
        }

        if (empty($email)) {
            throw new InvalidArgumentException('Email address is missing');
        }

        $response = $this->apiRequest("contacts/v1/contact/createOrUpdate/email/$email", 'POST', $contact_data);

        if ( ! empty($response->vid) && 'all' != $list_id) {
            $this->addSubscriberToList($list_id, $email);
        }

        return $response;
    }

    /**
     * Adds subscriber to an email list.
     *
     * @param string $list_id
     * @param string $email
     *
     * @return object
     * @throws InvalidArgumentException
     */
    private function addSubscriberToList($list_id, $email)
    {
        $params = (object)[
            'emails' => [$email]
        ];

        return $this->apiRequest("contacts/v1/lists/$list_id/add", 'POST', $params);
    }
}