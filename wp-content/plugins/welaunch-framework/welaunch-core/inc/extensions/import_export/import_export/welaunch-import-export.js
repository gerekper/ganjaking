/* global jQuery, document, welaunch, ajaxurl */

(function( $ ) {
	'use strict';

	welaunch.field_objects = welaunch.field_objects || {};
	welaunch.field_objects.import_export = welaunch.field_objects.import_export || {};

	welaunch.field_objects.import_export.copy_text = function( $text ) {
		var copyFrom = document.createElement( 'textarea' );
		document.body.appendChild( copyFrom );
		copyFrom.textContent = $text;
		copyFrom.select();
		document.execCommand( 'copy' );
		copyFrom.remove();
	};

	welaunch.field_objects.import_export.get_options = function( $secret ) {
		var $el = $( '#welaunch-export-code-copy' );
		var url = ajaxurl + '?download=0&action=welaunch_download_options-' + welaunch.optName.args.opt_name + '&secret=' + $secret;
		$el.addClass( 'disabled' ).attr( 'disabled', 'disabled' );
		$el.text( $el.data( 'copy' ) );
		$.get( url, function( data ) {
			welaunch.field_objects.import_export.copy_text( data );
			$el.removeClass( 'disabled' );
			$el.text( $el.data( 'copied' ) );
			setTimeout( function() {
				$el.text( $el.data( 'copy' ) ).removeClass( 'disabled' ).removeAttr( 'disabled' );
			}, 2000 );
		} );
	};

	welaunch.field_objects.import_export.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'import_export' );

		$( selector ).each(
			function() {
				var textBox1;
				var textBox2;

				var el = $( this );
				var parent = el;

				if ( ! el.hasClass( 'welaunch-field-container' ) ) {
					parent = el.parents( '.welaunch-field-container:first' );
				}

				if ( parent.is( ':hidden' ) ) {
					return;
				}

				if ( parent.hasClass( 'welaunch-field-init' ) ) {
					parent.removeClass( 'welaunch-field-init' );
				} else {
					return;
				}

				el.each(
					function() {
						$( '#welaunch-import' ).click(
							function( e ) {
								if ( '' === $( '#import-code-value' ).val() && '' === $(
									'#import-link-value' ).val() ) {
									e.preventDefault();
									return false;
								}
							}
						);

						$( this ).find( '#welaunch-import-code-button' ).click(
							function() {
								var $el = $( '#welaunch-import-code-wrapper' );
								if ( $( '#welaunch-import-link-wrapper' ).is( ':visible' ) ) {
									$( '#import-link-value' ).val( '' );
									$( '#welaunch-import-link-wrapper' ).fadeOut(
										'fast',
										function() {
											$el.fadeIn(
												'fast',
												function() {
													$( '#import-code-value' ).focus();
												}
											);
										}
									);
								} else {
									if ( $el.is( ':visible' ) ) {
										$el.fadeOut();
									} else {
										$el.fadeIn(
											'medium',
											function() {
												$( '#import-code-value' ).focus();
											}
										);
									}
								}
							}
						);

						$( this ).find( '#welaunch-import-link-button' ).click(
							function() {
								var $el = $( '#welaunch-import-link-wrapper' );
								if ( $( '#welaunch-import-code-wrapper' ).is( ':visible' ) ) {
									$( '#import-code-value' ).text( '' );
									$( '#welaunch-import-code-wrapper' ).fadeOut(
										'fast',
										function() {
											$el.fadeIn(
												'fast',
												function() {
													$( '#import-link-value' ).focus();
												}
											);
										}
									);
								} else {
									if ( $el.is( ':visible' ) ) {
										$el.fadeOut();
									} else {
										$el.fadeIn(
											'medium',
											function() {
												$( '#import-link-value' ).focus();
											}
										);
									}
								}
							}
						);
						$( this ).find( '#welaunch-export-code-dl' ).click( function( e ) {
							e.preventDefault();

							if ( !! window.onbeforeunload ) {
								if ( confirm( 'Your panel has unchanged values, would you like to save them now?' ) ) {
									$( '#welaunch_top_save' ).click();
									setTimeout( function() {
										window.open( $( this ).attr( 'href' ) );
									}, 2000 );
								}
							} else {
								window.open( $( this ).attr( 'href' ) );
							}
						} );
						$( this ).find( '#welaunch-import-upload' ).click( function() {
							$( '#welaunch-import-upload-file' ).click();
						} );

						document.getElementById( 'welaunch-import-upload-file' ).addEventListener( 'change', function() {
							var file_to_read = document.getElementById( 'welaunch-import-upload-file' ).files[0];
							var fileread = new FileReader();
							$( '#welaunch-import-upload span' ).text( ': ' + file_to_read.name );
							fileread.onload = function() {
								var content = fileread.result;
								$( '#import-code-value' ).val( content );
							};
							fileread.readAsText( file_to_read );
						} );
						$( this ).find( '#welaunch-export-code-copy' ).click(
							function( e ) {
								var $el = $( '#welaunch-export-code' );
								var $secret = $( this ).data( 'secret' );
								e.preventDefault();
								if ( !! window.onbeforeunload ) {
									if ( confirm(
										'Your panel has unchanged values, would you like to save them now?' ) ) {
										$( '#welaunch_top_save' ).click();
										setTimeout( function() {
											welaunch.field_objects.import_export.get_options( $secret, $el );
										}, 2000 );
									}
								} else {
									welaunch.field_objects.import_export.get_options( $secret, $el );
								}
							}
						);
						$( this ).find( 'textarea' ).focusout(
							function() {
								var $id = $( this ).attr( 'id' );
								var $el = $( this );
								var $container = $el;

								if ( 'import-link-value' === $id || 'import-code-value' === $id ) {
									$container = $( this ).parent();
								}

								$container.fadeOut(
									'medium',
									function() {
										if ( 'welaunch-export-link-value' !== $id ) {
											$el.text( '' );
										}
									}
								);
							}
						);

						$( this ).find( '#welaunch-export-link' ).click(
							function() {
								var $el = $( this );
								$el.addClass( 'disabled' ).attr( 'disabled', 'disabled' );
								$el.text( $el.data( 'copy' ) );
								welaunch.field_objects.import_export.copy_text( $el.data( 'url' ) );
								$el.removeClass( 'disabled' );
								$el.text( $el.data( 'copied' ) );
								setTimeout( function() {
									$el.text( $el.data( 'copy' ) ).removeClass( 'disabled' ).removeAttr( 'disabled' );
								}, 2000 );
							}
						);

						textBox1 = document.getElementById( 'welaunch-export-code' );

						textBox1.onfocus = function() {
							textBox1.select();

							// Work around Chrome's little problem.
							textBox1.onmouseup = function() {

								// Prevent further mouseup intervention.
								textBox1.onmouseup = null;
								return false;
							};
						};

						textBox2 = document.getElementById( 'import-code-value' );

						textBox2.onfocus = function() {
							textBox2.select();

							// Work around Chrome's little problem.
							textBox2.onmouseup = function() {

								// Prevent further mouseup intervention.
								textBox2.onmouseup = null;
								return false;
							};
						};
					}
				);
			}
		);
	};
})( jQuery );


