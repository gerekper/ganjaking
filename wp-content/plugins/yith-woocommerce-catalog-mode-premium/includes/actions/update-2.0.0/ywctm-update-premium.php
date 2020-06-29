<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * UPGRADE RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywctm_upgrade_2_0_0' ) ) {

	/**
	 * Run plugin upgrade to version 2.0.0
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_upgrade_2_0_0() {

		if ( 'yes' === get_transient( 'ywctm_update' ) || YWCTM_VERSION === get_transient( 'ywctm_prune_settings' ) ) {

			WC()->queue()->cancel(
				'ywctm_update_callback',
				array(
					'callback' => 'ywctm_set_version',
					'args'     => array(),
				),
				'ywctm-updates-end'
			);

			return;
		}
		set_transient( 'ywctm_update', 'yes' );

		$loop             = 0;
		$start_time       = time();
		$update_callbacks = array(
			'ywctm_upgrade_settings_premium',
			'ywctm_upgrade_exclusions_premium',
		);

		WC()->queue()->schedule_recurring(
			$start_time,
			10,
			'ywctm_update_callback',
			array(
				'callback' => 'ywctm_set_version',
				'args'     => array(),
			),
			'ywctm-updates-end'
		);

		foreach ( $update_callbacks as $update_callback ) {
			WC()->queue()->schedule_single(
				$start_time,
				'ywctm_update_callback',
				array(
					'callback' => $update_callback,
					'args'     => array(
						'vendor_id'   => '',
						'post_author' => 0,
					),
				),
				'ywctm-updates'
			);
			$loop ++;
			$start_time += $loop;
		}

		if ( ywctm_is_multivendor_active() && ywctm_is_multivendor_integration_active() ) {

			$vendors     = YITH_Vendors()->get_vendors( array( 'enabled_selling' => true ) );
			$vendor_loop = 0;
			foreach ( $vendors as $vendor ) {

				foreach ( $update_callbacks as $update_callback ) {
					WC()->queue()->schedule_single(
						$start_time,
						'ywctm_update_callback',
						array(
							'callback' => $update_callback,
							'args'     => array(
								'vendor_id'   => '_' . $vendor->id,
								'post_author' => $vendor->get_owner(),
							),
						),
						'ywctm-updates'
					);
					$loop ++;
					$start_time += $loop;
				}

				WC()->queue()->schedule_single(
					time() + $vendor_loop + 604800,
					'ywctm_update_callback',
					array(
						'callback' => 'ywctm_prune_old_settings',
						'args'     => array(
							'vendor_id' => '_' . $vendor->id,
						),
					),
					'ywctm-updates'
				);

				$vendor_loop += 10;
			}
		}

		WC()->queue()->schedule_single(
			time() + 604800,
			'ywctm_update_callback',
			array(
				'callback' => 'ywctm_prune_old_settings',
				'args'     => array(
					'vendor_id' => '',
				),
			),
			'ywctm-updates-prune'
		);

	}

	add_action( 'admin_init', 'ywctm_upgrade_2_0_0' );
}

if ( ! function_exists( 'ywctm_update_callback' ) ) {

	/**
	 * Run an update callback when triggered by ActionScheduler.
	 *
	 * @param   $callback   string
	 * @param   $args       array
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_update_callback( $callback, $args ) {

		if ( is_callable( $callback ) ) {
			call_user_func( $callback, $args );
		}
	}

	add_action( 'ywctm_update_callback', 'ywctm_update_callback', 10, 2 );
}

if ( ! function_exists( 'ywctm_upgrade_settings_premium' ) ) {

	/**
	 * Upgrade settings to version 2.0.0
	 *
	 * @param   $args   array
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_upgrade_settings_premium( $args ) {

		$vendor_id   = $args['vendor_id'];
		$post_author = $args['post_author'];

		//Users and geolocation settings
		$users_option = get_option( 'ywctm_hide_price_users' . $vendor_id, 'all' );
		if ( 'country' === $users_option ) {

			$geolocation_settings = array(
				'action'    => get_option( 'ywctm_hide_countries_reverse' . $vendor_id ) === 'yes' ? 'disable' : 'enable',
				'users'     => 'all',
				'countries' => maybe_serialize( get_option( 'ywctm_hide_countries' . $vendor_id ) ),
			);
			update_option( 'ywctm_apply_users' . $vendor_id, 'all' );
			update_option( 'ywctm_enable_geolocation' . $vendor_id, 'yes' );
			update_option( 'ywctm_geolocation_settings' . $vendor_id, $geolocation_settings );

		} else {
			update_option( 'ywctm_apply_users' . $vendor_id, $users_option );
		}

		//Review settings
		if ( 'unregistered' === get_option( 'ywctm_disable_review' . $vendor_id ) ) {
			update_option( 'ywctm_disable_review' . $vendor_id, 'yes' );
		} else {
			update_option( 'ywctm_disable_review' . $vendor_id, 'no' );
		}

		if ( '' === $vendor_id ) {

			//Admin override vendor settings
			if ( 'yes' === get_option( 'ywctm_admin_override' ) ) {

				$exclusion = get_option( 'ywctm_admin_override_exclusion', 'no' );
				$reverse   = get_option( 'ywctm_admin_override_reverse', 'no' );

				if ( 'yes' === $exclusion && 'no' === $reverse ) {
					$option = array(
						'action' => 'enable',
						'target' => 'selection',
					);
				} elseif ( 'yes' === $exclusion && 'yes' === $reverse ) {
					$option = array(
						'action' => 'disable',
						'target' => 'selection',
					);
				} else {
					$option = array(
						'action' => 'enable',
						'target' => 'all',
					);
				}
				update_option( 'ywctm_admin_override_settings', $option );

			}
		}

		//Disable shop settings
		if ( 'yes' === get_option( 'ywctm_hide_cart_header' . $vendor_id ) ) {
			update_option( 'ywctm_disable_shop' . $vendor_id, 'yes' );
		}

		//Add to cart and exclusions settings
		$reverse_atc = get_option( 'ywctm_exclude_hide_add_to_cart_reverse' . $vendor_id );
		$exclude_atc = get_option( 'ywctm_exclude_hide_add_to_cart' . $vendor_id );
		$hide_single = get_option( 'ywctm_hide_add_to_cart_single' . $vendor_id );
		$hide_loop   = get_option( 'ywctm_hide_add_to_cart_loop' . $vendor_id );
		if ( 'no' === $hide_loop && 'no' === $hide_single ) {
			$atc_option = array(
				'action' => 'show',
				'where'  => 'all',
				'items'  => 'all',
			);
		} else {
			switch ( true ) {
				case 'no' === $hide_loop && 'yes' === $hide_single:
					$where = 'product';
					break;
				case 'yes' === $hide_loop && 'no' === $hide_single:
					$where = 'shop';
					break;
				default:
					$where = 'all';
			}
			$atc_option = array(
				'action' => 'yes' === $reverse_atc ? 'show' : 'hide',
				'where'  => $where,
				'items'  => 'yes' === $exclude_atc ? 'exclusion' : 'all',
			);
		}
		update_option( 'ywctm_hide_add_to_cart_settings' . $vendor_id, $atc_option );

		//Price and exclusions settings
		$reverse_price = get_option( 'ywctm_exclude_hide_price_reverse' . $vendor_id );
		$exclude_price = get_option( 'ywctm_exclude_hide_price' . $vendor_id );
		$hide_price    = get_option( 'ywctm_hide_price' . $vendor_id );
		if ( 'no' === $hide_price ) {
			$price_option = array(
				'action' => 'show',
				'items'  => 'all',
			);
		} else {
			$price_option = array(
				'action' => 'yes' === $reverse_price ? 'show' : 'hide',
				'items'  => 'yes' === $exclude_price ? 'exclusion' : 'all',
			);
		}
		update_option( 'ywctm_hide_price_settings' . $vendor_id, $price_option );

		//Price alternative text and custom button settings
		$alternative_text = get_option( 'ywctm_exclude_price_alternative_text' . $vendor_id );
		if ( '' !== $alternative_text ) {
			$buttons_label = ywctm_get_button_label_defaults(
				array(
					'label_text' => $alternative_text,
				)
			);
			$button_data   = array(
				'post_title'   => $alternative_text,
				'post_content' => '',
				'post_excerpt' => '',
				'post_status'  => 'publish',
				'post_author'  => $post_author,
				'post_type'    => 'ywctm-button-label',
			);
			$button_id     = wp_insert_post( $button_data );
			foreach ( $buttons_label as $key => $value ) {
				update_post_meta( $button_id, $key, $value );
			}
			update_option( 'ywctm_custom_price_text_settings' . $vendor_id, $button_id );

			if ( ywctm_is_wpml_active() ) {

				$time      = 0;
				$group     = 0;
				$loop      = 0;
				$languages = apply_filters( 'wpml_active_languages', null, array() );

				foreach ( $languages as $language ) {

					$translated_label = wpml_translate_single_string_filter( '', 'admin_texts_ywctm_exclude_price_alternative_text' . $vendor_id, 'ywctm_exclude_price_alternative_text' . $vendor_id, $language['language_code'] );

					if ( $translated_label !== $alternative_text ) {

						set_transient(
							'ywctm_wpml_label_data_' . $loop,
							array(
								'original_id'      => $button_id,
								'language'         => $language['language_code'],
								'button_data'      => $button_data,
								'button_meta'      => $buttons_label,
								'translated_label' => $translated_label,
							)
						);

						WC()->queue()->schedule_single(
							time() + $time,
							'ywctm_update_callback',
							array(
								'callback' => 'ywctm_create_button_translations',
								'args'     => 'ywctm_wpml_label_data_' . $loop,
							),
							'ywctm-updates'
						);
					}

					$group ++;
					$loop ++;
					if ( $group >= 5 ) {
						$group = 0;

						$time += 10;
					}
				}
			}
		}
		if ( 'yes' === get_option( 'ywctm_custom_button' . $vendor_id ) || 'yes' === get_option( 'ywctm_custom_button_loop' . $vendor_id ) ) {
			$button_text     = get_option( 'ywctm_button_text' . $vendor_id );
			$button_url_type = get_option( 'ywctm_button_url_type' . $vendor_id );
			$button_url      = get_option( 'ywctm_button_url' . $vendor_id );
			$url             = 'generic' !== $button_url_type ? $button_url_type . ':' . $button_url : $button_url;
			$icon_setting    = get_option( 'ywctm_button_icon' . $vendor_id );
			$buttons_label   = ywctm_get_button_label_defaults(
				array(
					'label_text'       => $button_text,
					'text_color'       => array(
						'default' => get_option( 'ywctm_button_color' . $vendor_id ),
						'hover'   => get_option( 'ywctm_button_hover' . $vendor_id ),
					),
					'button_url'       => $url,
					'background_color' => array(
						'default' => get_option( 'ywctm_button_bg_color' . $vendor_id ),
						'hover'   => get_option( 'ywctm_button_bg_hover' . $vendor_id ),
					),
					'icon_type'        => $icon_setting['select'],
					'selected_icon'    => str_replace( 'fa-', '', $icon_setting['icon'] ),
					'custom_icon'      => $icon_setting['custom'],
				)
			);
			$button_data     = array(
				'post_title'   => $button_text,
				'post_content' => '',
				'post_excerpt' => '',
				'post_status'  => 'publish',
				'post_author'  => $post_author,
				'post_type'    => 'ywctm-button-label',
			);
			$button_id       = wp_insert_post( $button_data );
			foreach ( $buttons_label as $key => $value ) {
				update_post_meta( $button_id, $key, $value );
			}
			update_option( 'ywctm_custom_button_settings' . $vendor_id, $button_id );
			update_option( 'ywctm_custom_button_settings_loop' . $vendor_id, $button_id );

			if ( ywctm_is_wpml_active() ) {

				$time      = 0;
				$group     = 0;
				$loop      = 0;
				$languages = apply_filters( 'wpml_active_languages', null, array() );

				foreach ( $languages as $language ) {

					$translated_label = wpml_translate_single_string_filter( '', 'admin_texts_ywctm_button_text' . $vendor_id, 'ywctm_button_text' . $vendor_id, $language['language_code'] );

					if ( $translated_label !== $button_text ) {

						set_transient(
							'ywctm_wpml_button_data_' . $loop,
							array(
								'original_id'      => $button_id,
								'language'         => $language['language_code'],
								'button_data'      => $button_data,
								'button_meta'      => $buttons_label,
								'translated_label' => $translated_label,
							)
						);

						WC()->queue()->schedule_single(
							time() + $time,
							'ywctm_update_callback',
							array(
								'callback' => 'ywctm_create_button_translations',
								'args'     => 'ywctm_wpml_button_data_' . $loop,
							),
							'ywctm-updates'
						);
					}

					$group ++;
					$loop ++;
					if ( $group >= 5 ) {
						$group = 0;

						$time += 10;
					}
				}
			}
		}

		//Inquiry form settings
		if ( 'none' === get_option( 'ywctm_inquiry_form_type' . $vendor_id ) ) {
			update_option( 'ywctm_inquiry_form_enabled' . $vendor_id, 'hidden' );
		} else {
			update_option( 'ywctm_inquiry_form_enabled' . $vendor_id, 'visible' );
		}
		$form_option = get_option( 'ywctm_inquiry_form_where_show' . $vendor_id );
		if ( 'tab' !== $form_option ) {
			update_option( 'ywctm_inquiry_form_position' . $vendor_id, $form_option );
			update_option( 'ywctm_inquiry_form_where_show' . $vendor_id, 'desc' );
		}
	}
}

if ( ! function_exists( 'ywctm_upgrade_exclusions_premium' ) ) {

	/**
	 * Upgrade exclusions to version 2.0.0
	 *
	 * @param   $args   array
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_upgrade_exclusions_premium( $args ) {

		$vendor_id   = $args['vendor_id'];
		$post_author = $args['post_author'];

		global $wpdb;

		$time  = 0;
		$group = 0;
		$loop  = 0;

		// Search for previous custom button variations
		$buttons_query = "SELECT GROUP_CONCAT(DISTINCT ids) as ids, button_text, protocol, url
				FROM (
        				 ( SELECT GROUP_CONCAT(DISTINCT concat(id, '-product')) as ids, button_text, protocol, url
        				 FROM (
          					    SELECT a.ID AS id,
                          				MAX(CASE WHEN b.meta_key = '_ywctm_custom_url_enabled$vendor_id'	THEN b.meta_value ELSE NULL END) AS enabled,
                          				MAX(CASE WHEN b.meta_key = '_ywctm_button_text$vendor_id'			THEN b.meta_value ELSE NULL END) AS button_text,
                          				MAX(CASE WHEN b.meta_key = '_ywctm_custom_url_protocol$vendor_id'	THEN b.meta_value ELSE NULL END) AS protocol,
                          				MAX(CASE WHEN b.meta_key = '_ywctm_custom_url_link$vendor_id'		THEN b.meta_value ELSE NULL END) AS url
                          		FROM $wpdb->posts a INNER JOIN $wpdb->postmeta b ON a.ID = b.post_id
          					    WHERE a.post_type = 'product'
          					      AND (
          					          b.meta_key = '_ywctm_custom_url_enabled$vendor_id'
          					              OR b.meta_key = '_ywctm_button_text$vendor_id'
          					              OR b.meta_key = '_ywctm_custom_url_protocol$vendor_id'
          					              OR b.meta_key = '_ywctm_custom_url_link$vendor_id'
          					              )
        				 		GROUP BY a.ID) AS buttons
        				 GROUP BY url, button_text )
        				 UNION
        				 ( SELECT GROUP_CONCAT(DISTINCT concat(id, '-category')) as ids, button_text, protocol, url
        				 FROM (
        				     SELECT a.term_id AS id,
        				            MAX(CASE WHEN c.meta_key = '_ywctm_custom_url_enabled$vendor_id'	THEN c.meta_value ELSE NULL END) AS enabled,
        				            MAX(CASE WHEN c.meta_key = '_ywctm_button_text$vendor_id' 			THEN c.meta_value ELSE NULL END) AS button_text,
        				            MAX(CASE WHEN c.meta_key = '_ywctm_custom_url_protocol$vendor_id'	THEN c.meta_value ELSE NULL END) AS protocol,
        				            MAX(CASE WHEN c.meta_key = '_ywctm_custom_url_link$vendor_id'		THEN c.meta_value ELSE NULL END) AS url
        				     FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
        				     WHERE b.taxonomy = 'product_cat'
        				       AND (
        				           c.meta_key = '_ywctm_custom_url_enabled$vendor_id'
        				               OR c.meta_key = '_ywctm_button_text$vendor_id'
        				               OR c.meta_key = '_ywctm_custom_url_protocol$vendor_id'
        				               OR c.meta_key = '_ywctm_custom_url_link$vendor_id'
        				           )
        				     GROUP BY a.term_id) AS buttons
        				 GROUP BY url, button_text )
        				 UNION
        				 ( SELECT GROUP_CONCAT(DISTINCT concat(id, '-tag')) as ids, button_text, protocol, url
        				 FROM (
        				     SELECT a.term_id AS id,
        				            MAX(CASE WHEN c.meta_key = '_ywctm_custom_url_enabled$vendor_id'	THEN c.meta_value ELSE NULL END) AS enabled,
        				            MAX(CASE WHEN c.meta_key = '_ywctm_button_text$vendor_id' 			THEN c.meta_value ELSE NULL END) AS button_text,
        				            MAX(CASE WHEN c.meta_key = '_ywctm_custom_url_protocol$vendor_id'	THEN c.meta_value ELSE NULL END) AS protocol,
        				            MAX(CASE WHEN c.meta_key = '_ywctm_custom_url_link$vendor_id'		THEN c.meta_value ELSE NULL END) AS url
        				     FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
        				     WHERE b.taxonomy = 'product_tag'
        				       AND (
        				           c.meta_key = '_ywctm_custom_url_enabled$vendor_id'
        				               OR c.meta_key = '_ywctm_button_text$vendor_id'
        				               OR c.meta_key = '_ywctm_custom_url_protocol$vendor_id'
        				               OR c.meta_key = '_ywctm_custom_url_link$vendor_id'
        				           )
        				     GROUP BY a.term_id) AS buttons
        				 GROUP BY url, button_text )
					    ) AS buttons
				GROUP BY url, button_text";
		$buttons       = $wpdb->get_results( $buttons_query, ARRAY_A ); //phpcs:ignore
		foreach ( $buttons as $button ) {

			set_transient(
				'ywctm_temp_button_data_' . $loop,
				array(
					'button'      => $button,
					'vendor_id'   => $vendor_id,
					'post_author' => $post_author,
				)
			);

			WC()->queue()->schedule_single(
				time() + $time,
				'ywctm_update_callback',
				array(
					'callback' => 'ywctm_create_button_from_exclusions',
					'args'     => 'ywctm_temp_button_data_' . $loop,
				),
				'ywctm-updates'
			);

			$group ++;
			$loop ++;
			if ( $group >= 5 ) {
				$group = 0;

				$time += 10;
			}
		}

		// Search for previous price alternative text variations
		$labels_query = "SELECT GROUP_CONCAT(DISTINCT ids) as ids, button_text
				FROM (
        				 ( SELECT GROUP_CONCAT(DISTINCT concat(id, '-product')) as ids, button_text
        				 FROM (
          					    SELECT a.ID AS id,
                          				MAX(CASE WHEN b.meta_key = '_ywctm_alternative_text$vendor_id' THEN b.meta_value ELSE NULL END) AS button_text
                          		FROM $wpdb->posts a INNER JOIN $wpdb->postmeta b ON a.ID = b.post_id
          					    WHERE a.post_type = 'product'
          					      AND b.meta_key = '_ywctm_alternative_text$vendor_id'
        				 		GROUP BY a.ID) AS buttons
        				 GROUP BY button_text )
        				 UNION
        				 ( SELECT GROUP_CONCAT(DISTINCT concat(id, '-category')) as ids, button_text
        				 FROM (
        				     SELECT a.term_id AS id,
        				            MAX(CASE WHEN c.meta_key = '_ywctm_alternative_text$vendor_id' THEN c.meta_value ELSE NULL END) AS button_text
        				     FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
        				     WHERE b.taxonomy = 'product_cat'
        				       AND c.meta_key = '_ywctm_alternative_text$vendor_id'
        				     GROUP BY a.term_id) AS buttons
        				 GROUP BY button_text )
        				 UNION
        				 ( SELECT GROUP_CONCAT(DISTINCT concat(id, '-tag')) as ids, button_text
        				 FROM (
        				     SELECT a.term_id AS id,
        				            MAX(CASE WHEN c.meta_key = '_ywctm_alternative_text$vendor_id' THEN c.meta_value ELSE NULL END) AS button_text
        				     FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
        				     WHERE b.taxonomy = 'product_tag'
        				       AND c.meta_key = '_ywctm_alternative_text$vendor_id'
        				     GROUP BY a.term_id) AS buttons
        				 GROUP BY button_text )
					    ) AS buttons
				GROUP BY button_text";
		$labels       = $wpdb->get_results( $labels_query, ARRAY_A ); //phpcs:ignore
		foreach ( $labels as $label ) {

			set_transient(
				'ywctm_temp_button_data_' . $loop,
				array(
					'button'      => $label,
					'vendor_id'   => $vendor_id,
					'post_author' => $post_author,
				)
			);

			WC()->queue()->schedule_single(
				time() + $time,
				'ywctm_update_callback',
				array(
					'callback' => 'ywctm_create_labels_from_exclusions',
					'args'     => 'ywctm_temp_button_data_' . $loop,
				),
				'ywctm-updates'
			);

			$group ++;
			$loop ++;
			if ( $group >= 5 ) {
				$group = 0;

				$time += 10;
			}
		}

		// Search for custom button exclusions
		$nobuttons_query   = "SELECT id, type
				FROM (
				    ( SELECT a.ID      AS id,
				             'product' AS type
				    FROM $wpdb->posts a INNER JOIN $wpdb->postmeta b ON a.ID = b.post_id
				    WHERE a.post_type = 'product'
				      AND b.meta_key = '_ywctm_exclude_button'
				      AND b.meta_value = 'yes'
				    GROUP BY a.ID )
				    UNION
				    ( SELECT a.term_id AS id,
				            'category' AS type
				    FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
				    WHERE b.taxonomy = 'product_cat'
				      AND c.meta_key = '_ywctm_exclude_button'
				      AND c.meta_value = 'yes'
				    GROUP BY a.term_id )
				    UNION
				    ( SELECT a.term_id AS id,
				             'tag'     AS type
				    FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
				    WHERE b.taxonomy = 'product_tag'
				      AND c.meta_key = '_ywctm_exclude_button'
				      AND c.meta_value = 'yes'
				    GROUP BY a.term_id )
				    ) AS exclude_buttons";
		$button_exclusions = $wpdb->get_results( $nobuttons_query, ARRAY_A ); //phpcs:ignore
		foreach ( $button_exclusions as $exclusion ) {

			$price_global   = get_option( 'ywctm_hide_price_settings' . $vendor_id );
			$exclusion_data = array(
				'enable_inquiry_form'         => 'yes',
				'enable_atc_custom_options'   => 'yes',
				'atc_status'                  => 'hide',
				'custom_button'               => 'none',
				'custom_button_loop'          => 'none',
				'enable_price_custom_options' => 'no',
				'price_status'                => $price_global['action'],
				'custom_price_text'           => 'none',
			);

			if ( 'product' === $exclusion['type'] ) {
				$existing_exclusion = get_post_meta( $exclusion['id'], '_ywctm_exclusion_settings' . $vendor_id, true );
			} else {
				$existing_exclusion = get_term_meta( $exclusion['id'], '_ywctm_exclusion_settings' . $vendor_id, true );
			}

			if ( ! empty( $existing_exclusion ) ) {
				$exclusion_data                              = $existing_exclusion;
				$exclusion_data['enable_atc_custom_options'] = 'yes';
				$exclusion_data['atc_status']                = 'hide';
				$exclusion_data['custom_button']             = 'none';
				$exclusion_data['custom_button_loop']        = 'none';
			}

			if ( 'product' === $exclusion['type'] ) {
				update_post_meta( $exclusion['id'], '_ywctm_exclusion_settings' . $vendor_id, $exclusion_data );
			} else {
				update_term_meta( $exclusion['id'], '_ywctm_exclusion_settings' . $vendor_id, $exclusion_data );
			}
		}

		// Search for generic exclusions
		$exclusion_query = "SELECT id, add_to_cart, price, type
				FROM (
				    ( SELECT a.ID      AS id,
				            MAX( CASE WHEN b.meta_key = '_ywctm_exclude_catalog_mode' THEN b.meta_value ELSE NULL END ) AS add_to_cart,
				            MAX( CASE WHEN b.meta_key = '_ywctm_exclude_hide_price' THEN b.meta_value ELSE NULL END ) AS price,
				            'product' AS type
				    FROM $wpdb->posts a INNER JOIN $wpdb->postmeta b ON a.ID = b.post_id
				    WHERE a.post_type = 'product'
				      AND ( b.meta_key = '_ywctm_exclude_catalog_mode' OR b.meta_key = '_ywctm_exclude_hide_price' )
				      AND b.meta_value = 'yes'
				    GROUP BY a.ID )
				    UNION
				    ( SELECT a.term_id  AS id,
				             MAX( CASE WHEN c.meta_key = '_ywctm_exclude_catalog_mode' THEN c.meta_value ELSE NULL END ) AS add_to_cart,
				             MAX( CASE WHEN c.meta_key = '_ywctm_exclude_hide_price' THEN c.meta_value ELSE NULL END ) AS price,
				             'category' AS type
				    FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
				    WHERE b.taxonomy = 'product_cat'
				      AND ( c.meta_key = '_ywctm_exclude_catalog_mode' OR c.meta_key = '_ywctm_exclude_hide_price' )
				      AND c.meta_value = 'yes'
				    GROUP BY a.term_id )
				    UNION
				    ( SELECT a.term_id AS id,
				             MAX( CASE WHEN c.meta_key = '_ywctm_exclude_catalog_mode' THEN c.meta_value ELSE NULL END ) AS add_to_cart,
				             MAX( CASE WHEN c.meta_key = '_ywctm_exclude_hide_price' THEN c.meta_value ELSE NULL END ) AS price,
				             'tag'     AS type
				    FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
				    WHERE b.taxonomy = 'product_tag'
				      AND ( c.meta_key = '_ywctm_exclude_catalog_mode' OR c.meta_key = '_ywctm_exclude_hide_price' )
				      AND c.meta_value = 'yes'
				      GROUP BY a.term_id )
				    ) AS exclusions";
		$old_exclusions  = $wpdb->get_results( $exclusion_query, ARRAY_A ); //phpcs:ignore
		foreach ( $old_exclusions as $exclusion ) {

			$atc_global         = get_option( 'ywctm_hide_add_to_cart_settings' . $vendor_id );
			$button_global      = get_option( 'ywctm_custom_button_settings' . $vendor_id );
			$button_loop_global = get_option( 'ywctm_custom_button_settings_loop' . $vendor_id );
			$price_global       = get_option( 'ywctm_hide_price_settings' . $vendor_id );
			$label_global       = get_option( 'ywctm_custom_price_text_settings' . $vendor_id );

			$exclusion_data = array(
				'enable_inquiry_form'         => 'yes',
				'enable_atc_custom_options'   => 'no',
				'atc_status'                  => $atc_global['action'],
				'custom_button'               => $button_global,
				'custom_button_loop'          => $button_loop_global,
				'enable_price_custom_options' => 'no',
				'price_status'                => $price_global['action'],
				'custom_price_text'           => $label_global,
			);

			if ( 'product' === $exclusion['type'] ) {
				$existing_exclusion = get_post_meta( $exclusion['id'], '_ywctm_exclusion_settings' . $vendor_id, true );
			} else {
				$existing_exclusion = get_term_meta( $exclusion['id'], '_ywctm_exclusion_settings' . $vendor_id, true );
			}

			if ( empty( $existing_exclusion ) ) {
				if ( 'product' === $exclusion['type'] ) {
					update_post_meta( $exclusion['id'], '_ywctm_exclusion_settings' . $vendor_id, $exclusion_data );
				} else {
					update_term_meta( $exclusion['id'], '_ywctm_exclusion_settings' . $vendor_id, $exclusion_data );
				}
			}
		}

	}
}

if ( ! function_exists( 'ywctm_create_button_translations' ) ) {

	/**
	 * Create custom buttons translations
	 *
	 * @param   $transient_id string
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_create_button_translations( $transient_id ) {

		$args          = get_transient( $transient_id );
		$original_id   = $args['original_id'];
		$language_code = $args['language'];
		$button_data   = $args['button_data'];
		$button_meta   = $args['button_meta'];

		$button_data['post_title']       = $args['translated_label'];
		$button_meta['ywctm_label_text'] = $args['translated_label'];

		$new_button_id = wp_insert_post( $button_data );
		foreach ( $button_meta as $key => $value ) {
			update_post_meta( $new_button_id, $key, $value );
		}

		$get_language_args           = array(
			'element_id'   => $original_id,
			'element_type' => 'post',
		);
		$original_post_language_info = apply_filters( 'wpml_element_language_details', null, $get_language_args );

		$set_language_args = array(
			'element_id'           => $new_button_id,
			'element_type'         => apply_filters( 'wpml_element_type', 'ywctm-button-label' ),
			'trid'                 => $original_post_language_info->trid,
			'language_code'        => $language_code,
			'source_language_code' => $original_post_language_info->language_code,
		);

		do_action( 'wpml_set_element_language_details', $set_language_args );

		delete_transient( $transient_id );
	}
}

if ( ! function_exists( 'ywctm_create_button_from_exclusions' ) ) {

	/**
	 * Create custom buttons from old settings
	 *
	 * @param   $transient_id string
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_create_button_from_exclusions( $transient_id ) {

		$args            = get_transient( $transient_id );
		$button          = $args['button'];
		$vendor_id       = $args['vendor_id'];
		$post_author     = $args['post_author'];
		$button_text     = '' !== $button['button_text'] ? $button['button_text'] : get_option( 'ywctm_button_text' . $vendor_id );
		$button_url_type = $button['protocol'];
		$button_url      = $button['url'];
		$url             = 'generic' !== $button_url_type ? $button_url_type . ':' . $button_url : $button_url;
		$icon_setting    = get_option( 'ywctm_button_icon' . $vendor_id );
		$buttons_label   = ywctm_get_button_label_defaults(
			array(
				'label_text'       => $button_text,
				'text_color'       => array(
					'default' => get_option( 'ywctm_button_color' . $vendor_id ),
					'hover'   => get_option( 'ywctm_button_hover' . $vendor_id ),
				),
				'button_url'       => $url,
				'background_color' => array(
					'default' => get_option( 'ywctm_button_bg_color' . $vendor_id ),
					'hover'   => get_option( 'ywctm_button_bg_hover' . $vendor_id ),
				),
				'icon_type'        => $icon_setting['select'],
				'selected_icon'    => str_replace( 'fa-', '', $icon_setting['icon'] ),
				'custom_icon'      => $icon_setting['custom'],
			)
		);
		$button_data     = array(
			'post_title'   => $button_text,
			'post_content' => '',
			'post_excerpt' => '',
			'post_status'  => 'publish',
			'post_author'  => $post_author,
			'post_type'    => 'ywctm-button-label',
		);
		$button_id       = wp_insert_post( $button_data );
		foreach ( $buttons_label as $key => $value ) {
			update_post_meta( $button_id, $key, $value );
		}

		$items = explode( ',', $button['ids'] );

		ywctm_assign_custom_button( $items, $button_id, $vendor_id, 'button' );

		delete_transient( $transient_id );

	}
}

if ( ! function_exists( 'ywctm_create_labels_from_exclusions' ) ) {

	/**
	 * Get create lables from old settings
	 *
	 * @param   $transient_id string
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_create_labels_from_exclusions( $transient_id ) {

		$args          = get_transient( $transient_id );
		$button        = $args['button'];
		$vendor_id     = $args['vendor_id'];
		$post_author   = $args['post_author'];
		$button_text   = $button['button_text'];
		$buttons_label = ywctm_get_button_label_defaults(
			array(
				'label_text' => $button_text,
			)
		);
		$button_data   = array(
			'post_title'   => $button_text,
			'post_content' => '',
			'post_excerpt' => '',
			'post_status'  => 'publish',
			'post_author'  => $post_author,
			'post_type'    => 'ywctm-button-label',
		);
		$button_id     = wp_insert_post( $button_data );
		foreach ( $buttons_label as $key => $value ) {
			update_post_meta( $button_id, $key, $value );
		}

		$items = explode( ',', $button['ids'] );

		ywctm_assign_custom_button( $items, $button_id, $vendor_id, 'label' );

		delete_transient( $transient_id );

	}
}

if ( ! function_exists( 'ywctm_assign_custom_button' ) ) {

	/**
	 * Get create custom buttons from old settings
	 *
	 * @param   $items     array
	 * @param   $button_id integer
	 * @param   $vendor_id string
	 * @param   $type      string
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_assign_custom_button( $items, $button_id, $vendor_id, $type ) {

		foreach ( $items as $idtype ) {

			$id             = explode( '-', $idtype );
			$atc_global     = get_option( 'ywctm_hide_add_to_cart_settings' . $vendor_id );
			$price_global   = get_option( 'ywctm_hide_price_settings' . $vendor_id );
			$exclusion_data = array(
				'enable_inquiry_form'         => 'yes',
				'enable_atc_custom_options'   => 'no',
				'atc_status'                  => $atc_global['action'],
				'custom_button'               => 'none',
				'custom_button_loop'          => 'none',
				'enable_price_custom_options' => 'no',
				'price_status'                => $price_global['action'],
				'custom_price_text'           => 'none',
			);

			if ( 'product' === $id[1] ) {
				$existing_exclusion = get_post_meta( $id[0], '_ywctm_exclusion_settings' . $vendor_id, true );
			} else {
				$existing_exclusion = get_term_meta( $id[0], '_ywctm_exclusion_settings' . $vendor_id, true );
			}

			if ( ! empty( $existing_exclusion ) ) {
				$exclusion_data = $existing_exclusion;
			}

			if ( 'button' === $type ) {
				$exclusion_data['enable_atc_custom_options'] = 'yes';
				$exclusion_data['atc_status']                = 'hide';
				$exclusion_data['custom_button']             = $button_id;
				$exclusion_data['custom_button_loop']        = $button_id;
			} else {
				$exclusion_data['enable_price_custom_options'] = 'yes';
				$exclusion_data['price_status']                = 'hide';
				$exclusion_data['custom_price_text']           = $button_id;
			}

			if ( 'product' === $id[1] ) {
				update_post_meta( $id[0], '_ywctm_exclusion_settings' . $vendor_id, $exclusion_data );
			} else {
				update_term_meta( $id[0], '_ywctm_exclusion_settings' . $vendor_id, $exclusion_data );
			}
		}
	}
}

if ( ! function_exists( 'ywctm_get_button_label_defaults' ) ) {

	/**
	 * Get default values for imported buttons and labels
	 *
	 * @param   $args array
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_button_label_defaults( $args ) {

		return array(
			'ywctm_label_text'              => $args['label_text'],
			'ywctm_text_color'              => array(
				'default' => isset( $args['text_color']['default'] ) ? $args['text_color']['default'] : '#000000',
				'hover'   => isset( $args['text_color']['hover'] ) ? $args['text_color']['hover'] : '#000000',
			),
			'ywctm_button_url_type'         => 'custom',
			'ywctm_button_url'              => '',
			'ywctm_background_color'        => array(
				'default' => isset( $args['background_color']['default'] ) ? $args['background_color']['default'] : 'rgba(255,255,255,0)',
				'hover'   => isset( $args['background_color']['hover'] ) ? $args['background_color']['hover'] : 'rgba(255,255,255,0)',
			),
			'ywctm_border_color'            => array(
				'default' => 'rgba(255,255,255,0)',
				'hover'   => 'rgba(255,255,255,0)',
			),
			'ywctm_icon_color'              => array(
				'default' => 'rgba(255,255,255,0)',
				'hover'   => 'rgba(255,255,255,0)',
			),
			'ywctm_border_style'            => array(
				'thickness' => 1,
				'radius'    => '',
			),
			'ywctm_icon_type'               => isset( $args['icon_type'] ) ? $args['icon_type'] : 'none',
			'ywctm_selected_icon'           => isset( $args['selected_icon'] ) ? $args['selected_icon'] : '',
			'ywctm_selected_icon_alignment' => 'center',
			'ywctm_custom_icon'             => isset( $args['custom_icon'] ) ? $args['custom_icon'] : '',
			'ywctm_width_settings'          => array(
				'width' => '',
				'unit'  => '',
			),
			'ywctm_margin_settings'         => array(
				'top'    => '',
				'bottom' => '',
				'left'   => '',
				'right'  => '',
			),
			'ywctm_padding_settings'        => array(
				'top'    => '',
				'bottom' => '',
				'left'   => '',
				'right'  => '',
			),
		);

	}
}

if ( ! function_exists( 'ywctm_prune_old_settings' ) ) {

	/**
	 * Remove old settings
	 *
	 * @param   $args array
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_prune_old_settings( $args ) {

		$vendor_id = $args['vendor_id'];

		delete_option( 'ywctm_enable_plugin' . $vendor_id );
		delete_option( 'ywctm_hide_cart_header' . $vendor_id );
		delete_option( 'ywctm_hide_price_users' . $vendor_id );
		delete_option( 'ywctm_hide_countries' . $vendor_id );
		delete_option( 'ywctm_hide_countries_reverse' . $vendor_id );
		delete_option( 'ywctm_admin_override_exclusion' . $vendor_id );
		delete_option( 'ywctm_admin_override_reverse' . $vendor_id );
		delete_option( 'ywctm_exclude_hide_add_to_cart_reverse' . $vendor_id );
		delete_option( 'ywctm_exclude_hide_add_to_cart' . $vendor_id );
		delete_option( 'ywctm_hide_add_to_cart_single' . $vendor_id );
		delete_option( 'ywctm_hide_add_to_cart_loop' . $vendor_id );
		delete_option( 'ywctm_exclude_hide_price_reverse' . $vendor_id );
		delete_option( 'ywctm_exclude_hide_price' . $vendor_id );
		delete_option( 'ywctm_hide_price' . $vendor_id );
		delete_option( 'ywctm_exclude_price_alternative_text' . $vendor_id );
		delete_option( 'ywctm_button_icon' . $vendor_id );
		delete_option( 'ywctm_button_text' . $vendor_id );
		delete_option( 'ywctm_button_url_type' . $vendor_id );
		delete_option( 'ywctm_button_url' . $vendor_id );
		delete_option( 'ywctm_custom_button' . $vendor_id );
		delete_option( 'ywctm_custom_button_loop' . $vendor_id );
		delete_option( 'ywctm_button_color' . $vendor_id );
		delete_option( 'ywctm_button_hover' . $vendor_id );
		delete_option( 'ywctm_button_bg_color' . $vendor_id );
		delete_option( 'ywctm_button_bg_hover' . $vendor_id );
		delete_transient( 'ywctm_prune_settings' );
	}
}

if ( ! function_exists( 'ywctm_set_version' ) ) {

	/**
	 * Set plugin version
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_set_version() {

		$functions = WC()->queue()->search(
			array(
				'group'  => 'ywctm-updates',
				'status' => 'pending',
			)
		);

		if ( empty( $functions ) ) {
			update_option( 'ywctm_update_version', YWCTM_VERSION );
			delete_transient( 'ywctm_update' );
			set_transient( 'ywctm_prune_settings', YWCTM_VERSION );
		}
	}
}

/**
 * CUSTOM BUTTON LEGACY SETTINGS
 */
