<?php

namespace MailOptin\ContactForm7Connect;

class IntegrationsListing extends \WPCF7_Service
{
    public function is_active()
    {
        return ! empty(CF7::email_service_providers());
    }

    public function get_title()
    {
        return 'MailOptin';
    }

    public function get_categories()
    {
        return array('email_marketing');
    }

    public function link()
    {
        echo sprintf('<a href="%1$s">%2$s</a>',
            'https://mailoptin.io/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=cf7_integration_listing',
            'mailoptin.io'
        );
    }

    public function display($action = '')
    {
        echo '<p>' . sprintf(
                esc_html(__('The MailOptin integration allows you to send contact data collected through your contact forms to any email marketing software and CRM including Mailchimp, AWeber, Campaign Monitor, MailerLite, Drip, ConvertKit, Zoho, ActiveCampaign, HubSpot, Sendinblue, Sendy, GetResponse and more. %sFor details, see %s.', 'mailoptin')),
                '<br>',
                wpcf7_link(
                    'https://mailoptin.io/?p=25086&utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=cf7_integration_listing',
                    __('MailOptin Integration', 'mailoptin')
                )
            ) . '</p>';

        echo sprintf(
            '<p><a href="%1$s" class="button">%2$s</a></p>',
            MAILOPTIN_CONNECTIONS_SETTINGS_PAGE,
            esc_html__('Setup Integration', 'mailoptin')
        );
    }

    /**
     * @return self
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