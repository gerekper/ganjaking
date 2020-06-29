<?php
/**
 * WooCommerce FreshBooks
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce FreshBooks to newer
 * versions in the future. If you wish to customize WooCommerce FreshBooks for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-freshbooks/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * FreshBooks Product Admin class
 *
 * Handles customizations to the Product edit screen
 *
 * @since 3.0
 */
class WC_FreshBooks_Products_Admin {


	/**
	 * Add various admin hooks/filters
	 *
	 * @since 3.0
	 */
	public function __construct() {

		// add FreshBooks item mapping for simple products
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_simple_product_item_mapping' ) );

		// save FreshBooks item mapping for simple products
		add_action( 'woocommerce_process_product_meta', array( $this, 'process_simple_product_item_mapping' ) );

		// TODO: grouped product support?

		// add FreshBooks item mapping for individual variations
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_variable_product_item_mapping' ), 1, 3 );

		// save FreshBooks item mapping for individual variations
		add_action( 'woocommerce_save_product_variation', array( $this, 'process_variable_product_item_mapping' ) );

		// Add JS to bulk change variation item mapping
		add_action( 'woocommerce_product_data_panels', array( $this, 'add_variable_product_item_mapping_js' ) );
	}


	/**
	 * Add 'FreshBooks Item' select on 'General' tab of simple product write-panel
	 *
	 * @since 3.0
	 */
	public function add_simple_product_item_mapping() {
		global $post;

		$product       = $post ? wc_get_product( $post ) : null;
		$saved_item_id = $product instanceof \WC_Product ? $product->get_meta( '_wc_freshbooks_item_name', true ) : '';

		?>
		<div class="options_group wc_freshbooks hide_if_grouped hide_if_variable">
			<p class="form-field wc_freshbooks_item_name_field">
				<label for="wc_freshbooks_item_name"><?php esc_html_e( 'FreshBooks Item', 'woocommerce-freshbooks' ); ?></label>
				<select
					id="wc_freshbooks_item_name"
					name="wc_freshbooks_item_name">
					<option value="none"><?php esc_html_e( 'None', 'woocommerce-freshbooks' ); ?></option>
					<?php foreach ( $this->get_active_items() as $item_id => $item_name ) : ?>
						<option value="<?php echo esc_attr( $item_id ); ?>" <?php selected( $item_id, $saved_item_id, true ); ?>><?php echo esc_html( $item_name ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php echo wc_help_tip( __( 'Choose which FreshBooks item to link to this product.', 'woocommerce-freshbooks' ) ); ?>
			</p>
		</div>
		<?php
	}


	/**
	 * Save 'FreshBooks Item' select box on 'General' tab of simple product write-panel
	 *
	 * @since 3.0
	 * @param int $post_id post ID of product being saved
	 */
	public function process_simple_product_item_mapping( $post_id ) {

		if ( isset( $_POST['wc_freshbooks_item_name'] ) && ( $product = wc_get_product( $post_id ) ) ) {

			if ( 'none' === $_POST['wc_freshbooks_item_name'] ) {
				$product->delete_meta_data( '_wc_freshbooks_item_name' );
			} else {
				$product->update_meta_data( '_wc_freshbooks_item_name', $_POST['wc_freshbooks_item_name'] );
			}

			$product->save_meta_data();
		}
	}


	/**
	 * Add 'FreshBooks Item' select box on 'Variations' tab of variable product write-panel.
	 *
	 * @since 3.0
	 * @param int $loop_count current variation count.
	 * @param array $variation_data individual variation data.
	 * @param \WP_Post $variation_post The product variation post object.
	 */
	public function add_variable_product_item_mapping( $loop_count, $variation_data, $variation_post ) {

		$variation_product = wc_get_product( $variation_post );
		$variation_data    = array_merge( $variation_product->get_meta_data(), $variation_data );

		$saved_item_id = isset( $variation_data['_wc_freshbooks_item_name'][0] ) ? $variation_data['_wc_freshbooks_item_name'][0] : '';

		?>
		<div class="clearfix">
			<p class="form-row form-row-first">
				<label for="<?php echo esc_attr( 'wc_freshbooks_item_name_' . $loop_count ); ?>">
					<?php esc_html_e( 'FreshBooks Item', 'woocommerce-freshbooks' ); ?>
					<?php echo wc_help_tip( __( 'Choose which FreshBooks item to link to this product.', 'woocommerce-freshbooks' ) ); ?>
				</label>
				<select
					id="<?php echo esc_attr( 'wc_freshbooks_item_name_' . $loop_count ); ?>"
					name="<?php printf( '%1$s[%2$s]', 'variable_wc_freshbooks_item_name', $loop_count ); ?>"
					class="js-wc-freshbooks-item-select"
					style="min-width: 250px;">
					<option value="none"><?php esc_html_e( 'None', 'woocommerce-freshbooks' ); ?></option>
					<?php foreach ( $this->get_active_items() as $item_id => $item_name ) : ?>
						<option value="<?php echo esc_attr( $item_id ); ?>" <?php selected( $item_id, $saved_item_id, true ); ?>><?php echo esc_html( $item_name ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p class="form-row form-row-last" style="margin-top: 2.8em;">
				<a id="<?php echo 'wc_freshbooks_item_name_' . $loop_count; ?>" class="js-wc-freshbooks-bulk-set-items" href="#"><?php esc_html_e( 'Set all other variations to this item', 'woocommerce-freshbooks' ); ?></a>
			</p>
		</div>
		<?php
	}


	/**
	 * Add 'Set all other variations to this item' javascript
	 *
	 * @since 3.0
	 */
	public function add_variable_product_item_mapping_js() {

		wc_enqueue_js( '
			$( ".js-wc-freshbooks-bulk-set-items" ).click( function() {
				var selector = $( this ).attr( "id" );
				$( ".js-wc-freshbooks-item-select" ).val( $( "#" + selector ).val() );
				return false;
			} );
		' );
	}


	/**
	 * Save items mapped to product variations.
	 *
	 * @since 3.0
	 * @param int $variation_id The product variation ID.
	 */
	public function process_variable_product_item_mapping( $variation_id ) {

		// find the index for the given variation ID and save the associated item ID
		if ( false !== ( $i = array_search( $variation_id, $_POST['variable_post_id'], false ) ) ) {

			$variation_product = wc_get_product( $variation_id );

			if ( $variation_product && isset( $_POST['variable_wc_freshbooks_item_name'] ) && 'none' !== $_POST['variable_wc_freshbooks_item_name'][ $i ] ) {

				$variation_product->update_meta_data( '_wc_freshbooks_item_name', $_POST['variable_wc_freshbooks_item_name'][ $i ] );
				$variation_product->save_meta_data();
			}
		}
	}


	/**
	 * Gets an array of active invoice items.
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	private function get_active_items() {

		$active_items = array();

		try {

			foreach ( wc_freshbooks()->get_api()->get_active_items() as $item ) {

				$active_items[ $item['name'] ] = $item['name'];
			}

		} catch ( Framework\SV_WC_API_Exception $e ) {

			wc_freshbooks()->log( $e->getMessage() );
		}

		return $active_items;
	}


}
