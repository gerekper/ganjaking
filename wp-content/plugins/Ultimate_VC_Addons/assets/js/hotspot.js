( function ( $, window, undefined ) {
	$( document ).ready( function ( a ) {
		init_tooltip();
		setTimeout( function () {
			$( window ).load( function () {
				if ( $( '.ult_hotspot_container' ).length > 0 ) {
					const status =
						$( '.ult_hotspot_container' )
							.find( '.ult-hotspot-tooltip' )
							.attr( 'data-status' ) || 'hide';
					if ( status === 'show' ) {
						$( '.ult-tooltipstered' ).ulttooltipster( 'destroy' );
					}
					init_tooltip();
				}
			} );
		}, 700 );

		$( document ).ajaxComplete( function ( e, xhr, settings ) {
			init_tooltip();
		} );

		function init_tooltip() {
			let parama1 = '',
				parama2 = '';
			$( 'a[href="#"]' ).click( function ( event ) {
				event.preventDefault();
			} );

			a( '.ult_hotspot_container.ult-hotspot-tooltip-wrapper' ).each(
				function () {
					a( this );

					/*var f = a(this).data("width") || 320,*/
					const g = a( this ).data( 'opacity' ) || 0.5;

					a(
						".ult-hotspot-tooltip[data-link_style='tootip']",
						a( this )
					).each( function () {
						if ( $( this ).find( '.aio-icon-img' ).length > 0 ) {
							var iconHeight = $( this )
								.find( '.aio-icon-img' )
								.outerHeight( true );
							var iconWidth = $( this )
								.find( '.aio-icon-img' )
								.outerWidth( true );
						} else {
							var iconHeight = jQuery( this )
								.find( '.aio-icon' )
								.outerHeight( true );
							var iconWidth = jQuery( this )
								.find( '.aio-icon' )
								.outerWidth( true );
						}

						let y = Math.round( iconHeight / 2 );
						let x = Math.round( iconWidth / 2 );

						let h,
							d = a( this ).data( 'tooltipanimation' ),
							e = a( this ).data( 'trigger' ) || 'hover';

						const j = a( this ).data( 'arrowposition' ) || 'top';

						const ba = a( this ).data( 'bubble-arrow' );

						const ContentStyle = a( this ).data(
							'tooltip-content-style'
						); // || false;
						const BaseStyle = a( this ).data(
							'tooltip-base-style'
						); // || false;

						let k = a( this )
								.find( '.hotspot-tooltip-content' )
								.html(),
							l = 3,
							m = a( this ).data( 'tooltip-offsety' ) || 0;

						k = a( this )
							.find( '.hotspot-tooltip-content' )
							.html( k )
							.text();

						const custid = a( this ).data( 'mycust-id' );

						parama1 = a( this ).data( 'ultimate-target' );
						parama2 = a( this ).data( 'responsive-json-new' );
						parama2 = JSON.stringify( parama2 );

						if ( j == 'top' ) {
							y = 0;
						}
						if ( j == 'bottom' ) {
							y = iconHeight;
						}
						if ( j == 'left' ) {
							y = -y;
							x = 0;
						}
						if ( j == 'right' ) {
							x = iconWidth;
							y = -y;
						}

						if (
							/firefox/.test( navigator.userAgent.toLowerCase() )
						) {
							x = 0;
							y = 0;
						}

						a( this ).ulttooltipster( {
							content: k,
							position: j,
							offsetX: x, //l,
							offsetY: y, //0, //m,

							/*      Ultimate Options
							 *---------------------------*/
							ultCustomTooltipStyle: true,
							ultContentStyle: ContentStyle,
							ultBaseStyle: BaseStyle,
							//ultContainerWidth: r,
							//ultBaseWidth: f,
							//ultPadding: pd,

							//ultBaseBGColor: cbgc,//"rgb(8, 187, 252)",
							//ultBaseBorderColor: cbc,//"rgb(8, 187, 252)",

							//ultBaseColor: cc,

							//maxWidth: 300,
							arrow: ba,

							//  Bubble arrow
							//arrow: true,
							//customid: custid,
							delay: 100,
							speed: 300,
							interactive: ! 0,
							animation: d,
							trigger: e,
							/*positionTracker: true,*/
							/*contentAsHTML: 1,*/
							contentAsHTML: 1 /* ultimate */ /* ultimate */,
							//theme: 'tooltipster-default', //"tooltipster-" + c,
							/*minWidth: r,*/
							/*ultContainerWidth: f,*/ /*ultContentSize: r,*/ ult_adv_id: custid,
							responsive_json_new: parama2,
							ultimate_target: parama1,
						} );
					} );
				}
			);
		}
	} );
} )( jQuery, window );
