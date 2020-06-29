<?php
/**
 * Handle integration with YITH WooCommerce Cart Messages Premium
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup Premium
 * @version 1.1.0
 */

if ( ! defined( 'YITH_WACP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WACP_YWCM_Integration' ) ) {
	/**
	 * Integration class.
	 * The class manage all the integration behaviors with YITH WooCommerce Cart Messages Premium.
	 *
	 * @since 1.1.0
	 */
	class YITH_WACP_YWCM_Integration {

		/**
		 * Single instance of the class
		 *
		 * @since 1.1.0
		 * @var YITH_WACP_YWCM_Integration
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.1.0
		 * @return YITH_WACP_YWCM_Integration
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
		 * @access public
		 * @since  1.1.0
		 */
		public function __construct() {
			add_filter( 'ywcm_message_metabox', array( $this, 'add_option_meta' ), 99, 1 );
			add_action( 'yith_wacp_before_popup_content', array( $this, 'cart_messages' ), 15, 1 );
		}

		/**
		 * Add option on Cart Messages array meta box
		 *
		 * @since  1.1.0
		 * @author Francesco Licandro
		 * @param array $options And array of meta box options for cart message.
		 * @return array
		 */
		public function add_option_meta( $options ) {

			$options['ywcm_message_show_on_added_cart_popup'] = array(
				'label' => __( 'Show on "Added to Cart" popup', 'yith-woocommerce-added-to-cart-popup' ),
				'desc'  => __( 'Show this message also on YITH WooCommerce Added to Cart Popup', 'yith-woocommerce-added-to-cart-popup' ),
				'type'  => 'checkbox',
				'std'   => 'no',
			);

			return $options;
		}

		/**
		 * Add message on added to cart popup
		 *
		 * @since  1.1.0
		 * @author Francesco Licandro
		 */
		public function cart_messages() {

			global $YWCM_Instance;

			$messages      = YWCM_Cart_Message()->get_messages();
			$messages_html = '';

			if ( empty( $messages ) ) {
				return;
			}

			// Prevent check page types by removing filter.
			remove_filter( 'yith_ywcm_is_valid_message', array( $YWCM_Instance, 'is_valid_page' ), 10 );

			foreach ( $messages as $message ) {
				if ( apply_filters( 'yith_ywcm_is_valid_message', $YWCM_Instance->is_valid( $message->ID ), $message->ID ) ) {
					if ( ! get_post_meta( $message->ID, '_ywcm_message_show_on_added_cart_popup', true ) ) {
						continue;
					}

					$message_type = get_post_meta( $message->ID, '_ywcm_message_type', true );
					$layout       = ( get_post_meta( $message->ID, '_ywcm_message_layout', true ) !== '' ) ? get_post_meta( $message->ID, '_ywcm_message_layout', true ) : 'layout';
					$args         = ( method_exists( $YWCM_Instance, 'get_' . $message_type . '_args' ) ) ? $YWCM_Instance->{'get_' . $message_type . '_args'}( $message ) : false;

					if ( $args ) {
						$args['ywcm_id'] = $message->ID;

						$messages_html .= yit_plugin_get_template( YITH_YWCM_DIR, '/layouts/' . $layout . '.php', $args, true );
					}
				}
			}

			echo $messages_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

/**
 * Unique access to instance of YITH_WACP_YWCM_Integration class
 *
 * @since 1.1.0
 * @return YITH_WACP_YWCM_Integration
 */
function YITH_WACP_YWCM_Integration() { // phpcs:ignore
	return YITH_WACP_YWCM_Integration::get_instance();
}

YITH_WACP_YWCM_Integration();
