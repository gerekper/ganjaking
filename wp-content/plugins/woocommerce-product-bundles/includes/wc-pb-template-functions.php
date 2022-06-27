<?php
/**
 * Product Bundles template functions
 *
 * @package  WooCommerce Product Bundles
 * @since    4.11.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
|--------------------------------------------------------------------------
| Single-product.
|--------------------------------------------------------------------------
*/

/**
 * Add-to-cart template for Product Bundles. Handles the 'Form location > After summary' case.
 *
 * @since  6.14.1
 */
function wc_pb_template_add_to_cart_after_summary() {

	global $product;

	if ( wc_pb_is_product_bundle() ) {
		if ( 'after_summary' === $product->get_add_to_cart_form_location() ) {
			$classes = implode( ' ', apply_filters( 'woocommerce_bundle_form_wrapper_classes', array( 'summary-add-to-cart-form', 'summary-add-to-cart-form-bundle' ), $product ) );
			?><div class="<?php echo esc_attr( $classes );?>"><?php
				do_action( 'woocommerce_bundle_add_to_cart' );
			?></div><?php
		}
	}
}

/**
 * Add-to-cart template for Product Bundles.
 */
function wc_pb_template_add_to_cart() {

	global $product;

	if ( doing_action( 'woocommerce_single_product_summary' ) ) {
		if ( 'after_summary' === $product->get_add_to_cart_form_location() ) {
			return;
		}
	}

	// Enqueue variation scripts.
	wp_enqueue_script( 'wc-add-to-cart-bundle' );

	wp_enqueue_style( 'wc-bundle-css' );

	$bundled_items = $product->get_bundled_items();
	$form_classes  = array( 'layout_' . $product->get_layout(), 'group_mode_' . $product->get_group_mode() );

	if ( ! $product->is_in_stock() ) {
		$form_classes[] = 'bundle_out_of_stock';
	}

	if ( 'outofstock' === $product->get_bundled_items_stock_status() ) {
		$form_classes[] = 'bundle_insufficient_stock';
	}

	if ( ! empty( $bundled_items ) ) {

		wc_get_template( 'single-product/add-to-cart/bundle.php', array(
			'bundled_items'     => $bundled_items,
			'product'           => $product,
			'classes'           => implode( ' ', apply_filters( 'woocommerce_bundle_form_classes', $form_classes, $product ) ),
			// Back-compat.
			'product_id'        => $product->get_id(),
			'availability_html' => wc_get_stock_html( $product ),
			'bundle_price_data' => $product->get_bundle_form_data()
		), false, WC_PB()->plugin_path() . '/templates/' );
	}
}

/**
 * Add-to-cart buttons area.
 *
 * @since 5.5.0
 *
 * @param  WC_Product_Bundle  $bundle
 */
