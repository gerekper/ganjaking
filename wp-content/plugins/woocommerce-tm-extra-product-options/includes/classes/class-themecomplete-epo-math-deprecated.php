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
 * Based on MathExecutor by Alexander Kiryukhin
 * https://github.com/neonxp/MathExecutor
 * Copyright (c) Alexander Kiryukhin
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
class THEMECOMPLETE_EPO_MATH_DEPRECATED {

	/**
	 * Available variables
	 *
	 * @var array<string, float|string>
	 */
	public $variables = [];

	/**
	 * Variable not found handler.
	 *
	 * @var callable|null
	 */
	public $on_var_not_found = null;

	/**
	 * Validation method that will be invoked when a variable is set using set_var.
	 *
	 * @var callable|null
	 */
	public $on_var_validation = null;

	/**
	 * Operators array (default and custom)
	 * Operators + - * / %
	 * Logical operators ==, !=, <, <, >=, <=, &&, ||
	 *
	 * @var THEMECOMPLETE_EPO_MATH_Operator[]
	 */
	public $operators = [];

	/**
	 * Array of custom functions.
	 *
	 * @var array<string, THEMECOMPLETE_EPO_MATH_CustomFunction>
	 */
	public $functions = [];

	/**
	 * Token cache.
	 *
	 * @var array<string, THEMECOMPLETE_EPO_MATH_Token[]>
	 */
	protected $cache = [];

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->add_defaults();
	}

	/**
	 * When the object is cloned, set default operands and functions.
	 */
	public function __clone() {
		$this->add_defaults();
	}

	/**
	 * Add an operator.
	 *
	 * @param THEMECOMPLETE_EPO_MATH_Operator $operator Operator to add.
	 * @return THEMECOMPLETE_EPO_MATH
	 */
	public function add_operator( THEMECOMPLETE_EPO_MATH_Operator $operator ) : self {
		$this->operators[ $operator->operator ] = $operator;

		return $this;
	}

	/**
	 * Evaluate an expression.
	 *
	 * @param string  $expression The expression to execute.
	 * @param boolean $cache IF the result should be cached.
	 *
	 * @return int|float|string|null
	 */
	public static function evaluate( string $expression, bool $cache = true ) {
		$self = new self();
		$self->use_bcmath( 100 );
		$self->set_division_by_zero_to_zero();
		$result = $self->execute( $expression, $cache );
		unset( $self );
		return $result;
	}

	/**
	 * Execute the expression.
	 *
	 * @param string  $expression The expression to execute.
	 * @param boolean $cache IF the result should be cached.
	 *
	 * @return int|float|string|null
	 */
	public function execute( string $expression, bool $cache = true ) {
		$cache_key = $expression;

		if ( ! array_key_exists( $cache_key, $this->cache ) ) {
			$tokens = ( new THEMECOMPLETE_EPO_MATH_Tokenizer( $expression, $this->operators ) )->tokenize()->build_reverse_polish_notation();

			if ( $cache ) {
				$this->cache[ $cache_key ] = $tokens;
			}
		} else {
			$tokens = $this->cache[ $cache_key ];
		}

		$calculator = new THEMECOMPLETE_EPO_MATH_Calculator( $this->functions, $this->operators );

		$result = $calculator->calculate( $tokens, $this->variables, $this->on_var_not_found, $this );

		if ( ! is_numeric( $result ) ) {
			$result = 0;
		}

		return $result;
	}

	/**
	 * Add a custom function.
	 *
	 * @param string        $name Name of function.
	 * @param callable|null $function The Function to add.
	 *
	 * @return THEMECOMPLETE_EPO_MATH
	 */
	public function add_function( string $name, ?callable $function = null ) : self {
		$this->functions[ $name ] = new THEMECOMPLETE_EPO_MATH_CustomFunction( $name, $function );

		return $this;
	}

	/**
	 * Get all variables.
	 *
	 * @return array<string, float|string>
	 */
	public function get_vars() : array {
		return $this->variables;
	}

	/**
	 * Get a specific var
	 *
	 * @param string $variable The variable to get.
	 * @return int|float
	 */
	public function get_var( string $variable ) {
		if ( ! array_key_exists( $variable, $this->variables ) ) {
			if ( $this->on_var_not_found ) {
				return call_user_func( $this->on_var_not_found, $variable );
			}

			return THEMECOMPLETE_EPO_MATH_Error::trigger( "Variable ({$variable}) not set", 'UnknownVariableError', 0 );
		}

		return $this->variables[ $variable ];
	}

	/**
	 * Add a custom variable. To set a custom validator use set_var_validation_handler.
	 *
	 * @param string $variable The variable to set the value for.
	 * @param mixed  $value The value to set the variable to.
	 * @return THEMECOMPLETE_EPO_MATH
	 */
	public function set_var( string $variable, $value ) : self {
		if ( $this->on_var_validation ) {
			$value = call_user_func( $this->on_var_validation, $variable, $value );
		}

		$this->variables[ $variable ] = $value;

		return $this;
	}

	/**
	 * Test to see if a variable exists
	 *
	 * @param string $variable The variable to test if it exists.
	 */
	public function var_exists( string $variable ) : bool {
		return array_key_exists( $variable, $this->variables );
	}

	/**
	 * Add custom variables.
	 *
	 * @param  array<string, float|int|string> $variables The array variables to add.
	 * @param  bool                            $clear If we should clear previous variables.
	 * @return THEMECOMPLETE_EPO_MATH
	 */
	public function set_vars( array $variables, bool $clear = true ) : self {
		if ( $clear ) {
			$this->remove_vars();
		}

		foreach ( $variables as $name => $value ) {
			$this->set_var( $name, $value );
		}

		return $this;
	}

	/**
	 * Define a method that will be invoked when a variable is not found.
	 * The first parameter will be the variable name, and the returned value will be used as the variable value.
	 *
	 * @param callable $handler The handler to set.
	 * @return THEMECOMPLETE_EPO_MATH
	 */
	public function set_var_not_found_handler( callable $handler ) : self {
		$this->on_var_not_found = $handler;

		return $this;
	}

	/**
	 * Define a validation method that will be invoked when a variable is set using set_var.
	 * The first parameter will be the variable name, and the second will be the variable value.
	 * Set to null to disable validation.
	 *
	 * @param ?callable $handler The validation handler to set.
	 *
	 * @return THEMECOMPLETE_EPO_MATH
	 */
	public function set_var_validation_handler( ?callable $handler ) : self {
		$this->on_var_validation = $handler;

		return $this;
	}

	/**
	 * Remove a custom variable.
	 *
	 * @param string $variable The variable to remove.
	 *
	 * @return THEMECOMPLETE_EPO_MATH
	 */
	public function remove_var( string $variable ) : self {
		unset( $this->variables[ $variable ] );

		return $this;
	}

	/**
	 * Remove all variables and the variable not found handler
	 *
	 * @return THEMECOMPLETE_EPO_MATH
	 */
	public function remove_vars() : self {
		$this->variables        = [];
		$this->on_var_not_found = null;

		return $this;
	}

	/**
	 * Get all registered operators.
	 *
	 * @return array<THEMECOMPLETE_EPO_MATH_Operator> of operator class names
	 */
	public function get_operators() : array {
		return $this->operators;
	}

	/**
	 * Get all registered functions
	 *
	 * @return array<string, THEMECOMPLETE_EPO_MATH_CustomFunction> containing callback and places indexed by function name.
	 */
	public function get_functions() : array {
		return $this->functions;
	}

	/**
	 * Remove a specific operator
	 *
	 * @param string $operator The operator to remove.
	 * @return array<THEMECOMPLETE_EPO_MATH_Operator> of operator class names
	 */
	public function remove_operator( string $operator ) : self {
		unset( $this->operators[ $operator ] );

		return $this;
	}

	/**
	 * Set division by zero returns zero instead of throwing an error
	 */
	public function set_division_by_zero_to_zero() : self {
		$this->add_operator(
			new THEMECOMPLETE_EPO_MATH_Operator(
				'/',
				false,
				180,
				static function( $a, $b ) {
					return 0 == $b ? 0 : $a / $b; // phpcs:ignore WordPress.PHP.StrictComparisons
				}
			)
		);

		return $this;
	}

	/**
	 * Get cache array with tokens
	 *
	 * @return array<string, THEMECOMPLETE_EPO_MATH_Token[]>
	 */
	public function get_cache() : array {
		return $this->cache;
	}

	/**
	 * Clear token's cache
	 */
	public function clear_cache() : self {
		$this->cache = [];

		return $this;
	}

	/**
	 * Use BCMAth fixed precision.
	 *
	 * @param integer $scale The precision to use.
	 * @return self
	 */
	public function use_bcmath( int $scale = 2 ) : self {
		if ( ! function_exists( 'bcscale' ) ) {
			return $this;
		}
		bcscale( $scale );
		$this->add_operator(
			new THEMECOMPLETE_EPO_MATH_Operator(
				'+',
				false,
				170,
				static function( $a, $b ) use ( $scale ) {
					$a = number_format( (float) $a, $scale, '.', '' );
					$b = number_format( (float) $b, $scale, '.', '' );
					return bcadd( "{$a}", "{$b}" );
				}
			)
		);
		$this->add_operator(
			new THEMECOMPLETE_EPO_MATH_Operator(
				'-',
				false,
				170,
				static function( $a, $b ) use ( $scale ) {
					$a = number_format( (float) $a, $scale, '.', '' );
					$b = number_format( (float) $b, $scale, '.', '' );
					return bcsub( "{$a}", "{$b}" );
				}
			)
		);
		$this->add_operator(
			new THEMECOMPLETE_EPO_MATH_Operator(
				'uNeg',
				false,
				200,
				static function( $a ) use ( $scale ) {
					$a = number_format( (float) $a, $scale, '.', '' );
					return bcsub( '0.0', "{$a}" );
				}
			)
		);
		$this->add_operator(
			new THEMECOMPLETE_EPO_MATH_Operator(
				'*',
				false,
				180,
				static function( $a, $b ) use ( $scale ) {
					$a = number_format( (float) $a, $scale, '.', '' );
					$b = number_format( (float) $b, $scale, '.', '' );
					return bcmul( "{$a}", "{$b}" );
				}
			)
		);
		$this->add_operator(
			new THEMECOMPLETE_EPO_MATH_Operator(
				'/',
				false,
				180,
				static function( $a, $b ) use ( $scale ) {
					if ( 0 == $b ) { // phpcs:ignore WordPress.PHP.StrictComparisons
						return THEMECOMPLETE_EPO_MATH_Error::trigger( 'Division By Zero', 'DivisionByZeroError', 0 );
					}
					$a = number_format( (float) $a, $scale, '.', '' );
					$b = number_format( (float) $b, $scale, '.', '' );

					return bcdiv( "{$a}", "{$b}" );
				}
			)
		);
		$this->add_operator(
			new THEMECOMPLETE_EPO_MATH_Operator(
				'^',
				true,
				220,
				static function( $a, $b ) use ( $scale ) {
					$a = number_format( (float) $a, $scale, '.', '' );
					$b = number_format( (float) $b, $scale, '.', '' );
					return bcpow( "{$a}", "{$b}" );
				}
			)
		);
		$this->add_operator(
			new THEMECOMPLETE_EPO_MATH_Operator(
				'%',
				false,
				180,
				static function( $a, $b ) use ( $scale ) {
					$a = number_format( (float) $a, $scale, '.', '' );
					$b = number_format( (float) $b, $scale, '.', '' );
					return bcmod( "{$a}", "{$b}" );
				}
			)
		);

		return $this;
	}

	/**
	 * Set default operands and functions
	 */
	protected function add_defaults() : self {
		foreach ( $this->default_operators() as $name => $operator ) {
			[$callable, $priority, $is_right_assoc] = $operator;
			$this->add_operator( new THEMECOMPLETE_EPO_MATH_Operator( $name, $is_right_assoc, $priority, $callable ) );
		}

		foreach ( $this->default_functions() as $name => $callable ) {
			$this->add_function( $name, $callable );
		}

		$this->on_var_validation = [ $this, 'default_var_validation' ];
		$this->variables         = $this->default_vars();

		return $this;
	}

	/**
	 * Get the default operators
	 *
	 * @return array<string, array{callable, int, bool}>
	 */
	protected function default_operators() : array {
		return [
			'+'    => [
				static function( $a, $b ) {
					return $a + $b;
				},
				170,
				false,
			],
			'-'    => [
				static function( $a, $b ) {
					return $a - $b;
				},
				170,
				false,
			],
			// unary positive token.
			'uPos' => [
				static function( $a ) {
					return $a;
				},
				200,
				false,
			],
			// unary minus token.
			'uNeg' => [
				static function( $a ) {
					return 0 - $a;
				},
				200,
				false,
			],
			'*'    => [
				static function( $a, $b ) {
					return $a * $b;
				},
				180,
				false,
			],
			'/'    => [
				static function( $a, $b ) {
					if ( 0 == $b ) { // phpcs:ignore WordPress.PHP.StrictComparisons
						return THEMECOMPLETE_EPO_MATH_Error::trigger( 'Division By Zero', 'DivisionByZeroError', 0 );
					}

					return $a / $b;
				},
				180,
				false,
			],
			'^'    => [
				static function( $a, $b ) {
					return pow( $a, $b );
				},
				220,
				true,
			],
			'%'    => [
				static function( $a, $b ) {
					return $a % $b;
				},
				180,
				false,
			],
			'&&'   => [
				static function( $a, $b ) {
					return $a && $b;
				},
				100,
				false,
			],
			'||'   => [
				static function( $a, $b ) {
					return $a || $b;
				},
				90,
				false,
			],
			'=='   => [
				static function( $a, $b ) {
					return ( is_numeric( $a ) && is_numeric( $b ) ) ? THEMECOMPLETE_EPO_HELPER()->convert_to_number( $a, true ) === THEMECOMPLETE_EPO_HELPER()->convert_to_number( $b, true ) : ( is_string( $a ) || is_string( $b ) ? 0 == strcmp( $a, $b ) : $a == $b ); // phpcs:ignore WordPress.PHP.StrictComparisons
				},
				140,
				false,
			],
			'!='   => [
				static function( $a, $b ) {
					return ( is_numeric( $a ) && is_numeric( $b ) ) ? THEMECOMPLETE_EPO_HELPER()->convert_to_number( $a, true ) !== THEMECOMPLETE_EPO_HELPER()->convert_to_number( $b, true ) : ( is_string( $a ) || is_string( $b ) ? 0 != strcmp( $a, $b ) : $a != $b ); // phpcs:ignore WordPress.PHP.StrictComparisons
				},
				140,
				false,
			],
			'>='   => [
				static function( $a, $b ) {
					return $a >= $b;
				},
				150,
				false,
			],
			'>'    => [
				static function( $a, $b ) {
					return $a > $b;
				},
				150,
				false,
			],
			'<='   => [
				static function( $a, $b ) {
					return $a <= $b;
				},
				150,
				false,
			],
			'<'    => [
				static function( $a, $b ) {
					return $a < $b;
				},
				150,
				false,
			],
			'!'    => [
				static function( $a ) {
					return ! $a;
				},
				190,
				false,
			],
		];
	}

	/**
	 * Gets the default functions as an array.  Key is function name
	 * and value is the function as a closure.
	 *
	 * @return array<callable>
	 */
	protected function default_functions() : array {
		return [
			'abs'         => static function( $arg ) {
				return abs( $arg );
			},
			'acos'        => static function( $arg ) {
				return acos( $arg );
			},
			'acosh'       => static function( $arg ) {
				return acosh( $arg );
			},
			'arcsin'      => static function( $arg ) {
				return asin( $arg );
			},
			'arcctg'      => static function( $arg ) {
				return M_PI / 2 - atan( $arg );
			},
			'arccot'      => static function( $arg ) {
				return M_PI / 2 - atan( $arg );
			},
			'arccotan'    => static function( $arg ) {
				return M_PI / 2 - atan( $arg );
			},
			'arcsec'      => static function( $arg ) {
				return acos( 1 / $arg );
			},
			'arccosec'    => static function( $arg ) {
				return asin( 1 / $arg );
			},
			'arccsc'      => static function( $arg ) {
				return asin( 1 / $arg );
			},
			'arccos'      => static function( $arg ) {
				return acos( $arg );
			},
			'arctan'      => static function( $arg ) {
				return atan( $arg );
			},
			'arctg'       => static function( $arg ) {
				return atan( $arg );
			},
			'asin'        => static function( $arg ) {
				return asin( $arg );
			},
			'atan'        => static function( $arg ) {
				return atan( $arg );
			},
			'atan2'       => static function( $arg1, $arg2 ) {
				return atan2( $arg1, $arg2 );
			},
			'atanh'       => static function( $arg ) {
				return atanh( $arg );
			},
			'atn'         => static function( $arg ) {
				return atan( $arg );
			},
			'bindec'      => static function( $arg ) {
				return bindec( $arg );
			},
			'ceil'        => static function( $arg ) {
				return ceil( $arg );
			},
			'cos'         => static function( $arg ) {
				return cos( $arg );
			},
			'cosec'       => static function( $arg ) {
				return 1 / sin( $arg );
			},
			'csc'         => static function( $arg ) {
				return 1 / sin( $arg );
			},
			'cosh'        => static function( $arg ) {
				return cosh( $arg );
			},
			'ctg'         => static function( $arg ) {
				return cos( $arg ) / sin( $arg );
			},
			'cot'         => static function( $arg ) {
				return cos( $arg ) / sin( $arg );},
			'cotan'       => static function( $arg ) {
				return cos( $arg ) / sin( $arg );
			},
			'cotg'        => static function( $arg ) {
				return cos( $arg ) / sin( $arg );
			},
			'ctn'         => static function( $arg ) {
				return cos( $arg ) / sin( $arg );},
			'decbin'      => static function( $arg ) {
				return decbin( $arg );
			},
			'dechex'      => static function( $arg ) {
				return dechex( $arg );
			},
			'decoct'      => static function( $arg ) {
				return decoct( $arg );
			},
			'deg2rad'     => static function( $arg ) {
				return deg2rad( $arg );
			},
			'exp'         => static function( $arg ) {
				return exp( $arg );
			},
			'expm1'       => static function( $arg ) {
				return expm1( $arg );
			},
			'floor'       => static function( $arg ) {
				return floor( $arg );
			},
			'int'         => static function( $arg ) {
				return floor( $arg );
			},
			'fmod'        => static function( $arg1, $arg2 ) {
				return fmod( $arg1, $arg2 );
			},
			'hexdec'      => static function( $arg ) {
				return hexdec( $arg );
			},
			'hypot'       => static function( $arg1, $arg2 ) {
				return hypot( $arg1, $arg2 );
			},
			'if'          => function( $expr, $trueval, $falseval ) {
				if ( true === $expr || false === $expr ) {
					$exres = $expr;
				} else {
					$exres = $this->execute( $expr );
				}

				if ( $exres ) {
					return $this->execute( $trueval );
				}

				return $this->execute( $falseval );
			},
			'intdiv'      => static function( $arg1, $arg2 ) {
				return intdiv( $arg1, $arg2 );
			},
			'ln'          => static function( $arg ) {
				return log( $arg );
			},
			'lg'          => static function( $arg ) {
				return log10( $arg );
			},
			'log'         => static function( $arg ) {
				return log( $arg );
			},
			'log10'       => static function( $arg ) {
				return log10( $arg );
			},
			'log1p'       => static function( $arg ) {
				return log1p( $arg );
			},
			'octdec'      => static function( $arg ) {
				return octdec( $arg );
			},
			'pi'          => static function() {
				return M_PI;
			},
			'pow'         => static function( $arg1, $arg2 ) {
				return $arg1 ** $arg2;
			},
			'rad2deg'     => static function( $arg ) {
				return rad2deg( $arg );
			},
			'round'       => static function( $num, int $precision = 0 ) {
				return round( $num, $precision );
			},
			'sin'         => static function( $arg ) {
				return sin( $arg );
			},
			'sinh'        => static function( $arg ) {
				return sinh( $arg );
			},
			'sec'         => static function( $arg ) {
				return 1 / cos( $arg );
			},
			'sqrt'        => static function( $arg ) {
				return sqrt( $arg );
			},
			'tan'         => static function( $arg ) {
				return tan( $arg );
			},
			'tanh'        => static function( $arg ) {
				return tanh( $arg );
			},
			'tn'          => static function( $arg ) {
				return tan( $arg );
			},
			'tg'          => static function( $arg ) {
				return tan( $arg );
			},
			'lookuptable' => function( $field, $lookup_table_id ) {
				$lookup_tables = THEMECOMPLETE_EPO()->lookup_tables;
				$price         = 0;
				$table_num     = 0;
				if ( $lookup_tables ) {
					if ( is_array( $lookup_table_id ) ) {
						$table_num       = (int) $lookup_table_id[1];
						$lookup_table_id = $lookup_table_id[0];
					}
					if ( ! $lookup_table_id ) {
						return 0;
					}
					if ( ! $table_num ) {
						$table_num = 0;
					}
					if ( is_array( $field ) ) {
						$x = $field[0];
						$y = $field[1];
					} else {
						$x = $field;
						$y = '';
					}
					$table = $lookup_tables[ $lookup_table_id ];
					if ( $table ) {
						$table = $table[ $table_num ];
						if ( $table ) {
							$table = $table['data'];
							$x     = (float) $x;
							$y     = (float) $y;
							if ( intval( $x ) != $x ) { // phpcs:ignore WordPress.PHP.StrictComparisons
								$x = strval( $x );
							}
							if ( intval( $y ) != $y ) { // phpcs:ignore WordPress.PHP.StrictComparisons
								$y = strval( $y );
							}
							$x_row = false;
							if ( ! isset( $table[ $x ] ) ) {
								if ( ( (int) 0 === (int) $x ) ) {
									$x_row = $table[ array_keys( $table )[0] ];
								} elseif ( $x ) {
									$x = $this->find_lookup_table_index( $x, $table );
									if ( $x ) {
										$x_row = $table[ $x ];
									}
								}
							} else {
								$x_row = $table[ $x ];
							}
							if ( $x_row && is_array( $x_row ) ) {
								if ( $y ) {
									$y = $this->find_lookup_table_index( $y, $x_row );
								} else {
									$y = array_keys( $x_row );
									$y = $y[0];
								}

								if ( 'max' === $y ) {
									$price = (float) $x_row[ array_keys( $x_row )[ count( array_keys( $x_row ) ) - 1 ] ];
								} else {
									$price = (float) $x_row[ $y ];
								}
							}
						}
					}
				}
				return $price;
			},
		];
	}

	/**
	 * Find the closest index on the provided $table
	 *
	 * @param mixed $value The value to check.
	 * @param array $table The table check the value against.
	 */
	protected function find_lookup_table_index( $value, $table ) {
		$value = (float) $value;
		$table = array_keys( $table );
		$table = array_map(
			function( $n ) {
				return 'max' === $n ? $n : (float) $n;
			},
			$table
		);

		return array_reduce(
			$table,
			function( $a, $b ) use ( $value ) {
				if ( 'max' === $b && $value > $a ) {
					return $b;
				}
				if ( 'max' === $a && $value > $b ) {
					return $a;
				}
				if ( $a < $b ) {
					if ( $value > $a && $value <= $b ) {
						return $b;
					}
				} else {
					if ( ( $value > $b && $value <= $a ) || ( $value > $a || 'max' === $b ) ) {
						return $a;
					}
					return $b;
				}
				if ( $value > $b ) {
					return $b;
				}
				return $a;
			}
		);
	}

	/**
	 * Returns the default variables names as key/value pairs
	 *
	 * @return array<string, float>
	 */
	protected function default_vars() : array {
		return [
			'pi' => 3.141592653589793,
			'e'  => 2.718281828459045,
		];
	}

	/**
	 * Default variable validation, ensures that the value is a scalar or array.
	 *
	 * @param string $variable The variable to validate.
	 * @param mixed  $value The value to validate.
	 */
	protected function default_var_validation( string $variable, $value ) {
		if ( ! is_scalar( $value ) && ! is_array( $value ) && null !== $value ) {
			$type = gettype( $value );

			return THEMECOMPLETE_EPO_MATH_Error::trigger( "Variable ({$variable}) type ({$type}) is not scalar or array!", 0 );
		}
		return $value;
	}

}

