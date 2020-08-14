<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements helper functions for YITH Woocommerce Request A Quote
 *
 * @since   1.0.0
 * @author  YITH
 * @package YITH
 */

/* Admin Functions */
if ( ! function_exists( 'ywraq_get_pages' ) ) {

	/**
	 * Return the list of site's pages
	 *
	 * @return array
	 * @since  2.0
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_get_pages() {

		$args    = array(
			'sort_order'   => 'asc',
			'hierarchical' => 1,
			'exclude'      => '',
			'include'      => '',
			'meta_key'     => '',
			'meta_value'   => '',
			'authors'      => '',
			'child_of'     => 0,
			'parent'       => - 1,
			'exclude_tree' => '',
			'number'       => '',
			'offset'       => 0,
			'post_type'    => 'page',
			'post_status'  => 'publish'
		);
		$pages   = get_pages( $args );
		$options = array();
		if ( $pages ) {
			foreach ( $pages as $page ) {
				$options[ $page->ID ] = $page->post_title;
			}
		}

		return $options;

	}
}

if ( ! function_exists( 'yith_ywraq_render_button' ) ) {
	/**
	 * Render the Request a quote button.
	 *
	 * @param bool $product_id
	 */
	function yith_ywraq_render_button( $product_id = false ) {

		if ( ! $product_id ) {
			global $product;
		} else {
			$product = wc_get_product( $product_id );
		}

		if ( ! $product || ! apply_filters( 'yith_ywraq_before_print_button', true, $product ) ) {
			return;
		}

		$style_button = ( get_option( 'ywraq_show_btn_link' ) == 'button' ) ? 'button' : 'ywraq-link';
		$product_id   = yit_get_prop( $product, 'id', true );

		$args = array(
			'class'         => 'add-request-quote-button ' . $style_button,
			'wpnonce'       => wp_create_nonce( 'add-request-quote-' . $product_id ),
			'product_id'    => $product_id,
			'label'         => ywraq_get_label( 'btn_link_text' ),
			'label_browse'  => ywraq_get_label( 'browse_list' ),
			'template_part' => 'button',
			'rqa_url'       => YITH_Request_Quote()->get_raq_page_url(),
			'exists'        => ( $product->is_type( 'variable' ) ) ? false : YITH_Request_Quote()->exists( $product_id ),
		);

		if ( $product->is_type( 'variable' ) ) {
			$args['variations'] = implode( ',', YITH_Request_Quote()->raq_variations );
		}

		$args['args'] = $args;

		$template_button = 'add-to-quote.php';

		if ( class_exists( 'YITH_WAPO_Type' ) && ! is_product() ) {

			$has_addons = YITH_WAPO_Type::getAllowedGroupTypes( $product_id );

			if ( ! empty( $has_addons ) ) {
				$template_button = 'add-to-quote-addons.php';
			}
		}

		if ( $product->is_type( 'yith-composite' ) && ! is_product() ) {
			$template_button = 'add-to-quote-addons.php';
		}

		wc_get_template( $template_button, apply_filters( 'ywraq_add_to_quote_args', $args ), '', YITH_YWRAQ_TEMPLATE_PATH . '/' );
	}
}

if ( ! function_exists( 'yith_ywraq_get_roles' ) ) {
	/**
	 * Return the roles of users
	 *
	 * @return array
	 * @since 1.3.0
	 */
	function yith_ywraq_get_roles() {
		global $wp_roles;
		$roles = array();

		foreach ( $wp_roles->get_names() as $key => $role ) {
			$roles[ $key ] = translate_user_role( $role );
		}

		return array_merge( array( 'all' => esc_html__( 'All', 'yith-woocommerce-request-a-quote' ) ), $roles );
	}
}

/* Frontend Functions */
if ( ! function_exists( 'yith_ywraq_show_button_in_other_pages' ) ) {
	/**
	 * Check if the button can be showed on page.
	 *
	 * @return bool
	 */
	function yith_ywraq_show_button_in_other_pages() {

		$general_show_btn = get_option( 'ywraq_show_btn_other_pages' );
		if ( $general_show_btn != 'yes' ) {
			return false;
		}

		global $product, $sitepress;

		if ( ! $product instanceof WC_Product ) {
			global $post;
			$product_id = $post instanceof WP_Post ? $post->ID : '';
		} else {
			$product_id = $product->get_id();
		}

		if ( empty( $product_id ) ) {
			return false;
		}


		//WPML Integration
		$product_id = isset( $sitepress ) ? yit_wpml_object_id( $product_id, 'product', true, $sitepress->get_default_language() ) : $product_id;

		return ! ywraq_is_in_exclusion( $product_id );

	}
}

if ( ! function_exists( 'yith_ywraq_check_notices' ) ) {
	/**
	 * Check notices.
	 */
	function yith_ywraq_check_notices() {
		$all_notices = array();
		$session     = YITH_Request_Quote()->session_class;
		if ( ! is_null( $session ) ) {
			$all_notices = $session->get( 'yith_ywraq_notices', array() );
		}

		return count( $all_notices );
	}
}

