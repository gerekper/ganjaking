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
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct( $post_type, $taxonomy ) {

			$this->post_type = $post_type;
			$this->taxonomy  = $taxonomy;

			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_shortcode_scripts' ), 99 );
			add_action( 'wp_ajax_yfwp_find_faq', array( $this, 'find_faq' ) );
			add_action( 'wp_ajax_nopriv_yfwp_find_faq', array( $this, 'find_faq' ) );
			add_action( 'init', array( $this, 'gutenberg_block' ) );
			add_action( 'init', array( $this, 'register_styles' ) );
			add_filter( 'yith_faq_add_scripts', array( $this, 'use_elementor' ), 10, 2 );
			add_shortcode( 'yith_faq', array( $this, 'print_shortcode' ) );

		}

		/**
		 * Register styles
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function register_styles() {

			wp_register_style( 'yith-faq-shortcode-frontend', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/frontend.css' ), array(), YITH_FWP_VERSION );
			wp_register_style( 'yith-faq-shortcode-icons', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/icons.css' ), array(), YITH_FWP_VERSION );
			wp_register_script( 'yith-faq-shortcode-frontend', yit_load_js_file( YITH_FWP_ASSETS_URL . '/js/frontend.js' ), array( 'jquery' ), YITH_FWP_VERSION, true );

			$style_options = array();
			$custom_css    = '';

			if ( yfwp_get_option( 'customize-search', 'no' ) === 'yes' ) {
				$style_options['.yith-faqs-search-button button']['background']       = yfwp_get_option( 'search-color', '' );
				$style_options['.yith-faqs-search-button button']['color']            = yfwp_get_option( 'search-icon-color', '' );
				$style_options['.yith-faqs-search-button button:hover']['background'] = yfwp_get_option( 'search-color-hover', '' );
				$style_options['.yith-faqs-search-button button:hover']['color']      = yfwp_get_option( 'search-icon-color-hover', '' );
			}

			if ( yfwp_get_option( 'customize-category', 'no' ) === 'yes' ) {
				$style_options['ul.yith-faqs-categories li a']['background']        = yfwp_get_option( 'category-color', '' );
				$style_options['ul.yith-faqs-categories li a']['color']             = yfwp_get_option( 'category-text-color', '' );
				$style_options['ul.yith-faqs-categories li a:hover']['background']  = yfwp_get_option( 'category-color-hover', '' );
				$style_options['ul.yith-faqs-categories li a.active']['background'] = yfwp_get_option( 'category-color-hover', '' );
				$style_options['ul.yith-faqs-categories li a:hover']['background']  = yfwp_get_option( 'category-color-hover', '' );
				$style_options['ul.yith-faqs-categories li a.active']['color']      = yfwp_get_option( 'category-text-color-hover', '' );
			}

			if ( yfwp_get_option( 'customize-navigation', 'no' ) === 'yes' ) {
				$style_options['.yith-faqs-pagination > ul > li > a']['background']                   = yfwp_get_option( 'navigation-color', '' );
				$style_options['.yith-faqs-pagination > ul > li > a']['color']                        = yfwp_get_option( 'navigation-text-color', '' );
				$style_options['.yith-faqs-pagination > ul > li.disabled > span']['background']       = yfwp_get_option( 'navigation-color', '' );
				$style_options['.yith-faqs-pagination > ul > li.disabled > span:hover']['background'] = yfwp_get_option( 'navigation-color', '' );
				$style_options['.yith-faqs-pagination > ul > li.disabled > span']['color']            = yfwp_get_option( 'navigation-text-color', '' );
				$style_options['.yith-faqs-pagination > ul > li.disabled > span:hover']['color']      = yfwp_get_option( 'navigation-text-color', '' );
				$style_options['.yith-faqs-pagination > ul > li > a:hover']['background']             = yfwp_get_option( 'navigation-color-hover', '' );
				$style_options['.yith-faqs-pagination > ul > li.active > a']['background']            = yfwp_get_option( 'navigation-color-hover', '' );
				$style_options['.yith-faqs-pagination > ul > li.active > a:hover']['background']      = yfwp_get_option( 'navigation-color-hover', '' );
				$style_options['.yith-faqs-pagination > ul > li > a:hover']['background']             = yfwp_get_option( 'navigation-color-hover', '' );
				$style_options['.yith-faqs-pagination > ul > li.active > a']['color']                 = yfwp_get_option( 'navigation-text-color-hover', '' );
				$style_options['.yith-faqs-pagination > ul > li.active > a:hover']['color']           = yfwp_get_option( 'navigation-text-color-hover', '' );
			}

			if ( yfwp_get_option( 'customize-icons', 'no' ) === 'yes' ) {
				$style_options['.yith-faqs-title .icon']['background'] = yfwp_get_option( 'icon-background-color', '' );
				$style_options['.yith-faqs-title .icon']['color']      = yfwp_get_option( 'icon-color', '' );
			}

			if ( yfwp_get_option( 'customize-link', 'no' ) === 'yes' ) {
				$style_options['.yith-faqs-link > a > i']['background']       = yfwp_get_option( 'link-color', '' );
				$style_options['.yith-faqs-link > a > i']['color']            = yfwp_get_option( 'link-icon-color', '' );
				$style_options['.yith-faqs-link > a.hover > i']['background'] = yfwp_get_option( 'link-color-hover', '' );
				$style_options['.yith-faqs-link > a.hover > i']['color']      = yfwp_get_option( 'link-icon-color-hover', '' );
			}

			foreach ( $style_options as $selector => $rule ) {

				$rules = '';

				foreach ( $rule as $css => $value ) {

					if ( '' !== $value ) {
						$rules .= $css . ':' . $value . ';' . "\n";
					}
				}

				if ( '' !== $rules ) {
					$custom_css .= $selector . '{' . "\n" . $rules . '}' . "\n";
				}
			}

			wp_add_inline_style( 'yith-faq-shortcode-frontend', $custom_css );
		}

		/**
		 * Check if current page uses Elementor
		 *
		 * @param   $value boolean
		 * @param   $post  WP_Post
		 *
		 * @return  boolean
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function use_elementor( $value, $post ) {
			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				$value = \Elementor\Plugin::$instance->db->is_built_with_elementor( $post->ID );
			}

			return $value;
		}

		/**
		 * Add scripts and styles
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function frontend_shortcode_scripts() {

			global $post;

			if ( ! $post ) {
				return;
			}

			//APPLY_FILTER: yith_faq_add_scripts: add FAQ script in the page anyway. this is useful with some page builder
			if ( has_shortcode( $post->post_content, 'yith_faq' ) || apply_filters( 'yith_faq_add_scripts', false, $post ) ) {

				wp_enqueue_style( 'yith-faq-shortcode-frontend' );
				wp_enqueue_style( 'yith-faq-shortcode-icons' );
				wp_enqueue_script( 'yith-faq-shortcode-frontend' );

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
		 * Output shortcode
		 *
		 * @param   $args array
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
				'icon'             => 'yfwp:plus',
			);

			$args = shortcode_atts( $defaults, $args );

			$paged      = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
			$category   = isset( $_GET['term_id'] ) ? $_GET['term_id'] : '';
			$categories = ( '' !== $category && 'all' !== $category ) ? $category : $args['categories'];
			$permalink  = get_permalink();
			$options    = array(
				'post_type'      => $this->post_type,
				'posts_per_page' => $args['page_size'],
				'paged'          => $paged,
				'post_status'    => 'publish',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			);

			if ( '' !== $categories ) {

				$options['tax_query'] = array(
					array(
						'taxonomy' => $this->taxonomy,
						'field'    => 'term_id',
						'terms'    => explode( ',', $categories ),
					),
				);

			}

			add_filter( 'posts_where', array( $this, 'modify_query_where' ) );
			$faqs = new WP_Query( $options );
			remove_filter( 'posts_where', array( $this, 'modify_query_where' ) );

			ob_start();

			?>
			<div class="yith-faqs">

				<?php
				if ( 'on' === $args['search_box'] ) :

					//APPLY_FILTER: yith_faq_search_placeholder: modify the placeholder of the FAQ search field
					$search_field_text = apply_filters( 'yith_faq_search_placeholder', esc_html__( 'Search FAQ', 'yith-faq-plugin-for-wordpress' ) );

					?>

					<div class="yith-faqs-search">
						<div class="yith-faqs-search-container">
							<div class="yith-faqs-search-input">
								<label class="screen-reader-text"><?php echo $search_field_text; ?></label>
								<input type="text" value="" name="search" placeholder="<?php echo $search_field_text; ?>" />
							</div>
							<div class="yith-faqs-search-button">
								<button type="submit"><i class="yfwp-search"></i></button>
							</div>
						</div>
						<div class="yith-faqs-reset-container">
							<a class="yith-faqs-reset" href="<?php echo get_permalink(); ?>"><?php esc_html_e( 'Reset', 'yith-faq-plugin-for-wordpress' ); ?></a>
						</div>
					</div>

				<?php endif; ?>

				<?php
				if ( 'on' === $args['category_filters'] ) :
					?>

					<?php
					$cat_args = array(
						'taxonomy' => $this->taxonomy,
						'include'  => explode( ',', $args['categories'] ),
						//APPLY_FILTER: yith_faq_category_order: modify the placeholder of the FAQ search field
						'orderby'  => apply_filters( 'yith_faq_category_order', 'id' ),
						'order'    => 'ASC',
					);

					$categories = get_categories( $cat_args );
					?>

					<ul class="yith-faqs-categories">
						<li><a href="?term_id=all" class="<?php echo ( 'all' === $category || '' === $category ) ? 'active' : ''; ?>"><?php esc_html_e( 'All Categories', 'yith-faq-plugin-for-wordpress' ); ?></a></li>
						<?php foreach ( $categories as $cat ) : ?>
							<li><a href="?term_id=<?php echo $cat->term_id; ?>" class="<?php echo ( $category === $cat->term_id ) ? 'active' : ''; ?>"><?php echo $cat->name; ?></a></li>
						<?php endforeach ?>
					</ul>

				<?php endif; ?>

				<div id="yith-faqs-container" class="yith-faqs-container yith-faq-type-<?php echo $args['style']; ?>">

					<?php if ( ! $faqs->have_posts() ) : ?>

						<div class="yith-faqs-no-results">
							<?php esc_html_e( 'Sorry, no matching results for your search.', 'yith-faq-plugin-for-wordpress' ); ?>
						</div>

					<?php endif; ?>

					<?php while ( $faqs->have_posts() ) : ?>

						<?php
						$faqs->the_post();

						$icon_style  = '14' !== $args['icon_size'] ? 'style="font-size: ' . $args['icon_size'] . 'px; line-height: ' . $args['icon_size'] . 'px;"' : '';
						$line_height = '14' !== $args['icon_size'] ? 'style="height: ' . ( $args['icon_size'] + 20 ) . 'px; line-height: ' . ( $args['icon_size'] + 20 ) . 'px; padding-' . $args['show_icon'] . ': ' . ( $args['icon_size'] + 20 ) . 'px;"' : '';

						?>
						<div id="faq-<?php echo get_the_ID(); ?>" class="yith-faqs-item">
							<div class="yith-faqs-title <?php echo ( 'off' !== $args['show_icon'] && 'list' !== $args['style'] ) ? 'icon-' . $args['show_icon'] : ''; ?>" <?php echo $line_height; ?>>
								<?php if ( ( 'off' !== $args['show_icon'] && 'list' !== $args['style'] ) ) : ?>
									<div class="icon <?php echo $this->get_icon_class( $args['icon'] ); ?>" <?php echo $icon_style; ?>></div>
								<?php endif; ?>
								<b><?php the_title(); ?></b>
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
									<a class="yith-faqs-copy" href="#" data-faq="<?php echo $permalink; ?>#faq-<?php echo get_the_ID(); ?>">
										<span>
											<span class="hover-text"><?php esc_html_e( 'Copy FAQ Link', 'yith-faq-plugin-for-wordpress' ); ?></span>
											<span class="success-text"><?php esc_html_e( 'Copied!', 'yith-faq-plugin-for-wordpress' ); ?></span>
										</span>
										<i class="yfwp-link"></i>
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
							<li class="yith-faqs-page yith-faqs-first <?php echo $prev_class; ?>">
								<?php if ( 'disabled' === $prev_class ) : ?>
									<span>
										<span aria-hidden="true">&laquo;</span>
									</span>
								<?php else : ?>
									<a href="<?php echo '?page=' . ( $paged - 1 ); ?>" aria-label="<?php esc_html_e( 'Previous', 'yith-faq-plugin-for-wordpress' ); ?>">
										<span aria-hidden="true">&laquo;</span>
									</a>
								<?php endif; ?>
							</li>

							<?php for ( $i = 1; $i <= $faqs->max_num_pages; $i ++ ) : ?>
								<li class="yith-faqs-page page-<?php echo $i; ?> <?php echo( $paged === $i ? 'active' : '' ); ?>">
									<a href="<?php echo '?page=' . $i; ?>"><?php echo $i; ?></a>
								</li>
							<?php endfor; ?>

							<li class="yith-faqs-page yith-faqs-last  <?php echo $next_class; ?>">
								<?php if ( 'disabled' === $next_class ) : ?>
									<span>
										<span aria-hidden="true">&raquo;</span>
									</span>
								<?php else : ?>
									<a href="<?php echo '?page=' . ( $paged + 1 ); ?>" aria-label="<?php esc_html_e( 'Next', 'yith-faq-plugin-for-wordpress' ); ?>">
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
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
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
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_icon_class( $icon ) {

			$icon_data  = explode( ':', $icon );
			$icon_class = '';

			if ( 'FontAwesome' === $icon_data[0] || 'yfwp' === $icon_data[0] ) {
				$icon_class = 'yfwp-' . $icon_data[1];
			}

			return $icon_class;

		}

		/**
		 * Find FAQ by hash
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function find_faq() {

			try {

				$faq_id   = str_replace( '#faq-', '', $_POST['faq_id'] );
				$faq      = get_post( $faq_id );
				$faq_page = 0;

				if ( $faq && $faq->post_type === $this->post_type ) {

					$post       = get_post( $_POST['page_id'] );
					$args       = shortcode_parse_atts( $post->post_content );
					$page_size  = isset( $args['page_size'] ) ? $args['page_size'] : '10';
					$categories = isset( $args['categories'] ) ? $args['categories'] : '';

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

					if ( '' !== $categories ) {

						$options['tax_query'] = array(
							array(
								'taxonomy' => $this->taxonomy,
								'field'    => 'term_id',
								'terms'    => explode( ',', $categories ),
							),
						);

					}

					$faqs  = new WP_Query( $options );
					$index = 1;
					$page  = 1;

					if ( $faqs->have_posts() ) {

						foreach ( $faqs->posts as $faq ) {

							if ( $faq->ID === $faq_id ) {
								$faq_page = $page;
								break;
							}

							if ( $index === $page_size ) {
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

				wp_send_json(
					array(
						'success' => true,
						'page'    => $faq_page,
					)
				);

			} catch ( Exception $e ) {

				wp_send_json(
					array(
						'success' => false,
						'error'   => $e->getMessage(),
					)
				);

			}

		}

		/**
		 * Set shortcode
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function gutenberg_block() {

			$blocks = array(
				'yith-faq-shortcode' => array(
					'style'          => array( 'yith-faq-shortcode-frontend', 'yith-faq-shortcode-icons' ),
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
							'default' => 'list',
						),
						'page_size'        => array(
							'type'    => 'number',
							'label'   => esc_html_x( 'FAQs per page', '[gutenberg]: attributes description', 'yith-faq-plugin-for-wordpress' ),
							'default' => 10,
							//APPLY_FILTER: yith_faq_minimum_page : set minimum number of items in a page
							'min'     => apply_filters( 'yith_faq_minimum_page', 5 ),
							//APPLY_FILTER: yith_faq_maximum_page : set maximum number of items in a page
							'max'     => apply_filters( 'yith_faq_maximum_page', 20 ),
						),
						'categories'       => array(
							'type'     => 'select',
							'label'    => esc_html_x( 'Categories to display', '[gutenberg]: block description', 'yith-faq-plugin-for-wordpress' ),
							'options'  => yfwp_get_categories(),
							'multiple' => true,
							'default'  => array(),
						),
						'show_icon'        => array(
							'type'    => 'radio',
							'label'   => esc_html_x( 'Show icon', '[gutenberg]: block description', 'yith-faq-plugin-for-wordpress' ),
							'options' => array(
								'off'   => esc_html_x( 'Off', '[gutenberg]: inspector description', 'yith-faq-plugin-for-wordpress' ),
								'left'  => esc_html_x( 'Left', '[gutenberg]: inspector description', 'yith-faq-plugin-for-wordpress' ),
								'right' => esc_html_x( 'Right', '[gutenberg]: inspector description', 'yith-faq-plugin-for-wordpress' ),
							),
							'default' => 'right',
						),
						'icon_size'        => array(
							'type'    => 'number',
							'label'   => esc_html_x( 'Icon size (px)', '[gutenberg]: attributes description', 'yith-faq-plugin-for-wordpress' ),
							'default' => 14,
							'min'     => 8,
							'max'     => 40,
						),
						'icon'             => array(
							'type'    => 'select',
							'label'   => esc_html_x( 'Choose the icon', '[gutenberg]: block description', 'yith-faq-plugin-for-wordpress' ),
							'options' => array(
								'yfwp:plus'                => 'plus',
								'yfwp:plus-circle'         => 'plus-circle',
								'yfwp:plus-square'         => 'plus-square',
								'yfwp:plus-square-o'       => 'plus-square-o',
								'yfwp:chevron-down'        => 'chevron-down',
								'yfwp:chevron-circle-down' => 'chevron-circle-down',
								'yfwp:arrow-circle-o-down' => 'arrow-circle-o-down',
								'yfwp:arrow-down'          => 'arrow-down',
								'yfwp:arrow-circle-down'   => 'arrow-circle-down',
								'yfwp:angle-double-down'   => 'angle-double-down',
								'yfwp:angle-down'          => 'angle-down',
								'yfwp:caret-down'          => 'caret-down',
								'yfwp:caret-square-o-down' => 'caret-square-o-down',
							),
						),
					),
				),
			);

			yith_plugin_fw_gutenberg_add_blocks( $blocks );

		}

	}

}
