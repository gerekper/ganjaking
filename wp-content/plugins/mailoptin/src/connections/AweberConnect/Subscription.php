<?php

namespace MailOptin\AweberConnect;

class Subscription extends AbstractAweberConnect
{
    public $email;
    public $name;
    public $list_id;
    public $extras;
    protected $aweber;
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

            $lead_tags = $this->get_integration_tags('AweberConnect_lead_tags');

            if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                $gdpr_tag  = apply_filters('mo_connections_aweber_acceptance_tag', 'gdpr');
                $lead_tags = "{$gdpr_tag}," . $lead_tags;
            }

            $ip_address = \MailOptin\Core\get_ip_address();

            $payload = [
                'email'           => $this->email,
                'name'            => $this->name,
                'ip_address'      => $ip_address,
                'update_existing' => 'true'
            ];

            if ( ! filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                unset($payload['ip_address']);
            }

            if ( ! empty($lead_tags)) {
                $payload['tags'] = json_encode(array_map('trim', explode(',', $lead_tags)));
            }

            $custom_field_mappings = $this->form_custom_field_mappings();
            $list_custom_fields    = $this->connectInstance->get_optin_fields($this->list_id);

            if (is_array($custom_field_mappings) && is_array($list_custom_fields)) {
                $intersect_result = array_intersect(array_keys($custom_field_mappings), array_keys($list_custom_fields));

                if ( ! empty($intersect_result) && ! empty($custom_field_mappings)) {

                    $data_store_custom_fields = [];

                    foreach ($custom_field_mappings as $AWeberFieldKey => $customFieldKey) {
                        // we are checking if $customFieldKey is not empty because if a merge field doesn't have a custom field
                        // selected for it, the default "Select..." value is empty ("")
                        if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                            $value = $this->extras[$customFieldKey];
                            if (is_array($value)) {
                                $value = implode(', ', $value);
                            }

                            $data_store_custom_fields[$AWeberFieldKey] = esc_attr($value);
                        }
                    }

                    if ( ! empty($data_store_custom_fields)) {
                        $payload['custom_fields'] = json_encode($data_store_custom_fields);
                    }
                }
            }

            $payload = array_filter($payload, [$this, 'data_filter']);

            // save an instance of the Aweber Auth. Necessary to prevent re-instantiation and so we can
            // capture request status code below.
            $this->aweber = $this->aweber_instance();

            $this->aweber->addSubscriber(
                $this->account_id,
                $this->list_id,
                $payload
            );

            if (self::is_http_code_success($this->aweber->httpClient->getResponseHttpCode())) {
                return parent::ajax_success();
            }

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {

            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'aweber', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}