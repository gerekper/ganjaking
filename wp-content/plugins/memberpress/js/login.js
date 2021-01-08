(function($) {
  __ = wp.i18n.__;

  function mpResetToggle( show ) {
    $toggleButton
      .attr({
        'aria-label': show ? __( 'Show password' ) : __( 'Hide password' )
      })
      .find( '.text' )
        .text( show ? __( 'Show' ) : __( 'Hide' ) )
      .end()
      .find( '.dashicons' )
        .removeClass( show ? 'dashicons-hidden' : 'dashicons-visibility' )
        .addClass( show ? 'dashicons-visibility' : 'dashicons-hidden' );
  }

  $(document).ready( function() {
    $pass = $('#user_pass');
    $toggleButton = $('button.mp-hide-pw');
    $toggleButton.show().on( 'click', function () {
      if ( 'password' === $pass.attr( 'type' ) ) {
        $pass.attr( 'type', 'text' );
        mpResetToggle( false );
      } else {
        $pass.attr( 'type', 'password' );
        mpResetToggle( true );
      }
    });
  });
})(jQuery);