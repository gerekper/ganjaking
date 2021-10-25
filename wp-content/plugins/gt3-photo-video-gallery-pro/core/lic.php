<?php
defined('ABSPATH') OR exit;

require_once __DIR__.'/license/fix_windows_edd.php';
require_once __DIR__.'/license/GT3_EDD_SL_Plugin_Updater.php';
require_once __DIR__.'/license/gt3pg_updater.php';

if(!class_exists('gt3pg_pro_plugin_updater') && class_exists('gt3pg_updater_pro')) {
	class gt3pg_pro_plugin_updater extends gt3pg_updater_pro {
		protected $item_id     = 21755;
		protected $slug        = 'gt3-photo-video-gallery-pro';
		protected $plugin_name = 'GT3 Photo & Video Gallery - Pro';

		private static $instance = null;

		public static function instance($file = null){
			if(!isset(self::$instance) || !(self::$instance instanceof self)) {
				self::$instance = new self($file);
			}

			return self::$instance;
		}

		protected function __construct($file = null){
			parent::__construct($file);
			$plugin = gt3_photo_video_galery_pro::instance();

			add_action(
				'after_setup_theme', function() use ($plugin){
				$sts  = strrev('dilav');
				$cbck = strrev(base64_decode('c25vaXRjYQ=='));
				$fn   = base64_decode('Yzg0ZjljZWE1OGI5OTQzMGU4ZDRjNTY1NWFiNjI5OGY=');
				$tc   = wp_get_theme();
				$ic   = $tc->get('Template');
				!empty($ic) AND $tc = wp_get_theme($ic);
				$strtolower_function = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';

				if(!empty($this->license)) {
					if(method_exists($this, 'init_variables')) {
						$this->init_variables();
					}
				}

				if('valid' === $this->status
				   || (function_exists($fn) &&
				       'gt3themes' === call_user_func($strtolower_function, $tc->get('Author'))
				       && method_exists($plugin, $cbck))
				) {
					\GT3\PhotoVideoGalleryPro\Autoload::instance()->Init();
					$plugin->{$cbck}();
				}
			}
			);

			add_action(
				'admin_enqueue_scripts', function(){
				wp_enqueue_style(
					'gt3pg-pro-admin-license',
					GT3PG_PRO_CSSURL.'admin.css',
					array(),
					filemtime(GT3PG_PRO_CSSPATH.'admin.css')
				);
			}
			);
		}
	}

	function gt3pg_pro_plugin_updater($file = null){
		return gt3pg_pro_plugin_updater::instance($file);
	}

	gt3pg_pro_plugin_updater::instance(GT3PG_PRO_FILE);
}


