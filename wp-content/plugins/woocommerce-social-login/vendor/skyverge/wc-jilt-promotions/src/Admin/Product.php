<?php
/**
 * Jilt for WooCommerce Promotions
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Jilt_Promotions\Admin;

use SkyVerge\WooCommerce\Jilt_Promotions\Handlers\Installation;
use SkyVerge\WooCommerce\Jilt_Promotions\Handlers\Prompt;
use SkyVerge\WooCommerce\Jilt_Promotions\Messages;
use SkyVerge\WooCommerce\Jilt_Promotions\Notices\Notice;

defined( 'ABSPATH' ) or exit;

/**
 * Handles the notices shown when new product is created and when sale price changes
 *
 * @since 1.1.0
 */
class Product extends Prompt {


	/**
	 * @var string
	 */
	private $new_product_notice_message_id = 'new-product-notice';


	/**
	 * @var string
	 */
	private $product_sale_notice_message_id = 'product-sale-notice';

	/**
	 * @var string
	 */
	const META_KEY_NEW_PRODUCT_FLAG = '_sv_wc_jilt_is_new_product';


	/**
	 * {@inheritDoc}
	 *
	 * @since 1.1.0
	 */
	protected function add_prompt_hooks() {

		if ( ! Messages::is_message_enabled( $this->new_product_notice_message_id ) ) {

			add_action( 'wp_insert_post', [ $this, 'maybe_flag_new_product' ], 10, 3 );

			add_action( 'transition_post_status', [ $this, 'maybe_enable_new_product_notice' ], 10, 3 );

		}

		if ( ! Messages::is_message_enabled( $this->product_sale_notice_message_id ) ) {

			add_action( 'woocommerce_product_bulk_and_quick_edit', [ $this, 'add_enable_product_sale_notice_hooks' ], 5 );

			add_action( 'woocommerce_admin_process_product_object', [ $this, 'maybe_enable_product_sale_notice' ] );

		}

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_notices', [ $this, 'add_admin_notices' ] );

	}


	/**
	 * Adds action to maybe enable product sale message
	 *
	 * @since 1.1.0
	 */
	public function add_enable_product_sale_notice_hooks() {

		add_action( 'woocommerce_before_product_object_save', [ $this, 'maybe_enable_product_sale_notice' ] );

	}


	/**
	 * Enable new product message if product flagged as new and status changed to publish
	 *
	 * @since 1.1.0
	 *
	 * @param string $new_status New post status.
	 * @param string $old_status Old post status.
	 * @param \WP_Post $post Post object.
	 */
	public function maybe_enable_new_product_notice( $new_status, $old_status, $post ) {

		// bail if post/product isn't flagged as new product
		if ( 'yes' !== get_post_meta( $post->ID, self::META_KEY_NEW_PRODUCT_FLAG, true ) ) {
			return;
		}

		// bail if the new status isn't publish
		if ( 'publish' !== $new_status ) {
			return;
		}

		// enable the new product message
		Messages::enable_message( $this->new_product_notice_message_id );

		// clean up after
		delete_post_meta( $post->ID, self::META_KEY_NEW_PRODUCT_FLAG );

	}


	/**
	 * Enabled product sale message if sale price changes
	 *
	 * @since 1.1.0
	 *
	 * @param \WC_Product $product
	 */
	public function maybe_enable_product_sale_notice( $product ) {

		$changes = $product->get_changes();

		if ( ! empty( $changes['sale_price'] ) ) {
			Messages::enable_message( $this->product_sale_notice_message_id );
		}

	}


	/**
	 * Enqueues the assets.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 */
	public function enqueue_assets() {

		if ( Messages::is_message_enabled( $this->new_product_notice_message_id ) || Messages::is_message_enabled( $this->product_sale_notice_message_id ) ) {

			wp_enqueue_style( Installation::INSTALL_SCRIPT_HANDLE );
			wp_enqueue_script( Installation::INSTALL_SCRIPT_HANDLE );
		}
	}


	/**
	 * Renders a Notice object if new product and/or product sale message is enabled.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 */
	public function add_admin_notices() {

		if ( Messages::is_message_enabled( $this->new_product_notice_message_id ) ) {

			$new_product_notice = new Notice();
			$new_product_notice->set_message_id( $this->new_product_notice_message_id );
			$new_product_notice->set_actions( [
				[
					'label' => __( 'Learn more', 'sv-wc-jilt-promotions' ),
					'name'  => 'start-promoting-my-products-learn-more',
					'url'   => 'https://www.skyverge.com/go/promote-products',
					'type'  => Notice::ACTION_TYPE_LINK,
				],
				[
					'label'   => __( 'Start promoting my products', 'sv-wc-jilt-promotions' ),
					'name'    => 'start-promoting-my-products-cta',
					'primary' => true,
					'type'    => Notice::ACTION_TYPE_BUTTON,
				],
			] );
			$new_product_notice->set_title( __( 'Let customers know about your new products!', 'sv-wc-jilt-promotions' ) );
			$new_product_notice->set_content( __( 'With Jilt, you can send broadcast emails to all customers to let them know about the great new products available in your store.', 'sv-wc-jilt-promotions' ) );

			$new_product_notice->render();

		}

		if ( Messages::is_message_enabled( $this->product_sale_notice_message_id ) ) {

			$product_sale_notice = new Notice();
			$product_sale_notice->set_message_id( $this->product_sale_notice_message_id );
			$product_sale_notice->set_actions( [
				[
					'label' => __( 'Learn more', 'sv-wc-jilt-promotions' ),
					'name'  => 'broadcast-my-sale-learn-more',
					'url'   => 'https://www.skyverge.com/go/promote-sale',
					'type'  => Notice::ACTION_TYPE_LINK,
				],
				[
					'label'   => __( 'Broadcast my sale', 'sv-wc-jilt-promotions' ),
					'name'    => 'broadcast-my-sale-cta',
					'primary' => true,
					'type'    => Notice::ACTION_TYPE_BUTTON,
				],
			] );
			$product_sale_notice->set_title( __( 'Share your sale!', 'sv-wc-jilt-promotions' ) );
			$product_sale_notice->set_content( __( 'Jilt helps you communicate important events like product sales with your customers so they stay informed and interested in your store.', 'sv-wc-jilt-promotions' ) );

			$product_sale_notice->render();

		}

	}


	/**
	 * Flag product as new to look for when status changes
	 *
	 * @since 1.1.0
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param bool $is_update
	 */
	public function maybe_flag_new_product( $post_id, $post, $is_update ) {

		if ( $is_update ) {
			return;
		}

		if ( 'product' === get_post_type( $post ) ) {
			update_post_meta( $post_id, self::META_KEY_NEW_PRODUCT_FLAG, 'yes' );
		}
	}


	/**
	 * Gets the connection redirect args to attribute the plugin installation to this prompt.
	 *
	 * @see Prompt::add_connection_redirect_args()
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	protected function get_connection_redirect_args() {

		$args = [];

		$jilt_installed_from = Installation::get_jilt_installed_from();

		if ( $this->new_product_notice_message_id === $jilt_installed_from ) {
			$args = [ 'utm_term' => $this->new_product_notice_message_id ];
		}

		if ( $this->product_sale_notice_message_id === $jilt_installed_from ) {
			$args = [ 'utm_term' => $this->product_sale_notice_message_id ];
		}

		return $args;
	}


}
