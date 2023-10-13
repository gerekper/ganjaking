=== YITH Booking and Appointment for WooCommerce Premium ===

== Changelog ==

= 5.6.0 - Released on 9 October 2023 =

* New: support for WooCommerce 8.2
* Update: YITH plugin framework
* Update: language files

= 5.5.0 - Released on 12 September 2023 =

* New: support for WooCommerce 8.1
* New: support for WooCommerce Cart and Checkout blocks
* New: support for PHP 8.2
* Update: YITH plugin framework
* Update: language files
* Tweak: show 'booking of' label in WooCommerce Cart and Checkout blocks, if enabled

= 5.4.0 - Released on 07 August 2023 =

* New: support for WooCommerce 8.0
* New: support for WordPress 6.3
* New: support to WooCommerce blockified templates
* New: support to WooCommerce Cart and Checkout blocks
* Update: YITH plugin framework
* Tweak: improved booking notes, by preventing duplicated notes on booking creation
* Tweak: allow decreasing people in people selector without checking for the minimum global value
* Dev: deprecated 'yith_wcbk_rest_capability_for_manage_availability_rules' filter
* Dev: added 'yith_wcbk_rest_check_manager_permissions' filter, to filter REST API permissions for global rules

= 5.3.0 - Released on 19 July 2023 =

* New: support for WooCommerce 7.9
* Update: YITH plugin framework
* Update: language files
* Dev: added yith_wcbk_admin_booking_show_calculated_amount filter
* Dev: added yith_wcbk_get_maximum_advance_reservation filter
* Dev: added yith_wcbk_get_maximum_advance_reservation_unit filter

= 5.2.1 - Released on 9 June 2023 =

* Update: YITH plugin framework
* Update: language files
* Tweak: improved 'toggle' effect on rules
* Dev: added yith_wcbk_is_request filter

= 5.2.0 - Released on 8 June 2023 =

* New: support for WooCommerce 7.8
* Update: YITH plugin framework
* Update: language files
* Fix: issue when duplicating a bookable product having resources
* Dev: new filter 'yith_wcbk_admin_query_filters_vars'
* Dev: new filter 'yith_wcbk_rest_capability_for_manage_availability_rules'
* Dev: new filter 'yith_wcbk_rest_capability_for_manage_price_rules'


= 5.1.0 - Released on 15 May 2023 =

* New: support for WooCommerce 7.7
* Update: YITH plugin framework
* Update: language files
* Fix: availability issue when creating bookings on backend by using translated products in combination with WPML
* Tweak: redirect to booking or order when creating booking in Bookings' list page on admin side
* Tweak: prevent saving order item meta related to Booking when saving orders

= 5.0.3 - Released on 28 April 2023 =

* Update: YITH plugin framework
* Update: language files
* Fix: trigger plugin installation actions only when installing a new plugin version
* Fix: style when creating booking on admin side by using a mobile device
* Tweak: parse the number of people to integer when updating data for person types on bookings
* Tweak: improved support for YITH Deposits, by setting fully-paid booking to paid also when the same order contains other bookable products with deposit

= 5.0.2 - Released on 13 April 2023 =

* Update: language files

= 5.0.1 - Released on 12 April 2023 =

* Update: YITH plugin framework
* Update: language files
* Fix: load correct js files for global availability and price rules

= 5.0.0 - Released on 11 April 2023 =

* New: support for WooCommerce 7.6
* New: support for PHP 8.1
* New: admin panel UI
* New: create bookings from the calendar in the panel
* New: panel UI for vendor dashboard in combination with YITH Multi Vendor
* New: set emails in plugin panel
* New: use text editor in email messages
* New: email notification to be sent XX days before the booking start date
* New: email notification to be sent XX days after the booking start date
* New: email notification to be sent XX days before the booking end date
* New: email notification to be sent XX days after the booking end date
* New: option to block dates for pending bookings
* New: Gutenberg block for search forms
* New: option to show booking data in order items
* New: possibility to exclude bookable products in global price rules
* New: possibility to exclude products in global availability rules
* New: possibility to automatically select resources in booking forms by using the 'resources' parameter in query string
* Update: YITH plugin framework
* Update: language files
* Fix: order bookings by 'from' date in admin calendar
* Fix: support to YITH Multi Vendor
* Fix: search order results when creating a new booking
* Tweak: improved 'request a quote' button style in booking form in combination with YITH Request a Quote
* Tweak: improved style of booking notes
* Tweak: allow admins to create bookings for past and future dates and without checking for minimum/maximum duration and for allowed start days
* Tweak: improved support for YITH Multi Vendor
* Tweak: improved Gutenberg blocks
* Tweak: improved email settings
* Tweak: improved cache by using cache invalidation
* Tweak: added legend of statuses in backend calendar
* Tweak: improved global price rules
* Tweak: improved bookings' calendar style
* Dev: added yith_wcbk_json_search_order_limit filter
* Dev: translate the deposit expiration date in the booking form correctly

= 4.7.0 - Released on 16 March 2023 =

* New: support for WordPress 6.2
* New: support for WooCommerce 7.5
* Update: YITH plugin framework
* Update: language files
* Fix: available dates not updated on datepicker when selecting resources
* Fix: booking email translations in combination with WPML for confirmed/rejected booking notifications
* Fix: confirmed booking email settings not available for translation in WPML
* Tweak: prevent fatal error if booking does not exists
* Dev: filter yith_wcbk_booking_note

= 4.6.0 - Released on 17 February 2023 =

* New: support for WooCommerce 7.4
* Update: YITH plugin framework
* Update: language files
* Fix: support for YITH Multi Currency
* Fix: incorrect email heading key in WPML config
* Dev: new filter 'yith_wcbk_booking_product_parse_price_args' 
* Dev: new filter 'yith_wcbk_date_helper_time_diff'
* Dev: add filter 'yith_wcbk_booking_calendar_availability_classes'
* Dev: new filter 'yith_wcbk_order_check_order_item_for_booking'
* Dev: new filter 'yith_wcbk_is_upcoming_view'

= 4.5.0 - Released on 13 January 2023 =

* New: support for WooCommerce 7.3
* New: Catalan (Català) translation
* Update: YITH plugin framework
* Update: language files
* Fix: search order results when creating a new booking
* Tweak: fixed values in formatted cart item data to prevent issues with the WooCommerce Mini Cart block
* Dev: added yith_wcbk_json_search_order_limit filter
* Dev: translate correctly the deposit expiration date in the booking form

= 4.4.0 - Released on 13 December 2022 =

* New: support for WooCommerce 7.2
* New: support for WooCommerce HPOS feature
* Update: YITH plugin framework
* Update: language files
* Dev: added yith_wcbk_create_booking_assign_order_options filter

= 4.3.0 - Released on 17 November 2022 =

* New: support for WordPress 6.1
* New: support for WooCommerce 7.1
* Update: YITH plugin framework
* Update: language files
* Update: plugin framework
* Fix: issue when saving default availability of bookable products in combination with PHP 8
* Fix: support for WPML

= 4.2.2 - Released on 12 October 2022 =

* Fix: Google Calendar synchronization on booking update
* Fix: issue when changing vendor in booking services in combination with YITH WooCommerce Multi Vendor
* Tweak: improved booking objects saving

= 4.2.1 - Released on 11 October 2022 =

* Update: YITH plugin framework
* Update: language files
* Fix: undefined variable issue

= 4.2.0 - Released on 10 October 2022 =

* New: support for WooCommerce 7.0
* New: choose to show all available time-slots either in a dropdown menu or by listing them in the booking form
* New: choose to show the resources either in a dropdown menu or by listing them all in the booking form
* Update: YITH plugin framework
* Update: language files
* Fix: issue with resources' selector initialization in combination with YITH WooCommerce Quick View
* Fix: issue when setting external calendar URL including encoded chars
* Tweak: improved first available date calculation

= 4.1.0 - Released on 9 September 2022 =

* New: support for WooCommerce 6.9
* Update: YITH plugin framework
* Update: language files
* Fix: no booking found message in search form results
* Fix: deposit form issue when using the booking widget on mobile
* Tweak: improved Google Calendar background synchronization when synchronizing all bookings

= 4.0.3 - Released on 11 August 2022 =

* Update: YITH plugin framework
* Update: language files
* Fix: price shown in services in combination with WPML Multicurrency
* Fix: price conversion of totals in bookable product page in combination with WPML Multicurrency
* Fix: support for PHP 8.1, due to changes to static variables in inherited methods
* Tweak: fixed number of people in CSV export

