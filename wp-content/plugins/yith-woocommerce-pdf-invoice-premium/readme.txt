=== YITH WooCommerce PDF Invoices & Packing Slips ===

Contributors: yithemes
Tags: woocommerce, orders, woocommerce order, pdf, invoice, pdf invoice, delivery note, pdf invoices, automatic invoice, download, download invoice, bill order, billing, automatic billing, order invoice, billing invoice, new order, processing order, shipping list, shipping document, delivery, packing slip, transport document,  delivery, shipping, order, shop, shop invoice, customer, sell, invoices, email invoice, packing slips
Requires at least: 6.1
Tested up to: 6.3
Stable tag: 4.12.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Documentation: https://docs.yithemes.com/yith-woocommerce-pdf-invoice

== Changelog ==

= 4.12.0 - Released on 10 October 2023 =

* New: support for WooCommerce 8.2
* New: panel UI
* Update: YITH plugin framework
* Fix: URL to download the XML from the invoices table
* Fix: invoice creation date to be used with the custom templates
* Dev: round correctly the PrezzoUnitario in the XML invoice
* Dev: added new filter yith_ywpi_invoice_tax_label
* Dev: added new filter yith_ywpi_invoice_totals_label

= 4.11.0 - Released on 06 September 2023 =

* New: support for WooCommerce 8.1
* Update: YITH plugin framework
* Fix: fixed the invoice save path when the Electronic Invoice feature is enabled
* Fix: fixed issue with the packing slip save path
* Dev: minor changes in the HPOS compatibility


= 4.10.0 - Released on 17 August 2023 =

* New: support for WooCommerce 8.0
* New: support for WordPress 6.3
* New: HPOS compatibility
* Tweak: use the yith upload field for "Company logo" option by vendor side
* Update: YITH plugin framework
* Dev: minor changes

= 4.9.0 - Released on 19 July 2023 =

* New: support for WooCommerce 7.9
* Update: YITH plugin framework
* Dev: added new filter yith_ywpi_apply_old_percentage_tax_calculation_precision
* Dev: added the invoice_number placeholder in the order number block
* Dev: retrieve correctly the order refund date for the credit note
* Dev: remove the zip with the invoices after download it


= 4.8.0 - Released on 15 June 2023 =

* New: support for WooCommerce 7.8
* Tweak: integration with YITH WooCommerce Order & Shipment Tracking
* Update: YITH plugin framework
* Dev: added new action yith_pdf_invoice_after_total
* Dev: added new action yith_ywpi_after_product_image
* Dev: new filter 'yith_ywpi_packing_slip_generated_label_custom'
* Dev: added new filters yith_ywpi_packing_slip_status_label and yith_ywpi_no_packing_slip_available_label

= 4.7.0 - Released on 11 May 2023 =

* New: support for WooCommerce 7.7
* Update: YITH plugin framework
* Fix: prevent fatal error when the product has no images set
* Fix: minor fixes

= 4.6.0 - Released on 11 April 2023 =

* New: support for WooCommerce 7.6
* Update: YITH plugin framework
* Tweak: possibility to edit natura value by custom meta on order page (Italian electronic Invoice)
* Fix: fixed usage of deprecated method
* Dev: added order item object as a parameter to the yith_ywpi_column_product_after_content action
* Dev: added check to not display the proforma button if it is not generated
* Dev: minor changes

= 4.5.0 - Released on 13 March 2023 =

* New: support for WordPress 6.2
* New: support for WooCommerce 7.5
* New: order date field in electronic invoice for italian customers
* New: show order number in "DatiOrdineAcquisto" section (electronic invoice)
* Update: YITH plugin framework
* Fix: show order number for "DatiOrdineAcquisto -> IdDocumento"
* Fix: solved a fatal error when the date of the completed order is shown on the invoice
* Dev: added new filter yith_ywpi_replace_customer_details_pattern
* Dev: new filters 'yith_ywpi_get_bulk_actions_credit_note', 'yith_ywpi_get_bulk_actions_credit_notes_list_table' and 'yith_ywpi_get_bulk_actions_documents_list'
* Dev: added new filters yith_ywpi_invoice_list_table_actions, yith_ywpi_order_list_bulk_actions and yith_ywpi_show_regenerate_invoice_button
* Dev: added new filters yith_ywpi_invoices_table_order and yith_ywpi_credit_notes_table_order
* Dev: added new filter yith_ywpi_invoice_round_price
* Dev: added new filters yith_ywpi_invoice_subtotal_label and yith_ywpi_invoice_discount_label

= 4.4.0 - Released on 08 February 2023 =

* New: support for WooCommerce 7.4
* Update: YITH plugin framework
* Fix: Prevent empty meta titles to appear under the product data
* Dev: added new action yith_ywpi_before_process_meta_invoice
* Dev: added new filter yith_ywpi_invoice_totals_tax_label
* Dev: added an alternative server to get the templates

= 4.3.0 - Released on 10 January 2023 =

* New: support for WooCommerce 7.3
* Update: YITH plugin framework
* Fix: Solved an issue with the integration with POS where sometimes $product didn't exist and generated a fatal error
* Dev: added new filters yith_ywpi_billing_address_ssn_label and yith_ywpi_billing_address_vat_label
* Dev: added new filter yith_ywpi_custom_pdf_template_footer
* Dev: display the billing and shipping state name instead of the code when using the template builder
* Dev: updated Guzzle library to version 7.5.0
* Dev: minor changes

= 4.2.0 - Released on 13 December 2022 =

* New: support for WooCommerce 7.2
* Update: YITH plugin framework
* Dev: prevent a fatal error in the templates preview
* Dev: display the country name instead of the code when using the template editor
* Dev: added new filter ywpi_checkout_ssn_validation to prevent the SSN validation in the checkout
* Dev: minor changes

= 4.1.1 - Released on 15 November 2022 =

* Tweak: added button to download the invoice in the order details at checkout, if available
* Update: YITH plugin framework
* Fix: patched security vulnerability
* Dev: added new filter yith_ywpi_product_metadata

= 4.1.0 - Released on 26 October 2022 =

* New: support for WordPress 6.1
* New: support for WooCommerce 7.1
* Update: YITH plugin framework
* Dev: added additional argument to yith_ywpi_after_product_name action

= 4.0.3 - Released on 14 October 2022 =

* Fix: javascript issue on PDF template editor

