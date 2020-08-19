<?php
/**
 * Plugin Name: GP Reload Form
 * Description: Reload the form following an AJAX submission. Useful in situations where you would like to allow multiple form submission with refreshing the page.
 * Plugin URI: http://gravitywiz.com/
 * Version: 1.1.14
 * Author: David Smith
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 */

define( 'GP_RELOAD_FORM_VERSION', '1.1.14' );

require 'includes/class-gp-bootstrap.php';

$gp_reload_form_bootstrap = new GP_Bootstrap( 'class-gp-reload-form.php', __FILE__ );