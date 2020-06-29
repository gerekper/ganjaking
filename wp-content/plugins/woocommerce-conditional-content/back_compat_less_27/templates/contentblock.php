<?php
/**
 * The main template for displaying conditional content blocks. 
 */
?>

<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  ?>

<div class="wccc-content-block" id="wccc-content-block-<?php echo $content->ID; ?>">
	<?php if ( apply_filters( 'woocommerce_conditional_content_apply_the_content_filter', true, $content ) ) : ?>
		<?php echo apply_filters( 'woocommerce_conditional_content_the_content', apply_filters( 'the_content', $content->post_content ) ); ?>
	<?php else : ?>
		<?php echo apply_filters( 'woocommerce_conditional_content_the_content', $content->post_content ); ?>
	<?php endif; ?>
</div>