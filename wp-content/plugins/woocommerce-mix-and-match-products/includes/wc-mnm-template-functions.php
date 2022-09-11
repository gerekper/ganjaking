<?php
/**
 * Template Functions
 *
 * Functions for the WooCommerce Mix and Match templating system.
 *
 * @package  WooCommerce Mix and Match Products/Functions
 * @since    1.0.0
 * @version  2.0.7
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*--------------------------------------------------------*/
/*  Mix and Match single product template functions     */
/*--------------------------------------------------------*/


/**
 * Add-to-cart template for Mix and Match. Handles the 'Form location > After summary' case.
 *
 * @since  1.3.0
 */
function wc_mnm_template_add_to_cart_after_summary() {

	global $product;

	if ( $product->is_type( 'mix-and-match' ) ) {
		if ( 'after_summary' === $product->get_add_to_cart_form_location() ) {
			$classes = implode( ' ', apply_filters( 'wc_mnm_form_wrapper_classes', array( 'summary-add-to-cart-form', 'summary-add-to-cart-form-mnm' ), $product ) );
			?><div class="<?php echo esc_attr( $classes ); ?>">
			<?php
				do_action( 'woocommerce_mix-and-match_add_to_cart' );
			?>
			</div>
			<?php
		}
	}
}

/**
 * Add-to-cart template for Mix and Match products.
 *
 * @since  1.3.0
 *
 * @param WC_Product_Mix_and_Match $product - Optionally call template for a specific product. Since 1.11.7
 */
