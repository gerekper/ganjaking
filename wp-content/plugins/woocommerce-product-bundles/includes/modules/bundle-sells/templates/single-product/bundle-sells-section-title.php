<?php
/**
 * Bundle-Sells Section Title template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/bundle-sells-section-title.php'.
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

<div class="bundle_sells_title">
	<?php echo ( $wrap ? '<h3>' : '' ) . wp_kses_post( $title ) . ( $wrap ? '</h3>' : '' ); ?>
</div>
