=== Force Regenerate Thumbnails ===
Contributors: Pedro Elsner
Requires at least: 2.8
Tested up to: 4.0
Stable tag: trunk
Tags: force, regenerate thumbnails, thumbnail, thumbnails

Delete and REALLY force the regenerate thumbnail.

== Description ==

Force Regenerate Thumbnails allows you to delete all old images size and REALLY regenerate the thumbnails for your image attachments.

See the [screenshots tab](http://wordpress.org/extend/plugins/force-regenerate-thumbnails/screenshots/) for more details.

== Installation ==

1. Go to your admin area and select Plugins -> Add new from the menu.
2. Search for "Force Regenerate Thumbnails".
3. Click install.
4. Click activate.

== Screenshots ==

1. The plugin at work regenerating thumbnails
2. You can resize specific multiples images using the checkboxes and the "Bulk Actions" dropdown

== ChangeLog ==

= 2.0.6 =
* Add PHP7 compatibility

= 2.0.5 =
* No timeout limit

= 2.0.4 =
* Fix issue when "opendir()" return FALSE (thanks Krody Robert)

= 2.0.3 =
* Add debug information on regenerate
* Fix issue with update "_wp_attachment_metadata" and "_wp_attached_file" on windows

= 2.0.2 =
* New style for results (thanks @justonarnar)
* Automatic update "_wp_attachment_metadata" and "_wp_attached_file" (thanks @norecipes)

= 2.0.1 =
* Fix issue with get_option('upload_path') in Wordpress 3.5+ (thanks @DavidLingren)

= 2.0.0 = 
* Fix error handle

= 1.8 =
* New function to display ajax results

= 1.7 =
* Fix issue with getters path in Linux/Windows/Unix servers

= 1.6 =
* New CORE to regenerate thumbnails

= 1.5 =
* Reviewed some messages

= 1.4 =
* Change default image editor to GB in Wordpress 3.5+ (thanks @nikcree)

= 1.3 =
* Fix message error when WP_DEBUG in wp_config.php

= 1.2 =
* Fix for JPEG images

= 1.1 =
* Delete all custom image sizes when regenerate thumbnails
* Notifies you when thumbnails was deleted 

= 1.0 =
* First release.

== Upgrade Notice ==
