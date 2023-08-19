<?php

namespace ACP\Admin\Page;

use AC\Admin\RenderableHead;
use AC\Asset;
use AC\Asset\Assets;
use AC\Asset\Location;
use AC\Renderable;
use AC\View;

class Tools implements Asset\Enqueueables, Renderable, RenderableHead
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

    public function render(): string
    {
        $view = new View([
            'sections' => $this->sections,
        ]);

        $view->set_template('admin/page/tools');

        return $view->render();
    }

}