<?php
/**
 * Plugin Name: GP Preview Submission
 * Description: Add a simple submission preview to allow users to confirm their submission is correct before submitting the form.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-preview-submission/
 * Version: 1.3.10
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com
 * License: GPL2
 * Perk: True
 * Update URI: https://gravitywiz.com/updates/gp-preview-submission
 */

define( 'GP_PREVIEW_SUBMISSION_VERSION', '1.3.10' );

require 'includes/class-gp-bootstrap.php';

$gp_preview_submission_bootstrap = new GP_Bootstrap( 'class-gp-preview-submission.php', __FILE__ );
