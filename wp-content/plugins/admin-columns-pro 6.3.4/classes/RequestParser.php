<?php

namespace ACP;

use AC\Registerable;
use AC\Request;

class RequestParser implements Registerable
{

    /**
     * @var RequestHandlerFactory
     */
    private $handler_factory;

    public function __construct(RequestHandlerFactory $handler_factory)
    {
        $this->handler_factory = $handler_factory;
    }

    public function register(): void
    {
        add_action('admin_init', [$this, 'handle']);
    }

    public function handle()
    {
        if ( ! $this->handler_factory->is_request()) {
            return;
        }

        $this->handler_factory
            ->create()
            ->handle(new Request());
    }

}