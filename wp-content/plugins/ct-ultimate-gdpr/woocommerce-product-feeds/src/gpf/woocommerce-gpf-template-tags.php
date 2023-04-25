<?php

class WoocommerceGpfTemplateTags {

	/**
	 * Show the value of a Google Product Feed data value.
	 *
	 * @param  string  $element   The element to show, e.g. 'gtin', 'mpn'
	 * @param  WP_Post $the_post  The post to get the value from. Leave blank to use the global $post object
	 */
	public static function show_element( $element, $common, $template, WP_Post $the_post = null ) {
		$values = self::get_element_values( $element, $common, $the_post );
		if ( empty( $values ) ) {
			return;
		}
		$template->output_template_with_variables(
			'frontend',
			'gpf-element',
			array(
				'values' => implode( ', ', array_map( 'esc_html', $values ) ),
			)
		);
	}

	/**
	 * Show the value of a Google Product Feed data value with label.
	 *
	 * @param  string  $element   The element to show, e.g. 'gtin', 'mpn'
	 * @param  WP_Post $the_post  The post to get the value from. Leave blank to use the global $post object
	 */
	public static function show_element_with_label( $element, $common, $template, WP_Post $the_post = null ) {
		// Grab the value.
		$values = self::get_element_values( $element, $common, $the_post );
		if ( empty( $values ) ) {
			return;
		}
		// Grab the label text.
		if ( ! empty( $common->product_fields[ $element ]['desc'] ) ) {
			$label = $common->product_fields[ $element ]['desc'];
		} else {
			$label = ucfirst( $element );
		}
		$template->output_template_with_variables(
			'frontend',
			'gpf-element-with-label',
			array(
				'label'  => esc_html( $label ),
				'values' => implode( ', ', array_map( 'esc_html', $values ) ),
			)
		);
	}

	/**
	 * Retrieve the value of a Google Product Feed data value.
	 *
	 * @param  string  $element   The element to retrieve, e.g. 'gtin', 'mpn'
	 * @param  WP_Post $the_post  The post to get the value from. Leave blank to use the global $post object
	 *
	 * @return array              Array of the values for the element on the requested post.
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function get_element_values( $element, $common, WP_Post $the_post = null ) {
		global $post;
		// Use the passed in post if set, otherwise use the global $post object.
		if ( is_null( $the_post ) ) {
			$the_post = $post;
		}
		$feed_item = woocommerce_gpf_get_feed_item( $the_post );
		if ( ! empty( $feed_item->additional_elements[ $element ] ) ) {
			return $feed_item->additional_elements[ $element ];
		}
		return null;
	}
}
