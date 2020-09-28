(function ($) {
  
  $(document).ready(function () {

    $('#adminmenu a').on('click', function (e) {

      var url = $(this).attr('href');

      if (url.indexOf('__target_blank=1') !== -1) {

        e.preventDefault();

        window.open(url.replace('?__target_blank=1', '').replace('&__target_blank=1', ''), '_blank');

      } // end if; 

    });

  });

})(jQuery);