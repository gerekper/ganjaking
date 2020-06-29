<?php

namespace MailOptin\EmmaConnect;

use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;

class Subscription extends AbstractEmmaConnect
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

    /**
     * @return mixed
     */
    public function subscribe()
    {
        try {

            $name_split = self::get_first_last_names($this->name);

            $signup_form_id = $this->get_integration_data('EmmaConnect_signup_form');

            $disable_confirmation_email = $this->get_integration_data('EmmaConnect_disable_confirmation_email');

            $lead_data = [
                'fields'              => [
                    'first_name' => $name_split[0],
                    'last_name'  => $name_split[1],
                ],
                'email'               => $this->email,
                'group_ids'           => [$this->list_id],
                'opt_in_confirmation' => ! $disable_confirmation_email
            ];

            if ( ! empty($signup_form_id)) {
                $lead_data['signup_form_id'] = absint($signup_form_id);
            }

            if (isset($this->extras['mo-acceptance'])) {
                if ($this->extras['mo-acceptance'] == 'yes') {
                    $lead_data['subscriber_consent_tracking'] = true;
                } else {
                    $lead_data['subscriber_consent_tracking'] = false;
                }
            }

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {

                foreach ($custom_field_mappings as $EMMAKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesn't have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        // note for date field, Emma accept one in this format 2020-01-31 which is the default for pikaday
                        if (OCR::get_custom_field_type_by_id($customFieldKey, $this->extras['optin_campaign_id']) == 'checkbox') {
                            $lead_data['fields'][$EMMAKey] = $value;
                            continue;
                        }

                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }

                        $lead_data['fields'][$EMMAKey] = esc_html($value);
                    }
                }
            }

            $lead_data['fields'] = array_filter($lead_data['fields'], [$this, 'data_filter']);

            $response = $this->emma_instance()->make_request('members/signup', $lead_data, 'post');

            if (self::is_http_code_success($response['status_code'])) return parent::ajax_success();

            self::save_optin_error_log(json_encode($response['body']->error), 'emma', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {

            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'emma', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}