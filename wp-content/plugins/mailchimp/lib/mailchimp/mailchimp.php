<?php

class MailChimp_API {

    public $key;
    public $datacenter;

    public function __construct($api_key) {
        $api_key = trim($api_key);
        if(!$api_key) {
            throw new Exception(__('Invalid API Key: ' . $api_key));
        }

        $this->key        = $api_key;
        $dc               = explode('-', $api_key);
        $this->datacenter = empty($dc[1]) ? 'us1' : $dc[1];
        $this->api_url    = 'https://' . $this->datacenter . '.api.mailchimp.com/3.0/';
        return;
    }

    public function get($endpoint, $count = 10, $fields = array())
    {
        $query_params = '';

        $url = $this->api_url . $endpoint;

        if ($count) {
            $query_params = 'count=' . $count . '&';
        }

        if (!empty($fields)) {
            foreach ($fields as $field => $value) {
                $query_params .= $field . '=' . $value . '&';
            }
        }

        if ($query_params) {
            $url .= "?{$query_params}";
        }

        $args = array(
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.1',
            'user-agent'  => 'MailChimp WordPress Plugin/' . get_bloginfo('url'),
            'headers'     => array("Authorization" => 'apikey ' . $this->key)
        );

        $request = wp_remote_get($url, $args);

        if (is_array($request) && $request['response']['code'] == 200) {
            return json_decode($request['body'], true);
        } elseif (is_array($request) && $request['response']['code']) {
            $error = json_decode($request['body'], true);
            $error = new WP_Error('mailchimp-get-error', $error['detail']);
            return $error;
        } else {
            return false;
        }
    }

    public function post($endpoint, $body, $method = 'POST') {
        $url = $this->api_url . $endpoint;
        
        $args = array(
            'method' => $method,
            'timeout' => 5,
            'redirection' => 5,
            'httpversion' => '1.1',
            'user-agent'  => 'MailChimp WordPress Plugin/' . get_bloginfo( 'url' ),
            'headers'     => array("Authorization" => 'apikey ' . $this->key),
            'body' => json_encode($body)
        );
        $request = wp_remote_post($url, $args);

        if(is_array($request) && $request['response']['code'] == 200) {
            return json_decode($request['body'], true);
        } else {
            if(is_wp_error($request)) {
                return new WP_Error('mc-subscribe-error', $request->get_error_message());
            }

            $body = json_decode($request['body'], true);
            $merges = get_option('mc_merge_vars');
            foreach ($merges as $merge) {
                if (empty($body['errors'])) {
                    //Email address doesn't come back from the API, so if something's wrong, it's that.
                    $field_name = 'Email Address';
                    $body['errors'][0]['message'] = 'Please fill out a valid email address.';
                }
                elseif ($merge['tag'] == $body['errors'][0]['field']) {
                    $field_name = $merge['name'];
                }
            }
            $message = sprintf($field_name . ": " . $body['errors'][0]['message']);
            return new WP_Error('mc-subscribe-error-api', $message);
        }
    }
}