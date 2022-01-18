<?php

/**
 * WP Reset PRO
 * https://wpreset.com/
 * (c) WebFactory Ltd, 2015 - 2021
 */


// include only file
if (!defined('ABSPATH')) {
    die('Do not open this file directly.');
}


class WP_Reset_Collections
{
    public $license_activation_known_plugins = array(
        'under-construction-page' => 'Under Construction Page PRO',
        'minimal-coming-soon-maintenance-mode' => 'Coming Soon & Maintenance Mode PRO',
        'google-maps-widget' => 'Google Maps Widget PRO',
        '301-redirects' => 'WP 301 Redirects PRO',
        'ninja-tables-pro' => 'Ninja Tables PRO',
        'wp-seopress-pro' => 'SEOPress PRO',
        'elementor-pro' => 'Elementor PRO',
        'astra-addon' => 'Astra PRO addon',
        'advanced-custom-fields-pro' => 'Advanced Custom Fields PRO',
        'oxygen' => 'Oxygen Builder',
        'oxygen-gutenberg' => 'Oxygen Builder Gutenberg Integration',
        'oxygen-woocommerce' => 'Oxygen Builder WooCommerce Integration'
    );
    /**
     * Get all user's collection from cache or server
     *
     * @param boolean $skip_cache
     * @return array
     */
    function get_collections($force_reload_cache = false)
    {
        $collections = get_transient('wp_reset_collections');

        if (false === $collections || $force_reload_cache == true) {
            $collections = $this->get_collections_remote();
            if (is_wp_error($collections)) {
                $this->clear_cache();
                return $collections;
            } else {
                $collections = $this->clean_raw_collections($collections);
                set_transient('wp_reset_collections', $collections, DAY_IN_SECONDS);
            }
        }

        return $collections;
    } // get_collections


    /**
     * Get an array of collections, keyed with their ID
     *
     * @return array
     */
    function get_collections_keyed()
    {
        $collections = $this->get_collections();
        $collections_keyed = array();
        foreach ($collections as $collection) {
            $collection['has_plugins'] = false;
            $collection['has_themes'] = false;

            $items = array();
            if (!empty($collection['items'])) {
                foreach ($collection['items'] as $item) {
                    if ($item['type'] == 'plugin') {
                        $collection['has_plugins'] = true;
                    }
                    if ($item['type'] == 'theme') {
                        $collection['has_themes'] = true;
                    }
                    $items[$item['id']] = $item;
                }
            }
            $collection['items'] = $items;
            //TODO:
            @$collections_keyed[$collection['id']] = $collection;
        }

        return $collections_keyed;
    } // get_collections_keyed


    /**
     * Clear collections cache
     *
     * @return void
     */
    function clear_cache()
    {
        delete_transient('wp_reset_collections');
    } // clear_cache


    /**
     * Fetch user's collections from the server
     *
     * @return array | WP_Error
     */
    function get_collections_remote()
    {
        global $wp_reset_cloud;

        $res = $wp_reset_cloud->query_cloud_server(
            array(
                'cloud_action' => 'collections_get'
            )
        );

        if (is_wp_error($res)) {
            return new WP_Error(1, 'An error has occurred while fetching collections from the cloud. ' . $res->get_error_message());
        } elseif ($res['success'] == false) {
            return new WP_Error(1, 'An error has occurred while fetching collections from the cloud. ' . $res['data']);
        } else {
            return $res['data'];
        }
    } // get_collections_remote


    /**
     * Applies filters to collections fethed from the server
     *
     * @param array $collections
     *
     * @return array
     */
    function clean_raw_collections($collections)
    {
        array_walk_recursive($collections, function (&$val, $ind) {
            $val = stripslashes($val);
        });

        return $collections;
    } // clean_raw_collections


