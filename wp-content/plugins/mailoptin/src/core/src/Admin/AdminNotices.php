<?php

namespace MailOptin\Core\Admin;

use MailOptin\Core\Repositories\EmailCampaignRepository;
use PAnD as PAnD;

class AdminNotices
{
    public function __construct()
    {
        add_action('admin_init', function () {
            if (\MailOptin\Core\is_mailoptin_admin_page()) {
                remove_all_actions('admin_notices');
            }

            do_action('mailoptin_admin_notices');

            add_action('admin_notices', array($this, 'optin_campaigns_cache_cleared'));
            add_action('admin_notices', array($this, 'template_class_not_found'));
            add_action('admin_notices', array($this, 'optin_class_not_found'));
            add_action('admin_notices', array($this, 'failed_campaign_retried'));
            add_action('admin_notices', array($this, 'email_campaign_count_limit_exceeded'));
            add_action('admin_notices', array($this, 'optin_branding_added_by_default'));
            add_action('admin_notices', array($this, 'review_plugin_notice'));
            add_action('admin_notices', array($this, 'show_woocommerce_features'));
            add_action('admin_notices', array($this, 'show_wpforms_features'));
            add_action('admin_notices', array($this, 'show_cf7_features'));
            add_action('admin_notices', array($this, 'show_ninja_forms_features'));
            add_action('admin_notices', array($this, 'show_gravity_forms_features'));

            add_filter('removable_query_args', array($this, 'removable_query_args'));
        });

        add_action('admin_init', array('PAnD', 'init'));
        add_action('admin_init', array($this, 'dismiss_leave_review_notice_forever'));
    }

    public function is_admin_notice_show()
    {
        return apply_filters('mo_ads_admin_notices_display', true);
    }

    public function removable_query_args($args)
    {
        $args[] = 'email-campaign-error';
        $args[] = 'optin-cache';
        $args[] = 'settings-updated';
        $args[] = 'license-settings-updated';
        $args[] = 'failed-campaign';
        $args[] = 'fbca';

        return $args;
    }

    /**
     * Notice shown when optin campaign caches has been successfully cleared.
     */
    public function optin_campaigns_cache_cleared()
    {
        if ( ! is_super_admin(get_current_user_id())) return;

        if (isset($_GET['optin-cache']) && $_GET['optin-cache'] == 'cleared') : ?>
            <div id="message" class="updated notice is-dismissible">
                <p>
                    <?php _e('Optin campaigns cache successfully cleared.', 'mailoptin'); ?>
                </p>
            </div>
        <?php endif;
    }

    /**
     * Template class not found - admin notice.
     */
    public function template_class_not_found()
    {
        if ( ! is_super_admin(get_current_user_id()))
            return;

        if (isset($_GET['email-campaign-error']) && $_GET['email-campaign-error'] == 'class-not-found') : ?>
            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php
                    _e('There was an error fetching email campaign template dependency.', 'mailoptin');
                    ?>
                </p>
            </div>
        <?php endif;
    }

    /**
     * Optin template class not found - admin notice.
     */
    public function optin_class_not_found()
    {
        if ( ! is_super_admin(get_current_user_id()))
            return;

        if (isset($_GET['optin-error']) && $_GET['optin-error'] == 'class-not-found') : ?>
            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php _e('There was an error fetching optin dependency. Try again or select another template.', 'mailoptin'); ?>
                </p>
            </div>
        <?php endif;
    }

    /**
     * Template class not found - admin notice.
     */
    public function failed_campaign_retried()
    {
        if ( ! is_super_admin(get_current_user_id())) return;

        if (isset($_GET['failed-campaign']) && $_GET['failed-campaign'] == 'retried') : ?>
            <div id="message" class="updated notice is-dismissible">
                <p>
                    <?php _e('Email campaigned resent.', 'mailoptin'); ?>
                </p>
            </div>
        <?php endif;
    }

