<?php

namespace MailOptin\MailsterConnect;

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
     * True double optin is not disabled.
     *
     * @return bool
     */
    public function is_double_optin()
    {
        $optin_campaign_id = absint($this->extras['optin_campaign_id']);

        $setting = $this->get_integration_data('MailsterConnect_disable_double_optin');

        //external forms
        if($optin_campaign_id == 0) {
            $setting = $this->extras['is_double_optin'];
        }

        $val = ($setting !== true);

        return apply_filters('mo_connections_mailster_is_double_optin', $val, $optin_campaign_id);
    }

    /**
     * @return mixed
     */
    public function subscribe()
    {
        try {
            $name_split = self::get_first_last_names($this->name);

            $double_opt_in = $this->is_double_optin();

            $subscriber_data = [
                'email'     => $this->email,
                'firstname' => $name_split[0],
                'lastname'  => $name_split[1],
                'referer'   => $this->extras['referrer'],
                'status'    => $double_opt_in ? 0 : 1
            ];

            if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                $subscriber_data['gdpr'] = time();
            }

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {
                foreach ($custom_field_mappings as $MailsterFieldKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesnt have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        // date field just works. no multi-select support
                        $value = $this->extras[$customFieldKey];
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        $subscriber_data[$MailsterFieldKey] = esc_attr($value);
                    }
                }
            }

            $subscriber_data = array_filter($subscriber_data, [$this, 'data_filter']);

            $subscriber_id = mailster('subscribers')->add($subscriber_data, true);

            if (is_wp_error($subscriber_id)) {
                /** @var \WP_Error $subscriber_id */
                return self::save_optin_error_log($subscriber_id->get_error_message(), 'mailster', $this->extras['optin_campaign_id']);
            }

            mailster('subscribers')->assign_lists($subscriber_id, [$this->list_id]);

            return parent::ajax_success();

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'mailster', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}