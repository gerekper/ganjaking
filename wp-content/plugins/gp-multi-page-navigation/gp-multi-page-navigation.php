<?php
/**
 * Plugin Name: GP Multi-page Navigation
 * Description: Navigate between form pages quickly by converting the page steps into page links or creating your own custom page links.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-multi-page-navigation/
 * Version: 1.1.4
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com
 * License: GPLv2
 * Perk: True
 * Text Domain: gp-multi-page-navigation
 * Domain Path: /languages
 */

define( 'GP_MULTI_PAGE_NAVIGATION_VERSION', '1.1.4' );

require 'includes/class-gp-bootstrap.php';

$gp_multi_page_navigation_bootstrap = new GP_Bootstrap( 'class-gp-multi-page-navigation.php', __FILE__ );
