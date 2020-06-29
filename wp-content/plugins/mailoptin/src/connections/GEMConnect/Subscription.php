<?php

namespace MailOptin\GEMConnect;

class Subscription extends AbstractGEMConnect
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

            $custom_field_mappings = $this->form_custom_field_mappings();
            $list_custom_fields    = $this->connectInstance->get_optin_fields($this->list_id);

            if (is_array($custom_field_mappings) && is_array($list_custom_fields)) {
                $intersect_result = array_intersect(array_keys($custom_field_mappings), array_keys($list_custom_fields));

                if ( ! empty($intersect_result) && ! empty($custom_field_mappings)) {
                    foreach ($custom_field_mappings as $GEMFieldKey => $customFieldKey) {
                        // we are checking if $customFieldKey is not empty because if a merge field doesnt have a custom field
                        // selected for it, the default "Select..." value is empty ("")
                        if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                            $value = $this->extras[$customFieldKey];
                            if (is_array($value)) {
                                $value = implode(', ', $value);
                            }
                            $custom_field[$GEMFieldKey] = esc_attr($value);
                        }
                    }
                }
            }

            $response = $this->gem_instance()->add_subscriber(
                $this->email,
                $name_split[0],
                $name_split[1],
                $custom_field
            );

            if (parent::is_http_code_success($response['status'])) {

                if ($response['body']->subscriber->id) {
                    $result = $this->gem_instance()->add_subscriber_to_list($response['body']->subscriber->id, $this->list_id);
                    if (parent::is_http_code_success($result['status'])) return parent::ajax_success();
                }

                return parent::ajax_success();
            }

            if (is_object($response['body'])) {
                self::save_optin_error_log(json_encode($response['body']), 'gem', $this->extras['optin_campaign_id']);
            }

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'gem', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}