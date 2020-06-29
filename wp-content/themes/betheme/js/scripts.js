(function($) {

  /* globals jQuery, mfn */

  "use strict";

  var scrollTicker, lightboxAttr, sidebar,
    rtl = $('body').hasClass('rtl'),
    simple = $('body').hasClass('style-simple'),
    topBarTop = '61px',
    headerH = 0,
    mobileInitW = (mfn.mobileInit) ? mfn.mobileInit : 1240;

  /**
   * Lightbox | Magnific Popup
   */

  if (!mfn.lightbox.disable) {
    if (!(mfn.lightbox.disableMobile && (window.innerWidth < 768))) {
      lightboxAttr = {
        title: mfn.lightbox.title ? mfn.lightbox.title : false,
      };
    }
  }

  /**
   * Admin Bar & WooCommerce Store Notice
   */

  function adminBarH() {
    var height = 0;

    // WP adminbar
    if ($('body').hasClass('admin-bar')) {
      height += $('#wpadminbar').innerHeight();
    }

    // WC demo store
    if ($('body').hasClass('woocommerce-demo-store')) {
      height += $('body > p.demo_store').innerHeight();
    }

    return height;
  }

  /**
   * Header | Sticky
   */

  function mfnSticky() {

    if( ! $('body').hasClass('sticky-header') ){
      return false;
    }

    if( $('body').hasClass('header-creative') && window.innerWidth >= 768 ){
      return false;
    }

    var startY = headerH;
    var windowY = $(window).scrollTop();

    if (windowY > startY) {

      if (!($('#Top_bar').hasClass('is-sticky'))) {

        $('.header_placeholder').css('height', $('#Top_bar').height());

        $('#Top_bar')
          .addClass('is-sticky')
          .css('top', -60)
          .animate({
            'top': adminBarH() + 'px'
          }, 300);

        // Header width
        mfnHeader();
      }

    } else {

      if ($('#Top_bar').hasClass('is-sticky')) {

        $('.header_placeholder').css('height', 0);
        $('#Top_bar')
          .removeClass('is-sticky')
          .css('top', topBarTop);

        // Retina Logo - max height
        stickyLogo();

        // Header width
        mfnHeader();

      }

    }

  }

  /**
   * Header | Sticky | Retina Logo - max height
   */

  function stickyLogo() {

    // ! retina display
    if( window.devicePixelRatio <= 1 ){
      return false;
    }

    var parent = $('#Top_bar #logo'),
      el = $('img.logo-main', parent),
      height = el.data('height');

    // ! retina logo set
    if ( ! parent.hasClass('retina')) {
      return false;
    }

    if ($('body').hasClass('logo-overflow')) {
      // do nothing
    } else if (height > parent.data('height')) {
      height = parent.data('height');
    }

    el.css('max-height', height + 'px');

  }

  /**
   * Header | Sticky | Height
   */

  function mfnStickyH() {

    if ($('body').hasClass('header-below')) {

      // header below slider
      headerH = $('.mfn-main-slider').innerHeight() + $('#Top_bar').innerHeight();

    } else {

      // default
      headerH = $('#Top_bar').innerHeight() + $('#Action_bar').innerHeight();

    }

  }

  /**
   * Header | Sticky | Mobile
   */

  function mfnMobileSticky() {

    if( ! $('body').hasClass('.mobile-sticky') ){
      return false;
    }

    if( $(window).width() >= 768 ){
      return false;
    }

    var menuH,
      windowH = $(window).height(),
      offset = adminBarH() + $('#Top_bar .logo').height();

    if ((!$('#Top_bar').hasClass('is-sticky')) && $('#Action_bar').is(':visible')) {
      offset += $('#Action_bar').height();
    }

    menuH = windowH - offset;

    if (menuH < 176) {
      menuH = 176;
    }

    $('#Top_bar #menu').css('max-height', menuH + 'px');

  }

  /**
   * Header | Top bar left | Width
   */

  function mfnHeader() {
    var rightW = $('.top_bar_right').innerWidth();

    if (rightW && !$('body').hasClass('header-plain')) {
      rightW += 10;
    }

    var parentW = $('#Top_bar .one').innerWidth();
    var leftW = parentW - rightW;

    $('.top_bar_left, .menu > li > ul.mfn-megamenu').css('width', leftW);
  }

  /**
   * FIX | Header | Sticky | Height
   */

  function fixStickyHeaderH() {

    var stickyH = 0;

    // FIX | sticky top bar height

    var topBar = $('.sticky-header #Top_bar');

    if (topBar.hasClass('is-sticky')) {
      stickyH = $('.sticky-header #Top_bar').innerHeight();
    } else {
      topBar.addClass('is-sticky');
      stickyH = $('.sticky-header #Top_bar').innerHeight();
      topBar.removeClass('is-sticky');
    }

    // FIX | responsive

    if ($(window).width() < mobileInitW) {

      if ($(window).width() < 768) {

        // mobile
        if (!$('body').hasClass('mobile-sticky')) {
          stickyH = 0;
        }

      } else {

        // tablet
        if (!$('body').hasClass('tablet-sticky')) {
          stickyH = 0;
        }

      }

    } else {

      // desktop

      // FIX | header creative
      if ($('body').hasClass('header-creative')) {
        stickyH = 0;
      }

    }

    return stickyH;
  }

  /**
   * Sidebar | Sticky
   */

  function mfnSidebarSticky(){

    var spacing = 0;

    if( ! mfn.sidebarSticky ){
      return false;
    }

    if( ($('body').hasClass('sticky-header')) && ($(window).width() >= mobileInitW) ){
      spacing = 60;
    }

    sidebar = $('.mcb-sidebar .widget-area').stickySidebar({
      topSpacing: spacing
    });
  }

  mfnSidebarSticky();

  /**
   * Sidebar | Height
   */

  function mfnSidebar() {
    if ($('.mcb-sidebar').length) {

      var maxH = $('#Content .sections_group').outerHeight();

      $('.mcb-sidebar').each(function() {
        $(this).css('min-height', 0);
        if ($(this).height() > maxH) {
          maxH = $(this).height();
        }
      });

      $('.mcb-sidebar').css('min-height', maxH + 'px');

      if( sidebar ){
        sidebar.stickySidebar('updateSticky');
      }

    }
  }

  /**
   * Section | Full Screen
   */

  function mfnSectionH() {

    var windowH = $(window).height();

    // FIX | next/prev section
    var offset = 0;
    if ($('.section.full-screen:not(.hide-desktop)').length > 1) {
      offset = 5;
    }

    $('.section.full-screen').each(function() {

      var section = $(this);
      var wrapper = $('.section_wrapper', section);

      section
        .css('padding', 0)
        .css('min-height', windowH + offset);

      var padding = (windowH + offset - wrapper.height()) / 2;

      if (padding < 50) padding = 50;

      wrapper
        .css('padding-top', padding + 10) // 20 = column margin-bottom / 2
        .css('padding-bottom', padding - 10);
    });
  }

  /**
   * Equal Height | Wraps
   */

  function mfnEqualHeightWrap() {
    $('.section.equal-height-wrap .section_wrapper').each(function() {
      var maxH = 0;
      $('> .wrap', $(this)).each(function() {
        $(this).css('height', 'auto');
        if ($(this).innerHeight() > maxH) {
          maxH = $(this).innerHeight();
        }
      });
      $('> .wrap', $(this)).css('height', maxH + 'px');
    });
  }

  /**
   * Equal Height | Items
   */

  function mfnEqualHeight() {
    $('.section.equal-height .mcb-wrap-inner').each(function() {
      var maxH = 0;

      $('> .column', $(this)).each(function() {
        $(this).css('height', 'auto');
        if ($(this).height() > maxH) {
          maxH = $(this).height();
        }
      });
      $('> .column', $(this)).css('height', maxH + 'px');
    });
  }


  /**
   * Into | Full Screen
   */

  function mfnIntroH() {
    var windowH = $(window).height() - $('#Header_wrapper').height() - adminBarH();

    $('#Intro.full-screen').each(function() {

      var el = $(this);
      var inner = $('.intro-inner', el);

      el.css('padding', 0).css('min-height', windowH);

      var padding = (windowH - inner.height()) / 2;
      inner.css('padding-top', padding).css('padding-bottom', padding);

    });
  }

  /**
   * Footer | Sliding | Height
   */

  function mfnFooter() {

    // Fixed, Sliding
    if ($('.footer-fixed #Footer, .footer-sliding #Footer').length) {

      var footerH = $('#Footer').height() - 1;
      $('#Content').css('margin-bottom', footerH + 'px');

    }

    // Stick to bottom
    if ($('.footer-stick #Footer').length) {

      var headerFooterH = $('#Header_wrapper').height() + $('#Footer').height();
      var documentH = $(document).height() - adminBarH();

      if ((documentH <= $(window).height()) && (headerFooterH <= $(window).height())) {
        $('#Footer').addClass('is-sticky');
      } else {
        $('#Footer').removeClass('is-sticky');
      }

    }

  }

  /**
   * Back To Top | Sticky / Show on scroll
   */

  function backToTopSticky() {

    if ($('#back_to_top.sticky.scroll').length) {
      var el = $('#back_to_top.sticky.scroll');

      // Clear Timeout if one is pending
      if (scrollTicker) {
        window.clearTimeout(scrollTicker);
        scrollTicker = null;
      }

      el.addClass('focus');

      // Set Timeout
      scrollTicker = window.setTimeout(function() {
        el.removeClass('focus');
      }, 1500); // Timeout in msec

    }

  }

  /**
   * # Hash smooth navigation
   */

  function hashNav() {

    var hash = window.location.hash;

    if (hash) {

      // FIX | Master Slider

      if (hash.indexOf("&") > -1 || hash.indexOf("/") > -1) {
        return false;
      }

      // Contact Form 7 in popup

      if (hash.indexOf("wpcf7") > -1) {
        cf7popup(hash);
      }

      if ($(hash).length) {

        var offset,
          headerH = fixStickyHeaderH();

        // header builder

        if( $('body').hasClass('mhb') ){

          var currentView = $('.mhb-view').filter(':visible');
          headerH = currentView.height();

        }

        offset = headerH + adminBarH() + $(hash).siblings('.ui-tabs-nav').innerHeight();

        $('html, body').animate({
          scrollTop: $(hash).offset().top - offset
        }, 500);
      }

    }

  }

  /**
   * One Page | Scroll Active
   */

  function onePageActive() {
    if ($('body').hasClass('one-page')) {

      var stickyH = $('.sticky-header #Top_bar').innerHeight();
      var windowT = $(window).scrollTop();
      var start = windowT + stickyH + adminBarH() + 1;
      var first = false;

      $('[data-id]:not(.elementor-element), section[data-id]').each(function() {

        if ($(this).visible(true)) {

          if (!first) {
            first = $(this);
          } else if (($(this).offset().top < start) && ($(this).offset().top > first.offset().top)) {
            first = $(this);
          }
        }

        if (first) {

          var newActive = first.attr('data-id');
          var active = '[data-hash="' + newActive + '"]';

          if (newActive) {
            var menu = $('#menu');
            menu.find('li').removeClass('current-menu-item current-menu-parent current-menu-ancestor current_page_item current_page_parent current_page_ancestor');
            $(active, menu)
              .closest('li').addClass('current-menu-item')
              .closest('.menu > li').addClass('current-menu-item');
          }

        }

      });

    }
  }

  /**
   * Contact Form 7 | Popup
   */

  function cf7popup(hash) {
    if (hash && $(hash).length) {
      var id = $(hash).closest('.popup-content').attr('id');

      $('a.popup-link[href="#' + id + '"]:not(.once)')
        .addClass('once')
        .trigger('click');

    }
  }

  /**
   * $(document).ready
   * Specify a function to execute when the DOM is fully loaded.
   */

  $(document).ready(function() {

    /**
     * Top Bar
     */

    $('#Top_bar').removeClass('loading');

    topBarTop = parseInt($('#Top_bar').css('top'), 10);
    if (topBarTop < 0) topBarTop = 61;
    topBarTop = topBarTop + 'px';

    /**
     * Menu | Overlay
     */

    $('.overlay-menu-toggle').on('click',function(e) {
      e.preventDefault();

      $(this).toggleClass('focus');
      $('#Overlay').stop(true, true).fadeToggle(500);

      var menuH = $('#Overlay nav').height() / 2;
      $('#Overlay nav').css('margin-top', '-' + menuH + 'px');
    });

    $('#Overlay').on('click', '.menu-item > a', function() {
      $('.overlay-menu-toggle').trigger('click');
    });

    /**
     * Menu | Responsive | button
     */

    $('.responsive-menu-toggle').on('click', function(e) {
      e.preventDefault();

      var el = $(this);
      var menu = $('#Top_bar #menu');
      var menuWrap = menu.closest('.top_bar_left');

      el.toggleClass('active');

      // sticky menu button
      if (el.hasClass('is-sticky') && el.hasClass('active') && (window.innerWidth < 768)) {

        var top = 0;
        if (menuWrap.length) {
          top = menuWrap.offset().top - adminBarH();
        }
        $('body,html').animate({
          scrollTop: top
        }, 200);

      }

      menu.stop(true, true).slideToggle(200);
    });

    /**
     * Menu | Responsive | Side Slide
     */

    function sideSlide() {

      var slide = $('#Side_slide');
      var overlay = $('#body_overlay');
      var ssMobileInitW = mobileInitW;
      var pos = 'right';

      var shiftSlide = -slide.data('width');
      var shiftBody = shiftSlide / 2;

      // constructor

      var constructor = function() {
        if (!slide.hasClass('enabled')) {
          $('nav#menu').detach().appendTo('#Side_slide .menu_wrapper');
          slide.addClass('enabled');
        }
      };

      // destructor

      var destructor = function() {
        if (slide.hasClass('enabled')) {
          close();
          $('nav#menu').detach().prependTo('#Top_bar .menu_wrapper');
          slide.removeClass('enabled');
        }
      };

      // reload

      var reload = function() {

        if ((window.innerWidth < ssMobileInitW)) {
          constructor();
        } else {
          destructor();
        }
      };

      // init

      var init = function() {

        if (slide.hasClass('left')) {
          pos = 'left';
        }

        // responsive off
        if ($('body').hasClass('responsive-off')) {
          ssMobileInitW = 0;
        }

        // header simple
        if ($('body').hasClass('header-simple')) {
          ssMobileInitW = 9999;
        }

        reload();
      };

      // reset to default

      var reset = function(time) {

        $('.lang-active.active', slide).removeClass('active').children('i').attr('class', 'icon-down-open-mini');
        $('.lang-wrapper', slide).fadeOut(0);

        $('.icon.search.active', slide).removeClass('active');
        $('.search-wrapper', slide).fadeOut(0);

        $('.menu_wrapper, .social', slide).fadeIn(time);

      };

      // menu button

      var button = function() {

        // show

        if (pos == 'left') {
          slide.animate({
            'left': 0
          }, 300);
          $('body').animate({
            'right': shiftBody
          }, 300);
        } else {
          slide.animate({
            'right': 0
          }, 300);
          $('body').animate({
            'left': shiftBody
          }, 300);
        }

        overlay.fadeIn(300);

        // reset

        reset(0);

      };

      // close

      var close = function() {

        if (pos == 'left') {
          slide.animate({
            'left': shiftSlide
          }, 300);
          $('body').animate({
            'right': 0
          }, 300);
        } else {
          slide.animate({
            'right': shiftSlide
          }, 300);
          $('body').animate({
            'left': 0
          }, 300);
        }

        overlay.fadeOut(300);

        // if page contains revolution slider, trigger resize

        if ($('.rev_slider').length) {
          setTimeout(function() {
            $(window).trigger('resize');
          }, 300);
        }

      };

      // search

      $('.icon.search', slide).on('click', function(e) {

        e.preventDefault();

        var el = $(this);

        if (el.hasClass('active')) {

          $('.search-wrapper', slide).fadeOut(0);
          $('.menu_wrapper, .social', slide).fadeIn(300);

        } else {

          $('.search-wrapper', slide).fadeIn(300);
          $('.menu_wrapper, .social', slide).fadeOut(0);

          $('.lang-active.active', slide).removeClass('active').children('i').attr('class', 'icon-down-open-mini');
          $('.lang-wrapper', slide).fadeOut(0);

        }

        el.toggleClass('active');
      });

      // search form submit

      $('a.submit', slide).on('click', function(e) {
        e.preventDefault();
        $('#side-form').submit();
      });

      // lang menu

      $('.lang-active', slide).on('click', function(e) {
        e.preventDefault();

        var el = $(this);

        if (el.hasClass('active')) {

          $('.lang-wrapper', slide).fadeOut(0);
          $('.menu_wrapper, .social', slide).fadeIn(300);
          el.children('i').attr('class', 'icon-down-open-mini');

        } else {

          $('.lang-wrapper', slide).fadeIn(300);
          $('.menu_wrapper, .social', slide).fadeOut(0);
          el.children('i').attr('class', 'icon-up-open-mini');

          $('.icon.search.active', slide).removeClass('active');
          $('.search-wrapper', slide).fadeOut(0);

        }

        el.toggleClass('active');
      });

      // init, click, debouncedresize

      // init

      init();

      // click | menu button

      $('.responsive-menu-toggle').off('click');

      $('.responsive-menu-toggle').on('click', function(e) {
        e.preventDefault();
        button();
      });

      // click | close

      overlay.on('click', function(e) {
        close();
      });

      $('.close', slide).on('click', function(e) {
        e.preventDefault();
        close();
      });

      // click | below search or languages menu

      $(slide).on('click', function(e) {
        if ($(e.target).is(slide)) {
          reset(300);
        }
      });

      // debouncedresize

      $(window).on('debouncedresize', reload);

    }

    if ($('body').hasClass('mobile-side-slide')) {
      sideSlide();
    }

    /**
     * Gallery | WordPress Gallery
     */

    // WordPress <= 4.9 | content

    $('.sections_group .gallery').each(function() {

      var el = $(this);
      var id = el.attr('id');

      $('> br', el).remove();

      $('.gallery-icon > a', el)
        .wrap('<div class="image_frame scale-with-grid"><div class="image_wrapper"></div></div>')
        .prepend('<div class="mask"></div>')
        .children('img')
        .css('height', 'auto')
        .css('width', '100%');

      // lightbox | link to media file

      if (el.hasClass('file')) {
        $('.gallery-icon a', el)
          .attr('rel', 'prettyphoto[' + id + ']')
          .attr('data-elementor-lightbox-slideshow', id); // FIX: elementor lightbox gallery
      }

      // isotope for masonry layout

      if (el.hasClass('masonry')) {

        el.isotope({
          itemSelector: '.gallery-item',
          layoutMode: 'masonry',
          isOriginLeft: rtl ? false : true
        });

      }

    });

    // WordPress >= 5.0 | content

    $('.sections_group .wp-block-gallery').each(function(index) {

      var el = $(this);
      var link = $('.blocks-gallery-item a', el);

      // lightbox | link to media file

      if ((/\.(gif|jpg|jpeg|png)$/i).test(link.attr('href'))) {
        link.attr('rel', 'prettyphoto[wp5-gallery-' + index + ']');
      }

    });

    // widgets

    $('.widget_media_gallery .gallery').each(function() {

      var el = $(this);
      var id = el.attr('id');

      // lightbox | link to media file

      $('.gallery-icon a', el).attr('rel', 'prettyphoto[widget-' + id + ']');

    });

    /**
     * Lightbox | PrettyPhoto
     */

    $('a[rel^="prettyphoto[portfolio]"]').each(function() {

      var el = $(this);
      var parent = el.closest('.column');
      var index = $('.column').index(parent);

      el.attr('rel', 'prettyphoto[portfolio-' + index + ']');

    });

    /**
     * Lightbox | Magnific Popup
     * with prettyPhoto backward compatibility
     */

    function lightbox() {

      var galleries = [];

      // init

      var init = function() {

        if (lightboxAttr) {
          compatibility();
          setType();
          constructor();
        }

      };

      // backward compatibility for prettyPhoto

      var compatibility = function() {

        $('a[rel^="prettyphoto"], a.prettyphoto, a[rel^="prettyphoto"]').each(function() {

          var el = $(this);
          var rel = el.attr('rel');

          if (rel) {
            rel = rel.replace('prettyphoto', 'lightbox');
          } else {
            rel = 'lightbox';
          }

          // Visual Composer prettyPhoto
          var dataRel = el.attr('data-rel');
          if (dataRel) {
            rel = dataRel.replace('prettyPhoto', 'lightbox');
            el.removeAttr('data-rel');
          }

          el.removeClass('prettyphoto').attr('rel', rel);

        });

      };

      // check if item is a part of gallery

      var isGallery = function(rel) {

        if (!rel) {
          return false;
        }

        var regExp = /\[(?:.*)\]/;
        var gallery = regExp.exec(rel);

        if (gallery) {
          gallery = gallery[0];
          gallery = gallery.replace('[', '').replace(']', '');
          return gallery;
        }

        return false;
      };

      // set array of names of galleries

      var setGallery = function(gallery) {

        if (galleries.indexOf(gallery) == -1) {
          galleries.push(gallery);
          return true;
        }

        return false;
      };

      // get file type by link

      var getType = function(src) {

        if (src.match(/youtube\.com\/watch/i) || src.match(/youtube\.com\/embed/i) || src.match(/youtu\.be/i)) {
          return 'iframe';

        } else if (src.match(/youtube-nocookie\.com/i)) {
          return 'iframe';

        } else if (src.match(/vimeo\.com/i)) {
          return 'iframe';

        } else if (src.match(/\biframe=true\b/i)) {
          return 'ajax';

        } else if (src.match(/\bajax=true\b/i)) {
          return 'ajax';

        } else if (src.substr(0, 1) == '#') {
          return 'inline';

        } else {
          return 'image';

        }
      };

      // set file type

      var setType = function() {

        $('a[rel^="lightbox"]').each(function() {

          var el = $(this);
          var href = el.attr('href');
          var rel = el.attr('rel');

          if (href) {

            // gallery

            var gallery = isGallery(rel);

            if (gallery) {
              el.attr('data-type', 'gallery');
              setGallery(gallery);
              return true;
            }

            el.attr('data-type', getType(href));

            // iframe paremeters

            if (getType(href) == 'iframe') {
              el.attr('href', href.replace('&rel=0', ''));
            }
          }

        });

      };

      // constructor

      var constructor = function() {

        var attr = {
          autoFocusLast: false,
          removalDelay: 160,
          image: {
            titleSrc: function(item) {
              var img = item.el.closest('.image_wrapper').find('img').first();
              if (lightboxAttr.title && img.length) {
                return img.attr('alt');
              } else {
                return false;
              }
            }
          }
        };

        // image

        $('a[rel^="lightbox"][data-type="image"]').magnificPopup({
          autoFocusLast: attr.autoFocusLast,
          removalDelay: attr.removalDelay,
          type: 'image',
          image: attr.image
        });

        // iframe | video

        $('a[rel^="lightbox"][data-type="iframe"]').magnificPopup({
          autoFocusLast: attr.autoFocusLast,
          removalDelay: attr.removalDelay,
          type: 'iframe',
          iframe: {
            patterns: {
              youtube: {
                index: 'youtube.com/',
                id: 'v=',
                src: '//www.youtube.com/embed/%id%?autoplay=1&rel=0'
              },
              youtu_be: {
                index: 'youtu.be/',
                id: '/',
                src: '//www.youtube.com/embed/%id%?autoplay=1&rel=0'
              },
              nocookie: {
                index: 'youtube-nocookie.com/embed/',
                id: '/',
                src: '//www.youtube-nocookie.com/embed/%id%?autoplay=1&rel=0'
              }
            }
          }
        });

        // inline

        $('a[rel^="lightbox"][data-type="inline"]').magnificPopup({
          autoFocusLast: attr.autoFocusLast,
          type: 'inline',
          midClick: true,
          callbacks: {
            open: function() {
              $('.mfp-content').children().addClass('mfp-inline');
            },
            beforeClose: function() {
              $('.mfp-content').children().removeClass('mfp-inline');
            }
          }
        });

        // gallery

        for (var i = 0, len = galleries.length; i < len; i++) {

          var gallery = '[' + galleries[i] + ']';
          gallery = 'a[rel^="lightbox' + gallery + '"]:visible';

          $(gallery).magnificPopup({
            autoFocusLast: attr.autoFocusLast,
            removalDelay: attr.removalDelay,
            type: 'image',
            image: attr.image,
            gallery: {
              enabled: true,
              tCounter: '<span class="mfp-counter">%curr% / %total%</span>' // markup of counter
            }
          });
        }

        // FIX: disable in Elementor wrapper

        $('.elementor a[rel^="lightbox"]').off('click');

      };

      // reload

      var reload = function() {

        $('a[rel^="lightbox"]').off('click');
        constructor();

      };

      // Visual Composer prettyPhoto | off

      var offPretty = function() {

        if (lightboxAttr) {
          $('a[data-rel^="prettyPhoto"], a[rel^="lightbox"]').each(function() {
            $(this).off('click.prettyphoto');
          });
        }

      };

      // init

      init();

      // onload

      $(window).on('load', offPretty);
      $(document).on('isotope:arrange', reload);

    }

    lightbox();

    /**
     * Menu | mfnMenu
     */

    function mainMenu() {

      var mmMobileInitW = mobileInitW;

      if ($('body').hasClass('header-simple') || $('#Header_creative.dropdown').length) {
        mmMobileInitW = 9999;
      }

      $('#menu > ul.menu').mfnMenu({
        addLast: true,
        arrows: true,
        mobileInit: mmMobileInitW,
        responsive: mfn.responsive
      });

      $('#secondary-menu > ul.secondary-menu').mfnMenu({
        mobileInit: mmMobileInitW,
        responsive: mfn.responsive
      });

    }

    mainMenu();

    /**
     * Menu | NOT One Page | .scroll item
     * Works with .scroll class
     * Since 4.8 replaced with: Page Options > One Page | function: onePageMenu()
     */

    function onePageScroll() {
      if (!$('body').hasClass('one-page')) {
        var menu = $('#menu');

        if (menu.find('li.scroll').length > 1) {
          menu.find('li.current-menu-item:not(:first)').removeClass('current-menu-item currenet-menu-parent current-menu-ancestor current-page-ancestor current_page_item current_page_parent current_page_ancestor');

          // menu item click
          menu.on('click','a',function() {
            $(this).closest('li').siblings('li').removeClass('current-menu-item currenet-menu-parent current-menu-ancestor current-page-ancestor current_page_item current_page_parent current_page_ancestor');
            $(this).closest('li').addClass('current-menu-item');
          });
        }
      }
    }
    onePageScroll();

    /**
     * Menu | One Page | Active on Scroll & Click
     */

    function onePageMenu() {
      if ($('body').hasClass('one-page')) {

        var menu = $('#menu');

        // add attr [data-hash] & [data-id]

        $('a[href]', menu).each(function() {

          var url = $(this).attr('href');
          if (url && url.split('#')[1]) {

            // data-hash
            var hash = '#' + url.split('#')[1];
            if (hash && $(hash).length) {
              // check if element with specified ID exists
              $(this).attr('data-hash', hash);
              $(hash).attr('data-id', hash);
            }

            // Visual Composer
            var vcHash = '#' + url.split('#')[1];
            var vcClass = '.vc_row.' + url.split('#')[1];
            if (vcClass && $(vcClass).length) {
              // check if element with specified Class exists
              $(this).attr('data-hash', vcHash);
              $(vcClass).attr('data-id', vcHash);
            }

          }

        });

        // active

        var hash;
        var activeSelector = '.menu > li.current-menu-item, .menu > li.current-menu-parent, .menu > li.current-menu-ancestor, .menu > li.current-page-ancestor, .menu > li.current_page_item, .menu > li.current_page_parent, .menu > li.current_page_ancestor';
        var activeClasses = 'current-menu-item current-menu-parent current-menu-ancestor current-page-ancestor current_page_item current_page_parent current_page_ancestor';

        if ($(activeSelector, menu).length) {

          // remove duplicated
          $(activeSelector, menu)
            .not(':first').removeClass(activeClasses);

          // remove if 1st link to section & section is not visible
          hash = $(activeSelector, menu).find('a[data-hash]').attr('data-hash');

          if (hash) {
            hash = '[data-id="' + hash + '"]';

            if ($(hash).length && $(hash).visible(true)) {
              // do nothing
            } else {
              $(activeSelector, menu).removeClass('current-menu-item current-menu-parent current-menu-ancestor current-page-ancestor current_page_item current_page_parent current_page_ancestor')
                .closest('.menu > li').removeClass('current-menu-item current-menu-parent current-menu-ancestor current-page-ancestor current_page_item current_page_parent current_page_ancestor');
            }
          } else {
            // do nothing
          }

        } else {

          // add to first if none is active
          var first = $('.menu:first-child > li:first-child', menu);
          var firstA = first.children('a');

          if (firstA.attr('data-hash')) {
            hash = firstA.attr('data-hash');
            hash = '[data-id="' + hash + '"]';

            if ($(hash).length && ($(hash).offset().top == adminBarH())) {
              first.addClass('current-menu-item');
            }
          }

        }

        // click

        $('#menu a[data-hash]').on('click', function(e) {
          e.preventDefault(); // only with: body.one-page

          // active

          menu.find('li').removeClass('current-menu-item');
          $(this)
            .closest('li').addClass('current-menu-item')
            .closest('.menu > li').addClass('current-menu-item');

          var hash = $(this).attr('data-hash');
          hash = '[data-id="' + hash + '"]';

          // mobile - sticky header - close menu

          if (window.innerWidth < 768) {
            $('.responsive-menu-toggle').removeClass('active');
            $('#Top_bar #menu').hide();
          }

          // offset

          var headerFixedAbH = $('.header-fixed.ab-show #Action_bar').innerHeight();
          var tabsHeaderH = $(hash).siblings('.ui-tabs-nav').innerHeight();

          var offset = headerFixedAbH + tabsHeaderH + adminBarH();

          // sticky height

          var stickyH = fixStickyHeaderH();

          // FIX | Header below | 1st section
          if ($('body').hasClass('header-below') && $('#Content').length) {
            if ($(hash).offset().top < ($('#Content').offset().top + 60)) {
              stickyH = -1;
            }
          }

          // animate scroll

          $('html, body').animate({
            scrollTop: $(hash).offset().top - offset - stickyH
          }, 500);

        });

      }
    }
    onePageMenu();

    /**
     * Header | Creative
     */

    var cHeader = 'body:not( .header-open ) #Header_creative',
      cHeaderEl = $(cHeader),
      cHeaderCurrnet;

    function creativeHeader() {

      $('.creative-menu-toggle').on('click', function(e) {
        e.preventDefault();

        cHeaderEl.addClass('active');
        $('.creative-menu-toggle, .creative-social', cHeaderEl).fadeOut(500);
        $('#Action_bar', cHeaderEl).fadeIn(500);

      });

    }
    creativeHeader();

    $(document).on('mouseenter', cHeader, function() {
      cHeaderCurrnet = 1;
    });

    $(document).on('mouseleave', cHeader, function() {
      cHeaderCurrnet = null;
      setTimeout(function() {
        if (!cHeaderCurrnet) {

          cHeaderEl.removeClass('active');
          $('.creative-menu-toggle, .creative-social', cHeaderEl).fadeIn(500);
          $('#Action_bar', cHeaderEl).fadeOut(500);

        }
      }, 1000);
    });

    // Fix: Header Creative & Mobile Sticky | On Resize > 768 px
    function creativeHeaderFix() {
      if ($('body').hasClass('header-creative') && window.innerWidth >= 768) {
        if ($('#Top_bar').hasClass('is-sticky')) {
          $('#Top_bar').removeClass('is-sticky');
        }
      }
    }

    /**
     * Header | Search
     */

    $("#search_button:not(.has-input), #Top_bar .icon_close").on('click', function(e) {
      e.preventDefault();
      $('#Top_bar .search_wrapper').fadeToggle()
        .find('.field').focus();
    });

    /**
     * WPML | Language switcher in the WP Menu
     */

    function mfnWPML() {
      $('#menu .menu-item-language:not(.menu-item-language-current)').each(function() {
        var el = $(this).children('a');

        if (!el.children('span:not(.icl_lang_sel_bracket)').length) {
          el.wrapInner('<span></span>');
        }

      });

      $('#menu span.icl_lang_sel_bracket').each(function() {
        var el = $(this);
        el.replaceWith(el.html());
      });

    }
    mfnWPML();

    /**
     * Breadcrumbs | Remove last item link
     */

    function breadcrumbsRemoveLastLink() {
      var el = $('.breadcrumbs.no-link').find('li').last();
      var text = el.text();
      el.html(text);
    }
    breadcrumbsRemoveLastLink();

    /**
     * Downcount
     */

    $('.downcount').each(function() {
      var el = $(this);
      el.downCount({
        date: el.attr('data-date'),
        offset: el.attr('data-offset')
      });
    });

    /**
     * Hover | on Touch | .tooltip, .hover_box
     */

    $('.tooltip, .hover_box')
      .on('touchstart', function() {
        $(this).toggleClass('hover');
      })
      .on('touchend', function() {
        $(this).removeClass('hover');
      });

    /**
     * Popup | Contact Form | Button
     */

    $("#popup_contact > a.button").on('click', function(e) {
      e.preventDefault();
      $(this).parent().toggleClass('focus');
    });

    /**
     * Scroll | niceScroll for Header Creative
     */

    if (('#Header_creative.scroll').length && window.innerWidth >= 1240) {
      $('#Header_creative.scroll').niceScroll({
        autohidemode: false,
        cursorborder: 0,
        cursorborderradius: 5,
        cursorcolor: '#222222',
        cursorwidth: 0,
        horizrailenabled: false,
        mousescrollstep: 40,
        scrollspeed: 60
      });
    }

    /**
     * Black & White
     */

    function mfnGreyscale() {
      $('.greyscale .image_wrapper > a, .greyscale .image_wrapper_tiles, .greyscale.portfolio-photo a, .greyscale .client_wrapper .gs-wrapper').has('img').BlackAndWhite({
        hoverEffect: false,
        intensity: 1 // opacity: 0, 0.1, ... 1
      });
    }
    mfnGreyscale();

    /**
     * Sliding Top
     */

    $('.sliding-top-control').on('click', function(e) {
      e.preventDefault();
      $('#Sliding-top .widgets_wrapper').slideToggle();
      $('#Sliding-top').toggleClass('active');
    });

    /**
     * Alert
     */

    $('body').on('click', '.alert .close', function(e) {
      e.preventDefault();
      $(this).closest('.alert').hide(300);
    });

    /**
     * Button | mark buttons with icon & label
     */

    $('a.button_js').each(function() {
      var btn = $(this);
      if (btn.find('.button_icon').length && btn.find('.button_label').length) {
        btn.addClass('kill_the_icon');
      }
    });

    /**
     * Navigation Arrows | Sticky
     */

    $('.fixed-nav').appendTo('body');

    /**
     * Feature List
     */

    $('.feature_list').each(function() {
      var col = $(this).attr('data-col') ? $(this).attr('data-col') : 4;
      $(this).find('li:nth-child(' + col + 'n):not(:last-child)').after('<hr />');
    });

    /**
     * IE Fixes
     */

    function checkIE() {
      // IE 9
      var ua = window.navigator.userAgent;
      var msie = ua.indexOf("MSIE ");
      if (msie > 0 && parseInt(ua.substring(msie + 5, ua.indexOf(".", msie))) == 9) {
        $("body").addClass("ie");
      }
    }
    checkIE();

    /**
     * Parallax
     */

    var ua = navigator.userAgent,
      isMobileWebkit = /WebKit/.test(ua) && /Mobile/.test(ua);

    if (!isMobileWebkit && window.innerWidth >= 768) {

      if (mfn.parallax == 'stellar') {

        // Stellar
        $.stellar({
          horizontalScrolling: false,
          responsive: true
        });

      } else {

        // Enllax
        $(window).enllax();

      }

    } else {

      $('div[data-enllax-ratio], div[data-stellar-ratio]').css('background-attachment', 'scroll');

    }

    /**
     * Load More | Ajax
     */

    $('.pager_load_more').on('click', function(e) {
      e.preventDefault();

      var el = $(this);
      var pager = el.closest('.pager_lm');
      var href = el.attr('href');

      // index | for many items on the page
      var index = $('.lm_wrapper').index(el.closest('.isotope_wrapper').find('.lm_wrapper'));

      el.fadeOut(50);
      pager.addClass('loading');

      $.get(href, function(data) {

        // content
        var content = $('.lm_wrapper:eq(' + index + ')', data).wrapInner('').html();

        if ($('.lm_wrapper:eq(' + index + ')').hasClass('isotope')) {
          // isotope
          $('.lm_wrapper:eq(' + index + ')').append($(content)).isotope('reloadItems').isotope({
            sortBy: 'original-order'
          });
        } else {
          // default
          $(content).hide().appendTo('.lm_wrapper:eq(' + index + ')').fadeIn(1000);
        }

        // next page link
        href = $('.lm_wrapper:eq(' + index + ')', data).next().find('.pager_load_more').attr('href');
        pager.removeClass('loading');
        if (href) {
          el.fadeIn();
          el.attr('href', href);
        }

        // refresh some staff

        mfnGreyscale();
        mfnJPlayer();
        lightbox();

        // isotope fix: second resize

        $('.lm_wrapper.isotope').imagesLoaded().progress(function() {
          $('.lm_wrapper.isotope').isotope('layout');
        });

      });

    });

    /**
     * Filters | Blog & Portfolio
     */

    $('.filters_buttons .open').on('click', function(e) {
      e.preventDefault();
      var type = $(this).closest('li').attr('class');

      $('.filters_wrapper').show(200);
      $('.filters_wrapper ul.' + type).show(200);
      $('.filters_wrapper ul:not(.' + type + ')').hide();
    });

    $('.filters_wrapper .close a').on('click', function(e) {
      e.preventDefault();
      $('.filters_wrapper').hide(200);
    });

    /**
     * Portfolio List | Next v / Prev ^ buttons
     */

    $('.portfolio_next_js').on('click', function(e) {
      e.preventDefault();

      var item = $(this).closest('.portfolio-item').next();

      if (item.length) {
        $('html, body').animate({
          scrollTop: item.offset().top - fixStickyHeaderH()
        }, 500);
      }
    });

    $('.portfolio_prev_js').on('click', function(e) {
      e.preventDefault();

      var item = $(this).closest('.portfolio-item').prev();

      if (item.length) {
        $('html, body').animate({
          scrollTop: item.offset().top - fixStickyHeaderH()
        }, 500);
      }
    });

    /**
     * Link | Smooth Scroll | .scroll
     */

    $('.scroll > a, a.scroll').on('click', function(e) {

      // prevent default if link directs to the current page

      var urlL = location.href.replace(/\/#.*|#.*/, '');
      var urlT = this.href.replace(/\/#.*|#.*/, '');

      if (urlL == urlT) {
        e.preventDefault();
      }

      var hash = this.hash;

      // offset

      var headerFixedAbH = $('.header-fixed.ab-show #Action_bar').innerHeight();
      var tabsHeaderH = $(hash).siblings('.ui-tabs-nav').innerHeight();

      var offset = headerFixedAbH + tabsHeaderH + adminBarH();

      // animate scroll

      if (hash && $(hash).length) {

        $('html, body').animate({
          scrollTop: $(hash).offset().top - offset - fixStickyHeaderH()
        }, 500);
      }
    });

    /**
     * Tabs
     */

    $('.jq-tabs').tabs();

    /**
     * Accordion & FAQ
     */

    $('.mfn-acc').each(function() {
      var el = $(this);

      if (el.hasClass('openAll')) {

        // show all
        el.find('.question')
          .addClass("active")
          .children(".answer")
          .show();

      } else {

        // show one
        var activeTab = el.attr('data-active-tab');
        if (el.hasClass('open1st')) activeTab = 1;

        if (activeTab) {
          el.find('.question').eq(activeTab - 1)
            .addClass("active")
            .children(".answer")
            .show();
        }

      }
    });

    $('.mfn-acc .question > .title').on('click', function() {

      if ($(this).parent().hasClass("active")) {

        $(this).parent().removeClass("active").children(".answer").slideToggle(100);

      } else {

        if (!$(this).closest('.mfn-acc').hasClass('toggle')) {
          $(this).parents(".mfn-acc").children().each(function() {
            if ($(this).hasClass("active")) {
              $(this).removeClass("active").children(".answer").slideToggle(100);
            }
          });
        }
        $(this).parent().addClass("active");
        $(this).next(".answer").slideToggle(100);

      }

      setTimeout(function() {
        $(window).trigger('resize');
      }, 50);

    });

    // Visual Composer | Accordion | Sidebar height
    $('.wpb_wrapper .vc_tta-panel-title').on('click', 'a', function() {

      setTimeout(function() {
        $(window).trigger('resize');
      }, 50);

    });

    /**
     * Helper
     */

    $('.helper .link.toggle').on('click', function(e) {

      e.preventDefault();

      var el = $(this);
      var id = el.attr('data-rel');
      var parent = el.closest('.helper');

      if (el.hasClass('active')) {

        el.removeClass('active');
        parent.find('.helper_content > .item-' + id).removeClass('active').slideUp(200);

      } else {

        parent.find('.links > .link.active').removeClass('active');
        parent.find('.helper_content > .item.active').slideUp(200);

        el.addClass('active');
        parent.find('.helper_content > .item-' + id).addClass('active').slideDown(200);

      }

      setTimeout(function() {
        $(window).trigger('resize');
      }, 50);

    });

    /**
     * HTML5 Video | jPlayer
     */

    function mfnJPlayer() {
      $('.mfn-jplayer').each(function() {

        var m4v = $(this).attr('data-m4v'),
          poster = $(this).attr('data-img'),
          swfPath = $(this).attr('data-swf'),
          cssSelectorAncestor = '#' + $(this).closest('.mfn-jcontainer').attr('id');

        $(this).jPlayer({
          ready: function() {
            $(this).jPlayer('setMedia', {
              m4v: m4v,
              poster: poster
            });
          },
          play: function() {
            // To avoid both jPlayers playing together.
            $(this).jPlayer('pauseOthers');
          },
          size: {
            cssClass: 'jp-video-360p',
            width: '100%',
            height: '360px'
          },
          swfPath: swfPath,
          supplied: 'm4v',
          cssSelectorAncestor: cssSelectorAncestor,
          wmode: 'opaque'
        });
      });
    }
    mfnJPlayer();

    /**
     * Love
     */

    $('.mfn-love').on('click', function() {
      var el = $(this);

      if (el.hasClass('loved')) {
        return false;
      }
      el.addClass('loved');

      var post = {
        action: 'mfn_love',
        post_id: el.attr('data-id')
      };

      $.post(mfn.ajax, post, function(data) {
        el.find('.label').html(data);
      });

      return false;
    });

    /**
     * Go to top
     */

    $('#back_to_top').on('click', function() {
      $('body,html').animate({
        scrollTop: 0
      }, 500);
      return false;
    });

    /**
     * Section | Next v / Prev ^ arrows
     */

    $('.section .section-nav').on('click', function() {

      var el = $(this);
      var section = el.closest('.section');

      if (el.hasClass('prev')) {

        // Previous Section
        if (section.prev().length) {
          $('html, body').animate({
            scrollTop: section.prev().offset().top
          }, 500);
        }

      } else {

        // Next Section
        if (section.next().length) {
          $('html, body').animate({
            scrollTop: section.next().offset().top
          }, 500);
        }

      }
    });

    /**
     * Intro | Scroll v arrow
     */

    $('#Intro .intro-next').on('click', function() {
      var intro = $(this).closest('#Intro');

      if (intro.next().length) {
        $('html, body').animate({
          scrollTop: intro.next().offset().top - fixStickyHeaderH() - adminBarH()
        }, 500);
      }
    });

    /**
     * Widget | Muffin Menu | Hover on click
     */

    $('.widget_mfn_menu ul.submenus-click').each(function() {

      var el = $(this);

      $('a', el).on('click', function(e) {
        var li = $(this).closest('li');

        if (li.hasClass('hover') || !li.hasClass('menu-item-has-children')) {
          // link click
        } else {
          e.preventDefault();
          li.siblings('li').removeClass('hover')
            .find('li').removeClass('hover');
          $(this).closest('li').addClass('hover');
        }

      });

    });

    /**
     * WooCommerce | Add to cart
     */

    function addToCart() {

      $('body').on('click', '.add_to_cart_button', function() {
        $(this)
          .closest('.product')
          .addClass('adding-to-cart')
          .removeClass('added-to-cart');
      });

      $('body').on('added_to_cart', function() {
        $('.adding-to-cart')
          .removeClass('adding-to-cart')
          .addClass('added-to-cart');
      });
    }
    addToCart();

    /**
     * WooCommerce | Rating click
     */

    $('.woocommerce-product-rating').on('click', function(){

      var el;

      if( $('.woocommerce-content .jq-tabs').length ){

        el = $('.woocommerce-content .jq-tabs');
        $('.ui-tabs-nav a[href="#tab-reviews"]', el).trigger('click');

      } else {

        el = $('.woocommerce-content .accordion');
        $('#reviews').closest('.question:not(.active)').children('.title').trigger('click');

      }

      // offset

      var offset = $('.header-fixed.ab-show #Action_bar').innerHeight() + adminBarH();

      // animate scroll

      $('html, body').animate({
        scrollTop: el.offset().top - offset - fixStickyHeaderH()
      }, 500);

    });

    /**
     * Ajax | Complete
     */

    $(document).ajaxComplete(function() {

      setTimeout(function() {
        $(window).trigger('resize');
      }, 100);

    });

    /**
     * Isotope
     */

    // Isotope | Fiters

    function isotopeFilter(domEl, isoWrapper) {

      var filter = domEl.attr('data-rel');
      isoWrapper.isotope({
        filter: filter
      });

      setTimeout(function() {
        $(window).trigger('resize');
      }, 50);

    }

    // Isotope | Fiters | Click ----------

    $('.isotope-filters .filters_wrapper').find('li:not(.close) a').on('click', function(e) {
      e.preventDefault();

      var isoWrapper = $('.isotope'),
        filters = $(this).closest('.isotope-filters'),
        parent = filters.attr('data-parent');

      if (parent) {
        parent = filters.closest('.' + parent);
        isoWrapper = parent.find('.isotope').first();
      }

      filters.find('li').removeClass('current-cat');
      $(this).closest('li').addClass('current-cat');

      isotopeFilter($(this), isoWrapper);

      setTimeout(function() {
        $(document).trigger('isotope:arrange');
      }, 500);
    });

    // Isotope | Fiters | Reset

    $('.isotope-filters .filters_buttons').find('li.reset a').on('click', function(e) {
      e.preventDefault();

      $('.isotope-filters .filters_wrapper').find('li').removeClass('current-cat');
      isotopeFilter($(this), $('.isotope'));
    });

    /**
     * $(window).on('debouncedresize')
     * Specify a function to execute on window resize
     */

    $(window).on('debouncedresize', function() {

      // Isotope | Relayout
      $('.masonry.isotope').isotope();
      $('.masonry.gallery').isotope('layout');

      // Sliding Footer | Height
      mfnFooter();

      // Header Width
      mfnHeader();

      // Sidebar Height
      mfnSidebar();

      // Full Screen Section
      mfnSectionH();

      // Full Screen Intro
      mfnIntroH();

      // Equal | Height
      mfnEqualHeightWrap();
      mfnEqualHeight();

      // Header Creative & Mobile Sticky
      creativeHeaderFix();

    });

    /**
     * document.ready
     * Initialize document ready functions
     */

    mfnSliderBlog();
    mfnSliderClients();
    mfnSliderOffer();
    mfnSliderOfferThumb();
    mfnSliderShop();

    sliderPortfolio();
    sliderSlider();
    sliderTestimonials();

    // Sidebar | Height
    mfnSidebar();

    // Sliding Footer | Height
    mfnFooter();

    // Header | Width
    mfnHeader();

    // Full Screen Section
    mfnSectionH();
    // Navigation | Hash
    hashNav();

    // Full Screen Intro
    mfnIntroH();

    // Equal | Height
    mfnEqualHeightWrap();
    mfnEqualHeight();
  });

  /**
   * $(window).scroll
   * The scroll event is sent to an element when the user scrolls to a different place in the element.
   */

  $(window).scroll(function() {

    // Header | Sticky
    mfnSticky();
    mfnMobileSticky();

    // Back to top | Sticky
    backToTopSticky();

    // One Page | Scroll | Active Section
    onePageActive();

  });

  /**
   * $(window).load
   * The load event is sent to an element when it and all sub-elements have been completely loaded.
   */

  $(window).load(function() {

    /**
     * Elementor plugin
     * Disable built-in one page
     */

    function elementorDisableOnePage(){

      if( ! $('body').hasClass('one-page') ){
        return false;
      }

      setTimeout(function(){
        var doc=$(document),
          $events=$("a[href*='#']").length ? $._data(doc[0],"events") : null;
        if($events){
          for(var i=$events.click.length-1; i>=0; i--){
            var handler=$events.click[i];
            if(handler && handler.namespace != "mPS2id" && handler.selector === 'a[href*="#"]' ) doc.off("click",handler.handler);
          }
        }
      }, 300);

    }
    elementorDisableOnePage();

    /**
     * Retina Logo
     */

    function retinaLogo() {
      if (window.devicePixelRatio > 1) {

        var el, src, height,
          parent = $('#Top_bar #logo'),
          parentH = parent.data('height');

        var maxH = {
          sticky: {
            init: 35,
            noPadding: 60,
            overflow: 110
          },
          mobile: {
            mini: 50,
            miniNoPadding: 60
          },
          mobileSticky: {
            init: 50,
            noPadding: 60,
            overflow: 80
          }
        };

        $('#Top_bar #logo img').each(function(index) {

          el = $(this);
          src = el.data('retina');
          height = el.height();

          // main

          if (el.hasClass('logo-main')) {

            if ($('body').hasClass('logo-overflow')) {

              // do nothing

            } else if (height > parentH) {

              height = parentH;

            }

          }

          // sticky

          if (el.hasClass('logo-sticky')) {

            if ($('body').hasClass('logo-overflow')) {

              if (height > maxH.sticky.overflow) {
                height = maxH.sticky.overflow;
              }

            } else if ($('body').hasClass('logo-no-sticky-padding')) {

              if (height > maxH.sticky.noPadding) {
                height = maxH.sticky.noPadding;
              }

            } else if (height > maxH.sticky.init) {

              height = maxH.sticky.init;

            }

          }

          // mobile

          if (el.hasClass('logo-mobile')) {

            if ($('body').hasClass('mobile-header-mini')) {

              if (parent.data('padding') > 0) {

                if (height > maxH.mobile.mini) {
                  height = maxH.mobile.mini;
                }

              } else {

                if (height > maxH.mobile.miniNoPadding) {
                  height = maxH.mobile.miniNoPadding;
                }

              }

            }

          }

          // mobile-sticky

          if (el.hasClass('logo-mobile-sticky')) {

            if ($('body').hasClass('logo-no-sticky-padding')) {

              if (height > maxH.mobileSticky.noPadding) {
                height = maxH.mobileSticky.noPadding;
              }

            } else if (height > maxH.mobileSticky.init) {
              height = maxH.mobileSticky.init;
            }

          }

          // SET

          if (src) {
            el.parent().addClass('retina');
            el.attr('src', src).css('max-height', height + 'px');
          }

        });

      }
    }
    retinaLogo();

    /**
     * Isotope
     */

    // Portfolio - Isotope

    $('.blog_wrapper .isotope:not( .masonry ), .portfolio_wrapper .isotope:not( .masonry-flat, .masonry-hover, .masonry-minimal )').isotope({
      itemSelector: '.isotope-item',
      layoutMode: 'fitRows',
      isOriginLeft: rtl ? false : true
    });

    // Portfolio - Masonry Flat

    $('.portfolio_wrapper .masonry-flat').isotope({
      itemSelector: '.isotope-item',
      percentPosition: true,
      masonry: {
        columnWidth: 1
      },
      isOriginLeft: rtl ? false : true
    });

    // Blog & Portfolio - Masonry

    $('.isotope.masonry, .isotope.masonry-hover, .isotope.masonry-minimal').isotope({
      itemSelector: '.isotope-item',
      layoutMode: 'masonry',
      isOriginLeft: rtl ? false : true
    });

    // Portfolio | Active Category

    function portfolioActive() {
      var el = $('.isotope-filters .filters_wrapper');
      var active = el.attr('data-cat');

      if (active) {
        el.find('li.' + active).addClass('current-cat');
        $('.isotope').isotope({
          filter: '.category-' + active
        });
      }
    }
    portfolioActive();

    /**
     * Chart
     */

    $('.chart').waypoint({

      offset: '100%',
      triggerOnce: true,
      handler: function(){

        var el = $(this.element).length ? $(this.element) : $(this);
        var lineW = simple ? 4 : 8;

        el.easyPieChart({
          animate: 1000,
          lineCap: 'circle',
          lineWidth: lineW,
          size: 140,
          scaleColor: false
        });

        if (typeof this.destroy !== 'undefined' && $.isFunction(this.destroy)) {
          this.destroy();
        }
      }
    });

    /**
     * Skills
     */

    $('.bars_list').waypoint({

      offset: '100%',
      triggerOnce: true,
      handler: function() {

        var el = $(this.element).length ? $(this.element) : $(this);

        el.addClass('hover');

        if (typeof this.destroy !== 'undefined' && $.isFunction(this.destroy)) {
          this.destroy();
        }
      }
    });

    /**
     * Progress Icons
     */

    $('.progress_icons').waypoint({

      offset: '100%',
      triggerOnce: true,
      handler: function() {

        var el = $(this.element).length ? $(this.element) : $(this);
        var active = el.attr('data-active');
        var color = el.attr('data-color');
        var icon = el.find('.progress_icon');
        var timeout = 200; // timeout in milliseconds

        icon.each(function(i) {
          if (i < active) {
            var time = (i + 1) * timeout;
            setTimeout(function() {
              $(icon[i])
                .addClass('themebg')
                .css('background-color', color);
            }, time);
          }
        });

        if (typeof this.destroy !== 'undefined' && $.isFunction(this.destroy)) {
          this.destroy();
        }
      }
    });

    /**
     * Animate Math | Counter, Quick Fact, etc.
     */

    $('.animate-math .number').waypoint({

      offset: '100%',
      triggerOnce: true,
      handler: function() {

        var el = $(this.element).length ? $(this.element) : $(this);
        var duration = Math.floor((Math.random() * 1000) + 1000);
        var to = el.attr('data-to');

        $({
          property: 0
        }).animate({
          property: to
        }, {
          duration: duration,
          easing: 'linear',
          step: function() {
            el.text(Math.floor(this.property));
          },
          complete: function() {
            el.text(this.property);
          }
        });

        if (typeof this.destroy !== 'undefined' && $.isFunction(this.destroy)) {
          this.destroy();
        }
      }
    });

    /**
     * Before After | TwentyTwenty
     */

    $('.before_after.twentytwenty-container').twentytwenty();

    /**
     * window.load | Init
     * Initialize window load functions
     */

    // Header | Sticky
    mfnStickyH();
    mfnSticky();
    mfnMobileSticky();

    // Full Screen Section
    mfnSectionH();

    // Navigation | Hash
    hashNav();

    // Full Screen Intro
    mfnIntroH();

    // FIX | Revolution Slider Width & Height OnLoad
    $(window).trigger('resize');

    // Sidebar | Height
    setTimeout(function() {
      mfnSidebar();
    }, 10);

  });

  /**
   * $(document).mouseup
   * Specify a function to execute when the DOM is fully loaded.
   */

  $(document).mouseup(function(e) {

    // Widget | Muffin Menu | Hover on click

    if ($('.widget_mfn_menu ul.submenus-click').length && ($('.widget_mfn_menu ul.submenus-click').has(e.target).length === 0) ) {
      $('.widget_mfn_menu ul.submenus-click li').removeClass('hover');
    }

    // Mobile Menu | Classic | Close on click outside menu

    if ($('.menu_wrapper').length && ( $('.menu_wrapper').has(e.target).length === 0 )) {
      if( $('.responsive-menu-toggle').hasClass('active') ){
        $('.responsive-menu-toggle').trigger('click');
      }
    }

  });

  /**
   * Sliders configuration
   */

  // Slick Slider | Auto responsive

  function slickAutoResponsive(slider, max, size) {

    if (!max){
      max = 5;
    }
    if (!size){
      size = 380;
    }

    var width = slider.width(),
      count = Math.ceil(width / size);

    if (count < 1) count = 1;
    if (count > max) count = max;

    return count;
  }

  // Slider | Offer Thumb

  function mfnSliderOfferThumb() {

    var pager = function(el, i) {
      var img = $(el.$slides[i]).children('.thumbnail').html();
      return '<a>' + img + '</a>';
    };

    $('.offer_thumb_ul').each(function() {

      var slider = $(this);

      slider.slick({
        cssEase: 'ease-out',
        arrows: false,
        dots: true,
        infinite: true,
        touchThreshold: 10,
        speed: 300,

        adaptiveHeight: true,
        appendDots: slider.siblings('.slider_pagination'),
        customPaging: pager,

        rtl: rtl ? true : false,
        autoplay: mfn.slider.offer ? true : false,
        autoplaySpeed: mfn.slider.offer ? mfn.slider.offer : 5000,

        slidesToShow: 1,
        slidesToScroll: 1
      });

    });
  }

  // Slider | Offer

  function mfnSliderOffer() {
    $('.offer_ul').each(function() {

      var slider = $(this);

      slider.slick({
        cssEase: 'ease-out',
        dots: false,
        infinite: true,
        touchThreshold: 10,
        speed: 300,

        prevArrow: '<a class="button button_large button_js slider_prev" href="#"><span class="button_icon"><i class="icon-up-open-big"></i></span></a>',
        nextArrow: '<a class="button button_large button_js slider_next" href="#"><span class="button_icon"><i class="icon-down-open-big"></i></span></a>',

        adaptiveHeight: true,
        //customPaging 	: pager,

        rtl: rtl ? true : false,
        autoplay: mfn.slider.offer ? true : false,
        autoplaySpeed: mfn.slider.offer ? mfn.slider.offer : 5000,

        slidesToShow: 1,
        slidesToScroll: 1
      });

      // Pagination | Show (css)

      slider.siblings('.slider_pagination').addClass('show');

      // Pager | Set slide number after change

      slider.on('afterChange', function(event, slick, currentSlide, nextSlide) {
        slider.siblings('.slider_pagination').find('.current').text(currentSlide + 1);
      });

    });
  }

  // Slider | Shop

  function mfnSliderShop() {

    var pager = function(el, i) {
      return '<a>' + i + '</a>';
    };

    $('.shop_slider_ul').each(function() {

      var slider = $(this);
      var slidesToShow = 4;

      var count = slider.closest('.shop_slider').data('count');
      if (slidesToShow > count) {
        slidesToShow = count;
        if (slidesToShow < 1) {
          slidesToShow = 1;
        }
      }

      slider.slick({
        cssEase: 'ease-out',
        dots: true,
        infinite: true,
        touchThreshold: 10,
        speed: 300,

        prevArrow: '<a class="button button_js slider_prev" href="#"><span class="button_icon"><i class="icon-left-open-big"></i></span></a>',
        nextArrow: '<a class="button button_js slider_next" href="#"><span class="button_icon"><i class="icon-right-open-big"></i></span></a>',
        appendArrows: slider.siblings('.blog_slider_header'),

        appendDots: slider.siblings('.slider_pager'),
        customPaging: pager,

        rtl: rtl ? true : false,
        autoplay: mfn.slider.shop ? true : false,
        autoplaySpeed: mfn.slider.shop ? mfn.slider.shop : 5000,

        slidesToShow: slickAutoResponsive(slider, slidesToShow),
        slidesToScroll: slickAutoResponsive(slider, slidesToShow)
      });

      // ON | debouncedresize

      $(window).on('debouncedresize', function() {
        slider.slick('slickSetOption', 'slidesToShow', slickAutoResponsive(slider, slidesToShow), false);
        slider.slick('slickSetOption', 'slidesToScroll', slickAutoResponsive(slider, slidesToShow), true);
      });

    });
  }

  // Slider | Blog

  function mfnSliderBlog() {

    var pager = function(el, i) {
      return '<a>' + i + '</a>';
    };

    $('.blog_slider_ul').each(function() {

      var slider = $(this);
      var slidesToShow = 4;

      var count = slider.closest('.blog_slider').data('count');
      if (slidesToShow > count) {
        slidesToShow = count;
        if (slidesToShow < 1) {
          slidesToShow = 1;
        }
      }

      slider.slick({
        cssEase: 'ease-out',
        dots: true,
        infinite: true,
        touchThreshold: 10,
        speed: 300,

        prevArrow: '<a class="button button_js slider_prev" href="#"><span class="button_icon"><i class="icon-left-open-big"></i></span></a>',
        nextArrow: '<a class="button button_js slider_next" href="#"><span class="button_icon"><i class="icon-right-open-big"></i></span></a>',
        appendArrows: slider.siblings('.blog_slider_header'),

        appendDots: slider.siblings('.slider_pager'),
        customPaging: pager,

        rtl: rtl ? true : false,
        autoplay: mfn.slider.blog ? true : false,
        autoplaySpeed: mfn.slider.blog ? mfn.slider.blog : 5000,

        slidesToShow: slickAutoResponsive(slider, slidesToShow),
        slidesToScroll: slickAutoResponsive(slider, slidesToShow)
      });

      // On | debouncedresize

      $(window).on('debouncedresize', function() {
        slider.slick('slickSetOption', 'slidesToShow', slickAutoResponsive(slider, slidesToShow), false);
        slider.slick('slickSetOption', 'slidesToScroll', slickAutoResponsive(slider, slidesToShow), true);
      });

    });
  }

  // Slider | Clients

  function mfnSliderClients() {
    $('.clients_slider_ul').each(function() {

      var slider = $(this);

      slider.slick({
        cssEase: 'ease-out',
        dots: false,
        infinite: true,
        touchThreshold: 10,
        speed: 300,

        prevArrow: '<a class="button button_js slider_prev" href="#"><span class="button_icon"><i class="icon-left-open-big"></i></span></a>',
        nextArrow: '<a class="button button_js slider_next" href="#"><span class="button_icon"><i class="icon-right-open-big"></i></span></a>',
        appendArrows: slider.siblings('.clients_slider_header'),

        rtl: rtl ? true : false,
        autoplay: mfn.slider.clients ? true : false,
        autoplaySpeed: mfn.slider.clients ? mfn.slider.clients : 5000,

        slidesToShow: slickAutoResponsive(slider, 4),
        slidesToScroll: slickAutoResponsive(slider, 4)
      });

      // ON | debouncedresize

      $(window).on('debouncedresize', function() {
        slider.slick('slickSetOption', 'slidesToShow', slickAutoResponsive(slider, 4), false);
        slider.slick('slickSetOption', 'slidesToScroll', slickAutoResponsive(slider, 4), true);
      });

    });
  }

  // Slider | Portfolio

  function sliderPortfolio() {

    $('.portfolio_slider_ul').each(function() {

      var slider = $(this);
      var size = 380;
      var scroll = 5;

      if (slider.closest('.portfolio_slider').data('size')) {
        size = slider.closest('.portfolio_slider').data('size');
      }

      if (slider.closest('.portfolio_slider').data('size')) {
        scroll = slider.closest('.portfolio_slider').data('scroll');
      }

      slider.slick({
        cssEase: 'ease-out',
        dots: false,
        infinite: true,
        touchThreshold: 10,
        speed: 300,

        prevArrow: '<a class="slider_nav slider_prev themebg" href="#"><i class="icon-left-open-big"></i></a>',
        nextArrow: '<a class="slider_nav slider_next themebg" href="#"><i class="icon-right-open-big"></i></a>',

        rtl: rtl ? true : false,
        autoplay: mfn.slider.portfolio ? true : false,
        autoplaySpeed: mfn.slider.portfolio ? mfn.slider.portfolio : 5000,

        slidesToShow: slickAutoResponsive(slider, 5, size),
        slidesToScroll: slickAutoResponsive(slider, scroll, size)
      });

      // ON | debouncedresize
      $(window).on('debouncedresize', function() {
        slider.slick('slickSetOption', 'slidesToShow', slickAutoResponsive(slider, 5, size), false);
        slider.slick('slickSetOption', 'slidesToScroll', slickAutoResponsive(slider, scroll, size), true);
      });

    });
  }

  // Slider | Slider

  function sliderSlider() {

    var pager = function(el, i) {
      return '<a>' + i + '</a>';
    };

    $('.content_slider_ul').each(function() {

      var slider = $(this);
      var count = 1;
      var centerMode = false;

      if (slider.closest('.content_slider').hasClass('carousel')) {
        count = slickAutoResponsive(slider);

        $(window).on('debouncedresize', function() {
          slider.slick('slickSetOption', 'slidesToShow', slickAutoResponsive(slider), false);
          slider.slick('slickSetOption', 'slidesToScroll', slickAutoResponsive(slider), true);
        });
      }

      if (slider.closest('.content_slider').hasClass('center')) {
        centerMode = true;
      }

      slider.slick({
        cssEase: 'cubic-bezier(.4,0,.2,1)',
        dots: true,
        infinite: true,
        touchThreshold: 10,
        speed: 300,

        centerMode: centerMode,
        centerPadding: '20%',

        prevArrow: '<a class="button button_js slider_prev" href="#"><span class="button_icon"><i class="icon-left-open-big"></i></span></a>',
        nextArrow: '<a class="button button_js slider_next" href="#"><span class="button_icon"><i class="icon-right-open-big"></i></span></a>',

        adaptiveHeight: true,
        appendDots: slider.siblings('.slider_pager'),
        customPaging: pager,

        rtl: rtl ? true : false,
        autoplay: mfn.slider.slider ? true : false,
        autoplaySpeed: mfn.slider.slider ? mfn.slider.slider : 5000,

        slidesToShow: count,
        slidesToScroll: count
      });

      // Lightbox | disable on dragstart

      var clickEvent = false;

      slider.on('dragstart', '.slick-slide a[rel="lightbox"]', function(event) {
        if (lightboxAttr) {
          var events = $(this).data('events');
          if( events.hasOwnProperty('click') ){
            clickEvent = events.click[0];
            $(this).addClass('off-click').off('click');
          }
        }
      });

      // Lightbox | enable after change

      slider.on('afterChange', function(event, slick, currentSlide, nextSlide) {
        if (lightboxAttr) {
          $('a.off-click[rel="lightbox"]', slider).removeClass('off-click').on('click', clickEvent);
        }
      });

    });
  }

  // Slider | Testimonials

  function sliderTestimonials() {

    var pager = function(el, i) {
      var img = $(el.$slides[i]).find('.single-photo-img').html();
      return '<a>' + img + '</a>';
    };

    $('.testimonials_slider_ul').each(function() {

      var slider = $(this);

      slider.slick({
        cssEase: 'ease-out',
        dots: true,
        infinite: true,
        touchThreshold: 10,
        speed: 300,

        prevArrow: '<a class="button button_js slider_prev" href="#"><span class="button_icon"><i class="icon-left-open-big"></i></span></a>',
        nextArrow: '<a class="button button_js slider_next" href="#"><span class="button_icon"><i class="icon-right-open-big"></i></span></a>',

        adaptiveHeight: true,
        appendDots: slider.siblings('.slider_pager'),
        customPaging: pager,

        rtl: rtl ? true : false,
        autoplay: mfn.slider.testimonials ? true : false,
        autoplaySpeed: mfn.slider.testimonials ? mfn.slider.testimonials : 5000,

        slidesToShow: 1,
        slidesToScroll: 1
      });

    });
  }

})(jQuery);
