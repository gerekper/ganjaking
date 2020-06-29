<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$vendor_description = do_shortcode( $vendor_description );
$vendor_description = call_user_func( '__', $vendor_description, 'yith-woocommerce-product-vendors' );

$vendor_name = call_user_func( '__', $vendor_name, 'yith-woocommerce-product-vendors' );
?>

<h2>
    <a href="<?php echo $vendor_url ?>">
        <?php echo $vendor_name; ?>
    </a>
</h2>

<div class="vendor-description">
    <?php echo wpautop( $vendor_description );  ?>
</div>