if ( ! function_exists( 'ywraq_get_label' ) ) {
	/**
	 * Return or print a label from a specific $key
	 *
	 * @param      $key
	 * @param bool $echo
	 *
	 * @return string|void
	 */
	function ywraq_get_label( $key, $echo = false ) {

		$option_name = 'ywraq_show_' . $key;
		$option      = get_option( $option_name );

		switch ( $key ) {
			case 'product_added';
				$label = $option ? $option : apply_filters( 'yith_ywraq_product_added_to_list_message', esc_html__( 'Product added to the list!', 'yith-woocommerce-request-a-quote' ) );
				break;
			case 'browse_list';
				$label = $option ? $option : apply_filters( 'ywraq_product_added_view_browse_list', esc_html__( 'Browse the list', 'yith-woocommerce-request-a-quote' ) );
				break;
			case 'btn_link_text';
				$label = $option ? $option : apply_filters( 'ywraq_product_add_to_quote', esc_html__( 'Add to Quote', 'yith-woocommerce-request-a-quote' ) );
				break;
			case 'already_in_quote';
				$label = $option ? $option : apply_filters( 'yith_ywraq_product_already_in_list_message', esc_html__( 'Product already in the list.', 'yith-woocommerce-request-a-quote' ) );
				break;
			case 'accept' :
				$label = get_option( 'ywraq_accept_link_label', esc_html__( 'Accept', 'yith-woocommerce-request-a-quote' ) );
				break;
			case 'reject' :
				$label = get_option( 'ywraq_reject_link_label', esc_html__( 'Reject', 'yith-woocommerce-request-a-quote' ) );
				break;
			default:
				$label = '';

		}

		$label = apply_filters( 'ywraq_get_label', $label, $key );

		if ( $echo ) {
			echo esc_html( $label );
		} else {
			return $label;
		}
	}
}

if ( ! function_exists( 'ywraq_get_token' ) ) {
	/**
	 * Add a token to the mask quote number.
	 *
	 * @param $action
	 * @param $order_id
	 * @param $email
	 *
	 * @return string
	 */
	function ywraq_get_token( $action, $order_id, $email ) {
		return wp_hash( $action . '|' . $order_id . '|' . $email, 'yith-woocommerce-request-a-quote' );
	}
}

if ( ! function_exists( 'ywraq_verify_token' ) ) {
	/**
	 * Check the token.
	 *
	 * @param $token
	 * @param $action
	 * @param $order_id
	 * @param $email
	 *
	 * @return int
	 */
	function ywraq_verify_token( $token, $action, $order_id, $email ) {
		$expected = wp_hash( $action . '|' . $order_id . '|' . $email, 'yith-woocommerce-request-a-quote' );
		if ( hash_equals( $expected, $token ) ) {
			return 1;
		}

		return 0;
	}
}

if ( ! function_exists( 'ywraq_is_in_exclusion' ) ) {
	/**
	 * Check if product is in exclusion list
	 *
	 * @param int $product_id
	 *
	 * @return boolean
	 * @since  2.0.0
	 *
	 * @author Francesco Licandro
	 */
	function ywraq_is_in_exclusion( $product_id ) {

		//If the exclusion list is deactivated return false
		$exclusion_list_activated = get_option( 'ywraq_show_btn_exclusion', 'yes' );
		if ( 'yes' != $exclusion_list_activated ) {
			return false;
		}

		$is_excluded    = false;
		$exclusion_prod = array_filter( explode( ',', get_option( 'yith-ywraq-exclusions-prod-list', '' ) ) );
		$exclusion_cat  = array_filter( explode( ',', get_option( 'yith-ywraq-exclusions-cat-list', '' ) ) );
		$exclusion_tag  = array_filter( explode( ',', get_option( 'yith-ywraq-exclusions-tag-list', '' ) ) );

		$product_cat        = array();
		$product_tag        = array();
		$product_categories = get_the_terms( $product_id, 'product_cat' );
		$product_tags       = get_the_terms( $product_id, 'product_tag' );

		if ( ! empty( $product_categories ) ) {
			foreach ( $product_categories as $cat ) {
				$product_cat[] = $cat->term_id;
			}
		}

		$intersect_cat = array_intersect( $product_cat, $exclusion_cat );

		if ( ! empty( $product_tags ) ) {
			foreach ( $product_tags as $tag ) {
				$product_tag[] = $tag->term_id;
			}
		}

		$intersect_tag = array_intersect( $product_tag, $exclusion_tag );

		if ( in_array( $product_id, $exclusion_prod ) || ! empty( $intersect_cat ) || ! empty( $intersect_tag ) ) {
			$is_excluded = true;
		}

		//can be hide or show if it 'show' the list is reversed
		$is_excluded = ( 'hide' == get_option( 'ywraq_exclusion_list_setting', 'hide' ) ) ? $is_excluded : ! $is_excluded;

		return $is_excluded;
	}
}

