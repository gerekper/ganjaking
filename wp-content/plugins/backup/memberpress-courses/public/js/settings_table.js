jQuery(document).ready(function($) {
  $('table.settings-table td.settings-table-nav ul li').each( function() {
    var page_id = $(this).find('a').data('id');

    $(this).find('a').attr('id', 'nav-'+page_id);
    $(this).find('a').attr('href', '#'+page_id);
  });

  var SetPage = function (hash) {
    // IF ON INITIAL PAGE
    var page = 'table.settings-table td.settings-table-pages .page:first-child';
    var nav = 'table.settings-table td.settings-table-nav ul li:first-child a';

    if(!hash) { hash = window.location.hash; }

    var url = window.location.href.replace(/#.*$/,'');

    // Open correct page based on the hash
    var trypage = 'table.settings-table td.settings-table-pages .page' + hash;
    if ((hash != '') && ($(trypage).length > 0)) {
      page = trypage;
      nav = 'table.settings-table td.settings-table-nav ul li a#nav-' + hash.replace(/\#/,'');

      var href = url + hash;
      $( 'table.settings-table' ).trigger( 'settings-url', [ href, hash, url ] );

      // Don't do this for now ... it will make the page bump around when using anchors
      //window.location.href = href;
    }

    $('table.settings-table td.settings-table-nav ul li a').removeClass('active');
    $(nav).addClass('active');

    $('.page').hide();
    $(page).show();

    // Auto hide the menu in mobile mode when the button is clicked
    if($(window).width() <= 782) {
      $('td.settings-table-nav').hide();
    }
  };

  SetPage();

  $('table.settings-table').on( 'click', 'td.settings-table-nav ul li a', function (e) {
    e.preventDefault();
    SetPage($(this).attr('href'));
  });

  $('tr.mobile-nav a.toggle-nav').on('click', function(e) {
    e.preventDefault();
    $('td.settings-table-nav').toggle();
  });

  // This is in place so the settings table doesn't get screwed up when resizing
  // up to desktop mode from mobile ... not that that would ever happen of course
  $(window).on('resize', function(e) {
    if($(this).width() > 782) {
      $('td.settings-table-nav').css('display','');
    }
  });

  var show_box = function(box,animate) {
    $(box).trigger('show_box');
    animate ? $(box).slideDown() : $(box).show();
  };

  var hide_box = function(box,animate) {
    $(box).trigger('hide_box');
    animate ? $(box).slideUp() : $(box).hide();
  };

  // Toggle Box from Checkbox
  var toggle_checkbox_box = function(checkbox, box, animate, reverse) {
    if ($(checkbox).is(':checked')) {
      reverse ? hide_box(box,animate) : show_box(box,animate);
    }
    else {
      reverse ? show_box(box,animate) : hide_box(box,animate);
    }
  };

  // Toggle Box from Link
  var toggle_link_box = function(link, box, animate) {
    if ($(box).is(':visible')) {
      hide_box(box,animate);
    }
    else {
      show_box(box,animate);
    }
  };

  // Toggle Box from Link
  var toggle_select_box = function(select, boxes, animate) {
    var box = '';

    $.each(boxes, function(k,v) {
      box = '.'+v;
      hide_box(box,animate);
    });

    if (typeof boxes[$(select).val()] !== undefined) {
      box = '.'+boxes[$(select).val()];
      show_box(box,animate);
    }
  };

  // Toggle Box from Checkbox
  var toggle_logo_preview = function() {
    $(".mpcs-logo-actions").show();
    $("#mpcs-options-logo-preview").show();
    $("#plupload-upload-ui").hide();
  };

  // Setup all option toggle boxes
  var toggle_boxes = function() {
    $('.toggle-checkbox').each(function() {
      var box = '.'+$(this).data('box');
      var reverse  = (typeof $(this).data('reverse') !== 'undefined');

      toggle_checkbox_box(this, box, false, reverse);

      $(this).on('click', function() {
        toggle_checkbox_box(this, box, true, reverse);
      });
    });

    $('.toggle-link').each(function() {
      var box = '.'+$(this).data('box');
      var reverse = (typeof $(this).data('reverse') !== 'undefined');

      reverse ? show_box(box, false) : hide_box(box, false);

      $(this).on('click', function(e) {
        e.preventDefault();
        toggle_link_box(this, box, true);
      });
    });

    $('.toggle-select').each(function() {
      var boxes = {};
      var select = this;

      $(this).find('option').each(function() {
        var boxname = $(this).val()+'-box';
        if (typeof $(select).data(boxname) !== 'undefined') {
          boxes[$(this).val()] = $(select).data(boxname);
        }
      });

      toggle_select_box(this, boxes, false);

      $(this).on('change', function(e) {
        toggle_select_box(this, boxes, true);
      });
    });
  };

  toggle_boxes();

  // Adjust the action url so we can stay on the same settings page on update
  $('table.settings-table').on('settings-url', function( e, href, hash, url ) {
    $('form#options').attr('action',href);
  });

  // For color picker
  $('.mpcs-color-field').wpColorPicker();


  // create the logo uploader and pass the config from above
  var uploader = new plupload.Uploader(MPCS_Settings);

  // checks if browser supports drag and drop upload, makes some css adjustments if necessary
  uploader.bind('Init', function(up){
    var uploaddiv = $('#plupload-upload-ui');

    if(up.features.dragdrop){
      uploaddiv.addClass('drag-drop');
        $('#drag-drop-area')
          .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
          .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

    }else{
      uploaddiv.removeClass('drag-drop');
      $('#drag-drop-area').unbind('.wp-uploader');
    }
  });

  uploader.init();

  // a file was added in the queue
  uploader.bind('FilesAdded', function(up, files){
    var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);

    plupload.each(files, function(file){
      if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5'){
        // file size error?
        console.log('Error uploading file')
      }
    });

    up.refresh();
    up.start();
  });

  // a file was uploaded
  uploader.bind('FileUploaded', function(up, file, response) {
    let r = $.parseJSON(response.response);
    $("#mpcs-options-classroom-logo").val(r.id);
    $("#mpcs-options-logo-preview img").attr("src", r.url);
    toggle_logo_preview();
  });


  if($("#mpcs-options-logo-preview img").attr("src").length > 0 ){
    toggle_logo_preview();
  }

  $("#mpcs-options-logo-replace").on('click', function() {
    $("#plupload-upload-ui").slideToggle();
  });

  $("#mpcs-options-logo-remove").on('click', function() {
    $('#mpcs-options-logo-preview').hide();
    $('#mpcs-options-classroom-logo').val('');
  });

  var requiresClassroomMode = $('.requires-classroom-mode');
  $('#mpcs_options_classroom_mode').change(function(event) {
    toggleClassroomModeRequiredFields( $(this) );
  });


  var toggleClassroomModeRequiredFields = function (classroomCheckbox) {
    if($(classroomCheckbox).is(':checked')) {
      $.each(requiresClassroomMode, function(index, el) {
        $(el).removeClass('hidden');
      });
    } else {
      $.each(requiresClassroomMode, function(index, el) {
        $(el).addClass('hidden');
      });
    }
  };

  toggleClassroomModeRequiredFields( $('#mpcs_options_classroom_mode') );

});
