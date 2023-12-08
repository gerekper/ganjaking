<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="post-meta-info style-1">
	<?php include THEPLUS_INCLUDES_URL. 'dynamic-smart-showcase/meta-date.php'; ?>
	<span>|</span> <span class="post-author"><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a> </span>
</div>

