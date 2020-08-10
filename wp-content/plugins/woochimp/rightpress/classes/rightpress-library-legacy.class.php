<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress library legacy code support
 *
 * @class RightPress_Library_Legacy
 * @package RightPress
 * @author RightPress
 */
class RightPress_Library_Legacy extends RightPress_Legacy
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    // Legacy filters (new_filter => old_filter)
    protected $legacy_filters = array(
        'rightpress_product_price_live_update_label_html' => 'rightpress_live_product_price_update_label_html',
        'rightpress_product_price_live_update_extra_data' => 'rightpress_live_product_price_update_extra_data',
    );





}

RightPress_Library_Legacy::get_instance();
