<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSPointExpiry' ) ) {

    class RSPointExpiry {

        protected static $total_points ;
        protected static $redeemed_points ;
        protected static $expired_points ;
        protected static $available_points ;

        public static function init() {
            $orderstatuslist = get_option( 'rs_order_status_control' ) ;
            if ( is_array( $orderstatuslist ) && ! empty( $orderstatuslist ) ) {
                foreach ( $orderstatuslist as $value ) {
                    add_action( 'woocommerce_order_status_' . $value , array( __CLASS__ , 'update_earning_points_for_user' ) , 1 ) ;
//                    add_action( 'woocommerce_thankyou' , array( __CLASS__ , 'update_earning_points_for_user' ) , 1 ) ; Commented because product and referral purchase points awarded twice.
                    add_action( 'woocommerce_shipstation_shipnotify' , array( __CLASS__ , 'update_earning_points_for_user' ) , 1 ) ;

                    add_action( 'woocommerce_order_status_' . $value , array( __CLASS__ , 'award_reward_points_for_coupon' ) , 1 ) ;

                    add_action( 'woocommerce_order_status_' . $value , array( __CLASS__ , 'signup_points_after_purchase' ) ) ;
                }
            }

            add_action( 'woocommerce_checkout_update_order_meta' , array( __CLASS__ , 'checkout_cookies_referral_meta' ) , 1 , 2 ) ;

            add_action( 'woocommerce_checkout_update_order_meta' , array( __CLASS__ , 'delete_cookie_for_user_and_guest' ) , 10 , 2 ) ;

            add_action( 'woocommerce_order_status_' . get_option( 'rs_order_status_after_gateway_purchase' , 'completed' ) , array( __CLASS__ , 'redeem_points_for_reward_gateway' ) , 1 ) ;

            $order_status_control = get_option( 'rs_list_other_status' ) ;
            if ( is_array( $order_status_control ) && ! empty( $order_status_control ) ) {
                $orderstatuslist = get_option( 'rs_order_status_control' ) ;
                foreach ( $order_status_control as $order_status ) {
                    if ( is_array( $orderstatuslist ) && ! empty( $orderstatuslist ) ) {
                        foreach ( $orderstatuslist as $value ) {
                            add_action( 'woocommerce_order_status_' . $value . '_to_' . $order_status , array( __CLASS__ , 'update_revised_points_for_user' ) ) ;
                        }
                    }
                }
            }
            add_action( 'wpo_wcsre_email_sent' , array( __CLASS__ , 'update_revised_points_for_user' ) ) ;

            $orderstatuslistforredeem = get_option( 'rs_order_status_control_redeem' ) ;
            if ( is_array( $orderstatuslistforredeem ) && ! empty( $orderstatuslistforredeem ) ) {
                foreach ( $orderstatuslistforredeem as $value ) {

                    add_action( 'woocommerce_thankyou' , array( __CLASS__ , 'update_redeem_point_for_user_third_party_sites' ) , 1 ) ;

                    add_action( 'woocommerce_thankyou' , array( __CLASS__ , 'update_redeem_point_for_user' ) , 1 ) ;

                    add_action( 'woocommerce_order_status_' . $value , array( __CLASS__ , 'update_redeem_point_for_user' ) , 1 ) ;
                }
            }


            $order_status_control = get_option( 'rs_list_other_status_for_redeem' ) ;
            if ( is_array( $order_status_control ) && ! empty( $order_status_control ) ) {
                foreach ( $order_status_control as $order_status ) {
                    if ( is_array( $orderstatuslistforredeem ) && ! empty( $orderstatuslistforredeem ) ) {
                        foreach ( $orderstatuslistforredeem as $value ) {
                            if ( $value != 'pending' ) {
                                add_action( 'woocommerce_order_status_' . $value . '_to_' . $order_status , array( __CLASS__ , 'update_revised_redeem_points_for_user' ) ) ;
                            }
                            if ( in_array( 'pending' , $orderstatuslist ) ) {
                                if ( is_admin() ) {
                                    add_action( 'woocommerce_order_status_pending' . '_to_' . $order_status , array( __CLASS__ , 'update_revised_redeem_points_for_user' ) ) ;
                                }
                            }
                        }
                    }
                }
            }

            add_action( 'woocommerce_order_status_changed' , array( __CLASS__ , 'revise_redeemed_points_for_user' ) , 1 , 3 ) ;

            add_action( 'wp_head' , array( __CLASS__ , 'check_if_expiry' ) ) ;

            add_action( 'wp_head' , array( __CLASS__ , 'delete_if_used' ) ) ;

            add_action( 'wp_head' , array( __CLASS__ , 'delete_if_expired' ) ) ;

            add_action( 'admin_init' , array( __CLASS__ , 'update_order_status' ) , 9999 ) ;

            add_action( 'delete_user' , array( __CLASS__ , 'delete_referral_points_if_user_deleted' ) ) ;

            add_action( 'woocommerce_checkout_update_order_meta' , array( __CLASS__ , 'check_redeeming_in_order' ) , 10 , 2 ) ;

            add_action( 'rs_perform_action_for_order' , array( __CLASS__ , 'insert_buying_points_for_user' ) ) ;

            add_filter( 'the_content' , array( __CLASS__ , 'msg_for_page_and_post_comment' ) ) ;

            add_action( 'comment_post' , array( __CLASS__ , 'award_points_for_comments' ) , 10 ) ;

            add_action( 'transition_comment_status' , array( __CLASS__ , 'award_points_for_comments_is_approved' ) , 10 , 3 ) ;

            add_action( 'woocommerce_process_shop_order_meta' , array( __CLASS__ , 'point_info_for_manual_order' ) , 50 , 2 ) ;

            add_action( 'sumopaymentplans_payment_is_completed' , array( __CLASS__ , 'final_payment' ) , 1 , 3 ) ;

            add_action( 'woocommerce_register_form' , array( __CLASS__ , 'display_checkbox_in_registration_form' ) ) ;

            add_action( 'woocommerce_before_my_account' , array( __CLASS__ , 'display_checkbox_in_my_account_page' ) ) ;

            if ( get_option( 'rs_message_before_after_cart_table' ) == '1' ) {
                if ( get_option( 'rs_reward_point_troubleshoot_before_cart' ) == '1' ) {
                    add_action( 'woocommerce_before_cart' , array( __CLASS__ , 'available_points_for_user' ) ) ;
                } else {
                    add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'available_points_for_user' ) ) ;
                }
            } else {
                add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'available_points_for_user' ) ) ;
            }
            add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'available_points_for_user' ) ) ;

            add_action( 'save_post' , array( __CLASS__ , 'award_points_for_product_creation' ) , 1 , 2 ) ;
        }

        /* Award Points for Product Creation */

        public static function award_points_for_product_creation( $post_id , $post ) {
            if ( 'product' != $post->post_type )
                return ;

            $BanType = check_banning_type( get_current_user_id() ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            if ( get_option( 'rs_reward_for_enable_product_create' ) == 'no' )
                return ;

            if ( empty( get_option( 'rs_reward_Product_create' ) ) )
                return ;

            if ( get_post_meta( $post->ID , 'productcreationpoints' , true ) == 1 )
                return ;

            $new_obj        = new RewardPointsOrder( 0 , 'no' ) ;
            $valuestoinsert = array( 'pointstoinsert' => get_option( 'rs_reward_Product_create' ) , 'event_slug' => 'RPCPRO' , 'user_id' => $post->post_author , 'product_id' => $productid , 'totalearnedpoints' => get_option( 'rs_reward_Product_create' ) ) ;
            $new_obj->total_points_management( $valuestoinsert ) ;
            update_post_meta( $post->ID , 'productcreationpoints' , '1' ) ;
        }

        public static function available_points_for_user() {
            if ( ! is_user_logged_in() )
                return ;

            if ( ! allow_reward_points_for_user( get_current_user_id() ) )
                return ;

            if ( check_banning_type( get_current_user_id() ) == 'both' )
                return ;

            $ShowMsg = is_cart() ? get_option( 'rs_show_hide_message_for_my_rewards' ) : get_option( 'rs_show_hide_message_for_my_rewards_checkout_page' ) ;
            if ( $ShowMsg == 2 )
                return ;

            $PointsData = new RS_Points_Data( get_current_user_id() ) ;
            $Points     = $PointsData->total_available_points() ;
            if ( empty( $Points ) )
                return ;

            $Msg       = is_cart() ? get_option( 'rs_message_user_points_in_cart' ) : get_option( 'rs_message_user_points_in_checkout' ) ;
            $DivClass1 = is_cart() ? 'sumo_reward_points_current_points_message' : 'sumo_available_points' ;
            $DivClass2 = is_cart() ? 'rs_cart_message' : 'rs_checkout_messages' ;
            ?>
            <div class="woocommerce-info <?php echo $DivClass1 ; ?> <?php echo $DivClass2 ; ?>">
                <?php echo do_shortcode( $Msg ) ; ?>
            </div>
            <?php
        }

        /* Display Checkbox in My Account to involve User in Reward Program */

        public static function display_checkbox_in_my_account_page() {
            if ( get_option( 'rs_enable_reward_program' ) != 'yes' )
                return ;

            $checkbox_value = get_user_meta( get_current_user_id() , 'allow_user_to_earn_reward_points' , true ) ;
            if ( empty( $checkbox_value ) )
                update_user_meta( get_current_user_id() , 'allow_user_to_earn_reward_points' , 'yes' ) ;
            ?>
            <div class="enable_reward_points">
                <p>
                    <input type="checkbox" name="rs_enable_earn_points_for_user" id="rs_enable_earn_points_for_user" class="rs_enable_earn_points_for_user" <?php if ( $checkbox_value == 'yes' ) { ?>checked="checked"<?php } ?>/> 
                    <?php echo ( $checkbox_value == 'yes' ) ? get_option( 'rs_msg_in_acc_page_when_checked' ) : get_option( 'rs_msg_in_acc_page_when_unchecked' ) ; ?>
                </p>
            </div>
            <?php
        }

        /* Display Checkbox in Checkout to involve User in Reward Program */

        public static function display_checkbox_in_registration_form() {
            if ( get_option( 'rs_enable_reward_program' ) != 'yes' )
                return ;
            ?>
            <div class="enable_reward_points">
                <p>
                    <input type="checkbox" name="rs_enable_earn_points_for_user_in_reg_form" id="rs_enable_earn_points_for_user_in_reg_form" class="rs_enable_earn_points_for_user_in_reg_form"/> <?php echo get_option( 'rs_msg_in_reg_page' ) ; ?>
                </p>
            </div>
            <?php
        }

        /* Fires when admin change the status */

        public static function award_points_for_comments_is_approved( $NewStatus , $OldStatus , $CommentObj ) {
            if ( get_option( 'rs_reward_action_activated' ) != 'yes' )
                return ;

            if ( ! is_object( $CommentObj ) )
                return ;

            $CommentObj  = get_comment( $CommentObj->comment_ID ) ;
            $CommentType = get_post_type( $CommentObj->comment_post_ID ) ;
            $post_types  = apply_filters( 'rs_custom_post_type_for_posts' , array( 'post' ) ) ;

            $AwardAfterApproved = '' ;
            if ( $CommentType == 'page' ) {
                $AwardAfterApproved = get_option( "rs_page_comment_reward_status" ) ;
            } elseif ( in_array( $CommentType , $post_types ) ) {
                $AwardAfterApproved = get_option( "rs_post_comment_reward_status" ) ;
            } elseif ( $CommentType == 'product' ) {
                $AwardAfterApproved = get_option( "rs_review_reward_status" ) ;
            }
            if ( $AwardAfterApproved == 1 )
                if ( $NewStatus == 'approved' && $OldStatus == 'unapproved' )
                    self::award_points_for_comments( $CommentObj->comment_ID ) ;
        }

        /* Fires when Comment in Frontend */

        public static function award_points_for_comments( $commentid ) {
            if ( get_option( 'rs_reward_action_activated' ) != 'yes' )
                return ;

            $CommentObj  = get_comment( $commentid ) ;
            $CommentType = get_post_type( $CommentObj->comment_post_ID ) ;
            $PostId      = $CommentObj->comment_post_ID ;
            $PostStatus  = $CommentObj->comment_approved ;
            $UserId      = $CommentObj->user_id ;

            //Award Points for Page Comment
            self::award_points_for_page_comment( $CommentObj , $CommentType , $UserId , $PostId , $PostStatus ) ;

            //Award Points for Post Comment
            self::award_points_for_post_comment( $CommentObj , $CommentType , $UserId , $PostId , $PostStatus ) ;

            //Award Points for Product Review
            self::award_points_for_product_review( $CommentObj , $CommentType , $UserId , $PostId , $PostStatus ) ;
        }

        /* Awarding Points for Page Comment */

        public static function award_points_for_page_comment( $CommentObj , $CommentType , $UserId , $PostId , $PostStatus ) {
            //RPCPAR Checkpoints is changed to RPFPAC(Reward Points For Page Comment)
            if ( get_option( 'rs_reward_for_comment_Page' ) != 'yes' )
                return ;

            if ( $CommentType != 'page' )
                return ;

            $PointsToInsert = get_option( "rs_reward_page_review" ) ;
            if ( empty( $PointsToInsert ) )
                return ;

            $StatusToAwardPoints       = get_option( "rs_page_comment_reward_status" ) ;
            $RestrictPointsOncePerUser = get_option( 'rs_restrict_reward_page_comment' ) ;
            self::check_whether_award_points_once_or_more( $RestrictPointsOncePerUser , $UserId , $PostId , 'usercommentpage' , 'RPFPAC' , $PointsToInsert , $PostStatus , $StatusToAwardPoints ) ;
        }

        /* Awarding Points for Post Comment */

        public static function award_points_for_post_comment( $CommentObj , $CommentType , $UserId , $PostId , $PostStatus ) {
            //RPCPR Checkpoints is changed to RPFPOC(Reward Points For Post Comment)
            if ( get_option( 'rs_reward_for_comment_Post' ) != 'yes' )
                return ;

            if ( $CommentType != 'post' )
                return ;

            $PointsToInsert = get_option( "rs_reward_post_review" ) ;
            if ( empty( $PointsToInsert ) )
                return ;

            $StatusToAwardPoints       = get_option( "rs_post_comment_reward_status" ) ;
            $RestrictPointsOncePerUser = get_option( 'rs_restrict_reward_post_comment' ) ;
            self::check_whether_award_points_once_or_more( $RestrictPointsOncePerUser , $UserId , $PostId , 'usercommentpost' , 'RPFPOC' , $PointsToInsert , $PostStatus , $StatusToAwardPoints ) ;
        }

        /* Awarding Points for Product Review */

        public static function award_points_for_product_review( $CommentObj , $CommentType , $UserId , $PostId , $PostStatus ) {
            if ( get_option( 'rs_enable_product_review_points' ) != 'yes' )
                return ;

            if ( $CommentType != 'product' )
                return ;

            $PointsToInsert = get_option( "rs_reward_product_review" ) ;
            if ( empty( $PointsToInsert ) )
                return ;

            $StatusToAwardPoints       = get_option( "rs_review_reward_status" ) ;
            $RestrictPointsOncePerUser = get_option( 'rs_restrict_reward_product_review' ) ;
            if ( get_option( 'rs_reward_for_comment_product_review' ) == 'yes' ) {
                $UserInfo                        = get_user_by( 'id' , $UserId ) ;
                $EmailId                         = $UserInfo->user_email ;
                $CheckIfUserPurchasedThisProduct = self::check_if_customer_purchased( $UserId , $EmailId , $PostId , '' ) ;
                if ( $CheckIfUserPurchasedThisProduct <= 0 )
                    return ;

                self::check_whether_award_points_once_or_more( $RestrictPointsOncePerUser , $UserId , $PostId , 'userreviewed' , 'RPPR' , $PointsToInsert , $PostStatus , $StatusToAwardPoints ) ;
            } else {
                self::check_whether_award_points_once_or_more( $RestrictPointsOncePerUser , $UserId , $PostId , 'userreviewed' , 'RPPR' , $PointsToInsert , $PostStatus , $StatusToAwardPoints ) ;
            }
            do_action( 'fp_reward_point_for_product_review' ) ;
        }

        /* Check Whether to Award Point for Product Review, Page and Post Comment Only Once or More */

        public static function check_whether_award_points_once_or_more( $RestrictPointsOncePerUser , $UserId , $PostId , $MetaName , $EventSlug , $PointsToInsert , $PostStatus , $StatusToAwardPoints ) {
            if ( $RestrictPointsOncePerUser == 'yes' ) {
                $CheckIfUserAlreadyReviewed = get_user_meta( $UserId , $MetaName . $PostId , true ) ;
                if ( $CheckIfUserAlreadyReviewed == '1' )
                    return ;

                if ( $StatusToAwardPoints == '1' ) {
                    if ( $PostStatus == '1' )
                        self::rs_insert_points_for_comments( $PointsToInsert , $EventSlug , $UserId , $PostId , $MetaName ) ;
                }else {
                    self::rs_insert_points_for_comments( $PointsToInsert , $EventSlug , $UserId , $PostId , $MetaName ) ;
                }
            } else {
                if ( $StatusToAwardPoints == '1' ) {
                    if ( $PostStatus == '1' )
                        self::rs_insert_points_for_comments( $PointsToInsert , $EventSlug , $UserId , $PostId , $MetaName ) ;
                }else {
                    self::rs_insert_points_for_comments( $PointsToInsert , $EventSlug , $UserId , $PostId , $MetaName ) ;
                }
            }
        }

        /* Insert Points for Product Review, Page and Post Comment */

        public static function rs_insert_points_for_comments( $PointsToInsert , $EventSlug , $UserId , $PostId , $MetaName ) {
            if ( ! allow_reward_points_for_user( $UserId ) )
                return ;

            $Object = new RewardPointsOrder( 0 , 'no' ) ;
            if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                $Object->check_point_restriction( $PointsToInsert , 0 , $EventSlug , $UserId , '' , '' , $PostId , '' , '' ) ;
            } else {
                $ValuesToInsert = array( 'pointstoinsert' => $PointsToInsert , 'event_slug' => $EventSlug , 'user_id' => $UserId , 'product_id' => $PostId , 'totalearnedpoints' => $PointsToInsert ) ;
                $Object->total_points_management( $ValuesToInsert ) ;
                update_user_meta( $UserId , $MetaName . $PostId , '1' ) ;
            }
        }

        public static function final_payment( $payment_id , $order_id , $final_status ) {
            update_post_meta( $order_id , '_rs_final_payment_plan' , 'yes' ) ;
        }

        public static function revise_redeemed_points_for_user( $order_id , $old_status , $new_status ) {
            if ( ! in_array( $new_status , get_option( 'rs_list_other_status_for_revise_redeem' ) ) )
                return ;

            if ( get_post_meta( $order_id , 'refund_gateway' , true ) == '1' )
                return ;

            if ( get_post_meta( $order_id , '_payment_method' , true ) != 'reward_gateway' )
                return ;

            $total_redeem = get_post_meta( $order_id , 'total_redeem_points_for_order_point_price' , true ) ;
            if ( empty( $total_redeem ) )
                return ;

            $OrderObj   = new WC_Order( $order_id ) ;
            $OrderObj   = srp_order_obj( $OrderObj ) ;
            $table_args = array(
                'user_id'           => $OrderObj[ 'order_userid' ] ,
                'pointstoinsert'    => $total_redeem ,
                'checkpoints'       => 'RVPFRPG' ,
                'totalearnedpoints' => $total_redeem ,
                'orderid'           => $order_id
                    ) ;
            self::insert_earning_points( $table_args ) ;
            self::record_the_points( $table_args ) ;
            update_post_meta( $order_id , 'refund_gateway' , 1 ) ;
            update_post_meta( $order_id , 'second_time_gateway' , 1 ) ;
            update_post_meta( $order_id , 'redeem_point_once' , 2 ) ;
        }

        public static function msg_for_page_and_post_comment( $content ) {

            global $wp_query ;
            /* If Conflict with other plugins . So Check for display inside the loop for Earning Notices  */
            if ( isset( $wp_query->in_the_loop ) && ! $wp_query->in_the_loop )
                return $content ;

            if ( get_option( 'rs_reward_action_activated' ) != 'yes' )
                return $content ;

            if ( ! is_home() && ! is_cart() && ! is_checkout() && ! is_product() && ! is_account_page() ) {
                self::message_for_page_comment( $content ) ;
                self::message_for_post_creation( $content ) ;
                self::message_for_post_comment( $content ) ;
            }
            return $content ;
        }

        /* Award Points for Page Comment */

        public static function message_for_page_comment( $content ) {
            if ( ! is_page() )
                return $content ;

            if ( get_option( 'rs_reward_for_comment_Page' ) != 'yes' )
                return $content ;

            if ( get_option( 'rs_reward_page_review' ) == '' )
                return $content ;

            if ( get_option( 'rs_show_hide_message_for_page_comment' ) == '2' )
                return $content ;

            $PageCommentPoints = round_off_type( get_option( 'rs_reward_page_review' ) ) ;
            $PageCommentPoints = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $PageCommentPoints ) ;
            if ( empty( $PageCommentPoints ) )
                return $content ;

            $ReplacedMessage = str_replace( '[rspagecommentpoints]' , $PageCommentPoints , get_option( 'rs_message_user_points_for_page_comment' ) ) ;
            ?>
            <div class="woocommerce-info"><?php echo $ReplacedMessage ; ?></div>
            <?php
        }

        /* Award Points for Post Creation */

        public static function message_for_post_creation( $content ) {
            if ( is_page() )
                return $content ;

            $CheckIfPost = is_single() ? "post" : "" ;
            if ( $CheckIfPost != 'post' )
                return $content ;

            if ( get_option( 'rs_reward_for_Creating_Post' ) != 'yes' )
                return $content ;

            if ( get_option( 'rs_reward_post' ) == '' )
                return $content ;

            if ( get_option( 'rs_show_hide_message_for_blog_create' ) == '2' )
                return $content ;

            $PostCreationPoints = round_off_type( get_option( 'rs_reward_post' ) ) ;
            $PostCreationBased  = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $PostCreationPoints ) ;
            if ( empty( $PostCreationBased ) )
                return $content ;

            $ReplacedMessage = str_replace( '[rspostcreationpoints]' , $PostCreationBased , get_option( 'rs_message_user_points_for_blog_creation' ) ) ;
            ?>
            <div class="woocommerce-info"><?php echo $ReplacedMessage ; ?></div>
            <?php
        }

        /* Award Points for Post Comments */

        public static function message_for_post_comment( $content ) {
            if ( ! is_home() && ! is_cart() && ! is_checkout() && ! is_product() && ! is_account_page() ) {
                if ( ! is_page() ) {
                    $CheckIfPost = is_single() ? "post" : "" ;
                    if ( $CheckIfPost != 'post' )
                        return $content ;

                    if ( get_option( 'rs_reward_for_comment_Post' ) != 'yes' )
                        return $content ;

                    if ( get_option( 'rs_reward_post_review' ) == '' )
                        return $content ;

                    if ( get_option( 'rs_show_hide_message_for_post_comment' ) == '2' )
                        return $content ;

                    $PostCommentPoints = round_off_type( get_option( 'rs_reward_post_review' ) ) ;
                    $PostCommentPoints = RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $PostCommentPoints ) ;
                    if ( empty( $PostCommentPoints ) )
                        return $content ;

                    $ReplacedMessage = str_replace( '[rspostpoints]' , $PostCommentPoints , get_option( 'rs_message_user_points_for_blog_comment' ) ) ;
                    ?>
                    <div class="woocommerce-info"><?php echo $ReplacedMessage ; ?></div>
                    <?php
                }
            }
        }

        public static function point_info_for_manual_order( $order_id , $post ) {
            if ( get_post_meta( $order_id , 'frontendorder' , true ) == '1' )
                return ;

            $DiscountAmnt    = array() ;
            $TotalPointValue = array() ;
            $LineTotal       = array() ;
            $order           = new WC_Order( $order_id ) ;
            $OrderObj        = srp_order_obj( $order ) ;
            $UserId          = $OrderObj[ 'order_userid' ] ;

            if ( ! $UserId )
                return ;

            foreach ( $order->get_items()as $item ) {
                $ProductId       = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                $PointPriceValue = calculate_point_price_for_products( $ProductId ) ;
                if ( ! empty( $PointPriceValue[ $ProductId ] ) ) {
                    $TotalPointValue[] = $PointPriceValue[ $ProductId ] * $item[ 'qty' ] ;
                } else {
                    $LineTotal[] = $item[ 'line_subtotal' ] ;
                }
            }
            $TaxTotal       = (get_option( 'woocommerce_prices_include_tax' ) === 'yes') ? $order->get_total_tax() : 0 ;
            $TotalPrice     = $TaxTotal + $order->get_total_shipping() + array_sum( $LineTotal ) ;
            $ConvertedValue = redeem_point_conversion( $TotalPrice , $UserId ) ;
            $AppliedCoupons = $order->get_items( array( 'coupon' ) ) ;

            $UserInfo   = get_user_by( 'id' , $UserId ) ;
            $Username   = $UserInfo->user_login ;
            $AutoRedeem = 'auto_redeem_' . strtolower( $Username ) ;
            $Redeem     = 'sumo_' . strtolower( $Username ) ;
            foreach ( $AppliedCoupons as $coupon ) {
                if ( $AutoRedeem == $coupon[ 'name' ] || $Redeem == $coupon[ 'name' ] ) {
                    $DiscountAmnt[] = $coupon[ 'discount_amount' ] ;
                }
            }
            $redeemedpoints = (array_sum( $TotalPointValue ) + $ConvertedValue) - array_sum( $DiscountAmnt ) ;
            $ordertotal     = $order->get_total() ;
            foreach ( $order->get_items() as $item ) {
                $productid            = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                $args                 = array(
                    'productid'   => $item[ 'product_id' ] ,
                    'variationid' => $item[ 'variation_id' ] ,
                    'item'        => $item ,
                        ) ;
                $Points[ $productid ] = check_level_of_enable_reward_point( $args ) ;
            }
            update_post_meta( $order_id , 'points_for_current_order' , $Points ) ;
            update_post_meta( $order_id , 'rs_points_for_current_order_as_value' , array_sum( $Points ) ) ;
            if ( get_option( 'rs_gateway_for_manual_order' ) == 2 )
                return ;

            if ( $ordertotal < get_option( 'rs_max_redeem_discount_for_sumo_reward_points' ) )
                return ;

            if ( get_post_meta( $order_id , '_payment_method' , true ) != 'reward_gateway' )
                return ;

            update_post_meta( $order_id , 'total_redeem_points_for_order_point_price' , $redeemedpoints ) ;
            if ( get_post_meta( $order_id , 'manuall_order' , true ) == '1' )
                return ;

            self::perform_calculation_with_expiry( $redeemedpoints , $UserId ) ;
            $PointsData  = new RS_Points_Data( $UserId ) ;
            $totalpoints = $PointsData->total_available_points() ;
            if ( $totalpoints >= 0 && $totalpoints >= $redeemedpoints ) {
                $table_args = array(
                    'user_id'     => $UserId ,
                    'usedpoints'  => $redeemedpoints ,
                    'date'        => '999999999999' ,
                    'checkpoints' => 'RPFGW' ,
                    'orderid'     => $order_id
                        ) ;
                self::record_the_points( $table_args ) ;
                update_post_meta( $order_id , 'manuall_order' , 1 ) ;
                update_post_meta( $order_id , 'refund_gateway' , 2 ) ;
                update_post_meta( $order_id , 'second_time_gateway' , 2 ) ;
            }
        }

        public static function redeem_points_for_reward_gateway( $order_id ) {
            $order      = new WC_Order( $order_id ) ;
            $ordertotal = $order->get_total() ;
            if ( $ordertotal < get_option( 'rs_max_redeem_discount_for_sumo_reward_points' ) )
                return ;

            if ( get_post_meta( $order_id , '_payment_method' , true ) != 'reward_gateway' )
                return ;

            if ( get_post_meta( $order_id , 'sumo_gateway_used' , true ) == '1' )
                return ;

            $OrderObj    = srp_order_obj( $order ) ;
            $UserId      = $OrderObj[ 'order_userid' ] ;
            $PointsData  = new RS_Points_Data( $UserId ) ;
            $totalpoints = $PointsData->total_available_points() ;
            if ( $totalpoints < 0 )
                return ;

            $total_redeem = get_post_meta( $order_id , 'total_redeem_points_for_order_point_price' , true ) ;
            self::perform_calculation_with_expiry( $total_redeem , $UserId ) ;
            $table_args   = array(
                'user_id'     => $UserId ,
                'usedpoints'  => $total_redeem ,
                'date'        => '999999999999' ,
                'checkpoints' => 'RPFGW' ,
                'orderid'     => $order_id
                    ) ;
            self::record_the_points( $table_args ) ;
            do_action( 'fp_redeem_reward_points_using_rewardgateway' , $order_id , $total_redeem ) ;
            update_post_meta( $order_id , 'sumo_gateway_used' , 1 ) ;
            update_post_meta( $order_id , 'redeem_point_once' , 1 ) ;
        }

        /* Award Buying Points for User */

        public static function insert_buying_points_for_user( $order_id ) {
            if ( get_option( 'rs_buyingpoints_activated' ) != 'yes' )
                return ;

            $order = new WC_Order( $order_id ) ;
            foreach ( $order->get_items() as $item ) {
                $ProductObj = srp_product_object( $item[ 'product_id' ] ) ;
                $ProductId  = empty( $item[ 'variation_id' ] ) ? $item[ 'product_id' ] : $item[ 'variation_id' ] ;
                if ( get_post_meta( $ProductId , '_rewardsystem_buying_reward_points' , true ) != 'yes' && get_post_meta( $ProductId , '_rewardsystem_buying_reward_points' , true ) != 1 )
                    continue ;

                $BuyingPoints = get_post_meta( $ProductId , '_rewardsystem_assign_buying_points' , true ) ;
                if ( empty( $BuyingPoints ) )
                    continue ;

                $BuyingPoints = ( float ) $BuyingPoints * $item[ 'qty' ] ;
                $orderobj     = srp_order_obj( $order ) ;
                $new_obj      = new RewardPointsOrder( $order_id , 'no' ) ;
                if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                    $new_obj->check_point_restriction( $BuyingPoints , 0 , 'RPBSRP' , $orderobj[ 'order_userid' ] , '' , '' , $item[ 'product_id' ] , $item[ 'variation_id' ] , '' ) ;
                } else {
                    $valuestoinsert = array( 'pointstoinsert' => $BuyingPoints , 'event_slug' => 'RPBSRP' , 'user_id' => $orderobj[ 'order_userid' ] , 'product_id' => $item[ 'product_id' ] , 'variation_id' => $item[ 'variation_id' ] , 'totalearnedpoints' => $BuyingPoints ) ;
                    $new_obj->total_points_management( $valuestoinsert ) ;
                }
                do_action( 'fp_reward_point_for_buying_sumo_reward_points' , $item[ 'product_id' ] , $BuyingPoints ) ;
            }
        }

        /* Redeem Points for User */

        public static function update_redeem_point_for_user( $order_id ) {
            if ( get_post_meta( $order_id , 'redeem_point_once' , true ) == 1 )
                return ;

            $Order                 = wc_get_order( $order_id ) ;
            $OrderObj              = srp_order_obj( $Order ) ;
            $Orderstatus           = $OrderObj[ 'order_status' ] ;
            $Orderstatus           = str_replace( 'wc-' , '' , $Orderstatus ) ;
            $selected_order_status = get_option( 'rs_order_status_control_redeem' ) ;
            if ( in_array( $Orderstatus , $selected_order_status ) ) {
                $UserId      = $OrderObj[ 'order_userid' ] ;
                $PointsData  = new RS_Points_Data( $UserId ) ;
                $totalpoints = $PointsData->total_available_points() ;
                if ( get_post_meta( $order_id , '_payment_method' , true ) == 'reward_gateway' ) {
                    $total_redeem = get_post_meta( $order_id , 'total_redeem_points_for_order_point_price' , true ) ;
                    if ( get_post_meta( $order_id , 'second_time_gateway' , true ) == '1' ) {
                        self::perform_calculation_with_expiry( $total_redeem , $UserId ) ;
                        $table_args = array(
                            'user_id'     => $UserId ,
                            'usedpoints'  => $total_redeem ,
                            'date'        => '999999999999' ,
                            'checkpoints' => 'RPFGW' ,
                            'orderid'     => $order_id
                                ) ;
                        self::record_the_points( $table_args ) ;
                        update_post_meta( $order_id , 'refund_gateway' , 2 ) ;
                    }
                }
                $redeempoints = self::get_redeem_points_and_send_sms_when_redeem( $order_id , $UserId ) ;
                if ( $redeempoints != 0 ) {
                    $pointsredeemed = self::perform_calculation_with_expiry( $redeempoints , $UserId ) ;
                    $UserInfo       = get_user_by( 'id' , $UserId ) ;
                    $UserName       = $UserInfo->user_login ;
                    $AutoRedeem     = 'auto_redeem_' . strtolower( $UserName ) ;
                    $Redeem         = 'sumo_' . strtolower( $UserName ) ;
                    if ( $totalpoints >= 0 ) {
                        $table_args = array(
                            'user_id'     => $UserId ,
                            'usedpoints'  => $redeempoints ,
                            'date'        => '999999999999' ,
                            'checkpoints' => 'RP' ,
                            'orderid'     => $order_id
                                ) ;
                        self::record_the_points( $table_args ) ;

                        $used_coupons = ( float ) WC()->version < ( float ) ('3.7') ? $Order->get_used_coupons() : $Order->get_coupon_codes() ;
                        if ( in_array( $Redeem , $used_coupons ) ) {
                            do_action( 'fp_redeem_reward_points_manually' , $order_id , $pointsredeemed ) ;

                            if ( get_option( 'rs_email_activated' ) == 'yes' )
                                send_mail_for_product_purchase( $UserId , $order_id ) ;
                        }

                        if ( in_array( $AutoRedeem , $used_coupons ) ) {
                            do_action( 'fp_redeem_reward_points_automatically' , $order_id , $pointsredeemed ) ;

                            if ( get_option( 'rs_email_activated' ) == 'yes' )
                                send_mail_for_product_purchase( $UserId , $order_id ) ;
                        }

                        update_post_meta( $order_id , 'redeem_point_once' , 1 ) ;
                    }
                }

                update_post_meta( $order_id , 'second_time_gateway' , 1 ) ;
            }
        }

        /* Redeem Point when user use third party payment gateways like PayPal */

        public static function update_redeem_point_for_user_third_party_sites( $order_id ) {
            if ( ! in_array( 'pending' , get_option( 'rs_order_status_control_redeem' ) ) )
                return ;

            if ( get_post_meta( $order_id , 'redeem_point_once' , true ) == 1 )
                return ;

            $Order        = new WC_Order( $order_id ) ;
            $OrderObj     = srp_order_obj( $Order ) ;
            $UserId       = $OrderObj[ 'order_userid' ] ;
            $redeempoints = self::get_redeem_points_and_send_sms_when_redeem( $order_id , $UserId ) ;
            if ( empty( $redeempoints ) )
                return ;

            self::perform_calculation_with_expiry( $redeempoints , $UserId ) ;
            $table_args = array(
                'user_id'     => $UserId ,
                'usedpoints'  => $redeempoints ,
                'date'        => '999999999999' ,
                'checkpoints' => 'RP' ,
                'orderid'     => $order_id
                    ) ;
            self::record_the_points( $table_args ) ;
            update_post_meta( $order_id , 'redeem_point_once' , 1 ) ;
        }

        public static function get_redeem_points_and_send_sms_when_redeem( $OrderId , $UserId ) {
            if ( empty( $UserId ) )
                return ;

            $OrderObj       = new WC_Order( $OrderId ) ;
            $AppliedCoupons = $OrderObj->get_items( array( 'coupon' ) ) ;
            if ( ! srp_check_is_array( $AppliedCoupons ) )
                return ;

            $UserInfo   = get_user_by( 'id' , $UserId ) ;
            $UserName   = $UserInfo->user_login ;
            $Redeem     = 'sumo_' . strtolower( $UserName ) ;
            $AutoRedeem = 'auto_redeem_' . strtolower( $UserName ) ;
            foreach ( $AppliedCoupons as $value ) {
                if ( $Redeem == $value[ 'name' ] || $AutoRedeem == $value[ 'name' ] ) {
                    if ( get_option( 'rewardsystem_looped_over_coupon' . $OrderId ) == '1' )
                        continue ;

                    $CouponIds    = ($AutoRedeem == $value[ 'name' ]) ? get_user_meta( $UserId , 'auto_redeemcoupon_ids' , true ) : get_user_meta( $UserId , 'redeemcouponids' , true ) ;
                    $DiscountAmnt = $value[ 'discount_amount' ] ;
                    if ( get_option( 'woocommerce_tax_display_cart' ) == 'incl' && get_option( 'woocommerce_prices_include_tax' ) == 'no' ) {
                        $DiscountAmnt = $value[ 'discount_amount' ] + $value[ 'discount_amount_tax' ] ;
                    } elseif ( get_option( 'woocommerce_tax_display_cart' ) == 'incl' && get_option( 'woocommerce_prices_include_tax' ) == 'yes' ) {
                        $DiscountAmnt = $value[ 'discount_amount' ] + $value[ 'discount_amount_tax' ] ;
                    } elseif ( get_option( 'woocommerce_tax_display_cart' ) == 'excl' && get_option( 'woocommerce_prices_include_tax' ) == 'yes' ) {
                        $DiscountAmnt = $value[ 'discount_amount' ] + $value[ 'discount_amount_tax' ] ;
                    }
                    $RedeemedPoints = redeem_point_conversion( $DiscountAmnt , $UserId ) ;
                    if ( get_option( 'rs_sms_activated' ) == 'yes' && get_option( 'rs_enable_send_sms_to_user' ) == 'yes' ) {
                        if ( get_option( 'rs_send_sms_redeeming_points' ) == 'yes' ) {
                            $PhoneNumber = ! empty( get_user_meta( $UserId , 'rs_phone_number_value_from_signup' , true ) ) ? get_user_meta( $UserId , 'rs_phone_number_value_from_signup' , true ) : get_user_meta( $UserId , 'rs_phone_number_value_from_account_details' , true ) ;
                            $PhoneNumber = ! empty( $PhoneNumber ) ? $PhoneNumber : get_user_meta( $UserId , 'billing_phone' , true ) ;
                            if ( get_option( 'rs_sms_sending_api_option' ) == '1' ) {
                                RSFunctionForSms::send_sms_twilio_api( $OrderId , 'redeeming' , $RedeemedPoints , $PhoneNumber ) ;
                            } elseif ( get_option( 'rs_sms_sending_api_option' ) == '2' ) {
                                RSFunctionForSms::send_sms_nexmo_api( $OrderId , 'redeeming' , $RedeemedPoints , $PhoneNumber ) ;
                            }
                        }
                    }
                    update_option( 'rewardsystem_looped_over_coupon' . $OrderId , '1' ) ;
                    return $RedeemedPoints ;
                }
            }
        }

        public static function update_revised_redeem_points_for_user( $order_id ) {
            if ( get_post_meta( $order_id , 'redeem_point_once' , true ) == 2 )
                return ;

            $Order        = new WC_Order( $order_id ) ;
            $OrderObj     = srp_order_obj( $Order ) ;
            $UserId       = $OrderObj[ 'order_userid' ] ;
            $redeempoints = self::update_revised_reward_points_to_user( $order_id , $UserId ) ;
            if ( empty( $redeempoints ) )
                return ;

            $table_args = array(
                'user_id'           => $UserId ,
                'pointstoinsert'    => $redeempoints ,
                'checkpoints'       => 'RVPFRP' ,
                'totalearnedpoints' => $redeempoints ,
                'orderid'           => $order_id ,
                    ) ;
            self::insert_earning_points( $table_args ) ;
            self::record_the_points( $table_args ) ;
            update_post_meta( $order_id , 'redeem_point_once' , 2 ) ;
        }

        public static function signup_points_after_purchase( $order_id ) {
            $order    = new WC_Order( $order_id ) ;
            $OrderObj = srp_order_obj( $order ) ;
            $UserId   = $OrderObj[ 'order_userid' ] ;
            if ( ! empty( $UserId ) ) {
                global $wpdb ;
                $order_ids   = $wpdb->get_results( $wpdb->prepare( "SELECT posts.ID
			FROM $wpdb->posts as posts
			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
			WHERE   meta.meta_key       = '_customer_user'
			AND     posts.post_type     IN ('" . implode( "','" , wc_get_order_types( 'order-count' ) ) . "')
			AND     posts.post_status   IN ('" . implode( "','" , array_keys( wc_get_order_statuses() ) ) . "')
			AND     meta_value          = %d
		" , $UserId ) , ARRAY_A ) ;
                $order_count = count( $order_ids ) ;
                if ( get_option( 'rs_select_referral_points_award' ) == '1' ) {
                    if ( get_option( 'rs_referral_reward_signup_after_first_purchase' ) == 'yes' || get_option( 'rs_reward_signup_after_first_purchase' ) == 'yes' )
                        self::reward_points_after_first_purchase( $order_id ) ;
                }

                if ( get_option( 'rs_select_referral_points_award' ) == '2' ) {
                    if ( get_option( 'rs_number_of_order_for_referral_points' ) != '' ) {
                        if ( get_option( 'rs_number_of_order_for_referral_points' ) <= $order_count )
                            self::reward_points_after_first_purchase( $order_id ) ;
                    }
                }

                if ( get_option( 'rs_select_referral_points_award' ) == '3' ) {
                    if ( get_option( 'rs_amount_of_order_for_referral_points' ) != '' ) {
                        foreach ( $order_ids as $values ) {
                            $total[] = get_post_meta( $values[ 'ID' ] , '_order_total' , true ) ;
                        }
                        $order_total = array_sum( $total ) ;
                        if ( get_option( 'rs_amount_of_order_for_referral_points' ) <= $order_total ) {
                            self::reward_points_after_first_purchase( $order_id ) ;
                        }
                    }
                }
            }
            if ( get_option( 'rs_referral_reward_signup_getting_refer' ) == '1' && get_option( 'rs_referral_reward_getting_refer_after_first_purchase' ) == 'yes' )
                self::reward_points_after_first_purchase_get_refer( $order_id ) ;
        }

        public static function reward_points_after_first_purchase( $order_id ) {
            $Order    = new WC_Order( $order_id ) ;
            $OrderObj = srp_order_obj( $Order ) ;
            $UserId   = $OrderObj[ 'order_userid' ] ;
            if ( empty( $UserId ) )
                return ;

            if ( get_user_meta( $UserId , 'rs_after_first_purchase' , true ) == 'yes' )
                return ;

            $fetchdata = get_user_meta( $UserId , 'srp_data_for_reg_points' , true ) ;
            if ( ! srp_check_is_array( $fetchdata ) )
                return ;

            $curregpoints   = isset( $fetchdata[ $UserId ][ 'points' ] ) ? $fetchdata[ $UserId ][ 'points' ] : 0 ;
            $refregpoints   = isset( $fetchdata[ $UserId ][ 'refpoints' ] ) ? $fetchdata[ $UserId ][ 'refpoints' ] : 0 ;
            $userid         = $fetchdata[ $UserId ][ 'userid' ] ;
            $refuserid      = $fetchdata[ $UserId ][ 'refuserid' ] ;
            $event_slug     = isset( $fetchdata[ $UserId ][ 'event_slug' ] ) ? $fetchdata[ $UserId ][ 'event_slug' ] : '' ;
            $reasonindetail = isset( $fetchdata[ $UserId ][ 'reaseonidetail' ] ) ? $fetchdata[ $UserId ][ 'reaseonidetail' ] : '' ;
            $checkredeeming = self::check_redeeming_in_order( $order_id , $UserId ) ;
            if ( get_user_meta( $userid , '_points_awarded' , true ) != '1' && get_option( 'rs_reward_action_activated' ) == 'yes' ) {
                if ( get_post_meta( $order_id , 'rs_check_enable_option_for_redeeming' , true ) == 'yes' && $checkredeeming == false ) {
                    $table_args = array(
                        'user_id'           => $UserId ,
                        'pointstoinsert'    => $curregpoints ,
                        'checkpoints'       => $event_slug ,
                        'totalearnedpoints' => $curregpoints ,
                        'orderid'           => $order_id ,
                        'reason'            => $reasonindetail ,
                            ) ;
                    self::insert_earning_points( $table_args ) ;
                    self::record_the_points( $table_args ) ;
                } else {
                    $table_args = array(
                        'user_id'           => $UserId ,
                        'pointstoinsert'    => $curregpoints ,
                        'checkpoints'       => $event_slug ,
                        'totalearnedpoints' => $curregpoints ,
                        'orderid'           => $order_id ,
                        'reason'            => $reasonindetail ,
                            ) ;
                    self::insert_earning_points( $table_args ) ;
                    self::record_the_points( $table_args ) ;
                }
                add_user_meta( $UserId , '_points_awarded' , '1' ) ;
            }

            if ( $refuserid ) {
                if ( get_user_meta( $UserId , 'rs_referrer_regpoints_awarded' , true ) != '1' && get_option( 'rs_referral_activated' ) == 'yes' ) {
                    $new_obj                     = new RewardPointsOrder( $order_id , $apply_previous_order_points = 'no' ) ;
                    if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                        $new_obj->check_point_restriction( $refregpoints , $pointsredeemed = 0 , $event_slug     = 'RRRP' , $refuserid , $nomineeid      = '' , $UserId , $productid      = '' , $variationid    = '' , $reasonindetail = '' ) ;
                    } else {
                        $valuestoinsert = array( 'pointstoinsert' => $refregpoints , 'event_slug' => 'RRRP' , 'user_id' => $refuserid , 'referred_id' => $UserId , 'totalearnedpoints' => $refregpoints ) ;
                        $new_obj->total_points_management( $valuestoinsert ) ;
                        $previouslog    = get_option( 'rs_referral_log' ) ;
                        RS_Referral_Log::update_referral_log( $refuserid , $UserId , $refregpoints , array_filter( ( array ) $previouslog ) ) ;
                        update_user_meta( $UserId , '_rs_i_referred_by' , $refuserid ) ;
                    }

                    do_action( 'fp_signup_points_for_referrer' , $refuserid , $UserId , $refregpoints ) ;

                    add_user_meta( $UserId , 'rs_referrer_regpoints_awarded' , '1' ) ;
                }
            }
            add_user_meta( $UserId , 'rs_after_first_purchase' , 'yes' ) ;
        }

        public static function reward_points_after_first_purchase_get_refer( $order_id ) {
            if ( get_option( 'rs_referral_activated' ) != 'yes' )
                return ;

            $Order    = new WC_Order( $order_id ) ;
            $OrderObj = srp_order_obj( $Order ) ;
            $UserId   = $OrderObj[ 'order_userid' ] ;
            if ( empty( $UserId ) )
                return ;

            if ( get_user_meta( $UserId , 'rs_after_first_purchase_get_refer' , true ) == 'yes' )
                return ;

            $fetchdata = get_user_meta( $UserId , 'srp_data_for_get_referred_reg_points' , true ) ;
            if ( ! srp_check_is_array( $fetchdata ) )
                return ;

            if ( get_user_meta( $UserId , '_points_awarded_get_refer' , true ) == '1' )
                return ;

            $refregpoints = $fetchdata[ $UserId ][ 'refpoints' ] ;
            $refuserid    = $fetchdata[ $UserId ][ 'userid' ] ;
            $new_obj      = new RewardPointsOrder( $order_id , 'no' ) ;
            if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                $new_obj->check_point_restriction( $refregpoints , 0 , 'RRPGR' , $UserId , '' , $refuserid , '' , '' , '' ) ;
            } else {
                $valuestoinsert = array( 'pointstoinsert' => $refregpoints , 'event_slug' => 'RRPGR' , 'user_id' => $UserId , 'referred_id' => $refuserid , 'totalearnedpoints' => $refregpoints ) ;
                $new_obj->total_points_management( $valuestoinsert ) ;
            }

            do_action( 'fp_signup_points_for_getting_referred' , $refuserid , $UserId , $refregpoints ) ;

            add_user_meta( $UserId , '_points_awarded_get_refer' , '1' ) ;
            add_user_meta( $UserId , 'rs_after_first_purchase_get_refer' , 'yes' ) ;
        }

        public static function check_if_expiry() {
            global $wpdb ;
            $table_name = $wpdb->prefix . 'rspointexpiry' ;
            $Data       = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE expirydate < %d and expirydate NOT IN(999999999999) and expiredpoints IN(0) and userid = %d" , time() , get_current_user_id() ) , ARRAY_A ) ;
            if ( ! srp_check_is_array( $Data ) )
                return ;

            foreach ( $Data as $key => $eacharray ) {
                $wpdb->update( $table_name , array( 'expiredpoints' => $eacharray[ 'earnedpoints' ] - $eacharray[ 'usedpoints' ] ) , array( 'id' => $eacharray[ 'id' ] ) ) ;
            }
            foreach ( WC()->cart->get_applied_coupons() as $coupon_code ) {
                $coupon        = new WC_Coupon( $coupon_code ) ;
                $coupon_obj    = srp_coupon_obj( $coupon ) ;
                $coupon_amount = $coupon_obj[ 'coupon_amount' ] ;
                if ( strpos( $coupon_code , 'sumo_' ) || strpos( $coupon_code , 'auto_redeem_' ) ) {
                    $coupon_remove_check = self::remove_sumo_coupon_after_points_expiry( $coupon_amount ) ;
                    if ( $coupon_remove_check )
                        WC()->cart->remove_coupon( $coupon_code ) ;
                }
            }
            send_mail_for_thershold_points() ;
        }

        public static function remove_sumo_coupon_after_points_expiry( $coupon_amount ) {
            $PointsData      = new RS_Points_Data( get_current_user_id() ) ;
            $Points          = $PointsData->total_available_points() ;
            $available_price = redeem_point_conversion( $Points , get_current_user_id() , 'price' ) ;
            return ( $available_price > $coupon_amount ) ? false : true ;
        }

        public static function delete_if_used() {
            global $wpdb ;
            $table_name = $wpdb->prefix . 'rspointexpiry' ;
            $userid     = get_current_user_id() ;
            $Data       = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE earnedpoints=usedpoints and expiredpoints IN(0) and userid = %d" , $userid ) , ARRAY_A ) ;

            if ( srp_check_is_array( $Data ) ) {

                $totalearnedpoints = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(earnedpoints) FROM $table_name WHERE earnedpoints=usedpoints and expiredpoints IN(0) and userid = %d" , $userid ) ) ;
                $totalusedpoints   = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(usedpoints) FROM $table_name WHERE earnedpoints=usedpoints and expiredpoints IN(0) and userid = %d" , $userid ) ) ;

                $earned_points_before_delete = array_sum( $totalearnedpoints ) + ( float ) get_user_meta( $userid , 'rs_earned_points_before_delete' , true ) ;
                $used_points_before_delete   = array_sum( $totalusedpoints ) + ( float ) get_user_meta( $userid , 'rs_redeem_points_before_delete' , true ) ;

                update_user_meta( $userid , 'rs_earned_points_before_delete' , $earned_points_before_delete ) ;
                update_user_meta( $userid , 'rs_redeem_points_before_delete' , $used_points_before_delete ) ;

                foreach ( $Data as $eacharray ) {
                    $wpdb->delete( $table_name , array( 'id' => $eacharray[ 'id' ] ) ) ;
                }
            }
        }

        public static function delete_if_expired() {
            global $wpdb ;
            $table_name = $wpdb->prefix . 'rspointexpiry' ;
            $userid     = get_current_user_id() ;
            $Data       = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE earnedpoints=(usedpoints+expiredpoints) and expiredpoints NOT IN(0) and userid = %d" , $userid ) , ARRAY_A ) ;

            if ( srp_check_is_array( $Data ) ) {

                $totalearnedpoints  = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(earnedpoints) FROM $table_name WHERE earnedpoints=(usedpoints+expiredpoints) and expiredpoints NOT IN(0) and userid = %d" , $userid ) ) ;
                $totalusedpoints    = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(usedpoints) FROM $table_name WHERE earnedpoints=(usedpoints+expiredpoints) and expiredpoints NOT IN(0) and userid = %d" , $userid ) ) ;
                $totalexpiredpoints = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(expiredpoints) FROM $table_name WHERE earnedpoints=(usedpoints+expiredpoints) and expiredpoints NOT IN(0) and userid = %d" , $userid ) ) ;

                $earned_points_before_delete  = array_sum( $totalearnedpoints ) + ( float ) get_user_meta( $userid , 'rs_earned_points_before_delete' , true ) ;
                $used_points_before_delete    = array_sum( $totalusedpoints ) + ( float ) get_user_meta( $userid , 'rs_redeem_points_before_delete' , true ) ;
                $expired_points_before_delete = array_sum( $totalexpiredpoints ) + ( float ) get_user_meta( $userid , 'rs_expired_points_before_delete' , true ) ;

                update_user_meta( $userid , 'rs_earned_points_before_delete' , $earned_points_before_delete ) ;
                update_user_meta( $userid , 'rs_redeem_points_before_delete' , $used_points_before_delete ) ;
                update_user_meta( $userid , 'rs_expired_points_before_delete' , $expired_points_before_delete ) ;

                foreach ( $Data as $eacharray ) {
                    $wpdb->delete( $table_name , array( 'id' => $eacharray[ 'id' ] ) ) ;
                }
            }
        }

        /* Get the Paypal ID or Custom Payment Details */

        public static function get_paypal_id_form_cashback_form( $userid ) {
            if ( empty( $userid ) )
                return ;

            global $wpdb ;
            $table_name = $wpdb->prefix . "sumo_reward_encashing_submitted_data" ;
            $table_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE userid=%d" , $userid ) , ARRAY_A ) ;
            foreach ( $table_data as $data ) {
                $data_to_return = ($data[ 'encashpaymentmethod' ] == 'encash_through_paypal_method') ? $data[ 'paypalemailid' ] : $data[ 'otherpaymentdetails' ] ;
            }
            return $data_to_return ;
        }

        /* Insert the Data based on Point Expiry */

        public static function insert_earning_points( $args = array() ) {
            $default_args = array(
                'pointstoinsert'    => 0 ,
                'usedpoints'        => 0 ,
                'date'              => expiry_date_for_points() ,
                'orderid'           => 0 ,
                'totalearnedpoints' => 0 ,
                'totalredeempoints' => 0 ,
                'reason'            => ''
                    ) ;
            $table_args   = wp_parse_args( $args , $default_args ) ;
            extract( $table_args ) ;
            if ( empty( $user_id ) )
                return ;

            global $wpdb ;
            $table_name    = $wpdb->prefix . "rspointexpiry" ;
            $earned_points = get_option( 'rs_enable_round_off_type_for_calculation' ) == 'yes' ? round_off_type( $pointstoinsert ) : $pointstoinsert ;
            $noofday       = get_option( 'rs_point_expiry_activated' ) == 'yes' ? get_option( 'rs_point_to_be_expire' ) : 0 ;
            if ( empty( $noofday ) ) {
                $query = $wpdb->get_row( "SELECT * FROM $table_name WHERE userid = $user_id and expirydate = '999999999999'" , ARRAY_A ) ;
                if ( ! empty( $query ) && $date == 999999999999 ) {
                    $oldearnedpoints = $query[ 'earnedpoints' ] + $earned_points ;
                    $usedpoints      = $usedpoints + $query[ 'usedpoints' ] ;
                    $wpdb->update( $table_name , array( 'earnedpoints' => $oldearnedpoints , 'usedpoints' => $usedpoints ) , array( 'id' => $query[ 'id' ] ) ) ;
                } else {
                    $wpdb->insert(
                            $table_name , array(
                        'earnedpoints'      => $earned_points ,
                        'usedpoints'        => $usedpoints ,
                        'expiredpoints'     => 0 ,
                        'userid'            => $user_id ,
                        'earneddate'        => time() ,
                        'expirydate'        => $date ,
                        'checkpoints'       => $checkpoints ,
                        'orderid'           => $orderid ,
                        'totalearnedpoints' => $totalearnedpoints ,
                        'totalredeempoints' => $totalredeempoints ,
                        'reasonindetail'    => $reason
                    ) ) ;
                }
            } else {
                $wpdb->insert(
                        $table_name , array(
                    'earnedpoints'      => $earned_points ,
                    'usedpoints'        => $usedpoints ,
                    'expiredpoints'     => '0' ,
                    'userid'            => $user_id ,
                    'earneddate'        => time() ,
                    'expirydate'        => $date ,
                    'checkpoints'       => $checkpoints ,
                    'orderid'           => $orderid ,
                    'totalearnedpoints' => $totalearnedpoints ,
                    'totalredeempoints' => $totalredeempoints ,
                    'reasonindetail'    => $reason
                ) ) ;
            }
        }

        public static function record_the_points( $args = array() ) {
            $default_args = array(
                'pointstoinsert'    => 0 ,
                'usedpoints'        => 0 ,
                'date'              => expiry_date_for_points() ,
                'orderid'           => 0 ,
                'totalearnedpoints' => 0 ,
                'totalredeempoints' => 0 ,
                'reason'            => '' ,
                'productid'         => '' ,
                'variationid'       => '' ,
                'refuserid'         => 0 ,
                'nomineeid'         => 0 ,
                'nomineepoints'     => 0
                    ) ;
            $table_args   = wp_parse_args( $args , $default_args ) ;
            extract( $table_args ) ;
            if ( empty( $user_id ) )
                return ;

            global $wpdb ;
            $PointsData    = new RS_Points_Data( $user_id ) ;
            $PointsData->reset( $user_id ) ;
            $Points        = $PointsData->total_available_points() ;
            $earned_points = get_option( 'rs_enable_round_off_type_for_calculation' ) == 'yes' ? round_off_type( $pointstoinsert ) : $pointstoinsert ;
            $wpdb->insert( $wpdb->prefix . "rsrecordpoints" , array(
                'earnedpoints'             => $earned_points ,
                'redeempoints'             => $usedpoints ,
                'userid'                   => $user_id ,
                'earneddate'               => time() ,
                'expirydate'               => $date ,
                'checkpoints'              => $checkpoints ,
                'earnedequauivalentamount' => earn_point_conversion( $earned_points ) ,
                'redeemequauivalentamount' => redeem_point_conversion( $usedpoints , $user_id , 'price' ) ,
                'productid'                => $productid ,
                'variationid'              => $variationid ,
                'orderid'                  => $orderid ,
                'refuserid'                => $refuserid ,
                'reasonindetail'           => $reason ,
                'totalpoints'              => $Points ,
                'showmasterlog'            => false ,
                'showuserlog'              => false ,
                'nomineeid'                => $nomineeid ,
                'nomineepoints'            => $nomineepoints
            ) ) ;

            if ( $checkpoints == 'RRP' || $checkpoints == 'RRPGR' ) {
                $to        = get_user_by( 'id' , $user_id )->user_email ;
                $user_name = get_user_by( 'id' , $user_id )->user_login ;
                rs_send_mail_for_actions( $to , $checkpoints , $earned_points , $user_name ) ;
            }

            // Added action in V24.8.2.
            do_action( 'fp_reward_points_after_recorded' , $user_id , $earned_points , $usedpoints ) ;
        }

        public static function perform_calculation_with_expiry( $redeempoints , $UserId ) {
            if ( empty( $UserId ) )
                return $redeempoints ;

            global $wpdb ;
            $table_name = $wpdb->prefix . 'rspointexpiry' ;
            $Data       = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE earnedpoints-usedpoints NOT IN(0) and  expiredpoints IN(0) and userid=%d ORDER BY expirydate ASC" , $UserId ) , ARRAY_A ) ;
            if ( ! srp_check_is_array( $Data ) )
                return $redeempoints ;

            foreach ( $Data as $key => $eachrow ) {
                $BalancePoints = $eachrow[ 'earnedpoints' ] - $eachrow[ 'usedpoints' ] ;
                if ( $redeempoints >= $BalancePoints ) {
                    $usedpoints   = $eachrow[ 'usedpoints' ] + $BalancePoints ;
                    $id           = $eachrow[ 'id' ] ;
                    $redeempoints = $redeempoints - $BalancePoints ;

                    $wpdb->query( "UPDATE $table_name SET usedpoints = $usedpoints WHERE id = $id" ) ;
                    if ( empty( $redeempoints ) ) {
                        break ;
                    }
                } else {
                    $usedpoints = $eachrow[ 'usedpoints' ] + $redeempoints ;
                    $id         = $eachrow[ 'id' ] ;
                    $wpdb->query( "UPDATE $table_name SET usedpoints = $usedpoints  WHERE id = $id" ) ;
                    break ;
                }
            }
            return $redeempoints ;
        }

        public static function update_revised_points_for_user( $order_id ) {
            if ( get_post_meta( $order_id , 'reward_points_awarded' , true ) != 'yes' )
                return ;

            $new_obj = new RewardPointsOrder( $order_id , 'no' ) ;
            if ( $new_obj->check_redeeming_in_order() )
                return ;

            $Order                 = new WC_Order( $order_id ) ;
            $OrderObj              = srp_order_obj( $Order ) ;
            $orderuserid           = $OrderObj[ 'order_userid' ] ;
            $Orderstatus           = $OrderObj[ 'order_status' ] ;
            $Orderstatus           = str_replace( 'wc-' , '' , $Orderstatus ) ;
            $selected_order_status = get_option( 'rs_order_status_control' ) ;
            if ( in_array( $Orderstatus , $selected_order_status ) )
                return ;

            if ( get_post_meta( $order_id , 'srp_gateway_points_awarded' , true ) == 'yes' ) {
                $getpaymentgatewayused = points_for_payment_gateways( $order_id , $orderuserid , $OrderObj[ 'payment_method' ] ) ;
                $getpaymentgatewayused = RSMemberFunction::earn_points_percentage( $orderuserid , ( float ) $getpaymentgatewayused ) ;
                if ( ! empty( $getpaymentgatewayused ) ) {
                    $valuestoinsert = array( 'pointsredeemed' => $getpaymentgatewayused , 'event_slug' => 'RVPFRPG' , 'user_id' => $orderuserid , 'totalredeempoints' => $getpaymentgatewayused ) ;
                    $new_obj->total_points_management( $valuestoinsert ) ;
                }
            }
            if ( get_post_meta( $order_id , 'rs_revised_points_once' , true ) != '1' ) {
                if ( get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'yes' ) {
                    $product_ids = get_post_meta( $order_id , 'points_for_current_order' , true ) ;
                    self::insert_revised_points( $orderuserid , $order_id , $product_ids ) ;
                } else {
                    if ( get_option( 'rs_award_points_for_cart_or_product_total' ) == '1' ) {
                        $product_ids = get_post_meta( $order_id , 'points_for_current_order' , true ) ;
                        self::insert_revised_points( $orderuserid , $order_id , $product_ids ) ;
                    } else {
                        self::insert_revised_points_based_on_carttotal( $orderuserid , $order_id ) ;
                    }
                }
                $referreduser = get_post_meta( $order_id , '_referrer_name' , true ) ;
                if ( $referreduser != '' ) {
                    $product_ids  = get_post_meta( $order_id , 'rsgetreferalpoints' , true ) ;
                    self::insert_revised_get_refer_points( $redeempoints = 0 , $orderuserid , $order_id , $product_ids ) ;
                    self::insert_revised_referral_points( $redeempoints = 0 , $referreduser , $orderuserid , $order_id , $new_obj , $Order ) ;
                }
                update_post_meta( $order_id , 'rs_revised_points_once' , 1 ) ;
            }
            update_post_meta( $order_id , 'earning_point_once' , 2 ) ;
        }

        public static function update_revised_reward_points_to_user( $order_id , $orderuserid ) {
            // Inside Loop
            $Order          = new WC_Order( $order_id ) ;
            $AppliedCoupons = $Order->get_items( array( 'coupon' ) ) ;
            if ( ! srp_check_is_array( $AppliedCoupons ) )
                return 0 ;

            $UserInfo   = get_user_by( 'id' , $orderuserid ) ;
            $UserName   = $UserInfo->user_login ;
            $Redeem     = 'sumo_' . strtolower( $UserName ) ;
            $AutoRedeem = 'auto_redeem_' . strtolower( $UserName ) ;
            foreach ( $AppliedCoupons as $couponcode => $value ) {
                if ( $Redeem == $value[ 'name' ] || $AutoRedeem == $value[ 'name' ] ) {
                    if ( get_option( 'rs_revise_redeem_points_occur_once' . $order_id ) != '1' ) {
                        $getcouponid   = get_user_meta( $orderuserid , 'redeemcouponids' , true ) ;
                        $currentamount = get_post_meta( $getcouponid , 'coupon_amount' , true ) ;
                        $tax_value     = ('yes' == get_option( 'woocommerce_prices_include_tax' ) && isset( $value[ 'discount_tax' ] )) ? $value[ 'discount_tax' ] : 0 ;
                        $discount_amnt = $value[ 'discount_amount' ] + $tax_value ;
                        if ( $currentamount < $value[ 'discount_amount' ] )
                            continue ;

                        $redeemedpoints = redeem_point_conversion( $discount_amnt , $orderuserid ) ;

                        update_option( 'rs_revise_redeem_points_occur_once' . $order_id , '1' ) ;

                        return $redeemedpoints ;
                    }
                }
            }
        }

        public static function insert_revised_get_refer_points( $pointstoearn , $orderuserid , $order_id , $product_ids ) {
            if ( ! empty( $product_ids ) ) {
                foreach ( $product_ids as $key => $value ) {
                    $used_points = RSMemberFunction::earn_points_percentage( $orderuserid , ( float ) $value ) ;
                    if ( ! $used_points ) {
                        continue ;
                    }

                    $table_args = array(
                        'user_id'           => $orderuserid ,
                        'pointstoinsert'    => $pointstoearn ,
                        'usedpoints'        => $used_points ,
                        'productid'         => $key ,
                        'variationid'       => $key ,
                        'checkpoints'       => 'RVPPRRPG' ,
                        'totalearnedpoints' => $pointstoearn ,
                        'orderid'           => $order_id ,
                            ) ;
                    self::insert_earning_points( $table_args ) ;
                    self::record_the_points( $table_args ) ;
                }
            }
        }

        public static function insert_revised_points( $orderuserid , $order_id , $points_data ) {
            global $wpdb ;
            $table_name = $wpdb->prefix . "rsrecordpoints" ;

            $restrictuserpoints = get_option( 'rs_max_earning_points_for_user' ) ;
            if ( ! empty( $restrictuserpoints ) ) {
                $points_to_revise = $wpdb->get_results( "SELECT SUM(earnedpoints) as earnedpoints FROM $table_name WHERE userid = $orderuserid and orderid = $order_id and expirydate = '999999999999' and expirydate NOT IN(0) and checkpoints IN('MREPFU','PPRP')" , ARRAY_A ) ;
                if ( ! empty( $points_to_revise[ 0 ][ 'earnedpoints' ] ) ) {
                    self::revise_product_purchase_points( $points_to_revise[ 0 ][ 'earnedpoints' ] , $points_data , $orderuserid , $order_id ) ;
                }
            } else if ( ! empty( $points_data ) ) {
                self::revise_product_purchase_points( false , $points_data , $orderuserid , $order_id ) ;
            }

            $buy_points = get_post_meta( $order_id , 'buy_points_for_current_order' , true ) ;
            if ( ! empty( $buy_points ) ) {
                foreach ( $buy_points as $buy_id => $value ) {
                    $table_args = array(
                        'user_id'     => $orderuserid ,
                        'usedpoints'  => RSMemberFunction::earn_points_percentage( $orderuserid , ( float ) $value ) ,
                        'productid'   => $buy_id ,
                        'variationid' => $buy_id ,
                        'checkpoints' => 'RVPFBPRP' ,
                        'orderid'     => $order_id ,
                            ) ;
                    self::insert_earning_points( $table_args ) ;
                    self::record_the_points( $table_args ) ;
                }
            }
            $first_purchase = get_post_meta( $order_id , 'rs_first_purchase_points' , true ) ;
            if ( ! empty( $first_purchase ) ) {
                $table_args = array(
                    'user_id'     => $orderuserid ,
                    'usedpoints'  => $first_purchase ,
                    'checkpoints' => 'RPFFP' ,
                    'orderid'     => $order_id ,
                        ) ;
                self::insert_earning_points( $table_args ) ;
                self::record_the_points( $table_args ) ;
            }
        }

        public static function revise_product_purchase_points( $points_to_revise = false , $points_data , $orderuserid , $order_id ) {

            $table_args = array(
                'user_id'     => $orderuserid ,
                'checkpoints' => 'RVPFPPRP' ,
                'orderid'     => $order_id ,
                    ) ;

            if ( $points_to_revise ) {
                $table_args[ 'usedpoints' ] = RSMemberFunction::earn_points_percentage( $orderuserid , ( float ) $points_to_revise ) ;
                self::insert_earning_points( $table_args ) ;
                self::record_the_points( $table_args ) ;
            } else {
                foreach ( $points_data as $product_id => $value ) {

                    $table_args[ 'usedpoints' ]  = RSMemberFunction::earn_points_percentage( $orderuserid , ( float ) $value ) ;
                    $table_args[ 'productid' ]   = $product_id ;
                    $table_args[ 'variationid' ] = $product_id ;

                    self::insert_earning_points( $table_args ) ;
                    self::record_the_points( $table_args ) ;
                }
            }
        }

        public static function insert_revised_points_based_on_carttotal( $orderuserid , $OrderId ) {
            $buy_points = get_post_meta( $OrderId , 'buy_points_for_current_order' , true ) ;
            if ( ! empty( $buy_points ) ) {
                foreach ( $buy_points as $key => $value ) {
                    $table_args = array(
                        'user_id'     => $orderuserid ,
                        'usedpoints'  => RSMemberFunction::earn_points_percentage( $orderuserid , ( float ) $value ) ,
                        'productid'   => $key ,
                        'variationid' => $key ,
                        'checkpoints' => 'RVPFBPRP' ,
                        'orderid'     => $OrderId ,
                            ) ;
                    self::insert_earning_points( $table_args ) ;
                    self::record_the_points( $table_args ) ;
                }
            }

            $first_purchase = get_post_meta( $OrderId , 'rs_first_purchase_points' , true ) ;
            if ( ! empty( $first_purchase ) ) {
                $table_args = array(
                    'user_id'     => $orderuserid ,
                    'usedpoints'  => $first_purchase ,
                    'checkpoints' => 'RPFFP' ,
                    'orderid'     => $OrderId ,
                        ) ;
                self::insert_earning_points( $table_args ) ;
                self::record_the_points( $table_args ) ;
            }

            global $wpdb ;
            $table_name = $wpdb->prefix . "rsrecordpoints" ;

            $PointsToRevise = array() ;
            if ( ! empty( get_option( 'rs_max_earning_points_for_user' ) ) ) {
                $PointsToRevise = $wpdb->get_results( "SELECT SUM(earnedpoints) as earnedpoints FROM $table_name WHERE userid = $orderuserid and orderid = $OrderId and expirydate = '999999999999' and expirydate NOT IN(0) and checkpoints IN('MREPFU','PPRP')" , ARRAY_A ) ;
                $PointsToRevise = ! empty( $PointsToRevise[ 0 ][ 'earnedpoints' ] ) ? $PointsToRevise[ 0 ][ 'earnedpoints' ] : 0 ;
            } else {
                $PointsToRevise = get_post_meta( $OrderId , 'points_for_current_order_based_on_cart_total' , true ) ;
            }

            if ( empty( $PointsToRevise ) ) {
                return ;
            }

            $table_args = array(
                'user_id'     => $orderuserid ,
                'usedpoints'  => $PointsToRevise ,
                'checkpoints' => 'RVPFPPRPBCT' ,
                'orderid'     => $OrderId ,
                    ) ;
            self::insert_earning_points( $table_args ) ;
            self::record_the_points( $table_args ) ;
        }

        public static function insert_revised_referral_points( $pointsredeemed , $referreduser , $orderuserid , $order_id , $new_obj , $order ) {
            $refuser = get_user_by( 'login' , $referreduser ) ;
            $myid    = $refuser ? $refuser->ID : $referreduser ;
            foreach ( $order->get_items() as $item ) {
                $productid   = $item[ 'product_id' ] ;
                $variationid = empty( $item[ 'variation_id' ] ) ? 0 : $item[ 'variation_id' ] ;
                $args        = array(
                    'productid'     => $item[ 'product_id' ] ,
                    'variationid'   => empty( $item[ 'variation_id' ] ) ? 0 : $item[ 'variation_id' ] ,
                    'item'          => $item ,
                    'referred_user' => $myid ,
                        ) ;
                if ( 'yes' === get_option( 'rs_referral_points_after_discounts' ) ) {
                    $item_product_id        = 'variable' == wc_get_product( $item[ 'product_id' ] )->get_type() ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                    $points_after_discounts = get_post_meta( $order_id , 'rs_referrer_points_after_discounts' , true ) ;
                    $pointstoinsert         = isset( $points_after_discounts[ $item_product_id ] ) ? $points_after_discounts[ $item_product_id ] : 0 ;
                } else {
                    $pointstoinsert = check_level_of_enable_reward_point( $args ) ;
                }

                if ( $pointstoinsert ) {
                    $valuestoinsert = array( 'pointsredeemed' => $pointstoinsert , 'event_slug' => 'RVPFPPRRP' , 'user_id' => $myid , 'referred_id' => $orderuserid , 'product_id' => $productid , 'variation_id' => $variationid , 'totalredeempoints' => $pointstoinsert ) ;
                    $new_obj->total_points_management( $valuestoinsert ) ;
                }
            }
        }

        /*
         * @ updates earning points for user in db
         *
         */

        public static function update_earning_points_for_user( $OrderObj ) {
            $order_id = is_object( $OrderObj ) ? $OrderObj->get_id() : $OrderObj ;
            if ( get_option( 'rs_restrict_days_for_product_purchase' ) == 'no' ) {
                award_points_for_product_purchase_based_on_cron( $order_id ) ;
            } else {
                update_post_meta( $order_id , 'rs_order_status_reached' , 'yes' ) ;
                $interval = get_option( 'rs_restrict_product_purchase_time' ) ;
                if ( get_option( 'rs_restrict_product_purchase_cron_type' ) == 'minutes' ) {
                    $interval = $interval * 60 ;
                } else if ( get_option( 'rs_restrict_product_purchase_cron_type' ) == 'hours' ) {
                    $interval = $interval * 3600 ;
                } else if ( get_option( 'rs_restrict_product_purchase_cron_type' ) == 'days' ) {
                    $interval = $interval * 86400 ;
                }
                $timestamp = time() + ( int ) $interval ;
                $date      = date( 'Y-m-d h:i:sa' , $timestamp ) ;
                update_post_meta( $order_id , 'rs_date_time_for_awarding_points' , $date ) ;
                if ( wp_next_scheduled( 'rs_restrict_product_purchase_for_time' , array( $order_id ) ) == false )
                    wp_schedule_single_event( $timestamp , 'rs_restrict_product_purchase_for_time' , array( $order_id ) ) ;
            }
        }

        public static function check_redeeming_in_order( $order_id , $orderuserid ) {
            $new_obj = new RewardPointsOrder( $order_id , 'no' ) ;
            $new_obj->check_redeeming_in_order() ;
        }

        public static function delete_referral_points_if_user_deleted( $user_id ) {
            if ( get_option( '_rs_reward_referal_point_user_deleted' ) == 2 )
                return ;

            $UserInfo             = new WP_User( $user_id ) ;
            $ModifiedRegDate      = date( 'Y-m-d h:i:sa' , strtotime( $UserInfo->user_registered ) ) ;
            $DelayedDate          = date( 'Y-m-d h:i:sa' , strtotime( $ModifiedRegDate . ' + ' . get_option( '_rs_days_for_redeeming_points' ) . ' days ' ) ) ;
            $ModifiedCheckingDate = strtotime( $DelayedDate ) ;
            $ModifiedCurrentDate  = strtotime( date( 'Y-m-d h:i:sa' ) ) ;
            $condition            = (get_option( '_rs_time_validity_to_redeem' ) == '1') ? true : ($ModifiedCurrentDate < $ModifiedCheckingDate) ;
            if ( ! $condition )
                return ;

            global $wpdb ;
            $table_name = $wpdb->prefix . 'rsrecordpoints' ;
            $refuserid  = get_user_meta( $user_id , '_rs_i_referred_by' , true ) ;
            if ( ! empty( $refuserid ) ) {
                $RefRegPoints = $wpdb->get_results( $wpdb->prepare( "SELECT (earnedpoints) FROM $table_name WHERE userid = %d AND checkpoints = %s AND refuserid = %d" , $refuserid , 'RRRP' , $user_id ) , ARRAY_A ) ;
                if ( srp_check_is_array( $RefRegPoints ) ) {
                    $Count      = ( int ) get_user_meta( $refuserid , 'rsreferreduserregisteredcount' , true ) - 1 ;
                    update_user_meta( $refuserid , 'rsreferreduserregisteredcount' , $Count ) ;
                    $table_args = array(
                        'user_id'     => $refuserid ,
                        'usedpoints'  => isset( $RefRegPoints[ 0 ][ 'earnedpoints' ] ) ? $RefRegPoints[ 0 ][ 'earnedpoints' ] : 0 ,
                        'checkpoints' => 'RVPFRRRP' ,
                        'refuserid'   => $user_id ,
                            ) ;
                    self::insert_earning_points( $table_args ) ;
                    self::record_the_points( $table_args ) ;
                    update_user_meta( $user_id , '_rs_i_referred_by' , $refuserid ) ;
                }
            }
            $getlistoforder = get_user_meta( $user_id , '_update_user_order' , true ) ;
            if ( ! srp_check_is_array( $getlistoforder ) )
                return ;

            foreach ( $getlistoforder as $order_id ) {
                $order = new WC_Order( $order_id ) ;
                if ( $order->status != 'completed' )
                    continue ;

                $OrderObj = srp_order_obj( $order ) ;
                $UserId   = $OrderObj[ 'order_userid' ] ;

                foreach ( $order->get_items() as $item ) {
                    if ( get_option( 'rs_set_price_to_calculate_rewardpoints_by_percentage' ) == '1' ) {
                        $getregularprice = get_post_meta( $item[ 'product_id' ] , '_regular_price' , true ) ;
                        $getregularprice = empty( $getregularprice ) ? get_post_meta( $item[ 'product_id' ] , '_price' , true ) : $getregularprice ;
                    } else {
                        $getregularprice = get_post_meta( $item[ 'product_id' ] , '_price' , true ) ;
                        $getregularprice = empty( $getregularprice ) ? get_post_meta( $item[ 'product_id' ] , '_regular_price' , true ) : $getregularprice ;
                    }
                    do_action_ref_array( 'rs_delete_points_for_referral_simple' , array( $getregularprice , $item ) ) ;
                    $referreduser = get_post_meta( $order_id , '_referrer_name' , true ) ;
                    if ( ! empty( $referreduser ) ) {
                        $new_obj = new RewardPointsOrder( $order_id , 'no' ) ;
                        self::insert_revised_referral_points( 0 , $referreduser , $UserId , $order_id , $new_obj , $order ) ;
                    }
                    self::update_revised_reward_points_to_user( $order_id , $UserId ) ;
                }
            }
        }

        public static function check_if_customer_purchased( $user_id , $emails , $product_id , $variation_id ) {
            global $wpdb ;
            $results = $wpdb->get_results(
                    $wpdb->prepare( "
			SELECT DISTINCT order_items.order_item_id
			FROM {$wpdb->prefix}woocommerce_order_items as order_items
			LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS itemmeta ON order_items.order_item_id = itemmeta.order_item_id
                        LEFT JOIN {$wpdb->postmeta} AS postmeta ON order_items.order_id = postmeta.post_id
			LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
			WHERE
				posts.post_status IN ( 'wc-completed', 'wc-processing' ) AND
				itemmeta.meta_value  = %s AND
				itemmeta.meta_key    IN ( '_variation_id', '_product_id' ) AND
				postmeta.meta_key    IN ( '_billing_email', '_customer_user' ) AND
				(
					postmeta.meta_value  IN ( '" . implode( "','" , array_map( 'esc_sql' , array_unique( ( array ) $emails ) ) ) . "' ) OR
					(
						postmeta.meta_value = %s
					)
				)
			" , empty( $variation_id ) ? $product_id : $variation_id , $user_id
                    )
                    ) ;

            if ( ! srp_check_is_array( $results ) )
                return 0 ;

            foreach ( $results as $each_results ) {
                $array_results[] = $each_results->order_item_id ;
            }
            $new = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(meta_value) as totalqty FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id IN(%s) and meta_key='_qty'" , implode( ',' , $array_results ) ) ) ;
            return $new[ 0 ]->totalqty ;
        }

        public static function msg_for_log( $csvmasterlog , $user_deleted , $order_status_changed , $earnpoints , $checkpoints , $productid , $orderid , $variationid , $userid , $refuserid , $reasonindetail , $redeempoints , $masterlog , $nomineeid , $usernickname , $nominatedpoints ) {
            $myaccountlink          = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ;
            $vieworderlink          = esc_url_raw( add_query_arg( 'view-order' , $orderid , $myaccountlink ) ) ;
            $vieworderlinkforfront  = '<a href="' . $vieworderlink . '">#' . $orderid . '</a>' ;
            $view_product           = '<a target="_blank" href="' . get_permalink( $productid ) . '">' . get_the_title( $productid ) . '</a>' ;
            $vieworderlink1         = esc_url_raw( add_query_arg( 'view-subscription' , $orderid , $myaccountlink ) ) ;
            $vieworderlinkforfront1 = '<a href="' . $vieworderlink1 . '">#' . $orderid . '</a>' ;
            $payment_method_title   = get_post_meta( $orderid , '_payment_method' , true ) ;
            $gateway_title          = ! empty( $payment_method_title ) ? get_payment_gateway_title( $payment_method_title ) : '' ;
            switch ( $checkpoints ) {
                case 'RPFAC':
                    return get_option( 'rs_reward_log_for_affiliate' ) ;
                    break ;
                case 'RPFWLS':
                    $Msg = str_replace( '{rs_waitlist_product_name}' , $view_product , get_option( '_rs_localize_reward_points_for_waitlist_subscribing' ) ) ;
                    return $Msg ;
                    break ;
                case 'RPFWLSC':
                    $Msg = str_replace( '{rs_waitlist_product_name}' , $view_product , get_option( '_rs_localize_reward_points_for_waitlist_sale_conversion' ) ) ;
                    return $Msg ;
                    break ;
                case 'RPG' :
                    $Msg = str_replace( '{payment_title}' , $gateway_title , get_option( '_rs_localize_reward_for_payment_gateway_message' ) ) ;
                    return $Msg ;
                    break ;
                case 'PPRPBCT':
                    if ( $reasonindetail == 'Replaced' ) {
                        $Msg = get_option( 'rs_log_for_product_purchase_when_overidded' ) ;
                    } else {
                        if ( $masterlog == false ) {
                            $Msg = get_option( '_rs_localize_points_earned_for_purchase_based_on_cart_total_for_master_log' ) ;
                        } else {
                            $Msg = get_option( '_rs_localize_points_earned_for_purchase_based_on_cart_total' ) ;
                        }
                    }
                    $OrderLink = ( $csvmasterlog == false ) ? $vieworderlinkforfront : '#' . $orderid ;
                    $Msg       = str_replace( '{currentorderid}' , $OrderLink , $Msg ) ;
                    return $Msg ;
                    break ;
                case 'PFFP':
                    return get_option( 'rs_log_for_first_purchase_points' ) ;
                    break ;
                case 'RPFFP':
                    return str_replace( '{currentorderid}' , $vieworderlinkforfront , get_option( 'rs_log_for_revised_first_purchase_points' ) ) ;
                    break ;
                case 'PPRP':
                    if ( $reasonindetail == 'Replaced' ) {
                        $Msg = get_option( 'rs_log_for_product_purchase_when_overidded' ) ;
                    } else {
                        if ( $masterlog == false ) {
                            $Msg = get_option( '_rs_localize_points_earned_for_purchase_main' ) ;
                        } else {
                            $Msg = get_option( '_rs_localize_product_purchase_reward_points' ) ;
                        }
                    }
                    $OrderLink = ( $csvmasterlog == false ) ? $vieworderlinkforfront : '#' . $orderid ;
                    $Msg       = str_replace( '{currentorderid}' , $OrderLink , $Msg ) ;
                    $Msg       = str_replace( '{itemproductid}' , $productid , $Msg ) ;
                    $Msg       = str_replace( '{productname}' , $view_product , $Msg ) ;
                    return $Msg ;
                    break ;
                case 'PPRRPG':
                    $Msg       = str_replace( '{itemproductid}' , get_the_title( $productid ) , get_option( '_rs_localize_referral_reward_points_for_purchase_gettin_referred' ) ) ;
                    $Msg       = str_replace( '{productname}' , $view_product , $Msg ) ;
                    return $Msg ;
                    break ;
                case 'RRPGR':
                    return get_option( '_rs_localize_referral_reward_points_gettin_referred' ) ;
                    break ;
                case 'PPRRP':
                    $Msg       = str_replace( '{itemproductid}' , $productid , get_option( '_rs_localize_referral_reward_points_for_purchase' ) ) ;
                    $Msg       = str_replace( '{productname}' , $view_product , $Msg ) ;
                    $Msg       = str_replace( '{purchasedusername}' , $refuserid != '' ? $refuserid : __( 'Guest' , SRP_LOCALE ) , $Msg ) ;
                    return $Msg ;
                    break ;
                case 'RRP':
                    return get_option( '_rs_localize_points_earned_for_registration' ) ;
                    break ;
                case 'SLRRP':
                    $Msg       = str_replace( '[network_name]' , $reasonindetail , get_option( '_rs_localize_points_earned_for_social_registration' ) ) ;
                    return $Msg ;
                    break ;
                case 'RRRP':
                    $refuserid = $refuserid != '' ? $refuserid : '(User Deleted)' ;
                    $Msg       = str_replace( '{registereduser}' , $refuserid , get_option( '_rs_localize_points_earned_for_referral_registration' ) ) ;
                    return $Msg ;
                    break ;
                case 'LRP':
                    return get_option( '_rs_localize_reward_points_for_login' ) ;
                    break ;
                case 'SLRP':
                    $Msg       = str_replace( '[network_name]' , $reasonindetail , get_option( '_rs_localize_reward_points_for_social_login' ) ) ;
                    return $Msg ;
                    break ;
                case 'SLLRP':
                    $Msg       = str_replace( '[network_name]' , $reasonindetail , get_option( '_rs_localize_reward_points_for_social_linking' ) ) ;
                    return $Msg ;
                    break ;
                case 'CRFRP':
                    $Msg       = str_replace( '[field_name]' , $reasonindetail , get_option( '_rs_localize_reward_points_for_cus_reg_field' ) ) ;
                    return $Msg ;
                case 'CRPFDP':
                    $Msg       = str_replace( '[field_name]' , $reasonindetail , get_option( '_rs_localize_reward_points_for_datepicker_cus_reg_field' ) ) ;
                    return $Msg ;
                    break ;
                case 'RPC':
                    return get_option( '_rs_localize_coupon_reward_points_log' ) ;
                    break ;
                case 'RPFBP':
                    return get_option( '_rs_localize_reward_points_for_create_post' ) ;
                    break ;
                case 'RPFBPG':
                    return get_option( '_rs_localize_reward_points_for_create_group' ) ;
                    break ;
                case 'RPFBPC':
                    return get_option( '_rs_localize_reward_points_for_post_comment' ) ;
                    break ;
                case 'RPFL':
                case 'RPFLP':
                    return get_option( '_rs_localize_reward_for_facebook_like' ) ;
                    break ;
                case 'RPFS':
                case 'RPFSP':
                    return get_option( '_rs_localize_reward_for_facebook_share' ) ;
                    break ;
                case 'RPTT':
                case 'RPTTP':
                    return get_option( '_rs_localize_reward_for_twitter_tweet' ) ;
                    break ;
                case 'RPIF':
                case 'RPIFP':
                    return get_option( '_rs_localize_reward_for_instagram' ) ;
                    break ;
                case 'RPTF':
                case 'RPTFP':
                    return get_option( '_rs_localize_reward_for_twitter_follow' ) ;
                    break ;
                case 'RPOK':
                case 'RPOKP':
                    return get_option( '_rs_localize_reward_for_ok_follow' ) ;
                    break ;
                case 'RPGPOS':
                case 'RPGPOSP':
                    return get_option( '_rs_localize_reward_for_google_plus' ) ;
                    break ;
                case 'RPVL':
                case 'RPVLP':
                    return get_option( '_rs_localize_reward_for_vk' ) ;
                    break ;
                case 'RPPR':
                    $Msg       = str_replace( '{reviewproductid}' , $productid , get_option( '_rs_localize_points_earned_for_product_review' ) ) ;
                    $Msg       = str_replace( '{productname}' , $view_product , $Msg ) ;
                    return $Msg ;
                    break ;
                case 'RP':
                    $OrderLink = ( $csvmasterlog == false ) ? $vieworderlinkforfront : '#' . $orderid ;
                    $Msg       = str_replace( '{currentorderid}' , $OrderLink , get_option( '_rs_localize_points_redeemed_towards_purchase' ) ) ;
                    $Msg       = str_replace( '{productname}' , $view_product , $Msg ) ;
                    return $Msg ;
                    break ;
                case 'MAP':
                case 'MRP':
                case 'MAURP':
                case 'MRURP':
                    return $reasonindetail ;
                    break ;
                case 'CBRP':
                    return get_option( '_rs_localize_points_to_cash_log' ) ;
                    break ;
                case 'RCBRP':
                    return get_option( '_rs_localize_points_to_cash_log_revised' ) ;
                    break ;
                case 'RPGV':
                    $Msg       = str_replace( '{rsusedvouchercode}' , $reasonindetail , get_option( '_rs_localize_voucher_code_usage_log_message' ) ) ;
                    return $Msg ;
                    break ;
                case 'RPBSRP':
                    $OrderLink = ( $csvmasterlog == false ) ? $vieworderlinkforfront : '#' . $orderid ;
                    $Msg       = str_replace( '{currentorderid}' , $OrderLink , get_option( '_rs_localize_buying_reward_points_log' ) ) ;
                    $Msg       = str_replace( '{productname}' , $view_product , $Msg ) ;
                    return $Msg ;
                    break ;
                case 'RPCPR':
                    $Msg       = str_replace( '{postid}' , get_the_title( $productid ) , get_option( '_rs_localize_points_earned_for_post_review' ) ) ;
                    return $Msg ;
                    break ;
                case 'RPFPOC':
                    $Msg       = str_replace( '{postid}' , get_the_title( $productid ) , get_option( '_rs_localize_points_earned_for_post_review' ) ) ;
                    return $Msg ;
                    break ;
                case 'RPCPRO':
                    $Msg       = str_replace( '{ProductName}' , get_the_title( $productid ) , get_option( '_rs_localize_points_earned_for_product_creation' ) ) ;
                    return $Msg ;
                    break ;
                case 'MREPFU':
                    $Msg       = str_replace( '[rsmaxpoints]' , get_option( 'rs_max_earning_points_for_user' ) , get_option( '_rs_localize_max_earning_points_log' ) ) ;
                    return $Msg ;
                    break ;
                case 'RPFGW':
                    $OrderLink = ( $csvmasterlog == false ) ? $vieworderlinkforfront : '#' . $orderid ;
                    $Msg       = str_replace( '{currentorderid}' , $OrderLink , get_option( '_rs_reward_points_gateway_log_localizaation' ) ) ;
                    return $Msg ;
                    break ;
                case 'RPFGWS':
                    $Msg       = str_replace( '{subscription_id}' , $vieworderlinkforfront1 , get_option( '_rs_localize_reward_for_using_subscription' ) ) ;
                    return $Msg ;
                    break ;

                case 'RVPFRPG':
                    $Msg       = str_replace( '{payment_title}' , $gateway_title , get_option( '_rs_localize_revise_reward_for_payment_gateway_message' ) ) ;
                    return $Msg ;
                    break ;
                case 'RVPFPPRP':
                    $Msg       = ( $masterlog == false ) ? get_option( '_rs_log_revise_product_purchase_main' ) : get_option( '_rs_log_revise_product_purchase' ) ;
                    $OrderLink = ( $csvmasterlog == false ) ? $vieworderlinkforfront : '#' . $orderid ;
                    $Msg       = str_replace( '{currentorderid}' , $OrderLink , $Msg ) ;
                    $Msg       = str_replace( '{productid}' , $productid , $Msg ) ;
                    $Msg       = str_replace( '{productname}' , $view_product , $Msg ) ;
                    return $Msg ;
                    break ;
                case 'RVPFBPRP':
                    $OrderLink = ( $csvmasterlog == false ) ? $vieworderlinkforfront : '#' . $orderid ;
                    $Msg       = str_replace( array( '{currentorderid}' , '{productname}' ) , array( $OrderLink , $view_product ) , get_option( '_rs_log_revise_buy_points_main' ) ) ;
                    return $Msg ;
                    break ;
                case 'RVPFPPRPBCT':
                    if ( $masterlog == false ) {
                        $Msg = get_option( '_rs_log_revise_for_product_purchase_based_on_cart_total_in_my_reward' ) ;
                    } else {
                        $Msg = get_option( '_rs_log_revise_for_product_purchase_based_on_cart_total' ) ;
                    }
                    $OrderLink = ( $csvmasterlog == false ) ? $vieworderlinkforfront : '#' . $orderid ;
                    $Msg       = str_replace( '{orderid}' , $OrderLink , $Msg ) ;
                    return $Msg ;
                    break ;
                case 'RVPFPPRRP':
                    if ( $order_status_changed == true ) {
                        $Msg = str_replace( '{productid}' , $productid , get_option( '_rs_log_revise_referral_product_purchase' ) ) ;
                    } elseif ( $user_deleted == true ) {
                        $Msg = str_replace( '{productid}' , $productid , get_option( '_rs_localize_revise_points_for_referral_purchase' ) ) ;
                    }
                    $Msg = str_replace( '{productname}' , $view_product , $Msg ) ;
                    $Msg = str_replace( '{usernickname}' , $refuserid , $Msg ) ;
                    return $Msg ;
                    break ;
                case 'RVPPRRPG':
                    if ( $order_status_changed == true ) {
                        $Msg = str_replace( '{productid}' , $productid , get_option( '_rs_log_revise_getting_referred_product_purchase' ) ) ;
                    } elseif ( $user_deleted == true ) {
                        $Msg = str_replace( '{productid}' , $productid , get_option( '_rs_localize_revise_points_for_getting_referred_purchase' ) ) ;
                    }
                    $Msg           = str_replace( '{productname}' , $view_product , $Msg ) ;
                    $Msg           = str_replace( '{usernickname}' , $refuserid , $Msg ) ;
                    return $Msg ;
                    break ;
                case 'RVPFRP':
                    $Msg           = get_option( '_rs_log_revise_points_redeemed_towards_purchase' ) ;
                    $OrderLink     = ( $masterlog == false ) ? $vieworderlinkforfront : '#' . $orderid ;
                    $Msg           = str_replace( '{currentorderid}' , $OrderLink , $Msg ) ;
                    $Msg           = str_replace( '{productname}' , $view_product , $Msg ) ;
                    return $Msg ;
                    break ;
                case 'RVPFRRRP':
                    $Msg           = str_replace( '{usernickname}' , $refuserid , get_option( '_rs_localize_referral_account_signup_points_revised' ) ) ;
                    return $Msg ;
                    break ;
                case 'RVPFRPVL':
                    return get_option( '_rs_localize_reward_for_vk_like_revised' ) ;
                    break ;
                case 'RVPFRPGPOS':
                    return get_option( '_rs_localize_reward_for_google_plus_revised' ) ;
                    break ;
                case 'RVPFRPFL':
                    return get_option( '_rs_localize_reward_for_facebook_like_revised' ) ;
                    break ;
                case 'PPRPFN':
                    $Name          = ( $masterlog == true ) ? $usernickname : "Your" ;
                    $Msg           = str_replace( array( '[points]' , '[user]' , '[name]' ) , array( $earnpoints , $nomineeid , $Name ) , get_option( '_rs_localize_log_for_nominee' ) ) ;
                    return $Msg ;
                    break ;
                case 'PPRPFNP':
                    $Name          = ( $masterlog == true ) ? $usernickname : "Your" ;
                    $Msg           = str_replace( array( '[points]' , '[user]' , '[name]' ) , array( $nominatedpoints , $nomineeid , $Name ) , get_option( '_rs_localize_log_for_nominated_user' ) ) ;
                    return $Msg ;
                    break ;
                case 'IMPADD':
                    $Msg           = str_replace( '[points]' , $earnpoints , get_option( '_rs_localize_log_for_import_add' ) ) ;
                    return $Msg ;
                    break ;
                case 'IMPOVR':
                    $Name          = ( $masterlog == true ) ? $earnpoints : "Your" ;
                    $Msg           = str_replace( '[points]' , $Name , get_option( '_rs_localize_log_for_import_override' ) ) ;
                    return $Msg ;
                    break ;
                case 'RPFP':
                    $replacepostid = str_replace( '{postid}' , get_the_title( $productid ) , get_option( '_rs_localize_points_earned_for_post' ) ) ;
                    return $replacepostid ;
                    break ;
                case 'SP':
                    $Name          = ( $masterlog == true ) ? $usernickname : "You" ;
                    $Msg           = str_replace( array( '[points]' , '[user]' , '[name]' ) , array( $earnpoints , $nomineeid , $Name ) , get_option( '_rs_localize_log_for_reciver' ) ) ;
                    return $Msg ;
                    break ;
                case 'RPCPAR':
                case 'RPFPAC':
                    $Msg           = str_replace( '{pagename}' , get_the_title( $productid ) , get_option( '_rs_localize_points_earned_for_page_review' ) ) ;
                    return $Msg ;
                    break ;
                case 'SENPM':
                    $Msg           = str_replace( '[user]' , $nomineeid , get_option( '_rs_localize_log_for_sender' ) ) ;
                    $Msg           = str_replace( '[points]' , $redeempoints , $Msg ) ;
                    $Name          = ( $masterlog == true ) ? $usernickname : "Your" ;
                    $Msg           = str_replace( '[name]' , $Name , $Msg ) ;
                    return $Msg ;
                    break ;
                case 'SPB':
                    if ( $masterlog == false ) {
                        return get_option( '_rs_localize_log_for_sender_after_submit' ) ;
                        break ;
                    }
                case 'SPA':
                    if ( $masterlog == false ) {
                        $Msg = str_replace( '[user]' , $nomineeid , get_option( '_rs_localize_log_for_sender' ) ) ;
                        $Msg = str_replace( '[points]' , $redeempoints , $Msg ) ;
                        $Msg = str_replace( '[name]' , "Your" , $Msg ) ;
                        return $Msg ;
                        break ;
                    }
                case 'SEP':
                    return get_option( '_rs_localize_points_to_send_log_revised' ) ;
                    break ;
                case 'RPFURL':
                    $replacepoints = str_replace( '[points]' , $earnpoints , get_option( 'rs_message_for_pointurl' ) ) ;
                    return $replacepoints ;
                    break ;
            }
        }

        public static function update_order_status() {
            $EarnStatus   = array() ;
            $RedeemStatus = array() ;
            $ReviseRedeem = array() ;
            if ( function_exists( 'wc_get_order_statuses' ) ) {
                $Orderslugs = str_replace( 'wc-' , '' , array_keys( wc_get_order_statuses() ) ) ;
                foreach ( $Orderslugs as $value ) {
                    if ( srp_check_is_array( get_option( 'rs_order_status_control' ) ) )
                        if ( ! in_array( $value , get_option( 'rs_order_status_control' ) ) )
                            $EarnStatus[] = $value ;

                    if ( srp_check_is_array( get_option( 'rs_order_status_control_redeem' ) ) )
                        if ( ! in_array( $value , get_option( 'rs_order_status_control_redeem' ) ) )
                            $RedeemStatus[] = $value ;

                    if ( srp_check_is_array( get_option( 'rs_order_status_control_revise_redeem' ) ) )
                        if ( in_array( $value , get_option( 'rs_order_status_control_revise_redeem' ) ) )
                            $ReviseRedeem[] = $value ;
                }
            } else {
                $term_args = array(
                    'hide_empty' => false ,
                    'orderby'    => 'date' ,
                        ) ;
                $tax_terms = get_terms( 'shop_order_status' , $term_args ) ;
                foreach ( $tax_terms as $getterms ) {
                    if ( is_object( $getterms ) ) {
                        if ( is_array( get_option( 'rs_order_status_control' ) ) )
                            if ( ! in_array( $getterms->slug , get_option( 'rs_order_status_control' ) ) )
                                $EarnStatus[] = $getterms->slug ;

                        if ( is_array( get_option( 'rs_order_status_control_redeem' ) ) )
                            if ( ! in_array( $getterms->slug , get_option( 'rs_order_status_control_redeem' ) ) )
                                $RedeemStatus[] = $getterms->slug ;

                        if ( is_array( get_option( 'rs_order_status_control_revise_redeem' ) ) )
                            if ( in_array( $getterms->slug , get_option( 'rs_order_status_control_revise_redeem' ) ) )
                                $ReviseRedeem[] = $getterms->slug ;
                    }
                }
            }
            update_option( 'rs_list_other_status_for_redeem' , $RedeemStatus ) ;
            update_option( 'rs_list_other_status' , $EarnStatus ) ;
            update_option( 'rs_list_other_status_for_revise_redeem' , $ReviseRedeem ) ;
        }

        public static function checkout_cookies_referral_meta( $order_id , $order_posted ) {
            $UserId = get_current_user_id() ;
            if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
                $refuser = (get_option( 'rs_generate_referral_link_based_on_user' ) == 1) ? get_user_by( 'login' , $_COOKIE[ 'rsreferredusername' ] ) : get_user_by( 'id' , $_COOKIE[ 'rsreferredusername' ] ) ;

                if ( ! $refuser ) {
                    return ;
                }

                $myid = $refuser->ID ;
            } else {
                $myid = check_if_referrer_has_manual_link( $UserId ) ;
            }

            if ( ! $myid )
                return ;

            if ( $UserId == $myid )
                return ;

            if ( isset( $refuser->ID ) ) {
                $OrderCount           = self::get_order_count( $_POST[ 'billing_email' ] , $UserId , array_keys( wc_get_order_statuses() ) , $refuser->ID ) ;
                $CheckOrderCountLimit = self::check_order_count_limit( $OrderCount , 'yes' ) ;
                if ( $CheckOrderCountLimit )
                    return ;
            }

            update_post_meta( $order_id , '_referrer_name' , $myid ) ;
            update_post_meta( $order_id , '_referrer_email' , $refuser->user_email ) ;
            $referral_data = array(
                'referred_user_name'                => $myid ,
                'award_referral_points_for_renewal' => get_option( 'rs_award_referral_point_for_renewal_order' ) ,
                    ) ;

            update_post_meta( $order_id , 'rs_referral_data_for_renewal_order' , $referral_data ) ;
            $getmetafromuser = get_user_meta( $UserId , '_update_user_order' , true ) ;
            $getorderlist[]  = $order_id ;
            $mainmerge       = srp_check_is_array( $getmetafromuser ) ? array_merge( $getmetafromuser , $getorderlist ) : $getorderlist ;
            update_user_meta( $UserId , '_update_user_order' , $mainmerge ) ;

            // Update Postmeta for Referrer points after Discounts.
            if ( 'yes' === get_option( 'rs_referral_points_after_discounts' ) ) {
                $points_after_discounts = RSFunctionForReferralSystem::referrel_points_for_product_in_cart( $myid , false ) ;
                if ( srp_check_is_array( $points_after_discounts ) ) {
                    update_post_meta( $order_id , 'rs_referrer_points_after_discounts' , $points_after_discounts ) ;
                }
            }
        }

        public static function delete_cookie_for_user_and_guest() {
            if ( ! isset( $_COOKIE[ 'rsreferredusername' ] ) )
                return ;

            $referrer = (get_option( 'rs_generate_referral_link_based_on_user' ) == 1) ? get_user_by( 'login' , $_COOKIE[ 'rsreferredusername' ] ) : get_user_by( 'id' , $_COOKIE[ 'rsreferredusername' ] ) ;

            if ( ! is_object( $referrer ) ) {
                return ;
            }

            $OrderCount           = self::get_order_count( $_POST[ 'billing_email' ] , get_current_user_id() , array_keys( wc_get_order_statuses() ) , $referrer->ID ) ;
            $CheckOrderCountLimit = self::check_order_count_limit( $OrderCount , 'yes' ) ;
            if ( $CheckOrderCountLimit ) {
                setcookie( 'rsreferredusername' , $_COOKIE[ 'rsreferredusername' ] , time() - 3600 , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
                setcookie( 'referrerip' , $_COOKIE[ 'referrerip' ] , time() - 3600 , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
            }
        }

        public static function get_order_count( $billing_email , $userid , $poststatus , $referrer_id ) {
            $args = array(
                'post_type'      => 'shop_order' ,
                'post_status'    => $poststatus ,
                'fields'         => 'ids' ,
                'meta_query'     => array(
                    'relation' => 'AND' ,
                    array(
                        'key'     => '_billing_email' ,
                        'value'   => $billing_email ,
                        'compare' => '=' ,
                    ) ,
                    array(
                        'key'     => '_customer_user' ,
                        'value'   => $userid ,
                        'compare' => '=' ,
                    ) ,
                    array(
                        'key'     => '_referrer_name' ,
                        'value'   => $referrer_id ,
                        'compare' => '=' ,
                    ) ,
                ) ,
                'posts_per_page' => '-1' ,
                'cache_results'  => false
                    ) ;

            $order_id = get_posts( $args ) ;
            return count( $order_id ) ;
        }

        public static function check_order_count_limit( $OrderCount , $OrderCountLimit ) {
            if ( get_option( 'rs_enable_delete_referral_cookie_after_first_purchase' ) != 'yes' )
                return false ;

            $NoofPurchase = get_option( 'rs_no_of_purchase' ) ;
            if ( empty( $NoofPurchase ) )
                return false ;

            $CountLimit = ($OrderCountLimit == 'yes') ? ($OrderCount >= $NoofPurchase) : ($OrderCount > $NoofPurchase) ;
            if ( $CountLimit )
                return true ;

            return false ;
        }

        public static function check_if_user_has_multiple_referrer( $BillingEmail , $OrderObj ) {
            if ( get_option( 'rs_restrict_referral_points_for_multiple_referrer' ) != 'yes' )
                return true ;

            $args = array(
                'post_type'      => 'shop_order' ,
                'post_status'    => array( 'wc-processing' , 'wc-completed' , 'wc-on-hold' , 'wc-pending' ) ,
                'meta_query'     => array(
                    'relation' => $OrderObj[ 'order_userid' ] > 0 ? 'OR' : 'AND' ,
                    array(
                        'key'     => '_billing_email' ,
                        'value'   => $BillingEmail ,
                        'compare' => '=' ,
                    ) ,
                    array(
                        'key'     => '_customer_user' ,
                        'value'   => $OrderObj[ 'order_userid' ] ,
                        'compare' => '=' ,
                    ) ,
                ) ,
                'posts_per_page' => -1 ,
                'fields'         => 'ids'
                    ) ;

            $OrderIds = get_posts( $args ) ;
            if ( empty( $OrderIds ) )
                return true ;

            if ( count( $OrderIds ) == 1 )
                return true ;

            foreach ( $OrderIds as $OrderId ) {
                $PrevReferrerName = get_post_meta( $OrderId , '_referrer_name' , true ) ;
                $ReferrerName     = get_post_meta( $OrderObj[ 'order_id' ] , '_referrer_name' , true ) ;
                if ( $PrevReferrerName != $ReferrerName )
                    return false ;
            }

            return true ;
        }

        public static function check_if_referrer_and_referral_from_same_ip( $OrderObj ) {
            if ( get_option( 'rs_restrict_referral_points_for_same_ip' ) != 'yes' )
                return true ;

            if ( ! isset( $_COOKIE[ 'referrerip' ] ) )
                return true ;

            $RefIPAddrs = base64_decode( $_COOKIE[ 'referrerip' ] ) ;
            $IPAddrs    = $OrderObj->get_customer_ip_address() ;
            if ( $RefIPAddrs == $IPAddrs )
                return false ;

            return true ;
        }

        public static function award_reward_points_for_coupon( $OrderId ) {
            if ( get_option( 'rs_reward_action_activated' ) != 'yes' )
                return ;

            $Order          = new WC_Order( $OrderId ) ;
            $CouponsInOrder = $Order->get_items( array( 'coupon' ) ) ;
            if ( ! srp_check_is_array( $CouponsInOrder ) )
                return ;

            $OrderObj       = srp_order_obj( $Order ) ;
            $UserId         = $OrderObj[ 'order_userid' ] ;
            $AppliedCoupons = array() ;
            $SortType       = (get_option( 'rs_choose_priority_level_selection_coupon_points' ) == '1') ? 'desc' : 'asc' ;
            $Rules          = multi_dimensional_sort( get_option( 'rewards_dynamic_rule_couponpoints' ) , 'reward_points' , $SortType ) ;
            $Codes          = array() ;
            $Datas          = array() ;
            if ( ! srp_check_is_array( $Rules ) )
                return ;

            foreach ( $Rules as $Key => $Rule ) {
                if ( ! isset( $Rule[ 'coupon_codes' ] ) )
                    continue ;

                if ( ! srp_check_is_array( $Rule[ 'coupon_codes' ] ) )
                    continue ;

                foreach ( $Rule[ 'coupon_codes' ] as $Code ) {
                    if ( in_array( $Code , $Codes ) )
                        continue ;

                    $Codes[ $Key ] = $Code ;
                }
            }

            if ( ! srp_check_is_array( $Codes ) )
                return ;

            foreach ( $Codes as $KeyToFind => $Value ) {
                $Datas[] = $Rules[ $KeyToFind ] ;
            }

            if ( ! srp_check_is_array( $Datas ) )
                return ;

            foreach ( $CouponsInOrder as $Code ) {
                $AppliedCoupons[] = $Code[ 'name' ] ;
            }

            foreach ( $Datas as $Data ) {
                $CouponCodes = $Data[ "coupon_codes" ] ;
                $Points      = $Data[ "reward_points" ] ;
                foreach ( $CouponCodes as $CouponCode ) {
                    if ( ! check_if_coupon_exist_in_cart( $CouponCode , $AppliedCoupons ) )
                        continue ;

                    $new_obj = new RewardPointsOrder( $OrderId , 'no' ) ;
                    if ( get_option( 'rs_enable_disable_max_earning_points_for_user' ) == 'yes' ) {
                        $new_obj->check_point_restriction( $Points , $pointsredeemed = 0 , $event_slug     = 'RPC' , $UserId , $nomineeid      = '' , $referrer_id    = '' , $product_id     = '' , $variationid    = '' , $reasonindetail = '' ) ;
                    } else {
                        $valuestoinsert = array( 'pointstoinsert' => $Points , 'event_slug' => 'RPC' , 'user_id' => $UserId , 'totalearnedpoints' => $Points ) ;
                        $new_obj->total_points_management( $valuestoinsert ) ;
                    }
                }
            }
            do_action( 'fp_reward_point_for_using_coupons' ) ;
        }

        public static function replace_total_points_for_user( $UserId , $Points = 0 , $date , $reason , $replace = true ) {
            global $wpdb ;
            $table_name = $wpdb->prefix . 'rspointexpiry' ;
            if ( ! empty( $UserId ) && $replace )
                $wpdb->delete( $table_name , array( 'userid' => $UserId ) ) ;

            $table_args = array(
                'user_id'        => $UserId ,
                'pointstoinsert' => $Points ,
                'checkpoints'    => 'MAP' ,
                'date'           => $date ,
                'reason'         => $reason ,
                    ) ;
            self::insert_earning_points( $table_args ) ;
            self::record_the_points( $table_args ) ;
        }

    }

    RSPointExpiry::init() ;
}