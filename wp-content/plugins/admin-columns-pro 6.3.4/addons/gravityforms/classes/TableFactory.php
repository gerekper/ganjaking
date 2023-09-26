<?php

namespace ACA\GravityForms;

use GF_Entry_List_Table;
use GFCommon;

class TableFactory
{

    public function create(string $screen_id, int $form_id): GF_Entry_List_Table
    {
        require_once(GFCommon::get_base_path() . '/entry_list.php');

        return new GF_Entry_List_Table([
            'screen'  => $screen_id,
            'form_id' => $form_id,
        ]);
    }

}