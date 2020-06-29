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
<h3><?php esc_html_e( 'Options', 'woocommerce-csv-import-suite' ); ?></h3>

<table class="form-table">

	<?php
		/**
		 * Fire before rendering the import options fields in admin
		 *
		 * @since 3.0.0
		 * @param \WC_CSV_Import_Suite_Importer $importer Importer instance
		 */
		do_action( 'wc_csv_import_suite_before_import_options_fields', $csv_importer );
	?>

	<tr>
		<th>
			<?php esc_html_e( 'Merge/update', 'woocommerce-csv-import-suite' ); ?>
		</th>
		<td>
			<label>
				<input type="checkbox" value="1" name="options[merge]" id="wc-csv-import-suite-merge" class="js-wc-csv-import-suite-merge" />
				<?php esc_html_e( 'Update existing records if a match is found', 'woocommerce-csv-import-suite' ); ?>
			</label>
		</td>
	</tr>

	<tr style="display: none;"><!-- This advanced merge option is hidden by default -->
		<th></th>
		<td>
			<label>
				<input type="checkbox" value="1" name="options[insert_non_matching]" checked id="wc-csv-import-suite-insert-non-matching" />
				<?php esc_html_e( 'Insert as new if a match is not found', 'woocommerce-csv-import-suite' ); ?>
			</label>
		</td>
	</tr>

	<tr>
		<th>
			<?php esc_html_e( 'Debug Mode', 'woocommerce-csv-import-suite' ); ?>
		</th>
		<td>
			<label for="wc-csv-import-suite-debug-mode">
				<input type="checkbox" value="1" name="options[debug_mode]" id="wc-csv-import-suite-debug-mode" />
				<?php esc_html_e( 'Enable logging for this import. Please leave this disabled unless you are experiencing issues with imports.', 'woocommerce-csv-import-suite' ); ?>
			</label>
		</td>
	</tr>

	<tr>
		<th>
			<label for="wc-csv-import-suite-delimiter"><?php _e( 'Fields are separated by', 'woocommerce-csv-import-suite' ); ?></label>
		</th>
		<td>
			<select id="wc-csv-import-suite-delimiter" name="options[delimiter]" class="js-wc-csv-import-suite-delimiter">
				<?php foreach ( $csv_importer->get_valid_delimiters() as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $delimiter, $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>

	<?php
		/**
		 * Fire after rendering the import options fields in admin
		 *
		 * @since 3.0.0
		 * @param \WC_CSV_Import_Suite_Importer $importer Importer instance
		 */
		do_action( 'wc_csv_import_suite_after_import_options_fields', $csv_importer );
	?>

</table>
<?php
