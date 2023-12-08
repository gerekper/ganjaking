<?php

namespace ACA\JetEngine\Editing\Service;

use AC\Helper\Select\Options\Paginated;
use ACA\JetEngine\Utils\Api;
use ACP;
use ACP\Editing\View;
use ACP\Helper\Select\Post\PaginatedFactory;

class RelationshipLegacy implements ACP\Editing\Service, ACP\Editing\PaginatedOptions
{

    /**
     * @var string
     */
    private $related_key;

    /**
     * @var string
     */
    private $current_post_type;

    /**
     * @var string
     */
    private $related_post_type;

    /**
     * @var boolean
     */
    private $multiple;

    public function __construct($related_key, $current_post_type, $related_post_type, $multiple)
    {
        $this->related_key = (string)$related_key;
        $this->current_post_type = (string)$current_post_type;
        $this->related_post_type = (string)$related_post_type;
        $this->multiple = (bool)$multiple;
    }

    public function get_view(string $context): ?View
    {
        return self::CONTEXT_BULK === $context
            ? null
            : (new ACP\Editing\View\AjaxSelect())->set_multiple($this->multiple);
    }

    public function get_value($id)
    {
        $post_ids = Api::Relations()->get_related_posts([
            'hash'    => $this->related_key,
            'current' => $this->current_post_type,
            'post_id' => $id,
        ]);

        if (empty($post_ids)) {
            return [];
        }

        $result = [];

        foreach ((array)$post_ids as $post_id) {
            $result[$post_id] = get_the_title($post_id);
        }

        return $result;
    }

    public function update(int $id, $data): void
    {
        $ids = is_array($data) ? $data : [$data];

        Api::Relations()->process_meta(true, $id, $this->related_key, $ids);
    }

    public function get_paginated_options(string $search, int $page, int $id = null): Paginated
    {
        return (new PaginatedFactory())->create([
            's'         => $search,
            'paged'     => $page,
            'post_type' => $this->related_post_type,
        ]);
    }

}