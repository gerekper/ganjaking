<?php
/**
 * Porto Settings Options
 */
if ( ! class_exists( 'Redux_Framework_porto_settings' ) ) {
	class Redux_Framework_porto_settings {
		public $args     = array();
		public $sections = array();
		public $theme;
		public $ReduxFramework;
		private $css_var_selectors = array();
		/**
		 * Porto Soft Mode
		 *
		 * @var bool
		 * @since 6.3.0
		 */
		public $legacy_mode;
		public function __construct() {
			// Create the sections and fields
			$this->legacy_mode = apply_filters( 'porto_legacy_mode', true );
			$this->setSections();
			if ( ! class_exists( 'ReduxFramework' ) ) {
				return;
			}
			$this->initSettings();
		}

		public function initSettings() {
			$this->theme = wp_get_theme();
			// Set the default arguments
			$this->setArguments();
			// Set a few help tabs so you can see how it's done
			$this->setHelpTabs();
			if ( ! isset( $this->args['opt_name'] ) ) {
				// No errors please
				return;
			}
			$this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
		}
		function compiler_action( $options, $css, $changed_values ) {
		}
		function dynamic_section( $sections ) {
			return $sections;
		}
		function change_arguments( $args ) {
			return $args;
		}
		function change_defaults( $defaults ) {
			return $defaults;
		}
		function remove_demo() {
		}

		private function add_customizer_field( array $sections, $options_style, $first_options = false, $second_options = false ) {
			if ( $options_style ) {
				return array_merge( $sections, array( 'customizer_only' => true ) );
			}
			if ( $second_options ) {
				$sections['fields'] = array_merge( $second_options['fields'], isset( $sections['fields'] ) ? $sections['fields'] : array() );
			}
			if ( $first_options ) {
				$sections['fields'] = array_merge( $first_options['fields'], isset( $sections['fields'] ) ? $sections['fields'] : array() );
			}
			return $sections;
		}

		/**
		 * Get Unlimited Post Type
		 *
		 * @since 6.4.0
		 * @access public
		 */
		public function get_post_ptu() {
			if ( class_exists( 'Post_Types_Unlimited' ) ) {
				$custom_types = get_posts(
					array(
						'numberposts'      => -1,
						'post_type'        => 'ptu',
						'post_status'      => 'publish',
						'suppress_filters' => false,
						'fields'           => 'ids',
					)
				);
				$post_types   = array();
				// If we have custom post types, lets try and register them
				if ( $custom_types ) {
					// Loop through all custom post types and register them
					foreach ( $custom_types as $type_id ) {

						// Get custom post type meta
						$meta = get_post_meta( $type_id, '', false );

						// Check custom post type name
						$name = array_key_exists( '_ptu_name', $meta ) ? $meta['_ptu_name'][0] : '';

						// Custom post type name is required
						if ( ! $name ) {
							continue;
						}
						$post_types[] = $name;
					}
				}
				return $post_types;
			}
			return array();
		}

		public function setSections() {
			$page_layouts              = porto_options_layouts();
			$sidebars                  = porto_options_sidebars();
			$both_sidebars             = porto_options_both_sidebars();
			$body_wrapper              = porto_options_body_wrapper();
			$banner_wrapper            = porto_options_banner_wrapper();
			$wrapper                   = porto_options_wrapper();
			$porto_banner_pos          = porto_ct_banner_pos();
			$porto_footer_view         = porto_ct_footer_view();
			$porto_banner_type         = porto_ct_banner_type();
			$porto_master_sliders      = porto_ct_master_sliders();
			$porto_rev_sliders         = porto_ct_rev_sliders();
			$porto_categories_orderby  = porto_ct_categories_orderby();
			$porto_categories_order    = porto_ct_categories_order();
			$porto_categories_sort_pos = porto_ct_categories_sort_pos();
			$porto_header_type         = porto_options_header_types();
			$porto_footer_type         = porto_options_footer_types();
			$porto_breadcrumbs_type    = porto_options_breadcrumbs_types();
			$porto_footer_columns      = porto_options_footer_columns();

			global $porto_settings_optimize;
			$archive_url = $single_url = $type_url = $header_url = $footer_url = $shop_url = $product_url = admin_url( 'admin.php?page=porto-speed-optimize-wizard&step=general' );

			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'archive', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$archive_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=archive' );
			}
			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'single', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$single_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=single' );
			}
			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'type', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$type_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=type' );
			}
			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'header', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$header_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=header' );
			}
			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'footer', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$footer_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=footer' );
			}
			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'shop', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$shop_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=shop' );
			}
			if ( ! ( isset( $porto_settings_optimize['disabled_pbs'] ) && is_array( $porto_settings_optimize['disabled_pbs'] ) && in_array( 'product', $porto_settings_optimize['disabled_pbs'] ) ) ) {
				$product_url = admin_url( 'edit.php?post_type=porto_builder&porto_builder_type=product' );
			}

			if ( current_user_can( 'manage_options' ) && is_admin() ) {
				$product_layouts = porto_get_post_type_items(
					'porto_builder',
					array(
						'meta_query' => array(
							array(
								'key'   => 'porto_builder_type',
								'value' => 'product',
							),
						),
					),
					true
				);
			} else {
				$product_layouts = array();
			}

			$options_style = get_theme_mod( 'theme_options_use_new_style', false );

			/* default values for old versions */
			if ( ! get_theme_mod( 'theme_options_saved', false ) ) {
				$porto_settings = get_option( 'porto_settings' );
			}
			$search_layout_default = 'simple';
			$minicart_type         = 'simple';
			if ( isset( $porto_settings ) && ! empty( $porto_settings ) ) {
				if ( ! isset( $porto_settings['search-layout'] ) ) {
					if ( in_array( $porto_settings['header-type'], array( '2', '3', '7', '8', '18', '19' ) ) ) {
						$search_layout_default = 'large';
					} else {
						$search_layout_default = 'advanced';
					}
				}

				if ( isset( $porto_settings['show-minicart'] ) && ! $porto_settings['show-minicart'] ) {
					$minicart_type = 'none';
				} elseif ( ! isset( $porto_settings['minicart-type'] ) ) {
					$header_type = isset( $porto_settings['header-type'] ) ? (int) $porto_settings['header-type'] : '';
					if ( ( $header_type >= 1 && $header_type <= 9 ) || 18 == $header_type || 19 == $header_type || ( isset( $porto_settings['header-type-select'] ) && 'header_builder' == $porto_settings['header-type-select'] ) ) {
						$minicart_type = 'minicart-arrow-alt';
					} else {
						$minicart_type = 'minicart-inline';
					}
				}
			}
			$mainmenu_popup_top_border = array(
				'border-color' => isset( $porto_settings ) && isset( $porto_settings['mainmenu-popup-border-color'] ) && $porto_settings['mainmenu-popup-border-color'] ? $porto_settings['mainmenu-popup-border-color'] : '#0088cc',
				'border-top'   => isset( $porto_settings ) && isset( $porto_settings['mainmenu-popup-border'] ) && ! $porto_settings['mainmenu-popup-border'] ? '' : '3px',
			);
			/* end */

			global $wp_version;
			if ( ! defined( 'ELEMENTOR_VERSION' ) && ! defined( 'WPB_VC_VERSION' ) && version_compare( $wp_version, '6.0', '>=' ) ) {
				$gutenberg_site_option = array(
					'id'      => 'enable-gfse',
					'type'    => 'switch',
					'title'   => __( 'Gutenberg Full Site Editing', 'porto' ),
					'desc'    => __( 'Make this option enable, Porto Template Builders won\'t be available.', 'porto' ),
					'default' => false,
					'on'      => __( 'Yes', 'porto' ),
					'off'     => __( 'No', 'porto' ),
				);
			}
			// General Settings
			$general_site_options = array();
			if ( isset( $gutenberg_site_option ) ) {
				$general_site_options[] = $gutenberg_site_option;
			}
			$general_site_options = array_merge( 
				$general_site_options, 
				array(
					array(
						'id'         => 'show-loading-overlay',
						'type'       => 'switch',
						'title'      => __( 'Loading Overlay', 'porto' ),
						'desc'       => __( 'Loading overlay is shown until whole page is loaded.', 'porto' ),
						'default'    => false,
						'on'         => __( 'Show', 'porto' ),
						'off'        => __( 'Hide', 'porto' ),
						'customizer' => false,
					),
					array(
						'id'       => 'button-style',
						'type'     => 'button_set',
						'title'    => __( 'Button Style', 'porto' ),
						'subtitle' => __( 'Select "Borders" to set buttons outline style.', 'porto' ),
						'options'  => array(
							''            => __( 'Default', 'porto' ),
							'btn-borders' => __( 'Borders', 'porto' ),
						),
						'default'  => '',
					),
					array(
						'id'        => 'border-radius',
						'type'      => 'switch',
						'title'     => __( 'Border Radius', 'porto' ),
						'subtitle'  => __( 'Constrols if you\'re using rounded style throughout the site.', 'porto' ),
						'default'   => false,
						'compiler'  => true,
						'transport' => 'refresh',
					),
					array(
						'id'        => 'thumb-padding',
						'type'      => 'switch',
						'title'     => __( 'Thumbnail Padding', 'porto' ),
						'subtitle'  => __( 'This will display border and spacing for thumbnail images such as product images.', 'porto' ),
						'default'   => false,
						'compiler'  => true,
						'transport' => 'refresh',
					),
					array(
						'id'      => 'show-content-type-skin',
						'type'    => 'switch',
						'title'   => __( 'Show Content Type Skin Options', 'porto' ),
						'desc'    => __( 'Show skin options when edit post, page, product, portfolio, member, event.', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'show-category-skin',
						'type'    => 'switch',
						'title'   => __( 'Show Category Skin Options', 'porto' ),
						'desc'    => __( 'Show skin options when edit the category of post, product, portfolio, member, event', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'show-skeleton-screen',
						'type'    => 'button_set',
						'title'   => __( 'Show Skeleton Screens', 'porto' ),
						'desc'    => __( 'This will show skeleton screens during page load for the selected pages. Note: please disable options if you have any compatibility issues with the third party plugins.', 'porto' ),
						'multi'   => true,
						'options' => array(
							'shop'      => __( 'Shop Pages', 'porto' ),
							'product'   => __( 'Product Page', 'porto' ),
							'quickview' => __( 'Product Quickview', 'porto' ),
							'blog'      => __( 'Blog Pages', 'porto' ),
						),
						'default' => array(),
					),
				)
			);

			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'       => 'icon-general',
					'icon_class' => 'porto-icon',
					'title'      => __( 'General', 'porto' ),
					'fields'     => $general_site_options,
				),
				$options_style
			);

			// Layout
			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'            => 'Simple-Line-Icons-layers',
					'icon_class'      => '',
					'title'           => __( 'Layout', 'porto' ),
					'fields'          => array(
						array(
							'id'        => 'container-width',
							'type'      => 'text',
							'title'     => __( 'Container Max Width (px)', 'porto' ),
							'subtitle'  => 'Controls the overall site width. 960 - 1920',
							'default'   => '1140',
							'compiler'  => true,
							'transport' => 'refresh',
							'selector'  => array(
								'node' => '.container, .wp-block, .col-half-section, .elementor-section',
								'unit' => 'px',
							),
						),
						array(
							'id'        => 'grid-gutter-width',
							'type'      => 'button_set',
							'title'     => __( 'Grid Gutter Width', 'porto' ),
							'subtitle'  => __( 'Controls the space between columns in a row.', 'porto' ),
							'options'   => array(
								'16' => '16px',
								'20' => '20px',
								'24' => '24px',
								'30' => '30px',
							),
							'default'   => '30',
							'compiler'  => true,
							'transport' => 'refresh',
							'selector'  => array(
								'node' => ':root',
								'unit' => 'px',
							),
						),
						array(
							'id'       => 'wrapper',
							'type'     => 'image_select',
							'title'    => __( 'Body Wrapper', 'porto' ),
							'subtitle' => __( 'Controls the site layout.', 'porto' ),
							'options'  => $body_wrapper,
							'default'  => 'full',
						),
						array(
							'id'       => 'layout',
							'type'     => 'image_select',
							'title'    => __( 'Page Layout', 'porto' ),
							'subtitle' => __( 'Controls the global page layout with sidebars.', 'porto' ),
							'options'  => $page_layouts,
							'default'  => 'right-sidebar',
						),
						array(
							'id'       => 'sidebar',
							'type'     => 'select',
							'title'    => __( 'Select Sidebar', 'porto' ),
							'subtitle' => __( 'Select the global sidebar 1.', 'porto' ),
							'required' => array( 'layout', 'equals', $sidebars ),
							'data'     => 'sidebars',
							'default'  => 'blog-sidebar',
						),
						array(
							'id'       => 'sidebar2',
							'type'     => 'select',
							'title'    => __( 'Select Sidebar 2', 'porto' ),
							'subtitle' => __( 'Select the global sidebar 2.', 'porto' ),
							'required' => array( 'layout', 'equals', $both_sidebars ),
							'data'     => 'sidebars',
							'default'  => 'secondary-sidebar',
						),
						array(
							'id'       => 'header-wrapper',
							'type'     => 'image_select',
							'title'    => __( 'Header Wrapper', 'porto' ),
							'subtitle' => __( 'Controls the header layout.', 'porto' ),
							'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
							'options'  => $wrapper,
							'default'  => 'full',
						),
						array(
							'id'       => 'banner-wrapper',
							'type'     => 'image_select',
							'title'    => __( 'Banner Wrapper', 'porto' ),
							'subtitle' => __( 'Controls the banner layout.', 'porto' ),
							'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
							'options'  => $banner_wrapper,
							'default'  => 'wide',
						),
						array(
							'id'       => 'breadcrumbs-wrapper',
							'type'     => 'image_select',
							'title'    => __( 'Breadcrumbs Wrapper', 'porto' ),
							'subtitle' => __( 'Controls the page header layout.', 'porto' ),
							'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
							'options'  => $wrapper,
							'default'  => 'full',
						),
						array(
							'id'       => 'main-wrapper',
							'type'     => 'image_select',
							'title'    => __( 'Page Content Wrapper', 'porto' ),
							'subtitle' => __( 'Controls the page content layout.', 'porto' ),
							'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
							'options'  => $banner_wrapper,
							'default'  => 'wide',
						),
						array(
							'id'       => 'footer-wrapper',
							'type'     => 'image_select',
							'title'    => __( 'Footer Wrapper', 'porto' ),
							'subtitle' => __( 'Controls the footer layout.', 'porto' ),
							'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
							'options'  => $wrapper,
							'default'  => 'full',
						),
					),
				),
				$options_style
			);

			$this->sections[] = array(
				'id'         => 'html-blocks',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'HTML Blocks', 'porto' ),
				'desc'       => __( 'Please check "Theme Layout" section to see blocks\' locations.', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'       => 'html-top',
						'type'     => 'ace_editor',
						'mode'     => 'html',
						'title'    => __( 'Top', 'porto' ),
						'subtitle' => __( 'Executes at the top of the page', 'porto' ),
						'desc'     => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
					),
					array(
						'id'    => 'html-banner',
						'type'  => 'ace_editor',
						'mode'  => 'html',
						'title' => __( 'Banner', 'porto' ),
						'desc'  => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
					),
					array(
						'id'    => 'html-content-top',
						'type'  => 'ace_editor',
						'mode'  => 'html',
						'title' => __( 'Content Top', 'porto' ),
						'desc'  => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
					),
					array(
						'id'    => 'html-content-inner-top',
						'type'  => 'ace_editor',
						'mode'  => 'html',
						'title' => __( 'Content Inner Top', 'porto' ),
						'desc'  => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
					),
					array(
						'id'    => 'html-content-inner-bottom',
						'type'  => 'ace_editor',
						'mode'  => 'html',
						'title' => __( 'Content Inner Bottom', 'porto' ),
						'desc'  => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
					),
					array(
						'id'    => 'html-content-bottom',
						'type'  => 'ace_editor',
						'mode'  => 'html',
						'title' => __( 'Content Bottom', 'porto' ),
						'desc'  => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
					),
					array(
						'id'       => 'html-bottom',
						'type'     => 'ace_editor',
						'mode'     => 'html',
						'title'    => __( 'Bottom', 'porto' ),
						'subtitle' => __( 'Executes at the bottom of the page', 'porto' ),
						'desc'     => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
					),
				),
			);
			
			// Skin
			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'       => 'icon-visual',
					'icon_class' => 'porto-icon',
					'title'      => __( 'Skin', 'porto' ),
				),
				$options_style
			);
			$this->sections[] = array(
				'title'      => __( 'Theme Colors', 'porto' ),
				'id'         => 'skin-theme-colors',
				'subsection' => true,
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'       => 'css-type',
						'type'     => 'button_set',
						'title'    => __( 'Color Scheme', 'porto' ),
						'subtitle' => __( 'Controls the theme skin to be light or dark style.', 'porto' ),
						'options'  => array(
							''     => __( 'Light', 'porto' ),
							'dark' => __( 'Dark', 'porto' ),
						),
						'default'  => '',
						'compiler' => true,
						'selector' => array(
							'node' => ':root',
						),
					),
					array(
						'id'       => 'color-dark',
						'type'     => 'color',
						'required' => array( 'css-type', 'equals', 'dark' ),
						'title'    => __( 'Basic Background Color', 'porto' ),
						'subtitle' => __( 'Controls the skin color for dark theme.', 'porto' ),
						'default'  => '#1d2127',
						'validate' => 'color',
						'compiler' => true,
						'selector' => array(
							'node' => ':root',
						),
					),
					array(
						'id'       => 'skin-color',
						'type'     => 'color',
						'title'    => __( 'Primary Color', 'porto' ),
						'subtitle' => __( 'Controls the main color throughout the theme.', 'porto' ),
						'default'  => '#0088cc',
						'validate' => 'color',
						'compiler' => true,
						'selector' => array(
							'node' => ':root',
						),
					),
					array(
						'id'       => 'skin-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Primary Inverse Color', 'porto' ),
						'subtitle' => __( 'Controls the inverse color of main color throughout the theme.', 'porto' ),
						'desc'     => __( 'Hover, focus and active color of primary color.', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
						'compiler' => true,
						'selector' => array(
							'node' => ':root',
						),
					),
					array(
						'id'       => 'secondary-color',
						'type'     => 'color',
						'title'    => __( 'Secondary Color', 'porto' ),
						'subtitle' => __( 'Controls the secondary color throughout the theme.', 'porto' ),
						'default'  => '#e36159',
						'validate' => 'color',
						'compiler' => true,
						'selector' => array(
							'node' => ':root',
						),
					),
					array(
						'id'       => 'secondary-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Secondary Inverse Color', 'porto' ),
						'subtitle' => __( 'Controls the inverse color of secondary color throughout the theme.', 'porto' ),
						'desc'     => __( 'Hover, focus and active color of secondary color.', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
						'compiler' => true,
						'selector' => array(
							'node' => ':root',
						),
					),
					array(
						'id'       => 'tertiary-color',
						'type'     => 'color',
						'title'    => __( 'Tertiary Color', 'porto' ),
						'subtitle' => __( 'Controls the tertiary color throughout the theme.', 'porto' ),
						'default'  => '#2baab1',
						'validate' => 'color',
						'compiler' => true,
						'selector' => array(
							'node' => ':root',
						),
					),
					array(
						'id'       => 'tertiary-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Tertiary Inverse Color', 'porto' ),
						'subtitle' => __( 'Controls the inverse color of tertiary color throughout the theme.', 'porto' ),
						'desc'     => __( 'Hover, focus and active color of tertiary color.', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
						'compiler' => true,
						'selector' => array(
							'node' => ':root',
						),
					),
					array(
						'id'       => 'quaternary-color',
						'type'     => 'color',
						'title'    => __( 'Quaternary Color', 'porto' ),
						'subtitle' => __( 'Controls the quaternary color throughout the theme.', 'porto' ),
						'default'  => '#383f48',
						'validate' => 'color',
						'compiler' => true,
						'selector' => array(
							'node' => ':root',
						),
					),
					array(
						'id'       => 'quaternary-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Quaternary Inverse Color', 'porto' ),
						'subtitle' => __( 'Controls the inverse color of quaternary color throughout the theme.', 'porto' ),
						'desc'     => __( 'Hover, focus and active color of quaternary color.', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
						'compiler' => true,
						'selector' => array(
							'node' => ':root',
						),
					),
					array(
						'id'       => 'dark-color',
						'type'     => 'color',
						'title'    => __( 'Dark Color', 'porto' ),
						'subtitle' => __( 'Controls the dark color throughout the theme.', 'porto' ),
						'default'  => '#212529',
						'validate' => 'color',
						'compiler' => true,
						'selector' => array(
							'node' => ':root',
						),
					),
					array(
						'id'       => 'dark-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Dark Inverse Color', 'porto' ),
						'subtitle' => __( 'Controls the dark color of quaternary color throughout the theme.', 'porto' ),
						'desc'     => __( 'Hover, focus and active color of dark color.', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
						'compiler' => true,
						'selector' => array(
							'node' => ':root',
						),
					),
					array(
						'id'       => 'light-color',
						'type'     => 'color',
						'title'    => __( 'Light Color', 'porto' ),
						'subtitle' => __( 'Controls the light color throughout the theme.', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
						'compiler' => true,
						'selector' => array(
							'node' => ':root',
						),
					),
					array(
						'id'       => 'light-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Light Inverse Color', 'porto' ),
						'subtitle' => __( 'Controls the light color of quaternary color throughout the theme.', 'porto' ),
						'desc'     => __( 'Hover, focus and active color of light color.', 'porto' ),
						'default'  => '#212529',
						'validate' => 'color',
						'compiler' => true,
						'selector' => array(
							'node' => ':root',
						),
					),
					array(
						'id'       => 'placeholder-color',
						'type'     => 'color',
						'title'    => __( 'Placeholder Image Background Color', 'porto' ),
						'subtitle' => __( 'Controls the skeleton color throughout the theme.', 'porto' ),
						'default'  => '#f4f4f4',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'social-color',
						'type'     => 'button_set',
						'title'    => __( 'Social Links Color', 'porto' ),
						'desc'     => __( 'If you select "primary" option, social links will be determined by Primary and Primary Inverse color.', 'porto' ),
						'options'  => array(
							''        => __( 'Default', 'porto' ),
							'primary' => __( 'Primary Color', 'porto' ),
						),
						'default'  => '',
						'compiler' => true,
					),
				),
			);

			// Skin Typography
			if ( $this->legacy_mode ) {
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Typography', 'porto' ),
					'fields'     => array(
						array(
							'id'       => 'select-google-charset',
							'type'     => 'switch',
							'title'    => __( 'Select Google Font Character Sets', 'porto' ),
							'subtitle' => __( 'Select "YES" to set the subsets of Google fonts.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'google-charsets',
							'type'     => 'button_set',
							'title'    => __( 'Google Font Character Sets', 'porto' ),
							'multi'    => true,
							'required' => array( 'select-google-charset', 'equals', true ),
							'options'  => array(
								'cyrillic'     => 'Cyrrilic',
								'cyrillic-ext' => 'Cyrrilic Extended',
								'greek'        => 'Greek',
								'greek-ext'    => 'Greek Extended',
								'khmer'        => 'Khmer',
								'latin'        => 'Latin',
								'latin-ext'    => 'Latin Extended',
								'vietnamese'   => 'Vietnamese',
							),
							'default'  => array( 'latin', 'greek-ext', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext', 'vietnamese', 'khmer' ),
						),
						array(
							'id'      => 'google-webfont-loader',
							'type'    => 'switch',
							'title'   => __( 'Enable Web Font Loader for Google Fonts', 'porto' ),
							/* translators: $1: opening A tag which has link to the Google PageSpeed Insights $2: closing A tag */
							'desc'    => sprintf( esc_html__( 'By using this option, you can increase page speed about 4 percent in %1$sGoogle PageSpeed Insights%2$s for both of mobile and desktop.', 'porto' ), '<a href="https://developers.google.com/speed/pagespeed/insights/" target="_blank" rel="noopener noreferrer">', '</a>' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'             => 'body-font',
							'type'           => 'typography',
							'title'          => __( 'Body Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for all body text.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
							'compiler'       => true,
							'default'        => array(
								'color'          => '#777777',
								'google'         => true,
								'font-weight'    => '400',
								'font-family'    => 'Open Sans',
								'font-size'      => '14px',
								'line-height'    => '24px',
								'letter-spacing' => '0',
							),
							'transport'      => 'postMessage',
							'selector'       => array(
								'node' => ':root',
							),
						),
						array(
							'id'             => 'body-mobile-font',
							'type'           => 'typography',
							'title'          => __( 'Body Mobile Font', 'porto' ),
							'subtitle'       => __( 'Controls the mobile typography for all body text.', 'porto' ),
							'google'         => false,
							'subsets'        => false,
							'font-family'    => false,
							'font-weight'    => false,
							'text-align'     => false,
							'color'          => false,
							'font-style'     => false,
							'letter-spacing' => true,
							'desc'           => __( 'Will be change on mobile device(max width < 576px).', 'porto' ),
							'default'        => array(
								'font-size'      => '13px',
								'line-height'    => '22px',
								'letter-spacing' => '0',
							),
						),
						array(
							'id'          => 'alt-font',
							'type'        => 'typography',
							'title'       => __( 'Alternative Font', 'porto' ),
							'subtitle'    => __( 'Used in some elements and footer ribbon text.', 'porto' ),
							'google'      => true,
							'subsets'     => false,
							'font-style'  => false,
							'font-size'   => false,
							'text-align'  => false,
							'color'       => false,
							'line-height' => false,
							'desc'        => __( 'You can use css class name "alternative-font" when edit html element.', 'porto' ),
							'default'     => array(
								'google'      => true,
								'font-weight' => '400',
								'font-family' => 'Shadows Into Light',
							),
							'transport'   => 'postMessage',
							'selector'    => array(
								'node' => ':root',
							),
						),
						array(
							'id'             => 'h1-font',
							'type'           => 'typography',
							'title'          => __( 'H1 Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for all H1 headings.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
							'default'        => array(
								'color'          => '',
								'google'         => true,
								'font-weight'    => '700',
								'font-family'    => 'Open Sans',
								'font-size'      => '36px',
								'line-height'    => '44px',
								'letter-spacing' => '',
							),
							'transport'      => 'postMessage',
							'selector'       => array(
								'node' => 'h1',
							),
						),
						array(
							'id'             => 'h2-font',
							'type'           => 'typography',
							'title'          => __( 'H2 Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for all H2 headings.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
							'default'        => array(
								'color'          => '',
								'google'         => true,
								'font-weight'    => '700',
								'font-family'    => 'Open Sans',
								'font-size'      => '30px',
								'line-height'    => '40px',
								'letter-spacing' => '',
							),
							'transport'      => 'postMessage',
							'selector'       => array(
								'node' => 'h2',
							),
						),
						array(
							'id'             => 'h3-font',
							'type'           => 'typography',
							'title'          => __( 'H3 Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for all H3 headings.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
							'default'        => array(
								'color'          => '',
								'google'         => true,
								'font-weight'    => '700',
								'font-family'    => 'Open Sans',
								'font-size'      => '25px',
								'line-height'    => '32px',
								'letter-spacing' => '',
							),
							'transport'      => 'postMessage',
							'selector'       => array(
								'node' => 'h3, .daily-deal-title',
							),
						),
						array(
							'id'             => 'h4-font',
							'type'           => 'typography',
							'title'          => __( 'H4 Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for all H4 headings.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
							'default'        => array(
								'color'          => '',
								'google'         => true,
								'font-weight'    => '700',
								'font-family'    => 'Open Sans',
								'font-size'      => '20px',
								'line-height'    => '27px',
								'letter-spacing' => '',
							),
							'transport'      => 'postMessage',
							'selector'       => array(
								'node' => 'h4',
							),
						),
						array(
							'id'             => 'h5-font',
							'type'           => 'typography',
							'title'          => __( 'H5 Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for all H5 headings.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
							'default'        => array(
								'color'          => '',
								'google'         => true,
								'font-weight'    => '700',
								'font-family'    => 'Open Sans',
								'font-size'      => '14px',
								'line-height'    => '18px',
								'letter-spacing' => '',
							),
							'transport'      => 'postMessage',
							'selector'       => array(
								'node' => 'h5',
							),
						),
						array(
							'id'             => 'h6-font',
							'type'           => 'typography',
							'title'          => __( 'H6 Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for all H6 headings.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
							'default'        => array(
								'color'          => '',
								'google'         => true,
								'font-weight'    => '700',
								'font-family'    => 'Open Sans',
								'font-size'      => '14px',
								'line-height'    => '18px',
								'letter-spacing' => '',
							),
							'transport'      => 'postMessage',
							'selector'       => array(
								'node' => 'h6',
							),
						),
						array(
							'id'             => 'paragraph-font',
							'type'           => 'typography',
							'title'          => __( 'Paragraph Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for all p tags.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
						),
						array(
							'id'             => 'footer-font',
							'type'           => 'typography',
							'title'          => __( 'Footer Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for all footer text.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
						),
						array(
							'id'             => 'footer-heading-font',
							'type'           => 'typography',
							'title'          => __( 'Footer Heading Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for all footer heading tags (h1 ~ h6).', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'font-size'      => false,
							'line-height'    => false,
							'letter-spacing' => true,
						),
						array(
							'id'             => 'shortcode-testimonial-font',
							'type'           => 'typography',
							'title'          => __( 'Testimonial Shortcode Font', 'porto' ),
							'subtitle'       => __( 'Controls the testimonial text for the testimonial element.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'color'          => false,
							'font-size'      => false,
							'font-style'     => false,
							'font-weight'    => false,
							'text-align'     => false,
							'line-height'    => false,
							'letter-spacing' => true,
							'default'        => array(
								'google'      => true,
								'font-family' => 'Playfair Display',
							),
							'transport'      => 'postMessage',
						),
						array(
							'title'          => esc_html__( 'Custom Font 1', 'porto' ),
							'subtitle'       => __( 'Controls a custom font to use throughout the site.', 'porto' ),
							'desc'           => esc_html__( 'You can use this font in some shortcodes such as heading and icon box. Please use "custom-font1" css class to use this font.', 'porto' ),
							'id'             => 'custom1-font',
							'type'           => 'typography',
							'google'         => true,
							'font-style'     => false,
							'font-weight'    => true,
							'text-align'     => false,
							'font-size'      => false,
							'color'          => false,
							'line-height'    => false,
							'letter-spacing' => false,
							'subsets'        => false,
						),
						array(
							'title'          => esc_html__( 'Custom Font 2', 'porto' ),
							'subtitle'       => __( 'Controls a custom font to use throughout the site.', 'porto' ),
							'desc'           => esc_html__( 'You can use this font in some shortcodes such as heading and icon box. Please use "custom-font2" css class to use this font.', 'porto' ),
							'id'             => 'custom2-font',
							'type'           => 'typography',
							'google'         => true,
							'font-style'     => false,
							'font-weight'    => true,
							'text-align'     => false,
							'font-size'      => false,
							'color'          => false,
							'line-height'    => false,
							'letter-spacing' => false,
							'subsets'        => false,
						),
						array(
							'title'          => esc_html__( 'Custom Font 3', 'porto' ),
							'subtitle'       => __( 'Controls a custom font to use throughout the site.', 'porto' ),
							'desc'           => esc_html__( 'You can use this font in some shortcodes such as heading and icon box. Please use "custom-font3" css class to use this font.', 'porto' ),
							'id'             => 'custom3-font',
							'type'           => 'typography',
							'google'         => true,
							'font-style'     => false,
							'font-weight'    => true,
							'text-align'     => false,
							'font-size'      => false,
							'color'          => false,
							'line-height'    => false,
							'letter-spacing' => false,
							'subsets'        => false,
						),
					),
				);
			} else {
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Typography', 'porto' ),
					'fields'     => array(
						array(
							'id'      => 'select-google-charset',
							'type'    => 'switch',
							'title'   => __( 'Select Google Font Character Sets', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'google-charsets',
							'type'     => 'button_set',
							'title'    => __( 'Google Font Character Sets', 'porto' ),
							'multi'    => true,
							'required' => array( 'select-google-charset', 'equals', true ),
							'options'  => array(
								'cyrillic'     => 'Cyrrilic',
								'cyrillic-ext' => 'Cyrrilic Extended',
								'greek'        => 'Greek',
								'greek-ext'    => 'Greek Extended',
								'khmer'        => 'Khmer',
								'latin'        => 'Latin',
								'latin-ext'    => 'Latin Extended',
								'vietnamese'   => 'Vietnamese',
							),
							'default'  => array( 'latin', 'greek-ext', 'cyrillic', 'latin-ext', 'greek', 'cyrillic-ext', 'vietnamese', 'khmer' ),
						),
						array(
							'id'      => 'google-webfont-loader',
							'type'    => 'switch',
							'title'   => __( 'Enable Web Font Loader for Google Fonts', 'porto' ),
							/* translators: $1: opening A tag which has link to the Google PageSpeed Insights $2: closing A tag */
							'desc'    => sprintf( esc_html__( 'By using this option, you can increase page speed about 4 percent in %1$sGoogle PageSpeed Insights%2$s for both of mobile and desktop.', 'porto' ), '<a href="https://developers.google.com/speed/pagespeed/insights/" target="_blank" rel="noopener noreferrer">', '</a>' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'             => 'body-font',
							'type'           => 'typography',
							'title'          => __( 'Body Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for all body text.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
							'compiler'       => true,
							'default'        => array(
								'color'          => '#777777',
								'google'         => true,
								'font-weight'    => '400',
								'font-family'    => 'Open Sans',
								'font-size'      => '14px',
								'line-height'    => '24px',
								'letter-spacing' => '0',
							),
							'selector'       => array(
								'node' => ':root',
							),
						),
						array(
							'id'             => 'body-mobile-font',
							'type'           => 'typography',
							'title'          => __( 'Body Mobile Font', 'porto' ),
							'google'         => false,
							'subsets'        => false,
							'font-family'    => false,
							'font-weight'    => false,
							'text-align'     => false,
							'color'          => false,
							'font-style'     => false,
							'letter-spacing' => true,
							'desc'           => __( 'Will be change on mobile device(max width < 576px).', 'porto' ),
							'default'        => array(
								'font-size'      => '13px',
								'line-height'    => '20px',
								'letter-spacing' => '0',
							),
						),
						array(
							'id'          => 'alt-font',
							'type'        => 'typography',
							'title'       => __( 'Alternative Font', 'porto' ),
							'subtitle'    => __( 'Used in some elements and footer ribbon text.', 'porto' ),
							'google'      => true,
							'subsets'     => false,
							'font-style'  => false,
							'font-size'   => false,
							'text-align'  => false,
							'color'       => false,
							'line-height' => false,
							'desc'        => __( 'You can use css class name "alternative-font" when edit html element.', 'porto' ),
							'default'     => array(
								'google'      => true,
								'font-weight' => '400',
								'font-family' => 'Shadows Into Light',
							),
							'selector'    => array(
								'node' => ':root',
							),
						),
						array(
							'id'             => 'h1-font',
							'type'           => 'typography',
							'title'          => __( 'H1 Font', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
							'default'        => array(
								'color'          => '',
								'google'         => true,
								'font-weight'    => '700',
								'font-family'    => 'Open Sans',
								'font-size'      => '36px',
								'line-height'    => '44px',
								'letter-spacing' => '',
							),
							'selector'       => array(
								'node' => 'h1',
							),
						),
						array(
							'id'             => 'h2-font',
							'type'           => 'typography',
							'title'          => __( 'H2 Font', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
							'default'        => array(
								'color'          => '',
								'google'         => true,
								'font-weight'    => '700',
								'font-family'    => 'Open Sans',
								'font-size'      => '30px',
								'line-height'    => '40px',
								'letter-spacing' => '',
							),
							'selector'       => array(
								'node' => 'h2',
							),
						),
						array(
							'id'             => 'h3-font',
							'type'           => 'typography',
							'title'          => __( 'H3 Font', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
							'default'        => array(
								'color'          => '',
								'google'         => true,
								'font-weight'    => '700',
								'font-family'    => 'Open Sans',
								'font-size'      => '25px',
								'line-height'    => '32px',
								'letter-spacing' => '',
							),
							'selector'       => array(
								'node' => 'h3',
							),
						),
						array(
							'id'             => 'h4-font',
							'type'           => 'typography',
							'title'          => __( 'H4 Font', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
							'default'        => array(
								'color'          => '',
								'google'         => true,
								'font-weight'    => '700',
								'font-family'    => 'Open Sans',
								'font-size'      => '20px',
								'line-height'    => '27px',
								'letter-spacing' => '',
							),
							'selector'       => array(
								'node' => 'h4',
							),
						),
						array(
							'id'             => 'h5-font',
							'type'           => 'typography',
							'title'          => __( 'H5 Font', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
							'default'        => array(
								'color'          => '',
								'google'         => true,
								'font-weight'    => '700',
								'font-family'    => 'Open Sans',
								'font-size'      => '14px',
								'line-height'    => '18px',
								'letter-spacing' => '',
							),
							'selector'       => array(
								'node' => 'h5',
							),
						),
						array(
							'id'             => 'h6-font',
							'type'           => 'typography',
							'title'          => __( 'H6 Font', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
							'default'        => array(
								'color'          => '',
								'google'         => true,
								'font-weight'    => '700',
								'font-family'    => 'Open Sans',
								'font-size'      => '14px',
								'line-height'    => '18px',
								'letter-spacing' => '',
							),
							'selector'       => array(
								'node' => 'h6',
							),
						),
						array(
							'id'             => 'paragraph-font',
							'type'           => 'typography',
							'title'          => __( 'Paragraph Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for all p tags.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'letter-spacing' => true,
						),
						array(
							'id'             => 'shortcode-testimonial-font',
							'type'           => 'typography',
							'title'          => __( 'Testimonial Shortcode Font', 'porto' ),
							'subtitle'       => __( 'Controls the testimonial text for the testimonial element.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'color'          => false,
							'font-size'      => false,
							'font-style'     => false,
							'font-weight'    => false,
							'text-align'     => false,
							'line-height'    => false,
							'letter-spacing' => true,
							'default'        => array(
								'google'      => true,
								'font-family' => 'Playfair Display',
							),
						),
						array(
							'title'          => esc_html__( 'Custom Font 1', 'porto' ),
							'desc'           => esc_html__( 'You can use this font in some shortcodes such as heading and icon box. Please use "custom-font1" css class to use this font.', 'porto' ),
							'id'             => 'custom1-font',
							'type'           => 'typography',
							'google'         => true,
							'font-style'     => false,
							'font-weight'    => true,
							'text-align'     => false,
							'font-size'      => false,
							'color'          => false,
							'line-height'    => false,
							'letter-spacing' => false,
							'subsets'        => false,
						),
						array(
							'title'          => esc_html__( 'Custom Font 2', 'porto' ),
							'desc'           => esc_html__( 'You can use this font in some shortcodes such as heading and icon box. Please use "custom-font2" css class to use this font.', 'porto' ),
							'id'             => 'custom2-font',
							'type'           => 'typography',
							'google'         => true,
							'font-style'     => false,
							'font-weight'    => true,
							'text-align'     => false,
							'font-size'      => false,
							'color'          => false,
							'line-height'    => false,
							'letter-spacing' => false,
							'subsets'        => false,
						),
						array(
							'title'          => esc_html__( 'Custom Font 3', 'porto' ),
							'desc'           => esc_html__( 'You can use this font in some shortcodes such as heading and icon box. Please use "custom-font3" css class to use this font.', 'porto' ),
							'id'             => 'custom3-font',
							'type'           => 'typography',
							'google'         => true,
							'font-style'     => false,
							'font-weight'    => true,
							'text-align'     => false,
							'font-size'      => false,
							'color'          => false,
							'line-height'    => false,
							'letter-spacing' => false,
							'subsets'        => false,
						),
					),
				);
			}

			if ( $this->legacy_mode ) {
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Backgrounds', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'     => 'desc_info_bodybg',
							'type'   => 'info',
							'title'  => __( 'Body Background', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'body-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the background settings for body.', 'porto' ),
						),
						array(
							'id'       => 'body-bg-gradient',
							'type'     => 'switch',
							'title'    => __( 'Enable Background Gradient', 'porto' ),
							'subtitle' => __( 'Controls the background gradient settings of body.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'body-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Background Gradient Color', 'porto' ),
							'subtitle' => __( 'Controls the top and bottom background color of body.', 'porto' ),
							'required' => array( 'body-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '',
								'to'   => '',
							),
						),
						array(
							'id'     => 'desc_info_content_bg',
							'type'   => 'info',
							'title'  => __( 'Page Content Background', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'content-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the background settings for page content.', 'porto' ),
						),
						array(
							'id'       => 'content-bg-gradient',
							'type'     => 'switch',
							'title'    => __( 'Enable Background Gradient', 'porto' ),
							'subtitle' => __( 'Controls the background gradient settings of page content.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'content-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Background Gradient Color', 'porto' ),
							'subtitle' => __( 'Controls the top and bottom background color of page content.', 'porto' ),
							'required' => array( 'content-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '',
								'to'   => '',
							),
						),
						array(
							'id'     => 'desc_info_content_bottom',
							'type'   => 'info',
							'title'  => __( 'Content Bottom Widgets Area : For this options, you should build one more Content Bottom Widget.', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'content-bottom-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the background settings for content bottom widget.', 'porto' ),
						),
						array(
							'id'       => 'content-bottom-bg-gradient',
							'type'     => 'switch',
							'title'    => __( 'Enable Background Gradient', 'porto' ),
							'subtitle' => __( 'Controls the background gradient settings of content bottom content.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'content-bottom-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Background Gradient Color', 'porto' ),
							'subtitle' => __( 'Controls the top and bottom background color of content bottom widget.', 'porto' ),
							'required' => array( 'content-bottom-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '',
								'to'   => '',
							),
						),
						array(
							'id'       => 'content-bottom-padding',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Padding', 'porto' ),
							'subtitle' => __( 'Controls the padding of content bottom widget.', 'porto' ),
							'default'  => array(
								'padding-top'    => 0,
								'padding-bottom' => 20,
							),
						),
					),
				);
			}

			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Form Style', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'       => 'form-ih',
						'type'     => 'text',
						'title'    => __( 'The Height of input and select box', 'porto' ),
						'subtitle' => __( 'Enter value including any valid CSS unit, ex: 30px.', 'porto' ),
						'default'  => '',
					),
					array(
						'id'       => 'form-fs',
						'type'     => 'text',
						'title'    => __( 'Form Font Size', 'porto' ),
						'subtitle' => __( 'Inputs the font size of form and form fields.', 'porto' ),
						'default'  => '',
					),
					array(
						'id'       => 'form-color',
						'type'     => 'color',
						'title'    => __( 'Form Text Color', 'porto' ),
						'subtitle' => __( 'Controls the color of the form and form fields.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'form-field-bgc',
						'type'     => 'color',
						'title'    => __( 'Form Field Background Color', 'porto' ),
						'subtitle' => __( 'Controls the background color of form fields such as input and select boxes.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'form-field-bw',
						'type'     => 'spacing',
						'mode'     => 'border',
						'title'    => __( 'Form Field Border Width (px)', 'porto' ),
						'subtitle' => __( 'Controls the border size of the form fields such as input and select boxes.', 'porto' ),
						'units'    => 'px',
					),
					array(
						'id'       => 'form-field-bc',
						'type'     => 'color',
						'title'    => __( 'Form Field Border Color', 'porto' ),
						'subtitle' => __( 'Controls the border color of form fields such as input and select boxes.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'form-field-bcf',
						'type'     => 'color',
						'title'    => __( 'Form Field Border Color on Focus', 'porto' ),
						'subtitle' => __( 'Controls the border color of form fields such as input and select boxes on focus status.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'form-br',
						'type'     => 'text',
						'title'    => __( 'Form Border Radius', 'porto' ),
						'subtitle' => __( 'Controls the border radius of form fields such as input, select boxes and buttons. Enter value including any valid CSS unit, ex: 30px.', 'porto' ),
					),
				),
			);
			if ( $options_style ) {
				$this->sections[] = array(
					'icon'       => 'icon-visual',
					'icon_class' => 'porto-icon',
					'title'      => __( 'Skin', 'porto' ),
					'customizer' => false,
				);
			}
			$this->sections[] = array(
				'id'         => 'skin-custom-css',
				'icon_class' => 'icon',
				'title'      => __( 'Custom CSS', 'porto' ),
				'subsection' => true,
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'       => 'css-code',
						'type'     => 'ace_editor',
						'title'    => __( 'CSS Code', 'porto' ),
						'subtitle' => __( 'Paste your custom CSS code here.', 'porto' ),
						'mode'     => 'css',
						'theme'    => 'monokai',
						'default'  => '',
						'options'  => array(
							'height'   => 450,
							'minLines' => 40,
							'maxLines' => 50,
						),
					),
				),
			);
			$this->sections[] = array(
				'icon_class' => 'el-icon-edit',
				'title'      => __( 'Javascript Code', 'porto' ),
				'subsection' => true,
				'customizer' => false,
				'fields'     => array(
					array(
						'id'       => 'js-code-head',
						'type'     => 'ace_editor',
						'title'    => __( 'JS Code before &lt;/head&gt;', 'porto' ),
						'subtitle' => __( 'Paste your custom JavaScript code here.', 'porto' ),
						'mode'     => 'javascript',
						'theme'    => 'chrome',
						'default'  => '',
						'options'  => array(
							'height'   => 250,
							'minLines' => 15,
							'maxLines' => 25,
						),
					),
					array(
						'id'       => 'js-code',
						'type'     => 'ace_editor',
						'title'    => __( 'JS Code before &lt;/body&gt;', 'porto' ),
						'subtitle' => __( 'Paste your custom JavaScript code here.', 'porto' ),
						'mode'     => 'javascript',
						'theme'    => 'chrome',
						'default'  => '',
						'options'  => array(
							'height'   => 250,
							'minLines' => 15,
							'maxLines' => 25,
						),
					),
				),
			);
			// Header Settings
			if ( $this->legacy_mode ) {
				$this->sections[] = $this->add_customizer_field(
					array(
						'id'         => 'header-settings',
						'icon'       => 'Simple-Line-Icons-earphones',
						'icon_class' => '',
						'title'      => __( 'Header', 'porto' ),
						'transport'  => 'postMessage',
						'fields'     => array(
							array(
								'id'     => 'desc_info_header_skin_setting',
								'type'   => 'info',
								'desc'   => wp_kses(
									/* translators: %s: Header skin settings url */
									sprintf( __( 'Go to <a %s>Header Skin Settings</a>', 'porto' ), 'href="skin-header" class="goto-section"', 'porto' ),
									array(
										'a' => array(
											'href'  => array(),
											'class' => array(),
										),
									)
								),
								'class'  => 'field_move',
								'notice' => false,
							),
							array(
								'id'    => 'desc_info_go_header_builder',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $header_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'        => 'header-view',
								'type'      => 'button_set',
								'title'     => __( 'Header View', 'porto' ),
								'subtitle'  => __( 'Controls if using default header or fixed header, or hiding it.', 'porto' ),
								'options'   => array_merge(
									porto_ct_header_view(),
									array(
										'hide' => __( 'Hide', 'porto' ),
									)
								),
								'default'   => 'default',
								'transport' => 'refresh',
							),
							array(
								'id'        => 'header-side-position',
								'type'      => 'button_set',
								'title'     => __( 'Position (Header Type: Side)', 'porto' ),
								'subtitle'  => __( 'When your header type is side header, determines where to put it.', 'porto' ),
								'options'   => array(
									''      => __( 'Left', 'porto' ),
									'right' => __( 'Right', 'porto' ),
								),
								'default'   => '',
								'transport' => 'refresh',
							),
							array(
								'id'       => 'show-header-tooltip',
								'type'     => 'switch',
								'title'    => __( 'Show Tooltip', 'porto' ),
								'subtitle' => __( 'Turn on to display tooltip icon with flash effect and popup content.', 'porto' ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'header-tooltip',
								'type'     => 'textarea',
								'title'    => __( 'Tooltip Content', 'porto' ),
								'required' => array( 'show-header-tooltip', 'equals', true ),
							),
							array(
								'id'     => 'desc_info_header_preset_customize',
								'type'   => 'info',
								'desc'   => __( 'For Header Preset or Customize Header Builder', 'porto' ),
								'notice' => false,
								'class'  => 'porto-redux-section',
							),
							array(
								'id'       => 'show-header-top',
								'type'     => 'switch',
								'title'    => __( 'Show Header Top', 'porto' ),
								'subtitle' => __( 'Controls if show header top. This setting doesn\'t work for header builders.', 'porto' ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'welcome-msg',
								'type'     => 'textarea',
								'title'    => __( 'Welcome Message', 'porto' ),
								'subtitle' => __( 'Inputs the html to be displayed in the header top for preset header types.', 'porto' ),
								'default'  => '',
							),
							array(
								'id'       => 'header-contact-info',
								'type'     => 'textarea',
								'title'    => __( 'Contact Info', 'porto' ),
								'subtitle' => __( 'Inputs the html content to be used as contact information in the header.', 'porto' ),
								'default'  => "<ul class=\"nav nav-pills nav-top\">\r\n\t<li class=\"d-none d-sm-block\">\r\n\t\t<a href=\"#\" target=\"_blank\"><i class=\"fas fa-angle-right\"></i>About Us</a> \r\n\t</li>\r\n\t<li class=\"d-none d-sm-block\">\r\n\t\t<a href=\"#\" target=\"_blank\"><i class=\"fas fa-angle-right\"></i>Contact Us</a> \r\n\t</li>\r\n\t<li class=\"phone nav-item-left-border nav-item-right-border\">\r\n\t\t<span><i class=\"fas fa-phone\"></i>(123) 456-7890</span>\r\n\t</li>\r\n</ul>\r\n",
							),
							array(
								'id'      => 'header-copyright',
								'type'    => 'textarea',
								'title'   => __( 'Side Navigation Copyright (Header Type: Side)', 'porto' ),
								/* translators: %s: Current year */
								'default' => sprintf( __( '&copy; Copyright %s. All Rights Reserved.', 'porto' ), date( 'Y' ) ),
							),
						),
					),
					$options_style
				);
			} else {
				$this->sections[] = $this->add_customizer_field(
					array(
						'id'         => 'header-settings',
						'icon'       => 'Simple-Line-Icons-earphones',
						'icon_class' => '',
						'title'      => __( 'Header', 'porto' ),
						'transport'  => 'postMessage',
						'fields'     => array(
							array(
								'id'     => 'desc_info_header_skin_setting',
								'type'   => 'info',
								'desc'   => wp_kses(
									/* translators: %s: Header skin settings url */
									sprintf( __( 'Go to <a %s>Header Skin Settings</a>', 'porto' ), 'href="skin-header" class="goto-section"', 'porto' ),
									array(
										'a' => array(
											'href'  => array(),
											'class' => array(),
										),
									)
								),
								'class'  => 'field_move',
								'notice' => false,
							),
							array(
								'id'    => 'desc_info_go_header_builder',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily.', 'porto' ), $header_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'        => 'header-view',
								'type'      => 'button_set',
								'title'     => __( 'Header View', 'porto' ),
								'subtitle'  => __( 'Controls if using default header or fixed header, or hiding it.', 'porto' ),
								'options'   => array_merge(
									porto_ct_header_view(),
									array(
										'hide' => __( 'Hide', 'porto' ),
									)
								),
								'default'   => 'default',
								'transport' => 'refresh',
							),
							array(
								'id'        => 'header-side-position',
								'type'      => 'button_set',
								'title'     => __( 'Position (Header Type: Side)', 'porto' ),
								'subtitle'  => __( 'When your header type is side header, determines where to put it.', 'porto' ),
								'options'   => array(
									''      => __( 'Left', 'porto' ),
									'right' => __( 'Right', 'porto' ),
								),
								'default'   => '',
								'transport' => 'refresh',
							),
						),
					),
					$options_style
				);
			}

			if ( $this->legacy_mode ) {
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Header Type', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'      => 'desc_info_header_type',
							'type'    => 'info',
							'default' => '',
							'desc'    => wp_kses(
								sprintf(
									/* translators: %s: Header builder url */
									__( 'You can add new header layout using <a href="%s" class="goto-header-builder">Header Builder in customizer panel</a>.', 'porto' ),
									esc_url(
										add_query_arg(
											array(
												'autofocus' => array(
													'section' => 'porto_header_layouts',
												),
												'url' => home_url(),
											),
											admin_url( 'customize.php' )
										)
									)
								),
								array(
									'a' => array(
										'href'  => array(),
										'title' => array(),
										'class' => array(),
									),
								)
							),
						),
						array(
							'id'       => 'header-type-select',
							'type'     => 'button_set',
							'title'    => __( 'Select Header', 'porto' ),
							'subtitle' => __( 'Preset, Header Builder in Customizer or Header Builder in Porto Templates builder.', 'porto' ),
							'options'  => array(
								''                 => __( 'Header Type', 'porto' ),
								'header_builder'   => __( 'Header builder in Customizer', 'porto' ),
								'header_builder_p' => __( 'Header builder in Porto Templates builder', 'porto' ),
							),
							'default'  => '',
						),
						array(
							'id'       => 'header-woo-icon',
							'type'     => 'button_set',
							'title'    => __( 'Show Wishlist/Account', 'porto' ),
							'desc'     => __( 'Determines to show the icon in header preset.', 'porto' ),
							'multi'    => true,
							'options'  => array(
								'wishlist' => __( 'Wishlist', 'porto' ),
								'account'  => __( 'Account', 'porto' ),
							),
							'required' => array(
								array( 'header-type-select', 'equals', '' ),
								array( 'header-type', 'equals', array( '1', '4', '7', '9', 'side' ) ),
							),
							'default'  => array(),
						),
						array(
							'id'         => 'header-type',
							'type'       => 'image_select',
							'full_width' => true,
							'title'      => __( 'Header Types', 'porto' ),
							'subtitle'   => __( 'Whenever you change header type, related theme options such as Mini Cart Type and Search Layout may be changed together according to it.', 'porto' ),
							'options'    => $porto_header_type,
							'default'    => '10',
							'required'   => array( 'header-type-select', 'equals', '' ),
						),
					),
				);
			} else {
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Header Type', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'      => 'header-type-select',
							'type'    => 'button_set',
							'title'   => __( 'Select Header', 'porto' ),
							'options' => array(
								'header_builder_p' => __( 'Porto Templates builder', 'porto' ),
							),
							'default' => 'header_builder_p',
						),
					),
				);
			}
			$this->sections[] = array(
				'id'         => 'header-view-currency-switcher',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Language, Currency Switcher', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'      => 'wpml-switcher',
						'type'    => 'switch',
						'title'   => __( 'Show Language Switcher', 'porto' ),
						'desc'    => __( 'Show language switcher instead of view switcher menu.', 'porto' ) . ' ' . __( 'Compatible with Polylang and qTranslate X plugins.', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'wpml-switcher-pos',
						'type'     => 'button_set',
						'title'    => __( 'Language Switcher Position', 'porto' ),
						'required' => array( 'wpml-switcher', 'equals', true ),
						'options'  => array(
							''          => __( 'Default', 'porto' ),
							'top_nav'   => __( 'In Top Navigation', 'porto' ),
							'main_menu' => __( 'In Main Menu', 'porto' ),
						),
						'default'  => '',
					),
					array(
						'id'      => 'wpml-switcher-html',
						'type'    => 'switch',
						'title'   => __( 'Show Language Switcher HTML', 'porto' ),
						'desc'    => __( 'Show language switcher html code if there isn\'t any switcher.', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'wcml-switcher',
						'type'    => 'switch',
						'title'   => __( 'Show Currency Switcher', 'porto' ),
						'desc'    => __( 'Show currency switcher instead of currency switcher menu.', 'porto' ) . ' ' . __( 'Compatible with WPML Currency Switcher and Woocommerce Currency Switcher plugins.', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'wcml-switcher-pos',
						'type'     => 'button_set',
						'title'    => __( 'Currency Switcher Position', 'porto' ),
						'required' => array( 'wcml-switcher', 'equals', true ),
						'options'  => array(
							''          => __( 'Default', 'porto' ),
							'top_nav'   => __( 'In Top Navigation', 'porto' ),
							'main_menu' => __( 'In Main Menu', 'porto' ),
						),
						'default'  => '',
					),
					array(
						'id'      => 'wcml-switcher-html',
						'type'    => 'switch',
						'title'   => __( 'Show Currency Switcher HTML', 'porto' ),
						'desc'    => __( 'Show currency switcher html code if there isn\'t any switcher.', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'     => 'desc_info_switcher',
						'type'   => 'info',
						'title'  => __( 'Styling', 'porto' ),
						'notice' => false,
					),
					array(
						'id'       => 'switcher-bg-color',
						'type'     => 'color',
						'title'    => __( 'Background Color', 'porto' ),
						'subtitle' => __( 'Controls the background color for language switcher and currency switcher.', 'porto' ),
						'default'  => 'transparent',
						'validate' => 'color',
					),
					array(
						'id'       => 'switcher-hbg-color',
						'type'     => 'color',
						'title'    => __( 'Hover Background Color', 'porto' ),
						'subtitle' => __( 'Controls the background color for language switcher and currency switcher on hover.', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
					),
					array(
						'id'       => 'switcher-top-level-hover',
						'type'     => 'switch',
						'title'    => __( 'Change top level on hover', 'porto' ),
						'subtitle' => __( 'Controls if change the text color and background color for the first level item on hover.', 'porto' ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'switcher-link-color',
						'type'    => 'link_color',
						'active'  => false,
						'title'   => __( 'Link Color', 'porto' ),
						'default' => array(
							'regular' => '#777777',
							'hover'   => '#777777',
						),
						'desc'    => __( 'Regular is the color of top level link and hover is the color of sub menu items.', 'porto' ),
					),
				),
			);
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Social Links', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'       => 'show-header-socials',
						'type'     => 'switch',
						'title'    => __( 'Show Social Links', 'porto' ),
						'subtitle' => __( 'Show/Hide the social links in header.', 'porto' ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'header-socials-nofollow',
						'type'     => 'switch',
						'title'    => __( 'Add rel="nofollow" to social links', 'porto' ),
						'subtitle' => __( 'Turn on to add "nofollow" attribute to header social links.', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'header-social-facebook',
						'type'     => 'text',
						'title'    => __( 'Facebook', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-twitter',
						'type'     => 'text',
						'title'    => __( 'Twitter', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-rss',
						'type'     => 'text',
						'title'    => __( 'RSS', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-pinterest',
						'type'     => 'text',
						'title'    => __( 'Pinterest', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-youtube',
						'type'     => 'text',
						'title'    => __( 'Youtube', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-instagram',
						'type'     => 'text',
						'title'    => __( 'Instagram', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-skype',
						'type'     => 'text',
						'title'    => __( 'Skype', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-linkedin',
						'type'     => 'text',
						'title'    => __( 'LinkedIn', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-vk',
						'type'     => 'text',
						'title'    => __( 'VK', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-xing',
						'type'     => 'text',
						'title'    => __( 'Xing', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-tumblr',
						'type'     => 'text',
						'title'    => __( 'Tumblr', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-reddit',
						'type'     => 'text',
						'title'    => __( 'Reddit', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-vimeo',
						'type'     => 'text',
						'title'    => __( 'Vimeo', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-telegram',
						'type'     => 'text',
						'title'    => __( 'Telegram', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-yelp',
						'type'     => 'text',
						'title'    => __( 'Yelp', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-flickr',
						'type'     => 'text',
						'title'    => __( 'Flickr', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-whatsapp',
						'type'     => 'text',
						'title'    => __( 'WhatsApp', 'porto' ),
						'desc'     => __( 'Only For Mobile', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-wechat',
						'type'     => 'text',
						'title'    => __( 'WeChat', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
					array(
						'id'       => 'header-social-tiktok',
						'type'     => 'text',
						'title'    => __( 'Tiktok', 'porto' ),
						'required' => array( 'show-header-socials', 'equals', true ),
					),
				),
			);

			$skin_search_form = array(
				array(
					'id'     => 'desc_info_search_form',
					'type'   => 'info',
					'title'  => __( 'Styling', 'porto' ),
					'notice' => false,
				),
				array(
					'id'       => 'searchform-bg-color',
					'type'     => 'color',
					'title'    => __( 'Background Color', 'porto' ),
					'desc'     => __( 'Controls the background color of search form.', 'porto' ),
					'default'  => '#ffffff',
					'validate' => 'color',
				),
				array(
					'id'       => 'searchform-border-color',
					'type'     => 'color',
					'title'    => __( 'Border Color', 'porto' ),
					'desc'     => __( 'Controls the border color of search form.', 'porto' ),
					'default'  => '#eeeeee',
					'validate' => 'color',
				),
				array(
					'id'       => 'searchform-popup-border-color',
					'type'     => 'color',
					'title'    => __( 'Popup Border Color', 'porto' ),
					'desc'     => __( 'Controls the border color of search popup.', 'porto' ),
					'default'  => '#cccccc',
					'validate' => 'color',
				),
				array(
					'id'       => 'searchform-text-color',
					'type'     => 'color',
					'title'    => __( 'Text Color', 'porto' ),
					'desc'     => __( 'Controls the text color on search form.', 'porto' ),
					'default'  => '#555555',
					'validate' => 'color',
				),
				array(
					'id'       => 'searchform-hover-color',
					'type'     => 'color',
					'title'    => __( 'Button Text Color', 'porto' ),
					'desc'     => __( 'Controls the search icon color on search form.', 'porto' ),
					'default'  => '#333333',
					'validate' => 'color',
				),
				array(
					'id'     => 'desc_info_search_sticky',
					'type'   => 'info',
					'title'  => __( 'In Sticky Header', 'porto' ),
					'notice' => false,
				),
				array(
					'id'       => 'sticky-searchform-popup-border-color',
					'type'     => 'color',
					'title'    => __( 'Popup Border Color', 'porto' ),
					'desc'     => __( 'Controls the border color of search popup on sticky header.', 'porto' ),
					'default'  => '',
					'validate' => 'color',
				),
				array(
					'id'     => 'sticky-searchform-toggle-color',
					'type'   => 'link_color',
					'title'  => __( 'Toggle Text Color', 'porto' ),
					'desc'   => __( 'Controls the toggle color on sticky header.', 'porto' ),
					'active' => false,
				),
			);
			if ( $this->legacy_mode ) {
				$this->sections[] = array(
					'id'         => 'header-search-form',
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Search Form', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array_merge(
						array(
							array(
								'id'      => 'search-live',
								'type'    => 'switch',
								'title'   => __( 'Live Search', 'porto' ),
								'desc'    => __( 'This will display quick search results whenever you input characters in the search box.', 'porto' ),
								'default' => true,
							),
							array(
								'id'       => 'search-by',
								'type'     => 'button_set',
								'title'    => __( 'Search By', 'porto' ),
								'desc'     => __( 'Allow search by individual items in live search.', 'porto' ),
								'multi'    => true,
								'options'  => array(
									'sku'         => __( 'Search by SKU', 'porto' ),
									'product_tag' => __( 'Search by Product Tag', 'porto' ),
									'ct_taxonomy' => __( 'Custom Taxonomy', 'porto' ),
								),
								'required' => array( 'search-live', 'equals', true ),
								'default'  => array( 'sku', 'product_tag' ),
							),
							array(
								'id'    => 'desc_info_search_notice',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. If you use builder, some options might be overrided by Search Form widget.', 'porto' ), $header_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'show-searchform',
								'type'    => 'switch',
								'title'   => __( 'Show Search Form', 'porto' ),
								'default' => true,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'search-layout',
								'type'     => 'button_set',
								'title'    => __( 'Search Layout', 'porto' ),
								'subtitle' => __( 'Controls the layout of the search forms.', 'porto' ),
								'required' => array( 'show-searchform', 'equals', true ),
								'options'  => array(
									'simple'   => __( 'Popup 1', 'porto' ),
									'large'    => __( 'Popup 2', 'porto' ),
									'reveal'   => __( 'Reveal', 'porto' ),
									'advanced' => __( 'Form', 'porto' ),
									'overlay'  => __( 'Overlay Popup', 'porto' ),
								),
								'default'  => $search_layout_default,
							),
							array(
								'id'       => 'search-border-radius',
								'type'     => 'switch',
								'title'    => __( 'Border Radius', 'porto' ),
								'required' => array( 'show-searchform', 'equals', true ),
								'default'  => true,
								'on'       => __( 'On', 'porto' ),
								'off'      => __( 'Off', 'porto' ),
							),
							array(
								'id'       => 'search-type',
								'type'     => 'button_set',
								'title'    => __( 'Search Content Type', 'porto' ),
								'subtitle' => __( 'Controls the post types that displays in search results.', 'porto' ),
								'required' => array( 'show-searchform', 'equals', true ),
								'options'  => array(
									'all'       => __( 'All', 'porto' ),
									'post'      => __( 'Post', 'porto' ),
									'product'   => __( 'Product', 'porto' ),
									'portfolio' => __( 'Portfolio', 'porto' ),
									'event'     => __( 'Event', 'porto' ),
								),
								'default'  => 'all',
							),
							array(
								'id'       => 'search-cats',
								'type'     => 'switch',
								'title'    => __( 'Show Categories', 'porto' ),
								'required' => array( 'search-type', 'equals', array( 'post', 'product' ) ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'search-cats-mobile',
								'type'     => 'switch',
								'title'    => __( 'Show Categories on Mobile', 'porto' ),
								'desc'     => __( 'This option works for only real mobile devices.', 'porto' ),
								'required' => array( 'search-cats', 'equals', true ),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'search-sub-cats',
								'type'     => 'switch',
								'title'    => __( 'Show Sub Categories', 'porto' ),
								'subtitle' => __( 'Show categories including subcategory.', 'porto' ),
								'required' => array( 'search-cats', 'equals', true ),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'    => 'search-placeholder',
								'type'  => 'text',
								'title' => __( 'Search Placeholder', 'porto' ),
							),
						),
						$skin_search_form
					),
				);
			} else {
				$this->sections[] = array(
					'id'         => 'header-search-form',
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Search Form', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array_merge(
						array(
							array(
								'id'      => 'search-live',
								'type'    => 'switch',
								'title'   => __( 'Live Search', 'porto' ),
								'desc'    => __( 'This will display quick search results whenever you input characters in the search box.', 'porto' ),
								'default' => true,
							),
							array(
								'id'       => 'search-by',
								'type'     => 'button_set',
								'title'    => __( 'Search By', 'porto' ),
								'desc'     => __( 'Allow search by individual items in live search.', 'porto' ),
								'multi'    => true,
								'options'  => array(
									'sku'         => __( 'Search by SKU', 'porto' ),
									'product_tag' => __( 'Search by Product Tag', 'porto' ),
									'ct_taxonomy' => __( 'Custom Taxonomy', 'porto' ),
								),
								'required' => array( 'search-live', 'equals', true ),
								'default'  => array( 'sku', 'product_tag' ),
							),
							array(
								'id'    => 'desc_info_search_notice',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. If you use builder, some options might be overrided by Search Form widget.', 'porto' ), $header_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'show-searchform',
								'type'    => 'switch',
								'title'   => __( 'Show Search Form', 'porto' ),
								'default' => true,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'search-layout',
								'type'     => 'button_set',
								'title'    => __( 'Search Layout', 'porto' ),
								'subtitle' => __( 'Controls the layout of the search forms.', 'porto' ),
								'required' => array( 'show-searchform', 'equals', true ),
								'options'  => array(
									'simple'   => __( 'Popup 1', 'porto' ),
									'large'    => __( 'Popup 2', 'porto' ),
									'reveal'   => __( 'Reveal', 'porto' ),
									'advanced' => __( 'Form', 'porto' ),
									'overlay'  => __( 'Overlay Popup', 'porto' ),
								),
								'default'  => $search_layout_default,
							),
							array(
								'id'       => 'search-type',
								'type'     => 'button_set',
								'title'    => __( 'Search Content Type', 'porto' ),
								'subtitle' => __( 'Controls the post types that displays in search results.', 'porto' ),
								'required' => array( 'show-searchform', 'equals', true ),
								'options'  => array(
									'all'       => __( 'All', 'porto' ),
									'post'      => __( 'Post', 'porto' ),
									'product'   => __( 'Product', 'porto' ),
									'portfolio' => __( 'Portfolio', 'porto' ),
									'event'     => __( 'Event', 'porto' ),
								),
								'default'  => 'all',
							),
						),
						$skin_search_form
					)
				);
			}

			if ( $this->legacy_mode ) {
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Sticky Header', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'        => 'enable-sticky-header',
							'type'      => 'switch',
							'title'     => __( 'Enable Sticky Header', 'porto' ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'        => 'enable-sticky-header-tablet',
							'type'      => 'switch',
							'title'     => __( 'Enable on Tablet (width < 992px)', 'porto' ),
							//'required'  => array( 'enable-sticky-header', 'equals', true ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'        => 'enable-sticky-header-mobile',
							'type'      => 'switch',
							'title'     => __( 'Enable on Mobile (width <= 480)', 'porto' ),
							//'required'  => array( 'enable-sticky-header-tablet', 'equals', true ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'      => 'sticky-header-effect',
							'type'    => 'button_set',
							'title'   => __( 'Sticky Header Effect', 'porto' ),
							'options' => array(
								''       => __( 'None', 'porto' ),
								'reveal' => __( 'Reveal', 'porto' ),
							),
							'default' => '',
						),
						array(
							'id'      => 'show-sticky-logo',
							'type'    => 'switch',
							'title'   => __( 'Show Logo', 'porto' ),
							//'required' => array( 'enable-sticky-header', 'equals', true ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'change-header-logo',
							'type'    => 'switch',
							'title'   => __( 'Change Logo Size in Sticky Header', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'show-sticky-searchform',
							'type'    => 'switch',
							'title'   => __( 'Show Search Form', 'porto' ),
							'desc'    => __( 'If header type is 1, 4, 9, 13, 14 or header builder', 'porto' ),
							//'required' => array( 'enable-sticky-header', 'equals', true ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'show-sticky-minicart',
							'type'    => 'switch',
							'title'   => __( 'Show Mini Cart', 'porto' ),
							'desc'    => __( 'If header type is 1, 4, 9, 13, 14, 17 or header builder', 'porto' ),
							//'required' => array( 'enable-sticky-header', 'equals', true ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'show-sticky-menu-custom-content',
							'type'    => 'switch',
							'title'   => __( 'Show Menu Custom Content', 'porto' ),
							'desc'    => __( 'If header type is 1, 4, 13, 14, 17 or header builder', 'porto' ),
							//'required' => array( 'enable-sticky-header', 'equals', true ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'show-sticky-contact-info',
							'type'    => 'switch',
							'title'   => __( 'Show Wishlist / Account', 'porto' ),
							'desc'    => __( 'Determines to show woocommerce icon in sticky header of header preset.', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'     => 'desc_info_sticky_header',
							'type'   => 'info',
							'title'  => __( 'Styling', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'sticky-header-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the sticky header\'s background settings', 'porto' ),
							'default'  => array(
								'background-color' => '#ffffff',
							),
						),
						array(
							'id'      => 'sticky-header-bg-gradient',
							'type'    => 'switch',
							'title'   => __( 'Sticky Header Background Gradient', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'sticky-header-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Sticky Header Background Gradient Color', 'porto' ),
							'required' => array( 'sticky-header-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '#f6f6f6',
								'to'   => '#ffffff',
							),
						),
						array(
							'id'      => 'sticky-header-opacity',
							'type'    => 'text',
							'title'   => __( 'Sticky Header Background Opacity', 'porto' ),
							'default' => '100%',
						),
						array(
							'id'       => 'mainmenu-wrap-padding-sticky',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Sticky Header Padding', 'porto' ),
							'subtitle' => __( 'Controls the padding of header left, center and right parts in the sticky header.', 'porto' ),
							'default'  => array(
								'padding-top'    => 8,
								'padding-bottom' => 8,
								'padding-left'   => 0,
								'padding-right'  => 0,
							),
						),
					),
				);
			} else {
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Sticky Header', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'        => 'enable-sticky-header',
							'type'      => 'switch',
							'title'     => __( 'Enable Sticky Header', 'porto' ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'        => 'enable-sticky-header-tablet',
							'type'      => 'switch',
							'title'     => __( 'Enable on Tablet (width < 992px)', 'porto' ),
							//'required'  => array( 'enable-sticky-header', 'equals', true ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'        => 'enable-sticky-header-mobile',
							'type'      => 'switch',
							'title'     => __( 'Enable on Mobile (width <= 480)', 'porto' ),
							//'required'  => array( 'enable-sticky-header-tablet', 'equals', true ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'      => 'sticky-header-effect',
							'type'    => 'button_set',
							'title'   => __( 'Sticky Header Effect', 'porto' ),
							'options' => array(
								''       => __( 'None', 'porto' ),
								'reveal' => __( 'Reveal', 'porto' ),
							),
							'default' => '',
						),
						array(
							'id'     => 'desc_info_sticky_header',
							'type'   => 'info',
							'title'  => __( 'Styling', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'sticky-header-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the sticky header\'s background settings', 'porto' ),
							'default'  => array(
								'background-color' => '#ffffff',
							),
						),
						array(
							'id'      => 'sticky-header-opacity',
							'type'    => 'text',
							'title'   => __( 'Sticky Header Background Opacity', 'porto' ),
							'default' => '100%',
						),
						array(
							'id'       => 'mainmenu-wrap-padding-sticky',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Sticky Header Padding', 'porto' ),
							'subtitle' => __( 'Controls the padding of header left, center and right parts in the sticky header.', 'porto' ),
							'default'  => array(
								'padding-top'    => 8,
								'padding-bottom' => 8,
								'padding-left'   => 0,
								'padding-right'  => 0,
							),
						),
					),
				);
			}

			if ( class_exists( 'WooCommerce' ) ) { // Header > cart
				$cart_skin_options = array(
					array(
						'id'       => 'minicart-icon-font-size',
						'type'     => 'text',
						'title'    => __( 'Icon Font Size', 'porto' ),
						'subtitle' => __( 'Controls the font size for the mini cart icon. Enter value including any valid CSS unit, ex: 30px.', 'porto' ),
						'default'  => '',
					),
					array(
						'id'       => 'minicart-icon-color',
						'type'     => 'color',
						'title'    => __( 'Icon Color', 'porto' ),
						'subtitle' => __( 'Controls the color of cart icon.', 'porto' ),
						'default'  => '#0088cc',
						'validate' => 'color',
					),
					array(
						'id'       => 'minicart-item-color',
						'type'     => 'color',
						'title'    => __( 'Item Color', 'porto' ),
						'subtitle' => __( 'Controls the text color for the mini cart item count.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'minicart-item-bg-color',
						'type'     => 'color',
						'title'    => __( 'Item Background Color', 'porto' ),
						'subtitle' => __( 'Controls the background color for the mini cart item count.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'minicart-bg-color',
						'type'     => 'color',
						'title'    => __( 'Background Color', 'porto' ),
						'subtitle' => __( 'Controls the background color of mini cart.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'minicart-popup-border-color',
						'type'     => 'color',
						'title'    => __( 'Popup Border Color', 'porto' ),
						'subtitle' => __( 'Controls the border color of cart popup.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'     => 'desc_info_cart_sticky',
						'type'   => 'info',
						'title'  => __( 'In Sticky Header', 'porto' ),
						'notice' => false,
					),
					array(
						'id'       => 'sticky-minicart-icon-color',
						'type'     => 'color',
						'title'    => __( 'Icon Color', 'porto' ),
						'subtitle' => __( 'Controls the color of cart icon on sticky header.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'sticky-minicart-item-color',
						'type'     => 'color',
						'title'    => __( 'Item Color', 'porto' ),
						'subtitle' => __( 'Controls the text color of mini cart item count on sticky header.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'sticky-minicart-item-bg-color',
						'type'     => 'color',
						'title'    => __( 'Item Background Color', 'porto' ),
						'subtitle' => __( 'Controls the background color of mini cart item count on sticky header.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'sticky-minicart-bg-color',
						'type'     => 'color',
						'title'    => __( 'Background Color', 'porto' ),
						'subtitle' => __( 'Controls the background color of mini cart on sticky header.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'sticky-minicart-popup-border-color',
						'type'     => 'color',
						'title'    => __( 'Popup Border Color', 'porto' ),
						'subtitle' => __( 'Controls the border color of cart popup on sticky header.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
				);
				if ( $this->legacy_mode ) {
					$this->sections[] = array(
						'id'         => 'header-woocommerce',
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'WooCommerce', 'porto' ),
						'fields'     => array_merge(
							array(
								array(
									'id'    => 'desc_info_header_woocommerce_notice',
									'type'  => 'info',
									'desc'  => wp_kses(
										/* translators: %s: Builder url */
										sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. If you use builder, some options might be overrided by Mini-Cart, Wishlist, Account widget.', 'porto' ), $header_url ),
										array(
											'strong' => array(),
											'b'      => array(),
											'a'      => array(
												'href'   => array(),
												'target' => array(),
												'class'  => array(),
											),
										)
									),
									'class' => 'porto-important-note',
								),
								array(
									'id'       => 'wl-offcanvas',
									'type'     => 'switch',
									'title'    => __( 'Show Off Canvas Wishlist', 'porto' ),
									'subtitle' => __( 'Controls to show the wishlist dropdown as off canvas.', 'porto' ),
									'default'  => false,
									'on'       => __( 'Yes', 'porto' ),
									'off'      => __( 'No', 'porto' ),
								),
								array(
									'id'     => 'desc_info_header_account',
									'type'   => 'info',
									'title'  => __( 'Account Menu', 'porto' ),
									'notice' => false,
								),
								array(
									'id'        => 'show-account-dropdown',
									'type'      => 'switch',
									'title'     => __( 'Show Account Dropdown', 'porto' ),
									'subtitle'  => __( 'When user is logged in, Menu that is located in Account Menu will be shown.', 'porto' ),
									'default'   => false,
									'on'        => __( 'Yes', 'porto' ),
									'off'       => __( 'No', 'porto' ),
									'transport' => 'refresh',
								),
								array(
									'id'             => 'account-menu-font',
									'type'           => 'typography',
									'title'          => __( 'Account Dropdown Font', 'porto' ),
									'subtitle'       => __( 'Controls the typography for account dropdown menu.', 'porto' ),
									'google'         => true,
									'subsets'        => false,
									'font-style'     => false,
									'text-align'     => false,
									'color'          => false,
									'letter-spacing' => true,
									'compiler'       => true,
									'default'        => array(
										'google'      => true,
										'font-weight' => '400',
										'font-family' => '',
										'font-size'   => '11px',
										'line-height' => '16.5px',
									),
									'required'       => array( 'show-account-dropdown', 'equals', true ),
								),
								array(
									'id'       => 'account-dropdown-bgc',
									'type'     => 'color',
									'title'    => __( 'Background Color', 'porto' ),
									'subtitle' => __( 'Controls the background color for account dropdown.', 'porto' ),
									'default'  => '#ffffff',
									'validate' => 'color',
									'required' => array( 'show-account-dropdown', 'equals', true ),
								),
								array(
									'id'       => 'account-dropdown-hbgc',
									'type'     => 'color',
									'title'    => __( 'Hover Background Color', 'porto' ),
									'subtitle' => __( 'Controls the background color for account dropdown item on hover.', 'porto' ),
									'default'  => '',
									'validate' => 'color',
									'required' => array( 'show-account-dropdown', 'equals', true ),
								),
								array(
									'id'       => 'account-dropdown-lc',
									'type'     => 'link_color',
									'active'   => false,
									'title'    => __( 'Link Color', 'porto' ),
									'default'  => array(
										'regular' => '#777777',
										'hover'   => '#777777',
									),
									'required' => array( 'show-account-dropdown', 'equals', true ),
								),
								array(
									'id'     => 'desc_info_header_cart',
									'type'   => 'info',
									'title'  => __( 'Mini Cart', 'porto' ),
									'notice' => false,
								),
								array(
									'id'      => 'minicart-type',
									'type'    => 'button_set',
									'title'   => __( 'Mini Cart Type', 'porto' ),
									'options' => array(
										'none'               => __( 'None', 'porto' ),
										'simple'             => __( 'Simple', 'porto' ),
										'minicart-arrow-alt' => __( 'Arrow Alt', 'porto' ),
										'minicart-inline'    => __( 'Text', 'porto' ),
										'minicart-text'      => __( 'Icon & Text', 'porto' ),
									),
									'default' => $minicart_type,
								),
								array(
									'id'       => 'minicart-text',
									'type'     => 'text',
									'title'    => __( 'Mini Cart Text', 'porto' ),
									'subtitle' => __( 'Controls the cart label on header.', 'porto' ),
								),
								array(
									'id'       => 'minicart-icon',
									'type'     => 'text',
									'title'    => __( 'Mini Cart Icon', 'porto' ),
									'subtitle' => __( 'Inputs the custom mini cart icon. ex: porto-icon-shopping-cart', 'porto' ),
									'required' => array( 'minicart-type', 'equals', array( 'simple', 'minicart-arrow-alt', 'minicart-inline', 'minicart-text' ) ),
								),
								array(
									'id'       => 'minicart-content',
									'type'     => 'button_set',
									'title'    => __( 'Mini Cart Content Type', 'porto' ),
									'options'  => array(
										''          => __( 'Popup', 'porto' ),
										'offcanvas' => __( 'Off Canvas', 'porto' ),
									),
									'default'  => '',
									'required' => array( 'minicart-type', 'equals', array( 'simple', 'minicart-arrow-alt', 'minicart-inline' ) ),
								),
							),
							$cart_skin_options
						),
					);
				} else {
					$this->sections[] = array(
						'id'         => 'header-woocommerce',
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'WooCommerce', 'porto' ),
						'fields'     => array_merge(
							array(
								array(
									'id'    => 'desc_info_header_woocommerce_notice',
									'type'  => 'info',
									'desc'  => wp_kses(
										/* translators: %s: Builder url */
										sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. If you use builder, some options might be overrided by Mini-Cart widget.', 'porto' ), $header_url ),
										array(
											'strong' => array(),
											'b'      => array(),
											'a'      => array(
												'href'   => array(),
												'target' => array(),
												'class'  => array(),
											),
										)
									),
									'class' => 'porto-important-note',
								),
								array(
									'id'    => 'minicart-text',
									'type'  => 'text',
									'title' => __( 'Mini Cart Text', 'porto' ),
									'desc'  => __( 'Controls the cart label on header.', 'porto' ),
								),
							),
							$cart_skin_options
						),
					);
				}
			}

			if ( $this->legacy_mode ) {
				$this->sections[] = array(
					'id'         => 'skin-header',
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Styling', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'     => 'desc_info_header_builder',
							'type'   => 'info',
							'title'  => wp_kses(
								sprintf(
									/* translators: %s: Header Builder url */
									__( 'Go to <a href="%s" class="goto-header-builder">Header Builder</a>', 'porto' ),
									esc_url(
										add_query_arg(
											array(
												'autofocus' => array(
													'section' => 'porto_header_layouts',
												),
												'url' => home_url(),
											),
											admin_url( 'customize.php' )
										)
									)
								),
								array(
									'a' => array(
										'href'  => array(),
										'class' => array(),
									),
								)
							),
							'class'  => 'field_move',
							'notice' => false,
						),
						array(
							'id'    => 'desc_info_skin_header_notice',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $header_url ),
								array(
									'strong' => array(),
									'b'      => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'     => 'desc_info_header_wrapper',
							'type'   => 'info',
							'title'  => __( 'Header Wrapper', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'header-wrap-bg',
							'type'     => 'background',
							'title'    => __( 'Header Wrapper Background', 'porto' ),
							'subtitle' => __( 'Controls the header wrapper background settings.', 'porto' ),
							'default'  => array(
								'background-color' => '',
							),
						),
						array(
							'id'      => 'header-wrap-bg-gradient',
							'type'    => 'switch',
							'title'   => __( 'Header Wrapper Background Gradient', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'header-wrap-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Background Gradient Color', 'porto' ),
							'required' => array( 'header-wrap-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '',
								'to'   => '',
							),
						),
						array(
							'id'     => 'desc_info_header_top',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Header Top:</b> If you use <span>header builder</span>, below options <span>aren\'t</span> necessary. Please use the style options of header builder widgets.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'       => 'header-top-bg-color',
							'type'     => 'color',
							'title'    => __( 'Header Top Background Color', 'porto' ),
							'default'  => '#f4f4f4',
							'validate' => 'color',
						),
						array(
							'id'       => 'header-top-height',
							'type'     => 'slider',
							'title'    => __( 'Header Top Height', 'porto' ),
							'subtitle' => __( 'Controls the min height of header top.', 'porto' ),
							'default'  => 30,
							'min'      => 25,
							'max'      => 500,
						),
						array(
							'id'      => 'header-top-font-size',
							'type'    => 'text',
							'title'   => __( 'Header Top Font Size', 'porto' ),
							'desc'    => __( 'unit: px', 'porto' ),
							'default' => '',
						),
						array(
							'id'       => 'header-top-bottom-border',
							'type'     => 'border',
							'all'      => true,
							'style'    => false,
							'title'    => __( 'Bottom Border', 'porto' ),
							'subtitle' => __( 'Controls the bottom border width and color for header top section.', 'porto' ),
							'default'  => array(
								'border-color' => '#ededed',
								'border-top'   => '1px',
							),
						),
						array(
							'id'       => 'header-top-text-color',
							'type'     => 'color',
							'title'    => __( 'Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color in the header top section.', 'porto' ),
							'default'  => '#777777',
							'validate' => 'color',
						),
						array(
							'id'       => 'header-top-link-color',
							'type'     => 'link_color',
							'active'   => false,
							'title'    => __( 'Link Color', 'porto' ),
							'subtitle' => __( 'Controls the color of A tag in the header top section.', 'porto' ),
							'default'  => array(
								'regular' => '#0088cc',
								'hover'   => '#0099e6',
							),
						),
						array(
							'id'       => 'header-top-menu-padding',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Top Menu Padding', 'porto' ),
							'subtitle' => __( 'Controls the padding of top links.', 'porto' ),
							'default'  => array(
								'padding-top'    => 5,
								'padding-bottom' => 5,
								'padding-left'   => 5,
								'padding-right'  => 5,
							),
						),
						array(
							'id'       => 'header-top-menu-hide-sep',
							'type'     => 'switch',
							'title'    => __( 'Hide Top Menu Separator', 'porto' ),
							'subtitle' => __( 'Controls if hide the separator between top links items.', 'porto' ),
							'default'  => true,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),

						array(
							'id'     => 'desc_info_header',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Header:</b> If you use <span>header builder</span>, below options <span>aren\'t</span> necessary. Please use the style options of header builder widgets.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'       => 'header-bg',
							'type'     => 'background',
							'title'    => __( 'Header Main Background', 'porto' ),
							'subtitle' => __( 'Controls the header background settings', 'porto' ),
							'default'  => array(
								'background-color' => '#ffffff',
							),
						),
						array(
							'id'      => 'header-bg-gradient',
							'type'    => 'switch',
							'title'   => __( 'Header Background Gradient', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'header-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Header Background Gradient Color', 'porto' ),
							'required' => array( 'header-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '#f6f6f6',
								'to'   => '#ffffff',
							),
						),
						array(
							'id'       => 'header-text-color',
							'type'     => 'color',
							'title'    => __( 'Header Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color in the header.', 'porto' ),
							'default'  => '',
							'validate' => 'color',
						),
						array(
							'id'       => 'header-link-color',
							'type'     => 'link_color',
							'active'   => false,
							'title'    => __( 'Header Link Color', 'porto' ),
							'subtitle' => __( 'Controls the color of A tag in the header.', 'porto' ),
							'default'  => array(
								'regular' => '#999999',
								'hover'   => '#999999',
							),
						),
						array(
							'id'      => 'header-top-border',
							'type'    => 'border',
							'all'     => true,
							'style'   => false,
							'title'   => __( 'Header Top Border', 'porto' ),
							'default' => array(
								'border-color' => '#ededed',
								'border-top'   => '3px',
							),
						),
						array(
							'id'       => 'header-margin',
							'type'     => 'spacing',
							'mode'     => 'margin',
							'title'    => __( 'Header Margin', 'porto' ),
							'subtitle' => __( 'Controls the margin of header.', 'porto' ),
							'default'  => array(
								'margin-top'    => 0,
								'margin-bottom' => 0,
								'margin-left'   => 0,
								'margin-right'  => 0,
							),
						),
						array(
							'id'       => 'header-main-padding',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Header Main Padding', 'porto' ),
							'subtitle' => __( 'Controls padding top and bottom of the left, center and right parts in the header main.', 'porto' ),
							'left'     => false,
							'right'    => false,
							'units'    => 'px',
							'default'  => array(
								'padding-top'    => '',
								'padding-bottom' => '',
							),
						),
						array(
							'id'       => 'header-main-padding-mobile',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Header Main Padding (window width < 992px)', 'porto' ),
							'subtitle' => __( 'Controls padding top and bottom of the left, center and right parts in the header main on mobile.', 'porto' ),
							'left'     => false,
							'right'    => false,
							'default'  => array(
								'padding-top'    => '',
								'padding-bottom' => '',
							),
						),
						array(
							'id'     => 'desc_info_header_bottom',
							'type'   => 'info',
							'title'  => wp_kses(
								sprintf(
									/* translators: %s: Header Builder url */
									__( 'Header Bottom (Only <a href="%s" class="goto-header-builder">Customize Header Builder</a>.)', 'porto' ),
									esc_url(
										add_query_arg(
											array(
												'autofocus' => array(
													'section' => 'porto_header_layouts',
												),
												'url' => home_url(),
											),
											admin_url( 'customize.php' )
										)
									)
								),
								array(
									'a' => array(
										'href'  => array(),
										'title' => array(),
										'class' => array(),
									),
								)
							),
							'notice' => false,
						),
						array(
							'id'       => 'header-bottom-bg-color',
							'type'     => 'color',
							'title'    => __( 'Header Bottom Background Color', 'porto' ),
							'default'  => '',
							'validate' => 'color',
						),
						array(
							'id'       => 'header-bottom-container-bg-color',
							'type'     => 'color',
							'title'    => __( 'Header Bottom Container Background Color', 'porto' ),
							'default'  => '',
							'validate' => 'color',
						),
						array(
							'id'      => 'header-bottom-height',
							'type'    => 'text',
							'title'   => __( 'Header Bottom Height', 'porto' ),
							'desc'    => __( 'unit: px', 'porto' ),
							'default' => '',
						),
						array(
							'id'       => 'header-bottom-text-color',
							'type'     => 'color',
							'title'    => __( 'Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color in the header bottom section.', 'porto' ),
							'default'  => '',
							'validate' => 'color',
						),
						array(
							'id'       => 'header-bottom-link-color',
							'type'     => 'link_color',
							'active'   => false,
							'title'    => __( 'Link Color', 'porto' ),
							'subtitle' => __( 'Controls the color of A tag in the header bottom section.', 'porto' ),
							'default'  => array(
								'regular' => '',
								'hover'   => '',
							),
						),
						array(
							'id'     => 'desc_info_behind_header',
							'type'   => 'info',
							'title'  => __( 'Skin option when banner show behind header', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'header-opacity',
							'type'     => 'text',
							'title'    => __( 'Header Opacity', 'porto' ),
							'subtitle' => __( 'Controls the background opacity in the fixed header.', 'porto' ),
							'default'  => '80%',
						),
						array(
							'id'       => 'searchform-opacity',
							'type'     => 'text',
							'title'    => __( 'Search Form Opacity', 'porto' ),
							'subtitle' => __( 'Controls the search form\'s background opacity in the fixed header.', 'porto' ),
							'default'  => '50%',
						),
						array(
							'id'       => 'menuwrap-opacity',
							'type'     => 'text',
							'title'    => __( 'Menu Wrap Opacity', 'porto' ),
							'subtitle' => __( 'Controls the main menu section\'s background opacity in the fixed header for some header types.', 'porto' ),
							'default'  => '30%',
						),
						array(
							'id'       => 'menu-opacity',
							'type'     => 'text',
							'title'    => __( 'Menu Opacity', 'porto' ),
							'subtitle' => __( 'Controls the main menu\'s background opacity in the fixed header.', 'porto' ),
							'default'  => '30%',
						),
						array(
							'id'       => 'header-fixed-show-bottom',
							'type'     => 'switch',
							'title'    => __( 'Show Bottom Border', 'porto' ),
							'subtitle' => __( 'Controls if show bottom border with opacity in the fixed header.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'     => 'desc_info_side_navigation',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Side Header:</b> If you use <span>header builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'       => 'side-social-bg-color',
							'type'     => 'color',
							'title'    => __( 'Social Link Background Color', 'porto' ),
							'default'  => '#9e9e9e',
							'validate' => 'color',
						),
						array(
							'id'       => 'side-social-color',
							'type'     => 'color',
							'title'    => __( 'Social Link Color', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
						),
						array(
							'id'       => 'side-copyright-color',
							'type'     => 'color',
							'title'    => __( 'Copyright Text Color', 'porto' ),
							'default'  => '#777777',
							'validate' => 'color',
						),
					),
				);
			}
			
			if ( $this->legacy_mode ) {
				// Menu Settings
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon'       => 'Simple-Line-Icons-menu',
						'icon_class' => '',
						'title'      => __( 'Menu', 'porto' ),
						'transport'  => 'postMessage',
						'fields'     => array(
							array(
								'id'    => 'desc_info_menu_notice',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. If you use builder, some options might be overrided by Menu widget.', 'porto' ), $header_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'menu-arrow',
								'type'    => 'switch',
								'title'   => __( 'Show Menu Arrow', 'porto' ),
								'desc'    => __( 'If menu item have sub menus, show menu arrow.', 'porto' ),
								'default' => false,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'      => 'submenu-arrow',
								'type'    => 'switch',
								'title'   => __( 'Show Sub Menu Arrow', 'porto' ),
								'desc'    => __( 'Show top arrow to the sub menu.', 'porto' ),
								'default' => false,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'      => 'menu-type',
								'type'    => 'button_set',
								'title'   => __( 'Main Menu Type', 'porto' ),
								'options' => array(
									''                => __( 'Normal', 'porto' ),
									'menu-flat'       => __( 'Flat', 'porto' ),
									'menu-flat menu-flat-border' => __( 'Flat & Border', 'porto' ),
									'menu-hover-line' => __( 'Top Border on hover', 'porto' ),
									'menu-hover-line menu-hover-underline' => __( 'Thick Underline on hover', 'porto' ),
									'overlay'         => __( 'Popup', 'porto' ),
								),
								'default' => '',
							),
							array(
								'id'      => 'menu-login-pos',
								'type'    => 'button_set',
								'title'   => __( 'Display Login & Register / Logout Link', 'porto' ),
								'options' => array(
									''          => __( 'None', 'porto' ),
									'top_nav'   => __( 'In Top Navigation', 'porto' ),
									'main_menu' => __( 'In Main Menu', 'porto' ),
								),
								'default' => '',
							),
							array(
								'id'       => 'menu-enable-register',
								'type'     => 'switch',
								'title'    => __( 'Show Register Link', 'porto' ),
								'required' => array( 'menu-login-pos', 'equals', array( 'top_nav', 'main_menu' ) ),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'menu-show-login-icon',
								'type'     => 'switch',
								'title'    => __( 'Show Login/Logout Icon', 'porto' ),
								'required' => array( 'menu-login-pos', 'equals', array( 'top_nav', 'main_menu' ) ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'      => 'show-icon-menus-mobile',
								'type'    => 'button_set',
								'title'   => __( 'Show Sticky Icon Menu bar on mobile', 'porto' ),
								'desc'    => __( 'This will show sticky icon menu bar at the bottom of the page on mobile.', 'porto' ),
								'multi'   => true,
								'options' => array(
									'home'     => __( 'Home', 'porto' ),
									'blog'     => __( 'Blog', 'porto' ),
									'shop'     => __( 'Shop', 'porto' ),
									'wishlist' => __( 'Wishlist', 'porto' ),
									'account'  => __( 'Account', 'porto' ),
									'cart'     => __( 'Cart', 'porto' ),
								),
								'default' => array(),
							),
							array(
								'id'       => 'sticky-icon-home',
								'type'     => 'text',
								'title'    => __( 'Home Icon', 'porto' ),
								'default'  => __( 'porto-icon-category-home', 'porto' ),
								'required' => array( 'show-icon-menus-mobile', 'contains', 'home' ),
							),
							array(
								'id'       => 'sticky-icon-blog',
								'type'     => 'text',
								'title'    => __( 'Blog Icon', 'porto' ),
								'default'  => __( 'far fa-calendar-alt', 'porto' ),
								'required' => array( 'show-icon-menus-mobile', 'contains', 'blog' ),
							),
							array(
								'id'       => 'sticky-icon-shop',
								'type'     => 'text',
								'title'    => __( 'Shop Icon', 'porto' ),
								'default'  => __( 'porto-icon-bars', 'porto' ),
								'required' => array( 'show-icon-menus-mobile', 'contains', 'shop' ),
							),
							array(
								'id'       => 'sticky-icon-wishlist',
								'type'     => 'text',
								'title'    => __( 'Wishlist Icon', 'porto' ),
								'default'  => __( 'porto-icon-wishlist-2', 'porto' ),
								'required' => array( 'show-icon-menus-mobile', 'contains', 'wishlist' ),
							),
							array(
								'id'       => 'sticky-icon-account',
								'type'     => 'text',
								'title'    => __( 'Account Icon', 'porto' ),
								'default'  => __( 'porto-icon-user-2', 'porto' ),
								'required' => array( 'show-icon-menus-mobile', 'contains', 'account' ),
							),
							array(
								'id'       => 'sticky-icon-cart',
								'type'     => 'text',
								'title'    => __( 'Cart Icon', 'porto' ),
								'default'  => __( 'porto-icon-shopping-cart', 'porto' ),
								'required' => array( 'show-icon-menus-mobile', 'contains', 'cart' ),
							),
							array(
								'id'     => 'desc_info_side_header',
								'type'   => 'info',
								'title'  => __( 'When using Side header type or showing Main Menu in Sidebar', 'porto' ),
								'notice' => false,
							),
							array(
								'id'        => 'side-menu-type',
								'type'      => 'button_set',
								'title'     => __( 'Sidebar Menu Type', 'porto' ),
								'desc'      => __( 'Controls how to show its submenus.', 'porto' ),
								'options'   => array(
									''          => __( 'Normal', 'porto' ),
									'accordion' => __( 'Accordion Menu', 'porto' ),
									'slide'     => __( 'Horizontal Slide Menu', 'porto' ),
									'columns'   => __( 'Horizontal Columns', 'porto' ),
								),
								'default'   => '',
								'transport' => 'refresh',
							),
							array(
								'id'     => 'desc_info_header_preset1',
								'type'   => 'info',
								'title'  => __( 'If header type is 1, 4, 13, 14 or header builder', 'porto' ),
								'notice' => false,
							),
							array(
								'id'      => 'menu-align',
								'type'    => 'button_set',
								'title'   => __( 'Main Menu Align', 'porto' ),
								'options' => array(
									''         => __( 'Left', 'porto' ),
									'centered' => __( 'Center', 'porto' ),
								),
								'default' => '',
							),
							array(
								'id'        => 'menu-sidebar',
								'type'      => 'switch',
								'title'     => __( 'Show Main Menu in Sidebar', 'porto' ),
								'desc'      => __( 'If the layout of a page is left sidebar or right sidebar, the main menu shows in the sidebar.', 'porto' ),
								'default'   => false,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'refresh',
							),
							array(
								'id'       => 'menu-sidebar-title',
								'type'     => 'text',
								'title'    => __( 'Sidebar Menu Title', 'porto' ),
								'subtitle' => __( 'Input the title of sidebar menu.', 'porto' ),
								'default'  => __( 'All Department', 'porto' ),
							),
							array(
								'id'       => 'menu-sidebar-toggle',
								'type'     => 'switch',
								'title'    => __( 'Toggle Sidebar Menu', 'porto' ),
								'subtitle' => __( 'Add a toggle button of the sidebar menu.', 'porto' ),
								'required' => array( 'menu-sidebar', 'equals', true ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'        => 'menu-sidebar-home',
								'type'      => 'switch',
								'title'     => __( 'Show Main Menu in Sidebar only on Home', 'porto' ),
								'subtitle'  => __( 'You can see sidebar menu only on homepage.', 'porto' ),
								'required'  => array( 'menu-sidebar', 'equals', true ),
								'default'   => true,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'refresh',
							),
							array(
								'id'     => 'desc_info_header_preset2',
								'type'   => 'info',
								'title'  => __( 'If header type is 9 or header builder', 'porto' ),
								'notice' => false,
							),
							array(
								'id'       => 'menu-title',
								'type'     => 'text',
								'title'    => __( 'Main Toggle Menu Title', 'porto' ),
								'subtitle' => __( 'If you use toggle menu like shop 35, please input the title of menu.', 'porto' ),
								'default'  => __( 'All Department', 'porto' ),
							),
							array(
								'id'        => 'menu-toggle-onhome',
								'type'      => 'switch',
								'title'     => __( 'Toggle on home page', 'porto' ),
								'desc'      => __( 'In homepage, a toggle menu is collapsed at first. Then it works as a toggle....', 'porto' ),
								'default'   => false,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'refresh',
							),
							array(
								'id'     => 'desc_info_header_preset3',
								'type'   => 'info',
								'title'  => __( 'If header type is 1, 3, 4, 9, 13, 14 or header builder', 'porto' ),
								'notice' => false,
							),
							array(
								'id'      => 'menu-block',
								'type'    => 'textarea',
								'title'   => __( 'Menu Custom Content', 'porto' ),
								'desc'    => __( 'example: &lt;span&gt;Custom Message&lt;/span&gt;&lt;a href="#"&gt;Special Offer!&lt;/a&gt;&lt;a href="#"&gt;Buy this Theme!&lt;em class="tip hot"&gt;HOT&lt;/em&gt;&lt;/a&gt;', 'porto' ),
								'default' => '',
							),
						),
					),
					$options_style
				);
			} else {
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon'       => 'Simple-Line-Icons-menu',
						'icon_class' => '',
						'title'      => __( 'Menu', 'porto' ),
						'transport'  => 'postMessage',
						'fields'     => array(
							array(
								'id'    => 'desc_info_menu_notice',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Header</a> Builder helps you to develop your site easily. If you use builder, some options might be overrided by Menu widget.', 'porto' ), $header_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'menu-arrow',
								'type'    => 'switch',
								'title'   => __( 'Show Menu Arrow', 'porto' ),
								'desc'    => __( 'If menu item have sub menus, show menu arrow.', 'porto' ),
								'default' => false,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'      => 'submenu-arrow',
								'type'    => 'switch',
								'title'   => __( 'Show Sub Menu Arrow', 'porto' ),
								'desc'    => __( 'Show top arrow to the sub menu.', 'porto' ),
								'default' => false,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'      => 'menu-type',
								'type'    => 'button_set',
								'title'   => __( 'Main Menu Type', 'porto' ),
								'options' => array(
									''                => __( 'Normal', 'porto' ),
									'menu-flat'       => __( 'Flat', 'porto' ),
									'menu-flat menu-flat-border' => __( 'Flat & Border', 'porto' ),
									'menu-hover-line' => __( 'Top Border on hover', 'porto' ),
									'menu-hover-line menu-hover-underline' => __( 'Thick Underline on hover', 'porto' ),
									'overlay'         => __( 'Popup', 'porto' ),
								),
								'default' => '',
							),
							array(
								'id'      => 'menu-login-pos',
								'type'    => 'button_set',
								'title'   => __( 'Display Login & Register / Logout Link', 'porto' ),
								'options' => array(
									''          => __( 'None', 'porto' ),
									'top_nav'   => __( 'In Top Navigation', 'porto' ),
									'main_menu' => __( 'In Main Menu', 'porto' ),
								),
								'default' => '',
							),
							array(
								'id'       => 'menu-enable-register',
								'type'     => 'switch',
								'title'    => __( 'Show Register Link', 'porto' ),
								'required' => array( 'menu-login-pos', 'equals', array( 'top_nav', 'main_menu' ) ),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'menu-show-login-icon',
								'type'     => 'switch',
								'title'    => __( 'Show Login/Logout Icon', 'porto' ),
								'required' => array( 'menu-login-pos', 'equals', array( 'top_nav', 'main_menu' ) ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'      => 'show-icon-menus-mobile',
								'type'    => 'button_set',
								'title'   => __( 'Show Sticky Icon Menu bar on mobile', 'porto' ),
								'desc'    => __( 'This will show sticky icon menu bar at the bottom of the page on mobile.', 'porto' ),
								'multi'   => true,
								'options' => array(
									'home'     => __( 'Home', 'porto' ),
									'blog'     => __( 'Blog', 'porto' ),
									'shop'     => __( 'Shop', 'porto' ),
									'wishlist' => __( 'Wishlist', 'porto' ),
									'account'  => __( 'Account', 'porto' ),
									'cart'     => __( 'Cart', 'porto' ),
								),
								'default' => array(),
							),
							array(
								'id'       => 'sticky-icon-home',
								'type'     => 'text',
								'title'    => __( 'Home Icon', 'porto' ),
								'default'  => __( 'porto-icon-category-home', 'porto' ),
								'required' => array( 'show-icon-menus-mobile', 'contains', 'home' ),
							),
							array(
								'id'       => 'sticky-icon-blog',
								'type'     => 'text',
								'title'    => __( 'Blog Icon', 'porto' ),
								'default'  => __( 'far fa-calendar-alt', 'porto' ),
								'required' => array( 'show-icon-menus-mobile', 'contains', 'blog' ),
							),
							array(
								'id'       => 'sticky-icon-shop',
								'type'     => 'text',
								'title'    => __( 'Shop Icon', 'porto' ),
								'default'  => __( 'porto-icon-bars', 'porto' ),
								'required' => array( 'show-icon-menus-mobile', 'contains', 'shop' ),
							),
							array(
								'id'       => 'sticky-icon-wishlist',
								'type'     => 'text',
								'title'    => __( 'Wishlist Icon', 'porto' ),
								'default'  => __( 'porto-icon-wishlist-2', 'porto' ),
								'required' => array( 'show-icon-menus-mobile', 'contains', 'wishlist' ),
							),
							array(
								'id'       => 'sticky-icon-account',
								'type'     => 'text',
								'title'    => __( 'Account Icon', 'porto' ),
								'default'  => __( 'porto-icon-user-2', 'porto' ),
								'required' => array( 'show-icon-menus-mobile', 'contains', 'account' ),
							),
							array(
								'id'       => 'sticky-icon-cart',
								'type'     => 'text',
								'title'    => __( 'Cart Icon', 'porto' ),
								'default'  => __( 'porto-icon-shopping-cart', 'porto' ),
								'required' => array( 'show-icon-menus-mobile', 'contains', 'cart' ),
							),
							array(
								'id'     => 'desc_info_side_header',
								'type'   => 'info',
								'title'  => __( 'When using Side header type or showing Main Menu in Sidebar', 'porto' ),
								'notice' => false,
							),
							array(
								'id'        => 'side-menu-type',
								'type'      => 'button_set',
								'title'     => __( 'Sidebar Menu Type', 'porto' ),
								'desc'      => __( 'Controls how to show its submenus.', 'porto' ),
								'options'   => array(
									''          => __( 'Normal', 'porto' ),
									'accordion' => __( 'Accordion Menu', 'porto' ),
									'slide'     => __( 'Horizontal Slide Menu', 'porto' ),
									'columns'   => __( 'Horizontal Columns', 'porto' ),
								),
								'default'   => '',
								'transport' => 'refresh',
							),
							array(
								'id'        => 'menu-sidebar',
								'type'      => 'switch',
								'title'     => __( 'Show Main Menu in Sidebar', 'porto' ),
								'desc'      => __( 'If the layout of a page is left sidebar or right sidebar, the main menu shows in the sidebar.', 'porto' ),
								'default'   => false,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'refresh',
							),
							array(
								'id'       => 'menu-sidebar-title',
								'type'     => 'text',
								'title'    => __( 'Sidebar Menu Title', 'porto' ),
								'subtitle' => __( 'Input the title of sidebar menu.', 'porto' ),
								'default'  => __( 'All Department', 'porto' ),
							),
							array(
								'id'       => 'menu-sidebar-toggle',
								'type'     => 'switch',
								'title'    => __( 'Toggle Sidebar Menu', 'porto' ),
								'subtitle' => __( 'Add a toggle button of the sidebar menu.', 'porto' ),
								'required' => array( 'menu-sidebar', 'equals', true ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'        => 'menu-sidebar-home',
								'type'      => 'switch',
								'title'     => __( 'Show Main Menu in Sidebar only on Home', 'porto' ),
								'subtitle'  => __( 'You can see sidebar menu only on homepage.', 'porto' ),
								'required'  => array( 'menu-sidebar', 'equals', true ),
								'default'   => true,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'refresh',
							),
							array(
								'id'        => 'menu-toggle-onhome',
								'type'      => 'switch',
								'title'     => __( 'Toggle on home page', 'porto' ),
								'desc'      => __( 'In homepage, a toggle menu is collapsed at first. Then it works as a toggle....', 'porto' ),
								'default'   => false,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'refresh',
							),
							array(
								'id'       => 'menu-title',
								'type'     => 'text',
								'title'    => __( 'Main Toggle Menu Title', 'porto' ),
								'subtitle' => __( 'If you use toggle menu like shop 35, please input the title of menu.', 'porto' ),
								'default'  => __( 'All Department', 'porto' ),
							),
						),
					),
					$options_style
				);
			}

			if ( $this->legacy_mode ) {
				$this->sections[] = array(
					'id'         => 'skin-main-menu',
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Menu Styling', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'       => 'mainmenu-wrap-bg-color',
							'type'     => 'color',
							'title'    => __( 'Main Menu Wrapper Background Color', 'porto' ),
							'subtitle' => __( 'if header type is 1, 4, 9, 13, 14, 17 or header builder which contains main menu in header bottom section.', 'porto' ),
							'default'  => 'transparent',
							'validate' => 'color',
						),
						array(
							'id'       => 'mainmenu-wrap-bg-color-sticky',
							'type'     => 'color',
							'title'    => __( 'Main Menu Wrapper Background Color in Sticky Header', 'porto' ),
							'subtitle' => __( 'if header type is 1, 4, 9, 13, 14, 17 or header builder which contains main menu in header bottom section.', 'porto' ),
							'validate' => 'color',
						),
						array(
							'id'       => 'mainmenu-wrap-padding',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Menu Menu Wrapper Padding', 'porto' ),
							'subtitle' => __( 'if header type is 1, 4, 9, 13, 14, 17 or header builder which contains main menu in header bottom section.', 'porto' ),
							'default'  => array(
								'padding-top'    => 0,
								'padding-bottom' => 0,
								'padding-left'   => 0,
								'padding-right'  => 0,
							),
						),
						array(
							'id'       => 'mainmenu-bg-color',
							'type'     => 'color',
							'title'    => __( 'Main Menu Background Color', 'porto' ),
							'subtitle' => __( 'Controls the background color for main menu.', 'porto' ),
							'default'  => 'transparent',
							'validate' => 'color',
						),
						array(
							'id'     => 'desc_info_top_level',
							'type'   => 'info',
							'title'  => __( 'Top Level Menu Item', 'porto' ),
							'notice' => false,
						),
						array(
							'id'             => 'menu-font',
							'type'           => 'typography',
							'title'          => __( 'Menu Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for main menu\'s first level items.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'color'          => false,
							'letter-spacing' => true,
							'default'        => array(
								'google'         => true,
								'font-weight'    => '700',
								'font-family'    => 'Open Sans',
								'font-size'      => '12px',
								'line-height'    => '20px',
								'letter-spacing' => '0',
							),
							'transport'      => 'refresh',
						),
						array(
							'id'             => 'menu-side-font',
							'type'           => 'typography',
							'title'          => __( 'Side Menu Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for main sidebar menu\'s first level items.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'color'          => false,
							'letter-spacing' => true,
							'default'        => array(
								'google'         => true,
								'font-weight'    => '400',
								'font-family'    => 'Open Sans',
								'font-size'      => '14px',
								'line-height'    => '18px',
								'letter-spacing' => '0',
							),
							'selector'       => array(
								'node' => '.main-sidebar-menu',
							),
						),
						array(
							'id'       => 'menu-text-transform',
							'type'     => 'button_set',
							'title'    => __( 'Text Transform', 'porto' ),
							'subtitle' => __( 'Controls the text transform for the first level items of main menu and main sidebar menu.', 'porto' ),
							'options'  => array(
								'none'       => __( 'None', 'porto' ),
								'capitalize' => __( 'Capitalize', 'porto' ),
								'uppercase'  => __( 'Uppercase', 'porto' ),
								'lowercase'  => __( 'Lowercase', 'porto' ),
								'initial'    => __( 'Initial', 'porto' ),
							),
							'default'  => 'uppercase',
							'selector' => array(
								'node' => ':root',
							),
						),
						array(
							'id'       => 'mainmenu-toplevel-link-color',
							'type'     => 'link_color',
							'active'   => false,
							'title'    => __( 'Link Color', 'porto' ),
							'subtitle' => __( 'Controls the menu item color for the first level items of main menu and main sidebar menu.', 'porto' ),
							'default'  => array(
								'regular' => '#0088cc',
								'hover'   => '#ffffff',
							),
						),
						array(
							'id'       => 'mainmenu-toplevel-link-color-sticky',
							'type'     => 'link_color',
							'active'   => true,
							'title'    => __( 'Link Color in Sticky Header', 'porto' ),
							'subtitle' => __( 'Controls the menu item color for the first level items of main menu in sticky header.', 'porto' ),
						),
						array(
							'id'       => 'mainmenu-toplevel-hbg-color',
							'type'     => 'color',
							'title'    => __( 'Hover Background Color', 'porto' ),
							'subtitle' => __( 'Controls the background color for the first level items on hover and active.', 'porto' ),
							'default'  => '#0088cc',
							'validate' => 'color',
						),
						array(
							'id'       => 'mainmenu-toplevel-config-active',
							'type'     => 'switch',
							'title'    => __( 'Configure Active Color', 'porto' ),
							'subtitle' => __( 'Controls the background and color for the first level active menu items.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'mainmenu-toplevel-alink-color',
							'type'     => 'color',
							'title'    => __( 'Active Link Color', 'porto' ),
							'required' => array( 'mainmenu-toplevel-config-active', 'equals', true ),
							'default'  => '#ffffff',
							'validate' => 'color',
						),
						array(
							'id'       => 'mainmenu-toplevel-abg-color',
							'type'     => 'color',
							'title'    => __( 'Active Background Color', 'porto' ),
							'required' => array( 'mainmenu-toplevel-config-active', 'equals', true ),
							'default'  => '#0088cc',
							'validate' => 'color',
						),
						array(
							'id'       => 'mainmenu-toplevel-padding1',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Padding on Desktop', 'porto' ),
							'subtitle' => __( 'Controls the padding for the first level menu items on desktop.', 'porto' ),
							'desc'     => __( 'This is not working for sidebar menus.', 'porto' ),
							'default'  => array(
								'padding-top'    => 10,
								'padding-bottom' => 10,
								'padding-left'   => 16,
								'padding-right'  => 16,
							),
						),
						array(
							'id'       => 'mainmenu-toplevel-padding2',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Padding on Desktop (width > 991px)', 'porto' ),
							'subtitle' => __( 'Controls the padding for the first level menu items on small desktop.', 'porto' ),
							'desc'     => __( 'This is not working for sidebar menus.', 'porto' ),
							'default'  => array(
								'padding-top'    => 9,
								'padding-bottom' => 9,
								'padding-left'   => 14,
								'padding-right'  => 14,
							),
						),
						array(
							'id'       => 'mainmenu-toplevel-padding3',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Padding on Sticky Header (width > 991px)', 'porto' ),
							'subtitle' => __( 'Controls the padding for the first level menu items in sticky header on large displays.', 'porto' ),
							'desc'     => __( 'This is not working for sidebar menus. Please leave blank if you use same values with the ones in default header.', 'porto' ),
						),
						array(
							'id'     => 'desc_info_menu_popup',
							'type'   => 'info',
							'title'  => __( 'Menu Popup', 'porto' ),
							'notice' => false,
						),
						array(
							'id'             => 'menu-popup-font',
							'type'           => 'typography',
							'title'          => __( 'Menu Popup Font', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'color'          => false,
							'letter-spacing' => true,
							'default'        => array(
								'google'         => true,
								'font-weight'    => '400',
								'font-family'    => 'Open Sans',
								'font-size'      => '14px',
								'line-height'    => '24px',
								'letter-spacing' => '0',
							),
							'transport'      => 'refresh',
						),
						array(
							'id'      => 'menu-popup-text-transform',
							'type'    => 'button_set',
							'title'   => __( 'Text Transform', 'porto' ),
							'options' => array(
								'none'       => __( 'None', 'porto' ),
								'capitalize' => __( 'Capitalize', 'porto' ),
								'uppercase'  => __( 'Uppercase', 'porto' ),
								'lowercase'  => __( 'Lowercase', 'porto' ),
								'initial'    => __( 'Initial', 'porto' ),
							),
							'default' => 'none',
						),
						array(
							'id'      => 'mainmenu-popup-top-border',
							'type'    => 'border',
							'all'     => false,
							'style'   => false,
							'left'    => false,
							'right'   => false,
							'bottom'  => false,
							'title'   => __( 'Top Border', 'porto' ),
							'default' => $mainmenu_popup_top_border,
						),
						array(
							'id'       => 'mainmenu-popup-bg-color',
							'type'     => 'color',
							'title'    => __( 'Background Color', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
						),
						array(
							'id'       => 'mainmenu-popup-heading-color',
							'type'     => 'color',
							'title'    => __( 'Heading Color', 'porto' ),
							'subtitle' => __( 'Controls the color of sub titles in the mega menu (wide menu).', 'porto' ),
							'default'  => '#333333',
							'validate' => 'color',
						),
						array(
							'id'       => 'mainmenu-popup-text-color',
							'type'     => 'link_color',
							'active'   => false,
							'title'    => __( 'Link Color', 'porto' ),
							'default'  => array(
								'regular' => '#777777',
								'hover'   => '#777777',
							),
							'selector' => array(
								'node' => 'li.menu-item, .sub-menu',
							),
						),
						array(
							'id'       => 'mainmenu-popup-text-hbg-color',
							'type'     => 'color',
							'title'    => __( 'Link Hover Background Color', 'porto' ),
							'default'  => '#f4f4f4',
							'validate' => 'color',
							'selector' => array(
								'node' => 'li.menu-item',
							),
						),
						array(
							'id'       => 'mainmenu-popup-narrow-type',
							'type'     => 'button_set',
							'title'    => __( 'Narrow Menu Style', 'porto' ),
							'subtitle' => __( 'Controls the background color style for the narrow sub menus (menu popup).', 'porto' ),
							'desc'     => __( 'If you select "With Top Menu Hover Bg Color", please insert hover background color for the first level items in the "Top Level Menu Item / Hover Background Color".', 'porto' ),
							'options'  => array(
								''  => __( 'With Popup BG Color', 'porto' ),
								'1' => __( 'With Top Menu Hover Bg Color', 'porto' ),
							),
							'default'  => '',
						),
						array(
							'id'     => 'desc_info_tip',
							'type'   => 'info',
							'title'  => __( 'Tip', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'mainmenu-tip-bg-color',
							'type'     => 'color',
							'title'    => __( 'Tip Background Color', 'porto' ),
							'subtitle' => __( 'Controls the background color for the tip labels in the main menu item.', 'porto' ),
							'default'  => '#0cc485',
							'validate' => 'color',
						),
						array(
							'id'     => 'desc_info_menu_custom',
							'type'   => 'info',
							'title'  => __( 'Menu Custom Content (if header type is 1, 3, 4, 9, 13, 14 or header builder)', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'menu-custom-text-color',
							'type'     => 'color',
							'title'    => __( 'Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color for the menu custom content which is inserted in Header / Menu Custom Content', 'porto' ),
							'default'  => '#777777',
							'validate' => 'color',
						),
						array(
							'id'       => 'menu-custom-link',
							'type'     => 'link_color',
							'title'    => __( 'Link Color', 'porto' ),
							'subtitle' => __( 'Controls the color of A tag for the menu custom content which is inserted in Header / Menu Custom Content', 'porto' ),
							'active'   => false,
							'default'  => array(
								'regular' => '#0088cc',
								'hover'   => '#006fa4',
							),
						),
					),
				);
			} else {
				$this->sections[] = array(
					'id'         => 'skin-main-menu',
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Menu Styling', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'     => 'desc_info_top_level',
							'type'   => 'info',
							'title'  => __( 'Top Level Menu Item', 'porto' ),
							'notice' => false,
						),
						array(
							'id'             => 'menu-font',
							'type'           => 'typography',
							'title'          => __( 'Menu Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for main menu\'s first level items.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'color'          => false,
							'letter-spacing' => true,
							'default'        => array(
								'google'         => true,
								'font-weight'    => '700',
								'font-family'    => 'Open Sans',
								'font-size'      => '12px',
								'line-height'    => '20px',
								'letter-spacing' => '0',
							),
							'transport'      => 'refresh',
						),
						array(
							'id'             => 'menu-side-font',
							'type'           => 'typography',
							'title'          => __( 'Side Menu Font', 'porto' ),
							'subtitle'       => __( 'Controls the typography for main sidebar menu\'s first level items.', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'color'          => false,
							'letter-spacing' => true,
							'default'        => array(
								'google'         => true,
								'font-weight'    => '400',
								'font-family'    => 'Open Sans',
								'font-size'      => '14px',
								'line-height'    => '18px',
								'letter-spacing' => '0',
							),
							'transport'      => 'refresh',
							'selector'       => array(
								'node' => '.main-sidebar-menu',
							),
						),
						array(
							'id'       => 'menu-text-transform',
							'type'     => 'button_set',
							'title'    => __( 'Text Transform', 'porto' ),
							'subtitle' => __( 'Controls the text transform for the first level items of main menu and main sidebar menu.', 'porto' ),
							'options'  => array(
								'none'       => __( 'None', 'porto' ),
								'capitalize' => __( 'Capitalize', 'porto' ),
								'uppercase'  => __( 'Uppercase', 'porto' ),
								'lowercase'  => __( 'Lowercase', 'porto' ),
								'initial'    => __( 'Initial', 'porto' ),
							),
							'default'  => 'uppercase',
							'selector' => array(
								'node' => ':root',
							),
						),
						array(
							'id'       => 'mainmenu-toplevel-link-color',
							'type'     => 'link_color',
							'active'   => false,
							'title'    => __( 'Link Color', 'porto' ),
							'subtitle' => __( 'Controls the menu item color for the first level items of main menu and main sidebar menu.', 'porto' ),
							'default'  => array(
								'regular' => '#0088cc',
								'hover'   => '#ffffff',
							),
						),
						array(
							'id'       => 'mainmenu-toplevel-link-color-sticky',
							'type'     => 'link_color',
							'active'   => true,
							'title'    => __( 'Link Color in Sticky Header', 'porto' ),
							'subtitle' => __( 'Controls the menu item color for the first level items of main menu in sticky header.', 'porto' ),
						),
						array(
							'id'       => 'mainmenu-toplevel-hbg-color',
							'type'     => 'color',
							'title'    => __( 'Hover Background Color', 'porto' ),
							'subtitle' => __( 'Controls the background color for the first level items on hover and active.', 'porto' ),
							'default'  => '#0088cc',
							'validate' => 'color',
						),
						array(
							'id'       => 'mainmenu-toplevel-config-active',
							'type'     => 'switch',
							'title'    => __( 'Configure Active Color', 'porto' ),
							'subtitle' => __( 'Controls the background and color for the first level active menu items.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'mainmenu-toplevel-alink-color',
							'type'     => 'color',
							'title'    => __( 'Active Link Color', 'porto' ),
							'required' => array( 'mainmenu-toplevel-config-active', 'equals', true ),
							'default'  => '#ffffff',
							'validate' => 'color',
						),
						array(
							'id'       => 'mainmenu-toplevel-abg-color',
							'type'     => 'color',
							'title'    => __( 'Active Background Color', 'porto' ),
							'required' => array( 'mainmenu-toplevel-config-active', 'equals', true ),
							'default'  => '#0088cc',
							'validate' => 'color',
						),
						array(
							'id'       => 'mainmenu-toplevel-padding1',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Padding on Desktop', 'porto' ),
							'subtitle' => __( 'Controls the padding for the first level menu items on desktop.', 'porto' ),
							'desc'     => __( 'This is not working for sidebar menus.', 'porto' ),
							'default'  => array(
								'padding-top'    => 10,
								'padding-bottom' => 10,
								'padding-left'   => 16,
								'padding-right'  => 16,
							),
						),
						array(
							'id'       => 'mainmenu-toplevel-padding2',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Padding on Desktop (width > 991px)', 'porto' ),
							'subtitle' => __( 'Controls the padding for the first level menu items on small desktop.', 'porto' ),
							'desc'     => __( 'This is not working for sidebar menus.', 'porto' ),
							'default'  => array(
								'padding-top'    => 9,
								'padding-bottom' => 9,
								'padding-left'   => 14,
								'padding-right'  => 14,
							),
						),
						array(
							'id'       => 'mainmenu-toplevel-padding3',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Padding on Sticky Header (width > 991px)', 'porto' ),
							'subtitle' => __( 'Controls the padding for the first level menu items in sticky header on large displays.', 'porto' ),
							'desc'     => __( 'This is not working for sidebar menus. Please leave blank if you use same values with the ones in default header.', 'porto' ),
						),
						array(
							'id'     => 'desc_info_menu_popup',
							'type'   => 'info',
							'title'  => __( 'Menu Popup', 'porto' ),
							'notice' => false,
						),
						array(
							'id'             => 'menu-popup-font',
							'type'           => 'typography',
							'title'          => __( 'Menu Popup Font', 'porto' ),
							'google'         => true,
							'subsets'        => false,
							'font-style'     => false,
							'text-align'     => false,
							'color'          => false,
							'letter-spacing' => true,
							'default'        => array(
								'google'         => true,
								'font-weight'    => '400',
								'font-family'    => 'Open Sans',
								'font-size'      => '14px',
								'line-height'    => '24px',
								'letter-spacing' => '0',
							),
							'transport'      => 'refresh',
						),
						array(
							'id'      => 'menu-popup-text-transform',
							'type'    => 'button_set',
							'title'   => __( 'Text Transform', 'porto' ),
							'options' => array(
								'none'       => __( 'None', 'porto' ),
								'capitalize' => __( 'Capitalize', 'porto' ),
								'uppercase'  => __( 'Uppercase', 'porto' ),
								'lowercase'  => __( 'Lowercase', 'porto' ),
								'initial'    => __( 'Initial', 'porto' ),
							),
							'default' => 'none',
						),
						array(
							'id'      => 'mainmenu-popup-top-border',
							'type'    => 'border',
							'all'     => false,
							'style'   => false,
							'left'    => false,
							'right'   => false,
							'bottom'  => false,
							'title'   => __( 'Top Border', 'porto' ),
							'default' => $mainmenu_popup_top_border,
						),
						array(
							'id'       => 'mainmenu-popup-bg-color',
							'type'     => 'color',
							'title'    => __( 'Background Color', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
						),
						array(
							'id'       => 'mainmenu-popup-heading-color',
							'type'     => 'color',
							'title'    => __( 'Heading Color', 'porto' ),
							'subtitle' => __( 'Controls the color of sub titles in the mega menu (wide menu).', 'porto' ),
							'default'  => '#333333',
							'validate' => 'color',
						),
						array(
							'id'       => 'mainmenu-popup-text-color',
							'type'     => 'link_color',
							'active'   => false,
							'title'    => __( 'Link Color', 'porto' ),
							'default'  => array(
								'regular' => '#777777',
								'hover'   => '#777777',
							),
							'selector' => array(
								'node' => 'li.menu-item, .sub-menu',
							),
						),
						array(
							'id'       => 'mainmenu-popup-text-hbg-color',
							'type'     => 'color',
							'title'    => __( 'Link Hover Background Color', 'porto' ),
							'default'  => '#f4f4f4',
							'validate' => 'color',
						),
						array(
							'id'       => 'mainmenu-popup-narrow-type',
							'type'     => 'button_set',
							'title'    => __( 'Narrow Menu Style', 'porto' ),
							'subtitle' => __( 'Controls the background color style for the narrow sub menus (menu popup).', 'porto' ),
							'desc'     => __( 'If you select "With Top Menu Hover Bg Color", please insert hover background color for the first level items in the "Top Level Menu Item / Hover Background Color".', 'porto' ),
							'options'  => array(
								''  => __( 'With Popup BG Color', 'porto' ),
								'1' => __( 'With Top Menu Hover Bg Color', 'porto' ),
							),
							'default'  => '',
						),
						array(
							'id'     => 'desc_info_tip',
							'type'   => 'info',
							'title'  => __( 'Tip', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'mainmenu-tip-bg-color',
							'type'     => 'color',
							'title'    => __( 'Tip Background Color', 'porto' ),
							'subtitle' => __( 'Controls the background color for the tip labels in the main menu item.', 'porto' ),
							'default'  => '#0cc485',
							'validate' => 'color',
						),
					),
				);
			}

			$this->sections[] = array(
				'id'         => 'mobile-panel-settings',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Mobile Menu', 'porto' ),
				'fields'     => array(
					array(
						'id'      => 'mobile-panel-type',
						'type'    => 'button_set',
						'title'   => __( 'Mobile Panel Type', 'porto' ),
						'desc'    => __( 'Controls the panel type of mobile toggle menu.', 'porto' ),
						'options' => array(
							''     => __( 'Default', 'porto' ),
							'side' => __( 'Side Navigation', 'porto' ),
							'none' => __( 'None', 'porto' ),
						),
						'default' => '',
					),
					array(
						'id'        => 'mobile-panel-pos',
						'type'      => 'button_set',
						'title'     => __( 'Position', 'porto' ),
						'desc'      => __( 'Controls the position of mobile offcanvas menu.', 'porto' ),
						'options'   => array(
							''            => __( 'Default', 'porto' ),
							'panel-left'  => __( 'Left', 'porto' ),
							'panel-right' => __( 'Right', 'porto' ),
						),
						'default'   => '',
						'required'  => array( 'mobile-panel-type', 'equals', array( 'side' ) ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'mobile-panel-add-switcher',
						'type'      => 'switch',
						'title'     => __( 'Add Language, Currency Switcher', 'porto' ),
						'desc'      => __( 'Determines whether to put the switchers in the mobile menu.', 'porto' ),
						'required'  => array( 'mobile-panel-type', 'equals', array( '', 'side' ) ),
						'default'   => false,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'mobile-panel-add-search',
						'type'      => 'switch',
						'title'     => __( 'Add Search Box', 'porto' ),
						'desc'      => __( 'Determines whether to put a search box in the mobile menu.', 'porto' ),
						'required'  => array( 'mobile-panel-type', 'equals', array( 'side' ) ),
						'default'   => false,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'     => 'desc_info_mobile_menu_toggle',
						'type'   => 'info',
						'title'  => __( 'Mobile Menu Toggle', 'porto' ),
						'notice' => false,
					),
					array(
						'id'       => 'mobile-menu-toggle-text-color',
						'type'     => 'color',
						'title'    => __( 'Toggle Icon Color', 'porto' ),
						'desc'     => __( 'Controls the color of mobile toggle icon.', 'porto' ),
						'default'  => '#fff',
						'validate' => 'color',
					),
					array(
						'id'       => 'mobile-menu-toggle-bg-color',
						'type'     => 'color',
						'title'    => __( 'Background Color', 'porto' ),
						'desc'     => __( 'Controls the background color of mobile toggle icon.', 'porto' ),
						'validate' => 'color',
					),
					array(
						'id'     => 'desc_info_mobile_menu_panel',
						'type'   => 'info',
						'title'  => __( 'Mobile Menu Panel', 'porto' ),
						'notice' => false,
					),
					array(
						'id'       => 'panel-bg-color',
						'type'     => 'color',
						'title'    => __( 'Background Color', 'porto' ),
						'desc'     => __( 'Controls the background color of mobile offcanvas or dropdown.', 'porto' ),
						'validate' => 'color',
						'default'  => '#ffffff',
					),
					array(
						'id'       => 'panel-border-color',
						'type'     => 'color',
						'title'    => __( 'Border Color', 'porto' ),
						'desc'     => __( 'Controls the divider color of mobile offcanvas or dropdown.', 'porto' ),
						'default'  => '#e8e8e8',
						'validate' => 'color',
					),
					array(
						'id'       => 'panel-link-hbgcolor',
						'type'     => 'color',
						'title'    => __( 'Hover Background Color', 'porto' ),
						'desc'     => __( 'Controls the hover / active background color of mobile menu item.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'panel-text-color',
						'type'     => 'color',
						'title'    => __( 'Text Color', 'porto' ),
						'desc'     => __( 'Controls the text color in mobile panel.', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'      => 'panel-link-color',
						'type'    => 'link_color',
						'active'  => false,
						'title'   => __( 'Link Color', 'porto' ),
						'desc'    => __( 'Controls the link color in mobile panel.', 'porto' ),
						'default' => array(
							'regular' => '',
							'hover'   => '',
						),
					),
				),
			);

			// Logo
			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'       => 'Simple-Line-Icons-plus',
					'icon_class' => '',
					'title'      => __( 'Logo', 'porto' ),
					'id'         => 'logo-icons',
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'       => 'logo',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Logo', 'porto' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/logo/logo_black.png',
							),
						),
						array(
							'id'       => 'logo-retina',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Retina Logo', 'porto' ),
							'desc'     => __( 'It will be displayed for only retina displays which pixel ratio is greater than one.', 'porto' ),
						),
						array(
							'id'       => 'sticky-logo',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Logo in Sticky Header', 'porto' ),
						),
						array(
							'id'       => 'sticky-logo-retina',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Retina Logo in Sticky Header', 'porto' ),
							'desc'     => __( 'It will be displayed for only retina displays which pixel ratio is greater than one.', 'porto' ),
						),
						array(
							'id'    => 'logo-retina-width',
							'type'  => 'text',
							'title' => __( 'Default Logo Width', 'porto' ),
							'desc'  => __( 'If retina logo is uploaded, please input the default logo width. unit: px', 'porto' ),
						),
						array(
							'id'    => 'logo-retina-height',
							'type'  => 'text',
							'title' => __( 'Default Logo Height', 'porto' ),
							'desc'  => __( 'If retina logo is uploaded, please input the default logo height. unit: px', 'porto' ),
						),
						array(
							'id'      => 'logo-width',
							'type'    => 'text',
							'title'   => __( 'Logo Max Width', 'porto' ),
							'desc'    => __( 'unit: px', 'porto' ),
							'default' => '170',
						),
						array(
							'id'      => 'logo-width-wide',
							'type'    => 'text',
							'title'   => __( 'Logo Max Width on Wide Screen', 'porto' ),
							'default' => '250',
						),
						array(
							'id'      => 'logo-width-tablet',
							'type'    => 'text',
							'title'   => __( 'Logo Max Width on Tablet', 'porto' ),
							'default' => '110',
						),
						array(
							'id'      => 'logo-width-mobile',
							'type'    => 'text',
							'title'   => __( 'Logo Max Width on Mobile', 'porto' ),
							'default' => '110',
						),
						array(
							'id'      => 'logo-width-sticky',
							'type'    => 'text',
							'title'   => __( 'Logo Max Width in Sticky Header', 'porto' ),
							'default' => '80',
						),
						array(
							'id'     => 'desc_info_logo_overlay',
							'type'   => 'info',
							'title'  => __( 'Logo Overlay', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'logo-overlay',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Logo Overlay', 'porto' ),
						),
						array(
							'id'      => 'logo-overlay-width',
							'type'    => 'text',
							'title'   => __( 'Logo Overlay Max Width', 'porto' ),
							'default' => '250',
						),
						array(
							'id'     => 'desc_info_favicon',
							'type'   => 'info',
							'title'  => __( 'Icons', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'favicon',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Favicon', 'porto' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/logo/favicon.png',
							),
						),
						array(
							'id'       => 'icon-iphone',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Apple iPhone Icon', 'porto' ),
							'desc'     => __( 'Icon for Apple iPhone (60px X 60px)', 'porto' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/logo/apple-touch-icon.png',
							),
						),
						array(
							'id'       => 'icon-iphone-retina',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Apple iPhone Retina Icon', 'porto' ),
							'desc'     => __( 'Icon for Apple iPhone Retina (120px X 120px)', 'porto' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/logo/apple-touch-icon_120x120.png',
							),
						),
						array(
							'id'       => 'icon-ipad',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Apple iPad Icon', 'porto' ),
							'desc'     => __( 'Icon for Apple iPad (76px X 76px)', 'porto' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/logo/apple-touch-icon_76x76.png',
							),
						),
						array(
							'id'       => 'icon-ipad-retina',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Apple iPad Retina Icon', 'porto' ),
							'desc'     => __( 'Icon for Apple iPad Retina (152px X 152px)', 'porto' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/logo/apple-touch-icon_152x152.png',
							),
						),
					),
				),
				$options_style
			);

			// Breadcrumbs Settings
			$this->sections[] = $this->add_customizer_field(
				array(
					'id'         => 'header-breadcrumb',
					'icon'       => 'Simple-Line-Icons-link',
					'icon_class' => '',
					'title'      => __( 'Breadcrumbs', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'    => 'desc_info_bredcrumb',
							'type'  => 'info',
							'desc'  => wp_kses(
								__( '<strong>Important Note:</strong> Some below options might be overrided because the priority of the <b>Page Header</b> widget option is <b>higher</b>.', 'porto' ),
								array(
									'strong' => array(),
									'b'      => array(),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'         => 'breadcrumbs-type',
							'type'       => 'image_select',
							'full_width' => true,
							'title'      => __( 'Breadcrumbs Type', 'porto' ),
							'options'    => $porto_breadcrumbs_type,
							'default'    => '1',
						),
						array(
							'id'       => 'show-pagetitle',
							'type'     => 'switch',
							'title'    => __( 'Show Page Title', 'porto' ),
							'subtitle' => __( 'Please select "YES" to show the page title in the breacrumb.', 'porto' ),
							'default'  => true,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'pagetitle-archives',
							'type'     => 'switch',
							'title'    => __( 'Show Content Type Name in Singular', 'porto' ),
							'subtitle' => __( 'Show Content Type Name in the breadcrumb of single content type.', 'porto' ),
							'default'  => false,
							'required' => array( 'show-pagetitle', 'equals', '1' ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'pagetitle-parent',
							'type'     => 'switch',
							'title'    => __( 'Show Parent Page Title in Page', 'porto' ),
							'subtitle' => __( 'Show Parent Page title in the breadcrumb of single page.', 'porto' ),
							'default'  => false,
							'required' => array( 'show-pagetitle', 'equals', '1' ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'     => 'desc_info_breadcrumb',
							'type'   => 'info',
							'title'  => __( 'Breadcrumb Path', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'show-breadcrumbs',
							'type'     => 'switch',
							'title'    => __( 'Show Breadcrumbs', 'porto' ),
							'subtitle' => __( 'Please select "YES" to display the breadcrumb path.', 'porto' ),
							'default'  => true,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'        => 'breadcrumbs-pos',
							'type'      => 'button_set',
							'title'     => __( 'Breadcrumbs Position', 'porto' ),
							'desc'      => __( '"Default" is the below of header and "Inner Top" is the top position of main content.', 'porto' ),
							'required'  => array( 'show-breadcrumbs', 'equals', '1' ),
							'options'   => array(
								''          => __( 'Default', 'porto' ),
								'inner_top' => __( 'Inner Top', 'porto' ),
							),
							'default'   => '',
							'transport' => 'refresh',
						),
						array(
							'id'       => 'breadcrumbs-prefix',
							'type'     => 'text',
							'title'    => __( 'Breadcrumbs Prefix', 'porto' ),
							'subtitle' => __( 'Input the text before the breadcrumb path.', 'porto' ),
							'desc'     => __( 'It will be appeared on the top of path.', 'porto' ),
							'required' => array( 'show-breadcrumbs', 'equals', '1' ),
						),
						array(
							'id'       => 'breadcrumbs-blog-link',
							'type'     => 'switch',
							'title'    => __( 'Show Blog Link', 'porto' ),
							'subtitle' => __( 'Please select "YES" to insert the permalink of the blog page in single post page.', 'porto' ),
							'default'  => true,
							'required' => array( 'show-breadcrumbs', 'equals', '1' ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'breadcrumbs-shop-link',
							'type'     => 'switch',
							'title'    => __( 'Show Shop Link', 'porto' ),
							'subtitle' => __( 'Please select "YES" to insert permalink of shop page to breadcrumb path in single product page.', 'porto' ),
							'default'  => true,
							'required' => array( 'show-breadcrumbs', 'equals', '1' ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'breadcrumbs-archives-link',
							'type'     => 'switch',
							'title'    => __( 'Show Custom Post Type Archives Link', 'porto' ),
							'subtitle' => __( 'Please select "YES" to insert the permalink of "Archive Page" to breadcrumb path in single custom post page.', 'porto' ),
							'default'  => true,
							'required' => array( 'show-breadcrumbs', 'equals', '1' ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'breadcrumbs-categories',
							'type'     => 'switch',
							'title'    => __( 'Show Categories Link', 'porto' ),
							'subtitle' => __( 'Please select "YES" to display the categories in single page.', 'porto' ),
							'default'  => true,
							'required' => array( 'show-breadcrumbs', 'equals', '1' ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'breadcrumbs-delimiter',
							'type'     => 'button_set',
							'title'    => __( 'Breadcrumbs Delimiter', 'porto' ),
							'subtitle' => __( 'Select the type of separator between each breadcrumb.', 'porto' ),
							'required' => array( 'show-breadcrumbs', 'equals', '1' ),
							'options'  => array(
								''            => __( '/', 'porto' ),
								'delimiter-2' => __( '>', 'porto' ),
							),
							'default'  => '',
						),
						array(
							'id'       => 'breadcrumbs-css-class',
							'type'     => 'text',
							'title'    => __( 'Custom CSS Class', 'porto' ),
							'subtitle' => __( 'Input the class to customize the breadcrumb.', 'porto' ),
							'default'  => '',
						),
					),
				),
				$options_style
			);

			$this->sections[] = array(
				'id'         => 'skin-breadcrumb',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Breadcrumb Styling', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'       => 'breadcrumbs-bg',
						'type'     => 'background',
						'title'    => __( 'Background', 'porto' ),
						'subtitle' => __( 'Controls the background settings for the breadcrumbs.', 'porto' ),
					),
					array(
						'id'       => 'breadcrumbs-bg-gradient',
						'type'     => 'switch',
						'title'    => __( 'Background Gradient', 'porto' ),
						'subtitle' => __( 'Controls the background gradient settings for the breadcrumbs.', 'porto' ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'breadcrumbs-bg-gcolor',
						'type'     => 'color_gradient',
						'title'    => __( 'Background Gradient Color', 'porto' ),
						'subtitle' => __( 'Controls the top and bottom background color of the breadcrumb.', 'porto' ),
						'required' => array( 'breadcrumbs-bg-gradient', 'equals', true ),
						'default'  => array(
							'from' => '',
							'to'   => '',
						),
					),
					array(
						'id'       => 'breadcrumbs-parallax',
						'type'     => 'switch',
						'title'    => __( 'Enable Background Image Parallax', 'porto' ),
						'subtitle' => __( 'Select "YES" to use a parallax scrolling effect on the background image.', 'porto' ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'breadcrumbs-parallax-speed',
						'type'     => 'text',
						'title'    => __( 'Parallax Speed', 'porto' ),
						'subtitle' => __( 'Control the parallax scrolling speed on the background image.', 'porto' ),
						'default'  => '1.5',
						'required' => array( 'breadcrumbs-parallax', 'equals', true ),
					),
					array(
						'id'       => 'breadcrumbs-top-border',
						'type'     => 'border',
						'all'      => true,
						'style'    => false,
						'title'    => __( 'Top Border', 'porto' ),
						'subtitle' => __( 'Controls the width and color of top border for breadcrumb.', 'porto' ),
						'default'  => array(
							'border-color' => '#384045',
							'border-top'   => '',
						),
					),
					array(
						'id'       => 'breadcrumbs-bottom-border',
						'type'     => 'border',
						'all'      => true,
						'style'    => false,
						'title'    => __( 'Bottom Border', 'porto' ),
						'subtitle' => __( 'Controls the width and color of bottom border for breadcrumb.', 'porto' ),
						'default'  => array(
							'border-color' => '#cccccc',
							'border-top'   => '5px',
						),
					),
					array(
						'id'       => 'breadcrumbs-padding',
						'type'     => 'spacing',
						'mode'     => 'padding',
						'title'    => __( 'Content Padding', 'porto' ),
						'desc'     => __( 'default: 15 15', 'porto' ),
						'subtitle' => __( 'Controls the padding of breadcrumb.', 'porto' ),
						'left'     => false,
						'right'    => false,
						'default'  => array(
							'padding-top'    => 15,
							'padding-bottom' => 15,
						),
					),
					array(
						'id'     => 'desc_info_page_title',
						'type'   => 'info',
						'desc'   => wp_kses(
							__( '<b>Page Title:</b> If the title <span>isn\'t</span> displayed, please enable Theme Options/Breadcrumbs/Show Page Title.', 'porto' ),
							array(
								'span' => array(),
								'b'    => array(),
							)
						),
						'notice' => false,
						'class'  => 'porto-redux-section',
					),
					array(
						'id'             => 'breadcrumbs-title-font',
						'type'           => 'typography',
						'title'          => __( 'Page Title Font', 'porto' ),
						'google'         => true,
						'subsets'        => false,
						'font-style'     => false,
						'text-align'     => false,
						'color'          => false,
						'letter-spacing' => true,
						'default'        => array(
							'google'         => true,
							'font-weight'    => '',
							'font-family'    => '',
							'font-size'      => '',
							'line-height'    => '',
							'letter-spacing' => '',
						),
						'transport'      => 'postMessage',
						'selector'       => array(
							'node' => '.page-top .page-title',
						),
					),
					array(
						'id'       => 'breadcrumbs-title-color',
						'type'     => 'color',
						'title'    => __( 'Page Title Color', 'porto' ),
						'subtitle' => __( 'Controls the title color of breadcrumb.', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
					),
					array(
						'id'     => 'desc_info_page_subtitle',
						'type'   => 'info',
						'desc'   => wp_kses(
							__( '<b>Page Subtitle:</b> If the subtitle <span>isn\'t</span> displayed, please enable View Options/Page Sub Title of Page Meta Options.', 'porto' ),
							array(
								'span' => array(),
								'b'    => array(),
							)
						),
						'notice' => false,
						'class'  => 'porto-redux-section',
					),
					array(
						'id'             => 'breadcrumbs-subtitle-font',
						'type'           => 'typography',
						'title'          => __( 'Page Subtitle Font', 'porto' ),
						'google'         => true,
						'subsets'        => false,
						'font-style'     => false,
						'text-align'     => false,
						'color'          => false,
						'letter-spacing' => true,
						'default'        => array(
							'google'         => true,
							'font-weight'    => '',
							'font-family'    => '',
							'font-size'      => '',
							'line-height'    => '',
							'letter-spacing' => '',
						),
						'transport'      => 'postMessage',
						'selector'       => array(
							'node' => '.page-top .page-subtitle',
						),
					),
					array(
						'id'       => 'breadcrumbs-subtitle-color',
						'type'     => 'color',
						'title'    => __( 'Page Sub Title Color', 'porto' ),
						'subtitle' => __( 'Controls the subtitle color of breadcrumb.', 'porto' ),
						'default'  => '#e6e6e6',
						'validate' => 'color',
					),
					array(
						'id'       => 'breadcrumbs-subtitle-margin',
						'type'     => 'spacing',
						'mode'     => 'margin',
						'title'    => __( 'Page Sub Title Margin', 'porto' ),
						'subtitle' => __( 'Controls the margin of breadcrumb subtitle.', 'porto' ),
						'desc'     => __( 'If the subtitle isn\'t displayed, please input to <strong>View Options/Page Sub Title</strong> of Page Meta Options.', 'porto' ),
						'default'  => array(
							'margin-top'    => 0,
							'margin-bottom' => 0,
							'margin-left'   => 0,
							'margin-right'  => 0,
						),
					),
					array(
						'id'     => 'desc_info_breadcrumb_path',
						'type'   => 'info',
						'desc'   => wp_kses(
							__( '<b>Breadcrumb Path:</b> If the breadcrumb path <span>isn\'t</span> displayed, please enable Theme Options/Breadcrumbs/Show Breadcrumbs.', 'porto' ),
							array(
								'span' => array(),
								'b'    => array(),
							)
						),
						'notice' => false,
						'class'  => 'porto-redux-section',
					),
					array(
						'id'             => 'breadcrumbs-path-font',
						'type'           => 'typography',
						'title'          => __( 'Breadcrumb Path Font', 'porto' ),
						'google'         => true,
						'subsets'        => false,
						'font-style'     => false,
						'text-align'     => false,
						'color'          => false,
						'letter-spacing' => true,
						'default'        => array(
							'google'         => true,
							'font-weight'    => '',
							'font-family'    => '',
							'font-size'      => '',
							'line-height'    => '',
							'letter-spacing' => '',
						),
						'transport'      => 'postMessage',
						'selector'       => array(
							'node' => '.page-top .breadcrumb',
						),
					),
					array(
						'id'       => 'breadcrumbs-delimiter-font',
						'type'     => 'text',
						'title'    => __( 'Delimiter Font Size', 'porto' ),
						'subtitle' => __( 'Controls the font size of delimiter. Enter value including any valid CSS unit, ex: 30px.', 'porto' ),
					),
					array(
						'id'       => 'breadcrumbs-text-color',
						'type'     => 'color',
						'title'    => __( 'Text Color', 'porto' ),
						'subtitle' => __( 'Controls the text color of breadcrumb.', 'porto' ),
						'default'  => '#777777',
						'validate' => 'color',
					),
					array(
						'id'       => 'breadcrumbs-link-color',
						'type'     => 'color',
						'title'    => __( 'Link Color', 'porto' ),
						'subtitle' => __( 'Controls the hyperlink color of breadcrumb.', 'porto' ),
						'default'  => '#0088cc',
						'validate' => 'color',
					),
					array(
						'id'       => 'breadcrumbs-path-margin',
						'type'     => 'spacing',
						'mode'     => 'margin',
						'title'    => __( 'Path Margin', 'porto' ),
						'subtitle' => __( 'Controls the margin of breadcrumb path.', 'porto' ),
					),
				),
			);


			if ( $this->legacy_mode ) {
				// Footer Settings
				$this->sections[] = $this->add_customizer_field(
					array(
						'id'         => 'footer-settings',
						'icon'       => 'Simple-Line-Icons-arrow-down-circle',
						'icon_class' => '',
						'title'      => __( 'Footer', 'porto' ),
						'transport'  => 'postMessage',
						'fields'     => array(
							array(
								'id'    => 'desc_info_footer_notice',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Footer</a> Builder helps you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $footer_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'         => 'footer-type',
								'type'       => 'image_select',
								'full_width' => true,
								'title'      => __( 'Footer Type', 'porto' ),
								'subtitle'   => __( 'Determine how to set the layout of the footer main. This option isn\'t available to <strong>Footer Builder</strong>', 'porto' ),
								'options'    => $porto_footer_type,
								'default'    => '1',
							),
							array(
								'id'       => 'footer-customize',
								'type'     => 'switch',
								'title'    => __( 'Customize Footer Columns', 'porto' ),
								'subtitle' => __( 'This setting doesn\'t work for <strong>footer builder</strong>.', 'porto' ),
								'desc'     => __( 'Select "YES" to customize the width of footer widgets.', 'porto' ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'footer-widget1',
								'type'     => 'select',
								'title'    => __( 'Widget 1', 'porto' ),
								'required' => array( 'footer-customize', 'equals', true ),
								'subtitle' => __( 'Select the custom width of the footer widget 1.', 'porto' ),
								'options'  => $porto_footer_columns,
								'default'  => '',
							),
							array(
								'id'       => 'footer-widget2',
								'type'     => 'select',
								'title'    => __( 'Widget 2', 'porto' ),
								'required' => array( 'footer-customize', 'equals', true ),
								'subtitle' => __( 'Select the custom width of the footer widget 2.', 'porto' ),
								'options'  => $porto_footer_columns,
								'default'  => '',
							),
							array(
								'id'       => 'footer-widget3',
								'type'     => 'select',
								'title'    => __( 'Widget 3', 'porto' ),
								'required' => array( 'footer-customize', 'equals', true ),
								'subtitle' => __( 'Select the custom width of the footer widget 3.', 'porto' ),
								'options'  => $porto_footer_columns,
								'default'  => '',
							),
							array(
								'id'       => 'footer-widget4',
								'type'     => 'select',
								'title'    => __( 'Widget 4', 'porto' ),
								'required' => array( 'footer-customize', 'equals', true ),
								'subtitle' => __( 'Select the custom width of the footer widget 4.', 'porto' ),
								'options'  => $porto_footer_columns,
								'default'  => '',
							),
							array(
								'id'       => 'footer-reveal',
								'type'     => 'switch',
								'title'    => __( 'Show Reveal Effect', 'porto' ),
								'desc'     => __( 'Select "YES" to enable reveal effect.', 'porto' ),
								'subtitle' => __( 'This option is allowed to the footer higher than window\'s height.', 'porto' ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'footer-logo',
								'type'     => 'media',
								'url'      => true,
								'readonly' => false,
								'title'    => __( 'Footer Logo', 'porto' ),
								'subtitle' => __( 'This setting doesn\'t work for <strong>footer builder</strong>.', 'porto' ),
								'desc'     => __( 'Upload footer logo which is displayed at the left of footer bottom container.', 'porto' ),
								'default'  => array(
									'url' => PORTO_URI . '/images/logo/logo_footer.png',
								),
							),
							array(
								'id'      => 'footer-ribbon',
								'type'    => 'text',
								'desc'    => __( 'Please input ribbon text which is displayed at the top and left of the footer container if you want.', 'porto' ),
								'title'   => __( 'Ribbon Text', 'porto' ),
								'default' => '',
							),
							array(
								'id'       => 'footer-copyright',
								'type'     => 'textarea',
								'title'    => __( 'Copyright', 'porto' ),
								'subtitle' => __( 'This setting doesn\'t work for <strong>footer builder</strong>.', 'porto' ),
								'desc'     => __( 'Input the text that displays in the copyright bar.', 'porto' ),
								/* translators: %s: Current Year */
								'default'  => sprintf( __( '&copy; Copyright %s. All Rights Reserved.', 'porto' ), date( 'Y' ) ),
							),
							array(
								'id'       => 'footer-copyright-pos',
								'type'     => 'button_set',
								'title'    => __( 'Copyright Position', 'porto' ),
								'subtitle' => __( 'This setting doesn\'t work for <strong>footer builder</strong>.', 'porto' ),
								'desc'     => __( 'Controls the position that shows copyright text at the footer bottom container.', 'porto' ),
								'options'  => array(
									'left'   => __( 'Left', 'porto' ),
									'center' => __( 'Center', 'porto' ),
									'right'  => __( 'Right', 'porto' ),
								),
								'default'  => 'left',
							),
							array(
								'id'       => 'show-footer-tooltip',
								'type'     => 'switch',
								'title'    => __( 'Show Tooltip', 'porto' ),
								'subtitle' => __( 'Controls if show tooltip with exclamation mark and tooltip contents on click at the top and right of footer.', 'porto' ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'footer-tooltip',
								'type'     => 'textarea',
								'title'    => __( 'Tooltip Content', 'porto' ),
								'subtitle' => __( 'Please input tooltip text which is displayed at the footer main container. It you input nothing, you will not see the    tooltip.', 'porto' ),
								'required' => array( 'show-footer-tooltip', 'equals', true ),
							),
							array(
								'id'     => 'desc_info_footer_payment',
								'type'   => 'info',
								'desc'   => wp_kses(
									__( '<b>Payments:</b> If you use <span>footer builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
									array(
										'span' => array(),
										'b'    => array(),
									)
								),
								'notice' => false,
								'class'  => 'porto-redux-section',
							),
							array(
								'id'      => 'footer-payments',
								'type'    => 'switch',
								'title'   => __( 'Show Payments Logos', 'porto' ),
								'desc'    => __( 'Controls if show payment icons at the bottom of the footer.', 'porto' ),
								'default' => false,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'footer-payments-image',
								'type'     => 'media',
								'url'      => true,
								'readonly' => false,
								'title'    => __( 'Payments Image', 'porto' ),
								'subtitle' => __( 'Upload the payment image to show.', 'porto' ),
								'required' => array( 'footer-payments', 'equals', '1' ),
								'default'  => array(
									'url' => PORTO_URI . '/images/payments.png',
								),
							),
							array(
								'id'       => 'footer-payments-image-alt',
								'type'     => 'text',
								'title'    => __( 'Payments Image Alt', 'porto' ),
								'subtitle' => __( 'Input the alternative text.', 'porto' ),
								'required' => array( 'footer-payments', 'equals', '1' ),
								'default'  => '',
							),
							array(
								'id'       => 'footer-payments-link',
								'type'     => 'text',
								'title'    => __( 'Payments Link URL', 'porto' ),
								'subtitle' => __( 'Input the permalink of image.', 'porto' ),
								'required' => array( 'footer-payments', 'equals', '1' ),
								'default'  => '',
							),
						),
					),
					$options_style
				);
			} else {
				$this->sections[] = $this->add_customizer_field(
					array(
						'id'         => 'footer-settings',
						'icon'       => 'Simple-Line-Icons-arrow-down-circle',
						'icon_class' => '',
						'title'      => __( 'Footer', 'porto' ),
						'transport'  => 'postMessage',
						'fields'     => array(
							array(
								'id'    => 'desc_info_footer_notice',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Footer</a> Builder helps you to develop your site easily.', 'porto' ), $footer_url ),
									array(
										'strong' => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'       => 'footer-reveal',
								'type'     => 'switch',
								'title'    => __( 'Show Reveal Effect', 'porto' ),
								'subtitle' => __( 'Select "YES" to enable reveal effect.', 'porto' ),
								'desc'     => __( 'This option is allowed to the footer higher than window\'s height.', 'porto' ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'      => 'footer-ribbon',
								'type'    => 'text',
								'desc'    => __( 'Please input ribbon text which is displayed at the top and left of the footer container if you want.', 'porto' ),
								'title'   => __( 'Ribbon Text', 'porto' ),
								'default' => '',
							),
						),
					),
					$options_style
				);
			}

			if ( $this->legacy_mode ) {
				$this->sections[] = array(
					'id'         => 'skin-footer',
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Footer Styling', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'    => 'desc_info_skin_footer_notice',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Footer</a> Builder helps you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $footer_url ),
								array(
									'strong' => array(),
									'b'      => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'     => 'desc_info_footer_top_widget',
							'type'   => 'info',
							'title'  => __( 'For Footer Top Widget', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'footer-top-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the background settings for the footer top widget.', 'porto' ),
						),
						array(
							'id'       => 'footer-top-bg-gradient',
							'type'     => 'switch',
							'title'    => __( 'Enable Background Gradient', 'porto' ),
							'subtitle' => __( 'Controls the background gradient settings of the top widget.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-top-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Background Gradient Color', 'porto' ),
							'subtitle' => __( 'Controls the top and bottom background color of top widget.', 'porto' ),
							'required' => array( 'footer-top-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '',
								'to'   => '',
							),
						),
						array(
							'id'       => 'footer-top-padding',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Padding', 'porto' ),
							'subtitle' => __( 'Controls the padding of top widget.', 'porto' ),
							'left'     => false,
							'right'    => false,
						),
						array(
							'id'     => 'desc_info_footer_general_option',
							'type'   => 'info',
							'title'  => __( 'Footer General Options', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'footer-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the footer background settings.', 'porto' ),
							'default'  => array(
								'background-color' => '#212529',
							),
						),
						array(
							'id'       => 'footer-bg-gradient',
							'type'     => 'switch',
							'title'    => __( 'Background Gradient', 'porto' ),
							'subtitle' => __( 'Controls the footer background gradient settings.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Background Gradient Color', 'porto' ),
							'subtitle' => __( 'Controls the top and bottom color of background.', 'porto' ),
							'required' => array( 'footer-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '',
								'to'   => '',
							),
						),
						array(
							'id'       => 'footer-parallax',
							'type'     => 'switch',
							'title'    => __( 'Enable Background Image Parallax', 'porto' ),
							'subtitle' => __( 'Select "YES" to use a parallax scrolling effect on the background image.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-parallax-speed',
							'type'     => 'text',
							'title'    => __( 'Parallax Speed', 'porto' ),
							'subtitle' => __( 'Control the parallax scrolling speed on the background image.', 'porto' ),
							'default'  => '1.5',
							'required' => array( 'footer-parallax', 'equals', true ),
						),
						array(
							'id'     => 'desc_info_footer_main',
							'type'   => 'info',
							'title'  => __( 'For Footer Main Section which contains footer Widgets', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'footer-main-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the background settings for the footer main section which contains widget areas.', 'porto' ),
						),
						array(
							'id'       => 'footer-main-bg-gradient',
							'type'     => 'switch',
							'title'    => __( 'Background Gradient', 'porto' ),
							'subtitle' => __( 'Controls the footer main background gradient settings.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-main-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Background Gradient Color', 'porto' ),
							'subtitle' => __( 'Controls the top and bottom color of footer main background.', 'porto' ),
							'required' => array( 'footer-main-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '',
								'to'   => '',
							),
						),
						array(
							'id'       => 'footer-heading-color',
							'type'     => 'color',
							'title'    => __( 'Heading Color', 'porto' ),
							'subtitle' => __( 'Controls the heading color in the footer main section.(h1 - h6)', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
						),
						array(
							'id'       => 'footer-label-color',
							'type'     => 'color',
							'title'    => __( 'Label Color', 'porto' ),
							'subtitle' => __( 'Controls the title color of contact info widget in the footer.', 'porto' ),
							'validate' => 'color',
						),
						array(
							'id'       => 'footer-text-color',
							'type'     => 'color',
							'title'    => __( 'Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color in the footer main section.', 'porto' ),
							'default'  => '#777777',
							'validate' => 'color',
						),
						array(
							'id'       => 'footer-link-color',
							'type'     => 'link_color',
							'active'   => false,
							'title'    => __( 'Link Color', 'porto' ),
							'subtitle' => __( 'Controls normal and hover color of hyperlink in the footer main section.', 'porto' ),
							'default'  => array(
								'regular' => '#777777',
								'hover'   => '#ffffff',
							),
						),
						array(
							'id'       => 'footer-ribbon-bg-color',
							'type'     => 'color',
							'title'    => __( 'Ribbon Background Color', 'porto' ),
							'subtitle' => __( 'Controls the background color for the footer ribbon.', 'porto' ),
							'desc'     => __( 'This option is useful when <strong>Theme Option/Footer/Ribbon Text</strong> option gets value.', 'porto' ),
							'default'  => '#0088cc',
							'validate' => 'color',
						),
						array(
							'id'       => 'footer-ribbon-text-color',
							'type'     => 'color',
							'title'    => __( 'Ribbon Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color for the footer ribbon.', 'porto' ),
							'desc'     => __( 'This option is useful when <strong>Theme Option/Footer/Ribbon Text</strong> option gets value.', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
						),
						array(
							'id'     => 'desc_info_footer_bottom',
							'type'   => 'info',
							'title'  => __( 'For Footer Bottom', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'footer-bottom-bg',
							'type'     => 'background',
							'title'    => __( 'Background', 'porto' ),
							'subtitle' => __( 'Controls the background settings for the footer bottom.', 'porto' ),
							'default'  => array(
								'background-color' => '#1c2023',
							),
						),
						array(
							'id'       => 'footer-bottom-bg-gradient',
							'type'     => 'switch',
							'title'    => __( 'Background Gradient', 'porto' ),
							'subtitle' => __( 'Controls the footer bottom background gradient settings.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-bottom-bg-gcolor',
							'type'     => 'color_gradient',
							'title'    => __( 'Background Gradient Color', 'porto' ),
							'subtitle' => __( 'Controls the top and bottom background color of the footer bottom.', 'porto' ),
							'required' => array( 'footer-bottom-bg-gradient', 'equals', true ),
							'default'  => array(
								'from' => '',
								'to'   => '',
							),
						),
						array(
							'id'       => 'footer-bottom-text-color',
							'type'     => 'color',
							'title'    => __( 'Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color in the footer bottom.', 'porto' ),
							'default'  => '#555555',
							'validate' => 'color',
						),
						array(
							'id'       => 'footer-bottom-link-color',
							'type'     => 'link_color',
							'active'   => false,
							'title'    => __( 'Link Color', 'porto' ),
							'subtitle' => __( 'Controls normal and hover color of hyperlink in the footer bottom.', 'porto' ),
							'default'  => array(
								'regular' => '#777777',
								'hover'   => '#ffffff',
							),
						),
						array(
							'id'     => 'desc_info_footer_fixed',
							'type'   => 'info',
							'title'  => __( 'Background Opacity when footer show in fixed position', 'porto' ),
							'notice' => false,
						),
						array(
							'id'      => 'footer-opacity',
							'type'    => 'text',
							'title'   => __( 'Footer Opacity', 'porto' ),
							'default' => '80%',
						),
						array(
							'id'     => 'desc_info_follow_us',
							'type'   => 'info',
							'title'  => __( 'Follow Us Widget', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'footer-social-bg-color',
							'type'     => 'color',
							'title'    => __( 'Background Color', 'porto' ),
							'subtitle' => __( 'Controls the background color for the social links in the footer.', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
						),
						array(
							'id'       => 'footer-social-link-color',
							'type'     => 'color',
							'title'    => __( 'Link Color', 'porto' ),
							'subtitle' => __( 'Controls the text color for the social links in the footer.', 'porto' ),
							'default'  => '#333333',
							'validate' => 'color',
						),
					),
				);
			} else {
				$this->sections[] = array(
					'id'         => 'skin-footer',
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Footer Styling', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'       => 'footer-ribbon-bg-color',
							'type'     => 'color',
							'title'    => __( 'Ribbon Background Color', 'porto' ),
							'subtitle' => __( 'Controls the background color for the footer ribbon.', 'porto' ),
							'desc'     => __( 'This option is useful when <strong>Theme Option/Footer/Ribbon Text</strong> option gets value.', 'porto' ),
							'default'  => '#0088cc',
							'validate' => 'color',
						),
						array(
							'id'       => 'footer-ribbon-text-color',
							'type'     => 'color',
							'title'    => __( 'Ribbon Text Color', 'porto' ),
							'subtitle' => __( 'Controls the text color for the footer ribbon.', 'porto' ),
							'desc'     => __( 'This option is useful when <strong>Theme Option/Footer/Ribbon Text</strong> option gets value.', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
						),
					),
				);
			}

			// Sidebar
			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'       => 'Simple-Line-Icons-notebook',
					'icon_class' => 'icon',
					'title'      => __( 'Sidebar', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'      => 'sticky-sidebar',
							'type'    => 'switch',
							'title'   => __( 'Enable Sticky Sidebar', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'show-mobile-sidebar',
							'type'    => 'switch',
							'title'   => __( 'Show Sidebar in Navigation on mobile', 'porto' ),
							'desc'    => __( 'Show sidebar toggle button only which leads to the sidebar on the left side of the window.', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'sidebar-bw',
							'type'     => 'text',
							'title'    => __( 'Sidebar Border Width (px)', 'porto' ),
							'subtitle' => __( 'Controls the border size of the sidebar.', 'porto' ),
						),
						array(
							'id'       => 'sidebar-bc',
							'type'     => 'color',
							'title'    => __( 'Sidebar Border Color', 'porto' ),
							'subtitle' => __( 'Controls the border color of the sidebar.', 'porto' ),
							'default'  => '',
							'validate' => 'color',
							'required' => array( 'sidebar-bw', '!=', '' ),
						),
						array(
							'id'       => 'sidebar-pd',
							'type'     => 'spacing',
							'mode'     => 'padding',
							'title'    => __( 'Sidebar Padding (px)', 'porto' ),
							'subtitle' => __( 'Controls the padding of the sidebar.', 'porto' ),
							'units'    => 'px',
						),
					),
				),
				$options_style
			);

			// Page
			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'       => 'icon-content',
					'icon_class' => 'porto-icon',
					'title'      => __( 'Page', 'porto' ),
					'fields'     => array(
						array(
							'id'       => 'page-comment',
							'type'     => 'switch',
							'title'    => __( 'Show Comments', 'porto' ),
							'subtitle' => __( 'Show Page Comments and Comments Respond.', 'porto' ),
							'desc'     => __( 'To show comments respond, you should check Page Meta Option <strong>Allow comments</strong>.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'page-zoom',
							'type'     => 'switch',
							'title'    => __( 'Image Lightbox', 'porto' ),
							'subtitle' => __( 'If you use single & type builder, you should consider the options of builder widgets.', 'porto' ),
							'desc'     => __( 'Turn on to enable the lightbox on single and archive page for the main featured images.', 'porto' ),
							'default'  => true,
							'on'       => __( 'Enable', 'porto' ),
							'off'      => __( 'Disable', 'porto' ),
						),
						array(
							'id'       => 'page-share',
							'type'     => 'switch',
							'title'    => __( 'Show Social Share Links', 'porto' ),
							'subtitle' => __( 'To show social links, you should check <strong>Default</strong> or <strong>Yes</strong> of <strong>Meta Option Share</strong> and enable <strong>Theme Option/Extra/Social Share/Show Social Links</strong>.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'page-share-pos',
							'type'     => 'button_set',
							'title'    => __( 'Position', 'porto' ),
							'options'  => array(
								''      => __( 'Default', 'porto' ),
								'left'  => __( 'Float Left', 'porto' ),
								'right' => __( 'Float Right', 'porto' ),
							),
							'subtitle' => __( 'Show social links on left or right. To set kind of social links, enable options of <strong>Theme Option/Extra/Social Share</strong>.', 'porto' ),
							'default'  => '',
							'required' => array( 'page-share', 'equals', true ),
						),
						array(
							'id'       => 'page-microdata',
							'type'     => 'switch',
							'title'    => __( 'Microdata Rich Snippets', 'porto' ),
							'subtitle' => __( 'To make rich snippets data site wide, you should enable <strong>Microdata Rich Snippets</strong> of <strong>Page Meta Options</strong> and <strong>Microdata Rich Snippets</strong> of <strong>Theme Option/Extra/Seo</strong>.', 'porto' ),
							'default'  => true,
							'on'       => __( 'Enable', 'porto' ),
							'off'      => __( 'Disable', 'porto' ),
						),
					),
				),
				$options_style
			);

			if ( $this->legacy_mode ) {
				// Blog
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon'       => 'Simple-Line-Icons-docs',
						'icon_class' => '',
						'title'      => __( 'Post', 'porto' ),
						'fields'     => array(
							array(
								'id'       => 'post-format',
								'type'     => 'switch',
								'title'    => __( 'Show Post Format', 'porto' ),
								'subtitle' => __( 'Turn on to show post format.', 'porto' ),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'      => 'hot-label',
								'type'    => 'text',
								'title'   => __( '"HOT" Text', 'porto' ),
								'desc'    => __( 'sticky post label', 'porto' ),
								'default' => '',
							),
							array(
								'id'       => 'post-zoom',
								'type'     => 'switch',
								'title'    => __( 'Image Lightbox', 'porto' ),
								'subtitle' => __( 'If you use single & type builder, you should consider the options of builder widgets.', 'porto' ),
								'desc'     => __( 'Turn on to enable the lightbox on single and archive page for the main featured images.', 'porto' ),
								'default'  => true,
								'on'       => __( 'Enable', 'porto' ),
								'off'      => __( 'Disable', 'porto' ),
							),
							array(
								'id'      => 'post-metas',
								'type'    => 'button_set',
								'title'   => __( 'Post Meta', 'porto' ),
								'desc'    => __( 'Determines which metas to show', 'porto' ),
								'multi'   => true,
								'options' => array(
									'like'     => __( 'Like', 'porto' ),
									'date'     => __( 'Date', 'porto' ),
									'author'   => __( 'Author', 'porto' ),
									'cats'     => __( 'Categories', 'porto' ),
									'tags'     => __( 'Tags', 'porto' ),
									'comments' => __( 'Comments', 'porto' ),
									'-'        => 'None',
								),
								'default' => array( 'date', 'author', 'cats', 'tags', 'comments', '-' ),
							),
							array(
								'id'      => 'post-meta-position',
								'type'    => 'button_set',
								'title'   => __( 'Meta Position', 'porto' ),
								'desc'    => __( 'This doesn\'t work for some single post layouts including "Full Alt" and "Woocommerce".', 'porto' ),
								'options' => array(
									''       => __( 'Default', 'porto' ),
									'after'  => __( 'After content', 'porto' ),
									'before' => __( 'Before content', 'porto' ),
								),
								'default' => '',
							),
						),
					),
					$options_style
				);
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Blog & Post Archives', 'porto' ),
					'fields'     => array(
						array(
							'id'    => 'desc_info_post_archive',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Archive</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $archive_url, $type_url ),
								array(
									'strong' => array(),
									'b'      => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'      => 'desc_info_blog_sidebar',
							'type'    => 'info',
							'default' => '',
							'desc'    => wp_kses(
								sprintf(
									/* translators: %s: widgets url */
									__( 'You can control the blog sidebar and secondary sidebar in <a href="%s" target="_blank">here</a>.', 'porto' ),
									esc_url( admin_url( 'widgets.php' ) )
								),
								array(
									'a' => array(
										'href'   => array(),
										'target' => array(),
									),
								)
							),
						),
						array(
							'id'      => 'post-archive-layout',
							'type'    => 'image_select',
							'title'   => __( 'Page Layout', 'porto' ),
							'options' => $page_layouts,
							'default' => 'right-sidebar',
						),
						array(
							'id'      => 'post-layout',
							'type'    => 'image_select',
							'title'   => __( 'Archive Layout', 'porto' ),
							'options' => array(
								'full'        => array(
									'title' => __( 'Full', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_full.svg',
								),
								'large'       => array(
									'title' => __( 'Large', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_full.svg',
								),
								'large-alt'   => array(
									'title' => __( 'Large Alt', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_large_alt.svg',
								),
								'medium'      => array(
									'title' => __( 'Medium', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_medium.svg',
								),
								'grid'        => array(
									'title' => __( 'Grid', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_grid.svg',
								),
								'masonry'     => array(
									'title' => __( 'Masonry', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_masonry.svg',
								),
								'timeline'    => array(
									'title' => __( 'Timeline', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_timeline.svg',
								),
								'medium-alt'  => array(
									'title' => __( 'Medium Alternate', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_medium_alt.svg',
								),
								'woocommerce' => array(
									'title' => __( 'Woocommerce', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_woocommerce.svg',
								),
								'modern'      => array(
									'title' => __( 'Modern', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_modern.svg',
								),
							),
							'default' => 'full',
						),
						array(
							'id'       => 'post-style',
							'type'     => 'button_set',
							'title'    => __( 'Post Style', 'porto' ),
							'required' => array( 'post-layout', 'equals', array( 'grid', 'timeline', 'masonry' ) ),
							'options'  => array(
								'default'    => __( 'Default', 'porto' ),
								'date'       => __( 'Default - Date on Image', 'porto' ),
								'author'     => __( 'Default - Author Picture', 'porto' ),
								'related'    => __( 'Post Carousel Style', 'porto' ),
								'hover_info' => __( 'Hover Info', 'porto' ),
								'no_margin'  => __( 'No Margin & Hover Info', 'porto' ),
								'padding'    => __( 'With Borders', 'porto' ),
							),
							'default'  => 'border',
						),
						array(
							'id'       => 'grid-columns',
							'type'     => 'button_set',
							'title'    => __( 'Grid Columns', 'porto' ),
							'required' => array( 'post-layout', 'equals', array( 'grid', 'masonry' ) ),
							'options'  => array(
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
								'5' => '5',
								'6' => '6',
							),
							'default'  => '3',
						),
						array(
							'id'       => 'post-link',
							'type'     => 'switch',
							'title'    => __( 'Apply Post Link to Content', 'porto' ),
							'required' => array( 'post-style', 'equals', '' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'blog-infinite',
							'type'     => 'button_set',
							'title'    => __( 'Pagination Style', 'porto' ),
							'subtitle' => __( 'Controls the pagination type for post archive page.', 'porto' ),
							'options'  => array(
								''         => __( 'Default', 'porto' ),
								'ajax'     => __( 'Ajax Pagination', 'porto' ),
								'infinite' => __( 'Infinite Scroll', 'porto' ),
							),
							'default'  => 'infinite',
						),
						array(
							'id'      => 'blog-date-format',
							'type'    => 'text',
							'title'   => __( 'Date Format', 'porto' ),
							'desc'    => __( 'j = 1-31, F = January-December, M = Jan-Dec, m = 01-12, n = 1-12', 'porto' ) . '<br />' .
							__( 'For more, please visit ', 'porto' ) . '<a href="https://codex.wordpress.org/Formatting_Date_and_Time">https://codex.wordpress.org/Formatting_Date_and_Time</a>',
							'default' => '',
						),
						array(
							'id'       => 'desc_info_post_share',
							'type'     => 'info',
							'desc'     => wp_kses(
								__( '<b>Post Share:</b> If you use <span>type builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice'   => false,
							'class'    => 'porto-redux-section',
							'required' => array( 'post-layout', 'equals', array( 'grid', 'timeline', 'masonry', 'large-alt' ) ),
						),
						array(
							'id'       => 'blog-post-share',
							'type'     => 'switch',
							'title'    => __( 'Show Social Share Links', 'porto' ),
							'required' => array( 'post-layout', 'equals', array( 'grid', 'timeline', 'masonry', 'large-alt' ) ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),

						array(
							'id'       => 'blog-post-share-position',
							'type'     => 'button_set',
							'required' => array( 'blog-post-share', 'equals', true ),
							'title'    => __( 'Social Share Links Style', 'porto' ),
							'default'  => '',
							'options'  => array(
								''        => __( 'Default', 'porto' ),
								'advance' => __( 'Advance', 'porto' ),
							),
						),
						array(
							'id'     => 'desc_info_post_excerpt',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Post Excerpt:</b> If you use <span>type builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'      => 'blog-excerpt',
							'type'    => 'switch',
							'title'   => __( 'Show Excerpt', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'blog-excerpt-length',
							'type'     => 'text',
							'required' => array( 'blog-excerpt', 'equals', true ),
							'title'    => __( 'Excerpt Length', 'porto' ),
							'default'  => '50',
						),
						array(
							'id'       => 'blog-excerpt-base',
							'type'     => 'button_set',
							'required' => array( 'blog-excerpt', 'equals', true ),
							'title'    => __( 'Basis for Excerpt Length', 'porto' ),
							'subtitle' => __( 'Excerpt length is based on words or characters?', 'porto' ),
							'desc'     => __( 'This works for other post types too.', 'porto' ),
							'options'  => array(
								'words'      => __( 'Words', 'porto' ),
								'characters' => __( 'Characters', 'porto' ),
							),
							'default'  => 'words',
						),
						array(
							'id'       => 'blog-excerpt-type',
							'type'     => 'button_set',
							'required' => array( 'blog-excerpt', 'equals', true ),
							'title'    => __( 'Excerpt Type', 'porto' ),
							'desc'     => __( 'This works for other post types too.', 'porto' ),
							'options'  => array(
								'text' => __( 'Text', 'porto' ),
								'html' => __( 'HTML', 'porto' ),
							),
							'default'  => 'text',
						),
					),
				);
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Blog', 'porto' ),
					'fields'     => array(
						array(
							'id'        => 'blog-title',
							'type'      => 'text',
							'title'     => __( 'Page Title', 'porto' ),
							'default'   => 'Blog',
							'transport' => 'postMessage',
						),
						array(
							'id'      => 'blog-banner_pos',
							'type'    => 'select',
							'title'   => __( 'Blog Banner Position', 'porto' ),
							'options' => $porto_banner_pos,
							'default' => '',
						),
						array(
							'id'        => 'blog-footer_view',
							'type'      => 'select',
							'title'     => __( 'Blog Footer View', 'porto' ),
							'options'   => $porto_footer_view,
							'default'   => '',
							'transport' => 'postMessage',
						),
						array(
							'id'      => 'blog-banner_type',
							'type'    => 'select',
							'title'   => __( 'Blog Banner Type', 'porto' ),
							'options' => $porto_banner_type,
							'default' => '',
						),
						array(
							'id'       => 'blog-master_slider',
							'type'     => 'select',
							'required' => array( 'blog-banner_type', 'equals', 'master_slider' ),
							'title'    => __( 'Master Slider', 'porto' ),
							'options'  => $porto_master_sliders,
							'default'  => '',
						),
						array(
							'id'       => 'blog-rev_slider',
							'type'     => 'select',
							'required' => array( 'blog-banner_type', 'equals', 'rev_slider' ),
							'title'    => __( 'Revolution Slider', 'porto' ),
							'options'  => $porto_rev_sliders,
							'default'  => '',
						),
						array(
							'id'       => 'blog-banner_block',
							'type'     => 'ace_editor',
							'mode'     => 'html',
							'required' => array( 'blog-banner_type', 'equals', 'banner_block' ),
							'title'    => __( 'Banner Block', 'porto' ),
							'desc'     => __( 'Please input block slug name. You can create a block in <strong>Porto / Templates Builder / Block / Add New</strong>.', 'porto' ),
						),
						array(
							'id'        => 'blog-content_top',
							'type'      => 'text',
							'title'     => __( 'Content Top', 'porto' ),
							'desc'      => __( 'Please input comma separated block slug names. You can create a block in <strong>Porto / Templates Builder / Block / Add New</strong>.', 'porto' ),
							'transport' => 'postMessage',
						),
						array(
							'id'        => 'blog-content_inner_top',
							'type'      => 'text',
							'title'     => __( 'Content Inner Top', 'porto' ),
							'desc'      => __( 'Please input comma separated block slug names. You can create a block in <strong>Porto / Templates Builder / Block / Add New</strong>.', 'porto' ),
							'transport' => 'postMessage',
						),
						array(
							'id'        => 'blog-content_inner_bottom',
							'type'      => 'text',
							'title'     => __( 'Content Inner Bottom', 'porto' ),
							'desc'      => __( 'Please input comma separated block slug names. You can create a block in <strong>Porto / Templates Builder / Block / Add New</strong>.', 'porto' ),
							'transport' => 'postMessage',
						),
						array(
							'id'        => 'blog-content_bottom',
							'type'      => 'text',
							'title'     => __( 'Content Bottom', 'porto' ),
							'desc'      => __( 'Please input comma separated block slug names. You can create a block in <strong>Porto / Templates Builder / Block / Add New</strong>.', 'porto' ),
							'transport' => 'postMessage',
						),
					),
				);
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Single Post', 'porto' ),
					'fields'     => array(
						array(
							'id'    => 'desc_info_single_post',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Single</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $single_url, $type_url ),
								array(
									'strong' => array(),
									'b'      => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'      => 'desc_info_single_post_sidebar',
							'type'    => 'info',
							'default' => '',
							'desc'    => wp_kses(
								sprintf(
									/* translators: %s: widgets url */
									__( 'You can control the blog sidebar and secondary sidebar in <a href="%s" target="_blank">here</a>.', 'porto' ),
									esc_url( admin_url( 'widgets.php' ) )
								),
								array(
									'a' => array(
										'href'   => array(),
										'target' => array(),
									),
								)
							),
						),
						array(
							'id'      => 'post-single-layout',
							'type'    => 'image_select',
							'title'   => __( 'Page Layout', 'porto' ),
							'options' => $page_layouts,
							'default' => 'right-sidebar',
						),
						array(
							'id'    => 'post-banner-block',
							'type'  => 'text',
							'title' => __( 'Global Banner Block', 'porto' ),
							'desc'  => __( 'Please input comma separated block slug names. You can create a block in <strong>Porto / Templates Builder / Block / Add New</strong>.', 'porto' ),
						),
						array(
							'id'        => 'post-content_bottom',
							'type'      => 'text',
							'title'     => __( 'Content Bottom Block', 'porto' ),
							'desc'      => __( 'Please input comma separated block slug names. You can create a block in <strong>Porto / Templates Builder / Block / Add New</strong>.', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'      => 'post-content-layout',
							'type'    => 'image_select',
							'title'   => __( 'Post Layout', 'porto' ),
							'options' => array(
								'full'        => array(
									'title' => __( 'Full', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_full.svg',
								),
								'large'       => array(
									'title' => __( 'Large', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_full.svg',
								),
								'large-alt'   => array(
									'title' => __( 'Large Alt', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_large_alt.svg',
								),
								'medium'      => array(
									'title' => __( 'Medium', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/post_medium.svg',
								),
								'full-alt'    => array(
									'title' => __( 'Full Alt', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/post_full_alt.svg',
								),
								'woocommerce' => array(
									'title' => __( 'Woocommerce', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_woocommerce.svg',
								),
								'modern'      => array(
									'title' => __( 'Modern', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/blog_modern.svg',
								),
							),
							'default' => 'full',
						),
						array(
							'id'        => 'post-replace-pos',
							'type'      => 'switch',
							'title'     => __( 'Replace the position of title and meta', 'porto' ),
							'default'   => false,
							'required'  => array( 'post-content-layout', 'equals', array( 'large-alt', 'full-alt' ) ),
							'transport' => 'postMessage',
						),
						array(
							'id'      => 'post-title-style',
							'type'    => 'button_set',
							'title'   => __( 'Post Section Title Style', 'porto' ),
							'desc'    => __( 'Select title style of author, comment, etc.', 'porto' ),
							'options' => array(
								''             => __( 'With Icon', 'porto' ),
								'without-icon' => __( 'Without Icon', 'porto' ),
							),
							'default' => 'without-icon',
						),
						// array(
						// 	'id'        => 'post-slideshow',
						// 	'type'      => 'switch',
						// 	'title'     => __( 'Show Slideshow', 'porto' ),
						// 	'default'   => true,
						// 	'on'        => __( 'Yes', 'porto' ),
						// 	'off'       => __( 'No', 'porto' ),
						// 	'transport' => 'postMessage',
						// ),
						array(
							'id'        => 'post-title',
							'type'      => 'switch',
							'title'     => __( 'Show Title', 'porto' ),
							'subtitle'  => __( 'Turn on to show the title', 'porto' ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'postMessage',
						),
						array(
							'id'        => 'post-share',
							'type'      => 'switch',
							'title'     => __( 'Show Social Share Links', 'porto' ),
							'subtitle'  => __( 'Turn on to show the social share links.', 'porto' ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'postMessage',
						),

						array(
							'id'        => 'post-share-position',
							'type'      => 'button_set',
							'required'  => array( 'post-share', 'equals', true ),
							'title'     => __( 'Social Share Links Style', 'porto' ),
							'subtitle'  => __( 'Controls the social share links style.', 'porto' ),
							'default'   => '',
							'options'   => array(
								''        => __( 'Default', 'porto' ),
								'advance' => __( 'Advance', 'porto' ),
							),
							'transport' => 'postMessage',
						),
						array(
							'id'        => 'post-author',
							'type'      => 'switch',
							'title'     => __( 'Show Author Info', 'porto' ),
							'subtitle'  => __( 'Turn on to show the author information.', 'porto' ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'postMessage',
						),
						array(
							'id'        => 'post-comments',
							'type'      => 'switch',
							'title'     => __( 'Show Comments', 'porto' ),
							'subtitle'  => __( 'Turn on to show the comments.', 'porto' ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'postMessage',
						),
						array(
							'id'       => 'post-backto-blog',
							'type'     => 'switch',
							'title'    => __( 'Show Back to Blog Link', 'porto' ),
							'subtitle' => __( 'Turn on to show \'Back Icon\' to Blog Page Link.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'     => 'desc_info_related_post',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Related Posts:</b> If you use <span>single builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'      => 'post-related',
							'type'    => 'switch',
							'title'   => __( 'Show Related Posts', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'post-related-count',
							'type'     => 'text',
							'required' => array( 'post-related', 'equals', true ),
							'title'    => __( 'Related Posts Count', 'porto' ),
							'desc'     => __( 'If you want to show all the related posts, please input "-1".', 'porto' ),
							'default'  => '10',
						),
						array(
							'id'       => 'post-related-orderby',
							'type'     => 'button_set',
							'required' => array( 'post-related', 'equals', true ),
							'title'    => __( 'Related Posts Order by', 'porto' ),
							'options'  => array(
								'none'          => __( 'None', 'porto' ),
								'rand'          => __( 'Random', 'porto' ),
								'date'          => __( 'Date', 'porto' ),
								'ID'            => __( 'ID', 'porto' ),
								'modified'      => __( 'Modified Date', 'porto' ),
								'comment_count' => __( 'Comment Count', 'porto' ),
							),
							'default'  => 'rand',
						),
						array(
							'id'       => 'post-related-cols',
							'type'     => 'button_set',
							'required' => array( 'post-related', 'equals', true ),
							'title'    => __( 'Related Posts Columns', 'porto' ),
							'desc'     => __( 'reduce one column in left or right sidebar layout', 'porto' ),
							'options'  => array(
								'4' => '4',
								'3' => '3',
								'2' => '2',
								'1' => '1',
							),
							'default'  => '4',
						),
					),
				);
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Related Posts Carousel', 'porto' ),
					'fields'     => array(
						array(
							'id'    => 'desc_info_single_post_carousel',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Single</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily.', 'porto' ), $single_url, $type_url ),
								array(
									'strong' => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'      => 'post-related-image-size',
							'type'    => 'dimensions',
							'title'   => __( 'Post Image Size', 'porto' ),
							'desc'    => __( 'Please regenerate all the thumbnails in <strong>Tools > Regen.Thumbnails</strong> after save changes.', 'porto' ),
							'default' => array(
								'width'  => '450',
								'height' => '231',
							),
						),
						array(
							'id'      => 'post-related-style',
							'type'    => 'image_select',
							'title'   => __( 'Post Style', 'porto' ),
							'options' => array(
								''        => array(
									'title' => __( 'With Read More Link', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/post_style_1.svg',
								),
								'style-2' => array(
									'title' => __( 'With Post Meta', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/post_style_2.svg',
								),
								'style-3' => array(
									'title' => __( 'With Read More Button', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/post_style_3.svg',
								),
								'style-4' => array(
									'title' => __( 'With Side Image', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/post_style_4.svg',
								),
								'style-5' => array(
									'title' => __( 'With Categories', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/post_style_5.svg',
								),
								'style-6' => array(
									'title' => __( 'Simple', 'porto' ),
									'img'   => PORTO_OPTIONS_URI . '/images/post_style_6.svg',
								),
							),
							'default' => '',
						),
						array(
							'id'      => 'post-related-excerpt-length',
							'type'    => 'text',
							'title'   => __( 'Excerpt Length', 'porto' ),
							'desc'    => __( 'The number of words', 'porto' ),
							'default' => '20',
						),
						array(
							'id'       => 'post-related-thumb-bg',
							'type'     => 'button_set',
							'title'    => __( 'Image Overlay Background', 'porto' ),
							'subtitle' => __( 'Controls the overlay background of featured image.', 'porto' ),
							'options'  => array(
								''                => __( 'Darken', 'porto' ),
								'lighten'         => __( 'Lighten', 'porto' ),
								'hide-wrapper-bg' => __( 'Transparent', 'porto' ),
							),
							'default'  => 'hide-wrapper-bg',
						),
						array(
							'id'       => 'post-related-thumb-image',
							'type'     => 'button_set',
							'title'    => __( 'Hover Image Effect', 'porto' ),
							'subtitle' => __( 'Controls the hover effect of image.', 'porto' ),
							'options'  => array(
								''        => __( 'Zoom', 'porto' ),
								'no-zoom' => __( 'No Zoom', 'porto' ),
							),
							'default'  => '',
						),
						array(
							'id'       => 'post-related-thumb-borders',
							'type'     => 'button_set',
							'title'    => __( 'Image Borders', 'porto' ),
							'desc'     => __( 'This works for only related post carousel when "Skin -> Layout -> Thumbnail Padding" is enabled.', 'porto' ),
							'options'  => array(
								''           => __( 'With Borders', 'porto' ),
								'no-borders' => __( 'Without Borders', 'porto' ),
							),
							'default'  => '',
							'required' => array( 'thumb-padding', 'equals', true ),
						),
						array(
							'id'       => 'post-related-author',
							'type'     => 'switch',
							'title'    => __( 'Author Name', 'porto' ),
							'subtitle' => __( 'Show author name.', 'porto' ),
							'required' => array( 'post-related-style', 'equals', array( '', 'style-3' ) ),
							'default'  => false,
							'on'       => __( 'Show', 'porto' ),
							'off'      => __( 'Hide', 'porto' ),
						),
						array(
							'id'       => 'desc_info_related_post_button',
							'type'     => 'info',
							'desc'     => __( 'Read More Button', 'porto' ),
							'required' => array( 'post-related-style', 'equals', 'style-3' ),
							'notice'   => false,
						),
						array(
							'id'       => 'post-related-btn-style',
							'type'     => 'button_set',
							'title'    => __( 'Button Style', 'porto' ),
							'subtitle' => __( 'Controls the style of button.', 'porto' ),
							'required' => array( 'post-related-style', 'equals', 'style-3' ),
							'options'  => array(
								''            => __( 'Normal', 'porto' ),
								'btn-borders' => __( 'Borders', 'porto' ),
							),
							'default'  => '',
						),
						array(
							'id'       => 'post-related-btn-size',
							'type'     => 'button_set',
							'title'    => __( 'Button Size', 'porto' ),
							'subtitle' => __( 'Controls the size of the button.', 'porto' ),
							'required' => array( 'post-related-style', 'equals', 'style-3' ),
							'options'  => array(
								''       => __( 'Normal', 'porto' ),
								'btn-sm' => __( 'Small', 'porto' ),
								'btn-xs' => __( 'Extra Small', 'porto' ),
							),
							'default'  => '',
						),
						array(
							'id'       => 'post-related-btn-color',
							'type'     => 'button_set',
							'title'    => __( 'Button Color', 'porto' ),
							'subtitle' => __( 'Controls the skin of button.', 'porto' ),
							'required' => array( 'post-related-style', 'equals', 'style-3' ),
							'options'  => array(
								'btn-default'    => __( 'Default', 'porto' ),
								'btn-primary'    => __( 'Primary', 'porto' ),
								'btn-secondary'  => __( 'Secondary', 'porto' ),
								'btn-tertiary'   => __( 'Tertiary', 'porto' ),
								'btn-quaternary' => __( 'Quaternary', 'porto' ),
								'btn-dark'       => __( 'Dark', 'porto' ),
								'btn-light'      => __( 'Light', 'porto' ),
							),
							'default'  => 'btn-default',
						),
					),
				);
				// Portfolio
				$portfolio_options = array(
					'icon'       => 'Simple-Line-Icons-picture',
					'icon_class' => '',
					'title'      => __( 'Portfolio', 'porto' ),
					'customizer' => false,
					'fields'     => array(
						array(
							'id'       => 'enable-portfolio',
							'type'     => 'switch',
							'title'    => __( 'Portfolio Content Type', 'porto' ),
							'subtitle' => __( 'Enable to provide Portfolio type.', 'porto' ),
							'default'  => true,
							'on'       => __( 'Enable', 'porto' ),
							'off'      => __( 'Disable', 'porto' ),
						),
						array(
							'id'          => 'portfolio-slug-name',
							'type'        => 'text',
							'title'       => __( 'Slug Name', 'porto' ),
							'subtitle'    => __( 'This option changes the permalink when you use the permalink type as %postname%. Make sure to regenerate permalinks.', 'porto' ),
							'placeholder' => 'portfolio',
						),
						array(
							'id'          => 'portfolio-name',
							'type'        => 'text',
							'title'       => __( 'Name', 'porto' ),
							'subtitle'    => __( 'A plural descriptive name for the post type marked for translation.', 'porto' ),
							'placeholder' => __( 'Portfolios', 'porto' ),
						),
						array(
							'id'          => 'portfolio-singular-name',
							'type'        => 'text',
							'title'       => __( 'Singular Name', 'porto' ),
							'subtitle'    => __( 'Name for one object of this post type.', 'porto' ),
							'placeholder' => __( 'Portfolio', 'porto' ),
						),
						array(
							'id'          => 'portfolio-cat-slug-name',
							'type'        => 'text',
							'title'       => __( 'Category Slug Name', 'porto' ),
							'subtitle'    => __( 'The slug name of the taxonomy: category.', 'porto' ),
							'placeholder' => 'portfolio_cat',
						),
						array(
							'id'          => 'portfolio-skill-slug-name',
							'type'        => 'text',
							'title'       => __( 'Skill Slug Name', 'porto' ),
							'subtitle'    => __( 'The slug name of the taxonomy: skill.', 'porto' ),
							'placeholder' => 'portfolio_skill',
						),
					),
				);
				if ( $options_style ) {
					$this->sections[] = $portfolio_options;
				}
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon'       => 'Simple-Line-Icons-picture',
						'icon_class' => '',
						'title'      => __( 'Portfolio', 'porto' ),
						'fields'     => array(
							array(
								'id'       => 'portfolio-archive-page',
								'type'     => 'select',
								'data'     => 'page',
								'title'    => __( 'Portfolios Page', 'porto' ),
								'subtitle' => __( 'Select a portfolio archive page.', 'porto' ),
							),
							array(
								'id'       => 'portfolio-zoom',
								'type'     => 'switch',
								'title'    => __( 'Image Lightbox', 'porto' ),
								'subtitle' => __( 'If you use single & type builder, you should consider the options of builder widgets.', 'porto' ),
								'desc'     => __( 'Turn on to enable the lightbox on single and archive page for the main featured images.', 'porto' ),
								'default'  => true,
								'on'       => __( 'Enable', 'porto' ),
								'off'      => __( 'Disable', 'porto' ),
							),
							array(
								'id'       => 'portfolio-metas',
								'type'     => 'button_set',
								'title'    => __( 'Portfolio Meta', 'porto' ),
								'subtitle' => __( 'If you use single & type builder, you should consider the options of builder widgets.', 'porto' ),
								'desc'     => __( 'Determines which metas to show.', 'porto' ),
								'multi'    => true,
								'options'  => array(
									'like'     => __( 'Like', 'porto' ),
									'date'     => __( 'Date', 'porto' ),
									'cats'     => __( 'Categories', 'porto' ),
									'skills'   => __( 'Skills', 'porto' ),
									'location' => __( 'Location', 'porto' ),
									'client'   => __( 'Client', 'porto' ),
									'quote'    => __( 'Author', 'porto' ),
									'link'     => __( 'Link', 'porto' ),
									'-'        => 'None',
								),
								'default'  => array( 'like', 'date', 'cats', 'skills', 'location', 'client', 'quote', '-', 'link' ),
							),
							array(
								'id'      => 'portfolio-subtitle',
								'type'    => 'button_set',
								'title'   => __( 'Portfolio Sub Title', 'porto' ),
								'desc'    => __( 'Use this value in portfolio archives (grid, masonry, timeline layouts) and portfolio carousel.', 'porto' ),
								'options' => array(
									'none'        => __( 'None', 'porto' ),
									'like'        => __( 'Like', 'porto' ),
									'date'        => __( 'Date', 'porto' ),
									'cats'        => __( 'Categories', 'porto' ),
									'skills'      => __( 'Skills', 'porto' ),
									'location'    => __( 'Location', 'porto' ),
									'client_name' => __( 'Client Name', 'porto' ),
									'client_link' => __( 'Client URL(Link)', 'porto' ),
									'author_name' => __( 'Author Name', 'porto' ),
									'author_role' => __( 'Author Role', 'porto' ),
									'excerpt'     => __( 'Excerpt', 'porto' ),
								),
								'default' => 'cats',
							),
						),
					),
					$options_style,
					$portfolio_options
				);
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Portfolio Archives', 'porto' ),
					'fields'     => array(
						array(
							'id'    => 'desc_info_portfolio_archive',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Archive</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $archive_url, $type_url ),
								array(
									'strong' => array(),
									'b'      => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'        => 'portfolio-title',
							'type'      => 'text',
							'title'     => __( 'Page Title', 'porto' ),
							'default'   => 'Our <strong>Projects</strong>',
							'transport' => 'postMessage',
						),
						array(
							'id'      => 'portfolio-archive-layout',
							'type'    => 'image_select',
							'title'   => __( 'Page Layout', 'porto' ),
							'options' => $page_layouts,
							'default' => 'fullwidth',
						),
						array(
							'id'      => 'portfolio-archive-ajax',
							'type'    => 'switch',
							'title'   => __( 'Enable Ajax Load', 'porto' ),
							'desc'    => __( 'If enabled, portfolio content should be displayed above the portfolios or on modal when you click portfolio item in the list.', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'portfolio-archive-ajax-modal',
							'type'     => 'switch',
							'title'    => __( 'Ajax Load on Modal', 'porto' ),
							'desc'     => __( 'If enabled, portfolio content should be displayed on modal when you click portfolio item in the list.', 'porto' ),
							'required' => array( 'portfolio-archive-ajax', 'equals', true ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'portfolio-archive-sidebar',
							'type'     => 'select',
							'title'    => __( 'Select Sidebar', 'porto' ),
							'required' => array( 'portfolio-archive-layout', 'equals', $sidebars ),
							'data'     => 'sidebars',
						),
						array(
							'id'       => 'portfolio-archive-sidebar2',
							'type'     => 'select',
							'title'    => __( 'Select Sidebar 2', 'porto' ),
							'required' => array( 'portfolio-archive-layout', 'equals', $both_sidebars ),
							'data'     => 'sidebars',
						),
						array(
							'id'       => 'portfolio-infinite',
							'type'     => 'button_set',
							'title'    => __( 'Pagination Style', 'porto' ),
							'subtitle' => __( 'Controls the pagination type for portfolio archive page.', 'porto' ),
							'options'  => array(
								''         => __( 'Default', 'porto' ),
								'ajax'     => __( 'Ajax Pagination', 'porto' ),
								'infinite' => __( 'Infinite Scroll', 'porto' ),
							),
							'default'  => 'infinite',
						),
						array(
							'id'     => 'desc_info_category_portfolio_archive',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Category Filter in Portfolio Page:</b> If you use <span>archive builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'      => 'portfolio-cat-sort-pos',
							'type'    => 'button_set',
							'title'   => __( 'Categories Filter Position', 'porto' ),
							'options' => $porto_categories_sort_pos,
							'default' => 'content',
						),
						array(
							'id'       => 'portfolio-cat-sort-style',
							'type'     => 'button_set',
							'title'    => __( 'Filter Style', 'porto' ),
							'options'  => array(
								''        => __( 'Style 1', 'porto' ),
								'style-2' => __( 'Style 2', 'porto' ),
								'style-3' => __( 'Style 3', 'porto' ),
							),
							'required' => array( 'portfolio-cat-sort-pos', 'equals', array( 'content' ) ),
							'default'  => '',
						),
						array(
							'id'       => 'portfolio-cat-ft',
							'type'     => 'button_set',
							'title'    => __( 'Filter Type', 'porto' ),
							'options'  => array(
								''     => __( 'Filter using Javascript/CSS', 'porto' ),
								'ajax' => __( 'Ajax Loading', 'porto' ),
							),
							'default'  => '',
							'required' => array(
								array( 'portfolio-infinite', '!=', '' ),
								array( 'portfolio-cat-sort-pos', '!=', 'hide' ),
							),
						),
						array(
							'id'       => 'portfolio-cat-orderby',
							'type'     => 'button_set',
							'title'    => __( 'Sort Categories Order By', 'porto' ),
							'options'  => $porto_categories_orderby,
							'default'  => 'name',
							'required' => array( 'portfolio-cat-sort-pos', '!=', 'hide' ),
						),
						array(
							'id'       => 'portfolio-cat-order',
							'type'     => 'button_set',
							'title'    => __( 'Sort Order for Categories', 'porto' ),
							'options'  => $porto_categories_order,
							'default'  => 'asc',
							'required' => array( 'portfolio-cat-sort-pos', '!=', 'hide' ),
						),
						array(
							'id'      => 'portfolio-layout',
							'type'    => 'image_select',
							'title'   => __( 'Archive Layout', 'porto' ),
							'options' => array(
								'grid'     => array(
									'alt' => __( 'Grid', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_archive_style_1.jpg',
								),
								'masonry'  => array(
									'alt' => __( 'Masonry', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_archive_style_2.jpg',
								),
								'timeline' => array(
									'alt' => __( 'Timeline', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_archive_style_3.jpg',
								),
								'medium'   => array(
									'alt' => __( 'Medium', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_archive_style_4.jpg',
								),
								'large'    => array(
									'alt' => __( 'Large', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_archive_style_5.jpg',
								),
								'full'     => array(
									'alt' => __( 'Full', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_archive_style_6.jpg',
								),
							),
							'default' => 'grid',
						),
						array(
							'id'       => 'portfolio-archive-masonry-ratio',
							'type'     => 'text',
							'title'    => __( 'Masonry Image Aspect Ratio', 'porto' ),
							'required' => array( 'portfolio-layout', 'equals', array( 'masonry' ) ),
							'desc'     => __( 'ratio = width / height. if ratio is large than this value, will take more space.', 'porto' ),
							'default'  => '2.4',
						),
						array(
							'id'       => 'portfolio-grid-columns',
							'type'     => 'button_set',
							'title'    => __( 'Columns', 'porto' ),
							'required' => array( 'portfolio-layout', 'equals', array( 'grid', 'masonry' ) ),
							'options'  => array(
								'1' => __( '1 Column', 'porto' ),
								'2' => __( '2 Columns', 'porto' ),
								'3' => __( '3 Columns', 'porto' ),
								'4' => __( '4 Columns', 'porto' ),
								'5' => __( '5 Columns', 'porto' ),
								'6' => __( '6 Columns', 'porto' ),
							),
							'default'  => '4',
						),
						array(
							'id'       => 'portfolio-grid-view',
							'type'     => 'image_select',
							'title'    => __( 'View Type', 'porto' ),
							'required' => array( 'portfolio-layout', 'equals', array( 'grid', 'masonry' ) ),
							'options'  => array(
								'default'  => array(
									'alt' => __( 'Default', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_archive_view_1.jpg',
								),
								'full'     => array(
									'alt' => __( 'No Margin', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_archive_view_2.jpg',
								),
								'outimage' => array(
									'alt' => __( 'Out of Image', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_archive_view_3.jpg',
								),
							),
							'default'  => 'default',
						),

						array(
							'id'       => 'portfolio-archive-thumb',
							'type'     => 'image_select',
							'title'    => __( 'Info View Type', 'porto' ),
							'required' => array(
								array( 'portfolio-layout', 'equals', array( 'grid', 'masonry', 'timeline' ) ),
								array( 'portfolio-grid-view', '!=', 'outimage' ),
							),
							'options'  => array(
								''                 => array(
									'alt' => __( 'Left Info', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_info_view_1.jpg',
								),
								'centered-info'    => array(
									'alt' => __( 'Centered Info', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_info_view_2.jpg',
								),
								'bottom-info'      => array(
									'alt' => __( 'Bottom Info', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_info_view_3.jpg',
								),
								'bottom-info-dark' => array(
									'alt' => __( 'Bottom Info Dark', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_info_view_4.jpg',
								),
								'hide-info-hover'  => array(
									'alt' => __( 'Hide Info Hover', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_info_view_5.jpg',
								),
								'plus-icon'        => array(
									'alt' => __( 'Plus Icon', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_info_view_6.jpg',
								),
							),
							'default'  => '',
						),
						array(
							'id'       => 'portfolio-archive-thumb-style',
							'type'     => 'button_set',
							'title'    => __( 'Info View Type Style', 'porto' ),
							'subtitle' => __( 'Not to show info or show a plus icon instead for even items.', 'porto' ),
							'options'  => array(
								''                    => __( 'None', 'porto' ),
								'alternate-info'      => __( 'Alternate', 'porto' ),
								'alternate-with-plus' => __( 'Alternate with Plus', 'porto' ),
							),
							'default'  => '',
						),
						array(
							'id'       => 'portfolio-archive-thumb-bg',
							'type'     => 'button_set',
							'title'    => __( 'Image Overlay Background', 'porto' ),
							'subtitle' => __( 'Controls the overlay background of featured image.', 'porto' ),
							'options'  => array(
								''                => __( 'Darken', 'porto' ),
								'lighten'         => __( 'Lighten', 'porto' ),
								'hide-wrapper-bg' => __( 'Transparent', 'porto' ),
							),
							'default'  => 'lighten',
						),
						array(
							'id'       => 'portfolio-archive-thumb-image',
							'type'     => 'button_set',
							'title'    => __( 'Hover Image Effect', 'porto' ),
							'subtitle' => __( 'Controls the hover effect of image.', 'porto' ),
							'options'  => array(
								''          => __( 'Zoom', 'porto' ),
								'slow-zoom' => __( 'Slow Zoom', 'porto' ),
								'no-zoom'   => __( 'No Zoom', 'porto' ),
							),
							'default'  => '',
						),
						array(
							'id'       => 'portfolio-archive-image-counter',
							'type'     => 'switch',
							'title'    => __( 'Image Counter', 'porto' ),
							'subtitle' => __( 'Show the featured image count.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Show', 'porto' ),
							'off'      => __( 'Hide', 'porto' ),
						),

						array(
							'id'       => 'portfolio-archive-all-images',
							'type'     => 'switch',
							'title'    => __( 'Show More Featured Images in Slideshow', 'porto' ),
							'required' => array( 'portfolio-archive-link-zoom', 'equals', false ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'portfolio-archive-images-count',
							'type'     => 'text',
							'title'    => __( 'Featured Images Count', 'porto' ),
							'required' => array( 'portfolio-archive-all-images', 'equals', true ),
							'default'  => '2',
						),

						array(
							'id'     => 'desc_info_portfolio_content',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>View Info:</b> If you use <span>type builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),

						array(
							'id'      => 'portfolio-show-testimonial',
							'type'    => 'switch',
							'title'   => __( 'Show Author Testimonial', 'porto' ),
							'desc'    => __( 'If yes, it will show the testimonial after meta section if it exists.', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),

						array(
							'id'      => 'portfolio-show-content',
							'type'    => 'switch',
							'title'   => __( 'Show Content Section', 'porto' ),
							'desc'    => __( 'If yes, it will show the portfolio content in archive layout. If no, it will not show the content.', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),

						array(
							'id'       => 'portfolio-excerpt',
							'type'     => 'switch',
							'title'    => __( 'Show Excerpt', 'porto' ),
							'desc'     => __( 'If yes, it will show the excerpt in "Medium", "Large", "Full" archive layout. If no, will show the content.', 'porto' ),
							'default'  => true,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
							'required' => array( 'portfolio-show-content', 'equals', true ),
						),
						array(
							'id'       => 'portfolio-excerpt-length',
							'type'     => 'text',
							'required' => array( 'portfolio-excerpt', 'equals', true ),
							'title'    => __( 'Excerpt Length', 'porto' ),
							'desc'     => __( 'The number of words', 'porto' ),
							'default'  => '80',
						),

						array(
							'id'       => 'portfolio-archive-readmore',
							'type'     => 'switch',
							'title'    => __( 'Show "Read More" Link', 'porto' ),
							'desc'     => __( 'Show "Read More" link in "Out of Image" view type.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
							'required' => array( 'portfolio-grid-view', 'equals', 'outimage' ),
						),
						array(
							'id'          => 'portfolio-archive-readmore-label',
							'type'        => 'text',
							'title'       => __( '"Read More" Label', 'porto' ),
							'required'    => array( 'portfolio-archive-readmore', 'equals', true ),
							'placeholder' => __( 'View Project...', 'porto' ),
						),
						array(
							'id'     => 'desc_info_portfolio_link',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Link & Lightbox Icon:</b> If you use <span>type builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'       => 'portfolio-archive-link-zoom',
							'type'     => 'switch',
							'title'    => __( 'Enable Image Lightbox instead of Portfolio Link', 'porto' ),
							'subtitle' => __( 'Turn on to enable the image lightbox instead of link.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'portfolio-archive-img-lightbox-thumb',
							'type'     => 'button_set',
							'title'    => __( 'Select Style', 'porto' ),
							'required' => array( 'portfolio-archive-link-zoom', 'equals', true ),
							'options'  => array(
								''           => __( 'Without Thumbs', 'porto' ),
								'with-thumb' => __( 'With Thumbs', 'porto' ),
							),
							'default'  => '',
						),
						array(
							'id'       => 'portfolio-archive-link',
							'type'     => 'switch',
							'title'    => __( 'Show Link Icon', 'porto' ),
							'subtitle' => __( 'Turn on to show link icon in portfolio type.', 'porto' ),
							'required' => array( 'portfolio-archive-link-zoom', 'equals', false ),
							'default'  => true,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),

						array(
							'id'       => 'portfolio-archive-zoom',
							'type'     => 'switch',
							'title'    => __( 'Show Image Lightbox Icon', 'porto' ),
							'subtitle' => __( 'Turn on to show lightbox icon in portfolio type.', 'porto' ),
							'required' => array( 'portfolio-archive-link-zoom', 'equals', false ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'portfolio-external-link',
							'type'     => 'switch',
							'title'    => __( 'Show External Link instead of Portfolio Link', 'porto' ),
							'subtitle' => __( 'Determines the permalink with meta box portfolio link.', 'porto' ),
							'required' => array( 'portfolio-archive-link-zoom', 'equals', false ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
					),
				);
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Single Portfolio', 'porto' ),
					'fields'     => array(
						array(
							'id'    => 'desc_info_single_portfolio',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Single</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $single_url, $type_url ),
								array(
									'strong' => array(),
									'b'      => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'      => 'portfolio-single-layout',
							'type'    => 'image_select',
							'title'   => __( 'Page Layout', 'porto' ),
							'options' => $page_layouts,
							'default' => 'fullwidth',
						),
						array(
							'id'       => 'portfolio-single-sidebar',
							'type'     => 'select',
							'title'    => __( 'Select Sidebar', 'porto' ),
							'required' => array( 'portfolio-single-layout', 'equals', $sidebars ),
							'data'     => 'sidebars',
						),
						array(
							'id'       => 'portfolio-single-sidebar2',
							'type'     => 'select',
							'title'    => __( 'Select Sidebar 2', 'porto' ),
							'required' => array( 'portfolio-single-layout', 'equals', $both_sidebars ),
							'data'     => 'sidebars',
						),
						array(
							'id'    => 'portfolio-banner-block',
							'type'  => 'text',
							'title' => __( 'Global Banner Block', 'porto' ),
							'desc'  => __( 'Please input comma separated block slug names. You can create a block in <strong>Porto / Templates Builder / Block / Add New</strong>.', 'porto' ),
						),
						array(
							'id'        => 'portfolio-content_bottom',
							'type'      => 'text',
							'title'     => __( 'Content Bottom Block', 'porto' ),
							'desc'      => __( 'Please input comma separated block slug names. You can create a block in <strong>Porto / Templates Builder / Block / Add New</strong>.', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'      => 'portfolio-content-layout',
							'type'    => 'image_select',
							'title'   => __( 'Portfolio Layout', 'porto' ),
							'options' => array(
								'medium'      => array(
									'alt' => __( 'Medium Slider', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_single_style_1.jpg',
								),
								'large'       => array(
									'alt' => __( 'Large Slider', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_single_style_2.jpg',
								),
								'full'        => array(
									'alt' => __( 'Full Slider', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_single_style_3.jpg',
								),
								'gallery'     => array(
									'alt' => __( 'Gallery', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_single_style_4.jpg',
								),
								'carousel'    => array(
									'alt' => __( 'Carousel', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_single_style_5.jpg',
								),
								'medias'      => array(
									'alt' => __( 'Medias', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_single_style_6.jpg',
								),
								'full-video'  => array(
									'alt' => __( 'Full Width Video', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_single_style_7.jpg',
								),
								'masonry'     => array(
									'alt' => __( 'Masonry Images', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_single_style_8.jpg',
								),
								'full-images' => array(
									'alt' => __( 'Full Images', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_single_style_9.jpg',
								),
								'extended'    => array(
									'alt' => __( 'Extended', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_single_style_10.jpg',
								),
							),
							'default' => 'medium',
						),
						array(
							'id'       => 'desc_info_rs_carousel',
							'type'     => 'info',
							'required' => array( 'portfolio-content-layout', 'equals', 'carousel' ),
							'desc'     => wp_kses(
								__( 'Please install the <a href="https://www.sliderrevolution.com/" target="_blank">Slider Revolution</a>.', 'porto' ),
								array(
									'a' => array(
										'href'   => array(),
										'target' => array(),
									),
								)
							),
						),
						array(
							'id'       => 'portfolio-slider',
							'type'     => 'image_select',
							'title'    => __( 'Slider Type', 'porto' ),
							'required' => array( 'portfolio-content-layout', 'equals', array( 'medium', 'large', 'full' ) ),
							'options'  => array(
								'without-thumbs' => array(
									'alt' => __( 'Without Thumbs', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_slideshow_1.jpg',
								),
								'with-thumbs'    => array(
									'alt' => __( 'With Thumbs', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_slideshow_2.jpg',
								),
							),
							'default'  => 'without-thumbs',
						),
						array(
							'id'       => 'portfolio-slider-thumbs-count',
							'type'     => 'text',
							'title'    => __( 'Slider Thumbs Count', 'porto' ),
							'required' => array( 'portfolio-slider', 'equals', array( 'with-thumbs' ) ),
							'default'  => '4',
						),
						array(
							'id'      => 'portfolio-share',
							'type'    => 'switch',
							'title'   => __( 'Show Social Share Links', 'porto' ),
							'desc'    => __( 'Turn on to show social share links.', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'portfolio-author',
							'type'    => 'switch',
							'title'   => __( 'Show Author Info', 'porto' ),
							'desc'    => __( 'Turn on to show author info.', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'portfolio-comments',
							'type'    => 'switch',
							'title'   => __( 'Show Comments', 'porto' ),
							'desc'    => __( 'Turn on to show comments.', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'        => 'portfolio-page-nav',
							'type'      => 'switch',
							'title'     => __( 'Show Navigation', 'porto' ),
							'desc'      => __( 'Show list and title, next/prev links.', 'porto' ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'postMessage',
						),
						array(
							'id'       => 'portfolio-image-count',
							'type'     => 'switch',
							'title'    => __( 'Show Image Count', 'porto' ),
							'subtitle' => __( 'Show count when the single layout is full or full images. And also metabox option "Change Featured Image" is set.', 'porto' ),
							'desc'     => __( 'Turn on to show image count.', 'porto' ),
							'default'  => true,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'     => 'desc_info_related_portfolio',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Related Portfolios:</b> If you use <span>single builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'      => 'portfolio-related',
							'type'    => 'switch',
							'title'   => __( 'Show Related Portfolios', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'portfolio-related-count',
							'type'     => 'text',
							'required' => array( 'portfolio-related', 'equals', true ),
							'title'    => __( 'Related Portfolios Count', 'porto' ),
							'desc'     => __( 'If you want to show all the related portfolios, please input "-1".', 'porto' ),
							'default'  => '10',
						),
						array(
							'id'       => 'portfolio-related-orderby',
							'type'     => 'button_set',
							'required' => array( 'portfolio-related', 'equals', true ),
							'title'    => __( 'Related Portfolios Order by', 'porto' ),
							'options'  => array(
								'none'          => __( 'None', 'porto' ),
								'rand'          => __( 'Random', 'porto' ),
								'date'          => __( 'Date', 'porto' ),
								'ID'            => __( 'ID', 'porto' ),
								'modified'      => __( 'Modified Date', 'porto' ),
								'comment_count' => __( 'Comment Count', 'porto' ),
							),
							'default'  => 'rand',
						),
						array(
							'id'       => 'portfolio-related-cols',
							'type'     => 'button_set',
							'required' => array( 'portfolio-related', 'equals', true ),
							'title'    => __( 'Related Portfolios Columns', 'porto' ),
							'desc'     => __( 'reduce one column in left or right sidebar layout', 'porto' ),
							'options'  => array(
								'4' => '4',
								'3' => '3',
								'2' => '2',
							),
							'default'  => '4',
						),

					),
				);
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Related Portfolio Carousel', 'porto' ),
					'fields'     => array(
						array(
							'id'    => 'desc_info_single_portfolio_carousel',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Single</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily.', 'porto' ), $single_url, $type_url ),
								array(
									'strong' => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'      => 'portfolio-related-style',
							'type'    => 'image_select',
							'title'   => __( 'Portfolio Style', 'porto' ),
							'options' => array(
								''         => array(
									'alt' => __( 'Default', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_archive_view_1.jpg',
								),
								'full'     => array(
									'alt' => __( 'No Margin', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_archive_view_2.jpg',
								),
								'outimage' => array(
									'alt' => __( 'Out of Image', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_archive_view_3.jpg',
								),
							),
							'default' => '',
						),
						array(
							'id'       => 'portfolio-related-thumb',
							'type'     => 'image_select',
							'title'    => __( 'Info View Type', 'porto' ),
							'required' => array( 'portfolio-related-style', '!=', 'outimage' ),
							'options'  => array(
								''                 => array(
									'alt' => __( 'Left Info', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_info_view_1.jpg',
								),
								'centered-info'    => array(
									'alt' => __( 'Centered Info', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_info_view_2.jpg',
								),
								'bottom-info'      => array(
									'alt' => __( 'Bottom Info', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_info_view_3.jpg',
								),
								'bottom-info-dark' => array(
									'alt' => __( 'Bottom Info Dark', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_info_view_4.jpg',
								),
								'hide-info-hover'  => array(
									'alt' => __( 'Hide Info Hover', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/portfolio_info_view_5.jpg',
								),
							),
							'default'  => '',
						),
						array(
							'id'       => 'portfolio-related-thumb-bg',
							'type'     => 'button_set',
							'title'    => __( 'Image Overlay Background', 'porto' ),
							'subtitle' => __( 'Controls the overlay background of featured image.', 'porto' ),
							'options'  => array(
								''                => __( 'Darken', 'porto' ),
								'lighten'         => __( 'Lighten', 'porto' ),
								'hide-wrapper-bg' => __( 'Transparent', 'porto' ),
							),
							'default'  => 'lighten',
						),
						array(
							'id'       => 'portfolio-related-thumb-image',
							'type'     => 'button_set',
							'title'    => __( 'Hover Image Effect', 'porto' ),
							'subtitle' => __( 'Controls the hover effect of image.', 'porto' ),
							'options'  => array(
								''        => __( 'Zoom', 'porto' ),
								'no-zoom' => __( 'No Zoom', 'porto' ),
							),
							'default'  => '',
						),
						array(
							'id'       => 'portfolio-related-link',
							'type'     => 'switch',
							'title'    => __( 'Show Link Icon', 'porto' ),
							'subtitle' => __( 'Turn on to show link icon in related portfolio.', 'porto' ),
							'default'  => true,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'portfolio-related-show-content',
							'type'     => 'switch',
							'title'    => __( 'Show Excerpt Content', 'porto' ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
							'required' => array( 'portfolio-related-style', 'equals', 'outimage' ),
						),
					),
				);

				// Event
				$event_options = array(
					'id'         => 'event-settings',
					'icon'       => 'Simple-Line-Icons-event',
					'icon_class' => '',
					'title'      => __( 'Event', 'porto' ),
					'customizer' => false,
					'fields'     => array(
						array(
							'id'       => 'enable-event',
							'type'     => 'switch',
							'title'    => __( 'Event Content Type', 'porto' ),
							'subtitle' => __( 'Enable to provide Event type.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Enable', 'porto' ),
							'off'      => __( 'Disable', 'porto' ),
						),
						array(
							'id'          => 'event-slug-name',
							'type'        => 'text',
							'title'       => __( 'Slug Name', 'porto' ),
							'subtitle'    => __( 'This option changes the permalink when you use the permalink type as %postname%. Make sure to regenerate permalinks.', 'porto' ),
							'placeholder' => 'event',
						),
						array(
							'id'          => 'event-name',
							'type'        => 'text',
							'title'       => __( 'Name', 'porto' ),
							'subtitle'    => __( 'A plural descriptive name for the post type marked for translation.', 'porto' ),
							'placeholder' => __( 'Events', 'porto' ),
						),
						array(
							'id'          => 'event-singular-name',
							'type'        => 'text',
							'title'       => __( 'Singular Name', 'porto' ),
							'subtitle'    => __( 'Name for one object of this post type.', 'porto' ),
							'placeholder' => __( 'Event', 'porto' ),
						),
					),
				);
				if ( $options_style ) {
					$this->sections[] = $event_options;
				}
				$this->sections[] = $this->add_customizer_field(
					array(
						'id'         => 'customizer-event-settings',
						'title'      => __( 'Event', 'porto' ),
						'icon_class' => '',
						'icon'       => 'Simple-Line-Icons-event',
					),
					$options_style,
					$event_options
				);
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'Event Archives', 'porto' ),
						'fields'     => array(
							array(
								'id'    => 'desc_info_event_archive',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Archive</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $archive_url, $type_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'       => 'event-archive-page',
								'type'     => 'select',
								'data'     => 'page',
								'title'    => __( 'Events Page', 'porto' ),
								'subtitle' => __( 'Select a event archive page.', 'porto' ),
							),
							array(
								'id'      => 'event-title',
								'type'    => 'text',
								'title'   => __( 'Page Title', 'porto' ),
								'default' => 'Our <strong>Projects</strong>',
							),
							array(
								'id'      => 'event-sub-title',
								'type'    => 'textarea',
								'title'   => __( 'Page Sub Title', 'porto' ),
								'default' => '',
							),
							array(
								'id'      => 'event-archive-layout',
								'type'    => 'button_set',
								'title'   => __( 'Page Layout', 'porto' ),
								'options' => array(
									'list' => __( 'List', 'porto' ),
									'grid' => __( 'Grid', 'porto' ),
								),
								'default' => 'list',
							),

							array(
								'id'       => 'event-archive-countdown',
								'type'     => 'switch',
								'title'    => __( 'Show Event Countdown', 'porto' ),
								'required' => array( 'event-archive-layout', 'equals', 'grid' ),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),

							array(
								'id'      => 'event-excerpt',
								'type'    => 'switch',
								'title'   => __( 'Show Excerpt', 'porto' ),
								'desc'    => __( 'If yes, will show the excerpt in archive layout. If no, it will show the content.', 'porto' ),
								'default' => true,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'event-excerpt-length',
								'type'     => 'text',
								'required' => array( 'event-excerpt', 'equals', true ),
								'title'    => __( 'Excerpt Length', 'porto' ),
								'desc'     => __( 'The number of words', 'porto' ),
								'default'  => '80',
							),
							array(
								'id'      => 'event-readmore',
								'type'    => 'switch',
								'title'   => __( 'Show Read More button', 'porto' ),
								'default' => false,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
						),
					),
					$options_style
				);
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'Single Event', 'porto' ),
						'fields'     => array(
							array(
								'id'    => 'desc_info_single_event',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Single</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $single_url, $type_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'    => 'event-banner-block',
								'type'  => 'text',
								'title' => __( 'Global Banner Block', 'porto' ),
								'desc'  => __( 'Please input comma separated block slug names. You can create a block in <strong>Porto / Templates Builder / Block / Add New</strong>.', 'porto' ),
							),
							array(
								'id'      => 'event-single-countdown',
								'type'    => 'switch',
								'title'   => __( 'Show Event Countdown', 'porto' ),
								'default' => true,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),

						),
					),
					$options_style
				);

				// Member
				$member_options = array(
					'icon'       => 'Simple-Line-Icons-people',
					'icon_class' => '',
					'title'      => __( 'Member', 'porto' ),
					'customizer' => false,
					'fields'     => array(
						array(
							'id'       => 'enable-member',
							'type'     => 'switch',
							'title'    => __( 'Member Content Type', 'porto' ),
							'subtitle' => __( 'Enable to provide Member type.', 'porto' ),
							'default'  => true,
							'on'       => __( 'Enable', 'porto' ),
							'off'      => __( 'Disable', 'porto' ),
						),
						array(
							'id'          => 'member-slug-name',
							'type'        => 'text',
							'title'       => __( 'Slug Name', 'porto' ),
							'subtitle'    => __( 'This option changes the permalink when you use the permalink type as %postname%. Make sure to regenerate permalinks.', 'porto' ),
							'placeholder' => 'member',
						),
						array(
							'id'          => 'member-name',
							'type'        => 'text',
							'title'       => __( 'Name', 'porto' ),
							'subtitle'    => __( 'A plural descriptive name for the post type marked for translation.', 'porto' ),
							'placeholder' => __( 'Members', 'porto' ),
						),
						array(
							'id'          => 'member-singular-name',
							'type'        => 'text',
							'title'       => __( 'Singular Name', 'porto' ),
							'subtitle'    => __( 'Name for one object of this post type.', 'porto' ),
							'placeholder' => __( 'Member', 'porto' ),
						),
						array(
							'id'          => 'member-cat-slug-name',
							'type'        => 'text',
							'title'       => __( 'Category Slug Name', 'porto' ),
							'subtitle'    => __( 'The slug name of the taxonomy.', 'porto' ),
							'placeholder' => 'member_cat',
						),
					),
				);
				if ( $options_style ) {
					$this->sections[] = $member_options;
				}
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon'       => 'Simple-Line-Icons-people',
						'icon_class' => '',
						'id'         => 'customizer-member-settings',
						'title'      => __( 'Member', 'porto' ),
					),
					$options_style
				);
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'General', 'porto' ),
						'id'         => 'memeber-general',
						'fields'     => array(
							array(
								'id'       => 'member-archive-page',
								'type'     => 'select',
								'data'     => 'page',
								'title'    => __( 'Members Page', 'porto' ),
								'subtitle' => __( 'Select a member archive page.', 'porto' ),
							),
							array(
								'id'       => 'member-zoom',
								'type'     => 'switch',
								'title'    => __( 'Image Lightbox', 'porto' ),
								'subtitle' => __( 'If you use single & type builder, you should consider the options of builder widgets.', 'porto' ),
								'desc'     => __( 'Turn on to enable the lightbox on single and archive page for the main featured images.', 'porto' ),
								'default'  => true,
								'on'       => __( 'Enable', 'porto' ),
								'off'      => __( 'Disable', 'porto' ),
							),
							array(
								'id'       => 'member-social-target',
								'type'     => 'switch',
								'title'    => __( 'Show Social Link as target="_blank"', 'porto' ),
								'subtitle' => __( 'If you use single & type builder, you should consider the options of builder widgets.', 'porto' ),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'member-social-nofollow',
								'type'     => 'switch',
								'title'    => __( 'Add rel="nofollow" to social links', 'porto' ),
								'subtitle' => __( 'If you use single & type builder, you should consider the options of builder widgets.', 'porto' ),
								'desc'     => __( 'Turn on to add "nofollow" attribute to member social links.', 'porto' ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
						),
					),
					$options_style,
					$member_options
				);
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'Member Archives', 'porto' ),
						'fields'     => array(
							array(
								'id'    => 'desc_info_member_archive',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Archive</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $archive_url, $type_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'member-title',
								'type'    => 'text',
								'title'   => __( 'Page Title', 'porto' ),
								'default' => 'Meet the <strong>Team</strong>',
							),
							array(
								'id'      => 'member-sub-title',
								'type'    => 'textarea',
								'title'   => __( 'Page Sub Title', 'porto' ),
								'default' => '',
							),
							array(
								'id'      => 'member-archive-layout',
								'type'    => 'image_select',
								'title'   => __( 'Page Layout', 'porto' ),
								'options' => $page_layouts,
								'default' => 'fullwidth',
							),
							array(
								'id'       => 'member-archive-sidebar',
								'type'     => 'select',
								'title'    => __( 'Select Sidebar', 'porto' ),
								'required' => array( 'member-archive-layout', 'equals', $sidebars ),
								'data'     => 'sidebars',
							),
							array(
								'id'       => 'member-archive-sidebar2',
								'type'     => 'select',
								'title'    => __( 'Select Sidebar 2', 'porto' ),
								'required' => array( 'member-archive-layout', 'equals', $both_sidebars ),
								'data'     => 'sidebars',
							),
							array(
								'id'      => 'member-archive-ajax',
								'type'    => 'switch',
								'title'   => __( 'Enable Ajax Load', 'porto' ),
								'desc'    => __( 'If enabled, member content should be displayed above the members or on modal when you click member item in the list.', 'porto' ),
								'default' => false,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),

							array(
								'id'       => 'member-archive-ajax-modal',
								'type'     => 'switch',
								'title'    => __( 'Ajax Load on Modal', 'porto' ),
								'desc'     => __( 'If enabled, member content should be displayed on modal when you click member item in the list.', 'porto' ),
								'required' => array( 'member-archive-ajax', 'equals', true ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'member-infinite',
								'type'     => 'button_set',
								'title'    => __( 'Pagination Style', 'porto' ),
								'subtitle' => __( 'If you use Archive builder, you should consider the options of archive post grid widget.', 'porto' ),
								'desc'     => __( 'Controls the pagination type for member archive page.', 'porto' ),
								'options'  => array(
									''         => __( 'Default', 'porto' ),
									'ajax'     => __( 'Ajax Pagination', 'porto' ),
									'infinite' => __( 'Infinite Scroll', 'porto' ),
								),
								'default'  => 'infinite',
							),
							array(
								'id'     => 'desc_info_category_member_archive',
								'type'   => 'info',
								'desc'   => wp_kses(
									__( '<b>Category Filter in Member Page:</b> If you use <span>archive builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
									array(
										'span' => array(),
										'b'    => array(),
									)
								),
								'notice' => false,
								'class'  => 'porto-redux-section',
							),
							array(
								'id'       => 'member-cat-sort-pos',
								'type'     => 'button_set',
								'title'    => __( 'Categories Filter Position', 'porto' ),
								'subtitle' => __( 'If you use Archive builder, you should consider the options of archive post grid widget.', 'porto' ),
								'options'  => $porto_categories_sort_pos,
								'default'  => 'content',
							),
							array(
								'id'       => 'member-cat-sort-style',
								'type'     => 'button_set',
								'title'    => __( 'Filter Style', 'porto' ),
								'subtitle' => __( 'If you use Archive builder, you should consider the options of archive post grid widget.', 'porto' ),
								'options'  => array(
									''        => __( 'Style 1', 'porto' ),
									'style-2' => __( 'Style 2', 'porto' ),
									'style-3' => __( 'Style 3', 'porto' ),
								),
								'required' => array( 'member-cat-sort-pos', 'equals', array( 'content' ) ),
								'default'  => '',
							),
							array(
								'id'       => 'member-cat-ft',
								'type'     => 'button_set',
								'title'    => __( 'Filter Type', 'porto' ),
								'subtitle' => __( 'If you use Archive builder, you should consider the options of archive post grid widget.', 'porto' ),
								'options'  => array(
									''     => __( 'Filter using Javascript/CSS', 'porto' ),
									'ajax' => __( 'Ajax Loading', 'porto' ),
								),
								'default'  => '',
								'required' => array(
									array( 'member-infinite', '!=', '' ),
									array( 'member-cat-sort-pos', '!=', 'hide' ),
								),
							),
							array(
								'id'       => 'member-cat-orderby',
								'type'     => 'button_set',
								'title'    => __( 'Sort Categories Order By', 'porto' ),
								'desc'     => __( 'Defines how categories should be ordered.', 'porto' ),
								'options'  => $porto_categories_orderby,
								'default'  => 'name',
								'required' => array( 'member-cat-sort-pos', '!=', 'hide' ),
							),
							array(
								'id'       => 'member-cat-order',
								'type'     => 'button_set',
								'title'    => __( 'Sort Order for Categories', 'porto' ),
								'desc'     => __( 'Defines the sorting order of categories.', 'porto' ),
								'options'  => $porto_categories_order,
								'default'  => 'asc',
								'required' => array( 'member-cat-sort-pos', '!=', 'hide' ),
							),
							array(
								'id'     => 'desc_info_member_type',
								'type'   => 'info',
								'desc'   => wp_kses(
									__( '<b>Member Type:</b> If you use <span>type builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
									array(
										'span' => array(),
										'b'    => array(),
									)
								),
								'notice' => false,
								'class'  => 'porto-redux-section',
							),
							array(
								'id'       => 'member-view-type',
								'type'     => 'image_select',
								'title'    => __( 'View Type', 'porto' ),
								'subtitle' => __( 'Controls the member type.', 'porto' ),
								'default'  => '',
								'options'  => array(
									''         => array(
										'title' => __( 'Type 1', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/images/member_archive_view_1.jpg',
									),
									'2'        => array(
										'title' => __( 'Type 2', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/images/member_archive_view_2.jpg',
									),
									'3'        => array(
										'title' => __( 'Type 3', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/images/member_archive_view_3.jpg',
									),
									'advanced' => array(
										'title' => __( 'Advanced Type', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/images/member_advanced.svg',
									),
								),
							),
							array(
								'id'       => 'member-columns',
								'type'     => 'button_set',
								'title'    => __( 'Member Columns', 'porto' ),
								'subtitle' => __( 'Controls the number of columns in the archive page.', 'porto' ),
								'options'  => array(
									'2' => __( '2 Columns', 'porto' ),
									'3' => __( '3 Columns', 'porto' ),
									'4' => __( '4 Columns', 'porto' ),
									'5' => __( '5 Columns', 'porto' ),
									'6' => __( '6 Columns', 'porto' ),
								),
								'default'  => '4',
								'required' => array( 'member-view-type', '!=', 'advanced' ),
							),
							array(
								'id'       => 'custom-member-zoom',
								'type'     => 'button_set',
								'title'    => __( 'Hover Image Effect', 'porto' ),
								'subtitle' => __( 'Select the hover effect type.', 'porto' ),
								'options'  => array(
									''        => __( 'Zoom', 'porto' ),
									'no_zoom' => __( 'No_Zoom', 'porto' ),
								),
								'default'  => '',
							),
							array(
								'id'      => 'member-image-size',
								'type'    => 'button_set',
								'title'   => __( 'Member Image Size', 'porto' ),
								'options' => array(
									''     => __( 'Static', 'porto' ),
									'full' => __( 'Full', 'porto' ),
								),
								'default' => '',
							),
							array(
								'id'       => 'member-archive-readmore',
								'type'     => 'switch',
								'title'    => __( 'Show "Read More" Link', 'porto' ),
								'subtitle' => __( 'Turn on to display the read more link.', 'porto' ),
								'desc'     => __( 'Show "Read More" link in "Type 2" view type.', 'porto' ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
								'required' => array( 'member-view-type', 'equals', 2 ),
							),
							array(
								'id'          => 'member-archive-readmore-label',
								'type'        => 'text',
								'title'       => __( '"Read More" Label', 'porto' ),
								'required'    => array( 'member-archive-readmore', 'equals', true ),
								'placeholder' => __( 'View More...', 'porto' ),
							),
							array(
								'id'       => 'member-external-link',
								'type'     => 'switch',
								'title'    => __( 'Show External Link instead of Member Link', 'porto' ),
								'subtitle' => __( 'Determines the permalink with meta box member link.', 'porto' ),
								'default'  => false,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'member-overview',
								'type'     => 'switch',
								'title'    => __( 'Show Overview', 'porto' ),
								'subtitle' => __( 'Turn on to display the overview.', 'porto' ),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'member-excerpt',
								'type'     => 'switch',
								'title'    => __( 'Show Overview Excerpt', 'porto' ),
								'subtitle' => __( 'Turn on to display the overview excerpt.', 'porto' ),
								'required' => array( 'member-overview', 'equals', true ),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'member-excerpt-length',
								'type'     => 'text',
								'required' => array( 'member-excerpt', 'equals', true ),
								'title'    => __( 'Excerpt Length', 'porto' ),
								'desc'     => __( 'The number of words', 'porto' ),
								'default'  => '15',
							),
							array(
								'id'       => 'member-socials',
								'type'     => 'switch',
								'title'    => __( 'Show Social Links', 'porto' ),
								'subtitle' => __( 'Turn on to display the social links.', 'porto' ),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),

							array(
								'id'       => 'member-social-link-style',
								'type'     => 'button_set',
								'required' => array( 'member-socials', 'equals', true ),
								'title'    => __( 'Social Links Style', 'porto' ),
								'subtitle' => __( 'Controls the social link style.', 'porto' ),
								'default'  => '',
								'options'  => array(
									''        => __( 'Default', 'porto' ),
									'advance' => __( 'Advance', 'porto' ),
								),
							),
						),
					),
					$options_style
				);
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'Single Member', 'porto' ),
						'fields'     => array(
							array(
								'id'    => 'desc_info_single_member',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Single</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $single_url, $type_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'member-single-layout',
								'type'    => 'image_select',
								'title'   => __( 'Page Layout', 'porto' ),
								'options' => $page_layouts,
								'default' => 'fullwidth',
							),
							array(
								'id'       => 'member-single-sidebar',
								'type'     => 'select',
								'title'    => __( 'Select Sidebar', 'porto' ),
								'required' => array( 'member-single-layout', 'equals', $sidebars ),
								'data'     => 'sidebars',
							),
							array(
								'id'       => 'member-single-sidebar2',
								'type'     => 'select',
								'title'    => __( 'Select Sidebar 2', 'porto' ),
								'required' => array( 'member-single-layout', 'equals', $both_sidebars ),
								'data'     => 'sidebars',
							),
							array(
								'id'      => 'member-page-style',
								'type'    => 'switch',
								'title'   => __( 'Page Style', 'porto' ),
								'desc'    => __( 'Controls the style of page layout.', 'porto' ),
								'default' => false,
								'on'      => __( 'Advance', 'porto' ),
								'off'     => __( 'Default', 'porto' ),
							),
							array(
								'id'    => 'member-banner-block',
								'type'  => 'text',
								'title' => __( 'Global Banner Block', 'porto' ),
								'desc'  => __( 'Please input comma separated block slug names. You can create a block in <strong>Porto / Templates Builder / Block / Add New</strong>.', 'porto' ),
							),
							array(
								'id'        => 'member-content_bottom',
								'type'      => 'text',
								'title'     => __( 'Content Bottom Block', 'porto' ),
								'desc'      => __( 'Please input comma separated block slug names. You can create a block in <strong>Porto / Templates Builder / Block / Add New</strong>.', 'porto' ),
								'transport' => 'refresh',
							),
							array(
								'id'     => 'desc_info_social_single_member',
								'type'   => 'info',
								'desc'   => wp_kses(
									__( '<b>Social Links in Single Member:</b> If you use <span>single builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
									array(
										'span' => array(),
										'b'    => array(),
									)
								),
								'notice' => false,
								'class'  => 'porto-redux-section',
							),
							array(
								'id'       => 'single-member-socials',
								'type'     => 'switch',
								'title'    => __( 'Show Social Links', 'porto' ),
								'subtitle' => __( 'Turn on to display social links in single member page.', 'porto' ),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'single-member-social-link-style',
								'type'     => 'button_set',
								'required' => array( 'single-member-socials', 'equals', true ),
								'title'    => __( 'Social Links Style', 'porto' ),
								'subtitle' => __( 'Controls the style of social links.', 'porto' ),
								'default'  => '',
								'options'  => array(
									''        => __( 'Default', 'porto' ),
									'advance' => __( 'Advance', 'porto' ),
								),
							),
							array(
								'id'       => 'member-socials-pos',
								'type'     => 'button_set',
								'required' => array( 'single-member-social-link-style', 'equals', '' ),
								'title'    => __( 'Social Links Position', 'porto' ),
								'subtitle' => __( 'Controls the position of social links in single member page.', 'porto' ),
								'options'  => array(
									'before'      => __( 'Before Overview', 'porto' ),
									''            => __( 'After Overview', 'porto' ),
									'below_thumb' => __( 'Below Member Image', 'porto' ),
								),
								'default'  => '',
							),
							array(
								'id'     => 'desc_info_related_member',
								'type'   => 'info',
								'desc'   => wp_kses(
									__( '<b>Related Members:</b> If you use <span>single builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
									array(
										'span' => array(),
										'b'    => array(),
									)
								),
								'notice' => false,
								'class'  => 'porto-redux-section',
							),
							array(
								'id'       => 'member-related',
								'type'     => 'switch',
								'title'    => __( 'Show Related Members', 'porto' ),
								'subtitle' => __( 'Turn on to show related members.', 'porto' ),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'member-related-count',
								'type'     => 'text',
								'required' => array( 'member-related', 'equals', true ),
								'title'    => __( 'Related Members Count', 'porto' ),
								'desc'     => __( 'If you want to show all the related members, please input "-1".', 'porto' ),
								'default'  => '10',
							),
							array(
								'id'       => 'member-related-orderby',
								'type'     => 'button_set',
								'required' => array( 'member-related', 'equals', true ),
								'title'    => __( 'Related Members Order by', 'porto' ),
								'desc'     => __( 'Defines how members should be ordered.', 'porto' ),
								'options'  => array(
									'none'     => __( 'None', 'porto' ),
									'rand'     => __( 'Random', 'porto' ),
									'date'     => __( 'Date', 'porto' ),
									'ID'       => __( 'ID', 'porto' ),
									'modified' => __( 'Modified Date', 'porto' ),
								),
								'default'  => 'rand',
							),
							array(
								'id'       => 'member-related-cols',
								'type'     => 'button_set',
								'required' => array( 'member-related', 'equals', true ),
								'title'    => __( 'Related Members Columns', 'porto' ),
								'desc'     => __( 'reduce one column in left or right sidebar layout.', 'porto' ),
								'options'  => array(
									'4' => '4',
									'3' => '3',
									'2' => '2',
								),
								'default'  => '4',
							),
						),
					),
					$options_style
				);
			} else {
				// Blog
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon'       => 'Simple-Line-Icons-docs',
						'icon_class' => '',
						'title'      => __( 'Post', 'porto' ),
						'fields'     => array(
							array(
								'id'    => 'desc_info_post',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Archive</a>, <a href="%2$s" target="_blank">Single</a> & <a href="%3$s" target="_blank">Type</a> Builders help you to develop your site easily.', 'porto' ), $archive_url, $single_url, $type_url ),
									array(
										'strong' => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'desc_info_blog_sidebar',
								'type'    => 'info',
								'default' => '',
								'desc'    => wp_kses(
									sprintf(
										/* translators: %s: widgets url */
										__( 'You can control the blog sidebar and secondary sidebar in <a href="%s" target="_blank">here</a>.', 'porto' ),
										esc_url( admin_url( 'widgets.php' ) )
									),
									array(
										'a' => array(
											'href'   => array(),
											'target' => array(),
										),
									)
								),
							),
							array(
								'id'      => 'post-archive-layout',
								'type'    => 'image_select',
								'title'   => __( 'Post Archive Layout', 'porto' ),
								'options' => $page_layouts,
								'default' => 'right-sidebar',
							),
							array(
								'id'      => 'post-single-layout',
								'type'    => 'image_select',
								'title'   => __( 'Single Post Layout', 'porto' ),
								'options' => $page_layouts,
								'default' => 'right-sidebar',
							),
							array(
								'id'      => 'post-related-image-size',
								'type'    => 'dimensions',
								'title'   => __( 'Post Image Size', 'porto' ),
								'desc'    => __( 'Please regenerate all the thumbnails in <strong>Tools > Regen.Thumbnails</strong> after save changes.', 'porto' ),
								'default' => array(
									'width'  => '450',
									'height' => '231',
								),
							),
							array(
								'id'      => 'hot-label',
								'type'    => 'text',
								'title'   => __( '"HOT" Text', 'porto' ),
								'desc'    => __( 'sticky post label', 'porto' ),
								'default' => '',
							),
						),
					),
					$options_style
				);
				// Portfolio
				$portfolio_options = array(
					'icon'       => 'Simple-Line-Icons-picture',
					'icon_class' => '',
					'title'      => __( 'Portfolio', 'porto' ),
					'customizer' => false,
					'fields'     => array(
						array(
							'id'       => 'enable-portfolio',
							'type'     => 'switch',
							'title'    => __( 'Portfolio Content Type', 'porto' ),
							'subtitle' => __( 'Enable to provide Portfolio type.', 'porto' ),
							'default'  => true,
							'on'       => __( 'Enable', 'porto' ),
							'off'      => __( 'Disable', 'porto' ),
						),
						array(
							'id'          => 'portfolio-slug-name',
							'type'        => 'text',
							'title'       => __( 'Slug Name', 'porto' ),
							'subtitle'    => __( 'This option changes the permalink when you use the permalink type as %postname%. Make sure to regenerate permalinks.', 'porto' ),
							'placeholder' => 'portfolio',
						),
						array(
							'id'          => 'portfolio-name',
							'type'        => 'text',
							'title'       => __( 'Name', 'porto' ),
							'subtitle'    => __( 'A plural descriptive name for the post type marked for translation.', 'porto' ),
							'placeholder' => __( 'Portfolios', 'porto' ),
						),
						array(
							'id'          => 'portfolio-singular-name',
							'type'        => 'text',
							'title'       => __( 'Singular Name', 'porto' ),
							'subtitle'    => __( 'Name for one object of this post type.', 'porto' ),
							'placeholder' => __( 'Portfolio', 'porto' ),
						),
						array(
							'id'          => 'portfolio-cat-slug-name',
							'type'        => 'text',
							'title'       => __( 'Category Slug Name', 'porto' ),
							'subtitle'    => __( 'The slug name of the taxonomy.', 'porto' ),
							'placeholder' => 'portfolio_cat',
						),
						array(
							'id'          => 'portfolio-skill-slug-name',
							'type'        => 'text',
							'title'       => __( 'Skill Slug Name', 'porto' ),
							'subtitle'    => __( 'The slug name of the taxonomy: skill.', 'porto' ),
							'placeholder' => 'portfolio_skill',
						),
					),
				);

				$this->sections[] = $this->add_customizer_field(
					array(
						'icon'       => 'Simple-Line-Icons-picture',
						'icon_class' => '',
						'title'      => __( 'Portfolio', 'porto' ),
						'fields'     => array(
							array(
								'id'       => 'portfolio-archive-page',
								'type'     => 'select',
								'data'     => 'page',
								'title'    => __( 'Portfolios Page', 'porto' ),
								'subtitle' => __( 'Select a portfolio archive page.', 'porto' ),
							),
						),
					),
					$options_style,
					$portfolio_options
				);
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Portfolio Archives', 'porto' ),
					'fields'     => array(
						array(
							'id'    => 'desc_info_portfolio_archive',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Archive</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily.', 'porto' ), $archive_url, $type_url ),
								array(
									'strong' => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'      => 'portfolio-archive-layout',
							'type'    => 'image_select',
							'title'   => __( 'Page Layout', 'porto' ),
							'options' => $page_layouts,
							'default' => 'fullwidth',
						),
						array(
							'id'       => 'portfolio-archive-sidebar',
							'type'     => 'select',
							'title'    => __( 'Select Sidebar', 'porto' ),
							'required' => array( 'portfolio-archive-layout', 'equals', $sidebars ),
							'data'     => 'sidebars',
						),
						array(
							'id'       => 'portfolio-archive-sidebar2',
							'type'     => 'select',
							'title'    => __( 'Select Sidebar 2', 'porto' ),
							'required' => array( 'portfolio-archive-layout', 'equals', $both_sidebars ),
							'data'     => 'sidebars',
						),
					),
				);
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Single Portfolio', 'porto' ),
					'fields'     => array(
						array(
							'id'    => 'desc_info_single_portfolio',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Single</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily.', 'porto' ), $single_url, $type_url ),
								array(
									'strong' => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'      => 'portfolio-single-layout',
							'type'    => 'image_select',
							'title'   => __( 'Page Layout', 'porto' ),
							'options' => $page_layouts,
							'default' => 'fullwidth',
						),
						array(
							'id'       => 'portfolio-single-sidebar',
							'type'     => 'select',
							'title'    => __( 'Select Sidebar', 'porto' ),
							'required' => array( 'portfolio-single-layout', 'equals', $sidebars ),
							'data'     => 'sidebars',
						),
						array(
							'id'       => 'portfolio-single-sidebar2',
							'type'     => 'select',
							'title'    => __( 'Select Sidebar 2', 'porto' ),
							'required' => array( 'portfolio-single-layout', 'equals', $both_sidebars ),
							'data'     => 'sidebars',
						),
					),
				);

				// Event
				$event_options = array(
					'id'         => 'event-settings',
					'icon'       => 'Simple-Line-Icons-event',
					'icon_class' => '',
					'title'      => __( 'Event', 'porto' ),
					'customizer' => false,
					'fields'     => array(
						array(
							'id'    => 'desc_info_event',
							'type'  => 'info',
							'desc'  => wp_kses(
								/* translators: %s: Builder url */
								sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Archive</a>, <a href="%2$s" target="_blank">Single</a> & <a href="%3$s" target="_blank">Type</a> Builders help you to develop your site easily.', 'porto' ), $archive_url, $single_url, $type_url ),
								array(
									'strong' => array(),
									'a'      => array(
										'href'   => array(),
										'target' => array(),
										'class'  => array(),
									),
								)
							),
							'class' => 'porto-important-note',
						),
						array(
							'id'       => 'enable-event',
							'type'     => 'switch',
							'title'    => __( 'Event Content Type', 'porto' ),
							'subtitle' => __( 'Enable to provide Event type.', 'porto' ),
							'default'  => false,
							'on'       => __( 'Enable', 'porto' ),
							'off'      => __( 'Disable', 'porto' ),
						),
						array(
							'id'          => 'event-slug-name',
							'type'        => 'text',
							'title'       => __( 'Slug Name', 'porto' ),
							'subtitle'    => __( 'This option changes the permalink when you use the permalink type as %postname%. Make sure to regenerate permalinks.', 'porto' ),
							'placeholder' => 'event',
						),
						array(
							'id'          => 'event-name',
							'type'        => 'text',
							'title'       => __( 'Name', 'porto' ),
							'subtitle'    => __( 'A plural descriptive name for the post type marked for translation.', 'porto' ),
							'placeholder' => __( 'Events', 'porto' ),
						),
						array(
							'id'          => 'event-singular-name',
							'type'        => 'text',
							'title'       => __( 'Singular Name', 'porto' ),
							'subtitle'    => __( 'Name for one object of this post type.', 'porto' ),
							'placeholder' => __( 'Event', 'porto' ),
						),
					),
				);

				$this->sections[] = $this->add_customizer_field(
					array(
						'id'         => 'customizer-event-settings',
						'title'      => __( 'Event', 'porto' ),
						'icon_class' => '',
						'icon'       => 'Simple-Line-Icons-event',
						'fields'     => array(
							array(
								'id'       => 'event-archive-page',
								'type'     => 'select',
								'data'     => 'page',
								'title'    => __( 'Events Page', 'porto' ),
								'subtitle' => __( 'Select a event archive page.', 'porto' ),
							),
						),
					),
					$options_style,
					$event_options
				);
				// Member
				$member_options = array(
					'icon'       => 'Simple-Line-Icons-people',
					'icon_class' => '',
					'title'      => __( 'Member', 'porto' ),
					'customizer' => false,
					'fields'     => array(
						array(
							'id'       => 'enable-member',
							'type'     => 'switch',
							'title'    => __( 'Member Content Type', 'porto' ),
							'subtitle' => __( 'Enable to provide Member type.', 'porto' ),
							'default'  => true,
							'on'       => __( 'Enable', 'porto' ),
							'off'      => __( 'Disable', 'porto' ),
						),
						array(
							'id'          => 'member-slug-name',
							'type'        => 'text',
							'title'       => __( 'Slug Name', 'porto' ),
							'subtitle'    => __( 'This option changes the permalink when you use the permalink type as %postname%. Make sure to regenerate permalinks.', 'porto' ),
							'placeholder' => 'member',
						),
						array(
							'id'          => 'member-name',
							'type'        => 'text',
							'title'       => __( 'Name', 'porto' ),
							'subtitle'    => __( 'A plural descriptive name for the post type marked for translation.', 'porto' ),
							'placeholder' => __( 'Members', 'porto' ),
						),
						array(
							'id'          => 'member-singular-name',
							'type'        => 'text',
							'title'       => __( 'Singular Name', 'porto' ),
							'subtitle'    => __( 'Name for one object of this post type.', 'porto' ),
							'placeholder' => __( 'Member', 'porto' ),
						),
						array(
							'id'          => 'member-cat-slug-name',
							'type'        => 'text',
							'title'       => __( 'Category Slug Name', 'porto' ),
							'subtitle'    => __( 'The slug name of the taxonomy.', 'porto' ),
							'placeholder' => 'member_cat',
						),
					),
				);

				$this->sections[] = $this->add_customizer_field(
					array(
						'icon'       => 'Simple-Line-Icons-people',
						'icon_class' => '',
						'id'         => 'customizer-member-settings',
						'title'      => __( 'Member', 'porto' ),
						'fields'     => array(
							array(
								'id'       => 'member-archive-page',
								'type'     => 'select',
								'data'     => 'page',
								'title'    => __( 'Members Page', 'porto' ),
								'subtitle' => __( 'Select a member archive page.', 'porto' ),
							),
						),
					),
					$options_style,
					$member_options
				);
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'Member Archives', 'porto' ),
						'fields'     => array(
							array(
								'id'    => 'desc_info_member_archive',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Archive</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily.', 'porto' ), $archive_url, $type_url ),
									array(
										'strong' => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'member-archive-layout',
								'type'    => 'image_select',
								'title'   => __( 'Page Layout', 'porto' ),
								'options' => $page_layouts,
								'default' => 'fullwidth',
							),
							array(
								'id'       => 'member-archive-sidebar',
								'type'     => 'select',
								'title'    => __( 'Select Sidebar', 'porto' ),
								'required' => array( 'member-archive-layout', 'equals', $sidebars ),
								'data'     => 'sidebars',
							),
							array(
								'id'       => 'member-archive-sidebar2',
								'type'     => 'select',
								'title'    => __( 'Select Sidebar 2', 'porto' ),
								'required' => array( 'member-archive-layout', 'equals', $both_sidebars ),
								'data'     => 'sidebars',
							),
						),
					),
					$options_style
				);
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'Single Member', 'porto' ),
						'fields'     => array(
							array(
								'id'    => 'desc_info_single_member',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Single</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily.', 'porto' ), $single_url, $type_url ),
									array(
										'strong' => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'member-single-layout',
								'type'    => 'image_select',
								'title'   => __( 'Page Layout', 'porto' ),
								'options' => $page_layouts,
								'default' => 'fullwidth',
							),
							array(
								'id'       => 'member-single-sidebar',
								'type'     => 'select',
								'title'    => __( 'Select Sidebar', 'porto' ),
								'required' => array( 'member-single-layout', 'equals', $sidebars ),
								'data'     => 'sidebars',
							),
							array(
								'id'       => 'member-single-sidebar2',
								'type'     => 'select',
								'title'    => __( 'Select Sidebar 2', 'porto' ),
								'required' => array( 'member-single-layout', 'equals', $both_sidebars ),
								'data'     => 'sidebars',
							),
						),
					),
					$options_style
				);
			}

			// FAQ
			$faq_options = array(
				'icon'       => 'Simple-Line-Icons-speech',
				'icon_class' => '',
				'title'      => __( 'FAQ', 'porto' ),
				'customizer' => false,
				'fields'     => array(
					array(
						'id'    => 'desc_info_faq',
						'type'  => 'info',
						'desc'  => wp_kses(
							/* translators: %s: Builder url */
							sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Archive</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $archive_url, $type_url ),
							array(
								'strong' => array(),
								'b'      => array(),
								'a'      => array(
									'href'   => array(),
									'target' => array(),
									'class'  => array(),
								),
							)
						),
						'class' => 'porto-important-note',
					),
					array(
						'id'      => 'enable-faq',
						'type'    => 'switch',
						'title'   => __( 'FAQ Content Type', 'porto' ),
						'default' => false,
						'desc'    => __( 'Please select "Enable" to visit Faq Page.', 'porto' ),
						'on'      => __( 'Enable', 'porto' ),
						'off'     => __( 'Disable', 'porto' ),
					),
					array(
						'id'          => 'faq-slug-name',
						'type'        => 'text',
						'title'       => __( 'Slug Name', 'porto' ),
						'desc'        => __( 'If there isn\'t "FAQs Page", Show this slug name as permalink.', 'porto' ),
						'placeholder' => 'faq',
					),
					array(
						'id'          => 'faq-name',
						'type'        => 'text',
						'title'       => __( 'Name', 'porto' ),
						'placeholder' => __( 'FAQs', 'porto' ),
						'desc'        => __( 'Show this name in Faqs page and Admin Page.', 'porto' ),
					),
					array(
						'id'          => 'faq-singular-name',
						'type'        => 'text',
						'title'       => __( 'Singular Name', 'porto' ),
						'desc'        => __( 'Show individual faqs as this name.', 'porto' ),
						'placeholder' => __( 'FAQ', 'porto' ),
					),
					array(
						'id'          => 'faq-cat-slug-name',
						'type'        => 'text',
						'title'       => __( 'Category Slug Name', 'porto' ),
						'desc'        => __( 'Show individual faq categories as this name.', 'porto' ),
						'placeholder' => 'faq_cat',
					),
				),
			);
			if ( $options_style ) {
				$this->sections[] = $faq_options;
			}
			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'       => 'Simple-Line-Icons-question',
					'icon_class' => '',
					'title'      => __( 'FAQ', 'porto' ),
					'fields'     => array(
						array(
							'id'    => 'faq-archive-page',
							'type'  => 'select',
							'data'  => 'page',
							'title' => __( 'FAQs Page', 'porto' ),
						),
						array(
							'id'       => 'faq-title',
							'type'     => 'text',
							'title'    => __( 'Page Title', 'porto' ),
							'subtitle' => __( 'This option isn\'t available to <strong>Archive Builder</strong> Page.', 'porto' ),
							'desc'     => __( 'Only when "FAQ Filter Position" option is the "In Content", this text will be shown.', 'porto' ),
							'default'  => 'Frequently Asked <strong>Questions</strong>',
						),
						array(
							'id'       => 'faq-sub-title',
							'type'     => 'textarea',
							'title'    => __( 'Page Sub Title', 'porto' ),
							'subtitle' => __( 'This option isn\'t available to <strong>Archive Posts Grid</strong> Widget.', 'porto' ),
							'desc'     => __( 'Only when "FAQ Filter Position" option is the "In Content", this text will be shown.', 'porto' ),
							'default'  => '',
						),
						array(
							'id'      => 'faq-archive-layout',
							'type'    => 'image_select',
							'title'   => __( 'FAQ Page Layout', 'porto' ),
							'options' => $page_layouts,
							'default' => 'fullwidth',
						),
						array(
							'id'       => 'faq-archive-sidebar',
							'type'     => 'select',
							'title'    => __( 'Select Sidebar', 'porto' ),
							'required' => array( 'faq-archive-layout', 'equals', $sidebars ),
							'data'     => 'sidebars',
						),
						array(
							'id'       => 'faq-archive-sidebar2',
							'type'     => 'select',
							'title'    => __( 'Select Sidebar 2', 'porto' ),
							'required' => array( 'faq-archive-layout', 'equals', $both_sidebars ),
							'data'     => 'sidebars',
						),
						array(
							'id'     => 'desc_info_sort_faq',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Sort Faq Categories:</b> If you use <span>archive builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'      => 'faq-cat-orderby',
							'type'    => 'button_set',
							'title'   => __( 'Sort Categories Order By', 'porto' ),
							'options' => $porto_categories_orderby,
							'desc'    => __( 'Sort faq categories by this option.', 'porto' ),
							'default' => 'name',
						),
						array(
							'id'      => 'faq-cat-order',
							'type'    => 'button_set',
							'title'   => __( 'Sort Order for Categories', 'porto' ),
							'desc'    => __( 'Sort faq categories ascending or descending by this option.', 'porto' ),
							'options' => $porto_categories_order,
							'default' => 'asc',
						),
						array(
							'id'      => 'faq-cat-sort-pos',
							'type'    => 'button_set',
							'title'   => __( 'FAQ Filter Position', 'porto' ),
							'options' => $porto_categories_sort_pos,
							'default' => 'content',
						),
						array(
							'id'     => 'desc_info_sort_faq_item',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Sort Faq Items:</b> If you use <span>archive builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'      => 'faq-orderby',
							'type'    => 'button_set',
							'title'   => __( 'Sort Items Order By', 'porto' ),
							'options' => array_slice( $porto_categories_orderby, 0, 3 ),
							'desc'    => __( 'Sort faq items by this option.', 'porto' ),
							'default' => 'name',
						),
						array(
							'id'      => 'faq-order',
							'type'    => 'button_set',
							'title'   => __( 'Sort Order for Items', 'porto' ),
							'options' => $porto_categories_order,
							'desc'    => __( 'Sort faq items ascending or descending by this option.', 'porto' ),
							'default' => 'asc',
						),
						array(
							'id'     => 'desc_info_faq_pagination',
							'type'   => 'info',
							'desc'   => wp_kses(
								__( '<b>Faq Pagination:</b> If you use <span>archive builder</span>, below options <span>aren\'t</span> necessary.', 'porto' ),
								array(
									'span' => array(),
									'b'    => array(),
								)
							),
							'notice' => false,
							'class'  => 'porto-redux-section',
						),
						array(
							'id'      => 'faq-infinite',
							'type'    => 'button_set',
							'title'   => __( 'FAQ Pagination Style', 'porto' ),
							'desc'    => __( 'Control the pagination type in faq page.', 'porto' ),
							'options' => array(
								''         => __( 'Default', 'porto' ),
								'ajax'     => __( 'Ajax Pagination', 'porto' ),
								'infinite' => __( 'Infinite Scroll', 'porto' ),
							),
							'default' => 'infinite',
						),
						array(
							'id'       => 'faq-cat-ft',
							'type'     => 'button_set',
							'title'    => __( 'FAQ Filter Type', 'porto' ),
							'options'  => array(
								''     => __( 'Filter using Javascript/CSS', 'porto' ),
								'ajax' => __( 'Ajax Loading', 'porto' ),
							),
							'desc'     => __( 'Control filter type in faqs page or faqs archive page.', 'porto' ),
							'default'  => '',
							'required' => array(
								array( 'faq-infinite', '!=', '' ),
								array( 'faq-cat-sort-pos', '!=', 'hide' ),
							),
						),
					),
				),
				$options_style,
				$faq_options
			);

			/**
			 * Unlimited Post Types
			 *
			 * @since 6.4.0
			 */
			$ptus = $this->get_post_ptu();
			if ( ! empty( $ptus ) ) {

				$this->sections[] = $this->add_customizer_field(
					array(
						'id'         => 'ptu-layouts-settings',
						'icon'       => 'Simple-Line-Icons-grid',
						'icon_class' => '',
						'title'      => __( 'Unlimited Post Types', 'porto' ),
						'fields'     => array(
							array(
								'id'    => 'desc_info_ptu_layout',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Archive</a>, <a href="%2$s" target="_blank">Single</a> & <a href="%3$s" target="_blank">Type</a> Builders help you to develop your site easily.', 'porto' ), $archive_url, $single_url, $type_url ),
									array(
										'strong' => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'desc_info_go_ptu_sidebar',
								'type'    => 'info',
								'default' => '',
								'desc'    => wp_kses(
									sprintf(
										/* translators: %s: widgets url */
										__( 'You can create the sidebar in <a href="%s" target="_blank">here</a>.', 'porto' ),
										esc_url( admin_url( 'themes.php?page=multiple_sidebars' ) )
									),
									array(
										'a' => array(
											'href'   => array(),
											'target' => array(),
										),
									)
								),
							),
						),
					),
					$options_style
				);
				foreach ( $ptus as $name ) {
					$this->sections[] = $this->add_customizer_field(
						array(
							'icon_class' => 'icon',
							'subsection' => true,
							'title'      => sprintf( esc_html__( '%s Layouts', 'porto' ), ucfirst( $name ) ),
							'fields'     => array(
								array(
									'id'      => $name . '-ptu-archive-layout',
									'type'    => 'image_select',
									'title'   => __( 'Archive Layout', 'porto' ),
									'options' => $page_layouts,
									'default' => 'fullwidth',
								),
								array(
									'id'       => $name . '-ptu-archive-sidebar',
									'type'     => 'select',
									'title'    => __( 'Select Archive Sidebar', 'porto' ),
									'required' => array( $name . '-ptu-archive-layout', 'equals', $sidebars ),
									'data'     => 'sidebars',
								),
								array(
									'id'       => $name . '-ptu-archive-sidebar2',
									'type'     => 'select',
									'title'    => __( 'Select Archive Sidebar 2', 'porto' ),
									'required' => array( $name . '-ptu-archive-layout', 'equals', $both_sidebars ),
									'data'     => 'sidebars',
								),
								array(
									'id'      => $name . '-ptu-single-layout',
									'type'    => 'image_select',
									'title'   => __( 'Single Layout', 'porto' ),
									'options' => $page_layouts,
									'default' => 'fullwidth',
								),
								array(
									'id'       => $name . '-ptu-single-sidebar',
									'type'     => 'select',
									'title'    => __( 'Select Single Sidebar', 'porto' ),
									'required' => array( $name . '-ptu-single-layout', 'equals', $sidebars ),
									'data'     => 'sidebars',
								),
								array(
									'id'       => $name . '-ptu-single-sidebar2',
									'type'     => 'select',
									'title'    => __( 'Select Single Sidebar 2', 'porto' ),
									'required' => array( $name . '-ptu-single-layout', 'equals', $both_sidebars ),
									'data'     => 'sidebars',
								),
							),
						),
						$options_style
					);
				}
			}
			if ( class_exists( 'WooCommerce' ) ) {
				// Woocommerce
				$all_roles = array();
				if ( is_admin() ) {
					$roles = wp_roles()->roles;
					$roles = apply_filters( 'editable_roles', $roles );
					foreach ( $roles as $role_name => $role_info ) {
						$initial_assigned_roles = array( $role_name => $role_info['name'] );
						$all_roles              = array_merge( $all_roles, $initial_assigned_roles );
					}
				}
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon'       => 'icon-plugins',
						'icon_class' => 'porto-icon',
						'title'      => __( 'WooCommerce', 'porto' ),
						'transport'  => 'postMessage',
						'fields'     => array(
							array(
								'id'      => 'woo-account-login-style',
								'type'    => 'button_set',
								'title'   => __( 'Login Style', 'porto' ),
								'desc'    => __( 'Please select lightbox if you want to use login popup instead of displaying login link.', 'porto' ),
								'default' => '',
								'options' => array(
									''     => __( 'Lightbox', 'porto' ),
									'link' => __( 'Link', 'porto' ),
								),
							),
							array(
								'id'        => 'woo-show-default-page-header',
								'type'      => 'switch',
								'title'     => __( 'Show Progressive Page header in Cart and Checkout page', 'porto' ),
								'desc'      => __(
									'Select "Yes" to use progressive page header which displays three steps: shopping cart, checkout and order complete.',
									'porto'
								),
								'default'   => true,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'refresh',
							),
							array(
								'id'      => 'woo-show-product-border',
								'type'    => 'switch',
								'title'   => __( 'Show Border on product images', 'porto' ),
								'desc'    => __( 'Select "Yes" to display border( width: 1px, color: #F4F4F4 ) on product image in all products.', 'porto' ),
								'default' => true,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'     => 'desc_info_product_label',
								'type'   => 'info',
								'title'  => __( 'Product Labels', 'porto' ),
								'notice' => false,
							),
							array(
								'id'      => 'product-labels',
								'type'    => 'button_set',
								'title'   => __( 'Select labels to display', 'porto' ),
								'desc'    => __( 'Offers "Featured", "Sale" and "New" lables for Product', 'porto' ),
								'multi'   => true,
								'default' => array( 'hot', 'sale' ),
								'options' => array(
									'hot'  => __( 'Hot', 'porto' ),
									'sale' => __( 'Sale', 'porto' ),
									'new'  => __( 'New', 'porto' ),
								),
							),
							array(
								'id'       => 'product-hot-label',
								'type'     => 'text',
								'required' => array( 'product-labels', 'contains', 'hot' ),
								'title'    => __( '"Hot" Text', 'porto' ),
								'desc'     => __( 'This will be displayed in the featured product.', 'porto' ),
								'default'  => '',
							),
							array(
								'id'       => 'product-sale-label',
								'type'     => 'text',
								'required' => array( 'product-labels', 'contains', 'sale' ),
								'title'    => __( '"Sale" Text', 'porto' ),
								'desc'     => __( 'This will be displayed in the product on sale.', 'porto' ),
								'default'  => '',
							),
							array(
								'id'       => 'product-sale-percent',
								'type'     => 'switch',
								'required' => array( 'product-labels', 'contains', 'sale' ),
								'title'    => __( 'Show Saved Sale Price Percentage', 'porto' ),
								'desc'     => __( 'Select "No" to display "Sale" text instead of sale percentage.', 'porto' ),
								'default'  => true,
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'product-new-label',
								'type'     => 'text',
								'required' => array( 'product-labels', 'contains', 'new' ),
								'title'    => __( 'New Product Label', 'porto' ),
								'desc'     => __( 'This will be displayed in the new product.', 'porto' ),
								'default'  => '',
							),
							array(
								'id'       => 'product-new-days',
								'type'     => 'slider',
								'title'    => __( 'New Product Period (days)', 'porto' ),
								'required' => array( 'product-labels', 'contains', 'new' ),
								'desc'     => __( 'The Products which were created over this option will be displayed', 'porto' ),
								'default'  => 7,
								'min'      => 1,
								'max'      => 100,
							),
							array(
								'id'     => 'desc_info_sale_popup',
								'type'   => 'info',
								'title'  => __( 'Sales Popup : Show products popup in all page.', 'porto' ),
								'notice' => false,
							),
							array(
								'id'        => 'woo-sales-popup',
								'type'      => 'select',
								'title'     => __( 'Sales Popup Content', 'porto' ),
								'desc'      => __( 'Select which products you want to show in sales popup.', 'porto' ),
								'default'   => '',
								'options'   => array(
									''         => __( 'Do not show', 'porto' ),
									'real'     => __( 'Recent sale products', 'porto' ),
									'popular'  => __( 'Popular products', 'porto' ),
									'rating'   => __( 'Top rated products', 'porto' ),
									'sale'     => __( 'Sale products', 'porto' ),
									'featured' => __( 'Featured products', 'porto' ),
									'recent'   => __( 'Recent products', 'porto' ),
								),
								'transport' => 'refresh',
							),
							array(
								'id'        => 'woo-sales-popup-title',
								'type'      => 'text',
								'title'     => __( 'Popup Title', 'porto' ),
								'default'   => __( 'Someone just purchased', 'porto' ),
								'desc'      => __( 'This will show at top of popup dialog.', 'porto' ),
								'required'  => array( 'woo-sales-popup', '!=', '' ),
								'transport' => 'refresh',
							),
							array(
								'id'        => 'woo-sales-popup-count',
								'type'      => 'slider',
								'title'     => __( 'Products Count', 'porto' ),
								'required'  => array( 'woo-sales-popup', '!=', '' ),
								'default'   => 10,
								'min'       => 1,
								'max'       => 30,
								'transport' => 'refresh',
							),
							array(
								'id'        => 'woo-sales-popup-start-delay',
								'type'      => 'slider',
								'title'     => __( 'Start Delay(seconds)', 'porto' ),
								'desc'      => __( 'Change delay time to show the first popup after page loading.', 'porto' ),
								'required'  => array( 'woo-sales-popup', '!=', '' ),
								'default'   => 10,
								'min'       => 1,
								'max'       => 30,
								'transport' => 'refresh',
							),
							array(
								'id'        => 'woo-sales-popup-interval',
								'type'      => 'slider',
								'title'     => __( 'Interval(seconds)', 'porto' ),
								'desc'      => __( 'Change duration between popups. Each sales popup will be disappeared after 4 seconds.', 'porto' ),
								'required'  => array( 'woo-sales-popup', '!=', '' ),
								'default'   => 60,
								'min'       => 1,
								'max'       => 600,
								'transport' => 'refresh',
							),
							array(
								'id'        => 'woo-sales-popup-mobile',
								'type'      => 'switch',
								'title'     => __( 'Enable on Mobile', 'porto' ),
								'desc'      => __( 'Do you want to enable sales popup on mobile?', 'porto' ),
								'required'  => array( 'woo-sales-popup', '!=', '' ),
								'default'   => true,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'refresh',
							),
							array(
								'id'     => 'desc_info_pre_order',
								'type'   => 'info',
								'title'  => __( 'Pre-Order', 'porto' ),
								'notice' => false,
							),
							array(
								'id'        => 'woo-pre-order',
								'type'      => 'switch',
								'title'     => __( 'Enable Pre-Order', 'porto' ),
								'subtitle'  => __( 'Pre-Order functionality offers customers the chance to purchase the unavailable products and provide them only after they are officially on sale.', 'porto' ),
								'desc'      => __( 'Before selecting "ON", You should check "pre-order" meta option of WooCoommerce Product.', 'porto' ),
								'transport' => 'refresh',
							),
							array(
								'id'        => 'woo-pre-order-label',
								'type'      => 'text',
								'title'     => __( 'Pre-Order Label', 'porto' ),
								'desc'      => __( 'This text will be used on \'Add to Cart\' button.', 'porto' ),
								'required'  => array( 'woo-pre-order', 'equals', true ),
								'transport' => 'refresh',
							),
							array(
								'id'        => 'woo-pre-order-msg-date',
								'type'      => 'text',
								'title'     => __( 'Pre-Order Availability Date Text', 'porto' ),
								/* translators: available date */
								'desc'      => __( 'ex: Available date: %1$s (%1$s will be replaced with available date.)', 'porto' ),
								'required'  => array( 'woo-pre-order', 'equals', true ),
								'transport' => 'refresh',
							),
							array(
								'id'          => 'woo-pre-order-msg-nodate',
								'type'        => 'text',
								'title'       => __( 'Pre-Order No Date Message', 'porto' ),
								'desc'        => __( 'This text will be used for the product without Available Date.', 'porto' ),
								'placeholder' => __( 'Available soon', 'porto' ),
								'required'    => array( 'woo-pre-order', 'equals', true ),
								'transport'   => 'refresh',
							),
						),
					),
					$options_style
				);

				if ( $this->legacy_mode ) {
					$this->sections[] = array(
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'Product Archives', 'porto' ),
						'fields'     => array(
							array(
								'id'    => 'desc_info_shop',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Product Archive</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop shop page easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $shop_url, $type_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'desc_info_go_shop_sidebar',
								'type'    => 'info',
								'default' => '',
								'desc'    => wp_kses(
									sprintf(
										/* translators: %s: widgets url */
										__( 'You can control the Woo Category sidebar and <a  href="%1$s" target="_blank">secondary</a> sidebar in <a href="%2$s" target="_blank">here</a>.', 'porto' ),
										esc_url( admin_url( 'themes.php?page=multiple_sidebars' ) ),
										esc_url( admin_url( 'widgets.php' ) )
									),
									array(
										'a' => array(
											'href'   => array(),
											'target' => array(),
										),
									)
								),
							),
							array(
								'id'       => 'product-archive-layout',
								'type'     => 'image_select',
								'title'    => __( 'Page Layout', 'porto' ),
								'subtitle' => __( 'Shop Page Layout.', 'porto' ),
								'options'  => $page_layouts,
								'default'  => 'left-sidebar',
							),
							array(
								'id'       => 'product-archive-sidebar2',
								'type'     => 'select',
								'title'    => __( 'Select Sidebar 2', 'porto' ),
								'required' => array( 'product-archive-layout', 'equals', $both_sidebars ),
								'data'     => 'sidebars',
							),
							array(
								'id'       => 'product-archive-filter-layout',
								'type'     => 'button_set',
								'title'    => __( 'Filter Layout', 'porto' ),
								'subtitle' => __( 'Products filtering layout in shop pages.', 'porto' ),
								'desc'     => __( 'Horizontal 1 and Off Canvas filters requires the page layout which has sidebar.', 'porto' ),
								'default'  => '',
								'options'  => array(
									''            => __( 'Filters in Left & Right Sidebar', 'porto' ),
									'horizontal'  => __( 'Horizontal filters 1', 'porto' ),
									'horizontal2' => __( 'Horizontal filters 2', 'porto' ),
									'offcanvas'   => __( 'Off Canvas', 'porto' ),
								),
							),
							array(
								'id'       => 'product-infinite',
								'type'     => 'button_set',
								'title'    => __( 'Pagination style', 'porto' ),
								'default'  => '',
								'subtitle' => __( 'This option isn\'t fit to "Product Archive" Builder page.', 'porto' ),
								'options'  => array(
									''                => __( 'Default', 'porto' ),
									'load_more'       => __( 'Load More', 'porto' ),
									'infinite_scroll' => __( 'Infinite Scroll', 'porto' ),
								),
							),
							array(
								'id'      => 'category-ajax',
								'type'    => 'switch',
								'title'   => __( 'Enable Ajax Filter', 'porto' ),
								'desc'    => __( 'Select "Yes" to filter all products including default pagination by Ajax in shop pages.', 'porto' ),
								'default' => false,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'        => 'category-item',
								'type'      => 'text',
								'title'     => __( 'Products per Page', 'porto' ),
								'subtitle'  => __( 'This option is shown when the pagination type is default in non-builder page.', 'porto' ),
								'desc'      => __( 'Comma separated list of product counts.', 'porto' ),
								'default'   => '12,24,36',
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'category-view-mode',
								'type'      => 'button_set',
								'title'     => __( 'View Mode', 'porto' ),
								'desc'      => __( 'Products display mode in shop pages', 'porto' ),
								'subtitle'  => __( 'This option isn\'t available to "Product Archive" Builder.', 'porto' ),
								'options'   => porto_ct_category_view_mode(),
								'default'   => '',
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'shop-product-cols',
								'type'      => 'slider',
								'title'     => __( 'Shop Page Product Columns', 'porto' ),
								'desc'      => __( 'Controls the number of columns to display in non-builder shop page.', 'porto' ),
								'default'   => 3,
								'min'       => 2,
								'max'       => 8,
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'shop-product-cols-mobile',
								'type'      => 'slider',
								'title'     => __( 'Shop Page Product Columns on Mobile ( < 576px )', 'porto' ),
								'desc'      => __( 'Controls the number of columns to display for mobile in non-builder shop page.', 'porto' ),
								'default'   => 2,
								'min'       => 1,
								'max'       => 3,
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-cols',
								'type'      => 'slider',
								'title'     => __( 'Category Product Columns', 'porto' ),
								'subtitle'  => __( 'Controls the number of columns to display in non-builder category page.', 'porto' ),
								'default'   => 3,
								'min'       => 2,
								'max'       => 8,
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-cols-mobile',
								'type'      => 'slider',
								'title'     => __( 'Category Product Columns on Mobile ( < 576px )', 'porto' ),
								'subtitle'  => __( 'Controls the number of columns to display for mobile in non-builder category page.', 'porto' ),
								'default'   => 2,
								'min'       => 1,
								'max'       => 3,
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'cat-view-type',
								'type'      => 'select',
								'title'     => __( 'Category Content Position', 'porto' ),
								'subtitle'  => __( 'The position of content section which contains title, description and product count in a product category', 'porto' ),
								'default'   => '',
								'options'   => array(
									''  => __( 'Inner Bottom Left', 'porto' ),
									'2' => __( 'Outside Center', 'porto' ),
								),
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'category-image-hover',
								'type'      => 'switch',
								'title'     => __( 'Enable Image Hover Effect', 'porto' ),
								'subtitle'  => __( 'This option is available to <strong>Type Builder Widget</strong> <strong>Porto Posts Grid</strong>.', 'porto' ),
								'desc'      => __( 'If enabled, the first image of product gallery will be displayed on product hover.', 'porto' ),
								'default'   => true,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-stock',
								'type'      => 'switch',
								'title'     => __( 'Show "Out of stock" Status', 'porto' ),
								'desc'      => __( 'Select "Yes" to display "Out of stock" text for the out-of-stock products.', 'porto' ),
								'default'   => true,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'postMessage',
							),
							array(
								'id'     => 'desc_info_product_layout',
								'type'   => 'info',
								'title'  => __( 'Product Layout Options', 'porto' ),
								'notice' => false,
							),
							array(
								'id'        => 'category-addlinks-convert',
								'type'      => 'switch',
								'title'     => __( 'Change "A" Tag to "SPAN" Tag', 'porto' ),
								'desc'      => __( 'Select "Yes" to use span tag for the add to cart, quickview and add to wishlist buttons in shop pages.', 'porto' ),
								'default'   => false,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'category-addlinks-pos',
								'type'      => 'image_select',
								'title'     => __( 'Product Layout', 'porto' ),
								'desc'      => __( 'Select position of add to cart, add to wishlist, quickview.', 'porto' ),
								'options'   => array(
									'default'              => array(
										'title' => __( 'Default', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_default.jpg',
									),
									'onhover'              => array(
										'title' => __( 'Default - Show Links on Hover', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_default.jpg',
									),
									'outimage_aq_onimage'  => array(
										'title' => __( 'Add to Cart, Quick View On Image', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_outimage_aq_onimage.jpg',
									),
									'outimage_aq_onimage2' => array(
										'title' => __( 'Add to Cart, Quick View On Image with Padding', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_outimage_aq_onimage2.jpg',
									),
									'awq_onimage'          => array(
										'title' => __( 'Link On Image', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_awq_onimage.jpg',
									),
									'outimage'             => array(
										'title' => __( 'Out of Image', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_outimage.jpg',
									),
									'onimage'              => array(
										'title' => __( 'On Image', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_onimage.jpg',
									),
									'onimage2'             => array(
										'title' => __( 'On Image with Overlay 1', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_onimage2.jpg',
									),
									'onimage3'             => array(
										'title' => __( 'On Image with Overlay 2', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_onimage3.jpg',
									),
									'quantity'             => array(
										'title' => __( 'Show Quantity Input', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/product_layouts/product_layout_quantity_input.jpg',
									),
								),
								'default'   => 'default',
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'add-to-cart-notification',
								'type'      => 'image_select',
								'title'     => __( 'Add to Cart Notification Type', 'porto' ),
								'desc'      => __( 'Select the notification type whenever product is added to cart.', 'porto' ),
								'options'   => array(
									''  => array(
										'title' => __( 'Style 1', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/addcart-1.jpg',
									),
									'2' => array(
										'title' => __( 'Style 2', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/addcart-2.jpg',
									),
									'3' => array(
										'title' => __( 'Style 3', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/addcart-3.jpg',
									),
								),
								'default'   => '3',
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'show_swatch',
								'type'      => 'switch',
								'title'     => __( 'Show Color / Image swatch', 'porto' ),
								'subtitle'  => __( 'This is available for only variable product in shop page.', 'porto' ),
								'default'   => false,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-categories',
								'type'      => 'switch',
								'title'     => __( 'Show Categories', 'porto' ),
								'desc'      => __( 'Select "YES" to show product categories to each product type.', 'porto' ),
								'sbutitle'  => __( 'This option isn\'t available for theme product type.', 'porto' ),
								'default'   => true,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-review',
								'type'      => 'switch',
								'title'     => __( 'Show Reviews', 'porto' ),
								'desc'      => __( 'Select "YES" to show review price to each product type.', 'porto' ),
								'sbutitle'  => __( 'This option isn\'t available for theme product type.', 'porto' ),
								'default'   => true,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-price',
								'type'      => 'switch',
								'title'     => __( 'Show Price', 'porto' ),
								'desc'      => __( 'Select "YES" to show product price to each product type.', 'porto' ),
								'sbutitle'  => __( 'This option isn\'t available for theme product type.', 'porto' ),
								'default'   => true,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-desc',
								'type'      => 'switch',
								'title'     => __( 'Show Description', 'porto' ),
								'desc'      => __( 'Select "YES" to show product price to each product type.', 'porto' ),
								'sbutitle'  => __( 'This works for only Grid view. This option isn\'t available for theme product type.', 'porto' ),
								'default'   => false,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-wishlist',
								'type'      => 'switch',
								'title'     => __( 'Show Wishlist', 'porto' ),
								'desc'      => __( 'Select "YES" to show product wishlist to each product type.', 'porto' ),
								'sbutitle'  => __( 'This option isn\'t available for theme product type.', 'porto' ),
								'default'   => true,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-quickview',
								'type'      => 'switch',
								'title'     => __( 'Show Quick View', 'porto' ),
								'desc'      => __( 'Select "YES" to show product quickview to each product type.', 'porto' ),
								'sbutitle'  => __( 'This option isn\'t available for theme product type.', 'porto' ),
								'default'   => true,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-quickview-label',
								'type'      => 'text',
								'required'  => array( 'product-quickview', 'equals', true ),
								'title'     => __( '"Quick View" Text', 'porto' ),
								'desc'      => __( 'Shows this text instead of "Quick View".', 'porto' ),
								'default'   => '',
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-compare',
								'type'      => 'switch',
								'title'     => __( 'Show Compare', 'porto' ),
								'desc'      => __( 'Select "YES" to show product compare to each product type.', 'porto' ),
								'subtitle'  => __( 'This option isn\'t available for theme product type.', 'porto' ),
								'default'   => true,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-compare-title',
								'type'      => 'text',
								'title'     => __( 'Compare Popup Title', 'porto' ),
								'desc'      => __( 'Shows this text at the compare popup.', 'porto' ),
								'default'   => __( 'You just added to compare list.', 'porto' ),
								'required'  => array( 'product-compare', '!=', false ),
								'transport' => 'refresh',
							),
						),
					);
					$this->sections[] = array(
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'Single Product', 'porto' ),
						'transport'  => 'postMessage',
						'fields'     => array(
							array(
								'id'    => 'desc_info_single_product',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Single Product</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily. Some below options might be overrided because the priority of the builder widget option is <b>higher</b>.', 'porto' ), $product_url, $type_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'desc_info_go_product_sidebar',
								'type'    => 'info',
								'default' => '',
								'desc'    => wp_kses(
									sprintf(
										/* translators: %s: widgets url */
										__( 'You can control the Woo Product sidebar and <a  href="%1$s" target="_blank">secondary</a> sidebar in <a href="%2$s" target="_blank">here</a>.', 'porto' ),
										esc_url( admin_url( 'themes.php?page=multiple_sidebars' ) ),
										esc_url( admin_url( 'widgets.php' ) )
									),
									array(
										'a' => array(
											'href'   => array(),
											'target' => array(),
										),
									)
								),
							),
							array(
								'id'        => 'product-single-layout',
								'type'      => 'image_select',
								'title'     => __( 'Page Layout', 'porto' ),
								'subtitle'  => __( 'Product Page Layout', 'porto' ),
								'options'   => $page_layouts,
								'default'   => 'right-sidebar',
								'transport' => 'refresh',
							),
							array(
								'id'       => 'product-single-sidebar2',
								'type'     => 'select',
								'title'    => __( 'Select Sidebar 2', 'porto' ),
								'required' => array( 'product-single-layout', 'equals', $both_sidebars ),
								'data'     => 'sidebars',
							),
							array(
								'id'        => 'product-single-content-layout',
								'type'      => 'image_select',
								'title'     => __( 'Product Layout', 'porto' ),
								'subtitle'  => __( 'Individual product has the meta option for <b>product layout</b>', 'porto' ),
								'options'   => array(
									'default'          => array(
										'title' => __( 'Default', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/default.jpg',
									),
									'extended'         => array(
										'title' => __( 'Extended', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/extended.jpg',
									),
									'full_width'       => array(
										'title' => __( 'Full Width', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/full_width.jpg',
									),
									'grid'             => array(
										'title' => __( 'Grid Images', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/grid.jpg',
									),
									'sticky_info'      => array(
										'title' => __( 'Sticky Info', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/sticky_info.jpg',
									),
									'sticky_both_info' => array(
										'title' => __( 'Sticky Left & Right Info', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/sticky_info_both.jpg',
									),
									'transparent'      => array(
										'title' => __( 'Transparent Images', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/transparent.jpg',
									),
									'centered_vertical_zoom' => array(
										'title' => __( 'Centered Vertical Zoom', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/centered_vertical_zoom.jpg',
									),
									'left_sidebar'     => array(
										'title' => __( 'Left Sidebar', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/left_sidebar.jpg',
									),
									'builder'          => array(
										'title' => __( 'Custom', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/custom.jpg',
									),
								),
								'default'   => 'default',
								'transport' => 'refresh',
							),
							array(
								'id'       => 'product-single-content-builder',
								'type'     => 'select',
								'title'    => __( 'Custom Product Layout', 'porto' ),
								'subtitle' => __( 'We recommend to use <strong>Display Condition</strong> when creating single product builder instead of this option. This option is overrided by <strong>Display Condition</strong>.', 'porto' ),
								'desc'     => __( 'Please select a product layout. You can create a product layout in <strong>Porto / Templates Builder / Single Product / Add New</strong>.', 'porto' ),
								'options'  => $product_layouts,
								'default'  => '',
								'required' => array( 'product-single-content-layout', 'equals', 'builder' ),
							),
							/*array(
							'id'=>'product-ajax-addcart-button',
							'type' => 'switch',
							'title' => __( 'Enable AJAX add to cart button', 'porto' ),
							'default' => true,
							'on' => __('Yes', 'porto'),
							'off' => __('No', 'porto'),
							),*/
							array(
								'id'        => 'product-sticky-addcart',
								'type'      => 'button_set',
								'title'     => __( 'Sticky add to cart section', 'porto' ),
								'desc'      => __( 'Select the position to display sticky add to cart section in single product page.', 'porto' ),
								'subtitle'  => __( 'This option can be overrided by <strong>Product Sticky Add to Cart</strong>.', 'porto' ),
								'options'   => array(
									''       => __( 'None', 'porto' ),
									'top'    => __( 'At the Top', 'porto' ),
									'bottom' => __( 'At the Bottom', 'porto' ),
								),
								'default'   => '',
								'transport' => 'refresh',
							),
							array(
								'id'      => 'product-nav',
								'type'    => 'switch',
								'title'   => __( 'Show Prev/Next Product', 'porto' ),
								'desc'    => __( 'Select "YES" to show Prev/Next navigation.', 'porto' ),
								'default' => true,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'      => 'product-short-desc',
								'type'    => 'switch',
								'title'   => __( 'Show Short Description', 'porto' ),
								'desc'    => __( 'Select "YES" to show Short Description.', 'porto' ),
								'desc'    => __( 'This is available for Default Product Layouts.', 'porto' ),
								'default' => true,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'product-custom-tabs-count',
								'type'     => 'text',
								'title'    => __( 'Custom Tabs Count', 'porto' ),
								'subtitle' => __( 'This option determine the number of custom tab. If you input value, you will see that value of tab meta options.', 'porto' ),
								'default'  => '2',
							),
							array(
								'id'      => 'product-tabs-pos',
								'type'    => 'button_set',
								'title'   => __( 'Tabs Position', 'porto' ),
								'desc'    => __( 'Select the position of tab where to put.', 'porto' ),
								'options' => array(
									''      => __( 'Default', 'porto' ),
									'below' => __( 'Below Price & Short Description', 'porto' ),
								),
								'default' => '',
							),
							array(
								'id'      => 'product-metas',
								'type'    => 'button_set',
								'title'   => __( 'Product Meta', 'porto' ),
								'desc'    => __( 'Select product metas to show.', 'porto' ),
								'multi'   => true,
								'options' => array(
									'sku'  => __( 'SKU', 'porto' ),
									'cats' => __( 'Categories', 'porto' ),
									'tags' => __( 'Tags', 'porto' ),
									'-'    => 'None',
								),
								'default' => array( 'sku', 'cats', 'tags', '-' ),
							),
							array(
								'title'    => __( 'Variation(Attribute) Selection Mode', 'porto' ),
								'subtitle' => __( 'This is used in variable product page.', 'porto' ),
								'id'       => 'product_variation_display_mode',
								'type'     => 'button_set',
								'default'  => 'select',
								'options'  => array(
									'button' => __( 'Label, Image / Color swatch', 'porto' ),
									'select' => __( 'Select Box', 'porto' ),
								),
							),
							array(
								'id'      => 'product-attr-desc',
								'type'    => 'switch',
								'title'   => __( 'Show Description of Selected Attribute', 'porto' ),
								'desc'    => __( 'Select "Yes" to display description if it exists when selecting product attribute in the variations.', 'porto' ),
								'default' => false,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'      => 'product-tab-title',
								'type'    => 'text',
								'title'   => __( 'Global Product Custom Tab Title', 'porto' ),
								'desc'    => __( 'Input the title of Product Custom Tab.', 'porto' ),
								'default' => '',
							),
							array(
								'id'       => 'product-tab-block',
								'type'     => 'text',
								'title'    => __( 'Global Product Custom Tab Block', 'porto' ),
								'subtitle' => __( 'This block will be shown in the Custom Tab Content.', 'porto' ),
								'desc'     => __( 'Input block slug name', 'porto' ),
								'default'  => '',
							),
							array(
								'id'      => 'product-tab-priority',
								'type'    => 'text',
								'title'   => __( 'Global Product Custom Tab Priority', 'porto' ),
								'desc'    => __( 'Input the custom tab priority. (Description: 10, Additional Information: 20, Reviews: 30)', 'porto' ),
								'default' => '60',
							),
							array(
								'id'      => 'product-share',
								'type'    => 'switch',
								'title'   => __( 'Show Social Share Links', 'porto' ),
								'desc'    => __( 'Select "YES" to show Social Links.', 'porto' ),
								'default' => true,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'        => 'product-content_bottom',
								'type'      => 'text',
								'title'     => __( 'Content Bottom Block', 'porto' ),
								'desc'      => __( 'Please input comma separated block slug names. You can create a block in <strong>Porto / Templates Builder / Block / Add New</strong>.', 'porto' ),
								'transport' => 'refresh',
							),
							array(
								'id'     => 'desc_info_single_product_related',
								'type'   => 'info',
								'desc'   => wp_kses(
									__( '<b>Related Products in Single Product:</b>', 'porto' ),
									array(
										'span' => array(),
										'b'    => array(),
									)
								),
								'notice' => false,
								'class'  => 'porto-redux-section',
							),
							array(
								'id'      => 'product-related',
								'type'    => 'switch',
								'title'   => __( 'Show Related Products', 'porto' ),
								'desc'    => __( 'Select "YES" to show related products in the single product page.', 'porto' ),
								'default' => true,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'product-related-count',
								'type'     => 'text',
								'required' => array( 'product-related', 'equals', true ),
								'title'    => __( 'Related Products Count', 'porto' ),
								'default'  => '10',
							),
							array(
								'id'       => 'product-related-cols',
								'type'     => 'button_set',
								'required' => array( 'product-related', 'equals', true ),
								'title'    => __( 'Related Product Columns', 'porto' ),
								'options'  => porto_ct_related_product_columns(),
								'default'  => '4',
							),
							array(
								'id'     => 'desc_info_single_product_upsell',
								'type'   => 'info',
								'desc'   => wp_kses(
									__( '<b>Upsell Products in Single Product:</b>', 'porto' ),
									array(
										'span' => array(),
										'b'    => array(),
									)
								),
								'notice' => false,
								'class'  => 'porto-redux-section',
							),
							array(
								'id'      => 'product-upsells',
								'type'    => 'switch',
								'title'   => __( 'Show Up Sells', 'porto' ),
								'desc'    => __( 'Select "YES" to show Upsell products in the cart page.', 'porto' ),
								'default' => true,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'product-upsells-count',
								'type'     => 'text',
								'required' => array( 'product-upsells', 'equals', true ),
								'title'    => __( 'Up Sells Count', 'porto' ),
								'default'  => '10',
							),
							array(
								'id'       => 'product-upsells-cols',
								'type'     => 'button_set',
								'required' => array( 'product-upsells', 'equals', true ),
								'title'    => __( 'Up Sells Product Columns', 'porto' ),
								'options'  => porto_ct_related_product_columns(),
								'default'  => '4',
							),
						),
					);
				} else {
					$this->sections[] = array(
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'Product Archives', 'porto' ),
						'fields'     => array(
							array(
								'id'    => 'desc_info_shop',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Product Archive</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop shop page easily.', 'porto' ), $shop_url, $type_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'desc_info_go_shop_sidebar',
								'type'    => 'info',
								'default' => '',
								'desc'    => wp_kses(
									sprintf(
										/* translators: %s: widgets url */
										__( 'You can control the Woo Category sidebar and <a  href="%1$s" target="_blank">secondary</a> sidebar in <a href="%2$s" target="_blank">here</a>.', 'porto' ),
										esc_url( admin_url( 'themes.php?page=multiple_sidebars' ) ),
										esc_url( admin_url( 'widgets.php' ) )
									),
									array(
										'a' => array(
											'href'   => array(),
											'target' => array(),
										),
									)
								),
							),
							array(
								'id'       => 'product-archive-layout',
								'type'     => 'image_select',
								'title'    => __( 'Page Layout', 'porto' ),
								'subtitle' => __( 'Shop Page Layout', 'porto' ),
								'options'  => $page_layouts,
								'default'  => 'left-sidebar',
							),
							array(
								'id'       => 'product-archive-sidebar2',
								'type'     => 'select',
								'title'    => __( 'Select Sidebar 2', 'porto' ),
								'required' => array( 'product-archive-layout', 'equals', $both_sidebars ),
								'data'     => 'sidebars',
							),
							array(
								'id'       => 'product-archive-filter-layout',
								'type'     => 'button_set',
								'title'    => __( 'Filter Layout', 'porto' ),
								'subtitle' => __( 'Products filtering layout in shop pages.', 'porto' ),
								'desc'     => __( 'Horizontal 1 and Off Canvas filters requires the page layout which has sidebar.', 'porto' ),
								'default'  => '',
								'options'  => array(
									''            => __( 'Filters in Left & Right Sidebar', 'porto' ),
									'horizontal'  => __( 'Horizontal filters 1', 'porto' ),
									'horizontal2' => __( 'Horizontal filters 2', 'porto' ),
									'offcanvas'   => __( 'Off Canvas', 'porto' ),
								),
							),
							array(
								'id'      => 'category-ajax',
								'type'    => 'switch',
								'title'   => __( 'Enable Ajax Filter', 'porto' ),
								'desc'    => __( 'Select "Yes" to filter all products including default pagination by Ajax in shop pages.', 'porto' ),
								'default' => false,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'        => 'category-item',
								'type'      => 'text',
								'title'     => __( 'Products per Page', 'porto' ),
								'subtitle'  => __( 'This option is shown when the pagination type is default in non-builder page.', 'porto' ),
								'desc'      => __( 'Comma separated list of product counts.', 'porto' ),
								'default'   => '12,24,36',
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-stock',
								'type'      => 'switch',
								'title'     => __( 'Show "Out of stock" Status', 'porto' ),
								'desc'      => __( 'Select "Yes" to display "Out of stock" text for the out-of-stock products.', 'porto' ),
								'default'   => true,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'postMessage',
							),
							array(
								'id'     => 'desc_info_product_layout',
								'type'   => 'info',
								'title'  => __( 'Product Layout Options', 'porto' ),
								'notice' => false,
							),
							array(
								'id'        => 'category-addlinks-convert',
								'type'      => 'switch',
								'title'     => __( 'Change "A" Tag to "SPAN" Tag', 'porto' ),
								'desc'      => __( 'Select "Yes" to use span tag for the add to cart, quickview and add to wishlist buttons in shop pages.', 'porto' ),
								'default'   => false,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'add-to-cart-notification',
								'type'      => 'image_select',
								'title'     => __( 'Add to Cart Notification Type', 'porto' ),
								'desc'      => __( 'Select the notification type whenever product is added to cart.', 'porto' ),
								'options'   => array(
									''  => array(
										'title' => __( 'Style 1', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/addcart-1.jpg',
									),
									'2' => array(
										'title' => __( 'Style 2', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/addcart-2.jpg',
									),
									'3' => array(
										'title' => __( 'Style 3', 'porto' ),
										'img'   => PORTO_OPTIONS_URI . '/products/addcart-3.jpg',
									),
								),
								'default'   => '3',
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'show_swatch',
								'type'      => 'switch',
								'title'     => __( 'Show Color / Image swatch', 'porto' ),
								'subtitle'  => __( 'This is available for only variable product in shop page.', 'porto' ),
								'default'   => false,
								'on'        => __( 'Yes', 'porto' ),
								'off'       => __( 'No', 'porto' ),
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-quickview-label',
								'type'      => 'text',
								'title'     => __( '"Quick View" Text', 'porto' ),
								'desc'      => __( 'Shows this text instead of "Quick View".', 'porto' ),
								'default'   => '',
								'transport' => 'postMessage',
							),
							array(
								'id'        => 'product-compare-title',
								'type'      => 'text',
								'title'     => __( 'Compare Popup Title', 'porto' ),
								'desc'      => __( 'Shows this text at the compare popup.', 'porto' ),
								'default'   => __( 'You just added to compare list.', 'porto' ),
								'transport' => 'refresh',
							),
						),
					);
					$this->sections[] = array(
						'icon_class' => 'icon',
						'subsection' => true,
						'title'      => __( 'Single Product', 'porto' ),
						'transport'  => 'postMessage',
						'fields'     => array(
							array(
								'id'    => 'desc_info_single_product',
								'type'  => 'info',
								'desc'  => wp_kses(
									/* translators: %s: Builder url */
									sprintf( __( '<strong>Important Note:</strong> <a href="%1$s" target="_blank">Single Product</a> & <a href="%2$s" target="_blank">Type</a> Builders help you to develop your site easily.', 'porto' ), $product_url, $type_url ),
									array(
										'strong' => array(),
										'b'      => array(),
										'a'      => array(
											'href'   => array(),
											'target' => array(),
											'class'  => array(),
										),
									)
								),
								'class' => 'porto-important-note',
							),
							array(
								'id'      => 'desc_info_go_product_sidebar',
								'type'    => 'info',
								'default' => '',
								'desc'    => wp_kses(
									sprintf(
										/* translators: %s: widgets url */
										__( 'You can control the Woo Product sidebar and <a  href="%1$s" target="_blank">secondary</a> sidebar in <a href="%2$s" target="_blank">here</a>.', 'porto' ),
										esc_url( admin_url( 'themes.php?page=multiple_sidebars' ) ),
										esc_url( admin_url( 'widgets.php' ) )
									),
									array(
										'a' => array(
											'href'   => array(),
											'target' => array(),
										),
									)
								),
							),
							array(
								'id'        => 'product-single-layout',
								'type'      => 'image_select',
								'title'     => __( 'Page Layout', 'porto' ),
								'subtitle'  => __( 'Product Page Layout', 'porto' ),
								'options'   => $page_layouts,
								'default'   => 'right-sidebar',
								'transport' => 'refresh',
							),
							array(
								'id'       => 'product-single-sidebar2',
								'type'     => 'select',
								'title'    => __( 'Select Sidebar 2', 'porto' ),
								'required' => array( 'product-single-layout', 'equals', $both_sidebars ),
								'data'     => 'sidebars',
							),
							array(
								'id'       => 'product-custom-tabs-count',
								'type'     => 'text',
								'title'    => __( 'Custom Tabs Count', 'porto' ),
								'subtitle' => __( 'This option determine the number of custom tab. If you input value, you will see that value of tab meta options.', 'porto' ),
								'default'  => '2',
							),
							array(
								'id'      => 'product-metas',
								'type'    => 'button_set',
								'title'   => __( 'Product Meta', 'porto' ),
								'desc'    => __( 'Select product metas to show.', 'porto' ),
								'multi'   => true,
								'options' => array(
									'sku'  => __( 'SKU', 'porto' ),
									'cats' => __( 'Categories', 'porto' ),
									'tags' => __( 'Tags', 'porto' ),
									'-'    => 'None',
								),
								'default' => array( 'sku', 'cats', 'tags', '-' ),
							),
							array(
								'title'    => __( 'Variation(Attribute) Selection Mode', 'porto' ),
								'subtitle' => __( 'This is used in variable product page.', 'porto' ),
								'id'       => 'product_variation_display_mode',
								'type'     => 'button_set',
								'default'  => 'select',
								'options'  => array(
									'button' => __( 'Label, Image / Color swatch', 'porto' ),
									'select' => __( 'Select Box', 'porto' ),
								),
							),
							array(
								'id'      => 'product-attr-desc',
								'type'    => 'switch',
								'title'   => __( 'Show Description of Selected Attribute', 'porto' ),
								'desc'    => __( 'Select "Yes" to display description if it exists when selecting product attribute in the variations.', 'porto' ),
								'default' => false,
								'on'      => __( 'Yes', 'porto' ),
								'off'     => __( 'No', 'porto' ),
							),
							array(
								'id'      => 'product-tab-title',
								'type'    => 'text',
								'title'   => __( 'Global Product Custom Tab Title', 'porto' ),
								'desc'    => __( 'Input the title of Product Custom Tab.', 'porto' ),
								'default' => '',
							),
							array(
								'id'       => 'product-tab-block',
								'type'     => 'text',
								'title'    => __( 'Global Product Custom Tab Block', 'porto' ),
								'subtitle' => __( 'This block will be shown in the Custom Tab Content.', 'porto' ),
								'desc'     => __( 'Input block slug name', 'porto' ),
								'default'  => '',
							),
							array(
								'id'      => 'product-tab-priority',
								'type'    => 'text',
								'title'   => __( 'Global Product Custom Tab Priority', 'porto' ),
								'desc'    => __( 'Input the custom tab priority. (Description: 10, Additional Information: 20, Reviews: 30)', 'porto' ),
								'default' => '60',
							),
						),
					);
				}

				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Product Image & Zoom', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'      => 'product-thumbs',
							'type'    => 'switch',
							'title'   => __( 'Show Thumbnails', 'porto' ),
							'desc'    => __( 'Select "Yes" to display product thumbnails gallery below the main products slider in single product page.', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'product-thumbs-count',
							'type'     => 'text',
							'required' => array( 'product-thumbs', 'equals', true ),
							'title'    => __( 'Thumbnails Count', 'porto' ),
							'subtitle' => __( 'This option is available for default layout of single product image.', 'porto' ),
							'default'  => '4',
						),
						/*array(
						'id'=>'product-image-border',
						'type' => 'switch',
						'title' => __('Show Product Image Border', 'porto'),
						'desc' => __( 'If you select yes, this will display border on product image.', 'porto' ),
						'default' => true,
						'on' => __('Yes', 'porto'),
						'off' => __('No', 'porto'),
						),*/
						array(
							'id'      => 'product-zoom',
							'type'    => 'switch',
							'title'   => __( 'Enable Image Zoom', 'porto' ),
							'desc'    => __( 'Select "Yes" to display zoom lens on product image hover in single product page.', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'product-zoom-mobile',
							'type'     => 'switch',
							'title'    => __( 'Enable Image Zoom on Mobile', 'porto' ),
							'required' => array( 'product-zoom', 'equals', true ),
							'default'  => true,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'product-image-popup',
							'type'    => 'switch',
							'title'   => __( 'Enable Image Popup', 'porto' ),
							'desc'    => __( 'Select "Yes" to display the image gallery popup on click in single product page.', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'     => 'desc_info_zoom_type',
							'type'   => 'info',
							'title'  => __( 'Zoom Type For Single Product Page.', 'porto' ),
							'notice' => false,
						),
						array(
							'id'      => 'zoom-type',
							'type'    => 'button_set',
							'title'   => __( 'Zoom Type', 'porto' ),
							'desc'    => __( 'Select the type to zoom in/out image in single product page.', 'porto' ),
							'options' => array(
								'inner' => __( 'Inner', 'porto' ),
								'lens'  => __( 'Lens', 'porto' ),
							),
							'default' => 'inner',
						),
						array(
							'id'       => 'zoom-scroll',
							'type'     => 'switch',
							'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
							'title'    => __( 'Scroll Zoom', 'porto' ),
							'desc'     => __( 'Select "YES" to zoom in or out the product image by mouse scroll.', 'porto' ),
							'default'  => true,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'zoom-lens-size',
							'type'     => 'text',
							'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
							'title'    => __( 'Lens Size', 'porto' ),
							'desc'     => __( 'Input the zoom size of magnifier.', 'porto' ),
							'default'  => '200',
						),
						array(
							'id'       => 'zoom-lens-shape',
							'type'     => 'button_set',
							'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
							'title'    => __( 'Lens Shape', 'porto' ),
							'desc'     => __( 'Input the type of magnifier.', 'porto' ),
							'options'  => array(
								'round'  => __( 'Round', 'porto' ),
								'square' => __( 'Square', 'porto' ),
							),
							'default'  => 'square',
						),
						array(
							'id'       => 'zoom-contain-lens',
							'type'     => 'switch',
							'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
							'title'    => __( 'Contain Lens Zoom', 'porto' ),
							'default'  => true,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'zoom-lens-border',
							'type'     => 'text',
							'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
							'title'    => __( 'Lens Border', 'porto' ),
							'default'  => '1',
						),
						array(
							'id'       => 'zoom-border',
							'type'     => 'text',
							'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
							'title'    => __( 'Border Size', 'porto' ),
							'desc'     => __( 'Controls the border size of Lens.', 'porto' ),
							'default'  => '4',
						),
						array(
							'id'       => 'zoom-border-color',
							'type'     => 'color',
							'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
							'title'    => __( 'Border Color', 'porto' ),
							'desc'     => __( 'Controls the border color of Lens.', 'porto' ),
							'default'  => '#888888',
						),
					),
				);
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Cart & Checkout Page', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'      => 'product-crosssell',
							'type'    => 'switch',
							'title'   => __( 'Show Cross Sells', 'porto' ),
							'desc'    => __( 'Select "YES" to show cross-sell products.', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'product-crosssell-count',
							'type'     => 'text',
							'required' => array( 'product-crosssell', 'equals', true ),
							'title'    => __( 'Cross Sells Count', 'porto' ),
							'desc'     => __( 'Controls the count of product to show.', 'porto' ),
							'default'  => '8',
						),
						array(
							'id'        => 'cart-version',
							'type'      => 'button_set',
							'title'     => __( 'Cart Page Type', 'porto' ),
							'desc'      => __( 'Select the type of cart page layout.', 'porto' ),
							'options'   => array(
								'v1' => __( 'Type 1', 'porto' ),
								'v2' => __( 'Type 2', 'porto' ),
							),
							'default'   => 'v2',
							'transport' => 'refresh',
						),
						array(
							'id'      => 'checkout-version',
							'type'    => 'button_set',
							'title'   => __( 'Checkout Page Type', 'porto' ),
							'desc'    => __( 'Select the type of checkout page layout.', 'porto' ),
							'options' => array(
								'v1' => __( 'Type 1', 'porto' ),
								'v2' => __( 'Type 2', 'porto' ),
							),
							'default' => 'v1',
						),
					),
				);
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Catalog Mode', 'porto' ),
					'fields'     => array(
						array(
							'id'        => 'product-show-price-role',
							'type'      => 'button_set',
							'multi'     => true,
							'title'     => __( 'Select roles to see product price', 'porto' ),
							'desc'      => __( 'Show the product price by roles.', 'porto' ),
							'default'   => array(),
							'options'   => $all_roles,
							'transport' => 'refresh',
						),
						array(
							'id'      => 'catalog-enable',
							'type'    => 'switch',
							'title'   => __( 'Enable Catalog Mode', 'porto' ),
							'desc'    => __( 'Catalog mode is generally used to hide some product fields such as price and add to cart button on shop and product detail page.', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'catalog-admin',
							'type'     => 'switch',
							'title'    => __( 'Enable also for administrators', 'porto' ),
							'subtitle' => __( '"YES" option enables catalog mode to administrator also.', 'porto' ),
							'default'  => true,
							'required' => array( 'catalog-enable', 'equals', true ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'catalog-price',
							'type'     => 'switch',
							'title'    => __( 'Show Price', 'porto' ),
							'subtitle' => __( 'Select "YES" to show price on catalog mode.', 'porto' ),
							'default'  => false,
							'required' => array( 'catalog-enable', 'equals', true ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'catalog-review',
							'type'     => 'switch',
							'title'    => __( 'Show Reviews', 'porto' ),
							'subtitle' => __( 'Select "YES" to show reviews.', 'porto' ),
							'default'  => false,
							'required' => array( 'catalog-enable', 'equals', true ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'desc_info_add_cart',
							'type'     => 'info',
							'title'    => __( 'For Add To Cart Button', 'porto' ),
							'required' => array( 'catalog-enable', 'equals', true ),
							'notice'   => false,
						),
						array(
							'id'       => 'catalog-cart',
							'type'     => 'switch',
							'title'    => __( 'Show Add Cart Button', 'porto' ),
							'subtitle' => __( 'Select "YES" to show Add Cart Button on catalog mode.', 'porto' ),
							'default'  => false,
							'required' => array( 'catalog-enable', 'equals', true ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'catalog-readmore',
							'type'     => 'switch',
							'title'    => __( 'Show Read More Button', 'porto' ),
							'subtitle' => __( 'Select "YES" to show Read More Button on catalog mode.', 'porto' ),
							'default'  => false,
							'required' => array( 'catalog-cart', 'equals', false ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'catalog-readmore-target',
							'type'     => 'button_set',
							'title'    => __( 'Read More Button Target', 'porto' ),
							'subtitle' => __( 'Determines how to display the target of the linked URL.', 'porto' ),
							'required' => array( 'catalog-readmore', 'equals', true ),
							'options'  => array(
								''       => __( 'Self', 'porto' ),
								'_blank' => __( 'Blank', 'porto' ),
							),
							'default'  => '',
						),
						array(
							'id'       => 'catalog-readmore-label',
							'type'     => 'text',
							'required' => array( 'catalog-readmore', 'equals', true ),
							'title'    => __( 'Read More Button Label', 'porto' ),
							'subtitle' => __( 'Input the Label instead of "Read More".', 'porto' ),
							'default'  => 'Read More',
						),
						array(
							'id'       => 'catalog-readmore-archive',
							'type'     => 'button_set',
							'title'    => __( 'Use Read More Link in', 'porto' ),
							'required' => array( 'catalog-readmore', 'equals', true ),
							'options'  => array(
								'all'     => __( 'Product and Product Archives', 'porto' ),
								'product' => __( 'Product', 'porto' ),
							),
							'default'  => 'all',
						),
					),
				);
				// Register form
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Registration form', 'porto' ),
					'fields'     => array(
						array(
							'id'      => 'reg-form-info',
							'type'    => 'button_set',
							'title'   => __( 'Registration Fields', 'porto' ),
							'desc'    => __( 'If select "Full Info", extra fields such as first name, last name and password confirmation are added in registration form.', 'porto' ),
							'multi'   => false,
							'options' => array(
								'full'  => __( 'Full Info', 'porto' ),
								'short' => __( 'Short Info', 'porto' ),
							),
							'default' => 'short',
						),

					),
				);

				// WC Vendor
				if ( class_exists( 'WC_Vendors' ) ) {
					$this->sections[] = array(
						'title'      => __( 'Wc Vendor', 'porto' ),
						'icon'       => 'el el-usd',
						'customizer' => false,
						'fields'     => array(
							array(
								'id'    => 'desc_info_wc_vendor',
								'type'  => 'info',
								'title' => __( 'General Wc Vendor Shop Settings', 'porto' ),
							),
							array(
								'id'       => 'porto_wcvendors_phone',
								'type'     => 'switch',
								'title'    => __( 'Select Vendor Phone Number', 'porto' ),
								'compiler' => true,
								'default'  => '1',
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'porto_wcvendors_email',
								'type'     => 'switch',
								'title'    => __( 'Show Vendor Email', 'porto' ),
								'compiler' => true,
								'default'  => '1',
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'porto_wcvendors_url',
								'type'     => 'switch',
								'title'    => __( 'Show Vendor URL', 'porto' ),
								'compiler' => true,
								'default'  => '1',
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'    => 'desc_info_vendor_shop',
								'type'  => 'info',
								'title' => __( 'WC Vendors - Shop Page', 'porto' ),
							),

							array(
								'id'       => 'porto_wcvendors_shop_description',
								'type'     => 'switch',
								'title'    => __( 'Vendor Description on Top of Shop Page', 'porto' ),
								'compiler' => true,
								'default'  => '1',
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'porto_wcvendors_shop_avatar',
								'type'     => 'switch',
								'title'    => __( 'Show Vendor Avatar in Vendor Description', 'porto' ),
								'compiler' => true,
								'default'  => '1',
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'porto_wcvendors_shop_profile',
								'type'     => 'switch',
								'title'    => __( 'Show Social and Contact Info in Vendor Description', 'porto' ),
								'compiler' => true,
								'default'  => '1',
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'porto_wcvendors_shop_soldby',
								'type'     => 'switch',
								'title'    => __( 'Sold by" at Product List', 'porto' ),
								'compiler' => true,
								'default'  => '1',
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'    => 'desc_info_vendor_sp',
								'type'  => 'info',
								'title' => __( 'WC Vendors - Single Product Page', 'porto' ),
							),
							/*array(
							'id' => 'porto_single_wcvendors_hide_header',
							'type' => 'switch',
							'title' => __ ( 'Vendor Single Product Page Show Header', 'porto' ),
							'compiler' => true,
							'default' => '1',
							'on' => __('Yes','porto'),
							'off' =>  __('No','porto'),
							),*/
							array(
								'id'       => 'porto_single_wcvendors_product_description',
								'type'     => 'switch',
								'title'    => __( 'Vendor Description on Top of Single Product Page', 'porto' ),
								'compiler' => true,
								'default'  => '0',
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'porto_wcvendors_product_avatar',
								'type'     => 'switch',
								'title'    => __( 'Show Vendor Avatar in Vendor Description', 'porto' ),
								'compiler' => true,
								'default'  => '0',
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'porto_wcvendors_product_profile',
								'type'     => 'switch',
								'title'    => __( 'Show Social and Contact Info in Vendor Description', 'porto' ),
								'compiler' => true,
								'default'  => '0',
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'porto_wcvendors_product_tab',
								'type'     => 'switch',
								'title'    => __( '"Seller Info" at Product Tab', 'porto' ),
								'compiler' => true,
								'default'  => '0',
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'porto_wcvendors_product_moreproducts',
								'type'     => 'switch',
								'title'    => __( '"More From This Seller" Products', 'porto' ),
								'compiler' => true,
								'default'  => '0',
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'       => 'porto_wcvendors_product_soldby',
								'type'     => 'switch',
								'title'    => __( 'Sold by" at Product Meta', 'porto' ),
								'compiler' => true,
								'default'  => '0',
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
							array(
								'id'    => 'desc_info_vendor_cart',
								'type'  => 'info',
								'title' => __( 'WC Vendors - Cart Page', 'porto' ),
							),
							array(
								'id'       => 'porto_wcvendors_cartpage_soldby',
								'type'     => 'switch',
								'title'    => __( '"Sold by" at Cart page', 'porto' ),
								'compiler' => true,
								'default'  => '1',
								'on'       => __( 'Yes', 'porto' ),
								'off'      => __( 'No', 'porto' ),
							),
						),
					);
				}
				$this->sections[] = array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Styling', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'       => 'shop-add-links-color',
							'type'     => 'color',
							'title'    => 'Add Links Color',
							'subtitle' => __( 'Add to cart, Wishlist and Quick View Color on archive page', 'porto' ),
							'default'  => '#333333',
							'validate' => 'color',
							'selector' => array(
								'node' => 'ul.products, .porto-posts-grid',
							),
						),
						array(
							'id'       => 'shop-add-links-bg-color',
							'type'     => 'color',
							'title'    => 'Add Links Background Color',
							'subtitle' => __( 'Add to cart, Wishlist and Quick View Background Color on archive page', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
							'selector' => array(
								'node' => 'ul.products, .porto-posts-grid',
							),
						),
						array(
							'id'       => 'shop-add-links-border-color',
							'type'     => 'color',
							'title'    => 'Add Links Border Color',
							'subtitle' => __( 'Add to cart, Wishlist and Quick View Border Color on archive page', 'porto' ),
							'default'  => '#dddddd',
							'validate' => 'color',
							'selector' => array(
								'node' => 'ul.products, .porto-posts-grid',
							),
						),
						array(
							'id'       => 'hot-color',
							'type'     => 'color',
							'title'    => __( 'Hot Bg Color', 'porto' ),
							'subtitle' => __( 'Control the background of Hot label for featured product.', 'porto' ),
							'desc'     => __( 'To show Hot label, you should check <strong>WooComerce/Select labels to display</strong> option.', 'porto' ),
							'default'  => '#62b959',
							'validate' => 'color',
							'selector' => array(
								'node' => '.post-date, .onhot',
							),
						),
						array(
							'id'       => 'hot-color-inverse',
							'type'     => 'color',
							'title'    => __( 'Hot Text Color', 'porto' ),
							'subtitle' => __( 'Control the text color of Hot label for featured product.', 'porto' ),
							'desc'     => __( 'To show Hot label, you should check <strong>WooComerce/Select labels to display</strong> option.', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
							'selector' => array(
								'node' => '.post-date, .onhot',
							),
						),
						array(
							'id'       => 'sale-color',
							'type'     => 'color',
							'title'    => __( 'Sale Bg Color', 'porto' ),
							'subtitle' => __( 'Control the background of Sale label.', 'porto' ),
							'desc'     => __( 'To show Sale label, you should check <strong>WooComerce/Select labels to display</strong> option.', 'porto' ),
							'default'  => '#e27c7c',
							'validate' => 'color',
							'selector' => array(
								'node' => '.onsale',
							),
						),
						array(
							'id'       => 'sale-color-inverse',
							'type'     => 'color',
							'title'    => __( 'Sale Text Color', 'porto' ),
							'subtitle' => __( 'Control the text color of Sale label.', 'porto' ),
							'desc'     => __( 'To show Sale label, you should check <strong>WooComerce/Select labels to display</strong> option.', 'porto' ),
							'default'  => '#ffffff',
							'validate' => 'color',
							'selector' => array(
								'node' => '.onsale',
							),
						),
						array(
							'id'       => 'new-bgc',
							'type'     => 'color',
							'title'    => __( 'New Label Bg Color', 'porto' ),
							'subtitle' => __( 'Control the background of New label for products.', 'porto' ),
							'desc'     => __( 'To show New label, you should check <strong>WooComerce/Select labels to display</strong> option.', 'porto' ),
							'default'  => '',
							'validate' => 'color',
							'selector' => array(
								'node' => '.onnew',
							),
						),
						array(
							'id'          => 'add-to-cart-font',
							'type'        => 'typography',
							'title'       => __( 'Add to Cart Font', 'porto' ),
							'subtitle'    => __( 'Used in add to cart button, quickview, wishlist, price, etc', 'porto' ),
							'google'      => true,
							'subsets'     => false,
							'font-style'  => false,
							'text-align'  => false,
							'color'       => false,
							'font-weight' => false,
							'font-size'   => false,
							'line-height' => false,
							'default'     => array(
								'google'      => true,
								'font-family' => 'Open Sans',
							),
							'selector'    => array(
								'node' => ':root',
							),
						),
						array(
							'id'     => 'desc_info_wishlist_color',
							'type'   => 'info',
							'title'  => __( 'The below options request the YITH WooCommerce Wishlist.', 'porto' ),
							'notice' => false,
						),
						array(
							'id'       => 'wishlist-color',
							'type'     => 'color',
							'title'    => __( 'Wishlist and Compare Color on product page', 'porto' ),
							'default'  => '#302e2a',
							'validate' => 'color',
							'selector' => array(
								'node' => '.product-summary-wrap .yith-wcwl-add-to-wishlist, .product-summary-wrap .yith-compare',
							),
						),
						array(
							'id'       => 'wishlist-color-inverse',
							'type'     => 'color',
							'title'    => __( 'Wishlist and Compare Hover Color on product page', 'porto' ),
							'default'  => '',
							'validate' => 'color',
							'selector' => array(
								'node' => '.product-summary-wrap .yith-wcwl-add-to-wishlist, .product-summary-wrap .yith-compare',
							),
						),
					),
				);
			}
			// Extra
			$this->sections[] = array(
				'icon'       => 'icon-extra',
				'icon_class' => 'porto-icon',
				'title'      => __( 'Extra', 'porto' ),
				'customizer' => false,
			);

			$this->sections[] = array(
				'subsection' => true,
				'title'      => __( 'Google Map API', 'porto' ),
				'customizer' => false,
				'fields'     => array(
					array(
						'id'      => 'gmap_api',
						'type'    => 'text',
						'title'   => __( 'Google Map API Key', 'porto' ),
						'default' => '',
					),
				),
			);

			$this->sections[] = array(
				'title'      => __( 'SEO', 'porto' ),
				'customizer' => false,
				'subsection' => true,
				'fields'     => array(
					array(
						'id'      => 'rich-snippets',
						'type'    => 'switch',
						'title'   => __( 'Microdata Rich Snippets', 'porto' ),
						'desc'    => __( 'Select "Enable" to make Title, Update Date and Author rich snippets data site wide. For this option, you have to enable "Microdata Rich Snippets" of "Page Meta Options"', 'porto' ),
						'default' => true,
						'on'      => __( 'Enable', 'porto' ),
						'off'     => __( 'Disable', 'porto' ),
					),
					array(
						'id'      => 'mobile-menu-item-nofollow',
						'type'    => 'switch',
						'title'   => __( 'Add rel="nofollow" to mobile menu items', 'porto' ),
						'desc'    => __( 'Select "Yes" to add relationship attribute "nofollow" to the mobile menu items.', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'open-graph',
						'type'    => 'switch',
						'title'   => __( 'Open Graph Meta Tags', 'porto' ),
						'desc'    => __( 'Turn on to enable open graph meta tags which are mainly used when sharing pages on social networking sites like Facebook and Twitter.', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'desc_info_yoast',
						'type'     => 'info',
						'title'    => __( 'Compatible with Yoast SEO Plugin', 'porto' ),
						'descript' => __( 'Porto theme compatible with <a href="https://wordpress.org/plugins/wordpress-seo/">Yoast SEO</a> plugin.', 'porto' ),
						'notice'   => false,
					),
				),
			);
			// 404
			$this->sections[] = array(
				'title'      => __( '404 Error', 'porto' ),
				'customizer' => false,
				'subsection' => true,
				'fields'     => array(
					array(
						'id'      => 'error-block',
						'type'    => 'text',
						'title'   => __( '404 Links Block', 'porto' ),
						'desc'    => __( 'Input a block slug name. Show the block on the right space of 404 page.', 'porto' ),
						'default' => 'error-404',
					),
				),
			);

			// BBPress & BuddyPress
			$this->sections[] = array(
				'title'      => __( 'BBPress & BuddyPress', 'porto' ),
				'customizer' => false,
				'subsection' => true,
				'fields'     => array(
					array(
						'id'      => 'bb-layout',
						'type'    => 'image_select',
						'title'   => __( 'BBPress & BuddyPress Page Layout', 'porto' ),
						'options' => $page_layouts,
						'default' => 'fullwidth',
					),
					array(
						'id'       => 'bb-sidebar',
						'type'     => 'select',
						'title'    => __( 'Select Sidebar', 'porto' ),
						'required' => array( 'bb-layout', 'equals', $sidebars ),
						'data'     => 'sidebars',
					),
					array(
						'id'       => 'bb-sidebar2',
						'type'     => 'select',
						'title'    => __( 'Select Sidebar 2', 'porto' ),
						'required' => array( 'bb-layout', 'equals', $both_sidebars ),
						'data'     => 'sidebars',
					),
				),
			);

			// Social Share
			$this->sections[] = array(
				'title'      => __( 'Social Share', 'porto' ),
				'customizer' => false,
				'subsection' => true,
				'fields'     => array(
					array(
						'id'       => 'share-enable',
						'type'     => 'switch',
						'title'    => __( 'Show Social Links', 'porto' ),
						'subtitle' => __( 'To show social links, you should check <strong>Default</strong> or <strong>Yes</strong> of Share Meta Option.', 'porto' ),
						'desc'     => __( 'Show social links in post and product, page, portfolio, etc.', 'porto' ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-nofollow',
						'type'     => 'switch',
						'title'    => __( 'Add rel="nofollow" to social links', 'porto' ),
						'desc'     => __( 'Select "Yes" to add relationship attributes "nofollow" to the mobile menu items.', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-facebook',
						'type'     => 'switch',
						'title'    => __( 'Enable Facebook Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-twitter',
						'type'     => 'switch',
						'title'    => __( 'Enable Twitter Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-linkedin',
						'type'     => 'switch',
						'title'    => __( 'Enable LinkedIn Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-googleplus',
						'type'     => 'switch',
						'title'    => __( 'Enable Google + Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-pinterest',
						'type'     => 'switch',
						'title'    => __( 'Enable Pinterest Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-email',
						'type'     => 'switch',
						'title'    => __( 'Enable Email Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-vk',
						'type'     => 'switch',
						'title'    => __( 'Enable VK Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-xing',
						'type'     => 'switch',
						'title'    => __( 'Enable Xing Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-tumblr',
						'type'     => 'switch',
						'title'    => __( 'Enable Tumblr Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-reddit',
						'type'     => 'switch',
						'title'    => __( 'Enable Reddit Share', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-whatsapp',
						'type'     => 'switch',
						'title'    => __( 'Enable WhatsApp Share', 'porto' ),
						'desc'     => __( 'Only For Mobile', 'porto' ),
						'required' => array( 'share-enable', 'equals', true ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
				),
			);

			// Slider Config
			$this->sections[] = array(
				'title'      => __( 'Slider Config', 'porto' ),
				'customizer' => false,
				'subsection' => true,
				'fields'     => array(
					array(
						'id'    => 'desc_info_slider_option',
						'type'  => 'info',
						'desc'  => wp_kses(
							/* translators: %s: Builder url */
							sprintf( __( '<strong>Important Note:</strong> Controls the <b>Global Carousel Options</b> throughout the site. These Options can be replaced with widget options..', 'porto' ), $archive_url, $type_url ),
							array(
								'strong' => array(),
								'b'      => array(),
							)
						),
						'class' => 'porto-important-note',
					),
					array(
						'id'       => 'slider-loop',
						'type'     => 'switch',
						'title'    => __( 'Loop', 'porto' ),
						'subtitle' => __( 'Enable carousel items to slide infinitely.', 'porto' ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-autoplay',
						'type'     => 'switch',
						'title'    => __( 'Auto Play', 'porto' ),
						'subtitle' => __( 'Enable autoslide of carousel items.', 'porto' ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-speed',
						'type'     => 'text',
						'title'    => __( 'Play Speed', 'porto' ),
						'subtitle' => __( 'Change carousel item\'s autoplay duration.', 'porto' ),
						'required' => array( 'slider-autoplay', 'equals', true ),
						'desc'     => __( 'unit: millisecond', 'porto' ),
						'default'  => 5000,
					),
					array(
						'id'       => 'slider-autoheight',
						'type'     => 'switch',
						'title'    => __( 'Auto Height', 'porto' ),
						'subtitle' => __( 'Each slides have their own height. Slides could have different height.', 'porto' ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-nav',
						'type'     => 'switch',
						'title'    => __( 'Show Next/Prev Buttons', 'porto' ),
						'subtitle' => __( 'Determine whether to show/hide slider navigations.', 'porto' ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-nav-hover',
						'type'     => 'switch',
						'title'    => __( 'Show Next/Prev Buttons on Hover', 'porto' ),
						'subtitle' => __( 'Hides slider navs automatically and show them only if mouse is over.', 'porto' ),
						'required' => array( 'slider-nav', 'equals', true ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-margin',
						'type'     => 'switch',
						'title'    => __( 'Enable Margin', 'porto' ),
						'required' => array( 'slider-nav-hover', 'equals', false ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-dots',
						'type'     => 'switch',
						'title'    => __( 'Show Dots Navigation', 'porto' ),
						'subtitle' => __( 'Determine whether to show/hide slider dots.', 'porto' ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-animatein',
						'type'     => 'text',
						'title'    => __( 'Animate In', 'porto' ),
						'subtitle' => __( 'Choose sliding animation when next slides become visible.', 'porto' ),
						'default'  => '',
						'desc'     => __( 'Please input animation. Please reference <a href="http://daneden.github.io/animate.css/">animate.css</a>. ex: fadeIn', 'porto' ),
					),
					array(
						'id'       => 'slider-animateout',
						'type'     => 'text',
						'title'    => __( 'Animate Out', 'porto' ),
						'subtitle' => __( 'Choose sliding animation when previous slides become invisible.', 'porto' ),
						'default'  => '',
						'desc'     => __( 'Please input animation. Please reference <a href="http://daneden.github.io/animate.css/">animate.css</a>. ex: fadeOut', 'porto' ),
					),
				),
			);
		}
		public function setHelpTabs() {
		}
		public function setArguments() {
			$theme = wp_get_theme(); // For use with some settings. Not necessary.

			$header_html  = '<a class="porto-theme-link" href="' . esc_url( admin_url( 'admin.php?page=porto' ) ) . '">' . esc_html__( 'Dashboard', 'porto' ) . '</a>';
			$header_html .= '<a class="porto-theme-link" href="' . esc_url( admin_url( 'admin.php?page=porto-page-layouts' ) ) . '">' . esc_html__( 'Page Layouts', 'porto' ) . '</a>';
			if ( get_theme_mod( 'theme_options_use_new_style', false ) ) {
				$menu_title   = esc_html__( 'Advanced Options', 'porto' );
				$header_html .= '<a class="porto-theme-link" href="' . esc_url( admin_url( 'customize.php' ) ) . '">' . __( 'Theme Options', 'porto' ) . '</a>';
			} else {
				$menu_title   = esc_html__( 'Theme Options', 'porto' );
				$header_html .= '<a class="porto-theme-link active nolink" href="' . esc_url( admin_url( 'themes.php?page=porto_settings' ) ) . '">' . $menu_title . '</a>';
			}

			$header_html .= '<a class="porto-theme-link" href="' . esc_url( admin_url( 'admin.php?page=porto-setup-wizard' ) ) . '">' . esc_html__( 'Setup Wizard', 'porto' ) . '</a><a class="porto-theme-link" href="' . esc_url( admin_url( 'admin.php?page=porto-speed-optimize-wizard' ) ) . '">' . esc_html__( 'Speed Optimize Wizard', 'porto' ) . '</a><a class="porto-theme-link porto-theme-link-last" href="' . esc_url( admin_url( 'admin.php?page=porto-tools' ) ) . '">' . esc_html__( 'Tools', 'porto' ) . '</a>';

			if ( ! get_theme_mod( 'theme_options_use_new_style', false ) && $this->legacy_mode ) {
				$header_html .= '<a href="#" class="porto-theme-link switch-live-option-panel">' . esc_html__( 'Live Option Panel', 'porto' ) . '</a>';
			}

			$version_html = '<div class="header-left"><h1>' . $menu_title . '</h1><h6>' . __( 'Theme Options panel enables you full control over your website design and settings.', 'porto' ) . '</h6></div>';
			/* translators: theme version */
			$version_html .= '<div class="header-right"><div class="porto-logo"><img src="' . PORTO_URI . '/images/logo/logo_white_small.png" alt="Porto"><span class="version">' . sprintf( __( 'version %s', 'porto' ), PORTO_VERSION ) . '</span></div></div>';

			$this->args = array(
				'opt_name'                  => 'porto_settings',
				'display_name'              => '<span class="porto-admin-nav">' . $header_html . '</span>',
				'display_version'           => '<div class="porto-admin-header">' . $version_html . '</div>',
				'menu_type'                 => 'submenu',
				'allow_sub_menu'            => true,
				'menu_title'                => $menu_title,
				'page_title'                => $menu_title,
				'footer_credit'             => __( 'Porto Advanced Options', 'porto' ),
				'google_api_key'            => 'AIzaSyAX_2L_UzCDPEnAHTG7zhESRVpMPS4ssII',
				'disable_google_fonts_link' => true,
				'async_typography'          => false,
				'admin_bar'                 => false,
				'admin_bar_icon'            => 'dashicons-admin-generic',
				'admin_bar_priority'        => 50,
				'global_variable'           => '',
				'dev_mode'                  => false,
				'customizer'                => get_theme_mod( 'theme_options_use_new_style', false ),
				'compiler'                  => false,
				'page_priority'             => null,
				'page_parent'               => 'themes.php',
				'page_permissions'          => 'manage_options',
				'menu_icon'                 => '',
				'last_tab'                  => '',
				'page_icon'                 => 'icon-themes',
				'page_slug'                 => 'porto_settings',
				'save_defaults'             => true,
				'default_show'              => false,
				'default_mark'              => '',
				'show_import_export'        => true,
				'show_options_object'       => false,
				'transient_time'            => 60 * MINUTE_IN_SECONDS,
				'output'                    => false,
				'output_tag'                => true,
				'database'                  => '',
				'system_info'               => false,
				'hints'                     => array(
					'icon'          => 'icon-question-sign',
					'icon_position' => 'right',
					'icon_color'    => 'lightgray',
					'icon_size'     => 'normal',
					'tip_style'     => array(
						'color'   => 'light',
						'shadow'  => true,
						'rounded' => false,
						'style'   => '',
					),
					'tip_position'  => array(
						'my' => 'top left',
						'at' => 'bottom right',
					),
					'tip_effect'    => array(
						'show' => array(
							'effect'   => 'slide',
							'duration' => '500',
							'event'    => 'mouseover',
						),
						'hide' => array(
							'effect'   => 'slide',
							'duration' => '500',
							'event'    => 'click mouseleave',
						),
					),
				),
				'ajax_save'                 => true,
				'use_cdn'                   => true,
			);
			// Panel Intro text -> before the form
			if ( ! isset( $this->args['global_variable'] ) || false !== $this->args['global_variable'] ) {
				if ( ! empty( $this->args['global_variable'] ) ) {
					$v = $this->args['global_variable'];
				} else {
					$v = str_replace( '-', '_', $this->args['opt_name'] );
				}
			}
		}

		/**
		 * generates css variables
		 *
		 * @since 6.2.0
		 */
		public function get_css_vars() {
			if ( ! empty( $this->css_var_selectors ) ) {
				return $this->css_var_selectors;
			}
			if ( isset( $this->sections ) ) {
				foreach ( $this->sections as $sk => $section ) {
					if ( isset( $section['fields'] ) ) {
						foreach ( $section['fields'] as $k => $field ) {
							if ( empty( $field['id'] ) && empty( $field['type'] ) ) {
								continue;
							}
							if ( empty( $field['selector'] ) ) {
								continue;
							}

							if ( ! isset( $this->css_var_selectors[ $field['selector']['node'] ] ) ) {
								$this->css_var_selectors[ $field['selector']['node'] ] = array();
							}

							$arr = array( $field['id'] );
							if ( 'typography' == $field['type'] ) {
								$arr[] = '';
								$arr[] = 'typography';
							} else {
								if ( isset( $field['selector']['unit'] ) ) {
									$arr[] = $field['selector']['unit'];
								}
								if ( isset( $field['selector']['type'] ) ) {
									$arr[] = $field['selector']['type'];
								}
							}
							$this->css_var_selectors[ $field['selector']['node'] ][] = $arr;
						}
					}
				}
			}

			return $this->css_var_selectors;
		}
	}
	global $reduxPortoSettings;
	$reduxPortoSettings = new Redux_Framework_porto_settings();
}
