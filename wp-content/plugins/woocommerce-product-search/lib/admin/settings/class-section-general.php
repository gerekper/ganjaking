<?php
/**
 * class-section-general.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine\admin;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

use com\itthinx\woocommerce\search\engine\Settings;

/**
 * General Section.
 */
class Section_General extends \WooCommerce_Product_Search_Admin_Base {

	/**
	 * Records section settings.
	 */
	public static function save() {

		$settings = Settings::get_instance();

		$match_split = isset( $_POST[\WooCommerce_Product_Search_Service::MATCH_SPLIT] ) ? intval( $_POST[\WooCommerce_Product_Search_Service::MATCH_SPLIT] ) : \WooCommerce_Product_Search_Service::MATCH_SPLIT_DEFAULT;
		if ( $match_split < \WooCommerce_Product_Search_Service::MATCH_SPLIT_MIN || $match_split > \WooCommerce_Product_Search_Service::MATCH_SPLIT_MAX ) {
			$match_split = \WooCommerce_Product_Search_Service::MATCH_SPLIT_DEFAULT;
		}
		$settings->set( \WooCommerce_Product_Search_Service::MATCH_SPLIT, $match_split );
		$settings->set( \WooCommerce_Product_Search::RECORD_HITS, isset( $_POST[\WooCommerce_Product_Search::RECORD_HITS] ) );
		$settings->set( \WooCommerce_Product_Search::FILTER_PROCESS_DOM, isset( $_POST[\WooCommerce_Product_Search::FILTER_PROCESS_DOM] ) );
		$settings->set( \WooCommerce_Product_Search::FILTER_PARSE_DOM, isset( $_POST[\WooCommerce_Product_Search::FILTER_PARSE_DOM] ) );
		$settings->set( \WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY, isset( $_POST[\WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY] ) );
		$settings->set( \WooCommerce_Product_Search::LOG_QUERY_TIMES, isset( $_POST[\WooCommerce_Product_Search::LOG_QUERY_TIMES] ) );
		$settings->set( \WooCommerce_Product_Search::DELETE_DATA, isset( $_POST[\WooCommerce_Product_Search::DELETE_DATA] ) );

		$settings->set( \WooCommerce_Product_Search::USE_SHORT_DESCRIPTION, isset( $_POST[\WooCommerce_Product_Search::USE_SHORT_DESCRIPTION] ) );

		$max_title_words = isset( $_POST[\WooCommerce_Product_Search::MAX_TITLE_WORDS] ) && ( $_POST[\WooCommerce_Product_Search::MAX_TITLE_WORDS] !== '' ) ? intval( $_POST[\WooCommerce_Product_Search::MAX_TITLE_WORDS] ) : \WooCommerce_Product_Search::MAX_TITLE_WORDS_DEFAULT;
		if ( $max_title_words < 0 ) {
			$max_title_words = \WooCommerce_Product_Search::MAX_TITLE_WORDS_DEFAULT;
		}
		$settings->set( \WooCommerce_Product_Search::MAX_TITLE_WORDS, $max_title_words );

		$max_title_characters = isset( $_POST[\WooCommerce_Product_Search::MAX_TITLE_CHARACTERS] ) && ( $_POST[\WooCommerce_Product_Search::MAX_TITLE_CHARACTERS] !== '' ) ? intval( $_POST[\WooCommerce_Product_Search::MAX_TITLE_CHARACTERS] ) : \WooCommerce_Product_Search::MAX_TITLE_CHARACTERS_DEFAULT;
		if ( $max_title_characters < 0 ) {
			$max_title_characters = \WooCommerce_Product_Search::MAX_TITLE_CHARACTERS_DEFAULT;
		}
		$settings->set( \WooCommerce_Product_Search::MAX_TITLE_CHARACTERS, $max_title_characters );

		$max_excerpt_words = isset( $_POST[\WooCommerce_Product_Search::MAX_EXCERPT_WORDS] ) && ( $_POST[\WooCommerce_Product_Search::MAX_EXCERPT_WORDS] !== '' ) ? intval( $_POST[\WooCommerce_Product_Search::MAX_EXCERPT_WORDS] ) : \WooCommerce_Product_Search::MAX_EXCERPT_WORDS_DEFAULT;
		if ( $max_excerpt_words < 0 ) {
			$max_excerpt_words = \WooCommerce_Product_Search::MAX_EXCERPT_WORDS_DEFAULT;
		}
		$settings->set( \WooCommerce_Product_Search::MAX_EXCERPT_WORDS, $max_excerpt_words );

		$max_excerpt_characters = isset( $_POST[\WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS] ) && ( $_POST[\WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS] !== '' ) ? intval( $_POST[\WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS] ) : \WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS_DEFAULT;
		if ( $max_excerpt_characters < 0 ) {
			$max_excerpt_characters = \WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS_DEFAULT;
		}
		$settings->set( \WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS, $max_excerpt_characters );

		$settings->set( \WooCommerce_Product_Search::AUTO_REPLACE, isset( $_POST[\WooCommerce_Product_Search::AUTO_REPLACE] ) );
		if ( WPS_EXT_PDS ) {
			$settings->set( \WooCommerce_Product_Search::AUTO_REPLACE_ADMIN, isset( $_POST[\WooCommerce_Product_Search::AUTO_REPLACE_ADMIN] ) );
			$settings->set( \WooCommerce_Product_Search::AUTO_REPLACE_JSON, isset( $_POST[\WooCommerce_Product_Search::AUTO_REPLACE_JSON] ) );
			$json_limit = '';
			if ( isset( $_POST[\WooCommerce_Product_Search::JSON_LIMIT] ) ) {
				if ( trim( $_POST[\WooCommerce_Product_Search::JSON_LIMIT] ) !== '' ) {
					$json_limit = intval( $_POST[\WooCommerce_Product_Search::JSON_LIMIT] );
					if ( $json_limit < 0 ) {
						$json_limit = \WooCommerce_Product_Search::JSON_LIMIT_DEFAULT;
					}
				}
			}
			$settings->set( \WooCommerce_Product_Search::JSON_LIMIT, $json_limit );
		}
		if ( WPS_EXT_REST ) {
			$settings->set( \WooCommerce_Product_Search::AUTO_REPLACE_REST, isset( $_POST[\WooCommerce_Product_Search::AUTO_REPLACE_REST] ) );
		}

		$settings->set( \WooCommerce_Product_Search::AUTO_REPLACE_FORM, isset( $_POST[\WooCommerce_Product_Search::AUTO_REPLACE_FORM] ) );
		if ( $settings->get( \WooCommerce_Product_Search::AUTO_REPLACE_FORM ) ) {
			$old_instance = $settings->get( \WooCommerce_Product_Search::AUTO_INSTANCE, \WooCommerce_Product_Search_Widget::get_auto_instance_default() );
			$search_widget_instance = new \WooCommerce_Product_Search_Widget( 'wps-auto-instance' );
			$search_widget_instance->_set( 1 );
			$field_names = array(

				'query_title',
				'excerpt',
				'content',
				'categories',
				'tags',
				'attributes',
				'sku',
				'order_by',
				'order',
				'limit',
				'show_more',
				'category_results',
				'category_limit',
				'product_thumbnails',
				'show_description',
				'show_price',
				'show_add_to_cart',

				'delay',
				'characters',
				'inhibit_enter',
				'navigable',
				'placeholder',
				'show_clear',
				'submit_button',
				'submit_button_label',
				'dynamic_focus',
				'floating',
				'no_results',
				'height',
				'wpml'
			);
			$new_instance = array();
			foreach( $field_names as $field_name ) {
				$field = $search_widget_instance->get_field_name( $field_name );
				$parts = explode( ' ', trim( preg_replace( '/[ \[\]]+/', ' ', $field ) ) );
				$sub = $_POST;
				$n = count( $parts );
				$i = 0;
				$value = null;
				foreach( $parts as $part ) {
					if ( isset( $sub[$part] ) ) {
						$i++;
						$sub = $sub[$part];
						if ( $i === $n ) {
							$value = $sub;
						}
					} else {
						break;
					}
				}
				if ( $value !== null ) {
					$new_instance[$field_name] = stripslashes( $value );
				}
			}
			$new_instance = $search_widget_instance->update( $new_instance, $old_instance );
			$settings->set( \WooCommerce_Product_Search::AUTO_INSTANCE, $new_instance );
		} else {
			$settings->delete( \WooCommerce_Product_Search::AUTO_INSTANCE );
		}

		$settings->save();
	}