    /**
     * Creates HTML for a single collection
     *
     * @param array $collection
     * @return string
     */
    function get_collection_card($collection)
    {
        global $wp_reset;
        $out = '';

        $out .= '<div class="card" data-collection-id="' . $collection['id'] . '">';
        $out .= $wp_reset->get_card_header($collection['name'], 'card-collection-' . $collection['id'], array('collapse_button' => true));
        $out .= '<div class="card-body">';

        $out .= '<div class="half"><p class="_mb0">';
        $out .= '<div class="dropdown">
    <a class="button dropdown-toggle" href="#">Install collection</a>
    <div class="dropdown-menu">
      <a class="dropdown-item install-collection" data-activate="true" data-delete="false" href="#">Install &amp; activate collection</a>
      <a class="dropdown-item install-collection" data-activate="false" data-delete="false" href="#">Install collection</a>
      <a class="dropdown-item install-collection" data-activate="true" data-delete="true" href="#">Delete installed plugins &amp; themes then install &amp; activate collection</a>
      <a class="dropdown-item install-collection" data-activate="false" data-delete="true" href="#">Delete installed plugins &amp; themes then install collection</a>
    </div>
    </div>';
        if (WP_Reset_Utility::whitelabel_filter()) {
            $out .= '<a class="button add-collection-item" href="#" data-collection-id="' . $collection['id'] . '">Add new plugin or theme</a>';
        }
        $out .= '</div>';

        if (WP_Reset_Utility::whitelabel_filter()) {
            $out .= '<div class="half textright"><p class="_mb0">';
            $out .= '<div class="dropdown collection-actions">
        <a class="button dropdown-toggle" href="#">Actions</a>
        <div class="dropdown-menu">
        <a class="dropdown-item add-new-collection" href="#">Add new collection</a>
        <a class="dropdown-item edit-collection-name" data-text-done="Collection name saved." data-text-confirm="Save" data-text-title="Edit collection name" href="#">Rename collection</a>
        <a class="dropdown-item button-delete delete-collection" data-text-wait="Deleting collection. Please wait." data-text-done="Collection deleted" data-confirm-title="Are you sure you want to delete the selected collection and all of its items?" href="#">Delete collection</a>
        </div>
        </div>';
            $out .= '</p></div>';
        }


        $out .= '<table class="collection-table">';
        $out .= '<tr><th>Type</th><th>Name &amp; Note</th><th class="actions">Actions</th></tr>';
        $out .= '<tr class="table-empty hidden"><td colspan="3" class="textcenter"><p><b>Collection is empty.</b><br><a class="add-collection-item" href="#" data-collection-id="' . $collection['id'] . '">Add a plugin from the WP repository ' . (count($wp_reset->cloud_services) > 0 ? 'or a ZIP archive':'') . '</a></p></td></tr>';
        foreach ((array) $collection['items'] as $item) {
            $out .= $this->get_collection_item($item);
        } // foreach
        $out .= '</table>';
        $out .= '</div>';
        $out .= '</div>'; // card

        return $out;
    } // get_collection_card


    /**
     * Return HTML for a single collection item, plugin or theme
     *
     * @param array $item
     * @return string
     */
    function get_collection_item($item)
    {
        global $wp_reset;
        $options = $wp_reset->get_options();

        $out = '';

        $out .= '<tr data-collection-item-id="' . $item['id'] . '">';
        $out .= '<td>';
        if ($item['type'] == 'plugin') {
            $out .= '<span class="dashicons dashicons-admin-plugins" title="Plugin" data-tooltip="Plugin"></span>';
        } else {
            $out .= '<span class="dashicons dashicons-admin-appearance" title="Theme" data-tooltip="Theme"></span>';
        }
        if ($item['source'] == 'repo') {
            $out .= '<span class="dashicons dashicons-wordpress" title="Comes from the WordPress repository" data-tooltip="Comes from the WordPress repository"></span>';
        } else {
            if (!isset($item['location'])) {
                $item['location'] = '';
            }

            switch ($item['location']) {
                case 'wpreset':
                    $out .= '<span class="dashicons wpr-cloud-icon" title="Comes from a 3rd party source (ZIP file) stored in WP Reset Cloud" data-tooltip="Comes from a 3rd party source (ZIP file) stored in WP Reset Cloud"><img src="' . $wp_reset->plugin_url . 'img/wp-reset-icon.png' . '" /></span>';
                    break;
                case 'dropbox':
                    $out .= '<span class="dashicons wpr-cloud-icon" title="Comes from a 3rd party source (ZIP file) stored in Dropbox" data-tooltip="Comes from a 3rd party source (ZIP file) stored in Dropbox"><img src="' . $wp_reset->plugin_url . 'img/dropbox-icon.png' . '" /></span>';
                    break;
                case 'icedrive':
                    $out .= '<span class="dashicons wpr-cloud-icon" title="Comes from a 3rd party source (ZIP file) stored in Icedrive" data-tooltip="Comes from a 3rd party source (ZIP file) stored in Icedrive"><img src="' . $wp_reset->plugin_url . 'img/icedrive-icon.png' . '" /></span>';
                    break;
                case 'gdrive':
                    $out .= '<span class="dashicons wpr-cloud-icon" title="Comes from a 3rd party source (ZIP file) stored in Google Drive" data-tooltip="Comes from a 3rd party source (ZIP file) stored in Google Drive"><img src="' . $wp_reset->plugin_url . 'img/gdrive-icon.png' . '" /></span>';
                    break;
                case 'pcloud':
                case 'pcloudeu':
                    $out .= '<span class="dashicons wpr-cloud-icon" title="Comes from a 3rd party source (ZIP file) stored in pCloud" data-tooltip="Comes from a 3rd party source (ZIP file) stored in pCloud"><img src="' . $wp_reset->plugin_url . 'img/pcloud-icon.png' . '" /></span>';
                    break;
                default:
                    $out .= '<span class="dashicons dashicons-media-archive" title="Comes from a 3rd party source (ZIP file)" data-tooltip="Comes from a 3rd party source (ZIP file)"></span>';
                    break;
            }
        }
        $out .= '</td>';
        $out .= '<td class="collection-item-details">' . htmlspecialchars(html_entity_decode($item['name'])) . ' v' . $item['version'];
        if (!empty($item['license_key'])) {
            if (WP_Reset_Utility::whitelabel_filter()) {
                $out .= '<br>License key: ' . htmlspecialchars(html_entity_decode($item['license_key'])) . '';
            }
        }
        if (!empty($item['note'])) {
            $out .= '<br><i>' . html_entity_decode($item['note']) . '</i>';
        }
        $out .= '</td>';
        $out .= '<td class="textcenter">';
        $out .= '<div class="dropdown">
            <a class="button dropdown-toggle" href="#">Actions</a>
            <div class="dropdown-menu">
                <a href="#" data-activate="false" class="dropdown-item install-collection-item" data-text-done="Collection item installed">Install</a>
                <a href="#" data-activate="true" class="dropdown-item install-collection-item" data-text-done="Collection item installed">Install &amp; Activate</a>';
        if (WP_Reset_Utility::whitelabel_filter()) {
            $out .= '<a href="#" class="dropdown-item edit-collection-item" data-item-id="' . $item['id'] . '" data-item-type="' . $item['type'] . '" data-item-source="' . $item['source'] . '" data-item-slug="' . $item['slug'] . '" data-item-note="' . esc_attr($item['note']) . '" data-item-license_key="' . @esc_attr($item['license_key']) . '" data-collection-idxx="' . (33) . '">Edit</a>
                <a href="#" class="dropdown-item button-link-delete button-delete delete-collection-item" data-btn-confirm="Delete collection item" data-text-wait="Deleting collection item. Please wait." data-text-done="Collection item deleted" data-confirm-title="Are you sure you want to delete the selected collection item?">Delete</a>';
        }

        $out .= '</div>
        </div>';
        $out .= '</td>';
        $out .= '</tr>';

        return $out;
    } // get_collection_item


