<?php
/**
 * Plugin Name: GP Live Preview
 * Description: Preview your forms on the frontend of your site.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-live-preview/
 * Version: 1.6
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Text Domain: gp-live-preview
 * Domain Path: /languages
 */

define( 'GP_LIVE_PREVIEW_VERSION', '1.6' );

require 'includes/class-gp-bootstrap.php';

$gp_live_preview_bootstrap = new GP_Bootstrap( 'class-gp-live-preview.php', __FILE__ );
