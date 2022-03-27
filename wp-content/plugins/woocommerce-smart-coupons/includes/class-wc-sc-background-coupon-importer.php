<?php
/**
 * Class to handle import of coupons in background
 *
 * @author      StoreApps
 * @since       3.8.6
 * @version     1.7.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Background_Coupon_Importer' ) ) {

	/**
	 * WC_SC_Background_Coupon_Importer Class.
	 */
	class WC_SC_Background_Coupon_Importer {

		/**
		 * Start time of current process.
		 *
		 * (default value: 0)
		 *
		 * @var int
		 * @access protected
		 */
		protected $start_time = 0;

		/**
		 * Identifier
		 *
		 * @var mixed
		 * @access protected
		 */
		protected $identifier;

		/**
		 * Background process status
		 *
		 * (default value: '')
		 *
		 * @var string
		 * @access protected
		 */
		protected $is_process_running = '';

		/**
		 * Array for storing newly created global coupons
		 *
		 * @var $global_coupons_new
		 */
		public $global_coupons_new = array();

		/**
		 * Plugin data
		 *
		 * @var $plugin_data
		 */
		public $plugin_data = array();

		/**
		 * Variable to hold instance of WC_SC_Background_Coupon_Importer
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Initiate new background process.
		 */
		private function __construct() {

			$this->plugin_data = WC_Smart_Coupons::get_smart_coupons_plugin_data();

			// Uses unique prefix per blog so each blog has separate queue.
			$this->prefix     = 'wp_' . get_current_blog_id();
			$this->identifier = 'wc_sc_coupon_importer';

			add_action( 'admin_notices', array( $this, 'coupon_background_notice' ) );
			add_action( 'admin_footer', array( $this, 'styles_and_scripts' ) );
			add_action( 'wp_ajax_wc_sc_coupon_background_progress', array( $this, 'ajax_coupon_background_progress' ) );
			add_action( 'wp_ajax_wc_sc_stop_coupon_background_process', array( $this, 'ajax_stop_coupon_background_process' ) );
			add_action( 'wp_ajax_wc_sc_download_csv', array( $this, 'ajax_download_csv' ) );
			add_action( 'woo_sc_generate_coupon_csv', array( $this, 'woo_sc_generate_coupon_csv' ) );
			add_action( 'woo_sc_import_coupons_from_csv', array( $this, 'woo_sc_import_coupons_from_csv' ) );
			add_action( 'woocommerce_smart_coupons_send_combined_coupon_email', array( $this, 'send_scheduled_combined_email' ) );
			add_action( 'action_scheduler_failed_action', array( $this, 'restart_failed_action' ) );

			add_filter( 'heartbeat_send', array( $this, 'check_coupon_background_progress' ), 10, 2 );
			add_filter( 'cron_schedules', array( $this, 'modify_action_scheduler_default_interval' ), 1000 ); // phpcs:ignore 

		}

		/**
		 * Get single instance of WC_SC_Background_Coupon_Importer
		 *
		 * @return WC_SC_Background_Coupon_Importer Singleton object of WC_SC_Background_Coupon_Importer
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Get Identifier
		 *
		 * @return string The Identifier
		 */
		public function get_identifier() {
			return $this->identifier;
		}

		/**
		 * Memory exceeded
		 *
		 * Ensures the batch process never exceeds 90%
		 * of the maximum WordPress memory.
		 *
		 * @return bool
		 */
		protected function memory_exceeded() {
			$memory_limit   = $this->get_memory_limit() * 0.9; // 90% of max memory
			$current_memory = memory_get_usage( true );

			if ( $current_memory >= $memory_limit ) {
				return true;
			}

			return false;
		}

		/**
		 * Get memory limit.
		 *
		 * @return int
		 */
		protected function get_memory_limit() {
			if ( function_exists( 'ini_get' ) ) {
				$memory_limit = ini_get( 'memory_limit' );
			} else {
				// Sensible default.
				$memory_limit = '128M';
			}

			if ( ! $memory_limit || -1 === intval( $memory_limit ) ) {
				// Unlimited, set to 32GB.
				$memory_limit = '32G';
			}

			return wp_convert_hr_to_bytes( $memory_limit );
		}

		/**
		 * Time exceeded.
		 *
		 * Ensures the batch never exceeds a sensible time limit.
		 * A timeout limit of 30s is common on shared hosting.
		 *
		 * @param string $start_time start timestamp.
		 * @return bool
		 */
		protected function time_exceeded( $start_time = '' ) {

			if ( ! empty( $start_time ) ) {
				$this->start_time = $start_time;
			}

			$finish = $this->start_time + apply_filters( $this->identifier . '_default_time_limit', 20 ); // 20 seconds
			$return = false;

			if ( time() >= $finish ) {
				$return = true;
			}

			return apply_filters( $this->identifier . '_time_exceeded', $return );
		}

		/**
		 * Get list of scheduled acctions of this plugin
		 *
		 * Note: wc_sc_send_scheduled_coupon_email is not included because it's not used in bulk generate/import process
		 *
		 * @return array
		 */
		public function get_scheduled_action_hooks() {
			$hooks = array(
				'woo_sc_generate_coupon_csv',
				'woo_sc_import_coupons_from_csv',
				'woocommerce_smart_coupons_send_combined_coupon_email',
			);
			return $hooks;
		}

		/**
		 * Get scheduled actions by this plugin
		 *
		 * @return array
		 */
		public function get_scheduled_actions() {

			$found_actions = array();

			if ( ! function_exists( 'as_get_scheduled_actions' ) ) {
				return $found_actions;
			}

			$hooks = $this->get_scheduled_action_hooks();

			if ( ! empty( $hooks ) ) {
				foreach ( $hooks as $hook ) {
					$args  = array(
						'hook' => $hook,
					);
					$found = as_get_scheduled_actions( $args, ARRAY_A );
					if ( ! empty( $found ) ) {
						$found_actions[ $hook ] = $found;
					}
				}
			}

			return $found_actions;

		}

		/**
		 * Stop all scheduled actions by this plugin
		 */
		public function stop_scheduled_actions() {
			if ( function_exists( 'as_unschedule_action' ) ) {
				$hooks = $this->get_scheduled_action_hooks();
				if ( ! empty( $hooks ) ) {
					foreach ( $hooks as $hook ) {
						as_unschedule_action( $hook );
					}
				}
			}
			$this->clean_scheduled_action_data();
		}

		/**
		 * Clean scheduled action data
		 */
		public function clean_scheduled_action_data() {
			delete_option( 'woo_sc_generate_coupon_posted_data' );
			delete_option( 'start_time_woo_sc' );
			delete_option( 'current_time_woo_sc' );
			delete_option( 'all_tasks_count_woo_sc' );
			delete_option( 'remaining_tasks_count_woo_sc' );
			delete_option( 'bulk_coupon_action_woo_sc' );
		}

		/**
		 * Display notice if a background process is already running
		 */
		public function coupon_background_notice() {
			global $pagenow, $post, $store_credit_label;

			if ( ! is_admin() ) {
				return;
			}

			$page = ( ! empty( $_GET['page'] ) ) ? wc_clean( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore
			$tab  = ( ! empty( $_GET['tab'] ) ? ( 'send-smart-coupons' === $_GET['tab'] ? 'send-smart-coupons' : 'import-smart-coupons' ) : 'generate_bulk_coupons' ); // phpcs:ignore

			if ( ( ! empty( $post->post_type ) && 'shop_coupon' !== $post->post_type ) || ! in_array( $tab, array( 'generate_bulk_coupons', 'import-smart-coupons', 'send-smart-coupons' ), true ) ) {
				return;
			}

			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}
			if ( ! wp_script_is( 'heartbeat' ) ) {
				wp_enqueue_script( 'heartbeat' );
			}

			$upload_dir  = wp_get_upload_dir();
			$upload_path = $upload_dir['basedir'] . '/woocommerce_uploads';

			if ( 'wc-smart-coupons' === $page && 'generate_bulk_coupons' === $tab && ! empty( $upload_dir['error'] ) ) {
				if ( ! wp_script_is( 'jquery-tiptip', 'registered' ) ) {
					$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
					wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WC()->version, true );
				}

				if ( ! wp_script_is( 'jquery-tiptip' ) ) {
					wp_enqueue_script( 'jquery-tiptip' );
				}
				?>
				<div id="wc_sc_folder_permission_warning" class="error">
					<p>
						<span class="dashicons dashicons-warning"></span>&nbsp;
						<?php /* translators: 1. Important 2. Upload path */ ?>
						<?php echo sprintf( esc_html__( '%1$s: To allow bulk generation of coupons, please make sure %2$s directory is writable.', 'woocommerce-smart-coupons' ), '<strong>' . esc_html__( 'Important', 'woocommerce-smart-coupons' ) . '</strong>', '<strong><code>' . esc_html( $upload_path ) . '</code></strong>' ); ?>
					</p>
				</div>
				<script type="text/javascript">
					jQuery(function(){
						let sc_directory_permission_notice = decodeURIComponent( '<?php echo rawurlencode( __( 'Bulk generation is disabled since uploads directory is not writable. Please ensure uploads directory is writable before starting bulk generate process.', 'woocommerce-smart-coupons' ) ); ?>' );
						jQuery('#generate_and_import').addClass('disabled')
						.attr( 'data-tip', sc_directory_permission_notice )
						.tipTip({
							'attribute': 'data-tip',
							'fadeIn':    50,
							'fadeOut':   50,
							'delay':     100
						});
					});
				</script>
				<?php
			} else {

				if ( 'yes' === $this->is_process_running() ) {
					$bulk_action = get_option( 'bulk_coupon_action_woo_sc' );

					switch ( $bulk_action ) {

						case 'import_email':
						case 'import':
							$bulk_text    = __( 'imported', 'woocommerce-smart-coupons' );
							$bulk_process = __( 'import', 'woocommerce-smart-coupons' );
							break;

						case 'generate_email':
						case 'send_store_credit':
							$bulk_text    = __( 'generated & sent', 'woocommerce-smart-coupons' );
							$bulk_process = __( 'generate', 'woocommerce-smart-coupons' );
							break;
						case 'generate':
						default:
							$bulk_text    = __( 'generated', 'woocommerce-smart-coupons' );
							$bulk_process = __( 'generate', 'woocommerce-smart-coupons' );
							break;

					}
					$scheduled_actions = $this->get_scheduled_actions();
					?>
					<div id="wc_sc_coupon_background_progress" class="error" style="background-color: #fff0f0;">
						<?php
						if ( empty( $scheduled_actions ) ) {
							$this->clean_scheduled_action_data();
							?>
								<p>
								<?php
									/* translators: 1. Error title 2. The bulk process */
									echo sprintf( esc_html__( '%1$s: The coupon bulk %2$s process stopped. Please review the coupons list to check the status.', 'woocommerce-smart-coupons' ), '<strong>' . esc_html( $this->plugin_data['Name'] ) . ' ' . esc_html__( 'Error', 'woocommerce-smart-coupons' ) . '</strong>', esc_html( $bulk_process ) );
								?>
								</p>
								<?php
						} else {
							?>
								<p>
								<?php
								if ( 'send_store_credit' === $bulk_action ) {
									$coupon_text = ! empty( $store_credit_label['plural'] ) ? $store_credit_label['plural'] : esc_html__( 'Store Credits / Gift Cards', 'woocommerce-smart-coupons' );
								} else {
									$coupon_text = esc_html__( 'Coupons', 'woocommerce-smart-coupons' );
								}

								/* translators: 1. Coupon type */
								echo '<strong>' . esc_html__( 'Important', 'woocommerce-smart-coupons' ) . '</strong>: ' . sprintf( esc_html__( '%s are being', 'woocommerce-smart-coupons' ), esc_html( $coupon_text ) );
								echo '&nbsp;' . esc_html( $bulk_text ) . '&nbsp;';
								echo esc_html__( 'in the background. You will be notified when it is completed.', 'woocommerce-smart-coupons' ) . '&nbsp;';
								?>
									<span id="wc_sc_remaining_time_label" style="display: none;">
									<?php echo esc_html__( 'Progress', 'woocommerce-smart-coupons' ); ?>:&nbsp;
										<strong><span id="wc_sc_remaining_time"><?php echo esc_html__( '--:--:--', 'woocommerce-smart-coupons' ); ?></span></strong>&nbsp;&nbsp;
										<a id="wc-sc-stop-bulk-generate-import" href="javascript:void(0);"><?php echo esc_html__( 'Stop', 'woocommerce-smart-coupons' ); ?></a>
									</span>
								</p>
								<p>
									<?php echo esc_html__( 'You can continue with other work. But for bulk generating or importing new coupons, wait for the current process to complete.', 'woocommerce-smart-coupons' ); ?>
								</p>
								<?php
						}
						?>
					</div>
					<script type="text/javascript">
						jQuery(function(){
							let admin_ajax_url = <?php echo wp_json_encode( esc_url( admin_url( 'admin-ajax.php' ) ) ); ?>;
							let current_interval = false;
							function wc_sc_start_coupon_background_progress_timer( total_seconds, target_dom ) {
								var timer = total_seconds, hours, minutes, seconds;
								var target_element = target_dom.find('#wc_sc_remaining_time');
								var target_element_label = target_dom.find('#wc_sc_remaining_time_label');
								if ( false !== current_interval ) {
									clearInterval( current_interval );
								}
								current_interval = setInterval(function(){
									hours   = Math.floor(timer / 3600);
									timer   %= 3600;
									minutes = Math.floor(timer / 60);
									seconds = timer % 60;

									hours   = hours < 10 ? "0" + hours : hours;
									minutes = minutes < 10 ? "0" + minutes : minutes;
									seconds = seconds < 10 ? "0" + seconds : seconds;

									target_element_label.show();
									target_element.text(hours + ":" + minutes + ":" + seconds);

									if (--timer < 0) {
										timer = 0;
										clearInterval( current_interval );
										location.reload( true );
									}

								}, 1000);
							}
							function wc_sc_start_coupon_background_progress_percentage( progress_data, target_dom ) {
								let target_element_label = target_dom.find('#wc_sc_remaining_time_label');
								let target_element = target_dom.find('#wc_sc_remaining_time');
								let percent_completion = progress_data.percent_completion;
								let coupon_action = progress_data.coupon_action;
								let action_stage = progress_data.action_stage;
								let action_data = progress_data.action_data;

								let percent_ratio_data = {
									'add_to_store': [20, 80],
									'sc_export_and_import': [100],
									'import_from_csv': [100],
								}

								let total_percent_ratio = 0;
								if( 'add_to_store' == coupon_action ) {
									let percent_ratio = percent_ratio_data[coupon_action];
									jQuery.each(percent_ratio,function( index, value ){
										if( index < action_stage ) {
											total_percent_ratio += value;
										} else if( index == action_stage ) {
											current_ratio = value;
										}
									});
									percent_completion = percent_completion * ( current_ratio / 100 );
									percent_completion += total_percent_ratio;
								}

								target_element_label.show();
								percent_completion = Math.round( percent_completion  * 100 ) / 100;
								target_element.text(percent_completion + '%');
								jQuery('.woo-sc-importer-progress').val(percent_completion);
							}
							function wc_sc_hide_coupon_form() {
								jQuery('.woo-sc-form-wrapper').hide();
								if( 0 === jQuery('.woo-sc-scheduler-running-message').length ) {
									let backgroudn_process_message = '<div class="woo-sc-scheduler-running-message"><div class="woocommerce-progress-form-wrapper"><div class="wc-progress-form-content woocommerfce-importer woocommerce-importer__importing">\
										<header>\
											<p><?php echo esc_html__( 'We are processing coupons in background. Please wait before starting new process.', 'woocommerce-smart-coupons' ); ?></p>\
										</header>\
									<section>\
										<progress class="woocommerce-importer-progress woo-sc-importer-progress" max="100" value="0"></progress>\
									</section></div></div></p>';
									jQuery(backgroudn_process_message).insertAfter('.woo-sc-form-wrapper');
								}
							}

							function wc_sc_check_coupon_background_progress() {
								jQuery.ajax({
									url: admin_ajax_url,
									method: 'post',
									dataType: 'json',
									data: {
										action: 'wc_sc_coupon_background_progress',
										security: '<?php echo esc_attr( wp_create_nonce( 'wc-sc-background-coupon-progress' ) ); ?>'
									},
									success: function( response ) {

										let percent_completion = response.percent_completion;
										let coupon_action = response.coupon_action;
										let action_stage = response.action_stage;
										let action_data = response.action_data;

										let progress_data = {
											percent_completion: response.percent_completion,
											coupon_action: response.coupon_action,
											action_stage: response.action_stage,
											action_data: response.action_data
										}

										let target_dom = jQuery('#wc_sc_coupon_background_progress');

										if ( response.percent_completion !== undefined && response.percent_completion !== '' ) {
											if( 100 == response.percent_completion) {
												let should_reload = false;
												if( ( 'add_to_store' === response.coupon_action || 'woo_sc_is_email_imported_coupons' === response.coupon_action && 'send_store_credit' === response.coupon_action ) && 1 === response.action_stage ) {
													should_reload = true;
												} else if ( 'import_from_csv' === response.coupon_action || 'sc_export_and_import' === response.coupon_action ){
													should_reload = true;
												}

												if( should_reload ) {
													window.location.reload();
												}
											}

											target_dom.show();
											wc_sc_hide_coupon_form();
											wc_sc_start_coupon_background_progress_percentage( progress_data, target_dom );
										}
									}
								});
							}

							wc_sc_check_coupon_background_progress();
							setInterval(function(){
								wc_sc_check_coupon_background_progress();
							},5000);

							jQuery('body').on('click', '#wc-sc-stop-bulk-generate-import', function(e){
								e.preventDefault();
								<?php /* translators: 1. The bulk process */ ?>
								let result = window.confirm('<?php echo sprintf( esc_html__( 'Are you sure you want to stop the coupon bulk %s process? Click OK to stop.', 'woocommerce-smart-coupons' ), esc_html( $bulk_process ) ); ?>');
								if (result) {
									jQuery.ajax({
										url     : admin_ajax_url,
										method  : 'post',
										dataType: 'json',
										data    : {
											action  : 'wc_sc_stop_coupon_background_process',
											security: '<?php echo esc_attr( wp_create_nonce( 'wc-sc-stop-coupon-background-process' ) ); ?>'
										},
										success: function( response ) {
											location.reload();
										}
									});
								}
							});
						});
					</script>
					<?php
				} else {
					$background_coupon_process_result = get_option( 'wc_sc_background_coupon_process_result' );
					$woo_sc_action_data               = get_option( 'woo_sc_action_data', false );
					if ( false !== $background_coupon_process_result ) {
						switch ( $background_coupon_process_result['action'] ) {
							case 'import_email':
								$action_title = __( 'Coupon import', 'woocommerce-smart-coupons' );
								$action_text  = __( 'added & emailed', 'woocommerce-smart-coupons' );
								break;
							case 'generate_email':
								$action_title = __( 'Coupon bulk generation', 'woocommerce-smart-coupons' );
								$action_text  = __( 'added & emailed', 'woocommerce-smart-coupons' );
								break;
							case 'import':
								$action_title = __( 'Coupon import', 'woocommerce-smart-coupons' );
								$action_text  = __( 'added', 'woocommerce-smart-coupons' );
								break;
							case 'send_store_credit':
								$action_title = __( 'Store credit', 'woocommerce-smart-coupons' );
								$action_text  = __( 'sent', 'woocommerce-smart-coupons' );
								break;
							case 'generate':
							default:
								$action_title = __( 'Coupon bulk generation', 'woocommerce-smart-coupons' );
								$action_text  = ( ! empty( $woo_sc_action_data['name'] ) && 'download_csv' === $woo_sc_action_data['name'] ) ? __( 'generated', 'woocommerce-smart-coupons' ) : __( 'added', 'woocommerce-smart-coupons' );
								break;
						}

						$coupon_text = array();
						if ( 'send_store_credit' === $background_coupon_process_result['action'] ) {
							$coupon_text['single'] = ! empty( $store_credit_label['single'] ) ? $store_credit_label['single'] : esc_html__( 'store credit / gift card', 'woocommerce-smart-coupons' );
							$coupon_text['plural'] = ! empty( $store_credit_label['plural'] ) ? $store_credit_label['plural'] : esc_html__( 'store credits / gift cards', 'woocommerce-smart-coupons' );
						} else {
							$coupon_text['single'] = esc_html__( 'coupon', 'woocommerce-smart-coupons' );
							$coupon_text['plural'] = esc_html__( 'coupons', 'woocommerce-smart-coupons' );
						}
						?>
						<div id="wc_sc_coupon_background_progress" class="updated" style="background-color: #f0fff0;">
							<p>
								<strong><?php echo esc_html( $action_title ); ?></strong>:&nbsp;
								<?php echo esc_html__( 'Successfully', 'woocommerce-smart-coupons' ) . ' ' . esc_html( $action_text ) . ' ' . esc_html( $background_coupon_process_result['successful'] ) . ' ' . esc_html( _n( $coupon_text['single'], $coupon_text['plural'], $background_coupon_process_result['successful'], 'woocommerce-smart-coupons' ) ) . '.'; // phpcs:ignore ?>

							</p>
							<?php
							if ( ! empty( $woo_sc_action_data ) ) {
								if ( ! empty( $woo_sc_action_data['name'] ) && 'download_csv' === $woo_sc_action_data['name'] ) {
									?>
									<p class="download-csv-wrapper">
										<?php
											echo esc_html__( 'CSV file has been generated. You can download it from ', 'woocommerce-smart-coupons' ) . '<a href="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '?action=wc_sc_download_csv&download_nonce=' . esc_attr( wp_create_nonce( 'wc_sc_download_csv' ) ) . '">' . esc_html__( 'here', 'woocommerce-smart-coupons' ) . '</a>.';
										?>
									</p>
									<?php
								}
							} else {
								delete_option( 'wc_sc_background_coupon_process_result' );
							}
							?>
						</div>
						<?php
					}
				}
			}
		}

		/**
		 * Styles & scripts
		 */
		public function styles_and_scripts() {
			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}
			?>
			<script type="text/javascript">
				jQuery(function(){
					jQuery('body').on('click', '.download-csv-wrapper a', function(){
						jQuery('.download-csv-wrapper').hide('slow');
					});
				});
			</script>
			<?php

		}

		/**
		 * Get coupon background progress via ajax
		 */
		public function ajax_coupon_background_progress() {

			check_ajax_referer( 'wc-sc-background-coupon-progress', 'security' );

			$response = array();

			$progress = $this->calculate_coupon_background_progress();

			if ( isset( $progress['percent_completion'] ) ) {
				$response['percent_completion'] = $progress['percent_completion'];
				if ( floatval( 100 ) === floatval( $progress['percent_completion'] ) ) {
					$this->stop_scheduled_actions();
				}
			}

			$coupon_posted_data = get_option( 'woo_sc_generate_coupon_posted_data', false );

			if ( $coupon_posted_data ) {

				$coupon_action             = $coupon_posted_data['smart_coupons_generate_action'];
				$response['coupon_action'] = $coupon_action;
				$action_stage              = $coupon_posted_data['action_stage'];
				$response['action_stage']  = $action_stage;
				$action_data               = get_option( 'woo_sc_action_data', false );
				$response['action_data']   = $action_data;

			}

			wp_send_json( $response );
		}

		/**
		 * Stop coupoon background process via AJAX
		 */
		public function ajax_stop_coupon_background_process() {

			check_ajax_referer( 'wc-sc-stop-coupon-background-process', 'security' );

			$this->stop_scheduled_actions();

			wp_send_json_success();
		}

		/**
		 * Get coupon background progress via ajax
		 */
		public function ajax_download_csv() {

			check_ajax_referer( 'wc_sc_download_csv', 'download_nonce' );

			$woo_sc_action_data = get_option( 'woo_sc_action_data', false );

			if ( $woo_sc_action_data ) {
				WP_Filesystem();

				global $wp_filesystem;

				$file_path       = $woo_sc_action_data['data']['generated_file_path'];
				$csv_file_path   = '';
				$file_name       = basename( $file_path );
				$dirname         = dirname( $file_path );
				$mime_type       = 'text/x-csv';
				$upload_dir      = wp_get_upload_dir();
				$upload_dir_path = $upload_dir['basedir'] . '/woocommerce_uploads';

				if ( class_exists( 'ZipArchive' ) ) {
					$zip       = new ZipArchive();
					$zip_name  = $file_name . '.zip';
					$zip_path  = $dirname . '/' . $zip_name;
					$mime_type = 'application/zip';
					if ( $zip->open( $zip_path, ZIPARCHIVE::CREATE ) ) {
						$zip->addFile( $file_path, $file_name );
						$zip->close();
						$file_name     = $zip_name;
						$csv_file_path = $file_path;
						$file_path     = $zip_path;
					} else {
						echo esc_html__( 'Failed to create export file.', 'woocommerce-smart-coupons' );
						exit();
					}
				}
				if ( file_exists( $file_path ) && is_readable( $file_path ) ) {

					nocache_headers();
					header( 'X-Robots-Tag: noindex, nofollow', true );
					header( 'Content-Type: ' . $mime_type . '; charset=UTF-8' );
					header( 'Content-Description: File Transfer' );
					header( 'Content-Transfer-Encoding: binary' );
					header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $file_name ) . '";' );
					readfile( $file_path ); // phpcs:ignore
					if ( ! empty( $upload_dir_path ) && false !== strpos( $file_path, $upload_dir_path ) ) {
						unlink( $file_path ); // phpcs:ignore
					}
				} else {
					echo esc_html__( 'Failed to create export file.', 'woocommerce-smart-coupons' );
					exit();
				}

				if ( file_exists( $csv_file_path ) ) {
					if ( ! empty( $upload_dir_path ) && false !== strpos( $csv_file_path, $upload_dir_path ) ) {
						unlink( $csv_file_path ); // phpcs:ignore
					}
				}

				delete_option( 'woo_sc_action_data' );
				delete_option( 'wc_sc_background_coupon_process_result' );
				exit();

			}
		}

		/**
		 * Push coupon background progress in heartbeat response
		 *
		 * @param  array  $response  The response.
		 * @param  string $screen_id The screen id.
		 * @return array  $response
		 */
		public function check_coupon_background_progress( $response = array(), $screen_id = '' ) {

			if ( 'yes' === $this->is_process_running() ) {
				$progress = $this->calculate_coupon_background_progress();

				if ( ! empty( $progress['percent_completion'] ) ) {
					$response['percent_completion'] = $progress['percent_completion'];
				}

				$coupon_posted_data = get_option( 'woo_sc_generate_coupon_posted_data', false );

				if ( $coupon_posted_data ) {

					$coupon_action             = $coupon_posted_data['smart_coupons_generate_action'];
					$response['coupon_action'] = $coupon_action;
					$action_stage              = $coupon_posted_data['action_stage'];
					$response['action_stage']  = $action_stage;

				}

				$action_data = get_option( 'woo_sc_action_data', false );

				$response['action_data'] = $action_data;
			}

			return $response;
		}

		/**
		 * Checks if background process is running
		 *
		 * @return string  $is_process_running
		 */
		public function is_process_running() {

			// Return process status if it has already been saved.
			if ( ! empty( $this->is_process_running ) ) {
				return $this->is_process_running;
			}

			$bulk_action              = get_option( 'bulk_coupon_action_woo_sc', false );
			$this->is_process_running = ( ! empty( $bulk_action ) ) ? 'yes' : 'no';

			return $this->is_process_running;
		}

		/**
		 * Function to send combined emails when receiver is the same.
		 *
		 * @param array $action_args Action arguments.
		 */
		public function send_scheduled_combined_email( $action_args = array() ) {

			$posted_data = get_option( 'woo_sc_generate_coupon_posted_data', true );

			if ( true === $posted_data ) {
				return;
			}

			if ( empty( $action_args['receiver_email'] ) || empty( $action_args['coupon_ids'] ) || ! is_array( $action_args['coupon_ids'] ) ) {
				return;
			}

			$receiver_email   = $action_args['receiver_email'];
			$coupon_ids       = $action_args['coupon_ids'];
			$receiver_details = array();
			$message          = '';

			if ( ! empty( $posted_data ) && is_array( $posted_data ) ) {
				$message = ( ! empty( $posted_data['smart_coupon_message'] ) ) ? $posted_data['smart_coupon_message'] : '';
			}

			foreach ( $coupon_ids as $coupon_id ) {
				$coupon = new WC_Coupon( $coupon_id );
				if ( is_a( $coupon, 'WC_Coupon' ) ) {
					if ( $this->is_wc_gte_30() ) {
						$coupon_code = $coupon->get_code();
					} else {
						$coupon_code = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
					}
					$receiver_details[] = array(
						'code'    => $coupon_code,
						'message' => $message,
					);
				}
			}

			if ( ! empty( $receiver_details ) ) {
				$this->send_combined_coupon_email( $receiver_email, $receiver_details );
			}
		}

		/**
		 * Calculate progress of background coupon process
		 *
		 * @return array $progress
		 */
		public function calculate_coupon_background_progress() {
			$progress = array();

			$start_time            = get_option( 'start_time_woo_sc', false );
			$current_time          = get_option( 'current_time_woo_sc', false );
			$all_tasks_count       = get_option( 'all_tasks_count_woo_sc', false );
			$remaining_tasks_count = get_option( 'remaining_tasks_count_woo_sc', false );

			$percent_completion = floatval( 0 );
			if ( false !== $all_tasks_count && false !== $remaining_tasks_count ) {
				$percent_completion             = ( ( intval( $all_tasks_count ) - intval( $remaining_tasks_count ) ) * 100 ) / intval( $all_tasks_count );
				$progress['percent_completion'] = floatval( $percent_completion );
			}

			if ( $percent_completion > 0 && false !== $start_time && false !== $current_time ) {
				$time_taken_in_seconds         = $current_time - $start_time;
				$time_remaining_in_seconds     = ( $time_taken_in_seconds / $percent_completion ) * ( 100 - $percent_completion );
				$progress['remaining_seconds'] = ceil( $time_remaining_in_seconds );
			}

			return $progress;
		}

		/**
		 * Generate Coupons' CSV from saved coupon's data
		 */
		public function woo_sc_generate_coupon_csv() {

			$posted_data = get_option( 'woo_sc_generate_coupon_posted_data', true );

			if ( true === $posted_data ) {
				return;
			}

			$no_of_coupons_to_generate = absint( $posted_data['no_of_coupons_to_generate'] );

			$woocommerce_smart_coupon = WC_Smart_Coupons::get_instance();

			$coupon_column_headers   = $this->get_coupon_column_headers();
			$coupon_posts_headers    = $coupon_column_headers['posts_headers'];
			$coupon_postmeta_headers = $coupon_column_headers['postmeta_headers'];
			$coupon_term_headers     = $coupon_column_headers['term_headers'];

			$column_headers = array_merge( $coupon_posts_headers, $coupon_postmeta_headers, $coupon_term_headers );

			$batch_start_time = time();
			$start_time       = get_option( 'start_time_woo_sc', false );
			if ( false === $start_time ) {
				update_option( 'start_time_woo_sc', $batch_start_time, 'no' );
			}

			$all_tasks_count = get_option( 'all_tasks_count_woo_sc', false );

			if ( false === $all_tasks_count ) {
				update_option( 'all_tasks_count_woo_sc', $posted_data['total_coupons_to_generate'], 'no' );
			}

			if ( isset( $posted_data['export_file'] ) && is_array( $posted_data['export_file'] ) ) {
				$export_file      = $posted_data['export_file'];
				$csv_folder       = $export_file['wp_upload_dir'];
				$filename         = str_replace( array( '\'', '"', ',', ';', '<', '>', '/', ':' ), '', $export_file['file_name'] );
				$csvfilename      = $csv_folder . $filename;
				$csv_file_handler          = fopen( $csvfilename, 'a' ); // phpcs:ignore
				// Proceed only if file has opened in append mode.
				if ( false !== $csv_file_handler ) {
					for ( $no_of_coupons_created = 1; $no_of_coupons_created <= $no_of_coupons_to_generate; $no_of_coupons_created++ ) {
						$posted_data['no_of_coupons_to_generate'] = 1;
						$posted_data['customer_email']            = empty( $posted_data['customer_email'] ) ? $posted_data['smart_coupon_email'] : $posted_data['customer_email'];
						$coupon_data                              = $woocommerce_smart_coupon->generate_coupons_code( $posted_data, '', '' );
						$file_data = $woocommerce_smart_coupon->get_coupon_csv_data( $column_headers, $coupon_data ); // phpcs:ignore
						if ( $file_data ) {

								fwrite( $csv_file_handler, $file_data ); // phpcs:ignore
								$no_of_remaining_coupons = $no_of_coupons_to_generate - $no_of_coupons_created;

								update_option( 'current_time_woo_sc', time(), 'no' );
								update_option( 'remaining_tasks_count_woo_sc', $no_of_remaining_coupons, 'no' );

							if ( ! empty( $posted_data['customer_email'] ) ) {
								$emails = explode( ',', $posted_data['customer_email'] );
								array_shift( $emails ); // Remove first email so that it does not included in next run.
								$posted_data['customer_email'] = implode( ',', $emails );
							}

							// If csv generation is complete.
							if ( 0 === $no_of_remaining_coupons ) {
								// If user opted for add_to_store option then create another scheduler to generate actual coupons.
								if ( in_array( $posted_data['smart_coupons_generate_action'], array( 'add_to_store', 'woo_sc_is_email_imported_coupons', 'send_store_credit' ), true ) ) {

									delete_option( 'start_time_woo_sc' );
									delete_option( 'current_time_woo_sc' );

									$posted_data['no_of_coupons_to_generate'] = $posted_data['total_coupons_to_generate'];
									$posted_data['action_stage']              = 1;
									update_option( 'woo_sc_generate_coupon_posted_data', $posted_data, 'no' );

									do_action( 'woo_sc_import_coupons_from_csv' );
								} else {

									$bulk_coupon_action    = get_option( 'bulk_coupon_action_woo_sc' );
									$all_tasks_count       = get_option( 'all_tasks_count_woo_sc' );
									$remaining_tasks_count = get_option( 'remaining_tasks_count_woo_sc' );
									$success_count         = $all_tasks_count - $remaining_tasks_count;

									$coupon_background_process_result = array(
										'action'     => $bulk_coupon_action,
										'successful' => $success_count,
									);

									delete_option( 'bulk_coupon_action_woo_sc' );
									update_option( 'wc_sc_background_coupon_process_result', $coupon_background_process_result, 'no' );

									$action_data = array(
										'name' => 'download_csv',
										'data' => array(
											'generated_file_path' => $csvfilename,
										),
									);
									update_option( 'woo_sc_action_data', $action_data, 'no' );
								}
							} elseif ( $this->time_exceeded( $batch_start_time ) || $this->memory_exceeded() ) {
								$posted_data['no_of_coupons_to_generate'] = $no_of_remaining_coupons;
								update_option( 'woo_sc_generate_coupon_posted_data', $posted_data, 'no' );
								if ( function_exists( 'as_schedule_single_action' ) ) {
									as_schedule_single_action( time(), 'woo_sc_generate_coupon_csv' );
								}
								break;
							}
						}
					}
					fclose( $csv_file_handler ); // phpcs:ignore
				}
			}

		}

		/**
		 * Generate Coupons from generated/imported csv file
		 */
		public function woo_sc_import_coupons_from_csv() {

			$posted_data = get_option( 'woo_sc_generate_coupon_posted_data', true );

			if ( true === $posted_data ) {
				return;
			}

			$is_send_email             = $this->is_email_template_enabled();
			$combine_emails            = $this->is_email_template_enabled( 'combine' );
			$is_email_imported_coupons = get_option( 'woo_sc_is_email_imported_coupons' );
			$no_of_coupons_to_generate = $posted_data['no_of_coupons_to_generate'];

			require 'class-wc-sc-coupon-import.php';
			require 'class-wc-sc-coupon-parser.php';

			$wc_csv_coupon_import         = new WC_SC_Coupon_Import();
			$wc_csv_coupon_import->parser = new WC_SC_Coupon_Parser( 'shop_coupon' );
			$woocommerce_smart_coupon     = WC_Smart_Coupons::get_instance();

			$upload_dir      = wp_get_upload_dir();
			$upload_dir_path = $upload_dir['basedir'] . '/woocommerce_uploads';

			if ( isset( $posted_data['export_file'] ) && is_array( $posted_data['export_file'] ) ) {

				$export_file   = $posted_data['export_file'];
				$csv_folder    = $export_file['wp_upload_dir'];
				$filename      = str_replace( array( '\'', '"', ',', ';', '<', '>', '/', ':' ), '', $export_file['file_name'] );
				$csvfilename   = $csv_folder . $filename;
				$file_position = isset( $posted_data['file_position'] ) && is_numeric( $posted_data['file_position'] ) ? $posted_data['file_position'] : 0;

				// Set locale.
				$encoding = mb_detect_encoding( $csvfilename, 'UTF-8, ISO-8859-1', true );
				if ( $encoding ) {
					setlocale( LC_ALL, 'en_US.' . $encoding );
				}
				ini_set( 'auto_detect_line_endings', true ); // phpcs:ignore
				$csv_file_handler = fopen( $csvfilename, 'r' ); // phpcs:ignore
				if ( false !== $csv_file_handler ) {
					$csv_header = fgetcsv( $csv_file_handler, 0 );
					$counter    = 0;

					$batch_start_time = time();
					$start_time       = get_option( 'start_time_woo_sc', false );
					if ( false === $start_time ) {
						update_option( 'start_time_woo_sc', $batch_start_time, 'no' );
					}

					$reading_completed         = false;
					$no_of_remaining_coupons   = -1;
					$combined_receiver_details = array();
					for ( $no_of_coupons_created = 1; $no_of_coupons_created <= $no_of_coupons_to_generate; $no_of_coupons_created++ ) {

						$result            = $wc_csv_coupon_import->parser->parse_data_by_row( $csv_file_handler, $csv_header, $file_position, $encoding );
						$file_position     = $result['file_position'];
						$parsed_csv_data   = $result['parsed_csv_data'];
						$reading_completed = $result['reading_completed'];
						if ( ! $reading_completed ) {
							$coupon             = $wc_csv_coupon_import->parser->parse_coupon( $parsed_csv_data );
							$coupon_parsed_data = array(
								'filter' => array(
									'class'    => 'WC_SC_Coupon_Import',
									'function' => 'process_coupon',
								),
								'args'   => array( $coupon ),
							);

							$coupon_id = $this->create_coupon( $coupon_parsed_data );

							if ( ! empty( $parsed_csv_data['customer_email'] ) && 'yes' === $is_send_email && 'yes' === $combine_emails && 'yes' === $is_email_imported_coupons ) {
								$receiver_emails = explode( ',', $parsed_csv_data['customer_email'] );
								foreach ( $receiver_emails as $receiver_email ) {
									if ( ! isset( $combined_receiver_details[ $receiver_email ] ) || ! is_array( $combined_receiver_details[ $receiver_email ] ) ) {
										$combined_receiver_details[ $receiver_email ] = array();
									}
									$combined_receiver_details[ $receiver_email ][] = array(
										'code' => $parsed_csv_data['post_title'],
									);
								}
							}
							$counter++;
						}

						$no_of_remaining_coupons = $no_of_coupons_to_generate - $no_of_coupons_created;
						update_option( 'current_time_woo_sc', time(), 'no' );
						update_option( 'remaining_tasks_count_woo_sc', $no_of_remaining_coupons, 'no' );

						if ( 0 === $no_of_remaining_coupons ) {

							$bulk_coupon_action    = get_option( 'bulk_coupon_action_woo_sc' );
							$all_tasks_count       = get_option( 'all_tasks_count_woo_sc' );
							$remaining_tasks_count = get_option( 'remaining_tasks_count_woo_sc' );
							$success_count         = $all_tasks_count - $remaining_tasks_count;

							$coupon_background_process_result = array(
								'action'     => $bulk_coupon_action,
								'successful' => $success_count,
							);

							fclose( $csv_file_handler ); // phpcs:ignore
							if ( ! empty( $upload_dir_path ) && false !== strpos( $csvfilename, $upload_dir_path ) ) {
								unlink( $csvfilename ); // phpcs:ignore
							}
							update_option( 'woo_sc_is_email_imported_coupons', 'no', 'no' );
							delete_option( 'bulk_coupon_action_woo_sc' );

							update_option( 'wc_sc_background_coupon_process_result', $coupon_background_process_result, 'no' );
							break;
						}
						$posted_data['no_of_coupons_to_generate'] = $no_of_remaining_coupons;
						if ( $this->time_exceeded( $batch_start_time ) || $this->memory_exceeded() ) {
							fclose( $csv_file_handler ); // phpcs:ignore
							$posted_data['file_position'] = $file_position;
							update_option( 'woo_sc_generate_coupon_posted_data', $posted_data, 'no' );
							if ( function_exists( 'as_schedule_single_action' ) ) {
								as_schedule_single_action( time(), 'woo_sc_import_coupons_from_csv' );
							}
							break;
						}
					}
					if ( ! empty( $combined_receiver_details ) && is_array( $combined_receiver_details ) ) {

						foreach ( $combined_receiver_details as $receiver_email => $coupon_codes ) {
							$coupon_ids = array();
							foreach ( $coupon_codes as $coupon_data ) {
								$coupon_code = $coupon_data['code'];
								$coupon      = new WC_Coupon( $coupon_code );
								if ( is_a( $coupon, 'WC_Coupon' ) ) {
									if ( $this->is_wc_gte_30() ) {
										$coupon_id = $coupon->get_id();
									} else {
										$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
									}
									$coupon_ids[] = $coupon_id;
								}
							}
							if ( ! empty( $receiver_email ) && ! empty( $coupon_ids ) ) {
								$action_args = array(
									'args' => array(
										'receiver_email' => $receiver_email,
										'coupon_ids'     => $coupon_ids,
									),
								);
								if ( function_exists( 'as_schedule_single_action' ) ) {
									as_schedule_single_action( time(), 'woocommerce_smart_coupons_send_combined_coupon_email', $action_args );
								}
							}
						}
					}
				}
			}
		}

		/**
		 * Create coupon from parsed coupon data
		 *
		 * @param  array $parsed_data parsed coupon data.
		 */
		public function create_coupon( $parsed_data ) {

			$coupon_id = 0;
			if ( isset( $parsed_data['filter'], $parsed_data['args'] ) ) {
				try {
					if ( empty( $this->global_coupons_new ) && ! is_array( $this->global_coupons_new ) ) {
						$this->global_coupons_new = array();
					}
					if ( ! class_exists( $parsed_data['filter']['class'] ) ) {
						include_once 'class-' . strtolower( str_replace( '_', '-', $parsed_data['filter']['class'] ) ) . '.php';
					}
					$object    = $parsed_data['filter']['class']::get_instance();
					$coupon_id = call_user_func_array( array( $object, $parsed_data['filter']['function'] ), $parsed_data['args'] );
				} catch ( Exception $e ) {
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						error_log( 'Error: ' . $e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__ ); // phpcs:ignore
					}
				}
			}
			return $coupon_id;
		}

		/**
		 * Restart scheduler after one minute if it fails
		 *
		 * @param  array $action_id id of failed action.
		 */
		public function restart_failed_action( $action_id = 0 ) {

			if ( empty( $action_id ) || ! class_exists( 'ActionScheduler' ) || ! is_callable( array( 'ActionScheduler', 'store' ) ) || ! function_exists( 'as_schedule_single_action' ) ) {
				return;
			}

			$action      = ActionScheduler::store()->fetch_action( $action_id );
			$action_hook = $action->get_hook();

			$hooks = $this->get_scheduled_action_hooks();
			if ( in_array( $action_hook, $hooks, true ) ) {
				$posted_data = get_option( 'woo_sc_generate_coupon_posted_data', true );
				if ( true === $posted_data ) {
					return;
				}
			}

			if ( in_array( $action_hook, array( 'woo_sc_generate_coupon_csv', 'woo_sc_import_coupons_from_csv' ), true ) ) {
				as_schedule_single_action( time() + MINUTE_IN_SECONDS, $action_hook );
			} elseif ( in_array( $action_hook, array( 'wc_sc_send_scheduled_coupon_email', 'woocommerce_smart_coupons_send_combined_coupon_email' ), true ) ) {
				$action_args = $action->get_args();
				as_schedule_single_action( time() + MINUTE_IN_SECONDS, $action_hook, $action_args );
			}
		}

		/**
		 * Function to modify action scheduler's default interval between two consecutive scheduler when smart coupon process running
		 *
		 * @param array $schedules schedules with interval and display.
		 * @return array $schedules
		 */
		public function modify_action_scheduler_default_interval( $schedules = array() ) {

			if ( 'yes' === $this->is_process_running() ) {

				$schedules['every_minute'] = array(
					'interval' => 5,
					'display'  => __( 'Every 5 Seconds', 'woocommerce-smart-coupons' ),
				);
			}

			return $schedules;

		}

	}

}

WC_SC_Background_Coupon_Importer::get_instance();
