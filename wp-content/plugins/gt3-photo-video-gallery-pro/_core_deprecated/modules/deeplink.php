<?php
defined('ABSPATH') OR exit;

	if ( ! class_exists( 'gt3pg_extension_pro_deeplink' ) ) {
		class gt3pg_extension_pro_deeplink {
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
				$this->inlineActions();

				add_filter( 'gt3pg_allowed_shortcode_atts', array( $this, 'gt3pg_allowed_shortcode_atts' ), 10, 1 );
				add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
				add_action( 'enqueue_block_editor_assets', array( $this, 'wp_enqueue_scripts' ) );

			}

			private function inlineActions() {
				add_filter( 'gt3pg_hidden_lightbox_part', function ( $hiddent_lightbox ) {
					$hiddent_lightbox['56'] = new gt3input_onoff( array(
						'name'  => 'lightboxDeeplink',
						'title' => __( 'Deeplink', 'gt3pg_pro' )
					) );

					return $hiddent_lightbox;
				} );

				add_filter( 'gt3pg_after_render_gallery_lightbox', function ( $output, $atts, $gallery_json, $instance, $selector ) {
					if ( $atts['lightboxDeeplink'] == 1 ) {
						$this->enqueue_scripts();
					}

					return $output;
				}, 20, 5 );

				add_filter( 'gt3pg_render_lightbox_options', function ( $options, $atts ) {
					if ( $atts['lightboxDeeplink'] == 1 ) {
						$options['deepLink'] = 'true';
					}
					return $options;
				}, 20, 2 );
			}

			public function wp_enqueue_scripts() {
//				wp_register_script( 'gt3pg_pro_deeplink.js', $this->rootpath . '/start.js', array( 'jquery','blueimp-gallery.js' ), GT3PG_PRO_PLUGIN_VERSION, true );
			}

			private function enqueue_scripts() {
				wp_enqueue_script( 'gt3pg_pro_deeplink.js' );
			}

			public function gt3pg_allowed_shortcode_atts( $atts ) {
				global $gt3_photo_gallery;

				return array_merge( $atts, array(
					'gt3pg_lightbox_deeplink' => $gt3_photo_gallery['lightboxDeeplink'],
				) );
			}


		}

		$GLOBALS['gt3pg']['extension']['pro_deeplink'] = new gt3pg_extension_pro_deeplink();
	}
