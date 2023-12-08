<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Settings;
use ACP\Sorting;

class FeaturedImage extends AC\Column\Post\FeaturedImage
    implements Editing\Editable, Sorting\Sortable, Export\Exportable, Search\Searchable
{

    public function sorting()
    {
        if ('filesize' === $this->get_display_value()) {
            return new Sorting\Model\Post\FeaturedImageSize($this->get_meta_key(), $this->get_post_type());
        }

        return new Sorting\Model\Post\Meta($this->get_meta_key());
    }

    public function editing()
    {
        return new Editing\Service\Post\FeaturedImage();
    }

    public function export()
    {
        return new Export\Model\Post\FeaturedImage();
    }

    public function search()
    {
        if ('filesize' === $this->get_display_value()) {
            return null;
        }

        return new Search\Comparison\Post\FeaturedImage($this->get_post_type());
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\Column\FeaturedImage($this));
    }

    /**
     * @return bool|string
     */
    private function get_display_value()
    {
        $setting = $this->get_setting('featured_image');

        if ( ! $setting instanceof Settings\Column\FeaturedImage) {
            return false;
        }

        return $setting->get_featured_image_display();
    }

}