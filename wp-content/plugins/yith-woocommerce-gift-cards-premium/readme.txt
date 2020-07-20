=== YITH WooCommerce Gift Cards Premium ===

Contributors: YIThemes
Tags: gift card, gift cards, coupon, gift, discount
Requires at least: 4.0.0
Tested up to: 5.4
Stable tag: 3.1.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Documentation: https://docs.yithemes.com/yith-woocommerce-gift-cards/

== Changelog ==

=  3.1.6 - Released on 09 July 2020 =

* New: Support for WooCommerce 4.3
* Tweak: Added new option to select the gift card delivery interval and a button to update the cron
* Update: plugin framework
* Update: plugin language
* Fix: fixed the converted balance using WPML and the gift cards as coupons
* Fix: fixed the converted amount using WPML and the gift cards as coupons
* Fix: fixed the gift card not applied when the order is edited
* Dev: move the ywgc_add_gift_card_coupons_as_negative_fees filter
* Dev: changed delivered gift card email image into PNG format
* Dev: added $context param to ywgc_custom_company_logo_url and ywgc_custom_header_image_url filters
* Dev: calculate the correct VAT amount in the checkout
* Dev: added new filter 'yith_ywgc_mpdf_directionality'
* Dev: added new action ywgc_verify_coupon_code_condition
* Dev: added new param to filter yith_ywgc_check_gift_card_return
* Dev: added the ywgc_apply_gc_code_on_gc_product condition using a gift card as coupon
* Dev: change the physical gift card info title to h5
* Dev: fix a possible warning in the email using product addons
* Dev: changed some wrong filter names
* Dev: minor changes

=  3.1.5 - Released on 02 June 2020 =

* Fix: fix a problem with the delivery date and the format MM d, yy
* Fix: fixed the tax calculation using coupons
* Dev: added new filter yith_ywgc_formatted_pdf_filename
* Dev: changed the cron interval to hourly

=  3.1.4 - Released on 18 May 2020 =

* Tweak: automatically send the preprinted gift card email when the code is created and the order updated
* Update: updated the Elementor icons
* Fix: prevent a preprinted gift card to be sent without code
* Fix: fixed a problem with the tax calculation
* Fix: added some fixes to the taxes using coupons, enable it returning true here ywgc_add_gift_card_coupons_as_negative_fees
* Dev: updated some template classes
* Dev: avoid send a gift card without delivery date
* Dev: new filter ywgc_add_gift_card_coupons_as_negative_fees

=  3.1.3 - Released on 07 May 2020 =

* New: added Greek language
* Fix: fixed when converting gift card dates to timestamp
* Fix: fixed the tax calculation in order without gift cards
* Dev: added new filter yith_ywgc_recalculate_taxes_after_cart_total
* Dev: save empty timestamp when users not enters any date. Added show_in_rest parameter to gift cards image category taxonomy to make appear in Gutenberg
* Dev: added some strings to the domain and updated the pot file

=  3.1.2 - Released on 04 May 2020 =

* New: Support for WooCommerce 4.1
* New: added new option to allow the user show or not the price in the email
* Tweak: prevent sending the "Sender notification" if the recipient is also the sender
* Update: plugin fw
* Fix: prevent null cart when using gift card codes as a coupon
* Fix: fixed the fine uploader script loaded when the upload is not enabled
* Fix: recalculate totals when a gift card is applied as a coupon and the order is updated
* Fix: fixed not converting dates to timestamp
* Dev: added classes to the h3 elements in the form
* Dev: added the coupons as a negative fees
* Dev: added ywgc_description_template_text_pdf to WPML admin strings
* Dev: minor css changes
* Dev: minor fixes

=  3.1.1 - Released on 21 April 2020 =

* Fix: wrong subtotal displayed

=  3.1.0 - Released on 20 April 2020 =

* New: added a Drag and Drop upload in the designs modal
* New: added new email to notify the sender when the gift card is delivered
* New: Added a new option to show the "gift this product" button in the shop page
* New: added new option to include additional emails in the BCC email copy
* New: added new option that allows to modify the gift card PDF file name
* New: added integration with YITH Product Addons to display the selected options in the gift card template
* New: added Polish translation
* New: added German translation
* Update: updated Spanish translation
* Update: updated Dutch translation
* Update: updated Italian translation
* Update: plugin fw and language files
* Fix: fixed issue when converting dates to timestamp with some formats
* Fix: fixed issue with the applied gift cards in the Yith Subscriptions
* Fix: fixed wrong tax calculation when a gift card were applied
* Fix: removed the gift this product section from Quick View
* Dev: removed the CSS changes in the datepicker
* Dev: Minor changes
* Dev: added new filter ywgc_recipients_array_on_create_gift_cards_for_order
* Dev: some integrations with the Enfold theme
* Dev: added new filter yith_ywgc_allow_zero_gift_cards
* Dev: added a new parameter to the hook woocommerce_add_to_cart_redirect
* Dev: change the method to load plugin textdomain in the init.php file

=  3.0.9 - Released on 25 March 2020 =

* Tweak: improved the gift card table
* Tweak: added the direct link in the panel as a copy button
* Tweak: added on/off button to change the gift card status in the table
* Tweak: added some style improves
* Update: plugin framework
* Fix: fixed the wrong tax calculation when a gift card is applied
* Fix: fixed timestamp convertion issue
* Fix: fixed an issue with the expiration and send date updates
* Fix: the framework js was not loading in the table
* Fix: apply discount when entering a manual amount
* Fix: fixed a problem with the WPML currency switcher in the cart
* Fix: fixed the value converted by WPML when the gift card is created from order
* Dev: new filter 'yith_ywgc_display_code_order_details'
* Dev: new filter 'yith_ywgc_cart_product_title'
* Dev: new filter ywgc_remove_gallery_metabox_condition
* Dev: display gift card message with multiple lines
* Dev: fix non minified JS
* Dev: added "show_in_rest" param to the CPT
* Dev: Deleting comment from convert_to_user_currency wpml function

=  3.0.8 - Released on 06 March 2020 =

* New: added plugin shortcodes to the Elementor panel
* Fix: fixed an error with the non existing product object
* Dev: all strings escaped

=  3.0.7 - Released on 02 March 2020 =

* Tweak: display the pre-printed gift cards as a new label in the gift card table
* Fix: fixed a warning when saving a post meta
* Fix: fixed and issue in the presets template, global post deleted from the template

=  3.0.6 - Released on 27 February 2020 =

