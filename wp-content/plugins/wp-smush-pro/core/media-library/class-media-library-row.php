<?php

namespace Smush\Core\Media_Library;

use Smush\Core\Helper;
use Smush\Core\Media\Media_Item;
use Smush\Core\Media\Media_Item_Cache;
use Smush\Core\Media\Media_Item_Optimizer;
use Smush\Core\Media\Media_Item_Stats;
use Smush\Core\Settings;
use Smush\Core\Smush\Smush_Optimization;
use Smush\Core\Stats\Global_Stats;
use WP_Error;
use WP_Smush;

class Media_Library_Row {
	/**
	 * @var int
	 */
	private $attachment_id;

	/**
	 * @var WP_Error
	 */
	private $errors;

	/**
	 * @var Media_Item_Optimizer
	 */
	private $optimizer;

	/**
	 * @var Media_Item
	 */
	private $media_item;
	/**
	 * @var Global_Stats
	 */
	private $global_stats;
	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( $attachment_id ) {
		$this->attachment_id = $attachment_id;
		$this->media_item    = Media_Item_Cache::get_instance()->get( $this->attachment_id );
		$this->global_stats  = Global_Stats::get();
		$this->optimizer     = new Media_Item_Optimizer( $this->media_item );
		$this->errors        = $this->prepare_errors();
		$this->settings      = Settings::get_instance();
	}

	private function prepare_errors() {
		$error_list = $this->global_stats->get_error_list();
		if (
			$error_list->has_id( $this->attachment_id )
			|| ( ! $this->media_item->has_wp_metadata() && $this->media_item->is_mime_type_supported() )
		) {
			return $this->media_item->get_errors();
		}

		if ( $this->optimizer->has_errors() ) {
			return $this->optimizer->get_errors();
		}

		return new WP_Error();
	}

	/**
	 * @return string
	 */
	public function generate_markup() {
		if ( ! $this->media_item->is_image() || ! $this->media_item->is_mime_type_supported() ) {
			return esc_html__( 'Not processed', 'wp-smushit' );
		}

		if ( $this->optimizer->in_progress() || $this->optimizer->restore_in_progress() ) {
			return esc_html__( 'File processing is in progress.', 'wp-smushit' );
		}

		if ( $this->media_item->is_animated() ) {
			return $this->generate_markup_for_animated_item();
		}

		$has_error = $this->errors->has_errors();
		if ( $has_error && $this->media_item->size_limit_exceeded() ) {
			return $this->generate_markup_for_size_limited_item();
		}

		// Render ignored after animated/size limited to show upsell even ignored the image.
		// And render ignored before media item failed to show Ignored message when the image is ignored.
		if ( $this->media_item->is_ignored() ) {
			return $this->generate_markup_for_ignored_item();
		}

		if ( $has_error && $this->media_item->has_errors() ) {
			return $this->generate_markup_for_failed_item();
		}

		if ( $this->is_first_optimization_required() && ! $has_error ) {
			return $this->generate_markup_for_unsmushed_item();
		}

		return $this->generate_markup_for_smushed_item( $this->optimizer->get_total_stats() );
	}

	private function is_first_optimization_required() {
		return ! $this->optimizer->is_optimized() && $this->optimizer->should_optimize();
	}

	private function generate_markup_for_animated_item() {
		$error_message = esc_html__( 'Skipped animated file.', 'wp-smushit' );
		$utm_link      = $this->get_animated_html_utm_link();

		return $this->get_html_markup_for_failed_item_with_utm_link( $error_message, $utm_link );
	}

	private function get_animated_html_utm_link() {
		if ( WP_Smush::is_pro() ) {
			return $this->get_animated_cdn_notice_with_config_link();
		}

		return $this->get_html_utm_link(
			__( 'Upgrade to Serve GIFs faster with CDN.', 'wp-smushit' ),
			'smush_bulksmush_library_gif_cdn'
		);
	}

