<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships;

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Memberships main REST API handler.
 *
 * @since 1.11.0
 *
 * @method \WC_Memberships get_plugin()
 */
class REST_API extends Framework\REST_API {


	/** @var string[] supported API versions (matches namespaces) */
	private $supports;

	/** @var \SkyVerge\WooCommerce\Memberships\API\Controller\Membership_Plans[] instances */
	private $membership_plans = [];

	/** @var \SkyVerge\WooCommerce\Memberships\API\Controller\User_Memberships[] instances */
	private $user_memberships = [];


	/**
	 * Extends the WP REST API.
	 *
	 * @since 1.11.0
	 *
	 * @param \WC_Memberships $plugin main plugin class instance
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->supports = [
			'v2',
			'v3',
			'v4',
		];

		if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.6.0' ) ) {
			$this->includes();
		} else {
			add_action( 'rest_api_init', [ $this, 'includes' ], 5 );
		}
	}


	/**
	 * Loads handlers.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 */
	public function includes() {

		// ensure that WC REST API base abstracts extended by Memberships are available also while processing webhooks
		if (
			     Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.6.0' )
			&&   Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.7.0' )
			&& ! did_action( 'rest_api_init' )
			&& ! class_exists( 'WC_REST_Posts_Controller', false )
		) {
			wc()->api->rest_api_includes();
		}

		require_once( $this->get_plugin()->get_plugin_path() . '/src/api/abstract-wc-memberships-rest-api-controller.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/src/api/abstract-wc-memberships-rest-api-membership-plans.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/src/api/abstract-wc-memberships-rest-api-user-memberships.php' );
	}


	/**
	 * Adds new routes to the WP REST API.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 */
	public function register_routes() {

		foreach ( $this->supports as $api_version ) {

			if ( $membership_plans_api = $this->get_membership_plans( $api_version ) ) {

				$membership_plans_api->register_routes();
			}

			if ( $user_memberships_api = $this->get_user_memberships( $api_version ) ) {

				$user_memberships_api->register_routes();
			}
		}
	}


	/**
	 * Determines if the current request is for a WC REST API endpoint.
	 *
	 * @since 1.19.0-dev.1
	 *
	 * @return bool
	 */
	public function is_rest_api_request() {

		if ( function_exists( 'WC' ) && is_callable( [ WC(), 'is_rest_api_request' ] ) ) {
			return (bool) WC()->is_rest_api_request();
		}

		if ( empty( $_SERVER['REQUEST_URI'] ) || ! function_exists( 'rest_get_url_prefix' ) ) {
			return false;
		}

		$rest_prefix         = trailingslashit( rest_get_url_prefix() );
		$is_rest_api_request = false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix );

		/* applies WooCommerce core filter */
		return (bool) apply_filters( 'woocommerce_is_rest_api_request', $is_rest_api_request );
	}


	/**
	 * Gets the supported REST API namespace versions.
	 *
	 * @since 1.12.0
	 *
	 * @return string[]
	 */
	public function get_supported_versions() {

		return $this->supports;
	}


	/**
	 * Checks if a version of the API is supported by Memberships handlers.
	 *
	 * @since 1.12.0
	 *
	 * @param string $version API version
	 * @return bool
	 */
	public function is_supported_version( $version ) {

		return in_array( strtolower( $version ), $this->supports, true );
	}


	/**
	 * Gets a controller instance.
	 *
	 * @since 1.12.0
	 *
	 * @param string $which instance name: either 'user_memberships' or 'membership_plans'
	 * @param string $version version of the API (namespace), lowercase
	 * @return null|\SkyVerge\WooCommerce\Memberships\API\Controller\User_Memberships|\SkyVerge\WooCommerce\Memberships\API\Controller\Membership_Plans controller instance
	 */
	private function get_instance( $which, $version ) {

		$instance  = null;
		$namespace = null === $version ? end( $this->supports ) : strtolower( $version );

		if ( in_array( $namespace, $this->supports, true ) && class_exists( 'WC_REST_Posts_Controller' ) ) {

			// make sure abstract handlers are always available when needed
			if ( ! class_exists( '\\SkyVerge\\WooCommerce\\Memberships\\API\\Controller' ) ) {
				$this->includes();
			}

			if ( 'user_memberships' === $which ) {

				if ( ! array_key_exists( $namespace, $this->user_memberships ) || ! $this->user_memberships[ $namespace ] instanceof User_Memberships ) {

					$this->user_memberships[ $namespace ] = $this->get_plugin()->load_class( '/src/api/' . $namespace . '/class-wc-memberships-rest-api-' . $namespace . '-user-memberships.php', '\\SkyVerge\\WooCommerce\\Memberships\\API\\' . $namespace . '\\User_Memberships' );
				}

				$instance = $this->user_memberships[ $namespace ];

			} elseif ( 'membership_plans' === $which ) {

				if ( ! array_key_exists( $namespace, $this->membership_plans ) || ! $this->membership_plans[ $namespace ] instanceof Membership_PLans ) {

					$this->membership_plans[ $namespace ] = $this->get_plugin()->load_class( '/src/api/' . $namespace . '/class-wc-memberships-rest-api-' . $namespace . '-membership-plans.php', '\\SkyVerge\\WooCommerce\\Memberships\\API\\' . $namespace . '\\Membership_Plans' );
				}

				$instance = $this->membership_plans[ $namespace ];
			}
		}

		return $instance;
	}


	/**
	 * Returns the User Memberships REST API handler instance.
	 *
	 * @since 1.11.0
	 *
	 * @param null|string $version optional API version handler to get (gets latest)
	 * @return \SkyVerge\WooCommerce\Memberships\API\Controller\User_Memberships
	 */
	public function get_user_memberships( $version = null ) {

		return $this->get_instance( 'user_memberships', $version );
	}


	/**
	 * Returns the Membership Plans REST API handler instance.
	 *
	 * @since 1.11.0
	 *
	 * @param null|string $version optional API version handler to get (gets latest by default)
	 * @return null|\SkyVerge\WooCommerce\Memberships\API\Controller\Membership_Plans
	 */
	public function get_membership_plans( $version = null ) {

		return $this->get_instance( 'membership_plans', $version );
	}


	/**
	 * Returns the data to add to the WooCommerce REST API System Status response.
	 *
	 * @see \SkyVerge\WooCommerce\Memberships\System_Status_Report::get_system_status_report_data() for extending the response data
	 *
	 * @since 1.12.0
	 *
	 * @return array associative array of response data
	 */
	protected function get_system_status_data() {

		$status_data = [];

		foreach ( System_Status_Report::get_system_status_report_data() as $export_key => $data ) {

			if ( '' !== $export_key  && isset( $data['value'] ) ) {

				$key   = preg_replace( '/[^a-z]+/i', '_', strtolower( $export_key ) );
				$value = $data['value'];

				if ( is_string( $value ) || is_numeric( $value ) ) {

					$status_data[ $key ] = $value;

				} elseif ( is_array( $value ) ) {

					// sanity check: exclude individual plans from API response
					if ( false === strpos( $key, 'membership_plan_' ) ) {

						$status_data[ $key ] = $value;
					}
				}
			}
		}

		return $status_data;
	}


}
