<?php
/**
 * Payment Table Admin Panel
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly
?>

<div id="yith_wcaf_panel_payment">
	<form id="plugin-fw-wc" class="payment-table" method="get">
		<input type="hidden" name="page" value="yith_wcaf_panel" />
		<input type="hidden" name="tab" value="payments" />

		<?php
		$payments_table->views();
		$payments_table->display();
		?>
	</form>
</div>