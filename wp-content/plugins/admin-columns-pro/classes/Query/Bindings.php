<?php

namespace ACP\Query;

class Bindings
{

    /**
     * @var int[]
     */
    private static $aliases = [];

    protected $where = '';

    protected $join = '';

    protected $group_by = '';

    protected $order_by = '';

    protected $limits = '';

    protected $meta_query = [];

    public function get_unique_alias(string $column): string
    {
        if ( ! isset(self::$aliases[$column])) {
            self::$aliases[$column] = 0;
        }

        return $column . '_ac' . self::$aliases[$column]++;
    }

    public function get_where(): string
    {
        return $this->where;
    }

    public function where(string $where): self
    {
        $this->where = $where;

        return $this;
    }

    public function get_join(): string
    {
        return $this->join;
    }

    public function join(string $join): self
    {
        $this->join = $join;

        return $this;
    }

    public function get_limits(): string
    {
        return $this->limits;
    }

    public function limits(string $limits): self
    {
        $this->limits = $limits;

        return $this;
    }

    public function get_group_by(): string
    {
        return $this->group_by;
    }

    public function group_by(string $group): self
    {
        $this->group_by = $group;

        return $this;
    }

    public function get_order_by(): string
    {
        return $this->order_by;
    }

    public function order_by(string $order_by): self
    {
        $this->order_by = $order_by;

        return $this;
    }

    public function get_meta_query(): array
    {
        return $this->meta_query;
    }

    public function meta_query(array $args): self
    {
        $this->meta_query = $args;

        return $this;
    }

}