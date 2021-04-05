<?php
/**
 * Storefront Powerpack Customizer Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer' ) ) :

	/**
	 * The Customizer class.
	 */
	class SP_Customizer {

		/**
		 * The id of the Powerpack panel.
		 *
		 * @const string
		 */
		const POWERPACK_PANEL = 'sp_panel';

		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'customizer_enqueue' ) );
			add_action( 'customize_register', array( $this, 'customize_register' ), 20 );
			add_action( 'customize_register', array( $this, 'edit_default_customizer_settings' ), 99 );
			add_action( 'init', array( $this, 'default_theme_mod_values' ), 10 );
			add_filter( 'storefront_customizer_more', '__return_false' );
		}

		/**
		 * Enqueue styles common to all Powerpack sections.
		 *
		 * @since 1.0.0
		 */
		final public function customizer_enqueue() {
			wp_enqueue_style( 'sp-customizer', SP_PLUGIN_URL . 'assets/css/customizer.css', array(), storefront_powerpack()->version, 'all' );
		}

		/**
		 * Returns an array of the Storefront Powerpack setting defaults.
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function setting_defaults() {
			return $args = array();
		}

		/**
		 * Adds a value to each Powerpack setting if one isn't already present.
		 *
		 * @uses setting_defaults()
		 * @since 1.0.1
		 */
		public function default_theme_mod_values() {
			foreach ( $this->setting_defaults() as $mod => $val ) {
				add_filter( 'theme_mod_' . $mod, array( $this, 'get_theme_mod_value' ), 10 );
			}
		}

		/**
		 * Get theme mod value.
		 *
		 * @param string $value
		 * @return string
		 * @since 1.0.1
		 */
		public function get_theme_mod_value( $value ) {
			$key = substr( current_filter(), 10 );

			$set_theme_mods = get_theme_mods();

			if ( isset( $set_theme_mods[ $key ] ) ) {
				return $value;
			}

			$values = $this->setting_defaults();

			return isset( $values[ $key ] ) ? $values[ $key ] : $value;
		}

		/**
		 * Set Customizer setting defaults.
		 * These defaults need to be applied separately as child themes can filter storefront_setting_default_values
		 *
		 * @param array $wp_customize the Customizer object.
		 * @uses setting_defaults()
		 * @since 1.0.0
		 */
		public function edit_default_customizer_settings( $wp_customize ) {
			foreach ( $this->setting_defaults() as $mod => $val ) {
				$setting = $wp_customize->get_setting( $mod );
				if ( null !== $setting ) {
					$setting->default = $val;
				}
			}
		}

		/**
		 * Customizer Controls and Settings
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @since 1.0.0
		 */
		public function customize_register( $wp_customize ) {
			/**
			 * Include custom controls.
			 */
			require_once dirname( __FILE__ ) . '/customizer/controls/class-sp-buttonset-control.php';

			/**
			 * Powepack Panel
			 */
			$wp_customize->add_panel( self::POWERPACK_PANEL, array(
				'priority'       => 1,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __( 'Powerpack ⚡', 'storefront-powerpack' )
			) );
		}
	}

endif;

return new SP_Customizer();