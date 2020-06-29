<?php

namespace MailOptin\Core\Admin\SettingsPage;

use MailOptin\Core\Core;
use MailOptin\Core\Repositories\EmailCampaignMeta;
use MailOptin\Core\Repositories\EmailCampaignRepository as ER;

if ( ! class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Email_Campaign_List extends \WP_List_Table
{
    private $table;

    /** @var \wpdb */
    private $wpdb;

    /**
     * Class constructor
     */
    public function __construct($wpdb)
    {
        $this->wpdb  = $wpdb;
        $this->table = $this->wpdb->prefix . Core::email_campaigns_table_name;
        parent::__construct(array(
                'singular' => __('email_campaign', 'mailoptin'), //singular name of the listed records
                'plural'   => __('email_campaigns', 'mailoptin'), //plural name of the listed records
                'ajax'     => false //does this table support ajax?
            )
        );
    }

    /**
     * Retrieve campaigns data from the database
     *
     * @param int $per_page
     * @param int $current_page
     * @param string $campaign_type
     *
     * @return mixed
     */
    public function get_email_campaigns($per_page, $current_page = 1, $campaign_type = '')
    {
        $offset = ($current_page - 1) * $per_page;
        $sql    = "SELECT * FROM {$this->table}";
        if ( ! empty($campaign_type)) {
            $campaign_type = esc_sql($campaign_type);
            $sql           .= "  WHERE campaign_type = '$campaign_type'";
        } else {
            $sql .= sprintf("  WHERE campaign_type IN ('%s', '%s')",
                ER::NEW_PUBLISH_POST,
                ER::POSTS_EMAIL_DIGEST
            );
        }

        $sql .= "  ORDER BY id DESC";

        $sql .= " LIMIT $per_page";
        if ($current_page > 1) {
            $sql .= "  OFFSET $offset";
        }

        $result = $this->wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }


    /**
     * Delete a campaign record.
     *
     * @param int $email_campaign_id campaign ID
     */
    public function delete_email_campaign($email_campaign_id)
    {
        ER::delete_campaign_by_id($email_campaign_id);

        // remove the campaign meta data.
        ER::delete_settings_by_id($email_campaign_id);

        EmailCampaignMeta::delete_all_meta_data($email_campaign_id);
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public function record_count($campaign_type)
    {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM $this->table";
        if ( ! empty($campaign_type)) {
            $campaign_type = esc_sql($campaign_type);
            $sql           .= "  WHERE campaign_type = '$campaign_type'";
        } else {
            $campaign_type = esc_sql($campaign_type);
            $sql           .= sprintf("  WHERE campaign_type IN ('%s', '%s')",
                ER::NEW_PUBLISH_POST,
                ER::POSTS_EMAIL_DIGEST
            );
        }

        return $wpdb->get_var($sql);
    }

    /**
     * Generate URL to delete email campaign.
     *
     * @param int $item_id
     *
     * @return string
     */
    public static function _campaign_delete_url($item_id)
    {
        $delete_nonce = wp_create_nonce('mailoptin_delete_email_campaign');

        return add_query_arg(
            [
                'action'            => 'delete',
                'email-campaign-id' => absint($item_id),
                '_wpnonce'          => $delete_nonce
            ],
            MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_PAGE
        );
    }

    /**
     * Generate URL to clone email campaign.
     *
     * @param int $item_id
     *
     * @return string
     */
    public static function _campaign_clone_url($item_id)
    {
        $clone_nonce = wp_create_nonce('mailoptin_clone_email_campaign');

        return add_query_arg(
            [
                'action'            => 'clone',
                'email-campaign-id' => absint($item_id),
                '_wpnonce'          => $clone_nonce
            ],
            MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_PAGE
        );
    }

    /**
     * Generate URL to customize email campaign.
     *
     * @param int $item_id
     *
     * @return string
     */
    public static function _campaign_customize_url($item_id)
    {
        return add_query_arg(
            apply_filters('mo_email_campaign_customize_url', array(
                    'mailoptin_email_campaign_id' => $item_id,
                )
            ),
            admin_url('customize.php')
        );
    }

    /** Text displayed when no email campaign is available */
    public function no_items()
    {
        printf(
            __('No email automation is currently available. %sConsider creating one%s', 'mailoptin'),
            '<a href="' . add_query_arg('view', 'add-new-email-automation', MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_PAGE) . '">',
            '</a>'
        );
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="email_campaign_ids[]" value="%s" />', $item['id']
        );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_name($item)
    {
        $email_campaign_id = absint($item['id']);

        $customize_url = self::_campaign_customize_url($email_campaign_id);

        $delete_url = self::_campaign_delete_url($email_campaign_id);
        $name       = "<strong><a href=\"$customize_url\">" . $item['name'] . '</a></strong>';

        $actions = array(
            'delete' => sprintf("<a href=\"$delete_url\">%s</a>", __('Delete', 'mailoptin')),
        );

        return $name . $this->row_actions($actions);
    }

    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     *
     * @return mixed
     */
    public function column_action($item)
    {
        $email_campaign_id = absint($item['id']);

        $delete_url    = self::_campaign_delete_url($email_campaign_id);
        $clone_url     = self::_campaign_clone_url($email_campaign_id);
        $customize_url = self::_campaign_customize_url($email_campaign_id);

        $action = sprintf(
            '<a class="mo-tooltipster button action mailoptin-btn-blue" href="%s" title="%s">%s</a> &nbsp;',
            esc_url_raw($customize_url),
            __('Customize', 'mailoptin'),
            '<span class="dashicons dashicons-edit mo-action-icon"></span>'
        );
        $action .= sprintf(
            '<a class="mo-tooltipster button action" href="%s" title="%s">%s</a> &nbsp;',
            $clone_url,
            __('Clone', 'mailoptin'),
            '<span class="dashicons dashicons-admin-page mo-action-icon"></span>'
        );
        $action .= sprintf(
            '<a class="mo-tooltipster button action mailoptin-btn-red mo-delete-prompt" href="%s" title="%s">%s</a> &nbsp;',
            $delete_url,
            __('Delete', 'mailoptin'),
            '<span class="dashicons dashicons-trash mo-action-icon"></span>'
        );

        return $action;
    }

    /**
     * Method for activated column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    public function column_activated($item)
    {
        $email_campaign_id = absint($item['id']);

        $input_value = ER::is_campaign_active($email_campaign_id) ? 'yes' : 'no';
        $checked     = ($input_value == 'yes') ? 'checked="checked"' : null;

        $switch = sprintf(
            '<input data-mo-automation-id="%1$s" id="mo-automation-activate-switch-%1$s" type="checkbox" class="mo-automation-activate-switch tgl tgl-light" value="%%3$s" %3$s />',
            $email_campaign_id,
            $input_value,
            $checked
        );

        $switch .= sprintf(
            '<label for="mo-automation-activate-switch-%1$s" style="margin:auto;" class="tgl-btn"></label>',
            $email_campaign_id
        );

        return $switch;
    }

    /**
     * Display campaign type data and any other data.
     *
     * @param object $item
     * @param string $column_name
     *
     * @return string
     */
    public function column_default($item, $column_name)
    {
        return ER::get_type_name(
            sanitize_text_field($item[$column_name])
        );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function get_columns()
    {
        $columns = array(
            'cb'            => '<input type="checkbox">',
            'name'          => __('Name', 'mailoptin'),
            'campaign_type' => __('Automation Type', 'mailoptin'),
            'action'        => __('Actions', 'mailoptin'),
            'activated'     => __('Activated', 'mailoptin'),
        );

        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'name' => array('name', true),
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions()
    {
        $actions = array(
            'bulk-delete' => __('Delete', 'mailoptin'),
        );

        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items()
    {
        /** Process bulk action */
        $this->process_actions();

        $campaign_type = isset($_GET['page']) && $_GET['page'] == MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_SLUG && ! empty($_GET['type']) ? sanitize_text_field($_GET['type']) : '';

        if (isset($_POST['mo_email_automations_filter'])) {
            check_admin_referer('wp-csa-nonce', 'wp_csa_nonce');
            $campaign_type = sanitize_text_field($_POST['mo_email_automations_filter']);
        }

        $this->_column_headers = $this->get_column_info();
        $per_page              = defined('MAILOPTIN_DETACH_LIBSODIUM') ? $this->get_items_per_page('email_campaign_per_page', 15) : 1;
        $current_page          = $this->get_pagenum();
        $total_items           = defined('MAILOPTIN_DETACH_LIBSODIUM') ? $this->record_count($campaign_type) : 1;
        $this->set_pagination_args(array(
                'total_items' => $total_items, //WE have to calculate the total number of items
                'per_page'    => $per_page //WE have to determine how many items to show on a page
            )
        );

        $this->items = $this->get_email_campaigns($per_page, $current_page, $campaign_type);
    }

    public function extra_tablenav($which)
    {
        if ($which != 'top') return;
        $selected_option = isset($_POST['mo_email_automations_filter']) ? sanitize_text_field($_POST['mo_email_automations_filter']) : '';
        ?>
        <div class="alignleft actions bulkactions" style="line-height: 2em;font-size: 15px">
            <strong style="font-weight:500;"><?php _e('Filter By:', 'mailoptin'); ?></strong>
        </div>
        <div class="alignleft actions bulkactions">
            <label for="mo-listing-filter" class="screen-reader-text">
                <?php _e('Filter email automation by', 'mailoptin'); ?>
            </label>
            <select name="mo_email_automations_filter" id="mo-listing-filter">
                <option value="">
                    <?php _e('Select...', 'mailoptin'); ?>
                </option>
                <option value="<?= ER::NEW_PUBLISH_POST; ?>" <?php selected(ER::NEW_PUBLISH_POST, $selected_option); ?>>
                    <?= ER::get_type_name(ER::NEW_PUBLISH_POST) ?>
                </option>
                <option value="<?= ER::POSTS_EMAIL_DIGEST; ?>" <?php selected(ER::POSTS_EMAIL_DIGEST, $selected_option); ?>>
                    <?= ER::get_type_name(ER::POSTS_EMAIL_DIGEST) ?>
                </option>
            </select>
            <input type="submit" class="button action" value="<?php _e('Apply', 'mailoptin'); ?>">
        </div>
        <?php
    }

    public function process_actions($email_type = '')
    {
        // bail if user is not an admin or without admin privileges.
        if ( ! \MailOptin\Core\current_user_has_privilege()) {
            return;
        }

        $redirect_url = MAILOPTIN_EMAIL_CAMPAIGNS_SETTINGS_PAGE;

        $email_campaign_id   = @absint($_GET['email-campaign-id']);
        $email_campaign_type = ER::get_email_campaign_type($email_campaign_id);

        if ($email_campaign_type == ER::NEWSLETTER) {
            $redirect_url = add_query_arg('view', MAILOPTIN_EMAIL_NEWSLETTERS_SETTINGS_SLUG, $redirect_url);
        }

        if ('delete' === $this->current_action()) {
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);
            if ( ! wp_verify_nonce($nonce, 'mailoptin_delete_email_campaign')) {
                wp_nonce_ays('mailoptin_delete_email_campaign');
            } else {
                self::delete_email_campaign($email_campaign_id);
                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url
                wp_redirect(esc_url_raw($redirect_url));
                exit;
            }
        }

        if ('clone' === $this->current_action()) {
            if (apply_filters('mailoptin_add_new_email_campaign_limit', true) && ER::campaign_count() >= 1) return;

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);
            if ( ! wp_verify_nonce($nonce, 'mailoptin_clone_email_campaign')) {
                wp_nonce_ays('mailoptin_clone_email_campaign');
            } else {
                (new CloneEmailCampaign($email_campaign_id))->forge();
                wp_redirect(esc_url_raw($redirect_url));
                exit;
            }
        }

        if ('bulk-delete' === $this->current_action()) {
            $action = 'email_campaigns';
            if ($email_type == ER::NEWSLETTER) {
                $action = 'newsletters';
            }
            check_admin_referer('bulk-' . $action);
            $delete_ids = array_map('absint', $_POST['email_campaign_ids']);
            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                self::delete_email_campaign($id);
            }
            wp_redirect(esc_url_raw(add_query_arg()));
            exit;
        }
    }

    /**
     * @return Email_Campaign_List
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self($GLOBALS['wpdb']);
        }

        return $instance;
    }
}