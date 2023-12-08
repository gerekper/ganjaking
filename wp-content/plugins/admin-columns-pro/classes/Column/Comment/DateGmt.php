<?php

namespace ACP\Column\Comment;

use AC;
use ACP\ConditionalFormat;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

class DateGmt extends AC\Column\Comment\DateGmt
    implements Sorting\Sortable, Filtering\FilterableDateSetting, Search\Searchable,
               ConditionalFormat\Formattable
{

    use Filtering\FilteringDateSettingTrait;

    public function register_settings()
    {
        $this->add_setting(new Filtering\Settings\Date($this, ['future_past']));
    }

    public function sorting()
    {
        return new Sorting\Model\OrderBy('comment_date_gmt');
    }

    public function search()
    {
        return new Search\Comparison\Comment\Date\Gmt();
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new ConditionalFormat\FormattableConfig(new ConditionalFormat\Formatter\DateFormatter\FormatFormatter());
    }

}