    /**
     * Echoes content for entire collections tab
     *
     * @return null
     */
    function tab_collections()
    {
        global $wp_reset;

        $plugin_name = $wp_reset->get_rebranding('name');
        if($plugin_name === false){
            $plugin_name = 'WP Reset';
        }
        if (is_multisite()) {
            echo '<div class="card">';
            echo '<p class="mb0 wpmu-error">Collections are <b>not compatible</b> with WP multisite (WPMU). Using them would modify plugin and theme files shared by multiple sites in the WP network.</p>';
            echo '</div>';
            return;
        }

        echo '<div class="card">';
        echo $wp_reset->get_card_header('What are Plugin & Theme Collections?', 'collections-info', array('collapse_button' => true));
        echo '<div class="card-body">';

        if(count($wp_reset->cloud_services) > 0){
            echo '<p>' . __('Have a set of plugins (and themes) that you install and activate after every reset? Or on every fresh WP installation? Well, no more clicking install &amp; active for ten minutes! Build the collection once and install it with one click as many times as needed.</p><p>' . $plugin_name . ' stores collections in the cloud so they\'re accessible on every site you build. You can use free plugins and themes from the official repo, and PRO ones by uploading a ZIP file. We\'ll safely store your license keys too, so you have everything in one place.', 'wp-reset') . '</p>';        
            echo '<p>' . __('List of supported PRO plugin for automatic license activation<a href="#" class="toggle-auto-activation-supported-list" style="margin-left:4px;">(show)</a>', 'wp-reset') . '</p>';
        } else {
            echo '<p>' . __('Have a set of plugins (and themes) that you install and activate after every reset? Or on every fresh WP installation? Well, no more clicking install &amp; active for ten minutes! Build the collection once and install it with one click as many times as needed.</p>', 'wp-reset') . '</p>';            
        }
        echo '<ul class="plain-list auto-activation-supported-list hidden">';
        foreach ($this->license_activation_known_plugins as $plugin) {
            echo '<li>' . $plugin . '</li>';
        }
        echo '</ul>';

        echo '<p>';
        if (WP_Reset_Utility::whitelabel_filter()) {
            echo '<a class="button add-new-collection" href="#">Add new collection</a> &nbsp;';
        }

        if(count($wp_reset->cloud_services) > 0){
            echo '<a class="button reload-collections" href="#">Reload collections from the cloud</a>';
        }
        echo '</p>';

        echo '</div>';
        echo '</div>'; // collections-info

        echo '<div id="new-collection-placeholder" style="display: none;"></div>';

        $collections = $this->get_collections(false);

        if (is_wp_error($collections)) {
            echo '<div class="card">';
            echo '<p class="textcenter mb0"><b>' . $collections->get_error_message() . '</b><br><br><a class="button reload-collections" href="#">Reload collections from the cloud</a></p>';
            echo '</div>';
        } else {
            if (!empty($collections)) {
                foreach ($collections as $collection) {
                    echo $this->get_collection_card($collection);
                } // foreach
            }
            echo '<div id="no-collections" class="card' . (!empty($collections) ? ' hidden' : '') . '">';
            echo '<p class="textcenter mb0">You don\'t have any collections.<br><br><a href="#" class="button button-primary add-new-collection">Create Your First Collection</a></p>';
            echo '</div>';
        } // if collections

        echo '<div class="hidden edit-collection-item-dialog textleft">';
        echo '<div class="option-group"><span>Type:</span>';
        WP_Reset_Utility::create_toogle_switch('dialog-collection-item-type', array('saved_value' => '', 'value' => 'theme', 'class' => 'collection-item-type'));
        echo '</div>';

        if(count($wp_reset->cloud_services) > 0){
            echo '<div class="option-group"><span>Source:</span>';
            WP_Reset_Utility::create_toogle_switch('dialog-collection-item-source', array('saved_value' => 'repo', 'value' => 'zip', 'class' => 'collection-item-source'));
            echo '</div>';
        }

        echo '<div id="edit-collection-item-source-wp" class="option-group"><span>Slug:</span><select placeholder="Enter plugin or theme repository slug" class="dialog-collection-item-slug" style="width:25em;"></select></div>';
        
        echo '<div class="option-group" id="edit-collection-item-source-zip" style="display:none;"><span>ZIP:</span><input type="file" class="dialog-collection-item-zip" style="width:25em;" />';
        echo '<p class="note">ZIP archive containing an installable plugin or theme. The same archive you\'d use when adding a plugin via Plugins - Add New.</p>';
        echo '</div>';
        

        echo '<div class="option-group" id="edit-collection-item-license-key"><span>License Key:</span><input name="dialog-collection-item-license-key" class="dialog-collection-item-license-key" placeholder="" type="text" class="regular-text" value="">';
        echo '<p class="note">License key is used to automatically activate supported premium plugins and themes, including those using Freemius for license management. For unsupported premium plugins we only securely store the key. If your plugin does not require a license key just leave the field empty.</p>';
        echo '</div>';

        echo '<div class="option-group"><span>Note:</span><textarea rows="3" placeholder="The note will be visible only to you" class="regular-text dialog-collection-item-note" value=""></textarea></div>';
        echo '<input type="hidden" class="dialog-collection-item-id" value="0">';
        echo '<input type="hidden" class="dialog-collection-id" value="0">';
        echo '</div>';
    } // tab_collections