function wc_pb_template_add_to_cart_wrap( $product ) {

	$is_purchasable     = $product->is_purchasable();
	$purchasable_notice = __( 'This product is currently unavailable.', 'woocommerce-product-bundles' );

	if ( ! $is_purchasable && current_user_can( 'manage_woocommerce' ) ) {

		$purchasable_notice_reason = '';

		// Give store owners a reason.
		if ( defined( 'WC_PB_UPDATING' ) ) {
			/* translators: Ticket form URL  */
			$purchasable_notice_reason .= sprintf( __( 'The Product Bundles database is updating in the background. During this time, all bundles on your site will be unavailable. If this message persists, please <a href="%s" target="_blank">get in touch</a> with our support team. Note: This message is visible to store managers only.', 'woocommerce-product-bundles' ), WC_PB()->get_resource_url( 'ticket-form' ) );
		} elseif ( false === $product->contains( 'priced_individually' ) && '' === $product->get_price() ) {
			/* translators: %1$s: Product title %, %2$s: Pricing options doc URL */
			$purchasable_notice_reason .= sprintf( __( '&quot;%1$s&quot; is not purchasable just yet. But, fear not &ndash; setting up <a href="%2$s" target="_blank">pricing options</a> only takes a minute! <ul class="pb_notice_list"><li>To give &quot;%1$s&quot; a static base price, navigate to <strong>Product Data > General</strong> and fill in the <strong>Regular Price</strong> field.</li><li>To preserve the prices and taxes of individual bundled products, go to <strong>Product Data > Bundled Products</strong> and enable <strong>Priced Individually</strong> for each bundled product whose price must be preserved.</li></ul>Note: This message is visible to store managers only.', 'woocommerce-product-bundles' ), $product->get_title(), WC_PB()->get_resource_url( 'pricing-options' ) );
		} elseif ( $product->contains( 'non_purchasable' ) ) {
			$purchasable_notice_reason .= __( 'Please make sure that all products contained in this bundle have a price. WooCommerce does not allow products with a blank price to be purchased. Note: This message is visible to store managers only.', 'woocommerce-product-bundles' );
		} elseif ( $product->contains( 'subscriptions' ) && class_exists( 'WC_Subscriptions_Admin' ) && 'yes' !== get_option( WC_Subscriptions_Admin::$option_prefix . '_multiple_purchase', 'no' ) ) {
			$purchasable_notice_reason .= __( 'Please enable <strong>Mixed Checkout</strong> under <strong>WooCommerce > Settings > Subscriptions</strong>. Bundles that contain subscription-type products cannot be purchased when <strong>Mixed Checkout</strong> is disabled. Note: This message is visible to store managers only.', 'woocommerce-product-bundles' );
		}

		if ( $purchasable_notice_reason ) {
			$purchasable_notice .= '<span class="purchasable_notice_reason">' . $purchasable_notice_reason . '</span>';
		}
	}

	$form_data = $product->get_bundle_form_data();

	wc_get_template( 'single-product/add-to-cart/bundle-add-to-cart-wrap.php', array(
		'is_purchasable'     => $is_purchasable,
		'purchasable_notice' => $purchasable_notice,
		'availability_html'  => wc_get_stock_html( $product ),
		'bundle_form_data'   => $form_data,
		'product'            => $product,
		'product_id'         => $product->get_id(),
		// Back-compat:
		'bundle_price_data'  => $form_data,
	), false, WC_PB()->plugin_path() . '/templates/' );
}

/**
 * Add-to-cart button and quantity input.
 */
function wc_pb_template_add_to_cart_button( $bundle = false ) {

	if ( isset( $_GET[ 'update-bundle' ] ) ) {
		$updating_cart_key = wc_clean( $_GET[ 'update-bundle' ] );
		if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {
			echo '<input type="hidden" name="update-bundle" value="' . $updating_cart_key . '" />';
		}
	}

	if ( $bundle && ! $bundle->is_in_stock() ) {
		return;
	}

	wc_get_template( 'single-product/add-to-cart/bundle-quantity-input.php', array(), false, WC_PB()->plugin_path() . '/templates/' );
	wc_get_template( 'single-product/add-to-cart/bundle-button.php', array(), false, WC_PB()->plugin_path() . '/templates/' );
}

/**
 * Load the bundled item title template.
 *
 * @param  WC_Bundled_Item    $bundled_item
 * @param  WC_Product_Bundle  $bundle
 */
function wc_pb_template_bundled_item_title( $bundled_item, $bundle ) {

	$min_qty = $bundled_item->get_quantity( 'min' );
	$max_qty = $bundled_item->get_quantity( 'max' );

	$qty     = 'tabular' !== $bundle->get_layout() && $min_qty > 1 && $min_qty === $max_qty ? $min_qty : '';

	wc_get_template( 'single-product/bundled-item-title.php', array(
		'quantity'     => $qty,
		'title'        => $bundled_item->get_title(),
		'permalink'    => $bundled_item->get_permalink(),
		'optional'     => $bundled_item->is_optional(),
		'title_suffix' => $bundled_item->get_optional_suffix(),
		'bundled_item' => $bundled_item,
		'bundle'       => $bundle
	), false, WC_PB()->plugin_path() . '/templates/' );
}

/**
 * Load the bundled item thumbnail template.
 *
 * @param  WC_Bundled_Item    $bundled_item
 * @param  WC_Product_Bundle  $bundle
 */
