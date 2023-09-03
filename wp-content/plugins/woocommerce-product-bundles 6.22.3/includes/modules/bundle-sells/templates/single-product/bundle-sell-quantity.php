<?php
/**
 * Bundled Product Quantity template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/bundle-sell-quantity.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version  6.21.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<input class="bundled_qty" type="hidden" name="<?php echo esc_attr( $input_name ); ?>" value="1" />
