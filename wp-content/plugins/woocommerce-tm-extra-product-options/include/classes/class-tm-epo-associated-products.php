<?php
/**
 * Extra Product Options Associated Products Functionality
 *
 * @package Extra Product Options/Classes
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_Associated_Products {

	private $discount = '';
	private $discount_type = '';

	/**
	 * The single instance of the class
	 *
	 * @since 5.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 5.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 5.0
	 */
	public function __construct() {

		// Modify cart 
		add_filter( 'woocommerce_add_cart_item', array( $this, 'woocommerce_add_cart_item' ), 11, 1 );
		// Load cart data on every page load
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'woocommerce_get_cart_item_from_session' ), 9999, 3 );
		// Add associated products (from elements) to the cart.
		add_action( 'woocommerce_add_to_cart', array( $this, 'associated_woocommerce_add_to_cart' ), 8, 6 );
		// Remove associated products when the parent gets removed.
		add_action( 'woocommerce_remove_cart_item', array( $this, 'associated_woocommerce_remove_cart_item' ), 10, 2 );
		// Restore associated products when the parent gets restored.
		add_action( 'woocommerce_restore_cart_item', array( $this, 'associated_woocommerce_restore_cart_item' ), 10, 2 );
		// Clear notices
		add_action( 'init', array( $this, 'associated_clear_removed_notice' ) );
		// Validate quantity update in cart.
		add_filter( 'woocommerce_update_cart_validation', array( $this, 'woocommerce_update_cart_validation' ), 10, 4 );
		// Sync associated products quantity input
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'associated_woocommerce_cart_item_quantity' ), 10, 2 );
		// Sync associated products quantity with main product.
		add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'woocommerce_after_cart_item_quantity_update' ), 1, 2 );
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.7', '<' ) ) {
			add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'woocommerce_after_cart_item_quantity_update' ) );
		}
		// Make sure products marked as associated have a parent
		add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'associated_woocommerce_cart_loaded_from_session' ), 99999 );

		// Associated product cart remove link
		add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'associated_woocommerce_cart_item_remove_link' ), 10, 2 );
		// Associated product table item classes
		add_filter( 'woocommerce_cart_item_class', array( $this, 'associated_woocommerce_cart_item_class' ), 10, 2 );
		// Wrap associated products name in cart
		add_filter( 'woocommerce_cart_item_name', array( $this, 'associated_woocommerce_cart_item_name' ), 99999, 3 );
		// Wrap associated products price in cart
		add_filter( 'woocommerce_cart_item_price', array( $this, 'associated_woocommerce_cart_item_price' ), 99999, 3 );
		// Wrap associated products subtotal in cart
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'associated_woocommerce_cart_item_price' ), 99999, 3 );
		// Wrap associated products subtotal in checkout
		add_filter( 'woocommerce_checkout_item_subtotal', array( $this, 'associated_woocommerce_cart_item_price' ), 99999, 3 );
		// Associated product table item classes in mini cart
		add_filter( 'woocommerce_mini_cart_item_class', array( $this, 'associated_woocommerce_cart_item_class' ), 99999, 2 );
		// Wrap associated products price in mini cart
		add_filter( 'woocommerce_widget_cart_item_quantity', array( $this, 'associated_woocommerce_widget_cart_item_quantity' ), 99999, 3 );
		// Make cart item count not count associated products
		add_filter( 'woocommerce_cart_contents_count', array( $this, 'associated_woocommerce_cart_contents_count' ) );

		// Edit cart functionality
		add_action( 'woocommerce_add_to_cart', array( $this, 'woocommerce_add_to_cart' ), 10, 6 );

		// Add meta to order
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'woocommerce_checkout_create_order_line_item' ), 50, 3 );

		// Wrap associated products subtotal in order.
		add_filter( 'woocommerce_order_formatted_line_subtotal', array( $this, 'associated_woocommerce_order_item_price' ), 10, 3 );
		// Add table item classes.
		add_filter( 'woocommerce_order_item_class', array( $this, 'woocommerce_order_item_class' ), 10, 3 );
		// Add the label name to associated products at the order-details template.
		add_filter( 'woocommerce_order_item_name', array( $this, 'woocommerce_order_item_name' ), 10, 2 );
		// Delete associated product quantity from order-details template
		add_filter( 'woocommerce_order_item_quantity_html', array( $this, 'woocommerce_order_item_quantity_html' ), 10, 2 );
		// Filter order item count removing associated products
		add_filter( 'woocommerce_get_item_count', array( $this, 'woocommerce_get_item_count' ), 10, 3 );

		// Add associated product weights to the main product
		add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'woocommerce_cart_shipping_packages' ), 5 );

		add_action( 'wc_epo_associated_product_display', array( $this, 'wc_epo_associated_product_display' ), 10, 5 );

		add_action( 'wp_ajax_nopriv_wc_epo_get_associated_product_html', array( $this, 'wc_epo_get_associated_product_html' ) );
		add_action( 'wp_ajax_wc_epo_get_associated_product_html', array( $this, 'wc_epo_get_associated_product_html' ) );

	}

	/**
	 * Hook for displaying extra options
	 *
	 * @since 5.0
	 */
	public function wc_epo_get_associated_product_html() {

		global $tm_is_ajax;

		$tm_is_ajax  = TRUE;
		$json_result = array(
			'result' => 0,
			'html'   => '',
		);

		if ( isset( $_POST['layout_mode'] ) && isset( $_POST['product_id'] ) && isset( $_POST['name'] ) && isset( $_POST['uniqid'] ) ) {

			$name                              = $_POST['name'];
			$uniqid                            = $_POST['uniqid'];
			$quantity_min                      = isset( $_POST['quantity_min'] ) ? $_POST['quantity_min'] : '';
			$quantity_max                      = isset( $_POST['quantity_max'] ) ? $_POST['quantity_max'] : '';
			$priced_individually               = isset( $_POST['priced_individually'] ) ? $_POST['priced_individually'] : '';
			$discount               		   = isset( $_POST['discount'] ) ? $_POST['discount'] : '';
			$discount_type                     = isset( $_POST['discount_type'] ) ? $_POST['discount_type'] : '';
			$layout_mode                       = $_POST['layout_mode'];
			$product_id                        = $_POST['product_id'];
			$product                           = wc_get_product( $product_id );
			$product_list                      = array();
			$product_list_available_variations = array();

			if ( ! empty( $product ) && is_object( $product ) ) {

				$type                 = themecomplete_get_product_type( $product );
				$attributes           = array();
				$available_variations = array();

				if ( $type === "variable" ) {
					if ( is_callable( array( $product, 'get_variation_attributes' ) ) ) {
						$attributes           = $product->get_variation_attributes();
						$get_variations       = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
						$available_variations = $get_variations ? $product->get_available_variations() : FALSE;

						$product_list[ $product_id ] = $attributes;

						$variations_json                                  = wp_json_encode( $available_variations );
						$variations_attr                                  = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', TRUE );
						$product_list_available_variations[ $product_id ] = $variations_attr;
					}
				} else {
					$product_list[ $product_id ]                      = array();
					$product_list_available_variations[ $product_id ] = "";
				}

				$__min_value = $quantity_min;
				$__max_value = $quantity_max;

				if ( $__min_value !== '' ) {
					$__min_value = floatval( $__min_value );
				} else {
					$__min_value = 0;
				}
				if ( $__min_value === '' ) {
					$__min_value = 0;	
				}
				if ( $__max_value != '' ) {
					$__max_value = floatval( $__max_value );
				}

				if ( is_numeric( $__min_value ) && is_numeric( $__max_value ) ) {
					if ( $__min_value > $__max_value ) {
						$__max_value = $__min_value + $__step;
					}
				}

				$template = "template-item";

				$args = array(
					'tm_element_settings'               => array( 'uniqid' => $uniqid ),
					'template'                          => $template,
					'quantity_min'                      => $__min_value,
					'quantity_max'                      => $__max_value,
					'priced_individually'               => $priced_individually,
					'discount'                          => $discount,
					'discount_type'                     => $discount_type,
					"name"                              => $name,
					"product_id"                        => $product_id,
					"product_list"                      => $product_list,
					"product_list_available_variations" => $product_list_available_variations,
				);
				ob_start();
				wc_get_template(
					'products/template-container-ajax.php',
					$args,
					THEMECOMPLETE_EPO_DISPLAY()->get_namespace(),
					THEMECOMPLETE_EPO_TEMPLATE_PATH
				);
				$json_result["html"]   = ob_get_clean();
				$json_result["result"] = 200;

			}

		}

		wp_send_json( $json_result );
		die();

	}

	/**
	 * Apply discount to the option price
	 *
	 * @since 5.0.8
	 */
	public function wc_epo_apply_discount( $price = "", $original_price = "" ) {
		if (!is_array($price)){
			return $this->get_discounted_price( $price, $this->discount, $this->discount_type);	
		} else {
			foreach ($price as $key => $value) {
				$price[$key] = $this->get_discounted_price( $value, $this->discount, $this->discount_type);	
			}
			return $price;
		}

		return $price;		

	}

	/**
	 * Hook for displaying extra options
	 *
	 * @since 5.0
	 */
	public function wc_epo_associated_product_display( $product, $uniqid, $per_product_pricing = FALSE, $discount = "", $discount_type = "" ) {

		if ( $product ) {
			$product_id = themecomplete_get_id($product);
			if (!$per_product_pricing){
				$per_product_pricing = 0;
			} else {
				$per_product_pricing = 1;
			}

			$epo_id = rand();

			?>
            <div class="tc-extra-product-options-inline" data-epo-id="<?php echo esc_attr($epo_id); ?>" data-product-id="<?php echo esc_attr($product_id); ?>">
			<?php
			$uniqid = str_replace( array( ".", " ", "[" ), "", $uniqid );
			if ( class_exists( 'Normalizer' ) ) {
				$uniqid = Normalizer::normalize( $uniqid );
			}
			if ($discount){
				$this->discount = $discount;
				$this->discount_type = $discount_type;
				add_filter("wc_epo_apply_discount", array($this, "wc_epo_apply_discount"), 10, 2);
			}
			THEMECOMPLETE_EPO()->is_associated = TRUE;
			THEMECOMPLETE_EPO()->set_inline_epo( TRUE );
			THEMECOMPLETE_EPO_DISPLAY()->set_discount( $discount, $discount_type );
			THEMECOMPLETE_EPO_DISPLAY()->set_epo_internal_counter( $epo_id );
			THEMECOMPLETE_EPO_DISPLAY()->tm_epo_fields( $product, $uniqid );
			THEMECOMPLETE_EPO_DISPLAY()->tm_epo_totals( $product, $uniqid );
			THEMECOMPLETE_EPO_DISPLAY()->tm_add_inline_style();
			THEMECOMPLETE_EPO()->set_inline_epo( FALSE );
			THEMECOMPLETE_EPO_DISPLAY()->set_discount( '', '' );
			THEMECOMPLETE_EPO_DISPLAY()->restore_epo_internal_counter();
			THEMECOMPLETE_EPO()->is_associated = FALSE;
			if ($discount_type){
				$this->discount = '';
				$this->discount_type = '';
				remove_filter("wc_epo_apply_discount", array($this, "wc_epo_apply_discount"), 10);
			}
			?>
            </div><?php
		}
	}

	/**
	 * Add associated product weights to the main product
	 *
	 * @since 5.0
	 */
	public function woocommerce_cart_shipping_packages( $packages ) {

		if ( ! empty( $packages ) ) {

			foreach ( $packages as $package_key => $package ) {

				if ( ! empty( $package['contents'] ) ) {

					foreach ( $package['contents'] as $cart_item_key => $cart_item_data ) {

						if ( isset( $cart_item_data['associated_products'] ) && ! empty( $cart_item_data['associated_products'] ) ) {

							$main_product     = unserialize( serialize( $cart_item_data['data'] ) );
							$main_product_qty = $cart_item_data['quantity'];

							if ( $main_product->needs_shipping() ) {

								$associated_weight = 0.0;
								$associated_value  = 0.0;

								$main_product_totals = array(
									'line_subtotal'     => $cart_item_data['line_subtotal'],
									'line_total'        => $cart_item_data['line_total'],
									'line_subtotal_tax' => $cart_item_data['line_subtotal_tax'],
									'line_tax'          => $cart_item_data['line_tax'],
									'line_tax_data'     => $cart_item_data['line_tax_data']
								);

								$associated_cart_keys = $this->get_associated_cart_keys( $cart_item_data, WC()->cart->cart_contents );

								foreach ( $associated_cart_keys as $associated_cart_key ) {

									$associated_cart_data      = WC()->cart->cart_contents[ $associated_cart_key ];
									$associated_product        = $associated_cart_data['data'];
									$associated_product_qty    = $associated_cart_data['quantity'];
									$associated_product_value  = isset( $associated_product->associated_value ) ? $associated_product->associated_value : 0.0;
									$associated_product_weight = isset( $associated_product->associated_weight ) ? $associated_product->associated_weight : 0.0;

									if ( $associated_product_value ) {

										$associated_value += $associated_product_value * $associated_product_qty;

										$main_product_totals['line_subtotal']     += $associated_cart_data['line_subtotal'];
										$main_product_totals['line_total']        += $associated_cart_data['line_total'];
										$main_product_totals['line_subtotal_tax'] += $associated_cart_data['line_subtotal_tax'];
										$main_product_totals['line_tax']          += $associated_cart_data['line_tax'];

										$packages[ $package_key ]['contents_cost'] += $associated_cart_data['line_total'];

										$associated_line_tax_data = $associated_cart_data['line_tax_data'];

										$main_product_totals['line_tax_data']['total']    = array_merge( $main_product_totals['line_tax_data']['total'], $associated_line_tax_data['total'] );
										$main_product_totals['line_tax_data']['subtotal'] = array_merge( $main_product_totals['line_tax_data']['subtotal'], $associated_line_tax_data['subtotal'] );
									}

									if ( $associated_product_weight ) {
										$associated_weight += $associated_product_weight * $associated_product_qty;
									}
								}

								if ( $associated_value > 0 ) {
									$main_product_price = $main_product->get_price( 'edit' );
									$main_product->set_price( (double) $main_product_price + $associated_value / $main_product_qty );
								}

								$packages[ $package_key ]['contents'][ $cart_item_key ] = array_merge( $cart_item_data, $main_product_totals );

								if ( $associated_weight > 0 ) {
									$main_product_weight = $main_product->get_weight( 'edit' );
									$main_product->set_weight( (double) $main_product_weight + $associated_weight / $main_product_qty );
								}

								$packages[ $package_key ]['contents'][ $cart_item_key ]['data'] = $main_product;
							}
						}
					}
				}
			}
		}

		return $packages;
	}

	/**
	 * Validates in-cart component quantity changes.
	 *
	 * @since 5.0
	 */
	public function woocommerce_update_cart_validation( $passed, $cart_item_key, $cart_item, $quantity ) {

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {

			$parent = WC()->cart->cart_contents[ $cart_item['associated_parent'] ];

			$associated_id = array_search( $cart_item_key, $parent['associated_products'] );

			if ( $associated_id === FALSE ) {
				return FALSE;
			}

			$parent_key      = $cart_item['associated_parent'];
			$parent_quantity = intval( $parent['quantity'] );
			$min_quantity    = intval( $cart_item['tmproducts'][ $associated_id ]['quantity_min'] );
			$max_quantity    = $cart_item['tmproducts'][ $associated_id ]['quantity_max'] ? intval( $parent_quantity * $cart_item['tmproducts'][ $associated_id ]['quantity_max'] ) : '';

			if ( $quantity < $min_quantity ) {

				wc_add_notice( sprintf( __( 'The quantity of &quot;%s&quot; cannot be lower than %d.', 'woocommerce-tm-extra-product-options' ), $cart_item['data']->get_title(), $min_quantity ), 'error' );

				return FALSE;

			} elseif ( $max_quantity && $quantity > $max_quantity ) {

				wc_add_notice( sprintf( __( 'The quantity of &quot;%s&quot; cannot be higher than %d.', 'woocommerce-tm-extra-product-options' ), $cart_item['data']->get_title(), $max_quantity ), 'error' );

				return FALSE;

			} elseif ( $quantity % $parent_quantity != 0 ) {

				wc_add_notice( sprintf( __( 'The quantity of &quot;%s&quot; must be entered in multiples of %d.', 'woocommerce-tm-extra-product-options' ), $cart_item['data']->get_title(), $parent_quantity ), 'error' );

				return FALSE;

			} else {

				WC()->cart->cart_contents[ $parent_key ]['tmproducts'][ $associated_id ]['quantity'] = $quantity / $parent_quantity;

				$associated_cart_keys = $this->get_associated_cart_keys( $parent, WC()->cart->cart_contents );
				foreach ( $associated_cart_keys as $associated_key_id => $associated_key ) {
					WC()->cart->cart_contents[ $associated_key ]['tmproducts'][ $associated_key_id ]['quantity'] = $quantity / $parent_quantity;
				}

			}
		}

		return $passed;
	}

	/**
	 * Filter order item count removing associated products
	 *
	 * @since 5.0
	 */
	public function woocommerce_get_item_count( $count, $type, $order ) {

		$remove = 0;

		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			foreach ( $order->get_items() as $item ) {
				if ( isset( $item['_associated_key'] ) && $item['_associated_key'][0] !== "" ) {
					$remove += $item->get_quantity();
				}
			}
		}

		return intval( $count ) - intval( $remove );
	}

	/**
	 * Delete associated product quantity from order-details template
	 * Quantity is inserted into the product name by 'woocommerce_order_item_name'.
	 *
	 * @since 5.0
	 */
	public function woocommerce_order_item_quantity_html( $content, $item ) {

		if ( isset( $item['_associated_key'] ) && $item['_associated_key'][0] !== "" ) {
			$content = '';
		}

		return $content;
	}

	/**
	 * Add the label name to associated products at the order-details template.
	 *
	 * @since 5.0
	 */
	public function woocommerce_order_item_class( $class, $item, $order ) {

		if ( isset( $item['_associated_key'] ) && $item['_associated_key'][0] !== "" ) {
			$class .= ' tc-associated-table-product';
		} else if ( isset( $item['_tmproducts'] ) && $item['_tmproducts'][0] !== "" ) {
			$class .= ' tc-container-table-product';
		}

		return $class;

	}

	/**
	 * Add the label name to associated products at the order-details template.
	 *
	 * @since 5.0
	 */
	public function woocommerce_order_item_name( $content, $item ) {

		if ( isset( $item['_associated_key'] ) && $item['_associated_key'][0] !== "" ) {

			$qty = '';

			if ( did_action( 'woocommerce_view_order' ) ||
			     did_action( 'woocommerce_thankyou' ) ||
			     did_action( 'before_woocommerce_pay' ) ||
			     did_action( 'woocommerce_account_view-subscription_endpoint' ) ) {

				$qty = '<strong class="associated-product-quantity"> &times; '
				       . $item['qty']
				       . '</strong>';

			}

			if ( isset( $item["_associated_name"] ) && $item['_associated_name'][0] !== "" ) {
				$content = '<div class="tc-associated-table-product-name">' . $item["_associated_name"][0] . '</div>' . $content;
			}

			$content = '<div class="tc-associated-table-product-indent">' . $content . $qty . '</div>';
		}

		return $content;

	}

	/**
	 * Adds meta data to the order - WC >= 2.7 (crud)
	 *
	 * @since 5.0
	 */
	public function woocommerce_checkout_create_order_line_item( $item, $cart_item_key, $values ) {

		if ( isset( $values['associated_parent'] ) && ! empty( $values['associated_parent'] ) ) {

			$item->add_meta_data( '_associated_name', array( $values["tmproducts"][ $values['associated_key'] ]["name"] ) );
			$item->add_meta_data( '_associated_key', array( $values['associated_key'] ) );
			$item->add_meta_data( '_priced_individually', array( $values['associated_priced_individually'] ) );
			$item->add_meta_data( '_required', array( $values['associated_required'] ) );
		}

		if ( ! empty( $values['associated_products'] ) ) {
			if ( is_array( $values['tmproducts'] ) ) {
				$item->add_meta_data( '_tmproducts', array( array_map( function ( $v ) {
					return $v['product_id'];
				}, $values['tmproducts'] )
				) );
			}
		}

	}

	/**
	 * Wrap associated products in cart
	 *
	 * @since 5.0
	 */
	public function associated_woocommerce_order_item_price( $price, $item, $order ) {

		if ( isset( $item['_associated_key'] ) && $item['_associated_key'][0] !== "" ) {
			$priced_individually = isset( $item['_priced_individually'] ) ? $item['_priced_individually'][0] : "";

			if ( empty( $priced_individually ) && empty( $item->get_subtotal( 'edit' ) ) ) {
				$price = '';
			} elseif ( $price ) {
				$price = '<span class="tc-associated-table-product-price">' . $price . '</span>';
			}

		}

		return $price;

	}

	/**
	 * Make sure products marked as associated have a parent
	 *
	 * @since 5.0
	 */
	public function associated_woocommerce_cart_loaded_from_session( $cart ) {

		$cart_contents = $cart->cart_contents;

		if ( ! empty( $cart_contents ) ) {

			foreach ( $cart_contents as $key => $value ) {

				if ( isset( $value['associated_parent'] ) && ! empty( $value['associated_parent'] ) ) {

					$parent = array();
					if ( isset( $cart_contents[ $value['associated_parent'] ] ) ) {
						$parent = $cart_contents[ $value['associated_parent'] ];
					}

					if ( ! $parent ||
					     ! isset( $parent['associated_products'] ) ||
					     ! is_array( $parent['associated_products'] ) ||
					     ! in_array( $key, $parent['associated_products'] ) ) {
						unset( WC()->cart->cart_contents[ $key ] );
					}

				}

			}

		}

	}

	/**
	 * Make cart item count not count associated products
	 *
	 * @since 5.0
	 */
	public function associated_woocommerce_cart_contents_count( $count ) {

		$cart                      = WC()->cart->get_cart();
		$associated_products_count = 0;

		foreach ( $cart as $key => $value ) {

			if ( isset( $value['associated_parent'] ) && ! empty( $value['associated_parent'] ) ) {
				$associated_products_count += $value['quantity'];
			}
		}

		return absint( $count ) - absint( $associated_products_count );
	}

	/**
	 * Wrap associated products in mini cart
	 *
	 * @since 5.0
	 */
	public function associated_woocommerce_widget_cart_item_quantity( $price, $cart_item, $cart_item_key ) {

		remove_filter( 'woocommerce_cart_item_price', array( $this, 'associated_woocommerce_cart_item_price' ), 99999 );

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {
			if ( empty( $cart_item['associated_priced_individually'] ) && empty( $cart_item['line_subtotal'] ) ) {
				$price = '';
			} elseif ( $price ) {
				$price = '<span class="tc-associated-table-product-price">' . $price . '</span>';
			}
		}

		return $price;

	}

	/**
	 * Wrap associated products in cart
	 *
	 * @since 5.0
	 */
	public function associated_woocommerce_cart_item_price( $price, $cart_item, $cart_item_key ) {

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {

			if ( empty( $cart_item['associated_priced_individually'] ) && empty( $cart_item['line_subtotal'] ) ) {
				$price = '';
			} elseif ( $price ) {
				$price = '<span class="tc-associated-table-product-price">' . $price . '</span>';
			}

		}

		return $price;

	}

	/**
	 * Wrap associated products in cart
	 *
	 * @since 5.0
	 */
	public function associated_woocommerce_cart_item_name( $title = "", $cart_item = array(), $cart_item_key = "" ) {

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {

			if ( isset( $cart_item["associated_label"] ) && $cart_item['associated_label'] !== "" ) {
				$title = '<div class="tc-associated-table-product-name">' . $cart_item["associated_label"] . '</div>' . $title;
			}

			$title = '<div class="tc-associated-table-product-indent">' . $title . '</div>';

		}

		return $title;

	}

	/**
	 * Associated product cart remove link
	 *
	 * @since 5.0
	 */
	public function associated_woocommerce_cart_item_remove_link( $link, $cart_item_key ) {

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['associated_parent'] ) && ! empty( WC()->cart->cart_contents[ $cart_item_key ]['associated_parent'] ) && isset( WC()->cart->cart_contents[ $cart_item_key ]['associated_required'] ) && ! empty( WC()->cart->cart_contents[ $cart_item_key ]['associated_required'] ) ) {

			$parent_key = WC()->cart->cart_contents[ $cart_item_key ]['associated_parent'];

			if ( isset( WC()->cart->cart_contents[ $parent_key ] ) ) {
				return '';
			}
		}

		return $link;
	}

	/**
	 * Sync associated products quantity with main product
	 *
	 * @since 5.0
	 */
	public function woocommerce_after_cart_item_quantity_update( $cart_item_key, $quantity = 0 ) {

		if ( ! empty( WC()->cart->cart_contents[ $cart_item_key ] ) ) {

			$associated_cart_keys = $this->get_associated_cart_keys( WC()->cart->cart_contents[ $cart_item_key ], WC()->cart->cart_contents );

			if ( ! empty( $associated_cart_keys ) && is_array( $associated_cart_keys ) ) {

				if ( $quantity == 0 || $quantity < 0 ) {
					$quantity = 0;
				} else {
					$quantity = intval( WC()->cart->cart_contents[ $cart_item_key ]['quantity'] );
				}

				foreach ( $associated_cart_keys as $associated_key_id => $associated_key ) {

					$associated_data = WC()->cart->cart_contents[ $associated_key ];

					if ( ! isset( $associated_data['data'] ) || ! $associated_data['data'] ) {
						continue;
					}
					if ( $associated_data['data']->is_sold_individually() && $quantity > 0 ) {

						WC()->cart->set_quantity( $associated_key, 1, FALSE );

					} else {

						$associated_quantity = intval( $associated_data['tmproducts'][ $associated_key_id ]['quantity'] );
						WC()->cart->set_quantity( $associated_key, $associated_quantity * $quantity, FALSE );
					}

				}

			}

		}

	}

	/**
	 * Sync associated products quantity input
	 *
	 * @since 5.0
	 */
	public function associated_woocommerce_cart_item_quantity( $quantity, $cart_item_key ) {

		$cart_item = WC()->cart->cart_contents[ $cart_item_key ];

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {

			$parent = WC()->cart->cart_contents[ $cart_item['associated_parent'] ];

			$associated_id = array_search( $cart_item_key, $parent['associated_products'] );

			if ( $associated_id === FALSE ) {
				return $quantity;
			}

			if ( $cart_item['tmproducts'][ $associated_id ]['quantity_min'] === $cart_item['tmproducts'][ $associated_id ]['quantity_max'] ) {

				$quantity = $cart_item['quantity'];

			} else {

				$parent_quantity = $parent['quantity'];
				$max_stock       = $cart_item['data']->managing_stock() && ! $cart_item['data']->backorders_allowed() ? $cart_item['data']->get_stock_quantity() : '';
				$max_stock       = $max_stock === NULL ? '' : $max_stock;

				if ( '' !== $max_stock ) {
					$max_qty = '' !== $cart_item['tmproducts'][ $associated_id ]['quantity_max'] ? min( $max_stock, $parent_quantity * $cart_item['tmproducts'][ $associated_id ]['quantity_max'] ) : $max_stock;
				} else {
					$max_qty = '' !== $cart_item['tmproducts'][ $associated_id ]['quantity_max'] ? $parent_quantity * $cart_item['tmproducts'][ $associated_id ]['quantity_max'] : '';
				}

				$min_qty = $parent_quantity * $cart_item['tmproducts'][ $associated_id ]['quantity_min'];

				if ( ( $max_qty > $min_qty || '' === $max_qty ) && ! $cart_item['data']->is_sold_individually() ) {

					$quantity = woocommerce_quantity_input( array(
						'input_name'  => "cart[" . $cart_item_key . "][qty]",
						'input_value' => $cart_item['quantity'],
						'min_value'   => $min_qty,
						'max_value'   => $max_qty,
						'step'        => $parent_quantity
					), $cart_item['data'], FALSE );

				} else {
					$quantity = $cart_item['quantity'];
				}
			}
		}

		return $quantity;
	}

	/**
	 * Associated product table item classes
	 *
	 * @since 5.0
	 */
	public function associated_woocommerce_cart_item_class( $class, $cart_item ) {

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {
			$class .= ' tc-associated-table-product';
		} elseif ( isset( $cart_item['associated_products'] ) && ! empty( $cart_item['associated_products'] ) ) {
			$class .= ' tc-container-table-product';
		}

		return $class;
	}

	/**
	 * Clear notices
	 *
	 * @since 5.0
	 */
	public function associated_clear_removed_notice() {

		if ( is_admin() || ! function_exists( 'WC' ) ) {
			return;
		}

		$notices = isset( WC()->session ) ? WC()->session->get( 'wc_notices', array() ) : array();

		if ( isset( $notices['EPO_REMOVED_REQUIRED_ASSOCIATED_PRODUCT'] ) ) {
			if ( isset( $notices['success'] ) && is_array( $notices['success'] ) ) {
				$last = $notices['success'][ count( $notices['success'] ) - 1 ];
				if ( is_array( $last ) && isset( $last['notice'] ) ) {
					$last['notice']                                         = $notices['EPO_REMOVED_REQUIRED_ASSOCIATED_PRODUCT'][0]['notice'];
					$notices['success'][ count( $notices['success'] ) - 1 ] = $last;

				}
			}

			unset( $notices['EPO_REMOVED_REQUIRED_ASSOCIATED_PRODUCT'] );

			WC()->session->set( 'wc_notices', $notices );
		}

	}

	/**
	 * Fetch associated product cart keys
	 *
	 * @since 5.0
	 */
	public function get_associated_cart_keys( $cart_item, $cart_contents = FALSE ) {
		if ( ! $cart_contents ) {
			$cart_contents = WC()->cart->cart_contents;
		}

		$associated_cart_keys = array();

		if ( isset( $cart_item['associated_products'] ) && isset( $cart_item['tmproducts'] ) && ! empty( $cart_item['tmproducts'] ) && is_array( $cart_item['tmproducts'] ) ) {

			$associated_products = $cart_item['associated_products'];

			if ( ! empty( $cart_contents ) && ! empty( $associated_products ) && is_array( $associated_products ) ) {

				foreach ( $associated_products as $key ) {
					if ( isset( $cart_contents[ $key ] ) ) {
						$associated_cart_keys[ $key ] = $cart_contents[ $key ];
					}
				}

			}
		}

		return array_keys( $associated_cart_keys );
	}

	/**
	 * Remove associated products when the parent gets removed.
	 *
	 * @since 5.0
	 */
	public function associated_woocommerce_remove_cart_item( $cart_item_key, $cart ) {

		// This is an associated product
		if ( isset( $cart->cart_contents[ $cart_item_key ]['associated_parent'] ) ) {

			// If it is required remove all other associated products and the parent product
			if ( isset( $cart->cart_contents[ $cart_item_key ]['associated_required'] ) && ! empty( $cart->cart_contents[ $cart_item_key ]['associated_required'] ) ) {

				$associated_parent_key = $cart->cart_contents[ $cart_item_key ]['associated_parent'];

				// Remove all other associated products
				if ( isset( $cart->cart_contents[ $associated_parent_key ] ) ) {
					$cart_keys = $this->get_associated_cart_keys( $cart->cart_contents[ $associated_parent_key ], $cart->cart_contents );

					foreach ( $cart_keys as $associated_cart_key ) {
						if ( ! isset( $cart->cart_contents[ $associated_cart_key ] ) ) {
							continue;
						}

						unset( WC()->cart->cart_contents[ $associated_cart_key ] );
					}
				}

				// Remove parent product
				if ( isset( $cart->cart_contents[ $associated_parent_key ] ) ) {

					$product = wc_get_product( $cart->cart_contents[ $associated_parent_key ]['product_id'] );

					/* translators: %s: Item name. */
					$item_removed_title = $product ? $product->get_name() : "";

					/* Translators: %s Product title. */
					$removed_notice = sprintf( __( '%s removed along with all of its associated products!', 'woocommerce-tm-extra-product-options' ), $item_removed_title );

					wc_add_notice( $removed_notice, 'EPO_REMOVED_REQUIRED_ASSOCIATED_PRODUCT' );

					unset( WC()->cart->cart_contents[ $associated_parent_key ] );
				}

				unset( WC()->cart->removed_cart_contents[ $cart_item_key ] );

			}

			// This is a parent product
		} else if ( isset( $cart->removed_cart_contents[ $cart_item_key ]['associated_products'] ) && ! empty( $cart->removed_cart_contents[ $cart_item_key ]['associated_products'] ) && is_array( $cart->removed_cart_contents[ $cart_item_key ]['associated_products'] ) ) {

			$associated_cart_keys = $this->get_associated_cart_keys( $cart->removed_cart_contents[ $cart_item_key ], $cart->cart_contents );

			// Remove all other associated products
			foreach ( $associated_cart_keys as $associated_cart_key ) {
				if ( ! isset( $cart->cart_contents[ $associated_cart_key ] ) ) {
					continue;
				}
				$remove                                                   = $cart->cart_contents[ $associated_cart_key ];
				WC()->cart->removed_cart_contents[ $associated_cart_key ] = $remove;

				unset( WC()->cart->cart_contents[ $associated_cart_key ] );
			}

		}
	}

	/**
	 * Restore associated products when the parent gets restored.
	 *
	 * @since 5.0
	 */
	public function associated_woocommerce_restore_cart_item( $cart_item_key, $cart ) {

		if ( isset( $cart->cart_contents[ $cart_item_key ]['associated_parent'] ) && ! empty( $cart->cart_contents[ $cart_item_key ]['associated_parent'] ) ) {

			$cart_item_data = $cart->cart_contents[ $cart_item_key ];

			$position       = array_search( $cart_item_data['associated_parent'], array_keys( $cart->cart_contents ) );
			$position       = (int) $position + (int) $cart_item_data['associated_key'] + 1;
			$array          = $cart->cart_contents;
			$previous_items = array_slice( $array, 0, $position, TRUE );
			$next_items     = array_slice( $array, $position, NULL, TRUE );

			$item = array( $cart_item_key => $cart_item_data );

			WC()->cart->cart_contents = $previous_items + $item + $next_items;

		} elseif ( isset( $cart->cart_contents[ $cart_item_key ]['associated_products'] ) && ! empty( $cart->cart_contents[ $cart_item_key ]['associated_products'] ) && is_array( $cart->cart_contents[ $cart_item_key ]['associated_products'] ) ) {

			$cart_keys = $this->get_associated_cart_keys( $cart->cart_contents[ $cart_item_key ], $cart->removed_cart_contents );

			foreach ( $cart_keys as $associated_cart_key ) {

				$remove                                      = $cart->removed_cart_contents[ $associated_cart_key ];
				$cart->cart_contents[ $associated_cart_key ] = $remove;

				do_action( 'woocommerce_restore_cart_item', $associated_cart_key, $cart );

				unset( WC()->cart->removed_cart_contents[ $associated_cart_key ] );
			}
		}
	}

	/**
	 * Add associated products (from elements) to the cart.
	 *
	 * @since 5.0
	 */
	public function associated_woocommerce_add_to_cart( $parent_cart_key, $parent_id, $parent_quantity, $variation_id, $variation, $cart_item_data ) {

		if ( ! did_action( 'woocommerce_cart_loaded_from_session' ) ) {
			return;
		}

		// Check to see if there are associated product to add
		if ( isset( $cart_item_data['tmproducts'] ) && ! empty( $cart_item_data['tmproducts'] ) && is_array( $cart_item_data['tmproducts'] ) ) {

			// Prevent adding the same associated product for the same parent product
			foreach ( WC()->cart->cart_contents as $cart_key => $cart_value ) {
				if ( isset( $cart_value['tmproducts'] ) && isset( $cart_value['associated_parent'] ) && $cart_value['associated_parent'] == $parent_cart_key ) {
					return;
				}
			}

			// Required to allow a different version of the same product to be added to the cart
			$associated_cart_data = array(
				'associated_parent' => $parent_cart_key,
				'tmproducts'        => $cart_item_data['tmproducts']
			);

			foreach ( $cart_item_data['tmproducts'] as $key => $associated_data ) {

				$associated_item_cart_data = $associated_cart_data;

				$associated_item_cart_data['associated_key']                  = $key;
				$associated_item_cart_data['associated_required']             = $associated_data['required'];
				$associated_item_cart_data['associated_shipped_individually'] = $associated_data['shipped_individually'];
				$associated_item_cart_data['associated_priced_individually']  = $associated_data['priced_individually'];
				$associated_item_cart_data['associated_maintain_weight']      = $associated_data['maintain_weight'];
				$associated_item_cart_data['associated_uniqid']               = $associated_data['section'];
				$associated_item_cart_data['associated_label']                = $associated_data['section_label'];
				$associated_item_cart_data['associated_discount']             = $associated_data['discount'];
				$associated_item_cart_data['associated_discount_type']        = $associated_data['discount_type'];

				$associated_product_id = $associated_data['product_id'];
				$variation_id          = '';
				$variations            = array();

				if ( '' === $associated_product_id ) {
					continue;
				}

				$associated_product = wc_get_product( $associated_product_id );

				if ( ! $associated_product ) {
					continue;
				}

				// Only allow simple or variable products
				if ( ! ( $associated_product->is_type( 'simple' ) || $associated_product->is_type( 'variable' ) || $associated_product->is_type( 'variation' ) ) ) {
					continue;
				}

				$item_quantity = $associated_data['quantity'];
				$quantity      = $associated_product->is_sold_individually() ? 1 : $item_quantity * $parent_quantity;

				if ( $quantity === 0 ) {
					continue;
				}

				if ( $associated_product->is_type( 'variable' ) ) {

					$variation_id = ( int ) $associated_data['variation_id'];
					$variations   = $associated_data['attributes'];

					if ($variation_id){
						$variation_data = wc_get_product_variation_attributes( $variation_id );

						foreach ( $associated_product->get_attributes() as $attribute ) {
							if ( ! $attribute['is_variation'] ) {
								continue;
							}

							// Get valid value from variation data.
							$attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
							$valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ] : '';

							/**
							 * If the attribute value was posted, check if it's valid.
							 *
							 * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
							 */
							if ( isset( $cart_item_data['tmpost_data'][ $associated_data['element_name']. "_" . $attribute_key ] ) ) {
								$value = $cart_item_data['tmpost_data'][ $associated_data['element_name']. "_" . $attribute_key ];

								// Allow if valid or show error.
								if ( $valid_value === $value ) {
									$variations[ $attribute_key ] = $value;
								} elseif ( '' === $valid_value && in_array( $value, $attribute->get_slugs(), true ) ) {
									// If valid values are empty, this is an 'any' variation so get all possible values.
									$variations[ $attribute_key ] = $value;
								} else {
									// Invalid value posted
								}
							}
						}
					} else {
						continue;
					}
 
				}

				$associated_item_cart_key = $this->add_associated_to_cart( $parent_id, $associated_product, $quantity, $variation_id, $variations, $associated_item_cart_data );

				if ( ! isset( WC()->cart->cart_contents[ $parent_cart_key ]['associated_products'] ) ) {
					WC()->cart->cart_contents[ $parent_cart_key ]['associated_products'] = array();
				}
				if ( $associated_item_cart_key && ! in_array( $associated_item_cart_key, WC()->cart->cart_contents[ $parent_cart_key ]['associated_products'] ) ) {
					WC()->cart->cart_contents[ $parent_cart_key ]['associated_products'][] = $associated_item_cart_key;
				}

			}

		}

	}

	/**
	 * Add an associated product to the cart.
	 *
	 * @since 5.0
	 */
	private function add_associated_to_cart( $associated_id, $product, $quantity = 1, $variation_id = '', $variation = '', $cart_item_data ) {

		if ( $quantity <= 0 ) {
			return FALSE;
		}

		// Get the product / ID.
		if ( is_a( $product, 'WC_Product' ) ) {

			$product_id   = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
			$variation_id = $product->is_type( 'variation' ) ? $product->get_id() : $variation_id;
			$product_data = $product->is_type( 'variation' ) ? $product : wc_get_product( $variation_id ? $variation_id : $product_id );

		} else {

			$product_id   = absint( $product );
			$product_data = wc_get_product( $product_id );

			if ( $product_data->is_type( 'variation' ) ) {
				$product_id   = $product_data->get_parent_id();
				$variation_id = $product_data->get_id();
			} else {
				$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
			}
		}

		if ( ! $product_data ) {
			return FALSE;
		}

		// Load cart item data when adding to cart. WC core filter.
		$cart_item_data = ( array ) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity );
 
		// Generate a ID based on product ID, variation ID, variation data, and other cart item data.
		$cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

		// See if this product and its options is already in the cart.
		$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );

		// If cart_item_key is set, the item is already in the cart and its quantity will be handled by update_quantity_in_cart.
		if ( ! $cart_item_key ) {

			$cart_item_key = $cart_id;

			$position       = array_search( $cart_item_data['associated_parent'], array_keys( WC()->cart->cart_contents ) );
			$position       = (int) $position + (int) $cart_item_data['associated_key'] + 1;
			$array          = WC()->cart->cart_contents;
			$previous_items = array_slice( $array, 0, $position, TRUE );
			$next_items     = array_slice( $array, $position, NULL, TRUE );

			$item = array( $cart_item_key => apply_filters( 'woocommerce_add_cart_item', array_merge( $cart_item_data, array(
				'key'          => $cart_item_key,
				'product_id'   => absint( $product_id ),
				'variation_id' => absint( $variation_id ),
				'variation'    => $variation,
				'quantity'     => $quantity,
				'data'         => $product_data
			) ), $cart_item_key )
			);

			WC()->cart->cart_contents = $previous_items + $item + $next_items;

		}

		return $cart_item_key;
	}

	/**
	 * Get discounted price
	 *
	 * @since 5.0.8
	 */
	public function get_discounted_price( $current_price = 0, $discount = '', $discount_type = '' ) {

		$discount = wc_format_decimal( (double) $discount, wc_get_price_decimals() );
		
		if ( $current_price && $discount ) {
					
			$price = wc_format_decimal( (double) $current_price, wc_get_price_decimals() );
			if( $discount_type == 'fixed' ) {
				$current_price = max( $price - $discount, 0 );
			} else {
				$current_price = max( $price * ( ( 100 -  $discount ) / 100 ), 0 );
			}
					
		}

		return $current_price;

	}

	/**
	 * Modify cart item
	 *
	 * @since 5.0
	 */
	public function modify_cart_item( $cart_item = array() ) {

		if ( isset( $cart_item['associated_parent'] ) && ! empty( $cart_item['associated_parent'] ) ) {

			if ( empty( $cart_item['associated_priced_individually'] ) ) {
				$cart_item['data']->set_regular_price( 0 );
				$cart_item['data']->set_sale_price( '' );
				$cart_item['data']->set_price( 0 );
			}

			if ( $cart_item['data']->needs_shipping() ) {
				
				if ( $cart_item['associated_discount'] ) {

					$discounted_price = $this->get_discounted_price( $cart_item['data']->get_price( 'edit' ), $cart_item['associated_discount'], $cart_item['associated_discount_type']);

					$cart_item[ 'data' ]->set_price( $discounted_price );
					$cart_item[ 'data' ]->set_sale_price( $discounted_price );	

				}				

				if ( empty( $cart_item['associated_shipped_individually'] ) ) {

					if ( $cart_item['associated_maintain_weight'] === "1" ) {

						$cart_item_weight = $cart_item['data']->get_weight( 'edit' );

						if ( $cart_item['data']->is_type( 'variation' ) && '' === $cart_item_weight ) {

							$parent_data      = $cart_item['data']->get_parent_data();
							$cart_item_weight = $parent_data['weight'];
						}

						$cart_item['data']->associated_weight = $cart_item_weight;

					}

					$cart_item['data']->associated_value = $cart_item['data']->get_price( 'edit' );

					$cart_item['data']->set_virtual( 'yes' );
					$cart_item['data']->set_weight( '' );

				}
			}

		}

		return $cart_item;

	}

	/**
	 * Modify cart
	 *
	 * @since 5.0
	 */
	public function woocommerce_add_cart_item( $cart_item = array() ) {

		$cart_item = $this->modify_cart_item( $cart_item );

		return $cart_item;

	}

	/**
	 * Gets the cart from session.
	 *
	 * @since 5.0
	 */
	public function woocommerce_get_cart_item_from_session( $cart_item = array(), $values = array(), $cart_item_key = "" ) {

		if ( isset( $values['tmproducts'] ) ) {
			$cart_item['tmproducts'] = $values['tmproducts'];
		}
		if ( isset( $values['associated_products'] ) ) {
			$cart_item['associated_products'] = $values['associated_products'];
		}
		if ( isset( $values['associated_parent'] ) ) {
			$cart_item['associated_parent'] = $values['associated_parent'];
		}
		if ( isset( $values['associated_key'] ) ) {
			$cart_item['associated_key'] = $values['associated_key'];
		}
		if ( isset( $values['associated_required'] ) ) {
			$cart_item['associated_required'] = $values['associated_required'];
		}
		if ( isset( $values['associated_shipped_individually'] ) ) {
			$cart_item['associated_shipped_individually'] = $values['associated_shipped_individually'];
		}
		if ( isset( $values['associated_priced_individually'] ) ) {
			$cart_item['associated_priced_individually'] = $values['associated_priced_individually'];
		}
		if ( isset( $values['associated_maintain_weight'] ) ) {
			$cart_item['associated_maintain_weight'] = $values['associated_maintain_weight'];
		}
		if ( isset( $values['associated_uniqid'] ) ) {
			$cart_item['associated_uniqid'] = $values['associated_uniqid'];
		}
		if ( isset( $values['associated_label'] ) ) {
			$cart_item['associated_label'] = $values['associated_label'];
		}
		if ( isset( $values['associated_discount'] ) ) {
			$cart_item['associated_discount'] = $values['associated_discount'];
		}
		if ( isset( $values['associated_discount_type'] ) ) {
			$cart_item['associated_discount_type'] = $values['associated_discount_type'];
		}

		$cart_item = $this->modify_cart_item( $cart_item );

		return $cart_item;

	}


	/**
	 * Edit cart functionality
	 * This serves as edit cart regardless if the product has associated products.
	 *
	 * @since 5.0
	 */
	public function woocommerce_add_to_cart( $cart_item_key = "", $product_id = "", $quantity = "", $variation_id = "", $variation = "", $cart_item_data = "" ) {

		if ( THEMECOMPLETE_EPO()->cart_edit_key ) {

			$original_key = THEMECOMPLETE_EPO()->cart_edit_key;

			// Check if there isn't any data change
			if ( $original_key === $cart_item_key){
				WC()->cart->cart_contents[ $cart_item_key ]['quantity'] = $quantity;
				$this->woocommerce_after_cart_item_quantity_update( $cart_item_key, $quantity );
				return;
			}

			// Remove old associated products
			if ( isset( WC()->cart->cart_contents[ $original_key ]['associated_products'] ) && is_array( WC()->cart->cart_contents[ $original_key ]['associated_products'] ) ) {
				foreach ( WC()->cart->cart_contents[ $original_key ]['associated_products'] as $key ) {
					unset( WC()->cart->cart_contents[ $key ] );
				}
			}

			// Replace original key entry with the new key entry
			if ( $original_key !== $cart_item_key){
				$array = WC()->cart->cart_contents;
				$old_key = $original_key;
				$new_key = $cart_item_key;

				if( array_key_exists( $old_key, $array ) ){
				    $keys = array_keys( $array ); 
				    $keys[ array_search( $old_key, $keys ) ] = $new_key;
				    $array = array_combine( $keys, $array );
				    WC()->cart->cart_contents = $array;
				}

			}

			// Reposition new associated product to be below the edited product
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['associated_products'] ) && is_array( WC()->cart->cart_contents[ $cart_item_key ]['associated_products'] ) ) {

				$associated_products = array();
				foreach ( WC()->cart->cart_contents[ $cart_item_key ]['associated_products'] as $key ) {
					if ( isset( WC()->cart->cart_contents[ $key ] ) ) {
						$associated_products[ $key ] = WC()->cart->cart_contents[ $key ];
					}
				}

				$start_position = array_search( WC()->cart->cart_contents[ $cart_item_key ], array_keys( WC()->cart->cart_contents ) );
				foreach ( $associated_products as $key => $item ) {
					$position       = (int) $start_position + (int) $item['associated_key'] + 1;
					$array          = WC()->cart->cart_contents;
					$previous_items = array_slice( $array, 0, $position, TRUE );
					$next_items     = array_slice( $array, $position, NULL, TRUE );

					$item = array( $key => $item );

					WC()->cart->cart_contents = $previous_items + $item + $next_items;

				}

			}

		}

	}

}
