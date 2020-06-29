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
		?>
		<div class="ac-segments" data-initial="<?= $this->request->get( 'ac-segment' ); ?>">
			<div class="ac-segments__create">
				<span class="cpac_icons-segment"></span>
				<button class="button button-primary">
					<?php _e( 'Save Filters', 'codepress-admin-columns' ); ?>
				</button>
			</div>
			<div class="ac-segments__list">
			</div>
			<div class="ac-segments__instructions" rel="pointer-segments">
				<?php _e( 'Instructions', 'codepress-admin-columns' ); ?>
				<div id="ac-segments-instructions" style="display:none;">
					<h3><?php _e( 'Instructions', 'codepress-admin-columns' ); ?></h3>
					<p>
						<?php _e( 'Save a set of custom smart filters for later use.', 'codepress-admin-columns' ); ?>
					</p>
					<p>
						<?php _e( 'This can be useful to group your WordPress content based on different criteria.', 'codepress-admin-columns' ); ?>&nbsp;<?php _e( 'Click on a segment to load the filtered list.', 'codepress-admin-columns' ); ?>
					</p>
				</div>
			</div>

		</div>
		<div class="ac-modal" id="ac-modal-create-segment">
			<div class="ac-modal__dialog -create-segment">
				<form id="frm_create_segment">
					<div class="ac-modal__dialog__header">
						<?php _e( 'Save Filters', 'codepress-admin-columns' ); ?>
						<button class="ac-modal__dialog__close">
							<span class="dashicons dashicons-no"></span>
						</button>
					</div>
					<div class="ac-modal__dialog__content">
						<label for="inp_segment_name"><?php _e( 'Name', 'codepress-admin-columns' ); ?></label>
						<input type="text" name="segment_name" id="inp_segment_name" required autocomplete="off">
						<div class="ac-modal__error">
						</div>
					</div>
					<div class="ac-modal__dialog__footer">
						<div class="ac-modal__loading">
							<span class="dashicons dashicons-update"></span>
						</div>
						<button class="button button" data-dismiss="modal"><?php _e( 'Cancel' ); ?></button>
						<button type="submit" class="button button-primary"><?php _e( 'Save', 'codepress-admin-columns' ); ?></button>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

}