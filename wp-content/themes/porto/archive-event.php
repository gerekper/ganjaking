<?php get_header(); ?>

<?php
$builder_id = porto_check_builder_condition( 'archive' );
if ( $builder_id && 'publish' == get_post_status( $builder_id ) ) {
	echo do_shortcode( '[porto_block id="' . esc_attr( $builder_id ) . '"]' );
} else {
	global $porto_settings, $porto_layout;
	$event_layout = isset( $porto_settings['event-archive-layout'] ) ? $porto_settings['event-archive-layout'] : 'list';
	?>
<div id="content" role="main" class="container">

	<?php if ( ! is_search() && ! empty( $porto_settings['event-title'] ) ) : ?>
		<?php
		if ( 'widewidth' === $porto_layout ) :
			?>
			<div class="container"><?php endif; ?>
		<?php if ( ! empty( $porto_settings['event-sub-title'] ) ) : ?>
			<h2 class="m-b-xs"><?php echo wp_kses_post( $porto_settings['event-title'] ); ?></h2>
			<p class="lead m-b-xl"><?php echo wp_kses_post( $porto_settings['event-sub-title'] ); ?></p>
		<?php else : ?>
			<h2><?php echo wp_kses_post( $porto_settings['event-title'] ); ?></h2>
		<?php endif; ?>
		<?php
		if ( 'widewidth' === $porto_layout ) :
			?>
			</div><?php endif; ?>
	<?php endif; ?>
	<?php
	$args = array(
		'post_type'   => get_post_type(),
		'post_status' => 'publish',
		'meta_key'    => 'event_start_date',
		'orderby'     => 'meta_value',
	);

	$paged         = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );
	$args['paged'] = $paged;

	$event_query = new WP_Query( $args );
	?>

	<?php if ( $event_query->have_posts() ) : ?>
		<div class="page-events clearfix">
			<div class="row event-row archive-event-row">
				<?php
				$event_count = 0;
				while ( $event_query->have_posts() ) {
					$event_count++;
					$event_query->the_post();
					?>
					<div class="col-lg-6<?php echo 'grid' == $event_layout ? ' col-md-8 offset-lg-0 offset-md-2 custom-sm-margin-bottom-1 p-b-lg' : ''; ?>">
						<?php get_template_part( 'content', 'archive-event-' . $event_layout ); ?>
					</div>
					<?php
					if ( 0 === $event_count % 2 && ( $event_query->current_post + 1 ) != ( $event_query->post_count ) ) {
						echo '</div><div class="row event-row archive-event-row">';
					}
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