	private function get_animated_cdn_notice_with_config_link() {
		$cdn = WP_Smush::get_instance()->core()->mod->cdn;
		if ( $cdn->get_status() ) {
			return '<span class="smush-cdn-notice">' . esc_html__( 'GIFs are serving from global CDN', 'wp-smushit' ) . '</span>';
		}
		$cdn_link = Helper::get_page_url( 'smush-cdn' );

		return '<span class="smush-cdn-notice">' . sprintf(
			/* translators: %1$s : Open a link %2$s Close the link */
			esc_html__( '%1$sEnable CDN%2$s to serve GIFs closer and faster to visitors', 'wp-smushit' ),
			'<a href="' . esc_url( $cdn_link ) . '" target="_blank">',
			'</a>'
		) . '</span>';
	}

	private function get_html_utm_link( $utm_message, $utm_campain ) {
		$upgrade_url = 'https://wpmudev.com/project/wp-smush-pro/';
		$args        = array(
			'utm_source'   => 'smush',
			'utm_medium'   => 'plugin',
			'utm_campaign' => $utm_campain,
		);
		$utm_link    = add_query_arg( $args, $upgrade_url );

		return sprintf( '<a class="smush-upgrade-link" href="%1$s" target="_blank">%2$s</a>', esc_url( $utm_link ), esc_html( $utm_message ) );
	}

	private function get_html_markup_for_failed_item_with_utm_link( $error_message, $utm_link = '' ) {
		if ( $this->media_item->is_ignored() ) {
			$links = $this->get_revert_with_utm_link( $utm_link );
		} else {
			$links = $this->get_ignore_with_utm_link( $utm_link );
		}

		return $this->get_html_markup_for_failed_item( $error_message, $links );
	}

	private function get_revert_with_utm_link( $utm_link = '' ) {
		$class_names = array();
		$links       = $utm_link;
		if ( ! empty( $utm_link ) ) {
			$class_names[] = 'smush-revert-utm';
		}
		$links .= $this->get_revert_link( $class_names );

		return $links;
	}

	private function get_revert_link( $class_names = array() ) {
		$nonce         = wp_create_nonce( 'wp-smush-remove-skipped' );
		$class_names[] = 'wp-smush-remove-skipped'; // smush-revert-utm

		return sprintf(
			'<a href="#" class="%1$s" data-id="%2$d" data-nonce="%3$s">%4$s</a>',
			esc_attr( join( ' ', $class_names ) ),
			$this->attachment_id,
			$nonce,
			esc_html__( 'Revert back to previous state', 'wp-smushit' ) . '</a>'
		);
	}

	private function get_ignore_with_utm_link( $utm_link = '' ) {
		$class_names = array();
		$links       = $utm_link;
		if ( ! empty( $utm_link ) ) {
			$class_names[] = ' smush-ignore-utm';
		}
		$links .= $this->get_ignore_link( $class_names );

		return $links;
	}

	private function get_ignore_link( $class_names = array() ) {
		$class_names[] = 'smush-ignore-image';

		return sprintf(
			'<a href="#" class="%s" data-id="%d">%s</a>',
			esc_attr( join( ' ', $class_names ) ),
			$this->attachment_id,
			esc_html__( 'Ignore', 'wp-smushit' )
		);
	}

	private function get_html_markup_for_failed_item( $error_message, $links ) {
		$html = $this->get_html_markup_optimization_status_for_failed_item( $error_message );
		$html .= $this->get_html_markup_action_links( $links );

		return $html;
	}

	private function get_html_markup_optimization_status_for_failed_item( $error_message ) {
		if ( $this->media_item->is_ignored() ) {
			$class_name = 'smush-ignored';
		} else {
			$class_name = 'smush-warning';
		}

		return $this->get_html_markup_optimization_status( $error_message, $class_name );
	}

	private function get_html_markup_optimization_status( $message, $class_names = array() ) {
		return sprintf( '<p class="smush-status %s">%s</p>', join( ' ', (array) $class_names ), $message );
	}

	private function get_html_markup_action_links( $links, $separator = ' | ' ) {
		$links     = (array) $links;
		$max_links = 4;
		if ( count( $links ) > $max_links ) {
			$links = array_splice( $links, count( $links ) - $max_links );
		}

		return sprintf( '<div class="sui-smush-media smush-status-links">%s</div>', join( $separator, $links ) );
	}

