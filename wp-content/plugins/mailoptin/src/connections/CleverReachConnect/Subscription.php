<?php

namespace MailOptin\CleverReachConnect;

use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;
use function MailOptin\Core\get_ip_address;
use function MailOptin\Core\strtotime_utc;

class Subscription extends AbstractCleverReachConnect
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

            $name_split = self::get_first_last_names($this->name);

            $subscriber_data = ['email' => $this->email];

            $firstname_key = $this->get_first_name_field();
            $lastname_key  = $this->get_last_name_field();

            if ( ! empty($lastname_key)) {
                $subscriber_data['global_attributes'][str_replace("global_attr_", "", $lastname_key)] = $name_split[1];
            }

            if ( ! empty($firstname_key)) {
                $subscriber_data['global_attributes'][str_replace("global_attr_", "", $firstname_key)] = $name_split[0];
            }

            $group_id = $this->list_id;

            $lead_tags = $this->get_integration_data('CleverReachConnect_lead_tags');

            if ( ! empty($lead_tags)) {
                $subscriber_data['tags'] = array_map('trim', explode(',', $lead_tags));
            }

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {

                foreach ($custom_field_mappings as $key => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesn't have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    //key signifies either its global attributes or normal attributes
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        if (is_array($value)) $value = implode(', ', $value);

                        if (strpos($key, 'global_attr_') !== false) {
                            $key                                        = str_replace("global_attr_", "", $key);
                            $subscriber_data['global_attributes'][$key] = $value;
                        } else {
                            $key                                 = str_replace("attr_", "", $key);
                            $subscriber_data['attributes'][$key] = $value;
                        }
                    }
                }
            }

            $response = $this->cleverreachInstance()->addSubscriber($group_id, $this->email, array_filter($subscriber_data, [$this, 'data_filter']));

            if (isset($response->id) && ! empty($response->id)) {
                return parent::ajax_success();
            }

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'cleverreach', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}