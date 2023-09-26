<?php

namespace ACP\API;

use WP_Error;

class Response
{

    /**
     * @var object
     */
    private $body;

    /**
     * @var WP_Error
     */
    private $error;

    public function get_body()
    {
        return $this->body;
    }

    public function get_error(): WP_Error
    {
        return $this->error;
    }

    public function has_error(): bool
    {
        return $this->error instanceof WP_Error;
    }

    public function with_body(object $body): self
    {
        $self = clone $this;
        $self->body = $body;

        return $self;
    }

    public function with_error(WP_Error $error): self
    {
        $self = clone $this;
        $self->error = $error;

        return $self;
    }

    /**
     * Access properties from the body
     */
    public function get($key)
    {
        return $this->body->{$key} ?? null;
    }

}