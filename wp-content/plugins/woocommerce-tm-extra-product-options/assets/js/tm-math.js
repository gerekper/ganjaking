
/*jshint esversion: 6 */
( function( window ) {
	'use strict';

	var empty = function( mixedVar ) {
		var undef;
		var key;
		var i;
		var len;
		var emptyValues = [ undef, null, false, 0, '', '0' ];
		for ( i = 0, len = emptyValues.length; i < len; i++ ) {
			if ( mixedVar === emptyValues[ i ] ) {
				return true;
			}
		}
		if ( typeof mixedVar === 'object' ) {
			for ( key in mixedVar ) {
				if ( mixedVar.hasOwnProperty( key ) ) {
					return false;
				}
			}
			return true;
		}
		return false;
	};

	var array_key_exists = function( key, search ) {
		if ( ! search || ( search.constructor !== Array && search.constructor !== Object ) ) {
			return false;
		}
		return key in search;
	};

	var getNumberOfParameters = function( func ) {
		const ARROW = true;
		const FUNC_ARGS = ARROW ? /^(function)?\s*[^\(]*\(\s*([^\)]*)\)/m : /^(function)\s*[^\(]*\(\s*([^\)]*)\)/m;
		const FUNC_ARG_SPLIT = /,/;
		const FUNC_ARG = /^\s*(_?)(.+?)\1\s*$/;
		const STRIP_COMMENTS = /((\/\/.*$)|(\/\*[\s\S]*?\*\/))/mg;

		return ( ( func || '' )
			.toString()
			.replace( STRIP_COMMENTS, '' )
			.match( FUNC_ARGS ) || [ '', '', '' ] )[ 2 ]
			.split( FUNC_ARG_SPLIT )
			.map( function( arg ) {
				return arg.replace( FUNC_ARG, function( all, underscore, name ) {
					return name.split( '=' )[ 0 ].trim();
				} );
			} )
			.filter( String )
			.length;
	};

	var getNumberOfRequiredParameters = function( func ) {
		return func.length;
	};

	var is_scalar = function( mixedVar ) {
		return ( /boolean|number|string/ ).test( typeof mixedVar );
	};

	var gettype = function( obj ) {
		return {}.toString.call( obj ).split( ' ' )[ 1 ].slice( 0, -1 ).toLowerCase();
	};

	var floatval = function( s, d ) {
		var n;

		if ( ! ( typeof s === 'string' || typeof s === 'number' ) || isNaN( s ) ) {
			if ( d !== undefined ) {
				return d;
			}
			return 0;
		}
		n = parseFloat( s );
		if ( isNaN( n ) ) {
			if ( d !== undefined ) {
				return d;
			}
			return s;
		}

		return n;
	};

	var strcmp = function( str1, str2 ) {
		return ( ( str1 === str2 ) ? 0 : ( ( str1 > str2 ) ? 1 : -1 ) );
	};

	var bindec = function( binaryString ) {
		binaryString = ( binaryString + '' ).replace( /[^01]/gi, '' );
		return parseInt( binaryString, 2 );
	};

	var decbin = function( number ) {
		if ( number < 0 ) {
			number = 0xFFFFFFFF + number + 1;
		}
		return parseInt( number, 10 ).toString( 2 );
	};

	var dechex = function( number ) {
		if ( number < 0 ) {
			number = 0xFFFFFFFF + number + 1;
		}
		return parseInt( number, 10 ).toString( 16 );
	};

	var decoct = function( number ) {
		if ( number < 0 ) {
			number = 0xFFFFFFFF + number + 1;
		}
		return parseInt( number, 10 ).toString( 8 );
	};

	var deg2rad = function( angle ) {
		return angle * 0.017453292519943295; // (angle / 180) * Math.PI;
	};

	var roundWithPrecision = function( num, dec = 0 ) {
		var num_sign = num >= 0 ? 1 : -1;
		return dec === 0 ? Math.round( num ) : parseFloat( ( Math.round( ( num * Math.pow( 10, dec ) ) + ( num_sign * 0.0001 ) ) / Math.pow( 10, dec ) ).toFixed( dec ) );
	};

	var hexdec = function( hexString ) {
		hexString = ( hexString + '' ).replace( /[^a-f0-9]/gi, '' );
		return parseInt( hexString, 16 );
	};

	var octdec = function( octString ) {
		octString = ( octString + '' ).replace( /[^0-7]/gi, '' );
		return parseInt( octString, 8 );
	};

	var hypot = function( x, y ) {
		var t;
		x = Math.abs( x );
		y = Math.abs( y );
		t = Math.min( x, y );
		x = Math.max( x, y );
		t = t / x;
		return x * Math.sqrt( 1 + ( t * t ) ) || null;
	};

	var THEMECOMPLETE_EPO_MATH = {
		variables: {},
		on_var_not_found: undefined,
		on_var_validation: undefined,
		operators: {},
		functions: {},
		cache: {},
		construct: function() {
			this.add_defaults();
			this.set_division_by_zero_to_zero();
			return this;
		},
		add_operator: function( $operator ) {
			this.operators[ $operator.operator ] = $operator;
			return this;
		},
		evaluate: function( $expression, $cache = true ) {
			return this.execute( $expression, $cache );
		},
		execute: function( $expression, $cache = true ) {
			var $cache_key = $expression;
			var $calculator;
			var $tokens;
			var result;

			if ( ! array_key_exists( $cache_key, this.cache ) ) {
				$tokens = new THEMECOMPLETE_EPO_MATH_Tokenizer( $expression, this.operators );
				$tokens = $tokens.tokenize().build_reverse_polish_notation();
				if ( $cache ) {
					this.cache[ $cache_key ] = $tokens;
				}
			} else {
				$tokens = this.cache[ $cache_key ];
			}

			$calculator = new THEMECOMPLETE_EPO_MATH_Calculator( this.functions, this.operators );

			result = $calculator.calculate( $tokens, this.variables, this.on_var_not_found, this );

			if ( 'number' !== gettype( result ) || isNaN( result ) ) {
				result = 0;
			}

			return result;
		},
		add_function: function( $name, $function ) {
			this.functions[ $name ] = new THEMECOMPLETE_EPO_MATH_CustomFunction( $name, $function );
			return this;
		},
		get_vars: function() {
			return this.variables;
		},
		get_var: function( $variable ) {
			if ( ! array_key_exists( $variable, this.variables ) ) {
				if ( this.on_var_not_found && 'function' === typeof this.on_var_not_found ) {
					this.on_var_not_found( $variable );
				}
				return THEMECOMPLETE_EPO_MATH_Error.trigger( 'Variable (' + $variable + ') not set', 'UnknownVariableError', 0 );
			}
			return this.variables[ $variable ];
		},
		set_var: function( $variable, $value ) {
			if ( this.on_var_validation && 'function' === typeof this.on_var_validation ) {
				$value = this.on_var_validation( $variable, $value );
			}
			this.variables[ $variable ] = $value;
			return this;
		},
		var_exists: function( $variable ) {
			return array_key_exists( $variable, this.variables );
		},
		set_vars: function( $variables, $clear = true ) {
			var $this = this;
			if ( $clear ) {
				this.remove_vars();
			}
			$variables.forFach( function( $value, $name ) {
				$this.set_var( $name, $value );
			} );
			return this;
		},
		set_var_not_found_handler: function( $handler ) {
			this.on_var_not_found = $handler;
			return this;
		},
		set_var_validation_handler: function( $handler ) {
			this.on_var_validation = $handler;
			return this;
		},
		remove_var: function( $variable ) {
			delete this.variables[ $variable ];
			return this;
		},
		remove_vars: function() {
			this.variables = {};
			this.on_var_not_found = null;
			return this;
		},
		get_operators: function() {
			return this.operators;
		},
		get_functions: function() {
			return this.functions;
		},
		remove_operator: function( $operator ) {
			delete this.operators[ $operator ];
		},
		set_division_by_zero_to_zero: function() {
			this.add_operator(
				new THEMECOMPLETE_EPO_MATH_Operator(
					'/',
					false,
					180,
					function( $a, $b ) {
						$a = Number( $a );
						$b = Number( $b );
						return 0 == $b ? 0 : $a / $b; // eslint-disable-line eqeqeq
					}
				)
			);
			return this;
		},
		get_cache: function() {
			return this.cache;
		},
		clear_cache: function() {
			this.cache = [];
		},
		add_defaults: function() {
			var $this = this;
			var default_operators = this.default_operators();
			var default_functions = this.default_functions();
			Object.keys( default_operators ).forEach( function( $name ) {
				var $operator = default_operators[ $name ];
				$this.add_operator( new THEMECOMPLETE_EPO_MATH_Operator( $name, $operator[ 2 ], $operator[ 1 ], $operator[ 0 ] ) );
			} );
			Object.keys( default_functions ).forEach( function( $name ) {
				var $callable = default_functions[ $name ];
				$this.add_function( $name, $callable );
			} );
			this.on_var_validation = this.default_var_validation;
			this.variables = this.default_vars();
			return this;
		},
		default_operators: function() {
			return {
				'+': [
					function( $a, $b ) {
						$a = floatval( $a, 0 );
						$b = floatval( $b, 0 );
						return $a + $b;
					},
					170,
					false
				],
				'-': [
					function( $a, $b ) {
						$a = floatval( $a, 0 );
						$b = floatval( $b, 0 );
						return $a - $b;
					},
					170,
					false
				],
				// unary positive token.
				uPos: [
					function( $a ) {
						$a = floatval( $a, 0 );
						return $a;
					},
					200,
					false
				],
				// unary minus token.
				uNeg: [
					function( $a ) {
						$a = floatval( $a, 0 );
						return 0 - $a;
					},
					200,
					false
				],
				'*': [
					function( $a, $b ) {
						$a = floatval( $a, 0 );
						$b = floatval( $b, 0 );
						return $a * $b;
					},
					180,
					false
				],
				'/': [
					function( $a, $b ) {
						$a = floatval( $a, 0 );
						$b = floatval( $b, 0 );
						if ( empty( $b ) ) {
							return THEMECOMPLETE_EPO_MATH_Error.trigger( 'Division By Zero', 'DivisionByZeroError', 0 );
						}
						return $a / $b;
					},
					180,
					false
				],
				'^': [
					function( $a, $b ) {
						$a = floatval( $a, 0 );
						$b = floatval( $b, 0 );
						return Math.pow( $a, $b );
					},
					220,
					true
				],
				'%': [
					function( $a, $b ) {
						$a = floatval( $a, 0 );
						$b = floatval( $b, 0 );
						return $a % $b;
					},
					180,
					false
				],
				'&&': [
					function( $a, $b ) {
						$a = String( $a );
						$b = String( $b );
						if ( $a.isNumeric() ) {
							$a = floatval( $a, 0 );
						}
						if ( $b.isNumeric() ) {
							$b = floatval( $b, 0 );
						}
						return $a && $b ? 1 : 0;
					},
					100,
					false
				],
				'||': [
					function( $a, $b ) {
						$a = String( $a );
						$b = String( $b );
						if ( $a.isNumeric() ) {
							$a = floatval( $a, 0 );
						}
						if ( $b.isNumeric() ) {
							$b = floatval( $b, 0 );
						}
						return $a || $b ? 1 : 0;
					},
					90,
					false
				],
				'==': [
					function( $a, $b ) {
						$a = String( $a );
						$b = String( $b );
						if ( $a.isNumeric() && $b.isNumeric() ) {
							return floatval( $a ) === floatval( $b ) ? 1 : 0;
						}
						return 0 === strcmp( $a, $b ) ? 1 : 0;
					},
					140,
					false
				],
				'!=': [
					function( $a, $b ) {
						$a = String( $a );
						$b = String( $b );
						if ( $a.isNumeric() && $b.isNumeric() ) {
							return floatval( $a ) !== floatval( $b ) ? 1 : 0;
						}
						return 0 === strcmp( $a, $b ) ? 1 : 0;
					},
					140,
					false
				],
				'>=': [
					function( $a, $b ) {
						$a = String( $a );
						$b = String( $b );
						if ( $a.isNumeric() ) {
							$a = floatval( $a, 0 );
						}
						if ( $b.isNumeric() ) {
							$b = floatval( $b, 0 );
						}
						return $a >= $b ? 1 : 0;
					},
					150,
					false
				],
				'>': [
					function( $a, $b ) {
						$a = String( $a );
						$b = String( $b );
						if ( $a.isNumeric() ) {
							$a = floatval( $a, 0 );
						}
						if ( $b.isNumeric() ) {
							$b = floatval( $b, 0 );
						}
						return $a > $b ? 1 : 0;
					},
					150,
					false
				],
				'<=': [
					function( $a, $b ) {
						$a = String( $a );
						$b = String( $b );
						if ( $a.isNumeric() ) {
							$a = floatval( $a, 0 );
						}
						if ( $b.isNumeric() ) {
							$b = floatval( $b, 0 );
						}
						return $a <= $b ? 1 : 0;
					},
					150,
					false
				],
				'<': [
					function( $a, $b ) {
						$a = String( $a );
						$b = String( $b );
						if ( $a.isNumeric() ) {
							$a = floatval( $a, 0 );
						}
						if ( $b.isNumeric() ) {
							$b = floatval( $b, 0 );
						}
						return $a < $b ? 1 : 0;
					},
					150,
					false
				]
			};
		},
		default_functions: function() {
			var $this = this;
			return {
				abs: Math.abs,
				acos: Math.acos,
				acosh: Math.acosh,
				arcsin: Math.asin,
				arcctg: function( $arg ) {
					return ( Math.PI / 2 ) - Math.atan( $arg );
				},
				arccot: function( $arg ) {
					return ( Math.PI / 2 ) - Math.atan( $arg );
				},
				arccotan: function( $arg ) {
					return ( Math.PI / 2 ) - Math.atan( $arg );
				},
				arcsec: function( $arg ) {
					return Math.acos( 1 / $arg );
				},
				arccosec: function( $arg ) {
					return Math.asin( 1 / $arg );
				},
				arccsc: function( $arg ) {
					return Math.asin( 1 / $arg );
				},
				arccos: Math.acos,
				arctan: Math.atan,
				arctg: Math.atan,
				array: function( ...$args ) {
					return $args;
				},
				asin: Math.asin,
				atan: Math.atan,
				atan2: Math.atan2,
				atanh: Math.atanh,
				atn: Math.atan,

				avg: function( $arg1, ...$args ) {
					var sum;
					if ( Array.isArray( $arg1 ) ) {
						if ( 0 === $arg1.length ) {
							return THEMECOMPLETE_EPO_MATH_Error.trigger( 'Array must contain at least one element!', 'InvalidArgumentError', 0 );
						}
					}
					$args = [].concat.apply( [], [ $arg1, ...$args ] );

					sum = $args.reduce( function( previousValue, currentValue ) {
						return floatval( currentValue, 0 ) + floatval( previousValue, 0 );
					} );
					return sum / $args.length;
				},
				average: function( $arg1, ...$args ) {
					var sum;
					if ( Array.isArray( $arg1 ) ) {
						if ( 0 === $arg1.length ) {
							return THEMECOMPLETE_EPO_MATH_Error.trigger( 'Array must contain at least one element!', 'InvalidArgumentError', 0 );
						}
					}
					$args = [].concat.apply( [], [ $arg1, ...$args ] );

					sum = $args.reduce( function( previousValue, currentValue ) {
						return floatval( currentValue, 0 ) + floatval( previousValue, 0 );
					} );
					return sum / $args.length;
				},
				bindec: bindec,
				ceil: Math.ceil,
				cos: Math.cos,
				cosec: function( $arg ) {
					return Math.sin( 1 / $arg );
				},
				csc: function( $arg ) {
					return Math.sin( 1 / $arg );
				},
				cosh: Math.cosh,
				ctg: function( $arg ) {
					return Math.cos( $arg ) / Math.sin( $arg );
				},
				cot: function( $arg ) {
					return Math.cos( $arg ) / Math.sin( $arg );
				},
				cotan: function( $arg ) {
					return Math.cos( $arg ) / Math.sin( $arg );
				},
				cotg: function( $arg ) {
					return Math.cos( $arg ) / Math.sin( $arg );
				},
				ctn: function( $arg ) {
					return Math.cos( $arg ) / Math.sin( $arg );
				},
				decbin: decbin,
				dechex: dechex,
				decoct: decoct,
				deg2rad: deg2rad,
				exp: Math.exp,
				expm1: Math.expm1,
				floor: Math.floor,
				int: Math.floor,
				fmod: function( $arg1, $arg2 ) {
					return $arg1 % $arg2;
				},
				hexdec: hexdec,
				hypot: hypot,
				if: function( $expr, $trueval, $falseval ) {
					var $exres;
					if ( 0 === $expr || 1 === $expr || true === $expr || false === $expr ) {
						$exres = $expr;
					} else {
						$exres = $this.execute( $expr );
					}
					if ( $exres ) {
						return $this.execute( $trueval );
					}
					return $this.execute( $falseval );
				},
				intdiv: function( $arg1, $arg2 ) {
					return Math.trunc( Math.trunc( $arg1 ) / Math.trunc( $arg2 ) );
				},
				ln: Math.log,
				lg: Math.log10,
				log: Math.log,
				log1p: Math.log1p,
				max: function( $arg1, ...$args ) {
					var $array;
					if ( Array.isArray( $arg1 ) && 0 === $arg1.length ) {
						return THEMECOMPLETE_EPO_MATH_Error.trigger( 'Array must contain at least one element!', 'InvalidArgumentError', 0 );
					}
					$array = Array.isArray( $arg1 ) ? $arg1 : [ $arg1, ...$args ];
					$array = $array.map( floatval );

					return Math.max( ...$array );
				},
				min: function( $arg1, ...$args ) {
					var $array;
					if ( Array.isArray( $arg1 ) && 0 === $arg1.length ) {
						return THEMECOMPLETE_EPO_MATH_Error.trigger( 'Array must contain at least one element!', 'InvalidArgumentError', 0 );
					}
					$array = Array.isArray( $arg1 ) ? $arg1 : [ $arg1, ...$args ];
					$array = $array.map( floatval );

					return Math.min( ...$array );
				},
				octdec: octdec,
				pi: function() {
					return Math.PI;
				},
				pow: Math.pow,
				rad2deg: function( angle ) {
					return angle * 57.29577951308232; // angle / Math.PI * 180
				},
				round: roundWithPrecision,
				sin: Math.sin,
				sinh: Math.sinh,
				sec: function( $arg ) {
					return 1 / Math.cos( $arg );
				},
				sqrt: Math.sqrt,
				tan: Math.tan,
				tanh: Math.tanh,
				tn: Math.tan,
				tg: Math.tan,
				lookuptable: function( field, lookupTableId ) {
					var lookupTables;
					var x;
					var y;
					var table;
					var xColumn;
					var price = 0;
					var tableNum = 0;
					var TMEPOJS = window.TMEPOJS;

					if ( TMEPOJS ) {
						lookupTables = window && window.jQuery && window.jQuery.epoAPI && window.jQuery.epoAPI.util.parseJSON( TMEPOJS.lookupTables );
						if ( lookupTables ) {
							if ( Array.isArray( lookupTableId ) ) {
								tableNum = lookupTableId[ 1 ];
								lookupTableId = lookupTableId[ 0 ];
							}
							if ( empty( lookupTableId ) ) {
								return 0;
							}
							if ( empty( tableNum ) ) {
								tableNum = 0;
							}
							if ( Array.isArray( field ) ) {
								x = field[ 0 ];
								y = field[ 1 ];
							} else {
								x = field;
								y = '';
							}

							table = lookupTables[ lookupTableId ];
							if ( table ) {
								table = table[ tableNum ];
								if ( table ) {
									table = table.data;
									xColumn = table[ x ];
									if ( xColumn === undefined && x && x !== undefined ) {
										if ( floatval( x ) === 0 ) {
											xColumn = table[ Object.keys( table )[ 0 ] ];
										} else if ( x ) {
											x = $this.find_lookup_table_index( x, table );
											xColumn = table[ x ];
										}
									}
									if ( xColumn !== undefined ) {
										if ( y && y !== undefined ) {
											y = $this.find_lookup_table_index( y, xColumn );
										} else {
											// fetch the first row since this means we want
											// a single row result
											y = Object.keys( xColumn )[ 0 ];
										}
										if ( y === 'max' ) {
											price = floatval( xColumn[ Object.keys( xColumn )[ Object.keys( xColumn ).length - 1 ] ] );
										} else {
											price = floatval( xColumn[ y ] );
										}
									}
								}
							}
						}
					}
					return price;
				},
				concat: function( $arg1, ...$args ) {
					var $array;
					if ( Array.isArray( $arg1 ) && 0 === $arg1.length ) {
						return THEMECOMPLETE_EPO_MATH_Error.trigger( 'Array must contain at least one element!', 'InvalidArgumentError', 0 );
					}
					$array = Array.isArray( $arg1 ) ? $arg1 : [ $arg1, ...$args ];

					return $array.join( '' );
				}
			};
		},
		find_lookup_table_index: function( value, table ) {
			var r;
			var keys = Object.keys( table );
			value = floatval( value );

			r = keys.map( function( n ) {
				return n === 'max' ? n : floatval( n );
			} ).reduce( function( a, b ) {
				if ( b === 'max' && value > a ) {
					return b;
				}
				if ( a === 'max' && value > b ) {
					return a;
				}
				if ( a < b ) {
					if ( value > a && value <= b ) {
						return b;
					}
				} else {
					if ( ( value > b && value <= a ) || ( value > a || b === 'max' ) ) {
						return a;
					}
					return b;
				}
				if ( value > b ) {
					return b;
				}
				return a;
			} );
			keys = keys.map( function( n ) {
				return n === 'max' ? n : floatval( n );
			} );

			if ( value > Math.max( ...keys ) || value < Math.min( ...keys ) ) {
				return false;
			}

			return r;
		},
		default_vars: function() {
			return {
				pi: 3.141592653589793,
				e: 2.718281828459045
			};
		},
		default_var_validation: function( $variable, $value ) {
			if ( ! is_scalar( $value ) && ! Array.isArray( $value ) && undefined !== $value ) {
				return THEMECOMPLETE_EPO_MATH_Error.trigger( 'Variable (' + $variable + ') type (' + gettype( $value ) + ') is not scalar or array!', 0 );
			}
			return $value;
		}
	};

	var THEMECOMPLETE_EPO_MATH_Tokenizer = function( $input, $operators ) {
		var node = {
			tokens: [],
			input: '',
			operators: {},
			number_buffer: '',
			string_buffer: '',
			allow_negative: true,
			in_single_quoted_string: false,
			in_double_quoted_string: false,
			is_number: function( ch ) {
				return ch >= '0' && ch <= '9';
			},
			is_alpha: function( ch ) {
				return ( ch >= 'a' && ch <= 'z' ) || ( ch >= 'A' && ch <= 'Z' ) || '_' === ch;
			},
			is_dot: function( ch ) {
				return '.' === ch;
			},
			is_lp: function( ch ) {
				return '(' === ch;
			},
			is_rp: function( ch ) {
				return ')' === ch;
			},
			is_comma: function( ch ) {
				return ',' === ch;
			},
			empty_number_buffer_as_literal: function() {
				if ( this.number_buffer.length ) {
					this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.LITERAL, this.number_buffer ) );
					this.number_buffer = '';
				}
			},
			empty_str_buffer_as_variable: function() {
				if ( '' !== this.string_buffer ) {
					this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.VARIABLE, this.string_buffer ) );
					this.string_buffer = '';
				}
			},
			tokenize: function() {
				var is_last_char_escape = false;
				var $this = this;
				var $token_test = [];

				this.tokens = [];

				this.input.toString().split( '' ).forEach( function( ch ) {
					switch ( true ) {
						case $this.in_single_quoted_string:
							if ( '\\' === ch ) {
								if ( is_last_char_escape ) {
									$this.string_buffer += '\\';
									is_last_char_escape = false;
								} else {
									is_last_char_escape = true;
								}
								break;
							} else if ( "'" === ch ) {
								if ( is_last_char_escape ) {
									$this.string_buffer += "'";
									is_last_char_escape = false;
								} else {
									$this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.STRING, $this.string_buffer ) );
									$this.in_single_quoted_string = false;
									$this.string_buffer = '';
								}
								break;
							}
							if ( is_last_char_escape ) {
								$this.string_buffer += '\\';
								is_last_char_escape = false;
							}
							$this.string_buffer += ch;
							break;
						case $this.in_double_quoted_string:
							if ( '\\' === ch ) {
								if ( is_last_char_escape ) {
									$this.string_buffer += '\\';
									is_last_char_escape = false;
								} else {
									is_last_char_escape = true;
								}
								break;
							} else if ( '"' === ch ) {
								if ( is_last_char_escape ) {
									$this.string_buffer += '"';
									is_last_char_escape = false;
								} else {
									$this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.STRING, $this.string_buffer ) );
									$this.in_double_quoted_string = false;
									$this.string_buffer = '';
								}
								break;
							}
							if ( is_last_char_escape ) {
								$this.string_buffer += '\\';
								is_last_char_escape = false;
							}
							$this.string_buffer += ch;
							break;
						case '[' === ch:
							$this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.FUNCTION, 'array' ) );
							$this.allow_negative = true;
							$this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.LEFTPARENTHESIS, '' ) );
							break;
						case ' ' === ch || '\n' === ch || '\r' === ch || '\t' === ch:
							/**
							 * In case those tokens must not be ingored use the following
							 *
							 * $this.empty_number_buffer_as_literal();
							 * $this.empty_str_buffer_as_variable();
							 * $this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.SPACE, '' ) );
							 */
							break;
						case $this.is_number( ch ):
							if ( '' !== $this.string_buffer ) {
								$this.string_buffer += ch;
								break;
							}
							$this.number_buffer += ch;
							$this.allow_negative = false;
							break;
						case 'e' === ch.toLowerCase():
							if ( $this.number_buffer.length && -1 !== $this.number_buffer.indexOf( '.' ) ) {
								$this.number_buffer += 'e';
								$this.allow_negative = false;
								break;
							}
							/* falls through */
						case $this.is_alpha( ch ):
							if ( $this.number_buffer.length ) {
								$this.empty_number_buffer_as_literal();
								$this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.OPERATOR, '*' ) );
							}
							$this.allow_negative = false;
							$this.string_buffer += ch;
							break;
						case '"' === ch:
							$this.in_double_quoted_string = true;
							break;
						case "'" === ch:
							$this.in_single_quoted_string = true;
							break;
						case $this.is_dot( ch ):
							$this.number_buffer += ch;
							$this.allow_negative = false;
							break;
						case $this.is_lp( ch ):
							if ( '' !== $this.string_buffer ) {
								$this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.FUNCTION, $this.string_buffer ) );
								$this.string_buffer = '';
							} else if ( $this.number_buffer.length ) {
								$this.empty_number_buffer_as_literal();
								$this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.OPERATOR, '*' ) );
							}
							$this.allow_negative = true;
							$this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.LEFTPARENTHESIS, ch ) );
							break;
						case $this.is_rp( ch ) || ']' === ch :
							$this.empty_number_buffer_as_literal();
							$this.empty_str_buffer_as_variable();
							$this.allow_negative = false;
							$this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.RIGHTPARENTHESIS, ch ) );
							break;
						case $this.is_comma( ch ):
							$this.empty_number_buffer_as_literal();
							$this.empty_str_buffer_as_variable();
							$this.allow_negative = true;
							$this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.PARAMSEPARATOR, ch ) );
							break;
						default:
							// special case for unary operations.
							if ( '-' === ch || '+' === ch ) {
								if ( $this.allow_negative ) {
									$this.allow_negative = false;
									$this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.OPERATOR, '-' === ch ? 'uNeg' : 'uPos' ) );
									break;
								}
								// could be in exponent, in which case negative should be added to the number_buffer.
								if ( $this.number_buffer && 'e' === $this.number_buffer[ $this.number_buffer.length - 1 ] ) {
									$this.number_buffer += ch;
									break;
								}
							}
							$this.empty_number_buffer_as_literal();
							$this.empty_str_buffer_as_variable();

							if ( '$' !== ch ) {
								if ( $this.tokens.length > 0 ) {
									if ( THEMECOMPLETE_EPO_MATH_Constants.OPERATOR === $this.tokens[ $this.tokens.length - 1 ].type ) {
										$this.tokens[ $this.tokens.length - 1 ].value += ch;
									} else {
										$this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.OPERATOR, ch ) );
									}
								} else {
									$this.tokens.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.OPERATOR, ch ) );
								}
							}
							$this.allow_negative = true;
					}
				} );
				this.empty_number_buffer_as_literal();
				this.empty_str_buffer_as_variable();

				this.tokens.forEach( function( $token, $key ) {
					$token_test[ $key ] = $token.type;
				} );
				$token_test.forEach( function( $type, $key ) {
					if ( $key > 0 && 'space' === $type && 'variable' === $token_test[ $key + 1 ] && 'variable' === $token_test[ $key - 1 ] ) {
						$this.tokens[ $key ] = new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.LITERAL, 0 );
						$this.tokens.splice( $key + 1, 1 );
						$this.tokens.splice( $key - 1, 1 );
					}
				} );
				return this;
			},
			build_reverse_polish_notation: function() {
				var $tokens = [];
				var $stack = [];
				var $param_counter = [];
				var $ctoken;
				var $f;
				var $op1;
				var $op2;
				var $this = this;

				try {
					this.tokens.forEach( function( $token ) {
						switch ( $token.type ) {
							case THEMECOMPLETE_EPO_MATH_Constants.LITERAL:
							case THEMECOMPLETE_EPO_MATH_Constants.VARIABLE:
							case THEMECOMPLETE_EPO_MATH_Constants.STRING:
								$tokens.push( $token );

								if ( $param_counter.length > 0 && 0 === $param_counter[ $param_counter.length - 1 ] ) {
									$param_counter.push( $param_counter.pop() + 1 );
								}

								break;

							case THEMECOMPLETE_EPO_MATH_Constants.FUNCTION:
								if ( $param_counter.length > 0 && 0 === $param_counter[ $param_counter.length - 1 ] ) {
									$param_counter.push( $param_counter.pop() + 1 );
								}
								$stack.push( $token );
								$param_counter.push( 0 );

								break;

							case THEMECOMPLETE_EPO_MATH_Constants.LEFTPARENTHESIS:
								$stack.push( $token );

								break;

							case THEMECOMPLETE_EPO_MATH_Constants.PARAMSEPARATOR:
								while ( THEMECOMPLETE_EPO_MATH_Constants.LEFTPARENTHESIS !== $stack[ $stack.length - 1 ].type ) {
									if ( 0 === $stack.length ) {
										return THEMECOMPLETE_EPO_MATH_Error.trigger( 'Incorrect Brackets', 'IncorrectBracketsError', $tokens );
									}
									$tokens.push( $stack.pop() );
								}
								$param_counter.push( $param_counter.pop() + 1 );

								break;

							case THEMECOMPLETE_EPO_MATH_Constants.OPERATOR:
								if ( ! array_key_exists( $token.value, $this.operators ) ) {
									return THEMECOMPLETE_EPO_MATH_Error.trigger( $token.value, 'UnknownOperatorError', $tokens );
								}
								$op1 = $this.operators[ $token.value ];

								while ( $stack.length > 0 && THEMECOMPLETE_EPO_MATH_Constants.OPERATOR === $stack[ $stack.length - 1 ].type ) {
									if ( ! array_key_exists( $stack[ $stack.length - 1 ].value, $this.operators ) ) {
										return THEMECOMPLETE_EPO_MATH_Error.trigger( $stack[ $stack.length - 1 ].value, 'UnknownOperatorError', $tokens );
									}
									$op2 = $this.operators[ $stack[ $stack.length - 1 ].value ];

									if ( $op2.priority >= $op1.priority ) {
										$tokens.push( $stack.pop() );

										continue;
									}

									break;
								}
								$stack.push( $token );

								break;

							case THEMECOMPLETE_EPO_MATH_Constants.RIGHTPARENTHESIS:
								while ( true ) {
									try {
										$ctoken = $stack.pop();

										if ( THEMECOMPLETE_EPO_MATH_Constants.LEFTPARENTHESIS === $ctoken.type ) {
											break;
										}
										$tokens.push( $ctoken );
									} catch ( $e ) {
										return THEMECOMPLETE_EPO_MATH_Error.trigger( 'Incorrect Brackets', 'IncorrectBracketsError', $tokens );
									}
								}

								if ( $stack.length > 0 && THEMECOMPLETE_EPO_MATH_Constants.FUNCTION === $stack[ $stack.length - 1 ].type ) {
									$f = $stack.pop();
									$f.param_count = $param_counter.pop();
									$tokens.push( $f );
								}

								break;

							case THEMECOMPLETE_EPO_MATH_Constants.SPACE:
								// do nothing.
						}
					} );
				} catch ( $e ) {
					$tokens = [];
					return THEMECOMPLETE_EPO_MATH_Error.trigger( $e, 'Error', $tokens );
				}

				while ( 0 !== $stack.length ) {
					if ( THEMECOMPLETE_EPO_MATH_Constants.LEFTPARENTHESIS === $stack[ $stack.length - 1 ].type || THEMECOMPLETE_EPO_MATH_Constants.RIGHTPARENTHESIS === $stack[ $stack.length - 1 ].type ) {
						return THEMECOMPLETE_EPO_MATH_Error.trigger( 'Incorrect Brackets', 'IncorrectBracketsError', $tokens );
					}

					if ( THEMECOMPLETE_EPO_MATH_Constants.SPACE === $stack[ $stack.length - 1 ].type ) {
						$stack.pop();
						continue;
					}
					$tokens.push( $stack.pop() );
				}

				return $tokens;
			}
		};
		node.input = $input;
		node.operators = $operators;
		return node;
	};

	var THEMECOMPLETE_EPO_MATH_Constants = {
		LITERAL: 'literal',
		VARIABLE: 'variable',
		OPERATOR: 'operator',
		LEFTPARENTHESIS: 'LP',
		RIGHTPARENTHESIS: 'RP',
		FUNCTION: 'function',
		PARAMSEPARATOR: 'separator',
		STRING: 'string',
		SPACE: 'space'

	};

	var THEMECOMPLETE_EPO_MATH_Token = function( $type, $value, $name ) {
		var node = {
			type: THEMECOMPLETE_EPO_MATH_Constants.LITERAL,
			value: undefined,
			name: undefined,
			param_count: null
		};
		node.type = $type;
		node.value = $value;
		node.name = $name;
		return node;
	};

	var THEMECOMPLETE_EPO_MATH_Operator = function( $operator, $is_right_assoc, $priority, $function ) {
		var node = {
			operator: '',
			is_right_assoc: false,
			priority: 0,
			function: null,
			places: 0
		};
		node.operator = $operator;
		node.is_right_assoc = $is_right_assoc;
		node.priority = $priority;
		node.function = $function;
		node.places = getNumberOfParameters( $function );

		node.execute = function( $stack ) {
			var $args = [];
			var $i;
			var $result;

			if ( $stack.length < this.places ) {
				// Empty the $stack
				$stack.splice( 0, $stack.length );
				return THEMECOMPLETE_EPO_MATH_Error.trigger( 'Incorrect Expression', 'IncorrectExpressionError', new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.LITERAL, 0 ) );
			}

			for ( $i = 0; $i < this.places; $i++ ) {
				$args.unshift( $stack.pop().value );
			}

			$result = this.function.apply( null, $args );
			if ( 'number' !== gettype( $result ) && 'string' !== gettype( $result ) ) {
				$result = 0;
			}

			return new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.LITERAL, $result );
		};

		return node;
	};

	var THEMECOMPLETE_EPO_MATH_CustomFunction = function( $name, $function ) {
		var node = {
			name: '',
			function: undefined,
			required_param_count: undefined
		};

		node.name = $name;
		node.function = $function;
		node.required_param_count = getNumberOfRequiredParameters( $function );

		node.execute = function( $stack, $param_count_in_stack ) {
			var $args = [];
			var $i;
			var $argument;
			var $result;
			if ( $param_count_in_stack < this.required_param_count ) {
				$param_count_in_stack = this.required_param_count;
			}
			// We skip this section.
			if ( $param_count_in_stack < this.required_param_count ) {
				// Empty the $stack
				$stack.splice( 0, $stack.length );
				return THEMECOMPLETE_EPO_MATH_Error.trigger( this.name, 'IncorrectNumberOfFunctionParametersError', new THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.LITERAL, 0 ) );
			}

			if ( $param_count_in_stack > 0 ) {
				for ( $i = 0; $i < $param_count_in_stack; $i++ ) {
					$argument = $stack.length ? $stack.pop().value : 0;
					if ( null === $argument ) {
						$argument = '0';
						$args.push( $argument );
					} else {
						$args.unshift( $argument );
					}
				}
			}

			$result = this.function.apply( null, $args );

			if ( $result === Infinity || $result === -Infinity ) {
				$result = 0;
			}

			return THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.LITERAL, $result );
		};

		return node;
	};

	var THEMECOMPLETE_EPO_MATH_Calculator = function( $functions, $operators ) {
		var node = {
			functions: {},
			operators: {}
		};
		node.functions = $functions;
		node.operators = $operators;

		node.calculate = function( $tokens, $variables, $on_var_not_found = null, $math_object = false ) {
			var $this = this;
			/** Array of THEMECOMPLETE_EPO_MATH_Token */
			var $stack = [];
			var $result;

			if ( empty( $tokens ) ) {
				return 0;
			}
			try {
				$tokens.forEach( function( $token ) {
					var $variable;
					var $value;
					if ( THEMECOMPLETE_EPO_MATH_Constants.LITERAL === $token.type || THEMECOMPLETE_EPO_MATH_Constants.STRING === $token.type ) {
						$stack.push( $token );
					} else if ( THEMECOMPLETE_EPO_MATH_Constants.VARIABLE === $token.type ) {
						$variable = $token.value;

						$value = null;

						if ( array_key_exists( $variable, $variables ) ) {
							$value = $variables[ $variable ];
						} else if ( $on_var_not_found && 'function' === typeof $on_var_not_found ) {
							$value = $on_var_not_found( $variable );
						} else {
							$value = $variable;
							$math_object.variables[ $variable ] = $value;
							$variables[ $variable ] = $value;
						}
						$stack.push( THEMECOMPLETE_EPO_MATH_Token( THEMECOMPLETE_EPO_MATH_Constants.LITERAL, $value, $variable ) );
					} else if ( THEMECOMPLETE_EPO_MATH_Constants.FUNCTION === $token.type ) {
						if ( ! array_key_exists( $token.value, $this.functions ) ) {
							$math_object.add_function(
								$token.value,
								function() {
									return 0;
								}
							);
							$this.functions = $math_object.functions;
							if ( ! array_key_exists( $token.value, $this.functions ) ) {
								return THEMECOMPLETE_EPO_MATH_Error.trigger( $token.value, 'UnknownFunctionError', 0 );
							}
							THEMECOMPLETE_EPO_MATH_Error.trigger( $token.value, 'UnknownFunctionError', 0 );
						}
						$stack.push( $this.functions[ $token.value ].execute( $stack, $token.param_count ) );
					} else if ( THEMECOMPLETE_EPO_MATH_Constants.OPERATOR === $token.type ) {
						if ( ! array_key_exists( $token.value, $this.operators ) ) {
							return THEMECOMPLETE_EPO_MATH_Error.trigger( $token.value, 'UnknownOperatorError', 0 );
						}
						$stack.push( $this.operators[ $token.value ].execute( $stack ) );
					}
				} );
			} catch ( $e ) {
				if ( window.TMEPOJS && window.TMEPOJS.WP_DEBUG ) {
					window.console.log( $e );
				}
			}
			$result = $stack.pop();

			if ( null === $result || ! empty( $stack ) ) {
				return THEMECOMPLETE_EPO_MATH_Error.trigger( 'Stack must be empty', 'IncorrectExpressionError', 0 );
			}

			if ( false === $result.value ) {
				$result.value = 0;
			}

			if ( true === $result.value ) {
				$result.value = 1;
			}

			if ( 'string' === gettype( $result.value ) && $result.value.isNumeric() ) {
				$result.value = floatval( $result.value );
			}

			return $result.value;
		};
		return node;
	};

	var THEMECOMPLETE_EPO_MATH_Error = {
		trigger: function( $msg, $code = '', $return = false ) {
			if ( window.TMEPOJS && window.TMEPOJS.WP_DEBUG ) {
				window.console.log( $code + '\n' + $msg );
				window.console.trace();
			}
			return $return;
		}
	};

	window.tcmexp = THEMECOMPLETE_EPO_MATH.construct();
}( window ) );
