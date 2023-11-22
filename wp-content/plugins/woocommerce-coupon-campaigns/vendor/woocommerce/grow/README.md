# Compat Checker for WooCommerce Extensions

A simple library to run compatibility checks for WooCommerce extensions.

## Getting Started

1. Include this library in your WooCommerce plugin's `composer.json` like shown below:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/woocommerce/grow"
        }
    ],
    "require": {
        "woocommerce/grow": "dev-compat-checker"
    }
}
```
2. Run `composer update` to include the `woocommerce/grow` repo in the `vendor` folder.

3. In the main plugin file that contains the plugin header, add the compatibility check like the below example:

```php
require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Grow\Tools\CompatChecker\v0_0_1\Checker;

add_action( 'plugins_loaded', 'wc_plugin_init' );

function wc_plugin_init() {
    define( 'WC_BRANDS_VERSION', '1.6.56' ); // WRCS: DEFINED_VERSION.

    if ( ! Checker::instance()->is_compatible( __FILE__, WC_BRANDS_VERSION ) ) {
		return;
	}

    // Continue initializing the plugin.
}
```