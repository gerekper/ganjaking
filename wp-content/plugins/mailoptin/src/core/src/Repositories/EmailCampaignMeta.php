<?php

namespace MailOptin\Core\Repositories;


use MailOptin\Core\Core;

class EmailCampaignMeta
{
    /**
     * Add meta data field to email campaign.
     *
     * @param int $email_campaign_id
     * @param string $meta_key
     * @param string $meta_value
     * @param bool $unique
     * @return int|false Meta ID on success, false on failure.
     */
    public static function add_meta_data($email_campaign_id, $meta_key, $meta_value, $unique = false)
    {
        return add_metadata('email_campaign', $email_campaign_id, $meta_key, $meta_value, $unique);
    }

    /**
     * Update email campaign meta field based on campaign ID.
     *
     * @param int $email_campaign_id
     * @param string $meta_key
     * @param string $meta_value
     * @param string $prev_value
     * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
     */
    public static function update_meta_data($email_campaign_id, $meta_key, $meta_value, $prev_value = '')
    {
        return update_metadata('email_campaign', $email_campaign_id, $meta_key, $meta_value, $prev_value);
    }

    /**
     * Remove metadata matching criteria from a email campaign.
     *
     * @param int $email_campaign_id
     * @param string $meta_key
     * @param string $meta_value
     * @param bool $delete_all
     * @return bool True on success, false on failure.
     */
    public static function delete_meta_data($email_campaign_id, $meta_key, $meta_value = '', $delete_all = false)
    {
        return delete_metadata('email_campaign', $email_campaign_id, $meta_key, $meta_value, $delete_all);
    }


    /**
     * Delete all meta data belonging to an email campaign.
     * @param $email_campaign_id
     * @return bool
     */
    public static function delete_all_meta_data($email_campaign_id)
    {
        if (!isset($email_campaign_id)) return false;

        global $wpdb;

        return $wpdb->delete(
            $wpdb->prefix . Core::email_campaign_meta_table_name,
            array('email_campaign_id' => $email_campaign_id)
        );
    }

    /**
     * Retrieve post meta field for a email campaign.
     *
     * @param int $email_campaign_id
     * @param string $meta_key
     * @param bool $single
     * @return mixed
     */
    public static function get_meta_data($email_campaign_id, $meta_key = '', $single = true)
    {
        return get_metadata('email_campaign', $email_campaign_id, $meta_key, $single);
    }
}