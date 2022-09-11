=== EventON ===
Contributors: Ashan Jay
Plugin Name: EventON
Author URI: http://ashanjay.com/
Tags: calendar, event calendar, events, directory, location, organizer, repeating event
Requires at least: 5.5
Tested up to: 6.0.2
Stable tag: 4.1.3
Initial Release: 2011-12-21

EventON is an event calendar plugin for WordPress, that helps you showcase and present your events to your audiences in a clean and clutter-free layout. 

== Description ==

For more than 10 years, EventON has been powering event calendars around the world for small and large organizations. EventON comes packed with various features that empowers you to present to your audience in a way that is easily capturable. 

Once activated you can easily set up a comprehensive event calendar with features like:

- Multi day events
- Repeating events
- Feature events and prioritize those or show only feature events
- Categorize events with unlimited event types and filter events using those
- Add event location and display google map locations
- Add event organizers
- Select custom colors for events 
- Include a custom image and extra event images for an event
- Setup virtual events with Jitsi and zoom in-built and various other streams
- Live now calendar view
- Schedule view calendar

For complete list of features please visit: https://myeventon.com/

== Licensing ==

The regular license grants you, the purchaser, an ongoing, non-exclusive, worldwide license to make use of the digital work (Item) you have selected. Read the rest of this license for the details that apply to your use of the Item, as well as the FAQs (https://codecanyon.net/licenses/faq) 

You are licensed to use the Item to create one single End Product for yourself or for one client (a “single application”), and the End Product can be distributed for Free.

Complete license restrictions: https://codecanyon.net/licenses/terms/regular


== Installation ==

1. Unzip the download zip file from codecanyon
2. Upload 'eventon' zip file to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

= Updating = 

Automatic updates will work for plugin update after you have registered and activated your license in eventON settings > Licenes.

How to update eventON: http://docs.myeventon.com/documentations/update-eventon/ 

== Changelog ==
= 4.1.3 (2022-9-8) =
ADDED: organizer social link to display in eventcard as well
FIXED: single event page not loading the scripts
FIXED: google fonts not loading on admin pages
FIXED: login access restriction to single event page not working
FIXED: widget block arrow styles
FIXED: Custom time format on eventcard to honor am pm value set in eventon language
UPDATED: event edit page styles

= 4.1.2 (2022-9-1) = 
ADDED: evo_event_permalink pluggable filter for event link
ADDED: google doc link on how to restrict API key
FIXED: array undefined error missed conditions
FIXED: mobile layout issues
FIXED: event edit time display in UTC timezone
FIXED: happening now live calendar time incorrections
FIXED: custom meta data icon not showing on eventtop
FIXED: schedule view all day events text not translated
FIXED: schedule view all day events not showing time as all day
FIXED: schedule view responsive layout
FIXED: live now event image not showing
FIXED: when event starts they stop appearing in the calendar currenttime > site time
FIXED: live now calendar upcoming event countdown time error
FIXED: widgets admin area error
FIXED: elementor register controls() error
FIXED: Restrict single event page to loggedin users not working
UPDATED: moment.js to version 2.29.4

= 4.1.1 (2022-7-11) = 
FIXED: array undefined error
FIXED: Event tag types not showing without tags active
FIXED: Location map not working
FIXED: custom meta data field not showing in eventtop
UPDATED: POT file

= 4.1 (2022-7-5) = 
ADDED: pluggable filter evo_lang_values_healthcare_guidelines for language values
ADDED: settings for single event page only for logged in users
ADDED: support for hourly repeat mode
ADDED: link to single event from anywhere on your site using shortcode [eventon_anywhere id='' repeat_interval='']
ADDED: Ability to show current repeat instance relative to others in repeat event title
ADDED: eventtop designer to customize eventtop layout
ADDED: Basis for support for all webhook systems like zapier and ifttt
FIXED: download all events as ICS from settings give error
FIXED: featured event image stretching out in safari
FIXED: add to google cal all day events end date incorrect
FIXED: view switcher text strings missing in translate
FIXED: multi data types lightbox save not working
FIXED: tiles style lightbox eventtop to honor eventtop_styles 
FIXED: postponed events showing progress bar
FIXED: eventcard and eventtop time incorrectly showing
FIXED: ICS download event not showing correct time
FIXED: {startdate} dynamic tags not translating
FIXED: generate_time_() $data_array initial definition
FIXED: _vir_url formating special characters from virtual URL
FIXED: Use UTC offset time globally on calendars showing events in incorrect months
FIXED: search box not responding to eventtop_styles shortcode value
UPDATED: new function to datetime class __get_lang_formatted_timestr()

= 4.0.6 (2022-4-5) = 
ADDED: support for eventon element color picker with input field
ADDED: Tabbed view support into shortcode generator using shortcode [add_eventon_tabs]
FIXED: virtual moderator not saved showing user as moderator
FIXED: event type taxonomy archive page error
FIXED: multiple eventON gutenberg blocks not working properly on same page
FIXED: quick edit time pm value save as am
FIXED: text error for healthcare guide
FIXED: repeat events link not sharing correct on social share
FIXED: search not passing correct $_POST values for sanitizing
FIXED: schedule view showing end time for hide end time
FIXED: virtual event HTML content getting stripped of tags
FIXED: hide end time still shows on edit all events page
FIXED: eventcard location text not all caps
FIXED: Google fonts not loading in backend 
FIXED: ICS add to calendar all day events saving as different date
FIXED: all day events saving adjust set times
UPDATED: virtual stream url is optional now

= 4.0.5 (2022-3-1) =
FIXED: end time was saving as end hour
FIXED: syntax error on class-meta_boxes-health.php 74
UPDATED: Do not delete eventon settings changed to not delete by default
UPDATED: POT file

= 4.0.4 (2022-2-28) = 
FIXED: 12am/pm changing the date to next day
FIXED: event end time minutes not saving correctly
FIXED: Undefined variable $unix_vir_end 

= 4.0.3 (2022-2-24) =
ADDED: evo_helper->sanitize_array()
ADDED: download all events as ICS to include event url in description field
ADDED: pluggable filters to expand healthcare data
ADDED: select2 dropdown for virtual event moderator field
ADDED: Option to set virtual event end time (BETA)
ADDED: ability to hide next events in live now calendar
FIXED: repeat event all day showing time
FIXED: tiles with ux_val=3a causing eventcard to not load
FIXED: colorful eventtop text color in appearance
FIXED: font family missing some classes for secondary font
FIXED: repeat events settings not showing with translations
FIXED: event custom meta field dynamic data link value not working
FIXED: gutenberg editor not updating in real time
FIXED: widget area layout issues via block based widgets
FIXED: js > evo_cal_get_basic_eventdata() passing event name as html
UPDATED: backender placeholder font color for input field
UPDATED: virtual event eventtop tag to be shown based on event attendance mode
UPDATED: virtual event settings configuration for event edit moved to separate lightbox
UPDATED: backend lightbox styles and functions

= 4.0.2 (2021-11-11) = 
ADDED: ability to set default eventtop_style for calendars via settings
ADDED: related events to be sortable and draggable
ADDED: support for dynamic values for custom meta data for event
ADDED: 3:1 event card row for event card designer
FIXED: archieve pages showing events as white all
FIXED: eventon settings menu access to non admin roles using manage_eventon
FIXED: tiles with 4 in a row responsiveness
FIXED: featured image as direct image not showing extra images
FIXED: live now calendar not loading with featured images
FIXED: smaller screen content cut off
FIXED: JS evo_append_lb() function to support CAL value and set lightbox color
FIXED: eventon Settings zoom tooltip thanks @OvereatersAnonymous
FIXED: recursive_sanitize_array_fields() sanitation thank you @jamiolee
FIXED: lightbox event countdown timer not active
FIXED: lightbox eventtop color not syncing with rest of the calendar
FIXED: cancel event text not showing on non list events
FIXED: event card cell responsiveness
FIXED: quick edit to hide event time for month/year long events
FIXED: in mobile screen event data box content to spread evenly
UPDATED: Lightbox close button to stay sticky on top while scroll

= 4.0.1 (2021-10-13) =
ADDED: eventtop_style='4' clean eventtop with gaps
ADDED: legacy tile detail under image with clean style using tile_style='2'
FIXED: learn more link showing without a link
FIXED: Responsive styles and various other style conflicts
FIXED: single event box styles
FIXED: undefined function EVO_Error() caught via wordpress
FIXED: Live now calendar responsive issues
FIXED: PHP8 compatibility

= 4.0 (2021-10-7) = 
ADDED: shortcode hide_cancels=yes to hide all cancel events from calendar events
ADDED: view_switcher to allow switching between different calendar views quickly using view_switcher='yes' - addons required
ADDED: event card designer via Settings 
ADDED: Event image and color for related events in new bigger layout
ADDED: support to hide various parts of eventtop via shortcode hide_et_dn, hide_et_tags, hide_et_tl, hide_et_extra
ADDED: support for showing only certain parts of an event using [add_single_eventon event_parts='yes']
ADDED: virtual events filter for event filtering
ADDED: event type filter for event filtering
ADDED: event attendance mode option for event edit settings
ADDED: settings option to hide below eventtop tags
ADDED: Option to select event query method via Settings > WP EventON Core Settings
ADDED: new event health field Vaccination Required
ADDED: new eventtop date design style via eventtop_date_style='1'
ADDED: Schedule View as part of eventON
ADDED: pluggable filter eventon_event_title_editbtn for edit button on eventtop
ADDED: Ability to select single event eventtop style via settings > Single Events
ADDED: Event location coordinates generation via wp_remote_get
ADDED: Generate location coordinates button
FIXED: event type widget not working correct
FIXED: past and future event text translation error
UPDATED: calendar filtering function for post_tags specific only to events to show
UPDATED: Live now calendar styles and layout with show_et_ft_img='yes'
UPDATED: tiles event details below design and layout refresh 
UPDATED: cancelled events display style
UPDATED: Shortcode generator layout and styles
UPDATED: EventON Settings page styles and layout
UPDATED: Handlebar.js library to latest 4.7.7 version
UPDATED: Moved most of ajde library to EVO_Settings() and EVO()->elements
IMPORTANT: default calendar eventtop style to colorful eventtop with gaps

= 3.1.7 (2021-7-27) = 
ADDED: option to disable csv export all events formatting chracters
ADDED: shortcode option to hide happening now events only
FIXED: paypal third party button supporting currencies 
FIXED: widget arrow positions
FIXED: several settings related translation strings missing
FIXED: event type widget not saving correctly
FIXED: compatibility with wordpress 5.8
FIXED: live now events with featured image styling
FIXED: widget live now icon styles
FIXED: taxonomy archive page styles formatting issues
FIXED: tooltips to use global object using elements
UPDATED: event edit page UI styles

= 3.1.6 (2021-5-20) =
FIXED: organizer page social links not working
FIXED: ux_val 4a loading event card content
FIXED: event time remaining calculated incorrectly
FIXED: location page layout issue
FIXED: schema information not showing in event
FIXED: add to google calendar only link not working
FIXED: EVO()->elements->print_date_time_selector() date_format_hidden field not working
UPDATED: eventon_ics_download input sanitation

= 3.1.5 (2021-4-16) =
ADDED: option to disable event URL encoding in social share items
FIXED: further all day event issues on ICS file
FIXED: event bubble styles for event lists
FIXED: Cal date range fixed month and year value format to int
FIXED: email not sending issue using evo_helper send_email()
FIXED: default image not showing on some rare cases

= 3.1.4 (2021-4-8) = 
ADDED: option to set custom email character type encoding method via settings
FIXED: all day events correction for ICS file
FIXED: email encoding changes causing errors on emails

= 3.1.3 (2021-4-6) =
ADDED: basic text area field as custom meta field for event data
FIXED: minor style changes
FIXED: year long events not showing correctly
FIXED: repeat events set for sunday not working
FIXED: health guidelines not showing when shows after virtual info
FIXED: add to google calendar not working
FIXED: add to calendar not working and time corrections
FIXED: default event image not working

= 3.1.2 (2021-3-10) = 
FIXED: repeating events from future showing in wrong months
FIXED: get_event_time() not using utcoff
FIXED: widget styles changes

= 3.1.1 (2021-3-15) = 
ADDED: option to disable jitsi external API from loading
ADDED: website time on event edit page
ADDED: utc offset time method (Beta)
FIXED: minor styles updates
FIXED: duplicate language string editing issue
FIXED: day/days for happening now calendar and live time remaining
FIXED: jitsi external api loading on wp-admin pages
FIXED: open location link in new window showing as input field
FIXED: link passed as multi data field convert to clickable link
FIXED: month nav arrow hover color not working
FIXED: virtual event access box styles for responsiveness
FIXED: virtual live showing sooner than it should be
FIXED: live now and other current time validations to use UTC offset event times universally
FIXED: stop running initial ajax call if ajax loading calendars are not in page
FIXED: widget arrow styles
FIXED: single repeat event header month correction

= 3.1 (2021-2-18) =
ADDED: zoom meeting with meeting authentication ability
ADDED: social media links support for event organizer
ADDED: pluggable filter for ics download for an event
ADDED: jitsi complete integration for virtual events
ADDED: moderator role selection option for virtual events
ADDED: all language strings duplicates to be updated when editing duplites
ADDED: event starting shortly notice for virtual events 30 minutes before
FIXED: live now bar showing NAN for days
FIXED: repeat events skipping certain months on monthly generator
FIXED: _convert_ssl_url() for urls without protocol
FIXED: colorpicker to have unique class
FIXED: undefined class error evo_admin()
FIXED: search showing same events multiple times
FIXED: event details ul ol formatting
FIXED: live bar layout issues on tile
FIXED: post event content display issues
UPDATED: color scheme for over all plugin backend
UPDATED: virtual events access section on eventcard

= 3.0.8 (2021-1-26) =
ADDED: related events to be orders in ascending order & minor style update
ADDED: jitsi, vimeo, twitch, wistia & RTMP stream as options for virtual event streaming
FIXED: font awesome backward icon compatibility
FIXED: open location in new window not saving value
FIXED: live now calendar d text translation for countdown timer
FIXED: Eventon element date picker start opening end time
FIXED: shortcode generator icon missing from classic editor

= 3.0.7 (2021-1-12) = 
FIXED: number of repeats value creating 1 more than needed
FIXED: schema image url missing
FIXED: translation strings missing
FIXED: color eventon element not showing saved value
FIXED: add new event to reflect timezone set via settings
FIXED: adding custom repeats on 24 hour adds undefined value
FIXED: live now calendar with ux_val 3 clicks not working
FIXED: google map not working on ux_val 3a
FIXED: saving location showing as tags
FIXED: map scroll disable not working
FIXED: disable google maps not working
FIXED: disabling location info over image not working
FIXED: google maps API to not load on none eventon pages
FIXED: custom repeat to reset date range lock after adding
FIXED: arrow color settings not working in appearances
FIXED: zoom event image not working
FIXED: fourth week of month not saving repeat values correct
FIXED: new link to generate lanlat for locations
UPDATED: replaced repeat and featured icons on wp-admin with dashicons
UPDATED: font awesome fonts to 5.15.1

= 3.0.6 (2020-12-4) = 
ADDED: shortcode livenow_bar to hide live now bar and time via shortcode for calendar
ADDED: new filter function evo_event_desc_trig_outter_styles
ADDED: new filters to support for repeat customizer addon
ADDED: new method is_future_event() to EVO_Event Object
ADDED: back custom event timezone text string
ADDED: repeat increment field to have typable input field for large numbers
FIXED: number of repeats maxed at 4
FIXED: date store running the_content filter causing conflict with other plugins
FIXED: future repeats on eventcard separate end time with dash
FIXED: repeats time not correct as initial event time
FIXED: _generate_events() return data after pluggable filter is run
UPDATED: to use EVO_Event class to fetch event image

= 3.0.5 (2020-11-23) = 
FIXED: ux_val 4 not working
FIXED: event list calendar with gap between events not working
FIXED: enabling load eventon scripts only on eventON pages showing lightbox elements on page
FIXED: cancel event color on colorful events
FIXED: loggedin only events not showing
FIXED: yes no button in event edit not showing boxes

= 3.0.4 (2020-11-14) = 
FIXED: eventtop custom field textarea set to unsupported
FIXED: eventtop custom fields user role & login validation
FIXED: custom meta field icon not showing in event edit page
FIXED: styles for event map on lightbox eventcard
FIXED: undefiend author id in data_store object
FIXED: various repeat intervals not creating correct repeat days
FIXED: data store post_type value not passing through
FIXED: switched handlebar.js back to v4.4.3
UPDATED: repeat events edit user interface
UPDATED: user interface for event edit

= 3.0.3 (2020-11-12) = 
ADDED: option in event edit to hide live event progress bar
ADDED: new central data store object for all eventon based custom post types
ADDED: repeat event header appearance editing into setting
ADDED: EventON system log in eventon diagnose settings
ADDED: event postmeta database entries count to EventON environment data in settings
FIXED: feature event yes no button value not showing as saved
FIXED: shortcode generator collable sections
FIXED: cancelled events showing live now bar
FIXED: open single event in new window opening 2 tabs in firefox
FIXED: location archive page is_yes error
FIXED: event edit timezone GMT value adjusted to current time and event start time 
FIXED: GMT value to be adjusted based on event start on frontend
FIXED: day lights savings time not taken into effect for view in my time
FIXED: minor styles fixed for frontend
FIXED: location link open new window not working
FIXED: future repeat event times showing past events
FIXED: Location archive page location image error
FIXED: cancel events still showing virtual event details in eventcard
FIXED: event taxonomy archive page templates
FIXED: year & month long repeat event times showing full time
FIXED: live now bar super long for year long repeat events
FIXED: location image styling
UPDATED: all location information in eventcard to be linked if a location link given
UPDATED: handlebars.js to v 4.7.6

= 3.0.2 (2020-10-26) = 
ADDED: Option to hide GMT time value from front end calendar - in eventon settings
FIXED: minor styles changes
FIXED: time edit minute interval value issues
FIXED: view in my time button showing on calendar by default
FIXED: dark theme colors for new eventcard layout and other adjustments
FIXED: eventon secondary button type color appearance missing

= 3.0.1 (2020-10-23) = 
FIXED: Event list events not appearing
FIXED: Minor styles update

= 3.0 (2020-10-22) =
ADDED: shortcode ux_val 3a - to open lightbox content via ajax
ADDED: show after virtual event has taken place content
ADDED: after virtual event ended content to show at certain times
ADDED: user local time next to event time
ADDED: option to apply timezone value to all the events
ADDED: timezone appearances to eventON appearance settings
ADDED: shortcode option bottom_nav to add calendar navigation arrows to the bottom of calendar
ADDED: NEW [add_eventon_now] events happening now calendar view
ADDED: Live events to show real-time event progress bar
ADDED: Live events progress in real-time updating event time left
ADDED: When live event end, auto change live event progress bar and possible tags
ADDED: Event tiles to show event type & organize values
ADDED: event tiles support for single events via shortcode
ADDED: health guidelines for events, enabled via event edit
FIXED: Event options box not showing in event edit post
FIXED: Trying to access array offset on value of type bool in /plugins/eventON/includes/class-calendar-helper.php on line 119
FIXED: event list with multiple months with hide empty months not showing anything
FIXED: widget event title undefined issue
FIXED: location and organizer term name translation error
FIXED: date formatted time for event array value error
FIXED: create zoom meeting button not showing in new event page on page load
FIXED: search not showing results after clicking on load more events
FIXED: ux_val for open single event page (4) not returning on dynamic clicks
FIXED: all day events saving start seconds as 1 instead of 0
FIXED: eventon language settings changing not working
FIXED: tiles ux_val not setting to lightbox on default
FIXED: event tiles height value as min-height
FIXED: event tiles hover effect using translate instead of border adjust
UPDATED: new event card design styles
UPDATED: event details show more less button and section
UPDATED: event edit fields to use eventon elements
UPDATED: related events eventcard designs
UPDATED: event tiles styles

= 2.9.1 (2020-8-24) =
ADDED: Woocommerce related formating functions into evo_helper()
ADDED: Google meet and jit.si as option for virtual event broadcasting
ADDED: download event ics file to have URL of event with virtual event url
FIXED: zoom requiring jquery-form library
FIXED: event schema error with organizer url 
FIXED: add_eventon() not passing arguments when php - Thanks Mike Mabin
FIXED: custom map marker icon not working
FIXED: backend date picker to be RTL compatible
FIXED: several RTL style layout issues on backend
FIXED: widgets not saving values

= 2.9 (2020-7-8) = 
ADDED: option to disable virtual event access redirecting/ hiding
ADDED: hideable field values in eventon settings
ADDED: full integration with zoom meetings
ADDED: ability to create edit and delete zoom meetings from event page
ADDED: elementor - eventon shortcode generator for elementor editor page
ADDED: eventon elements which will be used standard everywhere in eventON
ADDED: shortcode option to select tile background image size
FIXED: shortcode generator intial load showing extra ]
FIXED: RTL calendar arrows to navigation opposite way
FIXED: feature event image pushing out of view on mobile
FIXED: gutenberg shortcode generator not reseting on new block or classic editor
FIXED: default featured event image not showing for some events
FIXED: load_google_maps_api() not loading correctly on the header
FIXED: sprint correct application on post registration
FIXED: virtual event link with duplicate / in link
FIXED: third party paypal payment event meta box not showing
FIXED: php 7.4 related code errors
FIXED: lightbox button color not changing
UPDATED: eventon wordpress widgets layouts
UPDATED: tooltip styles and animation
UPDATED: minor styles on various UI elements

