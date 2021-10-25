<?php
defined('ABSPATH') OR exit;

	if ( ! class_exists( 'gt3pg_extension_pro_title' ) ) {
		class gt3pg_extension_pro_title {

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
				add_filter( 'gt3pg_render_image_caption', array( $this, 'gt3pg_render_image_caption' ), 100, 8);
				add_action( 'gt3pg_admin_default_settings', array( $this, 'gt3pg_admin_default_settings' ), 10 );
			}

			public function gt3pg_allowed_shortcode_atts( $atts ) {
				global $gt3_photo_gallery;

				return array_merge( $atts, array(
					'showTitle' => $gt3_photo_gallery['showTitle'],
				) );
			}

			public function gt3pg_admin_default_settings() {
				?>
				gt3pg_show_image_title: 'default',
				<?php
			}

			public function gt3_admin_mix_tabs_controls( $controls ) {
				$controls[14.44] = new gt3pg_admin_mix_tab_control( array(
					'name'            => 'showTitle',
					'title'           => __( 'Show Image Title', 'gt3pg_pro' ),
					'description'     => __( 'You can show the image title on the page. This option is not available for the slider.', 'gt3pg_pro' ),
					'main_wrap_class' => 'showTitle',
					'option'          => new gt3input_onoff( array(
						'name' => 'showTitle',
					) )
				) );

				return $controls;
			}

			public function gt3_before_admin_panel_tabs_controls( $panels ) {
				$panels["18"] = new gt3panel_control( array(
					'title'  => __( "Show Image Title", 'gt3pg_pro' ),
					'name'   => 'gt3pg_show_image_title',
					'attr'   => new ArrayObject( array( new gt3attr( 'class', 'gt3pg_setting' ), ) ),
					'option' => new gt3panel_select( array(
						'name'    => 'gt3pg_show_image_title',
						'options' => new ArrayObject( array(
							'10' => new gt3options( __( 'Default', 'gt3pg_pro' ), 'default' ),
							'20' => new gt3options( __( 'Disabled', 'gt3pg_pro' ), '0' ),
							'30' => new gt3options( __( 'Enabled', 'gt3pg_pro' ), '1' )
						) )
					) )
				) );

				return $panels;
			}

			public function gt3pg_render_image_caption($caption, $atts, $attachment, $output, $id, $gallery_json, $instance, $selector ) {
				if ($atts['showTitle']) {
						$caption =  '<div class="gt3pg_image_title_text" id="' . $selector . '-title-' . $id . '">' . wptexturize( $attachment->post_title ) . '</div>'.$caption;
					}
				return $caption;
			}

		}

		$GLOBALS['gt3pg']['extension']['pro_title'] = new gt3pg_extension_pro_title();
	}