* New: Support for WordPress 5.4
* New: Support for WooCommerce 4.0
* New: added new shortcode yith_gift_cards_user_table to display the current user gift card table
* Tweak: added the categories metabox in the gift card products
* Tweak: Included the expiration date and the customer email in the smart coupons convertion
* Update: updated MPDF to 8.0.5
* Update: updated .pot
* Update: plugin framework
* Update: Spanish language
* Update: Italian language
* Fix: fixed a gift card not generated with gift this product
* Fix: fixed a little issue in the settings menu style
* Fix: fixed the conversion of the dates that are saved manually in the gift cards panel
* Fix: fixed Aelia integration when there are more than two gift cards in the order
* Fix: added a plugin-fw dep to hide disable code generation field
* Fix: fixed an issue with the gift this product add to cart from email button
* Fix: fixed wrong options names to show shop logo before/after the gift card image correctly
* Fix: fixed the non displayed amount button if only one was defined
* Fix: fixed the possibility of add a gift card as coupon even if it's disabled or deleted
* Fix: show manage stock field after saving a gift card
* Dev: minor css fix
* Dev: minor changes
* Dev: new filter 'yith_wcgc_admin_tabs_control'
* Dev: added new condition to display the gift this product form directly
* Dev: added some string changes and translations
* Dev: added a check in the product-suggestion template
* Dev: check if ywgc_data is an object in the JS
* Dev: do not display arguments in the gift this product link if they are not needed
* Dev: removed compatibilities with WooCommerce versions lower to 3.3.0
* Dev: added an array check in the gift card presets template
* Dev: delete the https in the template image
* Dev: deleted Emogrifier class load


=  3.0.5 - Released on 15 January 2020 =

* New: added the product suggestion also in the PDF
* Tweak: added the placeholder {order_id} in the gift card email subject
* Tweak: Added a message under the recipient details when the recipient details are not mandatory
* Tweak: improvements in the frontend styles
* Tweak: improvements in the multi-recipient feature
* Tweak: now, the disabled options by dependencies are hidden in the plugin settings
* Tweak: Changes in the modal upload section
* Update: updated .pot file
* Fix: fixed the gift card delivery date changed when update the gift card post
* Fix: fixed undefined wrapper class error
* Fix: fixed the disabled gift this product in the bundle products
* Dev: replacing the date method by date_i18n to get the date in the correct language
* Dev: minor changes


=  3.0.4 - Released on 31 December 2019 =

* New: support for WooCommerce 3.9
* Tweak: when it is the "gift this product" email, now only display the suggested product button, to avoid confusion with the default gift card button
* Tweak: changes in the gift card template
* Tweak: improve function ywgc_custom_condition_to_create_gift_card_call_back
* Update: plugin framework
* Fix: fixed Flatsome integration when parent theme is Flatsome
* Fix: fixed a possible issue with the preview currency symbol
* Fix: fixed the discount message not displayed in the physical gift cards
* Fix: changed the method to call the gift card form in cart and checkout
* Fix: fixed a string in the gift cards menu of my account to get it correctly in the language file
* Fix: removing Date column only from Gift Cards post type table
* Dev: new hook for the gift card template 'yith_wcgc_template_before_logo'

=  3.0.3 - Released on 16 December 2019 =

* New: Support to WooCommerce 3.9 beta 1
* New: Added new option in the general settings to avoid the use of gift card codes on the gift card products
* New: added new option to allow Shop Manager to manage the plugin settings
* Tweak: added styles to the display form shortcode to fit with Elementor
* Update: updated .pot
* Update: updated Spanish language
* Update: French language (thanks to Christophe Ferrandon)
* Update: updated plugin fw
* Fix: fixed the custom image not displayed when the gallery is disabled
* Fix: fixed minor issues
* Fix: fixed the selected image not displayed as the product image
* Fix: fixed an issue with the cart image
* Fix: display the values in the shortcodes as prices
* Fix: fixed the email width issue
* Fix: now is not possible to enter the same code in gift card field and in the coupons one
* Fix: fixed warning when apply the gift card using a link
* Fix: fixed minor issue in the cart message
* Fix: fixed a null object error
* Fix: added some strings to the domain
* Fix: fixed an issue with the popup close button
* Dev: changed the product name by the product title in the form preview
* Dev: added the add gift button on gift this product translatable
* Dev: minor changes in the admin settings
* Remove: removed the view button in the gift card taxonomy

=  3.0.2 - Released on 03 December 2019 =

* Tweak: fixed an string of the gift card template
* Update: language file
* Update: plugin framework
* Update: Spanish language
* Update: Italian language
* Update: Dutch language
* Fix: added the notify customer email option
* Fix: fixed a warning array_merge in frontend side
* Fix: fixed issue with the percentage coupons
* Fix: fixed some translatable strings in plugin options
* Dev: new hooks 'yith_ywgc_before_choose_design_section' and 'yith_ywgc_after_choose_design_section'
* Dev: added trigger in the "gift as product" link
* Dev: new filter 'yith_ywgc_gift_this_product_title_message'
* Dev: JS minified

=  3.0.1 - Released on 25 November 2019 =

* New: added upload section in the design modal
* Fix: added recipient name for physical gift cards
* Fix: removed the gift this product button still showing in the shop page
* Fix: fixing the preview not updated properly on the manual amount
* Fix: minor issues
* Dev: new filter 'yith_wcgc_show_in_menu_cpt'
* Dev: changed the filter names for each list of options
* Dev: added the settings strings to the WPML track

=  3.0.0 - Released on 25 November 2019 =

* New: Added new option to allow the gift card codes to be used in the default coupons fields
* New: New gift card template
* New: New design gallery in the gift card products and gift this product feature
* New: added a QR code in the gift card template to apply automatically the gift card on scan.
* New: Added new options to customize the plugin templates
* New: added a new option in the gift card products to select an expiration date
* New: added a new option to let the customer select a delivery time for the postponed gift cards
* Tweak: new appearance of the gift card in the product page
* Tweak: new appearance of the gift this product feature
* Tweak: Now, the gift cards panel is inside the plugin settings
* Tweak: Now, the gift cards categories panel is inside the plugin settings
* Tweak: updated the plugin settings with a new style
* Tweak: removed prettyPhoto, now the modals use a custom code
* Tweak: updated the main gift card image
* Update: Updated plugin Framework
* Dev: the plugin templates gift-card-template.php and gift-pdf-card-template.php are now merged in ywgc-gift-card-template.php
* Dev: the new template hooks are the onews from gift-card-template.php, adding an extra parameter (context) with the values email or pdf
* Remove: Removed template gift-card-template.php
* Remove: Removed template gift-pdf-card-template.php
* Remove: Removed template physical-gift-card-generator.php
* Remove: Removed all hooks from the removed templates
* Remove: Removed the gift this product button in the shop catalog, it will be included again in the next big update


