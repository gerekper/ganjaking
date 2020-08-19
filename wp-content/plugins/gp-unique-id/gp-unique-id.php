<?php
/**
 * Plugin Name: GP Unique ID
 * Description: Generate unique IDs (i.e. reference numbers, codes, invoice numbers, etc.) on submission for your Gravity Form entries.
 * Plugin URI: http://gravitywiz.com/documentation/gp-unique-id/
 * Version: 1.3.14
 * Author: David Smith
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Text Domain: gp-unique-id
 * Domain Path: /languages
 */

define( 'GP_UNIQUE_ID_VERSION', '1.3.14' );

require 'includes/class-gp-bootstrap.php';

$gp_unique_id_bootstrap = new GP_Bootstrap( 'class-gp-unique-id.php', __FILE__ );