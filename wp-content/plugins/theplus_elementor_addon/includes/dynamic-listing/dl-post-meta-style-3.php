<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly	
	if($display_post_meta_date=='yes' || $display_post_meta_author=='yes' || $display_post_meta_author_pic=='yes'){
		echo '<div class="post-meta-info style-3">';
		echo '<div class="post-author">';
	}
	
		if($display_post_meta_author_pic=='yes'){ ?>	
			<a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" rel="<?php echo esc_attr__('author','theplus'); ?>" class="author-meta"><span class="author-gravatar-details"><?php global $user;  echo get_avatar( get_the_author_meta('ID'), 45); ?></span></a> <?php
		}	
		if($display_post_meta_date=='yes' || $display_post_meta_author=='yes'){
			echo '<div class="author-date">';
		}		
			if($display_post_meta_author=='yes'){
			?>
				<a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" rel="<?php echo esc_attr__('author','theplus'); ?>" class="author-meta"><span class="author-title"><?php the_author_meta('display_name'); ?></span></a>
			<?php } 
			if($display_post_meta_date=='yes'){
				include THEPLUS_INCLUDES_URL. 'dynamic-listing/meta-date.php';
			}
			
		if($display_post_meta_date=='yes' || $display_post_meta_author=='yes'){
			echo '</div>';
		}
		
	if($display_post_meta_date=='yes' || $display_post_meta_author=='yes' || $display_post_meta_author_pic=='yes'){ 
		echo '</div>';
		echo '</div>';
	}

