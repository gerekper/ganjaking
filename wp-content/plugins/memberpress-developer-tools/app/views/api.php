<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php if( mpdt_rest_api_available() ): ?>
  <?php $api = MpdtCtrlFactory::fetch('api'); ?>
  <?php $routes = $api->routes(); ?>

  <div class="mepr-page-title"><?php _e('REST API', 'memberpress-developer-tools'); ?></div>
  <h3><?php _e('API Key:', 'memberpress-developer-tools'); ?></h3>
  <em>
    <?php _e('The API Key can be used for authenticating with the REST API. <b>Please ensure your site is using SSL and that you do not share this key.</b> If you feel your key has been compromised, you can regenerate a new one.', 'memberpress-developer-tools'); ?>
  </em>
  <p>
    <input id="mpdt_api_key" type="text" name="mpdt_api_key" value="<?php echo $api_key; ?>" onfocus="this.select();" onclick="this.select();" readonly>
    <span>
      <i class="mpdt-clipboard mp-icon mp-icon-clipboard mp-16" data-clipboard-target="#mpdt_api_key"></i>
      <i class="mpdt-regenerate mp-icon mp-icon-arrows-cw mp-16"></i>
    </span>
  </p>
  <hr/>

  <h3><?php _e('Select an API Route:', 'memberpress-developer-tools'); ?></h3>
  <p><em><?php _e('View dynamic API route documentation and examples.', 'memberpress-developer-tools'); ?></em></p>

  <div class="mpdt_select_wrap mpdt_routes_dropdown_wrap">
    <select id="mpdt_routes_dropdown" class="mpdt_select">
      <option value="-1">-- <?php _e('Select a Route', 'memberpress-developer-tools'); ?> --</option>
      <?php foreach($routes as $slug => $route): ?>
        <option value="<?php echo $slug; ?>"><?php echo $route->name; ?></option>
      <?php endforeach; ?>
    </select>
    <span class="mpdt_rolling">
      <?php echo file_get_contents(MPDT_IMAGES_PATH . '/rolling.svg'); ?>
    </span>
  </div>

  <div>&nbsp;</div>

  <div id="mpdt_route_display" class="mepr-sub-box" style="display: none;">
    <div class="mepr-arrow mepr-gray mepr-up mepr-sub-box-arrow"> </div>
    <div id="mpdt_route">
    </div>
  </div>
<?php else: ?>
  <h2><?php _e('You\'re not running WordPress 4.7 and the WordPress REST API plugin isn\'t active', 'memberpress-developer-tools'); ?></h2>
  <p><?php _e('The MemberPress REST API requires at least WordPress 4.7 or relies on WordPress\'s REST API plugin **Version 2** being installed and activated on this site.', 'memberpress-developer-tools'); ?></p>
  <p><?php printf('You can get the plugin from %1$shere%2$s.', '<a href="https://wordpress.org/plugins/rest-api/">', '</a>'); ?></p>
<?php endif; ?>
