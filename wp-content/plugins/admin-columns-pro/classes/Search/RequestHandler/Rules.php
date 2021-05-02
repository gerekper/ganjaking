<?php

namespace ACP\Search\RequestHandler;

use AC;
use ACP\Search\QueryFactory;
use ACP\Search\Searchable;
use ACP\Search\Value;
use LogicException;

/**
 * Handles rules request. Converts the request to a QueryBinding and registers it with WordPress.
 */
class Rules {

	/**
	 * @var AC\ListScreen
	 */
	private $list_screen;

	public function __construct( AC\ListScreen $list_screen ) {
		$this->list_screen = $list_screen;
	}

	public function handle( AC\Request $request ) {
		$rules = $request->filter( 'ac-rules', [], FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( ! $rules ) {
			return;
		}

		$bindings = [];

		foreach ( $rules as $rule ) {
			$column = $this->list_screen->get_column_by_name( $rule['name'] );

			if ( ! $column ) {
				continue;
			}

			if ( ! $column instanceof Searchable || ! $column->search() ) {
				continue;
			}

			// Skip unsupported operators
			if ( false === $column->search()->get_operators()->search( $rule['operator'] ) ) {
				continue;
			}

			try {
				$bindings[] = $column->search()->get_query_bindings(
					$rule['operator'],
					new Value( $rule['value'], $rule['value_type'] )
				);
			} catch ( LogicException $e ) {

				// Error message
				$message = sprintf( __( 'Smart filter for %s could not be applied.', 'codepress-admin-columns' ), sprintf( '<strong>%s</strong>', $column->get_custom_label() ) );
				$message = sprintf( '%s %s', $message, __( 'Try to re-apply the filter.', 'codepress-admin-columns' ) );

				( new AC\Message\Notice( $message ) )
					->set_type( AC\Message\Notice::WARNING )
					->register();

				continue;
			}
		}

		QueryFactory::create(
			$this->list_screen->get_meta_type(),
			$bindings
		)->register();
	}

}