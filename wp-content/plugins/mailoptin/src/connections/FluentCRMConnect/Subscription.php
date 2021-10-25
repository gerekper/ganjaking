<?php

namespace MailOptin\FluentCRMConnect;

use MailOptin\Core\Connections\AbstractConnect;
use FluentCrm\App\App;

class Subscription extends AbstractConnect
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
     * True double optin is not disabled.
     *
     * @return bool
     */
    public function is_double_optin()
    {
        $optin_campaign_id = absint($this->extras['optin_campaign_id']);

        $setting = $this->get_integration_data('FluentCRMConnect_disable_double_optin');

        //external forms
        if($optin_campaign_id == 0) {
            $setting = $this->extras['is_double_optin'];
        }

        $val = ($setting !== true);

        return apply_filters('mo_connections_fluentcrm_is_double_optin', $val, $optin_campaign_id);
    }

    /**
     * @return mixed
     */
    public function subscribe()
    {
        try {

            $name_split = self::get_first_last_names($this->name);

            $lead_tags = $this->get_integration_tags('FluentCRMConnect_lead_tags');

            $double_opt_in = $this->is_double_optin();

            $lead_data = array_filter(
                [
                    'email'         => $this->email,
                    'first_name'    => $name_split[0],
                    'last_name'     => $name_split[1],
                    'status'        => ! $double_opt_in ? 'subscribed' : 'pending',
                    'lists'         => [$this->list_id],
                    'tags'          => $lead_tags,
                    'custom_values' => []
                ],
                [$this, 'data_filter']
            );

            $custom_field_mappings = $this->form_custom_field_mappings();

            if (is_array($custom_field_mappings) && ! empty($custom_field_mappings)) {

                foreach ($custom_field_mappings as $fluentcrmFieldKey => $customFieldKey) {

                    $value = $this->extras[$customFieldKey];
                    // For date field, it accept one in this format 2020-01-31 which is the default for pikaday.
                    // doesnt support multi-select fields.
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }

                    if (false !== strpos($fluentcrmFieldKey, 'cf_')) {
                        $fluentcrmFieldKey                              = str_replace('cf_', '', $fluentcrmFieldKey);
                        $lead_data['custom_values'][$fluentcrmFieldKey] = esc_html($value);
                    } else {
                        $lead_data[$fluentcrmFieldKey] = esc_html($value);
                    }
                }
            }

            $contactApi = FluentCrmApi('contacts');

            $lead_data = apply_filters('mo_connections_fluentcrm_subscription_parameters', $lead_data, $this);

            $lead_data = array_filter($lead_data, [$this, 'data_filter']);

            $response = $contactApi->createOrUpdate($lead_data);

            if ($response->status == 'pending') {
                $response->sendDoubleOptinEmail();
            }

            return parent::ajax_success();

        } catch (\Exception $e) {

            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'fluentcrm', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}