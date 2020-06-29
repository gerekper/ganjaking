( function( $ ) {

wp.customize( 'loginpress_customization[google_font]', function( value ) {
    value.bind( function( loginPressVal ) {

      if( loginPressVal == '' ) {
        // live change #login font-family.
        $('#customize-preview iframe')
        .contents()
        .find( '#login' )
        .css( 'font-family', 'inherit' );
        // live change form and input fields font-family.
        $('#customize-preview iframe')
        .contents()
        .find( '.login form .input, .login input[type="text"]' )
        .css( 'font-family', 'inherit' );
        // live change submit button font-family.
        $('#customize-preview iframe')
        .contents()
        .find( '.login input[type="submit"]' )
        .css( 'font-family', 'inherit' );
        // live change footer text font-family.
        $('#customize-preview iframe')
        .contents()
        .find( '.footer-wrapper' )
        .css( 'font-family',  'inherit' );
      } else {

        $.ajax( {
          url     : loginpressLicense.ajaxurl,
          type    : 'post',
          data    : {
            'fontName' : loginPressVal,
            'action':'loginpress_pro_google_fonts',
          },
          success : function( response ) {
            console.log(response);
            if ( $( "#customize-preview iframe" )
            .contents()
            .find( "head" )
            .find( "#loginpress-custom-font" )
            .length === 0 ) {
              // console.log(response);
              $( "#customize-preview iframe" )
              .contents()
              .find("head")
              .append( "<link href='" + response + "' rel='stylesheet' id='loginpress-custom-font'>" );
            } else {
              $( "#customize-preview iframe" )
              .contents()
              .find( "head" )
              .find( "#loginpress-custom-font" )
              .attr( "href", response );
            }
          }, // !success.
          error : function( xhr, textStatus, errorThrown ) {
            console.log('Ajax Not Working');
          }
        } );  // ! $.ajax().

        // live change #login font-family.
        $('#customize-preview iframe')
        .contents()
        .find( '#login' )
        .css( 'font-family',  loginPressVal );
        // live change form and input fields font-family.
        $('#customize-preview iframe')
        .contents()
        .find( '.login form .input, .login input[type="text"]' )
        .css( 'font-family',  loginPressVal );
        // live change submit button font-family.
        $('#customize-preview iframe')
        .contents()
        .find( '.login input[type="submit"]' )
        .css( 'font-family',  loginPressVal );
        // live change footer text font-family.
        $('#customize-preview iframe')
        .contents()
        .find( '.footer-wrapper' )
        .css( 'font-family',  loginPressVal );
      }     // ! endif;
    } );  // ! value.bind();
  } );  // ! wp.customize();

  } )( jQuery );
