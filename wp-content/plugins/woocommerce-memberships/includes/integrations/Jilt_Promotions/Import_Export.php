<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Integrations\Jilt_Promotions;

use SkyVerge\WooCommerce\Jilt_Promotions\Handlers\Installation;
use SkyVerge\WooCommerce\Jilt_Promotions\Messages;
use SkyVerge\WooCommerce\Jilt_Promotions\Notices\Notice;
use SkyVerge\WooCommerce\Jilt_Promotions\Handlers\Prompt;

defined( 'ABSPATH' ) or exit;

/**
 * Jilt Promotions prompt for the Memberships Import/Export pages.
 *
 * @since 1.17.6
 */
class Import_Export extends Prompt {


	/** @var string the message identifier that should be triggered */
	private $import_export_message_id = 'memberships-import-export';

	/** @var string the screen identifier where the prompt should appear */
	private $import_export_screen_id = 'admin_page_wc_memberships_import_export';


	/**
	 * Adds action & filter hooks.
	 *
	 * @since 1.17.6
	 */
	protected function add_prompt_hooks() {

		if ( ! Messages::is_message_enabled( $this->import_export_message_id ) ) {
			add_action( "load-{$this->import_export_screen_id}", [ $this, 'maybe_enable_import_export_message' ] );
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_notices', [ $this, 'add_admin_notices' ] );
	}


	/**
	 * Enqueues the assets.
	 *
	 * @internal
	 *
	 * @since 1.1.0-dev.1
	 */
	public function enqueue_assets() {

		if ( Messages::is_message_enabled( $this->import_export_message_id ) ) {

			wp_enqueue_style( Installation::INSTALL_SCRIPT_HANDLE );
			wp_enqueue_script( Installation::INSTALL_SCRIPT_HANDLE );
		}
	}


	/**
	 * Adds admin notices to be shown.
	 *
	 * @internal
	 *
	 * @since 1.17.6
	 */
	public function add_admin_notices() {

		if ( Messages::is_message_enabled( $this->import_export_message_id ) ) {

			$notice = new Notice();

			$notice->set_message_id( $this->import_export_message_id );
			$notice->set_title( __( 'Engaged members are happy members.', 'woocommerce-memberships' ) );
			$notice->set_content( __( 'Keep in touch with your members using Jilt’s built-in Memberships integration to send welcome emails, renewal reminders, and newsletters.', 'woocommerce-memberships' ) );
			$notice->set_actions( [
				[
					'label' => __( 'Learn more', 'woocommerce-product-reviews-pro' ),
					'name'  => 'learn-more',
					'url'   => 'https://www.skyverge.com/go/contact-members',
					'type'  => Notice::ACTION_TYPE_LINK,
				],
				[
					'label'   => __( 'Message my members', 'woocommerce-memberships' ),
					'name'    => 'message-my-members',
					'primary' => true,
					'type'    => Notice::ACTION_TYPE_BUTTON,
				],
			] );

			$notice->render();
		}
	}


	/**
	 * Maybe enables a message to be shown on the import/export screens.
	 *
	 * @internal
	 *
	 * @since 1.17.6
	 */
	public function maybe_enable_import_export_message() {

		Messages::enable_message( $this->import_export_message_id );
	}


	/**
	 * Gets the connection redirect args to attribute the plugin installation to this prompt.
	 *
	 * @since 1.17.6
	 *
	 * @return array
	 */
	protected function get_connection_redirect_args() {

		$args = [];

		if ( $this->import_export_message_id === Installation::get_jilt_installed_from() ) {
			$args = [ 'utm_term' => $this->import_export_message_id ];
		}

		return $args;
	}


}
