<?php
/**
 * HTML required for each single archive on the waitlist tab
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="archive wcwl_tab_content" data-panel="archive">
	<div class="wcwl_add_user_wrap">
	</div>
	<?php include apply_filters( 'wcwl_include_path_admin_panel_archive_actions', Pie_WCWL_Custom_Tab::$component_path . 'panel-actions-archive.php' ); ?>
	<p class="wcwl_no_users_text">
		<?php _e( 'There are no saved users for this product.', 'woocommerce-waitlist' ); ?>
	</p>
	<table class="widefat wcwl_waitlist_table">
		<tr>
			<th><input name="wcwl_select_all" type="checkbox"/></th>
			<th><?php _e( 'User', 'woocommerce-waitlist' ); ?></th>
			<th><?php _e( 'Removed', 'woocommerce-waitlist' ); ?></th>
		</tr>
		<?php
		$archives = Pie_WCWL_Custom_Tab::retrieve_and_sort_archives( $product_id );
		foreach ( $archives as $date => $users ) { ?>
			<?php foreach ( $users as $user ) {
				include apply_filters( 'wcwl_include_path_admin_panel_table_row', Pie_WCWL_Custom_Tab::$component_path . 'panel-table-row.php' );
			}
		} ?>
	</table>
</div>
