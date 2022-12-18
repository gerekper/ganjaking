<?php
global $porto_settings;

$show_date = isset( $porto_settings['post-metas'] ) && in_array( 'date', $porto_settings['post-metas'] );
?>
<div class="post-item-small">
	<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	<?php
	if ( $show_date ) :
		?>
		<span class="post-date"><?php echo get_the_date(); ?></span><?php endif; ?>
</div>
