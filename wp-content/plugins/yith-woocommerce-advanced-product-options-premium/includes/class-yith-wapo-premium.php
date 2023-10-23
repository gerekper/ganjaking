<?php
/**
 * WAPO Premium Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_Premium' ) ) {

	/**
	 *  YITH_WAPO Premium Class
	 */
	class YITH_WAPO_Premium extends YITH_WAPO {

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WAPO_Premium
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
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			parent::__construct();

            /**
             * Register plugin to licence/update system.
             */

            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

            add_action( 'init', array( $this, 'load_modules' ), 0 );

            // YITH_WAPO_Compatibility.
            yith_wapo_compatibility();

            // Plugin options
            add_filter( 'woocommerce_order_item_get_formatted_meta_data', array( $this, 'yith_wapo_maybe_hide_options_on_email' ), 10, 2 );

		}

        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since 2.0.0
         */
        public function register_plugin_for_activation() {
            if ( function_exists( 'YIT_Plugin_Licence' ) ) {
                YIT_Plugin_Licence()->register( YITH_WAPO_INIT, YITH_WAPO_SECRET_KEY, YITH_WAPO_SLUG );
            }
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since 2.0.0
         */
        public function register_plugin_for_updates() {
            if ( function_exists( 'YIT_Upgrade' ) ) {
                YIT_Upgrade()->register( YITH_WAPO_SLUG, YITH_WAPO_INIT );
            }
        }

        /**
         * Load plugin modules
         *
         * @return void
         */
        public function load_modules() {

            // todo:remove yith_wapo_settings_disable_wccl option from the database.

            if ( ! function_exists( 'YITH_WCCL' ) ) {
                require_once YITH_WAPO_DIR . 'modules/color-label-variations/class-yith-wapo-color-label-variations.php';
            }
        }

		/**
		 * Get available addon types
		 *
		 * @return array
		 * @since 2.0.0
		 */
		public function get_available_addon_types() {

            $available_addon_types = array(
                'checkbox',
                'radio',
                'text',
                'textarea',
                'color',
                'number',
                'select',
                'label',
                'product',
                'date',
                'file',
                'colorpicker'
            );

            return $available_addon_types;
		}

        /**
         * Hide options on email depending on plugin option (yith_wapo_hide_options_in_order_email).
         *
         * @param array  $meta Meta value of email.
         * @param object $order_item The order item.
         *
         * @return mixed
         * @throws Exception The exception.
         */
        public function yith_wapo_maybe_hide_options_on_email( $meta, $order_item ) {

            if ( 'yes' === get_option( 'yith_wapo_hide_options_in_order_email', 'no' ) ) {

                $is_resend = isset( $_POST['wc_order_action'] ) ? 'send_order_details' === wc_clean( wp_unslash( $_POST['wc_order_action'] ) ) : false; //phpcs:ignore

                if ( ! $is_resend && ( is_admin() || is_wc_endpoint_url() ) ) {
                    return $meta;
                }

                $labels    = array();
                $item_id   = $order_item->get_id(); // ???
                $meta_data = wc_get_order_item_meta( $item_id, '_ywapo_meta_data', true );
                if ( $meta_data && is_array( $meta_data ) ) {
                    foreach ( $meta_data as $index => $option ) {
                        foreach ( $option as $key => $value ) {
                            if ( $key && '' !== $value ) {
                                $values = self::get_instance()->split_addon_and_option_ids( $key, $value );

                                $addon_id  = $values['addon_id'];
                                $option_id = $values['option_id'];

                                $label    = yith_wapo_get_option_label( $addon_id, $option_id );
                                $labels[] = $label;
                            }
                        }
                    }
                }

                foreach ( $meta as $meta_id => $meta_value ) {
                    foreach ( $labels as $label ) {
                        if ( $label === $meta_value->key ) {
                            unset( $meta[ $meta_id ] );
                        }
                    }
                }
            }

            return apply_filters( 'yith_wapo_options_in_order_email_meta', $meta, $order_item );
        }


	}
}
