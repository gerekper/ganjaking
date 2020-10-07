(function( $ ) {
  'use strict';

  var tables = $( '#wp-tables' );

  tables.bsmSelect({
    animate: true,
    title: dbReset.selectTable,
    plugins: [$.bsmSelect.plugins.compatibility()]
  });

  $( '#select-all' ).on('click', function(e) {
    e.preventDefault();

    tables.children()
      .attr( 'selected', 'selected' )
      .end()
      .change();
  });

  tables.on( 'change', function() {
    $( '#reactivate' ).showIfSelected( 'options' );
    $( '#disclaimer' ).showIfSelected( 'users' );
  });

  $( '#db-reset-code-confirm' ).on( 'change keyup paste', function() {
    $( '#db-reset-submit' ).prop( 'disabled', $( this ).val() !== $( "#security-code" ).text() );
  });

  $( '#db-reset-submit' ).on( 'click', function(e) {
    e.preventDefault();

    if ( !$( '#wp-tables' ).val() ) {
      alert( dbReset.selectOneTable );
      return false;
    }

    if ( confirm( dbReset.confirmAlert ) ) {
      $( '#db-reset-form' ).submit();
      $( '#loader' ).show();
      $( '#db-reset-form' ).css( 'pointer-events', 'none' );
    }
  });

  if ( !localStorage.getItem( 'rate-plugin-notice-dismiss' ) ) {
    $( '#rate-plugin-notice' ).show();
  }

  $( '.tools_page_database-reset' ).on( 'click', '#rate-plugin-notice .notice-dismiss', function( e ) {
    localStorage.setItem( 'rate-plugin-notice-dismiss', true );

    return false;
  });


  $( '.tools_page_database-reset' ).on( 'click', '.open-wpr-upsell', function( e ) {
    e.preventDefault();
    $( this ).blur();

    $( '#wpr-upsell-dialog' ).dialog( 'open' );

    return false;
  });


  // upsell dialog init
  $( '#wpr-upsell-dialog' ).dialog( { 'dialogClass': 'wp-dialog wpr-upsell-dialog',
                              'modal': 1,
                              'resizable': false,
                              'title': 'Develop & Debug in WordPress Faster',
                              'zIndex': 9999,
                              'width': 550,
                              'height': 'auto',
                              'show': 'fade',
                              'hide': 'fade',
                              'open': function( event, ui ) {
                                wpr_fix_dialog_close( event, ui );
                                $( this ).siblings().find( 'span.ui-dialog-title' ).html( dbReset.wprDialogTitle );
                              },
                              'close': function( event, ui ) { },
                              'autoOpen': false,
                              'closeOnEscape': true
  });


  $( window ).resize( function(e) {
    $( '#wpr-upsell-dialog' ).dialog( 'option', 'position', { my: 'center', at: 'center', of: window } );
  });


  $( '.install-wpr' ).on( 'click', function(e) {
    $( '#wpr-upsell-dialog' ).dialog( 'close' );
    $( 'body' ).append( '<div style="width:550px;height:450px; position:fixed;top:10%;left:50%;margin-left:-275px; color:#444; background-color: #fbfbfb;border:1px solid #DDD; border-radius:4px;box-shadow: 0px 0px 0px 4000px rgba(0, 0, 0, 0.85);z-index: 9999999;"><iframe src="' + dbReset.wprInstallUrl + '" style="width:100%;height:100%;border:none;" /></div>' );
    $( '#wpwrap' ).css( 'pointer-events', 'none' );
    
    e.preventDefault();
    return false;
  });


  $.fn.showIfSelected = function( selectValue ) {
    $( this ).toggle( $( "option[value='" + selectValue + "']:selected", tables ).length > 0 );
  }

})( jQuery );

function wpr_fix_dialog_close(event, ui) {
  jQuery( '.ui-widget-overlay' ).bind( 'click', function() {
    jQuery( '#' + event.target.id ).dialog( 'close' );
  });
} // wpr_fix_dialog_close
