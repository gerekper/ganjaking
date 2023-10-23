<?php
/**
 * Plugin functions
 *
 * @package YITH\MinimumMaximumQuantity
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'ywmmq_get_rules_fields' ) ) {

	/**
	 * Get the rules fields for Product, Category & Tag page
	 *
	 * @param array  $item      Product, Category or Tag rule data.
	 * @param string $item_type Product, Category or Tag item slug.
	 *
	 * @return  array
	 * @since   1.5.4
	 */
	function ywmmq_get_rules_fields( $item, $item_type = 'product' ) {
		$desc = '';
		switch ( $item_type ) {
			case 'category':
				$desc = esc_html__( 'Do not apply global restrictions to the products of this category.', 'yith-woocommerce-minimum-maximum-quantity' );
				break;
			case 'tag':
				$desc = esc_html__( 'Do not apply global restrictions to the products of this tag.', 'yith-woocommerce-minimum-maximum-quantity' );
				break;
			case 'product':
				$desc = esc_html__( 'Do not apply global restrictions to this product.', 'yith-woocommerce-minimum-maximum-quantity' );
				break;
			default:
				$desc .= '<span class="category">' . esc_html__( 'Do not apply global restrictions to the products of this category.', 'yith-woocommerce-minimum-maximum-quantity' ) . '</span>';
				$desc .= '<span class="tag">' . esc_html__( 'Do not apply global restrictions to the products of this tag.', 'yith-woocommerce-minimum-maximum-quantity' ) . '</span>';
				$desc .= '<span class="product">' . esc_html__( 'Do not apply global restrictions to this product.', 'yith-woocommerce-minimum-maximum-quantity' ) . '</span>';
		}

		return array(
			array(
				'id'    => '_exclusion',
				'name'  => '_exclusion',
				'type'  => 'onoff',
				'title' => esc_html__( 'Exclude', 'yith-woocommerce-minimum-maximum-quantity' ),
				'value' => $item['_exclusion'],
				'desc'  => $desc,
			),
			array(
				'id'    => '_quantity_limit_override',
				'name'  => '_quantity_limit_override',
				'type'  => 'onoff',
				'title' => esc_html__( 'Override quantity restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				'value' => $item['_quantity_limit_override'],
				'desc'  => esc_html__( 'Global restrictions will be overridden by current ones. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			),
			array(
				'id'      => '_minimum_quantity',
				'name'    => '_minimum_quantity',
				'type'    => 'number',
				'default' => '',
				'title'   => esc_html__( 'Minimum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'value'   => isset( $item['_minimum_quantity'] ) ? $item['_minimum_quantity'] : 0,
				'desc'    => '',
			),
			array(
				'id'      => '_maximum_quantity',
				'name'    => '_maximum_quantity',
				'type'    => 'number',
				'default' => '',
				'title'   => esc_html__( 'Maximum quantity restriction', 'yith-woocommerce-minimum-maximum-quantity' ),
				'value'   => isset( $item['_maximum_quantity'] ) ? $item['_maximum_quantity'] : 0,
				'desc'    => '',
			),
			array(
				'id'      => '_step_quantity',
				'name'    => '_step_quantity',
				'type'    => 'number',
				'default' => '',
				'title'   => esc_html__( 'Quantity groups of', 'yith-woocommerce-minimum-maximum-quantity' ),
				'value'   => isset( $item['_step_quantity'] ) ? $item['_step_quantity'] : 0,
				'desc'    => '',
			),
			array(
				'id'    => '_value_limit_override',
				'name'  => '_value_limit_override',
				'type'  => 'onoff',
				'title' => esc_html__( 'Override spend restrictions', 'yith-woocommerce-minimum-maximum-quantity' ),
				'value' => $item['_value_limit_override'],
				'desc'  => esc_html__( 'Global spend restrictions will be overridden by current ones. Set zero for no restrictions.', 'yith-woocommerce-minimum-maximum-quantity' ),
			),
			array(
				'id'      => '_minimum_value',
				'name'    => '_minimum_value',
				'type'    => 'number',
				'default' => '',
				'title'   => esc_html__( 'Minimum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'value'   => isset( $item['_minimum_value'] ) ? $item['_minimum_value'] : 0,
				'desc'    => '',
			),
			array(
				'id'      => '_maximum_value',
				'name'    => '_maximum_value',
				'type'    => 'number',
				'default' => '',
				'title'   => esc_html__( 'Maximum spend restriction', 'yith-woocommerce-minimum-maximum-quantity' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'value'   => isset( $item['_maximum_value'] ) ? $item['_maximum_value'] : 0,
				'desc'    => '',
			),
		);
	}
}

if ( ! function_exists( 'ywmmq_is_wcpb_active' ) ) {
	/**
	 * Check if YITH WooCommerce Product Bundles is active
	 *
	 * @return  bool
	 * @since   1.1.5
	 */
	function ywmmq_is_wcpb_active() {
		return defined( 'YITH_WCPB' ) && YITH_WCPB;
	}
}

if ( ! function_exists( 'ywmmq_is_ywgc_active' ) ) {

	/**
	 * Check if YITH WooCommerce Gift cards is active
	 *
	 * @return  bool
	 * @since   1.3.0
	 */
	function ywmmq_is_ywgc_active() {
		return function_exists( 'YITH_YWGC' ) && defined( 'YITH_YWGC_PREMIUM' ) && YITH_YWGC_PREMIUM;
	}
}

if ( ! function_exists( 'ywmmq_is_wraq_active' ) ) {

	/**
	 * Check if YITH WooCommerce Request a quote is active
	 *
	 * @return  bool
	 * @since   1.3.3
	 */
	function ywmmq_is_wraq_active() {
		return defined( 'YITH_YWRAQ_PREMIUM' ) && YITH_YWRAQ_PREMIUM;
	}
}

if ( ! function_exists( 'ywmmq_get_elementor_item_for_page' ) ) {

	/**
	 * Check if a page has a determined Elementor widget
	 *
	 * @param string  $item_id      The widget identifier (set with get_name() function in widget class).
	 * @param integer $post_id      The Post ID where we can check if the widget is used.
	 * @param boolean $get_settings Choose to get the widget settings.
	 *
	 * @return bool|array
	 * @since   1.6.6
	 */
	function ywmmq_get_elementor_item_for_page( $item_id, $post_id, $get_settings = false ) {

		// Check if Elementor is enabled.
		if ( defined( 'ELEMENTOR_VERSION' ) && 0 !== $post_id ) {

			// Check if page is built with Elementor.
			if ( \Elementor\Plugin::$instance->documents->get( $post_id )->is_built_with_elementor() ) {
				if ( ! $get_settings ) {

					// If i only want to check if the Elementor widget is used on the page.
					$meta = get_post_meta( $post_id, '_elementor_controls_usage', true );

					if ( is_array( $meta ) && array_key_exists( $item_id, $meta ) ) {
						return true;
					}
				} else {

					// If i want to get the Elementor widget settings.
					$meta = get_post_meta( $post_id, '_elementor_data', true );
					if ( is_string( $meta ) && ! empty( $meta ) ) {
						$meta = json_decode( $meta, true );
					}

					if ( ! empty( $meta ) ) {

						$item_settings = false;

						\Elementor\Plugin::$instance->db->iterate_data(
							$meta,
							function ( $element ) use ( $item_id, &$item_settings ) {
								if ( ! empty( $element['widgetType'] ) && $item_id === $element['widgetType'] ) {
									$item_settings = $element['settings'];
								}

								return $element;
							}
						);

						return $item_settings;
					}
				}
			}
		}

		return false;
	}
}

/**
 * Get the position and show YWMMQ rules in product page in block template
 *
 * @return void
 * @since  1.32.0
 */
function ywmmq_show_rules_blocks() {

	if ( get_option( 'ywmmq_rules_enable' ) !== 'no' ) {

		if ( apply_filters( 'ywmmq_exclude_role_from_rules', false ) ) {
			return;
		}

		$position = get_option( 'ywmmq_rules_position' );

		switch ( $position ) {

			case '1':
				$hook     = 'render_block_woocommerce/product-price';
				$function = 'ywmmq_add_rules_text_after_block';
				break;

			case '2':
				$hook     = 'render_block_woocommerce/add-to-cart-form';
				$function = 'ywmmq_add_rules_text_before_block';
				break;

			case '3':
				$hook     = 'render_block_woocommerce/product-details';
				$function = 'ywmmq_add_rules_text_before_block';
				break;

			default:
				$hook     = 'render_block_core/post-title';
				$function = 'ywmmq_add_rules_text_before_block';

		}

		add_filter( $hook, $function );

	}

}

/**
 * Add rules text before block
 *
 * @param string $content Block content.
 *
 * @return string
 * @since  1.32.0
 */
function ywmmq_add_rules_text_before_block( $content ) {

	$product = wc_get_product();
	ob_start();
	YITH_WMMQ()->add_rules_text( $product->get_id() );
	$before = ob_get_clean();

	return $before . $content;
}

/**
 * Add rules text after block
 *
 * @param string $content Block content.
 *
 * @return string
 * @since  1.32.0
 */
function ywmmq_add_rules_text_after_block( $content ) {

	$product = wc_get_product();
	ob_start();
	YITH_WMMQ()->add_rules_text( $product->get_id() );
	$after = ob_get_clean();

	return $content . $after;
}
