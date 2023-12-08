<?php

namespace ACP\Search\Comparison\Media;

use AC\Helper\Select\Options;
use ACP\Helper\Select\PostType\LabelFormatter;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;

class PostType extends Comparison
    implements Comparison\RemoteValues
{

    public function __construct()
    {
        $operators = new Operators([
            Operators::EQ,
        ]);

        parent::__construct($operators);
    }

    private function get_formatter(): LabelFormatter\Name
    {
        return new LabelFormatter\Name();
    }

    public function format_label(string $value): string
    {
        $post_type = get_post_type_object($value);

        return $post_type
            ? $this->get_formatter()->format_label($post_type)
            : $value;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        $sub_query = $wpdb->prepare("SELECT ID from $wpdb->posts WHERE post_type = %s", $value->get_value());

        $bindings = new Bindings();
        $bindings->where("$wpdb->posts.post_parent IN($sub_query)");

        return $bindings;
    }

    public function get_values(): Options
    {
        $options = [];

        foreach ($this->get_post_types() as $post_type) {
            $post_type_object = get_post_type_object($post_type);

            if ($post_type_object) {
                $options[$post_type_object->name] = $this->get_formatter()->format_label($post_type_object);
            }
        }

        return Options::create_from_array($options);
    }

    public function get_post_types(): array
    {
        global $wpdb;

        $sql = "
			SELECT DISTINCT posts.post_type
			FROM $wpdb->posts AS attachments
			INNER JOIN $wpdb->posts AS posts ON attachments.post_parent = posts.ID
			WHERE attachments.post_type = %s
			AND posts.post_type != %s
			AND attachments.post_parent <> ''
			ORDER BY 1
		";

        $values = $wpdb->get_col($wpdb->prepare($sql, 'attachment', 'attachment'));

        if (empty($values)) {
            return [];
        }

        return $values;
    }

}