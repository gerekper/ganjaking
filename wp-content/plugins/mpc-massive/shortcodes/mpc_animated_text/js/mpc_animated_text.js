/*----------------------------------------------------------------------------*\
	ANIMATED TEXT SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function rotate_word( $animated_text, $words, _options ) {
		var $word  = $words.eq( _options[ 'word_index' ] ),
			$block = $word.data( 'parent_block' ),
			_timeout;

		_options[ 'word_index' ] = _options[ 'word_index' ] + 1;

		_timeout = setTimeout( function() {
			if ( ( _options[ 'word_index' ] < _options[ 'words' ] ) ||
				( _options[ 'word_index' ] >= _options[ 'words' ] && _options[ 'loop' ] ) ) {
				if ( _options[ 'word_index' ] >= _options[ 'words' ] && _options[ 'loop' ] ) {
					_options[ 'word_index' ] = 0;
				}

				if ( _options[ 'dynamic' ] ) {
					_options[ 'words_wrap' ].stop( true ).animate( { 'width': $words.eq( _options[ 'word_index' ] ).data( 'width' ) }, _options[ 'duration' ] );
				}

				$block.stop( true ).animate( { 'margin-top': - _options[ 'height' ] }, {
					'duration': _options[ 'duration' ],
					'complete': function() {
						$block
							.appendTo( _options[ 'words_wrap' ] )
							.css( 'margin-top', '' );

						rotate_word( $animated_text, $words, _options );
					}
				} );
			}
		}, _options[ 'delay' ] );

		$animated_text.on( 'mpc.clear', function() {
			clearTimeout( _timeout );
		} );
	}

	function typewrite_word( $animated_text, $words, _options ) {
		var $word       = $words.eq( _options[ 'word_index' ] ),
			_char_index = 0,
			_word       = $word.data( 'clear_text' ),
			_length     = _word.length,
			_write_interval, _erase_interval, _timeout;

		_options[ 'word_index' ] = _options[ 'word_index' ] + 1;

		_write_interval = setInterval( function() {
			$word.text( $word.text() + _word[ _char_index++ ] );

			if ( _char_index >= _length ) {
				clearInterval( _write_interval );

				_timeout = setTimeout( function() {
					if ( ( _options[ 'word_index' ] < _options[ 'words' ] ) || ( _options[ 'word_index' ] >= _options[ 'words' ] && _options[ 'loop' ] ) ) {
						if ( _options[ 'word_index' ] >= _options[ 'words' ] && _options[ 'loop' ] ) {
							_options[ 'word_index' ] = 0;
						}

						_erase_interval = setInterval( function() {
							$word.text( $word.text().slice( 0, -1 ) );
							_char_index--;

							if ( _char_index < 0 ) {
								clearInterval( _erase_interval );

								typewrite_word( $animated_text, $words, _options );
							}
						}, _options[ 'duration' ] / 2 );
					}
				}, _options[ 'delay' ] );
			}
		}, _options[ 'duration' ] );

		$animated_text.on( 'mpc.clear', function() {
			clearInterval( _write_interval );
			clearInterval( _erase_interval );
			clearTimeout( _timeout );
		} );
	}


	function init_shortcode( $animated_text ) {
		var $words_wrap    = $animated_text.find( '.mpc-animated-text' ),
			$words         = $animated_text.find( '.mpc-animated-text__word' ),
			_options       = $animated_text.data( 'options' ),
			_word          = '',
			_longest_word  = '',
			_defaults      = {
				'style':      'rotator',
				'duration':   1000,
				'delay':      1000,
				'loop':       true,
				'dynamic':    false,
				'words':      $words.length,
				'word_index': 0,
				'words_wrap': $words_wrap
			};

		if ( typeof _options == 'undefined' ) {
			_options = _defaults
		} else {
			_options = $.extend( _defaults, _options );

			_options[ 'duration' ] = parseInt( _options[ 'duration' ] );
			_options[ 'delay' ]    = parseInt( _options[ 'delay' ] );
		}

		if ( $words.length == 1 && _options[ 'style' ] == 'rotator' ) {
			return;
		}

		if ( _options[ 'style' ] == 'rotator' ) {
			_options[ 'height' ] = _options[ 'default_height' ] = $words_wrap.height();

			$words_wrap.height( _options[ 'height' ] );

			$animated_text.addClass( 'mpc-loaded' );

			$words.each( function() {
				var $word = $( this );

				$word.data( 'parent_block', $word.parent() );

				if ( _options[ 'dynamic' ] ) {
					$word.data( 'width', $word.width() );
				}
			} );

			if ( _options[ 'dynamic' ] ) {
				$words_wrap.stop( true ).animate( { 'width': $words.eq( 0 ).data( 'width' ) }, _options[ 'duration' ] );

				_mpc_vars.$window.on( 'load', function() {
					$words.each( function() {
						var $word = $( this );

						$word.data( 'width', $word.width() );
					} );
				} );
			}

			rotate_word( $animated_text, $words, _options );

			_mpc_vars.$window.on( 'mpc.resize', function() {
				var _max_height = 0;

				$words.each( function() {
					var $this = $( this );

					if ( $this[ 0 ].scrollHeight > _max_height ) {
						_max_height = $this[ 0 ].scrollHeight;
					}
				} );

				_options[ 'height' ] = _max_height;

				$words.height( _options[ 'height' ] );
				$words_wrap.height( _options[ 'height' ] );
			} );
		} else if ( _options[ 'style' ] == 'typewrite' ) {
			$words.each( function() {
				var $word = $( this );

				_word = $word.text().replace( / /g, '\xa0' ); // non-breaking space

				$word.data( 'clear_text', $word.text() );

				$word.data( 'default_text', _word );

				if ( _longest_word.length < _word.length ) {
					_longest_word = _word.length;
				}
			} );

			$words.text( '' );

			_options[ 'duration' ] = _options[ 'duration' ] / _longest_word;

			typewrite_word( $animated_text, $words, _options );
		}

		$animated_text.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_animated_text = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $animated_text = this.$el.find( '.mpc-animated-text-wrap' );

				$animated_text.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $animated_text ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $animated_text ] );

				init_shortcode( $animated_text );

				window.InlineShortcodeView_mpc_animated_text.__super__.rendered.call( this );
			},
			beforeUpdate: function () {
				this.$el.find( '.mpc-animated-text-wrap' ).trigger( 'mpc.clear' );
			}
		} );
	}

	var $animated_texts = $( '.mpc-animated-text-wrap' );

	$animated_texts.each( function() {
		var $animated_text = $( this );

		$animated_text.one( 'mpc.init', function () {
			init_shortcode( $animated_text );
		} );
	} );
} )( jQuery );
