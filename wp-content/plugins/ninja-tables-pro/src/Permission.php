<?php namespace NinjaTablesPro;

class Permission
{
    /**
     * Get the permission from the options table.
     */
    public static function get()
    {
	    ninjaTablesValidateNonce();
        $roles = get_editable_roles();
        $formatted = array();
        foreach ($roles as $key => $role) {
            if($key != 'subscriber') {
                $formatted[] = array(
                    'name' => $role['name'],
                    'key'  => $key
                );
            }
        }
        
        $capability = get_option('_ninja_tables_permission');

        if (is_string($capability)) {
            $capability = [];
        }
        wp_send_json(array(
            'capability' => $capability,
            'roles'      => $formatted,
            'sql_permission' => get_option('_ninja_tables_sql_permission')
        ), 200);
    }

    /**
     * Set the permission to the options table.
     */
    public static function set()
    {
	    ninjaTablesValidateNonce();
        if(current_user_can('manage_options')) {
            $capability = isset($_REQUEST['capability']) ? $_REQUEST['capability'] : [];
            $sql_permission =  isset($_REQUEST['sql_permission']) ? sanitize_text_field($_REQUEST['sql_permission']) : 'no';
            update_option('_ninja_tables_permission', $capability, false);
            update_option('_ninja_tables_sql_permission', $sql_permission, false);

            wp_send_json( array(
                'message' => __('Successfully saved the role(s).', 'ninja-tables')
            ), 200 );
        } else {
            wp_send_json_error(array(
                'message' => __('Sorry, You can not update permissions. Only administrators can update permissions', 'ninja-tables')
            ), 423);
        }
    }
    
    public static function getPermission($permission)
    {
	    return get_option('_ninja_tables_permission', $permission);
    }
}