= 4.0.2 - Released on 11 October 2022 =

* Update: YITH plugin framework
* Dev: change the method to select the templates, to avoid external issues

= 4.0.1 - Released on 6 October 2022 =

* Update: YITH plugin framework
* Fix: minor fixes
* Fix: remove free deactivation to avoid a fatal error

= 4.0.0 - Released on 4 October 2022 =

* New: support for WooCommerce 7.0
* New: PDF builder to create custom templates for the documents
* Update: YITH plugin framework
* Fix: fixed issue with default options

= 3.13.0 - Released on 6 September 2022 =

* New: support for WooCommerce 6.9
* Update: YITH plugin framework

= 3.12.1 - Released on 05 August 2022 =

* Fix: Solved a fatal error when creating the credit note file

= 3.12.0 - Released on 03 August 2022 =

* New: added a new set of options to control the order info on all invoices
* New: added a new option to display the payment method in the invoices
* New: support for WooCommerce 6.8
* Update: YITH plugin framework

= 3.11.1 - Released on 19 July 2022 =

* Update: YITH plugin framework
* Fix: compatibility issue with YITH EU VAT, now plugin use the VAT field id of EU VAT plugin
* Fix: invoice PDF not generating when electronic invoice enabled

= 3.11.0 - Released on 06 July 2022 =

* New: support for WooCommerce 6.7
* Update: YITH plugin framework
* Fix: display of the receipt selection at checkout
* Fix: document ID didn't increase for electronic invoice

= 3.10.1 - Released on 16 June 2022 =

* Tweak: get "reason" field from post meta for electronic invoice
* Update: YITH plugin framework
* Update: language files
* Dev: added new filter yith_ywpi_show_invoice_button_view_order

= 3.10.0 - Released on 9 June 2022 =

* New: support for WooCommerce 6.6
* Update: YITH plugin framework
* Update: language files
* Fix: warning on division by zero

= 3.9.0 - Released on 10 May 2022 =

* New: support for WordPress 6.0
* New: support for WooCommerce 6.5
* Tweak: added the shipping cost in the Credit note totals
* Update: YITH plugin framework
* Fix: issue with the Vendor invoice numbers
* Fix: issue with the invoice number in the orders panel in Frontend Manager
* Fix: wrong amount for "Prezzo Unitario" field inside credit note xml file
* Dev: adding the Italian e-invoice frontend strings in English, and adding the translation to Italian
* Dev: added new action yith_ywpi_after_product_name

= 3.8.0 - Released on 7 April 2022 =
* New: support for WooCommerce 6.4
* Update: YITH plugin framework
* Tweak: remove hidden item meta fields from invoice
* Tweak: displayed the VAT and SSN data in the customer billing info, in the order edit page
* Fix: fixed issue with the invoice number increase when the Italian invoice feature is enabled
* Dev: re-added deleted filter 'ywpi_allow_sync_to_dropbox'
* Dev: added new hook yith_ywpi_export_output_column

= 3.7.0 - Released on 10 March 2022 =
* New: support for WooCommerce 6.3
* Update: YITH plugin framework
* Fix: now the XML is generated also in the 'Generate invoices' bulk action
* Dev: new filter 'yith_wcpdi_document_pattern'
* Dev: added default invoice generation to the option
* Dev: added new filter yith_ywpi_show_tax_total_condition
* Dev: changes in the proforma management options

= 3.6.0 - Released on 14 February 2022 =
* New: support for WooCommerce 6.2
* New: added new option to disable the invoice creation when order value is zero.
* Update: YITH plugin framework
* Update: language files
* Fix: plugin version constants
* Fix: wrong value for "PrezzoUnitario" field in Xml file (Electronic invoice module for Italian Customers)
* Dev: removed filters placed in wrong place
* Dev: added a condition to the percentage tax calculation in the invoice, to avoid errors if the product was removed
* Dev: added new filter yith_ywpi_download_all_files_as_zip_condition_per_order to apply custom conditions when downloading all the invoice in a zip
* Dev: added new filter yith_ywpi_generate_credit_note_automatically
* Dev: added new method to generate the packing slip automatically and not based in the invoice generation

= 3.5.0 - Released on 05 January 2022 =

* New: support for WordPress 5.9
* New: support for WooCommerce 6.1
* Update: YITH plugin framework
* Update: language files
* Fix: avoid issue when no tax rate is configured in WooCommerce
* Fix: added international date format to improve the date formats conversion
* Dev: fixed second parameter of 'woocommerce_order_item_name' filter in credit note template
* Dev: changed default values in the document options
* Dev: Added new filter 'yith_ywpi_encode_text'

= 3.4.0 - Released on 30 November 2021 =

* New: support for WooCommerce 6.0
* Update: YITH plugin framework
* Update: language files
* Update: updated Help tab with new videos
* Fix: fixed the placeholders replacement with the credit note number
* Fix: comparison error on credit notes template
* Dev: modified the 'ywpi_modify_customer_details_content' filter and function, to allow customize the section title
* Dev: added new filter 'yith_ywpi_sanitize_document_pattern'

= 3.3.0 - Released on 02 November 2021 =

* New: support for WooCommerce 5.9
* Update: YITH plugin framework
* Update: language files
* Fix: fixed _date_paid placeholder condition checking the datetime
* Fix: fixed the documents upload to Dropbox if the option is disabled
* Fix: fixed the option to open link in a new tab on My Account page
* Dev: changed method to get customer name in the documents table
* Dev: added a default value when retrieving the data to show on documents

= 3.2.1 - Released on 08 October 2021 =

* Update: YITH plugin framework
* Fix: changed placeholder '_paid_date' to '_date_paid'
* Fix: get correct percentage tax in the invoice details

= 3.2.0 - Released on 05 October 2021 =

* New: support for WooCommerce 5.8
* Update: YITH plugin framework
* Update: Added new videos to the help tab
* Fix: fixed pagination of the last page of the table of invoices and credit notes
* Fix: fixed the CSV export when filtering by dates
* Fix: fixed invoice number when regenerating the document
* Dev: improved queries for invoices and credit notes table
* Dev: improved query when getting orders where credit notes are created
* Dev: added additional parameter to 'yith_ywpi_style_before_pdf_creation' filter
* Dev: formatted date when using _paid_date placeholder
* Dev: added offset to query to export CSV

= 3.1.1 - Released on 27 September 2021 =

