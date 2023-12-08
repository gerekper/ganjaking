<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\User;

use AC\Helper\Select;
use WP_User;

class Options extends Select\Options {

	/**
	 * @var WP_User[]
	 */
	private $users;

	/**
	 * @var array
	 */
	private $labels = [];

	private $formatter;

	public function __construct( array $users, LabelFormatter $formatter ) {
		$this->formatter = $formatter;
		array_map( [ $this, 'set_user' ], $users );
		$this->rename_duplicates();

		parent::__construct( $this->get_options() );
	}

	private function set_user( WP_User $user ): void {
		$this->users[ $user->ID ] = $user;
		$this->labels[ $user->ID ] = $this->formatter->format_label( $user );
	}

	public function get_user( int $id ): WP_User {
		return $this->users[ $id ];
	}

	private function get_options(): array {
		return self::create_from_array( $this->labels )->get_copy();
	}

	protected function rename_duplicates(): void {
		$duplicates = array_diff_assoc( $this->labels, array_unique( $this->labels ) );

		foreach ( $this->labels as $id => $label ) {
			if ( in_array( $label, $duplicates, true ) ) {
				$this->labels[ $id ] = $this->formatter->format_label_unique( $this->get_user( $id ) );
			}
		}
	}

}