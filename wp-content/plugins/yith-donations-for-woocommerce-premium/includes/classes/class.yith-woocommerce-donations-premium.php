<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WC_Donations_Premium' ) ) {

	class YITH_WC_Donations_Premium extends YITH_WC_Donations {
		protected static $_instance;
		protected $_email_types = array();

		public function __construct() {
			parent::__construct();

			$this->_include();

			$this->_email_types = array(
				'donation' => array(
					'class' => 'YITH_WC_Donations_Email',
					'file'  => 'class.ywcds-donations-email.php',
					'hide'  => false,
				)
			);

			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			//add premium tabs
			add_filter( 'ywcds_add_premium_tab', array( $this, 'add_premium_tab' ) );

			add_action( 'ywcds_product_donation', array( YWCDS_Product_Donation_Table(), 'output' ) );
			//add premium labels
			add_filter( 'ywcds_init_messages', array( $this, 'init_premium_messages' ) );

			//enqueue style and scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_premium_style_script' ), 20 );
			//set current available payement for donation
			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'set_donation_gateways' ) );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_to_cart' ), 10, 5 );
			add_filter( 'woocommerce_update_cart_validation', array( $this, 'update_cart_validation' ), 10, 4 );

			if ( get_option( 'ywcds_button_style_select' ) == 'custom' ) {
				add_action( 'wp_print_scripts', array( $this, 'get_custom_button_style' ) );
			}

			if ( get_option( 'ywcds_show_donation_in_cart' ) == 'yes' ) {
				add_action( 'woocommerce_before_cart_totals', array( $this, 'add_form_donation_in_cart' ) );
			}

			//Add button Donation in loop
			add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'add_donation_in_shop_loop' ), 10, 2 );
			add_filter( 'add_to_cart_text', array( $this, 'add_donation_in_shop_loop' ), 99, 2 );
			add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_url_donation_in_shop_loop' ), 10, 2 );
			add_filter( 'woocommerce_loop_add_to_cart_link', array(
				$this,
				'disable_ajax_add_to_cart_in_loop'
			), 10, 2 );


			// add a custom post meta at orders that have a donation
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'mark_order_as_donation' ) );


			//send email when a order with donation is completed
			add_action( 'woocommerce_order_status_completed', array( $this, 'prepare_email' ) );
			add_filter( 'woocommerce_email_classes', array( $this, 'ywcds_custom_email' ) );
			add_filter( 'woocommerce_get_sections_email', array( $this, 'ywcds_hide_sections' ) );

			add_action( 'wp_loaded', array( $this, 'remove_items_from_cart' ), 15 );

			//custom action for add custom email template
			add_action( 'ywcds_email_header', array( $this, 'ywcds_email_header' ) );
			add_action( 'ywcds_email_footer', array( $this, 'ywcds_email_footer' ) );


			add_filter( 'ywcds_donation_message', array( $this, 'print_donation_message' ), 20, 2 );

			add_filter( 'woocommerce_is_purchasable', array( $this, 'set_product_with_donation_purchasable' ), 99, 2 );

			if ( is_admin() ) {

				add_filter( 'views_edit-shop_order', array( $this, 'add_order_donation_view' ) );
				add_action( 'pre_get_posts', array( $this, 'filter_order_donation_for_view' ) );
				add_filter( 'manage_edit-shop_order_columns', array( $this, 'edit_columns' ), 99 );
				add_action( 'manage_shop_order_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
				add_filter( 'ywcds_custom_types', array( $this, 'add_premium_custom_types' ) );
				add_action( 'plugins_loaded', 'ywcds_add_gutenberg_block', 20 );
				add_action( 'current_screen', array( $this, 'ywcds_add_shortcodes_button' ), 20 );


			}

			add_filter( 'woocommerce_get_item_data', array( $this, 'add_donation_item_data' ), 20, 2 );
			add_action( 'woocommerce_checkout_create_order_line_item', array(
				$this,
				'add_custom_item_meta_checkout'
			), 20, 3 );
			add_filter( 'woocommerce_order_item_display_meta_key', array(
				$this,
				'change_order_item_display_meta_key'
			), 20, 2 );
			add_filter( 'woocommerce_order_item_display_meta_value', array(
				$this,
				'change_order_item_display_meta_key'
			), 20, 2 );

			add_filter( 'yith_ywraq_before_print_button', array( $this, 'check_if_donation_product'), 20, 2);
			add_filter( 'ywraq_hide_add_to_cart_single', array( $this, 'check_if_donation_product'), 20, 2);


		}

		/** return single instance of class
		 * @return YITH_WC_Donations
		 * @since 1.0.0
		 * @author YIThemes
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/** add tabs for premium version
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function add_premium_tab( $tabs ) {

			unset( $tabs['premium-landing'] );

			$tabs['product-donation'] = __( 'Products & Donations', 'yith-donations-for-woocommerce' );
			$tabs['messages']         = __( 'Labels', 'yith-donations-for-woocommerce' );
			$tabs['mail']             = __( 'Email', 'yith-donations-for-woocommerce' );

			return $tabs;
		}

		/* Register plugins for activation tab
		*
		* @return void
		* @since    1.0.0
		* @author   Andrea Grillo <andrea.grillo@yithemes.com>
		*/
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YWCDS_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YWCDS_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YWCDS_INIT, YWCDS_SECRET_KEY, YWCDS_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once( YWCDS_DIR . 'plugin-fw/lib/yit-upgrade.php' );
			}
			YIT_Upgrade()->register( YWCDS_SLUG, YWCDS_INIT );
		}

		/**include custom field for plugin option
		 * @author YIThemes
		 * @since 1.0.0
		 */
		private function _include() {


			include_once( YWCDS_TEMPLATE_PATH . 'admin/product-donation-table.php' );

		}

		/**include premium style and script admin
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function enqueue_premium_style_script() {

			if ( isset( $_GET['page'] ) && 'yith_wc_donations' == $_GET['page'] ) {
				wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $this->_suffix . '.js', array(
					'jquery',
					'jquery-blockui',
					'jquery-ui-sortable',
					'jquery-ui-widget',
					'jquery-ui-core',
					'jquery-tiptip'
				) );
				wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $this->_suffix . '.js', array( 'jquery' ), false, true );

				wp_enqueue_script( 'woocommerce_admin' );
				wp_enqueue_script( 'ywcds_premium_admin', YWCDS_ASSETS_URL . 'js/ywcds_premium_admin' . $this->_suffix . '.js', array(
					'jquery',
					'jquery-ui-datepicker'
				), YWCDS_VERSION, true );
				wp_enqueue_style( 'ywcds_premium_admin_style', YWCDS_ASSETS_URL . 'css/ywcds_premium_admin.css', array(), YWCDS_VERSION );

			}

			$screen = get_current_screen();

			if( !is_null( $screen ) && 'product' == $screen->post_type ){

			    global $post;

			    $donation_id = $this->get_donation_id();

			    if( !is_null( $post ) && $donation_id == $post->ID ) {
				    $css = "
			       .options_group.pricing{
			            display:none!important;
			       }";

				    wp_add_inline_style('woocommerce_admin_styles', $css );
			    }
            }
		}

		/**show donation form in cart
		 * @author YIThemes
		 * @since 1.0.0
		 *
		 */
		public function add_form_donation_in_cart() {

			$ajax_cart_en = 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' );
			$ajax_class   = $ajax_cart_en ? 'ywcds_ajax_add_donation' : '';
			$ajax_class   = apply_filters( 'ywcds_ajax_class', $ajax_class );
			$donation_id  = $this->get_donation_id();
			$args         = array(
				'message_for_donation' => get_option( 'ywcds_message_for_donation' ),
				'button_class'         => 'ywcds_add_donation_product button alt ' . $ajax_class,
				'product_id'           => $donation_id,
				'button_text'          => $this->_messages['text_button'],
			);

			$multi_donation_amounts  = get_option( 'ywcds_multi_amount', '' );
			$show_donation_reference = get_option( 'ywcds_show_donation_reference', 'no' );

			if ( ! empty( $multi_donation_amounts ) ) {

				$args['donation_amount']       = $multi_donation_amounts;
				$args['donation_amount_style'] = get_option( 'ywcds_multi_amount_style', 'label' );
			}

			if ( 'yes' === $show_donation_reference ) {

				$args['show_extra_desc']  = 'on';
				$args['extra_desc_label'] = get_option( 'ywcds_text_extra_field', __( 'Why you are making a donation', 'yith-donations-for-woocommerce' ) );
			}


			wc_get_template( 'add-donation-form-widget.php', $args, '', YWCDS_TEMPLATE_PATH );
		}

		/** add premium label for frontend
		 *
		 * @param $messages
		 *
		 * @return array
		 * @author YIThemes
		 * @since 1.0.0
		 * @use ywcds_init_messages
		 *
		 */
		public function init_premium_messages( $messages ) {
			$messages = array(
				'no_number'   => get_option( 'ywcds_message_invalid_donation' ),
				'empty'       => get_option( 'ywcds_message_empty_donation' ),
				'success'     => get_option( 'ywcds_message_right_donation' ),
				'min_don'     => str_replace( '{min_donation}', wc_price( ywcds_get_min_donation() ), get_option( 'ywcds_message_min_donation' ) ),
				'max_don'     => str_replace( '{max_donation}', wc_price( ywcds_get_max_donation() ), get_option( 'ywcds_message_max_donation' ) ),
				'text_button' => get_option( 'ywcds_button_text' ),
				'obligatory'  => get_option( 'ywcds_message_obligatory_donation' ),
				'negative'    => get_option( 'ywcds_message_negative_donation' )
			);


			return $messages;
		}

		/**print from donation, in single page product/s
		 *
		 * method overidden
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function add_form_donation() {
			global $product;

			$product_id = $product->get_id();

			$is_associate  = $product->get_meta( '_ywcds_donation_associate' ) == 'yes';
			$is_obligatory = $product->get_meta( '_ywcds_donation_obligatory' ) == 'yes';

			if ( $is_associate ) {

				$args = array(
					'message_for_donation'      => get_option( 'ywcds_message_for_donation' ),
					'make_donation_placeholder' => get_option( 'ywcds_message_empty_donation', __( 'Please enter amount', 'yith-donations-for-woocommerce' ) ),
					'product_id'                => $product_id,
					'is_obligatory'             => $is_obligatory ? 'true' : 'false',
					'min_don'                   => ywcds_get_min_donation(),
					'max_don'                   => ywcds_get_max_donation(),
				);
				wc_get_template( 'add-donation-form-single-product.php', $args, '', YWCDS_TEMPLATE_PATH );
			}
		}


		/**
		 * overridden, mange add to cart for donation
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function add( $product_id, $variation_id = - 1, $quantity = 1, $amount ) {

			if ( ! empty( $amount ) ) {


				$amount = ywcds_format_number( $amount );

				if ( ! is_numeric( $amount ) ) {
					return 'no_number';
				}

				$amount = floatval( $amount );

				if ( $amount != null && $amount > 0 ) {

					$min = ywcds_get_min_donation();

					$min = empty( $min ) ? '' : $min;
					$max = ywcds_get_max_donation();
					$max = empty( $max ) ? '' : $max;


					if ( $min != '' && $amount < floatval( $min ) ) {
						return 'min_don';
					} elseif ( $max != '' && $amount > floatval( $max ) ) {
						return 'max_don';
					} else {

						$donation_id = $this->get_donation_id();

						$extra_info_label = isset( $_REQUEST['ywcds_show_extra_info_label'] ) ? stripslashes( $_REQUEST['ywcds_show_extra_info_label'] ) : '';
						$cart_item_data   = apply_filters( 'ywcds_add_cart_item_data', array(
								'ywcds_amount'                => $amount,
								'ywcds_product_id'            => $product_id != $donation_id ? $product_id : - 1,
								'ywcds_variation_id'          => $variation_id,
								'ywcds_data_added'            => date( "Y-m-d H:i:s" ),
								'ywcds_quantity'              => $quantity,
								'ywcds_extra_info'            => isset( $_REQUEST['ywcds_show_extra_info'] ) ? $_REQUEST['ywcds_show_extra_info'] : '',
								'ywcds_show_extra_info_label' => $extra_info_label,
							)
						);

						remove_action( 'woocommerce_add_to_cart', array( $this, 'add_donation_single_product' ), 25 );

						$res = WC()->cart->add_to_cart( $donation_id, 1, '', array(), $cart_item_data );

						add_action( 'woocommerce_add_to_cart', array( $this, 'add_donation_single_product' ), 25 );

						if ( $product_id != $donation_id && apply_filters( 'ywcds_show_success_message', true ) ) {
							wc_add_notice( $this->get_message( 'success' ) );
						}

						return 'true';
					}
				} else {
					return 'negative';
				}
			} else {
				return 'empty';
			}
		}

		/**overridden, manage add donation in single product page
		 * @author YIThemes
		 * @since 1.0.0
		 *
		 */
		public function add_donation_single_product() {

			if ( isset( $_REQUEST['amount_single_product'] ) && $_REQUEST['amount_single_product'] != '' && isset( $_REQUEST['add-to-cart'] ) ) {

				$product_id   = $_REQUEST['add-to-cart'];
				$variation_id = isset( $_REQUEST['variation_id'] ) ? $_REQUEST['variation_id'] : '';
				$quantity     = isset( $_REQUEST['quantity'] ) ? $_REQUEST['quantity'] : 1;
				$amount       = $_REQUEST['amount_single_product'];
				$res          = $this->add( $product_id, $variation_id, $quantity, $amount );

			}
		}


		/**call ajax for add a donation in cart
		 * this method is overridden
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function ywcds_add_donation_ajax() {

			if ( isset( $_REQUEST['add_donation_to_cart'] ) ) {
				$product_id = $_REQUEST['add_donation_to_cart'];
				$amount     = $_REQUEST['ywcds_amount'];
				$result     = $this->add( $product_id, '', 1, $amount );
				$message    = '';

				switch ( $result ) {

					case 'no_number':
						$message = $this->_messages['no_number'];
						break;

					case 'empty':
						$message = $this->_messages['empty'];
						break;

					case 'negative':
						$message = $this->_messages['negative'];
						break;

					case 'min_don'  :
						$message = $this->_messages['min_don'];
						break;
					case 'max_don'  :
						$message = $this->_messages['max_don'];
						break;

					default :
						$message = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_page_permalink( 'cart' ), __( 'View Cart', 'woocommerce' ), $this->_messages['success'] );
						break;
				}

				if ( $result == 'true' ) {
					WC_AJAX::get_refreshed_fragments();
				} else {
					wp_send_json(
						array(
							'result'  => $result,
							'message' => $message
						)
					);
				}
			}
		}

		/**
		 * print the button style in frontend
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function get_custom_button_style() {

			$button_typ = get_option( 'ywcds_button_typography' );

			$button_text_size   = $button_typ['size'] . $button_typ['unit'];
			$button_text_transf = $button_typ['transform'];
			$button_text_style  = '';
			$button_text_weight = '';

			switch ( $button_typ['style'] ) {

				case 'bold' :
					$button_text_style  = 'normal';
					$button_text_weight = '700';
					break;

				case 'extra-bold' :
					$button_text_style  = 'normal';
					$button_text_weight = '800';
					break;

				case 'italic' :
					$button_text_style  = 'italic';
					$button_text_weight = 'normal';
					break;

				case 'bold-italic' :
					$button_text_style  = 'italic';
					$button_text_weight = '700';
					break;
				case 'regular' :
				case 'normal' :
					$button_text_style  = 'normal';
					$button_text_weight = '400';
					break;

				default:
					if ( is_numeric( $button_typ['style'] ) ) {
						$button_text_style  = 'normal';
						$button_text_weight = $button_typ['style'];
					} else {
						$button_text_style  = 'italic';
						$button_text_weight = str_replace( 'italic', '', $button_typ['style'] );
					}
					break;
			}

			$button_text_color     = get_option( 'ywcds_text_color' );
			$button_text_color_hov = get_option( 'ywcds_text_hov_color' );

			$button_bg_color     = get_option( 'ywcds_bg_color' );
			$button_bg_color_hov = get_option( 'ywcds_bg_hov_color' );

			?>
            <style type="text/css">


                .ywcds_form_container .ywcds_button_field .ywcds_submit_widget, .woocommerce a.button.product_type_donation {

                    color: <?php echo $button_text_color;?> !important;
                    font-size: <?php echo $button_text_size;?> !important;
                    font-style: <?php echo $button_text_style;?> !important;
                    font-weight: <?php echo $button_text_weight;?> !important;
                    text-transform: <?php echo $button_text_transf;?> !important;
                    background-color: <?php echo $button_bg_color;?> !important;
                }

                .ywcds_form_container .ywcds_button_field .ywcds_submit_widget:hover, .woocommerce a.button.product_type_donation:hover {

                    color: <?php echo $button_text_color_hov;?> !important;
                    background-color: <?php echo $button_bg_color_hov;?> !important;
                }
            </style>

			<?php
		}

		/**set gateways in donation orders
		 *
		 * @param $gateways
		 *
		 * @return mixed
		 * @author YIThemes
		 * @since 1.0.0
		 *
		 */
		public function set_donation_gateways( $gateways ) {

			if ( $this->is_donation_in_cart() ) {

				$donation_payments = get_option( 'ywcds_select_gateway' );

				if ( ! empty( $donation_payments ) ) {

					foreach ( $gateways as $key => $gateway ) {
						if ( ! in_array( $key, $donation_payments ) ) {
							unset( $gateways[ $key ] );
						}
					}
				}
			}

			return $gateways;
		}

		/** check is a donation is in cart
		 * @return boolean
		 * @since 1.0.0
		 * @author YIThemes
		 */
		public function is_donation_in_cart() {

			$cart = WC()->cart;

			if ( ! empty( $cart ) && $cart instanceof WC_Cart && sizeof( $cart->get_cart() ) > 0 ) {
				$donation_id = $this->get_donation_id();
				foreach ( $cart->get_cart() as $cart_item_key => $values ) {
					$product = $values['data'];
					/**
					 * @var WC_Product $product
					 */
					$product_id = $product->get_id();
					if ( $donation_id == $product_id ) {
						return true;
					}
				}

				return false;
			}

			return false;
		}

		/**check is custom query, ?add-to-cart= is valid
		 * @return boolean
		 * @author YIThemes
		 * @since 1.0.0
		 * @use woocommerce_add_to_cart_validation
		 */
		public function validate_add_to_cart( $is_valid, $product_id, $quantity, $variation_id = '', $variations = '' ) {

			$min = ywcds_get_min_donation();
			$max = ywcds_get_max_donation();


			$min = empty( $min ) ? '' : floatval( $min );
			$max = empty( $max ) ? '' : floatval( $max );

			$donation_id = $this->get_donation_id();
			//if product is a donation
			if ( $product_id == $donation_id ) {

				if ( ( isset( $_REQUEST['add_donation_to_cart'] ) && ! isset( $_REQUEST['add-to-cart'] ) ) || ( isset( $_REQUEST['amount_single_product'] ) && isset( $_REQUEST['add-to-cart'] ) ) ) {

					if ( isset( $_REQUEST['ywcds_amount'] ) && ! empty( $_REQUEST['ywcds_amount'] ) ) {

						$amount = $_REQUEST['ywcds_amount'];

						if ( ! is_numeric( $amount ) ) {
							wc_add_notice( $this->get_message( 'no_number' ), 'error' );

							return false;
						} elseif ( $min != '' && $amount < $min ) {
							wc_add_notice( $this->get_message( 'min_don' ), 'error' );

							return false;
						} elseif ( $max != '' && $amount > $max ) {
							wc_add_notice( $this->get_message( 'max_don' ), 'error' );

							return false;
						} else {
							return true;
						}
					}
				} else {
					wc_add_notice( __( 'Invalid Add to cart', 'yith-donations-for-woocommerce' ), 'error' );

					return false;
				}
			} //if is associate product
			else {
				$product       = wc_get_product( $product_id );
				$is_associate  = $product->get_meta( '_ywcds_donation_associate' ) == 'yes';
				$is_obligatory = $product->get_meta( '_ywcds_donation_obligatory' ) == 'yes';

				if ( ! $is_associate ) {
					return $is_valid;
				}

				if ( isset( $_REQUEST['amount_single_product'] ) && ! empty( $_REQUEST['amount_single_product'] ) ) {

					$amount = ywcds_format_number( $_REQUEST['amount_single_product'] );

					if ( ! is_numeric( $amount ) ) {
						wc_add_notice( $this->get_message( 'no_number' ), 'error' );

						return false;
					} elseif ( $min != '' && ( $amount * 1 ) < $min ) {
						wc_add_notice( $this->get_message( 'min_don' ), 'error' );

						return false;
					} elseif ( $max != '' && ( $amount * 1 ) > $max ) {
						wc_add_notice( $this->get_message( 'max_don' ), 'error' );

						return false;
					} else {
						return true;
					}
				} elseif ( $is_obligatory ) {

					wc_add_notice( $this->get_message( 'obligatory' ), 'error' );

					return false;

				} else {
					return true;
				}
			}
		}

		/**check is the update is valid
		 *
		 * @param $is_valid
		 * @param $cart_item_key
		 * @param $values
		 * @param $quantity
		 *
		 * @return bool
		 * @since 1.0.0
		 *
		 * @author YIThemes
		 */
		public function update_cart_validation( $is_valid, $cart_item_key, $values, $quantity ) {
			$donation_id = $this->get_donation_id();
			if ( $values['product_id'] == $donation_id && $values['ywcds_product_id'] != - 1 ) {

				wc_add_notice( __( 'You can\'t update the cart', 'yith-donations-for-woocommerce' ), 'error' );
				$is_valid = false;
			} else {
				$is_associate  = get_post_meta( $values['product_id'], '_ywcds_donation_associate', true ) == 'yes';
				$is_obligatory = get_post_meta( $values['product_id'], '_ywcds_donation_obligatory', true ) == 'yes';

				if ( $is_associate && $is_obligatory ) {

					wc_add_notice( __( 'You can\'t update the cart', 'yith-donations-for-woocommerce' ), 'error' );
					$is_valid = false;
				}

			}

			return $is_valid;
		}

		/**check if order has donation and set custom post meta
		 *
		 * @param int $order_id
		 *
		 */
		public function mark_order_as_donation( $order_id ) {

			$order       = wc_get_order( $order_id );
			$donation_id = $this->get_donation_id();
			foreach ( $order->get_items() as $item ) {

				if ( $item['product_id'] == $donation_id ) {

					 $order->update_meta_data( '_ywcds_order_has_donation', 'true' );
					 $order->save();

					break;
				}
			}
		}

		/**
		 * Add the YITH_WC_Donations_Email class to WooCommerce mail classes
		 *
		 * @param   $email_classes
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywcds_custom_email( $email_classes ) {

			foreach ( $this->_email_types as $type => $email_type ) {

				$email_classes[ $email_type['class'] ] = include( YWCDS_INC . "/emails/{$email_type['file']}" );
			}

			return $email_classes;
		}

		/**
		 * Hides custom email settings from WooCommerce panel
		 *
		 * @param   $sections
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Andrea Grillo
		 */
		public function ywcds_hide_sections( $sections ) {
			foreach ( $this->_email_types as $type => $email_type ) {
				$class_name = strtolower( $email_type['class'] );
				if ( isset( $sections[ $class_name ] ) && $email_type['hide'] == true ) {
					unset( $sections[ $class_name ] );
				}
			}

			return $sections;
		}

		/**
		 * Get the email header.
		 *
		 * @param   $email_heading
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywcds_email_header( $email_heading ) {
			switch ( get_option( 'ywcds_mail_template' ) ) {
				case 'ywcds_template':
					wc_get_template( 'emails/donations-template/email-header.php', array( 'email_heading' => $email_heading ), YWCDS_TEMPLATE_PATH, YWCDS_TEMPLATE_PATH );
					break;
				default:
					wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
			}
		}

		/**
		 * Get the email footer.
		 *
		 * @param   $unsubscribe
		 *
		 * @return  void
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywcds_email_footer() {
			switch ( get_option( 'ywcds_mail_template' ) ) {
				case 'ywcds_template':
					wc_get_template( 'emails/donations-template/email-footer.php', array(), YWCDS_TEMPLATE_PATH, YWCDS_TEMPLATE_PATH );
					break;

				default:
					wc_get_template( 'emails/email-footer.php' );
			}
		}

		/** Send email for order donations
		 *
		 * @param $order_id
		 *
		 * @since 1.0.0
		 *
		 * @author YIThemes
		 */
		public function prepare_email( $order_id ) {

			$is_order_donation = get_post_meta( $order_id, '_ywcds_order_has_donation', true );
			if ( $is_order_donation ) {

				$donation_list = ywcds_get_donations_item( $order_id );
				$wc_email      = WC_Emails::instance();
				$email         = $wc_email->emails['YITH_WC_Donations_Email'];
				$email->trigger( $order_id, $donation_list );

			}
		}


		/**
		 *Add shortcode button to TinyMCE editor, adding filter on mce_external_plugins
		 *
		 * @param WP_Screen $current_screen
		 *
		 * @since 1.0.0
		 * @use admin_init
		 *
		 * @author YIThemes
		 */
		public function ywcds_add_shortcodes_button( $current_screen ) {

			if ( ! is_null( $current_screen ) && ! in_array( $current_screen->post_type, array( 'post', 'page' ) ) ) {
				if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
					return;
				}
				if ( get_user_option( 'rich_editing' ) == 'true' ) {

					add_filter( 'mce_external_plugins', array( $this, 'ywcds_add_shortcodes_tinymce_plugin' ) );
					add_filter( 'mce_buttons', array( $this, 'ywcds_register_shortcodes_button' ) );
					add_action( 'media_buttons_context', array( $this, 'ywcds_media_buttons_context' ) );
					add_action( 'admin_print_footer_scripts', array( $this, 'ywcds_add_quicktags' ) );
					add_filter( 'mce_external_languages', array( $this, 'add_button_lang' ) );

				}
			}
		}

		/**
		 * Add a script to TinyMCE script list
		 *
		 * @param   $plugin_array
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywcds_add_shortcodes_tinymce_plugin( $plugin_array ) {

			$plugin_array['ywcds_shortcode'] = YWCDS_ASSETS_URL . 'js/ywcds-tinymce' . $this->_suffix . '.js';


			return $plugin_array;
		}

		/**
		 * Make TinyMCE know a new button was included in its toolbar
		 *
		 * @param   $buttons
		 *
		 * @return  array()
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywcds_register_shortcodes_button( $buttons ) {

			array_push( $buttons, "|", "ywcds_shortcode" );

			return $buttons;

		}

		/**
		 * The markup of shortcode
		 *
		 * @param   $context
		 *
		 * @return  mixed
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywcds_media_buttons_context( $context ) {

			$out = '<a id="ywcds_shortcode" style="display:none" href="#" class="hide-if-no-js" title="' . __( 'Add YITH Donations for WooCommerce  Form', 'yith-donations-for-woocommerce' ) . '"></a>';

			return $context . $out;

		}

		/**
		 * Add quicktags to visual editor
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function ywcds_add_quicktags() {


			?>
            <script type="text/javascript">

                if (window.QTags !== undefined) {
                    QTags.addButton('ywcds_shortcode', 'add donation form', function () {

                        var str = '[yith_wcds_donations]',
                            win = window.dialogArguments || opener || parent || top;

                        win.send_to_editor(str);
                        var ed;
                        if (typeof tinyMCE != 'undefined' && (ed = tinyMCE.activeEditor) && !ed.isHidden()) {
                            ed.setContent(ed.getContent());
                        }

                    });
                }

            </script>
			<?php
		}

		/**
		 * Add multilingual to mce button from filter mce_external_languages
		 *
		 * @param array $locales
		 *
		 * @return   array
		 * @since    1.1.0
		 * @author   Salvatore Strano
		 *
		 */
		function add_button_lang( $locales ) {
			$locales ['ywcds_shortcode'] = YWCDS_INC . 'admin/tinymce/tinymce-plugin-langs.php';

			return $locales;
		}

		/**
		 * register premium widget
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function register_donations_widget() {

			parent::register_donations_widget();

			register_widget( 'YITH_Donations_Summary_Widget' );
		}


		/**hide add to car single if product is free
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function hide_add_to_cart() {

			global $product;

			$hide_is_free = get_option( 'ywcds_hide_add_cart_in_free' ) == 'yes';

			if ( $hide_is_free && $this->is_free( $product ) ) {

				$priority = has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart' );

				if ( $priority != false ) {

					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', $priority );
				}
			}
		}


		/**remove product and donation from cart
		 * @author YIThemes
		 * @since 1.0.0
		 *
		 */
		public function remove_items_from_cart() {

			if ( isset( $_REQUEST['remove_item'] ) ) {

				$cart_item_key = sanitize_text_field( $_REQUEST['remove_item'] );
				$cart_item     = WC()->cart->get_cart_item( $cart_item_key );
				$product_id    = isset( $cart_item['product_id'] ) ? $cart_item['product_id'] : false;
				$donation_id   = $this->get_donation_id();

				if ( $product_id && $donation_id == $product_id ) {

					$product_ass_id = ! empty( $cart_item['ywcds_variation_id'] ) ? $cart_item['ywcds_variation_id'] : $cart_item['ywcds_product_id'];
					$product        = wc_get_product( $cart_item['ywcds_product_id'] );
					$is_obligatory  =  $product->get_meta( '_ywcds_donation_obligatory', true ) == 'yes';
					if ( $product_ass_id != - 1 && $is_obligatory ) {

						$product                       = wc_get_product( $product_ass_id );
						$quantity_product_for_donation = $cart_item['ywcds_quantity'];

						$cart_item_key_ass = $this->get_cart_item_key( WC()->cart, $cart_item['ywcds_product_id'], $cart_item['ywcds_variation_id'] );


						$cart_item_ass = WC()->cart->get_cart_item( $cart_item_key_ass );
						$quantity      = $cart_item_ass['quantity'];

						$new_quantity = $quantity - $quantity_product_for_donation;


						WC()->cart->cart_contents[ $cart_item_key_ass ]['quantity'] = $new_quantity;

						$message = ywcds_get_product_donation_title( $product );
						WC()->cart->remove_cart_item( $cart_item_key );
						wc_add_notice( $message );
						$cart_url = version_compare( WC()->version, '3.0.0', '>=' ) ? wc_get_cart_url() : WC()->cart->get_cart_url();
						$referer  = wp_get_referer() ? remove_query_arg( array(
							'remove_item',
							'add-to-cart',
							'added-to-cart',
							'_wpnonce'
						) ) : $cart_url;
						wp_safe_redirect( $referer );
						exit;
					}

				} else {

					$is_associate = get_post_meta( $product_id, '_ywcds_donation_associate', true ) == 'yes';

					$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $product_id;

					if ( $product_id && $is_associate ) {

						foreach ( WC()->cart->get_cart() as $cart_item_key_ass => $value ) {

							if ( $value['product_id'] == $donation_id ) {

								$product_ass_id = ! empty( $value['ywcds_variation_id'] ) ? $value['ywcds_variation_id'] : $value['ywcds_product_id'];

								if ( $product_ass_id == $product_id ) {
									WC()->cart->remove_cart_item( $cart_item_key_ass );
								}
							}
						}

						$product = wc_get_product( $product_id );
						$message = sprintf( __( '%s and all donations have been removed', 'ywcs' ), $product->get_title() );
						WC()->cart->remove_cart_item( $cart_item_key );
						$cart_url = version_compare( WC()->version, '3.0.0', '>=' ) ? wc_get_cart_url() : WC()->cart->get_cart_url();
						wc_add_notice( $message );
						$referer = wp_get_referer() ? remove_query_arg( array(
							'remove_item',
							'add-to-cart',
							'added-to-cart',
							'_wpnonce'
						) ) : $cart_url;
						wp_safe_redirect( $referer );
						exit;


					}
				}
			}

		}

		/**get the right cart item key
		 *
		 * @param $cart
		 * @param $product_id
		 * @param string $variation_id
		 *
		 * @return bool|int|string
		 * @author YIThemes
		 * @since 1.0.0
		 *
		 */
		public function get_cart_item_key( $cart, $product_id, $variation_id = '' ) {
			/**@var $cart WC_Cart */


			foreach ( $cart->get_cart() as $cart_item_key => $value ) {
				if ( $variation_id != '' ) {
					if ( $value['variation_id'] == $variation_id ) {
						return $cart_item_key;
					}
				} elseif ( $value['product_id'] == $product_id ) {
					return $cart_item_key;
				}
			}

			return false;
		}

		/**
		 * @author YIThemes
		 * @since 1.0.4
		 * get add to cart text for name your price product
		 */
		public function add_donation_in_shop_loop( $text, $product = null ) {

			if ( ! isset( $product ) ) {
				global $product;
			}
			$is_associate  = $product->get_meta( '_ywcds_donation_associate' ) == 'yes';
			$is_obligatory =  $product->get_meta( '_ywcds_donation_obligatory' ) == 'yes';

			if ( $is_associate && $is_obligatory ) {

				return get_option( 'ywcds_button_text' );
			} else {
				return $text;
			}


		}

		/**
		 * @param $url
		 * @param WC_Product $product
		 *
		 * @return false|string
		 * @since 1.0.4
		 *
		 * @author YITH
		 */
		public function add_url_donation_in_shop_loop( $url, $product ) {

			$product_id    =  $product->get_id();
			$is_associate  =  $product->get_meta( '_ywcds_donation_associate' ) == 'yes';
			$is_obligatory = $product->get_meta( '_ywcds_donation_obligatory' ) == 'yes';

			if ( $is_associate && $is_obligatory ) {
				return $product->get_permalink();
			} else {
				return $url;
			}
		}

		/**
		 * @param $button_html
		 * @param WC_Product $product
		 *
		 * @return mixed
		 * @since 1.0.4
		 *
		 * @author YITH
		 */
		public function disable_ajax_add_to_cart_in_loop( $button_html, $product ) {

			$is_associate  = $product->get_meta('_ywcds_donation_associate' ) == 'yes';
			$is_obligatory = $product->get_meta( '_ywcds_donation_obligatory' ) == 'yes';

			if ( $is_associate && $is_obligatory ) {

				if ( version_compare( WC()->version, '2.5.0', '>=' ) ) {
					$button_html = str_replace( 'ajax_add_to_cart', '', $button_html );
				}

				return str_replace( array(
					'product_type_simple',
					'product_type_variable'
				), 'product_type_donation', $button_html );
			} else {
				return $button_html;
			}

		}

		public function print_donation_message( $message, $result ) {

			if ( 'no_number' == $result ) {
				$message['message'] = $this->get_message( 'no_number' );
				$message['type']    = 'error';
			} elseif ( 'min_don' == $result ) {
				$message['message'] = $this->get_message( 'min_don' );
				$message['type']    = 'error';
			} elseif ( 'max_don' == $result ) {
				$message['message'] = $this->get_message( 'max_don' );
				$message['type']    = 'error';
			}

			return $message;
		}


		/**
		 * add custom view in order table
		 *
		 * @param $views
		 *
		 * @return mixed
		 * @author YITHEMES
		 * @since 1.0.13
		 *
		 */
		public function add_order_donation_view( $views ) {

			$tot_order = $this->count_order_donation();

			if ( $tot_order > 0 ) {
				$filter_url   = esc_url( add_query_arg( array(
					'post_type'           => 'shop_order',
					'ywcd_order_donation' => true
				), admin_url( 'edit.php' ) ) );
				$filter_class = isset( $_GET['ywcd_order_donation'] ) ? 'current' : '';

				$views['ywcd_order_donation'] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>', $filter_url, $filter_class, __( 'Donations', 'yith-donations-for-woocommerce' ), $tot_order );
			}

			return $views;
		}

		/**
		 * customize query
		 * @author YITHEMES
		 * @since 1.0.13
		 */
		public function filter_order_donation_for_view() {

			if ( isset( $_GET['ywcd_order_donation'] ) && $_GET['ywcd_order_donation'] ) {
				add_filter( 'posts_join', array( $this, 'filter_order_join_for_view' ) );
				add_filter( 'posts_where', array( $this, 'filter_order_where_for_view' ) );
			}
		}

		/**
		 * add joins to order view query
		 *
		 * @param $join
		 *
		 * @return string
		 * @author YITHEMES
		 * @since 1.0.13
		 *
		 */
		public function filter_order_join_for_view( $join ) {

			global $wpdb;

			$join .= " LEFT JOIN {$wpdb->prefix}postmeta as pm ON {$wpdb->posts}.ID = pm.post_id";

			return $join;
		}

		/**
		 * Add conditions to order view query
		 *
		 * @param $where string Original where query section
		 *
		 * @return string filtered where query section
		 * @author YITHEMES
		 * @since 1.0.13
		 *
		 */
		public function filter_order_where_for_view( $where ) {
			global $wpdb;

			$where .= $wpdb->prepare( " AND pm.meta_key = %s AND pm.meta_value = %s", array(
				'_ywcds_order_has_donation',
				'true'
			) );

			return $where;
		}


		/**
		 * count order with donation
		 * @return int
		 * @since 1.0.13
		 * @author YITHEMES
		 */
		public function count_order_donation() {
			global $wpdb;

			$order_status = array_keys( wc_get_order_statuses() );

			$query        = $wpdb->prepare(
				"SELECT DISTINCT COUNT(*) 
                           FROM {$wpdb->posts} INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
                           WHERE {$wpdb->posts}.post_type IN ('" . implode( "','", wc_get_order_types() ) . "') AND 
                                {$wpdb->posts}.post_status IN ('" . implode( "','", $order_status ) . "') AND
                                ( {$wpdb->postmeta}.meta_key=%s AND {$wpdb->postmeta}.meta_value = %s )",
				'_ywcds_order_has_donation', 'true' );
			$result       = $wpdb->get_var( $query );


			return $result;
		}


		/**
         * @author YITH
		 * @param bool $purchasable
		 * @param WC_Product $product
         * @return bool
		 */
		public function set_product_with_donation_purchasable( $purchasable, $product ) {

			$is_associate = $product->get_meta( '_ywcds_donation_associate' ) == 'yes';

			return $is_associate ? true : $purchasable;
		}

		/**
		 * add a column in the order table
		 *
		 * @param $columns
		 *
		 * @return array
		 * @author Salvatore Strano
		 * @since 1.0.15
		 *
		 */
		public function edit_columns( $columns ) {

			$date = $columns['order_date'];
			unset( $columns['order_date'] );
			$columns['donation_total'] = __( 'Donation Total', 'yith-donations-for-woocommerce' );

			$columns['order_date'] = $date;

			return $columns;
		}

		/**
		 * show the donation total
		 *
		 * @param $column
		 * @param $post_id
		 *
		 * @author Salvatore Strano
		 * @since 1.0.15
		 *
		 */
		public function custom_columns( $column, $post_id ) {

			if ( 'donation_total' == $column ) {
				$donation_item = ywcds_get_donations_item( $post_id );

				$donation_total = 0;

				if ( is_array( $donation_item ) ) {
					foreach ( $donation_item as $item ) {

						$donation_total += $item['total'];
					}
				}

				echo wc_price( $donation_total );

			}
		}

		/**
		 * add custom field in wc panel
		 *
		 * @param array $types
		 *
		 * @return array
		 * @author Salvatore Strano
		 *
		 */
		public function add_premium_custom_types( $types ) {

			$types[] = 'donation-button-typography';

			return $types;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 * @param  $init_file
		 *
		 * @return   array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWCDS_INIT' ) {
			$new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}


		/**
		 * @param array $item_data
		 * @param array $cart_item
		 *
		 * @return array
		 * @since 1.1.0
		 *
		 * @author Salvatore Strano
		 */
		public function add_donation_item_data( $item_data, $cart_item ) {

			if ( ! empty( $cart_item['ywcds_extra_info'] ) ) {

				if ( empty( $item_data ) ) {
					$item_data = array();
				}

				if ( ! empty( $cart_item['ywcds_show_extra_info_label'] ) ) {

					$value = '<strong>' . $cart_item['ywcds_show_extra_info_label'] . ':</strong><br>';
				}
				$value .= $cart_item['ywcds_extra_info'];

				$item_data[] = array(
					'key'   => 'ywcds_extra_info',
					'value' => $value,
				);
			}

			return $item_data;
		}

		/**
		 * @param WC_Order_Item_Product $item_product
		 * @param $key
		 * @param $cart_item
		 *
		 * @since 1.1.0
		 *
		 * @author Salvatore Strano
		 */
		public function add_custom_item_meta_checkout( $item_product, $key, $cart_item ) {

			if ( ! empty( $cart_item['ywcds_extra_info'] ) ) {

				if ( ! empty( $cart_item['ywcds_show_extra_info_label'] ) ) {

					$value = '<strong>' . $cart_item['ywcds_show_extra_info_label'] . ':</strong>';
				}
				$value .= $cart_item['ywcds_extra_info'];

				$item_product->add_meta_data( 'ywcds_extra_info', $value );
			}
		}

		/**
		 * @param string $display_key
		 * @param WC_Meta_Data $meta
		 *
		 * @return string
		 */
		public function change_order_item_display_meta_key( $display_key, $meta ) {

			if ( 'ywcds_extra_info' == $meta->key ) {

				$meta_value = str_replace( array( '<strong>', '</strong>' ), '', $meta->value );
				$meta_value = explode( ':', $meta_value );
				if ( 'woocommerce_order_item_display_meta_key' == current_action() ) {

					$display_key = $meta_value[0];

				} else {
					$display_key = $meta_value[1];
				}
			}

			return $display_key;
		}

		/**
		 * @param boolean $show
		 * @param WC_Product $product
         * @return  boolean
		 */
		public function check_if_donation_product( $show, $product ){

			$is_associate  = $product->get_meta('_ywcds_donation_associate' ) == 'yes';

			if( $is_associate ){

			    if('ywraq_hide_add_to_cart_single' == current_filter() ){
			        $show = true;
                }else {
				    $show = false;
			    }
            }
			return $show;
        }
	}

}