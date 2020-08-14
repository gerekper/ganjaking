<?php

namespace MailOptin\InfusionsoftConnect;

use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Chosen_Select_Control;
use MailOptin\Core\Connections\ConnectionInterface;

class Connect extends AbstractInfusionsoftConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'InfusionsoftConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        add_filter('mo_optin_form_integrations_default', array($this, 'integration_customizer_settings'));
        add_filter('mo_optin_integrations_controls_after', array($this, 'integration_customizer_controls'));

        add_filter('mailoptin_email_campaign_customizer_page_settings', array($this, 'campaign_customizer_settings'));
        add_filter('mailoptin_email_campaign_customizer_settings_controls', array($this, 'campaign_customizer_controls'), 10, 4);

        parent::__construct();
    }

    public static function features_support()
    {
        return [
            self::OPTIN_CAMPAIGN_SUPPORT,
            self::EMAIL_CAMPAIGN_SUPPORT,
            self::OPTIN_CUSTOM_FIELD_SUPPORT
        ];
    }

    /**
     * Register Infusionsoft Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Infusionsoft (Keap)', 'mailoptin');

        return $connections;
    }

    public function campaign_customizer_settings($settings)
    {
        $settings['InfusionsoftConnect_email_user_id'] = array(
            'default'   => '',
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['InfusionsoftConnect_email_tag_recipients'] = array(
            'default'   => '',
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        return $settings;
    }

    public function campaign_customizer_controls($controls, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        // always prefix with the name of the connect/connection service.
        $controls['InfusionsoftConnect_email_user_id'] = [
            'type'        => 'select',
            'label'       => __('Email Sender (Required)', 'mailoptin'),
            'description' => __('The infusionsoft user to send the email on behalf of (required).', 'mailoptin'),
            'section'     => $customizerClassInstance->campaign_settings_section_id,
            'settings'    => $option_prefix . '[InfusionsoftConnect_email_user_id]',
            'choices'     => $this->users(),
            'priority'    => 195
        ];

        $controls['InfusionsoftConnect_email_tag_recipients'] = new WP_Customize_Chosen_Select_Control(
            $wp_customize,
            $option_prefix . '[InfusionsoftConnect_email_tag_recipients]',
            [
                'label'       => __('Tags to Send To', 'mailoptin'),
                'description' => __('Select tags that contacts must have for them to receive this campaign emails. Leave blank to send to all contacts.', 'mailoptin'),
                'section'     => $customizerClassInstance->campaign_settings_section_id,
                'settings'    => $option_prefix . '[InfusionsoftConnect_email_tag_recipients]',
                'choices'     => $this->get_tags(),
                'priority'    => 199
            ]
        );

        return $controls;
    }

    /**
     * @return mixed
     */
    public function get_tags()
    {
        if ( ! self::is_connected()) return [];

        try {

            return parent::infusionsoftInstance()->getTags();

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'infusionsoft');

            return [];
        }
    }

    /**
     * @return mixed
     */
    public function users()
    {
        if ( ! self::is_connected()) return [];

        try {
            return ['' => esc_html__('Select...', 'mailoptin')] + parent::infusionsoftInstance()->get_users();

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'infusionsoft');

            return [];
        }
    }

    /**
     * @param array $settings
     *
     * @return mixed
     */
    public function integration_customizer_settings($settings)
    {
        $settings['InfusionsoftConnect_lead_tags']    = apply_filters('mailoptin_customizer_optin_campaign_InfusionsoftConnect_lead_tags', '');
        $settings['InfusionsoftConnect_contact_type'] = apply_filters('mailoptin_customizer_optin_campaign_InfusionsoftConnect_contact_type', '');
        $settings['InfusionsoftConnect_lead_source']  = apply_filters('mailoptin_customizer_optin_campaign_InfusionsoftConnect_lead_source', '');
        $settings['InfusionsoftConnect_lead_owner']   = apply_filters('mailoptin_customizer_optin_campaign_InfusionsoftConnect_lead_owner', '');

        return $settings;
    }

    /**
     * @param $controls
     * @param $optin_campaign_id
     * @param $index
     * @param $saved_values
     *
     * @return array
     */
    public function integration_customizer_controls($controls)
    {
        if (defined('MAILOPTIN_DETACH_LIBSODIUM') === true) {
            // always prefix with the name of the connect/connection service.
            $controls[] = [
                'field'       => 'chosen_select',
                'name'        => 'InfusionsoftConnect_lead_tags',
                'label'       => __('Lead Tags', 'mailoptin'),
                'choices'     => $this->get_tags(),
                'description' => __('Select tags to assign to subscribers who opt-in via this campaign.', 'mailoptin'),
            ];

            $controls[] = [
                'field'   => 'select',
                'name'    => 'InfusionsoftConnect_contact_type',
                'choices' => [
                    ''                 => esc_html__('Select...', 'mailoptin'),
                    'Prospect'         => esc_html__('Prospect', 'mailoptin'),
                    'Customer'         => esc_html__('Customer', 'mailoptin'),
                    'Partner'          => esc_html__('Partner', 'mailoptin'),
                    'Personal Contact' => esc_html__('Personal Contact', 'mailoptin'),
                    'Vendor'           => esc_html__('Vendor', 'mailoptin'),
                ],
                'label'   => __('Person Type', 'mailoptin')
            ];

            $controls[] = [
                'field'   => 'select',
                'name'    => 'InfusionsoftConnect_lead_source',
                'choices' => [
                    ''   => esc_html__('Select...', 'mailoptin'),
                    '6'  => esc_html__('Advertisement', 'mailoptin'),
                    '9'  => esc_html__('Direct Mail', 'mailoptin'),
                    '11' => esc_html__('Online - Organic Search Engine', 'mailoptin'),
                    '12' => esc_html__('Online - Pay Per Click', 'mailoptin'),
                    '7'  => esc_html__('Referral - From Affiliate/Partner', 'mailoptin'),
                    '8'  => esc_html__('Referral - From Customer', 'mailoptin'),
                    '13' => esc_html__('Trade Show', 'mailoptin'),
                    '10' => esc_html__('Yellow Pages', 'mailoptin'),
                ],
                'label'   => __('Lead Source', 'mailoptin')
            ];

            $controls[] = [
                'field'   => 'select',
                'name'    => 'InfusionsoftConnect_lead_owner',
                'choices' => $this->users(),
                'label'   => __('Owner', 'mailoptin')
            ];

        } else {

            $content = sprintf(
                __("Upgrade to %sMailOptin Premium%s to map custom fields, assign tags to leads, set Person Type, Lead Source and Owner.", 'mailoptin'),
                '<a target="_blank" href="https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=infusionsoft_connection">',
                '</a>',
                '<strong>',
                '</strong>'
            );

            $controls[] = [
                'name'    => 'InfusionsoftConnect_upgrade_notice',
                'field'   => 'custom_content',
                'content' => $content
            ];
        }

        return $controls;
    }

    public function replace_placeholder_tags($content, $type = 'html')
    {
        if ($type == 'html') {
            // use regex to replace the "view web version" with infusionsoft ~HostedEmail.Link~
            $pattern     = ['/<a .+ href="{{webversion}}(?:.+)?">(.+)<\/a>/'];
            $replacement = ['~HostedEmail.Link~'];
            $content     = preg_replace($pattern, $replacement, $content);
        }

        // search and replace this if this operation is for text content.
        return $this->replace_footer_placeholder_tags($content);
    }

    /**
     * {@inherit_doc}
     *
     * Return array of email list
     *
     * @return mixed
     */
    public function get_email_list()
    {
        try {

            return [
                'all' => __('All Contacts', 'mailoptin'),
            ];

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'infusionsoft');
        }
    }

    /**
     * {@inherit_doc}
     *
     *
     * @return mixed
     */
    public function get_optin_fields($list_id = '')
    {
        $custom_fields = [
            'middle_name'        => esc_html__('Middle Name', 'mailoptin'),
            'preferred_name'     => esc_html__('Nickname', 'mailoptin'),
            'job_title'          => esc_html__('Job Title', 'mailoptin'),
            'spouse_name'        => esc_html__('Spouse Name', 'mailoptin'),
            'website'            => esc_html__('Website', 'mailoptin'),
            'notes'              => esc_html__('Person Notes', 'mailoptin'),

            // Phone (mophne)
            'mophne_phone_1'     => esc_html__('Phone 1', 'mailoptin'),
            'mophne_phone_1_ext' => esc_html__('Phone 1 Extension', 'mailoptin'),
            'mophne_phone_2'     => esc_html__('Phone 2', 'mailoptin'),
            'mophne_phone_2_ext' => esc_html__('Phone 2 Extension', 'mailoptin'),

            'anniversary' => esc_html__('Anniversary', 'mailoptin'),
            'birthday'    => esc_html__('Birthday', 'mailoptin'),

            'email_address_2'            => esc_html__('Email Address 2', 'mailoptin'),
            'email_address_3'            => esc_html__('Email Address 3', 'mailoptin'),

            // billing address (moblla)
            'moblla_address_line1'       => esc_html__('Billing Address Street (Line 1)', 'mailoptin'),
            'moblla_address_line2'       => esc_html__('Billing Address Street (Line 2)', 'mailoptin'),
            'moblla_address_city'        => esc_html__('Billing Address City', 'mailoptin'),
            // country_code is required if region (state) is specified
            'moblla_address_state'       => esc_html__('Billing Address State', 'mailoptin'),
            // alpha-3 code representation of billing country. E.g NGA, USA etc
            // see https://community.infusionsoft.com/t/invalid-country-code-and-region-what-is-valid/13354/2
            'moblla_address_country'     => esc_html__('Billing Address Country', 'mailoptin'),
            // Field used to store postal codes containing a combination of letters and numbers ex. 'EC1A', 'S1 2HE', '75000'
            // Particularly useful for international country postal code.
            'moblla_address_postal_code' => esc_html__('Billing Address Postal Code', 'mailoptin'),
            // Mainly used in the United States, this is typically numeric. ex. '85001', '90002'
            // Note: this is to be used instead of 'postal_code', not in addition to.
            'moblla_address_zip_code'    => esc_html__('Billing Address Zip Code', 'mailoptin'),
            // If you have an extended zip code, put the last four digits (those after the hyphen) here
            //Last four of a full zip code ex. '8244', '4320'. Totally optional
            // This field is supplemental to the zip_code field, otherwise will be ignored.
            'moblla_address_zip_four'    => esc_html__('Billing Address Zip Extension', 'mailoptin'),

            // shipping address (moshpa)
            'moshpa_address_line1'       => esc_html__('Shipping Address Street (Line 1)', 'mailoptin'),
            'moshpa_address_line2'       => esc_html__('Shipping Address Street (Line 2)', 'mailoptin'),
            'moshpa_address_city'        => esc_html__('Shipping Address City', 'mailoptin'),
            'moshpa_address_state'       => esc_html__('Shipping Address State', 'mailoptin'),
            'moshpa_address_country'     => esc_html__('Shipping Address Country', 'mailoptin'),
            'moshpa_address_postal_code' => esc_html__('Shipping Address Postal Code', 'mailoptin'),
            'moshpa_address_zip_code'    => esc_html__('Shipping Address Zip Code', 'mailoptin'),
            'moshpa_address_zip_four'    => esc_html__('Shipping Address Zip Extension', 'mailoptin'),

            // other address (motha)
            'motha_address_line1'        => esc_html__('Other Address Street (Line 1)', 'mailoptin'),
            'motha_address_line2'        => esc_html__('Other Address Street (Line 2)', 'mailoptin'),
            'motha_address_city'         => esc_html__('Other Address City', 'mailoptin'),
            'motha_address_state'        => esc_html__('Other Address State', 'mailoptin'),
            'motha_address_country'      => esc_html__('Other Address Country', 'mailoptin'),
            'motha_address_postal_code'  => esc_html__('Other Address Postal Code', 'mailoptin'),
            'motha_address_zip_code'     => esc_html__('Other Address Zip Code', 'mailoptin'),
            'motha_address_zip_four'     => esc_html__('Other Address Zip Extension', 'mailoptin'),

            // social network(mosonk)
            'mosonk_facebook'            => esc_html__('Facebook Username', 'mailoptin'),
            'mosonk_twitter'             => esc_html__('Twitter Username', 'mailoptin'),
            'mosonk_linkedin'            => esc_html__('LinkedIn Username', 'mailoptin'),
        ];

        try {

            $fields = $this->infusionsoftInstance()->get_custom_fields();

            if (is_array($fields) && ! empty($fields)) {

                // custom fields (cufd)
                foreach ($fields as $id => $label) {
                    $custom_fields['cufd_' . $id] = $label;
                }
            }

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'infusionsoft');
        }

        return $custom_fields;
    }

    /**
     * @param int $email_campaign_id
     * @param int $campaign_log_id
     * @param string $subject
     * @param string $content_html
     * @param string $content_text
     *
     * @return array
     * @throws \Exception
     *
     */
    public function send_newsletter($email_campaign_id, $campaign_log_id, $subject, $content_html, $content_text)
    {
        return (new SendCampaign($email_campaign_id, $campaign_log_id, $subject, $content_html, $content_text))->send();
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $list_id ID of email list to add subscriber to
     * @param mixed|null $extras
     *
     * @return mixed
     */
    public function subscribe($email, $name, $list_id, $extras = null)
    {
        return (new Subscription($email, $name, $list_id, $extras, $this))->subscribe();
    }

    /**
     * Singleton poop.
     *
     * @return Connect|null
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}