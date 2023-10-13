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

if ( ! class_exists( 'YITH_WCWL_Ajax_Handler' ) ) {
	/**
	 * WooCommerce Wishlist Ajax Handler
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Ajax_Handler {
		/**
		 * Performs all required add_actions to handle forms
		 *
		 * @return void
		 */
		public static function init() {
			// add to wishlist.
			add_action( 'wp_ajax_add_to_wishlist', array( 'YITH_WCWL_Ajax_Handler', 'add_to_wishlist' ) );
			add_action( 'wp_ajax_nopriv_add_to_wishlist', array( 'YITH_WCWL_Ajax_Handler', 'add_to_wishlist' ) );

			// remove from wishlist.
			add_action( 'wp_ajax_remove_from_wishlist', array( 'YITH_WCWL_Ajax_Handler', 'remove_from_wishlist' ) );
			add_action( 'wp_ajax_nopriv_remove_from_wishlist', array( 'YITH_WCWL_Ajax_Handler', 'remove_from_wishlist' ) );

			// remove from wishlist (button).
			add_action( 'wp_ajax_delete_item', array( 'YITH_WCWL_Ajax_Handler', 'delete_item' ) );
			add_action( 'wp_ajax_nopriv_delete_item', array( 'YITH_WCWL_Ajax_Handler', 'delete_item' ) );

			// save title.
			add_action( 'wp_ajax_save_title', array( 'YITH_WCWL_Ajax_Handler', 'save_title' ) );
			add_action( 'wp_ajax_nopriv_save_title', array( 'YITH_WCWL_Ajax_Handler', 'save_title' ) );

			// load mobile templates.
			add_action( 'wp_ajax_load_mobile', array( 'YITH_WCWL_Ajax_Handler', 'load_mobile' ) );
			add_action( 'wp_ajax_nopriv_load_mobile', array( 'YITH_WCWL_Ajax_Handler', 'load_mobile' ) );

			// add to wishlist and reload.
			add_action( 'wp_ajax_reload_wishlist_and_adding_elem', array( 'YITH_WCWL_Ajax_Handler', 'reload_wishlist_and_adding_elem' ) );
			add_action( 'wp_ajax_nopriv_reload_wishlist_and_adding_elem', array( 'YITH_WCWL_Ajax_Handler', 'reload_wishlist_and_adding_elem' ) );

			// load fragments.
			add_action( 'wp_ajax_load_fragments', array( 'YITH_WCWL_Ajax_Handler', 'load_fragments' ) );
			add_action( 'wp_ajax_nopriv_load_fragments', array( 'YITH_WCWL_Ajax_Handler', 'load_fragments' ) );
		}

		/**
		 * Add to wishlist from ajax call
		 *
		 * @return void
		 */
		public static function add_to_wishlist() {
			if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'add_to_wishlist' ) ) {
				wp_send_json( array( 'result' => false ) );
			}

			try {
				YITH_WCWL()->add();

				$return = 'true';

				/**
				 * APPLY_FILTERS: yith_wcwl_product_added_to_wishlist_message
				 *
				 * Filter the message shown when an item has been added to the wishlist.
				 *
				 * @param string $message Message
				 *
				 * @return string
				 */
				$message = apply_filters( 'yith_wcwl_product_added_to_wishlist_message', get_option( 'yith_wcwl_product_added_text' ) );

				// append view and close links.
				/**
				 * APPLY_FILTERS: yith_wcwl_show_popup_links
				 *
				 * Filter whether to show the links in the popup after an item has been added to the wishlist.
				 *
				 * @param bool $show_links Whether to show links or not in the popup
				 *
				 * @return bool
				 */
				if ( apply_filters( 'yith_wcwl_show_popup_links', YITH_WCWL()->is_multi_wishlist_enabled() ) ) {
					$message .= '<p class="after-links">
					<a href="' . YITH_WCWL()->get_last_operation_url() . '">' . __( 'View &rsaquo;', 'yith-woocommerce-wishlist' ) . '</a>
					<span class="separator">' . __( 'or', 'yith-woocommerce-wishlist' ) . '</span>
					<a href="#" class="close-popup">' . __( 'Close', 'yith-woocommerce-wishlist' ) . '</a>
					</p>';
				}
			} catch ( YITH_WCWL_Exception $e ) {
				$return = $e->getTextualCode();

				/**
				 * APPLY_FILTERS: yith_wcwl_error_adding_to_wishlist_message
				 *
				 * Filter the error message shown when adding an item to the wishlist.
				 *
				 * @param string $message Message
				 *
				 * @return string
				 */
				$message = apply_filters( 'yith_wcwl_error_adding_to_wishlist_message', $e->getMessage() );
			} catch ( Exception $e ) {
				$return  = 'error';
				$message = apply_filters( 'yith_wcwl_error_adding_to_wishlist_message', $e->getMessage() );
			}

			$product_id   = isset( $_REQUEST['add_to_wishlist'] ) ? intval( $_REQUEST['add_to_wishlist'] ) : false;
			$fragments    = isset( $_REQUEST['fragments'] ) ? wc_clean( $_REQUEST['fragments'] ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$wishlist_url = YITH_WCWL()->get_last_operation_url();

			$wishlists = YITH_WCWL_Wishlist_Factory::get_wishlists();

			$wishlists_to_prompt = array();

			foreach ( $wishlists as $wishlist ) {
				$wishlists_to_prompt[] = array(
					'id'                       => $wishlist->get_id(),
					'wishlist_name'            => $wishlist->get_formatted_name(),
					'default'                  => $wishlist->is_default(),
					'add_to_this_wishlist_url' => $product_id ? wp_nonce_url(
						add_query_arg(
							array(
								'add_to_wishlist' => $product_id,
								'wishlist_id'     => $wishlist->get_id(),
							),
							$wishlist->get_url()
						),
						'add_to_wishlist'
					) : '',
				);
			}

			if ( in_array( $return, array( 'exists', 'true' ), true ) ) {
				// search for related fragments.
				if ( ! empty( $fragments ) && ! empty( $product_id ) ) {
					foreach ( $fragments as $id => $options ) {
						if ( strpos( $id, 'add-to-wishlist-' . $product_id ) ) {
							$fragments[ $id ]['wishlist_url']      = $wishlist_url;
							$fragments[ $id ]['added_to_wishlist'] = 'true' === $return;
						}
					}
				}
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_ajax_add_return_params
			 *
			 * Filter the parameters sent in the Ajax response.
			 *
			 * @param array $params Array of parameters sent in the response
			 *
			 * @return array
			 */
			wp_send_json(
				apply_filters(
					'yith_wcwl_ajax_add_return_params',
					array(
						'prod_id'        => $product_id,
						'result'         => $return,
						'message'        => $message,
						'fragments'      => self::refresh_fragments( $fragments ),
						'user_wishlists' => $wishlists_to_prompt,
						'wishlist_url'   => $wishlist_url,
					)
				)
			);
		}

		/**
		 * Remove from wishlist from ajax call
		 *
		 * @return void
		 */
		public static function remove_from_wishlist() {
			if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'remove_from_wishlist' ) ) {
				wp_send_json( array( 'fragments' => array() ) );
			}

			$fragments = isset( $_REQUEST['fragments'] ) ? wc_clean( $_REQUEST['fragments'] ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

			try {
				YITH_WCWL()->remove();

				/**
				 * APPLY_FILTERS: yith_wcwl_product_removed_text
				 *
				 * Filter the message when an item has been removed from the wishlist.
				 *
				 * @param string $message Message
				 *
				 * @return string
				 */
				$message = apply_filters( 'yith_wcwl_product_removed_text', __( 'Product successfully removed.', 'yith-woocommerce-wishlist' ) );
			} catch ( Exception $e ) {
				$message = $e->getMessage();
			}

			yith_wcwl_add_notice( $message );

			wp_send_json(
				array(
					'fragments' => self::refresh_fragments( $fragments ),
				)
			);
		}

		/**
		 * Remove item from a wishlist
		 * Differs from remove from wishlist, since this accepts item id instead of product id
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function delete_item() {
			if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'delete_item' ) ) {
				wp_send_json( array( 'result' => false ) );
			}

			$item_id   = isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : false;
			$fragments = isset( $_REQUEST['fragments'] ) ? wc_clean( $_REQUEST['fragments'] ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$return    = array(
				'result' => false,
			);

			if ( $item_id ) {
				$item = YITH_WCWL_Wishlist_Factory::get_wishlist_item( $item_id );

				if ( $item ) {
					$item->delete();

					/**
					 * APPLY_FILTERS: yith_wcwl_product_removed_text
					 *
					 * Filter the message when an item has been removed from the wishlist.
					 *
					 * @param string $message Message
					 *
					 * @return string
					 */
					$return = array(
						'result'    => true,
						'message'   => apply_filters( 'yith_wcwl_product_removed_text', __( 'Product successfully removed.', 'yith-woocommerce-wishlist' ) ),
						'fragments' => self::refresh_fragments( $fragments ),
					);
				}
			}

			wp_send_json( $return );
		}

		/**
		 * Save new wishlist privacy
		 *
		 * @return void
		 * @since 3.0.7
		 */
		public static function save_title() {
			if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'save_title' ) ) {
				wp_send_json( array( 'result' => false ) );
			}

			$wishlist_id   = isset( $_POST['wishlist_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wishlist_id'] ) ) : false;
			$wishlist_name = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : false;
			$fragments     = isset( $_REQUEST['fragments'] ) ? wc_clean( $_REQUEST['fragments'] ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$wishlist      = $wishlist_id ? yith_wcwl_get_wishlist( $wishlist_id ) : false;

			if ( ! $wishlist_id || ! $wishlist ) {
				wp_send_json(
					array(
						'result' => false,
					)
				);
			}

			if ( ! $wishlist_name || strlen( $wishlist_name ) >= 65535 ) {
				wp_send_json(
					array(
						'result' => false,
					)
				);
			}

			$wishlist->set_name( $wishlist_name );
			$wishlist->save();

			/**
			 * DO_ACTION: yith_wcwl_after_rename_wishlist
			 *
			 * Allows to fire some action when the wishlist has been renamed.
			 *
			 * @param YITH_WCWL_Wishlist $wishlist Wishlist object
			 */
			do_action( 'yith_wcwl_after_rename_wishlist', $wishlist );

			$return = array(
				'result'    => true,
				'fragments' => self::refresh_fragments( $fragments ),
			);

			wp_send_json( $return );
		}

		/**
		 * Generated fragments to replace in the the page
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function load_fragments() {
			if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'load_fragments' ) ) {
				wp_send_json( array( 'result' => false ) );
			}

			$fragments = isset( $_POST['fragments'] ) ? wc_clean( $_POST['fragments'] ) : false; // phpcs:ignore WordPress.Security

			wp_send_json(
				array(
					'fragments' => self::refresh_fragments( $fragments ),
				)
			);
		}

		/**
		 * Reload wishlist and adding elem action
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public static function reload_wishlist_and_adding_elem() {
			if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'reload_wishlist_and_adding_elem' ) ) {
				wp_send_json( array( 'result' => false ) );
			}

			$type_msg = 'success';

			try {
				YITH_WCWL()->add();

				/**
				 * APPLY_FILTERS: yith_wcwl_product_added_to_wishlist_message
				 *
				 * Filter the message shown when an item has been added to the wishlist.
				 *
				 * @param string $message Message
				 *
				 * @return string
				 */
				$message = apply_filters( 'yith_wcwl_product_added_to_wishlist_message', get_option( 'yith_wcwl_product_added_text' ) );
			} catch ( YITH_WCWL_Exception $e ) {
				$message  = $e->getMessage();
				$type_msg = $e->getTextualCode();
			} catch ( Exception $e ) {
				$message  = $e->getMessage();
				$type_msg = 'error';
			}

			$wishlist_token = isset( $_REQUEST['wishlist_token'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wishlist_token'] ) ) : false;
			$atts           = array( 'wishlist_id' => $wishlist_token );

			if ( isset( $_REQUEST['pagination'] ) ) {
				$atts['pagination'] = sanitize_text_field( wp_unslash( $_REQUEST['pagination'] ) );
			}

			if ( isset( $_REQUEST['per_page'] ) ) {
				$atts['per_page'] = intval( $_REQUEST['per_page'] );
			}

			yith_wcwl_add_notice( $message, $type_msg );

			?>
			<div>
				<?php echo YITH_WCWL_Shortcode::wishlist( $atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<?php

			die();
		}

		/**
		 * Reloads fragments, returning mobile version when available
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function load_mobile() {
			global $yith_wcwl_is_mobile;

			if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'load_mobile' ) ) {
				wp_send_json( array( 'fragments' => array() ) );
			}

			$fragments = isset( $_POST['fragments'] ) ? wc_clean( $_POST['fragments'] ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$result    = array();

			if ( ! empty( $fragments ) ) {
				foreach ( $fragments as $id => $options ) {
					$yith_wcwl_is_mobile = isset( $options['is_mobile'] ) ? 'yes' === $options['is_mobile'] : false;

					$result = array_merge( $result, self::refresh_fragments( array( $id => $options ) ) );
				}
			}

			wp_send_json(
				array(
					'fragments' => $result,
				)
			);
		}

		/**
		 * Generate fragments for the templates that needs to be refreshed after ajax
		 *
		 * @param array $fragments Array of fragments to refresh.
		 * @return array Array of templates to be replaced on the page
		 */
		public static function refresh_fragments( $fragments ) {
			$result = array();

			if ( ! empty( $fragments ) ) {
				foreach ( $fragments as $id => $options ) {
					$id      = sanitize_text_field( $id );
					$options = YITH_WCWL_Frontend()->decode_fragment_options( $options );
					$item    = isset( $options['item'] ) ? $options['item'] : false;

					if ( ! $item ) {
						continue;
					}

					switch ( $item ) {
						case 'add_to_wishlist':
						case 'wishlist':
							$result[ $id ] = YITH_WCWL_Shortcode::$item( $options );
							break;
						case 'YITH_WCWL_Widget':
						case 'YITH_WCWL_Items_Widget':
							ob_start();
							the_widget( $item, $options );
							$result[ $id ] = ob_get_clean();
							break;
						default:
							/**
							 * APPLY_FILTERS: yith_wcwl_fragment_output
							 *
							 * Filter the output when reloading fragments.
							 *
							 * @param string $output  Fragment output
							 * @param int    $id      Fragment ID
							 * @param array  $options Fragment options
							 *
							 * @return string
							 */
							$result[ $id ] = apply_filters( 'yith_wcwl_fragment_output', '', $id, $options );
							break;
					}
				}
			}

			return $result;
		}
	}
}
YITH_WCWL_Ajax_Handler::init();
