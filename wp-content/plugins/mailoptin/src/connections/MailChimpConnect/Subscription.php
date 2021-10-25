<?php

namespace MailOptin\MailChimpConnect;

use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;
use function MailOptin\Core\strtotime_utc;

class Subscription extends AbstractMailChimpConnect
{
    public $email;
    public $name;
    public $list_id;
    public $extras;
    protected $optin_campaign_id;
    /** @var Connect */
    protected $connectInstance;

    public function __construct($email, $name, $list_id, $extras, $connectInstance)
    {
        $this->email           = $email;
        $this->name            = $name;
        $this->list_id         = $list_id;
        $this->extras          = $extras;
        $this->connectInstance = $connectInstance;

        $this->optin_campaign_id = absint($this->extras['optin_campaign_id']);

        parent::__construct();
    }

    /**
     * True if double optin is not disabled.
     *
     * @return bool
     */
    public function is_double_optin()
    {
        $setting = $this->get_integration_data('MailChimpConnect_disable_double_optin');

        $optin_campaign_id = absint($this->extras['optin_campaign_id']);

        //external forms
        if($optin_campaign_id == 0) {
            $setting = $this->extras['is_double_optin'];
        }

        $val = ($setting !== true);

        return apply_filters('mo_connections_mailchimp_is_double_optin', $val, $this->optin_campaign_id);
    }

    /**
     * Return array of selected interests.
     *
     * @return array
     */
    public function interests()
    {
        $segmentation_type = $this->get_integration_data('MailChimpConnect_group_segment_type');

        if ($segmentation_type == 'automatic') {
            $interests = array_keys($this->get_integration_data('MailChimpConnect_interests'));
        } else {
            $interests = isset($this->extras['mo-mailchimp-interests']) ? $this->extras['mo-mailchimp-interests'] : [];
        }

        if (empty($interests)) return [];

        $interests = array_map('sanitize_text_field', $interests);

        $interests = array_fill_keys($interests, true);

        return $interests;
    }

    public function update_gdpr_permission()
    {
        try {
            $request = $this->mc_list_instance()->getMembers($this->list_id, ['count' => 1, 'fields' => 'members.marketing_permissions.marketing_permission_id']);

            if (isset($request->members[0]->marketing_permissions)) {
                $permission_ids = array_reduce($request->members[0]->marketing_permissions, function ($carry, $item) {
                    $carry[] = $item->marketing_permission_id;

                    return $carry;
                });

                $parameters = ['marketing_permissions' => []];

                foreach ($permission_ids as $permission_id) {
                    $parameters['marketing_permissions'][] = [
                        'marketing_permission_id' => $permission_id,
                        'enabled'                 => true
                    ];
                }

                $this->mc_list_instance()->addOrUpdateMember($this->list_id, $this->email, $parameters);

            }
        } catch (\Exception $e) {
            // do nothing.
        }
    }

    public function get_list_merge_field($merge_tag)
    {
        try {

            $response = $this->mc_list_instance()->getMergeFields(
                $this->list_id,
                ['count' => 9999, 'fields' => 'merge_fields.tag,merge_fields.type,merge_fields.options']
            );

            if (isset($response->merge_fields) && is_array($response->merge_fields)) {
                foreach ($response->merge_fields as $key => $merge_field) {
                    if ($merge_field->tag == $merge_tag) {
                        return $response->merge_fields[$key];
                    }
                }
            }

            return [];


        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'mailchimp');

            return [];
        }
    }

    /**
     * @return mixed
     */
    public function subscribe()
    {
        try {

            $name_split = self::get_first_last_names($this->name);

            $optin_status = $this->is_double_optin() ? 'pending' : 'subscribed';

            $firstname_key = $this->get_first_name_merge_tag();
            $lastname_key  = $this->get_last_name_merge_tag();

            $merge_fields = [
                $firstname_key => $name_split[0],
                $lastname_key  => $name_split[1]
            ];

            // Could be useful in situation where there is only either first or lastname merge field
            // and by setting the first/lastname merge tag in customizer to empty, it will prevent a
            // 400: Your merge fields were invalid
            // $merge_fields = array_filter($merge_fields, [$this, 'data_filter'], ARRAY_FILTER_USE_KEY);

            $custom_field_mappings = $this->form_custom_field_mappings();

            if (is_array($custom_field_mappings) && ! empty($custom_field_mappings)) {

                // we don't need to be doing this array intersect again since we solved the root cause
                // in the way integrations are saved in optin customizer.
                // leaving because of backward compat.
                $list_merge_fields = $this->connectInstance->get_optin_fields($this->list_id);

                // this ensures the custom field mappings exist in mailchimp to prevent signing up failure.
                $intersect_result = array_intersect(array_keys($custom_field_mappings), array_keys($list_merge_fields));

                if ( ! empty($intersect_result)) {
                    foreach ($custom_field_mappings as $mergeFieldKey => $customFieldKey) {
                        // we are checking if $customFieldKey is not empty because if a merge field doesnt have a custom field
                        // selected for it, the default "Select..." value is empty ("")
                        if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                            $value = $this->extras[$customFieldKey];
                            if (OCR::get_custom_field_type_by_id($customFieldKey, $this->extras['optin_campaign_id']) == 'date') {

                                $response = $this->get_list_merge_field($mergeFieldKey);

                                // Get date format.
                                $date_format = $response->options->date_format;

                                // Convert field value to timestamp.
                                $field_value_timestamp = strtotime_utc($value);

                                // Format date.
                                switch ($date_format) {

                                    case 'DD/MM':
                                    case 'MM/DD':
                                        $value = gmdate('m/d', $field_value_timestamp);
                                        break;

                                    case 'DD/MM/YYYY':
                                    case 'MM/DD/YYYY':
                                        $value = gmdate('m/d/Y', $field_value_timestamp);
                                        break;
                                }
                            }

                            if (is_array($value)) {
                                $value = implode(', ', $value);
                            }

                            $merge_fields[$mergeFieldKey] = esc_html($value);
                        }
                    }
                }
            }

            $parameters = [
                'merge_fields'  => array_filter($merge_fields, [$this, 'data_filter']),
                'interests'     => $this->interests(),
                'status_if_new' => $optin_status,
                'status'        => 'subscribed',
                'ip_signup'     => \MailOptin\Core\get_ip_address()
            ];

            $parameters = apply_filters('mo_connections_mailchimp_subscription_parameters', array_filter($parameters, [$this, 'data_filter']), $this);

            $response = $this->mc_list_instance()->addOrUpdateMember($this->list_id, $this->email, $parameters);

            if (is_object($response) && in_array($response->status, ['subscribed', 'pending'])) {

                $lead_tags = $this->get_integration_tags('MailChimpConnect_lead_tags');

                if ( ! empty($lead_tags)) {

                    $lead_tags = array_map(function ($tag) {
                        return array(
                            'name'   => trim($tag),
                            'status' => 'active',
                        );
                    },
                        explode(',', $lead_tags)
                    );

                    $this->mc_list_instance()->request(
                        'POST',
                        '/lists/{list_id}/members/{subscriber_hash}/tags',
                        ['list_id' => $this->list_id, 'subscriber_hash' => md5(strtolower($this->email))],
                        ['tags' => $lead_tags]
                    );

                }

                if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                    $this->update_gdpr_permission();
                }

                return parent::ajax_success();
            }

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'mailchimp', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}