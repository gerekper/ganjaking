<?php
/**
 * Storefront Powerpack Admin Class
 *
 * @package  Storefront_Powerpack
 * @author   WooThemes
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Admin' ) ) :

	/**
	 * The admin class
	 */
	class SP_Admin {

		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_filter( 'plugin_action_links_' . SP_PLUGIN_BASENAME, array( $this, 'plugin_links' ) );
			add_action( 'admin_notices', array( $this, 'activation_notice' ) );

			if ( is_admin() && class_exists( 'WooCommerce' ) ) {
				add_filter( 'woocommerce_product_data_tabs', array( $this, 'custom_product_data_tab' ) );
				add_action( 'woocommerce_product_data_panels', array( $this, 'custom_product_data_panel' ) );
				add_action( 'woocommerce_process_product_meta', array( $this, 'single_product_layout_override_admin_process' ) );

				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			}
		}

		/**
		 * Enqueue Admin Styles
		 *
		 * @return void
		 */
		public function enqueue_styles() {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}

			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';

			if ( 'product' === $screen_id ) {
				wp_enqueue_style( 'sp-product-admin', SP_PLUGIN_URL . 'assets/css/admin.css', '', '1.0.0' );
			}
		}

		/**
		 * Save Storefront Layout field
		 *
		 * @param int $post_id the post ID.
		 * @return void
		 */
		public function single_product_layout_override_admin_process( $post_id ) {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}

			$fields = array( '_sp_sf_product_layout', '_sp_sf_gallery_layout', '_sp_sf_product_tabs', '_sp_sf_product_related', '_sp_sf_product_description', '_sp_sf_product_meta' );
			foreach ( $fields as $field ) {
				if ( empty( $_POST[ $field ] ) ) {
					delete_post_meta( $post_id, $field );
				} else {
					update_post_meta( $post_id, $field, stripslashes( $_POST[ $field ] ) );
				}
			}
		}

		/**
		 * Storefront Layout field
		 *
		 * @return void
		 */
		public function custom_product_data_panel() {
			global $post;

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}

			?>
			<div id="storefront_data" class="panel woocommerce_options_panel">
				<div class="options_group sf_layout_group">
					<h3 style="margin:15px 0 20px 11px;font-size:14px;"><?php esc_html_e( 'Storefront Layout Options', 'storefront-powerpack' ); ?><img class="help_tip" data-tip="<?php echo sprintf( esc_attr__( 'Use these options to fine tune the appearance of this product, overriding the current global Customizer configuration located at %sAppearance > Customize > Product Details%s.', 'storefront-powerpack' ), '<strong>', '<strong>' ); ?>" src="<?php echo esc_url( WC()->plugin_url() ); ?>/assets/images/help.png" width="16" height="16" /></h3>
					<?php

					$product_layout = get_post_meta( $post->ID, '_sp_sf_product_layout', true );

					// Storefront sidebar layout.
					woocommerce_wp_select( array(
						'id' 			=> '_sp_sf_product_layout',
						'value' 		=> $product_layout,
						'label' 		=> __( 'Product Details', 'storefront-powerpack' ),
						'desc_tip' 		=> true,
						'description' 	=> sprintf( __( 'Overrides the Layout option under %sAppearance > Customize > Product Details%s.', 'storefront-powerpack' ), '<strong>', '<strong>' ),
						'options' 		=> array(
											''           	=> __( 'Override layout&hellip;', 'storefront-powerpack' ),
											'default'    	=> __( 'Default', 'storefront-powerpack' ),
											'full-width' 	=> __( 'Full Width', 'storefront-powerpack' ),
										),
					) );

					$product_gallery_layout = get_post_meta( $post->ID, '_sp_sf_gallery_layout', true );

					// Storefront gallery layout.
					woocommerce_wp_select( array(
						'id'			=> '_sp_sf_gallery_layout',
						'value' 		=> $product_gallery_layout,
						'label' 		=> __( 'Gallery', 'storefront-powerpack' ),
						'desc_tip' 		=> true,
						'description' 	=> sprintf( __( 'Overrides the Gallery Layout option under %sAppearance > Customize > Product Details%s.', 'storefront-powerpack' ), '<strong>', '<strong>' ),
						'options' 		=> array(
											''        => __( 'Override layout&hellip;', 'storefront-powerpack' ),
											'default' => __( 'Default', 'storefront-powerpack' ),
											'stacked' => __( 'Stacked', 'storefront-powerpack' ),
											'hide'    => __( 'Hide', 'storefront-powerpack' ),
										),
					) );

					// Storefront product tabs.
					$product_tabs = get_post_meta( $post->ID, '_sp_sf_product_tabs', true );

					woocommerce_wp_select( array(
											'id' 			=> '_sp_sf_product_tabs',
											'value' 		=> $product_tabs,
											'label' 		=> __( 'Product tabs', 'storefront-powerpack' ),
											'desc_tip' 		=> true,
											'description' 	=> sprintf( __( 'Overrides the Product Tabs option under %sAppearance > Customize > Product Details%s.', 'storefront-powerpack' ), '<strong>', '<strong>' ),
											'options' 		=> array(
																''     => __( 'Override visibility&hellip;', 'storefront-powerpack' ),
																'show' => __( 'Show', 'storefront-powerpack' ),
																'hide' => __( 'Hide', 'storefront-powerpack' ),
															),
					) );

					// Storefront related products.
					$product_related = get_post_meta( $post->ID, '_sp_sf_product_related', true );

					woocommerce_wp_select( array(
											'id' 			=> '_sp_sf_product_related',
											'value' 		=> $product_related,
											'label' 		=> __( 'Related products', 'storefront-powerpack' ),
											'desc_tip' 		=> true,
											'description' 	=> sprintf( __( 'Overrides the Related Products option under %sAppearance > Customize > Product Details%s.', 'storefront-powerpack' ), '<strong>', '<strong>' ),
											'options' 		=> array(
																''     => __( 'Override visibility&hellip;', 'storefront-powerpack' ),
																'show' => __( 'Show', 'storefront-powerpack' ),
																'hide' => __( 'Hide', 'storefront-powerpack' ),
															),
					) );

					// Storefront product description.
					$product_description = get_post_meta( $post->ID, '_sp_sf_product_description', true );

					woocommerce_wp_select( array(
											'id' 			=> '_sp_sf_product_description',
											'value' 		=> $product_description,
											'label' 		=> __( 'Product description', 'storefront-powerpack' ),
											'desc_tip' 		=> true,
											'description' 	=> sprintf( __( 'Overrides the Product Description option under %sAppearance > Customize > Product Details%s.', 'storefront-powerpack' ), '<strong>', '<strong>' ),
											'options' 		=> array(
																''     => __( 'Override visibility&hellip;', 'storefront-powerpack' ),
																'show' => __( 'Show', 'storefront-powerpack' ),
																'hide' => __( 'Hide', 'storefront-powerpack' ),
															),
					) );

					// Storefront product meta.
					$product_meta = get_post_meta( $post->ID, '_sp_sf_product_meta', true );

					woocommerce_wp_select( array(
											'id' 			=> '_sp_sf_product_meta',
											'value' 		=> $product_meta,
											'label' 		=> __( 'Product meta', 'storefront-powerpack' ),
											'desc_tip' 		=> true,
											'description' 	=> sprintf( __( 'Overrides the Product Meta option under %sAppearance > Customize > Product Details%s.', 'storefront-powerpack' ), '<strong>', '<strong>' ),
											'options' 		=> array(
																''     => __( 'Override visibility&hellip;', 'storefront-powerpack' ),
																'show' => __( 'Show', 'storefront-powerpack' ),
																'hide' => __( 'Hide', 'storefront-powerpack' ),
															),
					) );

					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Adds a custom 'Storefront' tab to the product data box.
		 *
		 * @param array $tabs tab args.
		 * @return $tabs array of tab args
		 */
		public function custom_product_data_tab( $tabs ) {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return $tabs;
			}

			$tabs['storefront'] = array(
				'label'  => __( 'Storefront', 'storefront-powerpack' ),
				'target' => 'storefront_data',
				'class'  => array(),
			);

			return $tabs;
		}

		/**
		 * Display a notice linking to the Customizer
		 *
		 * @since   1.0.0
		 * @return  void
		 */
		public function activation_notice() {
			$notices = get_option( 'sp_activation_notice' );

			if ( $notices = get_option( 'sp_activation_notice' ) ) {

				foreach ( $notices as $notice ) {
					echo '<div class="updated">' . wp_kses_post( $notice ) . '</div>';
				}

				delete_option( 'sp_activation_notice' );
			}
		}

		/**
		 * Plugin page links
		 *
		 * @param array $links plugin action links.
		 * @since  1.0.0
		 */
		public function plugin_links( $links ) {
			$plugin_links = array(
				'<a href="https://woocommerce.com/my-account/tickets/">' . __( 'Support', 'storefront-powerpack' ) . '</a>',
				'<a href="https://docs.woocommerce.com/document/storefront-powerpack/">' . __( 'Docs', 'storefront-powerpack' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}
	}

endif;

return new SP_Admin();
