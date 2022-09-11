<?php
/**
 * WooCommerce Social Login
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * HTML template file for Social Login reports.
 *
 * @since 1.0.0
 * @version 2.6.0
 */
?>
<div id="poststuff" class="woocommerce-reports-wide wc-social-login-report">
	<table class="wp-list-table widefat fixed social-registrations">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Provider', 'woocommerce-social-login' ); ?></th>
				<th><?php echo esc_html_x( 'Registrations', 'The number of registrations for a provider', 'woocommerce-social-login' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! empty( $social_registrations ) ) : ?>
				<?php foreach ( $social_registrations as $data ) : ?>
					<tr>
						<td>
							<span class="chart-legend" style="background-color: <?php echo esc_attr( $data['chart_color'] ); ?>;">&nbsp;</span>
							<?php echo esc_html( $data['provider_title'] ); ?>
						</td>
						<td><?php echo esc_html( $data['linked_accounts'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>

	<div class="chart-container">
		<div class="chart-placeholder social-registrations pie-chart" style="height:200px"></div>
	</div>
	<script type="text/javascript">
		jQuery(function(){
			jQuery.plot(
				jQuery('.chart-placeholder.social-registrations'),
				[
					<?php if ( ! empty( $social_registrations ) ) : ?>
					<?php foreach ( $social_registrations as $data ) : ?>
					{
						label: "<?php echo esc_js( $data['provider_title'] ); ?>",
						data:  "<?php echo esc_js( $data['linked_accounts'] ); ?>",
						color: "<?php echo esc_js( $data['chart_color'] ); ?>"
					},
					<?php endforeach; ?>
					<?php endif; ?>
				],
				{
					grid: {
						hoverable: true
					},
					series: {
							pie: {
								show: true,
								radius: 1,
								innerRadius: 0.6,
								label: {
									show: false
								}
							},
							enable_tooltip: true,
							append_tooltip: "<?php echo ' ' . /* translators: Number of linked accounts for a provider. A number will be prepended to this string. Example: 5 linked accounts */ esc_attr__( 'linked accounts', 'woocommerce-social-login' ); ?>"
					},
					legend: {
							show: false
					}
				}
			);

			jQuery('.chart-placeholder.social-registrations').resize();
		});
	</script>
</div>
