<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements admin features of YITH_WC_RAC_Metaboxes
 *
 * @class   YITH_WC_RAC_Metaboxes
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author YITH
 */
if ( ! class_exists( 'YITH_WC_RAC_Metaboxes' ) ) {

	class YITH_WC_RAC_Metaboxes {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_RAC_Metaboxes
		 */

		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_RAC_Metaboxes
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
		 * @author Emanuela Castorina
		 */
		public function __construct() {
			// display info cart
			add_action( 'add_meta_boxes', array( $this, 'show_info_cart' ) );
			// display cart
			add_action( 'add_meta_boxes', array( $this, 'show_cart' ) );
			// display cart action metabox
			add_action( 'add_meta_boxes', array( $this, 'show_cart_action' ) );
			// Remove metabox Publish/Update
			add_action( 'admin_menu', array( $this, 'remove_metabox' ) );

			add_action( 'edit_form_top', array( $this, 'show_return_to_list' ) );
		}

		/**
		 * Add the metabox to show the info of the current cart
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function show_info_cart() {
			add_meta_box( 'ywrac-info-cart', __( 'Cart Info', 'yith-woocommerce-recover-abandoned-cart' ), array( $this, 'show_cart_info_metabox' ), 'ywrac_cart', 'normal', 'default' );
		}

		/**
		 * Metabox to show the info of the current cart
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function show_cart_info_metabox( $post ) {

			$user_id = get_post_meta( $post->ID, '_user_id', true );

			$args = array(
				'cart_id'         => $post->ID,
				'status'          => get_post_meta( $post->ID, '_cart_status', true ),
				'last_update'     => $post->post_modified_gmt,
				'user_email'      => sanitize_email( get_post_meta( $post->ID, '_user_email', true ) ),
				'user_first_name' => sanitize_text_field( get_post_meta( $post->ID, '_user_first_name', true ) ),
				'user_last_name'  => sanitize_text_field( get_post_meta( $post->ID, '_user_last_name', true ) ),
				'user_phone'      => get_post_meta( $post->ID, '_user_phone', true ),
				'language'        => sanitize_text_field( get_post_meta( $post->ID, '_language', true ) ),
				'history'         => get_post_meta( $post->ID, '_emails_sent', true ),
				'currency'        => get_post_meta( $post->ID, '_user_currency', true ),
			);

			if ( class_exists( 'WOOCS' ) ) {
				global $WOOCS;
				$WOOCS->current_currency = get_post_meta( $post->ID, '_user_currency', true );
			}

			wc_get_template( 'admin/metabox_cart_info_content.php', $args );

		}

		/**
		 * Add the metabox to show the content of current cart
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function show_cart() {
			add_meta_box( 'ywrac-cart', __( 'Cart Content', 'yith-woocommerce-recover-abandoned-cart' ), array( $this, 'show_cart_metabox' ), 'ywrac_cart', 'normal', 'default' );
		}

		/**
		 * Metabox to show the content of current cart
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function show_cart_metabox( $post ) {
			$cart_content = maybe_unserialize( get_post_meta( $post->ID, '_cart_content', true ) );
			$subtotal     = get_post_meta( $post->ID, '_cart_subtotal', true );
			$subtotal_tax = get_post_meta( $post->ID, '_cart_subtotal_tax', true );
			$currency     = get_post_meta( $post->ID, '_user_currency', true );

			if ( $cart_content != '' ) {
				wc_get_template(
					'admin/metabox_cart_content.php',
					array(
						'cart_content' => $cart_content,
						'subtotal'     => $subtotal,
						'subtotal_tax' => $subtotal_tax,
						'currency'     => $currency,
					)
				);
			}
		}

		/**
		 * Add the metabox to show the cart action
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function show_cart_action() {
			add_meta_box( 'ywrac-cart-action', __( 'Cart Action', 'yith-woocommerce-recover-abandoned-cart' ), array( $this, 'show_cart_action_metabox' ), 'ywrac_cart', 'side', 'default' );
		}


		/**
		 * Metabox to show the cart action
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function show_cart_action_metabox( $post ) {
			$email_sent = get_post_meta( $post->ID, '_email_sent', true );
			wc_get_template(
				'admin/metabox_cart_action.php',
				array(
					'cart_id'    => $post->ID,
					'email_sent' => ( $email_sent == 'no' || $email_sent == '' ) ? __(
						'Not sent',
						'yith-woocommerce-recover-abandoned-cart'
					) : $email_sent,
				)
			);
		}

		/**
		 * Remove the metabox update/publish
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		function remove_metabox() {
			remove_meta_box( 'submitdiv', YITH_WC_Recover_Abandoned_Cart()->post_type_name, 'side' );
		}

		/**
		 * Remove the metabox update/publish
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		function show_return_to_list() {
				printf( '<a href="%1$s" title="%2$s">%2$s</a>', YITH_WC_Recover_Abandoned_Cart_Admin()->get_panel_page_uri( $tab = 'carts' ), __( 'Return to Abandoned Cart List', 'yith-woocommerce-abandoned-cart' ) );
		}
	}
}

/**
 * Unique access to instance of YITH_WC_RAC_Metaboxes class
 *
 * @return \YITH_WC_RAC_Metaboxes
 */
function YITH_WC_RAC_Metaboxes() {
	return YITH_WC_RAC_Metaboxes::get_instance();
}
