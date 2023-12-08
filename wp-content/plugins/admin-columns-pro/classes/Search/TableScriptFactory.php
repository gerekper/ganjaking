<?php
declare( strict_types=1 );

namespace ACP\Search;

use AC;
use AC\Asset\Location\Absolute;
use AC\ListScreen;
use AC\Request;
use ACP\Search\Asset\Script;
use ACP\Search\Type\SegmentKey;

class TableScriptFactory {

	private $location;

	public function __construct( Absolute $location ) {
		$this->location = $location;
	}

	public function create(
		ListScreen $list_screen,
		Request $request,
        SegmentKey $segment_key = null
	): Script\Table {
		return new Script\Table(
			'aca-search-table',
			$this->location->with_suffix( 'assets/search/js/table.bundle.js' ),
			$this->get_filters( $list_screen ),
			$request,
			$list_screen,
            $segment_key
		);
	}

	/**
	 * Allow dashicons as label, all the rest is parsed by 'strip_tags'
	 */
	private function sanitize_label( string $label ): string {
		if ( false === strpos( $label, 'dashicons' ) ) {
			$label = strip_tags( $label );
		}

		return trim( $label );
	}

	private function get_filter_label( AC\Column $column ): string {
		$label = $this->sanitize_label( $column->get_custom_label() );

		if ( ! $label ) {
			$label = $this->sanitize_label( $column->get_label() );
		}

		if ( ! $label ) {
			$label = $column->get_type();
		}

		return $label;
	}

	private function get_filters( ListScreen $list_screen ): array {
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

}