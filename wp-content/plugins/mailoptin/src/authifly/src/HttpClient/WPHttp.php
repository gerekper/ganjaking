<?php
/*!
* Authifly
* https://hybridauth.github.io | https://github.com/mailoptin/authifly
*  (c) 2017 Hybridauth authors | https://hybridauth.github.io/license.html
*/

namespace Authifly\HttpClient;

/**
 * AuthiFly default Http client
 */
class WPHttp implements HttpClientInterface
{
    /**
     * Method request() arguments
     *
     * This is used for debugging.
     *
     * @var array
     */
    protected $requestArguments = [];

    /**
     * Default request headers
     *
     * @var array
     */
    protected $requestHeader = [
        'Accept'        => '*/*',
        'Cache-Control' => 'max-age=0',
        'Connection'    => 'keep-alive',
        'Expect'        => '',
        'Pragma'        => '',
    ];

    /**
     * Raw response returned by server
     *
     * @var string
     */
    protected $responseBody = '';

    /**
     * Headers returned in the response
     *
     * @var array
     */
    protected $responseHeader = [];

    /**
     * Response HTTP status code
     *
     * @var integer
     */
    protected $responseHttpCode = 0;

    /**
     * Last curl error number
     *
     * @var mixed
     */
    protected $responseClientError = null;

    /**
     * Authifly logger instance
     *
     * @var object
     */
    protected $logger = null;

    /**
     * {@inheritdoc}
     */
    public function request($uri, $method = 'GET', $parameters = [], $headers = [])
    {
        $this->requestArguments = ['uri' => $uri, 'method' => $method, 'parameters' => $parameters, 'headers' => $headers];

        $this->requestHeader = array_merge($this->requestHeader, (array)$headers);

        $this->requestArguments['headers'] = $this->requestHeader;

        $body_content = $parameters;

        if (in_array($method, ['POST', 'PUT'])) {

            if (isset($this->requestHeader['Content-Type']) && $this->requestHeader['Content-Type'] == 'application/json') {
                $body_content = json_encode($parameters);

                if (isset($parameters[0]) && $parameters[0] == 'empty_json') $body_content = '{}';
            }
        }

        $args = [
            'method'     => $method,
            'headers'    => $this->requestHeader,
            'body'       => $body_content,
            'connect_timeout'    => 10,
            'timeout'    => 60,
            'user-agent' => 'AuthiFly, PHP Social Authentication Library (https://github.com/mailoptin/authifly)'
        ];

        $response = wp_remote_request($uri, $args);

        if (is_wp_error($response)) {
            $this->responseClientError = $response->get_error_message();
        }

        $this->responseBody     = wp_remote_retrieve_body($response);
        $this->responseHttpCode = wp_remote_retrieve_response_code($response);
        $this->responseHeader   = wp_remote_retrieve_headers($response);

        if ($this->logger) {
            $this->logger->debug(sprintf('%s::request( %s, %s ), response:', get_class($this), $uri, $method), $this->getResponse());

            if (false === $response) {
                $this->logger->error(sprintf('%s::request( %s, %s ), error:', get_class($this), $uri, $method), [$this->responseClientError]);
            }
        }

        return $this->responseBody;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        return [
            'request'  => $this->getRequestArguments(),
            'response' => [
                'code'    => $this->getResponseHttpCode(),
                'headers' => $this->getResponseHeader(),
                'body'    => $this->getResponseBody(),
            ],
            'client'   => [
                'error' => $this->getResponseClientError(),
                'opts'  => null,
            ],
        ];
    }

    /**
     * Set logger instance
     *
     * @param object $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseHeader()
    {
        return $this->responseHeader;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseHttpCode()
    {
        return $this->responseHttpCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseClientError()
    {
        return $this->responseClientError;
    }

    /**
     * Returns method request() arguments
     *
     * This is used for debugging.
     *
     * @return array
     */
    protected function getRequestArguments()
    {
        return $this->requestArguments;
    }
}
