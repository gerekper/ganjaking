<?php
/**
 * Composite Product Template Functions
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*---------------------------------------------------------*/
/*                                                         */
/*  Composite products single product template functions.  */
/*                                                         */
/*---------------------------------------------------------*/

/**
 * Add-to-cart template for composite products. Form location: After summary.
 *
 * @since  3.13.0
 */
function wc_cp_add_to_cart_after_summary() {

	global $product;

	if ( is_composite_product() ) {
		if ( 'after_summary' === $product->get_add_to_cart_form_location() ) {
			$classes = implode( ' ', apply_filters( 'woocommerce_composite_form_wrapper_classes', array( 'summary-add-to-cart-form', 'summary-add-to-cart-form-composite' ), $product ) );
			?><div class="<?php echo esc_attr( $classes );?>"><?php
				do_action( 'woocommerce_composite_add_to_cart' );
			?></div><?php
		}
	}
}

/**
 * Add-to-cart template for composite products. Form location: Default.
 */
function wc_cp_add_to_cart() {

	global $product;

	if ( doing_action( 'woocommerce_single_product_summary' ) ) {
		if ( 'after_summary' === $product->get_add_to_cart_form_location() ) {
			return;
		}
	}

	// Enqueue scripts.
	wp_enqueue_script( 'wc-add-to-cart-composite' );

	// Enqueue styles.
	wp_enqueue_style( 'wc-composite-single-css' );

	// Load NYP scripts.
	if ( function_exists( 'WC_Name_Your_Price' ) ) {
		WC_Name_Your_Price()->display->nyp_scripts();
	}

	// Enqueue Bundle styles.
	if ( class_exists( 'WC_Bundles' ) ) {
		wp_enqueue_style( 'wc-bundle-css' );
	}

	$navigation_style           = $product->get_composite_layout_style();
	$navigation_style_variation = $product->get_composite_layout_style_variation();
	$components                 = $product->get_components();

	if ( ! empty( $components ) ) {
		wc_get_template( 'single-product/add-to-cart/composite.php', array(
			'navigation_style' => $navigation_style,
			'classes'          => implode( ' ', apply_filters( 'woocommerce_composite_form_classes', array( $navigation_style, $navigation_style_variation ), $product ) ),
			'components'       => $components,
			'product'          => $product
		), '', WC_CP()->plugin_path() . '/templates/' );
	}
}

/**
 * Add-to-cart button and quantity template for composite products.
 */
function wc_cp_add_to_cart_button() {

	if ( isset( $_GET[ 'update-composite' ] ) ) {
		$cart_id = wc_clean( $_GET[ 'update-composite' ] );
		echo '<input type="hidden" name="update-composite" value="' . $cart_id . '" />';
	}

	wc_get_template( 'single-product/add-to-cart/composite-quantity-input.php', array(), false, WC_CP()->plugin_path() . '/templates/' );
	wc_get_template( 'single-product/add-to-cart/composite-button.php', array(), false, WC_CP()->plugin_path() . '/templates/' );
}


/*-----------------------------------------------------------------------------*/
/*                                                                             */
/*  Composite products single product summary widget functions.                */
/*                                                                             */
/*-----------------------------------------------------------------------------*/

/**
 * Summary widget content.
 *
 * @since  3.6.0
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $composite
 * @param  array                 $widget_options
 */
function wc_cp_summary_widget_content( $components, $composite, $widget_options ) {

	?><div class="widget_composite_summary_elements composite_summary" data-summary_columns="<?php echo esc_attr( $widget_options[ 'columns' ] ); ?>"><?php
		wc_get_template( 'single-product/composite-summary-content.php', array(
			'summary_columns'  => $widget_options[ 'columns' ],
			'summary_elements' => count( $components ),
			'components'       => $components,
			'product'          => $composite
		), '', WC_CP()->plugin_path() . '/templates/' );
	?></div><?php
}

/**
 * Summary widget wrapper start.
 *
 * @since  3.12.0
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $composite
 * @param  array                 $widget_options
 */
function wc_cp_summary_widget_details_wrapper_start( $components, $composite, $widget_options ) {

	if ( 'fixed' === $widget_options[ 'display' ] ) {
		?><div class="widget_composite_summary_details_wrapper">
			<span role="button" class="summary_carousel_button prev disabled inactive"><button class="aria_button" aria-label="<?php echo __( 'View previous steps', 'woocommerce-composite-products' ); ?>"></button></span>
			<div class="widget_composite_summary_elements_wrapper"><?php
	}
}