function wc_pb_template_bundled_item_thumbnail( $bundled_item, $bundle ) {

	$layout     = $bundle->get_layout();
	$product_id = $bundled_item->get_product_id();

	if ( 'tabular' === $layout ) {
		echo '<td class="bundled_item_col bundled_item_images_col">';
	}

	if ( $bundled_item->is_visible() ) {
		if ( $bundled_item->is_thumbnail_visible() ) {

			/**
			 * 'woocommerce_bundled_product_gallery_classes' filter.
			 *
			 * @param  array            $classes
			 * @param  WC_Bundled_Item  $bundled_item
			 */
			$gallery_classes = apply_filters( 'woocommerce_bundled_product_gallery_classes', array( 'bundled_product_images', 'images' ), $bundled_item );

			/**
			 * 'woocommerce_bundled_item_image_tmpl_params' filter.
			 *
			 * @param  array            $params
			 * @param  WC_Bundled_Item  $bundled_item
			 */
			$bundled_item_image_tmpl_params = apply_filters( 'woocommerce_bundled_item_image_tmpl_params', array(
				'post_id'         => $product_id,
				'product_id'      => $product_id,
				'bundled_item'    => $bundled_item,
				'gallery_classes' => $gallery_classes,
				'image_size'      => $bundled_item->get_bundled_item_thumbnail_size(),
				'image_rel'       => current_theme_supports( 'wc-product-gallery-lightbox' ) ? 'photoSwipe' : 'prettyPhoto',
			), $bundled_item );

			wc_get_template( 'single-product/bundled-item-image.php', $bundled_item_image_tmpl_params, false, WC_PB()->plugin_path() . '/templates/' );
		}
	}

	if ( 'tabular' === $layout ) {
		echo '</td>';
	}
}

/**
 * Load the bundled item short description template.
 *
 * @param  WC_Bundled_Item    $bundled_item
 * @param  WC_Product_Bundle  $bundle
 */
function wc_pb_template_bundled_item_description( $bundled_item, $bundle ) {

	if ( ! $bundled_item->get_description() ) {
		return;
	}

	wc_get_template( 'single-product/bundled-item-description.php', array(
		'description' => $bundled_item->get_description()
	), false, WC_PB()->plugin_path() . '/templates/' );
}

/**
 * Adds the 'bundled_product' container div.
 *
 * @param  WC_Bundled_Item    $bundled_item
 * @param  WC_Product_Bundle  $bundle
 */
function wc_pb_template_bundled_item_details_wrapper_open( $bundled_item, $bundle ) {

	$layout = $bundle->get_layout();

	if ( ! in_array( $layout, array( 'default', 'tabular', 'grid' ) ) ) {
		return;
	}

	if ( 'default' === $layout ) {
		$el = 'div';
	} elseif ( 'tabular' === $layout ) {
		$el = 'tr';
	} elseif ( 'grid' === $layout ) {
		$el = 'li';
	}

	$classes = $bundled_item->get_classes( false );
	$style   = $bundled_item->is_visible() ? '' : ' style="display:none;"';

	if ( 'grid' === $layout && $bundled_item->is_visible() ) {
		// Get class of item in the grid.
		$classes[] = WC_PB()->display->get_grid_layout_class( $bundled_item );
		// Increment counter.
		WC_PB()->display->incr_grid_layout_pos( $bundled_item );
	}

	echo '<' . $el . ' class="' . implode( ' ' , $classes ) . '"' . $style . ' >';
}

/**
 * Adds a qty input column when using the tabular template.
 *
 * @param  WC_Bundled_Item    $bundled_item
 * @param  WC_Product_Bundle  $bundle
 */
function wc_pb_template_tabular_bundled_item_qty( $bundled_item, $bundle ) {

	$layout = $bundle->get_layout();

	if ( 'tabular' === $layout ) {

		/** Documented in 'WC_PB_Cart::get_posted_bundle_configuration'. */
		$bundle_fields_prefix = apply_filters( 'woocommerce_product_bundle_field_prefix', '', $bundle->get_id() );

		$quantity_min     = $bundled_item->get_quantity( 'min' );
		$quantity_max     = $bundled_item->get_quantity( 'max' );
		$quantity_default = $bundled_item->get_quantity( 'default' );
		$input_name       = $bundle_fields_prefix . 'bundle_quantity_' . $bundled_item->get_id();
		$hide_input       = $quantity_min === $quantity_max;

		echo '<td class="bundled_item_col bundled_item_qty_col">';

		wc_get_template( 'single-product/bundled-item-quantity.php', array(
			'bundled_item'         => $bundled_item,
			'quantity_min'         => $quantity_min,
			'quantity_max'         => $quantity_max,
			'quantity_default'     => $quantity_default,
			'input_name'           => $input_name,
			'layout'               => $layout,
			'hide_input'           => $hide_input,
			'bundle_fields_prefix' => $bundle_fields_prefix
		), false, WC_PB()->plugin_path() . '/templates/' );

		echo '</td>';
	}
}

