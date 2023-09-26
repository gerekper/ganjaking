<?php

namespace ACA\Types\Settings;

use AC;
use AC\View;

class ChildPostType extends AC\Settings\Column
{

    /**
     * @var string
     */
    private $post_type;

    protected function define_options()
    {
        return ['post_type'];
    }

    public function create_view()
    {
        $select = $this->create_element('select');

        $select
            ->set_no_result(__('No child post types available.', 'codepress-admin-columns'))
            ->set_options($this->get_post_types());

        $view = new View([
            'label'   => __('Post Type', 'codepress-admin-columns'),
            'setting' => $select,
        ]);

        return $view;
    }

    /**
     * @return string
     */
    public function get_post_type()
    {
        if (null === $this->post_type) {
            // Default
            $this->set_post_type($this->get_first_post_type());
        }

        return $this->post_type;
    }

    /**
     * @param string $post_type
     *
     * @return true
     */
    public function set_post_type($post_type)
    {
        $this->post_type = $post_type;

        return true;
    }

    /**
     * @return string
     */
    private function get_first_post_type()
    {
        $post_types = $this->get_post_types();

        reset($post_types);

        return key($post_types);
    }

    /**
     * @param string $post_type
     *
     * @return string
     */
    private function get_post_type_label($post_type)
    {
        return get_post_type_labels(get_post_type_object($post_type))->singular_name;
    }

    /**
     * @return array
     */
    private function get_post_types()
    {
        $options = [];

        $post_types = wpcf_pr_get_has($this->column->get_post_type());

        if ($post_types) {
            foreach ($post_types as $key => $data) {
                $options[$key] = $this->get_post_type_label($key);
            }
        }

        return $options;
    }

}