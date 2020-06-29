<?php

if ( ! class_exists( 'RewardPointsOrder' ) ) {

    class RewardPointsOrder {

        public function __construct( $order_id = 0 , $apply_previous_order_points ) {
            $this->order_id                    = $order_id ;
            $this->order                       = new WC_Order( $order_id ) ;
            $this->apply_previous_order_points = $apply_previous_order_points ;
        }

        public function check_point_restriction( $getpaymentgatewayused , $pointsredeemed , $event_slug , $orderuserid , $nomineeid , $referrer_id , $productid , $variationid , $reasonindetail ) {
            return self::check_point_restriction_of_user( $getpaymentgatewayused , $pointsredeemed , $event_slug , $orderuserid , $nomineeid , $referrer_id , $productid , $variationid , $reasonindetail ) ;
        }

        private function check_point_restriction_of_user( $getpaymentgatewayused , $pointsredeemed , $event_slug , $orderuserid , $nomineeid , $referrer_id , $productid , $variationid , $reasonindetail ) {
            $restrictuserpoints = get_option( 'rs_max_earning_points_for_user' ) ;
            if ( ! empty( $restrictuserpoints ) ) {
                $PointsData   = new RS_Points_Data( $orderuserid ) ;
                $getoldpoints = $PointsData->total_available_points() ;
                if ( $getoldpoints <= $restrictuserpoints ) {
                    $totalpointss = $getoldpoints + $getpaymentgatewayused ;
                    if ( $totalpointss <= $restrictuserpoints ) {
                        $valuestoinsert = array( 'pointstoinsert' => $getpaymentgatewayused , 'event_slug' => $event_slug , 'user_id' => $orderuserid , 'referred_id' => $referrer_id , 'product_id' => $productid , 'variation_id' => $variationid , 'reasonindetail' => $reasonindetail , 'nominee_id' => $nomineeid , 'totalearnedpoints' => $getpaymentgatewayused ) ;
                        $this->total_points_management( $valuestoinsert ) ;
                        if ( $nomineeid != '' ) {
                            $table_args = array(
                                'user_id'       => $nomineeid ,
                                'checkpoints'   => 'PPRPFNP' ,
                                'nomineeid'     => $orderuserid ,
                                'nomineepoints' => $getpaymentgatewayused
                                    ) ;
                            RSPointExpiry::record_the_points( $table_args ) ;
                        }
                        if ( $referrer_id != '' && $event_slug != 'PPRRPG' ) {
                            $previouslog = get_option( 'rs_referral_log' ) ;
                            RS_Referral_Log::update_referral_log( $orderuserid , $referrer_id , $getpaymentgatewayused , array_filter( ( array ) $previouslog ) ) ;
                        }
                        if ( $event_slug == 'RRRP' ) {
                            $previouslog = get_option( 'rs_referral_log' ) ;
                            RS_Referral_Log::update_referral_log( $orderuserid , $referrer_id , $getpaymentgatewayused , array_filter( ( array ) $previouslog ) ) ;
                            update_user_meta( $referrer_id , '_rs_i_referred_by' , $orderuserid ) ;
                        }
                        if ( $event_slug == 'RPCPAR' || $event_slug == 'RPFPAC' )
                            update_user_meta( $orderuserid , 'usercommentpage' . $productid , '1' ) ;

                        if ( $event_slug == 'RPCPR' || $event_slug == 'RPFPOC' )
                            update_user_meta( $orderuserid , 'usercommentpost' . $productid , '1' ) ;

                        if ( $event_slug == 'RPPR' )
                            update_user_meta( $orderuserid , 'userreviewed' . $productid , '1' ) ;

                        if ( $event_slug == 'RPGV' ) {
                            $Msg = str_replace( "[giftvoucherpoints]" , $getpaymentgatewayused , get_option( 'rs_voucher_redeem_success_message' ) ) ;
                            echo addslashes( $Msg ) ;
                        }
                    } else {
                        $insertpoints = $restrictuserpoints - $getoldpoints ;
                        $this->points_management( $insertpoints , $pointsredeemed , 'MREPFU' , $orderuserid ) ;
                    }
                } else {
                    $this->points_management( 0 , $pointsredeemed , 'MREPFU' , $orderuserid ) ;
                }
            } else {
                $valuestoinsert = array( 'pointstoinsert' => $getpaymentgatewayused , 'event_slug' => $event_slug , 'user_id' => $orderuserid , 'referred_id' => $referrer_id , 'product_id' => $productid , 'variation_id' => $variationid , 'nominee_id' => $nomineeid , 'totalearnedpoints' => $getpaymentgatewayused ) ;
                $this->total_points_management( $valuestoinsert ) ;
                if ( $nomineeid != '' ) {
                    $table_args = array(
                        'user_id'       => $nomineeid ,
                        'checkpoints'   => 'PPRPFNP' ,
                        'nomineeid'     => $orderuserid ,
                        'nomineepoints' => $getpaymentgatewayused
                            ) ;
                    RSPointExpiry::record_the_points( $table_args ) ;
                }
                if ( $referrer_id != '' && $event_slug != 'PPRRPG' ) {
                    $previouslog = get_option( 'rs_referral_log' ) ;
                    RS_Referral_Log::update_referral_log( $orderuserid , $referrer_id , $getpaymentgatewayused , array_filter( ( array ) $previouslog ) ) ;
                }
                if ( $event_slug == 'RRRP' ) {
                    $previouslog = get_option( 'rs_referral_log' ) ;
                    RS_Referral_Log::update_referral_log( $orderuserid , $referrer_id , $getpaymentgatewayused , array_filter( ( array ) $previouslog ) ) ;
                    update_user_meta( $referrer_id , '_rs_i_referred_by' , $orderuserid ) ;
                }
                if ( $event_slug == 'RPCPAR' )
                    update_user_meta( $orderuserid , 'usercommentpage' . $productid , '1' ) ;

                if ( $event_slug == 'RPCPR' )
                    update_user_meta( $orderuserid , 'usercommentpost' . $productid , '1' ) ;

                if ( $event_slug == 'RPPR' )
                    update_user_meta( $orderuserid , 'userreviewed' . $productid , '1' ) ;

                if ( $event_slug == 'RPGV' ) {
                    $Msg = str_replace( "[giftvoucherpoints]" , $getpaymentgatewayused , get_option( 'rs_voucher_redeem_success_message' ) ) ;
                    echo addslashes( $Msg ) ;
                }
            }
        }

        private function get_total_earned_points() {
            global $wpdb ;
            $table_name           = $wpdb->prefix . 'rspointexpiry' ;
            $gettotalearnedpoints = $wpdb->get_results( $wpdb->prepare( "SELECT SUM((earnedpoints)) as availablepoints FROM $table_name WHERE orderid = %d" , $this->order_id ) , ARRAY_A ) ;
            $totalearnedpoints    = ($gettotalearnedpoints[ 0 ][ 'availablepoints' ] != NULL) ? $gettotalearnedpoints[ 0 ][ 'availablepoints' ] : 0 ;
            return $totalearnedpoints ;
        }

        public function update_earning_points_for_user( $Method = '' ) {
            if ( ! $this->is_user_banned() )
                return ;

            if ( ! $this->check_restriction() )
                return ;

            if ( ! $this->award_earning_point_only_once() )
                return ;

            if ( ! $this->award_points_only_once_for_referral_system() )
                return ;

            if ( $this->check_redeeming_in_order() )
                return ;

            $order_id = $this->order_id ;
            if ( get_post_meta( $order_id , 'rs_prevent_point_for_first_purchase' , true ) == 'yes' )
                return ;

            /* Restrict Earn points for Selected Payment Gateway */
            $payment_method                     = get_post_meta( $order_id , '_payment_method' , true ) ;
            $default_value_for_restrict_payment = get_option( 'rs_disable_point_if_reward_points_gateway' , 'no' ) == 'yes' ? array( 'reward_gateway' ) : array() ;
            $selected_payment_method            = ( array ) get_option( 'rs_select_payment_gateway_for_restrict_reward' , $default_value_for_restrict_payment ) ;
            if ( ! empty( $selected_payment_method ) && in_array( $payment_method , $selected_payment_method ) ) {
                return ;
            }

            global $wpdb ;
            $PointsTable            = $wpdb->prefix . 'rspointexpiry' ;
            $enabledisablemaxpoints = get_option( 'rs_enable_disable_max_earning_points_for_user' ) ;
            $order                  = $this->order ;
            $orderobj               = srp_order_obj( $order ) ;
            $orderuserid            = $orderobj[ 'order_userid' ] ;

            do_action( 'rs_perform_action_for_order' , $order_id ) ;
            /* Reward Points For Using Payment Gateway Method - Start */
            if ( get_option( 'rs_reward_action_activated' ) == 'yes' ) {
                $payment_method = $orderobj[ 'payment_method' ] ;
                $GatewayPoints  = points_for_payment_gateways( $order_id , $orderuserid , $payment_method ) ;
                if ( ! empty( $GatewayPoints ) ) {
                    if ( $enabledisablemaxpoints == 'yes' ) {
                        $this->check_point_restriction( $GatewayPoints , 0 , 'RPG' , $orderuserid , '' , '' , '' , '' , '' ) ;
                    } else {
                        $valuestoinsert = array( 'pointstoinsert' => $GatewayPoints , 'event_slug' => 'RPG' , 'user_id' => $orderuserid , 'totalearnedpoints' => $GatewayPoints ) ;
                        $this->total_points_management( $valuestoinsert ) ;
                    }
                    update_post_meta( $order_id , 'srp_gateway_points_awarded' , 'yes' ) ;
                    do_action( 'fp_reward_point_for_using_gateways' ) ;
                }
            }
            /* Reward Points For Using Payment Gateway Method - End */

            /* Reward Points For Purchasing the Product - Start */
            $AwardPointsForRenewalOrder = block_points_for_renewal_order( $order_id , get_option( 'rs_award_point_for_renewal_order' ) ) ;
            if ( $AwardPointsForRenewalOrder == true ) {
                $RewardPointsBasedOn = get_option( 'rs_award_points_for_cart_or_product_total' ) ;
                $points_refer        = array() ;
                foreach ( $order->get_items() as $item ) {
                    $Productid     = $item[ 'product_id' ] ;
                    $Variationid   = $item[ 'variation_id' ] ;
                    $itemquantity  = $item[ 'qty' ] ;
                    $ProductObj    = srp_product_object( $Productid ) ;
                    $payment_price = 0 ;
                    $AwardPoints   = true ;
                    if ( class_exists( 'SUMOPaymentPlans' ) ) {
                        $Id            = empty( $item[ 'variation_id' ] ) ? $item[ 'product_id' ] : $item[ 'variation_id' ] ;
                        $payment_price = (is_payment_product( $order_id , $Id ) && is_final_payment( $order_id )) ? get_payment_product_price( $order_id ) : 0 ;
                        $AwardPoints   = (is_payment_product( $order_id , $Id ) && is_final_payment( $order_id )) ? true : false ;
                    }
                    if ( ($AwardPoints === true ) ) {
                        if ( get_option( 'rs_product_purchase_activated' ) == 'yes' ) {
                            $reasonindetail = $this->apply_previous_order_points == 'yes' ? $Method : '' ;
                            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'yes' ) {
                                $this->rs_insert_the_selected_level_in_reward_points( $enabledisablemaxpoints , 0 , $Productid , $Variationid , $itemquantity , $orderuserid , $equearnamt   = '' , $equredeemamt = '' , $order_id , $item , $reasonindetail , $payment_price ) ;
                            } else {
                                if ( $RewardPointsBasedOn == '1' )
                                    $this->rs_insert_the_selected_level_in_reward_points( $enabledisablemaxpoints , 0 , $Productid , $Variationid , $itemquantity , $orderuserid , $equearnamt   = '' , $equredeemamt = '' , $order_id , $item , $reasonindetail , $payment_price ) ;
                            }
                            do_action( 'fp_reward_point_for_product_purchase' ) ;
                        }

                        /* Referral Reward Points For Purchasing the Product - Start */
                        $AwardRefPointsForRenewalOrder = block_points_for_renewal_order( $order_id , get_option( 'rs_award_referral_point_for_renewal_order' ) ) ;
                        if ( $AwardRefPointsForRenewalOrder == true && get_option( 'rs_referral_activated' ) == 'yes' ) {
                            $OrderStatus     = array() ;
                            $orderstatuslist = get_option( 'rs_order_status_control' ) ;
                            if ( srp_check_is_array( $orderstatuslist ) ) {
                                foreach ( $orderstatuslist as $value ) {
                                    $OrderStatus[] = 'wc-' . $value ;
                                }
                            }
                            $ReferrerName         = get_post_meta( $order_id , '_referrer_name' , true ) ;
                            $ReferrerEmail        = get_post_meta( $order_id , '_referrer_email' , true ) ;
                            $BillingEmail         = (WC_VERSION <= ( float ) ('3.0')) ? $order->billing_email : $order->get_billing_email() ;
                            $OrderCount           = RSPointExpiry::get_order_count( $BillingEmail , $orderuserid , $OrderStatus , $ReferrerName ) ;
                            $CheckOrderCountLimit = RSPointExpiry::check_order_count_limit( $OrderCount , 'no' ) ;
                            $CheckIfMultiReferrer = RSPointExpiry::check_if_user_has_multiple_referrer( $BillingEmail , $orderobj ) ;
                            $CheckIfSameIP        = RSPointExpiry::check_if_referrer_and_referral_from_same_ip( $order ) ;
                            if ( ! $CheckOrderCountLimit && $CheckIfMultiReferrer && $CheckIfSameIP ) {
                                $ReferIdFromPaymentPlan = get_referrer_id_from_payment_plan( $order_id ) ;
                                $ReferrerId             = empty( $ReferIdFromPaymentPlan ) ? $ReferrerName : $ReferIdFromPaymentPlan ;
                                if ( ! empty( $ReferrerId ) && ($ReferrerEmail != $BillingEmail) ) {
                                    $points_refer[ $Productid ] = $this->rs_insert_the_selected_level_in_referral_reward_points( $enabledisablemaxpoints , $ReferrerId , $orderuserid , $Productid , $Variationid , $item , 'yes' , $payment_price ) ;
                                    $this->rs_insert_the_selected_level_in_referral_reward_points( $enabledisablemaxpoints , $ReferrerId , $orderuserid , $Productid , $Variationid , $item , 'no' , $payment_price ) ;
                                } else {
                                    $ReferrerId = check_if_referrer_has_manual_link( $orderuserid ) ;
                                    if ( $ReferrerId )
                                        $this->rs_insert_the_selected_level_in_referral_reward_points( $enabledisablemaxpoints , $ReferrerId , $orderuserid , $Productid , $Variationid , $item , 'no' , $payment_price ) ;
                                }
                            }
                        }
                    }
                    /* Referral Reward Points For Purchasing the Product - End */
                }

                if ( get_option( 'rs_product_purchase_activated' ) == 'yes' ) {
                    if ( get_option( 'rs_enable_first_purchase_reward_points' ) == 'yes' )
                        $this->rs_insert_points_based_on_first_purchase( $enabledisablemaxpoints , $orderuserid ) ;

                    //Award Points Based on Cart Total
                    if ( $RewardPointsBasedOn == '2' )
                        $this->rs_insert_points_based_on_cart_total( $enabledisablemaxpoints , 0 , $Productid , $Variationid , $itemquantity , $orderuserid , '' , '' , $order_id , $item , $reasonindetail , $payment_price ) ;
                }

                if ( $this->apply_previous_order_points == 'no' && get_post_meta( $order_id , 'reward_points_awarded' , true ) ) {
                    $PointsData = new RS_Points_Data( $orderuserid ) ;
                    $Points     = $PointsData->total_available_points() ;
                    if ( ! empty( $Points ) ) {
                        if ( get_option( 'rs_sms_activated' ) == 'yes' && get_option( 'rs_enable_send_sms_to_user' ) == 'yes' ) {
                            if ( get_option( 'rs_send_sms_earning_points' ) == 'yes' ) {
                                $PhoneNumber = ! empty( get_user_meta( $orderuserid , 'rs_phone_number_value_from_signup' , true ) ) ? get_user_meta( $orderuserid , 'rs_phone_number_value_from_signup' , true ) : get_user_meta( $orderuserid , 'rs_phone_number_value_from_account_details' , true ) ;
                                $PhoneNumber = ! empty( $PhoneNumber ) ? $PhoneNumber : get_user_meta( $orderuserid , 'billing_phone' , true ) ;
                                if ( get_option( 'rs_sms_sending_api_option' ) == '1' ) {
                                    RSFunctionForSms::send_sms_twilio_api( $order_id , 'earning' , '' , $PhoneNumber ) ;
                                } elseif ( get_option( 'rs_sms_sending_api_option' ) == '2' ) {
                                    RSFunctionForSms::send_sms_nexmo_api( $order_id , 'earning' , '' , $PhoneNumber ) ;
                                }
                            }
                        }
                    }

                    if ( get_option( 'rs_referral_activated' ) == 'yes' )
                        update_post_meta( $order_id , 'rsgetreferalpoints' , $points_refer ) ;

                    if ( get_option( 'rs_email_activated' ) == 'yes' )
                        send_mail_for_product_purchase( $orderuserid , $order_id ) ;
                }
            }
            /* Reward Points For Purchasing the Product - End */
        }

        public function rs_insert_points_based_on_first_purchase( $enabledisablemaxpoints , $orderuserid ) {

            if ( ! $orderuserid )
                return ;

            $OrderCount = get_posts( array(
                'numberposts' => -1 ,
                'meta_key'    => '_customer_user' ,
                'meta_value'  => $orderuserid ,
                'post_type'   => wc_get_order_types() ,
                'post_status' => array( 'wc-pending' , 'wc-processing' , 'wc-on-hold' , 'wc-completed' ) ,
                    ) ) ;

            if ( count( $OrderCount ) > 1 )
                return ;

            $PointsForFristPurchase = get_option( 'rs_reward_points_for_first_purchase_in_fixed' ) ;
            if ( empty( $PointsForFristPurchase ) )
                return ;

            if ( $enabledisablemaxpoints == 'yes' ) {
                $this->check_point_restriction( $PointsForFristPurchase , 0 , 'PFFP' , $orderuserid , '' , '' , '' , '' , '' ) ;
            } else {
                $valuestoinsert = array(
                    'pointstoinsert'    => $PointsForFristPurchase ,
                    'event_slug'        => 'PFFP' ,
                    'user_id'           => $orderuserid ,
                    'totalearnedpoints' => $PointsForFristPurchase
                        ) ;
                $this->total_points_management( $valuestoinsert ) ;
            }
        }

        public function rs_insert_points_based_on_cart_total( $enabledisablemaxpoints , $pointsredeemed , $productid , $variationid , $itemquantity , $orderuserid , $equearnamt , $equredeemamt , $order_id , $item , $reasonindetail , $payment_price ) {

            if ( ! $orderuserid )
                return ;

            if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'yes' )
                return ;

            if ( get_option( 'rs_enable_cart_total_reward_points' ) == '2' )
                return ;

            $event_slug                = 'PPRPBCT' ;
            $NomineeIdsinMyaccount     = (get_option( 'rs_nominee_activated' ) == 'yes') ? get_user_meta( $orderuserid , 'rs_selected_nominee' , true ) : '' ;
            $EnableNomineeinMyaccount  = (get_option( 'rs_nominee_activated' ) == 'yes') ? get_user_meta( $orderuserid , 'rs_enable_nominee' , true ) : 'no' ;
            $NomineeIdsinCheckout      = (get_option( 'rs_nominee_activated' ) == 'yes') ? get_post_meta( $order_id , 'rs_selected_nominee_in_checkout' , true ) : '' ;
            $productlevelrewardpointss = get_reward_points_based_on_cart_total( $this->order->get_total() ) ;

            if ( $productlevelrewardpointss != 0 )
                include ('frontend/rs_insert_points_for_product_purchase.php') ;
        }

        public function rs_insert_the_selected_level_in_reward_points( $enabledisablemaxpoints , $pointsredeemed , $productid , $variationid , $itemquantity , $orderuserid , $equearnamt , $equredeemamt , $order_id , $item , $reasonindetail , $payment_price ) {
            if ( block_points_for_salepriced_product( $productid , $variationid ) == 'yes' )
                return ;

            $NomineeIdsinMyaccount    = (get_option( 'rs_nominee_activated' ) == 'yes') ? get_user_meta( $orderuserid , 'rs_selected_nominee' , true ) : '' ;
            $EnableNomineeinMyaccount = (get_option( 'rs_nominee_activated' ) == 'yes') ? get_user_meta( $orderuserid , 'rs_enable_nominee' , true ) : 'no' ;
            $NomineeIdsinCheckout     = (get_option( 'rs_nominee_activated' ) == 'yes') ? get_post_meta( $order_id , 'rs_selected_nominee_in_checkout' , true ) : '' ;
            $order                    = $this->order ;
            $order_total              = $order->get_total() ;
            $minimum_cart_total       = get_option( 'rs_minimum_cart_total_for_earning' ) ;
            $maximum_cart_total       = get_option( 'rs_maximum_cart_total_for_earning' ) ;

            if ( get_option( 'rs_enable_disable_reward_point_based_coupon_amount' ) == 'yes' && $this->apply_previous_order_points == 'no' ) {

                $UsedCoupons = ( float ) WC()->version < ( float ) ('3.7') ? $order->get_used_coupons() : $order->get_coupon_codes() ;

                if ( srp_check_is_array( $UsedCoupons ) ) {
                    $productidss               = empty( $variationid ) ? $productid : $variationid ;
                    $modified_point_list       = get_post_meta( $order_id , 'points_for_current_order' , true ) ;
                    $productlevelrewardpointss = ( $payment_price == 0 ) ? $modified_point_list[ $productidss ] : $payment_price ;
                    if ( $minimum_cart_total != '' && $minimum_cart_total != 0 ) {
                        if ( $order_total < $minimum_cart_total )
                            $productlevelrewardpointss = 0 ;
                    }
                    if ( $maximum_cart_total != '' && $maximum_cart_total != 0 ) {
                        if ( $order_total > $maximum_cart_total )
                            $productlevelrewardpointss = 0 ;
                    }
                    if ( ! empty( $productlevelrewardpointss ) )
                        include ('frontend/rs_insert_points_for_product_purchase.php') ;
                } else {
                    $args                      = array(
                        'productid'     => $productid ,
                        'variationid'   => $variationid ,
                        'item'          => $item ,
                        'payment_price' => $payment_price
                            ) ;
                    $productlevelrewardpointss = check_level_of_enable_reward_point( $args ) ;
                    if ( $minimum_cart_total != '' && $minimum_cart_total != 0 ) {
                        if ( $order_total < $minimum_cart_total )
                            $productlevelrewardpointss = 0 ;
                    }
                    if ( $maximum_cart_total != '' && $maximum_cart_total != 0 ) {
                        if ( $order_total > $maximum_cart_total )
                            $productlevelrewardpointss = 0 ;
                    }
                    if ( ! empty( $productlevelrewardpointss ) )
                        include ('frontend/rs_insert_points_for_product_purchase.php') ;
                }
            } else {
                $args                      = array(
                    'productid'     => $productid ,
                    'variationid'   => $variationid ,
                    'item'          => $item ,
                    'payment_price' => $payment_price
                        ) ;
                $productlevelrewardpointss = check_level_of_enable_reward_point( $args ) ;
                if ( $minimum_cart_total != '' && $minimum_cart_total != 0 ) {
                    if ( $order_total < $minimum_cart_total )
                        $productlevelrewardpointss = 0 ;
                }

                if ( $maximum_cart_total != '' && $maximum_cart_total != 0 ) {
                    if ( $order_total > $maximum_cart_total )
                        $productlevelrewardpointss = 0 ;
                }
                if ( ! empty( $productlevelrewardpointss ) )
                    include ('frontend/rs_insert_points_for_product_purchase.php') ;
            }
        }

        public function rs_insert_the_selected_level_in_referral_reward_points( $enabledisablemaxpoints , $referrer_id , $orderuserid , $productid , $variationid , $item , $getting_referrer , $payment_price ) {
            //User Info who placed the order
            $limitation = false ;
            $myid       = '' ;
            if ( $orderuserid == 0 && get_option( 'rs_referrer_earn_point_purchase_by_guest_users' ) == 'yes' ) {
                $order            = new WC_Order( $this->order_id ) ;
                $referral_info    = (WC_VERSION <= ( float ) ('3.0')) ? $order->billing_email : $order->get_billing_email() ;
                $strtotimeregdate = time() ;
            } else {
                $referral_info    = $orderuserid ;
                $user_info        = new WP_User( $orderuserid ) ;
                $strtotimeregdate = strtotime( $user_info->user_registered ) ;
            }
            //User Info who referred the user to place the order
            $refuser_info          = new WP_User( $referrer_id ) ;
            $refregdate            = $refuser_info->user_registered ;
            $strtotimerefregdate   = strtotime( $refregdate ) ;
            $modifiedregdate       = date( 'Y-m-d h:i:sa' , $strtotimeregdate ) ;
            $delay_days            = get_option( '_rs_select_referral_points_referee_time_content' ) ;
            $checking_date         = date( 'Y-m-d h:i:sa' , strtotime( $modifiedregdate . ' + ' . $delay_days . ' days ' ) ) ;
            $modifiedcheckingdate  = strtotime( $checking_date ) ;
            $current_date          = date( 'Y-m-d h:i:sa' ) ;
            $modified_current_date = strtotime( $current_date ) ;
            $pointsredeemed        = 0 ;
            //Is for Immediatly
            if ( get_option( '_rs_select_referral_points_referee_time' ) == '1' ) {
                $limitation = true ;
            } else {
                // Is for Limited Time with Number of Days
                $limitation = ( $modified_current_date > $modifiedcheckingdate ) ? true : false ;
            }
            $CheckIfReferredAlreadyExist = $strtotimeregdate ? ($strtotimeregdate > $strtotimerefregdate) : false ;
            if ( $limitation == true && $CheckIfReferredAlreadyExist ) {
                $refuser = get_user_by( 'login' , $referrer_id ) ;
                $myid    = is_object( $refuser ) ? $refuser->ID : $referrer_id ;
            }

            $args           = array(
                'productid'        => $productid ,
                'variationid'      => $variationid ,
                'item'             => $item ,
                'referred_user'    => $myid ,
                'getting_referrer' => $getting_referrer ,
                'payment_price'    => $payment_price
                    ) ;
            $pointstoinsert = check_level_of_enable_reward_point( $args ) ;

            $event_slug = ( $getting_referrer == 'no' ) ? 'PPRRP' : 'PPRRPG' ;

            if ( 'yes' === get_option( 'rs_referral_points_after_discounts' ) && 'PPRRP' === $event_slug ) {
                $points_after_discounts = get_post_meta( $this->order_id , 'rs_referrer_points_after_discounts' , true ) ;
                $item_product_id        = 'variable' == wc_get_product( $productid )->get_type() ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                $pointstoinsert         = isset( $points_after_discounts[ $item_product_id ] ) ? $points_after_discounts[ $item_product_id ] : 0 ;
            }

            if ( empty( $pointstoinsert ) )
                return ;

            if ( $enabledisablemaxpoints == 'yes' ) {
                if ( $event_slug == 'PPRRP' ) {
                    $this->check_point_restriction( $pointstoinsert , $pointsredeemed , $event_slug , $myid , $nomineeid = '' , $orderuserid , $productid , $variationid , $reasonindetail ) ;
                } else if ( $event_slug == 'PPRRPG' ) {
                    $this->check_point_restriction( $pointstoinsert , $pointsredeemed , $event_slug , $orderuserid , $nomineeid = '' , $myid , $productid , $variationid , $reasonindetail ) ;
                }
            } else {
                if ( $event_slug == 'PPRRP' ) {
                    $valuestoinsert = array( 'pointstoinsert' => $pointstoinsert , 'event_slug' => $event_slug , 'user_id' => $myid , 'referred_id' => $orderuserid , 'product_id' => $productid , 'variation_id' => $variationid , 'totalearnedpoints' => $pointstoinsert ) ;
                } else if ( $event_slug == 'PPRRPG' ) {
                    $valuestoinsert = array( 'pointstoinsert' => $pointstoinsert , 'event_slug' => $event_slug , 'user_id' => $orderuserid , 'referred_id' => $myid , 'product_id' => $productid , 'variation_id' => $variationid , 'totalearnedpoints' => $pointstoinsert ) ;
                }
                $this->total_points_management( $valuestoinsert ) ;
                if ( $event_slug != 'PPRRPG' ) {
                    $previouslog = get_option( 'rs_referral_log' ) ;
                    RS_Referral_Log::update_referral_log( $myid , $referral_info , $pointstoinsert , array_filter( ( array ) $previouslog ) ) ;
                }
            }

            if ( $event_slug == 'PPRRP' ) {
                do_action( 'fp_product_purchase_points_for_referrer' , $myid , $orderuserid , $pointstoinsert ) ;
            } else {
                do_action( 'fp_product_purchase_points_for_getting_referred' , $orderuserid , $myid , $pointstoinsert ) ;
            }

            update_post_meta( $this->order_id , 'referralsystem_earning_once' , '1' ) ;
            if ( $getting_referrer == 'yes' )
                return $pointstoinsert ;
        }

        public function insert_points_for_product( $enabledisablemaxpoints , $order_id , $orderuserid , $nomineeid , $productlevelrewardpointss , $productid , $variationid , $reasonindetail ) {
            $event_slug = 'PPRPFN' ;
            if ( $enabledisablemaxpoints == 'yes' ) {
                $this->check_point_restriction( $productlevelrewardpointss , $pointsredeemed = 0 , $event_slug , $orderuserid , $nomineeid , '' , $productid , $variationid , $reasonindetail ) ;
            } else {
                $valuestoinsert = array( 'pointstoinsert' => $productlevelrewardpointss , 'event_slug' => $event_slug , 'user_id' => $orderuserid , 'product_id' => $productid , 'variation_id' => $variationid , 'reasonindetail' => $reasonindetail , 'nominee_id' => $nomineeid , 'nominee_points' => $productlevelrewardpointss , 'totalearnedpoints' => $productlevelrewardpointss ) ;
                $this->total_points_management( $valuestoinsert ) ;
                $table_args     = array(
                    'user_id'       => $nomineeid ,
                    'checkpoints'   => 'PPRPFNP' ,
                    'nomineeid'     => $orderuserid ,
                    'nomineepoints' => $productlevelrewardpointss
                        ) ;
                RSPointExpiry::record_the_points( $table_args ) ;
            }
        }

        public function check_restriction() {

            if ( get_option( 'rs_reward_action_activated' ) != 'yes' )
                return true ;

            $order         = $this->order ;
            $order_user_id = srp_order_obj( $order ) ;
            $order_user_id = $order_user_id[ 'order_userid' ] ;
            if ( ! $order_user_id )
                return true ;

            $OrderCount = get_posts( array(
                'numberposts' => -1 ,
                'meta_key'    => '_customer_user' ,
                'meta_value'  => $order_user_id ,
                'post_type'   => wc_get_order_types() ,
                'post_status' => implode( "','" , array_keys( wc_get_order_statuses() ) ) ,
                    ) ) ;

            if ( count( $OrderCount ) == 1 && get_option( '_rs_enable_signup' ) == 'yes' ) {
                if ( get_option( 'rs_reward_signup_after_first_purchase' ) == 'yes' && get_option( 'rs_signup_points_with_purchase_points' ) == 'yes' ) {
                    update_post_meta( $this->order_id , 'rs_prevent_point_for_first_purchase' , 'yes' ) ;
                    return false ;
                }
            }
            return true ;
        }

        public function award_earning_point_only_once() {
            $earningpointonce = get_post_meta( $this->order_id , 'earning_point_once' , true ) ;
            $earningpointonce = $earningpointonce != '1' ? true : false ;
            return $earningpointonce ;
        }

        public function award_points_only_once_for_referral_system() {
            $referrer_value = get_post_meta( $this->order_id , 'referralsystem_earning_once' , true ) ;
            $referrer_value = $referrer_value == "1" ? false : true ;
            return $referrer_value ;
        }

        public function is_user_banned() {
            $orderobj     = srp_order_obj( $this->order ) ;
            $banning_type = check_banning_type( $orderobj[ 'order_userid' ] ) ;
            $ban          = ($banning_type != 'earningonly' && $banning_type != 'both') ? true : false ;
            return $ban ;
        }

        public function check_redeeming_in_order() {
            $user_id             = srp_order_obj( $this->order ) ;
            $user_id             = $user_id[ 'order_userid' ] ;
            $rewardpointscoupons = $this->order->get_items( array( 'coupon' ) ) ;
            $getuserdatabyid     = get_user_by( 'id' , $user_id ) ;
            $getusernickname     = isset( $getuserdatabyid->user_login ) ? $getuserdatabyid->user_login : "" ;
            $maincouponchecker   = 'sumo_' . strtolower( $getusernickname ) ;
            $auto_redeem_name    = 'auto_redeem_' . strtolower( $getusernickname ) ;
            $sumo_coupon_name    = array( $maincouponchecker , $auto_redeem_name ) ;

            if ( get_option( 'rs_disable_point_if_coupon' ) == 'yes' ) {
                if ( ! empty( $rewardpointscoupons ) ) {
                    foreach ( $rewardpointscoupons as $array ) {
                        $applied_coupons[] = $array[ 'code' ] ;
                    }
                    $diff_array = array_diff( $applied_coupons , $sumo_coupon_name ) ;
                    if ( is_array( $diff_array ) && ! empty( $diff_array ) ) {
                        return true ;
                    }
                }
            }
            if ( get_option( 'rs_enable_redeem_for_order' ) == 'yes' ) {
                if ( ! empty( $rewardpointscoupons ) ) {
                    foreach ( $rewardpointscoupons as $array ) {
                        $applied_coupons[] = $array[ 'code' ] ;
                    }
                    if ( in_array( $maincouponchecker , $applied_coupons ) || in_array( $auto_redeem_name , $applied_coupons ) ) {
                        return true ;
                    }
                }
            }
            if ( get_option( 'rs_coupon_compatability_activated' ) == 'yes' && get_option( '_rs_not_allow_earn_points_if_sumo_coupon' ) == 'yes' ) {
                foreach ( $rewardpointscoupons as $couponcode => $value ) {
                    $coupon_id_array   = new WC_Coupon( $value[ 'name' ] ) ;
                    $coupon_id         = srp_coupon_obj( $coupon_id_array ) ;
                    $coupon_id         = $coupon_id[ 'coupon_id' ] ;
                    $sumo_coupon_check = get_post_meta( $coupon_id , 'sumo_coupon_check' , true ) ;
                    if ( $sumo_coupon_check == 'yes' ) {
                        return true ;
                    }
                }
            }

            return check_if_discount_applied() ;
        }

        public function total_points_management( $args ) {
            $default_args   = array(
                'pointstoinsert'    => 0 ,
                'pointsredeemed'    => 0 ,
                'referred_id'       => 0 ,
                'product_id'        => 0 ,
                'variation_id'      => 0 ,
                'reasonindetail'    => '' ,
                'nominee_id'        => '' ,
                'nominee_points'    => 0 ,
                'totalearnedpoints' => 0 ,
                'totalredeempoints' => 0
                    ) ;
            $valuestoinsert = wp_parse_args( $args , $default_args ) ;
            $user_id        = $valuestoinsert[ 'user_id' ] ;
            if ( empty( $user_id ) )
                return ;

            if ( ! allow_reward_points_for_user( $user_id ) )
                return ;

            if ( check_if_discount_applied() )
                return ;

            global $wpdb ;
            $table_name = $wpdb->prefix . 'rspointexpiry' ;
            $date       = expiry_date_for_points() ;
            if ( isset( $valuestoinsert[ 'manualaddpoints' ] ) )
                $date       = ! empty( $valuestoinsert[ 'expireddate' ] ) ? $valuestoinsert[ 'expireddate' ] : 999999999999 ;

            $user_name      = ! empty( $valuestoinsert[ 'referred_id' ] ) ? get_user_by( 'id' , $valuestoinsert[ 'referred_id' ] )->user_login : '' ;
            $pointstoinsert = ($valuestoinsert[ 'event_slug' ] == 'MAP') ? $valuestoinsert[ 'pointstoinsert' ] : RSMemberFunction::earn_points_percentage( $user_id , ( float ) $valuestoinsert[ 'pointstoinsert' ] ) ;

            if ( $valuestoinsert[ 'event_slug' ] == 'PFFP' )
                update_post_meta( $this->order_id , 'rs_first_purchase_points' , $pointstoinsert ) ;

            $table_args = array(
                'user_id'           => $user_id ,
                'pointstoinsert'    => $pointstoinsert ,
                'usedpoints'        => $valuestoinsert[ 'pointsredeemed' ] ,
                'date'              => $date ,
                'checkpoints'       => $valuestoinsert[ 'event_slug' ] ,
                'orderid'           => $this->order_id ,
                'totalearnedpoints' => $valuestoinsert[ 'totalearnedpoints' ] ,
                'totalredeempoints' => $valuestoinsert[ 'totalredeempoints' ] ,
                'reason'            => $valuestoinsert[ 'reasonindetail' ] ,
                'productid'         => $valuestoinsert[ 'product_id' ] ,
                'variationid'       => $valuestoinsert[ 'variation_id' ] ,
                'refuserid'         => $valuestoinsert[ 'referred_id' ] ,
                'nomineeid'         => $valuestoinsert[ 'nominee_id' ] ,
                'nomineepoints'     => $valuestoinsert[ 'nominee_points' ]
                    ) ;
            RSPointExpiry::insert_earning_points( $table_args ) ;
            RSPointExpiry::record_the_points( $table_args ) ;
            $to         = get_user_by( 'id' , $user_id )->user_email ;
            rs_send_mail_for_actions( $to , $valuestoinsert[ 'event_slug' ] , $pointstoinsert , $user_name , $this->order_id ) ;
            $no_of_days = days_from_point_expiry_email() ;
            if ( get_option( 'rs_email_template_expire_activated' ) == "yes" && $no_of_days != 0 && $date != 999999999999 ) {
                $date_to_send_mail = strtotime( '-' . $no_of_days . 'days' , $date ) ;
                wp_schedule_single_event( $date_to_send_mail , 'rs_send_mail_before_expiry' ) ;
            }
        }

        public function points_management( $earned_points , $redeemed_points , $event_slug , $user_id ) {
            $table_args = array(
                'user_id'           => $user_id ,
                'pointstoinsert'    => $earned_points ,
                'usedpoints'        => $redeemed_points ,
                'checkpoints'       => $event_slug ,
                'orderid'           => $this->order_id ,
                'totalearnedpoints' => $this->get_total_earned_points() ,
                'totalredeempoints' => 0 ,
                    ) ;
            RSPointExpiry::insert_earning_points( $table_args ) ;
            RSPointExpiry::record_the_points( $table_args ) ;
            $this->rs_send_mail_for_reaching_maximum_threshold( $user_id ) ;
        }

        public function rs_send_mail_for_reaching_maximum_threshold( $user_id ) {
            if ( get_option( 'rs_mail_for_reaching_maximum_threshold' ) == 'yes' ) {
                $PointsData     = new RS_Points_Data( $user_id ) ;
                $totalpoints    = $PointsData->total_available_points() ;
                $user_data      = get_user_by( 'ID' , $user_id ) ;
                $receiver_name  = is_object( $user_data ) ? $user_data->user_login : '' ;
                $receiver_mail  = is_object( $user_data ) ? $user_data->user_email : '' ;
                $Email_subject  = get_option( 'rs_mail_subject_for_reaching_maximum_threshold' ) ;
                $message        = get_option( 'rs_mail_message_for_reaching_maximum_threshold' ) ;
                $Email_message  = str_replace( '[maximum_threshold]' , get_option( 'rs_max_earning_points_for_user' ) , str_replace( '[availablepoints]' , $totalpoints , $message ) ) ;
                $Email_message  = do_shortcode( $Email_message ) ;
                $admin_email_id = get_option( 'admin_email' ) ;
                $admin_name     = get_bloginfo( 'name' , 'display' ) ;
                if ( $admin_email_id != '' && $admin_name != '' && $receiver_name != '' && $totalpoints != '' && $receiver_mail != '' ) {
                    add_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;
                    ob_start() ;
                    wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $Email_subject ) ) ;
                    echo $Email_message ;
                    wc_get_template( 'emails/email-footer.php' ) ;
                    $woo_temp_msg                 = ob_get_clean() ;
                    $message_headers              = "MIME-Version: 1.0\r\n" ;
                    $message_headers              .= "From: \"{$admin_name}\" <{$admin_email_id}>\n" . "Content-Type: text/html; charset=\"" . get_option( 'blog_charset' ) . "\"\n" ;
                    $message_headers              .= "Reply-To: " . $receiver_name . " <" . $receiver_mail . ">\r\n" ;
                    FPRewardSystem::$rs_from_name = $admin_name ;
                    add_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
                    if ( WC_VERSION <= ( float ) ('2.2.0') ) {
                        wp_mail( $receiver_mail , $Email_subject , $Email_message , $message_headers ) ;
                    } else {
                        $mailer = WC()->mailer() ;
                        $mailer->send( $receiver_mail , $Email_subject , $woo_temp_msg , $message_headers ) ;
                    }
                    remove_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 1 ) ;
                }
            }
        }

    }

}