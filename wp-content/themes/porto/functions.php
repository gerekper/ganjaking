<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
/**
 * Define variables
 */
define( 'PORTO_DIR', get_parent_theme_file_path() );              // template directory
define( 'PORTO_LIB', PORTO_DIR . '/inc' );                        // library directory
define( 'PORTO_ADMIN', PORTO_LIB . '/admin' );                    // admin directory
define( 'PORTO_PLUGINS', PORTO_LIB . '/plugins' );                // plugins directory
define( 'PORTO_CONTENT_TYPES', PORTO_LIB . '/content_types' );    // content_types directory
define( 'PORTO_MENU', PORTO_LIB . '/menu' );                      // menu directory
define( 'PORTO_FUNCTIONS', PORTO_LIB . '/functions' );            // functions directory
define( 'PORTO_OPTIONS_DIR', PORTO_ADMIN . '/theme_options' );    // options directory
define( 'PORTO_URI', get_parent_theme_file_uri() );               // template directory uri
define( 'PORTO_CSS', PORTO_URI . '/css' );                        // css uri
define( 'PORTO_JS', PORTO_URI . '/js' );                          // javascript uri
define( 'PORTO_PLUGINS_URI', PORTO_URI . '/inc/plugins' );             // plugins uri
define( 'PORTO_OPTIONS_URI', PORTO_URI . '/inc/admin/theme_options' ); // theme options uri
define( 'PORTO_LIB_URI', PORTO_URI . '/inc/lib' );                     // library uri

$theme_version = '';
$theme         = wp_get_theme();
if ( is_child_theme() ) {
	$theme = wp_get_theme( $theme->template );
}
$theme_version = $theme->version;
define( 'PORTO_VERSION', $theme_version );                    // set current version
/**
 * WordPress theme check
 */
// set content width
if ( ! isset( $content_width ) ) {
	$content_width = 1140;
}
/**
 * Porto content types functions
 */
require_once PORTO_FUNCTIONS . '/content_type.php';
/**
 * Porto functions
 */
require_once PORTO_FUNCTIONS . '/functions.php';
/**
 * Menu
 */
require_once PORTO_MENU . '/menu.php';

/**
 * Porto theme options
 */
require_once PORTO_ADMIN . '/theme_options.php';

/**
 * Porto admin options
 */
if ( current_user_can( 'manage_options' ) ) {
	require_once PORTO_ADMIN . '/admin.php';
}
/**
 * Porto Extensions
 */
require_once PORTO_LIB . '/lib/setup.php';
/**
 * Install Plugins
 */
require_once PORTO_PLUGINS . '/plugins.php';
/**
 * Theme support & Theme setup
 */
