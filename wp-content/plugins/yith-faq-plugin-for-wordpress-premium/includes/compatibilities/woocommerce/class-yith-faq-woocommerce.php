<?php
/**
 * WooCommerce integration class
 *
 * @package YITH\FAQPluginForWordPress\Compatibilities\WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_FAQ_WooCommerce' ) ) {

	/**
	 * Manages plugin settings
	 *
	 * @class   YITH_FAQ_WooCommerce
	 * @since   2.0.0
	 * @author  YITH <plugins@yithemes.com>
	 * @package YITH\FAQPluginForWordPress\Compatibilities\WooCommerce
	 */
	class YITH_FAQ_WooCommerce {

		/**
		 * ID for FAQ tab in product edit page
		 *
		 * @var string
		 */
		public $product_tab = 'yith_faq';

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 20 );
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_faq_tab' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'write_tab_options' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_faq_tab' ), 10 );
			add_filter( 'woocommerce_product_tabs', array( $this, 'add_faq_tab_frontend' ) );

		}

		/**
		 * Add scripts and styles
		 *
		 * @return void
		 * @since  2.0.0
		 */
		public function admin_scripts() {

			$current_screen = get_current_screen();

			if ( $current_screen && 'product' === $current_screen->post_type ) {
				wp_enqueue_style( 'yith-faq-woocommerce', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/woocommerce.css' ), array(), YITH_FWP_VERSION );
				wp_enqueue_script( 'yith-faq-woocommerce', yit_load_js_file( YITH_FWP_ASSETS_URL . '/js/woocommerce.js' ), array( 'jquery' ), YITH_FWP_VERSION, false );
				wp_enqueue_script( 'yith-plugin-fw-fields' );
				wp_enqueue_style( 'yith-plugin-fw-fields' );
				wp_localize_script(
					'yith-faq-woocommerce',
					'yfwp_wc',
					array(
						'errors' => array(
							'missing_field'  => esc_html__( 'This field is required.', 'yith-faq-plugin-for-wordpress' ),
							'missing_preset' => esc_html__( 'Please select an FAQ preset.', 'yith-faq-plugin-for-wordpress' ),
						),
					)
				);

			}
		}

		/**
		 * Add FAQ tab in product edit page
		 *
		 * @param array $tabs The tabs to display.
		 *
		 * @return array
		 * @since  2.0.0
		 */
		public function add_faq_tab( $tabs ) {
			$tabs['yith-faq'] = array(
				'label'    => esc_html__( 'FAQ', 'yith-faq-plugin-for-wordpress' ),
				'target'   => $this->product_tab,
				'class'    => '',
				'priority' => 100,
			);

			return $tabs;
		}

		/**
		 * Add FAQ tab content in product edit page
		 *
		 * @return void
		 * @since  2.0.0
		 */
		public function write_tab_options() {

			global $post;
			$product = wc_get_product( $post->ID );

			if ( $product ) {
				$show_faq_tab = $product->get_meta( 'yfwp_show_faq_tab' );
				$tab_label    = $product->get_meta( 'yfwp_tab_label' );
				$shortcode    = $product->get_meta( 'yfwp_shortcode' );
			} else {
				$show_faq_tab = 'off';
				$tab_label    = '';
				$shortcode    = '';
			}

			?>
			<div id="<?php echo esc_attr( $this->product_tab ); ?>" class="panel woocommerce_options_panel yith-plugin-ui options_group">
				<h4>
					<?php esc_html_e( 'Frequently Asked Questions', 'yith-faq-plugin-for-wordpress' ); ?>
				</h4>
				<div class="wrap-field">
					<span class="field-label">
						<?php esc_html_e( 'Show FAQ tab', 'yith-faq-plugin-for-wordpress' ); ?>
					</span>
					<span class="option-field">
						<?php
						yith_plugin_fw_get_field(
							array(
								'id'    => 'yfwp_show_faq_tab',
								'name'  => 'yfwp_show_faq_tab',
								'type'  => 'onoff',
								'value' => $show_faq_tab,
							),
							true
						);
						?>
					</span>
					<span class="description"><?php esc_html_e( 'Enable to show the FAQ tab in this product.', 'yith-faq-plugin-for-wordpress' ); ?></span>
				</div>
				<div class="wrap-field wrap-field-dep">
					<span class="field-label">
						<?php esc_html_e( 'Tab label', 'yith-faq-plugin-for-wordpress' ); ?>
					</span>
					<span class="option-field">
						<?php
						yith_plugin_fw_get_field(
							array(
								'id'    => 'yfwp_tab_label',
								'name'  => 'yfwp_tab_label',
								'type'  => 'text',
								'value' => $tab_label,
							),
							true
						);
						?>
					</span>
					<span class="description"><?php esc_html_e( 'Enter the label for the tab that contains the FAQs.', 'yith-faq-plugin-for-wordpress' ); ?></span>
				</div>
				<div class="wrap-field wrap-field-dep">
					<span class="field-label">
						<?php esc_html_e( 'FAQ to show', 'yith-faq-plugin-for-wordpress' ); ?>
					</span>
					<span class="option-field">
						<?php
						yith_plugin_fw_get_field(
							array(
								'id'      => 'yfwp_shortcode',
								'name'    => 'yfwp_shortcode',
								'class'   => 'wc-enhanced-select',
								'type'    => 'select',
								'value'   => $shortcode,
								'options' => yfwp_get_presets(),
							),
							true
						);
						?>
					</span>
					<span class="description"><?php esc_html_e( 'Choose the FAQ shortcode to show in this product.', 'yith-faq-plugin-for-wordpress' ); ?></span>
				</div>
			</div>
			<?php
		}

		/**
		 * Save FAQ tab options
		 *
		 * @param integer $product_id Product ID.
		 *
		 * @return void
		 * @since  2.0.0
		 */
		public function save_faq_tab( $product_id ) {

			// phpcs:disable WordPress.Security.NonceVerification.Missing
			$show_faq_tab = isset( $_POST['yfwp_show_faq_tab'] ) ? 'yes' : 'no';
			$tab_label    = isset( $_POST['yfwp_tab_label'] ) ? sanitize_text_field( wp_unslash( $_POST['yfwp_tab_label'] ) ) : '';
			$shortcode    = isset( $_POST['yfwp_shortcode'] ) ? sanitize_text_field( wp_unslash( $_POST['yfwp_shortcode'] ) ) : '';
			$product      = wc_get_product( $product_id );
			$product->update_meta_data( 'yfwp_show_faq_tab', $show_faq_tab );
			$product->update_meta_data( 'yfwp_tab_label', $tab_label );
			$product->update_meta_data( 'yfwp_shortcode', $shortcode );
			$product->save();

		}

		/**
		 * Add FAQ tab to single product page
		 *
		 * @param array $tabs Array of tabs.
		 *
		 * @return  array
		 * @since   2.0.0
		 */
		public function add_faq_tab_frontend( $tabs ) {

			global $product;

			if ( 'yes' === $product->get_meta( 'yfwp_show_faq_tab' ) ) {
				$tabs['yith_faq'] = array(
					'title'        => apply_filters( 'yith_fwp_faq_tab_label', $product->get_meta( 'yfwp_tab_label' ), $product ),
					'priority'     => 99,
					'callback'     => array( $this, 'print_faqs_on_product_page' ),
					'shortcode_id' => $product->get_meta( 'yfwp_shortcode' ),
				);
			}

			return $tabs;

		}

		/**
		 * FAQ tab template
		 *
		 * @param string $key Tab key.
		 * @param array  $tab Tab options.
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function print_faqs_on_product_page( $key, $tab ) {
			global $product;

			if ( ! $product || 'yith_faq' !== $key ) {
				return;
			}

			/**
			 * DO_ACTION: yith_fwp_before_faq_tab
			 *
			 * Execute code before printing the FAQ tab.
			 *
			 * @param WC_Product $product The current product.
			 */
			do_action( 'yith_fwp_before_faq_tab', $product );

			echo do_shortcode( yfwp_create_shortcode( $tab['shortcode_id'] ) );

			/**
			 * DO_ACTION: yith_fwp_after_faq_tab
			 *
			 * Execute code after printing the FAQ tab.
			 *
			 * @param WC_Product $product The current product.
			 */
			do_action( 'yith_fwp_after_faq_tab', $product );

		}

	}

	new YITH_FAQ_WooCommerce();

}
