<?php

namespace ACP\ListScreen;

use AC;
use ACP\Column;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Sorting;

class Comment extends AC\ListScreen\Comment
	implements Sorting\ListScreen, Editing\ListScreen, Filtering\ListScreen, Export\ListScreen, Editing\BulkDelete\ListScreen {

	public function sorting( $model ) {
		return new Sorting\Strategy\Comment( $model );
	}

	public function deletable() {
		return new Editing\BulkDelete\Deletable\Comment();
	}

	public function editing() {
		return new Editing\Strategy\Comment();
	}

	public function filtering( $model ) {
		return new Filtering\Strategy\Comment( $model );
	}

	public function export() {
		return new Export\Strategy\Comment( $this );
	}

	protected function register_column_types() {
		parent::register_column_types();

		$this->register_column_types_from_list( [
			Column\CustomField::class,
			Column\Actions::class,
			Column\Comment\Agent::class,
			Column\Comment\Approved::class,
			Column\Comment\Author::class,
			Column\Comment\AuthorAvatar::class,
			Column\Comment\AuthorEmail::class,
			Column\Comment\AuthorIP::class,
			Column\Comment\AuthorName::class,
			Column\Comment\AuthorUrl::class,
			Column\Comment\Comment::class,
			Column\Comment\Date::class,
			Column\Comment\DateGmt::class,
			Column\Comment\Excerpt::class,
			Column\Comment\HasReplies::class,
			Column\Comment\ID::class,
			Column\Comment\IsReply::class,
			Column\Comment\Post::class,
			Column\Comment\PostType::class,
			Column\Comment\ReplyTo::class,
			Column\Comment\Response::class,
			Column\Comment\Status::class,
			Column\Comment\Type::class,
			Column\Comment\User::class,
			Column\Comment\WordCount::class,
		] );
	}

}