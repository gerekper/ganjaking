<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if(!isset($post_title_tag) && empty($post_title_tag)){
	$post_title_tag='h3';
} ?>
<<?php echo theplus_validate_html_tag($post_title_tag); ?> class="post-title">
	<a href="<?php echo esc_url(get_the_permalink()); ?>" title="<?php echo esc_attr(get_the_title()); ?>">
	<?php echo wp_kses_post(get_the_title()); ?>


<?php 
	if ( class_exists( 'WC_Product_Subtitle' ) ) {
		?><br/><?php
		do_action( 'theplus_after_product_title' );
	}
?></a>
</<?php echo theplus_validate_html_tag($post_title_tag); ?>>