* Update: YITH plugin framework
* Fix: debug info feature removed for all logged in users
* Dev: fixed an unnecessary database call
* Dev: improved queries to show invoices and credit notes in the tables

= 3.1.0 - Released on 22 September 2021 =

* New: added new option to hide or show the shipping details in the table of pro-forma invoices and invoices
* Update: YITH plugin framework
* Update: language files
* Fix: fixed dropbox access token saving
* Fix: fixed Google Drive client loading on unnecessary pages
* Dev: added new HTML element in invoice totals template
* Dev: added styles to invoices tables
* Dev: changed 'Avoid duplicate invoices' option to disabled by default.
* Dev: Improved performance to get stored invoice numbers when creating the document

= 3.0.2 - Released on 14 September 2021 =

* Fix: get correct sequential invoice number when creating the document
* Fix: get correct invoice number when regenerating the document
* Dev: Added responsive mode to the filters of the invoices and credit notes table
* Dev: Added additional checks to avoid errors
* Dev: Downgrade guzzlehttp version to 6.X to avoid issues.

= 3.0.1 - Released on 14 September 2021 =

* Fix: fixed YITH Multi Vendor integration issues.
* Dev: added some style to the invoices/credit notes table

= 3.0.0 - Released on 14 September 2021 =

* New: support for WooCommerce 5.7
* New: Added Order ID & Order Number option for invoice numbers.
* New: Added invoice and credit notes table with all order details.
* New: Added two new designs to generate the documents.
* New: Added functionality to upload documents to Google Drive.
* New: Added functionality to download all invoices and all credit notes.
* Tweak: modified default templates to improve the design.
* Tweak: code refactoring according to PHPCS.
* Tweak: Improved content and style for the templates.
* Update: YITH plugin framework.
* Update: language files.
* Update: updated admin texts on the WPML XML config.
* Dev: reorganization of files and folders ('includes' folder added).

= 2.0.33 - Released on 30 August 2021 =

* Update: YITH plugin framework
* Fix: fixed templates issues

= 2.0.32 - Released on 26 August 2021 =

* Update: YITH plugin framework
* Fix: fixed error when creating credit notes
* Fix: fixed error when creating invoices

= 2.0.31 - Released on 17 August 2021 =

* New: support for WooCommerce 5.6
* Tweak: added placeholders to receiver pec and receiver id billing fields
* Update: YITH plugin framework
* Update: language files
* Fix: fixed invoice type field not displayed in checkout
* Fix: fixed the vendor company data not displayed
* Fix: don't generate the invoices in bulk, if the order is cancelled or failed
* Dev: new filter 'ywpi_allow_sync_to_dropbox'
* Dev: changed the hook to create the invoice in the new order created

= 2.0.30 - Released on 07 July 2021 =

* New: support for WooCommerce 5.5
* New: support for WordPress 5.8
* Update: YITH plugin framework
* Update: language files
* Dev: added new filter 'ywpi_invoice_type_field_default_value'

= 2.0.29 - Released on 31 May 2021 =

* New: support for WooCommerce 5.4
* Update: YITH plugin framework
* Update: language files

= 2.0.28 - Released on 20 May 2021 =

* Update: YITH plugin framework
* Fix: fixed the increment of the progressive file id
* Fix: progressive ID on XML was wrong for orders processed in bulk
* Fix: fixed the credit note number increment
* Dev: new filter 'ywpi_show_totals_in_documents'

= 2.0.27 - Released on 12 May 2021 =

* New: support for WooCommerce 5.3
* New: added new option to only display the footer in the last page
* Update: YITH plugin framework
* Fix: field "Natura" for city Livigno
* Dev: added new filters yith_ywpdi_mpdf_footer_in_all_pages and yith_ywpdi_mpdf_footer_in_last_page
* Dev: added new hook yith_ywpi_after_write_mpdf_html_template_pdf

= 2.0.26 - Released on 15 April 2021 =

* New: added new option to configure the number of digits of the invoice number
* New: added new option to show the Credit Note amounts in positive
* Update: YITH plugin framework

= 2.0.25 - Released on 07 April 2021 =

* New: support for WooCommerce 5.2
* Update: YITH plugin framework

= 2.0.24 - Released on 24 March 2021 =

* New: added new option in the plugin settings to avoid duplication of invoice numbers
* Fix: SSN field was incorrect when generating XML invoice

= 2.0.23 - Released on 10 March 2021 =

* Update: YITH plugin framework
* Update: updated translations
* Fix: value for "NATURA" field on electronc invoice
* Fix: billing vat for not europe customers
* Dev: added an is_object check
* Dev: added new filter ywpi_show_company_data_custom_condition
* Dev: added new filter yith_ywpi_hide_bundled_items
* Dev: added new parameter to the yith_ywpdi_before_generate_template hook
* Dev: implemented a new recursive method to avoid any kind of problem with the invoice number generation
* Dev: fixed conditions to not show notes in documents if not selected

= 2.0.22 - Released on 10 February 2021 =

* New: support for WooCommerce 5.0
* Update: YITH plugin framework
* Fix: fixed the generation and attachment of the credit notes in the partially refunded orders
* Fix: id codice value for not private customer that are not italian
* Fix: value for "Natura" in according to country
* Dev: added new filter ywpi_checkout_receipt_ssn_mandatory
* Dev: loading the frontend styles only in the checkout

= 2.0.21 - Released on 30 December 2020 =

* New: Support for WooCommerce 4.9
* New: added field "NATURA" for electronic invoice
* Update: plugin framework
* Fix: fixed the mandatory SSN when it's not necessary in the Electronic Invoice
* Fix: fixed user types field layout with Proteo
* Fix: fixed conditions on electronic invoices when you have a country different than Italy and you don't have receipt option enabled
* Fix: decode html tag in XML invoice

= 2.0.20 - Released on 25 November 2020 =

* New: Support for WooCommerce 4.8
* Tweak: Display the YITH bundled items depending on the "Hide bundled items in cart and checkout" option from YITH Bundles
* Update: update plugin fw
* Fix: fixed a JS error in the checkout
* Fix: number of decimals digits used for discounts (electronic module)
* Dev: new filter 'ywpi_electronic_invoice_filename'

= 2.0.19 - Released on 04 November 2020 =

* New: Support for WooCommerce 4.7
* New: Support for WordPress 5.6
* Update: update plugin fw
* Dev: Auto select plugin enabled
* Dev: removed the .ready method from jQuery
* Dev: add a check to not include the VAT field if YITH EU VAT is installed

