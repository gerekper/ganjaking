<?php
/**
 * WooCommerce Measurement Price Calculator
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Measurement Price Calculator to newer
 * versions in the future. If you wish to customize WooCommerce Measurement Price Calculator for your
 * needs please refer to http://docs.woocommerce.com/document/measurement-price-calculator/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Measurement_Price_Calculator;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 3.14.0
 *
 * @method \WC_Measurement_Price_Calculator get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 3.14.1
	 *
	 * @param \WC_Measurement_Price_Calculator $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'3.0.0',
		];
	}


	/**
	 * Handles initial plugin installation routine.
	 *
	 * Note that with version 3.3 of the plugin the database version option name changed, so this also handles the case of updating in that circumstance.
	 *
	 * @since 3.14.0
	 */
	protected function install() {
		global $wpdb;

		// check for a pre 3.3 version
		$legacy_version = get_option( 'wc_measurement_price_calculator_db_version' );

		if ( false !== $legacy_version ) {

			// upgrade path from previous version, trash old version option
			delete_option( 'wc_measurement_price_calculator_db_version' );

			// upgrade path
			$this->upgrade( $legacy_version );

			// and we're done
			return;
		}

		// true install
		require_once( $this->get_plugin()->get_plugin_path() . '/includes/class-wc-price-calculator-settings.php' );

		// set the default units for our custom measurement types
		add_option( 'woocommerce_area_unit',   \WC_Price_Calculator_Settings::DEFAULT_AREA );
		add_option( 'woocommerce_volume_unit', \WC_Price_Calculator_Settings::DEFAULT_VOLUME );

		// Upgrade path from pre-versioned 1.x
		// get all old-style measurement price calculator products
		$rows = $wpdb->get_results( "SELECT post_id, meta_value FROM " . $wpdb->postmeta . " WHERE meta_key='_measurement_price_calculator'" );

		foreach ( $rows as $row ) {

			if ( $row->meta_value ) {

				// calculator is enabled
				$product_custom_fields = get_post_custom( $row->post_id );

				// as long as the product doesn't also already have a new-style price calculator settings
				if ( ! isset( $product_custom_fields['_wc_price_calculator'][0] ) || ! $product_custom_fields['_wc_price_calculator'][0] ) {

					// we want the underlying raw settings array
					$settings = new \WC_Price_Calculator_Settings();
					$settings = $settings->get_raw_settings();

					switch ( $row->meta_value ) {

						case 'dimensions':

							$settings['calculator_type'] = 'dimension';

							// The previous version of the plugin allowed this weird multi-dimension tied input thing,
							// I don't think anyone actually used it, and it didn't make much sense, so I'm not supporting it any longer.
							if ( 'yes' === $product_custom_fields['_measurement_dimension_length'][0] ) {
								$settings['dimension']['length']['enabled']  = 'yes';
								$settings['dimension']['length']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
								$settings['dimension']['length']['editable'] = $product_custom_fields['_measurement_dimension_length_editable'][0];
							} elseif ( 'yes' === $product_custom_fields['_measurement_dimension_width'][0] ) {
								$settings['dimension']['width']['enabled']  = 'yes';
								$settings['dimension']['width']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
								$settings['dimension']['width']['editable'] = $product_custom_fields['_measurement_dimension_width_editable'][0];
							} elseif ( 'yes' === $product_custom_fields['_measurement_dimension_height'][0] ) {
								$settings['dimension']['height']['enabled']  = 'yes';
								$settings['dimension']['height']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
								$settings['dimension']['height']['editable'] = $product_custom_fields['_measurement_dimension_height_editable'][0];
							}

						break;

						case 'area':

							$settings['calculator_type']          = 'area';
							$settings['area']['area']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
							$settings['area']['area']['editable'] = $product_custom_fields['_measurement_editable'][0];

						break;

						case 'volume':

							$settings['calculator_type']              = 'volume';
							$settings['volume']['volume']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
							$settings['volume']['volume']['editable'] = $product_custom_fields['_measurement_editable'][0];

						break;

						case 'weight':

							$settings['calculator_type']              = 'weight';
							$settings['weight']['weight']['unit']     = $product_custom_fields['_measurement_display_unit'][0];
							$settings['weight']['weight']['editable'] = $product_custom_fields['_measurement_editable'][0];

						break;

						case 'walls':

							$settings['calculator_type']                  = 'wall-dimension';
							$settings['wall-dimension']['length']['unit'] = $product_custom_fields['_measurement_display_unit'][0];
							$settings['wall-dimension']['width']['unit']  = $product_custom_fields['_measurement_display_unit'][0];

						break;
					}

					update_post_meta( $row->post_id, '_wc_price_calculator', $settings );
				}
			}
		}
	}


	/**
	 * Updates to version 3.0.0
	 *
	 * From version 2.0 going to version 3.0, the '_wc_price_calculator' product post meta calculator settings structure changed:
	 * 'calculator' was added to the 'pricing' option
	 *
	 * @since 3.14.1
	 */
	protected function upgrade_to_3_0_0() {
		global $wpdb;

		require_once( $this->get_plugin()->get_plugin_path() . '/includes/class-wc-price-calculator-settings.php' );

		$rows = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key='_wc_price_calculator'" );

		foreach ( $rows as $row ) {

			if ( $row->meta_value ) {

				// calculator settings found
				$settings = new \WC_Price_Calculator_Settings();
				$settings = $settings->set_raw_settings( $row->meta_value );  // we want the updated underlying raw settings array

				$updated = false;

				foreach ( \WC_Price_Calculator_Settings::get_measurement_types() as $measurement_type ) {

					if ( isset( $settings[ $measurement_type ]['pricing']['enabled'] ) && 'yes' === $settings[ $measurement_type ]['pricing']['enabled'] ) {
						// enable the pricing calculator in the new settings data structure
						$settings[ $measurement_type ]['pricing']['calculator'] = [ 'enabled' => 'yes' ];
						$updated = true;
					}
				}

				if ( $updated ) {

					if ( $product = wc_get_product( $row->post_id ) ) {

						$product->update_meta_data( '_wc_price_calculator', $settings );
						$product->save_meta_data();
					}
				}
			}
		}
	}


}
