<?php

namespace ACP;

use AC\MetaType;
use LogicException;

final class QueryFactory
{

    private static $queries = [
        MetaType::POST    => Query\Post::class,
        MetaType::USER    => Query\User::class,
        MetaType::COMMENT => Query\Comment::class,
        MetaType::TERM    => Query\Term::class,
    ];

    public static function register(string $query_type, string $class_fqn): void
    {
        self::$queries[$query_type] = $class_fqn;
    }

    public static function can_create(string $query_type): bool
    {
        return isset(self::$queries[$query_type]);
    }

    public static function create(string $query_type, array $bindings): Query
    {
        $class = self::$queries[$query_type] ?? null;

        if ( ! $class) {
            throw new LogicException('Unsupported query meta type.');
        }

        $query = new $class($bindings);

        if ( ! $query instanceof Query) {
            throw new LogicException(sprintf('Expected class of type %s.', Query::class));
        }

        return $query;
    }

}