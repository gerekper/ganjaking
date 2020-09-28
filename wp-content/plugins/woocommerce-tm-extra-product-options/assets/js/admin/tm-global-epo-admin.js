( function( window, document, $ ) {
	'use strict';

	var wp = window.wp;
	var TMEPOADMINJS = window.TMEPOADMINJS;
	var TMEPOGLOBALADMINJS = window.TMEPOGLOBALADMINJS;
	var TMEPOOPTIONSJS;
	var woocommerce_admin;
	var tinyMCEPreInit;
	var QTags;
	var quicktags;
	var tinyMCE;
	var toastr = window.toastr;
	var _ = window._;
	var plupload = window.plupload;
	var globalVariationObject = false;
	var templateEngine = {
		tc_builder_elements: wp.template( 'tc-builder-elements' ),
		tc_builder_section: wp.template( 'tc-builder-section' )
	};
	var JSON = window.JSON;
	var TCBUILDER;
	var wc_enhanced_select_params;

	function tcArrayValues( input ) {
		var tmp_arr = [];
		Object.keys( input ).forEach( function( key ) {
			if ( Object.prototype.hasOwnProperty.call( input, key ) ) {
				tmp_arr[ tmp_arr.length ] = input[ key ];
			}
		} );
		return tmp_arr;
	}

	// https://locutus.io/php/misc/uniqid/index.html
	function tcCreateUniqid( prefix, more_entropy ) {
		var retId;
		var _formatSeed = function( seed, reqWidth ) {
			seed = parseInt( seed, 10 ).toString( 16 ); // to hex str
			if ( reqWidth < seed.length ) {
				// so long we split
				return seed.slice( seed.length - reqWidth );
			}
			if ( reqWidth > seed.length ) {
				// so short we pad
				return new Array( 1 + ( reqWidth - seed.length ) ).join( '0' ) + seed;
			}
			return seed;
		};
		var radom;

		if ( prefix === undefined ) {
			prefix = '';
		}

		$.epoAPI.php = $.epoAPI.php || {};

		if ( ! $.epoAPI.php.uniqidSeed ) {
			// init seed with big random int
			$.epoAPI.php.uniqidSeed = Math.floor( Math.random() * 0x75bcd15 );
		}
		$.epoAPI.php.uniqidSeed += 1;

		// start with prefix, add current milliseconds hex string
		retId = prefix;
		retId += _formatSeed( parseInt( Date.now() / 1000, 10 ), 8 );
		// add seed hex string
		retId += _formatSeed( $.epoAPI.php.uniqidSeed, 5 );
		if ( more_entropy ) {
			// for more entropy we add a float lower to 10
			radom = Math.random() * 10;
			retId += radom.toFixed( 8 ).toString();
		}

		return retId;
	}

	// https://medium.com/javascript-in-plain-english/how-to-deep-copy-objects-and-arrays-in-javascript-7c911359b089
	function deepCopyArray( inObject ) {
		var outObject;
		var value;

		if ( typeof inObject !== 'object' || inObject === null ) {
			return inObject; // Return the value if inObject is not an object
		}

		// Create an array or object to hold the values
		outObject = Array.isArray( inObject ) ? [] : {};

		Object.keys( inObject ).forEach( function( key ) {
			if ( inObject ) {
				value = inObject[ key ];

				// Recursively (deep) copy for nested objects, including arrays
				outObject[ key ] = typeof value === 'object' && value !== null ? deepCopyArray( value ) : value;
			}
		} );

		return outObject;
	}

	$.tmEPOAdmin = {
		add_events_done: 0,
		tm_variations_check_for_changes: 0,
		element_logic_object: {},
		section_logic_object: {},
		logic_operators: {
			is: TMEPOGLOBALADMINJS.i18n_is,
			isnot: TMEPOGLOBALADMINJS.i18n_is_not,
			isempty: TMEPOGLOBALADMINJS.i18n_is_empty,
			isnotempty: TMEPOGLOBALADMINJS.i18n_is_not_empty,
			startswith: TMEPOGLOBALADMINJS.i18n_starts_with,
			endswith: TMEPOGLOBALADMINJS.i18n_ends_with,
			greaterthan: TMEPOGLOBALADMINJS.i18n_greater_than,
			lessthan: TMEPOGLOBALADMINJS.i18n_less_than,
			greaterthanequal: TMEPOGLOBALADMINJS.i18n_greater_than_equal,
			lessthanequal: TMEPOGLOBALADMINJS.i18n_less_than_equal
		},
		builder_items_sortable_obj: {
			start: {},
			end: {}
		},
		builder_sections_sortable_obj: {
			start: {},
			end: {}
		},
		// Helper : Holds element and sections available sizes
		builder_size: {
			w1: '1%',
			w2: '2%',
			w3: '3%',
			w4: '4%',
			w5: '5%',
			w6: '6%',
			w7: '7%',
			w8: '8%',
			w9: '9%',
			w10: '10%',
			w11: '11%',
			w12: '12%',

			'w12-5': '12.5%',

			w13: '13%',
			w14: '14%',
			w15: '15%',
			w16: '16%',
			w17: '17%',
			w18: '18%',
			w19: '19%',
			w20: '20%',
			w21: '21%',
			w22: '22%',
			w23: '23%',
			w24: '24%',
			w25: '25%',
			w26: '26%',
			w27: '27%',
			w28: '28%',
			w29: '29%',
			w30: '30%',
			w31: '31%',
			w32: '32%',
			w33: '33%',
			w34: '34%',
			w35: '35%',
			w36: '36%',
			w37: '37%',

			'w37-5': '37.5%',

			w38: '38%',
			w39: '39%',
			w40: '40%',
			w41: '41%',
			w42: '42%',
			w43: '43%',
			w44: '44%',
			w45: '45%',
			w46: '46%',
			w47: '47%',
			w48: '48%',
			w49: '49%',
			w50: '50%',
			w51: '51%',
			w52: '52%',
			w53: '53%',
			w54: '54%',
			w55: '55%',
			w56: '56%',
			w57: '57%',
			w58: '58%',
			w59: '59%',
			w60: '60%',
			w61: '61%',
			w62: '62%',

			'w62-5': '62.5%',

			w63: '63%',
			w64: '64%',
			w65: '65%',
			w66: '66%',
			w67: '67%',
			w68: '68%',
			w69: '69%',
			w70: '70%',
			w71: '71%',
			w72: '72%',
			w73: '73%',
			w74: '74%',
			w75: '75%',
			w76: '76%',
			w77: '77%',
			w78: '78%',
			w79: '79%',
			w80: '80%',
			w81: '81%',
			w82: '82%',
			w83: '83%',
			w84: '84%',
			w85: '85%',
			w86: '86%',
			w87: '87%',

			'w87-5': '87.5%',

			w88: '88%',
			w89: '89%',
			w90: '90%',
			w91: '91%',
			w92: '92%',
			w93: '93%',
			w94: '94%',
			w95: '95%',
			w96: '96%',
			w97: '97%',
			w98: '98%',
			w99: '99%',
			w100: '100%'
		},
		id_array: {},
		is_element_dragged: false,

		variationSection: false,
		variationSectionIndex: false,
		variationFieldIndex: false,

		can_take_logic: [ 'product', 'color', 'range', 'radiobuttons', 'checkboxes', 'selectbox', 'textfield', 'textarea', 'variations' ],

		add_sortables: function() {
			if ( $.tmEPOAdmin.is_original ) {
				// Sections sortable
				$( '.builder_layout' ).sortable( {
					handle: '.move',
					cursor: 'move',
					items: '.builder_wrapper:not(.tma-nomove)',
					start: function( e, ui ) {
						var builder_wrapper;
						var sectionIndex;

						builder_wrapper = $( ui.item ).closest( '.builder_wrapper' );
						sectionIndex = builder_wrapper.index();
						$.tmEPOAdmin.builder_sections_sortable_obj.start.section = sectionIndex;

						ui.placeholder.height( ui.helper.outerHeight() );
						ui.placeholder.width( ui.helper.outerWidth() );
					},
					stop: function( e, ui ) {
						var builder_wrapper;
						var sectionIndex;

						builder_wrapper = $( ui.item ).closest( '.builder_wrapper' );
						sectionIndex = builder_wrapper.index();
						$.tmEPOAdmin.builder_sections_sortable_obj.end.section = sectionIndex;

						TCBUILDER.splice( sectionIndex, 0, TCBUILDER.splice( $.tmEPOAdmin.builder_sections_sortable_obj.start.section, 1 )[ 0 ] );

						$.tmEPOAdmin.builder_reorder_multiple();
						$.tmEPOAdmin.builder_sections_sortable_obj = {
							start: {},
							end: {}
						};
					},
					cancel: '.tma-nomove',
					forcePlaceholderSize: true,
					placeholder: 'bitem pl2',
					tolerance: 'pointer'
				} );

				// Elements sortable
				$.tmEPOAdmin.builder_items_sortable( $( '.builder_wrapper .bitem_wrapper' ) );
			}
		},

		getBuilder: function() {
			return TCBUILDER;
		},

		add_events: function() {
			var $waiting;
			var tcuploader;
			var popup;
			var uploadmeta;

			if ( $.tmEPOAdmin.add_events_done === 1 ) {
				return;
			}

			$.tmEPOAdmin.add_sortables();

			// Import CSV
			$waiting = $( '#builder_import_file' );

			tcuploader = new plupload.Uploader( {
				url: TMEPOGLOBALADMINJS.import_url,
				browse_button: $waiting[ 0 ],
				file_data_name: 'builder_import_file',
				multi_selection: false
			} );

			tcuploader.init();

			tcuploader.bind( 'FilesAdded', function( uploader ) {
				var $_html = $.tmEPOAdmin.builder_floatbox_template_import( {
					id: 'temp_for_floatbox_insert',
					html: '',
					title: TMEPOGLOBALADMINJS.i18n_import_title
				} );
				var $progress;
				var $selection;

				popup = $.tcFloatBox( {
					closefadeouttime: 0,
					animateOut: '',
					fps: 1,
					ismodal: true,
					refresh: 'fixed',
					width: '50%',
					height: '300px',
					classname: 'flasho tm_wrapper',
					data: $_html
				} );

				$progress = $( '<div class="tm_progress_bar tm_notice"><span class="tm_percent"></span></div><div class="tm_progress_info"><span class="tm_info"></span></div>' );
				$selection = $(
					'<div class="override-selection"><button type="button" class="tc tc-button details_override">' +
						TMEPOGLOBALADMINJS.i18n_overwrite_existing_elements +
						'</button><button type="button" class="tc tc-button details_append">' +
						TMEPOGLOBALADMINJS.i18n_append_new_elements +
						'</button></div>'
				);
				$selection.appendTo( '#temp_for_floatbox_insert' );

				uploadmeta = $.tmEPOAdmin.tm_escape( JSON.stringify( $.tmEPOAdmin.prepare_for_json( $.tmEPOAdmin.tcSerializeJsOptionsObject( TCBUILDER ) ) ) );

				if ( TMEPOGLOBALADMINJS.is_original_post ) {
					$( '.details_override' ).on( 'click', function() {
						$( '#temp_for_floatbox_insert' ).find( '.override-selection' ).remove();
						$progress.appendTo( '#temp_for_floatbox_insert' );
						$( '.tm_info' ).html( TMEPOGLOBALADMINJS.i18n_importing );
						uploader.setOption( 'multipart_params', {
							action: 'import',
							import_override: 1,
							post_id: TMEPOGLOBALADMINJS.post_id,
							security: TMEPOGLOBALADMINJS.import_nonce,
							is_original_post: TMEPOGLOBALADMINJS.is_original_post
						} );
						uploader.start();
						$( '.flasho' ).addClass( 'tm_color_notice' );
					} );

					$( '.details_append' ).on( 'click', function() {
						$( '#temp_for_floatbox_insert' ).find( '.override-selection' ).remove();
						$progress.appendTo( '#temp_for_floatbox_insert' );
						$( '.tm_info' ).html( TMEPOGLOBALADMINJS.i18n_importing );
						uploader.setOption( 'multipart_params', {
							action: 'import',
							import_override: 0,
							post_id: TMEPOGLOBALADMINJS.post_id,
							tm_uploadmeta: uploadmeta,
							security: TMEPOGLOBALADMINJS.import_nonce,
							is_original_post: TMEPOGLOBALADMINJS.is_original_post
						} );
						uploader.start();
						$( '.flasho' ).addClass( 'tm_color_notice' );
					} );
				} else {
					$( '#temp_for_floatbox_insert' ).find( '.override-selection' ).remove();
					$progress.appendTo( '#temp_for_floatbox_insert' );
					$( '.tm_info' ).html( TMEPOGLOBALADMINJS.i18n_importing );
					uploader.setOption( 'multipart_params', {
						action: 'import',
						import_override: 1,
						post_id: TMEPOGLOBALADMINJS.post_id,
						tm_uploadmeta: uploadmeta,
						security: TMEPOGLOBALADMINJS.import_nonce,
						is_original_post: TMEPOGLOBALADMINJS.is_original_post
					} );
					uploader.start();
					$( '.flasho' ).addClass( 'tm_color_notice' );
				}
			} );

			tcuploader.bind( 'FileUploaded', function( uploader, file, response ) {
				var data = $.epoAPI.util.parseJSON( response.response );

				if ( data && data.result !== undefined && data.message ) {
					$( '.tm_info' ).html( data.message );
				}
				if ( data && data.error && data.message ) {
					$( '.tm_info' ).html( data.message );
				}
				if ( data && data.result !== undefined && $.epoAPI.math.toFloat( data.result ) === 1 ) {
					$( '.tm_progress_bar' ).removeClass( 'tm_notice' ).addClass( 'tm_success' );
					$( '.tm_info' ).removeClass( 'tm_color_error' ).addClass( 'tm_color_success' );
					$( '.flasho' ).removeClass( 'tm_color_notice tm_color_error' ).addClass( 'tm_color_success' );
					$( '.floatbox-cancel' ).remove();
					$( '.tm_info' ).html( TMEPOGLOBALADMINJS.i18n_saving );
					$( window ).off( 'beforeunload.edit-post' );
					if ( data.options ) {
						if ( Array.isArray( data.jsobject ) ) {
							$( '.builder_layout' ).html( data.options );
							TCBUILDER = data.jsobject;
							$.tmEPOAdmin.setGlobalVariationObject( 'initialitize_on_after' );
							toastr.success( data.message, TMEPOGLOBALADMINJS.i18n_epo );
						} else {
							toastr.error( TMEPOGLOBALADMINJS.i18n_invalid_request, TMEPOGLOBALADMINJS.i18n_epo );
						}
					}
					popup.destroy();
				} else {
					$( '.tm_progress_bar' ).removeClass( 'tm_notice' ).addClass( 'tm_error' );
					$( '.tm_info' ).removeClass( 'tm_color_success' ).addClass( 'tm_color_error' );
					$( '.flasho' ).removeClass( 'tm_color_notice tm_color_success' ).addClass( 'tm_color_error' );
				}
			} );

			tcuploader.bind( 'UploadProgress', function( uploader, file ) {
				var progress = parseInt( file.percent, 10 );
				$( '.tm_progress_bar' ).css( 'width', progress + '%' );
				$( '.tm_percent' ).html( progress + '%' );
			} );

			tcuploader.bind( 'Error', function( uploader, error ) {
				if ( error && error.message ) {
					$( '.tm_info' )
						.removeClass( 'tm_color_success' )
						.addClass( 'tm_color_error' )
						.html( '\nError #' + error.code + ': ' + error.message );
				}
				$( '.tm_progress_bar' ).removeClass( 'tm_notice' ).addClass( 'tm_error' );
				$( '.flasho' ).removeClass( 'tm_color_notice tm_color_success' ).addClass( 'tm_color_error' );
			} );

			tcuploader.bind( 'UploadComplete', function() {
				$( 'body' ).removeClass( 'overflow' );
			} );

			// Export button
			$( document ).on( 'click.cpf', '#builder_export', function( e ) {
				var $this = $( this );
				var tm_meta;
				var data;

				e.preventDefault();

				if ( $this.data( 'doing_export' ) ) {
					return;
				}

				$this.data( 'doing_export', 1 ).prepend( '<i class="tm-icon tcfa tcfa-spin tcfa-spinner"></i>' );

				tm_meta = $.tmEPOAdmin.prepare_for_json( $.tmEPOAdmin.tcSerializeJsOptionsObject( TCBUILDER ) );

				tm_meta = JSON.stringify( tm_meta );

				data = {
					action: 'tm_export',
					metaserialized: tm_meta,
					is_original_post: TMEPOGLOBALADMINJS.is_original_post,
					security: TMEPOGLOBALADMINJS.export_nonce
				};

				$.post(
					TMEPOGLOBALADMINJS.ajax_url,
					data,
					function( response ) {
						var $_html;

						if ( response && response.result && response.result !== '' ) {
							window.location = response.result;
						} else if ( response && response.error && response.message ) {
							$_html = $.tmEPOAdmin.builder_floatbox_template_import( {
								id: 'temp_for_floatbox_insert',
								html: '<div class="tm-inner">' + response.message + '</div>',
								title: TMEPOGLOBALADMINJS.i18n_error_title
							} );
							$.tcFloatBox( {
								closefadeouttime: 0,
								animateOut: '',
								fps: 1,
								ismodal: true,
								refresh: 'fixed',
								width: '50%',
								height: '300px',
								classname: 'flasho tm_wrapper tm-error',
								data: $_html
							} );
						}
					},
					'json'
				).always( function() {
					$this.data( 'doing_export', 0 ).find( '.tm-icon' ).remove();
				} );
			} );

			// Save button
			$( document ).on( 'click.cpf', '#builder_save', function( e ) {
				var data;
				var $this = $( this );

				if ( $this.data( 'doing_export' ) ) {
					return;
				}

				$this.data( 'doing_export', 1 ).prepend( '<i class="tm-icon tcfa tcfa-spin tcfa-spinner"></i>' );

				data = {
					action: 'tm_save',
					tm_uploadmeta: $.tmEPOAdmin.tm_escape( JSON.stringify( $.tmEPOAdmin.prepare_for_json( $.tmEPOAdmin.tcSerializeJsOptionsObject( TCBUILDER ) ) ) ),
					post_id: TMEPOGLOBALADMINJS.post_id,
					security: TMEPOGLOBALADMINJS.save_nonce
				};

				$( '#tmformfieldsbuilderwrap' ).addClass( 'disabled' );

				e.preventDefault();
				$.post(
					TMEPOGLOBALADMINJS.ajax_url,
					data,
					function( response ) {
						if ( response && response.result && response.result === 1 ) {
							toastr.success( response.message, TMEPOGLOBALADMINJS.i18n_epo );
						} else {
							toastr.error( response.message, TMEPOGLOBALADMINJS.i18n_epo );
						}
					},
					'json'
				).always( function( response ) {
					$( '#tmformfieldsbuilderwrap' ).removeClass( 'disabled' );
					if ( response.responseText === '-1' ) {
						toastr.error( TMEPOGLOBALADMINJS.i18n_invalid_request, TMEPOGLOBALADMINJS.i18n_epo );
					}
					$this.data( 'doing_export', 0 ).find( '.tm-icon' ).remove();
				} );
			} );

			$( document ).on( 'click.cpf', '#builder_import,.tc-add-import-csv', function( e ) {
				e.preventDefault();
				$( '#builder_import_file' ).trigger( 'click' );
			} );

			// Fullsize button
			$( document ).on( 'click.cpf', '#builder_fullsize', function( e ) {
				e.preventDefault();
				$( 'body' ).addClass( 'overflow fullsize' );
			} );

			// Close Fullsize button
			$( document ).on( 'click.cpf', '#builder_fullsize_close', function( e ) {
				e.preventDefault();
				$( 'body' ).removeClass( 'overflow fullsize' );
			} );

			// Add Element button
			$( document ).on( 'click.cpf', '.builder-add-element', $.tmEPOAdmin.builder_add_element_onClick );
			// Section add button
			$( document ).on( 'click.cpf', '.builder_add_section,.tc-add-section ', $.tmEPOAdmin.builder_add_section_onClick );
			$( document ).on( 'click.cpf', '.builder-add-section-and-element,.tc-add-element', $.tmEPOAdmin.builder_add_section_and_element_onClick );
			// Variation button
			$( document ).on( 'click.cpf', '.builder_add_variation', $.tmEPOAdmin.builder_add_variation_onClick );

			// Section edit button
			$( document ).on( 'click.cpf', '.builder_wrapper .section-settings .edit', $.tmEPOAdmin.builder_section_item_onClick );
			// Section clone button
			$( document ).on( 'click.cpf', '.builder_wrapper .section-settings .clone', $.tmEPOAdmin.builder_section_clone_onClick );
			// Section plus button
			$( document ).on( 'click.cpf', '.builder_wrapper .section-settings .plus', $.tmEPOAdmin.builder_section_plus_onClick );
			// Section minus button
			$( document ).on( 'click.cpf', '.builder_wrapper .section-settings .minus', $.tmEPOAdmin.builder_section_minus_onClick );
			// Section delete button
			$( document ).on( 'click.cpf', '.builder_wrapper .btitle .delete:not(.builder_wrapper.tma-variations-wrap .btitle .delete)', $.tmEPOAdmin.builder_section_delete_onClick );
			// Section fold button
			$( document ).on( 'click.cpf', '.builder_wrapper .btitle .fold', $.tmEPOAdmin.builder_section_fold_onClick );

			// Variation delete button
			$( document ).on( 'click', '.builder_wrapper.tma-variations-wrap .btitle .delete', $.tmEPOAdmin.builder_variation_delete_onClick );

			// Element edit button
			$( document ).on( 'click.cpf', '.bitem .bitem-settings .edit', $.tmEPOAdmin.builder_item_onClick );
			// Element clone button
			$( document ).on( 'click.cpf', '.bitem .bitem-settings .clone', $.tmEPOAdmin.builder_clone_onClick );
			// Element plus button
			$( document ).on( 'click.cpf', '.bitem .bitem-settings .plus', $.tmEPOAdmin.builder_plus_onClick );
			// Element minus button
			$( document ).on( 'click.cpf', '.bitem .bitem-settings .minus', $.tmEPOAdmin.builder_minus_onClick );
			// Element delete button
			$( document ).on( 'click', '.bitem .delete', $.tmEPOAdmin.builder_delete_onClick );

			// EDIT panel events
			// Add options button
			$( document ).on( 'click.cpf', '.builder-panel-add', $.tmEPOAdmin.builder_panel_add_onClick );
			// Mass add options button
			$( document ).on( 'click.cpf', '.builder-panel-mass-add', $.tmEPOAdmin.builder_panel_mass_add_onClick );
			// Populate options button
			$( document ).on( 'click.cpf', '.builder-panel-populate', $.tmEPOAdmin.builder_panel_populate_onClick );
			// Remove image
			$( document ).on( 'click.cpf', '.builder-image-delete', $.tmEPOAdmin.builder_image_delete_onClick );
			// Delete options button
			$( document ).on( 'click.cpf', '.builder_panel_delete', $.tmEPOAdmin.builder_panel_delete_onClick );
			$( '.builder_panel_delete' ).on( 'click.cpf', $.tmEPOAdmin.builder_panel_delete_onClick ); //sortable bug
			$( document ).on( 'click.cpf', '.builder_panel_delete_all', $.tmEPOAdmin.builder_panel_delete_all_onClick );
			// Move panel up
			$( document ).on( 'click.cpf', '.builder_panel_up', function() {
				var t = $( this );
				var options_wrap = t.closest( '.options_wrap' );
				var prev = options_wrap.prev();

				prev.before( options_wrap );
				$.tmEPOAdmin.panels_reorder( t.closest( '.panels_wrap' ) );
				$.tmEPOAdmin.paginattion_init( 'current' );
			} );
			// Move panel downn
			$( document ).on( 'click.cpf', '.builder_panel_down', function() {
				var t = $( this );
				var options_wrap = t.closest( '.options_wrap' );
				var next = options_wrap.next();

				next.after( options_wrap );
				$.tmEPOAdmin.panels_reorder( t.closest( '.panels_wrap' ) );
				$.tmEPOAdmin.paginattion_init( 'current' );
			} );

			// Auto generate option value
			$( document ).on( 'keyup.cpf change.cpf', '.tm_option_title', function() {
				$( this ).closest( '.options_wrap' ).find( '.tm_option_value' ).val( $( '<div/>' ).html( $( this ).val() ).text() );
			} );

			$( document ).on( 'change.cpf changechoice', '.tm_option_enabled', function() {
				var t = $( this );
				if ( t.is( ':checked' ) ) {
					t.closest( '.options_wrap' ).removeClass( 'choice_is_disabled' );
				} else {
					t.closest( '.options_wrap' ).addClass( 'choice_is_disabled' );
				}
			} );

			// Price
			$( document ).on( 'click.cpf', '.tc-element-setting-price, .tc-element-setting-sale-price, .tm_option_price, .tm_option_sale_price', function() {
				var $_html;
				var thisPopup;
				var val;
				var $this = $( this );
				var pricetypeSelector;

				if ( $this.is( '.tc-element-setting-price, .tc-element-setting-sale-price' ) ) {
					pricetypeSelector = $( '.tm-pricetype-selector' );
				} else {
					pricetypeSelector = $this.closest( '.options_wrap' ).find( '.tm_option_price_type' );
				}

				if ( pricetypeSelector.val() === 'math' ) {
					val = $this.val();
					$_html = $.tmEPOAdmin.builder_floatbox_template(
						{
							id: 'temp_for_floatbox_insert_pop',
							update: TMEPOGLOBALADMINJS.i18n_save,
							html: '<div class="tm-inner">' + '<textarea class="tc-element-setting-edit-price"></textarea>' + '<div class="tc-price-variables"></div>' + '</div>',
							title: TMEPOGLOBALADMINJS.i18n_edit_price
						},
						'tc-floatbox-edit'
					);
					thisPopup = $.tcFloatBox( {
						closefadeouttime: 0,
						animateOut: '',
						fps: 1,
						ismodal: false,
						refresh: 'fixed',
						width: '50%',
						height: '80%',
						top: '10%',
						left: '25%',
						classname: 'flasho tm_wrapper tc-builder-add-section-and-element',
						data: $_html,
						cancelClass: '.floatbox-edit-cancel',
						unique: true
					} );

					$.tmEPOAdmin.populatePriceVariables( $( '.tc-price-variables' ) );

					$( '.tc-element-setting-edit-price' ).val( val );

					$( '.floatbox-edit-update' ).on( 'click.cpf', function( ev ) {
						ev.preventDefault();

						$this.val( $( '.tc-element-setting-edit-price' ).val() );
						thisPopup.destroy();
					} );

					$( '.tc-var-field' ).on( 'click.cpf', function( ev ) {
						var $txt = $( '.tc-element-setting-edit-price' );
						var textAreaTxt = $txt.val();
						var txtarea = $txt[ 0 ];
						var caretPos = txtarea.selectionStart;
						var scrollPos = txtarea.scrollTop;
						var front = textAreaTxt.substring( 0, caretPos );
						var back = textAreaTxt.substring( txtarea.selectionEnd, textAreaTxt.length );
						var txtToAdd = $( this ).attr( 'data-value' );

						ev.preventDefault();

						$txt.val( front + txtToAdd + back );
						caretPos = caretPos + txtToAdd.length;
						txtarea.selectionStart = caretPos;
						txtarea.selectionEnd = caretPos;
						txtarea.focus();
						txtarea.scrollTop = scrollPos;
					} );

					$( '.formula-field-mode' ).on( 'change.cpf', function() {
						var mode = $( this ).val();
						var list = $( '.tc-var-list.other .tc-var-field' );

						list.toArray().forEach( function( el ) {
							$( el ).attr( 'data-value', '{field.' + $( el ).attr( 'title' ) + '.' + mode + '}' );
						} );
					} );
				}
			} );

			// Upload button
			$( document ).on( 'click.cpf', '.tm_upload_button', $.tmEPOAdmin.upload );
			$( document ).on( 'change.cpf', '.use_images,.tm-use-lightbox,.use_colors', $.tmEPOAdmin.tm_upload );
			$( document ).on( 'change.cpf', '.use_url', $.tmEPOAdmin.tm_url );

			$( document ).on( 'change.cpf', '.tm-pricetype-selector', $.tmEPOAdmin.tm_pricetype_selector );
			$( document ).on( 'change.cpf', '.tm_option_price_type', $.tmEPOAdmin.tm_option_price_type );

			$( document ).on( 'change.cpf', '.variations-display-as', $.tmEPOAdmin.variations_display_as );
			$( document ).on( 'change.cpf', '.tm-attribute .tm-changes-product-image', $.tmEPOAdmin.variations_display_as );
			$( document ).on( 'click.cpf', '.tm-upload-button-remove', $.tmEPOAdmin.tm_upload_button_remove_onClick );

			$( document ).on( 'change.cpf', '.tm-weekday-picker', $.tmEPOAdmin.tm_weekday_picker );
			$( document ).on( 'change.cpf', '.tm-month-picker', $.tmEPOAdmin.tm_month_picker );

			$( document ).on( 'click.cpf', '.tm-tags-container .tab-header', function() {
				var $this = $( this );
				var tm_tags_container = $this.closest( '.tm-tags-container' );
				var tm_elements_container = tm_tags_container.find( '.tm-elements-container' );
				var elements = tm_elements_container.find( 'li.tm-element-button' );
				var headers = tm_tags_container.find( '.tab-header' );
				var tag_to_show = $this.attr( 'data-tm-tag' );

				headers.removeClass( 'open' ).addClass( 'closed' );
				$this.removeClass( 'closed' ).addClass( 'open' );

				if ( tag_to_show === 'all' ) {
					elements.removeClass( 'tm-hidden' );
				} else {
					elements.addClass( 'tm-hidden' );
					elements.filter( '.' + tag_to_show ).removeClass( 'tm-hidden' );
				}
			} );

			// popup editor identification
			$( document ).on( 'click.cpf', '.tm_editor_wrap', function() {
				var t = $( this ).find( 'textarea' );
				if ( t.attr( 'id' ) ) {
					window.wpActiveEditor = t.attr( 'id' );
				}
			} );

			$( document ).on( 'change.cpf', '.cpf-logic-element', $.tmEPOAdmin.cpf_logic_element_onchange );
			$( document ).on( 'change.cpf', '.cpf-logic-operator', $.tmEPOAdmin.cpf_logic_operator_onchange );

			$( document ).on( 'change.cpf', '.activate-sections-logic, .activate-element-logic', function() {
				if ( $( this ).is( ':checked' ) ) {
					$( this ).closest( '.message0x0' ).find( '.builder-logic-div' ).show();
				} else {
					$( this ).closest( '.message0x0' ).find( '.builder-logic-div' ).hide();
				}
			} );

			$( document ).on( 'dblclick.cpf', '.tm-default-radio', function() {
				$( this ).removeAttr( 'checked' ).prop( 'checked', false );
			} );

			$( document ).on( 'click', '.tm-element-label,.tm-internal-label', function() {
				var t = $( this );
				var edit;
				var input;
				var value;
				var sectionIndex;
				var fieldIndex;

				$.tmEPOAdmin.current_edit_label = t;
				edit = t.addClass( 'tm-hidden' ).closest( '.tm-label-desc' ).addClass( 'tm-hidden' ).next( '.tm-label-desc-edit' ).removeClass( 'tm-hidden' );
				sectionIndex = t.closest( '.builder_wrapper' ).index();
				fieldIndex = $.tmEPOAdmin.find_index( TCBUILDER[ sectionIndex ].section.is_slider, t.closest( '.bitem' ) );
				if ( edit.is( '.tm-for-bitem' ) ) {
					input = edit.attr( 'data-element' );
					value = $.tmEPOAdmin.getFieldValue( TCBUILDER[ sectionIndex ].fields[ fieldIndex ], 'internal_name', true );
				} else if ( edit.is( '.tm-for-section' ) ) {
					input = 'sections';
					value = TCBUILDER[ sectionIndex ].section.sections_internal_name.default;
				}

				input = input + '_internal_name';
				input = $( "<input type='text' value='" + value + "' name='tm_meta[tmfbuilder][" + input + "][]' class='t tm-internal-name'>" );

				edit.append( input );
				input.focus();
			} );

			$( document ).mouseup( function( e ) {
				var container = $.tmEPOAdmin.current_edit_label;
				var input;
				var sectionIndex;
				var fieldIndex;
				var edit;

				if ( ! $.tmEPOAdmin.current_edit_label ) {
					return;
				}

				// if the target of the click isn't the container...
				// ... nor a descendant of the container
				if ( ! container.is( e.target ) && container.has( e.target ).length === 0 && ! $( e.target ).is( '.tm-internal-name' ) ) {
					input = container.closest( '.tm-label-desc' ).next( '.tm-label-desc-edit' ).find( '.tm-internal-name' );
					input.trigger( 'change' );

					if ( container.is( '.tm-internal-label' ) ) {
						container.html( input.val() ).removeClass( 'tm-hidden' );
					}
					if ( container.is( '.tm-element-label' ) ) {
						container.removeClass( 'tm-hidden' ).closest( '.tm-label-desc' ).find( '.tm-internal-label' ).html( input.val() );
					}

					container.closest( '.tm-label-desc' ).removeClass( 'tm-hidden' ).next( '.tm-label-desc-edit' ).addClass( 'tm-hidden' );

					if ( input.val() === '' ) {
						container.closest( '.tm-label-desc' ).removeClass( 'tc-has-value' ).addClass( 'tc-empty-value' );
						container.closest( '.tm-label-desc' ).find( '.tm-internal-label' ).html( container.closest( '.tm-label-desc' ).find( '.tm-element-label' ).html() );
						input.val( container.closest( '.tm-label-desc' ).find( '.tm-element-label' ).html() );
					} else {
						container.closest( '.tm-label-desc' ).removeClass( 'tc-empty-value' ).addClass( 'tc-has-value' );
					}
					sectionIndex = input.closest( '.builder_wrapper' ).index();
					fieldIndex = $.tmEPOAdmin.find_index( TCBUILDER[ sectionIndex ].section.is_slider, input.closest( '.bitem' ) );
					edit = input.closest( '.tm-label-desc-edit' );
					if ( edit.is( '.tm-for-bitem' ) ) {
						$.tmEPOAdmin.setFieldValue( sectionIndex, fieldIndex, 'internal_name', input.val(), true );
					} else if ( edit.is( '.tm-for-section' ) ) {
						TCBUILDER[ sectionIndex ].section.sections_internal_name.default = input.val();
					}

					input.remove();
					$.tmEPOAdmin.current_edit_label = false;
				}
			} );

			$( document ).on( 'click.cpf', '.cpf-add-rule', $.tmEPOAdmin.cpf_add_rule );
			$( document ).on( 'click.cpf', '.cpf-delete-rule', $.tmEPOAdmin.cpf_delete_rule );

			// Variation attribute terms fold button
			$( document ).on( 'click.cpf', '.tma-handle-wrap .tma-handle', $.tmEPOAdmin.builder_fold_onClick );

			$( document ).on( 'keyup change', '#temp_for_floatbox_insert .n[type=text]', function() {
				var $this = $( this );
				var value = $this.val();
				var regex;
				var newvalue;
				var offset;

				if ( woocommerce_admin && value !== newvalue ) {
					regex = new RegExp( '[^-0-9%.\\' + woocommerce_admin.mon_decimal_point + ']+', 'gi' );
					newvalue = value.replace( regex, '' );
					$this.val( newvalue );
					if ( $this.parent().find( '.wc_error_tip' ).length === 0 ) {
						offset = $this.position();
						$this.after( '<div class="wc_error_tip">' + woocommerce_admin.i18n_mon_decimal_error + '</div>' );
						$( '.wc_error_tip' )
							.css( 'left', offset.left + $this.width() - ( $this.width() / 2 ) - ( $( '.wc_error_tip' ).width() / 2 ) )
							.css( 'top', offset.top + $this.height() )
							.fadeIn( '100' );
					}
				}

				return this;
			} );

			$( document ).on( 'click', '.tc-enable-responsive', function() {
				var $this = $( this );
				var on = $this.find( '.on' );
				var off = $this.find( '.off' );
				var divs = $( '#temp_for_floatbox_insert' ).find( '.builder_responsive_div' );

				if ( $this.is( '.active' ) ) {
					$this.removeClass( 'active' );
					on.addClass( 'tm-hidden' );
					off.removeClass( 'tm-hidden' );
					divs.hide();
				} else {
					$this.addClass( 'active' );
					on.removeClass( 'tm-hidden' );
					off.addClass( 'tm-hidden' );
					divs.show();
				}
			} );

			$( document ).on( 'change.cpf', '.product-mode', function() {
				var $temp_for_floatbox_insert = $( '#temp_for_floatbox_insert' );
				var mode = $( this ).val();
				var productDefaultValue = $( '.product-default-value-search' );
				var productProductsSelector = $( '.product-products-selector' );

				if ( mode === 'product' ) {
					mode = 'products';
					if ( productProductsSelector.is( '.enhanced' ) && productProductsSelector.prop( 'multiple' ) ) {
						productProductsSelector.selectWoo( 'destroy' ).removeClass( 'enhanced' );
						productProductsSelector.prop( 'multiple', false );
						$.tmEPOAdmin.create_products_search( $temp_for_floatbox_insert );
					}
					productProductsSelector.prop( 'multiple', false );
				} else if ( mode === 'products' ) {
					if ( productProductsSelector.is( '.enhanced' ) && ! productProductsSelector.prop( 'multiple' ) ) {
						productProductsSelector.selectWoo( 'destroy' ).removeClass( 'enhanced' );
						productProductsSelector.prop( 'multiple', true );
						$.tmEPOAdmin.create_products_search( $temp_for_floatbox_insert );
					}
					productProductsSelector.prop( 'multiple', true );
				}
				if ( productDefaultValue.is( '.enhanced' ) ) {
					productDefaultValue.selectWoo( 'destroy' ).removeClass( 'enhanced' );
					if ( mode === 'products' ) {
						productDefaultValue.removeData( 'action' );
						productDefaultValue.addClass( 'enhanced-dropdown' );
						productProductsSelector.triggerHandler( 'change.cpf' );
						$.tmEPOAdmin.create_enhanced_dropdown( $temp_for_floatbox_insert );
					} else {
						productDefaultValue.data( 'action', 'wc_epo_search_products_in_categories' );
						productDefaultValue.addClass( 'wc-product-search' );
						$.tmEPOAdmin.create_products_search( $temp_for_floatbox_insert );
					}
				}
				$( '.product-' + mode + '-selector' ).trigger( 'change.cpf' );
			} );

			$( document ).on( 'change.cpf', '.product-default-value-search', function() {
				var $this = $( this );
				var productId = $this.val();
				var mode = $( '.product-mode' );
				var catArray;
				var data = {
					action: 'wc_epo_get_product_categories',
					product_id: productId,
					security: TMEPOGLOBALADMINJS.get_products_categories_nonce
				};
				var valid;

				if ( ! productId ) {
					return;
				}

				$.post( TMEPOGLOBALADMINJS.ajax_url, data, function( response ) {
					$this.data( 'categoryIds', 'success' === response.result ? response.category_ids : [] );

					if ( ! $this.data( 'categoryIds' ) ) {
						return;
					}

					if ( mode.val() === 'categories' && productId !== $this.attr( 'data-current-selected' ) ) {
						catArray = $( '.product-categories-selector' )
							.find( ':selected' )
							.toArray()
							.map( function( x ) {
								return parseInt( $( x ).val(), 10 );
							} );
						valid = false;
						$.each( $this.data( 'categoryIds' ), function( i, id ) {
							if ( $.inArray( parseInt( id, 10 ), catArray ) !== -1 ) {
								valid = true;
								return false;
							}
						} );
						if ( ! valid ) {
							$this.find( 'option' ).remove();
							this.removeAttr( 'data-current-selected' );
							this.removeData( 'current-selected-text' );
							this.removeData( 'categoryIds' );
							this.removeData( 'include' );
						} else {
							$this.attr( 'data-current-selected', productId );
							$this.data( 'current-selected-text', $this.find( ':selected' ).text() );
						}
					} else {
						$this.attr( 'data-current-selected', productId );
						$this.data( 'current-selected-text', $this.find( ':selected' ).text() );
					}
				} );
			} );

			$( document ).on( 'change.cpf', '.product-products-selector', function() {
				var element = $( this );
				var productDefaultValue = $( '.product-default-value-search' );
				var temp = element.find( ':selected' ).clone().removeAttr( 'selected' ).removeAttr( 'data-select2-id' );

				productDefaultValue.removeData( 'action' );
				productDefaultValue.find( 'option' ).remove();
				productDefaultValue.append( temp );
				productDefaultValue.val( productDefaultValue.attr( 'data-current-selected' ) ).trigger( 'change.cpf' );
			} );

			$( document ).on( 'change.cpf', '.product-categories-selector', function() {
				var element = $( this );
				var catArray = element
					.find( ':selected' )
					.toArray()
					.map( function( x ) {
						return parseInt( $( x ).val(), 10 );
					} );
				var productDefaultValue = $( '.product-default-value-search' );
				var valid;

				productDefaultValue.data( 'include', catArray );
				productDefaultValue.data( 'action', 'wc_epo_search_products_in_categories' );

				if ( productDefaultValue.data( 'categoryIds' ) ) {
					valid = false;
					$.each( productDefaultValue.data( 'categoryIds' ), function( i, id ) {
						if ( $.inArray( parseInt( id, 10 ), catArray ) !== -1 ) {
							valid = true;
							return false;
						}
					} );
					if ( ! valid ) {
						productDefaultValue.find( 'option' ).remove();
						productDefaultValue.removeAttr( 'data-current-selected' );
						productDefaultValue.removeData( 'current-selected-text' );
						productDefaultValue.removeData( 'categoryIds' );
					}
				}
			} );

			if ( $().ajaxChosen ) {
				$( 'select.ajax_chosen_select_tm_product_ids' ).ajaxChosen(
					{
						method: 'GET',
						url: TMEPOGLOBALADMINJS.ajax_url,
						dataType: 'json',
						afterTypeDelay: 100,
						data: {
							action: 'woocommerce_json_search_products',
							security: TMEPOGLOBALADMINJS.search_products_nonce
						}
					},
					function( data ) {
						var terms = {};

						$.each( data, function( i, val ) {
							terms[ i ] = val;
						} );

						return terms;
					}
				);
			}

			$( 'body' ).on( 'woocommerce-product-type-change', function() {
				var product_type = $( '#product-type' );
				var variation_element;

				$.tmEPOAdmin.init_sections_check();

				if ( product_type.length ) {
					if ( ! ( product_type.val() === 'variable' || product_type.val() === 'variable-subscription' ) ) {
						variation_element = $( '.builder_layout .element-variations' );
						if ( variation_element.length ) {
							variation_element.closest( '.builder_wrapper' ).remove();
							$.tmEPOAdmin.builder_reorder_multiple();
							$.tmEPOAdmin.section_logic_init();
							$.tmEPOAdmin.init_sections_check();
							$.tmEPOAdmin.toggle_variation_button();
							$.tmEPOAdmin.var_remove( 'tm-style-variation-added' );
						}
					}
					$.tmEPOAdmin.toggle_variation_button();
				}
			} );

			$( document ).on( 'click.cpf', '.meta-disable-categories', function() {
				$.tmEPOAdmin.disable_categories();
			} );
			$( document ).on(
				'change.cpf',
				'.product_page_tm-global-epo #product_catdiv input:checkbox, .product_page_tm-global-epo #tm_product_ids, .product_page_tm-global-epo #tm_enabled_options, .product_page_tm-global-epo #tm_disabled_options',
				function() {
					$.tmEPOAdmin.check_if_applied();
				}
			);

			$.tmEPOAdmin.add_events_done = 1;
		},

		initialitize: function() {
			$.tmEPOAdmin.setGlobalVariationObject( 'initialitize_on' );
		},

		initialitize_on: function() {
			$.tmEPOAdmin.isinit = true;
			$.tmEPOAdmin.pre_element_logic_init_obj = {};
			$.tmEPOAdmin.pre_element_logic_init_obj_options = {};

			$.tmEPOAdmin.pre_element_logic_init( true );
			$.tmEPOAdmin.pre_element_logic_init_done = true;

			$.tmEPOAdmin.is_original = TMEPOGLOBALADMINJS.is_original_post;
			$.tmEPOAdmin.current_edit_label = false;

			$.tmEPOAdmin.toggle_variation_button();

			$.tmEPOAdmin.add_events();

			// Check section logic
			$.tmEPOAdmin.check_section_logic();
			// Check element logic
			$.tmEPOAdmin.check_element_logic();
			// Start logic
			$.tmEPOAdmin.section_logic_start();
			$.tmEPOAdmin.element_logic_start();

			$( '.builder_wrapper.tm-slider-wizard' ).each( function() {
				var bw = $( this );
				$.tmEPOAdmin.create_slider( bw );
			} );

			// Move disabled categories checkbox
			$( '#taxonomy-product_cat' ).before( $( '#tc_disabled_categories' ).removeClass( 'hidden' ) );
			$.tmEPOAdmin.disable_categories();

			$( '#product_catdiv' ).before( $( '<div class="tc-info-box hidden"></div>' ) );
			$.tmEPOAdmin.check_if_applied();

			$.tmEPOAdmin.init_sections_check();

			$.tmEPOAdmin.fix_form_submit();

			$.tmEPOAdmin.make_resizables( $( '.builder_wrapper' ) );
			$.tmEPOAdmin.make_resizables( $( '.bitem' ) );

			$.tmEPOAdmin.pre_element_logic_init_done = false;
			$.tmEPOAdmin.isinit = false;
			$( '.builder_layout' ).removeClass( 'tm-hidden' );
		},

		initialitize_on_after: function() {
			$.tmEPOAdmin.isinit = true;
			$.tmEPOAdmin.pre_element_logic_init_obj = {};
			$.tmEPOAdmin.pre_element_logic_init_obj_options = {};

			$.tmEPOAdmin.pre_element_logic_init( true );
			$.tmEPOAdmin.pre_element_logic_init_done = true;

			$.tmEPOAdmin.is_original = TMEPOGLOBALADMINJS.is_original_post;
			$.tmEPOAdmin.current_edit_label = false;

			$.tmEPOAdmin.var_remove( 'tm-style-variation-added' );
			$.tmEPOAdmin.toggle_variation_button();

			$.tmEPOAdmin.add_sortables();

			// Check section logic
			$.tmEPOAdmin.check_section_logic();
			// Check element logic
			$.tmEPOAdmin.check_element_logic();
			// Start logic
			$.tmEPOAdmin.section_logic_start();
			$.tmEPOAdmin.element_logic_start();

			$( '.builder_wrapper.tm-slider-wizard' ).each( function() {
				var bw = $( this );
				$.tmEPOAdmin.create_slider( bw );
			} );

			$.tmEPOAdmin.init_sections_check();

			$.tmEPOAdmin.fix_form_submit();

			$.tmEPOAdmin.make_resizables( $( '.builder_wrapper' ) );
			$.tmEPOAdmin.make_resizables( $( '.bitem' ) );

			$.tmEPOAdmin.pre_element_logic_init_done = false;
			$.tmEPOAdmin.isinit = false;
		},

		make_resizables: function( obj ) {
			var keys = Object.keys( $.tmEPOAdmin.builder_size );
			var widthStops = [];
			var startLimit;

			obj = $( obj );
			if ( obj.length === 0 ) {
				return;
			}

			keys.forEach( function( i ) {
				widthStops.push( parseFloat( $.tmEPOAdmin.builder_size[ i ].replace( '%', '' ), 10 ) );
			} );

			obj.not( '.tma-nomove' )
				.toArray()
				.forEach( function( el ) {
					var bitemContainer;
					el = $( el );
					if ( el.is( '.bitem' ) ) {
						bitemContainer = el.closest( '.bitem_wrapper' ).first();
					} else {
						bitemContainer = el.closest( '.builder_layout' );
					}

					if ( el.is( '.ui-resizable' ) ) {
						el.resizable().resizable( 'destroy' );
					}
					el.resizable( {
						maxWidth: bitemContainer.width(),
						minWidth: bitemContainer.width() / 8,
						start: function( event, ui ) {
							ui.originalElement.addClass( 'tm-label-info-hidden' );
							startLimit = parseFloat(
								$.tmEPOAdmin.builder_size[
									ui.originalElement
										.attr( 'class' )
										.split( ' ' )
										.filter( function( x ) {
											return x.indexOf( 'w' ) === 0;
										} )[ 0 ]
								].replace( '%', '' ),
								10
							);
						},
						stop: function( event, ui ) {
							var percentage = ( 100 * ui.size.width ) / bitemContainer.width();
							var limit1 = widthStops.filter( function( x ) {
								return x <= percentage;
							} );
							var limit2 = widthStops.filter( function( x ) {
								return x >= percentage;
							} );
							var half;
							var bitem = ui.originalElement;
							var currentSize;
							var size;
							var text;
							var sectionIndex;
							var fieldIndex;

							bitem.removeClass( 'ui-highlight tm-label-info-hidden' );

							limit1 = limit1[ limit1.length - 1 ];
							limit2 = limit2[ 0 ];
							if ( limit2 === undefined ) {
								limit2 = widthStops[ widthStops.length - 1 ];
							}

							if ( limit1 === undefined ) {
								if ( startLimit >= limit2 ) {
									limit1 = widthStops[ widthStops.length - 1 ];
								} else {
									limit1 = widthStops[ 1 ];
								}
							}

							half = ( limit2 - limit1 ) / 2;

							if ( percentage - limit1 > half ) {
								size = 'w' + limit2.toString().replace( '.', '-' );
								bitem.css( 'width', limit2 + '%' );
							} else {
								size = 'w' + limit1.toString().replace( '.', '-' );
								bitem.css( 'width', limit1 + '%' );
							}

							currentSize = bitem
								.attr( 'class' )
								.split( ' ' )
								.filter( function( x ) {
									return x.indexOf( 'w' ) === 0;
								} )[ 0 ];
							text = $.tmEPOAdmin.builder_size[ size ];

							if ( size !== currentSize ) {
								bitem.removeClass( String( currentSize ) );
								bitem.addClass( String( size ) );
								if ( el.is( '.bitem' ) ) {
									bitem.find( '.bitem-inner .size' ).text( text );
								} else {
									bitem.find( '.section-inner .size' ).text( text );
								}
								sectionIndex = bitem.closest( '.builder_wrapper' ).index();
								fieldIndex = $.tmEPOAdmin.find_index( TCBUILDER[ sectionIndex ].section.is_slider, bitem );
								if ( el.is( '.bitem' ) ) {
									$.tmEPOAdmin.setFieldValue( sectionIndex, fieldIndex, 'div_size', size );
								} else {
									TCBUILDER[ sectionIndex ].section.sections_size.default = size;
									TCBUILDER[ sectionIndex ].size = text;
								}
							}

							if ( el.is( '.builder_wrapper' ) ) {
								$.tmEPOAdmin.make_resizables( el.find( '.bitem' ) );
							}
						},
						resize: function( event, ui ) {
							var percentage = ( 100 * ui.size.width ) / bitemContainer.width();
							var limit1 = widthStops.filter( function( x ) {
								return x <= percentage;
							} );
							var limit2 = widthStops.filter( function( x ) {
								return x >= percentage;
							} );
							var half;
							var bitem = ui.originalElement;

							limit1 = limit1[ limit1.length - 1 ];
							limit2 = limit2[ 0 ];
							if ( limit1 === undefined ) {
								limit1 = widthStops[ widthStops.length - 1 ];
							}
							if ( limit2 === undefined ) {
								limit2 = widthStops[ 0 ];
							}
							half = ( limit2 - limit1 ) / 2;

							if ( startLimit >= limit2 ) {
								if ( limit2 - half < percentage ) {
									bitem.removeClass( 'ui-highlight' );
								} else {
									bitem.addClass( 'ui-highlight' );
								}
							} else if ( limit2 - half > percentage ) {
								bitem.removeClass( 'ui-highlight' );
							} else {
								bitem.addClass( 'ui-highlight' );
							}

							ui.size.height = ui.originalSize.height;
						}
					} );
				} );
		},

		cpf_logic_element_onchange: function( e, ison ) {
			var $this = $( this );
			var logic;
			var element;
			var section;
			var type;
			var cpf_logic_value;
			var select;
			var selectoperator;
			var value;

			if ( e instanceof $ ) {
				$this = e;
			}
			if ( ison === undefined ) {
				ison = $( this ).closest( '.section_elements, .builder_wrapper, .bitem, .builder_element_wrap' );
				if ( ison.is( '.section_elements' ) || ison.is( '.builder_wrapper' ) ) {
					ison = false;
				} else {
					ison = true;
				}
			}

			if ( ! ison ) {
				logic = $.tmEPOAdmin.section_logic_object;
			} else {
				logic = $.tmEPOAdmin.element_logic_object;
			}

			element = $this.val();
			section = $this.children( 'option:selected' ).attr( 'data-section' );
			type = $this.children( 'option:selected' ).attr( 'data-type' );
			cpf_logic_value = logic;

			if ( cpf_logic_value[ section ] !== undefined ) {
				cpf_logic_value = logic[ section ].values;
				if ( cpf_logic_value[ element ] !== undefined ) {
					cpf_logic_value = logic[ section ].values[ element ];
				} else {
					cpf_logic_value = false;
				}
			} else {
				cpf_logic_value = false;
			}

			select = $this.closest( '.tm-logic-rule' ).find( '.tm-logic-value' );
			selectoperator = $this.closest( '.tm-logic-rule' ).find( '.cpf-logic-operator' );
			value = selectoperator.val();

			if ( cpf_logic_value ) {
				cpf_logic_value = $( cpf_logic_value );

				select.empty().append( cpf_logic_value );

				selectoperator.find( "[value='is']" ).show();
				selectoperator.find( "[value='isnot']" ).show();
				if ( type === 'variation' || type === 'multiple' ) {
					if ( value === 'startswith' || value === 'endswith' || value === 'greaterthan' || value === 'lessthan' || value === 'greaterthanequal' || value === 'lessthanequal' ) {
						selectoperator.val( 'isempty' );
					}
					selectoperator.find( "[value='startswith']" ).hide();
					selectoperator.find( "[value='endswith']" ).hide();
					selectoperator.find( "[value='greaterthan']" ).hide();
					selectoperator.find( "[value='lessthan']" ).hide();
					selectoperator.find( "[value='greaterthanequal']" ).hide();
					selectoperator.find( "[value='lessthanequal']" ).hide();
					selectoperator.trigger( 'change.cpf' );
				} else {
					selectoperator.find( "[value='startswith']" ).show();
					selectoperator.find( "[value='endswith']" ).show();
					selectoperator.find( "[value='greaterthan']" ).show();
					selectoperator.find( "[value='lessthan']" ).show();
					selectoperator.find( "[value='greaterthanequal']" ).show();
					selectoperator.find( "[value='lessthanequal']" ).show();
				}
			} else if ( element === section ) {
				if ( value === 'is' || value === 'isnot' || value === 'startswith' || value === 'endswith' || value === 'greaterthan' || value === 'lessthan' ) {
					selectoperator.val( 'isempty' );
				}
				selectoperator.find( "[value='is']" ).hide();
				selectoperator.find( "[value='isnot']" ).hide();
				selectoperator.find( "[value='startswith']" ).hide();
				selectoperator.find( "[value='endswith']" ).hide();
				selectoperator.find( "[value='greaterthan']" ).hide();
				selectoperator.find( "[value='lessthan']" ).hide();
				selectoperator.find( "[value='greaterthanequal']" ).hide();
				selectoperator.find( "[value='lessthanequal']" ).hide();
				selectoperator.trigger( 'change.cpf' );
			} else {
				selectoperator.find( "[value='is']" ).show();
				selectoperator.find( "[value='isnot']" ).show();
				selectoperator.find( "[value='startswith']" ).show();
				selectoperator.find( "[value='endswith']" ).show();
				selectoperator.find( "[value='greaterthan']" ).show();
				selectoperator.find( "[value='lessthan']" ).show();
				selectoperator.find( "[value='greaterthanequal']" ).show();
				selectoperator.find( "[value='lessthanequal']" ).show();
			}
		},

		cpf_logic_operator_onchange: function( e ) {
			var $this = $( this );
			var value;
			var select;

			if ( e instanceof $ ) {
				$this = e;
			}

			value = $this.val();
			select = $this.closest( '.tm-logic-rule' ).find( '.tm-logic-value' );

			if ( value === 'isempty' || value === 'isnotempty' ) {
				select.hide();
			} else {
				select.show();
			}
		},

		create_slider: function( bw ) {
			bw.tmtabs( {
				headers: '.tm-slider-wizard-headers',
				header: '.tm-slider-wizard-header',
				selectedtab: 0,
				showonhover: function() {
					return $.tmEPOAdmin.is_element_dragged;
				},
				useclasstohide: true,
				afteraddtab: function( h, t ) {
					var slides;
					var bwindex = bw.index();
					$.tmEPOAdmin.builder_items_sortable( t );

					if ( ! bw.is( '.tm-slider-wizard' ) ) {
						slides = '';
					} else {
						slides = bw
							.find( '.bitem_wrapper' )
							.map( function( i, e ) {
								return $( e ).children( '.bitem' ).not( '.pl2' ).length;
							} )
							.get()
							.join( ',' );
					}
					TCBUILDER[ bwindex ].section.sections_slides.default = slides;
				},
				deletebutton: true,
				deleteconfirm: true,
				beforedeletetab: function( $header, $tab ) {
					var bwindex = bw.index();
					var length = $tab.find( '.bitem' ).length;
					var index = $.tmEPOAdmin.find_index( true, $tab.find( '.bitem' ).first() );
					TCBUILDER[ bwindex ].fields.splice( index, length );
					TCBUILDER[ bwindex ].section.sections.default = parseInt( TCBUILDER[ bwindex ].section.sections.default, 10 ) - length;
					TCBUILDER[ bwindex ].section.sections.default = TCBUILDER[ bwindex ].section.sections.default.toString();
				},
				afterdeletetab: function() {
					var slides;
					var bwindex = bw.index();
					if ( ! bw.is( '.tm-slider-wizard' ) ) {
						slides = '';
					} else {
						slides = bw
							.find( '.bitem_wrapper' )
							.map( function( i, e ) {
								return $( e ).children( '.bitem' ).not( '.pl2' ).length;
							} )
							.get()
							.join( ',' );
					}
					TCBUILDER[ bwindex ].section.sections_slides.default = slides;
					$.tmEPOAdmin.builder_reorder_multiple();
					$.tmEPOAdmin.section_logic_init();
					$.tmEPOAdmin.init_sections_check();
				},
				beforemovetab: function( oldIndex, $tab ) {
					$.tmEPOAdmin.sliderTabMoveBefore = [];
					$tab.find( '.bitem' ).toArray().forEach( function( el ) {
						$.tmEPOAdmin.sliderTabMoveBefore.push( {
							field_index: $.tmEPOAdmin.find_index( true, $( el ) ),
							disabledFieldIndex: $.tmEPOAdmin.find_index( true, $( el ), '.bitem', '.element_is_disabled' )
						} );
					} );
				},
				aftermovetab: function( newIndex, oldIndex, $tab, initialIndex ) {
					var bwindex = bw.index();
					var length = $tab.find( '.bitem' ).length;
					var index = $.tmEPOAdmin.find_index( true, $tab.find( '.bitem' ).first() );

					TCBUILDER[ bwindex ].fields
						.splice.apply(
							TCBUILDER[ bwindex ].fields, [ index, 0 ].concat( TCBUILDER[ bwindex ].fields.splice( initialIndex, length ) )
						);

					TCBUILDER[ bwindex ].section.sections_slides.default = $( '.builder_wrapper' ).eq( bwindex )
						.find( '.bitem_wrapper' )
						.map( function( i, e ) {
							return $( e ).children( '.bitem' ).not( '.pl2' ).length;
						} )
						.get()
						.join( ',' );

					$.tmEPOAdmin.sliderTabMoveAfter = [];
					$tab.find( '.bitem' ).toArray().forEach( function( el ) {
						$.tmEPOAdmin.sliderTabMoveAfter.push( {
							field_index: $.tmEPOAdmin.find_index( true, $( el ) ),
							disabledFieldIndex: $.tmEPOAdmin.find_index( true, $( el ), '.bitem', '.element_is_disabled' )
						} );
					} );

					$.tmEPOAdmin.sliderTabMoveAfter.forEach( function( el, i ) {
						$.tmEPOAdmin.builder_items_sortable_obj.start.section = TCBUILDER[ bwindex ].section.sections_uniqid.default;
						$.tmEPOAdmin.builder_items_sortable_obj.start.section_eq = bwindex.toString();
						$.tmEPOAdmin.builder_items_sortable_obj.start.element = $.tmEPOAdmin.sliderTabMoveBefore[ i ].field_index.toString();
						$.tmEPOAdmin.builder_items_sortable_obj.start.disabledFieldIndex = $.tmEPOAdmin.sliderTabMoveBefore[ i ].disabledFieldIndex;

						$.tmEPOAdmin.builder_items_sortable_obj.end.section = TCBUILDER[ bwindex ].section.sections_uniqid.default;
						$.tmEPOAdmin.builder_items_sortable_obj.end.section_eq = bwindex.toString();
						$.tmEPOAdmin.builder_items_sortable_obj.end.element = el.field_index.toString();
						$.tmEPOAdmin.builder_items_sortable_obj.end.disabledFieldIndex = el.disabledFieldIndex;

						$.tmEPOAdmin.logic_reindex();
					} );

					$.tmEPOAdmin.builder_reorder_multiple();
					$.tmEPOAdmin.section_logic_init();
					$.tmEPOAdmin.init_sections_check();
				}
			} );

			TCBUILDER[ bw.index() ].section.is_slider = true;
		},

		sections_type_onChange: function( sectionIndex ) {
			var bitem_wrapper;
			var style = TCBUILDER[ sectionIndex ].section.sections_type.default;
			var tab1;
			var add;
			var builder_wrapper = $( '.builder_wrapper' ).eq( sectionIndex );
			var btitle;

			if ( style === 'slider' && ! builder_wrapper.hasClass( 'tm-slider-wizard' ) ) {
				bitem_wrapper = builder_wrapper.find( '.bitem_wrapper' );
				btitle = builder_wrapper.find( '.btitle' );

				builder_wrapper.addClass( 'tm-slider-wizard' );
				tab1 = '<div class="tm-box"><h4 class="tm-slider-wizard-header" data-id="tm-slide0">1</h4></div>';
				add = '<div class="tm-box tm-add-box"><h4 class="tm-add-tab"><span class="tcfa tcfa-plus"></span></h4></div>';
				btitle.after( '<div class="transition tm-slider-wizard-headers">' + tab1 + add + '</div>' );
				bitem_wrapper.addClass( 'tm-slider-wizard-tab tm-slide0' );

				$.tmEPOAdmin.create_slider( builder_wrapper );

				TCBUILDER[ sectionIndex ].section.sections_slides.default = builder_wrapper
					.find( '.bitem_wrapper' )
					.map( function( i, e ) {
						return $( e ).children( '.bitem' ).not( '.pl2' ).length;
					} )
					.get()
					.join( ',' );

				TCBUILDER[ sectionIndex ].section.is_slider = true;
			} else if ( style !== 'slider' && builder_wrapper.hasClass( 'tm-slider-wizard' ) ) {
				builder_wrapper.find( '.bitem_wrapper' ).wrapAll( '<div class="tmtemp"></div>' );
				builder_wrapper.find( '.bitem_wrapper .bitem' ).appendTo( builder_wrapper.find( '.tmtemp' ) );
				builder_wrapper.find( '.bitem_wrapper' ).remove();
				builder_wrapper.find( '.tmtemp' ).addClass( 'bitem_wrapper' ).removeClass( 'tmtemp' );
				$.tmEPOAdmin.builder_items_sortable( builder_wrapper.find( '.bitem_wrapper' ) );
				builder_wrapper.find( '.tm-slider-wizard-headers' ).remove();
				builder_wrapper.removeClass( 'tm-slider-wizard' );

				TCBUILDER[ sectionIndex ].section.sections_slides.default = '';
				TCBUILDER[ sectionIndex ].section.is_slider = false;
			}
		},

		variation_events_success: function() {
			setTimeout( function() {
				$.tmEPOAdmin.setGlobalVariationObject( 'reindex' );
			}, 600 );
			$( document ).unbind( 'ajaxSuccess', $.tmEPOAdmin.variation_events_success );
			$.tmEPOAdmin.var_remove( 'tma-remove_variation-added' );
		},

		tm_variations_check_events_success: function() {
			setTimeout( function() {
				$.tmEPOAdmin.tm_variations_check_for_changes = 1;
				$.tmEPOAdmin.tm_variations_check();
			}, 600 );
			$( document ).unbind( 'ajaxSuccess', $.tmEPOAdmin.tm_variations_check_events_success );
			$.tmEPOAdmin.var_remove( 'tma-remove_variation-added' );
		},

		add_variation_events: function() {
			if ( $.tmEPOAdmin.var_is( 'tma-variation-events-added' ) === true ) {
				return;
			}
			if ( $.tmEPOAdmin.var_is( 'tma-remove_variation-added' ) !== true ) {
				$( '#variable_product_options' ).on( 'click.tma', '.remove_variation', function() {
					$( document ).ajaxSuccess( $.tmEPOAdmin.variation_events_success );
					$.tmEPOAdmin.var_is( 'tma-remove_variation-added', true );

					$( document ).ajaxSuccess( $.tmEPOAdmin.tm_variations_check_events_success );
				} );
			}
			if ( $.tmEPOAdmin.var_is( 'tma-remove_variation-added' ) !== true ) {
				$( '.wc-metaboxes-wrapper' ).on( 'click', 'a.bulk_edit', function() {
					var bulk_edit = $( 'select#field_to_edit' ).val();
					if ( bulk_edit === 'delete_all' ) {
						$( document ).ajaxSuccess( $.tmEPOAdmin.variation_events_success );
						$.tmEPOAdmin.var_is( 'tma-remove_variation-added', true );

						$( document ).ajaxSuccess( $.tmEPOAdmin.tm_variations_check_events_success );
					}
				} );
			}
			$( '#variable_product_options' ).on( 'woocommerce_variations_added', function() {
				$.tmEPOAdmin.setGlobalVariationObject( 'reindex' );
				$( document ).ajaxSuccess( $.tmEPOAdmin.tm_variations_check_events_success );
			} );

			$( '#woocommerce-product-data' ).on( 'woocommerce_variations_saved', function() {
				$.tmEPOAdmin.setGlobalVariationObject( 'reindex' );
				$( document ).ajaxSuccess( $.tmEPOAdmin.tm_variations_check_events_success );
			} );

			$( document ).on( 'click.cpf', '.save_attributes', function() {
				$( document ).ajaxSuccess( $.tmEPOAdmin.tm_variations_check_events_success );
			} );

			$.tmEPOAdmin.var_is( 'tma-variation-events-added', true );
		},

		toggle_variation_button: function() {
			var product_type = $( '#product-type' );
			var variation_element;
			var is_forced;
			var variation_element_builder_wrapper;

			if ( product_type.length ) {
				if ( product_type.val() === 'variable' || product_type.val() === 'variable-subscription' ) {
					$( '.builder_add_section' ).addClass( 'inline' );
					$( '.builder_add_variation' ).addClass( 'inline' ).removeClass( 'tm-hidden' );
					$( '.tma-variations-wrap' ).removeClass( 'tm-hidden' );
					$.tmEPOAdmin.add_variation_events();
					variation_element = $( '.builder_layout .element-variations' );

					is_forced = false;
					if ( ! variation_element.length ) {
						$.tmEPOAdmin.var_is( 'tm-style-variation-forced', true );
						$.tmEPOAdmin.builder_add_variation_onClick();
						is_forced = true;
						variation_element = $( '.builder_layout .element-variations' );
					} else if ( $.tmEPOAdmin.getFieldValue( $.tmEPOAdmin.variationSection, 'variations_disabled' ) === '1' ) {
						is_forced = true;
						variation_element = $( '.builder_layout .element-variations' );
						$.tmEPOAdmin.var_is( 'tm-style-variation-forced', true );
					}
					if ( variation_element.length ) {
						variation_element_builder_wrapper = variation_element.closest( '.builder_wrapper' );
						variation_element_builder_wrapper.find( '.tm-add-element-action,.tmicon.clone,.tmicon.size,.tmicon.move,.tmicon.plus,.tmicon.minus' ).remove();

						variation_element_builder_wrapper.addClass( 'tma-nomove tma-variations-wrap' );

						$.tmEPOAdmin.var_is( 'tm-style-variation-added', true );

						variation_element.addClass( 'tma-nomove' );
						variation_element.find( '.bitem-settings .size,.bitem-settings .clone,.tm-label-move,.bitem-settings .plus,.bitem-settings .minus,.tm-label-delete' ).remove();

						if ( is_forced ) {
							$.tmEPOAdmin.setFieldValue( $.tmEPOAdmin.variationSectionIndex, $.tmEPOAdmin.variationFieldIndex, 'variations_disabled', '1' );
							$( '.builder_add_section' ).addClass( 'inline' );
							$( '.builder_add_variation' ).addClass( 'inline' ).removeClass( 'tm-hidden' );
							$( '.tma-variations-wrap' ).addClass( 'tm-hidden' );
						} else {
							$.tmEPOAdmin.setFieldValue( $.tmEPOAdmin.variationSectionIndex, $.tmEPOAdmin.variationFieldIndex, 'variations_disabled', '' );
							$( '.builder_add_section' ).removeClass( 'inline' );
							$( '.builder_add_variation' ).removeClass( 'inline' ).addClass( 'tm-hidden' );
							$( '.tma-variations-wrap' ).removeClass( 'tm-hidden' );
						}

						$.tmEPOAdmin.tm_variations_check_for_changes = 1;
						$.tmEPOAdmin.tm_variations_check();
					}
				} else {
					$( '.builder_add_section' ).removeClass( 'inline' );
					$( '.builder_add_variation' ).removeClass( 'inline' ).addClass( 'tm-hidden' );
				}
			}
		},

		// Encode the JS Options array as an array of names and values.
		tcSerializeJsOptionsArray: function( options ) {
			var sections = [].concat.apply(
				[],
				options.map( function( x ) {
					return Object.keys( x.section )
						.map( function( key ) {
							if ( x && typeof x.section[ key ] === 'object' ) {
								return x.section[ key ];
							}
							return null;
						} )
						.filter( function( el ) {
							return el !== null && el !== undefined;
						} );
				} )
			);

			var fields = [].concat.apply(
				[],
				options.map( function( x ) {
					return [].concat.apply(
						[],
						x.fields.map( function( y ) {
							return [].concat.apply(
								[],
								y
									.filter( function( el ) {
										return el !== null;
									} )
									.map( function( z ) {
										if ( z && z.id === 'multiple' ) {
											return [].concat.apply( [], z.multiple );
										}
										return z;
									} )
							);
						} )
					);
				} )
			);

			options = [].concat.apply( [], [ sections, fields ] );

			options = options
				.map( function( y ) {
					if ( y ) {
						if ( y.checked !== undefined ) {
							if ( y.checked === true ) {
								return { name: y.tags.name, value: y.default };
							}
							return { name: y.tags.name, value: '' };
						}
						return { name: y.tags.name, value: y.default };
					}
					return {};
				} )
				.filter( function( el ) {
					return el !== null && el !== undefined;
				} );

			return options;
		},

		// convert element to a valid JSON object
		tcSerializeJsOptionsObject: function( options ) {
			var o = {};
			var a = $.tmEPOAdmin.tcSerializeJsOptionsArray( options );

			if ( $( '#tm_meta_priority' ).length ) {
				a.push( { name: 'tm_meta[priority]', value: $( '#tm_meta_priority' ).val() } );
			}

			$.each( a, function() {
				if ( o[ this.name ] !== undefined ) {
					if ( this.name.slice( -2 ) === '[]' ) {
						if ( ! o[ this.name ].push ) {
							o[ this.name ] = [ o[ this.name ] ];
						}
						o[ this.name ].push( this.value || '' );
					} else if ( o[ this.name ] === '' ) {
						o[ this.name ] = this.value || '';
					}
				} else if ( this.value !== null && this.value !== undefined && this.value.push ) {
					o[ this.name ] = [ this.value ];
				} else {
					o[ this.name ] = this.value || '';
				}
			} );

			return o;
		},

		prepare_for_json: function( data ) {
			var result = {};
			var arr;
			var value;
			var must_be_array;

			Object.keys( data ).forEach( function( i ) {
				if ( i.indexOf( 'tm_meta[' ) === 0 ) {
					arr = i.split( /[[\]]{1,2}/ );
					arr.pop();
					arr = arr.map( function( item ) {
						return item === '' ? null : item;
					} );
					if ( arr.length > 0 && arr[ arr.length - 1 ] === null ) {
						must_be_array = true;
					} else {
						must_be_array = false;
					}
					arr = arr.filter( function( v ) {
						if ( v !== null && v !== undefined ) {
							return v;
						}
						return null;
					} );
					if ( typeof data[ i ] !== 'object' && must_be_array ) {
						value = [ data[ i ] ];
					} else {
						value = data[ i ];
					}
					result = $.tmEPOAdmin.constructObject( arr, value, result );
				}
			} );

			return result;
		},

		constructObject: function( a, final_value, obj ) {
			var val = a.shift();

			if ( a.length > 0 ) {
				if ( ! Object.prototype.hasOwnProperty.call( obj, val ) ) {
					obj[ val ] = {};
				}
				obj[ val ] = $.tmEPOAdmin.constructObject( a, final_value, obj[ val ] );
			} else {
				obj[ val ] = final_value;
			}

			return obj;
		},

		create_tm_meta_serialized: function() {
			var data;
			var name;
			var tm_meta_serialized;
			var previewField = $( 'input#wp-preview' );
			var $tc_form = $( '#tmformfieldsbuilderwrap' );

			$( '.tm_meta_serialized' ).remove();
			$( '.tm_meta_serialized_wpml' ).remove();

			if ( ! $.tmEPOAdmin.is_original ) {
				name = 'tm_meta_serialized_wpml';
			} else {
				name = 'tm_meta_serialized';
			}

			data = $.tmEPOAdmin.tm_escape( JSON.stringify( $.tmEPOAdmin.prepare_for_json( $.tmEPOAdmin.tcSerializeJsOptionsObject( TCBUILDER ) ) ) );

			tm_meta_serialized = $( "<textarea class='tm_meta_serialized tm-hidden' name='" + name + "'></textarea>" ).val( data );
			$tc_form.prepend( tm_meta_serialized );

			if ( previewField.length > 0 && previewField.val() !== '' ) {
				$( '.tm_meta_serialized' ).remove();
			}
		},

		fix_form_submit: function() {
			var $post = $( '#post' );
			var found;
			var subscribe;
			var sub;

			if ( $post.length === 1 ) {
				$post.on( 'submit', function() {
					$.tmEPOAdmin.create_tm_meta_serialized();
					return true; // ensure form still submits
				} );
			} else if ( wp.data && wp.data.subscribe !== undefined ) {
				found = false;
				subscribe = wp.data.subscribe;

				if ( typeof subscribe === 'function' ) {
					sub = function() {
						var isSavingPost = wp.data.select( 'core/editor' ).isSavingPost();
						var didPostSaveRequestSucceed = wp.data.select( 'core/editor' ).didPostSaveRequestSucceed();
						var didPostSaveRequestFail = wp.data.select( 'core/editor' ).didPostSaveRequestFail();

						if ( ! found && isSavingPost ) {
							found = true;
							$.tmEPOAdmin.create_tm_meta_serialized();
						}
						if ( found && ( didPostSaveRequestSucceed || didPostSaveRequestFail ) ) {
							found = false;
							setTimeout( function() {
								$( '.tm_meta_serialized' ).remove();
								$( '.tm_meta_serialized_wpml' ).remove();
							}, 600 );
						}
					};

					subscribe( sub );
				}
			}
		},

		init_sections_check: function() {
			var length = $( '.builder_wrapper' ).length;

			if ( length === 1 ) {
				if ( $( '.builder_wrapper.tma-variations-wrap.tm-hidden' ).length ) {
					length = 0;
				}
			}

			if ( ! length ) {
				$( '.builder-add-section-action' ).hide();
				$( '.builder_selector' ).hide();
				$( '.tc-welcome' ).show();
				$( '.builder_layout' ).hide();
			} else {
				$( '.builder-add-section-action' ).show();
				$( '.builder_selector' ).show();
				$( '.tc-welcome' ).hide();
				$( '.builder_layout' ).show();
			}
		},

		disable_categories: function() {
			if ( $( '.meta-disable-categories' ).is( ':checked' ) ) {
				$( '#taxonomy-product_cat' ).slideUp();
			} else {
				$( '#taxonomy-product_cat' ).slideDown();
			}
		},

		check_if_applied: function() {
			var nocat;
			var cat;
			var tm_product_ids;
			var tm_enabled_options;
			var tm_disabled_options;

			if ( ! $( 'body' ).is( '.product_page_tm-global-epo' ) ) {
				return;
			}

			nocat = $( '#tm_meta_disable_categories:checked' ).length > 0;
			cat = $( '#product_catdiv input:checkbox' ).not( $( '#tm_meta_disable_categories' ) ).filter( ':checked' ).length > 0;
			tm_product_ids = $( '#tm_product_ids' ).val();
			tm_enabled_options = $( '#tm_enabled_options' ).val();
			tm_disabled_options = $( '#tm_disabled_options' ).val();
			tm_product_ids = tm_product_ids && tm_product_ids !== null ? tm_product_ids.length > 0 : false;
			tm_enabled_options = tm_enabled_options && tm_enabled_options !== null ? tm_enabled_options.length > 0 : false;
			tm_disabled_options = tm_disabled_options && tm_disabled_options !== null ? tm_disabled_options.length > 0 : false;
			if ( nocat ) {
				if ( tm_product_ids || tm_enabled_options || tm_disabled_options ) {
					$( '.tc-info-box' ).removeClass( 'tc-error tc-all-products' ).addClass( 'hidden' ).html( '' );
				} else {
					$( '.tc-info-box' ).removeClass( 'hidden tc-all-products' ).addClass( 'tc-error' ).html( TMEPOGLOBALADMINJS.i18n_form_not_applied_to_all );
				}
			} else if ( cat ) {
				$( '.tc-info-box' ).removeClass( 'tc-error tc-all-products' ).addClass( 'hidden' ).html( '' );
			} else {
				$( '.tc-info-box' ).removeClass( 'hidden error' ).addClass( 'tc-all-products' ).html( TMEPOGLOBALADMINJS.i18n_form_is_applied_to_all );
			}
		},

		check_section_logic: function( section ) {
			var sections;

			if ( ! ( Number.isFinite( section ) || section instanceof $ ) && $.tmEPOAdmin.isinit && $.tmEPOAdmin.done_check_section_logic ) {
				return;
			}

			if ( section instanceof $ ) {
				section = parseInt( section.index(), 10 );
			}

			if ( section === undefined ) {
				sections = TCBUILDER;
			} else {
				sections = [];
				section = $.epoAPI.math.toFloat( section );
				sections[ section ] = TCBUILDER[ section ];
			}

			Object.keys( sections ).forEach( function( i ) {
				var current_section = sections[ i ];
				var this_section_id;
				if ( current_section.section !== undefined ) {
					this_section_id = current_section.section.sections_uniqid.default;
					if ( ! this_section_id || this_section_id === '' || this_section_id === undefined || this_section_id === false ) {
						TCBUILDER[ i ].section.sections_uniqid.default = tcCreateUniqid( '', true );
					}
				}
			} );

			$.tmEPOAdmin.done_check_section_logic = true;
		},

		check_element_logic: function( element, section ) {
			var uniqids = [];
			var all = false;
			var sections;
			var fields;
			var field;
			var elementType;

			if ( ! ( Number.isFinite( element ) || element instanceof $ ) && $.tmEPOAdmin.isinit && $.tmEPOAdmin.done_check_section_logic ) {
				return;
			}

			if ( element instanceof $ ) {
				section = element.closest( '.builder_wrapper' ).index();
				element = $.tmEPOAdmin.find_index( TCBUILDER[ section ].section.is_slider, element );
			}

			if ( section === undefined && element !== undefined ) {
				return;
			}

			if ( element === undefined ) {
				sections = TCBUILDER;
				all = true;
			} else {
				sections = [];
				section = $.epoAPI.math.toFloat( section );
				element = $.epoAPI.math.toFloat( element );
				sections[ section ] = $.extend( true, {}, TCBUILDER[ section ] );
				field = sections[ section ].fields[ element ];
				sections[ section ].fields = [];
				sections[ section ].fields[ element ] = field;
			}

			Object.keys( sections ).forEach( function( i ) {
				fields = sections[ i ].fields;

				Object.keys( fields ).forEach( function( ii ) {
					var this_element_id;

					field = fields[ ii ];

					elementType = $.tmEPOAdmin.getFieldValue( field, 'element_type' );
					this_element_id = $.tmEPOAdmin.getFieldValue( field, 'uniqid', elementType );

					if ( ( all && uniqids.indexOf( this_element_id ) !== -1 ) || ! this_element_id || this_element_id === '' || this_element_id === undefined || this_element_id === false ) {
						$.tmEPOAdmin.setFieldValue( i, ii, 'uniqid', tcCreateUniqid( '', true ), elementType );
					}
					if ( all ) {
						uniqids.push( $.tmEPOAdmin.getFieldValue( TCBUILDER[ i ].fields[ ii ], 'uniqid', elementType ) );
					}
				} );
			} );

			$.tmEPOAdmin.done_check_section_logic = true;
		},

		section_logic_start: function( section ) {
			var sections;
			var rules;

			if ( section === undefined ) {
				sections = TCBUILDER;
			} else {
				sections = [];
				section = $.epoAPI.math.toFloat( section );
				sections[ section ] = $.extend( true, {}, TCBUILDER[ section ] );
			}

			Object.keys( sections ).forEach( function( i ) {
				$.tmEPOAdmin.section_logic_init( i );
				try {
					rules = sections[ i ].section.sections_clogic.default || 'null';
					rules = $.epoAPI.util.parseJSON( rules );
					rules = $.tmEPOAdmin.logic_check_section_rules( rules );

					TCBUILDER[ i ].section.sections_clogic.default = JSON.stringify( rules );

					TCBUILDER[ i ].section.rules_toggle = rules.toggle;
					TCBUILDER[ i ].section.rules_what = rules.what;
				} catch ( err ) {

				}
			} );
		},

		element_logic_start: function( element, section ) {
			var rules;
			var sections;
			var fields;
			var field;
			var elementType;

			if ( element === undefined ) {
				sections = TCBUILDER;
			} else {
				sections = [];
				section = $.epoAPI.math.toFloat( section );
				element = $.epoAPI.math.toFloat( element );
				sections[ section ] = $.extend( true, {}, TCBUILDER[ section ] );
				field = sections[ section ].fields[ element ];
				sections[ section ].fields = [];
				sections[ section ].fields[ element ] = field;
			}

			Object.keys( sections ).forEach( function( i ) {
				fields = sections[ i ].fields;
				if ( fields !== undefined ) {
					Object.keys( fields ).forEach( function( ii ) {
						field = fields[ ii ];

						$.tmEPOAdmin.element_logic_init( ii, i );

						try {
							elementType = $.tmEPOAdmin.getFieldValue( field, 'element_type' );
							rules = $.tmEPOAdmin.getFieldValue( field, 'clogic', elementType ) || 'null';
							rules = $.epoAPI.util.parseJSON( rules );
							rules = $.tmEPOAdmin.logic_check_element_rules( rules, i, ii );
							$.tmEPOAdmin.setFieldValue( i, ii, 'clogic', JSON.stringify( rules ), elementType );
						} catch ( err ) {

						}
					} );
				}
			} );
		},

		panels_reorder: function( obj ) {
			$( obj )
				.children( '.options_wrap' )
				.each( function( i, el ) {
					$( el ).find( '.tm-default-radio,.tm-default-checkbox' ).val( i );
				} );
		},

		// Options sortable
		panels_sortable: function( obj ) {
			if ( $( obj ).length === 0 || ! $.tmEPOAdmin.is_original ) {
				return;
			}

			obj.sortable( {
				handle: '.move',
				cancel: 'input,select,button:not(.move)',
				cursor: 'move',
				tolerance: 'pointer',
				forcePlaceholderSize: true,
				placeholder: 'panel_wrap pl',
				stop: function( e, ui ) {
					$.tmEPOAdmin.panels_reorder( $( ui.item ).closest( '.panels_wrap' ) );
				}
			} );
		},

		// Delete all options button
		builder_panel_delete_all_onClick: function( e ) {
			var tcpagination = $( this ).closest( '.onerow' ).find( '.tcpagination' );
			var panels_wrap = $( '.flasho.tm_wrapper' ).find( '.panels_wrap' );
			var options_wrap;

			e.preventDefault();
			$( this ).trigger( 'hideTtooltip' );

			tcpagination.tcPagination( 'destroy' );

			if ( panels_wrap.children().length > 1 ) {
				options_wrap = panels_wrap.find( '.options_wrap' );
				options_wrap.each( function( i ) {
					if ( i === 0 ) {
						return true;
					}
					$( this ).remove();
					panels_wrap.find( '.numorder' ).each( function( i2 ) {
						$( this ).html( parseInt( i2, 10 ) + 1 );
					} );
				} );
				options_wrap.find( 'input' ).val( '' );
				$.tmEPOAdmin.panels_reorder( panels_wrap );
			}
		},

		// Remove image
		builder_image_delete_onClick: function( e ) {
			var $this = $( this );
			e.preventDefault();
			$this.trigger( 'hideTtooltip' );
			$this
				.closest( '.tm_cell_images' )
				.find( 'input.' + $this.attr( 'rel' ) )
				.val( '' );
			$this.closest( 'span' ).find( 'img' ).attr( 'src', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=' );
		},

		// Delete options button
		builder_panel_delete_onClick: function( e ) {
			var _panels_wrap = $( this ).closest( '.panels_wrap' );

			e.preventDefault();
			$( this ).trigger( 'hideTtooltip' );

			if ( _panels_wrap.children().length > 1 ) {
				$( this )
					.closest( '.options_wrap' )
					.css( {
						margin: '0 auto'
					} )
					.animate(
						{
							opacity: 0,
							height: 0,
							width: 0
						},
						300,
						function() {
							$( this ).remove();
							_panels_wrap.find( '.numorder' ).each( function( i2 ) {
								$( this ).html( parseInt( i2, 10 ) + 1 );
							} );
							_panels_wrap.children( '.options_wrap' ).each( function( k ) {
								$( this )
									.find( '[id]' )
									.each( function() {
										var _name = $( this ).attr( 'name' ).replace( /[[\]]/g, '' );
										$( this ).attr( 'id', _name + '_' + k );
									} );
							} );
							$.tmEPOAdmin.panels_reorder( _panels_wrap );
						}
					);
			}
		},

		// Mass add options button
		builder_panel_mass_add_onClick: function( e ) {
			var html;
			var element = $( this );

			e.preventDefault();
			if ( element.is( '.disabled' ) ) {
				return;
			}
			element.addClass( 'disabled' );
			html =
				'<div class="tm-panel-populate-wrapper">' +
				'<textarea class="tm-panel-populate"></textarea>' +
				'<button type="button" class="tc tc-button builder-panel-populate">' +
				TMEPOGLOBALADMINJS.i18n_populate +
				'</button>' +
				'</div>';
			element.after( html );
		},

		// Populate options button
		builder_panel_populate_onClick: function( e ) {
			var panels_wrap = $( '.flasho.tm_wrapper' ).find( '.panels_wrap' );
			var _last = panels_wrap.children();
			var full_element = $( '' );
			var lines = $( '.tm-panel-populate' ).val().split( /\n/ );
			var texts = [];

			e.preventDefault();
			$( this ).remove();

			lines.forEach( function( value ) {
				if ( /\S/.test( value ) ) {
					texts.push( $.epoAPI.util.trim( value ) );
				}
			} );

			texts.forEach( function( value ) {
				var line = value.split( '|' );
				var len = line.length;
				var toadd;

				if ( len !== 0 ) {
					if ( len === 1 ) {
						line[ 1 ] = 0;
					}
					line[ 0 ] = $.epoAPI.util.trim( line[ 0 ] );
					line[ 1 ] = parseFloat( $.epoAPI.util.trim( line[ 1 ] ) );
					if ( ! Number.isFinite( line[ 1 ] ) ) {
						line[ 1 ] = '';
					}
					toadd = $.tmEPOAdmin.add_panel_row( line, panels_wrap, _last );

					full_element = full_element.add( toadd );
				}
			} );
			if ( full_element.length ) {
				panels_wrap.append( full_element );
				full_element.find( '.tm_option_enabled' ).prop( 'checked', true ).val( '1' ).trigger( 'changechoice' );
				$.tcToolTip( full_element.find( '.tm-tooltip' ) );
			}
			$( '.builder-panel-mass-add' ).removeClass( 'disabled' );
			$( '.tm-panel-populate-wrapper' ).remove();
			$.tmEPOAdmin.paginattion_init( 'last' );
			$.tmEPOAdmin.tm_upload();
		},

		add_panel_row: function( line, panels_wrap, _last ) {
			var _clone = _last.last().tcClone();

			if ( _clone ) {
				$.tmEPOAdmin.builder_clone_elements_after_events( _clone );
				_clone.find( '[name]' ).val( '' );
				_clone.find( '.tm_option_title' ).val( line[ 0 ] );
				_clone.find( '.tm_option_value' ).val( $( '<div/>' ).html( line[ 0 ] ).text() );
				_clone.find( '.tm_option_price' ).val( line[ 1 ] );
				if ( line[ 2 ] ) {
					_clone.find( '.tm_option_price_type' ).val( line[ 2 ] );
					if ( _clone.find( '.tm_option_price_type' ).val() === false ) {
						_clone.find( '.tm_option_price_type' ).val( '' );
					}
				}
				if ( line[ 3 ] ) {
					_clone.find( '.tm_option_description' ).val( line[ 3 ] );
					if ( _clone.find( '.tm_option_description' ).val() === false ) {
						_clone.find( '.tm_option_description' ).val( '' );
					}
				}
				_clone.find( '.tm_upload_image img' ).attr( 'src', '' );
				_clone.find( 'input.tm_option_image' ).val( '' );
				_clone.find( '.tm-default-radio,.tm-default-checkbox' ).removeAttr( 'checked' ).prop( 'checked', false ).val( _last.length );

				return _clone;
			}

			return $( '' );
		},

		// Add options button
		builder_panel_add_onClick: function( e ) {
			var panels_wrap = $( this ).prev( '.panels_wrap' );
			var _last = panels_wrap.children();
			var _clone = _last.last().tcClone();

			e.preventDefault();
			if ( _clone ) {
				_clone.find( '[name]' ).val( '' );
				_clone.find( '.tm_upload_image img' ).attr( 'src', '' );
				_clone.find( 'input.tm_option_image' ).val( '' );
				_clone.find( '.tm-default-radio,.tm-default-checkbox' ).removeAttr( 'checked' ).prop( 'checked', false ).val( _last.length );

				$.tmEPOAdmin.builder_clone_elements_after_events( _clone );

				panels_wrap.append( _clone );
				_clone.find( '.tm_option_enabled' ).prop( 'checked', true ).val( '1' ).trigger( 'changechoice' );

				$.tcToolTip( _clone.find( '.tm-tooltip' ) );
				$.tmEPOAdmin.paginattion_init( 'last' );
				$.tmEPOAdmin.tm_upload();
			}
		},

		// Section add button
		builder_add_section_onClick: function( e, ap, nt ) {
			var _template = $.epoAPI.template.html( templateEngine.tc_builder_section, {} );
			var _clone;
			var fieldObject;
			var sectionObject;
			var sectionField;

			if ( e ) {
				e.preventDefault();
			}

			if ( _template ) {
				_clone = $( _template );
				if ( _clone ) {
					_clone.addClass( 'w100' );
					//_clone.addClass("appear");
					_clone.find( '.tm-builder-sections-uniqid' ).val( tcCreateUniqid( '', true ) );

					fieldObject = $.tmEPOAdmin.getFieldObject( _clone );
					sectionField = {};
					Object.keys( fieldObject ).forEach( function( i ) {
						sectionField[ fieldObject[ i ].id ] = fieldObject[ i ];
					} );
					sectionObject = {
						fields: [],
						section: sectionField,
						size: $.tmEPOAdmin.builder_size[ sectionField.sections_size.default ],
						sections_internal_name: sectionField.sections_internal_name.default
					};
					_clone.find( '.section_elements' ).empty();
					_clone.find( '.tm-internal-name' ).remove();
					if ( ap ) {
						_clone.appendTo( '.builder_layout' );
						TCBUILDER.push( sectionObject );
					} else if ( $( '.builder_layout .tma-variations-wrap' ).length > 0 ) {
						$( '.builder_layout .tma-variations-wrap' ).after( _clone );
						TCBUILDER.splice( 1, 0, sectionObject );
					} else {
						_clone.prependTo( '.builder_layout' );
						TCBUILDER.unshift( sectionObject );
					}

					$.tmEPOAdmin.check_section_logic( _clone );
					$.tmEPOAdmin.section_logic_init( _clone );

					if ( ! nt ) {
						$.tmEPOAdmin.builder_items_sortable( _clone.find( '.bitem_wrapper' ) );
						$.tmEPOAdmin.builder_reorder_multiple();
						if ( $( this ).is( 'a' ) ) {
							$( window ).tcScrollTo( _clone );
						}
					}

					$.tmEPOAdmin.init_sections_check();

					$.tmEPOAdmin.make_resizables( _clone );

					return _clone;
				}
			}

			return false;
		},

		builder_add_element_onClick: function( e ) {
			var $this = $( this );
			var $_html = $.tmEPOAdmin.builder_floatbox_template_import( {
				id: 'temp_for_floatbox_insert',
				html: '<div class="tm-inner">' + TMEPOGLOBALADMINJS.element_data + '</div>',
				title: TMEPOGLOBALADMINJS.i18n_add_element
			} );
			var popup = $.tcFloatBox( {
				closefadeouttime: 0,
				animateOut: '',
				fps: 1,
				ismodal: false,
				refresh: 'fixed',
				width: '70%',
				height: '70%',
				top: '15%',
				left: '15%',
				classname: 'flasho tm_wrapper tc-builder-add-element',
				data: $_html
			} );

			if ( e ) {
				e.preventDefault();
			}

			$( '.tc-builder-add-element .tc-element-button' ).on( 'click.cpf', function( ev ) {
				var new_section = $this.closest( '.builder_wrapper' );
				var el = $( this ).attr( 'data-element' );

				ev.preventDefault();

				if ( $this.is( '.tc-prepend' ) ) {
					$.tmEPOAdmin.builder_clone_element( el, new_section, 'prepend' );
				} else {
					$.tmEPOAdmin.builder_clone_element( el, new_section );
				}

				$.tmEPOAdmin.logic_reindex();
				popup.destroy();
			} );
		},

		builder_add_section_and_element_onClick: function( e ) {
			var $_html = $.tmEPOAdmin.builder_floatbox_template_import( {
				id: 'temp_for_floatbox_insert',
				html: '<div class="tm-inner">' + TMEPOGLOBALADMINJS.element_data + '</div>',
				title: TMEPOGLOBALADMINJS.i18n_add_element
			} );
			var popup = $.tcFloatBox( {
				closefadeouttime: 0,
				animateOut: '',
				fps: 1,
				ismodal: false,
				refresh: 'fixed',
				width: '70%',
				height: '70%',
				top: '15%',
				left: '15%',
				classname: 'flasho tm_wrapper tc-builder-add-section-and-element',
				data: $_html
			} );

			if ( e ) {
				e.preventDefault();
			}

			$( '.tc-builder-add-section-and-element .tc-element-button' ).on( 'click.cpf', function( ev ) {
				var new_section = $.tmEPOAdmin.builder_add_section_onClick( false, true );
				var el;

				ev.preventDefault();

				if ( new_section ) {
					el = $( this ).attr( 'data-element' );
					$.tmEPOAdmin.builder_clone_element( el, new_section );
					$.tmEPOAdmin.logic_reindex();
					popup.destroy();
				}
			} );
		},

		tm_variations_check: function() {
			var variation_element = $( '.builder_layout .element-variations' );
			var data;
			var foundIndex;

			if ( ! variation_element.length ) {
				return;
			}

			if ( $.tmEPOAdmin.tm_variations_check_for_changes === 1 && TMEPOADMINJS ) {
				$( '#tm_extra_product_options' ).block( {
					message: null
				} );
				data = {
					action: 'woocommerce_tm_variations_check',
					post_id: TMEPOADMINJS.post_id,
					security: TMEPOADMINJS.check_attributes_nonce
				};
				$.post(
					TMEPOADMINJS.ajax_url,
					data,
					function( response ) {
						TCBUILDER[ 0 ].variations_html = response.html;
						$( '.tma-variations-wrap .tm-all-attributes' ).html( response.html );
						if ( response.jsobject ) {
							TCBUILDER[ 0 ].jsobject = response.jsobject;
							TCBUILDER[ 0 ].fields[ 0 ].find( function( y, index ) {
								if ( y.id === 'multiple' ) {
									foundIndex = index;
								}
								return y.id === 'multiple';
							} );
							if ( foundIndex !== undefined ) {
								TCBUILDER[ 0 ].fields[ 0 ][ foundIndex ] = response.jsobject;
							} else {
								TCBUILDER[ 0 ].fields[ 0 ].push( response.jsobject );
							}
						}
						$( '#tm_extra_product_options' ).unblock();
						$( '#tm_extra_product_options' ).trigger( 'woocommerce_tm_variations_check_loaded' );
						$.tmEPOAdmin.tm_variations_check_for_changes = 0;
						$.tmEPOAdmin.builder_reorder_multiple();
					},
					'json'
				);
			}
		},

		// Variation delete button
		builder_variation_delete_onClick: function() {
			$.tmEPOAdmin.var_is( 'tm-style-variation-forced', true );
			$( '.builder_add_section' ).addClass( 'inline' );
			$( '.builder_add_variation' ).addClass( 'inline' ).removeClass( 'tm-hidden' );
			if ( $( '.tma-variations-wrap' ).length > 1 ) {
				$( '.tma-variations-wrap' ).not( ':first' ).each( function() {
					var $this = $( this );
					var sectionIndex;
					var builder_wrapper;

					builder_wrapper = $this.closest( '.builder_wrapper' );
					sectionIndex = builder_wrapper.index();
					builder_wrapper.remove();
					TCBUILDER.splice( sectionIndex, 1 );
					$.tmEPOAdmin.builder_reorder_multiple();

					$.tmEPOAdmin.section_logic_init();
					$.tmEPOAdmin.init_sections_check();
				} );
			}
			$( '.tma-variations-wrap' ).addClass( 'tm-hidden' );
			$.tmEPOAdmin.setFieldValue( $.tmEPOAdmin.variationSectionIndex, $.tmEPOAdmin.variationFieldIndex, 'variations_disabled', '1' );
			$.tmEPOAdmin.init_sections_check();
		},

		// Variation button
		builder_add_variation_onClick: function( e ) {
			var _clone;
			var _clone2;

			if ( e ) {
				e.preventDefault();
			}

			if ( $.tmEPOAdmin.var_is( 'tm-style-variation-added' ) === true ) {
				if ( $.tmEPOAdmin.var_is( 'tm-style-variation-forced' ) === true ) {
					$.tmEPOAdmin.var_is( 'tm-style-variation-forced', false );
					$( '.builder_add_variation' ).addClass( 'tm-hidden' );
					$( '.builder_add_section' ).removeClass( 'inline' );
					$( '.tma-variations-wrap' ).removeClass( 'tm-hidden' );
					$.tmEPOAdmin.setFieldValue( $.tmEPOAdmin.variationSectionIndex, $.tmEPOAdmin.variationFieldIndex, 'variations_disabled', '' );
					$.tmEPOAdmin.init_sections_check();
				}
				return;
			}
			_clone = $.tmEPOAdmin.builder_add_section_onClick( false, false, true );
			if ( _clone ) {
				_clone.find( '.tm-add-element-action,.tmicon.clone,.tmicon.size,.tmicon.move,.tmicon.plus,.tmicon.minus' ).remove();
				_clone.addClass( 'tma-nomove tma-variations-wrap' );
				$.tmEPOAdmin.var_is( 'tm-style-variation-added', true );

				_clone2 = $.tmEPOAdmin.builder_clone_element( 'variations', $( '.builder_layout' ).find( '.builder_wrapper' ).first() );
				$.tmEPOAdmin.setVariationSection();
				if ( $.tmEPOAdmin.var_is( 'tm-style-variation-forced' ) === true ) {
					$( '.builder_add_section' ).addClass( 'inline' );
					$( '.builder_add_variation' ).addClass( 'inline' ).removeClass( 'tm-hidden' );
					$( '.tma-variations-wrap' ).addClass( 'tm-hidden' );
					$.tmEPOAdmin.setFieldValue( $.tmEPOAdmin.variationSectionIndex, $.tmEPOAdmin.variationFieldIndex, 'variations_disabled', '1' );
				} else {
					$( '.builder_add_variation' ).addClass( 'tm-hidden' );
					$( '.builder_add_section' ).removeClass( 'inline' );
					$( '.tma-variations-wrap' ).removeClass( 'tm-hidden' );
					$.tmEPOAdmin.var_is( 'tm-style-variation-forced', false );
					$.tmEPOAdmin.setFieldValue( $.tmEPOAdmin.variationSectionIndex, $.tmEPOAdmin.variationFieldIndex, 'variations_disabled', '' );
				}

				_clone2.find( '.bitem-settings .size,.bitem-settings .clone,.tm-label-move,.bitem-settings .plus,.bitem-settings .minus,.tm-label-delete' ).remove();

				_clone2.addClass( 'tma-nomove' );
				_clone2.find( '.builder-remove-for-variations' ).remove();
				_clone2.find( '.builder_hide_for_variation' ).hide();

				$.tmEPOAdmin.pre_element_logic_init_set();

				$.tmEPOAdmin.logic_reindex_force();

				$.tmEPOAdmin.tm_variations_check_for_changes = 1;
				$.tmEPOAdmin.tm_variations_check();
			}
		},

		var_is: function( v, d ) {
			if ( ! d ) {
				return $( 'body' ).data( v );
			}
			$( 'body' ).data( v, d );
		},

		var_remove: function( v ) {
			if ( v ) {
				$( 'body' ).removeData( v );
			}
		},

		tm_escape: function( val ) {
			return encodeURIComponent( val );
		},

		tm_unescape: function( val ) {
			return decodeURIComponent( val );
		},

		get_element_logic_init: function( do_section ) {
			if ( ! $.tmEPOAdmin.pre_element_logic_init_done ) {
				$.tmEPOAdmin.pre_element_logic_init( do_section );
			}
			if ( ! $.tmEPOAdmin.isinit ) {
				$.tmEPOAdmin.pre_element_logic_init_done = false;
			} else {
				$.tmEPOAdmin.pre_element_logic_init_done = true;
			}
			return $.tmEPOAdmin.pre_element_logic_init_obj;
		},

		get_element_logic_options_init: function( do_section ) {
			if ( ! $.tmEPOAdmin.pre_element_logic_init_done ) {
				$.tmEPOAdmin.pre_element_logic_init( do_section );
			}
			if ( ! $.tmEPOAdmin.isinit ) {
				$.tmEPOAdmin.pre_element_logic_init_done = false;
			} else {
				$.tmEPOAdmin.pre_element_logic_init_done = true;
			}
			return $.tmEPOAdmin.pre_element_logic_init_obj_options;
		},

		find_index: function( is_slider, field, include, exlcude ) {
			var sib = 0;
			var $lis;

			if ( is_slider ) {
				sib = field.closest( '.bitem_wrapper' ).prevAll( '.bitem_wrapper' ).find( '.bitem' ).length;
			}
			if ( include && exlcude ) {
				$lis = field.parent().find( include ).not( exlcude );
				return parseInt( sib, 10 ) + parseInt( $lis.index( field ), 10 );
			}

			return parseInt( sib, 10 ) + parseInt( field.index(), 10 );
		},

		setGlobalVariationObject: function( logic, do_section ) {
			var data;
			var c_ajaxurl;

			if ( TMEPOADMINJS ) {
				$( '#tm_extra_product_options' ).block( {
					message: null
				} );
				data = {
					action: 'woocommerce_tm_get_variations_array',
					post_id: TMEPOADMINJS.post_id,
					security: TMEPOADMINJS.check_attributes_nonce
				};
				c_ajaxurl = window.ajaxurl || TMEPOADMINJS.ajax_url;

				$.post(
					c_ajaxurl,
					data,
					function( response ) {
						if ( response ) {
							globalVariationObject = response;
							if ( logic === true ) {
								$.tmEPOAdmin.pre_element_logic_init_set( do_section );
							} else if ( logic === 'reindex' ) {
								$.tmEPOAdmin.logic_reindex_force();
							} else if ( logic === 'initialitize_on' ) {
								$.tmEPOAdmin.initialitize_on();
							} else if ( logic === 'initialitize_on_after' ) {
								$.tmEPOAdmin.initialitize_on_after();
							}
						}
					},
					'json'
				).always( function() {
					$( '#tm_extra_product_options' ).unblock();
				} );
			} else if ( logic === 'initialitize_on' ) {
				if ( ! globalVariationObject ) {
					globalVariationObject = {};
				}
				$.tmEPOAdmin.initialitize_on();
			} else if ( logic === 'initialitize_on_after' ) {
				if ( ! globalVariationObject ) {
					globalVariationObject = {};
				}
				$.tmEPOAdmin.initialitize_on_after();
			}
		},

		pre_element_logic_init: function( do_section ) {
			if ( ! globalVariationObject ) {
				$.tmEPOAdmin.setGlobalVariationObject( true, do_section );
			} else {
				$.tmEPOAdmin.pre_element_logic_init_set( do_section );
			}
		},

		getFieldValue: function( field, type, elementType ) {
			var value;

			if ( elementType && elementType !== true ) {
				type = elementType + '_' + type;
			} else if ( elementType === true ) {
				elementType = $.tmEPOAdmin.getFieldValue( field, 'element_type' );
				if ( elementType ) {
					type = elementType + '_' + type;
				}
			}

			//if (field !== undefined){
			value = field.find( function( y ) {
				return y.id === type;
			} );
			//}

			if ( value !== undefined && typeof value === 'object' ) {
				return value.default;
			}

			return undefined;
		},

		getFieldMultiple: function( field, type, elementType ) {
			var foundIndex;
			var multiple;
			var value;

			type = 'options_' + type;
			if ( elementType && elementType !== true ) {
				type = elementType + '_' + type;
			} else if ( elementType === true ) {
				elementType = $.tmEPOAdmin.getFieldValue( field, 'element_type' );
				if ( elementType ) {
					type = elementType + '_' + type;
				}
			}
			type = 'multiple_' + type;
			field.find( function( y, index ) {
				if ( y.id === 'multiple' ) {
					foundIndex = index;
				}
				return y.id === 'multiple';
			} );
			if ( foundIndex !== undefined ) {
				multiple = field[ foundIndex ].multiple;
				value = multiple
					.map( function( x ) {
						return x.find( function( y ) {
							return y.id === type;
						} );
					} )
					.filter( function( el ) {
						return el !== null && el !== undefined;
					} );
				if ( value && typeof value === 'object' ) {
					return value;
				}
			}

			return undefined;
		},

		setFieldValue: function( sectionIndex, fieldIndex, type, value, elementType ) {
			var field;
			var foundIndex;
			if ( elementType && elementType !== true ) {
				type = elementType + '_' + type;
			} else if ( elementType === true ) {
				elementType = $.tmEPOAdmin.getFieldValue( TCBUILDER[ sectionIndex ].fields[ fieldIndex ], 'element_type' );
				if ( elementType ) {
					type = elementType + '_' + type;
				}
			}
			field = TCBUILDER[ sectionIndex ].fields[ fieldIndex ];
			//if (field !== undefined){
			field.find( function( y, index ) {
				if ( y.id === type ) {
					foundIndex = index;
				}
				return y.id === type;
			} );
			//}

			if ( foundIndex !== undefined ) {
				TCBUILDER[ sectionIndex ].fields[ fieldIndex ][ foundIndex ].default = value;
			}
		},

		setFieldValueMultiple: function( sectionIndex, fieldIndex, type, value, elementType ) {
			var field;
			var foundIndex;
			var multiple;
			var multipleIndex;

			if ( type.toString.isNumeric() ) {
				multipleIndex = type;
			} else {
				type = 'options_' + type;
				if ( elementType && elementType !== true ) {
					type = elementType + '_' + type;
				} else if ( elementType === true ) {
					elementType = $.tmEPOAdmin.getFieldValue( TCBUILDER[ sectionIndex ].fields[ fieldIndex ], 'element_type' );
					if ( elementType ) {
						type = elementType + '_' + type;
					}
				}
				type = 'multiple_' + type;
			}

			field = TCBUILDER[ sectionIndex ].fields[ fieldIndex ];
			//if (field !== undefined){
			field.find( function( y, index ) {
				if ( y.id === 'multiple' ) {
					foundIndex = index;
				}
				return y.id === 'multiple';
			} );
			//}
			if ( foundIndex !== undefined ) {
				multiple = TCBUILDER[ sectionIndex ].fields[ fieldIndex ][ foundIndex ].multiple;
				if ( multipleIndex === undefined ) {
					multiple.find( function( y, index ) {
						if ( y.id === type ) {
							multipleIndex = index;
						}
						return y.id === type;
					} );
				} else {
					TCBUILDER[ sectionIndex ].fields[ fieldIndex ][ foundIndex ].multiple[ multipleIndex ].default = value;
				}
			}
		},

		setVariationSection: function() {
			TCBUILDER.find( function( y, index ) {
				var variationFieldIndex;
				var found;
				if ( y !== undefined && y.fields !== undefined ) {
					found = y.fields.find( function( x, index2 ) {
						var found2;
						found2 = x.find( function( z ) {
							return z.id === 'element_type' && z.default === 'variations';
						} );
						if ( found2 !== undefined ) {
							variationFieldIndex = index2;
						}
						return found2;
					} );
				}
				if ( found !== undefined ) {
					$.tmEPOAdmin.variationSectionIndex = index;
					$.tmEPOAdmin.variationFieldIndex = variationFieldIndex;
					$.tmEPOAdmin.variationSection = TCBUILDER[ $.tmEPOAdmin.variationSectionIndex ].fields[ $.tmEPOAdmin.variationFieldIndex ];
					TCBUILDER[ $.tmEPOAdmin.variationSectionIndex ].section.tma_variations_wrap = true;
				}
				return found !== undefined;
			} );
		},

		pre_element_logic_init_set: function( do_section ) {
			var options;
			var logicobj;
			var log_section_id;
			var section_id;
			var fields;
			var fieldsNoDisabled;
			var values;
			var field_values;
			var name;
			var field_index;
			var has_enabled;
			var is_enabled;
			var value;
			var internal_name;
			var field_type;
			var tm_title;
			var tm_title_label;
			var tm_option_titles;
			var tm_option_values;
			var _section_name;
			var elementType;
			var section;
			var field;
			var uniqid;

			if ( ! globalVariationObject ) {
				return;
			}
			$.tmEPOAdmin.pre_element_logic_init_obj = {};
			$.tmEPOAdmin.pre_element_logic_init_obj_options = {};

			options = {};
			logicobj = {};
			log_section_id = [];

			$.tmEPOAdmin.setVariationSection();

			Object.keys( TCBUILDER ).forEach( function( i ) {
				section = TCBUILDER[ i ].section;

				if ( section !== undefined ) {
					section_id = section.sections_uniqid.default;

					// Check if section id exists
					if ( log_section_id.indexOf( section_id ) !== -1 ) {
						TCBUILDER[ i ].section.sections_uniqid.default = tcCreateUniqid( '', true );
						section_id = TCBUILDER[ i ].section.sections_uniqid.default;
					}
					log_section_id.push( section_id );

					options[ section_id ] = [];
					if ( do_section ) {
						$.tmEPOAdmin.check_section_logic( i );
					}
					_section_name = section.sections_internal_name.default || 'Section';
					if ( ! section.tma_variations_wrap ) {
						options[ section_id ][ 0 ] = '<option data-type="section" data-section="' + section_id + '" value="' + section_id + '">' + _section_name + ' (' + section_id + ')</option>';
					}

					fields = TCBUILDER[ i ].fields.filter( function( x ) {
						var type = $.tmEPOAdmin.getFieldValue( x, 'element_type' );
						return type === 'variations' || ( $.inArray( type, $.tmEPOAdmin.can_take_logic ) !== -1 && $.tmEPOAdmin.getFieldValue( x, 'enabled', type ) === '1' );
					} );

					fieldsNoDisabled = TCBUILDER[ i ].fields.filter( function( x ) {
						var type = $.tmEPOAdmin.getFieldValue( x, 'element_type' );
						return $.tmEPOAdmin.getFieldValue( x, 'enabled', type ) === '1' || $.tmEPOAdmin.getFieldValue( x, 'enabled', type ) === undefined;
					} );

					values = [];
					field_values = [];

					// All the fields of current section that can be used as selector in logic
					Object.keys( fields ).forEach( function( ii ) {
						field = fields[ ii ];

						elementType = $.tmEPOAdmin.getFieldValue( field, 'element_type' );

						name = $.tmEPOAdmin.getFieldValue( field, 'header_title', elementType );

						uniqid = $.tmEPOAdmin.getFieldValue( field, 'uniqid', elementType );

						field_index = fieldsNoDisabled.findIndex( function( x ) {
							var type = $.tmEPOAdmin.getFieldValue( x, 'element_type' );
							return uniqid === $.tmEPOAdmin.getFieldValue( x, 'uniqid', type ) && ( type === 'variations' || ( $.inArray( type, $.tmEPOAdmin.can_take_logic ) !== -1 && $.tmEPOAdmin.getFieldValue( x, 'enabled', true ) === '1' ) );
						} );

						has_enabled = $.tmEPOAdmin.getFieldValue( field, 'enabled', elementType );
						is_enabled = $.tmEPOAdmin.getFieldValue( field, 'enabled', elementType ) === '1';

						if ( has_enabled && ! is_enabled ) {
							return true;
						}

						if ( name !== undefined ) {
							value = name;
							if ( value === undefined || value.length === 0 ) {
								value = '';
							}
							internal_name = $.tmEPOAdmin.getFieldValue( field, 'internal_name', elementType );
							if ( internal_name !== value ) {
								value = value + ' (' + internal_name + ')';
							}

							field_type = elementType === 'variations'
								? 'variation'
								: $.inArray( elementType, [ 'radiobuttons', 'checkboxes', 'product', 'selectbox' ] ) !== -1
									? 'multiple'
									: 'text';

							options[ section_id ][ field_index + 1 ] = '<option data-type="' + field_type + '" data-section="' + section_id + '" value="' + field_index + '">' + value + '</option>';

							if ( elementType === 'variations' ) {
								field_values = [];
								$( globalVariationObject.variations ).each( function( index, variation ) {
									tm_title = [];
									$( variation.attributes ).each( function( iii, sel ) {
										var arr = $.map( sel, function( el ) {
											return el;
										} );

										$( arr ).each( function( iiii, sel2 ) {
											tm_title.push( sel2 );
										} );
									} );
									tm_title = tm_title.join( ' - ' );
									tm_title_label = tm_title;
									try {
										tm_title_label = $.tmEPOAdmin.tm_unescape( tm_title );
									} catch ( err ) {
										tm_title_label = tm_title;
									}
									field_values.push( '<option value="' + $.tmEPOAdmin.tm_escape( variation.variation_id ) + '">' + tm_title_label + '</option>' );
								} );

								values[ field_index ] = '<select data-element="' + field_index + '" data-section="' + section_id + '" class="cpf-logic-value">' + field_values.join( '' ) + '</select>';
							} else if ( $.inArray( elementType, [ 'radiobuttons', 'checkboxes', 'selectbox' ] ) !== -1 ) {
								tm_option_titles = $.tmEPOAdmin.getFieldMultiple( field, 'title', elementType );
								tm_option_values = $.tmEPOAdmin.getFieldMultiple( field, 'value', elementType );
								field_values = [];

								Object.keys( tm_option_titles ).forEach( function( index ) {
									field_values.push( '<option value="' + $.tmEPOAdmin.tm_escape( tm_option_values[ index ].default ) + '">' + tm_option_titles[ index ].default + '</option>' );
								} );

								values[ field_index ] = '<select data-element="' + field_index + '" data-section="' + section_id + '" class="cpf-logic-value">' + field_values.join( '' ) + '</select>';
							} else {
								values[ field_index ] = '<input data-element="' + field_index + '" data-section="' + section_id + '" class="cpf-logic-value" type="text" value="">';
							}
						}
					} );

					logicobj[ section_id ] = {
						values: values
					};
				}
			} );
			$.tmEPOAdmin.pre_element_logic_init_obj = logicobj;
			$.tmEPOAdmin.pre_element_logic_init_obj_options = options;
		},

		element_logic_init: function( element, section, append ) {
			var field_index;
			var fieldsNoDisabled;
			var options = [];
			var section_id;
			var logicobj;
			var options_pre;
			var elementIndex;
			var uniqid;

			if ( element instanceof $ ) {
				section = element.closest( '.builder_wrapper' ).index();
				element = $.tmEPOAdmin.find_index( TCBUILDER[ section ].section.is_slider, element );
			}

			section = $.epoAPI.math.toFloat( section );
			elementIndex = $.epoAPI.math.toFloat( element );

			uniqid = $.tmEPOAdmin.getFieldValue( TCBUILDER[ section ].fields[ elementIndex ], 'uniqid', true );

			fieldsNoDisabled = TCBUILDER[ section ].fields.filter( function( x ) {
				var type = $.tmEPOAdmin.getFieldValue( x, 'element_type' );
				return $.tmEPOAdmin.getFieldValue( x, 'enabled', type ) === '1' || $.tmEPOAdmin.getFieldValue( x, 'enabled', type ) === undefined;
			} );

			field_index = fieldsNoDisabled.findIndex( function( x ) {
				var type = $.tmEPOAdmin.getFieldValue( x, 'element_type' );
				return uniqid === $.tmEPOAdmin.getFieldValue( x, 'uniqid', type ) && $.inArray( type, $.tmEPOAdmin.can_take_logic ) !== -1 && $.tmEPOAdmin.getFieldValue( x, 'enabled', true ) === '1';
			} );

			$.tmEPOAdmin.check_section_logic();
			$.tmEPOAdmin.check_element_logic( elementIndex, section );

			logicobj = $.extend( true, {}, $.tmEPOAdmin.get_element_logic_init() );
			options_pre = $.extend( true, {}, $.tmEPOAdmin.get_element_logic_options_init() );

			section_id = TCBUILDER[ section ].section.sections_uniqid.default;

			if ( section_id && logicobj[ section_id ] && logicobj[ section_id ].values[ field_index ] ) {
				delete logicobj[ section_id ].values[ field_index ];
				delete options_pre[ section_id ][ field_index + 1 ];
				delete options_pre[ section_id ][ 0 ];
			} else if ( section_id && options_pre[ section_id ] && options_pre[ section_id ][ 0 ] ) {
				delete options_pre[ section_id ][ 0 ];
			}
			$.each( options_pre, function( i, c ) {
				if ( c ) {
					$.each( c, function( ii, d ) {
						if ( d ) {
							options.push( d );
						}
					} );
				}
			} );
			if ( ! $.tmEPOAdmin.element_logic_object.init ) {
				$.tmEPOAdmin.element_logic_object.init = true;
			}
			$.tmEPOAdmin.element_logic_object = $.extend( $.tmEPOAdmin.element_logic_object, logicobj );

			if ( append ) {
				$.tmEPOAdmin.logic_append( append, options, 'element', section, elementIndex );
			}
		},

		section_logic_init: function( section, append ) {
			var options = [];
			var logicobj = {};
			var options_pre;
			var section_id;
			var sections;

			if ( section instanceof $ ) {
				section = parseInt( section.index(), 10 );
			} else if ( section !== undefined ) {
				section = $.epoAPI.math.toFloat( section );
			}

			if ( section === undefined ) {
				sections = TCBUILDER;
			} else {
				sections = [];
				sections[ section ] = $.extend( true, {}, TCBUILDER[ section ] );
			}

			$.tmEPOAdmin.check_section_logic( section );

			Object.keys( sections ).forEach( function( i ) {
				if ( TCBUILDER[ i ].section !== undefined ) {
					logicobj = $.extend( true, {}, $.tmEPOAdmin.get_element_logic_init( true ) );
					options_pre = $.extend( true, {}, $.tmEPOAdmin.get_element_logic_options_init( true ) );
					section_id = TCBUILDER[ i ].section.sections_uniqid.default;
					if ( section_id && logicobj[ section_id ] ) {
						delete logicobj[ section_id ];
						delete options_pre[ section_id ];
					}
					$.each( options_pre, function( ix, c ) {
						if ( c ) {
							$.each( c, function( ii, d ) {
								if ( d ) {
									options.push( d );
								}
							} );
						}
					} );
					if ( ! $.tmEPOAdmin.section_logic_object.init ) {
						$.tmEPOAdmin.section_logic_object.init = true;
					}

					$.tmEPOAdmin.section_logic_object = $.extend( $.tmEPOAdmin.section_logic_object, logicobj );
					if ( append ) {
						$.tmEPOAdmin.logic_append( append, options, 'section', i );
					}
				}
			} );
		},

		logic_check_section_rules: function( rules ) {
			var copy;
			var _logic;

			if ( typeof rules !== 'object' || rules === null ) {
				rules = {};
			}
			if ( rules.toggle === undefined ) {
				rules.toggle = 'show';
			}
			if ( rules.what === undefined ) {
				rules.what = 'any';
			}
			if ( rules.rules === undefined ) {
				rules.rules = [];
			}
			copy = deepCopyArray( rules );
			_logic = $.tmEPOAdmin.section_logic_object;

			$.each( rules.rules, function( i, _rule ) {
				if ( ! ( ( _logic[ _rule.section ] !== undefined && _logic[ _rule.section ].values[ _rule.element ] !== undefined ) || _rule.section === _rule.element ) ) {
					delete copy.rules[ i ];
				}
			} );
			copy.rules = tcArrayValues( copy.rules );

			return copy;
		},

		logic_check_element_rules: function( rules, sectionIndex, fieldIndex ) {
			var copy;
			var _logic;
			var bitem;

			if ( typeof rules !== 'object' || ! rules ) {
				rules = {};
			}
			if ( rules.toggle === undefined ) {
				rules.toggle = 'show';
			}
			if ( rules.what === undefined ) {
				rules.what = 'any';
			}
			if ( rules.rules === undefined ) {
				rules.rules = [];
			}
			copy = deepCopyArray( rules );
			_logic = $.tmEPOAdmin.element_logic_object;
			$.each( rules.rules, function( i, _rule ) {
				if ( ! ( ( _logic[ _rule.section ] !== undefined && _logic[ _rule.section ].values[ _rule.element ] !== undefined ) || _rule.section === _rule.element ) ) {
					if ( sectionIndex !== undefined && fieldIndex !== undefined ) {
						bitem = $( '.builder_layout' ).find( '.builder_wrapper' ).eq( sectionIndex );

						bitem = bitem.find( '.bitem' ).eq( fieldIndex );

						bitem.addClass( 'tm-wrong-rule' );
					}

					delete copy.rules[ i ];
				}
			} );
			copy.rules = tcArrayValues( copy.rules );

			return copy;
		},

		logic_append: function( el, options, type, sectionIndex, fieldIndex ) {
			var logic;
			var rawrules;
			var rulesobj;
			var h;
			var rule;
			var tm_logic_element;
			var operators;
			var ruleshtml;
			var current_rule;
			var set_select;
			var rules;
			var elementDoesItHaveLogic;

			el = $( el );
			logic = $( el ).find( '.tm-logic-wrapper' );

			if ( ! options || options.length === 0 ) {
				logic.html( '<div class="errortitle"><p>' + TMEPOGLOBALADMINJS.i18n_cannot_apply_rules + '</p></div>' );
				return false;
			}

			try {
				if ( type === 'element' ) {
					elementDoesItHaveLogic = $.tmEPOAdmin.getFieldValue( TCBUILDER[ sectionIndex ].fields[ fieldIndex ], 'logic', true ) || '';
				} else {
					elementDoesItHaveLogic = TCBUILDER[ sectionIndex ].section.sections_logic.default || '';
				}
				if ( elementDoesItHaveLogic === '1' ) {
					if ( type === 'element' ) {
						rawrules = $.tmEPOAdmin.getFieldValue( TCBUILDER[ sectionIndex ].fields[ fieldIndex ], 'clogic', true ) || 'null';
					} else {
						rawrules = TCBUILDER[ sectionIndex ].section.sections_clogic.default || 'null';
					}
				} else {
					rawrules = 'null';
				}
				rulesobj = $.epoAPI.util.parseJSON( rawrules );
				if ( type === 'element' ) {
					rules = $.tmEPOAdmin.logic_check_element_rules( rulesobj );
				} else {
					rules = $.tmEPOAdmin.logic_check_section_rules( rulesobj );
				}
				if ( type === 'element' ) {
					$.tmEPOAdmin.setFieldValue( sectionIndex, fieldIndex, 'clogic', JSON.stringify( rules ), true );
				} else {
					TCBUILDER[ sectionIndex ].section.sections_clogic.default = JSON.stringify( rules );
				}
			} catch ( err ) {
				rules = false;
			}
			logic.empty();
			h = '';
			h =
				'<div class="tc-row tm-logic-rule">' +
				'<div class="tc-cell tc-col-4 tm-logic-element">' +
				'</div>' +
				'<div class="tc-cell tc-col-2 tm-logic-operator">' +
				'</div>' +
				'<div class="tc-cell tc-col-4 tm-logic-value">' +
				'</div>' +
				'<div class="tc-cell tc-col-2 tm-logic-func">' +
				'<button type="button" class="tmicon tcfa tcfa-plus add cpf-add-rule"></button>' +
				' <button type="button" class="tmicon tcfa tcfa-times delete cpf-delete-rule"></button>' +
				'</div>' +
				'</div>';
			rule = $( h );
			tm_logic_element = $( '<select class="cpf-logic-element">' + options.join( '' ) + '</select>' );
			operators = '';

			Object.keys( $.tmEPOAdmin.logic_operators ).forEach( function( i ) {
				operators = operators + '<option value="' + i + '">' + $.tmEPOAdmin.logic_operators[ i ] + '</option>';
			} );

			operators = $( '<select class="cpf-logic-operator">' + operators + '</select>' );

			rule.find( '.tm-logic-element' ).append( tm_logic_element );
			rule.find( '.tm-logic-operator' ).append( operators );

			if ( ! rules || rules.rules === undefined || ! rules.rules.length ) {
				rule.appendTo( logic );
				rule.find( '.cpf-logic-element' ).trigger( 'change.cpf', [ type === 'element' ] );
				rule.find( '.cpf-logic-operator' ).trigger( 'change.cpf', [ type === 'element' ] );
			} else {
				ruleshtml = $( '<div class="temp">' );
				$.each( rules.rules, function( i, _rule ) {
					if ( _rule && typeof _rule === 'object' ) {
						current_rule = rule.clone();
						set_select = current_rule.find( '.cpf-logic-element' ).find( 'option[data-section="' + _rule.section + '"][value="' + _rule.element + '"]' );

						if ( $( set_select ).length ) {
							$( set_select )[ 0 ].selected = true;
						}
						$.tmEPOAdmin.cpf_logic_element_onchange( current_rule.find( '.cpf-logic-element' ), type === 'element' );

						current_rule.find( '.cpf-logic-operator' ).val( _rule.operator );
						$.tmEPOAdmin.cpf_logic_operator_onchange( current_rule.find( '.cpf-logic-operator' ), type === 'element' );

						if ( current_rule.find( '.cpf-logic-value' ).is( 'select' ) ) {
							current_rule.find( '.cpf-logic-value' ).val( $.tmEPOAdmin.tm_escape( $.tmEPOAdmin.tm_unescape( _rule.value ) ) );
						} else {
							current_rule.find( '.cpf-logic-value' ).val( $.tmEPOAdmin.tm_unescape( _rule.value ) );
						}
						ruleshtml.append( current_rule );
					}
				} );
				ruleshtml = ruleshtml.children();
				logic.append( ruleshtml );
			}
		},

		logic_get_JSON: function( s ) {
			var rules = $( s ).find( '.builder-logic-div' );
			var this_section_id = s.find( '.tm-builder-sections-uniqid' ).val();
			var section_logic = {};
			var _toggle = rules.find( '.epo-rule-toggle' ).val();
			var _what = rules.find( '.epo-rule-what' ).val();
			var $cpf_logic_element;
			var cpf_logic_section;
			var cpf_logic_element;
			var cpf_logic_operator;
			var cpf_logic_value;

			section_logic.section = this_section_id;
			section_logic.toggle = _toggle;
			section_logic.what = _what;
			section_logic.rules = [];

			rules
				.find( '.tm-logic-wrapper' )
				.children( '.tm-logic-rule' )
				.each( function( i, el ) {
					el = $( el );
					$cpf_logic_element = el.find( '.cpf-logic-element' );
					cpf_logic_section = $cpf_logic_element.children( 'option:selected' ).attr( 'data-section' );
					cpf_logic_element = $cpf_logic_element.val();
					cpf_logic_operator = el.find( '.cpf-logic-operator' ).val();
					cpf_logic_value = el.find( '.cpf-logic-value' ).val();

					if ( ! el.find( '.cpf-logic-value' ).is( 'select' ) ) {
						cpf_logic_value = $.tmEPOAdmin.tm_escape( cpf_logic_value );
					}

					section_logic.rules.push( {
						section: cpf_logic_section,
						element: cpf_logic_element,
						operator: cpf_logic_operator,
						value: cpf_logic_value
					} );
				} );

			return JSON.stringify( section_logic );
		},

		element_logic_get_JSON: function( s ) {
			var rules = $( s ).find( '.builder-logic-div' );
			var this_element_id = s.find( '.tm-builder-element-uniqid' ).val();
			var element_logic = {};
			var _toggle = rules.find( '.epo-rule-toggle' ).val();
			var _what = rules.find( '.epo-rule-what' ).val();
			var $cpf_logic_element;
			var cpf_logic_section;
			var cpf_logic_element;
			var cpf_logic_operator;
			var cpf_logic_value;

			element_logic.element = this_element_id;
			element_logic.toggle = _toggle;
			element_logic.what = _what;
			element_logic.rules = [];

			rules
				.find( '.tm-logic-wrapper' )
				.children( '.tm-logic-rule' )
				.each( function( i, el ) {
					el = $( el );
					$cpf_logic_element = el.find( '.cpf-logic-element' );
					cpf_logic_section = $cpf_logic_element.children( 'option:selected' ).attr( 'data-section' );
					cpf_logic_element = $cpf_logic_element.val();
					cpf_logic_operator = el.find( '.cpf-logic-operator' ).val();
					cpf_logic_value = el.find( '.cpf-logic-value' ).val();

					element_logic.rules.push( {
						section: cpf_logic_section,
						element: cpf_logic_element,
						operator: cpf_logic_operator,
						value: cpf_logic_value
					} );
				} );

			return JSON.stringify( element_logic );
		},

		cpf_add_rule: function( e ) {
			var _last = $( this ).closest( '.tm-logic-rule' );
			var _clone = _last.tcClone( true );

			e.preventDefault();
			if ( _clone ) {
				_last.after( _clone );
			}
		},

		cpf_delete_rule: function( e ) {
			var element = $( this );
			var _wrapper = element.closest( '.tm-logic-wrapper' );

			e.preventDefault();
			element.trigger( 'hideTtooltip' );

			if ( _wrapper.children().length > 1 ) {
				element
					.closest( '.tm-logic-rule' )
					.css( {
						margin: '0 auto'
					} )
					.animate(
						{
							opacity: 0,
							height: 0,
							width: 0
						},
						300,
						function() {
							$( this ).remove();
						}
					);
			}
		},

		section_logic_reindex: function() {
			var l = $.tmEPOAdmin.builder_items_sortable_obj;

			Object.keys( TCBUILDER ).forEach( function( sectionIndex ) {
				var section_eq = sectionIndex;
				var copy_rules = [];
				var section_rules = TCBUILDER[ sectionIndex ].section.sections_clogic.default || 'null';
				var copy;

				if ( ! TCBUILDER[ sectionIndex ].section.sections_logic.default ) {
					return true; // skip
				}

				section_rules = $.epoAPI.util.parseJSON( section_rules );

				if ( ! ( section_rules && section_rules.rules !== undefined && section_rules.rules.length > 0 ) ) {
					return true; // skip
				}

				// Element is dragged on this section
				if ( l.end.section_eq === section_eq ) {
					// Getting here means that an element from another section
					// is being dragged on this section
					$.each( section_rules.rules, function( i, rule ) {
						copy = deepCopyArray( rule );
						if ( rule.element > l.start.element && rule.secion === l.start.section ) {
							copy.element = parseInt( copy.element, 10 ) - 1;
							copy_rules[ i ] = $.tmEPOAdmin.validate_rule( copy, sectionIndex );
						} else {
							copy_rules[ i ] = $.tmEPOAdmin.validate_rule( copy, sectionIndex );
						}
					} );
					copy_rules = tcArrayValues( copy_rules );
					if ( copy_rules.length === 0 ) {
						TCBUILDER[ sectionIndex ].section.sections_logic.default = '';
					}
					section_rules.rules = copy_rules;
					TCBUILDER[ sectionIndex ].section.sections_clogic.default = JSON.stringify( section_rules );

					// Element is not dragged on this section
				} else {
					// Getting here means that an element from another section
					// is being dragged on another section that is not the current section
					$.each( section_rules.rules, function( i, rule ) {
						copy = deepCopyArray( rule );
						if ( l.start.section !== 'check' ) {
							// Element is not changing sections
							if ( rule.section === l.start.section && rule.section === l.end.section ) {
								// Element belonging to a rule is being dragged
								if ( rule.element === l.start.element ) {
									copy.section = l.end.section;
									copy.element = l.end.element;
								} else if ( parseInt( rule.element, 10 ) > parseInt( l.start.element, 10 ) && parseInt( rule.element, 10 ) <= parseInt( l.end.element, 10 ) ) {
									// Element not belonging to a rule is being dragged
									// and breaks the rule
									copy.element = parseInt( copy.element, 10 ) - 1;
								} else if ( parseInt( rule.element, 10 ) < parseInt( l.start.element, 10 ) && parseInt( rule.element, 10 ) >= parseInt( l.end.element, 10 ) ) {
									copy.element = parseInt( copy.element, 10 ) + 1;
								}
							} else if ( rule.section === l.start.section && rule.section !== l.end.section ) { // Element is getting dragged off this section
								// Element belonging to a rule is being dragged
								if ( rule.element === l.start.element ) {
									copy.section = l.end.section;
									copy.element = l.end.element;
								} else if ( parseInt( rule.element, 10 ) > parseInt( l.start.element, 10 ) ) {
								// Element not belonging to a rule is being dragged
								// and breaks the rule
									copy.element = parseInt( copy.element, 10 ) - 1;
								}
							} else if ( rule.section !== l.start.section && rule.section === l.end.section ) { // Element is getting dragged on this section
								if ( parseInt( rule.element, 10 ) >= parseInt( l.end.element, 10 ) ) {
									copy.element = parseInt( copy.element, 10 ) + 1;
								}
							}
						}
						if ( ! ( l.end.section === 'delete' && copy.element === 'delete' ) ) {
							copy_rules[ i ] = $.tmEPOAdmin.validate_rule( copy, sectionIndex );
						}
					} );
					copy_rules = tcArrayValues( copy_rules );
					if ( copy_rules.length === 0 ) {
						TCBUILDER[ sectionIndex ].section.sections_logic.default = '';
					}
					section_rules.rules = copy_rules;
					TCBUILDER[ sectionIndex ].section.sections_clogic.default = JSON.stringify( section_rules );
				}
			} );
		},

		element_logic_reindex: function() {
			var l = $.tmEPOAdmin.builder_items_sortable_obj;
			var copy;
			var field;
			var copy_rules;
			var element_rules;
			var disabledFieldIndex;
			var elementType;

			disabledFieldIndex = l.start.disabledFieldIndex;
			if ( disabledFieldIndex ) {
				l.start.element = disabledFieldIndex.toString();
			}
			disabledFieldIndex = l.end.disabledFieldIndex;
			if ( disabledFieldIndex ) {
				l.end.element = disabledFieldIndex.toString();
			}

			Object.keys( TCBUILDER ).forEach( function( sectionIndex ) {
				Object.keys( TCBUILDER[ sectionIndex ].fields ).forEach( function( fieldIndex ) {
					field = TCBUILDER[ sectionIndex ].fields[ fieldIndex ];
					elementType = $.tmEPOAdmin.getFieldValue( field, 'element_type' );

					if ( ! $.tmEPOAdmin.getFieldValue( field, 'logic', elementType ) ) {
						return true; // skip
					}

					copy_rules = [];
					element_rules = $.tmEPOAdmin.getFieldValue( field, 'clogic', elementType );

					element_rules = $.epoAPI.util.parseJSON( element_rules );

					if ( ! ( element_rules && element_rules.rules !== undefined && element_rules.rules.length > 0 ) ) {
						return true; // skip
					}

					$.each( element_rules.rules, function( i, rule ) {
						copy = deepCopyArray( rule );
						if ( rule.element === undefined ) {
							return true;
						}
						rule.element = rule.element.toString();

						if ( l.start.section !== 'check' ) {
							// Element is not changing sections
							if ( rule.section === l.start.section && rule.section === l.end.section ) {
								// Element belonging to a rule is being dragged
								if ( rule.element === l.start.element ) {
									//copy.section=l.end.section;
									copy.element = l.end.element;
								} else if ( parseInt( rule.element, 10 ) > parseInt( l.start.element, 10 ) && parseInt( rule.element, 10 ) <= parseInt( l.end.element, 10 ) ) {
									// Element not belonging to a rule is being dragged
									// and breaks the rule
									copy.element = parseInt( copy.element, 10 ) - 1;
								} else if ( parseInt( rule.element, 10 ) < parseInt( l.start.element, 10 ) && parseInt( rule.element, 10 ) >= parseInt( l.end.element, 10 ) ) {
									copy.element = parseInt( copy.element, 10 ) + 1;
								}
							} else if ( rule.section === l.start.section && rule.section !== l.end.section ) { // Element is getting dragged off its section
								// Element belonging to a rule is being dragged
								if ( rule.element === l.start.element ) {
									copy.section = l.end.section;
									copy.element = l.end.element;
								} else if ( ! $.tmEPOAdmin.builder_items_sortable_obj.start.disabled && parseInt( rule.element, 10 ) > parseInt( l.start.element, 10 ) ) {
									// Element not belonging to a rule is being dragged
									// and breaks the rule
									copy.element = parseInt( copy.element, 10 ) - 1;
								}
							} else if ( rule.section !== l.start.section && rule.section === l.end.section ) { // Element is getting dragged on this rule's section
								if ( parseInt( rule.element, 10 ) >= parseInt( l.end.element, 10 ) ) {
									copy.element = parseInt( copy.element, 10 ) + 1;
								}
							}
						}
						if ( ! ( l.end.section === 'delete' && copy.element === 'delete' ) ) {
							copy_rules[ i ] = $.tmEPOAdmin.validate_rule( copy, sectionIndex, fieldIndex );
						}
					} );

					copy_rules = tcArrayValues( copy_rules );
					if ( copy_rules.length === 0 ) {
						$.tmEPOAdmin.setFieldValue( sectionIndex, fieldIndex, 'logic', '', elementType );
					}
					element_rules.rules = copy_rules;

					$.tmEPOAdmin.setFieldValue( sectionIndex, fieldIndex, 'clogic', JSON.stringify( element_rules ), elementType );
				} );
			} );
		},

		validate_rule: function( rule, sectionIndex, fieldIndex ) {
			var section;
			var field;
			var check;
			var tm_option_values;
			var bitem;
			var elementType;
			var fieldsNoDisabled;

			if (
				! globalVariationObject ||
				! rule ||
				typeof rule !== 'object' ||
				rule.element === undefined ||
				rule.operator === undefined ||
				rule.section === undefined ||
				( rule.value === undefined && ! ( rule.operator === 'isempty' || rule.operator === 'isnotempty' ) )
			) {
				return []; //false wrong rule
			}
			section = TCBUILDER.findIndex( function( y ) {
				return y.section.sections_uniqid.default === rule.section;
			} );

			if ( section === -1 ) {
				return []; //false
			}

			fieldsNoDisabled = TCBUILDER[ section ].fields.filter( function( x ) {
				var type = $.tmEPOAdmin.getFieldValue( x, 'element_type' );
				return $.tmEPOAdmin.getFieldValue( x, 'enabled', type ) === '1' || $.tmEPOAdmin.getFieldValue( x, 'enabled', type ) === undefined;
			} );

			field = fieldsNoDisabled[ rule.element ];

			if ( field !== undefined ) {
				elementType = $.tmEPOAdmin.getFieldValue( field, 'element_type' );
				check = false;

				if ( $.inArray( elementType, [ 'radiobuttons', 'checkboxes', 'selectbox' ] ) !== -1 ) {
					tm_option_values = $.tmEPOAdmin.getFieldMultiple( field, 'value', elementType );

					Object.keys( tm_option_values ).some( function( el, index ) {
						if ( $.tmEPOAdmin.tm_escape( tm_option_values[ index ].default ) === rule.value ) {
							check = true;
						} else if ( tm_option_values[ index ].default === rule.value ) {
							rule.value = $.tmEPOAdmin.tm_escape( rule.value );
							check = true;
						}
						return check;
					} );
				} else if ( elementType === 'variations' ) {
					if ( rule.operator === 'is' || rule.operator === 'isnot' ) {
						$( globalVariationObject.variations ).each( function( index, variation ) {
							if ( $.tmEPOAdmin.tm_escape( variation.variation_id ) === rule.value ) {
								check = true;
								return false;
							}
						} );
					} else {
						check = true;
					}
				} else {
					check = true; //other fields always true if they exist
				}
			} else {
				check = true; //this is a section
			}

			bitem = $( '.builder_layout' ).find( '.builder_wrapper' ).eq( sectionIndex );
			if ( fieldIndex !== undefined ) {
				bitem = bitem.find( '.bitem' ).eq( fieldIndex );
			}

			if ( bitem.length > 0 ) {
				if ( check || ( fieldIndex !== undefined && $.tmEPOAdmin.getFieldValue( TCBUILDER[ sectionIndex ].fields[ fieldIndex ], 'logic', elementType ) === '' ) ) {
					bitem.removeClass( 'tm-wrong-rule' );
					return rule;
				}
				bitem.addClass( 'tm-wrong-rule' );
				return []; //false
			}

			// failsafe
			return []; //false
		},

		logic_reindex: function() {
			var l = $.tmEPOAdmin.builder_items_sortable_obj;

			if ( ! ( l.start.section === l.end.section && l.start.section_eq === l.end.section_eq && l.start.element === l.end.element ) ) {
				$.tmEPOAdmin.section_logic_reindex();
				$.tmEPOAdmin.element_logic_reindex();
			}
			$.tmEPOAdmin.builder_items_sortable_obj = { start: {}, end: {} };
		},

		logic_reindex_force: function() {
			$.tmEPOAdmin.builder_items_sortable_obj.start.section = 'check';
			$.tmEPOAdmin.builder_items_sortable_obj.start.section_eq = 'check';
			$.tmEPOAdmin.builder_items_sortable_obj.start.element = 'check';
			$.tmEPOAdmin.builder_items_sortable_obj.end.section = 'check2';
			$.tmEPOAdmin.builder_items_sortable_obj.end.section_eq = 'check2';
			$.tmEPOAdmin.builder_items_sortable_obj.end.element = 'check2';

			$.tmEPOAdmin.section_logic_reindex();
			$.tmEPOAdmin.element_logic_reindex();
			$.tmEPOAdmin.builder_items_sortable_obj = { start: {}, end: {} };
		},

		// Elements sortable
		builder_items_sortable: function( obj ) {
			if ( ! $.tmEPOAdmin.is_original ) {
				return;
			}

			obj.sortable( {
				handle: '.move',
				cursor: 'move',
				items: '.bitem',
				start: function( e, ui ) {
					var builder_wrapper;
					var is_slider;
					var field_index;
					var disabledFieldIndex;
					var sectionIndex;

					ui.placeholder.height( ui.helper.outerHeight() );
					ui.placeholder.width( ui.helper.outerWidth() );
					$.tmEPOAdmin.is_element_dragged = true;
					if ( ! $( ui.item ).hasClass( 'ditem' ) ) {
						builder_wrapper = $( ui.item ).closest( '.builder_wrapper' );
						sectionIndex = builder_wrapper.index();
						is_slider = TCBUILDER[ sectionIndex ].section.is_slider;

						field_index = $.tmEPOAdmin.find_index( is_slider, $( ui.item ) );
						disabledFieldIndex = $.tmEPOAdmin.find_index( is_slider, $( ui.item ), '.bitem', '.element_is_disabled' );

						TCBUILDER[ sectionIndex ].section.sections.default = parseInt( TCBUILDER[ sectionIndex ].section.sections.default, 10 ) - 1;
						TCBUILDER[ sectionIndex ].section.sections.default = TCBUILDER[ sectionIndex ].section.sections.default.toString();
						if ( is_slider ) {
							TCBUILDER[ sectionIndex ].section.sections_slides.default = builder_wrapper
								.find( '.bitem_wrapper' )
								.map( function( i, ee ) {
									return $( ee ).children( '.bitem' ).not( '.pl2' ).length;
								} )
								.get()
								.join( ',' );
						}

						$.tmEPOAdmin.builder_items_sortable_obj.start.section = TCBUILDER[ sectionIndex ].section.sections_uniqid.default;
						$.tmEPOAdmin.builder_items_sortable_obj.start.section_eq = sectionIndex.toString();
						$.tmEPOAdmin.builder_items_sortable_obj.start.element = field_index.toString();
						$.tmEPOAdmin.builder_items_sortable_obj.start.disabledFieldIndex = disabledFieldIndex;
					} else {
						$.tmEPOAdmin.builder_items_sortable_obj.start.section = 'drag';
						$.tmEPOAdmin.builder_items_sortable_obj.start.section_eq = 'drag';
						$.tmEPOAdmin.builder_items_sortable_obj.start.element = 'drag';
					}

					$( '.builder_layout .bitem_wrapper' ).not( '.tma-variations-wrap .bitem_wrapper' ).addClass( 'highlight' );
				},
				stop: function( e, ui ) {
					var builder_wrapper;
					var is_slider;
					var field_index;
					var disabledFieldIndex;
					var sectionIndex;

					$.tmEPOAdmin.is_element_dragged = false;
					builder_wrapper = $( ui.item ).closest( '.builder_wrapper' );
					sectionIndex = builder_wrapper.index();
					is_slider = TCBUILDER[ sectionIndex ].section.is_slider;
					field_index = $.tmEPOAdmin.find_index( is_slider, $( ui.item ) );
					disabledFieldIndex = $.tmEPOAdmin.find_index( is_slider, $( ui.item ), '.bitem', '.element_is_disabled' );
					if ( ! $( ui.item ).hasClass( 'ditem' ) ) {
						$( '.builder_wrapper.tm-zindex' ).css( 'zIndex', '' ).removeClass( 'tm-zindex' );
						TCBUILDER[ sectionIndex ].section.sections.default = parseInt( TCBUILDER[ sectionIndex ].section.sections.default, 10 ) + 1;
						TCBUILDER[ sectionIndex ].section.sections.default = TCBUILDER[ sectionIndex ].section.sections.default.toString();
						if ( is_slider ) {
							TCBUILDER[ sectionIndex ].section.sections_slides.default = builder_wrapper
								.find( '.bitem_wrapper' )
								.map( function( i, ee ) {
									return $( ee ).children( '.bitem' ).not( '.pl2' ).length;
								} )
								.get()
								.join( ',' );
						}
					}
					$.tmEPOAdmin.builder_items_sortable_obj.end.section = TCBUILDER[ sectionIndex ].section.sections_uniqid.default;
					$.tmEPOAdmin.builder_items_sortable_obj.end.section_eq = sectionIndex.toString();
					$.tmEPOAdmin.builder_items_sortable_obj.end.element = field_index.toString();
					$.tmEPOAdmin.builder_items_sortable_obj.end.disabledFieldIndex = disabledFieldIndex;

					TCBUILDER[ sectionIndex ].fields.splice( field_index, 0, TCBUILDER[ $.tmEPOAdmin.builder_items_sortable_obj.start.section_eq ].fields.splice( $.tmEPOAdmin.builder_items_sortable_obj.start.element, 1 )[ 0 ] );

					$.tmEPOAdmin.builder_reorder_multiple();

					$.tmEPOAdmin.logic_reindex();
					$( '.builder_layout .bitem_wrapper' ).removeClass( 'highlight' );
				},
				tolerance: 'pointer',
				forcePlaceholderSize: true,
				placeholder: 'bitem pl2',
				cancel: '.panels_wrap,.tma-nomove',
				dropOnEmptyType: true,
				revert: 200,
				connectWith: '.builder_wrapper:not(.tma-nomove) .bitem_wrapper'
			} );
		},

		builder_delete_do: function() {
			var builder_wrapper;
			var _bitem;
			var is_slider;
			var field_index;
			var $this = $( this );
			var sectionIndex;
			var elementType;
			var is_enabled;
			var field;

			builder_wrapper = $this.closest( '.builder_wrapper' );
			sectionIndex = builder_wrapper.index();
			_bitem = $this.closest( '.bitem' );
			is_slider = TCBUILDER[ sectionIndex ].section.is_slider;
			field_index = $.tmEPOAdmin.find_index( is_slider, _bitem );
			TCBUILDER[ sectionIndex ].section.sections.default = parseInt( TCBUILDER[ sectionIndex ].section.sections.default, 10 ) - 1;
			TCBUILDER[ sectionIndex ].section.sections.default = TCBUILDER[ sectionIndex ].section.sections.default.toString();
			field = TCBUILDER[ sectionIndex ].fields[ field_index ];
			elementType = $.tmEPOAdmin.getFieldValue( field, 'element_type' );
			is_enabled = $.tmEPOAdmin.getFieldValue( field, 'enabled', elementType ) === '1';

			$.tmEPOAdmin.builder_items_sortable_obj.start.section = TCBUILDER[ sectionIndex ].section.sections_uniqid.default;
			$.tmEPOAdmin.builder_items_sortable_obj.start.section_eq = sectionIndex.toString();
			$.tmEPOAdmin.builder_items_sortable_obj.start.element = field_index.toString();

			$.tmEPOAdmin.builder_items_sortable_obj.end.section = 'delete';
			$.tmEPOAdmin.builder_items_sortable_obj.end.section_eq = 'delete';
			$.tmEPOAdmin.builder_items_sortable_obj.end.element = 'delete';

			if ( ! is_enabled ) {
				$.tmEPOAdmin.builder_items_sortable_obj.start.disabled = true;
			}
			_bitem.remove();
			TCBUILDER[ sectionIndex ].fields.splice( field_index, 1 );
			$.tmEPOAdmin.logic_reindex();

			if ( is_slider ) {
				TCBUILDER[ sectionIndex ].section.sections_slides.default = builder_wrapper
					.find( '.bitem_wrapper' )
					.map( function( i, e ) {
						return $( e ).children( '.bitem' ).not( '.pl2' ).length;
					} )
					.get()
					.join( ',' );
			}

			$.tmEPOAdmin.builder_reorder_multiple();
		},

		// Element delete button
		builder_delete_onClick: function( e ) {
			e.preventDefault();
			$( this ).blur();
			$.tmEPOAdmin.doConfirm( TMEPOGLOBALADMINJS.i18n_builder_delete, $.tmEPOAdmin.builder_delete_do, this );
		},

		builder_fold_onClick: function() {
			var $this = $( this );
			var handle_wrap;
			var handle_wrapper;

			if ( $this.is( '.tma-handle' ) ) {
				$this = $this.find( '.fold' );
			}
			handle_wrap = $this.closest( '.tma-handle-wrap' );
			handle_wrapper = handle_wrap.find( '.tma-handle-wrapper' ).first();
			if ( ! $this.data( 'folded' ) && $this.data( 'folded' ) !== undefined ) {
				$this.data( 'folded', true );
				$this.removeClass( 'tcfa-caret-down' ).addClass( 'tcfa-caret-up' );
				handle_wrapper.addClass( 'tm-hidden' );
			} else {
				$this.data( 'folded', false );
				$this.removeClass( 'tcfa-caret-up' ).addClass( 'tcfa-caret-down' );
				handle_wrapper.removeClass( 'tm-hidden' );
			}
		},

		builder_section_fold_onClick: function() {
			var $this = $( this );
			var builder_wrapper = $this.closest( '.builder_wrapper' );

			if ( ! $this.data( 'folded' ) ) {
				$this.data( 'folded', true );
				builder_wrapper.addClass( 'tm-hide-bitems' ); //hide
				$this.removeClass( 'tcfa-caret-down' ).addClass( 'tcfa-caret-up' );
			} else {
				$this.data( 'folded', false );
				builder_wrapper.removeClass( 'tm-hide-bitems' ); //show
				$this.removeClass( 'tcfa-caret-up' ).addClass( 'tcfa-caret-down' );
			}

			$this.closest( '.builder_wrapper' ).find( '.float.builder_drag_elements' ).remove();
		},

		builder_section_delete_do: function() {
			var $this = $( this );
			var sectionIndex;
			var builder_wrapper;

			builder_wrapper = $this.closest( '.builder_wrapper' );
			sectionIndex = builder_wrapper.index();
			builder_wrapper.remove();
			TCBUILDER.splice( sectionIndex, 1 );
			$.tmEPOAdmin.builder_reorder_multiple();

			$.tmEPOAdmin.section_logic_init();
			$.tmEPOAdmin.init_sections_check();
		},

		// Section delete button
		builder_section_delete_onClick: function( e ) {
			e.preventDefault();
			$( this ).blur();
			$.tmEPOAdmin.doConfirm( TMEPOGLOBALADMINJS.i18n_builder_delete, $.tmEPOAdmin.builder_section_delete_do, this );
		},

		// Element plus button
		builder_plus_onClick: function() {
			var bitem = $( this ).parentsUntil( '.bitem' ).parent().first();
			var currentSize = bitem
				.attr( 'class' )
				.split( ' ' )
				.filter( function( x ) {
					return x.indexOf( 'w' ) === 0;
				} )[ 0 ];
			var keys = Object.keys( $.tmEPOAdmin.builder_size );
			var currentIndex = keys.findIndex( function( x ) {
				return x === currentSize;
			} );
			var newSize = keys[ currentIndex + 1 ];
			var newText = $.tmEPOAdmin.builder_size[ newSize ];
			var sectionIndex;
			var fieldIndex;

			if ( newSize !== undefined ) {
				bitem.removeClass( String( currentSize ) );
				bitem.addClass( String( newSize ) ).css( 'width', '' );
				bitem.find( '.size' ).text( newText );
				sectionIndex = bitem.closest( '.builder_wrapper' ).index();
				fieldIndex = $.tmEPOAdmin.find_index( TCBUILDER[ sectionIndex ].section.is_slider, bitem );
				$.tmEPOAdmin.setFieldValue( sectionIndex, fieldIndex, 'div_size', newSize );
			}
		},

		// Element minus button
		builder_minus_onClick: function() {
			var bitem = $( this ).parentsUntil( '.bitem' ).parent().first();
			var currentSize = bitem
				.attr( 'class' )
				.split( ' ' )
				.filter( function( x ) {
					return x.indexOf( 'w' ) === 0;
				} )[ 0 ];
			var keys = Object.keys( $.tmEPOAdmin.builder_size );
			var currentIndex = keys.findIndex( function( x ) {
				return x === currentSize;
			} );
			var newSize = keys[ currentIndex - 1 ];
			var newText = $.tmEPOAdmin.builder_size[ newSize ];
			var sectionIndex;
			var fieldIndex;

			if ( newSize !== undefined ) {
				bitem.removeClass( String( currentSize ) );
				bitem.addClass( String( newSize ) ).css( 'width', '' );
				bitem.find( '.size' ).text( newText );
				sectionIndex = bitem.closest( '.builder_wrapper' ).index();
				fieldIndex = $.tmEPOAdmin.find_index( TCBUILDER[ sectionIndex ].section.is_slider, bitem );
				$.tmEPOAdmin.setFieldValue( sectionIndex, fieldIndex, 'div_size', newSize );
			}
		},

		// Section plus button
		builder_section_plus_onClick: function() {
			var section = $( this ).closest( '.builder_wrapper' );
			var currentSize = section
				.attr( 'class' )
				.split( ' ' )
				.filter( function( x ) {
					return x.indexOf( 'w' ) === 0;
				} )[ 0 ];
			var keys = Object.keys( $.tmEPOAdmin.builder_size );
			var currentIndex = keys.findIndex( function( x ) {
				return x === currentSize;
			} );
			var newSize = keys[ currentIndex + 1 ];
			var newText = $.tmEPOAdmin.builder_size[ newSize ];
			var sectionIndex;

			if ( newSize !== undefined ) {
				section.removeClass( String( currentSize ) );
				section.addClass( String( newSize ) ).css( 'width', '' );
				section.find( '.section-settings .size' ).text( newText );
				sectionIndex = section.index();
				TCBUILDER[ sectionIndex ].section.sections_size.default = newSize;
				TCBUILDER[ sectionIndex ].size = newText;
				$.tmEPOAdmin.make_resizables( section.find( '.bitem' ) );
			}
		},

		// Section minus button
		builder_section_minus_onClick: function() {
			var section = $( this ).closest( '.builder_wrapper' );
			var currentSize = section
				.attr( 'class' )
				.split( ' ' )
				.filter( function( x ) {
					return x.indexOf( 'w' ) === 0;
				} )[ 0 ];
			var keys = Object.keys( $.tmEPOAdmin.builder_size );
			var currentIndex = keys.findIndex( function( x ) {
				return x === currentSize;
			} );
			var newSize = keys[ currentIndex - 1 ];
			var newText = $.tmEPOAdmin.builder_size[ newSize ];
			var sectionIndex;

			if ( newSize !== undefined ) {
				section.removeClass( String( currentSize ) );
				section.addClass( String( newSize ) ).css( 'width', '' );
				section.find( '.section-settings .size' ).text( newText );
				sectionIndex = section.index();
				TCBUILDER[ sectionIndex ].section.sections_size.default = newSize;
				TCBUILDER[ sectionIndex ].size = newText;
				$.tmEPOAdmin.make_resizables( section.find( '.bitem' ) );
			}
		},

		// Section edit button
		builder_section_item_onClick: function() {
			var _current_logic;
			var $_html;
			var clicked;
			var builder_wrapper;
			var sectionIndex;
			var _template = $.epoAPI.template.html( templateEngine.tc_builder_section, {} );
			var _clone;
			var content;

			builder_wrapper = $( this ).closest( '.builder_wrapper' );
			sectionIndex = builder_wrapper.index();
			_clone = $( _template );
			_clone.addClass( TCBUILDER[ sectionIndex ].section.sections_size.default );
			_clone.addClass( 'appear' );

			if ( TCBUILDER[ sectionIndex ].section.tma_variations_wrap === true ) {
				_clone.find( '.builder-remove-for-variations' ).remove();
				_clone.find( '.builder_hide_for_variation' ).hide();
				_clone.find( '.tma-tab-extra, .tma-tab-logic,.tma-tab-css,.tma-tab-woocommerce' ).hide();
			}
			content = _clone.find( '.section_elements' );

			$.tmEPOAdmin.copyContent( content, sectionIndex );

			$.tmEPOAdmin.check_section_logic( sectionIndex );
			_current_logic = $.tmEPOAdmin.section_logic_object;

			$_html = $.tmEPOAdmin.builder_floatbox_template( {
				id: 'temp_for_floatbox_insert',
				html: '',
				title: TMEPOGLOBALADMINJS.i18n_edit_settings,
				uniqidtext: TMEPOGLOBALADMINJS.i18n_section_uniqid + ':',
				uniqid: TCBUILDER[ sectionIndex ].section.sections_uniqid.default
			} );

			$.tcFloatBox( {
				closefadeouttime: 0,
				animateOut: '',
				fps: 1,
				ismodal: true,
				refresh: 'fixed',
				width: '80%',
				height: '80%',
				classname: 'flasho tm_wrapper' + ( builder_wrapper.is( '.tma-variations-wrap' ) ? ' tma-variations-section' : '' ),
				data: $_html,
				cancelEvent: function( inst ) {
					if ( clicked ) {
						return;
					}
					clicked = true;
					$.tmEPOAdmin.section_logic_object = _current_logic;
					$.tmEPOAdmin.removeTinyMCE( '.flasho.tm_wrapper' );

					inst.destroy();
					$( 'body' ).removeClass( 'floatbox-open' );
				},
				cancelClass: '.floatbox-cancel',
				updateEvent: function( inst ) {
					if ( clicked ) {
						return;
					}
					clicked = true;
					$.tmEPOAdmin.removeTinyMCE( '.flasho.tm_wrapper' );

					$.tmEPOAdmin.changeBuilder( content, sectionIndex );

					TCBUILDER[ sectionIndex ].section.sections_clogic.default = $.tmEPOAdmin.logic_get_JSON( content );

					$.tmEPOAdmin.sections_type_onChange( sectionIndex );

					$.tmEPOAdmin.logic_reindex_force();
					inst.destroy();
					$( 'body' ).removeClass( 'floatbox-open' );
				},
				updateClass: '.floatbox-update'
			} );
			clicked = false;
			$( 'body' ).addClass( 'floatbox-open' );

			content.appendTo( '#temp_for_floatbox_insert' ).removeClass( 'closed' );
			$.tmEPOAdmin.section_logic_init( sectionIndex, content );

			setTimeout( function() {
				var rules;
				rules = content.find( '.tm-builder-clogic' ).val() || 'null';
				rules = $.epoAPI.util.parseJSON( rules );
				rules = $.tmEPOAdmin.logic_check_element_rules( rules );
				content.find( '.epo-rule-toggle' ).val( rules.toggle );
				content.find( '.epo-rule-what' ).val( rules.what );
			}, 1 );

			$.tmEPOAdmin.set_fields_logic( content );

			content.find( '.activate-sections-logic' ).trigger( 'change' );

			content.find( '.tm-tabs' ).tmtabs( {
				dataopenattribute: 'data-tab'
			} );
			content.tmcheckboxes();

			$.tmEPOAdmin.builder_clone_after_events( content );

			$.tcToolTip( $( '#temp_for_floatbox_insert' ).find( '.tm-tooltip' ) );
			$.tmEPOAdmin.addTinyMCE( '.flasho.tm_wrapper' );
		},

		changeBuilder: function( content, sectionIndex, fieldIndex ) {
			var fieldObject = $.tmEPOAdmin.getFieldObject( content );
			var type;
			var basic;
			var sectionField;

			if ( fieldIndex !== undefined ) {
				type = $.tmEPOAdmin.getFieldValue( TCBUILDER[ sectionIndex ].fields[ fieldIndex ], 'element_type' );
				basic = TCBUILDER[ sectionIndex ].fields[ fieldIndex ].filter( function( x ) {
					return x.id === 'element_type' || x.id === 'div_size' || x.id === type + '_internal_name';
				} );
				TCBUILDER[ sectionIndex ].fields[ fieldIndex ] = [].concat.apply( basic, fieldObject );
			} else {
				basic = [ TCBUILDER[ sectionIndex ].section.sections_internal_name ];

				fieldObject = [].concat.apply( basic, fieldObject );

				sectionField = {};
				Object.keys( fieldObject ).forEach( function( i ) {
					sectionField[ fieldObject[ i ].id ] = fieldObject[ i ];
				} );

				sectionField.is_slider = TCBUILDER[ sectionIndex ].section.is_slider;
				sectionField.rules_toggle = TCBUILDER[ sectionIndex ].section.rules_toggle;
				sectionField.rules_what = TCBUILDER[ sectionIndex ].section.rules_what;

				TCBUILDER[ sectionIndex ].section = sectionField;
			}
		},

		copyContent: function( content, sectionIndex, fieldIndex ) {
			var builder;
			var panels_wrap = content.find( '.panels_wrap' );
			var options_wrap;
			var element;
			var temp;

			if ( fieldIndex !== undefined ) {
				builder = TCBUILDER[ sectionIndex ].fields[ fieldIndex ];
			} else {
				builder = TCBUILDER[ sectionIndex ].section;
			}

			Object.keys( builder ).forEach( function( i ) {
				var name;
				var value;

				if ( typeof builder[ i ] !== 'object' ) {
					return;
				}

				if ( builder[ i ].id === 'multiple' ) {
					Object.keys( builder[ i ].multiple ).forEach( function( ii ) {
						options_wrap = content.find( '.options_wrap' );
						if ( options_wrap.eq( ii ).length === 0 ) {
							options_wrap
								.eq( 0 )
								.tcClone()
								.appendTo( panels_wrap )
								.find( ':input' )
								.not( 'button' )
								.val( '' )
								.removeAttr( 'checked' )
								.prop( 'checked', false )
								.find( '.tm_option_enabled' )
								.prop( 'checked', true )
								.val( '1' )
								.trigger( 'changechoice' );
						}

						Object.keys( builder[ i ].multiple[ ii ] ).forEach( function( iii ) {
							name = builder[ i ].multiple[ ii ][ iii ].tags.name;
							name = name.replace( '[]', '' );
							name = name.substr( 0, name.lastIndexOf( '[' ) );
							value = builder[ i ].multiple[ ii ][ iii ].default;
							element = content
								.find( '.options_wrap' )
								.eq( ii )
								.find( "[name^='" + name + "']" );
							element.attr( 'data-builder', JSON.stringify( builder[ i ].multiple[ ii ][ iii ] ) );
							element.attr( 'name', builder[ i ].multiple[ ii ][ iii ].tags.name ).attr( 'data-field-id', builder[ i ].multiple[ ii ][ iii ].id ).removeAttr( 'id' ).val( value ).trigger( 'change' );

							if ( builder[ i ].multiple[ ii ][ iii ].checked !== undefined ) {
								if (
									builder[ i ].multiple[ ii ][ iii ].id === 'multiple_checkboxes_options_default_value' ||
									builder[ i ].multiple[ ii ][ iii ].id === 'multiple_radiobuttons_options_default_value' ||
									builder[ i ].multiple[ ii ][ iii ].id === 'multiple_selectbox_options_default_value'
								) {
									element.val( ii );
								} else {
									element.val( builder[ i ].multiple[ ii ][ iii ].tags.value );
								}
								if ( builder[ i ].multiple[ ii ][ iii ].checked === true ) {
									element.prop( 'checked', true );
								} else {
									element.prop( 'checked', false );
								}
							}
						} );

						options_wrap = content.find( '.options_wrap' ).eq( ii );
					} );
				} else {
					name = builder[ i ].tags.name;
					value = builder[ i ].default;
					element = content.find( "[name='" + name + "']" );

					if ( builder[ i ].fill === 'product' ) {
						if ( builder[ i ].options && builder[ i ].options.push ) {
							builder[ i ].options.forEach( function( x ) {
								element.append( $( '<option selected="selected" value="' + x.value + '">' + x.text + '</option>' ) );
							} );
							element.attr( 'data-exclude', TMEPOGLOBALADMINJS.post_id + ',' + TMEPOGLOBALADMINJS.original_post_id );
						}
					}
					if ( builder[ i ].fill === 'category' ) {
						if ( value && value.push ) {
							value.forEach( function( x ) {
								element.find( 'option[value="' + x + '"]' ).attr( 'selected', 'selected' );
							} );
						}
					}

					// must be after the product and categories dropdowns
					// and the mode selector for this to work
					if ( element.is( '.product-default-value-search' ) ) {
						if ( content.find( '.product-mode:checked' ).val() === 'products' || content.find( '.product-mode:checked' ).val() === 'product' ) {
							element.removeClass( 'wc-product-search' ).addClass( 'enhanced-dropdown' );
							temp = content.find( '.product-products-selector' ).find( ':selected' ).clone().removeAttr( 'selected' ).removeAttr( 'data-select2-id' );
							element.find( 'option' ).remove();
							element.append( temp );
						} else if ( value !== '' ) {
							if ( builder[ i ].current_selected_text ) {
								temp = builder[ i ].current_selected_text;
							} else {
								temp = value;
							}
							element.attr( 'data-current-selected', value );
							element.data( 'current-selected-text', temp );
							element.append( $( '<option selected="selected" value="' + value + '">' + temp + '</option>' ) );
							element.addClass( 'wc-product-search' );
						}

						if ( value !== '' ) {
							element.attr( 'data-current-selected', value );
						}
					}

					element.attr( 'data-builder', JSON.stringify( builder[ i ] ) ).attr( 'data-field-id', builder[ i ].id );

					if ( element.is( ':radio' ) ) {
						element.toArray().forEach( function( x, index ) {
							var elementValue;
							if ( builder[ i ].options ) {
								element.eq( index ).val( builder[ i ].options[ index ].value );
								elementValue = builder[ i ].options[ index ].value;
							} else {
								elementValue = element.eq( index ).val();
							}

							if ( elementValue === value ) {
								element.eq( index ).prop( 'checked', true );
								element.eq( index ).trigger( 'change' );
							} else {
								element.eq( index ).prop( 'checked', false );
							}
						} );
					} else {
						element.val( value ).trigger( 'change' );

						if ( builder[ i ].checked !== undefined ) {
							element.val( builder[ i ].tags.value );
							if ( builder[ i ].checked === true ) {
								element.prop( 'checked', true );
							} else {
								element.prop( 'checked', false );
							}
						}
					}

					if ( builder[ i ].disabled ) {
						element.addClass( 'tm-wmpl-disabled' ).prop( 'disabled', true );
						element.closest( '.message0x0' ).addClass( 'tm-setting-row-disabled' );
					}
				}
			} );
		},

		// Element edit button
		builder_item_onClick: function() {
			var bitem = $( this ).closest( '.bitem' );
			var _current_logic;
			var original_enabled;
			var $_html;
			var clicked;
			var pager;
			var sectionIndex;
			var fieldIndex;
			var builder_wrapper;
			var is_slider;
			var elementType;
			var _template = $.epoAPI.template.html( templateEngine.tc_builder_elements, {} );
			var _clone;
			var content;
			var uniqid;

			builder_wrapper = bitem.closest( '.builder_wrapper' );
			sectionIndex = builder_wrapper.index();

			is_slider = TCBUILDER[ sectionIndex ].section.is_slider;
			fieldIndex = $.tmEPOAdmin.find_index( is_slider, bitem );
			elementType = $.tmEPOAdmin.getFieldValue( TCBUILDER[ sectionIndex ].fields[ fieldIndex ], 'element_type' );
			_clone = $( _template )
				.filter( '.bitem.element-' + elementType )
				.tcClone( true );
			original_enabled = $.tmEPOAdmin.getFieldValue( TCBUILDER[ sectionIndex ].fields[ fieldIndex ], 'enabled', elementType );
			original_enabled = elementType === 'variations' || original_enabled === '1' || original_enabled === undefined ? '1' : '0';
			if ( TCBUILDER[ sectionIndex ].section.tma_variations_wrap === true ) {
				_clone.find( 'tma-tab-extra, .tma-tab-logic,.tma-tab-css,.tma-tab-woocommerce' ).remove();
			}
			content = _clone.find( '.hstc2' ).find( '.inside:first' );

			if ( elementType === 'variations' ) {
				content.find( '.tm-all-attributes' ).html( TCBUILDER[ sectionIndex ].variations_html );
			}

			$.tmEPOAdmin.copyContent( content, sectionIndex, fieldIndex );

			$.tmEPOAdmin.check_element_logic( fieldIndex, sectionIndex );
			_current_logic = $.tmEPOAdmin.element_logic_object;

			uniqid = $.tmEPOAdmin.getFieldValue( TCBUILDER[ sectionIndex ].fields[ fieldIndex ], 'uniqid', elementType );

			$_html = $.tmEPOAdmin.builder_floatbox_template( {
				id: 'temp_for_floatbox_insert',
				html: '',
				title: TMEPOGLOBALADMINJS.i18n_edit_settings,
				uniqidtext: elementType === 'variations' || uniqid === undefined ? '' : TMEPOGLOBALADMINJS.i18n_element_uniqid + ':',
				uniqid: elementType === 'variations' || uniqid === undefined ? '' : uniqid
			} );

			$.tcFloatBox( {
				closefadeouttime: 0,
				animateOut: '',
				fps: 1,
				ismodal: true,
				refresh: 'fixed',
				width: '80%',
				height: '80%',
				classname: 'flasho tm_wrapper' + ( elementType === 'variations' ? ' tma-variations-section' : '' ),
				data: $_html,
				cancelEvent: function( inst ) {
					if ( clicked ) {
						return;
					}
					clicked = true;

					pager = content.find( '.tcpagination' );
					if ( pager.data( 'tc-pagination' ) ) {
						pager.tcPagination( 'destroy' );
					}

					$.tmEPOAdmin.element_logic_object = _current_logic;
					$.tmEPOAdmin.removeTinyMCE( '.flasho.tm_wrapper' );

					inst.destroy();
					$( 'body' ).removeClass( 'floatbox-open' );
				},
				cancelClass: '.floatbox-cancel',
				updateEvent: function( inst ) {
					var new_enabled;
					var section_id;
					var true_field_index;
					var new_field_index;
					var rules;
					var section_rules;

					if ( clicked ) {
						return;
					}
					clicked = true;
					pager = content.find( '.tcpagination' );
					if ( pager.data( 'tc-pagination' ) ) {
						pager.tcPagination( 'destroy' );
					}
					$.tmEPOAdmin.removeTinyMCE( '.flasho.tm_wrapper' );

					$.tmEPOAdmin.changeBuilder( content, sectionIndex, fieldIndex );

					$.tmEPOAdmin.setFieldValue( sectionIndex, fieldIndex, 'clogic', $.tmEPOAdmin.element_logic_get_JSON( content ), elementType );

					$.tmEPOAdmin.set_field_title( bitem, sectionIndex, fieldIndex );

					$.tmEPOAdmin.logic_reindex_force();

					new_enabled = elementType === 'variations' || content.find( '.is_enabled' ).length === 0 || content.find( '.is_enabled' ).is( ':checked' ) ? '1' : '0';

					if ( new_enabled === '0' ) {
						bitem.addClass( 'element_is_disabled' );
					} else {
						bitem.removeClass( 'element_is_disabled' );
					}

					if ( original_enabled !== new_enabled ) {
						section_id = TCBUILDER[ sectionIndex ].section.sections_uniqid.default;
						true_field_index = fieldIndex;
						new_field_index = $.tmEPOAdmin.find_index( is_slider, bitem, '.bitem', '.element_is_disabled' );

						Object.keys( TCBUILDER[ sectionIndex ].fields ).forEach( function( i ) {
							rules = $.tmEPOAdmin.getFieldValue( TCBUILDER[ sectionIndex ].fields[ i ], 'clogic', true ) || 'null';
							rules = $.epoAPI.util.parseJSON( rules );
							rules = $.tmEPOAdmin.logic_check_rules_reindex( bitem, rules, true_field_index, new_field_index, section_id, new_enabled );
							$.tmEPOAdmin.setFieldValue( sectionIndex, i, 'clogic', JSON.stringify( rules ), true );
						} );

						// needs check if element in rule is from the section above, otherwise no need to checnge the rule.
						Object.keys( TCBUILDER ).forEach( function( i ) {
							i = parseInt( i, 10 );
							if ( i !== sectionIndex ) {
								section_rules = TCBUILDER[ i ].section.sections_clogic.default || 'null';
								section_rules = $.epoAPI.util.parseJSON( section_rules );
								section_rules = $.tmEPOAdmin.logic_check_rules_reindex( $( '.builder_wrapper' ).eq( i ), section_rules, true_field_index, new_field_index, section_id, new_enabled );
								TCBUILDER[ i ].section.sections_clogic.default = JSON.stringify( section_rules );

								Object.keys( TCBUILDER[ i ].fields ).forEach( function( ii ) {
									rules = $.tmEPOAdmin.getFieldValue( TCBUILDER[ i ].fields[ ii ], 'clogic', true ) || 'null';
									rules = $.epoAPI.util.parseJSON( rules );
									rules = $.tmEPOAdmin.logic_check_rules_reindex( $( '.builder_wrapper' ).eq( i ).find( '.bitem' ).eq( ii ), rules, true_field_index, new_field_index, section_id, new_enabled );
									$.tmEPOAdmin.setFieldValue( i, ii, 'clogic', JSON.stringify( rules ), true );
								} );
							}
						} );
					}
					inst.destroy();
					$( 'body' ).removeClass( 'floatbox-open' );
				},
				updateClass: '.floatbox-update'
			} );
			clicked = false;

			$( 'body' ).addClass( 'floatbox-open' );

			content.appendTo( '#temp_for_floatbox_insert' );
			$( '#temp_for_floatbox_insert' )
				.find( '.inside' )
				.addClass( 'tm-hidden' )
				.after( $( '<div class="floatbox-loading">' + TMEPOGLOBALADMINJS.i18n_loading + '</div>' ) );
			setTimeout( function() {
				var rules;

				$.tmEPOAdmin.element_logic_init( fieldIndex, sectionIndex, content );
				content.find( '.activate-element-logic' ).trigger( 'change' );
				content.find( '.tm_option_enabled' ).trigger( 'changechoice' );

				rules = content.find( '.tm-builder-clogic' ).val() || 'null';
				rules = $.epoAPI.util.parseJSON( rules );
				rules = $.tmEPOAdmin.logic_check_element_rules( rules );
				content.find( '.epo-rule-toggle' ).val( rules.toggle );
				content.find( '.epo-rule-what' ).val( rules.what );

				content.find( ':radio:checked' ).not( '.panels_wrap :radio' ).trigger( 'change' );
				if ( elementType === 'variations' ) {
					content.find( '.builder-remove-for-variations' ).remove();
					content.find( '.builder_hide_for_variation' ).hide();
				}

				content.tmcheckboxes();
				$.tmEPOAdmin.builder_clone_after_events( content );

				if ( elementType === 'variations' ) {
					$.tmEPOAdmin.variations_display_as();
				} else {
					$.tmEPOAdmin.tm_upload();
				}

				$.tmEPOAdmin.set_fields_logic( content );

				$.tcToolTip( $( '#temp_for_floatbox_insert' ).find( '.tm-tooltip' ) );
				$.tmEPOAdmin.tm_weekdays( $( '#temp_for_floatbox_insert' ) );
				$.tmEPOAdmin.tm_months( $( '#temp_for_floatbox_insert' ) );
				$.tmEPOAdmin.tm_url();
				$.tmEPOAdmin.tm_pricetype_selector();
				$.tmEPOAdmin.tm_option_price_type();
				$.tmEPOAdmin.addTinyMCE( '.flasho.tm_wrapper' );
				$.tmEPOAdmin.paginattion_init();
				$( '#temp_for_floatbox_insert' ).find( '.inside' ).removeClass( 'tm-hidden' );
				content.find( '.tm-tabs' ).tmtabs( {
					dataopenattribute: 'data-tab'
				} );
				$( '#temp_for_floatbox_insert' ).find( '.floatbox-loading' ).remove();
			}, 1 );
		},

		set_fields_logic: function( content ) {
			content
				.find( '.message0x0[data-required]' )
				.toArray()
				.forEach( function( div ) {
					var required;
					var thisCheck = true;

					div = $( div );
					required = div.data( 'required' );

					Object.keys( required ).forEach( function( selector ) {
						var operator = required[ selector ].operator;
						var value = required[ selector ].value;

						var func = function() {
							var $this = $( selector );
							var val = '';
							var check = true;

							if ( ! content.find( selector ).length ) {
								return check;
							}

							if ( $this.is( ':checkbox' ) ) {
								if ( $this.is( ':checked' ) ) {
									val = $this.val();
								}
							} else if ( $this.is( ':radio' ) ) {
								val = content.find( selector ).filter( ':checked' ).val();
							} else {
								val = $this.val();
							}

							if ( typeof value === 'object' ) {
								check = $.inArray( val, value ) !== -1;
							} else {
								check = val === value;
							}

							check = operator === 'is' ? check : ! check;

							if ( check ) {
								div.removeClass( 'tm-hidden' );
							} else {
								div.addClass( 'tm-hidden' );
							}

							return check;
						};

						content.on( 'change.required changerequired', selector, func );

						thisCheck = thisCheck && func();

						if ( ! thisCheck ) {
							div.addClass( 'tm-hidden' );
						}
					} );
				} );
		},

		logic_check_rules_reindex: function( el, rules, true_field_index, new_field_index, section_id, new_enabled ) {
			var copy;

			if ( typeof rules !== 'object' || rules === null ) {
				rules = {};
			}
			if ( rules.toggle === undefined ) {
				rules.toggle = 'show';
			}
			if ( rules.what === undefined ) {
				rules.what = 'any';
			}
			if ( rules.rules === undefined ) {
				rules.rules = [];
			}

			copy = deepCopyArray( rules );

			true_field_index = parseInt( true_field_index, 10 );
			new_field_index = parseInt( new_field_index, 10 );
			new_enabled = parseInt( new_enabled, 10 );

			$.each( rules.rules, function( i, _rule ) {
				var section = _rule.section;
				var element = _rule.element;

				if ( section === section_id && element.toString().isNumeric() ) {
					element = parseInt( element, 10 );
					if ( true_field_index === element ) {
						if ( new_enabled === 0 ) {
							delete copy.rules[ i ];
							el.addClass( 'tm-wrong-rule' );
						} else if ( new_enabled === 1 ) {
							element += 1;
							copy.rules[ i ].element = element.toString();
						}
					} else if ( true_field_index < element ) {
						if ( new_enabled === 0 ) {
							element -= 1;
							copy.rules[ i ].element = element.toString();
						} else if ( new_enabled === 1 ) {
							element += 1;
							copy.rules[ i ].element = element.toString();
						}
					}
				}
			} );

			copy.rules = tcArrayValues( copy.rules );

			return copy;
		},

		getFieldObject: function( _clone ) {
			var fieldObject = [];
			var name;
			var arr;
			var multipleObject;
			var multipleObjectWrap;
			var panelsWrap;
			var tempObject;
			var builder;

			_clone
				.find( ':input' )
				.not( 'button' )
				.not( '.panels_wrap :input' )
				.toArray()
				.forEach( function( el ) {
					el = $( el );
					if ( el.is( ':radio' ) && ! el.is( ':checked' ) ) {
						return;
					}
					name = el.attr( 'name' );

					if ( name !== undefined && name.indexOf( 'tm_meta[' ) === 0 ) {
						arr = name.split( /[[\]]{1,2}/ );
						arr.pop();
						arr = arr
							.map( function( item ) {
								return item === '' ? null : item;
							} )
							.filter( function( v ) {
								if ( v !== null && v !== undefined ) {
									return v;
								}
								return null;
							} )
							.splice( 2, 1 )[ 0 ];

						tempObject = {
							id: arr,
							default: el.val(),
							type: el.prop( 'nodeName' ).toLowerCase(),
							tags: {
								name: name
							}
						};

						if ( el.is( ':checkbox' ) || el.is( ':radio' ) ) {
							tempObject.checked = el.is( ':checked' );
							tempObject.tags.value = el.attr( 'value' );
							if ( tempObject.checked ) {
								tempObject.default = el.attr( 'value' );
							} else {
								tempObject.default = '';
							}
						}

						if ( el.attr( 'data-builder' ) ) {
							builder = $.epoAPI.util.parseJSON( el.attr( 'data-builder' ) );
							if ( builder.fill === 'product' || builder.fill === 'category' || el.is( '.product-products-selector' ) || el.is( '.product-categories-selector' ) ) {
								tempObject.options = el
									.children( 'option:selected' )
									.toArray()
									.map( function( option ) {
										return { text: $( option ).text(), value: $( option ).val() };
									} );
							}
							if ( el.is( '.product-categories-selector' ) ) {
								tempObject.fill = 'category';
							} else if ( el.is( '.product-products-selector' ) ) {
								tempObject.fill = 'product';
							}
							if ( builder ) {
								tempObject = Object.assign( builder, tempObject );
							}
						}

						if ( el.attr( 'data-current-selected' ) ) {
							tempObject.current_selected = el.attr( 'data-current-selected' );
							tempObject.current_selected_text = el.data( 'current-selected-text' );
						}

						fieldObject.push( tempObject );
					}
				} );

			panelsWrap = _clone.find( '.panels_wrap :input' ).not( 'button' );
			if ( panelsWrap.length > 0 ) {
				multipleObject = [];
				_clone.find( '.panels_wrap .options_wrap' ).each( function( i, wrap ) {
					multipleObjectWrap = [];
					$( wrap )
						.find( ':input' )
						.not( 'button' )
						.each( function( ii, el ) {
							el = $( el );
							name = el.attr( 'name' );

							if ( name !== undefined && name.indexOf( 'tm_meta[' ) === 0 ) {
								arr = name.split( /[[\]]{1,2}/ );
								arr.pop();
								arr = arr
									.map( function( item ) {
										return item === '' ? null : item;
									} )
									.filter( function( v ) {
										if ( v !== null && v !== undefined ) {
											return v;
										}
										return null;
									} )
									.splice( 2, 1 )[ 0 ];

								tempObject = {
									id: arr,
									default: el.val(),
									type: el.prop( 'nodeName' ).toLowerCase(),
									tags: {
										name: name
									}
								};

								if ( el.is( ':checkbox' ) || el.is( ':radio' ) ) {
									tempObject.checked = el.is( ':checked' );
									tempObject.tags.value = el.attr( 'value' );
									if ( tempObject.checked ) {
										tempObject.default = el.attr( 'value' );
									} else {
										tempObject.default = '';
									}
								}

								if ( el.attr( 'data-builder' ) ) {
									builder = $.epoAPI.util.parseJSON( el.attr( 'data-builder' ) );
									if ( builder ) {
										tempObject = Object.assign( builder, tempObject );
									}
								}

								multipleObjectWrap.push( tempObject );
							}
						} );
					multipleObject.push( multipleObjectWrap );
				} );

				fieldObject.push( {
					id: 'multiple',
					multiple: multipleObject
				} );
			}

			return fieldObject;
		},

		// Add Element to sortable via Add button
		builder_clone_element: function( element, wrapper_selector, append_or_prepend ) {
			var _template = $.epoAPI.template.html( templateEngine.tc_builder_elements, {} );
			var _clone;
			var is_slider;
			var field_index;
			var sectionIndex;
			var fieldObject;

			if ( ! _template ) {
				return;
			}
			if ( ! append_or_prepend ) {
				append_or_prepend = 'append';
			}
			wrapper_selector = $( wrapper_selector );
			_clone = $( _template )
				.filter( '.bitem.element-' + element )
				.tcClone( true );

			if ( _clone ) {
				_clone.find( '.tm-builder-element-uniqid' ).val( tcCreateUniqid( '', true ) );
				if ( $( '.builder_wrapper' ).length <= 0 ) {
					$.tmEPOAdmin.builder_add_section_onClick();
					wrapper_selector = $( '.builder_wrapper' ).first();
				}

				sectionIndex = wrapper_selector.index();

				if ( wrapper_selector.find( '.bitem_wrapper' ).find( '.ditem' ).length > 0 ) {
					wrapper_selector.find( '.bitem_wrapper' ).find( '.ditem' ).replaceWith( _clone );
				} else {
					if ( append_or_prepend === 'append' ) {
						wrapper_selector.find( '.bitem_wrapper' ).not( '.tm-hide' ).append( _clone );
					}
					if ( append_or_prepend === 'prepend' ) {
						wrapper_selector.find( '.bitem_wrapper' ).not( '.tm-hide' ).prepend( _clone );
					}
				}

				TCBUILDER[ sectionIndex ].section.sections.default = parseInt( TCBUILDER[ sectionIndex ].section.sections.default, 10 ) + 1;
				TCBUILDER[ sectionIndex ].section.sections.default = TCBUILDER[ sectionIndex ].section.sections.default.toString();

				is_slider = TCBUILDER[ sectionIndex ].section.is_slider;
				if ( is_slider ) {
					TCBUILDER[ sectionIndex ].section.sections_slides.default = wrapper_selector
						.find( '.bitem_wrapper' )
						.map( function( i, e ) {
							return $( e ).children( '.bitem' ).not( '.pl2' ).length;
						} )
						.get()
						.join( ',' );
				}

				if ( append_or_prepend === 'prepend' || append_or_prepend === 'append' ) {
					field_index = $.tmEPOAdmin.find_index( is_slider, _clone );

					$.tmEPOAdmin.builder_items_sortable_obj.start.section = 'drag';
					$.tmEPOAdmin.builder_items_sortable_obj.start.section_eq = 'drag';
					$.tmEPOAdmin.builder_items_sortable_obj.start.element = 'drag';

					$.tmEPOAdmin.builder_items_sortable_obj.end.section = TCBUILDER[ sectionIndex ].section.sections_uniqid.default;
					$.tmEPOAdmin.builder_items_sortable_obj.end.section_eq = wrapper_selector.index().toString();
					$.tmEPOAdmin.builder_items_sortable_obj.end.element = field_index.toString();
				}

				fieldObject = $.tmEPOAdmin.getFieldObject( _clone );

				TCBUILDER[ sectionIndex ].fields.splice( field_index, 0, fieldObject );

				_clone.find( '.builder_element_wrap' ).empty();
				_clone.find( '.builder_element_type' ).remove();
				_clone.find( '.div_size' ).remove();
				_clone.find( '.tm-internal-name' ).remove();

				$.tmEPOAdmin.check_element_logic( _clone );
				$.tmEPOAdmin.builder_reorder_multiple();

				$.tmEPOAdmin.make_resizables( _clone );

				return _clone;
			}
		},

		doConfirm: function( title, func, funcThis, funcArgs ) {
			var $_html = $.epoAPI.template.html( wp.template( 'tc-floatbox' ), {
				id: 'temp_for_floatbox_insert',
				title: title,
				html: '',
				uniqid: '',
				update: TMEPOGLOBALADMINJS.i18n_yes,
				cancel: TMEPOGLOBALADMINJS.i18n_no
			} );
			var clicked = false;

			$.tcFloatBox( {
				closefadeouttime: 0,
				overlayopacity: 0.6,
				animateOut: '',
				fps: 1,
				ismodal: true,
				refresh: 'fixed',
				width: '50%',
				height: 'auto',
				classname: 'flasho tm_wrapper tc-question',
				data: $_html,
				cancelEvent: function( inst ) {
					if ( clicked ) {
						return;
					}
					clicked = true;

					inst.destroy();
				},
				cancelClass: '.floatbox-cancel',
				updateEvent: function( inst ) {
					if ( clicked ) {
						return;
					}
					clicked = true;

					func.apply( funcThis, funcArgs );

					inst.destroy();
				},
				updateClass: '.floatbox-update',
				isconfirm: true
			} );
		},

		builder_clone_do: function() {
			var _bitem;
			var _label_data;
			var _clone;
			var is_slider;
			var field_index;
			var _bitem_field_index;
			var sectionIndex;
			var builder_wrapper;

			_bitem = $( this ).closest( '.bitem' );
			_label_data = _bitem.data( 'original_title' );
			_clone = _bitem.tcClone();
			_clone.data( 'original_title', _label_data );

			if ( _clone ) {
				_bitem.after( _clone );

				builder_wrapper = _clone.closest( '.builder_wrapper' );
				sectionIndex = builder_wrapper.index();

				is_slider = TCBUILDER[ sectionIndex ].section.is_slider;
				field_index = $.tmEPOAdmin.find_index( is_slider, _clone );

				TCBUILDER[ sectionIndex ].section.sections.default = parseInt( TCBUILDER[ sectionIndex ].section.sections.default, 10 ) + 1;
				TCBUILDER[ sectionIndex ].section.sections.default = TCBUILDER[ sectionIndex ].section.sections.default.toString();

				if ( is_slider ) {
					TCBUILDER[ sectionIndex ].section.sections_slides.default = builder_wrapper
						.find( '.bitem_wrapper' )
						.map( function( i, e ) {
							return $( e ).children( '.bitem' ).not( '.pl2' ).length;
						} )
						.get()
						.join( ',' );
				}

				_bitem_field_index = $.tmEPOAdmin.find_index( _bitem, _bitem );
				TCBUILDER[ sectionIndex ].fields.splice( _bitem_field_index, 0, deepCopyArray( TCBUILDER[ sectionIndex ].fields[ _bitem_field_index ] ) );
				$.tmEPOAdmin.setFieldValue( sectionIndex, field_index, 'uniqid', tcCreateUniqid( '', true ), true );
				$.tmEPOAdmin.builder_reorder_multiple();
				$.tmEPOAdmin.builder_items_sortable_obj.start.section = 'clone';
				$.tmEPOAdmin.builder_items_sortable_obj.start.section_eq = 'clone';
				$.tmEPOAdmin.builder_items_sortable_obj.start.element = 'clone';
				$.tmEPOAdmin.builder_items_sortable_obj.end.section = TCBUILDER[ sectionIndex ].section.sections_uniqid.default;
				$.tmEPOAdmin.builder_items_sortable_obj.end.section_eq = sectionIndex.toString();
				$.tmEPOAdmin.builder_items_sortable_obj.end.element = field_index.toString();
				$.tmEPOAdmin.logic_reindex();

				$.tmEPOAdmin.make_resizables( _clone );
			}
		},

		// Element clone button
		builder_clone_onClick: function( e ) {
			e.preventDefault();
			$.tmEPOAdmin.doConfirm( TMEPOGLOBALADMINJS.i18n_builder_clone, $.tmEPOAdmin.builder_clone_do, this, [ $( this ) ] );
		},

		// Section clone button
		builder_section_clone_do: function() {
			var builder_wrapper;
			var _clone;
			var original_titles;
			var sectionIndex;
			var _clonesectionIndex;

			builder_wrapper = $( this ).closest( '.builder_wrapper' );
			_clone = builder_wrapper.tcClone();
			if ( _clone ) {
				sectionIndex = builder_wrapper.index();

				original_titles = [];
				builder_wrapper.find( '.bitem' ).each( function( i, el ) {
					original_titles[ i ] = $( el ).data( 'original_title' );
				} );
				builder_wrapper.after( _clone );
				_clonesectionIndex = builder_wrapper.index();
				TCBUILDER.splice( sectionIndex, 0, deepCopyArray( TCBUILDER[ sectionIndex ] ) );
				TCBUILDER[ _clonesectionIndex ].section.sections_uniqid.default = tcCreateUniqid( '', true );

				_clone.find( '.bitem' ).each( function( i, el ) {
					$( el ).data( 'original_title', original_titles[ i ] );
				} );

				if ( TCBUILDER[ _clonesectionIndex ].section.is_slider ) {
					$.tmEPOAdmin.create_slider( _clone );
				}

				$.tmEPOAdmin.builder_reorder_multiple();
				$.tmEPOAdmin.builder_items_sortable( _clone.find( '.bitem_wrapper' ) );
				$.tmEPOAdmin.check_section_logic( _clonesectionIndex );
				$.tmEPOAdmin.check_element_logic();
				$.tmEPOAdmin.section_logic_init( _clonesectionIndex );
				//_clone.addClass("appear");

				$.tmEPOAdmin.make_resizables( _clone );
				$.tmEPOAdmin.make_resizables( _clone.find( '.bitem' ) );
			}
		},

		// Section clone button
		builder_section_clone_onClick: function( e ) {
			e.preventDefault();
			$.tmEPOAdmin.doConfirm( TMEPOGLOBALADMINJS.i18n_builder_clone, $.tmEPOAdmin.builder_section_clone_do, this, [ $( this ) ] );
		},

		// Helper : Creates the html for the edit pop up
		builder_floatbox_template: function( data, template ) {
			return $.epoAPI.template.html( wp.template( template || 'tc-floatbox' ), {
				id: data.id,
				title: data.title,
				html: data.html,
				uniqidtext: data.uniqidtext || '',
				uniqid: data.uniqid,
				update: data.update || TMEPOGLOBALADMINJS.i18n_update,
				cancel: data.cancel || TMEPOGLOBALADMINJS.i18n_cancel
			} );
		},

		builder_floatbox_template_import: function( data ) {
			return $.epoAPI.template.html( wp.template( 'tc-floatbox-import' ), {
				id: data.id,
				title: data.title,
				html: data.html,
				cancel: TMEPOGLOBALADMINJS.i18n_cancel
			} );
		},

		nameChange: function( _m, i ) {
			var __m = /\[[0-9]+\]\[\]/g;
			var __m2 = /\[[0-9]+\]/g;

			if ( _m.match( __m ) !== null ) {
				_m = _m.replace( __m, '[' + i + '][]' );
			} else if ( _m.match( __m2 ) !== null ) {
				_m = _m.replace( __m2, '[' + i + ']' );
			}

			return _m;
		},

		// Helper : Renames all fields that contain multiple options
		builder_reorder_multiple: function() {
			var obj;
			var outputArray = [].concat
				.apply(
					[],
					TCBUILDER.map( function( x ) {
						return x.fields.map( function( y ) {
							return y
								.map( function( z ) {
									if ( z.id === 'element_type' ) {
										return z.default;
									}
									return null;
								} )
								.filter( function( el ) {
									return el !== null;
								} )[ 0 ];
						} );
					} )
				)
				.filter( function( value, index, self ) {
					return self.indexOf( value ) === index;
				} );

			outputArray.forEach( function( selector ) {
				var counter = 0;
				obj = TCBUILDER.map( function( x ) {
					return x.fields.map( function( y ) {
						if (
							y.find( function( z ) {
								return z.id === 'element_type' && z.default === selector;
							} )
						) {
							return y;
						}
						return null;
					} );
				} );
				obj.forEach( function( el, sectionIndex ) {
					el.forEach( function( field, fieldIndex ) {
						var multiple;
						var foundIndex;
						if ( field ) {
							multiple = field.find( function( y, index ) {
								if ( y.id === 'multiple' ) {
									foundIndex = index;
								}
								return y.id === 'multiple';
							} );
							if ( multiple !== undefined && typeof multiple === 'object' ) {
								multiple = multiple.multiple;
								multiple.forEach( function( multipleObject, multipleIndex ) {
									multipleObject.forEach( function( multipleField, multipleFieldIndex ) {
										TCBUILDER[ sectionIndex ].fields[ fieldIndex ][ foundIndex ].multiple[ multipleIndex ][ multipleFieldIndex ].tags.name = $.tmEPOAdmin.nameChange(
											TCBUILDER[ sectionIndex ].fields[ fieldIndex ][ foundIndex ].multiple[ multipleIndex ][ multipleFieldIndex ].tags.name,
											counter
										);
									} );
								} );
							}
							counter = counter + 1;
						}
					} );
				} );
			} );
		},

		getEnhancedSelectFormatString: function() {
			return {
				language: {
					errorLoading: function() {
						// Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
						return wc_enhanced_select_params.i18n_searching;
					},
					inputTooLong: function( args ) {
						var overChars = args.input.length - args.maximum;

						if ( 1 === overChars ) {
							return wc_enhanced_select_params.i18n_input_too_long_1;
						}

						return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
					},
					inputTooShort: function( args ) {
						var remainingChars = args.minimum - args.input.length;

						if ( 1 === remainingChars ) {
							return wc_enhanced_select_params.i18n_input_too_short_1;
						}

						return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
					},
					loadingMore: function() {
						return wc_enhanced_select_params.i18n_load_more;
					},
					maximumSelected: function( args ) {
						if ( args.maximum === 1 ) {
							return wc_enhanced_select_params.i18n_selection_too_long_1;
						}

						return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
					},
					noResults: function() {
						return wc_enhanced_select_params.i18n_no_matches;
					},
					searching: function() {
						return wc_enhanced_select_params.i18n_searching;
					}
				}
			};
		},

		create_products_search: function( _clone ) {
			// Ajax product search box
			_clone
				.find( ':input.wc-product-search' )
				.filter( ':not(.enhanced)' )
				.each( function() {
					var select2_args = {
						allowClear: true,
						placeholder: $( this ).data( 'placeholder' ),
						dropdownCssClass: 'tc-dropdown',
						minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
						escapeMarkup: function( m ) {
							return m;
						},
						ajax: {
							url: wc_enhanced_select_params.ajax_url,
							dataType: 'json',
							delay: 250,
							data: function( params ) {
								return {
									term: params.term,
									action: $( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
									security: wc_enhanced_select_params.search_products_nonce,
									exclude: $( this ).data( 'exclude' ),
									include: $( this ).data( 'include' ),
									limit: $( this ).data( 'limit' ),
									display_stock: $( this ).data( 'display_stock' ),
									tcmode: 'builder'
								};
							},
							processResults: function( data ) {
								var terms = [];
								if ( data ) {
									$.each( data, function( id, text ) {
										terms.push( { id: id, text: text } );
									} );
								}
								return {
									results: terms
								};
							},
							cache: true
						}
					};
					var $select;
					var $list;

					select2_args = $.extend( select2_args, $.tmEPOAdmin.getEnhancedSelectFormatString() );

					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
					// fix for being inside modal
					$( this ).on( 'select2:open', function() {
						var y = $( window ).scrollTop();
						$( window ).scrollTop( y + 1 );
						$( window ).scrollTop( y );
					} );

					if ( $( this ).data( 'sortable' ) ) {
						$select = $( this );
						$list = $( this ).next( '.select2-container' ).find( 'ul.select2-selection__rendered' );

						$list.sortable( {
							placeholder: 'ui-state-highlight select2-selection__choice',
							forcePlaceholderSize: true,
							items: 'li:not(.select2-search__field)',
							tolerance: 'pointer',
							stop: function() {
								$( $list.find( '.select2-selection__choice' ).get().reverse() ).each( function() {
									var id = $( this ).data( 'data' ).id;
									var option = $select.find( 'option[value="' + id + '"]' )[ 0 ];
									$select.prepend( option );
								} );
							}
						} );
						// Keep multiselects ordered alphabetically if they are not sortable.
					} else if ( $( this ).prop( 'multiple' ) ) {
						$( this ).on( 'change', function() {
							var $children = $( this ).children();
							$children.sort( function( a, b ) {
								var atext = a.text.toLowerCase();
								var btext = b.text.toLowerCase();

								if ( atext > btext ) {
									return 1;
								}
								if ( atext < btext ) {
									return -1;
								}
								return 0;
							} );
							$( this ).html( $children );
						} );
					}
				} );
		},

		create_categories_search: function( _clone ) {
			// Ajax category search boxes
			_clone
				.find( ':input.wc-category-search' )
				.filter( ':not(.enhanced)' )
				.each( function() {
					var select2_args = $.extend(
						{
							allowClear: true,
							placeholder: $( this ).data( 'placeholder' ),
							dropdownCssClass: 'tc-dropdown'
						},
						$.tmEPOAdmin.getEnhancedSelectFormatString()
					);

					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
					$( this ).on( 'select2:open', function() {
						var y = $( window ).scrollTop();
						$( window ).scrollTop( y + 1 );
						$( window ).scrollTop( y );
					} );
				} );
		},

		create_enhanced_dropdown: function( _clone ) {
			_clone
				.find( ':input.enhanced-dropdown' )
				.filter( ':not(.enhanced)' )
				.each( function() {
					var select2_args = {
						allowClear: true,
						placeholder: $( this ).data( 'placeholder' ),
						dropdownCssClass: 'tc-dropdown'
					};

					select2_args = $.extend( select2_args, $.tmEPOAdmin.getEnhancedSelectFormatString() );
					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
				} );
		},

		create_normal_dropdown: function( _clone ) {
			_clone
				.find( 'select' )
				.filter( ':not(.enhanced, .enhanced-dropdown, .wc-category-search, .wc-product-search)' )
				.not( '.builder-logic-div select, .panels_wrap select, .options_wrap select' )
				.each( function() {
					var select2_args = {
						allowClear: false,
						minimumResultsForSearch: -1,
						dropdownCssClass: 'tc-dropdown'
					};

					select2_args = $.extend( select2_args, $.tmEPOAdmin.getEnhancedSelectFormatString() );
					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
				} );
		},

		// Helper : Generates new events after cloning an element
		builder_clone_elements_after_events: function( _clone ) {
			_clone.find( 'input.tm-color-picker' ).spectrum( 'destroy' );
			_clone.find( '.sp-replacer' ).remove();
			_clone.find( 'input.tm-color-picker' ).spectrum( {
				showInput: true,
				showInitial: true,
				allowEmpty: true,
				showAlpha: false,
				showPalette: false,
				clickoutFiresChange: true,
				showButtons: false,
				preferredFormat: 'hex',
				theme: 'epo'
			} );
		},

		// Helper : Generates new events after cloning an element
		builder_clone_after_events: function( _clone ) {
			$.tmEPOAdmin.builder_clone_elements_after_events( _clone );
			$.tmEPOAdmin.panels_sortable( _clone.find( '.panels_wrap' ) );

			$.tmEPOAdmin.create_normal_dropdown( _clone );
			$.tmEPOAdmin.create_enhanced_dropdown( _clone );
			$.tmEPOAdmin.create_products_search( _clone );
			$.tmEPOAdmin.create_categories_search( _clone );
		},

		paginattion_init: function( start ) {
			var obj = $( '#temp_for_floatbox_insert' );
			var pager = obj.find( '.tcpagination' );
			var panels_wrap;
			var options_wrap;
			var perpage;
			var total;

			if ( pager.length === 0 ) {
				return;
			}
			panels_wrap = obj.find( '.panels_wrap' );
			options_wrap = panels_wrap.find( '.options_wrap' );
			perpage = parseInt( pager.attr( 'data-perpage' ), 10 );
			total = Math.ceil( options_wrap.length / perpage );

			if ( pager.data( 'tc-pagination' ) ) {
				pager.tcPagination( 'destroy' );
			}

			if ( start === 'last' ) {
				start = total;
			} else if ( start === 'current' ) {
				start = pager.data( 'pagination_current' ) || 1;
			} else {
				start = 1;
			}

			pager.data( 'pagination_current', start );
			pager.tcPagination( {
				totalPages: total,
				startPage: start,
				visiblePages: 10,
				onPageClick: function( event, page ) {
					$.tmEPOAdmin.paginationOnClick( pager, page, perpage, options_wrap );
				}
			} );
		},

		paginationOnClick: function( pager, page, perpage, options_wrap ) {
			page = parseInt( page, 10 );
			pager.data( 'pagination_current', page );
			options_wrap.addClass( 'tm-hidden' );

			options_wrap
				.filter( function( index ) {
					//return ( index >= perpage * ( page - 1 ) ) && ( perpage * ( page - 1 ) >= index );

					return ( ( index >= ( perpage * ( page - 1 ) ) ) && ( ( ( perpage * page ) - 1 ) >= index ) );
				} )
				.removeClass( 'tm-hidden' );
		},

		addTinyMCE: function( element ) {
			var getter_tmce = 'excerpt';
			var tmc_defaults = {
				theme: 'modern',
				menubar: false,
				wpautop: true,
				indent: false,
				toolbar1: 'bold,italic,underline,blockquote,strikethrough,bullist,numlist,alignleft,aligncenter,alignright,undo,redo,link,unlink',
				plugins: 'fullscreen,image,wordpress,wpeditimage,wplink'
			};
			var qt_defaults = {
				buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,fullscreen'
			};
			var init_settings = typeof tinyMCEPreInit === 'object' && tinyMCEPreInit.mceInit !== undefined && getter_tmce in tinyMCEPreInit.mceInit ? tinyMCEPreInit.mceInit[ getter_tmce ] : tmc_defaults;
			var qt_settings = typeof tinyMCEPreInit === 'object' && tinyMCEPreInit.qtInit !== undefined && getter_tmce in tinyMCEPreInit.mceInit ? tinyMCEPreInit.qtInit[ getter_tmce ] : qt_defaults;
			var tmc_settings;
			var id;
			var tqt_settings;
			var editor_tools_html;
			var editor_tools_class;

			if ( ! $( element ) || typeof tinyMCE === 'undefined' ) {
				return;
			}

			editor_tools_html = $( '#wp-' + getter_tmce + '-editor-tools' ).html();
			editor_tools_class = $( '#wp-' + getter_tmce + '-editor-tools' ).attr( 'class' );

			$( element )
				.find( 'textarea' )
				.not( ':disabled' )
				.not( '.tm-no-editor' )
				.each( function( i, textarea ) {
					id = $( textarea ).attr( 'id' );
					if ( ! id ) {
						$( textarea ).attr( 'id', $( textarea ).attr( 'name' ).replace( /[[\]]/g, '' ) );
						id = $( textarea ).attr( 'id' );
					}
					if ( id ) {
						tmc_settings = $.extend( {}, init_settings, {
							selector: '#' + id
						} );
						tqt_settings = $.extend( {}, qt_settings, {
							id: id
						} );
						if ( typeof tinyMCEPreInit === 'object' ) {
							tinyMCEPreInit.mceInit[ id ] = tmc_settings;
							tinyMCEPreInit.qtInit[ id ] = tqt_settings;
						}
						$( textarea )
							.addClass( 'wp-editor-area' )
							.wrap( '<div id="wp-' + id + '-wrap" class="wp-core-ui wp-editor-wrap tmce-active tm_editor_wrap"></div>' )
							.before( '<div class="' + editor_tools_class + '">' + editor_tools_html + '</div>' )
							.wrap( '<div class="wp-editor-container"></div>' );
						$( '.tm_editor_wrap' )
							.find( '.wp-switch-editor' )
							.each( function( n, s ) {
								var aid;
								var l;
								var mode;

								if ( $( s ).attr( 'id' ) ) {
									aid = $( s ).attr( 'id' );
									l = aid.length;
									mode = aid.substr( l - 4 );
									$( s ).attr( 'id', id + '-' + mode );
									$( s ).attr( 'data-wp-editor-id', id );
								}
							} );
						$( '.tm_editor_wrap' ).find( '.insert-media' ).attr( 'data-editor', id );

						tinyMCE.init( tmc_settings );
						if ( QTags && quicktags ) {
							quicktags( tqt_settings );
							QTags._buttonsInit();
						}
						$( textarea ).closest( '.tm_editor_wrap' ).find( 'a.insert-media' ).data( 'editor', id ).attr( 'data-editor', id );
					}
				} );
		},

		removeTinyMCE: function( element ) {
			var id;
			var _check = '';
			var current_textarea_value;
			var is_tinymce_active;

			if ( ! $( element ) || tinyMCE === undefined ) {
				return;
			}

			$( element )
				.find( 'textarea' )
				.not( ':disabled' )
				.not( '.tm-no-editor' )
				.each( function( i, textarea ) {
					id = $( textarea ).attr( 'id' );
					if ( id && tinyMCE && tinyMCE.editors ) {
						current_textarea_value = $( textarea ).val();
						is_tinymce_active = tinyMCE !== undefined && tinyMCE.editors[ id ] && ! tinyMCE.editors[ id ].isHidden();

						if ( tinyMCE.editors[ id ] !== undefined ) {
							_check = tinyMCE.editors[ id ].getContent();
							tinyMCE.editors[ id ].remove();
						}
						$( textarea ).closest( '.tm_editor_wrap' ).find( '.quicktags-toolbar,.wp-editor-tools' ).remove();
						$( textarea ).unwrap().unwrap();

						if ( is_tinymce_active ) {
							if ( _check === '' ) {
								$( textarea ).val( '' );
							} else {
								$( textarea ).val( _check );
							}
						} else {
							$( textarea ).val( current_textarea_value );
						}
					}
				} );
		},

		set_field_title: function( bitem, sectionIndex, fieldIndex ) {
			var title;
			var elementType;

			if ( bitem.length === 0 ) {
				return;
			}

			elementType = $.tmEPOAdmin.getFieldValue( TCBUILDER[ sectionIndex ].fields[ fieldIndex ], 'element_type' );

			title = $.tmEPOAdmin.getFieldValue( TCBUILDER[ sectionIndex ].fields[ fieldIndex ], 'header_title', elementType !== 'header' );

			if ( title === undefined || title === '' ) {
				bitem.find( '.tm-label' ).html( TMEPOGLOBALADMINJS.i18n_no_title );
			} else {
				bitem.find( '.tm-label' ).html( title );
			}
		},

		tm_upload_button_remove_onClick: function() {
			var input = $( this ).prevAll( 'input' ).first();
			var image = $( this ).nextAll( '.tm_upload_image' ).first().find( '.tm_upload_image_img' );

			$( input ).val( '' );
			$( image ).attr( 'src', '' );
		},

		variations_display_as: function( e ) {
			var $this;
			var tm_attribute;
			var tm_terms;
			var tm_changes_product_image;

			if ( e ) {
				if ( $( this ).is( '.tm-changes-product-image' ) ) {
					$this = $( this ).closest( '.tm-attribute' ).find( '.variations-display-as' );
				} else {
					$this = $( this );
				}
			} else {
				$this = $( '#temp_for_floatbox_insert .variations-display-as' );
			}

			$this.each( function( i, el ) {
				var selected_mode;

				tm_attribute = $( el ).closest( '.tm-attribute' );
				tm_terms = tm_attribute.find( '.tm-term' );
				tm_changes_product_image = tm_attribute.find( '.tm-changes-product-image' );
				selected_mode = $( el ).val();
				if ( selected_mode === 'select' ) {
					tm_attribute.find( '.tma-hide-for-select-box' ).hide().addClass( 'tc-row-hidden' );
				} else {
					tm_attribute.find( '.tma-hide-for-select-box' ).show().removeClass( 'tc-row-hidden' );
				}
				if ( selected_mode === 'image' || selected_mode === 'color' || selected_mode === 'radiostart' || selected_mode === 'radioend' ) {
					tm_attribute.find( '.tma-show-for-swatches' ).show().removeClass( 'tc-row-hidden' );
				} else {
					tm_attribute.find( '.tma-show-for-swatches' ).hide().addClass( 'tc-row-hidden' );
				}
				tm_terms.each( function( i2, term ) {
					$( term ).hide().find( '.tma-term-color,.tma-term-image,.tma-term-custom-image' ).hide();
					switch ( selected_mode ) {
						case 'select':
							if ( tm_changes_product_image.val() === 'images' ) {
								tm_changes_product_image.val( '' );
							}
							tm_changes_product_image.children( "option[value='images']" ).attr( 'disabled', 'disabled' ).hide();
							if ( tm_changes_product_image.val() === 'custom' ) {
								$( term ).show().find( '.tma-term-custom-image' ).show();
							}

							break;
						case 'radio':
							if ( tm_changes_product_image.val() === 'images' ) {
								tm_changes_product_image.val( '' );
							}
							tm_changes_product_image.children( "option[value='images']" ).attr( 'disabled', 'disabled' ).hide();
							if ( tm_changes_product_image.val() === 'custom' ) {
								$( term ).show().find( '.tma-term-custom-image' ).show();
							}
							break;
						case 'image':
						case 'radiostart':
						case 'radioend':
							tm_changes_product_image.children( "option[value='images']" ).removeAttr( 'disabled' ).show();
							$( term ).show().find( '.tma-term-image' ).show();
							if ( tm_changes_product_image.val() === 'custom' ) {
								$( term ).show().find( '.tma-term-custom-image' ).show();
							}
							break;
						case 'color':
							if ( tm_changes_product_image.val() === 'images' ) {
								tm_changes_product_image.val( '' );
							}
							tm_changes_product_image.children( "option[value='images']" ).attr( 'disabled', 'disabled' ).hide();
							if ( tm_changes_product_image.val() === 'custom' ) {
								$( term ).show().find( '.tma-term-custom-image' ).show();
							}
							$( term ).show().find( '.tma-term-color' ).show();
							break;
					}
				} );
			} );

			$this = $( '#temp_for_floatbox_insert' );

			$this
				.find( '.tm_option_image' )
				.not( '.tm_option_imagep' )
				.each( function( i ) {
					$this.find( '.tm_upload_imagep' ).eq( i ).find( '.tm_upload_image_img' ).attr( 'src', $( this ).val() );
				} );
			$this.find( '.tm_option_imagep' ).each( function( i ) {
				$this.find( '.tm_upload_image' ).not( '.tm_upload_imagep' ).eq( i ).find( '.tm_upload_image_img' ).attr( 'src', $( this ).val() );
			} );
		},

		tm_upload: function() {
			var $this = $( '#temp_for_floatbox_insert' );
			var $use_images_all = $this.find( '.use_images' ).not( '.tm-changes-product-image' );
			var $use_color_all = $this.find( '.use_colors' );
			var $use_div_images = $this.find( '.tm-use-images' );
			var $use_div_colors = $this.find( '.tm-use-colors' );
			var $use_imagesp_all = $this.find( '.tm-changes-product-image' );
			var $swatchmode = $this.find( '.swatchmode' );
			var $use_lightbox = $( '#temp_for_floatbox_insert .tm-show-when-use-images' );
			var $show_when_use_color = $( '#temp_for_floatbox_insert .tm-show-when-use-color' );
			var tm_upload = $this.find( '.builder_element_wrap' ).find( '.tm_upload_button' ).not( '.tm_upload_buttonp,.tm_upload_buttonl' );
			var tm_upload_image = $this.find( '.builder_element_wrap' ).find( '.tm_upload_image' ).not( '.tm_upload_imagep,.tm_upload_imagel' );
			var tm_uploadp = $this.find( '.builder_element_wrap' ).find( '.tm_upload_buttonp' );
			var tm_upload_imagep = $this.find( '.builder_element_wrap' ).find( '.tm_upload_imagep' );
			var tm_uploadl = $this.find( '.builder_element_wrap' ).find( '.tm_upload_buttonl' );
			var tm_upload_imagel = $this.find( '.builder_element_wrap' ).find( '.tm_upload_imagel' );
			var tm_cell_images = $this.find( '.builder_element_wrap' ).find( '.tm_cell_images' );
			var tm_color_picker = $this.find( '.builder_element_wrap .panels_wrap' ).find( '.sp-replacer' );
			var tm_option_image;
			var tm_option_imagep;
			var tm_upload_imagep_img;

			tm_upload.hide();
			tm_upload_image.hide();
			tm_uploadp.hide();
			tm_upload_imagep.hide();
			tm_uploadl.hide();
			tm_upload_imagel.hide();
			tm_cell_images.hide();
			tm_color_picker.hide();

			if ( $use_images_all.val() !== '' && $use_color_all.val() !== '' ) {
				$use_div_colors.show();
				$use_div_images.show();
			}
			if ( $use_images_all.val() === '' && $use_color_all.val() === '' ) {
				$use_div_colors.show();
				$use_div_images.show();
			}
			if ( $use_images_all.val() !== '' && $use_color_all.val() === '' ) {
				$use_div_colors.hide();
				$use_div_images.show();
			}
			if ( $use_images_all.val() === '' && $use_color_all.val() !== '' ) {
				$use_div_colors.show();
				$use_div_images.hide();
			}

			if ( $use_imagesp_all.val() === 'images' && $use_images_all.val() === '' ) {
				tm_option_image = $this.find( '.tm_option_image' ).not( '.tm_option_imagep' );
				tm_option_imagep = $this.find( '.tm_option_imagep' );
				tm_upload_imagep_img = $this.find( '.tm_upload_imagep .tm_upload_image_img' );
				$use_imagesp_all.val( 'custom' );
				tm_option_image.each( function( i ) {
					tm_option_imagep.eq( i ).val( $( this ).val() );
					tm_upload_imagep_img.attr( 'src', $( this ).val() );
				} );
			}

			if ( ! $use_images_all.length || $use_images_all.val() !== 'images' ) {
				if ( $use_imagesp_all.val() === 'images' ) {
					$use_imagesp_all.val( '' );
				}
				$use_imagesp_all.find( "option[value='images']" ).attr( 'disabled', 'disabled' ).hide();
			} else {
				$use_imagesp_all.find( "option[value='images']" ).removeAttr( 'disabled' ).show();
			}
			setTimeout( function() {
				$use_imagesp_all.selectWoo( 'destroy' ).removeClass( 'enhanced' );
				$.tmEPOAdmin.create_normal_dropdown( $use_imagesp_all.parent() );
				$use_imagesp_all.trigger( 'change.select2' );
			}, 100 );
			if ( $use_images_all.val() === 'images' || $use_images_all.val() === 'start' || $use_images_all.val() === 'end' || ( $use_images_all.val() === 'images' && $use_imagesp_all.val() === 'images' ) ) {
				tm_upload.show();
				tm_upload_image.show();
				tm_cell_images.show();
			}
			if ( $use_imagesp_all.val() === 'custom' ) {
				tm_uploadp.show();
				tm_upload_imagep.show();
				tm_cell_images.show();
			}
			if ( $use_images_all.val() !== '' ) {
				$use_lightbox.show();
				if ( $( '#temp_for_floatbox_insert .tm-use-lightbox' ).val() === 'lightbox' ) {
					tm_uploadl.show();
					tm_upload_imagel.show();
				}
			} else {
				$use_lightbox.hide();
			}
			if ( $use_color_all.val() === 'color' ) {
				$show_when_use_color.show();
				if ( $swatchmode.val() === 'swatch_img' || $swatchmode.val() === 'swatch_img_lbl' || $swatchmode.val() === 'swatch_img_desc' || $swatchmode.val() === 'swatch_img_lbl_desc' ) {
					$swatchmode.val( '' );
				}
				$swatchmode.find( "option[value='swatch_img']" ).attr( 'disabled', 'disabled' ).hide();
				$swatchmode.find( "option[value='swatch_img_lbl']" ).attr( 'disabled', 'disabled' ).hide();
				$swatchmode.find( "option[value='swatch_img_desc']" ).attr( 'disabled', 'disabled' ).hide();
				$swatchmode.find( "option[value='swatch_img_lbl_desc']" ).attr( 'disabled', 'disabled' ).hide();
			} else {
				$swatchmode.find( "option[value='swatch_img']" ).removeAttr( 'disabled' ).show();
				$swatchmode.find( "option[value='swatch_img_lbl']" ).removeAttr( 'disabled' ).show();
				$swatchmode.find( "option[value='swatch_img_desc']" ).removeAttr( 'disabled' ).show();
				$swatchmode.find( "option[value='swatch_img_lbl_desc']" ).removeAttr( 'disabled' ).show();

				if ( $use_images_all.val() !== 'images' ) {
					$show_when_use_color.hide();
				}
			}

			if ( $use_color_all.val() === 'color' || $use_color_all.val() === 'start' || $use_color_all.val() === 'end' ) {
				tm_cell_images.show();
				tm_color_picker.show();
			}

			$this
				.find( '.tm_option_image' )
				.not( '.tm_option_imagec, .tm_option_imagep, .tm_option_imagel' )
				.each( function( i ) {
					$this.find( '.tm_upload_image' ).not( '.tm_upload_imagec, .tm_upload_imagep, .tm_upload_imagel' ).eq( i ).find( '.tm_upload_image_img' ).attr( 'src', $( this ).val() );
				} );

			$this.find( '.tm_option_imagec' ).each( function( i ) {
				$this.find( '.tm_upload_imagec' ).eq( i ).find( '.tm_upload_image_img' ).attr( 'src', $( this ).val() );
			} );
			$this.find( '.tm_option_imagep' ).each( function( i ) {
				$this.find( '.tm_upload_imagep' ).eq( i ).find( '.tm_upload_image_img' ).attr( 'src', $( this ).val() );
			} );
			$this.find( '.tm_option_imagel' ).each( function( i ) {
				$this.find( '.tm_upload_imagel' ).eq( i ).find( '.tm_upload_image_img' ).attr( 'src', $( this ).val() );
			} );
		},

		tm_weekdays: function( e ) {
			var obj;

			if ( e ) {
				obj = $( e );
			} else {
				obj = $( 'body' );
			}

			obj.find( '.tm-weekdays' ).each( function( i, el ) {
				var val = $( el ).val();
				var values = val.split( ',' );
				var wrap = $( el ).next( '.tm-weekdays-picker-wrap' );
				var pickers = $( wrap ).find( '.tm-weekday-picker' );

				pickers.each( function( x, picker ) {
					if ( values.indexOf( $( picker ).val() ) !== -1 ) {
						$( picker ).attr( 'checked', 'checked' ).prop( 'checked', true );
						$( picker ).closest( '.tm-weekdays-picker' ).addClass( 'tm-checked' );
					} else {
						$( picker ).removeAttr( 'checked' ).prop( 'checked', false );
						$( picker ).closest( '.tm-weekdays-picker' ).removeClass( 'tm-checked' );
					}
				} );
			} );
		},

		tm_weekday_picker: function() {
			var weekdays = $( this ).closest( '.tm-weekdays-picker-wrap' ).prev( '.tm-weekdays' );
			var values = $( weekdays ).val().split( ',' );
			var c = values.indexOf( $( this ).val() );

			if ( $( this ).is( ':checked' ) ) {
				if ( c === -1 ) {
					values.push( $( this ).val() );
				}
			} else if ( c !== -1 ) {
				values.splice( c, 1 );
			}
			values = $.map( values, function( item ) {
				return item === '' ? null : item;
			} );
			$( weekdays ).val( values.join( ',' ) );
			$.tmEPOAdmin.tm_weekdays( $( weekdays ).parent() );
		},

		tm_months: function( e ) {
			var obj;

			if ( e ) {
				obj = $( e );
			} else {
				obj = $( 'body' );
			}

			obj.find( '.tm-months' ).each( function( i, el ) {
				var val = $( el ).val();
				var values = val.split( ',' );
				var wrap = $( el ).next( '.tm-months-picker-wrap' );
				var pickers = $( wrap ).find( '.tm-month-picker' );

				pickers.each( function( x, picker ) {
					if ( values.indexOf( $( picker ).val() ) !== -1 ) {
						$( picker ).attr( 'checked', 'checked' ).prop( 'checked', true );
						$( picker ).closest( '.tm-months-picker' ).addClass( 'tm-checked' );
					} else {
						$( picker ).removeAttr( 'checked' ).prop( 'checked', false );
						$( picker ).closest( '.tm-months-picker' ).removeClass( 'tm-checked' );
					}
				} );
			} );
		},

		tm_month_picker: function() {
			var months = $( this ).closest( '.tm-months-picker-wrap' ).prev( '.tm-months' );
			var values = $( months ).val().split( ',' );
			var c = values.indexOf( $( this ).val() );

			if ( $( this ).is( ':checked' ) ) {
				if ( c === -1 ) {
					values.push( $( this ).val() );
				}
			} else if ( c !== -1 ) {
				values.splice( c, 1 );
			}
			values = $.map( values, function( item ) {
				return item === '' ? null : item;
			} );
			$( months ).val( values.join( ',' ) );
			$.tmEPOAdmin.tm_months( $( months ).parent() );
		},

		tm_pricetype_selector: function( e ) {
			var obj;
			var val;

			if ( e ) {
				obj = $( this );
			} else {
				obj = $( '#temp_for_floatbox_insert .tm-pricetype-selector' );
			}
			val = obj.val();

			if ( val === 'math' ) {
				$( '.tc-element-setting-price, .tc-element-setting-sale-price' ).attr( 'autocomplete', 'off' );
			} else {
				$( '.tc-element-setting-price, .tc-element-setting-sale-price' ).removeAttr( 'autocomplete' );
			}
		},

		tm_option_price_type: function( e ) {
			var obj;
			var val;

			if ( e ) {
				obj = $( this );
			} else {
				obj = $( '#temp_for_floatbox_insert .tm_option_price_type' );
			}
			val = obj.val();

			if ( val === 'math' ) {
				$( '.tm_option_price, .tm_option_sale_price' ).attr( 'autocomplete', 'off' );
			} else {
				$( '.tm_option_price, .tm_option_sale_price' ).removeAttr( 'autocomplete' );
			}
		},

		tm_url: function( e ) {
			var $this = $( '#temp_for_floatbox_insert' );
			var $use_url = $( this );
			var use_url = $this.find( '.builder_element_wrap' ).find( '.tm_cell_url' );

			if ( ! e ) {
				$use_url = $( '#temp_for_floatbox_insert .use_url' );
			}

			if ( $use_url.val() === 'url' ) {
				use_url.show();
			} else {
				use_url.hide();
			}
		},

		upload: function( e ) {
			var _this;
			var _this_image;
			var InsertImage;
			var $tm_upload_frame;

			e.preventDefault();

			if ( wp && wp.media ) {
				_this = $( this ).prev( 'input' );
				_this_image = $( this ).nextAll( '.tm_upload_image' ).first().find( 'img' );

				if ( _this.data( 'tm_upload_frame' ) ) {
					_this.data( 'tm_upload_frame' ).open();
					return;
				}

				InsertImage = wp.media.controller.Library.extend( {
					defaults: _.defaults(
						{
							id: 'insert-image',
							title: 'Insert Image Url',
							allowLocalEdits: true,
							displaySettings: true,
							displayUserSettings: true,
							multiple: true,
							type: 'image' //audio, video, application/pdf, ... etc
						},
						wp.media.controller.Library.prototype.defaults
					)
				} );

				$tm_upload_frame = wp.media( {
					button: { text: 'Select' },
					state: 'insert-image',
					states: [ new InsertImage() ]
				} );

				$tm_upload_frame.on( 'close', function() {

				} );
				$tm_upload_frame.on( 'select', function() {
					var state = $tm_upload_frame.state( 'insert-image' );
					var selection = state.get( 'selection' );

					if ( ! selection ) {
						return;
					}

					_this_image.attr( 'src', '' );
					_this.val( '' );

					selection.each( function( attachment ) {
						var display = state.display( attachment ).toJSON();
						var obj_attachment = attachment.toJSON();
						var caption = obj_attachment.caption;
						var options;

						// If captions are disabled, clear the caption.
						if ( ! wp.media.view.settings.captions ) {
							delete obj_attachment.caption;
						}
						display = wp.media.string.props( display, obj_attachment );

						options = {
							id: obj_attachment.id,
							post_content: obj_attachment.description,
							post_excerpt: caption
						};

						if ( display.linkUrl ) {
							options.url = display.linkUrl;
						}
						if ( 'image' === obj_attachment.type ) {
							_.each(
								{
									align: 'align',
									size: 'image-size',
									alt: 'image_alt'
								},
								function( option, prop ) {
									if ( display[ prop ] ) {
										options[ option ] = display[ prop ];
									}
								}
							);
							if ( options[ 'image-size' ] && attachment.attributes.sizes[ options[ 'image-size' ] ] ) {
								options.url = attachment.attributes.sizes[ options[ 'image-size' ] ].url;
							}
						} else {
							options.post_title = display.title;
						}

						_this_image.attr( 'src', options.url );
						_this.val( options.url );
					} );
				} );

				$tm_upload_frame.on( 'open', function() {
					var selection = $tm_upload_frame.state().get( 'library' ).toJSON();
					var isinit = true;

					$.each( selection, function( i, _el ) {
						var attachment;

						if ( _el.url === _this.val() ) {
							attachment = wp.media.attachment( _el.id );
							$tm_upload_frame
								.state()
								.get( 'selection' )
								.add( attachment ? [ attachment ] : [] );
							$( '.attachment-display-settings' ).find( 'select.size' ).val( 'full' );
							isinit = false;
						} else if ( _el.sizes ) {
							$.each( _el.sizes, function( s, size ) {
								if ( size.url === _this.val() ) {
									attachment = wp.media.attachment( _el.id );
									$tm_upload_frame
										.state()
										.get( 'selection' )
										.add( attachment ? [ attachment ] : [] );
									$( '.attachment-display-settings' ).find( 'select.size' ).val( s );
									isinit = false;
								}
							} );
						}
						if ( isinit ) {
							$( '.attachment-display-settings' ).find( 'select.size' ).val( 'full' );
						}
					} );
				} );

				_this.data( 'tm_upload_frame', $tm_upload_frame );
				$tm_upload_frame.open();
			} else {
				return false;
			}
		},

		populatePriceVariables: function( obj ) {
			var ids = deepCopyArray(
				TCBUILDER.map( function( x, sectionIndex ) {
					return x.fields.map( function( y, fieldIndex ) {
						var type = y.find( function( z ) {
							return z.id === 'element_type';
						} );
						var ret;
						if ( type.default ) {
							type = type.default;
						}
						ret = y
							.filter( function( el ) {
								return el !== null;
							} )
							.find( function( z ) {
								return z.id === type + '_uniqid';
							} );
						if ( ret ) {
							ret = ret.default;
						}
						return {
							sectionIndex: sectionIndex,
							fieldIndex: fieldIndex,
							uniqid: ret,
							type: type
						};
					} );
				} )
			);
			var list;
			var exclude = $( '.tm-element-uniqid' ).attr( 'data-uniqid' );
			var value;
			var value1;
			var value2;

			// global variables
			list = $( '<h4 class="tc-var-header">' + TMEPOGLOBALADMINJS.i18n_formula_global_variables + '</h4>' );
			obj.append( list );
			list = $( '<ul class="tc-var-list"></ul>' );
			obj.append( list );
			list.append( '<li class="tc-var-field" data-value="{quantity}"><span>' + TMEPOGLOBALADMINJS.i18n_formula_quantity + '</span></li>' );
			list.append( '<li class="tc-var-field" data-value="{product_price}"><span>' + TMEPOGLOBALADMINJS.i18n_formula_product_price + '</span></li>' );

			// this element
			list = $( '<h4 class="tc-var-header">' + TMEPOGLOBALADMINJS.i18n_formula_this_element + '</h4>' );
			obj.append( list );
			list = $( '<ul class="tc-var-list"></ul>' );
			obj.append( list );
			list.append( '<li class="tc-var-field" data-value="{this.value}"><span>' + TMEPOGLOBALADMINJS.i18n_formula_this_value + '</span></li>' );
			list.append( '<li class="tc-var-field" data-value="{this.value.length}"><span>' + TMEPOGLOBALADMINJS.i18n_formula_this_value_length + '</span></li>' );
			list.append( '<li class="tc-var-field" data-value="{this.count}"><span>' + TMEPOGLOBALADMINJS.i18n_formula_this_count + '</span></li>' );
			list.append( '<li class="tc-var-field" data-value="{this.count.quantity}"><span>' + TMEPOGLOBALADMINJS.i18n_formula_this_count_quantity + '</span></li>' );
			list.append( '<li class="tc-var-field" data-value="{this.quantity}"><span>' + TMEPOGLOBALADMINJS.i18n_formula_this_quantity + '</span></li>' );

			// other elements
			if ( ids.length ) {
				list = $( '<h4 class="tc-var-header">' + TMEPOGLOBALADMINJS.i18n_formula_other_elements + '</h4>' );
				obj.append( list );

				list = $(
					'<div class="tc-clearfix tm-epo-switch-wrapper"><div class="formula-field-mode-selector"><input checked="checked" name="formulamode" class="formula-field-mode" id="formulafieldmode0" value="price" type="radio" ><label for="formulafieldmode0"><span class="tc-radio-text">' +
						TMEPOGLOBALADMINJS.i18n_formula_field_price +
						'</span></label><input name="formulamode" class="formula-field-mode" id="formulafieldmode1" value="value" type="radio" ><label for="formulafieldmode1"><span class="tc-radio-text">' +
						TMEPOGLOBALADMINJS.i18n_formula_field_value +
						'</span></label><input name="formulamode" class="formula-field-mode" id="formulafieldmode2"  value="quantity" type="radio" ><label for="formulafieldmode2"><span class="tc-radio-text">' +
						TMEPOGLOBALADMINJS.i18n_formula_field_quantity +
						'</span></label><input name="formulamode" class="formula-field-mode" id="formulafieldmode3" value="count" type="radio" ><label for="formulafieldmode3"><span class="tc-radio-text">' +
						TMEPOGLOBALADMINJS.i18n_formula_field_count +
						'</span></label></div></div>'
				);
				obj.append( list );

				list = $( '<ul class="tc-var-list other"></ul>' );
				obj.append( list );
				ids.forEach( function( section ) {
					section.forEach( function( id ) {
						if ( id.uniqid === exclude || id.uniqid === undefined ) {
							return;
						}
						value1 = $.tmEPOAdmin.getFieldValue( TCBUILDER[ id.sectionIndex ].fields[ id.fieldIndex ], 'internal_name', id.type );
						value2 = $.tmEPOAdmin.getFieldValue( TCBUILDER[ id.sectionIndex ].fields[ id.fieldIndex ], 'header_title', id.type );

						if ( id.type === 'product' ) {
							return;
						}
						if ( value2 === undefined ) {
							value2 = '';
						}
						if ( value2 !== '' ) {
							value2 = ' (' + value2 + ')';
						}
						value = value1 + value2;
						list.append( '<li title="' + id.uniqid + '" class="tc-var-field" data-value="{field.' + id.uniqid + '.price}"><span>' + value + '</span></li>' );
					} );
				} );
			}
		}
	};

	$( document ).ready( function() {
		templateEngine = $.epoAPI.applyFilter( 'tc_adjust_admin_template_engine', templateEngine );

		woocommerce_admin = window.woocommerce_admin;
		tinyMCEPreInit = window.tinyMCEPreInit;
		QTags = window.QTags;
		quicktags = window.quicktags;
		tinyMCE = window.tinyMCE;
		TMEPOOPTIONSJS = $.epoAPI.util.parseJSON( window.TMEPOOPTIONSJS );
		wc_enhanced_select_params = window.wc_enhanced_select_params;

		// deep cloning the array
		TCBUILDER = deepCopyArray( TMEPOOPTIONSJS );
		if ( TCBUILDER === undefined || ! TCBUILDER ) {
			TCBUILDER = [];
		}

		$.tmEPOAdmin.initialitize();
		$.tcToolTip();

		if ( TMEPOADMINJS ) {
			$( '.tm_extra_product_options_tab' ).on( 'click.cpf', function() {
				$.tmEPOAdmin.make_resizables( $( '.builder_wrapper' ) );
				$.tmEPOAdmin.make_resizables( $( '.bitem' ) );
				$( this ).off( 'click.cpf' );
			} );
		}
	} );
}( window, document, window.jQuery ) );
