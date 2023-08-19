<?php

declare(strict_types=1);

namespace ACP\Search;

use AC;
use AC\Asset\Location;
use AC\ListScreen;
use AC\ListScreenFactory;
use AC\ListScreenRepository\Storage;
use AC\Registerable;
use ACP;
use ACP\Settings\ListScreen\HideOnScreenCollection;
use ACP\Type\HideOnScreen\Group;

final class Addon implements Registerable
{

    use ACP\Search\DefaultSegmentTrait;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var Location
     */
    private $location;

    /**
     * @var Preferences\SmartFiltering
     */
    private $table_preference;

    /**
     * @var Settings\HideOnScreen\SmartFilters
     */
    private $hide_smart_filters;

    /**
     * @var ListScreenFactory
     */
    private $list_screen_factory;

    private $request;

    public function __construct(
        Storage $storage,
        Location\Absolute $location,
        SegmentRepository $segment_repository,
        ListScreenFactory $list_screen_factory,
        AC\Request $request
    ) {
        $this->storage = $storage;
        $this->location = $location;
        $this->segment_repository = $segment_repository;
        $this->list_screen_factory = $list_screen_factory;
        $this->table_preference = new Preferences\SmartFiltering();
        $this->hide_smart_filters = new Settings\HideOnScreen\SmartFilters();
        $this->request = $request;
    }

    private function is_active(AC\ListScreen $list_screen): bool
    {
        return (bool)apply_filters(
            'acp/search/is_active',
            $this->table_preference->is_active($list_screen),
            $list_screen
        );
    }

    public function register(): void
    {
        $this->get_column_settings()->register();
        $this->get_table_screen_options()->register();

        add_action('ac/table/list_screen', [$this, 'table_screen_request']);
        add_action('wp_ajax_acp_search_comparison_request', [$this, 'comparison_request']);
        add_action('acp/admin/settings/hide_on_screen', [$this, 'add_hide_on_screen'], 10, 2);

        add_action('wp_ajax_acp_search_segment_request', [$this, 'segment_request']);

        add_action('ac/table/list_screen', [$this, 'request_setter']);
        add_action('acp/list_screen/deleted', [$this, 'delete_segments_after_list_screen_deleted']);
        add_action('deleted_user', [$this, 'delete_segments_after_user_deleted']);
    }

    private function get_column_settings(): Settings
    {
        return new Settings([
            new AC\Asset\Style('acp-search-admin', $this->location->with_suffix('assets/search/css/admin.css')),
        ]);
    }

    private function get_table_screen_options(): TableScreenOptions
    {
        return new TableScreenOptions(
            [
                new AC\Asset\Script(
                    'acp-search-table-screen-options',
                    $this->location->with_suffix('assets/search/js/screen-options.bundle.js'),
                    ['ac-table']
                ),
            ],
            $this->table_preference,
            $this->hide_smart_filters
        );
    }

    public function add_hide_on_screen(HideOnScreenCollection $collection, AC\ListScreen $list_screen): void
    {
        if ( ! TableScreenFactory::get_table_screen_reference($list_screen)) {
            return;
        }

        $collection->add($this->hide_smart_filters, new Group(Group::FEATURE), 40)
                   ->add(new Settings\HideOnScreen\SavedFilters(), new Group(Group::FEATURE), 41);
    }

    public function comparison_request(): void
    {
        check_ajax_referer('ac-ajax');

        $comparison = new RequestHandler\Comparison(
            $this->storage,
            $this->request,
            $this->list_screen_factory
        );

        $comparison->dispatch($this->request->get('method'));
    }

    public function table_screen_request(AC\ListScreen $list_screen): void
    {
        if ( ! $this->is_active($list_screen)) {
            return;
        }

        $this->request->add_middleware(new Middleware\Segment($list_screen, $this->segment_repository))
                      ->add_middleware(new Middleware\Request());

        $request_handler = new RequestHandler\Rules($list_screen);
        $request_handler->handle($this->request);

        if ($this->hide_smart_filters->is_hidden($list_screen)) {
            return;
        }

        $assets = [
            new AC\Asset\Style('aca-search-table', $this->location->with_suffix('assets/search/css/table.css')),
            new AC\Asset\Script('aca-search-moment', $this->location->with_suffix('assets/search/js/moment.min.js')),
            new AC\Asset\Script(
                'aca-search-querybuilder',
                $this->location->with_suffix('assets/search/js/query-builder.standalone.min.js'),
                ['jquery', 'jquery-ui-datepicker']
            ),
            new Asset\Script\Table(
                'aca-search-table',
                $this->location->with_suffix('assets/search/js/table.bundle.js'),
                $this->get_filters($list_screen),
                $this->request,
                $this->get_default_segment_key($list_screen)
            ),
        ];

        $table_screen = TableScreenFactory::create(
            $list_screen,
            $assets
        );

        if ($table_screen) {
            $table_screen->register();
        }
    }

    private function get_filters(AC\ListScreen $list_screen): array
    {
        $filters = [];

        foreach ($list_screen->get_columns() as $column) {
            $setting = $column->get_setting('search');

            if ( ! $setting instanceof Settings\Column) {
                continue;
            }

            $is_active = apply_filters_deprecated(
                'acp/search/smart-filtering-active',
                [$setting->is_active(), $setting],
                '5.2',
                'Smart filtering can be disabled using the UI.'
            );

            if ( ! $is_active) {
                continue;
            }

            if ( ! $column instanceof Searchable || ! $column->search()) {
                continue;
            }

            $filter = new Middleware\Filter(
                $column->get_name(),
                $column->search(),
                $this->get_filter_label($column)
            );

            $filters[] = apply_filters('acp/search/filters', $filter(), $column);
        }

        return $filters;
    }

    private function get_filter_label(AC\Column $column): string
    {
        $label = $this->sanitize_label($column->get_custom_label());

        if ( ! $label) {
            $label = $this->sanitize_label($column->get_label());
        }

        if ( ! $label) {
            $label = $column->get_type();
        }

        return $label;
    }

    /**
     * Allow dashicons as label, all the rest is parsed by 'strip_tags'
     */
    private function sanitize_label(string $label): string
    {
        if (false === strpos($label, 'dashicons')) {
            $label = strip_tags($label);
        }

        return trim($label);
    }
    
    public function request_setter(ListScreen $list_screen): void
    {
        $search_setter = new RequestHandler\RequestSetter(
            $list_screen,
            $this->segment_repository
        );
        $search_setter->handle($this->request);
    }

    public function segment_request(): void
    {
        check_ajax_referer('ac-ajax');

        $controller = new RequestHandler\Segment(
            $this->storage,
            $this->request,
            $this->segment_repository
        );

        $controller->dispatch($this->request->get('method'));
    }

    public function delete_segments_after_list_screen_deleted(ListScreen $list_screen): void
    {
        foreach ($this->segment_repository->find_all($list_screen->get_id()) as $segment) {
            $this->segment_repository->delete($segment->get_key());
        }
    }

    public function delete_segments_after_user_deleted(int $user_id): void
    {
        foreach ($this->segment_repository->find_all_by_user($user_id) as $segment) {
            $this->segment_repository->delete($segment->get_key());
        }
    }

}