= 4.0.2 - Released on 8 August 2022 =

* Update: YITH plugin framework
* Update: language files
* Tweak: improved integration with YITH WooCommerce Deposits and Down Payments

= 4.0.1 - Released on 5 August 2022 =

* Update: YITH plugin framework
* Update: language files
* Fix: missing minified version of JS files of modules
* Fix: issue with unavailable dates on first loading in Bookable products, when the 'Update non-available dates on loading (AJAX)' option is enabled
* Tweak: fixed double arrow shown in time select in combination with Proteo theme

= 4.0.0 - Released on 2 August 2022 =

* New: use modules to enable or disable features based on your needs
* New: use resources and share them among multiple bookable products
* New: option to set fields' font size
* New: support for WooCommerce 6.8
* New: bookable product form block, to allow showing the booking form on the single product page by using blocks for themes with full-site editing
* New: set border and border-radius in 'Booking form' block
* Update: YITH plugin framework
* Update: language files
* Fix: support to YITH WooCommerce Deposits & Down Payments
* Fix: time-to-start shown in bookings list takes into account the site timezone now
* Tweak: show price in booking form also when the product is not available
* Tweak: improved layout bookable product options
* Tweak: improved bookable product tabs
* Tweak: support to themes using theme.json file to load fonts when previewing Booking Gutenberg blocks
* Tweak: improved booking form block
* Tweak: show fields based on actual settings on the bookable product edit page
* Tweak: improved booking form style
* Tweak: improved info shown in orders containing bookable products
* Tweak: improved Gutenberg blocks' style

= 3.8.0 - Released on 18 July 2022 =

* New: support for WooCommerce 6.7
* Update: YITH plugin framework
* Update: language files

= 3.7.0 - Released on 21 June 2022 =

* New: support for WooCommerce 6.6
* Update: YITH plugin framework
* Update: language files
* Tweak: improved support for YITH WooCommerce Request a Quote by disabling request a quote button in bookable products that require confirmation
* Dev: new filter 'yith_wcbk_min_date'

= 3.6.0 - Released on 16 May 2022 =

* New: support for WordPress 6.0
* New: support for WooCommerce 6.5
* New: support to YITH WooCommerce Multi Vendor 4.0
* Update: YITH plugin framework
* Update: language files
* Fix: issue when updating default availability in bookable products translated with WPML
* Fix: availability rule issue when using 'generic dates' and 'from date' is greater than 'to date'
* Tweak: applying no cache getting available dates
* Dev: new filter 'yith_wcbk_args_for_get_bookings_in_time_range'
* Dev: added new parameter to 'yith_wcbk_searched_categories' filter

= 3.5.0 - Released on 7 April 2022 =

* New: support for WooCommerce 6.4
* New: send booking emails based on the order language in combination with WPML
* Update: YITH plugin framework
* Update: language files
* Update: Dompdf
* Fix: issue with utf-8 special characters when creating PDF files
* Fix: search form issue in results when searching for specific people in combination with WPML
* Fix: issue in search result links with 'full day' bookable products when duration is greater than one
* Tweak: include Email settings to let translation
* Dev: new filter 'yith_wcbk_get_product_not_available_date_ajax_referer'
* Dev: new filter 'yith_wcbk_admin_user_info_html'

= 3.4.0 - Released on 8 March 2022 =

* New: support for WooCommerce 6.3
* Update: YITH plugin framework
* Update: language files
* Fix: service issue with Bookable products that require confirmation
* Fix: date-picker issue with time zones with negative offset
* Tweak: hide tooltip in services if the description is empty
* Tweak: shop/hide 'Service info layout' option in panel based on the above options for showing prices and descriptions for services
* Tweak: improved integration with YITH WooCommerce Deposits and Down Payments, by calculating total amount in bookings by summing deposit and balance amount
* Dev: added 'yith_wcbk_booking_get_sold_price' filter
* Dev: added 'yith_wcbk_booking_get_sold_price_item_total' filter

= 3.3.0 - Released on 14 February 2022 =

* New: support for WooCommerce 6.2
* Update: YITH plugin framework
* Update: language files

= 3.2.1 - Released on 10 February 2022 =

* Update: YITH plugin framework
* Update: language files
* Fix: issue with scheduled actions, checking pending and completed bookings
* Fix: issue with time increment based on duration when a minimum duration is set
* Fix: date issue in 'end date' when 'Check min/max duration' option is disabled and 'Update non-available dates on loading' is enabled
* Tweak: message when services list table is empty
* Dev: new filter 'yith_wcbk_before_set_search_products_query'
* Dev: new filter 'yith_wcbk_email_placeholders'
* Dev: new filter 'yith_wcbk_booking_service_get_pricing_show_duration_period'
* Dev: new filter 'yith_wcbk_check_ajax_referer_on_get_booking_data'

= 3.2.0 - Released on 18 January 2022 =

* New: support for WordPress 5.9
* New: support for WooCommerce 6.1
* Update: YITH plugin framework
* Update: language files
* Update: plugin framework
* Update: plugin framework
* Fix: avoid date issue when genereting lookup tables with databases with timezone different from UTC
* Fix: fatal error ( Argument 1 passed to YITH_WCBK_Availability_Rule::map_from_old_version() must be of the type array, object given )
* Fix: wrong booking total shown on product pages (compatibility issue with WPML Multi Currency)
* Fix: fatal error "Call to a member function update_status() on bool"
* Fix: WPML integration
* Tweak: fixed warning with PHP 8 for 'wakeup' magic method visibility
* Dev: New filter 'yith_wcbk_ics_event_rows' to customize ICS event rows
* Dev: new filter 'yith_wcbk_booking_form_totals_list'

= 3.1.2 - Released on 7 December 2021 =

* Update: YITH plugin framework
* Update: language files
* Fix: enable days in calendar if time-slots are set through availability rules, for bookable products with time

= 3.1.1 - Released on 2 December 2021 =

* Update: YITH plugin framework
* Update: language files
* Fix: 'Booking form' block functionality on frontend
* Fix: vendor name shown in 'New booking' email for vendors
* Tweak: delete events on Google Calendar when trashing bookings if the 'on booking deletion' sync is enabled; re-sync them when untrashed
* Tweak: calculate price based on Search Form params also when results are shown in the Shop page

= 3.1.0 - Released on 1 December 2021 =

* New: support for WooCommerce 6.0
* New: 'Booking form' Gutenberg block
* Update: YITH plugin framework
* Update: language files
* Tweak: improved service quantity style
* Tweak: added 'counters' in statuses shown in Bookings' List
* Tweak: avoid issues due to line separator when parsing iCal files
* Tweak: avoid issues due to line separator when parsing iCal files
* Tweak: fixed time-zone issue with time shown in Booking details on admin side
* Dev: added 'yith_wcbk_admin_booking_status_actions_show_complete_action_if_paid' filter, to allow showing the 'complete' action button when the booking is paid in Bookings' List

= 3.0.2 - Released on 24 November 2021 =

* Update: YITH plugin framework
* Update: language files
* Fix: order search when creating booking on admin side
* Fix: issue when selecting service quantity and duration in mobile, using the bookable product form widget
* Fix: availability calculation when using default availability in combination with availability rules
* Fix: price conversion in search form results in combination with YITH Multi Currency Switcher for WooCommerce
* Tweak: fixed redirection after confirming/rejecting booking through actions in emails
* Tweak: fixed calendar redirection in Vendor's calendar in combination with YITH WooCommerce Multi Vendor
* Tweak: fixed dates set by default after clicking on search form results
* Tweak: improved link in search form results to include parameters also when dates are not selected
* Tweak: fixed date-picker initialization when adding a new date range to Availability rules

= 3.0.1 - Released on 10 November 2021 =

* Update: language files
* Tweak: prevent widget issues on mobile with different themes using z-index in sidebar
* Tweak: improved date-picker style
* Dev: added filter 'yith_wcbk_product_form_widget_mobile_move_to_footer', to allow disabling the feature that moves the 'Bookable product form' widget to the footer in mobile

= 3.0.0 - Released on 9 November 2021 =

