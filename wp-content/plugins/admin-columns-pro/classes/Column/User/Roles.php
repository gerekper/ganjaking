<?php

namespace ACP\Column\User;

use AC;
use ACP\ConditionalFormat;
use ACP\Editing;
use ACP\Export;
use ACP\Search;
use ACP\Sorting;
use WP_User;

class Roles extends AC\Column\Meta
    implements Editing\Editable, Sorting\Sortable, Search\Searchable, Export\Exportable,
               ConditionalFormat\Formattable
{

    use ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-roles')
             ->set_label(__('Roles', 'codepress-admin-columns'));
    }

    public function get_meta_key()
    {
        global $wpdb;

        return $wpdb->get_blog_prefix() . 'capabilities'; // WPMU compatible
    }

    public function get_value($user_id)
    {
        $user = new WP_User($user_id);

        $roles = [];
        foreach (ac_helper()->user->translate_roles($user->roles) as $role => $label) {
            $roles[] = ac_helper()->html->tooltip($label, $role);
        }

        if (empty($roles)) {
            return $this->get_empty_char();
        }

        return implode($this->get_separator(), $roles);
    }

    public function editing()
    {
        return new Editing\Service\User\Role(true);
    }

    public function sorting()
    {
        return new Sorting\Model\User\Roles($this->get_meta_key());
    }

    public function search()
    {
        return new Search\Comparison\User\Role($this->get_meta_key());
    }

    public function export()
    {
        return new Export\Model\User\Role(true);
    }

}