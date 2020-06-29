<?php

namespace MailOptin\Core\Admin\SettingsPage;

// Exit if accessed directly
use W3Guy\Custom_Settings_Page_Api;

if ( ! defined('ABSPATH')) {
    exit;
}

abstract class AbstractSettingsPage
{
    protected $option_name;

    public function init_menu()
    {
        add_action('admin_menu', array($this, 'register_core_menu'));
    }

    public function register_core_menu()
    {

        add_menu_page(
            __('MailOptin WordPress Plugin', 'mailoptin'),
            __('MailOptin', 'mailoptin'),
            \MailOptin\Core\get_capability(),
            MAILOPTIN_SETTINGS_SETTINGS_SLUG,
            '',
            MAILOPTIN_ASSETS_URL . 'images/admin-icon.svg'
        );

        add_filter('admin_body_class', array($this, 'add_admin_body_class'));
    }

    public function stylish_header()
    {
        $logo_url = MAILOPTIN_ASSETS_URL . 'images/logo-mailoptin.png';
        ?>
        <div class="mo-admin-banner">
            <div class="mo-admin-banner__logo">
                <img src="<?= $logo_url ?>" alt="">
            </div>
            <div class="mo-admin-banner__helplinks">
                <a rel="noopener" href="https://mailoptin.io/docs/" target="_blank">
                    <span class="dashicons dashicons-book"></span> <?= __('Documentation', 'mailoptin'); ?>
                </a>
                <a rel="noopener" href="https://mailoptin.io/submit-ticket/" target="_blank">
                    <span class="dashicons dashicons-admin-users"></span> <?= __('Request Support', 'mailoptin'); ?>
                </a>
            </div>
            <div class="clear"></div>
        </div>
        <?php
    }

    /**
     * Register mailoptin core settings.
     *
     * @param Custom_Settings_Page_Api $instance
     * @param bool $remove_sidebar
     */
    public function register_core_settings(Custom_Settings_Page_Api $instance)
    {
        $instance->tab($this->tab_args());

        $this->stylish_header();
    }

    /**
     * Adds admin body class to all admin pages created by the plugin.
     *
     * @since 0.1.0
     *
     * @param  string $classes Space-separated list of CSS classes.
     *
     * @return string Filtered body classes.
     */
    public function add_admin_body_class($classes)
    {
        $current_screen = get_current_screen();

        if (empty ($current_screen)) return;

        if (false !== strpos($current_screen->id, 'mailoptin')) {
            // Leave space on both sides so other plugins do not conflict.
            $classes .= ' mailoptin-admin ';
        }

        return $classes;
    }

    public function tab_args()
    {
        $args = [];

        if (isset($_GET['page']) && $_GET['page'] == MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_SLUG) {
            $args[80]  = array('url' => MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_PAGE, 'label' => __('Email Automation', 'mailoptin'));
            $args[90]  = array('url' => MAILOPTIN_EMAIL_NEWSLETTERS_SETTINGS_PAGE, 'label' => __('Newsletters', 'mailoptin'));
            $args[100] = array('url' => MAILOPTIN_CAMPAIGN_LOG_SETTINGS_PAGE, 'label' => __('Log', 'mailoptin'));
        }

        $tabs = apply_filters('mailoptin_settings_page_tabs', $args);

        ksort($tabs);

        return $tabs;
    }

    public static function why_upgrade_to_pro()
    {
        $url = 'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=why_upgrade_sidebar';

        $content = '<ul>';
        $content .= '<li>' . __('Unlimited number of optin conversion.', 'mailoptin') . '</li>';
        $content .= '<li>' . __('More optin types e.g slide-in & top bar.', 'mailoptin') . '</li>';
        $content .= '<li>' . __('More newsletters type e.g email digest.', 'mailoptin') . '</li>';
        $content .= '<li>' . __('Ton of premium optin and email templates.', 'mailoptin') . '</li> ';
        $content .= '<li>' . __('Optin triggers e.g Exit Intent, Scroll etc.', 'mailoptin') . '</li>';
        $content .= '<li>' . __('Actionable reporting & insights.', 'mailoptin') . '</li>';
        $content .= '<li>' . __('Leads - conversion backup.', 'mailoptin') . '</li>';
        $content .= '<li>' . __('Wow your visitors with DisplayEffects.', 'mailoptin') . '</li>';
        $content .= '<li>' . __('Page level targeting.', 'mailoptin') . '</li>';
        $content .= '<li>' . __('And lots more.', 'mailoptin') . '</li>';
        $content .= '</ul>';
        $content .= '<div>Get <strong>10%</strong> off using this coupon.</div>';
        $content .= '<div style="margin: 5px"><span style="background: #e3e3e3;padding: 2px;">10PERCENTOFF</span></div>';
        $content .= '<div><a target="_blank" href="' . $url . '" class="button-primary" type="button">Go Premium</a></div>';

        return $content;
    }

    public function sidebar_support_docs()
    {
        $content = '<p>';
        $content .= sprintf(
            __('For support, %sreach out to us%s.', 'mailoptin'),
            '<strong><a href="https://mailoptin.io/support/" target="_blank">', '</a></strong>'
        );
        $content .= '</p>';

        $content .= '<p>';
        $content .= sprintf(
            __('Visit the %s for guidance.', 'mailoptin'),
            '<strong><a href="https://mailoptin.io/docs/" target="_blank">' . __('Documentation', 'mailoptin') . '</a></strong>'
        );

        $content .= '</p>';

        return $content;
    }

    public function rate_review_ad()
    {
        ob_start();
        $review_url        = 'https://wordpress.org/support/view/plugin-reviews/mailoptin';
        $compatibility_url = 'https://wordpress.org/plugins/mailoptin/#compatibility';
        $twitter_url       = 'https://twitter.com/home?status=I%20love%20this%20WordPress%20plugin!%20https://wordpress.org/plugins/mailoptin/';

        ?>
        <div style="text-align: center; margin: auto">
            <ul>
                <li>
                    <?php printf(
                        wp_kses(__('Is this plugin useful for you? Leave a positive review on the plugin\'s <a href="%s" target="_blank">WordPress listing</a>', 'mailoptin'),
                            array(
                                'a' => array(
                                    'href'   => array(),
                                    'target' => array('_blank'),
                                ),
                            )
                        ),
                        esc_url($review_url));
                    ?>
                </li>
                <li><?php printf(wp_kses(__('<a href="%s" target="_blank">Share your thoughts on Twitter</a>',
                        'mailoptin'),
                        array(
                            'a' => array(
                                'href'   => array(),
                                'target' => array('_blank'),
                            ),
                        )),
                        esc_url($twitter_url)); ?></li>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function mailoptin_pro_ad()
    {
        $content = '<a href="https://mailoptin.io/pricing/?discount=10PERCENTOFF&utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=sidebar_banner" target="_blank">';
        $content .= '<img width="250" src="' . MAILOPTIN_ASSETS_URL . 'images/mo-pro-upgrade.jpg' . '">';
        $content .= '</a>';

        return $content;
    }

    public static function profilepress_ad()
    {
        $content = '<a href="https://profilepress.net/pricing/?discount=20PERCENTOFF&ref=mailoptin_settings_page" target="_blank">';
        $content .= '<img width="250" src="' . MAILOPTIN_ASSETS_URL . 'images/profilepress-ad.jpg' . '">';
        $content .= '</a>';

        return $content;
    }
}