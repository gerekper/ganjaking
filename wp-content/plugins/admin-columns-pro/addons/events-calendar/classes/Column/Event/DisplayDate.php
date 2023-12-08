<?php

namespace ACA\EC\Column\Event;

use AC;
use ACA\EC\Editing;
use ACA\EC\Service\ColumnGroups;
use ACA\EC\Settings;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Search;
use ACP\Sorting\Type\DataType;

class DisplayDate extends AC\Column\Meta
    implements ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\Editing\Editable,
               ACP\ConditionalFormat\Formattable, ACP\Filtering\FilterableDateSetting
{

    use ACP\Filtering\FilteringDateSettingTrait;

    public function __construct()
    {
        $this->set_type('column-ec-event_display_date')
             ->set_label(__('Date', 'codepress-admin-columns'))
             ->set_group(ColumnGroups::EVENTS_CALENDAR);
    }

    public function get_meta_key()
    {
        return $this->get_setting('event_date')->get_value();
    }

    public function get_value($id)
    {
        return $this->get_formatted_value($this->get_raw_value($id));
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\EventDates($this));
        $this->add_setting(new AC\Settings\Column\Date($this));
        $this->add_setting(new ACP\Filtering\Settings\Date($this));
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta($this->get_meta_key(), new DataType(DataType::DATETIME));
    }

    public function search()
    {
        return new Search\Comparison\Meta\DateTime\ISO(
            $this->get_meta_key(),
            (new AC\Meta\QueryMetaFactory())->create_by_meta_column($this)
        );
    }

    public function editing()
    {
        switch ($this->get_meta_key()) {
            case '_EventStartDate':
                return new ACP\Editing\Service\Basic(
                    new ACP\Editing\View\DateTime(),
                    new Editing\Storage\Event\StartDate()
                );
            case '_EventEndDate':
                return new ACP\Editing\Service\Basic(
                    new ACP\Editing\View\DateTime(),
                    new Editing\Storage\Event\EndDate()
                );
            default:
                return false;
        }
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(new ACP\ConditionalFormat\Formatter\DateFormatter\FormatFormatter());
    }

}