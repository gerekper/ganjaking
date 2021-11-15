<?php

/**
 * Parses module file and retrieves module metadata
 *
 * @param  string $module_file Path to module file
 *
 * @return array
 */
function pm_pro_get_module_data( $module_file ) {
    $default_headers = array(
        'name'        => 'Module Name',
        'description' => 'Description',
        'plugin_uri'  => 'Module URI',
        'thumbnail'   => 'Thumbnail URL',
        'class'       => 'Integration Class',
        'author'      => 'Author',
        'author_uri'  => 'Author URI',
        'version'     => 'Version',
    );

    $module_data = get_file_data( $module_file, $default_headers, 'pm_pro_modules' );
    $module_dir = pm_pro_module_data_format( $module_file );

    $module_data['thumbnail'] = pm_pro_config('define.url') . 'modules/' . $module_dir['file_name'] . $module_data['thumbnail'];

    return $module_data;
}

/**
 * Gets all the available modules
 *
 * @return array
 */
function pm_pro_get_modules() {
    $module_root  = pm_pro_config('define.module_path');
    $modules_dir  = @opendir( $module_root);
    $modules      = array();
    $module_files = array();

    if ( $modules_dir ) {

        while ( ( $file = readdir( $modules_dir ) ) !== false ) {

            if ( substr( $file, 0, 1 ) == '.' ) {
                continue;
            }

            if ( is_dir( $module_root . '/' . $file ) ) {
                $plugins_subdir = @opendir( $module_root . '/' . $file );

                if ( $plugins_subdir ) {

                    while ( ( $subfile = readdir( $plugins_subdir ) ) !== false ) {
                        if ( substr( $subfile, 0, 1 ) == '.' ) {
                            continue;
                        }

                        if ( substr($subfile, -4) == '.php' ) {
                            $module_files[] = "$file/$subfile";
                        }
                    }

                    closedir( $plugins_subdir );
                }
            }
        }

        closedir( $modules_dir );
    }

    if ( $module_files ) {

        foreach ( $module_files as $module_file ) {

            if ( ! is_readable( "$module_root/$module_file" ) ) {
                continue;
            }

            $module_data  = pm_pro_get_module_data( "$module_root/$module_file" );

            if ( empty ( $module_data['name'] ) ) {
                continue;
            }

            $file_base = wp_normalize_path( $module_file );


            $modules[ $file_base ] = $module_data;
        }
    }

    return $modules;
}

/**
 * Get a single module data
 *
 * @param  string $module
 *
 * @return WP_Error|Array
 */
function pm_pro_get_module( $module ) {
    $module_root  = pm_pro_config('define.module_path');

    $module_data = pm_pro_get_module_data( "$module_root/$module" );

    if ( empty ( $module_data['name'] ) ) {
        return new WP_Error( 'not-valid-plugin', __( 'This is not a valid plugin', 'pm-pro' ) );
    }

    return $module_data;
}

/**
 * Get the meta key to store the active module list
 *
 * @return string
 */
function pm_pro_active_module_key() {
    return 'pm_pro_active_modules';
}

/**
 * Get active modules
 *
 * @return array
 */
function pm_pro_get_active_modules() {
    return get_option( pm_pro_active_module_key(), array() );
}

/**
 * Check if a module is active
 *
 * @param  string $module basename
 *
 * @return boolean
 */
function pm_pro_is_module_active( $module ) {
    return in_array( $module, pm_pro_get_active_modules() );
}

/**
 * Check if a module is inactive
 *
 * @param  string $module basename
 *
 * @return boolean
 */
function pm_pro_is_module_inactive( $module ) {
    return ! pm_pro_is_module_active( $module );
}

/**
 * Activate a module
 *
 * @param  string $module basename of the module file
 *
 * @return WP_Error|null WP_Error on invalid file or null on success.
 */
function pm_pro_activate_module( $module ) {
    $current = pm_pro_get_active_modules();
    $module_root = pm_pro_config('define.module_path');
    $module_data = pm_pro_get_module_data( "$module_root/$module" );

    if ( empty ( $module_data['name'] ) ) {
        return new WP_Error( 'invalid-module', __( 'The module is invalid', 'pm-pro' ) );
    }

    // activate if enactive
    if ( pm_pro_is_module_inactive( $module ) ) {
        $current[] = $module;
        sort($current);

        // deactivate the addon if exists
        $module_classes = pm_pro_module_class_map( $module );

        foreach ( $module_classes as $key => $module_class ) {
            if ( $module_class && class_exists( $module_class ) ) {
                $reflector  = new ReflectionClass( $module_class );
                $addon_path = plugin_basename( $reflector->getFileName() );

                deactivate_plugins( $addon_path );
            }
        }

        $file_path = plugin_basename( "$module_root/$module" );

        update_option( pm_pro_active_module_key(), $current );

        $module_info  = pm_pro_module_data_format($module);

        $modules_path = pm_pro_config('define');
        $module_path  = $modules_path['module_path'] . '/' . $module;

        if ( file_exists( $module_path ) ) {
            include_once $module_path;
            do_action( 'pm-activation-' . strtolower( $module_info['file_name'] ), $module_info );
        }
    }

    return null;
}

function pm_pro_module_class_map( $module ) {
    $modules = array(
        'cpm-time-tracker/time-tracker.php'         => 'CPM_Time_Tracker',
        'gantt-chart/gantt.php'                     => 'CPM_Gantt_Chart',
        'kanboard/cpm-kanboard.php'                 => 'CPM_Kanboard',
        'pm-invoice/invoice.php'                    => 'WeDevs_CPM_Invoice',
        'project-manager-pro-buddypress/cpm-bp.php' => 'CPM_BP',
        'pm-sub-task/sub-task.php'                  => 'CPMST_Sub_Task',
        'pm-woo-order/cpm-woo.php'                  => 'CPM_Woo_Order',
    );

    //if ( array_key_exists( $module, $modules) ) {
        return $modules;
    //}

    //return false;
}

function pm_pro_register_activation_hook( $hook, $function) {
    if ( file_exists( $hook ) ) {
        $pathinfo = pathinfo( $hook );
        add_action( 'pm-activation-' . $pathinfo['filename'], $function);
    }
}

/**
 * Deactivate a module
 *
 * @param  string $module basename of the module file
 *
 * @return boolean
 */
function pm_pro_deactivate_module( $module ) {
    $current = pm_pro_get_active_modules();
    $module_info  = pm_pro_module_data_format($module);

    if ( pm_pro_is_module_active( $module ) ) {

        $key = array_search( $module, $current );

        if ( false !== $key ) {
            unset( $current[ $key ] );

            sort($current);
        }

        update_option( pm_pro_active_module_key(), $current);
        do_action( 'pm-deactivation-' . $module_info['file_name'], $module_info );
        return true;
    }

    return false;
}

function pm_pro_module_data_format( $path ) {
    $pathinfo = pathinfo( $path );

    return array(
        'path'   => $path,
        'slug'   => $pathinfo['dirname'],
        'file'   => $pathinfo['basename'],
        'file_name' => $pathinfo['filename']
    );
}

function pm_pro_load_module_routers() {
    $modules_path   = pm_pro_config( 'define.module_path' );
    $active_modules = pm_pro_get_active_modules();

    foreach ( $active_modules as $key => $module ) {

        $module_info = pm_pro_module_data_format( $module );
        $files       = glob( $modules_path . '/' . $module_info['slug'] . '/routes/*.php' );

        if ( $files !== false ) {

            foreach ( $files as $file ) {
                require_once $file;
            }

            unset( $file );
            unset( $files );
        }
    }
}

