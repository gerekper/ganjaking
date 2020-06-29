<?php

namespace MailOptin\MailjetConnect;

class APIClass
{
    /**
     * The public api key used to identify the mailjet user
     */
    protected $api_key;

    /**
     * The secret api key used to aunthenticate the user to mailjet
     */
    protected $secret_key;

    /**
     * The base url for all api requests
     */
    public $api_url = 'api.mailjet.com/v3/REST/';

    public function __construct($api_key, $secret_key)
    {
        $this->api_key    = $api_key;
        $this->secret_key = $secret_key;
    }

    /**
     * Performs a HTTP request to the mailjet API
     *
     * @param $endpoint
     * @param array $args
     * @param string $method
     *
     * @return array
     * @throws \Exception
     */
    public function make_request($endpoint, $args = [], $method = 'post')
    {
        //In case the user has preceeded the endpoint with a slash, remove it then add it to base url
        $endpoint = ltrim($endpoint, '/');

        //For some reason, sending credentials via an auth header does not work so we add it to the url
        $auth = "$this->api_key:$this->secret_key@";
        $url  = "https://{$auth}{$this->api_url}$endpoint";
        //prepare http args

        $wp_args = [
            'method'  => strtoupper($method),
            'timeout' => 30,
            'headers' => [],
        ];

        switch ($method) {
            case 'post':
                $wp_args['body']                    = json_encode($args);
                $wp_args['headers']['Content-Type'] = 'application/json';
                break;
            case 'get':
                $url = add_query_arg($args, $url);
                break;
        }

        $response = wp_remote_request($url, $wp_args);

        //throw any error returned by the wp http api
        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        //Check if the request was successfully authorized
        if (401 == wp_remote_retrieve_response_code($response)) {
            throw new \Exception(esc_html__('You have specified an incorrect API Key / API Secret Key pair. You may be unauthorized to access the API or your API key may be inactive.', 'mailoptin'));
        }

        $response_body = json_decode(wp_remote_retrieve_body($response));

        //Throw any errors returned by the api
        if ( ! empty($response_body->ErrorMessage) || ! empty($response_body->Errors)) {
            throw new \Exception(print_r($response_body, true));
        }

        //api transaction completed successfuly
        return $response_body;

    }

    /**
     * Returns user defined custom fields
     */
    public function get_custom_fields()
    {
        $fields = $this->make_request('contactmetadata', [], 'get');

        return wp_list_pluck($fields->Data, 'Name', 'Name');
    }

    /**
     * Returns contact lists
     */
    public function get_lists($count = 1000)
    {
        //Prepare args then fetch the lists
        $args  = [
            'Limit'     => $count,
            'IsDeleted' => false

        ];
        $lists = $this->make_request('contactslist', $args, 'get');

        return wp_list_pluck($lists->Data, 'Name', 'ID');
    }
}