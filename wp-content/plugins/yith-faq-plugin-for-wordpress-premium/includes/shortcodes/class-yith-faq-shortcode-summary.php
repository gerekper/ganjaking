<?php
/**
 * Summary Shortcode class
 *
 * @package YITH\FAQPluginForWordPress\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_FAQ_Shortcode_Summary' ) ) {

	/**
	 * Implements shortcode for FAQ plugin
	 *
	 * @class   YITH_FAQ_Shortcode_Summary
	 * @since   2.0.0
	 * @author  YITH <plugins@yithemes.com>
	 * @package YITH\FAQPluginForWordPress\Shortcodes
	 */
	class YITH_FAQ_Shortcode_Summary extends YITH_FAQ_Shortcode {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function __construct() {

			parent::__construct();

			add_action( 'init', array( $this, 'gutenberg_block' ) );
			add_shortcode( 'yith_faq_summary', array( $this, 'print_summary_shortcode' ) );

		}

		/**
		 * Output summary shortcode
		 *
		 * @param array $args Shortcode arguments.
		 *
		 * @return  string
		 * @since   2.0.0
		 */
		public function print_summary_shortcode( $args ) {

			$defaults   = yfwp_get_shortcode_defaults( yfwp_get_shortcode_allowed_params( 'summary' ) );
			$args       = shortcode_atts( $defaults, $args );
			$categories = $args['categories'];
			$permalink  = ( '' !== $args['page_id'] ) ? get_permalink( $args['page_id'] ) : get_permalink();
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

			$faqs = new WP_Query( $options );

			ob_start();

			?>
			<div class="yith-faqs-summary" data-page_id="<?php echo esc_attr( $args['page_id'] ); ?>">
				<?php if ( ! $faqs->have_posts() ) : ?>
					<div class="yith-faqs-no-results">
						<?php echo esc_html_x( 'Sorry, there are no matching results for your search.', '[Frontend] No results message', 'yith-faq-plugin-for-wordpress' ); ?>
					</div>
				<?php endif; ?>
				<?php
				if ( '' !== $args['title'] ) :
					$tag_type = $args['title_type'];
					$title    = $args['title'];
					echo wp_kses_post( "<$tag_type>$title</$tag_type>" );
				endif;
				?>
				<ul id="yith-faqs-summary-container" class="yith-faqs-summary-container">
					<?php while ( $faqs->have_posts() ) : ?>
						<?php $faqs->the_post(); ?>
						<li id="faq-summary-<?php echo get_the_ID(); ?>" class="yith-faqs-summary-item">
							<a class="yith-faqs-summary-link" href="#" data-href="<?php echo esc_url( $permalink ); ?>#faq-<?php echo get_the_ID(); ?>" data-font="yfwp" data-icon="&#xe80d" data-faq_id="#faq-<?php echo get_the_ID(); ?>"><?php the_title(); ?></a>
						</li>
					<?php endwhile; ?>
				</ul>
			</div>
			<?php

			$output = ob_get_clean();

			wp_reset_postdata();

			return $output;

		}

		/**
		 * Set shortcode
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function gutenberg_block() {

			$blocks = array(
				'yith-faq-summary-shortcode' => array(
					'style'          => 'yith-faq-shortcode-frontend',
					'title'          => esc_html__( 'FAQ Summary', 'yith-faq-plugin-for-wordpress' ),
					'description'    => esc_html__( 'Add the FAQ Summary shortcode.', 'yith-faq-plugin-for-wordpress' ),
					'shortcode_name' => 'yith_faq_summary',
					'do_shortcode'   => true,
					'keywords'       => array(
						esc_html__( 'FAQ', 'yith-faq-plugin-for-wordpress' ),
						esc_html__( 'Frequently Asked Questions', 'yith-faq-plugin-for-wordpress' ),
					),
					'attributes'     => array(
						'title'       => array(
							'type'    => 'text',
							'label'   => esc_html__( 'Block title', 'yith-faq-plugin-for-wordpress' ),
							'default' => yfwp_get_shortcode_defaults( 'title' ),
						),
						'title_type'  => array(
							'type'     => 'select',
							'label'    => esc_html__( 'Title type', 'yith-faq-plugin-for-wordpress' ),
							'options'  => array(
								'h1' => 'h1',
								'h2' => 'h2',
								'h3' => 'h3',
								'h4' => 'h4',
								'h5' => 'h5',
								'h6' => 'h6',
							),
							'multiple' => false,
							'default'  => yfwp_get_shortcode_defaults( 'title_type' ),
						),
						'faq_to_show' => array(
							'type'    => 'select',
							'label'   => esc_html__( 'FAQs to show', 'yith-faq-plugin-for-wordpress' ),
							'default' => yfwp_get_shortcode_defaults( 'faq_to_show' ),
							'options' => array(
								'all'       => esc_html__( 'All', 'yith-faq-plugin-for-wordpress' ),
								'selection' => esc_html__( 'Specific FAQs categories', 'yith-faq-plugin-for-wordpress' ),
							),
						),
						'categories'  => array(
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
						'page_id'     => array(
							'type'     => 'select',
							'label'    => esc_html__( 'FAQ page', 'yith-faq-plugin-for-wordpress' ),
							'options'  => yfwp_get_pages(),
							'multiple' => false,
							'default'  => yfwp_get_shortcode_defaults( 'page_id' ),
						),
					),
				),
			);

			yith_plugin_fw_gutenberg_add_blocks( $blocks );
			yith_plugin_fw_register_elementor_widgets( $blocks, true );

		}

	}

	new YITH_FAQ_Shortcode_Summary();

}