/**
 * Adds a qty input column when using the default template.
 *
 * @param  WC_Bundled_Item  $bundled_item
 */
function wc_pb_template_default_bundled_item_qty( $bundled_item ) {

	$bundle = $bundled_item->get_bundle();
	$layout = $bundle->get_layout();

	if ( in_array( $layout, array( 'default', 'grid' ) ) ) {

		/** Documented in 'WC_PB_Cart::get_posted_bundle_configuration'. */
		$bundle_fields_prefix = apply_filters( 'woocommerce_product_bundle_field_prefix', '', $bundle->get_id() );

		$quantity_min     = $bundled_item->get_quantity( 'min' );
		$quantity_max     = $bundled_item->get_quantity( 'max' );
		$quantity_default = $bundled_item->get_quantity( 'default' );
		$input_name       = $bundle_fields_prefix . 'bundle_quantity_' . $bundled_item->get_id();
		$hide_input       = $quantity_min === $quantity_max;
		wc_get_template( 'single-product/bundled-item-quantity.php', array(
			'bundled_item'         => $bundled_item,
			'quantity_min'         => $quantity_min,
			'quantity_max'         => $quantity_max,
			'quantity_default'     => $quantity_default,
			'input_name'           => $input_name,
			'layout'               => $layout,
			'hide_input'           => $hide_input,
			'bundle_fields_prefix' => $bundle_fields_prefix
		), false, WC_PB()->plugin_path() . '/templates/' );
	}
}


/**
 * Close the 'bundled_product' container div.
 *
 * @param  WC_Bundled_Item    $bundled_item
 * @param  WC_Product_Bundle  $bundle
 */
function wc_pb_template_bundled_item_details_wrapper_close( $bundled_item, $bundle ) {

	$layout = $bundle->get_layout();

	if ( ! in_array( $layout, array( 'default', 'tabular', 'grid' ) ) ) {
		return;
	}

	if ( 'default' === $layout ) {
		$el = 'div';
	} elseif ( 'tabular' === $layout ) {
		$el = 'tr';
	} elseif ( 'grid' === $layout ) {
		$el = 'li';
	}

	echo '</' . $el . '>';
}

/**
 * Add a 'details' container div.
 *
 * @param  WC_Bundled_Item    $bundled_item
 * @param  WC_Product_Bundle  $bundle
 */
function wc_pb_template_bundled_item_details_open( $bundled_item, $bundle ) {

	$layout = $bundle->get_layout();

	if ( 'tabular' === $layout ) {
		echo '<td class="bundled_item_col bundled_item_details_col">';
	}

	echo '<div class="details">';
}

/**
 * Close the 'details' container div.
 *
 * @param  WC_Bundled_Item    $bundled_item
 * @param  WC_Product_Bundle  $bundle
 */
function wc_pb_template_bundled_item_details_close( $bundled_item, $bundle ) {

	$layout = $bundle->get_layout();

	echo '</div>';

	if ( 'tabular' === $layout ) {
		echo '</td>';
	}
}

/**
 * Display bundled product details templates.
 *
 * @param  WC_Bundled_Item    $bundled_item
 * @param  WC_Product_Bundle  $bundle
 */