    /**
     * Deletes collection from remote storage and refreshes cache.
     *
     * @param array $params Only collection_id is used.
     * @return bool|object
     */
    function delete_collection($params)
    {
        global $wp_reset_cloud;

        $params = shortcode_atts(array('collection_id' => 0), (array) $params);
        $params['collection_id'] = (int) $params['collection_id'];

        if (!$params['collection_id']) {
            return new WP_Error(1, 'Invalid collection id.');
        }
        if (!$this->get_collection_details($params['collection_id'])) {
            return new WP_Error(1, 'Unknown collection id.');
        }

        $res = $wp_reset_cloud->query_cloud_server(
            array(
                'cloud_action' => 'collection_delete',
                'collection_id' => $params['collection_id']
            )
        );

        if (is_wp_error($res)) {
            return new WP_Error(1, 'An error has occurred while deleting collection from the cloud. ' . $res->get_error_message());
        } elseif ($res['success'] == false) {
            return new WP_Error(1, 'An error has occurred while deleting collection from the cloud. ' . $res['data']);
        } else {
            $res['data'] = $this->clean_raw_collections($res['data']);
            set_transient('wp_reset_collections', $res['data'], DAY_IN_SECONDS);
            return true;
        }
    } // delete_collection


    /**
     * Deletes a single collection item from remote storage and refreshes cache.
     *
     * @param array $params Use collection_id and collection_item_id
     * @return bool|object
     */
    function delete_collection_item($params)
    {
        global $wp_reset_cloud;

        $collections = $this->get_collections_keyed();
        $params = shortcode_atts(array('collection_id' => 0, 'collection_item_id' => 0), (array) $params);
        $params['collection_id'] = (int) $params['collection_id'];
        $params['collection_item_id'] = (int) $params['collection_item_id'];
        $params['item_data'] = $collections[$params['collection_id']]['items'][$params['collection_item_id']];

        if (!$params['collection_id']) {
            return new WP_Error(1, 'Invalid collection id.');
        }
        if (!$this->get_collection_details($params['collection_id'])) {
            return new WP_Error(1, 'Unknown collection id.');
        }
        if (!$params['collection_item_id']) {
            return new WP_Error(1, 'Invalid collection item id.');
        }
        if (!$this->get_collection_item_details($params['collection_id'], $params['collection_item_id'])) {
            return new WP_Error(1, 'Unknown collection item id.');
        }

        if (!empty($params['item_data']['location'])) {
            $wp_reset_cloud->cloud_collection_item_delete($params['item_data']['location'], $params['collection_id'], $params['item_data']);
        }

        $res = $wp_reset_cloud->query_cloud_server(
            array(
                'cloud_action' => 'collection_item_remove',
                'collection_id' => $params['collection_id'],
                'collection_item_id' => $params['collection_item_id']
            )
        );

        if (is_wp_error($res)) {
            return new WP_Error(1, 'An error has occurred while deleting collection item from the cloud. ' . $res->get_error_message());
        } elseif ($res['success'] == false) {
            return new WP_Error(1, 'An error has occurred while deleting collection item from the cloud. ' . $res['data']);
        } else {
            $res['data'] = $this->clean_raw_collections($res['data']);
            set_transient('wp_reset_collections', $res['data'], DAY_IN_SECONDS);
            return true;
        }
    } // delete_collection_item


