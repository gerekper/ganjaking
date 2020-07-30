<?php
/*
 * Support Tab Setting
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSRedeemingModule' ) ) {

    class RSRedeemingModule {

        public static function init() {

            add_action( 'rs_default_settings_fpredeeming' , array( __CLASS__ , 'set_default_value' ) ) ;

            add_action( 'woocommerce_rs_settings_tabs_fpredeeming' , array( __CLASS__ , 'reward_system_register_admin_settings' ) ) ; // Call to register the admin settings in the Reward System Submenu with general Settings tab        

            add_action( 'woocommerce_update_options_fprsmodules_fpredeeming' , array( __CLASS__ , 'reward_system_update_settings' ) ) ; // call the woocommerce_update_options_{slugname} to update the reward system                               

            add_action( 'woocommerce_admin_field_exclude_product_selection' , array( __CLASS__ , 'rs_select_product_to_exclude' ) ) ;

            add_action( 'woocommerce_admin_field_include_product_selection' , array( __CLASS__ , 'rs_select_product_to_include' ) ) ;

            add_action( 'woocommerce_admin_field_rs_enable_disable_redeeming_module' , array( __CLASS__ , 'enable_module' ) ) ;

            if ( class_exists( 'SUMODiscounts' ) )
                add_filter( 'woocommerce_fpredeeming' , array( __CLASS__ , 'setting_for_hide_redeem_field_when_sumo_discount_is_active' ) ) ;

            add_action( 'fp_action_to_reset_module_settings_fpredeeming' , array( __CLASS__ , 'reset_redeeming_module' ) ) ;

            if ( class_exists( 'SUMOMemberships' ) )
                add_filter( 'woocommerce_fpredeeming' , array( __CLASS__ , 'add_field_for_membership' ) ) ;

            add_action( 'woocommerce_admin_field_rs_user_role_dynamics_for_redeem' , array( __CLASS__ , 'rs_function_to_add_rule_for_redeeming_percentage' ) ) ;

            add_filter( "woocommerce_fpredeeming" , array( __CLASS__ , 'reward_system_add_settings_to_action' ) ) ;

            add_action( 'woocommerce_admin_field_rs_user_purchase_history_redeem' , array( __CLASS__ , 'rs_function_to_add_rule_for_redeeming_percentage_purchase_history' ) ) ;

            add_action( 'rs_display_save_button_fpredeeming' , array( 'RSTabManagement' , 'rs_display_save_button' ) ) ;

            add_action( 'rs_display_reset_button_fpredeeming' , array( 'RSTabManagement' , 'rs_display_reset_button' ) ) ;
        }

        public static function rs_function_to_add_rule_for_redeeming_percentage_purchase_history() {
            global $woocommerce ;
            wp_nonce_field( plugin_basename( __FILE__ ) , 'rsdynamicrulecreationsforuserpurchasehistory_redeeming' ) ;
            ?>
            <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr class="rsdynamicrulecreationsforuserpurchasehistory_redeeming">
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Level Name' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Type' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Value' , SRP_LOCALE ) ; ?></th>      
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Percentage' , SRP_LOCALE ) ; ?></th>   
                        <th class="manage-column column-columnname num" scope="col"><?php _e( 'Remove Level' , SRP_LOCALE ) ; ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="rsdynamicrulecreationsforuserpurchasehistory_redeeming">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="manage-column column-columnname num" scope="col"> <span class="add_product button-primary"><?php _e( 'Add New Level' , SRP_LOCALE ) ; ?></span></td>
                    </tr>
                    <tr class="rsdynamicrulecreationsforuserpurchasehistory_redeeming">
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Level Name' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Type' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Value' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Percentage' , SRP_LOCALE ) ; ?></th>

                        <th class="manage-column column-columnname num" scope="col"><?php _e( 'Remove Level' , SRP_LOCALE ) ; ?></th>

                    </tr>
                </tfoot>
                <tbody id="here_product">
                    <?php
                    $rewards_dynamic_rules = get_option( 'rewards_dynamic_rule_purchase_history_redeem' ) ;
                    if ( ! empty( $rewards_dynamic_rules ) ) {
                        if ( is_array( $rewards_dynamic_rules ) ) {
                            foreach ( $rewards_dynamic_rules as $i => $rewards_dynamic_rule ) {
                                ?>
                                <tr class="rsdynamicrulecreationsforuserpurchasehistory_redeeming">
                                    <td class="column-columnname">
                                        <p class="form-field">
                                            <input type="text" name="rewards_dynamic_rule_purchase_history_redeem[<?php echo $i ; ?>][name]" class="short" value="<?php echo $rewards_dynamic_rule[ 'name' ] ; ?>"/>
                                        </p>
                                    </td>
                                    <td class="column-columnname">
                                        <p class="form-field">
                                            <select style="width:225px !important;" name="rewards_dynamic_rule_purchase_history_redeem[<?php echo $i ; ?>][type]" id="rewards_dynamic_rule_purchase_history_redeem<?php echo $i ; ?>" class="short"  />
                            <option value="1" <?php selected( '1' , $rewards_dynamic_rule[ 'type' ] ) ; ?>><?php _e( 'Number of Successful Order(s)' , SRP_LOCALE ) ; ?></option>
                            <option value="2" <?php selected( '2' , $rewards_dynamic_rule[ 'type' ] ) ; ?>><?php _e( 'Total Amount Spent in Site' , SRP_LOCALE ) ; ?></option>

                        </select> 
                        </p>
                        </td>
                        <td class="column-columnname">
                            <p class="form-field">
                                <input type ="number" name="rewards_dynamic_rule_purchase_history_redeem[<?php echo $i ; ?>][value]" id="rewards_dynamic_rule_purchase_historyrewards_dynamic_rule_purchase_history_redeemvalue<?php echo $i ; ?>" class="short test" value="<?php echo $rewards_dynamic_rule[ 'value' ] ; ?>"/>
                            </p>
                        </td>
                        <td class="column-columnname">
                            <p class="form-field">
                                <input type ="number" name="rewards_dynamic_rule_purchase_history_redeem[<?php echo $i ; ?>][percentage]" id="rewards_dynamic_rule_purchase_history_redeempercentage<?php echo $i ; ?>" class="short test" value="<?php echo $rewards_dynamic_rule[ 'percentage' ] ; ?>"/>
                            </p>
                        </td>

                        <td class="column-columnname num">
                            <span class="remove button-secondary"><?php _e( 'Remove Level' , SRP_LOCALE ) ; ?></span>
                        </td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
            </tbody>
            </table>
            <script type="text/javascript">
                jQuery( document ).ready( function ( ) {
                jQuery( ".add_product" ).on( 'click' , function ( ) {
                var countrewards_dynamic_rule = Math.round( new Date( ).getTime( ) + ( Math.random( ) * 100 ) ) ;
            <?php ?>
                jQuery( '#here_product' ).append( '<tr class="rsdynamicrulecreationsforuserpurchasehistory_redeeming"><td><p class="form-field"><input type="text" name="rewards_dynamic_rule_purchase_history_redeem[' + countrewards_dynamic_rule + '][name]" class="short" value=""/></p></td>\n\
            <td><p class="form-field"><select style="width:225px !important;" id="rewards_dynamic_rule_purchase_history_redeem' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule_purchase_history_redeem[' + countrewards_dynamic_rule + '][type]" class="short">\n\
            <option value="1"><?php _e( 'Number of Successful Order(s)' , SRP_LOCALE ) ; ?></option>\n\
            <option value="2"><?php _e( 'Total Amount Spent in Site' , SRP_LOCALE ) ; ?></select></p></td>\n\
            <td><p class="form-field"><input type ="number" id="rewards_dynamic_rule_purchase_history_redeem' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule_purchase_history_redeem[' + countrewards_dynamic_rule + '][value]" class="short test"  value=""/></p></td>\n\
             <td><p class="form-field"><input type ="number" id="rewards_dynamic_rule_purchase_history_redeem' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule_purchase_history_redeem[' + countrewards_dynamic_rule + '][percentage]" class="short"  value=""/></p></td>\n\
            <td class="num"><span class="remove button-secondary"><?php _e( 'Remove Rule' , SRP_LOCALE ) ; ?></span></td></tr><hr>' ) ;
                return false ;
                } ) ;
                jQuery( document ).on( 'click' , '.remove' , function ( ) {
                jQuery( this ).parent( ).parent( ).remove( ) ;
                } ) ;
                jQuery( '#rs_enable_user_role_based_reward_points_for_redeem' ).addClass( 'rs_enable_user_role_based_reward_points_for_redeem' ) ;
                jQuery( '#rs_enable_earned_level_based_reward_points_for_redeem' ).addClass( 'rs_enable_earned_level_based_reward_points_for_redeem' ) ;
                } ) ;</script>
            <?php
        }

        /*
         * Function to Define Name of the Tab
         */

        public static function add_field_for_membership( $settings ) {
            $updated_settings = array() ;
            $membership_level = sumo_get_membership_levels() ;
            foreach ( $settings as $section ) {
                $updated_settings[] = $section ;
                if ( isset( $section[ 'id' ] ) && '_rs_user_role_reward_points_for_redeem' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    $updated_settings[] = array(
                        'name' => __( 'Reward Points Redeem Percentage based on Membership Plan' , SRP_LOCALE ) ,
                        'type' => 'title' ,
                        'id'   => '_rs_membership_plan_for_redeem' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'   => __( 'Don\'t allow Redeeming when the user hasn\'t purchased any membership plan through SUMO Memberships' , SRP_LOCALE ) ,
                        'desc'   => __( 'Don\'t allow Redeeming when the user hasn\'t purchased any membership plan through SUMO Memberships' , SRP_LOCALE ) ,
                        'id'     => 'rs_restrict_redeem_when_no_membership_plan' ,
                        'css'    => 'min-width:150px;' ,
                        'type'   => 'checkbox' ,
                        'newids' => 'rs_restrict_redeem_when_no_membership_plan' ,
                            ) ;
                    $updated_settings[] = array(
                        'name'    => __( 'Membership Plan based Redeem Level' , SRP_LOCALE ) ,
                        'desc'    => __( 'Enable this option to modify Redeem points based on membership plan' , SRP_LOCALE ) ,
                        'id'      => 'rs_enable_membership_plan_based_redeem' ,
                        'css'     => 'min-width:150px;' ,
                        'std'     => 'yes' ,
                        'default' => 'yes' ,
                        'type'    => 'checkbox' ,
                        'newids'  => 'rs_enable_membership_plan_based_redeem' ,
                            ) ;
                    foreach ( $membership_level as $key => $value ) {
                        $updated_settings[] = array(
                            'name'     => __( 'Reward Points Redeem Percentage for ' . $value , SRP_LOCALE ) ,
                            'desc'     => __( 'Please Enter Percentage of Redeem for ' . $value , SRP_LOCALE ) ,
                            'class'    => 'rewardpoints_membership_plan_for_redeem' ,
                            'id'       => 'rs_reward_membership_plan_for_redeem' . $key ,
                            'css'      => 'min-width:150px;' ,
                            'std'      => '100' ,
                            'type'     => 'text' ,
                            'newids'   => 'rs_reward_membership_plan_for_redeem' . $key ,
                            'desc_tip' => true ,
                                ) ;
                    }
                    $updated_settings[] = array(
                        'type' => 'sectionend' ,
                        'id'   => '_rs_membership_plan_for_redeem'
                            ) ;
                }
            }
            return $updated_settings ;
        }

        public static function rs_function_to_add_rule_for_redeeming_percentage() {
            global $woocommerce ;
            wp_nonce_field( plugin_basename( __FILE__ ) , 'rsdynamicrulecreation_for_redeem' ) ;
            ?>
            <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr class="rsdynamicrulecreation_for_redeem">
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Level Name' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Reward Points' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Redeem Points Percentage' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e( 'Remove Level' , SRP_LOCALE ) ; ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="rsdynamicrulecreation_for_redeem">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="manage-column column-columnname num" scope="col"> <span class="add_redeem button-primary"><?php _e( 'Add New Level' , SRP_LOCALE ) ; ?></span></td>
                    </tr>
                    <tr class="rsdynamicrulecreation_for_redeem">
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Level Name' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Reward Points' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname" scope="col"><?php _e( 'Redeem Points Percentage' , SRP_LOCALE ) ; ?></th>
                        <th class="manage-column column-columnname num" scope="col"><?php _e( 'Remove Level' , SRP_LOCALE ) ; ?></th>

                    </tr>
                </tfoot>
                <tbody id="here_redeem">
                    <?php
                    $rewards_dynamic_rules = get_option( 'rewards_dynamic_rule_for_redeem' ) ;
                    if ( ! empty( $rewards_dynamic_rules ) ) {
                        if ( is_array( $rewards_dynamic_rules ) ) {
                            foreach ( $rewards_dynamic_rules as $i => $rewards_dynamic_rule ) {
                                ?>
                                <tr class="rsdynamicrulecreation_for_redeem">
                                    <td class="column-columnname">
                                        <p class="form-field">
                                            <input type="text" name="rewards_dynamic_rule_for_redeem[<?php echo $i ; ?>][name]" class="short" value="<?php echo $rewards_dynamic_rule[ 'name' ] ; ?>"/>
                                        </p>
                                    </td>
                                    <td class="column-columnname">
                                        <p class="form-field">
                                            <input type="number" step="any" min="0" name="rewards_dynamic_rule_for_redeem[<?php echo $i ; ?>][rewardpoints]" id="rewards_dynamic_rewardpoints<?php echo $i ; ?>" class="short" value="<?php echo $rewards_dynamic_rule[ 'rewardpoints' ] ; ?>"/>
                                        </p>
                                    </td>
                                    <td class="column-columnname">
                                        <p class="form-field">
                                            <input type ="number" name="rewards_dynamic_rule_for_redeem[<?php echo $i ; ?>][percentage]" id="rewards_dynamic_rule_percentage<?php echo $i ; ?>" class="short test" value="<?php echo $rewards_dynamic_rule[ 'percentage' ] ; ?>"/>
                                        </p>
                                    </td>

                                    <td class="column-columnname num">
                                        <span class="remove_redeem button-secondary"><?php _e( 'Remove Level' , SRP_LOCALE ) ; ?></span>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
            <script type="text/javascript">
                jQuery( document ).ready( function ( ) {
                jQuery( ".add_redeem" ).on( 'click' , function ( ) {
                var countrewards_dynamic_rule = Math.round( new Date( ).getTime( ) + ( Math.random( ) * 100 ) ) ;
            <?php
            if ( ( float ) $woocommerce->version > ( float ) ('2.2.0') ) {
                if ( $woocommerce->version >= ( float ) ('3.0.0') ) {
                    ?>
                        jQuery( '#here_redeem' ).append( '<tr class="rsdynamicrulecreation_for_redeem"><td><p class="form-field"><input type="text" name="rewards_dynamic_rule_for_redeem[' + countrewards_dynamic_rule + '][name]" class="short" value=""/></p></td>\n\
                                                                                                                                                                                                                            \n\<td><p class="form-field"><input type="number" step="any" min="0" id="rewards_dynamic_ruleamount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule_for_redeem[' + countrewards_dynamic_rule + '][rewardpoints]" class="short" value=""/></p></td>\n\
                                                                                                                                                                                                                            \n\\n<td><p class="form-field"><input type ="number" id="rewards_dynamic_rule_claimcount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule_for_redeem[' + countrewards_dynamic_rule + '][percentage]" class="short test"  value=""/></p></td>\n\\n\
                                                                                                                                                                                                                            \n\ <td class="num"><span class="remove_redeem button-secondary">Remove Rule</span></td></tr><hr>' ) ;
                        jQuery( 'body' ).trigger( 'wc-enhanced-select-init' ) ;
                <?php } else {
                    ?>
                        jQuery( '#here_redeem' ).append( '<tr><td><p class="form-field"><input type="text" name="rewards_dynamic_rule_for_redeem[' + countrewards_dynamic_rule + '][name]" class="short" value=""/></p></td>\n\
                                                                                                                                                                                                                            \n\<td><p class="form-field"><input type="number" step="any" min="0" id="rewards_dynamic_ruleamount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule_for_redeem[' + countrewards_dynamic_rule + '][rewardpoints]" class="short" value=""/></p></td>\n\
                                                                                                                                                                                                                            \n\\n\
                                                                                                                                                                                                                            <td><p class="form-field"><input type ="number" id="rewards_dynamic_rule_claimcount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule_for_redeem[' + countrewards_dynamic_rule + '][percentage]" class="short test"  value=""/></p></td>\n\\n\
                                                                                                                                                                                                                            \n\ <td class="num"><span class="remove_redeem button-secondary">Remove Rule</span></td></tr><hr>' ) ;
                        jQuery( 'body' ).trigger( 'wc-enhanced-select-init' ) ;
                <?php } ?>
            <?php } else { ?>
                    jQuery( '#here_redeem' ).append( '<tr><td><p class="form-field"><input type="text" name="rewards_dynamic_rule_for_redeem[' + countrewards_dynamic_rule + '][name]" class="short" value=""/></p></td>\n\
                                                                                                                                                                                                                        \n\<td><p class="form-field"><input type="number" step="any" min="0" id="rewards_dynamic_ruleamount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule_for_redeem[' + countrewards_dynamic_rule + '][rewardpoints]" class="short" value=""/></p></td>\n\
                                                                                                                                                                                                                        \n\\n\
                                                                                                                                                                                                                        <td><p class="form-field"><input type ="number" id="rewards_dynamic_rule_claimcount' + countrewards_dynamic_rule + '" name="rewards_dynamic_rule_for_redeem[' + countrewards_dynamic_rule + '][percentage]" class="short test"  value=""/></p></td>\n\\n\
                                                                                                                                                                                                                        \n\  <td class="num"><span class="remove_redeem button-secondary">Remove Rule</span></td></tr><hr>' ) ;
            <?php } ?>
                return false ;
                } ) ;
                jQuery( document ).on( 'click' , '.remove_redeem' , function ( ) {
                jQuery( this ).parent( ).parent( ).remove( ) ;
                } ) ;
                jQuery( '#rs_enable_user_role_based_reward_points_for_redeem' ).addClass( 'rs_enable_user_role_based_reward_points_for_redeem' ) ;
                jQuery( '#rs_enable_earned_level_based_reward_points_for_redeem' ).addClass( 'rs_enable_earned_level_based_reward_points_for_redeem' ) ;
                } ) ;</script>
            <?php
        }

        /*
         * Function to add settings for Member Level in Member Level Tab
         */

        public static function reward_system_add_settings_to_action( $settings ) {
            global $wp_roles ;
            $updated_settings = array() ;
            $mainvariable     = array() ;
            global $woocommerce ;
            foreach ( $settings as $section ) {
                if ( isset( $section[ 'id' ] ) && '_rs_user_role_reward_points_for_redeem' == $section[ 'id' ] &&
                        isset( $section[ 'type' ] ) && 'sectionend' == $section[ 'type' ] ) {
                    foreach ( $wp_roles->role_names as $value => $key ) {
                        $updated_settings[] = array(
                            'name'     => __( 'Reward Points Redeeming Percentage for ' . $key . ' User Role' , SRP_LOCALE ) ,
                            'desc'     => __( 'Please Enter Percentage of Redeeming Reward Points for ' . $key , SRP_LOCALE ) ,
                            'class'    => 'rewardpoints_userrole_for_redeem' ,
                            'id'       => 'rs_reward_user_role_for_redeem_' . $value ,
                            'css'      => 'min-width:150px;' ,
                            'std'      => '100' ,
                            'default'  => '100' ,
                            'type'     => 'text' ,
                            'newids'   => 'rs_reward_user_role_for_redeem_' . $value ,
                            'desc_tip' => true ,
                                ) ;
                    }
                    $updated_settings[] = array(
                        'type' => 'sectionend' , 'id'   => '_rs_user_role_reward_points_for_redeem' ,
                            ) ;
                }

                $updated_settings[] = $section ;
            }

            return $updated_settings ;
        }

        /*
         * Function label settings to Member Level Tab
         */

        public static function reward_system_admin_fields() {
            global $woocommerce ;
            //Section and option details
            if ( class_exists( 'WooCommerce_PDF_Invoices' ) ) {
                $section_title = 'Message Settings in Edit Order Page and Invoices' ;
            } else {
                $section_title = 'Message Settings in Edit Order Page' ;
            }
            $newcombinedarray = fp_order_status() ;
            $categorylist     = fp_product_category() ;
            return apply_filters( 'woocommerce_fpredeeming' , array(
                array(
                    'type' => 'rs_modulecheck_start' ,
                ) ,
                array(
                    'name' => __( 'Redeeming Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_activate_redeeming_module'
                ) ,
                array(
                    'type' => 'rs_enable_disable_redeeming_module' ,
                ) ,
                array(
                    'name'     => __( 'Apply Redeeming Before Tax' , SRP_LOCALE ) ,
                    'desc'     => 'Works with WooCommerce Versions 2.2 or older' ,
                    'id'       => 'rs_apply_redeem_before_tax' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_apply_redeem_before_tax' ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => false ,
                ) ,
                array(
                    'name'     => __( 'Free Shipping when Reward Points is Redeemed' , SRP_LOCALE ) ,
                    'id'       => 'rs_apply_shipping_tax' ,
                    'std'      => '2' ,
                    'default'  => '2' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_apply_shipping_tax' ,
                    'options'  => array(
                        '1' => __( 'Enable' , SRP_LOCALE ) ,
                        '2' => __( 'Disable' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_activate_redeeming_module' ) ,
                array(
                    'type' => 'rs_modulecheck_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Reward Points Redeeming Order Status Settings' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_redeeming_status_setting' ,
                ) ,
                array(
                    'name'     => __( 'Redeemed Points will be deducted when the Order Status reaches' , SRP_LOCALE ) ,
                    'desc'     => __( 'Points will deduct from the account only when the order status matches with any one of the statuses selected in this field & the deducted points for the corresponding order will add back to the account when the status change to any other that is not selected in this field.
<br><br><b>Example:</b><br>
Selected only "Pending Payment, On-Hold, Processing & Completed" statuses in this field so that points will be deducted from the account once the order status reached any one of the selected statuses. The deducted points will be added back to the account when changed to any other status(ex. Canceled/Refunded/Failed).' , SRP_LOCALE ) ,
                    'id'       => 'rs_order_status_control_redeem' ,
                    'std'      => array( 'completed' , 'pending' , 'processing' , 'on-hold' ) ,
                    'default'  => array( 'completed' , 'pending' , 'processing' , 'on-hold' ) ,
                    'type'     => 'multiselect' ,
                    'options'  => $newcombinedarray ,
                    'newids'   => 'rs_order_status_control_redeem' ,
                    'desc_tip' => false ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_redeeming_status_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Member Level Settings for Redeeming' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_member_level_setting_for_redeem' ,
                ) ,
                array(
                    'name'     => __( 'Priority Level Selection' , SRP_LOCALE ) ,
                    'desc'     => __( 'If more than one type(level) is enabled then use the highest/lowest percentage' , SRP_LOCALE ) ,
                    'id'       => 'rs_choose_priority_level_selection_for_redeem' ,
                    'class'    => 'rs_choose_priority_level_selection_for_redeem' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'radio' ,
                    'newids'   => 'rs_choose_priority_level_selection_for_redeem' ,
                    'options'  => array(
                        '1' => __( 'Use the level that gives highest percentage' , SRP_LOCALE ) ,
                        '2' => __( 'Use the level that gives lowest percentage' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_member_level_setting_for_redeem' , 'class' => 'rs_member_level_setting_for_redeem' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Reward Points Redeeming Percentage based on User Role' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_user_role_reward_points_for_redeem' ,
                ) ,
                array(
                    'name'    => __( 'User Role based Redeeming Level' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to modify Redeeming points based on user role' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_user_role_based_reward_points_for_redeem' ,
                    'css'     => 'min-width:150px;' ,
                    'std'     => 'yes' ,
                    'default' => 'yes' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_enable_user_role_based_reward_points_for_redeem' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_user_role_reward_points_for_redeem' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Reward Points Redeeming Percentage based on Earned Points' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_member_level_earning_points_for_redeem' ,
                ) ,
                array(
                    'name'    => __( 'Points to Redeem based on Earned Points' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to modify Redeeming Points percentage based on earned points' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_redeem_level_based_reward_points' ,
                    'css'     => 'min-width:150px;' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_enable_earned_level_based_reward_points_for_redeem' ,
                ) ,
                array(
                    'name'    => __( 'Earned Points is decided' , SRP_LOCALE ) ,
                    'id'      => 'rs_select_redeem_points_based_on' ,
                    'css'     => 'min-width:150px;' ,
                    'std'     => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_select_redeem_points_based_on' ,
                    'options' => array(
                        '1' => __( 'Based on Total Earned Points' , SRP_LOCALE ) ,
                        '2' => __( 'Based on Current Points' , SRP_LOCALE ) ) ,
                ) ,
                array(
                    'type' => 'rs_user_role_dynamics_for_redeem' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_member_level_earning_points_for_redeem' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Reward Points Redeeming Percentage based on Purchase History' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_member_level_redeem_points_purchase_history' ,
                ) ,
                array(
                    'name'    => __( 'Purchase History based on Redeeming Level' , SRP_LOCALE ) ,
                    'desc'    => __( 'Enable this option to modify Redeeming points based on Purchase history' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_user_purchase_history_based_reward_points_redeem' ,
                    'css'     => 'min-width:150px;' ,
                    'std'     => 'yes' ,
                    'default' => 'yes' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_enable_user_purchase_history_based_reward_points_redeem' ,
                ) ,
                array(
                    'type' => 'rs_user_purchase_history_redeem' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_member_level_earning_points_purchase_history' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Redeeming Settings for Cart Page' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_redeem_settings'
                ) ,
                array(
                    'name'    => __( 'Enable Automatic Points Redeeming in Cart Page' , SRP_LOCALE ) ,
                    'desc'    => __( 'When enabled, available reward points will be automatically applied on cart to get a discount' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_disable_auto_redeem_points' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_disable_auto_redeem_points' ,
                ) ,
                array(
                    'name'    => __( 'Enable Automatic Points Redeeming in Checkout Page' , SRP_LOCALE ) ,
                    'desc'    => __( 'When enabled, available reward points will be automatically applied on checkout to get a discount when the page is redirected to checkout directly from shop page' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_disable_auto_redeem_checkout' ,
                    'type'    => 'checkbox' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'newids'  => 'rs_enable_disable_auto_redeem_checkout' ,
                ) ,
                array(
                    'name'    => __( 'Manual Redeeming Field Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_redeem_field_type_option' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_redeem_field_type_option' ,
                    'options' => array(
                        '1' => __( 'Default' , SRP_LOCALE ) ,
                        '2' => __( 'Button' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'              => __( 'Percentage of Cart Total to be Redeemed' , SRP_LOCALE ) ,
                    'desc'              => __( 'Enter the Percentage of the cart total that has to be Redeemed' , SRP_LOCALE ) ,
                    'id'                => 'rs_percentage_cart_total_redeem' ,
                    'std'               => '100 ' ,
                    'default'           => '100' ,
                    'type'              => 'number' ,
                    'newids'            => 'rs_percentage_cart_total_redeem' ,
                    'desc_tip'          => true ,
                    'custom_attributes' => array(
                        'min' => 1
                    )
                ) ,
                array(
                    'name'    => __( 'Redeeming Button Notice' , SRP_LOCALE ) ,
                    'id'      => 'rs_redeeming_button_option_message' ,
                    'std'     => __( '[cartredeempoints] points worth of [currencysymbol] [pointsvalue] will be Redeemed' , SRP_LOCALE ) ,
                    'default' => __( '[cartredeempoints] points worth of [currencysymbol] [pointsvalue] will be Redeemed' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_redeeming_button_option_message' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_redeem_settings' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Redeeming Settings for Checkout Page' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_restriction_in_checkout'
                ) ,
                array(
                    'name'    => __( 'Show/hide Redeeming Field in Checkout Page' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_redeem_field_checkout' ,
                    'std'     => '2' ,
                    'default' => '2' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_show_hide_redeem_field_checkout' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'    => __( 'Manual Redeeming Field Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_redeem_field_type_option_checkout' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'newids'  => 'rs_redeem_field_type_option_checkout' ,
                    'options' => array(
                        '1' => __( 'Default' , SRP_LOCALE ) ,
                        '2' => __( 'Button' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'              => __( 'Percentage of Cart Total to be Redeemed' , SRP_LOCALE ) ,
                    'desc'              => __( 'Enter the Percentage of the cart total that has to be Redeemed' , SRP_LOCALE ) ,
                    'id'                => 'rs_percentage_cart_total_redeem_checkout' ,
                    'std'               => '100 ' ,
                    'default'           => '100' ,
                    'type'              => 'number' ,
                    'newids'            => 'rs_percentage_cart_total_redeem_checkout' ,
                    'desc_tip'          => true ,
                    'custom_attributes' => array(
                        'min' => 1
                    )
                ) ,
                array(
                    'name'     => __( 'Show/Hide WooCommerce Coupon Field' , SRP_LOCALE ) ,
                    'id'       => 'rs_show_hide_coupon_field_checkout' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_show_hide_coupon_field_checkout' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Redeeming Field label' , SRP_LOCALE ) ,
                    'desc'     => __( 'This Text will be displayed as redeeming field label in checkout page' , SRP_LOCALE ) ,
                    'id'       => 'rs_reedming_field_label_checkout' ,
                    'std'      => __( 'Have Reward Points ?' , SRP_LOCALE ) ,
                    'default'  => __( 'Have Reward Points ?' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_reedming_field_label_checkout' ,
                    'class'    => 'rs_reedming_field_label_checkout' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Redeeming Field Link label' , SRP_LOCALE ) ,
                    'desc'     => __( 'This Text will be displayed as redeeming field link label in checkout page' , SRP_LOCALE ) ,
                    'id'       => 'rs_reedming_field_link_label_checkout' ,
                    'std'      => __( 'Redeem it' , SRP_LOCALE ) ,
                    'default'  => __( 'Redeem it' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_reedming_field_link_label_checkout' ,
                    'class'    => 'rs_reedming_field_link_label_checkout' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Redeeming Link Call to Action' , SRP_LOCALE ) ,
                    'desc'     => __( 'Show/Hide Redeem It Link Call To Action in WooCommerce' , SRP_LOCALE ) ,
                    'id'       => 'rs_show_hide_redeem_it_field_checkout' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_show_hide_redeem_it_field_checkout' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Redeeming Button Message ' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message for the Redeeming Button' , SRP_LOCALE ) ,
                    'id'       => 'rs_redeeming_button_option_message_checkout' ,
                    'std'      => __( '[cartredeempoints] points worth of [currencysymbol] [pointsvalue] will be Redeemed' , SRP_LOCALE ) ,
                    'default'  => __( '[cartredeempoints] points worth of [currencysymbol] [pointsvalue] will be Redeemed' , SRP_LOCALE ) ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_redeeming_button_option_message_checkout' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_restriction_in_checkout' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Redeeming Settings for Cart and Checkout Page' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_restriction_in_cart_and_checkout'
                ) ,
                array(
                    'name'    => __( 'Redeeming Field Label' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_redeem_caption' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_redeem_caption' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Redeeming Field Label' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Label which will be displayed in Redeem Field' , SRP_LOCALE ) ,
                    'id'       => 'rs_redeem_field_caption' ,
                    'std'      => __( 'Redeem your Reward Points:' , SRP_LOCALE ) ,
                    'default'  => __( 'Redeem your Reward Points:' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_redeem_field_caption' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide Redeeming Field Placeholder' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_redeem_placeholder' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_redeem_placeholder' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Placeholder' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Placeholder which will be displayed in Redeem Field' , SRP_LOCALE ) ,
                    'id'       => 'rs_redeem_field_placeholder' ,
                    'std'      => __( 'Reward Points to Enter' , SRP_LOCALE ) ,
                    'default'  => __( 'Reward Points to Enter' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_redeem_field_placeholder' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Redeeming Field Submit Button Caption' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Label which will be displayed in Submit Button' , SRP_LOCALE ) ,
                    'id'       => 'rs_redeem_field_submit_button_caption' ,
                    'std'      => __( 'Apply Reward Points' , SRP_LOCALE ) ,
                    'default'  => __( 'Apply Reward Points' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_redeem_field_submit_button_caption' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Coupon Label Settings' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed in Cart Subtotal' , SRP_LOCALE ) ,
                    'id'       => 'rs_coupon_label_message' ,
                    'std'      => __( 'Redeemed Points Value' , SRP_LOCALE ) ,
                    'default'  => __( 'Redeemed Points Value' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_coupon_label_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Extra Class Name for Redeeming Field Submit Button' , SRP_LOCALE ) ,
                    'desc'     => __( 'Add Extra Class Name to the Cart Apply Reward Points Button, Don\'t Enter dot(.) before Class Name' , SRP_LOCALE ) ,
                    'id'       => 'rs_extra_class_name_apply_reward_points' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_extra_class_name_apply_reward_points' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_restriction_in_cart_and_checkout' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Redeeming Restriction' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_cart_remaining_setting'
                ) ,
                array(
                    'name'     => __( 'Redeeming/WooCommerce Coupon Field display on cart & checkout' , SRP_LOCALE ) ,
                    'id'       => 'rs_show_hide_redeem_field' ,
                    'css'      => '' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'type'     => 'select' ,
                    'newids'   => 'rs_show_hide_redeem_field' ,
                    'options'  => array(
                        '1' => __( 'Display Both' , SRP_LOCALE ) ,
                        '2' => __( 'Hide WooCommerce Coupon Field' , SRP_LOCALE ) ,
                        '3' => __( 'Hide Redeeming Points Field' , SRP_LOCALE ) ,
                        '4' => __( 'Hide Both' , SRP_LOCALE ) ,
                        '5' => __( 'Hide one when coupon/point is used' , SRP_LOCALE )
                    ) ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Redeemed Points is applied on' , SRP_LOCALE ) ,
                    'id'      => 'rs_apply_redeem_basedon_cart_or_product_total' ,
                    'newids'  => 'rs_apply_redeem_basedon_cart_or_product_total' ,
                    'class'   => 'rs_apply_redeem_basedon_cart_or_product_total' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Cart Subtotal' , SRP_LOCALE ) ,
                        '2' => __( 'Product Total' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Redeeming Field Selection' , SRP_LOCALE ) ,
                    'id'       => 'rs_hide_redeeming_field' ,
                    'class'    => 'rs_hide_redeeming_field' ,
                    'std'      => '1' ,
                    'default'  => '1' ,
                    'newids'   => 'rs_hide_redeeming_field' ,
                    'type'     => 'select' ,
                    'options'  => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                    'desc_tip' => true ,
                    'desc'     => __( 'This option can be used to controls the redeeming field display when redeeming is restricted to specific products/categories.' , SRP_LOCALE )
                ) ,
                array(
                    'name'    => __( 'Enable to Restrict Redeeming for Sale Price Product(s)' , SRP_LOCALE ) ,
                    'id'      => 'rs_restrict_sale_price_for_redeeming' ,
                    'class'   => 'rs_restrict_sale_price_for_redeeming' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_restrict_sale_price_for_redeeming' ,
                ) ,
                array(
                    'name'    => __( 'Error Message' , SRP_LOCALE ) ,
                    'id'      => 'rs_redeeming_message_restrict_for_sale_price_product' ,
                    'std'     => __( 'Sorry, redeeming is not applicable for sale price product(s)' , SRP_LOCALE ) ,
                    'default' => __( 'Sorry, redeeming is not applicable for sale price product(s)' , SRP_LOCALE ) ,
                    'type'    => 'textarea' ,
                    'newids'  => 'rs_redeeming_message_restrict_for_sale_price_product' ,
                ) ,
                array(
                    'name'   => __( 'Enable Redeeming for Selected Products' , SRP_LOCALE ) ,
                    'id'     => 'rs_enable_redeem_for_selected_products' ,
                    'type'   => 'checkbox' ,
                    'newids' => 'rs_enable_redeem_for_selected_products' ,
                ) ,
                array(
                    'type' => 'include_product_selection' ,
                ) ,
                array(
                    'name'    => __( 'Products excluded from Redeeming' , SRP_LOCALE ) ,
                    'id'      => 'rs_exclude_products_for_redeeming' ,
                    'class'   => 'rs_exclude_products_for_redeeming' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_exclude_products_for_redeeming' ,
                ) ,
                array(
                    'type' => 'exclude_product_selection' ,
                ) ,
                array(
                    'name'    => __( 'Enable Redeeming for Selected Category' , SRP_LOCALE ) ,
                    'id'      => 'rs_enable_redeem_for_selected_category' ,
                    'class'   => 'rs_enable_redeem_for_selected_category' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_enable_redeem_for_selected_category' ,
                ) ,
                array(
                    'name'     => __( 'Categories allowed for Redeeming' , SRP_LOCALE ) ,
                    'desc'     => __( 'Select Category to enable redeeming' , SRP_LOCALE ) ,
                    'id'       => 'rs_select_category_to_enable_redeeming' ,
                    'class'    => 'rs_select_category_to_enable_redeeming' ,
                    'css'      => 'min-width:350px' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'multiselect' ,
                    'newids'   => 'rs_select_category_to_enable_redeeming' ,
                    'options'  => $categorylist ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Exclude Category for Redeeming' , SRP_LOCALE ) ,
                    'id'      => 'rs_exclude_category_for_redeeming' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_exclude_category_for_redeeming' ,
                ) ,
                array(
                    'name'     => __( 'Categories excluded from Redeeming' , SRP_LOCALE ) ,
                    'desc'     => __( 'Select Category to enable redeeming' , SRP_LOCALE ) ,
                    'id'       => 'rs_exclude_category_to_enable_redeeming' ,
                    'class'    => 'rs_exclude_category_to_enable_redeeming' ,
                    'css'      => 'min-width:350px' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'multiselect' ,
                    'newids'   => 'rs_exclude_category_to_enable_redeeming' ,
                    'options'  => $categorylist ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Enable to hide available reward points message in cart and checkout page when points used in any one of the pages' , SRP_LOCALE ) ,
                    'id'      => 'rs_available_points_display' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'checkbox' ,
                    'newids'  => 'rs_available_points_display' ,
                ) ,
                array(
                    'name'     => __( 'Maximum Redeeming Threshold Percentage for Auto Redeeming' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Percentage of the cart total that has to be Auto Redeemed' , SRP_LOCALE ) ,
                    'id'       => 'rs_percentage_cart_total_auto_redeem' ,
                    'std'      => '100 ' ,
                    'default'  => '100' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_percentage_cart_total_auto_redeem' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Maximum Redeeming Threshold Value (Discount) Type' , SRP_LOCALE ) ,
                    'id'      => 'rs_max_redeem_discount' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'newids'  => 'rs_max_redeem_discount' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'By Fixed Value' , SRP_LOCALE ) ,
                        '2' => __( 'By Percentage of Cart Total' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Maximum Redeeming Threshold Value (Discount) for Order in ' . get_woocommerce_currency_symbol() , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter a Fixed or Decimal Number greater than 0' , SRP_LOCALE ) ,
                    'id'       => 'rs_fixed_max_redeem_discount' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_fixed_max_redeem_discount' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Maximum Redeeming Threshold Value (Discount) for Order in Percentage %' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter a Fixed or Decimal Number greater than 0' , SRP_LOCALE ) ,
                    'id'       => 'rs_percent_max_redeem_discount' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_percent_max_redeem_discount' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Minimum Points required for Redeeming for the First Time' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter Minimum Points to be Earned for Redeeming First Time in Cart/Checkout' , SRP_LOCALE ) ,
                    'id'       => 'rs_first_time_minimum_user_points' ,
                    'std'      => '' ,
                    'default'  => '' ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_first_time_minimum_user_points' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide First Time Redeeming Minimum Points Required Warning Message' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_first_redeem_error_message' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_first_redeem_error_message' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Error Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when the user doesn\'t have enough points for first time redeeming' , SRP_LOCALE ) ,
                    'id'       => 'rs_min_points_first_redeem_error_message' ,
                    'std'      => __( 'You need Minimum of [firstredeempoints] Points when redeeming for the First time' , SRP_LOCALE ) ,
                    'default'  => __( 'You need Minimum of [firstredeempoints] Points when redeeming for the First time' , SRP_LOCALE ) ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_min_points_first_redeem_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Minimum Points required for Redeeming after First Redeeming' , SRP_LOCALE ) ,
                    'id'      => 'rs_minimum_user_points_to_redeem' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_minimum_user_points_to_redeem' ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide Minimum Points required for Redeeming after First Redeeming' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_after_first_redeem_error_message' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_after_first_redeem_error_message' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Error Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when the Current User doesn\'t have minimum points for Redeeming ' , SRP_LOCALE ) ,
                    'id'       => 'rs_min_points_after_first_error' ,
                    'std'      => __( 'You need minimum of [points_after_first_redeem] Points for Redeeming' , SRP_LOCALE ) ,
                    'default'  => __( 'You need minimum of [points_after_first_redeem] Points for Redeeming' , SRP_LOCALE ) ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_min_points_after_first_error' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Minimum Points to be entered for Redeeming' , SRP_LOCALE ) ,
                    'id'      => 'rs_minimum_redeeming_points' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_minimum_redeeming_points' ,
                ) ,
                array(
                    'name'    => __( 'Maximum Points above which points cannot be Redeemed' , SRP_LOCALE ) ,
                    'id'      => 'rs_maximum_redeeming_points' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_maximum_redeeming_points' ,
                ) ,
                array(
                    'name'    => __( 'Minimum Cart Total to Redeem Point(s)' , SRP_LOCALE ) ,
                    'id'      => 'rs_minimum_cart_total_points' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_minimum_cart_total_points' ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide Minimum Cart Total to Redeem Point(s)' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_minimum_cart_total_error_message' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_minimum_cart_total_error_message' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Error Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when current Cart total is less than minimum Cart Total for Redeeming' , SRP_LOCALE ) ,
                    'id'       => 'rs_min_cart_total_redeem_error' ,
                    'std'      => __( 'You need minimum cart Total of [currencysymbol][carttotal] in order to Redeem' , SRP_LOCALE ) ,
                    'default'  => __( 'You need minimum cart Total of [currencysymbol][carttotal] in order to Redeem' , SRP_LOCALE ) ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_min_cart_total_redeem_error' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Maximum Cart Total to Redeem Point(s)' , SRP_LOCALE ) ,
                    'id'      => 'rs_maximum_cart_total_points' ,
                    'std'     => '' ,
                    'default' => '' ,
                    'type'    => 'text' ,
                    'newids'  => 'rs_maximum_cart_total_points' ,
                ) ,
                array(
                    'name'    => __( 'Show/Hide Maximum Cart Total to Redeem Point(s)' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_maximum_cart_total_error_message' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_maximum_cart_total_error_message' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Error Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when current Cart total is less than Maximum Cart Total for Redeeming' , SRP_LOCALE ) ,
                    'id'       => 'rs_max_cart_total_redeem_error' ,
                    'std'      => __( 'You Cannot Redeem Points Because you Reach the Maximum Cart total [currencysymbol][carttotal]' , SRP_LOCALE ) ,
                    'default'  => __( 'You Cannot Redeem Points Because you Reach the Maximum Cart total [currencysymbol][carttotal]' , SRP_LOCALE ) ,
                    'type'     => 'textarea' ,
                    'newids'   => 'rs_max_cart_total_redeem_error' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Prevent Coupon Usage when points are redeemed' , SRP_LOCALE ) ,
                    'id'      => 'rs_coupon_applied_individual' ,
                    'class'   => 'rs_coupon_applied_individual' ,
                    'newids'  => 'rs_coupon_applied_individual' ,
                    'std'     => 'no' ,
                    'default' => 'no' ,
                    'type'    => 'checkbox' ,
                    'desc'    => __( 'Enable this option to prevent coupon usage when points are redeemed' , SRP_LOCALE ) ,
                ) ,
                array(
                    'name'     => __( 'Error Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Text for Error Message for redeeming Coupon When applied with other coupon' , SRP_LOCALE ) ,
                    'id'       => 'rs_coupon_applied_individual_error_msg' ,
                    'class'    => 'rs_coupon_applied_individual_error_msg' ,
                    'newids'   => 'rs_coupon_applied_individual_error_msg' ,
                    'css'      => 'min-width:400px;' ,
                    'std'      => 'Coupon cannot be applied when points are redeemed' ,
                    'default'  => 'Coupon cannot be applied when points are redeemed' ,
                    'type'     => 'textarea' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_cart_remaining_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( "$section_title" , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_err_msg_setting_in_edit_order'
                ) ,
                array(
                    'name'   => __( 'Display Redeemed Points' , SRP_LOCALE ) ,
                    'desc'   => __( 'Enable Message for Redeem Points' , SRP_LOCALE ) ,
                    'id'     => 'rs_enable_msg_for_redeem_points' ,
                    'newids' => 'rs_enable_msg_for_redeem_points' ,
                    'class'  => 'rs_enable_msg_for_redeem_points' ,
                    'type'   => 'checkbox' ,
                ) ,
                array(
                    'name'     => __( 'Message to Redeemed Points' , SRP_LOCALE ) ,
                    'desc'     => __( 'Message to Redeemed Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_msg_for_redeem_points' ,
                    'newids'   => 'rs_msg_for_redeem_points' ,
                    'class'    => 'rs_msg_for_redeem_points' ,
                    'std'      => __( 'Points Redeemed in this Order [redeempoints]' , SRP_LOCALE ) ,
                    'default'  => __( 'Points Redeemed in this Order [redeempoints]' , SRP_LOCALE ) ,
                    'type'     => 'textarea' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_err_msg_setting_in_edit_order' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Error Message Settings for Redeeming Field' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => '_rs_err_msg_setting'
                ) ,
                array(
                    'name'     => __( 'Error Message to display when User enters less than Minimum Points[Default Type]' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when Entered Points is less than Minimum Redeeming Points which is set in this Page' , SRP_LOCALE ) ,
                    'id'       => 'rs_minimum_redeem_point_error_message' ,
                    'std'      => __( 'Please Enter Points more than [rsminimumpoints]' , SRP_LOCALE ) ,
                    'default'  => __( 'Please Enter Points more than [rsminimumpoints]' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_minimum_redeem_point_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to display when User enters more than Maximum Points[Default Type]' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when Entered Points is more than Maximum Redeeming Points which is set in this Page' , SRP_LOCALE ) ,
                    'id'       => 'rs_maximum_redeem_point_error_message' ,
                    'std'      => __( 'Please Enter Points less than [rsmaximumpoints]' , SRP_LOCALE ) ,
                    'default'  => __( 'Please Enter Points less than [rsmaximumpoints]' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_maximum_redeem_point_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to display when User enters less than the Minimum Points  or more than Maximum Points[Default Type]' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when Maximum and Minimum Redeeming Points are Equal which is set in this Page' , SRP_LOCALE ) ,
                    'id'       => 'rs_minimum_and_maximum_redeem_point_error_message' ,
                    'std'      => __( 'Please Enter [rsequalpoints] Points' , SRP_LOCALE ) ,
                    'default'  => __( 'Please Enter [rsequalpoints] Points' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_minimum_and_maximum_redeem_point_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to display when User enters less than Minimum Points[Button Type]' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when Entered Points is less than Minimum Redeeming Points which is set in this Page' , SRP_LOCALE ) ,
                    'id'       => 'rs_minimum_redeem_point_error_message_for_button_type' ,
                    'std'      => __( 'You cannot redeem because the current points to be redeemed is less than [rsminimumpoints] Points' , SRP_LOCALE ) ,
                    'default'  => __( 'You cannot redeem because the current points to be redeemed is less than [rsminimumpoints] Points' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_minimum_redeem_point_error_message_for_button_type' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to display when User enters more than Maximum Points[Button Type]' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when Entered Points is more than Maximum Redeeming Points which is set in this Page' , SRP_LOCALE ) ,
                    'id'       => 'rs_maximum_redeem_point_error_message_for_button_type' ,
                    'std'      => __( 'You cannot redeem because the current points to be redeemed is more than [rsmaximumpoints] points' , SRP_LOCALE ) ,
                    'default'  => __( 'You cannot redeem because the current points to be redeemed is more than [rsmaximumpoints] points' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_maximum_redeem_point_error_message_for_button_type' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Error Message to display when User enters less than the Minimum Points  or more than Maximum Points[Button Type]' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when Maximum and Minimum Redeeming Points are Equal which is set in this Page' , SRP_LOCALE ) ,
                    'id'       => 'rs_minimum_and_maximum_redeem_point_error_message_for_buttontype' ,
                    'std'      => __( 'You cannot redeem because the points to be redeemed is not equal to [rsequalpoints] Points' , SRP_LOCALE ) ,
                    'default'  => __( 'You cannot redeem because the points to be redeemed is not equal to [rsequalpoints] Points' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_minimum_and_maximum_redeem_point_error_message_for_buttontype' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Redeeming Field Empty Error Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when Redeem Field has Empty Value' , SRP_LOCALE ) ,
                    'id'       => 'rs_redeem_empty_error_message' ,
                    'std'      => __( 'No Reward Points Entered' , SRP_LOCALE ) ,
                    'default'  => __( 'No Reward Points Entered' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_redeem_empty_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Unwanted Characters in Redeeming Field Error Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when redeeming field value contain characters' , SRP_LOCALE ) ,
                    'id'       => 'rs_redeem_character_error_message' ,
                    'std'      => __( 'Please Enter Only Numbers' , SRP_LOCALE ) ,
                    'default'  => __( 'Please Enter Only Numbers' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_redeem_character_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'     => __( 'Insufficient Points for Redeeming Error Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when Entered Reward Points is more than Earned Reward Points' , SRP_LOCALE ) ,
                    'id'       => 'rs_redeem_max_error_message' ,
                    'std'      => __( 'Reward Points you entered is more than Your Earned Reward Points' , SRP_LOCALE ) ,
                    'default'  => __( 'Reward Points you entered is more than Your Earned Reward Points' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_redeem_max_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Current User Points is Empty Error Message' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_points_empty_error_message' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_points_empty_error_message' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Error Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when the Current User Points is Empty' , SRP_LOCALE ) ,
                    'id'       => 'rs_current_points_empty_error_message' ,
                    'std'      => __( 'You don\'t have Points for Redeeming' , SRP_LOCALE ) ,
                    'default'  => __( 'You don\'t have Points for Redeeming' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_current_points_empty_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array(
                    'name'    => __( 'Error Message to display when Auto Redeeming/WooCommerce Coupon is not applicable to use in the cart' , SRP_LOCALE ) ,
                    'id'      => 'rs_show_hide_auto_redeem_not_applicable' ,
                    'std'     => '1' ,
                    'default' => '1' ,
                    'newids'  => 'rs_show_hide_auto_redeem_not_applicable' ,
                    'type'    => 'select' ,
                    'options' => array(
                        '1' => __( 'Show' , SRP_LOCALE ) ,
                        '2' => __( 'Hide' , SRP_LOCALE ) ,
                    ) ,
                ) ,
                array(
                    'name'     => __( 'Error Message' , SRP_LOCALE ) ,
                    'desc'     => __( 'Enter the Message which will be displayed when Auto Redeeming/WooCommerce Coupon is not applicable to use in the cart' , SRP_LOCALE ) ,
                    'id'       => 'rs_auto_redeem_not_applicable_error_message' ,
                    'std'      => __( 'Auto Redeem is not applicable to your cart contents.' , SRP_LOCALE ) ,
                    'default'  => __( 'Auto Redeem is not applicable to your cart contents.' , SRP_LOCALE ) ,
                    'type'     => 'text' ,
                    'newids'   => 'rs_auto_redeem_not_applicable_error_message' ,
                    'desc_tip' => true ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => '_rs_err_msg_setting' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                array(
                    'type' => 'rs_wrapper_start' ,
                ) ,
                array(
                    'name' => __( 'Shortcodes used in Redeeming Module' , SRP_LOCALE ) ,
                    'type' => 'title' ,
                    'id'   => 'rs_shortcodes_in_checkout' ,
                ) ,
                array(
                    'type' => 'title' ,
                    'desc' => '<b>[cartredeempoints]</b> - To display points can redeem based on cart total amount<br><br>'
                    . '<b>[currencysymbol]</b> - To display currency symbol<br><br>'
                    . '<b>[pointsvalue]</b> - To display currency value equivalent of redeeming points<br><br>'
                    . '<b>[productname]</b> - To display product name<br><br>'
                    . '<b>[firstredeempoints] </b> - To display points required for first time redeeming<br><br>'
                    . '<b>[points_after_first_redeem]</b> - To display points required after first redeeming<br><br>'
                    . '<b>[rsminimumpoints]</b> - To display minimum points required to redeem<br><br>'
                    . '<b>[rsmaximumpoints]</b> - To display maximum points required to redeem<br><br>'
                    . '<b>[rsequalpoints]</b> - To display exact points to redeem<br><br>'
                    . '<b>[carttotal]</b> - To display cart total value<br><br>' ,
                ) ,
                array( 'type' => 'sectionend' , 'id' => 'rs_shortcodes_in_checkout' ) ,
                array(
                    'type' => 'rs_wrapper_end' ,
                ) ,
                    ) ) ;
        }

        /**
         * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
         */
        public static function reward_system_register_admin_settings() {
            woocommerce_admin_fields( RSRedeemingModule::reward_system_admin_fields() ) ;
        }

        /**
         * Update the Settings on Save Changes may happen in SUMO Reward Points
         */
        public static function reward_system_update_settings() {
            woocommerce_update_options( RSRedeemingModule::reward_system_admin_fields() ) ;
            if ( isset( $_POST[ 'rs_select_products_to_enable_redeeming' ] ) ) {
                update_option( 'rs_select_products_to_enable_redeeming' , $_POST[ 'rs_select_products_to_enable_redeeming' ] ) ;
            } else {
                update_option( 'rs_select_products_to_enable_redeeming' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_exclude_products_to_enable_redeeming' ] ) ) {
                update_option( 'rs_exclude_products_to_enable_redeeming' , $_POST[ 'rs_exclude_products_to_enable_redeeming' ] ) ;
            } else {
                update_option( 'rs_exclude_products_to_enable_redeeming' , '' ) ;
            }
            if ( isset( $_POST[ 'rs_redeeming_module_checkbox' ] ) ) {
                update_option( 'rs_redeeming_activated' , $_POST[ 'rs_redeeming_module_checkbox' ] ) ;
            } else {
                update_option( 'rs_redeeming_activated' , 'no' ) ;
            }
            if ( isset( $_POST[ 'rewards_dynamic_rule_for_redeem' ] ) ) {
                update_option( 'rewards_dynamic_rule_for_redeem' , $_POST[ 'rewards_dynamic_rule_for_redeem' ] ) ;
            } else {
                update_option( 'rewards_dynamic_rule_for_redeem' , '' ) ;
            }
            if ( isset( $_POST[ 'rewards_dynamic_rule_purchase_history_redeem' ] ) ) {
                update_option( 'rewards_dynamic_rule_purchase_history_redeem' , $_POST[ 'rewards_dynamic_rule_purchase_history_redeem' ] ) ;
            } else {
                update_option( 'rewards_dynamic_rule_purchase_history_redeem' , '' ) ;
            }
        }

        /**
         * Initialize the Default Settings by looping this function
         */
        public static function set_default_value() {
            foreach ( RSRedeemingModule::reward_system_admin_fields() as $setting )
                if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
                    add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
                }
        }

        public static function reset_redeeming_module() {
            $settings = RSRedeemingModule::reward_system_admin_fields() ;
            RSTabManagement::reset_settings( $settings ) ;
            update_option( 'rs_redeem_point' , '1' ) ;
            update_option( 'rs_redeem_point_value' , '1' ) ;
        }

        public static function enable_module() {
            RSModulesTab::checkbox_for_module( get_option( 'rs_redeeming_activated' ) , 'rs_redeeming_module_checkbox' , 'rs_redeeming_activated' ) ;
        }

        public static function setting_for_hide_redeem_field_when_sumo_discount_is_active( $settings ) {
            $updated_settings = array() ;
            foreach ( $settings as $section ) {
                if ( isset( $section[ 'id' ] ) && ('_rs_cart_remaining_setting' === $section[ 'id' ]) &&
                        isset( $section[ 'type' ] ) && ('sectionend' === $section[ 'type' ]) ) {
                    $updated_settings[] = array(
                        'name'     => __( 'Show Redeeming Field' , SRP_LOCALE ) ,
                        'id'       => 'rs_show_redeeming_field' ,
                        'std'      => '1' ,
                        'default'  => '1' ,
                        'type'     => 'select' ,
                        'newids'   => 'rs_show_redeeming_field' ,
                        'options'  => array(
                            '1' => __( 'Always' , SRP_LOCALE ) ,
                            '2' => __( 'When Price is not altered through SUMO Discounts Plugin' , SRP_LOCALE ) ,
                        ) ,
                        'desc_tip' => true ,
                            ) ;
                }
                $updated_settings[] = $section ;
            }
            return $updated_settings ;
        }

        /*
         * Function to select products to exclude
         */

        public static function rs_select_product_to_exclude() {
            $field_id    = "rs_exclude_products_to_enable_redeeming" ;
            $field_label = "Products excluded from Redeeming" ;
            $getproducts = get_option( 'rs_exclude_products_to_enable_redeeming' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

        /*
         * Function to select products to include
         */

        public static function rs_select_product_to_include() {
            $field_id    = "rs_select_products_to_enable_redeeming" ;
            $field_label = "Products allowed for Redeeming" ;
            $getproducts = get_option( 'rs_select_products_to_enable_redeeming' ) ;
            echo rs_function_to_add_field_for_product_select( $field_id , $field_label , $getproducts ) ;
        }

    }

    RSRedeemingModule::init() ;
}