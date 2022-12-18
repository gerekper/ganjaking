(function( $ ) {
    'use strict';

    var init_particles = function( $obj ) {
        if ( typeof $obj == 'undefined' ) {
            $obj = $( document.body );
        }
        $obj.find( '.particles-wrapper' ).each( function() {
            var obj_id = $( this ).attr( 'id' ),
                opts = $( this ).data( 'plugin-options' );
            if ( ! obj_id || ! opts || ! opts.src ) {
                return;
            }

            particlesJS( obj_id, {
              "particles": {
                "number": {
                  "value": 30,
                  "density": {
                    "enable": true,
                    "value_area": 800
                  }
                },
                "color": {
                  "value": "#ffffff"
                },
                "shape": {
                  "type": "image",
                  "stroke": {
                    "width": 0,
                    "color": "#000000"
                  },
                  "polygon": {
                    "nb_sides": 5
                  },
                  "image": {
                    "src": opts.src,
                    "width": opts.w || 100,
                    "height": opts.h || 100
                  }
                },
                "opacity": {
                  "value": 1,
                  "random": true,
                  "anim": {
                    "enable": true,
                    "speed": 1,
                    "opacity_min": 0,
                    "sync": false
                  }
                },
                "size": {
                  "value": 48.10236182596568,
                  "random": true,
                  "anim": {
                    "enable": false,
                    "speed": 4,
                    "size_min": 0.3,
                    "sync": false
                  }
                },
                "line_linked": {
                  "enable": false,
                  "distance": 150,
                  "color": "#ffffff",
                  "opacity": 0.4,
                  "width": 1
                },
                "move": {
                  "enable": true,
                  "speed": 1,
                  "direction": "none",
                  "random": true,
                  "straight": false,
                  "out_mode": "out",
                  "bounce": false,
                  "attract": {
                    "enable": false,
                    "rotateX": 600,
                    "rotateY": 600
                  }
                }
              },
              "interactivity": {
                "detect_on": "canvas",
                "events": {
                  "onhover": {
                    "enable": opts.he ? true : false,
                    "mode": opts.he || 'bubble'
                  },
                  "onclick": {
                    "enable": opts.ce ? true : false,
                    "mode": opts.ce || "repulse"
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
                    "distance": 250,
                    "size": 0,
                    "duration": 2,
                    "opacity": 0,
                    "speed": 3
                  },
                  "repulse": {
                    "distance": 400,
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
            });
        } );
    };

    $( window ).on( 'load', function() {
        init_particles();
    } );

    $( document.body ).on( 'porto_init_particles_effect', function( e, $obj ) {
        init_particles( $obj );
    } );

}).apply( this, [ jQuery ]);