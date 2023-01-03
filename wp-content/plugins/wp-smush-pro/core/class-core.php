<?php
/**
 * Core class: Core class.
 *
 * @since 2.9.0
 * @package Smush\Core
 */

namespace Smush\Core;

use WP_Smush;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Core
 */
class Core extends Stats {

	/**
	 * Animated status.
	 *
	 * @var int
	 */
	const STATUS_ANIMATED = 2;

	/**
	 * S3 module
	 *
	 * @var Integrations\S3
	 */
	public $s3;

	/**
	 * NextGen module.
	 *
	 * @var Integrations\Nextgen
	 */
	public $nextgen;

	/**
	 * Modules array.
	 *
	 * @var Modules
	 */
	public $mod;

	/**
	 * Allowed mime types of image.
	 *
	 * @var array $mime_types
	 */
	public static $mime_types = array(
		'image/jpg',
		'image/jpeg',
		'image/x-citrix-jpeg',
		'image/gif',
		'image/png',
		'image/x-png',
	);

	/**
	 * List of external pages where smush needs to be loaded.
	 *
	 * @var array $pages
	 */
	public static $external_pages = array(
		'nggallery-manage-images',
		'gallery_page_nggallery-manage-gallery',
		'gallery_page_wp-smush-nextgen-bulk',
		'nextgen-gallery_page_nggallery-manage-gallery', // Different since NextGen 3.3.6.
		'nextgen-gallery_page_wp-smush-nextgen-bulk', // Different since NextGen 3.3.6.
		'post',
		'post-new',
		'page',
		'edit-page',
		'upload',
	);

	/**
	 * Attachment IDs which are smushed.
	 *
	 * @var array $smushed_attachments
	 */
	public $smushed_attachments = array();

	/**
	 * Unsmushed image IDs.
	 *
	 * @var array $unsmushed_attachments
	 */
	public $unsmushed_attachments = array();

	/**
	 * Skipped attachment IDs.
	 *
	 * @since 3.0
	 *
	 * @var array $skipped_attachments
	 */
	public $skipped_attachments = array();

	/**
	 * Smushed attachments out of total attachments.
	 *
	 * @var int $smushed_count
	 */
	public $smushed_count = 0;

	/**
	 * Smushed attachments out of total attachments.
	 *
	 * @var int $remaining_count
	 */
	public $remaining_count = 0;

	/**
	 * Images with errors that have been skipped from bulk smushing.
	 *
	 * @since 3.0
	 * @var int $skipped_count
	 */
	public $skipped_count = 0;

	/**
	 * Super Smushed attachments count.
	 *
	 * @var int $super_smushed
	 */
	public $super_smushed = 0;

	/**
	 * Total count of attachments for smushing.
	 *
	 * @var int $total_count
	 */
	public $total_count = 0;

	/**
	 * Limit for allowed number of images per bulk request.
	 *
	 * This is enforced at api level too.
	 *
	 * @var int $max_free_bulk
	 */
	public static $max_free_bulk = 50;

	/**
	 * Initialize modules.
	 *
	 * @since 2.9.0
	 */
	protected function init() {
		$this->mod = new Modules();

		// Enqueue scripts and initialize variables.
		add_action( 'admin_init', array( $this, 'init_settings' ) );

		// Load integrations.
		add_action( 'init', array( $this, 'load_integrations' ) );

		// Big image size threshold (WordPress 5.3+).
		add_filter( 'big_image_size_threshold', array( $this, 'big_image_size_threshold' ), 10 );

		/**
		 * Load NextGen Gallery, instantiate the Async class. if hooked too late or early, auto Smush doesn't
		 * work, also load after settings have been saved on init action.
		 */
		add_action( 'plugins_loaded', array( $this, 'load_libs' ), 90 );

		/**
		 * Maybe need to load some modules in REST API mode.
		 * E.g. S3.
		 */
		add_action( 'rest_api_init', array( $this, 'load_libs_for_rest_api' ), 99 );

		// Load Black Friday notice.
		add_action( 'admin_init', array( $this, 'load_blackfriday' ) );
	}

	/**
	 * Load integrations class.
	 *
	 * @since 2.8.0
	 */
	public function load_integrations() {
		new Integrations\Common();
	}

