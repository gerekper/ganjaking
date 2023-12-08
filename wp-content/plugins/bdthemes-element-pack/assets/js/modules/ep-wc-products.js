(function ($, elementor) {
  "use strict";
  $(window).on("elementor/frontend/init", function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
      WCProducts;

    WCProducts = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {};
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("wc") !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings("wc_products_" + key);
      },

      run: function () {
        const options = this.getDefaultSettings(),
         content = this.settings("enable_ajax_loadmore"),
         container = this.$element.find(".bdt-wc-products");

        if (!container.length || content === undefined) {
          return;
        }

        const settingsLoadmore = this.settings("show_loadmore"),
         settingsInfiniteScroll = this.settings("show_infinite_scroll"),
         loadButtonContainer = this.$element.find(".bdt-loadmore-container"),
         products = container.find(".bdt-wc-products-wrapper"),
         loadButton = loadButtonContainer.find(".bdt-loadmore");
        let loading = false;
        const settings = container.data("settings");
        let currentItemCount = Number(settings.posts_per_page);

        const loadMorePosts = () => {
          const dataSettings = {
            action: "bdt_ep_wc_products_load_more",
            settings: settings,
            per_page: settings.ajax_item_load,
            offset: currentItemCount,
            nonce: settings.nonce,
            paged: settings.paged,
          };

          $.ajax({
            url: window.ElementPackConfig.ajaxurl,
            type: "post",
            data: dataSettings,
            success: (response) => {
              $(products).append(response.markup);
              currentItemCount += settings.ajax_item_load;
              settings.paged += 1;
              loading = false;

              if (settingsLoadmore === "yes") {
                loadButton.html("Load More");
              }

              if ($(response.markup).length < settings.ajax_item_load) {
                loadButton.hide();
                loadButtonContainer.hide();
              }
            },
          });
        };

        const handleButtonClick = () => {
          if (!loading) {
            loading = true;
            loadButton.html("Loading...");
            loadMorePosts();
          }
        };

        if (settingsLoadmore === "yes") {
          $(loadButton).on("click", handleButtonClick);
        }

        if (settingsInfiniteScroll === "yes") {
          $(window).scroll(() => {
            if (
              $(window).scrollTop() ===
                $(document).height() - $(window).height() &&
              !loading
            ) {
              $(loadButton).css("display", "block");
              loading = true;
              loadMorePosts();
            }
          });
        }
      },
    });
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-wc-products.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(WCProducts, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);
