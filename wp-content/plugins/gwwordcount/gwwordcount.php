<?php
/**
 * Plugin Name: GP Word Count
 * Description: Limit the number of words that can be submitted in a Single Line Text, Paragraph Text and Post Body fields.
 * Plugin URI: http://gravitywiz.com/
 * Version: 1.4.5
 * Author: David Smith
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Text Domain: gp-word-count
 * Domain Path: /languages
 * Perk: True
 */

define( 'GP_WORD_COUNT_VERSION', '1.4.5' );

require 'includes/class-gp-bootstrap.php';

$gp_word_count_bootstrap = new GP_Bootstrap( 'class-gp-word-count.php', __FILE__ );