<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_FAQ_Shortcode' ) ) {

	/**
	 * Implements shortcode for FAQ plugin
	 *
	 * @class   YITH_FAQ_Shortcode
	 * @since   1.0.0
	 * @author  Alberto Ruggiero
	 *
	 */
	class YITH_FAQ_Shortcode {

		/**
		 * @var $post_type string post type name
		 */
		private $post_type = null;

		/**
		 * @var $taxonomy string taxonomy name
		 */
		private $taxonomy = null;

		/**
		 * Constructor
		 *
		 * @param   $post_type string
		 * @param   $taxonomy  string
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function __construct( $post_type, $taxonomy ) {

			$this->post_type = $post_type;
			$this->taxonomy  = $taxonomy;

			add_action( 'init', array( $this, 'add_shortcodes_button' ), 20 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_shortcode_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_shortcode_scripts' ), 99 );
			add_action( 'wp_ajax_ywfp_find_faq', array( $this, 'find_faq' ) );
			add_action( 'wp_ajax_nopriv_ywfp_find_faq', array( $this, 'find_faq' ) );
			add_action( 'init', array( $this, 'gutenberg_block' ) );
			add_action( 'init', array( $this, 'register_styles' ) );

			add_shortcode( 'yith_faq', array( $this, 'print_shortcode' ) );

		}

		/**
		 * Add scripts and styles
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function admin_shortcode_scripts() {

			global $pagenow;

			if ( ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) && $this->can_show_shortcode_buttons() ) {

				wp_enqueue_style( 'yith-faq-shortcode', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/yith-faq-shortcode.css' ), array(), YITH_FWP_VERSION );
				wp_enqueue_script( 'yith-faq-shortcode', yit_load_js_file( YITH_FWP_ASSETS_URL . '/js/yith-faq-shortcode.js' ), array( 'jquery' ), YITH_FWP_VERSION );

				global $post_ID, $temp_ID;

				$query_args = array(
					'action'    => 'yfwp_shortcode_panel',
					'post_id'   => (int) ( 0 == $post_ID ? $temp_ID : $post_ID ),
					'KeepThis'  => true,
					'TB_iframe' => true
				);

				wp_localize_script( 'yith-faq-shortcode', 'yfwp_shortcode', array(
					'lightbox_url' => add_query_arg( $query_args, admin_url( 'admin.php' ) ),
					'title'        => esc_html__( 'Add FAQ shortcode', 'yith-faq-plugin-for-wordpress' ),
				) );

			}

		}

		/**
		 * Register styles
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function register_styles() {

			wp_register_style( 'font-awesome', "https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css", array(), '4.6.3' );
			wp_register_style( 'yith-faq-shortcode-frontend', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/yith-faq-shortcode-frontend.css' ), array( 'font-awesome' ), YITH_FWP_VERSION );

			$style_options = array();
			$custom_css    = '';

			if ( ywfp_get_option( 'customize-search', 'no' ) == 'yes' ) {
				$style_options['.yith-faqs-search-button button']['background']       = ywfp_get_option( 'search-color', '' );
				$style_options['.yith-faqs-search-button button']['color']            = ywfp_get_option( 'search-icon-color', '' );
				$style_options['.yith-faqs-search-button button:hover']['background'] = ywfp_get_option( 'search-color-hover', '' );
				$style_options['.yith-faqs-search-button button:hover']['color']      = ywfp_get_option( 'search-icon-color-hover', '' );
			}

			if ( ywfp_get_option( 'customize-category', 'no' ) == 'yes' ) {
				$style_options['ul.yith-faqs-categories li a']['background']        = ywfp_get_option( 'category-color', '' );
				$style_options['ul.yith-faqs-categories li a']['color']             = ywfp_get_option( 'category-text-color', '' );
				$style_options['ul.yith-faqs-categories li a:hover']['background']  = ywfp_get_option( 'category-color-hover', '' );
				$style_options['ul.yith-faqs-categories li a.active']['background'] = ywfp_get_option( 'category-color-hover', '' );
				$style_options['ul.yith-faqs-categories li a:hover']['background']  = ywfp_get_option( 'category-color-hover', '' );
				$style_options['ul.yith-faqs-categories li a.active']['color']      = ywfp_get_option( 'category-text-color-hover', '' );
			}

			if ( ywfp_get_option( 'customize-navigation', 'no' ) == 'yes' ) {
				$style_options['.yith-faqs-pagination > ul > li > a']['background']                   = ywfp_get_option( 'navigation-color', '' );
				$style_options['.yith-faqs-pagination > ul > li > a']['color']                        = ywfp_get_option( 'navigation-text-color', '' );
				$style_options['.yith-faqs-pagination > ul > li.disabled > span']['background']       = ywfp_get_option( 'navigation-color', '' );
				$style_options['.yith-faqs-pagination > ul > li.disabled > span:hover']['background'] = ywfp_get_option( 'navigation-color', '' );
				$style_options['.yith-faqs-pagination > ul > li.disabled > span']['color']            = ywfp_get_option( 'navigation-text-color', '' );
				$style_options['.yith-faqs-pagination > ul > li.disabled > span:hover']['color']      = ywfp_get_option( 'navigation-text-color', '' );
				$style_options['.yith-faqs-pagination > ul > li > a:hover']['background']             = ywfp_get_option( 'navigation-color-hover', '' );
				$style_options['.yith-faqs-pagination > ul > li.active > a']['background']            = ywfp_get_option( 'navigation-color-hover', '' );
				$style_options['.yith-faqs-pagination > ul > li.active > a:hover']['background']      = ywfp_get_option( 'navigation-color-hover', '' );
				$style_options['.yith-faqs-pagination > ul > li > a:hover']['background']             = ywfp_get_option( 'navigation-color-hover', '' );
				$style_options['.yith-faqs-pagination > ul > li.active > a']['color']                 = ywfp_get_option( 'navigation-text-color-hover', '' );
				$style_options['.yith-faqs-pagination > ul > li.active > a:hover']['color']           = ywfp_get_option( 'navigation-text-color-hover', '' );
			}

			if ( ywfp_get_option( 'customize-icons', 'no' ) == 'yes' ) {
				$style_options['.yith-faqs-title .icon']['background'] = ywfp_get_option( 'icon-background-color', '' );
				$style_options['.yith-faqs-title .icon']['color']      = ywfp_get_option( 'icon-color', '' );
			}

			if ( ywfp_get_option( 'customize-link', 'no' ) == 'yes' ) {
				$style_options['.yith-faqs-link > a > i']['background']       = ywfp_get_option( 'link-color', '' );
				$style_options['.yith-faqs-link > a > i']['color']            = ywfp_get_option( 'link-icon-color', '' );
				$style_options['.yith-faqs-link > a.hover > i']['background'] = ywfp_get_option( 'link-color-hover', '' );
				$style_options['.yith-faqs-link > a.hover > i']['color']      = ywfp_get_option( 'link-icon-color-hover', '' );
			}

			foreach ( $style_options as $selector => $rule ) {

				$rules = '';

				foreach ( $rule as $css => $value ) {

					if ( $value != '' ) {
						$rules .= $css . ':' . $value . ';' . "\n";
					}

				}

				if ( $rules != '' ) {
					$custom_css .= $selector . '{' . "\n" . $rules . '}' . "\n";
				}

			}

			wp_add_inline_style( 'yith-faq-shortcode-frontend', $custom_css );
		}

		/**
		 * Add scripts and styles
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function frontend_shortcode_scripts() {

			global $post;

			if ( ! $post ) {
				return;
			}

			//APPLY_FILTER: yfwp_add_scripts: add FAQ script in the page anyway. this is useful with some page builder
			if ( has_shortcode( $post->post_content, 'yith_faq' ) || apply_filters( 'yfwp_add_scripts', false, $post ) ) {


				wp_enqueue_style( 'yith-faq-shortcode-frontend' );
				wp_enqueue_script( 'yith-faq-shortcode-frontend', yit_load_js_file( YITH_FWP_ASSETS_URL . '/js/yith-faq-shortcode-frontend.js' ), array( 'jquery' ), YITH_FWP_VERSION, true );

				$params = array(
					'ajax_url'      => admin_url( 'admin-ajax.php' ),
					'page_id'       => $post->ID,
					//APPLY_FILTER: yith_faq_enable_scroll: enable scrolling to a specific FAQ
					'enable_scroll' => apply_filters( 'yith_faq_enable_scroll', true ),
					//APPLY_FILTER: yith_faq_scroll_offset: offest for the scrolling
					'scroll_offset' => apply_filters( 'yith_faq_scroll_offset', 0 ),
				);

				wp_localize_script( 'yith-faq-shortcode-frontend', 'yith_faq', $params );

			}

		}

		/**
		 * Add shortcode button to TinyMCE editor, adding filter on mce_external_plugins
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function add_shortcodes_button() {

			//APPLY_FILTER: yfwp_instantiate_shortcode_button: check if shortcode button can be instantiated
			if ( is_admin() || apply_filters( 'yfwp_instantiate_shortcode_button', false ) ) {
				add_filter( 'mce_external_plugins', array( &$this, 'add_shortcodes_tinymce_plugin' ) );
				add_filter( 'mce_buttons', array( &$this, 'register_shortcodes_button' ) );
				add_action( 'media_buttons', array( $this, 'media_buttons_context' ) );
			}

		}

		/**
		 * Add a script to TinyMCE script list
		 *
		 * @param   $plugin_array array
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function add_shortcodes_tinymce_plugin( $plugin_array ) {

			if ( $this->can_show_shortcode_buttons() ) {

				$plugin_array['yfwp_shortcode'] = yit_load_js_file( YITH_FWP_ASSETS_URL . '/js/yith-faq-tinymce.js' );
			}

			return $plugin_array;

		}

		/**
		 * Make TinyMCE know a new button was included in its toolbar
		 *
		 * @param   $buttons array
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function register_shortcodes_button( $buttons ) {

			if ( $this->can_show_shortcode_buttons() ) {

				array_push( $buttons, "|", "yfwp_shortcode" );
			}

			return $buttons;

		}

		/**
		 * The markup of shortcode
		 *
		 * @param   $context string
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function media_buttons_context( $context ) {

			if ( $this->can_show_shortcode_buttons() ) {
				echo '<a id="yfwp_shortcode" href="#" class="hide-if-no-js" title=""></a>';
			}

			return $context;

		}

		/**
		 * Set post types where not show the shortcode
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_disabled_post_types() {

			$post_types = array(
				$this->post_type
			);

			//APPLY_FILTER: yith_faq_disabled_post_types : post types where not show the FAQ shortcode button
			return apply_filters( 'yith_faq_disabled_post_types', $post_types );
		}

		/**
		 * Check if shortcode buttons can be shown on the edit page
		 *
		 * @return  boolean
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function can_show_shortcode_buttons() {

			global $post;

			return ( $post && ! in_array( $post->post_type, $this->get_disabled_post_types() ) );

		}

		/**
		 * Output shortcode
		 *
		 * @param   $args array
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function print_shortcode( $args ) {

			$defaults = array(
				'search_box'       => 'no',
				'category_filters' => 'no',
				'style'            => 'list',
				'categories'       => '',
				'page_size'        => '10',
				'show_icon'        => 'right',
				'icon_size'        => '14',
				'icon'             => 'FontAwesome:plus'
			);

			$args = shortcode_atts( $defaults, $args );

			$paged      = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
			$category   = isset( $_GET['term_id'] ) ? $_GET['term_id'] : '';
			$categories = ( $category != '' && $category != 'all' ) ? $category : $args['categories'];
			$permalink  = get_permalink();
			$options    = array(
				'post_type'      => $this->post_type,
				'posts_per_page' => $args['page_size'],
				'paged'          => $paged,
				'post_status'    => 'publish',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			);

			if ( $categories != '' ) {

				$options['tax_query'] = array(
					array(
						'taxonomy' => $this->taxonomy,
						'field'    => 'term_id',
						'terms'    => explode( ',', $categories )
					)
				);

			}

			add_filter( 'posts_where', array( $this, 'modify_query_where' ) );
			$faqs = new WP_Query( $options );
			remove_filter( 'posts_where', array( $this, 'modify_query_where' ) );

			ob_start();

			?>
            <div class="yith-faqs">

				<?php if ( $args['search_box'] == 'on' ):

					//APPLY_FILTER: yith_fwp_search_placeholder: modify the placeholder of the FAQ search field
					$search_field_text = apply_filters( 'yith_fwp_search_placeholder', esc_html__( 'Search FAQ', 'yith-faq-plugin-for-wordpress' ) );

					?>

                    <div class="yith-faqs-search">

                        <div class="yith-faqs-search-container">
                            <div class="yith-faqs-search-input">
                                <input type="text" value="" name="search" placeholder="<?php echo $search_field_text; ?>" />
                            </div>
                            <div class="yith-faqs-search-button">
                                <button type="submit"><i class="fa fa-search"></i></button>
                            </div>
                        </div>

                    </div>

				<?php endif; ?>

				<?php if ( $args['category_filters'] == 'on' ): ?>

					<?php
					$cat_args = array(
						'taxonomy' => $this->taxonomy,
						'include'  => explode( ',', $args['categories'] ),
						'orderby'  => 'id',
						'order'    => 'ASC'
					);

					$categories = get_categories( $cat_args );
					?>

                    <ul class="yith-faqs-categories">
                        <li><a href="?term_id=all" class="<?php echo ( $category == 'all' || $category == '' ) ? 'active' : '' ?>"><?php esc_html_e( 'All Categories', 'yith-faq-plugin-for-wordpress' ) ?></a></li>
						<?php foreach ( $categories as $cat ) : ?>
                            <li><a href="?term_id=<?php echo $cat->term_id ?>" class="<?php echo ( $category == $cat->term_id ) ? 'active' : '' ?>"><?php echo $cat->name ?></a></li>
						<?php endforeach ?>
                    </ul>

				<?php endif; ?>

                <div id="yith-faqs-container" class="yith-faqs-container yith-faq-type-<?php echo $args['style'] ?>">

					<?php if ( ! $faqs->have_posts() ): ?>

                        <div class="yith-faqs-no-results">
							<?php esc_html_e( 'Sorry, no matching results for your search.', 'yith-faq-plugin-for-wordpress' ) ?>
                        </div>

					<?php endif; ?>

					<?php while ( $faqs->have_posts() ) : ?>

						<?php $faqs->the_post();

						$icon_style  = $args['icon_size'] != '14' ? 'style="font-size: ' . $args['icon_size'] . 'px; line-height: ' . $args['icon_size'] . 'px;"' : '';
						$line_height = $args['icon_size'] != '14' ? 'style="height: ' . ( $args['icon_size'] + 20 ) . 'px; line-height: ' . ( $args['icon_size'] + 20 ) . 'px; padding-' . $args['show_icon'] . ': ' . ( $args['icon_size'] + 20 ) . 'px;"' : '';

						?>
                        <div id="faq-<?php echo get_the_ID() ?>" class="yith-faqs-item">
                            <div class="yith-faqs-title <?php echo ( $args['show_icon'] != 'off' && $args['style'] != 'list' ) ? 'icon-' . $args['show_icon'] : '' ?>" <?php echo $line_height ?>>
								<?php if ( ( $args['show_icon'] != 'off' && $args['style'] != 'list' ) ): ?>
                                    <div class="icon <?php echo $this->get_icon_class( $args['icon'] ) ?>" <?php echo $icon_style ?>></div>
								<?php endif; ?>
                                <b><?php the_title() ?></b>
                            </div>
                            <div class="yith-faqs-content-wrapper">
                                <div class="yith-faqs-content">
									<?php
									//DO_ACTION: yith_fwp_before_content: hook before FAQ content
									do_action( 'yith_fwp_before_content' );

									echo wpautop( get_the_content() );

									//DO_ACTION: yith_fwp_after_content: hook after FAQ content
									do_action( 'yith_fwp_after_content' );
									?>
                                </div>
                                <div class="yith-faqs-link">
                                    <a class="yith-faqs-copy" href="#" data-faq="<?php echo $permalink ?>#faq-<?php echo get_the_ID() ?>">
                                        <span>
                                            <span class="hover-text"><?php esc_html_e( 'Copy FAQ Link', 'yith-faq-plugin-for-wordpress' ); ?></span>
                                            <span class="success-text"><?php esc_html_e( 'Copied!', 'yith-faq-plugin-for-wordpress' ); ?></span>
                                        </span>
                                        <i class="fa fa-link"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

					<?php endwhile; ?>

                </div>

				<?php if ( $faqs->max_num_pages > 1 ) : ?>

					<?php
					$prev_class = ( $paged > 1 ? '' : 'disabled' );
					$next_class = ( $paged < $faqs->max_num_pages ? '' : 'disabled' );
					?>

                    <nav class="yith-faqs-pagination">
                        <ul>
                            <li class="yith-faqs-page yith-faqs-first <?php echo $prev_class ?>">
								<?php if ( $prev_class == 'disabled' ): ?>
                                    <span>
                                    <span aria-hidden="true">&laquo;</span>
                                </span>
								<?php else: ?>
                                    <a href="<?php echo '?page=' . ( $paged - 1 ); ?>" aria-label="<?php esc_html_e( 'Previous', 'yith-faq-plugin-for-wordpress' ) ?>">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
								<?php endif; ?>
                            </li>

							<?php for ( $i = 1; $i <= $faqs->max_num_pages; $i ++ ): ?>
                                <li class="yith-faqs-page page-<?php echo $i; ?> <?php echo( $paged == $i ? 'active' : '' ) ?>">
                                    <a href="<?php echo '?page=' . $i; ?>"><?php echo $i; ?></a>
                                </li>
							<?php endfor; ?>

                            <li class="yith-faqs-page yith-faqs-last  <?php echo $next_class ?>">
								<?php if ( $next_class == 'disabled' ): ?>
                                    <span>
                                    <span aria-hidden="true">&raquo;</span>
                                </span>
								<?php else: ?>
                                    <a href="<?php echo '?page=' . ( $paged + 1 ); ?>" aria-label="<?php esc_html_e( 'Next', 'yith-faq-plugin-for-wordpress' ) ?>">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
								<?php endif; ?>
                            </li>
                        </ul>
                    </nav>

				<?php endif; ?>

            </div>
			<?php

			$output = ob_get_clean();

			wp_reset_query();
			wp_reset_postdata();

			return $output;

		}

		/**
		 * Manage word search
		 *
		 * @param   $where string
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function modify_query_where( $where ) {

			if ( isset( $_GET['faq-s'] ) ) {

				global $wpdb;

				$search_terms  = explode( ' ', $_GET['faq-s'] );
				$search_string = '';

				foreach ( $search_terms as $term ) {
					$search_string .= $wpdb->prefix . 'posts.post_title LIKE "%' . $term . '%" OR ' . $wpdb->prefix . 'posts.post_content LIKE "%' . $term . '%" OR ';
				}

				$search_string = rtrim( $search_string, ' OR ' );

				$where .= ' AND ( ' . $search_string . ' )';

			}

			return $where;

		}

		/**
		 * Get Icon Class
		 *
		 * @param   $icon string
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function get_icon_class( $icon ) {

			$icon_data  = explode( ':', $icon );
			$icon_class = '';

			if ( $icon_data[0] == 'FontAwesome' ) {
				$icon_class = 'fa fa-' . $icon_data[1];
			}

			return $icon_class;

		}

		/**
		 * Find FAQ by hash
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function find_faq() {

			try {

				$faq_id   = str_replace( '#faq-', '', $_POST['faq_id'] );
				$faq      = get_post( $faq_id );
				$faq_page = 0;

				if ( $faq && $faq->post_type == $this->post_type ) {

					$post       = get_post( $_POST['page_id'] );
					$args       = shortcode_parse_atts( $post->post_content );
					$page_size  = isset( $args['page_size'] ) ? $args['page_size'] : '10';
					$categories = isset ( $args['categories'] ) ? $args['categories'] : '';

					$options = array(
						'post_type'      => $this->post_type,
						'posts_per_page' => - 1,
						'post_status'    => 'publish',
						'meta_query'     => array(
							array(
								'key'   => '_enabled_faq',
								'value' => 'yes',
							),
						),
						'orderby'        => 'menu_order',
						'order'          => 'ASC',
					);

					if ( $categories != '' ) {

						$options['tax_query'] = array(
							array(
								'taxonomy' => $this->taxonomy,
								'field'    => 'term_id',
								'terms'    => explode( ',', $categories )
							)
						);

					}

					$faqs = new WP_Query( $options );

					$index = 1;
					$page  = 1;

					if ( $faqs->have_posts() ) {

						foreach ( $faqs->posts as $faq ) {

							if ( $faq->ID == $faq_id ) {
								$faq_page = $page;
								break;
							}

							if ( $index == $page_size ) {
								$page ++;
								$index = 1;
							} else {
								$index ++;
							}

						}

					}

					wp_reset_query();
					wp_reset_postdata();

				}

				wp_send_json( array( 'success' => true, 'page' => $faq_page ) );

			} catch ( Exception $e ) {

				wp_send_json( array( 'success' => false, 'error' => $e->getMessage() ) );

			}

		}

		/**
		 * Set shortcode
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function gutenberg_block() {

			$categories = get_terms( array(
				                         'taxonomy'   => $this->taxonomy,
				                         'hide_empty' => false,
			                         ) );

			$terms = array();
			foreach ( $categories as $category ) {
				$terms[ $category->term_id ] = $category->name;
			}

			$blocks = array(
				'yith-faq-shortcode' => array(
					'style'          => 'yith-faq-shortcode-frontend',
					'title'          => esc_html_x( 'FAQ', '[gutenberg]: block name', 'yith-faq-plugin-for-wordpress' ),
					'description'    => esc_html_x( 'Add the FAQ shortcode', '[gutenberg]: block description', 'yith-faq-plugin-for-wordpress' ),
					'shortcode_name' => 'yith_faq',
					'do_shortcode'   => true,
					'keywords'       => array(
						esc_html_x( 'FAQ', '[gutenberg]: keywords', 'yith-faq-plugin-for-wordpress' ),
						esc_html_x( 'Frequently Asked Questions', '[gutenberg]: keywords', 'yith-faq-plugin-for-wordpress' ),
					),
					'attributes'     => array(
						'search_box'       => array(
							'type'    => 'select',
							'label'   => esc_html_x( 'Show search box', '[gutenberg]: attribute description', 'yith-faq-plugin-for-wordpress' ),
							'default' => 'off',
							'options' => array(
								'on'  => esc_html_x( 'Show', '[gutenberg]: Help text', 'yith-faq-plugin-for-wordpress' ),
								'off' => esc_html_x( 'Hide', '[gutenberg]: Help text', 'yith-faq-plugin-for-wordpress' ),
							),
						),
						'category_filters' => array(
							'type'    => 'select',
							'label'   => esc_html_x( 'Show category filters', '[gutenberg]: attribute description', 'yith-faq-plugin-for-wordpress' ),
							'default' => 'off',
							'options' => array(
								'on'  => esc_html_x( 'Show', '[gutenberg]: Help text', 'yith-faq-plugin-for-wordpress' ),
								'off' => esc_html_x( 'Hide', '[gutenberg]: Help text', 'yith-faq-plugin-for-wordpress' ),
							),
						),
						'style'            => array(
							'type'    => 'radio',
							'label'   => esc_html_x( 'Choose the style', '[gutenberg]: block description', 'yith-faq-plugin-for-wordpress' ),
							'options' => array(
								'list'      => esc_html_x( 'List', '[gutenberg]: inspector description', 'yith-faq-plugin-for-wordpress' ),
								'accordion' => esc_html_x( 'Accordion', '[gutenberg]: inspector description', 'yith-faq-plugin-for-wordpress' ),
								'toggle'    => esc_html_x( 'Toggle', '[gutenberg]: inspector description', 'yith-faq-plugin-for-wordpress' ),
							),
							'default' => 'list'
						),
						'number'           => array(
							'type'    => 'number',
							'label'   => esc_html_x( 'FAQs per page', '[gutenberg]: attributes description', 'yith-faq-plugin-for-wordpress' ),
							'default' => 10,
							'min'     => 5,
							'max'     => 20
						),
						'categories'       => array(
							'type'     => 'select',
							'label'    => esc_html_x( 'Categories to display', '[gutenberg]: block description', 'yith-faq-plugin-for-wordpress' ),
							'options'  => $terms,
							'multiple' => true,
							'default'  => array()
						),
						'show_icon'        => array(
							'type'    => 'radio',
							'label'   => esc_html_x( 'Show icon', '[gutenberg]: block description', 'yith-faq-plugin-for-wordpress' ),
							'options' => array(
								'off'   => esc_html_x( 'Off', '[gutenberg]: inspector description', 'yith-faq-plugin-for-wordpress' ),
								'left'  => esc_html_x( 'Left', '[gutenberg]: inspector description', 'yith-faq-plugin-for-wordpress' ),
								'right' => esc_html_x( 'Right', '[gutenberg]: inspector description', 'yith-faq-plugin-for-wordpress' ),
							),
							'default' => 'right'
						),
						'icon_size'        => array(
							'type'    => 'number',
							'label'   => esc_html_x( 'Icon size (px)', '[gutenberg]: attributes description', 'yith-faq-plugin-for-wordpress' ),
							'default' => 14,
							'min'     => 8,
							'max'     => 40
						),
						'icon'             => array(
							'type'    => 'select',
							'label'   => esc_html_x( 'Choose the icon', '[gutenberg]: block description', 'yith-faq-plugin-for-wordpress' ),
							'options' => array(
								'FontAwesome:plus'                => 'plus',
								'FontAwesome:plus-circle'         => 'plus-circle',
								'FontAwesome:plus-square'         => 'plus-square',
								'FontAwesome:plus-square-o'       => 'plus-square-o',
								'FontAwesome:chevron-down'        => 'chevron-down',
								'FontAwesome:chevron-circle-down' => 'chevron-circle-down',
								'FontAwesome:arrow-circle-o-down' => 'arrow-circle-o-down',
								'FontAwesome:arrow-down'          => 'arrow-down',
								'FontAwesome:arrow-circle-down'   => 'arrow-circle-down',
								'FontAwesome:angle-double-down'   => 'angle-double-down',
								'FontAwesome:angle-down'          => 'angle-down',
								'FontAwesome:caret-down'          => 'caret-down',
								'FontAwesome:caret-square-o-down' => 'caret-square-o-down',
							),
						),
					)

				),
			);

			yith_plugin_fw_gutenberg_add_blocks( $blocks );

		}

	}

}