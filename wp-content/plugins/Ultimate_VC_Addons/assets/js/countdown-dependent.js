/*! Simple JavaScript Inheritance
 * By John Resig http://ejohn.org/
 * MIT Licensed.
 */
( function () {
	let j = false;
	window.JQClass = function () {};
	JQClass.classes = {};
	JQClass.extend = function extender( f ) {
		const g = this.prototype;
		j = true;
		const h = new this();
		j = false;
		for ( const i in f ) {
			h[ i ] =
				typeof f[ i ] === 'function' && typeof g[ i ] === 'function'
					? ( function ( d, e ) {
							return function () {
								const b = this._super;
								this._super = function ( a ) {
									return g[ d ].apply( this, a );
								};
								const c = e.apply( this, arguments );
								this._super = b;
								return c;
							};
					  } )( i, f[ i ] )
					: f[ i ];
		}
		function JQClass() {
			if ( ! j && this._init ) {
				this._init.apply( this, arguments );
			}
		}
		JQClass.prototype = h;
		JQClass.prototype.constructor = JQClass;
		JQClass.extend = extender;
		return JQClass;
	};
} )();
( function ( $ ) {
	JQClass.classes.JQPlugin = JQClass.extend( {
		name: 'plugin',
		defaultOptions: {},
		regionalOptions: {},
		_getters: [],
		_getMarker() {
			return 'is-' + this.name;
		},
		_init() {
			$.extend(
				this.defaultOptions,
				( this.regionalOptions && this.regionalOptions[ '' ] ) || {}
			);
			const c = camelCase( this.name );
			$[ c ] = this;
			$.fn[ c ] = function ( a ) {
				const b = Array.prototype.slice.call( arguments, 1 );
				if ( $[ c ]._isNotChained( a, b ) ) {
					return $[ c ][ a ].apply(
						$[ c ],
						[ this[ 0 ] ].concat( b )
					);
				}
				return this.each( function () {
					if ( typeof a === 'string' ) {
						if ( a[ 0 ] === '_' || ! $[ c ][ a ] ) {
							throw 'Unknown method: ' + a;
						}
						$[ c ][ a ].apply( $[ c ], [ this ].concat( b ) );
					} else {
						$[ c ]._attach( this, a );
					}
				} );
			};
		},
		setDefaults( a ) {
			$.extend( this.defaultOptions, a || {} );
		},
		_isNotChained( a, b ) {
			if (
				a === 'option' &&
				( b.length === 0 ||
					( b.length === 1 && typeof b[ 0 ] === 'string' ) )
			) {
				return true;
			}
			return $.inArray( a, this._getters ) > -1;
		},
		_attach( a, b ) {
			a = $( a );
			if ( a.hasClass( this._getMarker() ) ) {
				return;
			}
			a.addClass( this._getMarker() );
			b = $.extend(
				{},
				this.defaultOptions,
				this._getMetadata( a ),
				b || {}
			);
			const c = $.extend(
				{ name: this.name, elem: a, options: b },
				this._instSettings( a, b )
			);
			a.data( this.name, c );
			this._postAttach( a, c );
			this.option( a, b );
		},
		_instSettings( a, b ) {
			return {};
		},
		_postAttach( a, b ) {},
		_getMetadata( d ) {
			try {
				let f = d.data( this.name.toLowerCase() ) || '';
				f = f.replace( /'/g, '"' );
				f = f.replace( /([a-zA-Z0-9]+):/g, function ( a, b, i ) {
					const c = f.substring( 0, i ).match( /"/g );
					return ! c || c.length % 2 === 0 ? '"' + b + '":' : b + ':';
				} );
				f = $.parseJSON( '{' + f + '}' );
				for ( const g in f ) {
					const h = f[ g ];
					if (
						typeof h === 'string' &&
						h.match( /^new Date\((.*)\)$/ )
					) {
						f[ g ] = eval( h );
					}
				}
				return f;
			} catch ( e ) {
				return {};
			}
		},
		_getInst( a ) {
			return $( a ).data( this.name ) || {};
		},
		option( a, b, c ) {
			a = $( a );
			const d = a.data( this.name );
			if ( ! b || ( typeof b === 'string' && c == null ) ) {
				var e = ( d || {} ).options;
				return e && b ? e[ b ] : e;
			}
			if ( ! a.hasClass( this._getMarker() ) ) {
				return;
			}
			var e = b || {};
			if ( typeof b === 'string' ) {
				e = {};
				e[ b ] = c;
			}
			this._optionsChanged( a, d, e );
			$.extend( d.options, e );
		},
		_optionsChanged( a, b, c ) {},
		destroy( a ) {
			a = $( a );
			if ( ! a.hasClass( this._getMarker() ) ) {
				return;
			}
			this._preDestroy( a, this._getInst( a ) );
			a.removeData( this.name ).removeClass( this._getMarker() );
		},
		_preDestroy( a, b ) {},
	} );
	function camelCase( c ) {
		return c.replace( /-([a-z])/g, function ( a, b ) {
			return b.toUpperCase();
		} );
	}
	$.JQPlugin = {
		createPlugin( a, b ) {
			if ( typeof a === 'object' ) {
				b = a;
				a = 'JQPlugin';
			}
			a = camelCase( a );
			const c = camelCase( b.name );
			JQClass.classes[ c ] = JQClass.classes[ a ].extend( b );
			new JQClass.classes[ c ]();
		},
	};
} )( jQuery );
