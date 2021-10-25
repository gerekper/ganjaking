<?php
defined('ABSPATH') OR exit;

	if ( ! class_exists( 'gt3pg_extension_pro_downloads' ) ) {
		class gt3pg_extension_pro_downloads {
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
				add_filter( 'gt3_before_admin_panel_tabs_controls', array( $this, 'gt3_before_admin_panel_tabs_controls' ), 10, 2 );
				add_filter( 'gt3_admin_mix_tabs_controls', array( $this, 'gt3_admin_mix_tabs_controls' ), 10, 1 );

				add_action( 'gt3pg_admin_default_settings', array( $this, 'gt3pg_admin_default_settings' ), 10 );
				add_action( 'gt3_add_gallery_template_script_mousedown', array( $this, 'gt3_add_gallery_template_script_mousedown' ), 10 );
				add_action( 'gt3_add_gallery_template_script_main', array( $this, 'gt3_add_gallery_template_script_main' ), 10 );

				add_filter( 'gt3pg_render_slider_gallery_parts',  array($this, 'gt3pg_render_gallery_parts'), 10, 2 );
				add_filter( 'gt3pg_render_lightbox_gallery_parts',  array($this, 'gt3pg_render_gallery_parts'), 10, 2 );
				add_action( 'enqueue_block_editor_assets',  array($this, 'wp_enqueue_scripts') );

			}

			public function gt3pg_allowed_shortcode_atts( $atts ) {
				global $gt3_photo_gallery;

				return array_merge( $atts, array(
					'gt3pg_download'            => $gt3_photo_gallery['allowDownload'],
				) );
			}

			public function gt3pg_render_gallery_parts($gallery_parts, $atts) {
				if ( $atts['allowDownload'] == 1 ) {
					$gallery_parts['header']->content['30']->addContent( '20', '<a download class="gt3pg_button_download"></a>' );
					$this->wp_enqueue_scripts();
				}
				return $gallery_parts;
			}

			public function gt3_add_gallery_template_script_main() {
				?>

				'jQuery(".thumb_type").on("change", function () { ' +
				'   if (jQuery(".thumb_type").val() == "slider") jQuery(".gt3pg_setting.slider_hidden").show(); ' +
				' else jQuery(".gt3pg_setting.slider_hidden").hide(); ' +
				'});' +

				'jQuery(".thumb_type").trigger(\'change\');\n' +

				<?php
			}


			public function gt3pg_admin_default_settings() {
				?>
				gt3pg_download: 'default',
				<?php
			}

			public function gt3_add_gallery_template_script_mousedown() {
				?>
				'if (jQuery(\'select[name="link"]\').val() != "lightbox" && jQuery(\'select[name="thumb_type"]\').val() != "slider") {' +
				'    jQuery(\'select[name="gt3pg_download"]\').val("default").trigger("change").trigger("input").trigger("focus").trigger("blur");' +
				'}' +
				<?php
			}

			public function gt3_admin_mix_tabs_controls( $controls ) {
				$controls[22] = new gt3pg_admin_mix_tab_control( array(
					'name'            => 'allowDownload',
					'title'           => __( 'Download Image', 'gt3pg_pro' ),
					'description'     => __( 'You can enable/disable the option to download the image in the lightbox and slider.', 'gt3pg_pro' ),
					'main_wrap_class' => 'allowDownload',
					'option'          => new gt3input_onoff( array(
						'name' => 'allowDownload',
					) )
				) );

				return $controls;
			}

			public function gt3_before_admin_panel_tabs_controls( $panels ) {
				$panels["15"] = new gt3panel_control( array(
					'title'  => __( 'Download Image', 'gt3pg_pro' ),
					'name'   => 'gt3pg_download',
					'attr'   => new ArrayObject( array( new gt3attr( 'class', 'gt3pg_setting down_soc_hidden' ), ) ),
					'option' => new gt3panel_select( array(
						'name'    => 'gt3pg_download',
						'options' => new ArrayObject( array(
							'10' => new gt3options( __( 'Default', 'gt3pg_pro' ), 'default' ),
							'20' => new gt3options( __( 'Disabled', 'gt3pg_pro' ), '0' ),
							'30' => new gt3options( __( 'Enabled', 'gt3pg_pro' ), '1' )
						) )
					) )
				) );

				return $panels;
			}

			public function wp_enqueue_scripts() {
//				wp_enqueue_script( 'gt3pg_pro_downloads.js', $this->rootpath . '/index.js', array('blueimp-gallery.js'), GT3PG_PRO_PLUGIN_VERSION, true );
			}

		}

		$GLOBALS['gt3pg']['extension']['pro_downloads'] = new gt3pg_extension_pro_downloads();
	}
