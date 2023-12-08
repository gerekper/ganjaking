<?php

namespace ACP\Column\User;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class AdminColorScheme extends AC\Column\Meta
    implements Sorting\Sortable, Search\Searchable, Editing\Editable, ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-color-scheme')
             ->set_label(__('Admin Color Scheme', 'codepress-admin-columns'));
    }

    public function get_value($user_id)
    {
        $value = $this->get_raw_value($user_id);

        if ( ! $value) {
            return $this->get_empty_char();
        }

        $color_schemes = $this->get_color_schemes();

        return $color_schemes[$value] ?: $value;
    }

    public function get_meta_key()
    {
        return 'admin_color';
    }

    private function get_color_schemes()
    {
        global $_wp_admin_css_colors;

        $values = [];

        foreach ($_wp_admin_css_colors as $key => $admin_css_color) {
            $values[$key] = $admin_css_color->name;
        }

        return $values;
    }

    public function sorting()
    {
        $choices = $this->get_color_schemes();
        natcasesort($choices);

        return (new Sorting\Model\MetaMappingFactory())->create('user', $this->get_meta_key(), $choices);
    }

    public function search()
    {
        return new Search\Comparison\Meta\Select($this->get_meta_key(), $this->get_color_schemes());
    }

    public function editing()
    {
        return new Editing\Service\Basic(
            new Editing\View\Select($this->get_color_schemes()),
            new Editing\Storage\User\Meta($this->get_meta_key())
        );
    }

}