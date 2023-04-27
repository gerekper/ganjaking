<?php

declare( strict_types=1 );

namespace ACP\Asset\Script;

use AC\Asset\Location\Absolute;
use AC\Asset\Script;
use AC\Asset\Script\Localize\Translation;
use AC\Capabilities;
use AC\ColumnSize;
use AC\ListScreen;
use AC\ListScreenCollection;
use AC\ListScreenRepository\Filter\ExcludeAdmin;
use AC\ListScreenRepository\Sort\ListIds;
use AC\ListScreenRepository\Storage;
use AC\Storage\ListScreenOrder;
use AC\Type\ColumnWidth;
use ACP\Bookmark\Entity\Segment;
use ACP\Bookmark\SegmentRepository;
use ACP\Bookmark\Setting\PreferredSegment;
use ACP\Preference\User\TableListOrder;
use ACP\Settings\ListScreen\HideOnScreen;
use ACP\Settings\Option\LayoutStyle;
use WP_User;

class Table extends Script {

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	/**
	 * @var ColumnSize\UserStorage
	 */
	private $user_storage;

	/**
	 * @var ColumnSize\ListStorage
	 */
	private $list_storage;

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var SegmentRepository
	 */
	private $segment_repository;

	public function __construct(
		Absolute $location,
		ListScreen $list_screen,
		ColumnSize\UserStorage $user_storage,
		ColumnSize\ListStorage $list_storage,
		Storage $storage,
		SegmentRepository $segment_repository
	) {
		parent::__construct( 'acp-table', $location, [ Script\GlobalTranslationFactory::HANDLE ] );

		$this->list_screen = $list_screen;
		$this->user_storage = $user_storage;
		$this->list_storage = $list_storage;
		$this->storage = $storage;
		$this->segment_repository = $segment_repository;
	}

	private function is_column_order_active(): bool {
		$hide_on_screen = new HideOnScreen\ColumnOrder();

		return (bool) apply_filters( 'acp/column_order/active', ! $hide_on_screen->is_hidden( $this->list_screen ), $this->list_screen );
	}

	private function is_column_resize_active(): bool {
		$hide_on_screen = new HideOnScreen\ColumnResize();

		return (bool) apply_filters( 'acp/resize_columns/active', ! $hide_on_screen->is_hidden( $this->list_screen ), $this->list_screen );
	}

	public function register(): void {
		parent::register();

		$user = wp_get_current_user();

		if ( ! $user ) {
			return;
		}

		$translation = Translation::create( [
			'column_sets'          => [
				'more'        => _x( '%s more', 'number of items', 'codepress-admin-columns' ),
				'switch_view' => __( 'Switch View', 'codepress-admin-columns' ),
			],
			'column_screen_option' => [
				'button_reset'              => _x( 'Reset', 'column-resize-button', 'codepress-admin-columns' ),
				'label'                     => __( 'Columns', 'codepress-admin-columns' ),
				'resize_columns_tool'       => __( 'Resize Columns', 'codepress-admin-columns' ),
				'reset_confirmation'        => sprintf( '%s %s', __( 'Restore the current column widths and order to their defaults.', 'codepress-admin-columns' ), __( 'Are you sure?', 'codepress-admin-columns' ) ),
				'save_changes'              => __( 'Save changes', 'codepress-admin-columns' ),
				'save_changes_confirmation' => sprintf( '%s %s', __( 'Save the current column widths and order changes as the new default for ALL users.', 'codepress-admin-columns' ), __( 'Are you sure?', 'codepress-admin-columns' ) ),
				'tip_reset'                 => __( 'Reset columns to their default widths and order.', 'codepress-admin-columns' ),
				'tip_save_changes'          => __( 'Save the current column widths and order changes.', 'codepress-admin-columns' ),
			],
		] );

		$this
			->add_inline_variable( 'ACP_TABLE', [
				'column_sets'          => $this->get_column_sets( $user ),
				'column_sets_style'    => $this->get_column_set_style(),
				'column_screen_option' => [
					'has_manage_admin_cap' => current_user_can( Capabilities::MANAGE ),
				],
				'column_order'         => [
					'active'        => $this->is_column_order_active(),
					'current_order' => array_keys( $this->list_screen->get_columns() ),
				],
				'column_width'         => [
					'active'                    => $this->is_column_resize_active(),
					'can_reset'                 => $this->user_storage->exists( $this->list_screen->get_id() ),
					'minimal_pixel_width'       => 50,
					'column_sizes_current_user' => $this->get_column_sizes_by_user( $this->list_screen ),
					'column_sizes'              => $this->get_column_sizes( $this->list_screen ),
				],
			] )->localize( 'ACP_TABLE_I18N', $translation );
	}

