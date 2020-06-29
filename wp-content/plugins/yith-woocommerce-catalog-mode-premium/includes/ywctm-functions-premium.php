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

if ( ! function_exists( 'ywctm_is_multivendor_active' ) ) {

	/**
	 * Check if YITH WooCommerce Multi Vendor is active
	 *
	 * @return  boolean
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_is_multivendor_active() {
		return defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM;
	}
}

if ( ! function_exists( 'ywctm_is_multivendor_integration_active' ) ) {

	/**
	 * Check if YITH WooCommerce Multi Vendor integration is active
	 *
	 * @return  boolean
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_is_multivendor_integration_active() {
		return get_option( 'yith_wpv_vendors_enable_catalog_mode' ) === 'yes';
	}
}

if ( ! function_exists( 'ywctm_get_vendor_id' ) ) {

	/**
	 * Get current vendor ID
	 *
	 * @param   $id_only boolean
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_vendor_id( $id_only = false ) {

		$vendor_id = '';

		if ( ywctm_is_multivendor_active() ) {

			$vendor = yith_get_vendor( 'current', 'user' );

			if ( 0 < $vendor->id && ! $vendor->is_super_user() ) {

				$vendor_id = ( $id_only ? $vendor->id : '_' . $vendor->id );

			}
		}

		return $vendor_id;

	}
}

if ( ! function_exists( 'ywctm_get_exclusion_fields' ) ) {

	/**
	 * Get the exclusion fiedls for Product, Category & Tag page
	 *
	 * @param   $item          array
	 *
	 * @return  array
	 * @since   2.0.3
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_exclusion_fields( $item ) {
		return array(
			array(
				'id'    => 'ywctm_enable_inquiry_form',
				'name'  => 'ywctm_enable_inquiry_form',
				'type'  => 'onoff',
				'title' => esc_html__( 'Inquiry Form', 'yith-woocommerce-catalog-mode' ),
				'value' => $item['enable_inquiry_form'],
				'desc'  => esc_html__( 'Choose whether to show or hide the enquiry form in these product pages.', 'yith-woocommerce-catalog-mode' ),
			),
			array(
				'id'    => 'ywctm_enable_atc_custom_options',
				'name'  => 'ywctm_enable_atc_custom_options',
				'type'  => 'onoff',
				'title' => esc_html__( 'Use custom options for "Add to Cart"', 'yith-woocommerce-catalog-mode' ),
				'value' => $item['enable_atc_custom_options'],
				'desc'  => esc_html__( 'Enable to override the default settings for add to cart button.', 'yith-woocommerce-catalog-mode' ),
			),
			array(
				'id'      => 'ywctm_atc_status',
				'name'    => 'ywctm_atc_status',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => array(
					'show' => esc_html__( 'Visible', 'yith-woocommerce-catalog-mode' ),
					'hide' => esc_html__( 'Hidden', 'yith-woocommerce-catalog-mode' ),
				),
				'title'   => esc_html__( 'Set "Add to Cart" as:', 'yith-woocommerce-catalog-mode' ),
				'value'   => $item['atc_status'],
			),
			array(
				'id'      => 'ywctm_custom_button',
				'name'    => 'ywctm_custom_button',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => ywctm_get_buttons_labels(),
				'default' => 'none',
				'title'   => esc_html__( 'Replace "Add to Cart" in product page with:', 'yith-woocommerce-catalog-mode' ),
				'value'   => $item['custom_button'],
			),
			array(
				'id'      => 'ywctm_custom_button_url',
				'name'    => 'ywctm_custom_button_url',
				'type'    => 'text',
				'default' => '',
				'title'   => esc_html__( 'Override URL:', 'yith-woocommerce-catalog-mode' ),
				'value'   => isset( $item['custom_button_url'] ) ? $item['custom_button_url'] : '',
				'desc'    => esc_html__( 'Replace the button URL with a custom one. Leave empty to use the default URL.', 'yith-woocommerce-catalog-mode' ),
			),
			array(
				'id'      => 'ywctm_custom_button_loop',
				'name'    => 'ywctm_custom_button_loop',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => ywctm_get_buttons_labels(),
				'default' => 'none',
				'title'   => esc_html__( 'Replace "Add to Cart" in shop page with:', 'yith-woocommerce-catalog-mode' ),
				'value'   => $item['custom_button_loop'],
			),
			array(
				'id'      => 'ywctm_custom_button_loop_url',
				'name'    => 'ywctm_custom_button_loop_url',
				'type'    => 'text',
				'default' => '',
				'title'   => esc_html__( 'Override URL:', 'yith-woocommerce-catalog-mode' ),
				'value'   => isset( $item['custom_button_loop_url'] ) ? $item['custom_button_loop_url'] : '',
				'desc'    => esc_html__( 'Replace the button URL with a custom one. Leave empty to use the default URL.', 'yith-woocommerce-catalog-mode' ),
			),
			array(
				'id'    => 'ywctm_enable_price_custom_options',
				'name'  => 'ywctm_enable_price_custom_options',
				'type'  => 'onoff',
				'title' => esc_html__( 'Use custom options for price', 'yith-woocommerce-catalog-mode' ),
				'value' => $item['enable_price_custom_options'],
				'desc'  => esc_html__( 'Enable to override the default settings for price.', 'yith-woocommerce-catalog-mode' ),
			),
			array(
				'id'      => 'ywctm_price_status',
				'name'    => 'ywctm_price_status',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => array(
					'show' => esc_html__( 'Visible', 'yith-woocommerce-catalog-mode' ),
					'hide' => esc_html__( 'Hidden', 'yith-woocommerce-catalog-mode' ),
				),
				'title'   => esc_html__( 'Set price as:', 'yith-woocommerce-catalog-mode' ),
				'value'   => $item['price_status'],
			),
			array(
				'id'      => 'ywctm_custom_price_text',
				'name'    => 'ywctm_custom_price_text',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => ywctm_get_buttons_labels(),
				'default' => 'none',
				'title'   => esc_html__( 'Replace price with:', 'yith-woocommerce-catalog-mode' ),
				'value'   => $item['custom_price_text'],
			),
			array(
				'id'      => 'ywctm_custom_price_text_url',
				'name'    => 'ywctm_custom_price_text_url',
				'type'    => 'text',
				'default' => '',
				'title'   => esc_html__( 'Override URL:', 'yith-woocommerce-catalog-mode' ),
				'value'   => isset( $item['custom_price_text_url'] ) ? $item['custom_price_text_url'] : '',
				'desc'    => esc_html__( 'Replace the button URL with a custom one. Leave empty to use the default URL.', 'yith-woocommerce-catalog-mode' ),
			),
		);
	}
}

/**
 * CUSTOM BUTTON RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywctm_get_buttons_labels' ) ) {

	/**
	 * Get the list of all buttons and labels
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_buttons_labels() {

		$data = get_posts(
			array(
				'post_type'        => 'ywctm-button-label',
				'suppress_filters' => false,
				'numberposts'      => - 1,
			)
		);
		$list = array(
			'none' => esc_html__( 'Nothing', 'yith-woocommerce-catalog-mode' ),
		);
		if ( $data ) {
			foreach ( $data as $post ) {
				$list[ $post->ID ] = '' !== $post->post_title ? $post->post_title : esc_html__( '(no name)', 'yith-woocommerce-catalog-mode' );
			}
		}

		return $list;

	}
}

if ( ! function_exists( 'ywctm_get_active_buttons_id' ) ) {

	/**
	 * Get the IDs of all buttons and labels
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_active_buttons_id() {
		$list = get_posts(
			array(
				'post_type'   => 'ywctm-button-label',
				'numberposts' => - 1,
				'fields'      => 'ids',
			)
		);

		return $list;
	}
}

if ( ! function_exists( 'ywctm_get_buttons_label_name' ) ) {

	/**
	 * Get the list of all buttons and labels
	 *
	 * @param   $id integer
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_buttons_label_name( $id ) {

		$post  = get_post( $id );
		$title = $post ? $post->post_title : esc_html__( 'Nothing', 'yith-woocommerce-catalog-mode' );
		$title = '' !== $title ? $title : esc_html__( '(no name)', 'yith-woocommerce-catalog-mode' );

		return '<strong>' . $title . '</strong>';
	}
}

if ( ! function_exists( 'ywctm_get_icon_class' ) ) {

	/**
	 * Get Icon Class
	 *
	 * @param   $icon string
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_icon_class( $icon ) {

		$icon_data  = explode( ':', $icon );
		$icon_class = '';

		switch ( $icon_data[0] ) {
			case 'FontAwesome':
				$icon_class = 'fa fa-' . $icon_data[1];
				break;
			case 'Dashicons':
				$icon_class = 'dashicons dashicons-' . $icon_data[1];
				break;
			case 'retinaicon-font':
				$icon_class = 'retinaicon-font ' . $icon_data[1];
				break;
			default:
		}

		return $icon_class;

	}
}

if ( ! function_exists( 'ywctm_get_button_label_settings' ) ) {

	/**
	 * Get settings of selected custom button
	 *
	 * @param   $id integer
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_button_label_settings( $id ) {

		return apply_filters(
			'ywctm_button_label_settings',
			array(
				'label_text'              => get_post_meta( $id, 'ywctm_label_text', true ),
				'button_url_type'         => get_post_meta( $id, 'ywctm_button_url_type', true ),
				'button_url'              => get_post_meta( $id, 'ywctm_button_url', true ),
				'text_color'              => get_post_meta( $id, 'ywctm_text_color', true ),
				'icon_color'              => get_post_meta( $id, 'ywctm_icon_color', true ),
				'background_color'        => get_post_meta( $id, 'ywctm_background_color', true ),
				'border_color'            => get_post_meta( $id, 'ywctm_border_color', true ),
				'border_style'            => get_post_meta( $id, 'ywctm_border_style', true ),
				'icon_type'               => get_post_meta( $id, 'ywctm_icon_type', true ),
				'selected_icon'           => get_post_meta( $id, 'ywctm_selected_icon', true ),
				'selected_icon_size'      => get_post_meta( $id, 'ywctm_selected_icon_size', true ),
				'selected_icon_alignment' => get_post_meta( $id, 'ywctm_selected_icon_alignment', true ),
				'custom_icon'             => get_post_meta( $id, 'ywctm_custom_icon', true ),
				'width_settings'          => get_post_meta( $id, 'ywctm_width_settings', true ),
				'margin_settings'         => get_post_meta( $id, 'ywctm_margin_settings', true ),
				'padding_settings'        => get_post_meta( $id, 'ywctm_padding_settings', true ),
			),
			$id
		);

	}
}

if ( ! function_exists( 'ywctm_enabled_google_fonts' ) ) {

	/**
	 * Get enabled Google Fonts
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_enabled_google_fonts() {

		//APPLY_FILTERS: ywctm_google_fonts: add or remove supported Google Fonts
		return apply_filters(
			'ywctm_google_fonts',
			array(
				'Roboto'          => 'Roboto,sans-serif',
				'Slabo 27px'      => 'Slabo 27px,serif',
				'Oswald'          => 'Oswald,sans-serif',
				'Montserrat'      => 'Montserrat,sans-serif',
				'Source Sans Pro' => 'Source Sans Pro,sans-serif',
				'Dancing Script'  => 'Dancing Script,cursive',
				'Lora'            => 'Lora,serif',
				'Gochi Hand'      => 'GochiHand,cursive',
			)
		);
	}
}

if ( ! function_exists( 'ywctm_parse_icons' ) ) {

	/**
	 * Replaces the placeholders with icons HTML
	 *
	 * @param   $text string
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_parse_icons( $text ) {
		$pattern     = '/{{(((\w+-?)*) ((\w+\d*-?)*))}}/m';
		$replacement = '<i class="$1"></i>';

		return preg_replace( $pattern, $replacement, $text );
	}
}

if ( ! function_exists( 'ywctm_get_custom_button_url_override' ) ) {

	/**
	 * Get the custom URL override
	 *
	 * @param   $product  WC_Product
	 * @param   $type     string
	 * @param   $is_loop  boolean
	 *
	 * @return  string
	 * @since   2.0.3
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_custom_button_url_override( $product, $type, $is_loop = false ) {

		if ( ! $is_loop && 'atc' === $type ) {
			$option = 'custom_button_url';
		} elseif ( $is_loop && 'atc' === $type ) {
			$option = 'custom_button_loop_url';
		} else {
			$option = 'custom_price_text_url';
		}

		$product_exclusion = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_exclusion_settings' ), $product->get_id(), '_ywctm_exclusion_settings' );

		if ( $product_exclusion ) {

			if ( 'yes' === $product_exclusion[ 'enable_' . $type . '_custom_options' ] ) {
				return $product_exclusion[ $option ];
			}
		}

		$product_cats = wp_get_object_terms( $product->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
		foreach ( $product_cats as $cat_id ) {

			$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_exclusion_settings', true ), $product->get_id(), $cat_id, '_ywctm_exclusion_settings' );
			if ( $product_exclusion ) {

				if ( 'yes' === $product_exclusion[ 'enable_' . $type . '_custom_options' ] ) {

					return $product_exclusion[ $option ];
				}
			}
		}

		$product_tags = wp_get_object_terms( $product->get_id(), 'product_tag', array( 'fields' => 'ids' ) );
		foreach ( $product_tags as $tag_id ) {

			$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_exclusion_settings', true ), $product->get_id(), $tag_id, '_ywctm_exclusion_settings' );
			if ( $product_exclusion ) {

				if ( 'yes' === $product_exclusion[ 'enable_' . $type . '_custom_options' ] ) {

					return $product_exclusion[ $option ];
				}
			}
		}

		return '';

	}
}

if ( ! function_exists( 'ywctm_buttons_id_with_custom_url' ) ) {

	/**
	 * Get the IDs of all buttons and labels with custom URL
	 *
	 * @return  array
	 * @since   2.0.3
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_buttons_id_with_custom_url() {
		$list = get_posts(
			array(
				'post_type'   => 'ywctm-button-label',
				'numberposts' => - 1,
				'fields'      => 'ids',
				'meta_key'    => 'ywctm_button_url_type',
				'meta_value'  => 'custom',
			)
		);

		return $list;
	}
}

/**
 * EXCLUSION TABLE RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywctm_add_to_cart_column' ) ) {

	/**
	 * Print the add to cart column in the exclusion table
	 *
	 * @param   $item array
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_add_to_cart_column( $item ) {

		$exclusion = maybe_unserialize( $item['exclusion'] );
		$replace   = '';

		if ( 'no' === $exclusion['enable_atc_custom_options'] ) {
			$atc_global = get_option( 'ywctm_hide_add_to_cart_settings' . ywctm_get_vendor_id() );
			$status     = 'hide' === $atc_global['action'] ? esc_html__( 'Hidden', 'yith-woocommerce-catalog-mode' ) : esc_html__( 'Visible', 'yith-woocommerce-catalog-mode' );
		} else {
			$status = 'hide' === $exclusion['atc_status'] ? esc_html__( 'Hidden', 'yith-woocommerce-catalog-mode' ) : esc_html__( 'Visible', 'yith-woocommerce-catalog-mode' );
			if ( 'none' !== $exclusion['custom_button'] && 'hide' === $exclusion['atc_status'] ) {
				/* translators: %s button name */
				$replace .= ' <br />' . sprintf( esc_html__( 'Replaced with %s in product page', 'yith-woocommerce-catalog-mode' ), ywctm_get_buttons_label_name( $exclusion['custom_button'] ) );
			}
			if ( 'none' !== $exclusion['custom_button_loop'] && 'hide' === $exclusion['atc_status'] ) {
				/* translators: %s button name */
				$replace .= ' <br />' . sprintf( esc_html__( 'Replaced with %s in shop page', 'yith-woocommerce-catalog-mode' ), ywctm_get_buttons_label_name( $exclusion['custom_button_loop'] ) );
			}
		}

		return sprintf( '%s%s', $status, $replace );

	}
}

