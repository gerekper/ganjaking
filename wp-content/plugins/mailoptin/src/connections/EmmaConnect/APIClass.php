<?php

namespace MailOptin\EmmaConnect;

class APIClass
{
    protected $public_api_key;
    protected $private_api_key;
    protected $account_id;

    protected $api_url;

    public function __construct($public_api_key, $private_api_key, $account_id)
    {
        $this->public_api_key  = $public_api_key;
        $this->private_api_key = $private_api_key;
        $this->account_id      = $account_id;

        $this->api_url = 'https://api.e2ma.net/';
    }

    /**
     * All calls should be made using the POST method according to http://help.emma.com/knowledgebase/api-docs/
     *
     * @param $endpoint
     * @param array $args
     * @param string $method
     *
     * @return array
     * @throws \Exception
     */
    public function make_request($endpoint, $args = [], $method = 'get')
    {
        $url = $this->api_url . $this->account_id . '/' . $endpoint;

        $wp_args = ['method' => strtoupper($method), 'timeout' => 30];

        $wp_args['headers'] = [
            "Content-Type"  => 'application/json',
            "Authorization" => 'Basic ' . base64_encode($this->public_api_key . ':' . $this->private_api_key)
        ];

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