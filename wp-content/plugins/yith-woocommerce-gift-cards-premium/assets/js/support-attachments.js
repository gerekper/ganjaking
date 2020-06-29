/*jslint unparam: true */
/*global window, $ */
/**
 * @see: http://docs.fineuploader.com/
 */
(function ($) {
    'use strict';


  $(document).on('yith_ywgc_popup_template_loaded', function ( popup, item ) {

    var upload_wrapper = $('#' + yith_uploader.wrapper_id),
      form_field = '<input type="hidden" id="attachment_%file_id%" name="attachments[]"  data-file_id="%file_id%" data-file-name="%uploaded-file-name%" data-file-uuid="%uploaded-file-uuid%"/>',
      uploader = new qq.FineUploader(
        {
          debug     : yith_uploader.debug,
          element   : document.getElementById( yith_uploader.wrapper_id ),
          request   : yith_uploader.request,
          text      : yith_uploader.text,

          callbacks: {
            onComplete : function (id, name, responseJSON, xhr) {
              if( responseJSON.success == true ){

                var new_file_field = form_field
                  .replace( /%file_id%/g, id )
                  .replace( /%uploaded-file-name%/g, responseJSON.uploadName )
                  .replace( /%uploaded-file-uuid%/g, responseJSON.uuid );
                upload_wrapper.prepend( new_file_field );
              }

              if( responseJSON.success == false ){
                var not_uploaded_file = $( '.qq-upload-list').find( 'li.qq-file-id-' + id + ' .qq-upload-status-text'),
                  error_text = '';

                if( responseJSON.error == 'File is too large.' ){
                  error_text = yith_uploader.customMessages.maxFileSize;
                }

                else {
                  error_text = responseJSON.error;
                }

                not_uploaded_file.text( error_text );
              }
            },


          }

        });


  });


}(jQuery));
