=== YITH WooCommerce Questions and Answers Premium ===

Contributors: yithemes
Tags: woocommerce, e-commerce, ecommerce, shop, product details, question, questions, answer, answers, product question, product, pre sale question, ask a question, product enquiry, yith, plugin
Requires at least: 4.0.0
Tested up to: 5.4
Stable tag: 1.3.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Documentation: https://docs.yithemes.com/yith-woocommerce-questions-and-answers/

It allows users of your shop to ask questions on products and answer to them, a tool like in Amazon pages, but that can be also used as a FAQ section

== Changelog ==

= 1.3.7 - Released on 29 May 2020 =

* New: Support for WooCommerce 4.2
* Update: plugin framework
* Update: language files
* Dev: added new filter ywqa_get_author_name

= 1.3.6 - Released on 04 May 2020 =

* New: support for WooCommerce 4.1
* New: added a new option to only allow the admins to reply
* Update: plugin framework
* Update: Italian language files
* Update: Spanish language files
* Update: Dutch language files
* Fix: esc_html function deleted to not show HTML tags on emails
* Dev: added new filter ywqa_allow_guest_to_answer, this filter should be false if the guest can leave question but no answers

= 1.3.5 - Released on 09 March 2020 =

* New: support for WooCommerce 4.0
* New: support for WordPress 5.4
* New: added plugin shortcodes with Elementor PRO
* Tweak: now, the answers paging option also displays the selected number of question in the main Q&A view, and the answers are in the correct order
* Update: plugin-fw
* Fix: customer answer email when the answer is made from backend
* Dev: all strings are now escaped
* Dev: new filter 'yith_ywqa_questions_tab_priority'

= 1.3.4 - Released on 27 December 2019 =

* New: support for WooCommerce 3.9
* Fix: fixed the redirect to the product page when user in in login page and go back
* Update: Italian language
* Update: Spanish language
* Update: .pot file

= 1.3.3 - Released on 08 November 2019 =

* New: support for WordPress 5.3
* New: support for WooCommerce 3.8
* Tweak: added a new option to anonymize the Q&A dates
* Update: plugin framework
* Update: Dutch language
* Fix: now the answer email is sent when the answer is approved
* Fix: fixed the new metabox to be displayed in all the post pages, now are only displayed in the q&a post
* Dev: don't notify the answer to the question author if the answer needs to be approved

= 1.3.2 - Released on 05 August 2019 =

* New: support WooCommerce 3.7
* New: new link in the frontend to edit the Q&A in the backend
* New: new metabox with the last Q&A post editor data
* New: now, the Q&A author and the date are displayed in the Q&A overview on frontend
* Update: updated plugin core
* Fix: fixed a problem with the answer editor in the backend
* Dev: added a new condition to avoid a non object warning
* Dev: added an scroll up, if necessary, when access to the question
* Dev: fixing minor issues

= 1.3.1 - Released on 31 May 2019 =

* Update: Italian language
* Fix: gcaptcha is loaded although the option is disabled
* Dev: new filter 'ywqa_get_questions_order_by'


= 1.3.0 - Released on 24 May 2019 =

* New: support to WordPress 5.2
* New: support to reCaptcha v3
* Tweak: now, the notification bubble display the questions without answers
* Update: updated plugin submodules
* Remove: completely deleted the search bar

= 1.2.9 - Released on 09 April 2019 =

* New: support to WooCommerce 3.6.0 RC 1
* Update: updated Plugin Framework
* Update: updated Dutch language
* Update: updated Spanish language
* Dev: check if files exist to rename the files

= 1.2.8 - Released on 19 February 2019 =

* New: added a bubble notification in the backend when a new Q&A is added
* New: checking the Q&A with Akismet Anti-spam if it is installed
* Update: main file language
* Update: updated Dutch language
* Update: updated Plugin Framework
* Dev: new filter 'yith_wcqa_question_success_message'
* Dev: new filter 'yith_wcqa_answer_success_message'


= 1.2.7 - Released on 11 December 2018 =

* New: support to WordPress 5.0
* Update: plugin core to version 3.1.10
* Dev: new filter 'yith_ywqa_check_count_tab_title'