if ( ! function_exists( 'ywraq_get_order_status_tag' ) ) {

	/**
	 * Print the order status tag relative to a status.
	 *
	 * @param $status
	 *
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_get_order_status_tag( $status ) {
		switch ( $status ) {
			case 'ywraq-new':
				echo '<span class="raq_status new">' . esc_html__( 'new', 'yith-woocommerce-request-a-quote' ) . '</span>';
				break;
			case 'ywraq-pending':
				echo '<span class="raq_status pending">' . esc_html__( 'pending', 'yith-woocommerce-request-a-quote' ) . '</span>';
				break;
			case 'ywraq-expired':
				echo '<span class="raq_status expired">' . esc_html__( 'expired', 'yith-woocommerce-request-a-quote' ) . '</span>';
				break;
			case 'ywraq-rejected':
				echo '<span class="raq_status rejected">' . esc_html__( 'rejected', 'yith-woocommerce-request-a-quote' ) . '</span>';
				break;
			case 'pending':
				echo '<span class="raq_status accepted">' . esc_html__( 'accepted', 'yith-woocommerce-request-a-quote' ) . '</span>';
				break;
			default:
				echo '<span class="raq_status accepted">' . $status . '</span>';
		}
	}
}

if ( ! function_exists( 'yith_ywraq_get_product_meta' ) ) {
	/**
	 * Return the product meta
	 *
	 * @param      $raq
	 * @param bool $echo
	 *
	 * @param bool $show_price
	 *
	 * @return string
	 */
	function yith_ywraq_get_product_meta( $raq, $echo = true, $show_price = true ) {

		$item_data = array();

		// Variation data
		if ( ! empty( $raq['variation_id'] ) && is_array( $raq['variations'] ) ) {

			foreach ( $raq['variations'] as $name => $value ) {

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
						if ( $custom_att != '' ) {
							$label = wc_attribute_label( $custom_att );
						} else {
							$label = apply_filters( 'woocommerce_attribute_label', wc_attribute_label( $name ), $name );
							// $label = $name;
						}
					}
				}

				$item_data[] = array(
					'key'   => $label,
					'value' => $value
				);


			}
		}

		$item_data = apply_filters( 'ywraq_item_data', $item_data, $raq, $show_price );

		$carrets = apply_filters( 'ywraq_meta_data_carret', "\n" );

		$out = $echo ? $carrets : "";

		// Output flat or in list format
		if ( sizeof( $item_data ) > 0 ) {
			foreach ( $item_data as $data ) {
				if ( $echo ) {
					$out .= '<br />' . esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . $carrets;

				} else {
					$out .= ' - ' . strip_tags( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . ' ';
				}
			}
		}

		if ( $echo ) {
			echo $out;
		} else {
			return $out;
		}

		return '';
	}
}

if ( ! function_exists( 'yith_ywraq_get_product_meta_from_order_item' ) ) {
	/**
	 * Get product meta from order item
	 *
	 * @param      $item_meta
	 * @param bool $echo
	 *
	 * @return string
	 */
	function yith_ywraq_get_product_meta_from_order_item( $item_meta, $echo = true ) {
		/**
		 * Return the product meta in a varion product
		 *
		 * @param array $raq
		 * @param bool  $echo
		 *
		 * @return string
		 * @since 1.0.0
		 */
		$item_data = array();

		// Variation data
		if ( ! empty( $item_meta ) ) {

			foreach ( $item_meta as $name => $val ) {

				if ( empty( $val ) ) {
					continue;
				}

				if ( in_array( $name, apply_filters( 'woocommerce_hidden_order_itemmeta', array(
					'_qty',
					'_tax_class',
					'_product_id',
					'_variation_id',
					'_line_subtotal',
					'_line_subtotal_tax',
					'_line_total',
					'_line_tax',
					'_parent_line_item_id',
					'_commission_id',
					'_woocs_order_rate',
					'_woocs_order_base_currency',
					'_woocs_order_currency_changed_mannualy'
				) ) ) ) {
					continue;
				}

				// Skip serialised meta
				if ( is_serialized( $val[0] ) ) {
					continue;
				}


				$taxonomy = $name;

				// If this is a term slug, get the term's nice name
				if ( taxonomy_exists( $taxonomy ) ) {
					$term = get_term_by( 'slug', $val[0], $taxonomy );
					if ( ! is_wp_error( $term ) && $term && $term->name ) {
						$value = $term->name;
					} else {
						$value = $val[0];
					}
					$label = wc_attribute_label( $taxonomy );

				} else {
					$label = apply_filters( 'woocommerce_attribute_label', wc_attribute_label( $name ), $name );
					$value = $val[0];
				}

				if ( $label != '' && $val[0] != '' ) {
					$item_data[] = array(
						'key'   => $label,
						'value' => $value
					);
				}
			}
		}


		$item_data = apply_filters( 'ywraq_item_data', $item_data );
		$out       = "";
		// Output flat or in list format
		if ( sizeof( $item_data ) > 0 ) {
			foreach ( $item_data as $data ) {
				if ( $echo ) {
					echo esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . "\n";
				} else {
					$out .= ' - ' . esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . ' ';
				}
			}
		}

		return $out;

	}
}

if ( ! function_exists( 'yith_ywraq_add_notice' ) ) {
	/**
	 * Add and store a notice
	 *
	 * @param string $message     The text to display in the notice.
	 * @param string $notice_type The singular name of the notice type - either error, success or notice. [optional]
	 *
	 * @since 2.1
	 *
	 */
	function yith_ywraq_add_notice( $message, $notice_type = 'success' ) {

		$session = YITH_Request_Quote()->session_class;
		if ( ! $session ) {
			return;
		}

		$notices = $session->get( 'yith_ywraq_notices', array() );

		// Backward compatibility
		if ( 'success' === $notice_type ) {
			$message = apply_filters( 'yith_ywraq_add_message', $message );
		}

		$notices[ $notice_type ][] = array(
			'notice' => apply_filters( 'yith_ywraq_add_' . $notice_type, $message )
		);
		$session->set( 'yith_ywraq_notices', $notices );

	}
}

if ( ! function_exists( 'yith_ywraq_notice_count' ) ) {
	/**
	 * Get the count of notices added, either for all notices (default) or for one
	 * particular notice type specified by $notice_type.
	 *
	 * @param string $notice_type The name of the notice type - either error, success or notice. [optional]
	 *
	 * @return int
	 */
	function yith_ywraq_notice_count( $notice_type = '', $all_notices = array() ) {
		$notice_count = 0;

		if ( isset( $all_notices[ $notice_type ] ) ) {
			$notice_count = absint( sizeof( $all_notices[ $notice_type ] ) );
		} elseif ( empty( $notice_type ) ) {
			$notice_count += absint( sizeof( $all_notices ) );
		}

		return $notice_count;
	}
}

