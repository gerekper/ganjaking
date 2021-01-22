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
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_Factory;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Plugin;

defined( 'ABSPATH' ) or exit;

/**
 * Add-On Admin Screen Class
 *
 * Handles interaction on a single add-on screen in the admin.
 *
 * @since 2.0.0
 */
class Add_On_Screen extends Admin_Screen {


	/** @var Add_On|bool the current add-on being edited, if present */
	protected $add_on;


	/**
	 * Constructs this screen class.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		parent::__construct();

		$this->add_on = $this->admin->get_admin_screen_add_on();

		$this->add_meta_boxes();
	}


	/**
	 * Adds meta boxes to the admin screen.
	 *
	 * @since 2.0.0
	 */
	public function add_meta_boxes() {

		add_meta_box(
			Plugin::PLUGIN_PREFIX . 'add_on_data',
			_x( 'Add-on data', 'meta box title', 'woocommerce-checkout-add-ons' ),
			'SkyVerge\WooCommerce\Checkout_Add_Ons\Admin\Meta_Boxes\Add_On_Data::create_and_render',
			$this->admin->get_page_id(),
			'normal',
			'high'
		);

		add_meta_box(
			Plugin::PLUGIN_PREFIX . 'add_on_publish',
			_x( 'Publish', 'meta box title', 'woocommerce-checkout-add-ons' ),
			'SkyVerge\WooCommerce\Checkout_Add_Ons\Admin\Meta_Boxes\Add_On_Publish::create_and_render',
			$this->admin->get_page_id(),
			'side',
			'high'
		);
	}


	/**
	 * Renders the admin page.
	 *
	 * @since 2.0.0
	 */
	public function render() {

		$is_delete    = $this->admin->is_delete_add_on_screen();
		$is_new       = $this->admin->is_new_add_on_screen();
		$is_edit      = $this->admin->is_edit_add_on_screen();
		$is_duplicate = $this->admin->is_duplicate_add_on_screen();

		if ( $is_delete ) {
			$this->delete_add_on();
		}

		if ( $is_new || $is_edit || $is_duplicate ) {

			if ( ! empty( $_POST ) ) {
				$this->save_add_on();
			}

			if ( $is_new ) {

				$this->render_new_add_on_screen();

			} elseif ( $is_edit ) {

				$this->render_edit_add_on_screen();

			} elseif ( $is_duplicate ) {

				$this->duplicate_add_on();
			}
		}
	}


	/**
	 * Renders the new add-on screen.
	 *
	 * @since 2.0.0
	 */
	protected function render_new_add_on_screen() {

		$add_on                = null;
		$screen_id             = get_current_screen()->id;
		$screen_title          = _x( 'Add New Checkout Add-On', 'screen title', 'woocommerce-checkout-add-ons' );
		$new_add_on_screen_url = $this->admin->get_new_add_on_screen_url();

		include __DIR__ . '/Views/html-screen-add-edit-add-on.php';
	}


	/**
	 * Renders the edit add-on screen.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On|false $add_on the add-on to edit
	 */
	protected function render_edit_add_on_screen( $add_on = false ) {

		$add_on = $add_on ? $add_on : $this->admin->get_admin_screen_add_on();

		if ( $add_on instanceof Add_On ) {

			$screen_id             = get_current_screen()->id;
			$screen_title          = _x( 'Edit Checkout Add-On', 'screen title', 'woocommerce-checkout-add-ons' );
			$new_add_on_screen_url = $this->admin->get_new_add_on_screen_url();

			include __DIR__ . '/Views/html-screen-add-edit-add-on.php';
		}
	}


