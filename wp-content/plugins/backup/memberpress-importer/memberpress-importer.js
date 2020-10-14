(function($) {
  $(document).ready(function() {
    if( typeof MpimpResults != 'undefined' ) {
      var mpimp_complete = function(pstatus) {
        if( pstatus == 'complete' ) {
          $('.mpimp-loading-gif').hide();
          $('.mpimp-return-link').show();
          $('.mpimp-processing-complete').show();
        }
      }

      mpimp_complete( MpimpResults['status'] );

      var mpimp_set_results_data = function(obj) {
        // $('.mpimp-results').attr('data-status',obj['status']);
        // $('.mpimp-results').attr('data-row',obj.row);

        MpimpResults['status'] = obj['status']; // csv parse status
        MpimpResults['row']    = obj['row']; // row we're on
        MpimpResults['offset'] = obj['offset']; // file pointer offset

        var successful = $('.mepr-total-successful').text();
        var failed = $('.mepr-total-failed').text();
        var total = $('.mepr-total-processed').text();

        $('.mepr-total-successful').text( parseInt(successful,10) + parseInt(obj.successful,10) );
        $('.mepr-total-failed').text( parseInt(failed,10) + parseInt(obj.failed,10) );
        $('.mepr-total-processed').text( parseInt(total,10) + parseInt(obj.total,10) );

        if( obj.messages.length > 0 ) {
          $('.mpimp-messages').text( $('.mpimp-messages').text() + obj.messages.join("\n") + "\n" );
          $('.mpimp-messages').scrollTop( $('.mpimp-messages')[0].scrollHeight );
        }

        if( obj.errors.length > 0 ) {
          $('.mpimp-errors').text( $('.mpimp-errors').text() + obj.errors.join("\n") + "\n" );
          $('.mpimp-errors').scrollTop( $('.mpimp-errors')[0].scrollHeight );
        }

        if( obj.failed_rows.length > 0 ) {
          $('.mpimp-error-rows').text( $('.mpimp-error-rows').text() + obj.failed_rows.join('') );
          $('.mpimp-error-rows').scrollTop( $('.mpimp-error-rows')[0].scrollHeight );
        }
      }

      var mpimp_process_csv = function() {
        $.ajax({
          type: "GET",
          url: ajaxurl,
          data: MpimpResults,
          dataType: "json",
          timeout: 60000 // Must be slightly more than the server-side timeout
        })
        .done( function(obj) {
          // if(!obj.errors || (obj.errors.length <= 0)) {
            mpimp_set_results_data( obj );
            if(obj['status']=='complete') {
              mpimp_complete( obj['status'] );
            }
            else {
              mpimp_process_csv(); // Recursive bro
            }
          // }
        })
        .fail( function(xhr, stat, err) {
          // Cleanup that csv file even if there's an error
          MpimpResults.action = "delete_csv";
          $.post(ajaxurl, MpimpResults);
          alert("A critical error occurred, try again later");
        });
      }

      mpimp_process_csv();
    }

    var mpimp_file_type = $("#mpimp-import-file-type").val();
    $('#mpimp-'+mpimp_file_type+'-form').show();


    $("#mpimp-import-file-type").on('change', function (e) {
      mpimp_file_type = $("#mpimp-import-file-type").val();
      $('.mpimp-importer-form').hide();
      $('#mpimp-'+mpimp_file_type+'-form').show();
    });
  });
})(jQuery);
