<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Loader class
 *
 * @class   YITH\Subscription\RestApi\Loader
 * @package YITH WooCommerce Subscription
 * @since   2.3.0
 * @author  YITH
 */

namespace YITH\Subscription\RestApi;

defined( 'ABSPATH' ) || exit;

/**
 * Loader
 */
class Loader {
	/**
	 * Instance
	 *
	 * @var /Loader
	 */
	private static $instance;

	/**
	 * Server
	 *
	 * @var Server
	 */
	private $server;

	/**
	 * Singleton implementation
	 *
	 * @return Loader
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Loader constructor.
	 */
	protected function __construct() {
		if ( yith_ywsbs_is_wc_admin_enabled() ) {
			$this->load();
			$this->include_files();
			$this->init();

		}
	}

	/**
	 * Load function
	 */
	protected function load() {
		require_once 'Server.php';
		$this->server = \YITH\Subscription\RestApi\Server::get_instance();
	}

	/**
	 * Include files
	 */
	protected function include_files() {

		// Controllers.
		$controller_files = array(
			'v1' => array_keys( $this->server->get_v1_controllers() ),
		);

		foreach ( $controller_files as $version => $controllers ) {
			foreach ( $controllers as $controller ) {
				$filename = "class.yith-ywsbs-wc-rest-{$controller}-controller.php";
				$path     = "Controllers/{$version}/$filename";
				require_once $path;
			}
		}

		// Scheduler.
		require_once 'Schedulers/Scheduler.php';

		// Reports Subscription.
		require_once 'Reports/Subscriptions/Controller.php';
		require_once 'Reports/Subscriptions/DataStore.php';
		require_once 'Reports/Subscriptions/Query.php';

		// Reports Subscription.
		require_once 'Reports/Products/Controller.php';
		require_once 'Reports/Products/DataStore.php';
		require_once 'Reports/Products/Query.php';

		// Reports Subscription.
		require_once 'Reports/Customers/Controller.php';
		require_once 'Reports/Customers/DataStore.php';
		require_once 'Reports/Customers/Query.php';

		// Reports Subscription.
		require_once 'Reports/LostSubscribers/Controller.php';
		require_once 'Reports/LostSubscribers/DataStore.php';
		require_once 'Reports/LostSubscribers/Query.php';

		// Reports Stats - Subscriptions.
		require_once 'Reports/Subscriptions/Stats/Controller.php';
		require_once 'Reports/Subscriptions/Stats/DataStore.php';
		require_once 'Reports/Subscriptions/Stats/Query.php';

		// Reports Stats - Products.
		require_once 'Reports/Products/Stats/Controller.php';
		require_once 'Reports/Products/Stats/DataStore.php';
		require_once 'Reports/Products/Stats/Query.php';

	}

	/**
	 * Init function.
	 */
	protected function init() {
		\YITH\Subscription\RestApi\Schedulers\Scheduler::get_instance();
		$this->server->init();
	}

}
