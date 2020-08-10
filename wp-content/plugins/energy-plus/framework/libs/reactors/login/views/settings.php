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
          <?php esc_html_e('Position', 'energyplus'); ?>
        </div>
        <div class="col-lg-9 __A__Description">

          <div class="col-lg-12 input-group __A__Settings_NCT">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="position"  value="left" <?php if (EnergyPlus_Helpers::clean($settings['position'], 'center') === 'left') {echo " checked";}?>
                <label class="form-check-label" for="reactors-login-position">
                  <?php esc_html_e('Left', 'energyplus'); ?>
                </label>
              </div>

              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="position"  value="left2" <?php if (EnergyPlus_Helpers::clean($settings['position'], 'center') === 'left2') {echo " checked";}?>
                  <label class="form-check-label" for="reactors-login-position">
                    <?php esc_html_e('Left (2)', 'energyplus'); ?>
                  </label>
                </div>

              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="position" value="center" <?php if (EnergyPlus_Helpers::clean($settings['position'], 'center') === 'center') {echo " checked";}?>
                  <label class="form-check-label" for="reactors-login-position">
                    <?php esc_html_e('Center', 'energyplus'); ?>
                  </label>
                </div>

                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="position"  value="right" <?php if (EnergyPlus_Helpers::clean($settings['position'], 'center') === 'right') {echo " checked";}?>
                    <label class="form-check-label" for="reactors-login-position">
                      <?php esc_html_e('Right', 'energyplus'); ?>
                    </label>
                  </div>

                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="position"  value="right2" <?php if (EnergyPlus_Helpers::clean($settings['position'], 'center') === 'right2') {echo " checked";}?>
                      <label class="form-check-label" for="reactors-login-position">
                        <?php esc_html_e('Right (2)', 'energyplus'); ?>
                      </label>
                    </div>

                </div>
                </div>
            </div>
          </div>

          <div class="__A__Item">
            <div class="row">
              <div class="col-lg-3 __A__Title">
                <?php esc_html_e('Logo', 'energyplus'); ?>
              </div>
              <div class="col-lg-9 __A__Description">
                <div class="row">
                  <div class="custom-img-container">
                    <?php

                    $upload_link = esc_url( get_upload_iframe_src( 'image', EnergyPlus_Helpers::clean($settings['logo'], '0')) );
                    $energyplus_img_src = wp_get_attachment_image_src( EnergyPlus_Helpers::clean($settings['logo'], '0'), 'full' );
                    $valid_img = is_array( $energyplus_img_src );
                    ?>

                    <?php if ( $valid_img ) { ?>
                      <div class="__A__Settings_Logo __A__Settings_Logo_logo">
                        <a href="javascript:;" data-pr="logo" class="upload-custom-img"><img src="<?php echo esc_url($energyplus_img_src[0]) ?>"  style="max-height:120px" /></a>
                      </div>
                      <a href="javascript:;" data-pr="logo" class="remove-image upload-custom-img-text"><?php esc_html_e('Remove image', 'energyplus'); ?></a>
                    <?php } else { ?>
                      <div class="__A__Settings_Logo text-center"  id="x">
                        <a href="javascript:;" data-pr="logo" class="upload-custom-img upload-custom-img-text"><?php esc_html_e('Set image', 'energyplus'); ?></a>
                      </div>
                    <?php } ?>
                    <input class="custom-img-logo" name="logo" type="hidden"  value="<?php echo esc_attr( EnergyPlus_Helpers::clean($settings['logo'], '0') ); ?>"  />
                  </div>
                </div>
                </div>
            </div>

          </div>

          <div class="__A__Item">
            <div class="row">
              <div class="col-lg-3 __A__Title">
                <?php esc_html_e('Background Image', 'energyplus'); ?>
              </div>
              <div class="col-lg-9 __A__Description">
                <div class="row">
                  <div class="custom-img-container">
                    <?php

                    $upload_link = esc_url( get_upload_iframe_src( 'image', EnergyPlus_Helpers::clean($settings['background'], '0')) );
                    $energyplus_img_src = wp_get_attachment_image_src( EnergyPlus_Helpers::clean($settings['background'], '0'), 'full' );
                    $valid_img = is_array( $energyplus_img_src );
                    ?>

                    <?php if ( $valid_img ) { ?>
                      <div class="__A__Settings_Logo __A__Settings_Logo_background">
                        <a href="javascript:;" data-pr="background" class="upload-custom-img"><img src="<?php echo esc_url($energyplus_img_src[0]) ?>"  style="max-height:120px" /></a>
                      </div>
                      <a href="javascript:;" data-pr="background" class="remove-image upload-custom-img-text"><?php esc_html_e('Remove image', 'energyplus'); ?></a>
                    <?php } else { ?>
                      <div class="__A__Settings_Logo text-center" id="y">
                        <a href="javascript:;" data-pr="background" class="upload-custom-img upload-custom-img-text"><?php esc_html_e('Set image', 'energyplus'); ?></a>
                      </div>
                    <?php } ?>
                    <input class="custom-img-background" name="background" type="hidden" value="<?php echo esc_attr( EnergyPlus_Helpers::clean($settings['background'], '0') ); ?>" />
                  </div>
                </div>
                </div>
            </div>

          </div>

          <div class="__A__Item">
            <div class="row">
              <div class="col-lg-3 __A__Title">
                <?php esc_html_e('Colors', 'energyplus'); ?>
              </div>
              <div class="col-lg-9 __A__Description">
                <?php  EnergyPlus_Helpers::option_color(
                  array(
                    'name'=>'box',
                    'label'=> __('Box background', 'energyplus'),
                    'css'=>'',
                    'value'=>EnergyPlus_Helpers::clean($settings['box'], '#fff')
                  )
                );
                ?>

                <?php EnergyPlus_Helpers::option_color(
                  array(
                    'name'=>'text',
                    'label'=> __('Text color', 'energyplus'),
                    'css'=>'',
                    'value'=>EnergyPlus_Helpers::clean($settings['text'], '#555555')
                  )
                );
                ?>


                <?php EnergyPlus_Helpers::option_color(
                  array(
                    'name'=>'button',
                    'label'=> __('Button background', 'energyplus'),
                    'css'=>'',
                    'value'=>EnergyPlus_Helpers::clean($settings['button'], '#555555')
                  )
                );
                ?>

                <?php EnergyPlus_Helpers::option_color(
                  array(
                    'name'=>'buttontext',
                    'label'=> __('Button text color', 'energyplus'),
                    'css'=>'',
                    'value'=>EnergyPlus_Helpers::clean($settings['buttontext'], '#fff')
                  )
                );
                ?>


                </div>
            </div>

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

        var frame, selectedImg;

        jQuery(document).on('click', '.remove-image', function( event ){

          selectedImg = jQuery(this);
          jQuery('.__A__Settings_Logo_'+selectedImg.data('pr')).html('<a href="javascript:;" data-pr="' + selectedImg.data('pr') + '" class="upload-custom-img upload-custom-img-text"><?php esc_html_e('Set image', 'energyplus'); ?></a>' );
          jQuery('.custom-img-'+selectedImg.data('pr')).val('');

        });


        jQuery(document).on('click', '.upload-custom-img', function( event ){

          selectedImg = jQuery(this);

          event.preventDefault();

          if ( frame ) {
            frame.open();
            return;
          }

          frame = wp.media({
            title: 'Select or upload media',
            button: {
              text: 'Use this media'
            },
            multiple: false
          });

          frame.on( 'select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            selectedImg.parent().html( '<img src="'+attachment.url+'" alt="" style="max-height:120px" class="upload-custom-img" />' );
            jQuery('.custom-img-'+selectedImg.data('pr')).val( attachment.id );
          });

          frame.open();
        });

      })
      </script>
