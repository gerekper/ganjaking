<?php

namespace ACP\Editing\Admin;

use AC\Admin\Tooltip;
use AC\Form\Element\Checkbox;
use AC\Renderable;
use AC\Settings\General;
use AC\Type\Url;
use ACP\Editing\Settings\CustomField;

class CustomFieldEditing implements Renderable {

	/**
	 * @var CustomField
	 */
	private $option;

	public function __construct() {
		$this->option = new CustomField();
	}

	/**
	 * @return Tooltip
	 */
	private function get_tooltip() {
		$content = sprintf(
			'<p>%s</p><p>%s</p>',
			__( 'Inline edit will display all the raw values in an editable text field.', 'codepress-admin-columns' ),
			sprintf(
				__( "Please read %s if you plan to use these fields.", 'codepress-admin-columns' ),
				sprintf(
					'<a href="%s" target="_blank">%s</a>',
					( new Url\Documentation( Url\Documentation::ARTICLE_ENABLE_EDITING ) )->get_url(),
					__( 'our documentation', 'codepress-admin-columns' )
				)
			)
		);

		return new Tooltip( $this->option->get_name(), [ 'content' => $content ] );
	}

	private function get_label() {
		return sprintf( '%s %s %s',
			__( 'Enable editing for Custom Fields.', 'codepress-admin-columns' ),
			sprintf(
				__( "Default is %s.", 'codepress-admin-columns' ),
				sprintf( '<code>%s</code>', __( 'off', 'codepress-admin-columns' ) )
			),
			$this->get_tooltip()->get_label()
		);
	}

	public function render() {
		$name = sprintf( '%s[%s]', General::NAME, $this->option->get_name() );

		$checkbox = new Checkbox( $name );

		$checkbox
			->set_options( [
				'1' => $this->get_label(),
			] )
			->set_value( $this->option->is_enabled() ? 1 : 0 );

		return $checkbox->render() . $this->get_tooltip()->get_instructions();
	}

}