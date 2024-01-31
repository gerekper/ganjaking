<?php

/**
 * Description of Update
 *
 * @author Ali2Woo Team
 */

namespace Ali2Woo;

class Update {

    /**
     * The plugin current version
     * @var string
     */
    private $current_version;

    /**
     * The plugin remote update path
     * @var string
     */
    private $update_path;

    /**
     * Plugin Slug (plugin_directory/plugin_file.php)
     * @var string
     */
    private $plugin_slug;

    /**
     * Plugin name (plugin_file)
     * @var string
     */
    private $slug;

    /**
     * License User
     * @var string
     */
    private $license_user;

    /**
     * License Key 
     * @var string
     */
    private $license_key;

    /**
     * Initialize a new instance of the WordPress Auto-Update class
     * @param string $current_version
     * @param string $update_path
     * @param string $plugin_slug
     */
    public function __construct($current_version, $update_path, $plugin_slug, $license_user = '', $license_key = '') {
        // Set the class public variables
        $this->current_version = $current_version;
        $this->update_path = $update_path;

        // Set the License
        $this->license_user = $license_user;
        $this->license_key = $license_key;

        // Set the Plugin Slug	
        $this->plugin_slug = $plugin_slug;
        list ($t1, $t2) = explode('/', $plugin_slug);
        $this->slug = $t1;

        // define the alternative API for updating checking
        add_filter('pre_set_site_transient_update_plugins', array(&$this, 'check_update'));

        // Define the alternative response for information checking
        add_filter('plugins_api', array(&$this, 'check_info'), 10, 3);
    }

    static public function clean_autoupdate_cache(){
        delete_transient(sprintf('_%s_updates', A2W()->plugin_slug));
    }

    /**
     * Add our self-hosted autoupdate plugin to the filter transient
     *
     * @param $transient
     * @return object $ transient
     */
    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        $remote_version = $this->getRemote('info');

        // If a newer version is available, add the update
        if (
            $remote_version 
            && version_compare($this->current_version, $remote_version->version, '<')
            && version_compare($remote_version->requires_php, PHP_VERSION, '<=' )
        ) {
            $obj = new \stdClass();
            $obj->slug = $this->slug;
            $obj->last_updated  = $remote_version->last_updated;
            $obj->new_version = $remote_version->version;
            $obj->url = $remote_version->url;
            $obj->plugin = $this->plugin_slug;
            $obj->tested = $remote_version->tested;
            if(!empty($remote_version->package)){
                $obj->package = $remote_version->package;    
            }
            $obj->requires_php = $remote_version->requires_php;

            $transient->response[$this->plugin_slug] = $obj;
        }

        return $transient;
    }

	/**
	 * Plugin information callback.
	 *
	 * @param object $response The response core needs to display the modal.
	 * @param string $action The requested plugins_api() action.
	 * @param object $args Arguments passed to plugins_api().
	 *
	 * @return object An updated $response.
	 */
    public function check_info($response, $action, $arg) {
        // do nothing if this is not about getting plugin information
        if( 'plugin_information' !== $action ) {
            return $response;
        }
        
        if (isset($arg->slug) && $arg->slug === $this->slug) {
            $result = $this->getRemote('info');
            return $result === false ? $response : $result;
        }

        return $response;
    }

    /**
     * Return the remote version
     * 
     * @return string $remote_version
     */
    public function getRemote($action = '') {

        $payload = array(
            'action' => $action,
            'license_user' => $this->license_user,
            'license_key' => $this->license_key,
        );

        $params = array(
            'method'      => 'POST',
            'headers' => array('Content-Type' => 'application/x-www-form-urlencoded'),
            'body' => $payload,
        );

		$cache_key = sprintf('_%s_updates', $this->slug);
		$data      = get_transient( $cache_key );

        if ( false !== $data ) {
			return $data;
		}

        // Make the POST request
        $request = wp_remote_post($this->update_path, $params);
        

        // Check if response is valid
        if (!is_wp_error($request) 
            || wp_remote_retrieve_response_code($request) === 200
            || !empty( trim( wp_remote_retrieve_body( $request ) ) )
            ) {
           
                $data = @unserialize(trim(wp_remote_retrieve_body( $request )));
                set_transient( $cache_key, $data, 12 * HOUR_IN_SECONDS );

                return $data;
        }

        return false;
    }
}
