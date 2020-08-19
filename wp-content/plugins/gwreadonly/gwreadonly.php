<?php
/**
 * Plugin Name: GP Read Only
 * Description: Mark your form fields as read-only to allow users to see field data but not modify it.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-read-only/
 * Version: 1.3.11
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 */

define( 'GP_READ_ONLY_VERSION', '1.3.11' );

require 'includes/class-gp-bootstrap.php';

$gp_read_only_bootstrap = new GP_Bootstrap( 'class-gp-read-only.php', __FILE__ );