= 2.8.10 (2002-6-11) =
ADDED: Event post compatible with gutenberg editor
ADDED: Shortcode generator based eventon gutenberg block
ADDED: Event post editable using gutenberg editor
ADDED: collapsable sections into shortcode generator
FIXED: organizer URL field causing issues in the schema data
FIXED: style misalign for learn more field. - Thank you Torsten S.
FIXED: category widget not unselecting the values when saved
FIXED: duplicate text in eventon language to be overridden with one value
FIXED: missing language options for virtual events
FIXED: event color hex code to be stripped to 6 characters
UPDATED: moved extra event images to a separate box
UPDATED: event edit row select field to be generalized for used 
UPDATED: eventon logo as SVG instead of font to cut down on file size and load time

= 2.8.9 (2020-5-19) =
ADDED: a trigger when eventcard slidedown is complete
ADDED: Dedicated virtual event section for events
ADDED: Ability to disable jquery mobile library from loading
ADDED: Event status field to quick and bulk edit for events in wp-admin
FIXED: quotation marks on event title breaking schema 
FIXED: schema event time missing timezone offset
FIXED: date format conversion to js format is not escaping \ correct
FIXED: schema virtual event location cause missing ] error
FIXED: event type widget with multiple shortcodes not working
FIXED: single event box RTL issue
FIXED: future events filter switched to all not working
FIXED: organizer not passing url field into schema
FIXED: bubble event month long event layout
FIXED: Settings checkbox icons not showing correct
UPDATED: eventon_get_repeat_intervals()function

= 2.8.8 (2020-4-7) = 
FIXED: cancel events not saving new event status value
FIXED: organizer image not resetting after adding an organizer
FIXED: backward compatibility with non used post meta values not deleting
FIXED: generate google map from location value getting disabled on new location select
UPDATED: shortcode clarification for event_order
UPDATED: compatibility with wordpress 5.4

= 2.8.7 (2020-3-24) =
ADDED: support for virtual location for events
ADDED: Schema support for virtual event location type
ADDED: event attendance mode schema data value for events
ADDED: event status value and ability to select various values for it
ADDED: reason for cancel and other event status be visible in eventtop
ADDED: Event status to also show in all event edit page
FIXED: edit events location fields saying organizer
FIXED: related events not capturing quotation mark in event title
FIXED: widegt arrows styles not correct
FIXED: event cancel reason with quatation marks not working
FIXED: export events as CSV no escaping organizer description for quotation
FIXED: settings radio options not working
FIXED: settings icon selection missing click area
FIXED: view button not showing in event location edit page

= 2.8.6 (2020-3-9) =
ADDED: Option to select to use organizer data as performer schema data as well
ADDED: single event page template to be able to pass lang value via code
FIXED: additional functionality box id duplication
FIXED: added missing reply-to implementation in evo helper send mail function
FIXED: invalid taxonomy translations string in code
FIXED: tax name not translatable for list selection in backend
FIXED: events processed list not getting reset in event generation function
FIXED: search not processing the language value
FIXED: Event count value not working for basic event list with multiple months
FIXED: shortcode generator yes no buttons not working in gutenberg
FIXED: wp-admin month filter to show 24 months instead of 12
FIXED: past_event class name incorrectly assign to all events
FIXED: taxonomy archive pages not convertable to different languages using lang
FIXED: event card open on default not loading maps
FIXED: lightbox event map not loading when multiple calendars in page
FIXED: map option values not reflecting on calendar
FIXED: ajax to not run eventon_init_load when there are no calendars on page
FIXED: event type archive page not passing language correctly
FIXED: for RTL month moving around when switching months
UPDATED: map loading function rebuild using new function method
UPDATED: time out between each calendar loading events via ajax when multiple cals in page
UPDATED: Event tiles styles and layout design 
UPDATED: to font awesome 5.12.1 library
UPDATED: license verification to use new envato API

