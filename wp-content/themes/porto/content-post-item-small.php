<?php
global $porto_settings;

$featured_images = porto_get_featured_images();
$show_date       = in_array( 'date', $porto_settings['post-metas'] );
$tag             = isset( $title_tag ) ? $title_tag : 'h5';
?>
<div class="<?php echo isset( $post_item_class ) ? esc_attr( $post_item_class ) : 'post-item-small'; ?>">
	<?php
	if ( count( $featured_images ) ) :
		$attachment_id    = $featured_images[0]['attachment_id'];
		$attachment_thumb = porto_get_attachment( $attachment_id, isset( $image_size ) ? $image_size : 'widget-thumb-medium' );
		?>
		<div class="post-image img-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<img width="<?php echo esc_attr( $attachment_thumb['width'] ); ?>" height="<?php echo esc_attr( $attachment_thumb['height'] ); ?>" src="<?php echo esc_url( $attachment_thumb['src'] ); ?>" alt="<?php echo esc_attr( $attachment_thumb['alt'] ); ?>" />
			</a>
		</div>
	<?php endif; ?>
	<div class="post-item-content">
		<?php if ( ! isset( $show_cats ) && $show_date && isset( $meta_first ) ) : ?>
			<span class="post-date"><?php echo get_the_date(); ?></span>
		<?php endif; ?>
		<?php
		if ( isset( $show_cats ) ) {
			echo '<span class="meta-cats">' . get_the_category_list( ' ' ) . '</span>';
		}
		?>
		<<?php echo esc_html( $tag ); ?> class="post-item-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></<?php echo esc_html( $tag ); ?>>
		<?php if ( ! isset( $show_cats ) && $show_date && ! isset( $meta_first ) ) : ?>
			<span class="post-date"><?php echo get_the_date(); ?></span>
		<?php endif; ?>
	</div>
</div>
