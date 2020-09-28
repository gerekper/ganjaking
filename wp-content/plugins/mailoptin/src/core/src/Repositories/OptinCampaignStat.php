<?php

namespace MailOptin\Core\Repositories;

use function MailOptin\Core\cache_transform;

class OptinCampaignStat extends OptinCampaignMeta
{
    /**
     * @var int ID of optin campaign
     */
    protected $optin_campaign_id;

    /**
     * The meta key for optin campaign impressions.
     *
     * @var string
     */
    protected $impression = 'mo_counter';

    /**
     * The meta key for optin campaign conversions.
     *
     * @var string
     */
    protected $conversion = 'mo_conversions';

    public function __construct($optin_campaign_id)
    {
        $this->optin_campaign_id = $optin_campaign_id;
    }

    /**
     * Retrieves the impressions for a given optin campaign.
     *
     * @return int
     */
    public function get_impressions()
    {
        $cache_key = 'get_impressions_' . $this->optin_campaign_id;

        $callback = function () {
            return (int)parent::get_campaign_meta($this->optin_campaign_id, $this->impression, true);
        };

        return cache_transform($cache_key, $callback);
    }

    /**
     * Retrieves the conversions for a given optin campaign.
     *
     * @return int
     */
    public function get_conversions()
    {
        $cache_key = 'get_conversions_' . $this->optin_campaign_id;

        $callback = function () {
            return (int)parent::get_campaign_meta($this->optin_campaign_id, $this->conversion, true);
        };

        return cache_transform($cache_key, $callback);
    }

    /**
     * Retrieves the conversion rate for a given optin campaign.
     *
     * @return int
     */
    public function get_conversion_rate()
    {
        $conversions = $this->get_conversions();
        $impressions = $this->get_impressions();

        return (0 == $conversions || 0 == $impressions) ? '0' : number_format(($conversions / $impressions) * 100, 2) . '&#37;';
    }

    /**
     * Reset optin campaign stat.
     */
    public function reset_stat()
    {
        $this->update_campaign_meta($this->optin_campaign_id, $this->impression, (int)0);
        $this->update_campaign_meta($this->optin_campaign_id, $this->conversion, (int)0);
    }

    /**
     * Saves hit to DB.
     *
     * @param string $stat_type could be 'impression' or 'conversion'.
     *
     * @return bool
     */
    public function save($stat_type)
    {
        $key = $this->{$stat_type};
        // Increase the counter by 1.
        $counter = (int)parent::get_campaign_meta($this->optin_campaign_id, $key, true);

        return parent::update_campaign_meta($this->optin_campaign_id, $key, (int)$counter + 1);

    }
}