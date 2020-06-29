<?php
/**
 * Composite Products Template Hooks
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*----------------------------------*/
/*  Single product template hooks.  */
/*----------------------------------*/

// Single product form content: Displayed in the Summary.
add_action( 'woocommerce_composite_add_to_cart', 'wc_cp_add_to_cart' );

// Single product form content: Displayed after the Summary.
add_action( 'woocommerce_after_single_product_summary', 'wc_cp_add_to_cart_after_summary', -1000 );

// Single product add-to-cart button template for composite products.
add_action( 'woocommerce_composite_add_to_cart_button', 'wc_cp_add_to_cart_button' );

// Before and After components: Fire layout-specific actions.
add_action( 'woocommerce_composite_before_components', 'wc_cp_before_components', 10, 2 );
add_action( 'woocommerce_composite_after_components', 'wc_cp_after_components', 10, 2 );

// After components: Status Messages.
add_action( 'woocommerce_composite_after_components', 'wc_cp_status', 10, 2 );

// After components: Display no-JS notice regardless of layout.
add_action( 'woocommerce_composite_after_components', 'wc_cp_no_js_msg', 15, 2 );

/*----------------------------------------*/
/*  Single product summary widget hooks.  */
/*----------------------------------------*/

// Validation message for fixed-display widgets.
add_action( 'woocommerce_composite_summary_widget_content', 'wc_cp_summary_widget_message_fixed', 0, 3 );

// Selections wrapper start.
add_action( 'woocommerce_composite_summary_widget_content', 'wc_cp_summary_widget_details_wrapper_start', 5, 3 );

// Content.
add_action( 'woocommerce_composite_summary_widget_content', 'wc_cp_summary_widget_content', 10, 3 );

// Selections wrapper end.
add_action( 'woocommerce_composite_summary_widget_content', 'wc_cp_summary_widget_details_wrapper_end', 15, 3 );

// UI wrapper start.
add_action( 'woocommerce_composite_summary_widget_content', 'wc_cp_summary_widget_ui_wrapper_start', 15, 3 );

// Price.
add_action( 'woocommerce_composite_summary_widget_content', 'wc_cp_summary_widget_price', 20, 2 );

// Validation message for default-display widgets.
add_action( 'woocommerce_composite_summary_widget_content', 'wc_cp_summary_widget_message_default', 30, 3 );

// Availability.
add_action( 'woocommerce_composite_summary_widget_content', 'wc_cp_summary_widget_availability', 40, 2 );

// Button.
add_action( 'woocommerce_composite_summary_widget_content', 'wc_cp_summary_widget_button', 50, 2 );

// UI wrapper end.
add_action( 'woocommerce_composite_summary_widget_content', 'wc_cp_summary_widget_ui_wrapper_end', 100, 3 );

/*---------------------------*/
/*  Stacked layout hooks.    */
/*---------------------------*/

/*
 * After components:
 */

// Add-to-cart section.
add_action( 'woocommerce_composite_after_components_single', 'wc_cp_add_to_cart_section', 10, 2 );

/*
 * Component options:
 */

// Sorting and filtering.
add_action( 'woocommerce_composite_component_selections_single', 'wc_cp_component_options_sorting', 10, 2 );
add_action( 'woocommerce_composite_component_selections_single', 'wc_cp_component_options_filtering', 20, 2 );

// Component options title.
add_action( 'woocommerce_composite_component_selections_single', 'wc_cp_component_options_title', 30, 2 );

// Top Pagination.
add_action( 'woocommerce_composite_component_selections_single', 'wc_cp_component_options_pagination_top', 39, 2 );

// Component options: Dropdowns / Thumbnails / ...
add_action( 'woocommerce_composite_component_selections_single', 'wc_cp_component_options', 40, 2 );

// Pagination.
add_action( 'woocommerce_composite_component_selections_single', 'wc_cp_component_options_pagination_bottom', 41, 2 );

// Current selection in single-page mode.
add_action( 'woocommerce_composite_component_selections_single', 'wc_cp_component_selection', 50, 2 );

/*-----------------------------*/
/*  Progressive layout hooks.  */
/*-----------------------------*/

/*
 * After components:
 */

