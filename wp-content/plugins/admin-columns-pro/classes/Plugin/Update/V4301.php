<?php

namespace ACP\Plugin\Update;

use AC\Plugin\Update\V3201;
use AC\Storage;
use Exception;

class V4301 extends V3201 {

	/**
	 * @throws Exception
	 */
	public function apply_update() {
		$this->uppercase_class_files( ACP()->get_dir() . '/classes' );
		$this->update_notice_preference_renewal();
	}

	protected function set_version() {
		$this->version = '4.3.1';
	}

	/**
	 * @throws Exception
	 */
	private function update_notice_preference_renewal() {
		$phase_key = 'cpac_hide_license_notice_phase';
		$timeout_key = 'cpac_hide_license_notice_timeout';

		foreach ( $this->get_users_by_meta_key( $phase_key ) as $user_id ) {

			$phase = get_user_meta( $user_id, $phase_key, true );
			$timeout = get_user_meta( $user_id, $timeout_key, true );

			if ( ! $timeout ) {
				$timeout = time();
			}

			switch ( $phase ) {
				case '0':
					$option = new Storage\Timestamp(
						new Storage\UserMeta( 'ac_notice_dismiss_renewal_1', $user_id )
					);
					$option->save( time() + ( MONTH_IN_SECONDS * 3 ) );

					break;
				case '1':
					$option = new Storage\Timestamp(
						new Storage\UserMeta( 'ac_notice_dismiss_renewal_2', $user_id )
					);
					$option->save( time() + ( MONTH_IN_SECONDS * 3 ) );

					break;
				default: // completed or not set
					$option = new Storage\Timestamp(
						new Storage\UserMeta( 'ac_notice_dismiss_expired', $user_id )
					);

					$option->save( $timeout + ( MONTH_IN_SECONDS * 3 ) );
			}

			delete_user_meta( $user_id, $phase_key );
			delete_user_meta( $user_id, $timeout_key );
		}
	}

}