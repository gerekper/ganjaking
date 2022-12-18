<?php get_header(); ?>

<?php
$builder_id = porto_check_builder_condition( 'archive' );
if ( $builder_id && 'publish' == get_post_status( $builder_id ) ) {
	echo do_shortcode( '[porto_block id="' . esc_attr( $builder_id ) . '"]' );
} else {
	global $porto_settings, $wp_query;

	$term    = $wp_query->queried_object;
	$term_id = $term->term_id;

	$faq_infinite = $porto_settings['faq-infinite'];

	?>

<div id="content" role="main">

	<?php if ( category_description() ) : ?>
		<div class="page-content">
			<?php echo category_description(); ?>
		</div>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>

		<div class="page-faqs clearfix">

			<?php
			if ( 'hide' !== $porto_settings['faq-cat-sort-pos'] ) {
				if ( 'sidebar' === $porto_settings['faq-cat-sort-pos'] && ! ( 'widewidth' == $porto_layout || 'fullwidth' == $porto_layout ) ) {
					add_action( 'porto_before_sidebar', 'porto_show_faq_tax_filter', 1 );
				} elseif ( 'content' === $porto_settings['faq-cat-sort-pos'] ) {
					$faq_taxs = array();

					$taxs = get_categories(
						array(
							'taxonomy' => 'faq_cat',
							'child_of' => $term_id,
							'orderby'  => isset( $porto_settings['faq-cat-orderby'] ) ? $porto_settings['faq-cat-orderby'] : 'name',
							'order'    => isset( $porto_settings['faq-cat-order'] ) ? $porto_settings['faq-cat-order'] : 'asc',
						)
					);

					foreach ( $taxs as $tax ) {
						$faq_taxs[ urldecode( $tax->slug ) ] = $tax->name;
					}

					if ( ! $faq_infinite ) {
						global $wp_query;
						$posts_faq_taxs = array();
						if ( is_array( $wp_query->posts ) && ! empty( $wp_query->posts ) ) {
							foreach ( $wp_query->posts as $post ) {
								$post_taxs = wp_get_post_terms( $post->ID, 'faq_cat', array( 'fields' => 'all' ) );
								if ( is_array( $post_taxs ) && ! empty( $post_taxs ) ) {
									foreach ( $post_taxs as $post_tax ) {
										$posts_faq_taxs[ urldecode( $post_tax->slug ) ] = $post_tax->name;
									}
								}
							}
						}
						foreach ( $faq_taxs as $key => $value ) {
							if ( ! isset( $posts_faq_taxs[ $key ] ) ) {
								unset( $faq_taxs[ $key ] );
							}
						}
					}

					// Show Filters
					if ( is_array( $faq_taxs ) && ! empty( $faq_taxs ) ) :
						?>
						<ul class="faq-filter nav nav-pills sort-source">
							<li class="active" data-filter="*"><a href="#"><?php esc_html_e( 'Show All', 'porto' ); ?></a></li>
							<?php foreach ( $faq_taxs as $faq_tax_slug => $faq_tax_name ) : ?>
								<li data-filter="<?php echo esc_attr( $faq_tax_slug ); ?>"><a href="#"><?php echo esc_html( $faq_tax_name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
						<hr>
						<?php
					endif;
				}
			}
			?>

			<div class="faq-row<?php echo ! $faq_infinite ? '' : ' faqs-container'; ?>">
				<?php
				while ( have_posts() ) {
					the_post();

					get_template_part( 'content', 'archive-faq' );
				}
				?>
			</div>

			<?php porto_pagination(); ?>

		</div>

		<?php wp_reset_postdata(); ?>

	<?php else : ?>

		<p><?php esc_html_e( 'Apologies, but no results were found for the requested archive.', 'porto' ); ?></p>

	<?php endif; ?>

</div>
	<?php } ?>
<?php get_footer(); ?>