* New: plugin restyling
* New: speed and performance improvements, especially in stores with several hundreds/thousands of bookings
* New: easy way to set default availability for bookable products
* New: easy way to set specific time-slots for hourly and per-minute bookable products
* New: assign availability rule to more than one date
* New: use generic dates in availability rules
* New: easy way to set availability rules, including multiple time-slots in the same rule
* New: choose if setting the booking as paid when the deposit or the balance order is paid in combination with YITH WooCommerce Deposits and Down Payments
* New: choose what to show as name of the synchronized event in Google Calendar
* New: added 'attendee' email in Google Calendar sync events, so events will be automatically added to the customer's Google Calendar
* New: choose what to show as booking name in the plugin calendar
* New: integration with YITH Multi Currency Switcher for WooCommerce (this allows automatic price conversion through Multi Currency exchange rates)
* New: choose the time format to be used in "Time Pickers"
* New: choose if showing service information (price and descriptions) in a tooltip or inline
* New: option to choose which costs will be included in prices shown on the Shop page
* New: 'Upcoming' view in bookings list (this allows seeing the future bookings)
* New: time to start info in bookings list
* New: booking price shown in bookings list (the price is taken from the order)
* New: show sold price in booking data on admin side
* New: show calculated price in booking data and in bookings' list for pending and confirmed bookings on admin side
* New: option to redirect users to checkout after adding a bookable product to the cart
* New: option to show a 'booking of' label in Cart and Checkout for bookable products
* New: option to show unit in booking prices
* New: option to choose how to handle error messages in booking forms
* New: possibility to set decimal values for discounts
* New: choose if showing totals in Cart and Checkout
* New: filter bookings by dates in bookings list
* New: option to hide 'Read more' button in shop pages for bookable products
* New: 'Bookable Products' Gutenberg block
* New: option to use the 'week' formula for booking units that are a multiple of 7 days in prices
* New: services selector in search forms
* New: horizontal layout for Search Forms
* New: set default distance range for 'location' field in Search Forms
* New: choose to show or hide distance range for 'location' field in Search Form
* New: use date range picker selector in Search Forms
* New: use people selector in Search Forms
* New: choose colors used by the plugin for frontend styles
* New: possibility to set custom messages for each booking email
* New: support to YITH Proteo theme with 3 specific skins: Apartments, Hotels, Travel
* Update: YITH plugin framework
* Update: language files
* Fix: avoid direct add-to-cart for bookable products when using WooCommerce 'All Products' Gutenberg block
* Fix: price calculation in search form results if the bookable product has people types and no people type is set in the Search Form
* Fix: issue when storing label for 'Search' field in search forms
* Fix: fixed displayed prices of services and bookable products when prices include taxes
* Tweak: moved duration after dates in booking forms
* Tweak: show time field after selecting date
* Tweak: disable dates after non-available ones in the date-picker of the End Date
* Tweak: improved style of Google Calendar settings
* Tweak: improved description style in Google Calendar event description
* Tweak: improved 'Logs' tab style
* Tweak: improved search form style
* Tweak: improved booking form style
* Tweak: improved date picker style
* Tweak: improved field style in Booking Form and Booking Search Form
* Tweak: improved calendar range picker style
* Tweak: improved service creation panels
* Tweak: improved calendar style
* Tweak: improved style of booking details page
* Tweak: improved booking emails
* Tweak: improved booking calendar on admin side
* Tweak: set the previous status when restoring bookings from trash
* Tweak: disabled browser autocompletion on bookable product panel fields
* Tweak: customize border-radius for search button in Search Forms
* Tweak: added Help tab
* Dev: data_query param for querying bookings
* Dev: added 'yith_wcbk_after_add_to_cart_validation' action, to allow handling actions after add-to-cart valid
* Dev: added 'yith_wcbk_booking_product_single_person_type_{cost_type}' filter, to allow filtering person type specific costs
* Remove: YITH Booking theme

= 2.4.0 - Released on 4 November 2021 =

* New: support for WooCommerce 5.9
* Update: YITH plugin framework
* Dev: new filter 'yith_wcbk_product_tabs_service_name'

= 2.3.0 - Released on 15 October 2021 =

* New: support for WooCommerce 5.8
* Update: YITH plugin framework
* Dev: new filter 'yith_wcbk_pdf_font_family'

= 2.2.1 - Released on 27 September 2021 =

* Update: YITH plugin framework
* Update: language files
* Fix: debug info feature removed for all logged in users
* Dev: added 'yith_wcbk_shortcode_services_info_html' filter, to allow filtering service info for services shown through booking_services shortcode

= 2.2.0 - Released on 10 September 2021 =

* New: support for WooCommerce 5.7
* Update: YITH plugin framework
* Update: language files
* Dev: added 'yith_wcbk_booking_is_available_data' filter to manipulate is_available check data results

= 2.1.28 - Released on 9 August 2021 =

* New: support for WooCommerce 5.6
* Update: YITH plugin framework
* Update: language files
* Fix: availability issue when requesting confirmation for a booking product in combination with WPML
* Tweak: fixed issue with quotes in global rules
* Tweak: added responsive style for 'Create booking' page
* Tweak: show vendor products only when searching for booking products in calendar in combination with YITH WooCommerce Multi Vendor
* Tweak: hide booking products when searching for a person type that is not enabled in those products
* Tweak: fixed default duration set in daily bookings form after opening it through Search Form results
* Dev: new filter yith_wcbk_people_label to customize "People" label

= 2.1.27 - Released on 1 July 2021 =

* New: support for WordPress 5.8
* New: support for WooCommerce 5.5
* New: Norwegian (Bokmål) translation
* Update: YITH plugin framework
* Update: language files
* Tweak: added WPML translation to person types
* Tweak: added 'notranslate' class to date-pickers to prevent issues with dates when translating pages through Google Translate
* Tweak: improved service description shown in tooltip
* Tweak: store Google Maps coordinates retrieved by address in transient to reduce external calls to Google Maps API
* Dev: added yith_wcbk_maps_pre_get_location_by_address filter, to allow retrieving coordinates by address
* Dev: added yith_wcbk_maps_get_location_by_address_use_transients filter, to allow disabling transients when retrieving coordinates by address through Google Maps
* Dev: added yith_wcbk_maps_get_location_by_address_success filter, to allow custom action after retrieving coordinates by Google Maps
* Dev: added yith_wcbk_maps_get_location_by_address filter, to allow filtering location coordinates retrieved by Google Maps
* Dev: added yith_wcbk_check_for_monthly_discount filter used to apply the monthly discount conditionally
* Dev: added yith_wcbk_search_form_submit_label filter used to change the Search Form submit button label

= 2.1.26 - Released on 3 June 2021 =

* New: support for WooCommerce 5.4
* Update: YITH plugin framework
* Update: language files
* Fix: date-picker minimum date issue with negative timezone offsets
* Fix: re-initialize Search Form fields after filtering products through YITH Ajax Product Filters
* Dev: added yith_wcbk_i18n_clear filter
* Dev: added yith_wcbk_get_price_based_on_search_param filter
* Dev: added yith_wcbk_search param to get_posts params when searching for booking products

= 2.1.25 - Released on 10 May 2021 =

* New: support for WooCommerce 5.3
* Update: YITH plugin framework
* Update: language files
* Fix: support for YITH WooCommerce Request a Quote
* Tweak: improved time select field style

= 2.1.24 - Released on 12 April 2021 =

* New: support for WooCommerce 5.2
* New: translate service description through WPML
* Update: YITH plugin framework
* Update: language files
* Fix: wrong price amount shown in the search form results when using WooCommerce Multi Lingual and WPML
* Tweak: added specific CSS class to add-to-cart button of booking products that require confirmation
* Dev: added yith_wcbk_get_minimum_advance_reservation filter
* Dev: added yith_wcbk_get_minimum_advance_reservation_unit filter

= 2.1.23 - Released on 5 March 2021 =

* New: support for WordPress 5.7
* New: support for WooCommerce 5.1
* Update: YITH plugin framework
* Update: language files
* Dev: added yith_wcbk_set_buffer filter
* Dev: added yith_wcbk_product_booking_tabs filter

= 2.1.22 - Released on 27 January 2021 =

* New: support for WooCommerce 5.0
* New: German translation
* Update: YITH plugin framework
* Update: language files
* Dev: added yith_wcbk_buffer_field_custom_attributes filter
* Dev: added yith_wcbk_product_form_widget_mobile_fixed filter
* Dev: added yith_wcbk_search_form_label_location filter
* Dev: added yith_wcbk_search_form_label_tags filter
* Dev: added yith_wcbk_add_to_cart_for_selected_data action

= 2.1.21 - Released on 30 Dec 2020 =