// theme setup
if ( ! function_exists( 'porto_setup' ) ) :
	function porto_setup() {
		add_theme_support( 'title-tag' );
		// add_theme_support( 'custom-header', array() );
		// add_theme_support( 'custom-background', array() );
		add_editor_style( array( 'style.css', 'style_rtl.css' ) );
		if ( defined( 'WOOCOMMERCE_VERSION' ) ) {
			if ( version_compare( WOOCOMMERCE_VERSION, '2.1' ) >= 0 ) {
				add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
			} else {
				define( 'WOOCOMMERCE_USE_CSS', false );
			}
		}
		// translation
		load_theme_textdomain( 'porto', PORTO_DIR . '/languages' );
		load_child_theme_textdomain( 'porto', get_stylesheet_directory() . '/languages' );

		global $porto_settings, $porto_settings_optimize;
		// default rss feed links
		add_theme_support( 'automatic-feed-links' );
		// add support for post thumbnails
		add_theme_support( 'post-thumbnails' );
		// add image sizes
		add_image_size( 'blog-large', 1140, 445, true );
		add_image_size( 'blog-medium', 463, 348, true );
		add_image_size( 'blog-masonry', 640, 9999, false );
		add_image_size( 'blog-masonry-small', 400, 9999, false );
		add_image_size( 'blog-grid', 640, 480, true );
		add_image_size( 'blog-grid-small', 400, 300, true );
		add_image_size( 'related-post', ( isset( $porto_settings['post-related-image-size'] ) && (int) $porto_settings['post-related-image-size']['width'] ) ? (int) $porto_settings['post-related-image-size']['width'] : 450, ( isset( $porto_settings['post-related-image-size'] ) && (int) $porto_settings['post-related-image-size']['height'] ) ? (int) $porto_settings['post-related-image-size']['height'] : 231, true );
		if ( isset( $porto_settings['enable-portfolio'] ) && $porto_settings['enable-portfolio'] ) {
			add_image_size( 'portfolio-grid-one', 1140, 595, true );
			add_image_size( 'portfolio-grid-two', 560, 560, true );
			add_image_size( 'portfolio-grid', 367, 367, true );
			add_image_size( 'portfolio-masonry', 367, 9999, false );
			add_image_size( 'portfolio-full', 1140, 595, true );
			add_image_size( 'portfolio-large', 560, 367, true );
			add_image_size( 'portfolio-medium', 367, 367, true );
			add_image_size( 'portfolio-timeline', 560, 560, true );
			add_image_size( 'related-portfolio', 367, 367, true );
			add_image_size( 'portfolio-cat-stripes', 494, 1080, true );
			add_image_size( 'portfolio-cat-parallax', 1970, 627, true );
			add_image_size( 'portfolio-thumbnail', 200, 150, true );
		}

		if ( isset( $porto_settings['enable-member'] ) && $porto_settings['enable-member'] ) {
			add_image_size( 'member-two', 560, 560, true );
			add_image_size( 'member', 367, 367, true );
		}
		add_image_size( 'widget-thumb-medium', 85, 85, true );
		// add_image_size( 'widget-thumb', 50, 50, true );
		// woocommerce support
		add_theme_support( 'woocommerce', array( 'gallery_thumbnail_image_width' => 150 ) );
		// allow shortcodes in widget text
		add_filter( 'widget_text', 'do_shortcode' );
		// register menus
		register_nav_menus(
			array(
				'main_menu'         => __( 'Main Menu', 'porto' ),
				'secondary_menu'    => __( 'Secondary Menu', 'porto' ),
				'sidebar_menu'      => __( 'Sidebar Menu', 'porto' ),
				'top_nav'           => __( 'Top Navigation', 'porto' ),
				'view_switcher'     => __( 'View Switcher', 'porto' ),
				'currency_switcher' => __( 'Currency Switcher', 'porto' ),
			)
		);

		// add post formats
		add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio', 'chat' ) );

		// to output valid HTML5 formats
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);

		// disable master slider woocommerce product slider
		$options = get_option( 'msp_woocommerce' );
		if ( isset( $options ) && isset( $options['enable_single_product_slider'] ) && 'on' == $options['enable_single_product_slider'] ) {
			$options['enable_single_product_slider'] = '';
			update_option( 'msp_woocommerce', $options );
		}

		if ( ! is_customize_preview() ) {
			$porto_settings_optimize = get_option( 'porto_settings_optimize', array() );
		} else {
			$porto_settings_optimize = array();
		}

		if ( ( isset( $porto_settings['google-webfont-loader'] ) && $porto_settings['google-webfont-loader'] ) || ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
			add_filter( 'wp_head', 'porto_google_webfont_loader' );
		}
		if ( ! empty( $porto_settings_optimize['lazyload_menu'] ) ) {
			add_action( 'template_redirect', 'porto_action_lazyload_menu', 20 );
		}

		// add support
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );

		// Editor color palette.
		add_theme_support(
			'editor-color-palette',
			array(
				array(
					'name'  => __( 'Primary', 'porto' ),
					'slug'  => 'primary',
					'color' => $porto_settings['skin-color'],
				),
				array(
					'name'  => __( 'Secondary', 'porto' ),
					'slug'  => 'secondary',
					'color' => $porto_settings['secondary-color'],
				),
				array(
					'name'  => __( 'Tertiary', 'porto' ),
					'slug'  => 'tertiary',
					'color' => $porto_settings['tertiary-color'],
				),
				array(
					'name'  => __( 'Quaternary', 'porto' ),
					'slug'  => 'quaternary',
					'color' => $porto_settings['quaternary-color'],
				),
				array(
					'name'  => __( 'Dark', 'porto' ),
					'slug'  => 'dark',
					'color' => $porto_settings['dark-color'],
				),
				array(
					'name'  => __( 'Light', 'porto' ),
					'slug'  => 'light',
					'color' => $porto_settings['light-color'],
				),
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'porto_setup' );

/**
 * Enqueue css, js files
 */
add_action( 'wp_enqueue_scripts', 'porto_pre_css', 8 );
add_action( 'wp_enqueue_scripts', 'porto_css', 1000 );
add_action( 'wp_enqueue_scripts', 'porto_pre_scripts' );
add_action( 'wp_enqueue_scripts', 'porto_scripts', 1000 );
add_action( 'admin_enqueue_scripts', 'porto_admin_css', 1000 );
add_action( 'admin_enqueue_scripts', 'porto_admin_scripts', 1000 );

if ( is_admin() ) {

	add_action( 'enqueue_block_editor_assets', 'porto_admin_block_css', 1000 );

	if ( function_exists( 'mailpoet_deactivate_plugin' ) ) {
		// fix MailPoet3 style issue
		add_filter( 'mailpoet_conflict_resolver_whitelist_style', 'porto_add_styles_mailpoet_pages' );

		function porto_add_styles_mailpoet_pages( $styles ) {
			$styles[] = PORTO_CSS . '/admin.min.css';
			$styles[] = PORTO_CSS . '/admin_bar.css';
			return $styles;
		}
	}

	function porto_admin_block_css() {
		porto_include_google_font();

		$required_css = array( 'wp-edit-blocks', 'porto_admin' );
		if ( wp_style_is( 'owl.carousel', 'enqueued' ) ) {
			$required_css[] = 'owl.carousel';
		}

		if ( is_rtl() ) {
			wp_enqueue_style( 'porto-blocks-editor', PORTO_CSS . '/editor_rtl.css', $required_css, PORTO_VERSION );
		} else {
			wp_enqueue_style( 'porto-blocks-editor', PORTO_CSS . '/editor.css', $required_css, PORTO_VERSION );
		}

		porto_register_style( 'porto-blocks-editor-dynamic', 'style-editor', false, true, array( 'porto-blocks-editor' ) );
		wp_enqueue_style( 'porto-blocks-editor-dynamic' );

		global $porto_settings;
		$theme_options_custom_css = $porto_settings['css-code'];
		if ( $theme_options_custom_css ) {
			wp_add_inline_style( 'porto-blocks-editor-dynamic', wp_strip_all_tags( preg_replace( '#<style[^>]*>(.*)</style>#is', '$1', $theme_options_custom_css ) ) );
		}
	}
}

function porto_google_webfont_loader() {

	global $porto_settings;

	$gfont = array();
	$fonts = porto_settings_google_fonts();
	foreach ( $fonts as $option => $weights ) {
		if ( isset( $porto_settings[ $option . '-font' ]['google'] ) && 'false' !== $porto_settings[ $option . '-font' ]['google'] ) {
			$font = isset( $porto_settings[ $option . '-font' ]['font-family'] ) ? urlencode( $porto_settings[ $option . '-font' ]['font-family'] ) : '';
			if ( $font ) {
				$has_italic  = false;
				$font_weight = isset( $porto_settings[ $option . '-font' ]['font-weight'] ) ? $porto_settings[ $option . '-font' ]['font-weight'] : '';
				if ( $font_weight && isset( $porto_settings[ $option . '-font' ]['font-style'] ) && 'italic' == $porto_settings[ $option . '-font' ]['font-style'] ) {
					$font_weight .= 'italic';
					$has_italic   = true;
				}
				if ( $font_weight && ! in_array( $font_weight, $weights ) ) {
					$weights[] = $font_weight;
				}
				if ( isset( $gfont[ $font ] ) ) {
					foreach ( $gfont[ $font ] as $w ) {
						if ( ! in_array( $w, $weights ) ) {
							$weights[] = $w;
						}
					}
				}
				if ( $has_italic && ! in_array( '400italic', $weights ) ) {
					$weights[] = '400italic';
				}
				$gfont[ $font ] = $weights;
			}
		}
	}

	// charset
	$charsets = array();
	$subsets  = '';
	if ( isset( $porto_settings['select-google-charset'] ) && $porto_settings['select-google-charset'] && isset( $porto_settings['google-charsets'] ) && $porto_settings['google-charsets'] ) {
		foreach ( $porto_settings['google-charsets'] as $charset ) {
			if ( $charset && ! in_array( $charset, $charsets ) ) {
				$charsets[] = $charset;
			}
		}
	}
	if ( ! empty( $charsets ) ) {
		$subsets = implode( ',', $charsets );
	}

	$font_family_arr = array();
	foreach ( $gfont as $font => $weights ) {
		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			$weights = array( '400' );
			$subsets = false;
		} else {
			sort( $weights );
		}
		$font_family_arr[] = "'" . esc_js( str_replace( ' ', '+', $font ) . ( empty( $weights ) ? '' : ':' . implode( ',', $weights ) ) . ( $subsets ? ':' . $subsets : '' ) ) . "'";
		$subsets           = '';
	}
	if ( ! empty( $font_family_arr ) ) {
		?>
		<script type="text/javascript">
		WebFontConfig = {
			google: { families: [ <?php echo implode( ',', $font_family_arr ); ?> ] }
		};
		(function(d) {
			var wf = d.createElement('script'), s = d.scripts[0];
			wf.src = '<?php echo PORTO_JS; ?>/libs/webfont.js';
			wf.async = true;
			s.parentNode.insertBefore(wf, s);
		})(document);</script>
		<?php
	}
}