/**
 * Math Tokenizer class
 *
 * @package Extra Product Options/Classes/THEMECOMPLETE_EPO_MATH
 * @version 6.0
 */
class THEMECOMPLETE_EPO_MATH_Tokenizer {

	/**
	 * Array of tokens.
	 *
	 * @var array<THEMECOMPLETE_EPO_MATH_Token>
	 */
	public $tokens = [];

	/**
	 * The input expression.
	 *
	 * @var string
	 */
	private $input = '';

	/**
	 * The number buffer.
	 *
	 * @var string
	 */
	private $number_buffer = '';

	/**
	 * The string buffer.
	 *
	 * @var string
	 */
	private $string_buffer = '';

	/**
	 * If negative number is allowed.
	 *
	 * @var boolean
	 */
	private $allow_negative = true;

	/**
	 * Array of operators.
	 *
	 * @var array<THEMECOMPLETE_EPO_MATH_Operator>
	 */
	private $operators = [];

	/**
	 * If the string has single quotes.
	 *
	 * @var boolean
	 */
	private $in_single_quoted_string = false;

	/**
	 * If the string has double quotes.
	 *
	 * @var boolean
	 */
	private $in_double_quoted_string = false;

	/**
	 * Tokenizer constructor.
	 *
	 * @param string $input Input expression.
	 * @param array  $operators Operator array.
	 */
	public function __construct( string $input, array $operators ) {
		$this->input     = $input;
		$this->operators = $operators;
	}

