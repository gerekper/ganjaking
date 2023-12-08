<?php

namespace ACA\Pods\Field\Pick;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Value\DbRaw;
use ACP;

class PostFormat extends Field\Pick
{

    use Editing\DefaultServiceTrait;

    public function get_value($id)
    {
        $values = [];

        foreach ($this->get_raw_value($id) as $term_id) {
            $term = get_term($term_id);

            if ($term) {
                $values[] = $term->name;
            }
        }

        return implode(', ', $values);
    }

    public function sorting()
    {
        if ($this->is_multiple()) {
            return false;
        }

        $options = $this->get_options();
        natcasesort($options);

        if (empty($options)) {
            return false;
        }

        return (new ACP\Sorting\Model\MetaMappingFactory())->create(
            $this->get_meta_type(),
            $this->get_meta_key(),
            array_keys($options)
        );
    }

    public function get_raw_value($id)
    {
        return (new DbRaw($this->get_meta_key(), $this->get_meta_type()))->get_value($id);
    }

    public function get_options()
    {
        $formats = get_terms('post_format');

        if ( ! $formats || is_wp_error($formats)) {
            return [];
        }

        $options = [];

        foreach ($formats as $format) {
            $options[$format->term_id] = $format->name;
        }

        natcasesort($options);

        return $options;
    }

}