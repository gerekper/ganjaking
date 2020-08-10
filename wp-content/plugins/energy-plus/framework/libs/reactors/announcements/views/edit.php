<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>

<div class="energyplus-title inbrowser">
  <h4 class="pl-5"><?php esc_html_e('Announcements', 'energyplus'); ?></h3>
  </div>

  <div id="energyplus-settings-general" class="energyplus-settings __A__Reactors_Settings __A__GP pt-4">
    <?php if (1 === $saved) { ?>
      <div class="alert alert-success" role="alert">
        <span class="dashicons dashicons-smiley"></span>&nbsp;&nbsp;<?php esc_html_e('Settings have been saved', 'energyplus'); ?>
      </div>
    <?php } ?>
    <form action="" method="POST">

      <div class="__A__Item">
        <div class="row">
          <div class="col-lg-2 __A__Title">
            <?php esc_html_e('Title', 'energyplus'); ?>
          </div>
          <div class="col-lg-10 __A__Description">
            <input name="title" type="text" class="__A__Settings_Input form-control" value='<?php echo esc_attr($post['title']) ?>'/>
          </div>
        </div>
      </div>


      <div class="__A__Item">
        <div class="row">
          <div class="col-lg-2 __A__Title">
            <?php esc_html_e('Content', 'energyplus'); ?>
          </div>
          <div class="col-lg-10 __A__Description">
            <?php wp_editor(wp_kses_post($post['content']), 'content',array(
              'textarea_rows' => 10,
              'teeny' => true,
              'media_buttons' => FALSE,
              'quicktags' => false,
              'tinymce'       => array(
                'toolbar1'      => 'bold,italic,underline,link,undo,redo',
                'toolbar2'      => '',
                'toolbar3'      => '',
              )
            ) )?>

          </div>
        </div>
      </div>

      <div class="__A__Item">
        <div class="row">
          <div class="col-lg-2 __A__Title">
            <?php esc_html_e('Icon', 'energyplus'); ?>
          </div>
          <div class="col-lg-10 __A__Description">
            <?php
            global $wpdb;
            $posts = $wpdb->get_results(
              $wpdb->prepare("SELECT event_id, user, type, id, extra, time FROM {$wpdb->prefix}energyplus_events WHERE type = %d ORDER BY event_id DESC LIMIT 5", 15)
              , ARRAY_A);
              if ($posts) {
                foreach ($posts AS $icon) {
                  $icon = maybe_unserialize($icon['extra']);
                  if (is_array($icon)) {
                    $icon = $icon['icon'];
                    if (empty($icon) || 'empty' === $icon) {
                      continue;
                    }
                  }
                  ?>
                  <button data-icon='<?php echo esc_attr($icon)?>' class="__A__Announcements_Icon <?php echo esc_attr($icon)?>"></button>
                  <?php
                }
                ?>
                &nbsp; → &nbsp;
              <?php } ?>
              <button name="icon" data-icon="<?php echo esc_attr($post['icon'])?>" class="__A__Announcements_Icon1 __A__Settings_Change_Icon1 __A__StopPropagation" data-iconset="fontawesome5"><?php esc_html_e('Change icon', 'energyplus'); ?></button>
            </div>
          </div>
        </div>


        <div class="mt-4 text-center">
          <?php wp_nonce_field( 'energyplus_reactors' ); ?>
          <button name="submit" class="btn btn-sm __A__Button1" type="submit"><?php esc_html_e('Save', 'energyplus'); ?></button>
          &nbsp; &nbsp; &nbsp;
          <a  class="btn btn-sm __A__Button1" href="<?php echo EnergyPlus_Helpers::admin_page('reactors', array('action'=> 'detail', 'id'=>'announcements'))?>"><?php esc_html_e('Cancel', 'energyplus'); ?></a>
        </div>
      </form>
    </div>

    <script>
    jQuery(document).ready(function() {
      "use strict";

      jQuery('.__A__Settings_Change_Icon1').iconpicker();

      jQuery('.__A__Announcements_Icon').on('click', function(e) {
        e.preventDefault();

        var selected = jQuery(this).data('icon');
        console.log(selected);
        jQuery('.__A__Settings_Change_Icon1').iconpicker('setIcon', selected);


      })
    }
  );
</script>
