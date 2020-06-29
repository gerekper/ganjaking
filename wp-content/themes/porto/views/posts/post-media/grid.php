<?php
global $porto_settings;

$featured_images = porto_get_featured_images();
$image_count     = count( $featured_images );
if ( ! $image_count ) {
	return;
}
?>
	<div class="post-image mb-4<?php echo 1 == $image_count ? ' single' : ''; ?>">
		<div class="row mx-0 lightbox" data-plugin-options='<?php
		echo json_encode(
			array(
				'delegate'  => 'a',
				'type'      => 'image',
				'gallery'   => array( 'enabled' => true ),
				'mainClass' => 'mfp-with-zoom',
				'zoom'      => array(
					'enabled'  => true,
					'duration' => 300,
				),
			)
		);
		?>'>
			<?php
			foreach ( $featured_images as $featured_image ) :
				$attachment_medium = porto_get_attachment( $featured_image['attachment_id'], isset( $porto_settings['enable-portfolio'] ) && $porto_settings['enable-portfolio'] ? 'portfolio-grid' : 'blog-medium' );
				$attachment        = porto_get_attachment( $featured_image['attachment_id'] );
				?>
				<div class="col-6 col-md-4 p-0">
					<a href="<?php echo esc_url( $attachment['src'] ); ?>">
						<span class="thumb-info thumb-info-no-borders thumb-info-centered-icons">
							<span class="thumb-info-wrapper">
								<img class="img-responsive" width="<?php echo esc_attr( $attachment_medium['width'] ); ?>" height="<?php echo esc_attr( $attachment_medium['height'] ); ?>" src="<?php echo esc_url( $attachment_medium['src'] ); ?>" alt="<?php echo esc_attr( $attachment_medium['alt'] ); ?>" />
								<span class="thumb-info-action">
									<span class="thumb-info-action-icon thumb-info-action-icon-light" data-src="<?php echo esc_url( $attachment['src'] ); ?>" data-title="<?php echo esc_attr( $attachment['caption'] ); ?>"><i class="fas fa-plus text-dark"></i></span>
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

		<?php if ( is_single() && 'advance' === $porto_settings['post-share-position'] ) : ?>
			<?php get_template_part( 'views/posts/single/share' ); ?>
		<?php endif; ?>
	</div>
