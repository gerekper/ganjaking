// Content Switch Widget
( function( $ ) {
    'use strict';

    $( document ).ready(
        function() {
            $( '[data-content-switcher]' ).on( 'change', function() {
                var $this = $( this ),
                    $wrapper = $this.closest( '.content-switcher-wrapper' ),
                    switcherId = $this.is( ':checked' ) ? 'nav-second' : 'nav-first';

                // Switch
                $wrapper.find( '.switcher-label' ).removeClass( 'active' );
                $wrapper.find( '.switcher-label[data-switch-id=' + switcherId + ']' ).addClass( 'active' );

                // Content
                $wrapper.find( '.switch-content' ).removeClass( 'active' );
                $wrapper.find( '[data-content-id=' + switcherId + ']' ).addClass( 'active' );
            } );
        }
    );

} ).apply( this, [jQuery] );
