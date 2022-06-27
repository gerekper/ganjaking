jQuery( function( $ ) {
				
				var up_pointers = up_pointer_data.up_pointers;
				
				setTimeout( init_up_pointers, 800 );

				function init_up_pointers() {
					console.log(up_pointers);
					console.log(up_pointers.pointers);
					$.each( up_pointers.pointers, function( i ) {
						show_up_pointer( i );
						return false;
					});
				}

				function show_up_pointer( id ) {
					var pointer = up_pointers.pointers[ id ];
					var options = $.extend( pointer.options, {
						 close: function() {
              				  $.post( ajaxurl, {
                  					  pointer: id,
                 				      action: 'dismiss-wp-pointer'
              				  });
           				 }
					} );
					var this_pointer = $( pointer.target ).pointer( options );
					this_pointer.pointer( 'open' );

	}
});