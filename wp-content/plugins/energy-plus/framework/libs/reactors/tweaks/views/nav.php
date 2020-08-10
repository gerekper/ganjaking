<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="energyplus-title--menu __A__Coupons_Mode_2">
  <div class="row __A__GP">
    <ul>
      <li>
        <a href="<?php echo EnergyPlus_Helpers::secure_url('reactors', esc_attr($id), array('action'=>'activate', 'do'=>'deactivate', 'id'=> $id))  ?>" class="text-danger font-weight-normal">Deactivate now</a>
      </li>
    </ul>
  </div>
</div>
