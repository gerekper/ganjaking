<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

?>
<div style="font-size: 120%">
  <?php do_action('memberpress-helpscout-sidebar-header', $data); ?>
<?php

foreach($data->customer->emails as $email) {
  $u = new MeprUser();
  $u->load_user_data_by_email($email);
  $ts = strtotime($u->user_registered);

  if($u->ID <= 0) {
    ?>
      <div style="font-weight: bold; color: red;"><?php _e("No user record or active license was found for {$email}"); ?></div>
    <?php
  }
  else {
    do_action('memberpress-helpscout-sidebar-member-header', $data, $u);

    $uen_email = urlencode($u->user_email);

    ?>
      <div>
        <strong><?php _e('User:'); ?></strong>
        <a href="<?php echo admin_url("user-edit.php?user_id={$u->ID}"); ?>" target="_blank"><?php echo $u->user_login; ?></a>
      </div>
      <br/>
      <div>
        <strong><?php _e('Email:'); ?></strong>
        <?php echo $email; ?>
      </div>
      <br/>
      <div><strong><?php _e('Joined:'); ?></strong> <?php echo strftime('%D',$ts); ?></div>
      <br/>
    <?php

    $txns = $u->active_product_subscriptions('transactions');
    foreach($txns as $t) {
      //Don't show confirmations yo
      if($t->status != MeprTransaction::$complete_str) { continue; }

      $p = $t->product();
      $ets = strtotime($t->expires_at);

      do_action('memberpress-helpscout-sidebar-transaction-header', $data, $t, $u);

      ?>
        <div><strong><?php _e('Membership:'); ?></strong> <?php echo $p->post_title; ?></div>
        <div><strong><?php _e('Transaction:'); ?></strong> <?php echo $t->trans_num; ?></div>
        <?php
        if(($sub = $t->subscription())) {
          ?>
            <div><strong><?php _e('Subscription:'); ?></strong> <?php echo $sub->subscr_id; ?></div>
          <?php
        }
        ?>
        <div><strong><?php _e('Expires:'); ?></strong> <?php echo strftime('%D',$ets); ?></div>
        <br/>
      <?php

      do_action('memberpress-helpscout-sidebar-transaction-footer', $data, $t, $u);
    }
    ?>
      <div><strong><a href="<?php echo admin_url("user-edit.php?user_id={$u->ID}"); ?>" class="btn" style="width: 80%" target="_blank"><?php _e('User Profile'); ?></a></strong></div>
      <br/>
      <div><strong><a href="<?php echo admin_url("admin.php?page=memberpress-trans&search={$uen_email}&search-field=email"); ?>" class="btn" style="width: 80%" target="_blank"><?php _e('Transactions'); ?></a></strong></div>
      <br/>
      <div><strong><a href="<?php echo admin_url("admin.php?page=memberpress-subscriptions&search={$uen_email}&search-field=email"); ?>" class="btn" style="width: 80%" target="_blank"><?php _e('Subscriptions'); ?></a></strong></div>
      <br/>
      <hr/><br/>
    <?php

    do_action('memberpress-helpscout-sidebar-member-footer', $data, $u);
  }

}

?>
  <?php do_action('memberpress-helpscout-sidebar-footer', $data); ?>
</div>
<?php
