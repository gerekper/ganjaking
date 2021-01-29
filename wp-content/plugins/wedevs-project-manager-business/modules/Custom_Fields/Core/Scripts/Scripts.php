<?php
namespace WeDevs\PM_Pro\Modules\Custom_Fields\Core\Scripts;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Load general WP action hook
 */
class Scripts {
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
        add_action( 'admin_enqueue_scripts', [ $this, 'register' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

        //Scripts load from front-end
        add_action( 'pm_load_shortcode_script', [ $this, 'register' ] );
        add_action( 'pm_load_shortcode_script', [ $this, 'frontend_scripts' ] );
    }

    /**
     * Register all scripts
     */
    public function register() {

        wp_register_script( 'pm-pro-custom-field', PM_PRO_CUSTOM_FIELD_VIEW_URL . '/assets/js/custom-field.js', array('pm-const'), filemtime( PM_PRO_CUSTOM_FIELD_VIEW_PATH . '/assets/js/custom-field.js' ), true );
        //wp_register_style( 'pm-pro-custom-field', PM_PRO_CUSTOM_FIELD_VIEW_URL . '/assets/css/custom-field.css', array(), filemtime( PM_PRO_CUSTOM_FIELD_VIEW_PATH . '/css/custom-field.css' ) );
    }

    /**
     * Print scripts for admin panel
     */
    public function admin_scripts() {
        if (
            isset( $_GET['page'] )
                &&
            $_GET['page'] == 'pm_projects'
        ) {
            $this->frontend_scripts();
        }
    }

    /**
     * Print scripts for frontend
     */
    public function frontend_scripts() {
        wp_enqueue_script( 'pm-pro-custom-field' );
    }
}
