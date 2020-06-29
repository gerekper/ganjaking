<?php

namespace MailOptin\EmailOctopusConnect;

class APIClass
{
    protected $api_key;

    protected $api_url;

    /**
     * @var int
     */
    protected $api_version = '1.5';
    /**
     * @var string
     */
    protected $api_url_base = 'https://emailoctopus.com/api/';


    public function __construct($api_key)
    {
        $this->api_key = $api_key;
        $this->api_url = $this->api_url_base . $this->api_version . '/';
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

        $args['api_key'] = $this->api_key;
        $wp_args         = ['method' => strtoupper($method), 'timeout' => 30];

        switch ($method) {
            case 'post':
                $wp_args['headers'] = ["Content-Type" => "application/x-www-form-urlencoded"];
                $wp_args['body']    = $args;
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

    /**
     * @return array
     * @throws \Exception
     */
    public function get_lists()
    {
        return $this->make_request('lists');
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function create_list_field($list_id, $label, $tag, $type = 'TEXT', $fallback = '')
    {
        if ( ! isset($list_id) || ! isset($label) || ! isset($type)) {
            throw new \Exception('Required paramater list_id, label or tag not found');
        }

        return $this->make_request("lists/$list_id/fields", [
            'label'    => $label,
            'tag'      => $tag,
            'type'     => $type,
            'fallback' => $fallback
        ],
            'post'
        );
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
    public function add_subscriber($list_id, $email, $first_name = '', $last_name = '', $custom_fields = [], $status = '')
    {
        $payload = [
            'email_address' => $email,
            'fields'        => [
                'FirstName' => $first_name,
                'LastName'  => $last_name
            ]
        ];

        if ( ! empty($status) && in_array($status, ['SUBSCRIBED', 'UNSUBSCRIBED', 'PENDING'])) {
            $payload['status'] = $status;
        }

        if ( ! empty($custom_fields)) {
            foreach ($custom_fields as $key => $value) {
                $payload['fields'][$key] = $value;
            }
        }

        $payload = array_filter($payload, function ($value) {
            return ! empty($value);
        });

        $response = $this->make_request("lists/{$list_id}/contacts", $payload, 'post');

        return $response;
    }
}