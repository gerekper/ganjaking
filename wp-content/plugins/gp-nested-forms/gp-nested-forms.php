<?php
/**
 * Plugin Name: GP Nested Forms
 * Description: Create forms within forms for better management of complex forms. Formception!
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-nested-forms/
 * Version: 1.0.10
 * Author: Gravity Wiz
 * Author URI: https://gravitywiz.com
 * License: GPL2
 * Perk: True
 * Update URI: https://gravitywiz.com/updates/gp-nested-forms
 * Text Domain: gp-nested-forms
 * Domain Path: /languages
 */

define( 'GP_NESTED_FORMS_VERSION', '1.0.10' );

require 'includes/class-gp-bootstrap.php';

$gp_nested_forms_bootstrap = new GP_Bootstrap( 'class-gp-nested-forms.php', __FILE__ );
