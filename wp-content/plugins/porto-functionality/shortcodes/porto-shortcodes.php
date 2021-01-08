<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class PortoShortcodesClass {

	public static $shortcodes = array(
		'porto_toggles',
		'porto_block',
		'porto_container',
		'porto_animation',
		'porto_carousel',
		'porto_carousel_item',
		'porto_testimonial',
		'porto_content_box',
		'porto_image_frame',
		'porto_preview_image',
		'porto_feature_box',
		'porto_lightbox_container',
		'porto_lightbox',
		'porto_blockquote',
		'porto_tooltip',
		'porto_popover',
		'porto_grid_container',
		'porto_grid_item',
		'porto_links_block',
		'porto_links_item',
		'porto_recent_posts',
		'porto_blog',
		'porto_recent_portfolios',
		'porto_portfolios',
		'porto_portfolios_category',
		'porto_recent_members',
		'porto_members',
		'porto_faqs',
		'porto_concept',
		'porto_map_section',
		'porto_history',
		'porto_diamonds',
		'porto_section',
		'porto_price_boxes',
		'porto_price_box',
		'porto_sort_filters',
		'porto_sort_filter',
		'porto_sort_container',
		'porto_sort_item',
		'porto_sticky',
		'porto_sticky_nav',
		'porto_sticky_nav_link',
		'porto_schedule_timeline_container',
		'porto_schedule_timeline_item',
		'porto_experience_timeline_container',
		'porto_experience_timeline_item',
		'porto_floating_menu_container',
		'porto_floating_menu_item',
		'porto_events',
		'porto_sidebar_menu',
		/* 4.0 shortcodes */
		'porto_icon',
		'porto_ultimate_heading',
		'porto_info_box',
		'porto_stat_counter',
		'porto_buttons',
		'porto_ultimate_content_box',
		'porto_google_map',
		'porto_icons',
		'porto_single_icon',
		'porto_countdown',
		'porto_ultimate_carousel',
		'porto_fancytext',
		'porto_modal',
		'porto_carousel_logo',
		'porto_info_list',
		'porto_info_list_item',
		'porto_interactive_banner',
		'porto_interactive_banner_layer',
		'porto_page_header',
		'porto_section_scroll',
		'porto_share',
		'porto_360degree_image_viewer',
		/* 5.0 shortcodes */
		'porto_heading',
		'porto_button',
		'porto_hotspot',
	);

	public static $woo_shortcodes = array( 'porto_recent_products', 'porto_featured_products', 'porto_sale_products', 'porto_best_selling_products', 'porto_top_rated_products', 'porto_products', 'porto_product_category', 'porto_product_attribute', 'porto_product', 'porto_product_categories', 'porto_one_page_category_products', 'porto_product_attribute_filter', 'porto_products_filter', 'porto_widget_woo_products', 'porto_widget_woo_top_rated_products', 'porto_widget_woo_recently_viewed', 'porto_widget_woo_recent_reviews', 'porto_widget_woo_product_tags' );

	public static $gutenberg_blocks = array(
		'porto_blog',
		'porto_button',
		'porto_carousel',
		'porto_google_map',
		'porto_grid_container',
		'porto_grid_item',
		'porto_heading',
		'porto_icons',
		'porto_info_box',
		'porto_interactive_banner',
		'porto_interactive_banner_layer',
		'porto_recent_posts',
		//'porto_section',
		'porto_single_icon',
		'porto_stat_counter',
		'porto_ultimate_heading',
	);

	public function __construct() {

		add_action( 'init', array( $this, 'init_shortcodes' ) );
		add_action( 'plugins_loaded', array( $this, 'add_shortcodes' ) );

		if ( is_admin() ) {
			add_action( 'enqueue_block_editor_assets', array( $this, 'add_editor_assets' ), 999 );
			add_filter( 'block_categories', array( $this, 'porto_blocks_categories' ), 10, 2 );

			add_action( 'wp_ajax_porto_load_creative_layout_style', array( $this, 'load_creative_layout_style' ) );
			add_action( 'wp_ajax_nopriv_porto_load_creative_layout_style', array( $this, 'load_creative_layout_style' ) );
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_css_js' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_frontend_css_js' ) );
		add_filter( 'the_content', array( $this, 'format_shortcodes' ) );
		add_filter( 'widget_text', array( $this, 'format_shortcodes' ) );

		add_action( 'init', array( $this, 'init_vc_editor_iframe' ), 11 );
		add_action( 'admin_init', array( $this, 'init_vc_editor' ) );
	}

	public function porto_blocks_categories( $categories, $post ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'porto',
					'title' => __( 'Porto', 'porto-functionality' ),
					'icon'  => '',
				),
			)
		);
	}

	// load frontend css and js
	public function load_frontend_css_js() {
		$rtl_suffix = is_rtl() ? '_rtl' : '';
		wp_register_style( 'jquery-flipshow', PORTO_SHORTCODES_URL . 'assets/css/jquery.flipshow' . $rtl_suffix . '.min.css' );

		if ( ! is_404() && ! is_search() ) {
			global $post;
			if ( $post ) {
				$use_google_map = get_post_meta( $post->ID, 'porto_page_use_google_map_api', true );
				if ( '1' === $use_google_map || stripos( $post->post_content, '[porto_google_map' ) || stripos( $post->post_content, 'porto/porto-google-map' ) ) {
					wp_enqueue_script( 'googleapis' );
				}
				if ( stripos( $post->post_content, '[porto_concept ' ) ) {
					wp_enqueue_style( 'jquery-flipshow' );
				}
			}
		}

		wp_register_script( 'jquery-flipshow', PORTO_SHORTCODES_URL . 'assets/js/jquery.flipshow.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'porto_shortcodes_flipshow_loader_js', PORTO_SHORTCODES_URL . 'assets/js/jquery.flipshow-loader.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'countdown', PORTO_SHORTCODES_URL . 'assets/js/countdown.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'porto_shortcodes_countdown_loader_js', PORTO_SHORTCODES_URL . 'assets/js/countdown-loader.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'countup', PORTO_SHORTCODES_URL . 'assets/js/countup.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'porto_shortcodes_countup_loader_js', PORTO_SHORTCODES_URL . 'assets/js/countup-loader.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'porto_shortcodes_ultimate_carousel_loader_js', PORTO_SHORTCODES_URL . 'assets/js/ultimate-carousel-loader.min.js', array( 'jquery', 'jquery-slick' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'porto_shortcodes_map_loader_js', PORTO_SHORTCODES_URL . 'assets/js/map-loader.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'porto_section_scroll_js', PORTO_SHORTCODES_URL . 'assets/js/porto-section-scroll.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( 'porto_word_rotator', PORTO_SHORTCODES_URL . 'assets/js/porto-word-rotator.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_register_script( '360-degrees-product-viewer', PORTO_SHORTCODES_URL . 'assets/js/360-degrees-product-viewer.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );

		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			wp_register_script( 'porto_shortcodes_frontend-editor', PORTO_SHORTCODES_URL . 'assets/js/porto-shortcodes-frontend-editor.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
			wp_enqueue_script( 'porto_shortcodes_frontend-editor' );
		}
	}

	// load css and js
	public function load_admin_css_js() {
		wp_register_style( 'porto_shortcodes_admin', PORTO_SHORTCODES_URL . 'assets/css/admin.css', array(), PORTO_SHORTCODES_VERSION );
		wp_enqueue_style( 'porto_shortcodes_admin' );
		wp_register_style( 'simple-line-icons', PORTO_SHORTCODES_URL . 'assets/css/Simple-Line-Icons/Simple-Line-Icons.css' );
		wp_enqueue_style( 'simple-line-icons' );

		global $pagenow;
		if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {

			wp_register_style( 'bootstrap-datetimepicker-admin', PORTO_SHORTCODES_URL . 'assets/css/bootstrap-datetimepicker-admin' . ( WP_DEBUG ? '' : '.min' ) . '.css' );
			wp_enqueue_style( 'bootstrap-datetimepicker-admin' );
		}
	}

	/**
	 * Enqueue styles and scripts for gutenberg blocks
	 *
	 * @since 1.2
	 */
	public function add_editor_assets() {
		wp_enqueue_script( 'owl.carousel', PORTO_SHORTCODES_URL . 'assets/js/owl.carousel.min.js', array( 'jquery' ), '2.3.4', false );
		wp_enqueue_style( 'owl.carousel', PORTO_SHORTCODES_URL . 'assets/css/owl.carousel.min.css' );

		wp_enqueue_script( 'isotope', PORTO_SHORTCODES_URL . 'assets/js/isotope.pkgd.min.js', array( 'jquery' ), '3.0.6', false );

		wp_enqueue_script( 'porto_blocks', PORTO_SHORTCODES_URL . 'assets/blocks/blocks.min.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-data', 'wp-editor' ), PORTO_SHORTCODES_VERSION, true );

		$nav_types = array();
		foreach ( porto_sh_commons( 'carousel_nav_types' ) as $value => $key ) {
			$nav_types[] = array(
				'label' => str_replace( '&amp;', '&', esc_js( $value ) ),
				'value' => esc_js( $key ),
			);
		}
		$product_layouts = array();
		foreach ( porto_sh_commons( 'products_addlinks_pos' ) as $value => $key ) {
			$product_layouts[] = array(
				'label' => str_replace( '&amp;', '&', esc_js( $value ) ),
				'value' => esc_js( $key ),
			);
		}
		$image_sizes = array();
		foreach ( porto_sh_commons( 'image_sizes' ) as $value => $key ) {
			$image_sizes[] = array(
				'label' => str_replace( '&amp;', '&', esc_js( $value ) ),
				'value' => esc_js( $key ),
			);
		}

		$masonry_layouts  = porto_sh_commons( 'masonry_layouts' );
		$creative_layouts = array();
		for ( $index = 1; $index <= count( $masonry_layouts ); $index++ ) {
			$layout = porto_creative_grid_layout( '' . $index );
			if ( is_array( $layout ) ) {
				$creative_layouts[ $index ] = array();
				foreach ( $layout as $pl ) {
					$creative_layouts[ $index ][] = esc_js( 'grid-col-' . $pl['width'] . ' grid-col-md-' . $pl['width_md'] . ( isset( $pl['width_lg'] ) ? ' grid-col-lg-' . $pl['width_lg'] : '' ) . ( isset( $pl['height'] ) ? ' grid-height-' . $pl['height'] : '' ) );
				}
			}
		}

		global $porto_settings;
		wp_localize_script(
			'porto_blocks',
			'porto_block_vars',
			array(
				'ajax_url'           => esc_js( admin_url( 'admin-ajax.php' ) ),
				'nonce'              => wp_create_nonce( 'porto-nonce' ),
				'product_show_cats'  => esc_js( $porto_settings['product-categories'] ),
				'product_show_wl'    => esc_js( class_exists( 'YITH_WCWL' ) && $porto_settings['product-wishlist'] ),
				'product_type'       => esc_js( $porto_settings['category-addlinks-pos'] ),
				'product_layouts'    => $product_layouts,
				'carousel_nav_types' => $nav_types,
				'creative_layouts'   => $creative_layouts,
				'image_sizes'        => $image_sizes,
			)
		);

		porto_google_map_script();
		wp_enqueue_script( 'googleapis' );
	}

	// Add buttons to tinyMCE
	public function init_shortcodes() {
		if ( function_exists( 'get_plugin_data' ) ) {
			$plugin = get_plugin_data( dirname( dirname( __FILE__ ) ) . '/porto-functionality.php' );
			define( 'PORTO_SHORTCODES_VERSION', $plugin['Version'] );
		} else {
			define( 'PORTO_SHORTCODES_VERSION', '' );
		}

		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_js' ) );
			add_filter( 'mce_buttons', array( $this, 'register_tinymce_buttons' ) );
		}
	}

	public function add_tinymce_js( $plugin_array ) {
		if ( get_bloginfo( 'version' ) >= 3.9 ) {
			$plugin_array['shortcodes'] = PORTO_SHORTCODES_URL . 'assets/tinymce/shortcodes_4.js';
		} else {
			$plugin_array['shortcodes'] = PORTO_SHORTCODES_URL . 'assets/tinymce/shortcodes.js';
		}

		$plugin_array['porto_shortcodes'] = PORTO_SHORTCODES_URL . 'assets/tinymce/porto_shortcodes.js';
		return $plugin_array;
	}

	public function register_tinymce_buttons( $buttons ) {
		array_push( $buttons, 'porto_shortcodes_button' );
		return $buttons;
	}

	// Add shortcodes
	public function add_shortcodes() {

		require_once( PORTO_SHORTCODES_LIB . 'functions.php' );
		$is_wpb       = defined( 'WPB_VC_VERSION' );
		$is_gutenberg = function_exists( 'register_block_type' );
		foreach ( $this::$shortcodes as $shortcode ) {
			$callback = function( $atts, $content = null ) use ( $shortcode ) {
				ob_start();
				$template = porto_shortcode_template( $shortcode );
				if ( $template ) {
					include $template;
				}
				return ob_get_clean();
			};
			add_shortcode( $shortcode, $callback );
			if ( ( $is_wpb && ! in_array( $shortcode, array( 'porto_button', 'porto_heading' ) ) ) || in_array( $shortcode, array( 'porto_google_map', 'porto_page_header' ) ) || ( $is_gutenberg && 'porto_section' == $shortcode ) ) {
				include_once( PORTO_SHORTCODES_PATH . $shortcode . '.php' );
			}
			if ( in_array( $shortcode, $this::$gutenberg_blocks ) && $is_gutenberg ) {
				register_block_type(
					'porto/' . str_replace( '_', '-', $shortcode ),
					array(
						'editor_script'   => 'porto_blocks',
						'render_callback' => $callback,
					)
				);
			}
		}
		if ( class_exists( 'Woocommerce' ) ) {
			foreach ( $this::$woo_shortcodes as $woo_shortcode ) {
				require_once( PORTO_SHORTCODES_WOO_PATH . $woo_shortcode . '.php' );
			}
		}
	}

	// Format shortcodes content
	public function format_shortcodes( $content ) {
		$block = join( '|', $this::$shortcodes );
		// opening tag
		$content = preg_replace( "/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", '[$2$3]', $content );
		// closing tag
		$content = preg_replace( "/(<p>)?\[\/($block)](<\/p>|<br \/>)/", '[/$2]', $content );

		$woo_block = join( '|', $this::$woo_shortcodes );
		// opening tag
		$content = preg_replace( "/(<p>)?\[($woo_block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", '[$2$3]', $content );
		// closing tag
		$content = preg_replace( "/(<p>)?\[\/($woo_block)](<\/p>|<br \/>)/", '[/$2]', $content );

		return $content;
	}

	public function load_creative_layout_style() {
		check_ajax_referer( 'porto-nonce', 'nonce' );
		if ( ! empty( $_POST['layout'] ) && ! empty( $_POST['grid_height'] ) && ! empty( $_POST['selector'] ) ) {
			$layout_index = $_POST['layout'];
			$grid_height  = $_POST['grid_height'];
			$spacing      = ! empty( $_POST['spacing'] ) ? intval( $_POST['spacing'] ) : false;

			$grid_height_number = trim( preg_replace( '/[^0-9]/', '', $grid_height ) );
			$unit               = trim( str_replace( $grid_height_number, '', $grid_height ) );

			echo '<style scope="scope" data-id="' . esc_attr( $layout_index ) . '">';
			porto_creative_grid_style( porto_creative_grid_layout( $layout_index ), intval( $grid_height_number ), esc_html( $_POST['selector'] ), $spacing, false, $unit, isset( $_POST['item_selector'] ) ? esc_html( $_POST['item_selector'] ) : '.porto-grid-item' );
			echo '</style>';
		}
		die();
	}

	public function init_vc_editor() {
		if ( ! defined( 'WPB_VC_VERSION' ) ) {
			return;
		}
		add_action(
			'vc_frontend_editor_render',
			function() {
				wp_enqueue_style( 'porto-vc-editor-fonts', '//fonts.googleapis.com/css?family=Poppins%3A300%2C400%2C500%2C600%2C700' );
				wp_enqueue_style( 'porto-vc-editor', PORTO_SHORTCODES_URL . 'assets/css/porto-vc-editor.css', false, PORTO_SHORTCODES_VERSION );
			}
		);

		add_action(
			'vc_backend_editor_render',
			function() {
				$screen    = get_current_screen();
				$post_type = isset( $screen->post_type ) ? $screen->post_type : false;
				if ( ! $post_type || ! function_exists( 'porto_is_gutenberg' ) || ! porto_is_gutenberg( $post_type ) ) {
					wp_enqueue_style( 'porto-vc-editor-fonts', '//fonts.googleapis.com/css?family=Poppins%3A400%2C500%2C600%2C700' );
					wp_enqueue_style( 'porto-vc-editor', PORTO_SHORTCODES_URL . 'assets/css/porto-vc-editor.css', false, PORTO_SHORTCODES_VERSION );
					wp_enqueue_style( 'porto-vc-editor-iframe', PORTO_SHORTCODES_URL . 'assets/css/porto-vc-editor-iframe.css', false, PORTO_SHORTCODES_VERSION );
				}
			}
		);
	}

	public function init_vc_editor_iframe() {
		if ( ! defined( 'WPB_VC_VERSION' ) ) {
			return;
		}
		add_action(
			'vc_load_iframe_jscss',
			function() {
				global $porto_settings;
				$poppins_loaded = false;
				$fonts          = porto_settings_google_fonts();
				foreach ( $fonts as $option => $weights ) {
					if ( isset( $porto_settings[ $option . '-font' ]['google'] ) && 'false' !== $porto_settings[ $option . '-font' ]['google'] ) {
						$font = isset( $porto_settings[ $option . '-font' ]['font-family'] ) ? urlencode( $porto_settings[ $option . '-font' ]['font-family'] ) : '';
						if ( 'Poppins' == $font ) {
							$poppins_loaded = true;
							break;
						}
					}
				}
				if ( ! $poppins_loaded ) {
					wp_enqueue_style( 'porto-vc-front-editor-fonts', '//fonts.googleapis.com/css?family=Poppins%3A400%2C700' );
				} else {
					wp_enqueue_style( 'porto-vc-front-editor-fonts', '//fonts.googleapis.com/css?family=Poppins%3A700' );
				}
				wp_enqueue_style( 'porto-vc-editor-iframe', PORTO_SHORTCODES_URL . 'assets/css/porto-vc-editor-iframe.css', false, PORTO_SHORTCODES_VERSION );
			}
		);
	}
}

// Finally initialize code
new PortoShortcodesClass();

include_once( dirname( PORTO_SHORTCODES_PATH ) . '/lib/blocks/porto-blocks.php' );
