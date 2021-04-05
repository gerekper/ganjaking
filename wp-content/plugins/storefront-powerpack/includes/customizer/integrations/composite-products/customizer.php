<?php
/**
 * Storefront Powerpack Customizer Composite Product Details Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer_Composite_Product_Details' ) ) :

	/**
	 * The Customizer class.
	 */
	class SP_Customizer_Composite_Product_Details extends SP_Customizer {

		/**
		 * The id of this section.
		 *
		 * @const string
		 */
		const POWERPACK_COMPOSITE_PRODUCTS_SECTION = 'sp_cp_section';

		/**
		 * Returns an array of the Composite Products integration setting defaults.
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function setting_defaults() {
			return array(
				'sp_cp_component_options_loop_columns' => '3',
				'sp_cp_component_options_per_page'     => '6',
				'sp_cp_summary_max_columns'            => '6',
				'sp_cp_component_toggled'              => 'progressive'
			);
		}

		/**
		 * Customizer Composite Products settings.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		public function customize_register( $wp_customize ) {

			/**
		     * Composite Products section.
		     */
			$wp_customize->add_section( self::POWERPACK_COMPOSITE_PRODUCTS_SECTION, array(
				'title'       => __( 'Composite Product Details', 'storefront-powerpack' ),
				'panel'       => self::POWERPACK_PANEL,
				'priority'    => 60.5,
			) );

			/**
			 * A prompt to visit a Composite Product page.
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_setting( 'sp_visit_composite_product_prompt', array(
						'sanitize_callback' => 'sanitize_text_field',
					)
				);

				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_visit_composite_product_prompt', array(
					'description'     => '<div class="sp-section-notice"><span class="dashicons dashicons-info"></span>' . __( 'These settings do not affect the page you\'re currently previewing. Visit a Composite product page to see their effects.', 'storefront-powerpack' ) . '</div>',
					'section'         => self::POWERPACK_COMPOSITE_PRODUCTS_SECTION,
					'type'            => 'text',
					'settings'        => 'sp_visit_composite_product_prompt',
					'active_callback' => array( $this, 'is_not_composite_product_page' ),
					'priority'        => 0,
					)
				) );
			}

			/**
	         * Component Options (Product) Columns.
	         */
			$wp_customize->add_setting( 'sp_cp_component_options_loop_columns', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_cp_component_options_loop_columns', array(
				'label'       => __( 'Options columns', 'storefront-powerpack' ),
				'description' => sprintf( __( 'Applicable when the %1$sThumbnails%2$s style is active.', 'storefront-powerpack' ), '<strong>', '</strong>' ),
				'section'     => self::POWERPACK_COMPOSITE_PRODUCTS_SECTION,
				'settings'    => 'sp_cp_component_options_loop_columns',
				'type'        => 'select',
				'priority'    => 10,
				'choices'     => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5'
				)
			) ) );

			/**
	         * Component Options per Page.
	         */
			$wp_customize->add_setting( 'sp_cp_component_options_per_page', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_cp_component_options_per_page', array(
				'label'       => __( 'Options per page', 'storefront-powerpack' ),
				'description' => sprintf( __( 'Applicable when the %1$sThumbnails%2$s style is active.', 'storefront-powerpack' ), '<strong>', '</strong>' ),
				'section'     => self::POWERPACK_COMPOSITE_PRODUCTS_SECTION,
				'settings'    => 'sp_cp_component_options_per_page',
				'type'        => 'select',
				'priority'    => 20,
				'choices'     => array(
					'1'  => '1',
					'2'  => '2',
					'3'  => '3',
					'4'  => '4',
					'5'  => '5',
					'6'  => '6',
					'7'  => '7',
					'8'  => '8',
					'9'  => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
					'13' => '13',
					'14' => '14',
					'15' => '15',
					'16' => '16',
					'17' => '17',
					'18' => '18',
					'19' => '19',
					'20' => '20',
					'21' => '21',
					'22' => '22',
					'23' => '23',
					'24' => '24'
				)
			) ) );

			/**
	         * Max columns in Summary/Review section.
	         */
			$wp_customize->add_setting( 'sp_cp_summary_max_columns', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_cp_summary_max_columns', array(
				'label'       => __( 'Max columns in Summary', 'storefront-powerpack' ),
				'description' => sprintf( __( 'Applicable when using the %1$sStepped%2$s or %1$sComponentized%2$s layout.', 'storefront-powerpack' ), '<strong>', '</strong>' ),
				'section'     => self::POWERPACK_COMPOSITE_PRODUCTS_SECTION,
				'settings'    => 'sp_cp_summary_max_columns',
				'type'        => 'select',
				'priority'    => 30,
				'choices'     => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8'
				)
			) ) );

			/**
	         * Toggle Box view.
	         */
			$wp_customize->add_setting( 'sp_cp_component_toggled', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_cp_component_toggled', array(
				'label'       => __( 'Toggle-box view', 'storefront-powerpack' ),
				'description' => __( 'Apply the toggle-box Component view to the following layout(s):', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_COMPOSITE_PRODUCTS_SECTION,
				'settings'    => 'sp_cp_component_toggled',
				'type'        => 'select',
				'priority'    => 50,
				'choices'     => array(
					'single'      => __( 'Stacked', 'storefront-powerpack' ),
					'progressive' => __( 'Progressive', 'storefront-powerpack' ),
					'both'        => __( 'Both', 'storefront-powerpack' ),
					'none'        => __( 'None', 'storefront-powerpack' )
				)
			) ) );
		}

		/**
		 * Checks if the page currently being previewed is not a product page
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function is_not_composite_product_page() {
			if ( is_composite_product() ) {
				return false;
			} else {
				return true;
			}
		}
	}

endif;

return new SP_Customizer_Composite_Product_Details();
