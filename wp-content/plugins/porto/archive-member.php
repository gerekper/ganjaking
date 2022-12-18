<?php get_header(); ?>

<?php
$builder_id = porto_check_builder_condition( 'archive' );
if ( $builder_id && 'publish' == get_post_status( $builder_id ) ) {
	echo do_shortcode( '[porto_block id="' . esc_attr( $builder_id ) . '"]' );
} else {
	global $porto_settings, $porto_layout;
	$member_infinite = isset( $porto_settings['member-infinite'] ) ? $porto_settings['member-infinite'] : true;
	$member_columns  = isset( $porto_settings['member-columns'] ) ? $porto_settings['member-columns'] : 4;

	if ( ( ( isset( $porto_settings['member-cat-sort-pos'] ) && 'hide' !== $porto_settings['member-cat-sort-pos'] && ! empty( $porto_settings['member-cat-ft'] ) ) || 'ajax' == $member_infinite ) && ! wp_script_is( 'porto-infinite-scroll' ) ) {
		wp_enqueue_script( 'porto-infinite-scroll' );
	}
	?>

<div id="content" role="main">

	<?php if ( ! is_search() && isset( $porto_settings['member-cat-sort-pos'] ) && 'content' == $porto_settings['member-cat-sort-pos'] && ! empty( $porto_settings['member-title'] ) ) : ?>
		<?php
		if ( 'widewidth' === $porto_layout ) :
			?>
			<div class="container"><?php endif; ?>
		<?php if ( ! empty( $porto_settings['member-sub-title'] ) ) : ?>
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

	<?php
	if ( have_posts() ) :
		$wrap_cls   = 'page-members clearfix';
		$wrap_attrs = '';
		if ( $member_infinite ) {
			$wrap_cls   .= ' porto-ajax-load';
			$wrap_attrs .= ' data-post_type="member" data-post_layout="' . esc_attr( isset( $porto_settings['member-view-type'] ) ? $porto_settings['member-view-type'] : '' ) . '"';
			if ( 'ajax' == $member_infinite ) {
				$wrap_cls .= ' load-ajax';
			} else {
				$wrap_cls .= ' load-infinite';
			}
		}

		?>

		<div class="<?php echo esc_attr( $wrap_cls ); ?>"<?php echo porto_filter_output( $wrap_attrs ); ?>>

			<?php if ( ! empty( $porto_settings['member-archive-ajax'] ) ) : ?>
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
			if ( isset( $porto_settings['member-cat-sort-pos'] ) && 'hide' !== $porto_settings['member-cat-sort-pos'] && ! is_search() ) {
				if ( 'sidebar' === $porto_settings['member-cat-sort-pos'] && ! ( 'widewidth' == $porto_layout || 'fullwidth' == $porto_layout ) ) {
					add_action( 'porto_before_sidebar', 'porto_show_member_archive_filter', 1 );
				} elseif ( 'content' === $porto_settings['member-cat-sort-pos'] ) {
					$member_taxs = array();

					$taxs = get_categories(
						array(
							'taxonomy'   => 'member_cat',
							'hide_empty' => true,
							'orderby'    => isset( $porto_settings['member-cat-orderby'] ) ? $porto_settings['member-cat-orderby'] : 'name',
							'order'      => isset( $porto_settings['member-cat-order'] ) ? $porto_settings['member-cat-order'] : 'asc',
						)
					);

					foreach ( $taxs as $tax ) {
						$member_taxs[ urldecode( $tax->slug ) ] = $tax->name;
					}

					if ( empty( $porto_settings['member-cat-ft'] ) && 'infinite' != $member_infinite && 'load_more' != $member_infinite && '1' !== $member_infinite ) {
						global $wp_query;
						$posts_member_taxs = array();
						if ( is_array( $wp_query->posts ) && ! empty( $wp_query->posts ) ) {
							foreach ( $wp_query->posts as $post ) {
								$post_taxs = wp_get_post_terms( $post->ID, 'member_cat', array( 'fields' => 'slugs' ) );
								if ( is_array( $post_taxs ) && ! empty( $post_taxs ) ) {
									$posts_member_taxs = array_unique( array_merge( $posts_member_taxs, $post_taxs ) );
								}
							}
						}
						foreach ( $member_taxs as $key => $value ) {
							if ( ! in_array( $key, $posts_member_taxs ) ) {
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
						<ul class="member-filter nav sort-source <?php echo isset( $porto_settings['member-cat-sort-style'] ) && $porto_settings['member-cat-sort-style'] ? 'sort-source-' . esc_attr( $porto_settings['member-cat-sort-style'] ) : 'nav-pills', empty( $porto_settings['member-cat-ft'] ) || empty( $member_infinite ) ? '' : ' porto-ajax-filter'; ?>">
							<li class="active" data-filter="*"><a href="#"><?php esc_html_e( 'Show All', 'porto' ); ?></a></li>
							<?php foreach ( $member_taxs as $member_tax_slug => $member_tax_name ) : ?>
								<li data-filter="<?php echo esc_attr( $member_tax_slug ); ?>"><a href="<?php echo esc_url( get_term_link( $member_tax_slug, 'member_cat' ) ); ?>"><?php echo esc_html( $member_tax_name ); ?></a></li>
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

			if ( isset( $porto_settings['member-view-type'] ) && 'advanced' == $porto_settings['member-view-type'] ) :
				?>
				<div class="members-container member-row member-row-advanced">
				<?php
					$counter = 0;
				while ( have_posts() ) {
					the_post();
					porto_get_template_part(
						'content',
						'member',
						array(
							'member_counter' => $counter,
						)
					);
					$counter++;
				}
				?>
				</div>
			<?php else : ?>

				<div class="member-row members-container row ccols-wrap <?php echo porto_generate_column_classes( $member_columns ); ?>">
				<?php
				while ( have_posts() ) {
					the_post();

					get_template_part( 'content', 'archive-member' );
				}
				?>
				</div>
			<?php endif; ?>

			<?php porto_pagination(); ?>

		</div>

		<?php wp_reset_postdata(); ?>

	<?php else : ?>

		<p><?php esc_html_e( 'Apologies, but no results were found for the requested archive.', 'porto' ); ?></p>

	<?php endif; ?>

</div>

<?php } ?>
<?php get_footer(); ?>