	/**
	 * Tokenize the expression.
	 *
	 * @return self
	 */
	public function tokenize() : self {
		$is_last_char_escape = false;

		foreach ( str_split( $this->input ) as $ch ) {
			switch ( true ) {
				case $this->in_single_quoted_string:
					if ( '\\' === $ch ) {
						if ( $is_last_char_escape ) {
							$this->string_buffer .= '\\';
							$is_last_char_escape  = false;
						} else {
							$is_last_char_escape = true;
						}

						continue 2;
					} elseif ( "'" === $ch ) {
						if ( $is_last_char_escape ) {
							$this->string_buffer .= "'";
							$is_last_char_escape  = false;
						} else {
							$this->tokens[]                = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::STRING, $this->string_buffer );
							$this->in_single_quoted_string = false;
							$this->string_buffer           = '';
						}

						continue 2;
					}

					if ( $is_last_char_escape ) {
						$this->string_buffer .= '\\';
						$is_last_char_escape  = false;
					}
					$this->string_buffer .= $ch;

					continue 2;

				case $this->in_double_quoted_string:
					if ( '\\' === $ch ) {
						if ( $is_last_char_escape ) {
							$this->string_buffer .= '\\';
							$is_last_char_escape  = false;
						} else {
							$is_last_char_escape = true;
						}

						continue 2;
					} elseif ( '"' === $ch ) {
						if ( $is_last_char_escape ) {
							$this->string_buffer .= '"';
							$is_last_char_escape  = false;
						} else {
							$this->tokens[]                = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::STRING, $this->string_buffer );
							$this->in_double_quoted_string = false;
							$this->string_buffer           = '';
						}

						continue 2;
					}

					if ( $is_last_char_escape ) {
						$this->string_buffer .= '\\';
						$is_last_char_escape  = false;
					}
					$this->string_buffer .= $ch;

					continue 2;

				case '[' === $ch:
					$this->tokens[]       = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::FUNCTION, 'array' );
					$this->allow_negative = true;
					$this->tokens[]       = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::LEFTPARENTHESIS, '' );

					continue 2;

				case ' ' === $ch || "\n" === $ch || "\r" === $ch || "\t" === $ch:
					/**
					 * In case those tokens must not be ingored use the following
					 * $this->empty_number_buffer_as_literal();
					 * $this->empty_str_buffer_as_variable();
					 * $this->tokens[] = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::SPACE, '' );
					 */
					continue 2;

				case $this->is_number( $ch ):
					if ( '' !== $this->string_buffer ) {
						$this->string_buffer .= $ch;

						continue 2;
					}
					$this->number_buffer .= $ch;
					$this->allow_negative = false;

					break;

				case 'e' === strtolower( $ch ):
					if ( strlen( $this->number_buffer ) && false !== strpos( $this->number_buffer, '.' ) ) {
						$this->number_buffer .= 'e';
						$this->allow_negative = false;

						break;
					}
					// no break
					// Intentionally fall through.
				case $this->is_alpha( $ch ):
					if ( strlen( $this->number_buffer ) ) {
						$this->empty_number_buffer_as_literal();
						$this->tokens[] = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::OPERATOR, '*' );
					}
					$this->allow_negative = false;
					$this->string_buffer .= $ch;

					break;

				case '"' === $ch:
					$this->in_double_quoted_string = true;

					continue 2;

				case "'" === $ch:
					$this->in_single_quoted_string = true;

					continue 2;

				case $this->is_dot( $ch ):
					$this->number_buffer .= $ch;
					$this->allow_negative = false;

					break;

				case $this->is_lp( $ch ):
					if ( '' !== $this->string_buffer ) {
						$this->tokens[]      = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::FUNCTION, $this->string_buffer );
						$this->string_buffer = '';
					} elseif ( strlen( $this->number_buffer ) ) {
						$this->empty_number_buffer_as_literal();
						$this->tokens[] = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::OPERATOR, '*' );
					}
					$this->allow_negative = true;
					$this->tokens[]       = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::LEFTPARENTHESIS, '' );

					break;

				case $this->is_rp( $ch ) || ']' === $ch:
					$this->empty_number_buffer_as_literal();
					$this->empty_str_buffer_as_variable();
					$this->allow_negative = false;
					$this->tokens[]       = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::RIGHTPARENTHESIS, '' );

					break;

				case $this->is_comma( $ch ):
					$this->empty_number_buffer_as_literal();
					$this->empty_str_buffer_as_variable();
					$this->allow_negative = true;
					$this->tokens[]       = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::PARAMSEPARATOR, '' );

					break;

				default:
					// special case for unary operations.
					if ( '-' === $ch || '+' === $ch ) {
						if ( $this->allow_negative ) {
							$this->allow_negative = false;
							$this->tokens[]       = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::OPERATOR, '-' === $ch ? 'uNeg' : 'uPos' );

							continue 2;
						}
						// could be in exponent, in which case negative should be added to the number_buffer.
						if ( $this->number_buffer && 'e' === $this->number_buffer[ strlen( $this->number_buffer ) - 1 ] ) {
							$this->number_buffer .= $ch;

							continue 2;
						}
					}
					$this->empty_number_buffer_as_literal();
					$this->empty_str_buffer_as_variable();

					if ( '$' !== $ch ) {
						if ( count( $this->tokens ) > 0 ) {
							if ( THEMECOMPLETE_EPO_MATH_Token::OPERATOR === $this->tokens[ count( $this->tokens ) - 1 ]->type ) {
								$this->tokens[ count( $this->tokens ) - 1 ]->value .= $ch;
							} else {
								$this->tokens[] = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::OPERATOR, $ch );
							}
						} else {
							$this->tokens[] = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::OPERATOR, $ch );
						}
					}
					$this->allow_negative = true;
			}
		}
		$this->empty_number_buffer_as_literal();
		$this->empty_str_buffer_as_variable();

		$token_test = [];
		foreach ( $this->tokens as $key => $token ) {
			$token_test[ $key ] = $token->type;
		}
		foreach ( $token_test as $key => $type ) {
			if ( $key > 0 && THEMECOMPLETE_EPO_MATH_Token::SPACE === $type && THEMECOMPLETE_EPO_MATH_Token::VARIABLE === $token_test[ $key + 1 ] && THEMECOMPLETE_EPO_MATH_Token::VARIABLE === $token_test[ $key - 1 ] ) {
				unset( $this->tokens[ $key - 1 ] );
				unset( $this->tokens[ $key + 1 ] );
				$this->tokens[ $key ] = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::LITERAL, 0 );
			}
		}
		return $this;
	}

	/**
	 * Create Reverse Polish Notation.
	 *
	 * @return THEMECOMPLETE_EPO_MATH_Token[] Array of tokens in reverse polish notation
	 */
	public function build_reverse_polish_notation() : array {
		$tokens        = [];
		$stack         = new SplStack();
		$param_counter = new SplStack();

		foreach ( $this->tokens as $token ) {
			try {
				switch ( $token->type ) {
					case THEMECOMPLETE_EPO_MATH_Token::LITERAL:
					case THEMECOMPLETE_EPO_MATH_Token::VARIABLE:
					case THEMECOMPLETE_EPO_MATH_Token::STRING:
						$tokens[] = $token;

						if ( $param_counter->count() > 0 && 0 === $param_counter->top() ) {
							$param_counter->push( $param_counter->pop() + 1 );
						}

						break;

					case THEMECOMPLETE_EPO_MATH_Token::FUNCTION:
						if ( $param_counter->count() > 0 && 0 === $param_counter->top() ) {
							$param_counter->push( $param_counter->pop() + 1 );
						}
						$stack->push( $token );
						$param_counter->push( 0 );

						break;

					case THEMECOMPLETE_EPO_MATH_Token::LEFTPARENTHESIS:
						$stack->push( $token );

						break;

					case THEMECOMPLETE_EPO_MATH_Token::PARAMSEPARATOR:
						while ( THEMECOMPLETE_EPO_MATH_Token::LEFTPARENTHESIS !== $stack->top()->type ) {
							if ( 0 === $stack->count() ) {
								return THEMECOMPLETE_EPO_MATH_Error::trigger( 'Incorrect Brackets', 'IncorrectBracketsError', $tokens );
							}
							$tokens[] = $stack->pop();
						}
						$param_counter->push( $param_counter->pop() + 1 );

						break;

					case THEMECOMPLETE_EPO_MATH_Token::OPERATOR:
						if ( ! array_key_exists( $token->value, $this->operators ) ) {
							return THEMECOMPLETE_EPO_MATH_Error::trigger( $token->value, 'UnknownOperatorError', $tokens );
						}
						$op1 = $this->operators[ $token->value ];

						while ( $stack->count() > 0 && THEMECOMPLETE_EPO_MATH_Token::OPERATOR === $stack->top()->type ) {
							if ( ! array_key_exists( $stack->top()->value, $this->operators ) ) {
								return THEMECOMPLETE_EPO_MATH_Error::trigger( $stack->top()->value, 'UnknownOperatorError', $tokens );
							}
							$op2 = $this->operators[ $stack->top()->value ];

							if ( $op2->priority >= $op1->priority ) {
								$tokens[] = $stack->pop();

								continue;
							}

							break;
						}
						$stack->push( $token );

						break;

					case THEMECOMPLETE_EPO_MATH_Token::RIGHTPARENTHESIS:
						while ( true ) {
							try {
								$ctoken = $stack->pop();

								if ( THEMECOMPLETE_EPO_MATH_Token::LEFTPARENTHESIS === $ctoken->type ) {
									break;
								}
								$tokens[] = $ctoken;
							} catch ( RuntimeException $e ) {
								return THEMECOMPLETE_EPO_MATH_Error::trigger( 'Incorrect Brackets', 'IncorrectBracketsError', $tokens );
							}
						}

						if ( $stack->count() > 0 && THEMECOMPLETE_EPO_MATH_Token::FUNCTION == $stack->top()->type ) { // phpcs:ignore WordPress.PHP.StrictComparisons
							$f              = $stack->pop();
							$f->param_count = $param_counter->pop();
							$tokens[]       = $f;
						}

						break;

					case THEMECOMPLETE_EPO_MATH_Token::SPACE:
						// do nothing.
				}
			} catch ( Exception $e ) {
				$tokens = [];
				return $tokens;
			}
		}

		while ( 0 !== $stack->count() ) {
			if ( THEMECOMPLETE_EPO_MATH_Token::LEFTPARENTHESIS === $stack->top()->type || THEMECOMPLETE_EPO_MATH_Token::RIGHTPARENTHESIS === $stack->top()->type ) {
				return THEMECOMPLETE_EPO_MATH_Error::trigger( 'Incorrect Brackets', 'IncorrectBracketsError', $tokens );
			}

			if ( THEMECOMPLETE_EPO_MATH_Token::SPACE === $stack->top()->type ) {
				$stack->pop();

				continue;
			}
			$tokens[] = $stack->pop();
		}

		return $tokens;
	}

	/**
	 * Check if the current character is a number.
	 *
	 * @param string $ch Current character.
	 * @return boolean
	 */
	private function is_number( string $ch ) : bool {
		return $ch >= '0' && $ch <= '9';
	}

	/**
	 * Check if the current character is an alpha character.
	 *
	 * @param string $ch Current character.
	 * @return boolean
	 */
	private function is_alpha( string $ch ) : bool {
		return $ch >= 'a' && $ch <= 'z' || $ch >= 'A' && $ch <= 'Z' || '_' === $ch;
	}

	/**
	 * Empty tye number buffer.
	 */
	private function empty_number_buffer_as_literal() : void {
		if ( strlen( $this->number_buffer ) ) {
			$this->tokens[]      = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::LITERAL, $this->number_buffer );
			$this->number_buffer = '';
		}
	}

	/**
	 * Check if the current character is a dot.
	 *
	 * @param string $ch Current character.
	 * @return boolean
	 */
	private function is_dot( string $ch ) : bool {
		return '.' === $ch;
	}

	/**
	 * Check if the current character is a left parenthesis.
	 *
	 * @param string $ch Current character.
	 * @return boolean
	 */
	private function is_lp( string $ch ) : bool {
		return '(' === $ch;
	}

	/**
	 * Check if the current character is a right parenthesis.
	 *
	 * @param string $ch Current character.
	 * @return boolean
	 */
	private function is_rp( string $ch ) : bool {
		return ')' === $ch;
	}

	/**
	 * Empty the string buffer.
	 */
	private function empty_str_buffer_as_variable() : void {
		if ( '' !== $this->string_buffer ) {
			$this->tokens[]      = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::VARIABLE, $this->string_buffer );
			$this->string_buffer = '';
		}
	}

	/**
	 * Check if the current character is a comma.
	 *
	 * @param string $ch Current character.
	 * @return boolean
	 */
	private function is_comma( string $ch ) : bool {
		return ',' === $ch;
	}
}

