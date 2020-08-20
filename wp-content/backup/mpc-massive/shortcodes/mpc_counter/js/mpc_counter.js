/*----------------------------------------------------------------------------*\
	COUNTER SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	// var MPC_Counters = function() {
	// 	this.$el = $( '.mpc-counter' );
	//
	// 	this.init = function() {
	// 		var _self = this;
	//
	// 		if ( typeof CountUp !== 'undefined' ) {
	// 			_self.$el.each( function() {
	// 				var $this    = $( this ),
	// 					$counter = $this.find( '.mpc-counter--target' ),
	// 					_options = $counter.data( 'options' ),
	// 					_counter = new CountUp( $counter[ 0 ], _options.initial, _options.value, _options.decimals, _options.duration, _options );
	//
	// 				$this.on( 'mpc.waypoint', function() { _self.inview_init( $counter, _counter ); } );
	// 			} );
	//
	// 			mpc_init_class( _self.$el );
	// 			// this.$el.trigger( 'mpc.inited' );
	// 		} else {
	// 			setTimeout( function() {
	// 				_self.init();
	// 			}, 250 );
	// 		}
	// 	};
	//
	// 	this.inview_init = function( $target, _counter ) {
	// 		_counter.start();
	// 	};
	// };
	//
	// _mpc_vars.$document.ready( function() {
	// 	var _mpc_counters = new MPC_Counters();
	// 	_mpc_counters.init();
	// });

	function fast_init( $this ) {
		$this.text( $this.attr( 'data-to' ) );
	}

	function delay_init( $this ) {
		if ( typeof CountUp !== 'undefined' ) {
			var _options = $this.data( 'options' ),
				_counter = new CountUp( $this[0], parseFloat( _options.initial ), parseFloat( _options.value ),
										parseInt( _options.decimals ), parseFloat( _options.duration ), _options );

			if( parseInt( _options.delay ) > 0 ) {
				setTimeout( function() {
					_counter.start();
				}, parseInt( _options.delay ) );
			} else {
				_counter.start();
			}
		} else {
			setTimeout( function() {
				delay_init( $this );
			}, 50 );
		}
	}

	function init_shortcode( $counter ) {
		$counter.trigger( 'mpc.inited' );
	}

	var $counters = $( '.mpc-counter' );

	$counters.each( function() {
		var $counter = $( this ),
		    $parent = $counter.parents( '.mpc-container' );

		if( $parent.length ) {
			$parent.one( 'mpc.parent-init', function() {
				delay_init( $counter.find( '.mpc-counter--target' ) );
			} );
		} else if ( $counter.is( '.mpc-waypoint--init' ) ) {
			delay_init( $counter.find( '.mpc-counter--target' ) );
		} else {
			$counter.one( 'mpc.waypoint', function() {
				if( !$counter.is( '.mpc-init--fast' ) ) {
					delay_init( $counter.find( '.mpc-counter--target' ) );
				}
			});
		}

		$counter.one( 'mpc.init', function () {
			if( $counter.is( '.mpc-init--fast' ) ) {
				fast_init( $counter.find( '.mpc-counter--target' ) );
			}

			init_shortcode( $counter );
		} );

		$counter.one( 'mpc.init-fast', function() {
			fast_init( $counter.find( '.mpc-counter--target' ) );
		} );
	} );

	/* FrontEnd Init */
	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_counter = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $counter = this.$el.find( '.mpc-counter' );

				$counter.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $counter ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $counter ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $counter ] );

				delay_init( $counter.find( '.mpc-counter--target' ) );

				window.InlineShortcodeView_mpc_counter.__super__.rendered.call( this );
			}
		} );
	}
} )( jQuery );
