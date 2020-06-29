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
<div class="wrap woocommerce">
	<div class="icon32" id="icon-woocommerce-importer"><br></div>
	<h2><?php esc_html_e( 'Import Customers, Coupons &amp; Orders', 'woocommerce-csv-import-suite' ); ?></h2>

	<div id="message" class="updated woocommerce-message wc-connect">
		<div class="squeezer">
			<h4><?php printf( esc_html__( '%1$sCustomer CSV Import Suite%2$s Before getting started prepare your CSV files:', 'woocommerce-csv-import-suite' ), '<strong>', '</strong> &#8211;' ); ?></h4>

			<p class="submit"><a href="<?php echo wc_csv_import_suite()->get_documentation_url(); ?>" class="button-primary"><?php esc_html_e( 'Documentation', 'woocommerce-csv-import-suite' ); ?></a>
				<a class="docs button-primary" target="_blank" href="https://docs.google.com/spreadsheets/d/16ub-_xEJD9V5UL6d_rTQ4LLu0PT9jXJ0Ti-iirlKyuU/edit#gid=0"><?php esc_html_e( 'Sample Customer CSV', 'woocommerce-csv-import-suite' ); ?></a>
				<a class="docs button-primary" target="_blank" href="https://docs.google.com/spreadsheets/d/16ub-_xEJD9V5UL6d_rTQ4LLu0PT9jXJ0Ti-iirlKyuU/edit#gid=620764597"><?php esc_html_e( 'Sample Coupon CSV', 'woocommerce-csv-import-suite' ); ?></a>
				<a class="docs button-primary" target="_blank" href="https://docs.google.com/spreadsheets/d/16ub-_xEJD9V5UL6d_rTQ4LLu0PT9jXJ0Ti-iirlKyuU/edit#gid=584795629"><?php esc_html_e( 'Sample Order CSV', 'woocommerce-csv-import-suite' ); ?></a>
			<p>
		</div>
	</div>

	<div class="tool-box">

		<h3 class="title"><?php esc_html_e( 'Import Customer CSV', 'woocommerce-csv-import-suite' ); ?></h3>
		<p><?php esc_html_e('Import customers into WooCommerce using this tool.', 'woocommerce-csv-import-suite'); ?></p>
		<p class="description"><?php esc_html_e( 'Import a CSV from your computer. Import your CSV as new customers (existing customers will be skipped), or merge with existing customers.', 'woocommerce-csv-import-suite' ); ?></p>
		<p class="submit"><a class="button" href="<?php echo esc_url( ! empty( $import_progress_url ) ? $import_progress_url : admin_url( 'admin.php?import=woocommerce_customer_csv' ) ); ?>"><?php esc_html_e( 'Import Customers', 'woocommerce-csv-import-suite' ); ?></a></p>

	</div>

	<div class="tool-box">

		<h3 class="title"><?php esc_html_e( 'Import Coupons CSV', 'woocommerce-csv-import-suite' ); ?></h3>
		<p><?php esc_html_e( 'Import and add coupons using this tool.', 'woocommerce-csv-import-suite' ); ?></p>
		<p class="description"><?php esc_html_e( 'Import a CSV from your computer. Import your CSV as new coupons (existing coupons will be skipped), or merge with existing coupons.', 'woocommerce-csv-import-suite' ); ?></p>
		<p class="submit"><a class="button" href="<?php echo esc_url( ! empty( $import_progress_url ) ? $import_progress_url : admin_url( 'admin.php?import=woocommerce_coupon_csv' ) ); ?>"><?php esc_html_e( 'Import Coupons', 'woocommerce-csv-import-suite' ); ?></a></p>

	</div>

	<div class="tool-box">

		<h3 class="title"><?php esc_html_e( 'Import Orders CSV', 'woocommerce-csv-import-suite' ); ?></h3>
		<p><?php esc_html_e( 'Import and add orders using this tool.', 'woocommerce-csv-import-suite' ); ?></p>
		<p class="description"><?php esc_html_e( 'Import a CSV from your computer. Import your CSV as new orders (existing orders will be skipped), or merge with existing orders.', 'woocommerce-csv-import-suite' ); ?></p>
		<p class="submit"><a class="button" href="<?php echo esc_url( ! empty( $import_progress_url ) ? $import_progress_url : admin_url( 'admin.php?import=woocommerce_order_csv' ) ); ?>"><?php esc_html_e( 'Import Orders', 'woocommerce-csv-import-suite' ); ?></a></p>

	</div>

</div>
<?php
