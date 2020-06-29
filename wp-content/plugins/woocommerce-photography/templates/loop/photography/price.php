<?php
/**
 * Photography loop price.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;;

?>

<p class="price photography-price"><?php echo $product->get_price_html(); ?></p>
