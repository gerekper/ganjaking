<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="__A__Reactors_Details">

  <div class="__A__Reactors_Details_Intro_Icon text-center">
    <span class="dashicons dashicons-share-alt"></span>
  </div>

  <div class="__A__Reactors_Details_Intro text-center">
    <div class="w-75">
    <h2><?php echo esc_html($reactor['title']) ?></h2>
    <p><?php echo wp_kses_post($reactor['details']); ?></p>
    <br><br><br>
  </div>
  </div>

  <div class="text-center">
    <a href="<?php echo EnergyPlus_Helpers::secure_url('reactors', esc_attr($reactor['id']), array('action'=>'activate', 'id'=>$reactor['id']))  ?>" class="btn btn-sm btn-danger __A__Reports_Start_Import">Activate</a>
  </div>

</div>
