<?php

namespace ACP\Table;

use AC\ListScreen;
use AC\Registrable;
use AC\Table\Screen;
use ACP\Settings\ListScreen\HideOnScreen;

final class HideFilters implements Registrable {

	public function register() {
		add_action( 'ac/admin_head', [ $this, 'hide_all_filters' ] );
		add_action( 'ac/admin_head', [ $this, 'hide_filter_media_items' ] );
		add_action( 'ac/table', [ $this, 'hide_filter' ] );
	}

	/**
	 * @param Screen $table
	 */
	public function hide_filter( $table ) {
		$list_screen = $table->get_list_screen();

		if ( ( new HideOnScreen\FilterPostDate() )->is_hidden( $list_screen ) ) {
			add_filter( 'disable_months_dropdown', '__return_true' );
		}

		if ( ( new HideOnScreen\FilterPostFormat() )->is_hidden( $list_screen ) ) {
			add_filter( 'disable_formats_dropdown', '__return_true' );
		}

		if ( ( new HideOnScreen\FilterCategory() )->is_hidden( $list_screen ) ) {
			add_filter( 'disable_categories_dropdown', '__return_true' );
		}
	}

	public function hide_filter_media_items( ListScreen $list_screen ) {
		if ( ( new HideOnScreen\FilterMediaItem() )->is_hidden( $list_screen ) ) {
			?>
			<style>
				select#attachment-filter {
					display: none !important;
				}
			</style>
			<?php
		}
	}

	public function hide_all_filters( ListScreen $list_screen ) {
		if ( ( new HideOnScreen\Filters() )->is_hidden( $list_screen ) ) {
			?>
			<style>
				[class="alignleft actions"] {
					display: none !important;
				}
			</style>
			<?php
		}
	}

}