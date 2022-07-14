<?php
namespace Composer\Util;
if (!defined('ABSPATH')) exit;
@trigger_error('Composer\Util\MetadataMinifier is deprecated, use Composer\MetadataMinifier\MetadataMinifier from composer/metadata-minifier instead.', E_USER_DEPRECATED);
class MetadataMinifier extends \Composer\MetadataMinifier\MetadataMinifier
{
}