    /**
     * Adds empty collection to remote storage and refreshes cache.
     *
     * @param array $params Only name is used.
     * @return array|object
     */
    function add_new_collection($params)
    {
        global $wp_reset_cloud;

        $params = shortcode_atts(array('name' => ''), (array) $params);
        $params['name'] = substr(trim(strip_tags($params['name'])), 0, 255);

        $res = $wp_reset_cloud->query_cloud_server(
            array(
                'cloud_action' => 'collection_add',
                'collection_name' => $params['name']
            )
        );

        if (is_wp_error($res)) {
            return new WP_Error(1, 'An error has occurred while adding collection to the cloud. ' . $res->get_error_message());
        } elseif ($res['success'] == false) {
            return new WP_Error(1, 'An error has occurred while adding collection to the cloud. ' . $res['data']);
        } else {
            $res['data'] = $this->clean_raw_collections($res['data']);
            set_transient('wp_reset_collections', $res['data'], DAY_IN_SECONDS);
            return $res['data'][0];
        }
    } // add_new_collection


    /**
     * Edits collection name on remote storage and reloads cache.
     *
     * @param array Use collection_id and name.
     * @return string|object
     */
    function edit_collection_name($params)
    {
        global $wp_reset_cloud;

        $params = shortcode_atts(array('collection_name' => '', 'collection_id' => 0), (array) $params);
        $params['collection_id'] = (int) $params['collection_id'];
        $params['collection_name'] = substr(trim(strip_tags($params['collection_name'])), 0, 255);

        $res = $wp_reset_cloud->query_cloud_server(
            array(
                'cloud_action' => 'collection_edit',
                'collection_id' => $params['collection_id'],
                'collection_name' => $params['collection_name']
            )
        );

        if (is_wp_error($res)) {
            return new WP_Error(1, 'An error has occurred while saving collection to the cloud. ' . $res->get_error_message());
        } elseif ($res['success'] == false) {
            return new WP_Error(1, 'An error has occurred while saving collection to the cloud. ' . $res['data']);
        } else {
            $res['data'] = $this->clean_raw_collections($res['data']);
            set_transient('wp_reset_collections', $res['data'], DAY_IN_SECONDS);
            $tmp = $this->get_collection_details($params['collection_id']);

            return htmlspecialchars($tmp['name']);
        }
    } // edit_collection_name


