<?php
declare( strict_types=1 );

namespace ACA\MLA\Export\Model;

use ACP;

class Taxonomy implements ACP\Export\Service {

	/**
	 * @var string
	 */
	private $taxonomy;

	public function __construct( string $taxonomy ) {
		$this->taxonomy = $taxonomy;
	}

	public function get_value( $id ) {
		$terms = wp_get_post_terms( $id, $this->taxonomy, [ 'fields' => 'names' ] );

		if ( ! $terms || is_wp_error( $terms ) ) {
			return '';
		}

		return implode( ', ', $terms );
	}

}