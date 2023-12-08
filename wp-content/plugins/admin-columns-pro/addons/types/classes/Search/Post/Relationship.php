<?php

namespace ACA\Types\Search\Post;

use AC\Helper\Select\Options\Paginated;
use ACP;
use ACP\Helper\Select\Post\PaginatedFactory;
use ACP\Query\Bindings;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Value;
use Toolset_Relationship_Definition_Repository;
use Toolset_Relationship_Table_Name;

class Relationship extends ACP\Search\Comparison
    implements Comparison\SearchableValues
{

    /**
     * @var string
     */
    private $related_post_type;

    /**
     * @var string
     */
    private $relationship;

    /**
     * @var string
     */
    private $role;

    /**
     * @var string
     */
    private $return_role;

    public function __construct($relationship, $related_post_type, $role, $return_role)
    {
        $this->relationship = $relationship;
        $this->role = $role;
        $this->related_post_type = $related_post_type;
        $this->return_role = $return_role;

        parent::__construct($this->get_default_operators());
    }

    protected function get_default_operators()
    {
        return new Operators([
            Operators::EQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]);
    }

    /**
     * @return int
     */
    private function get_relationship_id()
    {
        $relationship = Toolset_Relationship_Definition_Repository::get_instance()->get_definition($this->relationship);

        return $relationship ? $relationship->get_row_id() : 0;
    }

    protected function create_query_bindings(string $operator, Value $value): Bindings
    {
        global $wpdb;

        if (in_array($operator, [Operators::IS_EMPTY, Operators::NOT_IS_EMPTY])) {
            return $this->get_associated_bindings($operator);
        }

        $bindings = new Bindings();

        $posts = toolset_get_related_posts(
            $value->get_value(),
            $this->relationship,
            ['query_by_role' => $this->role, 'role_to_return' => $this->return_role, 'limit' => -1]
        );
        $posts = empty($posts) ? [0] : $posts;

        return $bindings->where(sprintf("{$wpdb->posts}.ID IN( '%s')", implode("','", array_map('esc_sql', $posts))));
    }

    private function get_associated_bindings($operator)
    {
        global $wpdb;

        $table = esc_sql((new Toolset_Relationship_Table_Name())->association_table());
        $column = ('child' === $this->role) ? 'parent_id' : 'child_id';
        $in = ($operator === Operators::NOT_IS_EMPTY) ? 'IN' : 'NOT IN';

        $sql = $wpdb->prepare(
            "
				SELECT DISTINCT($column) 
				FROM $table
				WHERE relationship_id = %d",
            $this->get_relationship_id()
        );

        return (new Bindings())->where(
            "$wpdb->posts.ID $in( $sql )"
        );
    }

    public function format_label($value): string
    {
        $post = get_post($value);

        return $post
            ? (new ACP\Helper\Select\Post\LabelFormatter\PostTitle())->format_label(value)
            : $value;
    }

    public function get_values(string $search, int $page): Paginated
    {
        return (new PaginatedFactory())->create([
            's'         => $search,
            'paged'     => $page,
            'post_type' => $this->related_post_type,
        ]);
    }

}