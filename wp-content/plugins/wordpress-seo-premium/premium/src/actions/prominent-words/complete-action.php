<?php

namespace Yoast\WP\SEO\Actions\Prominent_Words;

use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Action for completing prominent words indexation.
 *
 * @package Yoast\WP\SEO\Actions\Prominent_Words
 */
class Complete_Action {

	/**
	 * Represents the options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * Complete_Action constructor.
	 *
	 * @param Options_Helper $options_helper Options helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Sets the indexation state to complete.
	 */
	public function complete() {
		$this->options_helper->set( 'prominent_words_indexation_completed', true );

		\set_transient( 'total_unindexed_prominent_words', '0' );
	}
}