* New: support for WooCommerce 4.9
* Update: plugin framework
* Update: language files
* Tweak: prevent issues with timezones in date-picker
* Tweak: prevent issue when synchronizing calendars with external services that requires the User-Agent set in the request header
* Dev: added yith_wcbk_is_last_minute_discount_allowed filter

= 2.1.20 - Released on 01 Dec 2020 =

* New: support for WordPress 5.6
* New: support for WooCommerce 4.8
* New: set 'minimum advance reservation' in hours
* Update: plugin framework
* Update: language files
* Fix: issue with timezone when checking for availability on current day
* Dev: new filter 'yith_booking_cart_item_data'

= 2.1.19 - Released on 28 Oct 2020 =

* New: support for WooCommerce 4.7
* Update: plugin framework
* Update: language files
* Tweak: redirect after registration if the customer is submitting a booking confirmation request
* Dev: added yith_wcbk_booking_calculate_cost_apply_person_type_rule_to_all_people filter
* Dev: added yith_wcbk_service_price filter

= 2.1.18 - Released on 15 Oct 2020 =

* New: greek translation
* Update: plugin framework
* Update: language files
* Tweak: redirect after login if the customer is submitting a booking confirmation request

= 2.1.17 - Released on 05 Oct 2020 =

* New: support for WooCommerce 4.6
* Update: plugin framework
* Update: language files
* Fix: issue with service quantities when max is set to zero
* Tweak: improved style
* Tweak: added support for ajax add-to-cart for booking products
* Dev: added yith_wcbk_calendar_booking_classes filter
* Dev: added yith_wcbk_search_form_label_categories filter

= 2.1.16 - Released on 17 Sep 2020 =

* New: support for WordPress 5.5
* New: support for WooCommerce 4.5
* New: show related booking details in orders
* Update: plugin framework
* Update: language files
* Fix: issue when selecting the 'End Date' and using inline datepickers
* Fix: timezone offset in iCal files when the offset is greater than 9
* Tweak: force updating coordinates (retrieved by location) when saving booking products if they are not set
* Tweak: fixed double arrows shown in selects
* Tweak: disable Request a Quote button if the booking product fields are not filled
* Tweak: prevent notice for trying to get property of non-object
* Tweak: services with min quantity set to zero will be considered as optional if the customer choose set the quantity to zero, so they will be not added to the booking
* Dev: new filter 'yith_wcbk_time_select_edit_booking_minute_step'
* Dev: new filter 'yith_wcbk_plugin_panel_args'

= 2.1.15 - Released on 03 Jul 2020 =

* New: support for WooCommerce 4.3
* Update: plugin framework
* Update: language files
* Fix: issue with Google Calendar sync when creating bookings through the Create Booking page on backend
* Tweak: prevent issue when booking product form is shown in products shown in the WP Customizer
* Tweak: prevent calendar style issues in combination with some themes
* Tweak: localized missing strings in Logs tab
* Dev: added yith_wcbk_searched_categories filter
* Dev: added yith_wcbk_related_booking_title filter
* Dev: added yith_wcbk_totals filter

= 2.1.14 - Released on 18 May 2020 =

* New: support for WooCommerce 4.2
* Update: plugin framework
* Update: language files
* Fix: issue when 'cancelled term' is set to 1 month
* Dev: added yith_wcbk_service_free_text filter

= 2.1.13 - Released on 23 April 2020 =

* New: support for WooCommerce 4.1
* New: support for YITH Proteo theme
* New: pagination for bookings in My Account > Bookings endpoint
* Update: plugin framework
* Update: language files
* Update: YITH Booking theme 1.2.0 includes option to enable/disable sticky header and options to change header and footer colors
* Fix: add-to-cart URL in search results now includes searched parameters
* Tweak: prevent 'get property of non-object' issue
* Dev: fixed object type for Availability Rule and Price Rule objects
* Dev: added yith_wcbk_[OBJECT_TYPE]_object_default_data filter
* Dev: added yith_wcbk_booking_endpoints filter
* Dev: added yith_wcbk_endpoint_booking filter
* Dev: added yith_wcbk_availability_rule_day_fields filter
* Dev: added yith_wcbk_after_availability_rule_options action
* Dev: new parameter $booking for 'yith_wcbk_my_account_booking_column_[COLUMN_ID]' hook
* Dev: new parameter for 'yith_wcbk_booking_is_available_non_available_reasons' filter
* Dev: added yith_wcbk_product_metabox_form_field_html filter
* Dev: added yith_wcbk_booking_product_create_availability_time_array filter

= 2.1.12 - Released on 28 February 2020 =

* New: support for WordPress 5.4
* New: support for WooCommerce 4.0
* Update: plugin framework
* Update: language files
* Fix: hidden orders in combination with YITH Deposits
* Fix: auto-fill people when searching through Booking Search Forms in popup
* Fix: issue when parsing iCal files if they contains additional information beyond normal events
* Tweak: prevent issues when saving search forms
* Tweak: prevent issues if 'date' column is not set
* Tweak: prevent issues on bulk actions if 'post' query string is not set
* Dev: new filter yith_wcbk_create_booking_order_item_data
* Dev: added yith_wcbk_count_booked_booking_in_period_args filter
* Dev: added yith_wcbk_get_future_bookings_by_product_args filter
* Dev: added yith_wcbk_create_booking_assign_order_default filter
* Dev: added yith_wcbk_product_get_not_available_dates_force_no_cache filter
* Dev: add 'is_create_page' param to ajax request in booking create form

= 2.1.11 - Released on 9 January 2020 =

* New: support to YITH WooCommerce Sms Notifications
* Fix: issue with YITH WooCommerce Request a Quote when adding to quote hourly booking products
* Tweak: prevent issues when calculating service costs

= 2.1.10 - Released on 7 January 2020 =

* Update: Spanish language
* Fix: issue when retrieving custom extra costs

= 2.1.9 - Released on 20 December 2019 =

* New: support for WooCommerce 3.9
* New: integration with YITH WooCommerce Review Reminder
* New: send 'Cancelled Booking' email to customers when the booking is cancelled by the customer
* New: when searching for booking products with Booking Search Forms on Shop page, prices reflect the selected parameters (dates, people and services)
* New: search forms autofilled after searching
* Update: language files
* Fix: integration with YITH WooCommerce Deposits and Down Payments
* Fix: issue with non-numeric values
* Fix: issues with custom extra cost when used with WPML
* Fix: issue with search form widget when using WP Customizer
* Fix: wrong "from" and "to" fields in request a quote table
* Fix: issue when emptying booking categories field
* Fix: init plus and minus in people select box
* Tweak: improved style
* Dev: added yith_wcbk_booking_form_after_label_duration action
* Dev: added yith_wcbk_ics_event_summary filter
* Dev: added yith_wcbk_ics_event_description_data filter
* Dev: added yith_wcbk_ics_event_description filter
* Dev: added yith_wcbk_google_calendar_sync_event_args filter
* Dev: added yith_wcbk_searched_value_for_field filter

= 2.1.8 - Released on 5 November 2019 =

* Update: plugin framework
* Update: Dutch language

= 2.1.7 - Released on 30 October 2019 =

* Update: plugin framework

= 2.1.6 - Released on 28 October 2019 =

* New: support for WordPress 5.3
* New: support for WooCommerce 3.8
* New: panel style
* Update: plugin framework
* Fix: issue with price for bookings created through the Create Booking page if prices include taxes in WooCommerce

= 2.1.5 - Released on 11 October 2019 =

* Fix: next month issue on calendar
* Fix: frontend styles for RTL languages
* Fix: update lookup table when syncing the booking price to avoid issues when sorting and filtering booking products by price
* Update: plugin framework
* Update: language files
* Tweak: fixed people label in search forms
* Tweak: specific CSS class for services in booking_services shortcode
* Dev: new filter 'yith_wcbk_notice_for_request_confirmation_login_required'
* Dev: new filter 'yith_wcbk_button_text_for_request_confirmation_login_required'
* Dev: new filter 'yith_wcbk_apply_weekly_discount' to prevent applying to of quickly discount in combination with monthly discount
* Dev: new filter 'yith_wcbk_ajax_booking_available_times_formatted_time' to let third party code filter time labels
* Dev: new action 'yith_wcbk_after_request_confirmation_action'
* Dev: new filter 'yith_wcbk_redirect_after_request_confirmation_action'

= 2.1.4 - Released on 5 August 2019 =

