<?php

namespace ACP\QuickAdd\Table;

use AC\Asset\Location;
use AC\Registrable;
use AC\Table;
use ACP\QuickAdd\Admin\HideOnScreen;
use ACP\QuickAdd\Filter;
use ACP\QuickAdd\Model;
use ACP\QuickAdd\Table\Checkbox\ShowButton;
use ACP\QuickAdd\Table\Preference;

class Loader implements Registrable {

	/**
	 * @var Location\Absolute
	 */
	private $location;

	/**
	 * @var HideOnScreen\QuickAdd
	 */
	private $hide_on_screen;

	/**
	 * @var Preference\ShowButton
	 */
	private $preference;

	/**
	 * @var Filter
	 */
	private $filter;

	public function __construct( Location $location, HideOnScreen\QuickAdd $hide_on_screen, Preference\ShowButton $preference, Filter $filter ) {
		$this->location = $location;
		$this->hide_on_screen = $hide_on_screen;
		$this->preference = $preference;
		$this->filter = $filter;
	}

	public function register() {
		add_action( 'ac/table', [ $this, 'load' ] );
	}

	public function load( Table\Screen $table_screen ) {
		$list_screen = $table_screen->get_list_screen();

		if ( ! $list_screen ) {
			return;
		}

		if ( ! $this->filter->match( $list_screen ) ) {
			return;
		}

		$model = Model\Factory::create( $list_screen );

		if ( ! $model || ! $model->has_permission( wp_get_current_user() ) ) {
			return;
		}

		if ( $this->hide_on_screen->is_hidden( $list_screen ) ) {
			return;
		}

		$table_screen->register_screen_option( new ShowButton( $this->preference->is_active( $list_screen->get_key() ) ? 1 : 0 ) );

		$script = new Script\AddNewInline( __( 'Quick Add', 'codepress-admin-columns' ), 'aca-add-new-inline', $this->location->with_suffix( 'assets/add-new-inline/js/table.js' ) );
		$script->enqueue();
	}

}