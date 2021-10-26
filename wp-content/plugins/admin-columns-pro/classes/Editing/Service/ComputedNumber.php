<?php

namespace ACP\Editing\Service;

use AC\Request;
use ACP;
use ACP\Editing;
use ACP\Editing\Storage;
use ACP\Editing\View;

class ComputedNumber implements Editing\Service {

	const ARG_VALUE = 'value';
	const ARG_COMPUTATION_TYPE = 'computation_type';
	const ARG_ALLOW_NEGATIVE = 'allow_negative';

	const COMPUTATION_ADD = 'add';
	const COMPUTATION_SUBTRACT = 'subtract';
	const COMPUTATION_MULTIPLY = 'multiply';
	const COMPUTATION_DIVIDE = 'divide';

	/**
	 * @var Storage
	 */
	protected $storage;

	public function __construct( Storage $storage ) {
		$this->storage = $storage;
	}

	public function get_view( $context ) {
		return $context === self::CONTEXT_BULK
			? new View\ComputedNumber()
			: new View\Number();
	}

	public function get_value( $id ) {
		return $this->storage->get( $id );
	}

	public function update( Request $request ) {
		$id = $request->get( 'id' );
		$args = $request->get( 'value' );

		if ( ! isset( $args[ self::ARG_COMPUTATION_TYPE ] ) ) {
			$args = [
				self::ARG_COMPUTATION_TYPE => null,
				self::ARG_VALUE            => $args,
				self::ARG_ALLOW_NEGATIVE   => null,
			];
		}

		$input_value = $args[ self::ARG_VALUE ];
		$compute_value = (float) $input_value;
		$stored_value = (float) $this->get_value( $id );

		switch ( $args[ self::ARG_COMPUTATION_TYPE ] ) {
			case self::COMPUTATION_ADD:
				$value = $stored_value + $compute_value;
				break;
			case self::COMPUTATION_SUBTRACT:
				$value = $stored_value - $compute_value;
				break;
			case self::COMPUTATION_MULTIPLY:
				$value = $stored_value * $compute_value;
				break;
			case self::COMPUTATION_DIVIDE:
				$value = 0 === $compute_value
					? $stored_value
					: $stored_value / $compute_value;
				break;
			default:
				$value = '' === $input_value
					? $input_value
					: $compute_value;
		}

		if ( isset( $args[ self::ARG_ALLOW_NEGATIVE ] ) && ! $args[ self::ARG_ALLOW_NEGATIVE ] && $value < 0 && $input_value !== '' ) {
			$value = 0;
		}

		return $this->storage->update( $id, $value );
	}

}