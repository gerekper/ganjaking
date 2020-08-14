<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YITH_YWGC_Frontend_Premium' ) ) {

	/**
	 *
	 * @class   YITH_YWGC_Frontend_Premium
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_YWGC_Frontend_Premium extends YITH_YWGC_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		public $popup = null;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
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
			parent::__construct ();

			/**
			 * Permit to enter a manual gift card amount
			 */
			add_action ( 'yith_gift_card_amount_selection_last_option', array( $this, 'show_manual_amount_area' ) );

			/**
			 * Add the input hidden to set if gift this product automatically
			 */
			add_action ( 'woocommerce_after_add_to_cart_button', array( $this, 'show_give_as_present_link_simple' ) );

			/**
			 * Let the customer choose if disable the gift this product option
			 */
			add_action ( 'woocommerce_after_add_to_cart_button', array( $this, 'yith_wcgc_display_input_hidden_disable_gift_this_product' ) );

			/**
			 * Let the customer to use a product of type WC_Product_Variable  as source for a gift card
			 */
			add_action ( 'woocommerce_after_variations_form', array( $this, 'show_give_as_present_link_variable' ), 99 );

			/**
			 * Integration with yith woocommerce product bundle
			 * Let the customer to use a product of type WC_Product_Yith_Bundle  as source for a gift card
			 */
			add_action ( 'woocommerce_after_add_to_cart_button', array( $this, 'show_give_as_present_link_product_bundle_product' ), 99 );

			add_action ( 'yith_ywgc_gift_card_design_section', array( $this, 'show_design_section' ) );

			add_action ( 'yith_ywgc_gift_card_delivery_info_section', array( $this,  'show_gift_card_details' ), 15 );

			/**
			 * Enqueue frontend scripts
			 */
			add_action( 'woocommerce_product_query', array( $this, 'hide_from_shop_page' ) );

			//Register new endpoint to use for My Account page
			add_action( 'init', array( $this, 'yith_ywgc_add_endpoint' ) );

			//Add new query var
			add_filter( 'query_vars', array( $this, 'yith_ywgc_gift_cards_query_vars' ) );

			//Insert the new endpoint into the My Account menu
			add_filter( 'woocommerce_account_menu_items', array( $this, 'yith_ywgc_add_gift_cards_link_my_account' ) );

			//Add content to the new endpoint
			add_action( 'woocommerce_account_gift-cards_endpoint', array( $this, 'yith_ywgc_gift_cards_content' ) );

			add_action ( 'woocommerce_order_item_meta_start', array( $this, 'show_gift_card_code_on_order_item' ), 10, 3 );

			//Gift this product button on the shop loop
			add_action ( 'woocommerce_after_shop_loop_item', array( $this, 'yiyh_wc_gift_card_woocommerce_after_shop_loop_item_call_back' ), 10 );

			add_shortcode( 'yith_ywgc_display_gift_card_form' , array( $this,  'yith_ywgc_display_gift_card_form' ) );

			add_shortcode( 'yith_gift_card_check_balance_form' , array( $this,  'yith_gift_card_check_balance_form' ) );

			add_shortcode( 'yith_redeem_gift_card_form' , array( $this, 'yith_redeem_gift_card_form' ) );

			add_shortcode( 'yith_gift_cards_user_table' , array( $this, 'yith_gift_cards_user_table' ) );

			add_filter( 'gettext', array( $this, 'yith_ywgc_rename_coupon_field_on_cart' ), 10, 3 );

			add_filter( 'woocommerce_checkout_coupon_message', array( $this, 'yith_ywgc_rename_coupon_label' ), 10, 1 );

			add_action( 'woocommerce_product_thumbnails', array( $this, 'yith_ywgc_display_gift_card_form_preview_below_image' ) );

			add_action( 'wp',  array( $this, 'yith_ywgc_remove_image_zoom_support' ), 100 );