/**
 * Summary widget wrapper end.
 *
 * @since  3.12.0
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $composite
 * @param  array                 $widget_options
 */
function wc_cp_summary_widget_details_wrapper_end( $components, $composite, $widget_options ) {

	if ( 'fixed' === $widget_options[ 'display' ] ) {
			?></div>
			<span role="button" class="summary_carousel_button next disabled inactive"><button class="aria_button" aria-label="<?php echo __( 'View next steps', 'woocommerce-composite-products' ); ?>"></button></span>
		</div><?php
	}
}

/**
 * Summary widget UI wrapper start.
 *
 * @since  3.12.0
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $composite
 * @param  array                 $widget_options
 */
function wc_cp_summary_widget_ui_wrapper_start( $components, $composite, $widget_options ) {

	if ( 'fixed' === $widget_options[ 'display' ] ) {
		?><div class="widget_composite_summary_ui_wrapper"><?php
	}
}

/**
 * Summary widget UI wrapper end.
 *
 * @since  3.12.0
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $composite
 * @param  array                 $widget_options
 */
function wc_cp_summary_widget_ui_wrapper_end( $components, $composite, $widget_options ) {

	if ( 'fixed' === $widget_options[ 'display' ] ) {
		?></div><?php
	}
}

/**
 * Summary widget price. Empty element to be populated by the script.
 *
 * @since  3.6.0
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $composite
 */
function wc_cp_summary_widget_price( $components, $composite ) {

	?><div class="widget_composite_summary_price">
		<div class="composite_price"></div>
	</div><?php
}

/**
 * Summary widget validation message. Empty element to be populated by the script.
 *
 * @since  3.6.0
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $composite
 */
function wc_cp_summary_widget_message( $components, $composite ) {

	?><div class="widget_composite_summary_error">
		<div class="composite_message" style="display:none;"><ul class="msg woocommerce-info"></ul></div>
	</div><?php
}

/**
 * Summary widget product availability.
 *
 * @since  3.6.0
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $composite
 */
function wc_cp_summary_widget_availability( $components, $product ) {

	?><div class="widget_composite_summary_availability">
		<div class="composite_availability"><?php
			echo wc_get_stock_html( $product );
		?></div>
	</div><?php
}

/**
 * Summary widget add-to-cart button.
 *
 * @since  3.6.0
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $composite
 */
function wc_cp_summary_widget_button( $components, $composite ) {

	?><div class="widget_composite_summary_button">
		<div class="composite_button">
			<?php do_action( 'woocommerce_composite_add_to_cart_button' ); ?>
		</div>
	</div><?php
}

/**
 * Summary widget validation message. Empty element to be populated by the script.
 *
 * @since  4.0.0
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $composite
 * @param  array                 $widget_options
 */
function wc_cp_summary_widget_message_default( $components, $composite, $widget_options ) {

	if ( 'default' === $widget_options[ 'display' ] ) {
		wc_cp_summary_widget_message( $components, $composite );
	}
}

/**
 * Summary widget validation message. Empty element to be populated by the script.
 *
 * @since  4.0.0
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $composite
 * @param  array                 $widget_options
 */
function wc_cp_summary_widget_message_fixed( $components, $composite, $widget_options ) {

	if ( 'fixed' === $widget_options[ 'display' ] ) {
		wc_cp_summary_widget_message( $components, $composite );
	}
}

/*-----------------------------------------------------------------------------*/
/*                                                                             */
/*  Composite products single product template functions - Component Options.  */
/*                                                                             */
/*-----------------------------------------------------------------------------*/

