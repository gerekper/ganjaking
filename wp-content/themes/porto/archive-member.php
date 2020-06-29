<?php get_header(); ?>

<?php
	global $porto_settings, $porto_layout;
	$member_infinite = $porto_settings['member-infinite'];
	$member_columns  = $porto_settings['member-columns'];
?>

<div id="content" role="main">

	<?php if ( ! is_search() && 'content' == $porto_settings['member-cat-sort-pos'] && $porto_settings['member-title'] ) : ?>
		<?php
		if ( 'widewidth' === $porto_layout ) :
			?>
			<div class="container"><?php endif; ?>
		<?php if ( $porto_settings['member-sub-title'] ) : ?>
			<h2 class="m-b-xs"><?php echo wp_kses_post( $porto_settings['member-title'] ); ?></h2>
			<p class="lead m-b-xl"><?php echo wp_kses_post( $porto_settings['member-sub-title'] ); ?></p>
		<?php else : ?>
			<h2><?php echo wp_kses_post( $porto_settings['member-title'] ); ?></h2>
		<?php endif; ?>
		<?php
		if ( 'widewidth' === $porto_layout ) :
			?>
			</div><?php endif; ?>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>

		<div class="page-members clearfix">

			<?php if ( $porto_settings['member-archive-ajax'] ) : ?>
				<div id="memberAjaxBox" class="ajax-box">
					<div class="bounce-loader">
						<div class="bounce1"></div>
						<div class="bounce2"></div>
						<div class="bounce3"></div>
					</div>
					<div class="ajax-box-content" id="memberAjaxBoxContent"></div>
					<?php if ( function_exists( 'porto_title_archive_name' ) && porto_title_archive_name( 'member' ) ) : ?>
						<?php /* translators: %s: Portfolio archive title */ ?>
						<div class="hide ajax-content-append"><h4 class="m-t-sm m-b-lg"><?php printf( esc_html__( 'More %s:', 'porto' ), porto_title_archive_name( 'member' ) ); ?></h4></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php
			if ( 'hide' !== $porto_settings['member-cat-sort-pos'] && ! is_search() ) {
				if ( 'sidebar' === $porto_settings['member-cat-sort-pos'] && ! ( 'widewidth' == $porto_layout || 'fullwidth' == $porto_layout ) ) {
					add_action( 'porto_before_sidebar', 'porto_show_member_archive_filter', 1 );
				} elseif ( 'content' === $porto_settings['member-cat-sort-pos'] ) {
					$member_taxs = array();

					$taxs = get_categories(
						array(
							'taxonomy' => 'member_cat',
							'orderby'  => isset( $porto_settings['member-cat-orderby'] ) ? $porto_settings['member-cat-orderby'] : 'name',
							'order'    => isset( $porto_settings['member-cat-order'] ) ? $porto_settings['member-cat-order'] : 'asc',
						)
					);

					foreach ( $taxs as $tax ) {
						$member_taxs[ urldecode( $tax->slug ) ] = $tax->name;
					}

					if ( ! $member_infinite ) {
						global $wp_query;
						$posts_member_taxs = array();
						if ( is_array( $wp_query->posts ) && ! empty( $wp_query->posts ) ) {
							foreach ( $wp_query->posts as $post ) {
								$post_taxs = wp_get_post_terms( $post->ID, 'member_cat', array( 'fields' => 'all' ) );
								if ( is_array( $post_taxs ) && ! empty( $post_taxs ) ) {
									foreach ( $post_taxs as $post_tax ) {
										$posts_member_taxs[ urldecode( $post_tax->slug ) ] = $post_tax->name;
									}
								}
							}
						}
						foreach ( $member_taxs as $key => $value ) {
							if ( ! isset( $posts_member_taxs[ $key ] ) ) {
								unset( $member_taxs[ $key ] );
							}
						}
					}

					// Show Filters
					if ( is_array( $member_taxs ) && ! empty( $member_taxs ) ) :
						?>
						<?php
						if ( 'widewidth' === $porto_layout ) :
							?>
							<div class="container"><?php endif; ?>
						<ul class="member-filter nav nav-pills sort-source">
							<li class="active" data-filter="*"><a href="#"><?php esc_html_e( 'Show All', 'porto' ); ?></a></li>
							<?php foreach ( $member_taxs as $member_tax_slug => $member_tax_name ) : ?>
								<li data-filter="<?php echo esc_attr( $member_tax_slug ); ?>"><a href="#"><?php echo esc_html( $member_tax_name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
						<hr>
						<?php
						if ( 'widewidth' === $porto_layout ) :
							?>
							</div><?php endif; ?>
						<?php
					endif;
				}
			}
			?>

			<div class="member-row members-container row <?php echo porto_generate_column_classes( $member_columns ); ?>">
			<?php
			while ( have_posts() ) {
				the_post();

				get_template_part( 'content', 'archive-member' );
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

<?php get_footer(); ?>
