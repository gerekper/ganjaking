<?php
namespace WeDevs\PM_Pro\Modules\Custom_Fields\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Load general WP action hook
 */
class Filters {
    /**
     * This plugin's instance.
     *
     * @var CoBlocks_Accordion_IE_Support
     */
    private static $instance;

    /**
     * Registers the plugin.
     */
    public static function instance() {
         if ( !self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * The Constructor.
     */
    public function __construct() {
        add_filter( 'pm_pro_load_router_files', [$this, 'route'] );

    }

    public function route( $files ) {
        $router_files = glob( PM_PRO_CUSTOM_FIELD_PATH . "/routes/*.php" );

        return array_merge( $files, $router_files );
    }
}

