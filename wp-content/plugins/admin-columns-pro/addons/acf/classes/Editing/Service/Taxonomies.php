<?php

namespace ACA\ACF\Editing\Service;

use ACP\Editing\View;
use InvalidArgumentException;
use LogicException;

class Taxonomies extends Taxonomy {

	public function get_view( string $context ): ?View {
		$view = parent::get_view( $context );

		if ( ! $view instanceof View\AjaxSelect ) {
			throw new LogicException( 'Invalid view' );
		}

		if ( $context === self::CONTEXT_BULK ) {
			$view->has_methods( true );
		}

		return $view->set_multiple( true );
	}

	public function update( int $id, $data ): void {
		$method = $data['method'] ?? null;

		if ( null === $method ) {
			$this->storage->update( $id, is_array( $data ) ? $this->sanitize_term_ids( $data ) : null );

			return;
		}

		$term_ids = $data['value'] ?? [];

		if ( ! is_array( $term_ids ) ) {
			throw new InvalidArgumentException( 'Invalid value' );
		}

		$term_ids = $this->sanitize_term_ids( $term_ids );

		switch ( $method ) {
			case 'add':
				$this->add_term_ids( $id, $term_ids );

				break;
			case 'remove':
				$this->remove_term_ids( $id, $term_ids );

				break;
			default:
				$this->storage->update( $id, $term_ids );
		}
	}

	private function add_term_ids( $id, array $add_term_ids ) {
		if ( ! $add_term_ids ) {
			return;
		}

		$this->storage->update( $id, array_merge( $this->get_current_term_ids( $id ), $add_term_ids ) );
	}

	private function remove_term_ids( $id, array $remove_term_ids ) {
		$term_ids = $this->get_current_term_ids( $id );

		if ( ! $term_ids || ! $remove_term_ids ) {
			return;
		}

		$this->storage->update( $id, array_unique( array_diff( $term_ids, $remove_term_ids ) ) );
	}

	protected function sanitize_term_ids( array $term_ids ) {
		// Cast term id to `string`
		return array_map( 'strval', array_unique( array_filter( $term_ids ) ) );
	}

	private function get_current_term_ids( $id ): array {
		return $this->storage->get( $id ) ?: [];
	}

}