<?php
/**
 * WordPress Admin
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WordPress\Plugin_Admin\REST_API\Controllers;

use SkyVerge\WordPress\Plugin_Admin\Package;

defined( 'ABSPATH' ) or exit;

/**
 * The Shop controller class.
 *
 * @since 1.0.0
 */
class Shop {


	/** @var string route namespace */
	protected $namespace = 'skyverge/v1';

	/** @var string route */
	protected $rest_route = 'shop';


	/**
	 * Register’s the controller’s routes.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace, "/{$this->rest_route}", [
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_item' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);

		register_rest_route(
			$this->namespace, "/{$this->rest_route}/plugins", [
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_plugins' ],
					'permission_callback' => [ $this, 'get_items_permissions_check' ],
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);
	}


	/**
	 * Gets the item schema.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_item_schema() {

		return [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'shop',
			'type'       => 'object',
			'properties' => [
				'woocommerceStatus' => [
					'description' => __( 'Whether or not the shop is connected to WooCommerce.com', 'sv-wordpress-plugin-admin' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'enum'        => [ 'connected', 'disconnected' ],
					'readonly'    => true,
				],
				'supportBotStatus'  => [
					'description' => __( 'Whether or not the support bot is connected', 'sv-wordpress-plugin-admin' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'enum'        => [ 'connected', 'disconnected' ],
					'readonly'    => true,
				],
				'siteURL'  => [
					'description' => __( "The shop's site URL", 'sv-wordpress-plugin-admin' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'shopEmail'        => [
					'description' => __( "The shop's e-mail address.", 'sv-wordpress-plugin-admin' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'adminEmail'        => [
					'description' => __( "The current admin's e-mail address.", 'sv-wordpress-plugin-admin' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'supportUser'       => [
					'description' => __( "The shop's support user.", 'sv-wordpress-plugin-admin' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'createdAt'         => [
					'description' => __( "The Shop page's creation date.", 'sv-wordpress-plugin-admin' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'pluginAdminVersion' => [
					'description' => __( 'The version of the currently loaded SkyVerge WordPress Plugin Admin package.', 'sv-wordpress-plugin-admin' ),
					'type'        => 'string',
					'context'     => [ 'view', 'edit' ],
					'readonly'    => true,
				],
				'location'          => [
					'type'       => 'object',
					'properties' => [
						'address1' => [
							'description' => __( 'Address line 1', 'sv-wordpress-plugin-admin' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'address2' => [
							'description' => __( 'Address line 2', 'sv-wordpress-plugin-admin' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'city'     => [
							'description' => __( 'City', 'sv-wordpress-plugin-admin' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'country'  => [
							'description' => __( 'Country', 'sv-wordpress-plugin-admin' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'state'    => [
							'description' => __( 'State', 'sv-wordpress-plugin-admin' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'postalCode'    => [
							'description' => __( 'Postal code', 'sv-wordpress-plugin-admin' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
					],
				],
				'plugins'           => [
					'type'       => 'object',
					'properties' => [
						'slug'              => [
							'description' => __( "The plugin's slug.", 'sv-wordpress-plugin-admin' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'name'              => [
							'description' => __( "The plugin's name.", 'sv-wordpress-plugin-admin' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'status'           => [
							'description' => __( "The plugin's WordPress status.", 'sv-wordpress-plugin-admin' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'enum'        => [ 'active', 'inactive' ],
							'readonly'    => true,
						],
						'license'           => [
							'description' => __( "The plugin's WooCommerce.com subscription status.", 'sv-wordpress-plugin-admin' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'enum'        => [ 'active', 'expired', 'inactive', 'none' ],
							'readonly'    => true,
						],
						'woocommerceStatus' => [
							'description' => __( 'Whether or not the plugin is connected to WooCommerce.com', 'sv-wordpress-plugin-admin' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'enum'        => [ 'connected', 'disconnected' ],
							'readonly'    => true,
						],
						'connectionUrl'     => [
							'description' => __( 'The URL to activate the plugin', 'sv-wordpress-plugin-admin' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
						'documentationUrl'  => [
							'description' => __( 'The plugin URL', 'sv-wordpress-plugin-admin' ),
							'type'        => 'string',
							'context'     => [ 'view', 'edit' ],
							'readonly'    => true,
						],
					],
				],
			],
		];
	}


	/**
	 * Checks if the user has permission to get items.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @return bool|\WP_Error
	 */
	public function get_items_permissions_check() {

		return current_user_can( 'manage_woocommerce' );
	}


