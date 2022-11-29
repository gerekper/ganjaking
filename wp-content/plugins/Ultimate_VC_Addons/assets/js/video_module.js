( function ( $ ) {
	$( document ).ready( function ( e ) {
		const id = $( '.ult-video' )
			.map( function () {
				return $( this ).attr( 'id' );
			} )
			.get();
		const id1 = $( '.ultv-video__outer-wrap' )
			.map( function () {
				return $( this ).attr( 'data-iconbg' );
			} )
			.get();
		const id2 = $( '.ultv-video__outer-wrap' )
			.map( function () {
				return $( this ).attr( 'data-overcolor' );
			} )
			.get();
		const id3 = $( '.ultv-video__outer-wrap' )
			.map( function () {
				return $( this ).attr( 'data-defaultbg' );
			} )
			.get();
		const play = $( '.ultv-video__outer-wrap' )
			.map( function () {
				return $( this ).attr( 'data-defaultplay' );
			} )
			.get();
		const video = $( '.ultv-video' )
			.map( function () {
				return $( this ).attr( 'data-videotype' );
			} )
			.get();

		for ( let i = id.length - 1; i >= 0; i-- ) {
			$( '#' + id[ i ] + ' .ultv-video' )
				.find( ' .ultv-video__outer-wrap' )
				.css( 'color', id1[ i ] );
			$( '#' + id[ i ] + ' .ultv-video' )
				.find( ' .ultv-youtube-icon-bg' )
				.css( { fill: id3[ i ] } );
			$( '#' + id[ i ] + ' .ultv-video' )
				.find( ' .ultv-vimeo-icon-bg' )
				.css( { fill: id3[ i ] } );
			const styleElem = document.head.appendChild(
				document.createElement( 'style' )
			);
			styleElem.innerHTML =
				'#' +
				id[ i ] +
				' .ultv-video .ultv-video__outer-wrap:before {background: ' +
				id2[ i ] +
				';}';
		}
		for ( let j = 0; j <= play.length - 1; j++ ) {
			if ( 'icon' == play[ j ] ) {
				$( '.ultv-video' )
					.find( ' .ultv-video__outer-wrap' )
					.hover(
						function () {
							const $this = $( this );
							$this.css( 'color', $this.data( 'hoverbg' ) );
						},
						function () {
							const $this = $( this );
							$this.css( 'color', $this.data( 'iconbg' ) );
						}
					);
			} else if ( 'defaulticon' == play[ j ] ) {
				if ( 'uv_iframe' == video[ j ] ) {
					$( '.ultv-video' )
						.find( ' .ultv-video__outer-wrap' )
						.hover(
							function () {
								const $this = $( this );
								$this.find( ' .ultv-youtube-icon-bg' ).css( {
									fill: $this.data( 'defaulthoverbg' ),
								} );
							},
							function () {
								const $this = $( this );
								$this
									.find( ' .ultv-youtube-icon-bg' )
									.css( { fill: $this.data( 'defaultbg' ) } );
							}
						);
				} else if ( 'vimeo_video' == video[ j ] ) {
					$( '.ultv-video' )
						.find( ' .ultv-video__outer-wrap' )
						.hover(
							function () {
								const $this = $( this );
								$this.find( ' .ultv-vimeo-icon-bg' ).css( {
									fill: $this.data( 'defaulthoverbg' ),
								} );
							},
							function () {
								const $this = $( this );
								$this
									.find( ' .ultv-vimeo-icon-bg' )
									.css( { fill: $this.data( 'defaultbg' ) } );
							}
						);
				}
			}
		}
		ultvideo();
		$( window ).resize( function ( e ) {
			ultvideo();
		} );
	} );
	function ultvideo() {
		$( '.ult-video' ).each( function () {
			this.nodeClass = '.' + $( this ).attr( 'id' );
			const outer_wrap = jQuery( this.nodeClass ).find(
				'.ultv-video__outer-wrap'
			);

			outer_wrap.off( 'click' ).on( 'click', function ( e ) {
				const selector = $( this ).find( '.ultv-video__play' );
				ultvideo_play( selector );
			} );
			if (
				'1' == outer_wrap.data( 'autoplay' ) ||
				true == outer_wrap.data( 'device' )
			) {
				ultvideo_play(
					jQuery( this.nodeClass ).find( '.ultv-video__play' )
				);
			}
		} );
	}
	function ultvideo_play( selector ) {
		const iframe = $( '<iframe/>' );
		const vurl = selector.data( 'src' );
		if ( 0 == selector.find( 'iframe' ).length ) {
			iframe.attr( 'src', vurl );
			iframe.attr( 'frameborder', '0' );
			iframe.attr( 'allowfullscreen', '1' );
			iframe.attr( 'allow', 'autoplay;encrypted-media;' );

			selector.html( iframe );
		}
		selector
			.closest( '.ultv-video__outer-wrap' )
			.find( '.ultv-vimeo-wrap' )
			.hide();
	}
} )( jQuery );