if ( ! function_exists( 'ywctm_price_column' ) ) {

	/**
	 * Print the price column in the exclusion table
	 *
	 * @param   $item array
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_price_column( $item ) {

		$exclusion = maybe_unserialize( $item['exclusion'] );
		$replace   = '';

		if ( 'no' === $exclusion['enable_price_custom_options'] ) {
			$price_global = get_option( 'ywctm_hide_price_settings' . ywctm_get_vendor_id() );
			$status       = 'hide' === $price_global['action'] ? esc_html__( 'Hidden', 'yith-woocommerce-catalog-mode' ) : esc_html__( 'Visible', 'yith-woocommerce-catalog-mode' );
		} else {
			$status = 'hide' === $exclusion['price_status'] ? esc_html__( 'Hidden', 'yith-woocommerce-catalog-mode' ) : esc_html__( 'Visible', 'yith-woocommerce-catalog-mode' );
			if ( 'none' !== $exclusion['custom_price_text'] && 'hide' === $exclusion['price_status'] ) {
				/* translators: %s button name */
				$replace = ' <br />' . sprintf( esc_html__( 'Replaced with %s', 'yith-woocommerce-catalog-mode' ), ywctm_get_buttons_label_name( $exclusion['custom_price_text'] ) );
			}
		}

		return sprintf( '%s%s', $status, $replace );

	}
}

