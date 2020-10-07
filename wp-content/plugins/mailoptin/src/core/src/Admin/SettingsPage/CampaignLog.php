<?php

namespace MailOptin\Core\Admin\SettingsPage;

// Exit if accessed directly
use W3Guy\Custom_Settings_Page_Api;

if ( ! defined('ABSPATH')) {
    exit;
}

class CampaignLog extends AbstractSettingsPage
{
    /** @var Campaign_Log_List */
    protected $campaign_instance;

    public function __construct()
    {
        add_action('mailoptin_register_email_campaign_settings_page', [$this, 'init']);
    }

    public function init($hook)
    {
        add_action("load-$hook", array($this, 'screen_option'));
    }

    /**
     * Build the settings page structure. I.e tab, sidebar.
     */
    public function settings_admin_page()
    {
        add_action('wp_cspa_main_content_area', array($this, 'wp_list_table'), 10, 2);

        $instance = Custom_Settings_Page_Api::instance();
        $instance->option_name('mailoptin_campaign_log');
        $instance->page_header(__('Emails', 'mailoptin'));
        $this->register_core_settings($instance);
        echo '<div class="mailoptin-log-listing">';
        $instance->build(true);
        echo '</div>';
    }

    /**
     * Screen options
     */
    public function screen_option()
    {
        if (isset($_GET['page'], $_GET['view']) && $_GET['page'] == MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_SLUG && $_GET['view'] == MAILOPTIN_CAMPAIGN_LOG_SETTINGS_SLUG) {

            $option = 'per_page';
            $args   = array(
                'label'   => __('Email Log', 'mailoptin'),
                'default' => 10,
                'option'  => 'campaign_log_per_page',
            );

            add_screen_option($option, $args);

            $this->campaign_instance = Campaign_Log_List::get_instance();
        }
    }

    /**
     * Callback to output content of Email_Template_List table.
     *
     * @param string $content
     * @param string $option_name settings Custom_Settings_Page_Api option name.
     *
     * @return string
     */
    public function wp_list_table($content, $option_name)
    {
        if ($option_name != 'mailoptin_campaign_log') {
            return $content;
        }

        $this->campaign_instance->prepare_items();

        ob_start();
        $this->campaign_instance->display();

        return ob_get_clean();
    }


    /**
     * @return CampaignLog
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