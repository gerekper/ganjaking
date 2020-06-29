<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Privacy Compliance Component
 *
 * @class RightPress_Privacy_Compliance
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Privacy_Compliance
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

RightPress_Privacy_Compliance::get_instance();
