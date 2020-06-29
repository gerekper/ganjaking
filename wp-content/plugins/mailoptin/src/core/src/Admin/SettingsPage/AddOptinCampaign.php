<?php
/**
 * Copyright (C) 2016  Agbonghama Collins <me@w3guy.com>
 */

namespace MailOptin\Core\Admin\SettingsPage;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

use MailOptin\Core\Repositories\OptinThemesRepository;
use W3Guy\Custom_Settings_Page_Api;

class AddOptinCampaign extends AbstractSettingsPage
{
    /**
     * Build the settings page structure. I.e tab, sidebar.
     */
    public function settings_admin_page()
    {
        add_action('wp_cspa_before_closing_header', [$this, 'back_to_optin_overview']);
        add_action('wp_cspa_before_post_body_content', array($this, 'optin_theme_sub_header'), 10, 2);
        add_filter('wp_cspa_main_content_area', [$this, 'optin_form_list']);

        $instance = Custom_Settings_Page_Api::instance();
        $instance->page_header(__('Add New', 'mailoptin'));
        $this->register_core_settings($instance);
        $instance->build(true, true);
    }

    /**
     * Sub-menu header for optin theme types.
     */
    public function optin_theme_sub_header()
    {
        if (!empty($_GET['page']) && $_GET['page'] == MAILOPTIN_OPTIN_CAMPAIGNS_SETTINGS_SLUG) : ?>
            <div class="mailoptin-optin-new-list mailoptin-optin-clear">
                <h4><?php _e('Title', 'mailoptin'); ?>
                    <input type="text" name="mailoptin-optin-campaign" id="mailoptin-add-optin-campaign-title" placeholder="<?php _e('Enter a name...', 'profilepress') ?>">
                </h4>
            </div>
            <div id="mailoptin-sub-bar">
                <div class="mailoptin-new-toolbar mailoptin-clear">
                    <h4><?php _e('Select Optin Type', 'mailoptin'); ?>
                        <span class="spinner mo-dash-spinner"></span>
                    </h4>
                    <span class="sr-only"><?php __('Loading...', 'mailoptin'); ?></span>
                    <ul class="mailoptin-design-options">
                        <li>
                            <a href="#" class="mo-select-optin-type mailoptin-type-active" data-optin-type="lightbox">
                                <?php _e('Lightbox', 'mailoptin'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="mo-select-optin-type" data-optin-type="inpost">
                                <?php _e('In-Post', 'mailoptin'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="mo-select-optin-type" data-optin-type="sidebar">
                                <?php _e('Sidebar/Widget', 'mailoptin'); ?>
                            </a></li>
                        <li>
                            <a href="#" class="mo-select-optin-type" data-optin-type="bar">
                                <?php _e('Notification-Bar', 'mailoptin'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="mo-select-optin-type" data-optin-type="slidein">
                                <?php _e('Slide-In', 'mailoptin'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endif;
    }

    /**
     * Display list of optin
     */
    public function optin_form_list()
    {
        // lightbox/modal display should be default.
        $optin_type = 'lightbox';

        echo '<div class="mailoptin-optin-themes mailoptin-optin-clear">';
        OptinThemesRepository::listing_display_template($optin_type);
        echo '</div>';
    }

    public function back_to_optin_overview()
    {
        $url = MAILOPTIN_OPTIN_CAMPAIGNS_SETTINGS_PAGE;
        echo "<a class=\"add-new-h2\" style='margin-left: 15px;' href=\"$url\">" . __('Back to Overview', 'mailoptin') . '</a>';
    }


    /**
     * @return AddOptinCampaign
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