//            add_action ( 'woocommerce_order_item_meta_start', array( $this, 'edit_gift_card'), 10, 3 );

			add_action( 'template_redirect',array( $this, 'ywgc_init_popup' ) , 0 );

			add_filter( 'yith_ywgc_check_gift_card_return',array( $this, 'yith_ywgc_check_gift_card_return_callback' ), 0 );


			/*  Drag and drop Integration */
			add_action( 'wp_ajax_upload_request_endpoint', array( $this,  'upload_request_endpoint' ) );
			add_action( 'wp_ajax_nopriv_upload_request_endpoint', array( $this,  'upload_request_endpoint' ) );

		}


		public function ywgc_init_popup(){

			include( 'class-yith-ywgc-popup.php' );
			$this->popup = new YITH_YWGC_Popup();

		}

		/**
		 * Display the input hidden to set if disable gift this product
		 */
		public function yith_wcgc_display_input_hidden_disable_gift_this_product() {

			global $product;

			if ( ! $product ){
				return;
			}

			echo "<input type='hidden' id='yith_wcyc_disable_gift_this_product' value='" . get_post_meta( $product->get_id(), '_yith_wcgc_disable_gift_this_product', true ) . "'>";

		}

		//Gift this product button on the shop loop
		public function yiyh_wc_gift_card_woocommerce_after_shop_loop_item_call_back() {

			$product = apply_filters( 'yith_wc_gift_this_product_shop_page_product_filter', wc_get_product() );

			if ( $product && apply_filters('yith_ywgc_give_product_as_present', true, $product ) && ( $product->is_in_stock() && $product->get_type() != 'gift-card' ) && ( get_option( 'ywgc_permit_its_a_present_shop_page' ) == 'yes' ) && ( get_option( 'ywgc_permit_its_a_present' ) == 'yes' ) && get_post_meta( $product->get_id(), '_yith_wcgc_disable_gift_this_product', true ) != true  ) {

				?>

				<a href="<?php echo get_permalink( $product->get_id() ) . '?yith-gift-this-product-form=yes'; ?>" style="text-align: center"
				   class="<?php echo apply_filters( 'yith_wc_gift_this_product_shop_page_class_filter', 'button yith_wc_gift_this_product_shop_page_class' ); ?>" rel="nofollow"><?php echo apply_filters( 'yith_wcgc_gift_this_product_shop_page_button_label', _x( YITH_YWGC()->ywgc_gift_this_product_label(), 'Gift this product from the shop page', 'yith-woocommerce-gift-cards' ) ); ?></a>

				<?php

			}

		}

		//Register new endpoint to use for My Account page
		public function yith_ywgc_add_endpoint() {
			add_rewrite_endpoint( 'gift-cards', EP_ROOT | EP_PAGES );
		}

		//Add new query var
		public function yith_ywgc_gift_cards_query_vars( $vars ) {
			$vars[] = 'gift-cards';

			return $vars;
		}

		//Insert the new endpoint into the My Account menu
		public function yith_ywgc_add_gift_cards_link_my_account( $items ) {


			$item_position = ( array_search( 'orders', array_keys( $items ) ) );

			$items_part1 = array_slice( $items, 0, $item_position + 1 );
			$items_part2 = array_slice( $items, $item_position );

			$items_part1['gift-cards'] = apply_filters( 'yith_wcgc_my_account_menu_item_title', esc_html__( 'Gift Cards', 'yith-woocommerce-gift-cards' ) );

			$items = array_merge( $items_part1, $items_part2 );


			return $items;
		}

		//Add content to the new endpoint
		public function yith_ywgc_gift_cards_content() {
			wc_get_template( 'myaccount/my-giftcards.php',
				array(),
				'',
				trailingslashit( YITH_YWGC_TEMPLATES_DIR ) );
		}

		/**
		 * Hide the temporary gift card product from being shown on shop page
		 *
		 * @param WP_Query $query The current query
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function hide_from_shop_page( $query ) {

			if ( YITH_YWGC ()->default_gift_card_id ) {
				$query->set ( 'post__not_in', array( YITH_YWGC ()->default_gift_card_id ) );
			}
		}

		public function show_manual_amount_area( $product ) {
			//  Check if the current product permit free entered amount...
			if ( ! $this->is_manual_amount_allowed ( $product ) ) {
				return;
			}

			?>
			<input id="ywgc-manual-amount" name="ywgc-manual-amount" class="ywgc-manual-amount" type="text"
			       placeholder="<?php echo apply_filters('yith_wcgc_manual_amount_option_text',esc_html__( "Enter amount", 'yith-woocommerce-gift-cards' )) ?>">
			<span class="ywgc-manual-currency-symbol ywgc-hidden"><?php echo get_woocommerce_currency_symbol() ?></span>
			<?php
		}

		/**
		 * Add frontend style to gift card product page
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function enqueue_frontend_script() {

			if ( is_product () || is_cart () || is_checkout () ||  is_account_page() || apply_filters ( 'yith_ywgc_do_eneuque_frontend_scripts', false ) ) {
				wp_register_script ( 'accounting', WC ()->plugin_url () . yit_load_js_file ( '/assets/js/accounting/accounting.js' ), array( 'jquery' ), '0.4.2' );

				$frontend_deps = array(
					'jquery',
					'woocommerce',
					'jquery-ui-datepicker',
					'accounting',
				);

				if ( is_cart () ) {
					$frontend_deps[] = 'wc-cart';
				}
				//  register and enqueue ajax calls related script file
				wp_register_script ( "ywgc-frontend-script",
					apply_filters( 'yith_ywgc_enqueue_script_source_path', YITH_YWGC_SCRIPT_URL . yit_load_js_file ( 'ywgc-frontend.js' ) ),
					$frontend_deps,
					YITH_YWGC_ENQUEUE_VERSION,
					true );

				global $post;


				if ( is_product() ) {
					$product = new WC_Product_Gift_Card($post->ID);

					$manual_mode = $product->get_manual_amount_status();

					if ($manual_mode != "global") {
						$manual_minimal_amount = get_post_meta($post->ID, '_ywgc_minimal_manual_amount', true);
					} else {
						$manual_minimal_amount = get_option('ywgc_minimal_amount_gift_card', '' );
					}

					if (is_numeric($manual_minimal_amount))
						$manual_minimal_amount_error = esc_html__("The minimum amount is", 'yith-woocommerce-gift-cards') . " " . $manual_minimal_amount;
					else
						$manual_minimal_amount_error = '';

				}
				else{
					$manual_minimal_amount = '';
					$manual_minimal_amount_error = '';
				}


				$default_color = defined( 'YITH_PROTEO_VERSION' ) ? get_theme_mod( 'yith_proteo_main_color_shade', '#448a85' ) : '#000000';
				$plugin_main_color = get_option( 'ywgc_plugin_main_color', $default_color);

				$date_format = get_option( 'ywgc_plugin_date_format_option', 'yy-mm-dd' );

				$yith_show_gift_this_product_form = ( isset( $_REQUEST[ 'yith-gift-this-product-form' ] ) ? $_REQUEST[ 'yith-gift-this-product-form' ] : '' );

				$enfold = 'Enfold' == wp_get_theme()->get('Name') || 'Enfold Child' == wp_get_theme()->get('Name') ? 'yes' : 'no';



				wp_localize_script ( 'ywgc-frontend-script',
					'ywgc_data',
					array(
						'loader'                       => apply_filters ( 'yith_gift_cards_loader', YITH_YWGC_ASSETS_URL . '/images/loading.gif' ),
						'ajax_url'                     => admin_url ( 'admin-ajax.php' ),
						'currency'                     => get_woocommerce_currency_symbol (),
						'custom_image_max_size'        => get_option ( 'ywgc_custom_image_max_size', 1 ),
						'invalid_image_extension'      => esc_html__( "File format is not valid, select a jpg, jpeg, png, gif or bmp file", 'yith-woocommerce-gift-cards' ),
						'invalid_image_size'           => esc_html__( "The size fo the uploaded file exceeds the maximum allowed", 'yith-woocommerce-gift-cards' ) . " (" .  get_option ( 'ywgc_custom_image_max_size', 1 ) . " MB)",
						'default_gift_card_image'      => YITH_YWGC ()->get_header_image ( is_product () ? wc_get_product ( $post ) : null ),
						'notify_custom_image_small'    => apply_filters ( "yith_gift_cards_custom_image_editor", esc_html__( '<b>Attention</b>: the <b>suggested minimum</b> size of the image is 490x195', 'yith-woocommerce-gift-cards' ) ),
						'multiple_recipient'           => esc_html__( "<b>Note</b>: You added more than one recipient, so <i class='ywgc-darkred-text'>you will buy %number_gift_cards% gift cards</i> and each recipient will receive a different gift card.", 'yith-woocommerce-gift-cards' ),
						'missing_scheduled_date'       => esc_html__( "Please enter a valid delivery date", 'yith-woocommerce-gift-cards' ),
						'wc_ajax_url'                  => WC_AJAX::get_endpoint ( "%%endpoint%%" ),
						'gift_card_nonce'              => wp_create_nonce ( 'apply-gift-card' ),
						// For accounting JS
						'currency_format'              => esc_attr ( str_replace ( array( '%1$s', '%2$s' ), array(
							'%s',
							'%v'
						), get_woocommerce_price_format () ) ),
						'mon_decimal_point'            => wc_get_price_decimal_separator (),
						'currency_format_num_decimals' => apply_filters ( "yith_gift_cards_format_number_of_decimals", wc_get_price_decimals () ),
						'currency_format_symbol'       => get_woocommerce_currency_symbol (),
						'currency_format_decimal_sep'  => esc_attr ( wc_get_price_decimal_separator () ),
						'currency_format_thousand_sep' => esc_attr ( wc_get_price_thousand_separator () ),
						'manual_amount_wrong_format'   => sprintf ( apply_filters( 'yith_ywgc_manual_amount_wrong_format_text',  esc_html__( "Please use only digits and the decimal separator '%1\$s'. Valid examples are '123', '123%1\$s9 and '123%1\$s99'.", 'yith-woocommerce-gift-cards' ),
							"Alert: the manual gift card field was filled with a wrong formatted value. It should contains only digits and a facultative decimal separator followed by one or two digits",
							'yith-woocommerce-gift-cards' ), wc_get_price_decimal_separator () ),
						'manual_minimal_amount'        => $manual_minimal_amount,
						'manual_minimal_amount_error'  => $manual_minimal_amount_error,
						'email_bad_format'             => esc_html__( "Please enter a valid email address", 'yith-woocommerce-gift-cards' ),
						'mandatory_email'              => YITH_YWGC ()->mandatory_recipient(),
						'name'                         => esc_html__( "ENTER ADDITIONAL RECIPIENT'S NAME", 'yith-woocommerce-gift-cards' ),
						'email'                        => esc_html__( "ENTER ADDITIONAL RECIPIENT'S EMAIL ADDRESS", 'yith-woocommerce-gift-cards' ),
						'label_name'                   => apply_filters('ywgc_recipient_name_label',esc_html__( "Name: ", 'yith-woocommerce-gift-cards' )),
						'label_email'                  => apply_filters('ywgc_recipient_email_label',esc_html__( "E-mail: ", 'yith-woocommerce-gift-cards' )),
						'notice_target'                => apply_filters ( 'yith_ywgc_gift_card_notice_target', 'div.ywgc_enter_code' ),
						'add_gift_text'                => apply_filters ( 'yith_gift_card_layout_add_gift_button_text', esc_html__( "Buy gift card", 'yith-woocommerce-gift-cards' ) ),
						'date_format'   => $date_format,
						'plugin_main_color'   => $plugin_main_color,
						'gift_this_product_automatically'   => $yith_show_gift_this_product_form,
						'enfold'   => $enfold,
					) );

				wp_enqueue_script ( "ywgc-frontend-script" );


				/**
				 * POPUP script
				 */

				if( $this->popup ) {
					wp_register_script( 'ywgc_popup_handler_js', YITH_YWGC_SCRIPT_URL . yit_load_js_file( 'ywgc-popup-handler.js' ),
						[ 'jquery', 'wp-util', 'jquery-blockui' ], YITH_YWGC_ENQUEUE_VERSION, true );


					wp_enqueue_script( 'ywgc_popup_handler_js' );
					// add script data
					wp_localize_script( 'ywgc_popup_handler_js',
						'ywgc_popup_data',
						apply_filters( 'ywgc_popup_handler_js_script_data', [
							'popupWidth'   => '100%',
							'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
							'loader'       => YITH_YWGC_ASSETS_URL . '/images/loading.gif',
							'mainSelector' => '.ywgc-choose-image.ywgc-choose-template',
						] ) );

				}

				if ( is_product() && get_option ( "ywgc_custom_design", 'no') == 'yes' ) {

					// Fine uploader
					wp_register_script( 'fine-uploader-script', YITH_YWGC_ASSETS_URL . '/vendor/jquery.fine-uploader/jquery.fine-uploader.js', array( 'jquery' ), YITH_YWGC_ENQUEUE_VERSION, true );
					wp_register_script( 'support-attachments', YITH_YWGC_SCRIPT_URL . yit_load_js_file( 'support-attachments.js' ), array( 'fine-uploader-script' ), YITH_YWGC_ENQUEUE_VERSION, true );
					// CSS to style the file input field as button and adjust the Bootstrap progress bars
					wp_register_style( 'fine-uploader-style', YITH_YWGC_ASSETS_URL . '/vendor/jquery.fine-uploader/fine-uploader-new.css', array( ), YITH_YWGC_ENQUEUE_VERSION, 'all');

					$ajaxurl = admin_url( 'admin-ajax.php' );
					wp_enqueue_script( 'support-attachments' );
					wp_enqueue_style( 'fine-uploader-style' );
					wp_localize_script( 'support-attachments', 'yith_uploader', [
						'debug'      => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG,
						'ajaxurl'    => $ajaxurl,
						'wrapper_id' => 'fine-uploader',
						'request'    => array(
							'endpoint' => $ajaxurl,
							'params'   => array(
								'action' => 'upload_request_endpoint'
							)
						),
						'customMessages' => array(
							'maxFileSize' => sprintf( '%s %dM', __( 'File is too large. Max file size:', 'yith-zendesk' ), get_option ( 'ywgc_custom_image_max_size', 1 ) )
						),
						'text' => array(
							'formatProgress' => "{total_size}",
							'uploading'      => __( "Uploading... {percent}%", 'yith-zendesk' ),
							'waitingForResponse' => __( "Sending...", 'yith-zendesk' )
						)
					] );

				}

			}
		}

		/**
		 * Add frontend style to gift card product page
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function enqueue_frontend_style() {

			if ( is_product () || is_cart () || is_checkout () || apply_filters ( 'yith_ywgc_do_eneuque_frontend_scripts', false ) ) {

				wp_enqueue_style ( 'ywgc-frontend',
					YITH_YWGC_ASSETS_URL . '/css/ywgc-frontend.css',
					array(),
					YITH_YWGC_ENQUEUE_VERSION );

				if ( apply_filters ( 'yith_ywgc_enqueue_jquery_ui_css', true ) ) {
					wp_enqueue_style ( 'jquery-ui-css',
						'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css' );
				}

				if( $this->popup ) {
					wp_register_style('ywgc_popup_style_css', YITH_YWGC_ASSETS_URL . '/css/ywgc-popup-style.css', [], YITH_YWGC_ENQUEUE_VERSION, 'all');

					wp_enqueue_style('ywgc_popup_style_css');
				}

				wp_add_inline_style('ywgc-frontend', $this->get_custom_css());
			}

		}


		public function get_custom_css(){

			$custom_css         = '';
			$default_color = defined( 'YITH_PROTEO_VERSION' ) ? get_theme_mod( 'yith_proteo_main_color_shade', '#448a85' ) : '#000000';
			$plugin_main_color = get_option( 'ywgc_plugin_main_color', $default_color);

			list($r, $g, $b) = sscanf($plugin_main_color, "#%02x%02x%02x");

			$gift_this_product_button_colors_default = Array(
				'default' => '#ffffff',
				'hover' => '#ffffff',
				'default_text' => '#448A85',
				'hover_text' => '#1A4E43'
			);

			$gift_this_product_button_colors_array = get_option( 'ywgc_gift_this_product_colors', $gift_this_product_button_colors_default );


			$form_button_colors_default = Array(
				'default' => '#448a85',
				'hover' => '#4ac4aa',
				'default_text' => '#ffffff',
				'hover_text' => '#ffffff'
			);

			$form_colors_default = Array(
				'default' => '#ffffff',
				'hover' => '#ffffff',
				'default_text' => '#000000',
				'hover_text' => '#000000'
			);

			$form_button_colors_array = get_option( 'ywgc_apply_gift_cards_button_colors', $form_button_colors_default );
			$form_colors_array = get_option( 'ywgc_apply_gift_cards_colors', $form_colors_default );


			$custom_css .= "
                    #give-as-present {
                        background-color: {$gift_this_product_button_colors_array['default']};
                        color:{$gift_this_product_button_colors_array['default_text']};
                    }
                    #give-as-present:hover {
                        background-color:{$gift_this_product_button_colors_array['hover']};
                        color:{$gift_this_product_button_colors_array['hover_text']};
                    }
                    #ywgc-cancel-gift-card {
                        background-color:{$gift_this_product_button_colors_array['default']};
                        color:{$gift_this_product_button_colors_array['default_text']};
                    }
                    #ywgc-cancel-gift-card:hover {
                        background-color:{$gift_this_product_button_colors_array['hover']};
                        color:{$gift_this_product_button_colors_array['hover_text']};
                    }
                    .ywgc_apply_gift_card_button{
                        background-color:{$form_button_colors_array['default']} !important;
                        color:{$form_button_colors_array['default_text']}!important;
                    }
                    .ywgc_apply_gift_card_button:hover{
                        background-color:{$form_button_colors_array['hover']}!important;
                        color:{$form_button_colors_array['hover_text']}!important;
                    }
                    .ywgc_enter_code{
                        background-color:{$form_colors_array['default']};
                        color:{$form_colors_array['default_text']};
                    }
                    .ywgc_enter_code:hover{
                        background-color:{$form_colors_array['default']};
                        color: {$form_colors_array['default_text']};
                    }
                    .gift-cards-list button{
                        border: 1px solid {$plugin_main_color};
                    }
                    .selected_image_parent{
                        border: 2px dashed {$plugin_main_color} !important;
                    }
                    .ywgc-preset-image.selected_image_parent:after{
                        background-color: {$plugin_main_color};
                    }
                    .ywgc-predefined-amount-button.selected_button{
                        background-color: {$plugin_main_color};
                    }
                    .ywgc-on-sale-text{
                        color:{$plugin_main_color};
                    }
                    .ywgc-choose-image.ywgc-choose-template:hover{
                        background: rgba({$r}, {$g}, {$b}, 0.9);
                    }
                    .ywgc-choose-image.ywgc-choose-template{
                        background: rgba({$r}, {$g}, {$b}, 0.8);
                    }
                    .ywgc-form-preview-separator{
                        background-color: {$plugin_main_color};
                    }
                    .ywgc-form-preview-amount{
                        color: {$plugin_main_color};
                    }
                    #ywgc-manual-amount{
                        border: 1px solid {$plugin_main_color};
                    }
                    .ywgc-template-categories a:hover,
                    .ywgc-template-categories a.ywgc-category-selected{
                        color: {$plugin_main_color};
                    }
                    .ywgc-design-list-modal .ywgc-preset-image:before {
                        background-color: {$plugin_main_color};
                    }

           ";

			if (class_exists('Storefront')) {
				$custom_css .= '#ywgc-choose-design-preview .ywgc-design-list > ul{
            						display: contents;
								}';
			}

			if ( 'Enfold' == wp_get_theme()->get('Name') || 'Enfold Child' == wp_get_theme()->get('Name') ){

				$custom_css .="

		            .ywgc-amount-buttons{
                        width: 8em;
                        height: 3em;
					}
					.gift-card-content-editor.step-content input {
                        width: 70% !important;
					}
					.gift-card-content-editor.step-content textarea {
                        width: 70% !important;
					}

					.ywgc-sender-info-title{
                        margin-top: 8em !important;
					}

		        ";

			}

			if ( 'Twenty Twenty' == wp_get_theme()->get('Name') || 'Twenty Twenty Child' == wp_get_theme()->get('Name')
			     || 'WooPress' == wp_get_theme()->get('Name') || 'WooPress Child' == wp_get_theme()->get('Name') ){

				$custom_css .="

		            .ywgc-currency-symbol {
		                margin-left: 0;
		                position: relative;
		                top: -32px;
		                left: 7px;
		            }
		        ";

			}

			if ( 'Avada' == wp_get_theme()->get('Name') || 'Avada Child' == wp_get_theme()->get('Name') ){

				$custom_css .="

		            .ywgc-currency-symbol {
		                 margin-left: -120px;
		            }
		        ";

			}



			return apply_filters( 'yith_ywgc_custom_css', $custom_css );
		}


		/**
		 * Show custom design area for the product
		 *
		 * @param WC_Product $product
		 */
		public function show_design_section( $product ) {


			$args = apply_filters( 'yith_wcgc_design_presets_args',
				array(
					'hide_empty' => 1
				)
			);

			$categories = get_terms ( YWGC_CATEGORY_TAXONOMY, $args );

			$item_categories = array();
			foreach ( $categories as $item ) {
				$object_ids = get_objects_in_term ( $item->term_id, YWGC_CATEGORY_TAXONOMY );
				foreach ( $object_ids as $object_id ) {
					$item_categories[ $object_id ] = isset( $item_categories[ $object_id ] ) ? $item_categories[ $object_id ] . ' ywgc-category-' . $item->term_id : 'ywgc-category-' . $item->term_id;
				}
			}

			// Load the template
			wc_get_template('yith-gift-cards/gift-card-design.php',
				array(
					'categories' => $categories,
					'item_categories' => $item_categories,
					'product' => $product,
				),
				'',
				trailingslashit(YITH_YWGC_TEMPLATES_DIR));
		}

		/**
		 * Show Gift Cards details
		 *
		 * @param WC_Product $product
		 */
		public function show_gift_card_details( $product ) {

			if ( ( $product instanceof WC_Product_Gift_Card ) && $product->is_virtual () ) { //load virtual gift cards template
				wc_get_template('yith-gift-cards/gift-card-details.php',
					array(
						'allow_multiple_recipients' => YITH_YWGC()->allow_multiple_recipients() && ($product instanceof WC_Product_Gift_Card),
						'mandatory_recipient' => apply_filters('yith_wcgc_gift_card_details_mandatory_recipient', YITH_YWGC()->mandatory_recipient()),
						'gift_this_product' => !($product instanceof WC_Product_Gift_Card),
						'allow_send_later' => ("yes" == get_option('ywgc_enable_send_later', 'no' ) ),
					),
					'',
					trailingslashit(YITH_YWGC_TEMPLATES_DIR));
			}
			else{ //load physical gift cards template
				wc_get_template ( 'yith-gift-cards/physical-gift-card-details.php',
					array(
						'allow_multiple_recipients' => YITH_YWGC ()->allow_multiple_recipients() && ( $product instanceof WC_Product_Gift_Card ),
						'ywgc_physical_details_mandatory'       => ( "yes" == get_option ( 'ywgc_physical_details_mandatory' ) ) ,
						'gift_this_product'         => ! ( $product instanceof WC_Product_Gift_Card ),
						'allow_send_later'          => ( "yes" == get_option ( 'ywgc_enable_send_later', 'no' ) ),
					),
					'',
					trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
			}


		}

		/**
		 * Show my gift cards status on myaccount page
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_my_gift_cards_table() {
			wc_get_template ( 'myaccount/my-giftcards.php',
				'',
				'',
				trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
		}


		/**
		 * Let the user to edit the gift card
		 *
		 * @param $order_item_id
		 * @param $item
		 * @param $order
		 */
		public function edit_gift_card( $order_item_id, $item, $order ) {

			if ( ! ( 'yes' == get_option ( 'ywgc_permit_modification' ) ) ) {
				return;
			}

			//  Allow editing only on checkout or my orders pages
			if ( ! is_checkout () && ! is_account_page () ) {
				return;
			}

			$item_meta_array = $item["item_meta"];
			//  Check if current order item is a gift card
			if ( ! isset( $item_meta_array[ YWGC_ORDER_ITEM_DATA ] ) ) {

				return;
			}

			//  Retrieve the gift card content. If a valid gift card was generated, the content to be edited is a postmeta of the
			//  Gift card post type, else the content is still on the order_item_meta.
			$gift_cards = ywgc_get_order_item_giftcards ( $order_item_id );

			if ( $gift_cards ) {
				$_gift_card_id = is_array ( $gift_cards ) ? $gift_cards[0] : $gift_cards;

				//  edit values from a gift card object stored on the DB
				$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $_gift_card_id ) );

			} else {
				//  edit the data stored as order item meta because the final gift card is not created yet
				$order_item_meta = $item_meta_array[ YWGC_ORDER_ITEM_DATA ];
				$order_item_meta = $order_item_meta[0];
				$order_item_meta = maybe_unserialize ( $order_item_meta );

				$gift_card = new YWGC_Gift_Card_Premium( $order_item_meta );
			}

			//  Check if the gift card still exists
			if ( ! $gift_card->exists () ) {
				//return;
			}

			//  There is nothing to edit for physical gift card product, only virtual gift cards
			//  can be edited

			if ( ! $gift_card->is_virtual () ) {
				return;
			}

			?>

			<div id="current-gift-card-<?php echo $order_item_id; ?>" class="ywgc-gift-card-content">
				<a href="#"
				   class="edit-details"><?php _e ( "See card details", 'yith-woocommerce-gift-cards' ); ?></a>

				<div class="ywgc-gift-card-details ywgc-hide">
					<h3><?php _e ( "Gift card details", 'yith-woocommerce-gift-cards' ); ?></h3>
					<fieldset class="ywgc-sender-details" style="border: none">
						<label><?php _e ( "Sender: ", 'yith-woocommerce-gift-cards' ); ?></label>
						<span class="ywgc-sender"><?php echo $gift_card->sender_name; ?></span>
					</fieldset>

					<fieldset class="ywgc-recipient-details" style="border: none">
						<label><?php _e ( "Recipient: ", 'yith-woocommerce-gift-cards' ); ?></label>
						<span class="ywgc-recipient"><?php echo $gift_card->recipient; ?></span>
					</fieldset>

					<fieldset class="ywgc-message-details" style="border: none">
						<label><?php _e ( "Message: ", 'yith-woocommerce-gift-cards' ); ?></label>
						<span class="ywgc-message"><?php echo $gift_card->message; ?></span>
					</fieldset>
					<button class="ywgc-do-edit btn btn-ghost" style="display: none;"><?php _e ( "Edit", 'yith-woocommerce-gift-cards' ); ?></button>
				</div>

				<div class="ywgc-gift-card-edit-details ywgc-hide" style="display: none">
					<h3><?php _e ( "Gift card details", 'yith-woocommerce-gift-cards' ); ?></h3>

					<form name="form-gift-card-<?php echo $gift_card->ID; ?>">
						<input type="hidden" name="ywgc-gift-card-id" value="<?php echo $gift_card->ID; ?>">
						<input type="hidden" name="ywgc-item-id" value="<?php echo $order_item_id; ?>">
						<fieldset>
							<label
								for="ywgc-edit-sender"><?php _e ( "Sender: ", 'yith-woocommerce-gift-cards' ); ?></label>
							<input type="text" name="ywgc-edit-sender" id="ywgc-edit-sender"
							       value="<?php echo $gift_card->sender_name; ?>">
						</fieldset>

						<fieldset>
							<label
								for="ywgc-edit-recipient"><?php _e ( "Recipient: ", 'yith-woocommerce-gift-cards' ); ?></label>
							<input type="email" name="ywgc-edit-recipient" id="ywgc-edit-recipient"
							       value="<?php echo $gift_card->recipient; ?>"">
						</fieldset>

						<fieldset>
							<label
								for="ywgc-edit-message"><?php _e ( "Message: ", 'yith-woocommerce-gift-cards' ); ?></label>
							<textarea name="ywgc-edit-message" id="ywgc-edit-message"
							          rows="5"><?php echo $gift_card->message; ?></textarea>
						</fieldset>
					</form>

					<button
						class="ywgc-apply-edit btn apply"><?php _e ( "Apply", 'yith-woocommerce-gift-cards' ); ?></button>
					<button
						class="ywgc-cancel-edit btn btn-ghost"><?php _e ( "Cancel", 'yith-woocommerce-gift-cards' ); ?></button>
				</div>
			</div>
			<?php
		}

		/**
		 * Let the customer to use a product of type WC_Product_Simple  as source for a gift card
		 */
		public function show_give_as_present_link_simple() {

			global $product;

			if ( ! $product ){
				return;
			}

			if ( get_post_meta( $product->get_id(), '_yith_wcgc_disable_gift_this_product', true ) == 'yes' ) {
				return;
			}

			if ( ! YITH_YWGC ()->allow_product_as_present() ) {
				return;
			}

			if ( $product instanceof WC_Product_Simple && apply_filters('yith_ywgc_give_product_as_present',true,$product) ) {
				// Load the template
				wc_get_template ( 'single-product/add-to-cart/give-product-as-present.php',
					array(
						'product'         => $product
					),
					'',
					trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
			}
		}

		/**
		 * Let the customer to use a product of type WC_Product_Variable  as source for a gift card
		 */
		public function show_give_as_present_link_variable() {

			global $product;

			if ( ! $product ){
				return;
			}

			if ( get_post_meta( $product->get_id(), '_yith_wcgc_disable_gift_this_product', true ) == 'yes' ) {
				return;
			}

			if ( ! YITH_YWGC ()->allow_product_as_present() ) {
				return;
			}

			if ( $product instanceof WC_Product_Variable && apply_filters('yith_ywgc_give_product_as_present',true,$product) )  {

				// Load the template
				wc_get_template ( 'single-product/add-to-cart/give-product-as-present.php',
					array(
						'product'         => $product
					),
					'',
					trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
			}
		}

		/**
		 * Integration with yith woocommerce product bundle
		 * Let the customer to use a product of type WC_Product_Yith_Bundle  as source for a gift card
		 */
		public function show_give_as_present_link_product_bundle_product() {

			global $product;

			if ( ! $product ){
				return;
			}

			if ( get_post_meta( $product->get_id(), '_yith_wcgc_disable_gift_this_product', true ) == 'yes' ) {
				return;
			}

			if ( ! YITH_YWGC ()->allow_product_as_present() ) {
				return;
			}


			if ( $product instanceof WC_Product_Yith_Bundle && apply_filters('yith_ywgc_give_product_as_present', true, $product ) ) {

				// Load the template
				wc_get_template ( 'single-product/add-to-cart/give-product-as-present.php',
					array(
						'product'         => $product
					),
					'',
					trailingslashit ( YITH_YWGC_TEMPLATES_DIR ) );
			}
		}

		/**
		 * Check if a gift card product avoid entering manual amount value
		 *
		 * @param WC_Product_Gift_Card $product
		 *
		 * @return bool
		 */
		public function is_manual_amount_allowed( $product ) {

			$manual_amount = $product->get_manual_amount_status ();

			//  if the gift card have specific manual entered amount behaviour, return that
			if ( "global" != $manual_amount ) {
				return "accept" == $manual_amount;
			}

			return YITH_YWGC()->allow_manual_amount();
		}


		/**
		 * Retrieve the number of templates available
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function template_design_count() {
			global $wp_version;
			if ( version_compare ( $wp_version, '4.5', '<' ) ) {
				$media_terms = get_terms ( YWGC_CATEGORY_TAXONOMY, array( 'hide_empty' => 1 ) );
			} else {
				$media_terms = get_terms ( array( 'taxonomy' => YWGC_CATEGORY_TAXONOMY, 'hide_empty' => 1, 'hierarchical' => false) );
			}
			$ids = array();
			foreach ( $media_terms as $media_term ) {
				$ids[] = $media_term->term_id;
			}

			$template_ids = array_unique ( get_objects_in_term ( $ids, YWGC_CATEGORY_TAXONOMY ) );

			return count ( $template_ids );
		}

		/**
		 * Show the gift card code under the order item, in the order admin page
		 *
		 * @param int        $item_id
		 * @param array      $item
		 * @param WC_product $_product
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_gift_card_code_on_order_item( $item_id, $item, $_product ) {

			global $theorder;
			$gift_ids = ywgc_get_order_item_giftcards ( $item_id );


			if ( empty( $gift_ids ) ) {
				return;
			}

			foreach ( $gift_ids as $gift_id ) {

				$gc = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_id ) );

				if ( ! $gc->is_pre_printed () ):
					?>
					<div>
						<?php if (apply_filters('yith_ywgc_display_code_order_details', true)) : ?>
							<span class="ywgc-gift-code-label"><?php _e ( "Gift card code: ", 'yith-woocommerce-gift-cards' ); ?></span>
							<span class="ywgc-card-code"><?php echo $gc->get_code (); ?></span>
						<?php endif;

						if ( $gc->delivery_send_date ){
							$status_class = "sent";
							$message      = sprintf ( esc_html__( "Sent on %s", 'yith-woocommerce-gift-cards' ), $gc->get_formatted_date( $gc->delivery_send_date ) );
						} else if ( $gc->delivery_date >= current_time ( 'timestamp' ) ) {
							$status_class = "scheduled";
							$message      = esc_html__( "Scheduled", 'yith-woocommerce-gift-cards' );
						} else if ( $gc->has_been_sent() == '' ) {
							$status_class = "not-sent";
							$message      = esc_html__( "Not yet sent", 'yith-woocommerce-gift-cards' );
						}else{
							$status_class = "failed";
							$message      = esc_html__( "Failed", 'yith-woocommerce-gift-cards' );
						}
						?>

						<div>
							<span><?php echo sprintf ( esc_html__( "Recipient: %s", 'yith-woocommerce-gift-cards' ), $gc->recipient ); ?></span>
						</div>
						<div>
							<?php if( $gc->delivery_date != '' ): ?>
								<span><?php echo sprintf ( esc_html__( "Delivery date: %s", 'yith-woocommerce-gift-cards' ), $gc->get_formatted_date( $gc->delivery_date ) ); ?></span>
								<br>
							<?php endif; ?>
							<span class="ywgc-delivery-status <?php echo $status_class; ?>"><?php echo $message; ?></span>

						</div>
						<?php


						?>
					</div>
				<?php endif;
			}
		}


		/**
		 * Shortcode to include the necessary hook to display the gift card form
		 */
		function yith_ywgc_display_gift_card_form( $atts, $content ){

			global $product;

			if ( is_object($product) && $product instanceof WC_Product_Gift_Card && 'gift-card' == $product->get_type() ) {

				ob_start();

				wc_get_template( 'single-product/add-to-cart/gift-card.php',
					'',
					'',
					trailingslashit( YITH_YWGC_TEMPLATES_DIR ) );

				$content = ob_get_clean();

			}
			return $content;
		}

		/**
		 * Shortcode add a check gift card balance form
		 */
		function yith_gift_card_check_balance_form( $atts, $content ){

			ob_start();

			wc_get_template( 'shortcodes/gift-card-check-balance-form.php', '', '', trailingslashit( YITH_YWGC_TEMPLATES_DIR ) );

			$content = ob_get_clean();

			return $content;
		}

		/**
		 * Shortcode to redeem the gift card manually
		 */
		function yith_redeem_gift_card_form( $atts, $content ){

			ob_start();

			wc_get_template( 'shortcodes/redeem-gift-card-form.php', '', '', trailingslashit( YITH_YWGC_TEMPLATES_DIR ) );

			$content = ob_get_clean();

			return $content;
		}


		/**
		 * Shortcode to display the user gift card table
		 */
		function yith_gift_cards_user_table( $atts, $content ){

			ob_start();

			wc_get_template( 'shortcodes/user-gift-card-table.php', $atts, '', trailingslashit( YITH_YWGC_TEMPLATES_DIR ) );

			$content = ob_get_clean();

			return $content;
		}




		/**
		 * Rename the coupon field on the cart page
		 */
		function yith_ywgc_rename_coupon_field_on_cart( $translated_text, $text, $text_domain ) {

			if ( is_admin() || 'woocommerce' !== $text_domain ) {
				return $translated_text;
			}
			if ( 'Apply coupon' === $text ) {
				$translated_text = get_option( 'ywgc_apply_coupon_button_text_button' , esc_html__( 'Apply coupon', 'yith-woocommerce-gift-cards' ) );
			}
			return $translated_text;
		}

		/**
		 * Rename the coupon label on the checkout page
		 */
		function yith_ywgc_rename_coupon_label( $text ) {

			$text_option =  get_option( 'ywgc_apply_coupon_label_text' , esc_html__( 'Have a coupon?', 'yith-woocommerce-gift-cards' ) );

			$text = $text_option . ' <a href="#" class="showcoupon">' . esc_html__( 'Click here to enter your code', 'woocommerce' ) . '</a>';


			return $text;
		}

		/**
		 * Display a preview of the form under the gift card image
		 */
		function yith_ywgc_display_gift_card_form_preview_below_image( ) {

			if (is_product()) {

				$product = wc_get_product(get_the_ID());

				if (is_object($product) && $product->is_type('gift-card') && $product->is_virtual()) {

					wc_get_template('single-product/form-preview.php',
						array(
							'product' => $product,
						),
						'',
						trailingslashit(YITH_YWGC_TEMPLATES_DIR));
				}
			}
		}


		/**
		 * Remove zoom in gift card product pages
		 */
		function yith_ywgc_remove_image_zoom_support( ) {

			if ( is_product() ){

				$product = wc_get_product( get_the_ID() );

				if ( is_object( $product ) && $product->is_type( 'gift-card' ) ) {
					remove_theme_support( 'wc-product-gallery-zoom' );
					remove_theme_support( 'wc-product-gallery-lightbox' );
				}
			}
		}

		/**
		 * Add condition when a gift card is applied
		 */
		function yith_ywgc_check_gift_card_return_callback( $bool ) {

			$items = WC()->cart->get_cart();

			if ( get_option( 'ywgc_apply_gc_code_on_gc_product', 'no' )  == 'yes' ){
				foreach ( $items as $cart_item_key => $values ) {
					$product = $values['data'];

					if ( $product->get_type() == 'gift-card' ){
						wc_add_notice( esc_html__( 'It is not possible to add a gift card code when the cart contains a gift card product', 'yith-woocommerce-gift-cards'), 'error' );

						$bool = false;
					}
				}
			}

			return $bool;
		}


		/**
		 * Upload Request endpoint
		 *
		 * @return bool
		 */
		public function upload_request_endpoint() {

			$current_upload_dir = ABSPATH . 'wp-content/uploads/temp-gift-cards-design';

			$uploader = new UploadHandler();

			if ( ! file_exists( $current_upload_dir ) ) {
				mkdir( $current_upload_dir, 0755 );
			}

			$uploader->allowedExtensions = apply_filters( 'yith_zendesk_upload_allowed_extensions', array( 'jpeg', 'jpg', 'png', ) );

			// Specify max file size in bytes.
			$uploader->sizeLimit = get_option ( 'ywgc_custom_image_max_size', 1 ) * 1024 * 1024;

			// Specify the input name set in the javascript.
			$uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default

			$method = $_SERVER["REQUEST_METHOD"];
			if ( $method == "POST" ) {

				header( "Content-Type: text/plain" );

				// Handles upload requests
				$result = $uploader->handleUpload( $current_upload_dir );

				$uploader->handleDelete( $current_upload_dir );

				$result["uploadName"] = $uploader->getUploadName();

				if ( isset( $result["uuid"] ) ){
					$image_url = $current_upload_dir . "/" . $result["uuid"] . "/" . $result["uploadName"];
					$type = pathinfo($image_url, PATHINFO_EXTENSION);
					$data = file_get_contents($image_url);
					$result["imagebase64"] = 'data:image/' . $type . ';base64,' . base64_encode($data);

					$directory_to_delete = $current_upload_dir . "/" . $result["uuid"];
					$this->delete_uploaded_files( $directory_to_delete );
				}


				wp_send_json( $result );

			}

		}

		public function delete_uploaded_files($target) {

			if(is_dir($target)){
				$files = glob( $target . '*', GLOB_MARK );
				foreach( $files as $file ){
					$this->delete_uploaded_files( $file );
				}
				if(is_dir($target))
					rmdir( $target );
			} elseif(is_file($target)) {
				unlink( $target );
			}
		}



	}
}
