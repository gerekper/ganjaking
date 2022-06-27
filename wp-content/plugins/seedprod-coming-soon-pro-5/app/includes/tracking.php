<?php
/**
 * Tracking functions for reporting plugin usage to the SeedProd site for users that have opted in
 *
 * @access public
 * @package     SeedProd
 * @subpackage  Admin
 * @copyright   Copyright (c) 2018, Chris Christoff
 * @since       6.4.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SeedProd_Tracking' ) ) {
	/**
	 * Usage tracking
	 *
	 */
	class SeedProd_Tracking {

		/**
		 * Class init function.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'schedule_send' ) );
			add_filter( 'cron_schedules', array( $this, 'add_schedules' ) );
			add_action( 'seedprod_usage_tracking_cron', array( $this, 'send_checkin' ) );
		}

		/**
		 * Fetch tracking data.
		 *
		 * @return array $data Tracked data.
		 */
		private function get_data() {
			$data = array();

			// Retrieve current theme info
			$theme_data = wp_get_theme();

			$count_b = 1;
			if ( is_multisite() ) {
				if ( function_exists( 'get_blog_count' ) ) {
					$count_b = get_blog_count();
				} else {
					$count_b = '0';
				}
			}

			$data['php_version']    = phpversion();
			$data['plugin']         = 'sp';
			$data['sp_version']     = SEEDPROD_PRO_VERSION;
			$data['wp_version']     = get_bloginfo( 'version' );
			$data['servertype']     = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';
			$data['over_time']      = get_option( 'seedprod_over_time', array() );
			$data['multisite']      = is_multisite();
			$data['url']            = home_url();
			$data['themename']      = $theme_data->Name; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$data['themeversion']   = $theme_data->Version; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$data['email']          = get_bloginfo( 'admin_email' );
			$data['key']            = get_option( 'seedprod_api_key' );
			$data['settings']       = get_option( 'seedprod_settings' );
			$data['pro']            = defined( 'SEEDPROD_PRO_BUILD' );
			$data['sites']          = $count_b;
			$data['usagetracking']  = get_option( 'seedprod_usage_tracking_config', false );
			$data['usercount']      = function_exists( 'count_users' ) ? count_users()['total_users'] : '0';
			$data['timezoneoffset'] = gmdate( 'P' );
			$data['installed_lite'] = defined( 'SEEDPROD_BUILD' );
			$data['installed_pro']  = defined( 'SEEDPROD_PRO_BUILD' );
			$data['wc_active']      = $this->check_if_wc_active();
			$data['usages']         = array(
				'blocks'            => $this->block_count_summation(),
				'sp_landing_pages'  => $this->get_sp_landing_pages_created(),
				'sp_template_pages' => $this->get_sp_template_pages_created(),
				'wp_pages'          => $this->get_wp_pages(),
				'wp_posts'          => $this->get_wp_posts(),
			);

			// Retrieve current plugin information
			if ( ! function_exists( 'get_plugins' ) ) {
				include ABSPATH . '/wp-admin/includes/plugin.php';
			}

			$plugins        = array_keys( get_plugins() );
			$active_plugins = get_option( 'active_plugins', array() );

			foreach ( $plugins as $key => $plugin ) {
				if ( in_array( $plugin, $active_plugins ) ) {
					// Remove active plugins from list so we can show active and inactive separately
					unset( $plugins[ $key ] );
				}
			}

			$data['active_plugins']   = $active_plugins;
			$data['inactive_plugins'] = $plugins;
			$data['locale']           = get_locale();

			$data['theme_enabled'] = get_option( 'seedprod_theme_enabled' );
			$data['csp_page_created'] = get_option( 'seedprod_coming_soon_page_id' );
			$data['mm_page_created'] = get_option( 'seedprod_maintenance_mode_page_id' );
			$data['login_page_created'] = get_option( 'seedprod_login_page_id' );
			$data['p404_page_created'] = get_option( 'seedprod_404_page_id' );
			$data['sp_theme'] = get_option( 'seedprod_theme_id' );

			return $data;
		}

		/**
		 * Send tracking data.
		 *
		 * @param boolean $override            Override tracking_allowed.
		 * @param boolean $ignore_last_checkin Ignore last checkin flag.
		 * @return boolean
		 */
		public function send_checkin( $override = false, $ignore_last_checkin = false ) {
			if ( ! $this->tracking_allowed() && ! $override ) {
				return false;
			}

			// Send a maximum of once per week
			$last_send = get_option( 'seedprod_usage_tracking_last_checkin' );
			if ( is_numeric( $last_send ) && $last_send > strtotime( '-1 week' ) && ! $ignore_last_checkin ) {
				return false;
			}

			$seedprod_version = defined('SEEDPROD_VERSION') ? SEEDPROD_VERSION : SEEDPROD_PRO_VERSION;

			$request = wp_remote_post(
				'https://usage.seedprod.com/capture',
				array(
					'method'      => 'POST',
					'timeout'     => 5,
					'redirection' => 5,
					'httpversion' => '1.1',
					'blocking'    => false,
					'body'        => $this->get_data(),
					'user-agent'  => 'SP/' . $seedprod_version . '; ' . get_bloginfo( 'url' ),
				)
			);

			if ( !is_wp_error($request) ) {
				// If we have completed successfully, recheck in 1 week
				update_option( 'seedprod_usage_tracking_last_checkin', time() );
			};

			return true;
		}

		/**
		 * Check if tracking is allowed.
		 *
		 * @return boolean
		 */
		private function tracking_allowed() {
			return (bool) get_option( 'seedprod_allow_usage_tracking' ) || defined( 'SEEDPROD_PRO_BUILD' );
		}

		/**
		 * Schedule send tracking data event.
		 *
		 * @return void
		 */
		public function schedule_send() {
			if ( ! wp_next_scheduled( 'seedprod_usage_tracking_cron' ) ) {
				$tracking             = array();
				$tracking['day']      = wp_rand( 0, 6 );
				$tracking['hour']     = wp_rand( 0, 23 );
				$tracking['minute']   = wp_rand( 0, 59 );
				$tracking['second']   = wp_rand( 0, 59 );
				$tracking['offset']   = ( $tracking['day'] * DAY_IN_SECONDS ) +
									( $tracking['hour'] * HOUR_IN_SECONDS ) +
									( $tracking['minute'] * MINUTE_IN_SECONDS ) +
									$tracking['second'];
				$tracking['initsend'] = strtotime( 'next sunday' ) + $tracking['offset'];

				wp_schedule_event( $tracking['initsend'], 'weekly', 'seedprod_usage_tracking_cron' );
				update_option( 'seedprod_usage_tracking_config', $tracking );
			}
		}

		/**
		 * Add schedules.
		 *
		 * @param array $schedules Available/current schedules.
		 * @return array $schedules Schedules array.
		 */
		public function add_schedules( $schedules = array() ) {
			// Adds once weekly to the existing schedules.
			$schedules['weekly'] = array(
				'interval' => 604800,
				'display'  => __( 'Once Weekly', 'seedprod-pro' ),
			);
			return $schedules;
		}

		/**
		 * Get WP Posts count.
		 *
		 * @return array $wp_post_count WP Posts count.
		 */
		public function get_wp_posts() {
			global $wpdb;

			$wp_post_count = 0;

			// $results = $wpdb->get_results(
			// 	"SELECT `post_type`, `post_status`, COUNT(`ID`) `hits`
			// 	FROM {$wpdb->posts}
			// 	WHERE `post_type` = 'post'
			// 	GROUP BY `post_type`, `post_status`;"
			// );

			// if ( $results ) {
			// 	foreach ( $results as $result ) {
			// 		$wp_post_count += $result->hits;
			// 	}
			// }

			$results = $wpdb->get_var(
				"SELECT COUNT(`ID`) `hits`
				FROM {$wpdb->posts}
				WHERE `post_type` = 'post' AND `post_status` = 'publish';"
			);
			if(!empty($results)){
				$wp_post_count = $results;
			}

			return $wp_post_count;
		}

		/**
		 * Get WP Pages count.
		 *
		 * @return array $wp_pages_count WP Pages count.
		 */
		public function get_wp_pages() {
			global $wpdb;

			$wp_pages_count = 0;

			// $results = $wpdb->get_results(
			// 	"SELECT `post_type`, `post_status`, COUNT(`ID`) `hits`
			// 	FROM {$wpdb->posts}
			// 	WHERE `post_type` = 'page'
			// 	GROUP BY `post_type`, `post_status`;"
			// );

			// if ( $results ) {
			// 	foreach ( $results as $result ) {
			// 		$wp_pages_count += $result->hits;
			// 	}
			// }

			$results = $wpdb->get_var(
				"SELECT COUNT(`ID`) `hits`
				FROM {$wpdb->posts}
				WHERE `post_type` = 'page' AND `post_status` = 'publish';"
			);
			if(!empty($results)){
				$wp_pages_count = $results;
			}

			return $wp_pages_count;
		}

		/**
		 * Get SP Template Pages created.
		 *
		 * @return array $template_pages_created Total template pages created.
		 */
		public function get_sp_template_pages_created() {
			global $wpdb;

			$template_pages_created = 0;

			// $results = $wpdb->get_results(
			// 	"SELECT `post_type`, `post_status`, COUNT(`ID`) `hits`
			// 	FROM {$wpdb->posts} `p`
			// 	LEFT JOIN {$wpdb->postmeta} `pm` ON( `p`.`ID` = `pm`.`post_id` )
			// 	WHERE `p`.`post_type` IN ('seedprod','page')
			// 		AND `pm`.meta_key = '_seedprod_is_theme_template'
			// 	GROUP BY `post_type`, `post_status`;"
			// );

			// if ( $results ) {
			// 	foreach ( $results as $result ) {
			// 		$template_pages_created += $result->hits;
			// 	}
			// }


			$results = $wpdb->get_var(
				"SELECT COUNT(`ID`) `hits`
				FROM {$wpdb->posts} `p`
				LEFT JOIN {$wpdb->postmeta} `pm` ON( `p`.`ID` = `pm`.`post_id` )
				WHERE `p`.`post_type` = 'seedprod'
					AND `pm`.meta_key = '_seedprod_is_theme_template' AND `post_status` = 'publish';"
			);
			if(!empty($results)){
				$template_pages_created = $results;
			}

			return $template_pages_created;
		}

		/**
		 * Get SP Landing Pages created count.
		 *
		 * @return array $landing_page_usage Landing pages created.
		 */
		public function get_sp_landing_pages_created() {
			global $wpdb;

			$landing_pages_created = 0;

			// $results = $wpdb->get_results(
			// 	"SELECT `post_status`, COUNT(`ID`) `hits`
			// 	FROM {$wpdb->posts} `p`
			// 	LEFT JOIN {$wpdb->postmeta} `pm` ON(`p`.`ID` = `pm`.`post_id`)
			// 	WHERE `p`.`post_type` IN ('seedprod','page')
			// 		AND `pm`.`meta_key` = '_seedprod_page' 
			// 	GROUP BY `post_status`;"
			// );

			// if ( $results ) {
			// 	foreach ( $results as $result ) {
			// 		$landing_pages_created += $result->hits;
			// 	}
			// }


			$results = $wpdb->get_var(
				"SELECT COUNT(`ID`) `hits`
				FROM {$wpdb->posts} `p`
				LEFT JOIN {$wpdb->postmeta} `pm` ON(`p`.`ID` = `pm`.`post_id`)
				WHERE `p`.`post_type` = 'page'
					AND `pm`.`meta_key` = '_seedprod_page' AND `post_status` = 'publish';"
			);
			if(!empty($results)){
				$landing_pages_created = $results;
			}

			return $landing_pages_created;
		}

		/**
		 * Sum all block usage data.
		 *
		 * @return array $blocks_usage_sum Array of all block sum usage.
		 */
		public function block_count_summation() {
			// Get all _seedprod_block_usage data.
			global $wpdb;

			$tablename = $wpdb->prefix . 'postmeta';
			$sql       = "SELECT meta_value FROM $tablename";
			$sql      .= ' WHERE meta_key = %s';
			$safe_sql  = $wpdb->prepare( $sql, '_seedprod_block_usage' ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$results   = $wpdb->get_results( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			$blocks_usage_sum = array();

			// Sum all block usage data.
			if ( $results ) {
				foreach ( $results as $result ) {
					if ( $result->meta_value ) {
						$page_usage_data = maybe_unserialize( $result->meta_value );
						if ( is_array( $page_usage_data ) ) {
							foreach ( $page_usage_data as $type => $value ) {
								if ( array_key_exists( $type, $blocks_usage_sum ) ) {
									// If set.
									$blocks_usage_sum[ $type ] = array(
										'name'  => $blocks_usage_sum[ $type ]['name'],
										'count' => $blocks_usage_sum[ $type ]['count'] + $value['count'], // Sum count.
									);
								}

								if ( ! array_key_exists( $type, $blocks_usage_sum ) ) {
									// If block type is not set.
									$blocks_usage_sum[ $type ] = $value;
								}
							}
						}
					}
				}
			}

			return $blocks_usage_sum;
		}

		/**
		 * Check if WooCommerce is active or not.
		 *
		 * @return boolean true|false Return if WC active.
		 */
		public function check_if_wc_active() {
			// Check if WooCommerce is active
			return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
		}

		/**
		 * Fetch block based on type.
		 *
		 * @param string $type Block type.
		 * @return null/array $block Block array.
		 */
		public function get_blocks_using_type( $type ) {
			$blocks = seedprod_pro_block_options();

			if ( $type ) {
				foreach ( $blocks as $block ) {
					if ( $type === $block['type'] ) {
						return $block;
					}
				}
			}

			return null;
		}

		/**
		 * Get block data.
		 */
		public function get_block_data( $sections = array() ) {
			$blocks = array();

			if ( is_array( $sections ) && isset( $sections ) ) {
				// Fetch block data.
				foreach ( $sections as $section ) {
					if ( $section->rows ) {
						foreach ( $section->rows as $row ) {
							if ( $row->cols ) {
								foreach ( $row->cols as $col ) {
									if ( $col->blocks ) {
										foreach ( $col->blocks as $block ) {
											array_push( $blocks, $block->type );
										}
									}
								}
							}
						}
					}
				}

				$blocks = array_count_values( $blocks );

				// Process block data.
				foreach ( $blocks as $type => $count ) {
					$block = $this->get_blocks_using_type( $type );

					if ( $block ) {
						$blocks[ $type ] = array(
							'name'  => $block['name'],
							'count' => $count,
						);
					}
				}
			}

			return $blocks;
		}
	}
}

new SeedProd_Tracking();
