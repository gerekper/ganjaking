<?php

namespace ACA\JetEngine\Column\Meta;

use AC\Settings\Column\NumberOfItems;
use ACA\JetEngine\Column;
use ACA\JetEngine\Editing\EditableTrait;
use ACA\JetEngine\Field;
use ACA\JetEngine\Search\SearchableTrait;
use ACA\JetEngine\Value\DefaultValueFormatterTrait;
use ACP;

/**
 * @property Field\Type\Checkbox $field
 */
class Checkbox extends Column\Meta implements ACP\Search\Searchable, ACP\Editing\Editable,
                                              ACP\ConditionalFormat\Formattable
{

    use SearchableTrait,
        ACP\ConditionalFormat\ConditionalFormatTrait,
        DefaultValueFormatterTrait,
        EditableTrait;

    protected function register_settings()
    {
        $this->add_setting(new NumberOfItems($this));
    }

}