<?php
/**
 * Exit if accessed directly
 */
/*  Popup Style */
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
$user_ID = get_current_user_ID();
$public_obj = new Coupon_Referral_Program_Public('Coupon Referral Program', '1.0.0'); 
?>
    <style type="text/css">
      <?php echo Coupon_Referral_Program_Public::get_custom_style_popup_btn();?>
      span.modal__close:after {
        background-image: url('<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL.'public/images/cancel.png'; ?>');
      }
    </style>
    <div id="mwb_modal" class="mwb_modal mwb_modal__bg">
      <div class="mwb_modal__dialog mwb-pr-dialog">
        <div class="mwb_modal__content">
          <div class="mwb_modal__header">
            <div class="mwb_modal__title">
              <h2 class="mwb_modal__title-text"><?php _e('Invite & Earn','coupon-referral-program');?></h2>
            </div>
            <span class="mwb_modal__close">
              <!-- <img src="<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL.'public/images/cancel.png'; ?>" alt="">   -->
              X
            </span>
          </div>
          <div class="mwb_modal__text mwb-pr-popup-text">
           
              <?php  if(is_user_logged_in()){ 
                $mwb_crp_img = COUPON_REFERRAL_PROGRAM_DIR_URL.'public/images/background.jpg';?>
               <div class="mwb-pr-popup-body mwb_cpr_logged_wrapper">
                <div class="mwb-popup-points-label" style="background-image: url('<?php echo $mwb_crp_img;?>')">
                  <h6><?php _e('Referral Link: ','coupon-referral-program');?></h6>
                  <div class="mwb_cpr_refrral_code_copy">
                    <p id="mwb_cpr_copy">
                        <code id="mwb_cpr_copyy" style="background-color: <?php echo Coupon_Referral_Program_Admin::get_selected_color();?>">
                          <?php echo $public_obj->get_referral_link($user_ID); ?>
                        </code>
                        <span class="mwb_cpr_copy_btn_wrap">
                          <button class="mwb_cpr_btn_copy mwb_tooltip" data-clipboard-target="#mwb_cpr_copyy" aria-label="copied">
                            <span class="mwb_tooltiptext"><?php _e('Copy','coupon-referral-program') ;?></span>
                            <span class="mwb_tooltiptext_copied mwb_tooltiptext"><?php _e('Copied','coupon-referral-program') ;?></span>
                            <img src="<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL.'admin/images/copy.png';?>" alt="">
                          </button>
                        </span>
                    </p>
                  </div>
                </div>
              <div class="mwb-pr-popup-tab-wrapper ">
                
                <!-- <a class="tab-link active" id ="notify_user_gain_tab" href="javascript:;"></a> -->
                <span id="notify_user_gain_tab" class="tab-link active" style="background-color: <?php echo Coupon_Referral_Program_Admin::get_selected_color();?>"><?php _e('Steps to Earn More','coupon-referral-program');?></span>
              </div>
              <!--mwb-pr-popup- rewards tab-container-->
              <div class="mwb-pr-popup-tab-container active" id="notify_user_earn_more_section">
                <ul class="mwb-pr-popup-tab-listing mwb-pr-popup-rewards-tab-listing">
                  <?php /*if(is_signup_enabled() && !is_user_logged_in()) {*/  ?>
                  <li>
                    <div class="mwb-pr-popup-left-rewards">                      
                        <div class="mwb-pr-popup-rewards-left-content">
                          <p><?php _e('1. Copy & Share Your Link','coupon-referral-program');?></p>
                          <p><?php _e('2. Your friends can register via your link','coupon-referral-program');?></p>
                           <?php if (!self::check_is_points_rewards_enable()) {?>
                          <!-- Fixed Fixed -->
                          <?php if($public_obj->get_referral_coupon_amount_type() == 'mwb_cpr_referral_fixed' && $public_obj->mwb_get_discount_type() == 'mwb_cpr_fixed') :?>
                          <p><?php _e('3. You will get the fixed discount coupon of  ','coupon-referral-program'); echo '<span class="mwb_cpr_highlight" style="color:'.Coupon_Referral_Program_Admin::get_selected_color().'">'.wc_price($public_obj->get_referral_discount_order()).'</span>';_e(' on the referral purchase.','coupon-referral-program');?></p>
                          <?php endif;?>
                          <!-- Percentage Fixed -->
                          <?php if( $public_obj->get_referral_coupon_amount_type() == 'mwb_cpr_referral_percent' && $public_obj->mwb_get_discount_type() == 'mwb_cpr_fixed') :?>
                           <p><?php _e('3. You will get the fixed discount coupon, ','coupon-referral-program'); echo '<span class="mwb_cpr_highlight" style="color:'.Coupon_Referral_Program_Admin::get_selected_color().'">'.$public_obj->get_referral_discount_order().'%</span>';_e(' of the referral order','coupon-referral-program').$public_obj->get_referral_coupon_amount_limit_html();?>
                           </p>
                          <?php endif;?>
                          <!-- Fixed Percentage-->
                          <?php if( $public_obj->get_referral_coupon_amount_type() == 'mwb_cpr_referral_fixed' && $public_obj->mwb_get_discount_type() == 'mwb_cpr_percent') :?>
                           <p><?php _e('3. You will get ','coupon-referral-program'); echo '<span class="mwb_cpr_highlight" style="color:'.Coupon_Referral_Program_Admin::get_selected_color().'">'.$public_obj->get_referral_discount_order().'%</span>';_e(' coupon on the referral order purchase.','coupon-referral-program');?>
                           </p>
                          <?php endif;?>
                          <!-- Percentage Percentage-->
                          <?php if( $public_obj->get_referral_coupon_amount_type() == 'mwb_cpr_referral_percent' && $public_obj->mwb_get_discount_type() == 'mwb_cpr_percent') :?>
                           <p><?php _e('3. You will get percentage coupon, ','coupon-referral-program'); echo '<span class="mwb_cpr_highlight" style="color:'.Coupon_Referral_Program_Admin::get_selected_color().'">'.$public_obj->get_referral_discount_order().'%</span>';_e(' of the referral order','coupon-referral-program').$public_obj->get_referral_coupon_amount_limit_html();?>
                           </p>
                          <?php endif;?>
                          <?php if($public_obj->check_signup_is_enable()){?>
                          <p><?php _e('4. Your friend will also get a ','coupon-referral-program'); echo '<span class="mwb_cpr_highlight" style="color:'.Coupon_Referral_Program_Admin::get_selected_color().'">'.$public_obj->get_signup_discount_html().'</span>'; _e(' discount coupon in return on Signup','coupon-referral-program');?></p>
                          <?php } ?>
                          <?php if($public_obj->check_signup_is_enable()){?>
                                  <?php if($public_obj->check_reffre_signup_is_enable()){?>
                              <p><?php _e('5. You will also get a ','coupon-referral-program'); echo '<span class="mwb_cpr_highlight" style="color:'.Coupon_Referral_Program_Admin::get_selected_color().'">'.$public_obj->get_refree_signup_discount_html().'</span>'; _e(' discount coupon on referral signup','coupon-referral-program');?></p>
                          <?php } ?>
                          <?php }?>
                          <?php if(!$public_obj->check_signup_is_enable()){?>
                          <?php if($public_obj->check_reffre_signup_is_enable()){?>
                          <p><?php _e('4. You will also get a ','coupon-referral-program'); echo '<span class="mwb_cpr_highlight" style="color:'.Coupon_Referral_Program_Admin::get_selected_color().'">'.$public_obj->get_refree_signup_discount_html().'</span>'; _e(' discount coupon on referral signup','coupon-referral-program');?></p>
                          <?php }?>
                          <?php }?>
                          <?php }?>
                          <?php if (self::check_is_points_rewards_enable()) {?>
                             <p><?php _e('3. You will get the ','coupon-referral-program'); echo '<span class="mwb_cpr_highlight" style="color:'.Coupon_Referral_Program_Admin::get_selected_color().'">'.$public_obj->get_points_for_reffral_purchase().__('points','coupon-referral-program').'</span>';_e(' on the referral order.','coupon-referral-program');?>
                             </p>
                             <p><?php _e('4. Your friend will also get a ','coupon-referral-program'); echo '<span class="mwb_cpr_highlight" style="color:'.Coupon_Referral_Program_Admin::get_selected_color().'">'.$public_obj->get_points_for_signup().__('points','coupon-referral-program').'</span>'; _e(' bonus in return on Signup','coupon-referral-program');?>
                             </p>
                             <p><?php _e('5. You will also get the ','coupon-referral-program'); echo '<span class="mwb_cpr_highlight" style="color:'.Coupon_Referral_Program_Admin::get_selected_color().'">'.$public_obj->get_points_for_refree_signup().__('points','coupon-referral-program').'</span>'; _e(' on referral signup','coupon-referral-program');?>
                             </p>
                          <?php }?>                
                        </div>
                    </div>
                    <div class="mwb-pr-popup-right-rewards">
                      <div class="mwb-pr-popup-rewards-right-content">
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
            <!--mwb-pr-popup-body-->
            <?php }
              else { 
                $mwb_crp_bg_img = Coupon_Referral_Program_Admin::get_selected_image();
                ?>
                <div class="mwb_cpr_guest" style="background-image: url('<?php echo $mwb_crp_bg_img;?>');">
                  <div class="meb_cpr_guest__content">
                      <div class="meb_cpr_guest__content-text">
                        <?php _e('Signup to start sharing your link','coupon-referral-program');?>
                      </div>
                      <span class="mwb_cpr_btn_wrapper">
                        <a href="<?php echo wc_get_page_permalink( 'myaccount' );?>" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-cpr-btn" style="background-color: <?php echo Coupon_Referral_Program_Admin::get_selected_color();?>"><?php _e('Signup','coupon-referral-program');?></a>
                      </span>
                  </div>
                </div>
             <?php }  ?>
          </div>
          <div class="mwb_modal__footer mwb-pr-footer">
            <a class="mdl-button mdl-button--colored mdl-js-button  mwb_modal__close mwb-pr-close-btn" style="border-color: <?php echo Coupon_Referral_Program_Admin::get_selected_color();?> ; color:<?php echo Coupon_Referral_Program_Admin::get_selected_color();?>"> <?php _e('Close','coupon-referral-program');?>
            </a>
          </div>
        </div>
      </div>
    </div>
<?php

