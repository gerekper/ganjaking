<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php if (0 === $step) { ?>

  <div class="__A__Reactors_Details">

    <div class="__A__Reactors_Details_Intro_Icon text-center">
      <span class="dashicons dashicons-heart"></span>
    </div>

    <div class="__A__Reactors_Details_Intro text-center">
      <div class="w-75">
        <h4><?php esc_html_e('Just a moment...', 'energyplus'); ?></h3>
          <br>
          <h2><?php esc_html_e('Energy+ is not activated', 'energyplus'); ?></h2>
          <p><?php esc_html_e('It\'s a good time to complete your activation!', 'asterisk'); ?></p>
          <br><br><br>
        </div>
      </div>

      <div class="text-center">
        <a href="<?php echo EnergyPlus_Helpers::admin_page('reactors', array('action'=>'energy-activate', 'id'=>EnergyPlus_Helpers::get('id')))  ?>" class="btn btn-sm btn-danger __A__Reports_Start_Import"><?php esc_html_e('Activate now', 'energyplus'); ?></a>
      </div>

    </div>

    <div class="fixed-bottom w-100 text-center mb-5 hidden"><a href="<?php echo EnergyPlus_Helpers::admin_page('reactors', array('action'=>'detail', 'id'=>EnergyPlus_Helpers::get('id'), 'later'=>1))  ?>" class="text-mute text-decoration-none"><u><?php esc_html_e('Not now', 'energyplus'); ?></u></a></div>

  <?php } ?>


  <?php if (1=== $step) { ?>

    <div class="__A__Reactors_Details">
      <form action="" method="POST">
        <div class="__A__Reactors_Details_Intro __A__Reactors_Energy_Activation text-left">
          <div class="w-75">
            <span class="dashicons dashicons-smiley"></span>
            <br>
            <br>
            <br>
            <h2><?php esc_html_e('Energy+ activation', 'energyplus'); ?></h2>
            <div class="__A__Item pt-0">
              <?php esc_html_e('We will send your Site URL and Purchase Code to Energy+ Activation Servers to complete process.', 'energyplus'); ?>
            </div>

            <div class="__A__Item">
              <div class="row">
                <div class="col-lg-3 __A__Title">
                  <?php esc_html_e('Your site adress', 'energyplus'); ?>
                </div>
                <div class="col-lg-9 __A__Description">
                  <?php echo get_bloginfo('url'); ?>
                </div>
              </div>
            </div>

            <div class="__A__Item">
              <div class="row">
                <div class="col-lg-3 __A__Title">
                  <?php esc_html_e('Purchase Code', 'energyplus'); ?>
                </div>
                <div class="col-lg-9 __A__Description">
                  <div class="col-lg-8 input-group __A__Settings_NCT">
                    <input name="code" class="__A__Settings_Input form-control"  placeholder="" value='<?php echo esc_attr(EnergyPlus_Helpers::post('code', '')) ?>'/>
                  </div>
                  <br>
                  <a href="//help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank"><?php esc_html_e('How to find my purchase code?', 'energyplus'); ?></a>
                </div>
              </div>
            </div>

            <?php if (2 === $return) { ?>

              <div class="__A__Item">
                <div class="row">

                  <div class="col-lg-12 __A__Description">
                    <div class="alert alert-danger text-center" role="alert">
                      <?php echo esc_html($response); ?>
                    </div>
                  </div>
                </div>
              </div>

            <?php } ?>
            <div class="__A__Item border-bottom-0">
              <div class="row">
                <div class="col-lg-3 __A__Title">
                </div>
                <div class="col-lg-9 __A__Description">
                   <?php wp_nonce_field( 'energyplus_reactors' ); ?>
                  <button class="btn btn-sm btn-primary" name="submit"><?php esc_html_e('Activate', 'energyplus'); ?></button>
                </div>
              </div>
            </div>

          </div>
        </div>
      </form>
    </div>
  <?php } ?>
