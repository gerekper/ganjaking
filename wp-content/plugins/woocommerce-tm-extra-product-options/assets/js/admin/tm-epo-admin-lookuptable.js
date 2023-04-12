( function( window, document, $ ) {
	'use strict';

	var wp = window.wp;
	var _ = window._;
	var TMEPOADMINLOOKUPJS = window.TMEPOADMINLOOKUPJS;
	var toastr = window.toastr;
	var plupload = window.plupload;
	var templateEngine = {
		tc_builder_elements: wp.template( 'tc-builder-elements' ),
		tc_builder_section: wp.template( 'tc-builder-section' )
	};
	var changedLookupTable = TMEPOADMINLOOKUPJS.lookuptable;

	function getLocalDecimalSeparator() {
		if ( TMEPOADMINLOOKUPJS.tm_epo_global_displayed_decimal_separator === '' ) {
			return TMEPOADMINLOOKUPJS.currency_format_decimal_sep;
		}
		return $.epoAPI.locale.getSystemDecimalSeparator();
	}
	function countDecimals( n ) {
		var numStr = String( n );
		var s;
		if ( numStr.includes( 'e-' ) ) {
			s = numStr.split( 'e-' );
			return Number( s[ 1 ] ) + Number( countDecimals( s[ 0 ] ) );
		} else if ( numStr.includes( '.' ) ) {
			return numStr.split( '.' )[ 1 ].length;
		}
		return 0;
	}

	function createTable() {
		var lookuptable = {};
		var columns = {};
		var rows = {};
		var tableName;
		$( '.lookup-table' ).toArray().forEach( function( table ) {
			table = $( table );
			tableName = table.closest( '.lookup-table-wrap' ).prev( '.lookuptable-name' ).find( '.table-name-value' ).text();
			lookuptable[ tableName ] = {};
			table.find( '.row' ).toArray().forEach( function( row ) {
				$( row ).find( '.ltcell' ).toArray().forEach( function( cell ) {
					var $cell = $( cell );
					var dataRow = $cell.attr( 'data-row' );
					var dataColumn = $cell.attr( 'data-column' );
					var cellValue = $cell.text();
					var h;
					var f;
					if ( dataRow === '1' && dataColumn === '1' ) {
						return;
					}
					h = cellValue.replace( /,/g, '.' );
					f = $.epoAPI.math.toFloat( h );
					if ( h === 'max' ) {
						cellValue = h;
					} else if ( isNaN( f ) ) {
						cellValue = 0;
					} else {
						cellValue = h;
					}

					if ( dataColumn === '1' ) {
						rows[ dataRow ] = cellValue;
						return;
					}
					if ( dataRow === '1' ) {
						columns[ dataColumn ] = cellValue;
						return;
					}
					if ( lookuptable[ tableName ][ columns[ dataColumn ] ] === undefined ) {
						lookuptable[ tableName ][ columns[ dataColumn ] ] = {};
					}
					lookuptable[ tableName ][ columns[ dataColumn ] ][ rows[ dataRow ] ] = cellValue;
				} );
			} );
		} );
		return lookuptable;
	}

	$.tmEPOAdmin = {
		addEventsDone: 0,

		addEvents: function() {
			var $waiting;
			var tcuploader;
			var popup;

			// Import CSV
			$waiting = $( '#builder_import_file' );

			tcuploader = new plupload.Uploader( {
				url: TMEPOADMINLOOKUPJS.ajax_url,
				browse_button: $waiting[ 0 ],
				file_data_name: 'builder_import_file',
				multi_selection: false
			} );

			tcuploader.init();

			tcuploader.bind( 'FilesAdded', function( uploader ) {
				var $_html = $.tmEPOAdmin.builder_floatbox_template_import( {
					id: 'tc-floatbox-content',
					html: '',
					title: TMEPOADMINLOOKUPJS.i18n_import_title
				} );
				var $progress;
				var $selection;
				var uploaderStart = function() {
					$( '#tc-floatbox-content' ).find( '.override-selection' ).addClass( 'tc-hidden' );
					$progress.appendTo( '#tc-floatbox-content' );
					$( '.tc-progress-info-content' ).html( TMEPOADMINLOOKUPJS.i18n_importing );
					uploader.setOption( 'multipart_params', {
						action: 'tc_lookup_table_import',
						post_id: TMEPOADMINLOOKUPJS.post_id,
						security: TMEPOADMINLOOKUPJS.import_nonce
					} );
					uploader.start();
					$( '.flasho' ).addClass( 'tc-color-notice' );
				};

				popup = $.tcFloatBox( {
					closefadeouttime: 0,
					animateOut: '',
					fps: 1,
					ismodal: true,
					refresh: 'fixed',
					width: '50%',
					height: '300px',
					classname: 'flasho tc-wrapper',
					data: $_html
				} );

				$progress = $( '<div class="tc-progress-bar tc-notice"><span class="tc-percent"></span></div><div class="tc-progress-info"><span class="tc-progress-info-content"></span></div>' );

				if ( $( '.lookuptable-wrapper' ).length ) {
					$selection = $(
						'<div class="override-selection"><button type="button" class="tc tc-button details-override">' +
							TMEPOADMINLOOKUPJS.i18n_overwrite_existing_tables +
							'</button></div>'
					);
					$selection.appendTo( '#tc-floatbox-content' );
					$( '.details-override' ).on( 'click', function() {
						uploaderStart();
					} );
				} else {
					uploaderStart();
				}
			} );

			tcuploader.bind( 'FileUploaded', function( uploader, file, response ) {
				var data = $.epoAPI.util.parseJSON( response.response );
				var postTitle;
				var noDestroypopup;

				if ( data && data.result !== undefined && data.message ) {
					$( '.tc-progress-info-content' ).html( data.message );
				}
				if ( data && data.error && data.message ) {
					$( '.tc-progress-info-content' ).html( data.message );
				}
				if ( data && data.result !== undefined && $.epoAPI.math.toFloat( data.result ) === 1 ) {
					$( '.tc-progress-bar' ).removeClass( 'tc-notice' ).addClass( 'tc-success' );
					$( '.tc-progress-info-content' ).removeClass( 'tc-color-error' ).addClass( 'tc-color-success' );
					$( '.flasho' ).removeClass( 'tc-color-notice tc-color-error' ).addClass( 'tc-color-success' );
					$( '.floatbox-cancel' ).addClass( 'tc-hidden' );
					$( '.tc-progress-info-content' ).html( TMEPOADMINLOOKUPJS.i18n_saving );
					$( window ).off( 'beforeunload.edit-post' );
					if ( data.table ) {
						if ( Array.isArray( data.jsobject ) ) {
							$( '.builder-layout' ).html( data.table );
							$.tmEPOAdmin.initSectionsCheck();
							toastr.success( data.message, TMEPOADMINLOOKUPJS.i18n_epo );
							postTitle = $( '#title' );
							if ( postTitle.length && postTitle.val() === '' ) {
								postTitle.val( Object.keys( data.jsobject[ 0 ] )[ 0 ] );
								$( '#title-prompt-text' ).addClass( 'screen-reader-text' );
								if ( $( '#publish' ).attr( 'name' ) === 'publish' ) {
									$( '#publish' ).trigger( 'click' );
								}
							}
						} else {
							toastr.error( TMEPOADMINLOOKUPJS.i18n_invalid_request, TMEPOADMINLOOKUPJS.i18n_epo );
						}
					} else if ( data.different ) {
						noDestroypopup = 1;
						$( '.details-override' ).on( 'click', function() {
							$.post(
								TMEPOADMINLOOKUPJS.ajax_url,
								{
									action: 'tc_lookup_table_import',
									import_override: 1,
									post_id: TMEPOADMINLOOKUPJS.post_id,
									security: TMEPOADMINLOOKUPJS.import_nonce
								},
								function( r ) {
									data = r;
									if ( data.table ) {
										if ( Array.isArray( data.jsobject ) ) {
											$( '.builder-layout' ).html( data.table );
											$.tmEPOAdmin.initSectionsCheck();
											toastr.success( data.message, TMEPOADMINLOOKUPJS.i18n_epo );
										} else {
											toastr.error( TMEPOADMINLOOKUPJS.i18n_invalid_request, TMEPOADMINLOOKUPJS.i18n_epo );
										}
									}
								},
								'json'
							).always( function() {
								popup.destroy();
							} );
						} );
						$( '#tc-floatbox-content' ).find( '.override-selection' ).removeClass( 'tc-hidden' ).addClass( 'height50' ).insertAfter( $( '.tc-progress-info' ) );
						$( '.floatbox-cancel' ).removeClass( 'tc-hidden' );
						$( '.tc-progress-bar' ).addClass( 'tc-hidden' );
						$( '.tc-progress-info-content' ).removeClass( 'tc-color-success tc-color-error' ).addClass( 'tc-color-notice' ).html( data.message );
						$( '.flasho' ).removeClass( 'tc-color-success tc-color-error' ).addClass( 'tc-color-notice' );
					} else {
						toastr.error( TMEPOADMINLOOKUPJS.i18n_invalid_csv, TMEPOADMINLOOKUPJS.i18n_epo );
					}
					if ( noDestroypopup ) {
						return;
					}
					popup.destroy();
				} else {
					$( '.tc-progress-bar' ).removeClass( 'tc-notice' ).addClass( 'tc-error' );
					$( '.tc-progress-info-content' ).removeClass( 'tc-color-success' ).addClass( 'tc-color-error' );
					$( '.flasho' ).removeClass( 'tc-color-notice tc-color-success' ).addClass( 'tc-color-error' );
					if ( ! data && response && response.response ) {
						$( '.tc-progress-info-content' ).addClass( 'tc-normalize' ).html( response.response );
					}
					toastr.error( TMEPOADMINLOOKUPJS.i18n_invalid_request, TMEPOADMINLOOKUPJS.i18n_epo );
				}
			} );

			tcuploader.bind( 'UploadProgress', function( uploader, file ) {
				var progress = parseInt( file.percent, 10 );
				$( '.tc-progress-bar' ).css( 'width', progress + '%' );
				$( '.tc-percent' ).html( progress + '%' );
			} );

			tcuploader.bind( 'Error', function( uploader, error ) {
				if ( error && error.message ) {
					$( '.tc-progress-info-content' )
						.removeClass( 'tc-color-success' )
						.addClass( 'tc-color-error' )
						.html( '\nError #' + error.code + ': ' + error.message );
				}
				$( '.tc-progress-bar' ).removeClass( 'tc-notice' ).addClass( 'tc-error' );
				$( '.flasho' ).removeClass( 'tc-color-notice tc-color-success' ).addClass( 'tc-color-error' );
				if ( error && error.response ) {
					$( '.flasho .header h3' ).html( error.message );
					$( '.tc-progress-info-content' ).addClass( 'tc-normal-font' ).html( error.response );
				}
				toastr.error( TMEPOADMINLOOKUPJS.i18n_invalid_request, TMEPOADMINLOOKUPJS.i18n_epo );
			} );

			tcuploader.bind( 'UploadComplete', function() {
				$( 'body' ).removeClass( 'overflow' );
			} );

			$( document ).on( 'click.cpf', '.tc-add-import-csv', function( e ) {
				e.preventDefault();
				$( '#builder_import_file' ).trigger( 'click' );
			} );

			$( document ).on( 'click.cpf', '.tc-add-export-csv', function( e ) {
				var $this = $( this );
				var data;

				e.preventDefault();
				if ( $this.data( 'doing_export' ) ) {
					return;
				}

				$this.data( 'doing_export', 1 ).prepend( '<i class="tm-icon tcfa tcfa-spin tcfa-spinner"></i>' );

				data = {
					action: 'tc_lookup_table_export',
					metaserialized: JSON.stringify( changedLookupTable ),
					security: TMEPOADMINLOOKUPJS.export_nonce
				};

				$.post(
					TMEPOADMINLOOKUPJS.ajax_url,
					data,
					function( response ) {
						var $_html;
						var message;

						if ( response && response.result && response.result !== '' ) {
							window.location = response.result;
						} else {
							if ( response && response.error && response.message ) {
								message = response.message;
							} else {
								message = TMEPOADMINLOOKUPJS.i18n_error_message;
							}
							$_html = $.tmEPOAdmin.builder_floatbox_template_import( {
								id: 'tc-floatbox-content',
								html: '<div class="tm-inner">' + message + '</div>',
								title: TMEPOADMINLOOKUPJS.i18n_error_title
							} );
							$.tcFloatBox( {
								closefadeouttime: 0,
								animateOut: '',
								fps: 1,
								ismodal: true,
								refresh: 'fixed',
								width: '50%',
								height: '300px',
								classname: 'flasho tc-wrapper tm-error',
								data: $_html
							} );
						}
					},
					'json'
				).always( function() {
					$this.data( 'doing_export', 0 ).find( '.tm-icon' ).remove();
				} );
			} );

			// table
			$( document ).on( 'mouseover', '.ltcell', function() {
				var $this = $( this );
				var table1 = $this.parent().parent().parent();
				var table2 = $this.parent().parent();
				var verTable = $( table1 ).data( 'vertable' ) + '';
				var column = $this.data( 'column' ) + '';
				var row = $this.data( 'row' ) + ' .column1';
				$( table2 ).find( '.' + column ).addClass( 'hov-column-' + verTable );
				$( table1 ).find( '.row.head .' + column ).addClass( 'hov-column-head-' + verTable );
				$( table1 ).find( '.row.' + row ).addClass( 'hov-column-head-' + verTable );
			} );
			$( document ).on( 'mouseout', '.ltcell', function() {
				var $this = $( this );
				var table1 = $this.parent().parent().parent();
				var table2 = $this.parent().parent();
				var verTable = $( table1 ).data( 'vertable' ) + '';
				var column = $this.data( 'column' ) + '';
				var row = $this.data( 'row' ) + ' .column1';
				$( table2 ).find( '.' + column ).removeClass( 'hov-column-' + verTable );
				$( table1 ).find( '.row.head .' + column ).removeClass( 'hov-column-head-' + verTable );
				$( table1 ).find( '.row.' + row ).removeClass( 'hov-column-head-' + verTable );
			} );
			$( document ).on( 'keypress', '[contenteditable="true"]', function( e ) {
				if ( e.which === 13 ) {
					e.preventDefault();
					$( this ).trigger( 'blur' );
				}
			} );
			$( document ).on( 'focus', '[contenteditable="true"]', function() {
				var $this = $( this );
				$this.attr( 'data-value', $this.text() );
			} );
			$( document ).on( 'blur', '[contenteditable="true"]', function() {
				var $this = $( this );
				var h = $this.text().replace( /,/g, '.' );
				var f = $.epoAPI.math.toFloat( h );
				var args = {
					symbol: '',
					format: '',
					decimal: getLocalDecimalSeparator(),
					thousand: '',
					precision: countDecimals( f )
				};
				var dataRow = $this.attr( 'data-row' );
				var dataColumn = $this.attr( 'data-column' );
				var dataValue = $this.attr( 'data-value' );
				var numRows;
				if ( dataValue === undefined ) {
					dataValue = 0;
				}
				if ( $this.is( '.table-name-value' ) ) {
					if ( h === '' ) {
						$this.html( dataValue );
					}
					changedLookupTable = createTable();
					return;
				}
				numRows = $this.closest( 'table' ).find( 'tr' ).length;
				if ( h === '' ) {
					if ( dataColumn === '1' || dataRow === '1' ) {
						$this.html( dataValue );
						return;
					}
				} else if ( h === 'max' ) {
					if ( Number( dataRow ) > 1 ) {
						if ( Number( dataRow ) < numRows || Number( dataColumn ) > 1 ) {
							$this.html( dataValue );
							return;
						}
					} else if ( Number( dataRow ) === 1 && Number( dataColumn ) <= $this.siblings().length ) {
						$this.html( dataValue );
						return;
					}
					$this.html( h );
				} else if ( isNaN( f ) || isNaN( Number( h ) ) ) {
					$this.html( dataValue );
				} else {
					if ( h !== '' ) {
						h = $.epoAPI.math.format( h, args );
					}
					$this.html( h );
				}
				changedLookupTable = createTable();
			} );
		},

		tm_escape: function( val ) {
			return encodeURIComponent( val );
		},

		createLookuptableMeta: function() {
			var data;
			var name;
			var tmMetaSerialized;
			var previewField = $( 'input#wp-preview' );
			var $tcForm = $( '#tmformfieldsbuilderwrap' );

			if ( _.isEqual( TMEPOADMINLOOKUPJS.lookuptable, changedLookupTable ) ) {
				return;
			}

			$( '.lookuptable-meta' ).remove();

			name = 'lookuptable_meta_changed';

			data = $.tmEPOAdmin.tm_escape( JSON.stringify( changedLookupTable ) );

			tmMetaSerialized = $( "<textarea class='lookuptable-meta tm-hidden' name='" + name + "'></textarea>" ).val( data );
			$tcForm.prepend( tmMetaSerialized );

			if ( previewField.length > 0 && previewField.val() !== '' ) {
				$( '.lookuptable-meta' ).remove();
			}
		},

		fixFormSubmit: function() {
			var $post = $( '#post' );
			var found;
			var subscribe;
			var sub;

			if ( $post.length === 1 ) {
				$post.on( 'submit', function() {
					$.tmEPOAdmin.createLookuptableMeta();
					return true; // ensure form still submits
				} );
			} else if ( wp.data && wp.data.subscribe !== undefined ) {
				found = false;
				subscribe = wp.data.subscribe;

				if ( typeof subscribe === 'function' ) {
					sub = function() {
						var isSavingPost;
						var didPostSaveRequestSucceed;
						var didPostSaveRequestFail;

						if ( ! wp.data.select( 'core/editor' ) ) {
							return;
						}
						didPostSaveRequestSucceed = wp.data.select( 'core/editor' ).didPostSaveRequestSucceed();
						didPostSaveRequestFail = wp.data.select( 'core/editor' ).didPostSaveRequestFail();

						if ( wp && wp.data && wp.data.select( 'core/editor' ) ) {
							isSavingPost = wp.data.select( 'core/editor' ).isSavingPost();
						}
						if ( ! found && isSavingPost ) {
							found = true;
							$.tmEPOAdmin.createLookuptableMeta();
						}
						if ( found && ( didPostSaveRequestSucceed || didPostSaveRequestFail ) ) {
							found = false;
							setTimeout( function() {
								$( '.lookuptable-meta' ).remove();
							}, 600 );
						}
					};

					subscribe( sub );
				}
			}
		},

		initialitize: function() {
			$.tmEPOAdmin.initSectionsCheck();
			$.tmEPOAdmin.addEvents();
			$( '.builder-layout' ).removeClass( 'tm-hidden' );
			$.tmEPOAdmin.fixFormSubmit();
		},
		initSectionsCheck: function() {
			var length = $( '.lookuptable-wrapper' ).length;

			if ( ! length ) {
				$( '.builder-add-section-action' ).hide();
				$( '.builder-selector' ).hide();
				$( '.tc-welcome' ).show();
				$( '.builder-layout' ).hide();
				$( '.tc-add-export-csv' ).hide();
			} else {
				$( '.builder-add-section-action' ).show();
				$( '.builder-selector' ).show();
				$( '.tc-welcome' ).hide();
				$( '.builder-layout' ).show();
				$( '.tc-add-export-csv' ).show();
			}
		},
		builder_floatbox_template: function( data, template ) {
			return $.epoAPI.template.html( wp.template( template || 'tc-floatbox' ), {
				id: data.id,
				title: data.title,
				html: data.html,
				uniqidtext: data.uniqidtext || '',
				uniqid: data.uniqid,
				update: data.update || TMEPOADMINLOOKUPJS.i18n_update,
				cancel: data.cancel || TMEPOADMINLOOKUPJS.i18n_cancel
			} );
		},

		builder_floatbox_template_import: function( data ) {
			return $.epoAPI.template.html( wp.template( 'tc-floatbox-import' ), {
				id: data.id,
				title: data.title,
				html: data.html,
				cancel: TMEPOADMINLOOKUPJS.i18n_cancel
			} );
		}
	};

	// document ready
	$( function() {
		templateEngine = $.epoAPI.applyFilter( 'tc_adjust_admin_template_engine', templateEngine );

		$.tmEPOAdmin.initialitize();
	} );
}( window, document, window.jQuery ) );
