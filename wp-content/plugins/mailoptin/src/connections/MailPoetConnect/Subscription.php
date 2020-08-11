<?php

namespace MailOptin\MailPoetConnect;

use MailOptin\Core\Connections\AbstractConnect;

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
     * True welcome message to new subscribers isn't disabled.
     *
     * @return bool
     */
    public function is_welcome_message()
    {
        $setting = $this->get_integration_data('MailPoetConnect_disable_schedule_welcome_email');

        return $setting !== true;
    }

    /**
     * @return mixed
     */
    public function subscribe()
    {
        try {
            $name_split = self::get_first_last_names($this->name);

            $subscriber_data = array_filter(
                [
                    'email'      => $this->email,
                    'first_name' => $name_split[0],
                    'last_name'  => $name_split[1]
                ],
                [$this, 'data_filter']
            );

            $mailpoet_api = \MailPoet\API\API::MP('v1');

            if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                $gdpr_tag = apply_filters('mo_connections_mailpoet_acceptance_tag', 'GDPR');

                $custom_fields = \MailPoet\API\API::MP('v1')->getSubscriberFields();

                $gdpr_field_id = false;
                foreach ($custom_fields as $field) {
                    if ($field['name'] == $gdpr_tag) {
                        $gdpr_field_id = $field['id'];
                        break;
                    }
                }

                if ( ! $gdpr_field_id) {
                    $result        = $mailpoet_api->addSubscriberField(['type' => 'text', 'name' => $gdpr_tag]);
                    $gdpr_field_id = 'cf_' . $result['id'];
                }

                $subscriber_data[$gdpr_field_id] = current_time('mysql');
            }

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {
                foreach ($custom_field_mappings as $MailerPoetFieldKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesnt have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        // For date field, it accept one in this format 2020-01-31 which is the default for pikaday.
                        // doesnt support multi-select fields.
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        $subscriber_data[$MailerPoetFieldKey] = esc_attr($value);
                    }
                }
            }

            $options = ['schedule_welcome_email' => $this->is_welcome_message()];

            $list_id = absint($this->list_id);

            $subscriber_exist = false;

            try {
                // Check if subscriber exists. If subscriber doesn't exist an exception is thrown
                $subscriber_exist = $mailpoet_api->getSubscriber($this->email);
            } catch (\Exception $e) {
            }

            if ($subscriber_exist) {
                \MailPoet\API\API::MP('v1')->subscribeToList($this->email, $list_id, $options);

            } else {
                \MailPoet\API\API::MP('v1')->addSubscriber($subscriber_data, [$list_id], $options);
            }

            return parent::ajax_success();

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'mailpoet', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}