* New: set date format for date pickers
* New: display Date Picker inline
* New: option to delete event on Google Calendar when the booking is deleted
* New: RTL support for admin side
* New: support to WooCommerce 3.7
* Update: plugin framework
* Update: language files
* Fix: only the customer assigned to the booking can view it
* Fix: non-available message issue in case the selected start date is not allowed
* Fix: add to cart validation when Max bookings per unit is greater than 1
* Fix: including iCal file only in booking emails, not WooCommerce ones
* Fix: Google Calendar sync on booking status update
* Fix: allow booking on same date for 'Full day' booking products
* Fix: set min date for 'End date' field when the 'Start date' field is filled by default
* Fix: whole disabled day issue when an hourly booking product is booked on midnight
* Fix: service prices shown in tooltip in combination with WPML Multi Currency
* Fix: support to YITH WooCommerce Multi Vendor: show externals in calendar to the related vendor only
* Fix: support to YITH WooCommerce Multi Vendor: admin can create Vendor services with the same name of the admin ones
* Fix: support to YITH WooCommerce Multi Vendor: issue with 'Booking status (Vendor)' email
* Fix: support to YITH WooCommerce Multi Vendor 3.3.7: suppress filters for booking post type to avoid issues when retrieving booking product availability through AJAX
* Fix: integration with YITH WooCommerce Catalog Mode
* Fix: booking form style in combination with Elementor plugin
* Fix: calendar style
* Tweak: display order status in Bookings list
* Tweak: set default email type to HTML for booking emails
* Tweak: prevent issues in Edge browser by disabling autocompletion in search forms
* Tweak: added 'bk-to-date' and 'bk-from-date' CSS classes to date-pickers
* Tweak: store timestamp in booking note through current_time instead of using MySQL timestamp, to prevent issues with different server timezones
* Tweak: prevent issues with duration field
* Tweak: improved support to YITH Deposits: booking is automatically set to cancelled if the balance order is set to cancelled
* Tweak: improved styles
* Dev: added yith_wcbk_booking_pdf_logo_url filter
* Dev: added yith_wcbk_product_retrieved_externals filter
* Dev: added yith_wcbk_user_can_view_booking filter
* Dev: added yith_wcbk_search_booking_products_show_daily_bookings_with_at_least_one_day_available filter

= 2.1.3 - Released on 23 May 2019 =

* New: custom extra costs per product
* Update: plugin framework
* Fix: issue when sorting and managing price and availability rules
* Fix: prevent issue when creating booking from order with wrong data
* Fix: issue on booking edit page if the related product was deleted
* Fix: outlook issue for emails with iCal attached
* Fix: issue when creating booking services in product edit page
* Tweak: possibility to set the price to a specific value in Price Rules
* Tweak: UX improvement for price rules
* Tweak: UX improvement for availability rules
* Tweak: include check-in and check-out times when attaching iCal in emails
* Tweak: prevent notice in cart validation
* Tweak: prevent issues in product row actions
* Tweak: delete booking product cache after creating a new booking
* Tweak: improved style
* Dev: added yith_wcbk_show_user_info_in_pdf_only_for_admin filter
* Dev: added yith_wcbk_allow_creating_people_types_in_product_edit_page filter

= 2.1.2 - Released on 23 April 2019 =

* New: set decimal prices for services
* Fix: availability issue
* Fix: search form issue when searching for booking products with time on a specific date
* Fix: cancel bookings when resuming orders to prevent multiple paid bookings when the payment fails
* Fix: time condition if the from time is greater than the to time
* Tweak: allow to translate custom labels through WPML String Translations
* Tweak: fixed order awaiting payment issue in WooCommerce 3.6
* Tweak: fixed issue with minimum duration
* Tweak: use CRUD to save meta in orders
* Dev: added yith_wcbk_product_availability_rules_when_checking_for_availability filter
* Dev: PHPUnit Tests: deprecated get_booking_prop tests

= 2.1.1 - Released on 29 March 2019 =

* New: support to WooCommerce 3.6.0 RC 1
* Update: language files
* Update: YITH Booking theme 1.1.3
* Fix: integration with Multi Vendor: issue when associating a booking service to vendors
* Fix: price calculation with fixed duration
* Fix: prevent memory issues
* Tweak: check for booking availability directly in cart
* Tweak: added product link in error cart message
* Tweak: improved performances
* Dev: added yith_wcbk_request_confirmation_login_required_notice filter
* Dev: added yith_wcbk_booking_product_calculated_price_totals_formatted filter

= 2.1.0 - Released on 18 March 2019 =

* New: completely redesigned product settings panel to improve the plugin usability and to make it easier to set up booking products
* New: extra costs
* New: weekly discount
* New: monthly discount
* New: last minute discount
* New: extra price for every person added to a specified value
* New: possibility to show details about booking price totals on frontend
* New: create a people type directly in product page (via AJAX)
* New: create services directly in product page (via AJAX)
* New: multiply base price by number of people
* New: multiply fixed base fee by number of people
* New: tooltip in booking service shortcode
* Update: YITH Booking theme 1.1.2
* Update: language files
* Fix: WPML integration
* Fix: availability time range issue
* Fix: availability rule issue when overriding a non-bookable rule
* Fix: js date issue on calendar on due to Daylight Saving Time
* Fix: search form results when there are hourly and per-minute booking products
* Fix: removed arguments in product links in search form results on hourly and per-minute booking products
* Fix: cache issue on non-available dates with external service synchronization
* Fix: added service quantities when paying for booking that requires confirmation
* Fix: integration with YITH WooCommerce Deposits and Down Payments: prevent deposits on request confirmation bookings
* Fix: issue when counting people as separate booking
* Fix: person type counting issue in booking form when checking for availability
* Fix: person types issue in Booking form
* Fix: issue when translating 'View cart' text
* Fix: show people type only if published
* Tweak: improved Booking Form style
* Dev: WooCommerce CRUD for Booking products
* Dev: added yith_wcbk_booking_product_last_minute_discount_applied_on filter
* Dev: added yith_wcbk_booking_product_calculated_price_totals filter
* Dev: added yith_wcbk_product_get_not_available_dates filter


= 2.0.10 - Released on 4 February 2019 =

* Update: plugin framework
* Update: language files
* Fix: warning when saving product

= 2.0.9 - Released on 16 January 2019 =

* Update: YITH Booking theme 1.1.1
* Update: plugin framework
* Update: language files
* Fix: search form issue in combination with WPML
* Fix: issue when creating Bookings in Create Booking page in combination with WPML
* Fix: date format in orders for hourly and per-minute booking products
* Fix: integration with Multi Vendor: allow vendor to edit their own services
* Fix: cancelled by customer notification
* Fix: issues when searching for bookings when permalink structure is set to plain
* Fix: translation issue for day/days text
* Fix: month calendar issue
* Fix: calendar style issue
* Fix: sorting fields in Search Forms
* Fix: non well formed numeric value in Search form results
* Fix: allowed days in datepicker can be updated
* Fix: pagination and sorting when search form results are shown in shop page
* Tweak: set default people to empty in Search Forms
* Tweak: fixed calendar issue
* Tweak: duration unit strings to downcase
* Dev: added yith_wcbk_search_form_start_date_input_data filter
* Dev: added yith_wcbk_booking_product_calculated_price filter
* Dev: added yith_wcbk_booking_product_get_calculated_price_html filter
* Dev: added yith_wcbk_booking_product_get_price filter
* Dev: added yith_wcbk_format_duration function
* Dev: added yith_wcbk_booking_services_separator filter
* Dev: added yith_wcbk_booking_services_html filter
* Dev: added yith_wcbk_booking_services_html function

= 2.0.8 - Released on 5 December 2018 =

