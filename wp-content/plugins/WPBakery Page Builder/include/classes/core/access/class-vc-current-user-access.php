<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'CORE_DIR', 'access/class-vc-role-access.php' );

/**
 * Class Vc_User_Access
 */
class Vc_Current_User_Access extends Vc_Role_Access {
	/**
	 * @param $part
	 *
	 * @return Vc_Current_User_Access_Controller;
	 */
	public function part( $part ) {
		if ( ! isset( $this->parts[ $part ] ) ) {
			require_once vc_path_dir( 'CORE_DIR', 'access/class-vc-current-user-access-controller.php' );
			/** @var Vc_Current_User_Access_Controller $user_access_controller */
			$this->parts[ $part ] = new Vc_Current_User_Access_Controller( $part );
		}
		/** @var Vc_Current_User_Access_Controller $user_access_controller */
		$user_access_controller = $this->parts[ $part ];
		// we also check for user "logged_in" status
		$is_user_logged_in = function_exists( 'is_user_logged_in' ) && is_user_logged_in();
		$user_access_controller->setValidAccess( $is_user_logged_in && $this->getValidAccess() ); // send current status to upper level
		$this->setValidAccess( true ); // reset

		return $user_access_controller;
	}

	/**
	 * @param $method
	 * @param $valid
	 * @param $argsList
	 * @return $this
	 */
	public function wpMulti( $method, $valid, $argsList ) {
		if ( $this->getValidAccess() ) {
			$access = ! $valid;
			foreach ( $argsList as &$args ) {
				if ( ! is_array( $args ) ) {
					$args = array( $args );
				}
				array_unshift( $args, 'current_user_can' );
				$this->setValidAccess( true );
				call_user_func_array( array(
					$this,
					$method,
				), $args );
				if ( $valid === $this->getValidAccess() ) {
					$access = $valid;
					break;
				}
			}
			$this->setValidAccess( $access );
		}

		return $this;
	}

	/**
	 * Check WordPress capability. Should be valid one cap at least.
	 *
	 * @return Vc_Current_User_Access
	 */
	public function wpAny() {
		if ( $this->getValidAccess() ) {
			$args = func_get_args();
			$this->wpMulti( 'check', true, $args );
		}

		return $this;
	}

	/**
	 * Check WordPress capability. Should be valid all caps.
	 *
	 * @return Vc_Current_User_Access
	 */
	public function wpAll() {
		if ( $this->getValidAccess() ) {
			$args = func_get_args();
			$this->wpMulti( 'check', false, $args );
		}

		return $this;
	}

	public function canEdit( $id ) {
		// @codingStandardsIgnoreStart
		$post = get_post( $id );
		if ( ! $post ) {
			$this->setValidAccess( false );

			return $this;
		}
		if ( $post->post_status === 'trash' ) {
			$this->setValidAccess( false );

			return $this;
		}
		if ( 'page' !== $post->post_type ) {
			if ( 'publish' === $post->post_status && $this->wpAll( [
						get_post_type_object( $post->post_type )->cap->edit_published_posts,
						$post->ID,
					] )->get() ) {
				$this->setValidAccess( true );

				return $this;
			} elseif ( 'publish' !== $post->post_status && $this->wpAll( [
						get_post_type_object( $post->post_type )->cap->edit_posts,
						$post->ID,
					] )->get() ) {
				$this->setValidAccess( true );

				return $this;
			}
		} elseif ( 'page' === $post->post_type && $this->wpAll( [
				'edit_pages',
				$post->ID,
			] )->get() ) {
			$this->setValidAccess( true );

			return $this;
		}

		$this->setValidAccess( false );

		return $this;
	}
}