if ( ! function_exists( 'yith_ywraq_print_notices' ) ) {
	/**
	 * Prints messages and errors which are stored in the session, then clears them.
	 *
	 */
	function yith_ywraq_print_notices() {
		$all_notices = array();
		$session     = YITH_Request_Quote()->session_class;

		if ( ! $session ) {
			if ( isset( $_GET['order'] ) ) {
				$order                    = $_GET['order'];
				$all_notices['success'][] = array( 'notice' => ywraq_get_message_after_request_quote_sending( $order ) );
			} else {
				return;
			}
		} else {
			$all_notices = $session->get( 'yith_ywraq_notices', array() );
		}


		$notice_types = apply_filters( 'yith_ywraq_notice_types', array( 'error', 'success', 'notice' ) );

		foreach ( $notice_types as $notice_type ) {
			if ( yith_ywraq_notice_count( $notice_type, $all_notices ) > 0 ) {
				if ( count( $all_notices ) > 0 && $all_notices[ $notice_type ] ) {
					$messages = array();

					foreach ( $all_notices[ $notice_type ] as $notice ) {
						$messages[] = isset( $notice['notice'] ) ? $notice['notice'] : $notice;
					}


					wc_get_template( "notices/{$notice_type}.php", array(
						'messages' => array_filter( $messages ), // @deprecated 3.9.0
						'notices'  => array_filter( $all_notices[ $notice_type ] ),
					) );

				}

			}
		}

		/*
		foreach ( $notice_types as $notice_type ) {
			if ( yith_ywraq_notice_count( $notice_type, $all_notices ) > 0 ) {
				if ( count( $all_notices ) > 0 && $all_notices[ $notice_type ] ) {
					wc_get_template( "notices/{$notice_type}.php", array(
						'messages' => $all_notices[ $notice_type ],
						'notices'  => array_filter( $all_notices[ $notice_type ] ),
					) );
				}
			}
		}
*/
		yith_ywraq_clear_notices();
	}
}

if ( ! function_exists( 'yith_ywraq_clear_notices' ) ) {
	/**
	 * Unset all notices
	 */
	function yith_ywraq_clear_notices() {
		$session = YITH_Request_Quote()->session_class;
		$session && $session->set( 'yith_ywraq_notices', null );
	}
}

if ( ! function_exists( 'ywraq_get_message_after_request_quote_sending' ) ) {
	/**
	 * Return the message after that the request quote sending
	 *
	 * @param $new_order
	 *
	 * @return string
	 */
	function ywraq_get_message_after_request_quote_sending( $new_order = '' ) {

		if ( get_option( 'ywraq_how_show_after_sent_the_request' ) !== 'simple_message' ) {
			return '';
		}

		$ywraq_message_after_sent_the_request = esc_html( get_option( 'ywraq_message_after_sent_the_request' ) );
		$ywraq_message_to_view_details        = esc_html( get_option( 'ywraq_message_to_view_details' ) );

		$quote_number = apply_filters( 'ywraq_quote_number', $new_order );
		if ( ! empty( $quote_number ) && is_user_logged_in() && ( get_option( 'ywraq_enable_link_details' ) === 'yes' && get_option( 'ywraq_enable_order_creation', 'yes' ) === 'yes' ) ) {
			$message_format = apply_filters( 'ywraq_simple_thank_you_message', '%1$s %2$s <a href="%3$s">#%4$s</a>' );
			$message        = sprintf( $message_format, $ywraq_message_after_sent_the_request, $ywraq_message_to_view_details, YITH_YWRAQ_Order_Request()->get_view_order_url( $new_order ), $quote_number );
		} else {
			$message = $ywraq_message_after_sent_the_request;
		}

		return $message;
	}
}

if ( ! function_exists( 'ywraq_get_list_empty_message' ) ) {
	/**
	 * @return mixed|void
	 */
	function ywraq_get_list_empty_message() {
		$label_return_to_shop    = apply_filters( 'yith_ywraq_return_to_shop_label', get_option( 'ywraq_return_to_shop_label' ) );
		$empty_list_message_text = apply_filters( 'ywraq_get_list_empty_message_text', esc_html__( 'Your list is empty, add products to the list to send a request', 'yith-woocommerce-request-a-quote' ) );
		$empty_list_message      = sprintf( '<p class="ywraq_list_empty_message">%s<p>', $empty_list_message_text );
		$shop_url                = apply_filters( 'yith_ywraq_return_to_shop_url', get_option( 'ywraq_return_to_shop_url' ) );
		$empty_list_message      .= sprintf( '<p class="return-to-shop"><a class="button wc-backward" href="%s">%s</a></p>', $shop_url, $label_return_to_shop );

		return apply_filters( 'ywraq_get_list_empty_message', $empty_list_message );
	}
}

/* Hooks */
if ( ! function_exists( 'yith_ywraq_show_button_in_single_page' ) ) {
	/**
	 * Check if the button can be showed in single product page.
	 *
	 * @return bool
	 */
	function yith_ywraq_show_button_in_single_page() {

		$general_show_btn = get_option( 'ywraq_show_btn_single_page' );
		if ( $general_show_btn != 'yes' ) {
			return false;
		}

		global $product, $sitepress;

		if ( ! $product ) {
			return false;
		}

		$product_id = $product->get_id();

		//WPML Integration
		$product_id = isset( $sitepress ) ? yit_wpml_object_id( $product_id, 'product', true, $sitepress->get_default_language() ) : $product_id;

		return ! ywraq_is_in_exclusion( $product_id );
	}
}

