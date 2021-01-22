<?php
/**
 * WooCommerce Bambora Gateway
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Bambora Gateway to newer
 * versions in the future. If you wish to customize WooCommerce Bambora Gateway for your
 * needs please refer to http://docs.woocommerce.com/document/bambora/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_Factory;

defined( 'ABSPATH' ) or exit;

/**
 * The Checkout Add-Ons plugin lifecycle handler.
 *
 * @since 2.0.0
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 2.0.2
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'1.6.1',
			'2.0.0',
			'2.3.0',
		];
	}


	/**
	 * Updates to v1.6.1
	 *
	 * @since 2.0.2
	 */
	protected function upgrade_to_1_6_1() {

		// add `woocommerce_checkout_` prefix to options
		$add_ons_position = get_option( 'wc_checkout_add_ons_position', 'after_customer_details' );

		update_option( 'wc_checkout_add_ons_position', 'woocommerce_checkout_' . $add_ons_position );
	}


	/**
	 * Updates to v2.0.0
	 *
	 * @since 2.0.2
	 */
	protected function upgrade_to_2_0_0() {
		global $wpdb;

		// remove now-unused option
		delete_option( 'wc_checkout_add_ons_next_add_on_id' );

		$old_add_ons   = get_option( 'wc_checkout_add_ons', array() );
		$add_on_id_map = array();

		// backup old add-ons in the event that upgrade fails or the plugin is rolled-back (would require manual restoration for a roll-back)
		add_option( 'wc_checkout_add_ons_legacy', $old_add_ons );

		// clear out the add-ons in preparation for the new, converted add-ons
		delete_option( 'wc_checkout_add_ons' );

		// convert old add-ons to new add-ons
		foreach ( $old_add_ons as $old_add_on_id => $old_add_on ) {

			if ( isset( $old_add_on['type'] ) ) {

				$new_add_on           = Add_On_Factory::create_add_on( $old_add_on['type'] );
				$attributes           = array();
				$supported_attributes = Add_On::get_supported_attributes();

				foreach ( $supported_attributes as $supported_attribute ) {
					if ( isset( $old_add_on[ $supported_attribute ] ) && $old_add_on[ $supported_attribute ] ) {
						$attributes[] = $supported_attribute;
					}
				}

				$options = array();

				if ( isset( $old_add_on['options'] ) ) {

					foreach ( $old_add_on['options'] as $option ) {

						$new_option = array(
							'label'           => isset( $option['label'] ) ? $option['label'] : '',
							'adjustment'      => isset( $option['cost'] ) ? $option['cost'] : 0,
							'adjustment_type' => isset( $option['cost_type'] ) && 'percent' === $option['cost_type'] ? 'percent' : 'fixed',
							'default'         => isset( $option['default'] ) ? (bool) $option['default'] : false,
						);

						if ( 'percent' === $new_option['adjustment_type'] ) {
							$new_option['adjustment'] *= 100;
						}

						$options[] = $new_option;
					}
				}

				$props = array(
					'name'            => isset( $old_add_on['name'] ) ? $old_add_on['name'] : '',
					'label'           => isset( $old_add_on['label'] ) ? $old_add_on['label'] : '',
					'adjustment'      => isset( $old_add_on['cost'] ) ? $old_add_on['cost'] : 0.0,
					'adjustment_type' => isset( $old_add_on['cost_type'] ) ? $old_add_on['cost_type'] : 'fixed',
					'is_taxable'      => isset( $old_add_on['tax_status'] ) ? 'taxable' === $old_add_on['tax_status'] : false,
					'tax_class'       => isset( $old_add_on['tax_class'] ) ? $old_add_on['tax_class'] : '',
					'attributes'      => $attributes,
					'options'         => $options,
				);

				$props['adjustment'] = 'percent' === $props['adjustment_type'] ? $props['adjustment'] * 100 : $props['adjustment'];

				$new_add_on->set_props( $props );
				$new_add_on->set_enabled( true );
				$new_add_on_id = $new_add_on->save();

				$add_on_id_map[ $old_add_on_id ] = $new_add_on_id;
			}
		}

		// update existing add-on ID references to the new format
		if ( ! empty( $add_on_id_map ) ) {

			foreach ( $add_on_id_map as $old_id => $new_id ) {

				$query = $wpdb->prepare( "
					UPDATE {$wpdb->prefix}woocommerce_order_itemmeta
					SET meta_value = '%s'
					WHERE meta_key = '_wc_checkout_add_on_id'
					AND meta_value = '%d'
				", $new_id, $old_id );

				$wpdb->query( $query );
			}
		}
	}


	/**
	 * Updates to v2.3.0.
	 *
	 * @since 2.3.0
	 */
	protected function upgrade_to_2_3_0() {

		// this value is intentionally different than the default value, to preserve the current behaviour for existing users
		update_option( 'woocommerce_checkout_add_ons_percentage_adjustment_from', Add_On::PERCENTAGE_ADJUSTMENT_TOTAL );
	}


}
