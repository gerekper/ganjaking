( function ( $, root ) {
    "use strict";

    // Create the local library object, to be exported or referenced globally later
    var lib = {};

    lib.blockParams = {
        message        : null,
        overlayCSS     : { background: '#fff', opacity: 0.7 },
        ignoreIfBlocked: true
    };

    lib.scrollTo = function ( _element ) {
        var _offset = _element.offset();

        if ( _offset && _offset.top ) {
            $( 'html, body' ).animate( { scrollTop: _offset.top - 32 - 20 } );
        }
    };

    root.yith_pos = lib;

} )( jQuery, this );