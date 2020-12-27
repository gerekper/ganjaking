/*----------------------------------------------------------------------------*\
	TABS SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function switch_tab( $this ) {
		var $tab_container = $this.closest( '.mpc-tabs' ),
		 	$tab           = $tab_container.find( '#' + $this.attr( 'data-tab_id' ) ),
		    $tabs          = $tab.siblings( '.mpc-tab' );

		$this.siblings().removeClass( 'mpc-active' );
		$this.addClass( 'mpc-active' );

		$tabs.attr( 'data-active', false );
		$tab.attr( 'data-active', true );

		if( $tab.find( '.mpc-parent--init' ).length ) {
			$tab.trigger( 'mpc.parent-init' );
			$tab.find( '.mpc-parent--init' ).removeClass( 'mpc-parent--init' );
		}
	}

	function responsive( $tabs ) {
		var _is_12_col = $tabs.parents( '.mpc-column' ).is( '.vc_col-sm-12' );

		if(  _mpc_vars.breakpoints.custom( '(max-width: 767px)' ) || ( _mpc_vars.breakpoints.medium && !_is_12_col ) ) {
			if( $tabs.is( '.mpc-tabs--left' ) || $tabs.is( '.mpc-tabs--right' ) ) {
				var _class = $tabs.is( '.mpc-tabs--left' ) ? 'left' : 'right';

				$tabs
					.attr( 'data-nav-position', _class )
					.removeClass( 'mpc-tabs--left mpc-tabs--right' )
					.addClass( 'mpc-tabs--top' );
			}
		} else if( $tabs.attr( 'data-nav-position' ) == 'left' || $tabs.attr( 'data-nav-position' ) == 'right' ) {
			var _position = $tabs.attr( 'data-nav-position' );

			$tabs
				.removeClass( 'mpc-tabs--top' )
				.addClass( 'mpc-tabs--' + _position )
				.removeAttr( 'data-nav-position' );
		}
	}

	function init_shortcode( $tabs ) {
		var $tabs_nav = $tabs.find( '.mpc-tabs__nav-item' ),
			_hash_url = window.location.hash;

		$tabs_nav.on( 'click', function() {
			switch_tab( $( this ) );
		} );

		if ( _hash_url !== undefined ) {
			use_hash( $tabs, _hash_url );
		}

		var $tab = $tabs.find( '.mpc-tab[data-active="true"]' );
		if ( $tab.length ) {
			if ( $tab.find( '.mpc-parent--init' ).length ) {
				$tab.trigger( 'mpc.parent-init' );
				$tab.find( '.mpc-parent--init' ).removeClass( 'mpc-parent--init' );
			}
		}

		responsive( $tabs );

		$tabs.trigger( 'mpc.inited' );
	}

	function use_hash( $tabs, _hash_url ) {
		var _open_tab		    = '#open-tab_',
			_open_tab_indicator = _hash_url.indexOf( _open_tab );

		if (  _open_tab_indicator < 0 ) {
			return false;
		}

		var _possible_tab_id = _hash_url.substr( _open_tab.length ),
			$possible_tab    = $tabs.find( 'li[data-tab_id="' + _possible_tab_id + '"]');

		if ( $possible_tab.length ) {

			$possible_tab.each( function() {
				switch_tab( $( this ) );
			} );
		}
	}

	var $tabs = $( '.mpc-tabs' );

	$tabs.each( function() {
		var $tab = $( this );

		$tab.one( 'mpc.init', function () {
			init_shortcode( $tab );
		} );
	} );

	_mpc_vars.$window.on( 'mpc.resize', function() {
		$.each( $tabs, function() {
			responsive( $( this ) );
		} );
	} );

} )( jQuery );