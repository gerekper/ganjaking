(function($) {
  $(document).ready(function() {

    $(".datepicker").datepicker({ dateFormat: 'yy-mm-dd' });

    $('.mpdl_suggest_files').suggest(
      ajaxurl+'?action=mpdl_file_search', {
        delay: 500,
        minchars: 2
      }
    );

  });
})(jQuery);
