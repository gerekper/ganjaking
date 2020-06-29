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

	<h3><?php esc_html_e( 'Where do you want to import data from?', 'woocommerce-csv-import-suite' ); ?></h3>

	<?php if ( ! empty( $upload_dir['error'] ) ) : ?>

		<div class="error">
			<p><?php esc_html_e( 'Before you can start importing, you will need to fix the following error:', 'woocommerce-csv-import-suite' ); ?></p>
			<p><strong><?php echo esc_html( $upload_dir['error'] ); ?></strong></p>
		</div>

	<?php else : ?>

		<form id="import-source-form" class="csv-import-suite-form" method="get">

			<input type="hidden" name="import" value="<?php echo esc_attr( $_GET['import'] ); ?>" />
			<input type="hidden" name="step" value="1" />

			<table class="form-table">
				<tbody>

				<?php foreach ( $source_options as $option ) : ?>

					<tr>
						<td>
							<label class="radio">
								<input name="source" type="radio" value="<?php echo esc_attr( $option['value'] ); ?>" class="tog" <?php checked( ( isset( $option['default'] ) && $option['default'] ), true ) ?> />
								<span class="title"><?php echo esc_html( $option['title'] ); ?></span>
								<span class="description"><?php echo esc_html( $option['description'] ); ?></span>
							</label>
						</td>
					</tr>

				<?php endforeach; ?>

				</tbody>
			</table>

			<p class="submit">
				<input type="submit" class="button" value="<?php esc_attr_e( 'Next &raquo;', 'woocommerce-csv-import-suite' ); ?>" />
			</p>

		</form>

	<?php endif; ?>

</div>
<?php
