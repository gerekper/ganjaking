<?php

namespace MailOptin\WordPressUserRegistrationConnect;

use MailOptin\Core\Connections\AbstractConnect;

class Subscription extends AbstractConnect
{
    public $email;
    public $name;
    public $role;
    public $extras;

    public function __construct($email, $name, $role, $extras)
    {
        $this->email  = $email;
        $this->name   = $name;
        $this->role   = $role;
        $this->extras = $extras;

        parent::__construct();
    }

    public function subscribe()
    {
        $name_split = self::get_first_last_names($this->name);

        $user_fields = [];

        $custom_field_mappings = $this->form_custom_field_mappings();

        if (is_array($custom_field_mappings) && ! empty($custom_field_mappings)) {

            foreach ($custom_field_mappings as $wordpressUserFieldKey => $customFieldKey) {
                $value = $this->extras[$customFieldKey];

                $user_fields[$wordpressUserFieldKey] = esc_html($value);
            }
        }

        $user_data = array_filter($user_fields, [$this, 'data_filter']);

        if ( ! isset($user_data['user_login']) || empty($user_data['user_login'])) {
            $user_data['user_login'] = $this->email;
        }

        if ( ! isset($user_data['user_pass']) || empty($user_data['user_pass'])) {
            $user_data['user_pass'] = wp_generate_password(12, false);
        }

        $lead_data = [
            'user_email' => $this->email,
            'first_name' => $name_split[0],
            'last_name'  => $name_split[1],
            'role'       => $this->role == 'administrator' ? '' : $this->role,
        ];

        $lead_data = array_merge($lead_data, $user_data);

        $lead_data = apply_filters('mo_connections_wordpress_user_registration_parameters', $lead_data, $this);

        $lead_data = array_filter($lead_data, [$this, 'data_filter']);

        $response = wp_insert_user($lead_data);

        if (is_wp_error($response)) {
            return parent::ajax_failure($response->get_error_message());
        }

        wp_send_new_user_notifications($response);

        return parent::ajax_success();
    }
}