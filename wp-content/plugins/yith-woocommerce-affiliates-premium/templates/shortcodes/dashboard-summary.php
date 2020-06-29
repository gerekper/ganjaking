<?php
/**
 * Affiliate Dashboard Summary
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

<div class="yith-wcaf yith-wcaf-dashboard-summary woocommerce">

	<?php
	if ( function_exists( 'wc_print_notices' ) ) {
		wc_print_notices();
	}
	?>

	<?php do_action( 'yith_wcaf_before_dashboard_summary' ); ?>

	<p class="myaccount_user">
		<?php echo $greeting_message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</p>

	<div class="dashboard-content">
		<?php if ( $show_left_column ) : ?>
			<div class="left-column <?php echo ( ! $show_right_column ) ? 'full-width' : ''; ?>">
				<!--AFFILIATE STATS-->
				<?php if ( $show_referral_stats ) : ?>
					<div class="dashboard-title">
						<h2><?php esc_html_e( 'Stats', 'yith-woocommerce-affiliates' ); ?></h2>
					</div>

					<table class="shop_table stat_table">
						<tbody>
						<tr>
							<th><?php esc_html_e( 'Affiliate rate', 'yith-woocommerce-affiliates' ); ?></th>
							<td><?php echo number_format( $referral_stats['rate'], 2 ); ?> <?php echo esc_html( apply_filters( 'yith_wcaf_display_symbol', '%' ) ); ?></td>
						</tr>

						<tr>
							<th><?php esc_html_e( 'Total Earnings', 'yith-woocommerce-affiliates' ); ?></th>
							<td><?php echo wc_price( $referral_stats['earnings'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
						</tr>

						<tr>
							<th><?php esc_html_e( 'Total Paid', 'yith-woocommerce-affiliates' ); ?></th>
							<td><?php echo wc_price( $referral_stats['paid'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
						</tr>

						<tr>
							<th><?php esc_html_e( 'Total Refunded', 'yith-woocommerce-affiliates' ); ?></th>
							<td><?php echo wc_price( $referral_stats['refunds'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
						</tr>

						<tr>
							<th><?php esc_html_e( 'Balance', 'yith-woocommerce-affiliates' ); ?></th>
							<td><?php echo wc_price( $referral_stats['balance'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
						</tr>

						<tr>
							<th><?php esc_html_e( 'Visits', 'yith-woocommerce-affiliates' ); ?></th>
							<td><?php echo esc_html( $referral_stats['click'] ); ?></td>
						</tr>

						<tr>
							<th><?php esc_html_e( 'Conversion rate', 'yith-woocommerce-affiliates' ); ?></th>
							<td><?php echo ! empty( $referral_stats['conv_rate'] ) ? number_format( $referral_stats['conv_rate'], 2 ) : esc_html__( 'N/A', 'yith-woocommerce-affiliates' ); ?> <?php echo esc_html( apply_filters( 'yith_wcaf_display_symbol', '%' ) ); ?></td>
						</tr>
						</tbody>
					</table>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!--NAVIGATION MENU-->
		<?php
		$atts = array(
			'show_right_column'    => $show_right_column,
			'show_left_column'     => $show_left_column,
			'show_dashboard_links' => $show_dashboard_links,
			'dashboard_links'      => $dashboard_links,
		);
		yith_wcaf_get_template( 'navigation-menu.php', $atts, 'shortcodes' );
		?>

	</div>

	<!--COMMISSION SUMMARY-->
	<?php if ( $show_commissions_summary ) : ?>
		<div class="dashboard-title">
			<h2><?php esc_html_e( 'Recent Commissions', 'yith-woocommerce-affiliates' ); ?></h2>

			<span class="view-all">(<a href="<?php echo esc_url( $dashboard_links['commissions']['url'] ); ?>"><?php esc_html_e( 'View all', 'yith-woocommerce-affiliates' ); ?></a>)</span>
		</div>

		<table class="shop_table">
			<thead>
			<tr>
				<th class="commission-ID"><span class="nobr"><?php esc_html_e( 'ID', 'yith-woocommerce-affiliates' ); ?></span></th>
				<th class="commission-status"><span class="nobr"><?php esc_html_e( 'Status', 'yith-woocommerce-affiliates' ); ?></span></th>
				<th class="commission-rate"><span class="nobr"><?php esc_html_e( 'Rate', 'yith-woocommerce-affiliates' ); ?></span></th>
				<th class="commission-amount"><span class="nobr"><?php esc_html_e( 'Amount', 'yith-woocommerce-affiliates' ); ?></span></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( ! empty( $commissions ) ) : ?>
				<?php foreach ( $commissions as $commission ) : ?>
					<tr>
						<td class="commission-ID">#<?php echo esc_attr( $commission['ID'] ); ?></td>
						<td class="commission-status <?php echo esc_attr( $commission['status'] ); ?>"><?php echo esc_html( YITH_WCAF_Commission_Handler()->get_readable_status( $commission['status'] ) ); ?></td>
						<td class="commission-rate"><?php echo number_format( $commission['rate'], 2 ); ?> <?php echo esc_html( apply_filters( 'yith_wcaf_display_symbol', '%' ) ); ?></td>
						<td class="commission-amount"><?php echo wc_price( $commission['amount'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="4" class="empty-set"><?php esc_html_e( 'Sorry! There are no registered commissions yet', 'yith-woocommerce-affiliates' ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<!--CLICKS SUMMARY-->
	<?php if ( $show_clicks_summary ) : ?>
		<div class="dashboard-title">
			<h2><?php esc_html_e( 'Recent Clicks', 'yith-woocommerce-affiliates' ); ?></h2>
			<span class="view-all">(<a href="<?php echo esc_url( $dashboard_links['clicks']['url'] ); ?>"><?php esc_html_e( 'View all', 'yith-woocommerce-affiliates' ); ?></a>)</span>
		</div>

		<table class="shop_table">
			<thead>
			<tr>
				<th class="click-link"><span class="nobr"><?php esc_html_e( 'Link', 'yith-woocommerce-affiliates' ); ?></span></th>
				<th class="click-origin"><span class="nobr"><?php esc_html_e( 'Origin', 'yith-woocommerce-affiliates' ); ?></span></th>
				<th class="click-status"><span class="nobr"><?php esc_html_e( 'Status', 'yith-woocommerce-affiliates' ); ?></span></th>
				<th class="click-date"><span class="nobr"><?php esc_html_e( 'Date', 'yith-woocommerce-affiliates' ); ?></span></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( ! empty( $clicks ) ) : ?>
				<?php foreach ( $clicks as $click ) : ?>
					<tr>
						<td class="click-link"><?php echo esc_url( $click['link'] ); ?></td>
						<td class="click-origin"><?php echo ! empty( $click['origin_base'] ) ? esc_url( $click['origin_base'] ) : esc_html__( 'N/A', 'yith-woocommerce-affiliates' ); ?></td>
						<td class="click-status <?php echo ! empty( $click['order_id'] ) ? 'converted' : 'not-converted'; ?>"><?php echo ! empty( $click['order_id'] ) ? esc_html__( 'Converted', 'yith-woocommerce-affiliates' ) : esc_html__( 'Not converted', 'yith-woocommerce-affiliates' ); ?></td>
						<td class="click-date"><time datetime="<?php echo esc_html( gmdate( 'Y-m-d', strtotime( $click['click_date'] ) ) ); ?>" title="<?php echo esc_attr( strtotime( $click['click_date'] ) ); ?>"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $click['click_date'] ) ) ); ?></time></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="4" class="empty-set"><?php esc_html_e( 'Sorry! There are no registered commissions yet', 'yith-woocommerce-affiliates' ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<?php do_action( 'yith_wcaf_after_dashboard_summary' ); ?>

</div>
