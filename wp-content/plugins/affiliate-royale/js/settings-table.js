jQuery(document).ready(function($) {
  $('table.esaf-settings-table td.esaf-settings-table-nav ul li').each( function() {
    var page_id = $(this).find('a').data('id');

    $(this).find('a').attr('id', 'esaf-nav-'+page_id);
    $(this).find('a').attr('href', '#'+page_id);
  });

  var esafSetPage = function (hash) {
    // IF ON INITIAL PAGE
    var page = 'table.esaf-settings-table td.esaf-settings-table-pages .esaf-page:first-child';
    var nav = 'table.esaf-settings-table td.esaf-settings-table-nav ul li:first-child a';

    if(!hash) { hash = window.location.hash; }

    var url = window.location.href.replace(/#.*$/,'');

    // Open correct page based on the hash
    var trypage = 'table.esaf-settings-table td.esaf-settings-table-pages .esaf-page' + hash;
    if ((hash != '') && ($(trypage).length > 0)) {
      page = trypage;
      nav = 'table.esaf-settings-table td.esaf-settings-table-nav ul li a#esaf-nav-' + hash.replace(/\#/,'');

      var href = url + hash;
      $( 'table.esaf-settings-table' ).trigger( 'esaf-settings-url', [ href, hash, url ] );

      // Don't do this for now ... it will make the page bump around when using anchors
      //window.location.href = href;
    }

    $('table.esaf-settings-table td.esaf-settings-table-nav ul li a').removeClass('esaf-active');
    $(nav).addClass('esaf-active');

    $('.esaf-page').hide();
    $(page).show();

    // Auto hide the menu in mobile mode when the button is clicked
    if($(window).width() <= 782) {
      $('td.esaf-settings-table-nav').hide();
    }
  };

  esafSetPage();

  $('table.esaf-settings-table').on( 'click', 'td.esaf-settings-table-nav ul li a', function (e) {
    e.preventDefault();
    esafSetPage($(this).attr('href'));
  });

  $('tr.esaf-mobile-nav a.esaf-toggle-nav').on('click', function(e) {
    e.preventDefault();
    $('td.esaf-settings-table-nav').toggle();
  });

  // This is in place so the settings table doesn't get screwed up when resizing
  // up to desktop mode from mobile ... not that that would ever happen of course
  $(window).on('resize', function(e) {
    if($(this).width() > 782) {
      $('td.esaf-settings-table-nav').css('display','');
    }
  });

  var esaf_show_box = function(box,animate) {
    $(box).trigger('esaf_show_box');
    animate ? $(box).slideDown() : $(box).show();
  };

  var esaf_hide_box = function(box,animate) {
    $(box).trigger('esaf_hide_box');
    animate ? $(box).slideUp() : $(box).hide();
  };

  // Toggle Box from Checkbox
  var esaf_toggle_checkbox_box = function(checkbox, box, animate, reverse) {
    if ($(checkbox).is(':checked')) {
      reverse ? esaf_hide_box(box,animate) : esaf_show_box(box,animate);
    }
    else {
      reverse ? esaf_show_box(box,animate) : esaf_hide_box(box,animate);
    }
  };

  // Toggle Box from Link
  var esaf_toggle_link_box = function(link, box, animate) {
    if ($(box).is(':visible')) {
      esaf_hide_box(box,animate);
    }
    else {
      esaf_show_box(box,animate);
    }
  };

  // Toggle Box from Link
  var esaf_toggle_select_box = function(select, boxes, animate) {
    var box = '';

    $.each(boxes, function(k,v) {
      box = '.'+v;
      esaf_hide_box(box,animate);
    });

    if (typeof boxes[$(select).val()] !== undefined) {
      box = '.'+boxes[$(select).val()];
      esaf_show_box(box,animate);
    }
  };

  // Setup all option toggle boxes
  var esaf_toggle_boxes = function() {
    $('.esaf-toggle-checkbox').each(function() {
      var box = '.'+$(this).data('box');
      var reverse  = (typeof $(this).data('reverse') !== 'undefined');

      esaf_toggle_checkbox_box(this, box, false, reverse);

      $(this).on('click', function() {
        esaf_toggle_checkbox_box(this, box, true, reverse);
      });
    });

    $('.esaf-toggle-link').each(function() {
      var box = '.'+$(this).data('box');
      var reverse = (typeof $(this).data('reverse') !== 'undefined');

      reverse ? esaf_show_box(box, false) : esaf_hide_box(box, false);

      $(this).on('click', function(e) {
        e.preventDefault();
        esaf_toggle_link_box(this, box, true);
      });
    });

    $('.esaf-toggle-select').each(function() {
      var boxes = {};
      var select = this;

      $(this).find('option').each(function() {
        var boxname = $(this).val()+'-box';
        if (typeof $(select).data(boxname) !== 'undefined') {
          boxes[$(this).val()] = $(select).data(boxname);
        }
      });

      esaf_toggle_select_box(this, boxes, false);

      $(this).on('change', function(e) {
        esaf_toggle_select_box(this, boxes, true);
      });
    });
  };

  esaf_toggle_boxes();

  // Adjust the action url so we can stay on the same settings page on update
  $('table.esaf-settings-table').on('esaf-settings-url', function( e, href, hash, url ) {
    $('form#esaf-options').attr('action',href);
  });
});