/**
 * Math Token class
 *
 * @package Extra Product Options/Classes/THEMECOMPLETE_EPO_MATH
 * @version 6.0
 */
class THEMECOMPLETE_EPO_MATH_Token {

	public const LITERAL          = 'literal';
	public const VARIABLE         = 'variable';
	public const OPERATOR         = 'operator';
	public const LEFTPARENTHESIS  = 'LP';
	public const RIGHTPARENTHESIS = 'RP';
	public const FUNCTION         = 'function';
	public const PARAMSEPARATOR   = 'separator';
	public const STRING           = 'string';
	public const SPACE            = 'space';

	/**
	 * Token type.
	 *
	 * @var string
	 */
	public $type = self::LITERAL;

	/**
	 * Token value.
	 *
	 * @var mixed
	 */
	public $value;

	/**
	 * Token name.
	 *
	 * @var string|null
	 */
	public $name;

	/**
	 * Store function parameter count in stack.
	 *
	 * @var integer|null
	 */
	public $param_count = null;

	/**
	 * Token constructor.
	 *
	 * @param string      $type Token type.
	 * @param mixed       $value Token value.
	 * @param string|null $name Token name.
	 */
	public function __construct( string $type, $value, ?string $name = null ) {
		$this->type  = $type;
		$this->value = $value;
		$this->name  = $name;
	}
}

