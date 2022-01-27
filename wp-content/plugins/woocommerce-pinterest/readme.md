# WooCommerce Pinterest
WordPress/Woocommerce plugin for creating Pinterest pins, catalogs and tracking events on site using Pinterest Analytics.

## Requirements
* WordPress 4.8+
* Woocommerce 3.0+

## Features
* Create pins from products
* Deferred pins creation
* Generate product catalog for Pinterest
* Add #hashtags to pins
* Update pins when products data updated.
* Rich Product Pins metadata at product page.
* Pinterest Track Conversations javascript events.

## Hooks

### Catalog filters
*'woocommerce_pinterest_catalog_product_types'* - allows to change products types which will be added to catalog

#### Rich pins filters 
*'woocommerce_pinterest_og_data'* (array $data) - allows to add custom og meta tag

#### Admin filters
*'woocommerce_pinterest_admin_description_variables'* (array $variables) - allows to add custom variables
*'woocommerce_pinterest_defer_pins_days'* (array $days) - allows to filter available days for deferred pins
*'woocommerce_pinterest_defer_pins_intervals'* (array $intervals) - allows to filter available intervals for deferred pins
*'woocommerce_pinterest_defer_pins_times'* (array $times) - allows to filter available times for deferred pins
*'woocommerce_pinterest_defer_pins_per_interval'* (array $pinsNumber) - allows to filter available options of number of pins per interval
*'woocommerce_pinterest_boards_options'* (array $boards, PinterestIntegration $integration) - allows to filter available Pinterest boards list when selecting default board in General settings section
*'woocommerce_pinterest_found_pins_for_table'* (array $data, PinsTable $table, PinModel $pinModel) - allows to filter pins which will be displayed in Pins table


#### Pin creation filters
*'woocommerce_pinterest_description_template'* (string $template, array $pin) - allows change description template dynamically
*'woocommerce_pinterest_description_placeholders'* (array $placeholders, array $pin) - allows to replace or add custom variables to pin template
*'woocommerce_pinterest_pin_data'* (array $data, array $pin) - allows to replace data before update or create request
*'woocommerce_pinterest_description_tags'* (array $tags, array $pin) - allows to filter Pinterest tags which will be added to pin
*'woocommerce_pinterest_description'* (string $note, array $pin, \WC_Product $product) - allows to filter the whole pin description before it will be sent to Pinterest
*'woocommerce_pinterest_api_sanitize_pin'* (array $sanitized, array $pin) - allows to modify pin data just before it will be sent to Pinterest. Runs after pin data was sanitized.

## Legal
### Authors
[Premmerce](https://premmerce.com)

### License
This project is licensed under the [GPLv2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html) license.