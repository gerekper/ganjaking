<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Shared Product Price Background Refresh
 *
 * @class RightPress_Product_Price_Background_Refresh
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Product_Price_Background_Refresh
{

    // TODO: price caching could be updated by background process as soon as at least one price is determined to be outdated (for that user prices are updated real time but for others it will potentially be faster)

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

    }





}

RightPress_Product_Price_Background_Refresh::get_instance();
