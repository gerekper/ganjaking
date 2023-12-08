<?php

namespace ACA\Types\Field;

use AC\MetaType;
use ACA\Types\Field;
use ACP\Editing;
use ACP\Search\Comparison;
use ACP\Sorting;

class Url extends Field
{

    public function get_value($id)
    {
        $url = $this->get_raw_value($id);

        return ac_helper()->html->link($url, urldecode(str_replace(['http://', 'https://'], '', $url)));
    }

    public function editing()
    {
        $validation = $this->get('validate');
        $view = $validation && isset($validation['url']['active']) && 1 === (int)$validation['url']['active']
            ? new Editing\View\Url()
            : new Editing\View\Text();

        return new Editing\Service\Basic(
            $view->set_clear_button(true),
            new Editing\Storage\Meta($this->get_meta_key(), new MetaType($this->get_meta_type()))
        );
    }

    public function sorting()
    {
        return (new Sorting\Model\MetaFactory())->create($this->get_meta_type(), $this->get_meta_key());
    }

    public function search()
    {
        return new Comparison\Meta\Text(
            $this->column->get_meta_key()
        );
    }

}