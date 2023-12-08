<?php

namespace ACP\Column\Media;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;

class Date extends AC\Column\Media\Date
    implements Filtering\FilterableDateSetting, Editing\Editable, Export\Exportable,
               Search\Searchable
{

    use Filtering\FilteringDateSettingTrait;

    public function register_settings()
    {
        parent::register_settings();

        $this->add_setting(new Filtering\Settings\Date($this));
    }

    public function editing()
    {
        return new Editing\Service\Media\Date();
    }

    public function export()
    {
        return new Export\Model\Post\Date();
    }

    public function search()
    {
        return new Search\Comparison\Post\Date\PostDate($this->get_post_type());
    }

}