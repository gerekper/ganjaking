<?php
/**
 * EventON html
 *
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/html
 * @version     0.2
 * @updated 	2.2.28
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function eventon_html_yesnobtn($args=''){

	global $ajde;

	return $ajde->wp_admin->html_yesnobtn($args);

}