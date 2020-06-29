<?php

namespace MailOptin\OntraportConnect;

class APIClass
{
    /**
     * The user's api key which is used to authenticate api requests
     */
    protected $api_key;

    /**
     * unique site ID used to identify the user
     */
    protected $app_id;

    /**
     * The base URL for all of the API requests
     */
    protected $api_url = 'https://api.ontraport.com/1/';


    public function __construct($api_key, $app_id)
    {
        $this->api_key = $api_key;
        $this->app_id  = $app_id;
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

        //CURL arguments to use with the api call
        $wp_args = [
            'method'  => strtoupper($method),
            'timeout' => 30,
            'headers' => [
                'Api-Key'   => $this->api_key,
                'Api-Appid' => $this->app_id,
            ]
        ];

        //Object url for the endpoint
        $url = $this->api_url . $endpoint;

        $args['api_key'] = $this->api_key;

        switch ($method) {
            case 'post':
                $wp_args['body']                    = $args;
                $wp_args['headers']['Content-Type'] = 'application/json';
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

        //In case request was not successful...
        if (299 < wp_remote_retrieve_response_code($response)) {
            throw new \Exception(wp_remote_retrieve_body($response));
        }

        /**
         * Every successful API call returns a JSON-formatted response containing:
         * code            integer        Indicates the success or failure of the call. If code is 0, this indicates an HTTP 200 successful call.
         * data            object        Contains object-specific attributes and data
         * account_id    integer        ID of the account making the API call
         */
        $response_body = json_decode(wp_remote_retrieve_body($response), true);


        return $response_body;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function get_fields()
    {
        $response = $this->make_request('Contacts/meta');

        //Was the request successful?
        if (0 != $response['code']) {
            return [];
        }

        //Prepare the fields
        $fields = [];
        foreach ($response['data']['0']['fields'] as $key => $args) {

            if ($args['type'] == 'drop' || $args['type'] == 'parent') {
                continue;
            }

            $fields[$key] = empty($args['alias']) ? $key : $args['alias'];
        }

        unset($fields['email']);
        unset($fields['firstname']);
        unset($fields['lastname']);

        return $fields;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function add_subscriber($args)
    {
        $response = $this->make_request('Contacts/saveorupdate', $args, 'post');

        //Was the request successful?
        if (0 != $response['code']) {
            throw new \Exception(print_r($response, true));
        }

        return $response['data'];

    }

    /**
     * @param array $tags
     *
     * @return array
     * @throws \Exception
     */
    public function add_tags($contact_id, $tags)
    {
        if ( ! is_array($tags)) throw new \Exception('Tags must be array');

        $payload = [
            'objectID' => 0,
            'add_list' => implode(",", $tags),
            'ids'      => $contact_id
        ];

        $response = $this->make_request('objects/tag', $payload, 'PUT');

        //Was the request successful?
        if (0 != $response['code']) {
            throw new \Exception(print_r($response, true));
        }

        return $response['data'];

    }
}