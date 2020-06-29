<?php

namespace MailOptin\Core\Repositories;

use MailOptin\Core\Core;

abstract class AbstractRepository
{
    /**
     * @return \wpdb|array
     */
    protected static function wpdb()
    {
        return $GLOBALS['wpdb'];
    }


    /**
     * Database table for optin campaigns.
     *
     * @return string
     */
    public static function campaigns_table()
    {
        return self::wpdb()->prefix . Core::optin_campaigns_table_name;
    }


    /**
     * Database table for optin conversions.
     *
     * @return string
     */
    public static function conversions_table()
    {
        return self::wpdb()->prefix . Core::conversions_table_name;
    }


    /**
     * Database table for email campaigns.
     *
     * @return string
     */
    public static function email_campaigns_table()
    {
        return self::wpdb()->prefix . Core::email_campaigns_table_name;
    }
}