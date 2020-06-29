/*global plupload, pluploadL10n, wpQueueError, wpFileError, WCPhotographyBatchUploadParams, accounting, woocommerce_admin */
/*jshint devel: true */
( function( $, _ ) {

	$( function() {
		var photographyIndex = 1;
		$( '#wc-photography-uploader .drag-drop-inside' ).append( '<div class="wc-photography-progress-bar"><div></div></div>' );

		/**
		 * Add loading.
		 */
		function addLoading() {
			var background = '';

			if ( WCPhotographyBatchUploadParams.ajax_loading_image ) {
				background = ' url(' + WCPhotographyBatchUploadParams.ajax_loading_image + ') no-repeat center';
			}

			$( '#wc-photography-image-edit .postbox' ).block({
				message: null,
				overlayCSS: {
					background: '#fff' + background,
					opacity: 0.6
				}
			});
		}

		/**
		 * Remove loading.
		 */
		function removeLoading() {
			$( '#wc-photography-image-edit .postbox' ).unblock();
		}

		/**
		 * Select the collection.
		 */
		function initCollectionsSelect( target ) {
			if ( ! target ) {
				target = $( '.wc-photography-collections-select' );
			}

			// Triger change for already enhanced selects
			target.trigger( 'change.select2' );

			target = $( target ).filter( ':not(.enhanced)' );

			var select2_args = {
				placeholder: WCPhotographyBatchUploadParams.search_placeholder,
				minimumInputLength: 2,
				multiple: true,
				ajax: {
					url: WCPhotographyBatchUploadParams.ajax_url,
					dataType: 'json',
					quietMillis: 200,
					data: function ( term ) {
						return {
							term: WCPhotographyBatchUploadParams.isLessThanWC30 ? term : term.term,
							action: 'wc_photography_search_collections',
							security: WCPhotographyBatchUploadParams.search_collections_nonce
						};
					},
					processResults: function ( data ) {
						return {
							results: data
						};
					}
				}
			};

			if ( WCPhotographyBatchUploadParams.isLessThanWC30 ) {
				select2_args.initSelection = function( element, callback ) {
					var data = $.parseJSON( element.attr( 'data-selected' ) );

					return callback( data );
				};

				select2_args.formatSelection = function( data ) {
					return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
				};

				select2_args.ajax.results = select2_args.ajax.processResults;
			}

			target.select2( select2_args ).addClass( 'enhanced' );
		}

		initCollectionsSelect();

		// Batch upload.
		var WCPhotographyBatchUpload = new plupload.Uploader( WCPhotographyBatchUploadParams.plupload ),
			WCPhotographyBar         = $( '#wc-photography-uploader .wc-photography-progress-bar' ),
			WCPhotographyProgress    = $( '#wc-photography-uploader .wc-photography-progress-bar div' ),
			WCPhotographyOutput      = $( '#wc-photography-uploader-output' );

		// Stop the script.
		if ( ! WCPhotographyBatchUpload ) {
			return;
		}

		WCPhotographyBatchUpload.bind( 'init', function( upload ) {
			var ui = $( '#wc-photography-uploader-upload-ui' );

			if ( upload.features.dragdrop && ! $( document.body ).hasClass( 'mobile' ) ) {
				ui.addClass( 'drag-drop' );

				$( '#wc-photography-drag-drop-area' ).bind( 'dragover.wp-uploader', function() {
					ui.addClass( 'drag-over' );
				}).bind( 'dragleave.wp-uploader, drop.wp-uploader', function() {
					ui.removeClass( 'drag-over' );
				});
			} else {
				ui.removeClass( 'drag-drop' );

				$( '#wc-photography-drag-drop-area' ).unbind( '.wp-uploader' );
			}

			if ( 'html4' === upload.runtime ) {
				$( '.upload-flash-bypass' ).hide();
			}
		});

		WCPhotographyBatchUpload.init();

		WCPhotographyBatchUpload.bind( 'filesAdded', function( upload, files ) {
			var hundredmb = 100 * 1024 * 1024,
				max       = parseInt( upload.settings.max_file_size, 10 );

			$( '#wc-photography-uploader-error' ).html( '' );
			$( '#wc-photography-uploader .form-table :input' ).attr( 'disabled', 'disabled' );

			$( WCPhotographyBar ).show().css( 'display', 'block' );

			plupload.each( files, function( file ) {
				if ( max > hundredmb && file.size > hundredmb && 'html5' !== upload.runtime ) {
					errorHtmlHandler( upload, file, true );
				}
			});

			upload.refresh();
			upload.start();
		});

		WCPhotographyBatchUpload.bind( 'uploadProgress', function( upload ) {
			$( WCPhotographyProgress ).css( 'width', upload.total.percent + '%' );
		});

		WCPhotographyBatchUpload.bind( 'fileUploaded', function( upload, file, info ) {
			var collections = $( '#wc-photography-batch-collection' );

			if ( WCPhotographyBatchUploadParams.isLessThanWC30 ) {
				collections = collections.val();
			} else {
				var tmp = collections.select2('data');
				collections = [];
				tmp.forEach( function (obj) {
					collections.push( obj.id );
				} );
				collections = collections.join( ',' );
			}

			$.ajax({
				url:      WCPhotographyBatchUploadParams.ajax_url,
				data:     {
					action:      'wc_photography_batch_upload',
					security:    WCPhotographyBatchUploadParams.batch_upload_nonce,
					image_id:    info.response,
					sku_pattern: $( '#wc-photography-batch-sku' ).val(),
					price:       accounting.unformat( $( '#wc-photography-batch-price' ).val(), woocommerce_admin.mon_decimal_point ),
					collections: collections,
				},
				type:     'POST',
				dataType: 'json',
				success:  function( response ) {
					$( WCPhotographyOutput ).append( response );

					var template = _.template( $( 'script#wc-photography-image-template' ).html() );

					response.index = photographyIndex;
					$( '#wc-photography-image-edit .wc-metaboxes' ).append( template( response ) );
					initCollectionsSelect();

					photographyIndex++;
				}
			});
		});

		WCPhotographyBatchUpload.bind( 'uploadComplete', function() {
			$( WCPhotographyBar ).hide().css( 'display', 'none' );
			$( WCPhotographyProgress ).removeAttr( 'style' );

			// Show the edit area.
			$( '#wc-photography-image-edit' ).css( 'display', 'block' );
			$( '#wc-photography-uploader .form-table :input' ).removeAttr( 'disabled' );
		});

		WCPhotographyBatchUpload.bind( 'error', function( upload, error ) {
			var hundredmb = 100 * 1024 * 1024,
				error_el  = $('#wc-photography-uploader-error'),
				max;

			switch ( error.code ) {
				case plupload.FAILED :
				case plupload.FILE_EXTENSION_ERROR :
					error_el.html( '<p class="error">' + pluploadL10n.upload_failed + '</p>' );
					break;
				case plupload.FILE_SIZE_ERROR :
					errorHtmlHandler( upload, error.file );
					break;
				case plupload.IMAGE_FORMAT_ERROR :
					wpFileError( upload, pluploadL10n.not_an_image );
					break;
				case plupload.IMAGE_MEMORY_ERROR :
					wpFileError( upload, pluploadL10n.image_memory_exceeded );
					break;
				case plupload.IMAGE_DIMENSIONS_ERROR :
					wpFileError( upload, pluploadL10n.image_dimensions_exceeded );
					break;
				case plupload.GENERIC_ERROR :
					wpQueueError( pluploadL10n.upload_failed );
					break;
				case plupload.IO_ERROR :
					max = parseInt( plupload.settings.max_file_size, 10 );

					if ( max > hundredmb && upload.size > hundredmb ) {
						wpFileError( upload, pluploadL10n.big_upload_failed.replace( '%1$s', '<a class="uploader-html" href="#">' ).replace( '%2$s', '</a>' ) );
					} else {
						wpQueueError( pluploadL10n.io_error);
					}
					break;
				case plupload.HTTP_ERROR :
					wpQueueError( pluploadL10n.http_error);
					break;
				case plupload.INIT_ERROR :
					$( '.media-upload-form' ).addClass( 'html-uploader' );
					break;
				case plupload.SECURITY_ERROR :
					wpQueueError( pluploadL10n.security_error );
					break;

				default :
					wpFileError( upload, pluploadL10n.default_error );
					break;
			}

			upload.refresh();
		});

		function errorHtmlHandler( upload, file, over100mb ) {
			var message;

			if ( over100mb ) {
				message = pluploadL10n.big_upload_queued.replace( '%s', file.name ) + ' ' + pluploadL10n.big_upload_failed.replace( '%1$s', '<a class="uploader-html" href="#">' ).replace( '%2$s', '</a>' );
			} else {
				message = pluploadL10n.file_exceeds_size_limit.replace( '%s', file.name );
			}

			$( '#wc-photography-uploader-error' ).html( '<div class="error"><p>' + message + '</p></div>' );

			upload.removeFile( file );
		}

		// Images close/open.
		$( '.wc-metaboxes-wrapper' )
			.on( 'click', '.wc-metabox h3', function() {
				$( this ).next( '.wc-metabox-content' ).toggle();
			})
			.on( 'click', '.expand_all', function() {
				$( this ).closest( '.wc-metaboxes-wrapper' ).find( '.wc-metabox > .wc-metabox-content' ).show();

				return false;
			})
			.on( 'click', '.close_all', function() {
				$( this ).closest( '.wc-metaboxes-wrapper' ).find( '.wc-metabox > .wc-metabox-content' ).hide();

				return false;
			});
		$( '.wc-metabox.closed' ).each( function() {
			$( this ).find( '.wc-metabox-content' ).hide();
		});

		// Image actions.
		$( 'body' ).on( 'click', '#wc-photography-image-edit .wc-metabox .remove', function() {
			var image = $( this ).closest( '.wc-metabox' );

			addLoading();

			$.ajax({
				url:      WCPhotographyBatchUploadParams.ajax_url,
				data:     {
					action:   'wc_photography_delete_image',
					security: WCPhotographyBatchUploadParams.delete_image_nonce,
					id:       image.attr( 'data-id' )
				},
				type:     'POST',
				success:  function() {
					image.remove();

					// Hides the editing area if don't have items.
					if ( 0 === $( '#wc-photography-image-edit .wc-metabox' ).length ) {
						$( '#wc-photography-image-edit' ).hide();
					}

					removeLoading();
				}
			});

			return false;
		})
		.on( 'click', '#wc-photography-image-edit .submit .button', function() {
			var images = $( '#wc-photography-image-edit .wc-metaboxes :input' ).serialize();

			addLoading();

			$.ajax({
				url:      WCPhotographyBatchUploadParams.ajax_url,
				data:     {
					action:   'wc_photography_save_images',
					security: WCPhotographyBatchUploadParams.save_images_nonce,
					images:   images
				},
				type:     'POST',
				success:  function() {
					var wrap = $( '#wc-photography-image-edit' );

					$( 'div.updated', wrap ).remove();

					wrap.prepend( '<div class="updated"><p>' + WCPhotographyBatchUploadParams.edit_success_message + '</p></div>' );

					$( 'html, body' ).animate({
						scrollTop: wrap.offset().top - 100
					}, 500 );

					removeLoading();
				}
			});

			return false;
		})
		.on( 'keyup', '#wc-photography-image-edit .sku-field', function() {
			var image = $( this ).closest( '.wc-metabox' ),
				index = image.attr( 'data-index' );

			$( '.image-name', image ).html( index + '. ' + $( this ).val() );
		})
		.on( 'click', '.photography-add-collection a', function () {
			$( this ).next( '.fields' ).toggle();

			return false;
		})
		.on( 'click', '.photography-add-collection .button', function () {
			var button = $( this ),
				wrap   = button.closest( '.collection-form-field' ),
				input  = $( 'input.new-collection', wrap ),
				value  = input.val();

			if ( '' === value ) {
				input.focus();
				return false;
			}

			input.attr( 'disabled', 'disabled' );
			button.attr( 'disabled', 'disabled' ).next( '.message' ).remove();
			button.after( ' <i class="loading">' + WCPhotographyBatchUploadParams.loading + '</i>' );

			$.ajax({
				url:      WCPhotographyBatchUploadParams.ajax_url,
				data:     {
					action:   'wc_photography_add_collection',
					security: WCPhotographyBatchUploadParams.add_collection_nonce,
					name:     value
				},
				type:     'POST',
				dataType: 'json',
				success:  function( response ) {
					input.val( '' );
					input.removeAttr( 'disabled' );
					button.removeAttr( 'disabled' ).next( '.loading' ).remove();

					// Show the response.
					if ( response.success ) {
						if ( WCPhotographyBatchUploadParams.isLessThanWC30 ) {
							var select = $( 'input.wc-photography-collections-select', wrap ),
								items  = [],
								values = [];

							// Include the new collection.
							$( '.selected-option', wrap ).each( function( index, val ) {
								var current = $( val );

								items.push({
									id: current.attr( 'data-id' ),
									text: current.text()
								});

								values.push( current.attr( 'data-id' ) );
							});

							items.push( response.data );
							values.push( response.data.id );

							select
								.attr( 'data-selected', JSON.stringify( items ) )
								.val( values.toString() );

							initCollectionsSelect( select );

							// Toggle the add new collections field.
							$( '.fields', wrap ).toggle();
						} else {
							var select = $( '.wc-photography-collections-select' );
							var option = new Option( response.data.text, response.data.id );
							option.selected = true;
							select.append( option );
						}
					} else {
						button.after( '<div class="error inline message">' + WCPhotographyBatchUploadParams.collection_error + '</div>' );
					}
				}
			});

			return false;
		})
		.on( 'select2-selecting', '.wc-photography-collections-select', function ( e ) {
			var wrap  = $( this ).prev( '.wc-photography-collections-select' ),
				items = [];

			$( '.selected-option', wrap ).each( function( index, val ) {
				var current = $( val );

				items.push({
					id: current.attr( 'data-id' ),
					text: current.text()
				});
			});

			items.push({
				id: e.choice.id,
				text: e.choice.text
			});

			$( this ).attr( 'data-selected', JSON.stringify( items ) );
		})
		.on( 'select2-removed', '.wc-photography-collections-select', function() {
			var wrap  = $( this ).prev( '.wc-photography-collections-select' ),
				items = [];

			$( '.selected-option', wrap ).each( function( index, val ) {
				var current = $( val );

				items.push({
					id: current.attr( 'data-id' ),
					text: current.text()
				});
			});

			$( this ).attr( 'data-selected', JSON.stringify( items ) );
		});

	});

}( jQuery, _ ) );
