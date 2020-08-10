<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php if (!$ajax) {  ?>

  <?php echo EnergyPlus_View::run('header-energyplus'); ?>
  <?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Products', 'energyplus'), 'description' => '', 'buttons'=> '<a href="' . admin_url( 'post-new.php?post_type=product' ). '" class="btn btn-sm btn-danger trig"> + &nbsp; ' . esc_html__('New product', 'energyplus'). ' &nbsp;</a>')); ?>
  <?php echo EnergyPlus_View::run('products/nav' ) ?>

  <div id="energyplus-products-1" class="energyplus-products ">
    <div class="__A__Searching<?php if ('' === EnergyPlus_Helpers::get('s', '')) echo" closed"; ?>">
      <div class="__A__Searching_In">
        <div class="input-group">
          <input type="text" class="form-control __A__Search_Input" aria-label="<?php esc_html_e('Search in products...', 'energyplus'); ?>" placeholder="<?php esc_html_e('Search in products...', 'energyplus'); ?>" value="<?php echo esc_attr(EnergyPlus_Helpers::get('s'));  ?>">
          <div class="input-group-append">
            <input type="hidden" name='__A__Input_Status' class='__A__Input_Status' value='<?php echo esc_attr(EnergyPlus_Helpers::get('category'));  ?>' />
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php if ('' !== EnergyPlus_Helpers::get('category', '')) { echo esc_html(EnergyPlus_Helpers::get('category', ''));} else { echo esc_html__('All Categories', 'energyplus'); }?></button>
              <div class="dropdown-menu">
                <a class="dropdown-item __A__Products_Cat_Dropdown" href="javascript:;" data-slug=''><?php esc_html_e('All Categories', 'energyplus'); ?></a>

                <?php foreach($categories[0] AS $category) { ?>
                  <a class="dropdown-item __A__Products_Cat_Dropdown" href="javascript:;" data-slug='<?php echo esc_attr($category['slug'])?>'><?php echo esc_html($category['name'])?></a>
                <?php } ?>

              </div>
            </div>
          </div>

          <input type="text" class="form-control __A__Search_Input __A__Display_None" placeholder="<?php esc_html_e('Search in products...', 'energyplus'); ?>" value="<?php echo esc_attr(EnergyPlus_Helpers::get('s'));  ?>" autofocus></span>
        </div>
      </div>
      <div class="__A__Container __A__GP">
      <?php } ?>
      <div class="row">

        <div class="col-12 col-lg-<?php if (!$ajax && '' === EnergyPlus_Helpers::get('s', '')) { echo '9'; } else { echo '12'; } ?> __A__Right">
          <div class="__A__GP __A__List_M1 __A__Container">

            <div class="__A__List_M1_Bulk __A__Bulk __A__Display_None">
              <?php if ('trash' === EnergyPlus_Helpers::get('status')) {  ?>
                <a class="__A__Button1 __A__Bulk_Do __A__Bulk_Restore" data-do="restore" data-status='restore' href="javascript:;"><?php esc_html_e('Restore products', 'energyplus'); ?></a>
                <a class="__A__Button1 __A__Bulk_Do __A__Bulk_Restore" data-do="deleteforever" data-status='deleteforever' href="javascript:;"><?php esc_html_e('Delete forever', 'energyplus'); ?></a>
              <?php } else {  ?>
                <a class="__A__Button1 __A__Bulk_Change_Price trig" data-do="bulk_price" href="<?php echo EnergyPlus_Helpers::admin_page('products', array('action' => 'bulk_price'))?>"><?php esc_html_e('Change Prices', 'energyplus'); ?></a>
                <a class="__A__Button1 __A__Bulk_Do" data-do="outofstock" href="javascript:;"><?php esc_html_e('Set to &mdash; Out of stock', 'energyplus'); ?></a>
                <a class="__A__Button1 __A__Bulk_Do" data-do="instock" href="javascript:;"><?php esc_html_e('Set to &mdash; In stock', 'energyplus'); ?></a>
                <a class="__A__Button1 __A__Bulk_Do" data-do="trash" data-status='trash' href="javascript:;"><?php esc_html_e('Delete', 'energyplus'); ?></a>

              <?php } ?>
              <a class="__A__Select_All float-right" data-state='select' href="javascript:;"><?php esc_html_e('Select All', 'energyplus'); ?></a>
            </div>

            <?php if (0 === count( $products )) {  ?>
              <div class="__A__EmptyTable d-flex align-items-center justify-content-center text-center">
                <div>  <span class="dashicons dashicons-marker"></span><br><?php esc_html_e('No records found', 'energyplus'); ?></div>
              </div>
            </div>
          <?php } else {  ?>

            <?php if ($slug = sanitize_text_field(EnergyPlus_Helpers::get('category'))) {  ?>
              <?php $category = get_term_by('slug', $slug, 'product_cat');
              if ($category) {
                echo "<h4 class='__A__Cat_Title'>".esc_html($category->name)."</h4>";
              } elseif ('-1' === $slug) {
                echo "<h4 class='__A__Cat_Title'>" . esc_html__('Critical Stock', 'energyplus'). "</h4>";
              } elseif ('-2' === $slug) {
                echo "<h4 class='__A__Cat_Title'>". esc_html__('Out of stock', 'energyplus')."</h4>";
              } elseif ('-3' === $slug) {
                echo "<h4 class='__A__Cat_Title'>". esc_html__('Trash', 'energyplus')."</h4>";
              } elseif ('-4' === $slug) {
                echo "<h4 class='__A__Cat_Title'>". esc_html__('Sales', 'energyplus')."</h4>";
              } ?>
            <?php } elseif ('trash' === EnergyPlus_Helpers::get('status')) {  ?>
              <h4 class='__A__Cat_Title'><?php esc_html_e('Trashed Products', 'energyplus'); ?></h4>
            <?php } elseif ('private' === EnergyPlus_Helpers::get('status')) {  ?>
              <h4 class='__A__Cat_Title'><?php esc_html_e('Private Products', 'energyplus'); ?></h4>
            <?php } elseif ('draft' === EnergyPlus_Helpers::get('status')) {  ?>
              <h4 class='__A__Cat_Title'><?php esc_html_e('Draft Products', 'energyplus'); ?></h4>
            <?php } else {  ?>
              <h4 class='__A__Cat_Title'><?php esc_html_e('All Products', 'energyplus'); ?></h4>
            <?php } ?>
            <hr />

            <div class="__A__Products_Sortable">
              <?php if ($products) {
                foreach ( $products AS $product ) {  ?>
                <?php if ('variant' !== $product['type']) {  ?>
                  <div class="btnA __A__Item collapsed"   data-id="<?php echo esc_attr($product['id']) ?>" id="item_<?php echo  esc_attr($product['id']) ?>" data-toggle="collapse" data-target="#item_d_<?php echo esc_attr($product['id']) ?>" aria-expanded="false" aria-controls="item_d_<?php echo  esc_attr($product['id']) ?>">
                    <div class="liste  row d-flex align-items-center">

                      <?php if ('variant' !== $product['type']) {   ?>

                        <div class="__A__Checkbox_Hidden">
                          <input type="checkbox" class="__A__Checkbox __A__StopPropagation"  data-id='<?php echo esc_attr($product['id'])  ?>'>
                        </div>

                        <div class="__A__Col_Image col-3 col-sm align-middle">
                          <img src="<?php  echo  get_the_post_thumbnail_url($product['id'], array(150,150)); ?>" class="__A__Product_Image">

                        </div>
                        <div class="__A__Col_Title col-6 col-sm-3 align-middle">

                          <?php echo esc_html($product['title']) ?>
                          <button class="__A__Mobile_Actions __A__M21 __A__Display_None"><span class="dashicons dashicons-arrow-down-alt2"></span></button>

                        </div>
                        <div class="align-middle col-2">
                          <div class="__A__Price1" id="__A__Price_<?php echo esc_attr($product['id'])  ?>">
                            <?php echo str_replace("&ndash;", "", $product['price_html']); ?>
                          </div>

                        </div>
                        <div class="__A__Col_3 col-2 align-middle text-center"  data-colname="Stock">
                          <div class="__A__Stocks1" id="__A__Stock_<?php echo esc_attr($product['id'])  ?>">
                            <?php if (true === $product['managing_stock']) {  ?>
                              <?php if (0 <  intval($product['stock_quantity'])) {
                                echo esc_html(intval($product['stock_quantity']));
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
                        </div>
                        <div class="__A__Col_3 col-1 align-middle"  data-colname="Visible">
                          <?php if ('trash' !== $product['status']) {  ?>
                            <label class="switch __A__StopPropagation">
                              <input type="checkbox" value="1" data-id="<?php echo  esc_attr($product['id'])?>" class="success __A__OnOff" <?php if ('visible' === $product['catalog_visibility']) echo ' checked'; ?> />
                              <span class="__A__slider round"></span>
                            </label>
                          <?php }?>
                        </div>
                        <div class="__A__Col_Categories __A__Col_3 col-2 align-middle"  data-colname="Categories">
                          <?php
                          foreach ($product['categories'] AS $category) { ?>
                          <a href="<?php echo EnergyPlus_Helpers::admin_page('products', array( 'category' => $category->slug ));  ?>"><?php echo esc_html($category->name) ?></a><br />
                        <?php }
                        ?>
                        <a class="__A__Products_Hand" data-id="<?php echo esc_attr($product['id']) ?>" href="javascript:;"><span>≡</span></a>
                      </div>
                    <?php } ?>
                  </div>

                  <div class="collapse col-xs-12 col-sm-12 col-md-12 text-right" id="item_d_<?php echo esc_attr($product['id']) ?>">
                    <div class="__A__Item_Details ">
                      <div class="containerx">
                        <div class="row">
                          <?php if ('trash' !== $product['status']) {  ?>
                            <?php if ('variable' === $product['type']) {  ?>
                              <div class="col col-sm-9 d-none d-sm-inline __A__StopPropagation">
                                <p>&nbsp;</p>
                                <span class="dashicons dashicons-info"></span> &nbsp; <?php esc_html_e('This is a variable product, so you can not edit it directly', 'energyplus'); ?>
                              </div>
                            <?php } else {  ?>
                              <div class="col col-sm-3 d-none d-md-inline __A__StopPropagation">
                                <h4><?php esc_html_e('Price', 'energyplus'); ?></h4>
                                <span class="input-price-container"><?php echo esc_html( get_woocommerce_currency_symbol() ) ?></span>
                                <span class="input-price-container __A__Products_Input_Price"><?php echo esc_html( get_woocommerce_currency_symbol() ) ?></span>
                                <input type="text" name="regular_price" class="__A__PriceAjax __A__PriceAjax_Regular form-control" data-id="<?php echo esc_attr($product['id'])?>"  placeholder="<?php esc_html_e('Regular Price', 'energyplus'); ?>" value="<?php echo esc_attr($product['regular_price'])  ?>"/>
                                <input type="text" name="sale_price" class="__A__PriceAjax __A__PriceAjax_Sale form-control" data-id="<?php echo esc_attr($product['id']); ?>" placeholder="<?php esc_html_e('Sale Price', 'energyplus'); ?>" value="<?php echo esc_attr($product['sale_price']) ?>"/>
                                <button data-id="<?php echo esc_attr($product['id'])?>" class="__A__PriceAjax1 button button-sm btn-black"><?php esc_html_e('Save', 'energyplus'); ?></button>
                              </div>
                              <div class="col-1">
                              </div>
                              <div class="col col-sm-3  d-none d-md-inline  __A__StopPropagation">
                                <h4><?php esc_html_e('Stock', 'energyplus'); ?></h4>
                                <input type="text" name="qnty" data-id="<?php echo esc_attr($product['id'])?>"  value="<?php echo esc_attr($product['stock_quantity'])  ?>" class="__A__StockAjax2 form-control"> <button data-id="<?php echo esc_attr($product['id']); ?>" class="__A__StockAjax1 button button-sm btn-black"><?php esc_html_e('Save', 'energyplus'); ?></button><br />
                                <input type="checkbox" name="unlimited" data-id="<?php echo esc_attr($product['id'])?>" class="__A__StockAjax" <?php if ((true !== $product['managing_stock'] && true === $product['in_stock']) OR ( true === $product['managing_stock'] && 9999 === $product['stock_quantity'])) echo ' checked'; ?>> <?php esc_html_e('Unlimited', 'energyplus'); ?><br>
                                <input type="checkbox" name="outofstock" data-id="<?php echo esc_attr($product['id'])?>"  class="__A__StockAjax" <?php if (true !== $product['in_stock']) echo ' checked'; ?>> <?php esc_html_e('Out of Stocks', 'energyplus'); ?><br>
                              </div>
                              <div class="col-2">
                              </div>
                            <?php } ?>
                          <?php } ?>
                          <?php if ('trash' === $product['status']) {  ?>
                            <div class="col-12 col-sm-12 __A__Product_Actions text-right">
                              <a href="<?php echo wp_nonce_url("post.php?action=untrash&amp;post=" . $product['id'] ,"untrash-post_" . $product['id']); ?>" class="__A__StopPropagation"><?php esc_html_e('Restore product', 'energyplus'); ?></a> &nbsp; &nbsp;
                              <a href="<?php echo get_delete_post_link( $product['id'], false, true ); ?>" class="__A__StopPropagation __A__CommentStatusButton_Red"><?php esc_html_e('Delete forever', 'energyplus'); ?></a> &nbsp; &nbsp;
                            <?php } else {  ?>
                              <div class="col-12 col-sm-3 __A__Product_Actions">
                                <a href="<?php echo admin_url( 'post.php?post=' . $product['id']. '&action=edit&energyplus_hide' );?>" class="__A__StopPropagation trig" data-hash="<?php echo esc_attr($product['id'])?>"><?php esc_html_e('Edit product', 'energyplus'); ?></a>
                                <br /><br />
                                <a href="<?php echo esc_url( get_post_permalink ($product['id']))?>" class="__A__StopPropagation" target="_new"><?php esc_html_e('View product page', 'energyplus'); ?></a>
                                <br /><br />
                                <a href="<?php echo get_delete_post_link( $product['id'], false, false ); ?>" class="__A__StopPropagation __A__CommentStatusButton_Red"><?php esc_html_e('Delete', 'energyplus'); ?></a>
                              <?php } ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              <?php }
            } ?>
            </div>
          </div>
        <?php } ?>

        <?php if (isset($pagination->found_posts)) {  ?>
          <?php  echo EnergyPlus_View::run( 'core/pagination', array( 'count' => $pagination->found_posts, 'per_page'=> absint(EnergyPlus::option('reactors-tweaks-pg-products', 10)), 'page' => intval ( EnergyPlus_Helpers::get( 'pg', 0 ) ), 'url' => remove_query_arg( 'pg', EnergyPlus_Helpers::admin_page('products', array('status' => EnergyPlus_Helpers::get('status'), 'category'=> isset($filter['category'])?$filter['category']:'',  'parents'=> EnergyPlus_Helpers::get('parents'), 's' => EnergyPlus_Helpers::get('s',''),  'orderby' => EnergyPlus_Helpers::get('orderby',''), 'order' => EnergyPlus_Helpers::get('order','')  ))) )); ?>
        <?php } ?>

      </div>

      <div class="col-lg-3 __A__Channels <?php if ('' !== EnergyPlus_Helpers::get('s', '')) echo" hidden"; ?>">
        <ul class="__A__General">
          <li><span class="__A__Info"><a href="<?php echo EnergyPlus_Helpers::admin_page('products', array( 'category' => '0' ));  ?>"><?php esc_html_e('All', 'energyplus'); ?></a></span></li>
          <li><span class="__A__Info"><a href="<?php echo EnergyPlus_Helpers::admin_page('products', array( 'category' => '-1' ));  ?>"><?php esc_html_e('Critical Stocks', 'energyplus'); ?> <?php if (0 < $critical_stock) {  ?><span class="badge badge-pill badge-danger"><?php echo esc_html($critical_stock) ?></span><?php } ?></a></span></li>
          <li><span class="__A__Info"><a href="<?php echo EnergyPlus_Helpers::admin_page('products', array( 'category' => '-2' ));  ?>"><?php esc_html_e('Out of Stocks', 'energyplus'); ?> <?php if (0 < $outof_stock) {  ?><span class="badge badge-pill badge-danger"><?php echo esc_html($outof_stock) ?></span><?php } ?></a></span></li>
          <li><span class="__A__Info"><a href="<?php echo EnergyPlus_Helpers::admin_page('products', array( 'category' => '-4' ));  ?>"><?php esc_html_e('Sales', 'energyplus'); ?> <?php if (0 < $on_sales) {  ?><span class="badge badge-pill badge-danger"><?php echo esc_html($on_sales) ?></span><?php } ?></a></span></li>
          <li><span class="__A__Info"><a href="<?php echo EnergyPlus_Helpers::admin_page('products', array( 'status' => 'private' ));  ?>"><?php esc_html_e('Private', 'energyplus'); ?> <?php if (0 < wp_count_posts('product')->private) {  ?><span class="badge badge-pill badge-danger"><?php echo esc_html(wp_count_posts('product')->private) ?></span><?php } ?></a></span></li>
          <li><span class="__A__Info"><a href="<?php echo EnergyPlus_Helpers::admin_page('products', array( 'status' => 'draft' ));  ?>"><?php esc_html_e('Draft', 'energyplus'); ?> <?php if (0 < wp_count_posts('product')->draft) {  ?><span class="badge badge-pill badge-danger"><?php echo esc_html(wp_count_posts('product')->draft) ?></span><?php } ?></a></span></li>
          <li><span class="__A__Info"><a href="<?php echo EnergyPlus_Helpers::admin_page('products', array(  'status' => 'trash' ));  ?>"><?php esc_html_e('Trash', 'energyplus'); ?></a></span></li>
        </ul>

        <?php
        function categories($categories, $all, $d = 0, $parent = 0, $parents = array(), $show_me = false) {
          ?>
          <ul class="__A__Depth_<?php echo esc_attr($d); ?>" >
            <?php
            $in_parents = explode ("-", EnergyPlus_Helpers::get('parents', "0-0"));
            if ($categories) {
            foreach ($categories AS $category) {  ?>
            <li  class="__A__Parent_<?php echo esc_attr($parent) ?>  __A__Category_<?php echo esc_attr($category['id']) ?> <?php if (0 < $d) {  ?>  collapse <?php if ( $show_me OR  in_array($category['id'], $in_parents) OR $category['slug'] === EnergyPlus_Helpers::get('category')) {  $show_me = TRUE; ?> show <?php } ?>" aria-labelledby="heading2"
              data-parent=".__A__Category_<?php echo esc_attr($parent) ?>"<?php } else { ?>"<?php } ?>>
                <span class="__A__Info">
                  <span class="badge badge-pill badge-secondary"><?php echo esc_html($category['count'])?></span> &nbsp;
                  <a href="<?php echo EnergyPlus_Helpers::admin_page('products', array( 'category' => $category['slug'], 'parents'=>implode('-',$parents) ));  ?>"><?php echo esc_html($category['name']) ?></a>
                  <?php
                  array_push($parents, $category['id']);
                  if (isset($all[$category['id']])) {
                    echo ' <button class="btn btn-link" type="button" data-toggle="collapse"
                    data-target=".__A__Parent_'. $category['id'].'" aria-expanded="true" aria-controls="collapse1ü2">
                    +
                    </button>    </span>';
                    categories($all[$category['id']], $all, ++$d, $category['id'], $parents, $show_me);
                    --$d;
                    $show_me = FALSE;
                  } else {
                    echo "</span>";
                  }?>
                </li>
                <?php
              }
            }
              ?>
            </ul>
          <?php } ?>
          <?php categories($categories[0], $categories); ?>
        </div>
      </div>
    </div>
  </div>
  <?php if (!$ajax) {  ?>
  </div>
<?php } ?>