/**
 * Math Operator class
 *
 * @package Extra Product Options/Classes/THEMECOMPLETE_EPO_MATH
 * @version 6.0
 */
class THEMECOMPLETE_EPO_MATH_Operator {

	/**
	 * The custom operator.
	 *
	 * @var string
	 */
	public $operator = '';

	/**
	 * If it is a right associative operator.
	 *
	 * @var boolean
	 */
	public $is_right_assoc = false;

	/**
	 * Operator priority
	 *
	 * @var integer
	 */
	public $priority = 0;

	/**
	 * The callable function.
	 *
	 * @var callable(SplStack)
	 */
	public $function;

	/**
	 * The number of parameters for the operator.
	 *
	 * @var integer
	 */
	public $places = 0;

	/**
	 * Operator constructor.
	 *
	 * @param string   $operator The operator.
	 * @param boolean  $is_right_assoc If it is a right associative operator.
	 * @param integer  $priority Operator priority.
	 * @param callable $function Operator Function.
	 */
	public function __construct( string $operator, bool $is_right_assoc, int $priority, callable $function ) {
		$this->operator       = $operator;
		$this->is_right_assoc = $is_right_assoc;
		$this->priority       = $priority;
		$this->function       = $function;
		$reflection           = new ReflectionFunction( $function );
		$this->places         = $reflection->getNumberOfParameters();
	}