= 1.2.6 - Released on 16 November 2018 =

* Update: Plugin framework
* Update: Dutch language
* Update: Language file .pot
* Fix: English correction text
* Fix: Compatibility fix with WPML
* Fix: load questions on "Back to questions" link for users not logged in
* Fix: fixed the answer sorting in the FAQ mode
* Remove: Search bar options


= 1.2.5 - Released on 23 October 2018 =

* Update: Plugin framework

* New: Support to WooCommerce 3.5.0
* Tweak: new action links and plugin row meta in admin manage plugins page
* Update: Italian language
* Fix: fixing the wp_nonce_field

= 1.2.4 - Released on 17 October 2018 =

* New: Support to WooCommerce 3.5.0
* Tweak: new action links and plugin row meta in admin manage plugins page
* Update: Italian language
* Fix: fixing the wp_nonce_field

= 1.2.3 - Released on 14 June 2018 =

* Update: Spanish language
* Update: Dutch language
* Update: updating minified JS
* Updated: updated the official documentation url of the plugin
* Fix: Fixed the issue with the google recaptcha and the JS
* Dev: added a new filter in the login redirect
* Dev: checking YITH_Privacy_Plugin_Abstract for old plugin-fw versions



= 1.2.2 - Released on 25 May 2018 =

* GDPR:
   - New: exporting user question and answers data info
   - New: erasing user question and answers data info
   - New: privacy policy content
* Tweak: added trigger yith_ywqa_answer_helpfull
* Tweak: add nofollow to plugin a tags
* Tweak: check product object in email values
* Update: Italian Translation
* Update: Documentation link of the plugin
* Fix: frontend and frontend.min.js get this files from the last version
* Fix: missing product id display on backend
* Fix: Fixed the pagination in answers
* Fix: Fixed an issue with the recaptcha dependencies
* Fix: register and enqueue recaptcha

= 1.2.1 - Released on 30 January 2018 =

* New: Dutch language
* New: support to WooCommerce 3.3.0-RC2
* Update: plugin framework 3.0.11
* Dev: new filter 'yith_ywqa_tab_title'
* Dev: new js trigger 'yith_ywqa_answer_helpfull'
* Dev: new filter 'yith_ywqa_answer'


= 1.2.0 - Released on 11 December 2017 =
* Update: Plugin fw to version 3.0


= 1.1.30 - Released on 27 November 2017 =
* New: support to WooCommerce 3.2.5
* Dev: new filter 'yith_wcqa_allow_user_to_reply'


= 1.1.29 - Released on 22 September 2017 =
* Dev: new filter 'yith_ywqa_no_answer_text'
* Tweak: email validation process on submit question or answer


= 1.1.28 - Released on 29 August 2017 =
* New: option to set label for the tab "Questions & Answers"
* New: option to set title for the section "Questions & Answers"
* Update: plugin framework


= 1.1.27 - Released on 06 July 2017 =

* New: support for WooCommerce 3.1.
* New: tested up to WordPress 4.8.
* Fix: email layout, prevent the product image from overlapping the email content.
* Update: YITH Plugin Framework.

= 1.1.26 - Released on 23 June 2017 =

* Update: users can change the answers order dynamically.

= 1.1.25 - Released on 22 June 2017 =

* Update: allow users to receive a notification when an answer to their question is submitted.
* Fix: 'back to questions' link not working due to missing product id.
* Fix: avoid showing duplicated reCaptcha element.
* Fix: do not show product link in Q&A table, if the product is deleted.

= 1.1.24 - Released on 15 May 2017 =

* Fix: typo in ywqa-product-questions.php file.

= 1.1.23 - Released on 11 April 2017 =

* New:  Support to WordPress 4.7.3.
* New:  Support to WooCommerce 3.0
* Fix: filtering questions or answers by product give empty results if the field is left blank.

= 1.1.22 - Released on 07 December 2016 =

* New: ready for WordPress 4.7

= 1.1.21 - Released on 16 November 2016 =

