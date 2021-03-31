# SkyVerge Plugin Admin
A self-contained package for the WordPress administrative area for SkyVerge plugins.

## Requirements
- PHP 5.6+
- WooCommerce 3.0+

## Installation

1. Require via composer:
    ```json
    {
        "repositories": [
            {
              "type": "vcs",
              "url": "https://github.com/skyverge/wordpress-plugin-admin"
            }
        ],
        "require": {
            "skyverge/wordpress-admin": "1.0.0"
        }
    }
    ```
1. Require the loader:
    ```php
    require_once( 'vendor/skyverge/wordpress-plugin-admin/load.php' );
    ```
    - Must be required _before_ `plugins_loaded`
    - Must be required _after_ any environmental checks, like PHP version (5.6+) or WooCommerce active/version checks

#### Customization

##### Filters

###### Messages JSON

* `sv_wordpress_plugin_admin_messages_data_url`: change where to retrieve the messages data from.

###### Staging scripts

* `sv_wordpress_plugin_admin_client_script_url`: change where to load the main React JS file from (the staging path is https://dashboard-assets.skyverge.com/scripts/staging/index.js)

* `sv_wordpress_plugin_admin_client_admin_script_url`: change where to load the admin menu JS file from (the staging path is https://dashboard-assets.skyverge.com/scripts/staging/admin.js)

## Development

* Compile assets: `gulp compile`
