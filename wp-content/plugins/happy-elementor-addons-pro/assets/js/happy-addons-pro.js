"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

window.Happy = window.Happy || {};

(function ($, Happy, w) {
  var $window = $(w);
  $(function () {
    var $container = $('.ha-fsb-container');

    if ($container.length > 0) {
      if (window.history && window.history.pushState) {
        ha_manage_fsb();
      }

      ;
      $(document.body).on("added_to_cart", function (event, fragments, cart_hash, $button) {
        ha_manage_fsb();
      });
      $(document.body).on("removed_from_cart", function (event, fragments, cart_hash, $button) {
        ha_manage_fsb();
      });
    }

    function ha_manage_fsb() {
      $.ajax({
        url: HappyProLocalize.ajax_url,
        type: "POST",
        data: {
          action: "ha_get_cart_subtotal_action",
          security: HappyProLocalize.nonce
        },
        success: function success(response) {
          var $data = JSON.parse(response);
          var $subTotalAmount = $data.subTotalAmount;

          if ($data.status == 'true' && $subTotalAmount >= 0) {
            var $container = $('.ha-fsb-container');
            var $elem = $container.find('.ha-fsb-size');
            var $settings = $container.data('fsb_settings');

            if ($settings != undefined) {
              var $aimationSpeed = $settings.hasOwnProperty('ha_fsb_animation_speed') ? $settings.ha_fsb_animation_speed : 15;
              var $progressType = $settings.hasOwnProperty('progress_type') ? $settings.progress_type : 'percent';
              var $currencySymbol = $settings.hasOwnProperty('currencySymbol') && $progressType == 'percent' ? '%' : $settings.currencySymbol;
              var $targetAmount = $settings.hasOwnProperty('target_amount') ? $settings.target_amount : 0;
              var $totalPercent = 0;

              if (parseFloat($subTotalAmount) >= parseFloat($targetAmount)) {
                $totalPercent = 100;
              } else {
                if (parseFloat($targetAmount) == 0) {
                  $totalPercent = 0;
                } else {
                  $totalPercent = parseFloat($subTotalAmount) * 100 / parseFloat($targetAmount);
                }
              }

              $totalPercent = parseFloat(Math.round($totalPercent)) >= 100 ? 100 : parseFloat(Math.round($totalPercent));
              var $progressText = $progressType == 'percent' ? $totalPercent : $subTotalAmount;
              var defaultWidth = $(window).width() <= 768 ? 12 : 5;

              if ($totalPercent > 0) {
                $elem.text($progressText + $currencySymbol);
                $elem.css({
                  'width': $totalPercent + "%",
                  'transition': 'width ' + $aimationSpeed + 'ms ease-in-out'
                });
              }

              fsbMessageControl($totalPercent);

              if ($progressText <= 0) {
                $elem.css('width', +defaultWidth + "%");
                $elem.text('0' + $currencySymbol);
              }
            }
          }
        },
        error: function error(_error) {}
      });
    }

    ;

    function fsbMessageControl() {
      var totalPercent = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var fsbContainer = $('.ha-fsb-container');
      var settings = fsbContainer.data('fsb_settings');

      if (fsbContainer && settings) {
        if (totalPercent >= 100) {
          $('.ha-fsb-inner-content').html(settings.fsb_success_message);
        } else {
          $('.ha-fsb-inner-content').html(settings.announcement);
        }
      }
    }

    ;

    function log(event, item, level) {
      $(document).on(event, item, level);
    }

    var e;
    e = $(".ha-menu-container");
    $(e).each(function () {
      var button = $(this);

      if ("yes" != button.attr("ha-dom-added")) {
        if (0 === button.parents(".elementor-widget-ha-nav-menu").length) {
          button.parents(".ha-wid-con").addClass("ha_menu_responsive_tablet");
        }

        button.attr("ha-dom-added", "yes");
      }
    });
    $(".ha-dropdown-has > a").on("click", function (event) {
      if (!$(this).parents(".ha-navbar-nav, .ha-vertical-navbar-nav").hasClass("submenu-click-on-icon") || $(event.target).hasClass("ha-submenu-indicator-wrap")) {
        event.preventDefault();
        var $oElemDragged = $(this).parent().find(">.ha-dropdown, >.ha-megamenu-panel");
        $oElemDragged.find(".ha-dropdown-open").removeClass("ha-dropdown-open");

        if ($oElemDragged.hasClass("ha-dropdown-open")) {
          $oElemDragged.removeClass("ha-dropdown-open");
        } else {
          $oElemDragged.addClass("ha-dropdown-open");
        }
      }
    }); // $(".ha-menu-toggler").on("click", function(event) {
    //     event.preventDefault();
    //     var el_form_group = $(this).parents(".ha-menu-container").parent();
    //     if (el_form_group.length < 1) {
    //         el_form_group = $(this).parent();
    //     }
    //     var $wrapElement = el_form_group.find(".ha-menu-offcanvas-elements");
    //     if ($wrapElement.hasClass("active")) {
    //         $wrapElement.removeClass("active");
    //     } else {
    //         $wrapElement.addClass("active");
    //     }
    // });

    $(".ha-navbar-nav li a").on("click", function (event) {
      if (!$(this).attr("href") && "ha-submenu-indicator-wrap" == event.target.className) {
        var thirdItem = $(this);
        var lnk = thirdItem.get(0);
        var oldUrl = lnk.href;
        var sepor = oldUrl.indexOf("#");
        var s = thirdItem.parents(".ha-menu-container").hasClass("ha-nav-menu-one-page-yes");

        if (-1 !== sepor && oldUrl.length > 1 && s && lnk.pathname == window.location.pathname) {
          event.preventDefault();
          thirdItem.parents(".ha-wid-con").find(".ha-menu-close").trigger("click");
        }
      }
    });
  });

  function debounce(func, wait, immediate) {
    var timeout;
    return function () {
      var context = this,
          args = arguments;

      var later = function later() {
        timeout = null;
        if (!immediate) func.apply(context, args);
      };

      var callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if (callNow) func.apply(context, args);
    };
  }

  $window.on("elementor/frontend/init", function () {
    if (typeof haDisplayCondition != "undefined" && haDisplayCondition.status == "true") {
      // Set user time in cookie
      var HappyLocalTimeZone = new Date().toString().match(/([A-Z]+[\+-][0-9]+.*)/)[1];
      var ha_secure = document.location.protocol === "https:" ? "secure" : "";
      document.cookie = "HappyLocalTimeZone=" + HappyLocalTimeZone + ";SameSite=Strict;" + ha_secure;
    } else {
      var now = new Date();
      now.setTime(now.getTime() - 1000 * 3600);
      document.cookie = "HappyLocalTimeZone=;expires=" + now.toUTCString() + ";";
    }

    var CountDown = function CountDown($scope) {
      var $item = $scope.find(".ha-countdown");
      var $countdown_item = $item.find(".ha-countdown-item");
      var $end_action = $item.data("end-action");
      var $redirect_link = $item.data("redirect-link");
      var $end_action_div = $item.find(".ha-countdown-end-action");
      var $editor_mode_on = $scope.hasClass("elementor-element-edit-mode");
      $item.countdown({
        end: function end() {
          if (("message" === $end_action || "img" === $end_action) && $end_action_div !== undefined) {
            $countdown_item.css("display", "none");
            $end_action_div.css("display", "block");
          } else if ("url" === $end_action && $redirect_link !== undefined && $editor_mode_on !== true) {
            window.location.replace($redirect_link);
          }
        }
      });
    };

    var SliderBase = elementorModules.frontend.handlers.Base.extend({
      onInit: function onInit() {
        elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
        this.run();
      },
      getDefaultSettings: function getDefaultSettings() {
        return {
          selectors: {
            container: ".ha-slider-container"
          }
        };
      },
      getDefaultElements: function getDefaultElements() {
        var selectors = this.getSettings("selectors");
        return {
          $container: this.$element.find(selectors.container)
        };
      },
      getReadySettings: function getReadySettings() {
        var $this = this;
        var slidesPerView = parseInt(this.getElementSettings("slides_per_view")) || 1;

        if (this.getElementSettings("thumbs_navigation") == "yes") {
          var selectorThumbs = this.elements.$container.find(".ha-slider-gallery-thumbs");
          var haGallaryThumbs = new HaSwiper(selectorThumbs[0], {
            spaceBetween: this.getElementSettings("space_between_thumbs"),
            freeMode: true,
            watchSlidesVisibility: true,
            watchSlidesProgress: true
          });
        } // TODO: Need to fix that in future. Currently, Disabled


        var tempEffect = this.getElementSettings("effect");

        if (tempEffect == 'cube') {
          tempEffect = false;
        }

        var settings = {
          direction: this.getElementSettings("slider_direction"),
          slidesPerView: slidesPerView || 1,
          spaceBetween: parseInt(this.getElementSettings("space_between_slides")) || 0,
          loop: !!(this.getElementSettings("infinity_loop") || false),
          speed: parseInt(this.getElementSettings("effect_speed")),
          effect: this.getElementSettings("slider_type") == "multiple" ? this.getElementSettings("effect_multiple") : tempEffect,
          ha_animation: this.getElementSettings("slider_content_animation"),
          sliderType: this.getElementSettings("slider_type")
        };

        if (this.getElementSettings("effect") == "flip") {
          settings.flipEffect = {
            limitRotation: true,
            slideShadows: true
          };
        } // TODO: Need to fix that in future. Currently, Disabled
        // if (this.getElementSettings("effect") == "cube") {
        //     settings.cubeEffect = {
        //         shadow: true,
        //         slideShadows: true,
        //         shadowOffset: 20,
        //         shadowScale: 0.94,
        //     };
        // }


        if (this.getElementSettings("effect_multiple") == "coverflow") {
          settings.coverflowEffect = {
            rotate: 50,
            stretch: 0,
            depth: 100,
            modifier: 1,
            slideShadows: true
          };
        }

        if (this.getElementSettings("slider_type") == "multiple") {
          var bpObj = {
            desktop: {
              slidesPerView: slidesPerView || 1,
              spaceBetween: parseInt(this.getElementSettings("space_between_slides")) || 0
            }
          };
          var breakpoints = elementorFrontend.config.responsive.breakpoints;
          $.each(breakpoints, function (idx, val) {
            if (val.is_enabled) {
              bpObj[idx] = {
                screenSize: val.value,
                slidesPerView: parseInt($this.getElementSettings("slides_per_view_" + idx)) || slidesPerView,
                spaceBetween: parseInt($this.getElementSettings("space_between_slides_" + idx)) || 0
              };
            }
          });
          settings.customBreakpoints = bpObj;
          settings.customMultiple = true;
        }

        if (this.getElementSettings("autoplay") == "yes") {
          settings.autoplay = {
            delay: this.getElementSettings("autoplay_speed"),
            disableOnInteraction: false,
            stopOnLastSlide: !(this.getElementSettings("infinity_loop") || false)
          };
        }

        if (this.getElementSettings("arrow_navigation") == "yes") {
          var selectorNext = this.elements.$container.find(".ha-slider-next");
          var selectorPrev = this.elements.$container.find(".ha-slider-prev");
          settings.navigation = {
            nextEl: selectorNext[0],
            prevEl: selectorPrev[0]
          };
        }

        if (this.getElementSettings("pagination_type") == "dots") {
          var selectorPagi = this.elements.$container.find(".ha-slider-pagination");
          settings.pagination = {
            el: selectorPagi[0],
            clickable: true
          };
        }

        if (this.getElementSettings("pagination_type") == "progressbar") {
          var selectorPagi = this.elements.$container.find(".ha-slider-pagination");
          settings.pagination = {
            el: selectorPagi[0],
            clickable: true,
            type: "progressbar"
          };
        }

        if (this.getElementSettings("pagination_type") == "numbers") {
          var selectorPagi = this.elements.$container.find(".ha-slider-pagination");
          settings.pagination = {
            el: selectorPagi[0],
            clickable: true,
            type: this.getElementSettings("number_pagination_type"),
            renderBullet: function renderBullet(index, className) {
              return '<span class="' + className + '">' + (index + 1) + "</span>";
            },
            renderFraction: function renderFraction(currentClass, totalClass) {
              return '<span class="' + currentClass + '"></span>' + "<span>/</span>" + '<span class="' + totalClass + '"></span>';
            }
          };
        }

        if (this.getElementSettings("scroll_bar") == "yes") {
          var selectorScroll = this.elements.$container.find(".ha-slider-scrollbar");
          settings.scrollbar = {
            el: selectorScroll[0],
            hide: this.getElementSettings("scroll_bar_visibility") == "true",
            draggable: true
          };
        }

        if (this.getElementSettings("thumbs_navigation") == "yes") {
          settings.thumbs = {
            swiper: haGallaryThumbs
          };
        }

        return $.extend({}, settings);
      },
      run: function run() {
        var elContainer = this.elements.$container;
        var slider = elContainer.find(".ha-slider-container");
        var readySettings = this.getReadySettings();
        var sliderObj = new HaSwiper(slider[0], readySettings);

        if (readySettings.customMultiple) {
          $(window).on('resize', function () {
            var mode = $('body').attr('data-elementor-device-mode');
            sliderObj.params.slidesPerView = readySettings.customBreakpoints[mode].slidesPerView;
            sliderObj.params.spaceBetween = readySettings.customBreakpoints[mode].spaceBetween;
            sliderObj.update();
          }).resize();
        }

        sliderObj.on("slideChange", function () {
          if (readySettings.sliderType == "multiple") {
            return;
          }

          var aI = sliderObj.activeIndex;
          var elSlide = elContainer.find(".ha-slider-slide");
          var elSlideContent = elContainer.find(".ha-slider-content");
          var currentSlide = elSlideContent.eq(aI);
          currentSlide.hide();

          if (currentSlide.length <= 0) {}

          setTimeout(function () {
            currentSlide.show();
          }, readySettings.speed);
          elSlide.eq(aI).find(".elementor-invisible, .animated").each(function (e, t) {
            var i = $(this).data("settings");

            if (i && (i._animation || i.animation)) {
              var n = i._animation_delay ? i._animation_delay : 0,
                  a = i._animation || i.animation;
              $(this).removeClass("elementor-invisible");
              $(this).addClass(a + " animated");
            }
          });
        });
        sliderObj.on("transitionEnd", function () {
          var aI = sliderObj.activeIndex;
          var elSlide = elContainer.find(".ha-slider-slide");
          var elSlideContent = elContainer.find(".ha-slider-content");
          var currentSlide = elSlideContent.eq(aI);
          setTimeout(function () {
            elSlide.eq(aI).find(".animated").each(function (e, t) {
              var i = $(this).data("settings");

              if (i && (i._animation || i.animation)) {
                var n = i._animation_delay ? i._animation_delay : 0,
                    a = i._animation || i.animation;
                $(this).removeClass(a);
              }
            });
          }, readySettings.speed);
        });
      }
    });
    var CarouselBase = elementorModules.frontend.handlers.Base.extend({
      onInit: function onInit() {
        elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
        this.run();
      },
      getDefaultSettings: function getDefaultSettings() {
        return {
          selectors: {
            container: ".ha-carousel-container"
          },
          arrows: false,
          dots: false,
          checkVisible: false,
          infinite: true,
          slidesToShow: 3,
          rows: 0,
          prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-chevron-left"></i></button>',
          nextArrow: '<button type="button" class="slick-next"><i class="fa fa-chevron-right"></i></button>'
        };
      },
      getDefaultElements: function getDefaultElements() {
        var selectors = this.getSettings("selectors");
        return {
          $container: this.findElement(selectors.container)
        };
      },
      onElementChange: function onElementChange() {
        this.elements.$container.slick("unslick");
        this.run();
      },
      getReadySettings: function getReadySettings() {
        var settings = {
          infinite: !!this.getElementSettings("loop"),
          autoplay: !!this.getElementSettings("autoplay"),
          autoplaySpeed: this.getElementSettings("autoplay_speed"),
          speed: this.getElementSettings("animation_speed"),
          centerMode: !!this.getElementSettings("center"),
          vertical: !!this.getElementSettings("vertical"),
          slidesToScroll: 1
        };

        switch (this.getElementSettings("navigation")) {
          case "arrow":
            settings.arrows = true;
            break;

          case "dots":
            settings.dots = true;
            break;

          case "both":
            settings.arrows = true;
            settings.dots = true;
            break;
        }

        settings.slidesToShow = parseInt(this.getElementSettings("slides_to_show")) || 1;
        settings.responsive = [{
          breakpoint: elementorFrontend.config.breakpoints.lg,
          settings: {
            slidesToShow: parseInt(this.getElementSettings("slides_to_show_tablet")) || settings.slidesToShow
          }
        }, {
          breakpoint: elementorFrontend.config.breakpoints.md,
          settings: {
            slidesToShow: parseInt(this.getElementSettings("slides_to_show_mobile")) || parseInt(this.getElementSettings("slides_to_show_tablet")) || settings.slidesToShow
          }
        }];
        return $.extend({}, this.getSettings(), settings);
      },
      run: function run() {
        this.elements.$container.slick(this.getReadySettings());
      }
    }); // Source Code

    var SourceCode = function SourceCode($scope) {
      var $item = $scope.find('.ha-source-code');
      var $lng_type = $item.data('lng-type');
      var $after_copy_text = $item.data('after-copy');
      var $code = $item.find('code.language-' + $lng_type);
      var $copy_btn = $scope.find('.ha-copy-code-button');
      $copy_btn.on('click', function () {
        var $temp = $("<textarea>");
        $(this).append($temp);
        $temp.val($code.text()).select();
        document.execCommand("copy");
        $temp.remove();

        if ($after_copy_text.length) {
          $(this).text($after_copy_text);
        }
      });

      if ($lng_type !== undefined && $code !== undefined) {
        Prism.highlightElement($code.get(0));
      }
    }; // Animated text


    var AnimatedText = function AnimatedText($scope) {
      var $item = $scope.find(".cd-headline"),
          $animationDelay = $item.data("animation-delay");
      happyAnimatedText({
        selector: $item,
        animationDelay: $animationDelay
      });
    }; //Instagram Feed


    var InstagramFeed = function InstagramFeed($scope) {
      var button = $scope.find(".ha-insta-load-more");
      var instagram_wrap = $scope.find(".ha-insta-default");
      button.on("click", function (e) {
        e.preventDefault();
        var $self = $(this),
            query_settings = $self.data("settings"),
            total = $self.data("total"),
            items = $scope.find(".ha-insta-item").length;
        $.ajax({
          url: HappyProLocalize.ajax_url,
          type: "POST",
          data: {
            action: "ha_instagram_feed_action",
            security: HappyProLocalize.nonce,
            query_settings: query_settings,
            loaded_item: items
          },
          success: function success(response) {
            if (total > items) {
              $(response).appendTo(instagram_wrap);
            } else {
              $self.text("All Loaded").addClass("loaded");
              setTimeout(function () {
                $self.css({
                  display: "none"
                });
              }, 800);
            }
          },
          error: function error(_error2) {}
        });
      });
    }; //Scrolling Image


    var ScrollingImage = elementorModules.frontend.handlers.Base.extend({
      onInit: function onInit() {
        elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
        this.wrapper = this.$element.find(".ha-scrolling-image-wrapper");
        this.run();
      },
      onElementChange: function onElementChange() {
        this.run();
      },
      run: function run() {
        var container_height = this.wrapper.innerHeight(),
            container_width = this.wrapper.innerWidth(),
            image_align = this.wrapper.data("align"),
            scroll_direction = this.wrapper.data("scroll-direction"),
            items = this.wrapper.find(".ha-scrolling-image-container"),
            single_image_box = items.find(".ha-scrolling-image-item"),
            scroll = "scroll" + image_align + scroll_direction + parseInt(container_height) + parseInt(container_width),
            duration = this.wrapper.data("duration"),
            direction = "normal",
            horizontal_align_width = 10,
            vertical_align_height = 10;
        var start = {
          transform: "translateY(" + container_height + "px)"
        },
            end = {
          transform: "translateY(-101%)"
        };

        if ("bottom" === scroll_direction || "right" === scroll_direction) {
          direction = "reverse";
        }

        if ("ha-horizontal" === image_align) {
          start = {
            transform: "translateX(" + container_width + "px)"
          };
          end = {
            transform: "translateX(-101%)"
          };
          single_image_box.each(function () {
            horizontal_align_width += $(this).outerWidth(true);
          });
          items.css({
            width: horizontal_align_width,
            display: "flex"
          });
          items.find("img").css({
            "max-width": "100%"
          });
          single_image_box.css({
            display: "inline-flex"
          });
        }

        if ("ha-vertical" === image_align) {
          single_image_box.each(function () {
            vertical_align_height += $(this).outerHeight(true);
          });
        }

        $.keyframe.define([{
          name: scroll,
          "0%": start,
          "100%": end
        }]);
        items.playKeyframe({
          name: scroll,
          duration: duration.toString() + "ms",
          timingFunction: "linear",
          delay: "0s",
          iterationCount: "infinite",
          direction: direction,
          fillMode: "none",
          complete: function complete() {}
        });
      }
    }); //Pricing Table ToolTip

    var PricingTableToolTip = function PricingTableToolTip($scope) {
      var tooltip_area = $scope.find(".ha-pricing-table-feature-tooltip");
      tooltip_area.on({
        mouseenter: function mouseenter(e) {
          var $this = $(this),
              direction = $this[0].getBoundingClientRect(),
              tooltip = $this.find(".ha-pricing-table-feature-tooltip-text"),
              tooltipW = tooltip.outerWidth(true),
              tooltipH = tooltip.outerHeight(true),
              W_width = $(window).width(),
              W_height = $(window).height();
          tooltip.css({
            left: "0",
            right: "auto",
            top: "calc(100% + 1px)",
            bottom: "auto"
          });

          if (W_width - direction.left < tooltipW && direction.right < tooltipW) {
            tooltip.css({
              left: "calc(50% - (" + tooltipW + "px/2))"
            });
          } else if (W_width - direction.left < tooltipW) {
            tooltip.css({
              left: "auto",
              right: "0"
            });
          }

          if (W_height - direction.bottom < tooltipH) {
            tooltip.css({
              top: "auto",
              bottom: "calc(100% + 1px)"
            });
          }
        }
      });
    };

    var TabHandlerBase = elementorModules.frontend.handlers.Base.extend({
      $activeContent: null,
      getDefaultSettings: function getDefaultSettings() {
        return {
          selectors: {
            tabTitle: ".ha-tab__title",
            tabContent: ".ha-tab__content"
          },
          classes: {
            active: "ha-tab--active"
          },
          showTabFn: "show",
          hideTabFn: "hide",
          toggleSelf: false,
          hidePrevious: true,
          autoExpand: true
        };
      },
      getDefaultElements: function getDefaultElements() {
        var selectors = this.getSettings("selectors");
        return {
          $tabTitles: this.findElement(selectors.tabTitle),
          $tabContents: this.findElement(selectors.tabContent)
        };
      },
      activateDefaultTab: function activateDefaultTab() {
        var settings = this.getSettings();

        if (!settings.autoExpand || "editor" === settings.autoExpand && !this.isEdit) {
          return;
        }

        var defaultActiveTab = this.getEditSettings("activeItemIndex") || 1,
            originalToggleMethods = {
          showTabFn: settings.showTabFn,
          hideTabFn: settings.hideTabFn
        }; // Toggle tabs without animation to avoid jumping

        this.setSettings({
          showTabFn: "show",
          hideTabFn: "hide"
        });
        this.changeActiveTab(defaultActiveTab); // Return back original toggle effects

        this.setSettings(originalToggleMethods);
      },
      deactivateActiveTab: function deactivateActiveTab(tabIndex) {
        var settings = this.getSettings(),
            activeClass = settings.classes.active,
            activeFilter = tabIndex ? '[data-tab="' + tabIndex + '"]' : "." + activeClass,
            $activeTitle = this.elements.$tabTitles.filter(activeFilter),
            $activeContent = this.elements.$tabContents.filter(activeFilter);
        $activeTitle.add($activeContent).removeClass(activeClass);
        $activeContent[settings.hideTabFn]();
      },
      activateTab: function activateTab(tabIndex) {
        var settings = this.getSettings(),
            activeClass = settings.classes.active,
            $requestedTitle = this.elements.$tabTitles.filter('[data-tab="' + tabIndex + '"]'),
            $requestedContent = this.elements.$tabContents.filter('[data-tab="' + tabIndex + '"]');
        $requestedTitle.add($requestedContent).addClass(activeClass);
        $requestedContent[settings.showTabFn]();
      },
      isActiveTab: function isActiveTab(tabIndex) {
        return this.elements.$tabTitles.filter('[data-tab="' + tabIndex + '"]').hasClass(this.getSettings("classes.active"));
      },
      bindEvents: function bindEvents() {
        var _this = this;

        this.elements.$tabTitles.on({
          keydown: function keydown(event) {
            if ("Enter" === event.key) {
              if ('A' == event.currentTarget.tagName && event.currentTarget.classList.contains('ha-tab__title')) {
                //this conditional scope in only for advance tab widget
                return;
              }

              event.preventDefault();

              _this.changeActiveTab(event.currentTarget.getAttribute("data-tab"));
            }
          },
          click: function click(event) {
            if ('A' == event.currentTarget.tagName && event.currentTarget.classList.contains('ha-tab__title')) {
              //this conditional scope in only for advance tab widget
              return;
            }

            event.preventDefault();

            _this.changeActiveTab(event.currentTarget.getAttribute("data-tab"));
          }
        });
      },
      onInit: function onInit() {
        elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
        this.activateDefaultTab();
      },
      onEditSettingsChange: function onEditSettingsChange(propertyName) {
        if ("activeItemIndex" === propertyName) {
          this.activateDefaultTab();
        }
      },
      changeActiveTab: function changeActiveTab(tabIndex) {
        var isActiveTab = this.isActiveTab(tabIndex),
            settings = this.getSettings();

        if ((settings.toggleSelf || !isActiveTab) && settings.hidePrevious) {
          this.deactivateActiveTab();
        }

        if (!settings.hidePrevious && isActiveTab) {
          this.deactivateActiveTab(tabIndex);
        }

        if (!isActiveTab) {
          this.activateTab(tabIndex);
        }

        $(window).trigger("resize");
      }
    });

    var TimeLine = function TimeLine($scope) {
      var T_ID = $scope.data("id");
      var timeline = $scope.find(".ha-timeline-wrap");
      var dataScroll = timeline.data("scroll");
      var timeline_block = timeline.find(".ha-timeline-block");
      var event = "scroll.timelineScroll" + T_ID + " resize.timelineScroll" + T_ID;

      function scroll_tree() {
        timeline_block.each(function () {
          var block_height = $(this).outerHeight(true);
          var $offsetTop = $(this).offset().top;
          var window_middle_p = $window.scrollTop() + $window.height() / 2;

          if ($offsetTop < window_middle_p) {
            $(this).addClass("ha-timeline-scroll-tree");
          } else {
            $(this).removeClass("ha-timeline-scroll-tree");
          }

          var scroll_tree_wrap = $(this).find(".ha-timeline-tree-inner");
          var scroll_height = $window.scrollTop() - scroll_tree_wrap.offset().top + $window.outerHeight() / 2;

          if ($offsetTop < window_middle_p && timeline_block.hasClass("ha-timeline-scroll-tree")) {
            if (block_height > scroll_height) {
              scroll_tree_wrap.css({
                height: scroll_height * 1.5 + "px"
              });
            } else {
              scroll_tree_wrap.css({
                height: block_height * 1.2 + "px"
              });
            }
          } else {
            scroll_tree_wrap.css("height", "0px");
          }
        });
      }

      if ("yes" === dataScroll) {
        scroll_tree();
        $window.on(event, scroll_tree);
      } else {
        $window.off(event);
      }
    };

    var HotSpots = elementorModules.frontend.handlers.Base.extend({
      onInit: function onInit() {
        elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
        this.init();
      },
      bindEvents: function bindEvents() {
        if (!this.isEdit) {
          this.elements.$items.on("click.spotClick", function (e) {
            e.preventDefault();
          });
        }
      },
      getDefaultSettings: function getDefaultSettings() {
        return {
          selectors: {
            item: ".ha-hotspots__item"
          }
        };
      },
      getDefaultElements: function getDefaultElements() {
        return {
          $items: this.findElement(this.getSettings("selectors.item"))
        };
      },
      onElementChange: function onElementChange(changedProp) {
        if (!this.hasTipso()) {
          return;
        }

        if (changedProp.indexOf("tooltip_") === 0) {
          this.elements.$items.tipso("destroy");
          this.init();
        }
      },
      hasTipso: function hasTipso() {
        return $.fn["tipso"];
      },
      init: function init() {
        var position = this.getElementSettings("tooltip_position"),
            width = this.getElementSettings("tooltip_width"),
            background = this.getElementSettings("tooltip_bg_color"),
            color = this.getElementSettings("tooltip_color"),
            speed = this.getElementSettings("tooltip_speed"),
            delay = this.getElementSettings("tooltip_delay"),
            hideDelay = this.getElementSettings("tooltip_hide_delay"),
            hideArrow = this.getElementSettings("tooltip_hide_arrow"),
            hover = this.getElementSettings("tooltip_hover"),
            elementId = this.getID();

        if (!this.hasTipso()) {
          return;
        }

        this.elements.$items.each(function (index, item) {
          var $item = $(item),
              target = $item.data("target"),
              settings = $item.data("settings"),
              classes = ["ha-hotspots--" + elementId, "elementor-repeater-item-" + target];
          $item.tipso({
            color: color,
            width: width.size || 200,
            position: settings.position || position,
            speed: speed,
            delay: delay,
            showArrow: !hideArrow,
            tooltipHover: !!hover,
            hideDelay: hideDelay,
            background: background,
            useTitle: false,
            content: $("#ha-" + target).html(),
            onBeforeShow: function onBeforeShow($e, e, tooltip) {
              $(document).find('.elementor-repeater-item-' + target).remove();
              tooltip.tipso_bubble.addClass(classes.join(" "));
            }
          });
        });
      }
    });

    var LineChart = function LineChart($scope) {
      elementorFrontend.waypoint($scope, function () {
        var $chart = $(this),
            $container = $chart.find(".ha-line-chart-container"),
            $chart_canvas = $chart.find("#ha-line-chart"),
            settings = $container.data("settings");

        if ($container.length) {
          var chart = new Chart($chart_canvas, settings);
        }
      });
    };

    var RadarChart = function RadarChart($scope) {
      elementorFrontend.waypoint($scope, function () {
        var $chart = $(this),
            $container = $chart.find(".ha-radar-chart-container"),
            $chart_canvas = $chart.find("#ha-radar-chart"),
            settings = $container.data("settings");

        if ($container.length) {
          var chart = new Chart($chart_canvas, settings);
        }
      });
    };

    var PolarChart = function PolarChart($scope) {
      elementorFrontend.waypoint($scope, function () {
        var $chart = $(this),
            $container = $chart.find(".ha-polar-chart-container"),
            $chart_canvas = $chart.find("#ha-polar-chart"),
            settings = $container.data("settings");

        if ($container.length) {
          var chart = new Chart($chart_canvas, settings);
        }
      });
    };

    var PieChart = function PieChart($scope) {
      elementorFrontend.waypoint($scope, function () {
        var $chart = $(this),
            $container = $chart.find(".ha-pie-chart-container"),
            $chart_canvas = $chart.find("#ha-pie-chart"),
            settings = $container.data("settings");

        if ($container.length) {
          var chart = new Chart($chart_canvas, settings);
        }
      });
    };

    var StickyVideoArray = [];

    var StickyVideo = function StickyVideo($scope) {
      var $id = "#ha-sticky-video-player-" + $scope.data("id"),
          $wrap = $scope.find(".ha-sticky-video-wrap"),
          $settting = $wrap.data("ha-player"),
          $box = $wrap.find(".ha-sticky-video-box"),
          $overlay_box = $wrap.find(".ha-sticky-video-overlay"),
          $overlay_play = $overlay_box.find(".ha-sticky-video-overlay-icon"),
          $sticky_close = $wrap.find(".ha-sticky-video-close i"),
          $all_box = $(".ha-sticky-video-box"),
          event = "scroll.haStickyVideo" + $scope.data("id"),
          set;
      var playerAbc = new Plyr($id);
      var StickyVideoObject = {
        player: playerAbc,
        event: event,
        player_box: $box
      };
      StickyVideoArray.push(StickyVideoObject); //on ready

      playerAbc.on("ready", function (e) {
        var $box_plyr = $box.find('.plyr.plyr--video');

        if (true == $settting.autoplay) {
          $box_plyr.trigger("click");
        }
      }); //on overlay click

      if (0 !== $overlay_box.length) {
        var $el = 0 !== $overlay_play.length ? $overlay_play : $overlay_box;
        $el.on("click", function () {
          playerAbc.play();
        });
      } //on close sticky


      $sticky_close.on("click", function () {
        $box.removeClass("sticky");
        $box.addClass("sticky-close");
        playerAbc.pause();
      }); //on Play

      playerAbc.on("play", function (e) {
        $overlay_box.css("display", "none");

        if ($box.hasClass("sticky-close")) {
          $box.removeClass("sticky-close");
        }

        StickyVideoArray.forEach(function (item, index) {
          if (item.player !== playerAbc) {
            item.player.pause();
          }

          if (item.event !== event) {
            $window.off(item.event);
          }

          if (item.player_box !== $box) {
            item.player_box.removeClass("sticky");
          }
        });

        if (true === $settting.sticky) {
          $window.on(event, function () {
            var height = $box.outerHeight(true);
            var $offsetTop = $wrap.offset().top;
            var videoBoxTopPoint = $window.scrollTop();
            var videoBoxMiddlePoint = $offsetTop + height / 2;

            if (!$box.hasClass("sticky-close")) {
              if (videoBoxMiddlePoint < videoBoxTopPoint) {
                $box.addClass("sticky");
              } else {
                $box.removeClass("sticky");
              }
            }
          });
        } else {
          $window.off(event);
        }
      }); // on pause

      playerAbc.on("pause", function (e) {
        $window.off(event);
      });
      $window.on("load resize", debounce(function () {
        var height = $box.find(".plyr").height();
        $wrap.css("min-height", height + "px");
      }, 100));
    }; //facebook feed


    var FacebookFeed = function FacebookFeed($scope) {
      var button = $scope.find(".ha-facebook-load-more");
      var facebook_wrap = $scope.find(".ha-facebook-items");
      button.on("click", function (e) {
        e.preventDefault();
        var $self = $(this),
            query_settings = $self.data("settings"),
            total = $self.data("total"),
            items = $scope.find(".ha-facebook-item").length;
        $.ajax({
          url: HappyProLocalize.ajax_url,
          type: "POST",
          data: {
            action: "ha_facebook_feed_action",
            security: HappyProLocalize.nonce,
            query_settings: query_settings,
            loaded_item: items
          },
          success: function success(response) {
            if (total > items) {
              $(response).appendTo(facebook_wrap);
            } else {
              $self.text("All Loaded").addClass("loaded");
              setTimeout(function () {
                $self.css({
                  display: "none"
                });
              }, 800);
            }
          },
          error: function error(_error3) {}
        });
      });
    }; //SmartPostList


    var SmartPostList = function SmartPostList($scope) {
      var filterWrap = $scope.find(".ha-spl-filter"),
          customSelect = $scope.find(".ha-spl-custom-select"),
          gridWrap = $scope.find(".ha-spl-grid-area"),
          mainWrapper = $scope.find(".ha-spl-wrapper"),
          querySettings = mainWrapper.data("settings"),
          dataOffset = mainWrapper.attr("data-offset"),
          nav = $scope.find(".ha-spl-pagination button"),
          navPrev = $scope.find(".ha-spl-pagination button.prev"),
          navNext = $scope.find(".ha-spl-pagination button.next"),
          filter = $scope.find("ul.ha-spl-filter-list li span"),
          event;
      customSelect.niceSelect();
      var select = $scope.find(".ha-spl-custom-select li");

      function afterClick(e) {
        e.preventDefault();
        var $self = $(this),
            dataFilter = filterWrap.attr("data-active"),
            dataTotalOffset = mainWrapper.attr("data-total-offset"),
            offset = "",
            termId = "";

        if (e.target.classList.contains("prev") || e.target.classList.contains("fa-angle-left")) {
          if (0 == parseInt(dataTotalOffset)) {
            navPrev.attr("disabled", true);
            return;
          }

          offset = 0 !== parseInt(dataTotalOffset) ? parseInt(dataTotalOffset) - parseInt(dataOffset) : "0";

          if (undefined !== dataFilter) {
            termId = dataFilter;
          }
        } else if (e.target.classList.contains("next") || e.target.classList.contains("fa-angle-right")) {
          offset = parseInt(dataTotalOffset) + parseInt(dataOffset);

          if (undefined !== dataFilter) {
            termId = dataFilter;
          }
        }

        if (e.target.hasAttribute("data-value")) {
          termId = e.target.dataset["value"];
          filterWrap[0].setAttribute("data-active", termId);

          if ("SPAN" === e.target.tagName) {
            filter.removeClass("ha-active");
            e.target.classList.toggle("ha-active");
          }

          offset = 0;
          navPrev.attr("disabled", true);
          navNext.removeAttr("disabled");
        }

        $.ajax({
          url: HappyProLocalize.ajax_url,
          type: "POST",
          data: {
            action: "ha_smart_post_list_action",
            security: HappyProLocalize.nonce,
            querySettings: querySettings,
            offset: offset,
            termId: termId
          },
          success: function success(response) {
            if ($(response).length > 0) {
              gridWrap.css("height", gridWrap.outerHeight(true) + "px");
              gridWrap.html("");
              $(response).appendTo(gridWrap);
              gridWrap.css("height", "");
              mainWrapper[0].attributes["data-total-offset"].value = offset;

              if (e.target.classList.contains("prev") || e.target.classList.contains("fa-angle-left")) {
                navNext.removeAttr("disabled");
              }

              if (e.target.classList.contains("next") || e.target.classList.contains("fa-angle-right")) {
                navPrev.removeAttr("disabled");
              }
            } else {
              if (e.target.classList.contains("next") || e.target.classList.contains("fa-angle-right")) {
                navNext.attr("disabled", true);
              }
            }
          },
          error: function error(_error4) {}
        });
      }

      nav.on("click", afterClick);
      filter.on("click", afterClick);
      select.on("click", afterClick);
    }; //PostGrid


    var postGridSkins = ["classic", "hawai", "standard", "monastic", "stylica", "outbox", "crossroad"];

    var PostGrid = function PostGrid($scope) {
      var wrapper = $scope.find(".ha-pg-wrapper"),
          gridWrap = wrapper.find(".ha-pg-grid-wrap"),
          button = wrapper.find("button.ha-pg-loadmore");
      button.on("click", function (e) {
        e.preventDefault();
        var $self = $(this),
            querySettings = $self.data("settings"),
            items = wrapper.find(".ha-pg-item").length;
        $self.attr("disabled", true);
        $.ajax({
          url: HappyProLocalize.ajax_url,
          type: "POST",
          data: {
            action: "hapro_post_grid_action",
            security: HappyProLocalize.nonce,
            querySettings: querySettings,
            loadedItem: items
          },
          beforeSend: function beforeSend() {
            $self.find(".eicon-loading").css({
              display: "inline-block"
            });
          },
          success: function success(response) {
            if (response) {
              $(response).each(function () {
                var $self = $(this);

                if ($self.hasClass("ha-pg-item")) {
                  $self.addClass("ha-pg-item-loaded").appendTo(gridWrap);
                } else {
                  $self.appendTo(gridWrap);
                }
              });
            } else {
              $self.text("All Loaded").addClass("loaded");
              setTimeout(function () {
                $self.css({
                  display: "none"
                });
              }, 800);
            }

            $self.find(".eicon-loading").css({
              display: "none"
            });
            $self.removeAttr("disabled");
          },
          error: function error(_error5) {}
        });
      });
    }; // Advanced Data Table


    var DataTable = function DataTable($scope) {
      var dataTable = $scope.find(".ha-advanced-table");
      var widgetWrapper = $scope.find(".elementor-widget-container");
      var row_td = $scope.find(".ha-advanced-table__body-row-cell");
      var search = dataTable.data("search") == true ? true : false;
      var paging = dataTable.data("paging") == true ? true : false;
      var scrollX = dataTable.data("scroll-x") == undefined ? false : true;

      if (window.innerWidth <= 767) {
        var scrollX = true;
      } // DataTables.js settings


      $(dataTable).DataTable({
        searching: search,
        paging: paging,
        orderCellsTop: true,
        scrollX: scrollX,
        aaSorting: []
      });
      var column_th = $scope.find(".ha-advanced-table__head-column-cell");
      column_th.each(function (index, v) {
        $(column_th[index]).css("width", "");
      }); // export table button

      if (dataTable.data("export-table") == true) {
        widgetWrapper.prepend('<div class="ha-advanced-table-btn-wrap"><button class="ha-advanced-table-btn">' + dataTable.data("export-table-text") + "</button></div>");
        var dataTableBtn = $scope.find(".ha-advanced-table-btn");
        dataTableBtn.on("click", function () {
          var oldDownloadNode = document.querySelector(".ha-advanced-data-table-export");

          if (oldDownloadNode) {
            oldDownloadNode.parentNode.removeChild(oldDownloadNode);
          }

          var csv = [];
          dataTable.find("tr").each(function (index, tr) {
            var data = [];
            $(tr).find("th").each(function (index, th) {
              data.push(th.textContent);
            });
            $(tr).find("td").each(function (index, td) {
              data.push(td.textContent);
            });
            csv.push(data.join(","));
          });
          var csvFile = new Blob([csv.join("\n")], {
            type: "text/csv"
          });
          var downloadNode = document.createElement("a");
          var url = URL.createObjectURL(csvFile);
          var date = new Date();
          var options = {
            day: "2-digit",
            month: "short",
            year: "numeric",
            hour: "numeric",
            minute: "numeric",
            second: "numeric"
          };
          var formatedDate = date.toLocaleDateString("en-BD", options);
          var dateString = JSON.stringify(formatedDate.replace(/[ , :]/g, "-"));
          downloadNode.download = "advanced-data-table-" + dateString + "~" + dataTable.data("widget-id") + ".csv";
          downloadNode.setAttribute("href", url);
          downloadNode.classList.add("ha-advanced-data-table-export");
          downloadNode.style.visibility = "hidden";
          document.body.appendChild(downloadNode);
          downloadNode.click();
        });
      } //for fit table th&td


      $(window).trigger('resize');
    }; // ModalPopup


    var ModalPopup = function ModalPopup($scope) {
      var boxWrapper = $scope.find(".ha-modal-popup-box-wrapper");
      var triggerType = boxWrapper.data("trigger-type");
      var trigger = $scope.find(".ha-modal-popup-trigger");
      var overlayClose = $scope.find(".ha-modal-popup-overlay");
      var iconClose = $scope.find(".ha-modal-popup-content-close");
      var modal = $scope.find(".ha-modal-popup-content-wrapper");
      var modalAnimation = modal.data("animation");
      var modalDelay = modal.data("display-delay");

      if ("pageload" == triggerType) {
        if (!modal.hasClass("ha-modal-show")) {
          setTimeout(function () {
            modal.addClass("ha-modal-show");
            modal.find(".ha-modal-animation").addClass(modalAnimation);
          }, parseInt(modalDelay));
        }
      } else {
        trigger.on("click", function (e) {
          e.preventDefault();
          var wrapper = $(this).closest(".ha-modal-popup-box-wrapper"),
              modal = wrapper.find(".ha-modal-popup-content-wrapper"),
              modalAnimation = modal.data("animation"),
              modalContent = modal.find(".ha-modal-animation");

          if (!modal.hasClass("ha-modal-show")) {
            modal.addClass("ha-modal-show");
            modalContent.addClass(modalAnimation);
          }
        });
      }

      overlayClose.on("click", close_modal);
      iconClose.on("click", close_modal);

      function close_modal(el) {
        var wrap = $(this).closest(".ha-modal-popup-content-wrapper"),
            modalAnimation = wrap.data("animation");

        if (wrap != null && wrap.hasClass("ha-modal-show")) {
          var doSetTimeout = function doSetTimeout(target, source) {
            setTimeout(function () {
              target.attr("src", source);
            }, 500);
          };

          var nVideo = wrap.find("iframe, video");
          $.each(nVideo, function (idx, val) {
            var videoSrc = $(this).attr("src");
            $(this).attr("src", "https://happyaddons.com/marvin/index.html");
            doSetTimeout($(this), videoSrc);
          });
          wrap.removeClass("ha-modal-show");
          wrap.find("." + modalAnimation).removeClass(modalAnimation);
        }
      }
    }; //Mini Cart


    var miniCart = function miniCart($scope) {
      $scope.find(".ha-mini-cart-inner").on("click mouseenter mouseleave", function (e) {
        var cart_btn = $(this),
            on_click = cart_btn.hasClass("ha-mini-cart-on-click"),
            on_hover = cart_btn.hasClass("ha-mini-cart-on-hover"),
            popup = cart_btn.find(".ha-mini-cart-popup");

        if (popup.length == 0) {
          return;
        }

        if ("click" === e.type && on_click) {
          popup.fadeToggle();
        }

        if ("mouseenter" === e.type && on_hover) {
          popup.fadeIn();
        }

        if ("mouseleave" === e.type && on_hover) {
          popup.fadeOut();
        }
      });

      if ($scope.find(".ha-mini-cart-popup").length > 0 && $scope.find(".ha-mini-cart-on-click").length > 0) {
        $("body").on("click", function (e) {
          if ($(e.target).hasClass("ha-mini-cart-popup") || $(e.target).parents().hasClass("ha-mini-cart-popup") || $(e.target).hasClass("ha-mini-cart-button") || $(e.target).parents().hasClass("ha-mini-cart-button")) {
            return;
          } else {
            $scope.find(".ha-mini-cart-popup").removeAttr("style");
          }
        });
      }
    }; //Image Scroller


    var ImageScroller = elementorModules.frontend.handlers.Base.extend({
      onInit: function onInit() {
        elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
        this.wrapper = this.$element.find(".ha-image-scroller-wrapper");
        this.run();
      },
      onElementChange: function onElementChange() {
        this.run();
      },
      run: function run() {
        var triggerType = this.wrapper.data("trigger-type");

        if ("hover" !== triggerType) {
          return;
        }

        var figure = this.wrapper.find(".ha-image-scroller-container");
        var scrollType = this.wrapper.data("scroll-type");
        var scrollDirection = this.wrapper.data("scroll-direction");
        var imageScroll = figure.find("img");
        var transformY = imageScroll.height() - figure.height();
        var transformX = imageScroll.width() - figure.width();

        if (scrollType === "vertical" && transformY > 0) {
          if ("top" === scrollDirection) {
            mouseEvent("translateY", transformY);
          } else {
            imageScroll.css("transform", "translateY" + "( -" + transformY + "px)");
            mouseEventRevers("translateY", transformY);
          }
        }

        if (scrollType === "horizontal" && transformX > 0) {
          if ("left" === scrollDirection) {
            mouseEvent("translateX", transformX);
          } else {
            imageScroll.css("transform", "translateX" + "( -" + transformX + "px)");
            mouseEventRevers("translateX", transformX);
          }
        }

        function mouseEvent(cssProperty, value) {
          figure.on("mouseenter", function () {
            imageScroll.css("transform", cssProperty + "( -" + value + "px)");
          });
          figure.on("mouseleave", function () {
            imageScroll.css("transform", cssProperty + "( 0px)");
          });
        }

        function mouseEventRevers(cssProperty, value) {
          figure.on("mouseenter", function () {
            imageScroll.css("transform", cssProperty + "( 0px)");
          });
          figure.on("mouseleave", function () {
            imageScroll.css("transform", cssProperty + "( -" + value + "px)");
          });
        }
      }
    }); // Mega Menu

    var NavMenu = function _init($scope) {
      $scope.find(".ha-menu-toggler").on("click", function (event) {
        event.preventDefault();
        var el_form_group = $(this).parents(".ha-menu-container").parent();

        if (el_form_group.length < 1) {
          el_form_group = $(this).parent();
        }

        var $wrapElement = el_form_group.find(".ha-menu-offcanvas-elements");

        if ($wrapElement.hasClass("active")) {
          $wrapElement.removeClass("active");
        } else {
          $wrapElement.addClass("active");
        }
      });

      if ($scope.find(".ha-menu-container").length > 0) {
        var additionalDigits = $scope.find(".ha-wid-con").data("responsive-breakpoint");
        var sidebar_mousemove = $scope.find(".ha-megamenu-has");
        var tabPadding = $scope.find(".ha-menu-container").outerHeight();
        $(window).on("resize", function () {
          $scope.find(".ha-megamenu-panel").css({
            top: tabPadding
          });
        }).trigger("resize");
        sidebar_mousemove.on("mouseenter", function () {
          var q = $(this).data("vertical-menu");
          var jqel = $(this).children(".ha-megamenu-panel");

          if ($(this).hasClass("ha-dropdown-menu-full_width") && $(this).hasClass("top_position")) {
            /** @type {number} */
            var ffleft = Math.floor($(this).position().left - $(this).offset().left);
            var $sharepreview = $(this);
            $sharepreview.find(".ha-megamenu-panel").css("width", $(window).width());
            $(window).on("resize", function () {
              $sharepreview.find(".ha-megamenu-panel").css({
                left: ffleft + "px"
              });
            }).trigger("resize");
          }

          if (!$(this).hasClass("ha-dropdown-menu-full_width") && $(this).hasClass("top_position")) {
            $(this).on({
              mouseenter: function setup() {
                if (0 === $(".default_menu_position").length) {
                  $(this).parents(".elementor-section-wrap").addClass("default_menu_position");
                }
              },
              mouseleave: function setup() {
                if (0 !== $(".default_menu_position").length) {
                  $(this).parents(".elementor-section-wrap").removeClass("default_menu_position");
                }
              }
            });
          }

          if (q && q !== undefined) {
            if ("string" == typeof q) {
              if (/^[0-9]/.test(q)) {
                $(window).on("resize", function () {
                  jqel.css({
                    width: q
                  });

                  if (!($(document).width() > Number(additionalDigits))) {
                    jqel.removeAttr("style");
                  }
                }).trigger("resize");
              } else {
                $(window).on("resize", function () {
                  jqel.css({
                    width: q + "px"
                  });

                  if (!($(document).width() > Number(additionalDigits))) {
                    jqel.removeAttr("style");
                  }
                }).trigger("resize");
              }
            } else {
              jqel.css({
                width: q + "px"
              });
            }
          } else {
            $(window).on("resize", function () {
              jqel.css({
                width: q + "px"
              });

              if (!($(document).width() > Number(additionalDigits))) {
                jqel.removeAttr("style");
              }
            }).trigger("resize");
          }
        });
        sidebar_mousemove.trigger("mouseenter");
      }
    };

    elementorFrontend.hooks.addAction("frontend/element_ready/ha-countdown.default", CountDown);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-team-carousel.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(CarouselBase, {
        $element: $scope,
        selectors: {
          container: ".ha-team-carousel-wrap"
        },
        prevArrow: '<button type="button" class="slick-prev"><i class="hm hm-arrow-left"></i></button>',
        nextArrow: '<button type="button" class="slick-next"><i class="hm hm-arrow-right"></i></button>'
      });
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-source-code.default", SourceCode);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-animated-text.default", AnimatedText);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-logo-carousel.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(CarouselBase, {
        $element: $scope,
        selectors: {
          container: ".ha-logo-carousel-wrap"
        }
      });
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-testimonial-carousel.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(CarouselBase, {
        $element: $scope,
        selectors: {
          container: ".ha-testimonial-carousel__wrap"
        }
      });
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-advanced-tabs.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(TabHandlerBase, {
        $element: $scope
      });
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-instagram-feed.default", InstagramFeed);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-scrolling-image.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(ScrollingImage, {
        $element: $scope
      });
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-pricing-table.default", PricingTableToolTip);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-accordion.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(TabHandlerBase, {
        $element: $scope,
        selectors: {
          tabTitle: ".ha-accordion__item-title",
          tabContent: ".ha-accordion__item-content"
        },
        classes: {
          active: "ha-accordion__item--active"
        },
        showTabFn: "slideDown",
        hideTabFn: "slideUp"
      });
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-toggle.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(TabHandlerBase, {
        $element: $scope,
        selectors: {
          tabTitle: ".ha-toggle__item-title",
          tabContent: ".ha-toggle__item-content"
        },
        classes: {
          active: "ha-toggle__item--active"
        },
        hidePrevious: false,
        autoExpand: "editor",
        showTabFn: "slideDown",
        hideTabFn: "slideUp"
      });
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-timeline.default", TimeLine);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-hotspots.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(HotSpots, {
        $element: $scope
      });
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-line-chart.default", LineChart);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-pie-chart.default", PieChart);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-polar-chart.default", PolarChart);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-radar-chart.default", RadarChart);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-facebook-feed.default", FacebookFeed);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-smart-post-list.default", SmartPostList);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-advanced-data-table.default", DataTable);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-twitter-carousel.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(CarouselBase, {
        $element: $scope,
        selectors: {
          container: ".ha-tweet-carousel-items"
        }
      });
    }); // Post Carousel

    elementorFrontend.hooks.addAction("frontend/element_ready/ha-post-carousel.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(CarouselBase, {
        $element: $scope,
        selectors: {
          container: ".ha-posts-carousel-wrapper"
        },
        prevArrow: '<button type="button" class="slick-prev"><i class="hm hm-arrow-left"></i></button>',
        nextArrow: '<button type="button" class="slick-next"><i class="hm hm-arrow-right"></i></button>'
      });
    }); // EDD Download product carousel

    function initEddCarousel($scope) {
      elementorFrontend.elementsHandler.addHandler(CarouselBase, {
        $element: $scope,
        selectors: {
          container: ".ha-edd-product-carousel-wrapper"
        },
        prevArrow: '<button type="button" class="slick-prev"><i class="hm hm-arrow-left"></i></button>',
        nextArrow: '<button type="button" class="slick-next"><i class="hm hm-arrow-right"></i></button>'
      });
    }

    function eddCarouselHandlerCallback($scope) {
      initEddCarousel($scope);
      initProductQuickView($scope); // if (
      // 	$scope
      // 		.find(".ha-edd-product-carousel-wrapper")
      // 		.hasClass("ha-layout-classic")
      // ) {
      // 	$scope
      // 		.find(".ha-product-carousel-add-to-cart a")
      // 		.html('<i class="fas fa-shopping-cart"></i>');
      // }
    } //  Carousel


    elementorFrontend.hooks.addAction("frontend/element_ready/ha-edd-product-carousel.default", eddCarouselHandlerCallback); // product carousel

    function initProductCarousel($scope) {
      elementorFrontend.elementsHandler.addHandler(CarouselBase, {
        $element: $scope,
        selectors: {
          container: ".ha-product-carousel-wrapper"
        },
        prevArrow: '<button type="button" class="slick-prev"><i class="hm hm-arrow-left"></i></button>',
        nextArrow: '<button type="button" class="slick-next"><i class="hm hm-arrow-right"></i></button>'
      });
    }

    function productCarouselHandlerCallback($scope) {
      initProductCarousel($scope);
      initProductQuickView($scope);

      if ($scope.find(".ha-product-carousel-wrapper").hasClass("ha-layout-modern")) {
        $scope.find(".ha-product-carousel-add-to-cart a").html('<i class="fas fa-shopping-cart"></i>');
      }
    }

    elementorFrontend.hooks.addAction("frontend/element_ready/ha-product-carousel.classic", productCarouselHandlerCallback);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-product-carousel.modern", productCarouselHandlerCallback);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-product-carousel-new.default", productCarouselHandlerCallback); // product category carousel

    var productCategoryCarouselSkins = ["classic", "minimal"];

    function initProductCategoryCarousel($scope) {
      elementorFrontend.elementsHandler.addHandler(CarouselBase, {
        $element: $scope,
        selectors: {
          container: ".ha-product-cat-carousel"
        },
        prevArrow: '<button type="button" class="slick-prev"><i class="hm hm-arrow-left"></i></button>',
        nextArrow: '<button type="button" class="slick-next"><i class="hm hm-arrow-right"></i></button>'
      });
    }

    productCategoryCarouselSkins.forEach(function (index) {
      elementorFrontend.hooks.addAction("frontend/element_ready/ha-product-category-carousel." + index, initProductCategoryCarousel);
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-product-category-carousel-new.default", initProductCategoryCarousel); // EDD category carousel

    elementorFrontend.hooks.addAction("frontend/element_ready/ha-edd-category-carousel.default", initProductCategoryCarousel);
    postGridSkins.forEach(function (index) {
      elementorFrontend.hooks.addAction("frontend/element_ready/ha-post-grid." + index, PostGrid);
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-post-grid-new.default", PostGrid);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-sticky-video.default", StickyVideo);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-modal-popup.default", ModalPopup);

    function initProductQuickView($scope) {
      var $button = $scope.find(".ha-pqv-btn");

      if (!$.fn["magnificPopup"]) {
        return;
      }

      $button.magnificPopup({
        type: "ajax",
        ajax: {
          settings: {
            cache: true,
            dataType: "html"
          },
          cursor: "mfp-ajax-cur",
          tError: "The product could not be loaded."
        },
        callbacks: {
          ajaxContentAdded: function ajaxContentAdded() {
            $(this.content).addClass(this.currItem.el.data("modal-class"));
          }
        }
      });
    }

    var initOnePageNav = elementorModules.frontend.handlers.Base.extend({
      onInit: function onInit() {
        elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
        this.run();
      },
      onElementChange: function onElementChange(e) {
        if (e == 'scroll_wheel' || e == 'touch_swipe' || e == 'scroll_keys') {
          this.run();
        }
      },
      getReadySettings: function getReadySettings() {
        var settings = {
          design: this.getElementSettings('select_design'),
          speed: this.getElementSettings('scrolling_speed') || 700,
          offset: this.getElementSettings('row_to_offset') || 0,
          threshold: this.getEditSettings('section_threshold') || 0.50,
          scrollWheel: this.getElementSettings('scroll_wheel') == 'yes',
          touchSwipe: this.getElementSettings('touch_swipe') == 'yes',
          scrollKeys: this.getElementSettings('scroll_keys') == 'yes'
        };
        return $.extend({}, this.getSettings(), settings);
      },
      scroll: function scroll(id, speed, offset) {
        $('html, body').animate({
          scrollTop: $('#' + id).offset().top - offset
        }, speed);
      },
      run: function run() {
        var $this = this;
        var settings = this.getReadySettings();
        var track = false;
        var $navWrapper = settings.design == 'ha-opn-design-default' ? this.$element.find(".ha-opn-dotted-item") : this.$element.find(".ha_opn__item");
        $navWrapper.on('click', function (e) {
          e.preventDefault();
          var targetId = $(this).find('a').data("section-id");

          if ($('#' + targetId).length > 0) {
            track = false;
            $this.scroll(targetId, settings.speed, settings.offset);
            $navWrapper.removeClass('ha_opn__item--current');
            $(this).addClass('ha_opn__item--current');
          }
        });
        var sections = [];
        var navItems = {};

        if ($navWrapper.length > 0) {
          $.each($navWrapper, function (index, nav) {
            var targetId = $(this).find('a').data('section-id');
            var targetSection = $('#' + targetId);
            sections.push(targetSection[0]);
            navItems[targetId] = $(this)[0];
          });
        }

        var observerOptions = {
          root: null,
          rootMargin: "0px 0px -100px 0px",
          threshold: settings.threshold
        };
        var currentEvent;
        var observer = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              var navItem = navItems[entry.target.id];
              navItem.classList.add('ha_opn__item--current');
              Object.values(navItems).forEach(function (item) {
                if (item != navItem) {
                  item.classList.remove('ha_opn__item--current');
                }
              });

              if (typeof elementor === 'undefined') {
                if (currentEvent == 'load' && track) {
                  track = false;
                  $this.scroll(entry.target.id, settings.speed, settings.offset);
                }

                if (settings.scrollWheel && currentEvent == 'scroll' && track) {
                  track = false;
                  $this.scroll(entry.target.id, settings.speed, settings.offset);
                }

                if (settings.scrollKeys && currentEvent == 'keys' && track) {
                  track = false;
                  $this.scroll(entry.target.id, settings.speed, settings.offset);
                }

                if (settings.touchSwipe && currentEvent == 'touch' && track) {
                  track = false;
                  $this.scroll(entry.target.id, settings.speed, settings.offset);
                }
              }
            }
          });
        }, observerOptions);
        $(window).on('load', function () {
          track = true;

          if (sections.length > 0) {
            sections.forEach(function (sec) {
              if (!(sec == undefined)) {
                currentEvent = 'load';
                observer.observe(sec);
              }
            });
          }
        });
        $(window).on('mousewheel DOMMouseScroll', debounce(function (e) {
          track = true;

          if (sections.length > 0) {
            sections.forEach(function (sec) {
              if (!(sec == undefined)) {
                currentEvent = 'scroll';
                observer.observe(sec);
              }
            });
          }
        }, 200));

        if (typeof elementor === 'undefined') {
          if (settings.scrollKeys) {
            $(window).on('keydown', debounce(function (evt) {
              track = true;
              var code = parseInt(evt.keyCode); // code == 38 for arrow up key
              // code == 40 for arrow up key

              if (sections.length > 0) {
                sections.forEach(function (sec) {
                  if (!(sec == undefined)) {
                    currentEvent = 'keys';
                    observer.observe(sec);
                  }
                });
              }
            }, 200));
          }

          if (settings.touchSwipe) {
            $(window).on('touchmove', debounce(function (evt) {
              track = true;

              if (sections.length > 0) {
                sections.forEach(function (sec) {
                  if (!(sec == undefined)) {
                    currentEvent = 'touch';
                    observer.observe(sec);
                  }
                });
              }
            }, 200));
          }
        }
      }
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-one-page-nav.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(initOnePageNav, {
        $element: $scope
      });
    }); // advancedSliderHandler

    elementorFrontend.hooks.addAction("frontend/element_ready/ha-advanced-slider.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(SliderBase, {
        $element: $scope,
        selectors: {
          container: ".ha-slider-widget-wrapper"
        }
      });
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-product-grid.classic", initProductQuickView);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-product-grid.hover", initProductQuickView);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-product-grid-new.default", initProductQuickView);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-single-product.classic", initProductQuickView);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-single-product.standard", initProductQuickView);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-single-product.landscape", initProductQuickView);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-single-product-new.default", initProductQuickView);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-edd-single-product.default", initProductQuickView);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-edd-product-grid.default", initProductQuickView);
    $(document.body).on("added_to_cart", function (event, fragment, hash, $addToCart) {
      if ($addToCart != undefined && $addToCart.length > 0) {
        $addToCart.parent(".ha-product-grid__btns").removeClass("ha-is--adding").addClass("ha-is--added");
      }
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-mini-cart.default", miniCart);
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-image-scroller.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(ImageScroller, {
        $element: $scope
      });
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-nav-menu.default", NavMenu); // Off Canvas

    var $trigger;

    var OffCanvas = function OffCanvas($scope) {
      this.node = $scope;
      this.wrap = $scope.find(".ha-offcanvas-content-wrap");
      this.cart_wrap = $scope.find(".ha-offcanvas-cart-container");
      this.content = $scope.find(".ha-offcanvas-content");
      this.button = $scope.find(".ha-offcanvas-toggle");
      this.settings = this.wrap.data("settings");
      this.toggle_source = this.settings.toggle_source;
      this.id = this.settings.content_id;
      this.toggle_id = this.settings.toggle_id;
      this.toggle_class = this.settings.toggle_class;
      this.transition = this.settings.transition;
      this.esc_close = this.settings.esc_close;
      this.body_click_close = this.settings.body_click_close;
      this.links_click_close = this.settings.links_click_close;
      this.direction = this.settings.direction;
      this.buttons_position = this.settings.buttons_position;
      this.duration = 500;
      this.destroy();
      this.init();
    };

    OffCanvas.prototype = {
      id: "",
      node: "",
      wrap: "",
      content: "",
      button: "",
      settings: {},
      transition: "",
      duration: 400,
      initialized: false,
      animations: ["slide", "slide-along", "reveal", "push"],
      init: function init() {
        if (!this.wrap.length) {
          return;
        }

        $("html").addClass("ha-offcanvas-content-widget");

        if ($(".ha-offcanvas-container").length === 0) {
          var faProJs = $("#font-awesome-pro-js").length > 0 ? $("#font-awesome-pro-js").attr("src") : false;

          if (faProJs) {
            $("#font-awesome-pro-js").remove();
          }

          $("body").wrapInner('<div class="ha-offcanvas-container" />');
          this.content.insertBefore(".ha-offcanvas-container");

          if (faProJs) {
            $("body").append('<script type="text/javascript" id="font-awesome-pro-js" src="' + faProJs + '"></script>');
          }
        }

        if (this.wrap.find(".ha-offcanvas-content").length > 0) {
          if ($(".ha-offcanvas-container > .ha-offcanvas-content-" + this.id).length > 0) {
            $(".ha-offcanvas-container > .ha-offcanvas-content-" + this.id).remove();
          }

          if ($("body > .ha-offcanvas-content-" + this.id).length > 0) {
            $("body > .ha-offcanvas-content-" + this.id).remove();
          }

          $("body").prepend(this.wrap.find(".ha-offcanvas-content"));
        }

        this.bindEvents();
      },
      destroy: function destroy() {
        this.close();
        this.animations.forEach(function (animation) {
          if ($("html").hasClass("ha-offcanvas-content-" + animation)) {
            $("html").removeClass("ha-offcanvas-content-" + animation);
          }
        });

        if ($("body > .ha-offcanvas-content-" + this.id).length > 0) {//$('body > .ha-offcanvas-content-' + this.id ).remove();
        }
      },
      setTrigger: function setTrigger() {
        var $trigger = false;

        if (this.toggle_source == "element-id" && this.toggle_id != "") {
          $trigger = $("#" + this.toggle_id);
        } else if (this.toggle_source == "element-class" && this.toggle_class != "") {
          $trigger = $("." + this.toggle_class);
        } else {
          $trigger = this.node.find(".ha-offcanvas-toggle");
        }

        return $trigger;
      },
      bindEvents: function bindEvents() {
        $trigger = this.setTrigger();

        if ($trigger) {
          $trigger.on("click", $.proxy(this.toggleContent, this));
        }

        $("body").delegate(".ha-offcanvas-content .ha-offcanvas-close", "click", $.proxy(this.close, this));

        if (this.links_click_close === "yes") {
          $("body").delegate(".ha-offcanvas-content .ha-offcanvas-body a", "click", $.proxy(this.close, this));
        }

        if (this.esc_close === "yes") {
          this.closeESC();
        }

        if (this.body_click_close === "yes") {
          this.closeClick();
        }

        $(window).resize($.proxy(this._resize, this));
      },
      toggleContent: function toggleContent(e) {
        e.preventDefault();

        if (!$("html").hasClass("ha-offcanvas-content-open")) {
          this.show();
        } else {
          this.close();
        }

        this._resize();
      },
      show: function show() {
        $(".ha-offcanvas-content-" + this.id).addClass("ha-offcanvas-content-visible"); // init animation class.

        $("html").addClass("ha-offcanvas-content-" + this.transition);
        $("html").addClass("ha-offcanvas-content-" + this.direction);
        $("html").addClass("ha-offcanvas-content-open");
        $("html").addClass("ha-offcanvas-content-" + this.id + "-open");
        $("html").addClass("ha-offcanvas-content-reset");
        this.button.addClass("ha-is-active");

        this._resize();
      },
      close: function close() {
        $("html").removeClass("ha-offcanvas-content-open");
        $("html").removeClass("ha-offcanvas-content-" + this.id + "-open");
        setTimeout($.proxy(function () {
          $("html").removeClass("ha-offcanvas-content-reset");
          $("html").removeClass("ha-offcanvas-content-" + this.transition);
          $("html").removeClass("ha-offcanvas-content-" + this.direction);
          $(".ha-offcanvas-content-" + this.id).removeClass("ha-offcanvas-content-visible");
        }, this), 500);
        this.button.removeClass("ha-is-active");
      },
      closeESC: function closeESC() {
        var self = this;

        if ("" === self.settings.esc_close) {
          return;
        } // menu close on ESC key


        $(document).on("keydown", function (e) {
          if (e.keyCode === 27) {
            self.close();
          }
        });
      },
      closeClick: function closeClick() {
        var self = this;

        if (this.toggle_source == "element-id" && this.toggle_id != "") {
          $trigger = "#" + this.toggle_id;
        } else if (this.toggle_source == "element-class" && this.toggle_class != "") {
          $trigger = "." + this.toggle_class;
        } else {
          $trigger = ".ha-offcanvas-toggle";
        }

        $(document).on("click", function (e) {
          if ($(e.target).is(".ha-offcanvas-content") || $(e.target).parents(".ha-offcanvas-content").length > 0 || $(e.target).is(".ha-offcanvas-toggle") || $(e.target).parents(".ha-offcanvas-toggle").length > 0 || $(e.target).is($trigger) || $(e.target).parents($trigger).length > 0 || !$(e.target).is(".ha-offcanvas-container")) {
            return;
          } else {
            self.close();
          }
        });
      },
      _resize: function _resize() {
        if (!this.cart_wrap.length) {
          return;
        }

        var off_canvas = $(".ha-offcanvas-content-" + this.id);

        if (off_canvas && off_canvas.length > 0) {
          if (this.buttons_position === "bottom") {
            var winHeight = window.innerHeight;
            var offset = 0;

            if ($("body").hasClass("admin-bar")) {
              offset = 32;
            }

            winHeight = winHeight - offset;
            off_canvas.find(".ha-offcanvas-inner").css({
              height: winHeight + "px",
              top: offset
            });
            headerHeight = off_canvas.find(".ha-offcanvas-cart-header").outerHeight(true);
            wrapHeight = off_canvas.find(".ha-offcanvas-wrap").outerHeight();
            cartTotalHeight = off_canvas.find(".woocommerce-mini-cart__total").outerHeight();
            cartButtonsHeight = off_canvas.find(".woocommerce-mini-cart__buttons").outerHeight();
            cartMessageHeight = off_canvas.find(".ha-woo-menu-cart-message").outerHeight();
            itemsHeight = winHeight - (headerHeight + cartTotalHeight + cartButtonsHeight + cartMessageHeight);
            finalItemsHeight = itemsHeight - (winHeight - wrapHeight);
            finalItemsHeight += "px";
          } else {
            finalItemsHeight = "auto";
          }

          var style = '<style id="ha-woo-style-' + this.id + '">';
          style += "#" + off_canvas.attr("id") + " .woocommerce-mini-cart.cart_list {";
          style += "height: " + finalItemsHeight;
          style += "}";
          style += "</style>";

          if ($("#ha-woopack-style-" + this.id).length > 0) {
            $("#ha-woopack-style-" + this.id).remove();
          }

          $("head").append(style);
        }
      }
    };

    var initOffCanvas = function initOffCanvas($scope, $) {
      var content_wrap = $scope.find(".ha-offcanvas-content-wrap");

      if ($(content_wrap).length > 0) {
        new OffCanvas($scope);
      }
    };

    elementorFrontend.hooks.addAction("frontend/element_ready/ha-off-canvas.default", initOffCanvas);
    var Unfold = elementorModules.frontend.handlers.Base.extend({
      onInit: function onInit() {
        elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
        this.run();
      },
      onElementChange: function onElementChange(e) {
        if (e == "unfold_text" || e == "unfold_icon" || e == "fold_text" || e == "fold_icon" || e == "fold_height" || e == "transition_duration" || e == "trigger") {
          this.run();

          if (e == "fold_height") {
            this.$element.find(".ha-unfold-data").css("height", this.getCollapseHeight() + "px");
          }
        }
      },
      getReadySettings: function getReadySettings() {
        var settings = {
          collapse_height: this.getElementSettings("fold_height") || 50,
          collapse_height_tablet: this.getElementSettings("fold_height_tablet") || this.getElementSettings("fold_height") || 50,
          collapse_height_mobile: this.getElementSettings("fold_height_mobile") || this.getElementSettings("fold_height_tablet") || this.getElementSettings("fold_height") || 50,
          trigger: this.getElementSettings("trigger"),
          transition_duration: this.getElementSettings("transition_duration") || 500,
          collapse_text: this.getElementSettings("unfold_text"),
          collapse_icon: this.getElementSettings("unfold_icon"),
          expand_text: this.getElementSettings("fold_text"),
          expand_icon: this.getElementSettings("fold_icon")
        };
        return $.extend({}, settings);
      },
      getCollapseHeight: function getCollapseHeight() {
        var body = this.$element.parents("body");
        var unfoldSettings = this.getReadySettings();
        var collapse_height = 50;

        if (body.attr("data-elementor-device-mode") == "desktop") {
          collapse_height = unfoldSettings.collapse_height;
        }

        if (body.attr("data-elementor-device-mode") == "tablet") {
          collapse_height = unfoldSettings.collapse_height_tablet;
        }

        if (body.attr("data-elementor-device-mode") == "mobile") {
          collapse_height = unfoldSettings.collapse_height_mobile;
        }

        return collapse_height;
      },
      fold: function fold(unfoldData, button, collapse_height, unfoldRender) {
        var unfoldSettings = this.getReadySettings();
        var html = unfoldSettings.collapse_icon ? unfoldSettings.collapse_icon.value ? '<i aria-hidden="true" class="' + unfoldSettings.collapse_icon.value + '"></i>' : "" : "";
        html += unfoldSettings.collapse_text ? "<span>" + unfoldSettings.collapse_text + "</span>" : "";
        unfoldData.css({
          "transition-duration": unfoldSettings.transition_duration + "ms",
          "height": unfoldRender.outerHeight(true) + 'px'
        });
        unfoldData.animate({
          height: collapse_height
        }, 0);
        var timeOut = setTimeout(function () {
          button.html(html);
          clearTimeout(timeOut);
        }, unfoldSettings.transition_duration);
        unfoldData.removeClass("folded");
      },
      unfold: function unfold(unfoldData, unfoldRender, button) {
        var unfoldSettings = this.getReadySettings();
        var html = unfoldSettings.expand_icon ? unfoldSettings.expand_icon.value ? '<i aria-hidden="true" class="' + unfoldSettings.expand_icon.value + '"></i>' : "" : "";
        html += unfoldSettings.expand_text ? "<span>" + unfoldSettings.expand_text + "</span>" : "";
        unfoldData.css("transition-duration", unfoldSettings.transition_duration + "ms");
        unfoldData.animate({
          height: unfoldRender.outerHeight(true)
        }, 0);
        var timeOut = setTimeout(function () {
          unfoldData.css("height", 'auto');
          button.html(html);
          clearTimeout(timeOut);
        }, unfoldSettings.transition_duration);
        unfoldData.addClass("folded");
      },
      run: function run() {
        var $this = this;
        var button = this.$element.find(".ha-unfold-btn"),
            unfoldData = this.$element.find(".ha-unfold-data"),
            unfoldRender = this.$element.find(".ha-unfold-data-render");
        var unfoldSettings = this.getReadySettings();
        var collapse_height = $this.getCollapseHeight();
        unfoldData.css("height", collapse_height + "px");

        if (unfoldSettings.trigger == "click") {
          button.on("click", function () {
            collapse_height = $this.getCollapseHeight();

            if (unfoldData.hasClass("folded")) {
              $this.fold(unfoldData, button, collapse_height, unfoldRender);
            } else {
              $this.unfold(unfoldData, unfoldRender, button);
            }
          });
        } else if (unfoldSettings.trigger == "hover") {
          unfoldData.on("mouseenter", function () {
            $this.unfold(unfoldData, unfoldRender, button);
          });
          unfoldData.on("mouseleave", function () {
            collapse_height = $this.getCollapseHeight();
            $this.fold(unfoldData, button, collapse_height, unfoldRender);
          });
        }
      }
    });
    var Maps = elementorModules.frontend.handlers.Base.extend({
      onInit: function onInit() {
        elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
        this.runMap();
      },
      defaultStyle: function defaultStyle() {
        var defaultStyles = {
          standard: "",
          silver: [{
            elementType: "geometry",
            stylers: [{
              color: "#f5f5f5"
            }]
          }, {
            elementType: "labels.icon",
            stylers: [{
              visibility: "off"
            }]
          }, {
            elementType: "labels.text.fill",
            stylers: [{
              color: "#616161"
            }]
          }, {
            elementType: "labels.text.stroke",
            stylers: [{
              color: "#f5f5f5"
            }]
          }, {
            featureType: "administrative.land_parcel",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#bdbdbd"
            }]
          }, {
            featureType: "poi",
            elementType: "geometry",
            stylers: [{
              color: "#eeeeee"
            }]
          }, {
            featureType: "poi",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#757575"
            }]
          }, {
            featureType: "poi.park",
            elementType: "geometry",
            stylers: [{
              color: "#e5e5e5"
            }]
          }, {
            featureType: "poi.park",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#9e9e9e"
            }]
          }, {
            featureType: "road",
            elementType: "geometry",
            stylers: [{
              color: "#ffffff"
            }]
          }, {
            featureType: "road.arterial",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#757575"
            }]
          }, {
            featureType: "road.highway",
            elementType: "geometry",
            stylers: [{
              color: "#dadada"
            }]
          }, {
            featureType: "road.highway",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#616161"
            }]
          }, {
            featureType: "road.local",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#9e9e9e"
            }]
          }, {
            featureType: "transit.line",
            elementType: "geometry",
            stylers: [{
              color: "#e5e5e5"
            }]
          }, {
            featureType: "transit.station",
            elementType: "geometry",
            stylers: [{
              color: "#eeeeee"
            }]
          }, {
            featureType: "water",
            elementType: "geometry",
            stylers: [{
              color: "#c9c9c9"
            }]
          }, {
            featureType: "water",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#9e9e9e"
            }]
          }],
          retro: [{
            elementType: "geometry",
            stylers: [{
              color: "#ebe3cd"
            }]
          }, {
            elementType: "labels.text.fill",
            stylers: [{
              color: "#523735"
            }]
          }, {
            elementType: "labels.text.stroke",
            stylers: [{
              color: "#f5f1e6"
            }]
          }, {
            featureType: "administrative",
            elementType: "geometry.stroke",
            stylers: [{
              color: "#c9b2a6"
            }]
          }, {
            featureType: "administrative.land_parcel",
            elementType: "geometry.stroke",
            stylers: [{
              color: "#dcd2be"
            }]
          }, {
            featureType: "administrative.land_parcel",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#ae9e90"
            }]
          }, {
            featureType: "landscape.natural",
            elementType: "geometry",
            stylers: [{
              color: "#dfd2ae"
            }]
          }, {
            featureType: "poi",
            elementType: "geometry",
            stylers: [{
              color: "#dfd2ae"
            }]
          }, {
            featureType: "poi",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#93817c"
            }]
          }, {
            featureType: "poi.park",
            elementType: "geometry.fill",
            stylers: [{
              color: "#a5b076"
            }]
          }, {
            featureType: "poi.park",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#447530"
            }]
          }, {
            featureType: "road",
            elementType: "geometry",
            stylers: [{
              color: "#f5f1e6"
            }]
          }, {
            featureType: "road.arterial",
            elementType: "geometry",
            stylers: [{
              color: "#fdfcf8"
            }]
          }, {
            featureType: "road.highway",
            elementType: "geometry",
            stylers: [{
              color: "#f8c967"
            }]
          }, {
            featureType: "road.highway",
            elementType: "geometry.stroke",
            stylers: [{
              color: "#e9bc62"
            }]
          }, {
            featureType: "road.highway.controlled_access",
            elementType: "geometry",
            stylers: [{
              color: "#e98d58"
            }]
          }, {
            featureType: "road.highway.controlled_access",
            elementType: "geometry.stroke",
            stylers: [{
              color: "#db8555"
            }]
          }, {
            featureType: "road.local",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#806b63"
            }]
          }, {
            featureType: "transit.line",
            elementType: "geometry",
            stylers: [{
              color: "#dfd2ae"
            }]
          }, {
            featureType: "transit.line",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#8f7d77"
            }]
          }, {
            featureType: "transit.line",
            elementType: "labels.text.stroke",
            stylers: [{
              color: "#ebe3cd"
            }]
          }, {
            featureType: "transit.station",
            elementType: "geometry",
            stylers: [{
              color: "#dfd2ae"
            }]
          }, {
            featureType: "water",
            elementType: "geometry.fill",
            stylers: [{
              color: "#b9d3c2"
            }]
          }, {
            featureType: "water",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#92998d"
            }]
          }],
          dark: [{
            elementType: "geometry",
            stylers: [{
              color: "#212121"
            }]
          }, {
            elementType: "labels.icon",
            stylers: [{
              visibility: "off"
            }]
          }, {
            elementType: "labels.text.fill",
            stylers: [{
              color: "#757575"
            }]
          }, {
            elementType: "labels.text.stroke",
            stylers: [{
              color: "#212121"
            }]
          }, {
            featureType: "administrative",
            elementType: "geometry",
            stylers: [{
              color: "#757575"
            }]
          }, {
            featureType: "administrative.country",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#9e9e9e"
            }]
          }, {
            featureType: "administrative.land_parcel",
            stylers: [{
              visibility: "off"
            }]
          }, {
            featureType: "administrative.locality",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#bdbdbd"
            }]
          }, {
            featureType: "poi",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#757575"
            }]
          }, {
            featureType: "poi.park",
            elementType: "geometry",
            stylers: [{
              color: "#181818"
            }]
          }, {
            featureType: "poi.park",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#616161"
            }]
          }, {
            featureType: "poi.park",
            elementType: "labels.text.stroke",
            stylers: [{
              color: "#1b1b1b"
            }]
          }, {
            featureType: "road",
            elementType: "geometry.fill",
            stylers: [{
              color: "#2c2c2c"
            }]
          }, {
            featureType: "road",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#8a8a8a"
            }]
          }, {
            featureType: "road.arterial",
            elementType: "geometry",
            stylers: [{
              color: "#373737"
            }]
          }, {
            featureType: "road.highway",
            elementType: "geometry",
            stylers: [{
              color: "#3c3c3c"
            }]
          }, {
            featureType: "road.highway.controlled_access",
            elementType: "geometry",
            stylers: [{
              color: "#4e4e4e"
            }]
          }, {
            featureType: "road.local",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#616161"
            }]
          }, {
            featureType: "transit",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#757575"
            }]
          }, {
            featureType: "water",
            elementType: "geometry",
            stylers: [{
              color: "#000000"
            }]
          }, {
            featureType: "water",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#3d3d3d"
            }]
          }],
          night: [{
            elementType: "geometry",
            stylers: [{
              color: "#242f3e"
            }]
          }, {
            elementType: "labels.text.fill",
            stylers: [{
              color: "#746855"
            }]
          }, {
            elementType: "labels.text.stroke",
            stylers: [{
              color: "#242f3e"
            }]
          }, {
            featureType: "administrative.locality",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#d59563"
            }]
          }, {
            featureType: "poi",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#d59563"
            }]
          }, {
            featureType: "poi.park",
            elementType: "geometry",
            stylers: [{
              color: "#263c3f"
            }]
          }, {
            featureType: "poi.park",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#6b9a76"
            }]
          }, {
            featureType: "road",
            elementType: "geometry",
            stylers: [{
              color: "#38414e"
            }]
          }, {
            featureType: "road",
            elementType: "geometry.stroke",
            stylers: [{
              color: "#212a37"
            }]
          }, {
            featureType: "road",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#9ca5b3"
            }]
          }, {
            featureType: "road.highway",
            elementType: "geometry",
            stylers: [{
              color: "#746855"
            }]
          }, {
            featureType: "road.highway",
            elementType: "geometry.stroke",
            stylers: [{
              color: "#1f2835"
            }]
          }, {
            featureType: "road.highway",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#f3d19c"
            }]
          }, {
            featureType: "transit",
            elementType: "geometry",
            stylers: [{
              color: "#2f3948"
            }]
          }, {
            featureType: "transit.station",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#d59563"
            }]
          }, {
            featureType: "water",
            elementType: "geometry",
            stylers: [{
              color: "#17263c"
            }]
          }, {
            featureType: "water",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#515c6d"
            }]
          }, {
            featureType: "water",
            elementType: "labels.text.stroke",
            stylers: [{
              color: "#17263c"
            }]
          }],
          aubergine: [{
            elementType: "geometry",
            stylers: [{
              color: "#1d2c4d"
            }]
          }, {
            elementType: "labels.text.fill",
            stylers: [{
              color: "#8ec3b9"
            }]
          }, {
            elementType: "labels.text.stroke",
            stylers: [{
              color: "#1a3646"
            }]
          }, {
            featureType: "administrative.country",
            elementType: "geometry.stroke",
            stylers: [{
              color: "#4b6878"
            }]
          }, {
            featureType: "administrative.land_parcel",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#64779e"
            }]
          }, {
            featureType: "administrative.province",
            elementType: "geometry.stroke",
            stylers: [{
              color: "#4b6878"
            }]
          }, {
            featureType: "landscape.man_made",
            elementType: "geometry.stroke",
            stylers: [{
              color: "#334e87"
            }]
          }, {
            featureType: "landscape.natural",
            elementType: "geometry",
            stylers: [{
              color: "#023e58"
            }]
          }, {
            featureType: "poi",
            elementType: "geometry",
            stylers: [{
              color: "#283d6a"
            }]
          }, {
            featureType: "poi",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#6f9ba5"
            }]
          }, {
            featureType: "poi",
            elementType: "labels.text.stroke",
            stylers: [{
              color: "#1d2c4d"
            }]
          }, {
            featureType: "poi.park",
            elementType: "geometry.fill",
            stylers: [{
              color: "#023e58"
            }]
          }, {
            featureType: "poi.park",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#3C7680"
            }]
          }, {
            featureType: "road",
            elementType: "geometry",
            stylers: [{
              color: "#304a7d"
            }]
          }, {
            featureType: "road",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#98a5be"
            }]
          }, {
            featureType: "road",
            elementType: "labels.text.stroke",
            stylers: [{
              color: "#1d2c4d"
            }]
          }, {
            featureType: "road.highway",
            elementType: "geometry",
            stylers: [{
              color: "#2c6675"
            }]
          }, {
            featureType: "road.highway",
            elementType: "geometry.stroke",
            stylers: [{
              color: "#255763"
            }]
          }, {
            featureType: "road.highway",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#b0d5ce"
            }]
          }, {
            featureType: "road.highway",
            elementType: "labels.text.stroke",
            stylers: [{
              color: "#023e58"
            }]
          }, {
            featureType: "transit",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#98a5be"
            }]
          }, {
            featureType: "transit",
            elementType: "labels.text.stroke",
            stylers: [{
              color: "#1d2c4d"
            }]
          }, {
            featureType: "transit.line",
            elementType: "geometry.fill",
            stylers: [{
              color: "#283d6a"
            }]
          }, {
            featureType: "transit.station",
            elementType: "geometry",
            stylers: [{
              color: "#3a4762"
            }]
          }, {
            featureType: "water",
            elementType: "geometry",
            stylers: [{
              color: "#0e1626"
            }]
          }, {
            featureType: "water",
            elementType: "labels.text.fill",
            stylers: [{
              color: "#4e6d70"
            }]
          }]
        };
        return defaultStyles;
      },
      settings: function settings(wrapper) {
        var item = document.querySelector(wrapper);
        var settings = {
          latitude: parseFloat(item.dataset.latitude),
          longitude: parseFloat(item.dataset.longitude),
          mapType: item.dataset.mapType,
          markers: item.dataset.markers,
          polylines: item.dataset.polylines,
          mapZoom: parseInt(item.dataset.mapZoom),
          mapZoomMobile: parseInt(item.dataset.mapZoomMobile),
          mapZoomTablet: parseInt(item.dataset.mapZoomTablet),
          scrollWheelZoom: item.dataset.scrollWheelZoom,
          zoomControl: item.dataset.zoomControl,
          fullscreenControl: item.dataset.fullScreen,
          mapDraggable: item.dataset.disableMapDrag,
          showLegend: item.dataset.showLegend,
          showRoute: item.dataset.showRoute,
          googleMapType: item.dataset.googleMapType,
          mapTypeControl: item.dataset.mapTypeControl,
          streetViewControl: item.dataset.streetView,
          infoOpen: item.dataset.infoOpen,
          infoOpenHover: item.dataset.infoOpenHover,
          mapStyle: item.dataset.mapStyle,
          customMapStyles: item.dataset.customMapStyle,
          defaultStyle: this.defaultStyle(),
          strokeColor: item.dataset.strokeColor,
          strokeOpacity: item.dataset.strokeOpacity,
          strokeWeight: item.dataset.strokeWeight,
          strokeFill: item.dataset.strokeFill,
          strokeFillOpacity: item.dataset.strokeFillOpacity,
          originLat: item.dataset.originLat,
          originLng: item.dataset.originLng,
          destLat: item.dataset.destLat,
          destLng: item.dataset.destLng,
          travelMode: item.dataset.travelMode
        };
        return settings;
      },
      isValidJsonString: function isValidJsonString(jsonString) {
        try {
          JSON.parse(jsonString);
        } catch (e) {
          return false;
        }

        return true;
      },
      mapInit: function mapInit(wrapper) {
        var settings = this.settings(wrapper);
        var markersOptionsArray = JSON.parse(settings.markers);
        var map;
        var directionsService = new google.maps.DirectionsService();
        var directionsRenderer = new google.maps.DirectionsRenderer();
        var finalZoomLevel = settings.mapZoom;
        window.addEventListener("resize", function () {
          if (elementorFrontend.getCurrentDeviceMode() == "mobile") {
            finalZoomLevel = settings.mapZoomMobile;
          } else if (elementorFrontend.getCurrentDeviceMode() == "tablet") {
            finalZoomLevel = settings.mapZoomTablet;
          } else {
            finalZoomLevel = settings.mapZoom;
          }
        });
        map = new google.maps.Map(document.querySelector(".ha-adv-google-map__wrapper" + wrapper), {
          center: {
            lat: settings.latitude === "" ? -34.391 : settings.latitude,
            lng: settings.longitude === "" ? 150.644 : settings.longitude
          },
          zoom: finalZoomLevel,
          mapTypeId: settings.mapType,
          zoomControl: settings.zoomControl === "yes",
          mapTypeControl: settings.mapTypeControl === "yes",
          fullscreenControl: settings.fullscreenControl === "yes",
          streetViewControl: settings.streetViewControl === "yes",
          gestureHandling: settings.scrollWheelZoom === "yes" ? "greedy" : "cooperative",
          draggable: settings.mapDraggable !== "yes",
          styles: settings.mapStyle !== "custom" ? settings.defaultStyle[settings.mapStyle] : !this.isValidJsonString(settings.customMapStyles) ? "" : JSON.parse(settings.customMapStyles)
        });

        if (settings.mapDraggable === "yes") {
          map.setOptions({
            draggableCursor: "default"
          });
        } // adding markers and info window on map


        markersOptionsArray.forEach(function (option) {
          var marker = new google.maps.Marker({
            icon: option.pin_icon.url === "" ? "" : {
              url: option.pin_icon.url,
              scaledSize: new google.maps.Size(option.rectangular_image === "yes" ? +option.rectangular_pin_size_width.size : +option.square_pin_size.size, option.rectangular_image === "yes" ? +option.rectangular_pin_size_height.size : +option.square_pin_size.size)
            },
            map: map,
            position: {
              lat: +option.pin_latitude,
              lng: +option.pin_longitude
            }
          });
          var infoWindow = new google.maps.InfoWindow({
            content: '<div class="ha-adv-google-map__marker-wrapper">' + '<div class="ha-adv-google-map__marker-title-wrapper">' + '<div class="ha-adv-google-map__marker-title" style="display: inline-block">' + option.pin_item_title + "</div>" + "</div>" + '<div class="ha-adv-google-map__marker-description-wrapper">' + '<div class="ha-adv-google-map__marker-description">' + option.pin_item_description + "</div>" + "</div>" + "</div>"
          });

          if (settings.infoOpen === "yes") {
            infoWindow.open(map, marker);
          }

          if (settings.infoOpenHover === "yes") {
            marker.addListener("mouseover", function (e) {
              infoWindow.open(map, marker);
            });
            marker.addListener("mouseout", function (e) {
              var timeOut = setTimeout(function () {
                infoWindow.close();
                clearTimeout(timeOut);
              }, 1000);
            });
          } else {
            marker.addListener("click", function () {
              infoWindow.open(map, marker);
            });
          }
        }); // adding map legend

        if (settings.showLegend === "yes") {
          var legend = document.querySelector(wrapper).nextElementSibling;
          map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);
        }

        if (settings.googleMapType == "polyline") {
          var polylines_data = JSON.parse(settings.polylines);
          var polylines_arr = [];
          polylines_data.forEach(function (polyline) {
            polylines_arr.push({
              lat: parseFloat(polyline.ha_gmap_polyline_lat),
              lng: parseFloat(polyline.ha_gmap_polyline_lng)
            });
          });
          var polyLinePath = JSON.parse(JSON.stringify(polylines_arr));
          var polyPath = new google.maps.Polyline({
            path: polyLinePath,
            strokeColor: settings.strokeColor,
            strokeOpacity: settings.strokeOpacity,
            strokeWeight: settings.strokeWeight
          });
          polyPath.setMap(map);
        }

        if (settings.googleMapType == "polygon") {
          var polylines_data = JSON.parse(settings.polylines);
          var polylines_arr = [];
          polylines_data.forEach(function (polyline) {
            polylines_arr.push({
              lat: parseFloat(polyline.ha_gmap_polyline_lat),
              lng: parseFloat(polyline.ha_gmap_polyline_lng)
            });
          });
          var polyLinePath = JSON.parse(JSON.stringify(polylines_arr));
          var polygon = new google.maps.Polygon({
            path: polyLinePath,
            strokeColor: settings.strokeColor,
            strokeOpacity: settings.strokeOpacity,
            strokeWeight: settings.strokeWeight,
            fillColor: settings.strokeFill,
            fillOpacity: settings.strokeFillOpacity
          });
          polygon.setMap(map);
        }

        if (settings.googleMapType == "routes") {
          this.routeDraw(directionsService, directionsRenderer, settings);
          directionsRenderer.setMap(map);
        }

        google.maps.event.addDomListener(window, "resize", function () {
          var center = map.getCenter();
          google.maps.event.trigger(map, "resize");
          map.setCenter(center);
          map.setZoom(finalZoomLevel);
        });
      },
      routeDraw: function routeDraw(directionsService, directionsRenderer, settings) {
        var start = settings.originLat + "," + settings.originLng;
        var end = settings.destLat + "," + settings.destLng;
        directionsService.route({
          origin: {
            query: start
          },
          destination: {
            query: end
          },
          travelMode: settings.travelMode.toUpperCase()
        }, function (response, status) {
          if (status === "OK") {
            directionsRenderer.setDirections(response);
          } else {}
        });
      },
      runMap: function runMap() {
        var gmaper = document.querySelectorAll(".ha-adv-google-map__wrapper");

        for (var i = 0; i < gmaper.length; i++) {
          gmaper[i].classList.add("ha-adv-google-map__wrapper--item" + i);
          this.mapInit(".ha-adv-google-map__wrapper--item" + i);
        }
      }
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-google-map.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(Maps, {
        $element: $scope,
        selectors: {
          container: ".ha-adv-google-map__wrapper"
        }
      });
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-unfold.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(Unfold, {
        $element: $scope
      });
    });

    var EddCart = function EddCart($scope) {
      var optionEL = $scope.find('.ha-edd-table-wrap');
      var settings = optionEL.data('options');
      var removeEl = $scope.find('.edd-remove-from-cart');

      if (settings.cart_btn_type == 'icon') {
        removeEl.html("<i class=\"".concat(settings.icon.value, "\"></i>"));
      } else {
        removeEl.text(settings.btn_text);
      }
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/ha-edd-cart.default', EddCart);

    var EddCheckout = function EddCheckout($scope) {
      var optionEL = $scope.find('.ha-edd-table-wrap');
      var settings = optionEL.data('options');
      var removeEl = $scope.find('.edd_cart_remove_item_btn');

      if (settings.cart_btn_type == 'icon') {
        removeEl.html("<i class=\"".concat(settings.icon.value, "\"></i>"));
      } else {
        removeEl.text(settings.btn_text);
      }
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/ha-edd-checkout.default', EddCheckout);

    var EddAjaxBtn = function EddAjaxBtn($scope) {
      var btn = $scope.find(".ha_edd_ajax_btn");
      btn.on('click', function (event) {
        event.preventDefault();
        var $self = $(this),
            download_id = $self.data('download-id'),
            nonce = $self.data('nonce');
        $.ajax({
          url: HappyProLocalize.ajax_url,
          type: "POST",
          data: {
            action: "ha_edd_ajax_add_to_cart_link",
            security: HappyProLocalize.nonce,
            download_id: download_id
          },
          success: function success(response) {
            if (response.success == true) {
              $self.html("<i class=\"fas fa-cart-plus\"></i>");
            }
          }
        });
      });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/ha-edd-product-grid.default', EddAjaxBtn);
    elementorFrontend.hooks.addAction('frontend/element_ready/ha-edd-product-carousel.default', EddAjaxBtn);
    elementorFrontend.hooks.addAction('frontend/element_ready/ha-edd-single-product.default', EddAjaxBtn);

    var Image_Swap = function Image_Swap($scope) {
      var element = $scope.find('.ha-image-swap-wrapper');
      var trigger_type = element.data('trigger');
      var status;

      if (trigger_type === 'click') {
        element.on('click', element, function () {
          var self = $(this);
          var click = self.attr('data-click');

          if ('inactive' == click) {
            status = 'active';
          } else {
            status = 'inactive';
          }

          self.attr('data-click', status);
          var first_img = self.find('.img_swap_first');
          var second_img = self.find('.img_swap_second');
        });
      }

      var elm = $scope.find('.ha-image-swap-ctn');
      var elm_img = $scope.find('.ha-image-swap-item');
      var trigger = elm.data('trigger');

      if (elm.length > 0) {
        $('.ha-image-swap-ctn').on('click', '.ha-image-swap-item', function (e) {
          var elem = $(this);
          var parent = elem.parent('.ha-image-swap-insider');
          var firstChild = parent.children('.ha-image-swap-item:first-of-type');
          var secondChild = parent.children('.ha-image-swap-item:nth-of-type(2)');

          if (!elem.is(':first-child')) {
            parent.addClass('changed');
            setTimeout(function () {
              firstChild.remove();
              parent.append(firstChild);
              parent.removeClass('changed');
            }, 400);
          }
        });
      } // if( trigger == 'hover'){
      //     elm.hover(function (e){
      //         var target = e.target;
      //         if(!$(target).hasClass('active')){
      //             var $self = $(this);
      //             var parent = $self.find('.ha-image-swap-insider');
      //             var firstChild = parent.children('.ha-image-swap-item:first-of-type');
      //             var secondChild = parent.children('.ha-image-swap-item:nth-of-type(2)');
      //             firstChild.toggleClass('active');
      //             secondChild.toggleClass('active');
      //         }
      //     });
      // }

    };

    var FlipBox = function FlipBox($scope) {
      var element = $scope.find('.ha-flip-box-inner'); // touchstart

      element.on('mouseenter mouseleave touchstart', function (e) {
        if (('mouseenter' == e.type || 'touchstart' == e.type) && !$(this).hasClass('flip')) {
          $(this).addClass('flip');
        } else if (('mouseleave' == e.type || 'touchstart' == e.type) && $(this).hasClass('flip')) {
          $(this).removeClass('flip');
        } else if ('touchstart' == e.type && $(this).hasClass('flip')) {
          $(this).removeClass('flip');
        }
      });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/ha-image-swap.default', Image_Swap);

    var FreeShippingBar = function FreeShippingBar($scope) {
      var $fsbContainer = $scope.find('.ha-fsb-container');

      if ($fsbContainer) {
        var $settings = $fsbContainer.data('fsb_settings');

        if ($settings != undefined) {
          var $aimationSpeed = $settings.hasOwnProperty('ha_fsb_animation_speed') ? $settings.ha_fsb_animation_speed : 15;
          var $achivePercent = $settings.hasOwnProperty('achive_percent') ? $settings.achive_percent : 0;
          var $progressType = $settings.hasOwnProperty('progress_type') ? $settings.progress_type : 'percent';
          var $currencySymbol = $settings.hasOwnProperty('currencySymbol') && $progressType == 'percent' ? '%' : $settings.currencySymbol;
          var $cartTotal = $settings.hasOwnProperty('cart_total') ? $settings.cart_total : 0;
          var $totalPercent = parseFloat(Math.round($achivePercent)) >= 100 ? 100 : parseFloat(Math.round($achivePercent));
          var $progressText = $progressType == 'percent' ? $totalPercent : $cartTotal;
          var $elem = $fsbContainer.find('.ha-fsb-size');
          $elem.text($progressText + $currencySymbol);
          var defaultWidth = $(window).width() <= 768 ? 12 : 5;
          $totalPercent > 0 ? $elem.css({
            'width': $totalPercent + "%",
            'transition': 'width ' + $aimationSpeed + 'ms ease-in-out'
          }) : $elem.css('width', +defaultWidth + "%");
        }
      }
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/ha-shipping-bar.default', FreeShippingBar);
    $('.fsb-close-icon').on('click', function () {
      document.cookie = "ha_close_fsb = yes;path=/";
      $('.ha-fsb-container').hide();
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/ha-flip-box.default', FlipBox);

    var HaRemoteCarousel = function HaRemoteCarousel($scope) {
      var $rcContainer = $scope.find('.ha-remote-carousel-container');

      if ($rcContainer) {
        var $rcBtn = $($rcContainer).find('.ha-custom-nav-remote-carousel');

        if ($rcBtn) {
          var $prevSliderIndex = 0;
          var $slides_to_show = 0;
          var $ha_show_thumbnail = $($rcBtn).parent().data('show_thumbnail');
          var $ha_rc_unique_id = $($rcBtn).parent().data('ha_rc_id');
          var $haRcSelector = $ha_rc_unique_id ? $('[data-ha_rcc_uid="' + $ha_rc_unique_id + '"]') : '';

          if ($ha_show_thumbnail && $ha_show_thumbnail == 'yes') {
            $haRcSelector.on('init', function (event, slick, currentSlide) {
              var $dataSettingsSelector = $haRcSelector.parent().parent();
              var $dataSettings = $dataSettingsSelector.data('settings');
              $slides_to_show = typeof $dataSettings === "undefined" ? $slides_to_show : $dataSettings.slides_to_show;
              $prevSliderIndex = (currentSlide ? currentSlide : 0) - 1;
              var $nextIndex = parseInt($slides_to_show) + parseInt($prevSliderIndex) + 1;
              var $nextItem = $haRcSelector.find('[data-slick-index="' + $nextIndex + '"]');
              var $nextImg = $nextItem.find('img').attr('src');
              var $prevItem = $haRcSelector.find('[data-slick-index="' + $prevSliderIndex + '"]');
              var $prevImg = $prevItem.find('img').attr('src');
              var $nextBtn = $rcContainer.find('.ha-remote-carousel-btn-next');
              var $prevBtn = $rcContainer.find('.ha-remote-carousel-btn-prev');
              $nextBtn.css({
                'background-image': 'url(' + $nextImg + ')',
                'background-position': 'center'
              });
              $prevBtn.css({
                'background-image': 'url(' + $prevImg + ')',
                'background-position': 'center'
              });
            });
            $haRcSelector.trigger('init');
            $($rcBtn).on("click", function (e) {
              e.preventDefault();
              $haRcSelector.on('afterChange', function (event, slick, currentSlideIndex) {
                var $dataSettingsSelector = $haRcSelector.parent().parent();
                var $dataSettings = $dataSettingsSelector.data('settings');
                var $slides_to_show = $dataSettings ? $dataSettings.slides_to_show : currentSlideIndex;
                $prevSliderIndex = parseInt(currentSlideIndex - 1);
                var $nextIndex = parseInt($slides_to_show) + parseInt($prevSliderIndex) + 1;
                var $nextItem = $haRcSelector.find('[data-slick-index="' + $nextIndex + '"]');
                var $nextImg = $nextItem.find('img').attr('src');
                var $prevItem = $haRcSelector.find('[data-slick-index="' + $prevSliderIndex + '"]');
                var $prevImg = $prevItem.find('img').attr('src');
                var $nextBtn = $rcContainer.find('.ha-remote-carousel-btn-next');
                var $prevBtn = $rcContainer.find('.ha-remote-carousel-btn-prev');

                if ($nextBtn) {
                  $nextBtn.css({
                    'background-image': 'url(' + $nextImg + ')',
                    'background-position': 'center'
                  });
                }

                if ($prevBtn) {
                  $prevBtn.css({
                    'background-image': 'url(' + $prevImg + ')',
                    'background-position': 'center'
                  });
                }
              });

              if ($(this).data("ha_rc_nav") == 'ha_rc_next_btn') {
                $haRcSelector.slick("slickNext");
              } else if ($(this).data("ha_rc_nav") == 'ha_rc_prev_btn') {
                $haRcSelector.slick("slickPrev");
              }
            });
          } else {
            $($rcBtn).on("click", function (e) {
              e.preventDefault();

              if ($(this).data("ha_rc_nav") == 'ha_rc_next_btn') {
                $haRcSelector.slick("slickNext");
              } else if ($(this).data("ha_rc_nav") == 'ha_rc_prev_btn') {
                $haRcSelector.slick("slickPrev");
              }
            });
          }
        }
      }
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/ha-remote-carousel.default', HaRemoteCarousel);
    var HaTOC = elementorModules.frontend.handlers.Base.extend({
      getDefaultSettings: function getDefaultSettings() {
        var elementSettings = this.getElementSettings(),
            listWrapperTag = "numbers" === elementSettings.marker_view ? "ol" : "ul";
        return {
          selectors: {
            widgetContainer: ".ha-toc-wrapper",
            container: '.elementor:not([data-elementor-type="header"]):not([data-elementor-type="footer"])',
            expandButton: ".ha-toc__header",
            collapseButton: ".ha-toc__header",
            body: ".ha-toc__body",
            headerTitle: ".ha-toc__header-title",
            scrollTop: ".ha-toc__scroll-to-top--container"
          },
          classes: {
            anchor: "ha-toc-menu-anchor",
            listWrapper: "ha-toc__list-wrapper",
            listItem: "ha-toc__list-item",
            listTextWrapper: "ha-toc__list-item-text-wrapper",
            firstLevelListItem: "ha-toc__top-level",
            listItemText: "ha-toc__list-item-text",
            activeItem: "ha-toc-item-active",
            headingAnchor: "ha-toc__heading-anchor",
            collapsed: "ha-toc--collapsed"
          },
          listWrapperTag: listWrapperTag
        };
      },
      getDefaultElements: function getDefaultElements() {
        var settings = this.getSettings(),
            elementSettings = this.getElementSettings();
        return {
          $pageContainer: jQuery(elementSettings.container || settings.selectors.container),
          $widgetContainer: this.$element.find(settings.selectors.widgetContainer),
          $expandButton: this.$element.find(settings.selectors.expandButton),
          $collapseButton: this.$element.find(settings.selectors.collapseButton),
          $tocBody: this.$element.find(settings.selectors.body),
          $listItems: this.$element.find("." + settings.classes.listItem),
          $scrollTop: this.$element.find(settings.selectors.scrollTop)
        };
      },
      bindEvents: function bindEvents() {
        var $self = this;
        var elementSettings = this.getElementSettings();

        if (elementSettings.minimize_box) {
          this.elements.$expandButton.on("click", function () {
            if (!$($self.$element).hasClass($self.getSettings("classes.collapsed"))) {
              return $self.collapseBox();
            } else {
              return $self.expandBox();
            }
          });
        }

        if (elementSettings.collapse_subitems) {
          this.elements.$listItems.hover(function (event) {
            return jQuery(event.target).slideToggle();
          });
        }

        if (elementSettings.sticky_toc_toggle) {
          elementorFrontend.elements.$window.on("resize", this.handleStickyToc);
        }

        if (elementSettings.scroll_to_top_toggle) {
          this.elements.$scrollTop.on("click", function () {
            $self.scrollToTop();
          });
        }
      },
      getHeadings: function getHeadings() {
        // Get all headings from document by user-selected tags
        var elementSettings = this.getElementSettings(),
            tags = elementSettings.headings_by_tags.join(","),
            selectors = this.getSettings("selectors"),
            excludedSelectors = elementSettings.exclude_headings_by_selector;
        return this.elements.$pageContainer.find(tags).not(selectors.headerTitle).filter(function (index, heading) {
          return !jQuery(heading).closest(excludedSelectors).length; // Handle excluded selectors if there are any
        });
      },
      addAnchorsBeforeHeadings: function addAnchorsBeforeHeadings() {
        var $this = this; // Add an anchor element right before each TOC heading to create anchors for TOC links

        var classes = this.getSettings("classes");
        this.elements.$headings.before(function (index) {
          var anchorLink = $this.getHeadingAnchorLink(index, classes);
          return '<span id="' + anchorLink + '" class="' + classes.anchor + ' "></span>';
        });
      },
      activateItem: function activateItem($listItem) {
        var classes = this.getSettings("classes");
        this.deactivateActiveItem($listItem);
        $listItem.addClass(classes.activeItem);
        this.$activeItem = $listItem;

        if (!this.getElementSettings("sticky_toc_toggle")) {
          //Note:- The "Collapse" option will not work unless you make the Table of Contents sticky.
          return;
        }

        if (!this.getElementSettings("collapse_subitems")) {
          return;
        }

        var $activeList = void 0;

        if ($listItem.hasClass(classes.firstLevelListItem)) {
          $activeList = $listItem.parent().next();
        } else {
          $activeList = $listItem.parents("." + classes.listWrapper).eq(-2);
        }

        if (!$activeList.length) {
          delete this.$activeList;
          return;
        }

        this.$activeList = $activeList;
        this.$activeList.stop().slideDown();
      },
      deactivateActiveItem: function deactivateActiveItem($activeToBe) {
        if (!this.$activeItem || this.$activeItem.is($activeToBe)) {
          return;
        }

        var _getSettings = this.getSettings(),
            classes = _getSettings.classes;

        this.$activeItem.removeClass(classes.activeItem);

        if (this.$activeList && (!$activeToBe || !this.$activeList[0].contains($activeToBe[0]))) {
          this.$activeList.slideUp();
        }
      },
      followAnchor: function followAnchor($element, index) {
        var $self = this;
        var anchorSelector = $element[0].hash;
        var $anchor = void 0;

        try {
          // `decodeURIComponent` for UTF8 characters in the hash.
          $anchor = jQuery(decodeURIComponent(anchorSelector));
        } catch (e) {
          return;
        }

        if (0 === index) {
          elementorFrontend.waypoint($anchor, function (direction) {
            if ($self.itemClicked) {
              return;
            }

            if ("down" === direction) {
              $self.activateItem($element);
            } else {
              $self.deactivateActiveItem();
            }
          }, {
            offset: "bottom-in-view",
            triggerOnce: false
          });
        }

        elementorFrontend.waypoint($anchor, function (direction) {
          if ($self.itemClicked) {
            return;
          }

          if ("down" === direction) {
            $self.activateItem($self.$listItemTexts.eq(index + 1));
          } else {
            $self.activateItem($element);
          }
        }, {
          offset: 0,
          triggerOnce: false
        });
      },
      followAnchors: function followAnchors() {
        var $self = this;
        this.$listItemTexts.each(function (index, element) {
          return $self.followAnchor(jQuery(element), index);
        });
      },
      setOffset: function setOffset($listItem) {
        var $this = this;
        var settings = this.getSettings();
        var list = this.$element.find("." + settings.classes.listItem);
        var offset = this.getCurrentDeviceSetting("scroll_offset");
        list.each(function () {
          $('a', this).on('click', function (e) {
            e.preventDefault();
            var hash = this.hash;
            $('html, body').animate({
              scrollTop: $(hash).offset().top - parseInt(offset.size)
            }, 800);
          });
        });
      },
      populateTOC: function populateTOC() {
        var $self = this;
        this.listItemPointer = 0;
        var elementSettings = this.getElementSettings();

        if (elementSettings.custom_style == 'yes') {
          this.elements.$widgetContainer.html(this.createSideDot());
        } else {
          if (elementSettings.hierarchical_view) {
            this.createNestedList();
          } else {
            this.createFlatList();
          }
        }

        this.$listItemTexts = this.$element.find(".ha-toc__list-item-text");
        this.$listItemTexts.on("click", this.onListItemClick.bind(this));

        if (!elementorFrontend.isEditMode()) {
          this.followAnchors();
        }

        $(window).on('scroll', function () {
          if ("window_top" === elementSettings.scroll_to_top_option) {
            if ($(window).scrollTop() > 0) {
              $self.elements.$scrollTop.show();
            } else {
              $self.elements.$scrollTop.hide();
            }
          } else {
            var $id = $self.getID().parents('.elementor-widget-wrap');

            if ($id.offset().top >= $(window).scrollTop()) {
              $self.elements.$scrollTop.hide();
            } else {
              $self.elements.$scrollTop.show();
            }
          }
        });
      },
      createNestedList: function createNestedList() {
        var $self = this;
        this.headingsData.forEach(function (heading, index) {
          heading.level = 0;

          for (var i = index - 1; i >= 0; i--) {
            var currentOrderedItem = $self.headingsData[i];

            if (currentOrderedItem.tag <= heading.tag) {
              heading.level = currentOrderedItem.level;

              if (currentOrderedItem.tag < heading.tag) {
                heading.level++;
              }

              break;
            }
          }
        });
        this.elements.$tocBody.html(this.getNestedLevel(0));
      },
      createFlatList: function createFlatList() {
        this.elements.$tocBody.html(this.getNestedLevel());
      },
      createSideDot: function createSideDot(level) {
        var settings = this.getSettings(),
            elementSettings = this.getElementSettings(),
            icon = this.getElementSettings("icon"),
            htmlul = '';

        if (elementSettings.custom_style == 'yes') {
          htmlul += "<div class=\"".concat(elementSettings.custom_style_list, "\">\n                    <div class=\"hm-toc \">\n                    <ul class=\"hm-toc-items\" style=\"list-style: none;\">\n                        <li>");

          if (elementSettings.custom_style_list == 'hm-toc-slide-style') {
            htmlul += "<".concat(elementSettings.html_tag, " class=\"hm-toc-title\"><span>").concat(elementSettings.widget_title, "<span></").concat(elementSettings.html_tag, ">");
          } else {
            htmlul += "<".concat(elementSettings.html_tag, " class=\"hm-toc-title\">").concat(elementSettings.widget_title, "</").concat(elementSettings.html_tag, ">");
          }

          htmlul += "<".concat(settings.listWrapperTag, " class=\"hm-toc-items-inner\">");

          for (var i = 0; i < this.headingsData.length; i++) {
            var list = '';
            var currentItemDot = this.headingsData[i];

            if (level > currentItemDot.level) {
              break;
            }

            var listItemTextClasses = settings.classes.listItemText;

            if (0 === currentItemDot.level) {
              // If the current list item is a top level item, give it the first level class
              listItemTextClasses += " " + settings.classes.firstLevelListItem;
            }

            if (level === currentItemDot.level) {
              list += "<li class=\"hm-toc-entry ".concat(settings.classes.listItem, "\">");

              if ('bullets' === elementSettings.marker_view && icon && elementSettings.custom_style_list == 'hm-toc-slide-style') {
                list += "<i class=\"".concat(icon.value, "\"></i>");
              }

              list += "<a href=\"#".concat(currentItemDot.anchorLink, "\" class=\"").concat(listItemTextClasses, "\"><span>").concat(currentItemDot.text, "</span></a>");
              list += "</li>";
            }

            htmlul += list;
          }

          htmlul += "</".concat(settings.listWrapperTag, ">");
          htmlul += "</li>\n                    </ul>";
        }

        return htmlul;
      },
      getNestedLevel: function getNestedLevel(level) {
        var settings = this.getSettings(),
            elementSettings = this.getElementSettings(),
            icon = this.getElementSettings("icon");
        var html = ''; // Open new list/nested list

        html += "<" + settings.listWrapperTag + ' class="' + settings.classes.listWrapper + '">'; // for each list item, build its markup.

        while (this.listItemPointer < this.headingsData.length) {
          var currentItem = this.headingsData[this.listItemPointer];
          var listItemTextClasses = settings.classes.listItemText;

          if (0 === currentItem.level) {
            // If the current list item is a top level item, give it the first level class
            listItemTextClasses += " " + settings.classes.firstLevelListItem;
          }

          if (level > currentItem.level) {
            break;
          }

          if (level === currentItem.level) {
            html += '<li class="' + settings.classes.listItem + " " + "level-" + level + '">';
            html += '<div class="' + settings.classes.listTextWrapper + '">';
            var liContent = '<a href="#' + currentItem.anchorLink + '" class="' + listItemTextClasses + '">' + currentItem.text + "</a>"; // If list type is bullets, add the bullet icon as an <i> tag

            if ("bullets" === elementSettings.marker_view && icon) {
              liContent = '<i class="' + icon.value + '"></i>' + liContent;
            }

            html += liContent;
            html += "</div>";
            this.listItemPointer++;
            var nextItem = this.headingsData[this.listItemPointer];

            if (nextItem && level < nextItem.level) {
              // If a new nested list has to be created under the current item,
              // this entire method is called recursively (outside the while loop, a list wrapper is created)
              html += this.getNestedLevel(nextItem.level);
            }

            html += "</li>";
          }
        }

        html += "</" + settings.listWrapperTag + ">";
        return html;
      },
      handleNoHeadingsFound: function handleNoHeadingsFound() {
        var _messages = 'No headings were found on this page.';

        if (elementorFrontend.isEditMode()) {
          return this.elements.$tocBody.html(_messages);
        }
      },
      collapseOnInit: function collapseOnInit() {
        var $self = this;
        var minimizedOn = this.getElementSettings("minimized_on"),
            currentDeviceMode = elementorFrontend.getCurrentDeviceMode();

        if ("" !== minimizedOn && "array" !== typeof minimizedOn) {
          minimizedOn = [minimizedOn];
        }

        if (0 !== minimizedOn.length && "object" === _typeof(minimizedOn)) {
          minimizedOn.forEach(function (value) {
            if ("desktop" === value[0] && "desktop" == currentDeviceMode && $(window).width() < elementorFrontend.config.breakpoints.xxl || "tablet" === value[0] && "tablet" === currentDeviceMode && $(window).width() < elementorFrontend.config.breakpoints.lg || "mobile" === value[0] && "mobile" === currentDeviceMode && $(window).width() < elementorFrontend.config.breakpoints.md) {
              $self.collapseBox();
            }
          });
        }
      },
      getHeadingAnchorLink: function getHeadingAnchorLink(index, classes) {
        var headingID = this.elements.$headings[index].id,
            wrapperID = this.elements.$headings[index].closest('.elementor-widget').id;
        var anchorLink = '';

        if (headingID) {
          anchorLink = headingID;
        } else if (wrapperID) {
          // If the heading itself has an ID, we don't want to overwrite it
          anchorLink = wrapperID;
        } // If there is no existing ID, use the heading text to create a semantic ID


        if (headingID || wrapperID) {
          jQuery(this.elements.$headings[index]).data('hasOwnID', true);
        } else {
          anchorLink = "".concat(classes.headingAnchor, "-").concat(index);
        }

        return anchorLink;
      },
      setHeadingsData: function setHeadingsData() {
        var $this = this;
        this.headingsData = [];
        var classes = this.getSettings("classes"); // Create an array for simplifying TOC list creation

        this.elements.$headings.each(function (index, element) {
          var anchorLink = $this.getHeadingAnchorLink(index, classes);
          $this.headingsData.push({
            tag: +element.nodeName.slice(1),
            text: element.textContent,
            anchorLink: anchorLink
          });
        });
      },
      run: function run() {
        var elementSettings = this.getElementSettings();
        this.elements.$headings = this.getHeadings();

        if (!this.elements.$headings.length) {
          return this.handleNoHeadingsFound();
        }

        this.setHeadingsData();

        if (!elementorFrontend.isEditMode()) {
          this.addAnchorsBeforeHeadings();
        }

        this.populateTOC();

        if (elementSettings.minimize_box) {
          this.collapseOnInit();
        }

        if (elementSettings.sticky_toc_toggle) {
          this.handleStickyToc();
        }

        if ("" !== elementSettings.scroll_offset.size) {
          this.setOffset();
        }
      },
      expandBox: function expandBox() {
        var boxHeight = this.getCurrentDeviceSetting("min_height");
        this.$element.removeClass(this.getSettings("classes.collapsed"));
        this.elements.$tocBody.slideDown(); // return container to the full height in case a min-height is defined by the user

        this.elements.$widgetContainer.css("min-height", boxHeight.size + boxHeight.unit);
      },
      collapseBox: function collapseBox() {
        this.$element.addClass(this.getSettings("classes.collapsed"));
        this.elements.$tocBody.slideUp(); // close container in case a min-height is defined by the user

        this.elements.$widgetContainer.css("min-height", "0px");
      },
      onInit: function onInit() {
        var $self = this;
        this.initElements();
        this.bindEvents();
        jQuery(document).ready(function () {
          $self.run();
        });
      },
      onListItemClick: function onListItemClick(event) {
        var $self = this;
        this.itemClicked = true;
        setTimeout(function () {
          return $self.itemClicked = false;
        }, 2000);
        var $clickedItem = jQuery(event.target),
            $list = $clickedItem.parent().next(),
            collapseNestedList = this.getElementSettings("collapse_subitems");

        if ("SPAN" == $clickedItem[0].tagName && 'yes' == this.getElementSettings("custom_style")) {
          $clickedItem = jQuery(event.currentTarget);
        }

        var listIsActive = void 0;

        if (collapseNestedList && $clickedItem.hasClass(this.getSettings("classes.firstLevelListItem"))) {
          if ($list.is(":visible")) {
            listIsActive = true;
          }
        }

        this.activateItem($clickedItem);

        if (collapseNestedList && listIsActive) {
          $list.slideUp();
        }
      },
      handleStickyToc: function handleStickyToc() {
        var elementSettings = this.getElementSettings();
        var currentDeviceMode = elementorFrontend.getCurrentDeviceMode();
        var $devices = elementSettings.sticky_toc_disable_on;
        var target = this.getID();
        var type = elementSettings.sticky_toc_type;

        if ("in-place" === type) {
          var parentWidth = target.parent().parent().outerWidth();
          target.css("width", parentWidth);
        } else if ("custom-position" === type) {
          var parentWidth = target.parent().parent().outerWidth();
          target.css("width", parentWidth);
        }

        if (-1 == $.inArray(currentDeviceMode, $devices)) {
          target.removeClass('floating-toc');
          $(window).off('scroll', this.stickyScroll);
          return;
        }

        $(window).on('scroll', $.proxy(this.stickyScroll, this));
      },
      stickyScroll: function stickyScroll() {
        var target = this.getID();
        var elementSettings = this.getElementSettings();
        var item = document.querySelector(".ha-table-of-contents");
        var bound, tocHeight;
        bound = item.getBoundingClientRect();
        tocHeight = target.outerHeight();

        if (target.hasClass("floating-toc")) {
          target.parent().parent().css("height", tocHeight);
        } else {
          target.parent().parent().css("height", '');
        }

        if (bound.y + bound.height / 2 < 0) {
          if (target.hasClass('floating-toc')) {
            return;
          }

          target.addClass("floating-toc");
        } else {
          if (!target.hasClass('floating-toc')) {
            return;
          }

          target.removeClass("floating-toc");
        }
      },
      scrollToTop: function scrollToTop() {
        var $self = this;
        var scrollTo = this.getElementSettings("scroll_to_top_option");

        if ("window_top" === scrollTo) {
          $("html, body").animate({
            scrollTop: 0
          }, 250);
        } else {
          var $id = this.getID().parents('.ha-table-of-contents');
          $("html, body").animate({
            scrollTop: $($id).offset().top - 60
          }, 1000);
        }
      },
      getID: function getID() {
        return $("#ha-toc-" + this.$element[0].attributes["data-id"].nodeValue);
      }
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/ha-table-of-contents.default", function ($scope) {
      elementorFrontend.elementsHandler.addHandler(HaTOC, {
        $element: $scope
      });
    }); //creativeSlider

    var HaCreativeSlider = function HaCreativeSlider($scope) {
      var $rcContainer = $scope.find('.ha-creative-slider-container');

      if ($rcContainer) {
        var $haCsWidth = window.innerWidth;
        var $haCsNextSelector = parseInt($haCsWidth) <= 767 ? $rcContainer.find('.ha_cs_mobile_next') : $rcContainer.find('.ha_cc_next_wrapper');
        var $haCsPrevSelector = parseInt($haCsWidth) <= 767 ? $rcContainer.find('.ha_cs_mobile_prev') : $rcContainer.find('.ha_cc_prev_wrapper');
        var csTextItemDelay = parseInt($haCsWidth) <= 767 ? 40 : 30;
        var csImageItemDelay = 40;
        var csPrevItemsOwl = $rcContainer.find('.ha_cc_prev_items');
        var csTextItemsOwl = $rcContainer.find('.ha_cc_text_items');
        var csImageItemsOwl = $rcContainer.find('.ha_cc_inner_image_items');
        var csNextItemsOwl = $rcContainer.find('.ha_cc_next_items');
        var csPrevNavOptions = {
          loop: true,
          dots: false,
          nav: false,
          items: 1,
          autoplay: false,
          smartSpeed: 500,
          touchDrag: false,
          mouseDrag: false,
          rewind: true,
          startPosition: -1
        };
        var csNextNavOptions = {
          loop: true,
          dots: false,
          nav: false,
          items: 1,
          autoplay: false,
          smartSpeed: 500,
          touchDrag: false,
          mouseDrag: false,
          rewind: true,
          startPosition: 1
        };
        var csGeneralOptions = {
          loop: true,
          dots: false,
          nav: false,
          items: 1,
          autoplay: false,
          smartSpeed: 1000,
          touchDrag: false,
          mouseDrag: false,
          singleItem: true,
          animateOut: 'fadeOutRight',
          animateIn: 'fadeInLeft',
          rewind: true
        };
      }

      csPrevItemsOwl.owlCarousel(csPrevNavOptions);
      csNextItemsOwl.owlCarousel(csNextNavOptions);
      csTextItemsOwl.owlCarousel(csGeneralOptions);
      csImageItemsOwl.owlCarousel(csGeneralOptions);
      window.parent.addEventListener('message', function (e) {
        if ('ha_cs_reinit' == e.data) {
          csTextItemsOwl.owlCarousel('refresh');
          csImageItemsOwl.owlCarousel('refresh');
        }
      });
      $($haCsPrevSelector).click(function (e) {
        e.preventDefault();
        csPrevItemsOwl.trigger('prev.owl.carousel');
        csNextItemsOwl.trigger('prev.owl.carousel');
        setTimeout(function () {
          csTextItemsOwl.trigger('prev.owl.carousel');
        }, csTextItemDelay);
        setTimeout(function () {
          csImageItemsOwl.trigger('prev.owl.carousel');
        }, csTextItemDelay + csImageItemDelay);
      });
      $($haCsNextSelector).click(function (e) {
        e.preventDefault();
        csPrevItemsOwl.trigger('next.owl.carousel');
        csNextItemsOwl.trigger('next.owl.carousel');
        setTimeout(function () {
          csTextItemsOwl.trigger('next.owl.carousel');
        }, csTextItemDelay);
        setTimeout(function () {
          csImageItemsOwl.trigger('next.owl.carousel');
        }, csTextItemDelay + csImageItemDelay);
      });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/ha-creative-slider.default', HaCreativeSlider);
  });
})(jQuery, Happy, window);