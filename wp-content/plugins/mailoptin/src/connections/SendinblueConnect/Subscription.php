<?php

namespace MailOptin\SendinblueConnect;

class Subscription extends AbstractSendinblueConnect
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

    public function subscribe()
    {
        $name_split = self::get_first_last_names($this->name);

        try {

            $lead_data = [
                'email'         => $this->email,
                'listIds'       => [absint($this->list_id)],
                'updateEnabled' => true
            ];

            if (apply_filters('mo_connections_sendinblue_list_sync', true)) {
                $req               = $this->sendinblue_instance()->make_request(sprintf('contacts/%s', urlencode($this->email)));
                $is_exist_list_ids = $req['body']->listIds;

                if ( ! empty($is_exist_list_ids)) {
                    $lead_data['listIds'] = array_map('absint', array_merge($lead_data['listIds'], $is_exist_list_ids));
                    // remove duplicate list Ids
                    $lead_data['listIds'] = array_unique($lead_data['listIds']);
                    // re-arrange the array index/key so json_encode later on will make it an array instead of object
                    // see https://stackoverflow.com/a/18977473/2648410
                    $lead_data['listIds'] = array_values($lead_data['listIds']);

                }
            }

            $firstname_key = $this->get_first_name_attribute();
            $lastname_key  = $this->get_last_name_attribute();

            if ( ! empty($name_split[0])) {
                $lead_data['attributes'][$firstname_key] = $name_split[0];
                $lead_data['attributes']['NOMBRE'] = $name_split[0];
                $lead_data['attributes']['PRENOM'] = $name_split[0];
            }

            if ( ! empty($name_split[1])) {
                $lead_data['attributes'][$lastname_key] = $name_split[1];
                $lead_data['attributes']['SURNAME'] = $name_split[1];
            }

            if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                // in capital letter pls.
                $gdpr_tag = strtoupper(apply_filters('mo_connections_sendinblue_acceptance_tag', 'GDPR'));
                $this->sendinblue_instance()->make_request("contacts/attributes/normal/$gdpr_tag", ['type' => 'text'], 'post');

                $lead_data['attributes'][$gdpr_tag] = 'true';
            }

            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {
                foreach ($custom_field_mappings as $SendinblueFieldKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesnt have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        // date field just works. no multi-select support
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        $lead_data['attributes'][$SendinblueFieldKey] = esc_attr($value);
                    }
                }
            }

            $lead_data = apply_filters('mo_connections_sendinblue_subscription_parameters', $lead_data, $this);

            $response = $this->sendinblue_instance()->make_request('contacts', $lead_data, 'post');

            if (self::is_http_code_success($response['status_code']) || isset($response['body']->id)) {
                return parent::ajax_success();
            }

            if (isset($response['body']->code, $response['body']->message)) {
                if ('duplicate_parameter' == $response['body']->code) {
                    return parent::ajax_success();
                }
            }

            self::save_optin_error_log($response['body']->code . ': ' . $response['body']->message, 'sendinblue', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'sendinblue', $this->extras['optin_campaign_id']);

            return parent::ajax_failure(__('There was an error saving your contact. Please try again.', 'mailoptin'));
        }
    }
}