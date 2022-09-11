<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Server class
 *
 * @class   YITH\Subscription\RestApi\Server
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */

namespace YITH\Subscription\RestApi;

use YITH\Subscription\RestApi\Schedulers\Scheduler;

defined( 'ABSPATH' ) || exit;

/**
 * Class responsible for loading the REST API and all REST API namespaces.
 */
class Server {
	/**
	 * Instance
	 *
	 * @var /Server
	 */
	private static $instance;

	/**
	 * REST API namespaces and endpoints.
	 *
	 * @var array
	 */
	protected $controllers = array();

	/**
	 * Singleton implementation
	 *
	 * @return Server
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Hook into WordPress ready to init the REST API as needed.
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
		add_filter( 'woocommerce_admin_rest_controllers', array( $this, 'wc_admin_rest_controllers' ), 10, 1 );
		add_filter( 'woocommerce_data_stores', array( __CLASS__, 'add_data_stores' ) );
	}

	/**
	 * Add data stores
	 *
	 * @param array $data_stores Data Store.
	 * @return array
	 */
	public static function add_data_stores( $data_stores ) {
		return array_merge(
			$data_stores,
			array(
				'yith-ywsbs-report-subscriptions-stats' => 'YITH\Subscription\RestApi\Reports\Subscriptions\Stats\DataStore',
				'yith-ywsbs-report-subscriptions'       => 'YITH\Subscription\RestApi\Reports\Subscriptions\DataStore',
				'yith-ywsbs-report-products'            => 'YITH\Subscription\RestApi\Reports\Products\DataStore',
				'yith-ywsbs-report-lost-subscribers'    => 'YITH\Subscription\RestApi\Reports\LostSubscribers\DataStore',
				'yith-ywsbs-report-customers'           => 'YITH\Subscription\RestApi\Reports\Customers\DataStore',
				'yith-ywsbs-report-products-stats'      => 'YITH\Subscription\RestApi\Reports\Products\Stats\DataStore',
			)
		);
	}

	/**
	 * Add wc admin Rest controllers
	 *
	 * @param array $controllers Controllers.
	 * @return mixed
	 */
	public function wc_admin_rest_controllers( $controllers ) {
		$controllers[] = 'YITH\Subscription\RestApi\Reports\Subscriptions\Stats\Controller';
		$controllers[] = 'YITH\Subscription\RestApi\Reports\Subscriptions\Controller';
		$controllers[] = 'YITH\Subscription\RestApi\Reports\Products\Controller';
		$controllers[] = 'YITH\Subscription\RestApi\Reports\Products\Stats\Controller';
		$controllers[] = 'YITH\Subscription\RestApi\Reports\LostSubscribers\Controller';
		$controllers[] = 'YITH\Subscription\RestApi\Reports\Customers\Controller';
		return $controllers;
	}

	/**
	 * Register REST API routes.
	 */
	public function register_rest_routes() {
		foreach ( $this->get_rest_namespaces() as $namespace => $controllers ) {
			foreach ( $controllers as $controller_name => $controller_class ) {
				$this->controllers[ $namespace ][ $controller_name ] = new $controller_class();
				$this->controllers[ $namespace ][ $controller_name ]->register_routes();
			}
		}

	}

	/**
	 * Get API namespaces - new namespaces should be registered here.
	 *
	 * @return array List of Namespaces and Main controller classes.
	 */
	protected function get_rest_namespaces() {
		return apply_filters( 'yith_ywsbs_rest_api_get_rest_namespaces', array( 'yith-ywsbs/v1' => $this->get_v1_controllers() ) );
	}

	/**
	 * List of controllers in the wc/v1 namespace.
	 *
	 * @return array
	 */
	public function get_v1_controllers() {
		return array(
			'subscriptions' => 'YITH_YWSBS_REST_Subscriptions_Controller',
		);
	}
}
