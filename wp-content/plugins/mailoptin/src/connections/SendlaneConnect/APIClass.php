<?php

namespace MailOptin\SendlaneConnect;

class APIClass
{
    protected $api_key;
    protected $hash_key;

    protected $api_url;

    /**
     * @var int
     */
    protected $api_version = 1;

    public function __construct($api_key, $hash_key, $domain)
    {
        $this->api_key  = $api_key;
        $this->hash_key = $hash_key;

        if (strpos($domain, 'http') === false) {
            $domain = 'https://' . $domain;
        }

        $this->api_url = $domain . '/api/v' . $this->api_version . '/';
    }

    /**
     * All calls should be made using the POST method according to http://help.sendlane.com/knowledgebase/api-docs/
     * @param $endpoint
     * @param array $args
     * @param string $method
     *
     * @return array
     * @throws \Exception
     */
    public function make_request($endpoint, $args = [], $method = 'post')
    {
        $url = add_query_arg(['api' => $this->api_key, 'hash' => $this->hash_key], $this->api_url . $endpoint);

        $wp_args = ['method' => strtoupper($method), 'timeout' => 30];

        $wp_args['headers'] = ["Content-Type" => 'application/json'];

        switch ($method) {
            case 'post':
                $wp_args['body'] = json_encode($args);
                break;
            case 'get':
                $url = add_query_arg($args, $url);
                break;
        }

        $response = wp_remote_request($url, $wp_args);

        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        $response_body      = json_decode(wp_remote_retrieve_body($response));
        $response_http_code = wp_remote_retrieve_response_code($response);

        return ['status_code' => $response_http_code, 'body' => $response_body];
    }
}