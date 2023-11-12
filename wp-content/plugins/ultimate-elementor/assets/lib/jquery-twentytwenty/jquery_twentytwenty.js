/*!
 * Twenty Twenty JS.
 * https://github.com/zurb/twentytwenty
 *
 * Copyright 2018 zurb
 * Released under the ISC License
 * https://github.com/zurb/twentytwenty/blob/master/LICENSE.md
 */

(function($){

  $.fn.twentytwenty = function(options) {
    var options = $.extend({default_offset_pct: 0.5, orientation: 'horizontal', move_on_hover: false}, options);
    return this.each(function() {

      var sliderPct = options.default_offset_pct;
      var container = $(this);
      var sliderOrientation = options.orientation;
      var beforeDirection = (sliderOrientation === 'vertical') ? 'down' : 'left';
      var afterDirection = (sliderOrientation === 'vertical') ? 'up' : 'right';
      var moveOnHover = Boolean( options.move_on_hover );

      var beforeImg = container.find("img:first");
      var afterImg = container.find("img:last");
      var slider = container.find(".twentytwenty-handle");
      var overlay = container.find(".twentytwenty-overlay");

      if( !$(this).hasClass('twentytwenty-container') ) {

        container.wrap("<div class='twentytwenty-wrapper twentytwenty-" + sliderOrientation + "'></div>");
        container.append("<div class='twentytwenty-overlay'></div>");
        beforeImg = container.find("img:first");
        afterImg = container.find("img:last");
        container.append("<div class='twentytwenty-handle'></div>");
        slider = container.find(".twentytwenty-handle");
        slider.append("<span class='twentytwenty-" + beforeDirection + "-arrow'></span>");
        slider.append("<span class='twentytwenty-" + afterDirection + "-arrow'></span>");
        container.addClass("twentytwenty-container");
        beforeImg.addClass("twentytwenty-before");
        afterImg.addClass("twentytwenty-after");
        
        overlay = container.find(".twentytwenty-overlay");
        overlay.append("<div class='twentytwenty-before-label'></div>");
        overlay.append("<div class='twentytwenty-after-label'></div>");   

      }

      var calcOffset = function(dimensionPct) {
        var w = beforeImg.width();
        var h = beforeImg.height();
        return {
          w: w+"px",
          h: h+"px",
          cw: (dimensionPct*w)+"px",
          ch: (dimensionPct*h)+"px"
        };
      };

      var adjustContainer = function(offset) {
        if (sliderOrientation === 'vertical') {
          beforeImg.css("clip", "rect(0,"+offset.w+","+offset.ch+",0)");
        }
        else {
          beforeImg.css("clip", "rect(0,"+offset.cw+","+offset.h+",0)");
      }
        container.css("height", offset.h);
      };

      var adjustSlider = function(pct) {
        var offset = calcOffset(pct);
        slider.css((sliderOrientation==="vertical") ? "top" : "left", (sliderOrientation==="vertical") ? offset.ch : offset.cw);
        adjustContainer(offset);
      }

      $(window).on("resize.twentytwenty", function(e) {
        adjustSlider(sliderPct);
      });

      var offsetX = 0;
      var imgWidth = 0;
      
      slider.on("movestart", function(e) {
        if (((e.distX > e.distY && e.distX < -e.distY) || (e.distX < e.distY && e.distX > -e.distY)) && sliderOrientation !== 'vertical') {
          e.preventDefault();
        }
        else if (((e.distX < e.distY && e.distX < -e.distY) || (e.distX > e.distY && e.distX > -e.distY)) && sliderOrientation === 'vertical') {
          e.preventDefault();
        }
        container.addClass("active");
        offsetX = container.offset().left;
        offsetY = container.offset().top;
        imgWidth = beforeImg.width(); 
        imgHeight = beforeImg.height();          
      });

      slider.on("moveend", function(e) {
        if ( ! moveOnHover )
          container.removeClass("active");
      });

      slider.on("move", function(e) {
        if (container.hasClass("active")) {
          sliderPct = (sliderOrientation === 'vertical') ? (e.pageY-offsetY)/imgHeight : (e.pageX-offsetX)/imgWidth;
          if (sliderPct < 0) {
            sliderPct = 0;
          }
          if (sliderPct > 1) {
            sliderPct = 1;
          }
          adjustSlider(sliderPct);
        }
      });

      container.find("img").on("mousedown", function(event) {
        event.preventDefault();
      });

      if ( moveOnHover ) {

        container.on("mouseenter", function(event) {
          container.addClass("active");
          offsetX = container.offset().left;
          offsetY = container.offset().top;
          imgWidth = beforeImg.width(); 
          imgHeight = beforeImg.height();
        });

        container.on("mouseleave", function(event) {
          container.removeClass("active");
        });

        container.on("mousemove", function(event) {
          if (container.hasClass("active")) {
           sliderPct = (sliderOrientation === 'vertical') ? (event.pageY-offsetY)/imgHeight : (event.pageX-offsetX)/imgWidth;
            if (sliderPct < 0) {
              sliderPct = 0;
            }
            if (sliderPct > 1) {
              sliderPct = 1;
            }
            adjustSlider(sliderPct);
          }
        });
      }

      $(window).trigger("resize.twentytwenty");
    });
  };

})(jQuery);
