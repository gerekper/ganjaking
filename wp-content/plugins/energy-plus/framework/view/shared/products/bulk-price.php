<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-in'); ?>

<div class="energyplus-title inbrowser">
  <h3><?php esc_html_e('Change prices', 'energyplus'); ?></h3>
</div>

<form action="" method="POST">

  <div class="container-fluid" id="bulk-prices">
    <div class="row">
      <div class="w-100">
        <div class="c" id="headingOne">
          <h5 class="mb-3">
            <input class="change_type" type="radio" name="type" value="1" checked>
            <?php esc_html_e('I want to increase the prices', 'energyplus'); ?>
          </h5>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="w-100">
        <div id="collapseOne" class="collapse show" data-toggle="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">%</span>
                  </div>
                  <input type="text" class="form-control percent_1" name="percent_1" placeholder="<?php esc_html_e('Percent', 'energyplus'); ?>" aria-label="<?php esc_html_e('Percent', 'energyplus'); ?>" autofocus>
                </div>
              </div>
              <div class="col-1 text-center __A__And_Or d-flex align-items-center">
                <div class="mb-3">
                  <?php esc_html_e('AND', 'energyplus'); ?>
                </div>
              </div>
              <div class="col-6">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><?php echo esc_html(get_woocommerce_currency_symbol()) ?></span>
                  </div>
                  <input type="text" class="form-control fixed_1" name="fixed_1" placeholder="<?php esc_html_e('Fixed', 'energyplus'); ?>" aria-label="<?php esc_html_e('Fixed', 'energyplus'); ?>">
                </div>
              </div>
            </div>

          </div>
        </div>
        <div class="" id="headingTwo">
          <h5 class="mb-3">
            <input class="change_type" type="radio" name="type" value="2">
            <?php esc_html_e('I want to decrease the prices', 'energyplus'); ?>
          </h5>
        </div>
        <div id="collapseTwo" class="collapse" data-toggle="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
          <div class="card-body">
            <div class="row">
              <div class="col-5">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">%</span>
                  </div>
                  <input type="text" class="form-control percent_2" name="percent_2" placeholder="<?php esc_html_e('Percent', 'energyplus'); ?>" aria-label="<?php esc_html_e('Percent', 'energyplus'); ?>" >
                </div>
              </div>
              <div class="col-1 text-center __A__And_Or d-flex align-items-center">
                <div class="mb-3">
                  <?php esc_html_e('AND', 'energyplus'); ?>
                </div>
              </div>
              <div class="col-6">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><?php echo esc_html(get_woocommerce_currency_symbol()) ?></span>
                  </div>
                  <input type="text" class="form-control fixed_2" name="fixed_2" placeholder="<?php esc_html_e('Fixed', 'energyplus'); ?>" aria-label="<?php esc_html_e('Fixed', 'energyplus'); ?>">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    <div class="row">
      &nbsp;
    </div>
    <div class="row">
      <input type="submit" class="btn btn-primary" value="<?php esc_html_e('Apply new prices', 'energyplus'); ?>"/>
    </div>

    <div class="row mb-10">
      &nbsp;
      <hr class="w-100" />
      <br />
      <br />
    </div>

    <div class="row mb-2">
      <h5><?php esc_html_e('Preview', 'energyplus'); ?></h5>
    </div>

    <div class="row">
      <table class="table table-bordered">
        <thead>
          <th>
            <?php esc_html_e('Product', 'energyplus'); ?>
          </th>
          <th class="text-right">
            <?php esc_html_e('New Regular Price', 'energyplus'); ?>
          </th>
          <th class="text-right">
            <?php esc_html_e('New Sale Price', 'energyplus'); ?>
          </th>
          <th class="text-right">
            <?php esc_html_e('Old Price', 'energyplus'); ?>
          </th>
          <th class="text-right bg-light">
            <?php esc_html_e('New Price', 'energyplus'); ?>
          </th>
        </thead>
        <tbody>
          <?php foreach ($products AS $product) {  ?>
            <?php if ($product->is_type( 'simple' )) {  ?>
              <tr>
                <td>
                  <?php echo esc_html($product->get_name()); ?>
                </td>
                <td class="text-right text-muted">
                  <span class="change_price new_regular" data-old='<?php echo esc_attr($product->get_regular_price()); ?>'><?php
                  if ($product->get_regular_price()) {
                    echo number_format($product->get_regular_price(), wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator());
                  } ?></span> <?php if ($product->get_regular_price()) { echo get_woocommerce_currency_symbol(); } ?>
                </td>
                <td class="text-right text-muted">
                  <span class="change_price new_sale" data-old='<?php echo esc_attr($product->get_sale_price()); ?>'><?php
                  if ($product->get_sale_price()) {
                    echo number_format($product->get_sale_price(), wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator());
                  } ?></span> <?php if ($product->get_sale_price()) { echo get_woocommerce_currency_symbol(); } ?>
                </td>
                <td class="text-right text-muted">
                  <span class="old_price" data-old='<?php echo esc_attr($product->get_price()); ?>'><?php echo number_format($product->get_price(), wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator()); ?></span> <?php echo get_woocommerce_currency_symbol();?>
                </td>
                <td class="text-right font-weight-bold bg-light">
                  <span class="change_price" data-old='<?php echo esc_attr($product->get_price()); ?>'><?php echo number_format($product->get_price(), wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator()); ?></span> <?php echo get_woocommerce_currency_symbol();?>
                </td>
              </tr>
            <?php } else {  ?>
              <tr>
                <td>
                  <?php echo esc_html($product->get_name()); ?>
                </td>
                <td colspan="4" class="text-muted __A__Bulk_No">
                  <?php esc_html_e("Because of this is a variable product you can't change price", 'energyplus'); ?>
                </td>
              </tr>
            <?php }  ?>
          <?php }  ?>
        </tbody>
      </table>
    </div>
  </div>
</form>
