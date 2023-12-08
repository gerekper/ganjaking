<?php

namespace ACP\Query\Bindings;

class Media extends Post
{

    protected $mime_types = [];

    public function get_mime_types(): array
    {
        return $this->mime_types;
    }

    public function mime_types(array $mime_types): self
    {
        $this->mime_types = $mime_types;

        return $this;
    }

}