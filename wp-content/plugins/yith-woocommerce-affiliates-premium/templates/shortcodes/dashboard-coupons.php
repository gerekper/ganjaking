<?php
/**
 * Affiliate Dashboard
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.5
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly
?>

<div class="yith-wcaf yith-wcaf-coupons woocommerce">

	<?php
	if ( function_exists( 'wc_print_notices' ) ) {
		wc_print_notices();
	}
	?>

	<?php do_action( 'yith_wcaf_before_dashboard_section', 'coupons' ); ?>

	<div class="left-column <?php echo ( ! $show_right_column ) ? 'full-width' : ''; ?>">

		<table class="shop_table">
			<thead>
			<tr>
				<th class="coupon-code">
					<?php esc_html_e( 'Code', 'yith-woocommerce-affiliates' ); ?>
				</th>
				<th class="coupon-type">
					<?php esc_html_e( 'Type', 'yith-woocommerce-affiliates' ); ?>
				</th>
				<th class="coupon-amount">
					<?php esc_html_e( 'Amount', 'yith-woocommerce-affiliates' ); ?>
				</th>
				<th class="coupon-expire">
					<?php esc_html_e( 'Expire', 'yith-woocommerce-affiliates' ); ?>
				</th>
				<th class="coupon-info">
					<?php esc_html_e( 'Info', 'yith-woocommerce-affiliates' ); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php if ( ! empty( $coupons ) ) : ?>
				<?php foreach ( $coupons as $coupon_id => $coupon ) : ?>

					<tr>
						<td class="coupon-code">
							<b><?php echo esc_html( $coupon['code'] ); ?></b>
						</td>
						<td class="coupon-type">
							<?php echo esc_html( $coupon['type'] ); ?>
						</td>
						<td class="coupon-amount">
							<?php echo esc_html( $coupon['amount'] ); ?>
						</td>
						<td class="coupon-expire">
							<?php echo esc_html( $coupon['expire'] ); ?>
						</td>
						<td class="coupon-info">
							<a href="#" data-tip="<?php echo esc_attr( $coupon['info'] ); ?>" class="help_tip">?</a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td class="empty-set" colspan="6"><?php esc_html_e( 'Sorry! There are no registered coupons yet', 'yith-woocommerce-affiliates' ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>

		<?php if ( ! empty( $page_links ) ) : ?>
			<nav class="woocommerce-pagination">
				<?php echo $page_links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</nav>
		<?php endif; ?>

	</div>

	<!--NAVIGATION MENU-->
	<?php
	$atts = array(
		'show_right_column'    => $show_right_column,
		'show_left_column'     => true,
		'show_dashboard_links' => $show_dashboard_links,
		'dashboard_links'      => $dashboard_links,
	);
	yith_wcaf_get_template( 'navigation-menu.php', $atts, 'shortcodes' );
	?>

	<?php do_action( 'yith_wcaf_after_dashboard_section', 'payments' ); ?>
</div>
