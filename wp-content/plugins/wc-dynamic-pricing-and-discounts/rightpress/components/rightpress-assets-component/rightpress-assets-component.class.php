<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Assets Component
 *
 * @class RightPress_Assets_Component
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Assets_Component
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
        require_once __DIR__ . '/classes/rightpress-asset.class.php';
        require_once __DIR__ . '/classes/rightpress-assets.class.php';
    }





}

RightPress_Assets_Component::get_instance();