/**
 * Show current selection scroll target in paged modes.
 *
 * @since  4.0.0
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_selection_scroll_target_paged_top( $component_id, $product ) {

	$options_style = $product->get_component( $component_id )->get_options_style();

	if ( 'thumbnails' === $options_style ) {
		?><div class="scroll_show_component_details"></div><?php
	}
}

/**
 * Component selection notices container displayed in the 'component_selections' container (paged layout, thumbnails).
 *
 * @since  4.0.0
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_selection_message_paged_top( $component_id, $product ) {

	$options_style = $product->get_component( $component_id )->get_options_style();

	if ( 'thumbnails' === $options_style ) {
		$classes = array( 'top' );
		wc_cp_component_message( $classes );
	}
}

/**
 * Show current selection details in paged modes -- added before component options when viewed as thumbnails.
 *
 * @since  4.0.0
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_selection_paged_top( $component_id, $product ) {

	$options_style = $product->get_component( $component_id )->get_options_style();

	if ( 'thumbnails' === $options_style ) {
		wc_cp_component_selection( $component_id, $product );
	}
}

/**
 * In progressive mode, wrap component options & sorting/filtering controls in a blockable div.
 *
 * @since  4.0.0
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_options_progressive_start( $component_id, $product ) {

	?><div class="component_selections_inner">
		<div class="block_component_selections_inner"></div><?php
}

/**
 * Add sorting input.
 *
 * @since  4.0.0
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_options_sorting( $component_id, $product ) {

	$component_ordering_options = $product->get_component( $component_id )->get_sorting_options();

	if ( $component_ordering_options ) {

		// Component Options sorting template.
		wc_get_template( 'single-product/component-options-orderby.php', array(
			'product'                    => $product,
			'component_id'               => $component_id,
			'component_ordering_options' => $component_ordering_options,
			'orderby'                    => $product->get_component( $component_id )->get_default_sorting_order()
		), '', WC_CP()->plugin_path() . '/templates/' );
	}
}

/**
 * Add attribute filters.
 *
 * @since  4.0.0
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_options_filtering( $component_id, $product ) {

	$component_filtering_options = $product->get_component( $component_id )->get_filtering_options();

	if ( $component_filtering_options ) {

		// Component Options filtering template.
		wc_get_template( 'single-product/component-options-filters.php', array(
			'product'                     => $product,
			'component_id'                => $component_id,
			'component_filtering_options' => $component_filtering_options
		), '', WC_CP()->plugin_path() . '/templates/' );
	}
}

/**
 * Show component options title.
 *
 * @since  4.0.0
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_options_title( $component_id, $product ) {

	wc_get_template( 'single-product/component-options-title.php', array(), '', WC_CP()->plugin_path() . '/templates/' );
}

/**
 * Show component options top pagination.
 *
 * @since  4.0.0
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_options_pagination_top( $component_id, $product ) {

	$component = $product->get_component( $component_id );

	if ( $component->paginate_options() ) {
		// The view must be initialized for pagination templates to work.
		$component->view->initialize();
		wc_cp_component_options_pagination( $component_id, $product, array( 'top' ) );
	}
}

/**
 * Show component options.
 *
 * @since  4.0.0
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_options( $component_id, $product ) {

	$component = $product->get_component( $component_id );

	// Component Options template.
	wc_get_template( 'single-product/component-options.php', array(
		'product'                => $product,
		'component_id'           => $component_id,
		'component'              => $component,
		'options_style'          => $component->get_options_style(),
		'component_options_data' => $component->view->get_options_data( array( 'lazy_load' => $component->is_lazy_loaded() ) )
	), '', WC_CP()->plugin_path() . '/templates/' );
}

/**
 * Show component options bottom pagination.
 *
 * @since  4.0.0
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_options_pagination_bottom( $component_id, $product ) {
	wc_cp_component_options_pagination( $component_id, $product, array( 'bottom' ) );
}

/**
 * In progressive mode, wrap component options & sorting/filtering controls in a blockable div.
 *
 * @since  4.0.0
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_options_progressive_end( $component_id, $product ) {

	?></div><?php
}

/**
 * Show current selection details in non-paged modes.
 *
 * @since  4.0.0
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 * @return void
 */
function wc_cp_component_selection( $component_id, $product ) {

	$selected_option  = $product->get_component( $component_id )->view->get_selected_option();
	$navigation_style = $product->get_composite_layout_style();

	?><div class="component_content" data-product_id="<?php echo $component_id; ?>">
		<div class="component_summary cp_clearfix"><?php

			/**
			 * Action 'woocommerce_composite_component_before_summary_content_{$navigation_style}'.
			 *
			 * @since  3.4.0
			 *
			 * @param  string                $component_id
			 * @param  WC_Product_Composite  $product
			 */
			do_action( 'woocommerce_composite_component_before_summary_content_' . $navigation_style, $component_id, $product );

			// View container.
			?><div class="product content summary_content <?php echo $selected_option ? 'populated' : ''; ?>"></div>
		</div>
	</div><?php
}