= 2.0.18 - Released on 01 october 2020 =

* New: Support for WooCommerce 4.6
* Update: plugin framework
* Dev: changes in the get order date method

= 2.0.17 - Released on 16 September 2020 =

* New: Support for WooCommerce 4.5
* New: Integration with YITH Frontend Manager for WooCommerce
* Update: plugin framework
* Update: language files
* Fix: fixed fatal error
* Fix: avoid displaying credit note documents in orders in My Account page if option is not enabled
* Dev: changes in the credit note with products
* Dev: added new filter yith_ywpi_validate_checkout_fields_conditions
* Dev: improved the credit note
* Dev: remove the precision in the credit note % tax
* Dev: convert state code and convert it to the state name
* Dev: optional label on checkout can be now translated
* Dev: added new filter ywpi_checkout_optional_label

= 2.0.16 - Released on 11 August 2020 =

* New: Support for WooCommerce 4.4
* New: Support for WordPress 5.5
* Tweak: make mandatory ssn only for invoices when electronic module is enabled (Italian Customers)
* Update: plugin-fw
* Fix: avoid display array and object meta values in product variations
* Fix: required ssn rule
* Fix: show phone field only if customer has a phone number associated (electronic invoice)
* Dev: replaced deprecated method get_product_from_item
* Dev: changes in the increment invoice number method
* Dev: added the gift card discount as negative

= 2.0.15 - Released on 03 Jul 2020 =

* New: Support for WooCommerce 4.3
* Tweak: validate SSN field only for private customers (Electronic Invoice)
* Update: plugin-fw
* Update: language files
* Fix: fixed displayed warnings in the plugin settings
* Fix: ssn error message showed always
* Dev: added new filter yith_ywpi_ssn_field_placeholder
* Dev: added new filter yith_ywpi_vat_field_placeholder
* Dev: changes the fee tax precision
* Dev: added new filter ywpi_invoice_information_url
* Dev: added new filter ywpi_dropbox_folder
* Dev: added the extension param in some functions

= 2.0.14 - Released on 22 May 2020 =

* New: Support for WooCommerce 4.2
* New: French translation
* Update: Language files
* Fix: fixed escaped strings in the plugin settings
* Fix: show the products in the parent order using multi vendor

= 2.0.13 - Released on 13 May 2020 =
* Fix: changed wrong field type in document date format field
* Update: plugin framework

= 2.0.12 - Released on 04 May 2020 =

* New: Support for WooCommerce 4.1
* New: added a new option in the checkout to let the customer choose between a receipt or invoice
* Fix: fatal error on "Call to undefined method WC_Product_Course::get_regular_price()"
* Fix: fixed wrong esc_html function with wp_kses_post function
* Update: plugin options updated
* Update: plugin framework
* Update: Italian language files
* Update: Spanish language files
* Update: Dutch language files
* Dev: added '#' to filename regexp pattern
* Dev: added an str_replace in the footer to change the character | by -
* Dev: new filter 'yith_ywpi_item_name_xml'

= 2.0.11 - Released on 10 March 2020 =

* New: Support for WooCommerce 4.0
* New: Support for WordPress 5.4
* New: Fields for electronic invoice xml document
* New: Options for electronic invoice
* New: Options for electronic invoice (third intermediary)
* New: A "Private/Company/Freelance" field in checkout when the electronic invoice is enabled
* New: Support for Khmer Unicode
* New: JS validation on SSN for italian customers
* Update: Plugin framework
* Update: Language .pot file
* Update: Italian translation
* Update: Updated MPDF to 8.0.5
* Fix: PEC destinatario field always shown
* Fix: Placeholder not replaced in credit notes
* Fix: Changed the method to add the footer with mpdf library
* Fix: Fixed a wrong percentage tax value in the invoice
* Dev: Added a self redirect to the regenerate button
* Dev: All string scaped

= 2.0.10 - Released on 27 December 2019 =

* New: support for WooCommerce 3.9
* Tweak: Check if rtl language before print the document
* Update: .pot file
* Update: Dutch language
* Update: Spanish language
* Dev: new filter 'ywpi_next_progressive_file_id'

= 2.0.9 - Released on 28 November 2019 =

* Tweak: option panel changes
* Tweak: enqueue frontend script only in Checkout page
* Update: updated plugin framework
* Fix: notice array to string conversion in admin order page
* Fix: prevent issue on validation of XML file (Electronic Invoice module for Italian customers)
* Fix: prevent get wrong shipping and fee taxes in the invoice details
* Fix: fixed the price for unit on invoice details template (also affected the discount percentage value)
* Dev: added action hook yith_ywpi_after_generate_template_pdf

= 2.0.8 - Released on 07 November 2019 =

* New: support for WordPress 5.3
* New: support for WooCommerce 3.8
* New: added new option to show or hide broken down taxes in the invoice summary
* Update: plugin framework
* Update: updated Dutch language
* Update: updated Italian language
* Fix: fixed product price and sale price on invoice details
* Fix: prevent to get wrong tax when there is two or more taxes.
* Dev: new filter 'yith_ywpi_get_item_price_per_unit_sale' and 'yith_ywpi_get_item_price_per_unit'

= 2.0.7 - Released on 16 October 2019 =

* Tweak: improved the method to display the prices in the invoice, now, the regular price, sale price and product prices are displayed with taxes included or excluded depending on the WooCommerce tax settings
* Tweak: improved the tax in the total table
* Update: Italian language
* Update: Spanish language
* Update: updated plugin fw
* Fix: fixed automatic document generation on the order creation
* Dev: fix warning if param is an array
* Dev: added new filter yith_ywpi_settings_panel_capability

= 2.0.6 - Released on 21 August 2019 =

* Update: updated .pot file
* Update: updated Dutch language
* Fix: fixed the automatic document generation on the order creation
* Fix: prevent warning on invoice fields in admin view
* Dev: fixed minor issue with the tax label
* Dev: added some changes in the placeholders calls

= 2.0.5 - Released on 05 August 2019 =

* New: support WooCommerce 3.7
* Update: updated Italian language
* Update: updated plugin core
* Fix: fixed the automatic packing slip generation
* Fix: fixed the bulk regenerate documents feature
* Fix: avoid additional tax rows in the total if the tax is zero
* Dev: new filter to change the VAT field name in the checkout
* Dev: fixed a warning with WC 3.7
* Dev: added a new filter ywpi_allow_attach_credit_note
* Dev: changed the method yith_get_prop to get_post_meta in the replace_customer_details_pattern method

