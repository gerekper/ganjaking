<?php
/**
 * Bundled Item Price template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/bundled-item-price.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 5.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $bundled_item->is_priced_individually() ) {
	?><span class="price"><?php echo $bundled_item->product->get_price_html(); ?></span><?php
}
