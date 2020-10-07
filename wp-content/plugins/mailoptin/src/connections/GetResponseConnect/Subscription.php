<?php

namespace MailOptin\GetResponseConnect;

class Subscription extends AbstractGetResponseConnect
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

            $lead_data = array(
                'name'       => $this->name,
                'email'      => $this->email,
                'dayOfCycle' => 0,
                'campaign'   => array('campaignId' => $this->list_id),
                // IP address of a contact (IPv4 or IPv6)
                'ipAddress'  => \MailOptin\Core\get_ip_address(),
                'tags'       => []
            );

            $subscriber_tags = $this->get_integration_tags('GetResponseConnect_subscriber_tags');

            foreach ($subscriber_tags as $tag_id) {
                $lead_data['tags'][] = ['tagId' => $tag_id];
            }

            if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                // tag has to be mall letters
                $gdpr_tag = strtolower(apply_filters('mo_connections_getresponse_acceptance_tag', 'gdpr'));
                $result   = (array)$this->getresponse_instance()->getTags(['fields' => 'name']);
                if ( ! empty($result)) {
                    foreach ($result as $tag) {
                        if ($tag->name == $gdpr_tag) {
                            $gdpr_tag_id = $tag->tagId;
                            break;
                        }
                    }
                }

                if ( ! isset($gdpr_tag_id)) {
                    $result      = $this->getresponse_instance()->setTags(['name' => $gdpr_tag]);
                    $gdpr_tag_id = $result->tagId;
                }

                $lead_data['tags'][] = ['tagId' => $gdpr_tag_id];
            }

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {
                $lead_data['customFieldValues'] = [];

                foreach ($custom_field_mappings as $GRFieldKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesnt have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        // note for date field, GR accept one in this format 2020-01-31 which is the default for pikaday. No multiselect field in GR.
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        $lead_data['customFieldValues'][] = [
                            'customFieldId' => $GRFieldKey,
                            'value'         => [
                                esc_html($value)
                            ]
                        ];
                    }
                }
            }

            // it's important to remove empty param as GR will throw an error if say name field is empty.
            $lead_data = array_filter($lead_data, [$this, 'data_filter']);

            $response = (array)$this->getresponse_instance()->addContact($lead_data);

            // if user/email already exist.
            if (isset($response['code']) && $response['code'] === 1008) return parent::ajax_success();

            // return empty (array) response on success.
            if (empty($response)) return parent::ajax_success();

            self::save_optin_error_log(json_encode($response), 'getresponse', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'getresponse', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}