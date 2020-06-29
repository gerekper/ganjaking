<?php

namespace MailOptin\EmailOctopusConnect;

class Subscription extends AbstractEmailOctopusConnect
{
    public $email;
    public $name;
    public $list_id;
    public $extras;
    /** @var Connect */
    protected $connectInstance;

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

            $name_split = self::get_first_last_names($this->name);

            $custom_field = [];
            if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                $gdpr_tag  = apply_filters('mo_connections_emailoctopus_acceptance_tag', 'GDPR');
                $field_key = sanitize_key($gdpr_tag);
                $this->emailoctopus_instance()->create_list_field(
                    $this->list_id,
                    $gdpr_tag,
                    $field_key
                );

                $custom_field = [$field_key => current_time('mysql')];
            }

            // backward compatibility
            $status          = '';
            $is_double_optin = $this->get_integration_data('EmailOctopusConnectConnect_enable_double_optin');
            if (isset($is_double_optin)) {
                $status = $is_double_optin === true ? 'PENDING' : 'SUBSCRIBED';
            }

            $custom_field_mappings = $this->form_custom_field_mappings();
            $list_custom_fields    = $this->connectInstance->get_optin_fields($this->list_id);

            if (is_array($custom_field_mappings) && is_array($list_custom_fields)) {
                $intersect_result = array_intersect(array_keys($custom_field_mappings), array_keys($list_custom_fields));

                if ( ! empty($intersect_result) && ! empty($custom_field_mappings)) {
                    foreach ($custom_field_mappings as $EAFieldKey => $customFieldKey) {
                        // we are checking if $customFieldKey is not empty because if a merge field doesnt have a custom field
                        // selected for it, the default "Select..." value is empty ("")
                        if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                            $value = $this->extras[$customFieldKey];
                            if (is_array($value)) {
                                $value = implode(', ', $value);
                            }
                            $custom_field[$EAFieldKey] = esc_attr($value);
                        }
                    }
                }
            }

            $response = $this->emailoctopus_instance()->add_subscriber(
                $this->list_id,
                $this->email,
                $name_split[0],
                $name_split[1],
                $custom_field,
                $status
            );

            if ($response['status_code'] >= 200 && $response['status_code'] <= 299) {
                return parent::ajax_success();
            }

            $error_code    = $response['body']->error->code;
            $error_message = $response['body']->error->message;

            if ($error_code == 'MEMBER_EXISTS_WITH_EMAIL_ADDRESS') return parent::ajax_success();

            self::save_optin_error_log($error_code . ': ' . $error_message, 'emailoctopus', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'emailoctopus', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}