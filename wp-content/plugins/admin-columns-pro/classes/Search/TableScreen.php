<?php

namespace ACP\Search;

use AC;
use AC\Asset\Enqueueable;
use AC\Registrable;
use ACP;
use ACP\Search\Settings\HideOnScreen;

abstract class TableScreen implements Registrable {

	/**
	 * @var AC\ListScreen
	 */
	protected $list_screen;

	/**
	 * @var Enqueueable[]
	 */
	protected $assets;

	public function __construct( AC\ListScreen $list_screen, array $assets ) {
		$this->list_screen = $list_screen;
		$this->assets = $assets;
	}

	public function register() {
		add_action( 'ac/table_scripts', [ $this, 'scripts' ] );
		add_action( 'admin_head', [ $this, 'hide_segments' ] );
		add_action( 'admin_footer', [ $this, 'add_segment_modal' ] );
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
			'user_can_manage_segments' => current_user_can( AC\Capabilities::MANAGE ),
		] );

		echo $view->set_template( 'table/segment-modal' )->render();
	}

}