<?php

namespace ACA\EC\ImportListscreens;

use AC;

class Message implements AC\Registerable {

	/**
	 * @var ImportedSetting
	 */
	private $setting;

	public function __construct( ImportedSetting $setting ) {
		$this->setting = $setting;
	}

	public function register() {
		add_action( 'ac/settings/after_title', [ $this, 'display' ] );
	}

	public function display( AC\ListScreen $list_screen ) {
		if ( ! $list_screen instanceof AC\ListScreen\Post ) {
			return;
		}

		if ( ! in_array( $list_screen->get_post_type(), [ 'tribe_events', 'tribe_venue', 'tribe_organizer' ] ) ) {
			return;
		}

		if ( $this->setting->is_imported() ) {
			return;
		}

		$this->display_import_layout_message();
	}

	public function display_import_layout_message() {
		?>

		<div class="notice notice-success">
			<p>
				<?php printf( __( 'Enable our predefined column sets for %s?', 'codepress-admin-columns' ), __( 'The Events Calendar', 'the-events-calendar' ) ); ?>
				<a href="<?= add_query_arg( Controller::ACTION_KEY, Controller::IMPORT_METHOD_KEY ) ?>" class="notice__actionlink">Yes</a>
				<a href="<?= add_query_arg( Controller::ACTION_KEY, Controller::DISMISS_METHOD_KEY ) ?>" class="notice__actionlink">No thanks</a>
			</p>
		</div>

		<?php
	}

}