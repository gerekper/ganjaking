<?php


if (!function_exists('a2w_global_template_redirect')) {

    function a2w_global_template_redirect() {
        do_action('a2w_template_redirect');
        
        do_action('a2w_after_template_redirect');
    }

}

if (!class_exists('A2W_JSON_API')) {

    class A2W_JSON_API {

        function __construct($root_menu_slug) {
            $this->root_menu_slug = $root_menu_slug;
            $this->query = new A2W_JSON_API_Query();
            $this->response = new A2W_JSON_API_Response();
            add_action('a2w_template_redirect', array(&$this, 'template_redirect'));
            
            add_action('a2w_after_template_redirect', array(&$this, 'after_template_redirect'));
            
            if(!has_action('template_redirect', 'a2w_global_template_redirect')){
                add_action('template_redirect', 'a2w_global_template_redirect');
            }
            
            // disable menu
            if (defined('A2W_DEBUG_PAGE') && A2W_DEBUG_PAGE) {
                add_action('admin_menu', array(&$this, 'admin_menu'));
            }
            
            add_action('update_option_a2w_json_api_base', array(&$this, 'flush_rewrite_rules'));
            add_action('pre_update_option_a2w_json_api_controllers', array(&$this, 'update_controllers'));
        }

        function template_redirect() {
            // Check to see if there's an appropriate API controller + method    
            $controller = strtolower($this->query->get_controller());
            $available_controllers = $this->get_controllers();
            $enabled_controllers = explode(',', a2w_get_setting('json_api_controllers'));
            $active_controllers = array_intersect($available_controllers, $enabled_controllers);
            
            
            if ($controller) {
                if (empty($this->query->dev)) {
                    //error_reporting(0);
                }

                if (!in_array($controller, $active_controllers)) {
                    $this->error("Unknown controller '$controller'.");
                }

                $controller_path = $this->controller_path($controller);
                if (file_exists($controller_path)) {
                    require_once $controller_path;
                }
                $controller_class = $this->controller_class($controller);

                if (!class_exists($controller_class)) {
                    $this->error("Unknown controller '$controller_class'.");
                }

                $this->controller = new $controller_class();
                $method = $this->query->get_method($controller);

                if ($method) {
                    if(method_exists($this->controller, 'permissions') && !$this->controller->permissions($method)){
                        // skip call if no have permissions
                        return;
                    }

                    // Run action hooks for method
                    do_action("a2w_json_api", $controller, $method);
                    do_action("a2w_json_api-{$controller}-$method");

                    // Error out if nothing is found
                    if ($method == '404' || $method == 'error') {
                        $this->error('Not found');
                    }

                    // Run the method
                    $result = $this->controller->$method();

                    // Handle the result
                    $this->response->respond($result);

                    // Done!
                    exit;
                }
            }
        }
        
        function after_template_redirect() {
            $controller = strtolower($this->query->get_controller());
            
            if ($controller) {
                // If this is a request to the API and we got here, then the authorization key is missing in the request
                // Need return auth error
                $this->error("Invalid authentication.");
            }
        }

        function admin_menu() {
            add_submenu_page($this->root_menu_slug, 'JSON API Settings', 'JSON API', 'manage_options', 'a2w-json-api', array(&$this, 'admin_options'));
        }

        function admin_options() {          
            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.', 'ali2woo'));
            }

            $available_controllers = $this->get_controllers();
            $active_controllers = explode(',', a2w_get_setting('json_api_controllers'));

            if (count($active_controllers) == 1 && empty($active_controllers[0])) {
                $active_controllers = array();
            }

            if (!empty($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], "update-options")) {
                if ((!empty($_REQUEST['action']) || !empty($_REQUEST['action2'])) &&
                        (!empty($_REQUEST['controller']) || !empty($_REQUEST['controllers']))) {
                    if (!empty($_REQUEST['action'])) {
                        $action = $_REQUEST['action'];
                    } else {
                        $action = $_REQUEST['action2'];
                    }

                    if (!empty($_REQUEST['controllers'])) {
                        $controllers = $_REQUEST['controllers'];
                    } else {
                        $controllers = array($_REQUEST['controller']);
                    }

                    foreach ($controllers as $controller) {
                        if (in_array($controller, $available_controllers)) {
                            if ($action == 'activate' && !in_array($controller, $active_controllers)) {
                                $active_controllers[] = $controller;
                            } else if ($action == 'deactivate') {
                                $index = array_search($controller, $active_controllers);
                                if ($index !== false) {
                                    unset($active_controllers[$index]);
                                }
                            }
                        }
                    }
                    a2w_set_setting('json_api_controllers', implode(',', $active_controllers));
                }
                if (isset($_REQUEST['a2w_json_api_base'])) {
                    a2w_set_setting('json_api_base', $_REQUEST['a2w_json_api_base']);
                }
            }
            ?>
            <div class="wrap">
                <div id="icon-options-general" class="icon32"><br /></div>
                <h2>JSON API Settings</h2>
                <form action="admin.php?page=a2w-json-api" method="post">
                    <?php wp_nonce_field('update-options'); ?>
                    <h3>Controllers</h3>
                    <?php $this->print_controller_actions(); ?>
                    <table id="all-plugins-table" class="widefat">
                        <thead>
                            <tr>
                                <th class="manage-column check-column" scope="col"><input type="checkbox" /></th>
                                <th class="manage-column" scope="col">Controller</th>
                                <th class="manage-column" scope="col">Description</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th class="manage-column check-column" scope="col"><input type="checkbox" /></th>
                                <th class="manage-column" scope="col">Controller</th>
                                <th class="manage-column" scope="col">Description</th>
                            </tr>
                        </tfoot>
                        <tbody class="plugins">
                            <?php
                            foreach ($available_controllers as $controller) {

                                $error = false;
                                $active = in_array($controller, $active_controllers);
                                $info = $this->controller_info($controller);

                                if (is_string($info)) {
                                    $active = false;
                                    $error = true;
                                    $info = array(
                                        'name' => $controller,
                                        'description' => "<p><strong>Error</strong>: $info</p>",
                                        'methods' => array(),
                                        'url' => null
                                    );
                                }
                                ?>
                                <tr class="<?php echo ($active ? 'active' : 'inactive'); ?>">
                                    <th class="check-column" scope="row">
                                        <input type="checkbox" name="controllers[]" value="<?php echo $controller; ?>" />
                                    </th>
                                    <td class="plugin-title">
                                        <strong><?php echo $info['name']; ?></strong>
                                        <div class="row-actions-visible">
                <?php
                if ($active) {
                    echo '<a href="' . wp_nonce_url('admin.php?page=a2w-json-api&amp;action=deactivate&amp;controller=' . $controller, 'update-options') . '" title="' . __('Deactivate this controller', 'ali2woo') . '" class="edit">' . __('Deactivate') . '</a>';
                } else if (!$error) {
                    echo '<a href="' . wp_nonce_url('admin.php?page=a2w-json-api&amp;action=activate&amp;controller=' . $controller, 'update-options') . '" title="' . __('Activate this controller', 'ali2woo') . '" class="edit">' . __('Activate') . '</a>';
                }

                if (!empty($info['url'])) {
                    echo ' | ';
                    echo '<a href="' . $info['url'] . '" target="_blank">Docs</a></div>';
                }
                ?>
                                    </td>
                                    <td class="desc">
                                        <p><?php echo $info['description']; ?></p>
                                        <p>
                <?php
                foreach ($info['methods'] as $method) {
                    $url = $this->get_method_url($controller, $method);
                    if ($active) {
                        echo "<code><a href=\"$url\">$method</a></code> ";
                    } else {
                        echo "<code>$method</code> ";
                    }
                }
                ?>
                                        </p>
                                    </td>
                                </tr>
                                        <?php } ?>
                        </tbody>
                    </table>
            <?php $this->print_controller_actions('action2'); ?>
                    <h3>Address</h3>
                    <p>Specify a base URL for JSON API. For example, using <code>api</code> as your API base URL would enable the following <code><?php bloginfo('url'); ?>/api/get_recent_posts/</code>. If you assign a blank value the API will only be available by setting a <code>json</code> query variable.</p>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">API base</th>
                            <td><code><?php bloginfo('url'); ?>/</code><input type="text" name="a2w_json_api_base" value="<?php echo a2w_get_setting('json_api_base'); ?>" size="15" /></td>
                        </tr>
                    </table>
            <?php if (!get_option('permalink_structure', '')) { ?>
                        <br />
                        <p><strong>Note:</strong> User-friendly permalinks are not currently enabled. <a target="_blank" class="button" href="options-permalink.php">Change Permalinks</a>
            <?php } ?>
                    <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e('Save changes', 'ali2woo') ?>" />
                    </p>
                </form>
            </div>
                        <?php
                    }

                    function print_controller_actions($name = 'action') {
                        ?>
            <div class="tablenav">
                <div class="alignleft actions">
                    <select name="<?php echo $name; ?>">
                        <option selected="selected" value="-1">Bulk Actions</option>
                        <option value="activate">Activate</option>
                        <option value="deactivate">Deactivate</option>
                    </select>
                    <input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Apply">
                </div>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
            <?php
        }

        function get_method_url($controller, $method, $options = '') {
            $url = get_bloginfo('url');
            $base = a2w_get_setting('json_api_base');
            $permalink_structure = get_option('permalink_structure', '');
            if (!empty($options) && is_array($options)) {
                $args = array();
                foreach ($options as $key => $value) {
                    $args[] = urlencode($key) . '=' . urlencode($value);
                }
                $args = implode('&', $args);
            } else {
                $args = $options;
            }
            if ($controller != 'core') {
                $method = "$controller/$method";
            }
            if (!empty($base) && !empty($permalink_structure)) {
                if (!empty($args)) {
                    $args = "?$args";
                }
                return "$url/$base/$method/$args";
            } else {
                return "$url?a2w-json=$method&$args";
            }
        }

        function get_controllers() {
            $controllers = array();
            $this->check_directory_for_controllers(A2W_JSON_API_DIR."/controllers", $controllers);
            $this->check_directory_for_controllers(get_stylesheet_directory(), $controllers);
            $controllers = apply_filters('a2w_json_api_controllers', $controllers);
            return array_map('strtolower', $controllers);
        }
        
        function get_enabled_controllers($default = 'core,auth') {
            return explode(',', a2w_get_setting('json_api_controllers', $default));
        }

        function check_directory_for_controllers($dir, &$controllers) {
            $dh = opendir($dir);
            while ($file = readdir($dh)) {
                if (preg_match('/(.+)\.php$/i', $file, $matches)) {
                    $src = file_get_contents("$dir/$file");
                    if (preg_match("/class\s+A2W_JSON_API_{$matches[1]}_Controller/i", $src)) {
                        $controllers[] = $matches[1];
                    }
                }
            }
        }

        function controller_is_active($controller) {
            if (defined('A2W_JSON_API_CONTROLLERS')) {
                $default = A2W_JSON_API_CONTROLLERS;
            } else {
                $default = 'core,auth';
            }
            $active_controllers = explode(',', a2w_get_setting('json_api_controllers', $default));
            return (in_array($controller, $active_controllers));
        }

        function update_controllers($controllers) {
            if (is_array($controllers)) {
                return implode(',', $controllers);
            } else {
                return $controllers;
            }
        }

        function controller_info($controller) {
            $path = $this->controller_path($controller);
            $class = $this->controller_class($controller);
            $response = array(
                'name' => $controller,
                'description' => '(No description available)',
                'methods' => array()
            );
            if (file_exists($path)) {
                $source = file_get_contents($path);
                if (preg_match('/^\s*Controller name:(.+)$/im', $source, $matches)) {
                    $response['name'] = trim($matches[1]);
                }
                if (preg_match('/^\s*Controller description:(.+)$/im', $source, $matches)) {
                    $response['description'] = trim($matches[1]);
                }
                if (preg_match('/^\s*Controller URI:(.+)$/im', $source, $matches)) {
                    $response['docs'] = trim($matches[1]);
                }
                if (!class_exists($class)) {
                    require_once($path);
                }
                
                $response['methods'] = array_diff(get_class_methods($class), array("__construct"));
                
                return $response;
            } else if (is_admin()) {
                return "Cannot find controller class '$class' (filtered path: $path).";
            } else {
                $this->error("Unknown controller '$controller'.");
            }
            return $response;
        }

        function controller_class($controller) {
            return "a2w_json_api_{$controller}_controller";
        }

        function controller_path($controller) {
            $json_api_path = A2W_JSON_API_DIR."/controllers/$controller.php";
            $theme_dir = get_stylesheet_directory();
            $theme_path = "$theme_dir/$controller.php";
            if (file_exists($theme_path)) {
                $path = $theme_path;
            } else if (file_exists($json_api_path)) {
                $path = $json_api_path;
            } else {
                $path = null;
            }
            $controller_class = $this->controller_class($controller);
            return apply_filters("{$controller_class}_path", $path);
        }

        function get_nonce_id($controller, $method) {
            $controller = strtolower($controller);
            $method = strtolower($method);
            return "a2w_json_api-$controller-$method";
        }

        function flush_rewrite_rules() {
            global $wp_rewrite;
            $wp_rewrite->flush_rules();
        }

        function error($message = 'Unknown error', $http_status = 404) {
            $this->response->respond(array('error' => $message), 'error', $http_status);
        }

        function include_value($key) {
            return $this->response->is_value_included($key);
        }

    }

}
