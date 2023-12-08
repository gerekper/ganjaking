<?php

declare(strict_types=1);

namespace ACP\RequestHandler\Middleware;

use AC\Middleware;
use AC\Request;

class ImportJson implements Middleware
{

    public function handle(Request $request): void
    {
        $file_name = $_FILES['import']['tmp_name'] ?? null;

        if ( ! $file_name) {
            return;
        }

        $request->get_parameters()->set('file_name', $file_name);
    }

}