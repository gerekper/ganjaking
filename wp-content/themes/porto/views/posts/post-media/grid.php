<?php
global $porto_settings;

$featured_images = porto_get_featured_images();
$image_count     = count( $featured_images );
if ( ! $image_count ) {
	return;
}

$column_class  = 'col-6 col-md-4';
$wrapper_class = '';
if ( ! empty( $grid_column ) ) {
	$column_class = '';
	if ( '1' == $grid_column ) {
		$column_class = 'col-12';
	} else {
		$columns = porto_generate_column_classes( 12 / (int) $grid_column, true );
		foreach ( $columns as $key => $column ) {
			if ( 'xs' == $key ) {
				$column_class .= ' col-' . $column;
			} else {
				$column_class .= ' col-' . $key . '-' . $column;
			}
		}
	}
	$column_class = trim( $column_class );
}
$icon_cls = ! empty( $icon_cl ) ? $icon_cl : 'fas fa-plus';

$wrapper_option = array(
	'delegate'  => 'a',
	'type'      => 'image',
	'gallery'   => array( 'enabled' => true ),
	'mainClass' => 'mfp-with-zoom',
	'zoom'      => array(
		'enabled'  => true,
		'duration' => 300,
	),
);
if ( ! empty( $masonry ) ) {
	wp_enqueue_script( 'isotope' );
	$column_class   = 'masonry-item';
	$wrapper_class  = 'masonry';
	$wrapper_option = array_merge(
		$wrapper_option,
		array( 'itemSelector' => '.masonry-item' )
	);
}
?>
	<div class="post-image mb-4 <?php echo 1 == $image_count ? ' single' : ''; ?>">
		<div class="row mx-0 lightbox <?php echo esc_attr( $wrapper_class ); ?>" <?php echo esc_attr( ! empty( $masonry ) ? 'data-plugin-masonry' : '' ); ?>
		data-plugin-options='<?php echo json_encode( $wrapper_option ); ?>'>
			<?php
			foreach ( $featured_images as $featured_image ) :
				$attachment_medium = porto_get_attachment( $featured_image['attachment_id'], isset( $porto_settings['enable-portfolio'] ) && $porto_settings['enable-portfolio'] ? 'portfolio-grid' : 'blog-medium' );
				$attachment        = porto_get_attachment( $featured_image['attachment_id'] );
				?>
				<div class="<?php echo esc_attr( $column_class ); ?> p-0">
					<a href="<?php echo esc_url( $attachment['src'] ); ?>">
						<span class="thumb-info thumb-info-no-borders thumb-info-centered-icons">
							<span class="thumb-info-wrapper">
								<img class="img-responsive" width="<?php echo esc_attr( $attachment_medium['width'] ); ?>" height="<?php echo esc_attr( $attachment_medium['height'] ); ?>" src="<?php echo esc_url( $attachment_medium['src'] ); ?>" alt="<?php echo esc_attr( $attachment_medium['alt'] ); ?>" />
								<span class="thumb-info-action">
									<span class="thumb-info-action-icon thumb-info-action-icon-light" data-src="<?php echo esc_url( $attachment['src'] ); ?>" data-title="<?php echo esc_attr( $attachment['caption'] ); ?>"><i class="<?php echo esc_attr( $icon_cls ); ?> text-dark"></i></span>
								</span>
							</span>
						</span>
					</a>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( isset( $extra_html ) ) : ?>
			<?php // @codingStandardsIgnoreLine ?>
			<?php echo porto_filter_output( $extra_html ); ?>
		<?php endif; ?>

		<?php if ( is_single() && isset( $porto_settings['post-share-position'] ) && 'advance' === $porto_settings['post-share-position'] ) : ?>
			<?php get_template_part( 'views/posts/single/share' ); ?>
		<?php elseif ( ! is_single() && isset( $porto_settings['blog-post-share-position'] ) && 'advance' === $porto_settings['blog-post-share-position'] ) : ?>
			<div class="post-block post-share post-share-advance">
				<div class="post-share-advance-bg">
					<?php get_template_part( 'share' ); ?>
					<i class="fa fa-share-alt"></i>
				</div>
			</div>
		<?php endif; ?>
	</div>
