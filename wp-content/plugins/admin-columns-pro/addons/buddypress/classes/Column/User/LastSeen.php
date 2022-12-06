<?php

namespace ACA\BP\Column\User;

use AC;
use ACA\BP\Filtering;
use ACA\BP\Search;
use ACA\BP\Settings;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;

class LastSeen extends AC\Column\Meta
	implements ACP\Filtering\Filterable, ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	public function __construct() {
		$this->set_type( 'column-buddypress_user_last_seen' )
		     ->set_label( __( 'Last Seen', 'codepress-admin-columns' ) )
		     ->set_group( 'buddypress' );
	}

	public function get_meta_key() {
		return 'last_activity';
	}

	public function get_value( $id ) {
		$value = $this->get_formatted_value( $this->get_raw_value( $id ) );

		return $value ?: $this->get_empty_char();
	}

	public function get_raw_value( $id ) {
		return bp_get_user_last_activity( $id );
	}

	public function register_settings() {
		$this->add_setting( new Settings\Date( $this ) );
	}

	public function filtering() {
		return new Filtering\User\LastSeen( $this );
	}

	public function sorting() {
		return new ACP\Sorting\Model\User\Meta( $this->get_meta_key() );
	}

	public function search() {
		return new Search\User\Date( $this->get_meta_key(), $this->get_meta_type() );
	}

	public function conditional_format(): ?FormattableConfig {
		return new FormattableConfig( new ACP\ConditionalFormat\Formatter\DateFormatter\FormatFormatter() );
	}

}