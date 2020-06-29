<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://woocommerce.com/
 * @since      1.0.0
 *
 * @package    class-coupon-referral-program
 * @subpackage class-coupon-referral-program/public/partials
 */
$user_id = get_current_user_ID();
$mwb_crp_revenue = $this->get_revenue($user_id);
?>
<style type="text/css"><?php echo self::get_custom_style_popup_btn();?></style>
<div class="mwb-crp-referral-wrapper">
	<div class="mwb-crp-referral-column">
		<div class="mwb-crp-referral-column-inner">
			<div class="mwb-crp-referral-icon"><i class="fas fa-dollar-sign"></i></div>
			<span><?php _e('Total Utilization','coupon-referral-program'); ?></span>
			<h4><?php echo wc_price($this->get_utilize_coupon_amount($user_id)); ?></h4>
		</div>	
	</div>
	<div class="mwb-crp-referral-column">
		<div class="mwb-crp-referral-column-inner">
			<div class="mwb-crp-referral-icon"><i class="fas fa-users"></i></div>
			<span><?php _e('Total Referred Users','coupon-referral-program'); ?></span>
			<h4><?php echo $mwb_crp_revenue['referred_users']; ?></h4>
		</div>	
	</div>
	<div class="mwb-crp-referral-column">
		<div class="mwb-crp-referral-column-inner">
		<div class="mwb-crp-referral-icon"><i class="fas fa-credit-card"></i></div>
			<span><?php _e('Total Coupons','coupon-referral-program'); ?></span>
			<h4><?php echo $mwb_crp_revenue['total_coupon']; ?></h4>
		</div>	
	</div>
