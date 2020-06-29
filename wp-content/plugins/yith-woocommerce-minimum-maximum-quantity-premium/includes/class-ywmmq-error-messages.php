<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWMMQ_Error_Messages' ) ) {

	/**
	 * Implements Error Messages for YWMMQ plugin
	 *
	 * @class   YWMMQ_Error_Messages
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWMMQ_Error_Messages {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var \YWMMQ_Error_Messages
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWMMQ_Error_Messages
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self( $_REQUEST );

			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @return  mixed
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_filter( 'ywmmq_cart_qty_error', array( $this, 'ywmmq_cart_error' ), 10, 6 );

		}

		/**
		 * Sets error message for wrong cart quantity
		 *
		 * @param   $value
		 * @param   $limit
		 * @param   $cart_limit
		 * @param   $total_cart
		 * @param   $current_page
		 * @param   $limit_type
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_cart_error( $value, $limit, $cart_limit, $total_cart, $current_page, $limit_type ) {

			$find = array(
				'{limit}',
				'{cart_quantity}',
				'{cart_quote}'
			);

			$replace = array(
				( $limit_type == 'value' ? wc_price( $cart_limit ) : $cart_limit ),
				( $limit_type == 'value' ? wc_price( $total_cart ) : $total_cart ),
				( YITH_WMMQ()->contents_type == 'cart' ? __( 'cart', 'yith-woocommerce-minimum-maximum-quantity' ) : __( 'quote', 'yith-woocommerce-minimum-maximum-quantity' ) )
			);

			$message = get_option( 'ywmmq_message_' . $limit . '_cart_' . $limit_type . '_' . $current_page );

			if ( get_option( 'ywmmq_cart_value_shipping' ) == 'no' && $limit_type == 'value' && YITH_WMMQ()->contents_type == 'cart' ) {
				$message .= ' (' . __( 'Shipping fees and related taxes excluded.', 'yith-woocommerce-minimum-maximum-quantity' ) . ')';
			}

			return str_replace( $find, $replace, $message );

		}

		/**
		 * Sets error message for wrong product quantity
		 *
		 * @param   $limit_type
		 * @param   $product_limit_qty
		 * @param   $item
		 * @param   $current_page
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_product_quantity_error( $limit_type, $product_limit_qty, $item, $current_page ) {

			$product_id = ( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];

			/*global $sitepress;
			$has_wpml = ! empty( $sitepress ) ? true : false;

			if ( $has_wpml && apply_filters( 'ywmmq_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
			}*/

			$product      = wc_get_product( $product_id );
			$product_name = '';

			switch ( $current_page ) {
				case 'cart':
					if ( YITH_WMMQ()->contents_type === 'cart' ) {
						$product_title = $item['data']->get_name();
					} else {
						$product_title = $product->get_title();

						if ( $item['variation_id'] ) {

							$variation_data = '';
							$item_data      = array();
							foreach ( $item['variations'] as $name => $value ) {
								$label = '';

								if ( '' === $value ) {
									continue;
								}

								$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

								// If this is a term slug, get the term's nice name
								if ( taxonomy_exists( $taxonomy ) ) {
									$term = get_term_by( 'slug', $value, $taxonomy );
									if ( ! is_wp_error( $term ) && $term && $term->name ) {
										$value = $term->name;
									}
									$label = wc_attribute_label( $taxonomy );

								} else {

									if ( strpos( $name, 'attribute_' ) !== false ) {
										$custom_att = str_replace( 'attribute_', '', $name );

										if ( '' !== $custom_att ) {
											$label = wc_attribute_label( $custom_att );
										} else {
											$label = $name;
										}
									}
								}

								$item_data[] = array(
									'key'   => $label,
									'value' => $value,
								);
							}

							if ( sizeof( $item_data ) > 0 ) {
								foreach ( $item_data as $data ) {
									$variation_data .= esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . '<br/>';
								}
							}

							$variation_data = rtrim( $variation_data, '<br/>' );

							$product_title .= ' (' . $variation_data . ')';

						}
					}

					$product_name = sprintf( '<a href="%1$s">%2$s</a>', get_permalink( $item['product_id'] ), $product_title );

					break;

				case 'atc':
					$product_name = $product->get_title();
					break;

			}

			$find = array(
				'{limit}',
				'{product_name}',
				'{cart_quote}',
			);

			$replace = array(
				$product_limit_qty,
				$product_name,
				( YITH_WMMQ()->contents_type === 'cart' ? __( 'cart', 'yith-woocommerce-minimum-maximum-quantity' ) : __( 'quote', 'yith-woocommerce-minimum-maximum-quantity' ) ),
			);

			$message = get_option( 'ywmmq_message_' . $limit_type . '_product_quantity_' . $current_page );

			return str_replace( $find, $replace, $message );

		}

		/**
		 * Sets error message for wrong category quantity
		 *
		 * @param   $limit
		 * @param   $category_limit
		 * @param   $category_id
		 * @param   $current_page
		 * @param   $limit_type
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_category_error( $limit, $category_limit, $category_id, $current_page, $limit_type ) {

			$category = get_term( $category_id, 'product_cat' );

			$category_name = '';

			switch ( $current_page ) {
				case 'cart':

					$category_name = '<a href="' . get_term_link( $category ) . '">' . $category->name . '</a>';
					break;

				case 'atc':

					$category_name = $category->name;
					break;

			}

			$find = array(
				'{limit}',
				'{category_name}',
				'{cart_quote}'
			);

			$replace = array(
				( $limit_type == 'value' ? wc_price( $category_limit ) : $category_limit ),
				$category_name,
				( YITH_WMMQ()->contents_type == 'cart' ? __( 'cart', 'yith-woocommerce-minimum-maximum-quantity' ) : __( 'quote', 'yith-woocommerce-minimum-maximum-quantity' ) )
			);

			$message = get_option( 'ywmmq_message_' . $limit . '_category_' . $limit_type . '_' . $current_page );

			return str_replace( $find, $replace, $message );

		}

		/**
		 * Sets error message for wrong tag quantity
		 *
		 * @param   $limit
		 * @param   $tag_limit
		 * @param   $tag_id
		 * @param   $current_page
		 * @param   $limit_type
		 *
		 * @return  string
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function ywmmq_tag_error( $limit, $tag_limit, $tag_id, $current_page, $limit_type ) {

			$tag = get_term( $tag_id, 'product_tag' );

			$tag_name = '';

			switch ( $current_page ) {
				case 'cart':

					$tag_name = '<a href="' . get_term_link( $tag ) . '">' . $tag->name . '</a>';
					break;

				case 'atc':

					$tag_name = $tag->name;
					break;

			}

			$find = array(
				'{limit}',
				'{tag_name}',
				'{cart_quote}'
			);

			$replace = array(
				( $limit_type == 'value' ? wc_price( $tag_limit ) : $tag_limit ),
				$tag_name,
				( YITH_WMMQ()->contents_type == 'cart' ? __( 'cart', 'yith-woocommerce-minimum-maximum-quantity' ) : __( 'quote', 'yith-woocommerce-minimum-maximum-quantity' ) )
			);

			$message = get_option( 'ywmmq_message_' . $limit . '_tag_' . $limit_type . '_' . $current_page );

			return str_replace( $find, $replace, $message );

		}

	}

	/**
	 * Unique access to instance of YWMMQ_Error_Messages class
	 *
	 * @return \YWMMQ_Error_Messages
	 */
	function YWMMQ_Error_Messages() {

		return YWMMQ_Error_Messages::get_instance();

	}

	new YWMMQ_Error_Messages();

}