	private function generate_markup_for_size_limited_item() {
		$utm_link = '';
		if ( ! WP_Smush::is_pro() ) {
			$utm_link = $this->get_html_utm_link(
				__( 'Upgrade to Pro to Smush larger images.', 'wp-smushit' ),
				'smush_bulksmush_library_filesizelimit'
			);
		}

		if ( $this->media_item->is_ignored() ) {
			$error_message = esc_html__( 'Ignored.', 'wp-smushit' );
		} else {
			$error_message = $this->errors->get_error_message();
		}

		return $this->get_html_markup_for_failed_item_with_utm_link( $error_message, $utm_link );
	}

	private function generate_markup_for_ignored_item() {
		return $this->get_html_markup_for_failed_item_with_suggestion_link( esc_html__( 'Ignored.', 'wp-smushit' ) );
	}

	private function generate_markup_for_failed_item() {
		$error_suggestion   = $this->get_error_suggestion();
		$suggestion_link    = $this->get_array_value( $error_suggestion, 'link' );
		$suggestion_message = $this->get_array_value( $error_suggestion, 'message' );

		$error_message = $this->errors->get_error_message();
		if ( $suggestion_message ) {
			$error_message = sprintf(
				'%s. %s',
				rtrim( $error_message, '.' ),
				$suggestion_message
			);
		}

		return $this->get_html_markup_for_failed_item_with_suggestion_link( $error_message, $suggestion_link );
	}

	private function get_error_suggestion() {
		$error_suggestion = array(
			'message' => '',
			'link'    => '',
		);
		if ( ! $this->errors->has_errors() ) {
			return $error_suggestion;
		}

		switch ( $this->errors->get_error_code() ) {
			case 'file_not_found':
			case 'no_file_meta':
				if ( $this->media_item->can_be_restored() ) {
					$error_suggestion['message'] = esc_html__( 'We recommend using the restore image function to regenerate the thumbnails.', 'wp-smushit' );
				} else {
					$error_suggestion['message'] = esc_html__( 'We recommend regenerating the thumbnails.', 'wp-smushit' );
					$error_suggestion['link']    = $this->get_html_markup_for_regenerate_doc_link();
				}
				break;
		}

		return $error_suggestion;
	}

	private function get_html_markup_for_regenerate_doc_link() {
		return sprintf(
			'<a target="_blank" href="%s" class="wp-smush-learnmore" data-id="%d">%s</a>',
			esc_url( $this->get_regenerate_doc_link() ),
			$this->attachment_id,
			esc_html__( 'Learn more', 'wp-smushit' )
		);
	}

	private function get_regenerate_doc_link() {
		$doc = 'https://wpmudev.com/docs/wpmu-dev-plugins/smush/';
		if ( ! WP_Smush::is_pro() ) {
			$doc = 'https://wpmudev.com/docs/wpmu-dev-plugins/smush/?utm_source=smush&utm_medium=plugin&utm_campaign=smush_pluginlist_docs';
		}
		$doc .= '#restoring-images';

		return $doc;
	}

	private function get_html_markup_for_failed_item_with_suggestion_link( $error_message, $suggestion_link = '' ) {
		$links = array();
		if ( $suggestion_link ) {
			$links[] = $suggestion_link;
		}
		if ( $this->media_item->is_ignored() ) {
			$links[] = $this->get_revert_link();
		} else {
			$resmush_link = $this->get_resmush_link();
			if ( $resmush_link ) {
				$links[] = $resmush_link;
			}
			$restore_link = $this->get_restore_link();
			if ( $restore_link ) {
				$links[] = $restore_link;
			}
			$links[] = $this->get_ignore_link();
		}

		return $this->get_html_markup_for_failed_item( $error_message, $links );
	}

	private function generate_markup_for_unsmushed_item() {
		$action_links = array(
			$this->get_smush_link(),
			$this->get_ignore_link(),
		);

		$html = $this->get_html_markup_optimization_status( esc_html__( 'Not processed', 'wp-smushit' ) );
		$html .= $this->get_html_markup_action_links( $action_links );

		return $html;
	}

	private function generate_markup_for_smushed_item( Media_Item_Stats $total_stats ) {
		$error_class = $this->errors->has_errors() ? 'smush-warning' : '';
		$html        = $this->get_html_markup_optimization_status( $this->get_optimization_status( $total_stats ), $error_class );
		$html        .= $this->get_html_markup_action_links( $this->get_action_links( $total_stats ) );
		$html        .= $this->get_html_markup_detailed_stats( $total_stats );

		return $html;
	}

