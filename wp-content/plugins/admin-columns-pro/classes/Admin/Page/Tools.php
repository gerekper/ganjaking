<?php

namespace ACP\Admin\Page;

use AC;
use AC\Admin\RenderableHead;
use AC\Admin\ScreenOptions;
use AC\Asset;
use AC\Asset\Assets;
use AC\Asset\Location;
use AC\Renderable;
use AC\View;

class Tools implements Asset\Enqueueables, Renderable, RenderableHead, ScreenOptions
{

    public const NAME = 'import-export';

    /**
     * @var Renderable[]
     */
    private $sections = [];

    /**
     * @var Location\Absolute
     */
    private $location;

    /**
     * @var Renderable
     */
    private $head;

    public function __construct(Location\Absolute $location, Renderable $head)
    {
        $this->location = $location;
        $this->head = $head;
    }

    public function render_head(): Renderable
    {
        return $this->head;
    }

    /**
     * @param Renderable $section
     *
     * @return $this
     */
    public function add_section(Renderable $section)
    {
        $this->sections[] = $section;

        return $this;
    }

    public function get_assets(): Assets
    {
        $script = new Asset\Script('acp-script-tools', $this->location->with_suffix('assets/core/js/tools.js'));
        $script->localize(
            'ACP_Tools_i18n',
            Asset\Script\Localize\Translation::create([
                'excluded'   => __('excluded', 'codepress-admin-columns'),
                'included'   => __('included', 'codepress-admin-columns'),
                'out_of'     => _x('out of', 'x out of x', 'codepress-admin-columns'),
                'select_all' => __('Select / Deselect All', 'codepress-admin-columns'),
            ])
        );
        $script->add_inline_variable('AC', [
            '_ajax_nonce' => wp_create_nonce(AC\Ajax\Handler::NONCE_ACTION),
        ]);

        $assets = new Asset\Assets([
            new Asset\Style('acp-style-tools', $this->location->with_suffix('assets/core/css/admin-tools.css')),
            $script,
        ]);

        foreach ($this->sections as $section) {
            if ($section instanceof Asset\Enqueueables) {
                $assets->add_collection($section->get_assets());
            }
        }

        return $assets;
    }

    private function get_attr_class(): string
    {
        $classes = [];

        if ($this->option_id()->is_active()) {
            $classes[] = 'show-list-screen-id';
        }

        if ($this->option_source()->is_active()) {
            $classes[] = 'show-list-screen-source';
        }

        return implode(' ', $classes);
    }

    public function render(): string
    {
        $view = new View([
            'sections'   => $this->sections,
            'attr_class' => $this->get_attr_class(),
        ]);

        $view->set_template('admin/page/tools');

        return $view->render();
    }

    private function option_source(): AC\Admin\ScreenOption\ListScreenSource
    {
        return new AC\Admin\ScreenOption\ListScreenSource(new AC\Admin\Preference\ScreenOptions());
    }

    private function option_id(): AC\Admin\ScreenOption\ListScreenId
    {
        return new AC\Admin\ScreenOption\ListScreenId(new AC\Admin\Preference\ScreenOptions());
    }

    public function get_screen_options(): array
    {
        return [
            $this->option_id(),
            $this->option_source(),
        ];
    }

}