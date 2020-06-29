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
<h3><?php esc_html_e( 'Copy & paste data', 'woocommerce-csv-import-suite' ); ?></h3>

<table class="form-table">

	<?php
		/**
		 * Fire before rendering the copy-paste import source fields
		 *
		 * @since 3.0.0
		 * @param \WC_CSV_Import_Suite_Importer $importer Importer instance
		 */
		do_action( 'wc_csv_import_suite_before_copypaste_input_fields', $csv_importer );
	?>

	<tr>
		<td>
			<textarea name="copypaste" rows="10" cols="50" id="copypaste" class="large-text code"></textarea>
		</td>
	</tr>

	<?php
		/**
		 * Fire after rendering the copy-paste import source fields
		 *
		 * @since 3.0.0
		 * @param \WC_CSV_Import_Suite_Importer $importer Importer instance
		 */
		do_action( 'wc_csv_import_suite_after_copypaste_input_fields', $csv_importer );
	?>

</table>
<?php