function wc_pb_template_bundled_item_product_details( $bundled_item, $bundle ) {

	if ( $bundled_item->is_purchasable() ) {

		$bundle_id          = $bundle->get_id();
		$bundled_product    = $bundled_item->product;
		$bundled_product_id = $bundled_product->get_id();
		$availability       = $bundled_item->get_availability();

		/** Documented in 'WC_PB_Cart::get_posted_bundle_configuration'. */
		$bundle_fields_prefix = apply_filters( 'woocommerce_product_bundle_field_prefix', '', $bundle_id );

		$bundled_item->add_price_filters();

		if ( $bundled_item->is_optional() ) {

			$label_price = '';

			if ( ( $price_html = $bundled_item->product->get_price_html() ) && $bundled_item->is_priced_individually() ) {

				/* translators: Product price */
				$label_price_format = __( ' for %s', 'woocommerce-product-bundles' );
				$html_from_text_native = wc_get_price_html_from_text();

				if ( false !== strpos( $price_html, $html_from_text_native ) ) {
					/* translators: Product price */
					$label_price_format = __( ' from %s', 'woocommerce-product-bundles' );
					$price_html  = str_replace( $html_from_text_native, '', $price_html );
				}

				$label_price = sprintf( $label_price_format, '<span class="price">' . $price_html . '</span>' );
			}

			$label_title = '';

			if ( $bundled_item->get_title() === '' ) {

				$min_quantity = $bundled_item->get_quantity( 'min' );
				$max_quantity = $bundled_item->get_quantity( 'max' );
				$label_suffix = $min_quantity > 1 && $max_quantity === $min_quantity ? $min_quantity : '';
				/* translators: Product title */
				$label_title  = sprintf( __( ' &quot;%s&quot;', 'woocommerce-product-bundles' ), WC_PB_Helpers::format_product_shop_title( $bundled_item->get_raw_title(), $label_suffix ) );
			}

			// Optional checkbox template.
			wc_get_template( 'single-product/bundled-item-optional.php', array(
				'label_title'          => $label_title,
				'label_price'          => $label_price,
				'bundled_item'         => $bundled_item,
				'bundle_fields_prefix' => $bundle_fields_prefix,
				'availability_html'    => false === $bundled_item->is_in_stock() ? $bundled_item->get_availability_html() : '',
				// Back-compat.
				'quantity'             => $bundled_item->get_quantity( 'min' )
			), false, WC_PB()->plugin_path() . '/templates/' );
		}

		if ( $bundled_product->get_type() === 'simple' || $bundled_product->get_type() === 'subscription' ) {

			// Simple Product template.
			wc_get_template( 'single-product/bundled-product-simple.php', array(
				'bundled_product_id'   => $bundled_product_id,
				'bundled_product'      => $bundled_product,
				'bundled_item'         => $bundled_item,
				'bundle_id'            => $bundle_id,
				'bundle'               => $bundle,
				'bundle_fields_prefix' => $bundle_fields_prefix,
				'availability'         => $availability,
				'custom_product_data'  => apply_filters( 'woocommerce_bundled_product_custom_data', array(), $bundled_item )
			), false, WC_PB()->plugin_path() . '/templates/' );

		} elseif ( $bundled_product->get_type() === 'variable' || $bundled_product->get_type() === 'variable-subscription' ) {

			$do_ajax                       = $bundled_item->use_ajax_for_product_variations();
			$variations                    = $do_ajax ? false : $bundled_item->get_product_variations();
			$variation_attributes          = $bundled_item->get_product_variation_attributes();
			$selected_variation_attributes = $bundled_item->get_selected_product_variation_attributes();

			if ( ! $do_ajax && empty( $variations ) ) {

				$is_out_of_stock = false === $bundled_item->is_in_stock();

				// Unavailable Product template.
				wc_get_template( 'single-product/bundled-product-unavailable.php', array(
					'bundled_item'        => $bundled_item,
					'bundle'              => $bundle,
					'custom_product_data' => apply_filters( 'woocommerce_bundled_product_custom_data', array(
						'is_unavailable'  => 'yes',
						'is_out_of_stock' => $is_out_of_stock ? 'yes' : 'no',
						'is_required'     => $bundled_item->get_quantity( 'min', array( 'check_optional' => true ) ) > 0 ? 'yes' : 'no'
					), $bundled_item )
				), false, WC_PB()->plugin_path() . '/templates/' );

			} else {

				// Variable Product template.
				wc_get_template( 'single-product/bundled-product-variable.php', array(
					'bundled_product_id'                  => $bundled_product_id,
					'bundled_product'                     => $bundled_product,
					'bundled_item'                        => $bundled_item,
					'bundle_id'                           => $bundle_id,
					'bundle'                              => $bundle,
					'bundle_fields_prefix'                => $bundle_fields_prefix,
					'availability'                        => $availability,
					'bundled_product_attributes'          => $variation_attributes,
					'bundled_product_variations'          => $variations,
					'bundled_product_selected_attributes' => $selected_variation_attributes,
					'custom_product_data'                 => apply_filters( 'woocommerce_bundled_product_custom_data', array(
						'bundle_id'       => $bundle_id,
						'bundled_item_id' => $bundled_item->get_id()
					), $bundled_item )
				), false, WC_PB()->plugin_path() . '/templates/' );
			}
		}

		$bundled_item->remove_price_filters();

	} else {
		// Unavailable Product template.
		wc_get_template( 'single-product/bundled-product-unavailable.php', array(
			'bundled_item'        => $bundled_item,
			'bundle'              => $bundle,
			'custom_product_data' => apply_filters( 'woocommerce_bundled_product_custom_data', array(
				'is_unavailable'  => 'yes',
				'is_required'     => $bundled_item->get_quantity( 'min', array( 'check_optional' => true ) ) > 0 ? 'yes' : 'no'
			), $bundled_item )
		), false, WC_PB()->plugin_path() . '/templates/' );
	}
}

