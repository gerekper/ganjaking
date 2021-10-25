<?php

namespace GT3\PhotoVideoGallery;

use DateTime;
use Elementor\Modules\Usage\Module as Usage_Module;
use Elementor\Plugin as Elementor_Plugin;
use GT3\PhotoVideoGallery\Settings;
use GT3\PhotoVideoGallery\Usage\Shortcode;

defined('ABSPATH') OR exit;


class Usage {
	const first_usage = '_gt3pg_first_usage';
	private static $instance = null;
	CONST cron_name = 'gt3pg_lite_usage_schedule';

	private static $url                     = 'https://gt3wpgallery.com/stats/';
	private static $first_time_options_name = 'gt3pg_lite_usage_notice';


	public static function instance(){
		if(!self::$instance instanceof self) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct(){
		add_action('init', array( $this, 'init' ));
		return;

		if(!get_option(self::$first_time_options_name)) {
			add_action('admin_notices', array( $this, 'admin_notice' ));
			add_action('wp_ajax_gt3_lite_usage_control', array($this,'enable_usage'));
		}
	}

	public function admin_notice(){
		$nonce = wp_create_nonce('gt3pg_usage_notice');
		?>
		<div class="notice notice-warning gt3pg_usage_notice">
			<h2>Will you help us to make GT3 Gallery even better?</h2>
			<p>We're working to understand how people, just like you, use our plugin every day. We'd like to better understand the gallery types used most to improve them.</p>
			<p>The data sent will be always completely anonymous and we will never share it to 3rd parties. The following data will be collected (site URL and used gallery
			   types).</p>
			<p>You can easily turn it off at any time within the plugin options.</p>
			<p>
				<a href="javascript:void(0)" class="button-primary button gt3_usage_button_enable">Yes, I want to help by sharing this information!</a>
				<a href="javascript:void(0)" class="button-secondary button gt3_usage_button_disable">No, I don't want to help!</a>
			</p>
		</div>
		<script>
			document.addEventListener("DOMContentLoaded", function () {
				var notice = document.querySelector('.gt3pg_usage_notice');
				if (notice) {
					var enable_button = notice.querySelector('.gt3_usage_button_enable');
					var disable_button = notice.querySelector('.gt3_usage_button_disable');
					enable_button.addEventListener('click', function (e) {
						jQuery.ajax({
							url: ajaxurl,
							method: "POST",
							data: {
								action: "gt3_lite_usage_control",
								gt3_action: 'enable',
								nonce: "<?php echo $nonce ?>"
							}
						});
						jQuery(notice).fadeOut();
					});
					disable_button.addEventListener('click', function (e) {
						jQuery.ajax({
							url: ajaxurl,
							method: "POST",
							data: {
								action: "gt3_lite_usage_control",
								gt3_action: 'disable',
								nonce: "<?php echo $nonce ?>"
							}
						});
						jQuery(notice).fadeOut();
					})
				}
			});
		</script>
		<?php
	}

	public function enable_usage() {
		header('Content-Type: application/json');
		$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
		if (!wp_verify_nonce($nonce, 'gt3pg_usage_notice')) {
			echo json_encode(array(
				'error' => true,
				'msg' => 'Nonce error',
			));
			die;
		}

		$action = isset($_POST['gt3_action']) ? $_POST['gt3_action'] : false;
		if (!$action) return;

		$settings_instance = Settings::instance();
		$settings = $settings_instance->getSettings();
		switch($action){
			case 'enable':
				$settings['basic']['usage'] = '1';
				$settings_instance->setSettings($settings);
				break;
			case 'disable':
				break;
		}
		update_option(self::$first_time_options_name, true);
	}

	public function init(){
		$settings = Settings::instance()->getSettings('basic');
		if((bool) $settings['usage']) {
			Usage\Shortcode::instance();
			Usage\Blocks::instance();

			add_filter('cron_schedules', array( $this, 'cron_schedules' ));
			add_action(self::cron_name, array( $this, 'cron_action' ));
			add_action('gt3pg_lite_single_event_usage', array( $this, 'gt3pg_single_event_usage' ));
			$this->add_schedule();

			if(!get_option(self::first_usage)) {
				update_option(self::first_usage, true, true);
				wp_schedule_single_event(current_time('timestamp'), 'gt3pg_lite_single_event_usage');
			}
		}
	}

	public function gt3pg_single_event_usage(){
		if(class_exists('\Elementor')) {
			/** @var Usage_Module $module */
			$module = Elementor_Plugin::$instance->modules_manager->get_modules('usage');
			$module->recalc_usage();
		}
		$query = new \WP_Query(
			array(
				'post_type'      => 'any',
				'posts_per_page' => '-1',
				'fields'         => 'ids',
				'meta_query'     => array_merge(
					array(
						'relation' => 'AND',
					),
					array(
						array(
							'key'     => '_elementor_controls_usage',
							'compare' => 'NOT EXISTS',
						),
					)
				),
			)
		);

		if($query->post_count) {
			$gutenberg_usage = Usage\Blocks::instance();
			$shortcode_usage = Usage\Shortcode::instance();
			foreach($query->posts as $_post) {
				$_post = get_post($_post);
				if($_post instanceof \WP_Post) {
					// is Gutenberg?
					$gutenberg_usage->enable_usage();
					$gutenberg_usage->save_post($_post->ID, $_post);
					$gutenberg_usage->disable_usage();;

					$shortcode_usage->get_post_shortcodes($_post);

				}
			}
		}

		wp_schedule_single_event(current_time('timestamp'), self::cron_name);
	}

	private function add_schedule(){
		if(!wp_next_scheduled(self::cron_name)) {
			$dateTime = new DateTime();
			$dateTime->setTimestamp(current_time('timestamp'));
			$dateTime->modify('next Saturday');
			wp_schedule_event($dateTime->getTimestamp(), 'weekly', self::cron_name);
			do_action(self::cron_name);
		}
	}


	public function cron_schedules($schedules){
		if(!key_exists('weekly', $schedules)) {
			$schedules['weekly'] = array(
				'interval' => WEEK_IN_SECONDS,
				'display'  => __('Once Week'),
			);
		}

		return $schedules;
	}

	public function cron_action(){
		@set_time_limit(0);
		$encoded_data = array(
			'source'     => 1,
			'home_url'   => home_url(),
			'site_url'   => site_url(),
			'shortcodes' => Usage\Shortcode::get_usage(),
			'blocks'     => Usage\Blocks::get_usage(),
			'widgets'    => Usage\Elementor_Widgets::get_usage(),
		);

		$response = wp_remote_request(
			self::$url, array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(
					'crc332' => md5(json_encode($encoded_data)),
					'gt3'    => 'PUT',
				),
				'body'        => $encoded_data,
				'cookies'     => array()
			)
		);
	}
}
