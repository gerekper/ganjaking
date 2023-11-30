<?php
/**
 * Plugin Name: GP Advanced Select
 * Description: Modern Drop Down and Multi Select fields with search and powerful integrations.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-advanced-select/
 * Version: 1.0
 * Author: Gravity Wiz
 * Author URI: https://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Text Domain: gp-advanced-select
 * Domain Path: /languages
 */

define( 'GP_ADVANCED_SELECT_VERSION', '1.0' );

require plugin_dir_path( __FILE__ ) . 'includes/class-gp-bootstrap.php';

$GP_Advanced_Select_bootstrap = new GP_Bootstrap( 'class-gp-advanced-select.php', __FILE__ );
