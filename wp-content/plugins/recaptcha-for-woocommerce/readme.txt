=== reCaptcha for WooCommerce===
Contributors:nik00726
Tags:Recpatcha
Requires at least:3.0
Tested up to:5.5
Version:1.0.15
Stable tag:1.0.15
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Protect your eCommerce site with google reCaptcha. 

== Description ==


Protect your eCommerce site with google recptcha. 



**=Features=**


1. Signup Protection

2. Login protection

3. Forgot Password protection

4. Checkout protection





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

