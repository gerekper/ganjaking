<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\Taxonomy;

use AC\Helper\Select;
use WP_Term;

class Options extends Select\Options {

	/**
	 * @var WP_Term[]
	 */
	private $terms = [];

	/**
	 * @var array
	 */
	private $labels = [];

	private $formatter;

	public function __construct( array $terms, LabelFormatter $formatter ) {
		$this->formatter = $formatter;
		array_map( [ $this, 'set_term' ], $terms );
		$this->rename_duplicates();

		parent::__construct( $this->get_options() );
	}

	private function set_term( WP_Term $term ): void {
		$this->terms[ $term->term_id ] = $term;
		$this->labels[ $term->term_id ] = $this->formatter->format_label( $term );
	}

	public function get_term( int $id ): WP_Term {
		return $this->terms[ $id ];
	}

	private function get_options(): array {
		return self::create_from_array( $this->labels )->get_copy();
	}

	protected function rename_duplicates(): void {
		$duplicates = array_diff_assoc( $this->labels, array_unique( $this->labels ) );

		foreach ( $this->labels as $id => $label ) {
			if ( in_array( $label, $duplicates, true ) ) {
				$this->labels[ $id ] = $this->formatter->format_label_unique( $this->get_term( $id ) );
			}
		}
	}

}