/**
 * Show current selection details in paged modes -- added after component options when viewed as drop-downs/radios.
 *
 * @since  4.0.0
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_selection_paged_bottom( $component_id, $product ) {

	$options_style = $product->get_component( $component_id )->get_options_style();

	if ( 'dropdowns' === $options_style || 'radios' === $options_style ) {
		wc_cp_component_selection( $component_id, $product );
	}
}

/**
 * Component selection notices container displayed in the 'component_selections' container (paged layout, dropdowns/radios).
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_selection_message_paged_bottom( $component_id, $product ) {

	$options_style = $product->get_component( $component_id )->get_options_style();

	if ( 'thumbnails' !== $options_style ) {
		$classes = array( 'bottom' );
		wc_cp_component_message( $classes );
	}
}

/**
 * Show component options pagination.
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 * @param  array                 $classes
 */
function wc_cp_component_options_pagination( $component_id, $product, $classes = array() ) {

	$component = $product->get_component( $component_id );

	if ( WC_CP_Component::options_style_supports( $component->get_options_style(), 'pagination' ) && ! $component->is_static() ) {

		if ( false === $component->paginate_options() ) {
			$classes[] = 'component_options_append';
		}

		// Component Options Pagination template.
		wc_get_template( 'single-product/component-options-pagination.php', array(
			'product'         => $product,
			'component_id'    => $component_id,
			'component'       => $component,
			'pagination_data' => $component->view->get_pagination_data(),
			'has_pages'       => $component->view->has_pages(),
			'append_options'  => false === $component->paginate_options(),
			'classes'         => implode( ' ', $classes )
		), '', WC_CP()->plugin_path() . '/templates/' );
	}
}

/**
 * Show component options as a dropdown.
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_options_dropdown( $component_id, $product ) {

	$component = $product->get_component( $component_id );

	// Dropdown template.
	wc_get_template( 'single-product/component-options-dropdown.php', array(
		'product'       => $product,
		'component_id'  => $component_id,
		'component'     => $component,
		'hide_dropdown' => 'dropdowns' !== $component->get_options_style()
	), '', WC_CP()->plugin_path() . '/templates/' );
}

/**
 * Show component options as thumbnails.
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_options_thumbnails( $component_id, $product ) {

	$component = $product->get_component( $component_id );

	// Thumbnails template.
	wc_get_template( 'single-product/component-options-thumbnails.php', array(
		'product'           => $product,
		'component_id'      => $component_id,
		'component'         => $component,
		'component_data'    => $component->get_data(),
		'quantity_min'      => $component->get_quantity( 'min' ),
		'quantity_max'      => $component->get_quantity( 'max' ),
		'thumbnail_columns' => $component->get_columns(),
		'component_options' => $component->view->get_options(),
		'selected_option'   => $component->view->get_selected_option(),
	), '', WC_CP()->plugin_path() . '/templates/' );

	wc_cp_component_options_dropdown( $component_id, $product );
}

/**
 * Show component option as radio buttons.
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_options_radios( $component_id, $product ) {

	$component = $product->get_component( $component_id );

	// Radio buttons template.
	wc_get_template( 'single-product/component-options-radio-buttons.php', array(
		'product'           => $product,
		'component_id'      => $component_id,
		'component'         => $component,
		'component_data'    => $component->get_data(),
		'quantity_min'      => $component->get_quantity( 'min' ),
		'quantity_max'      => $component->get_quantity( 'max' ),
		'component_options' => $component->view->get_options(),
		'selected_option'   => $component->view->get_selected_option()
	), '', WC_CP()->plugin_path() . '/templates/' );

	wc_cp_component_options_dropdown( $component_id, $product );
}

/*----------------------------------------------------------------------------------*/
/*                                                                                  */
/*  Composite products single product template functions - Composite.               */
/*                                                                                  */
/*----------------------------------------------------------------------------------*/

/**
 * Add Composite Summary on the 'woocommerce_before_add_to_cart_button' hook.
 */
