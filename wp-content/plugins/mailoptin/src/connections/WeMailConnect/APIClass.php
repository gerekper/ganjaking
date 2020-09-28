<?php

namespace MailOptin\WeMailConnect;

class APIClass
{
    protected $api_key;

    protected $api_url;

    /**
     * @var string
     */
    protected $api_url_base = 'https://api.getwemail.io/api/v1';

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
        $this->api_url = $this->api_url_base . '/';
    }

    /**
     * @param $endpoint
     * @param array $args
     * @param string $method
     *
     * @return array
     * @throws \Exception
     */
    public function make_request($endpoint, $args = [], $method = 'get')
    {
        $url = $this->api_url . $endpoint;

        $wp_args = ['method' => strtoupper($method), 'timeout' => 45];

        $wp_args['headers'] = [
            "Content-Type"  => 'application/json',
            'X-API-KEY' => $this->api_key
        ];

        $url = add_query_arg($args, $url);

        $response = wp_remote_request($url, $wp_args);

        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        $response_body      = json_decode(wp_remote_retrieve_body($response), true);
        $response_http_code = wp_remote_retrieve_response_code($response);

        return ['status_code' => $response_http_code, 'body' => $response_body];
    }
}