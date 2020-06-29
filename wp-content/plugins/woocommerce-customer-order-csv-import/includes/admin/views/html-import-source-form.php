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

	<form enctype="multipart/form-data" id="source-input-form" class="csv-import-suite-form" method="post">

		<?php $csv_importer->render_source_input_fields( $source ); ?>

		<input type="hidden" name="action" value="upload" />

		<?php wp_nonce_field( 'import-upload' ); ?>

		<p class="submit">
			<input type="submit" class="button" value="<?php esc_attr_e( 'Next &raquo;', 'woocommerce-csv-import-suite' ); ?>" />
		</p>

	</form>

</div>
<?php
