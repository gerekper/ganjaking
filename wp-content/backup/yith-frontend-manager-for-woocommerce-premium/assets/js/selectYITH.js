(function($) {
    if( typeof $.selectYITH == 'undefined' ){
        $.fn.selectYITH = function( options ){
            options = options || {};

            var t = $(this);

            if( typeof $.selectWoo != 'undefined' ){
                t.selectWoo( options );
            }

            else {
                t.select2( options );
            }
        };
    }
})(jQuery);