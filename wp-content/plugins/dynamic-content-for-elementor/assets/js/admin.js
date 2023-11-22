'use strict';

jQuery(function() {
    jQuery('.js-dce-select').select2();
});

// Checkbox for all features on Dashboard
jQuery( document ).on( 'click', '#dce-feature-activate-all', function( event ) {
	jQuery( this ).closest( '.dce-container' ).find( 'input.dce-checkbox' ).prop( 'checked', true );
	event.preventDefault();
	this.closest( '.dce-container' ).scrollIntoView({
		behavior: 'smooth'
	});
} );
jQuery( document ).on( 'click', '#dce-feature-deactivate-all', function( event ) {
	jQuery( this ).closest( '.dce-container' ).find( 'input.dce-checkbox' ).prop( 'checked', false );
	event.preventDefault();
	this.closest( '.dce-container' ).scrollIntoView({
		behavior: 'smooth'
	});
} );

// Checkbox for groups on Dashboard
jQuery( document ).on( 'click', '.dce-group-activate-all', function( event ) {
	jQuery( this ).closest( '.dce-feature-group' ).find( 'input.dce-checkbox' ).prop( 'checked', true );
	event.preventDefault();
	this.closest( '.dce-feature-group' ).scrollIntoView({
		behavior: 'smooth'
	});
} );
jQuery( document ).on( 'click', '.dce-group-deactivate-all', function( event ) {
	jQuery( this ).closest( '.dce-feature-group' ).find( 'input.dce-checkbox' ).prop( 'checked', false );
	event.preventDefault();
	this.closest( '.dce-feature-group' ).scrollIntoView({
		behavior: 'smooth'
	});
} );

(function( $ ) {
    $( function() {
		// Dismissable Admin Notices (from https://www.alexgeorgiou.gr/persistently-dismissible-notices-wordpress/):
        $( '.dce-dismissible-notice' ).on( 'click', '.notice-dismiss', function( event, el ) {
            var $notice = $(this).parent('.notice.is-dismissible');
            var dismiss_url = $notice.attr('data-dismiss-url');
            if ( dismiss_url ) {
                $.get( dismiss_url );
            }
        });
		let form = $('#dce-rollback-form');
		let confirmMsg = form.data('confirm');
		form.on( 'submit', (event) => {
			if (confirm(confirmMsg)) {
				return true;
			}
			return false;
		})
    } );
})( jQuery );
