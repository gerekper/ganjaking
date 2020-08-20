/*----------------------------------------------------------------------------*\
	MPC GLOBALS
\*----------------------------------------------------------------------------*/

if ( _mpc_vars == undefined ) {
	var _mpc_vars = {};
}

if ( typeof _mpc_ajax != 'undefined' ) {
	_mpc_vars.ajax_url = _mpc_ajax;
}

if ( typeof _mpc_animations != 'undefined' ) {
	_mpc_vars.animations = _mpc_animations === '1';
} else {
	_mpc_vars.animations = false;
}

if ( typeof _mpc_parallax != 'undefined' ) {
	_mpc_vars.parallax = _mpc_parallax === '1';
} else {
	_mpc_vars.parallax = false;
}

_mpc_vars.$window   = jQuery( window );
_mpc_vars.$body     = jQuery( 'body' );
_mpc_vars.$document = jQuery( document );
_mpc_vars.breakpoints = {
	'huge': window.matchMedia( '(min-width: 1200px)' ).matches,
	'large': window.matchMedia( '(max-width: 1199px) and (min-width: 993px)' ).matches,
	'medium': window.matchMedia( '(max-width: 992px) and (min-width: 769px)' ).matches,
	'small': window.matchMedia( '(max-width: 768px) and (min-width: 481px)' ).matches,
	'tiny': window.matchMedia( '(max-width: 480px)' ).matches,
	'custom': function( _media_query ) {
		if( _media_query != '' ) {
			return window.matchMedia( _media_query ).matches
		} else {
			return null;
		}
	}
};

_mpc_vars.carousel_breakpoints = function( $carousel ) {
	var _slick = $carousel.data( 'mpcslick' ),
	    _slides = $carousel.children().length,
	    _toShow = 0,
	    _toScroll = 0,
	    _unslick = false,
	    _is_odd = _slick.slidesToShow % 2 == 0 ? 0 : 1,
	    _breakpoints = [];

	if ( typeof _slick == 'undefined' ) {
		return _breakpoints;
	}

	_unslick =_slides <= _slick.slidesToShow;
	_breakpoints.push( {
		breakpoint: 9999,
		settings: _unslick ? 'unslick' : {
			slidesToShow: _slick.slidesToShow,
			slidesToScroll: _slick.slidesToScroll
		}
	} );

	_toShow = Math.max( 1, ( Math.ceil( _slick.slidesToShow * 0.75 ) - _is_odd ) );
	_toScroll = Math.min( _slick.slidesToScroll, _toShow );
	_unslick =_slides <= _toShow;
	_breakpoints.push( {
		breakpoint: 993,
		settings: _unslick ? 'unslick' : {
			slidesToShow: _toShow,
			slidesToScroll: _toScroll
		}
	} );

	_toShow = Math.max( 1, ( Math.ceil( _slick.slidesToShow * 0.5 ) - _is_odd ) );
	_toScroll = Math.min( _slick.slidesToScroll, _toShow );
	_unslick =_slides <= _toShow;
	_breakpoints.push( {
		breakpoint: 768,
		settings: _unslick ? 'unslick' : {
			slidesToShow: _toShow,
			slidesToScroll: _toScroll
		}
	} );

	_toShow = Math.max( 1, ( Math.ceil( _slick.slidesToShow * 0.25 ) - _is_odd ) );
	_toScroll = Math.min( _slick.slidesToScroll, _toShow );
	_unslick =_slides <= _toShow;
	_breakpoints.push( {
		breakpoint: 480,
		settings: _unslick ? 'unslick' : {
			slidesToShow: _toShow,
			slidesToScroll: _toScroll
		}
	} );

	return _breakpoints;
};

_mpc_vars.rtl = {
	'el': function( _el ) {
		var _dir;

		_el = _el[ 0 ];

		if ( _el.currentStyle ) {
			_dir = _el.currentStyle[ 'direction' ];
		} else if ( window.getComputedStyle ) {
			_dir = window.getComputedStyle( _el, null ).getPropertyValue( 'direction' );
		}

		return _dir == 'rtl';
	},
	'global': function() {
		var _el = document.getElementsByTagName( 'html' )[ 0 ],
			_dir;

		if ( _el.currentStyle ) {
			_dir = _el.currentStyle[ 'direction' ];
		} else if ( window.getComputedStyle ) {
			_dir = window.getComputedStyle( _el, null ).getPropertyValue( 'direction' );
		}

		return _dir == 'rtl';
	}
};