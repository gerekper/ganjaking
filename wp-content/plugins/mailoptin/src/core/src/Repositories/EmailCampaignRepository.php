<?php

namespace MailOptin\Core\Repositories;

use MailOptin\Core\Admin\Customizer\EmailCampaign\AbstractCustomizer;

class EmailCampaignRepository extends AbstractRepository
{
    const NEW_PUBLISH_POST = 'new_publish_post';
    const POSTS_EMAIL_DIGEST = 'posts_email_digest';
    const NEWSLETTER = 'newsletter';

    const CODE_YOUR_OWN_TEMPLATE = 'HTML';

    const NEWSLETTER_STATUS_DRAFT = 'draft';
    const NEWSLETTER_STATUS_FAILED = 'failed';

    /**
     * Return a human readable name for campaign identifier/key/type.
     *
     * @param string $type campaign type
     *
     * @return string
     */
    public static function get_type_name($type)
    {
        switch ($type) {
            case self::NEW_PUBLISH_POST:
                $value = __('New Post Notification', 'mailoptin');
                break;
            case self::POSTS_EMAIL_DIGEST:
                $value = __('Posts Email Digest', 'mailoptin');
                break;
            case self::NEWSLETTER:
                $value = __('Newsletter', 'mailoptin');
                break;
            default:
                $value = ucwords($type);
        }

        return apply_filters('mailoptin_campaign_type_name', $value, $type);
    }

    /**
     * Check if an email campaign name already exist.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function campaign_name_exist($name)
    {
        $campaign_name = sanitize_text_field($name);
        $table         = parent::email_campaigns_table();
        $result        = parent::wpdb()->get_var(
            parent::wpdb()->prepare("SELECT name FROM $table WHERE name = '%s'", $campaign_name)
        );

        return ! empty($result);
    }

    /**
     * Count number of email campaigns
     *
     * @return int
     */
    public static function campaign_count()
    {
        $table = parent::email_campaigns_table();

        return absint(parent::wpdb()->get_var("SELECT COUNT(*) FROM $table"));
    }

    /**
     * Add new email campaign to database.
     *
     * @param string $name
     * @param string $campaign_type
     * @param string $class
     *
     * @return false|int
     */
    public static function add_email_campaign($name, $campaign_type, $class)
    {
        $response = parent::wpdb()->insert(
            parent::email_campaigns_table(),
            array(
                'name'           => stripslashes($name),
                'campaign_type'  => $campaign_type,
                'template_class' => $class
            ),
            array(
                '%s',
                '%s',
                '%s'
            )
        );

        return ! $response ? $response : parent::wpdb()->insert_id;
    }

    /**
     * Get name or title of email campaign.
     *
     * @param int $email_campaign_id
     *
     * @return string
     */
    public static function get_email_campaign_name($email_campaign_id)
    {
        $table = parent::email_campaigns_table();

        return parent::wpdb()->get_var(
            parent::wpdb()->prepare("SELECT name FROM $table WHERE id = %d", $email_campaign_id)
        );
    }

    /**
     * The email campaign template PHP class name.
     *
     * @param int $email_campaign_id
     *
     * @return string
     */
    public static function get_template_class($email_campaign_id)
    {
        $table = parent::email_campaigns_table();

        return parent::wpdb()->get_var(
            parent::wpdb()->prepare("SELECT template_class FROM $table WHERE id = %d", $email_campaign_id)
        );
    }

    /**
     * The email campaign ID from class name.
     *
     * @param string $email_campaign_class_name
     *
     * @return string
     */
    public static function get_email_campaign_id_from_class_name($email_campaign_class_name)
    {
        $table = parent::email_campaigns_table();

        return parent::wpdb()->get_var(
            parent::wpdb()->prepare("SELECT id FROM $table WHERE template_class = %s", $email_campaign_class_name)
        );
    }

    /**
     * The email campaign type.
     *
     * @param int $email_campaign_id
     *
     * @return string
     */
    public static function get_email_campaign_type($email_campaign_id)
    {
        $table = parent::email_campaigns_table();

        return parent::wpdb()->get_var(
            parent::wpdb()->prepare("SELECT campaign_type FROM $table WHERE id = %d", $email_campaign_id)
        );
    }

    /**
     * Array of email campaign IDs
     *
     * @return array
     */
    public static function get_email_campaign_ids()
    {
        $table = parent::email_campaigns_table();

        return parent::wpdb()->get_col("SELECT id FROM $table");
    }

    /**
     * Array of email campaigns
     *
     * @return array
     */
    public static function get_email_campaigns()
    {
        $table = parent::email_campaigns_table();

        return parent::wpdb()->get_results("SELECT * FROM $table", 'ARRAY_A');
    }

    /**
     * Get email campaign by campaign ID.
     *
     * @param int $email_campaign_id
     *
     * @return mixed
     */
    public static function get_email_campaign_by_id($email_campaign_id)
    {
        $table = parent::email_campaigns_table();

        return parent::wpdb()->get_row(
            parent::wpdb()->prepare("SELECT * FROM $table WHERE id = %d", $email_campaign_id),
            'ARRAY_A'
        );
    }

    /**
     * Get email campaigns by campaign type.
     *
     * @param string $campaign_type
     *
     * @return mixed
     */
    public static function get_by_email_campaign_type($campaign_type)
    {
        $table = parent::email_campaigns_table();

        return parent::wpdb()->get_results(
            parent::wpdb()->prepare("SELECT * FROM $table WHERE campaign_type = '%s'", $campaign_type),
            'ARRAY_A'
        );
    }

