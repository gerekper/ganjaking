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
<h3><?php esc_html_e( 'Preview', 'woocommerce-csv-import-suite' ); ?></h3>
<p><?php esc_html_e( "Does the preview below look right? Each field should be in its own table cell. If it doesn't, simply adjust the delimiter until it looks right. If none of the options seem to work, make sure you are using a well-formatted CSV file.", 'woocommerce-csv-import-suite' ); ?></p>

<div class="wc-csv-import-suite-preview-container">
	<div class="wc-csv-import-suite-preview-overflow-container">

		<table class="widefat wc-csv-import-suite-preview" id="wc-csv-import-suite-preview" cellspacing="0">
			<?php echo $rows; ?>
		</table>

	</div>
</div>
<?php
