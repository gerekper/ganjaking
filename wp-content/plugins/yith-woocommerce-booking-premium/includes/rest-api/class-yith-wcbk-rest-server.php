<?php
/**
 * Initialize this version of the REST API.
 *
 * @package YITH\Booking\RestApi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class responsible for loading the REST API and all REST API namespaces.
 */
class YITH_WCBK_REST_Server {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * REST API namespaces and endpoints.
	 *
	 * @var array
	 */
	protected $controllers = array();

	/**
	 * Construct
	 */
	protected function __construct() {
		$this->init();
	}

	/**
	 * Hook into WordPress ready to init the REST API as needed.
	 */
	protected function init() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
	}

	/**
	 * Register REST API routes.
	 */
	public function register_rest_routes() {
		foreach ( $this->get_rest_namespaces() as $namespace => $controllers ) {
			$version_folder = str_replace( 'yith-booking/', '', $namespace );

			foreach ( $controllers as $controller_name => $controller_class ) {
				$versioned_controller_name = 'v1' !== $version_folder ? ( $controller_name . '-' . $version_folder ) : $controller_name;
				$filename                  = 'class-yith-wcbk-rest-' . $controller_name . '-controller.php';
				$filepath                  = trailingslashit( YITH_WCBK_REST_API_PATH ) . '/controllers/' . $version_folder . '/' . $filename;

				if ( file_exists( $filepath ) ) {
					require_once $filepath;

					$this->controllers[ $namespace ][ $controller_name ] = new $controller_class();
					$this->controllers[ $namespace ][ $controller_name ]->register_routes();
				}
			}
		}
	}

	/**
	 * Get API namespaces - new namespaces should be registered here.
	 *
	 * @return array List of Namespaces and Main controller classes.
	 */
	protected function get_rest_namespaces() {
		return array(
			'yith-booking/v1' => $this->get_v1_controllers(),
		);
	}

	/**
	 * List of controllers in the v1 namespace.
	 *
	 * @return array
	 */
	protected function get_v1_controllers() {
		return array(
			'global-availability-rules' => 'YITH_WCBK_REST_Global_Availability_Rules_Controller',
			'global-price-rules'        => 'YITH_WCBK_REST_Global_Price_Rules_Controller',
		);
	}
}
