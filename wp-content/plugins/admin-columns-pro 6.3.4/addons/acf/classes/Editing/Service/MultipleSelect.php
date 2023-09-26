<?php

namespace ACA\ACF\Editing\Service;

use ACP\Editing;
use ACP\Editing\Service;
use ACP\Editing\Storage;
use ACP\Editing\View;
use InvalidArgumentException;

class MultipleSelect extends Service\Basic {

	public function __construct( Editing\View\AdvancedSelect $view, Storage $storage ) {
		parent::__construct( $view, $storage );
	}

	public function get_view( string $context ): ?View {
		$view = parent::get_view( $context );

		if ( $view instanceof Editing\View\AdvancedSelect && $context === self::CONTEXT_BULK ) {
			$view->has_methods( true );
		}

		$view->set_multiple( true );

		return $view;
	}

	private function get_current_values( $id ): array {
		$values = $this->get_value( $id );

		return $values && is_array( $values )
			? $values
			: [];
	}

	public function update( int $id, $data ): void {
		$method = $data['method'] ?? null;

		if ( null === $method ) {
			$this->storage->update( $id, $data ?: false );

			return;
		}

		$values = $data['value'] ?? [];

		if ( ! is_array( $values ) ) {
			throw new InvalidArgumentException( 'Invalid value' );
		}

		switch ( $method ) {
			case 'add':
				if ( $values ) {
					$this->storage->update( $id, array_unique( array_merge( $this->get_current_values( $id ), $values ) ) );
				}
				break;
			case 'remove':
				$current = $this->get_current_values( $id );

				if ( $current && $values ) {
					$this->storage->update( $id, array_diff( $current, $values ) );
				}
				break;
			default:
				$this->storage->update( $id, $values );
		}
	}

}