<?php
/**
 * WooCommerce Customer/Order/Coupon Export
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\CSV_Export\Admin\Meta_Boxes;

use SkyVerge\WooCommerce\CSV_Export\Automations\Automation;
use SkyVerge\WooCommerce\CSV_Export\Automations\Automation_Factory;
use SkyVerge\WooCommerce\CSV_Export\Taxonomies_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * 'Exported By' Meta Box Class.
 *
 * @since 5.0.0
 */
class Exported_By {


	/** @var bool tracks whether the JS has been rendered to this screen already or not */
	protected static $js_rendered = false;


	/**
	 * Renders the Meta Box for Orders.
	 *
	 * @since 5.0.0
	 *
	 * @param \WP_Post $post the post object
	 */
	public static function render_order( $post ) {
		global $theorder;

		if ( ! is_object( $theorder ) ) {
			$theorder = wc_get_order( $post->ID );
		}

		$order = $theorder;

		if ( $order instanceof \WC_Order ) {

			$automations            = Automation_Factory::get_automations_by_export_type( \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS );
			$checked_automation_ids = Taxonomies_Handler::get_exported_automation_ids_for_order( $order->get_id() );
			$global_checked         = Taxonomies_Handler::is_order_exported_globally( $order->get_id() );

			self::render( $automations, $checked_automation_ids, $global_checked, 'orders', __( 'order', 'woocommerce-customer-order-csv-export' ) );
		}
	}


	/**
	 * Renders the Meta Box for a customer in an order context.
	 *
	 * @since 5.0.0
	 *
	 * @param \WP_Post $post the post object
	 */
	public static function render_order_customer( $post ) {
		global $theorder;

		if ( ! is_object( $theorder ) ) {
			$theorder = wc_get_order( $post->ID );
		}

		$order = $theorder;

		if ( $order instanceof \WC_Order ) {

			$customer_id            = $order->get_customer_id();
			$automations            = Automation_Factory::get_automations_by_export_type( \WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS );
			$checked_automation_ids = 0 === $customer_id ? Taxonomies_Handler::get_exported_automation_ids_for_guest_customer( $order->get_id() ) : Taxonomies_Handler::get_exported_automation_ids_for_customer( $customer_id );
			$global_checked         = 0 === $customer_id ? Taxonomies_Handler::is_guest_customer_exported_globally( $order->get_id() ) : Taxonomies_Handler::is_customer_exported_globally( $customer_id );

			self::render( $automations, $checked_automation_ids, $global_checked, 'customers', __( 'customer', 'woocommerce-customer-order-csv-export' ) );
		}
	}


	/**
	 * Renders the Meta Box for customers in a user screen context.
	 *
	 * @since 5.0.0
	 *
	 * @param \WP_User $user the user object
	 */
	public static function render_user( $user ) {

		if ( $user instanceof \WP_User ) {

			$automations            = Automation_Factory::get_automations_by_export_type( \WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS );
			$checked_automation_ids = Taxonomies_Handler::get_exported_automation_ids_for_customer( $user->ID );
			$global_checked         = Taxonomies_Handler::is_customer_exported_globally( $user->ID );

			self::render( $automations, $checked_automation_ids, $global_checked, 'customers', __( 'customer', 'woocommerce-customer-order-csv-export' ) );
		}
	}


	/**
	 * Outputs the contents of this Meta Box.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation[] $automations array of automations
	 * @param string[] $checked_automation_ids array of checked automation IDs
	 * @param bool $global_checked whether global is checked or not
	 * @param string $type the export_type string
	 * @param string $singular singular string representation of the export type
	 */
	public static function render( $automations, $checked_automation_ids, $global_checked, $type, $singular ) {

		add_action( 'admin_print_footer_scripts', self::class . '::render_js' );
		?>

		<div class="wc_customer_order_exported_by exported-by-meta-box">
			<div class="exported-by__section exported-by--instructions">
				<?php /* translators: %1$s - singular export type */ ?>
				<p><i><?php printf( esc_html__( 'Automated exports that are checked will not contain this %1$s in future exports.', 'woocommerce-customer-order-csv-export' ), $singular ); ?></i></p>
			</div>
			<input type="hidden"
			       name="<?php echo esc_attr( "wc_customer_order_exported_by[{$type}][]" ); ?>"
			       value=""
			/>
			<div class="exported-by__section exported-by--global">
				<label>
					<input type="checkbox"
					       name="<?php echo esc_attr( "wc_customer_order_exported_by[{$type}][]" ); ?>"
					       value="global"
					       <?php checked( $global_checked ); ?>
					> <?php printf(
							/* translators: Placeholder: %s - export type, such as order or customer */
							esc_html__( 'All automated %s exports', 'woocommerce-customer-order-csv-export' ),
							$singular
					) ; ?>
				</label>
			</div>
			<div class="exported-by__section exported-by--automations">
				<ul class="categorychecklist">
					<?php foreach ( $automations as $automation ) : ?>

						<li id="<?php echo esc_attr( "automation_{$automation->get_id()}" ); ?>" >
							<label>
								<input type="checkbox"
								       name="<?php echo esc_attr( "wc_customer_order_exported_by[{$type}][]" ); ?>"
								       value="<?php echo esc_attr( $automation->get_id() ); ?>"
								       <?php checked( in_array( $automation->get_id(), $checked_automation_ids, true ) ); ?>
								> <?php echo esc_html( $automation->get_name() ); ?>
							</label>
						</li>

					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<?php
	}


