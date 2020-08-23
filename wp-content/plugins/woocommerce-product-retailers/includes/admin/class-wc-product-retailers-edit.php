<?php
/**
 * WooCommerce Product Retailers
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Retailers to newer
 * versions in the future. If you wish to customize WooCommerce Product Retailers for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-retailers/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Product Retailers Admin Edit Screen.
 *
 * @since 1.0.0
 */
class WC_Product_Retailers_Edit {


	/**
	 * Initializes and sets up the retailer add/edit screen.
	 *
	 * @since 1.0.0
	 */
	public function  __construct() {

		add_action( 'admin_head', array( $this, 'highlight_retailers_menu' ) );

		add_filter( 'post_updated_messages', array( $this, 'retailers_updated_messages' ) );

		add_action( 'add_meta_boxes', array( $this, 'retailers_meta_boxes' ) );

		add_filter( 'enter_title_here', array( $this, 'enter_retailer_name_here' ), 1, 2 );

		add_action( 'save_post', array( $this, 'meta_boxes_save' ), 1, 2 );

		add_action( 'woocommerce_process_wc_product_retailer_meta', array( $this, 'process_retailer_meta' ), 10, 2 );

		// Disable autosave for the wc_product_retailer post type
		add_action( 'admin_footer', array( $this, 'disable_autosave' ) );
	}


	/**
	 * Highlights the correct top level admin menu item for the product retailers post type add screen.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function highlight_retailers_menu() {
		global $parent_file, $submenu_file, $post_type;

		if ( isset( $post_type ) && 'wc_product_retailer' === $post_type ) {

			$submenu_file = 'edit.php?post_type=' . $post_type;
			$parent_file  = 'woocommerce';
		}
	}


	/**
	 * Sets the product updated messages so they're specific to the product retailers.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $messages Array of messages
	 * @return array
	 */
	public function retailers_updated_messages( $messages ) {
		global $post;

		$messages['wc_product_retailer'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Retailer updated.', 'woocommerce-product-retailers' ),
			2 => __( 'Custom field updated.', 'woocommerce-product-retailers' ),
			3 => __( 'Custom field deleted.', 'woocommerce-product-retailers' ),
			4 => __( 'Retailer updated.', 'woocommerce-product-retailers'),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Retailer restored to revision from %s', 'woocommerce-product-retailers' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __( 'Retailer updated.', 'woocommerce-product-retailers' ),
			7 => __( 'Retailer saved.', 'woocommerce-product-retailers' ),
			8 => __( 'Retailer submitted.', 'woocommerce-product-retailers' ),
			/* translators: Placeholder: %s - Product Retailer post scheduled date for publishing */
			9 => sprintf( __( 'Retailer scheduled for: <strong>%s</strong>.', 'woocommerce-product-retailers' ),
				date_i18n( __( 'M j, Y @ G:i', 'woocommerce-product-retailers' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Retailer draft updated.', 'woocommerce-product-retailers'),
		);

		return $messages;
	}


	/**
	 * Sets a more appropriate placeholder text for the New Retailer title field.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $text "Enter Title Here" string
	 * @param \WP_Post $post post object
	 * @return string "Retailer Name" when the post type is wc_product_retailer
	 */
	public function enter_retailer_name_here( $text, $post ) {

		if ( 'wc_product_retailer' === $post->post_type ) {
			return esc_html__( 'Retailer Name', 'woocommerce-product-retailers' );
		}

		return $text;
	}


	/**
	 * Adds and removes meta boxes from the Retailer edit page.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function retailers_meta_boxes() {

		// Retailer Info box
		add_meta_box(
			'woocommerce-product-retailer-info',
			__( 'Retailer Info', 'woocommerce-product-retailers' ),
			array( $this, 'retailer_info_meta_box' ),
			'wc_product_retailer',
			'normal',
			'high'
		);

		// remove unnecessary meta boxes
		remove_meta_box( 'woothemes-settings', 'wc_product_retailer', 'normal' );
		remove_meta_box( 'commentstatusdiv',   'wc_product_retailer', 'normal' );
		remove_meta_box( 'slugdiv',            'wc_product_retailer', 'normal' );
	}


	/**
	 * Displays the Product Retailer info meta box.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Post $post the post object
	 */
	public function retailer_info_meta_box( $post ) {

		wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );

		?>
		<style type="text/css">
			#edit-slug-box,
			#misc-publishing-actions,
			#minor-publishing-actions {
				display: none !important;
			}
		</style>
		<div id="product_retailer_options" class="panel woocommerce_options_panel">
			<div class="options_group">
				<?php

				/* Automatically publish the post if the admin hits 'enter' in a form field */
				if ( 'auto-draft' === $post->post_status ) {
					echo '<input type="hidden" name="publish" value="Publish" />';
				}

				// URL
				woocommerce_wp_text_input( array(
					'id'          => '_product_retailer_default_url',
					'label'       => __( 'Default URL', 'woocommerce-product-retailers' ),
					'default'     => '',
					'description' => __( 'The default URL for the retailer, ie: http://www.example.com  This URL will be used unless overridden by a product.', 'woocommerce-product-retailers' ),
					'desc_tip'    => true,
				) );

				?>
			</div>
		</div>
		<?php
	}


	/**
	 * Runs when a post is saved and does an action which the write panel save scripts can hook into.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id post identifier
	 * @param \WP_Post $post post object
	 */
	public function meta_boxes_save( $post_id, $post ) {

		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		if ( ! isset( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( 'wc_product_retailer' !== $post->post_type ) {
			return;
		}

		do_action( 'woocommerce_process_wc_product_retailer_meta', $post_id, $post );
	}


	/**
	 * Processes and stores all product retailer data.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id the product retailer post id
	 * @param \WP_Post $post the product retailer post object
	 */
	public function process_retailer_meta( $post_id, $post ) {

		try {
			$retailer = new WC_Product_Retailers_Retailer( $post );
		} catch ( \Exception $e ) {
			return;
		}

		// URL
		$retailer->set_url( $_POST['_product_retailer_default_url'] ? $_POST['_product_retailer_default_url'] : '' );
		$retailer->persist();
	}


	/**
	 * Disables autosave for the wc_product_retailer post type
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function disable_autosave() {
		global $typenow;

		if ( 'wc_product_retailer' === $typenow ) {
			wp_dequeue_script( 'autosave' );
		}
	}


}
