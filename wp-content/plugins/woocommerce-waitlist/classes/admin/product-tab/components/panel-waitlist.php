<?php
/**
 * HTML required for each single waitlist on the waitlist tab
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="waitlist wcwl_tab_content current" data-panel="waitlist">
	<?php
	include apply_filters( 'wcwl_include_path_admin_panel_waitlist_add_new', Pie_WCWL_Custom_Tab::$component_path . 'panel-add-new.php' );
	include apply_filters( 'wcwl_include_path_admin_panel_waitlist_actions', Pie_WCWL_Custom_Tab::$component_path . 'panel-actions-waitlist.php' );
	?>
	<div class="wcwl_no_users">
		<p class="wcwl_no_users_text">
			<?php esc_html_e( apply_filters( 'wcwl_empty_waitlist_introduction', __( 'There are no users on the waiting list for this product.', 'woocommerce-waitlist' ) ) ); ?>
		</p>
	</div>

	<table class="widefat wcwl_waitlist_table">
		<tr>
			<th><input name="wcwl_select_all" type="checkbox"/></th>
			<th><?php _e( 'User', 'woocommerce-waitlist' ); ?></th>
			<th><?php _e( 'Added', 'woocommerce-waitlist' ); ?></th>
		</tr>
		<?php
		$product  = wc_get_product( $product_id );
		$waitlist = new Pie_WCWL_Waitlist( $product );
		$users    = $waitlist->waitlist;
		$errors   = get_post_meta( $product_id, 'wcwl_mailout_errors', true );
		foreach ( $users as $user => $date ) {
			if ( $user ) {
				include apply_filters( 'wcwl_include_path_admin_panel_table_row', Pie_WCWL_Custom_Tab::$component_path . 'panel-table-row.php' );
			}
		} ?>
	</table>
</div>
