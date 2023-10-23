<?php
/**
 * Base Shortcode class
 *
 * @package YITH\FAQPluginForWordPress\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_FAQ_Shortcode_Base' ) ) {

	/**
	 * Implements shortcode for FAQ plugin
	 *
	 * @class   YITH_FAQ_Shortcode_Base
	 * @since   2.0.0
	 * @author  YITH <plugins@yithemes.com>
	 * @package YITH\FAQPluginForWordPress\Shortcodes
	 */
	class YITH_FAQ_Shortcode_Base extends YITH_FAQ_Shortcode {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function __construct() {

			parent::__construct();

			add_action( 'wp_ajax_yfwp_find_faq', array( $this, 'find_faq' ) );
			add_action( 'wp_ajax_nopriv_yfwp_find_faq', array( $this, 'find_faq' ) );
			add_action( 'init', array( $this, 'gutenberg_block' ) );
			add_shortcode( 'yith_faq', array( $this, 'print_shortcode' ) );
			global $wp_version;

			/* === Prevent 404 issues for FAQ page === */
			if ( version_compare( $wp_version, 5.5, '>=' ) ) {
				add_filter( 'pre_handle_404', array( $this, 'handle_404' ), 10, 2 );
			}
		}

		/**
		 * Handles 404 errors
		 *
		 * @param boolean  $preempt  404 Handler.
		 * @param WP_Query $wp_query Current WP Query.
		 *
		 * @return  boolean
		 * @since   2.0.0
		 */
		public function handle_404( $preempt, $wp_query ) {

			$object = get_queried_object();

			if ( empty( $object ) || ( $object && ! isset( $object->post_content ) ) ) {
				return $preempt;
			}

			$paged = isset( $_GET['pg'] ) ? (int) $_GET['pg'] : false; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( $paged ) {
				$wp_query->query_vars['pg'] = $paged;
				$preempt                    = true;
			}

			return $preempt;
		}

		/**
		 * Output shortcode
		 *
		 * @param array $args Shortcode arguments.
		 *
		 * @return  string
		 * @since   2.0.0
		 */
		public function print_shortcode( $args ) {

			$defaults       = yfwp_get_shortcode_defaults( yfwp_get_shortcode_allowed_params( 'faqs' ) );
			$args           = shortcode_atts( $defaults, $args );
			$paged          = ( get_query_var( 'pg' ) ) ? get_query_var( 'pg' ) : 1;
			$category       = isset( $_GET['term_id'] ) ? sanitize_text_field( wp_unslash( $_GET['term_id'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$categories     = ( '' !== $category && 'all' !== $category ) ? array( $category ) : ( is_array( $args['categories'] ) ? $args['categories'] : explode( ',', $args['categories'] ) );
			$has_pagination = 'on' === $args['show_pagination'];
			$permalink      = get_permalink();
			$is_first       = true;
			$options        = array(
				'post_type'      => YITH_FWP_FAQ_POST_TYPE,
				'posts_per_page' => $has_pagination ? $args['page_size'] : -1,
				'paged'          => $paged,
				'post_status'    => 'publish',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			);

			if ( ! empty( $categories ) ) {
				//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				$options['tax_query'] = array(
					array(
						'taxonomy' => YITH_FWP_FAQ_TAXONOMY,
						'field'    => 'term_id',
						'terms'    => $categories,
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
				if ( 'on' === $args['search_box'] ) {
					/**
					 * APPLY_FILTERS: yith_faq_search_placeholder
					 *
					 * Modify the placeholder of the FAQ search field.
					 *
					 * @param string $value Placeholder text value.
					 *
					 * @return string
					 */
					$search_field_text = apply_filters( 'yith_faq_search_placeholder', esc_html_x( 'Search FAQ', '[Frontend] Search field placeholder', 'yith-faq-plugin-for-wordpress' ) );
					?>
					<div class="yith-faqs-search">
						<div class="yith-faqs-search-container">
							<div class="yith-faqs-search-input">
								<label class="screen-reader-text"><?php echo esc_attr( $search_field_text ); ?></label>
								<input type="text" value="" name="search" placeholder="<?php echo esc_attr( $search_field_text ); ?>"/>
							</div>
							<div class="yith-faqs-search-button">
								<button type="submit"><i class="yfwp-search"></i></button>
							</div>
						</div>
						<div class="yith-faqs-reset-container">
							<a class="yith-faqs-reset" href="<?php echo esc_attr( get_permalink() ); ?>"><?php echo esc_html_x( 'Reset', '[Frontend] Search field reset button', 'yith-faq-plugin-for-wordpress' ); ?></a>
						</div>
					</div>
					<?php
				}
				if ( 'on' === $args['category_filters'] ) :
					$categories = get_categories(
						array(
							'taxonomy' => YITH_FWP_FAQ_TAXONOMY,
							'include'  => $args['categories'],
							/**
							 * APPLY_FILTERS: yith_faq_category_order
							 *
							 * Modify the ordering crteria of the category list.
							 *
							 * @param string $value Ordering value.
							 *
							 * @return string
							 */
							'orderby'  => apply_filters( 'yith_faq_category_order', 'id' ),
							'order'    => 'ASC',
							/**
							 * APPLY_FILTERS: yith_faq_category_parent
							 *
							 * Set empty string to show also the subcategories.
							 *
							 * @param string $value Parent value.
							 *
							 * @return string
							 */
							'parent'   => apply_filters( 'yith_faq_category_parent', '0' ),
						)
					);
					?>

					<ul class="yith-faqs-categories <?php echo esc_attr( yfwp_get_option( 'filters-layout', yfwp_get_default( 'filters-layout' ) ) ); ?>">
						<li>
							<a href="?term_id=all" class="<?php echo ( 'all' === $category || '' === $category ) ? 'active' : ''; ?>"><?php echo esc_html_x( 'All', '[Frontend] All categories filter button', 'yith-faq-plugin-for-wordpress' ); ?></a>
						</li>
						<?php foreach ( $categories as $cat ) : ?>
							<li>
								<a href="?term_id=<?php echo esc_attr( $cat->term_id ); ?>" class="<?php echo ( (int) $category === $cat->term_id ) ? 'active' : ''; ?>"><?php echo esc_attr( $cat->name ); ?></a>
							</li>
						<?php endforeach ?>
					</ul>

				<?php endif; ?>
				<div id="yith-faqs-container" class="yith-faqs-container yith-faq-type-<?php echo esc_attr( $args['style'] ); ?> <?php echo esc_attr( yfwp_get_option( 'faq-layout', yfwp_get_default( 'faq-layout' ) ) ); ?> <?php echo( $has_pagination ? 'yith-faqs-paged' : 'yith-faqs-no-page' ); ?> <?php ( ! is_admin() ? 'yith-faqs-loading' : '' ); ?> <?php echo esc_attr( yfwp_get_option( 'faq-loader-type', yfwp_get_default( 'faq-loader-type' ) ) ) . '-loader'; ?>" style="--ywfp-icon-font-size: <?php echo esc_attr( $args['icon_size'] ); ?>px;">

					<?php if ( ! $faqs->have_posts() ) : ?>
						<div class="yith-faqs-item yith-faqs-no-results">
							<?php echo esc_html_x( 'Sorry, there are no matching results for your search.', '[Frontend] No results message', 'yith-faq-plugin-for-wordpress' ); ?>
						</div>
					<?php endif; ?>

					<?php while ( $faqs->have_posts() ) : ?>

						<?php
						$faqs->the_post();

						$active  = false;
						$classes = array( 'yith-faqs-item' );

						if ( 'off' !== $args['show_icon'] && 'list' !== $args['style'] ) {
							$classes[] = 'icon-' . $args['show_icon'];
						}

						if ( 'all-open' === $args['expand_faq'] && 'list' !== $args['style'] ) {
							$active    = true;
							$classes[] = 'opened';
						} elseif ( 'first-only' === $args['expand_faq'] && 'list' !== $args['style'] && $is_first ) {
							$active    = true;
							$is_first  = false;
							$classes[] = 'opened';
							$classes[] = 'active';
						}

						?>
						<div id="faq-<?php echo get_the_ID(); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
							<div class="yith-faqs-title">
								<?php if ( ( 'off' !== $args['show_icon'] && 'list' !== $args['style'] ) ) : ?>
									<div class="icon <?php echo esc_attr( $this->get_icon_class( $args['icon'], $active ) ); ?>"></div>
								<?php endif; ?>
								<b><?php the_title(); ?></b>
							</div>
							<div class="yith-faqs-content-wrapper">
								<div class="yith-faqs-content">
									<?php
									/**
									 * DO_ACTION: yith_fwp_before_content
									 *
									 * Hook before FAQ content.
									 */
									do_action( 'yith_fwp_before_content' );

									$content = get_the_content();
									$content = wp_kses_post( wptexturize( $content ) );
									$content = apply_filters( 'the_content', $content );
									$content = str_replace( ']]>', ']]&gt;', $content );
									echo $content; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

									/**
									 * DO_ACTION: yith_fwp_after_content
									 *
									 * Hook after FAQ content.
									 */
									do_action( 'yith_fwp_after_content' );
									?>
								</div>
								<?php if ( 'yes' === yfwp_get_option( 'faq-copy-button', yfwp_get_default( 'faq-copy-button' ) ) ) : ?>
									<div class="yith-faqs-link">
										<a class="yith-faqs-copy" href="#" data-faq="<?php echo esc_url( $permalink ); ?>#faq-<?php echo get_the_ID(); ?>">
											<span class="hover-text"><?php echo esc_html_x( 'Copy link', '[Frontend] Copy FAQ link text', 'yith-faq-plugin-for-wordpress' ); ?></span>
											<span class="success-text"><?php echo esc_html_x( 'Copied!', '[Frontend] Copy faq link message', 'yith-faq-plugin-for-wordpress' ); ?></span>
										</a>
									</div>
								<?php endif; ?>
							</div>
						</div>

					<?php endwhile; ?>

				</div>
				<?php if ( $faqs->max_num_pages > 1 ) : ?>
					<?php
					$prev_class = ( $paged > 1 ? '' : 'disabled' );
					$next_class = ( $paged < $faqs->max_num_pages ? '' : 'disabled' );
					?>
					<nav class="yith-faqs-pagination <?php echo esc_attr( yfwp_get_option( 'pagination-layout', yfwp_get_default( 'pagination-layout' ) ) ); ?>">
						<ul>
							<li class="yith-faqs-page yith-faqs-first <?php echo esc_attr( $prev_class ); ?>">
								<?php if ( 'disabled' === $prev_class ) : ?>
									<span>
										<span aria-hidden="true">&laquo;</span>
									</span>
								<?php else : ?>
									<a href="<?php echo esc_attr( '?pg=' . ( $paged - 1 ) ); ?>" aria-label="<?php echo esc_html_x( 'Previous', '[Frontend] Previous page button label', 'yith-faq-plugin-for-wordpress' ); ?>">
										<span aria-hidden="true">&laquo;</span>
									</a>
								<?php endif; ?>
							</li>

							<?php for ( $i = 1; $i <= $faqs->max_num_pages; $i++ ) : ?>
								<li class="yith-faqs-page page-<?php echo esc_attr( $i ); ?> <?php echo esc_attr( (int) $paged === $i ? 'active' : '' ); ?>">
									<a href="<?php echo esc_attr( '?pg=' . $i ); ?>"><?php echo esc_attr( $i ); ?></a>
								</li>
							<?php endfor; ?>

							<li class="yith-faqs-page yith-faqs-last  <?php echo esc_attr( $next_class ); ?>">
								<?php if ( 'disabled' === $next_class ) : ?>
									<span>
										<span aria-hidden="true">&raquo;</span>
									</span>
								<?php else : ?>
									<a href="<?php echo esc_attr( '?pg=' . ( $paged + 1 ) ); ?>" aria-label="<?php echo esc_html_x( 'Next', '[Frontend] Next page button label', 'yith-faq-plugin-for-wordpress' ); ?>">
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

			wp_reset_postdata();

			return $output;

		}

		/**
		 * Manage word search
		 *
		 * @param string $where Search query WHERE clause.
		 *
		 * @return  string
		 * @since   2.0.0
		 */
		public function modify_query_where( $where ) {

			$search = isset( $_GET['faq-s'] ) ? sanitize_text_field( wp_unslash( $_GET['faq-s'] ) ) : false; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( $search ) {

				global $wpdb;

				$search_terms  = explode( ' ', $search );
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
		 * @param string  $icon   Icon name.
		 * @param boolean $active Get the active status icon.
		 *
		 * @return  string
		 * @since   2.0.0
		 */
		public function get_icon_class( $icon, $active ) {

			$icon_data  = explode( ':', $icon );
			$icon_class = '';

			if ( 'FontAwesome' === $icon_data[0] || 'yfwp' === $icon_data[0] ) {
				$icon_class = 'yfwp-' . $icon_data[1];
			}

			if ( $active ) {
				$icon_class = str_replace( 'up', 'down', $icon_class );
				$icon_class = str_replace( 'plus', 'minus', $icon_class );
			}

			return $icon_class;

		}

		/**
		 * Find FAQ by hash
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function find_faq() {

			$faq_id  = isset( $_POST['faq_id'] ) ? (int) $_POST['faq_id'] : false;   //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$page_id = isset( $_POST['page_id'] ) ? (int) $_POST['page_id'] : false; //phpcs:ignore WordPress.Security.NonceVerification.Missing

			try {
				$faq_id   = str_replace( '#faq-', '', $faq_id );
				$faq      = get_post( $faq_id );
				$faq_page = 1;

				if ( $faq && YITH_FWP_FAQ_POST_TYPE === $faq->post_type ) {
					$args           = array();
					$post           = get_post( $page_id );
					$elementor_item = yfwp_get_elementor_item_for_page( $post->ID, true );
					if ( $elementor_item ) {
						$args = isset( $elementor_item['_yith_id'] ) ? yfwp_create_shortcode( $elementor_item['_yith_id'], true ) : $elementor_item;
					} else {
						if ( has_shortcode( $post->post_content, 'yith_faq_preset' ) ) {
							preg_match( '/\[yith_faq_preset.*id=.(.*).\]/', $post->post_content, $id );
							$args = yfwp_create_shortcode( $id[1], true );
						}
						if ( has_shortcode( $post->post_content, 'yith_faq' ) ) {
							preg_match( '/(\[yith_faq )(.*)(\])/', $post->post_content, $shortcode );
							$args = shortcode_parse_atts( $shortcode[2] );
						}
					}

					$defaults   = yfwp_get_shortcode_defaults( yfwp_get_shortcode_allowed_params( 'faqs' ) );
					$args       = shortcode_atts( $defaults, $args );
					$page_size  = 'on' === $args['show_pagination'] ? intval( $args['page_size'] ) : -1;
					$categories = isset( $args['categories'] ) ? $args['categories'] : array();
					$options    = array(
						'post_type'      => YITH_FWP_FAQ_POST_TYPE,
						'posts_per_page' => -1,
						'post_status'    => 'publish',
						'orderby'        => 'menu_order',
						'order'          => 'ASC',
					);

					if ( ! empty( $categories ) ) {
						//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						$options['tax_query'] = array(
							array(
								'taxonomy' => YITH_FWP_FAQ_TAXONOMY,
								'field'    => 'term_id',
								'terms'    => $categories,
							),
						);
					}

					$faqs  = new WP_Query( $options );
					$index = 1;
					$page  = 1;

					if ( $faqs->have_posts() ) {
						foreach ( $faqs->posts as $faq ) {
							if ( $faq->ID === (int) $faq_id ) {
								$faq_page = $page;
								break;
							}

							if ( $index === $page_size && -1 !== $page_size ) {
								$page++;
								$index = 1;
							} else {
								$index++;
							}
						}
					}

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
		 * @since   2.0.0
		 */
		public function gutenberg_block() {

			$blocks = array(
				'yith-faq-shortcode' => array(
					'style'          => 'yith-faq-shortcode-frontend',
					'title'          => esc_html__( 'FAQ', 'yith-faq-plugin-for-wordpress' ),
					'description'    => esc_html__( 'Add the FAQ shortcode.', 'yith-faq-plugin-for-wordpress' ),
					'shortcode_name' => 'yith_faq',
					'do_shortcode'   => true,
					'keywords'       => array(
						esc_html__( 'FAQ', 'yith-faq-plugin-for-wordpress' ),
						esc_html__( 'Frequently Asked Questions', 'yith-faq-plugin-for-wordpress' ),
					),
					'attributes'     => array(
						'search_box'       => array(
							'type'    => 'select',
							'label'   => esc_html__( 'Show search box', 'yith-faq-plugin-for-wordpress' ),
							'default' => yfwp_get_shortcode_defaults( 'search_box' ),
							'options' => array(
								'on'  => esc_html__( 'Show', 'yith-faq-plugin-for-wordpress' ),
								'off' => esc_html__( 'Hide', 'yith-faq-plugin-for-wordpress' ),
							),
						),
						'category_filters' => array(
							'type'    => 'select',
							'label'   => esc_html__( 'Show category filters', 'yith-faq-plugin-for-wordpress' ),
							'default' => yfwp_get_shortcode_defaults( 'category_filters' ),
							'options' => array(
								'on'  => esc_html__( 'Show', 'yith-faq-plugin-for-wordpress' ),
								'off' => esc_html__( 'Hide', 'yith-faq-plugin-for-wordpress' ),
							),
						),
						'style'            => array(
							'type'    => 'select',
							'label'   => esc_html__( 'Choose the style', 'yith-faq-plugin-for-wordpress' ),
							'options' => array(
								'list'      => esc_html__( 'List', 'yith-faq-plugin-for-wordpress' ),
								'accordion' => esc_html__( 'Accordion', 'yith-faq-plugin-for-wordpress' ),
								'toggle'    => esc_html__( 'Toggle', 'yith-faq-plugin-for-wordpress' ),
							),
							'default' => yfwp_get_shortcode_defaults( 'style' ),
						),
						'show_pagination'  => array(
							'label'   => esc_html__( 'Show pagination', 'yith-faq-plugin-for-wordpress' ),
							'default' => yfwp_get_shortcode_defaults( 'show_pagination' ),
							'type'    => 'select',
							'options' => array(
								'on'  => esc_html__( 'Show', 'yith-faq-plugin-for-wordpress' ),
								'off' => esc_html__( 'Hide', 'yith-faq-plugin-for-wordpress' ),
							),
						),
						'page_size'        => array(
							'type'    => 'number',
							'label'   => esc_html__( 'FAQs per page', 'yith-faq-plugin-for-wordpress' ),
							'default' => yfwp_get_shortcode_defaults( 'page_size' ),
							/**
							 * APPLY_FILTERS: yith_faq_minimum_page
							 *
							 * Set minimum number of items in a page.
							 *
							 * @param integer $value Minimum faq number.
							 *
							 * @return integer
							 */
							'min'     => apply_filters( 'yith_faq_minimum_page', 5 ),
							/**
							 * APPLY_FILTERS: yith_faq_maximum_page
							 *
							 * Set maximum number of items in a page.
							 *
							 * @param integer $value Maximum faq number.
							 *
							 * @return integer
							 */
							'max'     => apply_filters( 'yith_faq_maximum_page', 20 ),
							'deps'    => array(
								'id'    => 'show_pagination',
								'value' => 'on',
							),
						),
						'faq_to_show'      => array(
							'type'    => 'select',
							'label'   => esc_html__( 'FAQs to show', 'yith-faq-plugin-for-wordpress' ),
							'default' => yfwp_get_shortcode_defaults( 'faq_to_show' ),
							'options' => array(
								'all'       => esc_html__( 'All', 'yith-faq-plugin-for-wordpress' ),
								'selection' => esc_html__( 'Specific FAQs categories', 'yith-faq-plugin-for-wordpress' ),
							),
						),
						'categories'       => array(
							'type'     => 'select',
							'label'    => esc_html__( 'Categories to display', 'yith-faq-plugin-for-wordpress' ),
							'options'  => yfwp_get_categories(),
							'multiple' => true,
							'default'  => yfwp_get_shortcode_defaults( 'categories' ),
							'deps'     => array(
								'id'    => 'faq_to_show',
								'value' => array( 'selection' ),
							),
						),
						'expand_faq'       => array(
							'type'    => 'select',
							'label'   => esc_html__( 'Expand FAQs', 'yith-faq-plugin-for-wordpress' ),
							'default' => yfwp_get_shortcode_defaults( 'expand_faq' ),
							'options' => array(
								'all-closed' => esc_html__( 'Show all FAQs closed', 'yith-faq-plugin-for-wordpress' ),
								'all-open'   => esc_html__( 'Show all FAQs expanded', 'yith-faq-plugin-for-wordpress' ),
								'first-only' => esc_html__( 'Show first FAQ expanded', 'yith-faq-plugin-for-wordpress' ),
							),
							'deps'    => array(
								'id'    => 'style',
								'value' => array( 'accordion', 'toggle' ),
							),
						),
						'show_icon'        => array(
							'type'    => 'select',
							'label'   => esc_html__( 'Show icon', 'yith-faq-plugin-for-wordpress' ),
							'options' => array(
								'off'   => esc_html__( 'Off', 'yith-faq-plugin-for-wordpress' ),
								'left'  => esc_html__( 'Left', 'yith-faq-plugin-for-wordpress' ),
								'right' => esc_html__( 'Right', 'yith-faq-plugin-for-wordpress' ),
							),
							'default' => yfwp_get_shortcode_defaults( 'show_icon' ),
							'deps'    => array(
								'id'    => 'style',
								'value' => array( 'accordion', 'toggle' ),
							),
						),
						'icon_size'        => array(
							'type'    => 'number',
							'label'   => esc_html__( 'Icon size (px)', 'yith-faq-plugin-for-wordpress' ),
							'default' => yfwp_get_shortcode_defaults( 'icon_size' ),
							'min'     => 8,
							'max'     => 40,
							'deps'    => array(
								'id'    => 'style',
								'value' => array( 'accordion', 'toggle' ),
							),
						),
						'icon'             => array(
							'type'    => 'select',
							'label'   => esc_html__( 'Choose the icon', 'yith-faq-plugin-for-wordpress' ),
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
							'default' => yfwp_get_shortcode_defaults( 'icon' ),
							'deps'    => array(
								'id'    => 'style',
								'value' => array( 'accordion', 'toggle' ),
							),
						),
					),
				),
			);

			yith_plugin_fw_gutenberg_add_blocks( $blocks );
			yith_plugin_fw_register_elementor_widgets( $blocks, true );

		}

	}

	new YITH_FAQ_Shortcode_Base();

}
