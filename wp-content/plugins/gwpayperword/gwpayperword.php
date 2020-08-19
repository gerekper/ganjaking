<?php
/**
* Plugin Name: GP Pay Per Word
* Description: Create products which calculate a total based on the number of words in a Paragraph or Post Body field.
* Plugin URI: http://gravitywiz.com/
* Version: 1.1.3
* Author: Gravity Wiz
* Author URI: http://gravitywiz.com/
* License: GPL2
* Perk: True
*/

define( 'GP_PAY_PER_WORD_VERSION', '1.1.3' );

require 'includes/class-gp-bootstrap.php';

$gp_pay_per_word_bootstrap = new GP_Bootstrap( 'class-gp-pay-per-word.php', __FILE__ );