<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
} ?>

<div id='slider' class="d-none">
  <div id="inbrowser--loading" class="inbrowser--loading h100 d-flex align-items-center align-middle">
    <div class="lds-ellipsis lds-ellipsis-black"><div></div><div></div><div></div></div>
  </div>
  <div class="__A__Trig_Close">
    <a href="javascript:;" class="__A__Trig_CloseButton"><span class="dashicons dashicons-no-alt"></span></a>
  </div>
  <div class="__A__Trig_Framer"><iframe frameborder=0 class="__A__Trig_Framer_In" src="about:blank" id="inbrowser"></iframe>
  </div>
</div>

<div id='slider2' class="d-none">

  <div class="__A__LeftMenu __A__Channels">
    <div class="text-center">
      <?php
      $energyplus_img_src = wp_get_attachment_image_src( EnergyPlus::option('feature-logo'), 'full' );
      if (!is_array($energyplus_img_src)) {
        $energyplus_img_src = array('');
      }
      ?>
      <img src="<?php echo esc_url_raw($energyplus_img_src[0]) ?>" class="__A__LeftMenu_Logo"></div>
      <nav class="__A__MainMenu vertical">
        <ul>
          <li><a href="javascript:;" class="__A__Left_Search"><?php esc_html_e('Search', 'energyplus'); ?></a></li>
          <?php echo EnergyPlus_Admin::get_menu(array('settings' => true));  ?>
          <li>
            <a href="<?php echo wp_logout_url(); ?>"><?php esc_html_e('Logout', 'energyplus'); ?></a>
          </li>
        </ul>
      </nav>
    </div>
  </div>

  <div id="notifications"  class="d-none">
    <div id="heading"><?php esc_html_e('Notifications', 'energyplus'); ?>
      <span class="float-right">
        <a href="javascript:;" class="__A__X badge badge-black">x</a>
      </span>
    </div>
    <div class="__A__Notifications_Content"></div>
  </div>

  <div id="__A__Ajax_Notification" class="d-none">
    <div class="badge badge-pill badge-warning __A__Ajax_Notification_Container">
      <div class="d-flex align-items-center align-middle">

        <div class="row">
          <div class="__A__Ajax_Notification_Top">
            <div class="__A__Loading">
              <div class="lds-ellipsis"><div></div><div></div></div>
            </div>
            <div class="__A__OK d-none">
              <span class="dashicons dashicons-yes"></span>
            </div>

            <div class="__A__Error te d-none">
              <span class="dashicons dashicons-no"></span>
            </div>
          </div>

          <div class="col align-middle d-flex align-items-center">
            <span class="__A__Text">  Please wait...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="energyplus-search-1--overlay" id="energyplus-search-1--overlay">
  <div id="energyplus-search-1--wrapper">
    <form method="get" id="energyplus-search-1-form" action="" onsubmit="return false;">
      <a href="#" class="energyplus-search-1--close" id="energyplus-search-1--close-button"><span class="dashicons dashicons-no"></span></a>
      <input type="text" value="" name="ss1" placeholder="Search..."  class="energyplus-search-input" autofocus autocomplete="off" >
    </form>

    <div class="__A__Search_Container_Searching hidden"><?php esc_html_e('Searching...', 'energyplus'); ?></div>

    <div class="__A__Search_Products __A__Search_Start"></div>
    <div class="__A__Search_Orders __A__Search_Start"></div>
    <div class="__A__Search_Customers __A__Search_Start"></div>
    <div class="__A__Search_Container">
    </div>
    <div class="__A__Search_Container_No">
      <div class="__A__EmptyTable d-flex align-items-center justify-content-center text-center">
        <div>  <span class="dashicons dashicons-marker"></span><br><?php _e('Nothing found', 'energyplus'); ?></div>
      </div>
    </div>
  </div>
</div>
<?php if (EnergyPlus_Admin::is_energyplus()) {
  wp_nonce_field( 'energyplus-general' );
} ?>
</div>
