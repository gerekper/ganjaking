<?php

namespace Yoast\WP\SEO\Presenters\Admin\Prominent_Words;

use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Class Indexation_Modal_Presenter.
 */
class Indexation_Modal_Presenter extends Abstract_Presenter {

	/**
	 * The number of objects that need to be reindexed.
	 *
	 * @var int
	 */
	protected $total_unindexed;

	/**
	 * Indexation_Modal constructor.
	 *
	 * @param int $total_unindexed The number of objects that need to be indexed.
	 */
	public function __construct( $total_unindexed ) {
		$this->total_unindexed = $total_unindexed;
	}

	/**
	 * Presents the modal.
	 *
	 * @return string The modal HTML.
	 */
	public function present() {
		$blocks = [];

		if ( $this->total_unindexed === 0 ) {
			$inner_text = \sprintf(
				'<p>%s</p>',
				\esc_html__(
					'Good job! All your internal linking suggestions are up to date. These suggestions appear alongside your content when you are writing or editing. We will notify you the next time you need to update your internal linking suggestions.',
					'wordpress-seo-premium'
				)
			);
		}
		else {
			$progress = \sprintf(
				/* translators: 1: expands to a <span> containing the number of items recalculated. 2: expands to a <strong> containing the total number of items. */
				\esc_html__( 'Item %1$s of %2$s analyzed.', 'wordpress-seo-premium' ),
				'<span id="yoast-prominent-words-indexation-current-count">0</span>',
				\sprintf( '<strong id="yoast-prominent-words-indexation-total-count">%d</strong>', $this->total_unindexed )
			);

			$inner_text  = '<div id="yoast-prominent-words-indexation-progress-bar" class="wpseo-progressbar"></div>';
			$inner_text .= \sprintf( '<p>%s</p>', $progress );
		}

		$blocks[] = \sprintf(
			'<div><p>%s</p>%s</div>',
			\esc_html__( 'Generating suggestions for your content...', 'wordpress-seo-premium' ),
			$inner_text
		);

		return \sprintf(
			'<div id="yoast-prominent-words-indexation-wrapper" class="hidden">%s<button class="button yoast-indexation-stop" type="button">%s</button></div>',
			\implode( '<hr />', $blocks ),
			\esc_html__( 'Stop indexing', 'wordpress-seo-premium' )
		);
	}
}
