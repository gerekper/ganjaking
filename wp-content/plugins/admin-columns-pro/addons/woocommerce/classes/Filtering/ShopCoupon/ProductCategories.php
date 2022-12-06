<?php

namespace ACA\WC\Filtering\ShopCoupon;

use ACA\WC\Column;
use ACP;

/**
 * @property Column\CouponProductCategories $column
 */
class ProductCategories extends ACP\Filtering\Model\Meta {

	public function __construct( Column\CouponProductCategories $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_vars( $vars ) {
		if ( in_array( $this->get_filter_value(), [ 'cpac_empty', 'cpac_nonempty' ] ) ) {
			return $this->get_filtering_vars_empty_nonempty( $vars );
		}

		return $this->get_filtering_vars_serialized( $vars, (int) $this->get_filter_value() );
	}

	protected function get_filtering_vars_empty_nonempty( $vars ) {
		$empty = [
			'key'     => $this->column->get_meta_key(),
			'value'   => serialize( [] ),

			// Non empty
			'compare' => '!=',
		];

		if ( 'cpac_empty' === $this->get_filter_value() ) {

			// Empty
			$empty['compare'] = '=';
		}

		$vars['meta_query'][] = $empty;

		return $vars;
	}

	public function get_filtering_data() {
		$options = [];

		foreach ( $this->get_meta_values_unserialized() as $key => $term_id ) {
			$term = get_term( $term_id, $this->column->get_taxonomy() );

			if ( ! $term || is_wp_error( $term ) ) {
				continue;
			}

			$options[ $term_id ] = $term->name;
		}

		return [
			'options'      => $options,
			'empty_option' => true,
		];
	}

}