* Fix: a notice was shown on outgoing emails generated by third party plugins
* Fix: a notice was shown in admin settings as a conflict with the plugin 'Members'
* Fix: product link not valid in the email sent when a new question is submitted

= 1.1.20 - Released on 04 November 2016 =

* Fix: removed unwanted escape symbol in question and answer emails

= 1.1.19 - Released on 24 October 2016 =

* New: email notification with YITH Email Templates compatibility
* Update: frontend templates location
* Fix: enable vote option did not apply correctly
* Fix: questions and answers date format use the WordPress settings

= 1.1.18 - Released on 15 September 2016 =

* New: email templates overwritable from theme
* New: link to the product page in the notification email
* Fix: non-object reference when there aren't answers to a question
* Fix: user notification on new question not set correctly

= 1.1.17 - Released on 19 August 2016 =

* New: option for allowing anonymous question or answer submisssion
* Fix: HTML formatting not applied when clicking on "Read more" link

= 1.1.16 - Released on 27 July 2016 =

* Fix: the badge "answered by admin" shown for mistakenly

= 1.1.15 - Released on 20 June 2016 =

* Update: WooCommerce 2.6 100% compatible
* Fix: warning for missing 'domain' for custom view in questions and answers table
* Fix: some text in the email were not correctly localizable

= 1.1.14 - Released on 13 June 2016 =

* Update: WooCommerce 2.6 100% compatible
* Fix: warning for missing 'domain' for custom view in questions and answers table

= 1.1.13 - Released on 26 May 2016 =

* Fix: author name shown even if anonymous mode is set
* Update: plugin WooCommerce 2.6 ready

= 1.1.12 - Released on 17 May 2016 =

* Update: YITH Plugin FW
* Update: italian localization files
* Fix: missing method call vote_question
* Fix: typos in plugin code

= 1.1.11 - Released on 15 April 2016 =

* Fix: search questions/answers by product on back-end fails

= 1.1.10 - Released on 05 April 2016 =

* New: option that let you choose if answers should be moderated before being shown
* New: option that let you choose if guest users can post content
* New: guest users should enter their name and email address when posting content

= 1.1.9 - Released on 21 March 2016 =

* Fix: error on save on quick edit applied to a product
* Update: removed quick edit on Questions&Answers page

= 1.1.8 - Released on 11 March 2016 =

* New: choose if the output will be shown on a custom tab of the product tabs or manually via the [ywqa_questions] shortcode

= 1.1.7 - Released on 18 January 2016 =

* Update: plugin ready for WooCommerce 2.5.x
* Update: improved layout for backend questions page

= 1.1.6 - Released on 14 December 2015 =

* Fix: YITH Plugin Framework breaks updates on WordPress multisite
* Update: callback fails with PHP version prior to 5.4

= 1.1.5 - Released on 10 December 2015 =

* Fix: warning on backend Q&A table
* Fix: missing string localization
* Update: localization file

= 1.1.4 - Released on 09 December 2015 =

* Fix: filters on Questions and Answers table not working
* Fix: questions and answers of guest were assigned to current user if the post is opened on backend

= 1.1.3 - Released on 07 December 2015 =

* Update: question/answer author name and email shown on Q&A table
* Tweak: Database query for back compatibility fired even if not need

= 1.1.2 - Released on 13 November 2015 =

* Update: Changed text-domain from ywqa to yith-woocommerce-questions-and-answers
* Update: YITH Plugin Framework
* Update: changed action used for YITH Plugin FW loading

= 1.1.1 - Released on 14 October 2015 =

* New: CSS class for highlighting unapproved content on Questions & Answers back end table.

= 1.1.0 - Released on 04 September 2015 =

* New: editor for questions and answers.

= 1.0.4 - Released on 12 August 2015 =

* Tweak: update YITH Plugin framework.

= 1.0.3 - Released on 06 August 2015 =

* New: optional "No CAPTCHA reCAPTCHA" system to submit questions and answers.

= 1.0.2 - Released on 28 July 2015 =

* Fix: questions excluded from site search

= 1.0.1 - Released on 14 July 2015 =

* Added : user interface improved.

= 1.0.0 - Released on 19 June 2015 =

* Initial release