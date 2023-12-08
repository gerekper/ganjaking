<?php

namespace ACP\Column\Post;

use AC;
use ACP\ConditionalFormat;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

class Modified extends AC\Column\Post\Modified
    implements Sorting\Sortable, Editing\Editable, Filtering\FilterableDateSetting,
               Search\Searchable, ConditionalFormat\Formattable
{

    use Filtering\FilteringDateSettingTrait;

    public function register_settings()
    {
        parent::register_settings();

        $this->add_setting(
            new Filtering\Settings\Date($this)
        );
    }

    public function sorting()
    {
        return new Sorting\Model\OrderBy('modified');
    }

    public function editing()
    {
        return new Editing\Service\Post\Modified();
    }

    public function search()
    {
        return new Search\Comparison\Post\Date\PostModified($this->get_post_type());
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new ConditionalFormat\FormattableConfig(new Formatter\DateFormatter\FormatFormatter());
    }

}