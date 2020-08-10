<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>


<div id="energyplus-theme"  class="__A__Segment_<?php echo strtolower( EnergyPlus_Helpers::get( 'segment', 'dashboard' ) ) ?>">
  <div id="energyplus-header">
    <a id="trig2" href="javascript:;" hrefx="<?php echo EnergyPlus_Helpers::admin_page(''); ?>"><div class="d-flex align-items-center">
      <svg version="1.1" id="__A__Mobile_Menu_Icon" x="0px" y="0px" viewBox="0 0 384.97 384.97" xml:space="preserve"> <g> <g id="Menu_1_"> <path d="M12.03,120.303h360.909c6.641,0,12.03-5.39,12.03-12.03c0-6.641-5.39-12.03-12.03-12.03H12.03 c-6.641,0-12.03,5.39-12.03,12.03C0,114.913,5.39,120.303,12.03,120.303z"/> <path d="M372.939,180.455H12.03c-6.641,0-12.03,5.39-12.03,12.03s5.39,12.03,12.03,12.03h360.909c6.641,0,12.03-5.39,12.03-12.03 S379.58,180.455,372.939,180.455z"/> <path d="M372.939,264.667H132.333c-6.641,0-12.03,5.39-12.03,12.03c0,6.641,5.39,12.03,12.03,12.03h240.606 c6.641,0,12.03-5.39,12.03-12.03C384.97,270.056,379.58,264.667,372.939,264.667z"/> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg></div>
    </a>
    <div class="energyplus-logo d-flex justify-content-center align-items-center">
      <?php EnergyPlus_Helpers::is_desktop_app() ?>
      <?php
      $energyplus_img_src = wp_get_attachment_image_src( EnergyPlus::option('feature-logo'), 'full' );
      if (!is_array($energyplus_img_src)) {
        $energyplus_img_src = array('');
      }
      ?>
      <a href="<?php echo admin_url( "admin.php?page=energyplus" )?>"><img src="<?php echo esc_url($energyplus_img_src[0]) ?>"> </a>
    </div>
    <div class="energyplus-menu __A__EnergyPlus_Menu_C d-flex">
      <nav class="__A__MainMenu overflow-hidden">
        <ul class="__A__MenuH __A__MainMenuH">
          <?php echo EnergyPlus_Admin::get_menu();  ?>
          <li class="more"> <span>...</span>
            <ul id="overflow">
            </ul>
          </li>
        </ul>
      </nav>
    </div>
    <div class="float-right align-middle d-flex align-items-center energyplus-menu __A__My_Name">
      <nav class="__A__MainMenu">
        <ul class="__A__MenuH">
          <li>
            <a href="javascript:;"><?php if ('' !==  wp_get_current_user()->user_firstname) {
              echo esc_html(wp_get_current_user()->user_firstname);
            } else {
              echo '-';
            } ?></a>
            <ul class="energyplus-header-submenu">
              <?php if ("1" === EnergyPlus::option('feature-own_themes') || EnergyPlus_Admin::is_admin(null) || (!EnergyPlus_Admin::is_admin(null) && '1' === EnergyPlus::option('reactors-tweaks-settings-woocommerce',0))) { ?>
                <li><a href="<?php echo EnergyPlus_Helpers::admin_page('settings');  ?>"><span class="energyplus-menu--textx">Settings</span></a></li>
              <?php } ?>
              <li><a href="<?php echo esc_url_raw(get_bloginfo('url'))?>" target="_blank"><?php esc_html_e('View Store', 'energyplus'); ?></a></li>
              <li><a href="<?php echo wp_logout_url(); ?>"><span class="energyplus-menu--textx">Logout</span></a></li>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
    <div class="float-right d-flex align-items-center __A__My">
      <?php $notification_count = EnergyPlus_Events::notification_count(); ?>
      <a href="javascript:;" class="__A__DisplayNotifications badge badge-silent<?php if (0 < $notification_count) { ?> badge-danger<?php }?>">
        <?php echo esc_html($notification_count) ?>
      </a>
    </div>

    <div class="float-right __A__My d-flex align-items-center">
      <a href="javascript:;" class=""><span class="dashicons dashicons-search"></span></a>
    </div>
  </div>
