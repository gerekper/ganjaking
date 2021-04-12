<?php

namespace Authifly\Provider;

use Authifly\Adapter\OAuth2;
use Authifly\Exception\HttpRequestFailedException;
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
     * @throws HttpRequestFailedException
     * @throws \Authifly\Exception\HttpClientFailureException
     * @throws \Authifly\Exception\InvalidAccessTokenException
     */
    public function getGroupList()
    {
        $groups = $this->apiRequest("groups");
<<<<<<< HEAD

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
=======
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c

        $fields = array_merge($attributes_response, $group_response);

        $filtered = [];
<<<<<<< HEAD

        foreach ($fields as $field) {
            if ($field->group_id == 0) {
                $filtered['global_attributes'][$field->name] = $field->description;
            } else {
                $filtered['attributes'][$field->name] = $field->description;
            }

=======
        foreach ($groups as $group) {
            $filtered[$group->id] = $group->name;
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c
        }

        return $filtered;
    }

<<<<<<< HEAD
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


    public function getForms()
    {
        $response = $this->apiRequest("forms");
=======
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
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c

        $fields = (new Data\Collection($response))->toArray();

        $filtered = [];
        foreach ($fields as $key => $field) {
<<<<<<< HEAD
            $filtered[$field->id] = $field->name;
=======
            $filtered[$field->tag] = $field->tag;
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c

        }

        return $filtered;
    }

    /**
     * Add subscriber to an email list.
     *
     * @param string $group_id
     * @param string $email
     * @param array $subscriber_data
<<<<<<< HEAD
     *
     * @param array $doi_data
=======
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c
     *
     * @return object
     * @throws HttpRequestFailedException
     * @throws InvalidArgumentException
     * @throws \Authifly\Exception\HttpClientFailureException
     * @throws \Authifly\Exception\InvalidAccessTokenException
     */
<<<<<<< HEAD
    public function addSubscriber($group_id, $email, $subscriber_data = [], $doi_data = [])
=======
    public function addSubscriber($group_id, $email, $subscriber_data = [])
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c
    {
        if (empty($group_id)) {
            throw new InvalidArgumentException('Group ID is missing');
        }

        if (empty($email)) {
            throw new InvalidArgumentException('Email address is missing');
        }

<<<<<<< HEAD
        $subscriber_data['registered'] = time();
        $subscriber_data['activated']  = time();

        if ( ! empty($doi_data)) {

            $subscriber_data['activated'] = 0;

            $find_receiver = $this->getSubscribers($group_id, $email);

            if (false != $find_receiver) {
                $subscriber_data['activated'] = $find_receiver->activated;
                unset($subscriber_data['registered']);
            }
        }

        $receivers_update = $this->apiRequest("groups/$group_id/receivers/upsert", 'POST', $subscriber_data);

        if (isset($receivers_update->activated) && $receivers_update->activated == 0 && ! empty($doi_data)) {
            $form_id = $doi_data['form_id'];
            $this->apiRequest("forms/{$form_id}/send/activate", "POST", $doi_data);
        }

        return $receivers_update;
    }


    public function getSubscribers($group_id, $pool_id)
    {
        try {

            $find_receiver = $this->apiRequest("groups/$group_id/receivers/$pool_id");

            return $find_receiver;

        } catch (\Exception $e) {
            return false;
        }
=======
        return $this->apiRequest("groups/$group_id/receivers/upsert", 'POST', $subscriber_data);
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c
    }

    /**
     * @param array $payload
     * @param array $headers
     *
     * @return mixed
<<<<<<< HEAD
     * @throws HttpRequestFailedException
     * @throws \Authifly\Exception\HttpClientFailureException
     * @throws \Authifly\Exception\InvalidAccessTokenException
=======
     * @throws InvalidArgumentException
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c
     */
    public function sendMailing($payload, $headers = [])
    {
        $headers = array_replace(['Content-Type' => 'application/json'], $headers);

        $response = $this->apiRequest('mailings', 'POST', $payload, $headers);
<<<<<<< HEAD

        if (isset($response->error) || ! isset($response->id)) {
            throw new HttpRequestFailedException($response[0]->error_message, $this->httpClient->getResponseHttpCode());
        }

        $response2 = $this->apiRequest(sprintf('mailings/%s/release', $response->id), 'POST', ['time' => 0], $headers);

        return isset($response2->id);
=======

        if (isset($response->error)) {
            throw new InvalidArgumentException($response[0]->error_message, $this->httpClient->getResponseHttpCode());
        }

        return $response;
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c
    }
}