    /**
     * Adds collection item to remote storage and refreshes cache.
     *
     * @param array $params Accepts: collection_id, type, source, slug, note
     *
     * @return array|object
     */
    function add_collection_item($params)
    {
        global $wp_reset, $wp_reset_cloud;

        $params = shortcode_atts(array('collection_id' => 0, 'type' => '', 'source' => 'repo', 'slug' => '', 'note' => '', 'license_key' => ''), (array) $params);

        if (!$params['collection_id']) {
            return new WP_Error(1, 'Invalid collection id.');
        }
        if (!$this->get_collection_details($params['collection_id'])) {
            return new WP_Error(1, 'Unknown collection id.');
        }

        $params['type'] = strtolower($params['type']);
        if ($params['type'] != 'theme') {
            $params['type'] = 'plugin';
        }

        $params['name'] = '';
        $params['version'] = '';

        if ($params['source'] == 'zip') {
            $temp_upload = $wp_reset->export_dir_path('temp_upload');
            $folder = wp_mkdir_p($temp_upload);

            if (!$folder) {
                return new WP_Error(1, 'Unable to create ' . $temp_upload . ' folder.');
            }

            $temp_zip_folder = $wp_reset->export_dir_path('temp_upload/' . str_replace('.zip', '', $_FILES['zip']['name']));
            if (is_wp_error($temp_upload)) {
                return new WP_Error(1, $temp_upload->get_error_message());
            }
            $temp_zip_file = $temp_upload . '/' . $_FILES['zip']['name'];

            move_uploaded_file($_FILES['zip']['tmp_name'], $temp_zip_file);

            $temp_zip = new ZipArchive();
            $temp_zip->open($temp_zip_file);
            $temp_zip->extractTo($temp_zip_folder);
            $temp_zip->close();

            $files = $wp_reset->scan_folder($temp_zip_folder);

            foreach ($files as $id => $file) {
                $relative_path = str_replace(trailingslashit(str_replace('\\', '/', $temp_zip_folder)), '', str_replace('\\', '/', $file));
                if (substr_count($relative_path, '/') > 1) {
                    unset($files[$id]);
                }
            }

            if ($params['type'] == 'plugin') {
                $plugin_info = false;
                if ($files) {
                    foreach ($files as $file) {
                        $file_info = pathinfo($file);
                        if (@$file_info['extension'] == 'php') {
                            $plugin_info = get_plugin_data($file, false, false);
                            if (!empty($plugin_info['Name'])) {
                                $params['slug'] = basename($file_info['dirname']);
                                $params['name'] = $plugin_info['Name'];
                                $params['version'] = $plugin_info['Version'];
                                break;
                            }
                        }
                    }
                }

                if ($plugin_info === false || empty($params['slug'])) {
                    return new WP_Error(1, 'The ZIP file does not contain a valid plugin. If you were trying to add a theme instead please toggle Type to Theme in the Add Item dialog.');
                }
            } else {
                $theme_info = false;
                if ($files) {
                    foreach ($files as $file) {
                        if (basename($file) == 'style.css') {
                            $theme_info = wp_get_theme(basename(dirname($file)), dirname(dirname($file)));
                            if (!empty($theme_info['Name'])) {
                                $params['slug'] = strtolower(basename(dirname($file)));
                                $params['name'] = $theme_info['Name'];
                                $params['version'] = $theme_info['Version'];
                                break;
                            }
                        }
                    }
                }

                if ($theme_info === false || empty($params['slug'])) {
                    return new WP_Error(1, 'The ZIP file does not contain a valid theme. If you were trying to add a plugin instead please toggle Type to Plugin in the Add Item dialog.');
                }
            }
        } else {
            $params['slug'] = substr(trim(strip_tags($params['slug'])), 0, 128);
        }

        $wp_reset->delete_folder($temp_zip_folder, basename($temp_zip_folder));

        $params['note'] = substr(trim(strip_tags($params['note'])), 0, 255);

        $res = $wp_reset_cloud->query_cloud_server(
            array(
                'cloud_action' => 'collection_item_add',
                'collection_id' => $params['collection_id'],
                'collection_item_type' => $params['type'],
                'collection_item_source' => $params['source'],
                'collection_item_name' => $params['name'],
                'collection_item_version' => $params['version'],
                'collection_item_note' => $params['note'],
                'collection_item_license_key' => $params['license_key'],
                'collection_item_slug' => $params['slug']
            )
        );

        if (is_wp_error($res)) {
            if ($params['source'] == 'zip') {
                unlink($temp_zip_file);
            }
            return new WP_Error(1, 'An error has occurred while adding collection item to the cloud. ' . $res->get_error_message());
        } elseif ($res['success'] == false) {
            if ($params['source'] == 'zip') {
                unlink($temp_zip_file);
            }
            return new WP_Error(1, 'An error has occurred while adding collection item to the cloud. ' . $res['data']);
        } else {
            if ($params['source'] == 'zip') {
                $cloud_save_result = $wp_reset_cloud->upload_collection_item($params['collection_id'], $res['data']['item_id'], $temp_zip_file);
                unlink($temp_zip_file);
                if (is_wp_error($cloud_save_result)) {
                    return new WP_Error(1, $cloud_save_result->get_error_message());
                }
            }

            $collections = $this->get_collections(true);
            set_transient('wp_reset_collections', $collections, DAY_IN_SECONDS);

            return $this->get_collection_item_details($params['collection_id'], $res['data']['item_id']);
        }
    } // add_collection_item


    /**
     * Edits collection item on remote storage and refreshes cache.
     *
     * @param array $params Accepts: collection_id, item_id, note
     *
     * @return array|object
     */
    function edit_collection_item($params)
    {
        global $wp_reset_cloud;

        $params = shortcode_atts(array('collection_id' => 0, 'item_id' => 0, 'note' => '', 'license_key' => ''), (array) $params);
        if (!$params['collection_id']) {
            return new WP_Error(1, 'Invalid collection id.');
        }
        if (!$this->get_collection_details($params['collection_id'])) {
            return new WP_Error(1, 'Unknown collection id.');
        }

        if (!$params['item_id']) {
            return new WP_Error(1, 'Invalid collection item id.');
        }
        if (!$this->get_collection_item_details($params['collection_id'], $params['item_id'])) {
            return new WP_Error(1, 'Unknown item id.');
        }

        $params['note'] = preg_replace('/(\r\n|\n|\r)/', '<br/>', substr(trim(strip_tags($params['note'], '<a><b><i><br><u><strong>')), 0, 255));
        $res = $wp_reset_cloud->query_cloud_server(
            array(
                'cloud_action' => 'collection_item_edit',
                'collection_id' => $params['collection_id'],
                'item_id' => $params['item_id'],
                'collection_item_note' => $params['note'],
                'collection_item_license_key' => $params['license_key']
            )
        );

        if (is_wp_error($res)) {
            return new WP_Error(1, 'An error has occurred while editing collection item on the cloud. ' . $res->get_error_message());
        } elseif ($res['success'] == false) {
            return new WP_Error(1, 'An error has occurred while editing collection item on the cloud. ' . $res['data']);
        } else {
            $collections = $this->clean_raw_collections($res['data']['collections']);
            set_transient('wp_reset_collections', $collections, DAY_IN_SECONDS);

            return $this->get_collection_item_details($params['collection_id'], $params['item_id']);
        }
    } // edit_collection_item


    /**
     * Get collection details based on id, or false if no collection found.
     *
     * @param int $collection_id
     * @return array|bool
     */
    function get_collection_details($collection_id)
    {
        $collection_id = (int) $collection_id;
        $collections = $this->get_collections();

        if (!$collection_id) {
            return false;
        }

        foreach ($collections as $collection) {
            if ((int) $collection['id'] == $collection_id) {
                return $collection;
            }
        } // foreach

        return false;
    } // get_collection_details


