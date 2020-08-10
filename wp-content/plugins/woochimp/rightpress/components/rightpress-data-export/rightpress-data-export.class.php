<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Data Export Component
 *
 * @class RightPress_Data_Export
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Data_Export
{

    // TODO

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

RightPress_Data_Export::get_instance();
