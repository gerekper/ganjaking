<?php
/**
 * Copyright (C) 2016  Agbonghama Collins <me@w3guy.com>
 */

namespace MailOptin\ConvertKitConnect;

class APIClass
{
    protected $api_key;

    protected $api_url;

    /**
     * @var int
     */
    protected $api_version = 3;
    /**
     * @var string
     */
    protected $api_url_base = 'https://api.convertkit.com/';


    public function __construct($api_key)
    {
        $this->api_key = $api_key;
        $this->api_url = $this->api_url_base . 'v' . $this->api_version . '/';
    }

    /**
     * @param $endpoint
     * @param array $args
     * @param string $method
     * @return array
     * @throws \Exception
     */
    public function make_request($endpoint, $args = [], $method = 'get')
    {
        $url = add_query_arg('api_key', $this->api_key, $this->api_url . $endpoint);

        $wp_args = ['method' => strtoupper($method), 'timeout' => 30];

        switch ($method) {
            case 'post':
                $wp_args['headers'] = ["Content-Type" => "application/json"];
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

        $response_body = json_decode(wp_remote_retrieve_body($response));
        $response_http_code = wp_remote_retrieve_response_code($response);


        return ['status_code' => $response_http_code, 'body' => $response_body];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function get_forms()
    {
        $response = $this->make_request('forms');

        return $response;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function get_custom_fields()
    {
        $response = $this->make_request('custom_fields');

        return $response;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function get_sequences()
    {
        $response = $this->make_request('courses');

        return $response;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function get_tags()
    {
        $response = $this->make_request('tags');

        return $response;
    }

    /**
     * @param $form_id
     * @param $email
     * @param $first_name
     * @param $last_name
     * @param array $sequences
     * @param array $tags
     * @param array $custom_fields
     * @return array
     * @throws \Exception
     */
    public function add_subscriber($form_id, $email, $first_name, $last_name, $sequences = [], $tags = [], $custom_fields = [])
    {
        $payload = [
            'email' => $email,
            'first_name' => $first_name,
        ];

        $payload = array_filter($payload, function ($value) {
            return !empty($value);
        });

        if (!empty($last_name)) {
            $payload['fields'] = ['last_name' => $last_name];
        }

        if (!empty($sequences)) {
            $payload['courses'] = implode(',', $sequences);
        }

        if (!empty($tags)) {
            $payload['tags'] = implode(',', $tags);
        }

        if (!empty($custom_fields)) {
            $payload['fields'] = $custom_fields;
        }

        $response = $this->make_request("forms/{$form_id}/subscribe", $payload, 'post');

        return $response;
    }
}