/* Common Functions */
if ( ! function_exists( 'ywraq_get_date_format' ) ) {
	/**
	 * Return the date format based on locale.
	 *
	 * @param $language
	 *
	 * @return string
	 * @author Alberto Ruggiero
	 */
	function ywraq_get_date_format( $language ) {
		$date_format = wc_date_format();
		if ( isset( $language ) ) {
			$args_accept['lang'] = $language;
			$args_reject['lang'] = $language;

			global $sitepress;

			if ( $sitepress ) {

				$lang = $sitepress->get_locale( $language );

				setlocale( LC_ALL, $lang . '.UTF-8' );

				$local_formats = apply_filters( 'ywraq_date_local_formats', array() );

				if ( ! empty( $local_formats ) ) {

					if ( isset( $local_formats[ $lang ] ) ) {

						$date_format = $local_formats[ $lang ];

					}
				}
			}
		}

		return $date_format;
	}
}

if ( ! function_exists( 'ywraq_get_current_language' ) ) {
	/**
	 * Return the current language when a multilingual plugin is installed.
	 *
	 * @return string $current_language
	 * @since  2.0
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_get_current_language() {
		//WPML Compatibility
		global $sitepress, $polylang;
		$current_language = '';
		if ( function_exists( 'icl_get_languages' ) && class_exists( 'YITH_YWRAQ_Multilingual_Email' ) ) {
			$current_language = $sitepress->get_current_language();
		} elseif ( $polylang && isset( $polylang->curlang->slug ) ) {
			$current_language = $polylang->curlang->slug;
		}

		return $current_language;
	}
}

if ( ! function_exists( 'ywraq_check_recaptcha_options' ) ) {

	/**
	 * Check if recaptcha is enabled and it can be added to the form.
	 *
	 * @return mixed|void
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_check_recaptcha_options() {
		$recaptcha = get_option( 'ywraq_reCAPTCHA' );
		$sitekey   = get_option( 'ywraq_reCAPTCHA_sitekey' );
		$secretkey = get_option( 'ywraq_reCAPTCHA_secretkey' );

		$is_captcha = 'yes' == $recaptcha && ! empty( $sitekey ) && $secretkey;

		return apply_filters( 'ywraq_check_recaptcha', $is_captcha );
	}
}

if ( ! function_exists( 'get_array_column' ) ) {
	/**
	 * Get column of last names from a recordset
	 *
	 * @param $array
	 * @param $array_column
	 *
	 * @return array
	 * @since  2.0.0
	 * @author Alessio Torrisi
	 *
	 */
	function get_array_column( $array, $array_column ) {
		if ( function_exists( 'array_column' ) ) {
			return array_column( $array, $array_column );
		}

		$return = array();
		foreach ( $array as $row ) {
			if ( isset( $row[ $array_column ] ) ) {
				$return[] = $row[ $array_column ];
			}
		}

		return $return;
	}
}

if ( ! function_exists( 'wc_get_template_html' ) && function_exists( 'wc_get_template' ) ) {
	/**
	 * add the function wc_get_template_html if woocommerce version is < 2.5
	 *
	 * @param        $template_name
	 * @param array  $args
	 * @param string $template_path
	 * @param string $default_path
	 *
	 * @return string
	 */
	function wc_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		ob_start();
		wc_get_template( $template_name, $args, $template_path, $default_path );

		return ob_get_clean();
	}
}

if ( ! function_exists( 'ywraq_get_attachment_id_by_url' ) ) {
	/**
	 * Return an ID of an attachment by searching the database with the file URL.
	 *
	 * First checks to see if the $url is pointing to a file that exists in
	 * the wp-content directory. If so, then we search the database for a
	 * partial match consisting of the remaining path AFTER the wp-content
	 * directory. Finally, if a match is found the attachment ID will be
	 * returned.
	 *
	 * @param string $url The URL of the image (ex: http://mysite.com/wp-content/uploads/2013/05/test-image.jpg)
	 *
	 * @return mixed $attachment Returns an attachment ID, or null if no attachment is found
	 */
	function ywraq_get_attachment_id_by_url( $url ) {
		// Split the $url into two parts with the wp-content directory as the separator
		$parsed_url = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

		// Get the host of the current site and the host of the $url, ignoring www
		$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
		$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

		// Return nothing if there aren't any $url parts or if the current host and $url host do not match
		if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
			return;
		}

		// Now we're going to quickly search the DB for any attachment GUID with a partial path match
		// Example: /uploads/2013/05/test-image.jpg
		global $wpdb;

		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );

		// Returns null if no attachment is found
		return $attachment[0];
	}
}

if ( ! function_exists( 'yith_ywraq_get_email_template' ) ) {
	/**
	 * Return email template
	 *
	 * @param $html
	 *
	 * @return string
	 */
	function yith_ywraq_get_email_template( $html ) {

		$raq_data['order_id']    = WC()->session->raq_new_order;
		$raq_data['raq_content'] = YITH_Request_Quote()->get_raq_return();

		ob_start();
		if ( $html ) {
			wc_get_template( 'emails/request-quote-table.php', array(
				'raq_data' => $raq_data
			), '', YITH_YWRAQ_TEMPLATE_PATH . '/' );
		} else {
			wc_get_template( 'emails/plain/request-quote-table.php', array(
				'raq_data' => $raq_data
			), '', YITH_YWRAQ_TEMPLATE_PATH . '/' );
		}

		return ob_get_clean();
	}
}

