<?php

namespace MailOptin\JiltConnect;

class Subscription extends AbstractJiltConnect
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

            $customer_data = [
                'email'             => $this->email,
                'first_name'        => $name_split[0],
                'last_name'         => $name_split[1],
                'accepts_marketing' => true
            ];

            $list_id = $this->get_integration_data('JiltConnect_shop_lists');

            if ( ! empty($list_id)) {
                $customer_data['list_ids'] = [$list_id];
            }

            $lead_tags = $this->get_integration_tags('JiltConnect_lead_tags');

            if ( ! empty($lead_tags)) {
                $customer_data['tags'] = array_map('trim', explode(',', $lead_tags));
            }

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {

                foreach ($custom_field_mappings as $JLKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesn't have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];

                        if (is_array($value)) $value = implode(', ', $value);

                        $customer_data[$JLKey] = $value;
                    }
                }
            }

            $response = $this->jiltInstance()->addSubscriber($this->list_id, $this->email, array_filter($customer_data, [$this, 'data_filter']));

            if (isset($response->id) && ! empty($response->id)) {
                return parent::ajax_success();
            }

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'jilt', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}