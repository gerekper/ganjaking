<?php

namespace MailOptin\Core\Logging;

use MailOptin\Core\Core;

class CampaignLogPersistence implements PersistenceInterface
{
    /** @var \wpdb instance */
    private $wpdb;

    /** @var string campaign database table name */
    private $table;

    public function __construct($wpdb)
    {
        $this->wpdb = $wpdb;

        $this->table = $wpdb->prefix . Core::campaign_log_table_name;
    }

    /**
     * Persist campaign to database.
     *
     * @param CampaignLogInterface $campaign
     *
     * @return int
     */
    public function persist(CampaignLogInterface $campaign)
    {
        $this->wpdb->insert(
            $this->table,
            array(
                'email_campaign_id' => $campaign->email_campaign_id(),
                'title' => $campaign->title(),
                'content_html' => $campaign->content_html(),
                'content_text' => $campaign->content_text(),
                'status_time' => current_time('mysql'),
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
            )
        );

        return $this->wpdb->insert_id;
    }

    /**
     * Retrieve a campaign by ID
     *
     * @param int $id
     *
     * @return array|mixed
     */
    public function retrieve($id)
    {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM $this->table WHERE id = %d",
                $id
            )
        );
    }

    public function retrieveAll()
    {
        return $this->wpdb->get_results("SELECT * FROM $this->table");
    }

    /**
     * Update a column in database table.
     *
     * @param int $id
     * @param string $column
     * @param string $value
     *
     * @return false|int
     */
    public function updateColumn($id, $column, $value)
    {
        return $this->wpdb->update(
            $this->table,
            array(
                $column => $value,
            ),
            array('id' => $id),
            array(
                '%s',
            ),
            array('%d')
        );
    }

    /**
     * Retrieve a column in database table.
     *
     * @param int $id
     * @param string $column
     *
     * @return mixed
     */
    public function retrieveColumn($id, $column)
    {
        return $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT $column FROM $this->table WHERE id = %d",
                $id
            )
        );
    }


    /**
     * Singleton instance of the class
     *
     * @return CampaignLogPersistence
     */
    public static function instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self($GLOBALS['wpdb']);
        }

        return $instance;
    }
}