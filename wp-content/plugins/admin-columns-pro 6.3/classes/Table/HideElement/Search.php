<?php

namespace ACP\Table\HideElement;

use AC\ListScreen;
use ACP\Table\HideElement;

class Search implements HideElement {

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	public function __construct( ListScreen $list_screen ) {
		$this->list_screen = $list_screen;
	}

	public function hide() {
		add_action( 'ac/admin_head', [ $this, 'render' ] );
	}

	public function render() {
		?>
		<style>
			<?= sprintf( '%s { display: none; }', $this->get_search_selector() ); ?>
		</style>
		<?php
	}

	private function get_search_selector() {
		switch ( true ) {
			case $this->list_screen instanceof ListScreen\Media :
				return '.wrap form#posts-filter div.search-form';
			case $this->list_screen instanceof ListScreen\Post :
				return '.wrap form#posts-filter p.search-box';
			default :
				return 'p.search-box';
		}
	}

}