= 2.8.5 (2020-1-14) =
FIXED: event type color not passing to get_tax_ids()
FIXED: Repeat interval custom not saving date correct
FIXED: event location names list getting cut off in the shortcode generator
FIXED: single event box show_exp_evc=yes not working when linking to external url
FIXED: wp-admin javascript text string translations

= 2.8.4 (2019-12-12) =
ADDED: ux_val=4a to open event as single event page in new window
FIXED: empty schema organizer undefined error
FIXED: schema description, name show multiple times in abbreviated versions
FIXED: schema organizer to have organization property instead of person
FIXED: event card open on default not loading google map
FIXED: language not passing to new months
FIXED: Calendar shell to set fixed_day if shortcode values empty line:197
FIXED: by default save lang corresponding value when saving events
FIXED: Date picker not saving time correctly for events
FIXED: Events lists to use new date object instead of using calendar date object

= 2.8.3 (2019-11-28) = 
FIXED: search all not sending results for some websites
FIXED: Map zoom level not processed on new months
FIXED: filter button to hide when no filters are selected in settings
FIXED: wp-admin filter events by past current error on missing value
FIXED: Repeating events not appearing in events lists
FIXED: Schema showing description as html

= 2.8.2 (2019-11-26) = 
ADDED: wp-admin filter events by event month
ADDED: Remove wp-admin all events default WP month filter 
FIXED: search box not showing results
FIXED: Event descriptions escape quotations on frontend
FIXED: disable schema on site causing code errors
FIXED: Other schema related issues with google structured data tester
FIXED: latlng only locations map not showing without address
FIXED: Quick edit end time not showing
FIXED: Repeat interval not linking right for 1st instance
FIXED: 1st of future repeat intervals not showing on eventcard
FIXED: evc_open shortcode not effecting on addons
FIXED: license activation on remote server PHP version code error
FIXED: Sunday translations not passing through to init data
FIXED: Removing input field value from shortcode generator to remove shortcode value
FIXED: search all not searching past events

= 2.8.1 (2019-11-19) =
FIXED: Widget social share icons
FIXED: user interaction open in external url not working
FIXED: ICS download all events to only include future events
FIXED: Related events remove button not working
FIXED: Google map not loading on new loaded months
FIXED: Search box results not working
FIXED: Widget upcoming events not working
FIXED: Location address not showing in the eventtop
FIXED: Event type color override to pass into events data object
FIXED: Events list multiple months cut off time errors
UPDATED: Shortcode generator to use custom wpdb queries instead of wp_query

= 2.8 (2019-11-12) =
ADDED: pluggable filter to change the eventtop below title date time format
ADDED: SEO json schema pluggable function eventon_event_json_schema_adds
ADDED: AJAX based event loading to hide show more events button when at end
ADDED: Show more events in calendar to change to loading view during ajax
ADDED: option to set 1 letter week day names in eventon > language
ADDED: Related events for each event
ADDED: ability to show only set event filter values in filter drop down SC:filter_show_set_only
ADDED: past and future filtering via shortcode SC:event_past_future 
ADDED: Shortcode based event filter relationship method SC:filter_relationship
ADDED: Basic events list to support event filtering for more than one month
ADDED: Ability to export eventon language as just text strings for easy translation
ADDED: Option in location term edit to use latlng for get directions location
ADDED: Event filter terms to have proper html class names
ADDED: Shortcode generator to be able to select event type filter values easily
ADDED: shortcode option to disable initial calendar loading via ajax using cal_init_nonajax='yes'
FIXED: JSON schema data error
FIXED: cron process events marking them as completed code error
FIXED: hide multiple occurance effecting multiple calendars on same page
FIXED: Cal_id variable passed with space to be escaped with dash
FIXED: get_next_current_repeat() not returning correct repeat data
FIXED: get_all_event_data() function meta field incorrect value
FIXED: yes no field passed value to be formatted to lower case
FIXED: Social share links on https
FIXED: Filter values not working correctly with multiple filters
FIXED: Correct event URL passing with lang variations in the url from EVO_Event()->get_permalink
FIXED: additional images box showing on other posts as well
FIXED: Custom date format to reflect on event edit date selection
FIXED: event location and organizer add new term text strings
FIXED: Widget check box clicking not enabling save button
FIXED: Shortcode generator changes not applying to shortcode in real time
UPDATED: Using PHP datetime function and moment.js for time calculations
UPDATED: pluggable filter eventon_evt_fe_time to support extra values for event time
UPDATED: Styles for the calendar elements and on frontend
UPDATED: calendar event filtering mechanism and loading shortcodes to calendar
UPDATED: Bubble events calendar hover animation

= 2.7.3 (2019-7-24) = 
ADDED: Filter evo_eventcard_time for eventcard time
ADDED: Ability to set constant characters for event card date time format
ADDED: pluggable filter evo_eventcard_repeatseries_start_dtformat to allow repeat event series time format editing
FIXED: Dynamic styles write to header cause posts to not save
FIXED: Dynamic styles escaping befor print to page
FIXED: calendar ux_val=2 not loading the learn more link from event
UPDATED: duplicate event function to support a pluggable filter with event object

= 2.7.2 (2019-6-28) =
ADDED: repeat events to be trashed if set when last repeat is over
FIXED: when sort buttons hidden event filter not working for switch months
FIXED: to hide sort button if no sort options selected in settings
FIXED: single event box external link opening 2 windows
FIXED: Shortcode generator functioning errors 
FIXED: open in new window for events not working
UPDATED: event photos filter for feature images
UPDATED: javascript trigger values for lightbox operations

= 2.7.1 (2019-6-4) = 
FIXED: auto trash past events deleting non past events
FIXED: minor style conflicts
FIXED: event set to open link open in new tab despite not set 
FIXED: Single event box opening external link as well as single event page 
FIXED: JSON LD data escaping issue with apostrphe
FIXED: hide sort and filter bar in settings not responding

= 2.7 (2019-5-30) =
ADDED: Event organizer description in event card
ADDED: ability to hide sort filter section on event lists via shortcode hide_so
ADDED: EventON settings diagnose environment data and stats
ADDED: 2 multi data type additional fields on default with filter
ADDED: wp-admin eventon lightbox content loading via ajax in a dynamic way
ADDED: Basic version of event photos addon into eventON
ADDED: New colorful eventtop calendar style
ADDED: new css class name to child event type terms in filters
ADDED: Auto set past event as completed
ADDED: Ability to disable scheme JSON-LD data everywhere except single event page
ADDED: Option to enable general google maps for all newly created events on default
ADDED: Ability to set multiple week of month to repeat for monthly repeat mode for events
ADDED: ability to prioritize month and year long events above others
ADDED: cancelled events to show that on all events list in wp-admin
FIXED: Events linking to external link to actually show that in HTML DOM
FIXED: Single event shortcode not updating shortcode when only one event
FIXED: past/current events filter not working when select filter type
FIXED: to run ajax json event data load on event click only is needed
FIXED: multi data type additional fields setting yes to not save correct
FIXED: EventON Widgets styling and layout
FIXED: JSON LD description escaping
UPDATED: calendar styles and UI & backend UI & layouts
UPDATED: filter and sorting design and layout
UPDATED: pass event location slug into location html
UPDATED: EventON settings support section and its layout
UPDATED: multi data types styles and layout
UPDATED: several text strings
REMOVED: discontinued google plus social share

= 2.6.17 (2019-3-1) =
FIXED: undefined map values error when settings are not saved
FIXED: Feature events from wp-admin leading to 0 error
FIXED: text strings with [] not able to save value
FIXED: multi data types to filter description using the_content wp filter
FIXED: add to calendar cross site scrip vulnerability
FIXED: adding new locations not generating latlon because of gmap API requirement
FIXED: disble sorting events in wp-admin using unsupported event location tax filter
FIXED: appearance event title color not changing on tiles view

= 2.6.16 (2019-1-31) =
ADDED: reply-to argument for evo helpder object
FIXED: event sorting not showing up under filters
FIXED: RTL Tiles date not showing correct
FIXED: Template loader to support additional taxonomies
FIXED: when search input text to show by default hide search completely
FIXED: evo_get_wp_events_array() function not applying shortcode argument values to events list
FIXED: learn more event card field showing as 50% width
FIXED: event main js file had unsed loop code
FIXED: missing eventon class in class-evo-admin.php file
FIXED: repeat instance buttons under dark theme
FIXED: repeating interval validation error on fnc get_correct_formatted_event_repeat_time()
FIXED: EventON datetime not processing the date correct when previously saved repeats available
FIXED: Widget featured image styles
UPDATED: frontend yes no button styles
UPDATED: when creating or setting new event location in backend to set generate map to on
UPDATED: Twitter share link with ../intent/tweet/

= 2.6.15 (2018-11-21) =
FIXED: EventON license key format validation error
FIXED: filters not showing by default on calendars

= 2.6.14 (2018-11-15) =
ADDED: eventon icon to wysiwyg editor icons panel
ADDED: quick edit events to include span till hidden end time
ADDED: Bulk edit support for events in backend
ADDED: EventON basic integration for elementor
ADDED: EventON basic integration for gutenberg
FIXED: social share icons row in eventcard does not have close button
FIXED: jumper styles not working correct
FIXED: eventcard sub titles color not applying to all
FIXED: end date not showing when its all day event
FIXED: single event shortcode box not passing lang correctly
FIXED: ajax unix sanitization function for string length 
FIXED: Event date time validation in backend
FIXED: month long event to only show one month name in tile view
FIXED: Incorrect tag closing in front end calendar
FIXED: wp_query_event_cycle() to include repeat interval value as well
FIXED: Event location to be able to open in new window option
FIXED: weekly repeats not showing in ascending order when start of week changed
UPDATED: EVO_Cal_Gen class to include function to set properties
UPDATED: license activation form validation 
UPDATED: yes no button style

= 2.6.13 (2018-9-27) = 
FIXED: AJDE library updates to shortcode interpretation
FIXED: mobile click on feature image not resizing
FIXED: cron schedule filter error
FIXED: not able to change event title color via appearance
FIXED: event type filters passing on empty values
FIXED: ICS add to calendar not passing timezone included value correct
FIXED: hide end time not reflecting when moving to new months
UPDATED: on load event content passing via json and ajax
UPDATED: Styles
UPDATED: new cal gen object to load calendar options on the load

= 2.6.12 (2018-8-15) =
ADDED: Sanitization to ics file download unix numbers
ADDED: social share title into languages for translation
FIXED: Month long events showing in other years
FIXED: hide featured events not working for other months
FIXED: Sort events by event posted date not working
FIXED: hide past events by start time not working is hide past value set in settings
FIXED: Variations addon showing twice under license
FIXED: month long events showing in incorrect months
FIXED: event top event title color not reflecting changes on tile mode
FIXED: event edit page showing fields when user interaction set to X
FIXED: several RTL compatibility issues on backend
FIXED: social share title missing on some
FIXED: slashes issue on location address 
UPDATED: EVO_Event class

= 2.6.11 (2018-6-21) =
ADDED: Location and organizer more fields to CSV export
ADDED: location map settings reflect on location archive page
FIXED: and (&) sign in event name breaking email share information
FIXED: Event list months not translating correctly
UPDATED: event object to globalize event post meta values

= 2.6.10 (2018-5-29) =
ADDED: Global eventon language definition
FIXED: Single event page not showing correct language
FIXED: lightbox events repeat events not showing correct language
FIXED: hide event end time not making span till end time field show
FIXED: Included handlebars library in admin event post page
FIXED: Organizer description not showing saved values
FIXED: Event custom meta data field strong and bullets not working

= 2.6.9 (2018-5-7) = 
ADDED: New SVG class object
ADDED: new forms object to handle all eventon forms
ADDED: MDT sanitize fields
ADDED: Support for event tag filtering
FIXED: All day events showing end date info
FIXED: activation lightbox tooltips not showing up
FIXED: input field psuedo colors bleeding into other areas
FIXED: Custom map styles not reflecting on location archive page
FIXED: Future event times in the repeating event series from event card not working correct

