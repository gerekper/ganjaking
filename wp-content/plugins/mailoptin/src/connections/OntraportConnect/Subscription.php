<?php

namespace MailOptin\OntraportConnect;

use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;
use function MailOptin\Core\strtotime_utc;

class Subscription extends AbstractOntraportConnect
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
            //Prepare subscriber data
            $name_split = self::get_first_last_names($this->name);
            $data       = [
                "firstname" => $name_split[0],
                "lastname"  => $name_split[1],
                "email"     => $this->email
            ];

            $subscriber_tags = $this->get_integration_tags('OntraportConnect_subscriber_tags');

            $subscriber_tags = array_map('absint', $subscriber_tags);

            //Add custom fields
            $custom_field_mappings = $this->form_custom_field_mappings();
            if ( ! empty($custom_field_mappings)) {

                foreach ($custom_field_mappings as $OntraportKey => $customFieldKey) {

                    // we are checking if $customFieldKey is not empty because if a merge field doesn't have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];

                        if (OCR::get_custom_field_type_by_id($customFieldKey, $this->extras['optin_campaign_id']) == 'date') {
                            $data[$OntraportKey] = strtotime_utc($value);
                            continue;
                        }

                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        $data[$OntraportKey] = esc_html($value);
                    }
                }
            }

            $data = array_filter($data, [$this, 'data_filter']);

            $response = $this->ontraportInstance()->add_subscriber($data);

            if (is_array($response) && (isset($response['id']) || isset($response['attrs']['id']))) {

                $contact_id = isset($response['id']) ? $response['id'] : $response['attrs']['id'];

                if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                    $gdpr_tag = apply_filters('mo_connections_ontraport_acceptance_tag', 'GDPR');
                    try {
                        $result = $this->ontraportInstance()->make_request('Tags/saveorupdate',
                            ['tag_name' => $gdpr_tag],
                            'post'
                        );

                        if (isset($result['data']['tag_id'])) {
                            $subscriber_tags[] = $result['data']['tag_id'];
                        }

                    } catch (\Exception $e) {
                    }
                }

                if ( ! empty($subscriber_tags)) {
                    try {
                        $this->ontraportInstance()->add_tags($contact_id, $subscriber_tags);
                    } catch (\Exception $e) {
                    }
                }

                return parent::ajax_success();
            }

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {

            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'ontraport', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}