=  2.3.2 - Released on 19 November 2019 =

Fix: added new condition to avoid fatal error getting the product name in the emails

=  2.3.1 - Released on 14 November 2019 =

* New: support to WooCommerce 3.8
* New: support to WordPress 5.3

= 2.3.0 - Released on 05 August 2019 =

* New: support to WooCommerce 3.7.0
* New: added new shortcode "yith_gift_card_check_balance_form" with a form to check the gift card balance by code
* New: added new shortcode "yith_redeem_gift_card_form" to allow the manually redeem of the gift cards
* New: new feature to add a percentage discount to the gift card amount
* New: added a new column in the gift card table with the direct link to auto apply the gift card in the site
* New: added new filters ywgc_is_postdated_delivery_date_by_default, ywgc_postdated_by_default, ywgc_delivery_date_value_by_default
* New: added filter yith_ywgc_check_subscription_product_on_cart
* New: added filter ywgc-recipient-email
* Tweak: Virtual recipient details is mandatory
* Tweak: now get correct gift card image for other gift card language (wpml)
* Tweak: prevent load unnecessary data on plugin load
* Tweak: now, the expiration date of the gift card could be edited
* Update: Deutsch language files
* Update: Spanish language files
* Update: Italian language files
* Update: updated .pot
* Update: updated plugin fw
* Fix: fixed PHP Notice: Undefined property: stdClass::$delete_posts
* Fix: restore the gift card balance when the order changes to failed status
* Fix: avoid to recalculate the order total if the order don't have a gift card applied
* Fix: fixed an undefined variable
* Fix: removed the image error when select another image
* Dev: changes in the mpdf args filter
* Dev: added a new font to allow the thai characters
* Dev: added new parameters to the template price filters
* Dev: added new filter ywgc_temp_coupon_array to allow changes in the temp coupon array

= 2.2.5 - Released on 21 May 2019 =

* New: support to WordPress 5.2
* New: added a new option to disable the gift this product feature on specific products
* Tweak : avoid to apply gift cards when a WooCommerce Subscription product is in the cart
* Tweak : now, the gift cards appear in the account of the recipient user
* Update: updated Plugin Framework
* Update: Updated language file
* Update: Updated Italian translation
* Fix: display the manage stock checkbox in the product inventory tab
* Fix: fixed the lost image in the gift card template when the admin switch manually a physical gift card to virtual
* Fix: avoid the remove of the main recipient fields
* Fix: fixed the issue with the WoodMart sticky section
* Fix: fixed an issue with the gift card from the free version
* Fix: fixing an issue with the gift this product in variable products
* Dev: deleting error log
* Dev: deleting a WAITING string when the product image is loaded in the gift card template
* Dev: Added new admin texts to translate emails strings
* Dev: check if the hidden gift this product is gift card type
* Dev: added plugin-upgrade submodule

= 2.2.4 - Released on 09 April 2019 =

* New: support to WooCommerce 3.6.0 RC 1
* Tweak : Improve current date to timestamp conversion
* Update: updated Plugin Framework
* Fix: fixed Undefined index: yit_metaboxes
* Fix: fixed the z-index of the datepicker
* Dev: added a request to allow the custom add to cart url
* Dev: added a condition to check if the var is an object
* Dev: Added a condition to avoid if a gift card is a present.

= 2.2.3 - Released on 27 March 2019 =

* Tweak: Changed 'yith_ywgc_give_product_as_present' apply filter position in the condition.
* Tweak: Changed private to public function to notify when a gift card is used.
* Tweak: added the delivery data in the order info on my account
* Update: updated .pot file
* Update: updated Plugin Framework
* Update: updated FR .po and .mo
* Update: Updated Spanish translation
* Fix: fixed a string on German
* Fix: Fixed today time compare with delivery date time.
* Dev: Added new filter 'yith_ywgc_gift_card_coupon_message'.
* Dev: added new filter to the formatted price on email and pdf, yith_wcgc_email_template_formatted_price & yith_wcgc_pdf_template_formatted_price
* Dev: added new param to the yith_ywgc_apply_gift_card_discount_before_cart_total hook
* Dev: added new filter ywgc_design_section_customize
* Dev: Added new parameters in a filter and action of gift card template.


= 2.2.2 - Released on 19 February 2019 =

* New: added new shortcode yith_ywgc_display_gift_card_form to display the gift card form
* Update: updated .pot file
* Update: updated Plugin Framework
* Update: Updated Dutch translation
* Fix: PayPal issue with shipping costs
* Fix: fixed the date picker format
* Fix: fixing a string
* Dev: added a new param to a function to avoid warnings
* Dev: added new filter yith_gift_cards_template_amount_title
* Dev: Default value for email trigger args

= 2.2.1 - Released on 16 January 2019 =

* Fix:  include all gift cards in conprocess of gift card dates

= 2.2.0 - Released on 15 January 2019 =

* New: added option to enable and disable the currency switcher integrations
* Tweak: choose date using a date picker when create a gift card at backend
* Tweak: new status "not sent" for gift card which have not been sent yet
* Tweak: disable autocomplete of the field "Delivery Date" on frontend
* Tweak: created procedure of backup before to proceed with the update of delivery date postmeta
* Tweak: display the currency symbol when the manual amount is zero
* Tweak: added a new icon in the "Add recipients" link
* Update: updated language file .pot
* Update: updated Spanish language file
* Update: updated Dutch language file
* Update: updated the Smart Coupons integration settings description.
* Update: updated WPML config
* Fix: fixed issue adding physical product and virtual gift cards
* Fix: fixed the date format conflict with the send delayed gift cards method
* Fix: fixed wrong usage of variable ywgc_data
* Fix: expiration date set to unlimited always
* Fix: fixed problems with the French translation
* Dev: added a filter to the general settings options yith_ywgc_general_options_array
* Dev: added new filter yith_ywgc_apply_gift_card_discount_before_cart_total
* Dev: added new filter to the cron job hour yith_ywgc_send_scheduled_gift_cards_hour
* Dev: added new filter yith_gift_cards_format_number_of_decimals


= 2.1.1 - Released on 17 December 2018 =

