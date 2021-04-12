<?php
/**
 * @var GF_Field $field
 * @var array    $nested_fields    An array of GF_Field objects.
 * @var array    $nested_form      The form object of the nested form.
 * @var array    $nested_field_ids An array of nested field IDs.
 * @var string   $modifiers        Generated HTML for displaying related entries link.
 * @var bool     $is_all_fields
 */

echo gp_nested_forms()->get_all_entries_markup( $field, $value, $modifiers, $is_all_fields, $format );