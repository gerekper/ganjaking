<?php
/**
 * Storefront Powerpack Customizer Layout Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer_Layout' ) ) :

	/**
	 * The Customizer class.
	 */
	class SP_Customizer_Layout extends SP_Customizer {

		/**
		 * The id of this section.
		 *
		 * @const string
		 */
		const POWERPACK_LAYOUT_SECTION = 'sp_layout_section';

		/**
		 * Returns an array of the Storefront Powerpack setting defaults.
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function setting_defaults() {
			return $args = array(
				'sp_max_width'                => 'default',
				'sp_content_frame'            => 'default',
				'sp_content_frame_background' => '#ffffff'
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
			* Layout Section
			*/
			$wp_customize->add_section( self::POWERPACK_LAYOUT_SECTION, array(
				'title'    => __( 'Layout', 'storefront-powerpack' ),
				'panel'    => self::POWERPACK_PANEL,
				'priority' => 20,
			) );

			/**
			 * Max width
			 */
			$wp_customize->add_setting( 'sp_max_width', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new SP_Buttonset_Control( $wp_customize, 'sp_max_width', array(
				'label'       => __( 'Page layout', 'storefront-powerpack' ),
				'description' => __( 'Define the width of your site content.', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_LAYOUT_SECTION,
				'settings'    => 'sp_max_width',
				'type'        => 'select',
				'priority'    => 10,
				'choices'     => array(
					'default'   => 'Default',
					'max-width' => 'Max Width',
				),
			) ) );

			/**
			 * Storefront Text
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_max_width_info', array(
					'section'     => self::POWERPACK_LAYOUT_SECTION,
					'type'        => 'text',
					'description' => '<div style="padding: 10px; background-color: #fff; border: 1px solid #ccc;"><span class="dashicons dashicons-info" style="color: #007cb2; float: right; margin-left: 1em;"></span>' . sprintf( __( 'The "Max Width" option makes website content span the full width of the browser window rather than having a fixed width. With this option enabled, after publishing your changes and reloading the Customizer, you might want to adjust the size of the %sWooCommerce images%s.', 'storefront-powerpack' ), '<a style="text-decoration: underline; font-weight: 700;" href="https://docs.woocommerce.com/document/image-sizes-theme-developers/#section-2">', '</a>' ) . '</div>',
					'priority'    => 20,
				) ) );
			}

			/**
			 * Storefront Divider
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_max_width_divider', array(
					'section'  => self::POWERPACK_LAYOUT_SECTION,
					'type'     => 'divider',
					'priority' => 30,
				) ) );
			}

			/**
			 * Content frame
			 */
			$wp_customize->add_setting( 'sp_content_frame', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new SP_Buttonset_Control( $wp_customize, 'sp_content_frame', array(
				'label'       => __( 'Content frame', 'storefront-powerpack' ),
				'description' => __( 'Wraps the site content in a frame, offsetting it from the background.', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_LAYOUT_SECTION,
				'settings'    => 'sp_content_frame',
				'type'        => 'select',
				'priority'    => 40,
				'choices'     => array(
					'default'  => __( 'Default', 'storefront-powerpack' ),
					'frame'    => __( 'Frame', 'storefront-powerpack' ),
				),
			) ) );

			/**
			 * Content frame background
			 */
			$wp_customize->add_setting( 'sp_content_frame_background', array(
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sp_content_frame_background', array(
				'label'	          => __( 'Content background color', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_LAYOUT_SECTION,
				'settings'        => 'sp_content_frame_background',
				'active_callback' => array( $this, 'is_content_frame_active' ),
				'priority'        => 50,
			) ) );
		}

		/**
		 * Checks if the page currently being previewed is not a shop page
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function is_content_frame_active() {
			if ( 'frame' === get_theme_mod( 'sp_content_frame' ) ) {
				return true;
			}

			return false;
		}
	}

endif;

return new SP_Customizer_Layout();