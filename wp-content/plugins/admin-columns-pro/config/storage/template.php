<?php

$templates = [
    'media-audio',
    'media-images',
    'media-video',
    'post-compact',
    'user-compact',
    'user-settings',
];

return array_map(static function ($template) {
    return sprintf('config/storage/template/%s.json', $template);
}, $templates);