= 2.0.4 - Released on 09 July 2019 =

* Fix: notice on undefined index
* Dev: new filter 'yith_ywpi_reset_year_invoice_number'
* Dev: new filter 'yith_ywpi_reset_year_document_note_number'
* Tweak: allow placeholders in the invoice notes
* Update: plugin fw
* Update: main language file


= 2.0.3- Released on 04 June 2019 =

* Fix: mandatory SSN field for electronic invoice module

= 2.0.2- Released on 30 May 2019 =

* Fix: hide Electronic Invoice options for vendors when the module is disabled
* Dev: new filter 'ywpi_vendor_options'

= 2.0.1- Released on 29 May 2019 =

* New: added a new option to generate automatically the packing slip for the order
* New: added new template color selectors in the plugin settings
* New: validation of SSN field during checkout process
* New: check on receiver ID (electronic Invoice)
* New: added a bulk edit to generate the invoice and the packing slip in the order page. Thanks to Morten Virik from  Mementor AS  (www.mementor.no)
* New: added a bulk edit to regenerate the invoice and the packing slip in the order page.
* New: added a credit note button on my account if available
* Tweak: delete the invoice from the upload directory when use the remove button in the order
* Tweak: use same templates for Invoice and Credit notes documents (Elctronic Invoice)
* Update: Italian language
* Update: Plugin-fw
* Fix: option label
* Fix: in_array() expects parameter 2 to be array, boolean given
* Fix: fixed a duplicated product line in the credit note
* Fix: Undefined variable: tax_percentage
* Fix: fixed the date placeholders for the invoice number
* Fix: fixed the delivery note display in the documents
* Fix: fixed the date placeholders for the invoice store folder
* Fix: fixed Undefined property notice
* Fix: fixing the tax percentage in the template
* Fix: integration with Multi Vendor
* Removed: templates for credit note (Electronic invoice)
* Dev: fixing an issue with the translations
* Dev: changed the args in the MPDF call


= 2.0.0 - Released on 09 April 2019 =

