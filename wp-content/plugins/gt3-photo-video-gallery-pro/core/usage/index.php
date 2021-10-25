<?php

namespace GT3\PhotoVideoGalleryPro;

use DateTime;
use Elementor\Modules\Usage\Module as Usage_Module;
use Elementor\Plugin as Elementor_Plugin;
use GT3\PhotoVideoGalleryPro\Settings;
use GT3\PhotoVideoGalleryPro\Usage\Shortcode;

defined('ABSPATH') OR exit;


class Usage {
	const first_usage = '_gt3pg_first_usage';
	private static $instance = null;
	CONST cron_name = 'gt3pg_pro_usage_schedule';

	private static $url = 'https://gt3wpgallery.com/stats/';

	public static function instance(){
		if(!self::$instance instanceof self) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct(){
		$settings = Settings::instance()->getSettings('basic');
		if((bool) $settings['usage']) {
			Usage\Shortcode::instance();
			Usage\Blocks::instance();

			add_filter('cron_schedules', array( $this, 'cron_schedules' ));
			add_action(self::cron_name, array( $this, 'cron_action' ));
			add_action('gt3pg_single_event_usage', array( $this, 'gt3pg_single_event_usage' ));
			$this->add_schedule();

			if(!get_option(self::first_usage)) {
				update_option(self::first_usage, true, true);
				wp_schedule_single_event(current_time('timestamp'), 'gt3pg_single_event_usage');
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
			'source'     => 2,
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
