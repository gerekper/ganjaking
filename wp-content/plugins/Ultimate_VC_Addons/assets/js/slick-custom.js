( function ( $ ) {
	$( document ).ready( function () {
		$( '.ult-carousel-wrapper' ).each( function () {
			const $this = $( this );
			if ( $this.hasClass( 'ult_full_width' ) ) {
				$this.css( 'left', 0 );
				$this.css( 'right', 0 );
				const rtl = $this.attr( 'data-rtl' );
				const w = $( 'html' ).outerWidth();
				const al = 0;
				const bl = $this.offset().left;
				const xl = Math.abs( al - bl );
				const left = xl;
				if ( rtl === 'true' || rtl === true )
					$this.css( {
						position: 'relative',
						right: '-' + left + 'px',
						width: w + 'px',
					} );
				else
					$this.css( {
						position: 'relative',
						left: '-' + left + 'px',
						width: w + 'px',
					} );
			}
		} );
		$( '.ult-carousel-wrapper' ).each( function ( i, carousel ) {
			const gutter = $( carousel ).data( 'gutter' );
			const id = $( carousel ).attr( 'id' );
			if ( gutter != '' ) {
				const css =
					'<style>#' +
					id +
					' .slick-slide { margin:0 ' +
					gutter +
					'px; } </style>';
				$( 'head' ).append( css );
			}
		} );

		$( '.ult-carousel-wrapper' ).on( 'init', function ( event ) {
			event.preventDefault();

			$( '.ult-carousel-wrapper .ult-item-wrap.slick-active' ).each(
				function ( index, el ) {
					$this = $( this );
					$this.addClass( $this.data( 'animation' ) );
				}
			);
		} );

		$( '.ult-carousel-wrapper' ).on(
			'beforeChange',
			function ( event, slick, currentSlide ) {
				$inViewPort = $( "[data-slick-index='" + currentSlide + "']" );
				$inViewPort
					.siblings()
					.removeClass( $inViewPort.data( 'animation' ) );
			}
		);

		$( '.ult-carousel-wrapper' ).on(
			'afterChange',
			function ( event, slick, currentSlide, nextSlide ) {
				slidesScrolled = slick.options.slidesToScroll;
				slidesToShow = slick.options.slidesToShow;
				centerMode = slick.options.centerMode;
				windowWidth = jQuery( window ).width();
				if ( windowWidth < 1025 ) {
					slidesToShow =
						slick.options.responsive[ 0 ].settings.slidesToShow;
				}
				if ( windowWidth < 769 ) {
					slidesToShow =
						slick.options.responsive[ 1 ].settings.slidesToShow;
				}
				if ( windowWidth < 481 ) {
					slidesToShow =
						slick.options.responsive[ 2 ].settings.slidesToShow;
				}

				$currentParent = slick.$slider[ 0 ].parentElement.id;

				slideToAnimate = currentSlide + slidesToShow - 1;

				if ( slidesScrolled == 1 ) {
					if ( centerMode == true ) {
						animate = slideToAnimate - 2;
						$inViewPort = $(
							'#' +
								$currentParent +
								" [data-slick-index='" +
								animate +
								"']"
						);
						$inViewPort.addClass( $inViewPort.data( 'animation' ) );
					} else {
						$inViewPort = $(
							'#' +
								$currentParent +
								" [data-slick-index='" +
								slideToAnimate +
								"']"
						);
						$inViewPort.addClass( $inViewPort.data( 'animation' ) );
					}
				} else {
					for ( let i = slidesScrolled + currentSlide; i >= 0; i-- ) {
						$inViewPort = $(
							'#' +
								$currentParent +
								" [data-slick-index='" +
								i +
								"']"
						);
						$inViewPort.addClass( $inViewPort.data( 'animation' ) );
					}
				}
			}
		);

		$( window ).resize( function () {
			$( '.ult-carousel-wrapper' ).each( function () {
				const $this = $( this );
				if ( $this.hasClass( 'ult_full_width' ) ) {
					const rtl = $this.attr( 'data-rtl' );
					$this.removeAttr( 'style' );
					const w = $( 'html' ).outerWidth();
					const al = 0;
					const bl = $this.offset().left;
					const xl = Math.abs( al - bl );
					const left = xl;
					if ( rtl === 'true' || rtl === true )
						$this.css( {
							position: 'relative',
							right: '-' + left + 'px',
							width: w + 'px',
						} );
					else
						$this.css( {
							position: 'relative',
							left: '-' + left + 'px',
							width: w + 'px',
						} );
				}
			} );
		} );
	} );
	$( window ).on( 'load', function () {
		$( '.ult-carousel-wrapper' ).each( function () {
			const $this = $( this );
			if ( $this.hasClass( 'ult_full_width' ) ) {
				$this.css( 'left', 0 );
				$this.css( 'right', 0 );
				const al = 0;
				const bl = $this.offset().left;
				const xl = Math.abs( al - bl );
				const rtl = $this.attr( 'data-rtl' );
				const w = $( 'html' ).outerWidth();
				const left = xl;
				if ( rtl === 'true' || rtl === true )
					$this.css( {
						position: 'relative',
						right: '-' + left + 'px',
						width: w + 'px',
					} );
				else
					$this.css( {
						position: 'relative',
						left: '-' + left + 'px',
						width: w + 'px',
					} );
			}
		} );
	} );
	jQuery( document ).on(
		'ultAdvancedTabClickedCarousel',
		function ( event, nav ) {
			$( nav )
				.find( '.ult-carousel-wrapper' )
				.each( function () {
					const $this = $( this );
					if ( $this.hasClass( 'ult_full_width' ) ) {
						$this.css( 'left', 0 );
						$this.css( 'right', 0 );
						const al = 0;
						const bl = $this.offset().left;
						const xl = Math.abs( al - bl );
						const rtl = $this.attr( 'data-rtl' );
						const w = $( 'html' ).outerWidth();
						const left = xl;
						if ( rtl === 'true' || rtl === true )
							$this.css( {
								position: 'relative',
								right: '-' + left + 'px',
								width: w + 'px',
							} );
						else
							$this.css( {
								position: 'relative',
								left: '-' + left + 'px',
								width: w + 'px',
							} );
					}
				} );
		}
	);
} )( jQuery );
