<?php

namespace ACA\ACF\Service;

use AC\ListScreen;
use AC\Message;
use AC\Message\InlineMessage;
use AC\Registerable;
use ACA\ACF\Nonce\UpdateDeprecatedNonce;
use ACA\ACF\Utils\V2ToV3Migration;

/**
 * Class that converts the old ACF column to the new V3 version.
 * This is useful when the migration script (update) cannot be performed on read-only list screens.
 * Inline Editing is currently not support for the migrated columns
 */
class LegacyColumnMapper implements Registerable {

	public function register(): void
    {
		add_action( 'ac/table/list_screen', [ new V2ToV3Migration(), 'migrate_list_screen_settings' ], 9 );
		add_action( 'ac/settings/notice', [ $this, 'render_deprecated_columns_notice' ] );
	}

	private function has_deprecated_columns( ListScreen $list_screen ) {
		$deprecated_columns = array_filter( $list_screen->get_settings(), function ( $setting ) {
			return isset( $setting['type'] ) && 'column-acf_field' === $setting['type'];
		} );

		return count( $deprecated_columns ) > 0;
	}

	public function render_deprecated_columns_notice( ListScreen $list_screen ) {
		if ( ! $list_screen->is_read_only() && $this->has_deprecated_columns( $list_screen ) ) {
			ob_start();
			?>
			<div class="aca-acf-deprecated-columns-notice">
				<p>
					<?= __( 'These settings contain at least one deprecated ACF column.', 'codepress-admin-columns' ); ?>
					<?= __( 'You can automatically update these columns to the latest version.', 'codepress-admin-columns' ); ?>
				</p>
				<form method="post">
					<?= ( new UpdateDeprecatedNonce() )->create_field(); ?>
					<input type="hidden" name="migrate_list_screen_id" value="<?= $list_screen->get_id()->get_id() ?>"/>
					<button type="submit" class="button" name="action" value="aca-acf-map-legacy-list-screen"><?= __( 'Update Now', 'codepress-admin-columns' ); ?></button>
				</form>
			</div>
			<?php
			$message = ob_get_clean();

			$notice = new InlineMessage( $message, Message::WARNING );

			echo $notice->render();
		}
	}

}