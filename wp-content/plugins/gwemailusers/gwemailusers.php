<?php
/**
 * Plugin Name: GP Email Users
 * Description: Send a quick email to all users who have submitted a specific form.
 * Plugin URI: http://gravitywiz.com/
 * Version: 1.3.9
 * Author: David Smith
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Text Domain: gp-email-users
 * Domain Path: /languages
 * Perk: True
 */

define( 'GP_EMAIL_USERS_VERSION', '1.3.9' );

require 'includes/class-gp-bootstrap.php';

$gp_email_users_bootstrap = new GP_Bootstrap( 'class-gp-email-users.php', __FILE__ );