<?php
/**
 * Plugin Name: GP Word Count
 * Description: Limit the number of words that can be submitted in a Single Line Text, Paragraph Text and Post Body fields.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-word-count/
 * Version: 1.4.7
 * Author: Gravity Wiz
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Text Domain: gp-word-count
 * Domain Path: /languages
 * Perk: True
 */

define( 'GP_WORD_COUNT_VERSION', '1.4.7' );

require 'includes/class-gp-bootstrap.php';

$gp_word_count_bootstrap = new GP_Bootstrap( 'class-gp-word-count.php', __FILE__ );