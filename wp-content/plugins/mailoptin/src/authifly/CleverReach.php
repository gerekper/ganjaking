<?php

namespace Authifly\Provider;

use Authifly\Adapter\OAuth2;
use Authifly\Exception\InvalidArgumentException;
use Authifly\Data;

/**
 * CleverReach OAuth2 provider adapter.
 */
class CleverReach extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://rest.cleverreach.com/v3/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://rest.cleverreach.com/oauth/authorize.php';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://rest.cleverreach.com/oauth/token.php';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://rest.cleverreach.com/explorer/v3';

    protected $supportRequestState = false;

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->tokenRefreshHeaders = [
            'Authorization' => 'Basic ' . base64_encode($this->clientId . ":" . $this->clientSecret)
        ];

        $refresh_token = $this->getStoredData('refresh_token');

        if (empty($refresh_token)) {
            $refresh_token = $this->config->get('refresh_token');
        }

        $this->tokenRefreshParameters['refresh_token'] = $refresh_token;

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
     * Return the array of groups (receiver lists)
     *
     * @return array
     */
    public function getGroupList()
    {
        $groups = $this->apiRequest("groups");

        $filtered = [];
        foreach ($groups as $group) {
            $filtered[$group->id] = $group->name;
        }

        return $filtered;
    }

    public function get_custom_fields($group_id = '')
    {
        $attributes_response = (array)$this->apiRequest("attributes");

        $group_response = [];

        if ( ! empty($group_id)) {
            $group_response = (array)$this->apiRequest("groups/$group_id/attributes");
        }

        $fields = array_merge($attributes_response, $group_response);

        $filtered = [];

        foreach ($fields as $field) {
            if ($field->group_id == 0) {
                $filtered['global_attributes'][$field->name] = $field->description;
            } else {
                $filtered['attributes'][$field->name] = $field->description;
            }

        }

        return $filtered;
    }

    public function getTags($limit = 20, $group_id = 0)
    {
        $response = $this->apiRequest("tags", "GET", ['limit' => $limit, 'group_id' => $group_id]);

        $fields = (new Data\Collection($response))->toArray();

        $filtered = [];
        foreach ($fields as $key => $field) {
            $filtered[$field->tag] = $field->tag;

        }

        return $filtered;
    }

    /**
     * Add subscriber to an email list.
     *
     * @param string $group_id
     * @param string $email
     * @param array $subscriber_data
     *
     * @return object
     * @throws InvalidArgumentException
     */
    public function addSubscriber($group_id, $email, $subscriber_data = [])
    {
        if (empty($group_id)) {
            throw new InvalidArgumentException('Group ID is missing');
        }

        if (empty($email)) {
            throw new InvalidArgumentException('Email address is missing');
        }

        return $this->apiRequest("groups/$group_id/receivers/upsert", 'POST', $subscriber_data);
    }

    /**
     * @param array $payload
     * @param array $headers
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function sendMailing($payload, $headers = [])
    {
        $headers = array_replace(['Content-Type' => 'application/json'], $headers);

        $response = $this->apiRequest('mailings', 'POST', $payload, $headers);

        if (isset($response->error)) {
            throw new InvalidArgumentException($response[0]->error_message, $this->httpClient->getResponseHttpCode());
        }

        return $response;
    }
}