	/**
	 * Renders the section.
	 */
	public static function render() {

		$settings = Settings::get_instance();

		$auto_replace       = $settings->get( \WooCommerce_Product_Search::AUTO_REPLACE, \WooCommerce_Product_Search::AUTO_REPLACE_DEFAULT );
		$auto_replace_admin = $settings->get( \WooCommerce_Product_Search::AUTO_REPLACE_ADMIN, \WooCommerce_Product_Search::AUTO_REPLACE_ADMIN_DEFAULT );
		$auto_replace_json  = $settings->get( \WooCommerce_Product_Search::AUTO_REPLACE_JSON, \WooCommerce_Product_Search::AUTO_REPLACE_JSON_DEFAULT );
		$json_limit         = $settings->get( \WooCommerce_Product_Search::JSON_LIMIT, \WooCommerce_Product_Search::JSON_LIMIT_DEFAULT );
		if ( $json_limit !== '' ) {
			$json_limit = intval( $json_limit );
		}
		$auto_replace_rest  = $settings->get( \WooCommerce_Product_Search::AUTO_REPLACE_REST, \WooCommerce_Product_Search::AUTO_REPLACE_REST_DEFAULT );
		$auto_replace_form  = $settings->get( \WooCommerce_Product_Search::AUTO_REPLACE_FORM, \WooCommerce_Product_Search::AUTO_REPLACE_FORM_DEFAULT );
		$auto_instance      = $settings->get( \WooCommerce_Product_Search::AUTO_INSTANCE, \WooCommerce_Product_Search_Widget::get_auto_instance_default() );

		$use_short_description  = $settings->get( \WooCommerce_Product_Search::USE_SHORT_DESCRIPTION, \WooCommerce_Product_Search::USE_SHORT_DESCRIPTION_DEFAULT );
		$max_title_words        = intval( $settings->get( \WooCommerce_Product_Search::MAX_TITLE_WORDS, \WooCommerce_Product_Search::MAX_TITLE_WORDS_DEFAULT ) );
		$max_title_characters   = intval( $settings->get( \WooCommerce_Product_Search::MAX_TITLE_CHARACTERS, \WooCommerce_Product_Search::MAX_TITLE_CHARACTERS_DEFAULT ) );
		$max_excerpt_words      = intval( $settings->get( \WooCommerce_Product_Search::MAX_EXCERPT_WORDS, \WooCommerce_Product_Search::MAX_EXCERPT_WORDS_DEFAULT ) );
		$max_excerpt_characters = intval( $settings->get( \WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS, \WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS_DEFAULT ) );

		$match_split        = intval( $settings->get( \WooCommerce_Product_Search_Service::MATCH_SPLIT, \WooCommerce_Product_Search_Service::MATCH_SPLIT_DEFAULT ) );
		$record_hits        = $settings->get( \WooCommerce_Product_Search::RECORD_HITS, \WooCommerce_Product_Search::RECORD_HITS_DEFAULT );
		$filter_process_dom = $settings->get( \WooCommerce_Product_Search::FILTER_PROCESS_DOM, \WooCommerce_Product_Search::FILTER_PROCESS_DOM_DEFAULT );
		$filter_parse_dom   = $settings->get( \WooCommerce_Product_Search::FILTER_PARSE_DOM, \WooCommerce_Product_Search::FILTER_PARSE_DOM_DEFAULT );
		$service_get_terms_args_apply = $settings->get( \WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY, \WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY_DEFAULT );
		$log_query_times    = $settings->get( \WooCommerce_Product_Search::LOG_QUERY_TIMES, \WooCommerce_Product_Search::LOG_QUERY_TIMES_DEFAULT );
		$delete_data        = $settings->get( \WooCommerce_Product_Search::DELETE_DATA, false );

		echo '<div id="product-search-general-tab" class="product-search-tab">';

		echo '<h3 class="section-heading">' . esc_html( __( 'General Settings', 'woocommerce-product-search' ) ) . '</h3>';

		echo '<h4>';
		echo esc_html( __( 'Standard Product Search', 'woocommerce-product-search' ) );
		echo '</h4>';

		echo '<p>';
		echo '<label>';
		printf( '<input name="%s" type="checkbox" %s />', \WooCommerce_Product_Search::AUTO_REPLACE, $auto_replace ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Optimize front end product searches', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		esc_html_e( 'If enabled and where possible, front end product searches will provide results powered by the search engine.', 'woocommerce-product-search' );
		echo '</p>';

		if ( !WPS_EXT_PDS ) {
			echo '<p>';
			esc_html_e( 'Back end and JSON searches are disabled via WPS_EXT_PDS.', 'woocommerce-product-search' );
			echo '</p>';
		}

		echo '<p>';
		echo '<label>';
		printf(
			'<input name="%s" type="checkbox" %s %s />',
			\WooCommerce_Product_Search::AUTO_REPLACE_ADMIN,
			$auto_replace_admin ? ' checked="checked" ' : '',
			WPS_EXT_PDS ? '' : ' disabled="disabled" '
		);
		echo ' ';
		echo esc_html( __( 'Optimize back end product searches', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		esc_html_e( 'If enabled and where possible, back end product searches will provide results powered by the search engine.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<p>';
		echo '<label>';
		printf(
			'<input name="%s" type="checkbox" %s %s />',
			\WooCommerce_Product_Search::AUTO_REPLACE_JSON,
			$auto_replace_json ? ' checked="checked" ' : '',
			WPS_EXT_PDS ? '' : ' disabled="disabled" '
		);
		echo ' ';
		echo esc_html( __( 'Optimize JSON product searches', 'woocommerce-product-search' ) );
		echo '</label>';
		echo ' ';
		echo wc_help_tip( __( 'If enabled, JSON product searches are powered by the search engine when possible.', 'woocommerce-product-search' ) );
		echo ' &mdash; ';
		printf( '<label title="%s">', __( 'Limit JSON product search results', 'woocommerce-product-search' ) );
		echo esc_html( _x( 'Limit', 'Limit JSON product search results' , 'woocommerce-product-search' ) );
		echo ' ';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%s" placeholder="%s" %s/>',
			esc_attr( \WooCommerce_Product_Search::JSON_LIMIT ),
			esc_attr( $json_limit ),
			esc_attr__( 'inherit', 'woocommerce-product-search' ),
			WPS_EXT_PDS ? '' : ' disabled="disabled" '
		);
		echo '</label>';
		echo ' ';
		echo wc_help_tip(
			__( 'The number of results is capped by the limit.', 'woocommerce-product-search' ) .
			' ' .
			__( 'For unlimited results, use 0 (zero).', 'woocommerce-product-search' ) .
			' ' .
			__( 'Leave empty to use internal limits.', 'woocommerce-product-search' )
		);
		echo '</p>';
		echo '<p class="description">';
		echo ' ';
		echo esc_html__( 'These are used to look up products in fields.', 'woocommerce-product-search' );
		echo ' ';
		echo wc_help_tip( __( 'Test the current configuration with this field &hellip;', 'woocommerce-product-search' ) );
		echo ' ';
		echo '<form action="" method="post">';
		echo '<select class="wc-product-search" multiple="multiple" style="width: 33%;" id="add_item_id" name="add_order_items[]" data-placeholder="';
		echo esc_attr__( 'Search for a product&hellip;', 'woocommerce' );
		echo '"></select>';
		echo '</form>';
		echo '</p>';

		echo '<p>';
		echo '<label>';
		printf(
			'<input name="%s" type="checkbox" %s %s />',
			\WooCommerce_Product_Search::AUTO_REPLACE_REST,
			$auto_replace_rest ? ' checked="checked" ' : '',
			WPS_EXT_REST ? '' : ' disabled="disabled" '
		);
		echo ' ';
		echo esc_html( __( 'Optimize REST API product searches', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		esc_html_e( 'If enabled and where possible, product searches via the REST API will provide results powered by the search engine.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<p>';
		echo '<label>';
		printf( '<input id="wps-auto-replace-form-checkbox" name="%s" type="checkbox" %s />', \WooCommerce_Product_Search::AUTO_REPLACE_FORM, $auto_replace_form ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Replace the standard product search form', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		esc_html_e( 'If enabled and where possible, the standard product search form is replaced automatically with the advanced Product Search Field.', 'woocommerce-product-search' );
		echo ' ';
		esc_html_e( 'This provides the same functionality and options as the Product Search Field widget.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<div id="wps-auto-replace-instance-options" style="padding: 4px; border-radius: 4px; border: 1px dotted #aaa;">';
		echo '<p class="description">';
		esc_html_e( 'Product Search Field settings &hellip;', 'woocommerce-product-search' );
		echo '</p>';
		$search_widget_instance = new \WooCommerce_Product_Search_Widget( 'wps-auto-instance' );
		$search_widget_instance->_set( 1 );
		echo $search_widget_instance->form( $auto_instance );
		echo '</div>';

		echo '<script type="text/javascript">';
		echo 'document.addEventListener( "DOMContentLoaded", function() {';
		echo 'if ( typeof jQuery !== "undefined" ) {';
		echo 'jQuery("#wps-auto-replace-instance-options").toggle(jQuery("#wps-auto-replace-form-checkbox").is(":checked"));';
		echo 'jQuery(document).on( "click", "#wps-auto-replace-form-checkbox", function() {';
		echo 'jQuery("#wps-auto-replace-instance-options").toggle(this.checked);';
		echo '});';
		echo '}';
		echo '} );';
		echo '</script>';

		echo '<h4>';
		echo esc_html( __( 'Shorten Titles and Descriptions', 'woocommerce-product-search' ) );
		echo '</h4>';

		echo '<p class="description">';
		esc_html_e( 'The results shown with the Product Search Field can have their titles and descriptions automatically shortened.', 'woocommerce-product-search' );
		echo ' ';
		esc_html_e( 'Use any number higher than 0 to limit the number of words or characters shown.', 'woocommerce-product-search' );
		echo ' ';
		esc_html_e( 'Where 0 is indicated, no limit applies.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<table>';
		echo '<tr>';

		echo '<td>';
		printf(
			'<label for="%s" title="%s">',
			esc_attr( \WooCommerce_Product_Search::MAX_TITLE_WORDS ),
			esc_attr__( 'The maximum number of words shown in titles.', 'woocommerce-product-search' )
		);
		echo esc_html( __( 'Words in Titles', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
			esc_attr( \WooCommerce_Product_Search::MAX_TITLE_WORDS ),
			esc_attr( $max_title_words ),
			esc_attr( \WooCommerce_Product_Search::MAX_TITLE_WORDS_DEFAULT )
		);
		echo '</td>';

		echo '<td>';
		printf(
			'<label for="%s" title="%s">',
			esc_attr( \WooCommerce_Product_Search::MAX_EXCERPT_WORDS ),
			esc_attr__( 'The maximum number of words shown in short descriptions.', 'woocommerce-product-search' )
		);
		echo esc_html( __( 'Words in Descriptions', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
			esc_attr( \WooCommerce_Product_Search::MAX_EXCERPT_WORDS ),
			esc_attr( $max_excerpt_words ),
			esc_attr( \WooCommerce_Product_Search::MAX_EXCERPT_WORDS_DEFAULT )
		);
		echo '</td>';

		echo '</tr>';
		echo '<tr>';

		echo '<td>';
		printf(
			'<label for="%s" title="%s">',
			esc_attr( \WooCommerce_Product_Search::MAX_TITLE_CHARACTERS ),
			esc_attr__( 'The maximum number of characters shown in titles.', 'woocommerce-product-search' )
		);
		echo esc_html( __( 'Characters in Titles', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
			esc_attr( \WooCommerce_Product_Search::MAX_TITLE_CHARACTERS ),
			esc_attr( $max_title_characters ),
			esc_attr( \WooCommerce_Product_Search::MAX_TITLE_CHARACTERS_DEFAULT )
		);
		echo '</td>';
		echo '<td>';
		printf(
			'<label for="%s" title="%s">',
			esc_attr( \WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS ),
			esc_attr__( 'The maximum number of characters shown in short descriptions.', 'woocommerce-product-search' )
		);
		echo esc_html( __( 'Characters in Descriptions', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';

		echo '<td>';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d"/>',
			esc_attr( \WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS ),
			esc_attr( $max_excerpt_characters ),
			esc_attr( \WooCommerce_Product_Search::MAX_EXCERPT_CHARACTERS_DEFAULT )
		);
		echo '</td>';
		echo '</tr>';
		echo '</table>';

		echo '<p>';
		echo '<label>';
		printf( '<input name="%s" type="checkbox" %s />', esc_attr( \WooCommerce_Product_Search::USE_SHORT_DESCRIPTION ), $use_short_description ? ' checked="checked" ' : '' );
		echo ' ';
		esc_html_e( 'Use product short descriptions', 'woocommerce-product-search' );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		esc_html_e( 'If enabled and where a product\'s short description is not empty, this is used to display the (shortened) description in the results of the Product Search Field.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<h4>';
		echo esc_html( __( 'Search Term Threshold', 'woocommerce-product-search' ) );
		echo '</h4>';

		echo '<p class="description">';
		esc_html_e( 'Searches are quicker for exact matches and take a bit longer for similar words &ndash; those that start with the search term.', 'woocommerce-product-search' );
		echo ' ';
		esc_html_e( 'Exact matches may result in fewer search results, as search terms with a length below the threshold will only produce matches if the exact term is found.', 'woocommerce-product-search' );
		echo ' ';
		esc_html_e( 'Similar matches produce more search results when the length of the search terms is at or above the treshold.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<table>';
		echo '<tr>';
		echo '<td>';
		printf(
			'<label for="%s" title="%s">',
			esc_attr( \WooCommerce_Product_Search_Service::MATCH_SPLIT ),
			esc_attr(
				__( 'This determines the minimum length of a search term for similar matches to be retrieved.', 'woocommerce-product-search' ) .
				' ' .
				__( 'Search terms that are shorter will only produce matches if the exact term is found.', 'woocommerce-product-search' ) .
				' ' .
				__( 'When set to 0, no minimum length is required for similar matches to be retrieved.', 'woocommerce-product-search' ) .
				' ' .
				__( 'In this context, &ndash;similar&ndash; means words starting with the search term.', 'woocommerce-product-search' )
			)
		);
		echo esc_html( __( 'Threshold', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<select name="%s" title="%s">',
			esc_attr( \WooCommerce_Product_Search_Service::MATCH_SPLIT ),
			esc_attr__( 'Minimum word length to search for similar occurrences.', 'woocommerce-product-search' )
		);
		for ( $i = \WooCommerce_Product_Search_Service::MATCH_SPLIT_MIN; $i <= \WooCommerce_Product_Search_Service::MATCH_SPLIT_MAX; $i++ ) {
			switch( $i ) {
				case 0 :
					$info = __( 'always produce any similar matches', 'woocommerce-product-search' );
					break;
				default :
					$info = sprintf( __( 'at least %d for similar terms', 'woocommerce-product-search' ), $i );
			}
			printf( '<option value="%d" %s>%s</option>',
				esc_attr( $i ),
				$i === $match_split ? ' selected="selected" ' : '',
				esc_html( $i ) . '&nbsp;&mdash;&nbsp;' . esc_attr( $info )
			);
		}
		echo '</select>';
		echo '&nbsp;';
		esc_html_e( 'characters', 'woocommerce-product-search' );
		echo '</td>';
		echo '</tr>';
		echo '</table>';

		echo '<h4>';
		echo esc_html( __( 'Statistics', 'woocommerce-product-search' ) );
		echo '</h4>';

		echo '<p>';
		echo '<label>';
		printf( '<input name="%s" type="checkbox" %s />', esc_attr( \WooCommerce_Product_Search::RECORD_HITS ), $record_hits ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Record live search data', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		esc_html_e( 'If enabled, statistical data for product searches is recorded.', 'woocommerce-product-search' );
		echo ' ';
		echo wp_kses(
			sprintf(
				__( 'Live search statistics are available in the <a href="%s">Search</a> section of the reports.', 'woocommerce-product-search' ),
				esc_url( \WooCommerce_Product_Search_Admin_Navigation::get_report_url( 'searches' ) )
			),
			array( 'a' => array( 'href' => array(), 'class' => array() ) )
		);
		echo '</p>';

		echo '<h4>';
		echo esc_html( __( 'Optimization', 'woocommerce-product-search' ) );
		echo '</h4>';

		echo '<p>';
		echo '<label>';
		printf( '<input name="%s" type="checkbox" %s />', esc_attr( \WooCommerce_Product_Search::FILTER_PROCESS_DOM ), $filter_process_dom ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Optimize responses for filter requests', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		esc_html_e( 'If enabled, the HTML response for filter requests is optimized by removing unnecessary elements.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<p>';
		echo '<label>';
		printf( '<input name="%s" type="checkbox" %s />', esc_attr( \WooCommerce_Product_Search::FILTER_PARSE_DOM ), $filter_parse_dom ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Use accurate optimization', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		esc_html_e( 'If enabled, a more accurate algorithm is used to process filter responses.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<h4>';
		echo esc_html( __( 'Filters', 'woocommerce-product-search' ) );
		echo '</h4>';

		echo '<p>';
		echo '<label>';
		printf( '<input name="%s" type="checkbox" %s />', esc_attr( \WooCommerce_Product_Search::SERVICE_GET_TERMS_ARGS_APPLY ), $service_get_terms_args_apply ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Apply product filters in general', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		esc_html_e( 'If enabled, the current choice of product filters can affect the choices offered for products, product categories, product tags and product attributes presented more broadly, beyond the facilities provided by the extension.', 'woocommerce-product-search' );
		echo ' ';
		esc_html_e( 'For example, if enabled, this can affect the product categories displayed with the standard Product Categories widget, reducing those displayed to the set of matching product categories only.', 'woocommerce-product-search' );

		echo '</p>';

		echo '<h4>';
		echo esc_html( __( 'Logs', 'woocommerce-product-search' ) );
		echo '</h4>';

		echo '<p>';
		echo '<label>';
		printf( '<input name="%s" type="checkbox" %s />', esc_attr( \WooCommerce_Product_Search::LOG_QUERY_TIMES ), $log_query_times ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Log main query times', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		printf(
			wp_kses(
				__( 'If enabled, the query times for search terms will be logged. <a href="https://codex.wordpress.org/Debugging_in_WordPress">Debugging</a> must be enabled for query times to be recorded in the log.', 'woocommerce-product-search' ),
				array( 'a' => array( 'href' => array() ) )
			)
		);
		echo '</p>';

		echo '<h4>';
		echo esc_html( __( 'Delete Data', 'woocommerce-product-search' ) );
		echo '</h4>';

		echo '<p>';
		echo '<label>';
		printf( '<input name="%s" type="checkbox" %s />', esc_attr( \WooCommerce_Product_Search::DELETE_DATA ), $delete_data ? ' checked="checked" ' : '' );
		echo ' ';
		esc_html_e( 'Delete search settings and data on deactivation', 'woocommerce-product-search' );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		esc_html_e( 'This option will delete ALL search settings and data when the WooCommerce Product Search extension is deactivated.', 'woocommerce-product-search' );
		echo ' ';
		echo '<strong>';
		esc_html_e( 'This action cannot be reversed.', 'woocommerce-product-search' );
		echo '</strong>';
		echo '</p>';
		echo '<p class="description">';
		esc_html_e( 'CAUTION: If this option is active while the plugin is deactivated, ALL plugin settings and data will be DELETED.', 'woocommerce-product-search' );
		echo ' ';
		esc_html_e( 'This includes the removal of all search settings, search weights for products and terms and the associations of search thumbnail images with product categories, tags and attributes.', 'woocommerce-product-search' );
		echo ' ';
		esc_html_e( 'If you are going to use this option, NOW would be a good time to make a backup of your site and its database.', 'woocommerce-product-search' );
		echo '</p>';

		echo '</div>';
	}

}
