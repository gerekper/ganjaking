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

<div class="yith-wcaf yith-wcaf-payments woocommerce">

	<?php
	if ( function_exists( 'wc_print_notices' ) ) {
		wc_print_notices();
	}
	?>

	<?php do_action( 'yith_wcaf_before_dashboard_section', 'payments' ); ?>

	<div class="left-column <?php echo ( ! $show_right_column ) ? 'full-width' : ''; ?>">

		<div class="filters">
			<form>
				<div class="filters-row">
					<input type="text" class="datepicker" name="from" placeholder="<?php esc_attr_e( 'From:', 'yith-woocommerce-affiliates' ); ?>" value="<?php echo esc_attr( $from ); ?>"/>
					<input type="text" class="datepicker" name="to" placeholder="<?php esc_attr_e( 'To:', 'yith-woocommerce-affiliates' ); ?>" value="<?php echo esc_attr( $to ); ?>"/>
					<label for="per_page" class="per-page">
						<?php esc_html_e( 'Items per page:', 'yith-woocommerce-affiliates' ); ?>
						<input max="100" min="1" step="1" type="number" name="per_page" value="<?php echo esc_attr( $per_page ); ?>"/>
					</label>
				</div>
				<div class="button-row">
					<input type="submit" value="<?php esc_html_e( 'Filter', 'yith-woocommerce-affiliates' ); ?>"/>
					<?php if ( $filter_set ) : ?>
						<a href="<?php echo esc_url( $dashboard_payments_link ); ?>"><?php esc_html_e( 'Reset', 'yith-woocommerce-affiliates' ); ?></a>
					<?php endif; ?>
				</div>
			</form>
		</div>

		<table class="shop_table">
			<thead>
			<tr>
				<th class="column-id">
					<a rel="nofollow" class="<?php echo ( 'id' === $ordered ) ? 'ordered to-order-' . esc_attr( strtolower( $to_order ) ) : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'ID', 'order' => $to_order ) ) ); ?>"><?php esc_html_e( 'ID', 'yith-woocommerce-affiliates' ); ?></a>
				</th>
				<th class="column-status">
					<a rel="nofollow" class="<?php echo ( 'status' === $ordered ) ? 'ordered to-order-' . esc_attr( strtolower( $to_order ) ) : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'status', 'order' => $to_order ) ) ); ?>"><?php esc_html_e( 'Status', 'yith-woocommerce-affiliates' ); ?></a>
				</th>
				<th class="column-amount">
					<a rel="nofollow" class="<?php echo ( 'amount' === $ordered ) ? 'ordered to-order-' . esc_attr( strtolower( $to_order ) ) : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'amount', 'order' => $to_order ) ) ); ?>"><?php esc_html_e( 'Amount', 'yith-woocommerce-affiliates' ); ?></a>
				</th>
				<th class="column-created_at">
					<a rel="nofollow" class="<?php echo ( 'created_at' === $ordered ) ? 'ordered to-order-' . esc_attr( strtolower( $to_order ) ) : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'created_at', 'order' => $to_order ) ) ); ?>"><?php esc_html_e( 'Created at', 'yith-woocommerce-affiliates' ); ?></a>
				</th>
				<th class="column-completed_at">
					<a rel="nofollow" class="<?php echo ( 'completed_at' === $ordered ) ? 'ordered to-order-' . esc_attr( strtolower( $to_order ) ) : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'completed_at', 'order' => $to_order ) ) ); ?>"><?php esc_html_e( 'Completed at', 'yith-woocommerce-affiliates' ); ?></a>
				</th>
				<?php if ( 'yes' === $show_invoice ) : ?>
					<th class="column-invoice">
						<?php esc_html_e( 'Invoice', 'yith-woocommerce-affiliates' ); ?>
					</th>
				<?php endif; ?>
			</tr>
			</thead>
			<tbody>
			<?php if ( ! empty( $payments ) ) : ?>
				<?php foreach ( $payments as $payment ) : ?>
					<tr>
						<td class="column-id">#<?php echo esc_html( $payment['ID'] ); ?></td>
						<td class="column-status <?php echo esc_attr( $payment['status'] ); ?>"><a rel="nofollow" href="<?php echo esc_url( add_query_arg( 'status', $payment['status'] ) ); ?>"><?php echo esc_html( YITH_WCAF_Payment_Handler()->get_readable_status( $payment['status'] ) ); ?></a></td>
						<td class="column-amount"><?php echo wc_price( $payment['amount'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
						<td class="column-create_at"><?php echo esc_html( date_i18n( wc_date_format(), strtotime( $payment['created_at'] ) ) ); ?></td>
						<td class="column-completed_at"><?php echo esc_html( date_i18n( wc_date_format(), strtotime( $payment['completed_at'] ) ) ); ?></td>
						<?php if ( 'yes' === $show_invoice ) : ?>
							<td class="column-invoice">
								<?php
									$get_invoice_url = YITH_WCAF_Payment_Handler()->get_invoice_publishable_url( $payment['ID'] );

									echo $get_invoice_url ? sprintf( '<a rel="nofollow" href="%s">%s</a>', $get_invoice_url, esc_html__( 'Download', 'yith-woocommerce-affiliates' ) ) : esc_html__( 'N/A', 'yith-woocommerce-affiliates' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td class="empty-set" colspan="6"><?php esc_html_e( 'Sorry! There are no registered payments yet', 'yith-woocommerce-affiliates' ); ?></td>
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
