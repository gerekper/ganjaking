<?php
/**
 * Plugin Name: GP Post Content Merge Tags
 * Description: Adds support for Gravity Form merge tags in your post content.
 * Plugin URI: http://gravitywiz.com/gravity-forms-post-content-merge-tags/
 * Version: 1.1.9
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 */

define( 'GP_POST_CONTENT_MERGE_TAGS_VERSION', '1.1.9' );

require 'includes/class-gp-bootstrap.php';

$gp_limit_dates_bootstrap = new GP_Bootstrap( 'class-gp-post-content-merge-tags.php', __FILE__ );
