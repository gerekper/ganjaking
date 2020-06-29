<?php

namespace MailOptin\ZohoCampaignsConnect;

use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;
use function MailOptin\Core\strtotime_utc;

class Subscription extends AbstractZohoCampaignsConnect
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

            $email_key     = 'Contact Email';
            $firstname_key = 'First Name';
            $lastname_key  = 'Last Name';
            $response      = $this->zcInstance()->apiRequest('contact/allfields?type=json');

            foreach ($response->response->fieldnames->fieldname as $field) {
                if ($field->FIELD_DISPLAY_NAME == 'CONTACT_EMAIL') {
                    $email_key = $field->DISPLAY_NAME;
                }

                if ($field->FIELD_DISPLAY_NAME == 'FIRSTNAME') {
                    $firstname_key = $field->DISPLAY_NAME;
                }

                if ($field->FIELD_DISPLAY_NAME == 'LASTNAME') {
                    $lastname_key = $field->DISPLAY_NAME;
                }
            }

            $payload = [
                'listkey'     => $this->list_id,
                'contactinfo' => [
                    $email_key     => $this->email,
                    $firstname_key => $name_split[0],
                    $lastname_key  => $name_split[1]
                ]
            ];

            $payload = array_filter($payload, [$this, 'data_filter']);

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {

                foreach ($custom_field_mappings as $ZCKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesn't have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];

                        if (OCR::get_custom_field_type_by_id($customFieldKey, $this->extras['optin_campaign_id']) == 'date') {
                            $payload['contactinfo'][$ZCKey] = gmdate('m/d/Y', strtotime_utc($value));
                            continue;
                        }

                        if (OCR::get_custom_field_type_by_id($customFieldKey, $this->extras['optin_campaign_id']) == 'checkbox') {
                            $payload['contactinfo'][$ZCKey] = implode(';', $value);;
                            continue;
                        }

                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }

                        $payload['contactinfo'][$ZCKey] = $value;
                    }
                }
            }

            $payload['contactinfo'] = json_encode(array_filter($payload['contactinfo'], [$this, 'data_filter']));

            $response = $this->zcInstance()->apiRequest('json/listsubscribe?resfmt=JSON', 'POST', $payload);

            if (isset($response->status) && $response->status == 'success') {
                return parent::ajax_success();
            }

            self::save_optin_error_log(json_encode($response), 'zohocampaigns', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'zohocampaigns', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}