jQuery( document ).ready( function( $ ) {
	var body = $( 'body' ),
	    list = $( '#the-list' );

	$( '.update-status' ).click( function() {
		var id = $( this ).data( 'request_id' );
		var value = $( '#status_' + id ).val();
		var nonce = $( '#update_status_nonce_' + id ).val();

		if ( value ) {
			var data = {
				'action': 'warranty_update_request_fragment',
				'type': 'change_status',
				'status': value,
				'request_id': id,
				'security': nonce,
			};
			$.ajax( {
				type: 'POST',
				url: ajaxurl,
				data: data,
				success: function( response ) {
					if ( response ) {
						window.location.href = response;
					}
				},
			} );
		}
	} );

	$( 'a.inline-edit' ).click( function( e ) {
		e.preventDefault();

		var req_id = $( this ).data( 'request_id' );
		var tr = $( this ).closest( 'tr' );
		var cloned = $( '#inline-edit-' + req_id ).clone();

		$( '#the-list tr#inline-edit-' + req_id ).find( '.close_tr' ).click();

		cloned.insertAfter( tr ).show();

		$( '<tr class=\'hidden\'></tr>' ).insertBefore( cloned );

		$( '#the-list .tip' ).tipTip( {
			maxWidth: '400px',
		} );
	} );

	list.on( 'click', '.close-form', function( e ) {
		e.preventDefault();

		$( this ).parents( 'div.closeable' ).hide();
	} );

	list.on( 'click', '.close_tr', function() {
		$( this ).parents( 'tr' ).remove();
		list.find( 'tr.hidden' ).remove();
	} );

	// RMA Update
	list.on( 'click', '.rma-update', function() {
		var id = $( this ).data( 'id' );
		var request = list;
		var inputs = request.find( 'input,textarea,#status_' + id );
		var data = $( inputs ).serializeArray();

		data.push( { name: 'action', value: 'warranty_update_inline' } );
		data.push( { name: 'id', value: id } );
		data.push( { name: '_wpnonce', value: $( this ).data( 'security' ) } );

		request.block( {
			message: null, overlayCSS: {
				background: '#FFFFFF', opacity: 0.6,
			},
		} );

		$.post( ajaxurl, data, function( resp ) {
			if ( 'OK' === resp.status ) {
				var status_block = $( request ).find( '.warranty-update-message' );
				status_block.find( 'p' ).html( resp.message );
				status_block.show();
			} else {
				alert( resp.message );
			}
			request.unblock();
		} );

	} );

	// Uploading files
	var file_frame;

	list.on( 'click', '.rma-upload-button', function( event ) {
		event.preventDefault();

		var request_id = $( this ).data( 'id' );

		$( '#shipping_label_image_file_' + request_id ).click();
	} );

	list.on( 'change', '[name="shipping_label_image_file"]', function( e ) {
		var files = $( this ).prop( 'files' ), request_id = $( this ).data( 'request_id' ), data;

		if ( !files[ 0 ] ) {
			return;
		}

		data = new FormData();
		data.append( 'action', 'warranty_shipping_label_file_upload' );
		data.append( 'id', request_id );
		data.append( 'warranty_upload', files[ 0 ] );
		data.append( 'security', $( this ).data( 'security' ) );

		$.ajax( {
			url: ajaxurl,
			type: 'post',
			data: data,
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function( response ) {
				if ( response.success ) {
					$( '#shipping_label_' + request_id ).val( response.file_url );
					$( '#shipping_label_id_' + request_id ).val( response.file_id );
				} else {
					alert( response.message );
				}
			},
		} );
	} );

	body.on( 'click', '.warranty-process-refund', function() {
		var id = $( this ).data( 'id' );
		var security = $( this ).data( 'security' );
		var table = $( 'table.toplevel_page_warranties' );
		var tb_window = $( this ).parents( '#TB_window' );
		var amount = tb_window.find( 'input.amount' ).val();

		tb_remove();

		table.block( {
			message: null,
			overlayCSS: {
				background: '#FFFFFF',
				opacity: 0.6,
			},
		} );

		$.post( ajaxurl, {
			action: 'warranty_refund_item',
			ajax: true,
			id: $( this ).data( 'id' ),
			amount: amount,
			_wpnonce: security,
		}, function( resp ) {
			if ( 'OK' === resp.status ) {
				window.location.reload();
			} else {
				alert( resp.message );
				table.unblock();
			}

		} );
	} );

	body.on( 'click', '.warranty-process-coupon', function() {
		var id = $( this ).data( 'id' );
		var security = $( this ).data( 'security' );
		var table = $( 'table.toplevel_page_warranties' );
		var tb_window = $( this ).parents( '#TB_window' );
		var amount = tb_window.find( 'input.amount' ).val();

		tb_remove();

		table.block( {
			message: null, overlayCSS: {
				background: '#FFFFFF', opacity: 0.6,
			},
		} );

		$.post( ajaxurl, {
			action: 'warranty_send_coupon',
			ajax: true,
			id: $( this ).data( 'id' ),
			amount: amount,
			_wpnonce: security,
		}, function( resp ) {
			if ( 'OK' === resp.status ) {
				window.location.reload();
			} else {
				alert( resp.message );
				table.unblock();
			}

		} );
	} );

	body.on( 'click', '.add_note', function( e ) {
		e.preventDefault();
		var container = $( this ).parents( '.inline-edit-col' );
		var request = $( this ).data( 'request' );
		var nonce = $( this ).data( 'nonce' );
		var notes_list = container.find( 'ul.admin-notes' );
		var note = $( '#admin_note_' + request ).val();

		if ( 0 === note.length ) {
			return;
		}

		container.block( {
			message: null, overlayCSS: {
				background: '#FFFFFF', opacity: 0.6,
			},
		} );

		var data = { action: 'warranty_add_note', security: nonce, request: request, note: note };

		$.post( ajaxurl, data, function( resp ) {
			if ( resp.length < 1 ) {
				alert( wc_enhanced_select_params.i18n_ajax_error );
				container.unblock();
				return;
			}

			$( notes_list ).html( resp );
			container.unblock();
		} );
	} );

	body.on( 'click', '.delete_note', function( e ) {
		e.preventDefault();
		var container = $( this ).parents( '.inline-edit-col' );
		var note = $( this ).data( 'note_id' );
		var nonce = $( this ).data( 'nonce' );
		var request = $( this ).data( 'request' );
		var notes_list = container.find( 'ul.admin-notes' );

		container.block( {
			message: null,
			overlayCSS: {
				background: '#FFFFFF',
				opacity: 0.6,
			},
		} );

		var data = { action: 'warranty_delete_note', request: request, note_id: note, security: nonce };

		$.post( ajaxurl, data, function( resp ) {
			$( notes_list ).html( resp );
			container.unblock();
		} );
	} );

	$( '.tip' ).tipTip( { maxWidth: '400px' } );
});