<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Settings' ) ) {

	/**
	 * Class YITH_WCMBS_Settings
	 *
	 * @since 1.4.0
	 */
	class YITH_WCMBS_Settings {
		/**
		 * @var YITH_WCMBS_Settings
		 */
		private static $instance;

		/**
		 * Array containing the raw values of options
		 *
		 * @var array
		 */
		private $options = array();

		/**
		 * Singleton implementation.
		 *
		 * @return YITH_WCMBS_Settings
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCMBS_Settings constructor.
		 */
		private function __construct() {
		}

		/**
		 * Retrieve the default values
		 *
		 * @return array
		 */
		public function get_defaults() {
			// Important: if you use $this->get_option here, you MUST specify the default value!!!
			return array(
				'yith-wcmbs-memberships-on-user-register'                 => array(),
				'yith-wcmbs-memberships-on-user-register-enabled'         => ! ! $this->get_option( 'yith-wcmbs-memberships-on-user-register', array() ) ? 'yes' : 'no', // Backward compatibility < 1.4.0
				'yith-wcmbs-hide-contents'                                => 'all',
				'yith-wcmbs-default-alternative-content'                  => '',
				'yith-wcmbs-default-alternative-content-mode'             => 'set',
				'yith-wcmbs-default-alternative-content-id'               => '',
				'yith-wcmbs-redirect-link'                                => '',
				'yith-wcmbs-show-memberships-menu-in-my-account'          => 'yes',
				'yith-wcmbs-enable-guest-checkout'                        => 'no',
				'yith-wcmbs-products-in-membership-management'            => 'hide_products',
				'yith-wcmbs-download-link-position'                       => 'tab',
				'yith-wcmbs-hide-price-and-add-to-cart'                   => 'no',
				'yith-wcmbs-show-membership-info-in-reports'              => 'yes',
				'yith-wcmbs-use-external-services-to-get-user-ip-address' => 'yes',
				'yith-wcmbs-retrieve-membership-discount-settings'        => 'membership',
			);
		}

		/**
		 * Retrieve an option
		 *
		 * @param string                 $option  The option name.
		 * @param string|array|bool|null $default The default value.
		 *
		 * @return mixed
		 */
		public function get_option( $option, $default = null ) {
			$default = is_null( $default ) ? $this->get_default( $option ) : $default;

			if ( ! array_key_exists( $option, $this->options ) ) {
				$this->options[ $option ] = get_option( $option, null );
			}

			$value = $this->options[ $option ];

			if ( is_null( $value ) ) {
				$value = $default;
			}

			return $value;
		}

		/**
		 * Retrieve the default value for an option
		 *
		 * @param string $option
		 *
		 * @return mixed
		 */
		public function get_default( $option ) {
			$defaults = $this->get_defaults();

			return isset( $defaults[ $option ] ) ? $defaults[ $option ] : '';
		}

		/*
        |--------------------------------------------------------------------------
        | Specific getters
        |--------------------------------------------------------------------------
        |
        */

		/**
		 * Get the "hide contents" option
		 *
		 * @return string
		 */
		public function get_hide_contents() {
			return $this->get_option( 'yith-wcmbs-hide-contents' );
		}

		/*
        |--------------------------------------------------------------------------
        | Conditionals
        |--------------------------------------------------------------------------
        |
        */

		/**
		 * Is the alternative content enabled?
		 *
		 * @return bool
		 */
		public function is_alternative_content_enabled() {
			return 'alternative_content' === $this->get_hide_contents();
		}

	}
}

if ( ! function_exists( 'yith_wcmbs_settings' ) ) {
	/**
	 * Access to the YITH_WCMBS_Settings instance
	 *
	 * @return YITH_WCMBS_Settings
	 */
	function yith_wcmbs_settings() {
		return YITH_WCMBS_Settings::get_instance();
	}
}