<?php
/**
 * Bundled Item Short Description template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/bundled-item-description.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 4.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $description === '' ){
	return;
}

?><div class="bundled_product_excerpt product_excerpt"><?php
		echo $description;
?></div>
