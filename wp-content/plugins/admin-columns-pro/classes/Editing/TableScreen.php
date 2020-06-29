<?php

namespace ACP\Editing;

use AC;
use AC\Asset\Enqueueable;
use AC\Preferences;
use ACP\Editing;
use LogicException;

class TableScreen implements AC\Registrable {

	/**
	 * @var AC\ListScreen
	 */
	protected $list_screen;

	/**
	 * @var Enqueueable[]
	 */
	protected $assets;

	/**
	 * @var Preference\EditState
	 */
	protected $edit_state;

	/**
	 * @param AC\ListScreen    $list_screen
	 * @param Enqueueable[]    $assets
	 * @param Preferences\Site $editing_state
	 */
	public function __construct( AC\ListScreen $list_screen, array $assets, Preference\EditState $edit_state ) {
		if ( ! $list_screen instanceof Editing\ListScreen ) {
			throw new LogicException( 'ListScreen should be of type Editing\ListScreen.' );
		}

		$this->list_screen = $list_screen;
		$this->assets = $assets;
		$this->edit_state = $edit_state;
	}

	public function register() {
		add_action( 'ac/table_scripts', [ $this, 'scripts' ] );
		add_action( 'ac/table/actions', [ $this, 'edit_button' ] );
	}

	public function scripts() {
		foreach ( $this->assets as $asset ) {
			$asset->enqueue();
		}

		// Select 2
		wp_enqueue_script( 'ac-select2' );
		wp_enqueue_style( 'ac-select2' );

		// WP Media picker
		wp_enqueue_media();
		wp_enqueue_style( 'ac-jquery-ui' );

		// WP Color picker
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );

		// WP Content Editor
		wp_enqueue_editor();

		do_action( 'ac/table_scripts/editing', $this->list_screen );
	}

	public function edit_button() {
		if ( ! $this->list_screen->has_id() ) {
			return;
		}
		?>
		<label class="ac-table-button -toggle -iedit">
			<span class="ac-toggle">
				<input type="checkbox" value="1" id="acp-enable-editing" <?php checked( $this->edit_state->is_active( $this->list_screen->get_key() ) ); ?>>
				<span class="ac-toggle__switch">
					<svg class="ac-toggle__switch__on" width="2" height="6" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6"><path fill="#fff" d="M0 0h2v6H0z"></path></svg>
					<svg class="ac-toggle__switch__off" width="6" height="6" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6"><path fill="#fff" d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path></svg>
					<span class="ac-toggle__switch__track"></span>
				</span>
				<?php _e( 'Inline Edit', 'codepress-admin-columns' ); ?>
			</span>
		</label>
		<?php
	}

}