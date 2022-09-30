/* globals yith_wcmbs_protected_links_params */
jQuery( function ( $ ) {
	/* Protected Links Table */
	var countLinks        = function () {
			return $( '.yith-wcmbs-admin-protected-link' ).length;
		},
		hasAtLeastOneLink = function () {
			var firstRow     = $( '.yith-wcmbs-admin-protected-link' ).first(),
				firstRowName = firstRow.find( '.yith-wcmbs-admin-protected-link__name-field' ),
				firstRowLink = firstRow.find( '.yith-wcmbs-admin-protected-link__url-field' );

			return firstRowName && firstRowName.val() && firstRowLink && firstRowLink.val();
		},
		getGreaterIndex   = function () {
			var indexesFields = $( '.yith-wcmbs-admin-protected-link__id' ),
				indexes       = [];

			indexesFields.each( function () {
				indexes.push( parseInt( $( this ).val(), 10 ) );
			} );

			return Math.max.apply(null, indexes);
		},
		index             = getGreaterIndex(),
		template          = wp.template( 'yith-wcmbs-admin-protected-links' ),
		linksContainer    = $( '.yith-wcmbs-admin-protected-links' ),
		addLink           = $( '#yith-wcmbs-admin-protected-links__add-link' ),
		mediaFrame, urlField;

	addLink.on( 'click', function () {
		index++;

		var newLink = $( template( { index: index } ) );

		newLink.find( '.select2' ).remove();
		newLink.find( '.yith-wcmbs-select2' ).select2();
		linksContainer.append( newLink );
	} );

	$( document ).on( 'click', '.yith-wcmbs-admin-protected-link__action__delete', function ( event ) {
		var target = $( event.target ),
			row    = target.closest( '.yith-wcmbs-admin-protected-link' );
		row.remove();

		if ( !countLinks() ) {
			addLink.click();
		}
	} );

	$( document ).on( 'click', '.yith-wcmbs-admin-protected-link__upload', function () {
		var linkContainer = $( this ).closest( '.yith-wcmbs-admin-protected-link' );

		urlField = linkContainer.find( '.yith-wcmbs-admin-protected-link__url-field' );

		if ( !mediaFrame ) {
			mediaFrame = wp.media( {
									   title   : yith_wcmbs_protected_links_params.i18n.uploadFileTitle,
									   button  : {
										   text: yith_wcmbs_protected_links_params.i18n.uploadFileButtonText
									   },
									   multiple: false
								   } );

			mediaFrame.on( 'select', function () {
				var file    = mediaFrame.state().get( 'selection' ).first(),
					fileUrl = file.toJSON().url;

				urlField.val( fileUrl );
			} );

			// Set post to 0 and set our custom type.
			mediaFrame.on( 'ready', function () {
				mediaFrame.uploader.options.uploader.params = {
					type: 'membership_protected_link'
				};
			} );

		}
		mediaFrame.open();
	} );

	if ( !hasAtLeastOneLink() ) {
		var pf_input     = $( '#_protected-files-enabled' ),
			pf_container = pf_input.closest( '.yith-plugin-fw-onoff-container' ),
			pf_onoff     = pf_container.find( '.yith-plugin-fw-onoff' );

		if ( pf_input.is( ':checked' ) ) {
			pf_onoff.click();
		}
	}
} );