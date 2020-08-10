<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
} ?>
<?php foreach ( $_energyplus_menu AS $energyplus_menu_k => $energyplus_menu ) { ?>
  <?php if ( 1 === $energyplus_menu ['active']) { ?>
    <li id="energyplus-<?php echo esc_attr($energyplus_menu_k)?>" title="<?php echo esc_html(strip_tags($energyplus_menu['title'])) ?>" <?php if (isset($energyplus_menu['segment']) && EnergyPlus_Helpers::get("segment") === $energyplus_menu['segment']) { echo "class='energyplus-menu--selected'"; }?>>
      <?php if (isset($energyplus_menu['admin_link'])) { ?>
        <a href="<?php echo EnergyPlus_Helpers::clean( $energyplus_menu['admin_link'] ) ?>"<?php if(isset($energyplus_menu['target'])) {echo " target='_blank'";} ?>>
        <?php } else { ?>
          <a href="<?php echo admin_url( "admin.php?page=energyplus&segment=". $energyplus_menu['segment'] ."" )?>">
          <?php } ?>
          <?php if (false !== stripos($energyplus_menu['icon'], 'fa-')) {?>
            <div class="dashicons-before svg"><span class='__A__Custom_Icon_Container energyplus-custom-icon <?php echo esc_attr($energyplus_menu['icon'])?>'></span></div>
          <?php } else if ('dashicons-admin-generic' === $energyplus_menu['icon'] OR false === stripos($energyplus_menu['icon'], 'dashicons') or (false !== stripos($energyplus_menu['icon'], '//'))) {?>
            <div class="dashicons-before svg"><span class='__A__Custom_Icon_Container energyplus-menu--empty-icon'><?php echo esc_html(substr($energyplus_menu['title'],0,2)); ?></span></div>
          <?php } else {?>
            <div class="__A__Custom_Icon_Container dashicons-before <?php echo esc_attr($energyplus_menu["icon"]); ?>"><?php if("" === $energyplus_menu["icon"]) { echo "<span class='energyplus-menu--empty-icon'>" . esc_html(substr($energyplus_menu['title'],0,2)) ."</span>"; } ?></div>
          <?php } ?>
          <?php if (isset($energyplus_menu['badge']) && $energyplus_menu['badge'] > 0 ) { ?>
            <span class="badge badge-pill badge-danger __A__Menu_Badge"><?php echo absint($energyplus_menu['badge']); ?></span>
          <?php } ?>
          <div class="energyplus-menu--text"><?php echo wp_kses_post($energyplus_menu['title']) ?></div></a>
          <?php if (isset($energyplus_menu['submenu'])) { ?>
            <ul class="energyplus-header-submenu">
              <?php foreach ($energyplus_menu['submenu'] AS $sub) { ?>
                <li><a href="<?php echo esc_url($sub[2]) ?>"><span class="energyplus-menu--textx"><?php echo wp_kses_post($sub[0]) ?></span></a></li>
              <?php }?>
            </ul>
          <?php } ?>
        </li>
      <?php }?>
    <?php }?>
