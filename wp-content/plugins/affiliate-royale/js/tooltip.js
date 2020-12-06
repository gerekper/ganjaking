jQuery(document).ready(function($) {
  $('body').on('click', '.esaf-tooltip', function() {
    var tooltip_title = $(this).find('.esaf-data-title').html();
    var tooltip_info = $(this).find('.esaf-data-info').html();
    $(this).pointer({
      'content': '<h3>' + tooltip_title + '</h3><p>' + tooltip_info + '</p>',
      'position': {'edge':'left','align':'center'},
      //'buttons': function() {
      //  // intentionally left blank to eliminate 'dismiss' button
      //}
    })
    .pointer('open');
  });
});

