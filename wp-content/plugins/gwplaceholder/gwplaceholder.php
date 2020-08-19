<?php
/**
 * Plugin Name: GP Placeholder
 * Description: Add support for HTML5 placeholders to Gravity Forms.
 * Plugin URI: http://gravitywiz.com/
 * Version: 1.3.7
 * Author: David Smith
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 */

define( 'GP_PLACEHOLDER_VERSION', '1.3.7' );

require 'includes/class-gp-bootstrap.php';

$gp_placeholder_bootstrap = new GP_Bootstrap( 'class-gp-placeholder.php', __FILE__ );
