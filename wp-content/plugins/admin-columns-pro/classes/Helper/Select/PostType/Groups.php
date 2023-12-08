<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\PostType;

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
			$post = $options->get_post_type( $option->get_value() );

			$groups[ $formatter->format( $post ) ][] = $option;
		}

		$option_groups = [];

		foreach ( $groups as $label => $_options ) {
			$option_groups[] = new OptionGroup( $label, $_options );
		}

		return $option_groups;
	}

}