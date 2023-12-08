<?php

namespace ACA\YoastSeo\Column\Post;

use AC;
use ACA\YoastSeo\Editing;
use ACA\YoastSeo\Export;
use ACP;

class FocusKW extends AC\Column
    implements ACP\Editing\Editable, ACP\Export\Exportable, ACP\ConditionalFormat\Formattable, ACP\Search\Searchable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('wpseo-focuskw')
             ->set_group('yoast-seo')
             ->set_original(true);
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\Text())->set_placeholder(
                __('Enter your SEO Focus Keywords', 'codepress-admin-columns')
            ),
            new ACP\Editing\Storage\Post\Meta('_yoast_wpseo_focuskw')
        );
    }

    public function export()
    {
        return new ACP\Export\Model\Post\Meta('_yoast_wpseo_focuskw');
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Text('_yoast_wpseo_focuskw');
    }

}