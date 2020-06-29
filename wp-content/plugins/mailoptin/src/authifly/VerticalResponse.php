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
class VerticalResponse extends OAuth2
{
    /**
     * {@inheritdoc}
     */
    protected $apiBaseUrl = 'https://vrapi.verticalresponse.com/api/v1/';

    /**
     * {@inheritdoc}
     */
    protected $authorizeUrl = 'https://vrapi.verticalresponse.com/api/v1/oauth/authorize';

    /**
     * {@inheritdoc}
     */
    protected $accessTokenUrl = 'https://vrapi.verticalresponse.com/api/v1/oauth/access_token';

    /**
     * {@inheritdoc}
     */
    protected $apiDocumentation = 'http://developer.verticalresponse.com/docs';

    /**
     * VR doesn't return state in auth request.
     * @var bool
     */
    protected $supportRequestState = false;

    /**
     * Init the oauth vars
     *
     * {@inheritdoc}
     */
    protected function initialize()
    {
        parent::initialize();

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
     * Get email lists belonging to the user.
     *
     *
     * @return object
     */
    public function getEmailList()
    {
        $lists = $this->apiRequest("lists");
        $data  = new Data\Collection($lists);

        return $data->filter('items')->toArray();
    }

    /**
     * Get custom fields for a given user.
     *
     *
     * @return object
     */
    public function getListCustomFields()
    {
        $fields = $this->apiRequest("custom_fields");
        $data   = new Data\Collection($fields);

        return $data->filter('items')->toArray();
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
    public function addSubscriber($list_id, $email, $extra_data = [])
    {
        if (empty($list_id)) {
            throw new InvalidArgumentException('List ID is missing');
        }

        if (empty($email)) {
            throw new InvalidArgumentException('Email address is missing');
        }

        $payload['email'] = $email;
        $payload          = array_replace($payload, $extra_data);

        $headers = ['Content-Type' => 'application/json'];

        return $this->apiRequest("lists/$list_id/contacts", 'POST', $payload, $headers);
    }

    /**
     * Create draft campaign.
     *
     * @param array $payload
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function createDraftCampaign($payload)
    {
        if (empty($payload)) {
            throw new InvalidArgumentException('Payload is missing');
        }

        $required_fields = ['subject', 'from_label', 'from_address', 'message'];

        foreach ($required_fields as $required_field) {
            if ( ! in_array($required_field, array_keys($payload))) :
                throw new InvalidArgumentException(sprintf('%s required field is missing', $required_field));
                break;
            endif;
        }

        $headers = ['Content-Type' => 'application/json'];
        $res     = $this->apiRequest("messages/emails", 'POST', $payload, $headers);

        if (201 === $this->httpClient->getResponseHttpCode() && isset($res->url)) {
            return $res->url;
        }

        return false;

    }

    /**
     * Send draft campaign.
     *
     *
     * @param int $campaign_url
     * @param array $lists
     * @param string $send_date
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function sendDraftCampaign($campaign_url, $lists, $send_date = 0)
    {
        if (empty($campaign_url)) {
            throw new InvalidArgumentException('Campaign URL is missing');
        }

        if (empty($lists) || ! is_array($lists)) {
            throw new InvalidArgumentException('Campaign list is missing');
        }

        $payload = ['list_ids' => $lists];

        if (is_string($send_date)) {
            $payload['scheduled_at'] = $send_date;
        }

        $headers = ['Content-Type' => 'application/json'];

        $this->apiRequest($campaign_url, 'POST', $payload, $headers);

        return 200 === $this->httpClient->getResponseHttpCode();
    }

    protected function validateApiResponse($error = '')
    {
        $error .= ! empty($error) ? '. ' : '';

        if ($this->httpClient->getResponseClientError()) {
            throw new HttpClientFailureException(
                $error . 'HTTP client error: ' . $this->httpClient->getResponseClientError() . '.'
            );
        }

        // if validateApiResponseHttpCode is set to false, we by pass verification of http status code
        if ( ! $this->validateApiResponseHttpCode) {
            return;
        }

        $status = $this->httpClient->getResponseHttpCode();

        if ($status < 200 || $status > 299) {
            throw new HttpRequestFailedException($this->httpClient->getResponseBody());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param string $url
     * @param string $method
     * @param array $parameters
     * @param array $headers
     *
     * @return mixed
     */
    public function apiRequest($url, $method = 'GET', $parameters = [], $headers = [])
    {

        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
            $url = $this->apiBaseUrl . $url;
        }

        //When sending campaigns, VR throws an error if the payload contains an access_token param
        /*if($this->getStoredData('access_token')) {
            $this->apiRequestParameters[$this->accessTokenName] = $this->getStoredData('access_token');
        }*/

        $parameters = array_replace($this->apiRequestParameters, (array)$parameters);
        $headers    = array_replace($this->apiRequestHeaders, (array)$headers);

        $response = $this->httpClient->request(
            $url,
            $method,     // HTTP Request Method. Defaults to GET.
            $parameters, // Request Parameters
            $headers     // Request Headers
        );

        $this->validateApiResponse();

        $response = (new Data\Parser())->parse($response);

        return $response;
    }
}