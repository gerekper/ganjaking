<?php
/**
 * Storefront Powerpack Frontend Footer Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer_Footer' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Customizer_Footer extends SP_Customizer {
		/**
		 * The id of this section.
		 *
		 * @const string
		 */
		const POWERPACK_FOOTER_SECTION = 'sp_footer_section';

		/**
		 * Returns an array of the Storefront Powerpack setting defaults.
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function setting_defaults() {
			return $args = array(
				'sp_handheld_footer_bar' => true,
			);
		}

		/**
		 * Customizer Controls and Settings
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @since 1.0.0
		 */
		public function customize_register( $wp_customize ) {
			/**
			* Footer Section
			*/
			$wp_customize->add_section( self::POWERPACK_FOOTER_SECTION, array(
				'title'    => __( 'Footer', 'storefront-powerpack' ),
				'panel'    => self::POWERPACK_PANEL,
				'priority' => 50,
			) );

			/**
			 * Footer copyright text
			 */
			$wp_customize->add_setting( 'sp_footer_copyright', array(
				'default' => apply_filters( 'storefront_copyright_text', $content = '&copy; ' . get_bloginfo( 'name' ) . ' ' . get_the_date( 'Y' ) ),
			) );
			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_footer_copyright', array(
				'label'       => __( 'Footer text', 'storefront-powerpack' ),
				'description' => __( 'Tweak the copyright text in the footer.', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_FOOTER_SECTION,
				'settings'    => 'sp_footer_copyright',
				'type'        => 'text',
				'priority'    => 10,
			) ) );

			/**
			 * Footer credit
			 */
			$wp_customize->add_setting( 'sp_footer_credit', array(
				'default' => true,
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_footer_credit', array(
				'label'       => __( 'Display credit link', 'storefront-powerpack' ),
				'description' => __( 'Toggle the Storefront/WooThemes credit link in the footer.', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_FOOTER_SECTION,
				'settings'    => 'sp_footer_credit',
				'type'        => 'checkbox',
				'priority'    => 20,
			) ) );

			/**
			 * Turn off handheld footer bar
			 */
			$wp_customize->add_setting( 'sp_handheld_footer_bar', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_handheld_footer_bar', array(
				'label'       => __( 'Handheld Footer Bar', 'storefront-powerpack' ),
				'description' => __( 'Toggles the display of the footer bar when viewed on handheld devices.', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_FOOTER_SECTION,
				'settings'    => 'sp_handheld_footer_bar',
				'type'        => 'checkbox',
				'priority'    => 30,
				)
			) );

		}
	}

endif;

return new SP_Customizer_Footer();