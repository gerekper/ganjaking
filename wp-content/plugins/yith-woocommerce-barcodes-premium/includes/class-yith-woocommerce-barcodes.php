<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YITH_WooCommerce_Barcodes' ) ) {

	/**
	 *
	 * @class   YITH_WooCommerce_Barcodes
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_WooCommerce_Barcodes {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * @var bool set if the barcode generation is enabled for orders
		 */
		public $enable_on_orders = true;

		/**
		 * @var bool create automatically on new order
		 *
		 */
		public $create_on_orders = false;

		/**
		 * @var bool set if the barcode generation is enabled for products
		 */
		public $enable_on_products = true;

		/**
		 * @var bool permit to enter a custom value for the product barcode
		 */
		public $manual_value_on_products = false;

		/**
		 * @var bool create automatically barcode for new products
		 */
		public $create_on_products = false;

		/**
		 * @var string default barcode protocol to be used for orders
		 */
		public $orders_protocol = '';

		/**
		 * @var bool set if the barcode should be shown on order page
		 */
		public $show_on_order_page = false;

		/**
		 * @var string default barcode protocol to be used for orders
		 */
		public $products_protocol = '';

		/**
		 * @var bool set if the barcode should be shown on single product page
		 */
		public $show_on_product_page = true;

		/**
		 * @var bool show the order barcode on email sent whem the order is set as completed
		 */
		public $show_on_email_completed = false;

		/**
		 * @var bool show the order barcode on email sent when the order is set as completed
		 */
		public $show_on_email_all = false;

        /**
         * @var string Premium version landing link
         */
        protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-barcodes-and-qr-codes/';

        /**
         * @var string Plugin official documentation
         */
        protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-barcodes/';

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_live = 'https://plugins.yithemes.com/yith-woocommerce-barcodes/';

        /**
         * @var string Official plugin support page
         */
        protected $_support = 'https://yithemes.com/my-account/support/dashboard/';

        /**
         * @var string Plugin panel page
         */
        protected $_panel_page = 'yith_woocommerce_barcodes_panel';

		/**
		 * @var bool set if product barcode should be shown on completed emails
		 */
		public $show_product_barcode_on_email = false;

		public static function get_instance() {
			if ( is_null ( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {
			$this->init_settings ();

			$this->includes ();

		}

		public function includes() {
			/** Load external library, if needed */

			require_once ( YITH_YWBC_INCLUDES_DIR . 'class-yith-ywbc-shortcodes.php' );
            require_once ( YITH_YWBC_INCLUDES_DIR . 'class-yith-ywbc-manage-barcodes.php' );


            if ( is_admin () || ( ! empty( $_GET['wc-ajax'] ) ) ) {
				require_once ( YITH_YWBC_INCLUDES_DIR . 'class-yith-ywbc-backend.php' );
			}

			if ( ! is_admin () || ( defined ( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				require_once ( YITH_YWBC_INCLUDES_DIR . 'class-yith-ywbc-frontend.php' );
			}

			//  Elementor Widgets integration
			if ( defined('ELEMENTOR_VERSION') ) {
				require_once ( YITH_YWBC_DIR . 'includes/third-party/elementor/class-ywbc-elementor.php' );
			}

		}

		/**
		 * Initialize plugin settings
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function init_settings() {

			$this->enable_on_orders              = "yes" == get_option ( 'ywbc_enable_on_orders', 'no' );
			$this->create_on_orders              = "yes" == get_option ( 'ywbc_create_on_orders', 'no' );
			$this->orders_protocol               = get_option ( 'order_barcode_or_qr', 'barcode' ) == 'barcode' ? get_option ( 'ywbc_orders_protocol', 'EAN13' ) : 'QRcode' ;
			$this->show_on_order_page            = $this->enable_on_orders && ( "yes" == get_option ( 'ywbc_show_on_order_page', 'no' ) );
			$this->enable_on_products            = "yes" == get_option ( 'ywbc_enable_on_products', 'no' );
			$this->manual_value_on_products      = "yes" == get_option ( 'ywbc_product_manual_barcode_product', 'no' );
			$this->create_on_products            = "yes" == get_option ( 'ywbc_create_on_products', 'no' );
			$this->products_protocol             = get_option ( 'product_barcode_or_qr', 'barcode' ) == 'barcode' ? get_option ( 'ywbc_products_protocol', 'EAN13' ) : 'QRcode' ;
			$this->show_on_product_page          = $this->enable_on_products && ( "yes" == get_option ( 'ywbc_show_on_product_page', 'no' ) );
			$_show_on_emails                     = get_option ( 'ywbc_show_on_emails', 'no' );
			$this->show_on_email_completed       = "completed" == $_show_on_emails;
			$this->show_on_email_all             = "all" == $_show_on_emails;
			$this->show_product_barcode_on_email = "yes" == get_option ( 'ywbc_show_product_barcode_on_emails', 'no' );
		}

		/**
		 * Create the barcode values for the order
		 *
		 * @param int    $order_id
		 * @param string $protocol
		 * @param string $value
		 */
		public function create_order_barcode( $order_id, $protocol = '', $value = '' ) {
			$protocol  = $protocol ? $protocol : $this->orders_protocol;

			$order = wc_get_order( $order_id );

			$value_option = get_option( 'ywbc_order_barcode_type', 'id');

			if ( $value_option == 'number' ){
				$value_type = $order->get_order_number();
			}
			elseif ( $value_option == 'custom_field' ){
				$custom_field = get_option( 'ywbc_order_barcode_type_custom_field');
				$value_type = get_post_meta( $order_id, $custom_field , true );
			}
			else{
				$value_type = $order_id;
			}

			$the_value = $value ? $value : $value_type;

			$the_value = apply_filters ( 'yith_barcode_new_order_value', $the_value, $order_id, $protocol, $value );

			$this->generate_barcode_image ( $order_id, $protocol, $the_value );
		}

		/**
		 * Retrieve the filename to be used for the barcode file
		 *
		 * @param int    $obj_id   the object id related to the barcode
		 * @param string $protocol the Barcode protocol to use
		 * @param string $value    the value to use as barcode text
		 *
		 * @return string
		 */
		public function get_server_file_path( $obj_id, $protocol, $value ) {

			$image_filename = apply_filters ( 'yith_ywbc_image_filename', sprintf ( "Image%s_%s_%s.png", $obj_id, $protocol, $value ), $obj_id, $protocol, $value );

			$image_filename = str_replace( '/', '_', $image_filename);
			$image_filename = str_replace( ':', '_', $image_filename);

			return YITH_YWBC_UPLOAD_DIR . '/' . $image_filename;
		}

		/**
		 * Retrieve the filename to be used for the barcode file
		 *
		 * @param YITH_Barcode $barcode the barcode instance whose file should be generated
		 *
		 * @return string
		 */
		public function get_public_file_path( $barcode ) {

			$pos  = strrpos ( $barcode->image_filename, "/" );
			$pos2 = strrpos ( $barcode->image_filename, "\\" );

			$pos  = ( $pos === false ) ? - 1 : $pos;
			$pos2 = ( $pos2 === false ) ? - 1 : $pos2;

			$last_index = ( $pos > $pos2 ) ? $pos : $pos2;

			if ( $last_index ) {
				return YITH_YWBC_UPLOAD_URL . substr ( $barcode->image_filename, $last_index );
			}

			//  Previous value
			return str_replace ( YITH_YWBC_UPLOAD_DIR, YITH_YWBC_UPLOAD_URL, $barcode->image_filename );
		}

		/**
		 * Generate a new barcode instance
		 *
		 * @param int    $object_id the id of the object(WC_Product or WC_Order) associated to this barcode
		 * @param string $protocol  the protocol to use
		 * @param string $value     the value to use as the barcode value
		 *
		 * @return YITH_Barcode
		 */
		public function generate_barcode_image( $object_id, $protocol, $value ) {
			$barcode = new YITH_Barcode( $object_id );

			$image_path = $this->get_server_file_path ( $object_id, $protocol, $value );
			$barcode->generate ( $protocol, $value, $image_path );
			$barcode->save ();

			return $barcode;
		}

		/**
		 * Create the barcode values for the order
		 *
		 * @param int    $product_id
		 * @param string $protocol
		 * @param string $value
		 *
		 * @return YITH_Barcode
		 */
		public function create_product_barcode( $product_id, $protocol = '', $value = '' ) {

			$protocol  = $protocol ? $protocol : $this->products_protocol;

			$value_option = get_option( 'ywbc_product_barcode_type', 'id');

			if ( $value_option == 'sku' ){
				$product = wc_get_product( $product_id );
				$value_type = $product->get_sku();
			}
			elseif ( $value_option == 'custom_field' ){
				$custom_field = get_option( 'ywbc_product_barcode_type_custom_field');

				if ( $custom_field == 'product_url' && $protocol == 'QRcode' ){
					$product_link =get_permalink( $product_id );
					$value_type = str_replace( '/', 'O', $product_link );
				}
				else{
					$value_type = get_post_meta( $product_id, $custom_field , true );
				}
			}
			else{
				$value_type = $product_id;
			}

			$the_value = $value ? $value : $value_type;


			$the_value = apply_filters ( 'yith_barcode_new_product_value', $the_value, $product_id, $protocol, $value );

			return $this->generate_barcode_image ( $product_id, $protocol, $the_value );
		}

		/**
		 * Build an <img> tag with the barcode image rendered from the base64 format
		 *
		 * @param YITH_Barcode $barcode
		 * @param bool         $show_value Whether to show barcode value or not
		 * @param string       $layout     Optional layout for the barcode
		 *
		 * @return string
		 */
		public function render_barcode( $barcode, $show_value = false, $layout = '' ) {
			$barcode_src       = apply_filters( 'yith_ywbc_barcode_src',
                $barcode->image_filename ? $this->get_public_file_path ( $barcode ) : 'data:image/png;base64,' . $barcode->image,
                $barcode->get_display_value(),
                $barcode->get_protocol()
            );

            $barcode_value     = $barcode->get_display_value ();

			$barcode_image_tag = apply_filters('yith_ywbc_barcode_image_tag', '<img class="ywbc-barcode-image" src="' . esc_attr ( $barcode_src ) . '">', $barcode, $barcode_value, $barcode_src ) ;

			if ( empty( $layout ) ) {
				// Format the image src:  data:{mime};base64,{data};
				$src = $barcode_image_tag;

				if ( $show_value ) {
					$src .= '<div class="ywbc-barcode-display-container"><span class="ywbc-barcode-display-value">' . $barcode_value . '</span></div>';
				}
			} else {
				$finds    = array(
					'{barcode_image}',
					'{barcode_code}'
				);
				$replaces = array(
					$barcode_image_tag,
					$barcode_value
				);

				$src = str_replace ( $finds, $replaces, $layout );
			}

			return apply_filters ( 'yith_ywbc_render_barcode_html', $src, $barcode_src, $barcode_value, $barcode, $show_value, $layout );
		}

		/**
		 * Show the barcode text and graphic
		 *
		 * @param int|YITH_Barcode $obj             The object id or specific YITH_Barcode instance
		 * @param bool             $hide_if_missing Set if the result should be shown even if no barcode exists for the product
		 * @param string           $inline_css      CSS style to be used(needs CssInliner)
		 * @param string           $layout          HTML to be used to display barcode; it should contain {barcode_image} and or {barcode_code} placeholders
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_barcode( $obj, $hide_if_missing = false, $inline_css = '', $layout = '') {

			$barcode = is_numeric ( $obj ) ? new YITH_Barcode( $obj ) : $obj;

			if ( $barcode instanceof YITH_Barcode ) {
				ob_start ();
				?>
				<div id="ywbc_barcode_value">
					<?php if ( $barcode->exists () ) {
					    $show_value = apply_filters('yith_ywbc_show_value_on_barcode', true, $obj );
						echo $this->render_barcode ( $barcode, $show_value, $layout );
					} elseif ( ! $hide_if_missing ) {
						?>
						<span class="ywbc-no-barcode-value"><?php esc_html_e( "No barcode is available for this element.", 'yith-woocommerce-barcodes' ); ?></span>
						<?php
					} ?>
				</div>

				<?php
				$content = ob_get_clean ();

				if ( $inline_css ) {
					try {
                        if( !class_exists('CssInliner') ) {
                            include_once( ABSPATH . 'wp-content/plugins/woocommerce/vendor/pelago/emogrifier/src/Emogrifier/CssInliner.php' );
                        }
                        else{
                            $cssinliner_class = new CssInliner( $content, $inline_css );
                            $content = $cssinliner_class::fromHtml($content)->inlineCss( $inline_css )->render();
                        }
					} catch
					( Exception $e ) {
						$logger = new WC_Logger();
						$logger->add ( 'CssInliner', $e->getMessage () );
					}
				}
			}

			if ( $content ) {
				echo $content;
			}
		}

	}
}