	/**
	 * Outputs the necessary JS to the screen.
	 *
	 * @since 5.0.0
	 */
	public static function render_js() {

		if ( self::$js_rendered ) {
			return;
		}

		?>
		<script type="text/javascript">
			jQuery( function( $ ) {
				$( document.body ).on( 'change', '.exported-by-meta-box .exported-by--global input', function() {
					if ( $(this).is( ':checked' ) ) {
					    $( this ).closest( '.exported-by-meta-box' ).find( '.exported-by--automations' ).slideUp(150);
					} else {
                        $( this ).closest( '.exported-by-meta-box' ).find( '.exported-by--automations' ).slideDown(150);
					}
				} );

				$( '.exported-by-meta-box .exported-by--global input' ).change();
			} );
		</script>
		<?php

		self::$js_rendered = true;
	}


	/**
	 * Applies the changes made in the meta box to a particular order.
	 *
	 * @since 5.0.0
	 *
	 * @param int $order_id the order ID
	 */
	public static function save_order( $order_id ) {

		if ( ! current_user_can( 'manage_woocommerce_csv_exports' ) ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( $order instanceof \WC_Order ) {

			$exported_by = isset( $_POST['wc_customer_order_exported_by'] ) ? $_POST['wc_customer_order_exported_by'] : [];

			if ( isset( $exported_by['orders'] ) && is_array( $exported_by['orders'] ) ) {

				self::update_exported_by( $order_id, $exported_by['orders'], \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS, Taxonomies_Handler::TAXONOMY_NAME_ORDERS );
			}

			if ( isset( $exported_by['customers'] ) && is_array( $exported_by['customers'] ) ) {

				$customer_id = $order->get_customer_id();

				if ( 0 === $customer_id ) {

					self::update_exported_by( $order_id, $exported_by['customers'], \WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS, Taxonomies_Handler::TAXONOMY_NAME_GUEST_CUSTOMER );

				} else {

					self::update_exported_by( $customer_id, $exported_by['customers'], \WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS, Taxonomies_Handler::TAXONOMY_NAME_USER_CUSTOMER );
				}
			}
		}
	}


	/**
	 * Applies the changes made in the settings field to a particular customer.
	 *
	 * @since 5.0.0
	 *
	 * @param int $user_id the user ID
	 */
	public static function save_customer( $user_id ) {

		if ( ! current_user_can( 'manage_woocommerce_csv_exports' ) ) {
			return;
		}

		if ( get_user_by( 'id', $user_id ) ) {

			$exported_by = isset( $_POST['wc_customer_order_exported_by']['customers'] ) ? $_POST['wc_customer_order_exported_by']['customers'] : false;

			if ( is_array( $exported_by ) ) {

				self::update_exported_by( $user_id, $exported_by, \WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS, Taxonomies_Handler::TAXONOMY_NAME_USER_CUSTOMER );
			}
		}
	}


	/**
	 * Updates the exported-by terms for the given object ID, export type and taxonomy.
	 *
	 * Note: This overwrites any previous terms for the given object ID, so proceed with caution...
	 *
	 * @since 5.0.0
	 *
	 * @param int $object_id the user ID or order ID
	 * @param string[] $exported_by the array of automation IDs to mark as exported-by (or 'global')
	 * @param string $export_type the export type to validate automation IDs against
	 * @param string $taxonomy the taxonomy slug to update
	 */
	protected static function update_exported_by( $object_id, $exported_by, $export_type, $taxonomy ) {

		$automations = Automation_Factory::get_automations_by_export_type( $export_type );
		$term_slugs  = [];

		foreach ( $exported_by as $raw_value ) {

			if ( 'global' === $raw_value || array_key_exists( $raw_value, $automations ) ) {

				$term_slugs[] = 'global' === $raw_value ? $raw_value : Taxonomies_Handler::TERM_PREFIX . $raw_value;
			}
		}

		wp_set_object_terms( $object_id, $term_slugs, $taxonomy );
	}


}
