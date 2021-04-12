<?php
/**
 * Plugin Name: GP Blacklist
 * Description: Validate your form against your WordPress comment blacklist.
 * Plugin URI: http://gravitywiz.com/
 * Version: 1.2.6
 * Author: David Smith
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Text Domain: gp-blacklist
 * Domain Path: /languages
 * Perk: True
 */

define( 'GP_BLACKLIST_VERSION', '1.2.6' );

require 'includes/class-gp-bootstrap.php';

$gp_comment_blacklist_bootstrap = new GP_Bootstrap( 'class-gp-blacklist.php', __FILE__ );
