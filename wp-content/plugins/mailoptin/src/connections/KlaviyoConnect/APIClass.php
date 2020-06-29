<?php
/**
 * Copyright (C) 2016  Agbonghama Collins <me@w3guy.com>
 */

namespace MailOptin\KlaviyoConnect;

class APIClass
{
    protected $api_key;

    protected $api_url;

    /**
     * @var int
     */
    protected $api_version = 1;
    /**
     * @var string
     */
    protected $api_url_base = 'https://a.klaviyo.com/api/';


    public function __construct($api_key)
    {
        $this->api_key = $api_key;
        $this->api_url = $this->api_url_base . 'v' . $this->api_version . '/';
    }

    /**
     * @param $endpoint
     * @param array $args
     * @param string $method
     *
     * @return array
     * @throws \Exception
     */
    protected function make_request($endpoint, $args = [], $method = 'get')
    {
        $wp_args = ['method' => strtoupper($method), 'timeout' => 30];

        $wp_args['headers'] = [
            "Content-Type" => 'application/x-www-form-urlencoded'
        ];

        $url = $this->api_url . $endpoint;

        $args['api_key'] = $this->api_key;

        if (strpos($endpoint, 'list') !== false) {
            $url                = $this->api_url_base . 'v2/' . $endpoint;
            $wp_args['headers'] = [
                "Content-Type" => 'application/json'
            ];
            if ($method !== 'get') {
                $args = json_encode($args);
            }
        }

        switch ($method) {
            case 'post':
                $wp_args['body'] = $args;
                break;
            case 'get':
                $url = add_query_arg($args, $url);
                break;
            default:
                $wp_args['body'] = $args;
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

    /**
     * @return array
     * @throws \Exception
     */
    public function get_lists()
    {
        return $this->make_request('lists');
    }

    /**
     * @param string $list_id
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param array $properties extra data to tie to the subscriber
     *
     * @return array
     * @throws \Exception
     */
    public function add_subscriber($list_id, $email, $first_name = '', $last_name = '', $properties = [])
    {
        /** @var array $payload_properties @see https://www.klaviyo.com/docs/http-api#people eg there is $phone_number */
        $properties = array_replace(['$first_name' => $first_name, '$last_name' => $last_name], $properties);

        $body = array_merge(['email' => $email], $properties);

        $body = array_filter($body, function ($value) {
            return ! is_null($value) && ! empty($value);
        });

        $payload = [
            'profiles' => [$body]
        ];

        $response = $this->make_request("list/$list_id/subscribe", $payload, 'post');

        return $response;
    }

    /**
     * @param string $name The name of the email template.
     * @param string $html The HTML content for this template.
     *
     * @return array
     *
     * @throws \Exception
     */
    public function create_template($name, $html)
    {
        if (empty($name) || empty($html)) {
            throw new \Exception('name or html parameter is missing');
        }

        $payload = ['name' => $name, 'html' => $html];

        $response = $this->make_request('email-templates', $payload, 'post');

        return $response;
    }

    /**
     * @param string $template_id
     *
     * @return array
     *
     * @throws \Exception
     */
    public function delete_template($template_id)
    {
        if (empty($template_id)) {
            throw new \Exception('Template ID is missing');
        }

        $response = $this->make_request("email-template/{$template_id}", [], 'delete');

        return $response;
    }

    /**
     * @param array $payload {
     *
     * @type string $list_id
     * @type string $template_id
     * @type string $from_email
     * @type string $from_name
     * @type string $subject
     * @type string $name (optional)
     * @type string $use_smart_sending (optional)
     * @type string $add_google_analytics (optional)
     * }
     *
     * @return array
     *
     * @throws \Exception
     */
    public function create_campaign($payload)
    {
        $required = ['list_id', 'template_id', 'from_email', 'from_name', 'subject'];

        if (count(array_intersect($required, array_keys($payload))) !== 5) {
            throw new \Exception('missing one or more of the required parameters: ' . implode(', ', $required));
        }

        $payload = array_filter($payload, function ($value) {
            return ! empty($value);
        });

        $response = $this->make_request('campaigns', $payload, 'post');

        return $response;

    }

    /**
     * @param $campaign_id
     *
     * @return array
     * @throws \Exception
     */
    public function send_immediately($campaign_id)
    {
        if (empty($campaign_id)) {
            throw new \Exception('Campaign ID is required');
        }

        $response = $this->make_request("campaign/$campaign_id/send", [], 'post');

        return $response;
    }
}