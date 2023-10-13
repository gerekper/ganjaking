/* global jQuery, yith_booking, yith, wp, lodash */
( function ( $, _ ) {
	var _x = wp.i18n._x;

	var templates = {
			table       : wp.template( 'yith-wcbk-resources-modal-table' ),
			blank       : wp.template( 'yith-wcbk-resources-modal-content-blank' ),
			content     : wp.template( 'yith-wcbk-resources-modal-content' ),
			resourceData: wp.template( 'yith-wcbk-product-resource-data' )
		},
		ajaxCall,
		searchTimeout,
		modal;

	var dom                 = {
			main: $( '#yith-wcbk-booking-product-resources' ),
			list: $( '#yith-wcbk-booking-product-resources__list' )
		},
		resourceIds         = dom.main.data( 'resource-ids' ) || [],
		loadResources       = function ( options ) {
			if ( modal ) {
				options       = options || {};
				var data      = options.data || {},
					block     = options.block || {},
					firstLoad = options.firstLoad || false;

				data = $.extend( data, { request: 'resources_get_resources' } );

				if ( ajaxCall ) {
					ajaxCall.abort();
				}

				ajaxCall = yith_booking.adminAjax(
					data,
					{ block: block }
				).done(
					function ( response ) {
						if ( response.success ) {
							var tableWrap, theTable, responseData;

							responseData                      = response.data;
							responseData.product_resource_ids = resourceIds;
							responseData.page                 = data.page || 1;

							if ( !!firstLoad ) {
								modal.elements.content.html( '' );

								var theContent = $( templates.content() );

								if ( !response.data.items.length ) {
									theContent = $( templates.blank() );
								}

								tableWrap = theContent.find( '#yith-wcbk-resources-modal-table-wrapper' );

								modal.elements.content.append( theContent );
							} else {
								tableWrap = $( '#yith-wcbk-resources-modal-table-wrapper' );
								tableWrap.html( '' );
							}

							theTable = $( templates.table( responseData ) );
							tableWrap.append( theTable );

							if ( firstLoad ) {
								$( '#yith-wcbk-resources-modal-search' ).trigger( 'focus' );
							}
						}
					}
				);
			}
		},
		checkIfHasResources = function () {
			var resources = dom.list.find( '.yith-wcbk-booking-product-resource' );

			if ( resources.length ) {
				dom.main.addClass( 'has-resources' );
			} else {
				dom.main.removeClass( 'has-resources' );
			}

		},
		updateResourceIds   = function () {
			resourceIds = _.uniq( resourceIds );
			dom.main.data( 'resource-ids', resourceIds );
		},
		addResource         = function ( resource ) {
			resourceIds.push( resource.id );

			updateResourceIds();

			dom.list.append(
				$( templates.resourceData( resource ) )
			);

			dom.main.addClass( 'has-resources' );

			$( document ).trigger( 'yith_wcbk_product_metabox_dynamic_durations' );
		},
		removeResource      = function ( resourceID ) {
			resourceIds = resourceIds.filter( function ( id ) {
				return id !== resourceID;
			} );

			updateResourceIds();

			dom.list.find( '.yith-wcbk-booking-product-resource[data-id=' + resourceID + ']' ).remove();
			checkIfHasResources();
		};

	$( document ).on( 'click', '.yith-wcbk-booking-product-resources__add', function ( e ) {
		e.preventDefault();

		modal = yith.ui.modal(
			{
				classes      : {
					wrap: 'yith-wcbk-resources-modal'
				},
				title        : _x( 'Add resources', 'Modal title', 'yith-booking-for-woocommerce' ),
				scrollContent: false,
				width        : 700,
				onClose      : function () {
					modal = false;
				}
			}
		);

		loadResources( { block: modal.elements.content, firstLoad: true } );
	} );

	$( document ).on( 'keyup', '#yith-wcbk-resources-modal-search', function () {
		var value = $( this ).val();

		if ( searchTimeout ) {
			clearTimeout( searchTimeout );
		}

		searchTimeout = setTimeout( function () {
			loadResources(
				{
					data : {
						search: value
					},
					block: modal.elements.content
				}
			);
		}, 300 );
	} );

	$( document ).on( 'click', '.yith-wcbk-resources-modal-table .resource .resource__actions .add', function () {
		var resourceEl = $( this ).closest( '.resource' ),
			resource   = {
				id   : resourceEl.data( 'id' ),
				name : resourceEl.data( 'name' ),
				image: resourceEl.data( 'image' )
			};

		addResource( resource );
		resourceEl.addClass( 'added' );
	} );

	$( document ).on( 'click', '.yith-wcbk-resources-modal-table .resource .resource__actions .remove', function () {
		var resourceEl = $( this ).closest( '.resource' ),
			resourceID = resourceEl.data( 'id' );

		removeResource( resourceID );
		resourceEl.removeClass( 'added' );
	} );

	$( document ).on( 'click', '.yith-wcbk-booking-product-resource__remove', function () {
		var resourceEl = $( this ).closest( '.yith-wcbk-booking-product-resource' ),
			resourceID = resourceEl.data( 'id' );

		removeResource( resourceID );
	} );

	$( document ).on( 'click', '.yith-wcbk-resources-modal-table__pagination .pagination-action', function ( e ) {
		e.preventDefault();
		e.stopPropagation();
		var el          = $( this ),
			pagination  = el.closest( '.yith-wcbk-resources-modal-table__pagination' ),
			currentPage = pagination.data( 'current-page' ),
			totalPages  = pagination.data( 'total-pages' );

		if ( !el.is( '.disabled' ) ) {
			var pageToLoad = el.is( '.next' ) ? ( currentPage + 1 ) : ( currentPage - 1 );

			if ( pageToLoad > 0 && pageToLoad <= totalPages ) {
				var search = $( '#yith-wcbk-resources-modal-search' ).val(),
					data   = {
						page: pageToLoad
					};

				if ( search ) {
					data.search = search;
				}

				loadResources( { data: data, block: modal.elements.content } );

			}
		}
	} );

	// Label and placeholders.
	var label                       = $( '#_yith_booking_resources_label' ),
		fieldLabel                  = $( '#_yith_booking_resources_field_label' ),
		initialPlaceholder          = fieldLabel.attr( 'placeholder' ),
		updateFieldLabelPlaceholder = function () {
			var theValue = label.val();
			fieldLabel.attr( 'placeholder', !!theValue ? theValue : initialPlaceholder );
		};

	label.on( 'keyup', updateFieldLabelPlaceholder );
	updateFieldLabelPlaceholder();

} )( jQuery, lodash );