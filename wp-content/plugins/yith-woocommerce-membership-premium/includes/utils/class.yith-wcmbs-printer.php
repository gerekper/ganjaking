<?php
/**
 * Printer class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCMBS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCMBS_Printer' ) ) {

    /**
     * YITH WooCommerce Booking Printer
     *
     * @since 1.0.0
     */
    class YITH_WCMBS_Printer {
        /**
         * Single instance of the class
         *
         * @var \YITH_WCMBS_Printer
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCMBS_Printer
         * @since 1.0.0
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Constructor
         *
         */
        public function __construct() {

        }

        public function print_fields( $args = array() ) {
            foreach ( $args as $field_args ) {
                $this->print_field( $field_args );
            }
        }

        public function print_field( $args = array() ) {
            $default_args = array(
                'type'              => '',
                'id'                => '',
                'name'              => '',
                'value'             => '',
                'class'             => '',
                'custom_attributes' => '',
                'for'               => '',
                'options'           => array(),
                'data'              => array(),
                'title'             => '',
                'fields'            => array(),
                'help_tip'          => '',
                'section_html_tag'  => 'p'
            );
            $args         = wp_parse_args( $args, $default_args );

            $type     = $args[ 'type' ];
            $title    = $args[ 'title' ];
            $help_tip = $args[ 'help_tip' ];

            if ( !empty( $title ) ) {
                $this->print_field( array(
                    'type'  => 'label',
                    'value' => $title,
                    'for'   => $args[ 'id' ]
                ) );
            }

            switch ( $type ) {
                case 'section':
                    $fields = $args[ 'fields' ];
                    unset( $args[ 'fields' ] );

                    $args[ 'type' ] = 'section-start';
                    $this->print_field( $args );

                    foreach ( $fields as $field_args ) {
                        $this->print_field( $field_args );
                    }

                    $args[ 'type' ] = 'section-end';
                    $this->print_field( $args );
                    break;
                default:
                    if ( file_exists( YITH_WCMBS_TEMPLATE_PATH . '/printer/types/' . $type . '.php' ) ) {
                        wc_get_template( '/printer/types/' . $type . '.php', $args, '', YITH_WCMBS_TEMPLATE_PATH );
                    }
                    break;
            }

            if ( !empty( $help_tip ) ) {
                $this->print_field( array(
                    'type'  => 'help-tip',
                    'value' => $help_tip,
                ) );
            }
        }
    }
}

/**
 * Unique access to instance of YITH_WCMBS_Printer class
 *
 * @return \YITH_WCMBS_Printer
 * @since 1.0.0
 */
function YITH_WCMBS_Printer() {
    return YITH_WCMBS_Printer::get_instance();
}
