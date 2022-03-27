<?php
/**
 * Plugin Name: GP Limit Submissions
 * Description: Limit the number of entries that can be submitted by almost anything (e.g. user, role, IP, field value).
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-limit-submissions/
 * Version: 1.1
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com
 * License: GPL-3.0+
 * Text Domain: gp-limit-submissions
 * Domain Path: /languages
 * Perk: True
 * Update URI: https://gravitywiz.com/updates/gp-limit-submissions
 */

define( 'GP_LIMIT_SUBMISSIONS_VERSION', '1.1' );

require 'includes/class-gp-bootstrap.php';

$gp_limit_submissions_bootstrap = new GP_Bootstrap( 'class-gp-limit-submissions.php', __FILE__ );
