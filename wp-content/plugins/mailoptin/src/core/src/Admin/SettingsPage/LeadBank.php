<?php

namespace MailOptin\Core\Admin\SettingsPage;

use W3Guy\Custom_Settings_Page_Api;

class LeadBank extends AbstractSettingsPage
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'register_settings_page'));
    }

    public function register_settings_page()
    {
        $hook = add_submenu_page(
            MAILOPTIN_SETTINGS_SETTINGS_SLUG,
            __('Leads (Submissions) - MailOptin', 'mailoptin'),
            __('Leads', 'mailoptin'),
            \MailOptin\Core\get_capability(),
            MAILOPTIN_LEAD_BANK_SETTINGS_SLUG,
            array($this, 'settings_admin_page')
        );

        add_action("load-$hook", array($this, 'screen_option'));
    }

    /**
     * Screen options
     */
    public function screen_option()
    {
        $option = 'per_page';
        $args   = [
            'label'   => __('Leads', 'mailoptin'),
            'default' => 10,
            'option'  => 'conversions_per_page',
        ];

        add_screen_option($option, $args);

        do_action('mailoptin_leadbank_settings_page_screen_option');
    }

    public function settings_admin_page()
    {
        if ( ! apply_filters('mailoptin_enable_leadbank', false)) {
            add_filter('wp_cspa_main_content_area', array($this, 'upsell_settings_page'), 10, 2);
        }

        do_action("mailoptin_leadbank_settings_page");

        $instance = Custom_Settings_Page_Api::instance();
        $instance->option_name('mo_leads');
        $instance->page_header(__('Leads (Submissions)', 'mailoptin'));
        $this->register_core_settings($instance);
        $instance->build(true);
    }

    public function upsell_settings_page($content, $option_name)
    {
        if ($option_name != 'mo_leads') {
            return $content;
        }

        $url = 'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=leadbank_btn';

        ob_start();
        ?>
        <div class="mo-settings-page-disabled">
            <div class="mo-upgrade-plan">
                <div class="mo-text-center">
                    <div class="mo-lock-icon"></div>
                    <h1><?php _e('Leads are Locked', 'mailoptin'); ?></h1>
                    <p>
                        <?php printf(
                            __('Leads are all subscribers that sign up on your site.', 'mailoptin'),
                            '<strong>',
                            '</strong>');
                        ?>
                    </p>
                    <p>
                        <?php _e('Your current plan does not include this feature.', 'mailoptin');
                        ?>
                    </p>
                    <div class="moBtncontainer mobtnUpgrade">
                        <a target="_blank" href="<?= $url; ?>" class="mobutton mobtnPush mobtnGreen">
                            <?php _e('Upgrade to Unlock', 'mailoptin'); ?>
                        </a>
                    </div>
                </div>
            </div>
            <img src="<?php echo MAILOPTIN_ASSETS_URL; ?>images/leadbankscreenshot.png">
        </div>
        <?php

        return ob_get_clean();
    }

    /**
     * @return LeadBank
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