	/**
	 * Load plugin modules.
	 */
	public function load_libs() {
		$this->wp_smush_async();

		if ( is_admin() ) {
			$this->s3 = new Integrations\S3();
		}

		/**
		 * Load NextGen integration on admin or custom ajax request.
		 *
		 * @since 3.10.0
		 */
		if ( is_admin() || defined( 'NGG_AJAX_SLUG' ) && ! empty( $_REQUEST[ NGG_AJAX_SLUG ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->nextgen = new Integrations\Nextgen();
		}

		new Integrations\Gutenberg();
		new Integrations\Composer();
		new Integrations\Gravity_Forms();
		new Integrations\Envira( $this->mod->cdn );
		new Integrations\Avada( $this->mod->cdn );

		// Register logger to schedule cronjob.
		Helper::logger();
	}

	/**
	 * Load lib for REST API.
	 */
	public function load_libs_for_rest_api() {
		// Load S3 if there is media REST API.
		if ( ! Helper::is_non_rest_media() && ! $this->s3 ) {
			$this->s3 = new Integrations\S3();
		}
	}

	/**
	 * Initialize the Smush Async class.
	 */
	private function wp_smush_async() {
		// Check if Async is disabled.
		if ( defined( 'WP_SMUSH_ASYNC' ) && ! WP_SMUSH_ASYNC ) {
			return;
		}

		// Instantiate class.
		new Modules\Async\Async();

		// Load the Editor Async task only if user logged in or in backend.
		if ( is_admin() && is_user_logged_in() ) {
			new Modules\Async\Editor();
		}
	}

	/**
	 * Init settings.
	 */
	public function init_settings() {
		// Initialize Image dimensions.
		$this->mod->smush->image_sizes = $this->image_dimensions();
	}

	/**
	 * Localize translations.
	 */
	public function localize() {
		global $current_screen;

		$handle = 'smush-admin';

		$upgrade_url = add_query_arg(
			array(
				'coupon'       => 'SMUSH30OFF',
				'utm_source'   => 'smush',
				'utm_medium'   => 'plugin',
				'utm_campaign' => 'smush_bulksmush_inline_filesizelimit',
			),
			'https://wpmudev.com/project/wp-smush-pro/'
		);

		$wp_smush_msgs = array(
			'nonce'                   => wp_create_nonce( 'wp-smush-ajax' ),
			'webp_nonce'              => wp_create_nonce( 'wp-smush-webp-nonce' ),
			'settingsUpdated'         => esc_html__( 'Your settings have been updated', 'wp-smushit' ),
			'resmush'                 => esc_html__( 'Super-Smush', 'wp-smushit' ),
			'smush_now'               => esc_html__( 'Smush Now', 'wp-smushit' ),
			'error_in_bulk'           => esc_html__( '{{smushed}}/{{total}} images smushed successfully, {{errors}} images were not optimized, find out why and how to resolve the issue(s) below.', 'wp-smushit' ),
			'all_failed'              => esc_html__( 'All of your images failed to smush. Find out why and how to resolve the issue(s) below.', 'wp-smushit' ),
			'all_resmushed'           => esc_html__( 'All images are fully optimized.', 'wp-smushit' ),
			'all_smushed'             => esc_html__( 'All attachments have been smushed. Awesome!', 'wp-smushit' ),
			'error_size_limit'        => WP_Smush::is_pro() ? '' : sprintf(
			/* translators: %1$s - opening link tag, %2$s - </a> */
				esc_html__( 'Are you hitting the 5MB "size limit exceeded" warning? %1$sUpgrade to Smush Pro for FREE%2$s to optimize unlimited image files up to 32Mb each.', 'wp-smushit' ),
				'<a href="' . esc_url( $upgrade_url ) . '" target="_blank">',
				'</a>'
			),
			'processing_cdn_for_free' => esc_html__( 'Want to serve images even faster? Get up to 2x more speed with Smush Pro’s CDN, which spans 45 servers worldwide.', 'wp-smushit' ),
			'processed_cdn_for_free'  => esc_html__( 'Let images reach your audience faster no matter where your hosting servers are. Smush Pro’s global CDN serves images closer to site visitors via 45 worldwide server locations.', 'wp-smushit' ),
			'restore'                 => esc_html__( 'Restoring image...', 'wp-smushit' ),
			'smushing'                => esc_html__( 'Smushing image...', 'wp-smushit' ),
			'btn_ignore'              => esc_html__( 'Ignore', 'wp-smushit' ),
			'view_detail'             => esc_html__( 'View Details', 'wp-smushit' ),
			'membership_valid'        => esc_html__( 'We successfully verified your membership, all the Pro features should work completely. ', 'wp-smushit' ),
			'membership_invalid'      => esc_html__( "Your membership couldn't be verified.", 'wp-smushit' ),
			'missing_path'            => esc_html__( 'Missing file path.', 'wp-smushit' ),
			'failed_item_smushed'     => esc_html__( 'Images smushed successfully, No further action required', 'wp-smushit' ),
			// Used by Directory Smush.
			'unfinished_smush_single' => esc_html__( 'image could not be smushed.', 'wp-smushit' ),
			'unfinished_smush'        => esc_html__( 'images could not be smushed.', 'wp-smushit' ),
			'already_optimised'       => esc_html__( 'Already Optimized', 'wp-smushit' ),
			'ajax_error'              => esc_html__( 'Ajax Error', 'wp-smushit' ),
			'generic_ajax_error'      => esc_html__( 'Something went wrong with the request. Please reload the page and try again.', 'wp-smushit' ),
			'all_done'                => esc_html__( 'All Done!', 'wp-smushit' ),
			'sync_stats'              => esc_html__( 'Give us a moment while we sync the stats.', 'wp-smushit' ),
			// Progress bar text.
			'progress_smushed'        => esc_html__( 'images optimized', 'wp-smushit' ),
			'bulk_resume'             => esc_html__( 'Resume scan', 'wp-smushit' ),
			'bulk_stop'               => esc_html__( 'Stop current bulk smush process.', 'wp-smushit' ),
			// Errors.
			'error_ignore'            => esc_html__( 'Ignore this image from bulk smushing', 'wp-smushit' ),
			// Ignore text.
			'ignored'                 => esc_html__( 'Ignored', 'wp-smushit' ),
			'not_processed'           => esc_html__( 'Not processed', 'wp-smushit' ),
			// Notices.
			'noticeDismiss'           => esc_html__( 'Dismiss', 'wp-smushit' ),
			'noticeDismissTooltip'    => esc_html__( 'Dismiss notice', 'wp-smushit' ),
			'tutorialsRemoved'        => sprintf( /* translators: %1$s - opening a tag, %2$s - closing a tag */
				esc_html__( 'The widget has been removed. Smush tutorials can still be found in the %1$sTutorials tab%2$s any time.', 'wp-smushit' ),
				'<a href=' . esc_url( menu_page_url( 'smush-tutorials', false ) ) . '>',
				'</a>'
			),
			// URLs.
			'smush_url'               => network_admin_url( 'admin.php?page=smush' ),
			'bulk_smush_url'          => network_admin_url( 'admin.php?page=smush-bulk' ),
			'directory_url'           => network_admin_url( 'admin.php?page=smush-directory' ),
			'localWebpURL'            => network_admin_url( 'admin.php?page=smush-webp' ),
			'edit_link'               => Helper::get_image_media_link( '{{id}}', null, true ),
			'debug_mode'              => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'cancel'                  => esc_html__( 'Cancel', 'wp-smushit' ),
			'cancelling'              => esc_html__( 'Cancelling ...', 'wp-smushit' ),
		);

		wp_localize_script( $handle, 'wp_smush_msgs', $wp_smush_msgs );

		$product_analytics = WP_Smush::get_instance()->core()->mod->product_analytics;
		wp_localize_script(
			$handle,
			'wp_smush_mixpanel',
			array(
				'opt_in'           => Settings::get_instance()->get( 'usage' ),
				'token'            => $product_analytics->get_token(),
				'unique_id'        => $product_analytics->get_unique_id(),
				'super_properties' => $product_analytics->get_super_properties(),
			)
		);

		if ( 'toplevel_page_smush' === $current_screen->id ) {
			$slug = 'dashboard';
		} else {
			$slug = explode( 'page_smush-', $current_screen->id );
			$slug = isset( $slug[1] ) ? $slug[1] : false;
		}

		// Load the stats on selected screens only.
		if ( $slug && isset( WP_Smush::get_instance()->admin()->pages[ $slug ] ) && method_exists( WP_Smush::get_instance()->admin()->pages[ $slug ], 'dashboard_summary_meta_box' ) ) {
			// Get resmush list, If we have a resmush list already, localize those IDs.
			$resmush_ids = $this->get_resmush_ids();
			if ( $resmush_ids ) {
				// Get the attachments, and get lossless count.
				$this->resmush_ids = $resmush_ids;
			}

			if ( ! defined( 'WP_SMUSH_DISABLE_STATS' ) || ! WP_SMUSH_DISABLE_STATS ) {
				// Setup all the stats.
				$this->setup_global_stats( true );
			}

			// Get attachments if all the images are not smushed.
			$this->unsmushed_attachments = $this->remaining_count > 0 ? $this->get_unsmushed_attachments() : array();
			$this->unsmushed_attachments = ! empty( $this->unsmushed_attachments ) && is_array( $this->unsmushed_attachments ) ? array_values( $this->unsmushed_attachments ) : $this->unsmushed_attachments;

			// Array of all smushed, unsmushed and lossless IDs.
			$data = array(
				'count_supersmushed' => $this->super_smushed,
				'count_smushed'      => $this->smushed_count,
				'count_total'        => $this->total_count - $this->skipped_count,
				'count_images'       => $this->stats['total_images'],
				'count_resize'       => $this->stats['resize_count'],
				'count_skipped'      => $this->skipped_count,
				'unsmushed'          => $this->unsmushed_attachments,
				'resmush'            => $this->resmush_ids,
				'size_before'        => $this->stats['size_before'],
				'size_after'         => $this->stats['size_after'],
				'savings_bytes'      => $this->stats['bytes'],
				'savings_resize'     => $this->stats['resize_savings'],
				'savings_conversion' => $this->stats['conversion_savings'],
				'savings_dir_smush'  => $this->dir_stats,
				'savings_percent'    => $this->stats['percent'] > 0 ? number_format_i18n( $this->stats['percent'], 1 ) : 0,
				'percent_grade'      => $this->percent_grade,
				'percent_metric'     => $this->percent_metric,
				'percent_optimized'  => $this->percent_optimized,
				'remaining_count'    => $this->remaining_count,
			);
		} else {
			$data = array(
				'count_supersmushed' => '',
				'count_smushed'      => '',
				'count_total'        => '',
				'count_images'       => '',
				'unsmushed'          => '',
				'resmush'            => '',
				'savings_bytes'      => '',
				'savings_resize'     => '',
				'savings_conversion' => '',
				'savings_supersmush' => '',
				'savings_percent'    => '',
				'percent_grade'      => '',
				'percent_metric'     => '',
				'percent_optimized'  => '',
			);
		}

		// Check if scanner class is available.
		$scanner_ready = isset( $this->mod->dir->scanner );

		$data['dir_smush'] = array(
			'currentScanStep' => $scanner_ready ? $this->mod->dir->scanner->get_current_scan_step() : 0,
			'totalSteps'      => $scanner_ready ? $this->mod->dir->scanner->get_scan_steps() : 0,
		);

		$data['resize_sizes'] = $this->get_max_image_dimensions();

		// Convert it into ms.
		$data['timeout'] = WP_SMUSH_TIMEOUT * 1000;

		wp_localize_script( $handle, 'wp_smushit_data', apply_filters( 'wp_smush_script_data', $data ) );
	}

	/**
	 * Get registered image sizes with dimension
	 *
	 * @return array
	 */
	public function image_dimensions() {
		// Get from cache if available to avoid duplicate looping.
		$sizes = wp_cache_get( 'get_image_sizes', 'smush_image_sizes' );
		if ( $sizes ) {
			return $sizes;
		}

		global $_wp_additional_image_sizes;
		$additional_sizes = get_intermediate_image_sizes();
		$sizes            = array();

		if ( empty( $additional_sizes ) ) {
			return $sizes;
		}

		// Create the full array with sizes and crop info.
		foreach ( $additional_sizes as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ), true ) ) {
				$sizes[ $_size ]['width']  = get_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop']   = (bool) get_option( $_size . '_crop' );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
				);
			}
		}

		// Medium Large.
		if ( ! isset( $sizes['medium_large'] ) || empty( $sizes['medium_large'] ) ) {
			$width  = (int) get_option( 'medium_large_size_w' );
			$height = (int) get_option( 'medium_large_size_h' );

			$sizes['medium_large'] = array(
				'width'  => $width,
				'height' => $height,
			);
		}

		// Set cache to avoid this loop next time.
		wp_cache_set( 'get_image_sizes', $sizes, 'smush_image_sizes' );

		return $sizes;
	}

	/**
	 * Get the Maximum Width and Height settings for WrodPress
	 *
	 * @return array, Array of Max. Width and Height for image.
	 */
	public function get_max_image_dimensions() {
		global $_wp_additional_image_sizes;

		$width  = 0;
		$height = 0;
		$limit  = 9999; // Post-thumbnail.

		$image_sizes = get_intermediate_image_sizes();

		// If image sizes are filtered and no image size list is returned.
		if ( empty( $image_sizes ) ) {
			return array(
				'width'  => $width,
				'height' => $height,
			);
		}

		// Create the full array with sizes and crop info.
		foreach ( $image_sizes as $size ) {
			if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ), true ) ) {
				$size_width  = get_option( "{$size}_size_w" );
				$size_height = get_option( "{$size}_size_h" );
			} elseif ( isset( $_wp_additional_image_sizes[ $size ] ) ) {
				$size_width  = $_wp_additional_image_sizes[ $size ]['width'];
				$size_height = $_wp_additional_image_sizes[ $size ]['height'];
			}

			// Skip if no width and height.
			if ( ! isset( $size_width, $size_height ) ) {
				continue;
			}

			// If within te limit, check for a max value.
			if ( $size_width <= $limit ) {
				$width = max( $width, $size_width );
			}

			if ( $size_height <= $limit ) {
				$height = max( $height, $size_height );
			}
		}

		return array(
			'width'  => $width,
			'height' => $height,
		);
	}

	/**
	 * Set the big image threshold.
	 *
	 * @since 3.3.2
	 *
	 * @param int $threshold  The threshold value in pixels. Default 2560.
	 *
	 * @return int|bool  New threshold. False if scaling is disabled.
	 */
	public function big_image_size_threshold( $threshold ) {
		if ( Settings::get_instance()->get( 'no_scale' ) ) {
			return false;
		}

		if ( ! $this->mod->resize->is_active() ) {
			return $threshold;
		}

		$resize_sizes = Settings::get_instance()->get_setting( 'wp-smush-resize_sizes' );
		if ( ! $resize_sizes || ! is_array( $resize_sizes ) ) {
			return $threshold;
		}

		return $resize_sizes['width'] > $resize_sizes['height'] ? $resize_sizes['width'] : $resize_sizes['height'];
	}

	/**
	 * Load Black Friday.
	 *
	 * @since 3.12.4
	 */
	public function load_blackfriday() {
		if ( ! file_exists( plugin_dir_path( __FILE__ ) . 'external/blackfriday/banner.php' ) || WP_Smush::is_pro() ) {
			return;
		}

		require_once plugin_dir_path( __FILE__ ) . 'external/blackfriday/banner.php';

		$labels = array(
			'close'       => esc_html__( 'Close', 'wp-smushit' ),
			'get_deal'    => esc_html__( 'Get deal', 'wp-smushit' ),
			'intro'       => esc_html__( 'Black Friday offer for WP businesses and agencies', 'wp-smushit' ),
			'off'         => esc_html__( 'Off', 'wp-smushit' ),
			'title'       => esc_html__( 'Everything you need to run your WP business for', 'wp-smushit' ),
			'discount'    => '83.5',
			'price'       => '3000',
			'description' => wp_kses( __( "From the creators of SMUSH, WPMU DEV's all-in-one platform gives you all the Pro tools and support you need to run and grow a web development business. Trusted by over 50,000 web developers. Limited deals available.", 'wp-smushit' ), array() ),
		);

		$cta_url = add_query_arg(
			array(
				'coupon'	   => 'BFP-2022',
				'utm_source'   => 'smush',
				'utm_medium'   => 'plugin',
				'utm_campaign' => 'BFP-2022-smush',
				'utm_id'       => 'BFP-2022',
				'utm_term'     => 'BF-2022-plugin-Smush',
				'utm_content'  => 'BF-2022',
			),
			esc_url( 'https://wpmudev.com/black-friday/' )
		);

		new \WPMUDEV\BlackFriday\Banner(
			$labels,
			$cta_url,
			\WPMUDEV\BlackFriday\Banner::SMUSH
		);
	}
}
