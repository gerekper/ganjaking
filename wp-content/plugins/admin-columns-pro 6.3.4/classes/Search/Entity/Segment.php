<?php

declare(strict_types=1);

namespace ACP\Search\Entity;

use AC\Type\ListScreenId;
use ACP\Search\Type\SegmentKey;

final class Segment
{

    private $key;

    private $list_screen_id;

    private $user_id;

    private $name;

    private $url_parameters;

    public function __construct(
        SegmentKey $key,
        ListScreenId $list_screen_id,
        string $name,
        array $url_parameters,
        int $user_id = null
    ) {
        $this->key = $key;
        $this->list_screen_id = $list_screen_id;
        $this->name = $name;
        $this->url_parameters = $url_parameters;
        $this->user_id = $user_id;
    }

    public function get_key(): SegmentKey
    {
        return $this->key;
    }

    public function get_list_screen_id(): ListScreenId
    {
        return $this->list_screen_id;
    }

    public function get_user_id(): ?int
    {
        return $this->user_id;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_url_parameters(): array
    {
        return $this->url_parameters;
    }

    public function is_global(): bool
    {
        return $this->get_user_id() === null;
    }

}