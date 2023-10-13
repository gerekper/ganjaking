<?php
/**
 * Class YITH_WCBK_Integrations
 * handle plugin integrations
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Integrations
 *
 * @since   1.0.1
 */
class YITH_WCBK_Integrations {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * Integrations list.
	 *
	 * @var array
	 */
	protected $integrations_list;

	/**
	 * Integrations object list.
	 *
	 * @var YITH_WCBK_Integration[]
	 */
	protected $integrations = array();

	/**
	 * YITH_WCBK_Integrations constructor.
	 */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_integrations' ), 15 );
	}

	/**
	 * Magic getter.
	 * To handle backward-compatibility (to get the integrations as properties of the class).
	 *
	 * @param string $key The key.
	 *
	 * @return YITH_WCBK_Integration|null
	 */
	public function __get( string $key ) {
		if ( isset( $this->integrations[ $key ] ) ) {
			yith_wcbk_doing_it_wrong( __CLASS__ . '::' . $key, 'This property of should not be accessed directly. To retrieve an integration use YITH_WCBK_Integrations::get_integration.', '5.5.0' );

			return $this->integrations[ $key ];
		}

		return null;
	}

	/**
	 * Get the integrations list.
	 *
	 * @return array|mixed
	 */
	public function get_integrations_list() {
		if ( is_null( $this->integrations_list ) ) {
			$this->integrations_list = require_once __DIR__ . '/integrations-list.php';
		}

		return $this->integrations_list;
	}

	/**
	 * Load plugins
	 */
	public function load_integrations() {
		$this->load();
	}

	/**
	 * Load Integration classes
	 */
	private function load() {
		require_once YITH_WCBK_INCLUDES_PATH . '/integrations/class-yith-wcbk-integration.php';

		foreach ( $this->get_integrations_list() as $key => $integration_data ) {
			$type      = $integration_data['type'] ?? 'plugin';
			$folder    = 'theme' === $type ? 'themes' : 'plugins';
			$path      = YITH_WCBK_INCLUDES_PATH . '/integrations/' . $folder . '/';
			$filename  = $path . 'class-yith-wcbk-' . $key . '-integration.php';
			$classname = $this->get_class_name_from_key( $key );
			$var       = str_replace( '-', '_', $key );

			if ( file_exists( $filename ) && ! class_exists( $classname ) ) {
				require_once $filename;
			}

			$integration_data['key'] = $key;

			if ( class_exists( $classname ) && method_exists( $classname, 'get_instance' ) ) {
				/**
				 * The integration.
				 *
				 * @var YITH_WCBK_Integration $integration
				 */
				$integration = $classname::get_instance();

			} else {
				$integration = new YITH_WCBK_Integration();
			}

			$integration->set_data( $integration_data );
			$integration->init_once();

			$this->integrations[ $key ] = $integration;
		}
	}

	/**
	 * Get the class name from key.
	 *
	 * @param string $key The integration key.
	 *
	 * @return string
	 */
	public function get_class_name_from_key( $key ) {
		$class_key = str_replace( '-', ' ', $key );
		$class_key = ucwords( $class_key );
		$class_key = str_replace( ' ', '_', $class_key );

		return 'YITH_WCBK_' . $class_key . '_Integration';
	}

	/**
	 * Check if user has the component (plugin/theme).
	 *
	 * @param string $key The integration key.
	 *
	 * @return bool
	 */
	public function has_component( string $key ): bool {
		$integration = $this->get_integration( $key );

		return ! ! $integration && $integration->is_component_active();
	}

	/**
	 * Retrieve a specific integration instance.
	 *
	 * @param string $key The integration key.
	 *
	 * @return YITH_WCBK_Integration|bool
	 */
	public function get_integration( string $key ) {
		if ( ! empty( $this->integrations[ $key ] ) ) {
			return $this->integrations[ $key ];
		}

		return false;
	}
}
