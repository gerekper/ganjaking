<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Zoom Magnifier
 * @version 1.1.2
 */

if ( ! defined( 'YITH_WCMG' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WooCommerce_Zoom_Magnifier' ) ) {
	/**
	 * YITH WooCommerce Zoom Magnifier
	 *
	 * @since 1.0.0
	 */
	class YITH_WooCommerce_Zoom_Magnifier {

		/**
		 * Plugin object
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $obj = null;

		/**
		 * @var bool set if the WordPress version if prior than the 4.5.
		 */
		public $wp_prior_4_5 = false;

		/**
		 * @var bool set if the WooCommerce version is prior than the 2.6.
		 */
		public $wc_prior_2_6 = false;

		/**
		 * Constructor
		 *
		 * @return mixed|YITH_WCMG_Admin|YITH_WCMG_Frontend
		 * @since 1.0.0
		 */
		public function __construct() {

			// actions
			add_action( 'init', array( $this, 'init' ) );

			if ( is_admin() && ( ! isset( $_REQUEST['action'] ) || ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] != 'yith_load_product_quick_view' ) ) ) {
				$this->obj = new YITH_WCMG_Admin();
			} else {

				/** Stop the plugin on mobile devices */
				if ( ( 'yes' != get_option( 'yith_wcmg_enable_mobile' ) ) && wp_is_mobile() ) {
					return;
				}

				$this->obj = new YITH_WCMG_Frontend();
			}

			return $this->obj;
		}

		/**
		 * Init method:
		 *  - default options
		 *
		 * @access public
		 * @since  1.0.0
		 */
		public function init() {
			$this->_image_sizes();

			global $wp_version;
			$this->wp_prior_4_5 = version_compare( $wp_version, '4.5.0', '<' );
			$this->wc_prior_2_6 = version_compare( WC()->version, '2.6', '<' );

            /* === Show Plugin Information === */
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWZM_DIR . '/' . basename( YITH_YWZM_FILE ) ), array( $this, 'action_links' ) );
            add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
		}


		/**
		 * Add image sizes
		 *
		 * Init images
		 *
		 * @access protected
		 * @return void
		 * @since  1.0.0
		 */
		protected function _image_sizes() {
			$size   = get_option( 'woocommerce_magnifier_image' );
			$width  = $size['width'];
			$height = $size['height'];
			$crop   = isset( $size['crop'] ) ? true : false;

			add_image_size( 'shop_magnifier', $width, $height, $crop );
		}

        /**
         * Action links
         *
         *
         * @return void
         * @since    1.4.1
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function action_links( $links ) {
            $links = yith_add_action_links( $links, $this->_panel_page, false );
            return $links;
        }
        /**
         * Plugin Row Meta
         *
         *
         * @return void
         * @since    1.4.1
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWZM_FREE_INIT' ) {
            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
                $new_row_meta_args['slug'] = YITH_YWZM_SLUG;
            }

            return $new_row_meta_args;
        }
	}
}