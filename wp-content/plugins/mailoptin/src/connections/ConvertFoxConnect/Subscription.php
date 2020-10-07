<?php

namespace MailOptin\ConvertFoxConnect;

use MailOptin\Core\Repositories\OptinCampaignsRepository;
use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;
use function MailOptin\Core\moVar;
use function MailOptin\Core\strtotime_utc;

class Subscription extends AbstractConvertFoxConnect
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
     * Record / track event
     *
     * @param $campaign_id
     *
     * @return array
     * @throws \Exception
     */
    public function record_event()
    {
        $result = $this->convertfox_instance()->make_request('events', [
            'event_name' => 'mailoptin_lead',
            'email'      => $this->email,
            'properties' => [
                'optin_campaign' => OptinCampaignsRepository::get_optin_campaign_name(
                    $this->extras['optin_campaign_id']
                ),
                'conversion_url' => moVar($this->extras, 'conversion_page', ''),
                'referrer_url'   => moVar($this->extras, 'referrer', ''),
                'user_agent'     => moVar($this->extras, 'user_agent', '')
            ]
        ],
            'post'
        );

        if (parent::is_http_code_not_success($result['status_code'])) {
            self::save_optin_error_log($this->email . ': ' . json_encode($result['body']->errors), 'convertfox', $this->extras['optin_campaign_id']);
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function subscribe()
    {
        try {

            $lead_tags = $this->get_integration_tags('ConvertFoxConnect_lead_tags');

            $lead_tags = array_map('trim', explode(',', $lead_tags));

            // remove empty array.
            $lead_tags = array_filter($lead_tags, function ($value) {
                return ! empty($value);
            });

            $lead_data = [
                'name'  => $this->name,
                'email' => $this->email,
            ];

            $lead_data['tags'] = $lead_tags;

            if ( ! apply_filters('mo_optin_convertfox_default_tag_disable', false)) {
                // always include mailoptin tag.
                array_push($lead_data['tags'], 'mailoptin');
            }

            if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                $gdpr_tag = apply_filters('mo_connections_convertfox_acceptance_tag', 'gdpr');
                array_push($lead_data['tags'], $gdpr_tag);
            }

            $custom_fields_bucket = [];

            $form_custom_fields = $this->form_custom_fields();

            if ( ! empty($form_custom_fields)) {
                foreach ($form_custom_fields as $customField) {
                    $customFieldKey = $customField['cid'];
                    $placeholder    = $customField['placeholder'];
                    // we are checking if $customFieldKey is not empty because if a merge field doesnt have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        // Gist accept date in unix timestamp in milliseconds
                        if (OCR::get_custom_field_type_by_id($customFieldKey, $this->extras['optin_campaign_id']) == 'date') {
                            $custom_fields_bucket[$placeholder] = strtotime_utc($value);
                            continue;
                        }

                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }

                        $custom_fields_bucket[$placeholder] = esc_html($value);
                    }
                }
            }

            if ( ! empty($custom_fields_bucket)) {
                $lead_data['custom_properties'] = $custom_fields_bucket;
            }

            $lead_data = array_filter($lead_data, [$this, 'data_filter']);

            $response = $this->convertfox_instance()->make_request('leads', $lead_data, 'post');

            if (parent::is_http_code_success($response['status_code'])) {
                $this->record_event();

                return parent::ajax_success();
            }

            self::save_optin_error_log(json_encode($response['body']), 'convertfox', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'convertfox', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}