function wc_cp_before_add_to_cart_button() {

	global $product;

	if ( 'composite' === $product->get_type() ) {
		wc_cp_summary( $product->get_components(), $product );
	}
}

/**
 * Add Review/Summary with current configuration details.
 * The Summary template must be loaded if the summary widget is active.
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $product
 */
function wc_cp_summary( $components, $product ) {

	$navigation_style           = $product->get_composite_layout_style();
	$navigation_style_variation = $product->get_composite_layout_style_variation();

	/**
	 * Filter to determine whether the summary will be displayed.
	 *
	 * @param  boolean               $show_summary
	 * @param  string                $layout_id
	 * @param  string                $layout_variation_id
	 * @param  WC_Product_Composite  $product
	 */
	$show_summary = apply_filters( 'woocommerce_composite_summary_display', 'paged' === $navigation_style, $navigation_style, $navigation_style_variation, $product );

	if ( $show_summary ) {
		wc_get_template( 'single-product/composite-summary.php', array(
			'product'    => $product,
			'product_id' => $product->get_id(),
			'components' => $components,
			'hidden'     => false,
		), '', WC_CP()->plugin_path() . '/templates/' );
	}
}

/**
 * Hook layout/style-specific content on the 'woocommerce_composite_before_components' action.
 */
function wc_cp_before_components( $components, $product ) {

	$layout = $product->get_composite_layout_style();

	/**
	 * Action 'woocommerce_composite_before_components_paged':
	 *
	 * @since  3.4.0
	 *
	 * @param  array                 $components
	 * @param  WC_Product_Composite  $product
	 *
	 * @hooked wc_cp_component_transition_scroll_target    - 10
	 * @hooked wc_cp_pagination                            - 15
	 * @hooked wc_cp_navigation_top                        - 20
	 * @hooked wc_cp_navigation_movable                    - 20
	 */
	do_action( 'woocommerce_composite_before_components_' . $layout, $components, $product );
}

/**
 * Hook layout/style-specific content on the 'woocommerce_composite_after_components' action.
 *
 * @return void
 */
function wc_cp_after_components( $components, $product ) {

	$layout = $product->get_composite_layout_style();

	/**
	 * Action 'woocommerce_composite_after_components_{$layout}':
	 *
	 * @since  3.4.0
	 *
	 * @param  array                 $components
	 * @param  WC_Product_Composite  $product
	 *
	 * Action 'woocommerce_composite_after_components_single':
	 *
	 * @hooked wc_cp_add_to_cart_section - 10
	 *
	 *
	 * Action 'woocommerce_composite_after_components_progressive':
	 *
	 * @hooked wc_cp_add_to_cart_section - 10
	 * @hooked wc_cp_navigation_bottom   - 15
	 *
	 *
	 * Action 'woocommerce_composite_after_components_paged':
	 *
	 * @hooked wc_cp_add_to_cart_section                  - 10
	 * @hooked wc_cp_navigation_bottom                    - 20
	 */
	do_action( 'woocommerce_composite_after_components_' . $layout, $components, $product );
}

/**
 * Loading status message.
 */
function wc_cp_status() {
	?><div class="composite_status">
		<div class="wrapper"></div>
	</div><?php
}

/**
 * Add-to-cart section.
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $product
 */
function wc_cp_add_to_cart_section( $components, $product ) {

	wc_get_template( 'single-product/composite-add-to-cart.php', array(
		'product'                    => $product,
		'components'                 => $components,
		'product_id'                 => $product->get_id(),
		'availability_html'          => wc_get_stock_html( $product ),
		'navigation_style'           => $product->get_composite_layout_style(),
		'navigation_style_variation' => $product->get_composite_layout_style_variation(),
	), '', WC_CP()->plugin_path() . '/templates/' );

}

/**
 * Add previous/next navigation buttons in paged mode -- added on bottom of page under the component options section.
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $product
 */
function wc_cp_navigation_bottom( $components, $product ) {

	$position                   = 'bottom';
	$navigation_style           = $product->get_composite_layout_style();
	$navigation_style_variation = $product->get_composite_layout_style_variation();

	$classes = array( $position, $navigation_style, $navigation_style_variation );

	wc_cp_navigation( $classes, $product );
}