// Add-to-cart section.
add_action( 'woocommerce_composite_after_components_progressive', 'wc_cp_add_to_cart_section', 10, 2 );

// Previous / Next buttons.
add_action( 'woocommerce_composite_after_components_progressive', 'wc_cp_navigation_bottom', 15, 2 );

/*
 * Component options:
 */

// Current selections block wrapper in progressive mode -- start.
add_action( 'woocommerce_composite_component_selections_progressive', 'wc_cp_component_options_progressive_start', 0, 2 );

// Sorting and filtering.
add_action( 'woocommerce_composite_component_selections_progressive', 'wc_cp_component_options_sorting', 10, 2 );
add_action( 'woocommerce_composite_component_selections_progressive', 'wc_cp_component_options_filtering', 20, 2 );

// Component options title.
add_action( 'woocommerce_composite_component_selections_progressive', 'wc_cp_component_options_title', 30, 2 );

// Top Pagination.
add_action( 'woocommerce_composite_component_selections_progressive', 'wc_cp_component_options_pagination_top', 39, 2 );

// Dropdowns / Thumbnails / ...
add_action( 'woocommerce_composite_component_selections_progressive', 'wc_cp_component_options', 40, 2 );

// Bottom Pagination.
add_action( 'woocommerce_composite_component_selections_progressive', 'wc_cp_component_options_pagination_bottom', 41, 2 );

// Current selections block wrapper in progressive mode -- end.
add_action( 'woocommerce_composite_component_selections_progressive', 'wc_cp_component_options_progressive_end', 45, 2 );

// Current selection in single-page mode.
add_action( 'woocommerce_composite_component_selections_progressive', 'wc_cp_component_selection', 50, 2 );

// Component notices container.
add_action( 'woocommerce_composite_component_selections_progressive', 'wc_cp_component_selection_message_progressive', 60, 2 );

/*-------------------------------------------*/
/*  Stepped and Componentized layout hooks.  */
/*-------------------------------------------*/

/*
 * Before components:
 */

// Auto-scroll target at top of page when transitioning to a new component.

/*
 * Note:
 *
 * When component options loaded via ajax are appended instead of paginated (@see WC_Component::paginate_options),
 * the selected product details are relocated below the selected product thumbnail row.
 *
 * In this case, when transitioning back to a component with relocated selected product details, the relocated container will be moved back to the original position
 * and the viewport will auto-scroll to the target defined here.
 *
 * Alternatively, the 'woocommerce_composite_front_end_params' filter ('relocated_content_reset_on_return' key) can be used to prevent resetting the position of the relocated container.
 * In this case, the viewport will always auto-scroll to the relocated container.
 */
add_action( 'woocommerce_composite_before_components_paged', 'wc_cp_component_transition_scroll_target', 10, 2 );

// Component blocker div (blocks input during transitions).
add_action( 'woocommerce_composite_before_components_paged', 'wc_cp_component_blocker', 10, 2 );

// Composite pagination (anchors to components at top of page).
add_action( 'woocommerce_composite_before_components_paged', 'wc_cp_pagination', 15, 2 );

// Previous / Next buttons added on top of page when component options are viewed as thumbnails.
add_action( 'woocommerce_composite_before_components_paged', 'wc_cp_navigation_top', 20, 2 );

// Previous / Next buttons relocated by JS into the current selection details when component options are viewed as thumbnails and appended.
add_action( 'woocommerce_composite_before_components_paged', 'wc_cp_navigation_movable', 20, 2 );

/*
 * After components:
 */

// Add-to-cart section.

/*
 * Note:
 *
 * If 'wc_cp_add_to_cart_section' is moved to a later priority, the add-to-cart and summary section will no longer be part of the step-based process
 * In this case, use 'wc_cp_final_step_scroll_target' to define the auto-scroll target after clicking on the "Next" button of the final component, like so:
 * add_action( 'woocommerce_composite_after_components_paged', 'wc_cp_final_step_scroll_target', 9, 2 );
 */
add_action( 'woocommerce_composite_after_components_paged', 'wc_cp_add_to_cart_section', 10, 2 );

