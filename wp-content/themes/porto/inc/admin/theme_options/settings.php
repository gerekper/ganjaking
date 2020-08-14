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
		public function __construct() {
			if ( ! class_exists( 'ReduxFramework' ) ) {
				return;
			}
			// This is needed. Bah WordPress bugs.  ;)
			if ( true == Redux_Helpers::isTheme( __FILE__ ) ) {
				$this->initSettings();
			} else {
				add_action( 'plugins_loaded', array( $this, 'initSettings' ), 10 );
			}

		}

		public function initSettings() {
			$this->theme = wp_get_theme();
			// Set the default arguments
			$this->setArguments();
			// Set a few help tabs so you can see how it's done
			$this->setHelpTabs();
			// Create the sections and fields
			$this->setSections();
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

			if ( current_user_can( 'manage_options' ) && is_admin() ) {
				$product_layouts = porto_get_post_type_items( 'product_layout' );
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
					$header_type = (int) $porto_settings['header-type'];
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

			// General Settings
			$general_site_loader_options = array(
				'subsection' => true,
				'id'         => 'general_layout_loading',
				'title'      => __( 'Site Loader', 'porto' ),
				'customizer' => false,
				'fields'     => array(
					array(
						'id'         => 'show-loading-overlay',
						'type'       => 'switch',
						'title'      => __( 'Loading Overlay', 'porto' ),
						'default'    => false,
						'on'         => __( 'Show', 'porto' ),
						'off'        => __( 'Hide', 'porto' ),
						'customizer' => false,
					),
				),
			);
			$general_layout_options      = array(
				'subsection'      => true,
				'id'              => 'general_layout',
				'title'           => __( 'Layout', 'porto' ),
				'customizer_only' => true,
				'fields'          => array(
					array(
						'id'      => 'wrapper',
						'type'    => 'image_select',
						'title'   => __( 'Body Wrapper', 'porto' ),
						'options' => $body_wrapper,
						'default' => 'full',
					),
					array(
						'id'      => 'layout',
						'type'    => 'image_select',
						'title'   => __( 'Page Layout', 'porto' ),
						'options' => $page_layouts,
						'default' => 'right-sidebar',
					),
					array(
						'id'       => 'sidebar',
						'type'     => 'select',
						'title'    => __( 'Select Sidebar', 'porto' ),
						'required' => array( 'layout', 'equals', $sidebars ),
						'data'     => 'sidebars',
						'default'  => 'blog-sidebar',
					),
					array(
						'id'       => 'sidebar2',
						'type'     => 'select',
						'title'    => __( 'Select Sidebar 2', 'porto' ),
						'required' => array( 'layout', 'equals', $both_sidebars ),
						'data'     => 'sidebars',
						'default'  => 'secondary-sidebar',
					),
					array(
						'id'       => 'header-wrapper',
						'type'     => 'image_select',
						'title'    => __( 'Header Wrapper', 'porto' ),
						'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
						'options'  => $wrapper,
						'default'  => 'full',
					),
					array(
						'id'       => 'banner-wrapper',
						'type'     => 'image_select',
						'title'    => __( 'Banner Wrapper', 'porto' ),
						'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
						'options'  => $banner_wrapper,
						'default'  => 'wide',
					),
					array(
						'id'       => 'breadcrumbs-wrapper',
						'type'     => 'image_select',
						'title'    => __( 'Breadcrumbs Wrapper', 'porto' ),
						'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
						'options'  => $wrapper,
						'default'  => 'full',
					),
					array(
						'id'       => 'main-wrapper',
						'type'     => 'image_select',
						'title'    => __( 'Page Content Wrapper', 'porto' ),
						'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
						'options'  => $banner_wrapper,
						'default'  => 'wide',
					),
					array(
						'id'       => 'footer-wrapper',
						'type'     => 'image_select',
						'title'    => __( 'Footer Wrapper', 'porto' ),
						'required' => array( 'wrapper', 'equals', array( 'full', 'wide' ) ),
						'options'  => $wrapper,
						'default'  => 'full',
					),
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
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
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
						'id'         => 'show-skeleton-screen',
						'type'       => 'button_set',
						'title'      => __( 'Show Skeleton Screens', 'porto' ),
						'desc'       => __( 'This will show skeleton screens during page load for the selected pages.', 'porto' ),
						'multi'      => true,
						'options'    => array(
							'shop'      => __( 'Shop Pages', 'porto' ),
							'product'   => __( 'Product Page', 'porto' ),
							'quickview' => __( 'Product Quickview', 'porto' ),
							'blog'      => __( 'Blog Pages', 'porto' ),
						),
						'default'    => array(),
						'customizer' => false,
					),
					array(
						'id'         => 'show-icon-menus-mobile',
						'type'       => 'button_set',
						'title'      => __( 'Show Sticky Icon Menu bar on mobile', 'porto' ),
						'desc'       => __( 'This will show sticky icon menu bar at the bottom of the page on mobile.', 'porto' ),
						'multi'      => true,
						'options'    => array(
							'home'     => __( 'Home', 'porto' ),
							'blog'     => __( 'Blog', 'porto' ),
							'shop'     => __( 'Shop', 'porto' ),
							'wishlist' => __( 'Wishlist', 'porto' ),
							'account'  => __( 'Account', 'porto' ),
							'cart'     => __( 'Cart', 'porto' ),
						),
						'default'    => array(),
					),
					array(
						'id'      => 'sticky-icon-home',
						'type'    => 'text',
						'title'   => __( 'Home Icon', 'porto' ),
						'default' => __( 'porto-icon-category-home', 'porto' ),
						'class'   => __( 'sticky-home sticky-icon' ),
						'required' => array( 'show-icon-menus-mobile', 'contains', 'home' ),
					),
					array(
						'id'      => 'sticky-icon-blog',
						'type'    => 'text',
						'title'   => __( 'Blog Icon', 'porto' ),
						'default' => __( 'far fa-calendar-alt', 'porto' ),
						'class'   => __( 'sticky-blog sticky-icon' ),
						'required' => array( 'show-icon-menus-mobile', 'contains', 'blog' ),
					),
					array(
						'id'      => 'sticky-icon-shop',
						'type'    => 'text',
						'title'   => __( 'Shop Icon', 'porto' ),
						'default' => __( 'porto-icon-bars', 'porto' ),
						'class'   => __( 'sticky-shop sticky-icon' ),
						'required' => array( 'show-icon-menus-mobile', 'contains', 'shop' ),
					),
					array(
						'id'      => 'sticky-icon-wishlist',
						'type'    => 'text',
						'title'   => __( 'Wishlist Icon', 'porto' ),
						'default' => __( 'porto-icon-wishlist-2', 'porto' ),
						'class'   => __( 'sticky-wishlist sticky-icon' ),
						'required' => array( 'show-icon-menus-mobile', 'contains', 'wishlist' ),
					),
					array(
						'id'      => 'sticky-icon-account',
						'type'    => 'text',
						'title'   => __( 'Account Icon', 'porto' ),
						'default' => __( 'porto-icon-user-2', 'porto' ),
						'class'   => __( 'sticky-account sticky-icon' ),
						'required' => array( 'show-icon-menus-mobile', 'contains', 'account' ),
					),		
					array(
						'id'      => 'sticky-icon-cart',
						'type'    => 'text',
						'title'   => __( 'Cart Icon', 'porto' ),
						'default' => __( 'porto-icon-shopping-cart', 'porto' ),
						'class'   => __( 'sticky-cart sticky-icon' ),
						'required' => array( 'show-icon-menus-mobile', 'contains', 'cart' ),
					),																								
				),
			);

			if ( $options_style ) {
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon'       => 'icon-general',
						'icon_class' => 'porto-icon',
						'title'      => __( 'General', 'porto' ),
					),
					false,
					$general_site_loader_options
				);
				$this->sections[] = $general_layout_options;
			} else {
				$this->sections[] = $this->add_customizer_field(
					array(
						'icon'       => 'icon-general',
						'icon_class' => 'porto-icon',
						'title'      => __( 'General', 'porto' ),
					),
					$options_style,
					$general_site_loader_options,
					$general_layout_options
				);
			}

			$this->sections[] = $this->add_customizer_field(
				array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Theme Layout', 'porto' ),
					'fields'     => array(
						array(
							'id'      => 'general_theme_layout',
							'type'    => 'raw',
							'content' => '<img style="max-width: 100%;" src="' . PORTO_OPTIONS_URI . '/layouts/theme_layout.jpg" alt="Porto Theme Layout" />',
						),
					),
				),
				$options_style
			);
			$this->sections[] = $this->add_customizer_field(
				array(
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
							'desc'     => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
						),
						array(
							'id'    => 'html-content-top',
							'type'  => 'ace_editor',
							'mode'  => 'html',
							'title' => __( 'Content Top', 'porto' ),
							'desc'     => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
						),
						array(
							'id'    => 'html-content-inner-top',
							'type'  => 'ace_editor',
							'mode'  => 'html',
							'title' => __( 'Content Inner Top', 'porto' ),
							'desc'     => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
						),
						array(
							'id'    => 'html-content-inner-bottom',
							'type'  => 'ace_editor',
							'mode'  => 'html',
							'title' => __( 'Content Inner Bottom', 'porto' ),
							'desc'     => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
						),
						array(
							'id'    => 'html-content-bottom',
							'type'  => 'ace_editor',
							'mode'  => 'html',
							'title' => __( 'Content Bottom', 'porto' ),
							'desc'     => __( 'You can add any html or shortcodes here. If you want to add porto block, you can use [porto_block name="{block_slug}"].', 'porto' ),
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
				),
				$options_style
			);
			$this->sections[] = $this->add_customizer_field(
				array(
					'icon_class' => 'icon',
					'subsection' => true,
					'title'      => __( 'Logo, Icons', 'porto' ),
					'id'         => 'logo-icons',
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'     => '1',
							'type'   => 'info',
							'title'  => __( 'Logo', 'porto' ),
							'notice' => false,
						),
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
						),
						array(
							'id'       => 'sticky-logo',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Logo in Sticky Menu', 'porto' ),
						),
						array(
							'id'       => 'sticky-logo-retina',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Retina Logo in Sticky Menu', 'porto' ),
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
							'id'     => '1',
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
							'id'     => '1',
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
								'url' => PORTO_URI . '/images/logo/favicon.ico',
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
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Site Search', 'porto' ),
				'customizer' => false,
				'fields'     => array(
					array(
						'id'      => 'search-live',
						'type'    => 'switch',
						'title'   => __( 'Live Search', 'porto' ),
						'default' => true,
					),
					array(
						'id'       => 'search-sku',
						'type'     => 'switch',
						'title'    => __( 'Search by SKU', 'porto' ),
						'subtitle' => __( 'Allow search by SKU in live search', 'porto' ),
						'default'  => false,
						'required' => array( 'search-live', 'equals', true ),
					),
					array(
						'id'       => 'search-product_tag',
						'type'     => 'switch',
						'title'    => __( 'Search by Product Tag', 'porto' ),
						'subtitle' => __( 'Allow search by product tags in live search', 'porto' ),
						'default'  => false,
						'required' => array( 'search-live', 'equals', true ),
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
				'fields'     => array(
					array(
						'id'        => 'button-style',
						'type'      => 'button_set',
						'title'     => __( 'Button Style', 'porto' ),
						'options'   => array(
							''            => __( 'Default', 'porto' ),
							'btn-borders' => __( 'Borders', 'porto' ),
						),
						'default'   => '',
						'transport' => 'refresh',
					),
					array(
						'id'       => 'skin-color',
						'type'     => 'color',
						'title'    => __( 'Primary Color', 'porto' ),
						'default'  => '#0088cc',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'skin-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Primary Inverse Color', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'secondary-color',
						'type'     => 'color',
						'title'    => __( 'Secondary Color', 'porto' ),
						'default'  => '#e36159',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'secondary-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Secondary Inverse Color', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'tertiary-color',
						'type'     => 'color',
						'title'    => __( 'Tertiary Color', 'porto' ),
						'default'  => '#2baab1',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'tertiary-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Tertiary Inverse Color', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'quaternary-color',
						'type'     => 'color',
						'title'    => __( 'Quaternary Color', 'porto' ),
						'default'  => '#383f48',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'quaternary-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Quaternary Inverse Color', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'dark-color',
						'type'     => 'color',
						'title'    => __( 'Dark Color', 'porto' ),
						'default'  => '#212529',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'dark-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Dark Inverse Color', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'light-color',
						'type'     => 'color',
						'title'    => __( 'Light Color', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'light-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Light Inverse Color', 'porto' ),
						'default'  => '#212529',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'placeholder-color',
						'type'     => 'color',
						'title'    => __( 'Placeholder Image Background Color', 'porto' ),
						'default'  => '#f4f4f4',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'social-color',
						'type'     => 'button_set',
						'title'    => __( 'Social Links Color', 'porto' ),
						'options'  => array(
							''        => __( 'Default', 'porto' ),
							'primary' => __( 'Primary Color', 'porto' ),
						),
						'default'  => '',
						'compiler' => true,
					),
				),
			);
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Layout', 'porto' ),
				'fields'     => array(
					array(
						'id'       => 'css-type',
						'type'     => 'button_set',
						'title'    => __( 'Background Type', 'porto' ),
						'options'  => array(
							''     => __( 'Light', 'porto' ),
							'dark' => __( 'Dark', 'porto' ),
						),
						'default'  => '',
						'compiler' => true,
					),
					array(
						'id'       => 'color-dark',
						'type'     => 'color',
						'required' => array( 'css-type', 'equals', 'dark' ),
						'title'    => __( 'Basic Background Color', 'porto' ),
						'default'  => '#1d2127',
						'validate' => 'color',
						'compiler' => true,
					),
					array(
						'id'       => 'container-width',
						'type'     => 'text',
						'title'    => __( 'Container Max Width (px)', 'porto' ),
						'subtitle' => '960 - 1920',
						'default'  => '1140',
						'compiler' => true,
					),
					array(
						'id'       => 'grid-gutter-width',
						'type'     => 'button_set',
						'title'    => __( 'Grid Gutter Width', 'porto' ),
						'options'  => array(
							'16' => '16px',
							'20' => '20px',
							'24' => '24px',
							'30' => '30px',
						),
						'default'  => '30',
						'compiler' => true,
					),
					array(
						'id'       => 'border-radius',
						'type'     => 'switch',
						'title'    => __( 'Border Radius', 'porto' ),
						'default'  => true,
						'compiler' => true,
					),
					array(
						'id'       => 'thumb-padding',
						'type'     => 'switch',
						'title'    => __( 'Thumbnail Padding', 'porto' ),
						'default'  => false,
						'compiler' => true,
					),
				),
			);
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
						'desc'    => sprintf( esc_html__( 'By using this option, you can increase page speed about 4 percent in %1$sGoogle PageSpeed Insights%2$s for both of mobile and desktop.', 'porto' ), '<a href="https://developers.google.com/speed/pagespeed/insights/" target="_blank">', '</a>' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'             => 'body-font',
						'type'           => 'typography',
						'title'          => __( 'Body Font', 'porto' ),
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
							'font-weight'    => '400',
							'font-family'    => 'Open Sans',
							'font-size'      => '36px',
							'line-height'    => '44px',
							'letter-spacing' => '',
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
							'font-weight'    => '400',
							'font-family'    => 'Open Sans',
							'font-size'      => '30px',
							'line-height'    => '40px',
							'letter-spacing' => '',
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
							'font-weight'    => '400',
							'font-family'    => 'Open Sans',
							'font-size'      => '25px',
							'line-height'    => '32px',
							'letter-spacing' => '',
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
							'font-weight'    => '400',
							'font-family'    => 'Open Sans',
							'font-size'      => '20px',
							'line-height'    => '27px',
							'letter-spacing' => '',
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
							'font-weight'    => '400',
							'font-family'    => 'Open Sans',
							'font-size'      => '14px',
							'line-height'    => '18px',
							'letter-spacing' => '',
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
							'font-weight'    => '400',
							'font-family'    => 'Open Sans',
							'font-size'      => '14px',
							'line-height'    => '18px',
							'letter-spacing' => '',
						),
					),
					array(
						'id'             => 'paragraph-font',
						'type'           => 'typography',
						'title'          => __( 'Paragraph Font', 'porto' ),
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
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Backgrounds', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Body Background', 'porto' ),
						'notice' => false,
					),
					array(
						'id'    => 'body-bg',
						'type'  => 'background',
						'title' => __( 'Background', 'porto' ),
					),
					array(
						'id'      => 'body-bg-gradient',
						'type'    => 'switch',
						'title'   => __( 'Enable Background Gradient', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'body-bg-gcolor',
						'type'     => 'color_gradient',
						'title'    => __( 'Background Gradient Color', 'porto' ),
						'required' => array( 'body-bg-gradient', 'equals', true ),
						'default'  => array(
							'from' => '',
							'to'   => '',
						),
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Page Content Background', 'porto' ),
						'notice' => false,
					),
					array(
						'id'    => 'content-bg',
						'type'  => 'background',
						'title' => __( 'Background', 'porto' ),
					),
					array(
						'id'      => 'content-bg-gradient',
						'type'    => 'switch',
						'title'   => __( 'Enable Background Gradient', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'content-bg-gcolor',
						'type'     => 'color_gradient',
						'title'    => __( 'Background Gradient Color', 'porto' ),
						'required' => array( 'content-bg-gradient', 'equals', true ),
						'default'  => array(
							'from' => '',
							'to'   => '',
						),
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Content Bottom Widgets Area', 'porto' ),
						'notice' => false,
					),
					array(
						'id'    => 'content-bottom-bg',
						'type'  => 'background',
						'title' => __( 'Background', 'porto' ),
					),
					array(
						'id'      => 'content-bottom-bg-gradient',
						'type'    => 'switch',
						'title'   => __( 'Enable Background Gradient', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'content-bottom-bg-gcolor',
						'type'     => 'color_gradient',
						'title'    => __( 'Background Gradient Color', 'porto' ),
						'required' => array( 'content-bottom-bg-gradient', 'equals', true ),
						'default'  => array(
							'from' => '',
							'to'   => '',
						),
					),
					array(
						'id'      => 'content-bottom-padding',
						'type'    => 'spacing',
						'mode'    => 'padding',
						'title'   => __( 'Padding', 'porto' ),
						'default' => array(
							'padding-top'    => 0,
							'padding-bottom' => 20,
						),
					),
				),
			);
			$this->sections[] = array(
				'id'         => 'skin-header',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Header', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'     => '1',
						'type'   => 'info',
						'desc'   => wp_kses(
							/* translators: %s: Header layout settings url */
							sprintf( __( 'Go to <a %s>Header Layout Settings</a>', 'porto' ), 'href="header-settings" class="goto-section"', 'porto' ),
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
						'id'     => '1',
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
											'url'       => home_url(),
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
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Header Wrapper', 'porto' ),
						'notice' => false,
					),
					array(
						'id'      => 'header-wrap-bg',
						'type'    => 'background',
						'title'   => __( 'Background', 'porto' ),
						'default' => array(
							'background-color' => '',
						),
					),
					array(
						'id'      => 'header-wrap-bg-gradient',
						'type'    => 'switch',
						'title'   => __( 'Background Gradient', 'porto' ),
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
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Header', 'porto' ),
						'notice' => false,
					),
					array(
						'id'      => 'header-bg',
						'type'    => 'background',
						'title'   => __( 'Background', 'porto' ),
						'default' => array(
							'background-color' => '#ffffff',
						),
					),
					array(
						'id'      => 'header-bg-gradient',
						'type'    => 'switch',
						'title'   => __( 'Background Gradient', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'header-bg-gcolor',
						'type'     => 'color_gradient',
						'title'    => __( 'Background Gradient Color', 'porto' ),
						'required' => array( 'header-bg-gradient', 'equals', true ),
						'default'  => array(
							'from' => '#f6f6f6',
							'to'   => '#ffffff',
						),
					),
					array(
						'id'       => 'header-text-color',
						'type'     => 'color',
						'title'    => __( 'Text Color', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'      => 'header-link-color',
						'type'    => 'link_color',
						'active'  => false,
						'title'   => __( 'Link Color', 'porto' ),
						'default' => array(
							'regular' => '#999999',
							'hover'   => '#999999',
						),
					),
					array(
						'id'      => 'header-top-border',
						'type'    => 'border',
						'all'     => true,
						'style'   => false,
						'title'   => __( 'Top Border', 'porto' ),
						'default' => array(
							'border-color' => '#ededed',
							'border-top'   => '3px',
						),
					),
					array(
						'id'      => 'header-margin',
						'type'    => 'spacing',
						'mode'    => 'margin',
						'title'   => __( 'Margin', 'porto' ),
						'default' => array(
							'margin-top'    => 0,
							'margin-bottom' => 0,
							'margin-left'   => 0,
							'margin-right'  => 0,
						),
					),
					array(
						'id'      => 'header-main-padding',
						'type'    => 'spacing',
						'mode'    => 'padding',
						'title'   => __( 'Header Main Padding', 'porto' ),
						'left'    => false,
						'right'   => false,
						'units'   => 'px',
						'default' => array(
							'padding-top'    => '',
							'padding-bottom' => '',
						),
					),
					array(
						'id'      => 'header-main-padding-mobile',
						'type'    => 'spacing',
						'mode'    => 'padding',
						'title'   => __( 'Header Main Padding (window width < 992px)', 'porto' ),
						'left'    => false,
						'right'   => false,
						'default' => array(
							'padding-top'    => '',
							'padding-bottom' => '',
						),
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Sticky Header', 'porto' ),
						'notice' => false,
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'desc'   => wp_kses(
							/* translators: %s: Sticky header layout settings url */
							sprintf( __( 'Go to <a %s>Sticky Header Layout Settings</a>', 'porto' ), 'href="enable-sticky-header" class="goto-section field-control"', 'porto' ),
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
						'id'      => 'sticky-header-bg',
						'type'    => 'background',
						'title'   => __( 'Background', 'porto' ),
						'default' => array(
							'background-color' => '#ffffff',
						),
					),
					array(
						'id'      => 'sticky-header-bg-gradient',
						'type'    => 'switch',
						'title'   => __( 'Background Gradient', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'sticky-header-bg-gcolor',
						'type'     => 'color_gradient',
						'title'    => __( 'Background Gradient Color', 'porto' ),
						'required' => array( 'sticky-header-bg-gradient', 'equals', true ),
						'default'  => array(
							'from' => '#f6f6f6',
							'to'   => '#ffffff',
						),
					),
					array(
						'id'      => 'sticky-header-opacity',
						'type'    => 'text',
						'title'   => __( 'Background Opacity', 'porto' ),
						'default' => '100%',
					),
					array(
						'id'      => 'mainmenu-wrap-padding-sticky',
						'type'    => 'spacing',
						'mode'    => 'padding',
						'title'   => __( 'Padding', 'porto' ),
						'default' => array(
							'padding-top'    => 8,
							'padding-bottom' => 8,
							'padding-left'   => 0,
							'padding-right'  => 0,
						),
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Skin option when banner show behind header', 'porto' ),
						'notice' => false,
					),
					array(
						'id'      => 'header-opacity',
						'type'    => 'text',
						'title'   => __( 'Header Opacity', 'porto' ),
						'default' => '80%',
					),
					array(
						'id'      => 'searchform-opacity',
						'type'    => 'text',
						'title'   => __( 'Search Form Opacity', 'porto' ),
						'default' => '50%',
					),
					array(
						'id'      => 'menuwrap-opacity',
						'type'    => 'text',
						'title'   => __( 'Menu Wrap Opacity', 'porto' ),
						'default' => '30%',
					),
					array(
						'id'      => 'menu-opacity',
						'type'    => 'text',
						'title'   => __( 'Menu Opacity', 'porto' ),
						'default' => '30%',
					),
					array(
						'id'      => 'header-fixed-show-bottom',
						'type'    => 'switch',
						'title'   => __( 'Show Bottom Border', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Header Top', 'porto' ),
						'notice' => false,
					),
					array(
						'id'       => 'header-top-bg-color',
						'type'     => 'color',
						'title'    => __( 'Background Color', 'porto' ),
						'default'  => '#f4f4f4',
						'validate' => 'color',
					),
					array(
						'id'      => 'header-top-height',
						'type'    => 'slider',
						'title'   => __( 'Header Top Height', 'porto' ),
						'default' => 30,
						'min'     => 25,
						'max'     => 500,
					),
					array(
						'id'      => 'header-top-font-size',
						'type'    => 'text',
						'title'   => __( 'Header Top Font Size', 'porto' ),
						'desc'    => __( 'unit: px', 'porto' ),
						'default' => '',
					),
					array(
						'id'      => 'header-top-bottom-border',
						'type'    => 'border',
						'all'     => true,
						'style'   => false,
						'title'   => __( 'Bottom Border', 'porto' ),
						'default' => array(
							'border-color' => '#ededed',
							'border-top'   => '1px',
						),
					),
					array(
						'id'       => 'header-top-text-color',
						'type'     => 'color',
						'title'    => __( 'Text Color', 'porto' ),
						'default'  => '#777777',
						'validate' => 'color',
					),
					array(
						'id'      => 'header-top-link-color',
						'type'    => 'link_color',
						'active'  => false,
						'title'   => __( 'Link Color', 'porto' ),
						'default' => array(
							'regular' => '#0088cc',
							'hover'   => '#0099e6',
						),
					),
					array(
						'id'      => 'header-top-menu-padding',
						'type'    => 'spacing',
						'mode'    => 'padding',
						'title'   => __( 'Top Menu Padding', 'porto' ),
						'default' => array(
							'padding-top'    => 5,
							'padding-bottom' => 5,
							'padding-left'   => 5,
							'padding-right'  => 5,
						),
					),
					array(
						'id'      => 'header-top-menu-hide-sep',
						'type'    => 'switch',
						'title'   => __( 'Hide Top Menu Separator', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => wp_kses(
							sprintf(
								/* translators: %s: Header Builder url */
								__( 'Header Bottom (This is used only if you use header built using <a href="%s" class="goto-header-builder">Header Builder</a>.)', 'porto' ),
								esc_url(
									add_query_arg(
										array(
											'autofocus' => array(
												'section' => 'porto_header_layouts',
											),
											'url'       => home_url(),
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
						'title'    => __( 'Background Color', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'header-bottom-container-bg-color',
						'type'     => 'color',
						'title'    => __( 'Container Background Color', 'porto' ),
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
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'      => 'header-bottom-link-color',
						'type'    => 'link_color',
						'active'  => false,
						'title'   => __( 'Link Color', 'porto' ),
						'default' => array(
							'regular' => '',
							'hover'   => '',
						),
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Side Navigation', 'porto' ),
						'notice' => false,
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
			$this->sections[] = array(
				'id'         => 'skin-main-menu',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Main Menu', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'     => '1',
						'type'   => 'info',
						'desc'   => wp_kses(
							/* translators: %s: Menu layout settings url */
							sprintf( __( 'Go to <a %s> Menu Layout Settings</a>', 'porto' ), 'href="menu" class="goto-section"', 'porto' ),
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
						'id'       => 'mainmenu-wrap-bg-color',
						'type'     => 'color',
						'title'    => __( 'Wrapper Background Color', 'porto' ),
						'subtitle' => __( 'if header type is 1, 4, 9, 13, 14, 17 or header builder', 'porto' ),
						'default'  => 'transparent',
						'validate' => 'color',
					),
					array(
						'id'       => 'mainmenu-wrap-bg-color-sticky',
						'type'     => 'color',
						'title'    => __( 'Wrapper Background Color in Sticky Header', 'porto' ),
						'subtitle' => __( 'if header type is 1, 4, 9, 13, 14, 17 or header builder', 'porto' ),
						'validate' => 'color',
					),
					array(
						'id'       => 'mainmenu-wrap-padding',
						'type'     => 'spacing',
						'mode'     => 'padding',
						'title'    => __( 'Wrapper Padding', 'porto' ),
						'subtitle' => __( 'if header type is 1, 4, 9, 13, 14, 17 or header builder', 'porto' ),
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
						'title'    => __( 'Background Color', 'porto' ),
						'default'  => 'transparent',
						'validate' => 'color',
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Top Level Menu Item', 'porto' ),
						'notice' => false,
					),
					array(
						'id'             => 'menu-font',
						'type'           => 'typography',
						'title'          => __( 'Menu Font', 'porto' ),
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
						'id'             => 'menu-font-md',
						'type'           => 'typography',
						'title'          => __( 'Menu Font (window width < 992px)', 'porto' ),
						'google'         => false,
						'subsets'        => false,
						'font-style'     => false,
						'font-weight'    => false,
						'font-family'    => false,
						'text-align'     => false,
						'color'          => false,
						'letter-spacing' => true,
						'default'        => array(
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
					),
					array(
						'id'      => 'menu-text-transform',
						'type'    => 'button_set',
						'title'   => __( 'Text Transform', 'porto' ),
						'options' => array(
							'none'       => __( 'None', 'porto' ),
							'capitalize' => __( 'Capitalize', 'porto' ),
							'uppercase'  => __( 'Uppercase', 'porto' ),
							'lowercase'  => __( 'Lowercase', 'porto' ),
							'initial'    => __( 'Initial', 'porto' ),
						),
						'default' => 'uppercase',
					),
					array(
						'id'      => 'mainmenu-toplevel-link-color',
						'type'    => 'link_color',
						'active'  => false,
						'title'   => __( 'Link Color', 'porto' ),
						'default' => array(
							'regular' => '#0088cc',
							'hover'   => '#ffffff',
						),
					),
					array(
						'id'     => 'mainmenu-toplevel-link-color-sticky',
						'type'   => 'link_color',
						'active' => true,
						'title'  => __( 'Link Color in Sticky Header', 'porto' ),
					),
					array(
						'id'       => 'mainmenu-toplevel-hbg-color',
						'type'     => 'color',
						'title'    => __( 'Hover Background Color', 'porto' ),
						'default'  => '#0088cc',
						'validate' => 'color',
					),
					array(
						'id'      => 'mainmenu-toplevel-config-active',
						'type'    => 'switch',
						'title'   => __( 'Configure Active Color', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
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
						'id'      => 'mainmenu-toplevel-padding1',
						'type'    => 'spacing',
						'mode'    => 'padding',
						'title'   => __( 'Padding on Desktop', 'porto' ),
						'desc'    => __( 'This is not working for sidebar menus.', 'porto' ),
						'default' => array(
							'padding-top'    => 10,
							'padding-bottom' => 10,
							'padding-left'   => 16,
							'padding-right'  => 16,
						),
					),
					array(
						'id'      => 'mainmenu-toplevel-padding2',
						'type'    => 'spacing',
						'mode'    => 'padding',
						'title'   => __( 'Padding on Desktop (width > 991px)', 'porto' ),
						'desc'    => __( 'This is not working for sidebar menus.', 'porto' ),
						'default' => array(
							'padding-top'    => 9,
							'padding-bottom' => 9,
							'padding-left'   => 14,
							'padding-right'  => 14,
						),
					),
					array(
						'id'    => 'mainmenu-toplevel-padding3',
						'type'  => 'spacing',
						'mode'  => 'padding',
						'title' => __( 'Padding on Sticky Header (width > 991px)', 'porto' ),
						'desc'  => __( 'This is not working for sidebar menus. Please leave blank if you use same values with the ones in default header.', 'porto' ),
					),
					array(
						'id'     => '1',
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
						'default'  => '#333333',
						'validate' => 'color',
					),
					array(
						'id'      => 'mainmenu-popup-text-color',
						'type'    => 'link_color',
						'active'  => false,
						'title'   => __( 'Link Color', 'porto' ),
						'default' => array(
							'regular' => '#777777',
							'hover'   => '#777777',
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
						'id'      => 'mainmenu-popup-narrow-type',
						'type'    => 'button_set',
						'title'   => __( 'Narrow Menu Style', 'porto' ),
						'desc'    => __( 'if narrow menu style is "Style 2", please select "Top Level Menu Item / Hover Background Color".', 'porto' ),
						'options' => array(
							''  => __( 'With Popup BG Color', 'porto' ),
							'1' => __( 'With Top Menu Hover Bg Color', 'porto' ),
						),
						'default' => '',
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Tip', 'porto' ),
						'notice' => false,
					),
					array(
						'id'       => 'mainmenu-tip-bg-color',
						'type'     => 'color',
						'title'    => __( 'Tip Background Color', 'porto' ),
						'default'  => '#0cc485',
						'validate' => 'color',
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Menu Custom Content (if header type is 1, 3, 4, 9, 13, 14 or header builder)', 'porto' ),
						'notice' => false,
					),
					array(
						'id'       => 'menu-custom-text-color',
						'type'     => 'color',
						'title'    => __( 'Text Color', 'porto' ),
						'default'  => '#777777',
						'validate' => 'color',
					),
					array(
						'id'      => 'menu-custom-link',
						'type'    => 'link_color',
						'title'   => __( 'Link Color', 'porto' ),
						'active'  => false,
						'default' => array(
							'regular' => '#0088cc',
							'hover'   => '#006fa4',
						),
					),
				),
			);
			$this->sections[] = array(
				'id'         => 'skin-breadcrumb',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Breadcrumbs', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'     => '1',
						'type'   => 'info',
						'desc'   => wp_kses(
							/* translators: %s: Breadcrumbs layout settings url */
							sprintf( __( 'Go to <a %s>Breadcrumbs Layout Settings</a>', 'porto' ), 'href="header-breadcrumb" class="goto-section"', 'porto' ),
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
						'id'    => 'breadcrumbs-bg',
						'type'  => 'background',
						'title' => __( 'Background', 'porto' ),
					),
					array(
						'id'      => 'breadcrumbs-bg-gradient',
						'type'    => 'switch',
						'title'   => __( 'Background Gradient', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'breadcrumbs-bg-gcolor',
						'type'     => 'color_gradient',
						'title'    => __( 'Background Gradient Color', 'porto' ),
						'required' => array( 'breadcrumbs-bg-gradient', 'equals', true ),
						'default'  => array(
							'from' => '',
							'to'   => '',
						),
					),
					array(
						'id'      => 'breadcrumbs-parallax',
						'type'    => 'switch',
						'title'   => __( 'Enable Background Image Parallax', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'breadcrumbs-parallax-speed',
						'type'     => 'text',
						'title'    => __( 'Parallax Speed', 'porto' ),
						'default'  => '1.5',
						'required' => array( 'breadcrumbs-parallax', 'equals', true ),
					),
					array(
						'id'      => 'breadcrumbs-top-border',
						'type'    => 'border',
						'all'     => true,
						'style'   => false,
						'title'   => __( 'Top Border', 'porto' ),
						'default' => array(
							'border-color' => '#384045',
							'border-top'   => '',
						),
					),
					array(
						'id'      => 'breadcrumbs-bottom-border',
						'type'    => 'border',
						'all'     => true,
						'style'   => false,
						'title'   => __( 'Bottom Border', 'porto' ),
						'default' => array(
							'border-color' => '#cccccc',
							'border-top'   => '5px',
						),
					),
					array(
						'id'          => 'breadcrumbs-padding',
						'type'        => 'spacing',
						'mode'        => 'padding',
						'title'       => __( 'Content Padding', 'porto' ),
						'description' => __( 'default: 15 15', 'porto' ),
						'left'        => false,
						'right'       => false,
						'default'     => array(
							'padding-top'    => 15,
							'padding-bottom' => 15,
						),
					),
					array(
						'id'       => 'breadcrumbs-text-color',
						'type'     => 'color',
						'title'    => __( 'Text Color', 'porto' ),
						'default'  => '#777777',
						'validate' => 'color',
					),
					array(
						'id'       => 'breadcrumbs-link-color',
						'type'     => 'color',
						'title'    => __( 'Link Color', 'porto' ),
						'default'  => '#0088cc',
						'validate' => 'color',
					),
					array(
						'id'       => 'breadcrumbs-title-color',
						'type'     => 'color',
						'title'    => __( 'Page Title Color', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
					),
					array(
						'id'       => 'breadcrumbs-subtitle-color',
						'type'     => 'color',
						'title'    => __( 'Page Sub Title Color', 'porto' ),
						'default'  => '#e6e6e6',
						'validate' => 'color',
					),
					array(
						'id'      => 'breadcrumbs-subtitle-margin',
						'type'    => 'spacing',
						'mode'    => 'margin',
						'title'   => __( 'Page Sub Title Margin', 'porto' ),
						'default' => array(
							'margin-top'    => 0,
							'margin-bottom' => 0,
							'margin-left'   => 0,
							'margin-right'  => 0,
						),
					),
				),
			);
			$this->sections[] = array(
				'id'         => 'skin-footer',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Footer', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'     => '1',
						'type'   => 'info',
						'desc'   => wp_kses(
							/* translators: %s: Footer layout settings url */
							sprintf( __( 'Go to <a %s>Footer Layout Settings</a>', 'porto' ), 'href="footer-settings" class="goto-section"', 'porto' ),
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
						'id'      => 'footer-bg',
						'type'    => 'background',
						'title'   => __( 'Background', 'porto' ),
						'default' => array(
							'background-color' => '#212529',
						),
					),
					array(
						'id'      => 'footer-bg-gradient',
						'type'    => 'switch',
						'title'   => __( 'Background Gradient', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'footer-bg-gcolor',
						'type'     => 'color_gradient',
						'title'    => __( 'Background Gradient Color', 'porto' ),
						'required' => array( 'footer-bg-gradient', 'equals', true ),
						'default'  => array(
							'from' => '',
							'to'   => '',
						),
					),
					array(
						'id'      => 'footer-parallax',
						'type'    => 'switch',
						'title'   => __( 'Enable Background Image Parallax', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'footer-parallax-speed',
						'type'     => 'text',
						'title'    => __( 'Parallax Speed', 'porto' ),
						'default'  => '1.5',
						'required' => array( 'footer-parallax', 'equals', true ),
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Footer Widgets Area', 'porto' ),
						'notice' => false,
					),
					array(
						'id'    => 'footer-main-bg',
						'type'  => 'background',
						'title' => __( 'Background', 'porto' ),
					),
					array(
						'id'      => 'footer-main-bg-gradient',
						'type'    => 'switch',
						'title'   => __( 'Background Gradient', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'footer-main-bg-gcolor',
						'type'     => 'color_gradient',
						'title'    => __( 'Background Gradient Color', 'porto' ),
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
						'default'  => '#ffffff',
						'validate' => 'color',
					),
					array(
						'id'       => 'footer-label-color',
						'type'     => 'color',
						'title'    => __( 'Label Color', 'porto' ),
						'validate' => 'color',
					),
					array(
						'id'       => 'footer-text-color',
						'type'     => 'color',
						'title'    => __( 'Text Color', 'porto' ),
						'default'  => '#777777',
						'validate' => 'color',
					),
					array(
						'id'      => 'footer-link-color',
						'type'    => 'link_color',
						'active'  => false,
						'title'   => __( 'Link Color', 'porto' ),
						'default' => array(
							'regular' => '#777777',
							'hover'   => '#ffffff',
						),
					),
					array(
						'id'       => 'footer-ribbon-bg-color',
						'type'     => 'color',
						'title'    => __( 'Ribbon Background Color', 'porto' ),
						'default'  => '#0088cc',
						'validate' => 'color',
					),
					array(
						'id'       => 'footer-ribbon-text-color',
						'type'     => 'color',
						'title'    => __( 'Ribbon Text Color', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Footer Top Widget Area', 'porto' ),
						'notice' => false,
					),
					array(
						'id'    => 'footer-top-bg',
						'type'  => 'background',
						'title' => __( 'Background', 'porto' ),
					),
					array(
						'id'      => 'footer-top-bg-gradient',
						'type'    => 'switch',
						'title'   => __( 'Enable Background Gradient', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'footer-top-bg-gcolor',
						'type'     => 'color_gradient',
						'title'    => __( 'Background Gradient Color', 'porto' ),
						'required' => array( 'footer-top-bg-gradient', 'equals', true ),
						'default'  => array(
							'from' => '',
							'to'   => '',
						),
					),
					array(
						'id'    => 'footer-top-padding',
						'type'  => 'spacing',
						'mode'  => 'padding',
						'title' => __( 'Padding', 'porto' ),
						'left'  => false,
						'right' => false,
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Footer Bottom', 'porto' ),
						'notice' => false,
					),
					array(
						'id'      => 'footer-bottom-bg',
						'type'    => 'background',
						'title'   => __( 'Background', 'porto' ),
						'default' => array(
							'background-color' => '#1c2023',
						),
					),
					array(
						'id'      => 'footer-bottom-bg-gradient',
						'type'    => 'switch',
						'title'   => __( 'Background Gradient', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'footer-bottom-bg-gcolor',
						'type'     => 'color_gradient',
						'title'    => __( 'Background Gradient Color', 'porto' ),
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
						'default'  => '#555555',
						'validate' => 'color',
					),
					array(
						'id'      => 'footer-bottom-link-color',
						'type'    => 'link_color',
						'active'  => false,
						'title'   => __( 'Link Color', 'porto' ),
						'default' => array(
							'regular' => '#777777',
							'hover'   => '#ffffff',
						),
					),
					array(
						'id'     => '1',
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
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Follow Us Widget', 'porto' ),
						'notice' => false,
					),
					array(
						'id'       => 'footer-social-bg-color',
						'type'     => 'color',
						'title'    => __( 'Background Color', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
					),
					array(
						'id'       => 'footer-social-link-color',
						'type'     => 'color',
						'title'    => __( 'Link Color', 'porto' ),
						'default'  => '#333333',
						'validate' => 'color',
					),
				),
			);

			$this->sections[] = array(
				'id'         => 'skin-mobile-menu',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Mobile Menu', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'     => '1',
						'type'   => 'info',
						'desc'   => wp_kses(
							/* translators: %s: Mobile panel settings url */
							sprintf( __( 'Go to <a %s>Mobile Panel Settings</a>', 'porto' ), 'href="mobile-panel-settings" class="goto-section"', 'porto' ),
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
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Mobile Menu Toggle', 'porto' ),
						'notice' => false,
					),
					array(
						'id'       => 'mobile-menu-toggle-text-color',
						'type'     => 'color',
						'title'    => __( 'Text Color', 'porto' ),
						'default'  => '#fff',
						'validate' => 'color',
					),
					array(
						'id'       => 'mobile-menu-toggle-bg-color',
						'type'     => 'color',
						'title'    => __( 'Background Color', 'porto' ),
						'validate' => 'color',
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Mobile Menu Panel', 'porto' ),
						'notice' => false,
					),
					array(
						'id'       => 'panel-bg-color',
						'type'     => 'color',
						'title'    => __( 'Background Color', 'porto' ),
						'validate' => 'color',
						'default'  => '#ffffff',
					),
					array(
						'id'       => 'panel-border-color',
						'type'     => 'color',
						'title'    => __( 'Border Color', 'porto' ),
						'default'  => '#e8e8e8',
						'validate' => 'color',
					),
					array(
						'id'       => 'panel-link-hbgcolor',
						'type'     => 'color',
						'title'    => __( 'Hover Background Color', 'porto' ),
						'default'  => '#f5f5f5',
						'validate' => 'color',
					),
					array(
						'id'       => 'panel-text-color',
						'type'     => 'color',
						'title'    => __( 'Text Color', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'      => 'panel-link-color',
						'type'    => 'link_color',
						'active'  => false,
						'title'   => __( 'Link Color', 'porto' ),
						'default' => array(
							'regular' => '',
							'hover'   => '',
						),
					),
				),
			);

			$this->sections[] = array(
				'id'         => 'skin-view-currency-switcher',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'View, Currency Switcher', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'     => '1',
						'type'   => 'info',
						'desc'   => wp_kses(
							/* translators: %s: View, Currency switcher layout settings url */
							sprintf( __( 'Go to <a %s>View, Currency Switcher Layout Settings</a>', 'porto' ), 'href="header-view-currency-switcher" class="goto-section"', 'porto' ),
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
						'id'       => 'switcher-bg-color',
						'type'     => 'color',
						'title'    => __( 'Background Color', 'porto' ),
						'default'  => 'transparent',
						'validate' => 'color',
					),
					array(
						'id'       => 'switcher-hbg-color',
						'type'     => 'color',
						'title'    => __( 'Hover Background Color', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
					),
					array(
						'id'      => 'switcher-top-level-hover',
						'type'    => 'switch',
						'title'   => __( 'Change top level on hover', 'porto' ),
						'desc'    => __( 'Checks to change top level text color and background color on hover.', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
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
						'desc'    => __( 'Regular is the color of top level link and hover is the color of sub menu item.', 'porto' ),
					),
				),
			);
			$this->sections[] = array(
				'id'         => 'skin-search-form',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Search Form', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'     => '1',
						'type'   => 'info',
						'desc'   => wp_kses(
							/* translators: %s: Search form layout settings url */
							sprintf( __( 'Go to <a %s>Search Form Layout Settings</a>', 'porto' ), 'href="header-search-form" class="goto-section"', 'porto' ),
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
						'id'       => 'searchform-bg-color',
						'type'     => 'color',
						'title'    => __( 'Background Color', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
					),
					array(
						'id'       => 'searchform-border-color',
						'type'     => 'color',
						'title'    => __( 'Border Color', 'porto' ),
						'default'  => '#eeeeee',
						'validate' => 'color',
					),
					array(
						'id'       => 'searchform-popup-border-color',
						'type'     => 'color',
						'title'    => __( 'Popup Border Color', 'porto' ),
						'default'  => '#cccccc',
						'validate' => 'color',
					),
					array(
						'id'       => 'searchform-text-color',
						'type'     => 'color',
						'title'    => __( 'Text Color', 'porto' ),
						'default'  => '#555555',
						'validate' => 'color',
					),
					array(
						'id'       => 'searchform-hover-color',
						'type'     => 'color',
						'title'    => __( 'Button Text Color', 'porto' ),
						'default'  => '#333333',
						'validate' => 'color',
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'In Sticky Header', 'porto' ),
						'notice' => false,
					),
					array(
						'id'       => 'sticky-searchform-popup-border-color',
						'type'     => 'color',
						'title'    => __( 'Popup Border Color', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'sticky-searchform-toggle-text-color',
						'type'     => 'color',
						'title'    => __( 'Toggle Text Color', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'sticky-searchform-toggle-hover-color',
						'type'     => 'color',
						'title'    => __( 'Toggle Hover Color', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
				),
			);
			$this->sections[] = array(
				'id'         => 'skin-mini-cart',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Mini Cart', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'     => '1',
						'type'   => 'info',
						'desc'   => wp_kses(
							/* translators: %s: Mini cart layout settings url */
							sprintf( __( 'Go to <a %s>Mini Cart Layout Settings</a>', 'porto' ), 'href="minicart-type" class="goto-section field-control"', 'porto' ),
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
						'id'      => 'minicart-icon-font-size',
						'type'    => 'text',
						'title'   => __( 'Icon Font Size', 'porto' ),
						'default' => '',
					),
					array(
						'id'       => 'minicart-icon-color',
						'type'     => 'color',
						'title'    => __( 'Icon Color', 'porto' ),
						'default'  => '#0088cc',
						'validate' => 'color',
					),
					array(
						'id'       => 'minicart-item-color',
						'type'     => 'color',
						'title'    => __( 'Item Color', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'minicart-bg-color',
						'type'     => 'color',
						'title'    => __( 'Background Color', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'minicart-popup-border-color',
						'type'     => 'color',
						'title'    => __( 'Popup Border Color', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'In Sticky Header', 'porto' ),
						'notice' => false,
					),
					array(
						'id'       => 'sticky-minicart-icon-color',
						'type'     => 'color',
						'title'    => __( 'Icon Color', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'sticky-minicart-item-color',
						'type'     => 'color',
						'title'    => __( 'Item Color', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'sticky-minicart-bg-color',
						'type'     => 'color',
						'title'    => __( 'Background Color', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'sticky-minicart-popup-border-color',
						'type'     => 'color',
						'title'    => __( 'Popup Border Color', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
				),
			);
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Shop', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'       => 'shop-add-links-color',
						'type'     => 'color',
						'title'    => 'Add Links Color',
						'subtitle' => __( 'Add to cart, Wishlist and Quick View Color on archive page', 'porto' ),
						'default'  => '#333333',
						'validate' => 'color',
					),
					array(
						'id'       => 'shop-add-links-bg-color',
						'type'     => 'color',
						'title'    => 'Add Links Background Color',
						'subtitle' => __( 'Add to cart, Wishlist and Quick View Background Color on archive page', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
					),
					array(
						'id'       => 'shop-add-links-border-color',
						'type'     => 'color',
						'title'    => 'Add Links Border Color',
						'subtitle' => __( 'Add to cart, Wishlist and Quick View Border Color on archive page', 'porto' ),
						'default'  => '#dddddd',
						'validate' => 'color',
					),
					array(
						'id'       => 'wishlist-color',
						'type'     => 'color',
						'title'    => __( 'Wishlist Color on product page', 'porto' ),
						'default'  => '#302e2a',
						'validate' => 'color',
					),
					array(
						'id'       => 'wishlist-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Wishlist Hover Color on product page', 'porto' ),
						'default'  => '',
						'validate' => 'color',
					),
					array(
						'id'       => 'hot-color',
						'type'     => 'color',
						'title'    => __( 'Hot Bg Color', 'porto' ),
						'default'  => '#62b959',
						'validate' => 'color',
					),
					array(
						'id'       => 'hot-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Hot Text Color', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
					),
					array(
						'id'       => 'sale-color',
						'type'     => 'color',
						'title'    => __( 'Sale Bg Color', 'porto' ),
						'default'  => '#e27c7c',
						'validate' => 'color',
					),
					array(
						'id'       => 'sale-color-inverse',
						'type'     => 'color',
						'title'    => __( 'Sale Text Color', 'porto' ),
						'default'  => '#ffffff',
						'validate' => 'color',
					),
					array(
						'id'          => 'add-to-cart-font',
						'type'        => 'typography',
						'title'       => __( 'Add to Cart Font', 'porto' ),
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
						'transport'   => 'refresh',
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
					),
					array(
						'id'       => 'js-code',
						'type'     => 'ace_editor',
						'title'    => __( 'JS Code before &lt;/body&gt;', 'porto' ),
						'subtitle' => __( 'Paste your custom JavaScript code here.', 'porto' ),
						'mode'     => 'javascript',
						'theme'    => 'chrome',
						'default'  => 'jQuery(document).ready(function(){});',
					),
				),
			);
			// Header Settings
			$this->sections[] = $this->add_customizer_field(
				array(
					'id'         => 'header-settings',
					'icon'       => 'Simple-Line-Icons-earphones',
					'icon_class' => '',
					'title'      => __( 'Header', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'     => '1',
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
							'id'      => 'show-header-top',
							'type'    => 'switch',
							'title'   => __( 'Show Header Top', 'porto' ),
							'default' => false,
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
							'id'      => 'minicart-type',
							'type'    => 'button_set',
							'title'   => __( 'Mini Cart Type', 'porto' ),
							'options' => array(
								'none'               => __( 'None', 'porto' ),
								'simple'             => __( 'Simple', 'porto' ),
								'minicart-arrow-alt' => __( 'Arrow Alt', 'porto' ),
								'minicart-inline'    => __( 'Text', 'porto' ),
							),
							'default' => $minicart_type,
						),
						array(
							'id'       => 'minicart-icon',
							'type'     => 'text',
							'title'    => __( 'Mini Cart Icon', 'porto' ),
							'required' => array( 'minicart-type', 'equals', array( 'simple', 'minicart-arrow-alt', 'minicart-inline' ) ),
						),
						array(
							'id'        => 'header-view',
							'type'      => 'button_set',
							'title'     => __( 'Header View', 'porto' ),
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
							'id'      => 'welcome-msg',
							'type'    => 'textarea',
							'title'   => __( 'Welcome Message', 'porto' ),
							'default' => '',
						),
						array(
							'id'      => 'header-contact-info',
							'type'    => 'textarea',
							'title'   => __( 'Contact Info', 'porto' ),
							'default' => "<ul class=\"nav nav-pills nav-top\">\r\n\t<li class=\"d-none d-sm-block\">\r\n\t\t<a href=\"#\" target=\"_blank\"><i class=\"fas fa-angle-right\"></i>About Us</a> \r\n\t</li>\r\n\t<li class=\"d-none d-sm-block\">\r\n\t\t<a href=\"#\" target=\"_blank\"><i class=\"fas fa-angle-right\"></i>Contact Us</a> \r\n\t</li>\r\n\t<li class=\"phone nav-item-left-border nav-item-right-border\">\r\n\t\t<span><i class=\"fas fa-phone\"></i>(123) 456-7890</span>\r\n\t</li>\r\n</ul>\r\n",
						),
						array(
							'id'      => 'header-copyright',
							'type'    => 'textarea',
							'title'   => __( 'Side Navigation Copyright (Header Type: Side)', 'porto' ),
							/* translators: %s: Current year */
							'default' => sprintf( __( '&copy; Copyright %s. All Rights Reserved.', 'porto' ), date( 'Y' ) ),
						),
						array(
							'id'        => 'header-side-position',
							'type'      => 'button_set',
							'title'     => __( 'Position (Header Type: Side)', 'porto' ),
							'options'   => array(
								''      => __( 'Left', 'porto' ),
								'right' => __( 'Right', 'porto' ),
							),
							'default'   => '',
							'transport' => 'refresh',
						),
						array(
							'id'      => 'show-header-tooltip',
							'type'    => 'switch',
							'title'   => __( 'Show Tooltip', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'header-tooltip',
							'type'     => 'textarea',
							'title'    => __( 'Tooltip Content', 'porto' ),
							'required' => array( 'show-header-tooltip', 'equals', true ),
						),
					),
				),
				$options_style
			);

			$header_builder_layouts = get_option( 'porto_header_builder_layouts', array() );
			foreach ( $header_builder_layouts as $key => $layout ) {
				$header_builder_layouts[ $key ] = $layout['name'];
			}
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Header Type', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'      => '1',
						'type'    => 'info',
						'default' => '',
						'desc'    => wp_kses(
							sprintf(
								/* translators: %s: Header builder url */
								__( 'You can add new header layout using <a href="%s" class="goto-header-builder">Header Builder</a>.', 'porto' ),
								esc_url(
									add_query_arg(
										array(
											'autofocus' => array(
												'section' => 'porto_header_layouts',
											),
											'url'       => home_url(),
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
						'subtitle' => __( 'Preset or Header Builder', 'porto' ),
						'options'  => array(
							''               => __( 'Header Type', 'porto' ),
							'header_builder' => __( 'Header builder', 'porto' ),
						),
						'default'  => '',
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
			$this->sections[] = array(
				'id'         => 'header-view-currency-switcher',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'View, Currency Switcher', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'     => '1',
						'type'   => 'info',
						'desc'   => wp_kses(
							/* translators: %s: View, Currency switcher skin settings url */
							sprintf( __( 'Go to <a %s>View, Currency Switcher Skin Settings</a>', 'porto' ), 'href="skin-view-currency-switcher" class="goto-section"', 'porto' ),
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
				),
			);
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Social Links', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'      => 'show-header-socials',
						'type'    => 'switch',
						'title'   => __( 'Show Social Links', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'header-socials-nofollow',
						'type'     => 'switch',
						'title'    => __( 'Add rel="nofollow" to social links', 'porto' ),
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
						'id'       => 'header-social-googleplus',
						'type'     => 'text',
						'title'    => __( 'Google Plus', 'porto' ),
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
				),
			);

			$this->sections[] = array(
				'id'         => 'header-search-form',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Search Form', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'     => '1',
						'type'   => 'info',
						'desc'   => wp_kses(
							/* translators: %s: Search form skin settings url */
							sprintf( __( 'Go to <a %s>Search Form Skin Settings</a>', 'porto' ), 'href="skin-search-form" class="goto-section"', 'porto' ),
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
			);
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Sticky Header', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'     => '1',
						'type'   => 'info',
						'desc'   => wp_kses(
							/* translators: %s: Sticky header skin settings url */
							sprintf( __( 'Go to <a %s>Sticky Header Skin Settings</a>', 'porto' ), 'href="sticky-header-bg" class="goto-section field-control"', 'porto' ),
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
						'title'   => __( 'Show Contact Info', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
				),
			);
			$this->sections[] = array(
				'id'         => 'mobile-panel-settings',
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Mobile Panel', 'porto' ),
				'fields'     => array(
					array(
						'id'     => '1',
						'type'   => 'info',
						'desc'   => wp_kses(
							/* translators: %s: Mobile menu skin settings url */
							sprintf( __( 'Go to <a %s>Mobile Menu Skin Settings</a>', 'porto' ), 'href="skin-mobile-menu" class="goto-section"', 'porto' ),
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
						'id'      => 'mobile-panel-type',
						'type'    => 'button_set',
						'title'   => __( 'Mobile Panel Type', 'porto' ),
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
						'title'     => __( 'Add View, Currency Switcher', 'porto' ),
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
						'required'  => array( 'mobile-panel-type', 'equals', array( 'side' ) ),
						'default'   => false,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
				),
			);
			// Menu Settings
			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'       => 'Simple-Line-Icons-menu',
					'icon_class' => '',
					'title'      => __( 'Menu', 'porto' ),
					'transport'  => 'postMessage',
					'fields'     => array(
						array(
							'id'     => '1',
							'type'   => 'info',
							'desc'   => wp_kses(
								/* translators: %s: Main menu skin settings url */
								sprintf( __( 'Go to <a %s> Main Menu Skin Settings</a>', 'porto' ), 'href="skin-main-menu" class="goto-section"', 'porto' ),
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
							'id'     => '1',
							'type'   => 'info',
							'title'  => __( 'When using Side header type or showing Main Menu in Sidebar', 'porto' ),
							'notice' => false,
						),
						array(
							'id'        => 'side-menu-type',
							'type'      => 'button_set',
							'title'     => __( 'Sidebar Menu Type', 'porto' ),
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
							'id'     => '1',
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
							'id'      => 'menu-sidebar-title',
							'type'    => 'text',
							'title'   => __( 'Sidebar Menu Title', 'porto' ),
							'default' => __( 'All Department', 'porto' ),
						),
						array(
							'id'       => 'menu-sidebar-toggle',
							'type'     => 'switch',
							'title'    => __( 'Toggle Sidebar Menu', 'porto' ),
							'required' => array( 'menu-sidebar', 'equals', true ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'        => 'menu-sidebar-home',
							'type'      => 'switch',
							'title'     => __( 'Show Main Menu in Sidebar only on Home', 'porto' ),
							'required'  => array( 'menu-sidebar', 'equals', true ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'     => '1',
							'type'   => 'info',
							'title'  => __( 'If header type is 9 or header builder', 'porto' ),
							'notice' => false,
						),
						array(
							'id'      => 'menu-title',
							'type'    => 'text',
							'title'   => __( 'Main Menu Title', 'porto' ),
							'default' => __( 'All Department', 'porto' ),
						),
						array(
							'id'        => 'menu-toggle-onhome',
							'type'      => 'switch',
							'title'     => __( 'Toggle on home page', 'porto' ),
							'default'   => false,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'     => '1',
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
							'id'     => '1',
							'type'   => 'info',
							'desc'   => wp_kses(
								/* translators: %s: Breadcrumbs skin settings url */
								sprintf( __( 'Go to <a %s>Breadcrumbs Skin Settings</a>', 'porto' ), 'href="skin-breadcrumb" class="goto-section"', 'porto' ),
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
							'id'         => 'breadcrumbs-type',
							'type'       => 'image_select',
							'full_width' => true,
							'title'      => __( 'Breadcrumbs Type', 'porto' ),
							'options'    => $porto_breadcrumbs_type,
							'default'    => '1',
						),
						array(
							'id'      => 'show-pagetitle',
							'type'    => 'switch',
							'title'   => __( 'Show Page Title', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'          => 'pagetitle-archives',
							'type'        => 'switch',
							'title'       => __( 'Show Content Type Name in Singular', 'porto' ),
							'default'     => false,
							'required'    => array( 'show-pagetitle', 'equals', '1' ),
							'description' => __( 'Show Content Type Name in single content type.', 'porto' ),
							'on'          => __( 'Yes', 'porto' ),
							'off'         => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'pagetitle-parent',
							'type'     => 'switch',
							'title'    => __( 'Show Parent Page Title in Page', 'porto' ),
							'default'  => false,
							'required' => array( 'show-pagetitle', 'equals', '1' ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'show-breadcrumbs',
							'type'    => 'switch',
							'title'   => __( 'Show Breadcrumbs', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'breadcrumbs-prefix',
							'type'     => 'text',
							'title'    => __( 'Breadcrumbs Prefix', 'porto' ),
							'required' => array( 'show-breadcrumbs', 'equals', '1' ),
						),
						array(
							'id'       => 'breadcrumbs-blog-link',
							'type'     => 'switch',
							'title'    => __( 'Show Blog Link', 'porto' ),
							'default'  => true,
							'required' => array( 'show-breadcrumbs', 'equals', '1' ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'breadcrumbs-shop-link',
							'type'     => 'switch',
							'title'    => __( 'Show Shop Link', 'porto' ),
							'default'  => true,
							'required' => array( 'show-breadcrumbs', 'equals', '1' ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'breadcrumbs-archives-link',
							'type'     => 'switch',
							'title'    => __( 'Show Custom Post Type Archives Link', 'porto' ),
							'default'  => true,
							'required' => array( 'show-breadcrumbs', 'equals', '1' ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'breadcrumbs-categories',
							'type'     => 'switch',
							'title'    => __( 'Show Categories Link', 'porto' ),
							'default'  => true,
							'required' => array( 'show-breadcrumbs', 'equals', '1' ),
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'breadcrumbs-delimiter',
							'type'     => 'button_set',
							'title'    => __( 'Breadcrumbs Delimiter', 'porto' ),
							'required' => array( 'show-breadcrumbs', 'equals', '1' ),
							'options'  => array(
								''            => __( '/', 'porto' ),
								'delimiter-2' => __( '>', 'porto' ),
							),
							'default'  => '',
						),
						array(
							'id'      => 'breadcrumbs-css-class',
							'type'    => 'text',
							'title'   => __( 'Custom CSS Class', 'porto' ),
							'default' => '',
						),
					),
				),
				$options_style
			);
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
							'id'     => '1',
							'type'   => 'info',
							'desc'   => wp_kses(
								/* translators: %s: Footer skin url */
								sprintf( __( 'Go to <a %s>Footer Skin Settings</a>', 'porto' ), 'href="skin-footer" class="goto-section"', 'porto' ),
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
							'id'         => 'footer-type',
							'type'       => 'image_select',
							'full_width' => true,
							'title'      => __( 'Footer Type', 'porto' ),
							'options'    => $porto_footer_type,
							'default'    => '1',
						),
						array(
							'id'      => 'footer-customize',
							'type'    => 'switch',
							'title'   => __( 'Customize Footer Columns', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-widget1',
							'type'     => 'select',
							'title'    => __( 'Widget 1', 'porto' ),
							'required' => array( 'footer-customize', 'equals', true ),
							'options'  => $porto_footer_columns,
							'default'  => '',
						),
						array(
							'id'       => 'footer-widget2',
							'type'     => 'select',
							'title'    => __( 'Widget 2', 'porto' ),
							'required' => array( 'footer-customize', 'equals', true ),
							'options'  => $porto_footer_columns,
							'default'  => '',
						),
						array(
							'id'       => 'footer-widget3',
							'type'     => 'select',
							'title'    => __( 'Widget 3', 'porto' ),
							'required' => array( 'footer-customize', 'equals', true ),
							'options'  => $porto_footer_columns,
							'default'  => '',
						),
						array(
							'id'       => 'footer-widget4',
							'type'     => 'select',
							'title'    => __( 'Widget 4', 'porto' ),
							'required' => array( 'footer-customize', 'equals', true ),
							'options'  => $porto_footer_columns,
							'default'  => '',
						),
						array(
							'id'      => 'footer-reveal',
							'type'    => 'switch',
							'title'   => __( 'Show Reveal Effect', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-logo',
							'type'     => 'media',
							'url'      => true,
							'readonly' => false,
							'title'    => __( 'Footer Logo', 'porto' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/logo/logo_footer.png',
							),
						),
						array(
							'id'      => 'footer-ribbon',
							'type'    => 'text',
							'title'   => __( 'Ribbon Text', 'porto' ),
							'default' => '',
						),
						array(
							'id'      => 'footer-copyright',
							'type'    => 'textarea',
							'title'   => __( 'Copyright', 'porto' ),
							/* translators: %s: Current Year */
							'default' => sprintf( __( '&copy; Copyright %s. All Rights Reserved.', 'porto' ), date( 'Y' ) ),
						),
						array(
							'id'      => 'footer-copyright-pos',
							'type'    => 'button_set',
							'title'   => __( 'Copyright Position', 'porto' ),
							'options' => array(
								'left'   => __( 'Left', 'porto' ),
								'center' => __( 'Center', 'porto' ),
								'right'  => __( 'Right', 'porto' ),
							),
							'default' => 'left',
						),
						array(
							'id'      => 'footer-payments',
							'type'    => 'switch',
							'title'   => __( 'Show Payments Logos', 'porto' ),
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
							'required' => array( 'footer-payments', 'equals', '1' ),
							'default'  => array(
								'url' => PORTO_URI . '/images/payments.png',
							),
						),
						array(
							'id'       => 'footer-payments-image-alt',
							'type'     => 'text',
							'title'    => __( 'Payments Image Alt', 'porto' ),
							'required' => array( 'footer-payments', 'equals', '1' ),
							'default'  => '',
						),
						array(
							'id'       => 'footer-payments-link',
							'type'     => 'text',
							'title'    => __( 'Payments Link URL', 'porto' ),
							'required' => array( 'footer-payments', 'equals', '1' ),
							'default'  => '',
						),
						array(
							'id'      => 'show-footer-tooltip',
							'type'    => 'switch',
							'title'   => __( 'Show Tooltip', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'footer-tooltip',
							'type'     => 'textarea',
							'title'    => __( 'Tooltip Content', 'porto' ),
							'required' => array( 'show-footer-tooltip', 'equals', true ),
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
							'id'      => 'page-comment',
							'type'    => 'switch',
							'title'   => __( 'Show Comments', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'page-zoom',
							'type'    => 'switch',
							'title'   => __( 'Image Lightbox', 'porto' ),
							'default' => true,
							'on'      => __( 'Enable', 'porto' ),
							'off'     => __( 'Disable', 'porto' ),
						),
						array(
							'id'      => 'page-share',
							'type'    => 'switch',
							'title'   => __( 'Show Social Share Links', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
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
							'default'  => '',
							'required' => array( 'page-share', 'equals', true ),
						),
						array(
							'id'      => 'page-microdata',
							'type'    => 'switch',
							'title'   => __( 'Microdata Rich Snippets', 'porto' ),
							'default' => true,
							'on'      => __( 'Enable', 'porto' ),
							'off'     => __( 'Disable', 'porto' ),
						),
					),
				),
				$options_style
			);
			// Blog
			$this->sections[] = $this->add_customizer_field(
				array(
					'icon'       => 'Simple-Line-Icons-docs',
					'icon_class' => '',
					'title'      => __( 'Post', 'porto' ),
					'fields'     => array(
						array(
							'id'      => 'post-format',
							'type'    => 'switch',
							'title'   => __( 'Show Post Format', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'hot-label',
							'type'    => 'text',
							'title'   => __( '"HOT" Text', 'porto' ),
							'desc'    => __( 'sticky post label', 'porto' ),
							'default' => '',
						),
						array(
							'id'      => 'post-zoom',
							'type'    => 'switch',
							'title'   => __( 'Image Lightbox', 'porto' ),
							'default' => true,
							'on'      => __( 'Enable', 'porto' ),
							'off'     => __( 'Disable', 'porto' ),
						),
						array(
							'id'      => 'post-metas',
							'type'    => 'button_set',
							'title'   => __( 'Post Meta', 'porto' ),
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
							'options' => array(
								''       => __( 'After content', 'porto' ),
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
								'alt' => __( 'Full', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_archive_style_1.jpg',
							),
							'large'       => array(
								'alt' => __( 'Large', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_archive_style_2.jpg',
							),
							'large-alt'   => array(
								'alt' => __( 'Large Alt', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_archive_style_3.jpg',
							),
							'medium'      => array(
								'alt' => __( 'Medium', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_archive_style_4.jpg',
							),
							'grid'        => array(
								'alt' => __( 'Grid', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_archive_style_grid.jpg',
							),
							'masonry'     => array(
								'alt' => __( 'Masonry', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_archive_style_5.jpg',
							),
							'timeline'    => array(
								'alt' => __( 'Timeline', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_archive_style_6.jpg',
							),
							'medium-alt'  => array(
								'alt' => __( 'Medium Alternate', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_archive_style_7.jpg',
							),
							'woocommerce' => array(
								'alt' => __( 'Woocommerce', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_archive_style_woocommerce.jpg',
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
						'id'      => 'blog-infinite',
						'type'    => 'switch',
						'title'   => __( 'Enable Infinite Scroll', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
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
					array(
						'id'          => 'blog-date-format',
						'type'        => 'text',
						'title'       => __( 'Date Format', 'porto' ),
						'description' => __( 'j = 1-31, F = January-December, M = Jan-Dec, m = 01-12, n = 1-12', 'porto' ) . '<br />' .
						__( 'For more, please visit ', 'porto' ) . '<a href="https://codex.wordpress.org/Formatting_Date_and_Time">https://codex.wordpress.org/Formatting_Date_and_Time</a>',
						'default'     => '',
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
						'desc'     => __( 'Please input block slug name. You can create a block in <strong>Blocks/Add New</strong>.', 'porto' ),
					),
					array(
						'id'        => 'blog-content_top',
						'type'      => 'text',
						'title'     => __( 'Content Top', 'porto' ),
						'desc'      => __( 'Please input comma separated block slug names. You can create a block in <strong>Blocks/Add New</strong>.', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'blog-content_inner_top',
						'type'      => 'text',
						'title'     => __( 'Content Inner Top', 'porto' ),
						'desc'      => __( 'Please input comma separated block slug names. You can create a block in <strong>Blocks/Add New</strong>.', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'blog-content_inner_bottom',
						'type'      => 'text',
						'title'     => __( 'Content Inner Bottom', 'porto' ),
						'desc'      => __( 'Please input comma separated block slug names. You can create a block in <strong>Blocks/Add New</strong>.', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'blog-content_bottom',
						'type'      => 'text',
						'title'     => __( 'Content Bottom', 'porto' ),
						'desc'      => __( 'Please input comma separated block slug names. You can create a block in <strong>Blocks/Add New</strong>.', 'porto' ),
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
						'desc'  => __( 'Please input comma separated block slug names. You can create a block in <strong>Blocks/Add New</strong>.', 'porto' ),
					),
					array(
						'id'      => 'post-content-layout',
						'type'    => 'image_select',
						'title'   => __( 'Post Layout', 'porto' ),
						'options' => array(
							'full'        => array(
								'alt' => __( 'Full', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_single_style_1.jpg',
							),
							'large'       => array(
								'alt' => __( 'Large', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_single_style_2.jpg',
							),
							'large-alt'   => array(
								'alt' => __( 'Large Alt', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_single_style_3.jpg',
							),
							'medium'      => array(
								'alt' => __( 'Medium', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_single_style_4.jpg',
							),
							'full-alt'    => array(
								'alt' => __( 'Full Alt', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_single_style_5.jpg',
							),
							'woocommerce' => array(
								'alt' => __( 'Woocommerce', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_archive_style_woocommerce.jpg',
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
						'id'          => 'post-title-style',
						'type'        => 'button_set',
						'title'       => __( 'Post Section Title Style', 'porto' ),
						'description' => __( 'Select title style of author, comment, etc.', 'porto' ),
						'options'     => array(
							''             => __( 'With Icon', 'porto' ),
							'without-icon' => __( 'Without Icon', 'porto' ),
						),
						'default'     => 'without-icon',
					),
					array(
						'id'        => 'post-slideshow',
						'type'      => 'switch',
						'title'     => __( 'Show Slideshow', 'porto' ),
						'default'   => true,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'post-title',
						'type'      => 'switch',
						'title'     => __( 'Show Title', 'porto' ),
						'default'   => true,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'post-share',
						'type'      => 'switch',
						'title'     => __( 'Show Social Share Links', 'porto' ),
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
						'default'   => true,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'post-comments',
						'type'      => 'switch',
						'title'     => __( 'Show Comments', 'porto' ),
						'default'   => true,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
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
						'id'          => 'post-related-cols',
						'type'        => 'button_set',
						'required'    => array( 'post-related', 'equals', true ),
						'title'       => __( 'Related Posts Columns', 'porto' ),
						'description' => __( 'reduce one column in left or right sidebar layout', 'porto' ),
						'options'     => array(
							'4' => '4',
							'3' => '3',
							'2' => '2',
							'1' => '1',
						),
						'default'     => '4',
					),
					array(
						'id'      => 'post-backto-blog',
						'type'    => 'switch',
						'title'   => __( 'Show Back to Blog Link', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
				),
			);
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Post Carousel', 'porto' ),
				'fields'     => array(
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
								'alt' => __( 'With Read More Link', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_style_1.jpg',
							),
							'style-2' => array(
								'alt' => __( 'With Post Meta', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_style_2.jpg',
							),
							'style-3' => array(
								'alt' => __( 'With Read More Button Link', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_style_3.jpg',
							),
							'style-4' => array(
								'alt' => __( 'With Side Image', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_style_4.jpg',
							),
							'style-5' => array(
								'alt' => __( 'With Categories', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_style_5.jpg',
							),
							'style-6' => array(
								'alt' => __( 'Simple', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/post_style_6.jpg',
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
						'id'      => 'post-related-thumb-bg',
						'type'    => 'button_set',
						'title'   => __( 'Image Overlay Background', 'porto' ),
						'options' => array(
							''                => __( 'Darken', 'porto' ),
							'lighten'         => __( 'Lighten', 'porto' ),
							'hide-wrapper-bg' => __( 'Transparent', 'porto' ),
						),
						'default' => 'hide-wrapper-bg',
					),
					array(
						'id'      => 'post-related-thumb-image',
						'type'    => 'button_set',
						'title'   => __( 'Hover Image Effect', 'porto' ),
						'options' => array(
							''        => __( 'Zoom', 'porto' ),
							'no-zoom' => __( 'No Zoom', 'porto' ),
						),
						'default' => '',
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
						'required' => array( 'post-related-style', 'equals', array( '', 'style-3' ) ),
						'default'  => false,
						'on'       => __( 'Show', 'porto' ),
						'off'      => __( 'Hide', 'porto' ),
					),
					array(
						'id'       => 'post-related-btn-style',
						'type'     => 'button_set',
						'title'    => __( 'Button Style', 'porto' ),
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
						'id'      => 'enable-portfolio',
						'type'    => 'switch',
						'title'   => __( 'Portfolio Content Type', 'porto' ),
						'default' => true,
						'on'      => __( 'Enable', 'porto' ),
						'off'     => __( 'Disable', 'porto' ),
					),
					array(
						'id'          => 'portfolio-slug-name',
						'type'        => 'text',
						'title'       => __( 'Slug Name', 'porto' ),
						'placeholder' => 'portfolio',
					),
					array(
						'id'          => 'portfolio-name',
						'type'        => 'text',
						'title'       => __( 'Name', 'porto' ),
						'placeholder' => __( 'Portfolios', 'porto' ),
					),
					array(
						'id'          => 'portfolio-singular-name',
						'type'        => 'text',
						'title'       => __( 'Singular Name', 'porto' ),
						'placeholder' => __( 'Portfolio', 'porto' ),
					),
					array(
						'id'          => 'portfolio-cat-slug-name',
						'type'        => 'text',
						'title'       => __( 'Category Slug Name', 'porto' ),
						'placeholder' => 'portfolio_cat',
					),
					array(
						'id'          => 'portfolio-skill-slug-name',
						'type'        => 'text',
						'title'       => __( 'Skill Slug Name', 'porto' ),
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
							'id'    => 'portfolio-archive-page',
							'type'  => 'select',
							'data'  => 'page',
							'title' => __( 'Portfolios Page', 'porto' ),
						),
						array(
							'id'      => 'portfolio-zoom',
							'type'    => 'switch',
							'title'   => __( 'Image Lightbox', 'porto' ),
							'default' => true,
							'on'      => __( 'Enable', 'porto' ),
							'off'     => __( 'Disable', 'porto' ),
						),
						array(
							'id'      => 'portfolio-metas',
							'type'    => 'button_set',
							'title'   => __( 'Portfolio Meta', 'porto' ),
							'multi'   => true,
							'options' => array(
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
							'default' => array( 'like', 'date', 'cats', 'skills', 'location', 'client', 'quote', '-', 'link' ),
						),
						array(
							'id'          => 'portfolio-subtitle',
							'type'        => 'button_set',
							'title'       => __( 'Portfolio Sub Title', 'porto' ),
							'description' => __( 'Use this value in portfolio archives (grid, masonry, timeline layouts) and portfolio carousel.', 'porto' ),
							'options'     => array(
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
							'default'     => 'cats',
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
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'portfolio-archive-ajax-modal',
						'type'     => 'switch',
						'title'    => __( 'Ajax Load on Modal', 'porto' ),
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
						'id'      => 'portfolio-infinite',
						'type'    => 'switch',
						'title'   => __( 'Enable Infinite Scroll', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'portfolio-cat-orderby',
						'type'    => 'button_set',
						'title'   => __( 'Sort Categories Order By', 'porto' ),
						'options' => $porto_categories_orderby,
						'default' => 'name',
					),
					array(
						'id'      => 'portfolio-cat-order',
						'type'    => 'button_set',
						'title'   => __( 'Sort Order for Categories', 'porto' ),
						'options' => $porto_categories_order,
						'default' => 'asc',
					),
					array(
						'id'      => 'portfolio-cat-sort-pos',
						'type'    => 'button_set',
						'title'   => __( 'Filter Position', 'porto' ),
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
						'id'      => 'portfolio-archive-image-counter',
						'type'    => 'switch',
						'title'   => __( 'Image Counter', 'porto' ),
						'default' => false,
						'on'      => __( 'Show', 'porto' ),
						'off'     => __( 'Hide', 'porto' ),
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
						'required' => array( 'portfolio-layout', 'equals', array( 'grid', 'masonry', 'timeline' ) ),
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
						'id'      => 'portfolio-archive-thumb-style',
						'type'    => 'button_set',
						'title'   => __( 'Info View Type Style', 'porto' ),
						'options' => array(
							''                    => __( 'None', 'porto' ),
							'alternate-info'      => __( 'Alternate', 'porto' ),
							'alternate-with-plus' => __( 'Alternate with Plus', 'porto' ),
						),
						'default' => '',
					),
					array(
						'id'      => 'portfolio-archive-thumb-bg',
						'type'    => 'button_set',
						'title'   => __( 'Image Overlay Background', 'porto' ),
						'options' => array(
							''                => __( 'Darken', 'porto' ),
							'lighten'         => __( 'Lighten', 'porto' ),
							'hide-wrapper-bg' => __( 'Transparent', 'porto' ),
						),
						'default' => 'lighten',
					),
					array(
						'id'      => 'portfolio-archive-thumb-image',
						'type'    => 'button_set',
						'title'   => __( 'Hover Image Effect', 'porto' ),
						'options' => array(
							''          => __( 'Zoom', 'porto' ),
							'slow-zoom' => __( 'Slow Zoom', 'porto' ),
							'no-zoom'   => __( 'No Zoom', 'porto' ),
						),
						'default' => '',
					),
					array(
						'id'          => 'portfolio-archive-readmore',
						'type'        => 'switch',
						'title'       => __( 'Show "Read More" Link', 'porto' ),
						'description' => __( 'Show "Read More" link in "Out of Image" view type.', 'porto' ),
						'default'     => false,
						'on'          => __( 'Yes', 'porto' ),
						'off'         => __( 'No', 'porto' ),
					),
					array(
						'id'          => 'portfolio-archive-readmore-label',
						'type'        => 'text',
						'title'       => __( '"Read More" Label', 'porto' ),
						'required'    => array( 'portfolio-archive-readmore', 'equals', true ),
						'placeholder' => __( 'View Project...', 'porto' ),
					),
					array(
						'id'      => 'portfolio-archive-link-zoom',
						'type'    => 'switch',
						'title'   => __( 'Enable Image Lightbox instead of Portfolio Link', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
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
						'required' => array( 'portfolio-archive-link-zoom', 'equals', false ),
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
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
						'id'       => 'portfolio-archive-zoom',
						'type'     => 'switch',
						'title'    => __( 'Show Image Lightbox Icon', 'porto' ),
						'required' => array( 'portfolio-archive-link-zoom', 'equals', false ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'portfolio-external-link',
						'type'     => 'switch',
						'title'    => __( 'Show External Link instead of Portfolio Link', 'porto' ),
						'required' => array( 'portfolio-archive-link-zoom', 'equals', false ),
						'default'  => false,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
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
						'id'      => 'portfolio-show-testimonial',
						'type'    => 'switch',
						'title'   => __( 'Show Author Testimonial', 'porto' ),
						'desc'    => __( 'If yes, it will show the testimonial after meta section if it exists.', 'porto' ),
						'default' => false,
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
				),
			);
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Single Portfolio', 'porto' ),
				'fields'     => array(
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
						'desc'  => __( 'Please input comma separated block slug names. You can create a block in <strong>Blocks/Add New</strong>.', 'porto' ),
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
						'id'      => 'portfolio-image-count',
						'type'    => 'switch',
						'title'   => __( 'Show Image Count', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
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
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'portfolio-author',
						'type'    => 'switch',
						'title'   => __( 'Show Author Info', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'portfolio-comments',
						'type'    => 'switch',
						'title'   => __( 'Show Comments', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
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
						'id'          => 'portfolio-related-cols',
						'type'        => 'button_set',
						'required'    => array( 'portfolio-related', 'equals', true ),
						'title'       => __( 'Related Portfolios Columns', 'porto' ),
						'description' => __( 'reduce one column in left or right sidebar layout', 'porto' ),
						'options'     => array(
							'4' => '4',
							'3' => '3',
							'2' => '2',
						),
						'default'     => '4',
					),
				),
			);
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Portfolio Carousel', 'porto' ),
				'fields'     => array(
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
						'id'      => 'portfolio-related-thumb',
						'type'    => 'image_select',
						'title'   => __( 'Info View Type', 'porto' ),
						'options' => array(
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
						'default' => '',
					),
					array(
						'id'      => 'portfolio-related-thumb-bg',
						'type'    => 'button_set',
						'title'   => __( 'Image Overlay Background', 'porto' ),
						'options' => array(
							''                => __( 'Darken', 'porto' ),
							'lighten'         => __( 'Lighten', 'porto' ),
							'hide-wrapper-bg' => __( 'Transparent', 'porto' ),
						),
						'default' => 'lighten',
					),
					array(
						'id'      => 'portfolio-related-thumb-image',
						'type'    => 'button_set',
						'title'   => __( 'Hover Image Effect', 'porto' ),
						'options' => array(
							''        => __( 'Zoom', 'porto' ),
							'no-zoom' => __( 'No Zoom', 'porto' ),
						),
						'default' => '',
					),
					array(
						'id'      => 'portfolio-related-link',
						'type'    => 'switch',
						'title'   => __( 'Show Link Icon', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'portfolio-related-show-content',
						'type'    => 'switch',
						'title'   => __( 'Show Excerpt Content', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
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
						'id'      => 'enable-event',
						'type'    => 'switch',
						'title'   => __( 'Event Content Type', 'porto' ),
						'default' => false,
						'on'      => __( 'Enable', 'porto' ),
						'off'     => __( 'Disable', 'porto' ),
					),
					array(
						'id'          => 'event-slug-name',
						'type'        => 'text',
						'title'       => __( 'Slug Name', 'porto' ),
						'placeholder' => 'event',
					),
					array(
						'id'          => 'event-name',
						'type'        => 'text',
						'title'       => __( 'Name', 'porto' ),
						'placeholder' => __( 'Events', 'porto' ),
					),
					array(
						'id'          => 'event-singular-name',
						'type'        => 'text',
						'title'       => __( 'Singular Name', 'porto' ),
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
							'id'    => 'event-archive-page',
							'type'  => 'select',
							'data'  => 'page',
							'title' => __( 'Events Page', 'porto' ),
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
							'id'    => 'event-banner-block',
							'type'  => 'text',
							'title' => __( 'Global Banner Block', 'porto' ),
							'desc'  => __( 'Please input comma separated block slug names. You can create a block in <strong>Blocks/Add New</strong>.', 'porto' ),
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
						'id'      => 'enable-member',
						'type'    => 'switch',
						'title'   => __( 'Member Content Type', 'porto' ),
						'default' => true,
						'on'      => __( 'Enable', 'porto' ),
						'off'     => __( 'Disable', 'porto' ),
					),
					array(
						'id'          => 'member-slug-name',
						'type'        => 'text',
						'title'       => __( 'Slug Name', 'porto' ),
						'placeholder' => 'member',
					),
					array(
						'id'          => 'member-name',
						'type'        => 'text',
						'title'       => __( 'Name', 'porto' ),
						'placeholder' => __( 'Members', 'porto' ),
					),
					array(
						'id'          => 'member-singular-name',
						'type'        => 'text',
						'title'       => __( 'Singular Name', 'porto' ),
						'placeholder' => __( 'Member', 'porto' ),
					),
					array(
						'id'          => 'member-cat-slug-name',
						'type'        => 'text',
						'title'       => __( 'Category Slug Name', 'porto' ),
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
							'id'    => 'member-archive-page',
							'type'  => 'select',
							'data'  => 'page',
							'title' => __( 'Members Page', 'porto' ),
						),
						array(
							'id'      => 'member-zoom',
							'type'    => 'switch',
							'title'   => __( 'Image Lightbox', 'porto' ),
							'default' => true,
							'on'      => __( 'Enable', 'porto' ),
							'off'     => __( 'Disable', 'porto' ),
						),
						array(
							'id'      => 'member-social-target',
							'type'    => 'switch',
							'title'   => __( 'Show Social Link in Blank Target', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
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
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),

						array(
							'id'       => 'member-archive-ajax-modal',
							'type'     => 'switch',
							'title'    => __( 'Ajax Load on Modal', 'porto' ),
							'required' => array( 'member-archive-ajax', 'equals', true ),
							'default'  => false,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'member-infinite',
							'type'    => 'switch',
							'title'   => __( 'Enable Infinite Scroll', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'member-cat-orderby',
							'type'    => 'button_set',
							'title'   => __( 'Sort Categories Order By', 'porto' ),
							'options' => $porto_categories_orderby,
							'default' => 'name',
						),
						array(
							'id'      => 'member-cat-order',
							'type'    => 'button_set',
							'title'   => __( 'Sort Order for Categories', 'porto' ),
							'options' => $porto_categories_order,
							'default' => 'asc',
						),
						array(
							'id'      => 'member-cat-sort-pos',
							'type'    => 'button_set',
							'title'   => __( 'Filter Position', 'porto' ),
							'options' => $porto_categories_sort_pos,
							'default' => 'content',
						),
						array(
							'id'      => 'member-columns',
							'type'    => 'button_set',
							'title'   => __( 'Member Columns', 'porto' ),
							'options' => array(
								'2' => __( '2 Columns', 'porto' ),
								'3' => __( '3 Columns', 'porto' ),
								'4' => __( '4 Columns', 'porto' ),
								'5' => __( '5 Columns', 'porto' ),
								'6' => __( '6 Columns', 'porto' ),
							),
							'default' => '4',
						),
						array(
							'id'      => 'member-view-type',
							'type'    => 'image_select',
							'title'   => __( 'View Type', 'porto' ),
							'default' => '',
							'options' => array(
								''  => array(
									'alt' => __( 'Type 1', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/member_archive_view_1.jpg',
								),
								'2' => array(
									'alt' => __( 'Type 2', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/member_archive_view_2.jpg',
								),
								'3' => array(
									'alt' => __( 'Type 3', 'porto' ),
									'img' => PORTO_OPTIONS_URI . '/images/member_archive_view_3.jpg',
								),
							),
						),

						array(
							'id'      => 'custom-member-zoom',
							'type'    => 'button_set',
							'title'   => __( 'Hover Image Effect', 'porto' ),
							'options' => array(
								''        => __( 'Zoom', 'porto' ),
								'no_zoom' => __( 'No_Zoom', 'porto' ),
							),
							'default' => '',
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
							'id'          => 'member-archive-readmore',
							'type'        => 'switch',
							'title'       => __( 'Show "Read More" Link', 'porto' ),
							'description' => __( 'Show "Read More" link in "Type 2" view type.', 'porto' ),
							'default'     => false,
							'on'          => __( 'Yes', 'porto' ),
							'off'         => __( 'No', 'porto' ),
						),
						array(
							'id'          => 'member-archive-readmore-label',
							'type'        => 'text',
							'title'       => __( '"Read More" Label', 'porto' ),
							'required'    => array( 'member-archive-readmore', 'equals', true ),
							'placeholder' => __( 'View More...', 'porto' ),
						),
						array(
							'id'      => 'member-external-link',
							'type'    => 'switch',
							'title'   => __( 'Show External Link instead of Member Link', 'porto' ),
							'default' => false,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'member-overview',
							'type'    => 'switch',
							'title'   => __( 'Show Overview', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'member-excerpt',
							'type'     => 'switch',
							'title'    => __( 'Show Overview Excerpt', 'porto' ),
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
							'id'      => 'member-socials',
							'type'    => 'switch',
							'title'   => __( 'Show Social Links', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),

						array(
							'id'       => 'member-social-link-style',
							'type'     => 'button_set',
							'required' => array( 'member-socials', 'equals', true ),
							'title'    => __( 'Social Links Style', 'porto' ),
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
							'id'      => 'member-single-layout',
							'type'    => 'image_select',
							'title'   => __( 'Page Layout', 'porto' ),
							'options' => $page_layouts,
							'default' => 'fullwidth',
						),
						array(
							'id'      => 'member-page-style',
							'type'    => 'switch',
							'title'   => __( 'Page Style', 'porto' ),
							'default' => false,
							'on'      => __( 'Advance', 'porto' ),
							'off'     => __( 'Default', 'porto' ),
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
							'id'    => 'member-banner-block',
							'type'  => 'text',
							'title' => __( 'Global Banner Block', 'porto' ),
							'desc'  => __( 'Please input comma separated block slug names. You can create a block in <strong>Blocks/Add New</strong>.', 'porto' ),
						),
						array(
							'id'      => 'member-related',
							'type'    => 'switch',
							'title'   => __( 'Show Related Members', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
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
							'id'          => 'member-related-cols',
							'type'        => 'button_set',
							'required'    => array( 'member-related', 'equals', true ),
							'title'       => __( 'Related Members Columns', 'porto' ),
							'description' => __( 'reduce one column in left or right sidebar layout', 'porto' ),
							'options'     => array(
								'4' => '4',
								'3' => '3',
								'2' => '2',
							),
							'default'     => '4',
						),

						array(
							'id'      => 'single-member-socials',
							'type'    => 'switch',
							'title'   => __( 'Show Social Links', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),

						array(
							'id'       => 'single-member-social-link-style',
							'type'     => 'button_set',
							'required' => array( 'single-member-socials', 'equals', true ),
							'title'    => __( 'Social Links Style', 'porto' ),
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
							'options'  => array(
								'before'      => __( 'Before Overview', 'porto' ),
								''            => __( 'After Overview', 'porto' ),
								'below_thumb' => __( 'Below Member Image', 'porto' ),
							),
							'default'  => '',
						),
					),
				),
				$options_style
			);
			// FAQ
			$faq_options = array(
				'icon'       => 'Simple-Line-Icons-speech',
				'icon_class' => '',
				'title'      => __( 'FAQ', 'porto' ),
				'customizer' => false,
				'fields'     => array(
					array(
						'id'      => 'enable-faq',
						'type'    => 'switch',
						'title'   => __( 'FAQ Content Type', 'porto' ),
						'default' => false,
						'on'      => __( 'Enable', 'porto' ),
						'off'     => __( 'Disable', 'porto' ),
					),
					array(
						'id'          => 'faq-slug-name',
						'type'        => 'text',
						'title'       => __( 'Slug Name', 'porto' ),
						'placeholder' => 'faq',
					),
					array(
						'id'          => 'faq-name',
						'type'        => 'text',
						'title'       => __( 'Name', 'porto' ),
						'placeholder' => __( 'FAQs', 'porto' ),
					),
					array(
						'id'          => 'faq-singular-name',
						'type'        => 'text',
						'title'       => __( 'Singular Name', 'porto' ),
						'placeholder' => __( 'FAQ', 'porto' ),
					),
					array(
						'id'          => 'faq-cat-slug-name',
						'type'        => 'text',
						'title'       => __( 'Category Slug Name', 'porto' ),
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
							'id'      => 'faq-title',
							'type'    => 'text',
							'title'   => __( 'Page Title', 'porto' ),
							'default' => 'Frequently Asked <strong>Questions</strong>',
						),
						array(
							'id'      => 'faq-sub-title',
							'type'    => 'textarea',
							'title'   => __( 'Page Sub Title', 'porto' ),
							'default' => '',
						),
						array(
							'id'      => 'faq-cat-orderby',
							'type'    => 'button_set',
							'title'   => __( 'Sort Categories Order By', 'porto' ),
							'options' => $porto_categories_orderby,
							'default' => 'name',
						),
						array(
							'id'      => 'faq-cat-order',
							'type'    => 'button_set',
							'title'   => __( 'Sort Order for Categories', 'porto' ),
							'options' => $porto_categories_order,
							'default' => 'asc',
						),
						array(
							'id'      => 'faq-orderby',
							'type'    => 'button_set',
							'title'   => __( 'Sort Items Order By', 'porto' ),
							'options' => array_slice( $porto_categories_orderby, 0, 3 ),
							'default' => 'name',
						),
						array(
							'id'      => 'faq-order',
							'type'    => 'button_set',
							'title'   => __( 'Sort Order for Items', 'porto' ),
							'options' => $porto_categories_order,
							'default' => 'asc',
						),
						array(
							'id'      => 'faq-cat-sort-pos',
							'type'    => 'button_set',
							'title'   => __( 'Filter Position', 'porto' ),
							'options' => $porto_categories_sort_pos,
							'default' => 'content',
						),
						array(
							'id'      => 'faq-archive-layout',
							'type'    => 'image_select',
							'title'   => __( 'Page Layout', 'porto' ),
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
							'id'      => 'faq-infinite',
							'type'    => 'switch',
							'title'   => __( 'Enable Infinite Scroll', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
					),
				),
				$options_style,
				$faq_options
			);
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
							'default' => '',
							'options' => array(
								''     => __( 'Lightbox', 'porto' ),
								'link' => __( 'Link', 'porto' ),
							),
						),
						array(
							'id'        => 'woo-show-rating',
							'type'      => 'switch',
							'title'     => __( 'Show Rating in Woocommerce Products Widget', 'porto' ),
							'default'   => true,
							'on'        => __( 'Yes', 'porto' ),
							'off'       => __( 'No', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'      => 'woo-show-product-border',
							'type'    => 'switch',
							'title'   => __( 'Show Border on product images', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'      => 'product-hot',
							'type'    => 'switch',
							'title'   => __( 'Show "Hot" Label', 'porto' ),
							'desc'    => __( 'It will displayed in the featured product.', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'product-hot-label',
							'type'     => 'text',
							'required' => array( 'product-hot', 'equals', true ),
							'title'    => __( '"Hot" Text', 'porto' ),
							'default'  => '',
						),
						array(
							'id'      => 'product-sale',
							'type'    => 'switch',
							'title'   => __( 'Show "Sale" Label', 'porto' ),
							'default' => true,
							'on'      => __( 'Yes', 'porto' ),
							'off'     => __( 'No', 'porto' ),
						),
						array(
							'id'       => 'product-sale-label',
							'type'     => 'text',
							'required' => array( 'product-sale', 'equals', true ),
							'title'    => __( '"Sale" Text', 'porto' ),
							'default'  => '',
						),
						array(
							'id'       => 'product-sale-percent',
							'type'     => 'switch',
							'required' => array( 'product-sale', 'equals', true ),
							'title'    => __( 'Show Saved Sale Price Percentage', 'porto' ),
							'default'  => true,
							'on'       => __( 'Yes', 'porto' ),
							'off'      => __( 'No', 'porto' ),
						),
						array(
							'id'        => 'product-show-price-role',
							'type'      => 'button_set',
							'multi'     => true,
							'title'     => __( 'Select roles to see price', 'porto' ),
							'default'   => array(),
							'options'   => $all_roles,
							'transport' => 'refresh',
						),
						array(
							'id'        => 'woo-pre-order',
							'type'      => 'switch',
							'title'     => __( 'Enable Pre-Order', 'porto' ),
							'transport' => 'refresh',
						),
						array(
							'id'          => 'woo-pre-order-label',
							'type'        => 'text',
							'title'       => __( 'Pre-order Label', 'porto' ),
							'description' => __( 'This text will be used on \'Add to Cart\' button.', 'porto' ),
							'required'    => array( 'woo-pre-order', 'equals', true ),
							'transport'   => 'refresh',
						),
						array(
							'id'          => 'woo-pre-order-msg-date',
							'type'        => 'text',
							'title'       => __( 'Availability Date Text', 'porto' ),
							/* translators: available date */
							'description' => __( 'ex: Available date: %s (%s will be replaced with available date.)', 'porto' ),
							'required'    => array( 'woo-pre-order', 'equals', true ),
							'transport'   => 'refresh',
						),
						array(
							'id'          => 'woo-pre-order-msg-nodate',
							'type'        => 'text',
							'title'       => __( 'No Date Message', 'porto' ),
							'placeholder' => __( 'Available soon', 'porto' ),
							'required'    => array( 'woo-pre-order', 'equals', true ),
							'transport'   => 'refresh',
						),
					),
				),
				$options_style
			);
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Product Archives', 'porto' ),
				'fields'     => array(
					array(
						'id'      => 'product-archive-layout',
						'type'    => 'image_select',
						'title'   => __( 'Page Layout', 'porto' ),
						'options' => $page_layouts,
						'default' => 'left-sidebar',
					),
					array(
						'id'       => 'product-archive-sidebar2',
						'type'     => 'select',
						'title'    => __( 'Select Sidebar 2', 'porto' ),
						'required' => array( 'product-archive-layout', 'equals', $both_sidebars ),
						'data'     => 'sidebars',
					),
					array(
						'id'      => 'product-archive-filter-layout',
						'type'    => 'button_set',
						'title'   => __( 'Filter Layout', 'porto' ),
						'default' => '',
						'options' => array(
							''            => __( 'Filters in Left & Right Sidebar', 'porto' ),
							'horizontal'  => __( 'Horizontal filters 1', 'porto' ),
							'horizontal2' => __( 'Horizontal filters 2', 'porto' ),
						),
					),
					array(
						'id'      => 'product-infinite',
						'type'    => 'button_set',
						'title'   => __( 'Pagination style', 'porto' ),
						'default' => '',
						'options' => array(
							''                => __( 'Default', 'porto' ),
							'load_more'       => __( 'Load More', 'porto' ),
							'infinite_scroll' => __( 'Infinite Scroll', 'porto' ),
						),
					),
					array(
						'id'      => 'category-ajax',
						'type'    => 'switch',
						'title'   => __( 'Enable Ajax Filter', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'        => 'category-item',
						'type'      => 'text',
						'title'     => __( 'Products per Page', 'porto' ),
						'desc'      => __( 'Comma separated list of product counts.', 'porto' ),
						'default'   => '12,24,36',
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'category-view-mode',
						'type'      => 'button_set',
						'title'     => __( 'View Mode', 'porto' ),
						'options'   => porto_ct_category_view_mode(),
						'default'   => '',
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'shop-product-cols',
						'type'      => 'slider',
						'title'     => __( 'Shop Page Product Columns', 'porto' ),
						'default'   => 3,
						'min'       => 2,
						'max'       => 8,
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'shop-product-cols-mobile',
						'type'      => 'slider',
						'title'     => __( 'Shop Page Product Columns on Mobile ( < 576px )', 'porto' ),
						'default'   => 2,
						'min'       => 1,
						'max'       => 3,
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'product-cols',
						'type'      => 'slider',
						'title'     => __( 'Category Product Columns', 'porto' ),
						'default'   => 3,
						'min'       => 2,
						'max'       => 8,
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'product-cols-mobile',
						'type'      => 'slider',
						'title'     => __( 'Category Product Columns on Mobile ( < 576px )', 'porto' ),
						'default'   => 2,
						'min'       => 1,
						'max'       => 3,
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'cat-view-type',
						'type'      => 'image_select',
						'title'     => __( 'Category View Type', 'porto' ),
						'default'   => '',
						'options'   => array(
							''  => array(
								'alt' => __( 'Type 1', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/product_cat_view_1.jpg',
							),
							'2' => array(
								'alt' => __( 'Type 2', 'porto' ),
								'img' => PORTO_OPTIONS_URI . '/images/product_cat_view_2.jpg',
							),
						),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'category-image-hover',
						'type'      => 'switch',
						'title'     => __( 'Enable Image Hover Effect', 'porto' ),
						'default'   => true,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'product-stock',
						'type'      => 'switch',
						'title'     => __( 'Show "Out of stock" Status', 'porto' ),
						'default'   => true,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'     => '1',
						'type'   => 'info',
						'title'  => __( 'Add Links Options', 'porto' ),
						'notice' => false,
					),
					array(
						'id'        => 'category-addlinks-convert',
						'type'      => 'switch',
						'title'     => __( 'Change "A" Tag to "SPAN" Tag', 'porto' ),
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
						'type'      => 'button_set',
						'title'     => __( 'Add to Cart Notification Type', 'porto' ),
						'options'   => array(
							''  => __( 'Style 1', 'porto' ),
							'2' => __( 'Style 2', 'porto' ),
						),
						'default'   => '2',
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'category-hover',
						'type'      => 'switch',
						'title'     => __( 'Enable Hover Effect', 'porto' ),
						'default'   => true,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'show_swatch',
						'type'      => 'switch',
						'title'     => __( 'Show Color / Image swatch', 'porto' ),
						'subtitle'  => __( 'This is available for only variable product', 'porto' ),
						'default'   => false,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'product-categories',
						'type'      => 'switch',
						'title'     => __( 'Show Categories', 'porto' ),
						'default'   => true,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'product-review',
						'type'      => 'switch',
						'title'     => __( 'Show Reviews', 'porto' ),
						'default'   => true,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'product-price',
						'type'      => 'switch',
						'title'     => __( 'Show Price', 'porto' ),
						'default'   => true,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'product-desc',
						'type'      => 'switch',
						'title'     => __( 'Show Description', 'porto' ),
						'subtitle'  => __( 'This works for only Grid view.', 'porto' ),
						'default'   => false,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'product-wishlist',
						'type'      => 'switch',
						'title'     => __( 'Show Wishlist', 'porto' ),
						'default'   => true,
						'on'        => __( 'Yes', 'porto' ),
						'off'       => __( 'No', 'porto' ),
						'transport' => 'postMessage',
					),
					array(
						'id'        => 'product-quickview',
						'type'      => 'switch',
						'title'     => __( 'Show Quick View', 'porto' ),
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
						'default'   => '',
						'transport' => 'postMessage',
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
						'id'        => 'product-single-layout',
						'type'      => 'image_select',
						'title'     => __( 'Page Layout', 'porto' ),
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
						'options'   => array(
							'default'                => array(
								'title' => __( 'Default', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/products/default.jpg',
							),
							'extended'               => array(
								'title' => __( 'Extended', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/products/extended.jpg',
							),
							'full_width'             => array(
								'title' => __( 'Full Width', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/products/full_width.jpg',
							),
							'grid'                   => array(
								'title' => __( 'Grid Images', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/products/grid.jpg',
							),
							'sticky_info'            => array(
								'title' => __( 'Sticky Info', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/products/sticky_info.jpg',
							),
							'sticky_both_info'       => array(
								'title' => __( 'Sticky Left & Right Info', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/products/sticky_info_both.jpg',
							),
							'transparent'            => array(
								'title' => __( 'Transparent Images', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/products/transparent.jpg',
							),
							'centered_vertical_zoom' => array(
								'title' => __( 'Centered Vertical Zoom', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/products/centered_vertical_zoom.jpg',
							),
							'left_sidebar'           => array(
								'title' => __( 'Left Sidebar', 'porto' ),
								'img'   => PORTO_OPTIONS_URI . '/products/left_sidebar.jpg',
							),
							'builder'                => array(
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
						'desc'     => __( 'Please select a product layout. You can create a product layout in <strong>Product Layouts/Add New</strong>.', 'porto' ),
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
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'product-short-desc',
						'type'    => 'switch',
						'title'   => __( 'Show Short Description', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'product-custom-tabs-count',
						'type'    => 'text',
						'title'   => __( 'Custom Tabs Count', 'porto' ),
						'default' => '2',
					),
					array(
						'id'      => 'product-tabs-pos',
						'type'    => 'button_set',
						'title'   => __( 'Tabs Position', 'porto' ),
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
							'button' => __( 'Image / Color swatch', 'porto' ),
							'select' => __( 'Select Box', 'porto' ),
						),
					),
					array(
						'id'      => 'product-attr-desc',
						'type'    => 'switch',
						'title'   => __( 'Show Description of Selected Attribute', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'product-tab-title',
						'type'    => 'text',
						'title'   => __( 'Global Product Custom Tab Title', 'porto' ),
						'default' => '',
					),
					array(
						'id'      => 'product-tab-block',
						'type'    => 'text',
						'title'   => __( 'Global Product Custom Tab Block', 'porto' ),
						'desc'    => __( 'Input block slug name', 'porto' ),
						'default' => '',
					),
					array(
						'id'      => 'product-tab-priority',
						'type'    => 'text',
						'title'   => __( 'Global Product Custom Tab Priority', 'porto' ),
						'desc'    => __( 'Input the custom tab priority. (Description: 10, Additional Information: 20, Reviews: 30)', 'porto' ),
						'default' => '60',
					),
					array(
						'id'      => 'product-related',
						'type'    => 'switch',
						'title'   => __( 'Show Related Products', 'porto' ),
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
						'id'      => 'product-upsells',
						'type'    => 'switch',
						'title'   => __( 'Show Up Sells', 'porto' ),
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
					array(
						'id'      => 'product-share',
						'type'    => 'switch',
						'title'   => __( 'Show Social Share Links', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'        => 'product-content_bottom',
						'type'      => 'text',
						'title'     => __( 'Content Bottom', 'porto' ),
						'desc'      => __( 'Please input comma separated block slug names. You can create a block in <strong>Blocks/Add New</strong>.', 'porto' ),
						'transport' => 'refresh',
					),
				),
			);
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
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'product-thumbs-count',
						'type'     => 'text',
						'required' => array( 'product-thumbs', 'equals', true ),
						'title'    => __( 'Thumbnails Count', 'porto' ),
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
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'zoom-type',
						'type'    => 'button_set',
						'title'   => __( 'Zoom Type', 'porto' ),
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
						'default'  => true,
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'zoom-lens-size',
						'type'     => 'text',
						'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
						'title'    => __( 'Lens Size', 'porto' ),
						'default'  => '200',
					),
					array(
						'id'       => 'zoom-lens-shape',
						'type'     => 'button_set',
						'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
						'title'    => __( 'Lens Shape', 'porto' ),
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
						'default'  => '4',
					),
					array(
						'id'       => 'zoom-border-color',
						'type'     => 'color',
						'required' => array( 'zoom-type', 'equals', array( 'lens' ) ),
						'title'    => __( 'Border Color', 'porto' ),
						'default'  => '#888888',
					),
				),
			);
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Cart Page', 'porto' ),
				'transport'  => 'postMessage',
				'fields'     => array(
					array(
						'id'      => 'product-crosssell',
						'type'    => 'switch',
						'title'   => __( 'Show Cross Sells', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'product-crosssell-count',
						'type'     => 'text',
						'required' => array( 'product-crosssell', 'equals', true ),
						'title'    => __( 'Cross Sells Count', 'porto' ),
						'default'  => '8',
					),
					array(
						'id'        => 'cart-version',
						'type'      => 'button_set',
						'title'     => __( 'Cart Page Version', 'porto' ),
						'options'   => array(
							'v1' => __( 'Version 1', 'porto' ),
							'v2' => __( 'Version 2', 'porto' ),
						),
						'default'   => 'v2',
						'transport' => 'refresh',
					),
				),
			);
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Checkout Page', 'porto' ),
				'fields'     => array(
					array(
						'id'      => 'checkout-version',
						'type'    => 'button_set',
						'title'   => __( 'Checkout Page Version', 'porto' ),
						'options' => array(
							'v1' => __( 'Version 1', 'porto' ),
							'v2' => __( 'Version 2', 'porto' ),
						),
						'default' => 'v2',
					),
				),
			);
			$this->sections[] = array(
				'icon_class' => 'icon',
				'subsection' => true,
				'title'      => __( 'Catalog Mode', 'porto' ),
				'fields'     => array(
					array(
						'id'      => 'catalog-enable',
						'type'    => 'switch',
						'title'   => __( 'Enable Catalog Mode', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'catalog-price',
						'type'     => 'switch',
						'title'    => __( 'Show Price', 'porto' ),
						'default'  => false,
						'required' => array( 'catalog-enable', 'equals', true ),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'catalog-cart',
						'type'     => 'switch',
						'title'    => __( 'Show Add Cart Button', 'porto' ),
						'default'  => false,
						'required' => array( 'catalog-enable', 'equals', true ),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'catalog-readmore',
						'type'     => 'switch',
						'title'    => __( 'Show Read More Button', 'porto' ),
						'default'  => false,
						'required' => array( 'catalog-cart', 'equals', false ),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'catalog-readmore-target',
						'type'     => 'button_set',
						'title'    => __( 'Read More Button Target', 'porto' ),
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
					array(
						'id'       => 'catalog-review',
						'type'     => 'switch',
						'title'    => __( 'Show Reviews', 'porto' ),
						'default'  => false,
						'required' => array( 'catalog-enable', 'equals', true ),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'catalog-admin',
						'type'     => 'switch',
						'title'    => __( 'Enable also for administrators', 'porto' ),
						'default'  => true,
						'required' => array( 'catalog-enable', 'equals', true ),
						'on'       => __( 'Yes', 'porto' ),
						'off'      => __( 'No', 'porto' ),
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
						'title'   => __( 'Fields', 'porto' ),
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
							'id'    => '1',
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
							'id'    => '1',
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
							'id'    => '1',
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
							'id'    => '1',
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
						'default' => true,
						'on'      => __( 'Enable', 'porto' ),
						'off'     => __( 'Disable', 'porto' ),
					),
					array(
						'id'      => 'mobile-menu-item-nofollow',
						'type'    => 'switch',
						'title'   => __( 'Add rel="nofollow" to mobile menu items', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => '1',
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
						'desc'    => __( 'Input a block slug name', 'porto' ),
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
						'id'      => 'share-enable',
						'type'    => 'switch',
						'title'   => __( 'Show Social Links', 'porto' ),
						'desc'    => __( 'Show social links in post and product, page, portfolio, etc.', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'share-nofollow',
						'type'     => 'switch',
						'title'    => __( 'Add rel="nofollow" to social links', 'porto' ),
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
						'id'      => 'slider-loop',
						'type'    => 'switch',
						'title'   => __( 'Loop', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'slider-autoplay',
						'type'    => 'switch',
						'title'   => __( 'Auto Play', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-speed',
						'type'     => 'text',
						'title'    => __( 'Play Speed', 'porto' ),
						'required' => array( 'slider-autoplay', 'equals', true ),
						'desc'     => __( 'unit: millisecond', 'porto' ),
						'default'  => 5000,
					),
					array(
						'id'      => 'slider-autoheight',
						'type'    => 'switch',
						'title'   => __( 'Auto Height', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'slider-nav',
						'type'    => 'switch',
						'title'   => __( 'Show Next/Prev Buttons', 'porto' ),
						'default' => false,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'       => 'slider-nav-hover',
						'type'     => 'switch',
						'title'    => __( 'Show Next/Prev Buttons on Hover', 'porto' ),
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
						'id'      => 'slider-dots',
						'type'    => 'switch',
						'title'   => __( 'Show Dots Navigation', 'porto' ),
						'default' => true,
						'on'      => __( 'Yes', 'porto' ),
						'off'     => __( 'No', 'porto' ),
					),
					array(
						'id'      => 'slider-animatein',
						'type'    => 'text',
						'title'   => __( 'Animate In', 'porto' ),
						'default' => '',
						'desc'    => __( 'Please input animation. Please reference <a href="http://daneden.github.io/animate.css/">animate.css</a>. ex: fadeIn', 'porto' ),
					),
					array(
						'id'      => 'slider-animateout',
						'type'    => 'text',
						'title'   => __( 'Animate Out', 'porto' ),
						'default' => '',
						'desc'    => __( 'Please input animation. Please reference <a href="http://daneden.github.io/animate.css/">animate.css</a>. ex: fadeOut', 'porto' ),
					),
				),
			);
		}
		public function setHelpTabs() {
		}
		public function setArguments() {
			$theme = wp_get_theme(); // For use with some settings. Not necessary.

			$header_html = '<a class="porto-theme-link" href="' . esc_url( admin_url( 'admin.php?page=porto' ) ) . '">Welcome</a><a class="porto-theme-link" href="' . esc_url( admin_url( 'admin.php?page=porto' ) ) . '">Theme License</a><a class="porto-theme-link" href="' . esc_url( admin_url( 'admin.php?page=porto-changelog' ) ) . '">Change Log</a>';
			if ( get_theme_mod( 'theme_options_use_new_style', false ) ) {
				$menu_title   = esc_html__( 'Advanced Options', 'porto' );
				$header_html .= '<a class="porto-theme-link" href="' . esc_url( admin_url( 'customize.php' ) ) . '">' . __( 'Theme Options', 'porto' ) . '</a>';
			} else {
				$menu_title   = esc_html__( 'Theme Options', 'porto' );
				$header_html .= '<a class="porto-theme-link active nolink" href="' . esc_url( admin_url( 'themes.php?page=porto_settings' ) ) . '">' . $menu_title . '</a>';
			}

			$header_html .= '<a class="porto-theme-link" href="' . esc_url( admin_url( 'admin.php?page=porto-setup-wizard' ) ) . '">' . __( 'Setup Wizard', 'porto' ) . '</a><a class="porto-theme-link porto-theme-link-last" href="' . esc_url( admin_url( 'admin.php?page=porto-speed-optimize-wizard' ) ) . '">' . __( 'Speed Optimize Wizard', 'porto' ) . '</a>';

			if ( ! get_theme_mod( 'theme_options_use_new_style', false ) ) {
				$header_html .= '<a href="#" class="porto-theme-link switch-live-option-panel">' . esc_html__( 'Live Option Panel', 'porto' ) . '</a>';
			}

			$version_html = '<div class="header-left"><h1>' . $menu_title . '</h1><h6>' . __( 'Theme Options panel enables you full control over your website design and settings.', 'porto' ) . '</h6></div>';
			/* translators: theme version */
			$version_html .= '<div class="header-right"><div class="porto-logo"><img src="' . PORTO_URI . '/images/logo/logo_white_small.png" alt=""><span class="version">' . sprintf( __( 'version %s', 'porto' ), PORTO_VERSION ) . '</span></div></div>';

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
	}
	global $reduxPortoSettings;
	$reduxPortoSettings = new Redux_Framework_porto_settings();
}
