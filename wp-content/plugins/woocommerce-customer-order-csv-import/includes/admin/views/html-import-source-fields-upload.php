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
<h3><?php esc_html_e( 'Import data from CSV file', 'woocommerce-csv-import-suite' ); ?></h3>
<?php

if ( ! empty( $upload_dir['error'] ) ) :

	?>
	<div class="error">
		<p><?php esc_html_e( 'Before you can start importing, you will need to fix the following error:', 'woocommerce-csv-import-suite' ); ?></p>
		<p><strong><?php echo esc_html( $upload_dir['error'] ); ?></strong></p>
	</div>
	<?php

else :

	?>
	<table class="form-table">

		<?php
			/**
			 * Fire before rendering the upload import source fields
			 *
			 * @since 3.0.0
			 * @param \WC_CSV_Import_Suite_Importer $importer Importer instance
			 */
			do_action( 'wc_csv_import_suite_before_upload_input_fields', $csv_importer );
		?>

		<tr>
			<th>
				<label for="import"><?php esc_html_e( 'Choose a file from your computer:', 'woocommerce-csv-import-suite' ); ?></label>
			</th>
			<td>
				<input type="file" id="import" name="import" />
				<input type="hidden" name="max_file_size" value="<?php echo esc_attr( $bytes ); ?>" />
				<p class="description"><small><?php printf( esc_html__( 'Acceptable file types: CSV or tab-delimited text files. Maximum size: %1$s', 'woocommerce-csv-import-suite' ), $size ); ?></small></p>
			</td>
		</tr>

		<?php
			/**
			 * Fire after rendering the upload import source fields
			 *
			 * @since 3.0.0
			 * @param \WC_CSV_Import_Suite_Importer $importer Importer instance
			 */
			do_action( 'wc_csv_import_suite_after_upload_input_fields', $csv_importer );
		?>

	</table>
	<?php

endif;
