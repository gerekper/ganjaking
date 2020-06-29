<?php

namespace MailOptin\Core\Repositories;


abstract class AbstractCampaignLogMeta
{
    /**
     * Add meta data field to campaign log.
     *
     * @param int $campaign_log_id
     * @param string $meta_key
     * @param string $meta_value
     * @param bool $unique
     *
     * @return int|false Meta ID on success, false on failure.
     */
    public static function add_campaignlog_meta($campaign_log_id, $meta_key, $meta_value, $unique = false)
    {
        return add_metadata('campaign_log', $campaign_log_id, $meta_key, $meta_value, $unique);
    }

    /**
     * Update campaign log meta field based on campaign ID.
     *
     * @param int $campaign_log_id
     * @param string $meta_key
     * @param string $meta_value
     * @param string $prev_value
     *
     * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
     */
    public static function update_campaignlog_meta($campaign_log_id, $meta_key, $meta_value, $prev_value = '')
    {
        return update_metadata('campaign_log', $campaign_log_id, $meta_key, $meta_value, $prev_value);
    }

    /**
     * Remove metadata matching criteria from a campaign log.
     *
     * @param int $campaign_log_id
     * @param string $meta_key
     * @param string $meta_value
     * @param bool $delete_all
     *
     * @return bool True on success, false on failure.
     */
    public static function delete_campaignlog_meta($campaign_log_id, $meta_key, $meta_value = '', $delete_all = false)
    {
        return delete_metadata('campaign_log', $campaign_log_id, $meta_key, $meta_value, $delete_all);
    }

    /**
     * Retrieve post meta field for a campaign log.
     *
     * @param int $campaign_log_id
     * @param string $meta_key
     * @param bool $single
     *
     * @return mixed
     */
    public static function get_campaignlog_meta($campaign_log_id, $meta_key = '', $single = false)
    {
        return get_metadata('campaign_log', $campaign_log_id, $meta_key, $single);
    }
}