= 2.6.8 (2018-4-6) =
ADDED: new template class
ADDED: js handlebars library
FIXED: missing event start and end time from schema data
FIXED: repeats day of the week selection to match wp settings
FIXED: featured events not getting marked as featured
FIXED: do not delete option when eventON deleted, not working
FIXED: EVO_Calendar object to be constructed without any required values

= 2.6.7 (2018-3-26) =
ADDED: missing lang trnaslation for event text
ADDED: Day name on eventtop for multiday events as well
ADDED: New calendar object to process calendar settings faster with less codes
ADDED: hide end date via shortcode on calendar
ADDED: option to specify day light savings events to auto adjust time
ADDED: faster way to save and retrieve event data
FIXED: email share capitalize month name
FIXED: add to gcal leave - when there is no address
FIXED: Repeating event URL not passing correctly
FIXED: Event map addon passing incorrect addon ID for license activation
FIXED: current month button hiding when jumper go to next year same month
FIXED: search query checking for post_type exists
FIXED: Missing translations for past and future event filtering
FIXED: open in single event page not passing lang value to URL
FIXED: search on event list showing results as duplicated
FIXED: CSV exporter breaking column structure
FIXED: adjusting event time adding duplicate repeats
FIXED: CSV not exporting the organizer name correct
UPDATED: event object with smart formatted time method

= 2.6.6 (2018-2-7) =
FIXED: AJAX based load more events not working correct
FIXED: addon details actionuser plus ID correction
FIXED: Shortcode generator tooltips not showing
FIXED: All day events on multi days not showing end date
FIXED: datetime object get_int_correct_event_time return error
FIXED: Event inclusion in search results breaking bbpress forums
FIXED: load more events via ajax not reseting paged val
FIXED: subtitle with double quotes not saving correct
UPDAETD: Better URL structure for passing repeat event information
UPDATED: event edit meta box colors to a lighter colors

= 2.6.5 (2017-12-11) =
ADDED: Option to disable auto generated og: meta tags
ADDED: Notice for interchangeable shortcode usage
ADDED: Single event to be able to pass arguments using filter eventon_single_event_page_data
FIXED: all events wp-admin responsiveness
FIXED: Language saved text are not reflecting upon page reload
FIXED: Missing backend translation codes
FIXED: Facebook share image to be full size
FIXED: Single event not correctly using map scroll disable
FIXED: Deprecation handling method
FIXED: Quick edit not saving some of yes no values

= 2.6.4 (2017-11-29) =
FIXED: transient error on evo product class
FIXED: End day all day events not passing value correct 
FIXED: Settings page coming in blank
FIXED: Event maps license product ID not correct
FIXED: Event slider product ID error in licensing
FIXED: evo_check_updates cron hook name getting printed in the admin pages
FIXED: Corresponding language not saving in event edit page
FIXED: mobile lightbox positioning

= 2.6.3 (2017-11-20) =
FIXED: alphabetical sorting for addons list
FIXED: Setting changes not reflecting on reload

= 2.6.2 (2017-11-17) = 
ADDED: Deprecated class handing and recording for alternatives
FIXED: license activation not working

= 2.6.1 (2017-11-17) =
ADDED: Support for past and future event filtering on frontend
ADDED: Export events as CSV to include event id
ADDED: Remove invalid quotation marks from shortcode arguments
ADDED: function check for iconv() for users without support for that
ADDED: new remote auto updates system to eventon and addons
ADDED: remote updates subscription service for eventon products
ADDED: new filter evo_template_loader_file for template loading files
FIXED: featuring an event in wp-admin causing error
FIXED: Organizer open in new window not saving
FIXED: wp-admin styles not passing version
FIXED: ICS download file date format issue
FIXED: All day events passing all day text for end time as well
FIXED: Email share event name in email body not escaping correct
FIXED: event class maybe_unserialize return prop values
FIXED: email child theme support not working
FIXED: location coordinates not saving just by itself
FIXED: Location apostrophe
UPDATED: POT file updates
UPDATED: month jumper to show all months with no scroll
UPDATED: csv htmlentities separated to be able to turn off
UPDATED: Event id to be passed to eventtop
UPDATED: Event object class improvements
DEV: action filter when events auto trash

= 2.6 (2017-9-12) =
ADDED: ability to pass arguments to get_all_event_data() function
ADDED: globally accessible event post meta update ajax from wp-admin
ADDED: New calendar loader bar with cool knightrider like animation
ADDED: Support for access to new loadbar for eventon addons
ADDED: Support for event featured image ALT text
ADDED: Option to disable location and organizer link filtering for https
ADDED: new tile style with details under the tile
ADDED: location and organizer filter support into shortcode generator
FIXED: event end time not passing correct for dailyview addon
FIXED: Separate months event list showing 1 less event
FIXED: Single events template sidebar not position correct
FIXED: Universal time format for eventcard not applying to all day events
FIXED: hide end time events still showing end time
FIXED: external links with https not working for events
FIXED: twitter not including the URL
FIXED: Single event page styles on responsive
FIXED: event type archive page template layout issues
FIXED: location and organizer archive page layout issues and styles
FIXED: fixed month value with 0 yeild incorrect month
FIXED: wp_admin AJDE library table styles
FIXED: ajax based show more events causing filter events issues
FIXED: ICS add to calendar file time processing issue
FIXED: learn more link ux_val=2 not working
FIXED: location meta data not able to delete
FIXED: location description not saving and showing up in lightbox
FIXED: open in new window not working for ux_val & learn more link
FIXED: Google maps disable in settings to still work using other google maps APIs
FIXED: single event box repeat interval not passing correct to box data
FIXED: auto created events page to have page content as basic eventon shortcode
FIXED: load styles and scripts only on eventon pages
UPDATED: Possibility for extra filter text translation
UPDATED: evo helpers to support file attachment to emails
UPDATED: Helper styles for frontend
UPDATED: event tax archive page layout design update
UPDATED: eventon language saving for duplicate text strings
UPDATED: new event date time function using event id
UPDATED: location info over image styles and layout updated
UPDATED: eventon back end button styles
DEV: get_terms instead of get_categories to get event taxonomies for filter

= 2.5.5 (2017-7-4) =
ADDED: End year to show in eventtop with option to hide
ADDED: Option to set custom login URL
ADDED: Option to disable html special character decode on ICS file
ADDED: New cron class to handle similar tasks for addons
FIXED: error on eventon uninstall
FIXED: no events text on list view echoing instead of returning
FIXED: admin location and organizer not translating in event edit page
FIXED: tiles with ajax load more leaving clear div 
FIXED: ajax load more switching months showing incorrect events
FIXED: disabling schema data to break calendar layout
FIXED: organizer link formating correctly
FIXED: location link formating correctly as well
FIXED: not able to delete plugin via wp admin page
FIXED: Search bar styles overriding by theme styles for some users 
FIXED: missing argument error on evoadmin_get_unix_time_fromt_post()
FIXED: search not working when enabled via settings
FIXED: external link input field showing for lightbox UX option in event edit page
UPDATED: Minor style changes
UPDATED: calendar date styles and HTML structure
UPDATED: Minor updates to single events template codes

= 2.5.4 (2017-5-26) =
ADDED: seperate styles sheet for backend RTL styles
ADDED: Language corresponding event capabilities
ADDED: JSON-LD structured data for events
ADDED: Option to enable search for all calendars by default
ADDED: Get directions to use lat lon if address not provided
ADDED: Ability to use NOT-ALL to hide all events assigned with certain event taxonomy terms
ADDED: pluggable funtions on calendar evo_cal_eventtop_attrs
ADDED: Ability to run PHP codes on website via eventon settings
ADDED: Pluggble filter for taxonomy page shortcode used
ADDED: AJAX based pagination for loading new events
ADDED: Featured events tag on eventtop
FIXED: tile minor styles
FIXED: backslashes not getting stripped on organizer meta information
FIXED: Single events page showing multiple widgets
FIXED: press enter key to search not working
FIXED: search minor styles 
FIXED: Export events as CSV event date to be in matching date format for CSV Importer
FIXED: Schema based structured data errors
FIXED: Stripslashes for organizer address and contact data fields
FIXED: location and organizer button translatability
FIXED: Single event template function requireing a paramater
FIXED: Tiles feature image not showing background image
UPDATED: By default search be disable for calendars
UPDATED: Addons list on license page
UPDATED: AJDE library
UPDATED: all events page to show all day text instead of time

= 2.5.3 (2017-5-4) =
ADDED: event edit page to support location and organizer setting via ajax
ADDED: search addon is now a part of eventon
ADDED: ability to search on event type 2 and 3 with advance search
ADDED: Location with city, state and country data
ADDED: Search to be available in event list version of calendar as well
ADDED: Search box to be able to search all past and current events
ADDED: Option to show end time as well in repeat instance
ADDED: Option to hide add eventon shortcode generator button from backend
ADDED: Option to hide comments section on single event page
ADDED: global option for location to be hidden from non-loggedin users
ADDED: Option to download all events as ICS file from frontend
FIXED: MDT custom html code for extra field not getting stripslashes
FIXED: add to google calendar link compatibility with https protocol
FIXED: monthly and weekly repeat options incorrection on event edit page
FIXED: repeat interval time in eventcard be formatted with translated month names
FIXED: PHP7.1 conflicts in language edit page & other places
FIXED: export events as CSV to support csv importer date time formats
FIXED: saving date proper conversion for altered date formats
FIXED: External link for open event in single event page not complying with https
FIXED: ics export all events not importing correct
FIXED: Twitter sharing 2 links to events 
FIXED: custom repeats not saving on newly added events
FIXED: Language not saying in PHP 7.1+
FIXED: Tile view not switching to one tile in a row in mobile view
FIXED: Add to calendar ICS file formatting fix
FIXED: evo_get_long_month_names() not returning correct translated month names
FIXED: isset error on function get_int_correct_event_time()
FIXED: style adjustments for extra <p> tags added inside calendar by some themes
FIXED: deactivated multi data types not hiding from left menu
UPDATED: Select2 updates to version 4
UPDATED: Single event page template code

= 2.5.2 (2017-3-30) =
ADDED: filter hook for eventcard event details text
ADDED: Support for hide fearured events from a calendar using hide_ft='yes'
ADDED: event edit page ability to delete the initial repeat instance of custom repeat
ADDED: Eventtop time block pluggable filter evo_eventtop_day_block
ADDED: pluggable support for backend filtering options
ADDED: pluggable map styles and new map styles
ADDED: Searchable select fields for location and organizer
ADDED: Pluggable function support for schema data for events
ADDED: export events as CSV to comply with WP date/time format if set via settings
FIXED: ICS and google cal times were off by couple of hours
FIXED: Minor styles
FIXED: RTL styles update
FIXED: Some date/time formats not saving the correct value
FIXED: when none of terms selected from filtering checkbox count that as none instead of all
FIXED: Social share icons background color fix
FIXED: Repeating instances delete not completly hiding the row
FIXED: custom map marker not showing in single event pages
FIXED: event subtitle with apostrophe not showing correct in wp-admin
FIXED: Google maps custom styles undefine error
FIXED: featured events not move to top with show more events option
FIXED: error on evo_lang_e() function lang variable
FIXED: Facebook share not working
FIXED: lightbox event details paragraph padding bottom
FIXED: PHP 7.1 saving events save event time as current time
FIXED: dynamic styles file loading issues on https for multisites
FIXED: Widget title not showing correct for some themes
FIXED: language unsupported taxonomy names in filtering bar
FIXED: repeating event URL cross scripting vulnerability
FIXED: Quick edit issues
FIXED: Custom repeats when delete a repeat and add new not saved correct
FIXED: new months showing old month events during ajax call
FIXED: Some widget icons for eventcard not hidden
FIXED: Export events as CSV convert all info to htmlentities()
UPDATED: Various minor style updates for frontend layout
UPDATED: Eventon email helper function to support correct from email format
UPDATED: extended support for more fields under quick edit 
UPDATED: Repeating interval value convert passed value to integer
UPDATED: map styles preview images
UPDATED: Schema data structure
UPDATED: Close button styles for lightbox

= 2.5.1 (2017-1-23) =
FIXED: License activation not working & not saving the key
FIXED: Custom field button not opening links correct
FIXED: Missing language translations for multi data types
UPDATED: Deactivate single events addon if installed
UPDATED: Made organizer fields pluggable

