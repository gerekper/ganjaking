<?php
/**
 * Handle integration with YITH WooCommerce Request A Quote
 *
 * @author YITH
 * @package YITH WooCommerce Added to Cart Popup Premium
 * @version 1.1.0
 */

if ( ! defined( 'YITH_WACP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WACP_YWRAQ_Integration' ) ) {
	/**
	 * Integration class.
	 * The class manage all the integration behaviors with YITH WooCommerce Request A Quote.
	 *
	 * @since 1.1.0
	 */
	class YITH_WACP_YWRAQ_Integration {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WACP_YWRAQ_Integration
		 * @since 1.1.0
		 */
		protected static $instance;

		/**
		 * Update raq action
		 *
		 * @var string
		 * @since 1.3.0
		 */
		public $action_update_raq = 'yith_wacp_update_raq';

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WACP_YWRAQ_Integration
		 * @since 1.1.0
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
		 * @since 1.1.0
		 */
		public function __construct() {

			add_action( 'wc_ajax_' . $this->action_update_raq, array( $this, 'update_raq_ajax' ) );
			add_action( 'wp_ajax_nopriv' . $this->action_update_raq, array( $this, 'update_raq_ajax' ) );

			// Add message for request a quote action.
			add_filter( 'yith_ywraq_ajax_add_item_json', array( $this, 'get_popup_raq_content' ), 10, 1 );
			// Add action to localized.
			add_filter( 'yith_wacp_frontend_script_localized_args', array( $this, 'add_localized_args' ), 10, 1 );
			// Handle form scripts and style.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_form_scripts' ), 99 );
		}

		/**
		 * Handle scripts ans style for selected form
		 *
		 * @return void
		 * @author Francesco Licandro
		 * @since 1.4.5
		 */
		public function enqueue_form_scripts() {
			$form = get_option( 'ywraq_inquiry_form_type', 'default' );
			if ( 'gravity-forms' === $form && class_exists( 'GFForms' ) ) {
				$form_id = get_option( 'ywraq_inquiry_gravity_forms_id', 0 );
				$form_id && GFForms::enqueue_form_scripts( $form_id, true );
			}
		}

		/**
		 * Add args to frontend localized array
		 *
		 * @param array $args The frontend localized array.
		 * @return array
		 * @since 1.3.0
		 * @author Francesco Licandro
		 */
		public function add_localized_args( $args ) {
			$args['actionUpdateRaq'] = $this->action_update_raq;
			return $args;
		}


		/**
		 * Update Request a Quote form on popup
		 *
		 * @access public
		 * @since 1.3.0
		 * @author Francesco Licandro
		 */
		public function update_raq_ajax() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] !== $this->action_update_raq || ! isset( $_REQUEST['raq'] ) ) {
				die();
			}

			$json = $this->get_popup_raq_content();
			// Refresh popup!
			wp_send_json( $json );
		}

		/**
		 * Get content html for added to cart popup on request a quote action
		 *
		 * @access public
		 * @param array $json Default json response.
		 * @param array $product_raq An array of raq args.
		 * @return array
		 * @since 1.3.0
		 */
		public function get_popup_raq_content( $json = array(), $product_raq = array() ) {

			$args = apply_filters( 'yith_wacp_get_popup_raq_content', array() );

			ob_start();

			do_action( 'yith_wacp_before_popup_raq_content', $product_raq );

			wc_get_template( 'yith-wacp-popup-raq.php', $args, '', YITH_WACP_TEMPLATE_PATH . '/' );

			do_action( 'yith_wacp_after_popup_raq_content', $product_raq );

			$json['yith_wacp_raq'] = ob_get_clean();

			return $json;
		}
	}
}

/**
 * Unique access to instance of YITH_WACP_YWRAQ_Integration class
 *
 * @return YITH_WACP_YWRAQ_Integration
 * @since 1.1.0
 */
function YITH_WACP_YWRAQ_Integration() { // phpcs:ignore
	return YITH_WACP_YWRAQ_Integration::get_instance();
}

YITH_WACP_YWRAQ_Integration();