/**
 * Bundled variation details.
 *
 * @param  int              $product_id
 * @param  WC_Bundled_Item  $bundled_item
 */
function wc_pb_template_single_variation( $product_id, $bundled_item ) {
	?><div class="woocommerce-variation single_variation bundled_item_cart_details"></div><?php
}

/**
 * Bundled variation template.
 *
 * @since  5.6.0
 *
 * @param  int              $product_id
 * @param  WC_Bundled_Item  $bundled_item
 */
function wc_pb_template_single_variation_template( $product_id, $bundled_item ) {

	wc_get_template( 'single-product/bundled-variation.php', array(
		'bundled_item'         => $bundled_item,
		'bundle_fields_prefix' => apply_filters( 'woocommerce_product_bundle_field_prefix', '', $bundled_item->get_bundle_id() ) // Filter documented in 'WC_PB_Cart::get_posted_bundle_configuration'.
	), false, WC_PB()->plugin_path() . '/templates/' );
}

/**
 * Echo opening tabular markup if necessary.
 *
 * @param  WC_Product_Bundle  $bundle
 */
function wc_pb_template_before_bundled_items( $bundle ) {

	$layout = $bundle->get_layout();

	if ( 'tabular' === $layout ) {

		$table_classes = array( 'bundled_products' );

		if ( false === $bundle->contains( 'visible' ) ) {
			$table_classes[] = 'bundled_products_hidden';
		}

		/**
		 * 'woocommerce_bundles_tabular_classes' filter.
		 *
		 * @since  5.10.1
		 *
		 * @param  array              $classes
		 * @param  WC_Product_Bundle  $bundle
		 */
		$table_classes = apply_filters( 'woocommerce_bundles_tabular_classes', $table_classes, $bundle );

		?><table cellspacing="0" class="<?php echo esc_attr( implode( ' ', $table_classes ) ); ?>">
			<thead>
				<th class="bundled_item_col bundled_item_images_head"></th>
				<th class="bundled_item_col bundled_item_details_head"><?php _e( 'Product', 'woocommerce-product-bundles' ); ?></th>
				<th class="bundled_item_col bundled_item_qty_head"><?php _e( 'Quantity', 'woocommerce-product-bundles' ); ?></th>
			</thead>
			<tbody><?php

	} elseif ( 'grid' === $layout ) {

		// Reset grid counter.
		WC_PB()->display->reset_grid_layout_pos();

		echo '<ul class="products bundled_products columns-' . esc_attr( WC_PB()->display->get_grid_layout_columns( $bundle ) ) . '">';
	}
}

/**
 * Echo closing tabular markup if necessary.
 *
 * @param  WC_Product_Bundle  $bundle
 */
function wc_pb_template_after_bundled_items( $bundle ) {

	$layout = $bundle->get_layout();

	if ( 'tabular' === $layout ) {
		echo '</tbody></table>';
	} elseif ( 'grid' === $layout ) {
		echo '</ul>';
	}
}

/**
 * Display bundled product attributes.
 *
 * @param  WC_Product  $product
 */
function wc_pb_template_bundled_item_attributes( $product ) {

	if ( $product->is_type( 'bundle' ) ) {

		$bundled_items = $product->get_bundled_items();

		if ( ! empty( $bundled_items ) ) {

			foreach ( $bundled_items as $bundled_item ) {

				$args = $bundled_item->has_attributes() ? $bundled_item->get_attribute_template_args() : false;

				if ( ! $args || empty( $args[ 'product_attributes' ] ) ) {
					continue;
				}

				wc_get_template( 'single-product/bundled-item-attributes.php', $args, false, WC_PB()->plugin_path() . '/templates/' );
			}
		}
	}
}

