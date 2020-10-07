<?php

namespace MailOptin\SendlaneConnect;

use MailOptin\Core\Repositories\OptinCampaignsRepository;

class Subscription extends AbstractSendlaneConnect
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

            $lead_tags = $this->get_integration_tags('SendlaneConnect_lead_tags');

            $lead_data = [
                'first_name' => $name_split[0],
                'last_name'  => $name_split[1],
                'email'      => $this->email,
                'list_id'    => $this->list_id,
                'tag_names'  => $lead_tags
            ];

            // because adding a tag could lead to error for non single-optin account,
            // we need to check if a tag was specified because we assume presence of a tag
            // means the user has their account set to single optin
            if ( ! empty($lead_data['tag_names']) && isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                $lead_data['tag_names'] = 'GDPR,' . isset($lead_data['tag_names']) ? $lead_data['tag_names'] : '';
            }

            $form_custom_fields = $this->form_custom_fields();

            if ( ! empty($form_custom_fields)) {
                foreach ($form_custom_fields as $customField) {
                    $customFieldKey = $customField['cid'];
                    $placeholder    = $customField['placeholder'];
                    // we are checking if $customFieldKey is not empty because if a merge field doesnt have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        $lead_data[$placeholder] = esc_html($value);
                    }
                }
            }

            $lead_data = array_filter($lead_data, [$this, 'data_filter']);

            $response = $this->sendlane_instance()->make_request('list-subscriber-add', $lead_data);
            $response = $response['body'];

            if ( ! empty($response->success)) {
                return parent::ajax_success();
            }

            self::save_optin_error_log(json_encode($response), 'sendlane', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {

            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'sendlane', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}