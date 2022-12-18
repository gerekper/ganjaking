<?php
global $porto_settings, $post;

$archive_image = (int) get_post_meta( $post->ID, 'portfolio_archive_image', true );
if ( $archive_image ) {
	$featured_images   = array();
	$featured_image    = array(
		'thumb'         => wp_get_attachment_thumb_url( $archive_image ),
		'full'          => wp_get_attachment_url( $archive_image ),
		'attachment_id' => $archive_image,
	);
	$featured_images[] = $featured_image;
} else {
	$featured_images = porto_get_featured_images();
}
$portfolio_link     = get_post_meta( $post->ID, 'portfolio_link', true );
$show_external_link = isset( $porto_settings['portfolio-external-link'] ) ? $porto_settings['portfolio-external-link'] : false;

if ( count( $featured_images ) ) :
	$attachment_id    = $featured_images[0]['attachment_id'];
	$attachment_thumb = porto_get_attachment( $attachment_id, 'widget-thumb-medium' );
	?>
	<div class="portfolio-item-small">
		<div class="portfolio-image img-thumbnail">
			<a aria-label="portfolio" href="<?php echo ! $show_external_link || ! $portfolio_link ? esc_url( get_the_permalink() ) : esc_url( $portfolio_link ); ?>">
				<img width="<?php echo esc_attr( $attachment_thumb['width'] ); ?>" height="<?php echo esc_attr( $attachment_thumb['height'] ); ?>" src="<?php echo esc_url( $attachment_thumb['src'] ); ?>" alt="<?php echo esc_attr( $attachment_thumb['alt'] ); ?>" />
			</a>
		</div>
	</div>
	<?php
endif;
