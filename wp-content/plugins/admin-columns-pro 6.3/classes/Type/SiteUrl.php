<?php

namespace ACP\Type;

use AC\Type\Url;

class SiteUrl implements Url
{

    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function get_url(): string
    {
        return $this->url;
    }

    public function __toString(): string
    {
        return $this->get_url();
    }

}