/**
 * Add previous/next navigation buttons in paged mode -- added on top of page under the composite pagination section when component options are viewed as thumbnails.
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $product
 */
function wc_cp_navigation_top( $components, $product ) {

	$position                   = 'top';
	$navigation_style           = 'paged';
	$navigation_style_variation = $product->get_composite_layout_style_variation();

	$classes = array( $position, $navigation_style, $navigation_style_variation );
	wc_cp_navigation( $classes, $product );
}

/**
 * Add previous/next navigation buttons in multi-page mode -- added on top of page under the composite pagination section.
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $product
 */
function wc_cp_navigation_movable( $components, $product ) {

	$position                   = 'movable hidden';
	$navigation_style           = 'paged';
	$navigation_style_variation = $product->get_composite_layout_style_variation();

	$classes = array( $position, $navigation_style, $navigation_style_variation );
	wc_cp_navigation( $classes, $product );
}

/**
 * Add previous/next navigation buttons in multi-page mode.
 *
 * @param  array                 $classes
 * @param  WC_Product_Composite  $product
 */
function wc_cp_navigation( $classes, $product ) {

	wc_get_template( 'single-product/composite-navigation.php', array(
		'product'                    => $product,
		'product_id'                 => $product->get_id(),
		'navigation_style'           => $product->get_composite_layout_style(),
		'navigation_style_variation' => $product->get_composite_layout_style_variation(),
		'classes'                    => implode( ' ', $classes )
	), '', WC_CP()->plugin_path() . '/templates/' );
}

/**
 * Component selection notices container displayed in the component_selections container (progressive layout).
 *
 * @param  string                $component_id
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_selection_message_progressive( $component_id, $product ) {

	$classes = array( 'bottom' );
	wc_cp_component_message( $classes );
}

/**
 * Component selection notices container displayed in progressive/paged layouts.
 *
 * @param  array  $classes
 */
function wc_cp_component_message( $classes ) {

	?><div class="component_message <?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="display:none"></div><?php
}

/**
 * When changing between components in paged mode, the viewport will scroll to this div if it's not visible.
 * Adding the 'scroll_bottom' class to the element will scroll the bottom of the viewport to the target.
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_transition_scroll_target( $components, $product ) {

	?><div class="scroll_show_component"></div><?php
}

/**
 * Div for blocking form content during transitions.
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $product
 */
function wc_cp_component_blocker( $components, $product ) {

	?><div class="form_input_blocker"></div><?php
}

/**
 * Adds composite pagination in paged mode.
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $product
 */
function wc_cp_pagination( $components, $product ) {

	$layout_variation = $product->get_composite_layout_style_variation();

	if ( 'componentized' !== $layout_variation ) {

		wc_get_template( 'single-product/composite-pagination.php', array(
			'product'    => $product,
			'product_id' => $product->get_id(),
			'components' => $components
		), '', WC_CP()->plugin_path() . '/templates/' );

	}
}

/**
 * When selecting the final step in paged mode, the viewport will scroll to this div.
 * Adding the 'scroll_bottom' class to the element will scroll the bottom of the viewport to the target.
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $product
 */
function wc_cp_final_step_scroll_target( $components, $product ) {

	$navigation_style = $product->get_composite_layout_style();

	if ( 'paged' === $navigation_style ) {

		?><div class="scroll_final_step"></div><?php
	}
}

/**
 * No js notice.
 *
 * @param  array                 $components
 * @param  WC_Product_Composite  $product
 */
function wc_cp_no_js_msg( $components, $product ) {

	?><p class="cp-no-js-msg">
		<span id="cp-no-js-msg">
			<script type="text/javascript">
				var el = document.getElementById( 'cp-no-js-msg' );
				el.innerHTML = "<?php _e( 'Loading...', 'woocommerce-composite-products' ); ?>";
			</script>
		</span>
		<noscript>
			<?php _e( 'JavaScript must be supported by your browser and needs to be enabled in order to view this page.', 'woocommerce-composite-products' ); ?>
		</noscript>
	</p><?php
}

/*--------------------------------------------------------*/
/*                                                        */
/*  Component selection template functions.               */
/*                                                        */
/*--------------------------------------------------------*/

/**
 * Composited product title template.
 *
 * @param  WC_CP_Product  $component_option
 */