if ( ! function_exists( 'porto_pre_css' ) ) {
	function porto_pre_css() {
		$yith_wcwl = '';
		if ( wp_style_is( 'yith-wcwl-main', 'registered' ) ) {
			$yith_wcwl = 'yith-wcwl-main';
		}

		if ( wp_style_is( 'yith-wcwl-user-main', 'registered' ) ) {
			$yith_wcwl = 'yith-wcwl-user-main';
		}

		if ( $yith_wcwl ) {
			$yith_wcwl_style = wp_styles()->registered[ $yith_wcwl ];
			if ( isset( $yith_wcwl_style->deps ) && ! empty( $yith_wcwl_style->deps ) ) {
				foreach ( $yith_wcwl_style->deps as $index => $dep ) {
					if ( 'yith-wcwl-font-awesome' == $dep ) {
						unset( $yith_wcwl_style->deps[ $index ] );
						break;
					}
				}
			}
		}
	}
}

function porto_css() {
	// deregister plugin styles
	wp_deregister_style( 'font-awesome' );
	wp_dequeue_style( 'font-awesome' );
	wp_deregister_style( 'yith-wcwl-font-awesome' );
	wp_dequeue_style( 'yith-wcwl-font-awesome' );
	wp_dequeue_style( 'bsf-Simple-Line-Icons' );
	wp_deregister_style( 'vc_animate-css' );
	wp_dequeue_style( 'vc_animate-css' );
	wp_deregister_style( 'vc_font_awesome_5' );
	wp_dequeue_style( 'vc_font_awesome_5' );

	if ( class_exists( 'RevSliderFront' ) ) {
		remove_action( 'wp_footer', array( 'RevSliderFront', 'load_icon_fonts' ) );
		add_action( 'wp_footer', 'porto_revslider_icon_fonts' );
	}

	global $porto_settings, $porto_settings_optimize, $post;

	// import revslider js/css files for only used pages
	if ( class_exists( 'RevSlider' ) && isset( $porto_settings_optimize['optimize_revslider'] ) && $porto_settings_optimize['optimize_revslider'] ) {
		$use_revslider = false;
		$banner_type   = porto_get_meta_value( 'banner_type' );
		$rev_slider    = porto_get_meta_value( 'rev_slider' );
		if ( 'rev_slider' === $banner_type && ! empty( $rev_slider ) ) {
			$use_revslider = true;
		}
		if ( ! $use_revslider && is_singular( 'portfolio' ) ) {
			$portfolio_layout = get_post_meta( $post->ID, 'portfolio_layout', true );
			$portfolio_layout = ( 'default' == $portfolio_layout || ! $portfolio_layout ) ? $porto_settings['portfolio-content-layout'] : $portfolio_layout;
			if ( 'carousel' == $portfolio_layout ) {
				$use_revslider = true;
			}
		}
		if ( ! $use_revslider && isset( $porto_settings_optimize['optimize_revslider_pages'] ) ) {
			$rev_pages = $porto_settings_optimize['optimize_revslider_pages'];
			if ( $rev_pages && ! empty( $rev_pages ) ) {
				if ( ! is_search() && ! is_404() && isset( $post->ID ) && in_array( $post->ID, $rev_pages ) ) {
					$use_revslider = true;
				}
			}
		}
		if ( ! $use_revslider && isset( $porto_settings_optimize['optimize_revslider_portfolio'] ) && $porto_settings_optimize['optimize_revslider_portfolio'] && ( ( function_exists( 'is_porto_portfolios_page' ) && is_porto_portfolios_page() ) || is_tax( 'portfolio_cat' ) || is_tax( 'portfolio_skills' ) ) ) {
			$use_revslider = true;
		}
		if ( ! $use_revslider ) {
			wp_dequeue_style( 'rs-plugin-settings' );
			wp_dequeue_script( 'tp-tools' );
			wp_dequeue_script( 'revmin' );
		}
	}

	if ( ! wp_style_is( 'js_composer_front' ) ) {
		wp_enqueue_style( 'js_composer_front' );
	}
	// load ultimate addons default js
	$bsf_options             = get_option( 'bsf_options' );
	$ultimate_global_scripts = ( isset( $bsf_options['ultimate_global_scripts'] ) ) ? $bsf_options['ultimate_global_scripts'] : false;
	if ( 'enable' !== $ultimate_global_scripts ) {
		$ultimate_css = get_option( 'ultimate_css' );
		if ( 'enable' == $ultimate_css ) {
			if ( ! wp_style_is( 'ultimate-style-min' ) ) {
				wp_enqueue_style( 'ultimate-style-min' );
			}
		} else {
			if ( ! wp_style_is( 'ultimate-style' ) ) {
				wp_enqueue_style( 'ultimate-style' );
			}
		}
	}

	// dequeue block css
	if ( class_exists( 'Woocommerce' ) && isset( $porto_settings_optimize['dequeue_wc_block_css'] ) && $porto_settings_optimize['dequeue_wc_block_css'] ) {
		wp_dequeue_style( 'wc-block-vendors-style' );
		wp_dequeue_style( 'wc-block-style' );
	}
	if ( isset( $porto_settings_optimize['dequeue_wp_block_css'] ) && $porto_settings_optimize['dequeue_wp_block_css'] ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
	}

	/*
	 register styles */
	// plugins styles
	wp_deregister_style( 'porto-plugins' );
	$optimized_suffix = '';
	if ( isset( $porto_settings_optimize['optimize_fontawesome'] ) && $porto_settings_optimize['optimize_fontawesome'] ) {
		$optimized_suffix = '_optimized';
	}
	if ( is_rtl() ) {
		wp_register_style( 'porto-plugins', PORTO_URI . '/css/plugins_rtl' . $optimized_suffix . '.css?ver=' . PORTO_VERSION );
	} else {
		wp_register_style( 'porto-plugins', PORTO_URI . '/css/plugins' . $optimized_suffix . '.css?ver=' . PORTO_VERSION );
	}

	// default styles
	wp_deregister_style( 'porto-theme' );
	if ( is_rtl() ) {
		wp_register_style( 'porto-theme', PORTO_URI . '/css/theme_rtl.css?ver=' . PORTO_VERSION );
	} else {
		wp_register_style( 'porto-theme', PORTO_URI . '/css/theme.css?ver=' . PORTO_VERSION );
	}

	// shortcodes styles
	wp_deregister_style( 'porto-shortcodes' );
	if ( is_rtl() ) {
		porto_register_style( 'porto-shortcodes', 'shortcodes_rtl', false, true );
	} else {
		porto_register_style( 'porto-shortcodes', 'shortcodes', false, true );
	}

	// woocommerce styles
	if ( class_exists( 'WooCommerce' ) ) {
		wp_deregister_style( 'porto-theme-shop' );
		if ( is_rtl() ) {
			wp_register_style( 'porto-theme-shop', PORTO_URI . '/css/theme_rtl_shop.css?ver=' . PORTO_VERSION );
		} else {
			wp_register_style( 'porto-theme-shop', PORTO_URI . '/css/theme_shop.css?ver=' . PORTO_VERSION );
		}
	}

	// bbpress, buddypress styles
	if ( class_exists( 'bbPress' ) || class_exists( 'BuddyPress' ) ) {
		wp_deregister_style( 'porto-theme-bbpress' );
		if ( is_rtl() ) {
			wp_register_style( 'porto-theme-bbpress', PORTO_URI . '/css/theme_rtl_bbpress.css?ver=' . PORTO_VERSION );
		} else {
			wp_register_style( 'porto-theme-bbpress', PORTO_URI . '/css/theme_bbpress.css?ver=' . PORTO_VERSION );
		}
	}

	// custom styles
	wp_deregister_style( 'porto-style' );
	wp_register_style( 'porto-style', PORTO_URI . '/style.css' );
	if ( is_rtl() ) {
		wp_deregister_style( 'porto-style-rtl' );
		wp_register_style( 'porto-style-rtl', PORTO_URI . '/style_rtl.css' );
	}

	// Load Google fonts
	if ( ( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) && ( ! isset( $porto_settings['google-webfont-loader'] ) || ! $porto_settings['google-webfont-loader'] ) ) {
		porto_include_google_font();
	}

	/* enqueue styles */
	if ( defined( 'ELEMENTOR_VERSION' ) && ! wp_style_is( 'elementor-frontend', 'enqueued' ) && porto_check_using_elementor_style() ) {
		do_action( 'elementor/frontend/before_enqueue_styles' );
		wp_enqueue_style( 'elementor-icons' );
		wp_enqueue_style( 'elementor-animations' );
		wp_enqueue_style( 'elementor-frontend' );

		/*$kit_id = \Elementor\Plugin::$instance->kits_manager->get_active_id();
		if ( $kit_id ) {
			wp_enqueue_style( 'elementor-post-' . $kit_id, wp_upload_dir()['baseurl'] . '/elementor/css/post-' . $kit_id . '.css' );
		}*/

		if ( isset( \Elementor\Plugin::$instance ) ) {
			add_action(
				'wp_footer',
				function() {
					try {
						wp_enqueue_script( 'elementor-frontend' );
						$settings = \Elementor\Plugin::$instance->frontend->get_settings();
						\Elementor\Utils::print_js_config( 'elementor-frontend', 'elementorFrontendConfig', $settings );
					} catch ( Exception $e ) {
					}
				}
			);
		}
	}

	wp_enqueue_style( 'bootstrap' );
	wp_enqueue_style( 'porto-plugins' );
	wp_enqueue_style( 'porto-theme' );
	wp_enqueue_style( 'porto-shortcodes' );
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'porto-theme-shop' );
	}
	if ( class_exists( 'bbPress' ) || class_exists( 'BuddyPress' ) ) {
		wp_enqueue_style( 'porto-theme-bbpress' );
	}
	wp_enqueue_style( 'porto-dynamic-style' );

	if ( defined( 'ELEMENTOR_VERSION' ) && is_singular() && wp_style_is( 'elementor-post-' . intval( get_the_ID() ), 'enqueued' ) ) {
		wp_dequeue_style( 'elementor-post-' . intval( get_the_ID() ) );
		wp_enqueue_style( 'elementor-post-' . intval( get_the_ID() ) );
	}
	wp_enqueue_style( 'porto-style' );
	if ( is_rtl() ) {
		wp_enqueue_style( 'porto-style-rtl' );
	}

	if ( current_user_can( 'edit_theme_options' ) ) {
		// admin style
		wp_enqueue_style( 'porto_admin_bar', PORTO_CSS . '/admin_bar.css', false, PORTO_VERSION, 'all' );
	}
	porto_enqueue_revslider_css();
}

