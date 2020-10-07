<?php

namespace MailOptin\InfusionsoftConnect;

use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;
use function MailOptin\Core\get_ip_address;
use function MailOptin\Core\strtotime_utc;

class Subscription extends AbstractInfusionsoftConnect
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

            //Contact properties ... name/email etc
            $name_split = self::get_first_last_names($this->name);

            $optin_reason = esc_html__("Customer opted-in through MailOptin form", 'mailoptin');

            $owner_id    = $this->get_integration_data('InfusionsoftConnect_lead_owner');
            $person_type = $this->get_integration_data('InfusionsoftConnect_contact_type');
            $lead_source = $this->get_integration_data('InfusionsoftConnect_lead_source');

            $properties = [
                'given_name'       => $name_split[0],
                'family_name'      => $name_split[1],
                "duplicate_option" => "Email",
                "source_type"      => "WEBFORM",
                'origin'           => ['ip_address' => get_ip_address()],
                "opt_in_reason"    => apply_filters('mo_connections_infusionsoft_opt_in_reason', $optin_reason),
            ];

            if ( ! empty($person_type)) {
                $properties['contact_type'] = $person_type;
            }

            if ( ! empty($lead_source)) {
                $properties['lead_source_id'] = absint($lead_source);
            }

            if ( ! empty($owner_id)) {
                $properties['owner_id'] = absint($owner_id);
            }

            $properties['email_addresses'][] = [
                'email' => $this->email,
                'field' => 'EMAIL1'
            ];

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {

                foreach ($custom_field_mappings as $ISKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesn't have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];

                        if (OCR::get_custom_field_type_by_id($customFieldKey, $this->extras['optin_campaign_id']) == 'date') {
                            $value = gmdate('Y-m-d\TH:i:s\Z', strtotime_utc($value));
                        }

                        if (OCR::get_custom_field_type_by_id($customFieldKey, $this->extras['optin_campaign_id']) == 'checkbox') {
                            // from my test, it also accepts array of options eg ['Hello', 'Hi']
                            $value = is_array($value) ? implode(',', $value) : $value;
                        }
                        // ensures any value that gets here that is an array becomes a string
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }

                        // email1 transformer
                        if ($ISKey == 'email_address_2') {
                            $properties['email_addresses'][] = [
                                'email' => $value,
                                'field' => 'EMAIL2'
                            ];
                            continue;
                        }

                        if ($ISKey == 'email_address_3') {
                            $properties['email_addresses'][] = [
                                'email' => $value,
                                'field' => 'EMAIL3'
                            ];
                            continue;
                        }

                        // phone number transformer
                        if ($ISKey == 'mophne_phone_1') {
                            $properties['phone_numbers'][0]['field']  = 'PHONE1';
                            $properties['phone_numbers'][0]['number'] = $value;
                            continue;
                        }

                        if ($ISKey == 'mophne_phone_1_ext') {
                            $properties['phone_numbers'][0]['extension'] = $value;
                            continue;
                        }

                        if ($ISKey == 'mophne_phone_2') {
                            $properties['phone_numbers'][1]['field']  = 'PHONE2';
                            $properties['phone_numbers'][1]['number'] = $value;
                            continue;
                        }

                        if ($ISKey == 'mophne_phone_2_ext') {
                            $properties['phone_numbers'][1]['extension'] = $value;
                            continue;
                        }

                        // billing address transformer
                        if (false !== strpos($ISKey, 'moblla')) {
                            $fieldID = str_replace('moblla_address_', '', $ISKey);
                            $fieldID = str_replace(['city', 'state', 'country'], ['locality', 'region', 'country_code'], $fieldID);

                            // Contacts doesn't get added if region/state is lowercase.
                            if ($fieldID == 'region') {
                                $value = ucwords($value);
                            }

                            $properties['addresses'][0]['field']  = 'BILLING';
                            $properties['addresses'][0][$fieldID] = $value;
                            continue;
                        }

                        // shipping address transformer
                        if (false !== strpos($ISKey, 'moshpa')) {
                            $fieldID = str_replace('moshpa_address_', '', $ISKey);
                            $fieldID = str_replace(['city', 'state', 'country'], ['locality', 'region', 'country_code'], $fieldID);

                            if ($fieldID == 'region') {
                                $value = ucwords($value);
                            }

                            $properties['addresses'][1]['field']  = 'SHIPPING';
                            $properties['addresses'][1][$fieldID] = $value;
                            continue;
                        }

                        // other address transformer
                        if (false !== strpos($ISKey, 'motha')) {
                            $fieldID = str_replace('motha_address_', '', $ISKey);
                            $fieldID = str_replace(['city', 'state', 'country'], ['locality', 'region', 'country_code'], $fieldID);

                            if ($fieldID == 'region') {
                                $value = ucwords($value);
                            }

                            $properties['addresses'][2]['field']  = 'OTHER';
                            $properties['addresses'][2][$fieldID] = $value;
                            continue;
                        }

                        // social network transformer
                        if (false !== strpos($ISKey, 'mosonk')) {
                            $fieldID = str_replace('Linkedin', 'LinkedIn', ucwords(str_replace('mosonk_', '', $ISKey)));

                            $properties['social_accounts'][] = ['name' => $value, 'type' => $fieldID];
                            continue;
                        }

                        // custom fields transformer
                        if (false !== strpos($ISKey, 'cufd_')) {
                            $fieldID = absint(str_replace('cufd_', '', $ISKey));

                            $properties['custom_fields'][] = ['content' => $value, 'id' => $fieldID];
                            continue;
                        }

                        $properties[$ISKey] = $value;
                    }
                }
            }

            // reindex array so json_encode will convert to array properly instead of numeric indexed object.
            // see https://stackoverflow.com/a/20373067/2648410
            if (isset($properties['phone_numbers'])) {
                $properties['phone_numbers'] = array_values($properties['phone_numbers']);
            }

            if (isset($properties['addresses'])) {
                $properties['addresses'] = array_values($properties['addresses']);
            }

            $properties = apply_filters('mo_connections_infusionsoft_optin_payload', array_filter($properties, [$this, 'data_filter']));

            $response = $this->infusionsoftInstance()->addUpdateSubscriber($properties);

            if (isset($response->id)) {

                $tags = $this->get_integration_tags('InfusionsoftConnect_lead_tags');

                $tags = array_map('absint', $tags);

                if (is_array($tags) && ! empty($tags)) {
                    $this->infusionsoftInstance()->apply_tags($response->id, $tags);
                }

                return parent::ajax_success();
            }

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {

            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'infusionsoft', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}