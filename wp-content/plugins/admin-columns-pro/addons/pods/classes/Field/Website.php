<?php

namespace ACA\Pods\Field;

use ACA\Pods\Editing;
use ACA\Pods\Field;
use ACA\Pods\Sorting;
use ACP\Search;

class Website extends Field
{

    use Editing\DefaultServiceTrait;
    use Sorting\DefaultSortingTrait;

    public function get_value($id)
    {
        $field = $this->column->get_pod_field();
        $target = $field['options']['website_new_window'] ? '_blank' : '_self';
        $url = $this->get_raw_value($id);

        return ac_helper()->html->link($url, str_replace(['http://', 'https://'], '', $url), ['target' => $target]);
    }

}