<?php

namespace ACP\Sorting\Admin;

use AC\Admin\Tooltip;
use AC\Form\Element\Checkbox;
use AC\Renderable;
use AC\Settings\General;
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
		return sprintf( '%s %s %s',
			__( "Show all results when sorting.", 'codepress-admin-columns' ),
			sprintf( __( "Default is %s.", 'codepress-admin-columns' ), '<code>' . __( 'off', 'codepress-admin-columns' ) . '</code>' ),
			$this->get_tooltip()->get_label()
		);
	}

	public function render() {
		$name = sprintf( '%s[%s]', General::NAME, $this->option->get_name() );

		$checkbox = new Checkbox( $name );

		$checkbox->set_options( [ '1' => $this->get_label() ] )
		         ->set_value( $this->option->is_enabled() ? 1 : 0 );

		return $checkbox->render() . $this->get_tooltip()->get_instructions();
	}

	private function get_tooltip() {
		$content = sprintf(
			'<p>%s</p><p>%s</p><p>%s</p>',
			__( 'As a default, when sorting a list table by a column it will exclude items where its value is empty.', 'codepress-admin-columns' ),
			__( "By enabling the setting Show all results when sorting we can include empty values in our results.", 'codepress-admin-columns' ),
			sprintf(
				'<a href="%s" target="_blank">%s</a>',
				( new Documentation( Documentation::ARTICLE_SHOW_ALL_SORTING_RESULTS ) )->get_url(),
				__( 'Read more &raquo;', 'codepress-admin-columns' )
			)
		);

		return new Tooltip( $this->option->get_name(), [ 'content' => $content ] );
	}

}