	/**
	 * Gets the formatted data for the shop.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_item() {

		$support_user = $this->get_support_user();
		$admin_user   = wp_get_current_user();

		$shop_page_id = wc_get_page_id( 'shop' );
		$shop_page    = get_post( $shop_page_id );

		$item = [
			'shop' => [
				'woocommerceStatus'  => $this->is_woocommerce_connected() ? 'connected' : 'disconnected',
				'supportBotStatus'   => $this->is_support_bot_connected() ? 'connected' : 'disconnected',
				'siteURL'            => site_url(),
				'shopEmail'          => get_option( 'admin_email' ),
				'adminEmail'         => $admin_user instanceof \WP_User ? $admin_user->get( 'user_email' ) : '',
				'supportUser'        => $support_user instanceof \WP_User ? $support_user->get( 'user_email' ) : '',
				'createdAt'          => ! empty( $shop_page ) ? $shop_page->post_date : '',
				'pluginAdminVersion' => Package::VERSION,
				'location'           => [
					'address1'   => WC()->countries->get_base_address(),
					'address2'   => WC()->countries->get_base_address_2(),
					'city'       => WC()->countries->get_base_city(),
					'country'    => WC()->countries->get_base_country(),
					'state'      => WC()->countries->get_base_state(),
					'postalCode' => WC()->countries->get_base_postcode(),
				],
			],
		];

		return rest_ensure_response( $item );
	}


	/**
	 * Gets the formatted plugin data for all installed WC plugins.
	 *
	 * Can be filtered by author and status (active or inactive).
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_plugins() {

		$plugins = \WC_Helper::get_local_woo_plugins();

		// filter by author
		if ( ! empty( $_REQUEST['author'] ) ) {

			$author = sanitize_text_field( $_REQUEST['author'] );

			$plugins = array_filter( $plugins, function ( $plugin_data, $key ) use ( $author ) {

				return ( ! empty( $plugin_data['Author'] ) && $author === $plugin_data['Author'] );
			}, ARRAY_FILTER_USE_BOTH );
		}

		// filter by status
		if ( ! empty( $_REQUEST['status'] ) ) {

			$status = sanitize_text_field( $_REQUEST['status'] );

			$plugins = array_filter( $plugins, function ( $key ) use ( $status ) {

				if ( 'active' === $status ) {
					return ( ! empty( is_plugin_active( $key ) ) );
				} elseif ( 'inactive' === $status ) {
					return ( empty( is_plugin_active( $key ) ) );
				} else {
					// invalid value
					return true;
				}
			}, ARRAY_FILTER_USE_KEY );
		}

		$response_data = $this->get_formatted_plugins_data( $plugins );

		return rest_ensure_response( $response_data );
	}


	/**
	 * Gets the formatted plugin data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $plugins
	 * @return array
	 */
	private function get_formatted_plugins_data( $plugins ) {

		$plugins_data  = [];
		$subscriptions = \WC_Helper::get_subscriptions();

		foreach ( $plugins as $plugin_file => $plugin_data ) {

			$subscription_key = false;

			if ( ! empty( $product_id = $plugin_data['_product_id'] ) ) {

				$subscription_key = array_search( $product_id, array_map( static function ( $value ) {

					return ( ! empty( $value['product_id'] ) ? $value['product_id'] : null );
				}, $subscriptions ), false );
			}

			$connected = false;
			$license   = 'none';

			if ( false !== $subscription_key ) {

				$subscription = $subscriptions[ $subscription_key ];
				$auth         = \WC_Helper_Options::get( 'auth' );

				if ( ! empty( $auth ) && ! empty( $auth['site_id'] ) ) {

					$site_id   = absint( $auth['site_id'] );
					$connected = in_array( $site_id, $subscription['connections'], true );
				}

				if ( $connected ) {
					$license = 'active';
				} elseif ( ! empty( $subscription['expired'] ) ) {
					$license = 'expired';
				} else {
					$license = 'inactive';
				}

				$activate_url = add_query_arg(
					[
						'page'                  => 'wc-addons',
						'section'               => 'helper',
						'wc-helper-activate'    => 1,
						'wc-helper-product-key' => $subscription['product_key'],
						'wc-helper-product-id'  => $subscription['product_id'],
						'wc-helper-nonce'       => wp_create_nonce( 'activate:' . $subscription['product_key'] ),
					],
					admin_url( 'admin.php' )
				);
			}

			$plugins_data[] = [

				'slug'              => $plugin_data['slug'],
				'name'              => $plugin_data['Name'],
				'status'            => is_plugin_active( $plugin_file ) ? 'active' : 'inactive',
				'license'           => $license,
				'wooCommerceStatus' => $connected ? 'connected' : 'disconnected',
				'connectionUrl'     => ! empty( $activate_url ) ? $activate_url : '',
				'documentationUrl'  => $this->get_documentation_url( $plugin_data ),
			];
		}

		return [
			'plugins' => $plugins_data,
		];
	}


