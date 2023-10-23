<?php
/**
 * WAPO Frontend Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_Front' ) ) {

	/**
	 *  Front class.
	 *  The class manage all the frontend behaviors.
	 */
	class YITH_WAPO_Front {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WAPO_Front
		 */
		protected static $instance;

		/**
		 * Current product price
		 *
		 * @var float
		 */
		public $current_product_price = 0;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WAPO_Front | YITH_WAPO_Front_Premium
		 */
		public static function get_instance() {
			$self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

			return ! is_null( $self::$instance ) ? $self::$instance : $self::$instance = new $self();
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			// Enqueue scripts.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );

			add_action( 'wp_enqueue_scripts', array( $this, 'custom_frontend_styles' ), 11 );

			// Print Options.
			if ( get_option( 'yith_wapo_options_position' ) === 'after' ) {
				add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'print_container' ) );
			} else { // Default.
				add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'print_container' ) );
			}

			add_action( 'yith_gift_cards_template_before_add_to_cart_button', array( $this, 'print_container' ) );

			// Ajax live print.
			add_action( 'wp_ajax_live_print_blocks', array( $this, 'live_print_blocks' ) );
			add_action( 'wp_ajax_nopriv_live_print_blocks', array( $this, 'live_print_blocks' ) );
			// Ajax upload file.
			add_action( 'wp_ajax_yith_wapo_upload_file', array( $this, 'ajax_upload_file' ) );
			add_action( 'wp_ajax_nopriv_yith_wapo_upload_file', array( $this, 'ajax_upload_file' ) );
			// Ajax update product price.
			add_action( 'wp_ajax_update_totals_with_suffix', array( $this, 'update_totals_with_suffix' ) );
			add_action( 'wp_ajax_nopriv_update_totals_with_suffix', array( $this, 'update_totals_with_suffix' ) );

			// Ajax update default product price.
			add_action( 'wp_ajax_get_default_variation_price', array( $this, 'get_default_variation_price' ) );
			add_action( 'wp_ajax_nopriv_get_default_variation_price', array( $this, 'get_default_variation_price' ) );

			// Shortcodes.
			add_shortcode( 'yith_wapo_show_options', array( $this, 'yith_wapo_show_options_shortcode' ) );
			add_action( 'yith_wapo_show_options_shortcode', array( $this, 'print_container' ) );

			// Modify Uploads folder
			add_filter( 'upload_dir', array( $this, 'modify_upload_images_dir' ) );

			// Attach uploads to email
			add_filter( 'woocommerce_email_attachments', array( $this, 'attach_uploads_to_emails' ), 10, 3 );

		}

		/**
		 * Front enqueue scripts
		 */
		public function enqueue_scripts() {

			if ( apply_filters( 'yith_wapo_enqueue_front_scripts', true ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				// CSS.
				wp_enqueue_style( 'yith_wapo_front', YITH_WAPO_URL . 'assets/css/front.css', false, YITH_WAPO_SCRIPT_VERSION );
				wp_enqueue_style( 'yith_wapo_jquery-ui', YITH_WAPO_URL . 'assets/css/_new_jquery-ui-1.12.1.css', false, YITH_WAPO_SCRIPT_VERSION );
				wp_enqueue_style( 'yith_wapo_jquery-ui-timepicker', YITH_WAPO_URL . 'assets/css/_new_jquery-ui-timepicker-addon.css', false, YITH_WAPO_SCRIPT_VERSION );
				wp_enqueue_style( 'dashicons' );

				if ( ! wp_script_is( 'yith-plugin-fw-icon-font', 'registered' ) ) {
					wp_register_style( 'yith-plugin-fw-icon-font', YIT_CORE_PLUGIN_URL . '/assets/css/yith-icon.css', array(), YITH_WAPO_SCRIPT_VERSION );
				}
				wp_enqueue_style( 'yith-plugin-fw-icon-font' );

				// ColorPicker with Iris library.
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script(
					'iris',
					admin_url( 'js/iris.min.js' ),
					array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
					YITH_WAPO_SCRIPT_VERSION,
					true
				);
				wp_enqueue_script(
					'wp-color-picker',
					admin_url( 'js/color-picker.min.js' ),
					array( 'iris', 'wp-i18n' ),
					YITH_WAPO_SCRIPT_VERSION,
					true
				);

				// JS.
				wp_register_script( 'yith_wapo_front', YITH_WAPO_URL . 'assets/js/front' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-progressbar', 'wc-add-to-cart-variation' ), YITH_WAPO_SCRIPT_VERSION, true );

				$front_localize = array(
					'dom'                            => array(
						'single_add_to_cart_button' => '.single_add_to_cart_button',
					),
					'i18n'                           => array(
						// translators: Label printed in the add-on type Date, when activating the timepicker.
						'datepickerSetTime'          => __( 'Set time', 'yith-woocommerce-product-add-ons' ),
						// translators: Label printed in the add-on type Date, when activating the timepicker.
						'datepickerSaveButton'       => __( 'Save', 'yith-woocommerce-product-add-ons' ),
						// translators: Label printed if minimum value is 1.
						'selectAnOption'             => __( 'Please, select an option', 'yith-woocommerce-product-add-ons' ),
						// translators: Label printed if minimum value is more than 1. %d is the number of options.
						'selectAtLeast'              => __( 'Please, select at least %d options', 'yith-woocommerce-product-add-ons' ),
						// translators: Label printed for exact selection value. %d is the number of options.
						'selectOptions'              => __( 'Please, select %d options', 'yith-woocommerce-product-add-ons' ),
						// translators: [FRONT] Error when the user select more than allowed options ( min/max feature ).
						'maxOptionsSelectedMessage'  => __( 'More options than allowed have been selected', 'yith-woocommerce-product-add-ons' ),
						'uploadPercentageDoneString' => _x( 'done', '[FRONT] Percentage done when uploading a file on an add-on type File.', 'yith-woocommerce-product-add-ons' ),
					),
					'ajaxurl'                        => admin_url( 'admin-ajax.php' ),
					'addons_nonce'                   => wp_create_nonce( 'addons-nonce' ),
					'upload_allowed_file_types'      => get_option( 'yith_wapo_upload_allowed_file_types', '.jpg, .jpeg, .pdf, .png, .rar, .zip' ),
					'upload_max_file_size'           => get_option( 'yith_wapo_upload_max_file_size', '5' ),
					'total_price_box_option'         => get_option( 'yith_wapo_total_price_box', 'all' ),
					'replace_product_price'          => get_option( 'yith_wapo_replace_product_price', 'no' ),
					'woocommerce_currency_symbol'    => esc_attr( get_woocommerce_currency_symbol() ),
					'woocommerce_currency'           => esc_attr( get_woocommerce_currency() ),
					'woocommerce_currency_pos'       => get_option( 'woocommerce_currency_pos', 'left' ),
					'total_thousand_sep'             => get_option( 'woocommerce_price_thousand_sep', ',' ),
					'decimal_sep'                    => get_option( 'woocommerce_price_decimal_sep', '.' ),
					'num_decimal'                    => get_option( 'woocommerce_price_num_decimals', 2 ),
					'priceSuffix'                    => wc_tax_enabled() ? get_option( 'woocommerce_price_display_suffix', '' ) : '',
					'replace_image_path'             => $this->get_product_gallery_image_path(),
					'replace_product_price_class'    => $this->get_product_price_class(),
					'hide_button_required'           => get_option( 'yith_wapo_hide_button_if_required', 'no' ),
					'messages'                       => array(
						// translators: [FRONT] Message error when total of add-ons type numbers does not exceeds the minimum set in the configuration
						'minErrorMessage'         => __( 'The sum of the numbers is below the minimum. The minimum value is:', 'yith-woocommerce-product-add-ons' ),
						// translators: [FRONT] Message error when total of add-ons type numbers exceeds the maximum set in the configuration
						'maxErrorMessage'         => __( 'The sum of the numbers exceeded the maximum. The maximum value is:', 'yith-woocommerce-product-add-ons' ),
						// translators: [FRONT] Message giving the error after checking minimum and maximum quantity when adding to cart
						'checkMinMaxErrorMessage' => __( 'Please, select an option', 'yith-woocommerce-product-add-ons' ),
						// translators: [FRONT] [FRONT] Text to show when an option is required
						'requiredMessage'         => get_option( 'yith_wapo_required_option_text', __( 'This option is required.', 'yith-woocommerce-product-add-ons' ) ),
						// translators: [FRONT] Text to show for maximum files allowed on add-on type Upload
						'maxFilesAllowed'         => __( 'Maximum uploaded files allowed. The maximum number of files allowed is: ', 'yith-woocommerce-product-add-ons' ),
                        // translators: [FRONT] Message giving the error when the file upload is not a supported extension
                        'noSupportedExtension'    => __( 'Error - not supported extension!', 'yith-woocommerce-product-add-ons' ),
                        // translators: [FRONT] Message giving the error when the file has a hight size
                        'maxFileSize'    => __( 'Error - file size for %s - max %d MB allowed!', 'yith-woocommerce-product-add-ons' ),

                    ),
					'productQuantitySelector'        => apply_filters(
						'yith_wapo_product_quantity_selector',
						'form.cart .quantity input.qty:not(.wapo-product-qty)'
					),
					'enableGetDefaultVariationPrice' => apply_filters( 'yith_wapo_get_default_variation_price_calculation', true ),
					'currentLanguage'                => '',

				);

				$front_localize = apply_filters( 'yith_wapo_frontend_localize_args', $front_localize );

				wp_localize_script( 'yith_wapo_front', 'yith_wapo', $front_localize );
				wp_enqueue_script( 'yith_wapo_front' );
			}

		}

		/**
		 * Custom frontend styles.
		 */
		public function custom_frontend_styles() {
			$css = '';

			foreach ( yith_wapo_get_attributes() as $var => $value ) {
				$css .= '--yith-wapo-' . $var . ':' . $value . ';';
			}

			$css = ':root{' . $css . '}';

			$css = apply_filters( 'yith_wapo_custom_inline_styles', $css );

			wp_add_inline_style( 'yith_wapo_front', $css );

		}

        /**
         * Get the product price classes to modify the price.
         * @return string
         */
        private function get_product_price_class() {

            $product_class = '.product .entry-summary .price:first,
            div.elementor.product .elementor-widget-woocommerce-product-price .price';

            // Is using WC Blocks.
            if ( yith_plugin_fw_wc_is_using_block_template_in_single_product() ) {
                $product_class = '.woocommerce.product .wp-block-woocommerce-product-price .amount';
            }

            return apply_filters( 'yith_wapo_replace_product_price_class', esc_attr( $product_class ) );
        }

        /**
         * Get the main product image classes on the product page to replace the image.
         * @return string
         */
        private function get_product_gallery_image_path() {

            $image_class = '.woocommerce-product-gallery .woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image:first-child img.zoomImg,
            .woocommerce-product-gallery .woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image:first-child source,
            .yith_magnifier_zoom img, .yith_magnifier_zoom_magnifier,
            .owl-carousel .woocommerce-main-image,
            .woocommerce-product-gallery__image .wp-post-image,
            .dt-sc-product-image-gallery-container .wp-post-image';

            // Is using WC Blocks.
            if ( yith_plugin_fw_wc_is_using_block_template_in_single_product() ) {
                $image_class = '.wp-block-woocommerce-product-image-gallery .woocommerce-product-gallery__wrapper .wp-post-image';
            }

            return apply_filters( 'yith_wapo_additional_replace_image_path', esc_attr( $image_class ) );
        }

		/**
		 *  Ajax upload file
		 */
		public function ajax_upload_file() {

			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			$uploadedfile     = $_FILES['currentFile'] ?? null; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$filename         = isset( $uploadedfile['name'] ) ? $uploadedfile['name'] : '';
			$upload_overrides = array( 'test_form' => false );
			$movefile         = wp_handle_upload( $uploadedfile, $upload_overrides );

			if ( $movefile && ! isset( $movefile['error'] ) && $filename ) {
				$movefile['file_name'] = $filename;
				echo wp_json_encode( $movefile );
			} else {
				echo 'ERROR!';
			}
			wp_die();

		}

		/**
		 * Modify the path of the uploaded files (add-on type File).
		 *
		 * @param array $dir The directory.
		 *
		 * @return mixed
		 */
		function modify_upload_images_dir( $dir ) {
			$action = isset( $_POST['action'] ) ? $_POST['action'] : '';

			if ( 'yith_wapo_upload_file' === $action ) {
				$folder_name = get_option( 'yith_wapo_uploads_folder', 'yith_advanced_product_options' );

				$dir['path'] = YITH_WAPO_DOCUMENT_SAVE_DIR . '/' . $folder_name;
				$dir['url']  = YITH_WAPO_DOCUMENT_SAVE_URL . '/' . $folder_name;
			}

			return $dir;

		}

		/**
		 * Attach files uploaded to the emails.
		 *
		 * @param array    $attachments The attachments.
		 * @param string   $email_id The email id.
		 * @param WC_Order $object The order object.
		 *
		 * @return array
		 * @throws Exception
		 */
		function attach_uploads_to_emails( $attachments, $email_id, $object ) {

			$attach_uploads = get_option( 'yith_wapo_attach_file_to_email', 'no' );

			if ( 'yes' !== $attach_uploads || ! $object instanceof WC_Order ) {
				return $attachments;
			}

			$items = $object->get_items();

			foreach ( $items as $item_id => $item ) {

				$meta_data = wc_get_order_item_meta( $item_id, '_ywapo_meta_data', true );

				if ( $meta_data && is_array( $meta_data ) ) {
					foreach ( $meta_data as $index => $option ) {
						foreach ( $option as $key => $value ) {
							if ( $key && '' !== $value ) {
								$values = YITH_WAPO::get_instance()->split_addon_and_option_ids( $key, $value );

								$addon_id  = $values['addon_id'];
								$option_id = $values['option_id'];

								$info       = yith_wapo_get_option_info( $addon_id, $option_id );
								$addon_type = $info['addon_type'];

								if ( 'file' === $addon_type ) {
									if ( is_array( $value ) ) {
										foreach ( $value as $attachment ) {
											$attachments[] = $attachment;
										}
									}
								}
							}
						}
					}
				}
			}

			return $attachments;
		}

		/**
		 * Custom price
		 *
		 * @param string $price Price.
		 * @param object $product Product.
		 *
		 * @return float
		 */
		public function custom_price( $price, $product ) {
			return (float) $price;
		}

		/**
		 * Custom variable price
		 *
		 * @param string $price Price.
		 * @param string $variation Variation.
		 * @param object $product Product.
		 *
		 * @return float
		 */
		public function custom_variable_price( $price, $variation, $product ) {
			return (float) $price;
		}

		/**
		 * Live print blocks
		 */
		public function live_print_blocks() {

			// Simple, grouped and external products.
			add_filter( 'woocommerce_product_get_price', array( $this, 'custom_price' ), 99, 2 );
			add_filter( 'woocommerce_product_get_regular_price', array( $this, 'custom_price' ), 99, 2 );
			// Variations.
			add_filter( 'woocommerce_product_variation_get_regular_price', array( $this, 'custom_price' ), 99, 2 );
			add_filter( 'woocommerce_product_variation_get_price', array( $this, 'custom_price' ), 99, 2 );
			// Variable (price range).
			add_filter( 'woocommerce_variation_prices_price', array( $this, 'custom_variable_price' ), 99, 3 );
			add_filter( 'woocommerce_variation_prices_regular_price', array( $this, 'custom_variable_price' ), 99, 3 );

			global $woocommerce, $product, $variation;
			$woocommerce  = WC();
			$product_id   = 0;
			$variation_id = 0;
			$variation    = false;
			$addons       = $_POST['addons'] ?? array(); // phpcs:ignore
			foreach ( $addons as $key => $input ) {
				if ( 'yith_wapo_product_id' === $input['name'] ) {
					$product_id = $input['value'];
				}
				if ( 'variation_id' === $input['name'] ) {
					$variation_id = $input['value'];
					if ( $variation_id > 0 ) {
						$variation = new WC_Product_Variation( $variation_id );
					}
				}
			}

			$product = wc_get_product( $product_id );

			$this->print_blocks();
			wp_die();
		}

		/**
		 * Print container
		 */
		public function print_container() {
			global $product;

			$not_allowed_product_types = array( 'grouped' );

			if ( apply_filters( 'yith_wapo_allowed_product_types', true, $not_allowed_product_types ) && in_array( $product->get_type(), $not_allowed_product_types, true ) ) {
				return;
			}

			wc_get_template(
				'/front/addons-container.php',
				array(
					'instance' => $this,
					'product'  => $product,
				),
				'',
				YITH_WAPO_DIR . '/templates'
			);

		}

		/**
		 * Print blocks
		 */
		public function print_blocks() {

			global $product, $variation;

			$currency = $_POST['currency'] ?? false;

			if ( $product ) {

                $show_total_price_box = false;

                $_product_id   = apply_filters( 'yith_wapo_get_original_product_id', $product->get_id() );
                $_variation_id = '';

                if ( $variation instanceof WC_Product_Variation ) {
                    $_variation_id = apply_filters( 'yith_wapo_get_original_product_id', $variation->get_id() );
                }

                $exclude_global = $product && apply_filters( 'yith_wapo_exclude_global', get_post_meta( $product->get_id(), '_wapo_disable_global', true ) === 'yes' ? 1 : 0 );

				$blocks_product_price = floatval( $_POST['price'] ?? ( $variation ? $variation->get_price() : $product->get_price() ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$blocks_product_price = apply_filters( 'yith_wapo_blocks_product_price', $blocks_product_price, $product, $variation );
				$blocks_product_price = $blocks_product_price + ( ( $blocks_product_price / 100 ) * yith_wapo_get_tax_rate() );

				$this->current_product_price = $blocks_product_price;


				// Style options.
                $color_array_default = array( 'color' => '#ffffff' );

                $style_addon_titles     = get_option( 'yith_wapo_style_addon_titles', 'h3' );
				$style_addon_background = get_option( 'yith_wapo_style_addon_background', $color_array_default )['color'];

                $total_price_box = get_option( 'yith_wapo_total_price_box', 'all' );

				$blocks = YITH_WAPO_DB()->yith_wapo_get_blocks_by_product( $_product_id, $_variation_id, true );

                echo '<input type="hidden" id="yith_wapo_product_id" name="yith_wapo_product_id" value="' . esc_attr( $product->get_id() ) . '">';
                echo '<input type="hidden" id="yith_wapo_product_img" name="yith_wapo_product_img" value="">';
                echo '<input type="hidden" id="yith_wapo_is_single" name="yith_wapo_is_single" value="1">';

                /**
                 * Action before printing all the add-ons
                 */
                do_action( 'yith_wapo_before_addons' );

                foreach ( $blocks as $key => $block_id ) {

					/**
					 * @var YITH_WAPO_Block $block
					 */
					$block = yith_wapo_instance_class(
                        'YITH_WAPO_Block',
                        array(
                            'id'   => $block_id,
                        )
                    );

					// Vendor.
					$addons       = YITH_WAPO()->db->yith_wapo_get_addons_by_block_id( $block->id, true );
					$total_addons = count( $addons );
					if ( $total_addons > 0 ) {

                        $show_total_price_box = true;

						wc_get_template(
							'/front/block.php',
							array(
								'block'                  => $block,
								'addons'                 => $addons,
								'style_addon_titles'     => $style_addon_titles,
								'style_addon_background' => $style_addon_background,
								'currency'               => $currency,
							),
							'',
							YITH_WAPO_DIR . '/templates'
						);
					}
				}

                /**
                 * Action after printing all the add-ons
                 */
                do_action( 'yith_wapo_after_addons' );

                $show_total_price_box = apply_filters( 'yith_wapo_show_total_price_box', $show_total_price_box, $product, $variation );

				// TODO: create print_addons_price_table() function
				if ( 'hide_all' !== $total_price_box && $show_total_price_box ) :
					wc_get_template(
						'/front/addons-price-table.php',
						array(
							'product'              => $product,
							'variation'            => $variation,
							'total_price_box'      => $total_price_box,
							'blocks_product_price' => $blocks_product_price,
						),
						'',
						YITH_WAPO_DIR . '/templates'
					);

				endif;
			}
		} // end print_blocks()

		/**
		 * Show Options Shortcode
		 *
		 * @param array $atts Attributes.
		 */
		public function yith_wapo_show_options_shortcode( $atts ) {
			ob_start();
			if ( is_product() ) {
				do_action( 'yith_wapo_show_options_shortcode' );
			} else {
				echo '<strong>' . esc_html__( 'This is not a product page!', 'yith-woocommerce-product-add-ons' ) . '</strong>';
			}

			return ob_get_clean();
		}
		/**
		 * Update totals with suffix
		 *
		 * Update totals when there are suffix configured
		 *
		 * @since  3.2.0
		 */
		public function update_totals_with_suffix() {
			check_ajax_referer( 'addons-nonce', 'security' );
			$values = array( 'price_html' => '' );
			if ( isset( $_POST['product_id'] ) && $_POST['product_id'] ) {
				$product_id = $_POST['product_id'];
				$product    = wc_get_product( $product_id );
				if ( $product instanceof WC_Product ) {
					if ( $product instanceof WC_Product_Variable && empty( $product->get_default_attributes() ) ) {
						$price = 0;
					} else {
						$price = apply_filters( 'yith_wapo_convert_price', wc_get_price_to_display( $product ), true );
					}
					$totals_price_args    = apply_filters( 'yith_wapo_totals_price_args', array() );
					$display_product      = wc_price( $price, $totals_price_args ) . $product->get_price_suffix();
					$values['price_html'] = $display_product;
				}
			}
			wp_send_json( $values );
		}

		/**
		 * Get the default product price when variation is reset.
		 *
		 * @since  3.2.0
		 * @return array
		 */
		public function get_default_variation_price() {
			check_ajax_referer( 'addons-nonce', 'security' );
			$values = array( 'price_html' => '' );
			if ( isset( $_POST['product_id'] ) && $_POST['product_id'] ) {
				$product_id = $_POST['product_id'];
				$product    = wc_get_product( $product_id );
				if ( $product instanceof WC_Product ) {
					$product_price      = $product->get_price();
					$product_price_html = $product->get_price_html();

					$values['price_html']    = $product_price_html;
					$values['current_price'] = $product_price;

				}
			}
			wp_send_json( $values );
		}
	}

}

/**
 * Unique access to instance of YITH_WAPO_Front class
 *
 * @return YITH_WAPO_Front
 */
function YITH_WAPO_Front() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YITH_WAPO_Front::get_instance();
}
