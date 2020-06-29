<?php

// Integrate WP List Table for Master Log

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WP_List_Table_for_Nominee extends WP_List_Table {

    // Prepare Items
    public function prepare_items() {
        global $wpdb;

        $this->process_bulk_action();
        $columns = $this->get_columns();

        $hidden = $this->get_hidden_columns();

        $user = get_current_user_id();
        $screen = get_current_screen();
        $perPage = RSTabManagement::rs_get_value_for_no_of_item_perpage($user, $screen);
        $currentPage = $this->get_pagenum();
        $newdata = $wpdb->get_results("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='rs_selected_nominee' AND meta_value != ''", ARRAY_A);
        $num_rows = count($newdata);
        $data = $this->table_data();
        
        usort($data, array(&$this, 'sort_data'));        
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $num_rows,
            'per_page' => $perPage
        ));

        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

        $this->_column_headers = array($columns, $hidden);

        $this->items = $data;
    }

    public function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'sno' => __('S.No', SRP_LOCALE),
            'buyer' => __('Buyer', SRP_LOCALE),
            'nominee' => __('Nominee', SRP_LOCALE),
            'action' => __('Action', SRP_LOCALE),
        );

        return $columns;
    }

    public function get_hidden_columns() {
        return array();
    }

    public function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="id[]" value="%s" />', $item['cb']
        );
    }

    public function get_bulk_actions() {
        $columns = array(
            'delete' => __('Delete', SRP_LOCALE),
            'delete_all' => __('Delete All', SRP_LOCALE),
        );

        return $columns;
    }

    public function process_bulk_action() {
        global $wpdb;
        $getusers = $wpdb->get_results("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='rs_selected_nominee' AND meta_value != ''", ARRAY_A);
        if ('delete_all' === $this->current_action()) {
            if (is_array($getusers) && !empty($getusers)) {
                foreach ($getusers as $eachuser) {
                    $user_id = isset($eachuser['user_id']) ? $eachuser['user_id'] : '0';
                    update_user_meta($user_id, 'rs_selected_nominee', '');
                }
            }
        } elseif ('delete' === $this->current_action()) {
            $newupdates = array();
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids) && !empty($ids)) {
                foreach ($ids as $id) {
                    update_user_meta($id, 'rs_selected_nominee', '');
                }
            }
        }
    }

    private function table_data() {
        ?>
        <style type="text/css">
            .rs_delete_nominee {
                border: 2px solid #a1a1a1;
                padding: 3px 9px;
                background: #dddddd;
                width: 5px;
                border-radius: 25px;
            }
            .rs_delete_nominee:hover {
                cursor: pointer;
                background:red;
                color:#fff;
                border: 2px solid #fff;
            }
            .switch {
                position: relative;
                display: inline-block;
                width: 60px;
                height: 34px;
            }
            /* Hide default HTML checkbox */
            .switch input {display:none;}

            /* The slider */
            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                -webkit-transition: .4s;
                transition: .4s;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 26px;
                width: 26px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                -webkit-transition: .4s;
                transition: .4s;
            }

            input:checked + .slider {
                background-color: #2196F3;
            }

            input:focus + .slider {
                box-shadow: 0 0 1px #2196F3;
            }

            input:checked + .slider:before {
                -webkit-transform: translateX(26px);
                -ms-transform: translateX(26px);
                transform: translateX(26px);
            }

            /* Rounded sliders */
            .slider.round {
                border-radius: 34px;
            }

            .slider.round:before {
                border-radius: 50%;
            }
        </style>        
        <?php

        global $wpdb;
        $data = array();
        $i = 1;
        $getusers = $wpdb->get_results("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='rs_selected_nominee' AND meta_value != ''", ARRAY_A);
        if (is_array($getusers) && !empty($getusers)) {
            foreach ($getusers as $eachuser) {
                $user_id = isset($eachuser['user_id']) ? $eachuser['user_id'] : '0';
                $buyer_name = get_user_by('id', $user_id);
                $getnominee = get_user_meta($user_id, 'rs_selected_nominee', true);
                if ($getnominee != '') {
                    $nominee_id = get_user_by('id', $getnominee);
                    $nominee_name = is_object($nominee_id) ? $nominee_id->user_login : 'Guest';
                    $checked = '';
                    if (get_user_meta($user_id, 'rs_enable_nominee', true) == 'yes') {
                        $checked = "checked='checked'";
                    } else {
                        $checked = '';
                    }
                    $data[] = array(
                        'cb' => $user_id,
                        'sno' => $i,
                        'buyer' => is_object($buyer_name) ? $buyer_name->user_login : 'Guest',
                        'nominee' => $nominee_name,
                        'action' => '<label class="switch"><input type="checkbox" class="rs_enable_disable" ' . $checked . '" id="rs_enable_disable" data-userid="' . $user_id . '" data-nomineeid="' . $getnominee . '"><div class="slider round"></div></label>',
                    );
                    $i++;
                }
            }
        }
        return $data;
    }

    public function column_id($item) {
        return $item['sno'];
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'sno':
            case 'buyer':
            case 'nominee':
            case 'action':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    private function sort_data($a, $b) {

        $orderby = 'sno';
        $order = 'asc';

        if (!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }

        if (!empty($_GET['order'])) {
            $order = $_GET['order'];
        }
        $result = strnatcmp($a[$orderby], $b[$orderby]);

        if ($order === 'asc') {
            return $result;
        }

        return -$result;
    }

}