= 2.5 (2017-1-17) =
ADDED: All new calendar design styles
ADDED: Option to click repeating event series within eventcard
ADDED: Option to stop using eventon single event template for single event pages
ADDED: View archive page for event location tax term edit page in admin
ADDED: Option to remove schema everywhere except single event pages
ADDED: option to remove month header from basic event list version of the calendar
ADDED: Ability to concatenate addon styles into one file for faster load times
ADDED: Ability to change event text string value quick from settings
ADDED: Option to make location information only visible to logged-in users
ADDED: Option to show login required message for custom fields thats only visible to logged-in users
ADDED: Links to various documentation articles to help common questions in settings
ADDED: Multi data types for events
FIXED: location address strip slashes
FIXED: Backend event edit afterstatement not working for yes/no buttons
FIXED: Single event template overriding not working correct
FIXED: placeholder value not showing in settings textarea field type
FIXED: Twitter share missing link to event
FIXED: eventcard and eventtop background-image quotation marks
FIXED: Event edit page locaton and organizer dropdown order ASC
FIXED: Eventon settings collapse not working
FIXED: Language variable not passing for single event clicks
FIXED: Location address not passing in add to google cal button
FIXED: Location information missing in ICS add to calendar
FIXED: Repeat interval share not passing correct repeat interval value
FIXED: Month repeats saving initial dates twice
FIXED: Custom eventon only time offset not correctly adjusting time
UPDATED: compaibility with php 7.1
UPDATED: Better organizsation of font styles for easy changing fonts
UPDATED: ux_val set to 4 or X to not load eventCard content when switching months for faster loading
UPDATED: License activation system

= 2.4.10 (2016-12-1) =
ADDED: Ability for addons to load styles into one file for faster loading styles
ADDED: Option in settings to offset add to calendar times
ADDED: Weekly repeats to be able to select day or the week
ADDED: Pluggable support for single event sharable options
ADDED: Further edit location from event edit page button
ADDED: Location description to be shown with location image when available
ADDED: Jumper years count be changed via shortcode jumper_count value
FIXED: Better language variable name filtering
FIXED: wp-admin click outside lightbox causing other scripts to not work
FIXED: Styles for text over location image for lightbox event
FIXED: Excerpt function word count issue
FIXED: Image not sharing on facebook
FIXED: Appearance theme selection not saving
FIXED: Location address was not passing to get_all_event_data()
FIXED: Event type #5 not showing up
FIXED: Make sure custom repeat values pass integer
FIXED: Custom repeats saving correct repeat intervals
FIXED: RTL Styles & passed rtl class into body class
FIXED: eventcard and eventtop event image missing quotation marks for url
FIXED: Email share event to contain event date and remove dummy to email
FIXED: load more events redirect not working when switching months - thanks @rootheng 
FIXED: Mobile yes/no button clicks not working
UPDATED: Html decoding for no event text string with html code
UPDATED: shortcode generator styles
UPDATED: Font awesome icons to version 4.7.0

= 2.4.9 (2016-10-18) =
FIXED: Click outside lightbox close is back
FIXED: Backend edit event page styles missing
FIXED: Location address apostrophe escaping
FIXED: Missing social share icons
FIXED: Missing single event sidebar

= 2.4.8 (2016-10-13) =
ADDED: Single events addon has been merge into main eventON plugin now
ADDED: Ability to show year on eventtop
FIXED: Other style issues
FIXED: Tiles image not expanding all the way & styles
FIXED: Date time not saving - set Y/m/d format when saving time for event
FIXED: more/less button not working on lightbox
FIXED: Addon versions not correct on settings page

= 2.4.7 (2016-10-7) =
ADDED: Support for menu order for events with same time date
ADDED: Support to expand event type categories more than 5
FIXED: Location name not passing to eventop for some
FIXED: hide multiple occurance not working on multiple calendar on same page
FIXED: Select filtering type not showing multiple filters passed via shortcode
FIXED: Get directions not working for duplicated events
FIXED: Fullscreen jump months moving too far
FIXED: Custom repeats initial save removed first repeat instance
FIXED: Fixed apply filters missing translation
FIXED: location name over image not saving value
FIXED: organizer field value not showing in eventtop
FIXED: Organizer open in new window not working
FIXED: curl install check when generating latlon for event locations
FIXED: All day events to last till end of last day hour
FIXED: frontend lightbox map not working
UPDATED: AJDE library wp-admin lightbox window elements
UPDATED: youtube embed link proper content filter with eventon (thanks grupopolen for code)
UPDATED: changed the way location & organizer tax data saved for less querying
UPDATED: Concatenate location and location name in eventtop to one line
UPDATED: Featured image UI on event top

= 2.4.6 (2016-9-5) =
FIXED: Mousewheel missing script error
FIXED: last of month repeat not saving correct date
FIXED: Single events page event card translation not working
FIXED: Jump Months names not translating with eventon system
UPDATED: Jump month update when arrow change month
UPDATED: Previous saved locations transition to location taxonomy method
UPDATED: Organizer link not filling in backend

= 2.4.5 (2016-8-25) =
ADDED: shortcode option to show jump months expanded on load
ADDED: mousewheel scrolling and swiping for months in jump months
FIXED: Yes/no button script update
FIXED: multiple checkbox filter not working correct
FIXED: Duplicate event lead to blank page
UPDATED: calendar loading UI and function accessibility
UPDATED: UI for jump months

= 2.4.4 (2016-8-17) =
ADDED: Select multiple checkboxes for filters as option via shortcode
ADDED: Option to hide filter dropdown item icons
ADDED: Ability to show other future repeating instances of event times on eventcard
FIXED: Featured image select not working for other posts
FIXED: Edit location term page to auto generate latLng if not set
FIXED: htmlentities function to be used when exporting events
FIXED: Missing organizer address field in eventcard
FIXED: Contactform 7 duplicate form causing conflict
UPDATED: internationalization for shortcode strings
UPDATED: Styles for h4 on popup event window
UPDATED: Event lists code to support similar footer pluggable filters as main calendar
UPDATED: Widget construct function to be compatible with newer PHP
UPDATED: Filter priority for event types in calendar
UPDATED: Main calendar AJAX to pass processed events list on results
UPDATED: Exporting events to use htmlentities() for data values

= 2.4.3 (2016-7-10) =
ADDED: Support to include google maps API key
FIXED: location image not working on location edit page
FIXED: Eventtop custom fields that are buttons to look like buttons
FIXED: Multiple NOT filter values not working

= 2.4.2 (2016-6-28) =
FIXED: Organizer data not showing in eventcard for some
FIXED: Error on frontend eventon email template function
FIXED: license activation on https website not working

= 2.4.1 (2016-6-21) = 
FIXED: error on certain admin level pages
FIXED: Addon activation issue
UPDATED: PO language file
UPDATED: minor updates to events generation code

= 2.4 (2016-6-20) =
ADDED: pluggable hook for maximum event type taxonomy count
ADDED: Ability to download ICS file of all events from backend
ADDED: Ability to export and import general settings for eventON
ADDED: Organizer archive page to have map using organizer address
ADDED: Event type #1 icons support
ADDED: default featured image for events
ADDED: Custom meta data icons to be visible on eventtop
ADDED: New template file location function for addons
ADDED: Dynamic event types into shortcode generator
ADDED: Template locator that will be used in addons for customizing templates
ADDED: Tiles shortcode options into shortcode generator for list version
FIXED: https loading assets correctly
FIXED: event type color picker not working on event type edit page
UPDATED: Cleaner looking yes no buttons
UPDATED: Separated admin ajax functions from frontend

= 2.3.23 (2016-5-18) =
ADDED: Option to set organizer link to open in new window
ADDED: Year long and month long events to show applicable year/month
FIXED: sites without visual composer error
FIXED: some event class names not passing to event elements
FIXED: get directions to location field missing
FIXED: Font awesome font files not loading correct
FIXED: All events to show location columns using taxonomy data
UPDATED: script update to ajde library

= 2.3.22 (2016-5-6) =
ADDED: Basic Visual Composer element for eventON
ADDED: Month long events - support for addons coming after release
ADDED: filters to allow location and organizer taxonomy slug name
ADDED: Event Locations to auto save location coordinates for faster map generation
ADDED: Option for select google add to cal or ICS download
ADDED: Events from next month widget
ADDED: Completed event option for each event with line through event title
ADDED: event edit page, saved locations and organizer fields to be hidden if set
FIXED: Sort and filter dropdowns not closing up correct
FIXED: Styles for sorting and filtering section
UPDATED: Existing locations to generate loc. coords on update
UPDATED: font awesome fonts library to version 4.6.2

= 2.3.21 (2016-4-19) =
ADDED: show more events button be able to redirect to a link
ADDED: Terrain option for google maps
ADDED: edit event link direct into eventTop works with lightbox & sliderDown interaction
FIXED: event list sorted by end date as secondary sorting after start date
FIXED: Featured event priority not showing all featured events
FIXED: Addons list not showing up
FIXED: Location text in event card missing in language settings
FIXED: Eventcard with no fields but addon fields not working
FIXED: go to today button showing when filtering and sorting
FIXED: Stripslashes on location address in eventcard
FIXED: eventon_get_unix_time() return correct times for all day events
FIXED: Eventon version not showing correct & addons not listing out in addons tab
FIXED: event list ext addon event count not working
FIXED: event list ext addon featured image not working
FIXED: deleting plugin will not delete evo settings
FIXED: License tab showing incorrect eventon version
FIXED: location and organizer drop down set to select a location reset & clear all fields
FIXED: Event time not showing without location info
UPDATED: child theme based template loading support

= 2.3.20 (2016-3-9) =
ADDED: Option to show eventtop various data even on widgets
ADDED: Option to show eventtop various data even on widgets
ADDED: improved troubleshoot tab in eventON settings
FIXED: organizer link return null
FIXED: Reset permlainks button in eventon settings
FIXED: custom time string support eg G \h i \m\i\n
FIXED: organizer name not saving in backend
FIXED: Event edit meta boxes to be echoed into back instead of using buffer
FIXED: Hide past events not working when event lists addon is installed
FIXED: export events were not saving  
FIXED: single events repeat variable to check is repeats and enabled
FIXED: Location category name not saving in language
FIXED: Datetime class not returning correct repeat interval
FIXED: Export all events to export repeat data
FIXED: Location and organizer archive page text not translatable
UPDATED: Organizer archive page to include missing data
UPDATED: pot file for backend language translation

= 2.3.19 (2016-2-18) =
FIXED: Location Latitude and longitude not saving and showing map
FIXED: Dynamic styles for lightbox
FIXED: Subtitle for event with quotation marks not saving
FIXED: Do not do anything option in event edit page not working correct
FIXED: Minor security vulnerability with addons settings page
FIXED: Event top tags not changing colors via apperances
FIXED: download all events as CSV to include organizer and location term IDs
FIXED: Hide multiple occurance and show repeats options not working

= 2.3.18 (2016-2-9) =
FIXED: event top tag styles for lightbox version
FIXED: Security vulnerability on download all events CSV action
FIXED: tiles version events not clicking through 
UPDATED: AJDE settings library

= 2.3.17 (2016-2-4) =
FIXED: eventtop above title error on code
FIXED: events meta box php file error on location term
FIXED: Update to 2.3.16 file error on incorrect $p_id
FIXED: welcome screen not going away after initial view

= 2.3.16 (2016-2-1) =
ADDED: new filters into event top above title of the event
ADDED: Option to select time minute increment for event edit time in backend
ADDED: Event Oragnizer to have address field
ADDED: Do nothing user interaction option per each event
ADDED: Location link suport that will open on new window
FIXED: Navigation arrows in IE fix
FIXED: single events page click not working
FIXED: Disable google maps not disabling correctly 
FIXED: Undefined post content in evo helper function
FIXED: Event edit page custom image holder styles
FIXED: ux_val=1 not overriding individual event uxvals
FIXED: Long words in event description wrap in CSS
FIXED: ics add to calendar characters convert to HTML entities
FIXED: CSS for Search bar right align
FIXED: calendar loading class name conflict with theme styles
FIXED: First font awesome icon being blank and updated font awesome icons to 4.5
FIXED: Location name with commas creating multiple location tax terms
FIXED: location name and address not passing correct in the ics 
UPDATED: evo_datetime class to support hide end time
UPDATED: Auto move trash events are now a daily schedule event in WP 
UPDATED: Auto update sequences for eventon updating from old versions
UPDATED: Repeat events to show correct time in paypal button
UPDAETD: Event location and organizer information now pull from respective taxonomies
TEST: NOT-X event filtering works

= 2.3.15 (2015-12-22) =
FIXED: Javascript error with $

