<?php
/**
 * Storefront Powerpack Customizer Product Details Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer_Product_Details' ) ) :

	/**
	 * The Customizer class.
	 */
	class SP_Customizer_Product_Details extends SP_Customizer {

		/**
		 * The id of this section.
		 *
		 * @const string
		 */
		const POWERPACK_PRODUCT_DETAILS_SECTION = 'sp_product_details_section';

		/**
		 * Returns an array of the Storefront Powerpack setting defaults.
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function setting_defaults() {
			return $args = array(
				'sp_visit_product_prompt'   => '',
				'sp_product_layout'         => 'default',
				'sp_product_gallery_layout' => 'default',
				'sp_product_details_tab'    => true,
				'sp_related_products'       => true,
				'sp_product_description'    => true,
				'sp_product_meta'           => true,
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
			* Product Details Section
			*/
			$wp_customize->add_section( self::POWERPACK_PRODUCT_DETAILS_SECTION, array(
				'title'    => __( 'Product Details', 'storefront-powerpack' ),
				'panel'    => self::POWERPACK_PANEL,
				'priority' => 60,
			) );

			/**
			 * A prompt to visit the shop page.
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_setting( 'sp_visit_product_prompt', array(
						'sanitize_callback' => 'sanitize_text_field',
					)
				);

				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_visit_product_prompt', array(
					'description'     => '<div class="sp-section-notice"><span class="dashicons dashicons-info"></span>' . __( 'These settings do not affect the page you\'re currently previewing. Visit a product page to see their effects.', 'storefront-powerpack' ) . '</div>',
					'section'         => self::POWERPACK_PRODUCT_DETAILS_SECTION,
					'type'            => 'text',
					'settings'        => 'sp_visit_product_prompt',
					'active_callback' => array( $this, 'is_not_product_page' ),
					'priority'        => 10,
					)
				) );
			}

			/**
			 * Page Layout
			 */
			$wp_customize->add_setting( 'sp_product_layout', array(
				'sanitize_callback'	=> 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new SP_Buttonset_Control( $wp_customize, 'sp_product_layout', array(
				'label'    => __( 'Page layout', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PRODUCT_DETAILS_SECTION,
				'settings' => 'sp_product_layout',
				'type'     => 'select',
				'priority' => 20,
				'choices'  => array(
					'default'    => 'Default',
					'full-width' => 'Full Width',
				),
			) ) );

			/**
			 * Gallery layout
			 */
			$wp_customize->add_setting( 'sp_product_gallery_layout', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new SP_Buttonset_Control( $wp_customize, 'sp_product_gallery_layout', array(
				'label'    => __( 'Gallery layout', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PRODUCT_DETAILS_SECTION,
				'settings' => 'sp_product_gallery_layout',
				'type'     => 'select',
				'priority' => 30,
				'choices'  => array(
					'default' => 'Default',
					'stacked' => 'Stacked',
					'hidden'  => 'Hidden',
				),
			) ) );

			/**
			 * Storefront Divider
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_gallery_divider', array(
					'section'  => self::POWERPACK_PRODUCT_DETAILS_SECTION,
					'type'     => 'divider',
					'priority' => 40,
				) ) );
			}

			/**
			 * Display product tabs
			 */
			$wp_customize->add_setting( 'sp_product_details_tab', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_product_details_tab', array(
				'label'    => __( 'Display product tabs', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PRODUCT_DETAILS_SECTION,
				'settings' => 'sp_product_details_tab',
				'type'     => 'checkbox',
				'priority' => 50,
			) ) );

			/**
			 * Display related products
			 */
			$wp_customize->add_setting( 'sp_related_products', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_related_products', array(
				'label'    => __( 'Display related products', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PRODUCT_DETAILS_SECTION,
				'settings' => 'sp_related_products',
				'type'     => 'checkbox',
				'priority' => 60,
			) ) );

			/**
			 * Display product description
			 */
			$wp_customize->add_setting( 'sp_product_description', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_product_description', array(
				'label'    => __( 'Display product description', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PRODUCT_DETAILS_SECTION,
				'settings' => 'sp_product_description',
				'type'     => 'checkbox',
				'priority' => 70,
			) ) );

			/**
			 * Display product meta
			 */
			$wp_customize->add_setting( 'sp_product_meta', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_product_meta', array(
				'label'    => __( 'Display product meta', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_PRODUCT_DETAILS_SECTION,
				'settings' => 'sp_product_meta',
				'type'     => 'checkbox',
				'priority' => 80,
			) ) );

			/**
			 * Storefront Text
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_product_info', array(
					'section'     => self::POWERPACK_PRODUCT_DETAILS_SECTION,
					'type'        => 'text',
					'description' => '<div style="padding: 10px; background-color: #fff; border: 1px solid #ccc;"><span class="dashicons dashicons-info" style="color: #007cb2; float: right; margin-left: 1em;"></span>' . sprintf( __( 'These settings are applied globally to all products in your store. If you\'d like to apply a different layout to a specific product you can do so by visiting the %sEdit Product%s page in your dashboard and looking for the Storefront tab in the product data tabs.', 'storefront-powerpack' ), '<a style="text-decoration: underline; font-weight: 700;" href="' . esc_url( admin_url( 'edit.php?post_type=product' ) ) . '">', '</a>' ) . '</div>',
					'priority'    => 90,
				) ) );
			}
		}

		/**
		 * Checks if the page currently being previewed is not a product page
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function is_not_product_page() {
			if ( is_product() ) {
				return false;
			} else {
				return true;
			}
		}
	}

endif;

return new SP_Customizer_Product_Details();