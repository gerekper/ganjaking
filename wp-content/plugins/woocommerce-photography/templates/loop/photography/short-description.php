<?php
/**
 * Photography loop short description.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

if ( ! $post->post_excerpt ) {
	return;
}

?>
<p class="photography-description">
	<?php echo apply_filters( 'wc_photography_loop_short_description', $post->post_excerpt ); ?>
</p>
