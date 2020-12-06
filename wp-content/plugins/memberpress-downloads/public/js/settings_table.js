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
});
