<?php
/*
 * Simple Product Functionality
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFunctionforVariableProduct' ) ) {

	class RSFunctionforVariableProduct {

		public static function init() {

			if ( '1' == get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_before_single_product' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_before_single_product' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_before_single_product' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
			} elseif ( '2' == get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_before_single_product_summary' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_before_single_product_summary' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_before_single_product_summary' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
			} elseif ( '3' == get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_single_product_summary' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_single_product_summary' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_single_product_summary' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
			} elseif ( '4' == get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_after_single_product' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_after_single_product' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_after_single_product' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
			} elseif ( '5' == get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_after_single_product_summary' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_after_single_product_summary' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_after_single_product_summary' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
			} elseif ( '6' == get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_product_meta_end' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_product_meta_end' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_product_meta_end' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
			} elseif ( '7' == get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_before_add_to_cart_quantity' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_before_add_to_cart_quantity' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_before_add_to_cart_quantity' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
			} elseif ( '8' == get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_after_add_to_cart_quantity' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_after_add_to_cart_quantity' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_after_add_to_cart_quantity' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
			} else {

				add_action( 'woocommerce_after_add_to_cart_form' , array( __CLASS__ , 'display_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_after_add_to_cart_form' , array( __CLASS__ , 'display_referral_msg_for_variable_product' ) ) ;
				add_action( 'woocommerce_after_add_to_cart_form' , array( __CLASS__ , 'display_buying_points_msg_for_variable_product' ) ) ;
			}

			/* Filter for to alter variation range for only point price products */
			add_filter( 'woocommerce_get_price_html' , array( __CLASS__ , 'display_variation_price' ) , 10 , 2 ) ;

			add_filter( 'woocommerce_get_price_html' , array( __CLASS__ , 'display_buying_point_msg_for_variation' ) , 9999 , 2 ) ;

			add_filter( 'woocommerce_ajax_variation_threshold' , array( __CLASS__ , 'set_variation_limit' ) , 999 , 2 ) ;
		}

		public static function display_msg_for_variable_product() {
			if ( 'no' == get_option( 'rs_product_purchase_activated' ) ) {
				return ;
			}

			$banning_type = check_banning_type( get_current_user_id() ) ;
			if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
				return ;
			}

			if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && 1 != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
				return ;
			}

			global $post ;
			if ( 'yes' == get_option( 'rs_enable_display_earn_message_for_variation_single_product' ) ) {
				$variationid  = get_variation_id( $post->ID ) ;
				$earnmessages = '' ;
				$earnmessage  = '' ;
				$image        = '' ;

				if ( srp_check_is_array( $variationid ) ) {
					$varpointss = RSFunctionforSimpleProduct::rewardpoints_of_variation( $variationid[ 0 ] , $post->ID ) ;
					if ( '' != $varpointss ) {
						$CurrencyValue = redeem_point_conversion( $varpointss , get_current_user_id() , 'price' ) ;
						$CurrencyValue = number_format( ( float ) round_off_type_for_currency( $CurrencyValue ) , get_option( 'woocommerce_price_num_decimals' ) ) ;
						$message       = get_option( 'rs_message_for_variation_products' ) ;
						$earnmessage   = str_replace( '[variationrewardpoints]' , round_off_type( $varpointss ) , $message ) ;
						if ( 'right' == get_option( 'woocommerce_currency_pos' ) || 'right_space' == get_option( 'woocommerce_currency_pos' ) ) {
							$CurrencyValue = $CurrencyValue . get_woocommerce_currency_symbol() ;
						} elseif ( 'left' == get_option( 'woocommerce_currency_pos' ) || 'left_space' == get_option( 'woocommerce_currency_pos' ) ) {
							$CurrencyValue = get_woocommerce_currency_symbol() . $CurrencyValue ;
						}
						$earnmessage  = str_replace( '[variationpointsvalue]' , $CurrencyValue , $earnmessage ) ;
						$messages     = get_option( 'rs_message_for_single_product_variation' ) ;
						$earnmessages = str_replace( '[variationrewardpoints]' , round_off_type( $varpointss ) , $messages ) ;
						$earnmessages = str_replace( '[variationpointsvalue]' , $CurrencyValue , $earnmessages ) ;
						if ( '1' == get_option( '_rs_enable_disable_gift_icon' ) ) {
							if ( '' != get_option( 'rs_image_url_upload' ) ) {
								$image = "<img class = 'gifticon' src=" . get_option( 'rs_image_url_upload' ) . ' />&nbsp;' ;
							}
						}
						$earnmessages = $image . $earnmessages ;
					}
				}
			}
			?>
			<div id='value_variable_product'></div>
			<?php if ( 'yes' == get_option( 'rs_enable_display_earn_message_for_variation_single_product' ) ) { ?>
				<div id='value_variable_product1'></div>
				<?php
				if ( 'yes' == get_option( 'rs_enable_display_earn_message_for_variation_single_product' ) ) {
					if ( ( '' != $earnmessages || '' != $earnmessage ) ) {
											$localised_script_args = array(
												'purchase_message'   => $earnmessage,
												'earn_message'       => $earnmessages,
												'show_or_hide_purchase_message' => get_option( 'rs_show_hide_message_for_variable_product' ),
												'show_or_hide_earn_message' => get_option( 'rs_show_hide_message_for_variable_in_single_product_page' ),
											);
											
											self::enqueue_scripts_for_variable_product($localised_script_args);
					}
				}
			}

			//Span Tag Added for Variations as a Troubleshoot Option in Version 24.3.6 ['The Issue' - Theme Compatibility].
			if ( '2' == get_option( 'rs_earn_message_display_hook' ) ) {
				?>
				 <span class = "rs_variable_earn_messages"></span> 
				<?php
			}
			
			// Minimum Quantity restriction error for variations. 
			self::add_minimum_quantity_error_html_for_variations();
			
		 
		}
		
		public static function add_minimum_quantity_error_html_for_variations() {
			
			global $post;
			if (!is_object($post)) {
				return;
			}
			
			$post_id = $post->ID;
			$variation  = wc_get_product($post_id) ;
			if (!is_object($variation)) {
				return;
			}
				
			$variation_ids = $variation->get_children();
			if (!srp_check_is_array($variation_ids)) {
				return;
			}
			
			$min_quantity  = rs_get_minimum_quantity_based_on_product_total($post_id, $variation_ids[0]);
			if ( $min_quantity<=1) {
				return;
			}
				
			?>
			<div class="rs-minimum-quantity-error-variable"></div>
			<?php
		}

		public static function display_referral_msg_for_variable_product() {
			if ( 'no' == get_option( 'rs_referral_activated' ) ) {
				return ;
			}

			$banning_type = check_banning_type( get_current_user_id() ) ;
			if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
				return ;
			}

			if ( ! isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
				return ;
			}

			if ( 1 != get_option( 'rs_show_hide_message_for_single_product_guest_referral' )) {
				return ;
			}

			if ( ! is_user_logged_in() && 'yes' != get_option( 'rs_referrer_earn_point_purchase_by_guest_users' ) ) {
				return ;
			}
			?>
			<div id='referral_value_variable_product'></div>
			<?php
		}

		public static function display_buying_points_msg_for_variable_product() {
			if ( 'yes' != get_option( 'rs_buyingpoints_activated' )  ) {
				return ;
			}

			$banning_type = check_banning_type( get_current_user_id() ) ;
			if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
				return ;
			}

			global $post ;
			if ( 'yes' == get_option( 'rs_enable_display_earn_message_for_variation_single_product' ) ) {
				$variationid = get_variation_id( $post->ID ) ;
				if ( srp_check_is_array( $variationid ) ) {
					if ( 1 == get_post_meta( $variationid[ 0 ] , '_rewardsystem_buying_reward_points' , true ) ) {
						$BuyPoints = get_post_meta( $variationid[ 0 ] , '_rewardsystem_assign_buying_points' , true ) ;
						$BuyPoints = empty( get_current_user_id() ) ? $BuyPoints : RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $BuyPoints ) ;

						$CurrencyValueForBuyingPoints = redeem_point_conversion( $BuyPoints , get_current_user_id() , 'price' ) ;
						$CurrencyValueForBuyingPoints = number_format( ( float ) round_off_type_for_currency( $CurrencyValueForBuyingPoints ) , get_option( 'woocommerce_price_num_decimals' ) ) ;
						if ( 'right' == get_option( 'woocommerce_currency_pos' ) || 'right_space' == get_option( 'woocommerce_currency_pos' ) ) {
							$CurrencyValueForBuyingPoints = $CurrencyValueForBuyingPoints . get_woocommerce_currency_symbol() ;
						} elseif ( 'left' == get_option( 'woocommerce_currency_pos' ) || 'left_space' == get_option( 'woocommerce_currency_pos' ) ) {
							$CurrencyValueForBuyingPoints = get_woocommerce_currency_symbol() . $CurrencyValueForBuyingPoints ;
						}
						$BuyMsg    = ( ! empty( $BuyPoints ) ) ? str_replace( array( '[buypoints]' , '[buypointvalue]' ) , array( $BuyPoints , $CurrencyValueForBuyingPoints ) , get_option( 'rs_buy_point_message_in_product_page_for_variable' ) ) : '' ;
						$BuyingMsg = str_replace( '[variationbuyingpoint]' , round_off_type( $BuyPoints ) , get_option( 'rs_buy_point_message_for_variation_products' ) ) ;
						$BuyingMsg = str_replace( '[variationbuyingpointvalue]' , $CurrencyValueForBuyingPoints , $BuyingMsg ) ;
						if ( '' != get_option( 'rs_buy_point_message_for_variation_products' ) && $BuyPoints ) {
						
												$localised_script_args = array(
														'product_purchase_activated'           => get_option( 'rs_product_purchase_activated' ),
														'buying_points_purchase_message'       => $BuyingMsg,
														'buying_points_earn_message'           => $BuyMsg,
														'show_or_hide_buying_purchase_message' => get_option( 'rs_show_hide_buy_point_message_for_variable_product' ),
														'show_or_hide_buying_earn_message'     => get_option( 'rs_show_hide_buy_points_message_for_variable_in_product' ),
												);
											
												self::enqueue_scripts_for_variable_product($localised_script_args);
						}
					}
				}
			}
			?>
			<div id='buy_Point_value_variable_product'></div>
			<?php
		}

		public static function display_variation_price( $price, $ProductObj ) {
			if ( is_product() || is_shop() || is_page() || is_product_category() || is_tax( 'pwb-brand' ) ) {

				if ( 'yes' != get_option( 'rs_point_price_activated' ) ) {
					return $price ;
				}

				if ( 2 == get_option( 'rs_enable_disable_point_priceing' ) ) {
					return $price ;
				}

				if ( '2' == get_option( 'rs_point_price_visibility' ) && !is_user_logged_in() ) {
					return $price ;
				}

				$id = product_id_from_obj( $ProductObj ) ;

				$display_only_points = true ;

				$gettheproducts = srp_product_object( $id ) ;
				if ( is_object( $gettheproducts ) && check_if_variable_product( $gettheproducts ) ) {
					$variation_ids = get_variation_id( $id ) ;
					if ( ! srp_check_is_array( $variation_ids ) ) {
						return $price ;
					}

					foreach ( $variation_ids as $eachvariation ) {
						if ( '2' != check_display_price_type( $eachvariation )) {
							$display_only_points = false ;
							continue ;
						}

						$enable = calculate_point_price_for_products( $eachvariation ) ;
						if ( empty( $enable[ $eachvariation ] ) ) {
							$display_only_points = false ;
							continue ;
						}
					}
				}

				if ( $display_only_points && 'variable' == $ProductObj->get_type()) {
					$price = '' ;
				}

				return $price ;
			}
			return $price ;
		}

		public static function set_variation_limit( $variation_limit, $product ) {
			return 1000 ;
		}

		public static function display_buying_point_msg_for_variation( $price, $ProductObj ) {
			if ( 'yes' != get_option( 'rs_buyingpoints_activated' ) ) {
				return $price ;
			}

			global $post ;
			if ( ! is_object( $post ) ) {
				return $price ;
			}

			$banning_type = check_banning_type( get_current_user_id() ) ;
			if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
				return $price ;
			}

			if ( ! self::is_in_stock( $ProductObj ) ) {
				return $price ;
			}

			if ( ! check_if_variable_product( $ProductObj ) ) {
				return $price ;
			}

			$id            = product_id_from_obj( $ProductObj ) ;
			$variation_ids = get_variation_id( $id ) ;
			if ( ! srp_check_is_array( $variation_ids ) ) {
				return $price ;
			}

			$BuyingPoints = array() ;
			foreach ( $variation_ids as $eachvariation ) {
															
				if ( 'no' == get_post_meta( $eachvariation , '_rewardsystem_buying_reward_points' , true ) || '' == get_post_meta( $eachvariation , '_rewardsystem_buying_reward_points' , true ) ) {
					continue ;
				}

				if ( '' == get_post_meta( $eachvariation , '_rewardsystem_assign_buying_points' , true ) ) {
					continue ;
				}

				$BuyingPoints[] = get_post_meta( $eachvariation , '_rewardsystem_assign_buying_points' , true ) ;
			}

			if ( ! srp_check_is_array( $BuyingPoints ) ) {
				return $price ;
			}
						
			global $woocommerce_loop ;
			$related_product     = false ;
			$bool                = self::is_in_stock( $ProductObj ) ;
			if ( ( float ) WC()->version >= ( float ) '3.3.0' ) {
				if ( isset( $woocommerce_loop[ 'name' ] ) ) {
					if ( ( null != $woocommerce_loop[ 'name' ] ) && ( 'related' == $woocommerce_loop[ 'name' ] || 'up-sells' == $woocommerce_loop[ 'name' ] ) ) {
						$related_product = true ;
					}
				}
			}

			if ( is_shop() || is_product_category() ) {
				if ( is_user_logged_in() ) {
					if ( '1' == get_option( 'rs_show_hide_buy_points_message_for_variable_in_shop' )) {
						return $price . '<br>' . do_shortcode( get_option( 'rs_buy_point_message_in_shop_page_for_variable' ) ) ;
					}
				} else {
					if ( '1' == get_option( 'rs_show_hide_buy_pont_message_for_variable_in_shop_guest' ) ) {
						return $price . '<br>' . do_shortcode( get_option( 'rs_buy_point_message_in_shop_page_for_variable' ) ) ;
					}
				}
			}
						
			if (is_product()) {
				if ( $related_product ) {
					if ( '1' == get_option( 'rs_show_hide_message_related_product_buying_point_variable_product', 1 )) {
						return $price . '<br>' . do_shortcode( get_option( 'rs_message_related_product_buying_point_variable_product', 'Earn [buypoints] Reward Points' ) ) ;
					}
				} 
			}
			return $price ;
		}

		public static function is_in_stock( $ProductObj ) {
			$bool = $ProductObj->is_in_stock() ;
			if ( is_shop() ) {
				if ( ! ( $ProductObj->is_in_stock() ) && '1' == get_option( 'rs_show_or_hide_message_for_outofstock' ) ) {
					$bool = true ;
				}
			}
			if ( is_product() ) {
				if ( ! ( $ProductObj->is_in_stock() ) && '1' == get_option( 'rs_message_outofstockproducts_product_page' ) ) {
					$bool = true ;
				}
			}
			return $bool ;
		}
				
		public static function enqueue_scripts_for_variable_product( $localized_script_args) {
					
			$load_script = ( 'wp_footer' == get_option( 'rs_load_script_styles' ) ) ? true : false ;
			wp_enqueue_script( 'rs-variable-product' , SRP_PLUGIN_DIR_URL . 'includes/frontend/js/variable-product.js' , array( 'jquery' ) , SRP_VERSION , $load_script ) ;
			wp_localize_script( 'rs-variable-product' , 'variable_product_params' , $localized_script_args);
		}

	}

	RSFunctionforVariableProduct::init() ;
}
