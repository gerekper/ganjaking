<?php

namespace ACA\MetaBox\Search\Comparison\Table;

use AC\Helper\Select\Options\Paginated;
use ACP;
use ACP\Helper\Select\Post\GroupFormatter\MimeType;
use ACP\Helper\Select\Post\LabelFormatter\PostTitle;
use ACP\Helper\Select\Post\PaginatedFactory;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class Media extends TableStorage
    implements SearchableValues
{

    use MultiMapTrait;

    private $mime_type;

    public function __construct(
        Operators $operators,
        string $table,
        string $column,
        array $mime_type = [],
        string $value_type = null,
        Labels $labels = null
    ) {
        $this->mime_type = $mime_type;

        parent::__construct($operators, $table, $column, $value_type, $labels);
    }

    public function format_label($value): string
    {
        $post = get_post($value);

        return $post
            ? (new PostTitle())->format_label($post)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        $args = [
            's'         => $search,
            'paged'     => $page,
            'post_type' => 'attachment',
            'orderby'   => 'date',
            'order'     => 'DESC',
        ];

        if ($this->mime_type) {
            $args['post_mime_type'] = $this->mime_type;
        }

        return (new PaginatedFactory())->create($args, new PostTitle(), new MimeType());
    }

    protected function get_subquery(string $operator, Value $value): string
    {
        $_operator = $this->map_operator($operator);
        $_value = $this->map_value($value, $operator);

        $where = ACP\Search\Helper\Sql\ComparisonFactory::create($this->column, $_operator, $_value);

        return "SELECT ID FROM $this->table WHERE " . $where->prepare();
    }

}