<?php get_header(); ?>

<?php
global $porto_settings, $porto_layout;
$faq_infinite = $porto_settings['faq-infinite'];

$args = array(
	'post_type'   => 'faq',
	'post_status' => 'publish',
);
if ( isset( $porto_settings['faq-orderby'] ) && $porto_settings['faq-orderby'] ) {
	$args['orderby'] = $porto_settings['faq-orderby'];
}
if ( isset( $porto_settings['faq-order'] ) && $porto_settings['faq-order'] ) {
	$args['order'] = $porto_settings['faq-order'];
}
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );
$args['paged'] = $paged;
$query = new WP_Query( $args );
?>

<div id="content" role="main">

	<?php if ( ! is_search() && 'content' == $porto_settings['faq-cat-sort-pos'] && $porto_settings['faq-title'] ) : ?>
		<?php
		if ( 'widewidth' === $porto_layout ) :
			?>
			<div class="container"><?php endif; ?>
		<?php if ( $porto_settings['faq-sub-title'] ) : ?>
			<h2 class="m-b-xs"><?php echo wp_kses_post( $porto_settings['faq-title'] ); ?></h2>
			<p class="lead m-b-xl"><?php echo wp_kses_post( $porto_settings['faq-sub-title'] ); ?></p>
		<?php else : ?>
			<h2><?php echo wp_kses_post( $porto_settings['faq-title'] ); ?></h2>
		<?php endif; ?>
		<?php
		if ( 'widewidth' === $porto_layout ) :
			?>
			</div><?php endif; ?>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>

		<div class="page-faqs clearfix">

			<?php
			if ( 'hide' !== $porto_settings['faq-cat-sort-pos'] && ! is_search() ) {
				if ( 'sidebar' === $porto_settings['faq-cat-sort-pos'] && ! ( 'widewidth' == $porto_layout || 'fullwidth' == $porto_layout ) ) {
					add_action( 'porto_before_sidebar', 'porto_show_faq_archive_filter', 1 );
				} elseif ( 'content' === $porto_settings['faq-cat-sort-pos'] ) {
					$faq_taxs = array();

					$taxs = get_categories(
						array(
							'taxonomy' => 'faq_cat',
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
						<?php
						if ( 'widewidth' === $porto_layout ) :
							?>
							<div class="container"><?php endif; ?>
						<ul class="faq-filter nav nav-pills sort-source">
							<li class="active" data-filter="*"><a href="#"><?php esc_html_e( 'Show All', 'porto' ); ?></a></li>
							<?php foreach ( $faq_taxs as $faq_tax_slug => $faq_tax_name ) : ?>
								<li data-filter="<?php echo esc_attr( $faq_tax_slug ); ?>"><a href="#"><?php echo esc_html( $faq_tax_name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
						<hr>
						<?php
						if ( 'widewidth' === $porto_layout ) :
							echo '</div>';
						endif;
						?>
						<?php
					endif;
				}
			}
			?>

			<div class="faq-row faqs-container">
				<?php
				while ( $query->have_posts() ) {
					$query->the_post();

					get_template_part( 'content', 'archive-faq' );
				}
				?>
			</div>

			<?php porto_pagination( null, false, $query ); ?>

		</div>

		<?php wp_reset_postdata(); ?>

	<?php else : ?>

		<p><?php esc_html_e( 'Apologies, but no results were found for the requested archive.', 'porto' ); ?></p>

	<?php endif; ?>

</div>

<?php get_footer(); ?>
