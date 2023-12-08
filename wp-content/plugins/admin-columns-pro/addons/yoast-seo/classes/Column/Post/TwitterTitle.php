<?php

namespace ACA\YoastSeo\Column\Post;

use AC;
use ACA\YoastSeo\Editing;
use ACA\YoastSeo\Search;
use ACP;

class TwitterTitle extends AC\Column\Meta
	implements ACP\Editing\Editable, ACP\Search\Searchable, ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_group( 'yoast-seo' )
		     ->set_label( __( 'Twitter Title', 'codepress-admin-columns' ) )
		     ->set_type( 'column-yoast_twitter_title' );
	}

	public function get_meta_key() {
		return '_yoast_wpseo_twitter-title';
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			( new ACP\Editing\View\Text() )->set_clear_button( true ),
			new ACP\Editing\Storage\Meta( $this->get_meta_key(), new AC\MetaType( AC\MetaType::POST ) )
		);
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Text( $this->get_meta_key() );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key() );
	}
}