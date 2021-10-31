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
		/* 6.0 shortcodes */
		'porto_svg_floating',
		'porto_social_icons',
		'porto_image_comparison',
		'porto_image_gallery',
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

	/**
	 * Dimension Patterns
	 *
	 * @since 6.1.0
	 * @var array $dimensions
	 */
	public static $dimensions = array(
		'top'    => '{{TOP}}',
		'right'  => '{{RIGHT}}',
		'bottom' => '{{BOTTOM}}',
		'left'   => '{{LEFT}}',
	);

	/**
	 * product args to sort by multiple fields
	 *
	 * @since 6.2.0
	 */
	private $product_mult_sort_args = '';

	public function __construct() {

		add_action( 'init', array( $this, 'init_shortcodes' ) );
		add_action( 'plugins_loaded', array( $this, 'add_shortcodes' ) );

		if ( is_admin() ) {
			add_action( 'enqueue_block_editor_assets', array( $this, 'add_editor_assets' ), 999 );
			add_filter( 'block_categories_all', array( $this, 'porto_blocks_categories' ), 10, 1 );

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

	public function porto_blocks_categories( $categories ) {
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
		wp_register_script( 'jquery-event-move', PORTO_SHORTCODES_URL . 'assets/js/jquery.event.move.min.js', array( 'jquery' ), '2.0.0', true );
		wp_register_script( 'porto-image-comparison', PORTO_SHORTCODES_URL . 'assets/js/image-comparison.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );

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
		wp_enqueue_script( 'porto_wpb_addon', PORTO_SHORTCODES_URL . 'assets/js/porto-wpb-addon.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
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
		if ( function_exists( 'porto_include_google_font' ) ) {
			$fonts = porto_include_google_font();
		}
		wp_enqueue_script( 'owl.carousel', PORTO_SHORTCODES_URL . 'assets/js/owl.carousel.min.js', array( 'jquery' ), '2.3.4', false );
		wp_enqueue_style( 'owl.carousel', PORTO_SHORTCODES_URL . 'assets/css/owl.carousel.min.css' );

		wp_enqueue_script( 'isotope', PORTO_SHORTCODES_URL . 'assets/js/isotope.pkgd.min.js', array( 'jquery' ), '3.0.6', false );

		wp_enqueue_script( 'select2', PORTO_SHORTCODES_URL . 'assets/js/select2.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
		wp_enqueue_style( 'select2', PORTO_SHORTCODES_URL . 'assets/css/select2.min.css' );

		wp_enqueue_script( 'porto_blocks', PORTO_SHORTCODES_URL . 'assets/blocks/blocks.min.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-data'/*, 'wp-editor'*/ ), PORTO_SHORTCODES_VERSION, true );

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
		$portfolio_layouts = array();
		foreach ( porto_sh_commons( 'portfolio_layout' ) as $value => $key ) {
			$portfolio_layouts[] = array(
				'label' => str_replace( '&amp;', '&', esc_js( $value ) ),
				'value' => esc_js( $key ),
			);
		}
		$member_layouts = array();
		foreach ( porto_sh_commons( 'member_view' ) as $value => $key ) {
			$member_layouts[] = array(
				'label' => str_replace( '&amp;', '&', esc_js( $value ) ),
				'value' => esc_js( $key ),
			);
		}
		$easing_methods = array();
		foreach ( porto_sh_commons( 'easing_methods' ) as $value => $key ) {
			$easing_methods[] = array(
				'label' => str_replace( '&amp;', '&', esc_js( $value ) ),
				'value' => esc_js( $key ),
			);
		}
		$divider_type = array();
		foreach ( porto_sh_commons( 'divider_type' ) as $value => $key ) {
			$divider_type[] = array(
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
		$orderby_values = array();
		foreach ( porto_vc_order_by() as $value => $key ) {
			if ( empty( $value ) ) {
				$orderby_values[] = array(
					'label' => esc_js( __( 'Default', 'porto-functionality' ) ),
					'value' => '',
				);
				continue;
			}
			$orderby_values[] = array(
				'label' => esc_js( $value ),
				'value' => esc_js( $key ),
			);
		}
		$sortby_values = array();
		foreach ( porto_vc_order_by() as $value => $key ) {
			$sortby_values[] = array(
				'label' => esc_js( $value ),
				'value' => esc_js( $key ),
			);
		}
		global $porto_settings;
		$status_values = array(
			array(
				'label' => __( 'All', 'porto-functionality' ),
				'value' => '',
			),
			array(
				'label' => __( 'Featured', 'porto-functionality' ),
				'value' => 'featured',
			),
			array(
				'label' => __( 'On Sale', 'porto-functionality' ),
				'value' => 'on_sale',
			),
		);
		if ( ! empty( $porto_settings['woo-pre-order'] ) ) {
			$status_values[] = array(
				'label' => __( 'Pre-Order', 'porto-functionality' ),
				'value' => 'pre_order',
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

		global $pagenow;
		$js_porto_block_vars = array(
			'ajax_url'           => esc_url( admin_url( 'admin-ajax.php' ) ),
			'site_url'           => esc_url( get_site_url( '' ) ),
			'nonce'              => wp_create_nonce( 'porto-nonce' ),
			'product_layouts'    => $product_layouts,
			'portfolio_layouts'  => $portfolio_layouts,
			'member_layouts'     => $member_layouts,
			'carousel_nav_types' => $nav_types,
			'creative_layouts'   => $creative_layouts,
			'easing_methods'     => $easing_methods,
			'divider_type'       => $divider_type,
			'shape_divider'      => porto_sh_commons( 'shape_divider' ),
			'image_sizes'        => $image_sizes,
			'shortcodes_url'     => esc_url( PORTO_SHORTCODES_URL ),
			'is_rtl'             => esc_js( is_rtl() ),
			'builder_type'       => 'post.php' == $pagenow && get_the_ID() ? esc_js( get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) : '',
			'orderby_values'     => $orderby_values,
			'sortby_values'      => $sortby_values,
			'status_values'      => $status_values,
		);
		if ( ! empty( $porto_settings ) ) {
			$js_porto_block_vars['product_show_cats'] = esc_js( $porto_settings['product-categories'] );
			$js_porto_block_vars['product_type']      = esc_js( $porto_settings['category-addlinks-pos'] );
			$js_porto_block_vars['product_show_wl']   = esc_js( class_exists( 'YITH_WCWL' ) && $porto_settings['product-wishlist'] );
		}
		if ( ! empty( $fonts ) ) {
			$js_porto_block_vars['googlefonts'] = array_map( 'esc_js', $fonts );
		}
		wp_localize_script(
			'porto_blocks',
			'porto_block_vars',
			$js_porto_block_vars
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
		$is_wpb       = defined( 'WPB_VC_VERSION' );
		$is_gutenberg = function_exists( 'register_block_type' );

		if ( $is_wpb ) {
			add_filter( 'vc_base_build_shortcodes_custom_css', array( $this, 'add_shortcodes_custom_css' ), 10, 2 );
		}
		//if ( is_admin() && function_exists( 'register_block_type' ) ) {
			require_once( dirname( PORTO_META_BOXES_PATH ) . '/elementor/restapi/ajaxselect2.php' );
		//}
		require_once( PORTO_SHORTCODES_LIB . 'functions.php' );
		foreach ( $this::$shortcodes as $shortcode ) {
			$callback = function( $atts, $content = null ) use ( $shortcode, $is_wpb ) {
				ob_start();
				$template = porto_shortcode_template( $shortcode );

				$internal_css = '';

				if ( $is_wpb ) {
					// Shortcode class
					$shortcode_class = '';
					$sc              = WPBMap::getShortCode( $shortcode );
					if ( ! empty( $sc['params'] ) ) {
						$shortcode_class = ' wpb_custom_' . self::get_global_hashcode( $atts, $shortcode, $sc['params'] );
					}
					// Frontend editor
					if ( isset( $_REQUEST['vc_editable'] ) && ( true == $_REQUEST['vc_editable'] ) ) {
						$style_array = $this->generate_shortcode_css( $shortcode, $atts );

						foreach ( $style_array as $key => $value ) {
							if ( 'responsive' == $key ) {
								$internal_css .= $value;
							} else {
								$internal_css .= $key . '{' . $value . '}';
							}
						}
					}
				}
				if ( $template ) {
					include $template;
				}
				$result = ob_get_clean();
				if ( $result && $internal_css ) {
					$first_tag_index = strpos( $result, '>' );
					if ( $first_tag_index ) {
						$result = substr( $result, 0, $first_tag_index + 1 ) . '<style>' . wp_strip_all_tags( $internal_css ) . '</style>' . substr( $result, $first_tag_index + 1 );
					}
				}
				return $result;
			};
			add_shortcode( $shortcode, $callback );
			if ( ( $is_wpb && ! in_array( $shortcode, array( 'porto_button', 'porto_heading' ) ) ) || in_array( $shortcode, array( 'porto_google_map', 'porto_page_header' ) ) || ( $is_gutenberg && in_array( $shortcode, array( 'porto_section', 'porto_sidebar_menu', 'porto_hotspot', 'porto_portfolios', 'porto_recent_portfolios', 'porto_members', 'porto_recent_members' ) ) ) ) {
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
			add_filter(
				'woocommerce_shortcode_products_query',
				function( $query_args, $attributes, $type ) {
					if ( 'products' == $type && empty( $_GET['orderby'] ) && ( is_array( $attributes['orderby'] ) || false !== strpos( $attributes['orderby'], '{' ) ) && ! empty( $attributes['orderby'] ) ) {
						if ( ! is_array( $attributes['orderby'] ) ) {
							$attributes['orderby'] = json_decode( html_entity_decode( $attributes['orderby'] ), true );
						}
						$query_args['orderby'] = $attributes['orderby'];
						
						if ( array_key_exists( 'price', $attributes['orderby'] ) || array_key_exists( 'price-desc', $attributes['orderby'] ) || array_key_exists( 'popularity', $attributes['orderby'] ) || array_key_exists( 'rating', $attributes['orderby'] ) ) {
							$final_args            = array( 'orderby' => '', 'join' => '' );
							$final_args            = WC()->query->order_by_price_desc_post_clauses( $final_args );
							$final_args['orderby'] = '';

							global $wpdb;

							foreach ( $attributes['orderby'] as $key => $value ) {
								if ( empty( $value ) ) {
									$value = 'DESC';
								}
								if ( ! empty( $final_args['orderby'] ) ) {
									$final_args['orderby'] .= ',';
								}
								if ( 'price' == $key ) {
									$final_args['orderby'] .= ' wc_product_meta_lookup.max_price ' . $value . ' ';
								} elseif ( 'popularity' == $key ) {
									$final_args['orderby'] .= ' wc_product_meta_lookup.total_sales ' . $value . ' ';
								} elseif ( 'rating' == $key ) {
									$final_args['orderby'] .= ' wc_product_meta_lookup.average_rating ' . $value . ', wc_product_meta_lookup.rating_count ' . $value . ' ';
								} else {
									$other_args = WC()->query->get_catalog_ordering_args( $key, $value );
									$other_args['orderby'] = explode( ' ', $other_args['orderby'] );
									foreach ( $other_args['orderby'] as $index => $other_orderby ) {
										if ( 'id' == $other_orderby || 'ID' == $other_orderby ) {
											$other_orderby = 'wc_product_meta_lookup.product_id';
										} elseif ( 'menu_order' == $other_orderby ) {
											$other_orderby = $wpdb->posts . '.menu_order';
										} elseif ( 'rand' == $other_orderby ) {
											$other_orderby = 'RAND()';
										} else {
											$other_orderby = $wpdb->posts . '.post_' . $other_orderby;
										}
										if ( $index ) {
											$final_args['orderby'] .= ',';
										}
										$final_args['orderby'] .= $other_orderby . ' ' . $other_args['order'] . ' ';
									}
								}
							}
							if ( ! array_key_exists( 'id', $attributes['orderby'] ) && ! array_key_exists( 'rand', $attributes['orderby'] ) ) {
								$final_args['orderby'] .= ', wc_product_meta_lookup.product_id DESC ';
							}
							$this->product_mult_sort_args = $final_args;
							add_filter( 'posts_clauses', array( $this, 'wc_multi_order' ) );
						}

					}
					return $query_args;
				},
				10,
				3
			);

			add_filter(
				'the_posts',
				function( $posts, $query ) {
					if ( 'product_query' !== $query->get( 'wc_query' ) ) {
						return $posts;
					}
					remove_filter( 'posts_clauses', array( $this, 'wc_multi_order' ) );
					return $posts;
				},
				10,
				2
			);
		}
	}

	/**
	 * Check Units
	 *
	 * @param string $value
	 *
	 * @return string
	 * @since 6.1.0
	 */
	function porto_check_units( $value ) {
		if ( ! preg_match( '/((^\d+(.\d+){0,1})|((-){0,1}.\d+))(px|%|em|rem|pt){0,1}$/', $value ) ) {
			if ( 'auto' == $value || 'inherit' == $value || 'initial' == $value || 'unset' == $value ) {
				return $value;
			}
			return false;
		} elseif ( is_numeric( $value ) ) {
			$value .= 'px';
		}
		return $value;
	}

	/**
	 * Add custom css of shortcodes
	 *
	 * @param  string $css
	 * @param  string $id
	 *
	 * @return string
	 * @since  6.1.0
	 */
	public function add_shortcodes_custom_css( $css, $id ) {
		$post = get_post( $id );

		$css_array = $this->parse_shortcodes_custom_css( $post->post_content );

		foreach ( $css_array as $key => $value ) {
			if ( 'responsive' == $key ) {
				if ( ! is_array( $value ) ) {
					$css .= $value;
				} else {
					$value = array_unique( $value );
					$css  .= implode( '', $value );
				}
			} else {
				if ( ! is_array( $value ) ) {
					$css .= $key . '{' . $value . '}';
				} else {
					$value = array_unique( $value );
					$css  .= $key . '{' . implode( '', $value ) . '}';
				}
			}
		}

		return $css;
	}

	/**
	 * Parse shortcodes custom css
	 *
	 * @param string $content
	 *
	 * @return array
	 * @since 6.1.0
	 */
	public function parse_shortcodes_custom_css( $content ) {
		$css = array();

		WPBMap::addAllMappedShortcodes();
		preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes );

		foreach ( $shortcodes[2] as $index => $tag ) {
			// Get attributes
			$atts = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );
			$css  = array_merge_recursive( $css, $this->generate_shortcode_css( $tag, $atts ) );
		}

		foreach ( $shortcodes[5] as $shortcode_content ) {
			$css = array_merge_recursive( $css, $this->parse_shortcodes_custom_css( $shortcode_content ) );
		}

		return $css;
	}

	/**
	 * Generate Shortcode CSS
	 *
	 * @param string $tag
	 * @param array $atts
	 *
	 * @return array
	 * @since 6.1.0
	 */
	public function generate_shortcode_css( $tag, $atts ) {
		$css = array();
		if ( defined( 'WPB_VC_VERSION' ) ) {
			$shortcode = WPBMap::getShortCode( $tag );

			if ( isset( $shortcode['params'] ) && ! empty( $shortcode['params'] ) ) {
				$shortcode_class = '.wpb_custom_' . self::get_global_hashcode( $atts, $tag, $shortcode['params'] );

				foreach ( $shortcode['params'] as $param ) {
					if ( isset( $param['selectors'] ) && ( isset( $atts[ $param['param_name'] ] ) || isset( $param['std'] ) ) ) {

						foreach ( $param['selectors'] as $key => $value ) {
							if ( isset( $param['std'] ) ) {
								$saved_value = $param['std'];
							}
							if ( isset( $atts[ $param['param_name'] ] ) ) {
								$saved_value = $atts[ $param['param_name'] ];
							}

							if ( 'porto_number' == $param['type'] && ! empty( $param['units'] ) && is_array( $param['units'] ) ) {
								$saved_value       = str_replace( '``', '"', $saved_value );
								$responsive_values = json_decode( $saved_value, true );
								if ( ! empty( $responsive_values['xl'] ) || ( isset( $responsive_values['xl'] ) && '0' === $responsive_values['xl'] ) ) {
									$saved_value = $responsive_values['xl'];
								} else {
									$saved_value = '';
								}
							} elseif ( 'porto_dimension' == $param['type'] ) {
								$saved_value      = str_replace( '``', '"', $saved_value );
								$dimension_values = json_decode( $saved_value, true );
							} elseif ( 'porto_typography' == $param['type'] ) {
								$saved_value = str_replace( '``', '"', $saved_value );
								$saved_value = json_decode( $saved_value, true );
								$typography  = '';
								if ( ! empty( $saved_value['family'] ) && 'Default' != $saved_value['family'] ) {
									if ( 'Inherit' == $saved_value['family'] ) {
										$typography .= 'font-family:inherit;';
									} else {
										$typography .= "font-family:'" . $saved_value['family'] . "';";
									}
								}
								if ( ! empty( $saved_value['variant'] ) ) {
									preg_match( '/^\d+|(regular)|(italic)/', $saved_value['variant'], $weight );
									if ( ! empty( $weight ) ) {
										if ( 'regular' == $weight[0] || 'italic' == $weight[0] ) {
											$weight[0] = 400;
										}
										$typography .= 'font-weight:' . $weight[0] . ';';
									}
									preg_match( '/(italic)/', $saved_value['variant'], $weight );
									if ( ! empty( $weight ) ) {
										$typography .= 'font-style:' . $weight[0] . ';';
									}
								}
								if ( ! empty( $saved_value['font_size'] ) && $this->porto_check_units( $saved_value['font_size'] ) ) {
									$typography .= 'font-size:' . $this->porto_check_units( $saved_value['font_size'] ) . ';';
								}
								if ( ! empty( $saved_value['letter_spacing'] ) || ( isset( $saved_value['letter_spacing'] ) && '0' === $saved_value['letter_spacing'] ) ) {
									$typography .= 'letter-spacing:' . $saved_value['letter_spacing'] . ';';
								}
								if ( ! empty( $saved_value['line_height'] ) || ( isset( $saved_value['line_height'] ) && '0' === $saved_value['line_height'] ) ) {
									$typography .= 'line-height:' . $saved_value['line_height'] . ';';
								}
								if ( ! empty( $saved_value['text_transform'] ) || ( isset( $saved_value['text_transform'] ) && '0' === $saved_value['text_transform'] ) ) {
									$typography .= 'text-transform:' . $saved_value['text_transform'] . ';';
								}
							}

							if ( ! empty( $param['units'] ) && is_array( $param['units'] ) ) {
								if ( empty( $responsive_values['unit'] ) ) {
									$value = str_replace( '{{UNIT}}', $param['units'][0], $value );
								} else {
									$value = str_replace( '{{UNIT}}', $responsive_values['unit'], $value );
								}
							}

							if ( ! empty( $param['responsive'] ) && $param['responsive'] ) {
								if ( isset( $param['std'] ) ) {
									$saved_value = $param['std'];
								}
								if ( isset( $atts[ $param['param_name'] ] ) ) {
									$saved_value = $atts[ $param['param_name'] ];
								}
								$saved_value       = str_replace( '``', '"', $saved_value );
								$key               = str_replace( '{{WRAPPER}}', $shortcode_class, $key );
								$responsive_values = json_decode( $saved_value, true );
								$style             = '';

								// Generate Responsive CSS
								global $porto_settings;
								$breakpoints = array(
									'lg' => isset( $porto_settings['container-width'] ) && isset( $porto_settings['grid-gutter-width'] ) ? ( (int) $porto_settings['container-width'] + (int) $porto_settings['grid-gutter-width'] - 1 ) . 'px' : '1219px',
									'md' => '991px',
									'sm' => '767px',
									'xs' => '575px',
								);

								if ( 'porto_dimension' == $param['type'] ) {
									$temp_value = $value;
									foreach ( $this::$dimensions as $dimension => $pattern ) {
										if ( isset( $dimension_values[ $dimension ]['xl'] ) ) {
											$temp = $this->porto_check_units( $dimension_values[ $dimension ]['xl'] );
											if ( ! $temp ) {
												$temp_value = preg_replace( '/([^;]*)(\{\{' . strtoupper( $dimension ) . '\}\})([^;]*)(;*)/', '', $temp_value );
											} else {
												$temp_value = str_replace( $pattern, $temp, $temp_value );
											}
										}
									}
									$style = $key . '{' . $temp_value . '}';
									foreach ( $breakpoints as $breakpoint => $width ) {
										$temp_value = $value;
										foreach ( $this::$dimensions as $dimension => $pattern ) {
											if ( isset( $dimension_values[ $dimension ][ $breakpoint ] ) ) {
												$temp = $this->porto_check_units( $dimension_values[ $dimension ][ $breakpoint ] );
												if ( ! $temp ) {
													$temp_value = preg_replace( '/([^;]*)(\{\{' . strtoupper( $dimension ) . '\}\})([^;]*)(;*)/', '', $temp_value );
												} else {
													$temp_value = str_replace( $pattern, $temp, $temp_value );
												}
											}
										}
										if ( ! empty( $temp_value ) ) {
											$style .= '@media (max-width:' . $width . '){';
											$style .= $key . '{' . $temp_value . '}}';
										}
									}
								} else {
									if ( ! empty( $responsive_values['xl'] ) || ( isset( $responsive_values['xl'] ) && '0' === $responsive_values['xl'] ) ) {
										if ( ! empty( $param['with_units'] ) && $param['with_units'] ) {
											$responsive_values['xl'] = $this->porto_check_units( $responsive_values['xl'] );
											if ( false === $responsive_values['xl'] ) {
												break;
											}
										}
										$style = $key . '{' . str_replace( '{{VALUE}}', $responsive_values['xl'], $value ) . '}';
									}
									foreach ( $breakpoints as $breakpoint => $width ) {
										if ( ! empty( $param['with_units'] ) && $param['with_units'] ) {
											$responsive_values[ $breakpoint ] = $this->porto_check_units( $responsive_values[ $breakpoint ] );
										}
										if ( ! empty( $responsive_values[ $breakpoint ] ) || ( isset( $responsive_values[ $breakpoint ] ) && '0' === $responsive_values[ $breakpoint ] ) ) {
											$style .= '@media (max-width:' . $width . '){';
											$style .= $key . '{' . str_replace( '{{VALUE}}', $responsive_values[ $breakpoint ], $value ) . '}}';
										}
									}
								}

								if ( empty( $css['responsive'] ) ) {
									$css['responsive'] = $style;
								} else {
									$css['responsive'] .= $style;
								}
							} else {
								if ( ! empty( $param['with_units'] ) && $param['with_units'] ) {
									$saved_value = $this->porto_check_units( $saved_value );

									if ( ! $saved_value ) {
										continue;
									}
								}
								if ( 'porto_dimension' == $param['type'] ) { // Dimension
									foreach ( $this::$dimensions as $dimension => $pattern ) {
										$temp = $this->porto_check_units( $dimension_values[ $dimension ]['xl'] );
										if ( ! $temp ) {
											$value = preg_replace( '/([^;]*)(\{\{' . strtoupper( $dimension ) . '\}\})([^;]*)(;*)/', '', $value );
										} else {
											$value = str_replace( $pattern, $temp, $value );
										}
									}

									if ( empty( $css[ str_replace( '{{WRAPPER}}', $shortcode_class, $key ) ] ) ) {
										$css[ str_replace( '{{WRAPPER}}', $shortcode_class, $key ) ] = $value;
									} else {
										$css[ str_replace( '{{WRAPPER}}', $shortcode_class, $key ) ] .= $value;
									}
								} elseif ( 'porto_typography' == $param['type'] && ! empty( $typography ) ) {
									if ( empty( $css[ str_replace( '{{WRAPPER}}', $shortcode_class, $value ) ] ) ) {
										$css[ str_replace( '{{WRAPPER}}', $shortcode_class, $value ) ] = $typography;
									} else {
										$css[ str_replace( '{{WRAPPER}}', $shortcode_class, $value ) ] .= $typography;
									}
								} elseif ( 'checkbox' == $param['type'] && ( empty( $saved_value ) && 'yes' == $saved_value ) ) {
									if ( empty( $css[ str_replace( '{{WRAPPER}}', $shortcode_class, $key ) ] ) ) {
										$css[ str_replace( '{{WRAPPER}}', $shortcode_class, $key ) ] = $value;
									} else {
										$css[ str_replace( '{{WRAPPER}}', $shortcode_class, $key ) ] .= $value;
									}
								} else { // Others
									if ( ! empty( $saved_value ) || ( isset( $saved_value ) && '0' === $saved_value ) ) {
										if ( empty( $css[ str_replace( '{{WRAPPER}}', $shortcode_class, $key ) ] ) ) {
											$css[ str_replace( '{{WRAPPER}}', $shortcode_class, $key ) ] = str_replace( '{{VALUE}}', $saved_value, $value );
										} else {
											$css[ str_replace( '{{WRAPPER}}', $shortcode_class, $key ) ] .= str_replace( '{{VALUE}}', $saved_value, $value );
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return $css;
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

	/**
	 * Porto WPB Global HashCode
	 *
	 * Generate hash code from attribues
	 *
	 * @param array $params
	 *
	 * @return string
	 * @since 6.1.0
	 */

	public static function get_global_hashcode( $atts, $tag, $params ) {
		$result = '';
		if ( is_array( $atts ) ) {
			$callback = function( $item, $key ) use ( $params ) {
				foreach ( $params as $param ) {
					if ( $param['param_name'] == $key && ! empty( $param['selectors'] ) ) {
						return true;
					}
				}
				return false;
			};
			if ( 'porto_grid_container' != $tag ) {
				$atts = array_filter(
					$atts,
					$callback,
					ARRAY_FILTER_USE_BOTH
				);
			}

			$keys   = array_keys( $atts );
			$values = array_values( $atts );
			$hash   = $tag . implode( '', $keys ) . implode( '', $values );
			if ( 0 == strlen( $hash ) ) {
				return '0';
			}
			return hash( 'md5', $hash );
		}
		return '0';
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

	/**
	 * update product args to sort by multiple fields
	 *
	 * @since 6.2.0
	 */
	public function wc_multi_order( $args ) {
		if ( empty( $this->product_mult_sort_args ) ) {
			return $args;
		}
		$final_args                   = $this->product_mult_sort_args;
		$this->product_mult_sort_args = '';
		return array_merge( $args, $final_args );
	}
}

// Finally initialize code
new PortoShortcodesClass();

include_once( dirname( PORTO_SHORTCODES_PATH ) . '/lib/blocks/porto-blocks.php' );
