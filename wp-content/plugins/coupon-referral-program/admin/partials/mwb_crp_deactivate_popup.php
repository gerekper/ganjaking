<?php 
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="modal-overlay">
  <div class="modal">
    <a class="close-modal">
      <svg viewBox="0 0 20 20">
        <path fill="#000000" d="M15.898,4.045c-0.271-0.272-0.713-0.272-0.986,0l-4.71,4.711L5.493,4.045c-0.272-0.272-0.714-0.272-0.986,0s-0.272,0.714,0,0.986l4.709,4.711l-4.71,4.711c-0.272,0.271-0.272,0.713,0,0.986c0.136,0.136,0.314,0.203,0.492,0.203c0.179,0,0.357-0.067,0.493-0.203l4.711-4.711l4.71,4.711c0.137,0.136,0.314,0.203,0.494,0.203c0.178,0,0.355-0.067,0.492-0.203c0.273-0.273,0.273-0.715,0-0.986l-4.711-4.711l4.711-4.711C16.172,4.759,16.172,4.317,15.898,4.045z"></path>
      </svg>
    </a>


    <div class="modal-content">
      <div id="mwb_admin_crp_loader" style="display: none;">
      <img src="<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL;?>public/images/loading.gif">
    </div>
      <h3><?php esc_html_e( 'If you have a moment, please provide some feedback before deactivate', 'coupon-referral-program' ); ?></h3>

      	<?php 
    
        $mwb_crp_options = Coupon_Referral_Program_Admin::mwb_crp_deactivate_options();
      	if (isset($mwb_crp_options) && !empty($mwb_crp_options) && is_array($mwb_crp_options)) {
  
          $i = 0; 
        	foreach ($mwb_crp_options as $key => $option) {
        		?>
        		<div class="mwb_crp_reason_wrap">
        			<?php if ($i==0){ ?>
        				<label for="mwb_crp_reason">
  		                <input type="radio" name="mwb_crp_reason" class="mwb_crp_reason_button" value="<?php echo $key;?>">
  		                <span><?php echo $option;?></span>
                      <textarea id="mwb_crp_message" name="mwb_crp_message" class="message form-control mwb_crp_message" style="height:100px"></textarea>
  		          </label>
        			<?php }else{ ?>
        				<label for="mwb_crp_reason">
  	                <input type="radio" name="mwb_crp_reason" class="mwb_crp_reason_button" value="<?php echo $key;?>">
  	                <span><?php echo $option; ?></span>
                     <textarea id="mwb_crp_message" name="mwb_crp_message" class="message form-control mwb_crp_message" style="height:100px"></textarea>
  	          </label>
        			<?php }?>
  	      		
            </div>
        		<?php
        		$i++;
        	}
        } 
      	?>
      <a href="#" class="button mwb_crp_skip"><?php esc_attr_e('Skip Now','coupon-referral-program'); ?></a>
      <a href="javascript:" class="button mwb_crp_cancel"><?php esc_attr_e('Cancel','coupon-referral-program'); ?>
      <a href="javascript:" class="button mwb_crp_deactivate"><?php esc_attr_e('Submit & Deactivate','coupon-referral-program'); ?></a>
    </div>
  </div>
</div>
<?php
