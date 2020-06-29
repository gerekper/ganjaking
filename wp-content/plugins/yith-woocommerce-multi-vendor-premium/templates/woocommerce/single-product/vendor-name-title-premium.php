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
?>

<span class="by-vendor-name">
    <a class="by-vendor-name-link" href="<?php echo $vendor->get_url() ?>">
        <?php echo apply_filters( 'yith_frontend_vendor_name_prefix', __( 'by', 'yith-woocommerce-product-vendors' ) ) . ' ' . $vendor->name ?>
    </a>
</span>