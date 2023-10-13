<?php
/**
 * Class YITH_WCBK_Integration
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Integration
 *
 * @abstract
 * @since   1.0.1
 */
class YITH_WCBK_Integration {

	/**
	 * Plugin data.
	 *
	 * @var array
	 */
	protected $data = array(
		'type'              => 'plugin',
		'key'               => '',
		'name'              => '',
		'title'             => '',
		'landing_uri'       => '',
		'description'       => '',
		'optional'          => false,
		'constant'          => '',
		'installed_version' => '',
		'min_version'       => '',
		'version_compare'   => '>=',
		'new'               => false,
		'visible'           => true,
	);

	/**
	 * Initialized flag.
	 *
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * Initialization
	 */
	public function init_once() {
		if ( ! $this->initialized ) {
			$this->init();
			$this->initialized = true;
		}
	}

	/**
	 * Initialization
	 */
	protected function init() {

	}

	/**
	 * Set the integration data.
	 *
	 * @param array $integration_data The integration data.
	 */
	public function set_data( array $integration_data ) {
		foreach ( $this->data as $key => $value ) {
			if ( isset( $integration_data[ $key ] ) ) {
				$this->data[ $key ] = $integration_data[ $key ];
			}
		}
	}

	/**
	 * Get property
	 *
	 * @param string $prop The property.
	 *
	 * @return mixed|null
	 */
	public function get_prop( $prop ) {
		return array_key_exists( $prop, $this->data ) ? $this->data[ $prop ] : null;
	}

	/**
	 * Get the type.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->get_prop( 'type' );
	}

	/**
	 * Get the constant.
	 *
	 * @return string
	 */
	public function get_constant() {
		return $this->get_prop( 'constant' );
	}

	/**
	 * Get the key.
	 *
	 * @return string
	 */
	public function get_key() {
		return $this->get_prop( 'key' );
	}

	/**
	 * Get the installed_version.
	 *
	 * @return string
	 */
	public function get_installed_version() {
		return $this->get_prop( 'installed_version' );
	}

	/**
	 * Get the min_version.
	 *
	 * @return string
	 */
	public function get_min_version() {
		return $this->get_prop( 'min_version' );
	}

	/**
	 * Get the name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->get_prop( 'name' );
	}

	/**
	 * Get the title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->get_prop( 'title' );
	}

	/**
	 * Get the icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return YITH_WCBK_ASSETS_URL . '/images/plugins/' . $this->get_key() . '.svg';
	}

	/**
	 * Get the landing_uri.
	 *
	 * @return string
	 */
	public function get_landing_uri() {
		$url            = $this->get_prop( 'landing_uri' );
		$plugin_version = defined( 'YITH_WCBK_PREMIUM' ) ? 'premium' : 'extended';

		return yith_plugin_fw_add_utm_data( $url, YITH_WCBK_SLUG, 'integration-options', $plugin_version );
	}

	/**
	 * Get the landing_uri.
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->get_prop( 'description' );
	}

	/**
	 * Get the version_compare.
	 *
	 * @return string
	 */
	public function get_version_compare() {
		return $this->get_prop( 'version_compare' );
	}

	/**
	 * Get the activation URL.
	 *
	 * @return string
	 * @deprecated 5.0.0
	 */
	public function get_activation_url() {
		yith_wcbk_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '5.0.0' );

		return '#';
	}

	/**
	 * Get the deactivation URL.
	 *
	 * @return string
	 * @deprecated 5.0.0
	 */
	public function get_deactivation_url() {
		yith_wcbk_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '5.0.0' );

		return '#';
	}

	/**
	 * Is this optional?
	 *
	 * @return bool
	 * @deprecated 5.0.0
	 */
	public function is_optional(): bool {
		yith_wcbk_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '5.0.0' );

		return false;
	}

	/**
	 * Is this visible?
	 *
	 * @return bool
	 * @deprecated 5.0.0
	 */
	public function is_visible(): bool {
		yith_wcbk_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '5.0.0' );

		return false;
	}

	/**
	 * Is this new?
	 *
	 * @return bool
	 * @deprecated 5.0.0
	 */
	public function is_new(): bool {
		yith_wcbk_deprecated_function( __CLASS__ . '::' . __FUNCTION__, '5.0.0' );

		return false;
	}

	/**
	 * Is the component(plugin or theme) active?
	 *
	 * @return bool
	 */
	public function is_component_active() {
		if ( 'theme' === $this->get_type() ) {
			return YITH_WCBK()->theme->is_active( $this->get_key() );
		} else {
			$constant = $this->get_constant();
			if ( $constant && defined( $constant ) && constant( $constant ) ) {
				$installed_version = $this->get_installed_version();
				$min_version       = $this->get_min_version();

				if ( ! $installed_version || ! $min_version ) {
					return true;
				}

				if (
					defined( $installed_version ) && constant( $installed_version )
					&&
					version_compare( constant( $installed_version ), $min_version, $this->get_version_compare() )
				) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Is the integration enabled?
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return $this->is_component_active();
	}
}
