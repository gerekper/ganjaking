( function ( $ ) {
	$( document ).on( 'yith-wcbk-init-fields:services-selector', function () {
		$( '.yith-wcbk-services-selector:not(.yith-wcbk-services-selector--initialized)' ).each(
			function () {
				var serviceSelector = $( this ),
					placeholder     = serviceSelector.data( 'placeholder' ),
					dom             = {
						toggle   : serviceSelector.find( '.yith-wcbk-services-selector__toggle-handler' ),
						label    : serviceSelector.find( '.yith-wcbk-services-selector__label' ),
						labelFake: serviceSelector.find( '.yith-wcbk-services-selector__label__fake' ),
						content  : serviceSelector.find( '.yith-wcbk-services-selector__content' ),
						close    : serviceSelector.find( '.yith-wcbk-services-selector__close' ),
						services : serviceSelector.find( '.yith-wcbk-services-selector__service' )
					},
					updateLabelSize = function () {
						dom.label.css( { width: dom.labelFake.outerWidth() } );
					},
					isOpened        = function () {
						return serviceSelector.hasClass( 'yith-wcbk-services-selector--opened' );
					},
					handleToggle    = function () {
						if ( isOpened() ) {
							handleClose();
						} else {
							handleOpen();
						}
					},
					handleOpen      = function () {
						serviceSelector.addClass( 'yith-wcbk-services-selector--opened' );
						var contentRect = dom.content.get(0).getBoundingClientRect();

						if ( window.innerHeight < contentRect.y + contentRect.height ) {
							serviceSelector.addClass( 'yith-wcbk-services-selector--opened--above' );
							serviceSelector.removeClass( 'yith-wcbk-services-selector--opened--below' );
						} else {
							serviceSelector.addClass( 'yith-wcbk-services-selector--opened--below' );
							serviceSelector.removeClass( 'yith-wcbk-services-selector--opened--above' );
						}
					},
					handleClose     = function () {
						serviceSelector.removeClass( 'yith-wcbk-services-selector--opened' );
						serviceSelector.removeClass( 'yith-wcbk-services-selector--opened--below' );
						serviceSelector.removeClass( 'yith-wcbk-services-selector--opened--above' );
					},
					handleChange    = function () {
						var selected = serviceSelector.find( '.yith-wcbk-services-selector__service:checked' ).siblings( '.yith-wcbk-checkbox__label' );
						if ( selected.length ) {
							dom.label.html(
								selected.get().map( function ( single ) {
									return '<span class="item">' + single.innerHTML + '</span>';
								} ).join( ' ' )
							);
							dom.label.addClass( 'yith-wcbk-services-selector__label--selected' );
							dom.label.removeClass( 'yith-wcbk-services-selector__label--placeholder' );
						} else {
							dom.label.html( placeholder );
							dom.label.removeClass( 'yith-wcbk-services-selector__label--selected' );
							dom.label.addClass( 'yith-wcbk-services-selector__label--placeholder' );
						}
					},
					handleClick     = function ( e ) {
						if ( dom.close.get( 0 ).contains( e.target ) ) {
							e.preventDefault();
							handleClose();
						} else if ( dom.content.get( 0 ).contains( e.target ) ) {
							// do nothing.
						} else if ( dom.toggle.get( 0 ).contains( e.target ) ) {
							e.preventDefault();
							handleToggle();
						} else {
							handleClose();
						}
					};

				// Use Event Listener directly, to avoid bubbling (and issues with e.stopPropagation).
				document.addEventListener( 'click', handleClick, true );

				dom.services.on( 'change', handleChange );

				updateLabelSize();
				$( window ).on( 'resize', updateLabelSize );

				serviceSelector.addClass( 'yith-wcbk-services-selector--initialized' );
			}
		);
	} ).trigger( 'yith-wcbk-init-fields:services-selector' );

} )( jQuery );