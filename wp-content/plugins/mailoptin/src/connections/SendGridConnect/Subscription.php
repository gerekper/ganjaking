<?php

namespace MailOptin\SendGridConnect;

use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;
use function MailOptin\Core\strtotime_utc;

class Subscription extends AbstractSendGridConnect
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
                'contacts' => [
                    [
                        'email'      => $this->email,
                        'first_name' => $name_split[0],
                        'last_name'  => $name_split[1]
                    ]
                ]
            ];

            if ($this->list_id != 'none') {
                $lead_data['list_ids'] = [$this->list_id];
            }

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {
                foreach ($custom_field_mappings as $SendGridFieldKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesnt have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        // date field just works. no multi-select support
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }

                        if (OCR::get_custom_field_type_by_id($customFieldKey, $this->extras['optin_campaign_id']) == 'date') {
                            $value = gmdate('Y-m-d\TH:i:s\Z', strtotime_utc($value));
                        }

                        // https://github.com/sendgrid/sendgrid-nodejs/issues/953
                        if (strpos($SendGridFieldKey, 'sgcf_') !== false) {
                            $SendGridFieldKey                                             = str_replace('sgcf_', '', $SendGridFieldKey);
                            $lead_data['contacts'][0]['custom_fields'][$SendGridFieldKey] = $value;
                        } else {
                            $lead_data['contacts'][0][$SendGridFieldKey] = $value;
                        }
                    }
                }
            }

            $lead_data = apply_filters('mo_connections_sendgrid_subscription_parameters', $lead_data, $this);

            $response = $this->sendgrid_instance()->make_request('marketing/contacts', $lead_data, 'put');

            if (self::is_http_code_success($response['status_code'])) {
                return parent::ajax_success();
            }

            self::save_optin_error_log(json_encode($response['body']), 'sendgrid', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {

            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'sendgrid', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}