* New: option to prevent purchasing of virtual gift card and physical products in the same order
* Update: Updated .pot file
* Update: italian language file
* Update: Spanish language file
* Update: updating and fixing some string in Dutch
* Fix: prevent notice when date is not an object of DateTime
* Fix: string "Choose the delivery date" is not translatable
* Fix: notice undefined index 'ywgc_amount'
* Fix: fixing an issue with the gift card codes
* Fix: chaging postponed delivery placeholder
* Dev: new hook 'ywgc_before_sent_email_gift_card_notification'
* Dev: updating a string
* Dev: new filter 'yith_wcgc_set_gift_card_as_sent'


= 2.1.0 - Released on 05 December 2018 =

* New: support to WordPress 5.0-RC3
* New: added a new option to select the date format in the plugin
* New: shortcode "yith_wcgc_show_gift_card_list" to print the gift card list
* New: added a new option to disable the "Click here for the discount" button in the email if needed
* Tweak: custom filter to create gift card. No gift card created for processing status with Cash on delivery method
* Tweak: now the add to cart message when the product is added as a gift shows the product name
* Tweak: now the gift this product buttons in the shop are not shown in out of stock products
* Tweak: filter to modify the arguments to display design categories
* Tweak: changed admin setting text
* Tweak: new settings to display email button on gift card code email sent
* Update: Updated Spanish language
* Update: updated German .po file
* Update: updated plugin FW to 3.1.5
* Fix: prevent js error if ywgc_data is not defined
* Fix: prevent js errors when variables are not set
* Fix: Fixed error in the automatic discount template
* Fix: allow displaying message break lines on the gift card page
* Fix: delete backend link in the gift card code when an order is autocompleted
* Fix: Fixed some errors with the empty() method and the get_option
* Dev: new filter 'yith_ywgc_show_all_design_text' for "Show all designs"
* Dev: new filter 'yith_wcgc_deny_gift_card_email'
* Dev: filter to stop sending gift card code email by default
* Dev: new filter 'yith_wcgc_force_generate_manually_code_always'
* Dev: added a new hook yith_ywgc_after_gift_card_generation_save
* Dev: added some product checks
* Dev: added the parameter $context to the filter ywgc_display_price_template
* Dev: change a variable
* Dev: duplicate an action with different params
* Dev: added a new filter to the default email subject text
* Dev: changed some strings in the smart coupons transfer option
* Dev: added a new condition to the unlink files
* Dev: added WPML config file
* Dev: added new condition to check the gift card title
* Dev: added a new filter ywgc_email_notify_customer_recipient_email
* Dev: fixed a non-numeric value warning
* Tweak: filters to hide price on gift card email sending and PDF
* Tweak: different email for BCC gift card code email


= 2.0.5 - Released on 23 October 2018 =

* Update: plugin framework
* Update: plugin description
* Update: plugin links
* Update: updating language files
* Update: Updating plugin description
* Update: italian language
* Dev: added new filter yith_ywgc_pdf_new_file_path

= 2.0.4 - Released on 17 October 2018 =

* New: Support to WooCommerce 3.5.0
* New:  Basic integration with WooCommerce Smart Coupons
* Tweak: Allow to use the coupons form with a gift card code activating a filter
* Tweak: Aelia compatibility get the order total in actual currency
* Tweak: new action links and plugin row meta in admin manage plugins page
* Tweak: customize the button label for the gift card email and show it always even without applying disscount
* Update: Updated the language files
* Update: updated the Norwegian language, thanks to Jørgen Eggli.
* Update: Updated the plugin-FW
* Fix: email button add to cart automatically for non gift this product
* Fix: not show "x" image when logo is not selected
* Fix: fixing a string error
* Dev: Added new filter yith_ywgc_email_automatic_discount_text
* Dev: added a new action yith_ywgc_before_disallow_gift_cards_with_same_title_query
* Dev: added filter yith_ywgc_gift_card_orders_total
* Dev: added new arg to the yith_wcgc_template_after_message filter
* Dev: check if is_array, prevent errors with php "Count" function
* Dev: added a filter 'ywgc_empty_recipient_note'
* Dev: filters for images on the pdf
* Dev: new filter yith_wcgc_gift_card_details_mandatory_recipient
* Dev: javascript minify of frontend
* Dev: filter to display gift_amounts as a select2 of jquery

= 2.0.3 - Released on 19 September 2018 =

* Tweak: Compatibility with new Aelia (4.6.5.180828)
* Tweak: Set gift card custom post type as exportable
* Tweak: filter to customize the "add to card" of the gift card
* Tweak: text changed for better understanding
* Tweak: Manual amount option displayed even with only one gift card price
* Update: .pot main language file
* Update: Spanish translation
* Update: Dutch language
* Update: Italian language
* Fix: Add price with WooCommerce configuration taxes to the cart
* Dev: remove pdfs after sending
* Dev: Race conditions - gift card duplicated
* Dev: adding admin panel javascript minimized

= 2.0.2 - Released on 04 September 2018 =

* New: New options for the form to apply the gift card code on the cart and checkout page
* Dev: gift this product on PHP Unit

= 2.0.1 - Released on 03 September 2018 =

* New: Allow buyers to receive a BCC email with the gift card code
* New: pdf attached to the gift card code email
* Tweak: Check if $product exist for prevent fatal error
* Tweak: Hide default gift card product on the admin products
* Tweak: Display parent image if variation product does not have image
* Tweak: Adding the gif loader when choosing image to gift
* Update: Dutch translation
* Fix: Displaying product image for variable products
* Fix: add the customize label for "Gift this product" for variable products
* Fix: fixing wrong string
* Dev: comment of how to activate the internal note in the list of gift cards
* Dev: cleaning error_log code
* Dev: Removing fonts of mpdf
* Dev: added a new filter in the check gift card return
* Dev: displaying notes column on the list of Gift Cards

= 2.0.0 - Released on 09 August 2018 =

* New: Settings Admin panel
* Tweak: plugin action links and row metas
* Tweak: prevent error with gift this product button on shop loop
* Tweak: added new status to the gift card table
* Tweak: Check the recipient names as array
* Update: Spanish translation
* Update: Dutch translation
* Updated: official documentation url of the plugin
* Fix: Fixed a issue with the AJAX Call in Frontend
* Fix: string gft to gift on privacy policy content
* Fix: Change the hook of the gift this product button in variable products
* Dev: adding the product to the gift this product template
* Dev: fixed the filter to the variation products with gift this product button
* Dev: checking YITH_Privacy_Plugin_Abstract for old plugin-fw versions
* Dev: adding new filters in the checkout order tables
* Dev: PHPUnit

= 1.8.5 - Released on 28 May 2018 =

* GDPR:
   - New: exporting user question and answers data info
   - New: erasing user question and answers data info
   - New: privacy policy content

