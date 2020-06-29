(function($) {

  /* globals jQuery, wp */

  "use strict";

  function mfnFieldUpload() {

    $('body').on('click', '.mfn-opts-upload', function(e) {

      e.preventDefault();

      var activeFileUploadContext = $(this).parent();
      var type = $('input', activeFileUploadContext).attr('class');

      // Create the media frame

      var customFileFrame = wp.media.frames.customHeader = wp.media({
        title: $(this).data('choose'),
        library: {
          type: type
        },
        button: {
          text: $(this).data('update')
        }
      });

      customFileFrame.on('select', function() {

        var attachment = customFileFrame.state().get("selection").first();

        // Update value of the targetfield input with the attachment url

        $('.mfn-opts-screenshot', activeFileUploadContext).attr('src', attachment.attributes.url);
        $('input', activeFileUploadContext)
          .val(attachment.attributes.url)
          .trigger('change');

        $('.mfn-opts-upload', activeFileUploadContext).hide();
        $('.mfn-opts-screenshot', activeFileUploadContext).show();
        $('.mfn-opts-upload-remove', activeFileUploadContext).show();
      });

      customFileFrame.open();
    });

		$('body').on('click', '.mfn-opts-upload-remove', function(e) {

      e.preventDefault();

      var activeFileUploadContext = $(this).parent();

      $('input', activeFileUploadContext).val('');
      $(this).prev().fadeIn('slow');
      $('.mfn-opts-screenshot', activeFileUploadContext).fadeOut('slow');
      $(this).fadeOut('slow');
    });

  }

  /**
   * $(document).ready
   * Specify a function to execute when the DOM is fully loaded.
   */

  $(function() {
    mfnFieldUpload();
  });

})(jQuery);
