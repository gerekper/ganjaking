<?php

namespace WPML\FP;

use Exception;
use WPML\Collect\Support\Traits\Macroable;
use WPML\FP\Functor\Functor;
use WPML\FP\Functor\Pointed;

/**
 * Class Either
 * @package WPML\FP
 *
 * @method static callable|Right of( ...$value ) - Curried :: a → Right a
 *
 * @method static callable|Left left( ...$value ) - Curried :: a → Left a
 *
 * @method static callable|Right right( ...$value ) - Curried :: a → Right a
 *
 * @method static callable|Either fromNullable( ...$value ) - Curried :: a → Either a
 */
abstract class Either {
	use Functor;
	use Macroable;

	public static function init() {
		self::macro( 'of', Right::of() );

		self::macro( 'left', Left::of() );

		self::macro( 'right', Right::of() );

		self::macro( 'fromNullable', curryN( 1, function ( $value ) {
			return is_null( $value ) ? self::left( $value ) : self::right( $value );
		} ) );
	}

	public function join() {
		if( ! $this->value instanceof Either ) {
			return $this;
		}
		return $this->value->join();
	}

	abstract public function chain( callable $fn );
	abstract public function orElse( callable $fn );
}

class Left extends Either {

	use ConstApplicative;
	use Pointed;

	public function map( callable $fn ) {
		return $this; // noop
	}

	public function get() {
		throw new Exception( "Can't extract the value of Left" );
	}

	/**
	 * @param mixed $other
	 *
	 * @return mixed
	 */
	public function getOrElse( $other ) {
		return $other;
	}

	public function orElse( callable $fn ) {
		return Either::right( $fn( $this->value ) );
	}

	public function chain( callable $fn ) {
		return $this;
	}

	public function getOrElseThrow( $value ) {
		throw new Exception( $value );
	}

	public function filter( $fn ) {
		return $this;
	}

	public function tryCatch( callable $fn ) {
		return $this; // noop
	}

}

class Right extends Either {

	use Applicative;
	use Pointed;

	public function map( callable $fn ) {
		return Either::of( $fn( $this->value ) );
	}

	public function getOrElse( $other ) {
		return $this->value;
	}

	public function orElse( callable $fn ) {
		return $this;
	}

	public function getOrElseThrow( $value ) {
		return $this->value;
	}

	public function chain( callable $fn ) {
		return $this->map($fn)->join();
	}

	public function filter( callable $fn ) {
		return Either::fromNullable( $fn( $this->value ) ? $this->value : null );
	}

	public function tryCatch( callable $fn ) {
		return tryCatch( function() use ( $fn ) { return $fn( $this->value ); } );
	}

}

Either::init();