function wc_mnm_template_add_to_cart( $container = false ) {

	global $product;
	$backup_product = $product;

	if ( is_numeric( $container ) ) {
		$container = wc_get_product( intval( $container ) );
	}

	// Swap the global product for this specific container.
	if ( $container ) {
		$product = $container;
	}

	if ( ! $product || ! $product->is_type( 'mix-and-match' ) ) {
		return;
	}

	if ( doing_action( 'woocommerce_single_product_summary' ) ) {
		if ( 'after_summary' === $product->get_add_to_cart_form_location() ) {
			return;
		}
	}

	// Enqueue scripts and styles - then, initialize js variables.
	wp_enqueue_script( 'wc-add-to-cart-mnm' );
	wp_enqueue_style( 'wc-mnm-frontend' );

	$classes = array( 
		'mnm_form',
		'cart',
		'cart_group',
		'layout_' . $product->get_layout(),
	);

	/**
	 * Form classes.
	 *
	 * @param array - The classes that will print in the <form> tag.
	 * @param obj $product WC_Mix_And_Match of parent product
	 * @since  2.1.2
	 */
	$classes = apply_filters( 'wc_mnm_form_classes', $classes, $product );

	// Load the add to cart template.
	wc_get_template(
		'single-product/add-to-cart/mnm.php',
		array(
			'container'          => $product,
			'min_container_size' => $product->get_min_container_size(),
			'max_container_size' => $product->get_max_container_size(),
			'classes'            => $classes
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

	// Restore product object.
	$product = $backup_product;

}

/**
 * The child contents loop.
 *
 * @param obj $product WC_Mix_And_Match of parent product
 * @since  2.0.0
 */
function wc_mnm_content_loop( $product ) {

	if ( $product->has_child_items() ) {

		/**
		 * 'wc_mnm_before_child_items' action.
		 *
		 * @param WC_Mix_and_Match $product
		 * @since  2.0.0
		 *
		 * @hooked wc_mnm_first_category_caption - -10
		 * @hooked wc_mnm_template_child_items_wrapper_open - 0
		 */
		do_action( 'wc_mnm_before_child_items', $product );

		foreach ( $product->get_child_items() as $child_item ) {

			/**
			 * 'wc_mnm_item_details' action.
			 *
			 * @param WC_MNM_Child_Item $child_item
			 * @since 2.0.0 - variable changed to WC_MNM_Child_Item but most product methods should pass through magic getter.
			 *
			 * @hooked wc_mnm_category_caption - -10
			 * @hooked wc_mnm_template_child_item_details_wrapper_open  -   0
			 * @hooked wc_mnm_template_child_item_thumbnail_open        -  10
			 * @hooked wc_mnm_template_child_item_thumbnail             -  20
			 * @hooked wc_mnm_template_child_item_section_close         -  30
			 * @hooked wc_mnm_template_child_item_details_open          -  40
			 * @hooked wc_mnm_template_child_item_title                 -  50
			 * @hooked wc_mnm_template_child_item_attributes            -  60
			 * @hooked wc_mnm_template_child_item_section_close         -  70
			 * @hooked wc_mnm_template_child_item_quantity_open         -  80
			 * @hooked wc_mnm_template_child_item_quantity              -  90
			 * @hooked wc_mnm_template_child_item_section_close         - 100
			 * @hooked wc_mnm_template_child_item_details_wrapper_close - 110
			 */
			do_action( 'wc_mnm_child_item_details', $child_item, $product );

		}

		/**
		 * 'wc_mnm_after_child_items' action.
		 *
		 * @param  WC_Mix_and_Match  $product
		 * @since  2.0.0
		 *
		 * @hooked wc_mnm_template_child_items_wrapper_close        - 100
		 */
		do_action( 'wc_mnm_after_child_items', $product );

	}

}

/**
 * Echo opening markup if necessary.
 *
 * @param obj $product WC_Mix_And_Match of parent product
 * @since  1.3.0
 */
function wc_mnm_template_child_items_wrapper_open( $product ) {

	if ( $product->has_child_items() ) {

		// Get the columns.
		$default_columns = get_option( 'wc_mnm_number_columns', 3 );
		$columns = apply_filters( 'wc_mnm_grid_layout_columns', $default_columns, $product );

		// Reset the loop.
		wc_set_loop_prop( 'loop', 0 );
		wc_set_loop_prop( 'columns', $columns );

		/**
		 * Table column headings.
		 *
		 * @param array id => Heading Title
		 * @param obj $product WC_Mix_And_Match of parent product
		 * @since  1.3.1
		 */
		$column_headers = apply_filters(
			'wc_mnm_tabular_column_headers',
			array( 'thumbnail'  => '&nbsp;',
				   'name'       => _x( 'Product', '[Frontend]', 'woocommerce-mix-and-match-products' ),
				   'quantity'   => _x( 'Quantity', '[Frontend]', 'woocommerce-mix-and-match-products' )
			),
			$product
		);

		$classes = array(
			'products',
			'mnm_child_products',
			$product->get_layout(),
		);

		if ( 'tabular' === $product->get_layout() ) {
			$classes[] = 'mnm_table';
		} elseif ( 'grid' === $product->get_layout() ) {
			$classes[] = 'columns-' . $columns;

			/**
			 * Use flex to make the grid.
			 *
			 * @param bool - Whether or not to use the flex layout to make the grid.
			 * @param obj $product WC_Mix_And_Match of parent product
			 * @since  2.0.3
			 */
			if ( apply_filters( 'wc_mnm_grid_has_flex_layout', true, $product ) ) {
				$classes[] = 'has-flex';
			}

		}

		/**
		 * Wrapping classes.
		 *
		 * @param array - The classes that will print in the opening template.
		 * @param obj $product WC_Mix_And_Match of parent product
		 * @since  2.0.0
		 */
		$classes = apply_filters( 'wc_mnm_loop_classes', $classes, $product );

		wc_get_template(
			'single-product/mnm/' . $product->get_layout() . '/mnm-items-wrapper-open.php',
			array(
				'columns'        => $columns,
				'column_headers' => $column_headers,
				'classes'        => $classes,
			),
			'',
			WC_Mix_and_Match()->plugin_path() . '/templates/'
		);

	}
}

/**
 * Opens the 'mnm_item' child item container.
 *
 * @param obj WC_MNM_Child_Item $child_item of child item
 * @param obj WC_Mix_and_Match $product the parent container
 * @since  1.3.0
 */
function wc_mnm_template_child_item_details_wrapper_open( $child_item, $product ) {

	/**
	 * Wrapping classes.
	 *
	 * @param array - The classes that will print in the opening template.
	 * @param obj WC_Mix_And_Match $product Product - object of parent product
	 * @param obj WC_MNM_Child_Item $child_item - The object of child item
	 * @since  2.0.0
	 */
	$classes = (array) apply_filters( 'wc_mnm_child_item_classes', array( 'mnm_item', 'child-item' ), $child_item, $product );

	wc_get_template(
		'single-product/mnm/' . $product->get_layout() . '/mnm-child-item-wrapper-open.php',
		array(
			'regular_price'  => $product->is_priced_per_product() ? wc_get_price_to_display( $child_item->get_product(), array( 'price' => $child_item->get_product()->get_regular_price() ) ) : 0,
			'price'          => $product->is_priced_per_product() ? wc_get_price_to_display( $child_item->get_product(), array( 'price' => $child_item->get_product()->get_price() ) ) : 0,
			'child_item'     => $child_item,
			'mnm_id'         => $child_item->get_product()->get_id(), // For back-compatibility.
			'mnm_item'       => $child_item->get_product(), // For back-compatibility.
			'classes'        => $classes,
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}

/**
 * Open the thumbnail sub-section.
 *
 * @param obj WC_MNM_Child_Item $child_item of child item
 * @param obj WC_Mix_and_Match $product the parent container
 */
function wc_mnm_template_child_item_thumbnail_open( $child_item, $product ) {

	/**
	 * Wrapping classes.
	 *
	 * @param array - The classes that will print in the opening template.
	 * @param obj WC_Mix_And_Match $product Product - object of parent product
	 * @param obj WC_MNM_Child_Item $child_item - The object of child item
	 * @since  2.0.0
	 */
	$classes = (array) apply_filters( 'wc_mnm_child_item_thumbnail_classes', array( 'product-thumbnail' ), $child_item, $product );

	wc_get_template(
		'single-product/mnm/' . $product->get_layout() . '/mnm-child-item-detail-wrapper-open.php',
		array(
			'classes'    => $classes,
			'child_item' => $child_item,
			'mnm_item'   => $child_item->get_product(), // For back-compatibility.
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);
}


/**
 * Get the child item product thumbnail
 *
 * @param obj WC_MNM_Child_Item $child_item of child item
 * @param obj WC_Mix_and_Match $product the parent container
 * @since  1.3.0
 */
function wc_mnm_template_child_item_thumbnail( $child_item, $product ) {

	$thumbnail_id = has_post_thumbnail( $child_item->get_product()->get_id() ) ? get_post_thumbnail_id( $child_item->get_product()->get_id() ) : get_post_thumbnail_id( $child_item->get_product()->get_parent_id() );

	/**
	 * Child item thumbnail size.
	 *
	 * @since 2.0.0
	 *
	 * @param string $size
	 * @param  obj WC_MNM_Child_Item $child_item of child item
	 * @param  obj WC_Product_Mix_and_Match $product
	 */
	$image_size    = apply_filters( 'wc_mnm_child_item_thumbnail_size', 'woocommerce_thumbnail', $child_item, $product );

	if ( has_filter( 'woocommerce_mnm_product_thumbnail_size' ) ) {
		wc_deprecated_hook( 'woocommerce_mnm_product_thumbnail_size', '2.0.0', 'wc_mnm_child_item_thumbnail_size' );
		$image_size = apply_filters( 'woocommerce_mnm_product_thumbnail_size', $image_size, $child_item->get_product(), $product );
	}

	/**
	 * Child item link_classes.
	 * Some themes use different lightbox triggers.
	 *
	 * @since 2.0.0
	 *
	 * @param array $link_classes
	 * @param  obj WC_MNM_Child_Item $child_item of child item
	 * @param  obj WC_Product_Mix_and_Match $product
	 *
	 */
	$link_classes    = apply_filters( 'wc_mnm_child_item_thumbnail_link_classes', array( 'image', 'zoom' ), $child_item, $product );

	if ( has_filter( 'wc_mnm_product_thumbnail_link_classes' ) ) {
		wc_deprecated_hook( 'wc_mnm_product_thumbnail_link_classes', '2.0.0', 'wc_mnm_child_item_thumbnail_link_classes' );
		$link_classes = apply_filters( 'wc_mnm_product_thumbnail_link_classes', $link_classes, $child_item->get_product(), $product );
	}

	wc_get_template(
		'single-product/mnm/mnm-product-thumbnail.php',
		array(
			'child_item'   => $child_item,
			'mnm_product'  => $child_item->get_product(), // For back-compatibility.
			'mnm_item'     => $child_item->get_product(), // For back-compatibility.
			'thumbnail_id' => $thumbnail_id,
			'image_size'   => $image_size,
			'link_classes' => $link_classes,
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}


/**
 * Add a 'details' sub-section.
 *
 * @param obj WC_MNM_Child_Item $child_item of child item
 * @param obj WC_Mix_and_Match $product the parent container
 */
function wc_mnm_template_child_item_details_open( $child_item, $product ) {
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
 * @param obj WC_MNM_Child_Item $child_item of child item
 * @param obj WC_Mix_and_Match $product the parent container
 */
function wc_mnm_template_child_item_title( $child_item, $product ) {

	$min_qty = $child_item->get_quantity( 'min', $child_item->get_id() );
	$max_qty = $child_item->get_quantity( 'max', $child_item->get_id() );

	$qty     = 'tabular' !== $product->get_layout() && $min_qty > 1 && $min_qty === $max_qty ? $min_qty : '';

	wc_get_template(
		'single-product/mnm/mnm-product-title.php',
		array(
			'quantity'    => $qty,
			'title'       => $child_item->get_product()->get_title(),
			'child_item'  => $child_item,
			'mnm_product' => $child_item->get_product(), // For back-compatibility.
			'mnm_item'    => $child_item->get_product(), // For back-compatibility.
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}

/**
 * Get the MNM item product's hidden data attributes
 *
 * @todo: eventually deprecate. All data attributes will be on the opening element.
 * @see: WC_MNM_Child_Item::get_data_attributes()
 * @see: grid/mnm-child-item-wrapper-open.php and table/mnm-child-item-wrapper-open.php templates
 *
 * @param obj WC_MNM_Child_Item $child_item of child item
 * @param obj WC_Mix_and_Match $product the parent container
 */
function wc_mnm_template_child_item_data_details( $child_item, $product ) {

	$is_priced_per_product = $product->is_priced_per_product();

	wc_get_template(
		'single-product/mnm/mnm-item-data-attributes.php',
		array(
				'child_item'        => $child_item,
				'mnm_item'          => $child_item->get_product(), // Preserved for back-compat.
				'mnm_item_id'       => $child_item->get_product()->get_id(), // Preserved for back-compat.
				'regular_price'     => $is_priced_per_product ? wc_get_price_to_display( $child_item->get_product(), array( 'price' => $child_item->get_product()->get_regular_price() ) ) : 0,
				'price'             => $is_priced_per_product ? wc_get_price_to_display( $child_item->get_product(), array( 'price' => $child_item->get_product()->get_price() ) ) : 0,
				'price_incl_tax'    => $is_priced_per_product ? wc_get_price_including_tax( $child_item->get_product(), array( 'price' => $child_item->get_product()->get_price() ) ) : 0,
				'price_excl_tax'    => $is_priced_per_product ? wc_get_price_excluding_tax( $child_item->get_product(), array( 'price' => $child_item->get_product()->get_price() ) ) : 0,
			),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}


/**
 * Get the MNM item product's attributes
 *
 * @param obj WC_MNM_Child_Item $child_item of child item
 * @param obj WC_Mix_and_Match $product the parent container
 */
function wc_mnm_template_child_item_attributes( $child_item, $product ) {

	if ( $child_item->get_product()->is_type( array( 'variation' ) ) ) {
		wc_get_template(
			'single-product/mnm/mnm-product-variation-attributes.php',
			array(
				'child_item'  => $child_item,
				'mnm_product' => $child_item->get_product(), // For back-compatibility.
				'mnm_item'    => $child_item->get_product(), // For back-compatibility.
			),
			'',
			WC_Mix_and_Match()->plugin_path() . '/templates/'
		);
	}

}


/**
 * Maybe display individual MNM option price
 *
 * @param obj WC_MNM_Child_Item $child_item of child item
 * @param obj WC_Mix_and_Match $product the parent container
 * @since  1.4.0
 */
function wc_mnm_template_child_item_price( $child_item, $product ) {
	if ( $product->is_priced_per_product() ) {
		wc_get_template(
			'single-product/mnm/mnm-product-price.php',
			array(
				'child_item'  => $child_item,
				'mnm_product' => $child_item->get_product(), // For back-compatibility.
				'mnm_item'    => $child_item->get_product(), // For back-compatibility.
			),
			'',
			WC_Mix_and_Match()->plugin_path() . '/templates/'
		);
	}
}


/**
 * Open the MNM item product quantity sub-section
 *
 * @param obj WC_MNM_Child_Item $child_item of child item
 * @param obj WC_Mix_and_Match product of parent product
 */
function wc_mnm_template_child_item_quantity_open( $child_item, $product ) {

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
 * @param obj WC_MNM_Child_Item $child_item of child item
 * @param obj WC_Mix_and_Match product of parent product
 */
function wc_mnm_template_child_item_quantity( $child_item, $product ) {

	// Show nothing if the parent container is not purchasable.
	if ( ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return;
	}

	/* translators: %1$d: Quantity, %2$s: Product name. */
	$required_text = sprintf(
        _x( '&times;%1$d <span class="screen-reader-text">%2$s</span>', '[Frontend]', 'woocommerce-mix-and-match-products' ),
		$child_item->get_quantity( 'min' ),
		wp_strip_all_tags( $child_item->get_product()->get_name() )
	);

	/* translators: %1$d: Quantity, %2$s: Product name. */
	$checkbox_label = sprintf(
        _x( 'Add %1$d <span class="screen-reader-text">%2$s</span>', '[Frontend]', 'woocommerce-mix-and-match-products' ),
		$child_item->get_quantity( 'max' ),
		wp_strip_all_tags( $child_item->get_product()->get_name() )
	);

	$input_args = array(
		'input_id'    => uniqid( 'quantity_' ),
		'input_name'  => $child_item->get_input_name(),
		'input_value' => $child_item->get_quantity( 'value' ),
		'min_value'   => $child_item->get_quantity( 'min' ),
		'max_value'   => $child_item->get_quantity( 'max' ),
		'placeholder' => 0,
		'step'        => $child_item->get_quantity( 'step' ),
		'classes'     => array( 'qty', 'mnm-quantity', 'input-text' ),
		'required_text' => $required_text,
		'checkbox_label' => $checkbox_label,
	);

	/**
	 * Filter wc_mnm_child_item_quantity_input_args.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args
	 * @param obj WC_Product
	 * @param obj WC_Product_Mix_and_Match
	 */
	$input_args = apply_filters( 'wc_mnm_child_item_quantity_input_args', $input_args, $child_item, $product );

	// Backcompatibility.
	if ( has_filter( 'woocommerce_mnm_child_quantity_input_args' ) ) {
		wc_deprecated_hook( 'woocommerce_mnm_child_quantity_input_args', '2.0.0', 'wc_mnm_child_item_quantity_input_args : note that the 2nd parameter will be a WC_MNM_Child_Item instance.' );
		$input_args = apply_filters( 'woocommerce_mnm_child_quantity_input_args', $input_args, $child_item->get_product(), $this->get_container() );
	}

	wc_get_template(
		'single-product/mnm/mnm-product-quantity.php',
		array(
			'child_item'  => $child_item,
			'input_args'  => $input_args,
			'mnm_product' => $child_item->get_product(), // For back-compatibility.
			'mnm_item'    => $child_item->get_product(), // For back-compatibility.
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}


/**
 * Close a sub-section.
 *
 * @param obj WC_MNM_Child_Item $child_item of child item
 * @param obj WC_Mix_and_Match $product the parent container
 */
function wc_mnm_template_child_item_section_close( $child_item, $product ) {
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
function wc_mnm_template_child_item_details_wrapper_close( $child_item, $product ) {

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

	if ( $product->has_child_items() ) {

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

		if ( $product->is_type( 'mix-and-match' ) ) {
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
 * Get the Add to Cart button wrap.
 *
 * @param obj WC_Mix_and_Match product of parent product
 */
function wc_mnm_template_add_to_cart_wrap( $product ) {

	$purchasable_notice = _x( 'This product is currently unavailable.', '[Frontend]', 'woocommerce-mix-and-match-products' );

	if ( ! $product->is_purchasable()  && current_user_can( 'manage_woocommerce' ) ) {

		$purchasable_notice_reason = '';

		// Give store owners a reason.
		if ( defined( 'WC_MNM_UPDATING' ) ) {
			$purchasable_notice_reason .= sprintf( __( 'The Mix and Match database is updating in the background. During this time, all mix and match products on your site will be unavailable. If this message persists, please <a href="%s" target="_blank">get in touch</a> with our support team. Note: This message is visible to store managers only.', 'woocommerce-mix-and-match-products' ), WC_Mix_and_Match()->get_resource_url( 'ticket-form' ) );
		} elseif ( ! $product->is_priced_per_product() && '' === $product->get_price() ) {
			$purchasable_notice_reason .= sprintf( __( '&quot;%1$s&quot; is not purchasable because it is missing a base price. To give &quot;%1$s&quot; a static base price, navigate to <strong>Product Data > General</strong> and fill in the <strong>Regular Price</strong> field. Note: This message is visible to store managers only.', 'woocommerce-mix-and-match-products' ), $product->get_title() );
		} elseif ( ! $product->has_child_items() ) {
			$purchasable_notice_reason .= __( 'Please make sure that this product has allowed contents defined and that those products have a price and/or are in stock. WooCommerce does not allow products with a blank price to be purchased. Note: This message is visible to store managers only.', 'woocommerce-mix-and-match-products' );
		}

		if ( $purchasable_notice_reason ) {
			$purchasable_notice .= '<div class="woocommerce-info"><span class="purchasable_notice_reason">' . $purchasable_notice_reason . '</span></div>';
		}
	}

	if ( isset( $_GET['update-container'] ) ) {
		$updating_cart_key = wc_clean( $_GET['update-container'] );
		if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {
			echo '<input type="hidden" name="update-container" value="' . $updating_cart_key . '" />';
		}
	}

	wc_get_template(
		'single-product/add-to-cart/mnm-add-to-cart-wrap.php',
		array(
			'product'            => $product,
			'purchasable_notice' => $purchasable_notice,
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}

/**
 * Get the Add to Cart button.
 *
 * @since 2.0.0
 *
 * @param obj WC_Mix_and_Match product of parent product
 */
function wc_mnm_template_add_to_cart_button( $product ) {

	wc_get_template(
		'single-product/add-to-cart/mnm-add-to-cart-button.php',
		array(
			'product'            => $product,
		),
		'',
		WC_Mix_and_Match()->plugin_path() . '/templates/'
	);

}

/**
* Display Mix and Match child product short description
*
* @param obj WC_MNM_Child_Item $child_item of child item
* @param obj WC_Mix_and_Match $product the parent container
*/
function wc_mnm_child_item_short_description( $child_item, $product ) {

	global $post;
	$backup_post = $post;

	// If a variation, get the parent product. NB: Variation descriptions are post_content and so won't automatically display with the short description template.
	$_post = get_post( $child_item->get_product_id() );

	// Temporarily switch global $post to our child post.
	$post = $_post;

	woocommerce_template_single_excerpt();

	// Restore the global post object.
	$post = $backup_post;
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
	if ( $template_name == 'single-product/add-to-cart/mnm.php' ) {
		if ( false === strpos( $located, 'plugins\woocommerce-mix-and-match-products' ) ) {
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
	if ( $template_name == 'single-product/add-to-cart/mnm.php' ) {
		if ( did_action( 'woocommerce_mnm_row_item_thumbnail' ) ) {
			wc_deprecated_hook( 'woocommerce_mnm_row_item_thumbnail', '1.3.0', 'woocommerce_mnm_child_item_details' );
		}
		if ( did_action( 'woocommerce_mnm_row_item_description' ) ) {
			wc_deprecated_hook( 'woocommerce_mnm_row_item_description', '1.3.0', 'woocommerce_mnm_child_item_details' );
		}
		if ( did_action( 'woocommerce_mnm_row_item_quantity' ) ) {
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
	 * @param obj WC_MNM_Child_Item $child_item of child itemm
	 */
	function woocommerce_template_mnm_product_title( $child_item, $product ) {
		wc_deprecated_function( 'woocommerce_template_mnm_product_title', '1.3.0', 'wc_mnm_template_child_item_title' );
		return wc_mnm_template_child_item_title( $child_item, $product );
	}
}

if ( ! function_exists( 'woocommerce_template_mnm_product_thumbnail' ) ) {

	/**
	 * Get the MNM item product thumbnail
	 *
	 * @param obj WC_MNM_Child_Item $child_item of child item
	 */
	function woocommerce_template_mnm_product_thumbnail( $child_item, $product ) {
		wc_deprecated_function( 'woocommerce_template_mnm_product_thumbnail', '1.3.0', 'wc_mnm_template_child_item_thumbnail' );
		return wc_mnm_template_child_item_thumbnail( $child_item, $product );
	}
}

if ( ! function_exists( 'woocommerce_template_mnm_product_attributes' ) ) {

	/**
	 * Get the MNM item product's attributes
	 *
	 * @param obj WC_MNM_Child_Item $child_item of child item
	 */
	function woocommerce_template_mnm_product_attributes( $child_item, $product ) {
		wc_deprecated_function( 'woocommerce_template_mnm_product_attributes', '1.3.0', 'wc_mnm_template_child_item_attributes' );
		return wc_mnm_template_child_item_attributes( $child_item, $product );
	}
}

if ( ! function_exists( 'woocommerce_template_mnm_product_quantity' ) ) {

	/**
	 * Get the MNM item product quantity
	 *
	 * @param obj WC_MNM_Child_Item $child_item of child item
	 */
	function woocommerce_template_mnm_product_quantity( $child_item, $product ) {
		wc_deprecated_function( 'woocommerce_template_mnm_product_quantity', '1.3.0', 'wc_mnm_template_child_item_quantity' );
		return wc_mnm_template_child_item_quantity( $child_item, $product );
	}
}

/**
 * The child contents loop.
 *
 * @since  1.8.0
 * @deprecated 2.0.0
 *
 * @param obj $product WC_Mix_And_Match of parent product
 */
function woocommerce_mnm_content_loop( $product ) {
	wc_deprecated_function( 'woocommerce_mnm_content_loop', '2.0.0', 'wc_mnm_content_loop' );
	return wc_mnm_content_loop( $product );
}

/**
 * The first catagory caption.
 *
 * @since 2.0.0
 *
 * @param obj $child_item WC_MNM_Child_Item
 * @param obj $product WC_Mix_And_Match of parent product
 */
function wc_mnm_category_caption( $child_item, $product ) {

	$cat_ids = $product->get_child_category_ids();

	if ( 'categories' === $product->get_content_source() && count( $cat_ids ) > 1 ) {

		$cat_ids = $product->get_child_category_ids();

		if ( property_exists( $product, 'current_cat' ) && property_exists( $product, 'remaining_cats' ) ) {

			// Detect a change in category.
			if ( ! in_array( $product->current_cat, $child_item->get_product()->get_category_ids() ) ) {
				$pos = array_search( $product->current_cat, $cat_ids );

				$next_cat_id = array_shift( $product->remaining_cats );
				$category    = get_term_by( 'term_id', $next_cat_id, 'product_cat' );

				if ( $category instanceof WP_Term ) {

					wc_mnm_template_child_items_wrapper_close( $product );
					woocommerce_template_loop_category_title( $category );
					wc_mnm_template_child_items_wrapper_open( $product );

					// Stash the current category.
					$product->current_cat    = $next_cat_id;
					$product->remaining_cats = $product->remaining_cats;

				}

			}
		}

	}

}


/**
 * Switch the category captions in the loop.
 *
 * @since 2.0.0
 *
 * @param obj $product WC_Mix_And_Match of parent product
 */
function wc_mnm_first_category_caption( $product ) {

	$cat_ids = $product->get_child_category_ids();

	if ( 'categories' === $product->get_content_source() && count( $cat_ids ) > 1 ) {

		// Don't display the category count.
		add_filter( 'woocommerce_subcategory_count_html', '__return_null' );

		$first_cat_id = array_shift( $cat_ids );
		$category     = get_term_by( 'term_id', $first_cat_id, 'product_cat' );

		if ( $category instanceof WP_Term ) {

			// Use the existing category title template.
			woocommerce_template_loop_category_title( $category );

			// Stash the current category.
			$product->current_cat    = $first_cat_id;
			$product->remaining_cats = $cat_ids;
		}

	}
}
