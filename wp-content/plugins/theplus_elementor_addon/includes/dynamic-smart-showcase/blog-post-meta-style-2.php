<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="post-meta-info style-2">	
	<span class="post-author"><?php echo esc_html__('By ', 'theplus'); ?> <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a> </span><span>/</span>
	<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/meta-date.php'; ?>
</div>

