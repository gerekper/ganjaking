<?php

namespace ACP\QuickAdd\Admin;

use AC\ListScreen;
use AC\Registerable;
use ACP\QuickAdd\Filter;
use ACP\QuickAdd\Model\Factory;
use ACP\Settings\ListScreen\HideOnScreenCollection;
use ACP\Type\HideOnScreen\Group;

class Settings implements Registerable {

	/**
	 * @var Filter
	 */
	private $filter;

	public function __construct( Filter $filter ) {
		$this->filter = $filter;
	}

	public function register(): void
    {
		add_action( 'acp/admin/settings/hide_on_screen', [ $this, 'add_hide_on_screen' ], 10, 2 );
	}

	public function add_hide_on_screen( HideOnScreenCollection $collection, ListScreen $list_screen ) {
		if ( ! $this->filter->match( $list_screen ) ) {
			return;
		}

		$model = Factory::create( $list_screen );

		if ( ! $model || ! $model->has_permission( wp_get_current_user() ) ) {
			return;
		}

		$collection->add( new HideOnScreen\QuickAdd(), new Group( Group::FEATURE ), 60 );
	}

}