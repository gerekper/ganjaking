<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Post Type Builder
 *
 * @since 2.3.0
 */

if ( ! class_exists( 'PortoBuildersType' ) ) :
	class PortoBuildersType {

		/**
		 * Meta fields
		 *
		 * @since 2.3.0
		 */
		private $meta_fields;

		/**
		 * Porto Builder Type
		 *
		 * @since 2.3.0
		 */
		private $editor_builder_type;

		/**
		 * Global Instance Objects
		 *
		 * @var    array $instances
		 * @since  2.3.0
		 * @access private
		 */
		private static $instance = null;

		public static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 2.3.0
		 */
		public function __construct() {
			if ( is_admin() && ( 'post.php' == $GLOBALS['pagenow'] || 'post-new.php' == $GLOBALS['pagenow'] ) ) {
				add_action( 'current_screen', array( $this, 'init' ) );

				if ( defined( 'WPB_VC_VERSION' ) ) {
					// enable gutenberg editor in wpbakery
					add_filter( 'classic_editor_enabled_editors_for_post', array( $this, 'enable_gutenberg_regular' ), 10, 2 );
					add_filter( 'use_block_editor_for_post_type', array( $this, 'enable_gutenberg' ), 10, 2 );
				}
			} else {
				add_action( 'porto_enqueue_css', array( $this, 'enqueue' ) );
			}

			add_filter( 'porto_elements_wrap_css_class', array( $this, 'elements_wrap_class_filter' ), 10, 3 );

			add_action( 'pre_get_posts', array( $this, 'filter_search_loop' ) );

			$this->add_elements();

			// add shortcodes
			add_action( 'template_redirect', array( $this, 'add_shortcodes' ) );

			// add WPBakery elements
			if ( defined( 'WPB_VC_VERSION' ) ) {
				add_action( 'vc_after_init', array( $this, 'add_wpb_element' ) );
				add_action( 'porto_archive_builder_add_wpb_elements', array( $this, 'add_wpb_archive_element' ), 10, 2 );

				add_filter( 'vc_autocomplete_porto_tb_posts_builder_id_callback', 'builder_id_callback' );
				add_filter( 'vc_autocomplete_porto_tb_archives_builder_id_callback', 'builder_id_callback' );
				add_filter( 'vc_autocomplete_porto_tb_archives_list_builder_id_callback', 'builder_id_callback' );

				add_filter( 'vc_autocomplete_porto_tb_posts_builder_id_render', 'builder_id_render' );
				add_filter( 'vc_autocomplete_porto_tb_archives_builder_id_render', 'builder_id_render' );
				add_filter( 'vc_autocomplete_porto_tb_archives_list_builder_id_render', 'builder_id_render' );
			}

			/*if ( wp_doing_ajax() ) {
			add_filter( 'yith_wcwl_ajax_add_return_params', array( $this, 'yith_ajax_add_cart_add_porto_classes' ) );

			add_filter( 'yith_wcwl_add_to_wishlist_params', array( $this, 'yith_ajax_add_wishlist_add_porto_classes' ), 10, 2 );
			}*/
		}

		/**
		 * Init functions
		 *
		 * @since 2.3.0
		 */
		public function init() {
			$screen = get_current_screen();
			if ( $screen && 'post' == $screen->base && PortoBuilders::BUILDER_SLUG == $screen->id ) {

				if ( ! $this->editor_builder_type ) {
					$this->post_id = is_singular() ? get_the_ID() : ( isset( $_GET['post'] ) ? (int) $_GET['post'] : ( isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : false ) );
					if ( ! $this->post_id ) {
						return;
					}
					$this->editor_builder_type = get_post_meta( $this->post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
				}
				if ( ! $this->editor_builder_type || 'type' != $this->editor_builder_type ) {
					return;
				}

				if ( $screen->is_block_editor() ) {

					add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

					$preview_width = get_post_meta( $this->post_id, 'preview_width', true );
					if ( ! $preview_width ) {
						$preview_width = 360;
					}
					add_action(
						'admin_enqueue_scripts',
						function () use ( $preview_width ) {
							wp_add_inline_style( 'porto-blocks-editor', '.post-type-porto_builder #elementor-switch-mode, .post-type-porto_builder .composer-switch { display: none } .edit-post-visual-editor__content-area > div { background: #333 !important } body .editor-styles-wrapper{width:' . floatval( $preview_width ) . 'px;margin: 30px auto;padding:0 10px}' );
						},
						1002
					);

					// add elements
					add_action(
						'enqueue_block_editor_assets',
						function () {
							   wp_enqueue_script( 'porto-tb-blocks', PORTO_FUNC_URL . 'builders/elements/type/elements/blocks.min.js', array(), PORTO_SHORTCODES_VERSION, true );
						},
						998
					);
					add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue' ), 1000 );
					add_filter(
						'block_categories_all',
						function ( $categories ) {
							return array_merge(
								$categories,
								array(
									array(
										'slug'  => 'porto-tb',
										'title' => __( 'Porto Type Builder Blocks', 'porto-functionality' ),
										'icon'  => '',
									),
								)
							);
						},
						11,
						1
					);
					add_filter( 'porto_gutenberg_editor_vars', array( $this, 'add_dynamic_field_vars' ) );
				} else {
					add_action( 'save_post', array( $this, 'save_meta_values' ), 99, 2 );
				}
			}
		}

		/**
		 * Add meta box to set post type, dynamic content as, preview width
		 *
		 * @since 2.3.0
		 */
		public function add_meta_box() {
			add_meta_box(
				PortoBuilders::BUILDER_SLUG . '-type-meta-box',
				__( 'Post Type Builder Options', 'porto-functionality' ),
				array( $this, 'meta_box_content' ),
				PortoBuilders::BUILDER_SLUG,
				'normal',
				'high'
			);
		}

		/**
		 * Output the meta box content
		 *
		 * @since 2.3.0
		 */
		public function meta_box_content() {
			porto_show_meta_box( $this->get_meta_box_fields() );
		}

		/**
		 * Save meta fields
		 *
		 * @since 2.3.0
		 */
		public function save_meta_values( $post_id, $post ) {
			if ( ! $post || ! isset( $post->post_type ) || PortoBuilders::BUILDER_SLUG != $post->post_type || ! $post->post_content || 'type' != get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			porto_save_meta_value( $post_id, $this->get_meta_box_fields() );

			// save dynamic styles
			if ( false !== strpos( $post->post_content, '<!-- wp:porto' ) ) { // Gutenberg editor

				$blocks = parse_blocks( $post->post_content );
				if ( ! empty( $blocks ) ) {
					ob_start();
					$css = '';
					$this->include_style( $blocks );
					$css = ob_get_clean();
					if ( $css ) {
						update_post_meta( $post_id, 'porto_builder_css', wp_strip_all_tags( $css ) );
					} else {
						delete_post_meta( $post_id, 'porto_builder_css' );
					}
				}
			}
		}

		/**
		 * Generate meta box fields
		 *
		 * @since 2.3.0
		 */
		private function get_meta_box_fields() {
			if ( $this->meta_fields ) {
				return $this->meta_fields;
			}
			$choices = array(
				''     => __( 'Default', 'porto-functionality' ),
				'term' => __( 'Term', 'porto-functionality' ),
			);

			$post_types          = get_post_types(
				array(
					'public'            => true,
					'show_in_nav_menus' => true,
				),
				'objects',
				'and'
			);
			$post_taxonomies     = array();
			$sub_fields_types    = array();
			$disabled_post_types = array( 'attachment', 'porto_builder', 'page', 'e-landing-page' );

			foreach ( $disabled_post_types as $disabled ) {
				unset( $post_types[ $disabled ] );
			}
			foreach ( $post_types as $post_type ) {
				$taxonomies = get_object_taxonomies( $post_type->name, 'objects' );
				foreach ( $taxonomies as $new_taxonomy ) {
					$post_taxonomies[ $new_taxonomy->name ] = ucwords( esc_html( $new_taxonomy->label ) );
				}

				$sub_fields_types[ 'content_type_' . $post_type->name ] = array(
					'name'     => 'content_type_' . $post_type->name,
					/* translators: The post name. */
					'title'    => sprintf( __( 'Select %s', 'porto-functionality' ), $post_type->labels->singular_name ),
					/* translators: The post name. */
					'desc'     => sprintf( __( 'Choose to view dynamic content as %s. Leave Empty for random selection.', 'porto-functionality' ), $post_type->labels->singular_name ),
					'type'     => 'ajaxselect2',
					'option'   => $post_type->name,
					'required' => array(
						'name'  => 'content_type',
						'value' => $post_type->name,
					),
				);

				$choices[ $post_type->name ] = $post_type->labels->singular_name;

				if ( ! empty( $post_type->has_archive ) ) {
					$archive_choices[ $post_type->name ] = $post_type->labels->singular_name;
				}
			}

			unset( $post_taxonomies['post_format'] );
			unset( $post_taxonomies['product_visibility'] );

			$sub_fields_types['content_type_term'] = array(
				'name'     => 'content_type_term',
				'title'    => __( 'Select Taxonomy', 'porto-functionality' ),
				'desc'     => __( 'Select a taxonomy to pull a term from. The most recent term in the taxonomy will be used.', 'porto-functionality' ),
				'type'     => 'select',
				'default'  => '',
				'options'  => $post_taxonomies,
				'required' => array(
					'name'  => 'content_type',
					'value' => 'term',
				),
			);

			$this->meta_fields = array_merge(
				array(
					'content_type' => array(
						'name'    => 'content_type',
						'title'   => __( 'Content Type', 'porto-functionality' ),
						'type'    => 'select',
						'default' => '',
						'options' => $choices,
					),
				),
				$sub_fields_types
			);

			$this->meta_fields['preview_width'] = array(
				'name'    => 'preview_width',
				'title'   => __( 'Preview Width (px)', 'porto-functionality' ),
				'desc'    => __( 'Note: this is only used for previewing purposes.', 'porto-functionality' ),
				'type'    => 'text',
				'default' => '360',
			);

			return $this->meta_fields;
		}

		/**
		 * Enqueue styles
		 *
		 * @since 2.3.0
		 */
		public function enqueue() {
			wp_enqueue_style( 'porto-type-builder', PORTO_FUNC_URL . 'builders/assets/type-builder.css', array(), PORTO_SHORTCODES_VERSION );
		}

		/**
		 * Add dynamic field vars
		 *
		 * @since 2.3.0
		 */
		public function add_dynamic_field_vars( $block_vars ) {
			$meta_fields = array(
				'global'      => array(
					'page_sub_title' => array( esc_html__( 'Page Sub Title', 'porto-functionality' ), 'text' ),
				),
				'post'        => array(),
				'event'       => array(),
				'portfolio'   => array(),
				'member'      => array(),
				'product'     => array(),
				'product_cat' => array(),
			);

			foreach ( $meta_fields as $post_type => $val ) {
				if ( 'global' == $post_type ) {
					continue;
				}
				if ( 'product_cat' == $post_type ) {
					global $porto_settings;
					if ( isset( $porto_settings['show-category-skin'] ) ) {
						$backup                               = $porto_settings['show-category-skin'];
						$porto_settings['show-category-skin'] = false;
					}
				}
				$fn_name = 'porto_' . $post_type . '_meta_fields';
				if ( ! function_exists( $fn_name ) ) {
					continue;
				}
				$post_fields = $fn_name();
				if ( 'product_cat' == $post_type && isset( $backup ) ) {
					global $porto_settings;
					$porto_settings['show-category-skin'] = $backup;
				}
				foreach ( $post_fields as $key => $arr ) {
					$meta_fields[ $post_type ][ $key ] = array( esc_js( $arr['title'] ), $arr['type'] );
				}
			}

			$block_vars['meta_fields'] = $meta_fields;
			return $block_vars;
		}

		/**
		 * Load post type builder blocks
		 *
		 * @since 2.3.0
		 */
		private function add_elements() {

			register_block_type(
				'porto-tb/porto-featured-image',
				array(
					'attributes'      => array(
						'image_type'         => array(
							'type' => 'string',
						),
						'hover_effect'       => array(
							'type' => 'string',
						),
						'show_content_hover' => array(
							'type' => 'boolean',
						),
						'show_badges'        => array(
							'type' => 'boolean',
						),
						'zoom'               => array(
							'type' => 'boolean',
						),
						'content_type'       => array(
							'type' => 'string',
						),
						'content_type_value' => array(
							'type' => 'string',
						),
						'add_link'           => array(
							'type' => 'string',
						),
						'custom_url'         => array(
							'type' => 'string',
						),
						'link_target'        => array(
							'type' => 'string',
						),
						'image_size'         => array(
							'type' => 'string',
						),
						'zoom_icon'          => array(
							'type' => 'string',
						),
						'el_class'           => array(
							'type' => 'string',
						),
						'className'          => array(
							'type' => 'string',
						),
						'style_options'      => array(
							'type' => 'object',
						),
					),
					'editor_script'   => 'porto-tb-blocks',
					'render_callback' => function ( $atts, $content = null ) {
						return $this->render_block( $atts, 'featured-image', $content );
					},
				)
			);

			register_block_type(
				'porto-tb/porto-content',
				array(
					'attributes'      => array(
						'content_display'    => array(
							'type' => 'string',
						),
						'excerpt_length'     => array(
							'type' => 'integer',
						),
						'content_type'       => array(
							'type' => 'string',
						),
						'content_type_value' => array(
							'type' => 'string',
						),
						'alignment'          => array(
							'type' => 'string',
						),
						'font_settings'      => array(
							'type' => 'object',
						),
						'style_options'      => array(
							'type' => 'object',
						),
						'el_class'           => array(
							'type' => 'string',
						),
						'className'          => array(
							'type' => 'string',
						),
					),
					'editor_script'   => 'porto-tb-blocks',
					'render_callback' => function ( $atts ) {
						return $this->render_block( $atts, 'content' );
					},
				)
			);

			register_block_type(
				'porto-tb/porto-woo-price',
				array(
					'attributes'      => array(
						'content_type'       => array(
							'type' => 'string',
						),
						'content_type_value' => array(
							'type' => 'string',
						),
						'alignment'          => array(
							'type' => 'string',
						),
						'font_settings'      => array(
							'type' => 'object',
						),
						'style_options'      => array(
							'type' => 'object',
						),
						'el_class'           => array(
							'type' => 'string',
						),
						'className'          => array(
							'type' => 'string',
						),
					),
					'editor_script'   => 'porto-tb-blocks',
					'render_callback' => function ( $atts ) {
						return $this->render_block( $atts, 'woo-price' );
					},
				)
			);

			register_block_type(
				'porto-tb/porto-woo-rating',
				array(
					'attributes'      => array(
						'content_type'       => array(
							'type' => 'string',
						),
						'content_type_value' => array(
							'type' => 'string',
						),
						'alignment'          => array(
							'type' => 'string',
						),
						'font_settings'      => array(
							'type' => 'object',
						),
						'style_options'      => array(
							'type' => 'object',
						),
						'el_class'           => array(
							'type' => 'string',
						),
						'className'          => array(
							'type' => 'string',
						),
					),
					'editor_script'   => 'porto-tb-blocks',
					'render_callback' => function ( $atts ) {
						return $this->render_block( $atts, 'woo-rating' );
					},
				)
			);

			register_block_type(
				'porto-tb/porto-woo-stock',
				array(
					'attributes'      => array(
						'content_type'       => array(
							'type' => 'string',
						),
						'content_type_value' => array(
							'type' => 'string',
						),
						'alignment'          => array(
							'type' => 'string',
						),
						'font_settings'      => array(
							'type' => 'object',
						),
						'style_options'      => array(
							'type' => 'object',
						),
						'el_class'           => array(
							'type' => 'string',
						),
						'className'          => array(
							'type' => 'string',
						),
					),
					'editor_script'   => 'porto-tb-blocks',
					'render_callback' => function ( $atts ) {
						return $this->render_block( $atts, 'woo-stock' );
					},
				)
			);

			register_block_type(
				'porto-tb/porto-woo-desc',
				array(
					'attributes'      => array(
						'content_type'       => array(
							'type' => 'string',
						),
						'content_type_value' => array(
							'type' => 'string',
						),
						'alignment'          => array(
							'type' => 'string',
						),
						'font_settings'      => array(
							'type' => 'object',
						),
						'style_options'      => array(
							'type' => 'object',
						),
						'el_class'           => array(
							'type' => 'string',
						),
						'className'          => array(
							'type' => 'string',
						),
					),
					'editor_script'   => 'porto-tb-blocks',
					'render_callback' => function ( $atts ) {
						return $this->render_block( $atts, 'woo-desc' );
					},
				)
			);

			register_block_type(
				'porto-tb/porto-woo-buttons',
				array(
					'attributes'      => array(
						'content_type'        => array(
							'type' => 'string',
						),
						'content_type_value'  => array(
							'type' => 'string',
						),
						'link_source'         => array(
							'type' => 'string',
						),
						'show_quantity_input' => array(
							'type' => 'boolean',
						),
						'hide_title'          => array(
							'type' => 'boolean',
						),
						'icon_cls'            => array(
							'type' => 'string',
						),
						'icon_cls_added'      => array(
							'type' => 'string',
						),
						'icon_pos'            => array(
							'type' => 'string',
						),
						'spacing'             => array(
							'type' => 'string',
						),
						'alignment'           => array(
							'type' => 'string',
						),
						'font_settings'       => array(
							'type' => 'object',
						),
						'style_options'       => array(
							'type' => 'object',
						),
						'el_class'            => array(
							'type' => 'string',
						),
						'className'           => array(
							'type' => 'string',
						),
					),
					'editor_script'   => 'porto-tb-blocks',
					'render_callback' => function ( $atts ) {
						return $this->render_block( $atts, 'woo-buttons' );
					},
				)
			);

			register_block_type(
				'porto-tb/porto-meta',
				array(
					'attributes'      => array(
						'content_type'       => array(
							'type' => 'string',
						),
						'content_type_value' => array(
							'type' => 'string',
						),
						'field'              => array(
							'type' => 'string',
						),
						'date_format'        => array(
							'type' => 'string',
						),
						'icon_cls'           => array(
							'type' => 'string',
						),
						'icon_pos'           => array(
							'type' => 'string',
						),
						'spacing'            => array(
							'type' => 'integer',
						),
						'font_settings'      => array(
							'type' => 'object',
						),
						'style_options'      => array(
							'type' => 'object',
						),
						'el_class'           => array(
							'type' => 'string',
						),
						'className'          => array(
							'type' => 'string',
						),
					),
					'editor_script'   => 'porto-tb-blocks',
					'render_callback' => function ( $atts ) {
						return $this->render_block( $atts, 'meta' );
					},
				)
			);
		}

		/**
		 * Add shortcodes
		 *
		 * @since 2.3.0
		 */
		public function add_shortcodes() {
			$shortcode_type = false;
			if ( is_singular( PortoBuilders::BUILDER_SLUG ) ) {
				$type = get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
				if ( 'shop' == $type || 'archive' == $type ) {
					$shortcode_type = $type;
				}
			} elseif ( is_archive() && function_exists( 'porto_check_builder_condition' ) ) {
				if ( porto_check_builder_condition( 'shop' ) ) {
					$shortcode_type = 'shop';
				} elseif ( porto_check_builder_condition( 'archive' ) ) {
					$shortcode_type = 'archive';
				}
			}

			add_shortcode( 'porto_tb_posts', array( $this, 'render_posts_grid' ) );
			if ( $shortcode_type ) {
				add_shortcode(
					'porto_tb_archives',
					function ( $atts, $content = null ) use ( $shortcode_type ) {
						if ( empty( $atts ) ) {
							   $atts = array();
						}
						$atts['shortcode_type'] = $shortcode_type;
						return $this->render_posts_grid( $atts, $content, 'porto_tb_archives' );
					}
				);
			}
		}

		/**
		 * Add WPBakery element
		 *
		 * @since 2.3.0
		 */
		public function add_wpb_element() {
			$post_types          = get_post_types(
				array(
					'public'            => true,
					'show_in_nav_menus' => true,
				),
				'objects',
				'and'
			);
			$disabled_post_types = array( 'attachment', 'porto_builder', 'page', 'e-landing-page' );
			foreach ( $disabled_post_types as $disabled ) {
				unset( $post_types[ $disabled ] );
			}
			foreach ( $post_types as $key => $p_type ) {
				$post_types[ $key ] = esc_html( $p_type->label );
			}
			$post_types = apply_filters( 'porto_posts_grid_post_types', $post_types );

			$taxes = get_taxonomies( array(), 'objects' );
			unset( $taxes['post_format'], $taxes['product_visibility'], $taxes['elementor_library_category'] );
			foreach ( $taxes as $tax_name => $tax ) {
				$taxes[ $tax_name ] = esc_html( $tax->label );
			}
			$taxes = apply_filters( 'porto_posts_grid_taxonomies', $taxes );
			$left  = is_rtl() ? 'right' : 'left';
			$right = is_rtl() ? 'left' : 'right';

			global $porto_settings;
			$status_values = array(
				__( 'All', 'porto-functionality' )       => '',
				__( 'Featured', 'porto-functionality' )  => 'featured',
				__( 'On Sale', 'porto-functionality' )   => 'on_sale',
				__( 'Pre-Order', 'porto-functionality' ) => 'pre_order',
				__( 'Recently Viewed', 'porto-functionality' ) => 'viewed',
			);
			if ( empty( $porto_settings['woo-pre-order'] ) ) {
				unset( $status_values[ __( 'Pre-Order', 'porto-functionality' ) ] );
			}

			vc_map(
				array(
					'name'        => __( 'Porto Posts Grid', 'porto-functionality' ),
					'base'        => 'porto_tb_posts',
					'icon'        => 'far fa-calendar-alt',
					'category'    => __( 'Porto', 'porto-functionality' ),
					'description' => __( 'Show archive elements in the layout which built using Post Type Builder.', 'porto-functionality' ),
					'params'      => array_merge(
						array(
							array(
								'type'       => 'porto_param_heading',
								'param_name' => 'posts_layout',
								'text'       => __( 'Posts Selector', 'porto-functionality' ),
							),
							array(
								'type'        => 'autocomplete',
								'heading'     => __( 'Post Layout', 'porto-functionality' ),
								'param_name'  => 'builder_id',
								'settings'    => array(
									'multiple'      => false,
									'sortable'      => true,
									'unique_values' => true,
								),
								/* translators: starting and end A tags which redirects to edit page */
								'description' => sprintf( __( 'Please select a saved Post Layout template which was built using post type builder. Please create a new Post Layout template in %1$sPorto Templates Builder%2$s. If you don\'t select, default template will be used.', 'porto-functionality' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=' . PortoBuilders::BUILDER_SLUG . '&' . PortoBuilders::BUILDER_TAXONOMY_SLUG . '=type' ) ) . '">', '</a>' ),
								'admin_label' => true,
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Content Source', 'porto-functionality' ),
								'description' => __( 'Please select the content type which you would like to show.', 'porto-functionality' ),
								'param_name'  => 'source',
								'std'         => '',
								'value'       => array(
									__( 'Posts', 'porto-functionality' ) => '',
									__( 'Terms', 'porto-functionality' ) => 'terms',
								),
								'admin_label' => true,
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Post Type', 'porto-functionality' ),
								'description' => __( 'Please select a post type of posts to display.', 'porto-functionality' ),
								'param_name'  => 'post_type',
								'value'       => array_merge(
									array(
										'' => '',
									),
									array_flip( $post_types )
								),
								'dependency'  => array(
									'element' => 'source',
									'value'   => array( '' ),
								),
								'admin_label' => true,
							),
							array(
								'heading'    => __( 'Product Status', 'porto-functionality' ),
								'type'       => 'dropdown',
								'param_name' => 'product_status',
								'default'    => '',
								'value'      => $status_values,
								'dependency' => array(
									'element' => 'post_type',
									'value'   => 'product',
								),
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Terms', 'porto-functionality' ),
								'description' => __( 'Please input comma separated term ids to pull posts from.', 'porto-functionality' ),
								'param_name'  => 'post_terms',
								'dependency'  => array(
									'element' => 'source',
									'value'   => array( '' ),
								),
								'admin_label' => true,
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Taxonomy', 'porto-functionality' ),
								'description' => __( 'Please select a taxonomy to use.', 'porto-functionality' ),
								'param_name'  => 'tax',
								'value'       => array_merge(
									array(
										'' => '',
									),
									array_flip( $taxes )
								),
								'dependency'  => array(
									'element' => 'source',
									'value'   => array( 'terms' ),
								),
								'admin_label' => true,
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Terms', 'porto-functionality' ),
								'description' => __( 'Please input comma separated term ids to display.', 'porto-functionality' ),
								'param_name'  => 'terms',
								'dependency'  => array(
									'element' => 'source',
									'value'   => array( 'terms' ),
								),
								'admin_label' => true,
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Count (per page)', 'porto-functionality' ),
								'description' => __( 'Leave blank if you use default value.', 'porto-functionality' ),
								'param_name'  => 'count',
								'admin_label' => true,
							),
							array(
								'type'       => 'checkbox',
								'heading'    => __( 'Hide empty', 'js_composer' ),
								'param_name' => 'hide_empty',
								'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
								'dependency' => array(
									'element' => 'source',
									'value'   => array( 'terms' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Order by', 'porto-functionality' ),
								'param_name'  => 'orderby',
								'value'       => porto_vc_woo_order_by(),
								'description' => __( 'Price, Popularity and Rating values only work for product post type.', 'porto-functionality' ),
								'dependency'  => array(
									'element' => 'source',
									'value'   => array( '' ),
								),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Order by', 'porto-functionality' ),
								'param_name' => 'orderby_term',
								'value'      => array(
									__( 'Default', 'porto-functionality' ) => '',
									__( 'Title', 'porto-functionality' ) => 'name',
									__( 'ID', 'porto-functionality' ) => 'term_id',
									__( 'Post Count', 'porto-functionality' ) => 'count',
									__( 'None', 'porto-functionality' ) => 'none',
									__( 'Parent', 'porto-functionality' ) => 'parent',
									__( 'Description', 'porto-functionality' ) => 'description',
									__( 'Term Group', 'porto-functionality' ) => 'term_group',
								),
								'std'        => '',
								'dependency' => array(
									'element' => 'source',
									'value'   => array( 'terms' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Order way', 'porto-functionality' ),
								'param_name'  => 'order',
								'value'       => porto_vc_woo_order_way(),
								/* translators: %s: Wordpres codex page */
								'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
							),
							array(
								'type'       => 'porto_param_heading',
								'param_name' => 'posts_layout',
								'text'       => __( 'Posts Layout', 'porto-functionality' ),
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'View mode', 'porto-functionality' ),
								'param_name'  => 'view',
								'value'       => array(
									__( 'Grid', 'porto-functionality' ) => '',
									__( 'Grid - Creative', 'porto-functionality' ) => 'creative',
									__( 'Masonry', 'porto-functionality' ) => 'masonry',
									__( 'Slider', 'porto-functionality' ) => 'slider',
								),
								'admin_label' => true,
							),
							array(
								'type'       => 'porto_image_select',
								'heading'    => __( 'Grid Layout', 'porto-functionality' ),
								'param_name' => 'grid_layout',
								'dependency' => array(
									'element' => 'view',
									'value'   => array( 'creative' ),
								),
								'std'        => '1',
								'value'      => porto_sh_commons( 'masonry_layouts' ),
							),
							array(
								'type'       => 'number',
								'heading'    => __( 'Grid Height (px)', 'porto-functionality' ),
								'param_name' => 'grid_height',
								'dependency' => array(
									'element' => 'view',
									'value'   => array( 'creative' ),
								),
								'suffix'     => 'px',
								'std'        => 600,
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Column Spacing (px)', 'porto-functionality' ),
								'description' => __( 'Leave blank if you use theme default value.', 'porto-functionality' ),
								'param_name'  => 'spacing',
								'suffix'      => 'px',
								'std'         => '',
								'selectors'   => array(
									'{{WRAPPER}}' => '--porto-el-spacing: {{VALUE}}px;',
								),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Columns', 'porto-functionality' ),
								'param_name' => 'columns',
								'std'        => '4',
								'value'      => porto_sh_commons( 'products_columns' ),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Columns on tablet ( <= 991px )', 'porto-functionality' ),
								'param_name' => 'columns_tablet',
								'std'        => '',
								'value'      => array(
									__( 'Default', 'porto-functionality' ) => '',
									'1' => '1',
									'2' => '2',
									'3' => '3',
									'4' => '4',
								),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Columns on mobile ( <= 575px )', 'porto-functionality' ),
								'param_name' => 'columns_mobile',
								'std'        => '',
								'value'      => array(
									__( 'Default', 'porto-functionality' ) => '',
									'1' => '1',
									'2' => '2',
									'3' => '3',
								),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Pagination Type', 'porto-functionality' ),
								'param_name' => 'pagination_style',
								'std'        => '',
								'value'      => array(
									__( 'None', 'porto-functionality' ) => '',
									__( 'Ajax Pagination' ) => 'ajax',
									__( 'Infinite Scroll' ) => 'infinite',
									__( 'Load more' ) => 'load_more',
								),
								'dependency' => array(
									'element' => 'source',
									'value'   => array( '' ),
								),
							),
							array(
								'type'        => 'checkbox',
								'heading'     => __( 'Show category filter', 'porto-functionality' ),
								'description' => __( 'Defines whether to show or hide category filters above posts.', 'porto-functionality' ),
								'param_name'  => 'category_filter',
								'std'         => '',
								'admin_label' => true,
								'dependency'  => array(
									'element' => 'source',
									'value'   => array( '' ),
								),
							),
							array(
								'param_name'  => 'filter_cat_tax',
								'type'        => 'dropdown',
								'heading'     => __( 'Taxonomy', 'porto-functionality' ),
								'description' => __( 'Please select a post taxonomy to be used as category filter.', 'porto-functionality' ),
								'value'       => array_merge(
									array( __( 'Default', 'porto-functionality' ) => '' ),
									array_flip( $taxes )
								),
								'label_block' => true,
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
							),
							/*array(
								'type'        => 'checkbox',
								'heading'     => __( 'Show Image Counter', 'porto-functionality' ),
								'description' => __( 'Defines whether to show or hide the count of attachment images at the top of post item. If you use default template, this works for only portfolios.', 'porto-functionality' ),
								'param_name'  => 'image_counter',
								'std'         => '',
								'admin_label' => true,
								'dependency'  => array(
									'element' => 'source',
									'value'   => array( '' ),
								),
							),
							array(
								'type'        => 'checkbox',
								'heading'     => __( 'Enable Ajax Load', 'porto-functionality' ),
								'description' => __( 'If enabled, post content should be displayed above posts or on modal when you click post item in the list. If you use default template, this works for only portfolios and members.', 'porto-functionality' ),
								'param_name'  => 'ajax_load',
								'std'         => '',
								'admin_label' => true,
								'dependency'  => array(
									'element' => 'source',
									'value'   => array( '' ),
								),
							),
							array(
								'type'        => 'checkbox',
								'heading'     => __( 'Enable Ajax Load on Modal', 'porto-functionality' ),
								'description' => __( 'If enabled, post content should be displayed on modal when you click post item in the list. If you use default template, this works for only portfolios and members.', 'porto-functionality' ),
								'param_name'  => 'ajax_modal',
								'std'         => '',
								'admin_label' => true,
								'dependency'  => array(
									'element'   => 'ajax_load',
									'not_empty' => true,
								),
							),*/
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Image Size', 'porto-functionality' ),
								'param_name' => 'image_size',
								'value'      => porto_sh_commons( 'image_sizes' ),
								'std'        => '',
								'dependency' => array(
									'element'            => 'view',
									'value_not_equal_to' => 'creative',
								),
							),
							porto_vc_custom_class(),

							array(
								'type'       => 'porto_param_heading',
								'param_name' => 'p_style',
								'text'       => __( 'Pagination Style', 'porto-functionality' ),
								'dependency' => array(
									'element' => 'pagination_style',
									'value'   => array( '', 'ajax' ),
								),
								'group'      => 'Style',
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Horizontal Align', 'porto-functionality' ),
								'param_name' => 'p_align',
								'value'      => array(
									__( 'Default', 'porto-functionality' ) => '',
									__( 'Left', 'porto-functionality' ) => 'flex-start',
									__( 'Center', 'porto-functionality' ) => 'center',
									__( 'Right', 'porto-functionality' ) => 'flex-end',
								),
								'dependency' => array(
									'element' => 'pagination_style',
									'value'   => array( '', 'ajax' ),
								),
								'selectors'  => array(
									'{{WRAPPER}} .pagination' => 'justify-content: {{VALUE}};',
								),
								'group'      => 'Style',
							),
							array(
								'type'       => 'porto_dimension',
								'heading'    => __( 'Set custom margin of pagination part.', 'porto-functionality' ),
								'param_name' => 'p_margin',
								'value'      => '',
								'dependency' => array(
									'element' => 'pagination_style',
									'value'   => array( '', 'ajax' ),
								),
								'selectors'  => array(
									'{{WRAPPER}} .pagination-wrap' => 'margin-top: {{TOP}};margin-right: {{RIGHT}};margin-bottom: {{BOTTOM}};margin-left: {{LEFT}};',
								),
								'group'      => 'Style',
							),

							array(
								'type'       => 'porto_param_heading',
								'param_name' => 'lm_style',
								'text'       => __( 'Load More Button Style', 'porto-functionality' ),
								'dependency' => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'group'      => 'Style',
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Width', 'porto-functionality' ),
								'param_name' => 'lm_width',
								'value'      => array(
									'100%' => '',
									'auto' => 'auto',
								),
								'dependency' => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'  => array(
									'{{WRAPPER}} .load-more .next' => 'width: {{VALUE}};',
								),
								'group'      => 'Style',
							),
							array(
								'type'       => 'porto_typography',
								'heading'    => __( 'Typography', 'porto-functionality' ),
								'param_name' => 'lm_typography',
								'dependency' => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'  => array(
									'{{WRAPPER}} .load-more .next',
								),
								'group'      => 'Style',
							),
							array(
								'type'        => 'porto_dimension',
								'heading'     => __( 'Padding', 'porto-functionality' ),
								'description' => __( 'Controls padding value of button.', 'porto-functionality' ),
								'param_name'  => 'lm_padding',
								'value'       => '',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .load-more .next' => 'padding-top: {{TOP}};padding-right: {{RIGHT}};padding-bottom: {{BOTTOM}};padding-left: {{LEFT}};',
								),
								'group'       => 'Style',
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Spacing (px)', 'porto-functionality' ),
								'description' => __( 'Controls the spacing of load more button.', 'porto-functionality' ),
								'param_name'  => 'lm_spacing',
								'suffix'      => 'px',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .pagination-wrap' => 'margin-top: {{VALUE}}px;',
								),
								'group'       => 'Style',
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Color', 'porto-functionality' ),
								'description' => __( 'Controls the color of the button.', 'porto-functionality' ),
								'param_name'  => 'lm_color',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .load-more .next' => 'color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Hover Color', 'porto-functionality' ),
								'description' => __( 'Controls the hover color of the button.', 'porto-functionality' ),
								'param_name'  => 'lm_color_hover',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .load-more .next:hover' => 'color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Background Color', 'porto-functionality' ),
								'description' => __( 'Controls the background color of the button.', 'porto-functionality' ),
								'param_name'  => 'lm_back_color',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .load-more .next' => 'background-color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Hover Background Color', 'porto-functionality' ),
								'description' => __( 'Controls the hover background color of the button.', 'porto-functionality' ),
								'param_name'  => 'lm_back_color_hover',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .load-more .next:hover' => 'background-color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Border Color', 'porto-functionality' ),
								'description' => __( 'Controls the border color of the button.', 'porto-functionality' ),
								'param_name'  => 'lm_border_color',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .load-more .next' => 'border-color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Hover Border Color', 'porto-functionality' ),
								'description' => __( 'Controls the hover border color of the button.', 'porto-functionality' ),
								'param_name'  => 'lm_border_color_hover',
								'dependency'  => array(
									'element' => 'pagination_style',
									'value'   => 'load_more',
								),
								'selectors'   => array(
									'{{WRAPPER}} .load-more .next:hover' => 'border-color: {{VALUE}};',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),

							array(
								'type'       => 'porto_param_heading',
								'param_name' => 'filter_style',
								'text'       => __( 'Filters Style', 'porto-functionality' ),
								'dependency' => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'group'      => 'Style',
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Alignment', 'porto-functionality' ),
								'param_name'  => 'filter_align',
								'description' => __( 'Controls filters\'s alignment. Choose from Left, Center, Right.', 'porto-functionality' ),
								'value'       => array(
									__( 'Default', 'porto-functionality' ) => '',
									__( 'Left', 'porto-functionality' ) => 'flex-start',
									__( 'Center', 'porto-functionality' ) => 'center',
									__( 'Right', 'porto-functionality' ) => 'flex-end',
								),
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .sort-source' => 'justify-content: {{VALUE}};',
								),
								'group'       => 'Style',
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Between Spacing (px)', 'porto-functionality' ),
								'description' => __( 'Controls the spacing between filters.', 'porto-functionality' ),
								'param_name'  => 'filter_between_spacing',
								'suffix'      => 'px',
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .sort-source li' => 'margin-' . $right . ': {{VALUE}}px; margin-bottom: {{VALUE}}px;',
								),
								'group'       => 'Style',
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Bottom Spacing (px)', 'porto-functionality' ),
								'description' => __( 'Controls the spacing of the filters.', 'porto-functionality' ),
								'param_name'  => 'filter_spacing',
								'suffix'      => 'px',
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .sort-source' => 'margin-bottom: {{VALUE}}px;',
								),
								'group'       => 'Style',
							),
							array(
								'type'       => 'porto_typography',
								'heading'    => __( 'Typography', 'porto-functionality' ),
								'param_name' => 'filter_typography',
								'dependency' => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'  => array(
									'{{WRAPPER}} .sort-source a',
								),
								'group'      => 'Style',
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Item Background Color', 'porto-functionality' ),
								'description' => __( 'Controls the item\'s background color.', 'porto-functionality' ),
								'param_name'  => 'filter_normal_bgc',
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .sort-source a' => 'background-color: {{VALUE}};',
								),
								'group'       => 'Style',
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Item Color', 'porto-functionality' ),
								'description' => __( 'Controls the item\'s color.', 'porto-functionality' ),
								'param_name'  => 'filter_normal_color',
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .sort-source a' => 'color: {{VALUE}};',
								),
								'group'       => 'Style',
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Item Active Background Color', 'porto-functionality' ),
								'description' => __( 'Controls the item\'s active and hover background color.', 'porto-functionality' ),
								'param_name'  => 'filter_active_bgc',
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .sort-source li.active > a, {{WRAPPER}} .sort-source a:hover, {{WRAPPER}} .sort-source a:focus' => 'background-color: {{VALUE}};',
								),
								'group'       => 'Style',
							),
							array(
								'type'        => 'colorpicker',
								'heading'     => __( 'Item Active Color', 'porto-functionality' ),
								'description' => __( 'Controls the item\'s active and hover color.', 'porto-functionality' ),
								'param_name'  => 'filter_active_color',
								'dependency'  => array(
									'element'   => 'category_filter',
									'not_empty' => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .sort-source li.active > a, {{WRAPPER}} .sort-source a:hover, {{WRAPPER}} .sort-source a:focus' => 'color: {{VALUE}};',
								),
								'group'       => 'Style',
							),
							array(
								'type'       => 'porto_number',
								'heading'    => __( 'Stage Padding', 'porto-functionality' ),
								'param_name' => 'stage_padding',
								'dependency' => array(
									'element' => 'view',
									'value'   => 'slider',
								),
								'group'      => __( 'Slider Options', 'porto-functionality' ),
							),
						),
						porto_vc_product_slider_fields( 'slider' )
					),
				)
			);
		}

		/**
		 * Add WPBakery Archive element
		 *
		 * @since 2.3.0
		 */
		public function add_wpb_archive_element( $type = 'shop', $custom_class = array() ) {
			$right = is_rtl() ? 'left' : 'right';
			vc_map(
				array(
					'name'        => __( 'Type Builder Archives', 'porto-functionality' ),
					'base'        => 'porto_tb_archives',
					'icon'        => 'far fa-calendar-alt',
					'category'    => 'shop' == $type ? __( 'Shop Builder', 'porto-functionality' ) : __( 'Archive Builder', 'porto-functionality' ),
					'description' => 'shop' == $type ? __( 'Show products in the layout which built using Post Type Builder.', 'porto-functionality' ) : __( 'Show archive elements in the layout which built using Post Type Builder.', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'posts_layout',
							'text'       => __( 'Posts Selector', 'porto-functionality' ),
						),
						array(
							'type'        => 'autocomplete',
							'heading'     => __( 'Post Layout', 'porto-functionality' ),
							'param_name'  => 'builder_id',
							'settings'    => array(
								'multiple'      => false,
								'sortable'      => true,
								'unique_values' => true,
							),
							/* translators: starting and end A tags which redirects to edit page */
							'description' => sprintf( __( 'Please select a saved Post Layout template which was built using post type builder. Please create a new Post Layout template in %1$sPorto Templates Builder%2$s. If you don\'t select, default template will be used.', 'porto-functionality' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=' . PortoBuilders::BUILDER_SLUG . '&' . PortoBuilders::BUILDER_TAXONOMY_SLUG . '=type' ) ) . '">', '</a>' ),
							'admin_label' => true,
						),
						array(
							'type'        => 'autocomplete',
							'heading'     => __( 'Post Layout for List View', 'js_composer' ),
							'param_name'  => 'list_builder_id',
							'settings'    => array(
								'multiple'      => false,
								'sortable'      => true,
								'unique_values' => true,
							),
							/* translators: starting and end A tags which redirects to edit page */
							'description' => sprintf( __( 'Please select a saved Post Layout template which will be used in the list view when using Grid / List Toggle element. Please create a new Post Layout template in %1$sPorto Templates Builder%2$s', 'porto-functionality' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=' . PortoBuilders::BUILDER_SLUG . '&' . PortoBuilders::BUILDER_TAXONOMY_SLUG . '=type' ) ) . '">', '</a>' ),
							'admin_label' => true,
						),
						array(
							'type'        => 'number',
							'heading'     => __( 'Count (per page)', 'porto-functionality' ),
							/* translators: staring and ending A tag which is linked to Porto Theme Options */
							'description' => 'shop' == $type ? sprintf( __( 'Please leave empty if you want to use default value which is set using WooCommerce -> Product Archives -> Products per Page in %1$sTheme Options%2$s.', 'porto-functionality' ), '<a href="' . esc_url( admin_url( 'themes.php?page=porto_settings' ) ) . '">', '</a>' ) : __( 'Leave blank if you use default value.', 'porto-functionality' ),
							'param_name'  => 'count',
							'admin_label' => true,
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Order by', 'porto-functionality' ),
							'param_name'  => 'orderby',
							'value'       => 'shop' == $type ? porto_vc_woo_order_by() : porto_vc_order_by(),
							/* translators: %s: Wordpres codex page */
							'description' => sprintf( __( 'Select how to sort retrieved posts. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Order way', 'porto-functionality' ),
							'param_name'  => 'order',
							'value'       => porto_vc_woo_order_way(),
							/* translators: %s: Wordpres codex page */
							'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'porto-functionality' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'posts_layout',
							'text'       => __( 'Posts Layout', 'porto-functionality' ),
						),
						array(
							'type'        => 'number',
							'heading'     => __( 'Column Spacing (px)', 'porto-functionality' ),
							'description' => __( 'Leave blank if you use theme default value.', 'porto-functionality' ),
							'param_name'  => 'spacing',
							'suffix'      => 'px',
							'std'         => '',
							'selectors'   => array(
								'{{WRAPPER}}' => '--porto-el-spacing: {{VALUE}}px;',
							),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Columns', 'porto-functionality' ),
							'param_name' => 'columns',
							'std'        => '4',
							'value'      => porto_sh_commons( 'products_columns' ),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Columns on tablet ( <= 991px )', 'porto-functionality' ),
							'param_name' => 'columns_tablet',
							'std'        => '',
							'value'      => array(
								__( 'Default', 'porto-functionality' ) => '',
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
							),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Columns on mobile ( <= 575px )', 'porto-functionality' ),
							'param_name' => 'columns_mobile',
							'std'        => '',
							'value'      => array(
								__( 'Default', 'porto-functionality' ) => '',
								'1' => '1',
								'2' => '2',
								'3' => '3',
							),
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Columns on List View', 'porto-functionality' ),
							'description' => __( 'Select number of columns to display on desktop( >= 992px ).', 'porto-functionality' ),
							'param_name'  => 'list_col',
							'std'         => '',
							'value'       => array(
								__( 'Default', 'porto-functionality' ) => '',
								'1' => '1',
								'2' => '2',
								'3' => '3',
							),
							'dependency'  => array(
								'element'   => 'list_builder_id',
								'not_empty' => true,
							),
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Pagination Type', 'porto-functionality' ),
							'param_name' => 'pagination_style',
							'std'        => '',
							'value'      => array(
								__( 'Default', 'porto-functionality' ) => '',
								__( 'Ajax Pagination', 'porto-functionality' ) => 'ajax',
								__( 'Infinite Scroll', 'porto-functionality' ) => 'infinite',
								__( 'Load more', 'porto-functionality' )       => 'load_more',
								__( 'None', 'porto-functionality' ) => 'none',
							),
						),
						array(
							'type'        => 'checkbox',
							'heading'     => __( 'Show category filter', 'porto-functionality' ),
							'description' => __( 'Defines whether to show or hide category filters above products.', 'porto-functionality' ),
							'param_name'  => 'category_filter',
							'std'         => '',
							'admin_label' => true,
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Image Size', 'porto-functionality' ),
							'param_name' => 'image_size',
							'value'      => porto_sh_commons( 'image_sizes' ),
							'std'        => '',
							'dependency' => array(
								'element'            => 'view',
								'value_not_equal_to' => 'creative',
							),
						),
						array(
							'type'        => 'textarea',
							'heading'     => __( 'Nothing Found Message', 'porto-functionality' ),
							'description' => __( 'Text when no results are found.', 'porto-functionality' ),
							'param_name'  => 'post_found_nothing',
							'admin_label' => true,
						),
						$custom_class,
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'p_style',
							'text'       => __( 'Pagination Style', 'porto-functionality' ),
							'dependency' => array(
								'element' => 'pagination_style',
								'value'   => array( '', 'ajax' ),
							),
							'group'      => 'Style',
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Horizontal Align', 'porto-functionality' ),
							'param_name' => 'p_align',
							'value'      => array(
								__( 'Default', 'porto-functionality' ) => '',
								__( 'Left', 'porto-functionality' ) => 'flex-start',
								__( 'Center', 'porto-functionality' ) => 'center',
								__( 'Right', 'porto-functionality' ) => 'flex-end',
							),
							'dependency' => array(
								'element' => 'pagination_style',
								'value'   => array( '', 'ajax' ),
							),
							'selectors'  => array(
								'{{WRAPPER}} .pagination' => 'justify-content: {{VALUE}};',
							),
							'group'      => 'Style',
						),
						array(
							'type'       => 'porto_dimension',
							'heading'    => __( 'Set custom margin of pagination part.', 'porto-functionality' ),
							'param_name' => 'p_margin',
							'value'      => '',
							'dependency' => array(
								'element' => 'pagination_style',
								'value'   => array( '', 'ajax' ),
							),
							'selectors'  => array(
								'{{WRAPPER}} .pagination-wrap' => 'margin-top: {{TOP}};margin-right: {{RIGHT}};margin-bottom: {{BOTTOM}};margin-left: {{LEFT}};',
							),
							'group'      => 'Style',
						),

						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'lm_style',
							'text'       => __( 'Load More Button Style', 'porto-functionality' ),
							'dependency' => array(
								'element' => 'pagination_style',
								'value'   => 'load_more',
							),
							'group'      => 'Style',
						),
						array(
							'type'       => 'dropdown',
							'heading'    => __( 'Width', 'porto-functionality' ),
							'param_name' => 'lm_width',
							'value'      => array(
								'100%' => '',
								'auto' => 'auto',
							),
							'dependency' => array(
								'element' => 'pagination_style',
								'value'   => 'load_more',
							),
							'selectors'  => array(
								'{{WRAPPER}} .load-more .next' => 'width: {{VALUE}};',
							),
							'group'      => 'Style',
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Typography', 'porto-functionality' ),
							'param_name' => 'lm_typography',
							'dependency' => array(
								'element' => 'pagination_style',
								'value'   => 'load_more',
							),
							'selectors'  => array(
								'{{WRAPPER}} .load-more .next',
							),
							'group'      => 'Style',
						),
						array(
							'type'        => 'porto_dimension',
							'heading'     => __( 'Padding', 'porto-functionality' ),
							'description' => __( 'Controls padding value of button.', 'porto-functionality' ),
							'param_name'  => 'lm_padding',
							'value'       => '',
							'dependency'  => array(
								'element' => 'pagination_style',
								'value'   => 'load_more',
							),
							'selectors'   => array(
								'{{WRAPPER}} .load-more .next' => 'padding-top: {{TOP}};padding-right: {{RIGHT}};padding-bottom: {{BOTTOM}};padding-left: {{LEFT}};',
							),
							'group'       => 'Style',
						),
						array(
							'type'        => 'number',
							'heading'     => __( 'Spacing (px)', 'porto-functionality' ),
							'description' => __( 'Controls the spacing of load more button.', 'porto-functionality' ),
							'param_name'  => 'lm_spacing',
							'suffix'      => 'px',
							'dependency'  => array(
								'element' => 'pagination_style',
								'value'   => 'load_more',
							),
							'selectors'   => array(
								'{{WRAPPER}} .pagination-wrap' => 'margin-top: {{VALUE}}px;',
							),
							'group'       => 'Style',
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Color', 'porto-functionality' ),
							'description' => __( 'Controls the color of the button.', 'porto-functionality' ),
							'param_name'  => 'lm_color',
							'dependency'  => array(
								'element' => 'pagination_style',
								'value'   => 'load_more',
							),
							'selectors'   => array(
								'{{WRAPPER}} .load-more .next' => 'color: {{VALUE}};',
							),
							'group'       => 'Style',
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Hover Color', 'porto-functionality' ),
							'description' => __( 'Controls the hover color of the button.', 'porto-functionality' ),
							'param_name'  => 'lm_color_hover',
							'dependency'  => array(
								'element' => 'pagination_style',
								'value'   => 'load_more',
							),
							'selectors'   => array(
								'{{WRAPPER}} .load-more .next:hover' => 'color: {{VALUE}};',
							),
							'group'       => 'Style',
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Background Color', 'porto-functionality' ),
							'description' => __( 'Controls the background color of the button.', 'porto-functionality' ),
							'param_name'  => 'lm_back_color',
							'dependency'  => array(
								'element' => 'pagination_style',
								'value'   => 'load_more',
							),
							'selectors'   => array(
								'{{WRAPPER}} .load-more .next' => 'background-color: {{VALUE}};',
							),
							'group'       => 'Style',
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Hover Background Color', 'porto-functionality' ),
							'description' => __( 'Controls the hover background color of the button.', 'porto-functionality' ),
							'param_name'  => 'lm_back_color_hover',
							'dependency'  => array(
								'element' => 'pagination_style',
								'value'   => 'load_more',
							),
							'selectors'   => array(
								'{{WRAPPER}} .load-more .next:hover' => 'background-color: {{VALUE}};',
							),
							'group'       => 'Style',
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Border Color', 'porto-functionality' ),
							'description' => __( 'Controls the border color of the button.', 'porto-functionality' ),
							'param_name'  => 'lm_border_color',
							'dependency'  => array(
								'element' => 'pagination_style',
								'value'   => 'load_more',
							),
							'selectors'   => array(
								'{{WRAPPER}} .load-more .next' => 'border-color: {{VALUE}};',
							),
							'group'       => 'Style',
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Hover Border Color', 'porto-functionality' ),
							'description' => __( 'Controls the hover border color of the button.', 'porto-functionality' ),
							'param_name'  => 'lm_border_color_hover',
							'dependency'  => array(
								'element' => 'pagination_style',
								'value'   => 'load_more',
							),
							'selectors'   => array(
								'{{WRAPPER}} .load-more .next:hover' => 'border-color: {{VALUE}};',
							),
							'group'       => 'Style',
						),

						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'filter_style',
							'text'       => __( 'Filters Style', 'porto-functionality' ),
							'dependency' => array(
								'element'   => 'category_filter',
								'not_empty' => true,
							),
							'group'      => 'Style',
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Alignment', 'porto-functionality' ),
							'param_name'  => 'filter_align',
							'description' => __( 'Controls filters\'s alignment. Choose from Left, Center, Right.', 'porto-functionality' ),
							'value'       => array(
								__( 'Default', 'porto-functionality' ) => '',
								__( 'Left', 'porto-functionality' ) => 'flex-start',
								__( 'Center', 'porto-functionality' ) => 'center',
								__( 'Right', 'porto-functionality' ) => 'flex-end',
							),
							'dependency'  => array(
								'element'   => 'category_filter',
								'not_empty' => true,
							),
							'selectors'   => array(
								'{{WRAPPER}} .sort-source' => 'justify-content: {{VALUE}};',
							),
							'group'       => 'Style',
						),
						array(
							'type'        => 'number',
							'heading'     => __( 'Between Spacing (px)', 'porto-functionality' ),
							'description' => __( 'Controls the spacing between filters.', 'porto-functionality' ),
							'param_name'  => 'filter_between_spacing',
							'suffix'      => 'px',
							'dependency'  => array(
								'element'   => 'category_filter',
								'not_empty' => true,
							),
							'selectors'   => array(
								'{{WRAPPER}} .sort-source li' => 'margin-' . $right . ': {{VALUE}}px; margin-bottom: {{VALUE}}px;',
							),
							'group'       => 'Style',
						),
						array(
							'type'        => 'number',
							'heading'     => __( 'Bottom Spacing (px)', 'porto-functionality' ),
							'description' => __( 'Controls the spacing of the filters.', 'porto-functionality' ),
							'param_name'  => 'filter_spacing',
							'suffix'      => 'px',
							'dependency'  => array(
								'element'   => 'category_filter',
								'not_empty' => true,
							),
							'selectors'   => array(
								'{{WRAPPER}} .sort-source' => 'margin-bottom: {{VALUE}}px;',
							),
							'group'       => 'Style',
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Typography', 'porto-functionality' ),
							'param_name' => 'filter_typography',
							'dependency' => array(
								'element'   => 'category_filter',
								'not_empty' => true,
							),
							'selectors'  => array(
								'{{WRAPPER}} .sort-source a',
							),
							'group'      => 'Style',
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Item Background Color', 'porto-functionality' ),
							'description' => __( 'Controls the item\'s background color.', 'porto-functionality' ),
							'param_name'  => 'filter_normal_bgc',
							'dependency'  => array(
								'element'   => 'category_filter',
								'not_empty' => true,
							),
							'selectors'   => array(
								'{{WRAPPER}} .sort-source a' => 'background-color: {{VALUE}};',
							),
							'group'       => 'Style',
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Item Color', 'porto-functionality' ),
							'description' => __( 'Controls the item\'s color.', 'porto-functionality' ),
							'param_name'  => 'filter_normal_color',
							'dependency'  => array(
								'element'   => 'category_filter',
								'not_empty' => true,
							),
							'selectors'   => array(
								'{{WRAPPER}} .sort-source a' => 'color: {{VALUE}};',
							),
							'group'       => 'Style',
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Item Active Background Color', 'porto-functionality' ),
							'description' => __( 'Controls the item\'s active and hover background color.', 'porto-functionality' ),
							'param_name'  => 'filter_active_bgc',
							'dependency'  => array(
								'element'   => 'category_filter',
								'not_empty' => true,
							),
							'selectors'   => array(
								'{{WRAPPER}} .sort-source li.active > a, {{WRAPPER}} .sort-source a:hover, {{WRAPPER}} .sort-source a:focus' => 'background-color: {{VALUE}};',
							),
							'group'       => 'Style',
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Item Active Color', 'porto-functionality' ),
							'description' => __( 'Controls the item\'s active and hover color.', 'porto-functionality' ),
							'param_name'  => 'filter_active_color',
							'dependency'  => array(
								'element'   => 'category_filter',
								'not_empty' => true,
							),
							'selectors'   => array(
								'{{WRAPPER}} .sort-source li.active > a, {{WRAPPER}} .sort-source a:hover, {{WRAPPER}} .sort-source a:focus' => 'color: {{VALUE}};',
							),
							'group'       => 'Style',
						),

						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'nothing_style',
							'text'       => __( 'Found Nothing', 'porto-functionality' ),
							'dependency' => array(
								'element'   => 'post_found_nothing',
								'not_empty' => true,
							),
							'group'      => 'Style',
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Typography', 'porto-functionality' ),
							'param_name' => 'nothing_tg',
							'dependency' => array(
								'element'   => 'post_found_nothing',
								'not_empty' => true,
							),
							'selectors'  => array(
								'{{WRAPPER}} .nothing-found-message',
							),
							'group'      => 'Style',
						),
						array(
							'type'       => 'colorpicker',
							'heading'    => __( 'Color', 'porto-functionality' ),
							'param_name' => 'nothing_clr',
							'dependency' => array(
								'element'   => 'post_found_nothing',
								'not_empty' => true,
							),
							'selectors'  => array(
								'{{WRAPPER}} .nothing-found-message' => 'color: {{VALUE}};',
							),
							'group'      => 'Style',
						),
					),
				)
			);
		}

		/**
		 * render posts grid
		 *
		 * @since 2.3.0
		 */
		public function render_posts_grid( $atts, $content = null, $shortcode = 'porto_tb_posts' ) {
			if ( $template = porto_shortcode_template( 'porto_posts_grid' ) ) {
				ob_start();
				$internal_css = '';
				if ( defined( 'WPB_VC_VERSION' ) ) {
					// Shortcode class
					$shortcode_class = ' wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
						$atts,
						$shortcode,
						array(
							array(
								'param_name' => 'spacing',
								'selectors'  => true,
							),
							array(
								'param_name' => 'p_align',
								'selectors'  => true,
							),
							array(
								'param_name' => 'p_margin',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_width',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_typography',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_padding',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_spacing',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_back_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_border_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_color_hover',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_back_color_hover',
								'selectors'  => true,
							),
							array(
								'param_name' => 'lm_border_color_hover',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_align',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_between_spacing',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_spacing',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_typography',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_normal_bgc',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_normal_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_active_bgc',
								'selectors'  => true,
							),
							array(
								'param_name' => 'filter_active_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nothing_tg',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nothing_clr',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_pos_top',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_pos_bottom',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_pos_left',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_pos_right',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_br_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_abr_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_bg_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'dots_abg_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_fs',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_width',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_height',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_br',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_h_pos',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_v_pos',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_h_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_bg_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_h_bg_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_br_color',
								'selectors'  => true,
							),
							array(
								'param_name' => 'nav_h_br_color',
								'selectors'  => true,
							),
						)
					);
					   // Frontend editor
					if ( isset( $_REQUEST['vc_editable'] ) && ( true == $_REQUEST['vc_editable'] ) ) {
						$style_array = apply_filters( 'porto_shortcode_render_internal_css', $shortcode, $atts );
						if ( is_array( $style_array ) ) {
							foreach ( $style_array as $key => $value ) {
								if ( 'responsive' == $key ) {
									$internal_css .= $value;
								} else {
									$internal_css .= $key . '{' . $value . '}';
								}
							}
						}
					}

					if ( ( function_exists( 'porto_vc_is_inline' ) && porto_vc_is_inline() ) || is_singular( PortoBuilders::BUILDER_SLUG ) ) {
						if ( 'porto_tb_archives' == $shortcode ) {
							if ( empty( $atts ) ) {
								$atts = array();
							}
							   $atts['post_type'] = 'product';
							if ( empty( $atts['orderby'] ) ) {
								$atts['orderby'] = wc_get_loop_prop( 'is_search' ) ? 'relevance' : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
							}
							if ( empty( $atts['order'] ) ) {
								$atts['order'] = 'ASC';
							}
							if ( empty( $atts['count'] ) ) {
								$atts['count'] = apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page', 12 ) );
							}

							global $porto_settings;
							$porto_settings['shop_pg_type'] = isset( $atts['pagination_style'] ) ? $atts['pagination_style'] : '';

							if ( empty( $atts['pagination_style'] ) ) {
									  $atts['pagination_style'] = '1';
							} elseif ( 'none' == $atts['pagination_style'] ) {
								$atts['pagination_style'] = '';
							}
							$atts['filter_cat_tax'] = 'product_cat';
							unset( $atts['shortcode_type'] );
						}
					}
				}
				include $template;
				$result = ob_get_clean();
				if ( $result && $internal_css ) {
					$first_tag_index = strpos( $result, '>' );
					if ( $first_tag_index ) {
						$internal_css = porto_filter_inline_css( $internal_css, false );
						if ( $internal_css ) {
							$result = substr( $result, 0, $first_tag_index + 1 ) . '<style>' . wp_strip_all_tags( $internal_css ) . '</style>' . substr( $result, $first_tag_index + 1 );
						}
					}
				}
				return $result;
			}
		}

		/**
		 * Render block
		 *
		 * @since 2.3.0
		 */
		protected function render_block( $atts, $block_name, $content = null ) {
			ob_start();
			$should_save_global = false;
			if ( wp_is_json_request() && ( empty( $_POST['action'] ) || 'elementor_ajax' != $_POST['action'] ) ) { // in block editor
				$post = Porto_Func_Dynamic_Tags_Content::get_instance()->get_dynamic_content_data( false, $atts );
				if ( ! $post ) {
					return;
				}
				$should_save_global      = isset( $atts['content_type'] ) ? $atts['content_type'] : 'post';
				$original_query          = $GLOBALS['wp_query'];
				$original_queried_object = $GLOBALS['wp_query']->queried_object;
				if ( 'term' == $should_save_global ) {
					$original_is_tax     = $GLOBALS['wp_query']->is_tax;
					$original_is_archive = $GLOBALS['wp_query']->is_archive;

					$GLOBALS['wp_query']->queried_object = $post;
					$GLOBALS['wp_query']->is_tax         = true;
					$GLOBALS['wp_query']->is_archive     = true;
				} else {
					$original_post = $GLOBALS['post'];

					$GLOBALS['post'] = $post;
					setup_postdata( $GLOBALS['post'] );
					$GLOBALS['wp_query']->queried_object = $GLOBALS['post'];

					if ( 'product' == $should_save_global ) {
						$GLOBALS['product'] = wc_get_product( $post->ID );
					}
				}
			}
			include PORTO_BUILDERS_PATH . 'elements/type/views/' . $block_name . '.php';

			if ( 'term' == $should_save_global ) {
				$GLOBALS['wp_query']                 = $original_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$GLOBALS['wp_query']->queried_object = $original_queried_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$GLOBALS['wp_query']->is_tax         = $original_is_tax; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$GLOBALS['wp_query']->is_archive     = $original_is_archive; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			} elseif ( $should_save_global ) {
				// Restore global data.
				$GLOBALS['post']                     = $original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$GLOBALS['wp_query']                 = $original_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$GLOBALS['wp_query']->queried_object = $original_queried_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

				if ( 'product' == $should_save_global ) {
					unset( $GLOBALS['product'] );
				}
			}

			return ob_get_clean();
		}

		/**
		 * add style options css class
		 *
		 * @since 2.3.0
		 */
		public function elements_wrap_class_filter( $class_string, $atts, $name ) {
			if ( 'heading' == $name && isset( $atts['dynamic_content']['source'] ) && 'post' == $atts['dynamic_content']['source'] && isset( $atts['dynamic_content']['post_info'] ) && 'title' == $atts['dynamic_content']['post_info'] ) {
				$class_string .= ' post-title';
			}
			if ( is_array( $atts ) ) {
				if ( ! empty( $atts['font_settings'] ) || 'porto-tb/porto-featured-image' == $name || ( ( 'porto-tb/porto-meta' == $name || 'porto-tb/porto-woo-buttons' == $name ) && isset( $atts['spacing'] ) && ( $atts['spacing'] || '0' == $atts['spacing'] ) ) ) {
					$extra_cls = 'porto-gb-' . PortoShortcodesClass::get_global_hashcode( $atts, $name );
					if ( false === strpos( $class_string, $extra_cls ) ) {
						$class_string .= ' ' . $extra_cls;
					}
				}
			}

			return $class_string;
		}

		/**
		 * Add porto classes to wishlist wrapper
		 *
		 * @since 2.3.0
		 */
		public function yith_ajax_add_cart_add_porto_classes( $params ) {
			if ( ! empty( $params['fragments'] ) ) {
				$fragments = isset( $_REQUEST['fragments'] ) ? wc_clean( $_REQUEST['fragments'] ) : false;
				if ( $fragments ) {
					foreach ( $fragments as $id => $options ) {
						if ( isset( $params['fragments'][ $id ] ) ) {
							   $fragment_content = $params['fragments'][ $id ];
							$pure_cls            = array_filter(
								explode( apply_filters( 'yith_wcwl_fragments_index_glue', '.' ), $id ),
								function ( $c ) {
									if ( 0 === strpos( $c, 'porto-' ) || 'exists' == $c || 'with-count' == $c ) {
										return false;
									}
									return true;
								}
							);
									 $pure_cls   = implode( ' ', $pure_cls );
							if ( false !== strpos( str_replace( array( ' exists', ' with-count' ), '', $fragment_content ), $pure_cls ) ) {
								$porto_cls                  = array_filter(
									explode( apply_filters( 'yith_wcwl_fragments_index_glue', '.' ), $id ),
									function ( $c ) {
										if ( 0 === strpos( $c, 'porto-' ) ) {
																  return true;
										}
										return false;
									}
								);
								$porto_cls                  = implode( ' ', $porto_cls );
								$params['fragments'][ $id ] = str_replace( 'class="yith-wcwl-add-to-wishlist ', 'class="yith-wcwl-add-to-wishlist ' . esc_attr( $porto_cls ) . ' ', $fragment_content );
							}
						}
					}
				}
			}
			return $params;
		}

		/**
		 * Add porto classes to wishlist wrapper
		 *
		 * @since 2.3.0
		 */
		public function yith_ajax_add_wishlist_add_porto_classes( $params, $atts ) {
			$fragments = isset( $_POST['fragments'] ) ? wc_clean( $_POST['fragments'] ) : false;
			if ( ! empty( $fragments ) ) {
				foreach ( $fragments as $id => $options ) {
					$options = YITH_WCWL_Frontend()->decode_fragment_options( $options );
					if ( empty( array_diff_assoc( $options, $atts ) ) ) {
						$fragment_content            = $fragments[ $id ];
						$porto_cls                   = array_filter(
							explode( apply_filters( 'yith_wcwl_fragments_index_glue', '.' ), $id ),
							function ( $c ) {
								if ( 0 === strpos( $c, 'porto-' ) ) {
									return true;
								}
								return false;
							}
						);
						$porto_cls                   = implode( ' ', $porto_cls );
						$params['container_classes'] = empty( $params['container_classes'] ) ? $porto_cls : $params['container_classes'] . ' ' . $porto_cls;
					}
				}
			}
			return $params;
		}

		/**
		 * Enable Gutenberg editor only in WPBakery editor
		 *
		 * @since 2.3.0
		 */
		public function enable_gutenberg_regular( $editors, $post_type ) {
			if ( -1 === $this->enable_gutenberg( -1, $post_type ) ) {
				return $editors;
			}
			if ( is_array( $editors ) ) {
				$editors['gutenberg_editor'] = true;
				$editors['classic_editor']   = false;
			}

			return $editors;
		}

		/**
		 * Enable Gutenberg editor only in WPBakery editor
		 *
		 * @since 2.3.0
		 */
		public function enable_gutenberg( $result, $post_type ) {
			if ( PortoBuilders::BUILDER_SLUG != $post_type ) {
				return $result;
			}
			if ( ! $this->editor_builder_type ) {
				$this->post_id = is_singular() ? get_the_ID() : ( isset( $_GET['post'] ) ? (int) $_GET['post'] : ( isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : false ) );
				if ( ! $this->post_id ) {
					return $result;
				}
				$this->editor_builder_type = get_post_meta( $this->post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
			}
			if ( ! $this->editor_builder_type || 'type' != $this->editor_builder_type ) {
				return $result;
			}

			return true;
		}

		/**
		 * Filter main query to update posts per page
		 *
		 * @since 2.3.0
		 */
		public function filter_search_loop( $query ) {
			if ( ! is_admin() && $query->is_main_query() && ( $query->is_home() || $query->is_search() || $query->is_archive() ) ) {
				$post_type = isset( $query->query_vars ) && ! empty( $query->query_vars['post_type'] ) ? $query->query_vars['post_type'] : '';
				if ( ! $post_type ) {
					$post_types_exclude   = apply_filters( 'porto_condition_exclude_post_types', array( PortoBuilders::BUILDER_SLUG, 'attachment', 'elementor_library', 'page' ) );
					$available_post_types = get_post_types( array( 'public' => true ) );
					foreach ( $available_post_types as $p_type ) {
						if ( ! in_array( $p_type, $post_types_exclude ) && ( $query->is_post_type_archive( $p_type ) || $query->is_tax( get_object_taxonomies( $p_type ) ) ) ) {
							   $post_type = $p_type;
							   break;
						}
					}
				}
				if ( ! $post_type ) {
					$post_type = 'post';
				}
				if ( ! function_exists( 'porto_check_builder_condition' ) ) {
					return;
				}
				if ( 'product' == $post_type ) {
					$template_id = porto_check_builder_condition( 'shop' );
				} else {
					$template_id = porto_check_builder_condition( 'archive' );
				}
				if ( ! $template_id ) {
					return;
				}

				// check if has post type builder archive widget
				$query_vars = false;
				if ( defined( 'ELEMENTOR_VERSION' ) && ( get_post_meta( $template_id, '_elementor_edit_mode', true ) && ( $elements_data = get_post_meta( $template_id, '_elementor_data', true ) ) ) ) {
					$elements_data = json_decode( $elements_data, true );
					if ( ! $elements_data ) {
						return;
					}
					$query_vars = $this->parse_query_vars_elements( $elements_data );
				} else {
					$template_post = get_post( $template_id );
					$widgets       = array(
						'porto_tb_archives'   => '[porto_tb_archives',
						'porto_ab_posts_grid' => '[porto_ab_posts_grid',
					);
					$widget        = '';
					foreach ( $widgets as $key => $value ) {
						if ( false !== strpos( $template_post->post_content, $value ) ) {
							$widget = $key;
						}
					}
					if ( $template_post && ! empty( $widget ) ) {
						$pattern = get_shortcode_regex( array( $widget ) );
						if ( preg_match_all( '/' . $pattern . '/s', $template_post->post_content, $matches ) && isset( $matches[2] ) && in_array( $widget, $matches[2], true ) ) {
							$query_vars = shortcode_parse_atts( $matches[3][0] );
						}
					}
				}
				if ( empty( $query_vars ) ) {
					return;
				}
				// update query vars
				$query->set( 'paged', ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 0 );
				if ( empty( $_GET['count'] ) && ! empty( $query_vars['count'] ) && -1 !== (int) $query_vars['count'] ) {
					$query->set( 'posts_per_page', $query_vars['count'] );
				}
				if ( ! empty( $query_vars['orderby'] ) ) {
					if ( 'product' == $post_type && class_exists( 'WooCommerce' ) ) {
						$ordering_args = WC()->query->get_catalog_ordering_args( $query_vars['orderby'], empty( $query_vars['order'] ) ? '' : $query_vars['order'] );
						$query->set( 'orderby', $ordering_args['orderby'] );
						$query->set( 'order', $ordering_args['order'] );
						$query_vars['order'] = '';
					} else {
						$query->set( 'orderby', $query_vars['orderby'] );
					}
				}
				if ( ! empty( $query_vars['order'] ) ) {
					$query->set( 'order', $query_vars['order'] );
				}
				if ( 'product' == $post_type ) {
					global $porto_settings;
					$porto_settings['shop_pg_type'] = isset( $query_vars['pagination_style'] ) ? $query_vars['pagination_style'] : '';
				}

				remove_action( 'pre_get_posts', array( $this, 'filter_search_loop' ) );
			}
		}

		/**
		 * Get query vars from posts grid element in Elementor elements data
		 *
		 * @since 2.3.0
		 */
		private function parse_query_vars_elements( $elements_data ) {
			foreach ( $elements_data as $element_data ) {
				if ( ! empty( $element_data['elements'] ) ) {
					$call_result = $this->parse_query_vars_elements( $element_data['elements'] );
					if ( false !== $call_result ) {
						return $call_result;
					}
				} else {
					if ( isset( $element_data['widgetType'] ) && ( 'porto_sb_archives' == $element_data['widgetType'] || 'porto_archive_posts_grid' == $element_data['widgetType'] ) ) {
						$settings = $element_data['settings'];
						$result   = array();
						if ( ! empty( $settings['count'] ) && ! empty( $settings['count']['size'] ) ) {
							$result['count'] = (int) $settings['count']['size'];
						}
						if ( ! empty( $settings['orderby'] ) ) {
							$result['orderby'] = sanitize_text_field( $settings['orderby'] );
						}
						if ( ! empty( $settings['order'] ) ) {
							   $result['order'] = sanitize_text_field( $settings['order'] );
						}
						if ( isset( $settings['pagination_style'] ) ) {
							   $result['pagination_style'] = sanitize_text_field( $settings['pagination_style'] );
						}

						return $result;
					}
				}
			}
			return false;
		}

		/**
		 * Generate internal styles
		 *
		 * @since 2.3.0
		 */
		protected function include_style( $blocks ) {
			if ( empty( $blocks ) ) {
				return;
			}

			foreach ( $blocks as $block ) {
				if ( ! empty( $block['blockName'] ) && in_array( $block['blockName'], array( 'porto-tb/porto-content', 'porto/porto-heading', 'porto/porto-button', 'porto-tb/porto-woo-price', 'porto-tb/porto-woo-rating', 'porto-tb/porto-woo-stock', 'porto-tb/porto-woo-desc', 'porto-tb/porto-woo-buttons', 'porto-tb/porto-meta' ) ) ) {
					$atts = empty( $block['attrs'] ) ? array() : $block['attrs'];

					if ( 'porto-tb/porto-meta' == $block['blockName'] && isset( $atts['spacing'] ) && ( $atts['spacing'] || '0' == $atts['spacing'] ) ) {
						$atts['selector'] = '.porto-gb-' . PortoShortcodesClass::get_global_hashcode( $atts, str_replace( 'porto/porto-', '', $block['blockName'] ) ) . ' .porto-tb-icon';
						include PORTO_BUILDERS_PATH . '/elements/type/style-meta.php';
					}

					if ( 'porto-tb/porto-woo-buttons' == $block['blockName'] && isset( $atts['spacing'] ) && ( $atts['spacing'] || '0' == $atts['spacing'] ) ) {
						unset( $atts['selector'] );
						$atts['selector'] = '.porto-gb-' . PortoShortcodesClass::get_global_hashcode( $atts, str_replace( 'porto/porto-', '', $block['blockName'] ) );
						include PORTO_BUILDERS_PATH . '/elements/type/style-woo-buttons.php';
					}
				} elseif ( ! empty( $block['blockName'] ) && 'porto-tb/porto-featured-image' == $block['blockName'] ) {
					$atts = empty( $block['attrs'] ) ? array() : $block['attrs'];
					if ( ! empty( $atts['show_content_hover'] ) || ! empty( $atts['zoom'] ) ) {
						$atts['selector'] = '.porto-gb-' . PortoShortcodesClass::get_global_hashcode( $atts, str_replace( 'porto/porto-', '', $block['blockName'] ) );
						include PORTO_BUILDERS_PATH . '/elements/type/style-featured-image.php';
					}
				}
				if ( ! empty( $block['innerBlocks'] ) ) {
					$this->include_style( $block['innerBlocks'] );
				}
			}
		}
	}
endif;

PortoBuildersType::get_instance();