= 2.3.14 (2015-12-20) =
FIXED: font awesome fonts not working on backend
FIXED: conflict of 'my_restrict_manage_posts' function
FIXED: parallex website's scrolling not working
UPDATED: Certain time format not working correct eg. G:i

= 2.3.13 (2015-12-9) =
ADDED: Subtitle into quick edit section
ADDED: AM/PM translation for other languages
ADDED: Event id as class name for userinteraction lightbox 
ADDED: Multiple options for event content filtering in settings
ADDED: Location name to ICS add to calendar file download
ADDED: Filtering events in wp-admin based on past or current events
FIXED: Event type color override not working when eventtop fields are not active
FIXED: Show more events button to work with tile design
FIXED: Missing translation for organizer and location under sorting options
FIXED: User interaction conflict between calendar and individual event
FIXED: Event card not sliding for some with theme conflicts
FIXED: Language import export not recognizing the file format
FIXED: Support for (th,st,nd) type date format in date picker
FIXED: location and organizer archive pages hiding multiple occurances of repeating events
FIXED: Dulicate event issue
FIXED: Mobile map scroll disable option sync with web scroll wheel 
FIXED: Repeating events not saving end time correct
FIXED: map Javascript error when no map element present on eventcard
UPDATED: Random calID generated on frontend calendar to avoid calendar conflicts on page
UPDATED: event minutes selection to be increments on 1 minute
UPDATED: Day of the week repeating monthly to support last week of March type repeats
UPDATED: New lightbox styles for better user experience
UPDATED: RTL styles
UPDATED: AJDE library with new font awesome v4.4 icons and bug fixed
UPDAETD: POT files for language 
TESTED: event_type 3 filtering works

= 2.3.12 (2015-10-29) =
FIXED: Eventlist ext. addon compatibility
FIXED: Shortcodes not executing inside event details
FIXED: safari styles breaking for the rest of the site
FIXED: events arent sliding down for some because of <p> inject
UPDATED: Go to today changed to current month - text change

= 2.3.11 (2015-10-26) =
ADDED: Ability to hide month arrows via shortcode per calendar
ADDED: Option to align month navigation arrows to right side of the calendar
ADDED: Pluggable filter to increase custom meta fields per event
ADDED: Option to set featured image as a regular image
ADDED: Ability to random order events
ADDED: Ability to show events only to logged in users
ADDED: Placehodler text for language month and date names
ADDED: separate class name for past events
ADDED: Event organizer archive template page
ADDED: Ability to add custom paypal emails to invidual events
ADDED: Learnmore link to CSV export events
ADDED: Organizer external link field
ADDED: Option for only admin and loggedin users can see custom meta fields
FIXED: Google maps not working on some sections 
FIXED: Add to google calendar desription showing title
FIXED: Proper time passed to basic paypal checkout page
FIXED: duplicate events save correct event link
FIXED: Event organizer image not working from dropdown menu
FIXED: Custom map styles preview image not working
UPDATED: Removed max repeating times restriction
UPDATED: Map zoom level control to location taxonomy page template
UPDATED: New filter content function for event content
UPDATED: Map zoom control not showing up
UPDATED: Compatibility to eventList ext. addon v0.8
UPDATED: Google cal add to calendar to have excerpt of event details
UPDATED: All events wp-admin to show event times
UPDATED: Placeholder text for language each item box
UPDATED: Paypal settings box UI for event edit page
REMOVED: eventbrite and meetup support

= 2.3.10 (2015-9-21) =
ADDED: Missing this month button appearance into settings
FIXED: end time not saving correct on event edit page
FIXED: Repeating events not saving correctly (thanks oliver from germany)
FIXED: organizer image not to be a square but medium size
FIXED: Eventtop event type not getting translated correctly
FIXED: location and organizer images not working on taxonomy term pages
FIXED: Colorpicker not loading right on event type term pages

= 2.3.9 (2015-8-25) =
FIXED: Compatibility with WP 4.3 widget error
FIXED: error when language settings saving
FIXED: Not able to feature events via star icon & other permission issues

= 2.3.8 (2015-8-5) =
FIXED: BCC email error on helper class
FIXED: Time cutoff to wordpress site's local time
UPDATED: Repeat intervals in eventon datetime class
UPDATED: email helper class & removal of HTML email filter when finished
UPDATED: to auto sync dynamic styles after update to new version

= 2.3.7 (2015-7-22) =
ADDED: Support for location cards for event locations
FIXED: Event top tags showing regardless of settings
FIXED: export events as CSV start time var name error
FIXED: ics summary and description correctly specifying title
FIXED: ics file summary going into new lines and breaking description
FIXED: repeating events not linking correct to single events page
FIXED: https error for chrome version 44
UPDATED: Featured image responsive styles for mobile
UPDATED: organizer field in language settings

= 2.3.6 (2015-7-9) =
ADDED: New AJDE library to help scaling of eventon
FIXED: Organizer image not able to save
FIXED: Repeating days of week not selected correct after save
FIXED: Organizer contact field not saving correct
FIXED: Custom meta data field titles not translating in event card
FIXED: Tile styles
FIXED: Slashes in location name
UPDATED: Styles for tiles layout mobile view
UPDATED: Event top event types be seperated by commas

= 2.3.5 (2015-6-9) =
FIXED: Individual event user interaction not working
UPDATED: Eventon helper function to support bcc type emailing
UPDATED: code yesno value for settings

= 2.3.4 (2015-5-28) = 
FIXED: Uninstall error
FIXED: event edit page not showing event settings
 
= 2.3.3 (2015-5-28) = 
FIXED: repeat events custom not saving times correct
FIXED: User interaction not working correct on tiles
FIXED: new shortcode variable to show repeating events while hide multiple occurance of event is active
UPDATED: POT language file with new and missing strings
REMOVED: eventon shortcode button from wysiwyg editor due to multiple conflicts with themes

= 2.3.2 (2015-5-20) =
ADDED: Ability to hide individual data in event card
ADDED: Support for event tags
ADDED: Ability to style featured events from appearance
ADDED: ability to sort events by posted date
ADDED: Option to not delete eventon settings when deleting plugin
ADDED: Ability to import and export language translations for eventon
FIXED: fullcal and dailyview not moveing featured events up
FIXED: Subscriber not showing for other views of calendar
FIXED: Tile layout to allow single event page clicks
UPDATED: Events tax in the calendar
UPDATED: Users taxonomy for calendar
UPDATED: minor code fixes
UPDATED: font aweosme icons to version 4.3

= 2.3.1 (2015-4-22) = 
FIXED: Missing translation for go to today
FIXED: Event Card not showing for single events page
FIXED: Event type categories not working correct
FIXED: Hide filtering options dropdowns
UPDATED: Events linking to external links to not load event card HTML
UPDATED: Missing event cancelled in language
UPDATED: JQuery triggers for goto today button

= 2.3 (2015-4-15) =
ADDED: Tile based layout for event calendar
ADDED: Timezone text support for event time
ADDED: Go to today button for calendar to return to current month
ADDED: Ability to export events 
ADDED: Support for event type category archive page
ADDED: Ability to add image for organizer
UPDATED: Minor updates to javascript for eventon
UPDATED: Re-organized files for plugin
UPDATED: theme and styles to incorporated missing new elements
UPDATED: ICS file slug causing errors
FIXED: Foreach error on language tab for RSVP
FIXED: Tooltip not showing up correct on page
FIXED: Cancel event styles on lightbox version
FIXED: google map not working off lat lon values
FIXED: repeating interval url when first creating event
FIXED: Year long events not appearing in addon versions

= 2.2.29 (2015-3-27) =
FIXED: Event location in eventcard and eventtop display errors
FIXED: Filter not working error caused by small mistake

= 2.2.28 (2015-3-25) =
ADDED: location name to schema for event
ADDED: shortened event description to ICS file instead of name
ADDED: event type category terms language translation
ADDED: Ability to remove meta data for eventon generator version
ADDED: Ability to cancel events and show on calendar
FIXED: NOT filter in shortcode not working when switching months
FIXED: Calendar ux_val to override event ux_val
FIXED: Event Paging setting to show all available pages
UPDATED: Event card HTML to not load when not needed
UPDATED: Addons not saving activations fixed
UPDATED: Language translations for admin side
UPDATED: Sorting custom repeat intervals on first save
UPDAETD: Get directions to use https
UPDATED: Improvement to code handling and reusage of code
UPDATED: main lang translation function
UPDATED: Support for event type translatable text
UPDATED: POT file for admin translations


= 2.2.27 (2015-2-24) =
ADDED: Location name to schema data for events
FIXED: Only featured events cal not working when switching months
FIXED: Location image picker not working in event edit page
UPDATED: Included class names for event type filter terms on frontend

= 2.2.26 (2015-2-14) =
FIXED: Settings not saving correctly for eventon

= 2.2.25 (2015-2-13) =
ADDED: Support for location and organizer filtering in shortcode
DEV: new action hook evo_cal_after_footer
UPDATED: Location and organizer taxonomies to show tax term id in wp-admin
UPDATED: addon deactivation process minor bugs solved
FIXED: Trash events past date to only delete events
FIXED: UID for .ICS file to stop replacing multiple ICS add to calendar
FIXED: Event list sorting not working fixed
FIXED: Widget class missing error
FIXED: All day events to save actual beginning and end of day times when saved
FIXED: Location name not showing in eventtop

= 2.2.24 (2015-2-9) =
ADDED: Basic upcoming widget to widget collection
ADDED: Event type category widget to show only certain events in widget
ADDED: Ultra repeater - now you can set custom repeating event times
ADDED: Submenus to the eventon settings menu
ADDED: Ability to feature event from edit event page
ADDED: Reset permalink button to eventon settings for easy access
FIXED: EventON Settings custom field count not working
FIXED: Styles for lists inside event details
FIXED: EventON theme file not loading error
FIXED: Event details more less background gradient colors
FIXED: hide_so shortcode variable with filtered events lists
UPDATED: Eventon license activation and updating system
UPDATED: Minor featured image size settings corrections

= 2.2.23 (2015-1-20) =
ADDED: Option to diable font awesome font from loading
FIXED: Events not showing up in the calendar for some
FIXED: Other minor bugs

= 2.2.22 (2015-1-13) =
ADDED: Support for google maps custom color styles
ADDED: Extra google map zoom level
ADDED: New Calendar theme feature - still at beta stage and more themes in future
ADDED: Month arrow color into appearance section
FIXED: Minor issues with license activation process
FIXED: Undefined settings error on eventon settings page
FIXED: Some of the missing styles added to appearance
FIXED: Hide organizer not working
FIXED: Incorrect featured image open status

= 2.2.21 (2014-12-18) =
FIXED: Widget arrow positioning
FIXED: End time still showing even when hide end time selected
FIXED: Arrow styles
FIXED: Single events page showing open event cards for sidebar
FIXED: Show more events not working on event lists calendars
FIXED: ActionUser users variable support
FIXED: Languages not working correct on sort options section
FIXED: https for backend datepicker
FIXED: Last day of months not showing solved with setting UTC timezone
FIXED: Codestyling localization compatibility 
UPDATED: Activate debug report section

= 2.2.20 (2014-10-13) =
ADDED: Backup shortcode generator in settings
FIXED: Saving events without time cause undefine error solved
FIXED: Sorting not working
FIXED: Custom meta fields more than 3 fields not working correct
FIXED: Mobile eventTop tap not working
FIXED: Event color hex code processing incorrectly
FIXED: Jumper not moving when changing months
FIXED: Arrow styles to better work for all
UPDATED: Featured image height style options for better visual
UPDATED: month arrow JS to work off body DOM tree
UPDATED: POT files

= 2.2.19 (2014-9-18) =
FIXED: Sorting and filtering not working for eventon and addons
FIXED: Events lists month name fix
FIXED: Updated available on eventon settings addon tab
FIXED: W3C validation fixes

= 2.2.18 (2014-9-16) =
FIXED: Minor style issues
FIXED: Sorting not working error
FIXED: ux_val=X not working
FIXED: EventCard open by default settings fixed
FIXED: Show more events text missing in language
FIXED: Google fonts for SSL based https urls
UPDATED: Different method to check installed addons

