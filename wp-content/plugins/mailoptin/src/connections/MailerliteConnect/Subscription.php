<?php

namespace MailOptin\MailerliteConnect;

class Subscription extends AbstractMailerliteConnect
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

            $lead_data = [
                'email'       => $this->email,
                'name'        => $name_split[0],
                'fields'      => ['last_name' => $name_split[1]],
                'resubscribe' => true
            ];

            if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                $gdpr_tag   = apply_filters('mo_connections_mailerlite_acceptance_tag', 'GDPR');
                $get_fields = $this->mailerlite_instance()->fields()->get()->toArray();

                $found_flag = false;
                if (is_array($get_fields)) {
                    foreach ($get_fields as $item) {
                        if ($item->title == $gdpr_tag) {
                            $found_flag = true;
                            break;
                        }
                    }
                }

                if ( ! $found_flag) $this->mailerlite_instance()->fields()->create(['title' => $gdpr_tag]);

                $lead_data['fields'][strtolower($gdpr_tag)] = 'true';
            }

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {
                foreach ($custom_field_mappings as $MailerLiteFieldKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesnt have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        // note for date field, ML accept one in this format 2020-01-31 which is the default for pikaday. No multiselect field.
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        $lead_data['fields'][$MailerLiteFieldKey] = esc_attr($value);
                    }
                }
            }

            $lead_data = array_filter($lead_data, [$this, 'data_filter']);

            $response = $this->mailerlite_instance()->groups()->addSubscriber($this->list_id, $lead_data);

            if (isset($response->id)) {
                return parent::ajax_success();
            }

            self::save_optin_error_log(json_encode($response), 'mailerlite', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {

            // if already subscribed, return success.
            if (strpos($e->getMessage(), 'Member Exists')) {
                return parent::ajax_success();
            }

            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'mailerlite', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}