	/**
	 * Execute expression.
	 *
	 * @param array<Token> $stack The array of tokens.
	 */
	public function execute( array &$stack ) : THEMECOMPLETE_EPO_MATH_Token {

		if ( count( $stack ) < $this->places ) {
			$stack = [];
			return THEMECOMPLETE_EPO_MATH_Error::trigger( 'Incorrect Expression', 'IncorrectExpressionError', new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::LITERAL, 0 ) );
		}
		$args = [];

		for ( $i = 0; $i < $this->places; $i++ ) {
			array_unshift( $args, array_pop( $stack )->value );
		}

		$result = call_user_func_array( $this->function, $args );

		return new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::LITERAL, $result );
	}
}

/**
 * Math Custom Function class
 *
 * @package Extra Product Options/Classes/THEMECOMPLETE_EPO_MATH
 * @version 6.0
 */
class THEMECOMPLETE_EPO_MATH_CustomFunction {

	/**
	 * The function name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * The callable function.
	 *
	 * @var callable $function
	 */
	public $function;

	/**
	 * The required parameters of the function.
	 *
	 * @var integer
	 */
	private $required_param_count;

	/**
	 * CustomFunction constructor.
	 *
	 * @param string   $name The function name.
	 * @param callable $function The callable function.
	 */
	public function __construct( string $name, callable $function ) {
		$this->name                 = $name;
		$this->function             = $function;
		$this->required_param_count = ( new ReflectionFunction( $function ) )->getNumberOfRequiredParameters();
	}

