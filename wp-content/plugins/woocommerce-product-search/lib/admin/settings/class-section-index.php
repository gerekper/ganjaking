<?php
/**
 * class-section-index.php
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
 * Index section.
 */
class Section_Index extends \WooCommerce_Product_Search_Admin_Base {

	/**
	 * Records changes made to the settings.
	 */
	public static function save() {
		$settings = Settings::get_instance();
		if ( current_user_can( self::INDEXER_CONTROL_CAPABILITY ) ) {
			$show_in_admin_bar = isset( $_POST[\WooCommerce_Product_Search::SHOW_IN_ADMIN_BAR] );
			$settings->set( \WooCommerce_Product_Search::SHOW_IN_ADMIN_BAR, $show_in_admin_bar );

			$work_cycle = isset( $_POST[\WooCommerce_Product_Search_Worker::WORK_CYCLE] ) ? intval( $_POST[\WooCommerce_Product_Search_Worker::WORK_CYCLE] ) : \WooCommerce_Product_Search_Worker::get_work_cycle_default();
			if ( $work_cycle <= 0 ) {
				$work_cycle = \WooCommerce_Product_Search_Worker::get_work_cycle_default();
			}
			$settings->set( \WooCommerce_Product_Search_Worker::WORK_CYCLE, $work_cycle );

			$idle_cycle = isset( $_POST[\WooCommerce_Product_Search_Worker::IDLE_CYCLE] ) ? intval( $_POST[\WooCommerce_Product_Search_Worker::IDLE_CYCLE] ) : \WooCommerce_Product_Search_Worker::IDLE_CYCLE_DEFAULT;
			if ( $idle_cycle <= 0 ) {
				$idle_cycle = \WooCommerce_Product_Search_Worker::IDLE_CYCLE_DEFAULT;
			}
			$settings->set( \WooCommerce_Product_Search_Worker::IDLE_CYCLE, $idle_cycle );

			$index_per_cycle = isset( $_POST[\WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE] ) ? intval( $_POST[\WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE] ) : \WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE_DEFAULT;
			if ( $index_per_cycle <= 0 ) {
				$index_per_cycle = \WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE_DEFAULT;
			}
			$settings->set( \WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE, $index_per_cycle );

			$index_order = isset( $_POST[\WooCommerce_Product_Search_Indexer::INDEX_ORDER] ) ? $_POST[\WooCommerce_Product_Search_Indexer::INDEX_ORDER] : \WooCommerce_Product_Search_Indexer::INDEX_ORDER_DEFAULT;
			switch( $index_order ) {
				case \WooCommerce_Product_Search_Indexer::INDEX_ORDER_MOST_RECENT :
				case \WooCommerce_Product_Search_Indexer::INDEX_ORDER_LEAST_RECENT :
				case \WooCommerce_Product_Search_Indexer::INDEX_ORDER_MOST_RECENTLY_MODIFIED :
				case \WooCommerce_Product_Search_Indexer::INDEX_ORDER_LEAST_RECENTLY_MODIFIED :
					break;
				default :
					$index_order = \WooCommerce_Product_Search_Indexer::INDEX_ORDER_DEFAULT;
			}
			$settings->set( \WooCommerce_Product_Search_Indexer::INDEX_ORDER, $index_order );
		}
		$settings->save();
	}

