jQuery(document).ready(function($){

  var file_frame;

  $('#woocommerce_pdf_invoice_settings_logo_file_button').click(function(e) {

    e.preventDefault();
    
    // If the media frame already exists, reopen it.
    if (file_frame) {

      file_frame.open();
      return;

    }

    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({

      title: jQuery( this ).data( 'uploader_title' ),
      button: {
        text: jQuery( this ).data( 'uploader_button_text' ),
      },
      multiple: false  // Set to true to allow multiple files to be selected
      
    });

    // When a file is selected, grab the URL and set it as the text field's value
    file_frame.on('select', function() {

      attachment = file_frame.state().get('selection').first().toJSON();
      $('#woocommerce_pdf_invoice_settings_logo_file').val(attachment.url);

    });

    // Finally, open the modal
    file_frame.open();
  });

});