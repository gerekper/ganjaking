<?php

namespace MailOptin\GEMConnect;

class APIClass
{
    protected $gem_email;

    protected $api_key;

    protected $api_url;

    /**
     * @var int
     */
    protected $api_version = '3';
    /**
     * @var string
     */
    protected $api_url_base = 'https://gem.godaddy.com:443/api/';


    public function __construct($api_key, $gem_email)
    {
        $this->api_key   = $api_key;
        $this->gem_email = $gem_email;
        $this->api_url   = $this->api_url_base . 'v' . $this->api_version . '/';
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
        $url = add_query_arg(['username' => $this->gem_email, 'api_key' => $this->api_key], $this->api_url . $endpoint);

        $wp_args = [
            'method'  => strtoupper($method),
            'timeout' => 30,
            'headers' => ["Content-Type" => "application/json", 'X-WebsiteId' => 'MailOptin Plugin']
        ];

        switch ($method) {
            case 'get':
                $url = add_query_arg($args, $url);
                break;
            default:
                $wp_args['body'] = json_encode($args);
        }

        $response = wp_remote_request($url, $wp_args);

        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        $response_http_code = wp_remote_retrieve_response_code($response);

        $response_body = wp_remote_retrieve_body($response);

        if ($response_http_code >= 200 && $response_http_code <= 299) {
            $response_body = json_decode(wp_remote_retrieve_body($response));
        }

        return ['status' => $response_http_code, 'body' => $response_body];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function get_lists()
    {
        return $this->make_request('subscriberLists');
    }

    /**
     * @param $list_id
     * @param $email
     * @param $first_name
     * @param $last_name
     * @param $custom_fields
     *
     * @return array
     * @throws \Exception
     */
    public function add_subscriber($email, $first_name = '', $last_name = '', $custom_fields = [])
    {
        $payload = $custom_fields + [
                'email'     => $email,
                'firstName' => $first_name,
                'lastName'  => $last_name,
            ];

        $payload = array_filter($payload, function ($value) {
            return ! empty($value);
        });

        return $this->make_request("subscribers", $payload, 'post');
    }

    public function add_subscriber_to_list($subscriber_id, $list_id)
    {
        $payload = [
            'add' => [
                $list_id
            ]
        ];

        return $this->make_request(sprintf('subscribers/%s/memberships', $subscriber_id), $payload, 'put');
    }
}