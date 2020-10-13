<?php

namespace WPML\LIB\WP;

use WPML\FP\Curryable;
use WPML\FP\Fns;
use WPML\FP\Logic;
use WPML\FP\Str;

/**
 * @method static callable|bool hasBlock( ...$string ) - Curried :: string → bool
 * @method static callable|bool doesNotHaveBlock( ...$string ) - Curried :: string → bool
 */
class Gutenberg {
	use Curryable;

	const GUTENBERG_OPENING_START = '<!-- wp:';

	public static function init() {
		self::curryN( 'hasBlock', 1, Str::includes( self::GUTENBERG_OPENING_START ) );
		self::curryN( 'doesNotHaveBlock', 1, Logic::complement( self::hasBlock() ) );
	}

}

Gutenberg::init();
