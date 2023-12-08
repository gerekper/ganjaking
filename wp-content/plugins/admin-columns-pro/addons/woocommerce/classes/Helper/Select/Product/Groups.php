<?php
declare( strict_types=1 );

namespace ACA\WC\Helper\Select\Product;

use AC\Helper\Select;
use AC\Helper\Select\OptionGroup;

/**
 * Decorator for Select\Post\Options
 */
class Groups extends Select\Options {

	public function __construct( Options $options, GroupFormatter $formatter ) {
		parent::__construct( $this->create_groups( $options, $formatter ) );
	}

	private function create_groups( Options $options, GroupFormatter $formatter ): array {
		$groups = [];

		foreach ( $options as $option ) {
			$product = $options->get_product( $option->get_value() );

			$groups[ $formatter->format( $product ) ][] = $option;
		}

		$option_groups = [];

		foreach ( $this->sort( $groups ) as $label => $_options ) {
			$option_groups[] = new OptionGroup( $label, $_options );
		}

		return $option_groups;
	}

	protected function sort( array $groups ): array {
		// sort natural by key
		uksort( $groups, 'strnatcmp' );

		return $groups;
	}

}