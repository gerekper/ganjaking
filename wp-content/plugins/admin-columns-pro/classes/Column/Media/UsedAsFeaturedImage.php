<?php

namespace ACP\Column\Media;

use AC;
use ACP\Search;
use ACP\Settings;

class UsedAsFeaturedImage extends AC\Column
    implements Search\Searchable
{

    public function __construct()
    {
        $this->set_type('column-used_as_featured_image')
             ->set_group('media-image')
             ->set_label(__('Featured Image', 'codepress-admin-columns'));
    }

    public function get_raw_value($id)
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "
                SELECT pm.post_id 
                FROM $wpdb->postmeta AS pm
                    JOIN $wpdb->posts AS pp ON pp.ID = pm.post_id 
                WHERE meta_key = '_thumbnail_id' AND meta_value = %d",
            $id
        );

        return $wpdb->get_col($sql);
    }

    protected function register_settings()
    {
        $this->add_setting(new Settings\Column\FeaturedImageDisplay($this));
    }

    public function search()
    {
        return new Search\Comparison\Media\UsedAsFeaturedImage();
    }

}