= 1.8.4 - Released on 12 February 2018 =

* New: added a filter to register the script URL
* Fix: hide select in case of single amount in product page
* Fix: error when try to upload an image and the limit is 0
* Dev: added a new filter in to the shop url in email button

= 1.8.3 - Released on 07 February 2018 =

Fix: Aelia compatibility when adding to the cart selecting amount of a gift card and gift this product
Fix: Wrong calculation on shipping total row
Tweak: checking if the gift card is object

= 1.8.2 - Released on 29 January 2018 =
New: support to WooCommerce 3.3-RC2
New: show gift card code in order detail page inside my account section
New: plugin fw 3.0.10
Update: french language
Tweak: Adding 'use product image' for virtual gift cards
Dev: new filter 'ywgc_remove_gift_card_text'
Dev: new filter 'ywgc_checkout_enter_code_text'
Fix: fatal error showing quantity table with Dynamic Pricing and Discounts plugin
Fix: fatal error get_default_subject (for WooCommerce minor of 3.1)


= 1.8.1 - Released on 21 Dic 2017 =

* New: Apply filters yith_ywgc_give_product_as_present
* Update: Plugin core framework 3
* Update: French language files
* Dev: filter yith_ywgc_preset_image_size
* Dev: added second argument $args on filter yith_ywgc_email_automatic_cart_discount_url
* Dev: Create YWGC_META_GIFT_CARD_CODE for a order created
* Dev: new filter "yith_ywgc_update_totals_calculate_taxes"
* Dev: override method get_subject() inside class YITH_YWGC_Email_Send_Gift_Card
* Fix: PayPal error when gift discount can’t pay total shipping costs
* Fix: use the featured image when no image selected
* Fix: usage of nl2br() function to show the gift card message
* Fix: subscription totals
* Fix: Compatibility with YITH WooCommerce Subscription Premium
* Fix: error_log in code
* Fix: Compatibility Aelia: Converting the price in the gift cards of my account
* Fix: Remove “selected more than one recipient” for Safari

= 1.8.0 - Released on 23 November 2017 =

* New: Adding recipient details to physical gift cards
* Fix: Avoid new gift cards with the same name
* Update: Dutch translations
* Fix: Gift card default hidden product is indexed by Google

= 1.7.13 - Released on 20 November 2017 =

* New: Norwegian translations thanks to Rune Kristoffersen
* Fix: Coupons behaviour

= 1.7.12 - Released on 16 November 2017 =

* Fix: Coupons not correctly applied with gift cards in cart and checkout
* New: Partial French translations thanks to Yaël Fazy
* Fix: Warning on class-yith-ywgc-backend-premium.php with some php configuration


= 1.7.11 - Released on 15 November 2017 =

* Fix: PHP Fatal error "Call to undefined method WC_Cart::get_shipping_tax()"


= 1.7.10 - Released on 15 November 2017 =

* New: German translation (thanks to Wolfgang Männel)
* New: Dutch translation
* Dev: new filter 'ywgc_email_image_size'
* Fix: gift card discount is not calculate correctly
* Fix: scroll on choose design modal window


= 1.7.9 - Released on 10 November 2017 =
* Fix: shipping totals not included in gift card discount

= 1.7.8 - Released on 09 November 2017 =
* Tweak: My Account message when no gift cards found
* Fix: Zero amount display for zero balance gift cards
* Fix: Empty 'used' tab in Gift Cards backend panel
* Fix: Gift cards not applied to shipping taxes
* Fix: Error message when try to use a zero balance gift card

= 1.7.7 - Released on 08 November 2017 =
* New: gift-card endpoint to my-account area
* Update: Moved Gift Cards from my-account dashboard to the new gift-card area
* Fix: Pretty Photo modal position on Gift Card product page
* Fix: Allowing the administrator to use the product image when gifting a product
* Update: Spanish language files


= 1.7.6 - Released on 08 November 2017 =
* Add: filter ywgc_preview_code_title
* Add: filter ywgc_design_section_title
* Add: filter ywgc_checkout_box_title
* Add: filter ywgc_checkout_box_placeholder
* Add: filter ywgc_checkout_apply_code
* Fix: Zero amount warning for current balance on PHP 7.1.X

= 1.7.5 - Released on 07 November 2017 =
* Fix: field "name" not showed creating manually a new gift card
* Fix: missed string in yith-woocommerce-gift-cards.pot file
* Update: italian language file
* Update: spanish language file

= 1.7.4 - Released on 27 October 2017 =
* Fix: Zero amount displayed in gift cards with multiple amounts

= 1.7.3 - Released on 26 October 2017 =
* Fix: wrong amount added to cart in case of thousands

= 1.7.2 - Released on 25 October 2017 =
* Fix: price format gift card
* Fix: unminified js file was not updated

= 1.7.1 - Released on 24 October 2017 =
* New: use product image as gift card image
* Dev: new filter 'yith_ywcgc_attachment_image_url'
* Fix: custom image is not showed on gift card

= 1.7.0 - Released on 17 October 2017 =

* New: support to WooCommerce 3.2.1
* Updated: language files
* Fix: shop page URL link to get auto discount button
* Fix: gift card code pattern is not set by default
* Fix: Php warning and notices on canceled order without gift cards applied if paying with 2checkout
* Fix: default gift card image is not recovered correctly
* Fix: PayPal error if the shipping total is higher than cart subtotal
* Fix: issue layout when removing a coupon
* Fix: gift card expiration is not set for gift card created manually
* Fix: same name for multiple recipient emails
* Dev: new hook 'yith_wcgc_template_after_logo'
* Dev: new hook 'yith_wcgc_template_after_main_image'
* Dev: new hook 'yith_wcgc_template_after_amount'
* Dev: new hook 'yith_wcgc_template_after_code'
* Dev: new hook 'yith_wcgc_template_after_message'
* Tweak: scheduling cron to check scheduled gift card

= 1.6.18 - Released on 17 October 2017 =

* New: Add button 'use product image' for gift this product

= 1.6.17 - Released on 16 October 2017 =

* New: Add email and name for each recipient in virtual gift

= 1.6.16 - Released on 19 September 2017 =

* New: "expiration date" column in gift card table
* Tweak: button position inside product detail page
* Fix: incorrect gift card amount if all variations have same price and no variation is set as default
* Dev: add $gift_card as parameter for filter 'yith_ywgc_gift_card_email_expiration_message'
* Dev: new filter 'yith_wcgc_date_format'


= 1.6.15 - Released on 12 September 2017 =