* New: support to WordPress 5
* New: search form results include booking products with time if there is at least one slot available in the selected dates
* New: possibility to hide services in Search Forms only
* New: set Geocode API key different by Google Maps API key to allow different restriction settings for the API keys
* Update: YITH Booking theme 1.1.0: support to WordPress 5 and Gutenberg, option to enable/disable product gallery in header through WP Customizer, improved style and so on...
* Update: language files
* Fix: YITH WooCommerce Request a Quote integration: display quantity for services in quotes
* Fix: issue when showing Booking Map in Quick View
* Fix: save _booking_id meta data in order items to prevent creation of multiple booking from the same order item
* Fix: display quantity for services in order item meta
* Fix: default value for timeselect
* Fix: service quantity issue for booking with 'request confirmation' option enabled
* Fix: add to cart validation for all day bookings
* Fix: error message in cart validation for max bookings per unit reached
* Fix: messages for non-available reasons
* Fix: check for bookings and booking product in cart when validating add-to-cart for max bookings per unit
* Fix: YITH Deposits integration: hide deposit form in widget when it's closed in mobile
* Fix: cache availability issue when saving global availability
* Fix: check for minimum people when checking for availability
* Fix: regenerate booking product data when booking status changes, if needed
* Fix: availability issue on translated booking products in combination with WPML
* Tweak: set order_item_id meta in bookings after creating orders for 'request confirmation' bookings
* Tweak: prevent warning with PHP 7
* Tweak: improved calendar when showing End Dates for booking with min duration set
* Tweak: fixed js issue with ECMAScript < 6
* Tweak: fixed minor issue when getting location by address with empty address
* Tweak: removed Search Form Results popup from the DOM when it's closed
* Tweak: added CSS class to duration fields based on duration type of the booking product
* Tweak: improved style
* Dev: PHPUnit Test - check for minimum people when checking for availability if 'count_persons_as_bookings' enabled
* Dev: PHPUnit Test - cost ranges
* Dev: added yith_wcbk_booking_product_create_availability_time_array_custom_time_slots filter
* Dev: added yith_wcbk_delete_data_for_booking_products function
* Dev: added yith_wcbk_sync_booking_product_prices function
* Dev: added yith_wck_booking_helper_count_booked_bookings_in_period_get_post_args filter
* Dev: added yith_wck_booking_helper_count_booked_bookings_in_period filter
* Dev: added yith_wcbk_cache_delete_{$object_type}_data action
* Dev: added yith_wcbk_cache_delete_object_data action
* Dev: added yith_wcbk_booking_product_after_regenerating_data action
* Dev: added yith_wcbk_cache_get_object_data_object_id filter
* Dev: added yith_wcbk_cache_get_object_data_{$object_type}_id filter
* Dev: fixed filter name 'yith_wcbk_booking_metabox_info_after_first_column'

= 2.0.7 - Released on 23 October 2018 =

* New: support to YITH WordPress Test Environment
* New: cost rule by time range
* New: 'Update non-available dates on loading (AJAX)' option, useful to prevent issues when using cache plugins
* New: added non-available reasons in messages
* New: possibility to include buffer in Time increment for hourly and per-minute booking with duration in fixed units
* Fix: issue when searching for category
* Fix: issue when searching for availability with types of people
* Fix: integration with Request a Quote
* Fix: prevent adding booking products in orders through 'Add products' box
* Fix: issue with external sync
* Tweak: fixed hide/show times with changing duration unit
* Tweak: prevent warnings since PHP 7.1
* Tweak: changed Booking name in Booking details on my account
* Tweak: stored booking product location to prevent too many requests for Google places
* Tweak: added 'bk-non-available-date' CSS class in datepicker
* Dev: added yith_wcbk_booking_get_name filter
* Dev: added yith_wcbk_logger_enabled filter
* Dev: added yith_wcbk_js_people_selector_params filter

= 2.0.6 - Released on 27 September 2018 =

* New: support to 3.5.0-beta.1
* New: option to automatically set paid bookings to complete
* New: possibility to show booking details in order items
* New: Completed Booking email
* New: booking form is auto-filled after clicking on product in search results shown in Shop Page
* New: possibility to edit 'Booking Services' label
* Fix: integration with YITH WooCommerce Request a Quote
* Fix: issues with old PHP versions
* Fix: issue when scrolling booking form on mobile
* Fix: removed people field for booking with no person
* Fix: calendar JS issue when Booking Search form is in Product Single page
* Fix: 'Show More Results' in Search Form results
* Fix: availability issue for 'All day' bookings when adding to cart
* Fix: timezone issue with Google Calendar by adding timezone information in iCal files
* Fix: cache issue with time availability on past dates if you book the product today
* Fix: duration label
* Update: Plugin Framework
* Update: language files
* Tweak: improved style of onoff fields in the table of types of people
* Tweak: split services in email into additional and included
* Tweak: fixed pricing issue when using variables
* Tweak: fixed enabling/disabling cache
* Tweak: fixed datepicker style in Create Booking page
* Tweak: fix product price sync
* Tweak: improved speed performance when searching for booking products through Search Forms
* Tweak: added possibility to set default category in Search Form shortcode
* Tweak: fixed footer action position in Booking PDF
* Tweak: show label instead of input field if min = max for booking duration
* Tweak: removed duplicated yith_wcbk_before_booking_form action
* Dev: added yith_wcbk_get_service_type_labels function
* Dev: added yith_wcbk_split_services_by_type function
* Dev: added yith_wcbk_booking_get_service_names filter
* Dev: added yith_wcbk_assets_bk_global_params filter
* Dev: added yith_wcbk_no_add_to_cart_for_selected_data filter
* Dev: added yith_wcbk_get_max_months_to_load function and filter
* Dev: added yith_wcbk_booking_form_service_info_html filter
* Dev: added yith_wcbk_order_parse_booking_data filter
* Dev: added yith_wcbk_order_get_booking_order_item_details filter
* Dev: added yith_wcbk_is_cache_enabled filter


= 2.0.5 - Released on 23 July 2018 =

* New: set quantity for Booking Services
* New: 45, 60, 90 minute steps
* New: Buffer between two bookings
* New: possibility to set the first available time as default selected
* New: possibility to search by tags in Booking Search Forms
* New: possibility to edit further labels such as From, To, Duration, Services, People, Total people
* New: French translation (thanks to Josselyn Jayant)
* Fix: added 'select people' label in people selector when no person was selected
* Fix: YITH WooCommerce Request a Quote support
* Fix: click on people selector label
* Fix: 'first available' date issue
* Fix: issue when sorting products by price
* Fix: available date issue in calendar
* Fix: issue with url when synching external bookings
* Fix: iCal import timezone offset
* Tweak: possibility to set values in booking form via query strings
* Update: YITH Booking theme
* Update: language files
* Tweak: improved style
* Tweak: prevent notices on Booking Create page
* Tweak: prevent sync URL issues with booking.com sync
* Tweak: fixed textdomain for untranslatable strings
* Tweak: added login link in notice for request confirmation booking
* Tweak: added option to enable/disable booking cache
* Tweak: added log when errors occur on getting Google Maps location coordinates
* Dev: added yith_wcbk_product_form_get_booking_data_available_args filter
* Dev: added yith_wcbk_cart_get_booking_data_from_request filter
* Dev: added yith_wcbk_before_create_booking_page action
* Dev: added yith_wcbk_calendar_booking_title filter
* Dev: added yith_wcbk_calendar_single_booking_data_booking_title filter
* Dev: added yith_wcbk_booking_get_title filter
* Dev: added yith_wcbk_booking_get_raw_title filter
* Dev: added yith_wcbk_request_confirmation_login_required filter
* Dev: added yith_wcbk_product_sync_price_before action
* Dev: added yith_wcbk_product_sync_price_after action
* Dev: added yith_wcbk_duration_minute_select_options filter
* Dev: added yith_booking_form_params filter
* Dev: added yith_wcbk_get_minimum_minute_increment function
* Dev: added yith_wcbk_get_minimum_minute_increment filter

= 2.0.4 - Released on 20 June 2018 =

* Fix: YITH Booking Theme package
* Fix: 'All day' booking end date
* Fix: issue with 'All day' bookings in calendar
* Fix: duration issue when saving 'all day' bookings
* Fix: availability in past for hourly and per-minute bookings
* Tweak: prevent issue with out-of-date PHP versions

= 2.0.3 - Released on 12 June 2018 =

* New: support to WPML Multi Currency
* New: possibility to set booking products as non-virtual to allow shipping for them
* New: added 'search for keyword' in Search Forms
* New: view Booking availability in calendar
* New: view booking calendar for each booking product
* New: 'Check min/max duration' option to choose whether it considers the minimum and maximum duration to show available dates in the calendar
* Fix: issue when adding to cart 'all day' bookings with fixed dates
* Fix: integration with YITH WooCommerce Catalog Mode
* Fix: integration with YITH WooCommerce Multi Vendor
* Fix: datepicker issue in Firefox
* Fix: issue when saving cost rules, including costs with variables
* Fix: style issues in mobile
* Fix: message issues in booking form
* Fix: availability issue with 'All day' booking products
* Fix: calendar issue on iOS devices
* Fix: hidden People details in PDF if the related booking doesn't have persons
* Tweak: added messages directly in Time select to improve usability
* Tweak: improved style
* Tweak: prevent issues when creating PDF
* Update: YITH Booking theme
* Update: Italian language
* Update: Dutch language
* Dev: added yith_wcbk_csv_fields filter
* Dev: added yith_wcbk_csv_field_value filter

