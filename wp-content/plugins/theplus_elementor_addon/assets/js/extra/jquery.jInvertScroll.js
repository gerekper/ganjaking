/* jInvertScroll 0.8.3 custom*/(function($) {
    'use strict';
    $.jInvertScroll = function(sel, options) {
        var defaults = {
            width: 'auto',
            height: 'auto',
            onScroll: function(percent) {},
			/*added*/
			 footer_height: 0,
        };
        var config = $.extend(defaults, options);
        if (typeof sel === 'Object' && sel.length > 0) {
            return
        }
        var elements = [],
            longest = 0,
            whole_height, window_height, window_width;

        function init() {
            $.each(sel, function(i, val) {
                $(val).each(function(e) {
                    elements.push($(this));
                    var w = $(this).width();
                    if (longest < w) {
                        longest = w
                    }
                })
            });
            if (config.width == 'auto') {
                config.width = longest
            }
            if (config.height == 'auto') {
                config.height = longest
            }
            $('body').css('height', config.height + 'px')
        }

        function calc() {
            whole_height = $(document).height();
            window_height = $(window).height();
			/*added*/
            window_width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth
        }

        function onscroll(e) {
            var current_position_y = $(this).scrollTop();
            calc();
            var difference = whole_height - window_height;
            var scroll_percent = 0;
            if (difference != 0) {
                scroll_percent = (current_position_y / difference).toFixed(4)
            }
			/*added*/
			var hscroll_calc = whole_height - config.footer_height - window_height;
			var hscroll_percent = 0;
			if (hscroll_calc != 0) {
               hscroll_percent = (current_position_y / hscroll_calc).toFixed(4)
           }
		   
            if (typeof config.onScroll === 'function') {
                config.onScroll.call(this, scroll_percent,hscroll_percent)
            }
            $.each(elements, function(i, el) {
                var check_width = el.width() - window_width;
                /*added*/
				if (check_width < 0) {
                    check_width = 0
                }
				if (hscroll_percent <= 1) {
					var pos = Math.floor(check_width * hscroll_percent) * -1;
					el.css('left', pos);
					 el.css('top', 0)
				 } else {
					var top_move = (hscroll_percent - 1.0) * 100 * config.footer_height / ((difference / hscroll_calc - 1.0) * 100);
					el.css('left', Math.floor(check_width) * -1);
					el.css('top', -top_move)
				}                
            })
        }

        function setlisteners() {
            $(window).on('scroll resize', onscroll);
            $([document, window]).on('ready resize', calc)
        }
        init();
        setlisteners();
        return {
            reinitialize: function() {
                init();
                setlisteners()
            },
            destroy: function() {
                $('body').attr('style', '');
                $(window).off('scroll resize', onscroll);
                $([document, window]).off('ready resize', calc)
            }
        }
    }
}(jQuery));