(function($) {

    /*-- Strict mode enabled --*/
    'use strict';


    var _html = document.documentElement,
    isTouch = (('ontouchstart' in _html) || (navigator.msMaxTouchPoints > 0) || (navigator.maxTouchPoints));

    _html.className = _html.className.replace("no-js", "js");
    _html.classList.add(isTouch ? "touch" : "no-touch");


    var nHtmlNode = document.documentElement,
    nBodyNode = document.body || document.getElementsByTagName('body')[0],

    jWindow   = $(window),
    jBodyNode = $(nBodyNode),

    rAF = window.requestAnimationFrame ||
    window.mozRequestAnimationFrame ||
    window.webkitRequestAnimationFrame ||
    window.msRequestAnimationFrame||
    function (callback) {
        setTimeout(callback, 1000 / 60);
    };

    /* LazyLoad
    ================================================== */
    var myLazyLoad = new LazyLoad({
        elements_selector: ".lazy",
        data_src: 'src',
        data_srcset: 'srcset',
        threshold: 500,
        callback_enter: function (element) {

        },
        callback_load: function (element) {
            element.removeAttribute('data-src');

            var oTimeout = setTimeout(function ()
            {
                clearTimeout(oTimeout);

                AOS.refresh();

            }, 1000);
        },
        callback_error: function(element) {
            element.src = "https://placeholdit.imgix.net/~text?txtsize=21&txt=Image%20not%20load&w=200&h=200";
        }
    });


    /* header
    ================================================== */
    function _header ()
    {
        var nHeader      = document.getElementById('top-bar'),
        nMenu        = document.getElementById('top-bar__navigation'),
        nMenuToggler = document.getElementById('top-bar__navigation-toggler'),

        jHeader      = $(nHeader),
        jMenu        = $(nMenu),
        jMenuToggler = $(nMenuToggler),

        jLink        = jMenu.find('li a'),
        jSubmenu     = jMenu.find('.submenu');

        if ( jSubmenu.length > 0 )
        {
            jSubmenu.parent('li').addClass('has-submenu');
        };

        jMenuToggler.on('touchend click', function (e) {
            e.preventDefault();

            var $this = $(this);

            $this.toggleClass('is-active');
            jHeader.toggleClass('is-expanded');

            if ( $this.hasClass('is-active') )
            {
                nHtmlNode.style.overflow = 'hidden';
            }
            else
            {
                nHtmlNode.style.overflow = '';
            }

            return false;
        });
        /*
        jLink.on('touchend click', function (e) {

        var $this = $(this),
        $parent = $this.parent();

        if ( jMenuToggler.is(':visible') && $this.next(jSubmenu).length )
        {
        if ( $this.next().is(':visible') )
        {
        $parent.removeClass('drop_active');
        $this.next().slideUp('fast');

    } else {

    $this.closest('ul').find('li').removeClass('drop_active');
    $this.closest('ul').find('.submenu').slideUp('fast');
    $parent.addClass('drop_active');
    $this.next().slideDown('fast');
};

return false;
};

});
*/
$('.caret').on('touchend click', function (e) {
    var $this = $(this),
    $parent = $this.parent();
    $('.caret').removeClass('opened');
    if ( jMenuToggler.is(':visible') && $this.next(jSubmenu).length ) {

        if ( $this.next().is(':visible') ) {

            $parent.removeClass('drop_active');
            $this.next().slideUp('fast');
            $this.removeClass('opened');

        } else {

            $this.closest('ul').find('li').removeClass('drop_active');
            $this.closest('ul').find('.submenu').slideUp('fast');
            $parent.addClass('drop_active');
            $this.next().slideDown('fast');
            $this.addClass('opened');
        };

        return false;
    };
});
jWindow.smartresize(function() {

    if ( window.innerWidth >= 991 )
    {
        jHeader.removeClass('is-expanded');
        jMenuToggler.removeClass('is-active');
        jSubmenu.removeAttr('style');
        nHtmlNode.style.overflow = '';
    }
});
}

/* parallax
================================================== */
function _parallax ()
{
    if ( device.desktop() )
    {
        var el = document.querySelectorAll('.jarallax');

        jarallax(el, {
            type: 'scroll', // scroll, scale, opacity, scroll-opacity, scale-opacity
            zIndex: -20,
            onScroll: function(calculations) {

            },

            onInit: function() {

            },
            onDestroy: function() {

            },
            onCoverImage: function() {

            }
        });
    };
};

/* isotope sorting
================================================== */
function _gallery_sorting ()
{
    var nOptionSets = document.getElementById('gallery-set'),
    jOptionSets = $(nOptionSets);

    if ( jOptionSets.length > 0 )
    {
        var jIsoContainer = $('.js-isotope'),
        jOptionLinks = jOptionSets.find('a');

        jOptionLinks.on('click', function(e) {
            var $this = $(this),
            currentOption = $this.data('cat');

            jOptionSets.find('.selected').removeClass('selected');
            $this.addClass('selected');

            if (currentOption !== '*') {
                currentOption = '.' + currentOption;
            }

            jIsoContainer.isotope({filter : currentOption});

            return false;
        });
    };
};

/* slick slider
================================================== */
function _slickSlider ()
{
    var slider = $('.js-slick');

    if ( slider.length > 0 )
    {
        slider.each(function (index) {
            var _this = $(this);

            _this.on('init', function(event, slick){

            }).slick({
                autoplay: true,
                autoplaySpeed: 3000,
                adaptiveHeight: true,
                dots: true,
                arrows: false,
                speed: 800,
                slidesToShow: 1,
                slidesToScroll: 1,
                prevArrow: '<i class="fontello-left-open slick-prev"></i>',
                nextArrow: '<i class="fontello-right-open slick-next"></i>'
            });
        });
    };
};

/* slick slider
================================================== */
function _relatedSlider ()
{
    var slider = $('.related-slider .related .products');
    var rtl = $('body.rtl') ? true : false;

    if ( slider.length > 0 )
    {
        slider.slick({
            autoplay: true,
            autoplaySpeed: 3000,
            adaptiveHeight: true,
            dots: true,
            //rtl: rtl,
            arrows: true,
            speed: 800,
            slidesToShow: 3,
            slidesToScroll: 1,
            prevArrow: '<i class="fontello-left-open slick-prev"></i>',
            nextArrow: '<i class="fontello-right-open slick-next"></i>',
            responsive: [
              {
                breakpoint: 1024,
                settings: {
                  slidesToShow: 3,
                  slidesToScroll: 3,
                }
              },
              {
                breakpoint: 992,
                settings: {
                  slidesToShow: 2,
                  slidesToScroll: 2,
                }
              },
              {
                breakpoint: 768,
                settings: {
                  slidesToShow: 2,
                  slidesToScroll: 2
                }
              },
              {
                breakpoint: 480,
                settings: {
                  slidesToShow: 1,
                  slidesToScroll: 1
                }
              }
            ]
        });
    }
};

/* lightbox
================================================== */
function _fancybox ()
{
    var galleryElement = $("a[data-fancybox]");

    if ( galleryElement.length > 0 )
    {
        $("[data-fancybox]").fancybox({
            buttons : [
                'slideShow',
                'fullScreen',
                'thumbs',
                'close'
            ],
            loop : true,
            protect: true,
            wheel : false,
            transitionEffect : "tube"
        });
    }
};
/* Modal Content
================================================== */
function _modalContent ()
{
    var popupElement = $(".inline-popups");

    if ( popupElement.length > 0 )
    {
        // Inline popups
        popupElement.each(function () {
            $(this).magnificPopup({
                removalDelay: 500,
                callbacks: {
                    beforeOpen: function() {
                        this.st.mainClass = this.st.el.attr('data-effect');
                    }
                },
                midClick: true
            });
        });
    }
};

/* accordion
================================================== */
function _accordion ()
{
    var oAccordion = $('.accordion-container');

    if ( oAccordion.length > 0 ) {

        var oAccItem    = oAccordion.find('.accordion-item'),
        oAccTrigger = oAccordion.find('.accordion-toggler');

        oAccordion.each(function () {
            $(this).find('.accordion-item:eq(0)').addClass('active');
        });

        oAccTrigger.on('click', function (j) {
            j.preventDefault();

            var $this = $(this),
            parent = $this.parent(),
            dropDown = $this.next('article');

            parent.toggleClass('active').siblings(oAccItem).removeClass('active').find('article').not(dropDown).slideUp();

            dropDown.stop(false, true).slideToggle();

            return false;
        });
    };
};

/* tabs
================================================== */
function _tabs ()
{
    var oTab = $('.tab-container');

    if ( oTab.length > 0 ) {

        var oTabTrigger = oTab.find('nav a');

        oTab.each(function () {

            $(this)
            .find('nav a:eq(0)').addClass('active').end()
            .find('.tab-content__item:eq(0)').addClass('is-visible');
        });

        oTabTrigger.on('click', function (g) {
            g.preventDefault();

            var $this = $(this),
            index = $this.index(),
            parent = $this.closest('.tab-container');

            $this.addClass('active').siblings(oTabTrigger).removeClass('active');

            parent
            .find('.tab-content__item.is-visible').removeClass('is-visible').end()
            .find('.tab-content__item:eq(' + index + ')').addClass('is-visible');

            return false;
        });
    };
};

/* counters
================================================== */
function _counters ()
{
    var counter = $('.js-count');

    function _countInit() {
        counter.each(function() {
            var $this = $(this);

            if( $this.is_on_screen() && !$this.hasClass('animate') )
            {
                $this
                .addClass('animate')
                .countTo({
                    from: 0,
                    speed: 2000,
                    refreshInterval: 100,
                    formatter: function (value, options) {
                        if (counter.data('deci')== true) {
                            value = value.toFixed(options.decimals);
                            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            return value;
                        }
                        return value.toFixed(options.decimals);
                    }
                });
            };
        });
    };

    if ( counter.length > 0 )
    {
        _countInit();

        jWindow.on('scroll', function(e) {

            // _countInit();

            if( rAF ) {
                rAF(function(){
                    _countInit();
                });
            } else {
                _countInit();
            }
        });
    };
};

/* google map
================================================== */
function _g_map ()
{
    var maps = $('.g_map');

    if ( maps.length > 0 )
    {
        var apiKey = maps.attr('data-api-key'),
        apiURL;

        if (apiKey)
        {
            apiURL = 'https://maps.google.com/maps/api/js?key='+ apiKey +' &sensor=false';
        }
        else
        {
            apiURL = 'https://maps.google.com/maps/api/js?sensor=false';
        }

        $.getScript( apiURL , function( data, textStatus, jqxhr ) {

            maps.each(function() {
                var current_map = $(this),
                latlng = new google.maps.LatLng(current_map.attr('data-longitude'), current_map.attr('data-latitude')),
                point = current_map.attr('data-marker'),
                zoom = current_map.attr('data-zoom') ? parseFloat(current_map.attr('data-zoom') ) : 15,
                myOptions = {
                    zoom: zoom,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    mapTypeControl: false,
                    scrollwheel: false,
                    draggable: true,
                    panControl: false,
                    zoomControl: false,
                    disableDefaultUI: true
                },
                stylez = [
                    {
                        featureType: "all",
                        elementType: "all",
                        stylers: [
                            { saturation: -100 } // <-- THIS
                        ]
                    }
                ];

                var map = new google.maps.Map(current_map[0], myOptions);

                var mapType = new google.maps.StyledMapType(stylez, { name:"Grayscale" });
                map.mapTypes.set('Grayscale', mapType);
                map.setMapTypeId('Grayscale');

                var marker = new google.maps.Marker({
                    map: map,
                    icon: {
                        size: new google.maps.Size(59,69),
                        origin: new google.maps.Point(0,0),
                        anchor: new google.maps.Point(0,69),
                        url: point
                    },
                    position: latlng
                });

                google.maps.event.addDomListener(window, "resize", function() {
                    var center = map.getCenter();
                    google.maps.event.trigger(map, "resize");
                    map.setCenter(center);
                });
            });
        });
    };
};



/* scrollTo
================================================== */
function _scrollTo ()
{

    $(document).on("scroll", onScroll);
    var winw = $(window).width();
    if(winw > 992){
        var topMenuHeight = $('#top-bar__navigation').outerHeight()+70;
        var refElementpos = topMenuHeight + 20;
    } else {
        var topMenuHeight = $('#top-bar__navigation').outerHeight()+1;
        var refElementpos = topMenuHeight + 20;
    }

    //smoothscroll
    $('a[href^="#"]').click(function(){
        var target = $(this).attr('href');
        $('html, body').animate({scrollTop: $(target).offset().top-topMenuHeight}, 500);
        return false;

        $("#top-bar__navigation ul li a").click(function () {
            $("#top-bar__navigation ul li a").parent().removeClass('active');
        });
        $(this).addClass('active');

        var target = this.hash,
        menu = target;
        $target = $(target);
        $('html, body').stop().animate({
            'scrollTop': $(target).offset().top-120}, 1000, 'swing', function () {
                window.location.hash = target;
                $(document).on("scroll", onScroll);
            });
        });

        function onScroll(event){
            var scrollPos = $(document).scrollTop();
            $('#top-bar__navigation ul li a').each(function () {
                var currLink = $(this);
                var refElement = $(currLink.attr("href"));
                if (refElement.position().top-refElementpos <= scrollPos && refElement.position().top-topMenuHeight + refElement.height() > scrollPos) {
                    $('#top-bar__navigation ul li a').parent().removeClass("active");
                    currLink.parent().addClass("active");
                }
                else{
                    currLink.parent().removeClass("active");
                }
            });
        }

    };


    // SmoothScroll
    var scroll = new SmoothScroll('#top-bar a[href*="#"]', {

        // Selectors
        ignore: '[data-vc-accordion]', // Selector for links to ignore (must be a valid CSS selector)
        header: '#top-bar.topbar-fixed', // Selector for fixed headers (must be a valid CSS selector)
        topOnEmptyHash: true, // Scroll to the top of the page for links with href="#"
        // Speed & Easing
        speed: 1000, // Integer. How fast to complete the scroll in milliseconds
        clip: true, // If true, adjust scroll distance to prevent abrupt stops near the bottom of the page
        easing: 'easeInOutCubic', // Easing pattern to use
        customEasing: function (time) {

            // Function. Custom easing pattern
            return time < 0.5 ? 2 * time * time : -1 + (4 - 2 * time) * time;

        },
        offset: function (anchor,toggle) {
            var offsett = $('body').hasClass('admin-bar') ? -32 : 0;
            if ($('#top-bar__navigation-toggler').hasClass('is-active')) {
                $('#top-bar__navigation-toggler').removeClass('is-active');
                $('#top-bar').removeClass('is-expanded');
                if ( window.innerWidth < 992 ){
                    nHtmlNode.style.overflow = '';
                }

                return offsett;
            } else {
                if ( window.innerWidth < 992 ){
                    nHtmlNode.style.overflow = 'hidden';
                }

                return offsett;
            }
        },
        // History
        updateURL: true, // Update the URL on scroll
        popstate: true, // Animate scrolling with the forward/backward browser buttons (requires updateURL to be true)

        // Custom Events
        emitEvents: true // Emit custom events

    });
    // Log scroll events
    var logScrollEvent = function (event) {

        // The anchor link that triggered the scroll
        $('#top-bar__navigation ul li').removeClass('active');
        $(event.detail.toggle).parent().addClass('active');

    };
    // Listen for scroll events
    document.addEventListener('scrollStart', logScrollEvent, false);


    /* scroll to top
    ================================================== */
    function _scrollTop ()
    {
        var	nBtnToTopWrap = document.getElementById('btn-to-top-wrap'),
        jBtnToTopWrap = $(nBtnToTopWrap);

        if ( jBtnToTopWrap.length > 0 )
        {
            var nBtnToTop = document.getElementById('btn-to-top'),
            jBtnToTop = $(nBtnToTop);

            jBtnToTop.on('click', function (e) {
                e.preventDefault();

                $('body,html').stop().animate({ scrollTop: 0 } , 1500);

                return false;
            });

            jWindow.on('scroll', function(e) {

                if ( jWindow.scrollTop() > jBtnToTop.data('visible-offset') )
                {
                    if ( jBtnToTopWrap.is(":hidden") )
                    {
                        jBtnToTopWrap.fadeIn();
                    };

                }
                else
                {
                    if ( jBtnToTopWrap.is(":visible") )
                    {
                        jBtnToTopWrap.fadeOut();
                    };
                };
            });
        };
    };

    if ($('.topbar-fixed').size()) {

        var $nav = $('.topbar-fixed');
        var $navTop = $nav.offset().top;
        var pegarNav = function () {
            var $scrollTop = $(window).scrollTop();
            if ($scrollTop <= $navTop) {
                $nav.removeClass('fixed')
            } else {
                $nav.addClass('fixed')
            }
        };

        $(window).on('scroll',pegarNav);
    }

    /* scroll to top
    ================================================== */
    function _mobileScroll ()
    {
        $("#top-bar.onepage-menu ul li a").click(function () {
            $("#top-bar").removeClass('is-expanded');
        });
    };



    $(document).ready(function() {

        /* header
        ================================================== */
        _header();

        /* parallax
        ================================================== */
        _parallax();

        /* isotope sorting
        ================================================== */
        _gallery_sorting();

        /* slick slider
        ================================================== */
        _slickSlider();

        /* product related Slider
        ================================================== */
        _relatedSlider();

        /* lightbox
        ================================================== */
        _fancybox();

        /* modal Content
        ================================================== */
        _modalContent();

        /* counters
        ================================================== */
        _counters();

        /* scrollTo
        ================================================== */
        //_scrollTo();

        /* scroll to top
        ================================================== */
        _scrollTop();

        /* mobile menu toggle
        ================================================== */
        _mobileScroll();


    });

    jWindow.on('load', function () {

        var jMasonry = $('.js-masonry');

        if ( jMasonry.length > 0 )
        {
            jMasonry.masonry('layout')
        }
        /* scroll animate
        ================================================== */
        AOS.init({
            offset: 120,
            delay: 100,
            duration: 450, // or 200, 250, 300, 350.....
            easing: 'ease-in-out-quad',
            once: true,
            disable: 'mobile'
        });
        /* google map
        ================================================== */
        _g_map();
    });

    $.fn.is_on_screen = function () {
        var viewport = {
            top: jWindow.scrollTop(),
            left: jWindow.scrollLeft()
        };
        viewport.right = viewport.left + jWindow.width();
        viewport.bottom = viewport.top + jWindow.height();

        var bounds = this.offset();
        bounds.right = bounds.left + this.outerWidth();
        bounds.bottom = bounds.top + this.outerHeight();

        return ( !( viewport.right < bounds.left ||
            viewport.left > bounds.right ||
            viewport.bottom < bounds.top ||
            viewport.top > bounds.bottom
        ));
    };

    /* smartresize
    ================================================== */
    (function($,sr){

        // debouncing function from John Hann
        // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
        var debounce = function (func, threshold, execAsap) {
            var timeout;

            return function debounced () {
                var obj = this, args = arguments;
                function delayed () {
                    if (!execAsap)
                    func.apply(obj, args);
                    timeout = null;
                };

                if (timeout)
                clearTimeout(timeout);
                else if (execAsap)
                func.apply(obj, args);

                timeout = setTimeout(delayed, threshold || 100);
            };
        }
        // smartresize
        jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };
    })(jQuery,'smartresize');

    function ntrHeader() {
        var myHeader = $('[data-ntr-header]');
        if (myHeader.length) {
            var myHeaderSearch = $('.header_search', myHeader);
            var myHeaderSearchForm = $('.header_search_form', myHeader);
            var myHeaderSearchInput = $('.header_search_input', myHeader);
            var myHeaderSearchOpen = $('.header_search_open', myHeader);
            var myHeaderSearchClose = $('.header_search_close', myHeader);

            var myHeaderHandlers = {
                searchOpen: function() {
                    myHeaderSearch.addClass('is-active');
                    myHeaderSearchInput.focus();
                    $(document).on('click.ntrHeaderSearch', function(e) {
                        if (!$(e.target).closest(myHeaderSearchOpen).length) {
                            if (!$(e.target).closest(myHeaderSearch).length) {
                                myHeaderHandlers.searchClose();
                            }
                        }
                    });
                    $(document).on('keyup.ntrHeaderSearch', function(e) {
                        if (e.keyCode === 27) {
                            myHeaderHandlers.searchClose();
                        }
                    });
                },
                searchClose: function() {
                    myHeaderSearch.removeClass('is-active');
                    myHeaderSearchForm[0].reset();
                    $(document).off('click.ntrHeaderSearch');
                    $(document).off('keyup.ntrHeaderSearch');
                },
            }
            // Handlers
            myHeaderSearchOpen.on('click', function(e) {
                e.preventDefault();
                if (myHeaderSearch.hasClass('is-active')) {
                    myHeaderHandlers.searchClose();
                } else {
                    myHeaderHandlers.searchOpen();
                }
            });
            myHeaderSearchClose.on('click', function(e) {
                e.preventDefault();
                myHeaderHandlers.searchClose();
            });

        }
    }
    ntrHeader();
}(jQuery));
