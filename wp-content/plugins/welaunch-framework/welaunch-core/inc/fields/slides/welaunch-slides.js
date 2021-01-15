/*global welaunch_change, welaunch*/

(function( $ ) {
	'use strict';

	welaunch.field_objects        = welaunch.field_objects || {};
	welaunch.field_objects.slides = welaunch.field_objects.slides || {};

	welaunch.field_objects.slides.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'slides' );

		$( selector ).each(
			function() {
				var el     = $( this );
				var parent = el;

				welaunch.field_objects.media.init( el );

				if ( ! el.hasClass( 'welaunch-field-container' ) ) {
					parent = el.parents( '.welaunch-field-container:first' );
				}

				if ( parent.is( ':hidden' ) ) {
					return;
				}

				if ( parent.hasClass( 'welaunch-container-slides' ) ) {
					parent.addClass( 'welaunch-field-init' );
				}

				if ( parent.hasClass( 'welaunch-field-init' ) ) {
					parent.removeClass( 'welaunch-field-init' );
				} else {
					return;
				}

				el.find( '.welaunch-slides-remove' ).on(
					'click',
					function() {
						var slideCount;
						var contentNewTitle;

						welaunch_change( $( this ) );

						$( this ).parent().siblings().find( 'input[type="text"]' ).val( '' );
						$( this ).parent().siblings().find( 'textarea' ).val( '' );
						$( this ).parent().siblings().find( 'input[type="hidden"]' ).val( '' );

						slideCount = $( this ).parents( '.welaunch-container-slides:first' ).find( '.welaunch-slides-accordion-group' ).length;

						if ( slideCount > 1 ) {
							$( this ).parents( '.welaunch-slides-accordion-group:first' ).slideUp(
								'medium',
								function() {
									$( this ).remove();
								}
							);
						} else {
							contentNewTitle = $( this ).parent( '.welaunch-slides-accordion' ).data( 'new-content-title' );

							$( this ).parents( '.welaunch-slides-accordion-group:first' ).find( '.remove-image' ).click();
							$( this ).parents( '.welaunch-container-slides:first' ).find( '.welaunch-slides-accordion-group:last' ).find( '.welaunch-slides-header' ).text( contentNewTitle );
						}
					}
				);

				el.find( '.welaunch-slides-add' ).off( 'click' ).click(
					function() {
						var contentNewTitle;

						var newSlide    = $( this ).prev().find( '.welaunch-slides-accordion-group:last' ).clone( true );
						var slideCount  = $( newSlide ).find( '.slide-title' ).attr( 'name' ).match( /[0-9]+(?!.*[0-9])/ );
						var slideCount1 = slideCount * 1 + 1;

						$( newSlide ).find( 'input[type="text"], input[type="hidden"], textarea' ).each(
							function() {
								$( this ).attr( 'name', jQuery( this ).attr( 'name' ).replace( /[0-9]+(?!.*[0-9])/, slideCount1 ) ).attr( 'id', $( this ).attr( 'id' ).replace( /[0-9]+(?!.*[0-9])/, slideCount1 ) );

								$( this ).val( '' );

								if ( $( this ).hasClass( 'slide-sort' ) ) {
									$( this ).val( slideCount1 );
								}
							}
						);

						contentNewTitle = $( this ).prev().data( 'new-content-title' );

						$( newSlide ).find( '.screenshot' ).removeAttr( 'style' );
						$( newSlide ).find( '.screenshot' ).addClass( 'hide' );
						$( newSlide ).find( '.screenshot a' ).attr( 'href', '' );
						$( newSlide ).find( '.remove-image' ).addClass( 'hide' );
						$( newSlide ).find( '.welaunch-slides-image' ).attr( 'src', '' ).removeAttr( 'id' );
						$( newSlide ).find( 'h3' ).text( '' ).append( '<span class="welaunch-slides-header">' + contentNewTitle + '</span><span class="ui-accordion-header-icon ui-icon ui-icon-plus"></span>' );
						$( this ).prev().append( newSlide );
					}
				);

				el.find( '.slide-title' ).keyup(
					function( event ) {
						var newTitle = event.target.value;
						$( this ).parents().eq( 3 ).find( '.welaunch-slides-header' ).text( newTitle );
					}
				);

				el.find( '.welaunch-slides-accordion' ).accordion(
					{
						header: '> div > fieldset > h3',
						collapsible: true,
						active: false,
						heightStyle: 'content',
						icons: {
							'header': 'ui-icon-plus',
							'activeHeader': 'ui-icon-minus'
						}
					}
				).sortable(
					{
						axis: 'y',
						handle: 'h3',
						connectWith: '.welaunch-slides-accordion',
						start: function( e, ui ) {
							e = null;
							ui.placeholder.height( ui.item.height() );
							ui.placeholder.width( ui.item.width() );
						},
						placeholder: 'ui-state-highlight',
						stop: function( event, ui ) {
							var inputs;

							event = null;

							// IE doesn't register the blur when sorting
							// so trigger focusout handlers to remove .ui-state-focus.
							ui.item.children( 'h3' ).triggerHandler( 'focusout' );
							inputs = $( 'input.slide-sort' );
							inputs.each(
								function( idx ) {
									$( this ).val( idx );
								}
							);
						}
					}
				);
			}
		);
	};
})( jQuery );
