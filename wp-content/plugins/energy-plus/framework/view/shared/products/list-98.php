<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php if (0 < count( $products )) {   ?>
  <h2 class="badge badge-black badge-pill __A__Badge_Big_Title"><?php esc_html_e('Products', 'energyplus'); ?></h2>
  <div class="__A__List_M1" id="energyplus-products-1">
    <?php foreach ( $products AS $product ) {   ?>
      <?php if ('variant' !== $product['type']) {   ?>
        <div class="btnA __A__Item collapsed"  id="item_<?php echo  esc_attr($product['id']) ?>" data-toggle="collapse" data-target="#item_d_<?php echo esc_attr($product['id']) ?>" aria-expanded="false" aria-controls="item_d_<?php echo  esc_attr($product['id']) ?>">
          <div class="liste  row d-flex align-items-center">
            <div class="__A__Col_Image col-3 col-sm-1 align-middle">
              <img src="<?php  echo  get_the_post_thumbnail_url(esc_attr($product['id']), array(150,150)); ?>" class="__A__Product_Image">
            </div>
            <div class="__A__Col_Title col-6 col-sm-5 align-middle">
              <?php echo esc_html($product['title']) ?>
            </div>
            <div class="align-middle col-2">
              <div class="__A__Price1" id="__A__Price_<?php echo esc_attr($product['id'])  ?>">
                <?php  echo str_replace("&ndash;", "", $product['price_html']); ?>
              </div>
            </div>
            <div class="__A__Col_3 col-2 align-middle text-center"  data-colname="Stock">
              <div class="__A__Stocks1" id="__A__Stock_<?php echo esc_attr($product['id'])  ?>">
                <?php if (true === $product['managing_stock']) {   ?>
                  <?php if (0 <  intval($product['stock_quantity'])) {
                    echo esc_html($product['stock_quantity']);
                  } else {
                    echo '<span class="badge badge-danger">' . esc_html__('Out Of Stock', 'energyplus'). '</span>';
                  }?>
                <?php } else {  ?>
                  <?php if (true ===  $product['in_stock']) {
                    echo '<span class="text-mute">∞</span>';
                  } else {
                    echo '<span class="badge badge-danger">' . esc_html__('Out Of Stock', 'energyplus'). '</span>';
                  }?>
                <?php } ?>
              </div>
              <div class="__A__Stocks text-left __A__Display_None">
                <input type="text" name="qnty" data-id="<?php echo  esc_attr($product['id'])?>"  value="<?php echo esc_attr($product['stock_quantity'])  ?>" class="__A__StockAjax">
                <br>
                <input type="checkbox" name="unlimited" data-id="<?php echo  esc_attr($product['id'])?>" class="__A__StockAjax" <?php if ((true !== $product['managing_stock'] && true === $product['in_stock']) OR (true === $product['managing_stock'] && 9999 === $product['stock_quantity'])) echo ' checked'; ?>> <?php esc_html_e('Unlimited', 'energyplus'); ?><br>
                <input type="checkbox" name="outofstock" data-id="<?php echo  esc_attr($product['id'])?>"  class="__A__StockAjax" <?php if ('1' <> $product['in_stock']) echo ' checked'; ?>> <?php esc_html_e('Out of Stocks', 'energyplus'); ?><br>
              </div>
            </div>
            <div class="__A__Col_Categories __A__Col_3 col-2 align-middle"  data-colname="Categories">
              <?php
              foreach ($product['categories'] AS $category) {  ?>
              <a href="<?php echo EnergyPlus_Helpers::admin_page('products', array( 'category' => $category->slug ));  ?>"><?php echo esc_html($category->name) ?></a><br />
            <?php }
            ?> &nbsp;
          </div>
        </div>
        <div class="collapse col-xs-12 col-sm-12 col-md-12 text-right" id="item_d_<?php echo  esc_attr($product['id']) ?>">
          <div class="__A__Item_Details ">
            <div class="containerx">
              <div class="row">
                <div class="col-12 col-sm-12 text-right __A__Product_Actions">
                  <a href="<?php echo admin_url( 'post.php?post=' . esc_attr($product['id']). '&action=edit&energyplus_hide' );?>" class="__A__StopPropagation trig"><?php esc_html_e('Edit product', 'energyplus'); ?></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  <?php } ?>
</div>

<p>&nbsp;</p><p>&nbsp;</p>

<?php } ?>
