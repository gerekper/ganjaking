<?php

namespace ACA\YoastSeo\Column\Post;

use AC;
use AC\MetaType;

class FocusKeywordCount extends AC\Column
{

    public function __construct()
    {
        $this->set_label(__('Keyphrase Occurrence', 'codepress-admin-columns'))
             ->set_type('column-wpseo_column_focuskw_count')
             ->set_group('yoast-seo');
    }

    private function get_focus_keyword($id)
    {
        return get_metadata(MetaType::POST, $id, '_yoast_wpseo_focuskw', true);
    }

    public function get_value($id)
    {
        $key = strtolower($this->get_focus_keyword($id));

        if ( ! $key) {
            return $this->get_empty_char();
        }

        $values = [
            ac_helper()->html->tooltip(
                $this->calculate_occurrence(get_post_field('post_title', $id, 'raw'), $key),
                __('Title')
            ),
            ac_helper()->html->tooltip(
                $this->calculate_occurrence(get_post_field('post_content', $id, 'raw'), $key),
                __('Content')
            ),
        ];

        return implode(' / ', $values);
    }

    private function calculate_occurrence($content, $key)
    {
        return substr_count(strip_tags(strtolower($content)), $key);
    }

}