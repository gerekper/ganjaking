<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Compatibility Class
 *
 * @class   YITH_WCPB_Compatibility
 * @package Yithemes
 * @since   1.1.2
 * @author  Yithemes
 *
 */
class YITH_WCPB_Compatibility {

    /** @var YITH_WCPB_Compatibility */
    protected static $_instance;

    protected $_plugins = array();

    /** @var YITH_WCPB_Wpml_Compatibility */
    public $wpml;

    /** @return YITH_WCPB_Compatibility */
    public static function get_instance() {
        $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

        return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
    }

    /**
     * Constructor
     *
     * @access protected
     */
    protected function __construct() {
        $this->_load();
    }

    /**
     * set the plugins
     */
    protected function _set_plugins() {
        $this->_plugins = array(
            'wpml'            => array(
                'always_enabled' => true,
            )
        );
    }

    /**
     * Load Add-on classes
     */
    protected function _load() {
        $this->_set_plugins();

        foreach ( $this->_plugins as $slug => $plugin_info ) {
            $filename          = YITH_WCPB_INCLUDES_PATH . '/compatibility/class.yith-wcpb-' . $slug . '-compatibility.php';
            $premium_filename  = str_replace( '-compatibility.php', '-compatibility-premium.php', $filename );
            $premium_load      = file_exists( $premium_filename );
            $classname         = $this->get_class_name_from_slug( $slug );
            $premium_classname = $this->get_class_name_from_slug( $slug, $premium_load );
            $var               = str_replace( '-', '_', $slug );

            if ( !isset( $plugin_info[ 'always_enabled' ] ) || !$plugin_info[ 'always_enabled' ] ) {
                if ( !static::has_plugin( $slug ) )
                    continue;
            }

            if ( file_exists( $filename ) && !class_exists( $classname ) ) {
                require_once( $filename );
            }

            if ( file_exists( $premium_filename ) && !class_exists( $premium_classname ) ) {
                require_once( $premium_filename );
            }

            if ( class_exists( $classname ) && method_exists( $classname, 'get_instance' ) ) {
                $this->$var = $classname::get_instance();
            }
        }
    }

    /**
     * get the class name from slug
     *
     * @param string $slug
     *
     * @param bool   $premium_suffix
     *
     * @return string
     */
    public function get_class_name_from_slug( $slug, $premium_suffix = false ) {
        $class_slug = str_replace( '-', ' ', $slug );
        $class_slug = ucwords( $class_slug );
        $class_slug = str_replace( ' ', '_', $class_slug );

        return 'YITH_WCPB_' . $class_slug . '_Compatibility' . ( $premium_suffix ? '_Premium' : '' );
    }

    /**
     * Check if user has plugin
     *
     * @param string $plugin_name
     *
     * @author  Leanza Francesco <leanzafrancesco@gmail.com>
     * @since   1.1.2
     * @return bool
     */
    static function has_plugin( $plugin_name ) {
        return false;
    }
}