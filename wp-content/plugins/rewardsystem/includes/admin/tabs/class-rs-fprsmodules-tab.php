<?php
/*
 * Support Tab Setting
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSModulesTab' ) ) {

    class RSModulesTab {

        public static function init() {
            add_action( 'woocommerce_rs_settings_tabs_fprsmodules' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'woocommerce_admin_field_rs_modules_for_sumo' , array( __CLASS__ , 'reward_system_module_html' ) ) ;
        }

        public static function reward_system_admin_fields() {
            global $woocommerce ;
            return apply_filters( 'woocommerce_fprsmodules_tab' , array(
                array(
                    'type' => 'rs_modules_for_sumo'
                ) ,
                    ) ) ;
        }

        /* Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields */

        public static function reward_system_register_admin_settings() {
            woocommerce_admin_fields( RSModulesTab::reward_system_admin_fields() ) ;
        }

        /* Update the Settings on Save Changes may happen in SUMO Reward Points */

        public static function reward_system_update_settings() {
            woocommerce_update_options( RSModulesTab::reward_system_admin_fields() ) ;
        }

        public static function box_and_hyperlink_class_name( $enable ) {
            $activeclass    = ( $enable == 'yes' ) ? 'active_rs_box' : 'inactive_rs_box' ;
            $hyperlinkclass = ( $enable == 'yes' ) ? 'rs_active_hyperlink' : 'rs_inactive_hyperlink' ;
            $array          = array( 'classname1' => $activeclass , 'classname2' => $hyperlinkclass ) ;
            return $array ;
        }

        public static function reward_system_module_html() {
            if ( isset( $_GET[ 'section' ] ) ) {
                do_action( 'woocommerce_rs_settings_tabs_' . $_GET[ 'section' ] ) ;
            } else {
                ?>
                <div class="rs_Grid_wrapper"> 
                    <h1 class="rs_module_title"><?php _e( 'SUMO Reward points' , SRP_LOCALE ) ; ?> <span class="rs_module">- <?php _e( 'Modules' , SRP_LOCALE ) ; ?></span></h1>
                    <div class="rs_Grid_wrapper_inner">
                        <?php
                        //Product Purchase Module
                        $PPClassName = self::box_and_hyperlink_class_name( get_option( 'rs_product_purchase_activated' ) ) ;
                        self::html_element_for_module( $PPClassName[ 'classname1' ] , $PPClassName[ 'classname2' ] , 'Product Purchase' , 'fpproductpurchase' , get_option( 'rs_product_purchase_activated' ) , 'rs_product_purchase_activated' ) ;
                        
                        //Buying Points Module
                        $BPClassName = self::box_and_hyperlink_class_name( get_option( 'rs_buyingpoints_activated' ) ) ;
                        self::html_element_for_module( $BPClassName[ 'classname1' ] , $BPClassName[ 'classname2' ] , 'Buying Points' , 'fpbuyingpoints' , get_option( 'rs_buyingpoints_activated' ) , 'rs_buyingpoints_activated' ) ;

                        //Referral System Module
                        $RSClassName = self::box_and_hyperlink_class_name( get_option( 'rs_referral_activated' ) ) ;
                        self::html_element_for_module( $RSClassName[ 'classname1' ] , $RSClassName[ 'classname2' ] , 'Referral System' , 'fpreferralsystem' , get_option( 'rs_referral_activated' ) , 'rs_referral_activated' ) ;

                        //Social Reward Module
                        $SRClassName = self::box_and_hyperlink_class_name( get_option( 'rs_social_reward_activated' ) ) ;
                        self::html_element_for_module( $SRClassName[ 'classname1' ] , $SRClassName[ 'classname2' ] , 'Social Reward Points' , 'fpsocialreward' , get_option( 'rs_social_reward_activated' ) , 'rs_social_reward_activated' ) ;

                        //Reward Points for Actions Module
                        $ARClassName = self::box_and_hyperlink_class_name( get_option( 'rs_reward_action_activated' ) ) ;
                        self::html_element_for_module( $ARClassName[ 'classname1' ] , $ARClassName[ 'classname2' ] , 'Action Reward Points' , 'fpactionreward' , get_option( 'rs_reward_action_activated' ) , 'rs_reward_action_activated' ) ;

                        //Points Expiry Module
                        $PEClassName = self::box_and_hyperlink_class_name( get_option( 'rs_point_expiry_activated' ) ) ;
                        self::html_element_for_module( $PEClassName[ 'classname1' ] , $PEClassName[ 'classname2' ] , 'Points Expiry' , 'fppointexpiry' , get_option( 'rs_point_expiry_activated' ) , 'rs_point_expiry_activated' ) ;

                        //Redeeming Points Module
                        $RPClassName = self::box_and_hyperlink_class_name( get_option( 'rs_redeeming_activated' ) ) ;
                        self::html_element_for_module( $RPClassName[ 'classname1' ] , $RPClassName[ 'classname2' ] , 'Redeeming Points' , 'fpredeeming' , get_option( 'rs_redeeming_activated' ) , 'rs_redeeming_activated' ) ;

                        //Points Price Module
                        $POPClassName = self::box_and_hyperlink_class_name( get_option( 'rs_point_price_activated' ) ) ;
                        self::html_element_for_module( $POPClassName[ 'classname1' ] , $POPClassName[ 'classname2' ] , 'Points Price' , 'fppointprice' , get_option( 'rs_point_price_activated' ) , 'rs_point_price_activated' ) ;

                        //Email Module
                        $EMClassName = self::box_and_hyperlink_class_name( get_option( 'rs_email_activated' ) ) ;
                        self::html_element_for_module( $EMClassName[ 'classname1' ] , $EMClassName[ 'classname2' ] , 'Email' , 'fpmail' , get_option( 'rs_email_activated' ) , 'rs_email_activated' ) ;

                        //Email Expire Module
                        $EEClassName = self::box_and_hyperlink_class_name( get_option( 'rs_email_template_expire_activated' ) ) ;
                        self::html_element_for_module( $EEClassName[ 'classname1' ] , $EEClassName[ 'classname2' ] , 'Point Expiry Email' , 'fpemailexpiredpoints' , get_option( 'rs_email_template_expire_activated' ) , 'rs_email_template_expire_activated' ) ;

                        //Gift Voucher Module
                        $GVClassName = self::box_and_hyperlink_class_name( get_option( 'rs_gift_voucher_activated' ) ) ;
                        self::html_element_for_module( $GVClassName[ 'classname1' ] , $GVClassName[ 'classname2' ] , 'Gift Voucher' , 'fpgiftvoucher' , get_option( 'rs_gift_voucher_activated' ) , 'rs_gift_voucher_activated' ) ;

                        //SMS Module
                        $SMSClassName = self::box_and_hyperlink_class_name( get_option( 'rs_sms_activated' ) ) ;
                        self::html_element_for_module( $SMSClassName[ 'classname1' ] , $SMSClassName[ 'classname2' ] , 'SMS' , 'fpsms' , get_option( 'rs_sms_activated' ) , 'rs_sms_activated' ) ;

                        //Cashback Module
                        $CBClassName = self::box_and_hyperlink_class_name( get_option( 'rs_cashback_activated' ) ) ;
                        self::html_element_for_module( $CBClassName[ 'classname1' ] , $CBClassName[ 'classname2' ] , 'Cashback' , 'fpcashback' , get_option( 'rs_cashback_activated' ) , 'rs_cashback_activated' ) ;

                        //Nominee Module
                        $NMClassName = self::box_and_hyperlink_class_name( get_option( 'rs_nominee_activated' ) ) ;
                        self::html_element_for_module( $NMClassName[ 'classname1' ] , $NMClassName[ 'classname2' ] , 'Nominee' , 'fpnominee' , get_option( 'rs_nominee_activated' ) , 'rs_nominee_activated' ) ;

                        //Point URL Module
                        $PUClassName = self::box_and_hyperlink_class_name( get_option( 'rs_point_url_activated' ) ) ;
                        self::html_element_for_module( $PUClassName[ 'classname1' ] , $PUClassName[ 'classname2' ] , 'Point URL' , 'fppointurl' , get_option( 'rs_point_url_activated' ) , 'rs_point_url_activated' ) ;

                        //Reward Point Gateway Module
                        $GPClassName = self::box_and_hyperlink_class_name( get_option( 'rs_gateway_activated' ) ) ;
                        self::html_element_for_module( $GPClassName[ 'classname1' ] , $GPClassName[ 'classname2' ] , 'Reward Points Payment Gateway' , 'fprewardgateway' , get_option( 'rs_gateway_activated' ) , 'rs_gateway_activated' ) ;

                        //Send Points Module
                        $SPClassName = self::box_and_hyperlink_class_name( get_option( 'rs_send_points_activated' ) ) ;
                        self::html_element_for_module( $SPClassName[ 'classname1' ] , $SPClassName[ 'classname2' ] , 'Send Points' , 'fpsendpoints' , get_option( 'rs_send_points_activated' ) , 'rs_send_points_activated' ) ;

                        //Import/Export Points Module
                        $IEClassName = self::box_and_hyperlink_class_name( get_option( 'rs_imp_exp_activated' ) ) ;
                        self::html_element_for_module( $IEClassName[ 'classname1' ] , $IEClassName[ 'classname2' ] , 'Import/Export Points' , 'fpimportexport' , get_option( 'rs_imp_exp_activated' ) , 'rs_imp_exp_activated' ) ;

                        //Reports Module
                        $RMClassName = self::box_and_hyperlink_class_name( get_option( 'rs_report_activated' ) ) ;
                        self::html_element_for_module( $RMClassName[ 'classname1' ] , $RMClassName[ 'classname2' ] , 'Reports' , 'fpreportsincsv' , get_option( 'rs_report_activated' ) , 'rs_report_activated' ) ;

                        if ( class_exists( 'SUMODiscounts' ) ) {
                            //Discounts Compatability Module
                            $SDClassName = self::box_and_hyperlink_class_name( get_option( 'rs_discounts_compatability_activated' ) ) ;
                            self::html_element_for_module( $SDClassName[ 'classname1' ] , $SDClassName[ 'classname2' ] , 'SUMO Discounts Compatibility' , 'fpdiscounts' , get_option( 'rs_discounts_compatability_activated' ) , 'rs_discounts_compatability_activated' ) ;
                        }

                        if ( class_exists( 'SUMORewardcoupons' ) ) {
                            //SUMO Coupon Compatability Module
                            $SRCClassName = self::box_and_hyperlink_class_name( get_option( 'rs_coupon_compatability_activated' ) ) ;
                            self::html_element_for_module( $SRCClassName[ 'classname1' ] , $SRCClassName[ 'classname2' ] , 'SUMO Coupons Compatibility' , 'fpdiscounts' , get_option( 'rs_coupon_compatability_activated' ) , 'rs_coupon_compatability_activated' ) ;
                        }

                        //Reset Module
                        $REMClassName = self::box_and_hyperlink_class_name( get_option( 'rs_reset_activated' ) ) ;
                        self::html_element_for_module( $REMClassName[ 'classname1' ] , $REMClassName[ 'classname2' ] , 'Reset' , 'fpreset' , get_option( 'rs_reset_activated' ) , 'rs_reset_activated' ) ;
                        ?>
                    </div>
                </div>
                <?php
            }
        }

        public static function html_element_for_module( $classname1 , $classname2 , $module_name , $tab_name , $enable , $metakey ) {
            ?>             
            <div class="rs_grid">
                <div class="rs_inner_grid <?php echo $classname1 ; ?>">                    
                    <div class="<?php echo $classname2 ; ?>">
                        <h1><?php echo $module_name ; ?></h1>
                    </div>                    
                    <div class='bottom_sec'>
                        <label class="rs_switch_round">
                            <input type="checkbox" data-metakey="<?php echo $metakey ; ?>" class="rs_enable_module" <?php if ( $enable == 'yes' ) { ?> checked="checked" <?php } ?> />
                            <div class="rs_slider_round"></div>
                        </label>
                        <?php $style = ( $enable == 'yes' ) ? 'style="display:block;"' : 'style="display:none;"' ; ?>
                        <a class="rs_settings_link" <?php echo $style ; ?> href="<?php echo admin_url( 'admin.php?page=rewardsystem_callback&tab=fprsmodules&section=' . $tab_name ) ; ?>" ><?php _e( 'Settings' , SRP_LOCALE ) ; ?></a>
                    </div>
                </div>
            </div>                 
            <?php
        }

        public static function checkbox_for_module( $enable , $checkboxname , $metakey ) {
            ?>
            <tr valign="top">
                <th class="titledesc" scope="row">
                    <label><?php _e( 'Enable/Disable' , SRP_LOCALE ) ; ?></label>
                </th>
                <td class="forminp forminp-text">
                    <label class="rs_switch_round">
                        <input type="checkbox" value="yes" data-metakey="<?php echo $metakey ; ?>" name="<?php echo $checkboxname ; ?>" class="rs_enable_module" <?php if ( $enable == 'yes' ) { ?> checked="checked" <?php } ?> />
                        <div class="rs_slider_round"></div>
                    </label>
                </td>
            </tr>
            <?php
        }

    }

    RSModulesTab::init() ;
}