function wc_cp_composited_product_title( $component_option ) {

	$component = $component_option->get_component();
	$is_hidden = $component->hide_selected_option_title();

	?>
	<div class="composited_product_title_wrapper" data-show_title="<?php echo ! $is_hidden ? 'yes' : 'no'; ?>"></div>
	<?php
}

/**
 * Composited product wrapper open.
 *
 * @since  4.0.0
 *
 * @param  WC_CP_Product  $component_option
 */
function wc_cp_composited_product_wrapper_open( $component_option ) {
	echo '<div class="composited_product_details_wrapper">';
}

/**
 * Composited product thumbnail template.
 *
 * @param  WC_CP_Product  $component_option
 */
function wc_cp_composited_product_thumbnail( $component_option ) {

	$component  = $component_option->get_component();
	$product_id = $component_option->get_product_id();

	if ( ! $component->hide_selected_option_thumbnail() ) {

		/**
		 * 'woocommerce_bundled_product_gallery_classes' filter.
		 *
		 * @param  array          $classes
		 * @param  WC_CP_Product  $component_option
		 */
		$gallery_classes = apply_filters( 'woocommerce_composited_product_gallery_classes', array( 'composited_product_images', 'images' ), $component_option );

		wc_get_template( 'composited-product/image.php', array(
			'product_id'      => $product_id,
			'gallery_classes' => $gallery_classes,
			'image_size'      => $component_option->get_selection_thumbnail_size(),
			'image_rel'       => current_theme_supports( 'wc-product-gallery-lightbox' ) ? 'photoSwipe' : 'prettyPhoto',
			'component'       => $component
		), '', WC_CP()->plugin_path() . '/templates/' );
	}
}

/**
 * Composited product template.
 *
 * @since  4.0.0
 *
 * @param  WC_CP_Product  $component_option
 */
function wc_cp_composited_product_single( $component_option ) {

	/**
	 * Action 'woocommerce_composited_product_{$product_type}'.
	 * Composited product template (type-specific).
	 *
	 * @since  4.0.0
	 *
	 * @param  WC_CP_Product  $component_option
	 *
	 * @hooked wc_cp_composited_product_simple   - 10
	 * @hooked wc_cp_composited_product_variable - 10
	 */
	do_action( 'woocommerce_composited_product_' . $component_option->get_product()->get_type(), $component_option );
}

/**
 * Composited product details wrapper close.
 *
 * @since  4.0.0
 *
 * @param  WC_CP_Product  $component_option
 */
function wc_cp_composited_product_wrapper_close( $component_option ) {
	echo '</div>';
}

/**
 * Composited simple product template.
 *
 * @since  4.0.0
 *
 * @param  WC_CP_Product  $component_option
 */
function wc_cp_composited_product_simple( $component_option ) {

	wc_get_template( 'composited-product/simple-product.php', array(
		'product'           => $component_option->get_product(),
		'component_id'      => $component_option->get_component_id(),
		'quantity_min'      => $component_option->get_quantity_min(),
		'quantity_max'      => $component_option->get_quantity_max( true ),
		'composite_product' => $component_option->get_composite(),
		'component_option'  => $component_option
	), '', WC_CP()->plugin_path() . '/templates/' );
}

/**
 * Composited variable product template.
 *
 * @since  4.0.0
 *
 * @param  WC_CP_Product  $component_option
 */
function wc_cp_composited_product_variable( $component_option ) {

	$component       = $component_option->get_component();
	$variations_data = $component_option->get_variations_data();

	if ( empty( $variations_data ) ) {

		wc_get_template( 'composited-product/invalid-product.php', array(
			'is_static' => $component->is_static()
		), '', WC_CP()->plugin_path() . '/templates/' );

		return;
	}

	$attributes     = $component_option->get_product()->get_variation_attributes();
	$attribute_keys = array_keys( $attributes );

	wc_get_template( 'composited-product/variable-product.php', array(
		'attributes'        => $attributes,
		'attribute_keys'    => $attribute_keys,
		'product'           => $component_option->get_product(),
		'component_id'      => $component_option->get_component_id(),
		'quantity_min'      => $component_option->get_quantity_min(),
		'quantity_max'      => $component_option->get_quantity_max(),
		'composite_product' => $component_option->get_composite(),
		'component_option'  => $component_option
	), '', WC_CP()->plugin_path() . '/templates/' );
}

