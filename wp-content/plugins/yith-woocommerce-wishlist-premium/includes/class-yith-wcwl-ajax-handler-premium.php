<?php
/**
 * Static class that will handle all ajax calls for the list
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Ajax_Handler_Premium' ) ) {
	/**
	 * WooCommerce Wishlist Ajax Handler
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Ajax_Handler_Premium {
		/**
		 * Performs all required add_actions to handle forms
		 *
		 * @return void
		 */
		public static function init() {
			// handle ajax requests.
			add_action( 'wp_ajax_save_privacy', array( 'YITH_WCWL_Ajax_Handler_Premium', 'save_privacy' ) );
			add_action( 'wp_ajax_nopriv_save_privacy', array( 'YITH_WCWL_Ajax_Handler_Premium', 'save_privacy' ) );
			add_action( 'wp_ajax_move_to_another_wishlist', array( 'YITH_WCWL_Ajax_Handler_Premium', 'move_to_another_wishlist' ) );
			add_action( 'wp_ajax_nopriv_move_to_another_wishlist', array( 'YITH_WCWL_Ajax_Handler_Premium', 'move_to_another_wishlist' ) );
			add_action( 'wp_ajax_sort_wishlist_items', array( 'YITH_WCWL_Ajax_Handler_Premium', 'sort_items' ) );
			add_action( 'wp_ajax_nopriv_sort_wishlist_items', array( 'YITH_WCWL_Ajax_Handler_Premium', 'sort_items' ) );
			add_action( 'wp_ajax_ask_an_estimate', array( 'YITH_WCWL_Ajax_Handler_Premium', 'ask_an_estimate' ) );
			add_action( 'wp_ajax_nopriv_ask_an_estimate', array( 'YITH_WCWL_Ajax_Handler_Premium', 'ask_an_estimate' ) );
			add_action( 'wp_ajax_remove_from_all_wishlists', array( 'YITH_WCWL_Ajax_Handler_Premium', 'remove_from_all_wishlists' ) );
			add_action( 'wp_ajax_nopriv_remove_from_all_wishlists', array( 'YITH_WCWL_Ajax_Handler_Premium', 'remove_from_all_wishlists' ) );

			// update free responses with premium options.
			add_filter( 'yith_wcwl_ajax_add_return_params', array( 'YITH_WCWL_Ajax_Handler_Premium', 'change_add_return_params' ) );
		}

		/**
		 * Save new wishlist privacy
		 *
		 * @return void
		 * @since 3.0.7
		 */
		public static function save_privacy() {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'save_privacy' ) ) {
				wp_send_json( array( 'result' => false ) );
			}

			$wishlist_id      = isset( $_POST['wishlist_id'] ) ? intval( $_POST['wishlist_id'] ) : false;
			$wishlist_privacy = isset( $_POST['privacy'] ) ? intval( $_POST['privacy'] ) : false;
			$fragments        = isset( $_REQUEST['fragments'] ) ? wc_clean( $_REQUEST['fragments'] ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

			$wishlist = $wishlist_id ? yith_wcwl_get_wishlist( $wishlist_id ) : false;

			if ( ! $wishlist ) {
				wp_send_json( array( 'result' => false ) );
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_wishlist_privacy_types
			 *
			 * Filter the array with the wishlist privacy types.
			 *
			 * @param array $privacy_types Array with wishlist privacy types
			 *
			 * @return array
			 */
			if ( ! in_array( $wishlist_privacy, apply_filters( 'yith_wcwl_wishlist_privacy_types', array( 0, 1, 2 ) ), true ) ) {
				wp_send_json(
					array(
						'result' => false,
					)
				);
			}

			$wishlist->set_privacy( $wishlist_privacy );
			$wishlist->save();

			$return = array(
				'result'    => true,
				'fragments' => YITH_WCWL_Ajax_Handler::refresh_fragments( $fragments ),
			);

			wp_send_json( $return );
		}

		/**
		 * Move an item to another wishlist on an ajax call
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function move_to_another_wishlist() {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'move_to_another_wishlist' ) ) {
				wp_send_json( array( 'result' => false ) );
			}

			$origin_wishlist_token      = isset( $_POST['wishlist_token'] ) ? sanitize_text_field( wp_unslash( $_POST['wishlist_token'] ) ) : false;
			$destination_wishlist_token = isset( $_POST['destination_wishlist_token'] ) ? sanitize_text_field( wp_unslash( $_POST['destination_wishlist_token'] ) ) : false;
			$item_id                    = isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : false;
			$fragments                  = isset( $_REQUEST['fragments'] ) ? wc_Clean( $_REQUEST['fragments'] ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$moved                      = false;
			$message                    = '';

			if ( $destination_wishlist_token && $origin_wishlist_token && $item_id ) {
				if ( 'new' === $destination_wishlist_token ) {
					try {
						$destination_wishlist = YITH_WCWL()->add_wishlist();
					} catch ( Exception $e ) {
						$destination_wishlist = false;
					}
				}

				$origin_wishlist      = YITH_WCWL_Wishlist_Factory::get_wishlist( $origin_wishlist_token );
				$destination_wishlist = isset( $destination_wishlist ) ? $destination_wishlist : YITH_WCWL_Wishlist_Factory::get_wishlist( $destination_wishlist_token );

				if ( $origin_wishlist && $destination_wishlist && $origin_wishlist->current_user_can( 'remove_from_wishlist' ) && $destination_wishlist->current_user_can( 'add_to_wishlist' ) ) {
					$item = $origin_wishlist->get_product( $item_id );

					if ( $item ) {
						$destination_item = $destination_wishlist->get_product( $item_id );

						if ( $destination_item ) {
							$destination_item->set_date_added( current_time( 'mysql' ) );

							$destination_item->save();
							$item->delete();
						} else {
							$item->set_wishlist_id( $destination_wishlist->get_id() );
							$item->set_date_added( current_time( 'mysql' ) );

							$item->save();
						}

						$moved = true;
						wp_cache_delete( 'wishlist-items-' . $origin_wishlist->get_id(), 'wishlists' );
						wp_cache_delete( 'wishlist-items-' . $destination_wishlist->get_id(), 'wishlists' );

					}
				}
			}

			$wishlists           = YITH_WCWL_Wishlist_Factory::get_wishlists();
			$wishlists_to_prompt = array();

			foreach ( $wishlists as $wishlist ) {
				$wishlists_to_prompt[] = array(
					'id'                       => $wishlist->get_id(),
					'wishlist_name'            => $wishlist->get_formatted_name(),
					'default'                  => $wishlist->is_default(),
					'add_to_this_wishlist_url' => isset( $item ) ? wp_nonce_url(
						add_query_arg(
							array(
								'add_to_wishlist' => $item->get_product_id(),
								'wishlist_id'     => $wishlist->get_id(),
							),
							$origin_wishlist->get_url()
						),
						'add_to_wishlist'
					) : '',
				);
			}

			if ( $moved ) {
				/**
				 * APPLY_FILTERS: yith_wcwl_moved_element_message
				 *
				 * Filter the message when an item has been moved successfully to another wishlist.
				 *
				 * @param string $message Message
				 *
				 * @return string
				 */
				// translators: 1. Destination wishlist name.
				$message = apply_filters( 'yith_wcwl_moved_element_message', sprintf( __( 'Element correctly moved to %s', 'yith-woocommerce-wishlist' ), $destination_wishlist->get_name() ) );
			}

			$return = array(
				'result'         => $moved,
				'fragments'      => YITH_WCWL_Ajax_Handler::refresh_fragments( $fragments ),
				'user_wishlists' => $wishlists_to_prompt,
				'message'        => $message,
			);

			wp_send_json( $return );
		}

		/**
		 * Triggers action that sends an email when users ask an estimate
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function ask_an_estimate() {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ask_an_estimate' ) ) {
				wp_send_json( array( 'result' => false ) );
			}

			$wishlist_id      = isset( $_POST['ask_an_estimate'] ) ? sanitize_text_field( wp_unslash( $_POST['ask_an_estimate'] ) ) : false;
			$wishlist_id      = 'false' === $wishlist_id ? false : $wishlist_id;
			$additional_notes = ! empty( $_POST['additional_notes'] ) ? sanitize_text_field( wp_unslash( $_POST['additional_notes'] ) ) : false;
			$reply_email      = ! empty( $_POST['reply_email'] ) ? sanitize_email( wp_unslash( $_POST['reply_email'] ) ) : false;

			$template = '';

			$wishlist = yith_wcwl_get_wishlist( $wishlist_id );

			if ( ! $wishlist || ! $wishlist->current_user_can( 'ask_an_estimate' ) ) {
				wp_send_json(
					array(
						'result'  => false,
						'message' => __( 'There was an error while processing your request; please, try later', 'yith-woocommerce-wishlist' ), // @since 3.0.7
					)
				);
			}

			try {
				$ask_an_estimate_fields = yith_wcwl_maybe_format_field_array( get_option( 'yith_wcwl_ask_an_estimate_fields', array() ) );
				$valid_data             = YITH_WCWL_Form_Handler_Premium::get_valid_additional_data( $_POST, $ask_an_estimate_fields );
			} catch ( Exception $e ) {
				$error = $e->getMessage();

				wp_send_json(
					array(
						'result'  => false,
						'message' => $error,
					)
				);
			}

			if ( is_user_logged_in() || $reply_email ) {
				/**
				 * DO_ACTION: send_estimate_mail
				 *
				 * Allows to fire some action when the 'Ask for an estimate' email is sent.
				 *
				 * @param int    $wishlist_id      Wishlist ID
				 * @param string $additional_notes Additional notes
				 * @param string $reply_email      Email address to reply
				 * @param array  $valid_data       Array of valid data sent in the estimate request
				 */
				do_action( 'send_estimate_mail', $wishlist_id, $additional_notes, $reply_email, $valid_data );
				$status = true;

				/**
				 * APPLY_FILTERS: yith_wcwl_estimate_sent
				 *
				 * Filter the message when the estimate request has been sent.
				 *
				 * @param string $message Message
				 *
				 * @return string
				 */
				$message = apply_filters( 'yith_wcwl_estimate_sent', __( 'Estimate request sent', 'yith-woocommerce-wishlist' ) );

				/**
				 * APPLY_FILTERS: yith_wcwl_estimate_sent_contact_email
				 *
				 * Filter the sent from email address for the 'Ask for an estimate' email.
				 *
				 * @param string $email_address Email address
				 *
				 * @return string
				 */
				$template_contact_email = apply_filters( 'yith_wcwl_estimate_sent_contact_email', get_option( 'woocommerce_email_from_address' ) );

				/**
				 * APPLY_FILTERS: yith_wcwl_estimate_sent_popup_heading_icon_class
				 *
				 * Filter the heading icon in the 'Estimate sent' popup.
				 *
				 * @param string $heading_icon Heading icon
				 *
				 * @return string
				 */
				$template_icon = apply_filters( 'yith_wcwl_estimate_sent_popup_heading_icon_class', 'fa-envelope-o' );

				/**
				 * APPLY_FILTERS: yith_wcwl_show_popup_heading_icon_instead_of_title
				 *
				 * Filter whether to show the icon in the 'Estimate sent' popup.
				 *
				 * @param bool   $show_icon    Whether to show icon or not
				 * @param string $heading_icon Heading icon
				 *
				 * @return bool
				 */
				$template_heading = apply_filters( 'yith_wcwl_show_popup_heading_icon_instead_of_title', ! empty( $template_icon ), $template_icon ) ? "<i class='fa {$template_icon} heading-icon'></i>" : '';

				/**
				 * APPLY_FILTERS: yith_wcwl_estimate_sent_title
				 *
				 * Filter the title shown in the popup when the estimate request has been sent.
				 *
				 * @param string $message Message
				 *
				 * @return string
				 */
				$template_title = apply_filters( 'yith_wcwl_estimate_sent_title', __( 'Your request has been sent.<br/>Thanks!', 'yith-woocommerce-wishlist' ) );

				/**
				 * APPLY_FILTERS: yith_wcwl_estimate_sent_body
				 *
				 * Filter the body shown in the popup when the estimate request has been sent.
				 *
				 * @param string $message Message
				 *
				 * @return string
				 */
				$template_body = apply_filters( 'yith_wcwl_estimate_sent_body', sprintf( '<p class="ask-an-estimate-confirmation">%1$s <a href="mailto:%2$s">%2$s</a></p>', __( 'We will reply to you as soon as possible. For any questions, feel free to contact our customer service at', 'yith-woocommerce-wishlist' ), $template_contact_email ) );

				/**
				 * APPLY_FILTERS: yith_wcwl_estimate_sent_template
				 *
				 * Filter the template used to send the estimate request.
				 *
				 * @param string $template               HTML template
				 * @param string $template_contact_email Sent from email address
				 * @param string $template_icon          Template icon
				 * @param string $template_heading       Template hading
				 * @param string $template_title         Template title
				 * @param string $template_body          Template body
				 *
				 * @return string
				 */
				$template = apply_filters(
					'yith_wcwl_estimate_sent_template',
					"<div class='yith-wcwl-popup-content'>
					{$template_heading}
					<h3>{$template_title}</h3>
					<p>{$template_body}</p>
					</div>",
					$template_contact_email,
					$template_icon,
					$template_heading,
					$template_title,
					$template_body
				);
			} else {
				$status = false;

				/**
				 * APPLY_FILTERS: yith_wcwl_estimate_missing_email
				 *
				 * Filter the message shown in the popup when an email address has not been set for the estimate request.
				 *
				 * @param string $message Message
				 *
				 * @return string
				 */
				$message = apply_filters( 'yith_wcwl_estimate_missing_email', __( 'You should provide a valid email address that we can use to get back to you', 'yith-woocommerce-wishlist' ) );
			}

			wp_send_json(
				array(
					'result'   => $status,
					'message'  => $message,
					'template' => $template,
				)
			);
		}

		/**
		 * Sort items basing on order submitted via Ajax request
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function sort_items() {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'sort_wishlist_items' ) ) {
				die();
			}

			$wishlist_token = isset( $_POST['wishlist_token'] ) ? sanitize_text_field( wp_unslash( $_POST['wishlist_token'] ) ) : false;
			$positions      = isset( $_POST['positions'] ) ? array_map( 'intval', $_POST['positions'] ) : array();
			$page           = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
			$per_page       = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 0;

			if ( empty( $wishlist_token ) || empty( $positions ) ) {
				die();
			}

			$wishlist = yith_wcwl_get_wishlist( $wishlist_token );

			if ( ! $wishlist || ! $wishlist->current_user_can( 'sort_items' ) ) {
				die();
			}

			$items = $wishlist->get_items();

			if ( empty( $items ) ) {
				die();
			}

			// set missing positions.
			$counter = 0;
			$offset  = ( $page - 1 ) * $per_page;

			foreach ( $items as $item ) {
				$item->set_position( $counter );
				++$counter;
			}

			// set configured positions.
			foreach ( $items as $item ) {
				$index = array_search( $item->get_product_id(), $positions, true );

				if ( false !== $index ) {
					$item->set_position( $index + $offset );
				}

				$item->save();
			}

			// stops ajax call from further execution (no return value expected on answer body).
			die();
		}

		/**
		 * Remove item from wishlists
		 * Differs from remove_from_wishlist, since this removes all occurrences of a product across all wishlists
		 * If a wishlist id is passed, removes just from that list, but doesn't return the template of the list as remove_from_wishlist
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function remove_from_all_wishlists() {
			if ( ! isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'remove_from_all_wishlists' ) ) {
				wp_send_json( array( 'result' => false ) );
			}

			$prod_id     = isset( $_POST['prod_id'] ) ? intval( $_POST['prod_id'] ) : false;
			$wishlist_id = isset( $_POST['wishlist_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wishlist_id'] ) ) : false;
			$fragments   = isset( $_POST['fragments'] ) ? wc_clean( $_POST['fragments'] ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

			if ( ! $prod_id ) {
				wp_send_json(
					array(
						'result' => false,
					)
				);
			}

			$matching_items = YITH_WCWL_Wishlist_Factory::get_wishlist_items(
				array(
					'product_id'  => $prod_id,
					'wishlist_id' => $wishlist_id ? $wishlist_id : 'all',
				)
			);

			if ( ! empty( $matching_items ) ) {
				foreach ( $matching_items as $item ) {
					$item->delete();
				}
			}

			wp_send_json(
				array(
					'result'    => true,
					'fragments' => YITH_WCWL_Ajax_Handler::refresh_fragments( $fragments ),
				)
			);
		}

		/**
		 * Add premium parameters to response for Add to Wishlist ajax request
		 *
		 * @param array $params Array of parameters to output as json.
		 * @return array Filtered array of parameters to output as json
		 */
		public static function change_add_return_params( $params ) {
			$show_count = 'yes' === get_option( 'yith_wcwl_show_counter' );

			if ( $show_count && isset( $params['prod_id'] ) ) {
				$params['count'] = yith_wcwl_get_count_text( $params['prod_id'] );
			}
			return $params;
		}
	}
}
YITH_WCWL_Ajax_Handler_Premium::init();
