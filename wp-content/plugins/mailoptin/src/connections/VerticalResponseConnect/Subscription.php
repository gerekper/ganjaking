<?php

namespace MailOptin\VerticalResponseConnect;

class Subscription extends AbstractVerticalResponseConnect
{
    public $email;
    public $name;
    public $list_id;
    public $extras;
    /** @var Connect */
    public $connectInstance;

    public function __construct($email, $name, $list_id, $extras, $connectInstance)
    {
        $this->email           = $email;
        $this->name            = $name;
        $this->list_id         = $list_id;
        $this->extras          = $extras;
        $this->connectInstance = $connectInstance;

        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function subscribe()
    {
        try {

            //Prepare subscriber data
            $name_split = self::get_first_last_names($this->name);
            $extra_data = [
                "first_name" => $name_split[0],
                "last_name"  => $name_split[1],
                "custom"     => [],
            ];

            if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                $gdpr_tag = apply_filters('mo_connections_mailjet_acceptance_tag', 'gdpr');
                try {
                    $headers = ['Content-Type' => 'application/json'];
                    $this->verticalresponseInstance()->apiRequest('custom_fields', 'POST', ['name' => $gdpr_tag], $headers);
                } catch (\Exception $e) {
                }

                $extra_data['custom'][$gdpr_tag] = 'true';
            }

            //Add custom fields
            $custom_field_mappings = $this->form_custom_field_mappings();
            if ( ! empty($custom_field_mappings)) {

                foreach ($custom_field_mappings as $VRKey => $customFieldKey) {
                    if (in_array($VRKey, array_keys(Connect::predefined_custom_fields()))) {
                        $extra_data[$VRKey] = $this->extras[$customFieldKey];
                        continue;
                    }

                    // we are checking if $customFieldKey is not empty because if a merge field doesn't have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        // support text/string data types. no date or any other custom fields.
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        $extra_data['custom'][$VRKey] = esc_attr($value);
                    }
                }
            }

            $extra_data = array_filter($extra_data, [$this, 'data_filter']);

            $response = $this->verticalresponseInstance()->addSubscriber($this->list_id, $this->email, $extra_data);

            if (is_object($response)) {
                return parent::ajax_success();
            }

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            $result = json_decode($e->getMessage(), true);

            if (isset($result['error']['code']) && $result['error']['code'] == 409) {
                return parent::ajax_success();
            }

            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'verticalresponse', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}