/**
 * Composited product excerpt.
 *
 * @param  WC_Product            $product
 * @param  string                $component_id
 * @param  WC_Product_Composite  $composite
 */
function wc_cp_composited_product_excerpt( $product, $component_id, $composite ) {

	$product_id = $product->get_id();
	$component  = $composite->get_component( $component_id );

	if ( ! $component->hide_selected_option_description() ) {
		wc_get_template( 'composited-product/excerpt.php', array(
			'product_description' => $product->get_short_description(),
			'product_id'          => $product_id,
			'component_id'        => $component_id,
			'composite'           => $composite,
		), '', WC_CP()->plugin_path() . '/templates/' );
	}
}

/**
 * Composited simple product price.
 *
 * @param  WC_Product            $product
 * @param  string                $component_id
 * @param  WC_Product_Composite  $composite
 */
function wc_cp_composited_product_price( $product, $component_id, $composite ) {

	if ( 'simple' === $product->get_type() ) {

		$product_id         = $product->get_id();
		$component          = $composite->get_component( $component_id );
		$composited_product = $component->get_option( $product_id );

		if ( $composited_product->is_priced_individually() && false === $component->hide_selected_option_price() && '' !== $product->get_price() ) {
			wc_get_template( 'composited-product/price.php', array(
				'product' => $product
			), '', WC_CP()->plugin_path() . '/templates/' );
		}
	}
}

/**
 * Composited single variation details.
 *
 * @since  3.12.5
 *
 * @param  WC_Product_Variable   $product
 * @param  string                $component_id
 * @param  WC_Product_Composite  $composite
 */
function wc_cp_composited_single_variation( $product, $component_id, $composite ) {
	?><div class="woocommerce-variation single_variation"></div><?php
}

/**
 * Composited single variation template.
 *
 * @since  3.12.5
 *
 * @param  WC_Product_Variable   $product
 * @param  string                $component_id
 * @param  WC_Product_Composite  $composite
 */
function wc_cp_composited_single_variation_template( $product, $component_id, $composite ) {

	$product_id         = $product->get_id();
	$composited_product = $composite->get_component( $component_id )->get_option( $product_id );

	$quantity_min = $composited_product->get_quantity_min();
	$quantity_max = $composited_product->get_quantity_max();

	wc_get_template( 'composited-product/variation.php', array(
		'quantity_min'      => $quantity_min,
		'quantity_max'      => $quantity_max,
		'component_id'      => $component_id,
		'product'           => $product,
		'composite_product' => $composite
	), '', WC_CP()->plugin_path() . '/templates/' );
}

/**
 * Variation attribute options for composited products.
 *
 * @since  3.14.3
 *
 * @param  array  $args
 */
function wc_cp_composited_single_variation_attribute_options( $args ) {

	$options        = $args[ 'options' ];
	$attribute_name = $args[ 'attribute' ];
	$product        = $args[ 'product' ];
	$component      = $args[ 'component' ];

	$attribute_keys = array_keys( $args[ 'attributes' ] );
	$component_id   = $component->get_id();
	$selected       = isset( $_REQUEST[ 'wccp_attribute_' . sanitize_title( $attribute_name ) ][ $component_id ] ) ? wc_clean( wp_unslash( $_REQUEST[ 'wccp_attribute_' . sanitize_title( $attribute_name ) ][ $component_id ] ) ) : $product->get_variation_default_attribute( $attribute_name );
	$html           = '';

	ob_start();

	wc_dropdown_variation_attribute_options( array(
		'options'   => $options,
		'attribute' => $attribute_name,
		'name'      => 'wccp_attribute_' . sanitize_title( $attribute_name ) . '[' . $component_id . ']',
		'product'   => $product,
		'selected'  => $selected,
	) );

	$attribute_options = ob_get_clean();

	$html .= $attribute_options;

	if ( end( $attribute_keys ) === $attribute_name ) {
		// Change 'reset_variations_wrapper_fixed' to 'reset_variations_wrapper' if you want the 'Clear' link to slide in/out of view.
		$html .= wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<div class="reset_variations_wrapper_fixed"><a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce-composite-products' ) . '</a></div>' ) );
	}

	return $html;
}