	private function get_optimization_status( Media_Item_Stats $total_stats ) {
		$error_message = $this->errors->get_error_message();
		if ( $error_message ) {
			return $error_message;
		}

		$no_savings = $total_stats->get_size_after() >= $total_stats->get_size_before();
		if ( $no_savings ) {
			return esc_html__( 'Skipped: Image is already optimized.', 'wp-smushit' );
		}

		return $this->get_savings_status_text( $total_stats );
	}

	private function get_savings_status_text( $total_stats ) {
		$count_images = $this->optimizer->get_optimized_sizes_count();

		if ( 1 < $count_images ) {
			$status_text = sprintf( /* translators: %1$s: bytes savings, %2$s: percentage savings, %3$d: number of images */
				esc_html__( '%3$d images reduced by %1$s (%2$s)', 'wp-smushit' ),
				$total_stats->get_human_bytes(),
				sprintf( '%01.1f%%', $total_stats->get_percent() ),
				$count_images
			);
		} else {
			$status_text = sprintf( /* translators: %1$s: bytes savings, %2$s: percentage savings */
				esc_html__( 'Reduced by %1$s (%2$s)', 'wp-smushit' ),
				$total_stats->get_human_bytes(),
				sprintf( '%01.1f%%', $total_stats->get_percent() )
			);
		}

		// Do we need to show the main image size?
		$status_text .= sprintf(
			/* translators: 1: <br/> tag, 2: Image file size */
			esc_html__( '%1$sMain Image size: %2$s', 'wp-smushit' ),
			'<br />',
			size_format( $this->media_item->get_scaled_or_full_size()->get_filesize(), 2 )
		);

		return $status_text;
	}

	/**
	 * @return array
	 */
	private function get_action_links( Media_Item_Stats $total_stats ) {
		if ( $this->is_first_optimization_required() ) {
			return array( $this->get_smush_link(), $this->get_ignore_link() );
		}

		$links        = array();
		$resmush_link = $this->get_resmush_link();
		if ( $resmush_link ) {
			$links[] = $resmush_link;
			// Add ignore button while showing resmush button.
			$links[] = $this->get_ignore_link();
		}

		$no_savings = $total_stats->get_size_after() >= $total_stats->get_size_before();
		if ( $no_savings ) {
			return $links;
		}

		$restore_link = $this->get_restore_link();
		if ( $restore_link ) {
			$links[] = $restore_link;
		}

		$links[] = $this->get_view_stats_link();

		return $links;
	}

	/**
	 * @return string|void
	 */
	private function get_html_markup_detailed_stats( Media_Item_Stats $total_stats ) {
		$no_savings = $total_stats->get_size_after() >= $total_stats->get_size_before();
		if ( $no_savings ) {
			return;
		}

		return sprintf(
			'<div id="smush-stats-%d" class="sui-smush-media smush-stats-wrapper hidden">
				<table class="wp-smush-stats-holder">
					<thead>
						<tr>
							<th class="smush-stats-header">%s</th>
							<th class="smush-stats-header">%s</th>
						</tr>
					</thead>
					<tbody>%s</tbody>
				</table>
			</div>',
			$this->attachment_id,
			esc_html__( 'Image size', 'wp-smushit' ),
			esc_html__( 'Savings', 'wp-smushit' ),
			$this->get_detailed_stats_content()
		);
	}

	private function get_detailed_stats_content() {
		$stats_rows    = array();
		$savings_sizes = array();

		// Show Sizes and their compression.
		foreach ( $this->media_item->get_sizes() as $size_key => $size ) {
			$total_size_stats = $this->optimizer->get_total_size_stats( $size_key );
			if ( $total_size_stats->is_empty() ) {
				continue;
			}
			$dimensions = "{$size->get_width()}x{$size->get_height()}";

			$stats_rows[ $size_key ]    = sprintf(
				'<tr>
					<td>%s<br/>(%s)</td>
					<td>%s ( %s%% )</td>
				</tr>',
				strtoupper( $size_key ),
				$dimensions,
				$total_size_stats->get_human_bytes(),
				$total_size_stats->get_percent()
			);
			$savings_sizes[ $size_key ] = $total_size_stats->get_bytes();
		}

		uksort(
			$stats_rows,
			function( $size_key1, $size_key2 ) use ( $savings_sizes ) {
				return $savings_sizes[ $size_key2 ] - $savings_sizes[ $size_key1 ];
			}
		);

		return join( '', $stats_rows );
	}


