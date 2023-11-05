<?php
/**
 * NextGen admin view: Nextgen class
 *
 * @package Smush\App\Pages
 */

namespace Smush\App\Pages;

use Smush\App\Abstract_Page;
use Smush\App\Admin;
use WP_Smush;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Nextgen
 */
class Nextgen extends Abstract_Page {

	/**
	 * Function triggered when the page is loaded before render any content.
	 */
	public function on_load() {
		// Localize variables for NextGen Manage gallery page.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Render inner content.
	 */
	public function render_inner_content() {
		$this->view( 'smush-nextgen-page' );
	}

	/**
	 * Register meta boxes.
	 */
	public function register_meta_boxes() {
		$this->add_meta_box(
			'summary',
			null,
			array( $this, 'dashboard_summary_metabox' ),
			null,
			null,
			'summary',
			array(
				'box_class'         => 'sui-box sui-summary sui-summary-smush-metabox sui-summary-smush-nextgen',
				'box_content_class' => false,
			)
		);

		$this->add_meta_box(
			'bulk',
			__( 'Bulk Smush', 'wp-smushit' ),
			array( $this, 'bulk_metabox' ),
			array( $this, 'bulk_header_metabox' ),
			null,
			'bulk',
			array(
				'box_class' => 'sui-box bulk-smush-wrapper',
			)
		);
	}

	/**
	 * Enqueue Scripts on Manage Gallery page
	 */
	public function enqueue() {
		$current_screen = get_current_screen();
		if ( ! empty( $current_screen ) && in_array( $current_screen->base, Admin::$plugin_pages, true ) ) {
			WP_Smush::get_instance()->core()->nextgen->ng_admin->localize();
		}
	}


	/**
	 * NextGen summary meta box.
	 */
	public function dashboard_summary_metabox() {
		$ng_stats      = WP_Smush::get_instance()->core()->nextgen->ng_stats;
		$global_stats  = $ng_stats->get_global_stats();
		$lossy_enabled = $this->settings->get( 'lossy' );

		$this->view(
			'nextgen/summary-meta-box',
			array(
				'lossy_enabled'       => $lossy_enabled,
				'image_count'         => $ng_stats->get_array_value( $global_stats, 'count_images' ),
				'smushed_image_count' => $ng_stats->get_array_value( $global_stats, 'count_smushed' ),
				'super_smushed_count' => $ng_stats->get_array_value( $global_stats, 'count_supersmushed' ),
				'stats_human'         => $ng_stats->get_array_value( $global_stats, 'human_bytes' ),
				'stats_percent'       => $ng_stats->get_array_value( $global_stats, 'savings_percent'),
				'total_count'         => $ng_stats->get_array_value( $global_stats, 'count_total' ),
				'percent_grade'       => $ng_stats->get_array_value( $global_stats, 'percent_grade' ),
				'percent_metric'      => $ng_stats->get_array_value( $global_stats, 'percent_metric' ),
				'percent_optimized'   => $ng_stats->get_array_value( $global_stats, 'percent_optimized' ),
			)
		);
	}

	/**
	 * NextGen bulk Smush header meta box.
	 */
	public function bulk_header_metabox() {
		$this->view(
			'nextgen/meta-box-header',
			array(
				'title' => __( 'Bulk Smush', 'wp-smushit' ),
			)
		);
	}

	/**
	 * NextGen bulk Smush meta box.
	 */
	public function bulk_metabox() {
		$ng_stats     = WP_Smush::get_instance()->core()->nextgen->ng_stats;
		$global_stats = $ng_stats->get_global_stats();

		$url = add_query_arg(
			array(
				'page' => 'smush#wp-smush-settings-box',
			),
			admin_url( 'upload.php' )
		);

		$this->view(
			'nextgen/meta-box',
			array(
				'total_images_to_smush' => $ng_stats->get_array_value( $global_stats, 'remaining_count' ),
				'unsmushed_count'       => $ng_stats->get_array_value( $global_stats, 'count_unsmushed'),
				'resmush_count'         => $ng_stats->get_array_value( $global_stats, 'count_resmush'),
				'total_count'           => $ng_stats->get_array_value( $global_stats, 'count_total'),
				'url'                   => $url,
			)
		);
	}

}