=== Dynamic Featured Image ===
Contributors: ankitpokhrel, cfoellmann
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=J9FVY3ESPPD58
Tags: dynamic featured image, featured image, post thumbnail, dynamic post thumbnail, multiple featured image, multiple post thumbnail
Requires at least: 3.8
Tested up to: 5.1
Stable tag: 3.7.0
Requires PHP: 5.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Dynamically adds multiple featured image (post thumbnail) functionality to posts, pages and custom post types.

== Description ==

Dynamically adds multiple featured image or multiple post thumbnail functionality to your page, posts and custom post types. This plugin provides you an interface to add any number of featured image as you want without writing a single line of code. These featured images can then be collected by the various theme functions.

**Overview**  

Dynamic Featured Image enables the option to have MULTIPLE featured images within a post or page. 
This is especially helpful when you use other plugins, post thumbnails or sliders that use featured images. 
Why limit yourself to only one featured image if you can do some awesome stuffs with multiple featured image? 
DFI allows you to add different number of featured images to each post and page that can be collected by the various theme functions.

> **A NOTE ABOUT SUPPORT:** We’re here to help troubleshoot bugs, but please don't set expectations early as the support forums at WordPress.org are only checked once in a while.

**How it works?**  
1. After successfull plugin activation go to `add` or `edit` page of posts or pages and you will notice a box for second featured image.  
2. Click `Set featured image`, select required image from "Dynamic Featured Image - Media Selector" popup and click `Set Featured Image`.  
3. Click on `Add New` to add new featured image or use `Remove` link to remove the featured image box.  
4. You can then get the images by calling the function `$dynamic_featured_image->get_featured_images([$postId (optional)])` in your theme. ([Click here for details](https://github.com/ankitpokhrel/Dynamic-Featured-Image/wiki "Documentation for current version"))  
5. The data will be returned in the following format.
`
array
  0 => 
    array
      'thumb' => string 'http://your_site/upload_path/yourSelectedImage.jpg' (length=50)
      'full' => string 'http://your_site/upload_path/yourSelectedImage_fullSize.jpg' (length=69)
      'attachment_id' => string '197' (length=3)
  1 => 
    array
      'thumb' => string 'http://your_site/upload_path/yourSelectedImage.jpg' (length=50)
      'full' => string 'http://your_site/upload_path/yourSelectedImage_fullSize.jpg' (length=69)
      'attachment_id' => string '198' (length=3)
  2 => ...
`

**Resources**  
1. [Detail Documentation](https://github.com/ankitpokhrel/Dynamic-Featured-Image/wiki "Documentation for current ver.").  
2. [DFI Blog](https://ankitpokhrel.com/explore/category/dynamic-featured-image/ "DFI Blog").  
3. [StackOverflow Tag](https://stackoverflow.com/questions/tagged/dynamic-featured-image "StackOverflow Tag").

**MultiSite Info**  
You can use `Network Activate` to activate plugin for all sites on a single install. It is only available on the Network admin site not anywhere else. 
Simple `Activate` activates for the site you are currently on. These will be permitted to be activated or deactivated on ANY blog.

While deleting the plugin from the `Network` be sure that the plugin is deactive in all installation of your WordPress network.

**Contribute**  
If you'd like to check out the code and contribute, join us on [Github](https://github.com/ankitpokhrel/Dynamic-Featured-Image "View this plugin in github"). 
Pull requests, issues, and plugin recommendations are more than welcome!

== Installation ==

1. Unzip and upload the `dynamic-featured-images` directory to the plugin directory (`/wp-content/plugins/`) or install it from `Plugins->Add New->Upload`.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. If you don't see new featured image box, click `Screen Options` in the upper right corner of your wordpress admin and make sure that the `Featured Image 2` box is selected.

== Frequently Asked Questions ==
= 1. The media uploader screen freezes and stays blank after clicking insert into post? =
The problem is usually due to the conflicts with other plugin or theme functions. You can use general debugging technique to find out the problem.

i. Switch to the default wordpress theme to rule out any theme-specific problems.  
ii. Try the plugin in a fresh new WordPress installation.  
iii. If it works, deactivate all plugins from your current wordpress installation to see if this resolves the problem. If this works, re-activate the plugins one by one until you find the problematic plugin(s).  
iv. [Resetting the plugins folder](https://codex.wordpress.org/FAQ_Troubleshooting#How_to_deactivate_all_plugins_when_not_able_to_access_the_administrative_menus.3F) by FTP or PhpMyAdmin. Sometimes, an apparently inactive plugin can still cause problems.

= 2. There is no additional image on the page when I save it or publish it? =
This happens when there is any problem in saving you post or page properly. For example, if you try to save or publish the post without the post title the featured images may not be saved properly.

= 3. Can I set the image from remote url? =
If you need to add images from the remote url you need to switch back to ver. 2.0.2 . There is no such feature in ver. 3.0.0 and above.

Note: If you are using remote url to use the feature image, the helper functions may not work properly. 
Alt, caption and title attribute for these images cannot be retrieved using helper functions. `NULL` is returned instead.

= 4. I cannot add or change secondary featured images after update? =
This usually happens because of cache. Clear all your cache and try again if you are having this problem. If you still have such problem you can get help through support forum.

= 5. Is it possible to make DFI work only for certain post types? =
Yes! It is easily possible from version 3.1.13. A filter is added in the recent version for this purpose. Refer [this thread](https://ankitpokhrel.com/explore/is-it-possible-to-make-dfi-work-only-for-certain-post-types/) for mor info.

= 6. Other problems or questions? =
Other problems? Don't forget to check the [blog](https://ankitpokhrel.com/explore/category/dynamic-featured-image/) and learn to create some exciting things using DFI.

Please use [support forum](https://wordpress.org/support/plugin/dynamic-featured-image) first if you have any question or queries about the project. 
If you don't receive any help in support forum then you can directly contact me at `info [at] ankitpokhrel.com`. Please atleast wait for 48hrs before sending another request.

Please feel free to report any bug found at https://github.com/ankitpokhrel/Dynamic-Featured-Image/ or `info [at] ankitpokhrel.com`.

== Screenshots == 
1. New featured image box.
2. Selecting image from media box.
3. Add new featured image box.

== Changelog ==
= 3.7.0 =
* Feature: Auto-select media on open (#78)
* Fix: Attachment ID issue for edited image (#77, #80)

= 3.6.8 =
* Fix slow query issue (#74, Thanks @tobros91)
* Add German translations (#69, Thanks @swiffer)
* Correct Swedish translations (#72, Thanks @S8nsick66)
* Add sponsors

= 3.6.5 =
* Add Thai translations (#63, Thanks @isudnop)
* Fix warning when counting empty string in PHP 7.2 (#66, Thanks @zsmartin)
* Show only images in media selector (#67, Thanks @zsmartin)
* Add filters for metabox context and priority (#68, Thanks @zsmartin)

= 3.6.1 =
* Remove short array syntax to support php <= 5.4.

= 3.6.0 =
* Various security fixes.
* Missing text domains fixes.
* Dashicons css removed as it is no longer needed.
* Numerous WordPress coding standard improvements.

= 3.5.2 =
* Fix image url if image already points to CDN. PR #50
* Internal refactorings

= 3.5.1 =
* Minor code refactorings.

= 3.5.0 =
* Unit Tests.
* Added support for github updater. Issue #44
* Fix get_the_ID issue in WordPress below 4.0.
* Remove extra quotation mark that was making the html invalid.
* Added uninstall script.

= 3.4.0 =
* Added _Link to Image_ field.
* Portuguese Brazilian translation (Thanks to @bruno-rodrigues).
* And some minor refactorings.

= 3.3.1 =
* Increased code quality

= 3.3.0 =
* Fixed Invalid image path returned - Pull Request #35
* Added dfi_post_type_user_filter to disable metabox in post types.
* Added filter to change metabox title.
* Some minor fixes.

= 3.1.13 =
* Added post types filter - Pull Request #32 
* Fixed issue #33 - Incorrect data return when no image attached.
* Fixed issue #34 - Problem Retrieving Images in HTTPS Protocol.
* Revised code quality.
* Added Italian translation.

= 3.1.9 =
* Changed the scope of function get_image_id()
* Fixed typo in Nepali translation.

= 3.1.7 =
* Added Nepali, Swedish, Hebrew, Serbian, Croation and Bosnian languages.
* Fixed bug on issue #25 solution.
* Various code quality improvements.

= 3.1.2 =
* Fixed issue #25.

= 3.1.0 =
* Partial fix for issue #22.
* Increased code quality.

= 3.0.1 =
* Fixed several JSLint issues

= 3.0.0 =
* Fully Object Oriented (Thanks to @cfoellmann).
* New WordPress Media Uploader.
* Uses dashicons instead of images.
* Functions to retrieve image descriptions and nth featured image.
* Well documented.

= 2.0.2 =
* Minor css fix (issue #18 in GitHub, Thanks to @cfoellmann)

= 2.0.1 =
* Change in design.

= 2.0.0 =
* Now with various helper functions.
* Helpers to retrieve alt, title and caption of each featured image.
* Added support for remote url.
* WordPress 3.7 compatible.
* Primarily focused on theme developers.

= 1.1.5 =
* Fixed PHP Notice issues in strict debugging mode (Issue #4 in GitHub, Thanks to @Micky Hulse).
* Added post id in media upload box.
* Enhanced MultiSite Support.

= 1.1.2 =
* Resolved media uploader conflicts.

= 1.1.1 =
* Fixed a bug on user access for edit operation.

= 1.1.0 =
* Major security update
* Now uses AJAX to create new featured box
 
= 1.0.3 =
* First stable version with minimum features released.
* Fixed bug for duplicate id.
* Updated dfiGetFeaturedImages function to accept post id.
* Fixed some minor issues.

== Upgrade Notice ==
= 3.7.0 =
* Autoselect feature and attachment id bug fix.

= 3.6.8 =
* Some feature and bug fixes.

= 3.6.5 =
* Some enhancements, feature and bug fixes.

= 3.6.1 =
* Remove short array syntax to support php <= 5.4.

= 3.6.0 =
* Various security fixes.

= 3.5.2 =
* Some refactorings and bug fixes.

= 3.5.1 =
* Minor code refactorings.

= 3.5.0 =
* Unit tests and bug fixes.

= 3.4.0 =
Now with ability to add custom fields in media uploader for features targeted for future.

= 3.3.1 =
This version has no functionality change.

= 3.3.0 =
This version has multisite url bug fix and has added various useful filters.

= 3.1.13 =
This version has some major bug fix over ver. 3.1.9

= 3.1.9 =
This version has some bug fix over ver. 3.1.7

= 3.1.7 =
This version has translation in 6 different languages, bug fix on edited image and code quality improvement.

= 3.1.2 =
This version has bug fixes on edited image. View issue #25 in Github for more info.

= 3.1.0 =
This version has some bug fix and code quality improvement. You may need to change database value manually because the plugin finds the upload folder
automatically from now on.

= 3.0.1 =
This version has fixed various JSLint issues.

= 3.0.0 =
This version has major changes which are not compatible with the previous version of the plugin. The plugin is now fully object oriented.

= 2.0.2 =
This version has some minor css fix. Issue #18 in GitHub.

= 2.0.1 =
This version has just some graphics change to make it more attractive. Please clear the cache after update.

= 2.0.0 =
This version has some major updates and is much more powerful than before. Read the documentation carefully before update.