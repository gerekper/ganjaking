<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Promo popup class.
 *
 * @since   7.3
 */
class Vc_Promo_Popup {
	/**
	 * Render UI template.
	 */
	public function render_ui_template() {
		$user_id = get_current_user_id();

		// TODO: Remove this condition after 7.3.1 release.
		if ( '7.3' === WPB_VC_VERSION && ! get_user_meta( $user_id, '_vc_editor_promo_popup', true ) ) {
			update_user_meta( $user_id, '_vc_editor_promo_popup', WPB_VC_VERSION );
			vc_include_template( 'editors/popups/promo/promo-popup.tpl.php' );
			return;
		}

		$is_transient = (bool) get_transient( '_vc_editor_promo_popup' );
		$user_meta = get_user_meta( $user_id, '_vc_editor_promo_popup', true );
		$is_popup = $is_transient && WPB_VC_VERSION !== $user_meta;
		if ( empty( $is_popup ) ) {
			return;
		}

		vc_include_template( 'editors/popups/promo/promo-popup.tpl.php' );
		update_user_meta( $user_id, '_vc_editor_promo_popup', WPB_VC_VERSION );
	}
}