    /**
     * Get collection details based on id, or false if no collection found.
     *
     * @param int $collection_id
     * @param int $collection_item_id
     * @return array|bool
     */
    function get_collection_item_details($collection_id, $collection_item_id)
    {
        $collection_id = (int) $collection_id;
        $collection_item_id = (int) $collection_item_id;
        $collection = $this->get_collection_details($collection_id);

        if (false == $collection) {
            return false;
        }

        if (!$collection_item_id) {
            return false;
        }

        foreach ($collection['items'] as $item) {
            if ((int) $item['id'] == $collection_item_id) {
                return $item;
            }
        }

        return false;
    } // get_collection_item_details

    /**
     * Automatic license activation for premium plugins
     *
     * @return array
     */
    function license_activation($slug, $license_key)
    {
        if (array_key_exists($slug, $this->license_activation_known_plugins)) {
            switch ($slug) {
                case 'under-construction-page':
                    if (class_exists('UCP')) {
                        $_GET['license_key'] = $license_key;
                        if (UCP::validate_license_ajax($license_key)) {
                            return 'license_active';
                        }
                    }
                    break;
                case 'minimal-coming-soon-maintenance-mode':
                    if (class_exists('WF_Licensing_CSMM')) {
                        global $csmm_lc;
                        if ($csmm_lc->validate($license_key)) {
                            wp_send_json_success();
                        } else {
                            wp_send_json_error();
                        }
                    }
                    break;
                case '301-redirects':
                    if (class_exists('WF_Licensing_301')) {
                        global $wf_301_licensing;
                        if ($wf_301_licensing->validate($license_key)) {
                            wp_send_json_success();
                        } else {
                            wp_send_json_error();
                        }
                    }
                    break;
                case 'google-maps-widget':
                    if (class_exists('GMWP')) {
                        $tmp = GMWP::validate_activation_code($license_key);
                        $new_values['activation_code'] = $license_key;
                        if ($tmp['success']) {
                            $new_values['license_type'] = $tmp['license_type'];
                            $new_values['license_expires'] = $tmp['license_expires'];
                            $new_values['license_active'] = $tmp['license_active'];
                            GMWP::set_options($new_values);
                            if ($tmp['license_active']) {
                                add_settings_error(GMWP::$options, 'license_key', __('License key saved and activated!', 'google-maps-widget'), 'updated');
                                return true;
                            } else {
                                add_settings_error(GMWP::$options, 'license_key', 'License not active. ' . $tmp['error'], 'error');
                            }
                        } else {
                            add_settings_error(GMWP::$options, 'license_key', 'Unable to contact licensing server. Please try again in a few moments.', 'error');
                        }
                    }
                    break;
                case 'elementor-pro':
                    update_option('elementor_pro_license_key', $license_key);
                    break;
                case 'ninja-tables-pro':
                    update_option('_ninjatables_pro_license_key', $license_key);
                    update_option('_ninjatables_pro_license_status', 'valid');
                    break;
                case 'wp-seopress-pro':
                    update_option('seopress_pro_license_key', $license_key);
                    update_option('seopress_pro_activated', 'yes');
                    update_option('seopress_pro_license_status', 'valid');

                    break;
                case 'astra-addon':
                    if (class_exists('BSF_License_Manager')) {
                        $astra_license_manager = BSF_License_Manager::instance();
                        $_POST['bsf_activate_license'] = true;
                        $_POST['bsf_license_manager']['license_key'] = $license_key;
                        $_POST['bsf_license_manager']['product_id'] = 'astra-addon';
                        $astra_license_manager->bsf_activate_license();
                    }
                    break;
                case 'advanced-custom-fields-pro':
                    if (class_exists('ACF_Admin_Updates')) {
                        global $acf_instances;
                        $_POST['acf_pro_licence'] = $license_key;
                        $acf_instances['ACF_Admin_Updates']->activate_pro_licence();
                    }
                    break;
                case 'oxygen':
                    if (class_exists('OxygenMainPluginUpdater')) {
                        //update_option('oxygen_license_key', $license_key);
                        //update_option('oxygen_license_status', 'valid');
                        $_POST['oxygen_license_key'] = $license_key;
                        $_POST['oxygen_submit_license'] = true;
                        $_POST['oxygen_license_nonce_field'] = wp_create_nonce('oxygen_submit_license');
                        $oxygen_updater = new OxygenMainPluginUpdater(array(
                            "prefix"         => "oxygen_",
                            "plugin_name"     => "Oxygen",
                            "priority"         => 5
                        ));
                        $oxygen_updater->activate_license();
                    }
                    break;
                case 'oxygen-woocommerce':
                    if (class_exists('OxygenWooCommercePluginUpdater')) {
                        $_POST['oxygen_woocommerce_license_key'] = $license_key;
                        $_POST['oxygen_woocommerce_submit_license'] = true;
                        $_POST['oxygen_woocommerce_license_nonce_field'] = wp_create_nonce('oxygen_woocommerce_submit_license');
                        $oxygen_updater = new OxygenWooCommercePluginUpdater(array(
                            "prefix"         => "oxygen_woocommerce_",
                            "plugin_name"     => "Oxygen WooCommerce Integration",
                            "priority"         => 25
                        ));
                        $oxygen_updater->activate_license();
                    }
                    break;
                case 'oxygen-gutenberg':
                    if (class_exists('OxygenGutenbergPluginUpdater')) {
                        $_POST['oxygen_gutenberg_license_key'] = $license_key;
                        $_POST['oxygen_gutenberg_submit_license'] = true;
                        $_POST['oxygen_gutenberg_license_nonce_field'] = wp_create_nonce('oxygen_gutenberg_submit_license');
                        $oxygen_updater = new OxygenGutenbergPluginUpdater(array(
                            "prefix"         => "oxygen_gutenberg_",
                            "plugin_name"     => "Oxygen Gutenberg Integration",
                            "priority"         => 25
                        ));
                        $oxygen_updater->activate_license();
                    }
                    break;
            }
        }
        if (class_exists('Freemius')) {
            $accounts = get_option('fs_accounts');
            $freemius_data = false;
            foreach ($accounts['plugins'] as $plugin) {
                if ($plugin->premium_slug == $slug || $plugin->slug == $slug) {
                    $freemius_data = (array)$plugin;
                }
            }

            if ($freemius_data !== false) {
                define('WP_FS__SKIP_EMAIL_ACTIVATION', true);
                $freemius = fs_dynamic_init($freemius_data);
                $freemius->activate_migrated_license($license_key, false);
                return true;
            }
        }

        return false;
    }

