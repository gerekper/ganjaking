=== Disable "BIG Image" Threshold ===
Contributors: desrosj
Tags: images, upload, big images
Requires at least: 5.2
Tested up to: 5.3
Requires PHP: 5.6
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Disables the "BIG image" threshold introduced in WordPress 5.3.

== Description ==

In WordPress 5.3, several improvements were made to how images are processed. One of these improvements was the concept of "BIG images". When an image is uploaded that exceeds the "BIG image" threshold (2560 by default), a new "full" size image is generated. This new image is then used instead of the true original when generating image subsizes to reduce server load.

Prior to 5.3, it was possible for the originally uploaded image to be displayed on the front-end, even when they were not “web ready”. Unnecessarily large images can be bad for performance and wastes bandwidth (which is unfortunate for those with slow Internet, or bandwidth caps by their service plans). This new full size image will now be displayed instead.

There are some scenarios where disabling this threshold would be desired, though. Maybe you run a photography site that
needs to display original images, or your theme has full screen backgrounds.

This plugin disables the "BIG" image threshold and preserves the true originally uploaded image as the full size.

For more information on the improvements to image processing in WordPress 5.3, check out these [developer](https://make.wordpress.org/core/2019/10/09/introducing-handling-of-big-images-in-wordpress-5-3/) [notes](https://make.wordpress.org/core/2019/10/11/updates-to-image-processing-in-wordpress-5-3/) on the [Making WordPress Core blog](https://make.wordpress.org/core/).

== Installation ==

1. Upload this plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

This is brand new, so there are no questions that have been asked frequently yet!

== Changelog ==

= 1.0 =
* Hello world!
