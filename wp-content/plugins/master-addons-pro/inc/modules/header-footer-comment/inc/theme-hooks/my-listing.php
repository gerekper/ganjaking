<?php 
namespace MasterHeaderFooter\Theme_Hooks;

defined( 'ABSPATH' ) || exit;

/**
 * MyListing support for the header footer.
 */
class MyListing {


	/**
	 * Run all the Actions / Filters.
	 */
	function __construct($template_ids) {
		global $master_template_ids;
		
		$master_template_ids = $template_ids;
		include 'my-listing-functions.php';
	}
}