	/**
	 * Execute expression.
	 *
	 * @param array   $stack The array of tokens.
	 * @param integer $param_count_in_stack The function paramter count.
	 */
	public function execute( array &$stack, int $param_count_in_stack ) : THEMECOMPLETE_EPO_MATH_Token {

		if ( $param_count_in_stack < $this->required_param_count ) {
			$param_count_in_stack = $this->required_param_count;
		}

		// We skip this section.
		if ( $param_count_in_stack < $this->required_param_count ) {
			$stack = [];
			return THEMECOMPLETE_EPO_MATH_Error::trigger( $this->name, 'IncorrectNumberOfFunctionParametersError', new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::LITERAL, 0 ) );
		}

		$args = [];
		if ( $param_count_in_stack > 0 ) {
			for ( $i = 0; $i < $param_count_in_stack; $i++ ) {
				$object = array_pop( $stack );
				if ( is_object( $object ) ) {
					$argument = $object->value;
				} else {
					$argument = null;
				}
				if ( null === $argument ) {
					$argument = '0';
					array_push( $args, $argument );
				} else {
					array_unshift( $args, $argument );
				}
			}
		}

		$result = call_user_func_array( $this->function, $args );

		return new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::LITERAL, $result );
	}
}

/**
 * Math Calculator class
 *
 * @package Extra Product Options/Classes/THEMECOMPLETE_EPO_MATH
 * @version 6.0
 */
