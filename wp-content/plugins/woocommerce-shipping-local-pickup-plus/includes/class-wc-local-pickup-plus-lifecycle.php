<?php
/**
 * WooCommerce Local Pickup Plus
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Local Pickup Plus lifecycle scripts.
 *
 * Static class that handles custom table creations as well as upgrade scripts
 * from older to newer versions of Local Pickup Plus.
 *
 * @since 2.0.0
 *
 * @method \WC_Local_Pickup_Plus get_plugin()
 */
class WC_Local_Pickup_Plus_Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 2.4.1
	 *
	 * @param $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'1.13.5',
			'2.0.0',
			'2.3.4',
		];
	}


	/**
	 * Runs install scripts.
	 *
	 * @since 2.0.0
	 */
	protected function install() {

		$this->create_tables();
	}


	/**
	 * Gets table names used by Local Pickup Plus.
	 *
	 * @since 2.0.0
	 *
	 * @return string[]
	 */
	public function get_table_names() {
		global $wpdb;

		return [
			"{$wpdb->prefix}woocommerce_pickup_locations_geodata",
		];
	}


	/**
	 * Create new tables in WordPress database.
	 *
	 * @see \WC_Local_Pickup_Plus_Lifecycle::get_schema()
	 *
	 * @since 2.0.0
	 */
	public function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		foreach ( $this->get_table_names() as $table_name ) {

			if ( $table_name !== $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) ) {

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

				dbDelta( $this->get_schema() );

				break;
			}
		}
	}


	/**
	 * Get database schema for tables introduced by Local Pickup Plus.
	 *
	 * Local Pickup Plus adds a custom table to WordPress database:
	 *
	 * - The Pickup Locations Geodata table.
	 *
	 *   Stores all useful geographical data associated to a pickup location for fast lookup.
	 *   Normally we'd be most interested to lat,lon and matching location/post_id; but fields like country/state can help narrow down a lookup or if geocoding is not available, help out querying locations within a certain country/state area and so on:
	 *
	 *    - `post_id`: matches the post id of the pickup location post type as found in the posts table
	 *    - `lat`: latitude of the pickup location, this information is stored when the address is successfully geocoded
	 *    - `lon`: longitude of the pickup location, this information is stored when the address is successfully geocoded
	 *    - `country`: the pickup location country as 2-digit ISO code, like in WC core
	 *    - `state`: the pickup location state or secondary administrative entity (e.g. region, county...), variable digits according to country, like in WC core
	 *    - `city`: the pickup location city, variable length
	 *    - `postcode`: the pickup location postcode, variable alphanumeric length
	 *    - `address_1`: the pickup location address first line
	 *    - `address_2`: the pickup location address second line (optional)
	 *    - `last_updated`: a datetime stamp marking when the geodata was last updated
	 *
	 * @since 2.0.0
	 *
	 * @return string MySQL
	 */
	private function get_schema() {
		global $wpdb;

		$collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';
		$tables  = "

CREATE TABLE {$wpdb->prefix}woocommerce_pickup_locations_geodata (
  post_id BIGINT(20) unsigned NOT NULL default 0,
  lat DECIMAL(11,6) NOT NULL default 0,
  lon DECIMAL(11,6) NOT NULL default 0,
  title TEXT NOT NULL,
  state VARCHAR(180) NOT NULL default '',
  country VARCHAR(2) NOT NULL default '',
  postcode VARCHAR(40) NOT NULL default '',
  city VARCHAR(200) NOT NULL default '',
  address_1 VARCHAR(255) NOT NULL default '',
  address_2 VARCHAR(255) NOT NULL default '',
  last_updated DATETIME NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (post_id ASC),
  UNIQUE INDEX (post_id ASC),
  KEY coordinates (lat ASC, lon ASC),
  KEY country_state (country ASC, state ASC)
) $collate;
		";

		return $tables;
	}


	/**
	 * Runs upgrade scripts.
	 *
	 * @since 2.0.0
	 *
	 * @param string $installed_version the version that is being updated
	 */
	protected function upgrade( $installed_version ) {

		// versions before 1.13.5 did not have a version flag
		if ( empty( $installed_version ) || version_compare( $installed_version, '1.13.5', '<' ) ) {
			$installed_version = '1.13.4';
		}

		parent::upgrade( $installed_version );
	}


	/**
	 * Updates older versions which did not have a version flag.
	 *
	 * @since 2.4.1
	 */
	protected function upgrade_to_1_13_5() {

		// Very old versions may had featured a bug where pickup locations did not have a location country set.
		if (    ( $pickup_locations = get_option( 'woocommerce_pickup_locations' ) )
		     && ( $default_country  = get_option( 'woocommerce_default_country' ) ) ) {

			$default_country = explode( ':', $default_country );
			$default_country = $default_country[0];
			$update_option   = false;

			// if country is not set, we can only assume this is the shop country:
			foreach ( $pickup_locations as $key => $location ) {

				if ( $default_country && empty( $location['country'] ) ) {

					$pickup_locations[ $key ]['country'] = $default_country;

					$update_option = true;
				}
			}

			// Do not force update the option if there's no change.
			// May be helpful avoiding this on large installs with thousands of pickup locations.
			if ( $update_option ) {
				update_option( 'woocommerce_pickup_locations', $pickup_locations );
			}
		}
	}


	/**
	 * Upgrades plugin to version 2.0.0.
	 *
	 * @since 2.4.1
	 */
	protected function upgrade_to_2_0_0() {

		$this->create_tables();

		$plugin = $this->get_plugin();

		$plugin->log( 'Begin upgrade to version 2.0.0...' );

		// upgrade settings
		if ( $legacy_options = get_option( 'woocommerce_local_pickup_plus_settings' ) ) {

			if (      isset( $legacy_options['categories'] )
			     && ! empty( $legacy_options['categories'] )
			     &&   ( is_array( $legacy_options['categories'] ) || is_numeric( $legacy_options['categories'] ) ) ) {

				$pickup_categories = is_numeric( $legacy_options['categories'] ) && $legacy_options['categories'] > 0 ? (array) $legacy_options['categories'] : $legacy_options['categories'];

				if ( is_array( $pickup_categories ) ) {

					// maybe set some categories for pickup-only
					foreach( $pickup_categories as $product_cat_id ) {
						if ( is_numeric( $product_cat_id ) && term_exists( (int) $product_cat_id, 'product_cat' ) ) {
							update_term_meta( (int) $product_cat_id, '_wc_local_pickup_plus_local_pickup_product_cat_availability', 'required' );
						}
					}

					// optionally, disallow all other categories to be picked up
					if (    ! empty( $pickup_categories )
					     &&   isset( $legacy_options['categories_pickup_only'] )
					     &&   'yes' === $legacy_options['categories_pickup_only'] ) {

						$product_cats = get_terms( [
							'taxonomy'   => 'product_cat',
							'fields'     => 'ids',
							'exclude'    => $pickup_categories,
							'hide_empty' => false,
						] );

						if ( is_array( $product_cats ) ) {
							foreach ( $product_cats as $product_cat_id ) {
								update_term_meta( $product_cat_id, '_wc_local_pickup_plus_local_pickup_product_cat_availability', 'disallowed' );
							}
						}
					}
				}
			}

			// maybe import a default price adjustment
			if ( isset( $legacy_options['cost'] ) || isset( $legacy_options['discount'] ) ) {

				if ( ! empty( $legacy_options['cost'] ) ) {
					$price_adjustment_key = 'cost';
				} elseif ( ! empty( $legacy_options['discount'] ) ) {
					$price_adjustment_key = 'discount';
				}

				if ( isset( $price_adjustment_key ) ) {

					$price_adjustment = is_string( $legacy_options[ $price_adjustment_key ] ) ? trim( $legacy_options[ $price_adjustment_key ] ) : $legacy_options[ $price_adjustment_key ];

					if ( ! empty( $price_adjustment ) || is_numeric( $price_adjustment ) ) {

						$adjustment_type   = Framework\SV_WC_Helper::str_starts_with( (string) $price_adjustment, '-' ) ? 'discount'   : 'cost';
						$adjustment_unit   = Framework\SV_WC_Helper::str_ends_with(   (string) $price_adjustment, '%' ) ? 'percentage' : 'fixed';
						preg_match_all( '!\d+(?:\.\d+)?!', (string) $price_adjustment, $matches );
						$adjustment_amount = ! empty( $matches[0] ) ? trim( current( $matches[0] ) ) : null;

						if ( is_numeric( $adjustment_amount ) ) {

							// validate and sanitize a valid price adjustment string
							$default_price_adjustment = new \WC_Local_Pickup_Plus_Price_Adjustment();
							$default_price_adjustment->set_value( $adjustment_unit, (float) $adjustment_amount, $adjustment_type );

							update_option( 'woocommerce_local_pickup_plus_default_price_adjustment', $default_price_adjustment->get_value() );
						}
					}
				}
			}

			$plugin->log( 'Upgraded settings options.' );

		} else {

			$plugin->log( 'Settings options could not be upgraded.' );
		}

		// upgrade pickup locations
		$legacy_pickup_locations = get_option( 'woocommerce_pickup_locations' );

		if ( ! empty( $legacy_pickup_locations ) && is_array( $legacy_pickup_locations ) ) {

			$processed = 0;
			$skipped   = 0;

			require_once( $this->get_plugin()->get_plugin_path() . '/includes/class-wc-local-pickup-plus-post-types.php' );

			\WC_Local_Pickup_Plus_Post_Types::init();

			foreach ( $legacy_pickup_locations as $legacy_pickup_location ) {

				$post_id = wp_insert_post( [
					'post_type'    => 'wc_pickup_location',
					'post_status'  => isset( $legacy_pickup_location['country'] ) ? 'publish'                                                 : 'draft',
					'post_title'   => isset( $legacy_pickup_location['company'] ) ? sanitize_text_field( $legacy_pickup_location['company'] ) : '',
					'post_content' => isset( $legacy_pickup_location['note'] )    ? wp_kses_post( $legacy_pickup_location['note'] )           : '',
				] );

				if ( is_numeric( $post_id ) ) {

					$pickup_location = new \WC_Local_Pickup_Plus_Pickup_Location( $post_id );
					$pickup_location->set_address( [
						'name'      => isset( $legacy_pickup_location['company'] )   ? sanitize_text_field( $legacy_pickup_location['company'] ) : '',
						'country'   => isset( $legacy_pickup_location['country'] )   ? strtoupper( sanitize_text_field( $legacy_pickup_location['country'] ) ) : '',
						'state'     => isset( $legacy_pickup_location['state'] )     ? strtoupper( sanitize_text_field( $legacy_pickup_location['state'] ) )   : '',
						'postcode'  => isset( $legacy_pickup_location['postcode'] )  ? sanitize_text_field( $legacy_pickup_location['postcode'] )              : '',
						'city'      => isset( $legacy_pickup_location['city'] )      ? sanitize_text_field( $legacy_pickup_location['city'] )                  : '',
						'address_1' => isset( $legacy_pickup_location['address_1'] ) ? sanitize_text_field( $legacy_pickup_location['address_1'] )             : '',
						'address_2' => isset( $legacy_pickup_location['address_2'] ) ? sanitize_text_field( $legacy_pickup_location['address_2'] )             : '',
					] );

					if ( ! empty( $legacy_pickup_location['phone'] ) ) {
						$pickup_location->set_phone( sanitize_text_field( $legacy_pickup_location['phone'] ) );
					}

					if ( ! empty( $legacy_pickup_location['cost'] ) ) {

						$legacy_adjustment = is_string( $legacy_pickup_location['cost'] ) ? trim( $legacy_pickup_location['cost'] ) : $legacy_pickup_location['cost'];

						if ( ! empty( $legacy_adjustment ) || is_numeric( $legacy_adjustment ) ) {

							$adjustment_type   = Framework\SV_WC_Helper::str_starts_with( (string) $legacy_adjustment, '-' ) ? 'discount'   : 'cost';
							$adjustment_unit   = Framework\SV_WC_Helper::str_ends_with(   (string) $legacy_adjustment, '%' ) ? 'percentage' : 'fixed';
							preg_match_all( '!\d+(?:\.\d+)?!', (string) $legacy_adjustment, $matches );
							$adjustment_amount = ! empty( $matches[0] ) ? trim( current( $matches[0] ) ) : null;

							if ( is_numeric( $adjustment_amount ) ) {

								$pickup_location->set_price_adjustment( $adjustment_type, abs( (float) $adjustment_amount ), $adjustment_unit );

								// this flag is used to enable the override in the metabox
								update_post_meta( $post_id, '_pickup_location_price_adjustment_enabled', 'yes' );
							}
						}
					}

					$processed++;

				} else {

					$skipped++;
				}
			}

			if ( $processed > 0 || $skipped > 0 ) {

				if ( $processed > 0 ) {
					$plugin->log( 1 === $processed ? 'Upgraded 1 pickup location'          : sprintf( 'Upgraded %d pickup locations', $processed ) );
				}

				if ( $skipped > 0 ) {
					$plugin->log( 1 === $skipped   ? 'Could not upgrade 1 pickup location' : sprintf( 'Could not upgrade %d pickup locations', $skipped ) );
				}

				// add an admin notice to remind the admin to check for the updated pickup locations to post types
				$plugin->get_admin_notice_handler()->add_admin_notice(
					/* translators: Placeholders: %1$s - opening <a> link tag, %2$s closing </a> link tag */
					sprintf( __( 'Pickup locations have been upgraded to a new format! All location data should be the same, but %1$splease double-check them here%2$s.', 'woocommerce-shipping-local-pickup-plus' ), '<a href="' . admin_url( 'edit.php?post_type=wc_pickup_location' ) . '">', '</a>' ),
					'wc-local-pickup-plus-pickup-locations-upgraded-to-post-type',
					[
						'always_show_on_settings' => true,
						'notice_class'            => 'updated',
					]
				);
			}

		} else {

			$plugin->log ( 'No pickup locations found to upgrade.' );
		}

		// finally delete the legacy option meant for storing pickup locations
		delete_option( 'woocommerce_pickup_locations' );
	}


	/**
	 * Updates the plugin to version 2.3.4
	 *
	 * @since 2.4.1
	 */
	protected function upgrade_to_2_3_4() {
		global $wpdb;

		// set state length to 180 for consistency with new installs
		$wpdb->query( "
			ALTER TABLE {$wpdb->prefix}woocommerce_pickup_locations_geodata
			MODIFY state VARCHAR(180)
		" );
	}


}
