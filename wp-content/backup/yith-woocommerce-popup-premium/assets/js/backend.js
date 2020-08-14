jQuery( document ).ready( function( $ ) {

    $( document ).on( 'click', '.post-type-yith_popup .yith-plugin-fw-onoff', function() {

        var input = $( this ).prev( 'input' );
        var checked = input.is( ':checked' );


        var status = (  !checked ) ? 'disable' : 'enable';
        $.ajax({
            cache: false,
            data: 'post_id='+input.data('id')+'&action='+input.data('action')+'&status='+status,
            success: function(data, status, jqXHR){

            },
            url: ypop_backend.url
        });
    } );
} );