if ( ! function_exists( 'porto_include_google_font' ) ) :
	function porto_include_google_font() {
		global $porto_settings;
		$gfont = array();
		$fonts = porto_settings_google_fonts();
		foreach ( $fonts as $option => $weights ) {
			if ( isset( $porto_settings[ $option . '-font' ]['google'] ) && 'false' !== $porto_settings[ $option . '-font' ]['google'] ) {
				$font = isset( $porto_settings[ $option . '-font' ]['font-family'] ) ? urlencode( $porto_settings[ $option . '-font' ]['font-family'] ) : '';
				if ( $font ) {
					$has_italic  = false;
					$font_weight = isset( $porto_settings[ $option . '-font' ]['font-weight'] ) ? $porto_settings[ $option . '-font' ]['font-weight'] : '';
					if ( $font_weight && isset( $porto_settings[ $option . '-font' ]['font-style'] ) && 'italic' == $porto_settings[ $option . '-font' ]['font-style'] ) {
						$font_weight .= 'italic';
						$has_italic   = true;
					}
					if ( $font_weight && ! in_array( $font_weight, $weights ) ) {
						$weights[] = $font_weight;
					}
					if ( isset( $gfont[ $font ] ) ) {
						foreach ( $gfont[ $font ] as $w ) {
							if ( ! in_array( $w, $weights ) ) {
								$weights[] = $w;
							}
						}
					}
					if ( $has_italic && ! in_array( '400italic', $weights ) ) {
						$weights[] = '400italic';
					}
					$gfont[ $font ] = $weights;
				}
			}
		}
		$font_family     = '';
		$font_family_arr = array();
		foreach ( $gfont as $font => $weights ) {
			sort( $weights );
			$font_family_arr[] = str_replace( ' ', '+', $font ) . ( empty( $weights ) ? '' : ':' . implode( ',', $weights ) );
		}
		if ( ! empty( $font_family_arr ) ) {
			$font_family = implode( '%7C', $font_family_arr );
		}
		if ( $font_family ) {
			$charsets = array();
			if ( isset( $porto_settings['select-google-charset'] ) && $porto_settings['select-google-charset'] && isset( $porto_settings['google-charsets'] ) && $porto_settings['google-charsets'] ) {
				foreach ( $porto_settings['google-charsets'] as $charset ) {
					if ( $charset && ! in_array( $charset, $charsets ) ) {
						$charsets[] = $charset;
					}
				}
			}

			$custom_font_args = array(
				'family' => $font_family,
			);
			if ( ! empty( $charsets ) ) {
				$custom_font_args['subset'] = implode( ',', $charsets );
			}

			$google_font_url = add_query_arg( $custom_font_args, '//fonts.googleapis.com/css' );
			wp_register_style( 'porto-google-fonts', esc_url( $google_font_url ) );
			wp_enqueue_style( 'porto-google-fonts' );
		}
	}
