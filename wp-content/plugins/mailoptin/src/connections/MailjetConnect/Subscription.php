<?php

namespace MailOptin\MailjetConnect;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;
use function MailOptin\Core\strtotime_utc;

class Subscription extends AbstractMailjetConnect
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

    public function confirm_email_template()
    {
        $url             = home_url('?mo_mailjet_confirm_email=' . md5($this->email));
        $message_content = apply_filters('mo_mailjet_confirm_email_message_content', esc_html__('Thanks for subscribing to my email list. Please click the button below to complete your subscription.', 'mailoptin'));
        $button_text     = apply_filters('mo_mailjet_confirm_email_button_text', esc_html__('Confirm Subscription Now', 'mailoptin'));
        ob_start();
        require dirname(__FILE__) . '/confirm-email-tmpl.php';

        return ob_get_clean();
    }

    /**
     * @param $email
     *
     * @return bool
     * @throws \Exception
     */
    public function validate_sender_email($email)
    {
        $response = $this->mailjet_instance()->make_request('sender', ['Email' => $email], 'get');

        if ($response->Count === 0 || $response->Data[0]->Status != 'Active') {
            throw new \Exception('Sender email is not valid');
        }

        return true;
    }

    public function is_user_exist()
    {
        try {

            $response = $this->mailjet_instance()->make_request('contact/' . $this->email, [], 'get');

            return $response->Count > 0;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    public function send_confirmation_email()
    {
        $this->validate_sender_email(Settings::instance()->from_email());

        $email_body = $this->confirm_email_template();

        $payload = [
            'Messages' => [
                [
                    'From'     => [
                        'Email' => Settings::instance()->from_email(),
                        'Name'  => Settings::instance()->from_name(),
                    ],
                    'To'       => [
                        [
                            'Email' => $this->email,
                            'Name'  => ! empty($this->name) ? $this->name : '',
                        ]
                    ],
                    'Subject'  => apply_filters('mo_mailjet_confirm_email_subject', esc_html__('Confirm your subscription', 'mailoptin')),
                    'HTMLPart' => $email_body,
                    'TextPart' => \MailOptin\Core\html_to_text($email_body)
                ]
            ]
        ];

        $instance          = $this->mailjet_instance();
        $instance->api_url = 'api.mailjet.com/v3.1/';

        $response = $instance->make_request('send', $payload);

        if ( ! isset($response->Messages[0]->Status) || $response->Messages[0]->Status != 'success') {

            throw new \Exception(json_encode($response));
        }

        return true;
    }


    public function is_double_optin() {
        $optin_campaign_id = absint($this->extras['optin_campaign_id']);

        $setting = $this->get_integration_data('MailjetConnect_enable_double_optin');

        //external forms
        if($optin_campaign_id == 0) {
            $setting = $this->extras['is_double_optin'];
        }

        $val = ($setting === true);

        return apply_filters('mo_connections_mailjet_is_double_optin', $val, $optin_campaign_id);
    }

    /**
     * @return mixed
     */
    public function subscribe()
    {
        $error_message = esc_html__('There was an error saving your contact. Please try again.', 'mailoptin');

        try {

            $endpoint = "contactslist/{$this->list_id}/managecontact";

            $name_split = self::get_first_last_names($this->name);

            $firstname_key = $this->get_first_name_property();
            $lastname_key  = $this->get_last_name_property();

            $lead_data = [
                'Name'       => $this->name,
                'Email'      => $this->email,
                'Action'     => 'addforce', //The contact will be forcefully added to this list
                'Properties' => [
                    $firstname_key => $name_split[0],
                    $lastname_key  => $name_split[1]
                ],
            ];

            if (isset($this->extras['mo-acceptance']) && $this->extras['mo-acceptance'] == 'yes') {
                $gdpr_tag = apply_filters('mo_connections_mailjet_acceptance_tag', 'gdpr');
                try {
                    $this->mailjet_instance()->make_request('contactmetadata', [
                        'Datatype'  => 'str',
                        'Name'      => $gdpr_tag,
                        'NameSpace' => 'static'
                    ]);
                } catch (\Exception $e) {
                }

                $lead_data['Properties'][$gdpr_tag] = 'true';
            }

            //Add custom fields to the contact
            $custom_field_mappings = $this->form_custom_field_mappings();

            if ( ! empty($custom_field_mappings)) {

                foreach ($custom_field_mappings as $MailjetKey => $customFieldKey) {
                    // we are checking if $customFieldKey is not empty because if a merge field doesn't have a custom field
                    // selected for it, the default "Select..." value is empty ("")
                    if ( ! empty($customFieldKey) && ! empty($this->extras[$customFieldKey])) {
                        $value = $this->extras[$customFieldKey];
                        if (OCR::get_custom_field_type_by_id($customFieldKey, $this->extras['optin_campaign_id']) == 'date') {
                            $lead_data['Properties'][$MailjetKey] = strtotime_utc($value);
                            continue;
                        }

                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }

                        $lead_data['Properties'][$MailjetKey] = esc_html($value);
                    }
                }
            }

            $lead_data['Properties'] = array_filter($lead_data['Properties'], [$this, 'data_filter']);

            $lead_data = array_filter($lead_data, [$this, 'data_filter']);

            if ($this->is_double_optin() && ! $this->is_user_exist()) {

                $this->send_confirmation_email();

                $data                           = get_option('mo_mailjet_double_optin_bucket', []);
                $key                            = md5($this->email);
                $lead_data['mailjet_list_id']   = $this->list_id;
                $lead_data['optin_campaign_id'] = $this->extras['optin_campaign_id'];
                $data[$key]                     = $lead_data;

                update_option('mo_mailjet_double_optin_bucket', $data, false);

                return parent::ajax_success();
            }

            //Create the subscriber(if not exists) then add them to the list
            $response = $this->mailjet_instance()->make_request($endpoint, $lead_data);

            //If the contact was successfully created, the response count will be 1
            if (isset($response->Count) && ! empty($response->Count)) {
                return parent::ajax_success();
            }

            self::save_optin_error_log(json_encode($response), 'mailjet', $this->extras['optin_campaign_id']);

            return parent::ajax_failure($error_message);

        } catch (\Exception $e) {

            self::save_optin_error_log($e->getCode() . ': ' . $e->getMessage(), 'mailjet', $this->extras['optin_campaign_id']);

            return parent::ajax_failure($error_message);
        }
    }
}