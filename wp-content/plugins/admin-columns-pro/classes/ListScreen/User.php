<?php

namespace ACP\ListScreen;

use AC;
use ACP\Column;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Sorting;

class User extends AC\ListScreen\User
	implements Sorting\ListScreen, Editing\ListScreen, Filtering\ListScreen, Export\ListScreen, Editing\BulkDelete\ListScreen {

	public function sorting( $model ) {
		return new Sorting\Strategy\User( $model );
	}

	public function deletable() {
		return new Editing\BulkDelete\Deletable\User();
	}

	public function editing() {
		return new Editing\Strategy\User();
	}

	public function filtering( $model ) {
		return new Filtering\Strategy\User( $model );
	}

	public function export() {
		return new Export\Strategy\User( $this );
	}

	protected function register_column_types() {
		parent::register_column_types();

		$this->register_column_types_from_list( [
			Column\CustomField::class,
			Column\Actions::class,
			Column\User\CommentCount::class,
			Column\User\Description::class,
			Column\User\DisplayName::class,
			Column\User\Email::class,
			Column\User\FirstName::class,
			Column\User\FirstPost::class,
			Column\User\FullName::class,
			Column\User\Gravatar::class,
			Column\User\ID::class,
			Column\User\Language::class,
			Column\User\LastName::class,
			Column\User\LastPost::class,
			Column\User\Login::class,
			Column\User\Name::class,
			Column\User\Nicename::class,
			Column\User\Nickname::class,
			Column\User\PostCount::class,
			Column\User\Posts::class,
			Column\User\Registered::class,
			Column\User\RichEditing::class,
			Column\User\Role::class,
			Column\User\Roles::class,
			Column\User\ShowToolbar::class,
			Column\User\Url::class,
			Column\User\Username::class,
			Column\User\UserPosts::class,
		] );
	}

}