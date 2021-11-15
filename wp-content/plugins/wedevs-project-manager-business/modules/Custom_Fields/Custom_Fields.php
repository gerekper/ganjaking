<?php
/**
 * Module Name: Custom Fields
 * Description: Generate invoice for your projects anytime; print, download and send emails to your client.
 * Module URI: https://wedevs.com/weforms/
 * Thumbnail URL: /views/assets/images/sub-task.png
 * Author: weDevs
 * Version: 1.0
 * Author URI: https://wedevs.com
 */


if ( ! class_exists( 'custom_fields' ) ) :
    final class Custom_Fields {

        /**
         * This plugin's instance.
         *
         * @var custom_field
         * @since 1.0.0
         */
        private static $instance;

        /**
         * Main custom_field Instance.
         *
         * Insures that only one instance of custom_field exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 1.0.0
         * @static
         * @return object|custom_field The one true custom_field
         */
        public static function instance() {
            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Custom_Field ) ) {
                self::$instance = new self();
                self::$instance->define_constants();
                self::$instance->includes();
                self::$instance->dependency_class_instance();
            }
            return self::$instance;
        }

        /**
         * Load actions
         *
         * @return void
         */
        private function includes() {
            //include( custom_field_PATH . '/helpers/functions.php' );
        }

        /**
         * Define woogool Constants
         *
         * @return type
         */
        private function define_constants() {
            $this->define( 'PM_PRO_CUSTOM_FIELD_PATH', dirname( __FILE__ ) );
            $this->define( 'PM_PRO_CUSTOM_FIELD_VIEW_PATH', dirname( __FILE__ ) . '/views' );
            $this->define( 'PM_PRO_CUSTOM_FIELD_VIEW_URL', plugin_dir_url( __FILE__ ) . '/views' );
        }

        /**
         * Define constant if not already set
         *
         * @param  string $name
         * @param  string|bool $value
         * @return type
         */
        private function define( $name, $value ) {
            if ( ! defined( $name ) ) {
                define( $name, $value );
            }
        }

        private function dependency_class_instance() {
            \WeDevs\PM_Pro\Modules\Custom_Fields\Core\Scripts\Scripts::instance();
            \WeDevs\PM_Pro\Modules\Custom_Fields\Core\Actions::instance();
            \WeDevs\PM_Pro\Modules\Custom_Fields\Core\Filters::instance();
        }

    }

endif;


/**
 * The main function for that returns custom_field
 *
 */
function pm_pro_custom_field() {
    return custom_fields::instance();
}

pm_pro_custom_field();