= 2.2.17 (2014-9-10) =
ADDED: Mobile tap on eventtop and jquery mobile support
ADDED: Upto 10 custom meta data fields for events can now be activated
ADDED: Event Location and organizer fields as filtering options
ADDED: Support for NOT event type taxonomy eg. event_type="NOT-23" will exclude tax tag 23 events
ADDED: Location name and address over location image
ADDED: View more events button for calendar to show events as needed
ADDED: The ability to offset jumper start year
ADDED: New event paging section to settings to manage event archive page templates and slug
FIXED: Featured image height 100% fix
FIXED: Location Image dissapearing when updating events
FIXED: Location address and name with aphostrophe not saving correct
FIXED: Event Card open by default not working properly
FIXED: Event type color override is not working on calendars
FIXED: Event type 2 term tags not showing correctly on event top
FIXED: Generate google maps default yes value not working
FIXED: All day event date name capitalized properly
FIXED: All day events showing incorrectly on eventcard
FIXED: Category translation not working on calendar
UPDATED: Addons page not showing installed addons
UPDATED: All addons to support new eventon exists check
UPDATED: Location and organizer meta box in event edit page
UPDATED: Remove _blank on get directions for only mobile
UPDATED: JQuery UI CSS to 1.11 version 
UPDATED: Event Edit page UI

= 2.2.16 (2014-8-19) =
FIXED: Jquery nodevalue error when passing shortcode arguments between months
FIXED: Featured image at full height when switching months
FIXED: Minor style issues
FIXED: Minor bugs related to eventtop fields
UPDATED: User capabilities for actionUser compatibility

= 2.2.15(2014-8-13) =
ADDED: WPML compatibility
ADDED: Event Subtitle can now be added and styled
ADDED: The ability to select all categories to be shown on eventTop
ADDED: Event Location Image field - this need to be configured in settings first to show
ADDED: Organizer meta field with similar event location method
ADDED: Disable onClick zoom effect on event featured image
ADDED: Ability to create year around event without a specific date
ADDED: Ability to auto trash old event posts from wp-admin
ADDED: Option to hide sort options section per each calendar in the shortcode
ADDED: Shortcode generator to ESE widget
ADDED: Class names to custom meta fields so styles can be applied
UPDATED: Event Location saving machanism with better verification and using terms
UPDATED: i18n fields with missing plugin textdomain for translation
UPDATED: event end time can now be set to last beyond start date and still visible in calendar
UPDATED: Responsive Featured image styles
UPDATED: Google maps generate to be set to yes by default
UPDATED: Lightbox eventcard close button X made lighter for visibility
UPDATED: minor style issues are solved
FIXED: upcoming list hide past not working
FIXED: hide end date honored in dailyview times
FIXED: Featured events only calendar issue
FIXED: evo addon class redeclare error
FIXED: Minor style issues
FIXED: Custom universal times remove end time from event card
FIXED: 23 hour format G to be recognized in wp-admin time selection fields
FIXED: RGB related Javascript error on wp-admin that was stopping yes/no button function


= 2.2.14(2014-7-3) =
ADDED: Ability to exclude events from calendars without deleting them
ADDED: Overall calendar user interaction do not interact value
UPDATED: Removed month header blank space from event list
UPDATED: Schema SEO data to have end date that was missing
UPDATED: Improvements to Paypal intergration into front-end
UPDATED: Seperate function to load for calendar footer with action hook evo_cal_footer
UPDATED: Pretty time on eventcard converted into proper language
FIXED: Repeat events for week of the month not showing correct
FIXED: Addon license activation page not working correctly
FIXED: Hide multiple occurence not showing events on other calendars on same page
FIXED: Repeating events time now showing correct on event card
FIXED: Schema SEO showing event page URL when someone dont have single events addon
FIXED: shortcode generator showing a body double

= 2.2.13 (2014-6-16) =
FIXED: Option for adding dynamic styles to inline page when dynamic styles are not saved
FIXED: featured image on eventTop not showing
FIXED: shortcode generator not opening from wysiwyg editor button
FIXED: eventtop styles and HTML that usually get overridden by theme styles
UPDATED: Eventon addons page to now use ajax to load content
UPDATED: New welcome screen - hope you guys will like this

= 2.2.12 (2014-6-1) =
ADDED: yes no buttons to be translatable via I18n
ADDED: the ability to select start or end date for past event cut off
ADDED: option to limit remote server checks option if eventon wp-admin pages are loading slow due to remote server checks
ADDED: Addon license activation system 
UPDATE: Did some serious improvements to cut down remote server check to increase speed
UPDATED: improvements to addon class and eventon remote updater classes
UPDATED: UI layout for addons and license page
FIXED: removed eventon shortcode button from WYSIWYG editor on event-edit post page
FIXED: error on class-calendar_generator line 1595 with event color value
FIXED: styles not saving correct in the settings
FIXED: on widget time and location to be rows by itself
FIXED: several other minor bugs

= 2.2.11 (2014-5-19) =
ADDED: rtl support
ADDED: event type #3 into shortcode options if activated
ADDED: shortcode option to expand sort options section on load per calendar
ADDED: the ability to show featured image for events at 100% height
ADDED: the ability to turn off schema data for events
ADDED: the ability to turn off google fonts completely
ADDED: extended repeat feature to support first, second etc. friday type repeat events
ADDED: option to copy auto generated dynamic styles in case appearance doesnt save changes
UPDATED: UI super smooth all CSS yes/no buttons
UPDATED: Color picker rainbow circle no more changed it to a button
UPDATED: unix for virtual repeat events to be stored from back-end to reduce load on front-end
UPDATED: sort options and filters to close when clicked outside
FIXED: jumper month names
FIXED: eventon javascripts to load only on events pages in backend
FIXED: license activation issue solved
FIXED: events menu not showing up for some on left menu
FIXED: eventon popup box not showing correct solved z-index
FIXED: small bugs

= 2.2.10 (2014-5-5) =
ADDED: you can now show only featured events in the calendar with only_ft shortcode variable
ADDED: load calendars pre-sorted by date or title with sort_by variable
ADDED: add to google calendar button and updated add to calendar button
ADDED: one letter month names for language translation for month jumper
ADDED: accordion like event card opening capabilty controlled via shortcode
ADDED: You can now add custom meta fields to eventTop
ADDED: custom meta field names can be translated in languages now
ADDED: End 3 letter month to eventTop date - now month shortname is always on
ADDED: ability to customize the eventCard time format
ADDED: ability to open links in new window for custom field content type = buttons
ADDED: wp-admin sort events by event location column
UPDATED: Month jumper to jump months upon first change in time
UPDATED: PO file for eventon Admin pages
UPDATED: Sort options section to be more intuitive for user
UPDATED: Events list event order DESC now order months in descending order as well
UPDATED: matching events menu icon based off font icons
FIXED: Arrow circle CSS for IE
FIXED: default event color missing # on hex code
FIXED: Wysiwyg editor eventon shortcode generator icon not opening lightbox
FIXED: Event type ID column for additional event type categories
FIXED: Lon lat not saving for location addresses
FIXED: Secondary languages not getting correct words when switching months
FIXED: improvements to speed eventON and cut down server requests
FIXED: featured image hover issues
FIXED: Custom meta field activation on eventCard and reordering bug
FIXED: font bold not reflecting on event details
FIXED: the content filter disable settings issue

= 2.2.9 (2014-3-26) =
ADDED: More/less text background gradient to be able to change from settings
ADDED: ability to enable upto 5 additional event type categories for events
ADDED: shortcode generator button to wysiwyg editor
ADDED: the ability to turn off content filter on event details
ADDED: Language field to widget
FIXED: minor responsive styles
FIXED: zoom cursor arrow now loads from element class
FIXED: Capitalize date format on eventcard
FIXED: Featured image hover effect removal issues
FIXED: Jump months missing month and year text added to Language
CHANGED: plugin url to use a function to support SSL links

= 2.2.8 (2014-3-13) =
ADDED: Reset to default colors button for appearance settings
ADDED: Jump months option to jump to any month you want
ADDED: Ability to assign colors by event type
ADDED: the ability to create custom field as a button
ADDED: User Interaction for events be able to override by overall variable value
UPDATED: We have integrated chat support direct into eventON settings
UPDATED: the Calendar header Interface design new arrows and cleaner design
TWEAKED: main event wp_Query closing function
FIXED: bulk edit event deleting meta values for event
FIXED: Lan lat driven google map centering on the marker issue solved
FIXED: all text translations to be included in sort menu

= 2.2.7 (2014-2-13) =
ADDED: filter to eventCard and eventTop time and date strings
ADDED: filter 'eventon_eventtop_html' to allow customization for eventTop html
ADDED: filter 'eventon_google_map_url' to load custom google maps API url with custom map languages
ADDED: ability to disable featured image hover effect
ADDED: shortcode support to open event card at first load
UPDATED: shortcode generator code to support conditional variable fields
UPDATED: html element attributes changed to data- in front-end calendar
UPDATED: new data element in calendar front-end to hold all attributes to keep the calendar HTML clean
UPDATED: event locations tax posts column removed - which was no use
FIXED: schema event url itemprop
FIXED: 'less' text not getting translated on eventcard
FIXED: timezone issues to correct hide past events hiding at correct time
FIXED: loading bar not appearing due to style error
FIXED: open event card at first on events list
FIXED: Custom language other than L1 to be updated for new calendars
FIXED: add to calendar ICS file content and timezone issue resolved
FIXED: hide multiple occurance for repeating events shortcode support

= 2.2.6 (2014-1-30) =
ADDED: Ability to collpase eventON setting menus
UPDATED: settings apperance sections can now be closed for space management
UPDATED: Language page UI and pluggability
FIXED: Missing sort option selector colors from setting appearance
FIXED: quick edit incorrect saving event data when 24hour format in active
FIXED: Event popup lightbox click on page scroll bar closing popup
FIXED: eventop background color not saving issue
FIXED: Custom meta fields not saving values for events
FIXED: Widget title to use wp universal filters

= 2.2.5 (2014-1-27) =
ADDED: Event Location Name to eventTop
ADDED: Custom fields can now have Wysiwyg editor or single line text field to enter data
UPDATED: dynamic styles loading method to create a tangible eventon_dynamic_styles.css file instead of using admin-ajax.php to avoid long load times
UPDATED: Appreance color picker UI and the ability to support pluggability
UPDATED: Datepicker to consider start date when selecting end date
FIXED: 3rd custom field value not showing on calendar
FIXED: make sure settings page styles are loaded in page header

NOTE: Make sure to click save on eventON appearance to save new styles

= 2.2.4 (2014-1-12) =
FIXED: Custom meta field values not appearing correct on events page and calendar

= 2.2.3(2014-1-10) =
ADDED: Event locations can now be saved and used again for new events
ADDED: Event location name field
ADDED: featured event color can not be selected from Settings> Appearance and override the set event color with this
ADDED: event class name for featured events
ADDED: New widget to execute any eventON shortcode on sidebar
ADDED: One additional custom meta field, now we have 3 extra fields
ADDED: Font-awesome Vector/SVG icons for retina-readiness
ADDED: more options to change appearances of eventON easily
UPDATED: eventon settings UI for color picker
CHANGED: month nav arrows are now <span> elements instead of <a> elements - to avoid redirects on arrow click
FIXED: 3 letter month name not showing under event date for eventTop
FIXED: eventON widget upcoming event small bug that stopping it from showing the calendar

= 2.2.2(2013-12-21) =
ADDED: capability to add magnifying glass cursor for featured images
ADDED: event type names translatability with eventON dual lang
UPDATED: UI compatibility with wp 3.8
UPDATED: shortcode generator tooltips UI
FIXED: missing eventon settings page i18n 
FIXED: eventTop line will be a <div> if the event slideDown or else it will be <a>
FIXED: more/less text translatability and other translation issues
FIXED: L2 calendar month name switching back to L1 language when switching months
FIXED: All (sort options) text added to language translation
FIXED: event popup CSS/HTML for feature image and event type line CSS
FIXED: ics file date zone to use wordpress i18n date and location incorrect value
FIXED: event custom meta values to go through formatted filter

= 2.2.1(2013-11-30) =
ADDED: couple of wordpress pluggable functions to main calendar
FIXED: event time hours difference on front end than whats saved - using date_i18n() instead of date() now
FIXED: dual language saved value disappearing when switching languages
FIXED: draft events showing up on calendar when switching months
FIXED: month increment messing up due to february
FIXED: all day translation fixed
FIXED: ics file download error on date()
FIXED: event organizer field missing in action
UPDATED: widget to be able to set ID and hide empty months for list
UPDATED: Changed dynamic styles to load as a file and not print on header

