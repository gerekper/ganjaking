<?php

namespace ACA\YoastSeo\Column\Post;

use AC;
use AC\Meta\QueryMetaFactory;
use ACA\YoastSeo\Editing;
use ACA\YoastSeo\Export;
use ACA\YoastSeo\Search;
use ACP;

class FacebookImage extends AC\Column\Meta
    implements ACP\Editing\Editable, ACP\Search\Searchable, ACP\Export\Exportable
{

    public function __construct()
    {
        $this->set_group('yoast-seo')
             ->set_label(__('Facebook Image', 'codepress-admin-columns'))
             ->set_type('column-yoast_facebook_image');
    }

    private function get_meta_key_id()
    {
        return '_yoast_wpseo_opengraph-image-id';
    }

    private function get_meta_key_url()
    {
        return '_yoast_wpseo_opengraph-image';
    }

    public function get_meta_key()
    {
        return $this->get_meta_key_id();
    }

    protected function register_settings()
    {
        parent::register_settings();

        $this->add_setting(new AC\Settings\Column\Image($this));
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\Image())->set_clear_button(true),
            new Editing\Storage\Post\SocialImage($this->get_meta_key_id(), $this->get_meta_key_url())
        );
    }

    public function search()
    {
        $query_meta_factory = new QueryMetaFactory();

        return new ACP\Search\Comparison\Meta\Image(
            $this->get_meta_key(),
            $query_meta_factory->create($this->get_meta_key(), $this->get_meta_type())
        );
    }

    public function export()
    {
        return new ACP\Export\Model\Post\Meta($this->get_meta_key_url());
    }

}