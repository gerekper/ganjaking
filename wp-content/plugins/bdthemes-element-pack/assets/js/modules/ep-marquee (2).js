(function ($, elementor) {
  $(window).on("elementor/frontend/init", function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
      ListMarquee;

    ListMarquee = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },
      getDefaultSettings: function () {
        return {
          allowHTML: true,
        };
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("marquee_") !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings("marquee_" + key);
      },

      run: function () {
        let isElementorEditor = elementorFrontend.isEditMode();
        if (isElementorEditor) {
          return;
        }
        const widgetID = this.$element.data("id");
        var self = this;
        var options = this.getDefaultSettings();
        var element = this.findElement(".elementor-widget-container").get(0);
        if (jQuery(this.$element).hasClass("elementor-section")) {
          element = this.$element.get(0);
        }

        var $container = this.$element.find(".bdt-marquee");
        if (!$container) {
          return;
        }

        let advancedOptions =
          self.settings("advanced") === "yes" ? true : false;

        if (advancedOptions) {
          $("body").css("overflow-x", "hidden");
        }

        var widgetContainer = ".elementor-element-" + widgetID;

        const boxes = gsap.utils.toArray(widgetContainer + " .marquee-content");
        let reversedDirection =
            self.settings("direction") === "right" ? true : false,
          marqueeSpeed = self.settings("speed")
            ? self.settings("speed") / 100
            : 1,
          isClickable = self.settings("clickable") === "yes" ? true : false,
          isPauseOnHover =
            self.settings("pause_on_hover") === "yes" ? true : false,
          isDraggable = self.settings("draggable") === "yes" ? true : false,
          spacing = self.settings("spacing") ? self.settings("spacing") : 0,
          isLoop = self.settings("loop") === "yes" ? true : false;

        let activeElement;
        const loop = self.horizontalLoop(boxes, {
          paused: false,
          draggable: isDraggable,
          repeat: -1,
          speed: marqueeSpeed,
          paddingRight: spacing,
          reversed: reversedDirection,
          center: true,
          reversedDirection: reversedDirection,
          onChange: (element, index) => {
            // when the active element changes, this function gets called.
            activeElement && activeElement.classList.remove("active");
            element.classList.add("active");
            activeElement = element;
          },
        });

        if (isPauseOnHover) {
          self.pauseOnHover(loop, boxes);
        }

        if (isClickable) {
          $(boxes).on("mouseleave", () => {
            if (loop.reversed()) {
              loop.play();
              loop.reverse();
            } else {
              loop.play();
              loop.reversed();
            }
          });
          // loop.pause();
          boxes.forEach((box, i) => {
            box.addEventListener("click", () => {
              loop.toIndex(i, {
                duration: 0.8,
                ease: "power1.inOut",
              });
            });
          });
        }
      },

      pauseOnHover: function (tween, boxes) {
        $(boxes).on("mouseenter", () => {
          tween.pause();
        });

        $(boxes).on("mouseleave", () => {
          if (tween.reversed()) {
            tween.play();
            tween.reverse();
          } else {
            tween.play();
            tween.reversed();
          }
        });
      },
      horizontalLoop: function (items, config, reverseDirection = false) {
        var self = this;
        items = gsap.utils.toArray(items);
        config = config || {};
        let onChange = config.onChange,
          lastIndex = 0,
          tl = gsap.timeline({
            repeat: config.repeat,
            paused: config.paused,
            defaults: { ease: "none" },
            onReverseComplete: () =>
              tl.totalTime(tl.rawTime() + tl.duration() * 100),
          }),
          length = items.length,
          startX = items[0].offsetLeft,
          times = [],
          widths = [],
          spaceBefore = [],
          xPercents = [],
          curIndex = 0,
          center = config.center,
          pixelsPerSecond = (config.speed || 1) * 100,
          snap =
            config.snap === false
              ? (v) => v
              : gsap.utils.snap(config.snap || 1), // some browsers shift by a pixel to accommodate flex layouts, so for example if width is 20% the first element's width might be 242px, and the next 243px, alternating back and forth. So we snap to 5 percentage points to make things look more natural
          timeOffset = 0,
          container =
            center === true
              ? items[0].parentNode
              : gsap.utils.toArray(center)[0] || items[0].parentNode,
          totalWidth,
          getTotalWidth = () =>
            items[length - 1].offsetLeft +
            (xPercents[length - 1] / 100) * widths[length - 1] -
            startX +
            spaceBefore[0] +
            items[length - 1].offsetWidth *
              gsap.getProperty(items[length - 1], "scaleX") +
            (parseFloat(config.paddingRight) || 0),
          populateWidths = () => {
            let b1 = container.getBoundingClientRect(),
              b2;
            items.forEach((el, i) => {
              widths[i] = parseFloat(gsap.getProperty(el, "width", "px"));
              xPercents[i] = snap(
                (parseFloat(gsap.getProperty(el, "x", "px")) / widths[i]) *
                  100 +
                  gsap.getProperty(el, "xPercent")
              );
              b2 = el.getBoundingClientRect();
              spaceBefore[i] = b2.left - (i ? b1.right : b1.left);
              b1 = b2;
            });
            gsap.set(items, {
              // convert "x" to "xPercent" to make things responsive, and populate the widths/xPercents Arrays to make lookups faster.
              xPercent: (i) => xPercents[i],
            });
            totalWidth = getTotalWidth();
          },
          timeWrap,
          populateOffsets = () => {
            timeOffset = center
              ? (tl.duration() * (container.offsetWidth / 2)) / totalWidth
              : 0;
            center &&
              times.forEach((t, i) => {
                times[i] = timeWrap(
                  tl.labels["label" + i] +
                    (tl.duration() * widths[i]) / 2 / totalWidth -
                    timeOffset
                );
              });
          },
          getClosest = (values, value, wrap) => {
            let i = values.length,
              closest = 1e10,
              index = 0,
              d;
            while (i--) {
              d = Math.abs(values[i] - value);
              if (d > wrap / 2) {
                d = wrap - d;
              }
              if (d < closest) {
                closest = d;
                index = i;
              }
            }
            return index;
          },
          populateTimeline = () => {
            let i, item, curX, distanceToStart, distanceToLoop;
            tl.clear();
            for (i = 0; i < length; i++) {
              item = items[i];
              curX = (xPercents[i] / 100) * widths[i];
              distanceToStart =
                item.offsetLeft + curX - startX + spaceBefore[0];
              distanceToLoop =
                distanceToStart + widths[i] * gsap.getProperty(item, "scaleX");
              tl.to(
                item,
                {
                  xPercent: snap(((curX - distanceToLoop) / widths[i]) * 100),
                  duration: distanceToLoop / pixelsPerSecond,
                },
                0
              )
                .fromTo(
                  item,
                  {
                    xPercent: snap(
                      ((curX - distanceToLoop + totalWidth) / widths[i]) * 100
                    ),
                  },
                  {
                    xPercent: xPercents[i],
                    duration:
                      (curX - distanceToLoop + totalWidth - curX) /
                      pixelsPerSecond,
                    immediateRender: false,
                  },
                  distanceToLoop / pixelsPerSecond
                )
                .add("label" + i, distanceToStart / pixelsPerSecond);
              times[i] = distanceToStart / pixelsPerSecond;
            }
            timeWrap = gsap.utils.wrap(0, tl.duration());
          },
          refresh = (deep) => {
            let progress = tl.progress();
            tl.progress(0, true);
            populateWidths();
            deep && populateTimeline();
            populateOffsets();
            deep && tl.draggable
              ? tl.time(times[curIndex], true)
              : tl.progress(progress, true);
          },
          proxy;
        gsap.set(items, { x: 0 });
        populateWidths();
        populateTimeline();
        populateOffsets();
        window.addEventListener("resize", () => refresh(true));
        function toIndex(index, vars) {
          vars = vars || {};

          if (reverseDirection) {
            index = length - index;
          }
          Math.abs(index - curIndex) > length / 2 &&
            (index += index > curIndex ? -length : length); // always go in the shortest direction
          let newIndex = gsap.utils.wrap(0, length, index),
            time = times[newIndex];
          if (time > tl.time() !== index > curIndex) {
            // if we're wrapping the timeline's playhead, make the proper adjustments
            time += tl.duration() * (index > curIndex ? 1 : -1);
          }
          if (time < 0 || time > tl.duration()) {
            vars.modifiers = { time: timeWrap };
          }
          curIndex = newIndex;
          vars.overwrite = true;
          gsap.killTweensOf(proxy);
          return tl.tweenTo(time, vars);
        }
        tl.next = (vars) => toIndex(curIndex + 1, vars);
        tl.previous = (vars) => toIndex(curIndex - 1, vars);
        tl.current = () => curIndex;
        tl.toIndex = (index, vars) => toIndex(index, vars);
        tl.closestIndex = (setCurrent) => {
          let index = getClosest(times, tl.time(), tl.duration());
          setCurrent && (curIndex = index);
          return index;
        };
        tl.times = times;
        tl.progress(1, true).progress(0, true); // pre-render for performance
        if (config.reversed) {
          tl.vars.onReverseComplete();
          tl.reverse();
        }

        if (config.draggable && typeof Draggable === "function") {
          proxy = document.createElement("div");
          let wrap = gsap.utils.wrap(0, 1),
            ratio,
            startProgress,
            draggable,
            dragSnap,
            align = () =>
              tl.progress(
                wrap(startProgress + (draggable.startX - draggable.x) * ratio)
              ),
            syncIndex = () => tl.closestIndex(true);
          typeof InertiaPlugin === "undefined" &&
            console.warn(
              "InertiaPlugin required for momentum-based scrolling and snapping. https://greensock.com/club"
            );
          draggable = Draggable.create(proxy, {
            trigger: items[0].parentNode,
            type: "x",
            onPressInit() {
              gsap.killTweensOf(tl);
              startProgress = tl.progress();
              refresh();
              ratio = 1 / totalWidth;
              gsap.set(proxy, { x: startProgress / -ratio });
            },
            onDrag: align,
            onThrowUpdate: align,
            inertia: true,
            snap: (value) => {
              let time = -(value * ratio) * tl.duration(),
                wrappedTime = timeWrap(time),
                snapTime = times[getClosest(times, wrappedTime, tl.duration())],
                dif = snapTime - wrappedTime;
              Math.abs(dif) > tl.duration() / 2 &&
                (dif += dif < 0 ? tl.duration() : -tl.duration());
              return (time + dif) / tl.duration() / -ratio;
            },
            onRelease: syncIndex,
            onThrowComplete: syncIndex,
          })[0];
          tl.draggable = draggable;
        }
        tl.closestIndex(true);
        onChange && onChange(items[curIndex], curIndex);
        return tl;
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-marquee.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(ListMarquee, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);
