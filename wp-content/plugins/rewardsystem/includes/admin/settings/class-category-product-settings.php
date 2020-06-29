<?php
/*
 * Simple Product Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSCategoryField' ) ) {

    class RSCategoryField {

        public static function init() {
            add_action( 'product_cat_add_form_fields' , array( __CLASS__ , 'rs_admin_setting_for_category_page' ) ) ;

            add_action( 'product_cat_edit_form_fields' , array( __CLASS__ , 'rs_edit_admin_settings_for_category_page' ) , 10 , 2 ) ;

            add_action( 'created_term' , array( __CLASS__ , 'rs_save_admin_settings_for_category_page' ) , 10 , 3 ) ;

            add_action( 'edit_term' , array( __CLASS__ , 'rs_save_admin_settings_for_category_page' ) , 10 , 3 ) ;
        }

        public static function rs_admin_setting_for_category_page() {
            if ( get_option( 'rs_point_price_activated' ) == 'yes' && get_option( 'rs_enable_product_category_level_for_points_price' ) == 'yes' ) {
                ?>
                <h4><?php _e( 'Category Settings for Point Price' , SRP_LOCALE ) ; ?></h4>
                <div class="form-field">
                    <label for="enable_point_price_category"><?php _e( 'Enable Point Pricing' , SRP_LOCALE ) ; ?></label>
                    <select id="enable_point_price_category" name="enable_point_price_category" class="postform">
                        <option value="yes"><?php _e( 'Enable' , SRP_LOCALE ) ; ?></option>
                        <option value="no"><?php _e( 'Disable' , SRP_LOCALE ) ; ?></option>
                    </select>
                    <p><?php
                        _e( 'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
                                . 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ' , SRP_LOCALE ) ;
                        ?></p>
                </div>
                <div class="form-field">
                    <label for="pricing_category_types"><?php _e( 'Pricing Display Type' , SRP_LOCALE ) ; ?></label>
                    <select id = "pricing_category_types" name="pricing_category_types" class="postform">
                        <option value = "1"><?php _e( 'Currency and Point Price' , SRP_LOCALE ) ; ?></option>
                        <option value = "2"><?php _e( 'Only Point Price' , SRP_LOCALE ) ; ?></option>
                    </select>
                    <p><?php
                        _e( 'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
                                . 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ' , SRP_LOCALE ) ;
                        ?></p>
                </div>
                <div class="form-field">
                    <label for="point_price_category_type"><?php _e( 'Point Price Type' , SRP_LOCALE ) ; ?></label>
                    <select id="point_price_category_type" name="point_price_category_type" class="postform">
                        <option value="1"><?php _e( 'By Fixed' , SRP_LOCALE ) ; ?></option>
                        <option value="2"><?php _e( 'Based On Conversion' , SRP_LOCALE ) ; ?></option>
                    </select>
                    <p><?php
                        _e( 'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
                                . 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ' , SRP_LOCALE ) ;
                        ?></p>
                </div>

                <div class="form-field">
                    <label for="rs_category_points_price"><?php _e( 'By fixed Points Price' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="rs_category_points_price" id="rs_category_points_price" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
            <?php } if ( get_option( 'rs_product_purchase_activated' ) == 'yes' && get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'yes' ) { ?>
                <h3><?php _e( 'Category Settings for Reward Points' , SRP_LOCALE ) ; ?></h3>
                <div class="form-field">
                    <label for="enable_reward_system_category"><?php _e( 'Enable SUMO Reward Points for Product Purchase' , SRP_LOCALE ) ; ?></label>
                    <select id="enable_reward_system_category" name="enable_reward_system_category" class="postform">
                        <option value="yes"><?php _e( 'Enable' , SRP_LOCALE ) ; ?></option>
                        <option value="no"><?php _e( 'Disable' , SRP_LOCALE ) ; ?></option>
                    </select>
                    <p><?php
                        _e( 'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
                                . 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ' , SRP_LOCALE ) ;
                        ?></p>
                </div>
                <div class="form-field">
                    <label for="enable_rs_rule"><?php _e( 'Reward Type' , SRP_LOCALE ) ; ?></label>
                    <select id="enable_rs_rule" name="enable_rs_rule" class="postform">
                        <option value="1"><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                        <option value="2"><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="rs_category_points"><?php _e( 'Reward Points' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="rs_category_points" id="rs_category_points" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <div class="form-field">
                    <label for="rs_category_percent"><?php _e( 'Reward Percent in %' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="rs_category_percent" id="rs_category_percent" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
            <?php } if ( get_option( 'rs_referral_activated' ) == 'yes' && get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) == 'yes' ) { ?>
                <div class="form-field">
                    <label for="enable_referral_reward_system_category"><?php _e( 'Enable Referral Reward Points for Product Purchase' , SRP_LOCALE ) ; ?></label>
                    <select id="enable_referral_reward_system_category" name="enable_referral_reward_system_category" class="postform">
                        <option value="yes"><?php _e( 'Enable' , SRP_LOCALE ) ; ?></option>
                        <option value="no"><?php _e( 'Disable' , SRP_LOCALE ) ; ?></option>
                    </select>
                    <p><?php
                        _e( 'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
                                . 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ' , SRP_LOCALE ) ;
                        ?></p>
                </div>
                <div class="form-field">
                    <label for="referral_enable_rs_rule"><?php _e( 'Referral Reward Type' , SRP_LOCALE ) ; ?></label>
                    <select id="referral_enable_rs_rule" name="referral_enable_rs_rule" class="postform">
                        <option value="1"><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                        <option value="2"><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="referral_rs_category_points"><?php _e( 'Referral Reward Points' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="referral_rs_category_points" id="referral_rs_category_points" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <div class="form-field">
                    <label for="referral_rs_category_percent"><?php _e( 'Reward Percent in %' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="referral_rs_category_percent" id="referral_rs_category_percent" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>

                <div class="form-field">
                    <label for="referral_enable_rs_rule_refer"><?php _e( 'Reward Type for Getting Referred' , SRP_LOCALE ) ; ?></label>
                    <select id="referral_enable_rs_rule_refer" name="referral_enable_rs_rule_refer" class="postform">
                        <option value="1"><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                        <option value="2"><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                    </select>
                </div>

                <div class="form-field">
                    <label for="referral_rs_category_points_get_refered"><?php _e( ' Reward Points for Getting Referred' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="referral_rs_category_points_get_refered" id="referral_rs_category_points_get_refered" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>


                <div class="form-field">
                    <label for="referral_rs_category_percent_get_refer"><?php _e( 'Reward Points in Percent % for Getting Referred' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="referral_rs_category_percent_get_refer" id="referral_rs_category_percent_get_refer" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
            <?php } if ( get_option( 'rs_social_reward_activated' ) == 'yes' && get_option( 'rs_enable_product_category_level_for_social_reward' ) == 'yes' ) { ?>
                <div class="form-field">
                    <label for="enable_social_reward_system_category"><?php _e( 'Enable SUMO Reward Points for Social Promotion' , SRP_LOCALE ) ; ?></label>
                    <select id="enable_social_reward_system_category" name="enable_social_reward_system_category" class="postform">
                        <option value="yes"><?php _e( 'Enable' , SRP_LOCALE ) ; ?></option>
                        <option value="no"><?php _e( 'Disable' , SRP_LOCALE ) ; ?></option>
                    </select>
                    <p>
                        <?php
                        _e( 'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
                                . 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ' , SRP_LOCALE ) ;
                        ?>
                    </p>
                </div>
                <!-- Social Rewards Field for Facebook in Category Start -->
                <div class="form-field">
                    <label for="social_facebook_enable_rs_rule"><?php _e( 'Facebook Like Reward Type' , SRP_LOCALE ) ; ?></label>
                    <select id="social_facebook_enable_rs_rule" name="social_facebook_enable_rs_rule" class="postform">
                        <option value="1"><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                        <option value="2"><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="social_facebook_rs_category_points"><?php _e( 'Facebook Like Reward Points' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="social_facebook_rs_category_points" id="social_facebook_rs_category_points" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <div class="form-field">
                    <label for="social_facebook_rs_category_percent"><?php _e( 'Facebook Like Reward Points in Percent %' ) ; ?></label>
                    <input type="text" name="social_facebook_rs_category_percent" id="social_facebook_rs_category_percent" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <!-- Social Rewards Field for Facebook in Category which is End -->

                <div class="form-field">
                    <label for="social_facebook_share_enable_rs_rule"><?php _e( 'Facebook Share Reward Type' , SRP_LOCALE ) ; ?></label>
                    <select id="social_facebook_share_enable_rs_rule" name="social_facebook_share_enable_rs_rule" class="postform">
                        <option value="1"><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                        <option value="2"><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="social_facebook_share_rs_category_points"><?php _e( 'Facebook Share Reward Points' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="social_facebook_share_rs_category_points" id="social_facebook_share_rs_category_points" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <div class="form-field">
                    <label for="social_facebook_share_rs_category_percent"><?php _e( 'Facebook Share Reward Points in Percent %' ) ; ?></label>
                    <input type="text" name="social_facebook_share_rs_category_percent" id="social_facebook_share_rs_category_percent" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <!-- Social Rewards Field for Twitter in Category Start -->
                <div class="form-field">
                    <label for="social_twitter_enable_rs_rule"><?php _e( 'Twitter Tweet Reward Type' , SRP_LOCALE ) ; ?></label>
                    <select id="social_twitter_enable_rs_rule" name="social_twitter_enable_rs_rule" class="postform">
                        <option value="1"><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                        <option value="2"><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="social_twitter_rs_category_points"><?php _e( 'Twitter Tweet Reward Points' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="social_twitter_rs_category_points" id="social_twitter_rs_category_points" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <div class="form-field">
                    <label for="social_twitter_rs_category_percent"><?php _e( 'Twitter Tweet Reward Percent %' ) ; ?></label>
                    <input type="text" name="social_twitter_rs_category_percent" id="social_twitter_rs_category_percent" value=""/>
                </div>
                <!-- Social Rewards Field for Twitter in Category which is End -->
                <div class="form-field">
                    <label for="social_twitter_follow_enable_rs_rule"><?php _e( 'Twitter Follow Reward Type' , SRP_LOCALE ) ; ?></label>
                    <select id="social_twitter_follow_enable_rs_rule" name="social_twitter_follow_enable_rs_rule" class="postform">
                        <option value="1"><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                        <option value="2"><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="social_twitter_follow_rs_category_points"><?php _e( 'Twitter Follow Reward Points' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="social_twitter_follow_rs_category_points" id="social_twitter_follow_rs_category_points" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <div class="form-field">
                    <label for="social_twitter_follow_rs_category_percent"><?php _e( 'Twitter Follow Reward Percent %' ) ; ?></label>
                    <input type="text" name="social_twitter_follow_rs_category_percent" id="social_twitter_follow_rs_category_percent" value=""/>
                </div>

                <!-- Social Rewards Field for Google in Category Start -->
                <div class="form-field">
                    <label for="social_google_enable_rs_rule"><?php _e( 'Google+1 Reward Type' , SRP_LOCALE ) ; ?></label>
                    <select id="social_google_enable_rs_rule" name="social_google_enable_rs_rule" class="postform">
                        <option value="1"><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                        <option value="2"><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="social_google_rs_category_points"><?php _e( 'Google+1 Reward Points' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="social_google_rs_category_points" id="social_google_rs_category_points" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <div class="form-field">
                    <label for="social_google_rs_category_percent"><?php _e( 'Google+1 Reward Percent %' ) ; ?></label>
                    <input type="text" name="social_google_rs_category_percent" id="social_google_rs_category_percent" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <!-- Social Rewards Field for Google in Category which is End -->
                <!-- Social Rewards Field for VK in Category Start -->
                <div class="form-field">
                    <label for="social_vk_enable_rs_rule"><?php _e( 'VK.com Like Reward Type' , SRP_LOCALE ) ; ?></label>
                    <select id="social_vk_enable_rs_rule" name="social_vk_enable_rs_rule" class="postform">
                        <option value="1"><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                        <option value="2"><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="social_vk_rs_category_points"><?php _e( 'VK.com Like Reward Points' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="social_vk_rs_category_points" id="social_vk_rs_category_points" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <div class="form-field">
                    <label for="social_vk_rs_category_percent"><?php _e( 'VK.com Like Reward Percent %' ) ; ?></label>
                    <input type="text" name="social_vk_rs_category_percent" id="social_vk_rs_category_percent" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <!-- Social Rewards Field for VK in Category which is End -->
                <!-- Social Rewards Field for Instagram in Category which is Start -->
                <div class="form-field">
                    <label for="social_instagram_enable_rs_rule"><?php _e( 'Instagram Reward Type' , SRP_LOCALE ) ; ?></label>
                    <select id="social_instagram_enable_rs_rule" name="social_instagram_enable_rs_rule" class="postform">
                        <option value="1"><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                        <option value="2"><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="social_instagram_rs_category_points"><?php _e( 'Instagram Reward Points' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="social_instagram_rs_category_points" id="social_instagram_rs_category_points" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <div class="form-field">
                    <label for="social_instagram_rs_category_percent"><?php _e( 'Instagram Reward Percent %' ) ; ?></label>
                    <input type="text" name="social_instagram_rs_category_percent" id="social_instagram_rs_category_percent" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <!-- Social Rewards Field for Instagram in Category which is End -->
                <!-- Social Rewards Field for OK.ru in Category which is Start -->
                <div class="form-field">
                    <label for="social_ok_follow_enable_rs_rule"><?php _e( 'OK.ru Share Reward Type' , SRP_LOCALE ) ; ?></label>
                    <select id="social_ok_follow_enable_rs_rule" name="social_ok_follow_enable_rs_rule" class="postform">
                        <option value="1"><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                        <option value="2"><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="social_ok_follow_rs_category_points"><?php _e( 'OK.ru Share Reward Points' , SRP_LOCALE ) ; ?></label>
                    <input type="text" name="social_ok_follow_rs_category_points" id="social_ok_follow_rs_category_points" value=""/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                        ?></p>
                </div>
                <div class="form-field">
                    <label for="social_ok_follow_rs_category_percent"><?php _e( 'OK.ru Share Reward Percent %' ) ; ?></label>
                    <input type="text" name="social_ok_follow_rs_category_percent" id="social_ok_follow_rs_category_percent" value=""/>
                </div>
                <!-- Social Rewards Field for OK.ru in Category which is End -->
                <?php
            }
        }

        public static function rs_edit_admin_settings_for_category_page( $term , $taxonomy ) {
            $enablesocialvalue   = srp_term_meta( $term->term_id , 'enable_social_reward_system_category' ) ;
            $enablevalueforpoint = srp_term_meta( $term->term_id , 'enable_point_price_category' ) ;
            $pointprice          = srp_term_meta( $term->term_id , 'rs_category_points_price' ) ;
            $pointpricetype      = srp_term_meta( $term->term_id , 'point_price_category_type' ) ;
            $enable_pricing_type = srp_term_meta( $term->term_id , 'pricing_category_types' ) ;

            $enablevalue           = srp_term_meta( $term->term_id , 'enable_reward_system_category' ) ;
            $display_type          = srp_term_meta( $term->term_id , 'enable_rs_rule' ) ;
            $rewardpoints          = srp_term_meta( $term->term_id , 'rs_category_points' ) ;
            $rewardpercent         = srp_term_meta( $term->term_id , 'rs_category_percent' ) ;
            $enablereferralvalue   = srp_term_meta( $term->term_id , 'enable_referral_reward_system_category' ) ;
            $referralrewardpoints  = srp_term_meta( $term->term_id , 'referral_rs_category_points' ) ;
            $referralrewardpercent = srp_term_meta( $term->term_id , 'referral_rs_category_percent' ) ;
            $referralrewardrule    = srp_term_meta( $term->term_id , 'referral_enable_rs_rule' ) ;

            $referralrewardpoints_get_refer  = srp_term_meta( $term->term_id , 'referral_rs_category_points_get_refered' ) ;
            $referralrewardpercent_get_refer = srp_term_meta( $term->term_id , 'referral_rs_category_percent_get_refer' ) ;
            $referral_enable_rs_rule_refer   = srp_term_meta( $term->term_id , 'referral_enable_rs_rule_refer' ) ;

            $socialfacebooktype    = srp_term_meta( $term->term_id , 'social_facebook_enable_rs_rule' ) ;
            $socialfacebookpoints  = srp_term_meta( $term->term_id , 'social_facebook_rs_category_points' ) ;
            $socialfacebookpercent = srp_term_meta( $term->term_id , 'social_facebook_rs_category_percent' ) ;

            $socialfacebooktype_share    = srp_term_meta( $term->term_id , 'social_facebook_share_enable_rs_rule' ) ;
            $socialfacebookpoints_share  = srp_term_meta( $term->term_id , 'social_facebook_share_rs_category_points' ) ;
            $socialfacebookpercent_share = srp_term_meta( $term->term_id , 'social_facebook_share_rs_category_percent' ) ;

            $socialtwittertype           = srp_term_meta( $term->term_id , 'social_twitter_enable_rs_rule' ) ;
            $socialtwitterpoints         = srp_term_meta( $term->term_id , 'social_twitter_rs_category_points' ) ;
            $socialtwitterpercent        = srp_term_meta( $term->term_id , 'social_twitter_rs_category_percent' ) ;
            $socialtwittertype_follow    = srp_term_meta( $term->term_id , 'social_twitter_follow_enable_rs_rule' ) ;
            $socialtwitterpoints_follow  = srp_term_meta( $term->term_id , 'social_twitter_follow_rs_category_points' ) ;
            $socialtwitterpercent_follow = srp_term_meta( $term->term_id , 'social_twitter_follow_rs_category_percent' ) ;

            $socialoktype_follow    = srp_term_meta( $term->term_id , 'social_ok_follow_enable_rs_rule' ) ;
            $socialokpoints_follow  = srp_term_meta( $term->term_id , 'social_ok_follow_rs_category_points' ) ;
            $socialokpercent_follow = srp_term_meta( $term->term_id , 'social_ok_follow_rs_category_percent' ) ;

            $socialgoogletype    = srp_term_meta( $term->term_id , 'social_google_enable_rs_rule' ) ;
            $socialgooglepoints  = srp_term_meta( $term->term_id , 'social_google_rs_category_points' ) ;
            $socialgooglepercent = srp_term_meta( $term->term_id , 'social_google_rs_category_percent' ) ;

            $socialvktype    = srp_term_meta( $term->term_id , 'social_vk_enable_rs_rule' ) ;
            $socialvkpoints  = srp_term_meta( $term->term_id , 'social_vk_rs_category_points' ) ;
            $socialvkpercent = srp_term_meta( $term->term_id , 'social_vk_rs_category_percent' ) ;

            $socialinstagramtype    = srp_term_meta( $term->term_id , 'social_instagram_enable_rs_rule' ) ;
            $socialinstagrampoints  = srp_term_meta( $term->term_id , 'social_instagram_rs_category_points' ) ;
            $socialinstagrampercent = srp_term_meta( $term->term_id , 'social_instagram_rs_category_percent' ) ;
            if ( get_option( 'rs_point_price_activated' ) == 'yes' && get_option( 'rs_enable_product_category_level_for_points_price' ) == 'yes' ) {
                ?>
                <tr class="form-field">
                    <th scope="row" valign="top"><label> <?php _e( 'Enable Point Pricing' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="enable_point_price_category" name="enable_point_price_category" class="postform">
                            <option value="yes"<?php selected( 'yes' , $enablevalueforpoint ) ; ?>><?php _e( 'Enable' , SRP_LOCALE ) ; ?></option>
                            <option value="no"<?php selected( 'no' , $enablevalueforpoint ) ; ?>><?php _e( 'Disable' , SRP_LOCALE ) ; ?> </option>
                        </select>
                        <p><?php
                            _e( 'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
                                    . 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ' , SRP_LOCALE ) ;
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label> <?php _e( 'Pricing Display Type' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <div class="form-field">
                            <select id = "pricing_category_types" name="pricing_category_types" class="postform">
                                <option value = "1"<?php selected( '1' , $enable_pricing_type ) ; ?>><?php _e( 'Currency and Point Price' , SRP_LOCALE ) ; ?></option>
                                <option value = "2"<?php selected( '2' , $enable_pricing_type ) ; ?>><?php _e( 'Only Point Price' , SRP_LOCALE ) ; ?></option>
                            </select>
                            <p><?php
                                _e( 'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
                                        . 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ' , SRP_LOCALE ) ;
                                ?></p>
                        </div>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Point Price Type' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="point_price_category_type" name="point_price_category_type" class="postform">
                            <option value="1" <?php selected( '1' , $pointpricetype ) ; ?>><?php _e( 'By Fixed' , SRP_LOCALE ) ; ?></option>
                            <option value="2" <?php selected( '2' , $pointpricetype ) ; ?>><?php _e( 'Based on conversion' , SRP_LOCALE ) ; ?></option>
                        </select>
                        <p class="description"><?php
                            _e( 'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
                                    . 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ' , SRP_LOCALE ) ;
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><?php _e( 'By Fixed Point' , SRP_LOCALE ) ; ?></label></th>
                <td>
                    <input type="text" name="rs_category_points_price" id="rs_category_points_price" value="<?php echo $pointprice ; ?>"/>
                    <p><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                </td>
                </tr>
                <?php
            }
            if ( get_option( 'rs_product_purchase_activated' ) === 'yes' && get_option( 'rs_enable_product_category_level_for_product_purchase' ) == 'yes' ) {
                ?>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Enable SUMO Reward Points for Product Purchase' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="enable_reward_system_category" name="enable_reward_system_category" class="postform">
                            <option value="yes" <?php selected( 'yes' , $enablevalue ) ; ?>><?php _e( 'Enable' , SRP_LOCALE ) ; ?></option>
                            <option value="no" <?php selected( 'no' , $enablevalue ) ; ?>><?php _e( 'Disable' , SRP_LOCALE ) ; ?></option>
                        </select>
                        <p class="description"><?php
                            _e( 'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
                                    . 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ' , SRP_LOCALE ) ;
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Reward Type' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="enable_rs_rule" name="enable_rs_rule" class="postform">
                            <option value="1" <?php selected( '1' , $display_type ) ; ?>><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                            <option value="2" <?php selected( '2' , $display_type ) ; ?>><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Reward Points' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="rs_category_points" id="rs_category_points" value="<?php echo $rewardpoints ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Reward Percent' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="rs_category_percent" id="rs_category_percent" value="<?php echo $rewardpercent ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <?php
            }
            if ( get_option( 'rs_referral_activated' ) == 'yes' && get_option( 'rs_enable_product_category_level_for_referral_product_purchase' ) == 'yes' ) {
                ?>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Enable Referral Reward Points for Product Purchase' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="enable_referral_reward_system_category" name="enable_referral_reward_system_category" class="postform">
                            <option value="yes" <?php selected( 'yes' , $enablereferralvalue ) ; ?>><?php _e( 'Enable' , SRP_LOCALE ) ; ?></option>
                            <option value="no" <?php selected( 'no' , $enablereferralvalue ) ; ?>><?php _e( 'Disable' , SRP_LOCALE ) ; ?></option>
                        </select>
                        <p class="description"><?php
                            _e( 'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
                                    . 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ' , SRP_LOCALE ) ;
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Referral Reward Type' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="enable_rs_rule" name="referral_enable_rs_rule" class="postform">
                            <option value="1" <?php selected( '1' , $referralrewardrule ) ; ?>><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                            <option value="2" <?php selected( '2' , $referralrewardrule ) ; ?>><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Referral Reward Points' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="referral_rs_category_points" id="referral_rs_category_points" value="<?php echo $referralrewardpoints ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Referral Reward Percent' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="referral_rs_category_percent" id="referral_rs_category_percent" value="<?php echo $referralrewardpercent ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( ' Reward Type for Getting Referred' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="referral_enable_rs_rule_refer" name="referral_enable_rs_rule_refer" class="postform">
                            <option value="1" <?php selected( '1' , $referral_enable_rs_rule_refer ) ; ?>><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                            <option value="2" <?php selected( '2' , $referral_enable_rs_rule_refer ) ; ?>><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( ' Reward Points for Getting Referred' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="referral_rs_category_points_get_refered" id="referral_rs_category_points_get_refered" value="<?php echo $referralrewardpoints_get_refer ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>     
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( ' Reward Points In  Percent % for Getting Referred' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="referral_rs_category_percent_get_refer" id="referral_rs_category_percent_get_refer" value="<?php echo $referralrewardpercent_get_refer ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
            <?php } if ( get_option( 'rs_social_reward_activated' ) == 'yes' && get_option( 'rs_enable_product_category_level_for_social_reward' ) == 'yes' ) { ?>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Enable SUMO Reward Points for Social Promotion' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="enable_social_reward_system_category" name="enable_social_reward_system_category" class="postform">
                            <option value="yes" <?php selected( 'yes' , $enablesocialvalue ) ; ?>><?php _e( 'Enable' , SRP_LOCALE ) ; ?></option>
                            <option value="no" <?php selected( 'no' , $enablesocialvalue ) ; ?>><?php _e( 'Disable' , SRP_LOCALE ) ; ?></option>
                        </select>
                        <p class="description"><?php
                            _e( 'Category Settings will be considered when Product Settings is Enabled and Values are Empty. '
                                    . 'Priority Order is Product Settings, Category Settings and Global Settings in the Same Order. ' , SRP_LOCALE ) ;
                            ?></p>
                    </td>
                </tr>
                <!-- Below Field is for Facebook Social Rewards in Category Level Start-->
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Reward Type for Facebook' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="social_facebook_enable_rs_rule" name="social_facebook_enable_rs_rule" class="postform">
                            <option value="1" <?php selected( '1' , $socialfacebooktype ) ; ?>><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                            <option value="2" <?php selected( '2' , $socialfacebooktype ) ; ?>><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Facebook Reward Points' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_facebook_rs_category_points" id="social_facebook_rs_category_points" value="<?php echo $socialfacebookpoints ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Facebook Reward in Percent %' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_facebook_rs_category_percent" id="social_facebook_rs_category_percent" value="<?php echo $socialfacebookpercent ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <!-- Below Field is for Facebook Social Rewards in Category Level Ends -->
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Reward Type for Facebook Share' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="social_facebook_share_enable_rs_rule" name="social_facebook_share_enable_rs_rule" class="postform">
                            <option value="1" <?php selected( '1' , $socialfacebooktype_share ) ; ?>><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                            <option value="2" <?php selected( '2' , $socialfacebooktype_share ) ; ?>><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Facebook  Share Reward Points' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_facebook_share_rs_category_points" id="social_facebook_share_rs_category_points" value="<?php echo $socialfacebookpoints_share ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Facebook Share Reward in Percent %' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_facebook_share_rs_category_percent" id="social_facebook_share_rs_category_percent" value="<?php echo $socialfacebookpercent_share ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <!-- Below Field is for Twitter Social Rewards in Category Level Start-->
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Reward Type for Twitter' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="social_twitter_enable_rs_rule" name="social_twitter_enable_rs_rule" class="postform">
                            <option value="1" <?php selected( '1' , $socialtwittertype ) ; ?>><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                            <option value="2" <?php selected( '2' , $socialtwittertype ) ; ?>><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Twitter Reward Points' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_twitter_rs_category_points" id="social_twitter_rs_category_points" value="<?php echo $socialtwitterpoints ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Twitter Reward in Percent %' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_twitter_rs_category_percent" id="social_twitter_rs_category_percent" value="<?php echo $socialtwitterpercent ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <!-- Below Field is for Twitter Social Rewards in Category Level Ends -->
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Reward Type for Twitter Follow' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="social_twitter_follow_enable_rs_rule" name="social_twitter_follow_enable_rs_rule" class="postform">
                            <option value="1" <?php selected( '1' , $socialtwittertype_follow ) ; ?>><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                            <option value="2" <?php selected( '2' , $socialtwittertype_follow ) ; ?>><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Twitter Follow Reward Points' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_twitter_follow_rs_category_points" id="social_twitter_follow_rs_category_points" value="<?php echo $socialtwitterpoints_follow ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Twitter Follow Reward in Percent %' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_twitter_follow_rs_category_percent" id="social_twitter_follow_rs_category_percent" value="<?php echo $socialtwitterpercent_follow ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <!-- Below Field is for Google Social Rewards in Category Level Start-->
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Reward Type for Google' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="social_google_enable_rs_rule" name="social_google_enable_rs_rule" class="postform">
                            <option value="1" <?php selected( '1' , $socialgoogletype ) ; ?>><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                            <option value="2" <?php selected( '2' , $socialgoogletype ) ; ?>><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Google Reward Points' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_google_rs_category_points" id="social_twitter_rs_category_points" value="<?php echo $socialgooglepoints ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Google Reward in Percent %' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_google_rs_category_percent" id="social_google_rs_category_percent" value="<?php echo $socialgooglepercent ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <!-- Below Field is for Google Social Rewards in Category Level Ends -->

                <!-- Below Field is for VK Social Rewards in Category Level Start-->
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Reward Type for VK' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="social_vk_enable_rs_rule" name="social_vk_enable_rs_rule" class="postform">
                            <option value="1" <?php selected( '1' , $socialvktype ) ; ?>><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                            <option value="2" <?php selected( '2' , $socialvktype ) ; ?>><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social VK Reward Points' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_vk_rs_category_points" id="social_vk_rs_category_points" value="<?php echo $socialvkpoints ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social VK Reward in Percent %' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_vk_rs_category_percent" id="social_vk_rs_category_percent" value="<?php echo $socialvkpercent ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <!-- Below Field is for VK Social Rewards in Category Level Ends -->

                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Reward Type for Instagram' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="social_instagram_enable_rs_rule" name="social_instagram_enable_rs_rule" class="postform">
                            <option value="1" <?php selected( '1' , $socialinstagramtype ) ; ?>><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                            <option value="2" <?php selected( '2' , $socialinstagramtype ) ; ?>><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Instagram Reward Points' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_instagram_rs_category_points" id="social_instagram_rs_category_points" value="<?php echo $socialinstagrampoints ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Instagram Reward in Percent %' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_instagram_rs_category_percent" id="social_instagram_rs_category_percent" value="<?php echo $socialinstagrampercent ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <!-- Below Field is for Twitter Social Rewards in Category Level Ends -->
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social Reward Type for OK.ru Share' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <select id="social_ok_follow_enable_rs_rule" name="social_ok_follow_enable_rs_rule" class="postform">
                            <option value="1" <?php selected( '1' , $socialoktype_follow ) ; ?>><?php _e( 'By Fixed Reward Points' , SRP_LOCALE ) ; ?></option>
                            <option value="2" <?php selected( '2' , $socialoktype_follow ) ; ?>><?php _e( 'By Percentage of Product Price' , SRP_LOCALE ) ; ?></option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social OK.ru Share Reward Points' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_ok_follow_rs_category_points" id="social_ok_follow_rs_category_points" value="<?php echo $socialokpoints_follow ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row" valign="top"><label><?php _e( 'Social OK.ru Share Reward in Percent %' , SRP_LOCALE ) ; ?></label></th>
                    <td>
                        <input type="text" name="social_ok_follow_rs_category_percent" id="social_ok_follow_rs_category_percent" value="<?php echo $socialokpercent_follow ; ?>"/>
                        <p class="description"><?php _e( 'When left empty, Product and Global Settings will be considered in the same order and Current Settings (Category Settings) will be ignored. When value greater than or equal to 0 is entered then Current Settings (Category Settings) will be considered and Product/Global Settings will be ignored' , SRP_LOCALE )
                            ?></p>
                    </td>
                </tr>
                <?php
            }
        }

        public static function rs_save_admin_settings_for_category_page( $term_id , $tt_id , $taxonomy ) {
            if ( isset( $_POST[ 'enable_rs_rule' ] ) )
                srp_update_term_meta( $term_id , 'enable_rs_rule' , $_POST[ 'enable_rs_rule' ] ) ;

            if ( isset( $_POST[ 'enable_point_price_category' ] ) )
                srp_update_term_meta( $term_id , 'enable_point_price_category' , $_POST[ 'enable_point_price_category' ] ) ;

            if ( isset( $_POST[ 'rs_category_points_price' ] ) )
                srp_update_term_meta( $term_id , 'rs_category_points_price' , $_POST[ 'rs_category_points_price' ] ) ;

            if ( isset( $_POST[ 'point_price_category_type' ] ) )
                srp_update_term_meta( $term_id , 'point_price_category_type' , $_POST[ 'point_price_category_type' ] ) ;

            if ( isset( $_POST[ 'pricing_category_types' ] ) )
                srp_update_term_meta( $term_id , 'pricing_category_types' , $_POST[ 'pricing_category_types' ] ) ;

            if ( isset( $_POST[ 'referral_enable_rs_rule' ] ) )
                srp_update_term_meta( $term_id , 'referral_enable_rs_rule' , $_POST[ 'referral_enable_rs_rule' ] ) ;

            if ( isset( $_POST[ 'enable_reward_system_category' ] ) )
                srp_update_term_meta( $term_id , 'enable_reward_system_category' , $_POST[ 'enable_reward_system_category' ] ) ;

            if ( isset( $_POST[ 'enable_referral_reward_system_category' ] ) )
                srp_update_term_meta( $term_id , 'enable_referral_reward_system_category' , $_POST[ 'enable_referral_reward_system_category' ] ) ;

            if ( isset( $_POST[ 'rs_category_points' ] ) )
                srp_update_term_meta( $term_id , 'rs_category_points' , $_POST[ 'rs_category_points' ] ) ;
            if ( isset( $_POST[ 'rs_category_percent' ] ) )
                srp_update_term_meta( $term_id , 'rs_category_percent' , $_POST[ 'rs_category_percent' ] ) ;

            if ( isset( $_POST[ 'referral_rs_category_points' ] ) )
                srp_update_term_meta( $term_id , 'referral_rs_category_points' , $_POST[ 'referral_rs_category_points' ] ) ;
            if ( isset( $_POST[ 'referral_rs_category_percent' ] ) )
                srp_update_term_meta( $term_id , 'referral_rs_category_percent' , $_POST[ 'referral_rs_category_percent' ] ) ;
            if ( isset( $_POST[ 'referral_enable_rs_rule_refer' ] ) )
                srp_update_term_meta( $term_id , 'referral_enable_rs_rule_refer' , $_POST[ 'referral_enable_rs_rule_refer' ] ) ;
            if ( isset( $_POST[ 'referral_rs_category_points_get_refered' ] ) )
                srp_update_term_meta( $term_id , 'referral_rs_category_points_get_refered' , $_POST[ 'referral_rs_category_points_get_refered' ] ) ;
            if ( isset( $_POST[ 'referral_rs_category_percent_get_refer' ] ) )
                srp_update_term_meta( $term_id , 'referral_rs_category_percent_get_refer' , $_POST[ 'referral_rs_category_percent_get_refer' ] ) ;

//social updation for facebook,twitter,google
            if ( isset( $_POST[ 'enable_social_reward_system_category' ] ) )
                srp_update_term_meta( $term_id , 'enable_social_reward_system_category' , $_POST[ 'enable_social_reward_system_category' ] ) ;

            /* Facebook Rule and its Points Start */
            if ( isset( $_POST[ 'social_facebook_enable_rs_rule' ] ) )
                srp_update_term_meta( $term_id , 'social_facebook_enable_rs_rule' , $_POST[ 'social_facebook_enable_rs_rule' ] ) ;
            if ( isset( $_POST[ 'social_facebook_rs_category_points' ] ) )
                srp_update_term_meta( $term_id , 'social_facebook_rs_category_points' , $_POST[ 'social_facebook_rs_category_points' ] ) ;
            if ( isset( $_POST[ 'social_facebook_rs_category_percent' ] ) )
                srp_update_term_meta( $term_id , 'social_facebook_rs_category_percent' , $_POST[ 'social_facebook_rs_category_percent' ] ) ;

            /* Facebook Rule and its Points End */
            if ( isset( $_POST[ 'social_facebook_share_enable_rs_rule' ] ) )
                srp_update_term_meta( $term_id , 'social_facebook_share_enable_rs_rule' , $_POST[ 'social_facebook_share_enable_rs_rule' ] ) ;
            if ( isset( $_POST[ 'social_facebook_share_rs_category_points' ] ) )
                srp_update_term_meta( $term_id , 'social_facebook_share_rs_category_points' , $_POST[ 'social_facebook_share_rs_category_points' ] ) ;
            if ( isset( $_POST[ 'social_facebook_share_rs_category_percent' ] ) )
                srp_update_term_meta( $term_id , 'social_facebook_share_rs_category_percent' , $_POST[ 'social_facebook_share_rs_category_percent' ] ) ;

            /* Twitter Rule and Its Points updation Start */
            if ( isset( $_POST[ 'social_twitter_enable_rs_rule' ] ) )
                srp_update_term_meta( $term_id , 'social_twitter_enable_rs_rule' , $_POST[ 'social_twitter_enable_rs_rule' ] ) ;
            if ( isset( $_POST[ 'social_twitter_rs_category_points' ] ) )
                srp_update_term_meta( $term_id , 'social_twitter_rs_category_points' , $_POST[ 'social_twitter_rs_category_points' ] ) ;
            if ( isset( $_POST[ 'social_twitter_rs_category_percent' ] ) )
                srp_update_term_meta( $term_id , 'social_twitter_rs_category_percent' , $_POST[ 'social_twitter_rs_category_percent' ] ) ;
            /* Twitter Rule and Its Points Updation End */
            if ( isset( $_POST[ 'social_twitter_follow_enable_rs_rule' ] ) )
                srp_update_term_meta( $term_id , 'social_twitter_follow_enable_rs_rule' , $_POST[ 'social_twitter_follow_enable_rs_rule' ] ) ;
            if ( isset( $_POST[ 'social_twitter_follow_rs_category_points' ] ) )
                srp_update_term_meta( $term_id , 'social_twitter_follow_rs_category_points' , $_POST[ 'social_twitter_follow_rs_category_points' ] ) ;
            if ( isset( $_POST[ 'social_twitter_follow_rs_category_percent' ] ) )
                srp_update_term_meta( $term_id , 'social_twitter_follow_rs_category_percent' , $_POST[ 'social_twitter_follow_rs_category_percent' ] ) ;


            /* Google Rule and Its Points updation Start */
            if ( isset( $_POST[ 'social_google_enable_rs_rule' ] ) )
                srp_update_term_meta( $term_id , 'social_google_enable_rs_rule' , $_POST[ 'social_google_enable_rs_rule' ] ) ;
            if ( isset( $_POST[ 'social_google_rs_category_points' ] ) )
                srp_update_term_meta( $term_id , 'social_google_rs_category_points' , $_POST[ 'social_google_rs_category_points' ] ) ;
            if ( isset( $_POST[ 'social_google_rs_category_percent' ] ) )
                srp_update_term_meta( $term_id , 'social_google_rs_category_percent' , $_POST[ 'social_google_rs_category_percent' ] ) ;
            /* Google Rule and Its Points Updation End */

            /* VK Rule and Its Points updation Start */
            if ( isset( $_POST[ 'social_vk_enable_rs_rule' ] ) )
                srp_update_term_meta( $term_id , 'social_vk_enable_rs_rule' , $_POST[ 'social_vk_enable_rs_rule' ] ) ;
            if ( isset( $_POST[ 'social_vk_rs_category_points' ] ) )
                srp_update_term_meta( $term_id , 'social_vk_rs_category_points' , $_POST[ 'social_vk_rs_category_points' ] ) ;
            if ( isset( $_POST[ 'social_vk_rs_category_percent' ] ) )
                srp_update_term_meta( $term_id , 'social_vk_rs_category_percent' , $_POST[ 'social_vk_rs_category_percent' ] ) ;
            /* VK Rule and Its Points Updation End */

            if ( isset( $_POST[ 'social_instagram_enable_rs_rule' ] ) )
                srp_update_term_meta( $term_id , 'social_instagram_enable_rs_rule' , $_POST[ 'social_instagram_enable_rs_rule' ] ) ;
            if ( isset( $_POST[ 'social_instagram_rs_category_points' ] ) )
                srp_update_term_meta( $term_id , 'social_instagram_rs_category_points' , $_POST[ 'social_instagram_rs_category_points' ] ) ;
            if ( isset( $_POST[ 'social_instagram_rs_category_percent' ] ) )
                srp_update_term_meta( $term_id , 'social_instagram_rs_category_percent' , $_POST[ 'social_instagram_rs_category_percent' ] ) ;

            if ( isset( $_POST[ 'social_ok_follow_enable_rs_rule' ] ) )
                srp_update_term_meta( $term_id , 'social_ok_follow_enable_rs_rule' , $_POST[ 'social_ok_follow_enable_rs_rule' ] ) ;
            if ( isset( $_POST[ 'social_ok_follow_rs_category_points' ] ) )
                srp_update_term_meta( $term_id , 'social_ok_follow_rs_category_points' , $_POST[ 'social_ok_follow_rs_category_points' ] ) ;
            if ( isset( $_POST[ 'social_ok_follow_rs_category_percent' ] ) )
                srp_update_term_meta( $term_id , 'social_ok_follow_rs_category_percent' , $_POST[ 'social_ok_follow_rs_category_percent' ] ) ;

            delete_transient( 'wc_term_counts' ) ;
        }

    }

    RSCategoryField::init() ;
}