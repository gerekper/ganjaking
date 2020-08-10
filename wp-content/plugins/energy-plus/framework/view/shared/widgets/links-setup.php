<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-in'); ?>

<div class="energyplus-title inbrowser">
  <h3><?php esc_html_e('Shortcuts', 'energyplus'); ?></h3>
</div>

<div id="energyplus-settings-general" class="energyplus-settings __A__Reactors_Settings">
  <?php if (1 === $saved) { ?>
    <div class="alert alert-success mt-3" role="alert">
      <span class="dashicons dashicons-smiley"></span>&nbsp;&nbsp;<?php esc_html_e('Settings have been saved', 'energyplus'); ?>
    </div>
  <?php } ?>
  <form action="" method="POST">

    <div class="__A__Item">
      <div class="row">
        <div class="col-lg-2 __A__Title">
          <?php esc_html_e('Style', 'energyplus'); ?>
        </div>
        <div class="col-lg-10 __A__Description">
          <div class="row pl-4">
            <select name="style" class="__A__Settings_Select form-control ml-3">
              <option value="3" <?php if (3 === intval(EnergyPlus::option('widgets-links-style-'.$id, 1))) echo " selected"?>><?php esc_html_e('Text', 'energyplus'); ?></option>
                <option value="2" <?php if (2 === intval(EnergyPlus::option('widgets-links-style-'.$id, 1))) echo " selected"?>><?php esc_html_e('Icon', 'energyplus'); ?></option>
                  <option value="1" <?php if (1 === intval(EnergyPlus::option('widgets-links-style-'.$id, 1))) echo " selected"?>><?php esc_html_e('Icon + Text', 'energyplus'); ?></option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="__A__Item">
            <div class="row">
              <div class="col-lg-2 __A__Title">
                <?php esc_html_e('Items per line', 'energyplus'); ?>
              </div>
              <div class="col-lg-10 __A__Description">
                <div class="col-sm-2 input-group pl-4">
                  <input name="per-line" type="number" step="1" min="1" max="20" class="__A__Settings_Input form-control"  placeholder="2" value='<?php echo esc_attr(intval(EnergyPlus::option('widgets-links-items-'.$id, '2'))) ?>'/>
                </div>
              </div>
            </div>
          </div>

          <div class="__A__Item">
            <div class="row">
              <div class="col-lg-2 __A__Title">
                <?php esc_html_e('Menu items', 'energyplus'); ?>
              </div>
              <div class="col-lg-10 __A__Description">
                <div class="__A__List_M1 __A__Container __A__Widget_Links_Setup<?php if (1 === count($items)) echo ' d-none';?>">
                  <ol class="__A__Tweaks_Sortable">

                    <?php foreach ($items AS $item) { ?>
                      <li  id="item_li_<?php echo esc_attr($item['id'])?>">
                        <div class="btnA __A__Item collapsed item_<?php echo esc_attr($item['id'])?>" id="item_<?php echo esc_attr($item['id'])?>" data-toggle="collapse" data-target="#item_d_<?php echo esc_attr($item['id'])?>" aria-expanded="false" aria-controls="item_d_<?php echo esc_attr($item['id'])?>">
                          <div class="liste  row d-flex align-items-center">
                            <div class="col-6 col-sm-8 __A__Item_Title"><?php echo esc_html($item['title'])?></div>
                            <div class="col-1 col-sm-2 text-left">
                              <a href="javascript:;" class="__A__Delete d-none text-danger"><?php esc_html_e('Delete this', 'energyplus'); ?></a>
                            </div>
                            <div class="col-1 col-sm-1 text-right pr-3">
                              <label class="switch __A__StopPropagation">
                                <input name="active_<?php if (isset($item['new'])) echo 'tmpID'; else echo esc_attr($item['id'])?>" type="checkbox" value="1" class="success __A__OnOff" <?php if (1 === intval($item['active'])) echo ' checked'; ?> />
                                <span class="__A__slider round"></span>
                              </label>
                            </div>
                            <div class="col-7 col-sm-1 text-center pr-3"><a href="javascript:;" class="text-muted ui-sortable-handle __A__StopPropagation" title="Move"><span>≡</span></a></div>
                          </div>
                          <div class="collapse collap-me col-xs-12 col-sm-12 col-md-12 text-right" id="item_d_<?php echo esc_attr($item['id'])?>">
                            <div class="__A__Item_Details __A__StopPropagation">
                              <div class="row">

                                <div class="col-sm-7 text-left">
                                  <input name="tmp_id[]" value="<?php if (isset($item['new'])) echo 'tmpID'; else echo esc_attr($item['id'])?>" type="hidden">
                                  <div class="form-group row">
                                    <label for="title_tmpID" class="col-sm-2 col-form-label"><?php esc_html_e('Title', 'energyplus'); ?></label>
                                    <div class="col-sm-10">
                                      <input type="text" class="form-control  form-control-sm" name="title_<?php if (isset($item['new'])) echo 'tmpID'; else echo esc_attr($item['id'])?>" value="<?php echo esc_attr($item['title'])?>">
                                    </div>
                                  </div>

                                  <div class="form-group row">
                                    <label for="url_tmpID" class="col-sm-2 col-form-label"><?php esc_html_e('URL', 'energyplus'); ?></label>
                                    <div class="col-sm-10">
                                      <input type="text" class="form-control  form-control-sm"  name="url_<?php if (isset($item['new'])) echo 'tmpID'; else echo esc_attr($item['id'])?>" value="<?php echo esc_attr($item['url'])?>">
                                    </div>
                                  </div>

                                  <div class="form-group row">
                                    <label for="inputPassword" class="col-sm-2 col-form-label"><?php esc_html_e('Open', 'energyplus'); ?></label>
                                    <div class="col-sm-10">
                                      <select name="open_<?php if (isset($item['new'])) echo 'tmpID'; else echo esc_attr($item['id'])?>">
                                        <option value="1" <?php if (1 === intval($item['open'])) echo " selected"?>><?php esc_html_e('in right panel', 'energyplus'); ?></option>
                                        <option value="2" <?php if (2 === intval($item['open'])) echo " selected"?>><?php esc_html_e('in blank tab', 'energyplus'); ?></option>
                                        <option value="3" <?php if (3 === intval($item['open'])) echo " selected"?>><?php esc_html_e('in same page', 'energyplus'); ?></option>
                                      </select>
                                    </div>
                                  </div>

                                  <div class="form-group row">
                                    <label for="icon_tmpID" class="col-sm-2 col-form-label"><?php esc_html_e('Icon', 'energyplus'); ?></label>
                                    <div class="col-sm-10">
                                      <button name="icon_<?php if (isset($item['new'])) echo 'tmpID'; else echo esc_attr($item['id'])?>" data-icon="<?php echo esc_attr($item['icon'])?>" class="__A__Settings_Change_Icon1 __A__StopPropagation" data-iconset="fontawesome5"><?php esc_html_e('Change icon', 'energyplus'); ?></button>
                                    </div>
                                  </div>

                                  <div class="form-group row">
                                    <label for="users_tmpID" class="col-sm-2 col-form-label"><?php esc_html_e('Users', 'energyplus'); ?></label>
                                    <div class="col-sm-10">
                                      <?php
                                      $args = array(
                                        'role'    => 'administrator',
                                        'orderby' => 'user_nicename',
                                        'order'   => 'ASC'
                                      );
                                      $users = get_users( $args );

                                      $args = array(
                                        'role'    => 'shop_manager',
                                        'orderby' => 'user_nicename',
                                        'order'   => 'ASC'
                                      );
                                      $users = array_merge($users, (array)get_users( $args ));

                                      ?>

                                      <?php foreach ($users AS $user) { ?>
                                        <input type="checkbox" class="form-control form-control-sm pm-3" name="users_<?php if (isset($item['new'])) echo 'tmpID'; else echo esc_attr($item['id'])?>[]" value="<?php echo esc_attr($user->ID)?>" <?php if (0 === count($item['users']) || in_array($user->ID, array_values($item['users']))) echo " checked";?>><?php echo esc_html($user->display_name);?> <br>
                                      <?php } ?>

                                    </div>
                                  </div>


                                </div>

                                <div class="col-sm-1 text-left"></div>
                                <div class="col-sm-4 text-left">
                                  <?php  EnergyPlus_Helpers::option_color(
                                    array(
                                      'name'=>'background_color_' . ((isset($item['new'])) ? 'tmpID' : esc_attr($item['id'])),
                                      'label'=> __('Background Color', 'energyplus'),
                                      'css'=>'',
                                      'value'=>EnergyPlus_Helpers::clean($item['background_color'], '#ffffff'),
                                      'no-js'=> isset($item['new']) ? true : false
                                    )
                                  );
                                  ?>

                                  <?php  EnergyPlus_Helpers::option_color(
                                    array(
                                      'name'=>'text_color_' . ((isset($item['new'])) ? 'tmpID' : esc_attr($item['id'])),
                                      'label'=> __('Text Color', 'energyplus'),
                                      'css'=>'',
                                      'value'=>EnergyPlus_Helpers::clean($item['text_color'], '#353535'),
                                      'no-js'=>isset($item['new']) ? true : false
                                    )
                                  );
                                  ?>

                                </div>
                              </div>
                            </div>
                          </div>

                        </div>
                      </li>

                    <?php } ?>

                  </div>

                  <a href="javascript:;" class="__A__New pl-4"><?php esc_html_e('Add new menu item', 'energyplus'); ?></a>
                </div>
              </div>
            </div>

            <div class="mt-4 text-center mb-5">
              <?php wp_nonce_field( 'energyplus_reactors' ); ?>
              <button name="submit" class="btn btn-sm __A__Button1" disabled type="submit"><?php esc_html_e('Save', 'energyplus'); ?></button>
            </div>

          </form>
          <script>
          jQuery(document).ready(function() {
            "use strict";

            jQuery('.__A__Button1').attr('disabled', false);

            jQuery('.__A__Settings_Change_Icon1').iconpicker();

            jQuery( ".__A__Tweaks_Sortable" ).sortable( {
              axis: "y",
              revert: true,
              scroll: false,
              placeholder: "sortable-placeholder",
              cursor: "move",
              opacity: 1
            });

            jQuery('.__A__Delete').on('click', function() {
              jQuery(this).closest('.btnA').collapse('hide').remove();
            });

            jQuery('.__A__New').on('click', function() {
              jQuery('.__A__Widget_Links_Setup').removeClass('d-none');
              jQuery('.__A__Tweaks_Sortable').find('.btnA').addClass('collapsed');
              jQuery('.__A__Tweaks_Sortable').find('.collap-me').addClass('collapse').removeClass('show');
              var tmpID = (new Date()).getTime();
              var tmp = jQuery('#item_li_new').clone()[0].outerHTML;
              tmp = tmp.replace(/tmpID/g, tmpID);
              jQuery('.__A__Tweaks_Sortable').append(tmp.replace(/new/g, tmpID));
              jQuery('.__A__Settings_Change_Icon1').iconpicker();
              jQuery(".energyplus-color-field-"+tmpID).wpColorPicker({ width:200 });
              jQuery('#item_'+tmpID).removeClass('collapsed');
              jQuery('#item_'+tmpID).find('.collapse').removeClass('collapse');
              jQuery('input[name="title_'+tmpID+'"]').focus().select();
              jQuery('.__A__Delete').on('click', function() {
                jQuery(this).closest('.btnA').collapse('hide').remove();
              });
            })
          })
          </script>
