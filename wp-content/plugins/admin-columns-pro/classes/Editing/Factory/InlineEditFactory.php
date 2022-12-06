<?php
declare( strict_types=1 );

namespace ACP\Editing\Factory;

use AC;
use ACP\Editing\Editable;
use ACP\Editing\HideOnScreen;
use ACP\Editing\ListScreen;
use ACP\Editing\Service;
use ACP\Editing\ServiceFactory;
use ACP\Editing\Settings;

class InlineEditFactory {

	/**
	 * @var AC\ListScreen;
	 */
	private $list_screen;

	public function __construct( AC\ListScreen $list_screen ) {
		$this->list_screen = $list_screen;
	}

	/**
	 * @return AC\Column[]
	 */
	public function create() {
		return $this->is_list_screen_editable()
			? array_filter( $this->list_screen->get_columns(), [ $this, 'is_column_inline_editable' ] )
			: [];
	}

	private function is_list_screen_editable() {
		if ( ! $this->list_screen instanceof ListScreen ) {
			return false;
		}

		$strategy = $this->list_screen->editing();

		if ( ! $strategy->user_can_edit() ) {
			return false;
		}

		$option = new HideOnScreen\InlineEdit();

		return ! $option->is_hidden( $this->list_screen );
	}

	public function is_column_inline_editable( AC\Column $column ) {
		if ( ! $column instanceof Editable ) {
			return false;
		}

		$service = ServiceFactory::create( $column );

		if ( ! $service ) {
			return false;
		}

		if ( ! $service->get_view( Service::CONTEXT_SINGLE ) ) {
			return false;
		}

		$setting = $column->get_setting( Settings::NAME );

		return $setting instanceof Settings && $setting->is_active();
	}

}