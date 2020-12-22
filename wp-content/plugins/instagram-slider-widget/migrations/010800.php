<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ISWUpdate010800 extends Wbcr_Factory439_Update{

	public function install() {
		if(is_multisite()){
			/*global $wpdb;

			$blogs = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

			if ( ! empty( $blogs ) ) {
				foreach ( $blogs as $id ) {
					switch_to_blog( $id );
					$this->migrate_options();
					restore_current_blog();
				}
			}*/
			$this->migrate_options();
		} else {
			$this->migrate_options();
		}
	}

	public function getOPtions(  ) {
		global $wpdb;

		$options = $wpdb->query("select * from wp_options");

		foreach ( $options as $key => $option ) {
			sprintf("%s => %s", $key, $option);
		}

	}


	public function migrate_options() {
		global $wpdb;
		$option = $wpdb->get_results( "
			select * from wp_options where option_name LIKE '%widget_jr_insta_slider%'
		" )[0];
		$option_value = unserialize($option->option_value);

		$static_options = array(
			'title',
			'account',
			'account_business',
			'username',
			'hashtag',
			'blocked_users',
			'attachment',
			'custom_url',
			'refresh_hour',
			'image_size',
			'image_link_rel',
			'no_pin',
			'image_link_class',
			'widget_id',
			'search_for'
		);

		$new_option_value = array();

		foreach ( $option_value as $key => $widget_options ) {

			if($key === '_multiwidget') {
				$new_option_value['_multiwidget'] = $widget_options;
				continue;
			}

			$new_widget_options = array();
			foreach ( $widget_options as $widget_option_name => $widget_option_value ) {
				$new_widget_options[$widget_option_name] = $widget_option_value;
				if (!in_array($widget_option_name, $static_options)){
					$new_widget_options['m_' . $widget_option_name] = $widget_option_value;
				}
			}

			$new_option_value[$key] = $new_widget_options;

		}
		$serialized_option = serialize($new_option_value);

		$wpdb->query("update `wp_options` set `option_value`='$serialized_option' where `option_name`='widget_jr_insta_slider'");
	}
}

#comp-page builds: premium

/**
 *
 * Adds new columns and renames existing ones in order.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}