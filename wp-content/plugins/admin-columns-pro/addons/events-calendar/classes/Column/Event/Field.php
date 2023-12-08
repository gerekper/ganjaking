<?php

namespace ACA\EC\Column\Event;

use AC;
use ACA\EC\API;
use ACA\EC\Editing;
use ACA\EC\Service\ColumnGroups;
use ACP;

class Field extends AC\Column\Meta
    implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Export\Exportable,
               ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_label(__('Additional Fields', 'tribe-events-calendar-pro'))
             ->set_group(ColumnGroups::EVENTS_CALENDAR_FIELDS);
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function get_value($id)
    {
        $fields = tribe_get_custom_fields($id);

        if ( ! is_array($fields)) {
            return $this->get_empty_char();
        }

        $label = $this->get('label');

        if ( ! array_key_exists($label, $fields)) {
            return $this->get_empty_char();
        }

        return $fields[$label];
    }

    /**
     * @return false|array
     */
    public function get_field()
    {
        return API::get_field($this->get_meta_key());
    }

    /**
     * @return string
     */
    public function get_meta_key()
    {
        return substr($this->get_type(), strlen('column'));
    }

    /**
     * @param string $var
     *
     * @return mixed
     */
    public function get($var)
    {
        return API::get($this->get_meta_key(), $var);
    }

    public function is_valid()
    {
        return API::is_pro();
    }

    public function sorting()
    {
        return (new ACP\Sorting\Model\MetaFactory())->create($this->get_meta_type(), $this->get_meta_key());
    }

    public function editing()
    {
        return new ACP\Editing\Service\Basic(
            (new ACP\Editing\View\Text())->set_clear_button(true),
            new ACP\Editing\Storage\Post\Meta($this->get_meta_key())
        );
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }

    public function search()
    {
        return new ACP\Search\Comparison\Meta\Text($this->get_meta_key());
    }

}