if ( ! function_exists( 'ywraq_formatted_line_total' ) ) {

	/**
	 * Gets line subtotal - formatted for display.
	 *
	 * @param        $order
	 * @param array  $item
	 * @param string $tax_display
	 *
	 * @return string
	 */
	function ywraq_formatted_line_total( $order, $item, $tax_display = '' ) {

		$tax_display = $tax_display ? $tax_display : get_option( 'woocommerce_tax_display_cart' );

		if ( ! isset( $item['line_total'] ) || ! isset( $item['line_total'] ) || ! isset( $item['line_subtotal'] ) || ! isset( $item['line_subtotal'] ) ) {
			return '';
		}

		$show_old_price = get_option( 'ywraq_show_old_price', false );

		$show_discount = apply_filters( 'ywraq_show_discount_in_line_total', $show_old_price == 'yes' && $item['line_subtotal'] > $item['line_total'], $item );

		$currency = method_exists( $order, 'get_currency' ) ? $order->get_currency() : $order->get_order_currency();

		if ( 'excl' == $tax_display ) {
			$ex_tax_label = yit_get_prop( $order, '_prices_include_tax', true ) ? 1 : 0;

			$line_total    = wc_price( $order->get_line_total( $item ), array(
				'ex_tax_label' => $ex_tax_label,
				'currency'     => $currency
			) );
			$line_subtotal = wc_price( $order->get_line_subtotal( $item ), array(
				'ex_tax_label' => $ex_tax_label,
				'currency'     => $currency
			) );

		} else {
			$line_total    = wc_price( $order->get_line_total( $item, true ), array( 'currency' => $currency ) );
			$line_subtotal = wc_price( $order->get_line_subtotal( $item, true ), array( 'currency' => $currency ) );
		}

		if ( $show_discount ) {
			$show_discount = '<small><del>' . $line_subtotal . '</del></small>';
			$show_discount = apply_filters( 'ywraq_formatted_discount_line_total', $show_discount );
			$line_total    = $show_discount . ' ' . $line_total;
		}

		return apply_filters( 'ywraq_formatted_line_total', $line_total, $item, $order );
	}
}

if ( ! function_exists( 'ywraq_allow_raq_out_of_stock' ) ) {
	/**
	 * Check if the request of quote is allowed also to out of stock products.
	 *
	 * @return bool
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_allow_raq_out_of_stock() {
		$option   = get_option( 'ywraq_allow_raq_out_of_stock' );
		$is_valid = ( true === $option || 1 === $option || '1' === $option || 'yes' === $option );

		return $is_valid;
	}
}

if ( ! function_exists( 'ywraq_show_btn_only_out_of_stock' ) ) {
	/**
	 * Check if the request a quote button must be showed only for out of stock products.
	 *
	 * @return bool
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_show_btn_only_out_of_stock() {
		$option   = get_option( 'ywraq_show_btn_only_out_of_stock' );
		$is_valid = ( true === $option || 1 === $option || '1' === $option || 'yes' === $option );

		return $is_valid;
	}
}

if ( ! function_exists( 'ywraq_get_connect_fields' ) ) {
	/**
	 * Get Order connect meta fields.
	 *
	 * A list of
	 *
	 * @return array
	 * @since  2.0.0
	 * @author Emanuela Castorina
	 */
	function ywraq_get_connect_fields() {
		$fields          = array( '' => array(), 'order_comments' => array() );
		$fields_billing  = WC()->countries->get_address_fields( '', 'billing_' );
		$fields_shipping = WC()->countries->get_address_fields( '', 'shipping_' );
		$fields_billing  = is_array( $fields_billing ) ? $fields_billing : array();
		$fields_shipping = is_array( $fields_shipping ) ? $fields_shipping : array();
		$fields          = array_merge( $fields, $fields_billing, $fields_shipping );

		return apply_filters( 'ywraq_form_connect_fields', array_keys( $fields ) );
	}
}

