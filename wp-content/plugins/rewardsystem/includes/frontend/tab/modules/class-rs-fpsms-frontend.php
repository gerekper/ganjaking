<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFunctionForSms' ) ) {

    class RSFunctionForSms {

        public static function init() {
            if ( get_option( 'rs_ph_no_field_registration_page' ) == 'yes' ) {

                add_action( 'woocommerce_register_form' , array( __CLASS__ , 'phone_no_field_in_reg_form' ) ) ;

                add_action( 'woocommerce_register_post' , array( __CLASS__ , 'validate_phone_number_field' ) , 10 , 3 ) ;

                add_action( 'woocommerce_created_customer' , array( __CLASS__ , 'save_phone_number' ) , 10 , 3 ) ;

                add_action( 'woocommerce_edit_account_form' , array( __CLASS__ , 'phone_no_field_in_edit_form' ) ) ;

                add_action( 'woocommerce_save_account_details_errors' , array( __CLASS__ , 'validate_phone_number_field_in_edit_from' ) , 12 , 1 ) ;

                add_action( 'woocommerce_save_account_details' , array( __CLASS__ , 'update_phone_number_for_user' ) , 12 , 1 ) ;
            }
        }

        public static function send_sms_twilio_api( $OrderId , $MsgFor , $Points , $PhoneNumber ) {
            $MessageToSend = self::message_content( $OrderId , $MsgFor , $Points ) ;
            if ( ! $MessageToSend )
                return ;

            require_once SRP_PLUGIN_PATH . "/includes/frontend/SMS/Twilio.php" ;
            $PhoneNumber = (strpos( $PhoneNumber , '+' ) == false ) ? '+' . $PhoneNumber : $PhoneNumber ;
            $client      = new Services_Twilio( get_option( 'rs_twilio_secret_account_id' ) , get_option( 'rs_twilio_auth_token_id' ) ) ;
            $Response    = $client->account->messages->sendMessage(
                    get_option( 'rs_twilio_from_number' ) ,
                    // the number we are sending to - Any phone number
                                $PhoneNumber ,
                    // the sms body
                                $MessageToSend
                    ) ;
        }

        public static function send_sms_nexmo_api( $OrderId , $MsgFor , $Points , $PhoneNumber ) {
            $MessageToSend = self::message_content( $OrderId , $MsgFor , $Points ) ;
            if ( ! $MessageToSend )
                return ;

            include_once ( SRP_PLUGIN_PATH . "/includes/frontend/SMS/NexmoMessage.php" ) ;
            $PhoneNumber = (strpos( $PhoneNumber , '+' ) == false ) ? '+' . $PhoneNumber : $PhoneNumber ;
            $NexmoObj    = new NexmoMessage( get_option( 'rs_nexmo_key' ) , get_option( 'rs_nexmo_secret' ) ) ;
            $Response    = $NexmoObj->sendText( $PhoneNumber , 'SUMO Rewards' , $MessageToSend ) ;
        }

        public static function message_content( $OrderId , $MsgFor , $Points ) {
            if ( ! empty( $OrderId ) ) {
                $OrderObj = new WC_Order( $OrderId ) ;
                $OrderObj = srp_order_obj( $OrderObj ) ;
                $UserId   = $OrderObj[ 'order_userid' ] ;
            } else {
                $UserId = get_current_user_id() ;
            }
            if ( check_banning_type( $UserId ) == 'earningonly' || check_banning_type( $UserId ) == 'both' )
                return false ;

            $UserData        = get_user_by( 'id' , $UserId ) ;
            $UserName        = is_object( $UserData ) ? $UserData->user_login : 'Guest' ;
            $PointsData      = new RS_Points_Data( $UserId ) ;
            $AvailabelPoints = $PointsData->total_available_points() ;
            $Action          = "" ;
            $Message         = get_option( 'rs_send_sms_earning_points_content_for_actions' ) ;
            if ( $MsgFor == 'signup' ) {
                $Action = "Account Signup" ;
            } elseif ( $MsgFor == 'review' ) {
                $Action = "Product Review" ;
            } elseif ( $MsgFor == 'referralregistration' ) {
                $Action = "Referral Registration" ;
            } elseif ( $MsgFor == 'referralpurchase' ) {
                $Action = "Referral Product Purchase" ;
            } elseif ( $MsgFor == 'earning' ) {
                $Message = get_option( 'rs_points_sms_content_for_earning' ) ;
                $Points  = ( get_option( 'rs_award_points_for_cart_or_product_total' ) == 1 ) ? get_post_meta( $OrderId , 'rs_points_for_current_order_as_value' , true ) : get_post_meta( $OrderId , 'points_for_current_order_based_on_cart_total' , true ) ;
            } elseif ( $MsgFor == 'redeeming' ) {
                $Message = get_option( 'rs_points_sms_content_for_redeeming' ) ;
            }
            $ValueToFind    = array( '{points}' , '{username}' , '{rewardpoints}' , '{sitelink}' , '{orderid}' , '{action}' ) ;
            $ValueToReplace = array( $Points , $UserName , round_off_type( $AvailabelPoints ) , site_url() , '#' . $OrderId , $Action ) ;
            return str_replace( $ValueToFind , $ValueToReplace , $Message ) ;
        }

        public static function phone_no_field_in_reg_form() {
            ?>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label class = "rs_billing_phone_label"><?php echo get_option( 'rs_ph_no_field_label_registration' ) ; ?><span class="required"> * </span></label>
                <input type="text" class="input-text" name="rs_billing_phone_field" id="rs_billing_phone_field" value="<?php if ( ! empty( $_POST[ 'rs_billing_phone_field' ] ) ) ( $_POST[ 'rs_billing_phone_field' ] ) ; ?>" />
            </p>
            <div class="clear"></div>
            <?php
        }

        public static function validate_phone_number_field( $username , $email , $validation_errors ) {
            if ( get_option( 'rs_enable_reward_program' ) != 'yes' )
                return ;

            if ( empty( $_POST[ 'rs_enable_earn_points_for_user_in_reg_form' ] ) )
                return ;

            if ( isset( $_POST[ 'rs_billing_phone_field' ] ) && empty( $_POST[ 'rs_billing_phone_field' ] ) )
                $validation_errors->add( 'value_empty_error' , get_option( 'rs_ph_no_validationerror_emptyfield' ) ) ;
        }

        public static function save_phone_number( $UserId , $UserData , $pwdgenerated ) {
            if ( isset( $_POST[ 'rs_billing_phone_field' ] ) )
                update_user_meta( $UserId , 'rs_phone_number_value_from_signup' , $_POST[ 'rs_billing_phone_field' ] ) ;
        }

        public static function phone_no_field_in_edit_form() {
            ?>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label class = "rs_billing_phone_editaccount_label"><?php echo get_option( 'rs_ph_no_field_label_registration' ) ; ?><span class="required"> * </span></label>
                <input type="text" class="input-text" name="rs_billing_phone_editaccount_field" id="rs_billing_phone_editaccount_field" value="<?php echo get_user_meta( get_current_user_id() , 'rs_phone_number_value_from_signup' , true ) ; ?>" />
            </p>
            <div class="clear"></div>
            <?php
        }

        public static function validate_phone_number_field_in_edit_from( $args ) {
            if ( isset( $_POST[ 'rs_billing_phone_editaccount_field' ] ) && empty( $_POST[ 'rs_billing_phone_editaccount_field' ] ) )
                $args->add( 'value_empty_error' , get_option( 'rs_ph_no_validationerror_emptyfield' ) ) ;
        }

        public static function update_phone_number_for_user( $user_id ) {
            $customer = new WC_Customer( $user_id ) ;
            if ( $customer ) {
                if ( ! empty( $_POST[ 'rs_billing_phone_editaccount_field' ] ) ) {
                    $customer->set_billing_phone( $_POST[ 'rs_billing_phone_editaccount_field' ] ) ;
                    update_user_meta( $user_id , 'rs_phone_number_value_from_account_details' , $_POST[ 'rs_billing_phone_editaccount_field' ] ) ;
                } else {
                    $phone_number_value = get_user_meta( $user_id , 'rs_phone_number_value_from_signup' , true ) ;
                    $customer->set_billing_phone( $phone_number_value ) ;
                    update_user_meta( $user_id , 'rs_phone_number_value_from_account_details' , $phone_number_value ) ;
                }
                $customer->save() ;
            }
        }

    }

    RSFunctionForSms::init() ;
}