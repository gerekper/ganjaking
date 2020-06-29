<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCMAS_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Multiple_Addresses_Shipping_Frontend' ) ) {
    /**
     * Class YITH_Multiple_Addresses_Shipping_Frontend
     *
     * @since      Version 1.0.0
     * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
     */
    class YITH_Multiple_Addresses_Shipping_Frontend {

        /**
         * Construct
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
         */
        public function __construct() {
        	if ( 'no' == get_option( 'ywcmas_enable_mas_on_frontend', 'yes' ) )
        		return;
	        if ( ! get_current_user_id() && 'no' == get_option( 'ywcmas_enable_guest_users', 'yes' ) )
	        	return;
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_filter( 'woocommerce_shipping_package_name', array( $this, 'package_name' ), 10, 3 );
            add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'generate_packages' ) );
            add_filter( 'woocommerce_package_rates', array( $this, 'package_rates' ), 99, 2 );
            add_action( 'woocommerce_checkout_create_order_shipping_item', array( $this, 'add_shipping_address_meta_data_on_shipping_item' ), 10, 4 );
	        add_action( 'woocommerce_before_checkout_form', array( $this, 'manage_addresses_cb' ) );
	        add_action( 'woocommerce_checkout_before_customer_details', array( $this, 'manage_addresses_content' ) );
	        add_action( 'wp_ajax_ywcmas_print_address', array( $this, 'print_address' ) );
	        add_action( 'wp_ajax_nopriv_ywcmas_print_address', array( $this, 'print_address' ) );
	        add_action( 'wp_ajax_ywcmas_update_default_address', array( $this, 'update_default_address' ) );
	        add_action( 'wp_ajax_ywcmas_update_multi_shipping_data', array( $this, 'update_multi_shipping_data' ) );
	        add_action( 'wp_ajax_nopriv_ywcmas_update_multi_shipping_data', array( $this, 'update_multi_shipping_data' ) );
	        add_action( 'wp_ajax_ywcmas_shipping_address_form', array( $this, 'shipping_address_form' ) );
	        add_action( 'wp_ajax_nopriv_ywcmas_shipping_address_form', array( $this, 'shipping_address_form' ) );
	        add_action( 'wp_ajax_ywcmas_save_address', array( $this, 'save_address' ) );
	        add_action( 'wp_ajax_nopriv_ywcmas_save_address', array( $this, 'save_address' ) );
	        add_action( 'wp_ajax_ywcmas_delete_shipping_address_window', array( $this, 'delete_shipping_address_window' ) );
	        add_action( 'wp_ajax_nopriv_ywcmas_delete_shipping_address_window', array( $this, 'delete_shipping_address_window' ) );
	        add_action( 'wp_ajax_ywcmas_delete_shipping_address', array( $this, 'delete_shipping_address' ) );
	        add_action( 'wp_ajax_nopriv_ywcmas_delete_shipping_address', array( $this, 'delete_shipping_address' ) );
	        add_action( 'woocommerce_add_to_cart', array( $this, 'add_to_cart' ), 10, 6 );
	        add_action( 'woocommerce_cart_item_removed', array( $this, 'cart_item_removed' ) );
	        add_action( 'woocommerce_cart_item_restored', array( $this, 'cart_item_restored' ) );
	        add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'cart_item_qty_updated' ), 10, 2 );
	        add_action( 'woocommerce_checkout_order_processed', array( $this, 'checkout_order_processed' ) );
	        add_filter( 'woocommerce_get_order_item_totals', array( $this, 'add_shipping_rows_in_order_details' ), 10, 2 );
	        add_filter( 'woocommerce_order_shipping_to_display_shipped_via', array( $this, 'remove_shipped_via_text' ), 10, 2 );
	        add_action( 'woocommerce_created_customer', array( $this, 'save_guest_user_addresses_on_post_meta' ) );
	        add_action( 'woocommerce_after_edit_account_address_form', array( $this, 'add_new_shipping_address_button' ) );
	        add_shortcode( 'ywcmas_custom_addresses', array( $this, 'custom_addresses' ) );
        }

	    /**
	     * Enqueue the scripts on admin pages
	     *
	     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	     * @since 1.0.0
	     */
        public function enqueue_scripts() {
	        wp_enqueue_script( 'wc-country-select' );
	        wp_enqueue_script( 'wc-address-i18n' );

	        wp_enqueue_script( 'ywcmas-frontend-add-edit-delete-address',
		        YITH_WCMAS_ASSETS_JS_URL . yit_load_js_file( 'ywcmas-frontend-add-edit-delete-address.js' ),
		        array( 'jquery' ),
		        YITH_WCMAS_VERSION,
		        'true'
	        );
	        //Localize scripts
	        wp_localize_script( 'ywcmas-frontend-add-edit-delete-address', 'ywcmas_frontend_params',
		        array(
			        'ajax_url'                => admin_url( 'admin-ajax.php' ),
			        'delete_shipping_address' => wp_create_nonce( 'ywcmas-delete-shipping-address' ),
			        'fill_fields'             => esc_html__( 'Please fill in with all required information', 'yith-multiple-shipping-addresses-for-woocommerce' )
		        )
	        );

	        if ( is_account_page() ) {
		        wp_enqueue_script( 'ywcmas-frontend-my-account',
			        YITH_WCMAS_ASSETS_JS_URL . yit_load_js_file( 'ywcmas-frontend-my-account.js' ),
			        array( 'jquery' ),
			        YITH_WCMAS_VERSION,
			        'true'
		        );
		        //Localize scripts
		        wp_localize_script( 'ywcmas-frontend-my-account', 'ywcmas_my_account_params',
			        array(
				        'ajax_url' => admin_url( 'admin-ajax.php' ),
			        )
		        );
            }
            if ( is_checkout() ) {
	            wp_enqueue_script( 'ywcmas-frontend-checkout',
		            YITH_WCMAS_ASSETS_JS_URL . yit_load_js_file( 'ywcmas-frontend-checkout.js' ),
		            array( 'jquery', 'jquery-blockui' ),
		            YITH_WCMAS_VERSION,
		            'true'
	            );
	            wp_enqueue_style( 'ywcmas-checkout',
		            YITH_WCMAS_ASSETS_URL . 'css/ywcmas-checkout.css',
		            array(),
		            YITH_WCMAS_VERSION
                );

	            wp_localize_script( 'ywcmas-frontend-checkout', 'ywcmas_checkout_params',
		            array(
			            'ajax_url'          => admin_url( 'admin-ajax.php' ),
                        'update_data_nonce' => wp_create_nonce( 'update_data_nonce' )
		            )
	            );
            }

            // Enqueue ywcmas-checkout-order-received.css if the order has multi shipping only.
	        // This will hide the Shipping Address block below the Order details table in view order page and order received page
	        global $wp_query;
	        if ( is_view_order_page() || ( is_checkout() && is_order_received_page() && ! empty( $wp_query->query_vars['order-received'] ) ) ) {
		        $order = wc_get_order( is_view_order_page() ? $wp_query->query_vars['view-order'] : $wp_query->query_vars['order-received'] );
		        if ( $order ) {
			        $shipping_items = $order->get_items( 'shipping' );
			        if ( $shipping_items || $shipping_items > 1 ) {
				        foreach ( $shipping_items as $shipping_item_id => $shipping_item ) {
					        if ( $shipping_item->get_meta( 'ywcmas_shipping_destination' ) ) {
						        wp_enqueue_style( 'ywcmas-checkout-order-received',
							        YITH_WCMAS_ASSETS_URL . 'css/ywcmas-checkout-order-received.css',
							        array(),
							        YITH_WCMAS_VERSION
						        );
						        break;
					        }
				        }
			        }
		        }
	        }

	        // Enqueue styles
	        wp_enqueue_style( 'ywcmas-frontend',
		        YITH_WCMAS_ASSETS_URL . 'css/ywcmas-frontend.css',
		        array(),
		        YITH_WCMAS_VERSION );


	        $suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	        // PrettyPhoto
	        wp_enqueue_style( 'ywcmas_prettyPhoto_css', YITH_WCMAS_ASSETS_URL . 'css/prettyPhoto.css' );
	        wp_enqueue_script( 'ywcmas-prettyPhoto', YITH_WCMAS_ASSETS_JS_URL . 'jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), false, true );
        }

	    /*
	     * Name the package (shipping item) with the shipping address, for better identifying
	     *
	     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	     * @since 1.0.0
	     */
	    public function package_name( $package_name, $iteration, $package ) {
		    $multi_shipping_enabled = WC()->session->get( 'ywcmas_multi_shipping_enabled' );
		    $multi_shipping_data = WC()->session->get( 'ywcmas_multi_shipping_data' );
		    $addresses = yith_wcmas_get_user_default_and_custom_addresses( get_current_user_id() );

		    if ( ! $multi_shipping_enabled || ! $multi_shipping_data || ! $addresses ) {
			    return $package_name;
		    }

		    if ( isset( $package['local_pickup_package'] ) && 'yes' == $package['local_pickup_package'] ) {
			    return esc_html__( 'Local pickup', 'yith-multiple-shipping-addresses-for-woocommerce' );
		    }

		    $shipping_address = yith_wcmas_shipping_address_from_destination_array( $package['destination'] );
		    $package_name = sprintf( esc_html__( 'Ship to: %s', 'yith-multiple-shipping-addresses-for-woocommerce' ), $shipping_address );
		    if ( 'yes' == get_option( 'ywcmas_show_weight', 'no' ) ) {
			    $weight = 0;

			    foreach ( $package['contents'] as $item_key => $item ) {
				    if ( $item['data']->has_weight() ) {
					    $weight += (float) $item['data']->get_weight() * $item['quantity'];
				    }
			    }
			    $package_name .= apply_filters( 'ywcmas_weight_message', '<div>' . esc_html__( 'Package weight', 'yith-multiple-shipping-addresses-for-woocommerce' ) . ': ' . wc_format_weight( $weight ) . '</div>', $weight, $package );
		    }

		    return $package_name;
	    }

	    /*
		 * Filter the WC packages array and push the packages for generating the shipping items for the order
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 */
        public function generate_packages( $_packages ) {
	        $multi_shipping_enabled = WC()->session->get( 'ywcmas_multi_shipping_enabled' );
	        $multi_shipping = yith_wcmas_get_multi_shipping_array();
	        if ( ! $multi_shipping_enabled || ! $multi_shipping ) {
		        return $_packages;
	        }

        	$packages = array();
        	foreach ( $multi_shipping as $shipping_id => $shipping ) {
        	    $is_local_pickup = 'local_pickup' == $shipping_id ? 1 : 0;
		        $shipping_id = $is_local_pickup ? YITH_WCMAS_BILLING_ADDRESS_ID : $shipping_id;
		        $prefix = YITH_WCMAS_BILLING_ADDRESS_ID == $shipping_id ? 'billing_' : 'shipping_';
        	    $shipping_address = yith_wcmas_get_user_address_by_id( $shipping_id, get_current_user_id() );

        	    $country    = $shipping_address[$prefix . 'country'];
		        $state      = $shipping_address[$prefix . 'state'];
		        $postcode   = $shipping_address[$prefix . 'postcode'];
		        $city       = $shipping_address[$prefix . 'city'];
		        $address_1  = $shipping_address[$prefix . 'address_1'];
		        $address_2  = $shipping_address[$prefix . 'address_2'];
		        $first_name = $shipping_address[$prefix . 'first_name'];
		        $last_name  = $shipping_address[$prefix . 'last_name'];
		        $company    = $shipping_address[$prefix . 'company'];

		        $contents = array();
		        foreach ( $shipping as $item_id => $item ) {
		            $cart_item    = WC()->cart->get_cart_item( $item_id );
		            if ( ! $cart_item )
		            	continue;
		            $product_id   = $cart_item['product_id'];
			        $variation_id = $cart_item['variation_id'];
			        $variation    = $cart_item['variation'];
			        $quantity     = $item['qty'];
			        $line_total   = ( (float)$cart_item['line_total'] / $cart_item['quantity'] ) * $quantity;
			        $product_data = $cart_item['data'];

			        $contents[$item_id] = array(
				        'product_id'   => $product_id,
				        'variation_id' => $variation_id,
				        'variation'    => $variation,
				        'quantity'     => $quantity,
				        'line_total'   => $line_total,
				        'data'         => $product_data
                    );
                }

        		$package = array(
			        'contents'        => $contents,
			        'contents_cost'   => array_sum( wp_list_pluck( $contents, 'line_total' ) ),
			        'applied_coupons' => WC()->cart->applied_coupons,
			        'user'            => array( 'ID' => get_current_user_id() ),
			        'destination'     => array(
				        'country'    => $country,
				        'state'      => $state,
				        'postcode'   => $postcode,
				        'city'       => $city,
				        'address'    => $address_1,
				        'address_2'  => $address_2,
				        'first_name' => $first_name,
				        'last_name'  => $last_name,
				        'company'    => $company
			        )
		        );

		        if ( $is_local_pickup ) {
		            $package['local_pickup_package'] = 'yes';
                }

		        $packages[] = $package;
	        }
        	return $packages;
        }

	    /**
	     * When order is being created adds useful meta data for items
	     *
	     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	     * @since 1.0.0
	     *
	     * @param $item
	     * @param $package_key
	     * @param $package
	     * @param $order
	     */
        public function add_shipping_address_meta_data_on_shipping_item( $item, $package_key, $package, $order ) {
	        $multi_shipping_data = WC()->session->get( 'ywcmas_multi_shipping_data' );
	        $multi_shipping_enabled = WC()->session->get( 'ywcmas_multi_shipping_enabled' );
	        if ( $multi_shipping_enabled && $multi_shipping_data ) {
		        $item->add_meta_data( 'ywcmas_shipping_destination', $package['destination'] );
		        $item->add_meta_data( 'ywcmas_shipping_contents', $package['contents'] );
		        $item->add_meta_data( 'ywcmas_shipping_status', 'wcmas-processing' );
	        }
        }

	    /**
	     * Prints the template for the checkbox to enable/disable the multi shipping on the order. If the order contains excluded items, the template won't be printed.
	     *
	     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	     * @since 1.0.0
	     */
	    public function manage_addresses_cb() {
		    if ( WC()->cart->needs_shipping() ) {
			    ob_start();

			    wc_get_template( 'checkout/ywcmas-manage-addresses-cb.php', '', '', YITH_WCMAS_WC_TEMPLATE_PATH );

			    echo ob_get_clean();
            }
	    }

	    /**
	     * Prints the template for all the multi addresses manager. If the order contains excluded items, the template won't be printed.
	     *
	     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	     * @since 1.0.0
	     */
	    public function manage_addresses_content() {
		    ob_start();

		    wc_get_template( 'checkout/ywcmas-manage-addresses-content.php', '', '', YITH_WCMAS_WC_TEMPLATE_PATH );

		    echo ob_get_clean();
	    }

        /*
         * Prints the Addresses Viewer on Checkout page
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
         */
        public function print_address() {
	        $address_id = ! empty( $_POST['ywcmas_address_id'] ) ? stripslashes( $_POST['ywcmas_address_id'] ) : '';

	        ob_start();

	        wc_get_template( 'checkout/ywcmas-manage-addresses-viewer.php', array( 'address_id' => $address_id ), '', YITH_WCMAS_WC_TEMPLATE_PATH );

	        echo ob_get_clean();

            die();
        }

	    /**
	     * Update the default address that the user can set in My Account - Addresses
	     *
	     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	     * @since 1.0.0
	     */
	    public function update_default_address() {
		    $default_address = isset( $_POST['default_address'] ) ? $_POST['default_address'] : '';
		    $user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : '';
		    if ( $default_address && $user_id ) {
			    update_user_meta( $user_id, 'yith_wcmas_default_address', $default_address );

			    ob_start();
			    wc_get_template( 'ywcmas-default-address.php', '', '', YITH_WCMAS_WC_TEMPLATE_PATH . 'myaccount/' );
			    $output = ob_get_clean();
			    wp_send_json_success( $output );
		    } else {
			    wp_send_json_error();
		    }
	    }

        /*
         * Generates or updates the multi shipping data array on WC_Session
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
         */
        public function update_multi_shipping_data() {
	        $cart = WC()->cart->cart_contents;
            $multi_shipping_data = WC()->session->get( 'ywcmas_multi_shipping_data' );
	        $default_address = get_user_meta( get_current_user_id(), 'yith_wcmas_default_address', true );
	        $user_addresses = yith_wcmas_get_user_default_and_custom_addresses( get_current_user_id() );

	        if ( isset( $_POST['multi_shipping_enabled'] ) ) {
	            WC()->session->set( 'ywcmas_multi_shipping_enabled', $_POST['multi_shipping_enabled'] == 'true' ? 1 : 0 );
            }

	        // If multi shipping data array doesn't exist on WC_Session, generate it.
            if ( ! isset( $multi_shipping_data ) || empty( $multi_shipping_data ) ) {
	            $multi_shipping_data = array();
	            if ( ! empty( $cart ) ) {
		            foreach ( $cart as $item_id => $item ) {
			            // If the item if not shippable don't add it to multi_shipping_data array
			            if ( ! $item['data']->needs_shipping() )
				            continue;

			            if ( is_array( $user_addresses ) && ! empty( $user_addresses ) ) {
			                $shipping_selector_id = yith_wcmas_item_is_excluded( $item_id ) ? 'excluded_item' : uniqid();
				            $item_array = array(
					            $shipping_selector_id => array(
				            		'qty' => $item['quantity'],
						            'shipping' => $default_address ? $default_address : key( $user_addresses )
					            )
				            );
				            $multi_shipping_data[ $item_id ] = $item_array;
			            }
		            }
	            }
            } else { // If multi shipping data array exists, check if any update or delete must be done.
	            $item_cart_id = ! empty( $_POST['item_cart_id'] ) ? $_POST['item_cart_id'] : '';
	            $shipping_selector_id = ! empty( $_POST['shipping_selector_id'] ) ? $_POST['shipping_selector_id'] : '';

	            if ( ! empty( $_POST['update_data_action'] ) ) {
	                $data_action = $_POST['update_data_action'];

		            if ( 'ywcmas_update_qty' == $data_action ) {
			            $new_qty = ! empty( $_POST['new_qty'] ) ? $_POST['new_qty'] : '';
			            if ( $shipping_selector_id && $item_cart_id && $new_qty ) {
				            if ( isset( $multi_shipping_data[$item_cart_id][$shipping_selector_id]['qty'] ) ) {
					            // Set new qty on multi_shipping_data array
					            $multi_shipping_data[$item_cart_id][$shipping_selector_id]['qty'] = $new_qty;

					            // Set total qty of the item on WC()->cart
					            $total_qty = 0;
					            foreach ( $multi_shipping_data[$item_cart_id] as $_shipping_selector_id => $_shipping_selector ) {
						            if ( $shipping_selector_id == $_shipping_selector_id ) {
							            $total_qty += $new_qty;
						            } else {
							            $total_qty += $_shipping_selector['qty'];
						            }
					            }
					            WC()->cart->set_quantity( $item_cart_id, $total_qty, true );
				            }
			            }
		            } elseif ( 'ywcmas_update_shipping_address' == $data_action ) {
			            $new_shipping_address = ! empty( $_POST['new_shipping_address'] ) ? stripslashes( $_POST['new_shipping_address'] ) : '';
			            if ( $shipping_selector_id && $item_cart_id && $new_shipping_address ) {
				            $multi_shipping_data[$item_cart_id][$shipping_selector_id]['shipping'] = $new_shipping_address;
			            }
		            } elseif ( 'ywcmas_new_shipping_selector' == $data_action ) {
			            if ( $item_cart_id ) {
				            $number_of_shipping_selectors = count( $multi_shipping_data[$item_cart_id] );
				            $different_addresses_limit = get_option( 'ywcmas_different_addresses_limit', '10' );
				            if ( $number_of_shipping_selectors < $different_addresses_limit ) {
					            $qty_for_new_shipping_selector = 0;
					            foreach ( $multi_shipping_data[$item_cart_id] as $_shipping_selector_id => $_shipping_selector ) {
						            if ( $_shipping_selector['qty'] > 1 ) {
							            $qty_for_new_shipping_selector = (int) $_shipping_selector['qty'] - 1;
							            $multi_shipping_data[$item_cart_id][$_shipping_selector_id]['qty'] = 1;
							            break;
						            }
					            }
					            $multi_shipping_data[$item_cart_id][uniqid()] = array(
						            'qty' => $qty_for_new_shipping_selector,
						            'shipping' => $default_address ? $default_address : key( $user_addresses )
					            );
				            }
			            }
		            } elseif ( 'ywcmas_delete_shipping_selector' == $data_action ) {
			            $shipping_selector_qty = ! empty( $_POST['current_qty'] ) ? $_POST['current_qty'] : '';
			            $current_qty_in_cart = $cart[$item_cart_id]['quantity'];
			            $updated_qty = (int) $current_qty_in_cart - (int) $shipping_selector_qty;
			            unset( $multi_shipping_data[$item_cart_id][$shipping_selector_id] );
			            if ( ! $multi_shipping_data[$item_cart_id] ) {
				            unset( $multi_shipping_data[$item_cart_id] );
			            }
			            WC()->cart->set_quantity( $item_cart_id, $updated_qty, true );
		            }
                }
            }
            // After all changes, save the new multi shipping data array on WC_Session
	        WC()->session->set( 'ywcmas_multi_shipping_data', $multi_shipping_data );


	        // Save on buffer the new tables, which will overwrite the old ones later on JS
	        $this->print_manage_addresses_tables( $multi_shipping_data );
        }

        /*
         * Prints the item tables based on multi shipping data array
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
         */
	    public function print_manage_addresses_tables( $multi_shipping_data ) {
		    ob_start();

		    wc_get_template( 'checkout/ywcmas-manage-addresses-tables.php', array( 'multi_shipping_data' => $multi_shipping_data ), '', YITH_WCMAS_WC_TEMPLATE_PATH );

		    echo ob_get_clean();

		    die();
	    }


	    /*
		 * When a new item is added to WC cart, add it to multi shipping data array as well
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 */
	    public function add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
		    $multi_shipping_data = WC()->session->get( 'ywcmas_multi_shipping_data' );
		    if ( isset( $multi_shipping_data ) ) {
			    // Return if the product is not shippable
		    	if ( ! WC()->cart->get_cart_item( $cart_item_key ) || ! WC()->cart->get_cart_item( $cart_item_key )['data']->needs_shipping() )
		    		return;
		    	// Return is the product is already on the $multi_shipping_data array
		    	if ( isset( $multi_shipping_data[ $cart_item_key ] ) )
		    	    return;

			    $default_address = get_user_meta( get_current_user_id(), 'yith_wcmas_default_address', true );
			    $user_addresses = yith_wcmas_get_user_default_and_custom_addresses( get_current_user_id() );

			    $shipping_selector_id = yith_wcmas_item_is_excluded( $cart_item_key ) ? 'excluded_item' : uniqid();

			    $shipping_selector = array(
			            $shipping_selector_id => array( 'qty' => $quantity,
                                                        'shipping' => $default_address ? $default_address : key( $user_addresses ) ) );
			    $multi_shipping_data[ $cart_item_key ] = $shipping_selector;

			    // After all changes, save the new multi shipping data array on WC_Session
			    WC()->session->set( 'ywcmas_multi_shipping_data', $multi_shipping_data );
		    }
	    }

	    /*
		 * When an item is removed from WC cart, remove it from multi shipping data array as well
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 */
	    public function cart_item_removed( $cart_item_key ) {
		    $multi_shipping_data = WC()->session->get( 'ywcmas_multi_shipping_data' );
		    if ( isset( $multi_shipping_data ) ) {
			    if ( isset( $multi_shipping_data[$cart_item_key] ) ) {
				    unset( $multi_shipping_data[$cart_item_key] );

				    // After all changes, save the new multi shipping data array on WC_Session
				    WC()->session->set( 'ywcmas_multi_shipping_data', $multi_shipping_data );
			    }
		    }
	    }

	    /*
		 * When a item is restored on WC cart, restore it on multi shipping data array as well
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 */
	    public function cart_item_restored( $cart_item_key ) {
		    $multi_shipping_data = WC()->session->get( 'ywcmas_multi_shipping_data' );
		    if ( isset( $multi_shipping_data ) ) {
			    // Return if the product is not shippable
			    if ( ! WC()->cart->get_cart_item( $cart_item_key )['data']->needs_shipping() )
				    return;

			    $default_address = get_user_meta( get_current_user_id(), 'yith_wcmas_default_address', true );
			    $user_addresses = yith_wcmas_get_user_default_and_custom_addresses( get_current_user_id() );

			    // Get the quantity of the restored item
			    $shipping_selector_id = yith_wcmas_item_is_excluded( $cart_item_key ) ? 'excluded_item' : uniqid();
			    $quantity = isset( WC()->cart->cart_contents[ $cart_item_key ]['quantity'] ) ? WC()->cart->cart_contents[ $cart_item_key ]['quantity'] : '';
			    $shipping_selector = array(
			            $shipping_selector_id => array( 'qty' => $quantity ? $quantity : 1,
                                                        'shipping' => $default_address ? $default_address : key( $user_addresses ) ) );
			    $multi_shipping_data[ $cart_item_key ] = $shipping_selector;
			    // After all changes, save the new multi shipping data array on WC_Session
			    WC()->session->set( 'ywcmas_multi_shipping_data', $multi_shipping_data );
		    }
	    }

	    /**
	     * When a quantity of an item is updated, update it on multi_shipping_data array
	     *
	     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	     * @since 1.0.0
	     *
	     * @param $cart_item_key
	     * @param $quantity
	     */
	    public function cart_item_qty_updated( $cart_item_key, $quantity ) {
		    $multi_shipping_data = WC()->session->get( 'ywcmas_multi_shipping_data' );
		    if ( isset( $multi_shipping_data ) && $multi_shipping_data[$cart_item_key] ) {
			    $first = true;
			    foreach ( $multi_shipping_data[$cart_item_key] as $shipping_selector_id => &$shipping_selector ) {
				    if ( $first ) {
					    $shipping_selector['qty'] = $quantity;
				    } else {
					    unset( $multi_shipping_data[$cart_item_key][$shipping_selector_id] );
				    }
				    $first = false;
			    }
			    // After all changes, save the new multi shipping data array on WC_Session
			    WC()->session->set( 'ywcmas_multi_shipping_data', $multi_shipping_data );
		    }
	    }

	    /*
	     * Unset the multi shipping data array when order is placed
	     *
	     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	     * @since 1.0.0
	     */
	    public function checkout_order_processed() {
		    $multi_shipping_data = WC()->session->get( 'ywcmas_multi_shipping_data' );
		    if ( isset( $multi_shipping_data ) ) {
			    unset( WC()->session->ywcmas_multi_shipping_data );
			    unset( WC()->session->ywcmas_multi_shipping_enabled );
		    }
	    }

	    /**
	     * Adds rows for every destination the order has in the order details table
	     *
	     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	     * @since 1.0.0
	     *
	     * @param $total_rows
	     * @param WC_Order $order
	     * @param $tax_display
	     *
	     * @return mixed
	     */
	    public function add_shipping_rows_in_order_details( $total_rows, $order ) {
		    $shipping_items = $order->get_items( 'shipping' );
		    if ( ! $shipping_items || count( $shipping_items ) <= 1 ) {
			    return $total_rows;
		    }

		    foreach ( $shipping_items as $shipping_item_id => $shipping_item ) {
			    if ( ! $shipping_item->get_meta( 'ywcmas_shipping_destination' ) ) {
				    break;
			    }
			    $is_local_pickup = 'local_pickup' == $shipping_item->get_method_id();
			    $address = yith_wcmas_shipping_address_from_destination_array( $shipping_item->get_meta( 'ywcmas_shipping_destination' ) );
			    $label = $is_local_pickup ? esc_html__( 'Local pickup', 'yith-multiple-shipping-addresses-for-woocommerce' ) : sprintf( esc_html__( 'Ship to: %s', 'yith-multiple-shipping-addresses-for-woocommerce' ), $address );

			    $value = $shipping_item->get_method_title();
			    $value .= $shipping_item->get_total() != 0 ? ' - ' . wc_price( $shipping_item->get_total() ) : '';
			    if ( $value && $shipping_item->get_meta( 'Items' ) )
				    $value .= '<br><small>' . $shipping_item->get_meta( 'Items' ) . '</small>';
			    $status = yith_wcmas_shipping_item_statuses()[ $shipping_item->get_meta( 'ywcmas_shipping_status' ) ];
			    $value .= '<br><small><strong>' . esc_html__( 'Status:', 'yith-multiple-shipping-addresses-for-woocommerce' ) . '</strong>&nbsp;' . $status . '</small>';

			    $shipping = array( 'shipping_' . $shipping_item_id => array(
				    'label' => $label,
				    'value' => $value
			    ) );
			    yith_wcmas_array_insert( $total_rows, 'shipping', $shipping );
		    }

		    $total_rows['shipping']['label'] = esc_html__( 'Shipping total:', 'yith-multiple-shipping-addresses-for-woocommerce' );

		    return $total_rows;
	    }

	    /**
	     * Remove the label "via Free Shipping, Flat rate..." on the Shipping row of the order details table
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
	     *
	     * @param $text string Default text
	     * @param $order WC_Order The Order
	     *
	     * @return string
	     */
	    public function remove_shipped_via_text( $text, $order ) {
		    $shipping_items = $order->get_items( 'shipping' );
		    if ( ! $shipping_items || $shipping_items <= 1 ) {
			    return $text;
		    }

		    foreach ( $shipping_items as $shipping_item_id => $shipping_item ) {
			    if ( $shipping_item->get_meta( 'ywcmas_shipping_destination' ) ) {
				    return '';
			    }
		    }

		    return $text;
	    }

	    /**
         * Prepare data and then print template content for the prettyPhoto pop-up
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
	     */
	    public function shipping_address_form() {
		    $current_address_id = ! empty( $_GET['address_id'] ) ? stripslashes( $_GET['address_id'] ) : false;
		    $current_address = false;
		    if ( $current_address_id ) {
			    $current_address = yith_wcmas_get_user_address_by_id( $current_address_id );
		    }

		    $current_user = wp_get_current_user();

		    if ( $current_address ) {
			    if ( YITH_WCMAS_BILLING_ADDRESS_ID == $current_address_id ) {
				    $address = WC()->countries->get_address_fields( get_user_meta( get_current_user_id(), 'billing_country', true ), 'billing_' );
			    } elseif ( YITH_WCMAS_DEFAULT_SHIPPING_ADDRESS_ID == $current_address_id ) {
				    $address = WC()->countries->get_address_fields( get_user_meta( get_current_user_id(), 'shipping_country', true ), 'shipping_' );
			    } else {
				    $address = $current_address ? WC()->countries->get_address_fields( $current_address['shipping_country'], 'shipping_' ) : '';
			    }
		    } else {
			    $address = WC()->countries->get_address_fields( '', 'shipping_' );
		    }


		    if ( ! $address ) {
			    die( 'ywcmas_no_address_fields_by_country' );
		    }
		    foreach ( $address as $key => $field ) {
			    $value = ! empty( $current_address[ $key ] ) ? $current_address[ $key ] : get_user_meta( get_current_user_id(), $key, true );

			    if ( ! $value ) {
				    switch ( $key ) {
					    case 'billing_email' :
					    case 'shipping_email' :
						    $value = $current_user->user_email;
						    break;
					    case 'billing_country' :
					    case 'shipping_country' :
						    $value = ! empty( $current_address['shipping_country'] ) ? $current_address['shipping_country'] : WC()->countries->get_base_country();
						    break;
					    case 'billing_state' :
					    case 'shipping_state' :
						    $value = ! empty( $current_address['shipping_state'] ) ? $current_address['shipping_state'] : WC()->countries->get_base_state();
						    break;
				    }
			    }

			    $address[ $key ]['value'] = $value;
		    }

		    $args = array(
			    'address' => $address,
			    'current_address_id'  => $current_address_id,
			    'current_address'  => $current_address,
		    );

		    ob_start();

		    wc_get_template( 'ywcmas-address-form.php', $args, '', YITH_WCMAS_WC_TEMPLATE_PATH );

		    echo ob_get_clean();

		    die();
	    }

	    /**
	     * Saves the address from the new/edit address form
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
	     */
	    public static function save_address() {
		    check_ajax_referer( 'ywcmas_form_address', 'security' );
		    $address_id = ! empty( $_POST['address_id'] ) ? stripslashes( $_POST['address_id'] ) : false;
		    $location = ! empty( $_POST['location'] ) ? $_POST['location'] : '';
		    $current_address_id = ! empty( $_POST['current_address_id'] ) ? $_POST['current_address_id'] : false;
		    $user_id = get_current_user_id();


		    $load_address = YITH_WCMAS_BILLING_ADDRESS_ID == $address_id ? 'billing' : 'shipping';

		    $address = WC()->countries->get_address_fields( esc_attr( $_POST[ $load_address . '_country' ] ), $load_address . '_' );

		    if ( ! $current_address_id && ( YITH_WCMAS_BILLING_ADDRESS_ID == $address_id || YITH_WCMAS_DEFAULT_SHIPPING_ADDRESS_ID == $address_id ) ) {
			    wc_add_notice(
				    sprintf( esc_html__( '%s and %s are not valid shipping identifiers.', 'yith-multiple-shipping-addresses-for-woocommerce' ),
					    YITH_WCMAS_BILLING_ADDRESS_ID,
					    YITH_WCMAS_DEFAULT_SHIPPING_ADDRESS_ID ), 'error' );
		    }

		    foreach ( $address as $key => $field ) {

			    if ( ! isset( $field['type'] ) ) {
				    $field['type'] = 'text';
			    }

			    // Get Value.
			    switch ( $field['type'] ) {
				    case 'checkbox' :
					    $_POST[ $key ] = (int) isset( $_POST[ $key ] );
					    break;
				    default :
					    $_POST[ $key ] = isset( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : '';
					    break;
			    }

			    // Hook to allow modification of value.
			    $_POST[ $key ] = apply_filters( 'woocommerce_process_myaccount_field_' . $key, $_POST[ $key ] );

			    // Validation: Required fields.
			    if ( ! empty( $field['required'] ) && empty( $_POST[ $key ] ) ) {
				    wc_add_notice( sprintf( esc_html__( '%s is a required field.', 'yith-multiple-shipping-addresses-for-woocommerce' ), $field['label'] ), 'error' );
			    }

			    if ( ! empty( $_POST[ $key ] ) ) {

				    // Validation rules.
				    if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
					    foreach ( $field['validate'] as $rule ) {
						    switch ( $rule ) {
							    case 'postcode' :
								    $_POST[ $key ] = strtoupper( str_replace( ' ', '', $_POST[ $key ] ) );

								    if ( ! WC_Validation::is_postcode( $_POST[ $key ], $_POST[ $load_address . '_country' ] ) ) {
									    wc_add_notice( esc_html__( 'Please enter a valid postcode / ZIP.', 'yith-multiple-shipping-addresses-for-woocommerce' ), 'error' );
								    } else {
									    $_POST[ $key ] = wc_format_postcode( $_POST[ $key ], $_POST[ $load_address . '_country' ] );
								    }
								    break;
							    case 'phone' :
								    $_POST[ $key ] = wc_format_phone_number( $_POST[ $key ] );

								    if ( ! WC_Validation::is_phone( $_POST[ $key ] ) ) {
									    wc_add_notice( sprintf( esc_html__( '%s is not a valid phone number.', 'yith-multiple-shipping-addresses-for-woocommerce' ), '<strong>' . $field['label'] . '</strong>' ), 'error' );
								    }
								    break;
							    case 'email' :
								    $_POST[ $key ] = strtolower( $_POST[ $key ] );

								    if ( ! is_email( $_POST[ $key ] ) ) {
									    wc_add_notice( sprintf( esc_html__( '%s is not a valid email address.', 'yith-multiple-shipping-addresses-for-woocommerce' ), '<strong>' . $field['label'] . '</strong>' ), 'error' );
								    }
								    break;
						    }
					    }
				    }
			    }
		    }

		    do_action( 'woocommerce_after_save_address_validation', $user_id, $load_address, $address );

		    if ( 0 === wc_notice_count( 'error' ) ) {
			    if ( YITH_WCMAS_BILLING_ADDRESS_ID == $address_id || YITH_WCMAS_DEFAULT_SHIPPING_ADDRESS_ID == $address_id ) {
				    foreach ( $address as $key => $field ) {
					    update_user_meta( $user_id, $key, $_POST[ $key ] );
				    }
			    } else {
				    $new_address = array();
				    foreach ( $address as $key => $field ) {
					    $new_address[ $key ] = $_POST[ $key ];
				    }

				    if ( $user_id ) {
					    $addresses = yith_wcmas_get_user_custom_addresses( get_current_user_id() );
					    if ( $addresses ) {
						    if ( $current_address_id ) {
							    unset( $addresses[$current_address_id] );
						    }
						    if ( is_array( $addresses ) ) {
							    if ( array_key_exists( $address_id, $addresses ) ) {
								    wp_send_json_error( 'ywcmas_duplicated_address_id' );
							    }
							    $addresses[$address_id] = $new_address;
							    update_user_meta( get_current_user_id(), 'yith_wcmas_shipping_addresses', $addresses );
						    } else {
							    $new_addresses[$address_id] = $new_address;
							    update_user_meta( get_current_user_id(), 'yith_wcmas_shipping_addresses', $new_addresses );
						    }
					    } else {
						    $new_addresses[$address_id] = $new_address;
						    update_user_meta( get_current_user_id(), 'yith_wcmas_shipping_addresses', $new_addresses );
					    }
				    } else {
					    $addresses = WC()->session->get( 'ywcmas_guest_user_addresses' );
					    if ( $addresses ) {
						    if ( $current_address_id ) {
							    unset( $addresses[$current_address_id] );
						    }
						    if ( is_array( $addresses ) ) {
							    if ( array_key_exists( $address_id, $addresses ) ) {
								    wp_send_json_error( 'ywcmas_duplicated_address_id' );
							    }
							    $addresses[$address_id] = $new_address;
							    WC()->session->set( 'ywcmas_guest_user_addresses', $addresses );
						    } else {
							    $new_addresses[$address_id] = $new_address;
							    WC()->session->set( 'ywcmas_guest_user_addresses', $new_addresses );
						    }
					    } else {
						    $new_addresses[$address_id] = $new_address;
						    WC()->session->set( 'ywcmas_guest_user_addresses', $new_addresses );
					    }
				    }
			    }

			    if ( $location != 'checkout' ) {
				    wc_add_notice( esc_html__( 'Address changed successfully.', 'yith-multiple-shipping-addresses-for-woocommerce' ) );
			    }

			    do_action( 'woocommerce_customer_save_address', $user_id, $load_address );

			    wp_send_json_success( 'ywcmas_address_saved' );
		    } else {
			    wp_send_json_error( 'ywcmas_field_error' );
		    }
	    }

	    /**
	     * Pop-up content for deleting addresses
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
	     */
	    public function delete_shipping_address_window() {
		    $current_address_id = ! empty( $_GET['address_id'] ) ? stripslashes( $_GET['address_id'] ) : false;
		    $delete_all = ! empty( $_GET['delete_all'] ) ? $_GET['delete_all'] : false;
		    if ( $delete_all ) {
			    ?>
			    <div><?php esc_html_e( 'Are you sure you want to delete all the additional addresses?', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></div>
			    <div>
				    <button id="ywcmas_delete_address_yes"><?php esc_html_e( 'Yes', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></button>
				    <button id="ywcmas_delete_address_no"><?php esc_html_e( 'No', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></button>
				    <input id="ywcmas_delete_all" type="hidden" value="<?php echo true; ?>">
			    </div>
			    <?php
		    } else if ( $current_address_id ) {
			    $current_address = yith_wcmas_get_user_address_by_id( $current_address_id );
			    if ( $current_address && is_array( $current_address ) ) {
				    ?>
				    <div><?php printf( esc_html__( 'Are you sure you want to delete address "%s"?', 'yith-multiple-shipping-addresses-for-woocommerce' ), $current_address_id ); ?></div>
				    <div>
					    <button id="ywcmas_delete_address_yes"><?php esc_html_e( 'Yes', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></button>
					    <button id="ywcmas_delete_address_no"><?php esc_html_e( 'No', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></button>
					    <input id="ywcmas_current_address_id" type="hidden" value="<?php echo $current_address_id; ?>">
				    </div>
				    <?php
			    } else {
				    echo '<div>' . esc_html__( 'Shipping address does not exist. Please reload.', 'yith-multiple-shipping-addresses-for-woocommerce' ) . '</div>';
			    }
		    } else {
			    echo '<div>' . esc_html__( 'Shipping address is not provided', 'yith-multiple-shipping-addresses-for-woocommerce' ) . '</div>';
		    }
		    die();
	    }

	    /**
	     * Programmatic deleting of addresses via AJAX
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
	     */
	    public function delete_shipping_address() {
		    check_ajax_referer( 'ywcmas-delete-shipping-address', 'security' );

		    $current_address_id = ! empty( $_POST['current_address_id'] ) ? stripslashes( $_POST['current_address_id'] ) : false;
		    $delete_all = ! empty( $_POST['delete_all'] ) ? $_POST['delete_all'] : false;

		    do_action( 'ywcmas_before_delete_shipping_addresses' );

		    if ( $delete_all ) {
			    delete_user_meta( get_current_user_id(), 'yith_wcmas_shipping_addresses' );
			    update_user_meta( get_current_user_id(), 'yith_wcmas_default_address', '' );
			    wc_add_notice( sprintf( esc_html__( 'Additional addresses deleted successfully', 'yith-multiple-shipping-addresses-for-woocommerce' ), $current_address_id ) );
			    wp_send_json_success( 'ywcmas_address_deleted' );
		    } else if ( $current_address_id ) {
			    $current_address = yith_wcmas_get_user_address_by_id( $current_address_id );
			    if ( $current_address && is_array( $current_address ) ) {
				    $addresses = yith_wcmas_get_user_custom_addresses( get_current_user_id() );
				    unset( $addresses[$current_address_id] );
				    if ( get_current_user_id() ) {
					    update_user_meta( get_current_user_id(), 'yith_wcmas_shipping_addresses', $addresses );
					    if ( $current_address_id == get_user_meta( get_current_user_id(), 'yith_wcmas_default_address', true ) ) {
						    update_user_meta( get_current_user_id(), 'yith_wcmas_default_address', '' );
                        }
				    } else {
					    WC()->session->set( 'ywcmas_guest_user_addresses', $addresses );
				    }
				    wc_add_notice( sprintf( esc_html__( 'Address "%s" deleted successfully', 'yith-multiple-shipping-addresses-for-woocommerce' ), $current_address_id ) );
				    wp_send_json_success( 'ywcmas_address_deleted' );
			    } else {
				    wp_send_json_error( 'ywcmas_address_does_not_exist' );
			    }
		    } else {
			    wp_send_json_error( 'ywcmas_no_address_id' );
		    }
		    die();
	    }

	    /**
         * Saves the addresses created by the guest user on the post meta when processing the new user on checkout
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
	     */
	    public function save_guest_user_addresses_on_post_meta( $customer_id ) {
		    $multi_shipping_enabled = WC()->session->get( 'ywcmas_multi_shipping_enabled' );
		    $multi_shipping = yith_wcmas_get_multi_shipping_array();
		    if ( ! $multi_shipping_enabled || ! $multi_shipping || ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			    return $customer_id;
		    }

		    $addresses = WC()->session->get( 'ywcmas_guest_user_addresses' );
		    if ( $addresses ) {
			    update_user_meta( $customer_id, 'yith_wcmas_shipping_addresses', $addresses );
		    }
		    return $customer_id;
	    }

	    /**
	     * Calls the shortcode ywcmas_custom_addresses for displaying it on My Account - Addresses
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
	     */
	    public function add_new_shipping_address_button() {
		    global $wp;

		    if ( isset( $wp->query_vars['edit-address'] ) && ! empty( $wp->query_vars['edit-address'] ) ) {
			    return;
		    }
		    echo do_shortcode( '[ywcmas_custom_addresses]' );
	    }

	    /**
         * Shortcode for displaying New shipping address button and Additional addresses section
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0.0
         *
	     * @return string Template output
	     */
	    public function custom_addresses() {
		    ob_start();

		    wc_get_template( 'ywcmas-custom-addresses.php', '', '', YITH_WCMAS_WC_TEMPLATE_PATH . 'myaccount/' );

		    return ob_get_clean();
	    }

	    public function package_rates( $rates, $package ) {
		    $multi_shipping_enabled = WC()->session->get( 'ywcmas_multi_shipping_enabled' );
		    $multi_shipping = yith_wcmas_get_multi_shipping_array();
		    if ( ! $multi_shipping_enabled || ! $multi_shipping ) {
			    return $rates;
		    }

		    if ( isset( $package['local_pickup_package'] ) && 'yes' == $package['local_pickup_package'] ) {
			    foreach ( $rates as $rate_id => &$rate ) {
				    if ( substr( $rate_id, 0, 12 ) !== 'local_pickup'  ) {
					    unset( $rates[$rate_id] );
				    }
			    }
            } else {
			    foreach ( $rates as $rate_id => &$rate ) {
				    if ( substr( $rate_id, 0, 12 ) === 'local_pickup'  ) {
					    unset( $rates[$rate_id] );
				    }
			    }
            }

		    return $rates;
	    }

    }
}