if ( ! function_exists( 'ywraq_get_accepted_quote_page' ) ) {
	/**
	 * Return the url of the accepted page for a quote
	 *
	 * @param $order WC_Order
	 *
	 * @return string
	 * @since  2.0.8
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_get_accepted_quote_page( $order ) {
		$args = array(
			'request_quote' => $order->get_id(),
			'status'        => 'accepted',
			'raq_nonce'     => ywraq_get_token( 'accept-request-quote', $order->get_id(), $order->get_meta( 'ywraq_customer_email' ) ),
			'lang'          => $order->get_meta( 'wpml_language' )
		);

		$base_url = ( yith_plugin_fw_is_true( $order->get_meta( '_ywraq_pay_quote_now' ) ) ) ? $order->get_checkout_payment_url( false ) : YITH_Request_Quote()->get_raq_page_url();

		//APPLY_FILTER: ywraq_accepted_quote_page : Filtering the page url to accept a quote: order, args and redirect page url are passed as arguments
		return apply_filters( 'ywraq_accepted_quote_page', add_query_arg( $args, $base_url ), $order, $args, $base_url );
	}
}

if ( ! function_exists( 'ywraq_get_rejected_quote_page' ) ) {
	/**
	 * Return the url of the rejected page for a quote
	 *
	 * @param $order WC_Order
	 *
	 * @return string
	 * @since  2.0.8
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_get_rejected_quote_page( $order ) {
		$args = array(
			'request_quote' => $order->get_id(),
			'status'        => 'rejected',
			'raq_nonce'     => ywraq_get_token( 'reject-request-quote', $order->get_id(), $order->get_meta( 'ywraq_customer_email' ) ),
			'lang'          => $order->get_meta( 'wpml_language' )
		);

		//APPLY_FILTER: ywraq_rejected_quote_page : Filtering the page url to reject a quote: order, args and request a quote page url are passed as arguments
		return apply_filters( 'ywraq_rejected_quote_page', add_query_arg( $args, YITH_Request_Quote()->get_raq_page_url() ), $order, $args, YITH_Request_Quote()->get_raq_page_url() );
	}
}


/* YITH Contact Form */
if ( ! function_exists( 'ywraq_yit_contact_form_installed' ) ) {
	/**
	 * Check if YIT Contact Form is installed
	 *
	 * @return bool
	 */
	function ywraq_yit_contact_form_installed() {
		return apply_filters( 'ywraq_yit_contact_form_installation', defined( 'YIT_CONTACT_FORM' ) );
	}
}

/* Contact Form 7 */
if ( ! function_exists( 'ywraq_cf7_form_installed' ) ) {
	/**
	 * Check if Contact Form 7 is installed
	 *
	 * @return bool
	 */
	function ywraq_cf7_form_installed() {
		return apply_filters( 'ywraq_cf7_form_installation', class_exists( 'WPCF7_ContactForm' ) );
	}
}

/* GRAVITY FORMS */
if ( ! function_exists( 'ywraq_gravity_form_installed' ) ) {
	/**
	 * Check if Gravity Form is installed.
	 *
	 * @return bool
	 */
	function ywraq_gravity_form_installed() {
		return apply_filters( 'ywraq_gravity_form_installation', class_exists( 'GFForms' ) );
	}
}

/* YITH WooCommerce Catalog Mode */
if ( ! function_exists( 'catalog_mode_plugin_enabled' ) ) {
	/**
	 * Check if is installed YITH WooCommerce Catalog Mode
	 *
	 * @return bool
	 */
	function catalog_mode_plugin_enabled() {
		return defined( 'YWCTM_PREMIUM' ) && YWCTM_PREMIUM && get_option( 'ywctm_enable_plugin ' ) == 'yes';
	}
}

/* POLYLANG */
if ( defined( 'POLYLANG_VERSION' ) ) {

	if ( ! function_exists( 'ywraq_pll_getLanguageEntity' ) ) {
		/**
		 * @param $slug
		 *
		 * @return bool
		 */
		function ywraq_pll_getLanguageEntity( $slug ) {
			global $polylang;

			$langs = $polylang->model->get_languages_list();

			foreach ( $langs as $lang ) {
				if ( $lang->slug == $slug ) {
					return $lang;
				}
			}

			return false;
		}
	}

	if ( ! function_exists( 'ywraq_pll_refresh_email_lang' ) ) {
		/**
		 * @param $order_id
		 *
		 * @return mixed
		 */
		function ywraq_pll_refresh_email_lang( $order_id ) {

			global $polylang;
			$order  = wc_get_order( $order_id );
			$lang   = yit_get_prop( $order, 'wpml_language', true );
			$entity = ywraq_pll_getLanguageEntity( $lang );

			if ( $entity ) {
				$polylang->curlang = $polylang->model->get_language( $entity->locale );

				$GLOBALS['text_direction'] = $entity->is_rtl ? 'rtl' : 'ltr';
				if ( class_exists( 'WP_Locale' ) ) {
					$GLOBALS['wp_locale'] = new \WP_Locale();
				}

				return $entity->locale;
			}

		}

		add_action( 'send_quote_mail_notification', 'ywraq_pll_refresh_email_lang', 10 );
	}

}

/* FLATSOME */
if ( ! function_exists( 'show_wraq_product_lightbox' ) ) {
	/**
	 * Show request a quote button on Flatsome Lightbox.
	 *
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function show_wraq_product_lightbox() {

		if ( ! function_exists( 'YITH_YWRAQ_Frontend' ) ) {
			return;
		}

		global $product;

		if ( ! $product ) {
			global $post;
			if ( ! $post || ! is_object( $post ) || ! is_singular() ) {
				return;
			}
			$product = wc_get_product( $post->ID );
		}
		$show_button_near_add_to_cart = get_option( 'ywraq_show_button_near_add_to_cart', 'no' );
		if ( yith_plugin_fw_is_true( $show_button_near_add_to_cart ) && $product->is_in_stock() ) {
			add_action( 'woocommerce_after_add_to_cart_button', array(
				YITH_YWRAQ_Frontend(),
				'add_button_single_page'
			) );
		} else {
			add_action( 'woocommerce_single_product_lightbox_summary', array(
				YITH_YWRAQ_Frontend(),
				'add_button_single_page'
			), 35 );
		}
	}

	add_action( 'wc_quick_view_before_single_product', 'show_wraq_product_lightbox' );
}

if ( ! function_exists( 'ywraq_is_true' ) ) {
	/**
	 * @param $value
	 *
	 * @return bool
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_is_true( $value ) {
		return true === $value || 1 === $value || '1' === $value || 'yes' === $value;
	}
}

/* Deprecated */
if ( ! function_exists( 'yith_ywraq_locate_template' ) ) {
	/**
	 * Locate the templates and return the path of the file found
	 *
	 * @param string $path
	 * @param array  $var
	 *
	 * @return string
	 * @since 1.0.0
	 * @deprecated
	 */
	function yith_ywraq_locate_template( $path, $var = null ) {

		if ( function_exists( 'WC' ) ) {
			$woocommerce_base = WC()->template_path();
		} elseif ( defined( 'WC_TEMPLATE_PATH' ) ) {
			$woocommerce_base = WC_TEMPLATE_PATH;
		} else {
			$woocommerce_base = WC()->plugin_path() . '/templates/';
		}

		$template_woocommerce_path = $woocommerce_base . $path;
		$template_path             = '/' . $path;
		$plugin_path               = YITH_YWRAQ_DIR . 'templates/' . $path;

		$located = locate_template( array(
			                            $template_woocommerce_path, // Search in <theme>/woocommerce/
			                            $template_path,             // Search in <theme>/
			                            $plugin_path                // Search in <plugin>/templates/
		                            ) );

		if ( ! $located && file_exists( $plugin_path ) ) {
			return apply_filters( 'yith_ywraq_locate_template', $plugin_path, $path );
		}

		return apply_filters( 'yith_ywraq_locate_template', $located, $path );
	}
}

if ( ! function_exists( 'ywraq_get_quote_line_total' ) ) {

	/**
	 * @param $key
	 * @param $raq
	 *
	 * @return int|mixed|string|void
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 * @deprecated
	 *
	 */
	function ywraq_get_quote_line_total( $key, $raq ) {
		$total = 0;

		if ( ! isset( $raq[ $key ] ) ) {
			return $total;
		}

		$raq_item = $raq[ $key ];
		$_product = wc_get_product( ( isset( $raq_item['variation_id'] ) && $raq_item['variation_id'] != '' ) ? $raq_item['variation_id'] : $raq_item['product_id'] );

		if ( ! $_product ) {
			return $total;
		}

		$price = yit_get_display_price( $_product, $price = '', $raq_item['quantity'] );
		$total = apply_filters( 'yith_ywraq_hide_price_template', $price, $_product->get_id(), $raq );

		if ( is_numeric( $total ) ) {
			$total = apply_filters( 'yith_ywraq_product_price', $price, $_product, $raq_item );
		}

		return wc_price( $total );
	}
}

if ( ! function_exists( 'ywraq_get_quote_total' ) ) {
	/**
	 * @param $raq
	 *
	 * @return string
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 * @deprecated
	 *
	 */
	function ywraq_get_quote_total( $raq ) {
		$total = 0;

		foreach ( $raq as $key => $raq_item ) {
			$_product = wc_get_product( ( isset( $raq_item['variation_id'] ) && $raq_item['variation_id'] != '' ) ? $raq_item['variation_id'] : $raq_item['product_id'] );

			if ( ! $_product ) {
				continue;
			}
			$price = yit_get_display_price( $_product, $price = '', $raq_item['quantity'] );
			$total += apply_filters( 'yith_ywraq_product_price', $price, $_product, $raq_item );
		}

		return wc_price( $total );
	}
}

if ( ! function_exists( 'ywraq_get_browse_list_message' ) ) {
	/**
	 * @return mixed|void
	 * @deprecated
	 */
	function ywraq_get_browse_list_message() {
		return ywraq_get_label( 'browse_list' );
	}
}

if ( ! function_exists( 'ywraq_convert_date_format' ) ) {

	/**
	 * @param $format
	 *
	 * @return mixed
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 * @deprecated
	 */
	function ywraq_convert_date_format( $format ) {

		$keys = array(
			'd' => '%d', //Day of the month, 2 digits with leading zeros (01 to 31)
			'D' => '%a', //A textual representation of a day, three letters (Mon through Sun)
			'j' => '%e', //Day of the month without leading zeros (1 to 31)
			'l' => '%A', //A full textual representation of the day of the week (Sunday through Saturday)
			'F' => '%B', //A full textual representation of a month, such as January or March
			'm' => '%m', //Numeric representation of a month, with leading zeros (01 through 12)
			'M' => '%b', //A short textual representation of a month, three letters (Jan through Dec)
			'n' => '%m', //Numeric representation of a month, without leading zeros (01 through 12)
			'Y' => '%Y', //A full numeric representation of a year, 4 digits (Examples: 1999 or 2003)
			'y' => '%y', //A two digit representation of a year (Examples: 99 or 03)
		);

		return str_replace( array_keys( $keys ), $keys, $format );

	}

}

if ( ! function_exists( 'ywraq_adjust_type' ) ) {
	/**
	 * @param $attr
	 * @param $value
	 *
	 * @return false|int
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 * @deprecated
	 */
	function ywraq_adjust_type( $attr, $value ) {
		switch ( $attr ) {
			case 'date_created':
				$value = strtotime( $value );
				break;
			default:
		}

		return $value;
	}
}

if ( ! function_exists( 'ywraq_get_available_gateways' ) ) {

	function ywraq_get_available_gateways() {
		$payment  = WC()->payment_gateways()->payment_gateways();
		$gateways = array();
		foreach ( $payment as $gateway ) {
			if ( $gateway->enabled == 'yes' ) {
				$gateways[ $gateway->id ] = $gateway->title;
			}
		}

		return $gateways;
	}
}

if ( ! function_exists( 'ywraq_get_cookie_name' ) ) {

	function ywraq_get_cookie_name( $name = 'session' ) {

		$cookie_names = array(
			'session' => 'session',
			'items'   => 'items_in_raq',
			'hash'    => 'hash'
		);

		$current_name = isset( $cookie_names[ $name ] ) ? $cookie_names[ $name ] : 'session';

		$cookie_prefix = apply_filters( 'ywraq_cookie_prefix', 'yith_ywraq_' );

		return $cookie_prefix . '' . $current_name;
	}
}


if ( ! function_exists( 'ywraq_get_ajax_default_loader' ) ) {
	/**
	 * Return the default loader.
	 *
	 * @return mixed|void
	 */
	function ywraq_get_ajax_default_loader() {

		$ajax_loader_default = YITH_YWRAQ_ASSETS_URL . '/images/ajax-loader.gif';
		if ( defined( 'YITH_PROTEO_VERSION' ) ) {
			$ajax_loader_default = YITH_YWRAQ_ASSETS_URL . '/images/proteo-loader.gif';
		}

		return apply_filters( 'ywraq_ajax_loader', $ajax_loader_default );
	}
}