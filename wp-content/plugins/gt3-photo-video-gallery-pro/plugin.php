<?php
defined('ABSPATH') OR exit;

require_once __DIR__.'/notice.php';

add_action('plugins_loaded', function(){
	require_once __DIR__.'/core/lic.php';
}, 11);

if(!class_exists('gt3_photo_video_galery_pro')) {
	class gt3_photo_video_galery_pro {

		private $require_lite_version = '2.4.0.0';
		private $require_php = '5.6';
		private $plugin_version = null;
		private $license = null;
		private $jsurl;
		private $imgurl;
		private $cssurl;
		private $rootpath;
		private $rooturl;
		private $debug = false;

		private static $instance = null;

		public static function instance(){
			if(!isset(self::$instance) || !(self::$instance instanceof self)) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private function __construct(){
			$this->jsurl    = plugins_url('assets/js/', GT3PG_PRO_FILE);
			$this->imgurl   = plugins_url('assets/img/', GT3PG_PRO_FILE);
			$this->cssurl   = plugins_url('assets/css/', GT3PG_PRO_FILE);
			$this->rooturl  = plugins_url('/', GT3PG_PRO_FILE);
			$this->rootpath = plugin_dir_path(GT3PG_PRO_FILE).'/';

			if(!function_exists('get_plugin_data')) {
				require_once(ABSPATH.'wp-admin/includes/plugin.php');
			}
			$plugin_info          = get_plugin_data(__DIR__.'/gt3-photo-video-gallery-pro.php');
			$this->plugin_version = $plugin_info['Version'];

			if(version_compare(phpversion(), $this->require_php, '<')) {
				add_action('admin_notices', array( $this, 'phpVersionError' ));

				return false;
			}

			if(!$this->version_compare()) {
				return false;
			}
			$this->mainActions();
			add_action('admin_notices', array( $this, 'license_admin_notice' ));

		}

		private function version_compare(){
			if (!defined('GT3PG_PLUGINPATH')) return false;

			$lite_version = get_plugin_data(GT3PG_PLUGINPATH.'/gt3-photo-video-gallery.php');
			$lite_version = $lite_version['Version'];
			if(version_compare($lite_version, $this->require_lite_version, '<')) {
				add_action('admin_notices', function(){
					$msg = sprintf(esc_html__('GT3 Photo & Video Gallery Pro version requires an update of the Lite version up to %s to work properly.', 'gt3pg_pro'), $this->require_lite_version);
					echo '<div class="notice notice-warning gt3pg_error_notice"><p>'.$msg.'</p></div>';
				});

				return false;
			}

			return true;
		}


		public function mainActions(){
			add_action('init', function(){
				load_plugin_textdomain('gt3pg_pro', false, dirname(plugin_basename(__FILE__)).'/languages/');
			});

			add_action('admin_init', function(){
				$redirect = get_transient('_gt3pg_pro_license_redirect');
				if ($redirect) {
					delete_transient('_gt3pg_pro_license_redirect');
					flush_rewrite_rules(true);
					wp_redirect(menu_page_url('gt3pg_pro_license', false));
				}
			});

			add_action('admin_menu', function(){
				add_submenu_page(
					'gt3_photo_gallery_options',
					'Licenses',
					'Licenses',
					'administrator',
					'gt3pg_pro_license',
					array( $this, 'menu_license_page' )
				);
			}, 100);
			add_filter("plugin_row_meta", function($meta_fields, $file){
				if($file == 'gt3-photo-video-gallery-pro/gt3-photo-video-gallery-pro.php') {
					$settings_link = '<a href="'.menu_page_url('gt3pg_pro_license', false).'">'.esc_html__('License', 'gt3pg_pro').'</a>';
					$meta_fields[] = $settings_link;
				}

				return $meta_fields;
			}, 10, 2);
		}

		public function license_admin_notice(){
			echo '<div class="notice notice-warning gt3pg_error_notice is-dismissible"><p>'.esc_html__('GT3 Photo & Video Gallery Pro version requires license activation. ', 'gt3pg_pro').'</p><p>'.'<a class="button button-primary button-large" href="'.esc_url(menu_page_url('gt3pg_pro_license', false)).'">'.esc_html__('Activate License', 'gt3pg_pro').'</a>'.'</p></div>';
		}

		public function actions(){
			static $loaded = false;
			if ($loaded) return;
			$loaded = true;
			if (!defined('GT3PG_PRO_PLUGIN_VERSION'))define('GT3PG_PRO_PLUGIN_VERSION', $this->plugin_version);
			remove_action('admin_notices', array( $this, 'license_admin_notice' ));

			$this->inlineActions();

			add_filter('gt3pg_allowed_shortcode_atts', array( $this, 'gt3pg_allowed_shortcode_atts' ), 10, 1);
			add_filter('gt3pg_render_lightbox_gallery_parts', array( $this, 'gt3pg_render_lightbox_gallery_parts' ), 10, 2);
			add_action('gt3pg_pro_cron', array( $this, 'cron_callback' ));
			add_filter('gt3pg_render_lightbox_options', array( $this, 'gt3pg_render_lightbox_options' ), 10, 2);
//				add_filter( 'gt3_before_render_admin_control_linkTo', array(
//					$this,
//					'gt3_before_render_admin_control_linkTo'
//				), 10, 2 );
			add_filter('gt3_before_render_admin_panel_control_thumb_type', array( $this, 'gt3_before_render_admin_panel_control_thumb_type' ), 10, 2);
			add_filter('gt3_before_render_admin_panel_control_size', array( $this, 'gt3_before_render_admin_panel_control_size' ), 10, 2);
			add_filter('gt3pg_after_render_gallery', array( $this, 'gt3pg_after_render_gallery' ), 100, 5);
			add_filter('gt3_before_admin_panel_tabs_controls', array( $this, 'gt3_before_admin_panel_tabs_controls' ), 10, 2);
			add_filter('gt3_admin_mix_tabs_controls', array( $this, 'gt3_admin_mix_tabs_controls' ), 10, 1);
			add_filter('gt3pg_before_render_lightbox_wrap', array( $this, 'gt3pg_before_render_lightbox_wrap' ), 20, 2);
			add_filter('gt3pg_before_render_lightbox_wrap', array( $this, 'disable_right_click' ), 20, 2);
			add_filter('gt3pg_gallery_div_start', array( $this, 'disable_right_click' ), 20, 2);
			add_filter('gt3pg_before_render_slider_wrap', array( $this, 'disable_right_click' ), 20, 2);
			add_filter('gt3pg_gallery_style', array( $this, 'gt3pg_gallery_style' ), 20, 4);
			add_filter('gt3pg_after_gallery_json_item', array( $this, 'gt3pg_after_gallery_json_item' ), 20, 4);
			add_filter('gt3pg_after_gallery_json_item', array( $this, 'gt3pg_after_gallery_json_item_view_like' ), 20, 4);

			add_action('wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 9);
			add_action('enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ), 11);
			add_action('gt3_add_gallery_template_script_mousedown', array( $this, 'gt3_add_gallery_template_script_mousedown' ), 10);
			add_action('gt3pg_admin_default_settings', array( $this, 'gt3pg_admin_default_settings' ), 10);
			add_action('gt3_render_hidden_admin_mix_tab_control_linkTo', array( $this, 'gt3_render_hidden_admin_mix_tab_control_linkTo' ), 10);
			add_action('gt3_render_hidden_admin_mix_tab_control_imageSize', array( $this, 'gt3_render_hidden_admin_mix_tab_control_imageSize' ), 10);
			add_action('gt3_add_gallery_template_script_main', array( $this, 'gt3_add_gallery_template_script_main' ), 10);

			add_action('gt3_render_hidden_admin_mix_tab_control_gt3pg_thumbnail_type', array(
				$this,
				'gt3_render_hidden_admin_mix_tab_control_gt3pg_thumbnail_type_slider'
			), 10, 2);
			add_action('admin_init', array( $this, 'admin_enqueue_scripts' ));

			add_filter('gt3pg_video_class', array( $this, 'gt3pg_video_class' ), 10, 3);
			add_filter('plugin_action_links', array( $this, 'remove_get_pro_action_links' ), 11, 2);
			require_once __DIR__.'/_core_deprecated/modules/loader.php';
			$GLOBALS['gt3pg']['extension']['pro'] = $this;
		}

		function __debugInfo(){
			return array(
				'name' => 'GT3PG PRO',
				'ver'  => $this->plugin_version,
			);
		}

		public function phpVersionError(){
			$msg   = sprintf(esc_html__('GT3 Photo & Video Gallery Pro version requires php version %s. You have php version: %s ', 'gt3pg_pro'), $this->require_php, phpversion());
			$class = 'notice notice-error gt3pg_error_notice';
			echo '<div class="'.$class.'"><p>'.$msg.'</p></div>';
		}

		public function remove_get_pro_action_links($links, $file){
			if($file == 'gt3-photo-video-gallery/gt3-photo-video-gallery.php') {
				if(key_exists('get-pro', $links)) {
					unset($links['get-pro']);
				};
			}

			return $links;
		}

		private function inlineActions(){

			add_filter('gt3pg_menu_page_title', function(){
				return 'GT3 Gallery Pro';
			});

			add_filter('gt3pg_menu_title', function(){
				return 'GT3 Gallery Pro';
			});

			add_filter('gt3pg_admin_title', function(){
				return GT3PG_PRO_ADMIN_TITLE;
			});
			add_filter('gt3pg_admin_help', function(){
				return 'https://gt3themes.com/contact/';
			});

			add_filter('gt3pg_admin_version', function(){
				$plugin_info = get_plugin_data(GT3PG_PRO_FILE);

				return $plugin_info['Version'];
			});

			add_filter('plugin_action_links', function($links, $file){
				if($file == 'gt3-photo-video-gallery-pro/gt3-photo-video-gallery-pro.php') {
					$settings_link = '<a href="'.menu_page_url('gt3_photo_gallery_options', false).'">'.esc_html__('Settings', 'gt3pg_pro').'</a>';
					array_unshift($links, $settings_link);
				}

				return $links;
			}, 10, 2);

			add_filter('gt3pg_attachment_field_helps', function($msg, $post){
				$msg = __('Please enter your video URL: Youtube, Vimeo or Self-Hosted (.mp4, .webm, .ogg, .ogv)', 'gt3pg_pro');
				$msg .= '<br/><input type="button" id="gt3pg-open-video-media" value="'.__('Open media', 'gt3pg_pro').'" data-post_id="'.$post->ID.'" class="button" />';

				return $msg;
			}, 20, 2);

			add_filter('gt3_before_render_admin_control_gt3pg_thumbnail_type', function($control, $name){
				$control->option->options['51'] = new gt3options(array(
					'title' => __('Slider', 'gt3pg_pro'),
					'value' => 'slider',
				));

				return $control;
			}, 10, 2);

			add_filter('gt3pg_atts', function($atts){
				if($atts['thumb_type'] == 'slider') {
					$atts['link'] = 'none';

					$atts['slider_autoplay']      = $atts['slider_autoplay'] == 1 ? 'true' : 'false';
					$atts['slider_autoplay_time'] = (intval($atts['slider_autoplay_time'])*1000);
					if($atts['slider_autoplay_time'] < 1000) {
						$atts['slider_autoplay_time'] = $GLOBALS['gt3_photo_gallery']['sliderAutoplayTime'];
					}
				}

				return $atts;
			});

			add_filter('gt3pg_atts', function($atts){
				$atts['lightbox_autoplay']      = $atts['lightbox_autoplay'] == 1 ? 'true' : 'false';
				$atts['lightbox_autoplay_time'] = (intval($atts['lightbox_autoplay_time'])*1000);
				if($atts['lightbox_autoplay_time'] < 1000) {
					$atts['lightbox_autoplay_time'] = $GLOBALS['gt3_photo_gallery']['lightboxAutoplayTime'];
				}

				return $atts;
			});

			add_filter('gt3_before_render_admin_control_imageSize', function($control, $name){
				$option = apply_filters('gt3pg_optimized_option', new gt3options(array(
					'title' => __('Optimized *', 'gt3pg_pro'),
					'value' => 'gt3pg_optimized',
					'attr'  => new ArrayObject(array( new gt3attr('disabled', 'disabled') )),
				)));

				$control->option->options['41'] = $option;

				return $control;
			}, 10, 2);

			add_filter('gt3pg_before_render_thumb_type', function($type, $atts){
				return in_array($atts['thumb_type'], array(
					'slider'
				)) ? $atts['thumb_type'] : $type;

			}, 10, 2);

			add_filter('gt3pg_video_src', function($gallery_json_item, $tmp_url){
				if($this->is_allowed_video($tmp_url)) {
					$type = gt3pg_get_video_type_by_link($tmp_url);
					if($type != 404) {
						$gallery_json_item->poster   = $gallery_json_item->href;
						$gallery_json_item->href     = $tmp_url;
						$gallery_json_item->type     = $type;
						$gallery_json_item->is_video = 1;
					}
				}

				return $gallery_json_item;
			}, 10, 2);

			add_filter('the_content', function($content){
				global $post;
				if('attachment' == get_post_type()) {
					$src    = get_post_meta(get_the_ID(), 'gt3_video_url', true);
					$iframe = '';

					if(gt3pg_get_video_type_from_description($src) == false) {
						$class = apply_filters('gt3pg_before_render_slider_class_wrap', array(
							'gt3pg_gallery_wrap'      => 'gt3pg_gallery_wrap',
//								'blueimp-gallery-carousel' => 'blueimp-gallery-carousel',
							'gt3pg_wrap_controls'     => 'gt3pg_wrap_controls',
							'gt3_gallery_type_slider' => 'gt3_gallery_type_slider',
						));
						$type  = gt3pg_get_video_type_by_link($src);
						if($type != 404) {
							$gallery_json_item            = new stdClass();
							$gallery_json_item->title     = $post->post_title;
							$gallery_json_item->thumbnail = wp_get_attachment_image_url($post->ID, 'medium');
							$gallery_json_item->poster    = $post->guid;
							$gallery_json_item->href      = $src;
							$gallery_json_item->type      = $type;
							$iframe                       = '<div id="gt3pg_video" class="'.implode(' ', $class).'"><div class="gt3pg_slides"></div></div>';
							//	$gallery_json_item = json_encode( $gallery_json_item );
							$iframe .= '<script>
				var gt3pg_videolinks = ['.json_encode($gallery_json_item).'];
				var gt3pg_videooptions = {
							carousel: true,
							container: "#gt3pg_video",
						};
				jQuery(function($) {
	                var gt3pg_videogallery = blueimp.Gallery(gt3pg_videolinks ,gt3pg_videooptions);
	                $("#gt3pg_video").height(Math.ceil($("#gt3pg_video").width()*0.5625));
	                $(window).on(\'resize\', function () {
						$("#gt3pg_video").height(Math.ceil($("#gt3pg_video").width()*0.5625));
					})
 				});
				</script>';
						}

					}

					return $iframe.$content;
				} else {
					return $content;
				}
			}, 10, 1);
		}

		public function menu_license_page(){
			$licenses = apply_filters('gt3pg_admin_licence', array());

			?>
			<div class="wrap">
				<h2><?php esc_html_e('Product License Management', 'gt3pg_pro'); ?></h2>
				<h3><?php esc_html_e('Enter your license key(s) here to receive updates for the purchased extension(s).', 'gt3pg_pro'); ?></h3>
				<?php
				if(is_array($licenses) && count($licenses)) {
					echo '<div class="gt3_license_wrap">';
					foreach($licenses as $license) {
//								$license = array(
//									'slug'
//									'status_field'
//									'license_field'
//									'license'
//									'nonce'
//									'plugin_name'
//									'status'
//									'expires'
//								);
						?>

						<div class="gt3pg_block_wrap">
							<form method="post" action="options.php">
								<div class="gt3pg_title">
									<?php echo esc_html($license['plugin_name']); ?>
								</div>
								<div class="gt3pg_key">
									<input type="text" value="<?php echo esc_attr($license['license']) ?>" name="<?php echo esc_attr($license['license_field']) ?>" size="38"
									       maxlength="32"
									       placeholder="<?php esc_attr_e('Enter your license key here', 'gt3pg_pro') ?>" />

									<?php settings_fields($license['slug'].'_license'); ?>
									<?php wp_nonce_field($license['nonce'], $license['nonce']); ?>
								</div>
								<div class="gt3pg_status">
									<?php _e('Status:'); ?>
									<?php if($license['status'] !== false && $license['status'] == 'valid') { ?>
										<span style="color:green;"><?php esc_html_e('Active'); ?></span>
									<?php } else { ?>
										<span style="color:red;"><?php esc_html_e('Inactive'); ?></span>
									<?php } ?>
								</div>
								<div class="gt3pg_expires">
									<?php
									if($license['expires'] != 'invalid') {
										echo $license['expires'] != 'lifetime' ? sprintf(esc_html__('Expires %s'), date('d.m.Y H:i', $license['expires'])) : esc_html__('Lifetime license');
									} ?>
								</div>
								<div class="gt3pg_form_submit">
									<input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes') ?>">
									<?php
									if(!empty($license['license'])) {
										if($license['status'] !== false && $license['status'] == 'valid') { ?>
											<input type="submit" class="button-secondary" name="<?php echo esc_attr($license['slug']) ?>_deactivate"
											       value="<?php esc_attr_e('Deactivate License'); ?>" />
										<?php } else { ?>
											<input type="submit" class="button-secondary" name="<?php echo esc_attr($license['slug']) ?>_activate"
											       value="<?php esc_attr_e('Activate License'); ?>" />
										<?php }
									} ?>
								</div>
							</form>

						</div>

						<?php
					}
					echo '</div>';
				}
				?>

			</div>
			<?php
		}

		public static function activated(){
			self::deactivated();
			wp_schedule_event(time(), 'hourly', 'gt3pg_pro_cron');
			set_transient('_gt3pg_pro_license_redirect', 1, 30);
		}

		public static function deactivated(){
			wp_clear_scheduled_hook('gt3pg_pro_cron');
		}

		public function cron_callback(){
			$this->license = gt3pg_pro_plugin_updater::instance();
			if($this->license instanceof gt3pg_pro_plugin_updater) {
				/* @var gt3pg_pro_plugin_updater $this ->license */
				$data = $this->license->check_license();
				$this->gt3pg_save_file('cron_callback', $data);
			}
		}

		function gt3pg_save_file($file, $value){
			if(!$this->debug) {
				return;
			}
			$path = dirname(__FILE__).'/log';
			if(!file_exists($path)) {
				mkdir($path);
			}

			$date = date('d.m.Y_h.i.s');
			$file = $path.'/'.$file.'_'.$date.'.txt';
			$fp   = fopen($file, 'w+');
			if(is_array($value) || is_object($value)) {
				if(is_object($value)) {
					$value = get_object_vars($value);
				}
				$value = print_r($value, true);
			}
			fwrite($fp, $value);
			fflush($fp);
			fclose($fp);
		}

		public function gt3pg_video_class($class, $gallery_json_item, $tmp_url){
			if($this->is_allowed_video($tmp_url)) {
				return $class.' mfp-iframe';
			} else {
				return $class;
			}
		}


		private function is_allowed_video($src){
			$ext = substr(strrchr($src, '.'), 1);

			return in_array($ext, array( 'mp4', 'webm', 'ogg', 'ogv' ));
		}

		///////////////////////////////////////////////////////////////////////////
		///                                                                     ///
		///         Actions                                                     ///
		///                                                                     ///
		///////////////////////////////////////////////////////////////////////////

		public function admin_enqueue_scripts(){
			wp_enqueue_script('gt3pg_pro_admin.js', $this->rooturl.'dist/js/admin/admin.js', array( 'jquery' ), $this->plugin_version);
		}

		public function enqueue_block_editor_assets(){
			global $gt3pg_defaults;

			/*wp_localize_script(
				'gt3pg_pro_editor',
				'gt3pg',
				array(
					'defaults' => $gt3pg_defaults,
				)
			);*/

			/*			wp_enqueue_script('gt3pg_pro_editor', $this->rooturl.'dist/editor.js', array(), $this->plugin_version, true);*/
			$this->wp_enqueue_scripts();
		}

		function get_jed_locale_data($domain){
			$translations = get_translations_for_domain($domain);

			$locale = array(
				'' => array(
					'domain' => $domain,
					'lang'   => is_admin() ? get_user_locale() : get_locale(),
				),
			);

			if(!empty($translations->headers['Plural-Forms'])) {
				$locale['']['plural_forms'] = $translations->headers['Plural-Forms'];
			}

			foreach($translations->entries as $msgid => $entry) {
				$locale[$msgid] = $entry->translations;
			}

			return $locale;
		}


		public function wp_enqueue_scripts(){
			$locale  = $this->get_jed_locale_data('gt3pg_pro');
			$content = ';window.wp && wp.i18n && wp.i18n.setLocaleData && wp.i18n.setLocaleData('.json_encode($locale).', "gt3pg_pro" );';
			$content .= 'window.ajaxurl = window.ajaxurl || "'.admin_url('admin-ajax.php').'";';
			wp_enqueue_script('gt3pg_pro_frontend', $this->rooturl.'dist/js/deprecated.js', array(
				'blueimp-gallery.js',
				'wp-i18n',
				'imagesloaded',
			), $this->plugin_version, true);
			wp_script_add_data('gt3pg_pro_frontend', 'data', $content);

			wp_register_script('isotope', $this->rooturl.'dist/js/isotope.pkgd.min.js', array(), $this->plugin_version, true);
			wp_register_script('gt3pg_blueimp-gallery-indicator.js', $this->rooturl.'dist/js/blueimp-gallery-indicator.js', array(), $this->plugin_version, true);

			wp_enqueue_script('blueimp-gallery.js');
//			wp_enqueue_script('gt3pg_blueimp-gallery-indicator.js');

//			wp_enqueue_style('gt3pg_pro.css', $this->rooturl.'dist/css/gt3pg.css', null, $this->plugin_version);

			wp_register_script('vimeo_api', 'https://player.vimeo.com/api/player.js', array(), false, true);
			wp_register_script('youtube_api', 'https://www.youtube.com/iframe_api', array(), false, true);
		}


		public function gt3_add_gallery_template_script_main(){
			?>
			'jQuery(".link").on("change", function () { ' +

			'   if (jQuery(".link").val() == "lightbox") jQuery(".gt3pg_setting.lightbox_hidden").show(); ' +
			' else jQuery(".gt3pg_setting.lightbox_hidden").hide(); ' +
			'   if (jQuery(".thumb_type").val() == "slider" || jQuery(".link").val() == "lightbox") jQuery(".gt3pg_setting.down_soc_hidden").show(); ' +
			' else jQuery(".gt3pg_setting.down_soc_hidden").hide(); ' +
			'});' +
			'jQuery(".link").trigger(\'change\');\n' +

			<?php
		}

		public function gt3pg_admin_default_settings(){
			?>
			lightbox_autoplay: 'default',
			lightbox_autoplay_time: '<?php echo esc_html($GLOBALS["gt3_photo_gallery_defaults"]['lightboxAutoplayTime']) ?>',
			lightbox_preview: 'default',
			slider_autoplay: 'default',
			slider_autoplay_time: '<?php echo esc_html($GLOBALS["gt3_photo_gallery_defaults"]['sliderAutoplayTime']) ?>',
			slider_preview: 'default',
			gt3pg_social: 'default',
			gt3pg_right_click: 'default',
			gt3pg_lightbox_cover: 'default',
			gt3pg_slider_cover: 'default',
			gt3pg_lightbox_image_size: 'default',
			gt3pg_slider_image_size: 'default',

			<?php
		}

		public function gt3_add_gallery_template_script_mousedown(){
			?>
			'if (jQuery(\'select[name="link"]\').val() != "lightbox") {' +
			'    jQuery(\'select[name="lightbox_autoplay"]\').val("default").trigger("change").trigger("input").trigger("focus").trigger("blur");' +
			'    jQuery(\'input[name="lightbox_autoplay_time"]\').val(<?php echo esc_html($GLOBALS["gt3_photo_gallery_defaults"]["lightboxAutoplayTime"]) ?>).trigger("change").trigger("input").trigger("focus").trigger("blur");' +
			'    jQuery(\'select[name="lightbox_preview"]\').val("default").trigger("change").trigger("input").trigger("focus").trigger("blur")' +
			'}' +

			'if (jQuery(\'select[name="thumb_type"]\').val() != "slider") {' +
			'    jQuery(\'select[name="slider_autoplay"]\').val("default").trigger("change").trigger("input").trigger("focus").trigger("blur");' +
			'    jQuery(\'input[name="slider_autoplay_time"]\').val(<?php echo esc_html($GLOBALS["gt3_photo_gallery_defaults"]['sliderAutoplayTime']) ?>).trigger("change").trigger("input").trigger("focus").trigger("blur");' +
			'    jQuery(\'select[name="slider_preview"]\').val("default").trigger("change").trigger("input").trigger("focus").trigger("blur")' +
			'}' +

			'if (jQuery(\'select[name="link"]\').val() != "lightbox" && jQuery(\'select[name="thumb_type"]\').val() != "slider") {' +
			'    jQuery(\'select[name="gt3pg_social"]\').val("default").trigger("change").trigger("input").trigger("focus").trigger("blur");' +
			'}' +
			<?php
		}

		public function gt3_render_hidden_admin_mix_tab_control_linkTo(){
			$optimized_option = apply_filters('gt3pg_optimized_option', new gt3options(array(
				'title' => esc_html__('Optimized *', 'gt3pg_pro'),
				'value' => 'gt3pg_optimized',
				'attr'  => new ArrayObject(array( new gt3attr('disabled', 'disabled') )),
			)));

			$hiddent_lightbox = array(
				'10' => '<div>'.
				        new gt3select(array(
					        'name'    => 'lightboxImageSize',
					        'attr'    => array( new gt3attr('class', 'lightbox_image_size'), ),
					        'options' => new ArrayObject(array(
						        '10' => new gt3options(__('Large', 'gt3pg_pro'), 'large'),
						        '20' => new gt3options(__('Full', 'gt3pg_pro'), 'full'),
						        '30' => $optimized_option,
					        ))
				        )).PHP_EOL
				        .esc_html__('Select Image Size', 'gt3pg_pro')
				        .'</div>',
				'20' => '<div class="lightboxImageSize_full_hidden gt3pg_notice_red" style="display: none">'.
				        esc_html__('Not recommended. If the images are not optimized for the web it can lead to the slow page loading.', 'gt3pg_pro').
				        '</div>',
				'21' => '<div class="gt3pg_lightbox_optimized_notice_hidden gt3pg_notice_red" style="display: none">'.
				        sprintf('%s <a href="https://gt3themes.com/gt3-photo-video-gallery-pro-is-live-now/?#gt3-optimizer">%s</a>',
					        esc_html__('* You can get the GT3 Image Optimizer via this link', 'gt3pg_pro'),
					        esc_html__('https://gt3themes.com/image-optimizer/ ', 'gt3pg_pro')).
				        '</div>',
				'30' => new gt3input_onoff(array(
					'name'  => 'lightboxAutoplay',
					'title' => __('Autoplay', 'gt3pg_pro')
				)),
				'40' => '<div class="toggle-group"><label>'.
				        new gt3input(array(
					        'name' => 'lightboxAutoplayTime',
					        'attr' => array(
						        new gt3attr('size', '3'),
						        new gt3attr('maxlength', '3'),
						        new gt3attr('class', 'short-input'),
					        )
				        )).
				        __('Interval (s)', 'gt3pg_pro').
				        '</label></div>',
				'50' => new gt3input_onoff(array(
					'name'  => 'lightboxThumbnails',
					'title' => __('Thumbnails', 'gt3pg_pro')
				)),
				'60' => new gt3input_onoff(array(
					'name'  => 'lightboxCover',
					'title' => __('Fill entire space with image', 'gt3pg_pro')
				))
			);
			$hiddent_lightbox = apply_filters('gt3pg_hidden_lightbox_part', $hiddent_lightbox);
			ksort($hiddent_lightbox);
			?>
			<div class="hidden_linkTo_lightbox gt3pg_display_none" style="display: none;">
				<?php
				if(is_array($hiddent_lightbox) && count($hiddent_lightbox)) {
					foreach($hiddent_lightbox as $part) {
						echo $part;
					}
				}
				?>
			</div>

			<?php
		}

		public function gt3_render_hidden_admin_mix_tab_control_imageSize(){
			?>
			<div class="hidden_imageSize gt3pg_display_none" style="display: none;">
				<div class="gt3pg_optimized_full_hidden gt3pg_notice_red">
					<?php esc_html_e('Not recommended. If the images are not optimized for the web it can lead to the slow page loading.', 'gt3pg_pro') ?>
				</div>
			</div>
			<?php
			if(!key_exists('gt3pg', $GLOBALS) || !key_exists('extension', $GLOBALS['gt3pg']) || !key_exists('pro_optimized', $GLOBALS['gt3pg']['extension'])) {
				echo '<div class="imageSize_optimized_notice_hidden gt3pg_notice_red gt3pg_display_none" style="display: none">'.
				     sprintf('%s <a href="https://gt3themes.com/gt3-photo-video-gallery-pro-is-live-now/?#gt3-optimizer">%s</a>',
					     esc_html__('* You can get the GT3 Image Optimizer via this link', 'gt3pg_pro'),
					     esc_html__('https://gt3themes.com/image-optimizer/ ', 'gt3pg_pro')).
				     '</div>';
			}
		}


		public function gt3_render_hidden_admin_mix_tab_control_gt3pg_thumbnail_type_slider(){
			?>
			<div class="hidden_gt3pg_thumbnail_type_slider gt3pg_display_none" style="display: none;">
				<div class="toggle-group">
					<?php
					$optimized_option = apply_filters('gt3pg_optimized_option', new gt3options(array(
						'title' => __('Optimized *', 'gt3pg_pro'),
						'value' => 'gt3pg_optimized',
						'attr'  => new ArrayObject(array( new gt3attr('disabled', 'disabled') )),
					)));
					echo new gt3select(array(
						'name'    => 'sliderImageSize',
						'attr'    => array( new gt3attr('class', 'slider_image_size'), ),
						'options' => new ArrayObject(array(
							'10' => new gt3options(__('Large', 'gt3pg_pro'), 'large'),
							'20' => new gt3options(__('Full', 'gt3pg_pro'), 'full'),
							'30' => $optimized_option,
						))
					));
					esc_html_e('Select image size', 'gt3pg_pro');
					?>
				</div>
				<div class="sliderImageSize_full_hidden gt3pg_notice_red" style="display: none">
					<?php esc_html_e('Not recommended. If the images are not optimized for the web it can lead to the slow page loading.', 'gt3pg_pro') ?>
				</div>
				<?php
				// Slider
				if(!key_exists('gt3pg', $GLOBALS) || !key_exists('extension', $GLOBALS['gt3pg']) || !key_exists('pro_optimized', $GLOBALS['gt3pg']['extension'])) {
					echo '<div class="gt3pg_slider_optimized_notice_hidden gt3pg_notice_red" style="display: none">'.
					     sprintf('%s <a href="https://gt3themes.com/gt3-photo-video-gallery-pro-is-live-now/?#gt3-optimizer">%s</a>',
						     esc_html__('* You can get the GT3 Image Optimizer via this link', 'gt3pg_pro'),
						     esc_html__('https://gt3themes.com/image-optimizer/ ', 'gt3pg_pro')).
					     '</div>';
				}
				?>
				<?php echo new gt3input_onoff(array(
					'name'  => 'sliderAutoplay',
					'title' => __('Autoplay', 'gt3pg_pro')
				)) ?>
				<div class="toggle-group">
					<label>
						<?php
						echo new gt3input(array(
							'name' => 'sliderAutoplayTime',
							'attr' => array(
								new gt3attr('size', '3'),
								new gt3attr('maxlength', '3'),
								new gt3attr('class', 'short-input'),
							)
						));
						?>
						<?php esc_html_e('Interval (s)', 'gt3pg_pro') ?>
					</label>
				</div>
				<?php echo new gt3input_onoff(array(
					'name'  => 'sliderThumbnails',
					'title' => __('Thumbnails', 'gt3pg_pro')
				))
				?>
				<?php echo new gt3input_onoff(array(
					'name'  => 'sliderCover',
					'title' => __('Fill entire space with image', 'gt3pg_pro')
				))
				?>

			</div>
			<?php
		}




		///////////////////////////////////////////////////////////////////////////
		///                                                                     ///
		///         Filters                                                     ///
		///                                                                     ///
		///////////////////////////////////////////////////////////////////////////

		public function gt3pg_after_gallery_json_item_view_like($gallery_json_item, $atts, $id, $attachment){
			$gallery_json_item->all_viewed = intval(get_post_meta($id, 'gt3pg_views', true));

			return $gallery_json_item;
		}

		public function gt3pg_after_gallery_json_item($gallery_json_item, $atts, $id, $attachment){
			if($atts['thumb_type'] == 'slider') {
				$size  = $attachment->post_mime_type == 'image/gif' ? 'full' : $atts['sliderImageSize'];
				$image = wp_get_attachment_image_url($id, $size);
				if($gallery_json_item->is_video == 0) {
					$gallery_json_item->href = $image;
				} else {
					if(property_exists($gallery_json_item, 'youtube')) {
						$gallery_json_item->thumbnail = $image;
					} else if(property_exists($gallery_json_item, 'vimeo')) {
						$gallery_json_item->poster = $image;
					}
				}
			} else {
				if($atts['link'] == 'lightbox') {
					$size  = $attachment->post_mime_type == 'image/gif' ? 'full' : $atts['lightboxImageSize'];
					$image = wp_get_attachment_image_url($id, $size);
					if($gallery_json_item->is_video == 0) {
						$gallery_json_item->href = $image;
					} else {
						if(property_exists($gallery_json_item, 'youtube')) {
							$gallery_json_item->thumbnail = $image;
						} else if(property_exists($gallery_json_item, 'vimeo')) {
							$gallery_json_item->poster = $image;
						}
					}
				}
			}

			return $gallery_json_item;
		}

		public function gt3pg_gallery_style($gallery_style, $atts, $selector, $instance){
			if($atts['thumb_type'] == 'slider') {
				$gallery_style .= '/* Slider */
					#popup_'.$selector.'.gt3pg_wrap_autoplay:not(.changing-slide) .gt3pg_duration {
				animation-duration: '.$atts['slider_autoplay_time']*2/1000 .'s;
			}
			
			';
			} else if($atts['link'] == 'lightbox') {
				$gallery_style .= '/* Lightbox */
					#popup_'.$selector.'.gt3pg_wrap_autoplay:not(.changing-slide) .gt3pg_duration {
				animation-duration: '.$atts['lightbox_autoplay_time']*2/1000 .'s;
			}
			';
			}

			return $gallery_style;
		}

		public function disable_right_click($output, $atts){
			/* @var gt3_el $output */
			if($atts['rightClick'] == 1) {
				$output->addAttrs(array( 'oncontextmenu' => 'return false', 'onselectstart' => 'return false' ));
			}

			return $output;
		}

		public function gt3pg_before_render_lightbox_wrap($output, $atts){
			/* @var gt3_el $output */
			$output->removeClass('gt3pg_version_lite');
			$output->addClass('gt3pg_version_pro');

			return $output;
		}

		public function gt3_admin_mix_tabs_controls($controls){
			$controls[61] = new gt3pg_admin_mix_tab_control(array(
				'name'            => 'rightClick',
				'title'           => __('Right Click Guard', 'gt3pg_pro'),
				'description'     => __('You can enable/disable right-click guard option to protect your images from downloading.', 'gt3pg_pro'),
				'main_wrap_class' => 'rightClick',
				'option'          => new gt3input_onoff(array(
					'name' => 'rightClick',
				))
			));
			$controls[23] = new gt3pg_admin_mix_tab_control(array(
				'name'            => 'socials',
				'title'           => __("Social Links", 'gt3pg_pro'),
				'description'     => __('You can enable/disable the option to share the image in the lightbox and slider.', 'gt3pg_pro'),
				'main_wrap_class' => 'socials',
				'option'          => new gt3input_onoff(array(
					'name' => 'socials',
				))
			));

			return $controls;
		}

		public function gt3pg_after_render_gallery($output, $atts, $gallery_json, $instance, $selector){
			if($atts['thumb_type'] == 'slider') {

				$gallery_parts = array(
					'header'   => gt3_el::Create()->addClass('gt3pg_slide_header')
					                    ->addContent('20', '<div class="free-space"></div>'),
					'slides'   => '<div class="gt3pg_slides"></div>',
					'footer'   => gt3_el::Create()->addClass('gt3pg_slide_footer')
					                    ->addContent('10', '<div class="gt3pg_title_wrap">
                                        <div class="gt3pg_title gt3pg_clip"></div>
                                        <div class="gt3pg_description gt3pg_clip"></div>
                                    </div>')
					                    ->addContent('20', '<div class="free-space"></div>')
					                    ->addContent('40', '<div class="gt3pg_caption_wrap">
                                        <div class="gt3pg_caption_current"></div>
                                        <div class="gt3pg_caption_delimiter"></div>
                                        <div class="gt3pg_caption_all"></div>
                                    </div>'
					                    ),
					'controls' => gt3_el::Create()->addClass('gt3pg_controls')
					                    ->addContent('10', '<div class="gt3pg_prev_wrap"><div class="gt3pg_prev"></div></div>')
					                    ->addContent('20', '<div class="gt3pg_next_wrap"><div class="gt3pg_next"></div></div>')
				);

				if($atts['socials'] == 1) {
					$gallery_parts['header']
						->addContent('10', '<div class="gt3pg_share_wrap">
                                        <a class="gt3pg_share_twitter" target="_blank"></a>
                                        <a class="gt3pg_share_facebook" target="_blank"></a>
                                        <a class="gt3pg_share_pinterest" target="_blank"></a>
                                        <a class="gt3pg_share_google_plus" target="_blank"></a>
                                    </div>');
				}
				$gallery_parts['header']
					->addContent('30', gt3_el::Create()
					                         ->addClass('gt3pg_icons_wrap')
					                         ->addContent('30', '<a class="gt3pg_button_fullsize"></a>')
//					      ->addContent( '11', '<a class="gt3pg_button_shopping-bag"></a>' )
                                             ->addContent('40', '<a class="gt3pg_button_controls"></a>')
					);

				if($atts['slider_autoplay'] == 'true') {
					$gallery_parts['footer']
						->addContent('30', gt3_el::Create()->addClass('gt3pg_autoplay_wrap')
						                         ->addContent('<div class="gt3pg_autoplay_button">
                                                          	<svg class="gt3pg_svg" width="32px" height="32px" viewport="0 0 32px 32px" version="1.1" xmlns="http://www.w3.org/2000/svg">
		<rect class="gt3pg_svg_animate gt3pg_duration" x="1" y="1" width="30px" height="30px" rx="50%" ry="50%" fill="#000000" fill-opacity="0" stroke-width="2" style="fill: #747474;"></rect>
	</svg>
	<div class="gt3pg_play-pause"></div>
                                                        </div>'));
				}

				if($atts['slider_preview'] == 1) {
					$gallery_parts['footer']->addContent('50', '<div class="gt3pg_thumbnails"></div>');
				}

				$gallery_parts = apply_filters('gt3pg_render_slider_gallery_parts', $gallery_parts, $atts);
				$class         = apply_filters('gt3pg_before_render_slider_class_wrap', array(
					'gt3pg_gallery_wrap'      => 'gt3pg_gallery_wrap',
//						'blueimp-gallery-carousel'  => 'blueimp-gallery-carousel',
					'gt3pg_wrap_controls'     => 'gt3pg_wrap_controls',
					'gt3_gallery_type_slider' => 'gt3_gallery_type_slider',
					'gt3pg_version_pro'       => 'gt3pg_version_pro'
				));

				$output = gt3_el::Create()->addClasses($class)->addAttr('id', 'popup_'.$selector);

				if(is_array($gallery_parts) && count($gallery_parts)) {
					$output->addContent(implode(PHP_EOL, $gallery_parts));
				}
				$output = apply_filters('gt3pg_before_render_slider_wrap', $output, $atts);

				$output = $output->__toString();
				/////////////////////////////

				$options = array(
					'carousel'          => 'true',
					'startSlideshow'    => $atts['slider_autoplay'],
					'slideshowInterval' => $atts['slider_autoplay_time'],
					'container'         => '"#popup_'.$selector.'"',
					'instance'          => $instance,
				);

				if($atts['sliderCover'] == 1) {
					$options['stretchImages'] = '"cover"';
				}
				$options['toggleSlideshowOnSpace'] = $atts['slider_autoplay'];

				//////////////////////

				$options    = apply_filters('gt3pg_render_slider_options', $options, $atts);
				$pr_options = array();
				foreach($options as $k => $v) {
					$pr_options[] .= $k.': '.$v;
				}
				$options = implode(', '.PHP_EOL, $pr_options);

				$output .= '<script>
				var links'.$instance.' = ['.implode(",", $gallery_json).'];
				var options'.$instance.'  = { '.$options.' };
				var '.$selector.' = null;
				jQuery(function($) {
	                '.$selector.' = blueimp.Gallery(links'.$instance.' ,options'.$instance.' );
	              
 				});
				</script>';

			}

			return $output;
		}

		public function gt3pg_render_lightbox_gallery_parts($gallery_parts, $atts){
			/* @var gt3_el[] $gallery_parts */

			if($atts['socials'] == 1) {
				$gallery_parts['header']
					->addContent('10', '<div class="gt3pg_share_wrap">
                                        <a class="gt3pg_share_twitter" target="_blank"></a>
                                        <a class="gt3pg_share_facebook" target="_blank"></a>
                                        <a class="gt3pg_share_pinterest" target="_blank"></a>
                                        <a class="gt3pg_share_google_plus" target="_blank"></a>
                                    </div>');
			}
			$gallery_parts['header']
				->addContent('30', gt3_el::Create()
				                         ->addClass('gt3pg_icons_wrap')
				                         ->addContent('30', '<a class="gt3pg_button_fullsize"></a>')
//				                          ->addContent( '11', '<a class="gt3pg_button_shopping-bag"></a>' )
                                         ->addContent('40', '<a class="gt3pg_button_controls"></a>'));
			if($atts['lightbox_autoplay'] == 'true') {
				$gallery_parts['footer']
					->addContent('30', gt3_el::Create()->addClass('gt3pg_autoplay_wrap')
					                         ->addContent('<div class="gt3pg_autoplay_button">
                                                          	<svg class="gt3pg_svg" width="32px" height="32px" viewport="0 0 32px 32px" version="1.1" xmlns="http://www.w3.org/2000/svg">
		<rect class="gt3pg_svg_animate gt3pg_duration" x="1" y="1" width="30px" height="30px" rx="50%" ry="50%" fill="#000000" fill-opacity="0" stroke-width="2" style="fill: #747474;"></rect>
	</svg>
	<div class="gt3pg_play-pause"></div>
                                                        </div>'));
			}

			if($atts['lightbox_preview'] == 1) {
				$gallery_parts['footer']->addContent('50', '<div class="gt3pg_thumbnails"></div>');
			}

			return $gallery_parts;
		}

		public function gt3_before_render_admin_panel_control_thumb_type($panel, $name){
			/* @var gt3panel_control $panel */

			$panel->option->options['52'] = new gt3options(array(
				'title' => __('Slider', 'gt3pg_pro'),
				'value' => 'slider',
			));

			return $panel;
		}

		public function gt3_before_render_admin_panel_control_size($panel, $name){
			/* @var gt3panel_control $panel */
			$option = apply_filters('gt3pg_optimized_option', new gt3options(array(
				'title' => __('Optimized', 'gt3pg_pro'),
				'value' => 'gt3pg_optimized',
				'attr'  => new ArrayObject(array( new gt3attr('disabled', 'disabled') )),
			)));

			$panel->option->options['41'] = $option;

			return $panel;
		}


		public function gt3pg_render_lightbox_options($options, $atts){


			$options['startSlideshow']    = $atts['lightbox_autoplay'];
			$options['slideshowInterval'] = $atts['lightbox_autoplay_time'];

			$options['toggleSlideshowOnSpace'] = $atts['lightbox_autoplay'];

			if($atts['lightboxCover'] == 1) {
				$options['stretchImages'] = '"cover"';
			}

			return $options;
		}

		public function gt3pg_allowed_shortcode_atts($atts){
			global $gt3_photo_gallery;

			return array_merge($atts, array(
				'lightbox_autoplay'         => $gt3_photo_gallery['lightboxAutoplay'],
				'lightbox_autoplay_time'    => $gt3_photo_gallery['lightboxAutoplayTime'],
				'lightbox_preview'          => $gt3_photo_gallery['lightboxThumbnails'],
				'slider_autoplay'           => $gt3_photo_gallery['sliderAutoplay'],
				'slider_autoplay_time'      => $gt3_photo_gallery['sliderAutoplayTime'],
				'slider_preview'            => $gt3_photo_gallery['sliderThumbnails'],
				'socials'                   => $gt3_photo_gallery['socials'],
				'gt3pg_social'              => $gt3_photo_gallery['socials'],
				'gt3pg_right_click'         => $gt3_photo_gallery['rightClick'],
				'gt3pg_lightbox_cover'      => $gt3_photo_gallery['lightboxCover'],
				'gt3pg_slider_cover'        => $gt3_photo_gallery['sliderCover'],
				'gt3pg_lightbox_image_size' => $gt3_photo_gallery['lightboxImageSize'],
				'gt3pg_slider_image_size'   => $gt3_photo_gallery['sliderImageSize'],
			));
		}

		public function gt3_before_admin_panel_tabs_controls($panels){
			// Lightbox options
			$panels["10.1"] = new gt3panel_control(array(
				'title'  => __('Autoplay', 'gt3pg_pro'),
				'name'   => 'lightbox_autoplay',
				'attr'   => new ArrayObject(array(
					new gt3attr('class', 'gt3pg_setting lightbox_hidden'),
				)),
				'option' => new gt3panel_select(array(
					'name'    => 'lightbox_autoplay',
					'options' => new ArrayObject(array(
						'10' => new gt3options(__('Default', 'gt3pg_pro'), 'default'),
						'20' => new gt3options(__('Disabled', 'gt3pg_pro'), '0'),
						'30' => new gt3options(__('Enabled', 'gt3pg_pro'), '1')
					))
				))
			));
			$panels["10.2"] = new gt3panel_control(array(
				'title'  => __('Autoplay Time (sec.)', 'gt3pg_pro'),
				'name'   => 'lightbox_autoplay_time',
				'attr'   => new ArrayObject(array(
					new gt3attr('class', 'gt3pg_setting lightbox_hidden'),
				)),
				'option' => new gt3panel_input(array(
					'name' => 'lightbox_autoplay_time',
					'attr' => new ArrayObject(array(
						new gt3attr('class', 'short-input'),
						new gt3attr('maxlength', '3'),
						new gt3attr('data-setting', 'lightbox_autoplay_time'),
					))
				))
			));

			$panels["10.3"] = new gt3panel_control(array(
				'title'  => __('Thumbnails', 'gt3pg_pro'),
				'name'   => 'lightbox_preview',
				'attr'   => new ArrayObject(array(
					new gt3attr('class', 'gt3pg_setting lightbox_hidden'),
				)),
				'option' => new gt3panel_select(array(
					'name'    => 'lightbox_preview',
					'options' => new ArrayObject(array(
						'10' => new gt3options(__('Default', 'gt3pg_pro'), 'default'),
						'20' => new gt3options(__('Disabled', 'gt3pg_pro'), '0'),
						'30' => new gt3options(__('Enabled', 'gt3pg_pro'), '1')
					))
				))
			));

			$option         = apply_filters('gt3pg_optimized_option', new gt3options(array(
				'title' => __('Optimized', 'gt3pg_pro'),
				'value' => 'gt3pg_optimized',
				'attr'  => new ArrayObject(array( new gt3attr('disabled', 'disabled') )),
			)));
			$panels["10.4"] = new gt3panel_control(array(
				'title'  => __('Select image size', 'gt3pg_pro'),
				'name'   => 'gt3pg_lightbox_image_size',
				'attr'   => new ArrayObject(array(
					new gt3attr('class', 'gt3pg_setting lightbox_hidden'),
				)),
				'option' => new gt3panel_select(array(
					'name'    => 'gt3pg_lightbox_image_size',
					'options' => new ArrayObject(array(
						'10' => new gt3options(__('Default', 'gt3pg_pro'), 'default'),
						'20' => new gt3options(__('Large', 'gt3pg_pro'), 'large'),
						'30' => new gt3options(__('Full', 'gt3pg_pro'), 'full'),
						'40' => $option
					))
				))
			));
			$panels["10.5"] = new gt3panel_control(array(
				'title'  => __('Image Scaling', 'gt3pg_pro'),
				'name'   => 'gt3pg_lightbox_cover',
				'attr'   => new ArrayObject(array( new gt3attr('class', 'gt3pg_setting lightbox_hidden'), )),
				'option' => new gt3panel_select(array(
					'name'    => 'gt3pg_lightbox_cover',
					'options' => new ArrayObject(array(
						'10' => new gt3options(__('Default', 'gt3pg_pro'), 'default'),
						'20' => new gt3options(__('Disabled', 'gt3pg_pro'), '0'),
						'30' => new gt3options(__('Enabled', 'gt3pg_pro'), '1')
					))
				))
			));

			// Slider options
			$panels["90.1"] = new gt3panel_control(array(
				'title'  => __('Autoplay', 'gt3pg_pro'),
				'name'   => 'slider_autoplay',
				'attr'   => new ArrayObject(array( new gt3attr('class', 'gt3pg_setting slider_hidden'), )),
				'option' => new gt3panel_select(array(
					'name'    => 'slider_autoplay',
					'options' => new ArrayObject(array(
						'10' => new gt3options(__('Default', 'gt3pg_pro'), 'default'),
						'20' => new gt3options(__('Disabled', 'gt3pg_pro'), '0'),
						'30' => new gt3options(__('Enabled', 'gt3pg_pro'), '1')
					))
				))
			));
			$panels["90.2"] = new gt3panel_control(array(
				'title'  => __('Autoplay Time (sec.)', 'gt3pg_pro'),
				'name'   => 'slider_autoplay_time',
				'attr'   => new ArrayObject(array( new gt3attr('class', 'gt3pg_setting slider_hidden'), )),
				'option' => new gt3panel_input(array(
					'name' => 'slider_autoplay_time',
					'attr' => new ArrayObject(array(
						new gt3attr('class', 'short-input'),
						new gt3attr('maxlength', '3'),
						new gt3attr('data-setting', 'slider_autoplay_time'),
					))
				))
			));
			$panels["90.3"] = new gt3panel_control(array(
				'title'  => __('Thumbnails', 'gt3pg_pro'),
				'name'   => 'slider_preview',
				'attr'   => new ArrayObject(array( new gt3attr('class', 'gt3pg_setting slider_hidden'), )),
				'option' => new gt3panel_select(array(
					'name'    => 'slider_preview',
					'options' => new ArrayObject(array(
						'10' => new gt3options(__('Default', 'gt3pg_pro'), 'default'),
						'20' => new gt3options(__('Disabled', 'gt3pg_pro'), '0'),
						'30' => new gt3options(__('Enabled', 'gt3pg_pro'), '1')
					))
				))
			));
			$panels["90.5"] = new gt3panel_control(array(
				'title'  => __('Image Scaling', 'gt3pg_pro'),
				'name'   => 'gt3pg_slider_cover',
				'attr'   => new ArrayObject(array( new gt3attr('class', 'gt3pg_setting slider_hidden'), )),
				'option' => new gt3panel_select(array(
					'name'    => 'gt3pg_slider_cover',
					'options' => new ArrayObject(array(
						'10' => new gt3options(__('Default', 'gt3pg_pro'), 'default'),
						'20' => new gt3options(__('Disabled', 'gt3pg_pro'), '0'),
						'30' => new gt3options(__('Enabled', 'gt3pg_pro'), '1')
					))
				))
			));
			$option         = apply_filters('gt3pg_optimized_option', new gt3options(array(
				'title' => __('Optimized', 'gt3pg_pro'),
				'value' => 'gt3pg_optimized',
				'attr'  => new ArrayObject(array( new gt3attr('disabled', 'disabled') )),
			)));
			$panels["90.4"] = new gt3panel_control(array(
				'title'  => __('Select Image Size', 'gt3pg_pro'),
				'name'   => 'gt3pg_slider_image_size',
				'attr'   => new ArrayObject(array(
					new gt3attr('class', 'gt3pg_setting slider_hidden'),
				)),
				'option' => new gt3panel_select(array(
					'name'    => 'gt3pg_slider_image_size',
					'options' => new ArrayObject(array(
						'10' => new gt3options(__('Default', 'gt3pg_pro'), 'default'),
						'20' => new gt3options(__('Large', 'gt3pg_pro'), 'large'),
						'30' => new gt3options(__('Full', 'gt3pg_pro'), 'full'),
						'40' => $option
					))
				))
			));

			$panels["16"] = new gt3panel_control(array(
				'title'  => __("Social Links", 'gt3pg_pro'),
				'name'   => 'gt3pg_social',
				'attr'   => new ArrayObject(array( new gt3attr('class', 'gt3pg_setting down_soc_hidden'), )),
				'option' => new gt3panel_select(array(
					'name'    => 'gt3pg_social',
					'options' => new ArrayObject(array(
						'10' => new gt3options(__('Default', 'gt3pg_pro'), 'default'),
						'20' => new gt3options(__('Disabled', 'gt3pg_pro'), '0'),
						'30' => new gt3options(__('Enabled', 'gt3pg_pro'), '1')
					))
				))
			));

			return $panels;
		}


	}

	add_action('plugins_loaded', function(){
		if(defined('GT3PG_PLUGINPATH')) {
//			gt3_photo_video_galery_pro::instance();
		} else {
			add_action('admin_notices', function(){
				$plugins   = get_plugins();
				$installed = false;
				if(is_array($plugins) && count($plugins)) {
					foreach($plugins as $key => $plugin_info) {
						if(substr($key, 0, strpos($key, '/')) == 'gt3-photo-video-gallery') {
							$installed = $key;
							break;
						}
					}
				}
				if($installed) {
					// Activate

					$msg    = esc_html__('GT3 Photo & Video Gallery Pro version is not working because GT3 Photo & Video Gallery Lite version has not been activated yet.', 'gt3pg_pro');
					$action = 'activate-plugin';
					$slug   = $installed;
					$url    = wp_nonce_url(add_query_arg(array( 'action' => 'activate', 'plugin' => $slug ), admin_url('plugins.php')), 'activate-plugin_'.$slug);
					$button = '<a class="button button-primary button-large" href="'.esc_url($url).'">'.esc_html__('Activate Plugin Now', 'gt3pg_pro').'</a>';
				} else {
					// Install
					$action = 'install-plugin';
					$slug   = 'gt3-photo-video-gallery';
					$msg    = esc_html__('GT3 Photo & Video Gallery Pro version is not working because it requires the GT3 Gallery Lite version installation.', 'gt3pg_pro');;
					$url    = wp_nonce_url(add_query_arg(array( 'action' => $action, 'plugin' => $slug ), admin_url('update.php')), $action.'_'.$slug);
					$button = '<a class="button button-primary button-large" href="'.esc_url($url).'">'.esc_html__('Install Plugin Now', 'gt3pg_pro').'</a>';
				}

				$class = 'notice notice-error gt3pg_error_notice is-dismissible';
				echo '<div class="'.esc_attr($class).'"><p>'.$msg.'</p><p>'.$button.'</p></div>';
			});
		}
	}, 50);
	register_activation_hook(GT3PG_PRO_FILE, array( 'gt3_photo_video_galery_pro', 'activated' ));
	register_deactivation_hook(GT3PG_PRO_FILE, array( 'gt3_photo_video_galery_pro', 'deactivated' ));
}