// Previous / Next buttons at bottom of page.
add_action( 'woocommerce_composite_after_components_paged', 'wc_cp_navigation_bottom', 15, 2 );

/*
 * Component options:
 */

// Component details scroll target.
add_action( 'woocommerce_composite_component_selections_paged', 'wc_cp_component_selection_scroll_target_paged_top', -20, 2 );

// Component notices container (thumbnails).
add_action( 'woocommerce_composite_component_selections_paged', 'wc_cp_component_selection_message_paged_top', -10, 2 );

// Component options: Current selection details in paged mode - before thumbnails.
add_action( 'woocommerce_composite_component_selections_paged', 'wc_cp_component_selection_paged_top', 0, 2 );

// Component options: Sorting and filtering.
add_action( 'woocommerce_composite_component_selections_paged', 'wc_cp_component_options_sorting', 10, 2 );
add_action( 'woocommerce_composite_component_selections_paged', 'wc_cp_component_options_filtering', 20, 2 );

// Component options title.
add_action( 'woocommerce_composite_component_selections_paged', 'wc_cp_component_options_title', 30, 2 );

// Component options: Top Pagination.
add_action( 'woocommerce_composite_component_selections_paged', 'wc_cp_component_options_pagination_top', 39, 2 );

// Component options: Dropdowns / Thumbnails / ...
add_action( 'woocommerce_composite_component_selections_paged', 'wc_cp_component_options', 40, 2 );

// Component options: Bottom Pagination.
add_action( 'woocommerce_composite_component_selections_paged', 'wc_cp_component_options_pagination_bottom', 41, 2 );

// Component options: Current selection in paged mode - after dropdown.
add_action( 'woocommerce_composite_component_selections_paged', 'wc_cp_component_selection_paged_bottom', 50, 2 );

// Component notices container (dropdowns and radios).
add_action( 'woocommerce_composite_component_selections_paged', 'wc_cp_component_selection_message_paged_bottom', 60, 2 );

// Summary added inside the composite-add-to-cart.php template.
add_action( 'woocommerce_before_add_to_cart_button', 'wc_cp_before_add_to_cart_button', 5 );

/*--------------------------------------*/
/*  Component options template hooks.   */
/*--------------------------------------*/

add_action( 'woocommerce_composite_component_options_dropdowns', 'wc_cp_component_options_dropdown', 10, 2 );
add_action( 'woocommerce_composite_component_options_thumbnails', 'wc_cp_component_options_thumbnails', 10, 2 );
add_action( 'woocommerce_composite_component_options_radios', 'wc_cp_component_options_radios', 10, 2 );

/*--------------------------------------*/
/*  Composited product template hooks.  */
/*--------------------------------------*/

// Composited product title.
add_action( 'woocommerce_composited_product_single', 'wc_cp_composited_product_title', 5 );

// Composited product details wrapper open.
add_action( 'woocommerce_composited_product_single', 'wc_cp_composited_product_wrapper_open', 10 );

// Composited product thumbnail.
add_action( 'woocommerce_composited_product_single', 'wc_cp_composited_product_thumbnail', 20 );

// Composited product details.
add_action( 'woocommerce_composited_product_single', 'wc_cp_composited_product_single', 30 );

// Composited product details wrapper close.
add_action( 'woocommerce_composited_product_single', 'wc_cp_composited_product_wrapper_close', 100 );

// Composited product - Simple product template data.
add_action( 'woocommerce_composited_product_simple', 'wc_cp_composited_product_simple', 10 );

// Composited product - Variable product template data.
add_action( 'woocommerce_composited_product_variable', 'wc_cp_composited_product_variable', 10 );

// Composited product - Excerpt.
add_action( 'woocommerce_composited_product_details', 'wc_cp_composited_product_excerpt', 10, 3 );

// Composited Simple product - Price.
add_action( 'woocommerce_composited_product_add_to_cart', 'wc_cp_composited_product_price', 8, 3 );

// Composited Variable product - Selected variation.
add_action( 'woocommerce_composited_single_variation', 'wc_cp_composited_single_variation', 10, 3 );
add_action( 'woocommerce_composited_single_variation', 'wc_cp_composited_single_variation_template', 20, 3 );
