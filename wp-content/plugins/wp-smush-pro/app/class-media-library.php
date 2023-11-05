<?php
/**
 * Media library class.
 *
 * Responsible for displaying a UI (stats + action links) in the media library and the editor.
 *
 * @since 3.4.0
 * @package Smush\App
 */

namespace Smush\App;

use Smush\Core\Core;
use Smush\Core\Helper;
use Smush\Core\Media\Media_Item;
use Smush\Core\Media\Media_Item_Optimizer;
use Smush\Core\Media_Library\Media_Library_Row;
use Smush\Core\Modules\Abstract_Module;
use Smush\Core\Modules\Smush;
use Smush\Core\Stats\Global_Stats;
use WP_Post;
use WP_Query;

/**
 * Class Media_Library
 */
class Media_Library extends Abstract_Module {

	/**
	 * Core instance.
	 *
	 * @var Core $core
	 */
	private $core;
	private $allowed_image_sizes;

	/**
	 * Media_Library constructor.
	 *
	 * @param Core $core  Core instance.
	 */
	public function __construct( Core $core ) {
		parent::__construct();
		$this->core = $core;
	}

	/**
	 * Init functionality that is related to the UI.
	 */
	public function init_ui() {
		// Media library columns.
		add_filter( 'manage_media_columns', array( $this, 'columns' ) );
		add_filter( 'manage_upload_sortable_columns', array( $this, 'sortable_column' ) );
		add_action( 'manage_media_custom_column', array( $this, 'custom_column' ), 10, 2 );

		// Manage column sorting.
		add_action( 'pre_get_posts', array( $this, 'smushit_orderby' ) );

		// Smush image filter from Media Library.
		add_filter( 'ajax_query_attachments_args', array( $this, 'filter_media_query' ) );
		// Smush image filter from Media Library (list view).
		add_action( 'restrict_manage_posts', array( $this, 'add_filter_dropdown' ) );

		// Add pre WordPress 5.0 compatibility.
		add_filter( 'wp_kses_allowed_html', array( $this, 'filter_html_attributes' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'extend_media_modal' ), 15 );

		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'smush_send_status' ), 99, 3 );
	}

	/**
	 * Print column header for Smush results in the media library using the `manage_media_columns` hook.
	 *
	 * @param array $defaults  Defaults array.
	 *
	 * @return array
	 */
	public function columns( $defaults ) {
		$defaults['smushit'] = 'Smush';

		return $defaults;
	}

	/**
	 * Add the Smushit Column to sortable list
	 *
	 * @param array $columns  Columns array.
	 *
	 * @return array
	 */
	public function sortable_column( $columns ) {
		$columns['smushit'] = 'smushit';

		return $columns;
	}

	/**
	 * Print column data for Smush results in the media library using
	 * the `manage_media_custom_column` hook.
	 *
	 * @param string $column_name  Column name.
	 * @param int    $id           Attachment ID.
	 */
	public function custom_column( $column_name, $id ) {
		if ( 'smushit' === $column_name ) {
			$escaped_text = wp_kses_post( $this->generate_markup( $id ) );
			if ( $this->is_failed_processing_page() ) {
				$escaped_text = sprintf( '<div class="smush-failed-processing">%s</div>', $escaped_text );
			}
			echo $escaped_text;
		}
	}

	/**
	 * Detect failed processing page.
	 *
	 * @since 3.12.0
	 *
	 * @return boolean
	 */
	private function is_failed_processing_page() {
		static $is_failed_processing_page;
		if ( null === $is_failed_processing_page ) {
			$filter = filter_input( INPUT_GET, 'smush-filter', FILTER_SANITIZE_SPECIAL_CHARS );
			$is_failed_processing_page = 'failed_processing' === $filter;
		}
		return $is_failed_processing_page;
	}

	/**
	 * Order by query for smush columns.
	 *
	 * @param WP_Query $query  Query.
	 *
	 * @return WP_Query
	 */
	public function smushit_orderby( $query ) {
		global $current_screen;

		// Filter only media screen.
		if (
			! is_admin()
			|| ( ! empty( $current_screen ) && 'upload' !== $current_screen->base )
			|| 'attachment' !== $query->get( 'post_type' )
		) {
			return $query;
		}

		$filter = filter_input( INPUT_GET, 'smush-filter', FILTER_SANITIZE_SPECIAL_CHARS );

		// Ignored.
		if ( 'ignored' === $filter ) {
			$query->set( 'meta_query', $this->query_ignored() );
			return $query;
		} elseif ( 'unsmushed' === $filter ) {
			// Not processed.
			$query->set( 'meta_query', $this->query_unsmushed() );
			return $query;
		} elseif ( 'failed_processing' === $filter ) {
			// Failed processing.
			$query->set( 'meta_query', $this->query_failed_processing() );
			return $query;
		}

		// TODO: do we need this?
		$orderby = $query->get( 'orderby' );

		if ( isset( $orderby ) && 'smushit' === $orderby ) {
			$query->set(
				'meta_query',
				array(
					'relation' => 'OR',
					array(
						'key'     => Smush::$smushed_meta_key,
						'compare' => 'EXISTS',
					),
					array(
						'key'     => Smush::$smushed_meta_key,
						'compare' => 'NOT EXISTS',
					),
				)
			);
			$query->set( 'orderby', 'meta_value_num' );
		}

		return $query;
	}

	/**
	 * Add our filter to the media query filter in Media Library.
	 *
	 * @since 2.9.0
	 *
	 * @see wp_ajax_query_attachments()
	 *
	 * @param array $query  Query.
	 *
	 * @return mixed
	 */
	public function filter_media_query( $query ) {
		$post_query = filter_input( INPUT_POST, 'query', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );

		if ( ! isset( $post_query['stats'] ) ) {
			return $query;
		}

		$filter_name = $post_query['stats'];

		// Excluded.
		if ( 'excluded' === $filter_name ) {
			$query['meta_query'] = $this->query_ignored();
		} elseif ( 'unsmushed' === $filter_name ) {
			// Unsmushed.
			$query['meta_query'] = $this->query_unsmushed();
		} elseif ( 'failed_processing' === $filter_name ) {
			// Failed processing.
			$query['meta_query'] = $this->query_failed_processing();
		}

		return $query;
	}

	/**
	 * Meta query for images skipped from bulk smush.
	 *
	 * @return array
	 */
	private function query_failed_processing() {
		// Custom query to add error items.
		add_filter( 'posts_where_request', array( $this, 'filter_query_to_add_media_item_errors' ) );

		// Custom query for failed on optimization.
		$meta_query =  array(
			'relation' => 'AND',
			array(
				'key'     => Media_Item_Optimizer::ERROR_META_KEY,
				'compare' => 'EXISTS',
			),
			array(
				'key'     => Media_Item::IGNORED_META_KEY,
				'compare' => 'NOT EXISTS',
			),
		);

		return $meta_query;
	}


	public function filter_query_to_add_media_item_errors( $where ) {
		global $wpdb;

		remove_filter( 'posts_where_request', array( $this, 'filter_query_to_add_media_item_errors' ) );

		$media_error_ids = Global_Stats::get()->get_error_list()->get_ids();
		if ( empty( $media_error_ids ) ) {
			return $where;
		}

		$where .= sprintf( " OR {$wpdb->posts}.ID IN (%s)", join( ',', $media_error_ids ) );

		return $where;
	}

	/**
	 * Meta query for images skipped from bulk smush.
	 *
	 * @return array
	 */
	private function query_ignored() {
		return array(
			array(
				'key'     => Media_Item::IGNORED_META_KEY,
				'compare' => 'EXISTS',
			),
		);
	}

	/**
	 * Meta query for uncompressed images.
	 *
	 * @return array
	 */
	private function query_unsmushed() {
		return Core::get_unsmushed_meta_query();
	}

	/**
	 * Adds a search dropdown in Media Library list view to filter out images that have been
	 * ignored with bulk Smush.
	 *
	 * @since 3.2.0
	 */
	public function add_filter_dropdown() {
		$scr = get_current_screen();

		if ( 'upload' !== $scr->base ) {
			return;
		}

		$ignored = filter_input( INPUT_GET, 'smush-filter', FILTER_SANITIZE_SPECIAL_CHARS );

		?>
		<label for="smush_filter" class="screen-reader-text">
			<?php esc_html_e( 'Filter by Smush status', 'wp-smushit' ); ?>
		</label>
		<select class="smush-filters" name="smush-filter" id="smush_filter">
			<option value="" <?php selected( $ignored, '' ); ?>><?php esc_html_e( 'Smush: All images', 'wp-smushit' ); ?></option>
			<option value="unsmushed" <?php selected( $ignored, 'unsmushed' ); ?>><?php esc_html_e( 'Smush: Not processed', 'wp-smushit' ); ?></option>
			<option value="ignored" <?php selected( $ignored, 'ignored' ); ?>><?php esc_html_e( 'Smush: Bulk ignored', 'wp-smushit' ); ?></option>
			<option value="failed_processing" <?php selected( $ignored, 'failed_processing' ); ?>><?php esc_html_e( 'Smush: Failed Processing', 'wp-smushit' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Data attributes are not allowed on <a> elements on WordPress before 5.0.0.
	 * Add backward compatibility.
	 *
	 * @since 3.5.0
	 * @see https://github.com/WordPress/WordPress/commit/a0309e80b6a4d805e4f230649be07b4bfb1a56a5#diff-a0e0d196dd71dde453474b0f791828fe
	 * @param array $context  Context.
	 *
	 * @return mixed
	 */
	public function filter_html_attributes( $context ) {
		global $wp_version;

		if ( version_compare( '5.0.0', $wp_version, '<' ) ) {
			return $context;
		}

		$context['a']['data-tooltip'] = true;
		$context['a']['data-id']      = true;
		$context['a']['data-nonce']   = true;

		return $context;
	}

	/**
	 * Load media assets.
	 *
	 * Localization also used in Gutenberg integration.
	 */
	public function extend_media_modal() {
		// Get current screen.
		$current_screen = get_current_screen();

		// Only run on required pages.
		if ( ! empty( $current_screen ) && ! in_array( $current_screen->id, Core::$external_pages, true ) && empty( $current_screen->is_block_editor ) ) {
			return;
		}

		if ( wp_script_is( 'smush-backbone-extension', 'enqueued' ) ) {
			return;
		}

		wp_enqueue_script(
			'smush-backbone-extension',
			WP_SMUSH_URL . 'app/assets/js/smush-media.min.js',
			array(
				'jquery',
				'media-editor', // Used in image filters.
				'media-views',
				'media-grid',
				'wp-util',
				'wp-api',
			),
			WP_SMUSH_VERSION,
			true
		);

		wp_localize_script(
			'smush-backbone-extension',
			'smush_vars',
			array(
				'strings' => array(
					'stats_label'          => esc_html__( 'Smush', 'wp-smushit' ),
					'filter_all'           => esc_html__( 'Smush: All images', 'wp-smushit' ),
					'filter_not_processed' => esc_html__( 'Smush: Not processed', 'wp-smushit' ),
					'filter_excl'          => esc_html__( 'Smush: Bulk ignored', 'wp-smushit' ),
					'filter_failed'        => esc_html__( 'Smush: Failed Processing', 'wp-smushit' ),
					'gb'                   => array(
						'stats'        => esc_html__( 'Smush Stats', 'wp-smushit' ),
						'select_image' => esc_html__( 'Select an image to view Smush stats.', 'wp-smushit' ),
						'size'         => esc_html__( 'Image size', 'wp-smushit' ),
						'savings'      => esc_html__( 'Savings', 'wp-smushit' ),
					),
				),
			)
		);
	}

	/**
	 * Send smush status for attachment.
	 *
	 * @param array   $response    Response array.
	 * @param WP_Post $attachment  Attachment object.
	 *
	 * @return mixed
	 */
	public function smush_send_status( $response, $attachment ) {
		if ( ! isset( $attachment->ID ) ) {
			return $response;
		}

		// Validate nonce.
		$status            = $this->smush_status( $attachment->ID );
		$response['smush'] = $status;

		return $response;
	}

	/**
	 * Get the smush button text for attachment.
	 *
	 * @param int $id  Attachment ID for which the Status has to be set.
	 *
	 * @return string
	 */
	private function smush_status( $id ) {
		$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_NULL_ON_FAILURE );

		// Show Temporary Status, For Async Optimisation, No Good workaround.
		if ( ! get_transient( 'wp-smush-restore-' . $id ) && 'upload-attachment' === $action && $this->settings->get( 'auto' ) ) {
			$status_txt = '<p class="smush-status">' . __( 'Smushing in progress...', 'wp-smushit' ) . '</p>';
			$button_txt = __( 'Smush Now!', 'wp-smushit' );

			return $this->column_html( $id, $status_txt, $button_txt, false );
		}

		// Else return the normal status.
		return trim( $this->generate_markup( $id ) );
	}

	/**
	 * Skip messages respective to their IDs.
	 *
	 * @param string $msg_id  Message ID.
	 *
	 * TODO: Remove this method as no longer need.
	 *
	 * @return bool
	 */
	public function skip_reason( $msg_id ) {
		$count           = count( get_intermediate_image_sizes() );
		$smush_orgnl_txt = sprintf(
			/* translators: %s: number of thumbnails */
			esc_html__( 'When you upload an image to WordPress it automatically creates %s thumbnail sizes that are commonly used in your pages. WordPress also stores the original full-size image, but because these are not usually embedded on your site we don’t Smush them. Pro users can override this.', 'wp-smushit' ),
			$count
		);

		$skip_msg = array(
			'large_size' => $smush_orgnl_txt,
			'size_limit' => esc_html__( "Image couldn't be smushed as it exceeded the 5Mb size limit, Pro users can smush images without any size restriction.", 'wp-smushit' ),
		);

		$skip_rsn = '';
		if ( ! empty( $skip_msg[ $msg_id ] ) ) {
			$skip_rsn = '<a href="https://wpmudev.com/project/wp-smush-pro/?utm_source=smush&utm_medium=plugin&utm_campaign=smush_medialibrary_savings" target="_blank">
				<span class="sui-tooltip sui-tooltip-left sui-tooltip-constrained sui-tooltip-top-right-mobile" data-tooltip="' . $skip_msg[ $msg_id ] . '">
				<span class="sui-tag sui-tag-purple sui-tag-sm">' . esc_html__( 'PRO', 'wp-smushit' ) . '</span></span></a>';
		}

		return $skip_rsn;
	}

	/**
	 * Generate HTML for image status on the media library page.
	 *
	 * @since 3.5.0  Refactored from set_status().
	 *
	 * @param int $id  Attachment ID.
	 *
	 * @return string  HTML content or array of results.
	 */
	public function generate_markup( $id ) {
		$media_lib_item = new Media_Library_Row( $id );
		return $media_lib_item->generate_markup();
	}

	/**
	 * Print the column html.
	 *
	 * @param string  $id           Media id.
	 * @param string  $html         Status text.
	 * @param string  $button_txt   Button label.
	 * @param boolean $show_button  Whether to shoe the button.
	 *
	 * @return string
	 */
	private function column_html( $id, $html = '', $button_txt = '', $show_button = true ) {
		// Don't proceed if attachment is not image, or if image is not a jpg, png or gif, or if is not found.
		$is_smushable = Helper::is_smushable( $id );
		if ( ! $is_smushable ) {
			return false === $is_smushable ? esc_html__( 'Image not found!', 'wp-smushit' ) : esc_html__( 'Not processed', 'wp-smushit' );
		}

		// If we aren't showing the button.
		if ( ! $show_button ) {
			return $html;
		}

		if ( 'Super-Smush' === $button_txt ) {
			$html .= ' | ';
		}

		$html .= "<a href='#' class='wp-smush-send' data-id='{$id}'>{$button_txt}</a>";

		if ( get_post_meta( $id, 'wp-smush-ignore-bulk', true ) ) {
			$nonce = wp_create_nonce( 'wp-smush-remove-skipped' );
			$html .= " | <a href='#' class='wp-smush-remove-skipped' data-id={$id} data-nonce={$nonce}>" . esc_html__( 'Show in bulk Smush', 'wp-smushit' ) . '</a>';
		} else {
			$html .= " | <a href='#' class='smush-ignore-image' data-id='{$id}'>" . esc_html__( 'Ignore', 'wp-smushit' ) . '</a>';
		}

		$html .= self::progress_bar();

		return $html;
	}

	/**
	 * Returns the HTML for progress bar
	 *
	 * @return string
	 */
	public static function progress_bar() {
		return '<span class="spinner wp-smush-progress"></span>';
	}
}