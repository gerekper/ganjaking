/* global welaunch */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.sanitize = function() {
		if ( welaunch.optName.sanitize && welaunch.optName.sanitize.sanitize ) {
			$.each(
				welaunch.optName.sanitize.sanitize,
				function( sectionID, sectionArray ) {
					sectionID = null;
					$.each(
						sectionArray.sanitize,
						function( key, value ) {
							$.welaunch.fixInput( key, value );
						}
					);
				}
			);
		}
	};

	$.welaunch.fixInput = function( key, value ) {
		var val;
		var input;
		var inputVal;
		var ul;
		var li;

		if ( 'multi_text' === value.type ) {
			ul = $( '#' + value.id + '-ul' );
			li = $( ul.find( 'li' ) );

			li.each(
				function() {
					input    = $( this ).find( 'input' );
					inputVal = input.val();

					if ( inputVal === value.old ) {
						input.val( value.current );
					}
				}
			);

			return;
		}

		input = $( 'input#' + value.id + '-' + key );

		if ( 0 === input.length ) {
			input = $( 'input#' + value.id );
		}

		if ( 0 === input.length ) {
			input = $( 'textarea#' + value.id + '-textarea' );
		}

		if ( input.length > 0 ) {
			val = '' === value.current ? value.default : value.current;

			$( input ).val( val );
		}
	};

	$.welaunch.notices = function() {
		if ( welaunch.optName.errors && welaunch.optName.errors.errors ) {
			$.each(
				welaunch.optName.errors.errors,
				function( sectionID, sectionArray ) {
					sectionID = null;
					$.each(
						sectionArray.errors,
						function( key, value ) {
							$( '#' + welaunch.optName.args.opt_name + '-' + value.id ).addClass( 'welaunch-field-error' );
							if ( 0 === $( '#' + welaunch.optName.args.opt_name + '-' + value.id ).parent().find( '.welaunch-th-error' ).length ) {
								$( '#' + welaunch.optName.args.opt_name + '-' + value.id ).append( '<div class="welaunch-th-error">' + value.msg + '</div>' );
							} else {
								$( '#' + welaunch.optName.args.opt_name + '-' + value.id ).parent().find( '.welaunch-th-error' ).html( value.msg ).css( 'display', 'block' );
							}

							$.welaunch.fixInput( key, value );
						}
					);
				}
			);

			$( '.welaunch-container' ).each(
				function() {
					var totalErrors;

					var container = $( this );

					// Ajax cleanup.
					container.find( '.welaunch-menu-error' ).remove();

					totalErrors = container.find( '.welaunch-field-error' ).length;

					if ( totalErrors > 0 ) {
						container.find( '.welaunch-field-errors span' ).text( totalErrors );
						container.find( '.welaunch-field-errors' ).slideDown();
						container.find( '.welaunch-group-tab' ).each(
							function() {
								var sectionID;
								var subParent;

								var total = $( this ).find( '.welaunch-field-error' ).length;
								if ( total > 0 ) {
									sectionID = $( this ).attr( 'id' ).split( '_' );

									sectionID = sectionID[0];
									container.find( '.welaunch-group-tab-link-a[data-key="' + sectionID + '"]' ).prepend( '<span class="welaunch-menu-error">' + total + '</span>' );
									container.find( '.welaunch-group-tab-link-a[data-key="' + sectionID + '"]' ).addClass( 'hasError' );

									subParent = container.find( '.welaunch-group-tab-link-a[data-key="' + sectionID + '"]' ).parents( '.hasSubSections:first' );

									if ( subParent ) {
										subParent.find( '.welaunch-group-tab-link-a:first' ).addClass( 'hasError' );
									}
								}
							}
						);
					}
				}
			);
		}

		if ( welaunch.optName.warnings && welaunch.optName.warnings.warnings ) {
			$.each(
				welaunch.optName.warnings.warnings,
				function( sectionID, sectionArray ) {
					sectionID = null;
					$.each(
						sectionArray.warnings,
						function( key, value ) {
							$( '#' + welaunch.optName.args.opt_name + '-' + value.id ).addClass( 'welaunch-field-warning' );

							if ( 0 === $( '#' + welaunch.optName.args.opt_name + '-' + value.id ).parent().find( '.welaunch-th-warning' ).length ) {
								$( '#' + welaunch.optName.args.opt_name + '-' + value.id ).append( '<div class="welaunch-th-warning">' + value.msg + '</div>' );
							} else {
								$( '#' + welaunch.optName.args.opt_name + '-' + value.id ).parent().find( '.welaunch-th-warning' ).html( value.msg ).css( 'display', 'block' );
							}

							$.welaunch.fixInput( key, value );
						}
					);
				}
			);

			$( '.welaunch-container' ).each(
				function() {
					var sectionID;
					var subParent;
					var total;
					var totalWarnings;

					var container = $( this );

					// Ajax cleanup.
					container.find( '.welaunch-menu-warning' ).remove();

					totalWarnings = container.find( '.welaunch-field-warning' ).length;

					if ( totalWarnings > 0 ) {
						container.find( '.welaunch-field-warnings span' ).text( totalWarnings );
						container.find( '.welaunch-field-warnings' ).slideDown();
						container.find( '.welaunch-group-tab' ).each(
							function() {
								total = $( this ).find( '.welaunch-field-warning' ).length;

								if ( total > 0 ) {
									sectionID = $( this ).attr( 'id' ).split( '_' );

									sectionID = sectionID[0];
									container.find( '.welaunch-group-tab-link-a[data-key="' + sectionID + '"]' ).prepend( '<span class="welaunch-menu-warning">' + total + '</span>' );
									container.find( '.welaunch-group-tab-link-a[data-key="' + sectionID + '"]' ).addClass( 'hasWarning' );

									subParent = container.find( '.welaunch-group-tab-link-a[data-key="' + sectionID + '"]' ).parents( '.hasSubSections:first' );

									if ( subParent ) {
										subParent.find( '.welaunch-group-tab-link-a:first' ).addClass( 'hasWarning' );
									}
								}
							}
						);
					}
				}
			);
		}
	};
})( jQuery );
