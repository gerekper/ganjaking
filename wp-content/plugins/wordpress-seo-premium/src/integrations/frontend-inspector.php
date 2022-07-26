<?php

namespace Yoast\WP\SEO\Premium\Integrations;

use WPSEO_Metabox_Analysis_Readability;
use WPSEO_Metabox_Analysis_SEO;
use WPSEO_Options;
use Yoast\WP\SEO\Conditionals\Front_End_Conditional;
use Yoast\WP\SEO\Conditionals\Front_End_Inspector_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Frontend_Inspector class
 */
class Frontend_Inspector implements Integration_Interface {

	/**
	 * {@inheritDoc}
	 */
	public static function get_conditionals() {
		return [ Front_End_Conditional::class, Front_End_Inspector_Conditional::class ];
	}

	/**
	 * {@inheritDoc}
	 */
	public function register_hooks() {
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ], 11 );
	}

	/**
	 * Enqueue the workouts app.
	 */
	public function enqueue_assets() {
		if ( ! is_admin_bar_showing() || ! WPSEO_Options::get( 'enable_admin_bar_menu' ) ) {
			return;
		}

		// If the current user can't write posts, this is all of no use, so let's not output an admin menu.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$analysis_seo         = new WPSEO_Metabox_Analysis_SEO();
		$analysis_readability = new WPSEO_Metabox_Analysis_Readability();
		$current_page_meta    = \YoastSEO()->meta->for_current_page();
		$indexable            = $current_page_meta->indexable;
		$page_type            = $current_page_meta->page_type;

		$is_seo_analysis_active         = $analysis_seo->is_enabled();
		$is_readability_analysis_active = $analysis_readability->is_enabled();
		$display_metabox                = true;

		switch ( $page_type ) {
			case 'Home_Page':
			case 'Post_Type_Archive':
			case 'Date_Archive':
			case 'Error_Page':
			case 'Fallback':
			case 'Search_Result_Page':
				break;
			case 'Static_Home_Page':
			case 'Static_Posts_Page':
			case 'Post_Type':
				$display_metabox = WPSEO_Options::get( 'display-metabox-pt-' . $indexable->object_sub_type );
				break;
			case 'Term_Archive':
				$display_metabox = WPSEO_Options::get( 'display-metabox-tax-' . $indexable->object_sub_type );
				break;
			case 'Author_Archive':
				$display_metabox = false;
				break;
		}

		if ( ! $display_metabox ) {
			$is_seo_analysis_active         = false;
			$is_readability_analysis_active = false;
		}

		\wp_enqueue_script( 'yoast-seo-premium-frontend-inspector' );
		\wp_localize_script(
			'yoast-seo-premium-frontend-inspector',
			'wpseoScriptData',
			[
				'frontendInspector' => [
					'indexable'             => [
						'primary_focus_keyword'       => $indexable->primary_focus_keyword,
						'primary_focus_keyword_score' => $indexable->primary_focus_keyword_score,
						'readability_score'           => $indexable->readability_score,
					],
					'contentAnalysisActive' => $is_readability_analysis_active,
					'keywordAnalysisActive' => $is_seo_analysis_active,
				],
			]
		);
	}
}
