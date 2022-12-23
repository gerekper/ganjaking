/*global WCPhotographyCustomerParams */
( function( $ ) {

	$( function() {

		/**
		 * Select the collection.
		 */
		function initCollectionsSelect( target ) {
			if ( ! target ) {
				target = $( '.wc-photography-collections-select' );
			}

			// Triger change for already enhanced selects
			target.trigger( 'change.select2' );

			target = $( target ).filter( ':not(.enhanced)' )

			var select2_args = {
				placeholder: WCPhotographyCustomerParams.search_placeholder,
				minimumInputLength: 2,
				multiple: true,
				ajax: {
					url: WCPhotographyCustomerParams.ajax_url,
					dataType: 'json',
					quietMillis: 200,
					data: function ( term ) {
						return {
							term: term.term,
							action: 'wc_photography_search_collections',
							security: WCPhotographyCustomerParams.search_collections_nonce
						};
					},
					processResults: function ( data ) {
						return {
							results: data
						};
					}
				}
			};

			target.select2( select2_args ).addClass( 'enhanced' );
		}

		initCollectionsSelect();

		$( 'body' ).on( 'click', '.photography-add-collection a', function () {
			$( this ).next( '.fields' ).toggle();

			return false;
		})
		.on( 'click', '.photography-add-collection .button', function () {
			var button = $( this ),
				wrap   = button.closest( '.collection-form-field' ),
				input  = $( 'input.new-collection', wrap ),
				value  = input.val();

			if ( '' === value ) {
				input.trigger( 'focus' );
				return false;
			}

			input.prop( 'disabled', true );
			button.prop( 'disabled', true ).next( '.message' ).remove();
			button.after( ' <i class="loading">' + WCPhotographyCustomerParams.loading + '</i>' );

			$.ajax({
				url:      WCPhotographyCustomerParams.ajax_url,
				data:     {
					action:   'wc_photography_add_collection',
					security: WCPhotographyCustomerParams.add_collection_nonce,
					name:     value
				},
				type:     'POST',
				dataType: 'json',
				success:  function( response ) {
					input.val( '' );
					input.prop( 'disabled', false );
					button.prop( 'disabled', false ).next( '.loading' ).remove();

					if ( response.success ) {
						var select = $( '.wc-photography-collections-select' ),
						 option = new Option( response.data.text, response.data.id );
						option.selected = true;
						select.append( option );
					} else {
						button.after( '<div class="error inline message">' + WCPhotographyCustomerParams.collection_error + '</div>' );
					}
				}
			});

			return false;
		}).on( 'select2-selecting', '.wc-photography-collections-select', function ( e ) {
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

}( jQuery ) );
