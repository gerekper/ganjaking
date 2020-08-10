<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="d-flex flex-nowrap">
  <?php foreach ($results AS $item) { ?>
    <?php if ('true' === $item['active']) { ?>
      <div class="__A__I">
        <h2><?php echo (isset($item['is_price']) ? '<span class="woocommerce-Price-currencySymbol">'.get_woocommerce_currency_symbol().'</span>' . wc_price($item['count'],array('decimals' =>0)) : $item['count'])?></h2>
        <h4><?php echo esc_html($item['title'])?></h4>
      </div>
    <?php } ?>
  <?php } ?>
</div>
