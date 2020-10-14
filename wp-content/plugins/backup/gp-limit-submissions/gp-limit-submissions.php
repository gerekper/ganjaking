<?php
/**
 * Plugin Name: GP Limit Submissions
 * Plugin URI: http://gravitywiz.com/documentation/gravity-forms-limit-submissions/
 * Description: Limit the number of entries that can be submitted by almost anything (e.g. user, role, IP, field value).
 * Version: 1.0-beta-1.15
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com
 * License: GPL-3.0+
 * Text Domain: gp-limit-submissions
 * Domain Path: /languages
 * Perk: True
 */

define( 'GP_LIMIT_SUBMISSIONS_VERSION', '1.0-beta-1.15' );

require 'includes/class-gp-bootstrap.php';

$gp_limit_submissions_bootstrap = new GP_Bootstrap( 'class-gp-limit-submissions.php', __FILE__ );