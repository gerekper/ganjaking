<?php

namespace ACA\YoastSeo\Column\Post;

use AC;
use ACA\YoastSeo\Editing;
use ACA\YoastSeo\Export;
use ACP;

class Title extends AC\Column
    implements ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('wpseo-title')
             ->set_group('yoast-seo')
             ->set_original(true);
    }

    public function get_meta_key()
    {
        return '_yoast_wpseo_title';
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\Text())->set_placeholder(__('Enter your SEO Title', 'codepress-admin-columns')),
            new ACP\Editing\Storage\Post\Meta($this->get_meta_key())
        );
    }

    public function export()
    {
        return new Export\Post\Title();
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Text($this->get_meta_key());
    }

}