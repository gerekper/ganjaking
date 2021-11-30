<?php

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

class WC_MS_Checkout {

    private $wcms;

    public function __construct( WC_Ship_Multiple $wcms ) {

        $this->wcms = $wcms;

        // free shipping minimum order
        add_filter( 'woocommerce_shipping_free_shipping_is_available', array( $this, 'free_shipping_is_available_for_package' ), 10, 3 );

        add_filter( 'woocommerce_package_rates', array($this, 'remove_multishipping_from_methods'), 10, 2 );
        add_action( 'woocommerce_before_checkout_shipping_form', array( $this, 'before_shipping_form' ) );
        add_action( 'woocommerce_before_checkout_shipping_form', array( $this, 'display_set_addresses_button' ), 5 );
        add_action( 'woocommerce_before_checkout_shipping_form', array( $this, 'render_user_addresses_dropdown' ) );
        add_action( 'wp_ajax_wcms_ajax_save_billing_fields', array( $this, 'save_billing_fields' ) );
        add_action( 'wp_ajax_nopriv_wcms_ajax_save_billing_fields', array( $this, 'save_billing_fields' ) );
        add_action( 'woocommerce_after_checkout_validation', array( $this, 'checkout_validation' ) );

        add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'checkout_process' ) );


        add_filter( 'woocommerce_order_item_meta', array( $this, 'add_item_meta' ), 10, 2 );

        // handle order review events
        add_action( 'woocommerce_checkout_update_order_review', array( $this, 'update_order_review' ) );
        add_action( 'init', array( $this, 'totals_calculation_handler' ) );

        // modify a cart item's subtotal to include taxes
        add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'subtotal_item_include_taxes' ), 10, 3 );

        add_action( 'woocommerce_checkout_order_processed', array($wcms, 'clear_session') );

        // split order
        add_action( 'woocommerce_checkout_order_processed', array( $this, 'create_order_shipments' ), 10, 2 );

        // stop WC from updating the customer's shipping address
        // instead, store it in the address book if it's a new shipping address
        add_filter( 'woocommerce_checkout_update_customer_data', array( $this, 'prevent_customer_data_update' ), 90, 2 );
        add_action( 'woocommerce_checkout_update_user_meta', array( $this, 'maybe_store_shipping_address' ), 10, 2 );

        // Initialize order meta. Need to be called after plugins_loaded because of WC_VERSION check.
        add_action( 'plugins_loaded', array( $this, 'init_order_meta' ), 11 );
    }

    /**
     * Determine which hook to use, depending on core version.
     */
    public function totals_calculation_handler() {
        if ( version_compare( WC_VERSION, '3.2', '<' ) ) {
            add_action( 'woocommerce_calculate_totals', array( $this, 'calculate_totals' ), 10 );
        } else {
            // In 3.2+ with the cart rework, we need to calculate totals after WC core does it.
            add_action( 'woocommerce_after_calculate_totals', array( $this, 'calculate_totals' ), 10 );
        }
    }

    /**
     * Actions for order item meta.
     *
     * @since 3.3.23
     * @return void
     */
    public function init_order_meta() {
        if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
            add_action( 'woocommerce_add_order_item_meta', array( $this, 'store_item_id_bwc' ), 10, 3 );
        } else {
            add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'store_item_key' ), 10, 3 );
        }
    }

	/**
	 * This method checks if free shipping is available for the current package,
	 * depending on min_amount/requires.
	 *
	 * @param bool               $is_available
	 * @param array              $package
	 * @param WC_Shipping_Method $shipping_method
	 *
	 * @return bool
	 */
    public function free_shipping_is_available_for_package( $is_available, $package, $shipping_method ) {
        $min_amount         = $shipping_method->get_option( 'min_amount' );
        $requires           = $shipping_method->get_option( 'requires' );
        $ignore_discounts   = $shipping_method->get_option( 'ignore_discounts' );
        $has_met_min_amount = false;
        $has_coupon         = false;

        if ( in_array( $requires, array( 'coupon', 'either', 'both' ), true ) ) {
            $coupons = WC()->cart->get_coupons();

            if ( $coupons ) {
                foreach ( $coupons as $code => $coupon ) {
                    if ( $coupon->is_valid() && $coupon->get_free_shipping() ) {
                        $has_coupon = true;
                        break;
                    }
                }
            }
        }

        if ( in_array( $requires, array( 'min_amount', 'either', 'both' ), true ) && isset( $package['cart_subtotal'] ) ) {
            $total = $package['cart_subtotal'];

            if ( WC()->cart->display_prices_including_tax() ) {
                $total = $total - WC()->cart->get_discount_tax();
            }

            if ( 'no' === $ignore_discounts ) {
                $total = $total - WC()->cart->get_discount_total();
            }

            $total = round( $total, wc_get_price_decimals() );

            if ( $total >= $min_amount ) {
                $has_met_min_amount = true;
            }
        }

        switch ( $requires ) {
            case 'min_amount':
                $is_available = $has_met_min_amount;
                break;
            case 'coupon':
                $is_available = $has_coupon;
                break;
            case 'both':
                $is_available = $has_met_min_amount && $has_coupon;
                break;
            case 'either':
                $is_available = $has_met_min_amount || $has_coupon;
                break;
            default:
                $is_available = true;
                break;
        }

        return $is_available;
    }

    function remove_multishipping_from_methods( $rates ) {

        if ( !wcms_session_isset( 'wcms_packages' ) && isset($rates['multiple_shipping']) ) {
            unset($rates['multiple_shipping']);
        }

        return $rates;
    }

    function before_shipping_form($checkout = null) {

        if ( !$this->wcms->cart->cart_is_eligible_for_multi_shipping() ) {
            return;
        }

        $id = wc_get_page_id( 'multiple_addresses' );

        $sess_item_address = wcms_session_get( 'cart_item_addresses' );
        $sess_cart_address = wcms_session_get( 'cart_addresses' );
        $has_item_address = (!wcms_session_isset( 'cart_item_addresses' ) || empty($sess_item_address)) ? false : true;
        $has_cart_address = (!wcms_session_isset( 'cart_addresses' ) || empty($sess_cart_address)) ? false : true;
        $inline = false;

        if ( $has_item_address ) {
            $inline = 'jQuery(function() {
                    var col = jQuery("#customer_details .col-2");

                    jQuery("#shiptobilling").hide();
                    jQuery(".woocommerce-shipping-fields").find("#shiptobilling-checkbox")
                        .attr("checked", true)
                        .hide();

                    // WC2.1+
                    jQuery(".woocommerce-shipping-fields").find("#ship-to-different-address-checkbox")
                        .attr("checked", false)
                        .hide();
                    jQuery(".woocommerce-shipping-fields").find("h3#ship-to-different-address")
                        .hide();
                    jQuery(".woocommerce-shipping-fields").prepend("<h3 id=\'ship-to-multiple\'>'. __('Shipping Address', 'wc_shipping_multiple_address') .'</h3>");

                    jQuery(".woocommerce-shipping-fields").find(".shipping_address").remove();

                    jQuery(\'<p><a href=\"'. get_permalink($id) .'\" class=\"button button-primary\">'. __( 'Modify/Add Address', 'wc_shipping_multiple_address' ) .'</a></p>\').insertAfter("#customer_details .col-2 h3:first");
                });';

        } elseif ( $has_cart_address ) {
            $inline = 'jQuery(function() {
                    var col = jQuery("#customer_details .col-2");

                    jQuery(col).find("#shiptobilling-checkbox")
                        .attr("checked", true)
                        .hide();

                    // WC2.1+
                    jQuery(".woocommerce-shipping-fields").find("#ship-to-different-address-checkbox")
                        .attr("checked", false)
                        .hide();
                    jQuery(".woocommerce-shipping-fields").find("h3#ship-to-different-address")
                        .hide();
                    jQuery(".woocommerce-shipping-fields").prepend("<h3 id="ship-to-multiple">'. __('Shipping Address', 'wc_shipping_multiple_address') .'</h3>");

                    jQuery(".woocommerce-shipping-fields").find(".shipping_address").remove();

                    jQuery(\'<p><a href=\"'. add_query_arg( 'cart', 1, get_permalink($id)) .'\" class=\"button button-primary\">'. __( 'Modify/Add Address', 'wc_shipping_multiple_address' ) .'</a></p>\').insertAfter("#customer_details .col-2 h3:first");

                });';

        } elseif ( ! $this->wcms->cart->cart_has_multi_shipping() && WC()->cart->needs_shipping() ) {
			$page_id = wc_get_page_id( 'multiple_addresses' );
			$inline  = '
				var col = jQuery("#customer_details .col-2");

				jQuery(col).find("#shiptobilling-checkbox")
					.attr("checked", true)
					.hide();

				jQuery( "#wcms_set_addresses" ).on( "click", function( evt ) {

					evt.preventDefault();
					var ajax_url 		= WCMS.ajaxurl;
					var billing_container = jQuery(".woocommerce-billing-fields");

					var post_data 	= {
						"action"             : "wcms_ajax_save_billing_fields",
						"billing_first_name" : billing_container.find("#billing_first_name").val(),
						"billing_last_name"  : billing_container.find("#billing_last_name").val(),
						"billing_company"    : billing_container.find("#billing_company").val(),
						"billing_country"    : billing_container.find("#billing_country").val(),
						"billing_address_1"  : billing_container.find("#billing_address_1").val(),
						"billing_address_2"  : billing_container.find("#billing_address_2").val(),
						"billing_city"       : billing_container.find("#billing_city").val(),
						"billing_state"      : billing_container.find("#billing_state").val(),
						"billing_postcode"   : billing_container.find("#billing_postcode").val(),
						"billing_phone"      : billing_container.find("#billing_phone").val(),
						"billing_email"      : billing_container.find("#billing_email").val()
					};

					jQuery.ajax({
						method: "POST",
						url: ajax_url,
						data: post_data,
						success : function( res ) {
							window.location = "' . esc_url( get_permalink( $page_id ) ) . '";
						}
					});
				});';
		}

        if ( $inline ) {
            wc_enqueue_js( $inline );
        }
    }

	public function display_set_addresses_button( $checkout ) {
		if ( is_checkout() && ! $this->wcms->cart->cart_has_multi_shipping() && WC()->cart->needs_shipping() ) {
			
			$css = 'display:none;';

            if ( $this->wcms->is_multiship_enabled() && $this->wcms->cart->cart_is_eligible_for_multi_shipping() ) {
                $css = '';
            } else {
                // clear all session so we don't use old cart addresses in case
                // the customer adds more valid products to the cart
                $this->wcms->clear_session();
            }
			
			echo '
				<p class="woocommerce-info woocommerce_message" id="wcms_message" style="' . esc_attr( $css ) . '">
					' . WC_Ship_Multiple::$lang['notification'] . '<br /><br />
					<input type="button" id="wcms_set_addresses" name="wcms_set_addresses" value="' . esc_attr( WC_Ship_Multiple::$lang['btn_items'] ) . '" />
				</p>';
		}
	}

	public function save_billing_fields() {
		foreach ( WC()->checkout->get_checkout_fields( 'billing' ) as $key => $field ) {
			if (  is_callable( array( WC()->customer, "set_{$key}" ) ) ) {
				WC()->customer->{"set_{$key}"}( wc_clean( $_POST[ $key ] ) );
			}
		}

		WC()->customer->save();

		wp_send_json( array( 'result' => 'success' ) );
	}

	public function render_user_addresses_dropdown() {

		$addresses = $this->wcms->address_book->get_user_addresses( wp_get_current_user() );

		if ( count( $addresses ) ) :
	?>
		<p id="ms_shipping_addresses_field" class="form-row form-row-wide ms-addresses-field">
			<label><?php esc_html_e( 'Stored Addresses', 'wc_shipping_multiple_address' ); ?></label>
			<select id="ms_addresses">
				<option value=""><?php esc_html_e( 'Select an address to use&hellip;', 'wc_shipping_multiple_address' ); ?></option>
				<?php
					foreach ( $addresses as $key => $address ) {
						$formatted_address = $address['shipping_first_name'] . ' ' . $address['shipping_last_name'] . ', ' . $address['shipping_address_1'] . ', ' . $address['shipping_city'];
						echo '<option value="' . esc_attr( $key ) . '"';
						foreach ( $address as $key => $value ) {
							echo ' data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
						}
						echo '>' . esc_html( $formatted_address ) . '</option>';
					}
				?>
			</select>
		</p>
	<?php
		endif;
	}

	/**
	 * Store original cart item key
	 *
	 * @param  WC_Order_Item_Product $item          Order item.
	 * @param  string                $cart_key      Cart item key.
	 * @param  array                 $values        Order item values.
	 */
	public function store_item_key( $item, $cart_key, $values ) {
		$item->add_meta_data( '_wcms_cart_key', $cart_key, true );
	}

    /**
     * Include add-ons line item meta.
     * This method is for <= 2.6 compatibility.
     *
     * @param  int                   $item_id       Order item ID.
     * @param  array                 $values        Order item values.
     * @param  string                $cart_key      Cart item key.
     */
    public function store_item_id_bwc( $item_id, $values, $cart_key ) {
        global $wpdb;

        // get the order id
        $order_id = $wpdb->get_var($wpdb->prepare(
            "SELECT order_id
            FROM {$wpdb->prefix}woocommerce_order_items
            WHERE order_item_id = %d",
            $item_id
        ));

		$order = wc_get_order( $order_id );

        if ( ! $order ) {
            return;
        }

        $item_ids = $order->get_meta( '_packages_item_ids' );

        if ( !is_array( $item_ids ) ) {
            $item_ids = array();
        }

        $item_ids[ $cart_key ] = $item_id;

		$order->update_meta_data( '_packages_item_ids', $item_ids );
		$order->save();
    }


    public function checkout_process($order_id) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

        $sess_item_address = wcms_session_get( 'cart_item_addresses' );
        $has_item_address = (!wcms_session_isset( 'cart_item_addresses' ) || empty($sess_item_address)) ? false : true;

        do_action( 'wc_ms_before_checkout_process', $order_id );

        $packages = WC()->cart->get_shipping_packages();

        $sess_item_address  = wcms_session_isset( 'cart_item_addresses' ) ? wcms_session_get( 'cart_item_addresses' ) : false;
        $sess_packages      = wcms_session_isset( 'wcms_packages' ) ? wcms_session_get( 'wcms_packages' ) : false;
        $sess_methods       = wcms_session_isset( 'shipping_methods' ) ? wcms_session_get( 'shipping_methods' ) : false;
        $sess_rates         = wcms_session_isset( 'wcms_package_rates' ) ? wcms_session_get( 'wcms_package_rates' ) : false;

        // Allow outside code to modify session data one last time
        $sess_item_address  = apply_filters( 'wc_ms_checkout_session_item_address', $sess_item_address );
        $sess_packages      = apply_filters( 'wc_ms_checkout_session_packages', $sess_packages );
        $sess_methods       = apply_filters( 'wc_ms_checkout_session_methods', $sess_methods);
        $sess_rates         = apply_filters( 'wc_ms_checkout_session_rates', $sess_rates);

        if ( $has_item_address ) {
            $order->update_meta_data( '_multiple_shipping', 'yes' );
        }

        // update the taxes
        $packages       = $this->calculate_taxes( null, $packages , true);
        $sess_packages  = $this->calculate_taxes( null, $sess_packages, true );

        if ( $packages ) {
            $order->update_meta_data( '_shipping_packages', $packages );
        }

        if ( $sess_item_address !== false && ! empty( $sess_item_address ) ) {
            $order->update_meta_data( '_shipping_addresses', $sess_item_address );
            //wcms_session_delete( 'cart_item_addresses' );

            if ( $sess_packages ) {
                if ( $has_item_address ) {
                    $shipping_address = array(
                        'first_name'    => '',
                        'last_name'     => '',
                        'company'       => '',
                        'address_1'     => '',
                        'address_2'     => '',
                        'city'          => '',
                        'postcode'      => '',
                        'country'       => '',
                        'state'         => ''
                    );

                    if ( count( $sess_packages ) == 1 ) {
                        $current_package = current( $sess_packages );
                        $shipping_address = $current_package['destination'];
                    }

                    // remove the shipping address
                    $order->set_shipping_first_name( $shipping_address['first_name'] );
                    $order->set_shipping_last_name( $shipping_address['last_name'] );
                    $order->set_shipping_company( $shipping_address['company'] );
                    $order->set_shipping_address_1( $shipping_address['address_1'] );
                    $order->set_shipping_address_2( $shipping_address['address_2'] );
                    $order->set_shipping_city( $shipping_address['city'] );
                    $order->set_shipping_postcode( $shipping_address['postcode'] );
                    $order->set_shipping_country( $shipping_address['country'] );
                    $order->set_shipping_state( $shipping_address['state'] );
                }
            }

        }

        if ( $sess_packages !== false && !empty($sess_packages) && $has_item_address ) {
            $order->update_meta_data( '_wcms_packages', $sess_packages );
        }

        if ( $sess_methods !== false && !empty($sess_methods) && $has_item_address ) {
            $methods = $sess_methods;
            $order->update_meta_data( '_shipping_methods', $methods );
        
		} else {
            $methods = $order->get_shipping_methods();
            $ms_methods = array();

            if ( $sess_packages ) {
                foreach ( $sess_packages as $pkg_idx => $package ) {
                    foreach ( $methods as $method ) {
                        $ms_methods[ $pkg_idx ] = array(
                            'id'    => $method['method_id'],
                            'label' => $method['name']
                        );
                        continue 2;
                    }
                }
            }

            $order->update_meta_data( '_shipping_methods', $ms_methods );
        }

        if ( $sess_rates !== false ) {
            $order->update_meta_data( '_shipping_rates', $sess_rates );
        }

		$order->save();

        do_action( 'wc_ms_after_checkout_process', $order_id );
    }

    public function checkout_validation( $post ) {

	if ( ! $this->wcms->cart->cart_has_multi_shipping() ) {
		return;
	}

        if ( empty($post['shipping_method']) || $post['shipping_method'] == 'multiple_shipping' || ( is_array($post['shipping_method']) && count($post['shipping_method']) > 1 ) ) {
            $packages   = wcms_session_get('wcms_packages');
            $has_empty  = false;

            foreach ( $packages as $package ) {
                if ( empty( $package['contents'] ) || ( isset( $package['bundled_by'] ) && ! empty( $package['bundled_by'] ) ) ) {
                    continue;
                }

                if ( $this->wcms->is_address_empty( $package['destination'] ) ) {
                    $has_empty = true;
                }
            }

            if ( $has_empty ) {
                if ( function_exists('wc_add_notice') ) {
                    wc_add_notice( __( 'One or more items has no shipping address.', 'wc_shipping_multiple_address' ), 'error' );
                } else {
                    WC()->add_error( __( 'One or more items has no shipping address.', 'wc_shipping_multiple_address' ) );
                }

            }

        }

    }

    public function add_item_meta( $meta, $values ) {

        $packages   = wcms_session_get( 'wcms_packages' );
        $methods    = wcms_session_isset( 'shipping_methods' ) ? wcms_session_get( 'shipping_methods' ) : false;

        if ( $methods !== false && !empty($methods) ) {
            if ( isset($values['package_idx']) && isset($packages[$values['package_idx']]) ) {
                $meta->add( 'Shipping Method', $methods[$values['package_idx']]['label']);
            }
        }

    }

	public function update_order_review($post) {
		// #39: Not processing single-package shipment causes an infinite loop - check for an empty session instead.
		if ( ! wcms_session_isset( 'wcms_packages' ) ) {
			return;
		}
		$packages = wcms_session_get( 'wcms_packages' );
		if ( empty( $packages ) ) {
			return;
		}

        $ship_methods   = array();
        $data           = array();
        $field          = (function_exists('wc_add_notice')) ? 'shipping_method' : 'shipping_methods';
        parse_str($post, $data);

        $all_shippings  = isset( $data['all_shipping_methods'] ) ? json_decode( $data['all_shipping_methods'], true ) : array();

        if ( isset( $data[ $field ] ) && is_array( $data[ $field ] ) ) {
			$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

			foreach ( $data[ $field ] as $x => $method ) {
                $method_info = isset( $all_shippings[ $method ] ) ? $all_shippings[ $method ] : $method;

                if ( empty( $method_info['label'] ) ) {
                    $method_label = $method;
                } else {
                    $explode_method = explode( ' ', $method_info['label'] );
                    unset( $explode_method[ count( $explode_method)-1 ] );
                    $method_label = implode( ' ', $explode_method );
                }
                
				$ship_methods[ $x ] = array(
					'id'    => $method,
					'label' => $method_label,
                );

				// Update chosen methods in WooCommerce session.
				$chosen_shipping_methods[ $x ] = $method;
			}

			wcms_session_set( 'shipping_methods', $ship_methods );
			WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
		}
    }

	/**
	 * Return packages with subscription product only.
	 *
	 * @param  array $packages array of packages.
	 * @return array $packages array of packages that contain subscription product.
	 * 
	 * @since 3.6.28
	 */
	private function get_shippable_subscription_product_packages( $packages ){

		if( ! class_exists( 'WC_Subscriptions_Product' ) ){
			return $packages;
		}

		if( ! is_array( $packages ) ){
			return $packages;
		}

		if( empty( $packages ) ){
			return $packages;
		}

		$non_subscription_package_idx = array();

		$temp_packages = $packages;

		foreach( $temp_packages as $x => $package ){

			$package_contains_subscriptions_needing_shipping = false;

			foreach ( $package['contents'] as $cart_item_key => $values ) {
				$_product = $values['data'];
				if ( WC_Subscriptions_Product::is_subscription( $_product ) && $_product->needs_shipping() && ! WC_Subscriptions_Product::needs_one_time_shipping( $_product ) ) {
					$package_contains_subscriptions_needing_shipping = true;
				}
			}

			

			if( ! $package_contains_subscriptions_needing_shipping ){
				unset( $packages[ $x ] );
			}
		}

		return $packages;
	}

	/**
	 * Check the cart if it only contains a recurring product.
	 *
	 * @param array|WC_Cart $cart.
	 * @return bool
	 * 
	 * @since 3.6.28
	 */
	private function is_recurring_cart( $cart ){

		if( ! class_exists( 'WC_Subscriptions_Cart' ) ){
			return false;
		}
		
		if( true === WC_Subscriptions_Cart::cart_contains_subscriptions_needing_shipping( $cart ) && empty( $this->get_cart_non_subscription_item( $cart ) ) ){

			return true;
		
		}else{
			
			return false;
		
		}
	}

	/**
	 * Get non WC_Subscriptions_Product items from cart.
	 *
	 * @param  object WC_Cart Cart object.
	 * @return object WC_Cart Cart object without subscriptions product in the cart content.
	 * 
	 * @since 3.6.28
	 */
	private function get_cart_non_subscription_item( $cart ){

		if( ! class_exists( 'WC_Subscriptions_Product' ) ){
			return $cart;
		}
		
		$non_subscription_cart_items = array();

		foreach( $cart->get_cart() as $cart_item_key => $cart_item ){

			$_product = $cart_item['data'];
			if ( ! WC_Subscriptions_Product::is_subscription( $_product ) && $_product->needs_shipping() ) {
				$non_subscription_cart_items[ $cart_item_key ] = $cart_item;
			}
		}

		return $non_subscription_cart_items;
	}

    function calculate_totals($cart) {

		if ( isset( $_REQUEST['wc-ajax'] ) && 'update_shipping_method' === $_REQUEST['wc-ajax'] ) {

			// Update chosen shipping methods to match with WooCommerce session variable.
			$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
			$methods                 = wcms_session_get( 'shipping_methods' );

			if ( ! empty( $methods ) ) {
				foreach ( $methods as $key => $method ) {
					if ( isset( $chosen_shipping_methods[ $key ] ) ) {
                        $methods[ $key ] = array(
                            'id'    => $chosen_shipping_methods[ $key ],
                            'label' => $chosen_shipping_methods[ $key ],
                        );    
					}
				}
			}

			wcms_session_set( 'shipping_methods', $methods );
		}

        $shipping_total     = 0;
        $shipping_taxes     = array();

        if (! wcms_session_isset( 'wcms_packages' )) return $cart;
        if (! wcms_session_isset( 'shipping_methods' )) return $cart;
        if (! wcms_session_isset( 'cart_item_addresses' )) return $cart;

        $methods    = wcms_session_get( 'shipping_methods' );
        $packages   = wcms_session_get( 'wcms_packages' );
        $rates      = array();

        if ( ! $packages ) {
            $packages = WC()->cart->get_shipping_packages();
            WC()->shipping->calculate_shipping( $packages );
        }

        $packages = WC()->shipping->get_packages();
        $tax_based_on = get_option( 'woocommerce_tax_based_on', 'billing' );

		// Remove non subscription packages if the current cart is a recurring cart object.
		if( $this->is_recurring_cart( $cart ) ){
			$packages = $this->get_shippable_subscription_product_packages( $packages );
		}

        foreach ($packages as $x => $package) {
            $chosen = isset( $methods[ $x ] ) ? $methods[ $x ]['id'] : '';

            if ( $chosen ) {
                if ( is_callable( array( WC()->customer, 'set_calculated_shipping' ) ) ) {
                    WC()->customer->set_calculated_shipping( false );
                } else {
                    // This is for <= 2.6 compatibility.
                    WC()->customer->calculated_shipping( false );
                }
                WC()->customer->set_shipping_location(
                    $package['destination']['country'],
                    $package['destination']['state'],
                    $package['destination']['postcode'],
                    $package['destination']['city']
                );

                $ship = $chosen;

                if ( isset($package['rates'] ) ) {
                    if ( !isset( $package['rates'][ $ship ] ) ) {
                        $rate = wcms_get_cheapest_shipping_rate( $package['rates'] );

                        if ( isset( $rate['id'] ) ) {
                            $ship = $rate['id'];
                        }
                    }

                    if ( isset( $package['rates'][ $ship ]) ) {
                        $rate        = $package['rates'][ $ship ];
                        $rates[ $x ] = $package['rates'];
                        $shipping_total += $rate->cost;
                        // @see: https://github.com/woocommerce/woocommerce/issues/19131
                        $rate_options = get_option( 'woocommerce_' . $rate->get_method_id() . '_' . $rate->get_instance_id() . '_settings', true );

                        // Calculate tax based on package shipping address
                        if ( 'shipping' === $tax_based_on
                            && ( ! isset( $rate_options['tax_status'] ) || 'none' != $rate_options['tax_status'] ) && ! WC()->customer->get_is_vat_exempt() ) {
                            $shipping_tax_rates = WC_Tax::get_shipping_tax_rates();
                            $rate->taxes = WC_Tax::calc_tax( $rate->cost, $shipping_tax_rates );
                        }

                        // calculate tax
                        foreach ( array_keys( $shipping_taxes + $rate->taxes ) as $key ) {
                            $shipping_taxes[ $key ] = ( isset( $rate->taxes[ $key ] ) ? $rate->taxes[ $key ] : 0 ) + ( isset( $shipping_taxes[ $key ] ) ? $shipping_taxes[ $key ] : 0 );
                        }

						// Round shipping tax calculation
						$shipping_taxes     = $this->round_shipping_taxes( $shipping_taxes );
                    }
                }

            }

            $packages[ $x ] = $package;

        }

        if ( version_compare( WC_VERSION, '3.2.0', '<' ) ) {
            $cart->shipping_taxes = $shipping_taxes;
        } else {
            $cart->set_shipping_taxes( $shipping_taxes );
        }

        $cart->shipping_total       = $shipping_total;
        $cart->shipping_tax_total   = (is_array($shipping_taxes)) ? array_sum($shipping_taxes) : 0;

        // store the shipping rates
        wcms_session_set( 'wcms_package_rates', $rates );

		if( wc_tax_enabled() && ! WC()->customer->get_is_vat_exempt() ) {
			$this->calculate_taxes( $cart, $packages );
		}
    }

    private function get_discounted_price( $cart, $values, $price, $add_totals = false ) {
        if ( isset( $values['key'] ) ) {
            $cart_item_key = $values['key'];
        } else if ( ! isset( $values['line_total'] ) ) {
            $cart_item_key = current( array_keys( $cart->cart_contents ) );
        } else {
            return $values['line_total'];
        }
        $cart_item = $cart->cart_contents[ $cart_item_key ];
        return $cart_item['line_total'] / $cart_item['quantity'];
    }

    public function calculate_taxes( $cart = null, $packages = null, $return_packages = false ) {

        if ( ! $this->wcms->is_multiship_enabled() || ! $this->wcms->cart->cart_is_eligible_for_multi_shipping() ) {
            return $packages;
        }

        if ( get_option( 'woocommerce_calc_taxes', 0 ) != 'yes' ) {
            if ( $return_packages ) {
                return $packages;
            }

            return;
        }

        $merge = false;
        if ( !is_object( $cart ) ) {
            $cart = WC()->cart;
            $merge = true;
        }

        if (isset($_POST['action']) && $_POST['action'] == 'woocommerce_update_shipping_method') {
            return $cart;
        }

        if ( !$packages )
            $packages = $cart->get_shipping_packages();

        if ( empty($packages) ) {
            return;
        }

        // clear the taxes arrays remove tax totals from the grand total
        $pre_wc_32                  = version_compare( WC_VERSION, '3.2.0', '<' );
        $old_tax_total              = $cart->tax_total;
        $old_shipping_tax_total     = $cart->shipping_tax_total;

        // deduct taxes from the subtotal
        if ( $pre_wc_32 ) {
            $cart->subtotal -= $old_tax_total;
        }

        $item_taxes     = array();
        $cart_taxes     = array();

        foreach ( $packages as $idx => $package ) {
            if ( isset($package['destination']) && !$this->wcms->is_address_empty( $package['destination'] ) ) {
                if ( is_callable( array( WC()->customer, 'set_calculated_shipping' ) ) ) {
                    WC()->customer->set_calculated_shipping( false );
                } else {
                    // This is for <= 2.6 compatibility.
                    WC()->customer->calculated_shipping( false );
                }
                WC()->customer->set_shipping_location(
                    $package['destination']['country'],
                    $package['destination']['state'],
                    $package['destination']['postcode'],
                    $package['destination']['city']
                );
            }

            $tax_rates      = array();
            $shop_tax_rates = array();

            /**
             * Calculate subtotals for items. This is done first so that discount logic can use the values.
             */
            foreach ( $package['contents'] as $cart_item_key => $values ) {

				if( ! isset( $cart->cart_contents[ $values['key'] ] ) ){
					continue;
				}
				
                $_product = $values['data'];

                // Prices
                $line_price         = $_product->get_price() * $values['quantity'];
                $line_subtotal      = 0;
                $line_subtotal_tax  = 0;

                // WC Composite Products
                if ( isset( $values['composite_data'] ) ) {
                    $line_price = 0;

                    foreach ( $values['composite_data'] as $composite ) {
                        if ( isset( $composite['price'] ) ) {
                            $line_price += $composite['price'];
                        }
                    }

                    if ( isset( $values['quantity'] ) ) {
                        $line_price *= $values['quantity'];
                    }
                }

                if ( ! $_product->is_taxable() ) {
                    $line_subtotal = $line_price;
                } elseif ( $cart->prices_include_tax ) {

                    // Get base tax rates
                    if ( empty( $shop_tax_rates[ $_product->get_tax_class() ] ) ) {
                        $shop_tax_rates[ $_product->get_tax_class() ] = WC_Tax::get_base_tax_rates( $_product->get_tax_class() );
                    }

                    // Get item tax rates
                    if ( empty( $tax_rates[ $_product->get_tax_class() ] ) ) {
                        $tax_rates[ $_product->get_tax_class() ] = WC_Tax::get_rates( $_product->get_tax_class() );
                    }

                    $base_tax_rates = $shop_tax_rates[ $_product->get_tax_class() ];
                    $item_tax_rates = $tax_rates[ $_product->get_tax_class() ];

                    /**
                     * ADJUST TAX - Calculations when base tax is not equal to the item tax
                     */
                    if ( $item_tax_rates !== $base_tax_rates ) {

                        // Work out a new base price without the shop's base tax
                        $taxes                 = WC_Tax::calc_tax( $line_price, $base_tax_rates, true, true );

                        // Now we have a new item price (excluding TAX)
                        $line_subtotal         = $line_price - array_sum( $taxes );

                        // Now add modifed taxes
                        $tax_result            = WC_Tax::calc_tax( $line_subtotal, $item_tax_rates );
                        $line_subtotal_tax     = array_sum( $tax_result );

                        /**
                         * Regular tax calculation (customer inside base and the tax class is unmodified
                         */
                    } else {

                        // Calc tax normally
                        $taxes                 = WC_Tax::calc_tax( $line_price, $item_tax_rates, true );
                        $line_subtotal_tax     = array_sum( $taxes );
                        $line_subtotal         = $line_price - array_sum( $taxes );

                    }

                    /**
                     * Prices exclude tax
                     *
                     * This calculation is simpler - work with the base, untaxed price.
                     */
                } else {

                    // Get item tax rates
                    if ( empty( $tax_rates[ $_product->get_tax_class() ] ) ) {
                        $tax_rates[ $_product->get_tax_class() ] = WC_Tax::get_rates( $_product->get_tax_class() );
                    }

                    $item_tax_rates        = $tax_rates[ $_product->get_tax_class() ];

                    // Base tax for line before discount - we will store this in the order data
                    $taxes                 = WC_Tax::calc_tax( $line_price, $item_tax_rates );
                    $line_subtotal_tax     = array_sum( $taxes );
                    $line_subtotal         = $line_price;
                }

				// Only do this for versions less than 3.2. Otherwise, after woocommerce/commit/302512e it's calculated automatically.
				if ( version_compare( WC_VERSION, '3.2', '<' ) ) {
					// Add to main subtotal
					$cart->subtotal += $line_subtotal_tax;
				}
            }

            /**
             * Calculate totals for items
             */
            foreach ( $package['contents'] as $cart_item_key => $values ) {

				if( ! isset( $cart->cart_contents[ $values['key'] ] ) ){
					continue;
				}

                $_product = $values['data'];

                // Prices
                $base_price = $_product->get_price();
                $line_price = $_product->get_price() * $values['quantity'];

                // WC Composite Products
                if ( isset( $values['composite_data'] ) ) {
                    $line_price = 0;

                    foreach ( $values['composite_data'] as $composite ) {
                        $line_price += $composite['price'];
                    }
                    $base_price = $line_price;
                    $line_price *= $values['quantity'];
                }

                // Tax data
                $taxes = array();
                $discounted_taxes = array();

                if ( ! $_product->is_taxable() ) {
                    // Discounted Price (price with any pre-tax discounts applied)
                    $discounted_price      = $this->get_discounted_price( $cart, $values, $base_price, false );
                    $line_subtotal_tax     = 0;
                    $line_subtotal         = $line_price;
                    $line_tax              = 0;
                    $line_total            = WC_Tax::round( $discounted_price * $values['quantity'] );

                    /**
                     * Prices include tax
                     */
                } elseif ( $cart->prices_include_tax ) {

                    $base_tax_rates = $shop_tax_rates[ $_product->get_tax_class() ];
                    $item_tax_rates = $tax_rates[ $_product->get_tax_class() ];

                    /**
                     * ADJUST TAX - Calculations when base tax is not equal to the item tax
                     */
                    if ( $item_tax_rates !== $base_tax_rates ) {

                        // Work out a new base price without the shop's base tax
                        $taxes             = WC_Tax::calc_tax( $line_price, $base_tax_rates, true, true );

                        // Now we have a new item price (excluding TAX)
                        $line_subtotal     = wc_round_tax_total( $line_price - array_sum( $taxes ) );

                        // Now add modifed taxes
                        $taxes             = WC_Tax::calc_tax( $line_subtotal, $item_tax_rates );
                        $line_subtotal_tax = array_sum( $taxes );

                        // Adjusted price (this is the price including the new tax rate)
                        $adjusted_price    = ( $line_subtotal + $line_subtotal_tax ) / $values['quantity'];

                        // Apply discounts
                        $discounted_price  = $this->get_discounted_price( $cart, $values, $adjusted_price, false );
                        $discounted_taxes  = WC_Tax::calc_tax( $discounted_price * $values['quantity'], $item_tax_rates, false );
                        $discounted_taxes  = $this->round_line_taxes( $discounted_taxes );
						$line_tax          = array_sum( $discounted_taxes );
                        $line_total        = ( $discounted_price * $values['quantity'] ) - $line_tax;

                        /**
                         * Regular tax calculation (customer inside base and the tax class is unmodified
                         */
                    } else {

                        // Work out a new base price without the shop's base tax
                        $taxes             = WC_Tax::calc_tax( $line_price, $item_tax_rates, true );

                        // Now we have a new item price (excluding TAX)
                        $line_subtotal     = $line_price - array_sum( $taxes );
                        $line_subtotal_tax = array_sum( $taxes );

                        // Calc prices and tax (discounted)
                        $discounted_price = $this->get_discounted_price( $cart, $values, $base_price, false );
                        $discounted_taxes = WC_Tax::calc_tax( $discounted_price * $values['quantity'], $item_tax_rates, false );
                        $discounted_taxes = $this->round_line_taxes( $discounted_taxes );
						$line_tax         = array_sum( $discounted_taxes );
                        $line_total       = ( $discounted_price * $values['quantity'] ) - $line_tax;
                    }

                    // Tax rows - merge the totals we just got
                    foreach ( array_keys( $cart_taxes + $discounted_taxes ) as $key ) {
                        $cart_taxes[ $key ] = ( isset( $discounted_taxes[ $key ] ) ? $discounted_taxes[ $key ] : 0 ) + ( isset( $cart_taxes[ $key ] ) ? $cart_taxes[ $key ] : 0 );
                    }

                    /**
                     * Prices exclude tax
                     */
                } else {

                    $item_tax_rates        = $tax_rates[ $_product->get_tax_class() ];

                    // Work out a new base price without the shop's base tax
                    $taxes                 = WC_Tax::calc_tax( $line_price, $item_tax_rates );

                    // Now we have the item price (excluding TAX)
                    $line_subtotal         = $line_price;
                    $line_subtotal_tax     = array_sum( $taxes );

                    // Now calc product rates
                    $discounted_price      = $this->get_discounted_price( $cart, $values, $base_price, false );
                    $discounted_taxes      = WC_Tax::calc_tax( $discounted_price * $values['quantity'], $item_tax_rates );					
					$discounted_taxes 	   = $this->round_line_taxes( $discounted_taxes );
					$discounted_tax_amount = array_sum( $discounted_taxes );
                    $line_tax              = $discounted_tax_amount;
                    $line_total            = $discounted_price * $values['quantity'];

                    // Tax rows - merge the totals we just got
                    foreach ( array_keys( $cart_taxes + $discounted_taxes ) as $key ) {
                        $cart_taxes[ $key ] = ( isset( $discounted_taxes[ $key ] ) ? $discounted_taxes[ $key ] : 0 ) + ( isset( $cart_taxes[ $key ] ) ? $cart_taxes[ $key ] : 0 );
                    }
                }

                // Calculate the discount total from cart line data.
                $discount_total     = $line_subtotal - $line_total;
                $discount_tax_total = $line_subtotal_tax - $line_tax;

                // Store costs + taxes for lines
                if ( !isset( $item_taxes[ $cart_item_key ] ) ) {
                    $item_taxes[ $cart_item_key ]['line_total']          = $line_total;
                    $item_taxes[ $cart_item_key ]['line_tax']            = $line_tax;
                    $item_taxes[ $cart_item_key ]['line_subtotal']       = $line_subtotal;
                    $item_taxes[ $cart_item_key ]['line_subtotal_tax']   = $line_subtotal_tax;
                    $item_taxes[ $cart_item_key ]['line_tax_data']       = array('total' => $discounted_taxes, 'subtotal' => $taxes );
                    $item_taxes[ $cart_item_key ]['line_disc_total']     = $discount_total;
                    $item_taxes[ $cart_item_key ]['line_disc_total_tax'] = $discount_tax_total;
                } else {
                    $item_taxes[ $cart_item_key ]['line_total']                 += $line_total;
                    $item_taxes[ $cart_item_key ]['line_tax']                   += $line_tax;
                    $item_taxes[ $cart_item_key ]['line_subtotal']              += $line_subtotal;
                    $item_taxes[ $cart_item_key ]['line_subtotal_tax']          += $line_subtotal_tax;
                    $item_taxes[ $cart_item_key ]['line_tax_data']['total']     += $discounted_taxes;
                    $item_taxes[ $cart_item_key ]['line_tax_data']['subtotal']  += $taxes;
                    $item_taxes[ $cart_item_key ]['line_disc_total']            += $discount_total;
                    $item_taxes[ $cart_item_key ]['line_disc_total_tax']        += $discount_tax_total;
                }

                $packages[ $idx ]['contents'][ $cart_item_key ]['line_total']          = $line_total;
                $packages[ $idx ]['contents'][ $cart_item_key ]['line_tax']            = $line_tax;
                $packages[ $idx ]['contents'][ $cart_item_key ]['line_subtotal']       = $line_subtotal;
                $packages[ $idx ]['contents'][ $cart_item_key ]['line_subtotal_tax']   = $line_subtotal_tax;
                $packages[ $idx ]['contents'][ $cart_item_key ]['line_tax_data']       = array('total' => $discounted_taxes, 'subtotal' => $taxes);
                $packages[ $idx ]['contents'][ $cart_item_key ]['line_disc_total']     = $discount_total;
                $packages[ $idx ]['contents'][ $cart_item_key ]['line_disc_total_tax'] = $discount_tax_total;
            }
        }

        // Only do this for versions less than 3.2. Otherwise, after woocommerce/commit/302512e it's calculated automatically.
        if ( version_compare( WC_VERSION, '3.2', '<' ) ) {
            foreach ( $item_taxes as $cart_item_key => $taxes ) {
                if ( !isset($cart->cart_contents[ $cart_item_key ]) )
                    continue;

                $product_id = $cart->cart_contents[ $cart_item_key ]['product_id'];
                WC()->cart->recurring_cart_contents = array();

                $cart->cart_contents[ $cart_item_key ]['line_total']        = $taxes['line_total'];
                $cart->cart_contents[ $cart_item_key ]['line_tax']          = $taxes['line_tax'];
                $cart->cart_contents[ $cart_item_key ]['line_subtotal']     = $taxes['line_subtotal'];
                $cart->cart_contents[ $cart_item_key ]['line_subtotal_tax'] = $taxes['line_subtotal_tax'];
                $cart->cart_contents[ $cart_item_key ]['line_tax_data']     = $taxes['line_tax_data'];

                // Set recurring taxes for subscription products
                if ( class_exists('WC_Subscriptions_Product') && WC_Subscriptions_Product::is_subscription( $product_id ) ) {
                    WC()->cart->recurring_cart_contents[ $product_id ]['recurring_line_total']        = $taxes['line_total'];
                    WC()->cart->recurring_cart_contents[ $product_id ]['recurring_line_tax']          = $taxes['line_tax'];
                    WC()->cart->recurring_cart_contents[ $product_id ]['recurring_line_subtotal']     = $taxes['line_subtotal'];
                    WC()->cart->recurring_cart_contents[ $product_id ]['recurring_line_subtotal_tax'] = $taxes['line_subtotal_tax'];
                }
            }
        }

		// Calculate taxes for virtual product
		foreach( $cart->get_cart() as $cart_item_key => $cart_item ){

			$_product = $cart_item['data'];
			
			if( $_product->is_virtual() ){

                $item_taxes[ $cart_item_key ]['line_subtotal']       = $cart_item['line_subtotal'];
                $item_taxes[ $cart_item_key ]['line_subtotal_tax']   = $cart_item['line_subtotal_tax'];
                $item_taxes[ $cart_item_key ]['line_total']          = $cart_item['line_total'];
                $item_taxes[ $cart_item_key ]['line_tax']            = $cart_item['line_tax'];
                $item_taxes[ $cart_item_key ]['line_disc_total']     = $cart_item['line_subtotal'] - $cart_item['line_total'];
                $item_taxes[ $cart_item_key ]['line_disc_total_tax'] = $cart_item['line_subtotal_tax'] - $cart_item['line_tax'];

				// Get tax rates total from cart contents if exists
				if( isset( $cart_item['line_tax_data']['total'] ) ){
				
					$line_taxes  		= $cart_item['line_tax_data']['total'];
				
				// Manually calculate tax rates total
				}else{

					$item_tax_rates        	= WC_Tax::get_rates( $_product->get_tax_class() );
					$line_taxes  			= WC_Tax::calc_tax( $cart_item['line_total'], $item_tax_rates, false );

				}

				// Rounding the line taxes first before summing
				$line_taxes = $this->round_line_taxes( $line_taxes );

				// Tax rows - merge the totals we just got
				foreach ( array_keys( $cart_taxes + $line_taxes ) as $key ) {
					$cart_taxes[ $key ] = ( isset( $line_taxes[ $key ] ) ? $line_taxes[ $key ] : 0 ) + ( isset( $cart_taxes[ $key ] ) ? $cart_taxes[ $key ] : 0 );
				}

			}
		}

        // Total up/round taxes and shipping taxes
        if ( $cart->round_at_subtotal ) {
            $cart->tax_total          = WC_Tax::get_tax_total( $cart_taxes );
        } else {
            $cart->tax_total          = array_sum( $cart_taxes );
        }
        
        if ( version_compare( WC_VERSION, '3.2.0', '<' ) ) {
            $cart->taxes              = array_map( 'WC_Tax::round', $cart_taxes );
        } else {
			// Get discount tax discount data by calculating the discount with packages instead of cart.
			if ( class_exists( 'WC_Discounts' ) && version_compare( WC_VERSION, '3.2.3', '>=' ) ) {
				$coupon_discount_tax_totals = $this->calculate_coupon_discount_tax_amounts( $cart, $packages );
				$cart->set_discount_total( array_sum( $cart->get_coupon_discount_totals() ) );
				$cart->set_coupon_discount_tax_totals( $coupon_discount_tax_totals );
			}

            $cart->set_cart_contents_taxes( array_map( 'WC_Tax::round', $cart_taxes ) );

            // Get total discount total tax by checking on 'line_disc_total_tax' array value.
            $cart_disc_total_tax = array_sum( array_column( $item_taxes, 'line_disc_total_tax' ) );
		    
            //Set the new cart total data to the cart.
		    $cart->set_discount_tax( $cart_disc_total_tax );
            $cart->set_subtotal_tax( array_sum( array_column( $item_taxes, 'line_subtotal_tax' ) ) );
            
            $discounts = array_sum( $cart->coupon_discount_totals ) + array_sum( $cart->coupon_discount_tax_totals );

            $cart->set_total( ( $cart->get_subtotal() + $cart->get_subtotal_tax() ) + $cart->get_shipping_total() + $old_shipping_tax_total - $discounts );
        }

        if ( $merge ) {
            WC()->cart = $cart;
        }

        // Setting an empty default customer shipping location prevents
        // subtotal calculation from applying the incorrect taxes based
        // on the shipping address. But do not remove the shipping country
        // and shipping state to satisfy the validation done on WC_Checkout
        if ( is_callable( array( WC()->customer, 'set_calculated_shipping' ) ) ) {
            WC()->customer->set_calculated_shipping( false );
        } else {
            // This is for <= 2.6 compatibility.
            WC()->customer->calculated_shipping( false );
        }
        WC()->customer->set_shipping_location(
            WC()->customer->get_shipping_country(),
            WC()->customer->get_shipping_state(),
            ''
        );

        if ( $return_packages ) {
            return $packages;
        }

        // store the modified packages array
        wcms_session_set( 'wcms_packages', $packages );
        // store the modified packages array to different session
        wcms_session_set( 'wcms_packages_after_tax_calc', $packages );

        return $cart;

    }

    /**
	 * Get the coupons from the cart.
	 *
	 * @param WC_Cart    $cart
	 *
     * @since 3.6.34
	 * @return array
	 */
    public function get_coupons_from_cart( $cart ) {
		$coupons = $cart->get_coupons();

		foreach ( $coupons as $coupon ) {
			switch ( $coupon->get_discount_type() ) {
				case 'fixed_product':
					$coupon->sort = 1;
					break;
				case 'percent':
					$coupon->sort = 2;
					break;
				case 'fixed_cart':
					$coupon->sort = 3;
					break;
				default:
					$coupon->sort = 0;
					break;
			}

			// Allow plugins to override the default order.
			$coupon->sort = apply_filters( 'woocommerce_coupon_sort', $coupon->sort, $coupon );
		}

		uasort( $coupons, array( $this, 'sort_coupons_callback' ) );

        return $coupons;
	}

	/**
	 * Sort coupons so discounts apply consistently across installs.
	 *
	 * In order of priority;
	 *  - sort param
	 *  - usage restriction
	 *  - coupon value
	 *  - ID
	 *
	 * @param WC_Coupon $a Coupon object.
	 * @param WC_Coupon $b Coupon object.
     * 
     * $since 3.6.34
	 * @return int
	 */
	public function sort_coupons_callback( $a, $b ) {
		if ( $a->sort === $b->sort ) {
			if ( $a->get_limit_usage_to_x_items() === $b->get_limit_usage_to_x_items() ) {
				if ( $a->get_amount() === $b->get_amount() ) {
					return $b->get_id() - $a->get_id();
				}
				return ( $a->get_amount() < $b->get_amount() ) ? -1 : 1;
			}
			return ( $a->get_limit_usage_to_x_items() < $b->get_limit_usage_to_x_items() ) ? -1 : 1;
		}
		return ( $a->sort < $b->sort ) ? -1 : 1;
	}

    /**
	 * Recalculate the discount tax rates based on package destination.
	 *
	 * @param WC_Cart            $cart
	 * @param array              $packages
	 *
     * @since 3.6.34
	 * @return array
	 */
    public function calculate_coupon_discount_tax_amounts( $cart, $packages ) {
		$coupons = $this->get_coupons_from_cart( $cart );
        $coupon_discount_tax_amounts = array();
        
        // Calculate the line value.
        foreach( $packages as $idx => $package ) {
            $items = array();
            
            foreach ( $package['contents'] as $key => $cart_item ) {
                $item                          = new stdClass();
                $item->key                     = $key;
                $item->object                  = $cart_item;
                $item->product                 = $cart_item['data'];
                $item->quantity                = $cart_item['quantity'];
                $item->taxable                 = 'taxable' === $cart_item['data']->get_tax_status();
			    $item->price_includes_tax      = false;
                $item->price                   = wc_add_number_precision_deep( $cart_item['line_subtotal'] );
                $item->tax_rates               = $this->get_item_tax_rates( $item, $package );
                
                $items[ $key ] = $item;
            }

            $discounts = new WC_Discounts( $cart );

            // Set items directly so the discounts class can see any tax adjustments made thus far using subtotals.
            $discounts->set_items( $items );

            foreach ( $coupons as $coupon ) {
                $discounts->apply_coupon( $coupon );
            }

            $coupon_discount_amounts     = $discounts->get_discounts_by_coupon( true );

            // See how much tax was 'discounted' per item and per coupon.
            foreach ( $discounts->get_discounts( true ) as $coupon_code => $coupon_discounts ) {
                $coupon_discount_tax_amounts[ $coupon_code ] = isset( $coupon_discount_tax_amounts[ $coupon_code ] ) ? $coupon_discount_tax_amounts[ $coupon_code ] : 0;

                foreach ( $coupon_discounts as $item_key => $coupon_discount ) {
                    $item = $items[ $item_key ];

                    if ( $item->product->is_taxable() ) {
                        // Item subtotals were sent, so set 3rd param.
                        $item_tax = array_sum( WC_Tax::calc_tax( $coupon_discount, $item->tax_rates, $item->price_includes_tax ) );
                        
                        // Sum total tax.
                        $coupon_discount_tax_amounts[ $coupon_code ] += $item_tax;

                        // Remove tax from discount total.
                        if ( $item->price_includes_tax ) {
                            $coupon_discount_amounts[ $coupon_code ] -= $item_tax;
                        }
                    }
                }
            }
        }

		return wc_remove_number_precision_deep( $coupon_discount_tax_amounts );
	}

	/**
	* Apply rounding to an array of taxes before summing.
	*
	* @param array $item_taxes
	* @return array
	*/
	public function round_line_taxes( $item_taxes ){
				
		foreach( $item_taxes as $key => $item_tax ){

			if( 'yes' !== get_option( 'woocommerce_tax_round_at_subtotal' ) ){
				
				$item_tax  			= wc_add_number_precision( $item_tax, false );
				$item_tax 			= wc_round_tax_total( $item_tax, 0 );
				$item_tax  			= wc_remove_number_precision( $item_tax );
				$item_taxes[ $key ] = $item_tax;

			}
			
		}

		return $item_taxes;
	}

	/**
	* Apply rounding to an array of shipping taxes before summing.
	*
	* @param array $shipping_taxes
	* @return array
	*/
	public function round_shipping_taxes( $shipping_taxes ){
				
		$shipping_taxes     = wc_add_number_precision_deep( $shipping_taxes, false );
		$shipping_taxes     = array_map( 'WC_Item_Totals::round_item_subtotal', $shipping_taxes );
		$shipping_taxes 	= wc_remove_number_precision_deep( $shipping_taxes );

		return $shipping_taxes;
	}

    /**
	 * This method manipulate the subtotal item value for price with tax included.
	 *
	 * @param float              $product_subtotal
	 * @param array              $cart_item
	 * @param string             $cart_item_key
	 *
	 * @return float
	 */
    public function subtotal_item_include_taxes( $product_subtotal, $cart_item, $cart_item_key ) {
        $packages     = wcms_session_isset( 'wcms_packages_after_tax_calc' ) ? wcms_session_get( 'wcms_packages_after_tax_calc' ) : wcms_session_get( 'wcms_packages' );
        $tax_based_on = get_option( 'woocommerce_tax_based_on', 'billing' );

        // only process subtotal if multishipping is being used
        if ( ( is_array( $packages ) && count( $packages ) <= 1 ) || 'shipping' !== $tax_based_on )
            return $product_subtotal;

        // Value that needs to be updated.
        $package_item = array(
            'line_total' => 0,
            'line_tax'   => 0,
            'line_subtotal' => 0,
            'line_subtotal_tax' => 0
        );

        // Calculate the line value.
        for ( $i = 0; $i < count( $packages ); $i++ ) {
            if ( isset( $packages[ $i ]['contents'][ $cart_item_key ] ) ) {
                $new_cart_item = $packages[ $i ]['contents'][ $cart_item_key ];
                $package_item['line_total']        += isset( $new_cart_item['line_total'] ) ? $new_cart_item['line_total'] : 0;
                $package_item['line_tax']          += isset( $new_cart_item['line_tax'] ) ? $new_cart_item['line_tax'] : 0;
                $package_item['line_subtotal']     += isset( $new_cart_item['line_subtotal'] ) ? $new_cart_item['line_subtotal'] : 0;
                $package_item['line_subtotal_tax'] += isset( $new_cart_item['line_subtotal_tax'] ) ? $new_cart_item['line_subtotal_tax'] : 0;
            }
        }

        // Replace the cart item with the modified one.
        if ( isset( $new_cart_item ) ) {
            $cart_item = $new_cart_item;
            
            foreach ( $package_item as $key => $value ) {
                $cart_item[ $key ] = $value;
            }
        }

        $subtotal   = $this->wcms->get_cart_item_subtotal( $cart_item );
        $taxable    = $cart_item['data']->is_taxable();

		if ( $taxable && $subtotal !== ( $cart_item['line_total'] + $cart_item['line_tax'] ) ) {
			$tax_display_mode = version_compare( WC_VERSION, '4.4', '<' ) ? WC()->cart->tax_display_cart : WC()->cart->get_tax_price_display_mode();

			if ( $tax_display_mode == 'excl' ) {
				$row_price = $cart_item['line_subtotal'];

                $product_subtotal = wc_price( $row_price );

                if ( WC()->cart->prices_include_tax && $cart_item['line_tax'] > 0 ) {
                    $product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                }
            } else {
                $row_price = $cart_item['line_subtotal'] + $cart_item['line_subtotal_tax'];

                $product_subtotal = wc_price( $row_price );

                if ( ! WC()->cart->prices_include_tax && $cart_item['line_tax'] > 0 ) {
                    $product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                }
            }


        }

        return $product_subtotal;
    }

    /**
	 * This method get item tax rates based on package destination.
	 *
	 * @param array              $item
	 * @param array              $package
	 *
     * @since 3.6.34
	 * @return array
	 */
    public function get_item_tax_rates( $item, $package ) {

        $customer = new WC_Customer();

        $customer_country  = $package['destination']['country'];
        $customer_state    = $package['destination']['state'];
        $customer_postcode = $package['destination']['postcode'];
        $customer_city     = $package['destination']['city'];

        $customer->set_billing_location( $customer_country, $customer_state, $customer_postcode, $customer_city );
        $customer->set_shipping_location( $customer_country, $customer_state, $customer_postcode, $customer_city );

        $item_tax_rates = WC_Tax::get_rates( $item->product->get_tax_class(), $customer );

        return $item_tax_rates;
    }

    public function create_order_shipments( $order_id, $posted = null ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

        $multishipping = $order->get_meta( '_multiple_shipping' );
        $created       = $order->get_meta( '_shipments_created' );
        $packages      = $order->get_meta( '_wcms_packages' );
        $shipment      = $this->wcms->shipments;

        if ( $multishipping != 'yes' || $created == 'yes' ) {
            return;
        }

        foreach ( $packages as $i => $package ) {
            $shipment->create_from_package( $package, $i, $order_id );
        }

		$order->update_meta_data( '_shipments_created', 'yes' );
		$order->save();
    }

	public function prevent_customer_data_update( $update, $wc_checkout ) {
		if ( $this->wcms->cart->cart_has_multi_shipping() ) {
			return false;
		}

		return $update;
	}

	public function maybe_store_shipping_address( $customer_id, $posted ) {
		if ( ! $this->wcms->cart->cart_has_multi_shipping() ) {
			return;
		}

		$checkout = WC()->checkout;

		// Check if we should update customer data
		remove_filter( 'woocommerce_checkout_update_customer_data', array( $this, 'prevent_customer_data_update' ), 90, 2 );
		$update_customer_data = apply_filters( 'woocommerce_checkout_update_customer_data', true, $checkout );
		add_filter( 'woocommerce_checkout_update_customer_data', array( $this, 'prevent_customer_data_update' ), 90, 2 );

		if ( $update_customer_data ) {

			// Save billing address
			if ( $checkout->checkout_fields['billing'] ) {
				foreach ( array_keys( $checkout->checkout_fields['billing'] ) as $field ) {
					$field_name = str_replace( 'billing_', '', $field );
					update_user_meta( $customer_id, 'billing_' . $field_name, $checkout->get_posted_address_data( $field_name ) );
				}
			}

			// Get user addresses
			$addresses = $this->wcms->address_book->get_user_addresses( $customer_id );

			// Add guest addresses (needed for when account is created at checkout)
			$guest_addresses = ( wcms_session_isset('user_addresses') ) ? wcms_session_get('user_addresses') : array();
			$addresses = array_merge( $addresses, $guest_addresses );

			$this->wcms->address_book->save_user_addresses( $customer_id, $addresses );
		}
	}

}