* New: support to WooCommerce 3.6.0 RC 1
* New: now, the credit notes are attached to the refund email when the order status change to refund
* Tweak: electronic invoice module (credit to Lorenzo Novia - https://webipedia.it/)
* Update: Spanish language
* Update: Plugin Framework
* Update: updated mPDF library to the version 7.0
* Fix: replaced character € to prevent the file by Agenzia delle Entrate from being rejected (Electronic Invoice module for Italian customers)
* Fix: fixed the credit note formatted number
* Fix: billing receiver ID and PEc are not showed correctly
* Fix: Eletronic Invoice Fields not showed under billing section onside the checkout page
* Dev: deleted deprecated filter woocommerce_found_customer_details


= 1.9.9 - Released on 08 March 2019 =

* New: create filename with a unique progressive ID (Electronic Invoice module for Italian customers)
* Tweak: calculate unit price with approximation of 5 decimals (Electronic Invoice module for Italian customers)
* Fix: tax calculation on order line items (Electronic Invoice module for Italian customers)
* Fix: prevent errors with HTML special characters (Electronic Invoice module for Italian customers)
* Fix: tax calculation on "Dati Riepilogo" in case of Product Bundle generated with WooCommerce Product Bundles (Electronic Invoice module for Italian customers)
* Dev: added new font UnBatang_0613.ttf
* Remove: admin notice for XML file name (Electronic Invoice module for Italian customers)


= 1.9.8 - Released on 27 February 2019 =

* New: option to choose which date to show in the invoice
* Tweak: PDF and XML documents created at the same time (Electronic Invoice module for Italian customers)
* Tweak: admin panel layout to manage XML documents (Electronic Invoice module for Italian customers)
* Tweak: support for generation of credit notes (Electronic Invoice module for Italian customers)
* Tweak: show notice before proceeding with the refund to prevent wrong configuration for XML document (Electronic Invoice module for Italian customers)
* Tweak: open/download XML documents from Orders page (Electronic Invoice module for Italian customers)
* Tweak: notice for users to prevent wrong numeration for invoices when they are generared manually
* Update: Plugin Framework  3.1.21
* Update: plugin language file
* Fix: wrong post meta used to recover invoice number
* Fix: wrong regular price when the taxes are included
* Dev: new hook 'yith_ywpi_bottom_invoice_section'




= 1.9.7 - Released on 18 February 2019 =

* Fix: use order completed date as invoice invoice date (Electronic Invoice module for Italian customers)
* Fix: calculate discount for item quantity (Electronic Invoice module for Italian customers)
* Fix: show item name using htmlentities (Electronic Invoice module for Italian customers)
* Fix: shipping costs on the invoice (Electronic Invoice module for Italian customers)
* Fix: order total called incorrectly (Electronic Invoice module for Italian customers)


= 1.9.6 - Released on 15 February 2019 =

* Fix: percent tax amount in case of inline discounts (Electronic Invoice module for Italian customers)
* Fix: include additional fees in total fields (Electronic Invoice module for Italian customers)
* Fix: VAT chargeability value (esigibilità IVA) not recovered correctly (Electronic Invoice module for Italian customers)
* Update: Dutch translation
* Update: Plugin Framework


= 1.9.5 - Released on 06 February 2019 =

* Updated: Spanish translation
* Tweak: admin selects province by a select (electronic invoice for Italian Customers)
* Fix: remove localization for all strings of the module Electronic Invoice
* Fix: show "datiriepielogo" section for each tax class	(electronic invoice for Italian Customers)
* Fix: prevent warning if billing_receiver_pec is not set (electronic invoice for Italian Customers)
* Fix: prevent warning if billing_receiver_vat_ssn is not set (electronic invoice for Italian Customers)
* Fix: show 'IdFiscaleIVA' section only for companies (electronic invoice for Italian Customers)
* Fix: show 'ScontoMaggiorazione' section in case of discount on order (electronic invoice for Italian Customers)
* Fix: force to uppercase SSN value in XML document for electronic invoice (electronic invoice for Italian Customers)
* Fix: Fixing the increment of the Invoice number.


= 1.9.4 - Released on 01 February 2019 =

* Update: Spanish translation
* Fix: VAT field set as mandatory incorrectly
* Fix: hide "Codice Fiscale" field in XML document for not italian companies
* Fix: hide Provincia field for in XML document for not italian customers


= 1.9.3 - Released on 29 January 2019 =

* Fix: Increment of the invoice numbers
* Fix: SSN and VAT showed as mandatory in according to the plugin options

= 1.9.2 - Released on 24 January 2019 =

* New: option to set Fiscal Regime of the company (Electronic Invoice module for Italian customers)
* New: option to set Chargeability of VAT for the company (Electronic Invoice module for Italian customers)
* New: possibility to customize error messages showed on checkout page (Electronic Invoice module for Italian customers)
* Update: plugin fw to version 3.1.15
* Update: italian translation
* Tweak: Receiver ID and PEC can be edited from user profile page on the backend (Electronic Invoice module for Italian customers)
* Tweak: AJAX Loading of Receiver ID and Receiver PEC on user profile when an order is created manually on the backend (Electronic Invoice module for Italian customers)
* Tweak: make mandatory fields on checkout page depending on whether the user is a company or a private customer (Electronic Invoice module for Italian customers)
* Fix: VAT Number is recovered correctly based on the user country (Electronic Invoice module for italian customers)
* Fix: make SSN mandatory only for private Italian customers (Electronic Invoice module for Italian customers)

= 1.9.1 - Released on 09 January 2019 =

* Update: plugin fw to version 3.1.14
* Update: italian translation
* Fix: Receiver ID and Receiver PEC are mandatory only is Company name is set (for Electronic Invoice Module)
* Dev: new filter 'ywpi_invoice_date_format_document'


= 1.9.0 - Released on 27 December 2018 =

* New: support to electronic invoice for italian customers. You can now create an XML document including all fields required by the Italian Agenzia delle Entrate.
* Update: plugin fw to version 3.1.6

= 1.8.6 - Released on 07 December 2018 =

* New: support to WordPress 5.0
* New: added order_number as a placeholder to use in documents settings
* Update: updating Dutch language
* Update: plugin core to version 3.1.6
* Fix: short description not showing
* Dev: second parameter to filter 'yith_ywpi_image_path'


= 1.8.5 - Released on 24 October 2018 =

* New: adding a filter to allow RTL in the documents
* Update: plugin framework
* Update: plugin description
* Update: plugin links

= 1.8.4 - Released on 17 October 2018 =

* New: Support to WooCommerce 3.5.0
* Tweak: change pro-forma to proforma
* Tweak: new action links and plugin row meta in admin manage plugins page
* Update: Updating Plugin FW
* Update: .pot file
* Update: Dutch language
* Dev: fixed parent page
* Dev: added new filter ywpi_after_show_invoice_buttons


= 1.8.3 - Released on 18 September 2018 =

* New: added the Portuguese translations, thanks to Ricardo Araújo
* Update: Updated .pot
* Update: Updated Dutch language file
* Fix: Fixing a method call
* Fix: Now the no completed orders take the order data created and not the actual date
* Fix: Fixing the delivery date section in the documents
* Fix: Added missing options to wpml configuration file
* Dev: added a filter to the SSN text field
* Dev: added a filter in the document data invoice template to show the barcode if necessary
* Dev: added a filter to the pro-forma button text in my-account
* Dev: added filter to the SSN is_required field
* Dev: added new filters to the proforma and packing slip document names.
* Dev: adding a string to the text domain
* Dev: added a new filter to the current invoice number



= 1.8.2 - Released on 05 July 2018 =

* New: added a new feature in Credit Notes to display the refunded products
* Tweak: minor integration with YITH Role Based Prices
* Tweak: Improve CSS rule for Qty column
* Update: Dutch translation
* Dev: fixing a filter name
* Dev: added new filter to the discount symbol
* Dev: added a new condition and filter to display the invoice section in orders
* Dev: added float in order to prevent warning

= 1.8.1 - Released on 07 June 2018 =

* Fix: fixing an issue with the documents template

= 1.8 - Released on 05 June 2018 =

* Tweak: Get all the data from the orders
* Tweak: Improving the plugin settings
* Tweak: Now the footer can use the postmetas placeholders
* Tweak: Fees also added to the invoice subtotal
* Tweak: Adding the dimension option of the packing slip in the template, removing it from the class
* Update: Spanish translation
* Update: Updating Plugin Framework
* Update: updated the official documentation url of the plugin
* Fix: Fixed a problem when create the documents in the order table
* Fix: fixing the round in the details template
* Dev: checking YITH_Privacy_Plugin_Abstract for old plugin-fw versions
* Dev: Working in a new tab of the settings
* Dev: Hiding the new fields name tab
* Dev: hiding the postmetas created in the order
* Dev: added and old version of the get_order_currency functions to avoid incompatibilities


= 1.7.2 - Released on 29 May 2018 =

* New: Support to WooCommerce 3.4.0
* GDPR:
   - New: exporting user additional uploads data info
   - New: erasing user additional uploads data info
   - New: privacy policy content
* New filter yith_ywpi_print_invoice_name
* New: Show packing slip in a new tab
* Tweak: Remove blank lines in $replace_details
* Update: Update language files .pot
* Update: Italian translation
* Update: Dutch translation
* Update: documentation link of the plugin
* Fix: fixing a warning with non numeric value
* Fix: fixed a problem when generate credit notes
* Fix: fixed a php warning
* Dev: added a new filter in replace details
* Dev: added filter ywpi_get_item_product_regular_price

= 1.7.1 - Released on 26 February 2018 =

Tweak: Now in the orders table the download button open a new tab if needed
Tweak: show one meta for row in invoice in case of variable products
Dev: Added a filter to get the order currency
Dev: new filter 'yith_ywpdi_mpdf_args'
Fix: Fixing problem with order currency in the Invoices
Fix: Fixing the currency issues in the invoices and packing slip
Fix: Fixing the currency method
Fix: Percentage tax of the shipping on the invoice
Fix: Percentage tax of the shipping on the invoice (remove the wc_round_tax_total)
Fix: Force wp_redirect if wp_safe_redirect not works.

= 1.7.0 - Released on 29 January 2018 =

New: plugin fw 3.0.10
New: support to WooCommerce 3.3-RC2
New: integration with YITH Checkout Manager plugin (show additional fields using placeholders)
Fix: subtotal and tax on credit notes
Fix: order ID is not recovered properly (WooCommerce 2.6.14)
Dev: new argument for the filter 'yith_ywpi_template_product_variation_string'
Dev: new filter 'yith_ywpi_allowed_tag'
Dev: new filter 'yith_ywpi_replace_customer_details'
Dev: new hook 'yith_ywpi_before_replace_customer_details'
Dev: new filter 'ywpi_invoice_amount_label'
Dev: new filter 'ywpi_invoice_date_format'


= 1.6.4 - Released on 12 Dic 2017 =

* New: possibility to show the order number inside the invoice name
* Dev: new hook "yith_ywpdi_before_generate_template_mpdf"
* New: Dropbox folder option
* Fix: fatal error getting order ID with WooCommerce 2.6.14
* New: possibility to add order number in the invoice name
* Fix: Dropbox API

= 1.6.3 - Released on 29 November 2017 =

* New: regenerate proforma invoices
* Tweak: support to PHP 7.1
* Fix: subtotal and discount not showing correctly when coupons are applied
* Fix: encoding for arabian customers
* Fix: use "date completed order" as invoice date
* Fix: condition to show proforma status section box
* Dev: new filter 'yith_wcpdi_order_subtotal'
* Fix: Dropbox overwrite file
* Fix: initialization of the plugin (issue with YITH WooCommerce Multi Vendor)


= 1.6.2 - Released on 18 October 2017 =

* Tweak: protect invoice folder


= 1.6.1 - Released on 16 October 2017 =

* New: support to WooCommerce 3.2.x
* New: font XB Riyaz.ttf
* Fix: html closed bracket



= 1.6.0 - Released on 13 October 2017 =

* New: Dropbox API v2 support
* Fix: subtotal is not showed correctly in invoice
* Fix: regular price not showed correctly in invoice

= 1.5.3 - Released on 10 October 2017 =

* Fix: Adding images for the logo no bigger than 300x150 pixels (change coming from new version)

= 1.5.2 - Released on 19 September 2017 =

New: possibility to regenerate the document


= 1.5.1 - Released on 14 September 2017 =
* Fix: warning in invoice when the product has not tax applied
* Fix: show VAT field edited via YITH WooCommerce EU VAT

= 1.5.0 - Released on 05 September 2017 =
* Tweak: secured uploads folder

= 1.4.20 - Released on 25 August 2017 =
* Fix: product description not shown on invoice for product variations
* New: Dutch language files
* Update: plugin framework

= 1.4.19 - Released on 03 August 2017 =
* Dev: added ywpi_document_title filter
* Dev: ywpi_invoice_number_label_edit_order_page filter
* Dev: ywpi_invoice_number_label_for_credit_note filter
* Dev: ywpi_invoice_number_label filter
* Dev: ywpi_pattern_filename_invoice_or_credit_note filter
* Dev: ywpi_pattern_filename_proforma filter
* Dev: ywpi_pattern_filename_shipping filter

= 1.4.18 - Released on 31 July 2017 =
* Missing font Sun-Extra.ttf
* Fix: missed font
* Tweak: support to php 7
* Dev: new filter for document title


= 1.4.17 - Released on 06 June 2017 =

* New: support for WooCommerce 3.1.
* Dev: filter 'yith_pdf_invoice_customer_details_pattern' lets third party code to customize the customer details being shown.

= 1.4.16 - Released on 05 June 2017 =

* Fix: Call to undefined method in credit note documents with WooCommerce 3.

= 1.4.15 - Released on 29 May 2017 =

* Fix: VAT number and SSN number not properly retrieved from the customer details.

= 1.4.14 - Released on 16 May 2017 =

* Fix: gift cards row shown in invoice even if no gift cards were used.
* Dev: filter 'yith_pdf_invoice_after_customer_content' in customer-details.php template.

= 1.4.13 - Released on 08 May 2017 =

* New: show gift card amount on invoices.
* Dev: filter 'yith_pdf_invoice_show_gift_card_amount' lets third party plugin to change the layout for 'gift card discount' row in invoices.

= 1.4.12 - Released on 03 May 2017 =

* New: show delivery data in invoices when used with YITH WooCommerce Delivery Date plugin.
* Fix: when tax column is enabled, an error is thrown if free shipping method is used.
* Tweak; invoice layout for note field.
* Dev: added action 'yith_ywpi_after_document_notes' in notes field.

= 1.4.11 - Released on 27 April 2017 =

* Fix: cannot delete invoice once is created with WooCommerce 3.
* Fix: invoice not attached to outgoing emails with WooCommerce 3.
* Fix: wrong link for parent order when using YITH Multi Vendor and WooCommerce 3.0+.

= 1.4.10 - Released on 08 April 2017 =

* Fix: on WC 3.0, checkout error if an invoice is created on new order containing variable products.

= 1.4.9 - Released on 28 March 2017 =

* Fix: fatal error if mPDF class exists
* Fix: YITH Plugin Framework initialization.

= 1.4.8 - Released on 15 March 2017 =

* Fix: packing slip not rendered if the option for showing product size is enabled.

= 1.4.7 - Released on 07 Mar 2017 =

* New:  Support to WooCommerce 2.7.0-RC1
* Update: YITH Plugin Framework
* Fix: taxes amount calculation for shipping fee.
* Fix: Thai characters not shown correctly on invoice.
* Fix: pro-forma documents did not use the same layout of invoices

= 1.4.6 - Released on 20 February 2017 =

* Tweak: shipping costs and additional fees are shown on packing slip by default.
* Tweak: show a notice for pro-forma invoices not available when used with YITH Multi Vendor.

= 1.4.5 - Released on 01 February 2017 =

* New: integration with YITH Multi Vendor, show main order number in sub order invoices.
* Tweak: integration with YITH Multi Vendor, hide the metabox in admin's order pages if the invoicing for vendor orders is disabled.
* Fix: integration with YITH Multi Vendor, vendors cannot delete invoices.
* Dev: filter 'yith_ywpi_can_create_document' lets third party plugin to set if specific document could be created
* Dev: filter 'yith_ywpi_delete_document_capabilities' lets third party plugin to set the user capability that enable document deletion.

= 1.4.4 - Released on 16 January 2017 =

* New: invoice number can be set dynamically using the additional placeholders [year], [month] and [day]
* Fix: invoice generation failed if a product was deleted
* Dev: new filter 'yith_ywpi_set_document_date' for overriding the document date to be set in invoices and credit notes
* Dev: new filter 'yith_ywpi_image_path' for overriding the images shown in documents

= 1.4.3 - Released on 07 December 2016 =

* Added: ready for WordPress 4.7
* Fixed: proforma documents not attached to emails automatically

= 1.4.2 - Released on 23 November 2016 =

* Added: new option for showing weight and dimension of products in packing slip documents

= 1.4.1 - Released on 31 October 2016 =

* Fixed: DropBox sync fails on new document generation

= 1.4.0 - Released on 11 October 2016 =

* Added: manage refunds with credit notes
* Added: new templates hierarchy
* Added: all templates are customizable
* Added: compatibility with a wide range of character set
* Updated: changed the PDF library used from DOMPDF to MPDF

= 1.3.16 - Released on 12 August 2016 =

* Added: new PDF module

= 1.3.16 - Released on 12 August 2016 =

* Fixed: next invoice number updated when the invoice creation failed
* Fixed: conflict issue with DropBox library already instantiated

= 1.3.15 - Released on 27 July 2016 =

* Added: option for mandatory SSN number on checkout
* Added: option for mandatory VAT number on checkout

= 1.3.14 - Released on 04 July 2016 =

* Updated: the company logo is retrieved from the server path instead of the public path
* Updated: catalog file
* Updated: italian translation file

= 1.3.13 - Released on 20 June 2016 =

* Fixed: image not shown and other issue with DOMPDF library

= 1.3.12 - Released on 14 June 2016 =

* Added: WooCommerce 2.6 ready
* Fixed: in the YITH Multi Vendor plugin, the vendor was unable to set its own company logo

= 1.3.11 - Released on 29 April 2016 =

* Added: 100% compatibility with YITH WooCommerce Account Funds
* Added: filter yith_ywpi_print_document_notes for notes on invoices
* Added: filter that could hide buttons on plugin metabox
* Added: filter that could hide buttons on orders back-end page
* Added: filter that could hide pro-forma button on myaccount page

= 1.3.10 - Released on 14 April 2016 =

* Added: support for invoices made for the YITH WooCommerce Funds plugin
* Added: option for generating and attaching the pro-forma invoice on new order

= 1.3.9 - Released on 06 April 2016 =

* Fixed: the percentage discount column in the invoice shows the discounted percentage instead of the discount percentage

= 1.3.8 - Released on 05 April 2016 =

* Added: option that let you show a "discount percentage" column on invoice
* Added: option that let you show the order subotal inclusive or exclusive of the order discount
* Added: option that let you choose if the discount amount should be shown on the order summary

= 1.3.7 - Released on 16 March 2016 =

* Added: optionally show a column on invoice with total taxed
* Tweaked: huge improvement on resulting file size, reduced to few KB
* Added: option for enabling Unicode charset support(need to be disabled in order to have smaller image size)
* Updated: plugin catalog file

= 1.3.6 - Released on 14 March 2016 =

* Fixed: sanitize document file name
* Fixed: invoice number not incremented on automatic invoice
* Updated: yith-woocommerce-pdf-invoice.pot model file

= 1.3.5 - Released on 04 March 2016 =

* Fixed: download of documents from order page

= 1.3.4 - Released on 03 March 2016 =

* Fixed: unable to download the invoice on my-account page
* Fixed: missing button for invoice creation on orders page
* Updated: file yith-woocommerce-pdf-invoice.pot

= 1.3.3 - Released on 01 March 2016 =

* Fixed: missing $product on the invoice template when "show SKU" is enabled
* Fixed: show only valid taxonomy when the variation information should be displayed in the invoice
* Fixed: no file downloaded if "Document generation mode" was set to "Download"

= 1.3.2 - Released on 18 February 2016 =

* Updated: removed unused plugin options
* Fixed: warning on pro-forma document generation

= 1.3.1 - Released on 17 February 2016 =

* Fixed: wrong discount applied to the order totals

= 1.3.0 - Released on 16 February 2016 =

* Updated: plugin ready for WooCommerce 2.5
* Updated: invoice template can be override
* Added: YITH Multi Vendor compatibility: vendors can create their own invoices.
* Added: template system rewritten for improved performance and customization
* Added: customizable Customer billing details with third party postmeta

= 1.2.3 - Released on 29 December 2015 =

* Fixed: wrong discount calculation when price are entered inclusive of taxes

= 1.2.2 - Released on 15 December 2015 =

* Fixed: YITH Plugin Framework breaks updates on WordPress multisite
* Fixed: Missing localization for a string in invoice template

= 1.2.1 - Released on 11 December 2015 =

* Fixed: company logo not shown on invoice for DOMPDF issue

= 1.2.0 - Released on 04 December 2015 =

* Fixed: VAT number and SSN number not shown on invoice
* Updated: languages file

= 1.1.8 - Released on 04 November 2015 =

* Fixed: invoice generated and attached to emails not related to orders
* Updated : text-domain changed from ywpi to yith-woocommerce-pdf-invoice

= 1.1.7 - Released on 30 September 2015 =

* Fix: typo on invoice template
* Fix: wrong invoice number shown.

= 1.1.6 - Released on 01 September 2015 =

* Fix: removed deprecated WooCommerce_update_option_X hook.

= 1.1.5 - Released on 27 August 2015 =

* Tweak: update YITH Plugin framework.

= 1.1.4 - Released on 28 July 2015 =

* Added : new original product price column for invoices.

= 1.1.3 - Released on 19 June 2015 =

* Added : some placeholders for invoice prefix and suffix.

= 1.1.2 - Released on 22 May 2015 =

* Added : improved unicode support.

= 1.1.1 - Released on 24 April 2015 ==

* Tweak : invoice and pro-forma invoice template updated.

= 1.1.0 - Released on 22 April 2015 ==

* Fix : security issue (https://make.wordpress.org/plugins/2015/04/20/fixing-add_query_arg-and-remove_query_arg-usage/)
* Tweak : support up to Wordpress 4.2

= 1.0.5 - Released on 20 April 2015 ==

* Added : optionally display short description column.

= 1.0.4 - Released on 15 April 2015 ==

* Added : compatibility with WooThemes EU VAT Number plugin.

= 1.0.3 - Released on 07 April 2015 ==

* Fix : documents with greek text could not be rendered correctly.

= 1.0.2 - Released on 05 March 2015 ==

* Initial release
