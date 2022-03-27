<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'FPRewardSystem_Free_Product' ) ) {

	class FPRewardSystem_Free_Product {

		public static function init() {

			add_action( 'wp_head' , array( __CLASS__ , 'add_free_product' ) ) ;

			add_action( 'wp_head', array( __CLASS__, 'add_bouns_points_for_member_level' ) ) ;

			add_action( 'woocommerce_checkout_update_order_meta' , array( __CLASS__ , 'save_data_to_order' ) ) ;

			add_filter( 'woocommerce_order_item_name' , array( __CLASS__ , 'show_msg_in_order_details' ) , 10 , 2 ) ;

			add_filter( 'woocommerce_cart_item_name' , array( __CLASS__ , 'show_message_next_to_free_product' ) , 10 , 3 ) ;

			add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'display_free_product_after_cart_table' ) , 999 ) ;

			add_action( 'woocommerce_cart_loaded_from_session' , array( __CLASS__ , 'alter_price_for_free_product' ) ) ;

			add_filter( 'woocommerce_cart_item_price' , array( __CLASS__ , 'custom_price' ) , 10 , 3 ) ;

			if ( '1' == get_option( 'rs_free_product_add_quantity' ) ) {

				add_filter( 'woocommerce_cart_item_quantity' , array( __CLASS__ , 'alter_quantity_for_free_product' ) , 10 , 2 ) ;

				add_filter( 'woocommerce_is_sold_individually' , array( __CLASS__ , 'free_product_as_sold_individually' ) , 10 , 2 ) ;
			}

			add_filter( 'woocommerce_add_to_cart_validation' , array( __CLASS__ , 'add_to_cart_validation' ) , 10 , 5 ) ;

			$ToOrderStatus = array( 'cancelled' , 'failed' , 'refunded' ) ;
			foreach ( $ToOrderStatus as $ToStatus ) {
				$FromOrderStatus = array( 'pending ' , 'processing' , 'on-hold' , 'completed' ) ;
				foreach ( $FromOrderStatus as $FromStatus ) {
					add_action( 'woocommerce_order_status_' . $FromStatus . '_to_' . $ToStatus , array( __CLASS__ , 'unset_free_product_from_order' ) ) ;
				}
			}

			$ToOrderStatus = array( 'pending ' , 'processing' , 'on-hold' , 'completed' ) ;
			foreach ( $ToOrderStatus as $ToStatus ) {
				$FromOrderStatus = array( 'cancelled' , 'failed' , 'refunded' ) ;
				foreach ( $FromOrderStatus as $FromStatus ) {
					add_action( 'woocommerce_order_status_' . $FromStatus . '_to_' . $ToStatus , array( __CLASS__ , 'set_free_product_in_order' ) ) ;
				}
			}

			//For newer version of Woocommerce (i.e) Version > 3.7.0;
			add_action( 'woocommerce_remove_cart_item' , array( __CLASS__ , 'unset_removed_products' ) , 10 , 1 ) ;

						//For older version of Woocommerce (i.e) Version < 2.3.0;
						add_action( 'woocommerce_before_cart_item_quantity_zero' , array( __CLASS__ , 'unset_removed_products' ) , 10 , 1 ) ;

			//For newer version of Woocommerce (i.e) Version > 2.3.0;
			add_action( 'woocommerce_cart_item_removed' , array( __CLASS__ , 'unset_removed_products' ) , 10 , 1 ) ;
		}

		/* Add Free Product To Cart */

		public static function add_free_product() {

			if ( ! is_user_logged_in() ) {
				return ;
			}

			$BanType = check_banning_type( get_current_user_id() ) ;
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return ;
			}

			if ( '1' == get_option( 'rs_free_product_add_by_user_or_admin' ) && ! is_cart() ) {
				return ;
			}

			$UserId            = get_current_user_id() ;
			$PointsData        = new RS_Points_Data( $UserId ) ;
			$TotalEarnedPoints = '1' == get_option( 'rs_select_earn_points_based_on' ) ? $PointsData->total_earned_points() : $PointsData->total_available_points() ;
			if ( empty( $TotalEarnedPoints ) ) {
				return ;
			}

			$LevelId           = rs_get_earning_and_redeeming_level_id( $TotalEarnedPoints, 'earning' ) ;
			$free_product_type = self::free_product_type( $LevelId ) ;
			if ( '2' == $free_product_type ) {
				return ;
			}

			$FreeProductList = self::free_product_list( $LevelId ) ;
			if ( ! srp_check_is_array( $FreeProductList ) ) {
				return ;
			}

			global $woocommerce ;
			$ListofProductWithPrice    = array() ;
			$ListofProductWithoutPrice = array() ;
			$MainVariations            = array() ;
			$CartItemKeyForFreeProduct = array() ;

			$PurchasedFreeProductList = get_user_meta( $UserId , 'product_id_for_free_product' , true ) ;
			$PurchasedFreeProductList = srp_check_is_array( $PurchasedFreeProductList ) ? $PurchasedFreeProductList : array() ;

			foreach ( $FreeProductList as $ProductId ) {
				if ( '' == $ProductId ) {
					continue ;
				}

				if ( ! check_if_user_already_purchase( $ProductId , $LevelId , $PurchasedFreeProductList ) ) {
					continue ;
				}

				$cartremovedlist = ( array ) get_user_meta( $UserId , 'listsetofids' , true ) ;
				$ProductObj      = srp_product_object( $ProductId ) ;
				$VariationId     = ( is_object( $ProductObj ) && 'simple' == srp_product_type( $ProductId ) ) ? 0 : ( int ) $ProductId ;
				$ParentId        = get_post_parent( $ProductObj ) ;
				if ( $ParentId > 0 ) {
					$parent_productid = $ParentId ;
					$VariationObj     = new WC_Product_Variation( $ProductId ) ;
					$Variations       = wc_get_formatted_variation( $VariationObj->get_variation_attributes() , true ) ;
					$ExplodeVariation = explode( ',' , $Variations ) ;
					foreach ( $ExplodeVariation as $EachVariation ) {
						$ExplodeEachVariation                                        = explode( ': ' , $EachVariation ) ;
						$MainVariations[ 'attribute_' . $ExplodeEachVariation[ 0 ] ] = $ExplodeEachVariation[ 1 ] ;
					}
					$getcurrentcartids           = $woocommerce->cart->generate_cart_id( $parent_productid , $VariationId , $MainVariations ) ;
					$CartItemKeyForFreeProduct[] = $getcurrentcartids ;
				} else {
					$parent_productid            = $ProductId ;
					$getcurrentcartids           = $woocommerce->cart->generate_cart_id( $parent_productid , $VariationId , $MainVariations ) ;
					$CartItemKeyForFreeProduct[] = $getcurrentcartids ;
				}
				$getcartcount = $woocommerce->cart->cart_contents_count ;
				$cartcontent  = $woocommerce->cart->cart_contents ;
				foreach ( $cartcontent as $key => $val ) {
					$productprice = $val[ 'line_subtotal' ] ;
					if ( $productprice > 0 ) {
						$ListofProductWithPrice[] = $productprice ;
					} else {
						$ListofProductWithoutPrice[] = $productprice ;
					}
				}
				$ProductCountWithPrice    = array_sum( $ListofProductWithPrice ) ;
				$ProductCountWithoutPrice = array_sum( $ListofProductWithoutPrice ) ;
				$found_or_not             = self::check_if_free_product_exist( $ProductId ) ;
				if ( ! in_array( $getcurrentcartids , $cartremovedlist ) ) {
					if ( ( $ProductCountWithPrice > 0 ) ) {
						if ( ! $found_or_not ) {
							if ( ( $getcartcount > 0 ) ) {
								if ( '1' == get_option( 'rs_free_product_add_by_user_or_admin' ) ) {
									WC()->cart->add_to_cart( $parent_productid , 1 , $VariationId , $MainVariations ) ;
									self::set_price_for_free_product() ;
								}
							}
							WC()->session->set( 'setruleids' , $LevelId ) ;
							WC()->session->set( 'excludedummyids' , $LevelId ) ;
							WC()->session->set( 'dynamicruleproducts' , $FreeProductList ) ;
						}
					} else {
						if ( 1 == $ProductCountWithoutPrice ) {
							$woocommerce->cart->remove_cart_item( $getcurrentcartids ) ;
						} else {
							foreach ( $CartItemKeyForFreeProduct as $listofcartitemkey ) {
								if ( ! in_array( $listofcartitemkey , $cartremovedlist ) ) {
									$woocommerce->cart->remove_cart_item( $listofcartitemkey ) ;
								}
							}
						}
					}
				}
				WC()->session->set( 'freeproductcartitemkeys' , $CartItemKeyForFreeProduct ) ;
			}
		}

		public static function set_price_for_free_product() {
			$Pointsdata  = new RS_Points_Data( get_current_user_id() ) ;
			$Points      = '1' == get_option( 'rs_select_earn_points_based_on' ) ? $Pointsdata->total_earned_points() : $Pointsdata->total_available_points() ;
			$RuleId      = rs_get_earning_and_redeeming_level_id( $Points , 'earning' ) ;
			$ProductList = self::free_product_list( $RuleId ) ;
			if ( ! srp_check_is_array( $ProductList ) ) {
				return ;
			}

			$PurcahsedProductList = get_user_meta( get_current_user_id() , 'product_id_for_free_product' , true ) ;
			$PurcahsedProductList = srp_check_is_array( $PurcahsedProductList ) ? $PurcahsedProductList : array() ;

			foreach ( WC()->cart->cart_contents as $item ) {
				$ProductId = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
				if ( ! in_array( $ProductId , ( array ) $ProductList ) ) {
					continue ;
				}

				if ( ! check_if_user_already_purchase( $ProductId , $RuleId , $PurcahsedProductList ) ) {
					continue ;
				}

				$item[ 'data' ]->set_price( 0 ) ;
			}
		}

		public static function add_to_cart_validation( $Passed, $product_id, $Qty, $variation_id = '', $variations = array() ) {
			$AllowedProduct = self::check_if_free_product_already_exists_in_cart( get_current_user_id() ) ;
			$ProductId      = ! empty( $variation_id ) ? $variation_id : $product_id ;
			if ( isset( $AllowedProduct[ $ProductId ] ) ) {
				if ( '1' == get_option( 'rs_free_product_add_quantity' ) ) {
					wc_add_notice( __( 'This product already is in the cart.' , 'rewardsystem' ) , 'error' ) ;
					return false ;
				}
			} else {
				$UnsetRemovedList = self::check_if_product_exists_in_free_product_list( $product_id , get_current_user_id() ) ;
				if ( $UnsetRemovedList ) {
					delete_user_meta( get_current_user_id() , 'listsetofids' ) ;
				}
			}
			return $Passed ;
		}

		public static function check_if_free_product_already_exists_in_cart( $UserId ) {
			$ListofFreeProductInCart = array() ;
			foreach ( WC()->cart->cart_contents as $key => $values ) {
				$ProductInCart                             = '' != $values[ 'variation_id' ] ? $values[ 'variation_id' ] : $values[ 'product_id' ] ;
				$AllowedProductList                        = self::check_if_product_exists_in_free_product_list( $ProductInCart , $UserId ) ;
				if ( $AllowedProductList ) {
					$ListofFreeProductInCart[ $ProductInCart ] = $AllowedProductList ;
				}
			}
			return $ListofFreeProductInCart ;
		}

		public static function check_if_product_exists_in_free_product_list( $ProductId, $UserId ) {
			$PointsData        = new RS_Points_data( $UserId ) ;
			$TotalEarnedPoints = '1' == get_option( 'rs_select_earn_points_based_on' ) ? $PointsData->total_earned_points() : $PointsData->total_available_points() ;
			$LevelofUser       = rs_get_earning_and_redeeming_level_id( $TotalEarnedPoints , 'earning' ) ;
			$ListofFreeProduct = self::free_product_list( $LevelofUser ) ;
			if ( in_array( $ProductId , $ListofFreeProduct ) ) {
				return true ;
			}

			return false ;
		}

		/* Add Bouns Points for Member Level */
		public static function add_bouns_points_for_member_level() {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			if ( 'no' == get_option( 'rs_enable_earned_level_based_reward_points', 'no' ) ) {
				return ;
			}
			$user_id = get_current_user_id() ;
			$BanType = check_banning_type( $user_id ) ;
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return ;
			}

			$pointsdata = new RS_Points_Data( $user_id ) ;
			$points     = '1' == get_option( 'rs_select_earn_points_based_on' ) ? $pointsdata->total_earned_points() : $pointsdata->total_available_points() ;
			if ( ! $points ) {
				return ;
			}

			$rules = get_option( 'rewards_dynamic_rule' ) ;
			if ( ! srp_check_is_array( $rules ) ) {
				return ;
			}
			
			$reached_rule_id = rs_get_earning_and_redeeming_level_id( $points, 'earning' ) ;
			if ( empty( $reached_rule_id ) ) {
				return ;
			}
			
			$type = isset( $rules[ $reached_rule_id ][ 'type' ] ) ? $rules[ $reached_rule_id ][ 'type' ] : '1' ;
			if ( '1' == $type ) {
				return ;
			}
			
			$bonus_points = isset( $rules[ $reached_rule_id ][ 'bounspoints' ] ) ? $rules[ $reached_rule_id ][ 'bounspoints' ] : '' ;
			if ( ! $bonus_points ) {
				return ;
			}

			$rule_ids = get_user_meta( $user_id, 'rs_rule_ids_based_on_bouns_points', true ) ;
			if ( srp_check_is_array($rule_ids) && in_array( $reached_rule_id, $rule_ids ) ) {
				return ;
			}

			$enabledisablemaxpoints = get_option( 'rs_enable_disable_max_earning_points_for_user' ) ;
			$new_obj                = new RewardPointsOrder( 0, 'no' ) ;
			if ( 'yes'  == $enabledisablemaxpoints ) {
				$new_obj->check_point_restriction( $bonus_points, 0, 'MLBP', $user_id, '', '', '', '', '' ) ;
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $bonus_points,
					'event_slug'        => 'MLBP', /* Member Level Bonus Points */
					'user_id'           => $user_id,
					'totalearnedpoints' => $bonus_points
						) ;
				$new_obj->total_points_management( $valuestoinsert , false ) ;
			}

			self::send_mail_for_admin_bouns_points( $reached_rule_id ) ;
			self::send_mail_for_user_bouns_points( $reached_rule_id ) ;

			if ( srp_check_is_array( $rule_ids ) ) {
				$data = array_merge( $rule_ids, array( $reached_rule_id ) ) ;
				update_user_meta( $user_id, 'rs_rule_ids_based_on_bouns_points', array_unique( $data ) ) ;
			} else {
				update_user_meta( $user_id, 'rs_rule_ids_based_on_bouns_points', array_filter( array( $reached_rule_id ) ) ) ;
			}
		}

		public static function set_free_product_in_order( $OrderId ) {
			$Order    = new WC_Order( $OrderId ) ;
			$OrderObj = srp_order_obj( $order ) ;
			$UserId   = $OrderObj[ 'order_userid' ] ;
			$Rules    = get_option( 'rewards_dynamic_rule' ) ;
			if ( ! srp_check_is_array( $Rules ) ) {
				return ;
			}

			$OldIds = array() ;

			$ProductIdinOrder = get_user_meta( $UserId , 'product_id_for_free_product' , true ) ;
			$ProductIdinOrder = srp_check_is_array( $ProductIdinOrder ) ? $ProductIdinOrder : array() ;

			foreach ( $Rules as $RuleID => $Rule ) {
				foreach ( $Order->get_items() as $item_id => $eachitem ) {
					$ProductId = ! empty( $eachitem[ 'variation_id' ] ) ? $eachitem[ 'variation_id' ] : $eachitem[ 'product_id' ] ;
					if ( ! in_array( $ProductId , ( array ) $Rule[ 'product_list' ] ) ) {
						continue ;
					}

					if ( srp_check_is_array( $ProductIdinOrder ) ) {
						if ( count( $ProductIdinOrder ) == count( $ProductIdinOrder , 1 ) ) {
							$OldIds[ $RuleID ] = $ProductIdinOrder ;
							$ProductIdinOrder  = $OldIds ;
						} else {
							$ProductIdinOrder[ $RuleID ][] = $ProductId ;
							$ProductIdinOrder[ $RuleID ]   = array_unique( $ProductIdinOrder[ $RuleID ] ) ;
						}
					} else {
						$ProductIdinOrder[ $RuleID ][] = $ProductId ;
					}

					update_user_meta( $UserId , 'product_id_for_free_product' , $ProductIdinOrder ) ;
				}
			}
		}

		public static function unset_free_product_from_order( $OrderId ) {
			$Order    = new WC_Order( $OrderId ) ;
			$OrderObj = srp_order_obj( $Order ) ;
			$UserId   = $OrderObj[ 'order_userid' ] ;
			$Rules    = get_option( 'rewards_dynamic_rule' ) ;
			if ( ! srp_check_is_array( $Rules ) ) {
				return ;
			}

			$ProductIdinOrder = get_user_meta( $UserId , 'product_id_for_free_product' , true ) ;
			$ProductIdinOrder = srp_check_is_array( $ProductIdinOrder ) ? $ProductIdinOrder : array() ;

			foreach ( $Rules as $RuleId => $Rule ) {
				foreach ( $Order->get_items() as $item_id => $eachitem ) {
					$ProductId = ! empty( $eachitem[ 'variation_id' ] ) ? $eachitem[ 'variation_id' ] : $eachitem[ 'product_id' ] ;
					if ( ! in_array( $ProductId , ( array ) $Rule[ 'product_list' ] ) ) {
						continue ;
					}

					if ( ! isset( $ProductIdinOrder[ $RuleId ] ) ) {
						continue ;
					}

					if ( ! srp_check_is_array( $ProductIdinOrder[ $RuleId ] ) ) {
						continue ;
					}

					if ( ( array_search( $ProductId , $ProductIdinOrder[ $RuleId ] )  == $key ) ) {
						unset( $ProductIdinOrder[ $RuleId ] ) ;
						update_user_meta( $UserId , 'product_id_for_free_product' , $ProductIdinOrder ) ;
					}
				}
			}
		}

		public static function free_product_as_sold_individually( $BoolVal, $ProductObj ) {
			$PointsData  = new RS_Points_Data( get_current_user_id() ) ;
			$TotalPoints = '1' == get_option( 'rs_select_earn_points_based_on' )  ? $PointsData->total_earned_points() : $PointsData->total_available_points() ;
			if ( 0 == $TotalPoints ) {
				return $BoolVal ;
			}

			$RuleId = rs_get_earning_and_redeeming_level_id( $TotalPoints , 'earning' ) ;
			if ( ! $RuleId ) {
				return $BoolVal ;
			}

			$ProductList = self::free_product_list( $RuleId ) ;
			if ( ! srp_check_is_array( $ProductList ) ) {
				return $BoolVal ;
			}

			$ProductId = product_id_from_obj( $ProductObj ) ;
			if ( in_array( $ProductId , $ProductList ) ) {
				return true ;
			}

			return $BoolVal ;
		}

		public static function free_product_list( $RuleId ) {
			$Rules       = get_option( 'rewards_dynamic_rule' ) ;
			$ProductList = isset( $Rules[ $RuleId ][ 'product_list' ] ) ? $Rules[ $RuleId ][ 'product_list' ] : array() ;
			return $ProductList ;
		}

		public static function free_product_type( $rule_id ) {
			$rules        = get_option( 'rewards_dynamic_rule' ) ;
			$product_type = isset( $rules[ $rule_id ][ 'type' ] ) ? $rules[ $rule_id ][ 'type' ] : 1 ;
			return $product_type ;
		}

		public static function check_if_free_product_exist( $ProductId ) {
			$AllowedProduct = self::check_if_free_product_already_exists_in_cart( get_current_user_id() ) ;
			if ( isset( $AllowedProduct[ $ProductId ] ) ) {
				return true ; //Found
			}

			return false ; //Not Found
		}

		public static function send_mail_for_admin( $Points ) {

			if ( 'yes' != get_option( 'rs_enable_admin_email_for_free_product' ) ) {
				return ;
			}

			$rules = get_option( 'rewards_dynamic_rule' ) ;
			if ( ! srp_check_is_array( $rules ) ) {
				return ;
			}

			$ruleid           = rs_get_earning_and_redeeming_level_id( $Points , 'earning' ) ;
			$levelname        = isset( $rules[ $ruleid ][ 'name' ] ) ? $rules[ $ruleid ][ 'name' ] : '' ;
			$free_product_ids = self::free_product_list( $ruleid ) ;
			$product_ids      = implode( ',' , $free_product_ids ) ;

			$subject = get_option( 'rs_subject_for_free_product_mail_send_admin', 'Free Product - Notification' ) ;
			$msg     = get_option( 'rs_content_for_free_product_mail_send_admin', 'Hi,<br/> Your user has got the product as free for reaching the configured level. Please check the below details,<br/> Username: [username]<br/>Product Name: [product_id]<br/>Level Name: [current_level_name].<br/>Thanks' ) ;
			$message = str_replace( array( '[username] , [current_level_name]', '[product_id]' ), array( get_option( 'woocommerce_email_from_name' ), $levelname, $product_ids ), $msg ) ;

			send_mail( get_option( 'woocommerce_email_from_address' ), $subject, $message ) ;
		}

		public static function send_mail_for_admin_bouns_points( $ruleid ) {
			if ( 'yes' != get_option( 'rs_enable_admin_email_for_bonus_points', 'no' ) ) {
				return ;
			}
			
			$user    = get_user_by( 'id', get_current_user_id() ) ;
			if ( !is_object( $user)) {
				return;
			}
			
			$user_name    = is_object( $user ) ? $user->user_login : 'Guest' ;

			$rules = get_option( 'rewards_dynamic_rule' ) ;
			if ( ! srp_check_is_array( $rules ) ) {
				return ;
			}
			$levelname    = isset( $rules[ $ruleid ][ 'name' ] ) ? $rules[ $ruleid ][ 'name' ] : '' ;
			$bouns_points = isset( $rules[ $ruleid ][ 'bounspoints' ] ) ? $rules[ $ruleid ][ 'bounspoints' ] : '' ;

			$subject = get_option( 'rs_subject_for_bonus_points_admin_email', 'Bouns Points - Notification' ) ;
			$message = get_option( 'rs_message_for_bonus_points_admin_email', 'Hi,<br/><br/> Your user <b>[username]</b> has received <b>[points_value]</b> bonus points for reaching the <b>[level_name]</b> level.<br/><br/>Thanks' ) ;
			$message = str_replace( array( '[username]', '[level_name]', '[points_value]' ), array( $user_name , $levelname, $bouns_points ), $message ) ;

			send_mail( get_option( 'woocommerce_email_from_address' ), $subject, $message ) ;
		}

		public static function send_mail_for_user_bouns_points( $ruleid ) {

			if ( 'yes' != get_option( 'rs_enable_user_email_for_bonus_points', 'no' ) ) {
				return ;
			}
			$rules = get_option( 'rewards_dynamic_rule' ) ;
			if ( ! srp_check_is_array( $rules ) ) {
				return ;
			}

			$user_obj     = get_user_by( 'id', get_current_user_id() ) ;
			if ( !is_object( $user_obj)) {
				return;
			}
			
			$user_name    = is_object( $user_obj ) ? $user_obj->user_login : 'Guest' ;
			$levelname    = isset( $rules[ $ruleid ][ 'name' ] ) ? $rules[ $ruleid ][ 'name' ] : '' ;
			$bouns_points = isset( $rules[ $ruleid ][ 'bounspoints' ] ) ? $rules[ $ruleid ][ 'bounspoints' ] : '' ;

			$subject = get_option( 'rs_subject_for_bonus_points_customer_email', 'Bouns Points - Notification' ) ;
			$message = get_option( 'rs_message_for_bonus_points_customer_email', 'Hi [username],<br/><br/> You have received <b>[points_value]</b> bonus point for reaching the [level_name] level.<br/><br/>Thanks' ) ;
			$message = str_replace( array( '[username]', '[level_name]', '[points_value]' ), array( $user_name, $levelname, $bouns_points ), $message ) ;

			send_mail( $user_obj->user_email, $subject, $message ) ;
		}

		public static function save_data_to_order( $OrderId ) {
			$Rules = get_option( 'rewards_dynamic_rule' ) ;
			if ( ! srp_check_is_array( $Rules ) ) {
				return ;
			}

			$Order       = new WC_Order( $OrderId ) ;
			$OrderObj    = srp_order_obj( $Order ) ;
			$UserId      = $OrderObj[ 'order_userid' ] ;
			$PointsData  = new RS_Points_Data( $UserId ) ;
			$TotalPoints = '1'  == get_option( 'rs_select_earn_points_based_on' )? $PointsData->total_earned_points() : $PointsData->total_available_points() ;
			$RuleId      = rs_get_earning_and_redeeming_level_id( $TotalPoints , 'earning' ) ;
			if ( empty( $RuleId ) ) {
				return ;
			}

			$free_product_type = self::free_product_type( $RuleId ) ;
			if ( '2' == $free_product_type ) {
				return ;
			}

			$FreeProductMsg = WC()->session->get( 'freeproductmsg' ) ;
			update_post_meta( $OrderId , 'listruleids' , $RuleId ) ;
			update_post_meta( $OrderId , 'ruleidsdata' , $Rules[ $RuleId ] ) ;

			$ProductIdinOrder = get_user_meta( $UserId , 'product_id_for_free_product' , true ) ;
			$ProductIdinOrder = srp_check_is_array( $ProductIdinOrder ) ? $ProductIdinOrder : array() ;
			$OldIds           = array() ;

			$send_mail = false ;
			foreach ( $Order->get_items() as $item_id => $eachitem ) {
				$ProductId        = ! empty( $eachitem[ 'variation_id' ] ) ? $eachitem[ 'variation_id' ] : $eachitem[ 'product_id' ] ;
				$FreeProductsList = self::free_product_list( $RuleId ) ;
				if ( ! in_array( $ProductId , ( array ) $FreeProductsList ) ) {
					continue ;
				}

				if ( srp_check_is_array( $ProductIdinOrder ) ) {
					if ( count( $ProductIdinOrder ) == count( $ProductIdinOrder , 1 ) ) {
						$OldIds[ $RuleId ] = $ProductIdinOrder ;
						$ProductIdinOrder  = $OldIds ;
					} else {
						$ProductIdinOrder[ $RuleId ][] = $ProductId ;
						$ProductIdinOrder[ $RuleId ]   = array_unique( $ProductIdinOrder[ $RuleId ] ) ;
					}
				} else {
					$ProductIdinOrder[ $RuleId ][] = $ProductId ;
				}
				$send_mail = true ;
				update_user_meta( $UserId , 'product_id_for_free_product' , $ProductIdinOrder ) ;
				wc_add_order_item_meta( $item_id , '_ruleidsdata' , $Rules[ $RuleId ] ) ;
				wc_add_order_item_meta( $item_id , '_rsfreeproductmsg' , $FreeProductMsg ) ;
			}

			if ( $send_mail ) {
				// Admin Email
				self::send_mail_for_admin( $TotalPoints ) ;
			}

			WC()->session->__unset( 'setruleids' ) ;
			WC()->session->__unset( 'freeproductcartitemkeys' ) ;
			WC()->session->__unset( 'freeproductmsg' ) ;
		}

		public static function alter_price_for_free_product( $CartObj ) {
			$PointsData = new RS_Points_Data( get_current_user_id() ) ;
			$Points     = '1' == get_option( 'rs_select_earn_points_based_on' ) ? $PointsData->total_earned_points() : $PointsData->total_available_points() ;
			$RuleId     = rs_get_earning_and_redeeming_level_id( $Points , 'earning' ) ;
			if ( empty( $RuleId ) ) {
				return ;
			}
			
			$free_product_type = self::free_product_type( $RuleId ) ;
			if ( '2' == $free_product_type ) {
				return ;
			}

			$Qty = get_option( 'rs_free_product_quantity' ) ;
			if ( empty( $Qty ) ) {
				return ;
			}

			if ( 1 == $Qty ) {
				self::set_price_for_free_product() ;
			} else {
				$ProductList = self::free_product_list( $RuleId ) ;
				if ( ! srp_check_is_array( $ProductList ) ) {
					return ;
				}

				foreach ( $CartObj->cart_contents as $key => $value ) {
					$ProductId = ! empty( $value[ 'variation_id' ] ) ? $value[ 'variation_id' ] : $value[ 'product_id' ] ;
					if ( ! in_array( $ProductId , ( array ) $ProductList ) ) {
						continue ;
					}

					if ( $value[ 'quantity' ] > $Qty ) {
						$Price     = $value[ 'data' ]->get_price() ;
						$ExceedQty = $value[ 'quantity' ] - $Qty ;
						$Price     = ( $Price * $ExceedQty ) / $value[ 'quantity' ] ;
						$value[ 'data' ]->set_price( $Price ) ;
					} else {
						$value[ 'data' ]->set_price( 0 ) ;
					}
				}
			}
		}

		public static function custom_price( $price, $item, $key ) {
			if ( '2' == get_option( 'rs_free_product_add_by_user_or_admin' ) ) {
				return $price ;
			}

			if ( '1' == get_option( 'rs_free_product_add_quantity' ) ) {
				return $price ;
			}

			if ( '' == get_option( 'rs_free_product_quantity' ) ) {
				return $price ;
			}

			$ProductId  = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
			$ProductObj = wc_get_product( $ProductId ) ;
			return wc_price( $ProductObj->get_price() ) ;
		}

		public static function display_free_product_after_cart_table() {
			$BanType = check_banning_type( get_current_user_id() ) ;
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return ;
			}

			if ( ! allow_reward_points_for_user( get_current_user_id() ) ) {
				return ;
			}

			$PointsData = new RS_Points_Data( get_current_user_id() ) ;
			$Points     = '1' == get_option( 'rs_select_earn_points_based_on' ) ? $PointsData->total_earned_points() : $PointsData->total_available_points() ;
			if ( empty( $Points ) ) {
				return ;
			}

			$RuleId = rs_get_earning_and_redeeming_level_id( $Points , 'earning' ) ;
			if ( empty( $RuleId ) ) {
				return ;
			}

			$free_product_type = self::free_product_type( $RuleId ) ;
			if ( '2' == $free_product_type ) {
				return ;
			}

			$ProductLists = self::free_product_list( $RuleId ) ;
			if ( ! srp_check_is_array( $ProductLists ) ) {
				return ;
			}

			global $woocommerce ;

			$FreeProducts = get_user_meta( get_current_user_id() , 'product_id_for_free_product' , true ) ;
			$FreeProducts = srp_check_is_array( $FreeProducts ) ? $FreeProducts : array() ;
			?>
			<div class='fp_rs_display_free_product'>
				<h3><?php echo wp_kses_post(get_option( 'rs_free_product_msg_caption' ) ); ?></h3>
				<?php
				foreach ( $ProductLists as $ProductId ) {
					if ( empty( $ProductId ) ) {
						continue ;
					}

					if ( ! check_if_user_already_purchase( $ProductId , $RuleId , $FreeProducts ) ) {
						continue ;
					}

					$ProductObj  = srp_product_object( $ProductId ) ;
					$VariationId = ( is_object( $ProductObj ) && 'simple' == srp_product_type( $ProductId ) ) ? 0 : ( int ) $ProductId ;
					$ParentId    = get_post_parent( $ProductObj ) ;
					if ( $ParentId > 0 ) {
						$VarObject  = new WC_Product_Variation( $ProductId ) ;
						$Variations = wc_get_formatted_variation( $VarObject->get_variation_attributes() , true ) ;
						$ExplodeVar = explode( ',' , $Variations ) ;
						$Mainvar    = array() ;
						foreach ( $ExplodeVar as $eachvariation ) {
							$explode2 = explode( ': ' , $eachvariation ) ;

							$Mainvar[ 'attribute_' . $explode2[ 0 ] ] = $explode2[ 1 ] ;
						}
						$CartItemKey = $woocommerce->cart->generate_cart_id( $ParentId , $VariationId , $Mainvar ) ;
					} else {
						$CartItemKey = $woocommerce->cart->generate_cart_id( $ProductId , $VariationId , array() ) ;
					}

					$Deletedkeys = get_user_meta( get_current_user_id() , 'listsetofids' , true ) ;
					if ( ! in_array( $CartItemKey , ( array ) $Deletedkeys ) ) {
						continue ;
					}

					$ThumbnailImg = get_the_post_thumbnail_url( $ProductId ) ? get_the_post_thumbnail_url( $ProductId ) : wc_placeholder_img_src() ;
					?>
					<a href="javascript:void(0)" class="add_removed_free_product_to_cart" data-cartkey="<?php echo esc_attr($CartItemKey) ; ?>">
						<img src="<?php echo esc_url($ThumbnailImg) ; ?>" width="30" height="30"/><?php echo wp_kses_post(get_the_title( $ProductId )) ; ?>
					</a><br/>
					<?php
				}
				?>
			</div>
			<?php
		}

		public static function show_message_next_to_free_product( $product_name, $cart_item, $cart_item_key ) {
			$BanType = check_banning_type( get_current_user_id() ) ;
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return $product_name ;
			}

			$ProductId  = ! empty( $cart_item[ 'variation_id' ] ) ? $cart_item[ 'variation_id' ] : $cart_item[ 'product_id' ] ;
			$PointsData = new RS_Points_Data( get_current_user_id() ) ;
			$Points     = '1' == get_option( 'rs_select_earn_points_based_on' ) ? $PointsData->total_earned_points() : $PointsData->total_available_points() ;
			$RuleId     = rs_get_earning_and_redeeming_level_id( $Points , 'earning' ) ;
			if ( empty( $RuleId ) ) {
				return $product_name ;
			}

			$free_product_type = self::free_product_type( $RuleId ) ;
			if ( '2' == $free_product_type ) {
				return $product_name ;
			}

			$ProductList = self::free_product_list( $RuleId ) ;
			if ( ! srp_check_is_array( $ProductList ) ) {
				return $product_name ;
			}

			if ( ! in_array( $ProductId , $ProductList ) ) {
				return $product_name ;
			}

			$PurchasedProducts = get_user_meta( get_current_user_id() , 'product_id_for_free_product' , true ) ;
			$PurchasedProducts = srp_check_is_array( $PurchasedProducts ) ? $PurchasedProducts : array() ;

			if ( ! check_if_user_already_purchase( $ProductId , $RuleId , $PurchasedProducts ) ) {
				return $product_name ;
			}

			if ( 'yes' != get_option( 'rs_remove_msg_from_cart_order' ) ) {
				return $product_name ;
			}

			$MsgInfo     = ( '1' == get_option( 'rs_free_product_add_quantity' ) ) ? get_option( 'rs_free_product_message_info' ) : str_replace( '[free_product_quantity]' , get_option( 'rs_free_product_quantity' ) , get_option( 'rs_free_product_quantity_message_info' ) ) ;
			$ReplacedMsg = str_replace( '[current_level_points]' , $Points , $MsgInfo ) ;
			WC()->session->set( 'freeproductmsg' , $ReplacedMsg ) ;
			return $product_name . '<br>' . $ReplacedMsg ;
		}

		public static function alter_quantity_for_free_product( $productquantity, $values ) {
			$CartItemKeys = null == WC()->session->get( 'freeproductcartitemkeys' ) ? array() : WC()->session->get( 'freeproductcartitemkeys' ) ;
			if ( ! srp_check_is_array( $CartItemKeys ) ) {
				return $productquantity ;
			}

			if ( ! in_array( $values , $CartItemKeys ) ) {
				return $productquantity ;
			}

			echo wp_kses_post(sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />' , $values ) );
			return ;
		}

		public static function show_msg_in_order_details( $item_name, $item ) {
			$FreeProductMsg = $item[ 'rsfreeproductmsg' ] ;
			if ( null == $FreeProductMsg || empty( $FreeProductMsg ) ) {
				return $item_name ;
			}

			if ( 'yes'  == get_option( 'rs_remove_msg_from_cart_order' ) ) {
				return $item_name . '<br>' . $FreeProductMsg ;
			}

			return $item_name ;
		}

		public static function unset_removed_products( $cart_item_key ) {
			$ListofProducts = ( array ) get_user_meta( get_current_user_id() , 'listsetofids' , true ) ;
			$MergedData     = array_unique( array_filter( array_merge( $ListofProducts , ( array ) $cart_item_key ) ) ) ;
			update_user_meta( get_current_user_id() , 'listsetofids' , $MergedData ) ;
		}

	}

	FPRewardSystem_Free_Product::init() ;
}
