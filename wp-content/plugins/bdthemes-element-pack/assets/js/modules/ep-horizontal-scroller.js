(function ($, elementor) {
  $(window).on("elementor/frontend/init", function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
      HorizontalScroller;

    HorizontalScroller = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },
      getDefaultSettings: function () {
        return {
          allowHTML: true,
        };
      },

      settings: function (key) {
        return this.getElementSettings("horizontal_scroller_" + key);
      },

      sectionJoiner: function () {
        const widgetID = this.$element.data("id"),
          sectionId = [],
          sectionList = this.settings("section_list"),
          widgetContainer = ".elementor-element-" + widgetID,
          widgetWrapper = widgetContainer + " .bdt-ep-hc-wrapper";

        sectionList.forEach((section) => {
          sectionId.push("#" + section.horizontal_scroller_section_id);
        });

        //check is id exists
        var haveIds = [];
        let elements;
        elements = document.querySelectorAll(".elementor-top-section");
        if (elements.length == 0) {
          elements = document.querySelectorAll(".elementor-element.e-con");
        }

        elements.forEach((element) => {
          var elementsWrapper = element.getAttribute("id");
          haveIds.push("#" + elementsWrapper);
        });
        function intersection(arr1, arr2) {
          var temp = [];
          for (var i in arr1) {
            var element = arr1[i];

            if (arr2.indexOf(element) > -1) {
              temp.push(element);
            }
          }
          return temp;
        }
        function multi_intersect() {
          var arrays = Array.prototype.slice.apply(arguments).slice(1);
          var temp = arguments[0];
          for (var i in arrays) {
            temp = intersection(arrays[i], temp);
            if (temp == []) {
              break;
            }
          }
          return temp;
        }
        var ids = multi_intersect(haveIds, sectionId).toString();
        var selectedIDs = document.querySelectorAll(ids);
        $(widgetWrapper).append(selectedIDs);
      },

      horizontalScroller: function () {
        gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);
        let sections;
        const widgetID = this.$element.data("id"),
          widgetContainer = ".elementor-element-" + widgetID,
          widgetWrapper = widgetContainer + " .bdt-ep-hc-wrapper",
          scroller = document.querySelector(widgetWrapper),
          navLis = document.querySelectorAll(widgetContainer + " nav li");

        sections = gsap.utils.toArray(
          widgetContainer + " .elementor-top-section"
        );
        if (Object.keys(sections).length === 0) {
          sections = gsap.utils.toArray(
            widgetContainer + " .elementor-element.e-con"
          );
        }

        let numSections = sections.length - 1;
        let snapVal = 1 / numSections;
        let optionSnap;
        if (this.settings("auto_fill") !== undefined) {
          optionSnap = snapVal;
        } else {
          optionSnap = false;
        }
        let lastIndex = 0;

        let tween = gsap.to(sections, {
          xPercent: -100 * numSections,
          ease: "none",
          scrollTrigger: {
            trigger: widgetContainer + " .bdt-ep-hc-wrapper",
            pin: true,
            scrub: true,
            snap: optionSnap,
            end: () => "+=" + (scroller.offsetWidth - innerWidth),
            onUpdate: (self) => {
              const newIndex = Math.round(self.progress / snapVal);
              if (this.settings("show_dots") !== undefined) {
                if (newIndex !== lastIndex) {
                  navLis[lastIndex].classList.remove("is-active");
                  navLis[newIndex].classList.add("is-active");
                  lastIndex = newIndex;
                }
              }
            },
          },
        });
        navLis.forEach((anchor, i) => {
          anchor.addEventListener("click", function (e) {
            gsap.to(window, {
              scrollTo: {
                y:
                  tween.scrollTrigger.start +
                  i * innerWidth +
                  ((i + 1) * 1 + i * 1),
                autoKill: false,
              },
              duration: 1,
            });
          });
        });
      },

      run: function () {
        var editMode = Boolean(elementorFrontend.isEditMode());
        if (editMode) {
          return;
        }
        var $this = this;
        const widgetID = this.$element.data("id"),
          widgetContainer = ".elementor-element-" + widgetID;
        ScrollTrigger.matchMedia({
          "(min-width: 1024px)": function () {
            $(widgetContainer).addClass("bdt-ep-hc-active");
            $this.sectionJoiner();
            $this.horizontalScroller();
          },
          "(max-width:1023px)": function () {
            $(widgetContainer).removeClass("bdt-ep-hc-active");
          },
        });
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-horizontal-scroller.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(HorizontalScroller, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);
