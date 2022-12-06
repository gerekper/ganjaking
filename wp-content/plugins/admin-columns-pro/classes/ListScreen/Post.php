<?php

namespace ACP\ListScreen;

use AC;
use ACP\Column;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Sorting;

class Post extends AC\ListScreen\Post implements Sorting\ListScreen, Editing\ListScreen, Filtering\ListScreen, Export\ListScreen, Editing\BulkDelete\ListScreen {

	public function sorting( $model ) {
		return new Sorting\Strategy\Post( $model, $this->get_post_type() );
	}

	public function deletable() {
		return new Editing\BulkDelete\Deletable\Post( get_post_type_object( $this->get_post_type() ) );
	}

	public function editing() {
		return new Editing\Strategy\Post( $this->get_post_type() );
	}

	public function filtering( $model ) {
		return new Filtering\Strategy\Post( $model );
	}

	public function export() {
		return new Export\Strategy\Post( $this );
	}

	protected function register_column_types() {
		parent::register_column_types();

		$this->register_column_types_from_list( [
			Column\CustomField::class,
			Column\Actions::class,
			Column\Post\Ancestors::class,
			Column\Post\Attachment::class,
			Column\Post\Author::class,
			Column\Post\AuthorName::class,
			Column\Post\BeforeMoreTag::class,
			Column\Post\Categories::class,
			Column\Post\ChildPages::class,
			Column\Post\CommentCount::class,
			Column\Post\Comments::class,
			Column\Post\CommentStatus::class,
			Column\Post\Content::class,
			Column\Post\Date::class,
			Column\Post\DatePublished::class,
			Column\Post\Depth::class,
			Column\Post\EstimateReadingTime::class,
			Column\Post\Excerpt::class,
			Column\Post\FeaturedImage::class,
			Column\Post\Formats::class,
			Column\Post\GutenbergBlocks::class,
			Column\Post\HasTerm::class,
			Column\Post\ID::class,
			Column\Post\Images::class,
			Column\Post\LastModifiedAuthor::class,
			Column\Post\LatestComment::class,
			Column\Post\LinkCount::class,
			Column\Post\Menu::class,
			Column\Post\Modified::class,
			Column\Post\Order::class,
			Column\Post\PageTemplate::class,
			Column\Post\PasswordProtected::class,
			Column\Post\Path::class,
			Column\Post\Permalink::class,
			Column\Post\PingStatus::class,
			Column\Post\PostParent::class,
			Column\Post\PostType::class,
			Column\Post\PostVisibility::class,
			Column\Post\Revisions::class,
			Column\Post\Shortcode::class,
			Column\Post\Shortcodes::class,
			Column\Post\Slug::class,
			Column\Post\Status::class,
			Column\Post\Sticky::class,
			Column\Post\Tags::class,
			Column\Post\Taxonomy::class,
			Column\Post\Title::class,
			Column\Post\TitleRaw::class,
			Column\Post\WordCount::class,
		] );
	}

}