	private function get_column_sets( WP_User $user ): array {
		return array_values( array_map( [ $this, 'create_column_set_vars' ], $this->get_list_screens( $this->list_screen, $user )->get_copy() ) );
	}

	private function get_column_set_style(): string {
		$option = new LayoutStyle();

		return $option->get() ?: LayoutStyle::OPTION_DROPDOWN;
	}

	private function get_list_screens( ListScreen $list_screen, WP_User $user ): ListScreenCollection {
		$list_order_user = ( new TableListOrder( $user->ID ) )->get( $list_screen->get_key() ) ?: [];
		$list_order = ( new ListScreenOrder() )->get( $list_screen->get_key() );

		$list_ids = array_unique( array_merge( $list_order_user, $list_order ) );

		$list_screens = $this->storage->find_all_by_user(
			$list_screen->get_key(),
			$user,
			new ListIds( $list_ids )
		);

		$list_screens = ( new ExcludeAdmin( $user ) )->filter( $list_screens );

		// Add current list screeen for when an admin visits the table for a user or role specific listscreen
		if ( current_user_can( Capabilities::MANAGE ) && ! $list_screens->contains( $list_screen ) ) {
			$list_screens->add( $list_screen );
		}

		return $list_screens;
	}

	private function add_filter_args_to_url( string $link ): string {
		$post_status = filter_input( INPUT_GET, 'post_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( $post_status ) {
			$link = add_query_arg( [ 'post_status' => $post_status ], $link );
		}

		$author = filter_input( INPUT_GET, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( $author ) {
			$link = add_query_arg( [ 'author' => $author ], $link );
		}

		return $link;
	}

	private function create_column_set_vars( ListScreen $list_screen ): array {
		$segment = $this->get_predefined_segment( $list_screen );

		return [
			'id'                 => $list_screen->has_id() ? (string) $list_screen->get_id() : null,
			'label'              => $list_screen->get_title() ? htmlspecialchars_decode( $list_screen->get_title() ): $list_screen->get_label(),
			'url'                => $this->add_filter_args_to_url( $list_screen->get_screen_link() ),
			'pre_filtered'       => false,
			'pre_filtered_label' => $segment ? sprintf( __( 'Filtered by: %s', 'codepress-admin-columns' ), $segment->get_name() ) : null,
		];
	}

	private function get_predefined_segment( ListScreen $list_screen ): ?Segment {
		return ( new PreferredSegment( $list_screen, $this->segment_repository ) )->get_segment();
	}

	private function get_column_sizes( ListScreen $list_screen ): array {
		$result = [];

		if ( $list_screen->get_settings() ) {
			foreach ( $this->list_storage->get_all( $list_screen ) as $column_name => $width ) {
				$result[ $column_name ] = $this->create_vars( $width );
			}
		}

		return $result;
	}

	private function get_column_sizes_by_user( ListScreen $list_screen ): array {
		$result = [];

		if ( $list_screen->get_settings() ) {
			foreach ( $this->user_storage->get_all( $list_screen->get_id() ) as $column_name => $width ) {
				$result[ $column_name ] = $this->create_vars( $width );
			}
		}

		return $result;
	}

	private function create_vars( ColumnWidth $width ): array {
		return [
			'value' => $width->get_value(),
			'unit'  => $width->get_unit(),
		];
	}

}