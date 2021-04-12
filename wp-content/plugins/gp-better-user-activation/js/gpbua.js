jQuery( document ).ready( function( $ ) {

	$( '#gpbua-tabs' ).find( '#tabs' ).tabs();

	// reset content button
	$('.gpbua-reset-content-button').click( function( e ) {

		var view    = $(this).data( 'gpbua_view' ),
			spinner = new gfAjaxSpinner( $( this ).append( '<span />' ).find( 'span' ), null, 'position:relative;top:3px;left:5px;' );

		getDefaultContent( view, function() {
			spinner.elem.remove();
			spinner.destroy();
		} );

		e.preventDefault();

	} );

	window.GPBUA = {

		insertMergeTag: function( elem, tag ) {

			var $select = $( elem ),
				editor  = tinymce.EditorManager.get( $select.parents( '.gpbua-tab' ).find( 'textarea' ).attr( 'id' ) );

			editor.insertContent( tag );

			$select.val( '' );

		}

	};

	// reload default content
	function getDefaultContent( view, callback ) {

		var data = {
			'action': 'gpbua_reset_content',
			'view': view
		};

		$.post(ajaxurl, data, function( response ) {
			updateViewContent( view, response );
			callback();
		});

	}

	function updateViewContent( view, content ) {

		var editorKey = '_gpbua_activation_' + view;
		var editor = tinymce.get( editorKey );

		if( editor ) {

			editor.setContent( content );

		} else {

			$('#' + editorKey).val( content )

		}

	}

} );