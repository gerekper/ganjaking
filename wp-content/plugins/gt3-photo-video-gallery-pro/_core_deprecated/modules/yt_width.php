<?php
defined('ABSPATH') OR exit;

	if ( ! class_exists( 'gt3pg_extension_pro_yt_width' ) ) {
		class gt3pg_extension_pro_yt_width {
			private $jsurl;
			private $imgurl;
			private $cssurl;
			private $rootpath;
			private $rooturl;

			public function __construct() {
				$this->jsurl    = plugins_url( 'assets/js/', __FILE__ );
				$this->imgurl   = plugins_url( 'assets/img/', __FILE__ );
				$this->cssurl   = plugins_url( 'assets/css/', __FILE__ );
				$this->rootpath = plugins_url( '', __FILE__ );
				$this->rooturl  = plugin_dir_path( __FILE__ );

				$this->actions();
			}

			private function actions() {
				add_filter( 'gt3pg_allowed_shortcode_atts', array( $this, 'gt3pg_allowed_shortcode_atts' ), 10, 1 );
				add_filter( 'gt3_admin_mix_tabs_controls', array( $this, 'gt3_admin_mix_tabs_controls' ), 10, 1 );
				add_filter( 'gt3_before_admin_panel_tabs_controls', array( $this, 'gt3_before_admin_panel_tabs_controls' ), 10, 2 );

				add_filter( 'gt3pg_before_render_lightbox_class_wrap', function ( $class, $atts ) {
					if ( $atts['ytWidth'] == 1 ) {
						$class['yt_video'] = 'gt3pg_video_limit_size';
					}

					return $class;
				}, 20, 2 );
			}

			public function gt3pg_allowed_shortcode_atts( $atts ) {
				global $gt3_photo_gallery;

				return array_merge( $atts, array(
					'gt3pg_yt_width' => $gt3_photo_gallery['ytWidth'],
				) );
			}

			public function gt3_admin_mix_tabs_controls( $controls ) {
				$controls[15.44] = new gt3pg_admin_mix_tab_control( array(
					'name'            => 'ytWidth',
					'title'           => __( 'YouTube Width', 'gt3pg_pro' ),
					'description'     => __( 'You can enable/disable YouTube max-width.', 'gt3pg_pro' ),
					'main_wrap_class' => 'ytWidth',
					'option'          => new gt3input_onoff( array(
						'name' => 'ytWidth',
					) )
				) );

				return $controls;
			}

			public function gt3_before_admin_panel_tabs_controls( $panels ) {
				$panels["18.11"] = new gt3panel_control( array(
					'title'  => __( "YouTube Width", 'gt3pg_pro' ),
					'name'   => 'gt3pg_yt_width',
					'attr'   => new ArrayObject( array( new gt3attr( 'class', 'gt3pg_setting' ), ) ),
					'option' => new gt3panel_select( array(
						'name'    => 'gt3pg_yt_width',
						'options' => new ArrayObject( array(
							'10' => new gt3options( __( 'Default', 'gt3pg_pro' ), 'default' ),
							'20' => new gt3options( __( 'Disabled', 'gt3pg_pro' ), '0' ),
							'30' => new gt3options( __( 'Enabled', 'gt3pg_pro' ), '1' )
						) )
					) )
				) );

				return $panels;
			}

		}

		$GLOBALS['gt3pg']['extension']['pro_yt_width'] = new gt3pg_extension_pro_yt_width();
	}
