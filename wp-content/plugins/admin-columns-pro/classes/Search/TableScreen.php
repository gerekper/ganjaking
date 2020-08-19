<?php

namespace ACP\Search;

use AC;
use AC\Asset\Enqueueable;
use AC\Registrable;
use AC\Request;
use ACP;
use ACP\Search\Settings\HideOnScreen;

abstract class TableScreen
	implements Registrable {

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var AC\ListScreen
	 */
	protected $list_screen;

	/**
	 * @var Addon
	 */
	protected $addon;

	/**
	 * @var Enqueueable[]
	 */
	protected $assets;

	/**
	 * @param Addon         $addon
	 * @param AC\ListScreen $list_screen
	 * @param Request       $request
	 * @param array         $assets
	 */
	public function __construct( Addon $addon, AC\ListScreen $list_screen, Request $request, array $assets ) {
		$this->addon = $addon;
		$this->list_screen = $list_screen;
		$this->request = $request;
		$this->assets = $assets;
	}

	public function register() {
		add_action( 'ac/table_scripts', [ $this, 'scripts' ] );
		add_action( 'admin_head', [ $this, 'hide_segments' ] );
		add_action( 'admin_footer', [ $this, 'add_segment_modal' ] );

		$this->register_query();
	}

	public function register_query() {
		$rules = $this->request->filter( 'ac-rules', [], FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( ! $rules ) {
			return;
		}

		$bindings = [];

		foreach ( $rules as $rule ) {
			$column = $this->list_screen->get_column_by_name( $rule['name'] );

			if ( ! $column ) {
				continue;
			}

			if ( ! $column instanceof Searchable || ! $column->search() ) {
				continue;
			}

			// Skip unsupported operators
			if ( false === $column->search()->get_operators()->search( $rule['operator'] ) ) {
				continue;
			}

			$bindings[] = $column->search()->get_query_bindings(
				$rule['operator'],
				new Value( $rule['value'], $rule['value_type'] )
			);
		}

		QueryFactory::create(
			$this->list_screen->get_meta_type(),
			$bindings
		)->register();
	}

	public function scripts() {
		foreach ( $this->assets as $asset ) {
			$asset->enqueue();
		}

		wp_enqueue_script( 'ac-select2' );
		wp_enqueue_style( 'ac-select2' );

		wp_enqueue_style( 'ac-jquery-ui' );
		wp_enqueue_style( 'wp-pointer' );
	}

	/**
	 * Display the markup on the current list screen
	 */
	public function filters_markup() {
		?>

		<div id="ac-s"></div>

		<?php
	}

	/**
	 * @return bool
	 */
	private function is_segment_hidden() {
		return ( new HideOnScreen\SavedFilters() )->is_hidden( $this->list_screen );
	}

	public function hide_segments() {
		if ( $this->is_segment_hidden() ) {
			?>
			<style type="text/css">
				.ac-button__segments {
					display: none !important;
				}

				.ac-button__add-filter {
					border-top-right-radius: 3px !important;
					border-bottom-right-radius: 3px !important;
				}
			</style>
			<?php
		}
	}

	public function add_segment_modal() {
		if ( $this->is_segment_hidden() ) {
			return;
		}

		$view = new AC\View( [
			'current_segment_id'       => $this->request->get( 'ac-segment' ),
			'user_can_manage_segments' => current_user_can( AC\Capabilities::MANAGE ),
		] );

		echo $view->set_template( 'table/segment-modal' )->render();
	}

}