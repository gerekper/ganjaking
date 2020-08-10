<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>
<?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Coupons', 'energyplus'), 'description' => '', 'buttons'=>'<a href="' . admin_url( 'post-new.php?post_type=shop_coupon&energyplus_hide' ). '" class="btn btn-sm btn-danger trig"> ' . esc_html__(' &nbsp;+ &nbsp; New coupon &nbsp;', 'energyplus').'</a>')); ?>
<?php echo EnergyPlus_View::run('coupons/nav', array( 'counts' => $counts )) ?>

<div id="energyplus-coupons-stats">

  <div class="__A__Stats">
    <div class="energyplus-gp">
      <div class="row d-flex align-items-center">
        <div class="col">
          <div class="__A__Code"><span class="badge badge-pill badge-black"><?php echo esc_html($coupon['code']) ?></span></div>
          <?php  printf( esc_html__('Created at %s', 'energyplus'), $coupon['created_at']) ?></div>
          <div class="col"><div class="__A__Big"><?php echo esc_html( $coupon['usage_count'])?></div> <?php echo esc_html__('TIMES USED', 'energyplus'); ?></div>
          <div class="col"><div class="__A__Big"><?php echo wc_price($coupon['total_discount'])?></div> <?php echo esc_html__('TOTAL DISCOUNT', 'energyplus'); ?></div>
          <div class="col"><div class="__A__Big"><?php echo wc_price($coupon['total_sales'])?></div> <?php echo esc_html__('TOTAL REVENUE', 'energyplus'); ?></div>
          <div class="col"><div class="__A__Big"><?php if (0 < $coupon['total_sales']) {
            echo sprintf('%.2f', (100*$coupon['total_discount']/$coupon['total_sales']));
          } else {
            echo 0;
          } ?>%</div> <?php echo esc_html__('DISCOUNT PERCENT', 'energyplus'); ?></div>

        </div>

      </div>
    </div>

    <div class="__A__Stats">
      <div class="energyplus-gp text-center">
        <div class="__A__DotCal  ">
          <div class="row d-flex">
            <div class="__A__DotCal_Year">&nbsp;</div>
            <?php for ($i=1; $i<=12; ++$i) { ?>
              <div class="__A__DotCal_MonthNames d-flex justify-content-center align-items-center"><?php echo date_i18n("M", strtotime("2018-$i-01"))?></div>
            <?php } ?>
          </div>
          <?php foreach ($coupon['dots'] AS $year => $month) {?>
            <div class="row d-flex">
              <div class="__A__DotCal_Year  d-flex align-items-center"><?php echo esc_html($year) ?></div>
              <?php
              for ($i=1; $i<=12; ++$i) {
                $count = (isset($month[$i])?$month[$i]:'');
                if (0 === $count) {
                  $color = 0;
                } else {
                  $color = ceil(absint($count)/$coupon['dots_max']*3);
                }
                ?>
                <div class="__A__DotCal_Month d-flex justify-content-center align-items-center text-center __A__DotCal_Color_<?php echo esc_attr($color) ?>"><div class="__A__DotCal_Inner d-flex justify-content-center align-items-center text-center __A__DotCal_Colorx_<?php echo esc_attr($color) ?>"><?php echo esc_attr( $count ) ?></div></div>
              <?php } ?>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>

    <div id="energyplus-coupons-1" class="energyplus-gp">
      <?php foreach ($orders AS $order_group) { ?>
        <h4><?php echo esc_attr( $order_group['title'])?></h4>
        <?php foreach ($order_group['orders'] AS $order) { ?>
          <div class="energyplus-list--item row d-flex align-items-center">
            <div class="col col-lg-1 energyplus-orders--item-badge text-center"><?php
            switch ($order['status']) {
              case "on-hold": $badge = "warning"; break;
              case "pending": $badge = "danger"; break;
              case "processing": $badge = "info"; break;
              case "completed": $badge = "success"; break;
              case "refunded": $badge = "secondary"; break;
            }
            ?><span class="__A__Order_Text text-<?php echo esc_attr($badge);?>"><?php echo wc_get_order_status_name( $order['status'] ); ?></span>
            <br />
            <span class="__A__Order_Text __A__OrderDate"><?php echo esc_html( $order['order_date'] )?></span>
          </div>
          <div class="col-lg-2 __A__Coupons_Stats_Name"><?php echo esc_html( $order['customer']['name'] )?>
            <div class="__A__Adress"><?php echo esc_html( $order['customer']['city'] )?>, <?php echo esc_html( $order['customer']['state'] )?></div>
          </div>
          <div class="col col-lg-3 __A__Coupons_Items">
            <?php foreach ($order['line_items'] AS $item) { ?>
              <?php  echo EnergyPlus_Helpers::product_image(intval($item['product_id']), intval($item['quantity']), 'width: 50px'); ?>
            <?php }?>
          </div>
          <div class="col col-lg-2 text-center">
            <span class="energyplus-orders--item-price"><?php echo wc_price($order['discount']); ?></span>
            <br><span class="energyplus-orders--item-currency"><?php echo esc_html__('DISCOUNT', 'energyplus'); ?></span>
          </div>
          <div class="col col-lg-2 text-center">
            <span class="energyplus-orders--item-price"><?php echo wc_price($order['order_total']); ?></span>
            <br><span class="energyplus-orders--item-currency"><?php echo esc_html__('TOTAL', 'energyplus'); ?></span>
          </div>
          <div class="col-lg-1 text-right">
            <a href="<?php echo admin_url( 'post.php?post=' . $order['order_id']. '&action=edit&energyplus_hide' );?>" class="__A__Button1 trig"><?php echo esc_html__('View Order', 'energyplus'); ?></a>
          </div>
        </div>
      <?php } ?>
    <?php } ?>
  </div>
</div>
