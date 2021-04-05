<?php
/**
 * Storefront Powerpack Customizer Homepage Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer_Homepage' ) ) :

	/**
	 * The Customizer class.
	 */
	class SP_Customizer_Homepage extends SP_Customizer {

		/**
		 * The id of this section.
		 *
		 * @const string
		 */
		const POWERPACK_HOMEPAGE_SECTION = 'sp_homepage';

		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			add_action( 'customize_controls_print_styles', array( $this, 'customizer_section_css' ) );
		}

		/**
		 * Returns an array of the Storefront Powerpack setting defaults.
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function setting_defaults() {
			return $args = array(
				'sp_visit_homepage'                             => '',
				'sp_homepage_content'                           => true,
				'sp_homepage_categories'                        => true,
				'sp_homepage_category_title'                    => __( 'Product Categories', 'storefront-powerpack' ),
				'sp_homepage_category_description'              => '',
				'sp_homepage_category_columns'                  => '3',
				'sp_homepage_category_limit'                    => '3',
				'sp_homepage_category_more_url'                 => '',
				'sp_homepage_recent'                            => true,
				'sp_homepage_recent_products_title'             => __( 'Recent Products', 'storefront-powerpack' ),
				'sp_homepage_recent_products_description'       => '',
				'sp_homepage_recent_products_columns'           => '4',
				'sp_homepage_recent_products_limit'             => '4',
				'sp_homepage_recent_products_more_url'          => '',
				'sp_homepage_featured'                          => true,
				'sp_homepage_featured_products_title'           => __( 'Featured Products', 'storefront-powerpack' ),
				'sp_homepage_featured_products_description'     => '',
				'sp_homepage_featured_products_columns'         => '4',
				'sp_homepage_featured_products_limit'           => '4',
				'sp_homepage_featured_products_more_url'        => '',
				'sp_homepage_top_rated'                         => true,
				'sp_homepage_top_rated_products_title'          => __( 'Top rated Products', 'storefront-powerpack' ),
				'sp_homepage_top_rated_products_description'    => '',
				'sp_homepage_top_rated_products_columns'        => '4',
				'sp_homepage_top_rated_products_limit'          => '4',
				'sp_homepage_top_rated_products_more_url'       => '',
				'sp_homepage_on_sale'                           => true,
				'sp_homepage_on_sale_products_title'            => __( 'On sale Products', 'storefront-powerpack' ),
				'sp_homepage_on_sale_products_description'      => '',
				'sp_homepage_on_sale_products_columns'          => '4',
				'sp_homepage_on_sale_products_limit'            => '4',
				'sp_homepage_on_sale_products_more_url'         => '',
				'sp_homepage_best_sellers'                      => true,
				'sp_homepage_best_sellers_products_title'       => __( 'Best Sellers', 'storefront-powerpack' ),
				'sp_homepage_best_sellers_products_description' => '',
				'sp_homepage_best_sellers_products_columns'     => '4',
				'sp_homepage_best_sellers_products_limit'       => '4',
				'sp_homepage_best_sellers_products_more_url'    => ''
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
			 * Homepage Section
			 */
			$wp_customize->add_section( self::POWERPACK_HOMEPAGE_SECTION, array(
				'title'    => __( 'Homepage', 'storefront-powerpack' ),
				'priority' => 60,
				'panel'    => self::POWERPACK_PANEL,
			) );

			/**
			 * A prompt to visit the home page.
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_setting( 'sp_visit_homepage', array(
					'sanitize_callback' => 'sanitize_text_field',
				) );

				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_visit_homepage', array(
					'description'     => '<div class="sp-section-notice"><span class="dashicons dashicons-info"></span>' . sprintf( __( 'These settings do not affect the page you\'re currently previewing. Visit a page with the %sHomepage template%s enabled to see their effects.', 'storefront-powerpack' ), '<a target="_blank" href="https://docs.woocommerce.com/document/installation-configuration/#homepage" style="color: #fff; text-decoration: underline;">', '</a>' ) . '</div>',
					'section'         => self::POWERPACK_HOMEPAGE_SECTION,
					'type'            => 'text',
					'settings'        => 'sp_visit_homepage',
					'active_callback' => array( $this, 'is_not_storefront_homepage' ),
					'priority'        => 10,
				) ) );
			}

			/**
			 * Page Content Toggle
			 */
			$wp_customize->add_setting( 'sp_homepage_content', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_content', array(
				'label'    => __( 'Page content', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_HOMEPAGE_SECTION,
				'settings' => 'sp_homepage_content',
				'type'     => 'checkbox',
				'priority' => 20,
			) ) );

			/**
			 * Storefront Divider
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_home_content_divider', array(
					'section'  => self::POWERPACK_HOMEPAGE_SECTION,
					'type'     => 'divider',
					'priority' => 30,
				) ) );
			}

			/**
			 * Product Category Toggle
			 */
			$wp_customize->add_setting( 'sp_homepage_categories', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_categories', array(
				'label'    => __( 'Product categories', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_HOMEPAGE_SECTION,
				'settings' => 'sp_homepage_categories',
				'type'     => 'checkbox',
				'priority' => 40,
			) ) );

			/**
			 * Category Title
			 */
			$wp_customize->add_setting( 'sp_homepage_category_title', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_category_title', array(
				'label'           => __( 'Title', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_category_title',
				'type'            => 'text',
				'priority'        => 50,
				'active_callback' => array( $this, 'product_category_callback' ),
			) ) );

			/**
			 * Category Description
			 */
			$wp_customize->add_setting( 'sp_homepage_category_description', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_category_description', array(
				'label'           => __( 'Description', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_category_description',
				'type'            => 'textarea',
				'priority'        => 60,
				'active_callback' => array( $this, 'product_category_callback' ),
			) ) );

			/**
			 * Category Columns
			 */
			$wp_customize->add_setting( 'sp_homepage_category_columns', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_category_columns', array(
				'label'           => __( 'Columns', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_category_columns',
				'type'            => 'select',
				'priority'        => 70,
				'active_callback' => array( $this, 'product_category_callback' ),
				'choices'         => array(
					'1'	=> '1',
					'2'	=> '2',
					'3'	=> '3',
					'4'	=> '4',
					'5'	=> '5',
					'6'	=> '6',
				),
			) ) );

			/**
			 * Category limit
			 */
			$wp_customize->add_setting( 'sp_homepage_category_limit', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_category_limit', array(
				'label'           => __( 'Number of categories to display', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_category_limit',
				'type'            => 'select',
				'priority'        => 80,
				'active_callback' => array( $this, 'product_category_callback' ),
				'choices'         => array(
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
				),
			) ) );

			/**
			 * Category url
			 */
			$wp_customize->add_setting( 'sp_homepage_category_more_url', array(
				'sanitize_callback' => 'esc_url_raw',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_category_more_url', array(
				'label'           => __( '"View more" url', 'storefront-powerpack' ),
				'description'     => __( 'Add a url to append a "view more" button beneath product categories.', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_category_more_url',
				'type'            => 'url',
				'priority'        => 90,
				'active_callback' => array( $this, 'product_category_callback' ),
			) ) );

			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_home_product_cats_divider', array(
					'section'  => self::POWERPACK_HOMEPAGE_SECTION,
					'type'     => 'divider',
					'priority' => 100,
				) ) );
			}

			/**
			 * Recent Products Toggle
			 */
			$wp_customize->add_setting( 'sp_homepage_recent', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_recent', array(
				'label'    => __( 'Recent products', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_HOMEPAGE_SECTION,
				'settings' => 'sp_homepage_recent',
				'type'     => 'checkbox',
				'priority' => 110,
			) ) );

			/**
			 * Recent Products Title
			 */
			$wp_customize->add_setting( 'sp_homepage_recent_products_title', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_recent_products_title', array(
				'label'           => __( 'Title', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_recent_products_title',
				'type'            => 'text',
				'priority'        => 120,
				'active_callback' => array( $this, 'recent_products_callback' ),
			) ) );

			/**
			 * Recent Products Description
			 */
			$wp_customize->add_setting( 'sp_homepage_recent_products_description', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_recent_products_description', array(
				'label'           => __( 'Description', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_recent_products_description',
				'type'            => 'textarea',
				'priority'        => 130,
				'active_callback' => array( $this, 'recent_products_callback' ),
			) ) );

			/**
			 * Recent Products Columns
			 */
			$wp_customize->add_setting( 'sp_homepage_recent_products_columns', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_recent_products_columns', array(
				'label'           => __( 'Columns', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_recent_products_columns',
				'type'            => 'select',
				'priority'        => 140,
				'active_callback' => array( $this, 'recent_products_callback' ),
				'choices'         => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6'	=> '6',
				),
			) ) );

			/**
			 * Recent Products limit
			 */
			$wp_customize->add_setting( 'sp_homepage_recent_products_limit', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_recent_products_limit', array(
				'label'           => __( 'Number of products to display', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_recent_products_limit',
				'type'            => 'select',
				'active_callback' => array( $this, 'recent_products_callback' ),
				'priority'        => 150,
				'choices'         => array(
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
				),
			) ) );

			/**
			 * Recent products url
			 */
			$wp_customize->add_setting( 'sp_homepage_recent_products_more_url', array(
				'sanitize_callback' => 'esc_url_raw',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_recent_products_more_url', array(
				'label'           => __( '"View more" url', 'storefront-powerpack' ),
				'description'     => __( 'Add a url to append a "view more" button beneath recent products.', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_recent_products_more_url',
				'type'            => 'url',
				'priority'        => 160,
				'active_callback' => array( $this, 'recent_products_callback' ),
			) ) );

			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_home_recent_products_divider', array(
					'section'  => self::POWERPACK_HOMEPAGE_SECTION,
					'type'     => 'divider',
					'priority' => 170,
				) ) );
			}

			/**
			 * Featured Products Toggle
			 */
			$wp_customize->add_setting( 'sp_homepage_featured', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_featured', array(
				'label'    => __( 'Featured products', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_HOMEPAGE_SECTION,
				'settings' => 'sp_homepage_featured',
				'type'     => 'checkbox',
				'priority' => 180,
			) ) );

			/**
			 * Featured Products Title
			 */
			$wp_customize->add_setting( 'sp_homepage_featured_products_title', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_featured_products_title', array(
				'label'           => __( 'Title', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_featured_products_title',
				'type'            => 'text',
				'priority'        => 190,
				'active_callback' => array( $this, 'featured_products_callback' ),
			) ) );

			/**
			 * Featured Products description
			 */
			$wp_customize->add_setting( 'sp_homepage_featured_products_description', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_featured_products_description', array(
				'label'           => __( 'Description', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_featured_products_description',
				'type'            => 'textarea',
				'priority'        => 200,
				'active_callback' => array( $this, 'featured_products_callback' ),
			) ) );

			/**
			 * Featured Products Columns
			 */
			$wp_customize->add_setting( 'sp_homepage_featured_products_columns', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_featured_products_columns', array(
				'label'           => __( 'Columns', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_featured_products_columns',
				'type'            => 'select',
				'priority'        => 210,
				'active_callback' => array( $this, 'featured_products_callback' ),
				'choices'         => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
			) ) );

			/**
			 * Featured Products limit
			 */
			$wp_customize->add_setting( 'sp_homepage_featured_products_limit', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_featured_products_limit', array(
				'label'           => __( 'Number products to display', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_featured_products_limit',
				'type'            => 'select',
				'priority'        => 220,
				'active_callback' => array( $this, 'featured_products_callback' ),
				'choices'         => array(
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
				),
			) ) );

			/**
			 * Featured products url
			 */
			$wp_customize->add_setting( 'sp_homepage_featured_products_more_url', array(
				'sanitize_callback' => 'esc_url_raw',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_featured_products_more_url', array(
				'label'           => __( '"View more" url', 'storefront-powerpack' ),
				'description'     => __( 'Add a url to append a "view more" button beneath featured products.', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_featured_products_more_url',
				'type'            => 'url',
				'priority'        => 230,
				'active_callback' => array( $this, 'featured_products_callback' ),
			) ) );

			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_home_featured_products_divider', array(
					'section'  => self::POWERPACK_HOMEPAGE_SECTION,
					'type'     => 'divider',
					'priority' => 240,
				) ) );
			}

			/**
			 * Top Rated Toggle
			 */
			$wp_customize->add_setting( 'sp_homepage_top_rated', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_top_rated', array(
				'label'    => __( 'Top rated products', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_HOMEPAGE_SECTION,
				'settings' => 'sp_homepage_top_rated',
				'type'     => 'checkbox',
				'priority' => 250,
			) ) );

			/**
			 * Top rated Products Title
			 */
			$wp_customize->add_setting( 'sp_homepage_top_rated_products_title', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_top_rated_products_title', array(
				'label'           => __( 'Title', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_top_rated_products_title',
				'type'            => 'text',
				'priority'        => 260,
				'active_callback' => array( $this, 'top_rated_products_callback' ),
			) ) );

			/**
			 * Top rated Products description
			 */
			$wp_customize->add_setting( 'sp_homepage_top_rated_products_description', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_top_rated_products_description', array(
				'label'           => __( 'Description', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_top_rated_products_description',
				'type'            => 'textarea',
				'priority'        => 270,
				'active_callback' => array( $this, 'top_rated_products_callback' ),
			) ) );

			/**
			 * Top rated Products Columns
			 */
			$wp_customize->add_setting( 'sp_homepage_top_rated_products_columns', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_top_rated_products_columns', array(
				'label'           => __( 'Columns', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_top_rated_products_columns',
				'type'            => 'select',
				'priority'        => 280,
				'active_callback' => array( $this, 'top_rated_products_callback' ),
				'choices'         => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6'	=> '6',
				),
			) ) );

			/**
			 * Top rated Products limit
			 */
			$wp_customize->add_setting( 'sp_homepage_top_rated_products_limit', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_top_rated_products_limit', array(
				'label'           => __( 'Number of products to display', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_top_rated_products_limit',
				'type'            => 'select',
				'priority'        => 290,
				'active_callback' => array( $this, 'top_rated_products_callback' ),
				'choices'         => array(
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
				),
			) ) );

			/**
			 * Top rated products url
			 */
			$wp_customize->add_setting( 'sp_homepage_top_rated_products_more_url', array(
				'sanitize_callback' => 'esc_url_raw',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_top_rated_products_more_url', array(
				'label'           => __( '"View more" url', 'storefront-powerpack' ),
				'description'     => __( 'Add a url to append a "view more" button beneath top rated products.', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_top_rated_products_more_url',
				'type'            => 'url',
				'priority'        => 300,
				'active_callback' => array( $this, 'top_rated_products_callback' ),
			) ) );

			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_home_top_rated_products_divider', array(
					'section'  => self::POWERPACK_HOMEPAGE_SECTION,
					'type'     => 'divider',
					'priority' => 310,
				) ) );
			}

			/**
			 * On Sale Toggle
			 */
			$wp_customize->add_setting( 'sp_homepage_on_sale', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_on_sale', array(
				'label'    => __( 'On sale products', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_HOMEPAGE_SECTION,
				'settings' => 'sp_homepage_on_sale',
				'type'     => 'checkbox',
				'priority' => 320,
			) ) );

			/**
			 * On sale Products Title
			 */
			$wp_customize->add_setting( 'sp_homepage_on_sale_products_title', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_on_sale_products_title', array(
				'label'           => __( 'Title', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_on_sale_products_title',
				'type'            => 'text',
				'priority'        => 330,
				'active_callback' => array( $this, 'on_sale_products_callback' ),
			) ) );

			/**
			* On sale Products description
			*/
			$wp_customize->add_setting( 'sp_homepage_on_sale_products_description', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_on_sale_products_description', array(
				'label'           => __( 'Description', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_on_sale_products_description',
				'type'            => 'textarea',
				'priority'        => 340,
				'active_callback' => array( $this, 'on_sale_products_callback' ),
			) ) );

			/**
			 * On sale Products Columns
			 */
			$wp_customize->add_setting( 'sp_homepage_on_sale_products_columns', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_on_sale_products_columns', array(
				'label'           => __( 'Columns', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_on_sale_products_columns',
				'type'            => 'select',
				'priority'        => 350,
				'active_callback' => array( $this, 'on_sale_products_callback' ),
				'choices'         => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6'	=> '6',
				),
			) ) );

			/**
			 * On sale Products limit
			 */
			$wp_customize->add_setting( 'sp_homepage_on_sale_products_limit', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_on_sale_products_limit', array(
				'label'           => __( 'Number of products to display', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_on_sale_products_limit',
				'type'            => 'select',
				'priority'        => 360,
				'active_callback' => array( $this, 'on_sale_products_callback' ),
				'choices'         => array(
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
				),
			) ) );

			/**
			 * On sale products url
			 */
			$wp_customize->add_setting( 'sp_homepage_on_sale_products_more_url', array(
				'sanitize_callback' => 'esc_url_raw',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_on_sale_products_more_url', array(
				'label'           => __( '"View more" url', 'storefront-powerpack' ),
				'description'     => __( 'Add a url to append a "view more" button beneath on sale products.', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_on_sale_products_more_url',
				'type'            => 'url',
				'priority'        => 370,
				'active_callback' => array( $this, 'on_sale_products_callback' ),
			) ) );


			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_home_on_sale_products_divider', array(
					'section'  => self::POWERPACK_HOMEPAGE_SECTION,
					'type'     => 'divider',
					'priority' => 380,
				) ) );
			}

			/**
			 * Best Selling Products Toggle
			 */
			$wp_customize->add_setting( 'sp_homepage_best_sellers', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_best_sellers', array(
				'label'    => __( 'Best Sellers', 'storefront-powerpack' ),
				'section'  => self::POWERPACK_HOMEPAGE_SECTION,
				'settings' => 'sp_homepage_best_sellers',
				'type'     => 'checkbox',
				'priority' => 390,
			) ) );

			/**
			 * Best Selling Products Title
			 */
			$wp_customize->add_setting( 'sp_homepage_best_sellers_products_title', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_best_sellers_products_title', array(
				'label'           => __( 'Title', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_best_sellers_products_title',
				'type'            => 'text',
				'priority'        => 400,
				'active_callback' => array( $this, 'best_selling_products_callback' ),
			) ) );

			/**
			 * Best Selling Products description
			 */
			$wp_customize->add_setting( 'sp_homepage_best_sellers_products_description', array(
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_best_sellers_products_description', array(
				'label'           => __( 'Description', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_best_sellers_products_description',
				'type'            => 'textarea',
				'priority'        => 410,
				'active_callback' => array( $this, 'best_selling_products_callback' ),
			) ) );

			/**
			 * Best Selling Products Columns
			 */
			$wp_customize->add_setting( 'sp_homepage_best_sellers_products_columns', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_best_sellers_products_columns', array(
				'label'           => __( 'Columns', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_best_sellers_products_columns',
				'type'            => 'select',
				'priority'        => 420,
				'active_callback' => array( $this, 'best_selling_products_callback' ),
				'choices'         => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
			) ) );

			/**
			 * Best Selling Products limit
			 */
			$wp_customize->add_setting( 'sp_homepage_best_sellers_products_limit', array(
				'sanitize_callback' => 'storefront_sanitize_choices',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_best_sellers_products_limit', array(
				'label'           => __( 'Number products to display', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_best_sellers_products_limit',
				'type'            => 'select',
				'priority'        => 430,
				'active_callback' => array( $this, 'best_selling_products_callback' ),
				'choices'         => array(
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
				),
			) ) );

			/**
			 * Best selling products url
			 */
			$wp_customize->add_setting( 'sp_homepage_best_sellers_products_more_url', array(
				'sanitize_callback' => 'esc_url_raw',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_homepage_best_sellers_products_more_url', array(
				'label'           => __( '"View more" url', 'storefront-powerpack' ),
				'description'     => __( 'Add a url to append a "view more" button beneath the best selling products.', 'storefront-powerpack' ),
				'section'         => self::POWERPACK_HOMEPAGE_SECTION,
				'settings'        => 'sp_homepage_best_sellers_products_more_url',
				'type'            => 'url',
				'priority'        => 440,
				'active_callback' => array( $this, 'best_selling_products_callback' ),
			) ) );

			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_homepage_best_sellers_products_divider', array(
					'section'  => self::POWERPACK_HOMEPAGE_SECTION,
					'type'     => 'divider',
					'priority' => 450,
				) ) );
			}
		}

		/**
		 * Query whether the Storefront homepage is not being previewed.
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function is_not_storefront_homepage() {
			return is_page_template( 'template-homepage.php' ) ? false : true;
		}

		/**
		 * Product category callback
		 *
		 * @param  array $control the Customizer control.
		 * @return bool
		 * @since  1.0.0
		 */
		public function product_category_callback( $control ) {
			$is_section_visible = $control->manager->get_setting( 'sp_homepage_categories' )->value();

			if ( true === (boolean) $is_section_visible ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Recent products callback
		 *
		 * @param  array $control the Customizer control.
		 * @return bool
		 * @since  1.0.0
		 */
		public function recent_products_callback( $control ) {
			$is_section_visible = $control->manager->get_setting( 'sp_homepage_recent' )->value();

			if ( true === (boolean) $is_section_visible ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Featured products callback
		 *
		 * @param  array $control the Customizer control.
		 * @return bool
		 * @since  1.0.0
		 */
		public function featured_products_callback( $control ) {
			$is_section_visible = $control->manager->get_setting( 'sp_homepage_featured' )->value();

			if ( true === (boolean) $is_section_visible ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Top rated products callback
		 *
		 * @param  array $control the Customizer control.
		 * @return bool
		 * @since  1.0.0
		 */
		public function top_rated_products_callback( $control ) {
			$is_section_visible = $control->manager->get_setting( 'sp_homepage_top_rated' )->value();

			if ( true === (boolean) $is_section_visible ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * On sale products callback
		 *
		 * @param  array $control the Customizer control.
		 * @return bool
		 * @since  1.0.0
		 */
		public function on_sale_products_callback( $control ) {
			$is_section_visible = $control->manager->get_setting( 'sp_homepage_on_sale' )->value();

			if ( true === (boolean) $is_section_visible ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Best Selling products callback
		 *
		 * @param  array $control the Customizer control.
		 * @return bool
		 * @since  1.0.0
		 */
		public function best_selling_products_callback( $control ) {
			$is_section_visible = $control->manager->get_setting( 'sp_homepage_best_sellers' )->value();

			if ( true === (boolean) $is_section_visible ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Add CSS for custom controls
		 *
		 * @since  1.0.0
		 */
		public function customizer_section_css() {
			$section_css = '
				<style>
				#accordion-section-' . self::POWERPACK_HOMEPAGE_SECTION .' .customize-control-checkbox label {
					margin-left: 0;
					font-weight: 700;
					font-size: 14px;
				}

				#accordion-section-' . self::POWERPACK_HOMEPAGE_SECTION .' .customize-control-checkbox input[type=checkbox] {
					width: 40px;
					height: 20px;
					position: relative;
					background-color: #fff;
					border-radius: 4em;
					border: 1px solid #ccc;
					box-sizing: content-box;
					float: right;
					margin-top: -1px;
					transition: all ease .2s;
				}

				#accordion-section-' . self::POWERPACK_HOMEPAGE_SECTION .' .customize-control-checkbox input[type=checkbox]:before {
					content: "";
					display: block;
					height: 20px;
					width: 20px;
					background-color: #fff;
					position: absolute;
					top: 0px;
					left: 0px;
					margin: 0;
					border-radius: 100%;
					box-shadow: 0 1px 3px rgba(0,0,0,.5);
					transition: margin ease .2s;
				}

				#accordion-section-' . self::POWERPACK_HOMEPAGE_SECTION .' .customize-control-checkbox input[type=checkbox]:checked {
					background-color: #0085ba;
					border-color: #0085ba;
				}

				#accordion-section-' . self::POWERPACK_HOMEPAGE_SECTION .' .customize-control-checkbox input[type=checkbox]:focus {
					box-shadow: none;
				}

				#accordion-section-' . self::POWERPACK_HOMEPAGE_SECTION .' .customize-control-checkbox input[type=checkbox]:checked:before {
					margin-left: calc(100% - 20px);
				}
				</style>
			';

			echo $section_css;
		}
	}

endif;

return new SP_Customizer_Homepage();