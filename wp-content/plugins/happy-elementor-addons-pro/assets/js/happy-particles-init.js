"use strict";

(function ($, w) {
  "use strict";

  var $window = $(w);
  $window.on("elementor/frontend/init", function () {
    var Particles = function Particles($scope) {
      if (!$scope.hasClass("ha-particle-yes")) {
        return;
      }

      var id = $scope.data("id"),
          element_type = $scope.data("element_type"),
          particles_style = $scope.find(".ha-particle-wrapper").data("ha-partstyle");

      if (typeof particles_style != "undefined") {
        if (element_type == "column") {
          var $column = $scope.find(".elementor-column-wrap .elementor-background-overlay");
          var $particle_wrap = $scope.find(".ha-particle-wrapper");

          if ($column.next("#ha-particle-" + id).length == 0) {
            $column.after($particle_wrap);
          }
        } else {
          var $bg_wrap = $scope.find(".elementor-element-overlay ~ .elementor-background-overlay");
          var $particle_wrap = $scope.find("#ha-particle-" + id);

          if ($bg_wrap.next("#ha-particle-" + id).length == 0) {
            $bg_wrap.after($particle_wrap);
          }
        }

        var $selector = $scope.find("#ha-particle-" + id);
      } else {
        particles_style = $scope.data("ha-partstyle");
        var $selector = $scope;
        var $content = $('<div class="ha-particle-wrapper" id="ha-particle-' + id + '"></div>');

        if (element_type == "column") {
          if ($scope.find(".elementor-background-overlay").length == 0) {
            // var $column = $scope.find( '.elementor-column-wrap' );
            $scope.prepend($content);
          } else {
            var $column = $scope.find(".elementor-background-overlay");
            $column.after($content);
          }
        } else {
          if ($scope.find(".elementor-background-overlay ~ .elementor-container").length == 0) {
            $scope.prepend($content);
          } else {
            $scope.find(".elementor-background-overlay").after($content);
          }
        }
      }

      var particles_dot_color = $selector.data("ha-partcolor"),
          particles_custom = $selector.data("ha-partdata"),
          particles_opacity = $selector.data("ha-partopacity"),
          particles_direction = $selector.data("ha-partdirection");

      if (particles_custom !== "" && particles_style == "custom") {
        particlesJS("ha-particle-" + id, particles_custom);
      } else if (particles_style !== "custom") {
        var number_value = 150,
            shape_type = "circle",
            shape_nb_sides = 5,
            opacity_value = 0.6,
            opacity_random = true,
            opacity_anim_enable = false,
            line_linked = false,
            move_speed = 4,
            move_random = true,
            size_value = 2,
            size_random = true,
            size_anim_enable = false,
            onhover = "repulse",
            move_direction = "none",
            interactive = false;

        if ("default" == particles_style) {
          line_linked = true;
          opacity_random = false;
          move_random = false;
          move_speed = 6;
        } else if ("nasa" == particles_style) {
          number_value = 160;
          shape_type = "circle";
          opacity_value = 1;
          opacity_anim_enable = true;
          move_speed = 1;
          size_value = 3;
          onhover = "bubble";
        } else if ("snow" == particles_style) {
          opacity_value = 0.5;
          size_value = 4;
          move_speed = 3;
          move_direction = particles_direction;
          number_value = 200;
          opacity_random = false;
        } else if ("flow" == particles_style) {
          number_value = 14;
          shape_type = "polygon";
          shape_nb_sides = 6;
          opacity_value = 0.3;
          move_speed = 5;
          size_value = 40;
          size_random = false;
          size_anim_enable = true;
        } else if ("bubble" == particles_style) {
          move_speed = 5;
          move_direction = "top";
          number_value = 500;
          size_value = 1;
          size_random = false;
          opacity_value = 0.6;
          opacity_random = false;
        }

        if (particles_dot_color == "") {
          particles_dot_color = "#bdbdbd";
        }

        if (particles_opacity != "" || particles_opacity == "0") {
          opacity_value = particles_opacity;
        }

        if ($scope.hasClass("ha-particle-adv-yes")) {
          var particles_number = $selector.data("ha-partnum");
          var particles_size = $selector.data("ha-partsize");
          var particles_speed = $selector.data("ha-partspeed");
          var particles_interactive = $selector.data("ha-interactive");

          if (particles_number != "") {
            number_value = particles_number;
          }

          if (particles_size !== "") {
            size_value = particles_size;
          }

          if (particles_speed !== "") {
            move_speed = particles_speed;
          }

          if (particles_interactive == "yes") {
            interactive = true;
          }
        }

        var config = {
          particles: {
            number: {
              value: number_value,
              density: {
                enable: true,
                value_area: 800
              }
            },
            color: {
              value: particles_dot_color
            },
            shape: {
              type: shape_type,
              stroke: {
                width: 0,
                color: "#ffffff"
              },
              polygon: {
                nb_sides: shape_nb_sides
              }
            },
            opacity: {
              value: opacity_value,
              random: opacity_random,
              anim: {
                enable: opacity_anim_enable,
                speed: 1,
                opacity_min: 0.1,
                sync: false
              }
            },
            size: {
              value: size_value,
              random: size_random,
              anim: {
                enable: size_anim_enable,
                speed: 5,
                size_min: 35,
                sync: false
              }
            },
            line_linked: {
              enable: line_linked,
              distance: 150,
              color: particles_dot_color,
              opacity: 0.4,
              width: 1
            },
            move: {
              enable: true,
              speed: move_speed,
              direction: move_direction,
              random: move_random,
              straight: false,
              out_mode: "out",
              attract: {
                enable: false,
                rotateX: 600,
                rotateY: 1200
              }
            }
          },
          interactivity: {
            detect_on: "canvas",
            events: {
              onhover: {
                enable: interactive,
                mode: onhover
              },
              onclick: {
                enable: false,
                mode: "push"
              },
              resize: true
            },
            modes: {
              grab: {
                distance: 400,
                line_linked: {
                  opacity: 1
                }
              },
              bubble: {
                distance: 200,
                size: 0,
                duration: 2,
                opacity: 0,
                speed: 2
              },
              repulse: {
                distance: 150
              },
              push: {
                particles_nb: 4
              },
              remove: {
                particles_nb: 2
              }
            }
          },
          retina_detect: true
        };
        particlesJS("ha-particle-" + id, config);
      }
    };

    elementorFrontend.hooks.addAction("frontend/element_ready/section", Particles);
    elementorFrontend.hooks.addAction("frontend/element_ready/column", Particles);
    elementorFrontend.hooks.addAction("frontend/element_ready/container", Particles);
  });
})(jQuery, window);