<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Custom ThankYou Page for Woocommerce
 */

if ( ! defined( 'YITH_CTPW_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Custom_Thankyou_Page_Preview' ) ) {
	/**
	 * Thank you page preview class
	 *
	 * The class allow to have a preview of a page as Custom Thank you page.
	 *
	 * @class      YITH_Custom_Thankyou_Page_Preview
	 * @package    YITH Custom ThankYou Page for Woocommerce
	 * @since      1.1.7
	 * @author     YITH
	 */
	class YITH_Custom_Thankyou_Page_Preview {
		/**
		 * Main Class Instance
		 *
		 * @var YITH_Custom_Thankyou_Page_Preview
		 * @since 1.1.7
		 * @access protected
		 */
		protected static $_instance = null;

		/**
		 * Construct
		 *
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since  1.1.7
		 */
		public function __construct() {

			//phpcs:ignore
			// add_filter('parse_query', array( $this, 'hide_dummy_order'))

			/* add Preview as Custom Thank You Page action in Post Table row actions */
			add_filter( 'page_row_actions', array( $this, 'add_yctpw_preview_row_action' ), 10, 2 );

			if ( isset( $_GET['preview'] ) && $_GET['preview'] ) { //phpcs:ignore
				return;
			}

			/* add meta box to Page post type */
			add_action( 'add_meta_boxes', array( $this, 'add_metabox' ), 10 );

			/* set ajax method call and load script to create dummy order is needed */
			add_action( 'wp_ajax_yith_ctpw_create_dummy_order', array( $this, 'yith_ctpw_create_dummy_order' ), 10 );
			add_action( 'wp_ajax_yith_ctpw_get_preview_link', array( $this, 'yith_ctpw_get_preview_link' ), 10 );
			/* load admin side scripts */
			add_action( 'admin_enqueue_scripts', array( $this, 'load_script' ) );

			/* clean plugin option yith_ctpw_dummy_order_id if the related order is removed or moved to trash */
			add_action( 'before_delete_post', array( $this, 'clean_dummy_order_option' ), 10, 1 );
			add_action( 'wp_trash_post', array( $this, 'clean_dummy_order_option' ), 10, 1 );


		}

		/**
		 *
		 * Add action View as YITH Thank You page to Pages Table
		 *
		 * @param array   $actions .
		 * @param WP_Post $post .
		 *
		 * @return array
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since  1.1.8
		 */
		public function add_yctpw_preview_row_action( $actions, $post ) {
			if ( ! get_post_meta( $post->ID, '_elementor_edit_mode', true ) ) {

				$avoid_pages = apply_filters(
					'yctpw_avoid_pages',
					array(
						get_option( 'woocommerce_checkout_page_id' ),
						get_option( 'woocommerce_cart_page_id' ),
						get_option( 'woocommerce_shop_page_id' ),
						get_option( 'woocommerce_myaccount_page_id' ),
					)
				);

				if ( ! isset( $actions['yctpw_preview'] ) && ! in_array( $post->ID, $avoid_pages ) ) {

					/* get url args by order */
					$url_args = yith_ctpw_get_url_order_args( yith_ctpw_get_available_order_to_preview(), $post->ID );
					/* get full url adding the args to main page url */
					$full_url = add_query_arg( $url_args, get_permalink( $post ) );

					$actions['yctpw_preview'] = '<a target="_blank" href="' . $full_url . '">' . esc_html__( 'View as YITH Thank You page', 'yith-custom-thankyou-page-for-woocommerce' ) . '</a>';
				}
			}

			return $actions;
		}

		/**
		 * Check if we have a dummy order to use
		 *
		 * @return mixed|bool
		 * @since  1.1.7
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 */
		public function has_dummy_order() {
			if ( get_option( 'yith_ctpw_dummy_order_id', '' ) > 0 && get_option( 'yith_ctpw_dummy_order_id', '' ) !== '' && get_post_type( get_option( 'yith_ctpw_dummy_order_id', '' ) ) === 'shop_order' ) {
				return get_option( 'yith_ctpw_dummy_order_id' );
			} else {
				return false;
			}

		}

		/**
		 * Clean plugin option yith_ctpw_dummy_order_id if the related order is removed
		 *
		 * @param int $pid Post Id.
		 *
		 * @return void
		 * @since  1.1.7
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 */
		public function clean_dummy_order_option( $pid ) {
			if ( get_post_type( $pid ) === 'shop_order' && $this->has_dummy_order() === $pid ) {
				update_option( 'yith_ctpw_dummy_order_id', 0 );
			}

		}

		/**
		 * Hide Created Dummy Order
		 *
		 * @param WP_Query $query object for current query.
		 *
		 * @return WP_Query
		 * @since  1.1.7
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 */
		public function hide_dummy_order( $query ) {
			global $pagenow;
			if ( is_admin() && $pagenow === 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'shop_order' ) { //phpcs:ignore
				$query->set( 'post__not_in', array( 242 ) );
			}

			return $query;
		}

		/**
		 * Load script for this class methods
		 *
		 * @return void
		 * @since  1.1.7
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 */
		public function load_script() {

			global $pagenow;

			if ( isset( $_GET['post'] ) && get_post_type( $_GET['post'] ) === 'page' || ( $pagenow === 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'page' ) ) {//phpcs:ignore

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				/* check if we are with Gutenberg Editor or Classic one */
				if ( get_current_screen()->is_block_editor() ) {
					wp_enqueue_script(
						'yctpw-preview',
						YITH_CTPW_ASSETS_URL . 'js/yith_ctpw_preview' . $suffix . '.js',
						array(
							'jquery',
							'wp-data',
							'wp-editor',
							'autosave',
							'wp-plugins',
							'wp-edit-post',
							'wp-element',
						),
						YITH_CTPW_VERSION,
						false
					);
				} else {
					/* Classic Editor */
					wp_enqueue_script(
						'yctpw-preview',
						YITH_CTPW_ASSETS_URL . 'js/yith_ctpw_preview_classic_editor' . $suffix . '.js',
						array(
							'jquery',
							'wp-data',
							'wp-editor',
							'autosave',
						),
						YITH_CTPW_VERSION,
						false
					);
				}

				/* check if we have a random order to test in order to create a preview link */
				$o            = yith_ctpw_get_available_order_to_preview();
				$preview_link = false;

				if ( isset ( $_GET['post'] ) ) { //phpcs:ignore
					$page_id = $_GET['post']; //phpcs:ignore
				} else {
					$page_id = 0;
				}

				if ( $o ) {
					$preview_link  = 'order-received=' . $o->get_id();
					$preview_link .= '&key=' . $o->get_order_key();
					$preview_link .= '&ctpw=' . $page_id;
				}

				if ( get_option( 'yith_ctpw_dummy_order_id', '' ) > 0 && get_option( 'yith_ctpw_dummy_order_id', '' ) !== '' && get_post_type( get_option( 'yith_ctpw_dummy_order_id', '' ) ) === 'shop_order' ) {
					$preview_message = esc_html__( 'The Preview will be done using the dummy order ', 'yith-custom-thankyou-page-for-woocommerce' ) . get_option( 'yith_ctpw_dummy_order_id' );
				} else {
					$preview_message = esc_html__( 'The Preview will be done using a random completed order.', 'yith-custom-thankyou-page-for-woocommerce' );
				}


				$yctpw_preview_args = array(
					'admin_ajax_url'                  => admin_url( 'admin-ajax.php' ),
					'post_id'                         => get_the_ID(),
					'wp_nonce'                        => wp_create_nonce( 'wp_rest' ),
					'sidebar_title'                   => esc_html__( 'YITH Custom Thankyou Page', 'yith-custom-thankyou-page-for-woocommerce' ),
					'button_title'                    => esc_html__( 'Preview as Yith Custom Thank You Page', 'yith-custom-thankyou-page-for-woocommerce' ),
					'preview_message'                 => $preview_message,
					'preview_message_dummy_order'     => esc_html__( 'The Preview will be done using the dummy order', 'yith-custom-thankyou-page-for-woocommerce' ),
					'preview_link'                    => ( ! empty( $o ) ) ? $preview_link : false,
					'create_dummy_order_button_title' => esc_html__( 'Create Dummy Order', 'yith-custom-thankyou-page-for-woocommerce' ),
					'create_dummy_order_info'         => esc_html__( 'In order to test this page as a YITH Custom Thank You Page, you need a completed WooCommerce order, but it seems that there is no completed order available at the moment. You can click on the button below to create a dummy order.', 'yith-custom-thankyou-page-for-woocommerce' ),
				);

				wp_localize_script( 'yctpw-preview', 'yctpw_preview_args', $yctpw_preview_args );

			}

		}

		/**
		 * Add Preview Metabox to Page post type
		 *
		 * @return void
		 * @author YITH
		 * @since 1.1.7
		 */
		public function add_metabox() {
			/* if Gutenberg Editor we return, because in that case we add the plugin sidebar by js */
			if ( get_current_screen()->is_block_editor() ) {
				return;
			}

			global $pagenow;

			// avoid to work on woocommerce pages
			// APPLY_FILTER: yctpw_avoid_pages : array of pages where not add the metabox.
			$avoid_pages = apply_filters(
				'yctpw_avoid_pages',
				array(
					get_option( 'woocommerce_checkout_page_id' ),
					get_option( 'woocommerce_cart_page_id' ),
					get_option( 'woocommerce_shop_page_id' ),
					get_option( 'woocommerce_myaccount_page_id' ),
				)
			);
			if ( ( isset( $_GET['post'] ) && get_post_type( $_GET['post'] ) === 'page' && ! in_array( $_GET['post'], $avoid_pages ) ) || ( $pagenow === 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'page' ) ) { //phpcs:ignore

				$args = require_once( YITH_CTPW_OPTIONS_PATH . '/metabox/yctpw-metabox-page.php' );
				if ( ! function_exists( 'YIT_Metabox' ) ) {
					require_once( YITH_CTPW_PATH . 'plugin-fw/yit-plugin.php' );
				}
				$metabox = YIT_Metabox( 'yith-yctpw-metabox-page' );
				$metabox->init( $args );
			}

		}


		/**
		 * Create a Dummy Order to make the button Preview Available
		 *
		 * @return void
		 * @author YITH
		 * @since 1.1.7
		 * @throws . . .
		 */
		public function yith_ctpw_create_dummy_order() {
			global $woocommerce;

			/* result info array */
			$result = array(
				'response'  => false,
				'message'   => '',
				'order_id'  => '',
				'order_key' => '',
			);

			$address = array(
				'first_name' => 'YITH',
				'last_name'  => 'Test',
				'company'    => 'Yithemes',
				'email'      => 'yit@yopmail.com',
				'phone'      => '555-555-5555',
				'address_1'  => 'Yith Address',
				'address_2'  => '',
				'city'       => 'Santa Cruz de Tenerife',
				'state'      => 'Es',
				'postcode'   => '30002',
				'country'    => 'Es',
			);

			// Now we create the order.
			$order = wc_create_order();

			if ( is_wp_error( $order ) ) {

				$result['message'] = $order->get_error_message();
				echo wp_json_encode( $result );
			}


			/* get some random products to create the dummy order */
			$rp = wc_get_products(
				array(
					'status'  => 'publish',
					'limit'   => 2,
					'type'    => 'simple',
					'orderby' => 'rand',
				)
			);

			foreach ( $rp as $p ) {
				$order->add_product( $p, 1 );
			}

			$order->set_address( $address, 'billing' );

			$order->calculate_totals();
			if ( $order->update_status( 'wc-completed', 'Dummy Order created by Yith Custom Thank you Page for Woocommerce', true ) ) {
				$result['response']  = true;
				$result['order_id']  = $order->get_id();
				$result['order_key'] = $order->get_order_key();

				// if all is good we can save this order as the dummy order to use.
				update_option( 'yith_ctpw_dummy_order_id', $order->get_id() );
			} else {
				$result['message'] = esc_html__( 'Cannot set the Order to Complete', 'yith-custom-thankyou-page-for-woocommerce' );
			}

			echo wp_json_encode( $result );
			wp_die();
		}


		/**
		 * Get Preview Link
		 *
		 * @return void
		 * @author Armando Liccardo <armando.liccardo@yithemes.com>
		 * @since 1.1.7
		 */
		public function yith_ctpw_get_preview_link() {

			/* if we have a preview link we will get it */
			if ( isset( $_POST['has_preview'] ) && 'yes' === $_POST['has_preview'] ) { //phpcs:ignore
				$preview_query_args['preview_id']    = sanitize_key( wp_unslash( $_POST['post_id'] ) ); //phpcs:ignore
				$preview_query_args['preview_nonce'] = wp_create_nonce( 'post_preview_' . $_POST['post_id'] ); //phpcs:ignore

				$url = get_preview_post_link( $_POST['post_id'], $preview_query_args );

			} else {
				/* otherwise we will use the page link */
				$url = get_post_permalink( $_POST['post_id'] );
			}

			echo $url;

			wp_die();


		}

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_Custom_Thankyou_Page_Preview
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since 1.1.7
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	} /* end of class */


}
