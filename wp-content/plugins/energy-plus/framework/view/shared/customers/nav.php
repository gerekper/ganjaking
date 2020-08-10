<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="energyplus-title--menu __A__Coupons_Mode_2">
  <div class="row energyplus-gp">
    <ul>
      <li><a class="__A__Button1<?php EnergyPlus_Helpers::selected('role')?>" href="<?php echo EnergyPlus_Helpers::admin_page('customers', array( ));  ?>"><?php esc_html_e('All', 'energyplus'); ?> <?php echo esc_html($counts['total_users'])?></a> </li>
      <?php

      if (isset($counts['avail_roles'])) {
        foreach ($counts['avail_roles'] AS $key=>$count) {
          if ($count > 0) { ?>
            <li>
              <a class="__A__Button1<?php EnergyPlus_Helpers::selected('role', $key)?>" href="<?php echo EnergyPlus_Helpers::admin_page('customers', array( 'role' => $key ));  ?>">
                <?php echo esc_html($roles[$key]['name']); ?> <?php echo esc_html($count)?></a>
              </li>
            <?php }
          }
        } ?>

        <?php do_action('energyplus_submenu', 'customers'); ?>

        <li class="__A__Li_Search">
          <a href="javascript:;" class="__A__Button1 __A__Search_Button"><?php esc_html_e('Search', 'energyplus'); ?></a>
        </li>
      </ul>
    </div>
  </div>
