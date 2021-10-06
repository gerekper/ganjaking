<?php

namespace ACP\Column\Media;

use AC;
use ACP\Editing;
use ACP\Editing\Settings\EditableType;
use ACP\Editing\Storage;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

class Description extends AC\Column\Media\Description
	implements Editing\Editable, Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function register_settings() {
		parent::register_settings();

		$this->add_setting( ( new Editing\Settings\Factory\EditableType( $this, Editing\Settings\Factory\EditableType::TYPE_CONTENT ) )->create() );
	}

	public function editing() {
		$view = EditableType\Content::TYPE_WYSIWYG === $this->get_inline_editable_type()
			? new Editing\View\Wysiwyg()
			: new Editing\View\TextArea();

		return new Editing\Service\Basic(
			$view,
			new Storage\Post\Field( 'post_content' )
		);
	}

	public function filtering() {
		return new Filtering\Model\Post\Content( $this );
	}

	public function sorting() {
		return new Sorting\Model\Post\PostField( 'post_content' );
	}

	public function search() {
		return new Search\Comparison\Post\Content();
	}

	private function get_inline_editable_type() {
		$setting = $this->get_setting( Editing\Settings::NAME );

		if ( ! $setting instanceof Editing\Settings ) {
			return null;
		}

		$section = $setting->get_section( EditableType\Content::NAME );

		return $section instanceof EditableType\Content
			? $section->get_editable_type()
			: null;
	}
}