<?php

namespace MailOptin\Core\Admin\SettingsPage;

class PreviewCampaignLog
{
    public function __construct()
    {
        add_filter('template_include', array($this, 'preview_campaign'));
        add_action('template_redirect', array($this, 'preview_campaign_error_log'));
    }

    /**
     * Handle preview of campaign newsletter content.
     *
     * @param string $template
     *
     * @return string
     */
    public function preview_campaign($template)
    {

        if ( \MailOptin\Core\current_user_has_privilege()) {
            if (isset($_GET['mailoptin']) && isset($_GET['type']) && isset($_GET['id']) && 'preview-campaign' == $_GET['mailoptin']) {
                $template = MAILOPTIN_SETTINGS_PAGE_FOLDER . 'include.preview-campaign-log.php';
            }
        }

        return $template;
    }

    /**
     * Handles preview of campaign error log note.
     */
    public function preview_campaign_error_log()
    {
        if (isset($_GET['mailoptin'], $_GET['id']) && 'preview-campaign-error-log' == $_GET['mailoptin']) {
            $filename = sanitize_key($_GET['id']);
            echo nl2br(file_get_contents(MAILOPTIN_CAMPAIGN_ERROR_LOG . "$filename.log"));
            wp_die();
        }
    }

    /**
     * @return PreviewCampaignLog
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