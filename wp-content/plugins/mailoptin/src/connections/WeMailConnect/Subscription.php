<?php

namespace MailOptin\WeMailConnect;

class Subscription extends AbstractWeMailConnect
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

        $lead_tags = $this->get_integration_tags('WeMailConnect_lead_tags');

        try {

            $lead_data = [
                'email'      => $this->email,
                'first_name' => $name_split[0],
                'last_name'  => $name_split[1],
                'lists'      => $this->list_id,
                'tags'       => explode(',', $lead_tags)
            ];

            $custom_field_mappings = $this->form_custom_field_mappings();

            if (is_array($custom_field_mappings) && ! empty($custom_field_mappings)) {

                foreach ($custom_field_mappings as $weMailFieldKey => $customFieldKey) {
                    $value = $this->extras[$customFieldKey];

                    $lead_data[$weMailFieldKey] = esc_html($value);
                }
            }

            $lead_data = apply_filters('mo_connections_wemail_subscription_parameters', $lead_data, $this);

            $lead_data = array_filter($lead_data, [$this, 'data_filter']);

            $response = $this->wemail_instance()->make_request("lists/$this->list_id/subscribers", $lead_data, 'post');

            if (self::is_http_code_success($response['status_code']) && isset($response['body']['data'])) {
                return parent::ajax_success();
            }

            self::save_optin_error_log(json_encode($response['body']), 'wemail', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {

            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'wemail', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}