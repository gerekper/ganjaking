<?php

namespace ACA\YoastSeo\Service;

use AC\ListScreen;
use AC\ListScreenPost;
use AC\Registerable;
use AC\Table\Screen;
use ACA\YoastSeo\Settings\ListScreen\HideOnScreen\FilterReadabilityScore;
use ACA\YoastSeo\Settings\ListScreen\HideOnScreen\FilterSeoScores;
use ACP\Settings\ListScreen\HideOnScreenCollection;
use ACP\Type\HideOnScreen\Group;
use WPSEO_Metabox_Analysis_Readability;
use WPSEO_Metabox_Analysis_SEO;
use WPSEO_Post_Type;

final class HideFilters implements Registerable {

	/**
	 * @var FilterSeoScores
	 */
	private $seo_scores;

	/**
	 * @var FilterReadabilityScore
	 */
	private $readability_score;

	public function register(): void
    {
		$this->seo_scores = new FilterSeoScores();
		$this->readability_score = new FilterReadabilityScore();

		add_action( 'ac/table', [ $this, 'hide_filter' ] );
		add_action( 'acp/admin/settings/hide_on_screen', [ $this, 'add_hide_filter' ], 10, 2 );
	}

	public function hide_filter( Screen $table ) {
		global $wpseo_meta_columns;

		if ( ! $wpseo_meta_columns ) {
			return;
		}

		$list_screen = $table->get_list_screen();

		if ( $this->seo_scores->is_hidden( $list_screen ) ) {
			remove_action( 'restrict_manage_posts', [ $wpseo_meta_columns, 'posts_filter_dropdown' ] );
		}
		if ( $this->readability_score->is_hidden( $list_screen ) ) {
			remove_action( 'restrict_manage_posts', [ $wpseo_meta_columns, 'posts_filter_dropdown_readability' ] );
		}
	}

	/**
	 * @param string $post_type
	 *
	 * @return bool
	 */
	private function is_post_type_supported( $post_type ) {
		return class_exists( 'WPSEO_Post_Type' ) && WPSEO_Post_Type::is_post_type_accessible( $post_type );
	}

	/**
	 * @param string $post_type
	 *
	 * @return bool
	 */
	private function is_analysis_enabled( $post_type ) {
		return $this->is_post_type_supported( $post_type ) && ( new WPSEO_Metabox_Analysis_SEO() )->is_globally_enabled();
	}

	/**
	 * @param string $post_type
	 *
	 * @return bool
	 */
	private function is_readability_enabled( $post_type ) {
		return $this->is_post_type_supported( $post_type ) && ( new WPSEO_Metabox_Analysis_Readability() )->is_globally_enabled();
	}

	public function add_hide_filter( HideOnScreenCollection $collection, ListScreen $list_screen ) {
		if ( ! $list_screen instanceof ListScreenPost ) {
			return;
		}

		if ( $this->is_analysis_enabled( $list_screen->get_post_type() ) ) {
			$collection->add( $this->seo_scores, new Group( Group::ELEMENT ), 38 );
		}
		if ( $this->is_readability_enabled( $list_screen->get_post_type() ) ) {
			$collection->add( $this->readability_score, new Group( Group::ELEMENT ), 38 );
		}
	}

}