<?php
/**
 * YITH WooCommerce Subscriptions Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Subscriptions_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Membership_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Subscriptions_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {

			if ( 1 === version_compare( '2.0', YITH_YWSBS_VERSION ) ) {
				$this->endpoint_key = 'yith-subscription';
				$this->endpoint     = array(
					'slug'    => 'my-subscription',
					'label'   => __( 'My Subscriptions', 'yith-woocommerce-customize-myaccount-page' ),
					'icon'    => 'pencil',
					'content' => '[ywsbs_my_account_subscriptions]',
				);
				$this->register_endpoint();

				add_action( 'template_redirect', array( $this, 'hooks' ), 5 );
			} else { // Otherwise call YITH_Request_Quote_My_Account instance.
				if ( ! class_exists( 'YWSBS_Subscription_My_Account' ) ) {
					require_once YITH_YWSBS_INC . 'class.yith-wc-subscription-my-account.php';
				}
				YWSBS_Subscription_My_Account();

				$this->endpoint_key = 'subscriptions';

				add_filter( 'ywsbs_endpoint', array( $this, 'filter_endpoint_slug' ), 10, 1 );
				add_filter( 'yith_wcmap_get_default_endpoint_options', array( $this, 'filter_default_endpoint_values' ), 10, 2 );
				add_filter( 'yith_wcmap_endpoint_menu_class', array( $this, 'menu_item_active' ), 10, 2 );
			}

			add_filter( 'yith_wcmap_get_before_initialization', array( $this, 'migrate_old_item' ) );
		}

		/**
		 * Migrate old endpoint to new key for YITH Subscription greater then 2.0
		 *
		 * @since 3.1
		 * @param array $items An array of items.
		 * @return array
		 */
		public function migrate_old_item( $items ) {
			$parent = $this->search_old_item( $items );
			if ( 1 !== version_compare( '2.0', YITH_YWSBS_VERSION ) && false !== $parent ) {

				if ( $parent ) { // We are in a group.
					$items[ $parent ]['children'] = $this->replace_old_item( $items[ $parent ]['children'] );
				} else {
					$items = $this->replace_old_item( $items );
				}

				$option = get_option( 'yith_wcmap_endpoint_yith-subscription', array() );
				// Empty content cause the shortcode is deprecated.
				if ( isset( $option['content'] ) ) {
					$option['content'] = '';
				}
				if ( isset( $option['content_position'] ) ) {
					$option['content_position'] = 'before';
				}

				update_option( 'yith_wcmap_endpoint_' . $this->endpoint_key, $option );
				// Remove old option.
				delete_option( 'yith_wcmap_endpoint_yith-subscription' );
				// At the end, update the main option.
				update_option( 'yith_wcmap_endpoint', wp_json_encode( $items ) );
			}

			return $items;
		}

		/**
		 * Replace old item in the main items array
		 *
		 * @since 3.1.0
		 * @param array $items An array of items.
		 * @return array
		 */
		protected function replace_old_item( $items ) {
			$keys  = array_keys( $items );
			$index = array_search( 'yith-subscription', $keys ); // phpcs:ignore
			// Replace with the new key.
			$keys[ $index ] = $this->endpoint_key;

			// Replace with new values.
			return array_combine( $keys, $items );
		}

		/**
		 * Check if old endpoint key is registered
		 *
		 * @since 3.1
		 * @param array  $items An array of items where search.
		 * @param string $parent_key The parent key if any.
		 * @return boolean|string Parent key if found. False otherwise
		 */
		protected function search_old_item( $items, $parent_key = '' ) {
			if ( is_array( $items ) ) {
				foreach ( $items as $key => $item ) {
					$parent_key = isset( $item['children'] ) ? $this->search_old_item( $item['children'], $key ) : false;
					if ( 'yith-subscription' === $key || false !== $parent_key ) {
						return $parent_key;
					}
				}
			}

			return false;
		}

		/**
		 * Compatibility hooks and filters
		 *
		 * @since 3.0.0
		 */
		public function hooks() {
			if ( function_exists( 'YWSBS_Subscription_My_Account' ) ) {
				// Remove content in my account.
				remove_action( 'woocommerce_before_my_account', array( YWSBS_Subscription_My_Account(), 'my_account_subscriptions' ), 10 );
			}

			add_filter( 'yith_wcmap_endpoint_menu_class', array( $this, 'set_active' ), 10, 3 );
		}

		/**
		 * Assign active class to endpoint subscription
		 *
		 * @since  3.0.0
		 * @param array  $classes Current endpoint classes.
		 * @param string $endpoint The current endpoint.
		 * @param array  $options The endpoint options.
		 * @return array
		 */
		public function set_active( $classes, $endpoint, $options ) {

			global $wp;

			if ( 'yith-subscription' === $endpoint && ! in_array( 'active', $classes, true ) && isset( $wp->query_vars['view-subscription'] ) ) {
				$classes[] = 'active';
			}

			return $classes;
		}

		/**
		 * Set main endpoint active when a sub is the current one
		 *
		 * @since 3.0.5
		 * @param array  $classes An array of item classes.
		 * @param string $item The current menu item.
		 * @return mixed
		 */
		public function menu_item_active( $classes, $item ) {
			// Check if endpoint is active.
			$current = yith_wcmap_get_current_endpoint();
			if ( $item === $this->endpoint_key && YITH_WC_Subscription::$view_endpoint === $current ) {
				$classes[] = 'active';
			}

			return $classes;
		}

		/**
		 * Filter quote endpoint slug
		 *
		 * @since 3.0.5
		 * @param string $slug Current endpoint slug.
		 * @return string
		 */
		public function filter_endpoint_slug( $slug ) {
			$option = get_option( 'yith_wcmap_endpoint_' . $this->endpoint_key, array() );
			if ( ! empty( $option['slug'] ) ) {
				return $option['slug'];
			}

			return $slug;
		}

		/**
		 * Filter default endpoint value for quote
		 *
		 * @since 3.0.5
		 * @param array  $data Default endpoint options.
		 * @param string $endpoint The endpoint.
		 * @return array
		 */
		public function filter_default_endpoint_values( $data, $endpoint ) {
			if ( YITH_WC_Subscription::$endpoint === $endpoint ) {
				$data = array_merge(
					$data,
					array(
						'icon'             => 'pencil',
						'content_position' => 'before',
					)
				);
			}

			return $data;
		}
	}
}
