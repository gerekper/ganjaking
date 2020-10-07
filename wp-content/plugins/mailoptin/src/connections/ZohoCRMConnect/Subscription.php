<?php

namespace MailOptin\ZohoCRMConnect;

use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;
use function MailOptin\Core\strtotime_utc;

class Subscription extends AbstractZohoCRMConnect
{
    public $email;
    public $name;
    public $module;
    public $extras;

    public function __construct($email, $name, $module, $extras)
    {
        $this->email  = $email;
        $this->name   = $name;
        $this->module = $module;
        $this->extras = $extras;

        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function subscribe()
    {
        try {

            $name_split = self::get_first_last_names($this->name);

            $payload = [
                'Email'      => $this->email,
                'First_Name' => $name_split[0],
                // Contact creation will fail if there isn't a last name
                'Last_Name'  => empty($name_split[1]) ? 'Unknown' : $name_split[1],
            ];

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {

                foreach ($custom_field_mappings as $ZCRMKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesn't have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        // zoho expect date to be in this format 1999-11-20 which is pikaday default so the code below is redundant. See https://help.zoho.com/portal/community/topic/php-sdk-date-format
                        if (OCR::get_custom_field_type_by_id($customFieldKey, $this->extras['optin_campaign_id']) == 'date') {
                            $value = gmdate('Y-m-d', strtotime_utc($value));
                        }

                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }

                        $payload[$ZCRMKey] = $value;
                    }
                }
            }

            $payload = array_filter($payload, [$this, 'data_filter']);
            $payload = array($payload);

            $owner = $this->get_integration_data('ZohoCRMConnect_contact_owner');
            if ($this->module == 'Leads') {
                $owner = $this->get_integration_data('ZohoCRMConnect_lead_owner');
            }

            if ( ! empty($owner)) {
                $payload[0]['Owner'] = array(
                    'id' => $owner
                );
            }

            $lead_source = $this->get_integration_data('ZohoCRMConnect_contact_lead_source');
            if ($this->module == 'Leads') {
                $lead_source = $this->get_integration_data('ZohoCRMConnect_lead_lead_source');
            }

            if ( ! empty($lead_source)) {
                $payload[0]['Lead_Source'] = $lead_source;
            }

            $description = $this->get_integration_data('ZohoCRMConnect_description');

            if ( ! empty($description)) {
                $payload[0]['Description'] = $description;
            }

            $triggers = $this->get_integration_data('ZohoCRMConnect_triggers');
            if (empty($triggers)) $triggers = [];

            $response = $this->zcrmInstance()->apiRequest(
                $this->module,
                'POST',
                ['data' => $payload, 'trigger' => $triggers],
                ['Content-Type' => 'application/json']
            );

            if (isset($response->data[0]->details->id)) {
                $id      = $response->data[0]->details->id;
                $db_tags = $this->get_integration_tags('ZohoCRMConnect_tags');

                $tags = [];

                if ( ! empty($db_tags)) {
                    $tags = array_map('trim', explode(',', $db_tags));
                }

                if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                    $tags[] = apply_filters('mo_connections_zoho_crm_acceptance_tag', 'GDPR');
                }

                if ( ! empty($tags)) {
                    $this->zcrmInstance()->apiRequest(
                        sprintf('%s/%s/actions/add_tags?tag_names=%s', $this->module, $id, implode(',', $tags)),
                        'POST'
                    );
                }

                return parent::ajax_success();
            }

            self::save_optin_error_log(json_encode($response), 'zohocrm', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'zohocrm', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}