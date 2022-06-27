<?php
/**
 * Extra Product Options Math class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 * phpcs:disable Generic.Files.OneObjectStructurePerFile
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Math class
 *
 * Supports basic math and built-in functions only (NO eval function).
 * Based on EvalMath by Miles Kaufman Copyright (C) 2005 Miles Kaufmann http://www.twmagic.com/
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
class THEMECOMPLETE_EPO_MATH {

	/**
	 * Pattern used for a valid function or variable name. Note, var and func names are case insensitive.
	 *
	 * @var string
	 */
	private static $namepat = '[a-z][a-z0-9_]*';

	/**
	 * Last error.
	 *
	 * @var string
	 */
	public static $last_error = null;

	/**
	 * Variables (and constants).
	 *
	 * @var array
	 */
	public static $v = [
		'e'  => 2.71,
		'pi' => 3.14,
	];

	/**
	 * User-defined functions.
	 *
	 * @var array
	 */
	public static $f = [
		'int' => [
			'args' => [ 'a' ],
			'func' => [
				'a',
				[
					'fn'       => 'floor(',
					'fnn'      => 'floor',
					'argcount' => 1,
				],
			],
		],
	];

	/**
	 * Constants.
	 *
	 * @var array
	 */
	public static $vb = [ 'e', 'pi' ];

	/**
	 * Built-in functions.
	 *
	 * @var array
	 */
	public static $fb = [
		'sin',
		'cos',
		'tan',
		'asin',
		'acos',
		'atan',
		'sinh',
		'cosh',
		'tanh',
		'asinh',
		'acosh',
		'atanh',
		'ln',
		'log',
		'round',
		'ceil',
		'floor',
		'abs',
		'exp',
		'sqrt',
	];

	/**
	 * Evaluate maths string.
	 *
	 * @param string $expr The expression to evaluate.
	 *
	 * @return mixed
	 */
	public static function evaluate( $expr ) {
		self::$last_error = null;
		$expr             = trim( $expr );
		if ( ';' === substr( $expr, - 1, 1 ) ) {
			$expr = substr( $expr, 0, strlen( $expr ) - 1 ); // strip semicolons at the end.
		}
		// ===============
		// is it a variable assignment?
		if ( preg_match( '/^\s*(' . self::$namepat . ')\s*=\s*(.+)$/', $expr, $matches ) ) {
			// make sure we're not assigning to a constant.
			if ( in_array( $matches[1], self::$vb ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
				return self::trigger( "cannot assign to constant '$matches[1]'" );
			}
			$tmp = self::pfx( self::nfx( $matches[2] ) );
			if ( false === $tmp ) {
				return false; // get the result and make sure it's good.
			}
			self::$v[ $matches[1] ] = $tmp; // if so, stick it in the variable array.

			return self::$v[ $matches[1] ]; // and return the resulting value.
			// ===============
			// is it a function assignment?
		} elseif ( preg_match( '/^\s*(' . self::$namepat . ')\s*\(\s*(' . self::$namepat . '(?:\s*,\s*' . self::$namepat . ')*)\s*\)\s*=\s*(.+)$/', $expr, $matches ) ) {
			$fnn = $matches[1]; // get the function name.
			// make sure it isn't built in.
			if ( in_array( $matches[1], self::$fb ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
				return self::trigger( "cannot redefine built-in function '$matches[1]()'" );
			}
			$args  = explode( ',', preg_replace( '/\s+/', '', $matches[2] ) ); // get the arguments.
			$stack = self::nfx( $matches[3] );
			if ( false === $stack ) {
				return false; // see if it can be converted to postfix.
			}
			$stack_size = count( $stack );
			for ( $i = 0; $i < $stack_size; $i ++ ) { // freeze the state of the non-argument variables.
				$token = $stack[ $i ];
				if ( preg_match( '/^' . self::$namepat . '$/', $token ) && ! in_array( $token, $args ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
					if ( array_key_exists( $token, self::$v ) ) {
						$stack[ $i ] = self::$v[ $token ];
					} else {
						return self::trigger( "undefined variable '$token' in function definition" );
					}
				}
			}
			self::$f[ $fnn ] = [
				'args' => $args,
				'func' => $stack,
			];

			return true;
			// ===============
		} else {
			return self::pfx( self::nfx( $expr ) ); // straight up evaluation, woo.
		}
	}

	/**
	 * Convert infix to postfix notation.
	 *
	 * @param string $expr The expression to evaluate.
	 *
	 * @return array|string
	 */
	private static function nfx( $expr ) {

		$index  = 0;
		$stack  = new THEMECOMPLETE_EPO_MATH_Stack();
		$output = []; // postfix form of expression, to be passed to pfx().
		$expr   = trim( $expr );

		$ops   = [ '+', '-', '*', '/', '^', '_' ];
		$ops_r = [
			'+' => 0,
			'-' => 0,
			'*' => 0,
			'/' => 0,
			'^' => 1,
		]; // right-associative operator?
		$ops_p = [
			'+' => 0,
			'-' => 0,
			'*' => 1,
			'/' => 1,
			'_' => 1,
			'^' => 2,
		]; // operator precedence.

		// we use this in syntax-checking the expression
		// and determining when a - is a negation.
		$expecting_op = false;
		if ( preg_match( '/[^\w\s+*^\/()\.,-]/', $expr, $matches ) ) { // make sure the characters are all good.
			return self::trigger( "illegal character '{$matches[0]}'" );
		}

		while ( 1 ) { // 1 Infinite Loop ;)
			$op = substr( $expr, $index, 1 ); // get the first character at the current index.

			// find out if we're currently at the beginning of a number/variable/function/parenthesis/operand .
			$ex = preg_match( '/^(' . self::$namepat . '\(?|\d+(?:\.\d*)?(?:(e[+-]?)\d*)?|\.\d+|\()/', substr( $expr, $index ), $match );
			if ( '-' === $op && ! $expecting_op ) { // is it a negation instead of a minus?
				$stack->push( '_' ); // put a negation on the stack.
				$index ++;
			} elseif ( '_' === $op ) { // we have to explicitly deny this, because it's legal on the stack.
				return self::trigger( "illegal character '_'" ); // but not in the input expression.
				// are we putting an operator on the stack?
			} elseif ( ( in_array( $op, $ops ) || $ex ) && $expecting_op ) { // phpcs:ignore WordPress.PHP.StrictInArray
				if ( $ex ) { // are we expecting an operator but have a number/variable/function/opening parenthesis?
					$op = '*';
					$index --; // it's an implicit multiplication.
				}
				// heart of the algorithm.
				while ( $stack->count > 0 && ( $o2 = $stack->last() ) && in_array( $o2, $ops ) && ( $ops_r[ $op ] ? $ops_p[ $op ] < $ops_p[ $o2 ] : $ops_p[ $op ] <= $ops_p[ $o2 ] ) ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition, WordPress.PHP.StrictInArray
					$output[] = $stack->pop(); // pop stuff off the stack into the output.
				}
				// many thanks: https://en.wikipedia.org/wiki/Reverse_Polish_notation#The_algorithm_in_detail .
				$stack->push( $op ); // finally put OUR operator onto the stack.
				$index ++;
				$expecting_op = false;
			} elseif ( ')' === $op && $expecting_op ) { // ready to close a parenthesis?
				// pop off the stack back to the last ( .
				while ( '(' !== ( $o2 = $stack->pop() ) ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition
					if ( is_null( $o2 ) ) {
						return self::trigger( "unexpected ')'" );
					} else {
						$output[] = $o2;
					}
				}
				if ( preg_match( '/^(' . self::$namepat . ')\($/', $stack->last( 2 ), $matches ) ) { // did we just close a function?
					$fnn       = $matches[1]; // get the function name.
					$arg_count = $stack->pop(); // see how many arguments there were (cleverly stored on the stack, thank you).
					$fn        = $stack->pop();
					$output[]  = [
						'fn'       => $fn,
						'fnn'      => $fnn,
						'argcount' => $arg_count,
					]; // send function to output.
					// check the argument count.
					if ( in_array( $fnn, self::$fb ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
						if ( $arg_count > 1 ) {
							$a           = new stdClass();
							$a->expected = 1;
							$a->given    = $arg_count;

							return self::trigger( "wrong number of arguments '$a'" );
						}
					} elseif ( array_key_exists( $fnn, self::$f ) ) {
						if ( count( self::$f[ $fnn ]['args'] ) != $arg_count ) { // phpcs:ignore WordPress.PHP.StrictComparisons
							$a           = new stdClass();
							$a->expected = count( self::$f[ $fnn ]['args'] );
							$a->given    = $arg_count;

							return self::trigger( "wrong number of arguments '$a'" );
						}
					} else { // did we somehow push a non-function on the stack? this should never happen.
						return self::trigger( 'internal error' );
					}
				}
				$index ++;
				// ===============
			} elseif ( ',' === $op && $expecting_op ) { // did we just finish a function argument?
				while ( '(' !== ( $o2 = $stack->pop() ) ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition
					if ( is_null( $o2 ) ) {
						return self::trigger( "unexpected ','" ); // oops, never had a ( .
					} else {
						$output[] = $o2; // pop the argument expression stuff and push onto the output.
					}
				}
				// make sure there was a function.
				if ( ! preg_match( '/^(' . self::$namepat . ')\($/', $stack->last( 2 ), $matches ) ) {
					return self::trigger( "unexpected ','" );
				}
				$stack->push( $stack->pop() + 1 ); // increment the argument count.
				$stack->push( '(' ); // put the ( back on, we'll need to pop back to it again.
				$index ++;
				$expecting_op = false;
				// ===============
			} elseif ( '(' === $op && ! $expecting_op ) {
				$stack->push( '(' ); // that was easy.
				$index ++;
				// ===============
			} elseif ( $ex && ! $expecting_op ) { // do we now have a function/variable/number?
				$expecting_op = true;
				$val          = $match[1];
				if ( preg_match( '/^(' . self::$namepat . ')\($/', $val, $matches ) ) { // may be func, or variable w/ implicit multiplication against parentheses...
					// it's a func.
					if ( in_array( $matches[1], self::$fb ) || array_key_exists( $matches[1], self::$f ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
						$stack->push( $val );
						$stack->push( 1 );
						$stack->push( '(' );
						$expecting_op = false;
					} else { // it's a var w/ implicit multiplication.
						$val      = $matches[1];
						$output[] = $val;
					}
				} else { // it's a plain old var or num.
					$output[] = $val;
				}
				$index += strlen( $val );
			} elseif ( ')' === $op ) { // miscellaneous error checking.
				return self::trigger( "unexpected ')'" );
			} elseif ( in_array( $op, $ops ) && ! $expecting_op ) { // phpcs:ignore WordPress.PHP.StrictInArray
				return self::trigger( "unexpected operator '$op'" );
			} else { // I don't even want to know what you did to get here.
				return self::trigger( 'an unexpected error occurred' );
			}
			if ( strlen( $expr ) == $index ) { // phpcs:ignore WordPress.PHP.StrictComparisons
				// did we end with an operator? bad.
				if ( in_array( $op, $ops ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
					return self::trigger( "operator '$op' lacks operand" );
				} else {
					break;
				}
			}
			// step the index past whitespace (pretty much turns whitespace
			// into implicit multiplication if no operator is there).
			while ( ' ' === substr( $expr, $index, 1 ) ) {
				$index ++;
			}
		}
		// pop everything off the stack and push onto output.
		while ( ! is_null( $op = $stack->pop() ) ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition
			if ( '(' === $op ) {
				return self::trigger( "expecting ')'" ); // if there are (s on the stack, ()s were unbalanced.
			}
			$output[] = $op;
		}

		return $output;
	}

	/**
	 * Evaluate postfix notation.
	 *
	 * @param mixed $tokens Array of tokens.
	 * @param array $vars Array of variables.
	 *
	 * @return mixed
	 */
	private static function pfx( $tokens, $vars = [] ) {
		if ( false == $tokens ) { // phpcs:ignore WordPress.PHP.StrictComparisons
			return false;
		}
		$stack = new THEMECOMPLETE_EPO_MATH_Stack();

		foreach ( $tokens as $token ) { // nice and easy.

			// if the token is a function, pop arguments off the stack, hand them to the function, and push the result back on.
			if ( is_array( $token ) ) { // it's a function!
				$fnn   = $token['fnn'];
				$count = $token['argcount'];
				// built-in function.
				if ( in_array( $fnn, self::$fb ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
					$op1 = $stack->pop();
					if ( is_null( $op1 ) ) {
						return self::trigger( 'internal error' );
					}
					$fnn = preg_replace( '/^arc/', 'a', $fnn ); // for the 'arc' trig synonyms.
					if ( 'ln' === $fnn ) {
						$fnn = 'log';
					}

					$stack->push( call_user_func_array( $fnn, [ $op1 ] ) );

				} elseif ( array_key_exists( $fnn, self::$f ) ) { // user function.
					// get args.
					$args = [];
					for ( $i = count( self::$f[ $fnn ]['args'] ) - 1; $i >= 0; $i -- ) {
						$args[ self::$f[ $fnn ]['args'][ $i ] ] = $stack->pop();
						if ( is_null( $args[ self::$f[ $fnn ]['args'][ $i ] ] ) ) {
							return self::trigger( 'internal error' );
						}
					}
					$stack->push( self::pfx( self::$f[ $fnn ]['func'], $args ) );
				}
				// if the token is a binary operator, pop two values off the stack, do the operation, and push the result back on.
			} elseif ( in_array( $token, [ '+', '-', '*', '/', '^' ], true ) ) {
				$op2 = $stack->pop();
				if ( is_null( $op2 ) ) {
					return self::trigger( 'internal error' );
				}
				$op1 = $stack->pop();
				if ( is_null( $op1 ) ) {
					return self::trigger( 'internal error' );
				}
				switch ( $token ) {
					case '+':
						$stack->push( $op1 + $op2 );
						break;
					case '-':
						$stack->push( $op1 - $op2 );
						break;
					case '*':
						$stack->push( $op1 * $op2 );
						break;
					case '/':
						if ( (int) 0 === (int) $op2 ) {
							return self::trigger( 'division by zero' );
						}
						$stack->push( $op1 / $op2 );
						break;
					case '^':
						$stack->push( pow( $op1, $op2 ) );
						break;
				}
				// if the token is a unary operator, pop one value off the stack, do the operation, and push it back on.
			} elseif ( '_' === $token ) {
				$stack->push( - 1 * $stack->pop() );
				// if the token is a function, pop arguments off the stack, hand them to the function, and push the result back on.
			} elseif ( ! preg_match( '/^([a-z]\w*)\($/', $token, $matches ) ) {
				if ( is_numeric( $token ) ) {
					$stack->push( $token );
				} elseif ( array_key_exists( $token, self::$v ) ) {
					$stack->push( self::$v[ $token ] );
				} elseif ( array_key_exists( $token, $vars ) ) {
					$stack->push( $vars[ $token ] );
				} else {
					return self::trigger( "undefined variable '$token'" );
				}
			}
		}
		// when we're out of tokens, the stack should have a single element, the final result.
		if ( (int) 1 !== (int) $stack->count ) {
			return self::trigger( 'internal error' );
		}

		return $stack->pop();
	}

	/**
	 * Trigger an error, but nicely, if need be.
	 *
	 * @param string $msg The message to output.
	 *
	 * @return bool
	 */
	private static function trigger( $msg ) {
		self::$last_error = $msg;
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			echo "\nError found in:";
			self::debug_print_calling_function();
			echo '<br>';
			echo esc_html( $msg );
			echo '<br>';
			trigger_error( esc_html( $msg ), E_USER_WARNING ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		}

		return false;
	}

	/**
	 * Prints the file name, function name, and
	 * line number which called your function
	 * (not this function, then one that  called
	 * it to begin with)
	 */
	private static function debug_print_calling_function() {
		$file        = 'n/a';
		$func        = 'n/a';
		$line        = 'n/a';
		$debug_trace = debug_backtrace(); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		if ( isset( $debug_trace[1] ) ) {
			$file = $debug_trace[1]['file'] ? $debug_trace[1]['file'] : 'n/a';
			$line = $debug_trace[1]['line'] ? $debug_trace[1]['line'] : 'n/a';
		}
		if ( isset( $debug_trace[2] ) ) {
			$func = $debug_trace[2]['function'] ? $debug_trace[2]['function'] : 'n/a';
		}
		echo wp_kses_post( "\n$file, $func, $line\n" );
	}
}

/**
 * Class THEMECOMPLETE_EPO_MATH_Stack.
 */
class THEMECOMPLETE_EPO_MATH_Stack {

	/**
	 * Stack array.
	 *
	 * @var array
	 */
	public $stack = [];

	/**
	 * Stack counter.
	 *
	 * @var integer
	 */
	public $count = 0;

	/**
	 * Push value into stack.
	 *
	 * @param mixed $val Value to push to the stack.
	 */
	public function push( $val ) {
		$this->stack[ $this->count ] = $val;
		$this->count ++;
	}

	/**
	 * Pop value from stack.
	 *
	 * @return mixed
	 */
	public function pop() {
		if ( $this->count > 0 ) {
			$this->count --;

			return $this->stack[ $this->count ];
		}

		return null;
	}

	/**
	 * Get last value from stack.
	 *
	 * @param integer $n Number to deduct.
	 *
	 * @return mixed
	 */
	public function last( $n = 1 ) {
		$key = $this->count - $n;

		return array_key_exists( $key, $this->stack ) ? $this->stack[ $key ] : null;
	}
}
