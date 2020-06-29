<?php

namespace MailOptin\Core\Admin\SettingsPage;

use MailOptin\Core\Core;
use MailOptin\Core\Repositories\EmailCampaignMeta;
use MailOptin\Core\Repositories\EmailCampaignRepository as ER;

if ( ! class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Newsletter_List extends \WP_List_Table
{
    private $table;

    /** @var \wpdb */
    private $wpdb;

    public function __construct($wpdb)
    {
        $this->wpdb  = $wpdb;
        $this->table = $this->wpdb->prefix . Core::email_campaigns_table_name;
        parent::__construct(array(
            'singular' => __('newsletter', 'mailoptin'), //singular name of the listed records
            'plural'   => __('newsletters', 'mailoptin'), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ));
    }

    public function no_items()
    {
        _e('No newsletter has been sent yet.', 'mailoptin');
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'name'      => __('Name', 'mailoptin'),
            'action'    => __('Actions', 'mailoptin'),
            'date_sent' => __('Date Sent', 'mailoptin'),
        );

        return $columns;
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'name' => array('name', true),
        );

        return $sortable_columns;
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
        return Email_Campaign_List::get_instance()->column_cb($item);
    }

    function column_name($item)
    {
        $email_campaign_id = absint($item['id']);

        $customize_url = Email_Campaign_List::_campaign_customize_url($email_campaign_id);

        $delete_url = Email_Campaign_List::_campaign_delete_url($email_campaign_id);
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

        $delete_url    = Email_Campaign_List::_campaign_delete_url($email_campaign_id);
        $clone_url     = Email_Campaign_List::_campaign_clone_url($email_campaign_id);
        $customize_url = Email_Campaign_List::_campaign_customize_url($email_campaign_id);

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

    public function column_date_sent($item)
    {
        $email_campaign_id = absint($item['id']);

        $date_sent = EmailCampaignMeta::get_meta_data($email_campaign_id, 'newsletter_date_sent');

        if (empty($date_sent) || $date_sent == ER::NEWSLETTER_STATUS_DRAFT) {
            $date_sent = __('Draft', 'mailoptin');
        }

        if ( ! empty($date_sent) && $date_sent == ER::NEWSLETTER_STATUS_FAILED) {
            $date_sent = __('Failed', 'mailoptin');
        }

        return $date_sent;
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
        Email_Campaign_List::get_instance()->process_actions(ER::NEWSLETTER);

        $this->_column_headers = $this->get_column_info();
        $per_page              = $this->get_items_per_page('newsletters_per_page', 15);
        $current_page          = $this->get_pagenum();
        $total_items           = Email_Campaign_List::get_instance()->record_count(ER::NEWSLETTER);
        $this->set_pagination_args(array(
                'total_items' => $total_items, //WE have to calculate the total number of items
                'per_page'    => $per_page //WE have to determine how many items to show on a page
            )
        );

        $this->items = Email_Campaign_List::get_instance()->get_email_campaigns($per_page, $current_page, ER::NEWSLETTER);
    }


    /**
     * @return self
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