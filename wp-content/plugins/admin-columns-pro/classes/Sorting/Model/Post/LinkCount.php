<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post;

use ACP\Query\Bindings;
use ACP\Sorting\Model\QueryBindings;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\Order;

class LinkCount implements QueryBindings
{

    private $domains;

    public function __construct(array $domains)
    {
        $this->domains = $domains;
    }

    public function create_query_bindings(Order $order): Bindings
    {
        return (new Bindings())->order_by(
            SqlOrderByFactory::create(
                $this->get_sql_field(),
                (string)$order,
                [
                    'esc_sql' => false,
                    'empty_values' => [0],
                ]
            )
        );
    }

    private function get_sql_field(): string
    {
        $domains = array_map([$this, 'sql_prefix_with_href'], $this->domains);
        $field = implode(' + ', array_map([$this, 'sql_replace'], $domains));

        return sprintf('ROUND( %s )', $field);
    }

    private function sql_replace(string $string): string
    {
        global $wpdb;

        return $wpdb->prepare(
            "( LENGTH( $wpdb->posts.post_content ) - LENGTH( REPLACE ( $wpdb->posts.post_content, %s, '' ) ) ) / LENGTH( %s )",
            $string,
            $string
        );
    }

    private function sql_prefix_with_href(string $url): string
    {
        return sprintf('href="%s', esc_sql($url));
    }

}