	/**
	 * Deletes an add-on.
	 *
	 * @since 2.0.0
	 */
	protected function delete_add_on() {

		$add_on = $this->admin->get_admin_screen_add_on();
		$notice = __( 'Add-On Deleted.', 'woocommerce-checkout-add-ons' );

		try {

			// check permissions
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'You do not have permission to perform this action.', 'woocommerce-checkout-add-ons' ) );
			}

			if ( ! $add_on || '' === $add_on->get_id() ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid Add-On.', 'woocommerce-checkout-add-ons' ) );
			}

			check_admin_referer( 'delete_checkout_add_on_' . $add_on->get_id(), 'security' );

			$success = $add_on->delete();

			if ( ! $success ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'Unable to delete this add-on.', 'woocommerce-checkout-add-ons' ) );
			}

		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			$notice  = $exception->getMessage();
			$success = false;
		}

		if ( $success ) {
			wc_checkout_add_ons()->get_message_handler()->add_message( $notice );
		} else {
			wc_checkout_add_ons()->get_message_handler()->add_error( $notice );
		}

		wp_safe_redirect( $this->admin->get_list_add_ons_screen_url() );
	}


	/**
	 * Saves the submitted add-on data.
	 *
	 * @since 2.0.0
	 */
	protected function save_add_on() {

		// check nonce
		check_admin_referer( 'wc_checkout_add_ons_add_on_data' );

		$notice    = __( 'Add-On Saved.', 'woocommerce-checkout-add-ons' );
		$success   = true;
		$add_on_id = '';

		try {

			// check permissions
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'You do not have sufficient permissions to perform this action.', 'woocommerce-checkout-add-ons' ) );
			}

			$add_on_type = isset( $_POST['type'] ) ? $_POST['type'] : null;
			$add_on      = Add_On_Factory::create_add_on( $add_on_type );

			// check for valid add-on type
			if ( ! $add_on ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid Add-On Type', 'woocommerce-checkout-add-ons' ) );
			}

			if ( $this->admin->is_edit_add_on_screen() ) {

				$add_on->set_id( sanitize_text_field( $_GET['add_on'] ) );
			}

			// validate add-on data
			$add_on->set_props( $add_on->sanitize_data( $_POST ) );
			$add_on_id = $add_on->save();

		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			$notice  = $exception->getMessage();
			$success = false;
		}

		if ( $success ) {
			wc_checkout_add_ons()->get_message_handler()->add_message( $notice );
		} else {
			wc_checkout_add_ons()->get_message_handler()->add_error( $notice );
		}

		if ( '' !== $add_on_id ) {

			wp_safe_redirect( $this->admin->get_edit_add_on_screen_url( $add_on_id ) );
		}
	}


	/**
	 * Duplicates an add-on.
	 *
	 * @since 2.1.0
	 */
	protected function duplicate_add_on() {

		// get add_on from URL query string
		$add_on    = $this->admin->get_admin_screen_add_on();
		$notice    = __( 'Add-On Duplicated.', 'woocommerce-checkout-add-ons' );
		$success   = false;
		$add_on_id = '';

		try {
			// check nonce
			check_admin_referer( 'duplicate_checkout_add_on_' . $add_on->get_id(), 'security' );

			// check permissions
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'You do not have sufficient permissions to perform this action.', 'woocommerce-checkout-add-ons' ) );
			}

			if ( ! $add_on || '' === $add_on->get_id() ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'Invalid Add-On.', 'woocommerce-checkout-add-ons' ) );
			}

			// modify add-on and save with new ID
			$add_on->set_id( '' );
			$add_on->set_name( 'Copy of ' . $add_on->get_name() );
			$add_on->set_enabled( false );
			$add_on_id = $add_on->save();

			if ( '' !== $add_on_id ) {
				$success = true;
			}

		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			$notice  = $exception->getMessage();
			$success = false;
		}

		if ( $success ) {

			wc_checkout_add_ons()->get_message_handler()->add_message( $notice );
			wp_safe_redirect( $this->admin->get_edit_add_on_screen_url( $add_on_id ) );
			exit;

		} else {

			wc_checkout_add_ons()->get_message_handler()->add_error( $notice );
			wp_safe_redirect( $this->admin->get_list_add_ons_screen_url() );
			exit;
		}
	}


}
