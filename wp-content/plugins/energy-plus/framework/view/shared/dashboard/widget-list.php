<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
} ?>


<?php echo EnergyPlus_View::run('header-in'); ?>

<div class="energyplus-title inbrowser">
  <h3><?php esc_html_e('Widgets', 'energyplus'); ?></h3>
</div>

<div id="energyplus-widgets--list">
  <div class="grid __A__GP"></div>
  <h5><?php esc_html_e('Current widgets in your dashboard', 'energyplus'); ?></h5>
  <div class="row">
    <?php foreach ($installed AS $widget) {  ?>
      <div class="col-4">
        <div class="card">
          <div class="card-body bg-secondary"><?php echo esc_html($widget['title']); ?></div>
            <div class="card-body">
              <p class="card-text"><?php echo esc_html($widget['description']) ?></p>
            </div>
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <a href="javascript:;" class="__A__Widget_Delete" data-id="<?php echo esc_attr($widget['id']) ?>"><?php _e('Remove', 'energyplus'); ?></a>
              </li>
            </ul>
          </div>
        </div>
      <?php }?>
    </div>
    <br />

    <h5><?php esc_html_e('You can add them to your dashboard', 'energyplus'); ?></h5>
    <div class="row">

      <?php foreach ($all AS $widget) {  ?>
        <?php	if ((true === $widget['multiple']) OR ($widget['multiple'] === FALSE && array_search($widget['id'], array_column($installed, 'type')) === FALSE)) {  ?>
          <div class="col-4">
            <div class="card">
              <div class="card-body bg-secondary"><?php echo esc_html($widget['title']); ?></div>
                <div class="card-body">
                  <p class="card-text"><?php echo esc_attr($widget['description']) ?></p>
                </div>
                <ul class="list-group list-group-flush">
                  <li class="list-group-item">
                    <a href="javascript:;" class="__A__Widget_Add_Now" data-id="<?php echo esc_attr($widget['id']) ?>"><?php esc_html_e('Add to dashboard', 'energyplus'); ?></a>
                  </li>
                </ul>
              </div>
            </div>

          <?php } ?>
        <?php }?>
      </div>
      <p>&nbsp;</p>
    </div>
