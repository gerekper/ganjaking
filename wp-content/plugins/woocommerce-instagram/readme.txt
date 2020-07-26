=== WooCommerce Instagram ===
Contributors: woocommerce, themesquad
Tags: woocommerce, instagram, hashtag, product, showcase
Requires at least: 4.4
Tested up to: 5.4
Stable tag: 3.3.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
WC requires at least: 3.2
WC tested up to: 4.3
Woo: 260061:ecaa2080668997daf396b8f8a50d891a

Connect your store with Instagram. Upload your product catalog to Instagram and showcase how your customers are using them.
Visit our [product page](https://woocommerce.com/products/woocommerce-instagram/) for more info.

== Minimum Requirements ==

* PHP version 5.2.4 or greater (PHP 7.2 or greater is recommended)
* MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)

== Installation ==

1. Unzip and upload the plugin’s folder to your /wp-content/plugins/ directory.
2. Activate the extension through the ‘Plugins’ menu in WordPress.
3. Go to WooCommerce > Settings > General to configure the plugin.

== Documentation & support ==

For help setting up and configuring the extension please refer to our [user guide](https://docs.woocommerce.com/document/woocommerce-instagram/).

== Changelog ==

= 3.3.0 July 20, 2020 =
* Feature - Include the Google product category in the product catalogs.
* Tweak - Tested compatibility with WC 4.3.
* Fix - Fixed the values of the product brand and condition for product variations in the catalogs.

= 3.2.0 June 15, 2020 =
* Feature - Include all the product images in the catalog.
* Fix - Fixed the default value of the field used as the product description in the product catalogs.

= 3.1.1 June 1, 2020 =
* Tweak - Strip HTML tags from the product description.
* Tweak - Tested compatibility with WC 4.2.

= 3.1.0 May 18, 2020 =
* Feature - Define the brand and the condition per product.
* Feature - Choose the field used as the product description.
* Tweak - Added hook to filter the product availability.
* Fix - Fixed fatal error when the product catalogs setting is an empty string.
* Dev - Modified template `single-product/instagram.php`.

= 3.0.2 April 16, 2020 =
* Tweak - Tested compatibility with WC 4.1.
* Fix - Fixed error 404 when loading the product catalog feed for sites with a subdirectory in their URL.

= 3.0.1 March 25, 2020 =
* Tweak - Use the parent description when a product variation doesn't have a description.
* Tweak - Tested compatibility with WC 4.0.
* Tweak - Tested compatibility with WP 5.4.
* Tweak - Updated styles for Storefront 2.5.5.

= 3.0.0 February 12, 2020 =
* Feature - Added support for Instagram Shopping.
* Feature - Define multiple 'Product Catalog' feeds.
* Feature - Export the product catalogs to XML and CSV.
* Tweak - Tested compatibility with WC 3.9.
* Dev - Set the minimum requirements to WP 4.4 and WC 3.2.

= 2.2.1 November 6, 2019 =
* Tweak - Tested compatibility with WP 5.3.
* Tweak - Tested compatibility with WC 3.8.

= 2.2.0 October 1, 2019 =
* Feature - Choose the type of images to display on product pages.
* Feature - Configure the type of images to display per product.
* Feature - Added tool for clearing Instagram image transients.
* Tweak - Use the top images if there are not enough recent images for a hashtag.
* Tweak - Check if the access credentials have been deprecated and it requires a re-authentication.
* Tweak - Display a notice to manually renew the access credentials when the automated process fails several times.
* Fix - Fixed default expiration time of the access credentials.
* Dev - Updated the Instagram Graph API version to v4.0.

= 2.1.1 August 5, 2019 =
* Tweak - Remove invalid characters from the product hashtag.
* Tweak - Added compatibility with WC 3.7.

= 2.1.0 May 22, 2019 =
* Feature - Automatically renew the access credentials.
* Tweak - Keep the settings when disconnecting the Instagram account or removing the plugin.
* Tweak - Remove older update notices on plugin activation.
* Tweak - Added URL verification when connecting and disconnecting the Instagram account.
* Tweak - Increased `timeout` parameter for the API requests.
* Tweak - Added compatibility with WP 5.2.
* Fix - Fixed error when passing a callable as an argument to the `empty()` function in PHP 5.4 and lower.
* Dev - Updated the Instagram Graph API version to v3.3.

= 2.0.1 April 5, 2019 =
* Tweak - Added compatibility with WC 3.6.

= 2.0.0 February 4, 2019 =
* Feature - Use the new Instagram Graph API.
* Feature - Customize the frontend HTML content using WooCommerce template files.
* Feature - New and more intuitive settings page.
* Tweak - Added compatibility with WC 3.5.
* Tweak - Added compatibility with WP 5.0.
* Tweak - Updated Instagram logo.
* Tweak - Check the minimum requirements before initializing the plugin.
* Tweak - Remove the user credentials when uninstalling the plugin.
* Tweak - Optionally remove all the plugin data when uninstalling it.
* Tweak - Optimized the use of the API requests.
* Tweak - Better error handling for the API requests.
* Dev - Log possible errors in the API requests.
* Dev - Rewritten the entire extension.

== Upgrade Notice ==

= 3.0 =
3.0 is a major update. It is important that you make a full site backup and ensure you have installed WC 3.2+ before upgrading.