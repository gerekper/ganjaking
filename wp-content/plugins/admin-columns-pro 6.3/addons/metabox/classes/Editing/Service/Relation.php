<?php

namespace ACA\MetaBox\Editing\Service;

use ACA;
use ACA\MetaBox\Entity;
use ACP;
use ACP\Editing\View;
use InvalidArgumentException;

abstract class Relation implements ACP\Editing\PaginatedOptions, ACP\Editing\Service {

	/**
	 * @var Entity\Relation
	 */
	protected $relation;

	public function __construct( Entity\Relation $relation ) {
		$this->relation = $relation;
	}

	public function get_view( string $context ): ?View {
		$view = new ACP\Editing\View\AjaxSelect();

		$view->set_multiple( true )
		     ->set_clear_button( true );

		if ( 'bulk' === $context ) {
			$view->has_methods( true );
		}

		return $view;
	}

	public function update( int $id, $data ): void {
		$method = $data['method'] ?? null;

		if ( ! $method ) {
			$relation_ids = $data && is_array( $data )
				? $data
				: [];

			$this->replace( $id, $relation_ids );

			return;
		}

		$relation_ids = $data['value'] ?? [];

		if ( ! is_array( $relation_ids ) ) {
			throw new InvalidArgumentException( 'Invalid value' );
		}

		switch ( $method ) {
			case 'add':
				foreach ( $relation_ids as $relation_id ) {
					$this->relation->add_relation( $id, $relation_id );
				}
				break;
			case 'remove':
				foreach ( $relation_ids as $relation_id ) {
					$this->relation->delete_relation( $id, $relation_id );
				}

				break;
			default:
				$this->replace( $id, $relation_ids );
		}
	}

	protected function replace( $id, array $relation_ids ) {
		foreach ( $this->relation->get_related_ids( $id ) as $related_id ) {
			$this->relation->delete_relation( $id, $related_id );
		}

		foreach ( $relation_ids as $relation_id ) {
			$this->relation->add_relation( $id, $relation_id );
		}
	}

	public function get_value( $id ) {
		$ids = array_filter( $this->relation->get_related_ids( $id ) );

		return array_combine( $ids, $ids );
	}

}