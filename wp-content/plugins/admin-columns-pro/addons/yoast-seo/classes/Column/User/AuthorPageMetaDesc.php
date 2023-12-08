<?php

namespace ACA\YoastSeo\Column\User;

use AC;
use AC\MetaType;
use ACA\YoastSeo\Editing;
use ACA\YoastSeo\Export;
use ACP;
use ACP\Editing\Service\Basic;
use ACP\Editing\Storage\Meta;
use ACP\Editing\View\TextArea;

class AuthorPageMetaDesc extends AC\Column\Meta
    implements ACP\Editing\Editable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-yoast_author_meta_desc')
             ->set_group('yoast-seo')
             ->set_label(__('SEO Meta Description', 'codepress-admin-columns'));
    }

    public function get_meta_key()
    {
        return 'wpseo_metadesc';
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Text($this->get_meta_key());
    }

    public function editing()
    {
        return new Basic(new TextArea(), new Meta($this->get_meta_key(), new MetaType(MetaType::USER)));
    }

}