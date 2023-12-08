<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="post-meta-info style-3">	
	<div class="post-author">
		<a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" rel="<?php echo esc_attr__('author','theplus'); ?>" class="author-meta"><span class="author-gravatar-details"><?php global $user; echo get_avatar( get_the_author_meta('email'), '45',false ,get_the_author_meta('display_name', $user['ID'])); ?></span></a>
		<div class="author-date">
			<a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" rel="<?php echo esc_attr__('author','theplus'); ?>" class="author-meta"><span class="author-title"><?php the_author_meta('display_name'); ?></span></a>
			<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/meta-date.php'; ?>
		</div>
	</div>
</div>

