<?php

namespace ACP\Migrate\Admin\Table;

use AC;
use AC\ListScreen;
use AC\ListScreenCollection;
use ACP\Search\SegmentCollection;
use ACP\Search\SegmentRepository;

class Export extends AC\Admin\Table
{

    private $storage;

    private $list_keys_factory;

    private $segment_repository;

    private $is_network;

    public function __construct(
        AC\ListScreenRepository\Storage $storage,
        AC\Table\ListKeysFactoryInterface $list_keys_factory,
        SegmentRepository\Storage $segment_repository,
        bool $is_network = false
    ) {
        $this->storage = $storage;
        $this->list_keys_factory = $list_keys_factory;
        $this->segment_repository = $segment_repository;
        $this->is_network = $is_network;
    }

    private function get_list_screens(): ListScreenCollection
    {
        $list_screens = [];

        foreach ($this->list_keys_factory->create()->all() as $list_key) {
            if ($list_key->is_network() !== $this->is_network) {
                continue;
            }

            $list_screens[] = $this->storage->find_all_by_key(
                $list_key,
                new AC\ListScreenRepository\Sort\Label()
            )->get_copy();
        }

        return new ListScreenCollection(array_merge(...$list_screens));
    }

    public function get_rows(): ListScreenCollection
    {
        $list_screens = $this->get_list_screens();

        if ($list_screens->count() < 1) {
            $this->message = __('No column settings available.', 'codepress-admin-columns');
        }

        return $list_screens;
    }

    private function get_segments(AC\Type\ListScreenId $list_screen_id): SegmentCollection
    {
        return $this->segment_repository->find_all_global($list_screen_id);
    }

    public function get_column(string $key, $data): string
    {
        if ( ! $data instanceof ListScreen) {
            return '';
        }

        switch ($key) {
            case 'check-column' :
                return sprintf(
                    '<input name="list_screen_ids[]" type="checkbox" id="export-%1$s" value="%1$s">',
                    $data->get_layout_id()
                );
            case 'name' :
                return sprintf(
                    '<a href="%s">%s</a>',
                    esc_url((string)$data->get_editor_url()),
                    $data->get_title() ?: $data->get_label()
                );
            case 'list-table' :
                return sprintf(
                    '<label for="export-%s"><strong>%s</strong></label>',
                    $data->get_layout_id(),
                    $data->get_label()
                );
            case 'id' :
                return sprintf('<small>%s</small>', $data->get_layout_id());
            case 'source' :
                return $this->get_source($data);
            case 'segments':
                return $this->column_segments($this->get_segments($data->get_id()));
            case 'actions' :
                return sprintf(
                    '<button data-download="%s">%s</button>',
                    $data->get_layout_id(),
                    __('Export', 'codepress-admin-columns')
                );
        }

        return '';
    }

    private function column_segments(SegmentCollection $segments): string
    {
        if ( ! $segments->count()) {
            return '-';
        }

        $data = [];

        foreach ($segments as $segment) {
            $data[(string)$segment->get_key()] = $segment->get_name();
        }

        $label = sprintf(
            _n(
                '%s saved filter',
                '%s saved filters',
                count($data),
                'codepress-admin-columns'
            ),
            count($data)
        );

        return sprintf(
            '<div data-segments="%s" data-label="%s"></div>',
            esc_attr(json_encode($data)),
            esc_attr($label)
        );
    }

    private function get_repository_label($repository_name)
    {
        $labels = [
            'acp-database' => __('Database', 'codepress-admin-columns'),
            'acp-file'     => __('File', 'codepress-admin-columns'),
        ];

        return $labels[$repository_name] ?? $repository_name;
    }

    private function get_source(ListScreen $list_screen)
    {
        foreach (array_reverse($this->storage->get_repositories()) as $name => $repo) {
            if ( ! $repo->find($list_screen->get_id())) {
                continue;
            }

            $label = $this->get_repository_label($name);

            if ($repo->has_source($list_screen->get_id())) {
                return sprintf(
                    '<span data-ac-tip="%s">%s</span>',
                    sprintf('%s: %s', __('Path', 'codepress-admin-columns'), $repo->get_source($list_screen->get_id())),
                    $label
                );
            }

            return $label;
        }

        return null;
    }

    public function get_headings(): array
    {
        return [
            'check-column' => '<input type="checkbox" data-select-all>',
            'list-table'   => __('List Table', 'codepress-admin-columns'),
            'name'         => __('Name', 'codepress-admin-columns'),
            'source'       => __('Source', 'codepress-admin-columns'),
            'segments'     => __('Saved Filters', 'codepress-admin-columns'),
            'id'           => __('ID', 'codepress-admin-columns'),
            'actions'      => '',
        ];
    }

}