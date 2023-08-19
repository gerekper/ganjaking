<?php

namespace ACP;

use AC\Request;

interface RequestHandler
{

    public function handle(Request $request): void;

}