class THEMECOMPLETE_EPO_MATH_Calculator {

	/**
	 * Array of custom functions.
	 *
	 * @var array<string, CustomFunction>
	 */
	private $functions = [];

	/**
	 * Array of operators.
	 *
	 * @var array<THEMECOMPLETE_EPO_MATH_Operator>
	 */
	private $operators = [];

	/**
	 * Class Constructor
	 *
	 * @param array<string, CustomFunction>          $functions Array of custom functions.
	 * @param array<THEMECOMPLETE_EPO_MATH_Operator> $operators Array of operators.
	 */
	public function __construct( array $functions, array $operators ) {
		$this->functions = $functions;
		$this->operators = $operators;
	}

	/**
	 * Calculate array of tokens in reverse polish notation
	 *
	 * @param array<THEMECOMPLETE_EPO_MATH_Token> $tokens Array of tokens.
	 * @param array<string, float|string>         $variables Array of variables.
	 * @param callable|null                       $on_var_not_found Variable not found handler.
	 * @param mixed                               $math_object The math class.
	 *
	 * @return int|float|string|null
	 */
	public function calculate( array $tokens, array $variables, ?callable $on_var_not_found = null, $math_object = false ) {

		if ( empty( $tokens ) ) {
			return 0;
		}

		/** Array of THEMECOMPLETE_EPO_MATH_Token */
		$stack = [];

		foreach ( $tokens as $token ) {
			if ( THEMECOMPLETE_EPO_MATH_Token::LITERAL === $token->type || THEMECOMPLETE_EPO_MATH_Token::STRING === $token->type ) {
				$stack[] = $token;
			} elseif ( THEMECOMPLETE_EPO_MATH_Token::VARIABLE === $token->type ) {
				$variable = $token->value;

				$value = null;

				if ( array_key_exists( $variable, $variables ) ) {
					$value = $variables[ $variable ];
				} elseif ( $on_var_not_found ) {
					$value = call_user_func( $on_var_not_found, $variable );
				} else {
					$value                               = $variable;
					$math_object->variables[ $variable ] = $value;
					$variables[ $variable ]              = $value;
				}
				$stack[] = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Token::LITERAL, $value, $variable );
			} elseif ( THEMECOMPLETE_EPO_MATH_Token::FUNCTION === $token->type ) {
				if ( ! array_key_exists( $token->value, $this->functions ) ) {
					$math_object->add_function(
						$token->value,
						function() {
							return 0;
						}
					);
					$this->functions = $math_object->functions;
					if ( ! array_key_exists( $token->value, $this->functions ) ) {
						return THEMECOMPLETE_EPO_MATH_Error::trigger( $token->value, 'UnknownFunctionError', 0 );
					} else {
						THEMECOMPLETE_EPO_MATH_Error::trigger( $token->value, 'UnknownFunctionError', 0 );
					}
				}
				$stack[] = $this->functions[ $token->value ]->execute( $stack, $token->param_count );
			} elseif ( THEMECOMPLETE_EPO_MATH_Token::OPERATOR === $token->type ) {
				if ( ! array_key_exists( $token->value, $this->operators ) ) {
					return THEMECOMPLETE_EPO_MATH_Error::trigger( $token->value, 'UnknownOperatorError', 0 );
				}
				$stack[] = $this->operators[ $token->value ]->execute( $stack );
			}
		}
		$result = array_pop( $stack );

		if ( null === $result || ! empty( $stack ) ) {
			return THEMECOMPLETE_EPO_MATH_Error::trigger( 'Stack must be empty', 'IncorrectExpressionError', 0 );
		}

		if ( false === $result->value ) {
			$result->value = 0;
		}

		if ( true === $result->value ) {
			$result->value = 1;
		}

		if ( is_numeric( $result->value ) ) {
			$result->value = (float) $result->value;
		}

		return $result->value;
	}
}

/**
 * Math Error class
 *
 * Error handling for THEMECOMPLETE_EPO_MATH.
 *
 * @package Extra Product Options/Classes/THEMECOMPLETE_EPO_MATH
 * @version 6.0
 */
class THEMECOMPLETE_EPO_MATH_Error {

	/**
	 * Trigger an error, in a nice way.
	 *
	 * @param string $msg The message to output.
	 * @param string $code The error code.
	 * @param mixed  $return The value to return.
	 *
	 * @return bool
	 */
	public static function trigger( $msg, $code = '', $return = false ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			echo "\nError found in:";
			self::debug_print_calling_function();
			echo '<br>';
			echo esc_html( $code );
			echo '<br>';
			echo esc_html( $msg );
			echo '<br>';
			trigger_error( esc_html( $msg ), E_USER_WARNING ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		}

		return $return;
	}

	/**
	 * Prints the file name, function name, and
	 * line number which called your function
	 * (not this function, the one that called it to begin with)
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
