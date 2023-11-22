(function ($, elementor) {
  $(window).on("elementor/frontend/init", function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
      Stacker;

    Stacker = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },
      getDefaultSettings: function () {
        return {
          allowHTML: true,
        };
      },

      settings: function (key) {
        return this.getElementSettings("stacker_" + key);
      },

      sectionJoiner: function () {
        const widgetID = this.$element.data("id"),
          sectionId = [],
          sectionList = this.settings("section_list"),
          widgetContainer = ".elementor-element-" + widgetID,
          widgetWrapper = widgetContainer + " .bdt-ep-stacker";

        sectionList.forEach((section) => {
          sectionId.push("#" + section.stacker_section_id);
        });

        //check is id exists
        let haveIds = [];
        let elements;

        const topSection = document.querySelector(".elementor-top-section");
        const eConElements = document.getElementsByClassName("e-con");

        if (topSection) {
          // Code to handle 'elementor-top-section' existence
          elements = document.querySelectorAll(".elementor-top-section");
        }

        if (eConElements.length > 0) {
          // Code to handle 'e-con' existence
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
        if (ids) {
          var selectedIDs = document.querySelectorAll(ids);
          $(widgetWrapper).append(selectedIDs);
        }
        //check is id exists
      },

      StackerOpacity: function () {},
      StackerScript: function () {
        gsap.registerPlugin(ScrollTrigger);
        let cards;
        const widgetID = this.$element.data("id"),
          widgetContainer = ".elementor-element-" + widgetID;
        let stickDistance = 0;
        let opacityEnabled = this.settings("stacking_opacity") == 'yes' ? true : false;
        let stackingSpace = this.settings("stacking_space")
          ? this.settings("stacking_space").size
          : 40;
        let scrollerStart = this.settings("scroller_start")
          ? this.settings("scroller_start").size + "%"
          : "10%";
        cards = gsap.utils.toArray(
          widgetContainer + " .bdt-ep-stacker > .elementor-element"
        );

        cards.forEach((card, i) => {
          let lastCardST = ScrollTrigger.create({
            trigger: cards[cards.length - 1],
            start: `top-=${0 * i} ${scrollerStart}`,
          });

          if(opacityEnabled) {
                gsap.set(card, { opacity: 0 });
                gsap.from(card, {
                  opacity: 1,
                  scrollTrigger: {
                    trigger: card,
                    scrub: true,
                    start: `top-=${stackingSpace * i} ${scrollerStart}`,
                    end: () => lastCardST.start + stickDistance,
                  },
                  ease: "none",
                });
        }

          ScrollTrigger.create({
            trigger: card,
            start: `top-=${stackingSpace * i} ${scrollerStart}`,
            end: () => lastCardST.start + stickDistance,
            endTrigger: cards[cards.length - 1],
            pin: true,
            pinSpacing: false,
            ease: "none",
            toggleActions: "restart none none reverse",
          });
        });
      },

      run: function () {
        const widgetID = this.$element.data("id"),
          widgetContainer = ".elementor-element-" + widgetID,
          widgetWrapper = widgetContainer + " .bdt-ep-stacker";

        var editMode = Boolean(elementorFrontend.isEditMode());
        if (editMode) {
          $(widgetWrapper).append(
            '<div class="bdt-alert-warning" bdt-alert><a class="bdt-alert-close" bdt-close></a><p>Stacker Widget Placed Here (Only Visible for Editor).</p></div>'
          );
          return;
        }
        this.sectionJoiner();
        this.StackerScript();
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-stacker.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(Stacker, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);
