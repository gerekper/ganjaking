<?php
/**
 * Template Functions
 *
 * Functions for the WooCommerce Mix and Match templating system.
 *
 * @author   Kathy Darling
 * @category Core
 * @package  WooCommerce Mix and Match Products/Functions
 * @since    1.0.0
 * @version  1.9.9
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*--------------------------------------------------------*/
/*  Mix and Match single product template functions     */
/*--------------------------------------------------------*/


/**
 * Add-to-cart template for Product Bundles. Handles the 'Form location > After summary' case.
 *
 * @since  1.3.0
 */
function wc_mnm_template_add_to_cart_after_summary() {

	global $product;

	if ( $product->is_type( 'mix-and-match' ) ) {
		if ( 'after_summary' === $product->get_add_to_cart_form_location() ) {
			$classes = implode( ' ', apply_filters( 'woocommerce_mnm_form_wrapper_classes', array( 'summary-add-to-cart-form', 'summary-add-to-cart-form-mnm' ), $product ) );
			?><div class="<?php echo esc_attr( $classes );?>"><?php
				do_action( 'woocommerce_mix-and-match_add_to_cart' );
?></div><?php
		}
	}
}


/**
 * Add-to-cart template for Mix and Match products.
 *
 * @since  1.3.0
 */
function wc_mnm_template_add_to_cart() {

	global $product;

	if ( doing_action( 'woocommerce_single_product_summary' ) ) {
		if ( 'after_summary' === $product->get_add_to_cart_form_location() ) {
			return;
		}
	}

	// Enqueue scripts and styles - then, initialize js variables.
	wp_enqueue_script( 'wc-add-to-cart-mnm' );
	wp_enqueue_style( 'wc-mnm-frontend' );

	// Load the add to cart template.
	wc_get_template(
		'single-product/add-to-cart/mnm.php',
		array(
			'container'	      => $product,
			'min_container_size'  => $product->get_min_container_size(),
			'max_container_size'  => $product->get_max_container_size(),
			'classes'			=> 'layout_' . $product->get_layout()
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}

/**
 * The child contents loop.
 *
 * @param obj $product WC_Mix_And_Match of parent product
 * @since  1.8.0
 */
function woocommerce_mnm_content_loop( $product ) {

	if( $product->has_available_children() ) {

		/**
		 * 'woocommerce_before_mnm_items' action.
		 *
		 * @param WC_Mix_and_Match $product
		 * @since  1.3.0
		 *
		 * @hooked wc_mnm_template_child_items_wrapper_open - 0
		 */
		do_action( 'woocommerce_before_mnm_items', $product );

		foreach ( $product->get_available_children() as $mnm_item ) {

			/**
			 * 'woocommerce_mnm_item_details' action.
			 *
			 * @param WC_Product $mnm_item
		 	 * @since  1.3.0
			 *
			 * @hooked wc_mnm_template_child_item_details_wrapper_open 	-   0
			 * @hooked wc_mnm_template_child_item_thumbnail_open 		-  10
			 * @hooked wc_mnm_template_child_item_thumbnail 			-  20
			 * @hooked wc_mnm_template_child_item_section_close 		-  30
			 * @hooked wc_mnm_template_child_item_details_open 			-  40
			 * @hooked wc_mnm_template_child_item_title 				-  50
			 * @hooked wc_mnm_template_child_item_attributes			-  60
			 * @hooked wc_mnm_template_child_item_section_close 		-  70
			 * @hooked wc_mnm_template_child_item_quantity_open 		-  80
			 * @hooked wc_mnm_template_child_item_quantity 				-  90
			 * @hooked wc_mnm_template_child_item_section_close			- 100
			 * @hooked wc_mnm_template_child_item_details_wrapper_close	- 110
			 */
			do_action( 'woocommerce_mnm_child_item_details', $mnm_item, $product );
		}

		/**
		 * 'woocommerce_after_mnm_items' action.
		 *
		 * @param  WC_Mix_and_Match  $product
		 * @since  1.3.0
		 *
		 * @hooked wc_mnm_template_child_items_wrapper_close 		- 100
		 */
		do_action( 'woocommerce_after_mnm_items', $product );

	}

}

/**
 * Echo opening markup if necessary.
 *
 * @param obj $product WC_Mix_And_Match of parent product
 * @since  1.3.0
 */
function wc_mnm_template_child_items_wrapper_open( $product ) {

	if( $product->has_available_children() ) { 

		// Get the columns.
		$columns = apply_filters( 'woocommerce_mnm_grid_layout_columns', 3, $product ); 

		// Reset the loop.
		WC_MNM_Core_Compatibility::set_loop_prop( 'loop', 0 );
		WC_MNM_Core_Compatibility::set_loop_prop( 'columns', $columns ); 
		
		/**
		 * Table column headings.
		 *
		 * @param array id => Heading Title
		 * @param obj $product WC_Mix_And_Match of parent product
		 * @since  1.3.1
		 */
		$column_headers = apply_filters( 'woocommerce_mnm_tabular_column_headers', 
			array( 'thumbnail'	=> '&nbsp;',
				   'name' 		=> __( 'Product', 'woocommerce-mix-and-match-products' ),
				   'quantity' 	=> __( 'Quantity', 'woocommerce-mix-and-match-products' )
			),
			$product );

		wc_get_template(
			'single-product/mnm/' . $product->get_layout() . '/mnm-items-wrapper-open.php',
			array( 
				'columns'	  => $columns,
				'column_headers' => $column_headers
			),
			'',
			WC_Mix_and_Match()->plugin_path() . '/templates/'
		);

	}
}

/**
 * Opens the 'mnm_item' child item container.
 *
 * @param obj WC_Product $mnm_item of child item
 * @param obj WC_Mix_and_Match $product the parent container
 * @since  1.3.0
 */
function wc_mnm_template_child_item_details_wrapper_open( $mnm_item, $product ) {

	wc_get_template(
		'single-product/mnm/' . $product->get_layout() . '/mnm-child-item-wrapper-open.php',
		array( 
			'regular_price'	 => $product->is_priced_per_product() ? wc_get_price_to_display( $mnm_item, array( 'price' => $mnm_item->get_regular_price() ) ) : 0,
			'price'		  	 => $product->is_priced_per_product() ? wc_get_price_to_display( $mnm_item, array( 'price' => $mnm_item->get_price() ) ) : 0,
			'mnm_id'		 => $mnm_item->get_id()
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}

/**
 * Open the thumbnail sub-section.
 *
 * @param obj WC_Product $mnm_item the child product
 * @param obj WC_Mix_and_Match $product the parent container
 */
function wc_mnm_template_child_item_thumbnail_open( $mnm_item, $product ) {
	wc_get_template(
		'single-product/mnm/' . $product->get_layout() . '/mnm-child-item-detail-wrapper-open.php',
		array(
			'classes' => 'product-thumbnail',
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);
}


/**
 * Get the child item product thumbnail
 * 
 * @param obj WC_Product $mnm_item the child product
 * @param obj WC_Mix_and_Match $product the parent container
 * @since  1.3.0
 */
function wc_mnm_template_child_item_thumbnail( $mnm_item, $product ) { 

	wc_get_template(
		'single-product/mnm/mnm-product-thumbnail.php',
		array( 
			'mnm_product' => $mnm_item, // For back-compatibility.
			'mnm_item'	  => $mnm_item
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}

/**
 * Add a 'details' sub-section.
 *
 * @param obj WC_Product $mnm_item the child product
 * @param obj WC_Mix_and_Match $product the parent container
 */
function wc_mnm_template_child_item_details_open( $mnm_item, $product ) {
	wc_get_template(
		'single-product/mnm/' . $product->get_layout() . '/mnm-child-item-detail-wrapper-open.php',
		array(
			'classes' => 'product-details',
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);
}

/**
 * Load the child item title template.
 *
 * @param obj WC_Product $mnm_item the child product
 * @param obj WC_Mix_and_Match $product the parent container
 */
function wc_mnm_template_child_item_title( $mnm_item, $product ) {

	wc_get_template(
		'single-product/mnm/mnm-product-title.php',
		array( 
			'mnm_product' => $mnm_item, // For back-compatibility.
			'mnm_item'	  => $mnm_item
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}

/**
 * Get the MNM item product's hidden data attributes
 * 
 * @param obj $mnm_item WC_Product of child item
 * @param obj WC_Mix_and_Match $product the parent container
 */
function wc_mnm_template_child_item_data_details( $mnm_item, $product ) {

	$is_priced_per_product = $product->is_priced_per_product() ? true : false;

	wc_get_template(
		'single-product/mnm/mnm-item-data-attributes.php',
			array( 
				'mnm_item'	  => $mnm_item,
				'mnm_item_id' => $mnm_item->get_id(),
				'regular_price'	 => $is_priced_per_product ? wc_get_price_to_display( $mnm_item, array( 'price' => $mnm_item->get_regular_price() ) ) : 0,
				'price'		  	 => $is_priced_per_product ? wc_get_price_to_display( $mnm_item, array( 'price' => $mnm_item->get_price() ) ) : 0,
				'price_incl_tax' => $is_priced_per_product ? wc_get_price_including_tax( $mnm_item, array( 'price' => $mnm_item->get_price() ) ) : 0,
				'price_excl_tax' => $is_priced_per_product ? wc_get_price_excluding_tax( $mnm_item, array( 'price' => $mnm_item->get_price() ) ) : 0,
				'original_quantity' => apply_filters( 'woocommerce_mnm_quantity_input', '', $mnm_item, $product ),
			),
			'',
			WC_Mix_and_Match()->plugin_path() . '/templates/'
		);

}


/**
 * Get the MNM item product's attributes
 * 
 * @param obj $mnm_item WC_Product of child item
 * @param obj WC_Mix_and_Match $product the parent container
 */
function wc_mnm_template_child_item_attributes( $mnm_item, $product ) {

	if( $mnm_item->is_type( array( 'variation' ) ) ) {
		wc_get_template(
			'single-product/mnm/mnm-product-variation-attributes.php',
			array( 
				'mnm_product' => $mnm_item, // For back-compatibility.
				'mnm_item'	  => $mnm_item
			),
			'',
			WC_Mix_and_Match()->plugin_path() . '/templates/'
		);
	}

}


/**
 * Maybe display individual MNM option price
 * 
 * @param obj $mnm_item WC_Product of child item
 * @param obj WC_Mix_and_Match $product the parent container
 * @since  1.4.0
 */
function wc_mnm_template_child_item_price( $mnm_item, $parent_product ) {
	if( $parent_product->is_priced_per_product() ) {
    	wc_get_template(
			'single-product/mnm/mnm-product-price.php',
			array( 
				'mnm_product' => $mnm_item, // For back-compatibility.
				'mnm_item'	  => $mnm_item
			),
			'',
			WC_Mix_and_Match()->plugin_path() . '/templates/'
		);
	}
}


/**
 * Open the MNM item product quantity sub-section
 * 
 * @param obj $mnm_item WC_Product of child item
 * @param obj WC_Mix_and_Match product of parent product
 */
function wc_mnm_template_child_item_quantity_open( $mnm_item, $product ) {

	wc_get_template(
		'single-product/mnm/' . $product->get_layout() . '/mnm-child-item-detail-wrapper-open.php',
		array(
			'classes' => 'product-quantity',
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}

/**
 * Get the MNM item product quantity
 * 
 * @param obj $mnm_item WC_Product of child item
 * @param obj WC_Mix_and_Match product of parent product
 */
function wc_mnm_template_child_item_quantity( $mnm_item, $product ) {

	wc_get_template(
		'single-product/mnm/mnm-product-quantity.php',
		array(
			'mnm_product' => $mnm_item, // For back-compatibility.
			'mnm_item'	  => $mnm_item
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}


/**
 * Close a sub-section.
 *
 * @param obj WC_Product $mnm_item the child product
 * @param obj WC_Mix_and_Match $product the parent container
 */
function wc_mnm_template_child_item_section_close( $mnm_item, $product ) {
	wc_get_template(
		'single-product/mnm/' . $product->get_layout() . '/mnm-child-item-detail-wrapper-close.php',
		array(),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);
}


/**
 * Closes the 'mnm_item' child item container.
 *
 * @param  WC_Product    $mnm_item
 * @param  WC_Mix_And_Match  $product
 * @since  1.3.0
 */
function wc_mnm_template_child_item_details_wrapper_close( $mnm_item, $product ) {
	
	wc_get_template(
		'single-product/mnm/' . $product->get_layout() . '/mnm-child-item-wrapper-close.php',
		array(),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}


/**
 * Echo ending markup if neccessary.
 *
 * @param obj $product WC_Mix_And_Match of parent product
 * @since  1.3.0
 */
function wc_mnm_template_child_items_wrapper_close( $product ) {

	if( $product->has_available_children() ) { 

		wc_get_template(
			'single-product/mnm/' . $product->get_layout() . '/mnm-items-wrapper-close.php',
			array(),
			'',
			WC_Mix_and_Match()->plugin_path() . '/templates/'
		);

	}

}


if ( ! function_exists( 'wc_mnm_template_reset_link' ) ) {

	/**
	 * Add the MNM reset link
	 * @since  1.3.0
	 */
	function wc_mnm_template_reset_link() {

		global $product;

		if( $product->is_type( 'mix-and-match' ) ) { 
				wc_get_template(
				'single-product/mnm/mnm-reset.php',
				array(),
				'',
				WC_Mix_and_Match()->plugin_path() . '/templates/'
			);
		}
	}

}

/**
 * Get the Add to Cart button area.
 * 
 * @param obj WC_Mix_and_Match product of parent product
 */
function wc_mnm_template_add_to_cart_wrap( $product ) { 

	if ( isset( $_GET[ 'update-container' ] ) ) {
		$updating_cart_key = wc_clean( $_GET[ 'update-container' ] );
		if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {
			echo '<input type="hidden" name="update-container" value="' . $updating_cart_key . '" />';
		}
	}

	wc_get_template(
		'single-product/add-to-cart/mnm-add-to-cart-wrap.php',
		array(
			'product' => $product,
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}

/*-----------------------------------------------------------------------------------*/
/* Backcompatibility Functions */
/*-----------------------------------------------------------------------------------*/

/**
 * Load backcompatibility functions uniquely on woocommerce_mix-and-match_add_to_cart hook.
 */
function _wc_mnm_add_template_backcompatibility() {
	add_action( 'woocommerce_before_template_part', '_wc_mnm_add_deprecated_hooks', 10, 4 );
	add_action( 'woocommerce_after_template_part', '_wc_mnm_detect_deprecated_hooks', 10, 4 );
}

/**
 * Restore old 1.0.x "row item" hooks for folks overriding the mnm.php template
 */
function _wc_mnm_add_deprecated_hooks( $template_name, $template_path, $located, $args ) {
	if( $template_name == 'single-product/add-to-cart/mnm.php' ) {
		if( false === strpos ( $located, 'plugins\woocommerce-mix-and-match-products' ) ) {
			add_action( 'woocommerce_mnm_row_item_thumbnail', 'wc_mnm_template_child_item_thumbnail', 10, 2 );
			add_action( 'woocommerce_mnm_row_item_description', 'wc_mnm_template_child_item_title', 10, 2 );
			add_action( 'woocommerce_mnm_row_item_description', 'wc_mnm_template_child_item_attributes', 20, 2 );
			add_action( 'woocommerce_mnm_row_item_quantity', 'wc_mnm_template_child_item_quantity', 10, 2 );
		}
		remove_action( 'woocommerce_before_template_part', '_wc_mnm_add_deprecated_hooks', 10, 4 );		
	}
}

/**
 * Log errors if the deprecated hooks are called.
 */
function _wc_mnm_detect_deprecated_hooks( $template_name, $template_path, $located, $args ) {
	if( $template_name == 'single-product/add-to-cart/mnm.php' ) {
		if( did_action( 'woocommerce_mnm_row_item_thumbnail' ) ) {
			wc_deprecated_hook( 'woocommerce_mnm_row_item_thumbnail', '1.3.0', 'woocommerce_mnm_child_item_details' );
		}
		if( did_action( 'woocommerce_mnm_row_item_description' ) ) {
			wc_deprecated_hook( 'woocommerce_mnm_row_item_description', '1.3.0', 'woocommerce_mnm_child_item_details' );
		}
		if( did_action( 'woocommerce_mnm_row_item_quantity' ) ) {
			wc_deprecated_hook( 'woocommerce_mnm_row_item_quantity', '1.3.0', 'woocommerce_mnm_child_item_details' );
		}	
		remove_action( 'woocommerce_after_template_part', '_wc_mnm_detect_deprecated_hooks', 10, 4 );	
	}
}

/*-----------------------------------------------------------------------------------*/
/* Deprecated Functions */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woocommerce_template_mnm_product_title' ) ) {

	/**
	 * Get the MNM item product title
	 *
	 * @param obj $mnm_item WC_Product of child itemm
	 */
	function woocommerce_template_mnm_product_title( $mnm_item, $product ) {
		wc_deprecated_function( 'woocommerce_template_mnm_product_title', '1.3.0', 'wc_mnm_template_child_item_title' );
		return wc_mnm_template_child_item_title( $mnm_item, $product );
	}
}

if ( ! function_exists( 'woocommerce_template_mnm_product_thumbnail' ) ) {

	/**
	 * Get the MNM item product thumbnail
	 * 
	 * @param obj $mnm_item WC_Product of child item
	 */
	function woocommerce_template_mnm_product_thumbnail( $mnm_item, $product ) {
		wc_deprecated_function( 'woocommerce_template_mnm_product_thumbnail', '1.3.0', 'wc_mnm_template_child_item_thumbnail' );
		return wc_mnm_template_child_item_thumbnail( $mnm_item, $product );
	}
}

if ( ! function_exists( 'woocommerce_template_mnm_product_attributes' ) ) {

	/**
	 * Get the MNM item product's attributes
	 * 
	 * @param obj $mnm_item WC_Product of child item
	 */
	function woocommerce_template_mnm_product_attributes( $mnm_item, $product ) {
		wc_deprecated_function( 'woocommerce_template_mnm_product_attributes', '1.3.0', 'wc_mnm_template_child_item_attributes' );
		return wc_mnm_template_child_item_attributes( $mnm_item, $product );
	}
}

if ( ! function_exists( 'woocommerce_template_mnm_product_quantity' ) ) {

	/**
	 * Get the MNM item product quantity
	 * 
	 * @param obj $mnm_item WC_Product of child item
	 */
	function woocommerce_template_mnm_product_quantity( $mnm_item, $product ) {
		wc_deprecated_function( 'woocommerce_template_mnm_product_quantity', '1.3.0', 'wc_mnm_template_child_item_quantity' );
		return wc_mnm_template_child_item_quantity( $mnm_item, $product );
	}

}

