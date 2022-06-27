<?php

namespace ACP\Table;

use AC\ListScreen;
use AC\Registrable;
use ACP\Search;
use ACP\Settings\ListScreen\HideOnScreen;

final class HideElements implements Registrable {

	public function register() {
		add_action( 'ac/table_scripts', [ $this, 'hide_elements' ] );
	}

	public function hide_elements( ListScreen $list_screen ) {
		$hidden_elements = [];

		if ( ( new HideOnScreen\FilterMediaItem() )->is_hidden( $list_screen ) ) {
			$hidden_elements[] = new HideElement\FilterMediaItems();
		}

		if ( ( new HideOnScreen\FilterPostDate() )->is_hidden( $list_screen ) ) {
			$hidden_elements[] = new HideElement\FilterPostDate();
		}

		if ( ( new HideOnScreen\FilterPostFormat() )->is_hidden( $list_screen ) ) {
			$hidden_elements[] = new HideElement\FilterPostFormats();
		}

		if ( ( new HideOnScreen\FilterCategory() )->is_hidden( $list_screen ) ) {
			$hidden_elements[] = new HideElement\FilterPostCategories();
		}

		if ( ( new HideOnScreen\FilterCommentType() )->is_hidden( $list_screen ) ) {
			$hidden_elements[] = new HideElement\FilterCommentTypes();
		}

		if ( ( new HideOnScreen\Search() )->is_hidden( $list_screen ) ) {
			$hidden_elements[] = new HideElement\Search( $list_screen );
		}

		if ( ( new HideOnScreen\BulkActions() )->is_hidden( $list_screen ) ) {
			$hidden_elements[] = new HideElement\BulkActions();
		}

		if ( ( new HideOnScreen\RowActions() )->is_hidden( $list_screen ) ) {
			$hidden_elements[] = new HideElement\RowActions( $list_screen );
		}

		if ( ( new HideOnScreen\SubMenu( '' ) )->is_hidden( $list_screen ) ) {
			$hidden_elements[] = new HideElement\SubMenu();
		}

		$filters = new HideOnScreen\Filters();

		if ( $filters->is_hidden( $list_screen ) ) {
			$hidden_elements[] = new HideElement\Filters();
		}

		$smart_filters = new Search\Settings\HideOnScreen\SmartFilters();

		if ( $smart_filters->is_hidden( $list_screen ) && $filters->is_hidden( $list_screen ) ) {
			$hidden_elements[] = new HideElement\ActionsBar();
		}

		foreach ( $hidden_elements as $hidden_element ) {
			$hidden_element->hide();
		}
	}

}