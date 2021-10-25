<?php
defined('ABSPATH') OR exit;

	if ( ! class_exists( 'gt3pg_extension_pro_packery' ) ) {
		class gt3pg_extension_pro_packery {
			private $jsurl;
			private $imgurl;
			private $cssurl;
			private $rootpath;
			private $rooturl;

			private $packeries = array(
				1 => array(
					'lap'  => 6,
					'grid' => 3,
					'elem' => array(
						1 => array( 'w' => 2, 'h' => 2, ),
						3 => array( 'h' => 2, ),
						4 => array( 'w' => 2, ),
						6 => array( 'w' => 2, ),
					)
				),
				2 => array(
					'lap'  => 8,
					'grid' => 4,
					'elem' => array(
						1 => array( 'w' => 2, 'h' => 2, ),
						4 => array( 'w' => 2, ),
						7 => array( 'w' => 2, 'h' => 2, ),
						8 => array( 'w' => 2, ),
					)
				),
				3 => array(
					'lap'  => 10,
					'grid' => 5,
					'elem' => array(
						2  => array( 'h' => 2, ),
						3  => array( 'w' => 2, ),
						4  => array( 'h' => 2, ),
						6  => array( 'w' => 2, 'h' => 2, ),
						7  => array( 'w' => 2, 'h' => 2, ),
						10 => array( 'w' => 2, ),
					)
				),
				4 => array(
					'lap'  => 12,
					'grid' => 4,
					'elem' => array(
						1  => array( 'w' => 2, ),
						6  => array( 'w' => 2, ),
						7  => array( 'w' => 2, ),
						12 => array( 'w' => 2, ),
					)
				),
			);


			public function __construct() {
				$this->jsurl    = plugins_url( 'js/', __FILE__ );
				$this->imgurl   = plugins_url( 'img/', __FILE__ );
				$this->cssurl   = plugins_url( 'css/', __FILE__ );
				$this->rootpath = plugins_url( '', __FILE__ );
				$this->rooturl  = plugin_dir_path( __FILE__ );

				$this->actions();
			}

			private function actions() {
				$this->inlineActions();

				add_action('enqueue_block_editor_assets', array($this,'enqueuePackery'));

				add_filter( 'gt3pg_allowed_shortcode_atts', array( $this, 'gt3pg_allowed_shortcode_atts' ), 10, 1 );
				add_filter( 'gt3_before_admin_panel_tabs_controls', array( $this, 'gt3_before_admin_panel_tabs_controls' ), 10, 2 );

				add_action( 'gt3_render_hidden_admin_mix_tab_control_gt3pg_thumbnail_type', array(
					$this,
					'gt3_render_hidden_admin_mix_tab_control_gt3pg_thumbnail_type_packery'
				), 10, 2 );
				add_action( 'gt3_add_gallery_template_script_main', array( $this, 'gt3_add_gallery_template_script_main' ), 10 );
				add_action( 'gt3pg_admin_default_settings', array( $this, 'gt3pg_admin_default_settings' ), 10 );
				add_action( 'gt3_add_gallery_template_script_mousedown', array( $this, 'gt3_add_gallery_template_script_mousedown' ), 10 );
				add_filter( 'gt3_before_render_admin_panel_control_thumb_type', array(
					$this,
					'gt3_before_render_admin_panel_control_thumb_type'
				), 10, 2 );
				add_filter( 'gt3_before_admin_panel_tabs_controls', array(
					$this,
					'gt3_before_admin_panel_tabs_controls'
				), 10, 2 );
			}

			private function inlineActions() {
				add_filter( 'gt3pg_render_image_caption', function ( $caption, $atts ) {
					return ( $atts['thumb_type'] == 'packery' ) ? ' ' : $caption;
				}, 200, 2 );

				add_filter( 'gt3pg_before_render_thumb_type', function ( $type, $atts ) {
					return in_array( $atts['thumb_type'], array(
						'packery',
					) ) ? $atts['thumb_type'] : $type;

				}, 10, 2 );

				add_filter( 'gt3pg_gallery_class', function ( $class, $atts ) {
					if ( $atts['thumb_type'] == 'packery' ) {
						unset( $class['columns'] );
						$this->enqueuePackery();
					}

					return $class;
				}, 10, 2 );

				add_filter( 'gt3pg_gallery_data', function ( $gallery_data, $atts, $instance, $selector ) {
					if ( $atts['thumb_type'] == 'packery' ) {
						if ( ! key_exists( $atts['packery'], $this->packeries ) ) {
							$atts['packery'] = 0;
						}
						$gallery_data['packery_grid'] = ( json_encode( $this->packeries[ $atts['packery'] ] ) );
						$gallery_data['packery_type'] = $atts['packery'];
					}

					return $gallery_data;
				}, 10, 4 );

				add_filter( 'gt3_before_render_admin_control_gt3pg_thumbnail_type', function ( $control, $name ) {
					$control->option->options['50'] = new gt3options( array(
						'title' => __( 'Packery', 'gt3pg_pro' ),
						'value' => 'packery'
					) );

					return $control;
				}, 10, 2 );



			}


			public function gt3pg_allowed_shortcode_atts( $atts ) {
				global $gt3_photo_gallery;

				return array_merge( $atts, array(
					'packery'                   => $gt3_photo_gallery['packery'],
				) );
			}

			public function gt3_render_hidden_admin_mix_tab_control_gt3pg_thumbnail_type_packery() {
				?>
				<div class="hidden_gt3pg_thumbnail_type_packery gt3pg_display_none" style="display: none;">
					<?php
						echo new gt3input( array(
							'name' => 'packery',
							'attr' => new ArrayObject( array(
								new gt3attr( 'class', 'packery' ),
								new gt3attr( 'maxlength', '1' ),
							) )
						) )
					?>
					<div class="gt3pg_packery_wrap">
						<div class="gt3pg_img_packery_wrap">
							<div class="gt3pg_img_packery"
							     style=" background-image: url('<?php echo GT3PG_PRO_PLUGINROOTURL; ?>/dist/img/type1.png');"></div>
						</div>
						<div class="gt3pg_img_packery_wrap">
							<div class="gt3pg_img_packery"
							     style=" background-image: url('<?php echo GT3PG_PRO_PLUGINROOTURL; ?>/dist/img/type2.png');"></div>
						</div>
						<div class="gt3pg_img_packery_wrap">
							<div class="gt3pg_img_packery"
							     style=" background-image: url('<?php echo GT3PG_PRO_PLUGINROOTURL; ?>/dist/img/type3.png');"></div>
						</div>
						<div class="gt3pg_img_packery_wrap">
							<div class="gt3pg_img_packery"
							     style=" background-image: url('<?php echo GT3PG_PRO_PLUGINROOTURL; ?>/dist/img/type4.png');"></div>
						</div>
					</div>
				</div>

				<?php
			}

			public function enqueuePackery() {
				wp_enqueue_script( 'isotope', $this->jsurl . 'isotope.pkgd.min.js', array( 'jquery' ), GT3PG_PRO_PLUGIN_VERSION, true );
				wp_enqueue_script( 'packery-core', $this->jsurl . 'packery-mode.pkgd.js', array( 'jquery' ), GT3PG_PRO_PLUGIN_VERSION, true );
				wp_enqueue_script( 'gt3pg_packery', $this->jsurl . 'gt3pg_packery.js', array( 'jquery' ), GT3PG_PRO_PLUGIN_VERSION, true );
			}

			public function gt3_add_gallery_template_script_main() {
				?>

				'jQuery(".thumb_type").on("change", function () { ' +
				'   if (jQuery(".thumb_type").val() == "packery") jQuery(".gt3pg_setting.packery_hidden").show(); ' +
				' else jQuery(".gt3pg_setting.packery_hidden").hide(); ' +
				'});' +

				'jQuery(".thumb_type").trigger(\'change\');\n' +

				<?php
			}

			public function gt3pg_admin_default_settings() {
				?>
				packery: 'default',
				<?php
			}

			public function gt3_add_gallery_template_script_mousedown() {
				?>
				'if (jQuery(\'select[name="thumb_type"]\').val() != "packery") {' +
				'    jQuery(\'select[name="packery"]\').val("default").trigger("change").trigger("input").trigger("focus").trigger("blur");' +
				'}' +
				<?php
			}

			public function gt3_before_render_admin_panel_control_thumb_type( $panel, $name ) {
				/* @var gt3panel_control $panel */
				$panel->option->options['51'] = new gt3options( array(
					'title' => __( 'Packery', 'gt3pg_pro' ),
					'value' => 'packery',
				) );

				return $panel;
			}

			public function gt3_before_admin_panel_tabs_controls( $panels ) {
				$panels["90.01"] = new gt3panel_control( array(
					'title'  => __( 'Packery Grid', 'gt3pg_pro' ),
					'name'   => 'packery',
					'attr'   => new ArrayObject( array(
						new gt3attr( 'class', 'gt3pg_setting packery_hidden' ),
					) ),
					'option' => new gt3panel_select( array(
						'name'    => 'packery',
						'options' => new ArrayObject( array(
							'10' => new gt3options( __( 'Default', 'gt3pg_pro' ), 'default' ),
							'20' => new gt3options( __( 'Type1', 'gt3pg_pro' ), '1' ),
							'30' => new gt3options( __( 'Type2', 'gt3pg_pro' ), '2' ),
							'40' => new gt3options( __( 'Type3', 'gt3pg_pro' ), '3' ),
							'50' => new gt3options( __( 'Type4', 'gt3pg_pro' ), '4' ),
						) )
					) )
				) );

				return $panels;
			}
		}

		$GLOBALS['gt3pg']['extension']['pro_packery'] = new gt3pg_extension_pro_packery();
	}
