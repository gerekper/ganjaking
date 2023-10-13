/* global jQuery, yith_booking */
( function ( $ ) {

	var DEFAULT_CONTENT_SHOWN_CLASS = 'yith-wcbk-email-field__row--empty-value',
		INITIAL_TEXT_EDITOR_HEIGHT  = 300;

	function getFormDataObject( formElement ) {
		var formData = new FormData( formElement ),
			data     = {};

		for ( let key of formData.keys() ) {
			var isArray = key.endsWith( '[]' );
			var dataKey = isArray ? key.substring( 0, key.length - 2 ) : key;
			var value   = isArray ? formData.getAll( key ) : formData.get( key );

			data[ dataKey ] = value;
		}

		return data;
	}

	function syncEditors() {
		if ( 'tinyMCE' in window && 'triggerSave' in window.tinyMCE ) {
			window.tinyMCE.triggerSave();
		}
	}

	$( document ).on( 'change', '.yith-wcbk-emails__email__toggle-active .on_off', function () {
		var checkbox     = $( this ),
			toggle       = checkbox.closest( '.yith-wcbk-emails__email__toggle-active' ),
			emailWrapper = checkbox.closest( '.yith-wcbk-emails__email' ),
			emailKey     = emailWrapper.data( 'email' ),
			data         = {
				email  : emailKey,
				request: 'switch_email_activation',
				enabled: checkbox.is( ':checked' ) ? 'yes' : 'no'
			};

		yith_booking.adminAjax(
			data,
			{ block: toggle }
		);
	} );

	$( document ).on( 'click', '.yith-wcbk-emails__email__toggle-editing', function ( e ) {
		e.preventDefault();
		var target    = $( this ),
			email     = target.closest( '.yith-wcbk-emails__email' ),
			options   = email.find( '.yith-wcbk-emails__email__options' ),
			textAreas = email.find( 'textarea.wp-editor-area' );

		if ( textAreas.length ) {
			textAreas.each( function () {
				var id = $( this ).attr( 'id' );
				if ( 'tinymce' in window ) {
					var editor = window.tinymce.get( id );
					if ( editor ) {
						editor.theme.resizeTo( undefined, INITIAL_TEXT_EDITOR_HEIGHT );
					}
				}
			} );
		}

		if ( email.is( '.yith-wcbk-emails__email--open' ) ) {
			email.removeClass( 'yith-wcbk-emails__email--open' );
			options.slideUp();
		} else {
			email.addClass( 'yith-wcbk-emails__email--open' );
			options.slideDown();
		}
	} );

	var saveTimeout = false;
	$( document ).on( 'click', '.yith-wcbk-emails__email__save', function () {

		syncEditors();

		var target            = $( this ),
			emailWrapper      = target.closest( '.yith-wcbk-emails__email' ),
			emailKey          = emailWrapper.data( 'email' ),
			optionsForm       = emailWrapper.find( 'form' ),
			buttonTextElement = target.find( '.yith-wcbk-emails__email__save__text' ),
			saveMessage       = target.data( 'save-message' ),
			savedMessage      = target.data( 'saved-message' ),
			data              = {
				email  : emailKey,
				request: 'update_email_options',
				data   : getFormDataObject( optionsForm.get( 0 ) )
			},
			setSaved          = function ( saved ) {
				if ( saved ) {
					target.addClass( 'is-saved' );
					buttonTextElement.html( savedMessage );
				} else {
					target.removeClass( 'is-saved' );
					buttonTextElement.html( saveMessage );
				}
			};

		setSaved( false );
		if ( saveTimeout ) {
			clearTimeout( saveTimeout );
		}


		yith_booking.adminAjax(
			data,
			{ block: target }
		).done( function () {
			setSaved( true );
			saveTimeout = setTimeout( function () {
				setSaved( false );
			}, 1000 );
		} );
	} );

	$( document ).on( 'click', '.yith-wcbk-email-field__edit', function ( e ) {
		e.preventDefault();
		var row            = $( this ).closest( '.yith-wcbk-email-field__row' ),
			defaultContent = row.data( 'default-content' ),
			textArea       = row.find( 'textarea.wp-editor-area' ),
			id             = textArea.attr( 'id' );

		if ( 'tinymce' in window ) {
			var editor = window.tinymce.get( id );
			if ( editor ) {
				editor.theme.resizeTo( undefined, INITIAL_TEXT_EDITOR_HEIGHT );
				editor.setContent( defaultContent.replace( /\r?\n/g, '<br />' ) );
			} else {
				textArea.val( defaultContent );
			}
		}

		row.removeClass( DEFAULT_CONTENT_SHOWN_CLASS );
	} );

	$( document ).on( 'click', '.yith-wcbk-email-field__use-default', function () {
		var row      = $( this ).closest( '.yith-wcbk-email-field__row' ),
			textArea = row.find( 'textarea.wp-editor-area' ),
			id       = textArea.attr( 'id' );

		if ( 'tinymce' in window ) {
			var editor = window.tinymce.get( id );
			if ( editor ) {
				editor.setContent( '' );
			} else {
				textArea.val( '' );
			}
		}

		row.addClass( DEFAULT_CONTENT_SHOWN_CLASS );
	} );

} )( jQuery );