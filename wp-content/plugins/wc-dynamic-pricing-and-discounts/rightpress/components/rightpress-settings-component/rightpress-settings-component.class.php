<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Settings Component
 *
 * @class RightPress_Settings_Component
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Settings_Component
{

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

        // Load classes
        require_once __DIR__ . '/classes/rightpress-plugin-settings.class.php';
        require_once __DIR__ . '/classes/rightpress-settings-exception.class.php';
    }



}

RightPress_Settings_Component::get_instance();
