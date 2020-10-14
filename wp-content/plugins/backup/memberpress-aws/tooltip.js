jQuery(document).ready(function($) {
  $('body').on('mouseover', '.mpaws-tooltip', function() {
    var tooltip_title = $(this).find('.mpaws-data-title').html();
    var tooltip_info = $(this).find('.mpaws-data-info').html();
    $(this).pointer({ 'content':  '<h3>' + tooltip_title + '</h3><p>' + tooltip_info + '</p>',
                      'position': {'edge':'left','align':'center'},
                      'buttons': function() {
                        // intentionally left blank to eliminate 'dismiss' button
                      }
                    })
    .pointer('open');
  });

  $('body').on('mouseout', '.mpaws-tooltip', function() {
    $(this).pointer('close');
  });

  // if( MeprTooltip.show_about_notice ) {
  //   var mpaws_about_pointer_id = 'mpaws-about-info';

  //   var mpaws_setup_about_pointer = function() {
  //     $('#'+mpaws_about_pointer_id).pointer({
  //       content: MeprTooltip.about_notice,
  //       position: {'edge':'bottom','align':'left'},
  //       close: function() {
  //         var args = { action: 'mpaws_close_about_notice' };
  //         $.post( ajaxurl, args );
  //       }
  //     }).pointer('open');
  //   };

  //   $('.toplevel_page_memberpress .wp-menu-name').attr( 'id', mpaws_about_pointer_id );
  //   mpaws_setup_about_pointer();
  // }
});
