<?php

namespace ACA\BP\Column\User;

use AC;
use ACA\BP\Search;
use ACA\BP\Settings;
use ACA\BP\Sorting;
use ACP;

class ActivityUpdates extends AC\Column
    implements ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\IntegerFormattableTrait;

    public function __construct()
    {
        $this->set_type('column-buddypress_user_activity_updates')
             ->set_label(__('Activity Updates', 'codepress-admin-columns'))
             ->set_group('buddypress');
    }

    public function get_raw_value($id)
    {
        global $wpdb, $bp;

        $sql = $wpdb->prepare("SELECT COUNT(user_id) FROM {$bp->activity->table_name} WHERE user_id = %d", (int)$id);

        if ($this->get_activity_type()) {
            $sql .= $wpdb->prepare(' AND type = %s', $this->get_activity_type());
        }

        return $wpdb->get_var($sql);
    }

    protected function register_settings()
    {
        $this->add_setting(new Settings\ActivityType($this));
    }

    public function get_activity_type(): string
    {
        return (string)$this->get_setting('activity_type')->get_value();
    }

    public function is_valid()
    {
        return bp_is_active('activity');
    }

    public function sorting()
    {
        return new Sorting\User\ActivityUpdates($this->get_activity_type());
    }

    public function search()
    {
        return new Search\User\ActivityUpdates($this->get_activity_type());
    }

}