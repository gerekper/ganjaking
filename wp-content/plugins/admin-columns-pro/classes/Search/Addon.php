<?php

namespace ACP\Search;

use AC;
use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use AC\Registrable;
use ACP;
use ACP\Bookmark\SegmentRepository;
use ACP\Bookmark\Setting\PreferredSegment;
use ACP\Settings\ListScreen\HideOnScreenCollection;

final class Addon implements Registrable {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var Location
	 */
	private $location;

	/**
	 * @var SegmentRepository
	 */
	private $segment_repository;

	/**
	 * @var Preferences\SmartFiltering
	 */
	private $table_preference;

	/**
	 * @var ACP\Settings\ListScreen\HideOnScreen\Filters
	 */
	private $hide_filters;

	/**
	 * @var Settings\HideOnScreen\SmartFilters
	 */
	private $hide_smart_filters;

	public function __construct( Storage $storage, Location $location, SegmentRepository $segment_repository ) {
		$this->storage = $storage;
		$this->location = $location;
		$this->segment_repository = $segment_repository;
		$this->table_preference = new Preferences\SmartFiltering();
		$this->hide_filters = new ACP\Settings\ListScreen\HideOnScreen\Filters();
		$this->hide_smart_filters = new Settings\HideOnScreen\SmartFilters();
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return bool
	 */
	private function is_active( AC\ListScreen $list_screen ) {
		return apply_filters( 'acp/search/is_active', $this->table_preference->is_active( $list_screen ), $list_screen );
	}

	public function register() {
		$this->get_column_settings()->register();
		$this->get_table_screen_options()->register();

		add_action( 'ac/table/list_screen', [ $this, 'table_screen_request' ] );
		add_action( 'wp_ajax_acp_search_comparison_request', [ $this, 'comparison_request' ] );
		add_action( 'acp/admin/settings/hide_on_screen', [ $this, 'add_hide_on_screen' ], 10, 2 );
	}

	private function get_column_settings() {
		return new Settings( [
			new AC\Asset\Style( 'acp-search-admin', $this->location->with_suffix( 'assets/search/css/admin.css' ) ),
		] );
	}

	private function get_table_screen_options() {
		return new TableScreenOptions(
			[
				new AC\Asset\Script( 'acp-search-table-screen-options', $this->location->with_suffix( 'assets/search/js/screen-options.bundle.js' ), [ 'ac-table' ] ),
			],
			$this->table_preference,
			$this->hide_filters,
			$this->hide_smart_filters
		);
	}

	public function add_hide_on_screen( HideOnScreenCollection $collection, AC\ListScreen $list_screen ) {
		if ( ! TableScreenFactory::get_table_screen_reference( $list_screen ) ) {
			return;
		}

		$collection->add( $this->hide_smart_filters, 40 )
		           ->add( new Settings\HideOnScreen\SavedFilters(), 41 );
	}

	public function comparison_request() {
		check_ajax_referer( 'ac-ajax' );

		$request = new AC\Request();

		$comparison = new RequestHandler\Comparison(
			$this->storage,
			$request
		);

		$comparison->dispatch( $request->get( 'method' ) );
	}

	public function table_screen_request( AC\ListScreen $list_screen ) {
		if ( ! $this->is_active( $list_screen ) ) {
			return;
		}

		$preferred_segment = new PreferredSegment( $list_screen, $this->segment_repository );

		$request = new AC\Request();
		$request->add_middleware( new Middleware\Segment( $preferred_segment ) )
		        ->add_middleware( new Middleware\Request() );

		$request_handler = new RequestHandler\Rules( $list_screen );
		$request_handler->handle( $request );

		if ( $this->hide_filters->is_hidden( $list_screen ) || $this->hide_smart_filters->is_hidden( $list_screen ) ) {
			return;
		}

		$assets = [
			new AC\Asset\Style( 'aca-search-table', $this->location->with_suffix( 'assets/search/css/table.css' ) ),
			new AC\Asset\Script( 'aca-search-moment', $this->location->with_suffix( 'assets/search/js/moment.min.js' ) ),
			new AC\Asset\Script( 'aca-search-querybuilder', $this->location->with_suffix( 'assets/search/js/query-builder.standalone.min.js' ), [ 'jquery', 'jquery-ui-datepicker' ] ),
			new Asset\Script\Table(
				'aca-search-table',
				$this->location->with_suffix( 'assets/search/js/table.bundle.js' ),
				$this->get_filters( $list_screen ),
				$request,
				$preferred_segment->get_segment()
			),
		];

		$table_screen = TableScreenFactory::create(
			$list_screen,
			$assets
		);

		if ( $table_screen ) {
			$table_screen->register();
		}

	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return array
	 */
	private function get_filters( AC\ListScreen $list_screen ) {
		$filters = [];

		foreach ( $list_screen->get_columns() as $column ) {
			$setting = $column->get_setting( 'search' );

			if ( ! $setting instanceof Settings\Column ) {
				continue;
			}

			$is_active = apply_filters_deprecated( 'acp/search/smart-filtering-active', [ $setting->is_active(), $setting ], '5.2', 'Smart filtering can be disabled using the UI.' );

			if ( ! $is_active ) {
				continue;
			}

			if ( ! $column instanceof Searchable || ! $column->search() ) {
				continue;
			}

			$filter = new Middleware\Filter(
				$column->get_name(),
				$column->search(),
				$this->get_filter_label( $column )
			);

			$filters[] = apply_filters( 'acp/search/filters', $filter(), $column );
		}

		return $filters;
	}

	/**
	 * @param AC\Column $column
	 *
	 * @return string
	 */
	private function get_filter_label( AC\Column $column ) {
		$label = $this->sanitize_label( $column->get_custom_label() );

		if ( ! $label ) {
			$label = $this->sanitize_label( $column->get_label() );
		}

		if ( ! $label ) {
			$label = $column->get_type();
		}

		return $label;
	}

	/**
	 * Allow dashicons as label, all the rest is parsed by 'strip_tags'
	 *
	 * @param string $label
	 *
	 * @return string
	 */
	private function sanitize_label( $label ) {
		if ( false === strpos( $label, 'dashicons' ) ) {
			$label = strip_tags( $label );
		}

		return trim( $label );
	}

}