    /**
     * Automatic license activation for premium plugins
     *
     * @return array
     */
    function check_license_activation($slug)
    {
        if (array_key_exists($slug, $this->license_activation_known_plugins)) {
            switch ($slug) {
                case 'under-construction-page':
                    if (class_exists('UCP')) {
                        if (UCP::is_activated()) {
                            return 'license_active';
                        }
                    }
                    break;
                case 'minimal-coming-soon-maintenance-mode':
                    global $csmm_lc;
                    if ($csmm_lc->is_active()) {
                        return 'license_active';
                    }
                    break;
                case '301-redirects':
                    if (class_exists('WF_Licensing_301')) {
                        global $wf_301_licensing;
                        if ($wf_301_licensing->is_active()) {
                            return 'license_active';
                        }
                    }
                    break;
                case 'google-maps-widget':
                    if (class_exists('GMWP')) {
                        if (GMWP::is_activated()) {
                            return 'license_active';
                        }
                    }
                    break;
                case 'elementor-pro':
                    return 'license_active';
                    break;
                case 'ninja-tables-pro':
                    if (get_option('_ninjatables_pro_license_status') == 'valid') {
                        return 'license_active';
                    }
                    break;
                case 'wp-seopress-pro':
                    if (get_option('seopress_pro_activated') == 'yes') {
                        return 'license_active';
                    }
                    break;
                case 'astra-addon':
                    if (class_exists('BSF_License_Manager')) {
                        $astra_license_manager = BSF_License_Manager::instance();
                        if ($astra_license_manager->bsf_is_active_license('astra-addon')) {
                            return 'license_active';
                        }
                    }
                    break;
                case 'advanced-custom-fields-pro':
                    if (function_exists('acf_pro_get_license') && acf_pro_get_license() !== false) {
                        return 'license_active';
                    }
                    break;
                case 'oxygen':
                    $oxygen_license_key = get_option('oxygen_license_key');
                    if (false !== $oxygen_license_key) {
                        return 'license_active';
                    }
                    break;
                case 'oxygen-woocommerce':
                    $oxygen_woocommerce_license_key = get_option('oxygen_woocommerce_license_key');
                    if (false !== $oxygen_woocommerce_license_key) {
                        return 'license_active';
                    }
                    break;
                case 'oxygen-gutenberg':
                    $oxygen_gutenberg_license_key = get_option('oxygen_gutenberg_license_key');
                    if (false !== $oxygen_gutenberg_license_key) {
                        return 'license_active';
                    }
                    break;
            }
        }

        if (class_exists('Freemius')) {
            $accounts = get_option('fs_accounts');
            $freemius_data = false;
            foreach ($accounts['plugins'] as $plugin) {
                if ($plugin->premium_slug == $slug || $plugin->slug == $slug) {
                    $freemius_data = (array)$plugin;
                }
            }

            if ($freemius_data !== false) {
                $freemius = fs_dynamic_init($freemius_data);
                if ($freemius->can_use_premium_code()) {
                    return 'license_active';
                } else {
                    return false;
                }
            }
        }

        return false;
    }

    /**
     * Reloads collection from remote storage and refreshes cache.
     *
     * @return array
     */
    function reload_collections()
    {
        $this->clear_cache();
        $tmp = $this->get_collections(true);

        return $tmp;
    } // reload_collections
} // WP_Reset_Collections


global $wp_reset_collections;
$wp_reset_collections = new WP_Reset_Collections();