endif;

function porto_register_style( $handle, $filename, $themedir = true, $load_default = true, $deps = array() ) {
	if ( $themedir ) {
		$blog_id  = porto_get_blog_id();
		$css_file = PORTO_DIR . '/css/' . $filename . '_' . $blog_id . '.css';
		$css_uri  = PORTO_URI . '/css/' . $filename . '_' . $blog_id . '.css';
	} else {
		$upload_dir = wp_upload_dir();
		$css_file   = $upload_dir['basedir'] . '/porto_styles/' . $filename . '.css';
		$css_uri    = $upload_dir['baseurl'] . '/porto_styles/' . $filename . '.css';
	}
	if ( file_exists( $css_file ) ) {
		wp_register_style( $handle, $css_uri, $deps, PORTO_VERSION );
	} elseif ( $load_default ) {
		if ( 'style-editor' == $filename ) {
			ob_start();
			require_once PORTO_DIR . '/style-editor.php';
			$css = ob_get_contents();
			ob_end_clean();
			wp_add_inline_style( 'porto-shortcodes', $css );
		} else {
			wp_register_style( $handle, PORTO_URI . '/css/' . $filename . '.css', $deps, PORTO_VERSION );
		}
	}
}

function porto_pre_scripts() {
	if ( ! is_admin() && ! in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
		wp_reset_postdata();
		wp_register_script( 'isotope', PORTO_JS . '/libs/isotope.pkgd.min.js', array(), '3.0.6', true );
		wp_register_script( 'easypiechart', PORTO_JS . '/libs/easypiechart.min.js', array(), '2.1.4', true );
		wp_register_script( 'jquery-mousewheel', PORTO_JS . '/libs/jquery.mousewheel.min.js', array(), '3.1.13', true );
		wp_register_script( 'jquery-vide', PORTO_JS . '/libs/jquery.vide.min.js', array(), '0.5.1', true );
		wp_register_script( 'jquery-lazyload', PORTO_JS . '/libs/jquery.lazyload.min.js', array(), '1.9.7', true );
	}
}

