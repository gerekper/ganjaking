<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWCTM_Meta_Box' ) ) {

	/**
	 * Shows Meta Box in order's details page
	 *
	 * @class   YWCTM_Meta_Box
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWCTM_Meta_Box {

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			if ( get_option( 'ywctm_hide_add_to_cart_single' ) == 'yes' || get_option( 'ywctm_hide_add_to_cart_loop' ) == 'yes' || get_option( 'ywctm_hide_price' ) == 'yes' ) {

				if ( defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM ) {

					if ( get_option( 'yith_wpv_vendors_enable_catalog_mode' ) != 'yes' ) {

						return;

					}

				}

				add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
				add_action( 'woocommerce_process_product_meta', array( $this, 'save' ) );

			}

		}

		/**
		 * Add a metabox on product page
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_metabox() {

			add_meta_box( 'ywctm-metabox', __( 'Catalog Mode Options', 'yith-woocommerce-catalog-mode' ), array( $this, 'output' ), 'product', 'normal', 'high' );

		}

		/**
		 * Output Meta Box
		 *
		 * The function to be called to output the meta box in product details page.
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function output() {

			$atc_field   = __( 'Show "Add to cart" button for this product anyway', 'yith-woocommerce-catalog-mode' );
			$price_field = __( 'Show price for this product anyway', 'yith-woocommerce-catalog-mode' );

			if ( get_option( 'ywctm_exclude_hide_add_to_cart_reverse' ) == 'yes' ) {
				$atc_field = __( 'Hide "Add to cart" button for this product anyway', 'yith-woocommerce-catalog-mode' );
			}

			if ( get_option( 'ywctm_exclude_hide_price_reverse' ) == 'yes' ) {
				$price_field = __( 'Hide price for this product anyway', 'yith-woocommerce-catalog-mode' );
			}

			$args = array(
				'add_to_cart' => array(
					'id'          => '_ywctm_exclude_catalog_mode',
					'label'       => __( '"Add to cart" button', 'yith-woocommerce-catalog-mode' ),
					'description' => $atc_field
				),
				'price'       => array(
					'id'          => '_ywctm_exclude_hide_price',
					'label'       => __( 'Product price', 'yith-woocommerce-catalog-mode' ),
					'description' => $price_field
				)
			);

			?>
            <div class="panel woocommerce_options_panel">
                <div class="options_group">
                    <p class="form-field">
                        <i>
							<?php _e( 'Note: if you hide the price, also "Add to cart" button will be automatically hidden.', 'yith-woocommerce-catalog-mode' ); ?>
                        </i>
                    </p>
					<?php woocommerce_wp_checkbox( $args['add_to_cart'] ); ?>
					<?php woocommerce_wp_checkbox( $args['price'] ); ?>
                </div>

				<?php if ( get_option( 'ywctm_custom_button' ) == 'yes' || get_option( 'ywctm_custom_button_loop' ) == 'yes' ) : ?>

                    <div class="options_group">
						<?php

						$button_args = array(
							'exclude'  => array(
								'id'          => '_ywctm_exclude_button',
								'label'       => __( 'Custom button exclusion', 'yith-woocommerce-catalog-mode' ),
								'description' => __( 'Exclude the product from showing the custom button', 'yith-woocommerce-catalog-mode' )
							),
							'enable'   => array(
								'id'          => '_ywctm_custom_url_enabled',
								'label'       => __( 'Custom button URL override', 'yith-woocommerce-catalog-mode' ),
								'description' => __( 'Override global URL of custom button', 'yith-woocommerce-catalog-mode' )
							),
							'text'     => array(
								'id'          => '_ywctm_button_text',
								'label'       => __( 'Custom button text', 'yith-woocommerce-catalog-mode' ),
								'description' => __( 'Specify the text of the button', 'yith-woocommerce-catalog-mode' ),
								'desc_tip'    => true
							),
							'protocol' => array(
								'id'          => '_ywctm_custom_url_protocol',
								'label'       => __( 'Custom button URL protocol type', 'yith-woocommerce-catalog-mode' ),
								'description' => __( 'Specify the type of the URL', 'yith-woocommerce-catalog-mode' ),
								'desc_tip'    => true,
								'options'     => array(
									'generic' => __( 'Generic URL', 'yith-woocommerce-catalog-mode' ),
									'mailto'  => __( 'E-mail address', 'yith-woocommerce-catalog-mode' ),
									'tel'     => __( 'Phone number', 'yith-woocommerce-catalog-mode' ),
									'skype'   => __( 'Skype contact', 'yith-woocommerce-catalog-mode' ),
								),
							),
							'link'     => array(
								'id'          => '_ywctm_custom_url_link',
								'label'       => __( 'Custom button URL link', 'yith-woocommerce-catalog-mode' ),
								'description' => __( 'Specify the URL', 'yith-woocommerce-catalog-mode' ),
								'desc_tip'    => true
							),
							'target'   => array(
								'id'          => '_ywctm_custom_url_link_target',
								'label'       => __( 'New tab', 'yith-woocommerce-catalog-mode' ),
								'description' => __( 'Open link in new tab (Only for Generic URL)', 'yith-woocommerce-catalog-mode' )
							),
						);

						?>
                        <div><?php woocommerce_wp_checkbox( $button_args['exclude'] ); ?></div>
                        <div><?php woocommerce_wp_checkbox( $button_args['enable'] ); ?></div>
                        <div><?php woocommerce_wp_text_input( $button_args['text'] ); ?></div>
                        <div><?php woocommerce_wp_select( $button_args['protocol'] ); ?></div>
                        <div><?php woocommerce_wp_text_input( $button_args['link'] ); ?></div>
                        <div><?php woocommerce_wp_checkbox( $button_args['target'] ); ?></div>

                    </div>

				<?php endif; ?>

            </div>
			<?php
		}

		/**
		 * Save Meta Box
		 *
		 * The function to be called to save the meta box options.
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function save() {

			global $post;

			$product = wc_get_product( $post->ID );

			$product->update_meta_data( '_ywctm_exclude_catalog_mode', isset( $_POST['_ywctm_exclude_catalog_mode'] ) ? 'yes' : 'no' );
			$product->update_meta_data( '_ywctm_exclude_hide_price', isset( $_POST['_ywctm_exclude_hide_price'] ) ? 'yes' : 'no' );

			if ( get_option( 'ywctm_custom_button' ) == 'yes' || get_option( 'ywctm_custom_button_loop' ) == 'yes' ) {

				$product->update_meta_data( '_ywctm_custom_url_enabled', isset( $_POST['_ywctm_custom_url_enabled'] ) ? 'yes' : 'no' );
				$product->update_meta_data( '_ywctm_exclude_button', isset( $_POST['_ywctm_exclude_button'] ) ? 'yes' : 'no' );
				$product->update_meta_data( '_ywctm_custom_url_protocol', $_POST['_ywctm_custom_url_protocol'] );
				$product->update_meta_data( '_ywctm_button_text', $_POST['_ywctm_button_text'] );
				$product->update_meta_data( '_ywctm_custom_url_link', $_POST['_ywctm_custom_url_link'] );
				$product->update_meta_data( '_ywctm_custom_url_link_target', isset( $_POST['_ywctm_custom_url_link_target'] ) ? 'yes' : 'no' );

			}

			$product->save();

		}

	}

	new YWCTM_Meta_Box();

}