= 2.0.2 - Released on 24 May 2018 =

* New: support to WordPress 4.9.6
* New: support to WooCommerce 3.4.0
* New: Privacy Policy Guide
* Update: YITH Booking theme 1.0.2
* Fix: style issue in date reange picker
* Tweak: improved frontend style

= 2.0.1 - Released on 21 May 2018 =

* Fix: datepicker arrow issue
* Fix: wrong textdomain in some strings
* Fix: unlimited 'max bookings per unit'
* Fix: prevent issue with some payment methods
* Fix: js messages issue in booking form
* Fix: widget transition in mobile
* Fix: calendar style in frontend
* Fix: style of Booking Form widget on mobile devices
* Update: YITH Booking theme
* Update: Dutch translation
* Update: Spanish translation
* Tweak: improved usability of Booking Form
* Tweak: improved style
* Tweak: fixed overlay z-index
* Tweak: duration as number field for mobile devices



= 2.0.0 - Released on 9 May 2018 =

* New: Hourly bookings
* New: Per minute bookings
* New: All Day bookings
* New: Google Calendar integration
* New: improved performance
* New: YITH Booking theme
* New: show booking form in widget
* New: daily calendar
* New: Booking Notes (private and customer ones) on backend
* New: ICS export
* New: synchronization through ICS files (Booking Sync tab)
* New: show external bookings, loaded by ICS files, on calendar
* New: possibility to set "allowed start days"
* New: possibility to count people as separated bookings
* New: calendar style on backend
* New: person type ranges in Booking cost rules
* New: booking availability stored by using transient to improve performance
* New: load not-available dates via AJAX on frontend to improve performance
* New: Background Processes
* New: plain email templates
* New: booking emails contain the iCal event, so Gmail, for example, will show it in the email
* New: "Disable day if no time is available" option
* New: booking style
* New: people selector
* New: unique date range picker
* New: possibility to hide included services in Booking product form
* New: booking_services shortcode
* New: print service descriptions in Booking Form
* New: option to automatically reject pending confirmation bookings after X days
* New: actions to confirm/reject pending confirmation bookings in New Booking email
* New: show 'non bookable' text in price if product is not bookable
* New: default start date depends on 'Allow booking no sooner than' option
* New: set First Time Available as default start date
* New: fill booking form fields automatically when clicking on product links (results of booking search form)
* New: show messages for Min and Max duration in booking form
* New: possibility to hide Booking Search Form widget in single product
* New: show login form if booking form is shown to logged users only
* New: Booking List Table style
* New: Logs
* New: PHPUnit tests
* Update: Italian language
* Fix: availability issue for max bookings per unit
* Fix: availability issue with fixed duration bookings
* Fix: availability in past and future
* Fix: issue in availability table when creating a new product
* Fix: not-available dates
* Fix: style of services in booking form
* Fix: enqueued jquery-ui style only in Booking pages
* Fix: show booking data in YITH WooCommerce Request a Quote emails
* Fix: datepickers as readonly to prevent opening keyboard in mobile
* Fix: date picker min and max date when calendar range picker is enabled
* Fix: tiptip style in Booking list
* Fix: responsive calendar style
* Fix: copy to clipboard issue with input fields
* Fix: booking services not shown in frontend for vendors
* Fix: issue in Booking creation on backend
* Fix: wp_query issue
* Fix: service column width in product list
* Fix: notices when getting results of booking search forms
* Fix: style in services
* Fix: availability dates issue
* Fix: non-available booking message on checkout
* Fix: Search Form style
* Fix: removing non-available booking from cart issue
* Fix: WPML issue when paying with PayPal
* Fix: PHP7 warning for non-numeric values for prices
* Fix: yith_wcbk_is_booking_product issue with post objects
* Fix: issue with price rules
* Fix: issue in PDF booking details
* Fix: hide people in cart, emails and booking details if booking products doesn't have people
* Fix: price saved as float to fix issues with comma separator
* Tweak: click on the datepicker icon to open the datepicker
* Tweak: added label for services (additional and included)
* Tweak: possibility to set Default Time Step and Default Start Time for daily calendar view
* Tweak: improved table style of cost and person type rules
* Tweak: changed status colors
* Tweak: new blockUI loader style
* Tweak: order item meta set to be unique
* Tweak: hidden add-to-cart-timestamp order item meta
* Tweak: sorting Booking Labels by name
* Tweak: new style in "create booking" page
* Tweak: prevent issues on add to cart
* Tweak: changed PDF font to Helvetica
* Tweak: removed unused PDF fonts
* Update: templates
* Update: language files
* Dev: added yith_wcbk_monthpicker JS function
* Dev: added yith_wcbk_datepicker JS function
* Dev: added yith_wcbk_print_field function
* Dev: added yith_wcbk_print_fields function
* Dev: added yith_wcbk_array_add function
* Dev: added yith_wcbk_array_add_after function
* Dev: added yith_wcbk_array_add_before function
* Dev: added yith_wcbk_create_complete_time_array function
* Dev: added yith_wcbk_create_date_field function
* Dev: replaced yith_wcbk_my_account_bookingss_column_ action with yith_wcbk_my_account_booking_column_
* Dev: added yith_wcbk_printer_print_field_args filter
* Dev: added yith_wcbk_printer_print_fields_args filter
* Dev: added yith_wcbk_my_account_booking_columns filter
* Dev: added yith_wcbk_pdf_file_name filter
* Dev: added yith_wcbk_csv_delimiter filter
* Dev: added yith_wcbk_csv_file_name filter
* Dev: added yith_wcbk_booking_get_duration_html filter
* Dev: added yith_wcbk_booking_product_create_availability_time_array_unit_increment filter
* Dev: added yith_wcbk_show_booking_form_to_logged_users_only_show_login_form filter
* Dev: added yith_wcbk_product_get_not_available_dates_before filter
* Dev: added yith_wcbk_google_calendar_add_note_in_booking_on_sync filter
* Dev: added yith_wcbk_booking_search_form_default_location_range filter
* Dev: added yith_wcbk_booking_actions_for_emails filter
* Dev: added yith_wcbk_booking_product_get_mark_action_url_allowed_statuses filter
* Dev: added yith_wcbk_booking_product_get_mark_action_url filter
* Dev: deprecated argument in YITH_WCBK_Booking::update_status method
* Dev: class refactoring
* Dev: template refactoring


= 1.0.15 - Released on 30 January 2018 =

* New: support to WooCommerce 3.3.0-rc2
* Update: Plugin Framework
* Fix: WPML integration
* Fix: enqueued frontend scripts only when needed
* Fix: service cost per person type issue when 'Multiply all costs by number of people' option is enabled
* Fix: booking creating issue in backend

= 1.0.14 - Released on 10 January 2018 =

* Update: Plugin Framework 3
* Fix: Multi Vendor integration: vendors can add services with the same name of the admin vendors
* Fix: issue when paying for request confirmation bookings
* Fix: booking map in WooCommerce tabs
* Fix: YITH WooCommerce Quick View integration
* Fix: WooCommerce 3.x notice
* Fix: google map issue
* Fix: error when creating booking
* Fix: error when creating booking from order
* Dev: added yith_wcbk_printer_print_field_args filter
* Dev: added yith_wcbk_ajax_booking_data_request filter
* Dev: added yith_wcbk_cart_booking_data_request filter
* Dev: added yith_wcbk_booking_get_formatted_date filter
* Dev: added yith_wcbk_booking_product_free_price_html filter
* Dev: added yith_wcbk_booking_search_form_default_location_range filter


= 1.0.13 - Released on 11 October 2017 =

* New: support to Support to WooCommerce 3.2.0 RC2
* New: dutch language
* Fix: YITH WooCommerce Catalog Mode integration
* Fix: term issue in combination with YITH WooCommerce Multi Vendor
* Fix: issue pdf booking details
* Fix: Booking WP table list responsive issue
* Fix: search form result sorting
* Fix: month localization through PHP date in month picker
* Fix: check if booking has persons when check if it has multiply costs by persons enabled
* Dev: added yith_wcbk_ajax_search_booking_products_query_args filter
* Dev: added yith_wcbk_ajax_search_booking_products_posts_per_page filter
* Dev: added YITH_WCBK_DOING_AJAX constant
* Dev: added YITH_WCBK_DOING_AJAX_FRONTEND constant
* Dev: added YITH_WCBK_DOING_AJAX_ADMIN constant
* Dev: added yith_wcbk_booking_can_be_ filter
* Dev: js refactoring booking-map: added yith_booking_map function