function porto_scripts() {
	global $porto_settings, $porto_settings_optimize;
	if ( ! is_admin() && ! in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {

		// comment reply
		if ( is_singular() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// load visual composer default js
		if ( ! wp_script_is( 'wpb_composer_front_js' ) ) {
			wp_enqueue_script( 'wpb_composer_front_js' );
		}
		// load ultimate addons default js
		$bsf_options             = get_option( 'bsf_options' );
		$ultimate_global_scripts = ( isset( $bsf_options['ultimate_global_scripts'] ) ) ? $bsf_options['ultimate_global_scripts'] : false;
		if ( 'enable' !== $ultimate_global_scripts ) {
			$is_ajax             = false;
			$ultimate_ajax_theme = get_option( 'ultimate_ajax_theme' );
			if ( 'enable' == $ultimate_ajax_theme ) {
				$is_ajax = true;
			}
			$ultimate_js  = get_option( 'ultimate_js', 'disable' );
			$bsf_dev_mode = ( isset( $bsf_options['dev_mode'] ) ) ? $bsf_options['dev_mode'] : false;
			if ( ( 'enable' == $ultimate_js || $is_ajax ) && ( 'enable' != $bsf_dev_mode ) ) {
				if ( ! wp_script_is( 'ultimate-script' ) ) {
					wp_enqueue_script( 'ultimate-script' );
				}
			}
		}

		$min_suffix = '';
		if ( isset( $porto_settings_optimize['minify_css'] ) && $porto_settings_optimize['minify_css'] ) {
			$min_suffix = '.min';
		}
		// porto scripts
		wp_register_script( 'popper', PORTO_JS . '/libs/popper.min.js', array( 'jquery', 'jquery-migrate' ), '1.12.5', true );
		wp_enqueue_script( 'popper' );

		$optimize_suffix = '';
		if ( isset( $porto_settings_optimize['optimize_bootstrap'] ) && $porto_settings_optimize['optimize_bootstrap'] ) {
			$optimize_suffix = '.optimized';
		}
		wp_register_script( 'bootstrap', PORTO_JS . '/bootstrap' . $optimize_suffix . $min_suffix . '.js', array( 'popper' ), '4.1.3', true );
		wp_enqueue_script( 'bootstrap' );

		/* plugins */
		//wp_deregister_script( 'isotope' );
		wp_register_script( 'jquery-cookie', PORTO_JS . '/libs/jquery.cookie.min.js', array(), '1.4.1', true );
		wp_register_script( 'owl.carousel', PORTO_JS . '/libs/owl.carousel.min.js', array(), '2.3.4', true );
		//wp_register_script( 'jquery-appear', PORTO_JS . '/libs/jquery.appear.min.js', array(), null, true );
		wp_register_script( 'jquery-fitvids', PORTO_JS . '/libs/jquery.fitvids.min.js', array(), '1.1', true );
		wp_register_script( 'jquery-matchHeight', PORTO_JS . '/libs/jquery.matchHeight.min.js', array(), null, true );
		wp_register_script( 'modernizr', PORTO_JS . '/libs/modernizr.js', array(), '2.8.3', true );
		wp_register_script( 'jquery-magnific-popup', PORTO_JS . '/libs/jquery.magnific-popup.min.js', array(), '1.1.0', true );
		wp_register_script( 'jquery-selectric', PORTO_JS . '/libs/jquery.selectric.min.js', array(), '1.9.6', true );
		wp_register_script( 'jquery-waitforimages', PORTO_JS . '/libs/jquery.waitforimages.min.js', array(), '2.0.2', true );
		wp_register_script( 'skrollr', PORTO_JS . '/libs/skrollr.min.js', array(), '0.6.30', true );

		wp_enqueue_script( 'jquery-cookie' );
		wp_enqueue_script( 'owl.carousel' );
		//wp_enqueue_script( 'jquery-appear' );
		wp_enqueue_script( 'jquery-fitvids' );
		wp_enqueue_script( 'jquery-matchHeight' );
		wp_enqueue_script( 'modernizr' );
		wp_enqueue_script( 'jquery-magnific-popup' );
		wp_enqueue_script( 'jquery-waitforimages' );

		if ( $porto_settings['show-searchform'] && isset( $porto_settings['search-cats'] ) && $porto_settings['search-cats'] ) {
			wp_enqueue_script( 'jquery-selectric' );
		}
		if ( ( 'masonry' == $porto_settings['post-layout'] && is_home() || ( is_archive() && 'post' == get_post_type() ) || is_search() ) || ( is_archive() && ( 'portfolio' == get_post_type() || 'member' == get_post_type() ) ) ) {
			wp_enqueue_script( 'isotope' );
		}

		if ( class_exists( 'Woocommerce' ) ) {
			wp_register_script( 'jquery-scrollbar', PORTO_JS . '/libs/jquery.scrollbar.min.js', array(), '0.2.10', true );
			wp_register_script( 'jquery-elevatezoom', PORTO_JS . '/libs/jquery.elevatezoom.min.js', array(), '3.0.8', true );
			wp_register_script( 'jquery-fancybox', PORTO_JS . '/libs/jquery.fancybox.min.js', array(), '2.1.5', true );
			wp_register_script( 'easy-responsive-tabs', PORTO_JS . '/libs/easy-responsive-tabs.min.js', array( 'jquery' ), PORTO_VERSION, true );
		}
		if ( 'overlay' == $porto_settings['menu-type'] ) {
			if ( ! class_exists( 'Woocommerce' ) ) {
				wp_register_script( 'jquery-scrollbar', PORTO_JS . '/libs/jquery.scrollbar.min.js', array(), '0.2.10', true );
			}
			wp_enqueue_script( 'jquery-scrollbar' );
		}

		wp_register_script( 'jquery-slick', PORTO_JS . '/libs/jquery.slick.min.js', array( 'jquery' ), PORTO_VERSION, true );
		global $porto_product_layout;
		if ( class_exists( 'Woocommerce' ) && isset( $porto_product_layout ) && $porto_product_layout ) {
			if ( 'transparent' == $porto_product_layout ) {
				wp_enqueue_script( 'jquery-slick' );
			}

			wp_enqueue_script( 'jquery-elevatezoom' );
		}

		// load porto theme js file
		wp_register_script( 'porto-theme', PORTO_JS . '/theme' . $min_suffix . '.js', array( 'jquery' ), PORTO_VERSION, true );
		wp_enqueue_script( 'porto-theme' );

		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			wp_enqueue_script( 'porto-vc-frontend-editor', PORTO_JS . '/admin/vc-frontend-editor.js', array( 'porto-theme' ), PORTO_VERSION, true );
		}

		wp_register_script( 'porto-theme-async', PORTO_JS . '/theme-async' . $min_suffix . '.js', array( 'jquery', 'porto-theme' ), PORTO_VERSION, true );
		wp_enqueue_script( 'porto-theme-async' );

		if ( class_exists( 'Woocommerce' ) ) {
			wp_register_script( 'porto-woocommerce-theme', PORTO_JS . '/woocommerce-theme' . $min_suffix . '.js', array( 'porto-theme' ), PORTO_VERSION, true );
			wp_enqueue_script( 'porto-woocommerce-theme' );
		}

		// compatible check with product filter plugin
		$js_wc_prdctfltr = false;
		if ( class_exists( 'WC_Prdctfltr' ) ) {
			$porto_settings['category-ajax'] = false;
			if ( get_option( 'wc_settings_prdctfltr_use_ajax', 'no' ) == 'yes' ) {
				$js_wc_prdctfltr = true;
			}
		}
		$sticky_header      = porto_get_meta_value( 'sticky_header' );
		$show_sticky_header = false;
		if ( 'no' !== $sticky_header ) {
			if ( 'yes' === $sticky_header ) {
				$show_sticky_header = true;
			} elseif ( $porto_settings['enable-sticky-header'] ) {
				$show_sticky_header = true;
			} elseif ( wp_is_mobile() && ( $porto_settings['enable-sticky-header-tablet'] || $porto_settings['enable-sticky-header-mobile'] ) ) {
				$show_sticky_header = true;
			}
		}

		global $porto_product_layout;

		$porto_vars = array(
			'rtl'                       => esc_js( is_rtl() ? true : false ),
			'theme_url'                 => esc_js( get_theme_file_uri() ),
			'ajax_url'                  => esc_js( admin_url( 'admin-ajax.php' ) ),
			'change_logo'               => esc_js( $porto_settings['change-header-logo'] ),
			'container_width'           => esc_js( $porto_settings['container-width'] ),
			'grid_gutter_width'         => esc_js( $porto_settings['grid-gutter-width'] ),
			'show_sticky_header'        => esc_js( $show_sticky_header ),
			'show_sticky_header_tablet' => esc_js( $porto_settings['enable-sticky-header-tablet'] ),
			'show_sticky_header_mobile' => esc_js( $porto_settings['enable-sticky-header-mobile'] ),
			'ajax_loader_url'           => esc_js( str_replace( array( 'http:', 'https:' ), array( '', '' ), PORTO_URI . '/images/ajax-loader@2x.gif' ) ),
			'category_ajax'             => esc_js( $porto_settings['category-ajax'] ),
			'prdctfltr_ajax'            => esc_js( $js_wc_prdctfltr ),
			'slider_loop'               => esc_js( $porto_settings['slider-loop'] ),
			'slider_autoplay'           => esc_js( $porto_settings['slider-autoplay'] ),
			'slider_autoheight'         => esc_js( $porto_settings['slider-autoheight'] ),
			'slider_speed'              => esc_js( $porto_settings['slider-speed'] ),
			'slider_nav'                => esc_js( $porto_settings['slider-nav'] ),
			'slider_nav_hover'          => esc_js( $porto_settings['slider-nav-hover'] ),
			'slider_margin'             => esc_js( $porto_settings['slider-margin'] ),
			'slider_dots'               => esc_js( $porto_settings['slider-dots'] ),
			'slider_animatein'          => esc_js( $porto_settings['slider-animatein'] ),
			'slider_animateout'         => esc_js( $porto_settings['slider-animateout'] ),
			'product_thumbs_count'      => esc_js( $porto_settings['product-thumbs-count'] ),
			'product_zoom'              => isset( $porto_product_layout ) && ( 'extended' === $porto_product_layout || 'full_width' === $porto_product_layout ) ? 0 : esc_js( $porto_settings['product-zoom'] ),
			'product_zoom_mobile'       => esc_js( $porto_settings['product-zoom-mobile'] ),
			'product_image_popup'       => esc_js( $porto_settings['product-image-popup'] ),
			'zoom_type'                 => esc_js( $porto_settings['zoom-type'] ),
			'zoom_scroll'               => esc_js( $porto_settings['zoom-scroll'] ),
			'zoom_lens_size'            => esc_js( $porto_settings['zoom-lens-size'] ),
			'zoom_lens_shape'           => esc_js( $porto_settings['zoom-lens-shape'] ),
			'zoom_contain_lens'         => esc_js( $porto_settings['zoom-contain-lens'] ),
			'zoom_lens_border'          => esc_js( $porto_settings['zoom-lens-border'] ),
			'zoom_border_color'         => esc_js( $porto_settings['zoom-border-color'] ),
			'zoom_border'               => esc_js( 'inner' == $porto_settings['zoom-type'] ? 0 : $porto_settings['zoom-border'] ),
			'screen_lg'                 => esc_js( (int) $porto_settings['container-width'] + $porto_settings['grid-gutter-width'] ),
			/* translators: %url%: Magnific Popup Counter Error Url */
			'mfp_counter'               => esc_js( __( '%curr% of %total%', 'porto' ) ),
			/* translators: %url%: Magnific Popup Ajax Error Url */
			'mfp_img_error'             => esc_js( __( '<a href="%url%">The image</a> could not be loaded.', 'porto' ) ),
			/* translators: %url%: Magnific Popup Ajax Error Url */
			'mfp_ajax_error'            => esc_js( __( '<a href="%url%">The content</a> could not be loaded.', 'porto' ) ),
			'popup_close'               => esc_js( __( 'Close', 'porto' ) ),
			'popup_prev'                => esc_js( __( 'Previous', 'porto' ) ),
			'popup_next'                => esc_js( __( 'Next', 'porto' ) ),
			'request_error'             => esc_js( __( 'The requested content cannot be loaded.<br/>Please try again later.', 'porto' ) ),
			'loader_text'               => esc_js( __( 'Loading...', 'porto' ) ),
			'submenu_back'              => esc_js( __( 'Back', 'porto' ) ),
			'porto_nonce'               => wp_create_nonce( 'porto-nonce' ),
			'use_skeleton_screen'       => ! empty( $porto_settings['show-skeleton-screen'] ) ? array_map( 'esc_js', $porto_settings['show-skeleton-screen'] ) : array(),
		);

		if ( ! empty( $porto_settings['show-skeleton-screen'] ) && in_array( 'quickview', $porto_settings['show-skeleton-screen'] ) && class_exists( 'Woocommerce' ) && $porto_settings['product-quickview'] ) {
			$porto_vars['quickview_skeleton'] = '<div class="quickview-wrap skeleton-body product"><div class="row"><div class="col-lg-6 summary-before"></div><div class="col-lg-6 summary entry-summary"></div></div></div>';
		}
		if ( ! empty( $porto_settings_optimize['lazyload_menu'] ) ) {
			$porto_vars['lazyload_menu'] = esc_js( $porto_settings_optimize['lazyload_menu'] );
		}
		if ( class_exists( 'Woocommerce' ) && is_product() ) {
			$porto_vars['pre_order'] = ! empty( $porto_settings['woo-pre-order'] );
		}
		wp_localize_script(
			'porto-theme',
			'js_porto_vars',
			$porto_vars
		);
	}
}

function porto_admin_css() {
	wp_deregister_style( 'font-awesome' );
	wp_dequeue_style( 'font-awesome' );
	wp_dequeue_style( 'yith-wcwl-font-awesome' );
	if ( wp_style_is( 'rs-icon-set-fa-icon-', 'enqueued' ) ) {
		// wp_dequeue_style( 'rs-icon-set-fa-icon-' );
	}

	wp_enqueue_style( 'font-awesome', PORTO_CSS . '/font-awesome.min.css', false, PORTO_VERSION, 'all' );

	wp_dequeue_style( 'bsf-Simple-Line-Icons' );
	wp_deregister_style( 'simple-line-icons' );
	wp_dequeue_style( 'simple-line-icons' );

	// simple line icon font
	wp_register_style( 'simple-line-icons', PORTO_CSS . '/Simple-Line-Icons/Simple-Line-Icons.css', false, PORTO_VERSION, 'all' );

	global $pagenow;
	if ( 'post-new.php' == $pagenow || 'post.php' == $pagenow ) {
		// porto icon font
		wp_enqueue_style( 'porto-font', PORTO_CSS . '/Porto-Font/Porto-Font.css', false, PORTO_VERSION, 'all' );
		wp_enqueue_style( 'simple-line-icons' );
	} elseif ( isset( $_GET['page'] ) && 'themes.php' == $pagenow && 'porto_settings' == $_GET['page'] ) {
		wp_enqueue_style( 'porto-admin-fonts', '//fonts.googleapis.com/css?family=Poppins%3A400%2C500%2C600%2C700' );
		wp_enqueue_style( 'simple-line-icons' );
		wp_enqueue_style( 'porto_theme_options', PORTO_CSS . '/admin_theme_options.css', false, PORTO_VERSION, 'all' );
	} elseif ( isset( $_GET['page'] ) && 'admin.php' == $pagenow && 0 === strpos( $_GET['page'], 'porto' ) ) {
		wp_enqueue_style( 'porto-admin-fonts', '//fonts.googleapis.com/css?family=Poppins%3A400%2C500%2C700' );
	}

	// wp default styles
	// admin style
	wp_enqueue_style( 'porto_admin', PORTO_CSS . '/admin.min.css', false, PORTO_VERSION, 'all' );
	wp_enqueue_style( 'porto_admin_bar', PORTO_CSS . '/admin_bar.css', false, PORTO_VERSION, 'all' );
	porto_enqueue_revslider_css();
}

function porto_admin_scripts() {
	if ( function_exists( 'add_thickbox' ) ) {
		add_thickbox();
	}
	wp_enqueue_media();
	global $pagenow;
	if ( 'themes.php' == $pagenow && isset( $_GET['page'] ) && 'porto_settings' === $_GET['page'] && defined( 'WPB_VC_VERSION' ) && ! wp_script_is( 'vc_waypoints', 'registered' ) ) {
		wp_register_script( 'vc_waypoints', vc_asset_url( 'lib/vc_waypoints/vc-waypoints.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
		wp_enqueue_script( 'vc_waypoints' );
	}
	// admin script
	wp_register_script( 'porto-admin', PORTO_JS . '/admin/admin.min.js', array( 'common', 'jquery', 'media-upload', 'thickbox', 'wp-color-picker' ), PORTO_VERSION, true );
	wp_enqueue_script( 'porto-admin' );

	$admin_vars = array(
		'import_options_msg' => esc_js( __( 'If you want to import demo, please backup current theme options in "Import / Export" section before import. Do you want to import demo?', 'porto' ) ),
		'theme_option_url'   => esc_url( admin_url( 'themes.php?page=porto_settings' ) ),
	);
	if ( in_array( $pagenow, array( 'themes.php', 'customize.php' ) ) ) {
		$admin_vars['options_search_text']    = esc_js( __( 'Search for...', 'porto' ) );
		$admin_vars['header_default_options'] = json_encode( porto_header_types_default_options() );
	}

	wp_localize_script( 'porto-admin', 'js_porto_admin_vars', $admin_vars );
}

function porto_enqueue_revslider_css() {
	global $porto_settings;
	$style = '';
	if ( $porto_settings['skin-color'] ) {
		$style = '.tparrows:before{color:' . esc_html( $porto_settings['skin-color'] ) . ';text-shadow:0 0 3px #fff;}';
	}
	$style .= '.revslider-initialised .tp-loader{z-index:18;}';
	wp_add_inline_style( 'rs-plugin-settings', $style );
}
// retrieves the attachment ID from the file URL
function porto_get_image_id( $image_url ) {
	global $wpdb;
	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid=%s", $image_url ) );
	if ( isset( $attachment[0] ) ) {
		return $attachment[0];
	} else {
		return false;
	}
}
// gravityform notifications
add_filter( 'gform_validation_message', 'porto_gform_validation_message', 10, 2 );
function porto_gform_validation_message( $message, $form ) {
	return '<div class="alert alert-danger br-normal">' . porto_strip_script_tags( $message ) . '</div>';
}
add_filter( 'gform_confirmation', 'porto_gform_confirmation', 10, 4 );
function porto_gform_confirmation( $confirmation, $form, $entry, $ajax ) {
	if ( is_array( $confirmation ) ) {
		return $confirmation;
	}
	return '<div class="alert alert-success br-normal">' . $confirmation . '</div>';
}

// Fix for PHP Fatal error:  Call to undefined function YIT_Pointers() in \plugins\yith-woocommerce-wishlist\includes\class.yith-wcwl-admin-init.php
if ( function_exists( 'yith_wishlist_constructor' ) && ! class_exists( 'YIT_Pointers' ) ) {
	require_once PORTO_DIR . '/woocommerce/yit-pointers.php';
}

// Load Revslider Icon Fonts
if ( ! function_exists( 'porto_revslider_icon_fonts' ) ) :
	function porto_revslider_icon_fonts() {
		global $pe_7s_var;
		if ( $pe_7s_var ) {
			echo "<link rel='stylesheet' property='stylesheet' id='rs-icon-set-pe-7s-css' href='" . RS_PLUGIN_URL . "public/assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css' type='text/css' media='all' />\n";
		}
	}
endif;

/* Elementor Plugin */
if ( defined( 'ELEMENTOR_VERSION' ) ) {
	add_action( 'elementor/editor/after_enqueue_styles', 'porto_elementor_editor_styles' );

	function porto_elementor_editor_styles() {
		// porto icon font
		wp_enqueue_style( 'porto-font', PORTO_CSS . '/Porto-Font/Porto-Font.css', false, PORTO_VERSION, 'all' );
		// simple line icon font
		wp_dequeue_style( 'bsf-Simple-Line-Icons' );
		wp_deregister_style( 'simple-line-icons' );
		wp_dequeue_style( 'simple-line-icons' );
		wp_enqueue_style( 'simple-line-icons', PORTO_CSS . '/Simple-Line-Icons/Simple-Line-Icons.css', false, PORTO_VERSION, 'all' );
	}
}

/* Dequeue duplicated font awesome css */
if ( is_admin() && defined( 'WPB_VC_VERSION' ) ) {
	add_action( 'admin_print_scripts-post.php', 'porto_admin_dequeue_duplicated_css', 20 );
	add_action( 'admin_print_scripts-post-new.php', 'porto_admin_dequeue_duplicated_css', 20 );
	add_action( 'vc_base_register_admin_css', 'porto_admin_dequeue_duplicated_css', 20 );
	add_action( 'vc_frontend_editor_render', 'porto_admin_dequeue_duplicated_css', 20 );

	function porto_admin_dequeue_duplicated_css() {
		if ( wp_style_is( 'vc_font_awesome_5', 'enqueued' ) ) {
			wp_dequeue_style( 'vc_font_awesome_5' );
		}
	}
}