    /**
     * Display notice that branding is now included by default in mailoptin optin forms
     */
    public function optin_branding_added_by_default()
    {
        if ( ! PAnD::is_admin_notice_active('optin-branding-added-by-default-forever')) {
            return;
        }

        if (MAILOPTIN_VERSION_NUMBER > '1.1.2.0') return;

        $learn_more = 'https://mailoptin.io/article/make-money-mailoptin-branding/?ref=wp_dashboard';

        $notice = sprintf(
            __('MailOptin branding is now included on all optin forms unless you explicitly disabled it at "Configuration" panel in form builder. %sLearn more%s', 'mailoptin'),
            '<a href="' . $learn_more . '" target="_blank">',
            '</a>'
        );

        echo '<div data-dismissible="optin-branding-added-by-default-forever" class="update-nag notice notice-warning is-dismissible">';
        echo "<p><strong>$notice</strong></p>";
        echo '</div>';
    }

    public function dismiss_leave_review_notice_forever()
    {
        if ( ! empty($_GET['mo_admin_action']) && $_GET['mo_admin_action'] == 'dismiss_leave_review_forever') {
            update_option('mo_dismiss_leave_review_forever', true);

            wp_safe_redirect(esc_url_raw(remove_query_arg('mo_admin_action')));
            exit;
        }
    }

    /**
     * Display one-time admin notice to review plugin at least 7 days after installation
     */
    public function review_plugin_notice()
    {
        if ( ! PAnD::is_admin_notice_active('review-plugin-notice-forever')) return;

        if (get_option('mo_dismiss_leave_review_forever', false)) return;

        $install_date = get_option('mo_install_date', '');

        if (empty($install_date)) return;

        $diff = round((time() - strtotime($install_date)) / 24 / 60 / 60);

        if ($diff < 7) return;

        $review_url = 'https://wordpress.org/support/plugin/mailoptin/reviews/?filter=5#new-post';

        $dismiss_url = esc_url_raw(add_query_arg('mo_admin_action', 'dismiss_leave_review_forever'));

        $notice = sprintf(
            __('Hey, I noticed you have been using MailOptin for at least 7 days now - that\'s awesome! Could you please do me a BIG favor and give it a %1$s5-star rating on WordPress?%2$s This will help us spread the word and boost our motivation - thanks!', 'mailoptin'),
            '<a href="' . $review_url . '" target="_blank">',
            '</a>'
        );
        $label  = __('Sure! I\'d love to give a review', 'mailoptin');

        $dismiss_label = __('Dimiss Forever', 'mailoptin');

        $notice .= "<div style=\"margin:10px 0 0;\"><a href=\"$review_url\" target='_blank' class=\"button-primary\">$label</a></div>";
        $notice .= "<div style=\"margin:10px 0 0;\"><a href=\"$dismiss_url\">$dismiss_label</a></div>";

        echo '<div data-dismissible="review-plugin-notice-forever" class="update-nag notice notice-warning is-dismissible">';
        echo "<p>$notice</p>";
        echo '</div>';
    }

    /**
     * Display notice when limit of created email campaign is exceeded
     */
    public function email_campaign_count_limit_exceeded()
    {
        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) return;

        if ( ! PAnD::is_admin_notice_active('email-campaign-count-limit-exceeded-3')) {
            return;
        }

        if (EmailCampaignRepository::campaign_count() < 1) return;

        if (strpos(\MailOptin\Core\current_url_with_query_string(), MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_PAGE) === false) return;

