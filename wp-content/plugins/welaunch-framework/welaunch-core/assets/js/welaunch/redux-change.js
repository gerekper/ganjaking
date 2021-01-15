/* jshint unused:false */
/* global welaunch */

var confirmOnPageExit = function( e ) {

	// Return; // ONLY FOR DEBUGGING.
	// If we haven't been passed the event get the window.event.
	'use strict';

	var message;

	e = e || window.event;

	message = welaunch.optName.args.save_pending;

	// For IE6-8 and Firefox prior to version 4.
	if ( e ) {
		e.returnValue = message;
	}

	window.onbeforeunload = null;

	// For Chrome, Safari, IE8+ and Opera 12+.
	return message;
};

function welaunch_change( variable ) {
	'use strict';

	(function( $ ) {
		var rContainer;
		var opt_name;
		var parentID;
		var id;
		var th;
		var subParent;
		var errorCount;
		var errorsLeft;
		var warningCount;
		var warningsLeft;

		variable = $( variable );

		rContainer = $( variable ).parents( '.welaunch-container:first' );

		if ( welaunch.customizer ) {
			opt_name = $( '.welaunch-customizer-opt-name' ).data( 'opt-name' );
		} else {
			opt_name = $.welaunch.getOptName( rContainer );
		}

		$( 'body' ).trigger( 'check_dependencies', variable );

		if ( variable.hasClass( 'compiler' ) ) {
			$( '#welaunch-compiler-hook' ).val( 1 );
		}

		parentID = $( variable ).closest( '.welaunch-group-tab' ).attr( 'id' );

		// Let's count down the errors now. Fancy.  ;).
		id = parentID.split( '_' );

		id = id[0];

		th        = rContainer.find( '.welaunch-group-tab-link-a[data-key="' + id + '"]' ).parents( '.welaunch-group-tab-link-li:first' );
		subParent = $( '#' + parentID + '_li' ).parents( '.hasSubSections:first' );

		if ( $( variable ).parents( 'fieldset.welaunch-field:first' ).hasClass( 'welaunch-field-error' ) ) {
			$( variable ).parents( 'fieldset.welaunch-field:first' ).removeClass( 'welaunch-field-error' );
			$( variable ).parent().find( '.welaunch-th-error' ).slideUp();

			errorCount = ( parseInt( rContainer.find( '.welaunch-field-errors span' ).text(), 0 ) - 1 );

			if ( errorCount <= 0 ) {
				$( '#' + parentID + '_li .welaunch-menu-error' ).fadeOut( 'fast' ).remove();
				$( '#' + parentID + '_li .welaunch-group-tab-link-a' ).removeClass( 'hasError' );
				$( '#' + parentID + '_li' ).parents( '.inside:first' ).find( '.welaunch-field-errors' ).slideUp();
				$( variable ).parents( '.welaunch-container:first' ).find( '.welaunch-field-errors' ).slideUp();
				$( '#welaunch_metaboxes_errors' ).slideUp();
			} else {
				errorsLeft = ( parseInt( th.find( '.welaunch-menu-error:first' ).text(), 0 ) - 1 );

				if ( errorsLeft <= 0 ) {
					th.find( '.welaunch-menu-error:first' ).fadeOut().remove();
				} else {
					th.find( '.welaunch-menu-error:first' ).text( errorsLeft );
				}

				rContainer.find( '.welaunch-field-errors span' ).text( errorCount );
			}

			if ( 0 !== subParent.length ) {
				if ( 0 === subParent.find( '.welaunch-menu-error' ).length ) {
					subParent.find( '.hasError' ).removeClass( 'hasError' );
				}
			}
		}

		if ( $( variable ).parents( 'fieldset.welaunch-field:first' ).hasClass( 'welaunch-field-warning' ) ) {
			$( variable ).parents( 'fieldset.welaunch-field:first' ).removeClass( 'welaunch-field-warning' );
			$( variable ).parent().find( '.welaunch-th-warning' ).slideUp();

			warningCount = ( parseInt( rContainer.find( '.welaunch-field-warnings span' ).text(), 0 ) - 1 );

			if ( warningCount <= 0 ) {
				$( '#' + parentID + '_li .welaunch-menu-warning' ).fadeOut( 'fast' ).remove();
				$( '#' + parentID + '_li .welaunch-group-tab-link-a' ).removeClass( 'hasWarning' );
				$( '#' + parentID + '_li' ).parents( '.inside:first' ).find( '.welaunch-field-warnings' ).slideUp();
				$( variable ).parents( '.welaunch-container:first' ).find( '.welaunch-field-warnings' ).slideUp();
				$( '#welaunch_metaboxes_warnings' ).slideUp();
			} else {

				// Let's count down the warnings now. Fancy.  ;).
				warningsLeft = ( parseInt( th.find( '.welaunch-menu-warning:first' ).text(), 0 ) - 1 );

				if ( warningsLeft <= 0 ) {
					th.find( '.welaunch-menu-warning:first' ).fadeOut().remove();
				} else {
					th.find( '.welaunch-menu-warning:first' ).text( warningsLeft );
				}

				rContainer.find( '.welaunch-field-warning span' ).text( warningCount );
			}

			if ( 0 !== subParent.length ) {
				if ( 0 === subParent.find( '.welaunch-menu-warning' ).length ) {
					subParent.find( '.hasWarning' ).removeClass( 'hasWarning' );
				}
			}
		}

		// Don't show the changed value notice while save_notice is visible.
		if ( rContainer.find( '.saved_notice:visible' ).length > 0 ) {
			return;
		}

		if ( ! welaunch.optName.args.disable_save_warn ) {
			rContainer.find( '.welaunch-save-warn' ).slideDown();
			window.onbeforeunload = confirmOnPageExit;
		}
	})( jQuery );
}