	private function get_smush_link() {
		return sprintf(
			'<a href="#" class="wp-smush-send" data-id="%d">%s</a>',
			$this->attachment_id,
			esc_html__( 'Smush', 'wp-smushit' )
		);
	}

	private function should_reoptimize() {
		$reoptimize_list = $this->global_stats->get_reoptimize_list();
		$error_list      = $this->global_stats->get_error_list();
		return $reoptimize_list->has_id( $this->attachment_id ) || $error_list->has_id( $this->attachment_id );
	}

	/**
	 * @return string|void
	 */
	private function get_resmush_link() {
		if ( ! $this->should_reoptimize() || ! $this->media_item->has_wp_metadata() ) {
			return;
		}

		$next_level_smush_link = $this->get_next_level_smush_link();
		if ( ! empty( $next_level_smush_link ) ) {
			return $next_level_smush_link;
		}

		return sprintf(
			'<a href="#" data-tooltip="%s" data-id="%d" data-nonce="%s" class="wp-smush-action wp-smush-title sui-tooltip sui-tooltip-constrained wp-smush-resmush">%s</a>',
			esc_html__( 'Smush image including original file', 'wp-smushit' ),
			$this->attachment_id,
			wp_create_nonce( 'wp-smush-resmush-' . $this->attachment_id ),
			esc_html__( 'Resmush', 'wp-smushit' )
		);
	}

	/**
	 * @return string|void
	 */
	private function get_next_level_smush_link() {
		if (
			$this->errors->has_errors()
			|| $this->is_first_optimization_required()
			|| ! $this->is_next_level_smush_required()
		) {
			return;
		}

		$anchor_text = $this->get_next_level_smush_anchor_text();
		if ( ! $anchor_text ) {
			return;
		}

		return sprintf(
			'<a href="#" class="wp-smush-send" data-id="%d">%s</a>',
			$this->attachment_id,
			$anchor_text
		);
	}

	/**
	 * @return bool
	 */
	private function is_next_level_smush_required() {
		$smush_optimization = $this->get_smush_optimization();

		return $smush_optimization && $smush_optimization->is_next_level_available();
	}

	private function get_next_level_smush_anchor_text() {
		$required_level = $this->settings->get_lossy_level_setting();
		switch ( $required_level ) {
			case Settings::LEVEL_ULTRA_LOSSY:
				return esc_html__( 'Ultra Smush', 'wp-smushit' );

			case Settings::LEVEL_SUPER_LOSSY:
				return esc_html__( 'Super Smush', 'wp-smushit' );

			default:
				return false;
		}
	}

	/**
	 * @return Smush_Optimization|null
	 */
	private function get_smush_optimization() {
		/**
		 * @var $smush_optimization Smush_Optimization|null
		 */
		$smush_optimization = $this->optimizer->get_optimization( Smush_Optimization::KEY );
		return $smush_optimization;
	}

	/**
	 * @return string|void
	 */
	private function get_restore_link() {
		if ( ! $this->media_item->can_be_restored() ) {
			return;
		}

		return sprintf(
			'<a href="#" data-tooltip="%s" data-id="%d" data-nonce="%s" class="wp-smush-action wp-smush-title sui-tooltip wp-smush-restore">%s</a>',
			esc_html__( 'Restore original image', 'wp-smushit' ),
			$this->attachment_id,
			wp_create_nonce( 'wp-smush-restore-' . $this->attachment_id ),
			esc_html__( 'Restore', 'wp-smushit' )
		);
	}

	private function get_view_stats_link() {
		return sprintf(
			'<a href="#" class="wp-smush-action smush-stats-details wp-smush-title sui-tooltip sui-tooltip-top-right" data-tooltip="%s">%s</a>',
			esc_html__( 'Detailed stats for all the image sizes', 'wp-smushit' ),
			esc_html__( 'View Stats', 'wp-smushit' )
		);
	}

	private function get_array_value( $array, $key ) {
		return isset( $array[ $key ] ) ? $array[ $key ] : null;
	}
}