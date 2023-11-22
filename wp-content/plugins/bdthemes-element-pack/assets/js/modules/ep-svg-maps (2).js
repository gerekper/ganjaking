(function ($, elementor) {
  "use strict";

  $(window).on("elementor/frontend/init", function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
      SvgMaps;
    SvgMaps = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {};
      },

      onElementChange: debounce(function (prop) {
        if (prop.indexOf("svg_maps") !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings("svg_maps_" + key);
      },
      createColorAxisArray: function () {
        const axisColorList = this.settings("region_axis_color");

        const colors = [];
        // set default color
        // if (this.settings("default_color") != "") {
        //   colors.push(this.settings("default_color"));
        // }

        // set color axis
        axisColorList.forEach((color) => {
          if (color.axis_color !== "") {
            colors.push(color.axis_color);
          }
        });

        return colors;
      },

      createCustomRegion: function (data, options, isLinkable) {
        const regionList = this.settings("array_regions");
        const currentRegionColors = [];
        data.addColumn("string", "Country");
        data.addColumn("number", "Population");
        data.addColumn({ type: "string", role: "tooltip", p: { html: true } });
        regionList.forEach((region, index) => {
          currentRegionColors.push(
            region.active_region_color ? region.active_region_color : "#146C94"
          );
          options.colors = currentRegionColors;
          data.addRows([
            [
              {
                v: region.active_region_code,
                f:
                  region.active_region_name !== ""
                    ? region.active_region_name
                    : region.active_region_code,
              },
              index,
              region.active_tooltip_content,
            ],
          ]);

          isLinkable[region.active_region_code] = {
            url: region.region_link ? region.region_link.url : "",
            target:
              region.region_link && !region.region_link.is_external
                ? "_self"
                : "",
          };
        });
      },
      createDataVisRegions: function (isLinkable) {
        const dataVisualArray = [];
        const dataVisualTitle = this.settings("region_value_title");
        const dataRegionList = this.settings("data_visual_array_regions");
        dataVisualArray[0] = ["Country", dataVisualTitle];

        dataRegionList.forEach((region) => {
          dataVisualArray.push([
            region.visual_data_region_name,
            region.visual_data_value,
          ]);

          isLinkable[region.visual_data_region_name] = {
            url: region.visual_data_region_link
              ? region.visual_data_region_link.url
              : "",
            target:
              region.visual_data_region_link &&
              !region.visual_data_region_link.is_external
                ? "_self"
                : "",
          };
        });

        var data = google.visualization.arrayToDataTable(dataVisualArray);
        return {
          data,
        };
      },
      run: function () {
        const self = this;
        var options = this.getDefaultSettings();
        var element = this.findElement(".elementor-widget-container").get(0),
          widgetID = this.$element.data("id");
        if (jQuery(this.$element).hasClass("elementor-section")) {
          element = this.$element.get(0);
        }
        var $container = this.$element.find(".bdt-svg-maps");
        if (!$container.length) {
          return;
        }
        const $mapWrapper = document.getElementById(`bdt-svg-maps-${widgetID}`);

        google.charts.load("current", {
          packages: ["geochart"],
        });
        google.charts.setOnLoadCallback(drawTable);

        function drawTable() {
          var data = new google.visualization.DataTable();
          let isLinkable = [];
          let markerIsLinkable = [];

          // set region
          switch (self.settings("region_type")) {
            case "continent":
              options.region = self.settings("display_region_continent")
                ? self.settings("display_region_continent")
                : "002";
              break;

            case "subcontinent":
              options.region = self.settings("display_region_sub_continent")
                ? self.settings("display_region_sub_continent")
                : "015";
              break;

            case "countries":
              options.region = self.settings("display_region_countries")
                ? self.settings("display_region_countries")
                : "AU";
              break;

            default:
              options.region = "world";
              break;
          }
          // options.region = self.settings("display_region") ? self.settings("display_region") : "world";


          options.width = self.settings("width")
            ? self.settings("width").size
            : 600;
          options.height = self.settings("height")
            ? self.settings("height").size
            : 400;
          options.backgroundColor = self.settings("background_color")
            ? self.settings("background_color")
            : "#81d4fa";
          options.datalessRegionColor = self.settings("dataless_region_color")
            ? self.settings("dataless_region_color")
            : "#f8bbd0";

          options.tooltip = {
            isHtml: true,
            trigger: self.settings("tooltip_trigger")
              ? self.settings("tooltip_trigger")
              : "focus",
            textStyle: {
              // fontSize: self.settings("tooltip_font_size")   ? self.settings("tooltip_font_size") : 14,
              bold:
                self.settings("tooltip_font_weight") === "yes" ? true : false,
              italic:
                self.settings("tooltip_font_style") === "yes" ? true : false,
            },
          };
          // show legend
          if (self.settings("show_legend") !== "yes") {
            options.legend = "none";
          } else {
            options.legend = {
              textStyle: {
                color: self.settings("legend_font_color")
                  ? self.settings("legend_font_color")
                  : "#000000",
                fontSize: self.settings("legend_font_size")
                  ? self.settings("legend_font_size")
                  : 16,
                bold:
                  self.settings("legend_font_weight") === "yes" ? true : false,
                italic:
                  self.settings("legend_font_style") === "yes" ? true : false,
              },
            };
          }

          // run initilize code here to get data
          if (self.settings("display_mode") === "regions") {
            if (self.settings("display_type") === "custom") {
              self.createCustomRegion(data, options, isLinkable);
            } else {
              const dataVisRegions = self.createDataVisRegions(isLinkable);
              data = dataVisRegions.data;
              // set color axis
              options.colorAxis = {
                colors: self.createColorAxisArray(),
              };
            }
          }

          // Instantiate and draw our chart, passing in some options.
          var chart = new google.visualization.GeoChart($mapWrapper);
          google.visualization.events.addListener(chart, "select", () => {
            const selection = chart.getSelection();
            if (selection.length === 1) {
              const selectedRow = selection[0].row;
              const selectedRegion = data.getValue(selectedRow, 0);
              switch (self.settings("display_mode")) {
                case "regions":
                  isLinkable[selectedRegion].url !== ""
                    ? window.open(
                        isLinkable[selectedRegion].url,
                        isLinkable[selectedRegion].target
                      )
                    : "";
                  break;
                case "markers":
                  markerIsLinkable[selectedRegion].url !== ""
                    ? window.open(
                        markerIsLinkable[selectedRegion].url,
                        markerIsLinkable[selectedRegion].target
                      )
                    : "";
                  break;
              }
            }
          });
          chart.draw(data, options);
        }
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-svg-maps.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(SvgMaps, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);
