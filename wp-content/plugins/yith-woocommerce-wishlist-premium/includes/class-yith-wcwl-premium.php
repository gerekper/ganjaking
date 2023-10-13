<?php
/**
 * Init premium features of the plugin
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Premium' ) ) {
	/**
	 * WooCommerce Wishlist Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Premium extends YITH_WCWL_Extended {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCWL_Premium
		 * @since 2.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCWL_Premium
		 * @since 2.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			// register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			// on sale item handling.
			add_action( 'yith_wcwl_item_is_on_sale', array( $this, 'schedule_on_sale_item_emails' ), 10, 1 );
		}

		/**
		 * Init an array of plugin emails
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 */
		public function init_plugin_emails_array() {
			$this->emails = array(
				'yith_wcwl_back_in_stock',
				'yith_wcwl_on_sale_item',
				'estimate_mail',
				'yith_wcwl_promotion_mail',
			);
		}

		/* === ITEM METHODS === */

		/**
		 * Retrieve first list of current user where a specific product occurs; if no wishlist is found, returns false
		 *
		 * @param int $product_id Product id.
		 * @return \YITH_WCWL_Wishlist|bool First wishlist found where the product occurs (system will privilege default lists)
		 */
		public function get_wishlist_for_product( $product_id ) {
			$items = YITH_WCWL_Wishlist_Factory::get_wishlist_items(
				array(
					'product_id'  => $product_id,
					'wishlist_id' => 'all',
					'limit'       => 1,
					'orderby'     => 'dateadded',
					'order'       => 'DESC',
				)
			);

			if ( ! $items ) {
				return false;
			}

			$item = array_shift( $items );

			/**
			 * APPLY_FILTERS: yith_wcwl_wishlist_for_product
			 *
			 * Filter the first wishlist of current user where a specific product is found.
			 *
			 * @param YITH_WCWL_Wishlist $wishlist Wishlist object
			 * @param int                $product_id Product ID
			 *
			 * @return YITH_WCWL_Wishlist
			 */
			return apply_filters( 'yith_wcwl_wishlist_for_product', $item->get_wishlist(), $product_id );
		}

		/* === WISHLIST METHODS === */

		/**
		 * Add a new wishlist for the user.
		 *
		 * @param array $atts Add to Wishlist info:
		 * [
		 *   'wishlist_name'
		 *   'wishlist_visibility'
		 *   'user_id'
		 *   'session_id'
		 * ].
		 *
		 * @return YITH_WCWL_Wishlist
		 * @throws YITH_WCWL_Exception When something goes wrong with creation.
		 * @since 3.0.0
		 */
		public function add_wishlist( $atts = array() ) {
			$defaults = array(
				'wishlist_name'       => false,
				'wishlist_visibility' => 0,
				'user_id'             => false,
				'session_id'          => false,
			);

			$atts = empty( $atts ) && ! empty( $this->details ) ? $this->details : $atts;
			$atts = ! empty( $atts ) ? $atts : $_REQUEST; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$atts = wp_parse_args( $atts, $defaults );

			// filtering params.

			/**
			 * APPLY_FILTERS: yith_wcwl_adding_to_wishlist_wishlist_name
			 *
			 * Filter the name of the wishlist to be created.
			 *
			 * @param string $wishlist_name Wishlist name
			 *
			 * @return string
			 */
			$wishlist_name = apply_filters( 'yith_wcwl_adding_to_wishlist_wishlist_name', $atts['wishlist_name'] );

			/**
			 * APPLY_FILTERS: yith_wcwl_adding_to_wishlist_wishlist_visibility
			 *
			 * Filter the visibility of the wishlist to be created.
			 *
			 * @param int $wishlist_visibility Wishlist visibility
			 *
			 * @return int
			 */
			$wishlist_visibility = apply_filters( 'yith_wcwl_adding_to_wishlist_wishlist_visibility', in_array( (int) $atts['wishlist_visibility'], array( 0, 1, 2 ), true ) ? $atts['wishlist_visibility'] : 0 );

			/**
			 * APPLY_FILTERS: yith_wcwl_adding_to_wishlist_user_id
			 *
			 * Filter the user ID saved in the wishlist.
			 *
			 * @param int $user_id User ID
			 *
			 * @return int
			 */
			$user_id    = apply_filters( 'yith_wcwl_adding_to_wishlist_user_id', intval( $atts['user_id'] ) );
			$session_id = apply_filters( 'yith_wcwl_adding_to_wishlist_user_id', $atts['session_id'] );

			if ( ! $wishlist_name ) {
				throw new YITH_WCWL_Exception( esc_html__( 'Wishlist name is required', 'yith-woocommerce-wishlist' ), 0 );
			} elseif ( strlen( $wishlist_name ) >= 65535 ) {
				throw new YITH_WCWL_Exception( esc_html__( 'Wishlist name exceeds the maximum number of characters allowed', 'yith-woocommerce-wishlist' ), 0 );
			}

			$wishlist_name = sanitize_text_field( $wishlist_name );
			$wishlist_slug = sanitize_title_with_dashes( $wishlist_name );
			$session_id    = sanitize_title_with_dashes( $session_id );

			$new_wishlist = new YITH_WCWL_Wishlist();

			// set properties before saving.
			$new_wishlist->set_slug( $wishlist_slug );
			$new_wishlist->set_name( $wishlist_name );
			$new_wishlist->set_privacy( $wishlist_visibility );
			$new_wishlist->set_user_id( $user_id );
			$new_wishlist->set_session_id( $session_id );

			$new_wishlist->save();

			/**
			 * DO_ACTION: yith_wcwl_after_add_wishlist
			 *
			 * Allows to fire some action when a new wishlist has been created.
			 *
			 * @param YITH_WCWL_Wishlist $new_wishlist New wishlist object
			 */
			do_action( 'yith_wcwl_after_add_wishlist', $new_wishlist );

			return $new_wishlist;
		}

		/**
		 * Update wishlist with arguments passed as second parameter
		 *
		 * @param int   $wishlist_id Wishlist id.
		 * @param array $args Array of parameters to use for update query.
		 * @return void
		 * @throws YITH_WCWL_Exception When something goes wrong with update.
		 * @since 2.0.0
		 */
		public function update_wishlist( $wishlist_id, $args = array() ) {
			$wishlist = YITH_WCWL_Wishlist_Factory::get_wishlist( $wishlist_id );

			if ( ! $wishlist ) {
				throw new YITH_WCWL_Exception( esc_html__( 'Couldn\'t find any wishlist with the provided ID', 'yith-woocommerce-wishlist' ), 0 );
			}

			if ( ! $wishlist->current_user_can( 'update_wishlist' ) ) {
				throw new YITH_WCWL_Exception( esc_html__( 'There was an error while processing your request; please, try later', 'yith-woocommerce-wishlist' ), 0 ); // @since 3.0.7
			}

			if ( isset( $args['wishlist_name'] ) ) {
				$wishlist_name = $args['wishlist_name'];

				if ( ! $wishlist_name ) {
					throw new YITH_WCWL_Exception( esc_html__( 'Wishlist name is required', 'yith-woocommerce-wishlist' ), 0 );
				} elseif ( strlen( $wishlist_name ) >= 65535 ) {
					throw new YITH_WCWL_Exception( esc_html__( 'Wishlist name exceeds the maximum number of characters allowed', 'yith-woocommerce-wishlist' ), 0 );
				}

				$wishlist->set_name( $args['wishlist_name'] );
			}

			if ( isset( $args['wishlist_visibility'] ) || isset( $args['wishlist_privacy'] ) ) {
				$wishlist_visibility = isset( $args['wishlist_visibility'] ) ? $args['wishlist_visibility'] : $args['wishlist_privacy'];
				$wishlist_visibility = in_array( (int) $wishlist_visibility, array( 0, 1, 2 ), true ) ? $wishlist_visibility : 0;
				$wishlist->set_privacy( $wishlist_visibility );
			}

			$wishlist->save();
		}

		/**
		 * Delete indicated wishlist
		 *
		 * @param int $wishlist_id Wishlist id.
		 * @throws YITH_WCWL_Exception When something goes wrong with deletion.
		 * @return void
		 * @since 3.0.0
		 */
		public function remove_wishlist( $wishlist_id ) {
			$wishlist = YITH_WCWL_Wishlist_Factory::get_wishlist( $wishlist_id );

			if ( ! $wishlist ) {
				throw new YITH_WCWL_Exception( esc_html__( 'Couldn\'t find any wishlist with the provided ID', 'yith-woocommerce-wishlist' ), 0 );
			}

			if ( ! $wishlist->current_user_can( 'remove_wishlist' ) ) {
				throw new YITH_WCWL_Exception( esc_html__( 'There was an error while processing your request; please, try later', 'yith-woocommerce-wishlist' ), 0 ); // @since 3.0.7
			}

			$wishlist->delete();
		}

		/* === ITEM ON SALE HANDLING === */

		/**
		 * Schedule on sale item email notification when an item switches to on sale
		 *
		 * @param \YITH_WCWL_Wishlist_Item $item Item on sale.
		 * @return void
		 */
		public function schedule_on_sale_item_emails( $item ) {
			$product_id = $item->get_product_id();
			$product    = $item->get_product();
			$user_id    = $item->get_user_id();
			$user       = $item->get_user();

			if ( ! $user ) {
				return;
			}

			// skip if email ain't active.
			$email_options = get_option( 'woocommerce_yith_wcwl_on_sale_item_settings', array() );

			if ( ! isset( $email_options['enabled'] ) || 'yes' !== $email_options['enabled'] ) {
				return;
			}

			// skip if product is on exclusion list.
			$product_exclusions = ! empty( $email_options['product_exclusions'] ) ? array_map( 'absint', $email_options['product_exclusions'] ) : false;

			if ( $product_exclusions && in_array( $product_id, $product_exclusions, true ) ) {
				return;
			}

			// skip if product category is on exclusion list.
			$product_categories = $product->get_category_ids();

			if ( ! empty( $email_options['category_exclusions'] ) && array_intersect( $product_categories, $email_options['category_exclusions'] ) ) {
				return;
			}

			// queue handling.
			$queue        = get_option( 'yith_wcwl_on_sale_item_queue', array() );
			$unsubscribed = get_option( 'yith_wcwl_unsubscribed_users', array() );

			// skip if user unsubscribed.
			if ( in_array( $user->user_email, $unsubscribed, true ) ) {
				return;
			}

			if ( ! isset( $queue[ $user_id ] ) ) {
				$queue[ $user_id ] = array(
					$item->get_product_id() => $item->get_id(),
				);
			} else {
				$queue[ $user_id ][ $item->get_product_id() ] = $item->get_id();
			}

			update_option( 'yith_wcwl_on_sale_item_queue', $queue );
		}

		/* === GENERAL METHODS === */

		/**
		 * Checks whether multi-wishlist feature is enabled for current user
		 *
		 * @return bool Whether feature is enabled or not
		 */
		public function is_multi_wishlist_enabled() {
			$multi_wishlist_enabled = 'yes' === get_option( 'yith_wcwl_multi_wishlist_enable', 'no' );

			if ( $multi_wishlist_enabled && ! is_user_logged_in() ) {
				$multi_wishlist_enabled = $multi_wishlist_enabled && ( 'yes' === get_option( 'yith_wcwl_enable_multi_wishlist_for_unauthenticated_users', 'no' ) );
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_is_wishlist_enabled
			 *
			 * Filter whether the multi-wishlist is enabled for current user.
			 *
			 * @param bool $multi_wishlist_enabled Whether multi-wishlist is enabled or not
			 *
			 * @return bool
			 */
			return apply_filters( 'yith_wcwl_is_wishlist_enabled', $multi_wishlist_enabled );
		}

		/**
		 * Get current endpoint, if any
		 *
		 * @return string Current endpoint, empty string if no endpoint is being visited
		 */
		public function get_current_endpoint() {
			$action_params = get_query_var( YITH_WCWL()->wishlist_param, false );

			/**
			 * APPLY_FILTERS: yith_wcwl_current_wishlist_view_params
			 *
			 * Filter the array of parameters to see the current wishlist.
			 *
			 * @param array $params Array of parameters
			 *
			 * @return array
			 */
			$action_params    = explode( '/', apply_filters( 'yith_wcwl_current_wishlist_view_params', $action_params ) );
			$current_endpoint = ( isset( $action_params[0] ) ) ? $action_params[0] : '';

			/**
			 * APPLY_FILTERS: yith_wcwl_current_endpoint
			 *
			 * Filter the current endpoint, if any.
			 *
			 * @param string $current_endpoint Current endpoint
			 * @param array  $action_params Array of parameters
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcwl_current_endpoint', $current_endpoint, $action_params );
		}

		/**
		 * Check if we're on a specific endpoint
		 *
		 * @param string $endpoint Endpoint to test.
		 * @return bool Whether we're on test endpoint or not
		 */
		public function is_endpoint( $endpoint ) {
			$current_endpoint = $this->get_current_endpoint();

			if ( 'view' === $endpoint && '' === $current_endpoint && yith_wcwl_is_wishlist_page() ) {
				$is_endpoint = true;
			} else {
				$is_endpoint = $current_endpoint === $endpoint;
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_is_endpoint
			 *
			 * Filter whether a specific endpoint is visited.
			 *
			 * @param bool   $is_endpoint      Whether a specific endpoint is visited or not
			 * @param string $endpoint         Endpoint
			 * @param string $current_endpoint Current endpoint
			 *
			 * @return bool
			 */
			return apply_filters( 'yith_wcwl_is_endpoint', $is_endpoint, $endpoint, $current_endpoint );
		}

		/* === WISHLIST LICENCE HANDLING === */

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCWL_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_WCWL_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}

			YIT_Plugin_Licence()->register( YITH_WCWL_INIT, YITH_WCWL_SECRET_KEY, YITH_WCWL_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once YITH_WCWL_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}

			YIT_Upgrade()->register( YITH_WCWL_SLUG, YITH_WCWL_INIT );
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Premium class
 *
 * @return \YITH_WCWL_Premium
 * @since 2.0.0
 */
function YITH_WCWL_Premium() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid, Universal.Files.SeparateFunctionsFromOO
	return YITH_WCWL_Premium::get_instance();
}
