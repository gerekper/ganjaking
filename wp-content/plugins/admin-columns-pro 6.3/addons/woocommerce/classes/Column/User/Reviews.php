<?php

namespace ACA\WC\Column\User;

use AC;
use ACA\WC\ConditionalFormat\FilteredHtmlIntegerFormatterTrait;
use ACP;
use ACP\Sorting\Model\User\CommentCount;

class Reviews extends AC\Column
    implements ACP\Sorting\Sortable, ACP\Export\Exportable, ACP\ConditionalFormat\Formattable
{

    use FilteredHtmlIntegerFormatterTrait;

    public function __construct()
    {
        $this->set_type('column-wc-user-reviews')
             ->set_label(__('Reviews', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $count = $this->get_raw_value($id);

        if ( ! $count) {
            return $this->get_empty_char();
        }

        $comments_url = add_query_arg(['user_id' => $id, 'post_type' => 'product'], admin_url('edit-comments.php'));

        return ac_helper()->html->link($comments_url, $count);
    }

    public function get_raw_value($user_id)
    {
        global $wpdb;

        $sql = "
			SELECT COUNT( comment_ID )
			FROM {$wpdb->comments} AS c
			INNER JOIN {$wpdb->posts} AS p ON c.comment_post_ID = p.ID AND p.post_type = 'product'
			WHERE c.user_id = %d
			AND c.comment_approved = 1
		";

        $stmt = $wpdb->prepare($sql, [$user_id]);

        return (int)$wpdb->get_var($stmt);
    }

    public function export()
    {
        return new ACP\Export\Model\RawValue($this);
    }

    public function sorting()
    {
        return new CommentCount([CommentCount::STATUS_APPROVED], ['product']);
    }

}