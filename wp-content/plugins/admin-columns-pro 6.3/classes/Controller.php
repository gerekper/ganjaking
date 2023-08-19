<?php

namespace ACP;

use AC\Request;
use ACP\Exception\ControllerException;

abstract class Controller
{

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function dispatch(string $action): void
    {
        $method = $action . '_action';

        if ( ! is_callable([$this, $method])) {
            throw ControllerException::from_invalid_action($action);
        }

        $this->$method();
    }

}