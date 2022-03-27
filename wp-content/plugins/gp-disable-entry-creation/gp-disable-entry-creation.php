<?php
/**
 * Plugin Name: GP Disable Entry Creation
 * Description: Disable entry creation per form with Gravity Forms.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-disable-entry-creation/
 * Version: 2.0
 * Author: Gravity Wiz, Richard Wawrzyniak
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 */

define( 'GP_DISABLE_ENTRY_CREATION', '2.0' );

require 'includes/class-gp-bootstrap.php';

$gp_disable_entry_creation_bootstrap = new GP_Bootstrap( 'class-gp-disable-entry-creation.php', __FILE__ );