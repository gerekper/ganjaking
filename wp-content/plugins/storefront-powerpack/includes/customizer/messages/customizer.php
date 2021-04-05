<?php
/**
 * Storefront Powerpack Customizer Messages Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer_Messages' ) ) :

	/**
	 * The Customizer class.
	 */
	class SP_Customizer_Messages extends SP_Customizer {

		/**
		 * The id of this section.
		 *
		 * @const string
		 */
		const POWERPACK_MESSAGES_SECTION = 'sp_messages_section';

		/**
		 * Returns an array of the Storefront Powerpack setting defaults.
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function setting_defaults() {
			return $args = array(
				'sp_message_background_color' => '#0f834d',
				'sp_message_text_color'       => '#ffffff',
				'sp_info_background_color'    => '#3D9CD2',
				'sp_info_text_color'          => '#ffffff',
				'sp_error_background_color'   => '#e2401c',
				'sp_error_text_color'         => '#ffffff'
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
			* Messages Section
			*/
			$wp_customize->add_section( self::POWERPACK_MESSAGES_SECTION, array(
				'title'    => __( 'Messages', 'storefront-powerpack' ),
				'panel'    => self::POWERPACK_PANEL,
				'priority' => 70,
			) );

			/**
			 * Success message background color
			 */
			$wp_customize->add_setting( 'sp_message_background_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sp_message_background_color', array(
				'label'	          => __( 'Success message background color', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_MESSAGES_SECTION,
				'settings'        => 'sp_message_background_color',
				'priority'        => 10,
			) ) );

			/**
			 * Success message text color
			 */
			$wp_customize->add_setting( 'sp_message_text_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sp_message_text_color', array(
				'label'	          => __( 'Success message text color', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_MESSAGES_SECTION,
				'settings'        => 'sp_message_text_color',
				'priority'        => 20,
			) ) );

			/**
			 * Info message background color
			 */
			$wp_customize->add_setting( 'sp_info_background_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sp_info_background_color', array(
				'label'	          => __( 'Info message background color', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_MESSAGES_SECTION,
				'settings'        => 'sp_info_background_color',
				'priority'        => 30,
			) ) );

			/**
			 * Info message text color
			 */
			$wp_customize->add_setting( 'sp_info_text_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sp_info_text_color', array(
				'label'	          => __( 'Info message text color', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_MESSAGES_SECTION,
				'settings'        => 'sp_info_text_color',
				'priority'        => 40,
			) ) );

			/**
			 * Error message background color
			 */
			$wp_customize->add_setting( 'sp_error_background_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sp_error_background_color', array(
				'label'	          => __( 'Error message background color', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_MESSAGES_SECTION,
				'settings'        => 'sp_error_background_color',
				'priority'        => 50,
			) ) );

			/**
			 * Error message text color
			 */
			$wp_customize->add_setting( 'sp_error_text_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sp_error_text_color', array(
				'label'	          => __( 'Error message text color', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_MESSAGES_SECTION,
				'settings'        => 'sp_error_text_color',
				'priority'        => 60,
			) ) );
		}
	}

endif;

return new SP_Customizer_Messages();