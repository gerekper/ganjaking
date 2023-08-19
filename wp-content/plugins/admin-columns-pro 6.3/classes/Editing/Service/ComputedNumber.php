<?php

namespace ACP\Editing\Service;

use ACP\Editing;
use ACP\Editing\Storage;
use ACP\Editing\View;
use RuntimeException;

class ComputedNumber implements Editing\Service {

	const ARG_COMPUTATION_TYPE = 'computation_type';
	const ARG_ALLOW_NEGATIVE = 'allow_negative';

	protected $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function get_value( int $id ) {
		return $this->storage->get( $id );
	}

	public function get_view( string $context ): ?View {
		return $context === Editing\Service::CONTEXT_BULK
			? new View\ComputedNumber()
			: new View\Number();
	}

	private function compute( float $current_value, float $compute_value, string $computation = null ) {
		switch ( $computation ) {
			case 'add':
				return $current_value + $compute_value;
			case 'subtract':
				return $current_value - $compute_value;
			case 'multiply':
				return $current_value * $compute_value;
			case 'divide':
				if ( 0 == $compute_value ) {
					throw new RuntimeException( __( 'Cannot divide by zero', 'codepress-admin-columns' ) );
				}

				return $current_value / $compute_value;
			case 'replace':
			default:
				return $compute_value;
		}
	}

	public function update( int $id, $data ): void {
		$computation = $data[ self::ARG_COMPUTATION_TYPE ] ?? null;

		if ( null === $computation ) {
			$this->storage->update( $id, $data ?: null );

			return;
		}

		$value = $this->compute(
			(float) $this->get_value( $id ),
			(float) $data['value'],
			$computation
		);

		$allow_negative = $data[ self::ARG_ALLOW_NEGATIVE ] ?? null;

		if ( ! $allow_negative && $value < 0 ) {
			$value = 0;
		}

		$this->storage->update( $id, $value );
	}

}