<?php 
namespace Happy_Addons\Elementor\Theme_Hooks;

use Happy_Addons\Elementor\Theme_Builder;

defined( 'ABSPATH' ) || exit;

/**
 * MyListing support for the header footer.
 */
class MyListing {


	/**
	 * Run all the Actions / Filters.
	 */
	function __construct($template_ids) {
		global $ha__template_ids;
		
		$ha__template_ids = $template_ids;
		include 'my-listing-functions.php';
	}
}