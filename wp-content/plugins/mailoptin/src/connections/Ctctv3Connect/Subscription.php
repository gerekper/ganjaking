<?php

namespace MailOptin\Ctctv3Connect;

use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;
use function MailOptin\Core\get_ip_address;
use function MailOptin\Core\strtotime_utc;

class Subscription extends AbstractCtctv3Connect
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

            $properties = [
                'first_name'       => $name_split[0],
                'last_name'        => $name_split[1],
                'email_address'    => $this->email,
                'create_source'    => 'Contact',
                'list_memberships' => [$this->list_id]
            ];

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {

                foreach ($custom_field_mappings as $ISKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesn't have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];

                        if (OCR::get_custom_field_type_by_id($customFieldKey, $this->extras['optin_campaign_id']) == 'checkbox') {
                            // from my test, it also accepts array of options eg ['Hello', 'Hi']
                            $value = is_array($value) ? implode(',', $value) : $value;
                        }
                        // ensures any value that gets here that is an array becomes a string
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }

                        // home address transformer
                        if (false !== strpos($ISKey, 'mohma')) {
                            $fieldID                                = str_replace('mohma_', '', $ISKey);
                            $properties['street_address']['kind']   = 'home';
                            $properties['street_address'][$fieldID] = $value;
                            continue;
                        }

                        // work address transformer
                        if (false !== strpos($ISKey, 'mowka')) {
                            $fieldID                                = str_replace('mowka_', '', $ISKey);
                            $properties['street_address']['kind']   = 'work';
                            $properties['street_address'][$fieldID] = $value;
                            continue;
                        }

                        // other address transformer
                        if (false !== strpos($ISKey, 'moota')) {
                            $fieldID                                = str_replace('moota_', '', $ISKey);
                            $properties['street_address']['kind']   = 'other';
                            $properties['street_address'][$fieldID] = $value;
                            continue;
                        }

                        if (false !== strpos($ISKey, 'cufd_')) {
                            $fieldID = str_replace('cufd_', '', $ISKey);

                            $properties['custom_fields'][] = ['custom_field_id' => $fieldID, 'value' => $value];
                            continue;
                        }

                        $properties[$ISKey] = $value;
                    }
                }
            }

            $properties = apply_filters('mo_connections_constant_contact_v3_optin_payload', array_filter($properties, [$this, 'data_filter']));

            $response = $this->ctctv3Instance()->createOrUpdateContact($properties);

            if (isset($response->contact_id)) {
                return parent::ajax_success();
            }

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'constantcontactv3', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}