/**
 * Variation attribute options for bundled items. If:
 *
 * - only a single variation is active,
 * - all attributes have a defined value, and
 * - the single values are actually selected as defaults,
 *
 * ...then wrap the dropdown in a hidden div and show the single attribute value description before it.
 *
 * @param  array  $args
 */
function wc_pb_template_bundled_variation_attribute_options( $args ) {

	$bundled_item                = $args[ 'bundled_item' ];
	$variation_attribute_name    = $args[ 'attribute' ];
	$variation_attribute_options = $args[ 'options' ];

	/** Documented in 'WC_PB_Cart::get_posted_bundle_configuration'. */
	$bundle_fields_prefix = apply_filters( 'woocommerce_product_bundle_field_prefix', '', $bundled_item->get_bundle_id() );

	// The currently selected attribute option.
	$selected_option = isset( $_REQUEST[ $bundle_fields_prefix . 'bundle_attribute_' . sanitize_title( $variation_attribute_name ) . '_' . $bundled_item->get_id() ] ) ? wc_clean( wp_unslash( $_REQUEST[ $bundle_fields_prefix . 'bundle_attribute_' . sanitize_title( $variation_attribute_name ) . '_' . $bundled_item->get_id() ] ) ) : $bundled_item->get_selected_product_variation_attribute( $variation_attribute_name );

	$variation_attributes              = $bundled_item->get_product_variation_attributes();
	$configurable_variation_attributes = $bundled_item->get_product_variation_attributes( true );
	$html                              = '';

	// Fill required args.
	$args[ 'selected' ] = $selected_option;
	$args[ 'name' ]     = $bundle_fields_prefix . 'bundle_attribute_' . sanitize_title( $variation_attribute_name ) . '_' . $bundled_item->get_id();
	$args[ 'product' ]  = $bundled_item->product;
	$args[ 'id' ]       = sanitize_title( $variation_attribute_name ) . '_' . $bundled_item->get_id();

	// Render everything.
	if ( ! $bundled_item->display_product_variation_attribute_dropdown( $variation_attribute_name ) ) {

		$variation_attribute_value = '';

		// Get the singular option description.
		if ( taxonomy_exists( $variation_attribute_name ) ) {

			// Get terms if this is a taxonomy.
			$terms = wc_get_product_terms( $bundled_item->get_product_id(), $variation_attribute_name, array( 'fields' => 'all' ) );

			foreach ( $terms as $term ) {
				if ( $term->slug === sanitize_title( $selected_option ) ) {
					$variation_attribute_value = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );
					break;
				}
			}

		} else {

			foreach ( $variation_attribute_options as $option ) {

				if ( sanitize_title( $selected_option ) === $selected_option ) {
					$singular_found = $selected_option === sanitize_title( $option );
				} else {
					$singular_found = $selected_option === $option;
				}

				if ( $singular_found ) {
					$variation_attribute_value = esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) );
					break;
				}
			}
		}

		$html .= '<span class="bundled_variation_attribute_value">' . $variation_attribute_value . '</span>';

		// See https://github.com/woothemes/woocommerce/pull/11944 .
		$args[ 'show_option_none' ] = false;

		// Get the dropdowns markup.
		ob_start();
		wc_dropdown_variation_attribute_options( $args );
		$attribute_options = ob_get_clean();

		// Add the dropdown (hidden).
		$html .= '<div class="bundled_variation_attribute_options_wrapper" style="display:none;">' . $attribute_options . '</div>';

	} else {

		// Get the dropdowns markup.
		ob_start();
		wc_dropdown_variation_attribute_options( $args );
		$attribute_options = ob_get_clean();

		// Just render the dropdown.
		$html .= $attribute_options;
	}

	if ( count( $configurable_variation_attributes ) === count( $variation_attributes ) ) {
		$variation_attribute_keys = array_keys( $variation_attributes );
		// ...and add the reset-variations link.
		if ( end( $variation_attribute_keys ) === $variation_attribute_name ) {
			// Change 'reset_bundled_variations_fixed' to 'reset_bundled_variations' if you want the 'Clear' link to slide in/out of view.
			$html .= wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<div class="reset_bundled_variations_fixed"><a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a></div>' ) );
		}
	}

	return $html;
}
