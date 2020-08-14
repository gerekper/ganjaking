<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( empty( $args['items'] ) ) {
	return;
}

foreach ( $args['items'] as $id => $item ) {

	if ( isset( $args['active_var'] ) && $args['active_var'] != $id && isset( $args['shortcode'] ) && $args['shortcode'] == true ) {
		continue;
	}

	$style      = ( isset( $args['active_var'] ) && $args['active_var'] != $id ) ? ' style=" display: none;"' : '';
	$header_css = ( $item['expired'] == 'expired' ) ? ' style="display: none;"' : '';

	if ( $item['show_bar'] == 'show' ) {

		?>

		<div class="ywpc-sale-bar ywpc-item-<?php echo $id; ?>"<?php echo $style; ?>>
			<div class="ywpc-header"<?php echo $header_css ?>>
				<?php echo get_option( 'ywpc_sale_bar_title', esc_html__( 'On sale', 'yith-woocommerce-product-countdown' ) ); ?>
			</div>
			<div class="ywpc-bar">
				<div class="ywpc-back">
					<div class="ywpc-fore" style="width: <?php echo $item['percent']; ?>%">
					</div>
				</div>
				<div class="ywpc-label">
					<?php printf( esc_html__( '%d/%d Sold', 'yith-woocommerce-product-countdown' ), ( ( ! is_rtl() ) ? $item['sold_qty'] : $item['discount_qty'] ), ( ( ! is_rtl() ) ? $item['discount_qty'] : $item['sold_qty'] ) ); ?>
				</div>
			</div>
		</div>
		<?php
	}
}
