<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
} ?>
<?php if (0 < count($notifications)) {  ?>
  <div class="container">
    <div class="notification-area">
      <ul class="notification-bar">
        <?php foreach ($notifications AS $message) {  ?>
          <li class="__A__Notifications_Type_<?php echo esc_attr($message['type']) ?> __A__Notifications_Status_<?php echo (isset($message['status'])?esc_attr($message['status']):0)?>">
            <div>
              <age><?php printf( __( '%s ago', 'energyplus' ), human_time_diff( strtotime($message["time"]), current_time( 'timestamp' ) ) ); ?></age>
              <header><?php echo wp_kses_post($message["title"])?></header>

              <?php if ("2" === $message["type"]) {  // Orders ?>
                <?php if (isset($message['details'])) {  ?>
                  <div class="__A__Details container">
                    <div class="row">
                      <div class="col-6 __A__Content __A__Notifications_OrderTotal">
                        <h6><?php echo esc_html($message['details']['customer'])?></h6>
                        <?php echo esc_html($message['details']['city'])?>
                        <br>
                        <?php echo esc_html($message['details']['payment_method_title'])?>

                      </div>
                      <div class="col-5 text-right __A__Notifications_OrderTotal">
                        <h4><?php  echo wp_kses_post($message['details']['total']); ?></h4>
                        <span class="text-uppercase badge badge-pill badge-secondary badge-<?php echo esc_html($message['details']['status'])?>"><?php echo wc_get_order_status_name($message['details']['status'])?></span>
                      </div>

                    </div>
                    <div class="row __A__Action">

                      <ul>
                        <li class="text-right">
                          <a href="<?php echo admin_url( 'post.php?post=' . intval($message['details']['order_id']). '&action=edit&energyplus_hide' );?>" class="trig __A__Close_Before_Trig"><?php _e('View Order', 'energyplus') ?></a>
                        </li>
                      </div>
                    </div>
                  <?php } ?>
                <?php } ?>

                <?php if ("4" === $message["type"]) {  // Comments ?>
                  <?php if (isset($message['details'])) {  ?>
                    <div class="__A__Details container">
                      <div class="row">
                        <div class="col-1">
                          <img src="<?php echo get_the_post_thumbnail_url(intval($message['details']['post_id'])); ?>" class="__A__Product_Image __A__Product_Image_Not" >

                        </div>
                        <div class="col-10 __A__Content">
                          <?php $stars = intval($message['details']['star']);?>
                          <div class="__A__Stars">
                            <span class="__A__StarsUp"><?php echo str_repeat('★ ', $stars); ?></span>
                            <span class="__A__StarsDown"><?php echo str_repeat('★ ', 5-$stars); ?></span>
                          </div>
                          <?php  echo esc_html($message['details']['comment_content'])?>
                          <br><br>
                        </div>
                      </div>
                      <div class="row __A__Action">
                        <ul>
                          <li  class="text-right">
                            <a href="<?php echo admin_url('comment.php?action=editcomment&c=' .intval($message['details']['comment_id'])) ?>" class="trig"><?php esc_html_e('View Comment', 'energyplus') ?></a>
                          </li>

                        </ul>
                      </div>
                    </div>
                  <?php } ?>
                <?php } ?>


                <?php if ("11" === $message["type"]) {  // Coupons ?>
                  <?php if (isset($message['details'])) {  ?>
                    <div class="__A__Details container">
                      <div class="row">

                        <div class="col-12 __A__Content">
                          <?php printf(__('Coupon <span class="badge badge-pill badge-black text-uppercase">%s</span> usage limit (%s) has been reached', 'energyplus'),  esc_attr($message['details']['coupon_code']),  intval($message['details']['usage'])) ?> <br />&nbsp;<br />
                        </div>
                      </div>
                      <div class="row __A__Action">
                        <ul>
                          <li  class="text-right">
                            <a href="<?php echo admin_url('post.php?post=' . intval($message['details']['coupon_id']). '&action=edit&energyplus_hide') ?>" class="trig"><?php _e('View Coupon', 'energyplus') ?></a>
                          </li>

                        </ul>
                      </div>
                    </div>
                  <?php } ?>
                <?php } ?>

                <?php if ("12" === $message["type"]) {  ?>
                  <div class="__A__Details container">
                    <div class="row">
                      <div class="col-12 __A__Details_Info_Text">
                        <?php echo wp_kses_post( $message['details']['message'] ); ?>
                      </div>
                    </div>
                  </div>
                <?php } ?>

                <?php if ("14" === $message["type"]) {  // Stock ?>
                  <?php if (isset($message['details'])) {  ?>
                    <div class="__A__Details container">
                      <div class="row">

                        <div class="col-11 __A__Content text-center">
                          <h2 class="__A__Widget_onlineusers_Notice">
                            <img src="<?php echo get_the_post_thumbnail_url(intval($message['details']['product_id'])); ?>" class="__A__Product_Image __A__Product_Image_Not" style="min-height:50px;min-width:50px;vertical-align: middle;">
                            →
                            <?php echo esc_html($message['details']['qty']) ?>
                          </h2>
                          <?php echo sprintf (esc_html__("Low stock for %s.", 'energyplus'), $message['details']['product_name']) ?>
                          <br>                        <br>

                        </div>
                      </div>
                      <div class="row __A__Action">
                        <ul>
                          <li  class="text-right">
                            <a href="<?php echo admin_url('post.php?action=edit&post=' .intval($message['details']['product_id'])) ?>" class="trig"><?php esc_html_e('Edit Product', 'energyplus') ?></a>
                          </li>

                        </ul>
                      </div>
                    </div>
                  <?php } ?>
                <?php } ?>

                <?php if ("15" === $message["type"]) {  ?>
                  <?php if (isset($message['details'])) {  ?>
                    <div class="__A__Details container">
                      <div class="row">


                        <div class="col-11 __A__Content text-center pt-2">
                          <?php if ('empty' !== $message['details']['icon']) { ?>
                            <i class="<?php echo esc_attr($message['details']['icon']) ?>" style="font-size:42px;color:#aaa;"></i>
                            <br>  <br>
                          <?php } ?>

                          <?php echo nl2br(wp_kses_post($message['details']['content'])) ?>
                          <br><br>
                        </div>
                      </div>

                      <div class="row __A__Action">
                        <ul>
                          <li  class="text-left">
                            <a href="javascript:;"><?php $created_by = get_userdata($message['details']['created_by']);  echo esc_html( $created_by->display_name) ?></a>
                          </li>
                          <li  class="text-right">
                            <a href="javascript:;"><?php echo date_i18n('M d, H:i', strtotime($message['time'])) ?></a>
                          </li>

                        </ul>
                      </div>
                    </div>
                  <?php } ?>
                <?php } ?>

              </div>
            </li>
          <?php }?>

        </ul>
      </div>
    </div>

  <?php } else { ?>
    <div class="container">
      <div class="notification-area">
        <div class="__A__EmptyTable d-flex align-items-center justify-content-center text-center">
          <div><br><?php esc_html_e('No notification', 'energyplus'); ?></div>
        </div>
      </div>
    </div>
  <?php } ?>
