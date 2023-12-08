<?php

namespace ACP\Settings\Column;

use AC;

class FeaturedImageDisplay extends AC\Settings\Column
    implements AC\Settings\FormatValue
{

    /**
     * @var string
     */
    private $featured_image_display;

    protected function define_options()
    {
        return [
            'featured_image_display' => 'true_false',
        ];
    }

    public function create_view()
    {
        $select = $this->create_element('select');

        $select->set_attribute('data-refresh', 'column')
               ->set_options([
                   'count'      => _x('Count', 'Number/count of items'),
                   'title'      => __('Title'),
                   'true_false' => __('True / False', 'codepress-admin-columns'),
               ]);

        $view = new AC\View([
            'label'   => __('Field Type', 'codepress-admin-columns'),
            'setting' => $select,
        ]);

        return $view;
    }

    public function get_featured_image_display()
    {
        return $this->featured_image_display;
    }

    /**
     * @param $featured_image_display
     *
     * @return bool
     */
    public function set_featured_image_display($featured_image_display)
    {
        $this->featured_image_display = $featured_image_display;

        return true;
    }

    public function format($value, $original_value)
    {
        switch ($this->get_featured_image_display()) {
            case 'count':
                return count($value) ?: false;

            case 'title':
                $values = [];

                foreach ($value as $id) {
                    $post = get_post($id);

                    if ( ! $post) {
                        continue;
                    }

                    $url = get_edit_post_link($post);

                    $value = $post->post_title ?: $post->ID;

                    if ( ! post_type_exists($post->post_type)) {
                        $value = ac_helper()->html->tooltip(
                            $post->post_title,
                            sprintf(
                                __('Post type %s no longer exists', 'codepress-admin-columns'),
                                sprintf('"%s"', $post->post_type)
                            )
                        );
                    } elseif ($url) {
                        $value = ac_helper()->html->link($url, $value);
                    }

                    $values[] = $value;
                }

                return implode(ac_helper()->html->divider(), $values);

            case 'true_false':
                return count($value) ? ac_helper()->icon->yes() : false;

            default :
                return $value;
        }
    }

}