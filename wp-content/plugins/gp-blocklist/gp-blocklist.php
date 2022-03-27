<?php
/**
 * Plugin Name: GP Blocklist
 * Description: Validate your form using WordPress' "Disallowed Comment Keys" setting in Settings › Discussion.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-blocklist/
 * Version: 1.3.2
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Text Domain: gp-blocklist
 * Domain Path: /languages
 * Perk: True
 */

define( 'GP_BLOCKLIST_VERSION', '1.3.2' );

require 'includes/class-gp-bootstrap.php';

$gp_comment_blocklist_bootstrap = new GP_Bootstrap( 'class-gp-blocklist.php', __FILE__ );
