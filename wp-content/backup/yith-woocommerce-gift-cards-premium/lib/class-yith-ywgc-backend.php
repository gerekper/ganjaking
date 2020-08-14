<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists ( 'YITH_YWGC_Backend' ) ) {

	/**
	 *
	 * @class   YITH_YWGC_Backend
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_YWGC_Backend {

		const YWGC_GIFT_CARD_LAST_VIEWED_ID = 'ywgc_last_viewed';

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
        protected static $instance;

        /**
         * race condition active
         *
         * @since 2.0.3
         */
        protected static $rc_active;

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

			/**
			 * Enqueue scripts and styles
			 */
			add_action ( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_files' ) );

			/**
			 * Add the "Gift card" type to product type list
			 */
			add_filter ( 'product_type_selector', array(
				$this,
				'add_gift_card_product_type'
			) );

			/**
			 * * Save gift card data when a product of type "gift card" is saved
			 */
			add_action ( 'save_post', array(
				$this,
				'save_gift_card'
			), 1, 2 );

			/**
			 * * Save gift card data when a product of type "gift card" is saved
			 */
			add_action ( 'save_post', array(
				$this,
				'save_pre_printed_gift_card_code'
			), 1, 2 );

			/**
			 * Ajax call for adding and removing gift card amounts on product edit page
			 */
			add_action ( 'wp_ajax_add_gift_card_amount', array(
				$this,
				'add_gift_card_amount_callback'
			) );
			add_action ( 'wp_ajax_remove_gift_card_amount', array(
				$this,
				'remove_gift_card_amount_callback'
			) );

			/**
			 * Hide some item meta from product edit page
			 */
			add_filter ( 'woocommerce_hidden_order_itemmeta', array(
				$this,
				'hide_item_meta'
			) );

			/**
             * Append gift card amount generation controls to general tab on product page
             */
			add_action ( 'woocommerce_product_options_general_product_data', array( $this, 'show_gift_card_product_settings' ) );


			/**
			 * Generate a valid card number for every gift card product in the order
			 */
			add_action ( 'woocommerce_order_status_changed', array(
				$this,
				'order_status_changed'
			), 10, 3 );

			add_action ( 'woocommerce_before_order_itemmeta', array(
				$this,
				'show_gift_card_code_on_order_item'
			), 10, 3 );

			/**
			 * Set the CSS class 'show_if_gift-card in tax section
			 */
			add_action ( 'woocommerce_product_options_general_product_data', array(
				$this,
				'show_tax_class_for_gift_cards'
			) );

            /**
             * Custom condition to create gift card on cash on delivery only on complete status
             */
            add_filter ( 'ywgc_custom_condition_to_create_gift_card', array(
                $this,
                'ywgc_custom_condition_to_create_gift_card_call_back'
            ), 10, 2 );

            add_action( 'save_post', array( $this, 'set_gift_card_category_to_product' ) );


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

            if ( wc_get_order_item_meta ( $item_id, '_ywgc_product_as_present', true ) && apply_filters( 'ywgc_show_product_as_gift_card_on_order', version_compare( wc_get_order_item_meta( $item_id, '_ywgc_version', true ), '2.0.0', '>' ), $item_id ) ) {

                $product_id = wc_get_order_item_meta ( $item_id, '_ywgc_present_product_id', true );

                $product_link = $product_id ? admin_url( 'post.php?post=' . $product_id . '&action=edit' ) : '';

                $product_title = "<a href='" . $product_link . "' >" . wc_get_product( $product_id )->get_name() . "</a> " . apply_filters( 'yith_wc_gift_card_as_a_gift_card', esc_html__( 'purchased as a Gift Card', 'yith-woocommerce-gift-cards' ) );

                ?>
                <div class="ywgc_order_sold_as_gift_card">
                    <?php  echo $product_title; ?>
                </div>
                <?php

            }

            $gift_ids = ywgc_get_order_item_giftcards ( $item_id );

			if ( empty( $gift_ids ) ) {
				return;
			}

			foreach ( $gift_ids as $gift_id ) {

				$gc = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_id ) );

				if ( ! $gc->is_pre_printed () ):
					?>
					<div>
					<span
						class="ywgc-gift-code-label"><?php _e ( "Gift card code: ", 'yith-woocommerce-gift-cards' ); ?></span>

						<a href="<?php echo admin_url ( 'edit.php?s=' . $gc->get_code () . '&post_type=gift_card&mode=list' ); ?>"
						   class="ywgc-card-code"><?php echo $gc->get_code (); ?></a>
					</div>
				<?php elseif ( apply_filters ( 'yith_ywgc_enter_pre_printed_gift_card_code', true, $theorder, $_product ) ): ?>
					<div>
					<span
						class="ywgc-gift-code-label"><?php _e ( "Enter the pre-printed code: ", 'yith-woocommerce-gift-cards' ); ?></span>
						<input type="text" name="ywgc-pre-printed-code[<?php echo $gc->ID; ?>]"
						       class="ywgc-pre-printed-code">
					</div>
				<?php endif;
			}
		}


		/**
		 * Enqueue scripts on administration comment page
		 *
		 * @param $hook
		 */
		function enqueue_backend_files( $hook ) {
			global $post_type;

			$screen = get_current_screen ();

			//  Enqueue style and script for the edit-gift_card screen id
			if ( "edit-gift_card" == $screen->id ) {

				//  When viewing the gift card page, store the max id so all new gift cards will be notified next time
				global $wpdb;
				$last_id = $wpdb->get_var ( $wpdb->prepare ( "SELECT max(id) FROM {$wpdb->prefix}posts WHERE post_type = %s", YWGC_CUSTOM_POST_TYPE_NAME ) );
				update_option ( self::YWGC_GIFT_CARD_LAST_VIEWED_ID, $last_id );
			}

			if ( isset( $_REQUEST[ 'page' ] ) && $_REQUEST[ 'page' ] == 'yith_woocommerce_gift_cards_panel' ) {

                //  Add style and scripts
                wp_enqueue_style ( 'ywgc_gift_cards_admin_panel_css',
                    YITH_YWGC_ASSETS_URL . '/css/ywgc-gift-cards-admin-panel.css',
                    array(),
                    YITH_YWGC_VERSION );

                wp_register_script ( "ywgc_gift_cards_admin_panel",

                    YITH_YWGC_SCRIPT_URL . yit_load_js_file ( 'ywgc-gift-cards-admin-panel.js' ),
                    array(
                        'jquery',
                        'jquery-blockui',
                    ),
                    YITH_YWGC_VERSION,
                    true );

                wp_localize_script ( 'ywgc_gift_cards_admin_panel',
                    'ywgc_data', array(
                        'loader'            => apply_filters ( 'yith_gift_cards_loader', YITH_YWGC_ASSETS_URL . '/images/loading.gif' ),
                        'ajax_url'          => admin_url ( 'admin-ajax.php' ),
                    )
                );

                wp_enqueue_script ( "ywgc_gift_cards_admin_panel" );

            }

			if ( ( 'product' == $post_type ) || ( 'gift_card' == $post_type ) || ( 'shop_order' == $post_type ) ||  isset( $_REQUEST[ 'page' ] ) && $_REQUEST[ 'page' ] == 'yith_woocommerce_gift_cards_panel' ) {

				//  Add style and scripts
				wp_enqueue_style ( 'ywgc-backend-css',
					YITH_YWGC_ASSETS_URL . '/css/ywgc-backend.css',
					array(),
					YITH_YWGC_VERSION );

				wp_register_script ( "ywgc-backend",

					YITH_YWGC_SCRIPT_URL . yit_load_js_file ( 'ywgc-backend.js' ),
					array(
						'jquery',
						'jquery-blockui',
					),
					YITH_YWGC_VERSION,
					true );

                $date_format =get_option( 'ywgc_plugin_date_format_option', 'yy-mm-dd' );

				wp_localize_script ( 'ywgc-backend',
					'ywgc_data', array(
						'loader'            => apply_filters ( 'yith_gift_cards_loader', YITH_YWGC_ASSETS_URL . '/images/loading.gif' ),
						'ajax_url'          => admin_url ( 'admin-ajax.php' ),
						'choose_image_text' => esc_html__( 'Choose Image', 'yith-woocommerce-gift-cards' ),
                        'date_format'   => $date_format,
					)
				);

				wp_enqueue_script ( "ywgc-backend" );
			}

			if ( "upload" == $screen->id ) {

				wp_register_script ( "ywgc-categories",
					YITH_YWGC_SCRIPT_URL . yit_load_js_file ( 'ywgc-categories.js' ),
					array(
						'jquery',
						'jquery-blockui',
					),
					YITH_YWGC_VERSION,
					true );

				$categories1_id = 'categories1_id';
				$categories2_id = 'categories2_id';

				wp_localize_script ( 'ywgc-categories', 'ywgc_data', array(
					'loader'                => apply_filters ( 'yith_gift_cards_loader', YITH_YWGC_ASSETS_URL . '/images/loading.gif' ),
					'ajax_url'              => admin_url ( 'admin-ajax.php' ),
					'set_category_action'   => esc_html__( "Set gift card image category", 'yith-woocommerce-gift-cards' ),
					'unset_category_action' => esc_html__( "Unset gift card image category", 'yith-woocommerce-gift-cards' ),
					'categories1'           => $this->get_category_select ( $categories1_id ),
					'categories1_id'        => $categories1_id,
					'categories2'           => $this->get_category_select ( $categories2_id ),
					'categories2_id'        => $categories2_id,
				) );

				wp_enqueue_script ( "ywgc-categories" );
			}


            if ( "edit-giftcard-category" == $screen->id ) {

                wp_enqueue_media();
                wp_register_script ( "ywgc-media-button",
                    YITH_YWGC_SCRIPT_URL . yit_load_js_file ( 'ywgc-media-button.js' ),
                    array(
                        'jquery',
                    ),
                    YITH_YWGC_VERSION,
                    true );

                wp_localize_script( 'ywgc-media-button', 'ywgc_data', array(
                        'upload_file_frame_title' => esc_html__( 'Manage the Media library', 'yith-woocommerce-gift-cards' ),
                        'upload_file_frame_button' => esc_html__( 'Done', 'yith-woocommerce-gift-cards' )
                ) );

                wp_enqueue_script ( "ywgc-media-button" );
            }


		}

		public function get_category_select( $select_id ) {
			$media_terms = get_terms ( YWGC_CATEGORY_TAXONOMY, 'hide_empty=0' );

			$select = '<select id="' . $select_id . '" name="' . $select_id . '">';
			foreach ( $media_terms as $entry ) {
				$select .= '<option value="' . $entry->term_id . '">' . $entry->name . '</option>';
			}
			$select .= '</select>';

			return $select;

		}

		/**
		 * Add the "Gift card" type to product type list
		 *
		 * @param array $types current type array
		 *
		 * @return mixed
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function add_gift_card_product_type( $types ) {
			if ( YITH_YWGC ()->current_user_can_create () ) {
				$types[ YWGC_GIFT_CARD_PRODUCT_TYPE ] = esc_html__( "Gift card", 'yith-woocommerce-gift-cards' );
			}

			return $types;
		}

		/**
		 * Save gift card additional data
		 *
		 * @param $product_id
		 */
		public function save_gift_card_data( $product_id ) {

			$product = new WC_Product_Gift_Card( $product_id );

			/**
			 * Save custom gift card header image, if exists
			 */
			if ( isset( $_REQUEST['ywgc_product_image_id'] ) ) {
				if ( intval ( $_REQUEST['ywgc_product_image_id'] ) ) {

					$product->set_header_image ( $_REQUEST['ywgc_product_image_id'] );
				} else {

					$product->unset_header_image ();
				}
			}


			/**
			 * Save gift card amounts
			 */
			$amounts = isset( $_POST["gift-card-amounts"] ) ? $_POST["gift-card-amounts"] : array();
			$product->save_amounts ( $amounts );

			/**
			 * Save gift card settings about template design
			 */
			if ( isset( $_POST['template-design-mode'] ) ) {
				$product->set_design_status ( $_POST['template-design-mode'] );
			}
		}


		/**
		 * Check if there are pre-printed gift cards that were filled and need to be updated
		 *
		 * @param $post_id
		 * @param $post
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function save_pre_printed_gift_card_code( $post_id, $post ) {

			if ( 'shop_order' != $post->post_type ) {
				return;
			}

			if ( ! isset( $_POST["ywgc-pre-printed-code"] ) ) {
				return;
			}

			$codes = $_POST["ywgc-pre-printed-code"];

			foreach ( $codes as $gift_id => $gift_code ) {
				if ( ! empty( $gift_code ) ) {
					$gc = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_id ) );

					$gc->gift_card_number = $gift_code;
					$gc->set_enabled_status ( true );
					$gc->save ();

					YITH_YWGC_Emails::get_instance ()->send_gift_card_email ( $gc );

				}
			}
		}


		/**
		 * Save gift card amount when a product is saved
		 *
		 * @param $post_id int
		 * @param $post    object
		 *
		 * @return mixed
		 */
		function save_gift_card( $post_id, $post ) {

			$product = wc_get_product ( $post_id );

			if ( null == $product ) {
				return;
			}

			if ( ! isset( $_POST["product-type"] ) || ( YWGC_GIFT_CARD_PRODUCT_TYPE != $_POST["product-type"] ) ) {

				return;
			}

			// verify this is not an auto save routine.
			if ( defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			/**
			 * Update gift card amounts
			 */
			$this->save_gift_card_data ( $post_id );


			do_action ( 'yith_gift_cards_after_product_save', $post_id, $post, $product );
		}


		/**
		 * Add a new amount to a gift card prdduct
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function add_gift_card_amount_callback() {

			$amount = wc_format_decimal ( $_POST['amount'] );

			if ( ! is_numeric( $amount ) )
				return;

			$product_id = intval ( $_POST['product_id'] );
			$gift       = new WC_Product_Gift_Card( $product_id );
			$res        = false;

			if ( $gift->exists () ) {
				$res = $gift->add_amount ( $amount );
			}

			wp_send_json (
				array(
					"code"  => $res ? 1 : 0,
					"value" => $this->gift_card_amount_list_html ( $product_id )
				) );
		}

		/**
		 * Remove amount to a gift card prdduct
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function remove_gift_card_amount_callback() {
			$amount     = wc_format_decimal ( $_POST['amount'] );
			$product_id = intval ( $_POST['product_id'] );

			$gift = new WC_Product_Gift_Card( $product_id );
			if ( $gift->exists () ) {
				$gift->remove_amount ( $amount );
			}

			wp_send_json ( array( "code" => '1' ) );
		}

		/**
		 * Retrieve the html content that shows the gift card amounts list
		 *
		 * @param $product_id int gift card product id
		 *
		 * @return string
		 */
		private function gift_card_amount_list_html( $product_id ) {

			ob_start ();
			$this->show_gift_card_amount_list ( $product_id );
			$html = ob_get_contents ();
			ob_end_clean ();

			return $html;
		}


		/**
		 * Hide some item meta from order edit page
		 */
		public function hide_item_meta( $args ) {
			$args[] = YWGC_META_GIFT_CARD_POST_ID;

			return $args;
		}

        /**
         * Show input to enter a discount for the gift card
         */
        public function show_sale_discount_settings( $product_id ) {

            $sale_discount = get_post_meta( $product_id, '_ywgc_sale_discount_value', true );

            ?>
            <p class="form-field sale-discount">
                <label for="gift_card-sale-discount"><?php _e ( "Sale discount (%)", 'yith-woocommerce-gift-cards' ); ?></label>
                <input type="number" id="gift_card-sale-discount" name="gift_card-sale-discount" value="<?php echo $sale_discount ?>" placeholder="<?php _e ( "Enter a discount percentage", 'yith-woocommerce-gift-cards' ); ?>">
                <span class="ywgc-form-field__description "><?php _e ( "Set an optional discount for this gift card.", 'yith-woocommerce-gift-cards' ); ?></span>
            </p>
            <?php
        }

        /**
         * Show input to enter a discount text for the gift card
         */
        public function show_sale_discount_text_settings( $product_id ) {

            $sale_discount_text = get_post_meta( $product_id, '_ywgc_sale_discount_text', true );

            ?>
            <p class="form-field sale-discount-text">
                <label for="gift_card-sale-discount-text"><?php _e ( "Sale discount text", 'yith-woocommerce-gift-cards' ); ?></label>
                <input type="text" id="gift_card-sale-discount-text" name="gift_card-sale-discount-text" style="width: 50%" value="<?php echo $sale_discount_text ?>" placeholder="<?php _e ( "Sale discount text", 'yith-woocommerce-gift-cards' ); ?>">
                <span class="ywgc-form-field__description "><?php _e ( "Enter a text  to describe your discount.", 'yith-woocommerce-gift-cards' ); ?></span>
            </p>
            <?php
        }


        public function show_gift_card_expiration_date_settings( $product_id ) {

            $expiration_date = get_post_meta( $product_id, '_ywgc_expiration_date', true );


            $expiration_date = is_string($expiration_date) ? strtotime($expiration_date) : $expiration_date;

            $date_format = apply_filters('yith_wcgc_date_format','Y-m-d');

            $expiration_date = !empty( $expiration_date ) ? date_i18n( $date_format, $expiration_date ) : '';

            ?>
            <p class="form-field expiration-date-field">
                <label for="gift-card-expiration-date"><?php _e ( "Expiration date", 'yith-woocommerce-gift-cards' ); ?></label>
                <input type="text" class="ywgc-expiration-date-picker" id="gift-card-expiration-date" name="gift-card-expiration-date" value="<?php echo $expiration_date;?>" data-min-date="<?php echo $expiration_date;?>" placeholder="<?php echo $date_format;?>">
                <span class="ywgc-form-field__description "><?php _e ( "Set an expiration date for this gift card.", 'yith-woocommerce-gift-cards' ); ?></span>
            </p>
            <?php
        }


        public function show_gift_card_minimal_manual_amount( $product_id ) {

            $minimal_amount = get_post_meta( $product_id, '_ywgc_minimal_manual_amount', true );

            ?>
            <p class="form-field minimal-amount-field">
                <label for="ywgc-minimal-amount"><?php _e ( "Minimum custom amount", 'yith-woocommerce-gift-cards' ); ?></label>
                <input type="number" class="ywgc-minimal-amount" id="ywgc-minimal-amount" name="ywgc-minimal-amount" value="<?php echo $minimal_amount;?>" placeholder="<?php _e ( "Enter a minimum amount", 'yith-woocommerce-gift-cards' ); ?>">
                <span class="ywgc-form-field__description "><?php _e ( "Set an optional minimum custom amount for this gift card.", 'yith-woocommerce-gift-cards' ); ?></span>
            </p>
            <?php
        }


        /**
		 * Show checkbox enabling the product to avoid use of free amount
		 */
		public function show_manual_amount_settings( $product_id ) {

			$product        = new WC_Product_Gift_Card( $product_id );
			$manual_mode    = $product->get_manual_amount_status ();
			$global_checked = ( $manual_mode == "global" ) || ( ( $manual_mode != "accept" ) && ( $manual_mode != "reject" ) );
			?>

			<p class="form-field permit_free_amount">
				<label for="ywgc-manual-amount-mode"><?php _e ( "Manual amount mode", 'yith-woocommerce-gift-cards' ); ?></label>
				<span class="wrap">
                    <input type="radio" class="ywgc-manual-amount-mode global-manual-mode" name="manual_amount_mode"
                           value="global" <?php checked ( $global_checked, true ); ?>>
                    <span style="margin-right: 8px;"><?php _e ( "Default", 'yith-woocommerce-gift-cards' ); ?></span>
                    <input type="radio" class="ywgc-manual-amount-mode accept-manual-mode" name="manual_amount_mode"
                           value="accept" <?php checked ( $manual_mode, "accept" ); ?>>
                    <span style="margin-right: 8px;"><?php _e ( "Enabled", 'yith-woocommerce-gift-cards' ); ?></span>
                    <input type="radio" class="ywgc-manual-amount-mode deny-manual-mode" name="manual_amount_mode"
                           value="reject" <?php checked ( $manual_mode, "reject" ); ?>>
                    <span><?php _e ( "Disabled", 'yith-woocommerce-gift-cards' ); ?></span>
                </span>
			</p>

			<?php

            //if this option is enabled -> $this->show_gift_card_minimal_manual_amount($product_id)
            $this->show_gift_card_minimal_manual_amount($product_id);
		}

		/**
		 * Show controls on backend product page to let create the gift card price
		 */
		public function show_gift_card_product_settings() {

			if ( ! YITH_YWGC ()->current_user_can_create () ) {
				return;
			}

			global $post, $thepostid;
			?>
			<div class="options_group show_if_gift-card">
				<p class="form-field">
					<label for="gift_card-amount"><?php _e ( "Gift card amount", 'yith-woocommerce-gift-cards' ); ?></label>
					<span class="wrap add-new-amount-section">
                    <input type="text" id="gift_card-amount" name="gift_card-amount" class="short wc_input_price" style=""
                           placeholder="">
                    <a href="#" class="add-new-amount"><?php _e ( "Add", 'yith-woocommerce-gift-cards' ); ?></a>
                    </span>
				</p>

				<?php
				$this->show_gift_card_amount_list ( $thepostid );
				do_action ( 'yith_ywgc_product_settings_after_amount_list', $thepostid );

				?>
			</div>
			<?php
		}

		/**
		 * Show the gift card amounts list
		 *
		 * @param $product_id int gift card product id
		 */
		private function show_gift_card_amount_list( $product_id ) {

			$gift_card = new WC_Product_Gift_Card( $product_id );
			if ( ! $gift_card->exists () ) {
				return;
			}
			$amounts = $gift_card->get_product_amounts ();

			?>

			<p class="form-field _gift_card_amount_field">
				<?php if ( $amounts ): ?>
					<?php foreach ( $amounts as $amount ) : ?>
						<span class="variation-amount"><?php echo wc_price ( $amount ); ?>
							<input type="hidden" name="gift-card-amounts[]" value="<?php _e ( $amount ); ?>">
                        <a href="#" class="remove-amount"></a></span>
					<?php endforeach; ?>
				<?php else: ?>
					<span
						class="no-amounts"><?php _e ( "You haven't configured any gift card yet", 'yith-woocommerce-gift-cards' ); ?></span>
				<?php endif; ?>
			</p>
			<?php
		}


		/**
		 * When the order is completed, generate a card number for every gift card product
		 *
		 * @param int|WC_Order $order      The order which status is changing
		 * @param string       $old_status Current order status
		 * @param string       $new_status New order status
		 *
		 */
		public function order_status_changed( $order, $old_status, $new_status ) {

			if ( is_numeric ( $order ) ) {
				$order = wc_get_order ( $order );
			}

			$allowed_status = apply_filters ( 'yith_ywgc_generate_gift_card_on_order_status',
				array( 'completed', 'processing' ) );

			if ( in_array ( $new_status, $allowed_status ) ) {
				$this->generate_gift_card_for_order ( $order );

				$used_gift_cards = yit_get_prop($order, '_ywgc_applied_gift_cards', true);

				if ( isset($used_gift_cards) && ! empty($used_gift_cards) ) {
                    $checkout_instance = YITH_YWGC_Cart_Checkout::get_instance();
				    foreach ($used_gift_cards as $gift_card_code => $value) {
                        $gift_card = YITH_YWGC()->get_gift_card_by_code( $gift_card_code );
                        $checkout_instance->notify_customer_if_gift_cards_used( $gift_card );
                    }
                }

			} elseif ( 'refunded' == $new_status ) {
				$this->change_gift_cards_status_on_order ( $order, YITH_YWGC ()->order_refunded_action() );
			} elseif ( 'cancelled' == $new_status ) {
				$this->change_gift_cards_status_on_order ( $order, YITH_YWGC ()->order_cancelled_action() );
			}
		}

		/**
		 * Generate the gift card code, if not yet generated
		 *
		 * @param WC_Order $order
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function generate_gift_card_for_order( $order ) {
			if ( is_numeric ( $order ) ) {
				$order = new WC_Order( $order );
			}

			if ( apply_filters ( 'yith_gift_cards_generate_on_order_completed', true, $order ) ) {

				$this->create_gift_cards_for_order ( $order );
			}
		}

        /**
         * start race condition
         *
         * @param int order_id
         *
         * @author Daniel Sanchez <daniel.sanchez@yithemes.com>
         * @since  2.0.3
         * @return bool
         */
        public function start_race_condition( $order_id ) {

            global $wpdb;

            $ywgc_race_condition_uniqid = uniqid();

            $sql = "UPDATE {$wpdb->postmeta} pm1, {$wpdb->postmeta} pm2
                SET pm1.meta_value = 'yes',
                    pm2.meta_value = %s
                WHERE pm1.post_id = %d
                    AND pm1.meta_key = %s
                    AND pm1.meta_value != 'yes'
                    AND pm2.post_id = %d
                    AND pm2.meta_key = %s
                ";

            $this->rc_active = $wpdb->query( $wpdb->prepare( $sql,
                $ywgc_race_condition_uniqid,
                $order_id,
                YWGC_RACE_CONDITION_BLOCKED,
                $order_id,
                YWGC_RACE_CONDITION_UNIQUID
            ) );

            if ( $this->rc_active ){

                $sub_sql = "SELECT meta_value FROM {$wpdb->postmeta}
                    WHERE post_id = %d
                    AND meta_key = %s
                ";

                $uniqid_result = $wpdb->get_results( $wpdb->prepare( $sub_sql,
                    $order_id,
                    YWGC_RACE_CONDITION_UNIQUID
                ) );

                if ( is_array( $uniqid_result ) && isset( $uniqid_result[ 0 ] ) && $uniqid_result[ 0 ]->meta_value != $ywgc_race_condition_uniqid )
                    return 0;

            }

            return 1;

        }

        /**
         * end race condition
         *
         * @param int order_id
         *
         * @author Daniel Sanchez <daniel.sanchez@yithemes.com>
         * @since  2.0.3
         */
        public function end_race_condition( $order_id ) {

            global $wpdb;

            if ( $this->rc_active ){

                $sql = "UPDATE {$wpdb->postmeta}
                SET meta_value = 'no'
                WHERE post_id = %d
                    AND meta_key = %s
                ";

                $result = $wpdb->query( $wpdb->prepare( $sql,
                    $order_id,
                    YWGC_RACE_CONDITION_BLOCKED
                ) );

            }

        }

        /**
         * Custom condition
         *
         * @param WC_Order $order
         * @return boolean
         *
         * @author Daniel Sanchez <daniel.sanchez@yithemes.com>
         * @since  2.0.6
         */
        public function ywgc_custom_condition_to_create_gift_card_call_back( $cond, $order ) {

            $gateway = wc_get_payment_gateway_by_order( $order );
            if ( $order->get_status() == 'processing' && is_object($gateway) && $gateway instanceof WC_Gateway_COD )
                return false;

            return true;

        }

		/**
		 * Create the gift cards for the order
		 *
		 * @param WC_Order $order
		 */
		public function create_gift_cards_for_order( $order ) {


            if ( ! apply_filters( 'ywgc_custom_condition_to_create_gift_card', true, $order ) )
                return;

            if ( apply_filters( 'ywgc_apply_race_condition', false ) )
                if ( ! $this->start_race_condition( $order->get_id() ) )
                    return;

			foreach ( $order->get_items ( 'line_item' ) as $order_item_id => $order_item_data ) {

                $product_id_alternative  = wc_get_order_item_meta ( $order_item_id, '_ywgc_product_id' );

                $product_id = $order_item_data["product_id"] != '' ? $order_item_data["product_id"] : $product_id_alternative;
                $product    = wc_get_product ( $product_id );

				//  skip all item that belong to product other than the gift card type
				if ( ! $product instanceof WC_Product_Gift_Card ) {
					continue;
				}

				//  Check if current product, of type gift card, has a previous gift card
				// code before creating another
                if ( $gift_ids = ywgc_get_order_item_giftcards ( $order_item_id ) ) {
                    continue;
                }

                if ( ! apply_filters ( 'yith_ywgc_create_gift_card_for_order_item', true, $order, $order_item_id, $order_item_data ) ) {
                    continue;
                }

                $is_product_as_present = wc_get_order_item_meta ( $order_item_id, '_ywgc_product_as_present', true );
                $present_product_id    = 0;
                $present_variation_id  = 0;

                if ( $is_product_as_present ) {
                    $present_product_id   = wc_get_order_item_meta ( $order_item_id, '_ywgc_present_product_id', true );
                    $present_variation_id = wc_get_order_item_meta ( $order_item_id, '_ywgc_present_variation_id', true );
                }

                $order_id = yit_get_order_id ( $order );

                $line_subtotal     = apply_filters ( 'yith_ywgc_line_subtotal', $order_item_data["line_subtotal"], $order_item_data, $order_id, $order_item_id );
                $line_subtotal_tax = apply_filters ( 'yith_ywgc_line_subtotal_tax', $order_item_data["line_subtotal_tax"], $order_item_data, $order_id, $order_item_id );

                //  Generate as many gift card code as the quantity bought
                $quantity      = $order_item_data["qty"];
                $single_amount = (float) ( $line_subtotal / $quantity );
                $single_tax    = (float) ( $line_subtotal_tax / $quantity );

                $new_ids = array();

                $order_currency = $order->get_currency();

                $product_id       = wc_get_order_item_meta ( $order_item_id, '_ywgc_product_id' );
                $amount           = wc_get_order_item_meta ( $order_item_id, '_ywgc_amount' );
                $is_manual_amount = wc_get_order_item_meta ( $order_item_id, '_ywgc_is_manual_amount' );
                $is_digital       = wc_get_order_item_meta ( $order_item_id, '_ywgc_is_digital' );

                $is_postdated = false;

				if ( $is_digital ) {
					$recipients        = apply_filters('ywgc_recipients_array_on_create_gift_cards_for_order', wc_get_order_item_meta ( $order_item_id, '_ywgc_recipients' ) );
					$recipient_count   = count ( $recipients );
					$sender            = wc_get_order_item_meta ( $order_item_id, '_ywgc_sender_name' );
					$recipient_name    = wc_get_order_item_meta ( $order_item_id, '_ywgc_recipient_name' );
					$message           = wc_get_order_item_meta ( $order_item_id, '_ywgc_message' );
					$has_custom_design = wc_get_order_item_meta ( $order_item_id, '_ywgc_has_custom_design' );
					$design_type       = wc_get_order_item_meta ( $order_item_id, '_ywgc_design_type' );
					$postdated         = apply_filters('ywgc_postdated_by_default', wc_get_order_item_meta ( $order_item_id, '_ywgc_postdated' ));

                    $is_postdated = true == apply_filters('ywgc_is_postdated_delivery_date_by_default', wc_get_order_item_meta ( $order_item_id, '_ywgc_postdated', true ));
                    if ( $is_postdated ) {
                        $delivery_date = wc_get_order_item_meta ( $order_item_id, '_ywgc_delivery_date', true );
                    }
				}

				for ( $i = 0; $i < $quantity; $i ++ ) {

					//  Generate a gift card post type and save it
					$gift_card = new YWGC_Gift_Card_Premium();

					$gift_card->product_id       = $product_id;
					$gift_card->order_id         = $order_id;
					$gift_card->is_digital       = $is_digital;
					$gift_card->is_manual_amount = $is_manual_amount;

					$gift_card->product_as_present = $is_product_as_present;
					if ( $is_product_as_present ) {
						$gift_card->present_product_id   = $present_product_id;
						$gift_card->present_variation_id = $present_variation_id;
					}

					if ( $gift_card->is_digital ) {
						$gift_card->sender_name        = $sender;
						$gift_card->recipient_name     = $recipient_name;
						$gift_card->message            = $message;
						$gift_card->postdated_delivery = $is_postdated;
						if ( $is_postdated ) {
							$gift_card->delivery_date = $delivery_date;
						}

						$gift_card->has_custom_design = $has_custom_design;
						$gift_card->design_type       = $design_type;

						if ( $has_custom_design ) {
							$gift_card->design = wc_get_order_item_meta ( $order_item_id, '_ywgc_design' );
						}

						$gift_card->postdated_delivery = $postdated;
						if ( $postdated ) {
							$gift_card->delivery_date = $delivery_date;
						}

						/**
						 * If the user entered several recipient email addresses, one gift card
						 * for every recipient will be created and it will be the unique recipient for
						 * that email. If only one, or none if allowed, recipient email address was entered
						 * then create '$quantity' specular gift cards
						 */
						if ( ( $recipient_count == 1 ) && ! empty( $recipients[0] ) ) {
							$gift_card->recipient = $recipients[0];
						} elseif ( ( $recipient_count > 1 ) && ! empty( $recipients[ $i ] ) ) {
							$gift_card->recipient = $recipients[ $i ];
						} else {
							/**
							 * Set the customer as the recipient of the gift card
							 *
							 */
							$gift_card->recipient = apply_filters ( 'yith_ywgc_set_default_gift_card_recipient', yit_get_prop($order, 'billing_email') );
						}
					}

					if (   "yes" == get_option ( "ywgc_enable_pre_printed", 'no' ) || apply_filters ( 'yith_ywgc_custom_condition_set_gift_card_as_preprinted', false, $gift_card ) ) {
						$gift_card->set_as_pre_printed ();
					} else {
						$attempts = 100;
						do {
							$code       = apply_filters( 'yith_wcgc_generated_code', YITH_YWGC ()->generate_gift_card_code(), $order, $gift_card );
							$check_code = get_page_by_title ( $code, OBJECT, YWGC_CUSTOM_POST_TYPE_NAME );

							if ( ! $check_code ) {
								$gift_card->gift_card_number = $code;
								break;
							}
							$attempts --;
						} while ( $attempts > 0 );

						if ( ! $attempts ) {
							//  Unable to find a unique code, the gift card need a manual code entered
							$gift_card->set_as_code_not_valid ();
						}
					}

					$gift_card->total_amount = $single_amount + $single_tax;

					// Add the default amount and not the converted one by WPML
					global $woocommerce_wpml;
					if ( $woocommerce_wpml && $woocommerce_wpml->multi_currency ) {
						$gift_card->total_amount = wc_get_order_item_meta ( $order_item_id, '_ywgc_default_currency_amount' );
					}

                    $on_sale = get_post_meta( $gift_card->product_id, '_ywgc_sale_discount_value', true );

                    if ( $on_sale ) {
                        $gift_card->total_amount =  wc_get_order_item_meta( $order_item_id, '_ywgc_amount_without_discount', true);
                    }

					$gift_card->update_balance ( $gift_card->total_amount );
					$gift_card->version  = YITH_YWGC_VERSION;
					$gift_card->currency = $order_currency;

                    $expiration_date = get_post_meta( $product_id, '_ywgc_expiration_date', true );

                    $expiration_date = strtotime($expiration_date);

                    if ( $expiration_date != '' ){

                        if ( $expiration_date == 0 ){
                            $gift_card->expiration =  0;
                        }
                        else{
                            $gift_card->expiration = $expiration_date;
                        }
                    }
					else{
                        try {
                            $usage_expiration      = apply_filters( 'ywgc_usage_expiration_in_months', get_option ( 'ywgc_usage_expiration', "" ), $gift_card, $product_id );
                            $start_usage_date      = $gift_card->delivery_date ? $gift_card->delivery_date : current_time('timestamp');
                            $gift_card->expiration = $usage_expiration != 0 ? strtotime ( "+$usage_expiration month", $start_usage_date ) : 0;
                        } catch ( Exception $e ) {
                            error_log ( 'An error occurred setting the expiration date for gift card: ' . $gift_card->gift_card_number );
                        }
                    }

                    do_action( 'yith_ywgc_before_gift_card_generation_save', $gift_card );


                    $gift_card->save ();

					do_action( 'yith_ywgc_after_gift_card_generation_save', $gift_card );

					//  Save the gift card id
					$new_ids[] = $gift_card->ID;

					//  ...and send it now if it's not postdated
					if ( (! $is_postdated && apply_filters( 'ywgc_send_gift_card_code_by_default', true, $gift_card) ) && $gift_card->get_code() != '' || apply_filters('yith_wcgc_send_now_gift_card_to_custom_recipient',false,$gift_card ) ) {

						YITH_YWGC_Emails::get_instance ()->send_gift_card_email ( $gift_card );
					}
				}

				// save gift card Post ids on order item
				ywgc_set_order_item_giftcards ( $order_item_id, $new_ids );

			}
            if ( apply_filters( 'ywgc_apply_race_condition', false ) )
                $this->end_race_condition( $order->get_id() );

		}


		/**
		 * The order is set to completed
		 *
		 * @param WC_Order $order
		 * @param string   $action
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function change_gift_cards_status_on_order( $order, $action ) {

			if ( 'nothing' == $action ) {
				return;
			}

			foreach ( $order->get_items () as $item_id => $item ) {
				$ids = ywgc_get_order_item_giftcards ( $item_id );

				if ( $ids ) {
					foreach ( $ids as $gift_id ) {

						$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_id ) );

						if ( ! $gift_card->exists () ) {
							continue;
						}

						if ( 'dismiss' == $action ) {
							$gift_card->set_dismissed_status ();
						} elseif ( 'disable' == $action ) {

							$gift_card->set_enabled_status ( false );
						}
					}
				}
			}
		}

		public function show_tax_class_for_gift_cards() {
			echo '<script>
                jQuery("select#_tax_status").closest(".options_group").addClass("show_if_gift-card");
            </script>';
		}

        public function set_gift_card_category_to_product( $post_id ) {

            //  Skip all request without an action
            if ( ! isset( $_REQUEST['action'] ) && ! isset( $_REQUEST['action2'] ) ) {
                return;
            }

            //  Skip all request without a valid action
            if ( ( '-1' == $_REQUEST['action'] ) && ( '-1' == $_REQUEST['action2'] ) ) {
                return;
            }

            $selected_catergories = isset( $_REQUEST['tax_input']['giftcard-category'] ) ? $_REQUEST['tax_input']['giftcard-category'] : array();

            $selected_catergories_serialized = serialize($selected_catergories);

            update_post_meta( $post_id, 'selected_images_categories', $selected_catergories_serialized );
        }

	}
}