	/**
	 * Determines if WooCommerce.com is connected.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_woocommerce_connected() {

		$auth = \WC_Helper_Options::get( 'auth' );

		return ! empty( $auth['access_token'] );
	}


	/**
	 * Determines if the support bot is connected.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_support_bot_connected() {
		global $wpdb;

		// look for WooCommerce API keys with "SkyVerge Support" in the description
		$keys = $wpdb->get_var(
			"SELECT COUNT(key_id) FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE '%SkyVerge Support%'"
		); // WPCS: unprepared SQL ok

		return ! empty( $keys );
	}


	/**
	 * Gets the admin support user, usually created by the support bot.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_User|false
	 */
	private function get_support_user() {

		$support_user = get_user_by( 'login', 'skyverge' );

		if ( false === $support_user ) {

			// look for the support user by email
			$support_user = get_user_by( 'email', 'support@skyverge.com' );
		}

		return $support_user;
	}


	/**
	 * Gets the documentation URL from the plugin data.
	 *
	 * If not available, falls back to the plugin instance's get_documentation_url() method.
	 * If that is also not avaialble, falls back to the WooCommerce plugin URL.
	 *
	 * @param array $plugin_data plugin data
	 * @return string
	 *
	 * @since 1.0.0
	 */
	private function get_documentation_url( array $plugin_data ) {

		$documentation_url = '';

		// try to get the documentation URL from the plugin header
		if ( ! empty( $plugin_data['Documentation URI'] ) ) {

			$documentation_url = $plugin_data['Documentation URI'];

		} else {

			// try to get the documentation URL from the plugin instance
			try {

				$slug = $plugin_data['slug'];

				// if the plugin is not active, we cannot call the plugin's get_documentation_url() method
				if ( is_plugin_active( $plugin_data['_filename'] ) && 'SkyVerge' === $plugin_data['Author'] ) {

					$instance_function = str_replace( 'woocommerce', 'wc', str_replace( '-', '_', $slug ) );

					if ( is_callable( $instance_function ) ) {

						$plugin_instance = $instance_function();

						if ( method_exists( $plugin_instance, 'get_documentation_url' ) ) {
							$documentation_url = $plugin_instance->get_documentation_url();
						}
					}
				}
			} catch ( \Exception $e ) {
			}
		}

		// fallback to the plugin URL
		if ( empty( $documentation_url ) ) {
			$documentation_url = $plugin_data['PluginURI'];
		}

		return $documentation_url;
	}


}
