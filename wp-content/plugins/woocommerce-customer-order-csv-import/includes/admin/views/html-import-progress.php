<?php
/**
 * WooCommerce Customer/Order/Coupon CSV Import Suite
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon CSV Import Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon CSV Import Suite for your
 * needs please refer to http://docs.woocommerce.com/document/customer-order-csv-import-suite/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

?>
<div class="woocommerce">

	<p><?php printf( $is_complete ? esc_html__( 'Imported file: %s', 'woocommerce-csv-import-suite' ) : esc_html__( 'Importing file: %s', 'woocommerce-csv-import-suite' ), $filename ); ?></p>

	<?php if ( isset( $_GET['block_new_import'] ) && 'yes' === $_GET['block_new_import'] ) : ?>
		<div class="notice notice-error">
			<p><?php /* translators: Placeholders: %1$s - <strong>, %2$s - </strong> */
				printf( esc_html__( '%1$sAn import is currently in progress.%2$s Please wait for the current import to complete before beginning another.', 'woocommerce-csv-import-suite' ), '<strong>', '</strong>' );
			?></p>
		</div>
	<?php endif; ?>

	<?php if ( $is_complete ) : ?>
		<div class="notice notice-<?php echo $some_skipped_or_failed ? 'warning' : 'success'; ?> notice-complete">
			<p><?php esc_html_e( 'Import complete.', 'woocommerce-csv-import-suite' ); ?><?php if ( $some_skipped_or_failed ) : echo ' ' . esc_html__( 'Some lines were skipped or failed to import. See below for details.', 'woocommerce-csv-import-suite' ) ; endif; ?></p>
		</div>
	<?php else: ?>
		<div class="notice notice-success js-notice-safe-to-leave-screen">
			<p><?php esc_html_e( 'Import has started. You can safely leave this page - you will see a notice in the admin area once the import is complete.', 'woocommerce-csv-import-suite' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( $options['dry_run'] ) : ?>
		<div class="notice notice-warning notice-dry-run">
			<p>
				<?php if ( $is_complete ) : ?>
					<?php printf( esc_html__( 'Performed a dry run with the selected file. No database records were inserted or updated. %1$sRun a live import now%2$s or %3$sChange import settings%4$s.', 'woocommerce-csv-import-suite' ), '<a href="' . wp_nonce_url( admin_url( 'admin.php?import=' . esc_attr( $_GET['import'] ) . '&job_id=' . esc_attr( $job->id ) . '&action=run_live' ), 'import-woocommerce' ) . '">', '</a>', '<a href="' . admin_url( 'admin.php?import=' . esc_attr( $_GET['import'] ) . '&step=2&file=' . urlencode( $job->file_path ) ) . '">', '</a>' ); ?>
				<?php else : ?>
					<?php esc_html_e( 'Performing a dry run with the selected file. No database records will be inserted or updated.', 'woocommerce-csv-import-suite' ) ; ?>
				<?php endif; ?>
			</p>
		</div>
	<?php endif; ?>

	<div id="wc-csv-import-suite-progress">

		<div class="progress-bar">
			<div class="bar" style="width: <?php echo $is_complete ? '100' : $percentage; ?>%">
				<?php if ( ! $is_complete ) : ?>
					<span class="js-spinner dashicons dashicons-update wc-csv-import-suite-dashicons-spin"></span>
				<?php endif; ?>
				<span class="percentage"><?php echo $is_complete ? '100' : $percentage; ?> %</span>
			</div>
		</div>

		<div class="progress-count">
			<?php esc_html_e( 'Processed:', 'woocommerce-csv-import-suite' ); ?> <?php printf( $csv_importer->i18n['count'], '<span class="processed-count">' . ( is_array( $results ) || is_object( $results ) ? count( $results ) : 0 ) . '</span>' ); ?>
		</div>

	</div>

	<div id="wc-csv-import-suite-results">

		<h3><?php esc_html_e( 'Results', 'woocommerce-csv-import-suite' ); ?></h3>

		<div class="woocommerce-reports-wide">
			<div class="postbox">

			<div class="inside chart-with-sidebar">
				<div class="chart-sidebar">
					<ul class="chart-legend">
						<?php foreach ( $legends as $key => $legend ) : ?>
							<li style="border-color: <?php echo $legend['color']; ?>" <?php if ( isset( $legend['highlight_series'] ) ) echo 'class="highlight_series js-wc-csv-import-suite-highlight-series' . ( isset( $legend['placeholder'] ) ? 'tips' : '' ) . ' ' . esc_attr( $key ) . '" data-series="' . esc_attr( $legend['highlight_series'] ) . '"'; ?> data-tip="<?php echo isset( $legend['placeholder'] ) ? $legend['placeholder'] : ''; ?>">
								<?php echo $legend['title']; ?>
							</li>
						<?php endforeach; ?>
					</ul>

					<ul class="chart-widgets">
						<li class="chart-widget details-widget js-wc-csv-import-suite-results-details-link-widget" <?php if ( ! $some_skipped_or_failed ) : echo 'style="display:none;"'; endif; ?>>
							<a class="js-wc-csv-import-suite-toggle-results-details details-toggle" href="#"><span class="dashicons dashicons-list-view"></span> <span class="js-wc-csv-import-suite-toggle-results-details-text"><?php esc_html_e( 'View detailed results', 'woocommerce-csv-import-suite' ); ?></span></a>
						</li>
					</ul>
				</div>

				<div class="main">
					<div class="chart-container">
						<div class="chart-placeholder import-results pie-chart"></div>
					</div>
				</div>
			</div>

		</div>
		</div>

		<table class="widefat results-details">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Line', 'woocommerce-csv-import-suite' ); ?></th>
					<th><?php esc_html_e( 'Status', 'woocommerce-csv-import-suite' ); ?></th>
					<th><?php esc_html_e( 'Reason', 'woocommerce-csv-import-suite' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! empty( $results ) ) : foreach ( $results as $line => $result ) : ?>
					<?php if ( in_array( $result['status'], array( 'skipped', 'failed' ), true ) ) : ?>
					<tr class="line-<?php echo esc_attr( $line ); ?> <?php echo esc_attr( $result['status'] ); ?>">
						<td><?php echo esc_html( $line ); ?></td>
						<td><?php echo esc_html( $legends[ $result['status'] ]['label'] ); ?></td>
						<td><?php echo esc_html( $result['message'] ); ?></td>
					</tr>
					<?php endif; ?>
				<?php endforeach; endif; ?>
			</tbody>
		</table>

	</div>

</div>
<?php
