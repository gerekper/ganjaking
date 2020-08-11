<?php

namespace Authifly\Provider;

use Authifly\Adapter\OAuth2;
use Authifly\Exception\InvalidArgumentException;
use Authifly\Data;

/**
 * Infusionsoft OAuth2 provider adapter.
 */
class Infusionsoft extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://api.infusionsoft.com/crm/rest/v1/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://accounts.infusionsoft.com/app/oauth/authorize';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://api.infusionsoft.com/token';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'https://developer.infusionsoft.com/get-started/';

    /**
     * {@inheritdoc}
     */
    protected $scope = 'full';

    protected $supportRequestState = false;

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

        $this->tokenRefreshHeaders = [
            'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
            'Content-Type'  => 'application/x-www-form-urlencoded'
        ];

        $refresh_token = $this->getStoredData('refresh_token');

        if (empty($refresh_token)) {
            $refresh_token = $this->config->get('refresh_token');
        }

        $this->tokenRefreshParameters['refresh_token'] = $refresh_token;

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
     * Infusionsoft doesn't work when access_token is included in payload. So we removed it by redeclaring the method.
     *
     * {@inheritdoc}
     */
    public function apiRequest($url, $method = 'GET', $parameters = [], $headers = [])
    {
        // refresh tokens if needed
        if ($this->hasAccessTokenExpired() === true) {
            $this->refreshAccessToken();
        }

        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
            $url = $this->apiBaseUrl . $url;
        }

        $parameters = array_replace($this->apiRequestParameters, (array)$parameters);
        $headers    = array_replace($this->apiRequestHeaders, (array)$headers);

        $response = $this->httpClient->request(
            $url,
            $method,     // HTTP Request Method. Defaults to GET.
            $parameters, // Request Parameters
            $headers     // Request Headers
        );

        $this->validateApiResponse('Signed API request has returned an error');

        $response = (new Data\Parser())->parse($response);

        return $response;
    }

    /**
     * Return all tags
     *
     * @return array
     */
    public function getTags()
    {
        $response = $this->apiRequest("tags", 'GET', ['limit' => 5000]);

        $tags = (new Data\Collection($response))->filter('tags')->toArray();

        if ( ! is_array($tags) || empty($tags)) return [];

        $filtered = [];

        foreach ($tags as $tag) {
            $filtered[$tag->id] = $tag->name;
        }

        return $filtered;
    }

    /**
     * @return array
     */
    public function get_custom_fields()
    {
        $response = $this->apiRequest("contacts/model");

        $fields = (new Data\Collection($response))->filter('custom_fields')->toArray();

        $filtered = [];

        foreach ($fields as $field) {
            $filtered[$field->id] = $field->label;
        }

        return $filtered;
    }

    /**
     * @return array
     */
    public function get_users()
    {
        $response = $this->apiRequest("users", 'GET', ['limit' => 5000]);

        $users = (new Data\Collection($response))->filter('users')->toArray();

        if ( ! is_array($users) || empty($users)) return [];

        $filtered = [];

        foreach ($users as $user) {
            $name = trim(! empty($user->preferred_name) ? $user->preferred_name : $user->given_name . ' ' . $user->family_name);

            if (empty($name)) {
                $name = $user->email_address;
            }

            $filtered[$user->id] = $name;
        }

        return $filtered;
    }

    public function get_contact_ids($tagId = false)
    {
        if ( ! empty($tagId)) {
            $response = $this->apiRequest(sprintf("tags/%s/contacts", $tagId), 'GET', ['limit' => 9999999999]);
        } else {
            $response = $this->apiRequest("contacts", 'GET', ['limit' => 9999999999]);
        }

        $contacts = (new Data\Collection($response))->filter('contacts')->toArray();

        if ( ! is_array($contacts) || empty($contacts)) return [];

        $filtered = [];

        foreach ($contacts as $contact) {

            $filtered[] = isset($contact->contact->id) ? $contact->contact->id : $contact->id;
        }

        return $filtered;
    }

    public function apply_tags($contactId, $tags)
    {
        if (empty($tags)) throw new InvalidArgumentException('No tag specified');

        return $this->apiRequest(sprintf("contacts/%s/tags", $contactId), 'POST', ['tagIds' => $tags]);
    }

    /**
     * @param $payload
     *
     * @return mixed
     */
    public function addUpdateSubscriber($payload)
    {
        return $this->apiRequest("contacts", 'PUT', $payload);
    }

    /**
     * @param $payload
     *
     * @return mixed
     */
    public function sendEmail($payload)
    {
        return $this->apiRequest("emails/queue", 'POST', $payload);
    }
}