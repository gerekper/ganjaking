<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>

<?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html($reactor['title']), 'description' => '', 'buttons'=>'')); ?>

<?php echo EnergyPlus_View::reactor('tweaks/views/nav', array('id'=> $reactor['id']) ) ?>

<div id="energyplus-settings-general" class="energyplus-settings __A__Reactors_Settings __A__GP">
  <?php if (1 === $saved) { ?>
    <div class="alert alert-success" role="alert">
      <span class="dashicons dashicons-smiley"></span>&nbsp;&nbsp;<?php esc_html_e('Settings are saved', 'energyplus'); ?>
    </div>
  <?php } ?>
  <form action="" method="POST">

    <div class="__A__Item">
      <div class="row">
        <div class="col-lg-3 __A__Title">
          <?php esc_html_e('Detail window width', 'energyplus'); ?>
        </div>
        <div class="col-lg-9 __A__Description">
          <div class="col-lg-3 input-group __A__Settings_NCT">
            <input name="reactors-tweaks-window-size" class="__A__Settings_Input form-control"  placeholder="<?php esc_attr_e('1090', 'energyplus'); ?>" value='<?php echo esc_attr(intval(EnergyPlus::option('reactors-tweaks-window-size', '1090px'))) ?>'/>
            <select name="reactors-tweaks-window-size-dimension" class="__A__Settings_Select form-control">
              <option<?php if (stripos(EnergyPlus::option('reactors-tweaks-window-size', '1090px'), 'px') > 0) {echo " selected";}?>>px</option>
                <option<?php if (stripos(EnergyPlus::option('reactors-tweaks-window-size', '1090px'), '%') > 0) {echo " selected";}?>>%</option></select>
                </div>
                <br>
                <?php esc_html_e('Adjust the width of the detail window that opens from the right. It can be px or % value. (Example: 1090px or 90%)', 'energyplus'); ?>
              </div>
            </div>
          </div>

          <div class="__A__Item">
            <div class="row">
              <div class="col-lg-3 __A__Title">
                <?php esc_html_e('Order statuses', 'energyplus'); ?>
              </div>
              <div class="col-lg-9 __A__Description">

                <div class="row">

                  <div class="nav flex-column nav-pills __A__Tweaks_SOS" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <?php foreach ( wc_get_order_statuses() AS $key=>$value) {
                      if (!isset($status_active)) { $status_active = " active"; } else {$status_active = "";} ?>
                      <a class="nav-link<?php echo esc_attr($status_active)?>" id="v-pills-<?php echo esc_attr($key)?>-tab" data-toggle="pill" href="#v-pills-<?php echo esc_attr($key)?>" role="tab" aria-controls="v-pills-<?php echo esc_attr($key)?>" aria-selected="true"><?php echo esc_html($value)?></a>
                    <?php }  ?>
                  </div>

                  <div class="tab-content w-75 __A__Tweaks_SOS" id="v-pills-tabContent">
                    <?php

                    $wc_statuses = array_keys(wc_get_order_statuses());
                    $wc_statuses_all = wc_get_order_statuses();
                    $wc_statuses['trash'] = 'trash';
                    $wc_statuses_all['trash'] = __('Delete', 'energyplus');

                    $status_details = EnergyPlus::option('reactors-tweaks-order-cond', array());

                    foreach ( wc_get_order_statuses() AS $key=>$value) {

                      if (isset($status_details[$key])) {
                        $status_detail = $status_details[$key];
                      } else {
                        $status_detail =  array_keys(wc_get_order_statuses());
                      }

                      if (!isset($status_active2)) { $status_active2 = " active"; } else {$status_active2 = "";}

                      ?>
                      <div class="tab-pane fade show<?php echo esc_attr($status_active2)?>" id="v-pills-<?php echo esc_attr($key)?>" role="tabpanel" aria-labelledby="v-pills-<?php echo esc_attr($key)?>-tab">
                        <ol class="__A__Tweaks_Sortable">
                          <?php foreach ( $status_detail AS $key1) {
                            if ('-' === $key1) continue; ?>
                            <li class="__A__Tweaks_OS __A__Settings_NCT row">
                              <div class="form-check">
                                <a href="javascript:;" class="text-muted" title="<?php esc_attr_e('Move', 'energyplus')?>"><span>≡</span></a>
                                &nbsp; <input type="checkbox" value="<?php echo esc_attr($key1)?>" name="reactors-tweaks-order-cond[<?php echo esc_attr($key)?>][]" <?php if (in_array($key1, $status_detail)) { echo " checked";}?>>
                                <?php echo esc_html($wc_statuses_all[$key1]) ?>
                              </div>
                            </li>
                          <?php } ?>

                          <?php
                          if (is_array($wc_statuses) && is_array($status_detail)) {
                            $other_status = array_diff($wc_statuses, $status_detail);
                          } else {
                            $other_status = array();
                          }
                          foreach ( $other_status AS $key1) {  ?>
                            <li class="__A__Tweaks_OS __A__Settings_NCT row">
                              <div class="form-check">
                                <a href="javascript:;" class="text-muted" title="<?php esc_attr_e('Move', 'energyplus')?>"><span>≡</span></a>
                                &nbsp; <input type="checkbox" value="<?php echo esc_attr($key1)?>" name="reactors-tweaks-order-cond[<?php echo esc_attr($key)?>][]" <?php if (in_array($key1, $status_detail)) { echo " checked";}?>>
                                <?php echo esc_html($wc_statuses_all[$key1]) ?>
                              </div>
                            </li>
                          <?php } ?>
                          <input type="checkbox" class="__A__Tweaks_OS-hidden" value="-" name="reactors-tweaks-order-cond[<?php echo esc_attr($key)?>][-]" checked>
                        </ol>
                      </div>
                    <?php } ?>
                  </div>
                </div>
                <br>
                <?php esc_html_e('Select which statuses will be shown when an order is clicked. You can show/hide or sort them.', 'energyplus'); ?>
              </div>
            </div>
          </div>

          <div class="__A__Item">
            <div class="row">
              <div class="col-lg-3 __A__Title">
                <?php esc_html_e('Homepage', 'energyplus'); ?>
              </div>
              <div class="col-lg-9 __A__Description">
                <div class="row">
                  <div class="col-sm-12 __A__Settings_NCT">
                    <div class="form-check">

                      <select name="reactors-tweaks-landing" class="__A__Settings_Select form-control">
                        <option<?php if (EnergyPlus::option('reactors-tweaks-landing', 'dashboard') === 'dashboard') {echo " selected";}?>><?php esc_html_e('Dashboard', 'energyplus'); ?></option>
                          <option<?php if (EnergyPlus::option('reactors-tweaks-landing', 'dashboard') === 'orders') {echo " selected";}?>><?php esc_html_e('Orders', 'energyplus'); ?></option>
                            <option<?php if (EnergyPlus::option('reactors-tweaks-landing', 'dashboard') === 'products') {echo " selected";}?>><?php esc_html_e('Products', 'energyplus'); ?></option>
                              <option<?php if (EnergyPlus::option('reactors-tweaks-landing', 'dashboard') === 'reports') {echo " selected";}?>><?php esc_html_e('Reports', 'energyplus'); ?></option>
                                <option<?php if (EnergyPlus::option('reactors-tweaks-landing', 'dashboard') === 'customers') {echo " selected";}?>><?php esc_html_e('Customers', 'energyplus'); ?></option>
                                  <option<?php if (EnergyPlus::option('reactors-tweaks-landing', 'dashboard') === 'coupons') {echo " selected";}?>><?php esc_html_e('Coupons', 'energyplus'); ?></option>
                                  </select>
                                  <br>
                                  <?php esc_html_e('What will be the landing page', 'energyplus'); ?>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="__A__Item">
                        <div class="row">
                          <div class="col-lg-3 __A__Title">
                            <?php esc_html_e('Items', 'energyplus'); ?>
                          </div>
                          <div class="col-lg-7 __A__Description">
                            <div class="row">
                              <div class="col-sm-2">
                                <input type="number" step="1"  min="1" name="reactors-tweaks-pg-orders" class="form-control"  placeholder="<?php esc_attr_e('10', 'energyplus'); ?>" value='<?php echo esc_attr(intval(EnergyPlus::option('reactors-tweaks-pg-orders', '10'))) ?>'/><br> <?php esc_html_e('Orders', 'energyplus'); ?>
                              </div>

                              <div class="col-sm-2">
                                <input type="number" step="1"  min="1" name="reactors-tweaks-pg-products" class="form-control"  placeholder="<?php esc_attr_e('10', 'energyplus'); ?>" value='<?php echo esc_attr(intval(EnergyPlus::option('reactors-tweaks-pg-products', '10'))) ?>'/><br> <?php esc_html_e('Products', 'energyplus'); ?>
                              </div>

                              <div class="col-sm-2">
                                <input type="number" step="1"  min="1" name="reactors-tweaks-pg-coupons" class="form-control"  placeholder="<?php esc_attr_e('10', 'energyplus'); ?>" value='<?php echo esc_attr(intval(EnergyPlus::option('reactors-tweaks-pg-coupons', '10'))) ?>'/><br> <?php esc_html_e('Coupons', 'energyplus'); ?>
                              </div>

                              <div class="col-sm-2">
                                <input type="number" step="1"  min="1" name="reactors-tweaks-pg-customers" class="form-control"  placeholder="<?php esc_attr_e('10', 'energyplus'); ?>" value='<?php echo esc_attr(intval(EnergyPlus::option('reactors-tweaks-pg-customers', '10'))) ?>'/><br> <?php esc_html_e('Customers', 'energyplus'); ?>
                              </div>

                              <div class="col-sm-2">
                                <input type="number" step="1" min="1" name="reactors-tweaks-pg-comments" class="form-control"  placeholder="<?php esc_attr_e('10', 'energyplus'); ?>" value='<?php echo esc_attr(intval(EnergyPlus::option('reactors-tweaks-pg-comments', '10'))) ?>'/><br> <?php esc_html_e('Comments', 'energyplus'); ?>
                              </div>

                            </div>
                            <br>

                            <br>

                            <?php esc_html_e('How many items will be shown per page', 'energyplus'); ?>
                          </div>
                        </div>
                      </div>

                      <div class="__A__Item">
                        <div class="row">
                          <div class="col-lg-3 __A__Title">
                            <?php esc_html_e('Appearance', 'energyplus'); ?>
                          </div>
                          <div class="col-lg-9 __A__Description">
                            <div class="row">
                              <div class="col-sm-12 __A__Settings_NCT">

                                <div class="form-check">
                                  <input type="checkbox" value="1" name="reactors-tweaks-settings-woocommerce" <?php if ("1" === EnergyPlus::option('reactors-tweaks-settings-woocommerce', "0")) { echo " checked"; } ?>>
                                    <?php esc_html_e('Show "WooCommerce Settings" tab in Settings for Shop Managers', 'energyplus'); ?>
                                  </div>

                                  <?php if (in_array(EnergyPlus::$theme, array('one', 'one-shadow'))) {?>
                                    <div class="form-check">
                                      <input type="checkbox" value="1" name="reactors-tweaks-icon-text" <?php if ("1" === EnergyPlus::option('reactors-tweaks-icon-text', "0")) { echo " checked"; } ?>>
                                        <?php esc_html_e('Show menu item titles at bottom of icons', 'energyplus'); ?>
                                      </div>
                                    <?php } ?>

                                    <div class="form-check">
                                      <input type="checkbox" value="1" name="reactors-tweaks-screenoptions" <?php if ("1" === EnergyPlus::option('reactors-tweaks-screenoptions', "0")) { echo " checked"; } ?>>
                                        <?php esc_html_e('Show Screen Options for non-Energy pages', 'energyplus'); ?>
                                      </div>

                                      <div class="form-check">
                                        <input type="checkbox" value="1" name="reactors-tweaks-adminbar-hotkey" <?php if ("1" === EnergyPlus::option('reactors-tweaks-adminbar-hotkey', "1")) { echo " checked"; } ?>>
                                          <?php esc_html_e('Show WP Adminbar when press A key from keyboard', 'energyplus'); ?>
                                        </div>

                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>


                              <div class="__A__Item">
                                <div class="row">
                                  <div class="col-lg-3 __A__Title">
                                    <?php esc_html_e('Font', 'energyplus'); ?>
                                  </div>
                                  <div class="col-lg-9 __A__Description">
                                    <div class="col-lg-8 input-group __A__Settings_NCT">
                                      <input id="reactors-tweaks-font" name="reactors-tweaks-font" class="__A__Settings_Input form-control"  placeholder="<?php esc_attr_e('Font', 'energyplus'); ?>" value='<?php echo esc_attr(EnergyPlus::option('reactors-tweaks-font', 'Theme+Default')) ?>'/>
                                    </div>
                                    <br>
                                    <?php esc_html_e('Change your Energy+ font.', 'energyplus'); ?>
                                    <a href="//fonts.google.com/" target="_blank"><?php esc_html_e('Go to Google Fonts >', 'energyplus'); ?></a>
                                  </div>
                                </div>
                                <script>
                                jQuery(document).ready(function() {
                                  "use strict";

                                  jQuery('#reactors-tweaks-font')
                                  .fontselect({
                                    systemFonts: ['Theme+Default']
                                  });
                                });
                                </script>
                              </div>


                              <div class="mt-4 text-center">
                                <?php wp_nonce_field( 'energyplus_reactors' ); ?>
                                <button name="submit" class="btn btn-sm __A__Button1" type="submit"><?php esc_html_e('Save', 'energyplus'); ?></button>
                              </div>
                            </form>
                          </div>

                          <script>
                          jQuery(document).ready(function() {
                            "use strict";

                            jQuery( ".__A__Tweaks_Sortable" ).sortable( {
                              axis: "y",
                              revert: true,
                              scroll: false,
                              placeholder: "sortable-placeholder",
                              cursor: "move",
                              opacity: 1
                            });

                          })
                          </script>
