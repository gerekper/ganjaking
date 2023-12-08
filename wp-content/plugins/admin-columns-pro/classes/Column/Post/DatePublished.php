<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

class DatePublished extends AC\Column\Post\DatePublished
    implements Sorting\Sortable, Filtering\FilterableDateSetting, Editing\Editable,
               Search\Searchable, ConditionalFormat\Formattable
{

    use Filtering\FilteringDateSettingTrait;

    public function sorting()
    {
        return new Sorting\Model\OrderBy('date');
    }

    public function register_settings()
    {
        parent::register_settings();

        $this->add_setting(new Filtering\Settings\Date($this, ['future_past']));
    }

    public function search()
    {
        return new Search\Comparison\Post\Date\PostPublished($this->get_post_type());
    }

    public function editing()
    {
        return new Editing\Service\Post\Date();
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new ConditionalFormat\FormattableConfig(
            new ConditionalFormat\Formatter\DateFormatter\FormatFormatter('Y-m-d H:i:s')
        );
    }

}