* Fix: wrong order total when the order is saved
* Fix: incorrect gift card amount if all variations have same price
* Dev: new filter ywgc_amount_order_total_item
* Dev: new filter 'ywgc_sender_name_label'
* Dev: new filter 'ywgc_sender_name_value'
* Dev: new filter 'ywgc_edit_message_label'
* Dev: new filter 'ywgc_edit_message_placeholder'
* Dev: new filter 'ywgc_postdated_field_label'
* Dev: new filter 'ywgc_choose_delivery_date_placeholder'
* Dev: new filter 'ywgc_cancel_gift_card_button_text'

= 1.6.14 - Released on 07 September 2017 =

* Fix: update gift card balance when order status is cancelled or refunded


= 1.6.13 - Released on 31 August 2017 =

* Fix: order item meta warning
* New: filter 'yith_wcgc_gift_this_product_button_label'
* New: filter 'yith_wcgc_manual_amount_input_placeholder'
* New: filter 'yith_wcgc_manual_amount_option_text'


= 1.6.12 - Released on 24 August 2017 =

* Tweak: order item meta is shown as string
* Update: language files

= 1.6.11 - Released on 09 August 2017 =

* Add: Filters on Gift Card Post Type admin page

= 1.6.10 - Released on 09 August 2017 =

* Fix: wrong amount shown in cart page when using Aelia Currency Switcher plugin
* Fix: 'Add another recipient' string not shonw in gift card page due a typo in gift-card-details.php

= 1.6.9 - Released on 03 July 2017 =

* New: Support to WooCommerce 3.1
* Fix: disable 'gift this product' button when a variation is not selected.

= 1.6.8 - Released on 19 June 2017 =

* Update: improved the layout of gift this product section for variable products.
* Fix: wrong CSS prop set in ywgc-frontend.js.
* Fix: gift card image is shown and then hide on product's page when 'Gift this product' option is enabled.

= 1.6.7 - Released on 19 May 2017 =

* New: added 'D' placeholder in gift card code pattern, it will replaced by a digit in place of a random letter.
* Fix: missing amount conwith Aelia Currency Switcher and WooCommerce 3.
* Fix: prevent a Javascript error when WooCommerce trigger 'found_variation' and the variation object is not set.
* Fix: avoid a fatal error when activating the plugin on website with PHP prior than 5.4.

= 1.6.6 - Released on 08 May 2017 =

* Fix: 'choose design' button does nothing when clicked, showing the modal window when clicked again.
* Fix: 'choose design' not visible when using the 'gift this product' feature.
* Tweak: improved layout for preset design modal window.

= 1.6.5 - Released on 27 April 2017 =

* Update: plugin language file.
* Tweak: improved the gift card layout.
* Fix: 'Add gift' text not localizable.
* Fix: billing_first_name property called directly in WooCommerce 3.

= 1.6.4 - Released on 21 April 2017 =

* Update: plugin-fw
* Fix: using 'Gift this product' feature do not add the product to the cart due to product not purchasable.
* Fix: fatal error due to huge amount of post meta

= 1.6.3 - Released on 14 April 2017 =

* Fix: 'missing recipient' notice shown when purchasing a physical gift card.
* Fix: broken compatibility with Aelia Currency Switcher.
* Fix: usage notification not sent to the buyer in case of physical gift card.
* Fix: prevent email sending duplicates.
* Dev: use 'ywgc-reset' query string var in order to recover the default gift card product.

= 1.6.2 - Released on 11 April 2017 =

* New: compatible with both WPML "Product Translation Interface" mode.
* New: template /single-product/add-to-cart/gift-card-add-to-cart.php.
* Update: language files.
* Tweak: purchasable status depends on gift card amounts and to the manual amount status.
* Tweak: gift card template not shown on gift card product page if the product is not purchasable.
* Fix: digital gift card shown as physical product on back end when used with WooCommerce 3.0 or newer.

= 1.6.1 - Released on 28 March 2017 =

* Fix: removed 'debugger' on front end script
* Fix: updated minified front end script.

= 1.6.0 - Released on 27 March 2017 =

* New: Support WooCommerce 3.0
* New: apply gift card to the cart totals after applying coupon and calculate taxes.
* New: register gift cards usage as order notes.
* New: set your own pattern for the gift card code to generate.
* New: if the gift card code pattern is not so complex to allow unique code, the gift card code will be created but will be not valid until a manual code is entered.
* New: avoid unique code conflicts for new or edited gift cards
* Remove: option to choose if gift card discount should applies to shipping fee as it is the default behavior.
* Remove: gift card code can no longer be entered in the coupon field.
* Remove: gift cards are no more linked to WooCommerce coupon system.
* Fix: YITH Plugin Framework initialization.
* Fix: gift card usage notification not sent to the buyer

= 1.5.16 - Released on 17 February 2017 =

* New: add internal notes to gift cards and show them on gift cards table.
* Fix: notice shown when 'free shipping' is the selected shipping method.
* Fix: custom image set from edit product page not saved correctly.
* Fix: the order action for sending gift cards not triggered.
* Fix: wrong amount shown on gift card product page when using third party plugin for currency switching.
* Fix: amount not shown on admin product page when added to the gift card product, needing a page refresh.
* Dev: customize the gift card's parameters with the filter 'yith_ywgc_gift_card_coupon_data' when the gift card code is applied to the cart.

= 1.5.15 - Released on 07 February 2017 =

* Fix: scheduled gift card email sent to the recipient even if already delivered.

= 1.5.14 - Released on 07 February 2017 =

* Update: gift-card-details.php template
* Update: language files
* Tweak: in gift card product page, the 'quantity' field and 'add to cart' button are moved under the gift card area
* Fix: scheduled date for gift cards was not set correctly during the purchase.
* Fix: gift card design not enabled for products translated with WPML.
* Fix: gift card manual amount not enabled for products translated with WPML.

= 1.5.13 - Released on 02 February 2017 =

* New: show message about the expiration date in gift card email.
* Tweak: add Prettyphoto scripts in gift card product page.
* Fix: gift card amounts shown on products translated with WPML.

= 1.5.12 - Released on 01 February 2017 =

* New: show gift card expiration date in customer gift card table.
* Fix: issues with gift card expiration date.
* Fix: issues with shipping of scheduled gift cards.
* Dev: yith_ywgc_my_gift_cards_columns filter lets third-party plugins customize columns shown on customer gift card table.

= 1.5.11 - Released on 27 January 2017 =

* Fix: gift card amount ordering was not sorted correctly
* Fix: wrong image on gift cart product pages used when the user clicks on 'default image' button

= 1.5.10 - Released on 18 January 2017 =

