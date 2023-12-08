(function ($, elementor) {
  $(window).on("elementor/frontend/init", function () {
    let ModuleHandler = elementorModules.frontend.handlers.Base,
      ReadingTimer;

    ReadingTimer = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },
      getDefaultSettings: function () {
        return {
          allowHTML: true,
        };
      },

      settings: function (key) {
        return this.getElementSettings("reading_timer_" + key);
      },

      calculateReadingTime: function (ReadingContent) {
        let wordCount = ReadingContent.split(/\s+/).filter(function (word) {
            return word !== "";
          }).length,
          averageReadingSpeed = this.settings("avg_words_per_minute")
            ? this.settings("avg_words_per_minute").size
            : 200,
          readingTime = Math.floor(wordCount / averageReadingSpeed),
          reading_seconds = Math.floor(
            (wordCount % averageReadingSpeed) / (averageReadingSpeed / 60)
          ),
          minText = this.settings("minute_text")
            ? this.settings("minute_text")
            : "min read",
          secText = this.settings("seconds_text")
            ? this.settings("seconds_text")
            : "sec read";

        if (wordCount >= averageReadingSpeed) {
          return `${readingTime} ${minText}`;
        } else {
          return `${reading_seconds} ${secText}`;
        }
      },

      run: function () {
        const widgetID = this.$element.data("id"),
          widgetContainer = `.elementor-element-${widgetID} .bdt-reading-timer`,
          contentSelector = this.settings("content_id");
        let minText = this.settings("minute_text")
          ? this.settings("minute_text")
          : "min read";

        var editMode = Boolean(elementorFrontend.isEditMode());
        if (editMode) {
          $(widgetContainer).append("2 " + minText + "");
          return;
        }
        if (contentSelector) {
          ReadingContent = $(document).find(`#${contentSelector}`).text();
          var readTime = this.calculateReadingTime(ReadingContent);
          $(widgetContainer).append(readTime);
        } else return;
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-reading-timer.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(ReadingTimer, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);
