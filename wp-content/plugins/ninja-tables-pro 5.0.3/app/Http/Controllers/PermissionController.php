<?php

namespace NinjaTablesPro\App\Http\Controllers;

use NinjaTables\Framework\Request\Request;
use NinjaTables\Framework\Support\Sanitizer;

class PermissionController extends Controller
{
    public function store(Request $request)
    {
        if (current_user_can('manage_options')) {

            $capability               = $request->get('capability', []);
            $sql_permission           = $request->get('sql_permission', 'no');
            $sanitized_sql_permission = Sanitizer::sanitizeTextField($sql_permission);
            $sanitized_capability     = ninja_tables_sanitize_array($capability);

            update_option('_ninja_tables_permission', $sanitized_capability, false);
            update_option('_ninja_tables_sql_permission', $sanitized_sql_permission, false);

            return $this->json([
                'message' => __('Successfully saved the role(s).', 'ninja-tables')
            ]);

        } else {

            return $this->sendError([
                'message' => __('Sorry, You can not update permissions. Only administrators can update permissions',
                    'ninja-tables')
            ], 423);

        }
    }
}
