<?php

namespace ACA\EC\Column\Event;

use AC;
use ACA\EC\Editing;
use ACP;
use ACP\Search;

class StartDate extends AC\Column\Meta
    implements ACP\Editing\Editable, ACP\Search\Searchable,
               ACP\Filtering\FilterableDateSetting
{

    use ACP\Filtering\FilteringDateSettingTrait;

    public function __construct()
    {
        $this->set_type('start-date')
             ->set_original(true);
    }

    protected function register_settings()
    {
        parent::register_settings();

        $this->add_setting(new ACP\Filtering\Settings\Date($this));
    }

    public function get_meta_key()
    {
        return '_EventStartDate';
    }

    public function get_value($id)
    {
        return '';
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\DateTime(),
            new Editing\Storage\Event\StartDate()
        );
    }

    public function search()
    {
        return new Search\Comparison\Meta\DateTime\ISO(
            $this->get_meta_key(),
            (new AC\Meta\QueryMetaFactory())->create_by_meta_column($this)
        );
    }

}