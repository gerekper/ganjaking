<?php

namespace ACP\Search;

use AC;
use AC\Asset\Location;
use AC\ListScreenRepository\Storage;
use AC\Registerable;
use AC\Services;
use ACP\Settings\ListScreen\HideOnScreenCollection;
use ACP\Type\HideOnScreen\Group;

final class Addon implements Registerable
{

    use DefaultSegmentTrait;

    /**
     * @var Storage
     */
    private $storage;

    private $location;

    private $table_preference;

    private $hide_smart_filters;

    private $list_screen_factory;

    private $request;

    public function __construct(
        Storage $storage,
        Location\Absolute $location,
        SegmentRepository\Database $segment_repository,
        AC\ListScreenFactory $list_screen_factory,
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
        $services = new Services();
        $services->add($this->get_table_screen_options())
                 ->add($this->get_column_settings());
        $services->register();

        add_action('ac/table/list_screen', [$this, 'table_screen_request']);
        add_action('wp_ajax_acp_search_comparison_request', [$this, 'comparison_request']);
        add_action('wp_ajax_acp_enable_smart_filtering_button', [$this, 'update_smart_filtering_preference']);
        add_action('acp/admin/settings/hide_on_screen', [$this, 'add_hide_on_screen'], 10, 2);
        add_action('wp_ajax_acp_search_segment_request', [$this, 'segment_request']);
        add_action('ac/table/list_screen', [$this, 'request_setter']);
        add_action('acp/list_screen/deleted', [$this, 'delete_segments_after_list_screen_deleted']);
        add_action('deleted_user', [$this, 'delete_segments_after_user_deleted']);
    }

    public function update_smart_filtering_preference(): void
    {
        check_ajax_referer('ac-ajax');

        $is_active = ('true' === filter_input(INPUT_POST, 'value')) ? 1 : 0;

        (new Preferences\SmartFiltering())->set(filter_input(INPUT_POST, 'list_screen'), $is_active);
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
            $this->location,
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

        $request = new AC\Request();

        $comparison = new RequestHandler\Comparison(
            $this->storage,
            $request,
            $this->list_screen_factory
        );

        $comparison->dispatch($request->get('method'));
    }

    public function table_screen_request(AC\ListScreen $list_screen): void
    {
        if ( ! $list_screen->has_id() ||
             ! $this->is_active($list_screen) ||
             ! TableScreenSupport::is_searchable($list_screen)) {
            return;
        }

        $this->request->add_middleware(new Middleware\Segment($list_screen, $this->segment_repository))
                      ->add_middleware(new Middleware\Request());

        $request_handler = new RequestHandler\Rules($list_screen);
        $request_handler->handle($this->request);

        if ($this->hide_smart_filters->is_hidden($list_screen)) {
            return;
        }

        $table_factory = new TableScriptFactory($this->location);

        $assets = [
            new AC\Asset\Style('aca-search-table', $this->location->with_suffix('assets/search/css/table.css')),
            $table_factory->create(
                $list_screen,
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

    public function request_setter(AC\ListScreen $list_screen): void
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

    public function delete_segments_after_list_screen_deleted(AC\ListScreen $list_screen): void
    {
        $this->segment_repository->delete_all($list_screen->get_id());
    }

    public function delete_segments_after_user_deleted(int $user_id): void
    {
        foreach ($this->segment_repository->find_all_personal($user_id) as $segment) {
            $this->segment_repository->delete($segment->get_key());
        }
    }

}