<?php

namespace ACP\Column\User;

use AC;
use ACP\ConditionalFormat;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;

class FirstPost extends AC\Column\User\FirstPost
    implements Sorting\Sortable, Export\Exportable, Search\Searchable, ConditionalFormat\Formattable
{

    public function sorting()
    {
        return new Sorting\Model\User\MaxPostDate(
            $this->get_related_post_type(), $this->get_related_post_stati(), true
        );
    }

    public function export()
    {
        return new Export\Model\StrippedValue($this);
    }

    public function search()
    {
        return new Search\Comparison\User\MaxPostDate(
            $this->get_related_post_type(),
            (array)$this->get_related_post_stati(),
            true
        );
    }

    public function conditional_format(): ?FormattableConfig
    {
        $setting = $this->get_setting('post');

        $formatter = $setting instanceof AC\Settings\Column\Post && AC\Settings\Column\Post::PROPERTY_DATE === $setting->get_value(
        )
            ? new ConditionalFormat\Formatter\FilterHtmlFormatter(
                new ConditionalFormat\Formatter\DateFormatter\FormatFormatter()
            )
            : null;

        return new ConditionalFormat\FormattableConfig($formatter);
    }

}