<?php

namespace MailOptin\SendFoxConnect;

class Subscription extends AbstractSendFoxConnect
{
    public $email;
    public $name;
    public $list_id;
    public $extras;

    public function __construct($email, $name, $list_id, $extras)
    {
        $this->email   = $email;
        $this->name    = $name;
        $this->list_id = $list_id;
        $this->extras  = $extras;

        parent::__construct();
    }

    public function subscribe()
    {
        $name_split = self::get_first_last_names($this->name);

        try {

            $lead_data = [
                'email'      => $this->email,
                'first_name' => $name_split[0],
                'last_name'  => $name_split[1],
                'lists' => $this->list_id,
            ];

            $lead_data = apply_filters('mo_connections_sendfox_subscription_parameters', $lead_data, $this);

            $lead_data = array_filter($lead_data, [$this, 'data_filter']);

            $response = $this->sendfox_instance()->make_request('contacts', $lead_data, 'post');

            if (self::is_http_code_success($response['status_code']) && isset($response['body']['id'])) {
                return parent::ajax_success();
            }

            self::save_optin_error_log(json_encode($response['body']), 'sendfox', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {

            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'sendfox', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}