<?php

namespace ACA\JetEngine\Column;

use AC;
use ACA\JetEngine\Export;
use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Service\ColumnGroups;
use ACP;

class Meta extends AC\Column implements ACP\Export\Exportable
{

    /**
     * @var Field
     */
    protected $field;

    public function __construct()
    {
        $this->set_group(ColumnGroups::JET_ENGINE)
             ->set_label('JetEngine Meta');
    }

    public function get_raw_value($id)
    {
        return get_metadata($this->list_screen->get_meta_type(), $id, $this->get_type(), true);
    }

    // Delayed constructor
    public function set_field(Field $field)
    {
        $this->field = $field;
    }

    public function get_meta_key()
    {
        return $this->get_type();
    }

    public function export()
    {
        return (new Export\ModelFactory())->create($this, $this->field) ?: false;
    }

}