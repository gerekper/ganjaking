# WPMU DEV Free Notices module #

WPMU DEV Free Notices module (short wpmu-free-notice) is used in our free plugins hosted on WordPress.org
It will display a welcome message upon plugin activation that offers the user a 5-day introduction email course for the plugin. After 7 days the module will display another message asking the user to rate the plugin on WordPress.org

# How to use it #

1. Insert this repository as **sub-module** into the existing project

2. Include the file `module.php` in your main plugin file.

3. Call the action `wdev_register_plugin` with the params mentioned below.

4. Done!

# Upgrading from 1.3.0 version #

The 2.0.0 release is backward incompatible with the 1.x versions. To accommodate new functionality and fix WordPress coding standards violations, a lot of the hooks/filters have been refactored.
Make sure to change the following:

1. Update the `do_action` hook name from `wdev-register-plugin` to `wdev_register_plugin`.
2. Both `wdev-email-message-` and `wdev-rating-message-` filters have been changed to `wdev_email_title_`/`wdev_email_message_` and `wdev_rating_title_`/`wdev_rating_message_`

## Code Example (from Smush ) ##

```
#!php

<?php
// Load the WPMU_Free_Notice module.
include_once 'lib/wdev-frash/module.php';

// Register the current plugin.
do_action(
	'wdev_register_plugin',
	/* 1             Plugin ID */ plugin_basename( __FILE__ ),
	/* 2          Plugin Title */ 'Smush',            
	/* 3 https://wordpress.org */ '/plugins/wp-smushit/',
	/* 4      Email Button CTA */ __( 'Get Fast!', MYD_TEXT_DOMAIN ),  
	/* 5  Mailchimp List id for the plugin - e.g. 4b14b58816 is list id for Smush */ '4b14b58816'
);
// All done!
```

1. Always same, do not change
2. The plugin title, same as in the plugin header (no translation!)
3. The WordPress.org plugin-URL
4. Optional: Title of the Email-subscription button. If empty no email message is displayed.
5. Optional: Mailchimp List id for the plugin. If empty no email message is displayed


## Optional: Customize the messages via filters ##

```
<?php
// The email message contains 1 variable: plugin-name
add_filter(
    'wdev_email_message_' . plugin_basename( __FILE__ ),
    'custom_email_message'
);
function custom_email_message( $message ) {
    $message = 'You installed %s! This is a custom <u>email message</u>';
    return $message;
}
```

```
<?php
// The rating message contains 2 variables: user-name, plugin-name
add_filter(
    'wdev_rating_message_' . plugin_basename( __FILE__ ),
    'custom_rating_message'
);
function custom_rating_message( $message ) {
    $message = 'Hi %s, you used %s for a while now! This is a custom <u>rating message</u>';
    return $message;
}
```
