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
<h3><?php esc_html_e( 'Map Fields', 'woocommerce-csv-import-suite' ); ?></h3>
<p><?php esc_html_e( 'Here you can map your imported columns to WooCommerce data fields.', 'woocommerce-csv-import-suite' ); ?></p>

<div class="woocommerce">

	<form class="csv-import-suite-form" action="<?php echo esc_url( admin_url( 'admin.php?import=' . esc_attr( $_GET['import'] ) . '&step=4' ) ); ?>" method="post">

		<?php wp_nonce_field( 'import-woocommerce' ); ?>

		<input type="hidden" name="import" value="<?php echo esc_attr( $_GET['import'] ); ?>" />
		<input type="hidden" name="file" value="<?php echo esc_attr( $_GET['file'] ); ?>" />
		<input type="hidden" name="action" value="kickoff" />

		<?php foreach ( $options as $option => $option_value ) : ?>
			<input type="hidden" name="options[<?php echo esc_attr( $option ) ; ?>]" value="<?php echo esc_attr( $option_value ); ?>" />
		<?php endforeach; ?>

		<?php
			/**
			 * Fire before rendering the column mapper in admin
			 *
			 * @since 3.0.0
			 * @param \WC_CSV_Import_Suite_Importer $importer Importer instance
			 */
			do_action( 'wc_csv_import_suite_before_import_column_mapper', $csv_importer );
		?>

		<table class="widefat widefat_importer csv-import-suite-column-mapper">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Column Header', 'woocommerce-csv-import-suite' ); ?></th>
					<th><?php esc_html_e( 'Map to', 'woocommerce-csv-import-suite' ); ?></th>
					<th colspan="<?php echo esc_attr( $sample_size ); ?>"><?php esc_html_e( 'Example Column Values', 'woocommerce-csv-import-suite' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $raw_headers as $field => $raw_header ) : ?>
				<tr>
					<td width="25%"><?php echo esc_html( $raw_header ); ?></td>
					<td width="25%">
						<select name="options[mapping][<?php echo esc_attr( $field ); ?>]">
							<option value=""><?php esc_html_e( '-- Skip --', 'woocommerce-csv-import-suite' ); ?></option>
							<?php echo $csv_importer->generate_mapping_options_html( $mapping_options, $columns[ $field ]['default_mapping'] ); ?>
						</select>
					</td>
					<?php foreach ( $columns[ $field ]['sample_values'] as $value ) : ?>
					<td class="sample-value"><code><?php if ( '' !== $value ) echo esc_html( wp_trim_words( $value, 30 ) ); else echo '-'; ?></code></td>
					<?php endforeach; ?>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php
			/**
			 * Fire after rendering the column mapper in admin
			 *
			 * @since 3.0.0
			 * @param \WC_CSV_Import_Suite_Importer $importer Importer instance
			 */
			do_action( 'wc_csv_import_suite_after_import_column_mapper', $csv_importer );
		?>

		<p class="submit">
			<button type="submit" class="button" name="options[dry_run]" value="1"><?php esc_html_e( 'Dry Run &raquo;', 'woocommerce-csv-import-suite' ); ?></button>
			<button type="submit" class="button"><?php esc_attr_e( 'Start Import &raquo;', 'woocommerce-csv-import-suite' ); ?></button>
		</p>

	</form>

</div>
<?php
