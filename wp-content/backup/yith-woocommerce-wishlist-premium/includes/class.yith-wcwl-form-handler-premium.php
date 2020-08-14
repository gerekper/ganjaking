<?php
/**
 * Static class that will handle all form submission from customer
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Form_Handler_Premium' ) ) {
	/**
	 * WooCommerce Wishlist Form Handler
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Form_Handler_Premium {
		/**
		 * Performs all required add_actions to handle forms
		 *
		 * @return void
		 */
		public static function init() {
			/**
			 * This check was added to prevent bots from accidentaly executing wishlist code
			 *
			 * @since 3.0.10
			 */
			if ( ! YITH_WCWL_Form_Handler::process_form_handling() ) {
				return;
			}

			add_action( 'init', array( 'YITH_WCWL_Form_Handler_Premium', 'create_wishlist' ) );
			add_action( 'init', array( 'YITH_WCWL_Form_Handler_Premium', 'manage_wishlists' ) );
			add_action( 'init', array( 'YITH_WCWL_Form_Handler_Premium', 'delete_wishlists' ) );
			add_action( 'init', array( 'YITH_WCWL_Form_Handler_Premium', 'update_wishlist' ) );
			add_action( 'init', array( 'YITH_WCWL_Form_Handler_Premium', 'move_to_another_wishlist' ) );
			add_action( 'init', array( 'YITH_WCWL_Form_Handler_Premium', 'ask_an_estimate' ) );
			add_action( 'init', array( 'YITH_WCWL_Form_Handler_Premium', 'download_pdf' ) );
			add_action( 'init', array( 'YITH_WCWL_Form_Handler_Premium', 'unsubscribe' ) );

			// these actions manage cart, and needs to hooked to wp_loaded
			add_action( 'wp_loaded', array( 'YITH_WCWL_Form_Handler_Premium', 'apply_bulk_actions' ), 15 );
			add_action( 'wp_loaded', array( 'YITH_WCWL_Form_Handler_Premium', 'add_all_to_cart' ), 15 );

			if( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ){
				add_action( 'wp_loaded', array( 'YITH_WCWL_Form_Handler_Premium', 'bulk_add_to_cart' ), 30 );
			}
		}

		/**
		 * Apply bulk actions to wishlist items
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function apply_bulk_actions() {
			if( ! isset( $_POST['yith_wcwl_edit_wishlist'] ) || ! wp_verify_nonce( $_POST['yith_wcwl_edit_wishlist'], 'yith_wcwl_edit_wishlist_action' ) || ! isset( $_POST['apply_bulk_actions'] ) || empty( $_POST['items'] ) ){
				return;
			}

			$wishlist_id = isset( $_POST['wishlist_id'] ) ? sanitize_text_field( $_POST['wishlist_id'] ) : false;
			$action = isset( $_POST['bulk_actions'] ) ? sanitize_text_field( $_POST['bulk_actions'] ) : false;
			$items = isset( $_POST['items'] ) ? array_filter( $_POST['items'] ) : false;

			if( ! $wishlist_id || ! $action ){
				return;
			}

			if( empty( $items ) ){
				wc_add_notice( __( 'You have to select at least one product', 'yith-woocommerce-wishlist' ), 'error' );
			}

			$wishlist = yith_wcwl_get_wishlist( $wishlist_id );

			if( ! $wishlist ){
				return;
			}

			$remove_after_add_to_cart = 'yes' == get_option( 'yith_wcwl_remove_after_add_to_cart' );
			$redirect_to_cart = 'yes' == get_option( 'yith_wcwl_redirect_cart' );
			$processed = array();
			$result = false;

			foreach( $items as $item_id => $prop ){
				if( empty( $prop['cb'] ) ){
					continue;
				}

				$item = $wishlist->get_product( $item_id );

				if( ! $item ){
					continue;
				}

				switch( $action ){
					case 'add_to_cart':
						try {
							$result = (bool) WC()->cart->add_to_cart( $item->get_product_id(), $item->get_quantity() );

							if( ! $remove_after_add_to_cart ){
								break;
							}
						}
						catch( Exception $e ){
							continue 2;
						}
					case 'delete':
						$result = $item->delete();
						break;
					default:
						// maybe customer wants to move items to another list
						$destination_wishlist = yith_wcwl_get_wishlist( $action );

						if( ! $destination_wishlist ) {
							continue 2;
						}

						$item->set_wishlist_id( $destination_wishlist->get_id() );
						$item->set_date_added( current_time( 'mysql' ) );
						$result = $item->save();
				}

				if( $result ) {
					$processed[] = $item;
				}
			}

			if( ! empty( $processed ) ){
				switch( $action ){
					case 'add_to_cart':
						$message = __( 'The items have been correctly added to the cart', 'yith-woocommerce-wishlist' );
						break;
					case 'delete':
						$message = __( 'The items have been correctly removed', 'yith-woocommerce-wishlist' );
						break;
					default:
						$message = sprintf( __( 'The items have been correctly moved to %s', 'yith-woocommerce-wishlist' ), $destination_wishlist->get_formatted_name() );
				}

				wc_add_notice( $message, 'success' );
			}
			else{
				wc_add_notice( __( 'An error occurred while processing this action', 'yith-woocommerce-wishlist' ), 'error' );
			}

			$cart_url = wc_get_cart_url();
			$redirect_url = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : $wishlist->get_url();
			$redirect_url = ( 'add_to_cart' == $action && $redirect_to_cart ) ? $cart_url : $redirect_url;

			wp_redirect( $redirect_url );
			die();
		}

		/**
		 * Update wishlist items (save quantity and position)
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function update_wishlist() {
			if( ! isset( $_POST['yith_wcwl_edit_wishlist'] ) || ! wp_verify_nonce( $_POST['yith_wcwl_edit_wishlist'], 'yith_wcwl_edit_wishlist_action' ) || ! isset( $_POST['update_wishlist'] ) || empty( $_POST['items'] ) ){
				return;
			}

			$wishlist_id = isset( $_POST['wishlist_id'] ) ? sanitize_text_field( $_POST['wishlist_id'] ) : false;
			$items = isset( $_POST['items'] ) ? array_filter( $_POST['items'] ) : false;

			if( ! $wishlist_id || ! $items ){
				return;
			}

			$wishlist = yith_wcwl_get_wishlist( $wishlist_id );

			if( ! $wishlist ){
				return;
			}

			foreach( $items as $product_id => $values ){
				$item = $wishlist->get_product( $product_id );

				if( ! $item ){
					continue;
				}

				if( isset( $values['quantity'] ) ) {
					$item->set_quantity( $values['quantity'] );
				}

				if( isset( $values['position'] ) ) {
					$item->set_position( $values['position'] );
				}

				$item->save();
			}

			wc_add_notice( __( 'Changes applied correctly', 'yith-woocommerce-wishlist' ), 'success' );

			$redirect_url = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : $wishlist->get_url();

			wp_redirect( $redirect_url );
			die;
		}

		/**
		 * Add all items of a wishlist to cart
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function add_all_to_cart() {
			if( ! isset( $_REQUEST['yith_wcwl_edit_wishlist'] ) || ! wp_verify_nonce( $_REQUEST['yith_wcwl_edit_wishlist'], 'yith_wcwl_edit_wishlist_action' ) || ! isset( $_REQUEST['add_all_to_cart'] ) ){
				return;
			}

			$wishlist_id = isset( $_REQUEST['wishlist_id'] ) ? sanitize_text_field( $_REQUEST['wishlist_id'] ) : false;
			$wishlists = array();

			if( $wishlist_id ) {
				$wishlist = yith_wcwl_get_wishlist( $wishlist_id );

				if ( ! $wishlist ) {
					return;
				}

				$wishlists[] = $wishlist;
			}
			else{
				$wishlists = YITH_WCWL()->get_current_user_wishlists();
			}

			$remove_after_add_to_cart = 'yes' == get_option( 'yith_wcwl_remove_after_add_to_cart' );
			$redirect_to_cart = 'yes' == get_option( 'yith_wcwl_redirect_cart' );
			$processed = array();

			remove_action( 'woocommerce_add_to_cart', array( 'YITH_WCWL_Form_Handler', 'remove_from_wishlist_after_add_to_cart' ) );

			if( ! empty( $wishlists ) ) {
				foreach( $wishlists as $wishlist ) {
					if ( $wishlist->has_items() ) {
						foreach ( $wishlist->get_items() as $item ) {
							try {
								$result = (bool) WC()->cart->add_to_cart( $item->get_product_id(), $item->get_quantity() );

								if ( $result ) {
									$processed[] = $item;

									if ( $remove_after_add_to_cart ) {
										$item->delete();
									}
								}
							} catch ( Exception $e ) {
								continue;
							}
						}
					}
				}
			}

			if ( ! empty( $processed ) ) {
				wc_add_notice( __( 'Items correctly added to the cart', 'yith-woocommerce-wishlist' ), 'success' );
			} else {
				wc_add_notice( __( 'An error occurred while adding the items to the cart; please, try again later.', 'yith-woocommerce-wishlist' ), 'error' );
			}

			$cart_url = wc_get_cart_url();
			$redirect_url = $wishlist_id ? $wishlist->get_url() : remove_query_arg( array( 'yith_wcwl_edit_wishlist', 'add_all_to_cart' ) );
			$redirect_url = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : $redirect_url;
			$redirect_url = $redirect_to_cart ? $cart_url : $redirect_url;

			wp_redirect( $redirect_url );
			die;
		}

		/**
		 * Move an item from a wishlist to another
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function move_to_another_wishlist() {
			if( ! isset( $_GET['move_to_another_wishlist'] ) || ! isset( $_GET['move_to_another_wishlist_nonce'] ) || ! wp_verify_nonce( $_GET['move_to_another_wishlist_nonce'], 'move_to_another_wishlist' ) ) {
				return;
			}

			$row_id = isset( $_POST['row_id'] ) ? intval( $_POST['row_id'] ) : false;
			$wishlist_id = sanitize_text_field( $_GET['move_to_another_wishlist'] );
			$wishlist_id = ( $wishlist_id == 'false' ) ? false : $wishlist_id;
			$wishlist    = yith_wcwl_get_wishlist( $wishlist_id );
			$destination_wishlist_id = isset( $_POST['new_wishlist_id'] ) ? intval( $_POST['new_wishlist_id'] ) : false;
			$destination_wishlist = yith_wcwl_get_wishlist( $destination_wishlist_id );

			if( $wishlist && $destination_wishlist && $item = $wishlist->get_product( $row_id ) ){
				if( $destination_item = $destination_wishlist->get_product( $row_id ) ){
					$destination_item->set_date_added( current_time( 'mysql' ) );

					$destination_item->save();
					$item->delete();
				}
				else {
					$item->set_wishlist_id( $destination_wishlist_id );
					$item->set_date_added( current_time( 'mysql' ) );
					$item->save();
				}

				wc_add_notice( sprintf( __( 'Element correctly moved to %s', 'yith-woocommerce-wishlist' ), $destination_wishlist->get_formatted_name() ), 'success' );
			}
			else{
				wc_add_notice( __( 'An error occurred while moving item to destination wishlist; please, try again', 'yith-woocommerce-wishlist' ), 'error' );
			}

			$redirect_url = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : $wishlist->get_url();
			$redirect_url = $wishlist ? $redirect_url : YITH_WCWL()->get_wishlist_url();

			wp_redirect( $redirect_url );
			die;
		}

		/**
		 * Create a new wishlist from request
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public static function create_wishlist() {
			if( ! isset( $_POST['yith_wcwl_create'] ) || ! wp_verify_nonce( $_POST['yith_wcwl_create'], 'yith_wcwl_create_action' ) || ! isset( $_POST['wishlist_name'] ) ){
				return;
			}

			try{
				$wishlist = YITH_WCWL_Premium()->add_wishlist();
			}
			catch( YITH_WCWL_Exception $e ){
				wc_add_notice( $e->getMessage(), 'error' );
				return;
			}

			$message = apply_filters( 'yith_wcwl_wishlist_correctly_created', __( 'Wishlist created successfully', 'yith-woocommerce-wishlist' ) );
			wc_add_notice( $message, 'success' );

			$redirect_url = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : $wishlist->get_url();
			$redirect_url = apply_filters( 'yith_wcwl_redirect_after_create_wishlist', $redirect_url );

			wp_redirect( $redirect_url );
			die;
		}

		/**
		 * Update or delete wishlist basing on request data
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function manage_wishlists(){
			if( ! isset( $_POST['yith_wcwl_manage'] ) || ! wp_verify_nonce( $_POST['yith_wcwl_manage'], 'yith_wcwl_manage_action' ) || empty( $_POST['wishlist_options'] ) ){
				return;
			}

			foreach( $_POST['wishlist_options'] as $wishlist_id => $wishlist ){
				$wishlist_id = intval( $wishlist_id );

				try {
					if ( isset( $wishlist['delete'] ) ) {
						YITH_WCWL_Premium()->remove_wishlist( $wishlist_id );
					} else {
						YITH_WCWL_Premium()->update_wishlist( $wishlist_id, $wishlist );
					}
				}
				catch( YITH_WCWL_Exception $e ){
					wc_add_notice( $e->getMessage(), 'error' );
					continue;
				}
			}

			if( ! wc_notice_count( 'error' ) ) {
				$message = apply_filters( 'yith_wcwl_wishlist_correctly_managed', __( 'Changes saved', 'yith-woocommerce-wishlist' ) );
				wc_add_notice( $message, 'success' );
			}

			$redirect_url = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : YITH_WCWL()->get_wishlist_url( 'manage' );

			wp_redirect( $redirect_url );
			die;
		}

		/**
		 * Delete wishlist when "Delete" button is selected on manage view
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function delete_wishlists() {
			if( ! isset( $_GET['yith_wcwl_delete'] ) || ! wp_verify_nonce( $_GET['yith_wcwl_delete'], 'yith_wcwl_delete_action' ) || empty( $_GET['wishlist_id'] ) ) {
				return;
			}

			$wishlist_id = intval( $_GET['wishlist_id'] );

			try {
				YITH_WCWL_Premium()->remove_wishlist( $wishlist_id );

				$message = apply_filters( 'yith_wcwl_wishlist_successfully_deleted', __( 'Wishlist deleted successfully','yith-woocommerce-wishlist' ) );
				wc_add_notice( $message, 'success' );
			}
			catch( YITH_WCWL_Exception $e ){
				wc_add_notice( $e->getMessage(), 'error' );
			}

			// redirect to manage page after removing wishlist
			$redirect_url = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : YITH_WCWL()->get_wishlist_url( 'manage' );
			$redirect_url = apply_filters( 'yith_wcwl_wishlist_correctly_deleted', $redirect_url );

			wp_redirect( $redirect_url );
			die();
		}

		/**
		 * Triggers action that sends an email when users ask an estimate
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function ask_an_estimate() {
			if( ! isset( $_GET['ask_an_estimate'] ) || ! isset( $_GET['estimate_nonce'] ) || ! wp_verify_nonce( $_GET['estimate_nonce'], 'ask_an_estimate' ) ) {
				return;
			}

			$wishlist_id = sanitize_text_field( $_GET['ask_an_estimate'] );
			$wishlist_id = ( $wishlist_id == 'false' ) ? false : $wishlist_id;
			$wishlist    = yith_wcwl_get_wishlist( $wishlist_id );
			$valid_data  = array();

			$additional_notes = ! empty( $_POST['additional_notes'] ) ? sanitize_text_field( $_POST['additional_notes'] ) : false;
			$reply_email = ! empty( $_POST['reply_email'] ) ? sanitize_email( $_POST['reply_email'] ) : false;

			$redirect_url = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : $wishlist->get_url();
			$redirect_url = apply_filters( 'yith_wcwl_after_ask_an_estimate_redirect', $redirect_url, $wishlist_id, $additional_notes, $reply_email, $_POST );

			if( 'yes' == get_option( 'yith_wcwl_show_additional_info_textarea' ) ) {
				try {
					$ask_an_estimate_fields = yith_wcwl_maybe_format_field_array( get_option( 'yith_wcwl_ask_an_estimate_fields', array() ) );
					$valid_data = self::get_valid_additional_data( $_POST, $ask_an_estimate_fields );
				} catch ( Exception $e ) {
					wc_add_notice( $e->getMessage(), 'error' );
					wp_redirect( $redirect_url );
					exit();
				}
			}

			$wishlist = yith_wcwl_get_wishlist( $wishlist_id );

			if( ! $wishlist || ! $wishlist->current_user_can( 'ask_an_estimate' ) ){
				wp_send_json( array(
					'result' => false,
					'message' => __( 'There was an error while processing your request; please, try later', 'yith-woocommerce-wishlist' ) // @since 3.0.7
				) );
			}

			if( is_user_logged_in() || $reply_email ){
				do_action( 'send_estimate_mail', $wishlist_id, $additional_notes, $reply_email, $valid_data );
				wc_add_notice( apply_filters( 'yith_wcwl_estimate_sent', __( 'Price estimate request sent', 'yith-woocommerce-wishlist' ) ), 'success' );
			}
			else{
				wc_add_notice( apply_filters( 'yith_wcwl_estimate_missing_email', __( 'You should provide a valid email address, that we can use to get back to you', 'yith-woocommerce-wishlist' ) ), 'error' );
			}

			wp_redirect( $redirect_url );
			exit();
		}

		/**
		 * Adds multiple items to the cart from wishlist page
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function bulk_add_to_cart() {
			if( ! isset( $_REQUEST['wishlist_products_to_add_to_cart'] ) ) {
				return;
			}

			$ids = array_filter( explode( ',', $_REQUEST['wishlist_products_to_add_to_cart'] ) );
			$remove_after_add_to_cart = 'yes' == get_option( 'yith_wcwl_remove_after_add_to_cart' );
			$redirect_to_cart = 'yes' == get_option( 'yith_wcwl_redirect_cart' );
			$wishlist_token = isset( $_REQUEST['wishlist_token'] ) ? $_REQUEST['wishlist_token'] : false;
			$added = array();

			if( ! empty( $ids ) ){
				$wishlist = YITH_WCWL_Wishlist_Factory::get_wishlist( $wishlist_token );

				foreach( $ids as $id ){
					try {
						$result = (bool) WC()->cart->add_to_cart( $id );

						if( $wishlist && $remove_after_add_to_cart ){
							$wishlist->remove_product( $id );
						}
					}
					catch( Exception $e ){
						continue;
					}

					if( $result ){
						$added[ $id ] = 1;
					}
				}

				if( ! empty( $added ) ){
					wc_add_to_cart_message( $added );
				}
			}
			else{
				wc_add_notice( __( 'Please, select one product at least', 'yith-woocommerce-wishlist' ), 'error' );
			}

			$cart_url = wc_get_cart_url();
			$redirect_url = $redirect_to_cart ? $cart_url : YITH_WCWL()->get_wishlist_url( 'view/' . $wishlist_token );
			$redirect_url = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : $redirect_url;
			$redirect_url = count( $added ) ? $redirect_url : YITH_WCWL()->get_wishlist_url( 'view/' . $wishlist_token );

			wp_redirect( $redirect_url );
			die();
		}

		/**
		 * Download wishlist in pdf form
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function download_pdf() {
			if( ! isset( $_GET['download_wishlist'] ) || ! wp_verify_nonce( $_GET['download_nonce'], 'download_wishlist' ) ){
				return;
			}

			$wishlist_id = intval( $_GET['download_wishlist'] );
			$wishlist = yith_wcwl_get_wishlist( $wishlist_id );

			if( ! $wishlist || ! $wishlist->current_user_can( 'download_pdf' ) ){
				return;
			}

			if( ! class_exists( 'Dompdf\Dompdf' ) ){
				include( YITH_WCWL_DIR . 'vendor/autoload.php' );
			}

			// retrieve style for the PDF
			$located = locate_template( WC()->template_path() . 'wishlist-pdf.css' );

			if( ! $located ){
				$css_url = YITH_WCWL_URL . 'assets/css/pdf.css';
			}
			else{
				$stylesheet_directory = get_stylesheet_directory();
				$stylesheet_directory_uri = get_stylesheet_directory_uri();
				$template_directory = get_template_directory();
				$template_directory_uri = get_template_directory_uri();

				$css_url = ( strpos( $located, $stylesheet_directory ) ) ? str_replace( $stylesheet_directory, $stylesheet_directory_uri, $located ) : str_replace( $template_directory, $template_directory_uri, $located );
			}

			$template = yith_wcwl_get_template( 'wishlist-pdf.php', apply_filters( 'yith_wcwl_pdf_parameters', array(
				'wishlist' => $wishlist,
				'wishlist_items' => $wishlist->get_items(),
				'page_title' => $wishlist->get_formatted_name(),
				'show_price' => 'yes' == get_option( 'yith_wcwl_price_show' ),
				'show_dateadded' => 'yes' == get_option( 'yith_wcwl_show_dateadded' ),
				'show_stock_status' => 'yes' == get_option( 'yith_wcwl_stock_show' ),
				'show_price_variations' => 'yes' == get_option( 'yith_wcwl_price_changes_show' ),
				'show_variation' => 'yes' == get_option( 'yith_wcwl_variation_show' ),
				'show_quantity' => 'yes' == get_option( 'yith_wcwl_quantity_show' ),
				'css_url' => $css_url
			) ), true );

			// generate pdf
			$dompdf = new Dompdf\Dompdf();
			$dompdf->loadHtml( $template );
			$dompdf->setPaper('A4', 'landscape');
			$dompdf->render();
			$dompdf->stream( $wishlist->get_formatted_name() . '.pdf' );

			// no redirect required; browser will process this request as download
			die();
		}

		/**
		 * Unsubscribe from mailing lists for wishlist plugin
		 *
		 * @return void
		 */
		public static function unsubscribe() {
			if( ! isset( $_GET['yith_wcwl_unsubscribe'] ) ){
				return;
			}

			// retrieve unsubscription_process
			$unsubscribe_token = isset( $_GET['yith_wcwl_unsubscribe'] ) ? sanitize_text_field( $_GET['yith_wcwl_unsubscribe'] ) : false;

			if( ! $unsubscribe_token ){
				return;
			}

			// if user is not logged in, send to login page
			if( ! is_user_logged_in() ){
				wc_add_notice( __( 'Please, log in to continue with the unsubscribe process', 'yith-woocommerce-wishlist' ), 'notice' );
				wp_redirect( add_query_arg( 'redirect', esc_url( add_query_arg( $_GET, get_home_url() ) ), wc_get_page_permalink( 'myaccount' ) ) );
				die;
			}

			// redirect uri
			$redirect = apply_filters( 'yith_wcwl_after_unsubscribe_redirect_uri', get_home_url() );

			// get current user token
			$user_id = get_current_user_id();
			$user = wp_get_current_user();
			$user_unsubscribe_token = get_user_meta( $user_id, 'yith_wcwl_unsubscribe_token', true );
			$unsubscribe_token_expiration = get_user_meta( $user_id, 'yith_wcwl_unsubscribe_token_expiration', true );

			// check for match with provided token
			if( $unsubscribe_token != $user_unsubscribe_token ){
				wc_add_notice( __( 'The token provided does not match the current user; make sure to log in with the right account', 'yith-woocommerce-wishlist' ), 'notice' );
				wp_redirect( $redirect );
				die;
			}

			if( $unsubscribe_token_expiration < time() ){
				wc_add_notice( __( 'The token provided is expired; contact us to so we can manually unsubscribe your from the list', 'yith-woocommerce-wishlist' ), 'notice' );
				wp_redirect( $redirect );
				die;
			}

			$unsubscribed_users = get_option( 'yith_wcwl_unsubscribed_users', array() );

			if( ! in_array( $user->user_email, $unsubscribed_users ) ){
				$unsubscribed_users[] = $user->user_email;

				update_option( 'yith_wcwl_unsubscribed_users', $unsubscribed_users );

				delete_user_meta( $user_id, 'yith_wcwl_unsubscribe_token' );
				delete_user_meta( $user_id, 'yith_wcwl_unsubscribe_token_expiration' );
			}

			wc_add_notice( __( 'You have unsubscribed from our wishlist-related mailing lists', 'yith-woocommerce-wishlist' ), 'success' );
			wp_redirect( $redirect );
			die;
		}

		/**
		 * Format data submitted by customer, basing on a specific field structure
		 *
		 * @param $data array Posted data
		 * @param $field_structure array Array of field structure, as required by WC to print field
		 *
		 * @return array Array of valid sanitized data
		 * @throws Exception When system find a malformed data or a required field is empty
		 */
		public static function get_valid_additional_data( $data, $field_structure ){
			$valid_data = array();

			if( empty( $field_structure ) ){
				return array();
			}

			foreach( $field_structure as $field_id => $field ){
				if( isset( $data[ $field_id ] ) ) {
					switch ( $field['type'] ) {
						case 'radio':
						case 'select':
							$options = ! empty( $field['options'] ) ? $field['options'] : array();

							if ( array_key_exists( $data[ $field_id ], $options ) ) {
								$valid_data[ $field['label'] ] = $options[ $data[ $field_id ] ];
							}

							break;
						case 'checkbox':
							$valid_data[ $field['label'] ] = __( 'Yes', 'yith-woocommerce-wishlist' );
							break;
						case 'number':
							if ( $number = preg_replace( '/[^\d+]/', '', $data[ $field_id ] ) ) {
								$valid_data[ $field['label'] ] = $number;
							}
							break;
						case 'date':
							if ( preg_match( '/[\d]{4}-[\d]{2}-[\d]{2}/', $data[ $field_id ] ) ) {
								$valid_data[ $field['label'] ] = date( wc_date_format(), strtotime( $data[ $field_id ] ) );
							}
							break;
						case 'tel':
							if ( $phone = wc_sanitize_phone_number( $data[ $field_id ] ) ) {
								$valid_data[ $field['label'] ] = $phone;
							}
							break;
						case 'email':
							if ( $email = sanitize_email( $data[ $field_id ] ) ) {
								$valid_data[ $field['label'] ] = $email;
							}
							break;
						case 'url':
							if ( $url = esc_url_raw( $data[ $field_id ] ) ) {
								$valid_data[ $field['label'] ] = $url;
							}
							break;
						case 'textarea':
							if ( $escaped_value = sanitize_textarea_field( $data[ $field_id ] ) ) {
								$valid_data[ $field['label'] ] = $escaped_value;
							}
							break;
						default:
							if ( $escaped_value = sanitize_text_field( $data[ $field_id ] ) ) {
								$valid_data[ $field['label'] ] = $escaped_value;
							}
							break;
					}
				}

				// double check for required field
				if( 'yes' == $field['required'] && ! isset( $valid_data[ $field['label'] ] ) ){
					throw new Exception( sprintf( _x( 'Missing required argument: %s', 'Ask for an estimate submit error', 'yith-woocommerce-wishlist' ), $field['label'] ) );
				}
			}

			return $valid_data;
		}
	}
}
YITH_WCWL_Form_Handler_Premium::init();
