<?php
/**
 * @var $nested_form
 * @var $entry
 * @var $modifiers
 * @var $format
 */

echo GFCommon::get_submitted_fields( $nested_form, $entry, $args['display_empty'], $args['use_text'], $format, $args['use_admin_label'], 'all_fields', $modifiers );