</div>
<div class="mwb-crp-referral-table-wrapper">
	<table id="mwb-crp-referral-table" class="mwb-crp-referral-table">
		<thead >
			<tr >
				<th class="mwb_crp_reporting_heading"><?php _e('Coupon ','coupon-referral-program') ?></th>
				<th class="mwb_crp_reporting_heading"><?php _e('Coupon Created','coupon-referral-program') ?></th>
				<th class="mwb_crp_reporting_heading"><?php _e('Expiry Date','coupon-referral-program') ?></th>
				<th class="mwb_crp_reporting_heading"><?php _e('Event','coupon-referral-program') ?></th>
				<th class="mwb_crp_reporting_heading"><?php _e('Referred Users','coupon-referral-program') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if(!empty($this->get_signup_coupon($user_id)) && is_array($this->get_signup_coupon($user_id))):
				$signup_coupon  = $this->get_signup_coupon($user_id);
				$coupon = new WC_Coupon($signup_coupon['singup'] );
				if ( 'publish' == get_post_status ( $signup_coupon['singup'] ) && $coupon->is_valid()) :
			?>
			<tr>
				<td data-th="Coupon">
					<div class="mwb-crp-coupon-code">
						<p id="<?php echo 'mwb'.$signup_coupon['singup']; ?>">
							<?php echo $coupon->get_code();?>
						</p>
						<span class="mwb-crp-coupon-amount"><?php echo ($coupon->get_discount_type() == "fixed_cart")?
							wc_price($coupon->get_amount()) : $coupon->get_amount()."%"; ?>
						</span> 
						<img class="mwb-crp-coupon-scissors" src="<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL."public/images/scissors.png"; ?>" alt=""> 
						<span class="mwb-crp-coupon-wrap">
							<button class="mwb-crp-coupon-btn-copy"
							data-clipboard-target="#mwb<?php echo $signup_coupon['singup']; ?>" aria-label="copied">
								<span class="mwb-crp-coupon-tooltiptext"><?php _e('Copy','coupon-referral-program') ;?></span>
								<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext"><?php _e('Copied','coupon-referral-program') ;?></span>
								<img src="<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL.'admin/images/copy.png';?>" alt="">
						   </button>
					    </span>
				    </div>
				</td>
				<td data-th="Coupon Created"><?php echo (!empty($coupon->get_date_created()))?$coupon->get_date_created()->date(wc_date_format()):"---"; ?></td>
				<td data-th="Expiry Date"><?php echo $expiry_date = $coupon->get_date_expires( 'edit' ) ? $coupon->get_date_expires( 'edit' )->date( wc_date_format()) : _e("Never","coupon-referral-program"); ?></td>
				<td data-th="Event"><?php _e('Signup Coupon','coupon-referral-program') ?></td>
				<td data-th="Referred Users">----</td>
			</tr>
			<?php endif;?>
			<?php endif;?>
			<!-- Refferal sigup -->
			<?php if(!empty($this->mwb_crp_get_referal_signup_coupon($user_id))):
			  foreach ($this->mwb_crp_get_referal_signup_coupon($user_id) as $coupon_code => $crp_user_id):
			  	  $coupon = new WC_Coupon($coupon_code);
			  	if ( 'publish' == get_post_status ( $coupon_code) && $coupon->is_valid()):
			?>
			<tr>
				<td data-th="Coupon">
					<div class="mwb-crp-coupon-code"><p id="mwb<?php echo $coupon_code; ?>"><?php echo $coupon->get_code(); ?></p>
						<span class="mwb-crp-coupon-amount"><?php echo ($coupon->get_discount_type() == "fixed_cart")?
							wc_price($coupon->get_amount()) : $coupon->get_amount()."%"; ?></span> <img class="mwb-crp-coupon-scissors" src="<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL."public/images/scissors.png"; ?>" alt=""> <span class="mwb-crp-coupon-wrap">
							<button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb<?php echo $coupon_code; ?>" aria-label="copied">
								<span class="mwb-crp-coupon-tooltiptext"><?php _e('Copy','coupon-referral-program') ;?></span>
								<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext"><?php _e('Copied','coupon-referral-program') ;?></span>
								<img src="<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL.'admin/images/copy.png';?>" alt="">
							</button>
						</span>
					</div>
				</td>
				<td data-th="Coupon Created"><?php echo $coupon->get_date_created()->date(wc_date_format()); ?></td>
				<td data-th="Expiry Date"><?php echo $expiry_date = $coupon->get_date_expires( 'edit' ) ? $coupon->get_date_expires( 'edit' )->date( wc_date_format()) : _e("Never","coupon-referral-program"); ?></td>
				<td data-th="Event"><?php _e('Referral Signup','coupon-referral-program'); ?></td>
				<td data-th="Referred Users"><?php echo get_userdata($crp_user_id)?get_userdata($crp_user_id)->data->display_name :__("User has been deleted","coupon-referral-program"); ?></td>
			</tr>
			<?php endif;?>
			<?php endforeach;
			endif;?>
			<!-- End Refferal sigup -->
			<?php if(!empty($this->get_referral_purchase_coupons($user_id))):
			  foreach ($this->get_referral_purchase_coupons($user_id) as $coupon_code => $crp_user_id):
			  	  $coupon = new WC_Coupon($coupon_code);
			  	if ( 'publish' == get_post_status ( $coupon_code) && $coupon->is_valid()):
			?>
			<tr>
				<td data-th="Coupon">
					<div class="mwb-crp-coupon-code"><p id="mwb<?php echo $coupon_code; ?>"><?php echo $coupon->get_code(); ?></p>
						<span class="mwb-crp-coupon-amount"><?php echo ($coupon->get_discount_type() == "fixed_cart")?
							wc_price($coupon->get_amount()) : $coupon->get_amount()."%"; ?></span> <img class="mwb-crp-coupon-scissors" src="<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL."public/images/scissors.png"; ?>" alt=""> <span class="mwb-crp-coupon-wrap">
							<button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb<?php echo $coupon_code; ?>" aria-label="copied">
								<span class="mwb-crp-coupon-tooltiptext"><?php _e('Copy','coupon-referral-program') ;?></span>
								<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext"><?php _e('Copied','coupon-referral-program') ;?></span>
								<img src="<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL.'admin/images/copy.png';?>" alt="">
							</button>
						</span>
					</div>
				</td>
				<td data-th="Coupon Created"><?php echo $coupon->get_date_created()->date(wc_date_format()); ?></td>
				<td data-th="Expiry Date"><?php echo $expiry_date = $coupon->get_date_expires( 'edit' ) ? $coupon->get_date_expires( 'edit' )->date( wc_date_format()) : _e("Never","coupon-referral-program"); ?></td>
				<td data-th="Event"><?php _e('Referral Purchase','coupon-referral-program'); ?></td>
				<td data-th="Referred Users"><?php echo get_userdata($crp_user_id)?get_userdata($crp_user_id)->data->display_name :__("User has been deleted","coupon-referral-program"); ?></td>
			</tr>
			<?php endif;?>
			<?php endforeach;
			endif;?>
			
		</tbody>
		
	</table>
</div>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
