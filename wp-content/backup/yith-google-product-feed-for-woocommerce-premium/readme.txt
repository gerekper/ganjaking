=== YITH Google Product Feed for WooCommerce Premium  ===

== Changelog ==

= 1.1.12 - Released on 25 May 2020 =

* New: Support to WooCommerce 4.2
* Update: Plugin-fw

= 1.1.11 - Released on 29 April 2020 =

* New: Support to WooCommerce 4.1
* Fix: Prevent fatal error when product doesn't exists
* Fix: Additional images on feed
* Update: Plugin-fw

= 1.1.10 - Released on 27 February 2020 =

* New: Support to WordPress 5.4
* New: Support to WooCommerce 4.0
* Update: Plugin-fw

= 1.1.9 - Released on 23 December 2019 =

* New: Support to WooCommerce 3.9.0
* Tweak: prevent fatal error on generate feed file
* Update: Plugin-fw
* Dev yith_wcgpf_get_product_title filter

= 1.1.8 - Released on 06 November 2019 =

* Update: Plugin-fw

= 1.1.7 - Released on 24 October 2019 =

* New: Support to WooCommerce 3.8.0 RC1
* New: Plugin panel style
* Update: Plugin-fw
* Dev: filter yith_wcgpf_get_products_mapping_quantity

= 1.1.6 - Released on 05 August 2019 =

* Tweak: check isset the variable before print the content
* Tweak: check if isset $values['feed_template']
* Fix: display google product feed variation information
* Fix: generate google categories for en locale
* Update: Italian language

= 1.1.5 - Released on 22 May 2019 =

* Update: Spanish language
* Update: Plugin-fw
* Dev: filter yith_wcgpf_add_google_product_feed_information
* Dev: action yith_wcgpf_template_product_information
* Dev: action yith_wcgpf_template_variation_information

= 1.1.4 - Released on 11 April 2019 =

* New: Support to WooCommerce 3.6.0 RC2
* Tweak: Add a check control if product is an instanceof WC Product
* Tweak: Added custom label for simple products and variations product
* Update: Spanish language
* Dev: Filter yith_wcgpf_get_product_permalink
* Dev: Filter yith_wcgpf_product_ids

= 1.1.3 - Released on 18 February 2019 =

* New: German translation thanks to Alexander Cekic
* Tweak: Improve speed to generate the file
* Dev: Filter yith_wcgpf_product_ids
* Dev: Filter yith_wcgpf_preg_replace_description

= 1.1.2 - Released on 31 December 2018 =

* Tweak: Improve get feed url
* Update: Dutch translation
* Update: Plugin Framework
* Update: .pot language
* Dev: filter yith_wcgpf_get_the_term_list

= 1.1.1 - Released on 23 October 2018 =

* New: compatibility with YITH WooCommerce EU Energy Label
* Fix: warning get property or non object
* Fix: js on google product feed pages
* Tweak: generating feed block when you click on generate feed button
* Update: Spanish translation
* Update: Plugin Framework
* Dev: filter yith_wcgpf_settings_panel_capability
* Dev: filter yith_wcgpf_show_shipping_information_on_product_page
* Dev: filter yith_wcgpf_show_product_information_on_product_page

= 1.1.0 - Released on 21 May 2018 =

* New: Spanish translation
* New: Support to WordPress 4.9.6 RC2
* New: Support to WooCommerce 3.4.0 RC1
* Update: Plugin Framework
* Update: Dutch translation

= 1.0.10 - Released on 24 April 2018 =

* New : Spanish version
* Fix : Replaced yith_wcgpf_pfd_shipping_weigth to yith_wcgpf_pfd_shipping_weight
* Fix : Get custom variable for variations product.
* Fix : Blank Spaces in the name for feed file
* Fix : Get additional images

= 1.0.9 - Released on 30 January 2018 =

* New: support to WordPress 4.9.2
* New: support to WooCommerce 3.3.0-RC2
* Update: plugin framework 3.0.11
* Dev : added yith_wcgpf_feed_url_merchant

= 1.0.8 - Released on 15 January 2018 =

* Fix : style issue in filter and conditions select
* New : Dutch translation
* Dev : added yith_wcgpf_show_brand_yith_plugin hook

= 1.0.7 - Released on 14 December 2017 =

* New : compatibility with plugin-fw 3.0
* Dev : added yith_wcgpf_get_google_categories_url hook

= 1.0.6 - Released on 30 November 2017 =

* Fix : weight label
* Fix : get the correct google categories
* Update: Language .pot

= 1.0.5 - Released on 25 October 2017 =

* New : added general field in product information and variation information
* Fix : prevent sintax error generate feed
* Update: YITH plugin framework
* Dev: added filter yith_wcgpf_product_condition
* Dev: added filter yith_wcgpf_get_sale_price
* Dev: added filter yith_wcgpf_get_sale_price_if_exists

= 1.0.4 - Released on 28 August 2017 =

* Fix : Error get a short description for product
* Fix : Error identifier_exist parameter in the feed

= 1.0.3 - Released on 14 August 2017 =

* New : Added strip tags to clean code
* New : General options to show price with tax
* New : Italian translation by Antonio Mercurio
* Fix : Error when filtering products in the feed
* Fix : Strip HTML tags from description row
* Fix : Prevent get orphan product
* Fix : Add product categories to the feed
* Dev : Added filter yith_wcgpf_enable_variation_in_feed
* Dev : Added filter yith_wcgpf_custom_fields_for_variations
* Dev : Added filter yith_wcgpf_get_attributes_wc
* Dev : Added filter yith_wcgpf_custom_fields_for_variations
* Dev : Added filter yith_wcgpf_get_variation_title
* Dev : Added filter yith_wcgpf_preg_replace_description
* Dev : Added filter yith_wcgpf_fields_value
f
= 1.0.2 - Released on 22 May 2017 =

* New : Compatibility with YITH WooCommerce Brands Add-On
* Fix : Error when filtering products in the feed
* Fix : Strip HTML tags from description row
* Dev : Added filter yith_wcgpf_values_in_feed

= 1.0.1 - Released on 10 May 2017 =
* Fix : Error generating feed.
* Update: YITH plugin framework

= 1.0.0 - Released on 28 April 2017 =

* Initial release