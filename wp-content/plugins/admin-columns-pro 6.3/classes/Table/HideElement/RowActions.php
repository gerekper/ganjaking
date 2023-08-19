<?php

namespace ACP\Table\HideElement;

use AC\ListScreen;
use ACP\ListScreen\MSUser;
use ACP\ListScreen\Taxonomy;
use ACP\Table\HideElement;

class RowActions implements HideElement {

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	public function __construct( ListScreen $list_screen ) {
		$this->list_screen = $list_screen;
	}

	public function hide() {
		switch ( true ) {
			case $this->list_screen instanceof ListScreen\Post :
				if ( is_post_type_hierarchical( $this->list_screen->get_post_type() ) ) {
					add_filter( 'page_row_actions', '__return_empty_array', 10000 );

					return;
				}
				add_filter( 'post_row_actions', '__return_empty_array', 10000 );

				return;
			case $this->list_screen instanceof ListScreen\Media :
				add_filter( 'media_row_actions', '__return_empty_array', 10000 );

				return;
			case $this->list_screen instanceof MSUser :
				add_filter( 'ms_user_row_actions', '__return_empty_array', 10000 );

				return;
			case $this->list_screen instanceof ListScreen\User :
				add_filter( 'user_row_actions', '__return_empty_array', 10000 );

				return;
			case $this->list_screen instanceof Taxonomy :
				add_filter( $this->list_screen->get_taxonomy() . "_row_actions", '__return_empty_array', 10000 );

				return;
			case $this->list_screen instanceof ListScreen\Comment :
				add_filter( 'comment_row_actions', '__return_empty_array', 10000 );

				return;
		}
	}

}