= 2.2 (2013-11-21) =
ADDED: event quick edit can now edit more event data on the fly
ADDED: class attribute names to events based on event type category event belong to
ADDED: Get directions field to eventCard - selectable from eventCard settings. Credit to Morten Bech for the suggestion
ADDED: The ability to rearrange the order of the eventCard data fields. Credit to Gilbert Dawed for the suggestion
ADDED: ICS file for each event so events can be added a users calendar
ADDED: new license activation server to stop all errors when activating eventON
ADDED: new add eventon shortcode button next to add media button on WYSIWYG editor
ADDED: brand spanking new shortcode generator popup box with super easy intuitive steps to customize shortcodes
ADDED: ability to reverse the event order ASC or DESC
ADDED: new shortcode "event_order" -- allow ability to set reverse order per calendar
ADDED: ability to add featured image thumbnail to eventTop
ADDED: new shortcode "show_et_ft_img" - allow to show featured image on eventTop or not
ADDED: new support tab to settings page
ADDED: i18n ready and compatible POT file for translation
UPDATED: we removed events lists options area from eventon settings and its now inside shortcode box
UPDATED: template loader function to look up templates in order
UPDATED: better event image full sizing when clicked to fit calendar
UPDATED: calendar eventCard UI - including a new close button
UPDATED: eventon wp-admin wide popup box design and functionality
UPDATED: wp-admin event edit UI - now you can hide each section of event meta data and declutter the space
FIXED: widget checkbox malfunction when there are more than one widgets.
FIXED: unnecessary google maps loading in wp-admin pages
FIXED: Addons & License tab errors some people were experiencing due to XML get file from myeventon server with addons latest info

IMPORTANT: all addons need to be updated to latest to run with eventon 2.2



= 2.1.19 (2013-10-12) =
ADDED: backend time selection now changes based on WP time format - 24hr
ADDED: All events edit page dates are now sync with sitewide date format
ADDED: new option for user interaction; open an even as a popup box
ADDED: the ability to hide end time on calendar -- end date must be same as start or empty
ADDED: the ability to hide multiple occurance of events spread across several months -- on upcoming list on shortcode calendar and widget
FIXED: shortcode button adding multiples of shortcodes 
FIXED: shortcode popup box appearing empty on second occasion
FIXED: CSS sort options button overlapping
FIXED: Upcoming list featured image expanding issues
FIXED: Gmaps event Location now works w/o the address in eventTop
FIXED: google maps init javascript issue on FF fixed 
UPDATED: Date and time selection UI
UPDATED: changed data-vocabulary microSEO data to schema.org and update to fields

= 2.1.18 (2013-9-17) =
ADDED: publish event capability to the list
FIXED: Day abbreviation for custom languages
FIXED: Addon error of scandir failed

= 2.1.17 (2013-9-16) =
ADDED: The EvenTop data customization options
ADDED: Hide past events option to eventON Calendar widget
ADDED: The ability to customize the format of calendar header month/year title
ADDED: The ability to edit color of text under event title on eventTop
ADDED: Event ID can now be found by hovering over events list in wp-admin events
ADDED: [core] new filter 'eventon_sorted_dates' to access sorted events list
UPDATED: JQuery UI css to latest version
UPDATED: Backend UI a little
UPDATED: [core] myEventON Settings page tab filter
FIXED: Backend events sorting incorrect issue on all event posts list
FIXED: EventON widget event type filtering issue when switching months
FIXED: EventON Shortcode popup window not closing issue
FIXED: EventCard featured image not expanding full height sometimes
FIXED: array_merge error some people were getting for event types

New Verbage: eventTop - the event line that opens up the eventCard

= 2.1.16 (2013-8-21) =
ADDED: UX - click on featured event image to expand the image to full height
TWEAKED: UI of the frontend calendar with clean tiny icons for time and location
FIXED: Event details overflowing when floated images
FIXED: bug with upcoming events set to hide causing events to not show up on full cal and other cals
FIXED: javascript delegate() has been changes to on() based on jQuery's new change
FIXED: time and location icons can now be edited from eventON settings

= 2.1.15 (2013-8-8) =
FIXED: sort options text not dissapearing when set to hide
FIXED: javascript issue causing eventON to stop work with WP 3.6
TWEAKED: eventon Addon data are now also checked via cURL if failed with file_get_content

= 2.1.14 (2013-8-6) =
UPDATED: Back-end widget UI to a whole new level which you gonna love
ADDED: shortcode variable "hide_past" to give the ability to hide past events per each shortcode
ADDED: Fixed month/year are now supported in widget
ADDED: Ability to select scroll wheel zoom on google maps or disable it
TWEAKED: Addons pull live addon details and the UI got a face lift.
TWEAKED: License tab reside in addons tab under eventon settings now
TWEAKED: events can now be repeated longer than 10 times
TWEAKED: sort options in a minimal dropdown menu
TWEAKED: Javascripts handles can now be called at will for AJAX driven pages
FIXED: Filtering issue when using multiple filters at once
FIXED: Quick edit for events

NOTES: If you are using eventON addons most of them will give minor bugs with newer version of eventON and you will NEED to update your eventON Addons to latest versions to get them working properly.


= 2.1.13 =
ADDED: Ability to add addresses using Latitude Longitude - for addresses that are not found correctly by google.
ADDED: Shortcode guide link to shortcode popup window
FIXED: Single quote values not saving correct for organizer
FIXED: Upcoming event list month text color
FIXED: Eventbrite non-connecting issue

= 2.1.12 =
FIXED: Google Map display issue when switching months
FIXED: Backend javascript not loading into wp-admin issue
TWEAKED: Minor fixes and compatibility updates for addons

= 2.1.11 =
FIXED: Colorpicker issue on Firefox
FIXED: Daily repeats addon not working

= 2.1.10 =
ADDED: Google microdata for SEO for events included in calendar
ADDED: Ability to choose height of the event's featured image from settings
ADDED: Ability to remove more/less button in long event descriotion
ADDED: You are not limited 5 colors now, you can select your on custom event color
ADDED: Now you can add upto 2 custom fields for events and eventcard
ADDED: Ability to set fixed starting month/year for upcoming events list in shortcode
ADDED: You can now select to show year in upcoming events list
ADDED: Yearly event repeats
ADDED: Ability to set event date without time for multi-day events
UPDATED: Minor improvements to code and UI
FIXED: Event date not saving correct in some languages due to WP default date format and JQ UI datepicker issue. Now you can select either to use wp default date format in backend date selection or not. (if you chose not, the date format will be yyyy/mm/dd)
FIXED: Template locator bug
FIXED: Incorrect new update available notifications

= 2.1.9 [2-13-5-6] =
FIXED: error on call to undefined function date_parse_from_format() for those running php 5.2
FIXED: Template error that cause entire site layout for some
FIXED: Widget title not appearing

= 2.1.8 [2013-5-1] =
ADDED: basic single event page support and "../events/" url slug can be used to show calendar now - which is coming from a new page called "Events" in WP admin pages. 
ADDED: more/less custom language support
FIXED: new events not showing on calendar
FIXED: issue with EventON widget messing other widgets
FIXED: incorrect day name on multi-day event
FIXED: license version to update to current version after an update
FIXED: weird download issue with autoupdate
FIXED: incorrect date saving for non-american time format

= 2.1.7 [2013-4-30] =
FIXED: event start date going to 1st of month error
FIXED: addons not showing issue
FIXED: error you get when saving styles
FIXED: array_merge error for addons

= 2.1.6 [2013-4-28] =
ADDED: ability to get automatic new updates
ADDED: new and exciting license management tab to myEventON settings
ADDED: new plugin update notifications 
ADDED: event date picker date format is now based off your site's date format
UPDATED: Event card got little jazzed up now
UPDATED: Main settings page - removed some junk
UPDATED: in-window pop up box, added new loading animation and notifications
UPDATED: EventON widgets UI
UPDATED: improved event generator class for faster loading
FIXED: issue with event close button not working for new months
FIXED: upcoming events list shortcode
FIXED: event time default value to 00
FIXED: minor style and functionality issues on eventON widget

= 2.1.5 [2013-4-18] =
ADDED: visible event type IDs to event types category page
ADDED: ability to duplicate events 
ADDED: more useful pluggable hooks into base plugin
ADDED: ability to disable google gmaps api partly and fully
ADDED: ability to set google maps zoom level
ADDED: close button at the bottom of each event details
UPDATED: frontend styles
UPDATED: backend settings tabs, better UI for language tab
UPDATED: event repeating UI
FIXED: issue with calendar font settings not working properly
FIXED: external event links not opening
FIXED: php template tag not working correctly

= 2.1.4 [2013-4-8] =
ADDED: a new shortcode popup box for better user experience

= 2.1.3 [2013-4-7] =
* Added support to open learn more links in new window
* Improvements to addon handling
* Few more minor bugs distroyed for good

= 2.1.2 [2013-4-5] =
* Minor bugs fixed
* Added the ability to disable google maps API
* Fix custom event type names on events column in backend
* Improvements on addon handling

= 2.1.1 [2013-3-28] =
* Fixed small bugs
* Added auto plugin update notifier for eventon
* Added upcoming events list support to widget

= 2.1 [2013-3-28] =
* Implemented hooks and filters for extensions and further customization
* You can now add addons to extend features of the calendar
* Fixed bunch more bugs
* Changed the name and a whole new shi-bang now
* Quick shortcode button on Page text editor

= 2.0.8 [2013-3-23]=
* Fixed bugs

= 2.0.7 [2013-3-17]=
* Fixed shortcode upcoming list issue
* Added the ability to hide empty months in upcoming list

= 2.0.6 [2013-2-28]=
* fixed minor error with usort array

= 2.0.5 [2013-2-25] =
* Added repeat events capability for monthly and weekly events
* Reconstructed the event computations system to support future expansions
* Now you can hide the sort bar from backend options
* Event card icons can be changed easily from backend now
* Added the template tag support for upcoming events list format
* Primary font for the calendar can also be changed from the backend options

= 2.0.4 [2013-2-11]=
* Added the ability to add an extra custom taxonomy for event sorting
* Custom taxonomies can be given custom names now
* Better control over front-end event sorting options
* Further minimalized the sort bar present on front-end calendar
* Fixed bugs on eventbrite and meetup api
* Added a learn more event link option
* Fixed event redirect when external link is empty
* Added 2 more different google map display types

= 2.0.3 [2013-1-13] =
* Fixed the bug with google map images

= 2.0.2 [2012-12-28] =
* Calendar arrow nav issue fixed in some themes

= 2.0.1 [2012-12-24] =
* Added the ability to create calendars with different starting months.

= 2.0 [2012-12-21] =
* Squished bugs in the code with data save and bunch of other stuff...
* Added Meetup API support to connect to meetup events and get event data in an interactive way.
* Updated eventbrite API to a more interactive event data-bridge setup.
* Added event organizer field.
* You can now link events to a url instead of opening event details.
* Event Calendar now support featured images for events right in the "event card".
* Added more animated effects to frontend of the calendar.
* Ditched the default skin to nail down some of the CSS issues with skins on "Slick"
* Updated event option saving method to streamline load time.
* Added TON of more customizable options

= 1.9 [2012-11- ]=
* Fixed saved dates and other custom event data dissapearing after auto event save in WP
* Improved custom style appending method
* Added Paypal direct link to event ticket payment
* Added easy color picker

= 1.8 [2012-10-23]=
* Added widget support
* UI Update to backend
* Existing skins update
* Improvements to algorithm

= 1.7 [2012-10-16]=
* Updated back-end UI
* Better hidden past event management
* Ability to disable month scrolling on front-end
* Added responsiveness to skins

= 1.6 [2012-5-31] =
* Multiple calendars in one page
* Calendar to show only certain event types with shortcode or template tags
* custom language for "no events"
* "Slick" new skin added
* Correct several CSS issues with parent CSS styles

= 1.5 [2012-5-1] =
* Improvement to code for faster loading
* Added smoother month transitions
* "Event Type" support for events
* Apply multiple colors to events and allow sorting by color
* Added "all day event" support
* Default wordpress main text editor is now used for event description box
* Better event data management

= 1.4 [2012-4-5] =
* CSS issues fixed
* Multiple Skin support 

= 1.3 [2012-1-31] =
* Minor changes to Interface design 
* New Loading spinner on AJAX calls
* Added auto Google Map API integration based on event location address
* Added control over past events display on the calendar
* Improvements to events algorithm for faster load time
* Bug fixed (End month and start month date issue)
* Bug fixed (Month filtering issues)

= 1.2 [2012-1-12] =
* Minor bugged fixed
* Back-end Internationalization
* Added plugin data cleanup upon deactivation

= 1.1 [2012-1-4] =
* Added custom language support

= 1.0 [2011-12-21] =
* Initial release