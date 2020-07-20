<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( ! class_exists( 'YITH_YWGC_Backend_Premium' ) ) {

	/**
	 *
	 * @class   YITH_YWGC_Backend_Premium
	 *
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_YWGC_Backend_Premium extends YITH_YWGC_Backend {
		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
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

			parent::__construct();

			/**
			 * Set the CSS class 'show_if_gift-card in 'sold indidually' section
			 */
			add_action( 'woocommerce_product_options_inventory_product_data', array(
				$this,
				'show_sold_individually_for_gift_cards'
			) );

			/**
			 * manage CSS class for the gift cards table rows
			 */
			add_filter( 'post_class', array( $this, 'add_cpt_table_class' ), 10, 3 );

			add_action( 'init', array( $this, 'redirect_gift_cards_link' ) );

			add_action( 'load-upload.php', array( $this, 'set_gift_card_category_to_media' ) );

			add_action( 'edited_term_taxonomy', array( $this, 'update_taxonomy_count' ), 10, 2 );

			/**
			 * Show icon that prompt the admin for a pre-printed gift cards buyed and whose code is not entered
			 */
			add_action( 'manage_shop_order_posts_custom_column', array(
				$this,
				'show_warning_for_pre_printed_gift_cards'
			) );

			/*
			 * Save additional product attribute when a gift card product is saved
			 */
			add_action( 'yith_gift_cards_after_product_save', array(
				$this,
				'save_gift_card_product'
			) );

			/**
			 * Show inventory tab in product tabs
			 */
			add_filter( 'woocommerce_product_data_tabs', array(
				$this,
				'show_inventory_tab'
			) );

			add_action( 'yith_ywgc_gift_card_email_sent', array(
				$this,
				'manage_bcc'
			) );

			add_action( 'yith_ywgc_product_settings_after_amount_list', array(
				$this,
				'show_advanced_product_settings'
			) );

			/**
			 * Show gift cards code and amount in order's totals section, in edit order page
			 */
			add_action( 'woocommerce_admin_order_totals_after_tax', array(
				$this,
				'show_gift_cards_total_before_order_totals'
			) );

			/**
			 * Add filters on the Gift Card Post Type page
			 */
			add_filter( 'views_edit-gift_card', array( $this, 'add_gift_cards_filters' ) );
			add_action( 'pre_get_posts', array( $this, 'filter_gift_card_page_query' ) );

			/*
			 * Filter display order item meta key to show
			 */
			add_filter( 'woocommerce_order_item_display_meta_key',array($this,'show_as_string_order_item_meta_key'),10,1 );

			/*
			 * Filter display order item meta value to show
			 */
			add_filter( 'woocommerce_order_item_display_meta_value', array( $this,'show_formatted_date' ),10,3 );


			add_action('woocommerce_order_status_changed', array($this,'update_gift_card_amount_on_order_status_change'),10,4);

			/*
			 * Recalculate order totals on save order items (in order to show always the correct total for the order)
			 */
			add_action('woocommerce_saved_order_items', array($this,'update_totals_on_save_order_items'),10,2);


			add_action( 'woocommerce_admin_field_yith_ywgc_transform_smart_coupons_html', array($this, 'yith_ywgc_transform_smart_coupons_buttons' ) );


			add_filter( 'yith_ywgc_general_options_array', array( $this, 'yith_ywgc_general_options_array_custom' ), 10, 1 ) ;


			//Ajax methods for Apply buttons
			add_action( 'wp_ajax_yith_convert_smart_coupons_button', array( $this, 'ywgc_convert_smart_coupons_to_gift_cards' ) );
			add_action( 'wp_ajax_nopriv_yith_convert_smart_coupons_button', array( $this, 'ywgc_convert_smart_coupons_to_gift_cards' ) );


			add_action( 'wp_ajax_ywgc_toggle_enabled_action', array( $this, 'ywgc_toggle_enabled_action' ) );
			add_action( 'wp_ajax_nopriv_ywgc_toggle_enabled_action', array( $this, 'ywgc_toggle_enabled_action' ) );


			add_action( 'wp_ajax_ywgc_update_cron', array( $this, 'ywgc_update_cron' ) );
			add_action( 'wp_ajax_nopriv_ywgc_update_cron', array( $this, 'ywgc_update_cron' ) );


//            add_action( 'add_tag_form', array( $this, 'ywgc_edit_design_category_form' ) );

			add_action( 'add_meta_boxes' ,  array( $this, 'ywgc_remove_product_meta_boxes' ), 40 );

			// Hidde the item meta in the order
			add_filter('woocommerce_hidden_order_itemmeta', array( $this, 'ywgc_hidden_order_item_meta' ), 10, 1);

			add_filter( 'woocommerce_order_get_tax_totals', array( $this, 'ywgc_recalculate_tax_totals' ), 10, 2 );

//			add_filter( 'woocommerce_order_get_total', array( $this, 'ywgc_recalculate_totals' ), 10, 2 );


		}


		/**
		 * Show gift cards code and amount in order's totals section, in edit order page
		 *
		 * @param int $order_id
		 */
		public function show_gift_cards_total_before_order_totals( $order_id ) {

			$order            = wc_get_order( $order_id );
			$order_gift_cards = yit_get_prop( $order, YITH_YWGC_Cart_Checkout::ORDER_GIFT_CARDS, true );
			$currency         = $order->get_currency();

			if ( $order_gift_cards ) :
				foreach ( $order_gift_cards as $code => $amount ): ?>
					<?php $amount = apply_filters('ywgc_gift_card_amount_order_total_item', $amount, YITH_YWGC()->get_gift_card_by_code( $code ) ); ?>
					<tr>
						<td class="label"><?php _e( 'Gift card: ' . $code, 'yith-woocommerce-gift-cards' ); ?>:</td>
						<td width="1%"></td>
						<td class="total">
							<?php echo wc_price( $amount, array( 'currency' => $currency ) ); ?>
						</td>
					</tr>
				<?php endforeach;
			endif;
		}

		/**
		 * Send a copy of gift card email to additional recipients, if set
		 *
		 * @param $gift_card
		 */
		public function manage_bcc( $gift_card ) {

			$this->notify_customer_if_gift_cards_is_delivered( $gift_card );

			$order = new WC_Order( $gift_card->order_id );

			$recipients = array();

			//  Check if the option is set to add the admin email
			if ( get_option ( "ywgc_blind_carbon_copy", 'no' ) == "yes" ) {
				$recipients[] = get_option( 'admin_email' );
			}

			//  Check if the option is set to add the gift card buyer email
			if ( get_option ( "ywgc_blind_carbon_copy_to_buyer", 'no' ) == "yes" && $gift_card->recipient != $order->get_billing_email()) {
				$recipients[] = $order->get_billing_email();
			}

			$additional_emails = get_option ( "ywgc_blind_carbon_copy_additionals", '' );

			if ( $additional_emails != "" ){
				$emails_array = explode(',', $additional_emails);
				foreach ( $emails_array as $email ){
					$recipients[] = $email;
				}
			}

			$recipients = apply_filters( 'yith_ywgc_bcc_additional_recipients', $recipients );
			if ( empty( $recipients ) )
				return;

			WC()->mailer();

			foreach ( $recipients as $recipient ) {
				//  Send a copy of the gift card to the recipient
				$gift_card->recipient = $recipient;
				do_action( 'ywgc-email-send-gift-card_notification', $gift_card, 'BCC' );
			}
		}

		public function notify_customer_if_gift_cards_is_delivered( $gift_card ) {

			if ( "yes" == get_option ( 'ywgc_delivery_notify_customer' , 'no' ) ) {

				if ( $gift_card->exists() ) {
					WC()->mailer();
					do_action( 'ywgc-email-delivered-gift-card', $gift_card );
				}
			}
		}

		/**
		 * Show inventory section for gift card products
		 *
		 * @param array $tabs
		 *
		 * @return mixed
		 */
		public function show_inventory_tab( $tabs ) {
			if ( isset( $tabs['inventory'] ) ) {

				array_push( $tabs['inventory']['class'], 'show_if_gift-card' );
			}

			return $tabs;

		}

		/**
		 * Save additional product attribute when a gift card product is saved
		 *
		 * @param int $post_id current product id
		 */
		public function save_gift_card_product( $post_id ) {

			//	Save the flag for manual amounts when the product is saved
			if ( isset( $_POST["manual_amount_mode"] ) ) {
				$product = new WC_Product_Gift_Card( $post_id );

				$product->update_manual_amount_status( $_POST["manual_amount_mode"] );
			}

			if ( isset( $_POST["gift_card-sale-discount"] ) ) {
				update_post_meta( $post_id, '_ywgc_sale_discount_value', $_POST["gift_card-sale-discount"] );
			}

			if ( isset( $_POST["gift_card-sale-discount-text"] ) ) {
				update_post_meta( $post_id, '_ywgc_sale_discount_text', $_POST["gift_card-sale-discount-text"] );
			}

			if ( isset( $_POST["gift-card-expiration-date"] ) ) {

				$date_format = apply_filters('yith_wcgc_date_format','Y-m-d');

				$expiration_date = is_string($_POST["gift-card-expiration-date"]) ? strtotime($_POST["gift-card-expiration-date"]) : $_POST["gift-card-expiration-date"];

				$expiration_date_formatted = !empty( $expiration_date ) ? date_i18n( $date_format, $expiration_date ) : '';

				update_post_meta( $post_id, '_ywgc_expiration', $expiration_date );

				update_post_meta( $post_id, '_ywgc_expiration_date', $expiration_date_formatted );
			}

			if ( isset( $_POST["ywgc-minimal-amount"] ) ) {
				update_post_meta( $post_id, '_ywgc_minimal_manual_amount', $_POST["ywgc-minimal-amount"] );
			}

		}

		/**
		 * Show icon on backend page "orders" for order where there is file uploaded and waiting to be confirmed.
		 *
		 * @param string $column current column being shown
		 */
		public function show_warning_for_pre_printed_gift_cards( $column ) {
			//  If column is not of type order_status, skip it
			if ( 'order_status' !== $column ) {
				return;
			}

			global $the_order;
			if ( !empty( $the_order ) && ( $the_order instanceof WC_Order ) ){
				$count = $this->pre_printed_cards_waiting_count( $the_order );
				if ( $count ) {
					$message = _n( "This order contains one pre-printed gift card that needs to be filled", sprintf( "This order contains %d pre-printed gift cards that needs to be filled", $count ), $count, 'yith-woocommerce-gift-cards' );
					echo '<img class="ywgc-pre-printed-waiting" src="' . YITH_YWGC_ASSETS_IMAGES_URL . 'waiting.png" title="' . $message . '" />';
				}
			}
		}

		/**
		 * Retrieve the number of pre-printed gift cards that are not filled
		 *
		 * @param WC_Order $order
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 * @return int
		 */
		private function pre_printed_cards_waiting_count( $order ) {
			$order_items = $order->get_items( 'line_item' );
			$count       = 0;

			foreach ( $order_items as $order_item_id => $order_data ) {
				$gift_ids = ywgc_get_order_item_giftcards( $order_item_id );

				if ( empty( $gift_ids ) ) {
					return;
				}

				foreach ( $gift_ids as $gift_id ) {

					$gc = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_id ) );

					if ( $gc->is_pre_printed() ) {
						$count ++;
					}
				}
			}

			return $count;
		}

		/**
		 * Fix the taxonomy count of items
		 *
		 * @param $term_id
		 * @param $taxonomy_name
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function update_taxonomy_count( $term_id, $taxonomy_name ) {
			//  Update the count of terms for attachment taxonomy
			if ( YWGC_CATEGORY_TAXONOMY != $taxonomy_name ) {
				return;
			}

			//  update now
			global $wpdb;
			$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts p1 WHERE p1.ID = $wpdb->term_relationships.object_id AND ( post_status = 'publish' OR ( post_status = 'inherit' AND (post_parent = 0 OR (post_parent > 0 AND ( SELECT post_status FROM $wpdb->posts WHERE ID = p1.post_parent ) = 'publish' ) ) ) ) AND post_type = 'attachment' AND term_taxonomy_id = %d", $term_id ) );

			$wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term_id ) );
		}


		public function set_gift_card_category_to_media() {

			//  Skip all request without an action
			if ( ! isset( $_REQUEST['action'] ) && ! isset( $_REQUEST['action2'] ) ) {
				return;
			}

			//  Skip all request without a valid action
			if ( ( '-1' == $_REQUEST['action'] ) && ( '-1' == $_REQUEST['action2'] ) ) {
				return;
			}

			$action = '-1' != $_REQUEST['action'] ? $_REQUEST['action'] : $_REQUEST['action2'];

			//  Skip all request that do not belong to gift card categories
			if ( ( 'ywgc-set-category' != $action ) && ( 'ywgc-unset-category' != $action ) ) {
				return;
			}

			//  Skip all request without a media list
			if ( ! isset( $_REQUEST['media'] ) ) {
				return;
			}

			$media_ids = $_REQUEST['media'];

			//  Check if the request if for set or unset the selected category to the selected media
			$action_set_category = ( 'ywgc-set-category' == $action ) ? true : false;

			//  Retrieve the category to be applied to the selected media
			$category_id = '-1' != $_REQUEST['action'] ? intval( $_REQUEST['categories1_id'] ) : intval( $_REQUEST['categories2_id'] );

			foreach ( $media_ids as $media_id ) {

				// Check whether this user can edit this post
				//if ( ! current_user_can ( 'edit_post', $media_id ) ) continue;

				if ( $action_set_category ) {
					$result = wp_set_object_terms( $media_id, $category_id, YWGC_CATEGORY_TAXONOMY, true );
				} else {
					$result = wp_remove_object_terms( $media_id, $category_id, YWGC_CATEGORY_TAXONOMY );
				}

				if ( is_wp_error( $result ) ) {
					return $result;
				}
			}
		}

		/**
		 * manage CSS class for the gift cards table rows
		 *
		 * @param array  $classes
		 * @param string $class
		 * @param int    $post_id
		 *
		 * @return array|mixed|void
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function add_cpt_table_class( $classes, $class, $post_id ) {

			if ( YWGC_CUSTOM_POST_TYPE_NAME != get_post_type( $post_id ) ) {
				return $classes;
			}

			$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $post_id ) );

			if ( ! $gift_card->exists() ) {
				return $class;
			}

			$classes[] = $gift_card->status;

			return apply_filters( 'yith_gift_cards_table_class', $classes, $post_id );
		}


		/**
		 * Make some redirect based on the current action being performed
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function redirect_gift_cards_link() {

			/**
			 * Check if the user ask for downloading the gift pdf file
			 */
			if ( isset( $_GET[ YWGC_ACTION_DOWNLOAD_PDF ] ) ) {

				$gift_id = $_GET[ 'id' ];
				$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_id ) );

				$new_file = YITH_YWGC()->create_gift_card_pdf_file( $gift_card );

				header('Content-type:  application/pdf');
				header('Content-Length: ' . filesize( $new_file ) );
				header('Content-Disposition: attachment; filename="' . basename( $new_file ) . '"');
				readfile( $new_file );

				ignore_user_abort( true );
				( connection_aborted() ? unlink( $new_file ) : unlink( $new_file ) );

				exit;

			}

			/**
			 * Check if the user ask for retrying sending the gift card email that are not shipped yet
			 */
			if ( isset( $_GET[ YWGC_ACTION_RETRY_SENDING ] ) ) {

				$gift_card_id = $_GET['id'];

				YITH_YWGC_Emails::get_instance()->send_gift_card_email( $gift_card_id, false );
				$redirect_url = remove_query_arg( array( YWGC_ACTION_RETRY_SENDING, 'id' ) );

				wp_redirect( $redirect_url );
				exit;
			}

			/**
			 * Check if the user ask for enabling/disabling a specific gift cards
			 */
			if ( isset( $_GET[ YWGC_ACTION_ENABLE_CARD ] ) || isset( $_GET[ YWGC_ACTION_DISABLE_CARD ] ) ) {

				$gift_card_id = $_GET['id'];
				$enabled      = isset( $_GET[ YWGC_ACTION_ENABLE_CARD ] );

				$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_card_id ) );

				if ( ! $gift_card->is_dismissed() ) {

					$current_status = $gift_card->is_enabled();

					if ( $current_status != $enabled ) {

						$gift_card->set_enabled_status( $enabled );
						do_action( 'yith_gift_cards_status_changed', $gift_card, $enabled );
					}

					wp_redirect( remove_query_arg( array(
						YWGC_ACTION_ENABLE_CARD,
						YWGC_ACTION_DISABLE_CARD,
						'id'
					) ) );
					die();
				}
			}

			if ( ! isset( $_GET["post_type"] ) || ! isset( $_GET["s"] ) ) {
				return;
			}


			if ( 'shop_coupon' != ( $_GET["post_type"] ) ) {
				return;
			}

			if ( preg_match( "/(\w{4}-\w{4}-\w{4}-\w{4})(.*)/i", $_GET["s"], $matches ) ) {
				wp_redirect( admin_url( 'edit.php?s=' . $matches[1] . '&post_type=gift_card' ) );
				die();
			}
		}


		public function show_sold_individually_for_gift_cards() {
			?>
			<script>
				jQuery("#_sold_individually").closest(".options_group").addClass("show_if_gift-card");
				jQuery("#_sold_individually").closest(".form-field").addClass("show_if_gift-card");
			</script>
			<?php
		}

		/**
		 * Show advanced product settings
		 *
		 * @param int $thepostid
		 */
		public function show_advanced_product_settings( $thepostid ) {

			$this->show_manual_amount_settings( $thepostid );

			$this->show_sale_discount_settings( $thepostid );
			$this->show_sale_discount_text_settings( $thepostid );

			$this->show_gift_card_expiration_date_settings( $thepostid );


		}

		/**
		 * Add filters on the Gift Card Post Type page
		 *
		 * @param $views
		 *
		 * @return mixed
		 */

		public function add_gift_cards_filters( $views ) {
			global $wpdb;
			$args = array(
				'post_status' => 'published',
				'post_type'   => 'gift_card',
				'balance'     => 'active'
			);

			$count_active = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( DISTINCT( post_id ) ) FROM {$wpdb->postmeta} AS pm LEFT JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id WHERE meta_key = %s AND meta_value <> 0 AND p.post_type= %s", '_ywgc_balance_total', 'gift_card' ) );
			$count_used   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( DISTINCT( post_id ) ) FROM {$wpdb->postmeta} AS pm LEFT JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id WHERE meta_key = %s AND ROUND(meta_value, %d) = 0 AND p.post_type= %s", '_ywgc_balance_total', wc_get_price_decimals(), 'gift_card' ) );

			$views['active'] = sprintf( '<a href="%s">%s <span class="count">(%d)</span></a>', add_query_arg( $args, admin_url( 'edit.php' ) ), esc_html__( 'Active', 'yith-woocommerce-gift-cards' ), $count_active );
			$args['balance'] = 'used';
			$views['used']   = sprintf( '<a href="%s">%s <span class="count">(%d)</span></a>', add_query_arg( $args, admin_url( 'edit.php' ) ), esc_html__( 'Used', 'yith-woocommerce-gift-cards' ), $count_used );

			return $views;
		}


		/**
		 * Add filters on the Gift Card Post Type page
		 *
		 * @param $query
		 */

		public function filter_gift_card_page_query( $query ) {
			global $pagenow, $post_type;

			if ( $pagenow == 'edit.php' && $post_type == 'gift_card' && isset( $_GET['balance'] ) && in_array( $_GET['balance'], array(
					'used',
					'active'
				) ) ) {
				if ( 'active' == $_GET['balance'] ) {
					$meta_query = array(
						array(
							'key'     => '_ywgc_balance_total',
							'value'   => 0,
							'compare' => '>'
						)
					);
				} else {
					$meta_query = array(
						array(
							'key'     => '_ywgc_balance_total',
							'value'   => pow( 10, - wc_get_price_decimals() ),
							'compare' => '<'
						)
					);
				}

				$query->set( 'meta_query', $meta_query );
			}
		}

		/**
		 * Hide item meta from the orders.
		 */
		public function ywgc_hidden_order_item_meta( $meta_array ) {
			$meta_array[] = '_ywgc_design';

			return apply_filters( 'yith_cog_order_item_meta', $meta_array );
		}


		/**
		 * Localize order item meta and show theme as strings
		 *
		 * @param $display_key
		 * @param $meta
		 * @param $order_item
		 * @return string|void
		 */
		public function show_as_string_order_item_meta_key($display_key){
			if( strpos($display_key,'ywgc') !== false){
				if( $display_key == '_ywgc_product_id' ){
					$display_key = esc_html__('Product ID','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_product_as_present' ){
					$display_key = esc_html__('Product as a present','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_present_product_id' ){
					$display_key = esc_html__('Present product ID','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_present_variation_id' ){
					$display_key = esc_html__('Present variation ID','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_amount' ){
					$display_key = esc_html__('Amount','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_is_digital' ){
					$display_key = esc_html__('Digital','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_sender_name' ){
					$display_key = esc_html__('Sender\'s name','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_recipient_name' ){
					$display_key = esc_html__('Recipient\'s name','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_message' ){
					$display_key = esc_html__('Message','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_design_type' ){
					$display_key = esc_html__('Design type','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_design' ){
					$display_key = esc_html__('Design','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_subtotal' ){
					$display_key = esc_html__('Subtotal','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_subtotal_tax' ){
					$display_key = esc_html__('Subtotal tax','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_version' ){
					$display_key = esc_html__('Version','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_delivery_date' ){
					$display_key = esc_html__('Delivery date','yith-woocommerce-gift-cards');
				}
				elseif( $display_key == '_ywgc_postdated' ){
					$display_key = esc_html__('Postdated','yith-woocommerce-gift-cards');
				}


			}
			return $display_key;
		}

		/**
		 * Format date to show as meta value in order page
		 * @param $meta_value
		 * @param $meta
		 * @return mixed
		 */
		public function show_formatted_date( $meta_value, $meta ="", $item="" ){

			if( '_ywgc_delivery_date' == $meta->key ){
				$date_format = apply_filters( 'yith_wcgc_date_format','Y-m-d' );
				$meta_value = date_i18n( $date_format,$meta_value ) . ' (' . $date_format . ')';
			}

			return $meta_value;

		}

		/**
		 * Update gift card amount in case the order is cancelled or refunded
		 * @param $order_id
		 * @param $from_status
		 * @param $to_status
		 * @param bool $order
		 */
		public function update_gift_card_amount_on_order_status_change( $order_id, $from_status, $to_status, $order = false ){
			$is_gift_card_amount_refunded = yit_get_prop($order,'_ywgc_is_gift_card_amount_refunded');
			if( ($to_status == 'cancelled' || ( $to_status == 'refunded' ) || ( $to_status == 'failed' )) && $is_gift_card_amount_refunded != 'yes' ){
				$gift_card_applied = yit_get_prop( $order,'_ywgc_applied_gift_cards',true );
				if (empty($gift_card_applied)) {
					return;
				}

				foreach ($gift_card_applied as $gift_card_code => $gift_card_value  ){
					$args = array(
						'gift_card_number' => $gift_card_code
					);
					$gift_card = new YITH_YWGC_Gift_Card( $args );
					$new_amount = $gift_card->get_balance() + $gift_card_value;
					$gift_card->update_balance( $new_amount );
				}

				yit_save_prop($order,'_ywgc_is_gift_card_amount_refunded','yes');
			}
		}


		public function update_totals_on_save_order_items( $order_id, $items ){

			if( isset( $items['order_status'] ) && $items[ 'order_status' ] == 'wc-refunded' )
				return;

			$order = wc_get_order( $order_id );

			$used_gift_cards = get_post_meta( $order_id, '_ywgc_applied_gift_cards', true);

			if (! $used_gift_cards )
				return;

			$order_total = $order->get_total();
			$applied_gift_card_amount = yit_get_prop( $order,'_ywgc_applied_gift_cards_totals' );

			$updated_total = $order_total - $applied_gift_card_amount;

			$order->set_total( $updated_total );

			$order->apply_changes();
			$order->save();
		}


		public function ywgc_convert_smart_coupons_to_gift_cards(){

			global $wpdb;

			$date_format = apply_filters('yith_wcgc_date_format','Y-m-d');

			$this->offset  = intval( $_POST['offset'] );
			$this->limit   = intval( $_POST['limit'] );

			if ( $this->limit == 0 ) {
				$this->limit = 50 ;
			}

			$query_coupons = "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key= 'discount_type' AND meta_value= 'smart_coupon'";
			$coupons_array = $wpdb->get_results($query_coupons);
			$total_coupons_number = count($coupons_array);

			if ( $this->limit > $total_coupons_number ){
				$counter = $total_coupons_number;
			}
			else{
				$counter = $this->offset + $this->limit;
			}

			foreach ($coupons_array as $coupons) {

				$coupon_id = $coupons->post_id;

				for ($i = $this->offset; $i < $counter; $i++) {
					$coupon_amount = get_post_meta($coupon_id, 'coupon_amount', true);
					$recipient_emails_array = get_post_meta($coupon_id, 'customer_email', true);
					$expiration_timestamp = get_post_meta($coupon_id, 'date_expires', true);
					$expiration_formatted = $expiration_timestamp != '0' ? date_i18n ( $date_format, $expiration_timestamp ) : '';
					$coupon_code = get_the_title($coupon_id);
				}

				$query_duplicated_post = "SELECT ID FROM {$wpdb->posts} WHERE post_title = '{$coupon_code}' AND post_type = 'gift_card' ";
				$duplicated_post_array = $wpdb->get_results($query_duplicated_post);

				foreach ($duplicated_post_array as $duplicated_post) {
					$duplicated_post_id = $duplicated_post->ID;
				}
				if ( $coupon_code == get_the_title( $duplicated_post_id ) ) {
					continue;
				}
				else{
					$new_draft_post = array(
						'post_title' => $coupon_code,
						'post_status' => 'draft',
						'post_type' => 'gift_card',
					);

					$post_id = wp_insert_post($new_draft_post);

					$updated_post = array(
						'ID' => $post_id,
						'post_title' => $coupon_code,
						'post_status' => 'publish',
						'post_type' => 'gift_card'
					);

					wp_update_post($updated_post);
					update_post_meta($post_id, '_ywgc_amount_total', $coupon_amount);
					update_post_meta($post_id, '_ywgc_balance_total', $coupon_amount);
					update_post_meta($post_id, '_ywgc_is_digital', '1');
					update_post_meta($post_id, '_ywgc_expiration', $expiration_timestamp);
					update_post_meta($post_id, '_ywgc_expiration_date_formatted', $expiration_formatted);
					update_post_meta($post_id, '_ywgc_recipient', $recipient_emails_array['0']);

				}
			}

			$new_offset = $this->offset + $this->limit;

			if (($total_coupons_number - $new_offset) < $this->limit){
				$this->limit = $total_coupons_number - $new_offset;
			}

			if ( $new_offset < $total_coupons_number ){
				$data=array(
					"limit"=> "$this->limit",
					"offset" => "$new_offset",
					"loop" => "1",
				);

				wp_send_json( $data );
			}
			else{
				$data=array(
					"limit"=> "$this->limit",
					"offset" => "$new_offset",
					"loop" => "0",
				);
				wp_send_json( $data );
			}
		}

		/**
		 * Render the import cost buttons.
		 */
		public function yith_ywgc_transform_smart_coupons_buttons(){

			if ( class_exists( 'WC_Smart_Coupons' ) ) {
				?>
				<h2><?php _e( 'WooCommerce Smart Coupons integration', 'yith-woocommerce-gift-cards' );?></h2>
				<tr id="ywgc_ajax_zone_transform_smart_coupons">
					<th>
						<label><?php _e( 'Transfer WooCommerce Smart Coupons to YITH Gift Cards ', 'yith-woocommerce-gift-cards' );?></label>
					</th>
					<td>
						<button type="button" class="ywgc_transform_smart_coupons_class button button-primary" id="yith_ywgc_transform_smart_coupons" >Transfer</button>
						<span class="description"><?php _e( 'Transfer "Store Credit / Gift Certificate" coupons. This action cannot be undone', 'yith-woocommerce-gift-cards' ); ?></span>
					</td>
				</tr>
				<?php
			}
		}


		/**
		 * Currency Switchers options
		 */
		public function yith_ywgc_general_options_array_custom( $general_options ) {

			if ( class_exists('WC_Aelia_CurrencySwitcher') ) {

				$aux = array(
					'aelia_currency_switchers_tab_start'    => array(
						'type' => 'sectionstart',
						'id'   => 'yith_aelia_currency_switchers_settings_tab_start'
					),
					'aelia_currency_switchers_tab_title'    => array(
						'type'  => 'title',
						'name' => esc_html__( 'Aelia Currency Switcher integration', 'yith-woocommerce-gift-cards' ),
						'desc'  => '',
						'id'    => 'yith_aelia_currency_switchers_tab'
					),
					'enable_aelia_option' => array(
						'name'    => esc_html__( 'Enable Aelia integration', 'yith-woocommerce-gift-cards' ),
						'type'    => 'checkbox',
						'id'      => 'ywgc_aelia_integration_option',
						'default' => 'yes',
					),

					'aelia_currency_switchers_tab_end'      => array(
						'type' => 'sectionend',
						'id'   => 'yith_aelia_currency_switchers_settings_tab_end'
					),
				);

				$general_options['general'] = array_merge( $general_options['general'], $aux );

			}

			global $woocommerce_wpml;
			if ( $woocommerce_wpml ) {

				$aux = array(
					'wpml_currency_switchers_tab_start' => array(
						'type' => 'sectionstart',
						'id' => 'yith_wpml_currency_switchers_settings_tab_start'
					),
					'wpml_currency_switchers_tab_title' => array(
						'type' => 'title',
						'name' => esc_html__('WPML Currency Switcher integration', 'yith-woocommerce-gift-cards'),
						'desc' => '',
						'id' => 'yith_wpml_currency_switchers_tab'
					),
					'enable_wpml_option' => array(
						'name' => esc_html__('Enable WPML integration', 'yith-woocommerce-gift-cards'),
						'type' => 'checkbox',
						'id' => 'ywgc_wpml_integration_option',
						'default' => 'yes',
					),

					'wpml_currency_switchers_tab_end' => array(
						'type' => 'sectionend',
						'id' => 'yith_wpml_currency_switchers_settings_tab_end'
					),
				);

				$general_options['general'] = array_merge( $general_options['general'], $aux );
			}

			return $general_options;
		}


		public function ywgc_edit_design_category_form() {
			global $current_screen;

			if ( $current_screen->id == 'edit-giftcard-category' ){
				?>
				<div>
					<h2><?php echo esc_html__('Manage the gift card images through the WordPress Media Library', 'yith-woocommerce-gift-cards');?></h2>
					<br>
					<button id="ywgc-media-upload-button" class="button" style="padding: 3px 15px;"><span class="dashicons dashicons-admin-media"></span><?php echo ' ' .  esc_html__('Manage media', 'yith-woocommerce-gift-cards'); ?></button>
					<p><?php echo esc_html__('Upload/manage images in the WordPress Media Library and include them in the existing gift card categories.', 'yith-woocommerce-gift-cards');  ?></p>
				</div>
				<?php
			}
		}

		function ywgc_remove_product_meta_boxes(){

			$product = wc_get_product(get_the_ID());

			if (is_object($product) &&  $product->get_type() == 'gift-card' && apply_filters( 'ywgc_remove_gallery_metabox_condition' , true ) ) {
				remove_meta_box('woocommerce-product-images', 'product', 'side');
			}
			if (is_object($product) && $product->get_type() != 'gift-card' ) {
				remove_meta_box('giftcard-categorydiv', 'product', 'side');
			}
		}


		public function ywgc_toggle_enabled_action(){

			if ( isset( $_POST['id'] ) && isset( $_POST['enabled'] ) && $_POST['enabled'] == 'no' ){
				$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $_POST['id'] ) );
				$gift_card->set_enabled_status(false);
			}
			else if ( isset( $_POST['id'] ) && isset( $_POST['enabled'] ) && $_POST['enabled'] == 'yes') {
				$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $_POST['id'] ) );
				$gift_card->set_enabled_status(true);
			}

		}

		public function ywgc_update_cron(){

			if ( $_POST['interval_mode'] == 'hourly' ){

				update_option( 'ywgc_delivery_mode', 'hourly' );

				wp_clear_scheduled_hook ( 'ywgc_start_gift_cards_sending' );

				wp_schedule_event( time() , 'hourly', 'ywgc_start_gift_cards_sending' );

			}
			else{

				update_option( 'ywgc_delivery_mode', 'daily' );
				update_option( 'ywgc_delivery_hour', $_POST['hour'] );

				$hour = strtotime( get_option( 'ywgc_delivery_hour', '00:00' ) );
				wp_clear_scheduled_hook ( 'ywgc_start_gift_cards_sending' );

				wp_schedule_event(strtotime('-' . get_option( 'gmt_offset' ) . ' hours', $hour ) , 'daily', 'ywgc_start_gift_cards_sending' );
			}

		}

		public function ywgc_recalculate_tax_totals( $tax_totals , $order ){


			global $current_screen;

			if ( ! empty( $current_screen ) && 'shop_order' == $current_screen->id ) {

				$gift_card_applied = yit_get_prop( $order, '_ywgc_applied_gift_cards', true );

				$aux_tax_total = get_post_meta( $order->get_id(), '_ywgc_aux_cart_total_tax', true );

				if ( empty( $aux_tax_total ) ) {
					return $tax_totals;
				}

				$tax_totals = array();

				foreach ( $order->get_items( 'tax' ) as $key => $tax ) {
					$code = $tax->get_rate_code();

					if ( ! isset( $tax_totals[ $code ] ) ) {
						$tax_totals[ $code ]         = new stdClass();
						$tax_totals[ $code ]->amount = 0;
					}

					$tax_totals[ $code ]->id               = $key;
					$tax_totals[ $code ]->rate_id          = $tax->get_rate_id();
					$tax_totals[ $code ]->is_compound      = $tax->is_compound();
					$tax_totals[ $code ]->label            = $tax->get_label();
					$tax_totals[ $code ]->amount           += (float) $aux_tax_total;
					$tax_totals[ $code ]->formatted_amount = wc_price( wc_round_tax_total( $tax_totals[ $code ]->amount ), array( 'currency' => $order->get_currency() ) );
				}

				if ( apply_filters( 'woocommerce_order_hide_zero_taxes', true ) ) {
					$amounts    = array_filter( wp_list_pluck( $tax_totals, 'amount' ) );
					$tax_totals = array_intersect_key( $tax_totals, $amounts );
				}

				return $tax_totals;

			}
			else{
				return $tax_totals;
			}

		}

		public function ywgc_recalculate_totals( $total, $order ){

			$used_gift_cards = yit_get_prop( $order, '_ywgc_applied_gift_cards', true);

			if ( ! $used_gift_cards )
				return $total;

			$order_total = get_post_meta( $order->get_id(), '_order_total', true);

			$applied_gift_card_amount = yit_get_prop( $order,'_ywgc_applied_gift_cards_totals' );

			$updated_total = (float)$order_total - (float)$applied_gift_card_amount;

			$total = $updated_total;

			return $total;

		}



	}
}