= 1.0.12 - Released on 3 August 2017 =

* New: automatically cancel booking if related order is cancelled
* New: added css classes in Booking form rows
* Tweak: added desc-tip in settings
* Update: language files
* Fix: multiple non-purchasable booking notices in cart
* Fix: removed empty select in Service edit page options
* Fix: button label in search form result
* Fix: booking availability if end date is missing
* Dev: added yith_wcbk_booking_form_dates_duration_label_html filter
* Dev: added yith_wcbk_get_duration_units filter
* Dev: added yith_wcbk_booking_product_single_service_cost_total filter
* Dev: added yith_wcbk_booking_product_calculate_service_costs filter
* Dev: added yith_wcbk_search_booking_products_no_bookings_available_text filter
* Dev: added yith_wcbk_search_booking_products_no_bookings_available_after action
* Dev: added yith_wcbk_calendar_single_booking_data_before action
* Dev: added yith_wcbk_calendar_single_booking_data_after action

= 1.0.11 - Released on 27 June 2017 =

* Fix: integration with YITH WooCommerce Request a Quote and YITH WooCommerce Multi Vendor
* Fix: duration display in booking form
* Fix: more than one booking in cart issue in combination with WPML
* Tweak: prevent error with old PHP version
* Tweak: prevent issue when creating PDF

= 1.0.10 - Released on 11 May 2017 =

* New: add to cart more than one booking product with the same configuration
* Fix: issue in combination with WPML
* Fix: search form issue in combination with WPML
* Fix: New Booking (Admin) email recipients
* Fix: select2 issue in Booking Search Forms with WooCommerce 3.0.x
* Tweak: prevent issue if Shop Manager rule doesn't exist
* Dev: added yith_wcbk_order_add_booking_details_in_order_item filter
* Dev: added yith_wcbk_search_booking_products_before_get_results action
* Dev: added yith_wcbk_search_booking_products_after_get_results action
* Dev: added yith_wcbk_search_booking_products_search_results filter

= 1.0.9 - Released on 30 March 2017 =

* Fix: search form result issue

= 1.0.8 - Released on 23 March 2017 =

* New: support to WooCommerce 3.0-RC1
* New: choose whether to show the search form results through popup or in shop page
* New: possibility to set start and end date labels
* New: New Booking email for admins
* New: New Booking email for vendors
* Fix: booking status vendor email issue
* Fix: date localization
* Dev: search form class refactoring
* Dev: added yith_wcbk_get_search_form function
* Dev: added yith_wcbk_search_booking_products_search_args filter

= 1.0.7 - Released on 14 February 2017 =

* New: integration with YITH WooCommerce Multi Vendor Premium 1.12.0
* New: integration with YITH WooCommerce Quick View Premium 1.1.5
* New: spanish language
* New: italian language
* Fix: add to cart validation issue with booking product already added to the cart
* Fix: add booking capabilities on plugin activation only
* Fix: cost per person number calculation
* Dev: improved integration classes

= 1.0.6 - Released on 23 January 2017 =

* New: set default start date
* New: backend datepicker flat design
* Update: language file
* Fix: added missing variable
* Fix: wrong textdomain
* Fix: duration display issue
* Dev: added action yith_wcbk_before_booking_form
* Dev: added filter yith_wcbk_show_booking_form

= 1.0.5 - Released on 9 January 2017 =

* New: booking calendar flat design in frontend
* New: hide booking form from non-logged users
* Fix: issue when all dates are available
* Fix: datepicker issue

= 1.0.4 - Released on 6 December 2016 =

* Fix: issue when showing info of booking related to a deleted order
* Fix: person type display issues

= 1.0.3 - Released on 24 November 2016 =

* New: WPML integration for booking products, people and services
* Fix: admin select style in cost table
* Dev: added filter yith_wcbk_booking_form_message_bookable_text

= 1.0.2 - Released on 10 October 2016 =

* Fix: integration with YITH WooCommerce Deposits and Down Payments Premium 1.0.4

= 1.0.1 - Released on 4 October 2016 =

* New: integration with YITH WooCommerce Request a Quote Premium 1.5.7
* New: integration with YITH WooCommerce Catalog Mode Premium 1.4.3
* New: integration with YITH WooCommerce Deposits and Down Payments Premium 1.0.3
* Fix: service saving issue
* Fix: booking_map shortcode issue
* Fix: pay after booking confirmation

= 1.0.0 - Released on 31 August 2016 =

* Initial release


== Dev Notes ==

= Folder structure =
    - assets                            plugin assets, such us CSS, JS and images
    - bin                               contains the sh file to install PHPUnit test
    - includes                          plugin class and function files
        - assets                        classes to handle assets in admin, frontend and both
        - background-process            classes to manage background processes
        - booking                       classes to manage the Booking object
        - emails                        email classes
        - integrations                  classes to manage plugin and theme integrations
        - libraries                     libraries used by the plugin
        - utils                         utilities
        - widgets                       classes to manage widgets
    - languages                         plugin language files
    - lib                               external libraries
    - plugin-fw                         the YITH plugin framework
    - plugin-options                    the plugin options shown in YITH Plugins > Booking
    - templates                         plugin templates (they can be overridden by the theme)
    - tests                             PHPUnit tests
    - views                             plugin views (backend, they cannot be overridden by the theme)
    - init.php                          start file
    - wpml-config.php                   WPML configuration file
    - yith-booking.zip                  ZIP package of YITH Booking theme

= Notes =

    - class names:
        classes that handles CPT are called YITH_WCBK_Obj_Post_Type_Admin (example YITH_WCBK_Booking_Post_Type_Admin)
        classes that handles taxonomies are called YITH_WCBK_Obj_Tax_Admin (example YITH_WCBK_Service_Tax_Admin)
        classes that allows to handle (get, set, search, etc...) something, such as CPT, are the Helpers (examples: YITH_WCBK_Service_Helper, YITH_WCBK_Person_Type_Helper, YITH_WCBK_Date_Helper )
    - on backend the Booking (and the Booking menu) is handled by the class includes/booking/class.yith-wcbk-booking-admin.php
        it calls:
          includes/booking/admin/class.yith-wcbk-booking-calendar.php           -> handle the Calendar on backend
          includes/booking/admin/class.yith-wcbk-booking-create.php             -> handle the booking creation on backend
          includes/booking/admin/class.yith-wcbk-booking-metabox.php            -> handle booking metaboxes
          includes/booking/admin/class.yith-wcbk-booking-post-type-helper.php   -> is the Helper of Booking Post Type
    - difference between templates (frontend, so they can be overridden by the theme) and views (backend, so they cannot be overridden)
    - the AJAX calls are fully handled by the YITH_WCBK_Ajax class

    [Italian]

    - nomenclatura classi:
            le classi che gestiscono i CPT si chiamano YITH_WCBK_Obj_Post_Type_Admin (esempio YITH_WCBK_Booking_Post_Type_Admin)
            le classi che gestiscono le tassonomie si chiamano YITH_WCBK_Obj_Tax_Admin (esempio YITH_WCBK_Service_Tax_Admin)
            le classi che permettono di gestire (get, set, search, etc...) qualcosa, come CPT, sono gli Helper (esempi: YITH_WCBK_Service_Helper, YITH_WCBK_Person_Type_Helper, YITH_WCBK_Date_Helper )
    - il booking sul backend (e il menu Booking) viene gestito dalla classe includes/booking/class.yith-wcbk-booking-admin.php
        essa richiama:
          includes/booking/admin/class.yith-wcbk-booking-calendar.php           -> gestisce il calendario a backend
          includes/booking/admin/class.yith-wcbk-booking-create.php             -> gestisce la creazione del booking a backend
          includes/booking/admin/class.yith-wcbk-booking-metabox.php            -> gestisce le metabox del booking
          includes/booking/admin/class.yith-wcbk-booking-post-type-helper.php   -> gestisce il post type del booking
    - differenza tra templates (frontend e quindi sovrascrivibili dal tema) e views (admin e quindi NON sovrascrivibili)
    - la parte AJAX viene gestita interamente dalla classe YITH_WCBK_Ajax