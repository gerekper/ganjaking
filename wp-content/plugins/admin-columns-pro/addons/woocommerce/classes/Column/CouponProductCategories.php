<?php

namespace ACA\WC\Column;

use AC\Column;
use ACP;

/**
 * @since 3.0
 */
abstract class CouponProductCategories extends Column\Meta
	implements ACP\Export\Exportable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_group( 'woocommerce' );
	}

	public function get_taxonomy() {
		return 'product_cat';
	}

	public function get_value( $id ) {
		$taxonomy_ids = $this->get_raw_value( $id );

		if ( ! $taxonomy_ids ) {
			return $this->get_empty_char();
		}

		$terms = [];
		foreach ( $taxonomy_ids as $term_id ) {
			$term = get_term( $term_id, $this->get_taxonomy() );

			if ( ! $term ) {
				continue;
			}

			$terms[] = $term->name;
		}

		return implode( ', ', $terms );
	}

	public function export() {
		return new ACP\Export\Model\Value( $this );
	}

}