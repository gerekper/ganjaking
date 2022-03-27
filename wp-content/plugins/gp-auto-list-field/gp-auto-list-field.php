<?php
/**
 * Plugin Name: GP Auto List Field
 * Description: Automatically set the number of rows in a List field by the value of another field - or - capture the number of List field rows.
 * Plugin URI: http://gravitywiz.com/
 * Version: 1.0.1
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Text Domain: gp-auto-list-field
 * Domain Path: /languages
 */

define( 'GP_AUTO_LIST_FIELD_VERSION', '1.0.1' );

require 'includes/class-gp-bootstrap.php';

$gp_auto_list_field_bootstrap = new GP_Bootstrap( 'class-gp-auto-list-field.php', __FILE__ );
