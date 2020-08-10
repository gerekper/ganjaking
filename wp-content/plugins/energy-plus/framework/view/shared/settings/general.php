<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>
<?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Settings', 'woocommerce'), 'description' => '', 'buttons'=>'')); ?>
<?php echo EnergyPlus_View::run('settings/nav') ?>

<div id="energyplus-settings-general" class="energyplus-settings __A__GP">

  <?php if ( EnergyPlus_Admin::is_admin( 'administrator' ) ) { ?>
    <div class="__A__Item">
      <div class="row">
        <div class="col-lg-2 __A__Title">
          <?php esc_html_e('Logo', 'energyplus'); ?>
        </div>
        <div class="col-lg-8 __A__Description">
          <div id="energyplus-user-logo">
            <div class="custom-img-container">
              <?php

              $upload_link = esc_url( get_upload_iframe_src( 'image', $settings_logo) );
              $energyplus_img_src = wp_get_attachment_image_src( $settings_logo, 'full' );
              // Check if the array is valid
              $valid_img = is_array( $energyplus_img_src );
              ?>

              <?php if ( $valid_img ) { ?>
                <div class="__A__Settings_Logo">
                  <a href=";" class="upload-custom-img"><img src="<?php echo esc_url($energyplus_img_src[0]) ?>" /></a>
                </div>
                <a href=";" class="upload-custom-img upload-custom-img-text"><?php esc_html_e('Set your logo', 'energyplus'); ?></a>
              <?php } else { ?>
                <div class="__A__Settings_Logo text-center">
                  <a href=";" class="upload-custom-img upload-custom-img-text"><?php esc_html_e('Set your logo', 'energyplus'); ?></a>
                </div>
              <?php } ?>
              <input class="custom-img-id" name="custom-img-id" type="hidden" data-segment='settings' data-section='features' data-feature='logo' value="<?php echo esc_attr( $settings_logo ); ?>" />
            </div>
          </div>
          <br>
          <br>
          <?php esc_html_e('Sometimes we need your logo to use in the Energy+', 'energyplus'); ?>
        </div>
      </div>
    </div>

    <div class="__A__Item">
      <div class="row">
        <div class="col-lg-2 __A__Title">
          <?php esc_html_e('Full Mode', 'energyplus'); ?>
        </div>
        <div class="col-lg-8 __A__Description">
          <div class="d-flex align-items-center mt-0">
            <label class="switch">
              <input type="checkbox" value="1" class="__A__OnOff success" data-segment='settings' data-section='features' data-feature='use-administrator' <?php if ( "1" === EnergyPlus::option('feature-use-administrator', "0") ) { echo ' checked'; } ?> />
                <span class="__A__slider"></span>
              </label>
              <?php esc_html_e('Admins', 'energyplus.', 'energyplus'); ?>
              <div class="mr-5"></div>
              <label class="switch">
                <input type="checkbox" value="1" class="__A__OnOff success" data-segment='settings' data-section='features' data-feature='use-shop_manager' <?php if ( "1" === EnergyPlus::option('feature-use-shop_manager', "0")) echo ' checked' ?>/>
                  <span class="__A__slider"></span>
                </label>
                <?php esc_html_e('Shop Managers', 'energyplus.', 'energyplus'); ?>
              </div>
              <br>
              <?php esc_html_e('If you do not enable full mode, they can use both Energy+ and classic WP Admin', 'energyplus'); ?>

            </div>
          </div>
        </div>

        <div class="__A__Item __A__Item_ForceToUse<?php if ("1" === EnergyPlus::option('feature-use-shop_manager', "0") && "1" === EnergyPlus::option('feature-use-administrator', "0")) { echo ' __A__Display_None';} ?>">
          <div class="row">
            <div class="col-lg-2 __A__Title">
              <?php esc_html_e('Auto start', 'energyplus'); ?>
            </div>
            <div class="col-lg-8 __A__Description">
              <label class="switch">
                <input type="checkbox" value="1" class="__A__OnOff success" data-segment='settings' data-section='features' data-feature='auto' <?php if ( "1" === EnergyPlus::option('feature-auto', "0")) echo ' checked' ?>/>
                  <span class="__A__slider"></span>
                </label>
                <br>
                <br>
                <?php esc_html_e('Starts Energy+ automatically when full mode is disabled', 'energyplus'); ?>
              </div>
            </div>
          </div>

        <?php } ?>

        <?php if ( EnergyPlus_Admin::is_admin( 'administrator' ) OR "1" === EnergyPlus::option('feature-own_themes') ) { ?>

          <div class="__A__Item">
            <div class="row">
              <div class="col-lg-2 __A__Title">
                <?php esc_html_e('Themes', 'energyplus'); ?>
              </div>
              <div class="col-lg-8 __A__Description">

                <?php foreach ($themes['list'] AS $theme) { ?>
                  <?php  $selected = $themes['selected'] === $theme ? "checked" : ""; ?>
                  <div class="radio">
                    <input data-theme="<?php echo esc_attr($theme);?>" id="<?php echo esc_attr($theme);?>" class="energyplus-settigns-general--themes-change radio-button" type="radio" name="radio" <?php echo esc_attr($selected) ?> />
                    <label for="<?php echo esc_attr($theme);?>" class="radio-tile-label text-uppercase"><?php echo esc_html($theme);?></label><br />
                  </div>
                <?php } ?>

                <div class="mt-3">
                  <?php esc_html_e('Select your theme for Energy+ Admin Panel. This will not affect your website theme.', 'energyplus'); ?>
                </div>

                <?php if ( EnergyPlus_Admin::is_admin( 'administrator' ) ) { ?>
                  <div class="d-flex align-items-center mt-4">
                    <label class="switch">
                      <input type="checkbox" value="1" class="__A__OnOff success" data-segment='settings' data-section='features' data-feature='own_themes' <?php if ( "1" === EnergyPlus::option('feature-own_themes', "0")) echo ' checked' ?>/>
                        <span class="__A__slider"></span>
                      </label>
                      <?php esc_html_e('Allow other shop managers to choose their own themes.', 'energyplus'); ?>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>



            <div class="__A__Item">
              <div class="row">
                <div class="col-lg-2 __A__Title">
                  <?php esc_html_e('Colors', 'energyplus'); ?>
                </div>
                <div class="col-lg-8 __A__Description">

                  <div class="__A__Settings_Colors">
                    <?php foreach ($colors AS $color_key => $color_value) {
                      if ('custom' !== $color_key) { ?>
                        <a href="javascript:;" class="__A__Settings_Color<?php if ($colors_selected['key'] === $color_key) {echo " __A__Settings_Color_Selected";}?>" style="background: #<?php echo esc_attr($color_key)?>" data-colors='<?php echo esc_attr(json_encode($color_value)); ?>'></a>
                        <?php }
                      }?>
                      <a href="javascript:;" class="__A__Settings_Color __A__Settings_Color_Own<?php if ($colors_selected['key'] === 'custom') {echo " __A__Settings_Color_Selected";}?>" data-colors='<?php echo esc_attr(json_encode($colors['custom'])); ?>'><span class="dashicons dashicons-admin-customizer"></span></a>
                    </div>

                    <div class="__A__Settings_Color_Own_Div d-none">

                      <?php
                      $color_labels = array(
                        'header-background' => __('Menu', 'energyplus'),
                        'header-icons'      => __('Icons', 'energyplus'),
                        'header-hover'      => __('Menu Hover', 'energyplus'),
                        'primary-buttons'   => __('Buttons', 'energyplus'),
                      );
                      ?>
                      <?php foreach ($colors['custom'] AS $color_key => $color_value) {
                        if (isset($color_labels[$color_key])) { ?>
                          <div class="__A__Settings_Color_Own_Options">
                            <?php echo esc_html($color_labels[$color_key]); ?>
                            <br>
                            <input type="text" value="<?php echo esc_attr($color_value)?>" class="__A__Settings_Color_Own-<?php echo esc_attr($color_key)?> energyplus-color-field" data-default-color="<?php echo esc_attr($color_value)?>" />
                          </div>
                        <?php }
                      } ?>
                      <button class="__A__Settings_Color_Own_Save __A__Button1 badge-black"><?php esc_html_e('Save custom colors', 'energyplus'); ?></button>
                    </div>
                  </div>
                </div>
              </div>

            <?php } ?>

            <?php if ( EnergyPlus_Admin::is_admin( 'administrator' ) ) { ?>
              <div class="__A__Item">
                <div class="row">
                  <div class="col-lg-2 __A__Title">
                    <?php esc_html_e('Goals', 'energyplus'); ?>
                  </div>
                  <div class="col-lg-8 __A__Description">
                    <div class="row">

                      <div class="col-12 col-md-3 mb-3">
                        <label class="text-uppercase"><?php esc_html_e('Daily', 'energyplus'); ?></label>

                        <div class="input-group">
                          <div class="input-group-prepend">
                            <div class="input-group-text"><?php echo esc_html(get_woocommerce_currency_symbol())?></div>
                          </div>
                          <input class="__A__Settings_Input form-control" data-segment='settings' data-section='features' data-feature='goals-daily'  placeholder="<?php esc_attr_e('N/A', 'energyplus'); ?>" value='<?php echo esc_attr(EnergyPlus::option('feature-goals-daily', '')) ?>'/>
                        </div>
                      </div>

                      <div class="col-12 col-md-3 mb-3">
                        <label class="text-uppercase"><?php esc_html_e('Weekly', 'energyplus'); ?></label>

                        <div class="input-group">
                          <div class="input-group-prepend">
                            <div class="input-group-text"><?php echo esc_html(get_woocommerce_currency_symbol())?></div>
                          </div>
                          <input class="__A__Settings_Input form-control" data-segment='settings' data-section='features' data-feature='goals-weekly'  placeholder="<?php esc_attr_e('N/A', 'energyplus'); ?>" value='<?php echo esc_attr(EnergyPlus::option('feature-goals-weekly', '')) ?>'/>
                        </div>
                      </div>

                      <div class="col-12 col-md-3 mb-3">
                        <label class="text-uppercase"><?php esc_html_e('Monthly', 'energyplus'); ?></label>

                        <div class="input-group">
                          <div class="input-group-prepend">
                            <div class="input-group-text"><?php echo esc_html(get_woocommerce_currency_symbol())?></div>
                          </div>
                          <input class="__A__Settings_Input form-control" data-segment='settings' data-section='features' data-feature='goals-monthly'  placeholder="<?php esc_attr_e('N/A', 'energyplus'); ?>" value='<?php echo esc_attr(EnergyPlus::option('feature-goals-monthly', '')) ?>'/>
                        </div>
                      </div>

                      <div class="col-12 col-md-3 mb-3">
                        <label class="text-uppercase"><?php esc_html_e('Yearly', 'energyplus'); ?></label>

                        <div class="input-group">
                          <div class="input-group-prepend">
                            <div class="input-group-text"><?php echo esc_html(get_woocommerce_currency_symbol())?></div>
                          </div>
                          <input class="__A__Settings_Input form-control" data-segment='settings' data-section='features' data-feature='goals-yearly'  placeholder="<?php esc_attr_e('N/A', 'energyplus'); ?>" value='<?php echo esc_attr(EnergyPlus::option('feature-goals-yearly', '')) ?>'/>
                        </div>
                      </div>
                      <div class="col-12">
                        <?php esc_html_e('Set your goals. Changes do not affect past statistics.', 'energyplus'); ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>

            <?php if ( EnergyPlus_Admin::is_admin( 'administrator' ) ) { ?>
              <div class="__A__Item">
                <div class="row">
                  <div class="col-lg-2 __A__Title">
                    <?php esc_html_e('Notification Check Time', 'energyplus'); ?>
                  </div>
                  <div class="col-lg-8 __A__Description">

                    <div class="col-lg-4 input-group __A__Settings_NCT">

                      <input class="__A__Settings_Input form-control" data-segment='settings' data-section='features' data-feature='refresh'  placeholder="<?php esc_attr_e('N/A', 'energyplus'); ?>" value='<?php echo esc_attr(EnergyPlus::option('feature-refresh', '10')) ?>'/>
                      <div class="input-group-append">
                        <div class="input-group-text"><?php esc_html_e('seconds', 'energyplus'); ?></div>
                      </div>
                    </div>

                    <br>
                    <?php esc_html_e('Enter the number of seconds to check the notifications. If you enter a number less than 10, widgets and notifications are only checked when navigating through the panel.', 'energyplus'); ?>

                  </div>
                </div>
              </div>
            <?php } ?>

            <?php if ( EnergyPlus_Admin::is_admin( 'administrator' ) ) { ?>
              <div class="__A__Item">
                <div class="row">
                  <div class="col-lg-2 __A__Title">
                    <?php esc_html_e('Title & Badge Count', 'energyplus'); ?>
                  </div>
                  <div class="col-lg-8 __A__Description">

                    <div class="col-lg-4 __A__Settings_NCT">
                      <div class="radio d-block">
                        <input class="__A__Options_Badge form-control" data-segment='settings' data-section='features' data-feature='badge' value="0" type="radio" name="energyplus-settings-badge" <?php if ('0' === EnergyPlus::option('feature-badge', '0')) { echo ' checked'; } ?> />
                          <label for="1" class="radio-tile-label"><?php esc_html_e("Notifications", 'energyplus'); ?></label>
                        </div>

                        <div class="radio  d-block">
                          <input class="__A__Options_Badge form-control" data-segment='settings' data-section='features' data-feature='badge' value="1" type="radio" name="energyplus-settings-badge" <?php if ('1' === EnergyPlus::option('feature-badge', '0')) { echo ' checked'; } ?> />
                            <label for="2" class="radio-tile-label"><?php esc_html_e("Online users", 'energyplus'); ?></label>
                          </div>

                          <div class="radio  d-block">
                            <input class="__A__Options_Badge form-control" data-segment='settings' data-section='features' data-feature='badge' value="3"  type="radio" name="energyplus-settings-badge" <?php if ('3' === EnergyPlus::option('feature-badge', '0')) { echo ' checked'; } ?> />
                              <label for="3" class="radio-tile-label"><?php esc_html_e("Today's visitors", 'energyplus'); ?></label>
                            </div>

                            <div class="radio  d-block">
                              <input class="__A__Options_Badge form-control" data-segment='settings' data-section='features' data-feature='badge' value="4"  type="radio" name="energyplus-settings-badge" <?php if ('4' === EnergyPlus::option('feature-badge', '0')) { echo ' checked'; } ?> />
                                <label for="4" class="radio-tile-label"><?php esc_html_e("Today's sales", 'energyplus'); ?></label>
                              </div>
                            </div>

                            <br>
                            <?php echo sprintf(esc_html__('The number in your browser title & desktop app badge (Mac only). For example; (3) %s', 'energyplus'),get_bloginfo('name')); ?>

                          </div>
                        </div>
                      </div>
                    <?php } ?>

                    <?php if ( EnergyPlus_Admin::is_admin( 'administrator' ) ) { ?>
                      <div class="__A__Item">
                        <div class="row">
                          <div class="col-lg-2 __A__Title">
                            <?php esc_html_e('Tracker', 'energyplus'); ?>
                          </div>
                          <div class="col-lg-8 __A__Description">
                            <label class="switch">
                              <input type="checkbox" value="1" class="__A__OnOff success" data-segment='settings' data-section='features' data-feature='pulse' <?php if ( "1" === EnergyPlus::option('feature-pulse', "0")) echo ' checked' ?>/>
                                <span class="__A__slider"></span>
                              </label>
                              <br>
                              <br>
                              <?php esc_html_e('Track users for stats like online users, product views etc. If you have performance issues, you should disable it.', 'energyplus'); ?>
                            </div>
                          </div>
                        </div>
                      <?php } ?>
                    </div>
