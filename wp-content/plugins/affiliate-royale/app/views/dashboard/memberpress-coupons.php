</ul>
<h3><?php _e('My Coupons', 'affiliate-royale', 'easy-affiliate'); ?></h3>
<ul class="wafp-link-list">
<?php foreach($my_coupons as $c_id): ?>
  <?php $c = new MeprCoupon($c_id); ?>
  <li>
    <div class="wafp-target-url">
      <strong><?php _e('Coupon Code', 'affiliate-royale', 'easy-affiliate'); ?>: </strong><?php echo $c->post_title; ?>
    </div>
    <div class="wafp-link-code">
      <strong><?php _e('Valid Products', 'affiliate-royale', 'easy-affiliate'); ?>:</strong><br/>
      <?php foreach($c->valid_products as $p_id): ?>
        <?php
          $p = new MeprProduct($p_id);
          $coupon_code = $p->url('?coupon='.$c->post_title.'&aff='.$aff_id, true);
        ?>
        <span><?php echo $p->post_title; ?></span><br/>
        <input type="text" style="display: inline-block;" onfocus="this.select();" onclick="this.select();" readonly="true" value="<?php echo $coupon_code; ?>" />
        <span class="wafp-clipboard"><i class="ar-icon-clipboard ar-list-icon icon-clipboardjs" data-clipboard-text="<?php echo $coupon_code; ?>"></i></span>

        <br/><br/>
      <?php endforeach; ?>
    </div>
  </li>
<?php endforeach; ?>
<style>
  embed {
    margin:0 !important;
  }
</style>
