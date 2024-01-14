<?php
/**
 * Simple Product Messages.
 *
 * @package Rewardsystem/Frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RS_Simple_Product_Messages' ) ) {

	/**
	 * Class RS_Simple_Product_Messages
	 */
	class RS_Simple_Product_Messages {

		/**
		 * Init Hooks.
		 */
		public static function init() {
			add_action( 'woocommerce_before_shop_loop', array( __CLASS__, 'rs_msg_in_shop_page' ) );
			add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'display_reward_point_msg_for_product' ), 100, 2 );
			add_filter( 'woocommerce_get_price_html', array( __CLASS__, 'display_buying_point_msg_for_simple_product' ), 9999, 2 );

			if ( '9' === get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_after_add_to_cart_form', array( __CLASS__, 'display_purchase_message_for_simple_in_single_product_page' ) );
			} elseif ( '2' === get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_before_single_product_summary', array( __CLASS__, 'display_purchase_message_for_simple_in_single_product_page' ) );
			} elseif ( '3' === get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'display_purchase_message_for_simple_in_single_product_page' ) );
			} elseif ( '4' === get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_after_single_product', array( __CLASS__, 'display_purchase_message_for_simple_in_single_product_page' ) );
			} elseif ( '5' === get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'display_purchase_message_for_simple_in_single_product_page' ) );
			} elseif ( '6' === get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_product_meta_end', array( __CLASS__, 'display_purchase_message_for_simple_in_single_product_page' ) );
			} elseif ( '7' === get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_before_add_to_cart_quantity', array( __CLASS__, 'display_purchase_message_for_simple_in_single_product_page' ) );
			} elseif ( '8' === get_option( 'rs_msg_position_in_product_page' ) ) {

				add_action( 'woocommerce_after_add_to_cart_quantity', array( __CLASS__, 'display_purchase_message_for_simple_in_single_product_page' ) );
			} else {

				add_action( 'woocommerce_before_single_product', array( __CLASS__, 'display_purchase_message_for_simple_in_single_product_page' ) );
			}

			add_filter( 'woocommerce_variation_sale_price_html', array( __CLASS__, 'display_point_price_in_variable_product' ), 99, 2 );
			add_filter( 'woocommerce_variation_price_html', array( __CLASS__, 'display_point_price_in_variable_product' ), 99, 2 );
		}

		public static function rs_msg_in_shop_page() {
			if ( 'yes' !== get_option( 'rs_product_purchase_activated' ) ) {
				return;
			}

			if ( 'yes' === get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
				return;
			}

			if ( '2' !== get_option( 'rs_award_points_for_cart_or_product_total', 1 ) ) {
				return;
			}

			if ( '2' === get_option( 'rs_enable_cart_total_reward_points' ) ) {
				return;
			}

			// Membership compatibility.
			if ( 'yes' === get_option( 'rs_enable_restrict_reward_points' ) && function_exists( 'check_plan_exists' ) && get_current_user_id() ) {
				$restrict_membership = check_plan_exists( get_current_user_id() ) ? 'yes' : 'no';
				if ( 'yes' !== $restrict_membership ) {
					return;
				}
			}

			if ( '1' === get_option( 'rs_reward_type_for_cart_total' ) ) {
				if ( '2' === get_option( 'rs_enable_msg_for_fixed_cart_total_based_product_purchase_in_shop' ) ) {
					return;
				}

				$shop_page_msg = get_option( 'rs_msg_for_fixed_cart_total_based_product_purchase_in_shop' );
				$fixed_points  = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) get_option( 'rs_reward_points_for_cart_total_in_fixed' ) );
				if ( empty( $fixed_points ) ) {
					return;
				}
			} else {
				if ( '2' === get_option( 'rs_enable_msg_for_percent_cart_total_based_product_purchase_in_shop' ) ) {
					return;
				}

				$shop_page_msg  = get_option( 'rs_msg_for_percent_cart_total_based_product_purchase_in_shop' );
				$percent_points = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) get_option( 'rs_reward_points_for_cart_total_in_percent' ) );
				if ( empty( $percent_points ) ) {
					return;
				}
			}
			?>
			<div class="woocommerce-info"><?php echo do_shortcode( $shop_page_msg ); ?></div>
			<?php
		}

		public static function display_buying_point_msg_for_simple_product( $price, $ProductObj ) {
			if ( 'yes' !== get_option( 'rs_buyingpoints_activated' ) ) {
				return $price;
			}

			$banning_type = check_banning_type( get_current_user_id() );
			if ( 'earningonly' === $banning_type || 'both' === $banning_type ) {
				return $price;
			}

			if ( ! self::is_in_stock( $ProductObj ) ) {
				return $price;
			}

			global $post;
			if ( ! is_object( $post ) ) {
				return $price;
			}

			if ( 'yes' !== get_post_meta( $post->ID, '_rewardsystem_buying_reward_points', true ) ) {
				return $price;
			}

			if ( '' === get_post_meta( $post->ID, '_rewardsystem_assign_buying_points', true ) ) {
				return $price;
			}

			global $woocommerce_loop;
			$related_product = false;
			$bool            = self::is_in_stock( $ProductObj );
			if ( (float) WC()->version >= (float) '3.3.0' ) {
				if ( isset( $woocommerce_loop['name'] ) ) {
					if ( ( null != $woocommerce_loop['name'] ) && ( 'related' === $woocommerce_loop['name'] || 'up-sells' === $woocommerce_loop['name'] ) ) {
						$related_product = true;
					}
				}
			}

			if ( is_object( $ProductObj ) && ( 'simple' === srp_product_type( $post->ID ) || ( 'subscription' === srp_product_type( $post->ID ) ) ) ) {
				$sumo_bookings_check = is_sumo_booking_active( $post->ID );
				if ( ! $sumo_bookings_check ) {
					if ( is_product() ) {
						if ( is_user_logged_in() && ! $related_product ) {
							if ( '1' === get_option( 'rs_show_hide_buy_points_message_for_simple_in_product' ) ) {
								$shop_msg = do_shortcode( get_option( 'rs_buy_point_message_in_product_page_for_simple' ) );
								return "$price<br><div class = rs_buypoints_message_simple >$shop_msg</div>";
							}
						} elseif ( ! $related_product ) {
							if ( '1' === get_option( 'rs_show_hide_buy_point_message_for_simple_in_product_guest' ) ) {
								$shop_msg = do_shortcode( get_option( 'rs_buy_point_message_in_product_page_for_simple' ) );
								return "$price<br><div class = rs_buypoints_message_simple >$shop_msg</div>";
							}
						}

						if ( $related_product ) {
							if ( '1' === get_option( 'rs_show_hide_message_related_product_buying_point', 1 ) ) {
								$shop_msg = do_shortcode( get_option( 'rs_message_related_product_buying_point', 'Earn [buypoints] Reward Points' ) );
								return "$price<br><div class = rs_buypoints_message_simple >$shop_msg</div>";
							}
						}
					}

					if ( is_shop() || is_product_category() ) {
						if ( is_user_logged_in() ) {
							if ( '1' === get_option( 'rs_show_hide_buy_points_message_for_simple_in_shop' ) ) {
								$shop_msg = do_shortcode( get_option( 'rs_buy_point_message_in_shop_page_for_simple' ) );
								return $price . '<br>' . $shop_msg;
							}
						} elseif ( '1' === get_option( 'rs_show_hide_buy_pont_message_for_simple_in_shop_guest' ) ) {
								$shop_msg = do_shortcode( get_option( 'rs_buy_point_message_in_shop_page_for_simple' ) );
								return $price . '<br>' . $shop_msg;
						}
					}
				}

				if ( is_page() || fp_check_is_taxonomy_page() ) {
					if ( is_user_logged_in() ) {
						if ( '1' === get_option( 'rs_show_hide_buy_points_message_for_simple_in_custom' ) ) {
							$shop_msg = do_shortcode( get_option( 'rs_buy_point_message_in_custom_shop_page_for_simple' ) );
							return $price . '<br>' . $shop_msg;
						}
					} elseif ( '1' === get_option( 'rs_show_hide_buy_point_message_for_simple_in_custom_shop_guest' ) ) {
							$shop_msg = do_shortcode( get_option( 'rs_buy_point_message_in_custom_shop_page_for_simple' ) );
							return $price . '<br>' . $shop_msg;
					}
				}
			}

			return $price;
		}

		public static function display_reward_point_msg_for_product( $price, $ProductObj ) {
			$banning_type = check_banning_type( get_current_user_id() );
			if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
				return $price;
			}

			global $post;
			global $woocommerce;
			global $woocommerce_loop;
			$related_product     = false;
			$point_price_display = '';
			$id                  = product_id_from_obj( $ProductObj );
			$pdt_type            = srp_product_type( $id );
			$bool                = self::is_in_stock( $ProductObj );
			if ( (float) $woocommerce->version >= (float) '3.3.0' ) {
				if ( isset( $woocommerce_loop['name'] ) ) {
					if ( ( null != $woocommerce_loop['name'] ) && ( 'related' == $woocommerce_loop['name'] || 'up-sells' == $woocommerce_loop['name'] ) ) {
						$related_product = true;
					}
				}
			}

			$args = array(
				'price'               => $price,
				'id'                  => $id,
				'bool'                => $bool,
				'ProductObj'          => $ProductObj,
				'pdt_type'            => $pdt_type,
				'related_product'     => $related_product,
				'point_price_display' => $point_price_display,
			);

			if ( is_object( $ProductObj ) && check_if_variable_product( $ProductObj ) ) {
				return self::get_variable_product_earn_messages( $args );
			} else {
				return self::get_simple_product_earn_messages( $args );
			}

			return $price;
		}

		public static function get_variable_product_earn_messages( $args ) {
			extract( $args );
			$variation_ids = get_variation_id( $id );
			if ( srp_check_is_array( $variation_ids ) && 'yes' == block_points_for_salepriced_product( $variation_ids[0], 0 ) ) {
				return $price;
			}

			$varpointss = srp_check_is_array( $variation_ids ) ? self::rewardpoints_of_variation( $variation_ids[0], $id ) : '';
			$pointmin   = self::get_point_price( $id );
			if ( ! empty( $pointmin ) && true == $bool ) {
				$displaymin = min( $pointmin );
				$displaymax = max( $pointmin );
				$separator  = get_option( 'rs_separator_for_point_price' );
				if ( '2' == get_option( 'rs_point_price_visibility' ) && ! is_user_logged_in() ) {
					$point_price_display = '';
				} elseif ( $displaymin == $displaymax ) {
						$PointPrice          = display_point_price_value( $displaymin );
						$point_price_display = $PointPrice;
				} else {
					$MinPointPrice       = display_point_price_value( $displaymin );
					$MaxPointPrice       = display_point_price_value( $displaymax );
					$MaxPointPrice       = str_replace( $separator, '', $MaxPointPrice );
					$point_price_display = $MinPointPrice . ' - ' . $MaxPointPrice;
				}

				if ( '2' === get_option( 'rs_pricing_type_global_level' ) ) {
					$point_price_display = str_replace( $separator, '', $point_price_display );
				}
			}

			if ( is_product() ) {
				$message_position = get_option('rs_message_position_in_single_product_page_for_variable_products');
				// Single Product and Custom Page Message for Variable Product
				if ( true == $related_product && '1' == get_option( 'rs_show_hide_message_for_shop_archive_variable_related_products' ) && true == $bool ) {
					global $post;
					$variation_ids = get_variation_id( $post->ID );
					$varpointss    = srp_check_is_array( $variation_ids ) ? self::rewardpoints_of_variation( $variation_ids[0], $post->ID ) : '';
					if ( '' != $varpointss ) {
						$earnmessage = str_replace( '[variationrewardpoints]', round_off_type( $varpointss ), get_option( 'rs_message_in_variable_related_products' ) );
						return self::rs_function_to_get_msg_with_gift_icon_for_variable( $earnmessage, $price, $point_price_display, $message_position, true );
					}

					// To display point price in the product.
					if ( ! empty( $pointmin ) ) {
						return self::rs_function_to_get_msg_with_gift_icon_for_variable( '', $price, $point_price_display, $message_position );
					}
				}
				if ( false == $related_product && true == $bool ) {
					if ( '1' == get_option( 'rs_show_hide_message_for_variable_in_single_product_page' ) ) {
						// Shop Page Message for Variable Product with Gift Icon
						if ( 'yes' == get_option( 'rs_enable_display_earn_message_for_variation_single_product' ) ) {
							if ( '' != $varpointss ) {
								if ( 'variable' == $pdt_type ) {
									return self::rs_function_to_get_msg_with_gift_icon_for_variable( '', $price, $point_price_display, $message_position );
								}
							}
						} elseif ( 'variable' == $pdt_type ) {
								return self::rs_function_to_get_msg_with_gift_icon_for_variable( '', $price, $point_price_display, $message_position );
						}
					}
					if ( 'variable' == $pdt_type ) {
						if ( '1' == get_option( 'rs_enable_disable_point_priceing' ) && 'yes' == get_option( 'rs_point_price_activated' ) ) {
							return self::rs_function_to_get_msg_with_gift_icon_for_variable( '', $price, $point_price_display, $message_position );
						}
					}
					if ( 'variation' == $pdt_type ) {
						if ( '1' == get_option( 'rs_enable_disable_point_priceing' ) ) {
							return self::display_point_price_in_variable_product( $price, $ProductObj );
						}
					}
				}
			}

			if ( is_page() || fp_check_is_taxonomy_page() ) {    
				$message_position = get_option('rs_message_position_in_single_product_page_for_variable_products');
				if ( 'yes' == get_option( 'rs_enable_display_earn_message_for_variation_custom_shop' ) ) {
					if ( '' != $varpointss ) {
						$earnmessage = ( 'yes' == get_option( 'rs_product_purchase_activated' ) ) ? str_replace( '[variationrewardpoints]', round_off_type( $varpointss ), get_option( 'rs_message_for_custom_shop_variation' ) ) : '';
						// Shop Page Message for Variable Product with Gift Icon
						return self::rs_function_to_get_msg_with_gift_icon_for_variable( $earnmessage, $price, $point_price_display, $message_position );
					}

					// To display point price in the product.
					if ( ! empty( $pointmin ) ) {
						return self::rs_function_to_get_msg_with_gift_icon_for_variable( '', $price, $point_price_display, $message_position );
					}
				} elseif ( ! empty( $pointmin ) ) {// To display point price in the custom pages.
						return self::rs_function_to_get_msg_with_gift_icon_for_variable( '', $price, $point_price_display, $message_position );
				}
			}

			if ( is_shop() || is_product_category() || is_product_tag() || fp_check_is_taxonomy_page() ) {                                                                        // Shop and Category Page Message for Variable Product
				$message_position = get_option('rs_msg_position_for_var_products_in_shop_page');
				if ( 'yes' == get_option( 'rs_enable_display_earn_message_for_variation' ) && true == $bool ) {
					$earnmessage    = ( 'yes' == get_option( 'rs_product_purchase_activated' ) ) ? str_replace( '[variationrewardpoints]', round_off_type( $varpointss ), get_option( 'rs_message_in_shop_page_for_variable', 'Earn [variationrewardpoints] Reward Points' ) ) : '';
					$VarPointsValue = redeem_point_conversion( $varpointss, get_current_user_id(), 'price' );
					$earnmessage    = str_replace( '[variationpointsvalue]', wc_price( round_off_type_for_currency( $VarPointsValue ) ), $earnmessage );

					if ( is_user_logged_in() ) {
						if ( '' != $varpointss ) {
							// Shop Page Message for Variable Product with Gift Icon
							return self::rs_function_to_get_msg_with_gift_icon_for_variable( $earnmessage, $price, $point_price_display, $message_position );
						}
					} elseif ( '1' == get_option( 'rs_show_hide_message_for_variable_in_shop_guest', '1' ) ) {

							return self::rs_function_to_get_msg_with_gift_icon_for_variable( $earnmessage, $price, $point_price_display, $message_position );
					}
					// To display point price in the product.
					if ( ! empty( $pointmin ) ) {
						return self::rs_function_to_get_msg_with_gift_icon_for_variable( '', $price, $point_price_display, $message_position );
					}
				} else {
					return self::rs_function_to_get_msg_with_gift_icon_for_variable( '', $price, $point_price_display, $message_position );
				}
			}

			return $price;
		}

		public static function get_simple_product_earn_messages( $args ) {
			extract( $args );
			$getshortcodevalues  = points_for_simple_product( $id );
			$enabledpoints       = calculate_point_price_for_products( $id );
			$enabled_point_price = $enabledpoints[ $id ];
			$point_price         = empty( $enabled_point_price ) ? 0 : round_off_type( $enabled_point_price );
			$point_price_type    = ! empty( $point_price ) ? check_display_price_type( $id ) : '';
			if ( 0 != $point_price && true == $bool ) {
				$point_price_info = display_point_price_value( $point_price );
			} else {
				$point_price_info = $price;
			}
			if ( $getshortcodevalues > 0 ) {
				// Single Product and Custom Page Message for Simple Product.
				if ( is_product() ) {
					global $post;
					$sumo_bookings_check = is_sumo_booking_active( $post->ID );

					if ( true == $related_product && '1' == get_option( 'rs_show_hide_message_for_shop_archive_single_related_products' ) && true == $bool ) {
						$sumo_bookings_check = is_sumo_booking_active( $post->ID );
						if ( ! $sumo_bookings_check ) {
							$earnmessage           = do_shortcode( get_option( 'rs_message_in_single_product_page_related_products' ) );
							$earnpoint_msg_in_shop = self::rs_function_to_get_msg_with_gift_icon( $earnmessage );
							$msg_position          = get_option( 'rs_message_position_in_single_product_page_for_simple_products' );
							return self::rs_function_to_get_earnpoint_msg( $point_price_type, $point_price, $price, $point_price_info, $earnpoint_msg_in_shop, $msg_position );
						}
					}
					if ( false == $related_product && true == $bool ) {
						if ( ! $sumo_bookings_check ) {
							$message = '';
							if ( '1' == get_option( 'rs_show_hide_message_for_shop_archive_single' ) && is_user_logged_in() ) {
								$message = get_option( 'rs_message_in_single_product_page' );
							}

							if ( '1' == get_option( 'rs_show_or_hide_earn_message_single_product_guest', 1 ) && ! is_user_logged_in() ) {
								$message = get_option( 'rs_earn_message_single_product_guest', 'Earn [rewardpoints] Reward Points' );
							}

							if ( $message ) {
								$earnmessage = do_shortcode( $message );
								// Single Product and Custom Page Message for Simple Product with Gift Icon.
								$earnpoint_msg_in_shop = self::rs_function_to_get_msg_with_gift_icon( $earnmessage );
								$msg_position          = get_option( 'rs_message_position_in_single_product_page_for_simple_products' );
								// Function to Return Single Product and Custom Page Message.
								return self::rs_function_to_get_earnpoint_msg( $point_price_type, $point_price, $price, $point_price_info, $earnpoint_msg_in_shop, $msg_position );
							}
						}
					}
				}
				if ( is_shop() || is_product_category() || is_product_tag() || fp_check_is_taxonomy_page() ) {// Shop and Category Page Message for Simple Product
					global $post;
					$sumo_bookings_check = is_sumo_booking_active( $post->ID );
					if ( ! $sumo_bookings_check ) {
						$earnmessage = do_shortcode( get_option( 'rs_message_in_shop_page_for_simple' ) );
						// Shop Page Message for Simple Product with Gift Icon.
						$earnpoint_msg_in_shop = self::rs_function_to_get_msg_with_gift_icon( $earnmessage );
						$msg_position          = get_option( 'rs_message_position_for_simple_products_in_shop_page' );
						// Shop Page Message for Simple Product for User.
						if ( is_user_logged_in() ) {
							if ( '1' == get_option( 'rs_show_hide_message_for_simple_in_shop' ) && true == $bool ) {

								// Function to Return Shop Page Message.
								return self::rs_function_to_get_earnpoint_msg( $point_price_type, $point_price, $price, $point_price_info, $earnpoint_msg_in_shop, $msg_position );
							}
						} elseif ( '1' == get_option( 'rs_show_hide_message_for_simple_in_shop_guest' ) && true == $bool ) {// Shop Page Message for Simple Product for Guest
								// Function to Return Shop Page Message.
								return self::rs_function_to_get_earnpoint_msg( $point_price_type, $point_price, $price, $point_price_info, $earnpoint_msg_in_shop, $msg_position );
						}
					}
				}
				if ( ( is_page() || fp_check_is_taxonomy_page() ) && $bool ) {
					$earnmessage = do_shortcode( get_option( 'rs_message_in_custom_shop_page_for_simple' ) );

					// Shop Page Message for Simple Product with Gift Icon.
					$earnpoint_msg_in_shop = self::rs_function_to_get_msg_with_gift_icon( $earnmessage );
					$msg_position          = get_option( 'rs_message_position_for_simple_products_in_custom_shop_page' );
					// Shop Page Message for Simple Product for User.
					if ( is_user_logged_in() ) {
						if ( '1' == get_option( 'rs_show_hide_message_for_simple_in_custom_shop' ) ) {

							// Function to Return Shop Page Message.
							return self::rs_function_to_get_earnpoint_msg( $point_price_type, $point_price, $price, $point_price_info, $earnpoint_msg_in_shop, $msg_position );
						}
					} elseif ( '1' == get_option( 'rs_show_hide_message_for_simple_in_custom_shop_guest' ) ) {// Shop Page Message for Simple Product for Guest
							// Function to Return Shop Page Message.
							return self::rs_function_to_get_earnpoint_msg( $point_price_type, $point_price, $price, $point_price_info, $earnpoint_msg_in_shop, $msg_position );
					}
				}
			}

			if ( '1' == get_option( 'rs_enable_disable_point_priceing' ) && 'yes' == get_option( 'rs_point_price_activated' ) && true == $bool ) {
				$VisibilityForPointPrice = ( 1 == get_option( 'rs_point_price_visibility' ) ) ? true : is_user_logged_in();
				if ( ! $VisibilityForPointPrice ) {
					return $price;
				}

				global $post;
				if ( ! is_sumo_booking_active( $post->ID ) ) {
					if ( '2' == $point_price_type ) {
						$separator        = get_option( 'rs_separator_for_point_price' );
						$point_price_info = str_replace( $separator, '', $point_price_info );
						return $point_price_info;
					} elseif ( 0 != $point_price ) {
							return $price . '<span class="point_price_label">' . $point_price_info;
					}
				}
			}

			return $price;
		}

		public static function rs_function_to_get_earnpoint_msg( $point_price_type, $point_price, $price, $point_price_info, $earnpoint_msg_in_shop, $msg_position ) {
			$VisibilityForPointPrice = ( 1 == get_option( 'rs_point_price_visibility' ) ) ? true : is_user_logged_in();
			if ( '1' == get_option( 'rs_enable_disable_point_priceing' ) && 'yes' == get_option( 'rs_point_price_activated' ) && $VisibilityForPointPrice ) {                          // Shop Page Message for Simple Product when Points Price is Enabled
				if ( '2' == $point_price_type ) {
					$point_price_info = str_replace( '/', '', $point_price_info );
					$separator        = get_option( 'rs_separator_for_point_price' );
					$point_price_info = str_replace( $separator, '', $point_price_info );
					if ( '1' == $msg_position ) {    // Position of Shop Page Message for Simple Product - Before
						if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
							return '<small>' . $point_price_info . '</small><br>';
						} else {
							return '<small>' . $earnpoint_msg_in_shop . '</small> <br>' . $point_price_info;
						}
					} elseif ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {                                                                            // Position of Shop Page Message for Simple Product - After
							return '<small>' . $point_price_info . '</small><br>';
					} else {
						return '<small>' . $point_price_info . '<br>' . $earnpoint_msg_in_shop . '</small><br>';
					}
				} elseif ( 0 != $point_price ) {
					if ( '1' == $msg_position ) {    // Position of Shop Page Message for Simple Product - Before
						if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
							return $price . '<span class="point_price_label">' . $point_price_info . '</span>';
						} else {
							return $earnpoint_msg_in_shop . '<br>' . $price . '<span class="point_price_label">' . $point_price_info . '</span>';
						}
					} elseif ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {                                                                              // Position of Shop Page Message for Simple Product - After
							return $price . '<span class="point_price_label">' . $point_price_info . '</span>';
					} else {
						return $price . '<span class="point_price_label">' . $point_price_info . '</span><br><small>' . $earnpoint_msg_in_shop . '</small>';
					}
				} elseif ( '1' == $msg_position ) {
					// Position of Shop Page Message for Simple Product - Before
					if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
						return $price;
					} else {
						return $earnpoint_msg_in_shop . '<br>' . $price;
					}
				} elseif ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {                                                                              // Position of Shop Page Message for Simple Product - After
						return $price;
				} else {
					return $price . '<br>' . $earnpoint_msg_in_shop;
				}
			} else {                                                                              // Shop Page Message for Simple Product when Points Price is Disabled
				if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
					return $price;
				}

				if ( '1' == $msg_position ) {    // Position of Shop Page Message for Simple Product - Before
					return $earnpoint_msg_in_shop . '<br>' . $price;
				} else {                                                                              // Position of Shop Page Message for Simple Product - After
					return $price . '<br>' . $earnpoint_msg_in_shop;
				}
			}
			return $price;
		}

		public static function rs_function_to_get_msg_with_gift_icon( $earnmessage ) {
			if ( 'yes' == get_option( 'rs_product_purchase_activated' ) ) {
				if ( '1' == get_option( '_rs_enable_disable_gift_icon' ) ) {
					if ( '' != get_option( 'rs_image_url_upload' ) ) {
						$earnpoint_msg_in_shop = "<span class='simpleshopmessage'><img src=" . get_option( 'rs_image_url_upload' ) . '/>&nbsp; ' . $earnmessage . '</span>';
					} else {
						$earnpoint_msg_in_shop = "<span class='simpleshopmessage'>" . $earnmessage . '</span>';
					}
				} else {
					$earnpoint_msg_in_shop = "<span class='simpleshopmessage'>" . $earnmessage . '</span>';
				}
			} else {
				$earnpoint_msg_in_shop = '';
			}
			return $earnpoint_msg_in_shop;
		}

		public static function rs_function_to_get_msg_with_gift_icon_for_variable( $earnmessage, $price, $point_price_display, $message_position, $related_product = false ) {

			$image = "<img class='gift_icon' src=" . get_option( 'rs_image_url_upload' ) . ' />&nbsp;';
			if ( '' != $earnmessage ) {
				$break = '<br>';
			} else {
				$break = '';
				if ( is_shop() || is_product_category() ) {
					$image = '';
				}
			}

			$classname = ( $related_product ) ? 'variablerelatedmessage' : 'variableshopmessage';

			$VisibilityForPointPrice = ( 1 == get_option( 'rs_point_price_visibility' ) ) ? true : is_user_logged_in();
			if ( '1' == get_option( '_rs_enable_disable_gift_icon' ) ) {
				if ( '1' == get_option( 'rs_enable_disable_point_priceing' ) && 'yes' == get_option( 'rs_point_price_activated' ) && $VisibilityForPointPrice ) {
					if ( '' != get_option( 'rs_image_url_upload' ) ) {
						if ( '1' == $message_position ) {
							if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
								return $image . $break . $price . $point_price_display;
							} else {
								return $image . "<span class='$classname'>" . $earnmessage . '</span>' . $break . $price . $point_price_display;
							}
						} elseif ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
								return $price . $point_price_display . '<br>' . $image;
						} else {
							return $price . $point_price_display . '<br>' . $image . "<span class='$classname'>" . $earnmessage . '</span>';
						}
					} elseif ( '1' == $message_position ) {
						if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
							return $break . $price . $point_price_display;
						} else {
							return "<span class='$classname'>" . $earnmessage . '</span>' . $break . $price . $point_price_display;
						}
					} elseif ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
							return $price . $point_price_display . $break;
					} else {
						return $price . $point_price_display . $break . "<span class='$classname'>" . $earnmessage . '</span>';
					}
				} else {
					if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
						return $price;
					}

					if ( '' != get_option( 'rs_image_url_upload' ) ) {
						if ( '1' == $message_position ) {
							return $image . "<span class='$classname'>" . $earnmessage . '</span>' . $break . $price;
						} else {
							return $price . '<br>' . $image . "<span class='$classname'>" . $earnmessage . '</span>';
						}
					} elseif ( '1' == $message_position ) {
							return "<span class='$classname'>" . $earnmessage . '</span><br>' . $price;
					} else {
						return $price . "<br><span class='$classname'>" . $earnmessage . '</span>';
					}
				}
			} elseif ( is_product() ) {
				if ( '1' == get_option( 'rs_enable_disable_point_priceing' ) && 'yes' == get_option( 'rs_point_price_activated' ) && $VisibilityForPointPrice ) {
					if ( '1' == $message_position ) {
						if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
							return $break . $price . $point_price_display;
						} else {
							return "<span class='$classname'>" . $earnmessage . '</span>' . $break . $price . $point_price_display;
						}
					} elseif ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
							return $price . $point_price_display . $break;
					} else {
						return $price . $point_price_display . $break . "<span class='$classname'>" . $earnmessage . '</span>';
					}
				} else {
					if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
						return $price;
					}

					if ( '1' == $message_position ) {
						return "<span class='$classname'>" . $earnmessage . '</span>' . $break . $price;
					} else {
						return $price . $break . "<span class='$classname'>" . $earnmessage . '</span>';
					}
				}
			} elseif ( is_shop() || is_product_category() || is_page() || fp_check_is_taxonomy_page() ) {
				if ( '1' == get_option( 'rs_enable_disable_point_priceing' ) && 'yes' == get_option( 'rs_point_price_activated' ) && $VisibilityForPointPrice ) {
					if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
						return $price . $point_price_display . $break;
					} elseif ( '1' == $message_position ) {
						if ( $point_price_display ) {
							return $point_price_display . $break . "<span class='$classname'>" . $earnmessage . '</span>' . $break . $price;
						} else {
							return "<span class='$classname'>" . $earnmessage . '</span>' . $break . $price;
						}
					} elseif ( $point_price_display ) {
							return $price . $point_price_display . $break . "<span class='$classname'>" . $earnmessage . '</span>';
					} else {
						return $price . $break . "<span class='$classname'>" . $earnmessage . '</span>';
					}
				} else {
					if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
						return $price;
					}

					if ( '1' == $message_position ) {
						return "<span class='$classname'>" . $earnmessage . '</span>' . $break . $price;
					} else {
						return $price . $break . "<span class='$classname'>" . $earnmessage . '</span>';
					}
				}
			}
			return $price;
		}

		public static function get_point_price( $parent_id ) {

			if ( ! $parent_id ) {
				return;
			}

			if ( 'yes' !== get_option( 'rs_point_price_activated' ) ) {
				return;
			}

			if ( '2' === get_option( 'rs_point_price_visibility' ) && ! is_user_logged_in() ) {
				return;
			}

			$enabledpoints1 = array();
			$product        = wc_get_product( $parent_id );
			$variation_ids  = is_object( $product ) ? $product->get_children() : '';
			foreach ( $variation_ids as $key ) {
				$enabledpoints = calculate_point_price_for_products( $key );
				if ( '' != $enabledpoints[ $key ] ) {
					$enabledpoints1[ $key ] = $enabledpoints[ $key ];
				}
			}

			return $enabledpoints1;
		}

		public static function display_purchase_msg_in_single_product_page() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( ! class_exists( 'FPWaitList' ) ) {
				return;
			}

			if ( 'yes' != get_option( 'rs_reward_action_activated' ) ) {
				return;
			}

			if ( '2' == get_option( 'rs_show_hide_message_for_waitlist' ) ) {
				return;
			}

			$points = round_off_type( get_option( 'rs_reward_for_waitlist_subscribing' ) );
			if ( 0 == $points ) {
				return;
			}

			global $post;
			$checkproducttype = srp_product_object( $post->ID );

			if ( $checkproducttype->is_in_stock() ) {
				return;
			}

			if ( 'yes' != get_option( 'wl_show_form_member' ) ) {
				return;
			}

			$message = get_option( 'rs_message_for_subscribing_product' );
			$replace = str_replace( '[subscribingpoints]', RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $points ), $message );
			?>
			<div class="woocommerce-info rs_message_for_single_product"><?php echo do_shortcode( $replace ); ?></div>
			<?php
		}

		public static function display_cart_total_based_product_purchase_msg_in_single_product_page() {
			if ( 'yes' != get_option( 'rs_product_purchase_activated' ) ) {
				return;
			}

			if ( 'yes' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) ) {
				return;
			}

			if ( '2' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
				return;
			}

			if ( '2' == get_option( 'rs_enable_cart_total_reward_points' ) ) {
				return;
			}

			// Membership compatibility.
			if ( 'yes' == get_option( 'rs_enable_restrict_reward_points' ) && function_exists( 'check_plan_exists' ) && get_current_user_id() ) {
				$restrict_membership = check_plan_exists( get_current_user_id() ) ? 'yes' : 'no';
				if ( 'yes' != $restrict_membership ) {
					return;
				}
			}

			if ( '1' == get_option( 'rs_reward_type_for_cart_total' ) ) {
				$ShowMsg = is_user_logged_in() ? get_option( 'rs_enable_msg_for_fixed_cart_total_based_product_purchase' ) : get_option( 'rs_enable_msg_for_fixed_cart_total_based_product_purchase_guest' );
				if ( 2 == $ShowMsg ) {
					return;
				}

				$ProductPageMsg            = get_option( 'rs_msg_for_fixed_cart_total_based_product_purchase' );
				$FixedCartTotalBasedPoints = get_option( 'rs_reward_points_for_cart_total_in_fixed' );
				$FixedCartTotalBasedPoints = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $FixedCartTotalBasedPoints );
				if ( 0 == $FixedCartTotalBasedPoints ) {
					return;
				}
			} else {
				$ShowMsg = is_user_logged_in() ? get_option( 'rs_enable_msg_for_percent_cart_total_based_product_purchase' ) : get_option( 'rs_enable_msg_for_percent_cart_total_based_product_purchase_guest' );
				if ( 2 == $ShowMsg ) {
					return;
				}

				$ProductPageMsg              = get_option( 'rs_msg_for_percent_cart_total_based_product_purchase' );
				$PercentCartTotalBasedPoints = get_option( 'rs_reward_points_for_cart_total_in_percent' );
				$PercentCartTotalBasedPoints = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $PercentCartTotalBasedPoints );
				if ( 0 == $PercentCartTotalBasedPoints ) {
					return;
				}
			}
			?>
			<div class="woocommerce-info"><?php echo do_shortcode( $ProductPageMsg ); ?></div>
			<?php
		}

		public static function display_product_review_msg_in_single_product_page() {
			global $post;

			if ( 'yes' !== get_option( 'rs_reward_action_activated' ) ) {
				return;
			}

			if ( 'yes' !== get_option( 'rs_enable_product_review_points' ) ) {
				return;
			}

			if ( '2' === get_option( 'rs_show_hide_message_for_product_review' ) ) {
				return;
			}

			if ( '2' === get_option( 'rs_show_hide_message_for_product_review_for_guest_user' ) ) {
				if ( ! is_user_logged_in() ) {
					return;
				}
			}

			$ProductReviewMsg              = get_option( 'rs_message_for_product_review' );
			$ProductReviewPoints           = round_off_type( rs_get_product_review_reward_points( $post->ID ) );
			$ProductReviewPoints           = RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $ProductReviewPoints );
			$ProductReviewMsginProductPage = str_replace( '[productreviewpoint]', $ProductReviewPoints, $ProductReviewMsg );
			if ( 0 === $ProductReviewPoints ) {
				return;
			}

			/* Validate_product_review_restrictions */
			if ( ! self::validate_product_review_restrictions() ) {
				return;
			}
			?>
			<div class="woocommerce-info rs_message_for_single_product"><?php echo do_shortcode( $ProductReviewMsginProductPage ); ?></div>
			<?php
		}

		/**
		 * Validate product review restrictions
		 *
		 * @return bool.
		 * */
		public static function validate_product_review_restrictions() {

			global $product;
			if ( ! is_object( $product ) ) {
				return false;
			}

			$user_id = get_current_user_id();
			if ( empty( $user_id ) ) {
				return false;
			}

			// Validate One Product Per Review
			if ( ! self::validate_one_product_per_review() ) {
				return false;
			}

			if ( 'yes' !== get_option( 'rs_reward_for_comment_product_review', 'no' ) ) {
				return true;
			}

			$product_id = $product->get_id();
			$user_info  = get_user_by( 'id', $user_id );
			if ( ! is_object( $user_info ) ) {
				return false;
			}

			$email = $user_info->user_email;
			// Return if customer not purchased
			if ( ! RSPointExpiry::check_if_customer_purchased( $user_id, $email, $product_id, '' ) ) {
				return false;
			}

			// Return if limited days is reached
			if ( ! RSPointExpiry::validate_product_review_based_on_specific_days_limit( $user_id, $email, $product_id ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Validate One Product Per Review
		 *
		 * @return bool.
		 * */
		public static function validate_one_product_per_review() {
			global $product;

			if ( ! is_object( $product ) ) {
				return false;
			}

			if ( 'yes' != get_option( 'rs_restrict_reward_product_review' ) ) {
				return true;
			}

			if ( '1' == get_user_meta( get_current_user_id(), 'userreviewed' . $product->get_id(), true ) ) {
				return false;
			}
			return true;
		}

		public static function display_product_purchase_msg_in_single_product_page() {
			$userid       = get_current_user_id();
			$banning_type = check_banning_type( $userid );
			if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
				return;
			}

			global $post;
			if ( is_user_logged_in() ) {
				$sumo_bookings_check = is_sumo_booking_active( $post->ID );
				$checkproducttype    = srp_product_object( $post->ID );
				if ( referral_points_for_simple_product() > 0 ) {
					if ( '1' == get_option( 'rs_show_hide_message_for_single_product_referral' ) && 'yes' == get_option( 'rs_referral_activated' ) ) {
						if ( isset( $_COOKIE['rsreferredusername'] ) ) {
							$cookie_name = wc_clean( wp_unslash( $_COOKIE['rsreferredusername'] ) );
							$refuser     = ( 1 == get_option( 'rs_generate_referral_link_based_on_user' ) ) ? get_user_by( 'login', $cookie_name ) : get_user_by( 'id', $cookie_name );
							$myid        = $refuser->ID;
						} else {
							$myid = check_if_referrer_has_manual_link( get_current_user_id() );
						}
						$username = get_user_by( 'id', $myid )->user_login;
						?>
						<div class="woocommerce-info rs_message_for_single_product">
							<?php
							$strrplc = str_replace( '[rsreferredusername]', $username, get_option( 'rs_message_for_single_product_point_rule_referral' ) );
							echo do_shortcode( $strrplc );
							?>
						</div>
						<?php
					}
				}

				if ( 'yes' === get_option( 'rs_buyingpoints_activated' ) ) {
					$buy_points = buying_points_for_simple_product();
					if ( '1' === get_option( 'rs_show_hide_buy_point_message_for_single_product' ) && 'yes' === get_post_meta( $post->ID, '_rewardsystem_buying_reward_points', true ) && $buy_points > 0 ) {
						?>
						<div class="woocommerce-info rs_message_for_single_product"><?php echo do_shortcode( get_option( 'rs_buy_point_message_for_single_product_point_rule' ) ); ?></div>
						<?php
					}
				}

				if ( 'yes' !== get_option( 'rs_product_purchase_activated' ) ) {
					return;
				}

				if ( 'no' === get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' !== get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
					return;
				}

				self::display_minimum_quantity_error_message_simple_product( $post->ID );

				if ( '2' === get_option( 'rs_show_hide_message_for_single_product' ) ) {
					return;
				}

				if ( is_object( $checkproducttype ) && ( 'simple' === srp_product_type( $post->ID ) || 'subscription' == srp_product_type( $post->ID ) || 'bundle' == srp_product_type( $post->ID ) ) ) {
					$rewardpoints = points_for_simple_product();
					if ( $rewardpoints > 0 && ! $sumo_bookings_check ) {
						$message = is_user_logged_in() ? get_option( 'rs_message_for_single_product_point_rule' ) : get_option( 'rs_purchase_reward_message_single_product_guest', 'Purchase this Product and Earn [rewardpoints] Reward Points ([equalamount])' );
						?>
						<div class="woocommerce-info rs_message_for_single_product"><?php echo do_shortcode( $message ); ?></div>
						<?php
					} elseif ( $rewardpoints > 0 && $sumo_bookings_check ) {
						?>
						<div class="woocommerce-info rs_message_for_single_product"><?php echo do_shortcode( get_option( 'rs_message_for_booking_product' ) ); ?></div>

						<?php
					}
				}
			} else {
				$sumo_bookings_check = is_sumo_booking_active( $post->ID );
				if ( isset( $_COOKIE['rsreferredusername'] ) ) {
					if ( 'yes' == get_option( 'rs_referrer_earn_point_purchase_by_guest_users' ) && '1' == get_option( 'rs_show_hide_message_for_single_product_guest_referral' ) ) {
						$referralpoints = referral_points_for_simple_product();
						if ( $referralpoints > 0 ) {
							$msg = str_replace( '[rsreferredusername]', wc_clean( wp_unslash( $_COOKIE['rsreferredusername'] ) ), get_option( 'rs_message_for_single_product_point_rule_referral' ) );
							?>
							<div class="woocommerce-info rs_message_for_single_product"><?php echo do_shortcode( $msg ); ?></div>
							<?php
						}
					}
				}
				if ( 'yes' != get_option( 'rs_product_purchase_activated' ) ) {
					return;
				}

				if ( 'no' == get_option( 'rs_enable_product_category_level_for_product_purchase' ) && '1' != get_option( 'rs_award_points_for_cart_or_product_total' ) ) {
					return;
				}

				if ( '2' == get_option( 'rs_show_hide_message_for_single_product_guest' ) ) {
					return;
				}

				$checkproducttype = srp_product_object( $post->ID );
				if ( is_object( $checkproducttype ) && ( 'simple' == srp_product_type( $post->ID ) || ( 'subscription' == srp_product_type( $post->ID ) ) || 'bundle' == srp_product_type( $post->ID ) ) ) {
					$rewardpoints = points_for_simple_product();
					if ( $rewardpoints > 0 && ! $sumo_bookings_check ) {
						$message = is_user_logged_in() ? get_option( 'rs_message_for_single_product_point_rule' ) : get_option( 'rs_purchase_reward_message_single_product_guest', 'Purchase this Product and Earn [rewardpoints] Reward Points ([equalamount])' );
						?>
						<div class="woocommerce-info rs_message_for_single_product"><?php echo do_shortcode( $message ); ?></div>
						<?php
					} elseif ( $rewardpoints > 0 && $sumo_bookings_check ) {
						?>
						<div class="woocommerce-info rs_message_for_single_product"><?php echo do_shortcode( get_option( 'rs_message_for_booking_product' ) ); ?></div>
						<?php
					}
				}
			}
		}

		public static function display_purchase_message_for_simple_in_single_product_page() {
			global $post;
			$product = srp_product_object( $post->ID );
			$bool    = self::is_in_stock( $product );
			if ( ! $bool ) {
				return;
			}

			if ( 1 === did_action( 'woocommerce_before_single_product' ) ) {

				/* To Display Product Review Message */
				self::display_product_review_msg_in_single_product_page();

				/* To Display Subscribing Product Message */
				self::display_purchase_msg_in_single_product_page();

				/* To Display Product Purchase Message Based on Cart Total */
				self::display_cart_total_based_product_purchase_msg_in_single_product_page();

				/* To Display Product Purchase Message Based on Product Total */
				self::display_product_purchase_msg_in_single_product_page();
			}
		}

		public static function display_point_price_in_variable_product( $price, $ProductObj ) {
			if ( 'yes' != get_option( 'rs_point_price_activated' ) ) {
				return $price;
			}

			$ProductId = product_id_from_obj( $ProductObj );
			global $post;
			if ( '2' == get_option( 'rs_point_price_visibility' ) && ! is_user_logged_in() ) {
				return $price;
			} elseif ( '1' == get_option( 'rs_enable_disable_point_priceing' ) ) {
					$enabledpoints = calculate_point_price_for_products( $ProductId );
					$point_price   = $enabledpoints[ $ProductId ];
					$typeofprice   = check_display_price_type( $ProductId );
				if ( '2' == $typeofprice ) {
					$point_price = round_off_type( $point_price );
					$totalamount = display_point_price_value( $point_price );
					$separator   = get_option( 'rs_separator_for_point_price' );
					$totalamount = str_replace( $separator, '', $totalamount );
					return $totalamount;
				} elseif ( '' != $point_price ) {
						$point_price = round_off_type( $point_price );
						$totalamount = display_point_price_value( $point_price, true );
						return $price . '<span class="point_price_label">' . $totalamount;
				} else {
					return $price;
				}
			}
		}

		public static function rewardpoints_of_variation( $variation_id, $newparentid ) {
			$args         = array(
				'productid'   => $newparentid,
				'variationid' => $variation_id,
				'item'        => array( 'qty' => '1' ),
			);
			$rewardpoints = check_level_of_enable_reward_point( $args );
			$varpoints    = is_user_logged_in() ? RSMemberFunction::earn_points_percentage( get_current_user_id(), (float) $rewardpoints ) : (float) $rewardpoints;
			return $varpoints;
		}

		public static function is_in_stock( $ProductObj ) {

			if ( ! is_object( $ProductObj ) ) {
				return false;
			}

			$bool = $ProductObj->is_in_stock();

			if ( is_shop() ) {
				if ( ! $bool && '1' == get_option( 'rs_show_or_hide_message_for_outofstock' ) ) {
					$bool = true;
				}
			}
			if ( is_product() ) {
				if ( ! $bool && '1' == get_option( 'rs_message_outofstockproducts_product_page' ) ) {
					$bool = true;
				}
			}
			if ( is_page() ) {
				if ( ! $bool && '1' == get_option( 'rs_show_or_hide_message_for_customshop' ) ) {
					$bool = true;
				}
			}
			return $bool;
		}

		public static function display_minimum_quantity_error_message_simple_product() {

			global $product;

			if ( ! is_object( $product ) || 'variable' == $product->get_type() ) {
				return;
			}

			$min_quantity = rs_get_minimum_quantity_based_on_product_total( $product->get_id() );
			if ( $min_quantity <= 1 ) {
				return;
			}

			$rewardpoints = points_for_simple_product();
			if ( ! $rewardpoints ) {
				return;
			}

			$message = get_option( 'rs_minimum_quantity_error_message', 'Minimum <b>{min_quantity}</b> quantities required to earn points by purchasing <b>{product_name}</b>' );
			$message = str_replace( array( '{product_name}', '{min_quantity}' ), array( $product->get_name(), $min_quantity ), $message );
			?>
			<div class="woocommerce-error rs-minimum-quantity-error-simple"><?php echo esc_html( $message ); ?></div>
			<?php
		}
	}

	RS_Simple_Product_Messages::init();
}
