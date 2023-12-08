<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Comment;

use ACP\Query\Bindings;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Sorter;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;

class FieldFormat implements QueryBindings
{

    protected $field;

    protected $formatter;

    protected $value_length;

    protected $data_type;

    public function __construct(
        string $field,
        FormatValue $formatter,
        DataType $data_type = null,
        int $value_length = null
    ) {
        $this->field = $field;
        $this->formatter = $formatter;
        $this->value_length = $value_length;
        $this->data_type = $data_type;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        global $wpdb;

        return (new Bindings())->order_by(
            SqlOrderByFactory::create_with_ids(
                "$wpdb->comments.comment_ID",
                $this->get_sorted_ids(),
                (string)$order
            )
        );
    }

    private function get_comment_status(): string
    {
        global $comment_status;

        switch ($comment_status) {
            case 'moderated' :
                return '0';
            case 'spam' :
                return 'spam';
            case 'trash' :
                return 'trash';
            case 'approved' :
                return '1';
            default:
                return '';
        }
    }

    private function get_sorted_ids(): array
    {
        global $wpdb;

        $field = $this->value_length
            ? sprintf("LEFT( cc.%s, %s )", esc_sql($this->field), $this->value_length)
            : sprintf("cc.%s", esc_sql($this->field));

        $sql = sprintf(
            "
			SELECT cc.comment_ID AS id, %s AS value 
			FROM $wpdb->comments AS cc
		",
            $field
        );

        $status = $this->get_comment_status();

        if ($status) {
            $sql .= $wpdb->prepare(" WHERE cc.comment_approved = %s", $status);
        }

        $results = $wpdb->get_results($sql);

        if ( ! $results) {
            return [];
        }

        $values = [];

        foreach ($results as $object) {
            $values[$object->id] = $this->formatter->format_value($object->value);
        }

        return (new Sorter())->sort($values, $this->data_type);
    }

}