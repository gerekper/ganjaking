<?php

namespace ACP\API;

class Request
{

    protected $args = [];

    public function __construct(array $body)
    {
        $this->set_body($body)
             ->set_timeout(15);
    }

    public function get_body(): array
    {
        return $this->args['body'];
    }

    public function set_body(array $value): self
    {
        $this->args['body'] = $value;

        return $this;
    }

    public function set_timeout(int $value): self
    {
        $this->args['timeout'] = $value;

        return $this;
    }

    public function get_args(): array
    {
        return $this->args;
    }

}