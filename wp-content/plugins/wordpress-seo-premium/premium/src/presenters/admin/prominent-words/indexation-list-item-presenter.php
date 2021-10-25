<?php

namespace Yoast\WP\SEO\Presenters\Admin\Prominent_Words;

use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Represents the list item presenter for the prominent words indexation.
 *
 * @package Yoast\WP\SEO\Presentations\Admin
 */
class Indexation_List_Item_Presenter extends Abstract_Presenter {

	/**
	 * The number of objects that need to be reindexed.
	 *
	 * @var int
	 */
	protected $total_unindexed;

	/**
	 * Prominent_Words_Indexation_List_Item_Presenter constructor.
	 *
	 * @param int $total_unindexed The number of objects that need to be indexed.
	 */
	public function __construct( $total_unindexed ) {
		$this->total_unindexed = $total_unindexed;
	}

	/**
	 * Returns the output as string.
	 *
	 * @return string The output.
	 */
	public function present() {
		$output = '<li><strong>' . \esc_html__( 'Internal linking', 'wordpress-seo-premium' ) . '</strong><br/>';

		if ( $this->total_unindexed === 0 ) {
			$output .= '<span class="wpseo-checkmark-ok-icon"></span>' . \esc_html__( 'Good job! All your internal linking suggestions are up to date. These suggestions appear alongside your content when you are writing or editing. We will notify you the next time you need to update your internal linking suggestions.', 'wordpress-seo-premium' );
		}

		if ( $this->total_unindexed > 0 ) {
			$analyze_button = \sprintf(
				'<button type="button" class="button yoast-open-indexation" data-title="%1$s" data-settings="yoastProminentWordsIndexationData">%2$s</button>',
				\esc_attr__( 'Generating internal linking suggestions', 'wordpress-seo-premium' ),
				\esc_html__( 'Analyze your content', 'wordpress-seo-premium' )
			);

			$output .= \sprintf(
				'<span id="yoast-prominent-words-indexation"><p>%s</p>%s</span>',
				\esc_html__( 'Some content on your website is not yet analyzed with the currently installed version of Internal linking suggestions. For the best results, it\'s always a good idea to keep your linking suggestions up to date. Please note that depending on how much content need to be analyzed it can take different amounts of time. But don\'t worry, you can always stop and resume the analysis later.', 'wordpress-seo-premium' ),
				$analyze_button
			);
		}

		$output .= '</li>';

		return $output;
	}
}
