== YITH Cost of Goods for WooCommerce ==

Contributors: yithemes
Tags: cost of goods, woocommerce, products, themes, yit, e-commerce, shop, plugins
Requires at least: 4.0.0
Tested up to: 5.4
Stable tag: 1.2.2

Licence: GPLv2 or later
Licence URI: http://www.gnu.org/licences/gpl-2.0.html
Documentation: https://docs.yithemes.com/yith-cost-of-goods-for-woocommerce


== Changelog ==

= 1.2.2 - Released on 05 May 2020 =

* New: Support to WooCommerce 4.1
* Update: updated plugin fw
* Update: updated plugin languages
* Dev: updated the plugin settings page slug
* Dev: changed the type number field in the settings
* Dev: Minor changes

= 1.2.1 - Released on 21 February 2020 =

* New: Support to WordPress 5.4 and WooCommerce 4.0
* New: added stock report total row and pagination
* Update: language file .pot
* Update: Italian language
* Update: Dutch language
* Update: updated plugin fw
* Fix: added the percentage marging to the totals row
* Fix: fixed a division by zero warning
* Fix: fixed a wrong total margin calculation
* Dev: added a change to not display a zero if the cost is not set

= 1.2.0 - Released on 06 November 2019 =

* New: support WooCommerce 3.8
* Tweak: improve the totals in the report, now it's the table footer and not a different table
* Tweak: added some changes in the report, now the average columns show a real average between the different product prices and margins
* Tweak: now, the variations cog have more priority than the parent product
* Tweak: Improving the plugin performance and making minor changes
* Update: updated plugin fw
* Update: updated .pot
* Fix: fixed an issue with the pagination
* Fix: fixed "Call to a member function is_type() on boolean" error
* Fix: now, the variations cog is automatically updated when adding a cog to the parent product
* Fix: fixed the cog in the imported variation from the WooCommerce plugin
* Dev: added a new condition to avoid a non object warning

= 1.1.10 - Released on 05 August 2019 =

* New: support WooCommerce 3.7
* Update: updated plugin core
* Fix: fixed a wrong value in the category totals table
* Dev: minor changes

= 1.1.9 - Released on 29 May 2019 =

* Update: Updated Plugin FrameWork
* Fix: fix a wrong numeric value when product has not cost of goods
* Fix: fixed A non-numeric value encountered
* Fix: fixed an issue with the cost decimals
* Fix: fixed an issue with the totals in the category and product reports

= 1.1.8 - Released on 9 April 2019 =

* New - support to WooCommerce 3.6.0 RC 1
* Tweak: improving the currency switcher integrations
* Update: Updated Plugin FrameWork
* Fix: Fixed a non-numeric value warning
* Dev: added new filter _yith_cog_order_total_cost_display_value and _yith_cog_item_cost_display_value
* Dev: added a new condition in the product per page variable

= 1.1.7 - Released on 22 March 2019 =

* Update: Updated Plugin FrameWork
* Update: Spanish language
* Dev: added a new condition in the product per page variable

= 1.1.6 - Released on 19 February 2019 =

* Update: Updating language files
* Update: Updated Plugin FrameWork
* Fix: fixed problems with the decimals
* Fix: Fixed the stock status in the variable products


= 1.1.5 - Released on 14 January 2019 =

* Update: Updating language files
* Update: Updated Plugin FrameWork
* Dev: adding a new filter in the str_replace of the get_cost_html method
* Dev: fixing a wrong string

= 1.1.4 - Released on 10 December 2018 =

* New: support to WordPress 5.0
* New: new compatibility with YITH Name your Price
* Update: Updating language files
* Update: Updated Plugin FrameWork
* Fix: fixing issues in the stock report
* Dev: changing an option description
* Dev: changing plugin author name


= 1.1.3 - Released on 22 October 2018 =

* Update: Updating language files
* Update: Updated Plugin FrameWork
* Fix: fixing the variable products in the stock report
* Dev: changing the plugin description

= 1.1.2 - Released on 17 October 2018 =

* New: Support to WooCommerce 3.5.0
* New: Basic integration with the WPML currency switcher
* New: New option to apply cost only to the selected order
* Update: Dutch language
* Update: Updated Plugin FrameWork
* Fix: fixing an issue with the decimal values in the product table
* Fix: fixing non numeric values warnings
* Fix: Fixing some notices.
* Dev: Fixing the wp_ajax_nopriv hooks
* Dev: added filters to the from_currency and to_currency variables in WPML integration

= 1.1.1 - Released on 20 August 2018 =

* New: New option to let the admin decide the status of the orders to be displayed in the report
* New: Added a basic integration with Aelia Currency Switcher
* New: Added a new option to add a new column with the margin percentage in the report
* New: Added the order total Cost of Goods in the orders totals table
* Update: Italian language
* Update: Spanish language
* Update: Plugin framework 3.0.21
* Update: Updating plugin options
* Update: Updating the plugin data
* Fix: Change the product price caught from the order, now the product price have included the coupons
* Fix: Fixing the refunded items values
* Fix: Fixed an issue with the pagination
* Fix: Fixed a plugin string
* Fix: Fixing non numeric values
* Dev: Fixing a filter name
* Dev: Changing the language file names
* Dev: Added instance in the admin premium class
* Dev: Deleting the plugin name from the text domain


= 1.1.0 - Released on 24 May 2018 =

* New: added a new pagination options in the settings
* New: Added a new option to hide the currency symbol in the reports
* Tweak: Improving the technical language of the reports
* Update: Italian language
* Update: Spanish language
* Update: Dutch language
* Update: Plugin framework 3.0.15
* Fix: Fixing the stock
* Fix: Fixed the name of the variation on the stock report
* Fix: fixing minor issues.
* Fix: adding a round to the values without currency symbol
* Fix: Fixed the item per page select.
* Dev: Hiding the item meta in the orders


= 1.0.6 - Released on 20 March 2018 =
* Update: Updating .pot
* Fix: Changing some texts strings
* Fix: Fixed a problem with the JS
* Fix: Fixing the stock report with variations.
* Dev: Added a filter in the product column value


= 1.0.5 - Released on 5 February 2018 =
* New: Add a Export CSV link in the stock report
* New: Add a button to import the cost from WooCommerce Cost of Goods


= 1.0.4 - Released on 30 January 2018 =
* Fix: Fixing the Quick Edit cost value, now don't disappear when quick edit the product
* Fix: Fixing a problem with the price that the report takes, now takes the order price, with the discount if it have one


= 1.0.3 - Released on 30 January 2018 =
* New: Support to 3.3.0-rc.2
* Fix: Now the product name is from the order, the deleted products now are showed correctly in the report


= 1.0.2 - Released on 28 January 2018 =
* Fix: Fixing Ajax problem with the apply cost buttons
* New: Spanish translations
* New: Italian translations


= 1.0.1 - Released on 22 January 2018 =
* Fix - Fixing Ajax mixed content
* New: Dutch translations


= 1.0.0 - Released on 11 January 2018 =
* First release


== Suggestions ==
If you have suggestions about how to improve YITH Cost of Goods for WooCommerce Premium, you can [write us](mailto:plugins@yithemes.com "Your Inspiration Themes") so we can bundle them into the next release of the plugin.


== Translators ==
If you have created your own language pack, or have an update for an existing one, you can send [gettext PO and MO file](http://codex.wordpress.org/Translating_WordPress "Translating WordPress")
[use](http://yithemes.com/contact/ "Your Inspiration Themes") so we can bundle it into YITH Cost of Goods for WooCommerce Premium.

 = Available Languages =
 * English
 * Dutch
 * Spanish
 * Italian
