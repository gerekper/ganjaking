<?php

namespace MailOptin\Core\Admin\SettingsPage;

use MailOptin\Core\Core;
use MailOptin\Core\EmailCampaigns\NewPublishPost\NewPublishPost;
use MailOptin\Core\EmailCampaigns\Newsletter\Newsletter as NL;
use MailOptin\Core\EmailCampaigns\PostsEmailDigest\PostsEmailDigest;
use MailOptin\Core\Repositories\EmailCampaignRepository as ER;
use MailOptin\Core\Repositories\EmailCampaignRepository;

if ( ! class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Campaign_Log_List extends \WP_List_Table
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
        $this->table = $this->wpdb->prefix . Core::campaign_log_table_name;
        parent::__construct(array(
            'singular' => __('campaign_log', 'mailoptin'), //singular name of the listed records
            'plural'   => __('campaign_logs', 'mailoptin'), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ));
    }

    /**
     * Retrieve campaign log data from the database
     *
     * @param int $per_page
     * @param int $current_page
     *
     * @return mixed
     */
    public function get_campaign_log($per_page, $current_page = 1)
    {
        $replacement = [$per_page];
        $offset      = ($current_page - 1) * $per_page;
        $sql         = "SELECT * FROM {$this->table}";
        $sql         .= " ORDER BY id DESC";
        $sql         .= " LIMIT %d";
        if ($current_page > 1) {
            $sql           .= "  OFFSET %d";
            $replacement[] = $offset;
        }

        $result = $this->wpdb->get_results(
            $this->wpdb->prepare($sql, $replacement), 'ARRAY_A'
        );

        return $result;
    }

    /**
     * Resend failed email campaign.
     *
     * @param int $campaign_log_id
     */
    public function resend_email_campaign($campaign_log_id)
    {
        $email_campaign_id = $this->get_email_campaign_id_from_campaign_log($campaign_log_id);
        $campaign_type     = ER::get_email_campaign_type($email_campaign_id);

        if ($campaign_type == ER::NEW_PUBLISH_POST) {
            NewPublishPost::get_instance()->send_campaign($email_campaign_id, $campaign_log_id);
        }

        if ($campaign_type == ER::POSTS_EMAIL_DIGEST) {
            PostsEmailDigest::get_instance()->send_campaign($email_campaign_id, $campaign_log_id);
        }

        if ($campaign_type == ER::NEWSLETTER) {
            NL::get_instance()->send_campaign($email_campaign_id, $campaign_log_id);
        }

        wp_safe_redirect(add_query_arg('failed-campaign', 'retried', MAILOPTIN_CAMPAIGN_LOG_SETTINGS_PAGE));
        exit;
    }

    /**
     * Delete a campaign log record.
     *
     * @param int $id campaign_log ID
     *
     * @return false|int
     */
    public function delete_a_campaign_log($id)
    {
        return $this->wpdb->delete(
            $this->table,
            array('id' => $id),
            array('%d')
        );
    }

    /**
     * Get email campaign ID from campaign log ID
     *
     * @param int $campaign_log_id
     *
     * @return null|int
     */
    public function get_email_campaign_id_from_campaign_log($campaign_log_id)
    {
        $result = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT email_campaign_id FROM $this->table WHERE id = %d",
                $campaign_log_id
            )
        );

        // cast to integer if result isn't null.
        return ! is_null($result) ? absint($result) : $result;
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public function record_count()
    {
        $sql = "SELECT COUNT(*) FROM $this->table";

        return $this->wpdb->get_var($sql);
    }

    /** Text displayed when no campaign log is available */
    public function no_items()
    {
        _e('No email campaign has been sent yet.', 'mailoptin');
    }


    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function get_columns()
    {
        $columns = array(
            'cb'             => '<input type="checkbox" />',
            'subject'        => __('Subject', 'mailoptin'),
            'content_html'   => __('HTML', 'mailoptin'),
            'content_text'   => __('Plain Text', 'mailoptin'),
            'status'         => __('Status', 'mailoptin'),
            'email_campaign' => __('Campaign', 'mailoptin'),
            'date_time'      => __('Date & Time', 'mailoptin'),
        );

        return $columns;
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
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    /**
     * Column for subject
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_subject($item)
    {
        $name            = '<strong>' . $item['title'] . '</strong>';
        $campaign_log_id = absint($item['id']);

        $delete_href = add_query_arg(
            [
                'action'          => 'delete',
                'campaign-log-id' => $campaign_log_id,
                '_wpnonce'        => wp_create_nonce('mo_delete_campaign_log')
            ],
            MAILOPTIN_CAMPAIGN_LOG_SETTINGS_PAGE
        );

        $actions = [
            'delete' => sprintf(
                '<a class="mo-delete-prompt" href="%s">%s</a>',
                $delete_href, __('Delete', 'mailoptin')
            ),
        ];

        $resend_href = add_query_arg(
            [
                'action'          => 'resend',
                'campaign-log-id' => $campaign_log_id,
                '_wpnonce'        => wp_create_nonce('mo_resend_failed_campaign')
            ],
            MAILOPTIN_CAMPAIGN_LOG_SETTINGS_PAGE
        );

        $actions['resend'] = sprintf(
            '<a href="%s">%s</a>',
            $resend_href,
            'resend',
            __('Resend', 'mailoptin')
        );

        return $name . $this->row_actions($actions);
    }

    /**
     * Column for HTML preview
     *
     * @param array $item
     *
     * @return mixed
     */
    public function column_content_html($item)
    {
        $campaign_id = intval($item['id']);
        $preview_url = add_query_arg(
            ['mailoptin' => 'preview-campaign', 'type' => 'html', 'id' => $campaign_id],
            home_url()
        );

        return "<a class=\"mo-open-link-fancybox\" target='_blank' href=\"$preview_url\"><span class=\"dashicons dashicons-visibility\"></span></a>";
    }

    /**
     * Column for plain text preview
     *
     * @param array $item
     *
     * @return mixed
     */
    public function column_content_text($item)
    {
        $campaign_id = intval($item['id']);
        $preview_url = add_query_arg(
            ['mailoptin' => 'preview-campaign', 'type' => 'text', 'id' => $campaign_id],
            home_url()
        );

        return "<a target='_blank' class=\"mo-open-link-fancybox\" href=\"$preview_url\"><span class=\"dashicons dashicons-visibility\"></span></a>";
    }

    /**
     * Column for email campaign
     *
     * @param array $item
     *
     * @return mixed
     */
    public function column_email_campaign($item)
    {
        $email_campaign_id    = absint($item['email_campaign_id']);
        $email_campaign_title = EmailCampaignRepository::get_email_campaign_name($email_campaign_id);

        $customize_url = Email_Campaign_List::_campaign_customize_url($email_campaign_id);

        return "<a href=\"$customize_url\">$email_campaign_title</span></a>";
    }

    /**
     * Column for campaign status
     *
     * @param array $item
     *
     * @return mixed
     */
    public function column_status($item)
    {
        $campaign_log_id      = absint($item['id']);
        $email_campaign_id    = absint($item['email_campaign_id']);
        $email_campaign_title = EmailCampaignRepository::get_email_campaign_name($email_campaign_id);
        $error_log_filename   = md5($email_campaign_title . $campaign_log_id);

        $preview_url = add_query_arg(
            ['mailoptin' => 'preview-campaign-error-log', 'id' => $error_log_filename],
            home_url()
        );

        $log_file = MAILOPTIN_CAMPAIGN_ERROR_LOG . "{$error_log_filename}.log";

        $err_log = '';
        // only display the link to error log if error content isn't empty.
        if (file_exists($log_file) && file_get_contents($log_file) != '') {
            $err_log .= "<div><a target='_blank' class=\"mo-open-link-fancybox\" href=\"$preview_url\">" . __('Error logs', 'mailoptin') . "</span></a></div>";
        }

        return $item['status'] . $err_log;
    }

    /**
     * Column for campaign status
     *
     * @param array $item
     *
     * @return mixed
     */
    public function column_date_time($item)
    {
        return $item['status_time'];
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
        $this->_column_headers = $this->get_column_info();
        /** Process bulk action */
        $this->process_bulk_action();
        $per_page     = $this->get_items_per_page('campaign_log_per_page', 10);
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();
        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ));

        $this->items = $this->get_campaign_log($per_page, $current_page);
    }


    /**
     * Process bulk action.
     */
    public function process_bulk_action()
    {
        // bail if user is not an admin or without admin privileges.
        if ( ! \MailOptin\Core\current_user_has_privilege()) return;

        if ('delete' === $this->current_action()) {
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);
            if ( ! wp_verify_nonce($nonce, 'mo_delete_campaign_log')) {
                wp_nonce_ays('mo_delete_campaign_log');
            } else {
                self::delete_a_campaign_log(absint($_GET['campaign-log-id']));
                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url
                wp_safe_redirect(MAILOPTIN_CAMPAIGN_LOG_SETTINGS_PAGE);
                exit;
            }
        }

        if ('resend' === $this->current_action()) {
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);
            if ( ! wp_verify_nonce($nonce, 'mo_resend_failed_campaign')) {
                wp_nonce_ays('mo_resend_failed_campaign');
            } else {
                self::resend_email_campaign(absint($_GET['campaign-log-id']));
                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url
                wp_safe_redirect(esc_url_raw(MAILOPTIN_CAMPAIGN_LOG_SETTINGS_PAGE));
                exit;
            }
        }

        /**
         * @see WP_List_Table::display_tablenav()
         */
        if ('bulk-delete' === $this->current_action()) {
            check_admin_referer('bulk-campaign_logs');
            $delete_ids = array_map('absint', $_POST['bulk-delete']);
            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                self::delete_a_campaign_log($id);
            }
            wp_safe_redirect(esc_url_raw(add_query_arg()));
            exit;
        }
    }

    /**
     * @return Campaign_Log_List
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