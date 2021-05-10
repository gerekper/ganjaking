<?php
/**
 * HTML required for the waitlist panel on the event edit screen
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$product_id = Pie_WCWL_Custom_Tab::$product->get_id(); ?>
<button class="accordion-header woocommerce-waitlist" id="wcwl-tab" aria-selected="false" aria-expanded="false" aria-controls="wcwl-panel" role="tab">
	<?php _e( 'Waitlist', 'woocommerce-waitlist' ); ?>
</button>
<section class="accordion-content" id="wcwl-panel" aria-hidden="true" aria-labelledby="wcwl-tab" role="tabpanel">
	<div id="wcwl_waitlist_data" class="panel woocommerce_options_panel">
		<div class="wcwl_body_wrap" data-product-id="<?php echo $product_id; ?>">
			<?php
			require apply_filters( 'wcwl_include_path_admin_panel_tabs', Pie_WCWL_Custom_Tab::$component_path . 'panel-tabs.php' );
			require apply_filters( 'wcwl_include_path_admin_panel_waitlist_tab', Pie_WCWL_Custom_Tab::$component_path . 'panel-waitlist.php' );
			require apply_filters( 'wcwl_include_path_admin_panel_archive_tab', Pie_WCWL_Custom_Tab::$component_path . 'panel-archive.php' );
			require apply_filters( 'wcwl_include_path_admin_panel_options_tab', Pie_WCWL_Custom_Tab::$component_path . 'panel-options.php' );
			?>
		</div>
	</div>
</section>
