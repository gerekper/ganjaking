<?php

namespace MailOptin\CampaignMonitorConnect;

use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;

class Subscription extends AbstractCampaignMonitorConnect
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

            $custom_fields = [];

            $consent = 'Unchanged';

            if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                $consent = 'Yes';
            }

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {

                foreach ($custom_field_mappings as $CMKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesn't have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        // note for date field, CM accept one in this format 2020-01-31 which is the default for pikaday
                        if (OCR::get_custom_field_type_by_id($customFieldKey, $this->extras['optin_campaign_id']) == 'checkbox') {
                            if (is_array($value)) {
                                $custom_fields[$CMKey] = [];
                                foreach ($value as $val) {
                                    $custom_fields[$CMKey][] = esc_html($val);
                                }
                                continue;
                            }
                        }

                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }

                        $custom_fields[$CMKey] = esc_html($value);
                    }
                }
            }

            $response = $this->campaignmonitorInstance()->addSubscriberEmailName($this->list_id, $this->email, $this->name, $custom_fields, $consent);

            if ($response) {
                return parent::ajax_success();
            }

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'campaignmonitor', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}