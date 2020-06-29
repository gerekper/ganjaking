=== Plugin Name ===
Contributors: nechii
Tags: ad, advertisment, module
Requires at least: 4.2
Tested up to: 5.2
Stable tag: 1.2.0
Requires PHP: 5.4

Module adds ad units to plugins.

== Description ==

The module allows you to add the specified ad blocks to the plugins.

Managing and storing the content of each block occurs in special side plugin.

To add the necessary blocks, you need to add the following code to the main plugin file:
if ( is_admin() ) {
	global $wbcr_plugin_name_adinserter;

	$wbcr_plugin_name_adinserter = new WBCR\Factory_Adverts_000\Base(
		__FILE__,
		array_merge(
			$plugin_info,
			array(
				'dashboard_widget' => true, // show dashboard widget (default: false)
				'right_sidebar'    => true, // show adverts sidebar (default: false)
				'notice'           => true, // show notice message (default: false)
			)
		)
	);
}
In this example, three ad blocks will be added: dashboard_widget, right_sidebar and notice.

It is possible to add an ad block manually. To do this, in the specific place of the code you need to insert the following code:
global $wbcr_plugin_name_adinserter;
echo $wbcr_plugin_name_adinserter->get_adverts( 'right_sidebar' );
In this example will be displayed content for ad block 'right_sidebar';

== Changelog ==

= 1.2.3 =
* Set dashboard widget first in order

= 1.2.2 =
* Changed the name of a global variable and its use rule

= 1.2.1 =
* Moved constant for rest request url from wp-config.php to boot.php

= 1.2.0 =
* Removed rest request. Only get request available.
* Cleaning and refactoring code.

= 1.1.0 =
* Add new notice functions.
* Cleaning and refactoring code.

= 1.0.0 =
* Cleaning and refactoring code.
* Add doc blocks.
* Some fixes and changes.

= 0.9.0 =
* Added new blocks.
* Fixed widget functions.

= 0.8.0 =
* Added notice block.
* Added constants for unique dashboard notice.
* Fixed widget functions.

= 0.7.0 =
* Added constants for unique dashboard widget.
* Fixed widget functions.

= 0.6.0 =
* Improve widget functions.
* Fixed cache functions.

= 0.5.0 =
* Added default rest response.
* Fixed rest request.

= 0.4.0 =
* Fixed rest request response.
* Fixed base functions.

= 0.3.0 =
* Improve rest request.
* Fixed requests functions.

= 0.2.0 =
* Added expiration time for cached data.
* Fixed cache functions.

= 0.1.0 =
* Version with base functions.