    /**
     *
     * Get customizer value for campaign.
     *
     * @param int $email_campaign_id
     * @param string $settings_name
     * @param string $default
     *
     * @return mixed
     */
    public static function get_customizer_value($email_campaign_id, $settings_name, $default = '')
    {
        $abstract_customizer = new AbstractCustomizer($email_campaign_id);
        $customizer_defaults = isset($abstract_customizer->customizer_defaults[$settings_name]) && $abstract_customizer->customizer_defaults[$settings_name] !== null ? $abstract_customizer->customizer_defaults[$settings_name] : '';
        $default             = ! empty($default) ? $default : $customizer_defaults;
        $settings            = self::get_settings();
        $value               = isset($settings[$email_campaign_id][$settings_name]) ? $settings[$email_campaign_id][$settings_name] : null;

        return ! is_null($value) ? $value : $default;
    }

    /**
     *
     * Get customizer value for campaign.
     *
     * @param int $email_campaign_id
     * @param string $settings_name
     * @param string $default
     *
     * @return string
     */
    public static function get_customizer_value_without_default($email_campaign_id, $settings_name, $default = '')
    {
        $settings = self::get_settings();

        return isset($settings[$email_campaign_id][$settings_name]) ? $settings[$email_campaign_id][$settings_name] : '';
    }

    /**
     * Return value of a email campaign customizer settings or the default settings value if not found.
     *
     * @param int $optin_campaign_id
     * @param string $optin_form_setting
     *
     * @return mixed
     */
    public static function get_merged_customizer_value($email_campaign_id, $settings_name)
    {
        $customizer_defaults = (new AbstractCustomizer($email_campaign_id))->customizer_defaults;

        $default = isset($customizer_defaults[$settings_name]) ? $customizer_defaults[$settings_name] : '';

        return self::get_customizer_value($email_campaign_id, $settings_name, $default);
    }


    /**
     * Retrieve/return all email campaign customizer settings as stored in wp_option table.
     *
     * @return array
     */
    public static function get_settings()
    {
        return get_option(MO_EMAIL_CAMPAIGNS_WP_OPTION_NAME, []);
    }

    /**
     * Retrieve a email campaign settings by its ID.
     *
     * @param int $email_campaign_id
     *
     * @return mixed
     */
    public static function get_settings_by_id($email_campaign_id)
    {
        $settings = self::get_settings();

        return isset($settings[$email_campaign_id]) ? $settings[$email_campaign_id] : '';
    }

    /**
     * Check if email campaign is newsletter.
     *
     * @param $email_campaign_id
     *
     * @return bool
     */
    public static function is_newsletter($email_campaign_id)
    {
        return self::get_email_campaign_type($email_campaign_id) == self::NEWSLETTER;
    }

    /**
     * Is this email campaign active?
     *
     * @param $email_campaign_id
     *
     * @return bool
     */
    public static function is_campaign_active($email_campaign_id)
    {
        $val = self::get_customizer_value($email_campaign_id, 'activate_email_campaign');

        return 1 === $val ? true : (is_bool($val) ? $val : false);
    }

    public static function is_code_your_own_template($email_campaign_id)
    {
        return self::get_template_class($email_campaign_id) == self::CODE_YOUR_OWN_TEMPLATE;
    }

    /**
     * Update campaign title / name.
     *
     * @param string $title
     * @param int $email_campaign_id
     *
     * @return false|int
     */
    public static function update_campaign_name($title, $email_campaign_id)
    {
        $table = parent::email_campaigns_table();

        return parent::wpdb()->update(
            $table,
            array(
                'name' => $title
            ),
            array('id' => absint($email_campaign_id)),
            array(
                '%s'
            ),
            array('%d')
        );
    }

    /**
     * Delete campaign by ID
     *
     * @param int $email_campaign_id
     *
     * @return false|int
     */
    public static function delete_campaign_by_id($email_campaign_id)
    {
        $table = parent::email_campaigns_table();

        return parent::wpdb()->delete(
            $table,
            array('id' => $email_campaign_id),
            array('%d')
        );
    }

    /**
     * Update all email campaign settings.
     *
     * @param array $campaignSettings
     *
     * @return bool
     */
    public static function updateSettings($campaignSettings)
    {
        return update_option(MO_EMAIL_CAMPAIGNS_WP_OPTION_NAME, $campaignSettings, false);
    }

    /**
     * Activate email campaign.
     *
     * @param int $email_campaign_id
     *
     * @return bool
     */
    public static function activate_email_campaign($email_campaign_id)
    {
        // update the "activate_email_campaign" setting to true
        $all_settings                                                = self::get_settings();
        $all_settings[$email_campaign_id]['activate_email_campaign'] = true;

        return self::updateSettings($all_settings);
    }

    /**
     * Deactivate email campaign.
     *
     * @param int $email_campaign_id
     *
     * @return bool
     */
    public static function deactivate_email_campaign($email_campaign_id)
    {
        // update the "activate_email_campaign" setting to true
        $all_settings                                                = self::get_settings();
        $all_settings[$email_campaign_id]['activate_email_campaign'] = false;
        self::updateSettings($all_settings);
    }

    /**
     * Delete all settings data of an email campaign.
     *
     * @param int $email_campaign_id
     *
     * @return bool
     */
    public static function delete_settings_by_id($email_campaign_id)
    {
        $all_email_campaign_settings = self::get_settings();
        unset($all_email_campaign_settings[$email_campaign_id]);

        return self::updateSettings($all_email_campaign_settings);
    }
}