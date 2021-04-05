<?php
/**
 * Storefront Powerpack Customizer Shop Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer_Shop' ) ) :

	/**
	 * The Customizer class.
	 */
	class SP_Customizer_Shop extends SP_Customizer {

		/**
		 * The id of this section.
		 *
		 * @const string
		 */
		const POWERPACK_SHOP_SECTION = 'sp_section';

		/**
		 * Returns an array of the Storefront Powerpack setting defaults.
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function setting_defaults() {
			return $args = array(
				'sp_visit_shop_prompt'             => '',
				'sp_shop_layout'                   => 'default',
				'sp_archive_description'           => 'default',
				'sp_product_columns'               => '3',
				'sp_products_per_page'             => '12',
				'sp_shop_alignment'                => 'center',
				'sp_product_archive_results_count' => true,
				'sp_product_archive_sorting'       => true,
				'sp_product_archive_image'         => true,
				'sp_product_archive_title'         => true,
				'sp_product_archive_sale_flash'    => true,
				'sp_product_archive_rating'        => true,
				'sp_product_archive_price'         => true,
				'sp_product_archive_add_to_cart'   => true,
				'sp_product_archive_description'   => false,
				'sp_infinite_scroll'               => false,
				'sp_reviews_star_color'            => '#FFA200',
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
			* Shop Section
			*/
			$wp_customize->add_section( self::POWERPACK_SHOP_SECTION, array(
				'title'    => __( 'Shop', 'storefront-powerpack' ),
				'panel'    => self::POWERPACK_PANEL,
				'priority' => 90,
			) );

			/**
			 * A prompt to visit the shop page.
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_setting( 'sp_visit_shop_prompt', array(
					'sanitize_callback' => 'sanitize_text_field',
				) );

				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_visit_shop_prompt', array(
					'description'     => '<div class="sp-section-notice"><span class="dashicons dashicons-info"></span>' . __( 'These settings do not affect the page you\'re currently previewing. Visit a shop page to see their effects.', 'storefront-powerpack' ) . '</div>',
					'section'         => self::POWERPACK_SHOP_SECTION,
					'type'            => 'text',
					'settings'        => 'sp_visit_shop_prompt',
					'active_callback' => array( $this, 'is_not_shop_page' ),
					'priority'        => 10,
					)
				) );
			}

			/**
			 * Shop Layout
			 */
			$wp_customize->add_setting( 'sp_shop_layout', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new SP_Buttonset_Control( $wp_customize, 'sp_shop_layout', array(
				'label'           => __( 'Page layout', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_SHOP_SECTION,
				'settings'        => 'sp_shop_layout',
				'type'            => 'select',
				'active_callback' => array( $this, 'is_not_homepage_template' ),
				'priority'        => 20,
				'choices'         => array(
					'default'    => 'Default',
					'full-width' => 'Full Width',
				),
			) ) );

			/**
			 * Archive Description
			 */
			$wp_customize->add_setting( 'sp_archive_description', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new SP_Buttonset_Control( $wp_customize, 'sp_archive_description', array(
				'label'           => __( 'Description', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_SHOP_SECTION,
				'settings'        => 'sp_archive_description',
				'type'            => 'select',
				'priority'        => 30,
				'active_callback' => array( $this, 'is_not_homepage_template' ),
				'choices'         => array(
					'default' => 'Above products',
					'beneath' => 'Beneath products',
				),
			) ) );

			/**
			 * Product Columns
			 */
			$wp_customize->add_setting( 'sp_product_columns', array(
				'sanitize_callback'	=> 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_product_columns', array(
				'label'    => __( 'Columns', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_SHOP_SECTION,
				'settings' => 'sp_product_columns',
				'type'     => 'select',
				'priority' => 40,
				'choices'  => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'active_callback' => array( $this, 'check_compatibility' ),
			) ) );

			/**
			 * Products per Page
			 */
			$wp_customize->add_setting( 'sp_products_per_page', array(
				'sanitize_callback'	=> 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_products_per_page', array(
				'label'    => __( 'Products per page', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_SHOP_SECTION,
				'settings' => 'sp_products_per_page',
				'type'     => 'select',
				'priority' => 50,
				'choices'  => array(
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
					'24' => '24',
				),
				'active_callback' => array( $this, 'check_compatibility' ),
			) ) );

			/**
			 * Storefront Divider
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_products_per_page_divider', array(
					'section'  => self::POWERPACK_SHOP_SECTION,
					'type'     => 'divider',
					'priority' => 60,
				) ) );
			}

			/**
			 * Product Alignment
			 */
			$wp_customize->add_setting( 'sp_shop_alignment', array(
				'sanitize_callback'	=> 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new SP_Buttonset_Control( $wp_customize, 'sp_shop_alignment', array(
				'label'    => __( 'Content alignment', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_SHOP_SECTION,
				'settings' => 'sp_shop_alignment',
				'type'     => 'select',
				'priority' => 70,
				'choices'  => array(
					'left'   => 'Left',
					'center' => 'Center',
					'right'  => 'Right',
				),
			) ) );

			/**
			 * Storefront Divider
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_shop_layout_divider', array(
					'section'  => self::POWERPACK_SHOP_SECTION,
					'type'     => 'divider',
					'priority' => 80,
				) ) );
			}

			/**
			 * Archive Result Count
			 */
			$wp_customize->add_setting( 'sp_product_archive_results_count', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_product_archive_results_count', array(
				'label'           => __( 'Display product results count', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_SHOP_SECTION,
				'settings'        => 'sp_product_archive_results_count',
				'type'            => 'checkbox',
				'active_callback' => array( $this, 'is_not_homepage_template' ),
				'priority'        => 90,
			) ) );

			/**
			 * Archive Sorting
			 */
			$wp_customize->add_setting( 'sp_product_archive_sorting', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_product_archive_sorting', array(
				'label'           => __( 'Display product sorting', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_SHOP_SECTION,
				'settings'        => 'sp_product_archive_sorting',
				'type'            => 'checkbox',
				'active_callback' => array( $this, 'is_not_homepage_template' ),
				'priority'        => 100,
			) ) );

			/**
			 * Archive Image
			 */
			$wp_customize->add_setting( 'sp_product_archive_image', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_product_archive_image', array(
				'label'    => __( 'Display product image', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_SHOP_SECTION,
				'settings' => 'sp_product_archive_image',
				'type'     => 'checkbox',
				'priority' => 110,
			) ) );

			/**
			 * Archive title
			 */
			$wp_customize->add_setting( 'sp_product_archive_title', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_product_archive_title', array(
				'label'    => __( 'Display product title', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_SHOP_SECTION,
				'settings' => 'sp_product_archive_title',
				'type'     => 'checkbox',
				'priority' => 120,
			) ) );

			/**
			 * Archive Sale Flash
			 */
			$wp_customize->add_setting( 'sp_product_archive_sale_flash', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_product_archive_sale_flash', array(
				'label'    => __( 'Display sale flash', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_SHOP_SECTION,
				'settings' => 'sp_product_archive_sale_flash',
				'type'     => 'checkbox',
				'priority' => 130,
			) ) );

			/**
			 * Archive Rating
			 */
			$wp_customize->add_setting( 'sp_product_archive_rating', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_product_archive_rating', array(
				'label'    => __( 'Display rating', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_SHOP_SECTION,
				'settings' => 'sp_product_archive_rating',
				'type'     => 'checkbox',
				'priority' => 140,
			) ) );

			/**
			 * Archive Price
			 */
			$wp_customize->add_setting( 'sp_product_archive_price', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_product_archive_price', array(
				'label'    => __( 'Display price', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_SHOP_SECTION,
				'settings' => 'sp_product_archive_price',
				'type'     => 'checkbox',
				'priority' => 150,
			) ) );

			/**
			 * Archive Add To Cart
			 */
			$wp_customize->add_setting( 'sp_product_archive_add_to_cart', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_product_archive_add_to_cart', array(
				'label'    => __( 'Display add to cart button', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_SHOP_SECTION,
				'settings' => 'sp_product_archive_add_to_cart',
				'type'     => 'checkbox',
				'priority' => 160,
			) ) );

			/**
			 * Archive Description
			 */
			$wp_customize->add_setting( 'sp_product_archive_description', array(
				'sanitize_callback'	=> 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_product_archive_description', array(
				'label'    => __( 'Display description', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_SHOP_SECTION,
				'settings' => 'sp_product_archive_description',
				'type'     => 'checkbox',
				'priority' => 170,
			) ) );

			/**
			 * Infinite Scroll
			 */
			$wp_customize->add_setting( 'sp_infinite_scroll', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_infinite_scroll', array(
				'label'           => __( 'Enable infinite scroll', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_SHOP_SECTION,
				'settings'        => 'sp_infinite_scroll',
				'type'            => 'checkbox',
				'priority'        => 180,
				'active_callback' => array( $this, 'is_infinite_scroll_active' ),
			) ) );

			/**
			 * Storefront Divider
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_infinite_scroll_divider', array(
					'section'  => self::POWERPACK_SHOP_SECTION,
					'type'     => 'divider',
					'priority' => 190,
				) ) );
			}

			/**
			 * Star color
			 */
			$wp_customize->add_setting( 'sp_reviews_star_color', array(
				'sanitize_callback' => 'sanitize_hex_color',
			) );

			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sp_reviews_star_color', array(
				'label'       => __( 'Star color', 'storefront-powerpack' ),
				'description' => __( 'The color of the star ratings throughout the store', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_SHOP_SECTION,
				'settings'    => 'sp_reviews_star_color',
				'priority'    => 200,
			) ) );
		}

		/**
		 * Checks if the page currently being previewed is not a shop page
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function is_not_shop_page() {
			if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() || is_page_template( 'template-homepage.php' ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Checks if the page currently being previewed is not a shop page
		 *
		 * @return bool
		 * @since  1.4.9
		 */
		public function is_not_homepage_template() {
			if ( is_page_template( 'template-homepage.php' ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Checks if Infinite Scroll is active.
		 * Removes the option from users that are not using it.
		 *
		 * @return bool
		 * @since  1.2.0
		 */
		public function is_infinite_scroll_active() {
			if ( true === get_theme_mod( 'sp_infinite_scroll' ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Checks if activated WooCommerce version is below 3.3.
		 *
		 * @return bool
		 * @since  1.4.5
		 */
		public function check_compatibility() {
			return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.3', '<' );
		}
	}

endif;

return new SP_Customizer_Shop();