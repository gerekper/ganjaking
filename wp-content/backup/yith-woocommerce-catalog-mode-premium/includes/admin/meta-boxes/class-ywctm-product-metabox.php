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

if ( ! class_exists( 'YWCTM_Product_Metabox' ) ) {

	/**
	 * Shows Meta Box in order's details page
	 *
	 * @class   YWCTM_Product_Metabox
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWCTM_Product_Metabox {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'init' ) );
		}

		/**
		 * Init class
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function init() {

			if ( ywctm_is_multivendor_active() && ! ywctm_is_multivendor_integration_active() && '' !== ywctm_get_vendor_id( true ) ) {
				return;
			}

			add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
			add_action( 'woocommerce_admin_process_product_object', array( $this, 'save' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 20 );

		}

		/**
		 * Add scripts and styles
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function admin_scripts() {

			$screen = null;

			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
			}

			if ( ! $screen || 'product' !== $screen->post_type ) {
				return;
			}

			wp_enqueue_style( 'yith-plugin-fw-fields' );
			wp_enqueue_style( 'ywctm-admin-premium' );
			wp_enqueue_script( 'yith-plugin-fw-fields' );
			wp_enqueue_script( 'ywctm-admin-premium' );

		}

		/**
		 * Add a metabox on product page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_metabox() {
			add_meta_box( 'ywctm-product-metabox', esc_html__( 'Catalog Mode Options', 'yith-woocommerce-catalog-mode' ), array( $this, 'output' ), 'product', 'normal', 'high' );
		}

		/**
		 * The function to be called to output the meta box in product details page.
		 *
		 * @param   $post WP_Post
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function output( $post ) {

			$product       = wc_get_product( $post );
			$item          = $product->get_meta( '_ywctm_exclusion_settings' . ywctm_get_vendor_id() );
			$has_exclusion = 'yes';

			if ( ! $item ) {
				$atc_global         = get_option( 'ywctm_hide_add_to_cart_settings' . ywctm_get_vendor_id() );
				$button_global      = get_option( 'ywctm_custom_button_settings' . ywctm_get_vendor_id() );
				$button_loop_global = get_option( 'ywctm_custom_button_settings_loop' . ywctm_get_vendor_id() );
				$price_global       = get_option( 'ywctm_hide_price_settings' . ywctm_get_vendor_id() );
				$label_global       = get_option( 'ywctm_custom_price_text_settings' . ywctm_get_vendor_id() );
				$has_exclusion      = 'no';

				$item = array(
					'enable_inquiry_form'         => 'yes',
					'enable_atc_custom_options'   => 'no',
					'atc_status'                  => $atc_global['action'],
					'custom_button'               => $button_global,
					'custom_button_loop'          => $button_loop_global,
					'enable_price_custom_options' => 'no',
					'price_status'                => $price_global['action'],
					'custom_price_text'           => $label_global,
				);
			}

			$fields  = array_merge(
				array(
					array(
						'id'    => 'ywctm_has_exclusion',
						'name'  => 'ywctm_has_exclusion',
						'type'  => 'onoff',
						'title' => esc_html__( 'Add to exclusion list', 'yith-woocommerce-catalog-mode' ),
						'value' => $has_exclusion,
					),
				),
				ywctm_get_exclusion_fields( $item )
			);
			$enabled = get_option( 'ywctm_inquiry_form_enabled' . ywctm_get_vendor_id(), 'hidden' );

			?>
			<div class="yith-plugin-ui yith-plugin-fw">
				<table class="form-table <?php echo( 'hidden' !== $enabled && ywctm_exists_inquiry_forms() ? '' : 'no-active-form' ); ?>">
					<tbody>
					<?php foreach ( $fields as $field ) : ?>
						<tr valign="top" class="yith-plugin-fw-panel-wc-row <?php echo $field['type']; ?> <?php echo $field['name']; ?>">
							<th scope="row" class="titledesc">
								<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
							</th>
							<td class="forminp forminp-<?php echo $field['type']; ?>">
								<?php yith_plugin_fw_get_field( $field, true ); ?>
								<?php if ( isset( $field['desc'] ) && '' !== $field['desc'] ) : ?>
									<span class="description"><?php echo $field['desc']; ?></span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php
		}

		/**
		 * Save Meta Box
		 *
		 * The function to be called to save the meta box options.
		 *
		 * @param   $product WC_Product
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function save( $product ) {

			if ( isset( $_POST['ywctm_has_exclusion'] ) ) {

				$exclusion_data = array(
					'enable_inquiry_form'         => isset( $_POST['ywctm_enable_inquiry_form'] ) ? 'yes' : 'no',
					'enable_atc_custom_options'   => isset( $_POST['ywctm_enable_atc_custom_options'] ) ? 'yes' : 'no',
					'atc_status'                  => $_POST['ywctm_atc_status'],
					'custom_button'               => $_POST['ywctm_custom_button'],
					'custom_button_url'           => $_POST['ywctm_custom_button_url'],
					'custom_button_loop'          => $_POST['ywctm_custom_button_loop'],
					'custom_button_loop_url'      => $_POST['ywctm_custom_button_loop_url'],
					'enable_price_custom_options' => isset( $_POST['ywctm_enable_price_custom_options'] ) ? 'yes' : 'no',
					'price_status'                => $_POST['ywctm_price_status'],
					'custom_price_text'           => $_POST['ywctm_custom_price_text'],
					'custom_price_text_url'       => $_POST['ywctm_custom_price_text_url'],
				);

				$product->add_meta_data( '_ywctm_exclusion_settings' . ywctm_get_vendor_id(), $exclusion_data, true );
			} else {
				$product->delete_meta_data( '_ywctm_exclusion_settings' . ywctm_get_vendor_id() );
			}
		}

	}

	new YWCTM_Product_Metabox();

}