if ( ! function_exists( 'ywctm_keep_legacy_buttons' ) ) {

	/**
	 * Keep legacy buttons until update is done
	 *
	 * @param   $value   mixed
	 * @param   $post_id integer
	 * @param   $type    string
	 *
	 * @return  mixed
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_keep_legacy_buttons( $value, $post_id = 0, $type ) {

		if ( 'custom_button' === $type && 'custom_button_loop' === $type ) {

			if ( version_compare( YWCTM_VERSION, get_option( 'ywctm_update_version' ), '>' ) ) {
				$value = 'legacy';
			}
		}

		return $value;
	}

	add_filter( 'ywctm_get_exclusion', 'ywctm_keep_legacy_buttons', 15, 3 );
}

if ( ! function_exists( 'ywctm_return_legacy_settings' ) ) {

	/**
	 * Get settings for legacy buttons
	 *
	 * @param   $settings array
	 * @param   $id       string
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_return_legacy_settings( $settings, $id ) {

		if ( 'legacy' !== $id ) {
			return $settings;
		}

		global $post;

		if ( ! isset( $post ) ) {
			return array();
		}

		$button_text = '';
		$protocol    = '';
		$link        = '';
		$post_id     = $post->ID;
		$product     = wc_get_product( $post->ID );

		if ( ! $product ) {
			return array();
		}

		global $sitepress;
		$has_wpml = ! empty( $sitepress ) ? true : false;

		if ( $has_wpml && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
			$post_id = yit_wpml_object_id( $post_id, 'product', true, wpml_get_default_language() );
		}

		if ( 'yes' === apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_exclude_button' ), $post_id, '_ywctm_exclude_button' ) ) {
			return array();
		}

		if ( 'yes' === apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_custom_url_enabled' ), $post_id, '_ywctm_custom_url_enabled' ) ) {

			$button_text = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_button_text' ), $post_id, '_ywctm_button_text' );
			$protocol    = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_custom_url_protocol' ), $post_id, '_ywctm_custom_url_protocol' );
			$link        = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_custom_url_link' ), $post_id, '_ywctm_custom_url_link' );

		} else {

			$product_cats = wp_get_object_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );
			foreach ( $product_cats as $cat_id ) {

				if ( 'yes' === apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_exclude_button', true ), $post_id, $cat_id, '_ywctm_exclude_button' ) ) {
					return array();
				}

				if ( 'yes' === apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_custom_url_enabled', true ), $post_id, $cat_id, '_ywctm_custom_url_enabled' ) ) {
					if ( '' === $button_text ) {
						$button_text = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_button_text', true ), $post_id, $cat_id, '_ywctm_button_text' );
					}

					$protocol = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_custom_url_protocol', true ), $post_id, $cat_id, '_ywctm_custom_url_protocol' );
					$link     = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_custom_url_link', true ), $post_id, $cat_id, '_ywctm_custom_url_link' );
				}
			}

			if ( '' === $protocol ) {

				$product_tags = wp_get_object_terms( $post_id, 'product_tag', array( 'fields' => 'ids' ) );
				foreach ( $product_tags as $tag_id ) {

					if ( 'yes' === apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_exclude_button', true ), $post_id, $tag_id, '_ywctm_exclude_button' ) ) {
						return array();
					}

					if ( 'yes' === apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_custom_url_enabled', true ), $post_id, $tag_id, '_ywctm_custom_url_enabled' ) ) {
						if ( '' === $button_text ) {
							$button_text = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_button_text', true ), $post_id, $tag_id, '_ywctm_button_text' );
						}
						$protocol = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_custom_url_protocol', true ), $post_id, $tag_id, '_ywctm_custom_url_protocol' );
						$link     = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_custom_url_link', true ), $post_id, $tag_id, '_ywctm_custom_url_link' );
					}
				}
			}

			if ( '' === $protocol ) {
				$protocol = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_url_type' ), $post_id, 'ywctm_button_url_type' );
				$link     = apply_filters( 'ywctm_get_vendor_option', get_option( ( class_exists( 'SitePress' ) ? 'ywctm_button_url_wpml' : 'ywctm_button_url' ) ), $post_id, ( class_exists( 'SitePress' ) ? 'ywctm_button_url_wpml' : 'ywctm_button_url' ) );
			}
		}
		if ( '' === $button_text ) {
			$button_text = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_text' ), $post_id, 'ywctm_button_text' );
		}

		$button_url_type = 'generic' === $protocol ? '' : $protocol . ':';

		if ( class_exists( 'SitePress' ) && is_array( $link ) ) {

			$page_language = wpml_get_language_information( null, $post_id );
			$link          = $link[ $page_language['language_code'] ];

		}

		$button_url = '' === $link ? '#' : $link;
		$icon       = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_icon' ), $post_id, 'ywctm_button_icon' );

		return array(
			'label_text'              => $button_text,
			'text_color'              => '',
			'button_url'              => sprintf( '%s%s', $button_url_type, $button_url ),
			'background_color'        => '',
			'border_color'            => '',
			'border_style'            => '',
			'icon_type'               => $icon['select'],
			'selected_icon'           => str_replace( 'fa-', '', $icon['icon'] ),
			'selected_icon_size'      => 14,
			'selected_icon_alignment' => '',
			'custom_icon'             => $icon['custom'],
			'width_settings'          => '',
			'margin_settings'         => '',
			'padding_settings'        => '',
		);

	}

	add_filter( 'ywctm_button_label_settings', 'ywctm_return_legacy_settings', 10, 2 );
}

if ( ! function_exists( 'ywctm_legacy_button_classes' ) ) {

	/**
	 * Set "button" class for legacy buttons
	 *
	 * @param   $classes   string
	 * @param   $button_id string
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_legacy_button_classes( $classes, $button_id ) {
		if ( 'legacy' === $button_id ) {
			$classes = 'button';
		}

		return $classes;
	}

	add_filter( 'ywctm_custom_button_additional_classes', 'ywctm_legacy_button_classes', 10, 2 );
}

if ( ! function_exists( 'ywctm_legacy_styles' ) ) {

	/**
	 * Load legacy styles
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_legacy_styles() {

		if ( ! version_compare( YWCTM_VERSION, get_option( 'ywctm_update_version' ), '>' ) ) {
			return;
		}

		global $post;

		if ( empty( $post ) ) {
			return;
		}

		$post_id = $post->ID;

		global $sitepress;
		$has_wpml = ! empty( $sitepress ) ? true : false;

		if ( $has_wpml && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
			$post_id = yit_wpml_object_id( $post_id, 'product', true, wpml_get_default_language() );
		}

		$button_color          = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_color' ), $post_id, 'ywctm_button_color' );
		$button_hover_color    = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_hover' ), $post_id, 'ywctm_button_hover' );
		$button_bg_color       = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_bg_color' ), $post_id, 'ywctm_button_bg_color' );
		$button_bg_hover_color = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_button_bg_hover' ), $post_id, 'ywctm_button_bg_hover' );
		$button_custom_css     = '.ywctm-custom-button-container span.ywctm-icon-form { font-size: 20px; margin-right: 5px; }
		.ywctm-custom-button-container span.ywctm-icon-form,.ywctm-custom-button-container span.ywctm-icon-form:before,.ywctm-custom-button-container span.ywctm-inquiry-title { display: inline-block; vertical-align: middle; }
		.ywctm-inquiry-form-wrapper .ywctm-form-title { margin: 0 0 10px 0; font-size: 23px; font-weight: bold;}';

		if ( '' !== $button_color ) {
			$button_custom_css .= 'a.ywctm-custom-button {';
			$button_custom_css .= 'color:' . $button_color . '!important;';
			$button_custom_css .= 'background-color:' . $button_bg_color . '!important;';
			$button_custom_css .= '}';
		}

		if ( '' !== $button_hover_color ) {
			$button_custom_css .= 'a.ywctm-custom-button:hover {';
			$button_custom_css .= 'color:' . $button_hover_color . '!important;';
			$button_custom_css .= 'background-color:' . $button_bg_hover_color . '!important;';
			$button_custom_css .= '}';
		}

		wp_enqueue_style( 'ywctm-frontend', yit_load_css_file( YWCTM_ASSETS_URL . 'css/frontend.css' ), array(), YWCTM_VERSION );
		wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0' );
		wp_enqueue_style( 'ywctm-retinaicon-font', yit_load_css_file( YWCTM_ASSETS_URL . 'css/retinaicon-font.css' ), array(), YWCTM_VERSION );
		wp_add_inline_style( 'ywctm-frontend', $button_custom_css );
	}

	add_action( 'wp_enqueue_scripts', 'ywctm_legacy_styles', 25 );
}
