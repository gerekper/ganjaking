( function ( a ) {
	a( document ).ready( function () {
		let b = '';
		a( '.ult-video-banner' ).each( function ( g, e ) {
			const k = a( e ).attr( 'id' );
			const h = a( e ).data( 'current-time' );
			const j = a( e ).data( 'placeholder' );
			let d = 0;
			a( e )
				.find( 'video' )
				.get( 0 )
				.addEventListener( 'canplay', function () {
					if ( d >= 1 ) {
						return false;
					}
					a( e ).find( 'video' ).get( 0 ).currentTime = h;
					a( e ).find( 'video' ).get( 0 ).pause();
					d++;
				} );
			const f = a( e )
				.find( '.ult-video-banner-overlay' )
				.data( 'overlay' );
			const c = a( e )
				.find( '.ult-video-banner-overlay' )
				.data( 'overlay-hover' );
			if ( f != '' ) {
				b +=
					'#' +
					k +
					' .ult-video-banner-overlay { background:' +
					f +
					' }';
			}
			if ( c != '' ) {
				b +=
					'#' +
					k +
					'.ult-vb-touch-start .ult-video-banner-overlay { background:' +
					c +
					' }';
			}
		} );
		if ( b != '' ) {
			a( 'head' ).append( '<style>' + b + '</style>' );
		}
		a( document ).on( 'mouseover', '.ult-video-banner', function () {
			a( this ).addClass( 'ult-vb-touch-start' );
			a( this ).find( 'video' ).get( 0 ).play();
		} );
		a( document ).on( 'mouseout', '.ult-video-banner', function () {
			a( this ).removeClass( 'ult-vb-touch-start' );
			a( this ).find( 'video' ).get( 0 ).pause();
		} );
		a( document ).on( 'touchstart', '.ult-video-banner', function () {
			if ( a( this ).hasClass( 'ult-vb-touch-start' ) ) {
				a( this ).removeClass( 'ult-vb-touch-start' );
				a( this ).find( 'video' ).get( 0 ).pause();
				return false;
			}
			a( this ).addClass( 'ult-vb-touch-start' );
			a( this ).find( 'video' ).get( 0 ).play();
		} );
	} );
} )( jQuery );
