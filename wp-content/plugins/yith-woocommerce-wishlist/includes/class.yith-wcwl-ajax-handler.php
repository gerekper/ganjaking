<?php
/**
 * Static class that will handle all ajax calls for the list
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
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
			// add to wishlist
			add_action( 'wp_ajax_add_to_wishlist', array( 'YITH_WCWL_Ajax_Handler', 'add_to_wishlist' ) );
			add_action( 'wp_ajax_nopriv_add_to_wishlist', array( 'YITH_WCWL_Ajax_Handler', 'add_to_wishlist' ) );

			// remove from wishlist
			add_action( 'wp_ajax_remove_from_wishlist', array( 'YITH_WCWL_Ajax_Handler', 'remove_from_wishlist' ) );
			add_action( 'wp_ajax_nopriv_remove_from_wishlist', array( 'YITH_WCWL_Ajax_Handler', 'remove_from_wishlist' ) );

			// remove from wishlist (button)
			add_action( 'wp_ajax_delete_item', array( 'YITH_WCWL_Ajax_Handler', 'delete_item' ) );
			add_action( 'wp_ajax_nopriv_delete_item', array( 'YITH_WCWL_Ajax_Handler', 'delete_item' ) );

			// load mobile templates
			add_action( 'wp_ajax_load_mobile', array( 'YITH_WCWL_Ajax_Handler', 'load_mobile' ) );
			add_action( 'wp_ajax_nopriv_load_mobile', array( 'YITH_WCWL_Ajax_Handler', 'load_mobile' ) );

			// add to wishlist and reload
			add_action( 'wp_ajax_reload_wishlist_and_adding_elem', array( 'YITH_WCWL_Ajax_Handler', 'reload_wishlist_and_adding_elem' ) );
			add_action( 'wp_ajax_nopriv_reload_wishlist_and_adding_elem', array( 'YITH_WCWL_Ajax_Handler', 'reload_wishlist_and_adding_elem' ) );

			// load fragments
			add_action( 'wp_ajax_load_fragments', array( 'YITH_WCWL_Ajax_Handler', 'load_fragments' ) );
			add_action( 'wp_ajax_nopriv_load_fragments', array( 'YITH_WCWL_Ajax_Handler', 'load_fragments' ) );
		}

		/**
		 * Add to wishlist from ajax call
		 *
		 * @return void
		 */
		public static function add_to_wishlist() {
			try {
				YITH_WCWL()->add();

				$return  = 'true';
				$message = apply_filters( 'yith_wcwl_product_added_to_wishlist_message', get_option( 'yith_wcwl_product_added_text' ) );

				// append view and close links.
				if ( apply_filters( 'yith_wcwl_show_popup_links', YITH_WCWL()->is_multi_wishlist_enabled() ) ) {
					$message .= '<p class="after-links">
					<a href="' . YITH_WCWL()->get_last_operation_url() . '">' . __( 'View &rsaquo;', 'yith-woocommerce-wishlist' ) . '</a>
					<span class="separator">' . __( 'or', 'yith-woocommerce-wishlist' ) . '</span>
					<a href="#" class="close-popup">' . __( 'Close', 'yith-woocommerce-wishlist' ) . '</a>
					</p>';
				}
			} catch ( YITH_WCWL_Exception $e ) {
				$return  = $e->getTextualCode();
				$message = apply_filters( 'yith_wcwl_error_adding_to_wishlist_message', $e->getMessage() );
			} catch ( Exception $e ) {
				$return  = 'error';
				$message = apply_filters( 'yith_wcwl_error_adding_to_wishlist_message', $e->getMessage() );
			}

			$product_id   = isset( $_REQUEST['add_to_wishlist'] ) ? intval( $_REQUEST['add_to_wishlist'] ) : false;
			$fragments    = isset( $_REQUEST['fragments'] ) ? $_REQUEST['fragments'] : false;
			$wishlist_url = YITH_WCWL()->get_last_operation_url();

			$wishlists = YITH_WCWL_Wishlist_Factory::get_wishlists();

			$wishlists_to_prompt = array();

			foreach ( $wishlists as $wishlist ) {
				$wishlists_to_prompt[] = array(
					'id'                       => $wishlist->get_id(),
					'wishlist_name'            => $wishlist->get_formatted_name(),
					'default'                  => $wishlist->is_default(),
					'add_to_this_wishlist_url' => $product_id ? add_query_arg(
						array(
							'add_to_wishlist' => $product_id,
							'wishlist_id'     => $wishlist->get_id(),
						)
					) : '',
				);
			}

			if ( in_array( $return, array( 'exists', 'true' ) ) ) {
				// search for related fragments
				if ( ! empty( $fragments ) && ! empty( $product_id ) ) {
					foreach ( $fragments as $id => $options ) {
						if ( strpos( $id, 'add-to-wishlist-' . $product_id ) ) {
							$fragments[ $id ]['wishlist_url']      = $wishlist_url;
							$fragments[ $id ]['added_to_wishlist'] = 'true' == $return;
						}
					}
				}
			}

			wp_send_json(
				apply_filters( 'yith_wcwl_ajax_add_return_params', array(
					'prod_id'        => $product_id,
					'result'         => $return,
					'message'        => $message,
					'fragments'      => self::refresh_fragments( $fragments ),
					'user_wishlists' => $wishlists_to_prompt,
					'wishlist_url'   => $wishlist_url,
				) )
			);
		}

		/**
		 * Remove from wishlist from ajax call
		 *
		 * @return void
		 */
		public static function remove_from_wishlist() {
			try {
				YITH_WCWL()->remove();
				$message = apply_filters( 'yith_wcwl_product_removed_text', __( 'Product successfully removed.', 'yith-woocommerce-wishlist' ) );
			} catch ( Exception $e ) {
				$message = $e->getMessage();
			}

			yith_wcwl_add_notice( $message );

			wp_send_json( array(
				'fragments' => self::refresh_fragments( isset( $_REQUEST['fragments'] ) ? $_REQUEST['fragments'] : false ),
			) );
		}

		/**
		 * Remove item from a wishlist
		 * Differs from remove from wishlist, since this accepts item id instead of product id
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function delete_item() {
			$item_id = isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : false;
			$return  = array(
				'result' => false,
			);

			if ( $item_id ) {
				$item = YITH_WCWL_Wishlist_Factory::get_wishlist_item( $item_id );

				if ( $item ) {
					$item->delete();

					$return = array(
						'result'    => true,
						'message'   => apply_filters( 'yith_wcwl_product_removed_text', __( 'Product successfully removed.', 'yith-woocommerce-wishlist' ) ),
						'fragments' => YITH_WCWL_Ajax_Handler::refresh_fragments( isset( $_REQUEST['fragments'] ) ? $_REQUEST['fragments'] : false ),
					);
				}
			}

			wp_send_json( $return );
		}

		/**
		 * Generated fragments to replace in the the page
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function load_fragments() {
			wp_send_json( array(
				'fragments' => self::refresh_fragments( isset( $_POST['fragments'] ) ? $_POST['fragments'] : false ),
			) );
		}

		/**
		 * Reload wishlist and adding elem action
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public static function reload_wishlist_and_adding_elem() {
			$type_msg = 'success';

			try {
				YITH_WCWL()->add();
				$message = apply_filters( 'yith_wcwl_product_added_to_wishlist_message', get_option( 'yith_wcwl_product_added_text' ) );
			} catch ( YITH_WCWL_Exception $e ) {
				$message  = $e->getMessage();
				$type_msg = $e->getTextualCode();
			} catch ( Exception $e ) {
				$message  = $e->getMessage();
				$type_msg = 'error';
			}

			$wishlist_token = isset( $_REQUEST['wishlist_token'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wishlist_token'] ) ) : false;

			$atts = array( 'wishlist_id' => $wishlist_token );
			if ( isset( $_REQUEST['pagination'] ) ) {
				$atts['pagination'] = sanitize_text_field( wp_unslash( $_REQUEST['pagination'] ) );
			}

			if ( isset( $_REQUEST['per_page'] ) ) {
				$atts['per_page'] = intval( $_REQUEST['per_page'] );
			}

			ob_start();

			yith_wcwl_add_notice( $message, $type_msg );

			echo '<div>' . YITH_WCWL_Shortcode::wishlist( $atts ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

			$fragments = isset( $_POST['fragments'] ) ? $_POST['fragments'] : false;
			$result    = array();

			if ( ! empty( $fragments ) ) {
				foreach ( $fragments as $id => $options ) {
					$yith_wcwl_is_mobile = isset( $options['is_mobile'] ) ? 'yes' == $options['is_mobile'] : false;

					$result = array_merge( $result, self::refresh_fragments( array( $id => $options ) ) );
				}
			}

			wp_send_json( array(
				'fragments' => $result,
			) );
		}

		/**
		 * Generate fragments for the templates that needs to be refreshed after ajax
		 *
		 * @param $fragments array Array of fragments to refresh
		 * @return array Array of templates to be replaced on the page
		 */
		public static function refresh_fragments( $fragments ) {
			$result = array();

			if ( ! empty( $fragments ) ) {
				foreach ( $fragments as $id => $options ) {
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
