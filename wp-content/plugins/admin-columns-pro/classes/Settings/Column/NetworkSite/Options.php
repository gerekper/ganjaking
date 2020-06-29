<?php

namespace ACP\Settings\Column\NetworkSite;

use AC\Settings\Column;
use ACP\Column\NetworkSite;

class Options extends Column\Meta {

	public function __construct( NetworkSite\Options $column ) {
		parent::__construct( $column );
	}

	public function create_view() {
		$view = parent::create_view();

		$view->set( 'label', __( 'Option', 'codepress-admin-columns' ) );

		return $view;
	}

	protected function get_setting_field() {
		$setting = parent::get_setting_field();

		$setting->set_attribute( 'data-label', 'update' );

		return $setting;
	}

	public function get_cache_group() {
		return 'acp_network_site_options';
	}

	public function get_meta_keys() {
		global $wpdb;

		$keys = [];

		foreach ( get_sites() as $site ) {
			$table = $wpdb->get_blog_prefix( $site->blog_id ) . 'options';

			$sql = "
					SELECT {$table}.option_name, {$table}.option_value 
					FROM {$table}
					WHERE option_name NOT LIKE %s
				";

			// Exclude transients
			$values = $wpdb->get_results( $wpdb->prepare( $sql, $wpdb->esc_like( '_transient' ) . '%' ) );

			// Exclude serialized data
			foreach ( $values as $value ) {
				if ( is_serialized( $value->option_value ) ) {
					continue;
				}

				$keys[ $value->option_name ] = $value->option_name;
			}
		}

		natcasesort( $keys );

		return $keys;
	}

}