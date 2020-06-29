<?php

namespace MailOptin\Core\Repositories;


class OptinCampaignMeta
{
    /**
     * Add meta data field to optin campaign.
     *
     * @param int $campaign_id
     * @param string $meta_key
     * @param string $meta_value
     * @param bool $unique
     * @return int|false Meta ID on success, false on failure.
     */
    public static function add_campaign_meta($campaign_id, $meta_key, $meta_value, $unique = false)
    {
        return add_metadata('optin_campaign', $campaign_id, $meta_key, $meta_value, $unique);
    }

    /**
     * Update optin campaign meta field based on campaign ID.
     *
     * @param int $campaign_id
     * @param string $meta_key
     * @param string $meta_value
     * @param string $prev_value
     * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
     */
    public static function update_campaign_meta($campaign_id, $meta_key, $meta_value, $prev_value = '')
    {
        return update_metadata('optin_campaign', $campaign_id, $meta_key, $meta_value, $prev_value);
    }

    /**
     * Remove metadata matching criteria from a optin campaign.
     *
     * @param int $campaign_id
     * @param string $meta_key
     * @param string $meta_value
     * @param bool $delete_all
     * @return bool True on success, false on failure.
     */
    public static function delete_campaign_meta($campaign_id, $meta_key, $meta_value = '', $delete_all = false)
    {
        return delete_metadata('optin_campaign', $campaign_id, $meta_key, $meta_value, $delete_all);
    }

    /**
     * Retrieve post meta field for a optin campaign.
     *
     * @param int $campaign_id
     * @param string $meta_key
     * @param bool $single
     * @return mixed
     */
    public static function get_campaign_meta($campaign_id, $meta_key = '', $single = false)
    {
        return get_metadata('optin_campaign', $campaign_id, $meta_key, $single);
    }

    /**
     * Get optin campaign ID by meta value and meta key.
     *
     * @param string $meta_key
     * @param int $parent_optin_id
     */
    public static function get_optin_id_by_meta_key_value($meta_key, $parent_optin_id)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'mo_optin_campaignmeta';

        return $wpdb->get_col(
            $wpdb->prepare(
                "SELECT optin_campaign_id FROM $table WHERE meta_key = %s AND meta_value = %s",
                $meta_key,
                $parent_optin_id
            )
        );
    }
}