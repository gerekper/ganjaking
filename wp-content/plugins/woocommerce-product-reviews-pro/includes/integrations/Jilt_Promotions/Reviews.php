<?php
/**
 * WooCommerce Product Reviews Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Product_Reviews_Pro\Integrations\Jilt_Promotions;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Jilt_Promotions\Handlers\Installation;
use SkyVerge\WooCommerce\Jilt_Promotions\Handlers\Prompt;
use SkyVerge\WooCommerce\Jilt_Promotions\Messages;
use SkyVerge\WooCommerce\Jilt_Promotions\Notices\Notice;

/**
 * WooCommerce Product Vendors integration.
 *
 * @since 1.16.0
 */
final class Reviews extends Prompt {


	/** @var string the ID of the message shown in the Reviews page */
	private $reviews_message_id = 'product-reviews-pro-reviews';

	/** @var string the screen ID for the Reviews page */
	private $reviews_screen_id = 'woocommerce_page_reviews';


	/**
	 * Adds the necessary action & filter hooks.
	 *
	 * @since 1.16.0
	 */
	protected function add_prompt_hooks() {

		if ( ! Messages::is_message_enabled( $this->reviews_message_id ) ) {
			add_action( "load-{$this->reviews_screen_id}", [ $this, 'maybe_enable_reviews_message' ] );
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		add_action( 'admin_notices', [ $this, 'add_admin_notices' ] );
	}


	/**
	 * Gets the connection redirect args to attribute the plugin installation to this prompt.
	 *
	 * @since 1.16.0
	 *
	 * @return array
	 */
	protected function get_connection_redirect_args() {

		$args = [];

		if ( $this->reviews_message_id === Installation::get_jilt_installed_from() ) {
			$args = [ 'utm_term' => $this->reviews_message_id ];
		}

		return $args;
	}


	/**
	 * Enables the Reviews message when the merchant visits the WooCommerce > Reviews page.
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 */
	public function maybe_enable_reviews_message() {

		Messages::enable_message( $this->reviews_message_id );
	}


	/**
	 * Enqueues the assets.
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 */
	public function enqueue_assets() {

		if ( Messages::is_message_enabled( $this->reviews_message_id ) ) {

			wp_enqueue_style( Installation::INSTALL_SCRIPT_HANDLE );
			wp_enqueue_script( Installation::INSTALL_SCRIPT_HANDLE );
		}
	}


	/**
	 * Outputs the admin notice for the Reviews message.
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 */
	public function add_admin_notices() {

		if ( Messages::is_message_enabled( $this->reviews_message_id ) ) {

			$notice = new Notice();

			$notice->set_message_id( $this->reviews_message_id );
			$notice->set_title( __( 'More reviews mean more sales.', 'woocommerce-product-reviews-pro' ) );
			$notice->set_content( __( "With Jilt, it's easy to request product reviews from your customers and offer discounts as a reward.", 'woocommerce-product-reviews-pro' ) );
			$notice->set_actions( [
				[
					'name'  => 'learn-more',
					'label' => __( 'Learn more', 'woocommerce-product-reviews-pro' ),
					'url'   => 'https://www.skyverge.com/go/get-reviews',
					'type'  => Notice::ACTION_TYPE_LINK,
				],
				[
					'name'    => 'start-getting-more-reviews',
					'label'   => __( 'Start getting more reviews', 'woocommerce-product-reviews-pro' ),
					'primary' => true,
					'type'    => Notice::ACTION_TYPE_BUTTON,
				],
			] );

			$notice->render();
		}
	}


}
