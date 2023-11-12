
(function($){
	var currentRollbackButton;
	
 	rollbackVersion = {
		init: function() {
			$( document ).on('change', '.bsf-rollback-version-select' , rollbackVersion.onSelectVersion );
			$( document ).on('click', '.bsf-rollback-button' , rollbackVersion.onRollbackClick );
			$( document ).on('click', '.bsf-confirm-cancel' , rollbackVersion.closeRollbackPopup );
			$( document ).on('click', '.bsf-confirm-ok' , rollbackVersion.onRollbackOk );
		},
		
		onSelectVersion:function() {
			var selectRollback = jQuery( this );
			rollbackButton  = selectRollback.next( '.bsf-rollback-button' )
			placeholderText = rollbackButton.data( 'placeholder-text' );
			placeholderUrl  = rollbackButton.data( 'placeholder-url' );

			rollbackButton.attr( 'href', placeholderUrl.replace( 'VERSION', selectRollback.val() ) );
		},

		onRollbackClick: function ( e ) {
			e.preventDefault();
			var rollbackButton        = jQuery( this );
			// This will update the current rollback button object.
			currentRollbackButton     = rollbackButton;
			rollbackHeading           = $('.bsf-confirm-heading');
			rollbackConfirmText       = $('.bsf-rollback-text');
			closestDiv                = rollbackButton.closest('.bsf-rollback-version');
			versionNumber             = closestDiv.find('.bsf-rollback-version-select').val();
			selectProductName         = closestDiv.find('#bsf-product-name').val();

			// Rollback Heading text.
			rollbackHeadingdata = rollbackHeading.data('text').replace( '#PRODUCT_NAME#', selectProductName );
			rollbackHeading.html( rollbackHeadingdata );
			// Rollback Confirmation text.
			rollbackConfirmdata = rollbackConfirmText.data('text').replace( '#PRODUCT_NAME#', selectProductName );
			rollbackConfirmdata = rollbackConfirmdata.replace( '#VERSION#', versionNumber )
			rollbackConfirmText.html( rollbackConfirmdata );

			document.querySelector('.bsf-confirm-rollback-popup').style.display = 'block';
		},

		closeRollbackPopup: function ( e ) {
			document.querySelector('.bsf-confirm-rollback-popup').style.display = 'none';
		},

		onRollbackOk:function ( e ) {
			e.preventDefault();
			if( currentRollbackButton ){
				var redirectUrl = currentRollbackButton.closest('.bsf-rollback-version').find('.bsf-rollback-button').attr('href');
				location.href = redirectUrl;
			}
			rollbackVersion.closeRollbackPopup( e );
		}
	}

	$( document ).ready(function() {
		rollbackVersion.init();
	});
})( jQuery );
