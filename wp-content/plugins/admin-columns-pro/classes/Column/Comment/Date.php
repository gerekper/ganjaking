<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;

class Date extends AC\Column\Comment\Date
    implements Filtering\FilterableDateSetting, Export\Exportable, Search\Searchable
{

    use Filtering\FilteringDateSettingTrait;

    public function register_settings()
    {
        parent::register_settings();

        $this->add_setting(new Filtering\Settings\Date($this, ['future_past']));
    }

    public function export()
    {
        return new Export\Model\Comment\Date();
    }

    public function search()
    {
        return new Search\Comparison\Comment\Date\Date();
    }

}