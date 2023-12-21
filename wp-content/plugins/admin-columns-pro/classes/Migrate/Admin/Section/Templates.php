<?php

declare(strict_types=1);

namespace ACP\Migrate\Admin\Section;

use AC\ListScreen;
use AC\ListScreenRepository\Filter;
use AC\Renderable;
use AC\View;
use ACP\ListScreenPreferences;
use ACP\ListScreenRepository\Template;
use ACP\Search\SegmentCollection;
use ACP\Type\Url\Preview;

final class Templates implements Renderable
{

    private $repository;

    private $network_only;

    public function __construct(
        Template $repository,
        bool $network_only
    ) {
        $this->repository = $repository;
        $this->network_only = $network_only;
    }

    private function get_list_items(): array
    {
        $list_screens = $this->repository->find_all();

        $filter = $this->network_only
            ? new Filter\Network()
            : new Filter\Site();

        $list_screens = $filter->filter($list_screens);

        $items = [];

        $sources = $this->repository->get_sources();

        foreach ($list_screens as $list_screen) {
            $source = $sources->contains($list_screen->get_id())
                ? $sources->get($list_screen->get_id())
                : null;

            $items[] = [
                'url_preview' => new Preview($list_screen->get_table_url()),
                'list_id'     => (string)$list_screen->get_id(),
                'file_name'   => $source,
                'description' => $this->render_description($list_screen),
                'source'      => $this->render_source($source),
                'label_page'  => $list_screen->get_label(),
                'label_view'  => $list_screen->get_title() ?: $list_screen->get_label(),
            ];
        }

        usort($items, [$this, 'sort_by_label']);

        return $items;
    }

    public function sort_by_label(array $a, array $b): int
    {
        if ($a['label_page'] !== $b['label_page']) {
            return $a['label_page'] > $b['label_page'] ? 1 : 0;
        }

        return $a['label_view'] > $b['label_view'] ? 1 : 0;
    }

    private function render_description(ListScreen $list_screen): string
    {
        $column_names = [];

        foreach ($list_screen->get_columns() as $column) {
            $column_names[] = strip_tags($column->get_custom_label()) ?: $column->get_label();
        }

        $segment_names = [];

        $segments = $list_screen->get_preference(ListScreenPreferences::SHARED_SEGMENTS);

        if ($segments instanceof SegmentCollection) {
            foreach ($segments as $segment) {
                $segment_names[] = $segment->get_name();
            }
        }

        $html = ac_helper()->html->tooltip(
            sprintf(_n('%s column', '%s columns', count($column_names)), count($column_names)),
            ac_helper()->string->enumeration_list($column_names, 'and')
        );

        if ($segment_names) {
            $html = sprintf(
                "%s %s %s",
                $html,
                __('and', 'codepress-admin-columns'),
                ac_helper()->html->tooltip(
                    sprintf(_n('%s saved filter', '%s saved filters', count($segment_names)), count($segment_names)),
                    ac_helper()->string->enumeration_list($segment_names, 'and')
                )
            );
        }

        return $html;
    }

    private function render_source(string $path): string
    {
        return sprintf(
            '<span data-ac-tip="%s">%s</span>',
            sprintf(
                '%s: %s',
                __('Path', 'codepress-admin-columns'),
                $path
            ),
            __('File', 'codepress-admin-columns')
        );
    }

    public function render(): string
    {
        $items = $this->get_list_items();

        if ( ! $items) {
            return '';
        }

        return (new View())->set('list_items', $items)
                           ->set_template('admin/section-template')
                           ->render();
    }

}