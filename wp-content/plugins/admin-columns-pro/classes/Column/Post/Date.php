<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;

class Date extends AC\Column\Post\Date
    implements Filtering\FilterableDateSetting, Editing\Editable, Export\Exportable, Search\Searchable
{

    use Filtering\FilteringDateSettingTrait;

    protected function register_settings()
    {
        $this->add_setting(
            new Filtering\Settings\Date($this)
        );
    }

    public function editing()
    {
        return new Editing\Service\Post\Date();
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