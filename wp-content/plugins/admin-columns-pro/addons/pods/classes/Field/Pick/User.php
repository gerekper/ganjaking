<?php

namespace ACA\Pods\Field\Pick;

use AC;
use AC\Collection;
use AC\Settings;
use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Filtering;
use ACA\Pods\Search;
use ACP;
use ACP\Sorting\FormatValue\SerializedSettingFormatter;
use ACP\Sorting\FormatValue\SettingFormatter;

class User extends Field\Pick {

	public function sorting() {
		$setting = $this->column->get_setting( AC\Settings\Column\User::NAME );

		if ( ! $this->is_multiple() ) {
			$model = ( new ACP\Sorting\Model\MetaRelatedUserFactory() )->create( $this->get_meta_type(), $setting->get_value(), $this->get_meta_key() );

			if ( $model ) {
				return $model;
			}
		}

		$formatter = $this->is_multiple()
			? new SerializedSettingFormatter( new SettingFormatter( $setting ) )
			: new SettingFormatter( $setting );

		return ( new ACP\Sorting\Model\MetaFormatFactory() )->create( $this->column->get_meta_type(), $this->column->get_meta_type(), $formatter );
	}

	public function get_value( $id ) {
		return $this->column->get_formatted_value( new Collection( $this->get_raw_value( $id ) ) );
	}

	public function get_raw_value( $id ) {
		return (array) $this->get_ids_from_array( parent::get_raw_value( $id ) );
	}

	public function editing() {
		$view = ( new ACP\Editing\View\AjaxSelect() )->set_clear_button( true );
		$storage = new Editing\Storage\Field( $this->get_pod(), $this->get_field_name(), new Editing\Storage\Read\DbRaw( $this->get_meta_key(), $this->get_meta_type() ) );
		$args = [];
		if ( $this->get_option( 'pick_user_role' ) ) {
			$args['role__in'] = $this->get_option( 'pick_user_role' );
		}

		$paginated = new ACP\Editing\PaginatedOptions\Users( $args );

		return $this->is_multiple()
			? new ACP\Editing\Service\Users( $view->set_multiple( true ), $storage, $paginated )
			: new ACP\Editing\Service\User( $view, $storage, $paginated );
	}

	public function filtering() {
		return new Filtering\PickUsers( $this->column() );
	}

	public function search() {
		return new Search\PickUser( $this->column()->get_meta_key(), $this->column()->get_meta_type(), $this->get_option( 'pick_user_role' ) );
	}

	public function get_users( $user_ids ) {
		$names = [];

		if ( empty( $user_ids ) ) {
			return false;
		}

		foreach ( (array) $user_ids as $user_id ) {
			if ( $user_id ) {
				$names[ $user_id ] = ac_helper()->user->get_display_name( $user_id );
			}
		}

		return $names;
	}

	public function get_dependent_settings() {
		return [
			new Settings\Column\User( $this->column() ),
		];
	}

}