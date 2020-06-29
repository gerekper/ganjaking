<?php

namespace MailOptin\MailerliteConnect\APIClass\Common;

class RestClient
{

    public $httpClient;

    public $apiKey;

    public $baseUrl;

    /**
     * @param string $baseUrl
     * @param string $apiKey
     * @param mixed $httpClient
     */
    public function __construct($baseUrl, $apiKey, $httpClient = null)
    {
        $this->baseUrl    = $baseUrl;
        $this->apiKey     = $apiKey;
        $this->httpClient = $httpClient;
    }

    /**
     * Execute GET request
     *
     * @param  string $endpointUri
     * @param  array $queryString
     *
     * @return [type]
     */
    public function get($endpointUri, $queryString = [])
    {
        return $this->send('GET', $endpointUri . '?' . http_build_query($queryString));
    }

    /**
     * Execute POST request
     *
     * @param  string $endpointUri
     * @param  array $data
     *
     * @return [type]
     */
    public function post($endpointUri, $data = [])
    {
        return $this->send('POST', $endpointUri, $data);
    }

    /**
     * Execute PUT request
     *
     * @param  string $endpointUri
     * @param  array $putData
     *
     * @return [type]
     */
    public function put($endpointUri, $putData = [])
    {
        return $this->send('PUT', $endpointUri, $putData);
    }

    /**
     * Execute DELETE request
     *
     * @param  string $endpointUri
     *
     * @return [type]
     */
    public function delete($endpointUri)
    {
        return $this->send('DELETE', $endpointUri);
    }

    /**
     * Execute HTTP request
     *
     * @param  string $method
     * @param  string $endpointUri
     * @param  string $body
     * @param  array $headers
     *
     * @return [type]
     */
    protected function send($method, $endpointUri, $body = null, array $headers = [])
    {
        $headers     = array_merge($headers, self::getDefaultHeaders());
        $endpointUrl = $this->baseUrl . $endpointUri;

        $args = array(
            'headers' => $headers,
            'method'  => $method,
            'timeout' => 30
        );

        if(isset($body)) {
            $args['body'] = json_encode($body);
        }

        $response = wp_remote_request($endpointUrl, $args);

        return $this->handleResponse($response);
    }

    /**
     * Handle HTTP response
     *
     * @param $response
     *
     * @return [type]
     */
    protected function handleResponse($response)
    {
        $status = wp_remote_retrieve_response_code($response);

        $data             = (string)wp_remote_retrieve_body($response);
        $jsonResponseData = json_decode($data, false);
        $body             = $data && $jsonResponseData === null ? $data : $jsonResponseData;

        return ['status_code' => $status, 'body' => $body];
    }

    /**
     * @return array
     */
    protected function getDefaultHeaders()
    {
        return [
            'User-Agent'          => ApiConstants::SDK_USER_AGENT . '/' . ApiConstants::SDK_VERSION,
            'X-MailerLite-ApiKey' => $this->apiKey,
            'Content-Type'        => 'application/json'
        ];
    }
}