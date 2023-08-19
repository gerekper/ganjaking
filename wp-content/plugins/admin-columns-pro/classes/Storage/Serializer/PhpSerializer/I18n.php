<?php

declare(strict_types=1);

namespace ACP\Storage\Serializer\PhpSerializer;

use ACP\Storage\Serializer;

final class I18n implements Serializer
{

    private $serializer;

    private $text_domain;

    public function __construct(Serializer $serializer, string $text_domain)
    {
        $this->serializer = $serializer;
        $this->text_domain = $text_domain;
    }

    public function serialize(array $data): string
    {
        $patterns = [
            'ac_translate_open' => "__('",
            'ac_translate_close' => sprintf("','%s')", sanitize_key($this->text_domain)),
        ];

        $translatable_keys = ['title', 'label', 'before', 'after'];

        array_walk_recursive(
            $data,
            function (&$value, $key) use ($translatable_keys) {
                if ( ! is_array($value) && in_array($key, $translatable_keys, true)) {
                    $value =
                        $this->add_delimiter('ac_translate_open') .
                        $value .
                        $this->add_delimiter('ac_translate_close');
                }
            }
        );

        $output = $this->serializer->serialize($data);

        foreach ($patterns as $tag => $replacement) {
            $search = $this->add_delimiter($tag);
            $search = str_contains($tag, 'open')
                ? "'" . $search
                : $search . "'";

            $output = str_replace(
                $search,
                $replacement,
                $output
            );
        }

        return $output;
    }

    private function add_delimiter($key): string
    {
        return '%%ac_i18n_replace_' . $key . '%%';
    }

}