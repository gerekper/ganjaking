<?php
/**
 * Composite Pagination template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/composite-pagination.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div id="composite_pagination_<?php echo $product_id; ?>" class="composite_pagination"></div>
