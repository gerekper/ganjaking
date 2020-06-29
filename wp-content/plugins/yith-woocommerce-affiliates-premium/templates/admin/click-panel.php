<?php
/**
 * Click Admin Panel
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

<div id="yith_wcaf_panel_click">
	<form id="plugin-fw-wc" class="click-table" method="get">
		<input type="hidden" name="page" value="yith_wcaf_panel" />
		<input type="hidden" name="tab" value="clicks" />

		<div class="yith-wcaf-delete-logs">
			<h4><?php _e( 'Delete click logs', 'yith-woocommerce-affiliates' ) ?></h4>
			<?php
			yit_add_select2_fields( array(
				'class' => 'yith-users-select wc-product-search',
				'name' => 'yith_delete_affiliate_log',
				'data-action' => 'json_search_affiliates',
				'data-placeholder' => __( 'Search for an affiliate&hellip;', 'yith-woocommerce-affiliates' ),
				'style' => 'min-width: 300px;',
			) );
			?>
			<input type="submit" class="yith-delete-affiliate-log-submit button button-primary" value="<?php echo esc_attr( __( 'Delete Logs', 'yith-woocommerce-affiliates' ) ) ?>" />
		</div>

		<?php
		$clicks_table->views();
		$clicks_table->display();
		?>
	</form>
</div>