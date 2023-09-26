<?php

namespace ACA\BP\Column\User;

use AC;
use ACP;

class LastActivity extends AC\Column
	implements ACP\Export\Exportable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	private $actions;

	public function __construct() {
		$this->set_type( 'column-buddypress_user_last_activity' )
		     ->set_label( __( 'Last Activity', 'codepress-admin-columns' ) )
		     ->set_group( 'buddypress' );
	}

	public function get_value( $id ) {
		$activity = $this->get_last_activity( $id );

		if ( ! $activity ) {
			return $this->get_empty_char();
		}

		return $this->get_action( $activity );
	}

	public function get_raw_value( $id ) {
		$activity = $this->get_last_activity( $id );

		if ( ! $activity ) {
			return false;
		}

		return $activity->id;
	}

	public function is_valid() {
		return bp_is_active( 'activity' );
	}

	private function get_last_activity( $user_id ) {
		$activities = bp_activity_get( [
			'max'              => 1,
			'per_page'         => 1,
			'display_comments' => 'stream',
			'filter'           => [
				'user_id' => $user_id,
			],
			'show_hidden'      => true,
		] );

		if ( empty( $activities['activities'] ) ) {
			return false;
		}

		return $activities['activities'][0];
	}

	private function get_actions() {
		if ( null === $this->actions ) {
			$this->actions = bp_activity_admin_get_activity_actions();
		}

		return $this->actions;
	}

	private function get_action( $item ) {
		$actions = $this->get_actions();

		if ( isset( $actions[ $item->type ] ) ) {
			return $actions[ $item->type ];
		}

		return sprintf( __( 'Unregistered action - %s', 'buddypress' ), $item->type );
	}

	public function export() {
		return new ACP\Export\Model\StrippedValue( $this );
	}

}