	/**
	 * Renders the section.
	 */
	public static function render() {

		$settings = Settings::get_instance();

		wp_enqueue_script( 'wps-indexer' );

		echo '<h4>';
		echo esc_html( __( 'Search Index', 'woocommerce-product-search' ) );
		echo '</h4>';

		echo '<p>';
		esc_html_e( 'Indexing is an automated process which is usually free of manual intervention.', 'woocommerce-product-search' );
		echo ' ';
		esc_html_e( 'Normally, you would not need to modify the values here or stop the indexing process.', 'woocommerce-product-search' );
		echo ' ';
		esc_html_e( 'If the indexer is stopped while there are remaining entries left unprocessed, search results will not include all products.', 'woocommerce-product-search' );
		echo '</p>';

		echo '<h5>';
		esc_html_e( 'Status', 'woocommerce-product-search' );
		echo '</h5>';

		$status = \WooCommerce_Product_Search_Worker::get_status();
		$indexer = new \WooCommerce_Product_Search_Indexer();
		$processable = $indexer->get_processable_count();
		$total       = $indexer->get_total_count();
		if ( $total > 0 ) {
			$pct = 100 - $processable / $total * 100;
		} else {
			$pct = 100;
		}
		$next_scheduled_datetime = '&mdash;';
		if ( $next_scheduled = \WooCommerce_Product_Search_Worker::get_next_scheduled() ) {
			$next_scheduled_datetime = get_date_from_gmt( date( 'Y-m-d H:i:s', $next_scheduled ) );
		}

		echo '<div class="wps-index-status-display-wrapper">';
		esc_html_e( 'Indexed', 'woocommerce-product-search' );
		echo '&nbsp;';
		echo '<div id="wps-index-status-display" style="display:inline-block;padding:0 0.31em 0 1.618em;">';
		printf( '%.2f', $pct );
		echo '</div>';
		echo '%';

		echo '&nbsp;&nbsp;';

		echo '<div style="display:inline-block;padding:0 0.62em 0 1.618em;">';
		echo '&#91;&nbsp;';
		printf( '<div title="%s" id="wps-index-status-total" style="display:inline-block;padding:0;cursor:help;">', esc_attr__( 'Total', 'woocommerce-product-search' ) );
		echo esc_html( $total );
		echo '</div>';
		echo '&nbsp;&#47;&nbsp;';
		printf( '<div title="%s" id="wps-index-status-processable" style="display:inline-block;padding:0;cursor:help;">', esc_attr__( 'Remaining', 'woocommerce-product-search' ) );
		echo esc_html( $processable );
		echo '</div>';
		echo '&nbsp;&#93;';
		echo '</div>';

		echo '&nbsp;&nbsp;';

		echo '<div style="display:inline-block;padding:0 0.62em 0 1.618em;">';
		echo '&#91;&nbsp;';
		printf( '<div title="%s" id="wps-index-status-next-scheduled" style="display:inline-block;padding:0;cursor:help;">', esc_attr__( 'Next indexing cycle schedule', 'woocommerce-product-search' ) );
		echo esc_html( $next_scheduled_datetime );
		echo '</div>';
		echo '&nbsp;&#93;';
		echo '</div>';

		echo '</div>';

		$show_in_admin_bar = $settings->get( \WooCommerce_Product_Search::SHOW_IN_ADMIN_BAR, \WooCommerce_Product_Search::SHOW_IN_ADMIN_BAR_DEFAULT );
		echo '<p>';
		echo '<label>';
		printf( '<input name="%s" type="checkbox" %s />', esc_attr( \WooCommerce_Product_Search::SHOW_IN_ADMIN_BAR ), $show_in_admin_bar ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Show the index status in the Admin Bar', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';
		echo '<p class="description">';
		echo esc_html( __( 'If this option is enabled, status information is displayed in the Admin Bar until the index is completed.', 'woocommerce-product-search' ) );
		echo '</p>';

		$error = \WooCommerce_Product_Search_Worker::cron_test();
		if ( $error === null ) {
			echo '<p>';
			esc_html_e( 'Scheduled tasks (cron) seem to be working and the indexer should process products automatically.', 'woocommerce-product-search' );
			echo '</p>';
		} else {
			echo '<div class="wps-cron-error">';
			echo '<p>';
			esc_html_e( 'Scheduled tasks (cron) seem to be failing:', 'woocommerce-product-search' );
			echo '</p>';
			echo '<p>';
			echo '<code>';
			esc_html_e( $error->get_error_message() );
			echo '</code>';
			echo '</p>';
			echo '<p>';
			echo ' ';
			esc_html_e( 'If the index is not completing automatically, click the "Run" button to run the indexer once.', 'woocommerce-product-search' );
			echo ' ';
			esc_html_e( 'Repeat if necessary until all products have been indexed.', 'woocommerce-product-search' );
			echo '</p>';
			echo '</div>';
		}

		$js_nonce = wp_create_nonce( 'wps-index-js' );

		echo '<h5>';
		esc_html_e( 'Indexer', 'woocommerce-product-search' );
		echo '</h5>';

		$work_cycle      = $settings->get( \WooCommerce_Product_Search_Worker::WORK_CYCLE, \WooCommerce_Product_Search_Worker::get_work_cycle_default() );
		$idle_cycle      = $settings->get( \WooCommerce_Product_Search_Worker::IDLE_CYCLE, \WooCommerce_Product_Search_Worker::IDLE_CYCLE_DEFAULT );
		$index_per_cycle = $settings->get( \WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE, \WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE_DEFAULT );
		$index_order     = $settings->get( \WooCommerce_Product_Search_Indexer::INDEX_ORDER, \WooCommerce_Product_Search_Indexer::INDEX_ORDER_DEFAULT );

		$can_control = current_user_can( self::INDEXER_CONTROL_CAPABILITY );

		echo '<table>';

		echo '<tr>';
		echo '<td>';
		printf(
			'<label for="%s" title="%s">',
			esc_attr( \WooCommerce_Product_Search_Worker::WORK_CYCLE ),
			esc_attr__( 'The indexer will process unindexed entries periodically every indicated number of seconds, while there are unprocessed entries.', 'woocommerce-product-search' )
		);
		esc_html_e( 'Work Cycle', 'woocommerce-product-search' );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d" %s/>',
			esc_attr( \WooCommerce_Product_Search_Worker::WORK_CYCLE ),
			esc_attr( $work_cycle ),
			esc_attr( \WooCommerce_Product_Search_Worker::get_work_cycle_default() ),
			( $can_control ? '' : ' readonly="readonly" ' )
		);
		echo '&nbsp;';
		esc_html_e( 'seconds', 'woocommerce-product-search' );
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td>';
		printf(
			'<label for="%s" title="%s">',
			esc_attr( \WooCommerce_Product_Search_Worker::IDLE_CYCLE ),
			esc_attr__( 'The indexer will check for new entries periodically every indicated number of seconds, once all entries have been indexed.', 'woocommerce-product-search' )
		);
		echo esc_html( __( 'Idle Cycle', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%s" %s/>',
			esc_attr( \WooCommerce_Product_Search_Worker::IDLE_CYCLE ),
			esc_attr( $idle_cycle ),
			esc_attr( \WooCommerce_Product_Search_Worker::IDLE_CYCLE_DEFAULT ),
			( $can_control ? '' : ' readonly="readonly" ' )
		);
		echo '&nbsp;';
		esc_html_e( 'seconds', 'woocommerce-product-search' );
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td>';
		printf(
			'<label for="%s" title="%s">',
			esc_attr( \WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE ),
			esc_attr__( 'The indexer will try to process as many entries on each work cycle.', 'woocommerce-product-search' )
		);
		echo esc_html( __( 'Process', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<input name="%s" style="width:5em;text-align:right;" type="number" value="%d" placeholder="%d" %s/>',
			esc_attr( \WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE ),
			esc_attr( $index_per_cycle ),
			\WooCommerce_Product_Search_Indexer::INDEX_PER_CYCLE_DEFAULT,
			( $can_control ? '' : ' readonly="readonly" ' )
		);
		echo '&nbsp;';
		esc_html_e( 'entries', 'woocommerce-product-search' );
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td>';
		printf(
			'<label for="%s" title="%s">',
			esc_attr( \WooCommerce_Product_Search_Indexer::INDEX_ORDER ),
			esc_attr__( 'The indexer will process entries first as indicated.', 'woocommerce-product-search' )
		);
		echo esc_html( __( 'Order', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</td>';
		echo '<td>';
		printf(
			'<select name="%s" title="%s" %s>',
			esc_attr( \WooCommerce_Product_Search_Indexer::INDEX_ORDER ),
			esc_attr__( 'Index entries in this order', 'woocommerce-product-search' ),
			( $can_control ? '' : ' disabled="disabled" ' )
		);
		$index_orders = array(
			\WooCommerce_Product_Search_Indexer::INDEX_ORDER_MOST_RECENT => __( 'Most recent first', 'woocommerce-product-search' ),
			\WooCommerce_Product_Search_Indexer::INDEX_ORDER_LEAST_RECENT => __( 'Least recent first', 'woocommerce-product-search' ),
			\WooCommerce_Product_Search_Indexer::INDEX_ORDER_MOST_RECENTLY_MODIFIED => __( 'Most recently modified first', 'woocommerce-product-search' ),
			\WooCommerce_Product_Search_Indexer::INDEX_ORDER_LEAST_RECENTLY_MODIFIED => __( 'Least recently modified first', 'woocommerce-product-search' )
		);
		foreach( $index_orders as $index_order_key => $index_order_label ) {
			printf( '<option value="%s" %s>%s</option>',
				esc_attr( $index_order_key ),
				$index_order === $index_order_key ? ' selected="selected" ' : '',
				esc_html( $index_order_label )
			);
		}
		echo '</select>';
		echo '</td>';
		echo '</tr>';

		echo '</table>';

		if ( $can_control ) {
			echo '<p>';

			printf(
				'<input class="button wps-index-start-button" type="button" id="wps_index_start" name="wps_index_start" value="%s" title="%s" %s/>',
				esc_attr__( 'Start', 'woocommerce-product-search' ),
				esc_attr__( 'Start indexing &hellip;', 'woocommerce-product-search' ),
				$status ? ' disabled="disabled" ' : ''
			);

			echo '&emsp;';

			printf(
				'<input class="button wps-index-stop-button" type="button" id="wps_index_stop" name="wps_index_stop" value="%s" title="%s" %s/>',
				esc_attr__( 'Stop', 'woocommerce-product-search' ),
				esc_attr__( 'Stop indexing &hellip;', 'woocommerce-product-search' ),
				$status ? '' : ' disabled="disabled" '
			);

			echo '&emsp;';

			printf(
				'<input class="button wps-index-rebuild-button" type="button" id="wps_index_rebuild" name="wps_index_rebuild" value="%s" title="%s"/>',
				esc_attr__( 'Rebuild', 'woocommerce-product-search' ),
				esc_attr__( 'Completely rebuild the index &hellip;', 'woocommerce-product-search' )
			);

			echo '</p>';

		}

		echo '<div id="wps-index-status"></div>';
		echo '<div id="wps-index-update"></div>';
		echo '<div id="wps-index-blinker"></div>';

		if ( $can_control ) {
			echo '<p>';
			esc_html_e( 'If necessary, you can trigger the indexer manually here &hellip;', 'woocommerce-product-search' );
			echo ' ';
			printf(
				'<input class="button wps-index-run-button" type="button" id="wps_index_run" name="wps_index_run" value="%s" title="%s"/>',
				esc_attr__( 'Run', 'woocommerce-product-search' ),
				esc_attr__( 'Run the indexer once &hellip;', 'woocommerce-product-search' )
			);
			echo '</p>';
		}

		echo '<script type="text/javascript">';
		echo 'document.addEventListener( "DOMContentLoaded", function() {';
		echo 'if ( typeof jQuery !== "undefined" ) {';

		echo 'if ( typeof wpsIndexer !== "undefined" ) {';
		printf( 'wpsIndexer.msg_starting = "%s";', esc_html__( 'The indexer is starting &hellip;', 'woocommerce-produce-search' ) );
		printf( 'wpsIndexer.msg_started = "%s";', esc_html__( '&hellip; ready.', 'woocommerce-produce-search' ) );
		printf( 'wpsIndexer.msg_stopping = "%s";', esc_html__( 'The indexer is stopping &hellip;', 'woocommerce-produce-search' ) );
		printf( 'wpsIndexer.msg_stopped = "%s";', esc_html__( '&hellip; ready.', 'woocommerce-produce-search' ) );
		printf( 'wpsIndexer.msg_rebuilding = "%s";', esc_html__( 'The index is being rebuilt &hellip;', 'woocommerce-produce-search' ) );
		printf( 'wpsIndexer.msg_rebuilt = "%s";', esc_html__( 'The index has been cleared and is being rebuilt.', 'woocommerce-produce-search' ) );
		printf( 'wpsIndexer.msg_run = "%s";', esc_html__( 'The indexer is running once &hellip;', 'woocommerce-produce-search' ) );
		printf( 'wpsIndexer.msg_ran = "%s";', esc_html__( '&hellip; ready.', 'woocommerce-produce-search' ) );
		echo '}';

		echo 'jQuery("#wps_index_start").click(function(e){';
		echo 'e.stopPropagation();';
		echo 'jQuery("#wps-index-status").html("");';
		echo 'jQuery("#wps-index-update").html("");';
		echo 'jQuery(this).prop( "disabled", true );';

		printf(
			'wpsIndexer.start("%s","%s");',
			add_query_arg(
				array(
					'action' => 'wps_indexer',
					'cmd'    => 'start',
					'nonce'  => $js_nonce
				),
				admin_url( 'admin-ajax.php' )
			),
			self::get_admin_section_url( self::SECTION_INDEX )
		);

		echo '});';

		echo 'jQuery("#wps_index_stop").click(function(e){';
		echo 'e.stopPropagation();';
		echo 'jQuery("#wps-index-status").html("");';
		echo 'jQuery("#wps-index-update").html("");';
		echo 'jQuery(this).prop( "disabled", true );';

		printf(
			'wpsIndexer.stop("%s","%s");',
			add_query_arg(
				array(
					'action' => 'wps_indexer',
					'cmd'    => 'stop',
					'nonce'  => $js_nonce
				),
				admin_url( 'admin-ajax.php' )
			),
			self::get_admin_section_url( self::SECTION_INDEX )
		);

		echo '});';

		echo 'jQuery("#wps_index_rebuild").click(function(e){';
		echo 'e.stopPropagation();';
		printf(
			'if ( confirm("%s") ) {',
			esc_html__( 'Are you sure that you wish to rebuild the index completely?', 'woocommerce-product-search' ) .
			' ' .
			esc_html__( 'Please note that this will remove all indexes and create them from scratch.', 'woocommerce-product-search' ) .
			' ' .
			esc_html__( 'Especially for sites with a large product base, it is highly recommended to run this process only during low traffic hours.', 'woocommerce-product-search' )
		);
		echo 'jQuery("#wps-index-status").html("");';
		echo 'jQuery("#wps-index-update").html("");';
		echo 'jQuery(this).prop( "disabled", true );';

		printf(
			'wpsIndexer.rebuild("%s","%s");',
			add_query_arg(
				array(
					'action' => 'wps_indexer',
					'cmd'    => 'rebuild',
					'nonce'  => $js_nonce
				),
				admin_url( 'admin-ajax.php' )
			),
			self::get_admin_section_url( self::SECTION_INDEX )
		);
		echo '} else {';
		echo 'e.preventDefault();';
		echo '}';

		echo '});';

		echo 'jQuery("#wps_index_run").click(function(e){';
		echo 'e.stopPropagation();';
		echo 'jQuery("#wps-index-status").html("");';
		echo 'jQuery("#wps-index-update").html("");';
		echo 'jQuery(this).prop( "disabled", true );';

		printf(
			'wpsIndexer.run_once("%s","%s");',
			add_query_arg(
				array(
					'action' => 'wps_indexer',
					'cmd'    => 'run_once',
					'nonce'  => $js_nonce
				),
				admin_url( 'admin-ajax.php' )
				),
			self::get_admin_section_url( self::SECTION_INDEX )
			);

		echo '});';

		printf(
			'wpsIndexerStatus.url = "%s";',
			add_query_arg(
				array(
					'action' => 'wps_indexer',
					'cmd'    => 'status',
					'nonce'  => $js_nonce
				),
				admin_url( 'admin-ajax.php' )
			)
		);

		printf(
			'wpsIndexerStatus.cron = "%s";',
			add_query_arg(
				array(
					'doing_wp_cron' => 1
				),
				site_url( 'wp-cron.php' )
			)
		);

		echo '}';
		echo '} );';
		echo '</script>';
	}

}
