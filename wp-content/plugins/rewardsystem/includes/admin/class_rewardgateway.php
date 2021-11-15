<?php

function init_reward_gateway_class() {

	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return ;
	}

	class WC_Reward_Gateway extends WC_Payment_Gateway {

		public function __construct() {
			global $woocommerce ;
			$this->id                 = 'reward_gateway' ;
			$this->method_title       = __( 'SUMO Reward Points Payment Gateway' , 'rewardsystem' ) ;
			$this->method_description = __( 'Pay with your SUMO Reward Points' , 'rewardsystem' ) ;
			$this->has_fields         = false ; //Load Form Fields
			$this->init_form_fields() ;
			$this->init_settings() ;
			$this->title              = $this->get_option( 'title' ) ;
			$this->description        = $this->get_option( 'description' ) ;
			$this->description        = do_shortcode( $this->description ) ;

			$this->is_forced_automatic_subscription_payment = $this->get_option( 'rs_subscription_based_payment_option' ) == 'yes' && $this->get_option( 'rs_force_auto_r_manual_subscription_payment' ) == '2' ;
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id , array( $this , 'process_admin_options' ) ) ;
			add_action( 'woocommerce_after_checkout_validation' , array( $this , 'rs_remove_pending_order' ) , 11 , 2 ) ;
		}

		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'                           => array(
					'title'   => __( 'Enable/Disable' , 'rewardsystem' ) ,
					'type'    => 'checkbox' ,
					'label'   => __( 'Enable Rewards Point Gateway' , 'rewardsystem' ) ,
					'default' => 'no'
				) ,
				'title'                             => array(
					'title'       => __( 'Title' , 'rewardsystem' ) ,
					'type'        => 'text' ,
					'description' => __( 'This Controls the Title which the user sees during checkout' , 'rewardsystem' ) ,
					'default'     => __( 'SUMO Reward Points Payment Gateway' , 'rewardsystem' ) ,
					'desc_tip'    => true ,
				) ,
				'description'                       => array(
					'title'       => __( 'Description' , 'rewardsystem' ) ,
					'type'        => 'textarea' ,
					'description' => __( 'This controls the description which the user sees during checkout.' , 'rewardsystem' ) ,
					'default'     => __( 'Pay with your SUMO Reward Points' , 'rewardsystem' ) ,
					'desc_tip'    => true ,
				) ,
				'error_payment_gateway'             => array(
					'title'       => 'Error Message' ,
					'type'        => 'textarea' ,
					'description' => __( 'This Controls the errror message which is displayed during Checkout' , 'rewardsystem' ) ,
					'desc_tip'    => true ,
					'default'     => __( 'You need [needpoints] Points in your Account .But You have only [userpoints] Points.' , 'rewardsystem' ) ,
				) ,
				'error_message_for_payment_gateway' => array(
					'title'       => 'Error Message for Payment Gateway' ,
					'type'        => 'textarea' ,
					'description' => __( 'This Controls the error message which is displayed during Checkout' , 'rewardsystem' ) ,
					'desc_tip'    => true ,
					'default'     => __( 'Maximum Cart Total has been Limited to [maximum_cart_total]' ) ,
				) ,
					) ;

			if ( is_sumosubscriptions_active() ) {
				$this->form_fields[ 'rs_subscription_based_payment_option' ] = array(
					'title'    => __( 'Remove the option for the Subscriber to choose Automatic/Manual Payment when placed using SUMO Reward Points Payment Gateway' , 'rewardsystem' ) ,
					'type'     => 'checkbox' ,
					'std'      => 'no' ,
					'default'  => 'no' ,
					'desc_tip' => __( 'This option controls whether the user should have an option for preapproving the future subscription renewals.' , 'rewardsystem' ) ,
						) ;

				$this->form_fields[ 'rs_force_auto_r_manual_subscription_payment' ] = array(
					'title'    => __( 'Force Automatic/Manual Payment' , 'rewardsystem' ) ,
					'type'     => 'select' ,
					'css'      => 'width:315px' ,
					'std'      => '2' ,
					'options'  => array(
						'1' => __( 'Force Manual Reward Points Payment' , 'rewardsystem' ) ,
						'2' => __( 'Force Automatic Reward Points Payment' , 'rewardsystem' ) ,
					) ,
					'desc'     => __( 'This option controls how the subscription renewals has to be managed when the user purchases using inbuilt reward points payment gateway.' , 'rewardsystem' ) ,
					'desc_tip' => true
						) ;
			}
		}

		public function process_payment( $order_id ) {
			global $woocommerce ;
			$redeemedpoints = gateway_points( $order_id ) ;
			$order          = new WC_Order( $order_id ) ;
			$OrderObj       = srp_order_obj( $order ) ;
			$payment_method = $OrderObj[ 'payment_method' ] ;
			update_post_meta( $order_id , 'total_redeem_points_for_order_point_price' , $redeemedpoints ) ;
			update_post_meta( $order_id , 'frontendorder' , 1 ) ;
			//For SUMOSubscriptions, Automatic Subscription Payment Compatibility.
			$parent_id      = get_post_parent( $order ) > 0 ? get_post_parent( $order ) : $order_id ;

			if ( is_sumosubscriptions_active() ) {
				if ( ( isset( $_REQUEST[ 'rs_reward_points_payment_selection' ] ) && '1' == wc_clean(wp_unslash($_REQUEST[ 'rs_reward_points_payment_selection' ] )) ) || $this->is_forced_automatic_subscription_payment ) {

					if ( function_exists( 'sumo_save_subscription_payment_info' ) ) {
						sumo_save_subscription_payment_info( $order_id , array(
							'payment_type'         => 'auto' ,
							'payment_method'       => $payment_method ,
							'payment_order_amount' => $order->get_total() ,
						) ) ;
					} else {
						update_post_meta( $parent_id , 'sumo_parent_order_auto_manual' , 'auto' ) ;
						update_post_meta( $parent_id , 'sumo_order_payment_method' , $payment_method ) ;
						update_post_meta( $parent_id , 'sumo_totalamount' , $order->get_total() ) ;
					}
				} else {
					if ( function_exists( 'sumo_save_subscription_payment_info' ) ) {
						sumo_save_subscription_payment_info( $order_id , array(
							'payment_type'         => 'manual' ,
							'payment_method'       => $payment_method ,
							'payment_order_amount' => $order->get_total() ,
						) ) ;
					} else {
						update_post_meta( $parent_id , 'sumo_parent_order_auto_manual' , 'manual' ) ;
						update_post_meta( $parent_id , 'sumo_order_payment_method' , $payment_method ) ;
						update_post_meta( $parent_id , 'sumo_totalamount' , $order->get_total() ) ;
					}
				}
			}

			$order->payment_complete() ;
			$order_status = get_option( 'rs_order_status_after_gateway_purchase' , 'completed' ) ;

			$order->update_status( $order_status ) ;
			//Reduce Stock Levels
			//  $order->reduce_order_stock();
			//Remove Cart
			$woocommerce->cart->empty_cart() ;

			//Redirect the User
			return array(
				'result'   => 'success' ,
				'redirect' => $this->get_return_url( $order )
					) ;
			wc_add_notice( __( 'Payment error:' , 'woothemes' ) . $error_message , 'error' ) ;
			return ;
		}

		public function rs_remove_pending_order( $data, $error ) {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			if ( isset( $_REQUEST[ 'payment_method' ] ) && 'reward_gateway' != wc_clean(wp_unslash($_REQUEST[ 'payment_method' ])) ) {
				return ;
			}

			global $woocommerce ;
			if ( ! srp_check_is_array( $woocommerce->cart->cart_contents ) ) {
				return ;
			}

			$MaxDiscount     = get_option( 'rs_max_redeem_discount_for_sumo_reward_points' ) ;
			$PointsData      = new RS_Points_Data( get_current_user_id() ) ;
			$Points          = $PointsData->total_available_points() ;
			$PointPriceValue = array() ;
			$PointPriceTax   = array() ;
			$LineTotal       = array() ;
			$Price           = array() ;
			$tax_display     = get_option( 'woocommerce_tax_display_cart' ) ;
			
			$exclude_coupon = false;
			foreach ( $woocommerce->cart->cart_contents as $item ) {
				$product_id     = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
				$bundledproduct = isset( $item[ '_bundled_by' ] ) ? $item[ '_bundled_by' ] : 0 ;
				$enable         = calculate_point_price_for_products( $product_id ) ;
				if ( ! empty( $enable[ $product_id ] ) && null == $bundledproduct ) {
					$PointPriceValue[] = $enable[ $product_id ] * $item[ 'quantity' ] ;
					if ( 'no' == get_option( 'woocommerce_prices_include_tax' )) {
						$PointPriceTax[]   = $item[ 'line_subtotal_tax' ] ;
					}
				} else {
					$Price[] = get_post_meta( $product_id , '_price' , true ) ;
					if ( 'incl' == $tax_display ) {
						$LineTotal[] = $item[ 'line_subtotal' ] + $item[ 'line_subtotal_tax' ] ;
					} else {
						$LineTotal[] = $item[ 'line_subtotal' ] ;
					}
				}
				
				if ('1' == check_display_price_type($product_id)) {
					$exclude_coupon = true;
				}
			}
			$ShippingCost =  'incl' == $tax_display ? $woocommerce->shipping->shipping_total + array_sum( $woocommerce->shipping->shipping_taxes ) : $woocommerce->shipping->shipping_total ;
			$CouponAmnt   = array() ;

			$user       = get_user_by( 'ID' , get_current_user_id() ) ;
			$Username   = is_object( $user ) ? $user->user_login : '' ;
			$Redeem     = 'sumo_' . strtolower( "$Username" ) ;
			$AutoRedeem = 'auto_redeem_' . strtolower( "$Username" ) ;

			$redeeming_coupon = 0 ;
			if ( srp_check_is_array( $woocommerce->cart->get_applied_coupons() ) ) {
				foreach ( $woocommerce->cart->get_applied_coupons() as $Coupon ) {
					
					if ($exclude_coupon) {
						continue;
					}
					
					$CouponObj    = new WC_Coupon( $Coupon ) ;
					$CouponAmnt[] = ( float ) $woocommerce->version >= ( float ) '3.0' ? $CouponObj->get_amount() : $CouponObj->coupon_amount ;

					if ( $Coupon == $Redeem || $Coupon == $AutoRedeem ) {
						$redeeming_coupon = ( float ) $woocommerce->version >= ( float ) '3.0' ? $CouponObj->get_amount() : $CouponObj->coupon_amount ;
					}
				}
			}
			$BalancePoints  = $ShippingCost + array_sum( $LineTotal ) + array_sum( $PointPriceTax ) ;
			$excl_tax       = 'excl' == $tax_display ? WC()->cart->get_taxes() : array() ;
			$BalancePoints  = $BalancePoints + apply_filters( 'rs_points_for_additional_fee' , WC()->cart->get_fee_total() ) + array_sum( $excl_tax ) ;
			$redeemedpoints = ( array_sum( $PointPriceValue ) + redeem_point_conversion( $BalancePoints , get_current_user_id() ) ) - array_sum( $CouponAmnt ) ;

			/* Compatible with YITH WooCommerce Gift Cards Premium for Calculating Order Total */
			$yith_gift_value = get_yith_gift_card_value() ;
			$redeemedpoints  = 0 != $yith_gift_value ? $redeemedpoints - $yith_gift_value : $redeemedpoints ;

			$Points = 0 != $Points ? $Points - $redeeming_coupon : $Points ;

			if ( $Points < $redeemedpoints ) {
				$error_msg    = $this->get_option( 'error_payment_gateway' ) ;
				$finalreplace = str_replace( array( '[userpoints]' , '[needpoints]' ) , array( '<b>' . $Points . '</b>' , '<b>' . round_off_type( $redeemedpoints ) . '</b>' ) , $error_msg ) ;
				$error->add( 'error' , __( $finalreplace , 'rewardsystem' ) ) ;
			} else {
				if ( ! empty( $MaxDiscount ) ) {
					if ( $MaxDiscount > $BalancePoints ) {
						$error_msg    = $this->get_option( 'error_message_for_payment_gateway' ) ;
						$finalreplace = str_replace( '[maximum_cart_total]' , get_woocommerce_currency_symbol() . round_off_type( $MaxDiscount ) , $error_msg ) ;
						$error->add( 'error' , __( $finalreplace , 'rewardsystem' ) ) ;
					}
				}
			}
		}

	}

	/*
	 * Get Yith Gift Card Value
	 */

	function get_yith_gift_card_value() {

		if ( ! isset( WC()->cart->applied_gift_cards ) ) {
			return 0 ;
		}

		$amount = 0 ;

		foreach ( WC()->cart->applied_gift_cards as $code ) :
			$amount = isset( WC()->cart->applied_gift_cards_amounts[ $code ] ) ? WC()->cart->applied_gift_cards_amounts[ $code ] : 0 ;
		endforeach ;

		return $amount ;
	}

	function gateway_points( $order_id ) {
		$CouponAmnt      = array() ;
		$PointPriceValue = array() ;
		$LineTotal       = array() ;
		$PointPriceTax   = array() ;
		$order           = new WC_Order( $order_id ) ;
		$fee_total       = 0 ;
		$tax_display     = get_option( 'woocommerce_tax_display_cart' ) ;
		
		$coupon_display = true;
		foreach ( $order->get_items() as $item ) {
			$product_id     = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
			$bundledproduct = isset( $item[ '_bundled_by' ] ) ? $item[ '_bundled_by' ] : 0 ;
			$enable         = calculate_point_price_for_products( $product_id ) ;
			if ( ! empty( $enable[ $product_id ] ) && null == $bundledproduct ) {
				$PointPriceValue[] = $enable[ $product_id ] * $item[ 'qty' ] ;
				if ( 'incl' == $tax_display ) {
					$PointPriceTax[]   = $item[ 'line_subtotal_tax' ] ;
				}
			} else {
				if ( 'incl' == $tax_display ) {
					$LineTotal[] = $item[ 'line_subtotal' ] + $item[ 'line_subtotal_tax' ] ;
				} else {
					$LineTotal[] = $item[ 'line_subtotal' ] ;
				}
			}
			
			$display_point_price_type = check_display_price_type( $product_id ) ;
			if ('1' == $display_point_price_type ) {
				   $coupon_display = false;
			}
		}

		// The fee total amount
		foreach ( $order->get_items( 'fee' ) as $item_fee ) {
			if ( 'incl' == $tax_display ) {
				$fee_total = $fee_total + $item_fee->get_total() + $item_fee->get_total_tax() ;
			} else {
				$fee_total += $item_fee->get_total() ;
			}
		}
		$OrderObj       = srp_order_obj( $order ) ;
		$UserId         = $OrderObj[ 'order_userid' ] ;
		$excl_tax_total = 'excl' == $tax_display ? $order->get_total_tax() : 0 ;
		$shipping_tax   = 'excl' == $tax_display  ? $order->get_total_shipping() : $order->get_total_shipping() + $order->get_shipping_tax() ;
		$Points         = $shipping_tax + array_sum( $LineTotal ) + array_sum( $PointPriceTax ) + apply_filters( 'rs_points_for_additional_fee' , $fee_total ) + $excl_tax_total ;
		$AppliedCoupons = $order->get_items( array( 'coupon' ) ) ;
		if ( $coupon_display && srp_check_is_array( $AppliedCoupons ) ) {
			foreach ( $AppliedCoupons as $Coupon ) {
				$CouponAmnt[] = $Coupon[ 'discount_amount' ] ;
			}
		}
		$redeemedpoints  = ( array_sum( $PointPriceValue ) + redeem_point_conversion( $Points , $UserId ) ) - array_sum( $CouponAmnt ) ;
		$yith_gift_value = get_yith_gift_card_value() ;
		$redeemedpoints  = 0 !=  $yith_gift_value ? $redeemedpoints - $yith_gift_value : $redeemedpoints ;
		return $redeemedpoints ;
	}

	add_filter( 'woocommerce_available_payment_gateways' , 'filter_gateway' , 10 , 1 ) ;

	function filter_gateway( $gateways ) {
		global $woocommerce ;

		if ( ! is_object( $woocommerce->cart ) || ! srp_check_is_array( $woocommerce->cart->cart_contents ) ) {
			unset( $gateways[ 'reward_gateway' ] ) ;
			return $gateways ;
		}

		$PointsData = new RS_Points_Data( get_current_user_id() ) ;
		if ( empty( $PointsData->total_available_points() ) ) {
			foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
				if ( 'reward_gateway' != $gateway->id ) {
					continue ;
				}

				unset( $gateways[ $gateway->id ] ) ;
			}
		}

		foreach ( $woocommerce->cart->cart_contents as $key => $values ) {
			$gateways = show_reward_gateway( $values[ 'product_id' ] , $gateways ) ;
		}
		return 'NULL' != $gateways ? $gateways : array() ;
	}

	function show_reward_gateway( $ProductId, $gateways ) {
		if ('1'== get_option( 'rs_show_hide_reward_points_gateway' )) {
			$show_reward_gateway = validate_product_category_filter( $ProductId ) ;
			if ( $show_reward_gateway ) {
				if ('2' == check_display_price_type($ProductId)) {
					foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
						if ( 'reward_gateway' != $gateway->id ) {
							unset( $gateways[ $gateway->id ] ) ;
						}
					}
				}

				// For Normal Product Validation.
				$show_reward_gateway = validate_product_category_filter( $ProductId , false ) ;
				if ( $show_reward_gateway && ! check_display_price_type( $ProductId ) ) {
					foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
						if ( 'reward_gateway' != $gateway->id ) {
							unset( $gateways[ $gateway->id ] ) ;
						}
					}
				} 
			} else {
				if ( 'no' == get_option( 'rs_enable_gateway_visible_to_all_product' ) ) {
					foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
						if ( 'reward_gateway' == $gateway->id ) {
							unset( $gateways[ $gateway->id ] ) ;
						}
					}
				}
			}
		} else {
			if ( ( 'yes' == get_option( 'rs_enable_selected_product_for_hide_gateway' ) ) && '' != get_option( 'rs_select_product_for_hide_gateway' ) ) {
				$IDs = srp_check_is_array( get_option( 'rs_select_product_for_hide_gateway' ) ) ? get_option( 'rs_select_product_for_hide_gateway' ) : explode( ',' , get_option( 'rs_select_product_for_hide_gateway' ) ) ;
				if ( in_array( $ProductId , $IDs ) ) {
					foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
						if ('reward_gateway' ==  $gateway->id ) {
							unset( $gateways[ $gateway->id ] ) ;
						}
					}
				}
			}
			if ( ( 'yes'  == get_option( 'rs_enable_selected_category_to_hide_gateway' ) ) ) {
				$IncCat       = get_option( 'rs_select_category_to_hide_gateway' ) ;
				$CategoryList = get_the_terms( $ProductId , 'product_cat' ) ;

				if ( srp_check_is_array( $CategoryList ) ) {
					foreach ( $CategoryList as $Category ) {
						$termid = $Category->term_id ;
						if ( in_array( $Category->term_id , $IncCat ) ) {
							foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {
								if ( 'reward_gateway'  == $gateway->id) {
									unset( $gateways[ $gateway->id ] ) ;
								}
							}
						}
					}
				}
			}
		}
		return $gateways ;
	}

	function validate_product_category_filter( $ProductId, $condition = true ) {

		if ( ( 'yes' == get_option( 'rs_enable_selected_product_for_purchase_using_points' ) ) && '' != get_option( 'rs_select_product_for_purchase_using_points' ) ) {
			$IDs       = srp_check_is_array( get_option( 'rs_select_product_for_purchase_using_points' ) ) ? get_option( 'rs_select_product_for_purchase_using_points' ) : explode( ',' , get_option( 'rs_select_product_for_purchase_using_points' ) ) ;
			$condition = false ;
			if ( in_array( $ProductId , $IDs ) ) {
				return true ;
			}
		}

		if ( ( 'yes' == get_option( 'rs_enable_selected_category_for_purchase_using_points' ) && '' != get_option( 'rs_select_category_for_purchase_using_points' ) ) ) {
			$IncCat       = get_option( 'rs_select_category_for_purchase_using_points' ) ;
			$CategoryList = get_the_terms( $ProductId , 'product_cat' ) ;
			$condition    = false ;
			if ( srp_check_is_array( $CategoryList ) ) {
				foreach ( $CategoryList as $Category ) {
					$termid = $Category->term_id ;
					if ( in_array( $Category->term_id , $IncCat ) ) {
						return true ;
					}
				}
			}
		}

		return $condition ;
	}

	function add_your_gateway_class( $methods ) {
		if ( is_user_logged_in() ) {
			$banning_type = check_banning_type( get_current_user_id() ) ;
			if ( 'redeemingonly' != $banning_type && 'both' != $banning_type ) {
				$methods[]    = 'WC_Reward_Gateway' ;
			}
		}
		return $methods ;
	}

	add_filter( 'woocommerce_payment_gateways' , 'add_your_gateway_class' ) ;

	function rs_force_auto_r_manual_adaptive_payment( $description, $gateway_id ) {
		$reward_points_gateway = new WC_Reward_Gateway() ;

		if ( 'no' == $reward_points_gateway->get_option( 'rs_subscription_based_payment_option' ) && 'reward_gateway' == $gateway_id ) {
			if ( is_checkout_pay_page() && isset( $_GET[ 'key' ] ) ) {
				$order_id = wc_get_order_id_by_order_key( wc_clean(wp_unslash($_GET[ 'key' ])) ) ;

				if ( function_exists( 'sumo_is_order_contains_subscriptions' ) && sumo_is_order_contains_subscriptions( $order_id ) ) {
					return $description . rs_display_adaptive_payment_selection_checkbox() ;
				}
			} else if ( function_exists( 'sumo_is_cart_contains_subscription_items' ) && function_exists( 'sumo_is_order_subscription' ) &&
					is_checkout() && ( sumo_is_cart_contains_subscription_items() || sumo_is_order_subscription() ) ) {
				return $description . rs_display_adaptive_payment_selection_checkbox() ;
			}
		}
		return $description ;
	}

	function rs_display_adaptive_payment_selection_checkbox() {
		ob_start() ;
		?>
		<div class = rs_reward_points_payment_selection >
			<br><br>
			<input type="checkbox" id = "rs_reward_points_payment_selection" name = "rs_reward_points_payment_selection" value = "1" /><?php echo esc_html__( 'Enable Automatic Preapproval Payments' , 'woocommerce' ); ?>
		</div>
		<?php
		return ob_get_clean() ;
	}

	function is_sumosubscriptions_active() {
		if ( is_multisite() && ! is_plugin_active_for_network( 'sumosubscriptions/sumosubscriptions.php' ) && ! is_plugin_active( 'sumosubscriptions/sumosubscriptions.php' ) ) {
			return false ;
		} else if ( ! is_plugin_active( 'sumosubscriptions/sumosubscriptions.php' ) ) {
			return false ;
		}
		return true ;
	}

	function sumosubscription_is_preapproval_status_valid( $PostId, $ParentOrderId ) {
		if ( ! is_sumosubscriptions_active() ) {
			return false ;
		}

		$PaymentMethod = function_exists( 'sumo_get_subscription_payment_method' ) ? sumo_get_subscription_payment_method( $PostId ) : get_post_meta( $ParentOrderId , 'sumo_order_payment_method' , true ) ;

		$RenewalOrderId = get_post_meta( $PostId , 'sumo_get_renewal_id' , true ) ;

		if ( 'reward_gateway' != $PaymentMethod ) {
			return false ;
		}

		$Order          = new WC_Order( $RenewalOrderId ) ;
		$PointsRedeemed = gateway_points( $RenewalOrderId ) ;
		$MaxCartTotal   = get_option( 'rs_max_redeem_discount_for_sumo_reward_points' ) ;
		$OrderObj       = srp_order_obj( $Order ) ;
		$PointsData     = new RS_Points_Data( $OrderObj[ 'order_userid' ] ) ;
		$Points         = $PointsData->total_available_points() ;

		if ( $PointsRedeemed > $Points ) {
			return false ;
		}

		if ( empty( $MaxCartTotal ) ) {
			return true ;
		}

		if ( $Order->get_total() > $MaxCartTotal ) {
			return true ;
		}

		return false ;
	}

	function sumosubscription_get_preapproval_status( $subscription_post_id, $parent_order_id ) {
		if ( sumosubscription_is_preapproval_status_valid( $subscription_post_id , $parent_order_id ) ) {
			$preapproval_status = 'valid' ;
			update_post_meta( $subscription_post_id , 'sumo_subscription_preapproval_status' , $preapproval_status ) ;
		}
	}

	function sumosubscription_preapproved_recurring_payment_transaction( $PostId, $ParentOrderId ) {
		if ( ! sumosubscription_is_preapproval_status_valid( $PostId , $ParentOrderId ) ) {
			return ;
		}

		$RenewalOrderId = get_post_meta( $PostId , 'sumo_get_renewal_id' , true ) ;
		$Order          = new WC_Order( $RenewalOrderId ) ;
		$RedeemedPoints = gateway_points( $RenewalOrderId ) ;
		$OrderObj       = srp_order_obj( $Order ) ;
		$UserId         = $OrderObj[ 'order_userid' ] ;

		$table_args = array(
			'user_id'     => $UserId ,
			'usedpoints'  => $RedeemedPoints ,
			'date'        => '999999999999' ,
			'checkpoints' => 'RPFGWS' ,
			'productid'   => $PostId
				) ;
		RSPointExpiry::perform_calculation_with_expiry( $RedeemedPoints , $UserId ) ;
		RSPointExpiry::record_the_points( $table_args ) ;
		update_post_meta( $PostId , 'sumo_subscription_preapproved_payment_transaction_status' , 'success' ) ;
	}

	function rs_payment_complete( $args ) {
		$renewal_order = wc_get_order( $args[ 'renewal_order_id' ] );
		if ( ! $renewal_order ) {
			return ;
		}

		if ( function_exists( 'sumo_get_subscription_payment_method' ) && 'reward_gateway' === sumo_get_subscription_payment_method( $args[ 'subscription_id' ] ) ) {
			//Update new Order status to Renew the Subscription.
			$renewal_order->update_status( 'completed' ) ;
		}
	}

	function sumosubscriptions_add_force_auto_renewals( $order ) {
			$contents = '.fp-srp-force-payment"{
                    display:none;
            }' ;
		wp_register_style( 'fp-srp-rewardgateway-style' , false , array() , SRP_VERSION ) ; // phpcs:ignore
		wp_enqueue_style( 'fp-srp-rewardgateway-style' ) ;
		wp_add_inline_style( 'fp-srp-rewardgateway-style' , $contents ) ; 
									
		echo wp_kses_post('<p class="fp-srp-force-payment">'
		. '<input type="checkbox" id="sumorewardsystem_subsc_payment_mode" name="sumorewardsystem_subsc_payment_mode"/>'
		. __( 'Force Automatic Payment' , 'rewardsystem' )
		. '</p>') ;

		wc_enqueue_js( '
            $( function ( $ ) {
                $( "#_payment_method" ).change( function () {
                    $( "#sumorewardsystem_subsc_payment_mode" ).closest( "p" ).hide() ;

                    if ( this.value === "reward_gateway" ) {
                        $( "#sumorewardsystem_subsc_payment_mode" ).closest( "p" ).show() ;
                    }
                } ) ;
            } ) ;'
		) ;
	}

	function sumosubscriptions_process_force_auto_renewals( $order_id, $order ) {

		if ( isset( $_REQUEST[ 'sumorewardsystem_subsc_payment_mode' ] ) && 'on' === wc_clean(wp_unslash($_REQUEST[ 'sumorewardsystem_subsc_payment_mode' ] ))) {
			$order = wc_get_order( $order_id ) ;

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION , '3.0' , '<' ) ) {
				$payment_method = $order->payment_method ;
			} else {
				$payment_method = $order->get_payment_method() ;
			}

			if ( function_exists( 'sumo_is_order_contains_subscriptions' ) && function_exists( 'sumo_save_subscription_payment_info' ) ) {
				//Check it is valid Subscription Order.
				if ( 'reward_gateway' === $payment_method && sumo_is_order_contains_subscriptions( $order_id ) ) {
					//Save default payment information.
					sumo_save_subscription_payment_info( $order_id , array(
						'payment_type'         => 'auto' ,
						'payment_method'       => $payment_method ,
						'payment_order_amount' => $order->get_total() ,
					) ) ;
				}
			}
		}
	}

	if ( is_sumosubscriptions_active() ) {
		add_filter( 'woocommerce_gateway_description' , 'rs_force_auto_r_manual_adaptive_payment' , 10 , 2 ) ;
		add_action( 'sumosubscriptions_process_preapproval_status' , 'sumosubscription_get_preapproval_status' , 10 , 2 ) ;
		add_action( 'sumosubscriptions_process_preapproved_payment_transaction' , 'sumosubscription_preapproved_recurring_payment_transaction' , 10 , 2 ) ;
		add_action( 'sumosubscriptions_preapproved_payment_transaction_success' , 'rs_payment_complete' , 999 , 1 ) ;
		add_action( 'woocommerce_admin_order_data_after_billing_address' , 'sumosubscriptions_add_force_auto_renewals' ) ;
		add_action( 'woocommerce_process_shop_order_meta' , 'sumosubscriptions_process_force_auto_renewals' , 999999 , 2 ) ;
	}
}
