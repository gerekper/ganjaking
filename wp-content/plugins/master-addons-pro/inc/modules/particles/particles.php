<?php

namespace MasterAddons\Modules;

use \Elementor\Controls_Manager;

class Extension_Particles
{

  private static $_instance = null;

  public function __construct()
  {

    add_action('elementor/element/after_section_end', [$this, 'register_controls'], 10, 3);

    // Add print template for editor preview
    add_action('elementor/section/print_template', [$this, '_print_template'], 10, 2);
    add_action('elementor/column/print_template', [$this, '_print_template'], 10, 2);

    add_action('elementor/frontend/column/before_render', [$this, '_before_render'], 10, 1);
    add_action('elementor/frontend/section/before_render', [$this, '_before_render'], 10, 1);

    add_action('elementor/frontend/section/after_render', array($this, 'after_render'));

    // add_action( 'elementor/editor/wp_head', [ $this, 'ma_el_add_particles_admin' ] );
  }

  public static function jltma_add_particles_scripts()
  {
    wp_enqueue_script('master-addons-particles', MELA_PLUGIN_URL . '/assets/js/particles.min.js', ['jquery'], MELA_VERSION, true);
  }


  public function register_controls($element, $section_id, $args)
  {

    if (('section' === $element->get_name() && 'section_background' === $section_id) || ('column' === $element->get_name() && 'section_style' === $section_id)) {

      $element->start_controls_section(
        'ma_el_particles',
        [
          'tab' => Controls_Manager::TAB_STYLE,
          'label' => MA_EL_BADGE . __(' Particles', MELA_TD)
        ]
      );

      $element->add_control(
        'ma_el_enable_particles',
        [
          'type'  => Controls_Manager::SWITCHER,
          'label' => __('Enable Particle Background', MELA_TD),
          'default' => '',
          'label_on' => __('Yes', MELA_TD),
          'label_off' => __('No', MELA_TD),
          'return_value' => 'yes',
          'prefix_class' => 'ma-el-particle-',
          'render_type' => 'template',
        ]
      );


      $element->add_control(
        'ma_el_particle_area_zindex',
        [
          'label'              => __('Z-index', MELA_TD),
          'type'               => Controls_Manager::NUMBER,
          'default'            => 0,
          'condition'          => [
            'ma_el_enable_particles' => 'yes',
          ],
          'frontend_available' => true,
        ]
      );


      $element->add_control(
        'ma_el_enable_particles_alert',
        [
          'type' => Controls_Manager::RAW_HTML,
          'content_classes' => 'ma_el_enable_particles_alert elementor-control-field-description',
          'raw' => __('<a href="https://vincentgarreau.com/particles.js/" target="_blank">Click here</a> to generate JSON for the below field. </br><a href="http://bit.ly/2Zoicet" target="_blank">Know more</a> about using this feature.', MELA_TD),
          'separator' => 'none',
          'condition' => [
            'ma_el_enable_particles' => 'yes',
          ],
        ]
      );

      $element->add_control(
        'ma_el_particle_json',
        [
          'type'    => Controls_Manager::CODE,
          'label'   => __('Add Particle Json', MELA_TD),
          'default' => '{
                                  "particles": {
                                    "number": {
                                      "value": 80,
                                      "density": {
                                        "enable": true,
                                        "value_area": 800
                                      }
                                    },
                                    "color": {
                                      "value": "#ffffff"
                                    },
                                    "shape": {
                                      "type": "circle",
                                      "stroke": {
                                        "width": 0,
                                        "color": "#000000"
                                      },
                                      "polygon": {
                                        "nb_sides": 5
                                      },
                                      "image": {
                                        "src": "img/github.svg",
                                        "width": 100,
                                        "height": 100
                                      }
                                    },
                                    "opacity": {
                                      "value": 0.5,
                                      "random": false,
                                      "anim": {
                                        "enable": false,
                                        "speed": 1,
                                        "opacity_min": 0.1,
                                        "sync": false
                                      }
                                    },
                                    "size": {
                                      "value": 3,
                                      "random": true,
                                      "anim": {
                                        "enable": false,
                                        "speed": 40,
                                        "size_min": 0.1,
                                        "sync": false
                                      }
                                    },
                                    "line_linked": {
                                      "enable": true,
                                      "distance": 150,
                                      "color": "#ffffff",
                                      "opacity": 0.4,
                                      "width": 1
                                    },
                                    "move": {
                                      "enable": true,
                                      "speed": 6,
                                      "direction": "none",
                                      "random": false,
                                      "straight": false,
                                      "out_mode": "out",
                                      "bounce": false,
                                      "attract": {
                                        "enable": false,
                                        "rotateX": 600,
                                        "rotateY": 1200
                                      }
                                    }
                                  },
                                  "interactivity": {
                                    "detect_on": "canvas",
                                    "events": {
                                      "onhover": {
                                        "enable": true,
                                        "mode": "repulse"
                                      },
                                      "onclick": {
                                        "enable": true,
                                        "mode": "push"
                                      },
                                      "resize": true
                                    },
                                    "modes": {
                                      "grab": {
                                        "distance": 400,
                                        "line_linked": {
                                          "opacity": 1
                                        }
                                      },
                                      "bubble": {
                                        "distance": 400,
                                        "size": 40,
                                        "duration": 2,
                                        "opacity": 8,
                                        "speed": 3
                                      },
                                      "repulse": {
                                        "distance": 200,
                                        "duration": 0.4
                                      },
                                      "push": {
                                        "particles_nb": 4
                                      },
                                      "remove": {
                                        "particles_nb": 2
                                      }
                                    }
                                  },
                                  "retina_detect": true
                                }',
          'render_type' => 'template',
          'condition' => [
            'ma_el_enable_particles' => 'yes'
          ]
        ]
      );

      $element->end_controls_section();
    }
  }

  public function _before_render($element)
  {

    if ($element->get_name() != 'section' && $element->get_name() != 'column') {
      return;
    }

    $settings = $element->get_settings();
    if ($settings['ma_el_enable_particles'] == 'yes') {
      $element->add_render_attribute('_wrapper', 'data-ma-el-particle', $settings['ma_el_particle_json']);
      self::jltma_add_particles_scripts();
    }
  }

  function _print_template($template, $widget)
  {
    if ($widget->get_name() != 'section' && $widget->get_name() != 'column') {
      return $template;
    }

    $old_template = $template;
    ob_start();
?>

    <div class="ma-el-particle-wrapper" id="ma-el-particle-{{ view.getID() }}" data-ma-el-pdata=" {{ settings
            .ma_el_particle_json }}"></div>

    <?php
    $slider_content = ob_get_contents();
    ob_end_clean();
    $template = $slider_content . $old_template;
    return $template;
  }

  public function after_render($element)
  {

    $data     = $element->get_data();
    $settings = $element->get_settings_for_display();
    $type     = $data['elType'];
    $zindex   = !empty($settings['ma_el_particle_area_zindex']) ? $settings['ma_el_particle_area_zindex'] : 0;

    if (('section' === $type) && ($element->get_settings('ma_el_enable_particles') === 'yes')) {
    ?>
      <style>
        .elementor-element-<?php echo $element->get_id(); ?>.ma-el-particle-wrapper>canvas {
          z-index: <?php echo $zindex; ?>;
          position: absolute;
          top: 0;
        }
      </style>
<?php
    }
  }


  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }
}

Extension_Particles::instance();