        $upgrade_url = 'https://mailoptin.io/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=email_campaign_limit';
        $notice      = sprintf(__('Upgrade to %s now to create multiple email automation with advance targeting and option to send directly to your email list subscribers.', 'mailoptin'),
            '<a href="' . $upgrade_url . '" target="_blank">' . __('MailOptin premium', 'mailoptin') . '</a>'
        );
        echo '<div data-dismissible="email-campaign-count-limit-exceeded-3" class="updated notice notice-success is-dismissible">';
        echo "<p>$notice</p>";
        echo '</div>';
    }

    public function show_woocommerce_features()
    {
        if ( ! $this->is_admin_notice_show()) return;

        if ( ! PAnD::is_admin_notice_active('show_woocommerce_features-forever')) {
            return;
        }

        if ( ! class_exists('WooCommerce')) return;

        $upgrade_url = 'https://mailoptin.io/integrations/woocommerce/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=woo_admin_notice';
        $notice      = sprintf(__('Did you know you can display targeted messages and optin forms across your WooCommerce store and also automatically send email alert of new products to your subscribers and customers? %sLearn more%s', 'mailoptin'),
            '<a href="' . $upgrade_url . '" target="_blank">', '</a>'
        );
        echo '<div data-dismissible="show_woocommerce_features-forever" class="notice notice-info is-dismissible">';
        echo "<p>$notice</p>";
        echo '</div>';
    }

    public function show_cf7_features()
    {
        if ( ! $this->is_admin_notice_show()) return;

        if ( ! PAnD::is_admin_notice_active('show_cf7_features-forever')) {
            return;
        }

        if ( ! class_exists('WPCF7')) return;

        $upgrade_url = 'https://mailoptin.io/article/contact-form-7-mailchimp-aweber-more/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=cf7_admin_notice';
        $notice      = sprintf(__('Did you know with MailOptin, you can connect Contact Form 7 to major email marketing software such as Mailchimp, AWeber, Campaign Monitor, MailerLite, ActiveCampaign? %sLearn more%s', 'mailoptin'),
            '<a href="' . $upgrade_url . '" target="_blank">', '</a>'
        );
        echo '<div data-dismissible="show_cf7_features-forever" class="notice notice-info is-dismissible">';
        echo "<p>$notice</p>";
        echo '</div>';
    }

    public function show_ninja_forms_features()
    {
        if ( ! $this->is_admin_notice_show()) return;

        if ( ! PAnD::is_admin_notice_active('show_ninja_forms_features-forever')) {
            return;
        }

        if ( ! class_exists('Ninja_Forms')) return;

        $upgrade_url = 'https://mailoptin.io/article/ninja-forms-mailchimp-aweber-more/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=ninja_forms_admin_notice';
        $notice      = sprintf(__('Did you know with MailOptin, you can connect Ninja Forms to major email marketing software such as Mailchimp, AWeber, Campaign Monitor, MailerLite, ActiveCampaign? %sLearn more%s', 'mailoptin'),
            '<a href="' . $upgrade_url . '" target="_blank">', '</a>'
        );
        echo '<div data-dismissible="show_ninja_forms_features-forever" class="notice notice-info is-dismissible">';
        echo "<p>$notice</p>";
        echo '</div>';
    }

    public function show_gravity_forms_features()
    {
        if ( ! $this->is_admin_notice_show()) return;

        if ( ! PAnD::is_admin_notice_active('show_gravity_forms_features-forever')) {
            return;
        }

        if ( ! class_exists('GFForms')) return;

        $upgrade_url = 'https://mailoptin.io/article/gravity-forms-mailchimp-aweber-more/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=gravity_forms_admin_notice';
        $notice      = sprintf(__('Did you know with MailOptin, you can connect Gravity Forms to your email marketing software and CRM including Mailchimp, Sendinblue, MailerLite, Ontraport, GetResponse? %sLearn more%s', 'mailoptin'),
            '<a href="' . $upgrade_url . '" target="_blank">', '</a>'
        );
        echo '<div data-dismissible="show_gravity_forms_features-forever" class="notice notice-info is-dismissible">';
        echo "<p>$notice</p>";
        echo '</div>';
    }

    public function show_wpforms_features()
    {
        if ( ! $this->is_admin_notice_show()) return;

        if ( ! PAnD::is_admin_notice_active('show_wpforms_features-forever')) {
            return;
        }

        if ( ! class_exists('WPForms\WPForms')) return;

        $upgrade_url = 'https://mailoptin.io/article/wpforms-email-marketing-crm/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=wpforms_admin_notice';
        $notice      = sprintf(__('Did you know with MailOptin, you can connect WPForms to major email marketing software such as Mailchimp, ConvertKit, MailerLite, HubSpot, Sendinblue? %sLearn more%s', 'mailoptin'),
            '<a href="' . $upgrade_url . '" target="_blank">', '</a>'
        );
        echo '<div data-dismissible="show_wpforms_features-forever" class="notice notice-info is-dismissible">';
        echo "<p>$notice</p>";
        echo '</div>';
    }

    /**
     * @return AdminNotices
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