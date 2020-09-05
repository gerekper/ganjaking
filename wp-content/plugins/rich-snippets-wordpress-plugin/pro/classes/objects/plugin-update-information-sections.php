<?php

namespace wpbuddy\rich_snippets\pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Plugin Update Information class.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Plugin_Update_Information_Sections extends Dictionary {

	public $description = '';
	public $installation = '';
	public $faq = '';
	public $screenshots = '';
	public $changelog = '';
	public $other_notes = '';
}
