<?php

namespace ACA\WC\Column\User;

use AC;
use ACA\WC\ConditionalFormat\FilteredHtmlIntegerFormatterTrait;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACP;

class Ratings extends AC\Column
    implements ACP\Sorting\Sortable, ACP\Export\Exportable, ACP\ConditionalFormat\Formattable
{

    use FilteredHtmlIntegerFormatterTrait;

    public function __construct()
    {
        $this->set_type('column-wc-user-ratings')
             ->set_label(__('Ratings', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\User\Ratings($this));
    }

    public function get_value($id)
    {
        return $this->get_raw_value($id) ?: $this->get_empty_char();
    }

    /**
     * @return string
     */
    private function get_rating_type()
    {
        return $this->get_setting('user_ratings')->get_value();
    }

    public function get_raw_value($user_id)
    {
        global $wpdb;

        $is_avg = 'avg' === $this->get_rating_type();
        $af = $is_avg ? 'AVG' : 'COUNT';

        $sql = "
			SELECT {$af}(cm.meta_value)
			FROM {$wpdb->comments} AS c
			INNER JOIN {$wpdb->posts} AS p 
				ON c.comment_post_ID = p.ID 
				AND p.post_type = 'product'
			INNER JOIN {$wpdb->commentmeta} AS cm 
				ON cm.comment_id = c.comment_ID
			WHERE c.user_id = %d
			AND c.comment_approved = 1
			AND cm.meta_key = 'rating'
		";

        $stmt = $wpdb->prepare($sql, [$user_id]);
        $value = $wpdb->get_var($stmt);

        if ($is_avg) {
            $value = round($value, 3);
        }

        return $value;
    }

    public function export()
    {
        return new ACP\Export\Model\RawValue($this);
    }

    public function sorting()
    {
        $rating_type = 'avg' === $this->get_rating_type()
            ? 'AVG'
            : 'COUNT';

        return new Sorting\User\Ratings($rating_type);
    }

}