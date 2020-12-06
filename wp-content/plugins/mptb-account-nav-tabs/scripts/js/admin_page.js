(function($) {
  $(document).ready(function() {
    function mant_get_blank_form() {
      var data = {
        action: 'mant_get_blank_form'
      };

      $.post(ajaxurl, data, function(response) {
        $(response).hide().appendTo('div.mant-tabs').slideDown({
          complete: function() {
            /* CAN'T SEEM TO GET THIS WORKING
            var editorid = 'navtabcontent' + $('body .mant-tab').last().attr('dataid');

            // remove existing editor instance
            tinymce.execCommand('mceRemoveEditor', true, editorid);

            // init editor for newly appended div
            var init = tinymce.extend({}, tinyMCEPreInit.mceInit[editorid]);
            try { tinymce.init(init); } catch(e) {}
            */

            $('.mant-new-tab-spinner').hide();
            $('.mant-new-tab').fadeIn();
          }
        });
      });
    }

    // Toggle tab types
    $('body').on('click', '.mant-tab-radio', function() {
      var id = $(this).attr('dataid');
      var type = $(this).attr('datatype');
      
      if(type == 'content') {
        $('#mant-tab-hidden-url-' + id).hide();
        $('#mant-tab-hidden-content-' + id).slideDown();
      } else {
        $('#mant-tab-hidden-content-' + id).hide();
        $('#mant-tab-hidden-url-' + id).slideDown();
      }
    });

    // Add new tab
    $('.mant-new-tab').on('click', function() {
      $(this).hide();
      $('.mant-new-tab-spinner').fadeIn();
      mant_get_blank_form();
    });

    // Remove tab
    $('body').on('click', '.mant-tab-remove', function() {
      var id = $(this).attr('dataid');

      if( confirm("Are you sure you want to remove this tab? It cannot be recovered once you save this page.") ) {
        $(".mant-tab-" + id).fadeOut("fast", function() {
          $(this).remove();
        });
      }
    });
  });
})(jQuery);