if ( ! function_exists( 'ywctm_item_type_column' ) ) {

	/**
	 * Print the item type column in the exclusion table
	 *
	 * @param   $item_type string
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_item_type_column( $item_type ) {

		$item_types = array(
			'product'  => esc_html__( 'Product', 'yith-woocommerce-catalog-mode' ),
			'category' => esc_html__( 'Category', 'yith-woocommerce-catalog-mode' ),
			'tag'      => esc_html__( 'Tag', 'yith-woocommerce-catalog-mode' ),
		);

		return $item_types[ $item_type ];

	}
}

if ( ! function_exists( 'ywctm_item_name_column' ) ) {

	/**
	 * Print item name with action links in the exclusion table
	 *
	 * @param   $item    array
	 * @param   $table   YITH_Custom_Table
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_item_name_column( $item, $table ) {

		$query_args = array(
			'page'    => $_GET['page'],
			'tab'     => $_GET['tab'],
			'sub_tab' => $_GET['sub_tab'],
			'id'      => $item['ID'],
		);

		if ( isset( $_GET['paged'] ) ) {
			$query_args['return_page'] = $_GET['paged'];
		}

		$section = str_replace( 'exclusions-', '', $_GET['sub_tab'] );

		if ( 'items' === $section ) {
			$query_args['item_type'] = $item['item_type'];
			$section                 = $item['item_type'];
		}

		$items      = array(
			'product'  => array(
				'edit_label' => esc_html__( 'Edit product', 'yith-woocommerce-catalog-mode' ),
				'edit_link'  => esc_url(
					add_query_arg(
						array(
							'post'   => $item['ID'],
							'action' => 'edit',
						),
						admin_url( 'post.php' )
					)
				),
				'view_link'  => get_permalink( $item['ID'] ),
			),
			'category' => array(
				'edit_label' => esc_html__( 'Edit category', 'yith-woocommerce-catalog-mode' ),
				'edit_link'  => esc_url(
					add_query_arg(
						array(
							'taxonomy'  => 'product_cat',
							'post_type' => 'product',
							'tag_ID'    => $item['ID'],
							'action'    => 'edit',
						),
						admin_url( 'edit-tags.php' )
					)
				),
				'view_link'  => get_term_link( intval( $item['ID'] ), 'product_cat' ),
			),
			'tag'      => array(
				'edit_label' => esc_html__( 'Edit tag', 'yith-woocommerce-catalog-mode' ),
				'edit_link'  => esc_url(
					add_query_arg(
						array(
							'taxonomy'  => 'product_tag',
							'post_type' => 'product',
							'tag_ID'    => $item['ID'],
							'action'    => 'edit',
						),
						admin_url( 'edit-tags.php' )
					)
				),
				'view_link'  => get_term_link( intval( $item['ID'] ), 'product_tag' ),
			),
			'vendors'  => array(
				'edit_label' => esc_html__( 'Edit vendor', 'yith-woocommerce-catalog-mode' ),
				'edit_link'  => esc_url(
					add_query_arg(
						array(
							'taxonomy'  => 'yith_shop_vendor',
							'post_type' => 'product',
							'tag_ID'    => $item['ID'],
							'action'    => 'edit',
						),
						admin_url( 'edit-tags.php' )
					)
				),
				'view_link'  => get_term_link( intval( $item['ID'] ), 'yith_shop_vendor' ),
			),
		);
		$edit_url   = esc_url( add_query_arg( array_merge( $query_args, array( 'action' => 'edit' ) ), admin_url( 'admin.php' ) ) );
		$delete_url = esc_url( add_query_arg( array_merge( $query_args, array( 'action' => 'delete' ) ), admin_url( 'admin.php' ) ) );
		$actions    = array(
			'edit'   => sprintf( '<a href="%s">%s</a>', $edit_url, esc_html__( 'Edit exclusion', 'yith-woocommerce-catalog-mode' ) ),
			'item'   => sprintf( '<a target="_blank" href="%s">%s</a>', $items[ $section ]['edit_link'], $items[ $section ]['edit_label'] ),
			'delete' => sprintf( '<a href="%s">%s</a>', $delete_url, esc_html__( 'Remove from list', 'yith-woocommerce-catalog-mode' ) ),
			'view'   => sprintf( '<a target="_blank" href="%s">%s</a>', $items[ $section ]['view_link'], esc_html__( 'View', 'yith-woocommerce-catalog-mode' ) ),
		);

		if ( '' !== ywctm_get_vendor_id( true ) && 'products' !== $section ) {
			unset( $actions['item'] );
		}

		return sprintf( '<strong><a class="row-title" href="%s" data-tip="%s">#%d %s </a></strong> %s', $edit_url, esc_html__( 'Edit exclusion', 'yith-woocommerce-catalog-mode' ), $item['ID'], $item['name'], $table->__call( 'row_actions', array( $actions ) ) );

	}
}

if ( ! function_exists( 'ywctm_inquiry_form_column' ) ) {

	/**
	 * Print the inquiry form column in the exclusion table
	 *
	 * @param   $item array
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_inquiry_form_column( $item ) {

		$exclusion = maybe_unserialize( $item['exclusion'] );

		$args = array(
			'id'    => 'enable_inquiry_form_' . $item['item_type'] . '_' . $item['ID'],
			'name'  => 'enable_inquiry_form',
			'type'  => 'onoff',
			'value' => $exclusion['enable_inquiry_form'],
			'data'  =>
				array(
					'item-id' => $item['ID'],
					'section' => $item['item_type'],
				),
		);

		yith_plugin_fw_get_field( $args, true );

	}
}

if ( ! function_exists( 'ywctm_vendor_column' ) ) {

	/**
	 * Print the exclude vendor column in the exclusion table
	 *
	 * @param   $item array
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_vendor_column( $item ) {

		$exclusion = $item['exclude'];

		$args = array(
			'id'    => 'exclude_vendor' . $item['ID'],
			'name'  => 'exclude_vendor',
			'type'  => 'onoff',
			'value' => $exclusion,
		);

		yith_plugin_fw_get_field( $args, true );

	}
}

if ( ! function_exists( 'ywctm_enable_inquiry_form' ) ) {

	/**
	 * Enable/disable inquiry from exclusion list overview
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_enable_inquiry_form() {

		try {

			$option_name = ( '' !== $_POST['vendor_id'] ? '_ywctm_exclusion_settings_' . $_POST['vendor_id'] : '_ywctm_exclusion_settings' );

			switch ( $_POST['section'] ) {

				case 'category':
				case 'tag':
					$exclusion_data                        = get_term_meta( $_POST['item_id'], $option_name, true );
					$exclusion_data['enable_inquiry_form'] = $_POST['enabled'];

					update_term_meta( $_POST['item_id'], $option_name, $exclusion_data );

					break;
				default:
					$product                               = wc_get_product( $_POST['item_id'] );
					$exclusion_data                        = $product->get_meta( $option_name );
					$exclusion_data['enable_inquiry_form'] = $_POST['enabled'];
					$product->update_meta_data( $option_name, $exclusion_data );
					$product->save();
			}

			wp_send_json( array( 'success' => true ) );

		} catch ( Exception $e ) {

			wp_send_json(
				array(
					'success' => false,
					'error'   => $e->getMessage(),
				)
			);

		}
	}

	add_action( 'wp_ajax_ywctm_enable_inquiry_form', 'ywctm_enable_inquiry_form' );

}

if ( ! function_exists( 'ywctm_exclude_vendor' ) ) {

	/**
	 * Enable/disable vendor exclusion
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_exclude_vendor() {

		try {
			$exclusion_data = isset( $_POST['enabled'] ) ? 'yes' : 'no';
			update_term_meta( $_POST['item_id'], '_ywctm_vendor_override_exclusion', $exclusion_data );
			wp_send_json( array( 'success' => true ) );

		} catch ( Exception $e ) {

			wp_send_json(
				array(
					'success' => false,
					'error'   => $e->getMessage(),
				)
			);

		}
	}

	add_action( 'wp_ajax_ywctm_exclude_vendor', 'ywctm_exclude_vendor' );

}

if ( ! function_exists( 'ywctm_set_table_columns' ) ) {

	/**
	 * Prepare columns for exclusion table
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_set_table_columns() {

		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'item_name'   => esc_html__( 'Item Name', 'yith-woocommerce-catalog-mode' ),
			'item_type'   => esc_html__( 'Item Type', 'yith-woocommerce-catalog-mode' ),
			'add_to_cart' => esc_html__( 'Add to cart', 'yith-woocommerce-catalog-mode' ),
			'show_price'  => esc_html__( 'Price', 'yith-woocommerce-catalog-mode' ),
		);

		$enabled = get_option( 'ywctm_inquiry_form_enabled' . ywctm_get_vendor_id(), 'hidden' );

		if ( 'hidden' !== $enabled && ywctm_exists_inquiry_forms() ) {
			$columns['inquiry_form'] = esc_html__( 'Inquiry form', 'yith-woocommerce-catalog-mode' );
		}

		return $columns;
	}
}

/**
 * INQUIRY FORM RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywctm_exists_inquiry_forms' ) ) {

	/**
	 * Check if at least a form plugin is active
	 *
	 * @return  boolean
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_exists_inquiry_forms() {

		$form_plugins = ywctm_get_active_form_plugins();
		unset( $form_plugins['none'] );

		return ( ! empty( $form_plugins ) );

	}
}

if ( ! function_exists( 'ywctm_get_active_form_plugins' ) ) {

	/**
	 * Get active form plugins
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_active_form_plugins() {

		$active_plugins = array(
			'none' => esc_html__( 'Select plugin', 'yith-woocommerce-catalog-mode' ),
		);

		if ( function_exists( 'YIT_Contact_Form' ) ) {
			$active_plugins['yit-contact-form'] = 'YIT Contact Form';
		}

		if ( function_exists( 'wpcf7_contact_form' ) ) {
			$active_plugins['contact-form-7'] = 'Contact Form 7';
		}

		if ( function_exists( 'Ninja_Forms' ) ) {
			$active_plugins['ninja-forms'] = 'Ninja Forms';
		}

		if ( class_exists( 'FrmAppHelper' ) ) {
			$active_plugins['formidable-forms'] = 'Formidable Forms';
		}

		if ( function_exists( 'gravity_form' ) ) {
			$active_plugins['gravity-forms'] = 'Gravity Forms';
		}

		return $active_plugins;
	}
}

if ( ! function_exists( 'ywctm_get_forms_list' ) ) {

	/**
	 * Get list of forms
	 *
	 * @param $form_plugin string
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_forms_list( $form_plugin ) {

		$forms = '';

		switch ( $form_plugin ) {
			case 'yit-contact-form':
				$forms = ywctm_yit_get_contact_forms();
				break;
			case 'contact-form-7':
				$forms = ywctm_wpcf7_get_contact_forms();
				break;
			case 'ninja-forms':
				$forms = ywctm_ninja_get_contact_forms();
				break;
			case 'formidable-forms':
				$forms = ywctm_formidable_get_contact_forms();
				break;
			case 'gravity-forms':
				$forms = ywctm_gravity_get_contact_forms();
				break;
		}

		if ( ! is_array( $forms ) ) {

			if ( 'inactive' === $forms ) {
				$form_list = array( 'none' => esc_html__( 'Plugin not activated or not installed', 'yith-woocommerce-catalog-mode' ) );
			} else {
				$form_list = array( 'none' => esc_html__( 'No contact form found', 'yith-woocommerce-catalog-mode' ) );
			}
		} else {
			$form_list = $forms;
		}

		return $form_list;

	}
}

if ( ! function_exists( 'ywctm_yit_get_contact_forms' ) ) {

	/**
	 * Get list of forms by YIT Contact Form plugin
	 *
	 * @return  array|string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_yit_get_contact_forms() {

		if ( ! function_exists( 'YIT_Contact_Form' ) ) {
			return 'inactive';
		}

		$active_forms = array();
		$forms        = get_posts( array( 'post_type' => YIT_Contact_Form()->contact_form_post_type ) );

		foreach ( $forms as $form ) {
			$active_forms[ $form->post_name ] = $form->post_title;
		}

		if ( array() === $active_forms ) {
			return 'no-forms';
		}

		return $active_forms;

	}
}

if ( ! function_exists( 'ywctm_wpcf7_get_contact_forms' ) ) {

	/**
	 * Get list of forms by Contact Form 7 plugin
	 *
	 * @return  array|string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_wpcf7_get_contact_forms() {

		if ( ! function_exists( 'wpcf7_contact_form' ) ) {
			return 'inactive';
		}

		$active_forms = array();
		$forms        = WPCF7_ContactForm::find();

		foreach ( $forms as $form ) {
			$active_forms[ $form->id() ] = $form->title();
		}

		if ( array() === $active_forms ) {
			return 'no-forms';
		}

		return $active_forms;

	}
}

if ( ! function_exists( 'ywctm_ninja_get_contact_forms' ) ) {

	/**
	 * Get list of forms by Ninja Forms plugin
	 *
	 * @return  array|string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_ninja_get_contact_forms() {

		if ( ! function_exists( 'Ninja_Forms' ) ) {
			return 'inactive';
		}

		$active_forms = array();
		$forms        = Ninja_Forms()->form()->get_forms();

		foreach ( $forms as $form ) {
			$active_forms[ $form->get_id() ] = $form->get_setting( 'title' );
		}

		if ( array() === $active_forms ) {
			return 'no-forms';
		}

		return $active_forms;

	}
}

if ( ! function_exists( 'ywctm_formidable_get_contact_forms' ) ) {

	/**
	 * Get list of forms by Formidable Forms plugin
	 *
	 * @return  array|string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_formidable_get_contact_forms() {

		if ( ! class_exists( 'FrmAppHelper' ) ) {
			return 'inactive';
		}

		$active_forms = array();
		$forms        = FrmForm::getAll();

		foreach ( $forms as $form ) {
			$active_forms[ $form->id ] = $form->name;
		}

		if ( array() === $active_forms ) {
			return 'no-forms';
		}

		return $active_forms;

	}
}

if ( ! function_exists( 'ywctm_gravity_get_contact_forms' ) ) {

	/**
	 * Get list of forms by Gravity Forms plugin
	 *
	 * @return  array|string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_gravity_get_contact_forms() {

		if ( ! function_exists( 'gravity_form' ) ) {
			return 'inactive';
		}

		$active_forms = array();
		$forms        = RGFormsModel::get_forms( null, 'title' );

		foreach ( $forms as $form ) {
			$active_forms[ $form->id ] = $form->title;
		}

		if ( array() === $active_forms ) {
			return 'no-forms';
		}

		return $active_forms;

	}
}

if ( ! function_exists( 'ywctm_ninja_message' ) ) {

	/**
	 * Append Product page permalink to mail body and to database entry (Ninja Forms)
	 *
	 * @param   $data array
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_ninja_message( $data ) {

		$post_id   = false;
		$field_key = false;
		foreach ( $data['fields'] as $key => $field ) {
			if ( 'ywctm-product-id' === $field['key'] ) {
				$post_id   = $field['value'];
				$field_key = $key;
				break;
			}
		}

		if ( $post_id ) {

			$ninja_forms = ywctm_get_localized_form( 'ninja-forms', $post_id );

			if ( $data['id'] === $ninja_forms && apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_product_permalink' ), $post_id, 'ywctm_inquiry_product_permalink' ) === 'yes' ) {

				$product = wc_get_product( $post_id );

				$data['fields'][ $field_key ]['value'] = sprintf( '%s: %s', $product->get_formatted_name(), $product->get_permalink() );

			}
		}

		return $data;
	}

	add_filter( 'ninja_forms_submit_data', 'ywctm_ninja_message', 10 );

}

if ( ! function_exists( 'ywctm_formidable_message' ) ) {

	/**
	 * Append Product page permalink to mail body and to database entry (Formidable Forms)
	 *
	 * @param   $values array
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_formidable_message( $values ) {

		if ( isset( $values['ywctm-product-id'] ) ) {

			$post_id         = $values['ywctm-product-id'];
			$form_id         = $values['form_id'];
			$field_id        = $values['ywctm-ff-field-id'];
			$formidable_form = ywctm_get_localized_form( 'formidable-forms', $post_id );

			if ( (int) $form_id === (int) $formidable_form && apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_product_permalink' ), $post_id, 'ywctm_inquiry_product_permalink' ) === 'yes' ) {

				$product = wc_get_product( $post_id );

				$values['item_meta'][ $field_id ] = sprintf( '<a href="%s">%s</a>', $product->get_permalink(), $product->get_formatted_name() );
			}
		}

		return $values;

	}

	add_filter( 'frm_pre_create_entry', 'ywctm_formidable_message' );

}

if ( ! function_exists( 'ywctm_cf7_message' ) ) {

	/**
	 * Append Product page permalink to mail body (WPCF7)
	 *
	 * @param   $components   array
	 * @param   $contact_form WPCF7_ContactForm
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_cf7_message( $components, $contact_form ) {

		if ( isset( $_REQUEST['ywctm-product-id'] ) ) {

			$post_id        = $_REQUEST['ywctm-product-id'];
			$contact_form_7 = ywctm_get_localized_form( 'contact-form-7', $post_id );

			if ( $contact_form->id() === (int) $contact_form_7 && apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_product_permalink' ), $post_id, 'ywctm_inquiry_product_permalink' ) === 'yes' ) {

				$form_atts    = $contact_form->get_properties();
				$field_label  = esc_html__( 'Related product', 'yith-woocommerce-catalog-mode' );
				$product      = wc_get_product( $post_id );
				$product_link = $product->get_permalink();
				$product_name = $product->get_formatted_name();

				if ( ! $form_atts['mail']['use_html'] ) {

					$field_data = "{$field_label}: {$product_name} - {$product_link}\n\n";

				} else {

					ob_start();
					?>
					<p>
						<?php echo $field_label; ?>: <a href="<?php echo $product_link; ?>"><?php echo $product_name; ?></a>
					</p>
					<?php
					$field_data = ob_get_clean();

				}

				$components['body'] = $field_data . $components['body'];

			}
		}

		return $components;

	}

	add_filter( 'wpcf7_mail_components', 'ywctm_cf7_message', 10, 2 );

}

if ( ! function_exists( 'ywctm_gravity_message' ) ) {

	/**
	 * Append Product page permalink to mail body (Gravity Forms)
	 *
	 * @param   $components  array
	 * @param   $mail_format string
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_gravity_message( $components, $mail_format ) {

		if ( isset( $_REQUEST['ywctm-product-id'] ) ) {

			$post_id = $_REQUEST['ywctm-product-id'];

			if ( apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_product_permalink' ), $post_id, 'ywctm_inquiry_product_permalink' ) === 'yes' ) {

				$field        = '';
				$lead         = '';
				$field_label  = esc_html__( 'Related product', 'yith-woocommerce-catalog-mode' );
				$product      = wc_get_product( $post_id );
				$product_link = $product->get_permalink();
				$product_name = $product->get_formatted_name();

				if ( 'html' !== $mail_format ) {

					$field_data = "{$field_label}: {$product_name} - {$product_link}\n\n";

				} else {

					ob_start();
					?>
					<table width="99%" border="0" cellpadding="1" cellspacing="0" bgcolor="#EAEAEA">
						<tr>
							<td>
								<table width="100%" border="0" cellpadding="5" cellspacing="0" bgcolor="#FFFFFF">
									<tr bgcolor="<?php echo apply_filters( 'gform_email_background_color_label', '#EAF2FA', $field, $lead ); ?>">
										<td colspan="2">
											<font
												style="font-family: sans-serif; font-size:12px;"><strong><?php echo $field_label; ?></strong></font>
										</td>
									</tr>
									<tr bgcolor="<?php echo apply_filters( 'gform_email_background_color_data', '#FFFFFF', $field, $lead ); ?>">
										<td width="20">&nbsp;</td>
										<td>
											<a href="<?php echo $product_link; ?>" style="font-family: sans-serif; font-size:12px;"><?php echo $product_name; ?></a>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<br />
					<?php
					$field_data = ob_get_clean();

				}

				$components['message'] = $field_data . $components['message'];

			}
		}

		return $components;

	}

	add_filter( 'gform_pre_send_email', 'ywctm_gravity_message', 10, 2 );

}

if ( ! function_exists( 'ywctm_get_localized_form' ) ) {

	/**
	 * Get form id for current language
	 *
	 * @param   $form_type string
	 * @param   $post_id   integer
	 *
	 * @return  integer
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_localized_form( $form_type, $post_id ) {

		if ( ywctm_is_wpml_active() ) {
			$option_name  = 'ywctm_inquiry_' . str_replace( '-', '_', $form_type ) . '_id_wpml';
			$options      = apply_filters( 'ywctm_get_vendor_option', get_option( $option_name, '' ), $post_id, $option_name );
			$default_form = isset( $options[ wpml_get_default_language() ] ) ? $options[ wpml_get_default_language() ] : '';
			$form_id      = isset( $options[ wpml_get_current_language() ] ) ? $options[ wpml_get_current_language() ] : $default_form;

		} else {
			$option_name = 'ywctm_inquiry_' . str_replace( '-', '_', $form_type ) . '_id';
			$form_id     = apply_filters( 'ywctm_get_vendor_option', get_option( $option_name, '' ), $post_id, $option_name );
		}

		return $form_id;

	}
}

/**
 * GEOLOCATION RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywctm_get_ip_address' ) ) {

	/**
	 * Get user IP address
	 *
	 * @return  string
	 * @since   1.3.4
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_ip_address() {

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip_addr = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip_addr = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip_addr = $_SERVER['REMOTE_ADDR'];
		}

		if ( false === $ip_addr ) {
			$ip_addr = '0.0.0.0';

			return $ip_addr;
		}

		if ( strpos( $ip_addr, ',' ) !== false ) {
			$x       = explode( ',', $ip_addr );
			$ip_addr = trim( end( $x ) );
		}

		if ( ! ywctm_validate_ip( $ip_addr ) ) {
			$ip_addr = '0.0.0.0';
		}

		return $ip_addr;

	}
}

if ( ! function_exists( 'ywctm_validate_ip' ) ) {

	/**
	 * Validate IP Address
	 *
	 * @param   $ip    string
	 * @param   $which string (ipv4 or ipv6)
	 *
	 * @return  boolean
	 * @since   1.3.4
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_validate_ip( $ip, $which = '' ) {

		$which = strtolower( $which );

		// First check if filter_var is available
		if ( is_callable( 'filter_var' ) ) {
			switch ( $which ) {
				case 'ipv4':
					$flag = FILTER_FLAG_IPV4;
					break;

				case 'ipv6':
					$flag = FILTER_FLAG_IPV6;
					break;

				default:
					$flag = '';
					break;
			}

			return (bool) filter_var( $ip, FILTER_VALIDATE_IP, $flag );
		}

		if ( 'ipv6' !== $which && 'ipv4' !== $which ) {
			if ( strpos( $ip, ':' ) !== false ) {
				$which = 'ipv6';
			} elseif ( strpos( $ip, '.' ) !== false ) {
				$which = 'ipv4';
			} else {
				return false;
			}
		}

		return call_user_func( 'validate_' . $which, $ip );
	}
}

if ( ! function_exists( 'ywctm_validate_ipv4' ) ) {

	/**
	 * Validate IPv4 Address
	 *
	 * @param   $ip string
	 *
	 * @return  boolean
	 * @since   1.3.4
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_validate_ipv4( $ip ) {

		$ip_segments = explode( '.', $ip );

		// Always 4 segments needed
		if ( count( $ip_segments ) !== 4 ) {
			return false;
		}
		// IP can not start with 0
		if ( '0' === $ip_segments[0][0] ) {
			return false;
		}

		// Check each segment
		foreach ( $ip_segments as $segment ) {
			// IP segments must be digits and can not be longer than 3 digits or greater then 255
			if ( '' === $segment || preg_match( '/[^0-9]/', $segment ) || $segment > 255 || strlen( $segment ) > 3 ) {
				return false;
			}
		}

		return true;
	}
}

if ( ! function_exists( 'ywctm_validate_ipv6' ) ) {

	/**
	 * Validate IPv6 Address
	 *
	 * @param   $str string
	 *
	 * @return  boolean
	 * @since   1.3.4
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_validate_ipv6( $str ) {

		// 8 groups, separated by : 0-ffff per group one set of consecutive 0 groups can be collapsed to ::
		$groups    = 8;
		$collapsed = false;
		$chunks    = array_filter( preg_split( '/(:{1,2})/', $str, null, PREG_SPLIT_DELIM_CAPTURE ) );

		// Rule out easy nonsense
		if ( current( $chunks ) === ':' || end( $chunks ) === ':' ) {
			return false;
		}

		// PHP supports IPv4-mapped IPv6 addresses, so we'll expect those as well
		if ( strpos( end( $chunks ), '.' ) !== false ) {
			$ipv4 = array_pop( $chunks );
			if ( ! ywctm_validate_ipv4( $ipv4 ) ) {
				return false;
			}
			$groups --;
		}

		$seg = array_pop( $chunks );
		while ( $seg ) {
			if ( ':' === $seg[0] ) {
				if ( 0 === -- $groups ) {
					return false; // too many groups
				}
				if ( strlen( $seg ) > 2 ) {
					return false; // long separator
				}
				if ( '::' === $seg ) {
					if ( $collapsed ) {
						return false; // multiple collapsed
					}
					$collapsed = true;
				}
			} elseif ( preg_match( '/[^0-9a-f]/i', $seg ) || strlen( $seg ) > 4 ) {
				return false; // invalid segment
			}
		}

		return $collapsed || 1 === $groups;
	}
}
