<?php
/**
 * Photography loop addons.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $Product_Addon_Display;

?>

<div class="photography-addons">

	<?php $Product_Addon_Display->display( $post->ID ); ?>

</div>
