<?php

declare(strict_types=1);

namespace ACP\RequestHandler;

use AC\Request;

class ImportUpload extends Import
{

    public function is_request(Request $request): bool
    {
        return parent::is_request($request) &&
               $this->get_imported_file_name() &&
               $this->get_imported_file_tmp_name();
    }

    private function get_imported_file_name(): ?string
    {
        return $_FILES['import']['name'] ?? null;
    }

    private function get_imported_file_tmp_name(): ?string
    {
        return $_FILES['import']['tmp_name'] ?? null;
    }

    public function handle(Request $request): void
    {
        $request->add_middleware(
            new Middleware\ImportJson()
        );

        if ( ! ac_helper()->string->ends_with($this->get_imported_file_name(), 'json')) {
            $this->set_message(
                sprintf(
                    __('Uploaded file does not have a %s extension.', 'codepress-admin-columns'),
                    '.json'
                )
            );

            return;
        }

        parent::handle($request);
    }

}