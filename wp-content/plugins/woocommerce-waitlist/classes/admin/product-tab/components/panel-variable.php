<?php
/**
 * HTML required for the waitlist panel on the product edit screen for variable products
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} ?>

<div id="wcwl_waitlist_data" class="panel woocommerce_options_panel">
	<?php
	$children = Pie_WCWL_Custom_Tab::$product->get_children();
	foreach ( $children as $product_id ) { ?>
		<div class="wcwl_variation_tab" id="wcwl_variation_<?php echo $product_id; ?>">
			<div class="wcwl_header_wrap">
				<h3>
					<?php echo Pie_WCWL_Custom_Tab::return_variation_tab_title( $product_id ); ?>
				</h3>
			</div>
			<div class="wcwl_body_wrap" data-product-id="<?php echo $product_id ?>">
				<?php
				include apply_filters( 'wcwl_include_path_admin_panel_tabs', Pie_WCWL_Custom_Tab::$component_path . 'panel-tabs.php' );
				include apply_filters( 'wcwl_include_path_admin_panel_waitlist_tab', Pie_WCWL_Custom_Tab::$component_path . 'panel-waitlist.php' );
				include apply_filters( 'wcwl_include_path_admin_panel_archive_tab', Pie_WCWL_Custom_Tab::$component_path . 'panel-archive.php' );
				include apply_filters( 'wcwl_include_path_admin_panel_options_tab', Pie_WCWL_Custom_Tab::$component_path . 'panel-options.php' );
				?>
			</div>
		</div>
	<?php } ?>
</div>
