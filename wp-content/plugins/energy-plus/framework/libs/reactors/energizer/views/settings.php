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
      <span class="dashicons dashicons-smiley"></span>&nbsp;&nbsp;<?php esc_html_e('Settings have been saved', 'energyplus'); ?>
    </div>
  <?php } ?>
  <form action="" method="POST">

    <div class="__A__Item">
      <div class="row">
        <div class="col-lg-3 __A__Title">
          <?php esc_html_e('Screens', 'energyplus'); ?>
        </div>
        <div class="col-lg-9 __A__Description">
          <div class="col-lg-12  __A__Settings_NCT">
            <?php

            $enabled = EnergyPlus::option('reactors-energizer-screens', array_keys(Reactors__energizer__energizer::all_screens()));

            foreach ($screens AS $key=>$value) { ?>
              <div class="form-check w-100 pb-1">
                <input type="checkbox" name="reactors-energizer-screens[]" class=" form-control" value='<?php echo esc_attr($key) ?>'<?php if (in_array($key, $enabled)) { echo 'checked';} ?>/><?php echo esc_html($value) ?> <br>
              </div>
            <?php } ?>
          </div>
          <br>
          <?php esc_html_e('If you have compatibility problems, please turn off Energizer for that page', 'energyplus'); ?>
        </div>
      </div>
    </div>

    <div class="__A__Item">
      <div class="row">
        <div class="col-lg-3 __A__Title">
          <?php esc_html_e('Settings', 'energyplus'); ?>
        </div>
        <div class="col-lg-9 __A__Description">
          <div class="row">
            <div class="col-sm-12 __A__Settings_NCT">

              <div class="form-check">
                <input type="checkbox" value="1" name="reactors-energizer-shadow" <?php if ("1" === EnergyPlus::option('reactors-energizer-shadow', "1")) { echo " checked"; } ?>>
                  <?php esc_html_e('Add shadows to tables', 'energyplus'); ?>
                </div>

                <div class="form-check  pt-1">
                  <input type="checkbox" value="1" name="reactors-energizer-bg" <?php if ("1" === EnergyPlus::option('reactors-energizer-bg', "1")) { echo " checked"; } ?>>
                    <?php esc_html_e('Remove table row background colors', 'energyplus'); ?>
                  </div>

                <div class="form-check pt-1">
                  <input type="checkbox" value="1" name="reactors-energizer-click" <?php if ("1" === EnergyPlus::option('reactors-energizer-click', "0")) { echo " checked"; } ?>>
                    <?php esc_html_e('Show item actions when click like E+ (EXPERIMENTAL)', 'energyplus'); ?>
                  </div>

              </div>
            </div>
          </div>
        </div>
      </div>



      <div class="mt-4 text-center">
        <?php wp_nonce_field( 'energyplus_reactors' ); ?>
        <button name="submit" class="btn btn-sm __A__Button1" type="submit"><?php esc_html_e('Save', 'energyplus'); ?></button>
      </div>
    </form>
  </div>
