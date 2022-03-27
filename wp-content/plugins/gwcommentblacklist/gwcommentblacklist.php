<?php
/**
 * Plugin Name: GP Blacklist
 * Description: Validate your form against your WordPress comment blacklist.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-blacklist/
 * Version: 1.2.9
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Text Domain: gp-blacklist
 * Domain Path: /languages
 * Perk: True
 */

define( 'GP_BLACKLIST_VERSION', '1.2.9' );

require 'includes/class-gp-bootstrap.php';

$gp_comment_blacklist_bootstrap = new GP_Bootstrap( 'class-gp-blacklist.php', __FILE__ );
