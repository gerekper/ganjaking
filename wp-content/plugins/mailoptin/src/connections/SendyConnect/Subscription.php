<?php

namespace MailOptin\SendyConnect;

class Subscription extends AbstractSendyConnect
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
     * @return array
     */
    public function subscribe()
    {
        try {
            $api_config = parent::api_config();

            $config = array(
                'api_key'          => $api_config['api_key'],
                'installation_url' => $api_config['installation_url'],
                // this is redundant because it isn't used by create campaign. added to prevent any exception being thrown.
                'list_id'          => $this->list_id
            );

            $sendy = new SendyPHP($config);

            $lead_data = array(
                'name'      => $this->name,
                'email'     => $this->email,
                'ipaddress' => \MailOptin\Core\get_ip_address(),
                'referrer'  => $this->extras['conversion_page'],
            );

            if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                $lead_data['gdpr'] = 'true';
            }

            $form_custom_fields = $this->form_custom_fields();

            if ( ! empty($form_custom_fields)) {
                foreach ($form_custom_fields as $customField) {
                    $customFieldKey = $customField['cid'];
                    $placeholder    = $customField['placeholder'];
                    // we are checking if $customFieldKey is not empty because if a merge field doesnt have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        $lead_data[$placeholder] = esc_attr($value);
                    }
                }
            }

            $lead_data = array_filter($lead_data, [$this, 'data_filter']);

            $response = $sendy->subscribe($lead_data);

            // return true if $response['status'] is true otherwise false.
            if (isset($response['status']) && $response['status']) {
                return parent::ajax_success();
            }

            self::save_optin_error_log($response['message'], 'sendy', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'sendy', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}