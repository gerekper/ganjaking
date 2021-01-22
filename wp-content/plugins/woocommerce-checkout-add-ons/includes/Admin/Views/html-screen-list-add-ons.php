<?php
/**
 * WooCommerce Checkout Add-Ons
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Admin\Add_Ons_List_Table;

defined( 'ABSPATH' ) or exit;

/**
 * View for the add-on list screen
 *
 * @since 2.0.0
 *
 * @type string $new_add_on_url the URL to the screen for creating a new add-on
 * @type Add_Ons_List_Table $list_table the list table object
 */
?>

<div class="wrap woocommerce">
	<form method="get" id="mainform" action="" class="wc-checkout-add-ons">
		<h1 class="wp-heading-inline"><?php echo esc_html_x( 'Checkout Add-Ons', 'page title', 'woocommerce-checkout-add-ons' ); ?></h1>
		<a href="<?php echo esc_url( $new_add_on_url ); ?>" class="page-title-action"><?php echo esc_html_x( 'Add add-on', 'page title action', 'woocommerce-checkout-add-ons' ); ?></a>
		<hr class="wp-header-end">
		<input type="hidden" name="page" value="wc_checkout_add_ons" />
		<?php

		$list_table->prepare_items();
		$list_table->display();
		$list_table->bulk_edit_fields();

		?>
	</form>
</div>
