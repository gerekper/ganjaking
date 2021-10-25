=== reCaptcha for WooCommerce===
Contributors:nik00726
Tags:Recaptcha
Requires at least:3.0
Tested up to:5.7.2
Version:2.17
Stable tag:2.17
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Protect your eCommerce site with google reCaptcha. 

== Description ==


Protect your eCommerce site with google recptcha. 



**=Features=**

1. Support both reRecapcha V2 and V3.

2. Login reCaptcha

3. Registration reCaptcha

4. Lost password reCaptcha

5. Guest checkout reCaptcha

6. Login user checkout reCaptcha

7. Add payment method reCaptcha

8. WooCommerce pay for order captcha

9. WP-Login reCaptcha

10. WP-Register reCaptcha

11.WP-Lost password reCaptcha

12.Custom label for reCaptcha

13.Custom error messages

14.Recaptcha dark/light theme

15.Captcha full/compact mode

16.Auto-detect user language

17.Disable submit button until captcha checked

18.Reset captcha on order postback error

19.Bypass reCAPTCHA (do not show captcha) using IP or IP-Range for specific clients/users






[Get Support](http://www.i13websolution.com/contacts)


== Installation ==

This plugin is easy to install like other plug-ins of Wordpress as you need to just follow the below mentioned steps:

1. upload woo-recaptcha folder to wp-Content/plugins folder.

2. Activate the plugin from Dashboard / Plugins window.

4. Now Plugin is Activated, You can see reCaptcha tab in woocommerce settings screen.


### Usage ###

1.Use of plugin "reCaptcha for WooCommerce " is easy.

2.You can goto settings screen of WooCommerce where you can see reCaptcha tab"

3.Now set reCaptcha keys and enabled reCaptcha on required places.




== Changelog ==

= 2.17 =

* Added filter for reCaptcha v2 to change language if needed programmatically. To change hl paramter of recaptcha anyone can use this filter - i13_recapchav2_set_lang


= 2.16 =

* Added functionality to Bypass reCAPTCHA (do not show captcha) using IP or IP-Range for your clients/users

= 2.15 =

* Fixed registration captcha not work for some theme as it is using _nonce.

= 2.14 =

* Added captcha for Post Comment forms.


= 2.13 =

* Fixed complain that plugin automatically deactivated.


= 2.12 =

* Fixed Elavon payment processor shown catcha twice on add payment method page


= 2.11 =

* Added option to enable/disable recaptcha for stripe payment request buttons (Google Pay and Apple Pay)


= 2.10 =

* Added option to disable on the fly reRecapcha v3 generation for checkout
* Disable recaptcha plugin when while using REST end point

= 2.9 =

* Added reCAPTCHA for product review form.
* Added recaptcha v2 language selection
* Fixed recaptcha duplication on Elavon credit card pay for order


= 2.8 =

* Fixed problem with Elavon payment processor legacy mode.


= 2.7 =


* Added support for Elavon payment processor

* Added new option "Disable on the fly reCAPTCHA v3 token generation" that will allow you to use this option if two submit button fighting for taking control. So use only if you have problem 
  with submit button.
  
* Fixed problem with javascript errors, When the html of page is minified.


= 2.6 =

* Google reCAPTCHA token is missing Fixed


= 2.5 =

* Added support for custom login form that built using "wp_login_form" function

= 2.4 =

* Fixed reCAPTCHA v3 not working with 2FA.


= 2.3 =

* Fixed reCaptcha not working for IE 11.


= 2.2 =

* Fixed reCaptcha v2 disable submit button on reCaptcha reset problem


= 2.1 =

* Fixed Uncaught SyntaxError: Unexpected string error after updates 2.0

= 2.0 =

* Now "recaptcha for woocommerce" support google latest version of greCAPTCHA V3
* This version never disturb user, ReCaptcha V3 Uses a behind-the-scenes scoring system to detect abusive traffic, and lets you decide the minimum passing score. Please note that there is no user interaction shown in reRecapcha V3 meaning that no recaptcha challenge is shown to solve. 

= 1.0.17 =

* Added captcha protection for WooCommerce Pay For Order (This is only used when your order is failed. WooCommerce allow failed order to repay using this page.



= 1.0.16 =

* Added option for checkout captcha to refresh when there are checkout errors.
* Fixed issue of refresh captcha sometimes not working


= 1.0.15 =

* Tested With WooCommerce 4.4.0

= 1.0.14 =

* Added javascript callbacks after reCaptcha varified
* Tested With WordPress 5.5


= 1.0.13 =

* Fixed issue of Add payment method captcha not shwoing up.

= 1.0.12 =

* Removed jQuery.noConflict() as this cause problem for some of users.


= 1.0.11 =

* Added facility to show/hide reCaptcha label
* Tested with WooCommerce 4.2


= 1.0.10 =

* Fixed small problem reported by user when nonce is diffrent
* Tested with WooCommerce 4.1


= 1.0.9 =

* Fixed broken link in admin WooCommerce reCaptcha settings tab

* Added new protection for Add payment method

* Added new feature - disable submit button until captcha checked

* Added translation so that if messages labels etc left blank then you can translate it


= 1.0.8 =

* As per some of the clients complain that Recaptcha not working when the payment processer takes more time. So added ReCaptcha validity settings for checkout. So once Recaptcha verified it will valid for a given number of minutes

* Added option to enable reCpacha on login checkout

* Fixed for multisite have problem when not activated for networks


= 1.0.6 =

* Important fix found by Macneil. His hosting is strict and there is problem while loading recaptcha settings tab


= 1.0.5 =

* Make plugin compatible with WordPress multisite

= 1.0.4 =

* Added new option "No-conflict" mode. This will helpful when there is conflict is Recaptcha js


= 1.0.3 =

* Fixed error reported by Thomas Wurwal that some of theme not rendere captcha on checkout page, due to in wp_enqueue_scripts action is_woocommerce() return false.


= 1.0.2 =

* Fixed error shown in console "reCaptcha already rendered"
* Added option to refresh reCaptcha on checkout page

= 1.0.1 =

* Tested with WooCommerce 4.0

= 1.0 =

* Stable 1.0 first release

