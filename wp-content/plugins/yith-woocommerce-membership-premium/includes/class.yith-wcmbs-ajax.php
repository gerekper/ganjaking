<?php
/**
 * AJAX class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership Premium
 * @version 1.0.0
 * @since   1.2.9
 */


if ( ! defined( 'YITH_WCMBS' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_AJAX' ) ) {
	/**
	 * YITH WooCommerce Membership Premium AJAX
	 */
	class YITH_WCMBS_AJAX {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCMBS_AJAX
		 */
		protected static $_instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCMBS_AJAX
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * Constructor
		 *
		 * @return YITH_WCMBS_AJAX
		 */
		public function __construct() {
			$ajax_actions = array(
				'json_search_posts',
				'download_product_update',
				'get_plan_post_type_items',
			);

			foreach ( $ajax_actions as $ajax_action ) {
				add_action( 'wp_ajax_yith_wcmbs_' . $ajax_action, array( $this, $ajax_action ) );
				add_action( 'wp_ajax_nopriv_yith_wcmbs_' . $ajax_action, array( $this, $ajax_action ) );
			}
		}


		/**
		 * Post Search
		 */
		public function json_search_posts() {
			check_ajax_referer( 'search-posts', 'security' );

			$search_term = isset( $_REQUEST['term']['term'] ) ? $_REQUEST['term']['term'] : $_REQUEST['term'];

			$term = (string) wc_clean( stripslashes( $search_term ) );

			$exclude = array();
			$include = array();

			if ( empty( $term ) ) {
				die();
			}

			if ( ! empty( $_GET['exclude'] ) ) {
				$exclude = array_map( 'intval', explode( ',', $_GET['exclude'] ) );
			}
			if ( ! empty( $_GET['include'] ) ) {
				$include = array_map( 'intval', explode( ',', $_GET['include'] ) );
			}

			$post_type = ! empty( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : 'post';

			$found_posts = array();

			$args = array(
				'post_type'        => $post_type,
				'post_status'      => 'publish',
				'numberposts'      => - 1,
				'orderby'          => 'title',
				'order'            => 'asc',
				'post_parent'      => 0,
				'suppress_filters' => 0,
				'include'          => $include,
				's'                => $term,
				'fields'           => 'ids',
				'exclude'          => $exclude,
			);

			$posts = get_posts( $args );

			if ( ! empty( $posts ) ) {
				foreach ( $posts as $post_id ) {
					if ( ! current_user_can( 'read_product', $post_id ) ) {
						continue;
					}

					$found_posts[ $post_id ] = rawurldecode( get_the_title( $post_id ) );
				}
			}
			$found_posts = apply_filters( 'yith_wcmbs_json_search_found_posts', $found_posts );

			wp_send_json( $found_posts );
		}

		/**
		 * Update box or button when downloading a product
		 */
		public function download_product_update() {
			$product_id = $_POST['product_id'];
			$type       = $_POST['type'];

			if ( $type === 'box' ) {
				$html = do_shortcode( "[membership_download_product_links layout='box' id={$product_id}]" );
			} else {
				$html = do_shortcode( "[membership_download_product_links id={$product_id}]" );
			}

			wp_send_json( array( 'html' => $html ) );
		}

		/**
		 * Get the items for a specific plan and post type
		 */
		public function get_plan_post_type_items() {
			$membership_id = $_POST['membership_id'];
			$plan_id       = $_POST['plan_id'];
			$post_type     = $_POST['post_type'];
			$page          = $_POST['page'];

			check_ajax_referer( "yith-wcmbs-get-plan-{$post_type}-items", 'security' );

			try {
				$plan = yith_wcmbs_get_plan( $plan_id );
				if ( ! $plan ) {
					throw new Exception( __( 'Error: invalid plan', 'yith-woocommerce-membership' ) );
				}

				$membership = false;

				if ( ! yith_wcmbs_has_full_access() ) {

					if ( ! $membership_id ) {
						throw new Exception( __( 'Error: missing membership ID parameter', 'yith-woocommerce-membership' ) );
					}

					$membership = yith_wcmbs_get_membership( $membership_id );
					if ( ! $membership ) {
						throw new Exception( __( 'Error: invalid membership', 'yith-woocommerce-membership' ) );
					}

					$membership_plan = $membership->get_plan();

					if ( ! $membership_plan || $membership_plan->get_id() !== $plan->get_id() ) {
						throw new Exception( __( 'Error: invalid membership and plan', 'yith-woocommerce-membership' ) );
					}

					if ( ! $membership->get_user_id() || get_current_user_id() !== $membership->get_user_id() ) {
						throw new Exception( __( 'Error: invalid membership and plan', 'yith-woocommerce-membership' ) );
					}
				}

				ob_start();
				wc_get_template( '/membership/membership-plan-post-type-items.php', compact( 'plan', 'membership', 'post_type', 'page' ), '', YITH_WCMBS_TEMPLATE_PATH );

				wp_send_json( array(
								  'success' => true,
								  'html'    => ob_get_clean(),
							  ) );

			} catch ( Exception $e ) {
				wp_send_json( array(
								  'success' => false,
								  'error'   => $e->getMessage(),
							  ) );
			}
		}
	}
}