* New: choose if the image title should be shown in design list pop-up
* Dev: the filter 'yith_ywgc_gift_cards_amounts' lets third party plugin to change the content of the amounts dropdown

= 1.5.9 - Released on 13 January 2017 =

* New: template automatic-discount.php in /templates/emails
* Tweak: footer content in emails can be customized

= 1.5.8 - Released on 03 January 2017 =

* Fixed: gift card with empty recipient email not added to the cart

= 1.5.7 - Released on 02 January 2017 =

* Added: gift cards with multi-recipient will be added to the cart one per row
* Added: choose if the recipient email should be shown in cart details

= 1.5.6 - Released on 22 December 2016 =

* Added: email format validation before adding the gift card to the cart
* Added: set gift card expiration
* Tweaked: improved cart messages when a gift card code is used
* Fixed: gift card email not send to the admin email when BCC option is set

= 1.5.5 - Released on 21 December 2016 =

* Fixed: wrong amount shown in variable products with 'Gift this product' option
* Fixed: missing currency conwith Aelia Currency Switcher
* Fixed: sender name not shown in product suggestion template
* Fixed: amount not shown in user currency in email
* Fixed: WPML currency for gift cards
* Fixed: multiple gift cards not added to the cart correctly

= 1.5.4 - Released on 15 December 2016 =

* Fixed: gift card first usage email should be sent to the customer instead of the gift card recipient
* Fixed: the real gift card image was not emailed to the recipient

= 1.5.3 - Released on 14 December 2016 =

* Added: email style can be overwritten from the theme
* Fixed: recipient email validation on gift card page

= 1.5.2 - Released on 13 December 2016 =

* Fixed: 'Send now' not working on manual created digital gift cards

= 1.5.1 - Released on 07 December 2016 =

* Added: ready for WordPress 4.7

= 1.5.0 - Released on 06 December 2016 =

* Added: gift cards can be create manually from back end
* Added: gift cards balance can be managed from back end
* Added: gift cards details can be edited from back end
* Added: create your own gift card template overwriting the plugin templates
* Added: allow inventory for gift card products
* Added: allow 'sold individually' for gift cards
* Added: choose if in digital gift cards, the recipient email is mandatory.
* Added: recipient name in gift card product template
* Updated: gift card code are generated as soon as possible after the payment of the order(in 'processing' or 'completed' order status)
* Updated: the sender name is no more mandatory when a digital gift cards is purchased
* Fixed: various issues with Aelia Currency Switcher

= 1.4.13 - Released on 18 November 2016 =

* Fixed: a console log shown when manual amount option is not set
* Updated: plugin language files

= 1.4.12 - Released on 15 November 2016 =

* Added: gift cards can be used for paying shipping cost
* Updated: improved checks on the amount entered by the customer in manual amount mode
* Updated: plugin language files
* Fixed: the link for applying the gift card to the cart directly from the cart redirect to wrong page.
* Fixed: notice not shown if WooCommerce was not installed

= 1.4.11 - Released on 03 November 2016 =

* Fixed: when using the 'gift this product' feature, the amount is not correctly set.

= 1.4.10 - Released on 02 November 2016 =

* Added: gift card amounts managed through the accounting.js script
* Updated: gift card product page layout, removing default colors and style to let the page being rendered by the theme
* Fixed: gift card amount set to 0 when in manual mode only.

= 1.4.9 - Released on 20 October 2016 =

* Fixed: backward compatibility with WooCommerce 2.6.0 or sooner: wrong product title shown when a gift card is added to the cart

= 1.4.8 - Released on 11 October 2016 =

* Updated: removed duplicated 'Add to cart' button
* Updated: the amounts dropdown is now selected on first item by default
* Updated: the preview layout on single product page
* Fixed: empty product title shown after adding a gift card to cart
* Added: spanish translation files

= 1.4.7 - Released on 27 July 2016 =

* Updated: Aelia Currency Switcher compatibility to latest plugin version
* Added: multiple BCC recipients for gift cards sold
* Added: template for gift card footer
* Added: template for gift card suggestion section

= 1.4.6 - Released on 12 July 2016 =

* Fixed: manual amount in physical product do not work
* Fixed: total not updated correctly in mini-cart

= 1.4.5 - Released on 05 July 2016 =

* Fixed: tab 'general' not visible in gift cards products after the update to WooCommerce 2.6.2

= 1.4.4 - Released on 30 June 2016 =

* Fixed: the amount shown on mini cart was not converted in current currency when using the Aelia Currency Switcher plugin

= 1.4.3 - Released on 30 June 2016 =

* Fixed: mini cart amounts not updated when a gift card is added to the cart

= 1.4.2 - Released on 28 June 2016 =

* Fixed: email footer and header not shown when using the "send now" feature

= 1.4.1 - Released on 27 June 2016 =

* Updated: do not show the amount dropdown if only manual amount is enabled

= 1.4.0 - Released on 14 June 2016 =

* Added: WooCommerce 2.6 ready
* Added: set the gift cards product as downloadable to let the payment gateway to set the order as completed when paid
* Fixed: issue that would prevent to edit a gift card when the order was in processing status
* Fixed: a warning was shown in product of type other than the gift cards due to a conflict with YITH Dynamic Pricing
* Fixed: wrong gift card object retrieved then using a numeric gift card code

= 1.3.8 - Released on 18 May 2016 =

* Updated: the form-gift-cards.php template file
* Fixed: the discount code was not applied correctly clicking on the email received

= 1.3.7 - Released on 09 May 2016 =

* Added: allow manual entered amounts for physical products
* Added: support to WPML Multiple Currency
* Added: let the vendor to manage his own gift cards when YITH Multi Vendor is active
* Added: gift card code fields could be removed from checkout page via a filter
* Fixed: wrong amount value retrieved when reading old gift cards

= 1.3.6 - Released on 05 May 2016 =

* Fixed: gift cards generated twice when used within YITH Multi Vendor plugin

= 1.3.5 - Released on 28 April 2016 =

* Added: support to WooCommerce 2.6.0 for edit product page
* Fixed: out of date get_status() function call removed from the /templates/myaccount/my-giftcards.php file
* Fixed: conflict on emails sent by the YITH WooCommerce Points and Rewards plugin

= 1.3.4 - Released on 26 April 2016 =

* Fixed: the 'alt' and 'title' attribute of the gift cards template were not localizable
* Updated: yith-woocommerce-gift-cards.pot file

= 1.3.3 - Released on 21 April 2016 =

