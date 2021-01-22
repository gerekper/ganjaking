<?php
/**
 * WooCommerce Checkout Add-Ons
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Admin;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Plugin;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_Factory;

defined( 'ABSPATH' ) or exit;

/**
 * Add-On List Admin Screen Class
 *
 * Handles interaction on the add-on list screen in the admin.
 *
 * @since 2.0.0
 */
class Add_On_List_Screen extends Admin_Screen {


	/**
	 * Enqueues necessary scripts for this screen.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function enqueue_scripts() {

		$admin_js_url = wc_checkout_add_ons()->get_plugin_url() . '/assets/js/admin/';

		wp_enqueue_script(
			'wc-checkout-add-ons-add-on-list',
			$admin_js_url . 'wc-checkout-add-ons-add-on-list.min.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			Plugin::VERSION
		);
	}


	/**
	 * Renders the checkout add-ons list screen.
	 *
	 * @since 2.0.0
	 */
	public function render() {

		$new_add_on_url = $this->admin->get_new_add_on_screen_url();
		$list_table     = new Add_Ons_List_Table();
		$do_action      = $list_table->current_action();

		if ( $do_action ) {

			check_admin_referer( 'bulk-add-ons' );

			switch ( $do_action ) {

				case 'bulk-edit':
					$this->bulk_edit();
					exit;
				break;

				case 'bulk-enable':
					$this->bulk_set_enabled( true );
					exit;
				break;

				case 'bulk-disable':
					$this->bulk_set_enabled( false );
					exit;
				break;

				case 'bulk-delete':
					$this->bulk_delete();
					exit;
				break;
			}

		} elseif ( ! empty( $_GET['_wp_http_referer'] ) ) {

			wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
			exit;
		}

		include __DIR__ . '/Views/html-screen-list-add-ons.php';
	}


	/**
	 * Gets IDs passed into the URL for bulk add-on actions.
	 *
	 * @since 2.0.0
	 *
	 * @return string[]
	 */
	protected function get_bulk_add_on_ids() {

		return isset( $_GET['bulk-action'] ) && is_array( $_GET['bulk-action'] ) ? array_map( 'sanitize_text_field', $_GET['bulk-action'] ) : array();
	}


	/**
	 * Performs a bulk action on add-ons.
	 *
	 * @since 2.0.0
	 *
	 * @param string $action_name_present the name of the action in present tense, used in messages
	 * @param string $action_name_past the name of the action in past tense, used in messages
	 * @param callable $action method to call on the action - must accept an Add_On and return a bool indicating success
	 */
	protected function do_bulk_action( $action_name_present, $action_name_past, callable $action ) {

		$count         = 0;
		$error_notices = array();

		try {

			$bulk_ids = $this->get_bulk_add_on_ids();

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'You do not have permission to perform this action.', 'woocommerce-checkout-add-ons' ) );
			}

			if ( ! is_array( $bulk_ids ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid Add-On Selection' ) );
			}

			foreach ( $bulk_ids as $bulk_id ) {

				if ( $add_on = Add_On_Factory::get_add_on( $bulk_id ) ) {

					if ( is_callable( $action ) && $action( $add_on ) ) {

						$count++;

					} else {

						$error_notices[] = sprintf(
							/* translators: Placeholders: %1$s - action name present tense, %2$s - add-on ID */
							esc_html__( 'Unable to %1$s add-on with ID: %2$s', 'woocommerce-checkout-add-ons' ),
							$action_name_present,
							$bulk_id
						);
					}
				} else {

					/* translators: Placeholders: %1$s - add-on ID */
					$error_notices[] = sprintf( esc_html__( 'Unable to find add-on ID: %1$s', 'woocommerce-checkout-add-ons' ), $bulk_id );
				}
			}
		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			$error_notices[] = $exception->getMessage();
		}

		foreach( $error_notices as $error_notice ) {
			wc_checkout_add_ons()->get_message_handler()->add_error( $error_notice );
		}

		if ( 0 < $count ) {

			wc_checkout_add_ons()->get_message_handler()->add_message( sprintf(
				/* translators: Placeholders: %1$s - action name past tense, %2$s - number of add-ons affected */
				esc_html( _n( 'Successfully %1$s %2$s Add-On.', 'Successfully %1$s %2$s Add-Ons.', $count, 'woocommerce-checkout-add-ons' ) ),
				$action_name_past,
				$count
			) );
		}

		wp_safe_redirect( $this->admin->get_list_add_ons_screen_url() );
	}


	/**
	 * Handles bulk enable/disable of add-ons.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $enabled whether to enable or disable add-ons
	 */
	protected function bulk_set_enabled( $enabled ) {

		$enabled = (bool) $enabled;
		$present = $enabled ? _x( 'enable', 'bulk action present tense', 'woocommerce-checkout-add-ons' ) : _x( 'disable', 'bulk action present tense', 'woocommerce-checkout-add-ons' );
		$past    = $enabled ? _x( 'enabled', 'bulk action past tense', 'woocommerce-checkout-add-ons' ) : _x( 'disabled', 'bulk action past tense', 'woocommerce-checkout-add-ons' );

		$this->do_bulk_action(
			$present,
			$past,
			function( Add_On $add_on ) use ( $enabled ) {

				$add_on->set_enabled( $enabled );

				return '' !== $add_on->save();
			}
		);
	}


	/**
	 * Handles bulk deletion of add-ons.
	 *
	 * @since 2.0.0
	 */
	protected function bulk_delete() {

		$this->do_bulk_action(
			_x( 'delete', 'bulk action present tense', 'woocommerce-checkout-add-ons' ),
			_x( 'deleted', 'bulk action past tense', 'woocommerce-checkout-add-ons' ),
			function( Add_On $add_on ) {
				return $add_on->delete();
			}
		);
	}


	/**
	 * Handles bulk editing of add-ons.
	 *
	 * @since 2.0.0
	 */
	protected function bulk_edit() {

		$is_taxable = isset( $_GET['is_taxable'] ) ? 'yes' === $_GET['is_taxable'] : null;
		$tax_class  = isset( $_GET['tax_class'] ) && in_array( $_GET['tax_class'], \WC_Tax::get_tax_class_slugs(), true ) ? $_GET['tax_class'] : null;

		$this->do_bulk_action(
			_x( 'update', 'bulk action present tense', 'woocommerce-checkout-add-ons' ),
			_x( 'updated', 'bulk action past tense', 'woocommerce-checkout-add-ons' ),
			function ( Add_On $add_on ) use ( $is_taxable, $tax_class ) {

				if ( null !== $is_taxable ) {
					$add_on->set_is_taxable( $is_taxable );

					if ( $is_taxable && null !== $tax_class ) {
						$add_on->set_tax_class( $tax_class );
					}
				}

				return '' !== $add_on->save();
			}
		);
	}


}
