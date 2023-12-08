<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly	
	if($display_post_meta_date=='yes' || $display_post_meta_author=='yes'){
		echo '<div class="post-meta-info style-2">';
	}
	if($display_post_meta_author=='yes'){
	?>
	<span class="post-author"><?php echo esc_attr($author_prefix); ?> <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a> </span>
	<?php }
	
	if($display_post_meta_date=='yes' && $display_post_meta_author=='yes'){
		echo '<span>/</span>';
	}
	
	if($display_post_meta_date=='yes'){
		include THEPLUS_INCLUDES_URL. 'blog/meta-date.php'; 
	}
	
	if($display_post_meta_date=='yes' || $display_post_meta_author=='yes'){
		echo '</div>';
	}

