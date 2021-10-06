<?php

namespace ACP\Sorting\Admin;

use AC\Admin\Tooltip;
use AC\Form\Element\Toggle;
use AC\Renderable;
use AC\Type\Url\Documentation;
use ACP\Sorting\Settings\AllResults;

class ShowAllResults implements Renderable {

	/**
	 * @var AllResults
	 */
	private $option;

	public function __construct() {
		$this->option = new AllResults();
	}

	private function get_label() {
		return sprintf( '%s %s',
			__( "Include empty values in sorting results.", 'codepress-admin-columns' ),
			$this->get_tooltip()->get_label()
		);
	}

	public function render() {
		$toggle = new Toggle( $this->option->get_name(), $this->get_label(), $this->option->is_enabled() );
		$toggle->set_attribute( 'data-ajax-setting', $this->option->get_name() );

		return $toggle->render() . $this->get_tooltip()->get_instructions();
	}

	private function get_tooltip() {
		$content = sprintf(
			'<p>%s</p><p>%s</p><p>%s</p>',
			__( 'As a default, when sorting a list table by a column it will exclude items where its value is empty.', 'codepress-admin-columns' ),
			__( "By enabling this setting the sorting results will include empty values.", 'codepress-admin-columns' ),
			sprintf(
				'<a href="%s" target="_blank">%s</a>',
				( new Documentation( Documentation::ARTICLE_SHOW_ALL_SORTING_RESULTS ) )->get_url(),
				__( 'Read more &raquo;', 'codepress-admin-columns' )
			)
		);

		return new Tooltip( $this->option->get_name(), [ 'content' => $content ] );
	}

}