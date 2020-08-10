<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>

<?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Reactors', 'energyplus'), 'description' => '', 'buttons'=>'')); ?>

<?php echo EnergyPlus_View::run('reactors/nav', array('counts'=>$counts)) ?>

<div id="energyplus-reactors" class="__A__GP">

  <div id="energyplus-widgets--list">
    <div class="row">
      <?php foreach ($all AS $widget) {
        if ('inactive' === EnergyPlus_Helpers::get('status') && 1 === $widget['active']) { continue; }
        if ('active' === EnergyPlus_Helpers::get('status') && 0 === $widget['active']) { continue; }
        ?>
        <div class="col-md-3 col-sm-12 py-2">
          <div class="card h-100  text-white bg-<?php if (1 === $widget['active']) { echo "primary"; } else {echo "dark"; }?>">
            <div class="card-body"><br>
              <?php if (false !== $widget['badge'] && 0 === $widget['active']) { ?>
                <span class="dashicons dashicons-share-alt text-warning"></span>
                <span class="text-warning"><?php echo esc_html($widget['badge']); ?></span>
              <?php } else { ?>
                <span class="dashicons dashicons-share-alt"></span>
              <?php } ?>
              <h3 class="text-white"><?php echo esc_html($widget['title']); ?></h3></div>
              <div class="card-body">
                <p class="card-text"><?php echo esc_html($widget['description']) ?></p>
                <br>
                <?php if (false !== $widget['url']) {
                  $url = explode('|', $widget['url']); ?>
                  <a href="<?php echo esc_url_raw($url[1]);  ?>" class="btn btn-sm btn-outline-light" target="_blank"><?php echo esc_html($url[0]); ?></a>
                <?php  } else if (1 === $widget['active']) { ?>
                  <a href="<?php echo EnergyPlus_Helpers::admin_page('reactors', array( 'action' => 'detail', 'id'=> $widget['id'] ));  ?>" class="btn btn-sm btn-outline-light trig trig-close" data-id="<?php echo esc_attr($widget['id']) ?>"><?php _e('Settings', 'energyplus'); ?></a>
                <?php } else { ?>
                  <a href="<?php echo EnergyPlus_Helpers::admin_page('reactors', array( 'action' => 'detail', 'id'=> $widget['id'] ));  ?>" class="btn btn-sm btn-outline-light trig trig-close" ><?php esc_html_e('Activate', 'energyplus'); ?></a>
                <?php } ?>
              </div>
              <ul class="list-group list-group-flush d-none">
                <li class="list-group-item">
                  <?php if (isset($installed[$widget['id']])) { ?>
                    <a href="<?php echo EnergyPlus_Helpers::admin_page('reactors', array( 'action' => 'detail', 'id'=> $widget['id'] ));  ?>" class="trig trig-close" data-id="<?php echo esc_attr($widget['id']) ?>"><?php esc_html_e('Settings', 'energyplus'); ?></a>
                  <?php } else { ?>
                    <a href="<?php echo EnergyPlus_Helpers::admin_page('reactors', array( 'action' => 'detail', 'id'=> $widget['id'] ));  ?>" class="trig trig-close" ><?php esc_html_e('Activate', 'energyplus'); ?></a>
                  <?php } ?>
                </li>
              </ul>
            </div>
          </div>
        <?php }?>
      </div>

    </div>
  </div>
