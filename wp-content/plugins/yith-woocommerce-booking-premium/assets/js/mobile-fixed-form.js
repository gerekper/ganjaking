/* global jQuery */
( function ( $ ) {
	"use strict";

	var form = $( '.yith-wcbk-mobile-fixed-form' ).first();

	if ( form.length > 0 ) {
		var mouseTrap      = $( '.yith-wcbk-mobile-fixed-form__mouse-trap' ),
			closeBtn       = $( '.yith-wcbk-mobile-fixed-form__close' ),
			body           = $( 'body' ),
			isInFooter     = false,
			friends        = {
				parent: form.parent(),
				next  : form.next(),
				prev  : form.prev()
			},
			open           = function () {
				show_overlay();
				form.addClass( 'is-open' );
			},
			close          = function () {
				form.removeClass( 'is-open' );
				hide_overlay();
			},
			show_overlay   = function () {
				var overlay = $( '.yith-wcbk-mobile-fixed-form__overlay' );
				if ( overlay.length < 1 ) {
					overlay = $( '<div class="yith-wcbk-mobile-fixed-form__overlay"></div>' );
					$( 'body' ).append( overlay );
				}

				overlay.show();
			},
			hide_overlay   = function () {
				$( '.yith-wcbk-mobile-fixed-form__overlay' ).hide();
			},
			handlePosition = function () {
				if ( 'fixed' === form.css( 'position' ) ) {
					if ( !isInFooter ) {
						isInFooter = true;
						body.append( form );
					}
				} else {
					if ( isInFooter ) {
						isInFooter = false;
						if ( friends.prev.length ) {
							friends.prev.after( form );
						} else if ( friends.next.length ) {
							friends.next.before( form );
						} else {
							friends.parent.append( form );
						}
					}
				}
			};

		$( document ).on( 'click', '.yith-wcbk-mobile-fixed-form__overlay', close );

		mouseTrap.on( 'click', open );
		closeBtn.on( 'click', close );

		/**
		 * Move the widget to the footer in mobile to avoid issues with theme and z-index.
		 * This can be disabled through 'yith_wcbk_product_form_widget_mobile_move_to_footer' filter.
		 *
		 * @since 3.0.1
		 */
		if ( form.hasClass( 'move-to-footer-in-mobile' ) ) {
			handlePosition();
			$( window ).on( 'resize', handlePosition );
		}
	}

} )( jQuery );