* Fixed: the custom image chosen while purchasing a gift card was not used in the email
* Fixed: resetting the custom image from the edit product page, the featured image was not used anymore

= 1.3.2 - Released on 13 April 2016 =

* Fixed: customize gift card button not shown if 'show template' is set to false

= 1.3.1 - Released on 12 April 2016 =

* Updated: pre-printed gift cards are not sent automatically when the code is filled
* Fixed: gallery items do not load properly
* Fixed: option for show the shop logo on the gift card template not visible on plugin settings

= 1.3.0 - Released on 11 April 2016 =

* Added: create a gallery of standard design from which the customer can choose the one that best fits the festivity or recurrence for which the gift card is being purchased
* Added: new feature for shops selling pre-printed physical gift cards, you can add the code manually instead of being auto generated
* Added: new option let you use the product featured image can be used as the gift card header image
* Added: from the product edit page you can set any image from the media gallery as the gift card header image
* Added: new option let you choose if shop logo should be shown on the gift card template
* Added: you can choose between two layouts for the gift card template
* Fixed: email header not visible when using the bulk action "Order actions" from the order page

= 1.2.12 - Released on 22 March 2016 =

* Fixed: gift cards table filter fails after "send now" button pressed
* Fixed: Aelia Currency Switcher add-on, wrong currency shown on emails
* Fixed: unwanted edit link shown on gift cards email
* Fixed: standard coupon not accepted when used together with a gift card

= 1.2.11 - Released on 15 March 2016 =

* Fixed: wrong gift card value shown on gift this product for variable product

= 1.2.10 - Released on 14 March 2016 =

* Updated: on back end gift cards table page, show the sum of order totals instead of subtotals
* Fixed: duplicated orders shown on back end gift cards table page

= 1.2.9 - Released on 11 March 2016 =

* Added: new gift card status: "Dismissed" is for gift card not valid and no more usable.
* Added: Syncronization between gift cards status and order status
* Fixed: wrong calculation on gift card when a manual amount is entered
* Updated: YITH Plugin FW

= 1.2.8 - Released on 09 March 2016 =

* Added: Rich snippets for the gift card product
* Added: automatic cart discount clicking from the email received
* Deleted: yith-status-options.php file no more used

= 1.2.7 - Released on 07 March 2016 =

* Fixed: ywgc-frontend.min.js not updated to the latest version
* Added: let the customer to change the recipient of gift card, crating a new gift card with update balance
* Added: in my-account page show the order where a gift card was used
* Updated: yith-woocommerce-gift-cards.pot in /languages folder

= 1.2.6 - Released on 01 March 2016 =

* Updated: all the gift cards used by a customer are now shown on my-account page
* Added: Aelia Currency Switcher compatibility let you use gift cards in multiple currency environment

= 1.2.5 - Released on 26 February 2016 =

* Added: gift cards can be set as disabled and no discount will be applied
* Added: template myaccount/my-giftcards.php for showing gift cards balance
* Added: show balance of used gift cards in my-account page
* Fixed: coupon code section shown twice on cart page based on the theme used
* Updated: removed filter yith_woocommerce_gift_cards_empty_price_html

= 1.2.4 - Released on 11 February 2016 =

* Fixed: in cart page the "Coupon" text was not localizable.
* Fixed: the class selector for datepicker conflict with other datepicker in the page
* Fixed: adding to cart of product with "sold individually" flag set fails
* Fixed: require_once of class.ywgc-product-gift-card.php lead sometimes to "the Class 'WC_Product' not found" fatal error
* Added: compatibility with the YITH WooCommerce Points and Rewards plugin

= 1.2.3 - Released on 18 January 2016 =

* Fixed: notification email on gift card code used not delivered to the customer

= 1.2.2 - Released on 15 January 2016 =

* Added: compatibility with YITH WooCommerce Dynamic Pricing

= 1.2.1 - Released on 14 January 2016 =

* Fixed: missing parameter 2 on emails

= 1.2.0 - Released on 13 January 2016 =

* Updated: gift card code is generated only one time, even if the order status changes to 'completed' several time
* Updated: plugin ready for WooCommerce 2.5
* Updated: removed action ywgc_gift_cards_email_footer for woocommerce_email_footer on email template
* Fixed: prevent gift card message containing HTML or scripts to be rendered
* Added: resend gift card email on the resend order emails dropdown on order page

= 1.1.6 - Released on 28 December 2015 =

* Added: digital gift cards content shown on gift cards table in admin dashboard
* Added: option to force gift card code sending when automatic sending fails

= 1.1.5 - Released on 14 December 2015 =

* Fixed: YITH Plugin Framework breaks updates on WordPress multisite

= 1.1.4 - Released on 11 December 2015 =

* Fixed: manual entered text not used in emails

= 1.1.3 - Released on 08 December 2015 =

* Fixed: YIT panel script not enqueued in admin

= 1.1.2 - Released on 07 December 2015 =

* Fixed: temporary gift card tax calculation
* Updated: temporary gift card is visible on dashboard so it can be set the title and the image

= 1.1.1 - Released on November 30 2015 =

* Fixed: Emogrifier warning caused by typo in CSS
* Fixed: problem that prevent the gift card email from being sent
* Fixed: ask for a valid date when postdated delivery is checked

= 1.1.0 - Released on November 26 2015 =

* Added: optionally redirect to cart after a gift cards is added to cart
* Fixed: postdated gift cards was sent on wrong date

= 1.0.9 - Released on November 25 2015 =

* Fixed: missing function on YIT Plugin Framework
* Updated: gift cards sender and recipient details added on emails

= 1.0.8 - Released on November 24 2015 =

* Fixed: wrong gift card values generated when in WooCommerce Tax Options, prices are set as entered without tax and displayd inclusiding taxes

= 1.0.7 - Released on November 20 2015 =

* Updated: gift card price support price including or excluding taxes

= 1.0.6 - Released on November 19 2015 =

* Updated: Gift Cards object cast to array for third party compatibility

= 1.0.5 - Released on November 17 2015 =

* Fixed: tax not deducted when gift card code was used

= 1.0.4 - Released on November 13 2015 =

* Fixed: multiple gift cards code not generated

= 1.0.3 - Released on November 12 2015 =

* Added: tax class on gift card product type
* Updated: changed action used for YITH Plugin FW loading
* Updated: gift card full amount(product price plus taxes) used for cart discount

= 1.0.2 - Released on 06 November 2015 =

* Fixed: coupon conflicts at checkout

= 1.0.1 - Released on 29 October 2015 =

* Update: YITH plugin framework

= 1.0.0 - Released on 22 October 2015 =

* Initial release
