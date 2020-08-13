jQuery( document ).ready( function( $ ) {

    if ( $( '.detailed-features' )[0] ) {
        changeClassNameOnResize();
        $( window ).on( 'resize', function() {
            changeClassNameOnResize();
        } );
    }

    function changeClassNameOnResize() {
        var privacyCenterBox = $( '.detailed-features' );
        var parentWidth = privacyCenterBox.parent().width();
        if ( parentWidth >= 1030 ) {
            privacyCenterBox.removeClass( 'ct-tablet' );
            privacyCenterBox.addClass( 'ct-desktop' );
        } else if ( parentWidth >= 570 ) {
            privacyCenterBox.removeClass( 'ct-desktop' );
            privacyCenterBox.addClass( 'ct-tablet' );
        } else {
            privacyCenterBox.removeClass( 'ct-tablet' );
            privacyCenterBox.removeClass( 'ct-desktop' );
        }
    }

    // SHOW COOKIE MODAL POPUP WHEN COOKIE SETTINGS READ ME BUTTON CLICKED IN PRIVACY CENTER SC
    if ( $( '.detailed-features' ).length ) {
        $( '.ct-full-link' ).on( 'click', function( e ) {
            var popupTrigger = $( this ).parent().find( '.ct-ultimate-triggler-modal-sc' );
            if ( popupTrigger.length ) {
                e.preventDefault();
                popupTrigger.click();
            }
        } );
    }

} );
