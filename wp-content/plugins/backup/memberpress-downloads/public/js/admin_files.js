(function($) {
  $(document).ready(function() {
    //jQuery form validation
    $.validate({
      validateHiddenInputs: true,
      submitErrorMessageCallback: function($form, errorMessages, config) {
        $('#file-upload-notice', $form).show();
        return false;
      }
    });
    $('a#mpdl-cancel-upload').on('click', function(e) {
      e.preventDefault();
      $('.content#upload-file').hide();
      $('.content#file-details-container').show();
    });
    $('a#mpdl-replace-file').on('click', function(e) {
      e.preventDefault();
      $('.content#file-details-container').hide();
      $('.content#upload-file').show();
    });
    $('a.mpdl-clipboard-link').on('click', function(e) {
      e.preventDefault();
    });
    let clipboard = new ClipboardJS('.mpdl-clipboard-link');
    let copy_text = 'Copy to Clipboard';
    $('.mpdl-clipboard-link').tooltipster({
      theme: 'tooltipster-borderless',
      content: copy_text,
      trigger: 'custom',
      triggerClose: {
        mouseleave: true,
        touchleave: true
      },
      triggerOpen: {
        mouseenter: true,
        touchstart: true
      }
    });
    clipboard.on('success', function(e) {
      let tooltip = $(e.trigger).tooltipster('instance');
      tooltip.content('Copied!')
      .one('after', function(){
        tooltip.content(copy_text);
      });
    })
    .on('error', function(e) {
      let tooltip = $(e.trigger).tooltipster('instance');
      tooltip.content('Oops, Copy Failed!')
      .one('after', function(){
        instance.content(copy_text);
      });
    });

    // Prevent the default document dropZone
    $(document).bind('drop dragover', function(e) {
      e.preventDefault();
    });
    $('#drop-zone').bind('dragover', function(e) {
      let timeout = window.dropZoneTimeout,
          dropZone = $('#drop-zone');
      if(timeout) {
        clearTimeout(timeout);
      } else {
        $(dropZone).addClass('hover');
      }
      window.dropZoneTimeout = setTimeout(function() {
        window.dropZoneTimeout = null;
        $(dropZone).removeClass('hover');
      }, 100);
    });

    $('#mpdl-file-upload').fileupload({
      dataType: 'json',
      formData: {
        action: 'mpdl_file_upload',
        post_id: MpdlFile.post_id,
        file_nonce: MpdlFile.nonce,
      },
      dropZone: $('.drop-zone'),
      add: function(e, data) {
        $('p', '#upload-progress #uploading-filename').html(data.files[0].name);
        data.submit();
      },
      start: function(e) {
        $('#file-upload-notice').hide();
        $('.content#upload-file').hide();
        $('.content#upload-progress').show();
      },
      progressall: function (e, data) {
        let progress = parseInt(data.loaded / data.total * 100, 10);
        $('#upload-progress #progress .bar').css('width', progress + '%');
      },
      done: function(e, data) {
        $('.content#upload-progress').hide();
        $('.content#file-details-container').show();
        $('#file-details #file-name').html(data.files[0].name);
        $('#file-details #file-size').html(`(${data.jqXHR.responseJSON.data.hsize})`);
        if(data.jqXHR.responseJSON.data.type.match(/image\/[a-zA-Z]+/)) {
          $('#file-details #file-thumb').html(`<img src="${data.jqXHR.responseJSON.data.thumb}">`);
        }
        else {
          $('#file-details #file-thumb').html(`<i class="${data.jqXHR.responseJSON.data.thumb} mpdl-icon large">`);
        }
        $('#mpdl-file-name').val(data.jqXHR.responseJSON.data.filename);
        $('#mpdl-file-size').val(data.jqXHR.responseJSON.data.size);
        $('#mpdl-file-type').val(data.jqXHR.responseJSON.data.type);
      },
      fail: function(e, data) {
        let $response = 'Unknown Error';
        if(typeof data.jqXHR.responseJSON !== 'undefined') {
          $response = data.jqXHR.responseJSON.data.message;
        }
        else {
          switch(data.jqXHR.status) {
            case 413:
              $response = data.files[0].name + " exceeds the maximum upload size for this site.";
              break;
          }
        }
        $('p', '#file-upload-notice').html($response);
        $('.content#upload-progress').hide();
        $('.content#upload-file').show();
        $('#file-upload-notice').show();
      }
    });
  });
})(jQuery);
