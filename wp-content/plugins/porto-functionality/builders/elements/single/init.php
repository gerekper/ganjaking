<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Single Builder
 *
 * @since 2.3.0
 */
use Elementor\Controls_Manager;
if ( ! class_exists( 'PortoBuildersSingle' ) ) :
	class PortoBuildersSingle {

		/**
		 * The Shortcodes
		 *
		 * @access private
		 * @var array $shortcodes
		 * @since 2.3.0
		 */
		private $shortcodes = array(
			'image',
			'author-box',
			'meta',
			'comments',
			'related',
			'navigation',
			'share',
			'post-format',
		);

		/**
		 * Edit Post
		 *
		 * @access public
		 * @var object $edit_post
		 * @since 2.3.0
		 */
		public $edit_post = null;

		/**
		 * Edit Post Type
		 *
		 * @access public
		 * @var object $edit_post_type
		 * @since 2.3.0
		 */
		public $edit_post_type = null;

		/**
		 * Global Instance Objects
		 *
		 * @var array $instances
		 * @since 2.3.0
		 * @access private
		 */
		private static $instance = null;

		/**
		 * Is Single Builder Page
		 *
		 * @access protected
		 * @var object $is_single
		 * @since 2.3.0
		 */
		protected $is_single = false;

		/**
		 * WPB Post Elements Flag
		 *
		 * @access private
		 * @var object $wpb_display_post_page_elements
		 * @since 2.3.0
		 */
		private $wpb_display_post_page_elements = null;

		public static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->init();
		}

		/**
		 * Init Single Builder
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function init() {

			$this->shortcodes = apply_filters( 'porto_builder_single_shortcodes', $this->shortcodes );

			if ( defined( 'WPB_VC_VERSION' ) ) {
				add_action(
					'template_redirect',
					function () {
						$should_add_shortcodes = false;
						if ( ( is_singular( PortoBuilders::BUILDER_SLUG ) && 'single' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) || ! empty( $_GET['vcv-ajax'] ) || ( function_exists( 'porto_is_ajax' ) && porto_is_ajax() && ! empty( $_GET[ PortoBuilders::BUILDER_SLUG ] ) ) ) {
							$should_add_shortcodes = true;
						} elseif ( function_exists( 'porto_check_builder_condition' ) && porto_check_builder_condition( 'single' ) ) {
								$should_add_shortcodes = true;
						}

						if ( $should_add_shortcodes ) {
							foreach ( $this->shortcodes as $shortcode ) {
								add_shortcode( 'porto_single_' . str_replace( '-', '_', $shortcode ), array( $this, 'shortcode_single_' . str_replace( '-', '_', $shortcode ) ) );
							}
						}
					}
				);

				add_action(
					'admin_init',
					function () {
						$should_add_shortcodes = false;
						if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'vc_save' == $_REQUEST['action'] ) {
							$should_add_shortcodes = true;
						} elseif ( isset( $_POST['action'] ) && 'editpost' == $_POST['action'] && isset( $_POST['post_type'] ) && PortoBuilders::BUILDER_SLUG == $_POST['post_type'] ) {
							$should_add_shortcodes = true;
						}

						if ( $should_add_shortcodes ) {
							foreach ( $this->shortcodes as $shortcode ) {
								add_shortcode( 'porto_single_' . str_replace( '-', '_', $shortcode ), array( $this, 'shortcode_single_' . str_replace( '-', '_', $shortcode ) ) );
							}
						}
					}
				);
				if ( is_admin() || ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
					add_action( 'vc_after_init', array( $this, 'wpb_custom_single_shortcodes' ) );
				}

				add_filter( 'vc_autocomplete_porto_single_related_builder_id_callback', 'builder_id_callback' );
				add_filter( 'vc_autocomplete_porto_single_related_builder_id_render', 'builder_id_render' );
				//apply changed post
				add_action( 'wp_ajax_porto_single_builder_preview_wpb_apply', array( $this, 'apply_preview_wpb_post' ) );
			}

			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				if ( is_admin() && isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) {
					add_action(
						'elementor/elements/categories_registered',
						function( $self ) {
							$self->add_category(
								'porto-single',
								array(
									'title'  => __( 'Porto Single Builder', 'porto-functionality' ),
									'active' => false,
								)
							);
						}
					);
				}
				add_action( 'elementor/widgets/register', array( $this, 'elementor_custom_single_shortcodes' ), 10, 1 );
				//apply changed post
				add_action( 'wp_ajax_porto_single_builder_preview_apply', array( $this, 'apply_preview_post' ) );
				add_action( 'elementor/documents/register_controls', array( $this, 'register_elementor_preview_controls' ) );
			}

		}

		/**
		 * Sigle Post Feature Image Widget
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function shortcode_single_image( $atts ) {
			if ( ( ! $this->restore_global_single_variable() && function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) || ( ! $this->restore_global_single_variable() && function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
				return null;
			}
			global $porto_settings;
			$share_position         = ! empty( $porto_settings['post-share-position'] ) ? $porto_settings['post-share-position'] : '';
			$args                   = array();
			$el_class               = '';
			$internal_css           = '';
			$args['carousel_class'] = '';

			$args['navigation'] = false;
			if ( isset( $atts['navigation'] ) && ( true == $atts['navigation'] || 'yes' == $atts['navigation'] ) ) {
				$args['navigation'] = true;

				if ( ! empty( $atts['nav_pos'] ) ) {
					$args['carousel_class'] .= esc_attr( $atts['nav_pos'] ) . ' ';
				}
				if ( ! empty( $atts['nav_pos2'] ) ) {
					$args['carousel_class'] .= esc_attr( $atts['nav_pos2'] ) . ' ';
				}
				if ( ! empty( $atts['nav_type'] ) ) {
					$args['carousel_class'] .= esc_attr( $atts['nav_type'] ) . ' ';
				}
				if ( ! empty( $atts['show_nav_hover'] ) ) {
					$args['carousel_class'] .= ' show-nav-hover ';
				}
			}
			$args['pagination'] = false;
			if ( isset( $atts['pagination'] ) && ( true == $atts['pagination'] || 'yes' == $atts['pagination'] ) ) {
				$args['pagination'] = true;
				if ( ! empty( $atts['dots_style'] ) ) {
					$args['carousel_class'] .= esc_attr( $atts['dots_style'] ) . ' ';
				}
				if ( ! empty( $atts['dots_pos'] ) ) {
					$args['carousel_class'] .= esc_attr( $atts['dots_pos'] );
				}
			}
			if ( ! empty( $atts['hover_effect'] ) ) {
				// hover effect
				$args['hover_effect'] = $atts['hover_effect'];
			}
			if ( ! empty( $atts['show_thumbnail'] ) ) {
				// add thumnail image
				$args['show_thumbnail'] = $atts['show_thumbnail'];
				if ( isset( $atts['thumbnail_space'] ) ) {
					$args['thumbnail_space'] = $atts['thumbnail_space'];
				}

				if ( ! empty( $atts['image_thumbnail_size'] ) ) {
					$args['image_thumbnail_size'] = $atts['image_thumbnail_size'];
				}
			}
			$args['grid_column'] = ! empty( $atts['grid_column'] ) ? $atts['grid_column'] : '3';

			if ( ! empty( $atts['share_position'] ) && ( true == $atts['share_position'] || 'yes' == $atts['share_position'] ) ) {
				$porto_settings['post-share-position'] = 'advance';
			} else {
				$porto_settings['post-share-position'] = '';
			}
			if ( ! empty( $atts['follow_meta'] ) && ( 'yes' == $atts['follow_meta'] || $atts['follow_meta'] ) ) {
				$slideshow_type = get_post_meta( get_the_ID(), 'slideshow_type', true );
				if ( ! $slideshow_type ) {
					$slideshow_type = 'images';
				}
				$porto_settings['post-zoom'] = true;
			} else {
				$slideshow_type = ! empty( $atts['show_type'] ) ? $atts['show_type'] : 'images';
				if ( 'masonry' == $slideshow_type ) {
					$slideshow_type  = 'grid';
					$args['masonry'] = true;
				}
				$args['image_size']          = ! empty( $atts['thumbnail_size'] ) ? $atts['thumbnail_size'] : 'medium';
				$image_zoom                  = ! empty( $porto_settings['post-zoom'] ) ? $porto_settings['post-zoom'] : '';
				$porto_settings['post-zoom'] = ! empty( $atts['popup'] ) ? ( 'yes' == $atts['popup'] || true == $atts['popup'] ) : false;
			}

			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				// Shortcode class
				$shortcode_class = ' wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					'porto_single_image',
					array(
						array(
							'param_name' => 'border_radius',
							'selectors'  => true,
						),
						array(
							'param_name' => 'overlay_bgc',
							'selectors'  => true,
						),
						array(
							'param_name' => 'icon_bgc',
							'selectors'  => true,
						),
						array(
							'param_name' => 'icon_clr',
							'selectors'  => true,
						),
						array(
							'param_name' => 'icon_width',
							'selectors'  => true,
						),
						array(
							'param_name' => 'icon_size',
							'selectors'  => true,
						),
						array(
							'param_name' => 'image_height',
							'selectors'  => true,
						),
						array(
							'param_name' => 'image_padding',
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
						array(
							'param_name' => 'icon_pos',
							'selectors'  => true,
						),
						array(
							'param_name' => 'thumbnail_space',
							'selectors'  => true,
						),
					)
				);
				$internal_css    = PortoShortcodesClass::generate_wpb_css( 'porto_single_image', $atts );
				if ( ! empty( $shortcode_class ) ) {
					$el_class .= ' wpb-image' . $shortcode_class;
				}

				if ( ! empty( $atts['icon_type'] ) ) {
					switch ( $atts['icon_type'] ) {
						case 'simpleline':
							if ( ! empty( $atts['icon_simpleline'] ) ) {
								$args['icon_cl'] = $atts['icon_simpleline'];
							}
							break;
						case 'porto':
							if ( ! empty( $atts['icon_porto'] ) ) {
								$args['icon_cl'] = $atts['icon_porto'];
							}
							break;
					}
				} elseif ( ! empty( $atts['icon_cl'] ) ) {
					$args['icon_cl'] = $atts['icon_cl'];
				}
			} elseif ( defined( 'ELEMENTOR_VERSION' ) && ! empty( $atts['page_builder'] ) ) {
				if ( isset( $atts['icon_cl'] ) && ! empty( $atts['icon_cl']['value'] ) ) {
					if ( isset( $atts['icon_cl']['library'] ) && ! empty( $atts['icon_cl']['value']['id'] ) ) {
						$args['icon_cl'] = $atts['icon_cl']['value']['id'];
					} else {
						$args['icon_cl'] = $atts['icon_cl']['value'];
					}
				}
			}
			if ( ! empty( $atts['el_class'] ) ) {
				$el_class .= ' ' . $atts['el_class'];
			}

			ob_start();
			if ( ! empty( $el_class ) ) {
				?>
				<div class="<?php echo esc_attr( $el_class ); ?>">
				<?php
			}
			porto_get_template_part( 'views/posts/post-media/' . $slideshow_type, null, $args );

			if ( ! empty( $el_class ) ) {
				?>
				</div>
				<?php
			}

			$this->reset_global_single_variable();

			$result = PortoShortcodesClass::generate_insert_css( ob_get_clean(), $internal_css );
			//retreive Porto Settings
			if ( ! empty( $image_zoom ) ) {
				$porto_settings['post-zoom'] = $image_zoom;
			}
			if ( ! empty( $share_position ) ) {
				$porto_settings['post-share-position'] = $share_position;
			}

			return $result;
		}

		/**
		 * Sigle Post Author Box Widget
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function shortcode_single_author_box( $atts ) {

			if ( ( ! $this->restore_global_single_variable() && function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) || ( ! $this->restore_global_single_variable() && function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
				return null;
			}
			global $porto_settings;
			$internal_css = '';
			$custom_css   = '';
			ob_start();
			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				// Shortcode class
				$shortcode_class = ' wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					'porto_single_author_box',
					array(
						array(
							'param_name' => 'author_icon_space',
							'selectors'  => true,
						),
						array(
							'param_name' => 'author_space',
							'selectors'  => true,
						),
						array(
							'param_name' => 'author_image_size',
							'selectors'  => true,
						),
						array(
							'param_name' => 'author_image_radius',
							'selectors'  => true,
						),
						array(
							'param_name' => 'heading_title_style',
							'selectors'  => true,
						),
						array(
							'param_name' => 'author_title_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'heading_name_style',
							'selectors'  => true,
						),
						array(
							'param_name' => 'author_name_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'heading_desc_style',
							'selectors'  => true,
						),
						array(
							'param_name' => 'author_desc_color',
							'selectors'  => true,
						),
					)
				);
				$internal_css    = PortoShortcodesClass::generate_wpb_css( 'porto_single_author_box', $atts );
				if ( ! empty( $shortcode_class ) ) {
					$custom_css .= ' wpb-post-author' . $shortcode_class;
				}
				if ( ! empty( $atts['el_class'] ) ) {
					$custom_css .= ' ' . $atts['el_class'];
				}
			}
			if ( ! empty( $custom_css ) ) {
				?>
				<div class="<?php echo esc_attr( $custom_css ); ?>">
				<?php
			}
			$author_icon                   = ! empty( $porto_settings['post-title-style'] ) ? $porto_settings['post-title-style'] : '';
			$post_author                   = ! empty( $porto_settings['post-author'] ) ? $porto_settings['post-author'] : '';
			$porto_settings['post-author'] = true;

			if ( ! empty( $atts['author_icon'] ) && ( ( 'yes' == $atts['author_icon'] ) || ( true == $atts['author_icon'] ) ) ) {
				$porto_settings['post-title-style'] = 'without-icon';
			} else {
				$porto_settings['post-title-style'] = '';
			}

			porto_get_template_part( 'views/posts/single/author' );

			if ( ! empty( $custom_css ) ) {
				?>
				</div>
				<?php
			}

			$result = PortoShortcodesClass::generate_insert_css( ob_get_clean(), $internal_css );
			//retreive Porto Settings
			if ( ! empty( $author_icon ) ) {
				$porto_settings['post-title-style'] = $author_icon;
			}
			if ( ! empty( $post_author ) ) {
				$porto_settings['post-author'] = $post_author;
			}
			return $result;
		}

		/**
		 * Sigle Post Navigation Widget
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function shortcode_single_navigation( $atts ) {
			if ( ( ! $this->restore_global_single_variable() && function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) || ( ! $this->restore_global_single_variable() && function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
				return null;
			}
			$internal_css    = '';
			$shortcode_class = '';
			if ( ( function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) || ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
				$post_type = $this->edit_post_type;
			} else {
				$post_type = get_post_type();
			}
			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				// Shortcode class
				$shortcode_class = ' wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					'porto_single_navigation',
					array(
						array(
							'param_name' => 'nav_space',
							'selectors'  => true,
						),
						array(
							'param_name' => 'nav_typography',
							'selectors'  => true,
						),
						array(
							'param_name' => 'nav_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'nav_hover_color',
							'selectors'  => true,
						),
					)
				);
				$internal_css    = PortoShortcodesClass::generate_wpb_css( 'porto_single_navigation', $atts );
				if ( ! empty( $atts['nav_align'] ) ) {
					$internal_css .= ' .' . trim( $shortcode_class ) . '{ justify-content: ' . $atts['nav_align'] . '; }';
				}
				$prev_icon = ! empty( $atts['nav_icon_prev'] ) ? $atts['nav_icon_prev'] : 'porto-icon-chevron-left';
				$next_icon = ! empty( $atts['nav_icon_next'] ) ? $atts['nav_icon_next'] : 'porto-icon-chevron-right';
				if ( ! empty( $atts['el_class'] ) ) {
					$shortcode_class .= ' ' . $atts['el_class'];
				}
			} elseif ( defined( 'ELEMENTOR_VERSION' ) ) {
				$prev_icon = ! empty( $atts['nav_icon_prev']['value'] ) ? $atts['nav_icon_prev']['value'] : 'porto-icon-chevron-left';
				$next_icon = ! empty( $atts['nav_icon_next']['value'] ) ? $atts['nav_icon_next']['value'] : 'porto-icon-chevron-right';
			}

			ob_start();
			?>
			<div class="single-navigation d-flex<?php echo esc_attr( ! empty( $shortcode_class ) ? $shortcode_class : '' ); ?>">
				<style><?php porto_filter_inline_css( wp_strip_all_tags( $internal_css ) ); ?></style>
				<?php previous_post_link( '%link', '<div data-bs-tooltip title="' . esc_attr__( 'Previous', 'porto-functionality' ) . '" class="' . $post_type . '-nav-prev"><i class="' . $prev_icon . '"></i></div>' ); ?>
				<?php next_post_link( '%link', '<div data-bs-tooltip title="' . esc_attr__( 'Next', 'porto-functionality' ) . '" class="' . $post_type . '-nav-next"><i class="' . $next_icon . '"></i></div>' ); ?>
			</div>

			<?php
			$this->reset_global_single_variable();
			$result = ob_get_clean();
			return $result;
		}

		/**
		 * Sigle Post Meta Widget
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function shortcode_single_meta( $atts ) {
			if ( ( ! $this->restore_global_single_variable() && function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) || ( ! $this->restore_global_single_variable() && function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
				return null;
			}
			global $porto_settings;
			$internal_css    = '';
			$el_class        = '';
			$shortcode_class = '';
			$args            = array();
			$post_metas      = ! empty( $porto_settings['post-metas'] ) ? $porto_settings['post-metas'] : '';
			if ( ! empty( $atts['post-metas'] ) ) {
				$porto_settings['post-metas'] = 'string' === gettype( $atts['post-metas'] ) ? explode( ',', $atts['post-metas'] ) : $atts['post-metas'];
			} else {
				$porto_settings['post-metas'] = array();
			}
			if ( ! empty( $atts['show_divider'] ) && ( 'yes' == $atts['show_divider'] || true == $atts['show_divider'] ) ) {
				$el_class = 'post-meta-divider';
			}
			if ( ! empty( $atts['elementor_id'] ) ) {
				$shortcode_class = "elementor-element-{$atts['elementor_id']}";
			}
			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				// Shortcode class
				$shortcode_class = ' wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					'porto_single_meta',
					array(
						array(
							'param_name' => 'meta_space',
							'selectors'  => true,
						),
						array(
							'param_name' => 'icon_space',
							'selectors'  => true,
						),
						array(
							'param_name' => 'meta_style',
							'selectors'  => true,
						),
						array(
							'param_name' => 'meta_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'meta_hover_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'divider_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'divider_space',
							'selectors'  => true,
						),
					)
				);
				$el_class       .= $shortcode_class;
				if ( ! empty( $atts['meta_align'] ) ) {
					$internal_css .= ' .' . trim( $shortcode_class ) . " { text-align:{$atts['meta_align']}; }";
				}
				$internal_css .= PortoShortcodesClass::generate_wpb_css( 'porto_single_meta', $atts );
				if ( ! empty( $atts['el_class'] ) ) {
					$el_class .= ' ' . $atts['el_class'];
				}
			}
			$args['el_class']  = $el_class;
			$args['hide_icon'] = isset( $atts['hide_icon'] ) ? ( 'yes' == $atts['hide_icon'] || true == $atts['hide_icon'] ) : false;

			if ( $args['hide_icon'] ) {
				$internal_css .= ' .' . trim( $shortcode_class ) . ' .meta-date > i { display: none; }';
			} else {
				$internal_css .= ' .' . trim( $shortcode_class ) . ' .meta-like span > i { display: inline-block; }';
			}

			if ( ! empty( $porto_settings['post-metas'] ) && is_array( $porto_settings['post-metas'] ) && in_array( 'date', $porto_settings['post-metas'] ) ) {
				$args['show_date'] = true;
			}
			$args['hide_by'] = isset( $atts['hide_by'] ) ? ( 'yes' == $atts['hide_by'] || true == $atts['hide_by'] ) : false;
			ob_start();
			porto_get_template_part( 'views/posts/single/meta', null, $args );

			//retreive Porto Settings
			if ( ! empty( $post_metas ) ) {
				$porto_settings['post-metas'] = $post_metas;
			}
			$this->reset_global_single_variable();
			$result = PortoShortcodesClass::generate_insert_css( ob_get_clean(), $internal_css );
			return $result;
		}

		/**
		 * Sigle Post Comments Widget
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function shortcode_single_comments( $atts ) {
			if ( ( ! $this->restore_global_single_variable() && function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) || ( ! $this->restore_global_single_variable() && function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
				return null;
			}
			ob_start();
			$internal_css = '';
			$el_class     = '';
			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				// Shortcode class
				$shortcode_class = ' wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					'porto_single_comments',
					array(
						array(
							'param_name' => 'comments_spacing',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comments_reply',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comments_form',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_image_between',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_image_width',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_image_radius',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_title',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_title_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_name_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_name',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_setting',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_setting_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_setting_h_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_date',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_date_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_text',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_text_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'form_heading',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_form_heading_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_reply',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_form_reply_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_form_reply_color_hover',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_form_reply_space',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_label',
							'selectors'  => true,
						),
						array(
							'param_name' => 'comment_form_label_color',
							'selectors'  => true,
						),
					)
				);
				$internal_css    = PortoShortcodesClass::generate_wpb_css( 'porto_single_comments', $atts );
				if ( ! empty( $shortcode_class ) ) {
					$el_class .= ' wpb-post-comments' . $shortcode_class;
				}
				if ( ! empty( $atts['el_class'] ) ) {
					$el_class .= ' ' . $atts['el_class'];
				}
			}
			if ( ! empty( $el_class ) ) {
				?>
				<div class="<?php echo esc_attr( $el_class ); ?>">
				<?php
			}
			global $post_layout, $porto_settings;
			$post_layout_type = ! empty( $porto_settings['post-layout'] ) ? $porto_settings['post-layout'] : 'full';
			$post_author_icon = ! empty( $porto_settings['post-title-style'] ) ? $porto_settings['post-title-style'] : '';

			if ( ! empty( $atts['comment_author_icon'] ) && ( 'yes' == $atts['comment_author_icon'] || true == $atts['comment_author_icon'] ) ) {
				$porto_settings['post-title-style'] = 'without-icon';
			} else {
				$porto_settings['post-title-style'] = 'with-icon';
			}

			$soft_mode = ! apply_filters( 'porto_legacy_mode', true );
			if ( $soft_mode ) {
				$post_layout = 'modern';
			}

			comments_template();
			if ( $soft_mode ) {
				$post_layout = $post_layout_type;
			}

			if ( ! empty( $post_author_icon ) ) {
				$porto_settings['post-title-style'] = $post_author_icon;
			}
			if ( ! empty( $el_class ) ) {
				?>
				</div>
				<?php
			}

			$this->reset_global_single_variable();

			$result = PortoShortcodesClass::generate_insert_css( ob_get_clean(), $internal_css );

			return $result;
		}

		/**
		 * Sigle Post Related Widget
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function shortcode_single_related( $atts ) {
			if ( ( ! $this->restore_global_single_variable() && function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) || ( ! $this->restore_global_single_variable() && function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
				return null;
			}
			if ( empty( $atts ) ) {
				$atts = array();
			}
			if ( $template = porto_shortcode_template( 'porto_posts_grid' ) ) {
				ob_start();
				$internal_css = '';
				if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
					// Shortcode class
					$shortcode_class = ' wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
						$atts,
						'porto_single_related',
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
					$internal_css = PortoShortcodesClass::generate_wpb_css( 'porto_single_related', $atts );
				} elseif ( defined( 'ELEMENTOR_VERSION' ) ) {
					if ( empty( $atts['spacing'] ) ) {
						$atts['spacing'] = '';
					}
					if ( ! empty( $atts['count'] ) && is_array( $atts['count'] ) ) {
						if ( isset( $atts['count']['size'] ) ) {
							$atts['count'] = $atts['count']['size'];
						} else {
							$atts['count'] = '';
						}
					}
				}

				if ( ( function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) || ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
					$atts['post_type']    = $this->edit_post_type;
					$atts['related_post'] = $this->edit_post->ID;
				} else {
					global $post;
					$atts['post_type']    = get_post_type();
					$atts['related_post'] = $post->ID;
				}
				include $template;

				$result = PortoShortcodesClass::generate_insert_css( ob_get_clean(), $internal_css );

				$this->reset_global_single_variable();

				return $result;
			}
		}

		/**
		 * Sigle Post Share Widget
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function shortcode_single_share( $atts ) {
			if ( ( ! $this->restore_global_single_variable() && function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) || ( ! $this->restore_global_single_variable() && function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
				return null;
			}
			global $porto_settings;
			$internal_css   = '';
			$el_class       = '';
			$share_position = ! empty( $porto_settings['post-share-position'] ) ? $porto_settings['post-share-position'] : '';
			$post_share     = ! empty( $porto_settings['post-share'] ) ? $porto_settings['post-share'] : '';
			$share_enable   = ! empty( $porto_settings['share-enable'] ) ? $porto_settings['share-enable'] : '';
			$with_icon      = ! empty( $porto_settings['post-title-style'] ) ? $porto_settings['post-title-style'] : '';

			ob_start();
			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				// Shortcode class
				$shortcode_class = ' wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					'porto_single_share',
					array(
						array(
							'param_name' => 'share_inline',
							'selectors'  => true,
						),
						array(
							'param_name' => 'hide_heading',
							'selectors'  => true,
						),
						array(
							'param_name' => 'icon_space',
							'selectors'  => true,
						),
						array(
							'param_name' => 'share_heading_style',
							'selectors'  => true,
						),
						array(
							'param_name' => 'title_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'title_space',
							'selectors'  => true,
						),
						array(
							'param_name' => 'share_icons',
							'selectors'  => true,
						),
						array(
							'param_name' => 'share_width',
							'selectors'  => true,
						),
						array(
							'param_name' => 'share_radius',
							'selectors'  => true,
						),
						array(
							'param_name' => 'share_space',
							'selectors'  => true,
						),
						array(
							'param_name' => 'share_icon_box_shadow',
							'selectors'  => true,
						),
						array(
							'param_name' => 'share_color_normal',
							'selectors'  => true,
						),
						array(
							'param_name' => 'share_bg_color_normal',
							'selectors'  => true,
						),
						array(
							'param_name' => 'share_color_hover',
							'selectors'  => true,
						),
						array(
							'param_name' => 'share_bg_color_hover',
							'selectors'  => true,
						),
					)
				);
				$internal_css    = PortoShortcodesClass::generate_wpb_css( 'porto_single_share', $atts );
				if ( ! empty( $shortcode_class ) ) {
					$el_class .= ' wpb-post-share' . $shortcode_class;
				}
				if ( ! empty( $atts['el_class'] ) ) {
					$el_class .= ' ' . $atts['el_class'];
				}
			}
			if ( ! empty( $el_class ) ) {
				?>
				<div class="<?php echo esc_attr( $el_class ); ?>">
				<?php
			}
			$style = '';
			if ( ! empty( $atts['share_inline'] ) && ( 'yes' == $atts['share_inline'] || true == $atts['share_inline'] ) ) {
				$atts['style'] = 'inline';
			}

			$porto_settings['post-share']          = true;
			$porto_settings['share-enable']        = true;
			$porto_settings['post-share-position'] = '';
			if ( ! empty( $atts['with_icon'] ) && ( 'yes' == $atts['with_icon'] || true == $atts['with_icon'] ) ) {
				$porto_settings['post-title-style'] = 'without-icon';
			} else {
				$porto_settings['post-title-style'] = '';
			}
			porto_get_template_part( 'views/posts/single/share', null, $atts );

			if ( ! empty( $el_class ) ) {
				?>
				</div>
				<?php
			}

			$this->reset_global_single_variable();

			$result = PortoShortcodesClass::generate_insert_css( ob_get_clean(), $internal_css );

			//retreive Porto Settings
			if ( ! empty( $share_position ) ) {
				$porto_settings['post-share-position'] = $share_position;
			}

			if ( ! empty( $post_share ) ) {
				$porto_settings['post-share'] = $post_share;
			}

			if ( ! empty( $share_enable ) ) {
				$porto_settings['share-enable'] = $share_enable;
			}

			if ( ! empty( $with_icon ) ) {
				$porto_settings['post-title-style'] = $with_icon;
			}

			return $result;
		}

		/**
		 * Single Post Format Widget
		 *
		 * @since 2.5.0
		 * @access public
		 */
		public function shortcode_single_post_format( $atts ) {
			if ( ( ! $this->restore_global_single_variable() && function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) || ( ! $this->restore_global_single_variable() && function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
				return null;
			}
			global $porto_settings;
			$post_format  = '';
			$internal_css = '';
			$el_class     = '';

			if ( ! empty( $porto_settings['post-format'] ) ) {
				$post_format = $porto_settings['post-format'];
			}
			$porto_settings['post-format'] = true;

			if ( defined( 'WPB_VC_VERSION' ) && empty( $atts['page_builder'] ) ) {
				// Shortcode class
				$shortcode_class = ' wpb_custom_' . PortoShortcodesClass::get_global_hashcode(
					$atts,
					'porto_single_post_format',
					array(
						array(
							'param_name' => 'format_heading',
							'selectors'  => true,
						),
						array(
							'param_name' => 'format_padding',
							'selectors'  => true,
						),
						array(
							'param_name' => 'format_radius',
							'selectors'  => true,
						),
						array(
							'param_name' => 'format_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'format_bg_color',
							'selectors'  => true,
						),
						array(
							'param_name' => 'format_shadow',
							'selectors'  => true,
						),
					)
				);
				$internal_css    = PortoShortcodesClass::generate_wpb_css( 'porto_single_post_format', $atts );
				if ( ! empty( $shortcode_class ) ) {
					$el_class .= ' wpb-post-format' . $shortcode_class;
				}
				if ( ! empty( $atts['el_class'] ) ) {
					$el_class .= ' ' . $atts['el_class'];
				}
			}

			if ( ! empty( $atts['page_builder'] ) ) {
				$el_class .= ' ' . $atts['elementor_id'];
			}
			ob_start();
			if ( ! empty( $el_class ) ) {
				?>
				<div class="<?php echo esc_attr( $el_class ); ?>">
				<?php
			}

			porto_post_format();

			$internal_css .= ' .' . str_replace( ' ', '.', trim( esc_attr( $el_class ) ) ) . ' .format { display: inline-block; margin: 0; }';
			if ( ! empty( $el_class ) ) {
				?>
				</div>
				<?php
			}
			$result = PortoShortcodesClass::generate_insert_css( ob_get_clean(), $internal_css );
			if ( ! empty( $post_format ) ) {
				$porto_settings['post-format'] = $post_format;
			}
			return $result;
		}
		/**
		 * Register Single Builder shortcodes for WP Bakery
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function wpb_custom_single_shortcodes() {
			if ( ! $this->wpb_display_post_page_elements ) {
				$this->wpb_display_post_page_elements = PortoBuilders::check_load_wpb_elements( 'single' );
			}
			if ( ! $this->wpb_display_post_page_elements ) {
				return;
			}

			// feature image
			$feature_slide                  = porto_vc_product_slider_fields();
			$feature_slide[0]['dependency'] = array(
				'element' => 'show_type',
				'value'   => array( 'images' ),
			);
			$feature_slide[5]['dependency'] = array(
				'element' => 'show_type',
				'value'   => array( 'images' ),
			);
			$feature_slide[1]['value']      = array(
				__( 'Middle', 'porto-functionality' ) => '',
				__( 'Middle Inside', 'porto-functionality' ) => 'nav-pos-inside',
				__( 'Middle of Images', 'porto-functionality' ) => 'nav-center-images-only',
				__( 'Middle Outside', 'porto-functionality' ) => 'nav-pos-outside',
			);
			$feature_slide[3]['dependency'] = array(
				'element' => 'navigation',
				'value'   => 'yes',
			);

			unset( $feature_slide[9] );
			unset( $feature_slide[8] );
			$feature_slide[0]['std'] = $feature_slide[4]['std'] = $feature_slide[5]['std'] = '';

			$left  = is_rtl() ? 'right' : 'left';
			$right = is_rtl() ? 'left' : 'right';
			vc_map(
				array(
					'name'        => __( 'Featured Image', 'porto-functionality' ),
					'base'        => 'porto_single_image',
					'icon'        => 'vc_general vc_element-icon icon-wpb-single-image',
					'category'    => __( 'Single Builder', 'porto-functionality' ),
					'description' => __( 'Display the feautred image.', 'porto-functionality' ),
					'params'      => array_merge(
						array(
							array(
								'type'        => 'checkbox',
								'heading'     => __( 'Follow Post Meta Box', 'porto-functionality' ),
								'description' => __( 'To check this option, image depends on Meta Options.', 'porto-functionality' ),
								'param_name'  => 'follow_meta',
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Media Type', 'porto-functionality' ),
								'description' => __( 'To choose the media type.', 'porto-functionality' ),
								'param_name'  => 'show_type',
								'value'       => array(
									__( 'Select..', 'porto-functionality' ) => '',
									__( 'Grid', 'porto-functionality' ) => 'grid',
									__( 'Images', 'porto-functionality' ) => 'images',
									__( 'Masonry', 'porto-functionality' ) => 'masonry',
									__( 'Video', 'porto-functionality' ) => 'video',
								),
								'dependency'  => array(
									'element'  => 'follow_meta',
									'is_empty' => true,
								),
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Columns', 'porto-functionality' ),
								'description' => esc_html__( 'Controls the column count.', 'porto-functionality' ),
								'param_name'  => 'grid_column',
								'std'         => '3',
								'value'       => array(
									__( '6 columns', 'porto-functionality' ) => '6',
									__( '4 columns', 'porto-functionality' ) => '4',
									__( '3 columns', 'porto-functionality' ) => '3',
									__( '2 columns', 'porto-functionality' ) => '2',
									__( '1 columns', 'porto-functionality' ) => '1',
								),
								'dependency'  => array(
									'element' => 'show_type',
									'value'   => 'grid',
								),
							),
							array(
								'type'        => 'porto_number',
								'heading'     => __( 'Image Border Radius', 'porto-functionality' ),
								'description' => __( 'Control the border radius of image.', 'porto-functionality' ),
								'param_name'  => 'border_radius',
								'units'       => array( 'px', 'rem', 'em' ),
								'dependency'  => array(
									'element' => 'show_type',
									'value'   => 'images',
								),
								'selectors'   => array(
									'{{WRAPPER}} img' => 'border-radius: {{VALUE}}{{UNIT}};',
								),
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Image Size', 'porto-functionality' ),
								'param_name'  => 'thumbnail_size',
								'description' => __( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'porto-functionality' ),
								'dependency'  => array(
									'element' => 'show_type',
									'value'   => 'images',
								),
							),
							array(
								'type'       => 'checkbox',
								'heading'    => __( 'Popup Image', 'porto-functionality' ),
								'param_name' => 'popup',
								'value'      => false,
								'dependency' => array(
									'element' => 'show_type',
									'value'   => 'images',
								),
							),
							array(
								'type'        => 'checkbox',
								'heading'     => __( 'Zoom Icon Position', 'porto-functionality' ),
								'description' => __( 'Put the icon in the center.', 'porto-functionality' ),
								'param_name'  => 'icon_pos',
								'selectors' => array(
									'{{WRAPPER}} .zoom' => 'right: 50%; bottom: 50%; transform: translate(50%, 50%)',
								),
								'dependency'  => array(
									'element' => 'popup',
									'not_empty'   => true,
								),
							),
							array(
								'type'        => 'checkbox',
								'heading'     => __( 'Advanced Post Share?', 'porto-functionality' ),
								'param_name'  => 'share_position',
								'description' => __( 'To show Share Icons near the image.', 'porto-functionality' ),
								'value'       => false,
								'dependency'  => array(
									'element' => 'show_type',
									'value'   => 'images',
								),
							),
							array(
								'type'       => 'colorpicker',
								'heading'    => __( 'Overlay Background Color', 'porto-functionality' ),
								'param_name' => 'overlay_bgc',
								'dependency' => array(
									'element' => 'show_type',
									'value'   => array( 'images', 'grid', 'masonry' ),
								),
								'qa_selector' => '.post-image',
								'selectors'  => array(
									'{{WRAPPER}} .thumb-info.thumb-info-no-borders .thumb-info-wrapper:after' => 'background-color: {{VALUE}};',
									'{{WRAPPER}} .img-zoom-overlay .img-thumbnail:hover:after' => 'background-color: {{VALUE}};',
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'       => 'dropdown',
								'heading'    => __( 'Overlay Icon Type', 'porto-functionality' ),
								'param_name' => 'icon_type',
								'value'      => array(
									__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
									__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
									__( 'Porto Icon', 'porto-functionality' ) => 'porto',
								),
								'dependency' => array(
									'element' => 'show_type',
									'value'   => array( 'grid', 'masonry' ),
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'       => 'iconpicker',
								'heading'    => __( 'Overlay Icon', 'porto-functionality' ),
								'param_name' => 'icon_cl',
								'value'      => '',
								'dependency' => array(
									'element' => 'icon_type',
									'value'   => array( 'fontawesome' ),
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'       => 'iconpicker',
								'heading'    => __( 'Overlay Icon', 'porto-functionality' ),
								'param_name' => 'icon_simpleline',
								'settings'   => array(
									'type'         => 'simpleline',
									'iconsPerPage' => 4000,
								),
								'dependency' => array(
									'element' => 'icon_type',
									'value'   => 'simpleline',
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'       => 'iconpicker',
								'heading'    => __( 'Overlay Icon', 'porto-functionality' ),
								'param_name' => 'icon_porto',
								'settings'   => array(
									'type'         => 'porto',
									'iconsPerPage' => 4000,
								),
								'dependency' => array(
									'element' => 'icon_type',
									'value'   => 'porto',
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'       => 'colorpicker',
								'heading'    => __( 'Icon Background Color', 'porto-functionality' ),
								'param_name' => 'icon_bgc',
								'dependency' => array(
									'element' => 'show_type',
									'value'   => array( 'images', 'grid', 'masonry' ),
								),
								'selectors'  => array(
									'{{WRAPPER}} .thumb-info .thumb-info-action .thumb-info-action-icon-light' => 'background-color: {{VALUE}};',
									'{{WRAPPER}} .zoom' => 'background-color: {{VALUE}} !important;',
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'       => 'colorpicker',
								'heading'    => __( 'Icon Color', 'porto-functionality' ),
								'param_name' => 'icon_clr',
								'dependency' => array(
									'element' => 'show_type',
									'value'   => array( 'images', 'grid', 'masonry' ),
								),
								'selectors'  => array(
									'{{WRAPPER}} .thumb-info .thumb-info-action-icon-light i' => 'color: {{VALUE}} !important;',
									'{{WRAPPER}} .zoom i' => 'color: {{VALUE}} !important;',
								),
								'group'      => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'porto_number',
								'heading'     => __( 'Icon Background Size(px)', 'porto-functionality' ),
								'description' => __( 'Control the icon width.', 'porto-functionality' ),
								'param_name'  => 'icon_width',
								'dependency' => array(
									'element' => 'show_type',
									'value'   => array( 'images', 'grid' ),
								),
								'selectors'   => array(
									'{{WRAPPER}} .zoom, {{WRAPPER}} .thumb-info .thumb-info-action-icon-light'   => 'width: {{VALUE}}px; height: {{VALUE}}px;',
									'{{WRAPPER}} .zoom i, {{WRAPPER}} .thumb-info .thumb-info-action-icon-light i' => 'line-height: {{VALUE}}px;',
								),
								'qa_selector' => '.owl-item.active .img-thumbnail .zoom, .lightbox >div:not(.masonry-item):first-child .thumb-info-action-icon',
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'porto_number',
								'heading'     => __( 'Icon Size(px)', 'porto-functionality' ),
								'description' => __( 'Control the font size of the icon.', 'porto-functionality' ),
								'param_name'  => 'icon_size',
								'dependency' => array(
									'element' => 'show_type',
									'value'   => array( 'images', 'grid' ),
								),
								'selectors'   => array(
									'{{WRAPPER}} .zoom i' => 'font-size: {{VALUE}}px;',
									'{{WRAPPER}} .thumb-info .thumb-info-action-icon-light i' => 'font-size: {{VALUE}}px;',
								),
								'qa_selector' => '.owl-item.active .img-thumbnail .zoom i, .lightbox >div:not(.masonry-item):first-child .thumb-info-action-icon i',
								'group'       => __( 'Style', 'porto-functionality' ),
							),

							array(
								'type'        => 'porto_number',
								'heading'     => __( 'Image Height', 'porto-functionality' ),
								'description' => __( 'Control the height of image.', 'porto-functionality' ),
								'param_name'  => 'image_height',
								'units'       => array( 'px', 'rem', 'em' ),
								'dependency'  => array(
									'element' => 'show_type',
									'value'   => array( 'images', 'grid' ),
								),
								'selectors'   => array(
									'{{WRAPPER}} .post-image >div:first-child img' => 'height: {{VALUE}}{{UNIT}}; object-fit: cover;',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'porto_number',
								'heading'     => __( 'Image Padding', 'porto-functionality' ),
								'description' => __( 'Control the padding of image.', 'porto-functionality' ),
								'param_name'  => 'image_padding',
								'units'       => array( 'px' ),
								'dependency'  => array(
									'element' => 'show_type',
									'value'   => 'grid',
								),
								'selectors'   => array(
									'{{WRAPPER}} .lightbox >div' => 'padding: {{VALUE}}{{UNIT}} !important;',
								),
								'group'       => __( 'Style', 'porto-functionality' ),
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Hover Effect', 'porto-functionality' ),
								'description' => __( 'Choose the hover effet.', 'porto-functionality' ),
								'param_name'  => 'hover_effect',
								'std'         => '',
								'value'       => array(
									__( 'None', 'porto-functionality' ) => '',
									__( 'Zoom', 'porto-functionality' ) => 'img-zoom-in',
									__( 'Zoom Overlay', 'porto-functionality' ) => 'img-zoom-overlay',
								),
								'dependency'  => array(
									'element' => 'show_type',
									'value'   => 'images',
								),
								'group'       => __( 'Image Option', 'porto-functionality' ),
							),
							array(
								'type'        => 'checkbox',
								'heading'     => __( 'Show Thumbanil Images', 'porto-functionality' ),
								'param_name'  => 'show_thumbnail',
								'dependency'  => array(
									'element' => 'show_type',
									'value'   => 'images',
								),
								'group'       => __( 'Image Option', 'porto-functionality' ),
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Thumbnail Size', 'porto-functionality' ),
								'param_name'  => 'image_thumbnail_size',
								'description' => __( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)).', 'porto-functionality' ),
								'dependency'  => array(
									'element' => 'show_thumbnail',
									'not_empty'   => true,
								),
								'std'         => 'thumbnail',
								'group'       => __( 'Image Option', 'porto-functionality' ),
							),
							array(
								'type'        => 'porto_number',
								'heading'     => __( 'Thumbnail Space(px)', 'porto-functionality' ),
								'description' => __( 'Control the space of thumbnail images.', 'porto-functionality' ),
								'param_name'  => 'thumbnail_space',
								'std'         => '10',
								'dependency'  => array(
									'element' => 'show_thumbnail',
									'not_empty'   => true,
								),
								'selectors'   => array(
									'{{WRAPPER}} .thumbnail-wrapper .porto-carousel' => 'margin-bottom: {{VALUE}}px;',
								),
								'qa_selector' => '.thumbnail-gallery',
								'group'       => __( 'Image Option', 'porto-functionality' ),
							),
							porto_vc_custom_class(),
						),
						$feature_slide
					),
				)
			);
			vc_map(
				array(
					'name'        => __( 'Post Comments', 'porto-functionality' ),
					'base'        => 'porto_single_comments',
					'icon'        => 'vc_general vc_element-icon far fa-comment',
					'category'    => __( 'Single Builder', 'porto-functionality' ),
					'description' => __( 'Display the comments of the single post.', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'checkbox',
							'heading'    => __( 'Hide Author Icon', 'porto-functionality' ),
							'param_name' => 'comment_author_icon',
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Space between Comments.', 'porto-functionality' ),
							'description' => __( 'To control the space between the comments. To perform this, the post has more than 2 comments.', 'porto-functionality' ),
							'param_name'  => 'comments_spacing',
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} ul.comments>li + li' => 'margin-top: {{VALUE}}{{UNIT}};',
							),
							'qa_selector' => '.comment:nth-child(2)',
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Space between Comment and Reply.', 'porto-functionality' ),
							'description' => __( 'To control the space between the comment and reply object.', 'porto-functionality' ),
							'param_name'  => 'comments_reply',
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} .comment-respond' => 'margin-top: {{VALUE}}{{UNIT}};',
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Space between Reply and Form.', 'porto-functionality' ),
							'description' => __( 'To control the space between the reply title and reply form.', 'porto-functionality' ),
							'param_name'  => 'comments_form',
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} .comment-respond .comment-reply-title' => 'margin-bottom: {{VALUE}}{{UNIT}};',
							),
							'qa_selector' => '.comment-form',
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Image Spacing', 'porto-functionality' ),
							'param_name'  => 'comment_image_between',
							'description' => __( 'To control the space the avatar and the comment body.', 'porto-functionality' ),
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} ul.comments>li .img-thumbnail' => "margin-{$right}: -{{VALUE}}{{UNIT}};",
							),
							'qa_selector' => '.comment:first-child .img-thumbnail',
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Max Width of Comment Image', 'porto-functionality' ),
							'param_name'  => 'comment_image_width',
							'description' => __( 'To control the max width of avatar.', 'porto-functionality' ),
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} ul.comments>li img.avatar' => 'max-width: {{VALUE}}{{UNIT}};',
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Border Radius of Comment Image', 'porto-functionality' ),
							'units'       => array( 'px', 'rem', '%' ),
							'description' => __( 'To control the border radius of avatar.', 'porto-functionality' ),
							'param_name'  => 'comment_image_radius',
							'selectors'   => array(
								'{{WRAPPER}} ul.comments>li img.avatar' => 'border-radius: {{VALUE}}{{UNIT}};',
							),
						),

						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'comment_title_typography',
							'text'       => __( 'Comment Title', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'porto_typography',
							'heading'     => __( 'Typography', 'porto-functionality' ),
							'description' => __( 'To control the commenter title.', 'porto-functionality' ),
							'param_name'  => 'comment_title',
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .post-comments h4, {{WRAPPER}} .post-comments h3',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'comment_title_color',
							'heading'     => __( 'Color', 'porto-functionality' ),
							'description' => __( 'To control the color of the commente title.', 'porto-functionality' ),
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .post-comments h3, {{WRAPPER}} .post-comments h4' => 'color: {{VALUE}};',
							),
						),

						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'comment_name_typography',
							'text'       => __( 'Comment Name', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'porto_typography',
							'heading'     => __( 'Typography', 'porto-functionality' ),
							'description' => __( 'To control the commenter.', 'porto-functionality' ),
							'param_name'  => 'comment_name',
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .comment-by strong',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'comment_name_color',
							'heading'     => __( 'Color', 'porto-functionality' ),
							'description' => __( 'To control the color of the commenter.', 'porto-functionality' ),
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} ul.comments .comment-by strong,{{WRAPPER}} ul.comments .comment-by strong a' => 'color: {{VALUE}};',
							),
							'qa_selector' => '.comment:first-child .comment-by strong',
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'comment_setting_typography',
							'text'       => __( 'Comment Settings', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
						),

						array(
							'type'        => 'porto_typography',
							'heading'     => __( 'Typography', 'porto-functionality' ),
							'description' => __( 'To control the commenter options.', 'porto-functionality' ),
							'param_name'  => 'comment_setting',
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .comment-by span a',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'comment_setting_color',
							'heading'     => __( 'Color', 'porto-functionality' ),
							'description' => __( 'To control the color of commenter options.', 'porto-functionality' ),
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .comment-by span a' => 'color: {{VALUE}};',
							),
							'qa_selector' => '.comment:first-child .comment-by>span',
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'comment_setting_h_color',
							'heading'     => __( 'Hover Color', 'porto-functionality' ),
							'description' => __( 'To control the hover color of commenter options.', 'porto-functionality' ),
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .comment-by span a:hover' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'comment_date_typography',
							'text'       => __( 'Comment Date', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'porto_typography',
							'heading'     => __( 'Typography', 'porto-functionality' ),
							'description' => __( 'To control the comment date.', 'porto-functionality' ),
							'param_name'  => 'comment_date',
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} ul.comments .comment-block .date',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'comment_date_color',
							'heading'     => __( 'Color', 'porto-functionality' ),
							'description' => __( 'To control the color of comment date.', 'porto-functionality' ),
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} ul.comments .comment-block .date' => 'color: {{VALUE}};',
							),
							'qa_selector' => '.comment:first-child .comment-block .date',
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'comment_text_typography',
							'text'       => __( 'Comment Text', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'porto_typography',
							'heading'     => __( 'Typography', 'porto-functionality' ),
							'description' => __( 'To control the comment content.', 'porto-functionality' ),
							'param_name'  => 'comment_text',
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .comment-block > div p',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'comment_text_color',
							'heading'     => __( 'Color', 'porto-functionality' ),
							'description' => __( 'To control the color of comment content.', 'porto-functionality' ),
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .comment-block > div p' => 'color: {{VALUE}};',
							),
							'qa_selector' => '.comment:first-child .comment-block > div p',
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'form_heading_typography',
							'text'       => __( 'Reply Heading', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'porto_typography',
							'heading'     => __( 'Typography', 'porto-functionality' ),
							'param_name'  => 'form_heading',
							'description' => __( 'To control the reply form heading.', 'porto-functionality' ),
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .comment-respond .comment-reply-title',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'comment_form_heading_color',
							'heading'     => __( 'Color', 'porto-functionality' ),
							'description' => __( 'To control the color of reply heading.', 'porto-functionality' ),
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .comment-respond .comment-reply-title' => 'color: {{VALUE}};',
							),
							'qa_selector' => '.comment-respond .comment-reply-title',
						),
						array(
							'type'        => 'porto_param_heading',
							'param_name'  => 'form_reply_typography',
							'text'        => __( 'Comment Form Reply', 'porto-functionality' ),
							'description' => __( 'These options are shown if edit or reply the comments. Try on the real page.', 'porto-functionality' ),
							'group'       => 'Style',
						),
						array(
							'type'        => 'porto_typography',
							'heading'     => __( 'Typography', 'porto-functionality' ),
							'param_name'  => 'comment_reply',
							'description' => __( 'To control the reply heading.', 'porto-functionality' ),
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .comment-respond #cancel-comment-reply-link',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'comment_form_reply_color',
							'heading'     => __( 'Color', 'porto-functionality' ),
							'description' => __( 'To control the color of reply heading.', 'porto-functionality' ),
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .comment-respond #cancel-comment-reply-link' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'comment_form_reply_color_hover',
							'heading'     => __( 'Hover Color', 'porto-functionality' ),
							'description' => __( 'To control the hover color of reply heading.', 'porto-functionality' ),
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .comment-respond #cancel-comment-reply-link:hover' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Space between Reply title and button', 'porto-functionality' ),
							'description' => __( 'To control the color of reply heading.', 'porto-functionality' ),
							'units'       => array( 'px', 'rem', '%' ),
							'param_name'  => 'comment_form_reply_space',
							'selectors'   => array(
								'{{WRAPPER}} .comment-respond #cancel-comment-reply-link' => "margin-{$left}: {{VALUE}}{{UNIT}};",
							),
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'form_label_style',
							'text'       => __( 'Comment Form Label', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'        => 'porto_typography',
							'heading'     => __( 'Typography', 'porto-functionality' ),
							'param_name'  => 'comment_label',
							'description' => __( 'To control the reply form lable.', 'porto-functionality' ),
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .comment-form label',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'comment_form_label_color',
							'description' => __( 'To control the color of reply form lable.', 'porto-functionality' ),
							'heading'     => __( 'Color', 'porto-functionality' ),
							'group'       => 'Style',
							'selectors'   => array(
								'{{WRAPPER}} .comment-form label' => 'color: {{VALUE}};',
							),
							'qa_selector' => '.comment-form label',
						),
						porto_vc_custom_class(),
					),
				)
			);
			vc_map(
				array(
					'name'        => __( 'Post Navigation', 'porto-functionality' ),
					'base'        => 'porto_single_navigation',
					'icon'        => 'vc_general vc_element-icon fas fa-retweet',
					'category'    => __( 'Single Builder', 'porto-functionality' ),
					'description' => __( 'Display the navigation for previous or next post.', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Alignment', 'porto-functionality' ),
							'description' => esc_html__( 'Controls navigations alignment. Choose from Left, Center, Right.', 'porto-functionality' ),
							'param_name'  => 'nav_align',
							'value'       => array(
								__( 'Left', 'porto-functionality' ) => 'flex-start',
								__( 'Center', 'porto-functionality' ) => 'center',
								__( 'Right', 'porto-functionality' ) => 'flex-end',
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Navigation Spacing', 'porto-functionality' ),
							'param_name'  => 'nav_space',
							'description' => __( 'To control the space between previous and next navigation.', 'porto-functionality' ),
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} a + a' => "margin-{$left}: {{VALUE}}{{UNIT}};",
							),
						),
						array(
							'type'        => 'iconpicker',
							'heading'     => esc_html__( 'Preview Icon', 'porto-functionality' ),
							'param_name'  => 'nav_icon_prev',
							'description' => __( 'To select the previous icon', 'porto-functionality' ),
							'value'       => 'fas fa-angle-left',
							'settings'    => array(
								'emptyIcon'    => false,
								'iconsPerPage' => 500,
							),
						),
						array(
							'type'        => 'iconpicker',
							'heading'     => esc_html__( 'Next Icon', 'porto-functionality' ),
							'param_name'  => 'nav_icon_next',
							'value'       => 'fas fa-angle-right',
							'description' => __( 'To select the next icon', 'porto-functionality' ),
							'settings'    => array(
								'emptyIcon'    => false,
								'iconsPerPage' => 500,
							),
						),
						array(
							'type'        => 'porto_param_heading',
							'param_name'  => 'navigation_typography',
							'description' => __( 'To select the typography of icon', 'porto-functionality' ),
							'text'        => __( 'Typography', 'porto-functionality' ),
							'group'       => 'Style',
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Typography', 'porto-functionality' ),
							'param_name' => 'nav_typography',
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}}',
							),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'nav_color',
							'heading'    => __( 'Color', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} a' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'nav_hover_color',
							'heading'    => __( 'Hover Color', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} a:hover' => 'color: {{VALUE}};',
							),
						),
						porto_vc_custom_class(),
					),
				)
			);
			vc_map(
				array(
					'name'        => __( 'Share', 'porto-functionality' ),
					'base'        => 'porto_single_share',
					'icon'        => 'vc_general vc_element-icon fas fa-share',
					'category'    => __( 'Single Builder', 'porto-functionality' ),
					'description' => __( 'Display the share icons of the single post.', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'checkbox',
							'heading'    => __( 'Inline Style', 'porto-functionality' ),
							'param_name' => 'share_inline',
							'default'    => '',
							'selectors'  => array(
								'{{WRAPPER}} .post-share > .share-links' => 'display: inline-block;',
							),
						),
						array(
							'type'       => 'checkbox',
							'heading'    => __( 'Hide Heading', 'porto-functionality' ),
							'param_name' => 'hide_heading',
							'default'    => '',
							'selectors'  => array(
								'{{WRAPPER}} .post-share > span' => 'display: none;',
							),
						),
						array(
							'type'        => 'checkbox',
							'heading'     => __( 'Without Icon', 'porto-functionality' ),
							'description' => __( 'To hide the heading icon', 'porto-functionality' ),
							'param_name'  => 'with_icon',
							'dependency'  => array(
								'element'  => 'share_inline',
								'is_empty' => true,
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Icon Spacing', 'porto-functionality' ),
							'param_name'  => 'icon_space',
							'description' => __( 'To control the space between the icon and heading', 'porto-functionality' ),
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} .post-share > h3 i' => "margin-{$right}: {{VALUE}}{{UNIT}};",
								'{{WRAPPER}} .post-share > span i' => "margin-{$right}: {{VALUE}}{{UNIT}};",
							),
						),
						array(
							'type'        => 'porto_param_heading',
							'param_name'  => 'share_heading_typography',
							'description' => __( 'To control the heading', 'porto-functionality' ),
							'text'        => __( 'Typography', 'porto-functionality' ),
							'group'       => 'Style',
							'dependency'  => array(
								'element'  => 'hide_heading',
								'is_empty' => true,
							),
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Typography', 'porto-functionality' ),
							'param_name' => 'share_heading_style',
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} .post-share h3',
								'{{WRAPPER}} .post-share > span',
							),
							'dependency' => array(
								'element'  => 'hide_heading',
								'is_empty' => true,
							),
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Title Color', 'porto-functionality' ),
							'param_name'  => 'title_color',
							'description' => __( 'To control the color of the share heading.', 'porto-functionality' ),
							'group'       => __( 'Style', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}} .post-share h3' => 'color: {{VALUE}}',
								'{{WRAPPER}} .post-share > span' => 'color: {{VALUE}}',
							),
							'dependency'  => array(
								'element'  => 'hide_heading',
								'is_empty' => true,
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Title Spacing', 'porto-functionality' ),
							'param_name'  => 'title_space',
							'description' => __( 'To control the space between the icon and title', 'porto-functionality' ),
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} .post-share h3' => 'margin-bottom: {{VALUE}}{{UNIT}};',
								'{{WRAPPER}} .post-share span' => "margin-{$right}: {{VALUE}}{{UNIT}};",
							),
							'dependency'  => array(
								'element'  => 'hide_heading',
								'is_empty' => true,
							),
						),
						array(
							'type'        => 'porto_param_heading',
							'param_name'  => 'share_icons_typography',
							'text'        => __( 'Icon Typography', 'porto-functionality' ),
							'description' => __( 'To control the typography of the social icons', 'porto-functionality' ),
							'group'       => 'Style',
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'share_icon',
							'text'       => __( 'Share Icons', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Typography', 'porto-functionality' ),
							'param_name' => 'share_icons',
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} .share-links a',
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Share Width', 'porto-functionality' ),
							'param_name'  => 'share_width',
							'description' => __( 'To control the width and height of the share icons.', 'porto-functionality' ),
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}}  .share-links a' => 'width: {{VALUE}}{{UNIT}}; Height: {{VALUE}}{{UNIT}};',
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Border Radius', 'porto-functionality' ),
							'description' => __( 'To control the border radius of the share icons.', 'porto-functionality' ),
							'param_name'  => 'share_radius',
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}}  .share-links a' => 'border-radius: {{VALUE}}{{UNIT}};',
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Share Spacing', 'porto-functionality' ),
							'description' => __( 'To control the space between the share icons.', 'porto-functionality' ),
							'param_name'  => 'share_space',
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}}  .share-links a:not(:last-child)' => "margin-{$right}: {{VALUE}}{{UNIT}};",
							),
						),
						array(
							'type'       => 'porto_boxshadow',
							'heading'    => __( 'Icon Box Shadow', 'porto-functionality' ),
							'param_name' => 'share_icon_box_shadow',
							'unit'       => 'px',
							'positions'  => array(
								__( 'Horizontal', 'porto-functionality' ) => '',
								__( 'Vertical', 'porto-functionality' ) => '',
								__( 'Blur', 'porto-functionality' )   => '',
								__( 'Spread', 'porto-functionality' ) => '',
							),
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} .post-share > .share-links a',
							),
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Icon Color', 'porto-functionality' ),
							'param_name'  => 'share_color_normal',
							'description' => __( 'To control the color of the share icons.', 'porto-functionality' ),
							'group'       => __( 'Style', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}} .post-share > .share-links a:not(:hover)' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Icon Background Color', 'porto-functionality' ),
							'param_name'  => 'share_bg_color_normal',
							'description' => __( 'To control the background color of the share icons.', 'porto-functionality' ),
							'group'       => __( 'Style', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}} .post-share > .share-links a:not(:hover)' => 'background-color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Icon Hover Color', 'porto-functionality' ),
							'param_name'  => 'share_color_hover',
							'description' => __( 'To control the hover color of the share icons.', 'porto-functionality' ),
							'group'       => __( 'Style', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}} .post-share > .share-links a:hover' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'colorpicker',
							'heading'     => __( 'Icon Background Hover Color', 'porto-functionality' ),
							'param_name'  => 'share_bg_color_hover',
							'description' => __( 'To control the background hover color of the share icons.', 'porto-functionality' ),
							'group'       => __( 'Style', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}} .post-share > .share-links a:hover' => 'background-color: {{VALUE}};',
							),
						),
						porto_vc_custom_class(),
					),
				)
			);
			vc_map(
				array(
					'name'        => __( 'Post Author Box', 'porto-functionality' ),
					'base'        => 'porto_single_author_box',
					'icon'        => 'vc_general vc_element-icon fas fa-users',
					'category'    => __( 'Single Builder', 'porto-functionality' ),
					'description' => __( 'Display the author box of the single post.', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'        => 'checkbox',
							'heading'     => __( 'Hide author icon', 'porto-functionality' ),
							'description' => __( 'To hide the icon of author title.', 'porto-functionality' ),
							'param_name'  => 'author_icon',
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Icon Spacing', 'porto-functionality' ),
							'param_name'  => 'author_icon_space',
							'description' => __( 'To control the space between icon and title.', 'porto-functionality' ),
							'units'       => array( 'px', 'rem', 'em' ),
							'dependency'  => array(
								'element'  => 'author_icon',
								'is_empty' => true,
							),
							'selectors'   => array(
								'{{WRAPPER}} h3 i' => "margin-{$right}: {{VALUE}}{{UNIT}};",
							),
							'qa_selector' => '.post-author h3 i',
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Author Spacing', 'porto-functionality' ),
							'param_name'  => 'author_space',
							'description' => __( 'To control the space between the title and the content.', 'porto-functionality' ),
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} h3' => 'margin-bottom: {{VALUE}}{{UNIT}};',
							),
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'heading_image_typography',
							'text'       => __( 'Author Image', 'porto-functionality' ),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Author Image Max Width', 'porto-functionality' ),
							'param_name'  => 'author_image_size',
							'description' => __( 'To control the max width of author image.', 'porto-functionality' ),
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} .img-thumbnail img' => 'max-width: {{VALUE}}{{UNIT}};',
							),
							'qa_selector' => '.post-author .img-thumbnail img',
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Author Image Border Radius', 'porto-functionality' ),
							'param_name'  => 'author_image_radius',
							'description' => __( 'To control the border radius of author image.', 'porto-functionality' ),
							'units'       => array( 'px', 'rem', '%' ),
							'selectors'   => array(
								'{{WRAPPER}} .img-thumbnail img' => 'border-radius: {{VALUE}}{{UNIT}};',
							),
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'heading_title_typography',
							'text'       => __( 'Author Title', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Typography', 'porto-functionality' ),
							'param_name' => 'heading_title_style',
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} .post-author h3',
							),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'author_title_color',
							'heading'    => __( 'Color', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} .post-author h3' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'heading_name_typography',
							'text'       => __( 'Author Name', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Typography', 'porto-functionality' ),
							'param_name' => 'heading_name_style',
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} .name a',
							),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'author_name_color',
							'heading'    => __( 'Color', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} .name a' => 'color: {{VALUE}};',
							),
							'qa_selector' => '.name a',
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'heading_desc_typography',
							'text'       => __( 'Author Description', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Typography', 'porto-functionality' ),
							'param_name' => 'heading_desc_style',
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} .author-content',
							),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'author_desc_color',
							'heading'    => __( 'Color', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} .author-content' => 'color: {{VALUE}};',
							),
							'qa_selector' => '.author-content',
						),
						porto_vc_custom_class(),
					),
				)
			);
			vc_map(
				array(
					'name'        => __( 'Related Posts Grid', 'porto-functionality' ),
					'base'        => 'porto_single_related',
					'icon'        => 'far fa-calendar-alt',
					'category'    => __( 'Single Builder', 'porto-functionality' ),
					'description' => __( 'Display the related posts with type builder template.', 'porto-functionality' ),
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
								'description' => sprintf( __( 'Please select a saved Post Layout template which was built using post type builder. Please create a new Post Layout template in %1$sPorto Templates Builder%2$s', 'porto-functionality' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=' . PortoBuilders::BUILDER_SLUG . '&' . PortoBuilders::BUILDER_TAXONOMY_SLUG . '=type' ) ) . '">', '</a>' ),
								'admin_label' => true,
							),
							array(
								'type'        => 'number',
								'heading'     => __( 'Count', 'porto-functionality' ),
								'description' => __( 'Leave blank if you use default value.', 'porto-functionality' ),
								'param_name'  => 'count',
								'admin_label' => true,
							),
							array(
								'type'        => 'dropdown',
								'heading'     => __( 'Order by', 'porto-functionality' ),
								'param_name'  => 'orderby',
								'value'       => porto_vc_order_by(),
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
						),
						porto_vc_product_slider_fields( 'slider' )
					),
				)
			);
			vc_map(
				array(
					'name'        => __( 'Meta', 'porto-functionality' ),
					'base'        => 'porto_single_meta',
					'icon'        => 'vc_general vc_element-icon fas fa-info-circle',
					'category'    => __( 'Single Builder', 'porto-functionality' ),
					'description' => __( 'Display the metas of the single post.', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'        => 'checkbox',
							'heading'     => __( 'Without Icon', 'porto-functionality' ),
							'description' => __( 'To hide icon of metas except the date.', 'porto-functionality' ),
							'param_name'  => 'hide_icon',
						),
						array(
							'type'        => 'checkbox',
							'heading'     => __( 'Show Divider', 'porto-functionality' ),
							'description' => __( 'To show divider between the post metas.', 'porto-functionality' ),
							'param_name'  => 'show_divider',
						),
						array(
							'type'        => 'checkbox',
							'heading'     => __( 'Hide author by', 'porto-functionality' ),
							'description' => __( 'To hide by of author meta.', 'porto-functionality' ),
							'param_name'  => 'hide_by',
						),
						array(
							'type'       => 'porto_multiselect',
							'heading'    => __( 'Select Metas To Show.', 'porto-functionality' ),
							'param_name' => 'post-metas',
							'std'        => '',
							'value'      => array(
								__( 'Date', 'porto-functionality' )     => 'date',
								__( 'Author', 'porto-functionality' )   => 'author',
								__( 'Category', 'porto-functionality' ) => 'cats',
								__( 'Tags', 'porto-functionality' )     => 'tags',
								__( 'Comments', 'porto-functionality' ) => 'comments',
								__( 'Like', 'porto-functionality' )     => 'like',
							),
						),
						array(
							'type'        => 'dropdown',
							'heading'     => __( 'Alignment', 'porto-functionality' ),
							'description' => esc_html__( 'Controls metas alignment. Choose from Left, Center, Right.', 'porto-functionality' ),
							'param_name'  => 'meta_align',
							'value'       => array(
								__( 'Left', 'porto-functionality' )   => 'left',
								__( 'Center', 'porto-functionality' ) => 'center',
								__( 'Right', 'porto-functionality' )  => 'right',
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Meta Spacing', 'porto-functionality' ),
							'description' => __( 'To control the space between post metas.', 'porto-functionality' ),
							'param_name'  => 'meta_space',
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} > span' => "margin-{$right}: {{VALUE}}{{UNIT}};",
							),
							'qa_selector' => '.post-meta > span:nth-of-type(2)',
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Icon Spacing', 'porto-functionality' ),
							'description' => __( 'To control the space between icon and meta.', 'porto-functionality' ),
							'param_name'  => 'icon_space',
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} > span i' => "margin-{$right}: {{VALUE}}{{UNIT}};",
							),
							'qa_selector' => '.post-meta > span:first-of-type i',
						),
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'meta_typography',
							'text'       => __( 'Author Title', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Typography', 'porto-functionality' ),
							'param_name' => 'meta_style',
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}}',
							),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'meta_color',
							'heading'    => __( 'Color', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} > span'   => 'color: {{VALUE}};',
								'{{WRAPPER}} > span i' => 'color: {{VALUE}};',
								'{{WRAPPER}} > span a' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'meta_hover_color',
							'heading'    => __( 'Hover Color', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} span a:hover' => 'color: {{VALUE}};',
								'{{WRAPPER}} span a:focus' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'       => 'colorpicker',
							'param_name' => 'divider_color',
							'heading'    => __( 'Divider Color', 'porto-functionality' ),
							'group'      => __( 'Style', 'porto-functionality' ),
							'selectors'  => array(
								'{{WRAPPER}} > span:after' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Divider Spacing', 'porto-functionality' ),
							'param_name'  => 'divider_space',
							'description' => __( 'To control the space between meta and divider.', 'porto-functionality' ),
							'units'       => array( 'px', 'rem', 'em' ),
							'selectors'   => array(
								'{{WRAPPER}} > span:after' => "margin-{$left}: {{VALUE}}{{UNIT}};",
							),
						),
						porto_vc_custom_class(),
					),
				)
			);
			vc_map(
				array(
					'name'        => __( 'Post Format', 'porto-functionality' ),
					'base'        => 'porto_single_post_format',
					'icon'        => 'vc_general vc_element-icon fas fa-file-alt',
					'category'    => __( 'Single Builder', 'porto-functionality' ),
					'description' => __( 'Display the single Post Format.', 'porto-functionality' ),
					'params'      => array(
						array(
							'type'       => 'porto_param_heading',
							'param_name' => 'description_format',
							'text'       => esc_html__( 'This widget is only available for Single Post. For this, you should set post format on Post Settings.', 'porto-functionality' ),
						),
						array(
							'type'       => 'porto_typography',
							'heading'    => __( 'Typography', 'porto-functionality' ),
							'param_name' => 'format_heading',
							'selectors'  => array(
								'{{WRAPPER}} .gallery > i',
							),
						),
						array(
							'type'        => 'porto_dimension',
							'heading'     => __( 'Padding', 'porto-functionality' ),
							'description' => __( 'Controls the padding of format icon.', 'porto-functionality' ),
							'param_name'  => 'format_padding',
							'selectors'   => array(
								'{{WRAPPER}} .format i' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
							),
						),
						array(
							'type'        => 'porto_number',
							'heading'     => __( 'Border Radius', 'porto-functionality' ),
							'description' => __( 'Controls the border radius of post format.', 'porto-functionality' ),
							'param_name'  => 'format_radius',
							'units'       => array( 'px', 'em', 'rem' ),
							'selectors'   => array(
								'{{WRAPPER}} .format' => 'border-radius: {{VALUE}}{{UNIT}};',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'format_color',
							'heading'     => __( 'Color', 'porto-functionality' ),
							'description' => __( 'Controls the color of format icon.', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}} .format i' => 'color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'colorpicker',
							'param_name'  => 'format_bg_color',
							'description' => __( 'Controls the background color of post format.', 'porto-functionality' ),
							'heading'     => __( 'Background Color', 'porto-functionality' ),
							'selectors'   => array(
								'{{WRAPPER}} .format' => 'background-color: {{VALUE}};',
							),
						),
						array(
							'type'        => 'porto_boxshadow',
							'heading'     => __( 'Box Shadow', 'porto-functionality' ),
							'description' => __( 'Controls the box shadow.', 'porto-functionality' ),
							'param_name'  => 'format_shadow',
							'unit'        => 'px',
							'positions'   => array(
								__( 'Horizontal', 'porto-functionality' ) => '',
								__( 'Vertical', 'porto-functionality' ) => '',
								__( 'Blur', 'porto-functionality' )   => '',
								__( 'Spread', 'porto-functionality' ) => '',
							),
							'selectors'   => array(
								'{{WRAPPER}} .format',
							),
						),
						porto_vc_custom_class(),
					),
				)
			);
		}

		/**
		 * Register Single Builder shortcodes
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function elementor_custom_single_shortcodes( $self ) {

			$load_widgets = false;
			if ( is_single() && ! ( function_exists( 'porto_is_elementor_preview' ) && porto_is_elementor_preview() ) ) {
				$load_widgets = true;
			} elseif ( is_singular( PortoBuilders::BUILDER_SLUG ) && 'single' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
				$load_widgets = true;
			} elseif ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' == $_REQUEST['action'] && ! empty( $_POST['editor_post_id'] ) ) {
				$load_widgets = true;
			}
			if ( $load_widgets ) {
				foreach ( $this->shortcodes as $shortcode ) {
					include_once PORTO_BUILDERS_PATH . 'elements/single/elementor/' . $shortcode . '.php';
					$class_name = 'Porto_Elementor_Single_' . ucfirst( str_replace( '-', '_', $shortcode ) ) . '_Widget';
					if ( class_exists( $class_name ) ) {
						$self->register( new $class_name( array(), array( 'widget_name' => $class_name ) ) );
					}
				}
			}
		}

		/**
		 * Regsiter Preview Settings for Dynamic Post Type
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function register_elementor_preview_controls( $document ) {

			if ( ! $document instanceof Elementor\Core\DocumentTypes\PageBase && ! $document instanceof Elementor\Modules\Library\Documents\Page ) {
				return;
			}
			// Add Template Builder Controls
			$id = (int) $document->get_main_id();
			if ( $id && 'single' == get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {

				$_post_types = get_post_types(
					array(
						'public'            => true,
						'show_in_nav_menus' => true,
					),
					'objects'
				);
				$post_types  = array();
				foreach ( $_post_types as $post_type => $object ) {
					if ( ! in_array( $post_type, array( 'page', 'product', 'e-landing-page' ) ) ) {
						$post_types[ $post_type ] = sprintf( esc_html__( 'Single %s', 'porto-functionality' ), $object->labels->singular_name );
					}
				}
				$document->start_controls_section(
					'single_preview_settings',
					array(
						'label' => esc_html__( 'Preview Settings', 'porto-functionality' ),
						'tab'   => Controls_Manager::TAB_SETTINGS,
					)
				);

					$document->add_control(
						'single_preview_type',
						array(
							'label'       => esc_html__( 'Preview Dynamic Content as', 'porto-functionality' ),
							'label_block' => true,
							'type'        => Controls_Manager::SELECT,
							'default'     => 'post',
							'groups'      => array(
								'single' => array(
									'label'   => esc_html__( 'Single', 'porto-functionality' ),
									'options' => $post_types,
								),
							),
							'export'      => false,
						)
					);

					$document->add_control(
						'single_preview_apply',
						array(
							'type'        => Controls_Manager::BUTTON,
							'label'       => esc_html__( 'Apply & Preview', 'porto-functionality' ),
							'label_block' => true,
							'show_label'  => false,
							'text'        => esc_html__( 'Apply & Preview', 'porto-functionality' ),
							'separator'   => 'none',
						)
					);

					$document->end_controls_section();
			}
		}

		/**
		 * Apply Global Post Type to Builder Type
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function restore_global_single_variable() {

			$this->find_preview();

			if ( ! $this->edit_post && ( is_singular( PortoBuilders::BUILDER_SLUG ) || ( ! empty( $_REQUEST['post'] ) && PortoBuilders::BUILDER_SLUG == get_post_type( (int) $_REQUEST['post'] ) ) || ( isset( $_REQUEST['context'] ) && 'edit' == $_REQUEST['context'] ) || ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' == $_REQUEST['action'] ) || ( isset( $_REQUEST['vc_editable'] ) && $_REQUEST['vc_editable'] ) || ( ! empty( $_REQUEST['wpb_vc_js_status'] ) && ! empty( $_REQUEST['post'] ) ) ) ) {
				$query = new WP_Query(
					array(
						'post_type'           => $this->edit_post_type,
						'post_status'         => 'publish',
						'posts_per_page'      => 1,
						'ignore_sticky_posts' => true,
					)
				);
				if ( $query->have_posts() ) {
					$the_post        = $query->next_post();
					$this->edit_post = $the_post;
				}
			}
			if ( $this->edit_post ) {
				global $post;
				$post = $this->edit_post;
				setup_postdata( $this->edit_post );
				return true;
			}
			return false;
		}

		/**
		 * Retrieve Global Post Type
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function reset_global_single_variable() {
			if ( $this->edit_post ) {
				wp_reset_postdata();
			}
		}

		/**
		 * Find the registered post type
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function find_preview() {
			if ( $this->edit_post_type ) {
				return;
			}
			if ( ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' == $_REQUEST['action'] ) ||
			( function_exists( 'vc_is_inline' ) && vc_is_inline() ) || ( isset( $_REQUEST['post'] ) && PortoBuilders::BUILDER_SLUG == get_post_type( (int) $_REQUEST['post'] ) ) ) {
				$post_id = 0;
				// backend
				if ( ! empty( $_REQUEST['post'] ) ) {
					$post_id = (int) $_REQUEST['post'];
				}

				// Wpb frontend
				if ( ! empty( $_REQUEST['post_id'] ) ) {
					$post_id = (int) $_REQUEST['post_id'];
				}

				// Elementor Preview
				if ( ! $post_id ) {
					$post_id = get_the_ID();
				}
				if ( 'single' != get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
					return;
				}

				$edit_post_type       = get_post_meta( $post_id, 'preview_type', true );
				$this->edit_post_type = $edit_post_type ? $edit_post_type : 'post';
			}
		}

		/**
		 * Apply preview mode in ajax - Elementor
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function apply_preview_post() {
			check_ajax_referer( 'porto-elementor-nonce', 'nonce' );
			update_post_meta( (int) $_REQUEST['post_id'], 'preview_type', sanitize_title( $_REQUEST['mode'] ) );
			die;
		}

		/**
		 * Apply preview mode in ajax - WP Bakery
		 *
		 * @since 2.3.0
		 * @access public
		 */
		public function apply_preview_wpb_post() {
			check_ajax_referer( 'porto-admin-nonce', 'nonce' );
			update_post_meta( (int) $_REQUEST['post_id'], 'preview_type', sanitize_title( $_REQUEST['mode'] ) );
			die;
		}
	}
	PortoBuildersSingle::get_instance();
endif;
