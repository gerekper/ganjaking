<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * WP Compatibility Class
 *
 * @class   YITH_WCMBS_WP_Compatibility
 * @package Yithemes
 * @since   1.2.10
 * @author  Yithemes
 *
 */
class YITH_WCMBS_WP_Compatibility {

    /**
     * @var \YITH_WCMBS_WP_Compatibility
     */
    private static $_instance;

    public $wp_version = '';

    /**
     * @return \YITH_WCMBS_WP_Compatibility
     */
    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
    }

    private function __construct() {
        global $wp_version;
        $this->wp_version = $wp_version;
    }

    /**
     * @param array $args
     *
     * @return array|int|WP_Error
     */
    public function get_terms( $args = array() ) {
        if ( $this->compare( '4.5.0', '>=' ) ) {
            return get_terms( $args );
        } else {
            $taxonomy = isset( $args[ 'taxonomy' ] ) ? $args[ 'taxonomy' ] : '';
            if ( isset( $args[ 'taxonomy' ] ) )
                unset( $args[ 'taxonomy' ] );

            return get_terms( $taxonomy, $args );
        }
    }


    /**
     * @param string      $version
     * @param string|null $operator
     *
     * @return mixed By default returns
     * -1 if the version is lower than the WP version,
     * 0 if they are equal, and
     * 1 if the version is higher than the WP version.
     *
     * When using the optional operator argument, the
     * function will return true if the relationship is the one specified
     * by the operator, false otherwise.
     */
    public function compare( $version, $operator = null ) {
        return version_compare( $version, $this->wp_version, $operator );
    }
}