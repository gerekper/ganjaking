<?php

namespace ACP\Column\Taxonomy;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Sorting;
use ACP\Sorting\Sortable;

class Excerpt extends AC\Column
    implements Editing\Editable, ConditionalFormat\Formattable, Sortable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-excerpt');
        $this->set_label(__('Description', 'codepress-admin-columns'));
    }

    public function get_raw_value($term_id)
    {
        return ac_helper()->taxonomy->get_term_field('description', $term_id, $this->get_taxonomy());
    }

    public function editing()
    {
        return new Editing\Service\Basic(
            new Editing\View\TextArea(),
            new Editing\Storage\Taxonomy\Field($this->get_taxonomy(), 'description')
        );
    }

    public function sorting()
    {
        return new Sorting\Model\Taxonomy\Excerpt();
    }

    public function register_settings()
    {
        $this->add_setting(new AC\Settings\Column\WordLimit($this));
    }

}