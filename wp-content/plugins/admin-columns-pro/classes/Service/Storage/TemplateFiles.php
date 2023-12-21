<?php

declare(strict_types=1);

namespace ACP\Service\Storage;

use AC\Registerable;

final class TemplateFiles implements Registerable
{

    private $files;

    public function __construct(array $files)
    {
        array_map([$this, 'add'], $files);
    }

    private function add(string $file): void
    {
        $this->files[] = realpath($file);
    }

    public static function from_directory(string $path): self
    {
        $path = rtrim($path, '/');

        return new self(glob($path . '/*.json'));
    }

    public function register(): void
    {
        add_filter('acp/storage/template/files', [$this, 'add_files'], 5);
    }

    public function add_files(array $files): array
    {
        return array_merge($files, $this->files);
    }

}