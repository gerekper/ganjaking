<?php

namespace MailOptin\ActiveCampaignConnect;

use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;

class Subscription extends AbstractActiveCampaignConnect
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

            $lead_tags = $this->get_integration_tags('ActiveCampaignConnect_lead_tags');

            if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                $gdpr_tag  = apply_filters('mo_connections_activecampaign_acceptance_tag', 'gdpr');
                $lead_tags = "{$gdpr_tag}," . $lead_tags;
            }

            $subscription_form = $this->get_integration_data('ActiveCampaignConnect_form');

            $list_id = $this->list_id;

            $ip_address = \MailOptin\Core\get_ip_address();

            $contact = array(
                "email"                         => $this->email,
                "first_name"                    => $name_split[0],
                "last_name"                     => $name_split[1],
                "p[{$list_id}]"                 => $list_id,
                "ip4"                           => $ip_address,
                "status[{$list_id}]"            => 1, // "Active" status
                "instantresponders[{$list_id}]" => 1,
                'form'                          => absint($subscription_form),
                'tags'                          => $lead_tags
            );

            if ( ! filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                unset($contact['ip4']);
            }

            $custom_field_mappings = $this->form_custom_field_mappings();
            $list_custom_fields    = $this->connectInstance->get_optin_fields($this->list_id);

            if (is_array($custom_field_mappings) && is_array($list_custom_fields)) {
                $intersect_result = array_intersect(array_keys($custom_field_mappings), array_keys($list_custom_fields));

                if ( ! empty($intersect_result) && ! empty($custom_field_mappings)) {

                    foreach ($custom_field_mappings as $ActiveCampaignFieldKey => $customFieldKey) {
                        // we are checking if $customFieldKey is not empty because if a merge field doesn't have a custom field
                        // selected for it, the default "Select..." value is empty ("")
                        if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                            $value = $this->extras[$customFieldKey];
                            // note for date field, AC accept one in this format 2020-01-31 which is the default for pikaday
                            if (is_array($value)) {
                                $value = implode(', ', $value);
                            }
                            $value = esc_html($value);
                            switch ($ActiveCampaignFieldKey) {
                                case 'phone':
                                    $contact['phone'] = $value;
                                    break;
                                case 'orgname':
                                    $contact['orgname'] = $value;
                                    break;
                                default :
                                    $contact[sprintf('field[%s,0]', $ActiveCampaignFieldKey)] = $value;
                                    break;
                            }
                        }
                    }
                }
            }

            $contact = array_filter($contact, [$this, 'data_filter']);

            $response = $this->activecampaign_instance()->api("contact/sync", $contact);

            if (1 == $response->result_code) {
                return parent::ajax_success();
            }

            self::save_optin_error_log($response->result_message, 'activecampaign', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'activecampaign', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}